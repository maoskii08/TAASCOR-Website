<?php

declare(strict_types=1);

const RESUME_EXTENSION_MIME = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];

function private_upload_directory(): string
{
    $directory = (string) config_value('upload_dir');
    if ($directory === '' || (!str_starts_with($directory, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:[\\\\\/]/', $directory))) {
        throw new RuntimeException('UPLOAD_DIR must be an absolute path.');
    }
    if (path_is_within($directory, TAASCOR_ROOT) && config_value('environment') !== 'test') {
        throw new RuntimeException('UPLOAD_DIR must be outside the public project root.');
    }
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create private upload storage.');
    }
    $canonicalDirectory = realpath($directory);
    if ($canonicalDirectory === false) {
        throw new RuntimeException('Unable to resolve private upload storage.');
    }
    if (path_is_within($canonicalDirectory, TAASCOR_ROOT) && config_value('environment') !== 'test') {
        throw new RuntimeException('UPLOAD_DIR resolved inside the public project root.');
    }
    return $canonicalDirectory;
}

function validate_resume_contents(string $path, string $extension): string
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $detectedMime = (string) $finfo->file($path);
    $prefix = file_get_contents($path, false, null, 0, 8);
    if ($prefix === false) {
        throw new InvalidArgumentException('Unable to inspect the uploaded file.');
    }

    if ($extension === 'pdf') {
        if (!str_starts_with($prefix, '%PDF-')) {
            throw new InvalidArgumentException('The file content is not a valid PDF.');
        }
        return RESUME_EXTENSION_MIME['pdf'];
    }

    if ($extension === 'doc') {
        if (!str_starts_with($prefix, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")) {
            throw new InvalidArgumentException('The file content is not a valid legacy Word document.');
        }
        return RESUME_EXTENSION_MIME['doc'];
    }

    if ($extension === 'docx') {
        if (!in_array($detectedMime, ['application/zip', RESUME_EXTENSION_MIME['docx']], true)) {
            throw new InvalidArgumentException('The file content is not a valid Word document.');
        }
        $archive = new ZipArchive();
        if ($archive->open($path) !== true) {
            throw new InvalidArgumentException('The Word document archive cannot be read.');
        }
        $hasContentTypes = $archive->locateName('[Content_Types].xml') !== false;
        $hasDocument = $archive->locateName('word/document.xml') !== false;
        $archive->close();
        if (!$hasContentTypes || !$hasDocument) {
            throw new InvalidArgumentException('The DOCX package is missing required document parts.');
        }
        return RESUME_EXTENSION_MIME['docx'];
    }

    throw new InvalidArgumentException('Use PDF, DOC, or DOCX format.');
}

/**
 * @param array{name?:string,tmp_name?:string,error?:int,size?:int} $upload
 * @return array{original_name:string,storage_name:string,mime_type:string,size_bytes:int,content_sha256:string,storage_path:string}
 */
function store_resume_upload(array $upload): array
{
    if (!resume_upload_is_enabled()) {
        throw new DomainException(
            'Resume upload is unavailable until malware scanning, retention, and private-storage controls are approved.'
        );
    }
    $error = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        $message = match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The resume is larger than the upload limit.',
            UPLOAD_ERR_PARTIAL => 'The resume upload was interrupted. Try again.',
            UPLOAD_ERR_NO_FILE => 'No resume was selected.',
            default => 'The resume could not be uploaded.',
        };
        throw new InvalidArgumentException($message);
    }

    $temporaryPath = (string) ($upload['tmp_name'] ?? '');
    if ($temporaryPath === '' || !is_file($temporaryPath)) {
        throw new InvalidArgumentException('The temporary upload is unavailable.');
    }
    if (config_value('environment') !== 'test' && !is_uploaded_file($temporaryPath)) {
        throw new InvalidArgumentException('The upload source is invalid.');
    }

    $size = filesize($temporaryPath);
    if ($size === false || $size <= 0) {
        throw new InvalidArgumentException('The resume file is empty.');
    }
    if ($size > (int) config_value('max_upload_bytes', 5 * 1024 * 1024)) {
        throw new InvalidArgumentException('The resume exceeds the 5 MB limit.');
    }

    $originalName = str_replace(["\0", "\r", "\n"], '', basename((string) ($upload['name'] ?? 'resume')));
    $originalName = mb_substr($originalName, 0, 180);
    $extension = mb_strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
    if (!array_key_exists($extension, RESUME_EXTENSION_MIME)) {
        throw new InvalidArgumentException('Use PDF, DOC, or DOCX format.');
    }
    $mime = validate_resume_contents($temporaryPath, $extension);
    $contentHash = hash_file('sha256', $temporaryPath);
    if (!is_string($contentHash) || strlen($contentHash) !== 64) {
        throw new RuntimeException('Unable to fingerprint the resume before quarantine.');
    }
    $storageName = bin2hex(random_bytes(24)) . '.' . $extension;
    $storagePath = private_upload_directory() . DIRECTORY_SEPARATOR . $storageName;

    $moved = config_value('environment') === 'test'
        ? rename($temporaryPath, $storagePath)
        : move_uploaded_file($temporaryPath, $storagePath);
    if (!$moved) {
        throw new RuntimeException('Unable to move the resume into private quarantine.');
    }
    @chmod($storagePath, 0600);

    return [
        'original_name' => $originalName,
        'storage_name' => $storageName,
        'mime_type' => $mime,
        'size_bytes' => (int) $size,
        'content_sha256' => $contentHash,
        'storage_path' => $storagePath,
    ];
}

function remove_stored_upload(array $document): void
{
    $path = $document['storage_path'] ?? null;
    if (is_string($path) && is_file($path) && path_is_within($path, private_upload_directory())) {
        unlink($path);
    }
}

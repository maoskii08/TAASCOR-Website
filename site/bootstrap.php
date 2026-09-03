<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/early.php';
require_once dirname(__DIR__) . '/app/config.php';
require_once dirname(__DIR__) . '/app/security.php';

enforce_production_https();

/**
 * Shared public-site shell.
 *
 * The public support pages deliberately contain no database access, sessions,
 * authentication state, or applicant data. They remain usable without
 * JavaScript; site.js only enhances the mobile navigation and small UI details.
 */

$taascorCspNonce = base64_encode(random_bytes(18));
$GLOBALS['taascor_csp_nonce'] = $taascorCspNonce;

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header_remove('X-Powered-By');
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    header(
        "Content-Security-Policy: default-src 'self'; " .
        "base-uri 'self'; object-src 'none'; frame-ancestors 'none'; " .
        "img-src 'self' data:; font-src 'self'; style-src 'self'; " .
        "script-src 'self' 'nonce-{$taascorCspNonce}'; connect-src 'self'; " .
        "form-action 'self'"
    );
    if (request_uses_https()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}

function taascor_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function taascor_public_indexing_enabled(): bool
{
    $value = getenv('PUBLIC_INDEXING_ENABLED');
    return $value !== false && filter_var($value, FILTER_VALIDATE_BOOL) === true;
}

function taascor_url(string $path = '/'): string
{
    if ($path === '') {
        return '/';
    }

    return '/' . ltrim($path, '/');
}

function taascor_nav_link(string $href, string $label, string $pageKey, string $activePage): string
{
    $current = $pageKey === $activePage ? ' aria-current="page"' : '';

    return sprintf(
        '<a href="%s"%s>%s</a>',
        taascor_escape(taascor_url($href)),
        $current,
        taascor_escape($label)
    );
}

/**
 * @param array{
 *   title:string,
 *   description:string,
 *   active?:string,
 *   body_class?:string,
 *   robots?:string,
 *   styles?:list<string>,
 *   canonical_path?:string,
 *   json_ld?:array<mixed>|object
 * } $page
 */
function taascor_page_start(array $page): void
{
    $title = $page['title'];
    $description = $page['description'];
    $activePage = $page['active'] ?? '';
    $bodyClass = $page['body_class'] ?? '';
    $robots = $page['robots'] ?? 'index,follow';
    if (!taascor_public_indexing_enabled()) {
        $robots = 'noindex,nofollow';
    }
    if (!headers_sent()) {
        header('X-Robots-Tag: ' . $robots);
    }
    $styles = isset($page['styles']) && is_array($page['styles']) ? $page['styles'] : [];
    $validatedStyles = [];
    foreach ($styles as $stylesheet) {
        if (
            is_string($stylesheet) &&
            preg_match('#^/?[A-Za-z0-9][A-Za-z0-9/_-]*\.css$#', $stylesheet) === 1 &&
            !in_array($stylesheet, $validatedStyles, true)
        ) {
            $validatedStyles[] = $stylesheet;
        }
    }
    $jsonLd = $page['json_ld'] ?? null;
    $jsonLdOutput = null;
    $canonicalPath = $page['canonical_path'] ?? (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($canonicalPath) || preg_match('#^/[A-Za-z0-9/_-]*$#', $canonicalPath) !== 1) {
        $canonicalPath = '/';
    }
    if ($canonicalPath !== '/' && !str_ends_with($canonicalPath, '/')) {
        $canonicalPath .= '/';
    }
    $configuredBaseUrl = rtrim((string) (getenv('APP_URL') ?: ''), '/');
    $canonicalUrl = app_url_is_origin($configuredBaseUrl, is_production())
        ? $configuredBaseUrl . $canonicalPath
        : null;

    if (is_array($jsonLd) || is_object($jsonLd)) {
        try {
            $jsonLdOutput = json_encode(
                $jsonLd,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            );
        } catch (JsonException $exception) {
            $jsonLdOutput = null;
        }
    }
    ?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= taascor_escape($description) ?>">
    <meta name="robots" content="<?= taascor_escape($robots) ?>">
    <meta name="theme-color" content="#f4f7fb">
    <meta property="og:site_name" content="TAASCOR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= taascor_escape($title) ?> | TAASCOR">
    <meta property="og:description" content="<?= taascor_escape($description) ?>">
    <meta name="twitter:card" content="summary">
    <title><?= taascor_escape($title) ?> | TAASCOR</title>
    <?php if ($canonicalUrl !== null): ?>
        <link rel="canonical" href="<?= taascor_escape($canonicalUrl) ?>">
        <meta property="og:url" content="<?= taascor_escape($canonicalUrl) ?>">
    <?php endif; ?>
    <link rel="icon" href="<?= taascor_escape(taascor_url('/assets/brand/favicon-32.png')) ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?= taascor_escape(taascor_url('/assets/brand/icon-192.png')) ?>" type="image/png" sizes="192x192">
    <link rel="apple-touch-icon" href="<?= taascor_escape(taascor_url('/assets/brand/apple-touch-icon.png')) ?>" sizes="180x180">
    <script src="<?= taascor_escape(taascor_url('/assets/js/theme.js')) ?>"></script>
    <link rel="stylesheet" href="<?= taascor_escape(taascor_url('/assets/css/site.css')) ?>">
    <?php foreach ($validatedStyles as $stylesheet): ?>
        <link rel="stylesheet" href="<?= taascor_escape(taascor_url($stylesheet)) ?>">
    <?php endforeach; ?>
    <?php if ($jsonLdOutput !== null): ?>
        <script type="application/ld+json" nonce="<?= taascor_escape((string) $GLOBALS['taascor_csp_nonce']) ?>"><?= $jsonLdOutput ?></script>
    <?php endif; ?>
    <script src="<?= taascor_escape(taascor_url('/assets/js/site.js')) ?>" defer></script>
</head>
<body class="<?= taascor_escape($bodyClass) ?>">
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <div class="signal-rail" aria-hidden="true"><span></span></div>
    <header class="site-header" data-site-header>
        <div class="shell nav-shell">
            <a class="brand" href="<?= taascor_escape(taascor_url('/')) ?>" aria-label="TAASCOR home">
                <img class="brand-mark" src="<?= taascor_escape(taascor_url('/assets/brand/taascor-mark.png')) ?>" width="859" height="756" alt="">
                <span class="brand-copy" aria-hidden="true"><span class="brand-name">TAASCOR</span><span class="brand-legal">Management &amp; General Services Corp.</span></span>
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
                <span class="sr-only">Open navigation</span>
                <span aria-hidden="true"></span><span aria-hidden="true"></span>
            </button>

            <nav class="site-nav" id="site-navigation" aria-label="Primary navigation">
                <div class="nav-links">
                    <?= taascor_nav_link('/solutions/', 'Solutions', 'solutions', $activePage) ?>
                    <?= taascor_nav_link('/industries/', 'Industries', 'industries', $activePage) ?>
                    <?= taascor_nav_link('/jobs/', 'Jobs & Apply', 'jobs', $activePage) ?>
                    <?= taascor_nav_link('/platform/', 'Platform', 'platform', $activePage) ?>
                    <?= taascor_nav_link('/proof/', 'Proof', 'proof', $activePage) ?>
                    <?= taascor_nav_link('/about/', 'About', 'about', $activePage) ?>
                </div>
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Use dark theme" aria-pressed="false">
                    <span aria-hidden="true">◐</span><span data-theme-label>Light</span>
                </button>
                <a class="button button-small button-outline" href="<?= taascor_escape(taascor_url('/portal/')) ?>"<?= $activePage === 'portal' ? ' aria-current="page"' : '' ?>>Access TAASCOR</a>
            </nav>
        </div>
    </header>
    <?php
}

function taascor_page_end(): void
{
    ?>
    <footer class="site-footer" data-site-footer>
        <div class="shell footer-grid">
            <div class="footer-brand">
                <a class="brand" href="<?= taascor_escape(taascor_url('/')) ?>" aria-label="TAASCOR home">
                    <img class="brand-mark" src="<?= taascor_escape(taascor_url('/assets/brand/taascor-mark.png')) ?>" width="859" height="756" alt="">
                    <span class="brand-copy" aria-hidden="true"><span class="brand-name">TAASCOR</span><span class="brand-legal">Management &amp; General Services Corp.</span></span>
                </a>
                <p>A clearer path from workforce need to coordinated action, and from opportunity to application.</p>
            </div>
            <nav aria-label="Workforce journeys">
                <h2>Journeys</h2>
                <a href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Build a workforce</a>
                <a href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Find work</a>
                <a href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
            </nav>
            <nav aria-label="Company and trust">
                <h2>Company</h2>
                <a href="<?= taascor_escape(taascor_url('/about/')) ?>">About</a>
                <a href="<?= taascor_escape(taascor_url('/leadership/')) ?>">Leadership</a>
                <a href="<?= taascor_escape(taascor_url('/locations/')) ?>">Locations</a>
                <a href="<?= taascor_escape(taascor_url('/contact/')) ?>">Contact routes</a>
            </nav>
            <nav aria-label="Evidence and resources">
                <h2>Explore</h2>
                <a href="<?= taascor_escape(taascor_url('/proof/')) ?>">Proof and compliance</a>
                <a href="<?= taascor_escape(taascor_url('/insights/')) ?>">Insights</a>
                <a href="<?= taascor_escape(taascor_url('/resources/')) ?>">Resources</a>
                <a href="<?= taascor_escape(taascor_url('/clients/')) ?>">Client relationships</a>
                <a href="<?= taascor_escape(taascor_url('/case-studies/')) ?>">Case studies</a>
                <a href="<?= taascor_escape(taascor_url('/legal/anti-fraud/')) ?>">Recruitment safety</a>
            </nav>
        </div>
        <div class="shell footer-base">
            <p>&copy; <span data-current-year>2026</span> TAASCOR · Workforce services, recruitment, and workforce technology.</p>
            <nav aria-label="Legal information">
                <a href="<?= taascor_escape(taascor_url('/legal/privacy/')) ?>">Privacy</a>
                <a href="<?= taascor_escape(taascor_url('/legal/terms/')) ?>">Terms</a>
                <a href="<?= taascor_escape(taascor_url('/legal/accessibility/')) ?>">Accessibility</a>
            </nav>
        </div>
    </footer>
</body>
</html>
    <?php
}

function taascor_status_tag(string $label, string $tone = 'neutral'): string
{
    $allowed = ['neutral', 'available', 'review'];
    if (!in_array($tone, $allowed, true)) {
        $tone = 'neutral';
    }

    return '<span class="status-tag status-' . $tone . '"><span aria-hidden="true"></span>' . taascor_escape($label) . '</span>';
}

function taascor_legal_navigation(string $activePage): void
{
    $items = [
        'privacy' => ['/legal/privacy/', 'Privacy framework'],
        'terms' => ['/legal/terms/', 'Website terms'],
        'accessibility' => ['/legal/accessibility/', 'Accessibility'],
        'anti-fraud' => ['/legal/anti-fraud/', 'Recruitment safety'],
    ];
    ?>
    <nav class="legal-nav" aria-label="Legal and support information">
        <?php foreach ($items as $key => [$href, $label]): ?>
            <a href="<?= taascor_escape(taascor_url($href)) ?>"<?= $key === $activePage ? ' aria-current="page"' : '' ?>><?= taascor_escape($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php
}

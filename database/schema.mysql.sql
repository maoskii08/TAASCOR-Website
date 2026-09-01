CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(32) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL DEFAULT '',
    password_hash VARCHAR(255) NOT NULL,
    session_version INT UNSIGNED NOT NULL DEFAULT 1,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_users_role (role, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(160) NOT NULL UNIQUE,
    title VARCHAR(160) NOT NULL,
    company VARCHAR(160) NOT NULL,
    location VARCHAR(160) NOT NULL,
    employment_type VARCHAR(80) NOT NULL,
    function_area VARCHAR(120) NOT NULL DEFAULT '',
    shift_pattern VARCHAR(120) NOT NULL DEFAULT '',
    summary VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT NOT NULL,
    openings INT UNSIGNED NULL,
    closing_date DATE NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    is_demo TINYINT(1) NOT NULL DEFAULT 0,
    version INT UNSIGNED NOT NULL DEFAULT 1,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_jobs_public (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_change_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT UNSIGNED NOT NULL,
    event_type VARCHAR(64) NOT NULL,
    previous_job_json TEXT NULL,
    new_job_json TEXT NOT NULL,
    actor_user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_job_change_history (job_id, created_at),
    CONSTRAINT fk_job_change_history_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE RESTRICT,
    CONSTRAINT fk_job_change_history_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS applications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    applicant_user_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NOT NULL,
    job_snapshot_json TEXT NOT NULL,
    candidate_full_name VARCHAR(120) NOT NULL,
    candidate_email VARCHAR(190) NOT NULL,
    candidate_phone VARCHAR(30) NOT NULL,
    current_city VARCHAR(120) NOT NULL,
    eligibility_confirmed TINYINT(1) NOT NULL DEFAULT 0,
    experience_summary VARCHAR(1500) NOT NULL DEFAULT '',
    current_status VARCHAR(32) NOT NULL DEFAULT 'draft',
    privacy_notice_version VARCHAR(100) NOT NULL,
    privacy_accepted_at DATETIME NOT NULL,
    certification_at DATETIME NULL,
    submitted_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY uq_application_applicant_job (applicant_user_id, job_id),
    INDEX idx_applications_applicant (applicant_user_id, updated_at),
    INDEX idx_applications_status (current_status, updated_at),
    CONSTRAINT fk_applications_applicant FOREIGN KEY (applicant_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_applications_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(180) NOT NULL,
    storage_name VARCHAR(100) NOT NULL UNIQUE,
    mime_type VARCHAR(160) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    content_sha256 CHAR(64) NOT NULL,
    scan_status VARCHAR(32) NOT NULL DEFAULT 'quarantine',
    scanned_at DATETIME NULL,
    scan_result VARCHAR(100) NULL,
    retention_expires_at DATETIME NULL,
    uploaded_at DATETIME NOT NULL,
    UNIQUE KEY uq_document_application (application_id),
    INDEX idx_documents_application (application_id, scan_status),
    CONSTRAINT fk_documents_application FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_job_snapshot_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    event_type VARCHAR(64) NOT NULL,
    previous_snapshot_json TEXT NULL,
    accepted_snapshot_json TEXT NOT NULL,
    previous_content_sha256 CHAR(64) NULL,
    accepted_content_sha256 CHAR(64) NOT NULL,
    applicant_reviewed_changes TINYINT(1) NOT NULL DEFAULT 0,
    acknowledged_at DATETIME NULL,
    actor_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_snapshot_history_application (application_id, created_at),
    CONSTRAINT fk_snapshot_history_application FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    CONSTRAINT fk_snapshot_history_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS private_storage_usage (
    scope VARCHAR(64) NOT NULL PRIMARY KEY,
    used_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS schema_migrations (
    migration_key VARCHAR(160) NOT NULL PRIMARY KEY,
    applied_at DATETIME NOT NULL,
    details_json TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_status_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(32) NOT NULL,
    note_public VARCHAR(500) NOT NULL DEFAULT '',
    changed_by_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_history_application (application_id, created_at),
    CONSTRAINT fk_history_application FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    CONSTRAINT fk_history_user FOREIGN KEY (changed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_tasks (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    due_date DATE NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    applicant_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_by_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_tasks_application (application_id, status),
    CONSTRAINT fk_tasks_application FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_user FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    actor_user_id BIGINT UNSIGNED NULL,
    event_type VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    metadata_json TEXT NOT NULL,
    ip_hash CHAR(64) NOT NULL,
    user_agent_hash CHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_audit_entity (entity_type, entity_id, created_at),
    INDEX idx_audit_ip_event (ip_hash, event_type, created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS auth_attempts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    scope VARCHAR(64) NOT NULL,
    subject_hash CHAR(64) NOT NULL,
    attempted_at DATETIME NOT NULL,
    INDEX idx_auth_attempt_lookup (scope, subject_hash, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS privacy_acknowledgements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    notice_scope VARCHAR(64) NOT NULL,
    notice_version VARCHAR(100) NOT NULL,
    acknowledged_at DATETIME NOT NULL,
    UNIQUE KEY uq_privacy_acknowledgement (user_id, notice_scope, notice_version),
    INDEX idx_privacy_ack_user (user_id, acknowledged_at),
    CONSTRAINT fk_privacy_ack_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS workforce_briefs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    organization VARCHAR(160) NOT NULL,
    contact_name VARCHAR(120) NOT NULL,
    contact_email VARCHAR(190) NOT NULL,
    contact_phone VARCHAR(30) NOT NULL DEFAULT '',
    sites VARCHAR(1000) NOT NULL,
    roles_needed VARCHAR(1500) NOT NULL,
    estimated_headcount INT UNSIGNED NULL,
    shift_pattern VARCHAR(500) NOT NULL DEFAULT '',
    target_start_date DATE NULL,
    service_needs VARCHAR(1500) NOT NULL,
    notes VARCHAR(2000) NOT NULL DEFAULT '',
    privacy_notice_version VARCHAR(100) NOT NULL,
    privacy_accepted_at DATETIME NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'submitted',
    submitted_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_workforce_briefs_status (status, submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

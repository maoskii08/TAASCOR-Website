PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    full_name TEXT NOT NULL,
    phone TEXT NOT NULL DEFAULT '',
    password_hash TEXT NOT NULL,
    session_version INTEGER NOT NULL DEFAULT 1,
    is_active INTEGER NOT NULL DEFAULT 1,
    last_login_at TEXT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    company TEXT NOT NULL,
    location TEXT NOT NULL,
    employment_type TEXT NOT NULL,
    function_area TEXT NOT NULL DEFAULT '',
    shift_pattern TEXT NOT NULL DEFAULT '',
    summary TEXT NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT NOT NULL DEFAULT '',
    openings INTEGER NULL,
    closing_date TEXT NULL,
    status TEXT NOT NULL DEFAULT 'draft',
    is_demo INTEGER NOT NULL DEFAULT 0,
    version INTEGER NOT NULL DEFAULT 1,
    published_at TEXT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS job_change_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    job_id INTEGER NOT NULL,
    event_type TEXT NOT NULL,
    previous_job_json TEXT NULL,
    new_job_json TEXT NOT NULL,
    actor_user_id INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE RESTRICT,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS applications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    applicant_user_id INTEGER NOT NULL,
    job_id INTEGER NOT NULL,
    job_snapshot_json TEXT NOT NULL,
    candidate_full_name TEXT NOT NULL,
    candidate_email TEXT NOT NULL,
    candidate_phone TEXT NOT NULL,
    current_city TEXT NOT NULL,
    eligibility_confirmed INTEGER NOT NULL DEFAULT 0,
    experience_summary TEXT NOT NULL DEFAULT '',
    current_status TEXT NOT NULL DEFAULT 'draft',
    privacy_notice_version TEXT NOT NULL,
    privacy_accepted_at TEXT NOT NULL,
    certification_at TEXT NULL,
    submitted_at TEXT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    UNIQUE (applicant_user_id, job_id),
    FOREIGN KEY (applicant_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS application_documents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    application_id INTEGER NOT NULL,
    original_name TEXT NOT NULL,
    storage_name TEXT NOT NULL UNIQUE,
    mime_type TEXT NOT NULL,
    size_bytes INTEGER NOT NULL,
    content_sha256 TEXT NOT NULL,
    scan_status TEXT NOT NULL DEFAULT 'quarantine',
    scanned_at TEXT NULL,
    scan_result TEXT NULL,
    retention_expires_at TEXT NULL,
    uploaded_at TEXT NOT NULL,
    UNIQUE (application_id),
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS application_job_snapshot_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    application_id INTEGER NOT NULL,
    event_type TEXT NOT NULL,
    previous_snapshot_json TEXT NULL,
    accepted_snapshot_json TEXT NOT NULL,
    previous_content_sha256 TEXT NULL,
    accepted_content_sha256 TEXT NOT NULL,
    applicant_reviewed_changes INTEGER NOT NULL DEFAULT 0,
    acknowledged_at TEXT NULL,
    actor_user_id INTEGER NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS private_storage_usage (
    scope TEXT PRIMARY KEY,
    used_bytes INTEGER NOT NULL DEFAULT 0,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS schema_migrations (
    migration_key TEXT PRIMARY KEY,
    applied_at TEXT NOT NULL,
    details_json TEXT NOT NULL DEFAULT '{}'
);

CREATE TABLE IF NOT EXISTS application_status_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    application_id INTEGER NOT NULL,
    status TEXT NOT NULL,
    note_public TEXT NOT NULL DEFAULT '',
    changed_by_user_id INTEGER NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS application_tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    application_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    due_date TEXT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    applicant_visible INTEGER NOT NULL DEFAULT 1,
    created_by_user_id INTEGER NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS audit_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    actor_user_id INTEGER NULL,
    event_type TEXT NOT NULL,
    entity_type TEXT NOT NULL,
    entity_id INTEGER NULL,
    metadata_json TEXT NOT NULL DEFAULT '{}',
    ip_hash TEXT NOT NULL,
    user_agent_hash TEXT NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS auth_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    scope TEXT NOT NULL,
    subject_hash TEXT NOT NULL,
    attempted_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS privacy_acknowledgements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    notice_scope TEXT NOT NULL,
    notice_version TEXT NOT NULL,
    acknowledged_at TEXT NOT NULL,
    UNIQUE (user_id, notice_scope, notice_version),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS workforce_briefs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organization TEXT NOT NULL,
    contact_name TEXT NOT NULL,
    contact_email TEXT NOT NULL,
    contact_phone TEXT NOT NULL DEFAULT '',
    sites TEXT NOT NULL,
    roles_needed TEXT NOT NULL,
    estimated_headcount INTEGER NULL,
    shift_pattern TEXT NOT NULL DEFAULT '',
    target_start_date TEXT NULL,
    service_needs TEXT NOT NULL,
    notes TEXT NOT NULL DEFAULT '',
    privacy_notice_version TEXT NOT NULL,
    privacy_accepted_at TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'submitted',
    submitted_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_jobs_public ON jobs (status, published_at);
CREATE INDEX IF NOT EXISTS idx_job_change_history ON job_change_history (job_id, created_at);
CREATE INDEX IF NOT EXISTS idx_applications_applicant ON applications (applicant_user_id, updated_at);
CREATE INDEX IF NOT EXISTS idx_applications_status ON applications (current_status, updated_at);
CREATE INDEX IF NOT EXISTS idx_history_application ON application_status_history (application_id, created_at);
CREATE INDEX IF NOT EXISTS idx_tasks_application ON application_tasks (application_id, status);
CREATE INDEX IF NOT EXISTS idx_snapshot_history_application ON application_job_snapshot_history (application_id, created_at);
CREATE INDEX IF NOT EXISTS idx_audit_entity ON audit_events (entity_type, entity_id, created_at);
CREATE INDEX IF NOT EXISTS idx_audit_ip_event ON audit_events (ip_hash, event_type, created_at);
CREATE INDEX IF NOT EXISTS idx_auth_attempt_lookup ON auth_attempts (scope, subject_hash, attempted_at);
CREATE INDEX IF NOT EXISTS idx_privacy_ack_user ON privacy_acknowledgements (user_id, acknowledged_at);
CREATE INDEX IF NOT EXISTS idx_workforce_briefs_status ON workforce_briefs (status, submitted_at);

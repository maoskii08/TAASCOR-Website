<?php

declare(strict_types=1);

require_once __DIR__ . '/early.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';

date_default_timezone_set((string) config_value('timezone', 'Asia/Manila'));
enforce_production_https();
apply_security_headers();
start_secure_session();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/audit.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/migrations.php';
require_once __DIR__ . '/recruitment.php';
require_once __DIR__ . '/workforce.php';
require_once __DIR__ . '/upload.php';

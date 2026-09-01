<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

require_post();
verify_csrf();
logout_user();
redirect_to('/');

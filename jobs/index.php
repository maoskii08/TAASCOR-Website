<?php

declare(strict_types=1);

// Physical catalogue entry point keeps /jobs/ available even when directory
// indexes are used; slug detail routes remain governed by router/.htaccess.
require dirname(__DIR__) . '/careers/index.php';

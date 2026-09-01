<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$user = require_role('applicant', '/account/login.php');
require_post();
verify_csrf();

try {
    withdraw_application(
        (int) ($_POST['application_id'] ?? 0),
        (int) $user['id'],
        (string) ($_POST['reason'] ?? '')
    );
    flash('success', 'Your application was withdrawn and the change was added to its status history.');
} catch (InvalidArgumentException | DomainException $exception) {
    flash('error', $exception->getMessage());
}

redirect_to('/applicant/');

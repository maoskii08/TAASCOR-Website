<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$user = require_role('applicant', '/account/login.php');
require_post();
verify_csrf();

try {
    set_task_status_for_applicant(
        (int) ($_POST['task_id'] ?? 0),
        (int) $user['id'],
        (string) ($_POST['status'] ?? '')
    );
    flash('success', 'Task updated.');
} catch (InvalidArgumentException | DomainException $exception) {
    flash('error', $exception->getMessage());
}
redirect_to('/applicant/');

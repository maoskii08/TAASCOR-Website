<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$pageTitle = 'Applicant privacy notice';
$pageEyebrow = 'Draft governance artifact';
$pageDescription = 'This local notice explains the prototype collection flow. DPO approval, official contact details, retention periods, and the production notice URL remain release gates.';
require __DIR__ . '/../app/views/header.php';
?>
<article class="portal-card legal-copy">
    <p class="status-chip status-warning">Version <?= e(config_value('privacy_notice_version')) ?> · Not approved for production</p>
    <h2>What this prototype collects</h2>
    <p>Account and first-stage application data is limited to your name, email address, phone number, current city or municipality, chosen job, work-eligibility confirmation, and privacy acknowledgement.</p>
    <p>The second stage accepts an optional short experience summary and optional resume. Uploaded files are placed in private quarantine and are not exposed through a public download route.</p>

    <h2>Why it is collected</h2>
    <p>The information supports applicant identity, communication, role matching, application review, status updates, and applicant-visible recruitment tasks for the role you selected.</p>

    <h2>What this prototype does not collect</h2>
    <p>This first-contact workflow does not ask for religion, civil status, family information, government identification numbers, medical information, or banking details. Any later-stage collection requires a documented necessity, approved notice, access controls, and retention rule.</p>

    <h2>Production decisions still required</h2>
    <ul>
        <li>Identity and contact channel of the TAASCOR personal information controller and Data Protection Officer.</li>
        <li>Approved purpose, lawful basis, recipients, processors, storage location, and retention schedule.</li>
        <li>How applicants exercise access, correction, objection, erasure, portability, and complaint rights.</li>
        <li>Approved incident-response, file scanning, deletion, and backup controls.</li>
    </ul>
    <p>No production applicant collection should begin until those items are approved and published in clear language.</p>
</article>
<?php require __DIR__ . '/../app/views/footer.php'; ?>

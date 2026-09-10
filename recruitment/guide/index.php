<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/site/bootstrap.php';

$candidateSteps = [
    ['Discover', 'Review only roles published through TAASCOR Careers, including the work location, employment type, responsibilities, requirements, and closing date.', '/jobs/', 'Browse current roles'],
    ['Create your account', 'Use one protected applicant profile for drafts, submissions, status updates, and assigned next steps.', '/account/register.php', 'Create an applicant account'],
    ['Confirm the exact role', 'The selected role stays attached to the application so you do not have to choose the company or position again.', '/jobs/', 'Review role details'],
    ['Apply with minimum data', 'The first stage asks only for contact, location, eligibility, and the approved privacy acknowledgement. Sensitive requirements belong later.', null, null],
    ['Review and submit', 'Add a relevant experience summary, review the captured role terms, certify accuracy, and keep the application receipt.', null, null],
    ['Track progress', 'Your applicant workspace shows dated candidate-safe status changes and the next action without exposing internal notes.', '/account/login.php', 'Sign in to your workspace'],
    ['Attend interviews', 'When scheduled, review the time, timezone, location or meeting method, then follow the confirmation or reschedule instructions.', null, null],
    ['Review an offer', 'Read the exact approved version, expiry, and response instructions before accepting or declining.', null, null],
    ['Complete onboarding', 'Only applicable requirements appear, with a purpose, owner, due date, visibility, acceptable alternatives, and help route.', null, null],
    ['Move into employee access', 'After offer acceptance and independently approved readiness, HR completes the governed employee conversion. Employee HRIS access begins only after activation.', null, null],
];

$operationsSteps = [
    ['Requisition', 'A named owner prepares headcount, assignment, target dates, and business context. An authorized approver records the decision.'],
    ['Publication', 'Recruitment publishes candidate-safe job content only from an approved requisition and keeps version and channel history.'],
    ['Screening', 'Authorized staff review assigned applications against approved criteria and record candidate-safe stage changes.'],
    ['Interview', 'Coordinators schedule in a named timezone; panel members submit independent structured scorecards.'],
    ['Offer', 'A preparer creates an immutable version and an independent approver authorizes delivery.'],
    ['Onboarding', 'HR applies the approved template, resolves dependencies and exceptions, and independently approves readiness.'],
    ['Employee conversion', 'HRIS checks duplicates, creates the minimum employee record once, and reconciles it to the approved candidate payload.'],
];

$topics = [
    ['id' => 'accounts', 'label' => 'Accounts and privacy', 'copy' => 'Candidate and employee identities remain separate. Application data is collected only after the approved notice and release controls are active.'],
    ['id' => 'applications', 'label' => 'Applications and status', 'copy' => 'Every submission retains the accepted job snapshot, a dated history, a candidate-safe status, and an accountable next action.'],
    ['id' => 'interviews', 'label' => 'Interviews', 'copy' => 'Invitations must state time, timezone, location or meeting method, confirmation steps, rescheduling, and an accessibility support route.'],
    ['id' => 'offers', 'label' => 'Offers', 'copy' => 'Candidates review one immutable approved version with its expiry. Preparation, approval, delivery, and response evidence remain distinct.'],
    ['id' => 'onboarding', 'label' => 'Onboarding requirements', 'copy' => 'Each request explains why it is needed, who can see it, when it is due, what alternatives are accepted, and how to ask for help.'],
    ['id' => 'documents', 'label' => 'Documents', 'copy' => 'Files are requested only at the approved stage and remain private through quarantine, malware scanning, authorized review, retention, and deletion controls.'],
    ['id' => 'safety', 'label' => 'Safety and support', 'copy' => 'Use only published TAASCOR routes. TAASCOR does not require a recruitment fee. Pause before sharing identifiers, medical data, documents, or payment details through an unverified message.'],
];

taascor_page_start([
    'title' => 'Recruitment and onboarding guide',
    'description' => 'A complete guide to the TAASCOR candidate journey, recruitment operations, onboarding controls, and employee handoff.',
    'active' => 'jobs',
    'styles' => ['/assets/css/recruitment-guide.css'],
    'canonical_path' => '/recruitment/guide/',
]);
?>
<main id="main-content" class="guide-page">
    <section class="guide-hero">
        <div class="shell guide-hero-grid">
            <div>
                <p class="eyebrow">TAASCOR Guide Center</p>
                <h1>Know what happens next—before you share anything.</h1>
                <p class="hero-lede">One clear map for candidates, Recruitment, HR, hiring teams, and approvers—from published opportunity through employee activation.</p>
                <div class="hero-actions">
                    <a class="button" href="#candidate-journey">Follow the candidate journey</a>
                    <a class="button button-outline" href="#operations-journey">See the staff workflow</a>
                </div>
            </div>
            <aside class="guide-boundary" aria-label="TAASCOR recruitment domain boundary">
                <p class="section-kicker">One brand. Two protected surfaces.</p>
                <div><span>Public and candidate experience</span><strong>taascor.com</strong><small>Careers, applicant accounts, application guidance, and candidate-visible progress.</small></div>
                <div><span>Employee and staff operations</span><strong>TAASCOR HRIS</strong><small>Authorized recruitment queues, approvals, onboarding administration, and employee records.</small></div>
            </aside>
        </div>
    </section>

    <section class="guide-status-band" aria-label="Current availability">
        <div class="shell">
            <strong>Current release boundary</strong>
            <p>This guide is live. New applicant data collection and connected recruitment transactions remain closed until the approved privacy, security, operating, and release gates are activated. You can safely review the full intended flow now.</p>
        </div>
    </section>

    <section class="guide-search-section" aria-labelledby="guide-search-title">
        <div class="shell guide-search-grid">
            <div>
                <p class="section-kicker">Find an answer</p>
                <h2 id="guide-search-title">Search the journey</h2>
            </div>
            <label class="guide-search" for="guide-search-input">
                <span>Search topics and steps</span>
                <input id="guide-search-input" type="search" placeholder="Try offers, documents, status, or onboarding" autocomplete="off" data-guide-search>
            </label>
        </div>
        <p class="shell guide-search-result" aria-live="polite" data-guide-search-result></p>
    </section>

    <section class="guide-section" id="candidate-journey" aria-labelledby="candidate-title" data-guide-section data-guide-keywords="candidate apply account jobs interview offer onboarding documents status privacy">
        <div class="shell">
            <div class="guide-section-heading">
                <div><p class="section-kicker">Candidate path</p><h2 id="candidate-title">From role discovery to employee access</h2></div>
                <p>The next request appears only when the previous stage and its safeguards are complete.</p>
            </div>
            <ol class="journey-timeline">
                <?php foreach ($candidateSteps as $index => [$title, $copy, $href, $linkLabel]): ?>
                    <li data-guide-item data-guide-keywords="<?= taascor_escape(strtolower($title . ' ' . $copy)) ?>">
                        <span class="journey-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <div><h3><?= taascor_escape($title) ?></h3><p><?= taascor_escape($copy) ?></p><?php if ($href !== null): ?><a class="text-link" href="<?= taascor_escape($href) ?>"><?= taascor_escape((string) $linkLabel) ?></a><?php endif; ?></div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="guide-section guide-section-operations" id="operations-journey" aria-labelledby="operations-title" data-guide-section data-guide-keywords="staff recruitment hris requisition publication screening interview offer onboarding conversion">
        <div class="shell">
            <div class="guide-section-heading">
                <div><p class="section-kicker">Authorized operations</p><h2 id="operations-title">The controlled staff workflow</h2></div>
                <p>Staff work stays inside TAASCOR HRIS. The public website never exposes internal queues, candidate records, or employee data.</p>
            </div>
            <div class="operations-grid">
                <?php foreach ($operationsSteps as $index => [$title, $copy]): ?>
                    <article data-guide-item data-guide-keywords="<?= taascor_escape(strtolower($title . ' ' . $copy)) ?>"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><h3><?= taascor_escape($title) ?></h3><p><?= taascor_escape($copy) ?></p></article>
                <?php endforeach; ?>
            </div>
            <div class="guide-staff-entry">
                <div><p class="section-kicker">Staff only</p><h3>Continue inside the protected HRIS</h3><p>Use your authorized employee or recruitment credentials. Applicant credentials do not work here.</p></div>
                <a class="button button-outline" href="https://taascor.visiotechsolutions.com/hris/login/" target="_blank" rel="noopener noreferrer">Open TAASCOR HRIS <span class="sr-only">in a new tab</span></a>
            </div>
        </div>
    </section>

    <section class="guide-section" aria-labelledby="topics-title" data-guide-section data-guide-keywords="guide topics help privacy application interview offer onboarding document safety support">
        <div class="shell">
            <div class="guide-section-heading"><div><p class="section-kicker">Detailed guidance</p><h2 id="topics-title">What each stage must make clear</h2></div></div>
            <div class="guide-topic-grid">
                <?php foreach ($topics as $topic): ?>
                    <details id="<?= taascor_escape($topic['id']) ?>" data-guide-item data-guide-keywords="<?= taascor_escape(strtolower($topic['label'] . ' ' . $topic['copy'])) ?>">
                        <summary><span><?= taascor_escape($topic['label']) ?></span><span aria-hidden="true">+</span></summary>
                        <p><?= taascor_escape($topic['copy']) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="guide-help" aria-labelledby="guide-help-title">
        <div class="shell guide-help-grid">
            <div><p class="section-kicker">Need help or verification?</p><h2 id="guide-help-title">Use a published TAASCOR route.</h2><p>For accessibility needs, suspicious recruitment contact, or general routing, verify the request before sharing information.</p></div>
            <div class="hero-actions"><a class="button" href="/contact/">Contact routes</a><a class="button button-outline" href="/legal/anti-fraud/">Recruitment safety</a></div>
        </div>
    </section>
</main>
<script src="<?= taascor_escape(taascor_asset_url('/assets/js/recruitment-guide.js')) ?>" defer></script>
<?php taascor_page_end(); ?>

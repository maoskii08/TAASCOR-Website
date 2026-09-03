<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/site/bootstrap.php';

$collectionEnabled = privacy_collection_is_enabled('workforce');

$values = [
    'organization' => trim((string) ($_POST['organization'] ?? '')),
    'contact_name' => trim((string) ($_POST['contact_name'] ?? '')),
    'contact_email' => trim((string) ($_POST['contact_email'] ?? '')),
    'contact_phone' => trim((string) ($_POST['contact_phone'] ?? '')),
    'sites' => trim((string) ($_POST['sites'] ?? '')),
    'roles_needed' => trim((string) ($_POST['roles_needed'] ?? '')),
    'estimated_headcount' => trim((string) ($_POST['estimated_headcount'] ?? '')),
    'shift_pattern' => trim((string) ($_POST['shift_pattern'] ?? '')),
    'target_start_date' => trim((string) ($_POST['target_start_date'] ?? '')),
    'service_needs' => trim((string) ($_POST['service_needs'] ?? '')),
    'notes' => trim((string) ($_POST['notes'] ?? '')),
];
$errors = [];

if (is_post()) {
    verify_csrf();
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        audit_event('workforce_brief.spam_rejected', 'workforce_brief', null, [], null, false);
        http_abort(400, 'The workforce brief could not be accepted.');
    }

    try {
        if (function_exists('workforce_brief_rate_limited') && workforce_brief_rate_limited()) {
            throw new PublicRateLimitException('Too many briefs were submitted from this connection. Please wait before trying again.');
        }
        $briefId = create_workforce_brief($values + [
            'privacy_accepted' => isset($_POST['privacy_accepted']),
        ]);
        $_SESSION['_workforce_receipt'] = $briefId;
        redirect_to('/workforce/?submitted=1');
    } catch (InvalidArgumentException | DomainException | PublicRateLimitException $exception) {
        $errors[] = $exception->getMessage();
    } catch (Throwable $exception) {
        $errors[] = config_value('debug')
            ? $exception->getMessage()
            : 'The brief could not be recorded. Please try again after the enquiry service is configured.';
    }
}

$receipt = null;
if (($_GET['submitted'] ?? '') === '1' && isset($_SESSION['_workforce_receipt'])) {
    $receipt = (int) $_SESSION['_workforce_receipt'];
    unset($_SESSION['_workforce_receipt']);
}

$privacyVersion = (string) config_value('workforce_privacy_notice_version', 'draft');
$noticeIsDraft = privacy_notice_is_draft('workforce');

taascor_page_start([
    'title' => 'Plan a workforce',
    'description' => 'Shape a reviewable workforce brief with roles, sites, headcount, timing, service needs, and named contact context.',
    'active' => 'solutions',
    'styles' => ['/assets/css/workforce.css'],
    'robots' => $noticeIsDraft ? 'noindex,nofollow' : 'index,follow',
]);
?>
<main id="main-content">
    <section class="workforce-hero" aria-labelledby="workforce-title">
        <div class="shell workforce-hero-grid">
            <div>
                <p class="eyebrow">Build a workforce</p>
                <h1 id="workforce-title">Start with the operating truth, not a generic contact form.</h1>
                <p class="hero-lede">Frame the roles, sites, scale, schedule, timing, and service boundary TAASCOR should evaluate. Estimates remain planning inputs until owners approve a proposal and operating model.</p>
            </div>
            <dl class="brief-map" aria-label="Workforce brief sequence">
                <div><dt>01</dt><dd>Demand</dd></div>
                <div><dt>02</dt><dd>Worksite</dd></div>
                <div><dt>03</dt><dd>Formation</dd></div>
                <div><dt>04</dt><dd>Controls</dd></div>
            </dl>
        </div>
    </section>

    <?php if ($receipt !== null): ?>
        <section class="workforce-scene" aria-labelledby="brief-recorded-title">
            <div class="shell narrow">
                <div class="receipt-panel" role="status">
                    <span class="receipt-code">BRIEF / WB-<?= str_pad((string) $receipt, 6, '0', STR_PAD_LEFT) ?></span>
                    <h2 id="brief-recorded-title">Your workforce brief is recorded.</h2>
                    <p>This prototype receipt confirms only that the brief was stored locally. A production review queue, accountable owner, response channel, scope, availability, timing, commercial terms, and legal responsibilities still require approval; this page does not represent an accepted order or service commitment.</p>
                    <div class="hero-actions">
                        <a class="button" href="/solutions/">Review the service framework</a>
                        <a class="button button-outline" href="/workforce/">Create another brief</a>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="workforce-scene" aria-labelledby="brief-form-title">
            <div class="shell workforce-layout">
                <aside class="brief-intro">
                    <p class="section-kicker">Governed first touch</p>
                    <h2 id="brief-form-title">Shape the decision brief.</h2>
                    <p>Required fields capture only the information needed to route and assess an initial workforce enquiry. Do not include employee records, applicant résumés, government identifiers, medical data, passwords, payroll files, or client-confidential attachments.</p>
                    <ol>
                        <li><span>01</span>Describe the work and locations.</li>
                        <li><span>02</span>Estimate scale, shifts, and timing.</li>
                        <li><span>03</span>Identify the service support to evaluate.</li>
                        <li><span>04</span>Resolve ownership and evidence before commitment.</li>
                    </ol>
                    <?php if ($noticeIsDraft): ?>
                        <div class="draft-notice" role="note"><strong>Collection safeguard</strong><p>This route accepts workforce briefs only when an approved privacy notice and retention process are active.</p></div>
                    <?php endif; ?>
                </aside>

                <section class="brief-form-panel">
                    <?php if ($errors): ?>
                        <div class="form-alert" role="alert">
                            <strong>Check the workforce brief.</strong>
                            <ul><?php foreach ($errors as $error): ?><li><?= taascor_escape($error) ?></li><?php endforeach; ?></ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!$collectionEnabled): ?>
                        <div class="draft-notice" role="status"><strong>Collection unavailable</strong><p>This route will accept workforce briefs only after the approved privacy notice and explicit collection enablement are configured.</p></div>
                    <?php else: ?>
                    <form method="post" action="/workforce/" class="workforce-form">
                        <?= csrf_field() ?>
                        <div class="trap-field" aria-hidden="true">
                            <label for="website">Website</label>
                            <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>

                        <fieldset>
                            <legend>Organization and contact</legend>
                            <div class="form-grid form-grid-two">
                                <label><span>Organization</span><input name="organization" value="<?= taascor_escape($values['organization']) ?>" autocomplete="organization" maxlength="160" required></label>
                                <label><span>Contact name</span><input name="contact_name" value="<?= taascor_escape($values['contact_name']) ?>" autocomplete="name" maxlength="120" required></label>
                                <label><span>Work email</span><input name="contact_email" type="email" value="<?= taascor_escape($values['contact_email']) ?>" autocomplete="email" maxlength="190" required></label>
                                <label><span>Phone <small>Optional</small></span><input name="contact_phone" type="tel" value="<?= taascor_escape($values['contact_phone']) ?>" autocomplete="tel" maxlength="30"></label>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Workforce demand</legend>
                            <div class="form-grid">
                                <label><span>Sites or locations</span><textarea name="sites" rows="3" maxlength="1000" required><?= taascor_escape($values['sites']) ?></textarea></label>
                                <label><span>Roles needed</span><textarea name="roles_needed" rows="4" maxlength="1500" required><?= taascor_escape($values['roles_needed']) ?></textarea></label>
                            </div>
                            <div class="form-grid form-grid-two">
                                <label><span>Estimated headcount <small>Optional planning input</small></span><input name="estimated_headcount" type="number" value="<?= taascor_escape($values['estimated_headcount']) ?>" min="1" max="100000" inputmode="numeric"></label>
                                <label><span>Target start <small>Optional</small></span><input name="target_start_date" type="date" value="<?= taascor_escape($values['target_start_date']) ?>"></label>
                            </div>
                            <label><span>Shift pattern <small>Optional</small></span><textarea name="shift_pattern" rows="3" maxlength="500"><?= taascor_escape($values['shift_pattern']) ?></textarea></label>
                        </fieldset>

                        <fieldset>
                            <legend>Service boundary</legend>
                            <label><span>Service needs to evaluate</span><textarea name="service_needs" rows="5" maxlength="1500" required><?= taascor_escape($values['service_needs']) ?></textarea></label>
                            <label><span>Constraints or context <small>Optional</small></span><textarea name="notes" rows="5" maxlength="2000"><?= taascor_escape($values['notes']) ?></textarea></label>
                        </fieldset>

                        <label class="consent-row">
                            <input type="checkbox" name="privacy_accepted" value="1" required>
                            <span>I reviewed the <a href="<?= taascor_escape((string) config_value('workforce_privacy_notice_url', '/legal/privacy/')) ?>" target="_blank" rel="noopener">privacy framework</a> (<?= taascor_escape($privacyVersion) ?>) and understand this is an enquiry, not an accepted order.</span>
                        </label>
                        <button class="button" type="submit">Record workforce brief</button>
                        <p class="submit-note">No payment, employee data, résumé, or credential is required for this first step.</p>
                    </form>
                    <?php endif; ?>
                </section>
            </div>
        </section>
    <?php endif; ?>

    <section class="brief-boundary" aria-labelledby="brief-boundary-title">
        <div class="shell">
            <p class="section-kicker">What happens next</p>
            <h2 id="brief-boundary-title">Review before promise.</h2>
            <div class="boundary-grid">
                <article><span>01</span><h3>Completeness review</h3><p>Role, site, timing, operating constraints, and decision owners are checked for gaps.</p></article>
                <article><span>02</span><h3>Capability confirmation</h3><p>Availability, geography, systems, legal boundary, safety, and service scope are verified for this enquiry.</p></article>
                <article><span>03</span><h3>Owned proposal</h3><p>Only approved documentation can define commercial terms, responsibilities, measures, and mobilization gates.</p></article>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

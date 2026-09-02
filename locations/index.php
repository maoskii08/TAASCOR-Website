<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Locations',
    'description' => 'Use the right TAASCOR route for a worksite, job location, meeting destination, or corporate-location evidence request.',
    'active' => 'about',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="locations-page-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Location register</p>
                <h1 id="locations-page-title">A direction should lead to a <em>current destination.</em></h1>
                <p class="hero-lede">Office, branch, worksite, coverage, hours, and service-area details are held behind a verification gate while accountable owners confirm one canonical location register.</p>
                <div class="hero-actions">
                    <a class="button" href="#choose-route">Choose a location route</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the evidence standard</a>
                </div>
                <p class="hero-note">Do not travel based on an older address, job post, social-media entry, or third-party map listing without current confirmation.</p>
            </div>
            <div class="location-hold" aria-hidden="true">
                <div>
                    <span class="pin"></span>
                    <p class="meta">Verified-location layer reserved</p>
                </div>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="location-state-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Publication state / 01</p>
                <h2 id="location-state-title">No unverified pin becomes a public promise.</h2>
                <p class="section-copy">A previous or third-party listing is not enough to establish that an office is open, a site accepts visitors, a branch serves a region, or a role is available there.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Location evidence gate open', 'review') ?>
                <h3>Current addresses are awaiting owner approval</h3>
                <p>This route intentionally does not publish an office count, street address, map pin, operating hours, directions, coverage claim, or service-availability statement until each field has an owner, evidence source, effective date, and review date.</p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="location-proof-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Location record / 02</p>
                <h2 id="location-proof-title">What a publishable location needs.</h2>
                <p class="section-copy">The same controlled record should power the website, directions, recruitment content, service proposals, and authorized portal references.</p>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Identity and address</h3>
                    <p>Approved site name, complete postal address, location type, map reference, and the legal entity or client relationship that permits publication.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Operating status</h3>
                    <p>Whether the location is active, public-facing, appointment-only, restricted, temporary, hiring, or not available for walk-in visits.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Visitor guidance</h3>
                    <p>Approved hours, contact route, accessibility notes, arrival instructions, security requirements, and any restrictions on applicant or client visits.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Service boundary</h3>
                    <p>The exact areas and services the location supports without turning a base address into an unsupported regional-coverage claim.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Ownership</h3>
                    <p>A named facilities or operations owner, a public-content approver, an escalation route, and one source authorized to correct the record.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>Freshness</h3>
                    <p>An effective date, last verification date, next review date, change history, and a rapid removal path when a location closes or changes.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" id="choose-route" aria-labelledby="location-routes-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Choose by intent / 03</p>
                <h2 id="location-routes-title">Preserve the context of your location question.</h2>
            </div>
            <div class="path-grid path-grid-four">
                <article class="role-card">
                    <span class="role-code">EMPLOYER / WORKSITE</span>
                    <h3>Evaluate a workforce location</h3>
                    <p>Describe the worksite, roles, schedule, scale, timing, access, and constraints so coverage and capability can be assessed for that exact need.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                </article>
                <article class="role-card">
                    <span class="role-code">APPLICANT / JOB</span>
                    <h3>Confirm a job location</h3>
                    <p>Use the published job record for role and location context. Do not assume a vacancy exists because a place appeared in an older post.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">View current jobs</a>
                </article>
                <article class="role-card">
                    <span class="role-code">AUTHORIZED USER</span>
                    <h3>Access your workspace</h3>
                    <p>Applicant, employee, client, and staff information belongs in the appropriate authenticated system, not in a public location enquiry.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Choose a secure route</a>
                </article>
                <article class="role-card">
                    <span class="role-code">DUE DILIGENCE</span>
                    <h3>Review publication status</h3>
                    <p>See how corporate, location, compliance, client, and capability statements move from pending evidence to approved public proof.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Open the proof ledger</a>
                </article>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Contact and Support Routes',
    'description' => 'Choose the right TAASCOR route for workforce planning, job applications, corporate evidence, or authorized account access.',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="contact-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Contact by intent</p>
                <h1 id="contact-title">Choose the route that <em>keeps your context intact.</em></h1>
                <p class="hero-lede">A workforce enquiry, job application, evidence request, and account-support need involve different information and owners. Start in the path designed for your purpose.</p>
                <div class="hero-actions">
                    <a class="button" href="#contact-routes">Choose a route</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/legal/anti-fraud/')) ?>">Recruitment safety</a>
                </div>
                <p class="hero-note">Response time, monitored hours, office destinations, escalation targets, and service availability require confirmation for each channel.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Right<br>route</div>
                <span class="network-node node-a">Employer</span>
                <span class="network-node node-b">Applicant</span>
                <span class="network-node node-c">Evidence</span>
                <span class="network-node node-d">Account</span>
            </div>
        </div>
    </section>

    <section class="scene" id="contact-routes" aria-labelledby="contact-routes-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Four routes / 01</p>
                <h2 id="contact-routes-title">Send the minimum information to the right workflow.</h2>
                <p class="section-copy">Do not place passwords, government identifiers, payroll files, medical information, résumés, employee records, or client-confidential data in a public or unrelated enquiry.</p>
            </div>
            <div class="path-grid path-grid-four">
                <article class="role-card">
                    <span class="role-code">EMPLOYER / WORKFORCE</span>
                    <h3>Shape a staffing brief</h3>
                    <p>Describe the roles, worksite, estimated scale, shifts, timing, constraints, and service areas you want TAASCOR to assess.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                </article>
                <article class="role-card">
                    <span class="role-code">APPLICANT / CAREERS</span>
                    <h3>Find a role and apply</h3>
                    <p>Start from a published job record so your application preserves the exact role context and uses the staged applicant workflow.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Browse current jobs</a>
                </article>
                <article class="role-card">
                    <span class="role-code">DUE DILIGENCE / PROOF</span>
                    <h3>Review a public claim</h3>
                    <p>See which corporate, compliance, client, location, capability, and outcome statements are approved, pending, or intentionally withheld.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Open the proof ledger</a>
                </article>
                <article class="role-card">
                    <span class="role-code">USER / SECURE ACCESS</span>
                    <h3>Access your workspace</h3>
                    <p>Choose the applicant, employee/HRIS, client, or authorized-staff route. Each destination has a different identity and access boundary.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Choose a portal</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="safe-contact-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Before you send / 02</p>
                <h2 id="safe-contact-title">Keep first contact useful and proportionate.</h2>
                <p class="section-copy">The right starting detail helps an owner respond without collecting high-risk information too early.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>State your purpose</h3>
                    <p>Identify whether you are planning workforce demand, applying for a job, seeking evidence, or trying to access an existing workspace.</p>
                </li>
                <li>
                    <h3>Use the structured route</h3>
                    <p>Structured fields preserve role or business context and reduce the chance that your request lands with the wrong owner.</p>
                </li>
                <li>
                    <h3>Share only what is needed now</h3>
                    <p>Use broad context first. Sensitive identity, worker, client, payroll, or health information belongs only in an approved secure stage with an applicable notice.</p>
                </li>
                <li>
                    <h3>Verify unexpected requests</h3>
                    <p>Pause if someone asks for a recruitment fee, password, one-time code, payment, or sensitive document through an unverified channel.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene" aria-labelledby="channel-state-title">
        <div class="shell split">
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Contact register awaiting approval', 'review') ?>
                <h3>General contact facts remain gated</h3>
                <p>A canonical register for public mailboxes, phone numbers, office destinations, operating hours, channel owners, response expectations, and escalation routes still requires business-owner approval. The structured routes above remain the safest public starting point.</p>
            </div>
            <div class="section-heading">
                <p class="section-kicker">Channel ownership / 03</p>
                <h2 id="channel-state-title">A published channel needs someone accountable behind it.</h2>
                <p class="section-copy">Before a contact detail is presented as current, its owner should confirm monitoring, purpose, retention, escalation, out-of-hours behavior, and the process for correcting outdated information.</p>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/locations/')) ?>">Review the location standard</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

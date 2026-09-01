<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Resources',
    'description' => 'Find the appropriate TAASCOR public route for workforce planning, job applications, account access, proof, privacy, accessibility, and recruitment safety.',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="resources-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Resource network</p>
                <h1 id="resources-title">Find the right path before you <em>reach for a file.</em></h1>
                <p class="hero-lede">This hub brings together the public journeys already available and defines the release standard for future guides, templates, checklists, and downloads.</p>
                <div class="hero-actions">
                    <a class="button" href="#available-routes">Browse available routes</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/insights/')) ?>">Explore the insights standard</a>
                </div>
                <p class="hero-note">No unlisted guide, report, policy, template, download, author, date, statistic, or legal position is implied by this resource hub.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Right<br>resource</div>
                <span class="network-node node-a">Plan</span>
                <span class="network-node node-b">Apply</span>
                <span class="network-node node-c">Verify</span>
                <span class="network-node node-d">Access</span>
            </div>
        </div>
    </section>

    <section class="scene" id="available-routes" aria-labelledby="available-routes-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Available routes / 01</p>
                <h2 id="available-routes-title">Start in the surface built for your purpose.</h2>
                <p class="section-copy">These links route an employer, applicant, reviewer, or authorized user without asking a generic public form to carry sensitive or unrelated information.</p>
            </div>
            <div class="path-grid path-grid-four">
                <article class="role-card">
                    <span class="role-code">EMPLOYER / PLAN</span>
                    <h3>Workforce Planner</h3>
                    <p>Frame roles, sites, scale, shifts, timing, constraints, and service needs as a non-binding brief for review.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Plan a workforce</a>
                </article>
                <article class="role-card">
                    <span class="role-code">APPLICANT / OPPORTUNITY</span>
                    <h3>Jobs and applications</h3>
                    <p>Start from a published role, preserve job context, review recruitment safety, and use the staged applicant route.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Find work</a>
                </article>
                <article class="role-card">
                    <span class="role-code">REVIEWER / EVIDENCE</span>
                    <h3>Proof ledger</h3>
                    <p>See how corporate, compliance, location, client, capability, security, and outcome statements are governed before publication.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review proof</a>
                </article>
                <article class="role-card">
                    <span class="role-code">USER / AUTHORIZED ACCESS</span>
                    <h3>Portal chooser</h3>
                    <p>Select the appropriate applicant, employee/HRIS, client, or staff destination without blending their identity and access boundaries.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="support-library-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Public guidance / 02</p>
                <h2 id="support-library-title">Read the boundary before sharing information.</h2>
                <p class="section-copy">These pages explain the current public-framework state. Draft or approval-dependent material remains visibly gated and must not be treated as a signed contract, legal advice, certification, or production-control attestation.</p>
            </div>
            <div class="module-grid module-grid-two">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Privacy framework</h3>
                    <p>Review the intended data-minimization, purpose, access, retention, and data-subject information requirements before using a collection route.</p>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/legal/privacy/')) ?>">Read the privacy framework</a>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Recruitment safety</h3>
                    <p>Recognize suspicious requests, protect credentials and sensitive documents, and use the published job and application routes.</p>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/legal/anti-fraud/')) ?>">Review recruitment safety</a>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Accessibility</h3>
                    <p>Understand the accessibility direction, supported alternatives, known limits, and the evidence needed before a formal conformance claim is made.</p>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/legal/accessibility/')) ?>">Read the accessibility statement</a>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Website terms</h3>
                    <p>Review the boundaries between public information, enquiries, job availability, authenticated systems, and approved contractual commitments.</p>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/legal/terms/')) ?>">Read the website terms</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="library-state-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Download library / 03</p>
                <h2 id="library-state-title">Future files need lifecycle controls too.</h2>
                <p class="section-copy">A download can drift long after the page that introduced it changes. Every future resource therefore needs a controlled source, approved audience, accessible format, version state, owner, and withdrawal route.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Download register awaiting approval', 'review') ?>
                <h3>No downloadable resources are listed yet</h3>
                <p>No guide, report, template, checklist, policy, presentation, form, or dataset is offered until content ownership, source evidence, legal and privacy review, accessibility, file safety, versioning, and publication permission are complete.</p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="release-gates-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Resource lifecycle / 04</p>
                <h2 id="release-gates-title">Publish, review, replace, retire.</h2>
                <p class="section-copy">The resource stays trustworthy only while its source, purpose, version, permissions, and destination remain current.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Define purpose and audience</h3>
                    <p>State who the resource serves, what decision or task it supports, what it excludes, and which route owns the next action.</p>
                </li>
                <li>
                    <h3>Approve content and evidence</h3>
                    <p>Assign an accountable author or reviewer, reconcile sources and claims, and obtain required legal, operational, privacy, client, and media approvals.</p>
                </li>
                <li>
                    <h3>Verify safe and accessible delivery</h3>
                    <p>Check document structure, reading order, alternatives, file format, link behavior, security, data exposure, and usable fallback content.</p>
                </li>
                <li>
                    <h3>Control version and review</h3>
                    <p>Expose the approved version state, content owner, review trigger, replacement history, and a reliable route to correct or withdraw the file.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="resources-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Need a specific route?</p>
                <h2 id="resources-action-title">Choose by intent, not by attachment.</h2>
                <p class="section-copy">Start with a workforce need, a published job, an evidence question, or an authorized account destination.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/contact/')) ?>">Choose a contact route</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/insights/')) ?>">Explore insights</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

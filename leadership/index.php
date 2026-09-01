<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Leadership and Accountability',
    'description' => 'Understand the verification and publication standard for TAASCOR leadership, organization structure, biographies, and accountable ownership.',
    'active' => 'about',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="leadership-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Leadership and accountability</p>
                <h1 id="leadership-title">Names matter when the <em>authority is current.</em></h1>
                <p class="hero-lede">Leadership roles, appointments, biographies, reporting lines, and portraits will appear only after Executive Office and HR confirm the current structure and approve public use.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/about/')) ?>">Explore the TAASCOR model</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the publication standard</a>
                </div>
                <p class="hero-note">This page does not infer current officeholders or an organization chart from older public material.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Named<br>authority</div>
                <span class="network-node node-a">Mandate</span>
                <span class="network-node node-b">Owner</span>
                <span class="network-node node-c">Evidence</span>
                <span class="network-node node-d">Review</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="leadership-state-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Publication state / 01</p>
                <h2 id="leadership-state-title">The current leadership register is awaiting approval.</h2>
                <p class="section-copy">Publishing an outdated name or role can misdirect applicants, clients, employees, authorities, and escalation decisions. Missing evidence therefore stays visible instead of being silently filled.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Leadership evidence gate open', 'review') ?>
                <h3>No names, titles, biographies, portraits, or reporting lines are released here yet</h3>
                <p>The current appointment or organization evidence, approved biography, image rights, public-display permission, effective date, and next review are still required for each record.</p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="accountability-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Accountability map / 02</p>
                <h2 id="accountability-title">The functions a credible digital experience must resolve.</h2>
                <p class="section-copy">These are governance responsibilities to assign and verify; they are not presented as TAASCOR’s approved organization chart or current job titles.</p>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Executive accountability</h3>
                    <p>Accountability to approve the corporate position, public commitments, operating mandate, material risk decisions, and authority delegated to functional owners.</p>
                    <?= taascor_status_tag('Named owner required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Workforce operations</h3>
                    <p>Accountability for service readiness, location and capacity truth, operating boundaries, client handoffs, escalations, and evidence of delivery.</p>
                    <?= taascor_status_tag('Named owner required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Recruitment and applicant experience</h3>
                    <p>Accountability for job accuracy, candidate communication, screening policy, stage decisions, safe hiring channels, and recruitment-workflow evidence.</p>
                    <?= taascor_status_tag('Named owner required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>People and payroll governance</h3>
                    <p>Accountability for employee policy, HR and payroll process boundaries, exception authority, communications, and human approval for material decisions.</p>
                    <?= taascor_status_tag('Named owner required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Privacy, security, and systems</h3>
                    <p>Accountability for data purpose, access boundaries, system controls, incident routes, retention, vendor dependencies, and evidence for public security statements.</p>
                    <?= taascor_status_tag('Named owner required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>Public content and proof</h3>
                    <p>Accountability for canonical copy, media rights, claims, client permissions, evidence expiry, accessibility, search content, and corrections across public surfaces.</p>
                    <?= taascor_status_tag('Named owner required', 'review') ?>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="profile-standard-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Profile standard / 03</p>
                <h2 id="profile-standard-title">A public profile is a controlled record.</h2>
                <p class="section-copy">Approved profiles should help people understand authority and expertise without disclosing personal information that has no public purpose.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Confirm appointment and authority</h3>
                    <p>Validate the current role, effective date, reporting context, public-facing mandate, and approving corporate source.</p>
                </li>
                <li>
                    <h3>Approve the biography</h3>
                    <p>Use concise, relevant, source-backed experience and responsibilities; remove unsupported superlatives, private details, and expired credentials.</p>
                </li>
                <li>
                    <h3>Confirm media rights and accessibility</h3>
                    <p>Document portrait ownership and consent, provide meaningful alternative text, and offer an accessible structure rather than an image-only organization chart.</p>
                </li>
                <li>
                    <h3>Set review and removal rules</h3>
                    <p>Assign a content owner, next review date, correction route, and prompt unpublishing process when an appointment or permission changes.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="leadership-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Enter the network</p>
                <h2 id="leadership-action-title">Choose the path with the right owner and controls.</h2>
                <p class="section-copy">Start a workforce brief, explore current job records, or use the appropriate authenticated portal for an existing relationship.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Plan a workforce</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Find work</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

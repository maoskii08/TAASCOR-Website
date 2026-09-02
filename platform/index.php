<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Workforce Platform',
    'description' => 'Explore TAASCOR’s governed workforce-platform model and the boundaries between public information, operational workflows, and secure employee systems.',
    'active' => 'platform',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="platform-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Operate and support</p>
                <h1 id="platform-title">One lifecycle. <em>Clearer handoffs.</em></h1>
                <p class="hero-lede">The platform story is being verified module by module. This public tour explains the intended workforce-data flow without exposing live employee or payroll information.</p>
                <div class="hero-actions">
                    <a class="button" href="#lifecycle">Explore the lifecycle</a>
                    <a class="button button-outline" href="https://taascor.visiotechsolutions.com/hris/login/" target="_blank" rel="noopener noreferrer">Open existing HRIS login <span class="sr-only">in a new tab</span></a>
                </div>
                <p class="hero-note">Capability, production status, roles, integrations, controls, and commercial boundaries require system-owner approval before publication as fact.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Governed<br>workflow</div>
                <span class="network-node node-a">Time input</span>
                <span class="network-node node-b">Validation</span>
                <span class="network-node node-c">Payroll basis</span>
                <span class="network-node node-d">Worker record</span>
            </div>
        </div>
    </section>

    <section class="scene" id="lifecycle" aria-labelledby="lifecycle-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Conceptual lifecycle / 01</p>
                <h2 id="lifecycle-title">A system is useful when every transition has an owner.</h2>
                <p class="section-copy">This target model organizes the public platform story. It does not assert that every module or automated transition is currently enabled.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Capture the authorized source</h3>
                    <p>Identify the official workforce, assignment, schedule, and time inputs, plus the person or system allowed to create and correct them.</p>
                </li>
                <li>
                    <h3>Validate before value moves</h3>
                    <p>Route incomplete, conflicting, late, or unsupported inputs to an accountable review step instead of silently treating gaps as zero.</p>
                </li>
                <li>
                    <h3>Approve the payroll basis</h3>
                    <p>Separate calculation preparation from human approval, posting, locking, distribution, and any money- or identity-impacting exception.</p>
                </li>
                <li>
                    <h3>Release the right view</h3>
                    <p>Give each role only the information and actions required for its purpose; employee, client, payroll, HR, and administrator access must remain distinct.</p>
                </li>
                <li>
                    <h3>Retain evidence and resolve exceptions</h3>
                    <p>Changes, approvals, exports, and status movement should be traceable, with retention and access rules approved by the relevant owners.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="platform-model-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Capability model / 02</p>
                <h2 id="platform-model-title">Six modules to verify, not six claims to assume.</h2>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Worker and assignment records</h3>
                    <p>Target view: role-governed records connecting a person, assignment, worksite, schedule, status, and effective period.</p>
                    <?= taascor_status_tag('Owner verification required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Time and attendance intake</h3>
                    <p>Target view: source lineage, receipt status, validation results, correction ownership, and a visible cut-off state.</p>
                    <?= taascor_status_tag('Owner verification required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Validation and exceptions</h3>
                    <p>Target view: explicit gaps, conflicting data, decision queues, supporting evidence, and approved resolution.</p>
                    <?= taascor_status_tag('Owner verification required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Payroll-basis preparation</h3>
                    <p>Target view: calculation inputs and reconciliation separated from final approval, posting, lock, and distribution authority.</p>
                    <?= taascor_status_tag('Owner verification required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Employee self-service</h3>
                    <p>Target view: a secure employee surface for only the records, documents, requests, and notices authorized for that user.</p>
                    <?= taascor_status_tag('Owner verification required', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>Audit and operating insight</h3>
                    <p>Target view: dated, source-backed evidence with clear denominators, ownership, exceptions, and no fictional real-time status.</p>
                    <?= taascor_status_tag('Owner verification required', 'review') ?>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="roles-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Role boundaries / 03</p>
                <h2 id="roles-title">One network does not mean one permission set.</h2>
                <p class="section-copy">The authentication and authorization model must deny access by default and distinguish the purpose of every role.</p>
            </div>
            <div class="path-grid">
                <article class="role-card">
                    <span class="role-code">ROLE / EMPLOYEE</span>
                    <h3>Personal workforce access</h3>
                    <p>Only the user’s own approved records, notices, documents, and actions, presented with accessible help and recovery paths.</p>
                    <a class="button button-dark" href="https://taascor.visiotechsolutions.com/hris/login/" target="_blank" rel="noopener noreferrer">Employee / HRIS login <span class="sr-only">in a new tab</span></a>
                </article>
                <article class="role-card">
                    <span class="role-code">ROLE / CLIENT</span>
                    <h3>Engagement-level visibility</h3>
                    <p>A client surface should expose only the approved engagement, workforce, service, and report subset defined by contract and privacy review.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Review access paths</a>
                </article>
                <article class="role-card">
                    <span class="role-code">ROLE / AUTHORIZED STAFF</span>
                    <h3>Controlled operating actions</h3>
                    <p>Recruitment, HR, payroll, support, and administration need distinct scopes, approvals, audit events, and escalation responsibilities.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Review access paths</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="data-boundary-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Data boundary / 04</p>
                <h2 id="data-boundary-title">The public site stops where workforce data begins.</h2>
                <p class="section-copy">No public animation, marketing card, analytics event, URL, or browser log should contain live applicant, employee, attendance, payroll, medical, government-ID, or client-confidential data.</p>
            </div>
            <div class="notice-panel">
                <strong>System boundary</strong>
                <p>The external HRIS is a separately governed destination. This page does not authenticate users, inspect account state, or assert the security or availability of that system. Confirm the destination before entering credentials.</p>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/legal/privacy/')) ?>">Read the privacy framework</a>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="platform-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Evaluate fit</p>
                <h2 id="platform-action-title">Start with the workflow, roles, and evidence, not the dashboard.</h2>
                <p class="section-copy">A useful platform discussion maps the current source, exception path, approvals, integrations, privacy boundary, retention, and implementation ownership.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="/contact/">Review platform contact routes</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review evidence posture</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

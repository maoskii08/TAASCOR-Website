<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Proof and Compliance',
    'description' => 'Understand how TAASCOR intends to verify corporate, regulatory, service, privacy, client, and operational claims before they appear as public proof.',
    'active' => 'proof',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="proof-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Proof ledger</p>
                <h1 id="proof-title">Trust should resolve to <em>current evidence.</em></h1>
                <p class="hero-lede">TAASCOR’s public proof standard is simple: identify the claim, match it to the correct entity and period, name an owner, and publish only the wording and assets that have been approved.</p>
                <div class="hero-actions">
                    <a class="button" href="#ledger">Review the evidence ledger</a>
                    <a class="button button-outline" href="/contact/">Review evidence-request routes</a>
                </div>
                <p class="hero-note">Current registration numbers, certificates, client logos, leadership details, locations, and performance claims are intentionally withheld here until the publication gate closes.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Evidence<br>gate</div>
                <span class="network-node node-a">Entity</span>
                <span class="network-node node-b">Authority</span>
                <span class="network-node node-c">Period</span>
                <span class="network-node node-d">Approval</span>
            </div>
        </div>
    </section>

    <section class="scene" id="ledger" aria-labelledby="ledger-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Publication status / 01</p>
                <h2 id="ledger-title">Evidence gaps stay visible. They do not become marketing claims.</h2>
                <p class="section-copy">This ledger communicates the website’s publication posture. “Verification required” is not evidence of absence, failure, or non-compliance; it means the current approved document and wording are not yet available to this implementation.</p>
            </div>
            <div class="evidence-grid">
                <article class="evidence-card">
                    <?= taascor_status_tag('Verification required', 'review') ?>
                    <h3>Corporate identity</h3>
                    <p>Legal name, registration details, corporate history, leadership, and organization information require current official records and display approval.</p>
                </article>
                <article class="evidence-card">
                    <?= taascor_status_tag('Verification required', 'review') ?>
                    <h3>Contractor registration</h3>
                    <p>Any public contractor-registration statement must match the correct legal entity, current framework, issuing office, effective dates, official record, and approved wording.</p>
                </article>
                <article class="evidence-card">
                    <?= taascor_status_tag('Scope-specific', 'neutral') ?>
                    <h3>Service and operating controls</h3>
                    <p>Recruitment, screening, onboarding, time, payroll, statutory, supervision, and support controls should be evidenced for the applicable service, role, location, and period.</p>
                </article>
                <article class="evidence-card">
                    <?= taascor_status_tag('Permission required', 'review') ?>
                    <h3>Clients and outcomes</h3>
                    <p>A relationship, logo, testimonial, case study, job association, or result appears only with current status, usage rights, dated support, and the required approvals.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="register-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Evidence register / 02</p>
                <h2 id="register-title">What closes each publication gate.</h2>
            </div>
            <table class="proof-ledger">
                <thead>
                    <tr>
                        <th scope="col">Proof area</th>
                        <th scope="col">Required before publication</th>
                        <th scope="col">Current website treatment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Legal entity</td>
                        <td>Current official record; exact entity match; evidence date; corporate and legal approval.</td>
                        <td><?= taascor_status_tag('Held', 'review') ?></td>
                    </tr>
                    <tr>
                        <td>Regulatory status</td>
                        <td>Current certificate or official record; issuing office; effective and expiry dates; permitted wording and surfaces.</td>
                        <td><?= taascor_status_tag('Held', 'review') ?></td>
                    </tr>
                    <tr>
                        <td>Service capability</td>
                        <td>Service catalogue; owner; scope, exclusions, locations, controls, evidence, review date, and approved copy.</td>
                        <td><?= taascor_status_tag('Qualified framework', 'neutral') ?></td>
                    </tr>
                    <tr>
                        <td>HRIS capability</td>
                        <td>Module inventory; production status; role matrix; approved demonstration; security and service boundary.</td>
                        <td><?= taascor_status_tag('Conceptual model', 'neutral') ?></td>
                    </tr>
                    <tr>
                        <td>Client proof</td>
                        <td>Relationship state; exact approved name; logo rights; copy; case evidence; approval and permission dates.</td>
                        <td><?= taascor_status_tag('Held', 'review') ?></td>
                    </tr>
                    <tr>
                        <td>Jobs and locations</td>
                        <td>Authoritative owner; current state; dates; closure rules; address/coverage verification; review timestamp.</td>
                        <td><?= taascor_status_tag('Dynamic source only', 'neutral') ?></td>
                    </tr>
                    <tr>
                        <td>Quantitative outcomes</td>
                        <td>Defined metric; population and denominator; source; period; calculation; limitations; owner and approval.</td>
                        <td><?= taascor_status_tag('No unsupported KPI', 'available') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="scene" aria-labelledby="protocol-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Verification protocol / 03</p>
                <h2 id="protocol-title">A four-part test for every public claim.</h2>
                <p class="section-copy">Evidence is useful only when a reader can understand what it proves, for whom, and for how long.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Identity</h3>
                    <p>Confirm the exact legal entity, service, client relationship, role, location, or product module described.</p>
                </li>
                <li>
                    <h3>Authority</h3>
                    <p>Use an official or accountable source and identify the business owner, evidence custodian, and required approver.</p>
                </li>
                <li>
                    <h3>Time</h3>
                    <p>Record the effective period, evidence date, review date, expiry, and the event that removes or refreshes the claim.</p>
                </li>
                <li>
                    <h3>Meaning</h3>
                    <p>Publish precise wording with scope and limitations. Never stretch a document, logo permission, or data point beyond what it supports.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="external-frameworks-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">External frameworks / 04</p>
                <h2 id="external-frameworks-title">Verify against the authority, not a screenshot.</h2>
                <p class="section-copy">These links provide public regulatory context. They do not verify TAASCOR’s current status and this page does not make a legal determination.</p>
            </div>
            <div class="module-grid">
                <article class="content-panel">
                    <p class="meta">Department of Labor and Employment</p>
                    <h3>Contractor registration framework</h3>
                    <p>Review the official Department Order No. 174 registration context and use the government’s current verification route for entity-specific claims.</p>
                    <a class="text-link" href="https://ble.dole.gov.ph/registration-of-job-contractor/" target="_blank" rel="noopener noreferrer">Open DOLE guidance <span class="sr-only">in a new tab</span></a>
                </article>
                <article class="content-panel">
                    <p class="meta">National Privacy Commission</p>
                    <h3>Transparency and proportionality</h3>
                    <p>Recruitment-data collection requires purpose-specific information, necessary fields, appropriate protection, retention decisions, and a route for data-subject rights.</p>
                    <a class="text-link" href="https://privacy.gov.ph/data-privacy-act/" target="_blank" rel="noopener noreferrer">Open NPC guidance <span class="sr-only">in a new tab</span></a>
                </article>
                <article class="content-panel">
                    <p class="meta">Web Accessibility Initiative</p>
                    <h3>WCAG 2.2</h3>
                    <p>The supporting experience targets Level AA-oriented semantic, keyboard, contrast, focus, responsive, and reduced-motion behavior.</p>
                    <a class="text-link" href="https://www.w3.org/TR/WCAG22/" target="_blank" rel="noopener noreferrer">Open WCAG 2.2 <span class="sr-only">in a new tab</span></a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="proof-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Request the right evidence</p>
                <h2 id="proof-action-title">Tell us which entity, service, site, and period you need to evaluate.</h2>
                <p class="section-copy">Evidence availability, permitted use, confidentiality, and response timing are confirmed for the specific request.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="/contact/">Review evidence-request routes</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/legal/privacy/')) ?>">Privacy framework</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

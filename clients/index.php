<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Clients and Relationships',
    'description' => 'Understand the evidence and permission standard TAASCOR applies before naming a client, partner, worksite, or commercial relationship publicly.',
    'active' => 'proof',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="clients-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Permissioned relationships</p>
                <h1 id="clients-title">A logo is not proof. <em>A governed relationship is.</em></h1>
                <p class="hero-lede">Client names, partner descriptions, logos, job associations, and outcomes appear only when the exact relationship, wording, permission, period, and approving owner support public use.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Discuss a workforce need</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the proof ledger</a>
                </div>
                <p class="hero-note">No organization should infer a current relationship, endorsement, job opening, or performance result from this page.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Approved<br>proof</div>
                <span class="network-node node-a">Identity</span>
                <span class="network-node node-b">Scope</span>
                <span class="network-node node-c">Permission</span>
                <span class="network-node node-d">Review</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="client-state-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Publication state / 01</p>
                <h2 id="client-state-title">The public relationship library is awaiting approval.</h2>
                <p class="section-copy">Candidate client and partner references from earlier public material remain in private review until account, marketing, and legal owners reconcile each record.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('All client candidates on hold', 'review') ?>
                <h3>No client identities are released here</h3>
                <p>This page does not publish or imply names, logos, testimonials, active contracts, service scope, hiring relationships, outcomes, or endorsements. Missing approval remains a visible gap, not a substitute claim.</p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="relationship-record-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Relationship record / 02</p>
                <h2 id="relationship-record-title">Six gates before public display.</h2>
                <p class="section-copy">A reusable record separates facts that are often collapsed into one logo card.</p>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Canonical identity</h3>
                    <p>Approved display name, legal name where relevant, spelling, brand assets, and the entity to which the relationship actually belongs.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Relationship state</h3>
                    <p>Client, former client, partner, vendor, prospect, worksite, or another exact category, plus dates and currentness.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Approved scope</h3>
                    <p>The specific service, geography, period, and wording permitted for public disclosure without expanding the underlying facts.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Publicity permission</h3>
                    <p>Written approval for the name, logo, description, channels, territory, effective period, and any required brand guidance.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Job relationship</h3>
                    <p>Only current, owner-approved job records may connect to a company or worksite; old card labels do not create live vacancies.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>Expiry and removal</h3>
                    <p>Every public reference needs a review date, account owner, change history, and a defined route for correction or withdrawal.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="client-proof-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Proof ladder / 03</p>
                <h2 id="client-proof-title">Relationship, testimonial, and outcome are separate approvals.</h2>
                <p class="section-copy">Permission to show a logo does not automatically authorize a testimonial, job association, case study, performance metric, or continuing-relationship claim.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Confirm the relationship</h3>
                    <p>Reconcile the correct organization, commercial entity, account owner, service scope, locations, and effective period.</p>
                </li>
                <li>
                    <h3>Confirm what may be said</h3>
                    <p>Approve the exact public wording and surfaces; keep confidential scope, worker data, pricing, security details, and client-owned information out.</p>
                </li>
                <li>
                    <h3>Confirm media rights</h3>
                    <p>Validate the logo or image source, license, brand rules, territory, term, required attribution, and removal conditions.</p>
                </li>
                <li>
                    <h3>Confirm any outcome independently</h3>
                    <p>Define metric, population, denominator, baseline, period, exclusions, source, owner, and limitations before a result is presented.</p>
                </li>
                <li>
                    <h3>Publish with a review date</h3>
                    <p>Release only the approved fields and revalidate them on schedule or when the relationship, permission, or underlying evidence changes.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="client-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Choose your relationship</p>
                <h2 id="client-action-title">Start a conversation or enter your authorized workspace.</h2>
                <p class="section-copy">Prospective employers can shape a new brief. Existing users should use the role-specific portal path and keep client or workforce data out of public enquiries.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Plan a workforce</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

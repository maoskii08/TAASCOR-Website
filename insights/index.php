<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Insights',
    'description' => 'Explore TAASCOR’s evidence-led editorial framework for workforce planning, recruitment, operations, platform governance, and public proof.',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="insights-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Evidence-led thinking</p>
                <h1 id="insights-title">An idea becomes useful when its <em>evidence is visible.</em></h1>
                <p class="hero-lede">This hub is designed for owner-reviewed thinking on workforce planning, recruitment, operations, platform governance, and proof. Publication begins only after authorship, sources, permissions, limitations, and review ownership are confirmed.</p>
                <div class="hero-actions">
                    <a class="button" href="#editorial-lenses">Explore the editorial lenses</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the proof standard</a>
                </div>
                <p class="hero-note">No article, author, publication date, research claim, client result, or statistic is implied by this holding experience.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Source<br>to insight</div>
                <span class="network-node node-a">Question</span>
                <span class="network-node node-b">Evidence</span>
                <span class="network-node node-c">Review</span>
                <span class="network-node node-d">Context</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="insight-state-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Publication state / 01</p>
                <h2 id="insight-state-title">The editorial register is awaiting accountable owners.</h2>
                <p class="section-copy">A polished card is not a substitute for a source, a qualified author, an approved interpretation, or a process for correction. Until those controls are assigned, this route exposes the standard rather than invented content.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Editorial evidence gate open', 'review') ?>
                <h3>No public articles are listed yet</h3>
                <p>Each future item requires a defined purpose and audience, accountable author or reviewer, source register, claim review, public-use permission, privacy and media review, accessibility check, version state, and next review or withdrawal trigger.</p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" id="editorial-lenses" aria-labelledby="lenses-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Editorial lenses / 02</p>
                <h2 id="lenses-title">Questions worth answering carefully.</h2>
                <p class="section-copy">These lenses define potential areas for reviewed content. They do not assert that a report, programme, dataset, service, or published point of view already exists.</p>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Workforce demand and readiness</h3>
                    <p>How roles, sites, schedules, scale, timing, dependencies, and named decisions become a brief that can be evaluated.</p>
                    <?= taascor_status_tag('Topic awaiting owner', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Candidate journeys and safe recruitment</h3>
                    <p>How role context, staged information collection, transparent decisions, privacy boundaries, and anti-fraud guidance shape applicant trust.</p>
                    <?= taascor_status_tag('Topic awaiting owner', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Operational handoffs</h3>
                    <p>How recruitment, onboarding, worksite readiness, workforce support, time inputs, exceptions, and approvals connect without hiding gaps.</p>
                    <?= taascor_status_tag('Topic awaiting owner', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Platform and data governance</h3>
                    <p>How purpose, source lineage, access boundaries, human authority, retention, audit evidence, and failure states inform workforce systems.</p>
                    <?= taascor_status_tag('Topic awaiting owner', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Measurement and proof</h3>
                    <p>How definitions, populations, denominators, periods, exclusions, limitations, owners, and client permissions determine what a result can support.</p>
                    <?= taascor_status_tag('Topic awaiting owner', 'review') ?>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>Workplace experience</h3>
                    <p>How accessible communication, clear service boundaries, safe escalation, and proportionate information needs affect people at each stage.</p>
                    <?= taascor_status_tag('Topic awaiting owner', 'review') ?>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="editorial-contract-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Editorial contract / 03</p>
                <h2 id="editorial-contract-title">Label what the reader is actually receiving.</h2>
                <p class="section-copy">Facts, interpretation, guidance, and illustrative models serve different purposes. A future publication should make that distinction explicit.</p>
            </div>
            <div class="accordion">
                <details data-disclosure>
                    <summary>Source-backed fact</summary>
                    <p>A bounded statement supported by an identified authoritative source, accountable owner, applicable period, approved wording, and review date.</p>
                </details>
                <details data-disclosure>
                    <summary>Interpretation</summary>
                    <p>An explained reading of evidence that identifies the author or reviewer, reasoning boundary, uncertainty, and material alternative explanations.</p>
                </details>
                <details data-disclosure>
                    <summary>Practical guidance</summary>
                    <p>A recommended approach with a clear audience, scope, assumptions, limitations, and escalation route when legal, employment, security, or operational approval is needed.</p>
                </details>
                <details data-disclosure>
                    <summary>Illustrative model</summary>
                    <p>A teaching or design example explicitly separated from live operations, client evidence, actual performance, and promised service capability.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="insights-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Use the working routes</p>
                <h2 id="insights-action-title">Move from a question to the right next step.</h2>
                <p class="section-copy">Use the resource hub for current public pathways, the proof ledger for claim status, or the structured employer and applicant journeys for a specific need.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/resources/')) ?>">Explore resources</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Plan a workforce</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Find work</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

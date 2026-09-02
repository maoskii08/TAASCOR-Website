<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Case Studies',
    'description' => 'See the evidence, permission, measurement, and review standard required before TAASCOR publishes a client case study.',
    'active' => 'proof',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="case-studies-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Evidence in context</p>
                <h1 id="case-studies-title">A result is credible when the <em>method survives scrutiny.</em></h1>
                <p class="hero-lede">TAASCOR case studies will connect an approved relationship, defined operating need, dated intervention, source-backed result, and material limitations without exposing client or worker information.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Open the proof ledger</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Shape a workforce brief</a>
                </div>
                <p class="hero-note">No public case study is represented as approved or current on this route at this stage.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Dated<br>result</div>
                <span class="network-node node-a">Baseline</span>
                <span class="network-node node-b">Method</span>
                <span class="network-node node-c">Outcome</span>
                <span class="network-node node-d">Limits</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="study-state-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Publication state / 01</p>
                <h2 id="study-state-title">No approved public stories are listed yet.</h2>
                <p class="section-copy">The absence of a case-study card is intentional. It prevents an unsupported relationship, anecdote, metric, or illustrative homepage number from being mistaken for client evidence.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Case-study approval gate open', 'review') ?>
                <h3>Evidence packages are still required</h3>
                <p>Publication needs account-owner confirmation, client permission, source lineage, approved wording, privacy review, a dated measurement contract, and an expiry or review date.</p>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/clients/')) ?>">Review the relationship standard</a>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="anatomy-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Case-study anatomy / 02</p>
                <h2 id="anatomy-title">Six parts of a publishable result.</h2>
                <p class="section-copy">Each approved story should make its scope and uncertainty legible enough for a buyer, candidate, client, or reviewer to understand what the evidence actually supports.</p>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Approved context</h3>
                    <p>Client and worksite identity where permitted, engagement period, operating environment, scope, and the public-use boundary.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Defined problem</h3>
                    <p>The workforce or operating need in the client’s approved language, including important constraints and responsibilities.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Baseline</h3>
                    <p>A dated starting point with a stable metric definition, population, denominator, source, exclusions, and known data gaps.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Intervention</h3>
                    <p>What changed, when, under whose authority, with which dependencies, and which activities remained outside TAASCOR’s scope.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Dated outcome</h3>
                    <p>A source-backed result measured on the same contract as the baseline, reconciled by the data owner, and never implied to be universal.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>Limitations and review</h3>
                    <p>Confounders, missing data, comparability limits, client approval, publication date, next review, and removal conditions.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="measure-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Measurement discipline / 03</p>
                <h2 id="measure-title">A metric is not ready because it looks precise.</h2>
                <p class="section-copy">Before-and-after numbers can still mislead when definitions, populations, windows, ownership, or external drivers differ.</p>
            </div>
            <div class="accordion">
                <details data-disclosure>
                    <summary>What defines the result?</summary>
                    <p>The event, numerator, denominator, unit, population, observation window, exclusions, data source, and calculation owner must be explicit.</p>
                </details>
                <details data-disclosure>
                    <summary>What makes two periods comparable?</summary>
                    <p>The same definitions and populations should be used, with material changes in demand, process, staffing, seasonality, or data coverage disclosed.</p>
                </details>
                <details data-disclosure>
                    <summary>Does improvement prove TAASCOR caused it?</summary>
                    <p>Not automatically. A case study should describe contributing actions and constraints without claiming causality beyond the available design and evidence.</p>
                </details>
                <details data-disclosure>
                    <summary>Can illustrative website figures be reused?</summary>
                    <p>No. Demonstration visuals and synthetic operating figures remain separate from client evidence and cannot become a case-study result without an authoritative source and approval.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="study-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Make the evidence useful</p>
                <h2 id="study-action-title">Start with your operating need, not a borrowed outcome.</h2>
                <p class="section-copy">Every environment has its own baseline, constraints, ownership, and readiness gates. Use the planner to frame what TAASCOR should evaluate for yours.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

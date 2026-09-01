<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Industry Context',
    'description' => 'Explore how TAASCOR evaluates workforce requirements across different operating environments without assuming service fit, coverage, or availability.',
    'active' => 'industries',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="industries-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Industry context</p>
                <h1 id="industries-title">Start with the operating environment. <em>Verify the fit.</em></h1>
                <p class="hero-lede">Industry labels alone do not define a workable service. The worksite, task boundaries, schedule, safety interfaces, volume pattern, and accountable owners do.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Describe your workforce need</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the proof standard</a>
                </div>
                <p class="hero-note">Industry fit, geographic coverage, capacity, timing, controls, and commercial scope must be confirmed for each enquiry.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Operating<br>context</div>
                <span class="network-node node-a">Work</span>
                <span class="network-node node-b">Site</span>
                <span class="network-node node-c">Schedule</span>
                <span class="network-node node-d">Controls</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="context-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Planning contexts / 01</p>
                <h2 id="context-title">Four environments. No blanket availability claim.</h2>
                <p class="section-copy">These are discovery lenses for an employer conversation—not a public statement that TAASCOR currently serves every role, site, region, or business in the category.</p>
            </div>
            <div class="module-grid module-grid-two">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Production and throughput environments</h3>
                    <p>Evaluate task definition, line or cell dependencies, shift design, safe-work interfaces, quality ownership, attendance sources, and ramp constraints.</p>
                    <?= taascor_status_tag('Fit to verify', 'review') ?>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/industries/production-throughput/')) ?>">Review this context lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Distribution and fulfilment environments</h3>
                    <p>Evaluate volume patterns, work zones, material-handling boundaries, peak planning, shift coverage, site access, and operational escalation.</p>
                    <?= taascor_status_tag('Fit to verify', 'review') ?>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/industries/distribution-fulfilment/')) ?>">Review this context lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Office and service-support environments</h3>
                    <p>Evaluate role outcomes, skills, schedules, supervision, data access, confidentiality, service acceptance, and the handoff into day-to-day operations.</p>
                    <?= taascor_status_tag('Fit to verify', 'review') ?>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/industries/office-service-support/')) ?>">Review this context lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Facilities and site-support environments</h3>
                    <p>Evaluate work areas, equipment and materials, health and safety interfaces, access windows, response paths, inspection evidence, and site ownership.</p>
                    <?= taascor_status_tag('Fit to verify', 'review') ?>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/industries/facilities-site-support/')) ?>">Review this context lens</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="fit-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Fit assessment / 02</p>
                <h2 id="fit-title">What turns a category into an assessable brief.</h2>
                <p class="section-copy">A credible answer needs enough context to test readiness and define the boundary between employer, TAASCOR, worker, worksite, system, and external-party responsibilities.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Work and role definition</h3>
                    <p>Document tasks, outcomes, skills, working conditions, exclusions, supervision, and the evidence used to confirm performance.</p>
                </li>
                <li>
                    <h3>Demand pattern</h3>
                    <p>Describe headcount estimates, shifts, peaks, ramp dates, duration, change authority, and what happens when demand moves.</p>
                </li>
                <li>
                    <h3>Worksite readiness</h3>
                    <p>Resolve access, orientation, equipment, safe-work controls, schedules, facilities, transport dependencies, and named site contacts.</p>
                </li>
                <li>
                    <h3>Employment and data handoffs</h3>
                    <p>Identify approved inputs, decision owners, worker communications, time and attendance sources, privacy boundaries, and exception paths.</p>
                </li>
                <li>
                    <h3>Evidence and authorization</h3>
                    <p>Confirm the legal, operational, commercial, capacity, location, and control evidence required before any service commitment is made.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene" aria-labelledby="industry-proof-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Evidence gate / 03</p>
                <h2 id="industry-proof-title">Named industry pages follow evidence—not ambition.</h2>
                <p class="section-copy">A future industry profile should include approved service scope, applicable locations, current capability owners, operating constraints, dated proof, and permitted client or case-study references.</p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Industry register awaiting approval', 'review') ?>
                <h3>Publication remains intentionally limited</h3>
                <p>No client name, logo, location, outcome, capacity, or availability is inferred from a generic industry category. Until the content owner approves a canonical register, enquiries move through the Workforce Planner for specific assessment.</p>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/proof/')) ?>">See how claims are released</a>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="industry-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Build from context</p>
                <h2 id="industry-action-title">Tell us what the work actually requires.</h2>
                <p class="section-copy">Bring the role family, worksite, scale, shifts, timing, operating constraints, and the service boundary you want TAASCOR to evaluate.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/solutions/')) ?>">Explore solution lenses</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

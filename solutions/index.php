<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Workforce Solutions',
    'description' => 'Explore a governed framework for shaping workforce demand, sourcing, deployment, administration, and workforce-system support with TAASCOR.',
    'active' => 'solutions',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="solutions-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Build a workforce</p>
                <h1 id="solutions-title">Turn workforce complexity into a <em>clear operating brief.</em></h1>
                <p class="hero-lede">Begin with the work, the site, the schedule, and the decisions that need owners. TAASCOR structures the conversation so service scope can be evaluated without hiding assumptions.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Shape a workforce brief</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the proof standard</a>
                </div>
                <p class="hero-note">Engagement scope, geography, controls, service levels, and legal responsibilities are confirmed in signed documentation.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Workforce<br>brief</div>
                <span class="network-node node-a">Demand</span>
                <span class="network-node node-b">Formation</span>
                <span class="network-node node-c">Operations</span>
                <span class="network-node node-d">Support</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="solution-lines-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Formation engine / 01</p>
                <h2 id="solution-lines-title">Six lenses for the workforce conversation.</h2>
                <p class="section-copy">These capability areas are a planning framework, not a promise that every service, location, control, or system module applies to every engagement. Each scope is verified with its accountable owner before contracting.</p>
            </div>
            <div class="module-grid">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Workforce staffing</h3>
                    <p>Translate role demand, worksite conditions, schedules, ramp timing, supervision boundaries, and mobilization dependencies into a reviewable brief.</p>
                    <span class="meta">Demand → deployment</span>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/solutions/workforce-staffing/')) ?>">Explore the staffing lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Recruitment and sourcing</h3>
                    <p>Define the candidate profile, objective screening criteria, required evidence, decision owners, and candidate communications for the role.</p>
                    <span class="meta">Profile → decision</span>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/solutions/recruitment-sourcing/')) ?>">Explore the recruitment lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Payroll coordination</h3>
                    <p>Map attendance inputs, validation, approval, payroll-basis preparation, exception ownership, and release dependencies before operations begin.</p>
                    <span class="meta">Input → controlled basis</span>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/solutions/payroll-coordination/')) ?>">Explore the payroll lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>HR administration</h3>
                    <p>Clarify worker records, onboarding handoffs, case ownership, employee communications, and the separation between service and client responsibilities.</p>
                    <span class="meta">Onboarding → support</span>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/solutions/hr-administration/')) ?>">Explore the administration lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">05</span>
                    <h3>Facility support</h3>
                    <p>Frame the required work environment, shift coverage, site controls, equipment dependencies, safety interfaces, and named escalation path.</p>
                    <span class="meta">Site → service model</span>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/solutions/facility-support/')) ?>">Explore the facility lens</a>
                </article>
                <article class="module-card">
                    <span class="module-index">06</span>
                    <h3>HRIS-enabled operations</h3>
                    <p>Explore where structured workforce data and controlled handoffs may support the operating model, subject to verified module and integration scope.</p>
                    <a class="text-link" href="<?= taascor_escape(taascor_url('/solutions/hris-enabled-operations/')) ?>">Explore the HRIS-enabled lens</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="sequence-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Engagement sequence / 02</p>
                <h2 id="sequence-title">Decisions become gates, not assumptions.</h2>
                <p class="section-copy">A professional workforce engagement begins by separating known inputs, open questions, client responsibilities, TAASCOR responsibilities, and external dependencies.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Describe the operating need</h3>
                    <p>Roles, headcount, worksite, shift pattern, timing, task boundaries, constraints, and required outcomes are captured without treating estimates as commitments.</p>
                </li>
                <li>
                    <h3>Resolve readiness inputs</h3>
                    <p>Recruitment criteria, site access, equipment, data exchange, orientation, transport, approvals, and compliance dependencies receive named owners.</p>
                </li>
                <li>
                    <h3>Define the service boundary</h3>
                    <p>The parties document who decides, supplies, approves, validates, escalates, and retains evidence at each stage.</p>
                </li>
                <li>
                    <h3>Agree controls and measures</h3>
                    <p>Service levels, denominators, review cadence, exception treatment, privacy controls, and change authority are defined before performance is reported.</p>
                </li>
                <li>
                    <h3>Authorize and mobilize</h3>
                    <p>Deployment proceeds only after the commercial, operational, legal, data, and site-readiness gates that apply to the engagement are closed.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene" aria-labelledby="boundary-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Responsibility map / 03</p>
                <h2 id="boundary-title">A starting framework for the handoff.</h2>
                <p class="section-copy">The exact boundary varies by engagement. This model helps surface ownership questions; it does not replace a proposal, contract, operating procedure, or legal review.</p>
            </div>
            <div class="module-grid module-grid-two">
                <article class="content-panel">
                    <p class="section-kicker">TAASCOR-side questions</p>
                    <h3>Workforce formation and service operations</h3>
                    <p>What sourcing, screening, onboarding, records, workforce-support, supervision, time-input, exception, and reporting activities are in scope, and which evidence proves completion?</p>
                </article>
                <article class="content-panel">
                    <p class="section-kicker">Client-side questions</p>
                    <h3>Work design and site readiness</h3>
                    <p>Who approves the role, schedule, safe-work conditions, access, equipment, training, day-to-day direction, attendance source, service acceptance, and changes to demand?</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="solutions-faq-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Before you enquire / 04</p>
                <h2 id="solutions-faq-title">Useful answers, without invented certainty.</h2>
            </div>
            <div class="accordion">
                <details data-disclosure>
                    <summary>Can TAASCOR support every location and role?</summary>
                    <p>Coverage, role suitability, timing, authority, and operating capacity must be confirmed for the specific enquiry. This page intentionally makes no universal availability claim.</p>
                </details>
                <details data-disclosure>
                    <summary>Is the six-part framework one bundled service?</summary>
                    <p>No assumption should be made from the website alone. A proposal should identify the applicable components, exclusions, responsibility split, commercial model, and service evidence.</p>
                </details>
                <details data-disclosure>
                    <summary>Does the HRIS automatically form part of an engagement?</summary>
                    <p>Not necessarily. Platform modules, access roles, data exchanges, controls, and support boundaries require product-owner and security confirmation.</p>
                </details>
                <details data-disclosure>
                    <summary>Where can we review current corporate or compliance evidence?</summary>
                    <p>The Proof page describes the publication and request standard. Current documents should be verified against the legal entity, issuing authority, effective period, and permitted use.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" id="start" aria-labelledby="solutions-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Start with the work</p>
                <h2 id="solutions-action-title">Bring the demand. We’ll structure the questions.</h2>
                <p class="section-copy">Include the role family, estimated headcount, location, shift pattern, desired timing, and the operational constraint you are trying to solve. Estimates remain non-binding until reviewed.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                <a class="button button-outline" href="/contact/">Choose a contact route</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

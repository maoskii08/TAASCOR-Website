<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'About',
    'description' => 'Learn how the TAASCOR Workforce Network experience connects employer needs, job opportunities, accountable operating handoffs, and role-specific systems.',
    'active' => 'about',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="about-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">TAASCOR / The workforce network</p>
                <h1 id="about-title">People, operations, and systems—<em>connected with purpose.</em></h1>
                <p class="hero-lede">TAASCOR’s new digital front door is designed around one idea: every workforce journey should make the next action, its owner, and its evidence easier to understand.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/solutions/')) ?>">Build a workforce</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Find work</a>
                </div>
                <p class="hero-note">Canonical mission, vision, values, corporate history, leadership, and office details remain subject to source and publication approval.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">TAASCOR<br>network</div>
                <span class="network-node node-a">Employers</span>
                <span class="network-node node-b">Applicants</span>
                <span class="network-node node-c">Workforce</span>
                <span class="network-node node-d">Operations</span>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="model-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Operating idea / 01</p>
                <h2 id="model-title">A network is only as strong as its handoffs.</h2>
                <p class="section-copy">The experience does not reduce workforce delivery to a logo wall or a decorative counter. It shows how intent moves into governed action.</p>
            </div>
            <ol class="process-list">
                <li>
                    <h3>Employer demand becomes a defined brief</h3>
                    <p>The role, site, scale, timing, constraints, dependencies, and responsibility split are made explicit before an engagement is represented as ready.</p>
                </li>
                <li>
                    <h3>Opportunity becomes an informed choice</h3>
                    <p>Applicants should see current role context, objective requirements, location, status, privacy information, and an application route that preserves job context.</p>
                </li>
                <li>
                    <h3>Activity becomes accountable workflow</h3>
                    <p>Recruitment, onboarding, deployment, workforce support, time, payroll-basis, and exception handoffs need named owners and appropriate human approval.</p>
                </li>
                <li>
                    <h3>Statements become dated proof</h3>
                    <p>Corporate, compliance, capability, client, location, and outcome claims are released only when their sources and permissions support the exact public wording.</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="principles-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Experience principles / 02</p>
                <h2 id="principles-title">Professional does not have to feel impersonal.</h2>
                <p class="section-copy">These are design and operating principles for the new digital experience. They are not presented as TAASCOR’s formally approved corporate values.</p>
            </div>
            <div class="module-grid module-grid-two">
                <article class="module-card">
                    <span class="module-index">01</span>
                    <h3>Clarity before commitment</h3>
                    <p>Separate facts, estimates, options, exclusions, dependencies, and approvals so each person knows what a website statement does—and does not—mean.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">02</span>
                    <h3>Dignity by design</h3>
                    <p>Collect only the applicant or employee information needed at the current stage, explain why, and avoid making sensitive data the price of initial access.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">03</span>
                    <h3>Human authority where it matters</h3>
                    <p>Identity-, employment-, access-, payroll-, and money-impacting decisions retain explicit ownership, review, and auditability.</p>
                </article>
                <article class="module-card">
                    <span class="module-index">04</span>
                    <h3>Evidence over decoration</h3>
                    <p>Use dated documents, permissioned relationships, defined measures, and visible limitations instead of unsupported badges, counters, or simulated live status.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="company-facts-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Corporate profile / 03</p>
                <h2 id="company-facts-title">Company facts are moving through verification.</h2>
                <p class="section-copy">The previous public experiences contain conflicting or unverified corporate, registration, leadership, location, and service statements. This build does not silently choose one version.</p>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/proof/')) ?>">See the publication standard</a>
            </div>
            <div class="content-panel">
                <?= taascor_status_tag('Evidence gate open', 'review') ?>
                <h3>What will appear after approval</h3>
                <ul>
                    <li>Exact legal name and current corporate registration details.</li>
                    <li>One approved mission, vision, and values source.</li>
                    <li>Current leadership names, roles, biographies, and public-use permissions.</li>
                    <li>Verified office locations, directions, contact channels, hours, and service areas.</li>
                    <li>Current service catalogue, operating boundaries, and review date.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="locations-title">
        <div class="shell split">
            <div class="location-hold" aria-hidden="true">
                <div>
                    <span class="pin"></span>
                    <p class="meta">Verified-location layer reserved</p>
                </div>
            </div>
            <div class="section-heading">
                <p class="section-kicker">Locations / 04</p>
                <h2 id="locations-title">Directions should lead to a real, current destination.</h2>
                <p class="section-copy">No office address, branch count, coverage map, or operating-hours claim is published here until Facilities and Operations confirm the address, service status, contact route, effective date, and next review.</p>
                <p>For a current meeting or service-location enquiry, contact TAASCOR directly and confirm the destination before travelling.</p>
                <div class="hero-actions">
                    <a class="button" href="/contact/">Review contact routes</a>
                    <a class="button button-outline" href="/contact/">Choose a contact route</a>
                </div>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="about-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Choose your route</p>
                <h2 id="about-action-title">Enter the network from where you are.</h2>
                <p class="section-copy">Employer, applicant, employee, client, and authorized staff journeys remain distinct so the right information and controls appear at the right time.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/solutions/')) ?>">Build a workforce</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

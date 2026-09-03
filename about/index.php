<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'About',
    'description' => 'Discover TAASCOR’s mission, vision, core values, and approach to connecting employer needs, job opportunities, and accountable workforce operations.',
    'active' => 'about',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="about-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">TAASCOR / The workforce network</p>
                <h1 id="about-title">People, operations, and systems, <em>connected with purpose.</em></h1>
                <p class="hero-lede">TAASCOR’s new digital front door is designed around one idea: every workforce journey should make the next action, its owner, and its evidence easier to understand.</p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/solutions/')) ?>">Build a workforce</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Find work</a>
                </div>
                <p class="hero-note">Our mission, vision, and values shape how we serve clients, support people, and improve the work behind every workforce.</p>
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

    <section class="scene scene-tinted" aria-labelledby="company-profile-title">
        <div class="shell company-profile-layout">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Company profile / 02</p>
                <h2 id="company-profile-title">Built by experienced workforce and outsourcing professionals.</h2>
                <p class="section-copy">TAASCOR Management &amp; General Services Corporation is managed by seasoned HR outsourcing professionals and supports recruitment, onboarding, workforce administration, and operational services for employers and workers.</p>
            </div>
            <div class="company-profile-panel">
                <p class="company-profile-lede">The existing TAASCOR company profile identifies the organization through the following corporate records.</p>
                <dl class="company-fact-list">
                    <div><dt>Legal name</dt><dd>TAASCOR Management &amp; General Services Corporation</dd></div>
                    <div><dt>SEC registration</dt><dd>CS201212925</dd></div>
                    <div><dt>Labor registration</dt><dd>D.O. 174, Series of 2017 · Certificate No. RO1VA-LPO DO174-1220-083-R</dd></div>
                    <div><dt>Operating reach presented</dt><dd>Laguna, Cavite, Bulacan, Metro Manila, Rizal, and other key Philippine regions</dd></div>
                </dl>
                <p class="profile-source">Company-profile continuity from TAASCOR’s existing public website. Certificate currency and official use remain subject to the issuing records.</p>
            </div>
        </div>
    </section>

    <section class="scene purpose-section" aria-labelledby="purpose-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Mission and vision / 03</p>
                <h2 id="purpose-title">A clear purpose for every person we place and every client we support.</h2>
            </div>
            <div class="purpose-grid">
                <article class="purpose-statement">
                    <p class="purpose-label">Our mission</p>
                    <h3>Keep clients moving with the right people.</h3>
                    <p>To continuously support our clients in their outsourcing needs by providing well-trained, skilled, and motivated people.</p>
                </article>
                <article class="purpose-statement purpose-statement-vision">
                    <p class="purpose-label">Our vision</p>
                    <h3>Lead through excellent and varied service.</h3>
                    <p>To be a leading job outsourcing provider in the industry by giving excellent and varied services to our clients.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene values-section" aria-labelledby="values-title">
        <div class="shell values-layout">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Company core values / 04</p>
                <h2 id="values-title">The standards behind the service.</h2>
                <p class="section-copy">TAASCOR believes success grows from a strong culture of dedicated, quality service made possible by motivated employees.</p>
            </div>
            <ol class="values-list">
                <li>
                    <span class="value-number" aria-hidden="true">01</span>
                    <div>
                        <h3>Quality</h3>
                        <p>Quality is the philosophy behind everything we do. It is the result of the high standards we set, sincere effort, and skillful execution by every member of our team.</p>
                    </div>
                </li>
                <li>
                    <span class="value-number" aria-hidden="true">02</span>
                    <div>
                        <h3>Service</h3>
                        <p>Quality, friendly, and personalized service sets us apart. We build positive relationships through hard work, ingenuity, and a passion for service excellence.</p>
                    </div>
                </li>
                <li>
                    <span class="value-number" aria-hidden="true">03</span>
                    <div>
                        <h3>Results Oriented</h3>
                        <p>We focus on positive outcomes in every action and decision. We continuously measure performance and set targets against relevant benchmarks to remain competitive and move forward.</p>
                    </div>
                </li>
                <li>
                    <span class="value-number" aria-hidden="true">04</span>
                    <div>
                        <h3>Responsibility</h3>
                        <p>We believe business has a responsibility to employees, clients, and the community. We do not overlook our duties; we continually raise the standard of our performance.</p>
                    </div>
                </li>
                <li>
                    <span class="value-number" aria-hidden="true">05</span>
                    <div>
                        <h3>Passion</h3>
                        <p>Passion fuels our commitment to the endless pursuit of excellence. It drives us to persevere and meet every challenge.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="company-facts-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Inside TAASCOR / 05</p>
                <h2 id="company-facts-title">Meet the structure behind the service.</h2>
                <p class="section-copy">The company profile now connects directly to TAASCOR’s leadership, organizational chart, office network, and portfolio of organizations served.</p>
            </div>
            <div class="path-grid path-grid-four company-paths">
                <article class="role-card"><span class="role-code">LEADERSHIP</span><h3>Board and management</h3><p>Meet the directors and management roles presented in TAASCOR’s company profile.</p><a class="button button-dark" href="/leadership/">Meet the leadership</a></article>
                <article class="role-card"><span class="role-code">ORGANIZATION</span><h3>Organizational chart</h3><p>Explore the executive, head-office, recruitment, finance, and branch structure in one accessible view.</p><a class="button button-dark" href="/leadership/#organizational-chart">View the organization</a></article>
                <article class="role-card"><span class="role-code">OFFICES</span><h3>Office network</h3><p>Find the seven office and branch addresses carried forward from TAASCOR’s existing public profile.</p><a class="button button-dark" href="/locations/">Explore locations</a></article>
                <article class="role-card"><span class="role-code">PORTFOLIO</span><h3>Organizations served</h3><p>Browse the 27-company portfolio presented by TAASCOR’s existing website.</p><a class="button button-dark" href="/clients/">View the portfolio</a></article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="partnership-title">
        <div class="shell partnership-statement">
            <p class="section-kicker">Partnership / 06</p>
            <blockquote id="partnership-title">“We believe we can contribute to your success in business, and we are looking forward to a fruitful business partnership with your company.”</blockquote>
            <p>TAASCOR Management &amp; General Services Corporation</p>
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

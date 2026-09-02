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
                <p class="hero-note">Mission, vision, and core values are drawn from TAASCOR’s supplied company profile and adapted here for clear digital reading.</p>
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

    <section class="scene scene-tinted purpose-section" aria-labelledby="purpose-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Mission and vision / 02</p>
                <h2 id="purpose-title">A clear purpose for every person we place and every client we support.</h2>
            </div>
            <div class="purpose-grid">
                <article class="purpose-statement">
                    <p class="purpose-label">Our mission</p>
                    <h3>Lead through excellent and varied service.</h3>
                    <p>To be a leading manpower provider in the industry by delivering excellent and varied services to our clients.</p>
                </article>
                <article class="purpose-statement purpose-statement-vision">
                    <p class="purpose-label">Our vision</p>
                    <h3>Keep clients moving forward with the right people.</h3>
                    <p>To continuously support our clients in their outsourcing needs by providing well-trained, skilled, and motivated people.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene values-section" aria-labelledby="values-title">
        <div class="shell values-layout">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Company core values / 03</p>
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

    <section class="scene" aria-labelledby="company-facts-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Corporate profile / 04</p>
                <h2 id="company-facts-title">The remaining company facts are moving through verification.</h2>
                <p class="section-copy">Mission, vision, and values now reflect the supplied company profile. Registration, leadership, location, and detailed service statements remain evidence-gated.</p>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/proof/')) ?>">See the publication standard</a>
            </div>
            <div class="content-panel">
                <?= taascor_status_tag('Evidence gate open', 'review') ?>
                <h3>What will appear after approval</h3>
                <ul>
                    <li>Exact legal name and current corporate registration details.</li>
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
                <p class="section-kicker">Locations / 05</p>
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

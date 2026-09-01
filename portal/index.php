<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Access TAASCOR',
    'description' => 'Choose the correct TAASCOR access path for applicants, employees and HRIS users, clients, or authorized staff.',
    'active' => 'portal',
    'robots' => 'noindex,follow',
    'canonical_path' => '/portal/',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="portal-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Role gateway</p>
                <h1 id="portal-title">Enter through the <em>right access boundary.</em></h1>
                <p class="hero-lede">Applicant, employee, client, and staff systems serve different purposes. Choose your role before entering credentials or sharing information.</p>
                <div class="hero-actions">
                    <a class="button" href="#access-paths">Choose an access path</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/legal/anti-fraud/')) ?>">Check recruitment safety</a>
                </div>
                <p class="hero-note">TAASCOR will not ask for your account password or one-time code through this public gateway.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">Role<br>gateway</div>
                <span class="network-node node-a">Applicant</span>
                <span class="network-node node-b">Employee</span>
                <span class="network-node node-c">Client</span>
                <span class="network-node node-d">Staff</span>
            </div>
        </div>
    </section>

    <section class="scene" id="access-paths" aria-labelledby="access-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Choose your role / 01</p>
                <h2 id="access-title">Four paths. No ambiguous shared login.</h2>
                <p class="section-copy">If you are uncertain which role applies, use the public contact path instead of testing credentials across systems.</p>
            </div>
            <div class="path-grid path-grid-four">
                <article class="role-card">
                    <span class="role-code">PATH / APPLICANT</span>
                    <h3>Search, apply, or continue</h3>
                    <p>Browse published opportunities without an account. Sign in only to continue your own saved application or review your applicant activity.</p>
                    <div class="hero-actions">
                        <a class="button" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Browse careers</a>
                        <a class="button button-dark" href="<?= taascor_escape(taascor_url('/account/login.php')) ?>">Applicant sign in</a>
                    </div>
                </article>
                <article class="role-card">
                    <span class="role-code">PATH / EMPLOYEE &amp; HRIS</span>
                    <h3>Open the workforce system</h3>
                    <p>This external destination is intended for users who already have an authorized HRIS relationship. Confirm the domain before entering credentials.</p>
                    <a class="button" href="https://taascor.visiotechsolutions.com/hris/login/" target="_blank" rel="noopener noreferrer">Open HRIS login <span class="sr-only">in a new tab</span></a>
                </article>
                <article class="role-card">
                    <span class="role-code">PATH / CLIENT</span>
                    <h3>Confirm the approved workspace</h3>
                    <p>A public client-login destination has not been verified for this implementation. Request the approved route from your named TAASCOR contact.</p>
                    <a class="button button-dark" href="/contact/">Review client access routes</a>
                </article>
                <article class="role-card">
                    <span class="role-code">PATH / AUTHORIZED STAFF</span>
                    <h3>Recruitment operations</h3>
                    <p>For TAASCOR staff provisioned for the recruitment workspace. Role and data access remain restricted after authentication.</p>
                    <a class="button button-dark" href="<?= taascor_escape(taascor_url('/staff/login.php')) ?>">Staff sign in</a>
                </article>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="safe-access-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Safe access / 02</p>
                <h2 id="safe-access-title">Pause when the route, message, or request does not look right.</h2>
                <p class="section-copy">A polished login screen is not proof that a message or destination is legitimate.</p>
            </div>
            <div class="content-panel">
                <ul>
                    <li>Navigate from a known TAASCOR page instead of an unsolicited message.</li>
                    <li>Check the full domain before entering an email address, password, or personal record.</li>
                    <li>Do not share passwords, one-time codes, recovery links, or session details with another person.</li>
                    <li>Do not send government identifiers, medical information, or payroll records through an unverified chat or address.</li>
                    <li>Report suspicious recruitment messages through the published contact route.</li>
                </ul>
                <a class="text-link" href="<?= taascor_escape(taascor_url('/legal/anti-fraud/')) ?>">Read recruitment safety guidance</a>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="boundary-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Boundary map / 03</p>
                <h2 id="boundary-title">What this gateway does—and does not do.</h2>
            </div>
            <div class="metric-strip">
                <div><strong>Routes</strong><span>Directs each user to a role-specific destination.</span></div>
                <div><strong>Does not authenticate</strong><span>This public page does not receive or validate credentials.</span></div>
                <div><strong>Does not inspect data</strong><span>No account, application, employee, client, or payroll state appears here.</span></div>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="portal-help-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Need help?</p>
                <h2 id="portal-help-title">Verify the path before trying again.</h2>
                <p class="section-copy">Describe your role and the destination you expected. Never include a password, one-time code, government identifier, medical detail, or payroll record in the enquiry.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="/contact/">Review access-help routes</a>
                <a class="button button-outline" href="/contact/">Choose a contact route</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

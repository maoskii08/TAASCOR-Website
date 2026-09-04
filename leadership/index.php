<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

$directors = [
    ['Ernesto P. Villanueva', 'Chairman & CEO'],
    ['Ernesto S. Villanueva Jr.', 'President & General Manager'],
    ['Helecita S. Villanueva', 'Vice President'],
    ['Nora V. Alcantara', 'Corporate Secretary'],
    ['Emelita M. Aquino', 'Assistant Corporate Secretary'],
    ['Emelita S. Del Valle', 'Finance Manager'],
    ['Rosernie L. Santos', 'Internal Editor'],
];

taascor_page_start([
    'title' => 'Leadership and Organization',
    'description' => 'Meet the TAASCOR board and management team and explore the company organizational chart across head-office, recruitment, finance, and branch operations.',
    'active' => 'about',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="leadership-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Leadership and organization</p>
                <h1 id="leadership-title">The people accountable for <em>moving the organization.</em></h1>
                <p class="hero-lede">TAASCOR’s company profile brings its board, management, head-office functions, recruitment teams, and branch network into one connected organizational view.</p>
                <div class="hero-actions">
                    <a class="button" href="#board">Meet the leadership</a>
                    <a class="button button-outline" href="#organizational-chart">Open the organization chart</a>
                </div>
                <p class="hero-note">Roles are reproduced from TAASCOR’s existing public company profile and should be maintained with Corporate and HR as appointments change.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core">One<br>organization</div>
                <span class="network-node node-a">Board</span>
                <span class="network-node node-b">Head office</span>
                <span class="network-node node-c">Branches</span>
                <span class="network-node node-d">People</span>
            </div>
        </div>
    </section>

    <section class="scene" id="board" aria-labelledby="board-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Board and management / 01</p>
                <h2 id="board-title">Leadership presented with a clear mandate.</h2>
                <p class="section-copy">The board and management directory preserves the names and roles published in TAASCOR’s existing company profile.</p>
            </div>
            <div class="leadership-grid">
                <?php foreach ($directors as $index => [$name, $role]): ?>
                    <article class="leader-card<?= $index === 0 ? ' leader-card-primary' : '' ?>">
                        <span class="leader-index"><?= taascor_escape(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                        <div><h3><?= taascor_escape($name) ?></h3><p><?= taascor_escape($role) ?></p></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" id="organizational-chart" aria-labelledby="org-chart-title">
        <div class="shell">
            <div class="section-heading org-heading">
                <div>
                    <p class="section-kicker">Organizational chart / 02</p>
                    <h2 id="org-chart-title">One view of leadership, support functions, and branch operations.</h2>
                </div>
                <p class="section-copy">The chart is retained as a faithful company-profile reference and paired with a text structure so it remains usable on small screens and with assistive technology.</p>
            </div>
            <figure class="org-chart-media">
                <div class="org-chart-scroll" tabindex="0" aria-label="Scrollable TAASCOR organizational chart">
                    <img src="<?= taascor_escape(taascor_asset_url('/assets/img/organizational-chart.webp')) ?>" width="1365" height="768" loading="lazy" alt="TAASCOR organizational chart showing executive leadership, head-office teams, recruitment and finance roles, and the Cabuyao, Cavite, Cainta, San Pedro, Parian, and Bulacan branches.">
                </div>
                <figcaption>TAASCOR Management &amp; General Services Corporation organizational chart, reproduced from the existing TAASCOR public company profile.</figcaption>
            </figure>

            <details class="org-directory" data-disclosure>
                <summary>Read the organization as text</summary>
                <div class="org-directory-grid">
                    <section aria-labelledby="org-executive">
                        <h3 id="org-executive">Executive chain</h3>
                        <ul>
                            <li><strong>Ernesto P. Villanueva</strong><span>Chairman &amp; CEO</span></li>
                            <li><strong>Ernesto S. Villanueva Jr.</strong><span>President &amp; General Manager</span></li>
                            <li><strong>Emelita S. Del Valle</strong><span>Finance Manager</span></li>
                            <li><strong>Jeanette B. Centes</strong><span>Operations Manager</span></li>
                        </ul>
                    </section>
                    <section aria-labelledby="org-head-office">
                        <h3 id="org-head-office">Head-office functions</h3>
                        <ul>
                            <li><strong>Administration</strong><span>Admin officer and admin support</span></li>
                            <li><strong>Compensation and benefits</strong><span>Benefits, audit, and employee records</span></li>
                            <li><strong>Accounting</strong><span>General accounting, payroll, and billing</span></li>
                            <li><strong>Finance operations</strong><span>Credit, collection, cashier, liaison, and transport support</span></li>
                        </ul>
                    </section>
                    <section aria-labelledby="org-recruitment">
                        <h3 id="org-recruitment">Recruitment and people support</h3>
                        <ul>
                            <li><strong>Marianne E. Pabilan</strong><span>HR Recruitment</span></li>
                            <li><strong>John Carl Banares</strong><span>HR / IT Staff</span></li>
                            <li><strong>May T. Placer</strong><span>HR Recruitment</span></li>
                            <li><strong>Eric Palomata</strong><span>HR / Recruitment Officer</span></li>
                        </ul>
                    </section>
                    <section aria-labelledby="org-branches">
                        <h3 id="org-branches">Branch operations</h3>
                        <ul>
                            <li><strong>Cabuyao</strong><span>Branch operations</span></li>
                            <li><strong>Cavite</strong><span>Branch head and HR officer</span></li>
                            <li><strong>Cainta</strong><span>Recruitment and marketing</span></li>
                            <li><strong>San Pedro</strong><span>HR and marketing</span></li>
                            <li><strong>Parian</strong><span>Branch head and recruitment team</span></li>
                            <li><strong>Bulacan</strong><span>HR team</span></li>
                        </ul>
                    </section>
                </div>
            </details>
        </div>
    </section>

    <section class="scene" aria-labelledby="organization-principles-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">How the structure works / 03</p>
                <h2 id="organization-principles-title">Authority becomes useful when the handoff is visible.</h2>
                <p class="section-copy">The organization chart is more than a roster. It shows how executive direction connects to finance, administration, recruitment, workforce support, and local branch delivery.</p>
            </div>
            <ol class="process-list">
                <li><h3>Executive direction</h3><p>Corporate leadership sets mandate, business priorities, accountability, and organization-wide decisions.</p></li>
                <li><h3>Head-office control</h3><p>Finance, accounting, compensation, benefits, administration, billing, and collection support consistent operations.</p></li>
                <li><h3>Recruitment coordination</h3><p>HR and recruitment teams connect applicant journeys, role requirements, and branch demand.</p></li>
                <li><h3>Branch execution</h3><p>Local teams carry the relationship into the places where applicants, employees, clients, and operations meet.</p></li>
            </ol>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="leadership-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Continue exploring</p>
                <h2 id="leadership-action-title">See the organization in action.</h2>
                <p class="section-copy">Explore TAASCOR’s office network, workforce services, and company values—or enter the route that matches your relationship.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="/locations/">Explore the office network</a>
                <a class="button button-outline" href="/about/">Mission, vision, and values</a>
                <a class="button button-outline" href="/portal/">Access TAASCOR</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

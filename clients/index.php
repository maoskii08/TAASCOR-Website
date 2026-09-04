<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

$portfolio = [
    ['First Sumiden Circuits Inc.', 'first_sumiden.webp', 'Electronics manufacturing'],
    ['Ohgitani Corporation', 'ohgitani.webp', 'Industrial operations'],
    ['Multimix International Manufacturing Corp.', 'multimix.webp', 'Manufacturing and packaging'],
    ['Lazada Philippines', 'lazada.webp', 'E-commerce and fulfilment'],
    ['Shopee Philippines', 'shopee.webp', 'E-commerce and fulfilment'],
    ['Cainiao Smart Logistics Network Philippines', 'cainiao.webp', 'Smart logistics'],
    ['Leslie Corporation', 'leslie.webp', 'Food manufacturing'],
    ['Aboitiz Land', 'aboitiz.webp', 'Property and construction'],
    ['Bi-Chain Sci. & Tech.', 'bichain.webp', 'Warehousing and distribution'],
    ['Fujifilm Optics Phils.', 'fujifilm.webp', 'Optics and manufacturing'],
    ['SIIX Coxon Precision Phils. Corp.', 'siix_coxon.webp', 'Electronics and logistics'],
    ['MTC-Transport', 'mtc_transport.webp', 'Cargo and transport'],
    ['Prime Worldwide Paper Packaging Corporation', 'prime_worldwide.webp', 'Paper and packaging'],
    ['Swhistler Steel', 'swhistler.webp', 'Steel manufacturing'],
    ['Centro Manufacturing Corp.', 'centro.webp', 'Vehicle-body manufacturing'],
    ['Cavite Light Industrial Park', 'cavite_lip.webp', 'Industrial property'],
    ['Globalmaxx Manufacturing Corp.', 'globalmaxx.webp', 'Vehicle equipment'],
    ['Auto 88 Corporation', 'auto88.webp', 'Automotive interiors'],
    ['Sealed Air Corporation', 'sealed_air.webp', 'Packaging solutions'],
    ['WCL Cold Storage Inc.', 'wcl_cold.webp', 'Cold storage'],
    ['CYA Industries Inc.', 'cya_industries.webp', 'Appliance distribution'],
    ['Delta Milling Industries Inc.', 'delta_milling.webp', 'Food production'],
    ['Pasture to Plate Agribusiness Inc.', 'pasture_to_plate.webp', 'Agribusiness'],
    ['Euro-Med Laboratories Phil, Inc.', 'euromed.webp', 'Pharmaceutical manufacturing'],
    ['Yuanshan Electronics (Phils) Inc.', 'yuanshan.webp', 'Electronics manufacturing'],
    ['Maxistar Enterprises Inc.', 'maxistar.webp', 'Vehicle fabrication'],
    ['Shinsei Printing', 'shinsei.webp', 'Printing and production'],
];

taascor_page_start([
    'title' => 'Client Portfolio',
    'description' => 'Explore the 27-company portfolio presented in TAASCOR’s existing public company experience across manufacturing, logistics, e-commerce, facilities, and related industries.',
    'active' => 'proof',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="clients-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">27 organizations in the portfolio</p>
                <h1 id="clients-title">Workforce experience shaped across <em>real operating environments.</em></h1>
                <p class="hero-lede">TAASCOR’s existing public portfolio spans electronics, manufacturing, logistics, e-commerce, food production, facilities, transport, property, and distribution.</p>
                <div class="hero-actions">
                    <a class="button" href="#portfolio">Explore the portfolio</a>
                    <a class="button button-outline" href="/workforce/">Discuss a workforce need</a>
                </div>
                <p class="hero-note">Portfolio inclusion is carried forward from TAASCOR’s existing website. Current vacancies and engagement scope are confirmed separately through TAASCOR’s governed job and workforce routes.</p>
            </div>
            <div class="portfolio-orbit" aria-hidden="true">
                <span class="portfolio-count">27</span>
                <span class="portfolio-orbit-label">organizations</span>
                <span class="portfolio-sector sector-one">Manufacturing</span>
                <span class="portfolio-sector sector-two">Logistics</span>
                <span class="portfolio-sector sector-three">Facilities</span>
            </div>
        </div>
    </section>

    <section class="scene" id="portfolio" aria-labelledby="portfolio-title">
        <div class="shell">
            <div class="section-heading portfolio-heading">
                <div>
                    <p class="section-kicker">Company portfolio / 01</p>
                    <h2 id="portfolio-title">The organizations presented by TAASCOR.</h2>
                </div>
                <p class="section-copy">Each card retains the organization identity and brand asset used on TAASCOR’s existing public portfolio. Job availability is never inferred from a logo.</p>
            </div>
            <div class="client-portfolio-grid">
                <?php foreach ($portfolio as $index => [$name, $image, $sector]): ?>
                    <article class="client-portfolio-card">
                        <div class="client-logo-frame">
                            <img src="<?= taascor_escape(taascor_asset_url('/assets/img/clients/' . $image)) ?>" loading="lazy" decoding="async" alt="<?= taascor_escape($name) ?> brand mark">
                        </div>
                        <div class="client-card-copy">
                            <span><?= taascor_escape(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?> / 27</span>
                            <h3><?= taascor_escape($name) ?></h3>
                            <p><?= taascor_escape($sector) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="portfolio-context-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Portfolio context / 02</p>
                <h2 id="portfolio-context-title">A company card and a current opportunity are different records.</h2>
                <p class="section-copy">The portfolio preserves TAASCOR’s company history. The Careers experience separately controls whether a role is current, where it is based, and how an applicant should respond.</p>
            </div>
            <ol class="process-list">
                <li><h3>Explore the operating context</h3><p>Use the portfolio to understand the range of industries and environments represented in TAASCOR’s existing company story.</p></li>
                <li><h3>Check current opportunities</h3><p>Only the governed job register should be used to determine whether a role is open, its location, requirements, and closing status.</p><a class="text-link" href="/jobs/">View current jobs</a></li>
                <li><h3>Shape a new workforce need</h3><p>Employers can describe the work, site, scale, schedule, target date, and operational dependencies for review.</p><a class="text-link" href="/workforce/">Open the Workforce Planner</a></li>
            </ol>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="client-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Build the next relationship</p>
                <h2 id="client-action-title">Start with the operating need.</h2>
                <p class="section-copy">Bring the roles, location, headcount, shift pattern, timeline, and constraints. TAASCOR can then structure the right workforce conversation.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="/workforce/">Plan a workforce</a>
                <a class="button button-outline" href="/solutions/">Explore services</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

$offices = [
    ['Main Office — San Pedro', '3rd Floor EM Arcade Building, Poblacion, San Pedro, Laguna', 'Main office'],
    ['Cabuyao Branch', 'Units 109–111, Centennial Business Center, Brgy. Pulo, Cabuyao, Laguna', 'Branch office'],
    ['Calamba Branch', 'Unit 5, 3rd Floor, Sta. Cecilia Business Center 2, National Highway, Parian, Calamba City, Laguna', 'Branch office'],
    ['Dasmariñas Branch', '2nd Floor, Giron Building, Governor’s Drive, Brgy. Langkaan, Dasmariñas, Cavite', 'Branch office'],
    ['Cainta Office', 'Unit 208, Jenny’s Avenue Saturn Field Building, Cainta, Rizal', 'Office'],
    ['Finance Office', 'Lot 1, Block 2, Phase 2E, No. 7 Peach Street, Greenwoods Executive Village, Cainta, Rizal', 'Finance'],
    ['Bulacan Branch', 'El Camino Road corner Cancer Street, Phase 3C LVDSN, Brgy. Perez, Meycauayan City, Bulacan', 'Branch office'],
];

taascor_page_start([
    'title' => 'Office Network',
    'description' => 'Explore TAASCOR office and branch locations across Laguna, Cavite, Rizal, and Bulacan.',
    'active' => 'about',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="locations-page-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">TAASCOR office network</p>
                <h1 id="locations-page-title">Local teams, connected through <em>one operating network.</em></h1>
                <p class="hero-lede">TAASCOR’s existing company profile presents seven offices and branches across Laguna, Cavite, Rizal, and Bulacan.</p>
                <div class="hero-actions">
                    <a class="button" href="#office-directory">Explore the directory</a>
                    <a class="button button-outline" href="/contact/">Plan a visit or enquiry</a>
                </div>
                <p class="hero-note">Confirm the destination and appointment route before travelling, especially when responding to a job or recruitment message.</p>
            </div>
            <div class="location-network" aria-hidden="true">
                <span class="location-origin">TAASCOR</span>
                <span class="location-point point-one">Laguna</span>
                <span class="location-point point-two">Cavite</span>
                <span class="location-point point-three">Rizal</span>
                <span class="location-point point-four">Bulacan</span>
            </div>
        </div>
    </section>

    <section class="scene" id="office-directory" aria-labelledby="office-directory-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Office directory / 01</p>
                <h2 id="office-directory-title">Seven points in the TAASCOR network.</h2>
                <p class="section-copy">Addresses are carried forward from TAASCOR’s existing public company profile. Use the appropriate contact or recruitment route to confirm hours, access, and the correct destination for your purpose.</p>
            </div>
            <div class="office-grid">
                <?php foreach ($offices as $index => [$name, $address, $type]): ?>
                    <article class="office-card<?= $index === 0 ? ' office-card-primary' : '' ?>">
                        <div class="office-card-top"><span><?= taascor_escape(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span><span><?= taascor_escape($type) ?></span></div>
                        <h3><?= taascor_escape($name) ?></h3>
                        <address><?= taascor_escape($address) ?></address>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="location-intent-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Choose by intent / 02</p>
                <h2 id="location-intent-title">The right place depends on why you are connecting.</h2>
                <p class="section-copy">A corporate meeting, workforce discussion, application, employee concern, and client request should not all enter through the same door.</p>
            </div>
            <ol class="process-list">
                <li><h3>Employer or workforce enquiry</h3><p>Start with the worksite, roles, schedule, headcount, target date, and operating constraints through the Workforce Planner.</p><a class="text-link" href="/workforce/">Shape a workforce brief</a></li>
                <li><h3>Applicant or job enquiry</h3><p>Use the published job record for the role and location, and verify recruitment messages against TAASCOR’s safety guidance.</p><a class="text-link" href="/jobs/">View current opportunities</a></li>
                <li><h3>Existing applicant, employee, or client</h3><p>Use the relevant authenticated workspace so personal, employment, or client information stays out of public enquiries.</p><a class="text-link" href="/portal/">Choose your portal</a></li>
                <li><h3>Corporate or office visit</h3><p>Confirm the receiving team, appointment time, access instructions, and exact destination before travelling.</p><a class="text-link" href="/contact/">Review contact routes</a></li>
            </ol>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="locations-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Connect to the network</p>
                <h2 id="locations-action-title">Start with context, then choose the location.</h2>
                <p class="section-copy">Tell TAASCOR what you need, where the work happens, and who the request concerns. The right team can then confirm the destination and next step.</p>
            </div>
            <div class="hero-actions">
                <a class="button" href="/workforce/">Plan a workforce</a>
                <a class="button button-outline" href="/portal/">Access TAASCOR</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

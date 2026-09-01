<?php

declare(strict_types=1);

$existingStatus = http_response_code();
$status = $existingStatus === 400 ? 400 : 404;
http_response_code($status);
require_once __DIR__ . '/bootstrap.php';

$isBadRequest = $status === 400;

taascor_page_start([
    'title' => $isBadRequest ? 'Bad Request' : 'Page Not Found',
    'description' => $isBadRequest
        ? 'The request path contained unsupported characters.'
        : 'The requested TAASCOR page could not be found. Return home, browse careers, or choose a role-specific access path.',
    'active' => '',
    'body_class' => 'error-page',
    'robots' => 'noindex,nofollow',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="error-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow"><?= $isBadRequest ? 'Request rejected' : 'Route unavailable' ?></p>
                <p class="error-code" aria-hidden="true"><?= $status ?></p>
                <h1 id="error-title"><?= $isBadRequest ? 'This request cannot enter the <em>active network.</em>' : 'This path is outside the <em>active network.</em>' ?></h1>
                <p class="hero-lede"><?= $isBadRequest ? 'The path contained unsupported control characters. Use a normal site link or enter a valid address.' : 'The page may have moved, the opportunity may have closed, or the address may be incomplete. Choose a current route below.' ?></p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/')) ?>">Return home</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Browse careers</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/portal/')) ?>">Access TAASCOR</a>
                </div>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span>
                <div class="network-core">Route<br>offline</div>
                <span class="network-node node-a">Home</span>
                <span class="network-node node-b">Careers</span>
                <span class="network-node node-c">Portal</span>
                <span class="network-node node-d">Support</span>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

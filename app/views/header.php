<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'TAASCOR Workforce Portal';
$pageEyebrow = $pageEyebrow ?? 'TAASCOR Workforce Network';
$pageDescription = $pageDescription ?? '';
$bodyClass = $bodyClass ?? '';
$navigationUser = auth_user();
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#f4f7fb">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" href="<?= e(taascor_asset_url('/assets/brand/favicon-32.png')) ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?= e(taascor_asset_url('/assets/brand/icon-192.png')) ?>" type="image/png" sizes="192x192">
    <link rel="apple-touch-icon" href="<?= e(taascor_asset_url('/assets/brand/apple-touch-icon.png')) ?>" sizes="180x180">
    <script src="<?= e(taascor_asset_url('/assets/js/theme.js')) ?>"></script>
    <link rel="stylesheet" href="<?= e(taascor_asset_url('/assets/css/portal.css')) ?>">
    <script defer src="<?= e(taascor_asset_url('/assets/js/portal.js')) ?>"></script>
</head>
<body class="portal-body <?= e($bodyClass) ?>">
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="portal-header">
    <a class="portal-brand" href="/" aria-label="TAASCOR home">
        <img class="portal-brand-mark" src="<?= e(taascor_asset_url('/assets/brand/taascor-mark.png')) ?>" width="38" height="33" alt="">
        <span class="portal-brand-copy" aria-hidden="true"><span class="portal-brand-name">TAASCOR</span><span class="portal-brand-legal">Management &amp; General Services Corp.</span></span>
    </a>
    <nav class="portal-nav" aria-label="Portal navigation">
        <a href="/jobs/">Careers</a>
        <?php if ($navigationUser && $navigationUser['role'] === 'applicant'): ?>
            <a href="/applicant/">My applications</a>
            <a href="/account/settings.php">Account</a>
        <?php elseif ($navigationUser && $navigationUser['role'] === 'staff'): ?>
            <a href="/staff/">Staff workspace</a>
        <?php else: ?>
            <a href="/account/login.php">Applicant sign in</a>
        <?php endif; ?>
        <?php if ($navigationUser): ?>
            <form action="/account/logout.php" method="post" class="nav-form">
                <?= csrf_field() ?>
                <button class="link-button" type="submit">Sign out</button>
            </form>
        <?php endif; ?>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Use dark theme" aria-pressed="false">
            <span aria-hidden="true">◐</span><span data-theme-label>Light</span>
        </button>
    </nav>
</header>
<main id="main-content" class="portal-main">
    <section class="portal-intro" aria-labelledby="page-title">
        <p class="eyebrow"><?= e($pageEyebrow) ?></p>
        <h1 id="page-title"><?= e($pageTitle) ?></h1>
        <?php if ($pageDescription !== ''): ?>
            <p class="lede"><?= e($pageDescription) ?></p>
        <?php endif; ?>
    </section>
    <?php foreach (consume_flashes() as $flashMessage): ?>
        <div class="alert alert-<?= e($flashMessage['type']) ?>" role="status">
            <?= e($flashMessage['message']) ?>
        </div>
    <?php endforeach; ?>

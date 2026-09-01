<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/site/bootstrap.php';

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
$pathSlug = preg_match('#^/jobs/([a-z0-9][a-z0-9-]{0,159})/?$#', $requestPath, $routeMatch) === 1
    ? $routeMatch[1]
    : null;
$slug = mb_substr(trim((string) ($pathSlug ?? $_GET['job'] ?? '')), 0, 160);
$job = null;
$relatedJobs = [];
$catalogueError = null;

try {
    if ($slug !== '') {
        $job = find_published_job_by_slug($slug);
    }
} catch (Throwable $exception) {
    $catalogueError = 'The local job catalogue is not initialized yet.';
}

if ($job === null) {
    http_response_code(404);
    taascor_page_start([
        'title' => 'Role not found',
        'description' => 'The requested TAASCOR role is not available.',
        'active' => 'jobs',
        'styles' => ['/assets/css/careers.css'],
        'robots' => 'noindex,nofollow',
    ]);
    ?>
    <main id="main-content" class="job-not-found">
        <section class="page-hero">
            <div class="shell narrow">
                <p class="eyebrow">Role unavailable</p>
                <h1>This role is not currently published.</h1>
                <p class="hero-lede"><?= taascor_escape($catalogueError ?? 'It may have closed, changed, or the link may be incomplete.') ?></p>
                <div class="hero-actions"><a class="button" href="/jobs/">View published roles</a></div>
            </div>
        </section>
    </main>
    <?php
    taascor_page_end();
    exit;
}

try {
    $relatedJobs = related_published_jobs(
        (int) $job['id'],
        (string) $job['function_area']
    );
} catch (Throwable $exception) {
    $relatedJobs = [];
}

$isDemo = (bool) $job['is_demo'];
$jsonLd = null;
if (!$isDemo) {
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'JobPosting',
        'title' => (string) $job['title'],
        'description' => (string) $job['description'],
        'datePosted' => substr((string) $job['published_at'], 0, 10),
        'employmentType' => strtoupper(str_replace(['-', ' '], '_', (string) $job['employment_type'])),
        'occupationalCategory' => (string) $job['function_area'],
        'hiringOrganization' => [
            '@type' => 'Organization',
            'name' => (string) $job['company'],
        ],
        'jobLocation' => [
            '@type' => 'Place',
            'address' => [
                '@type' => 'PostalAddress',
                'addressRegion' => (string) $job['location'],
                'addressCountry' => 'PH',
            ],
        ],
    ];
    if (!empty($job['closing_date'])) {
        $closingMoment = new DateTimeImmutable(
            (string) $job['closing_date'] . ' 23:59:59',
            new DateTimeZone((string) config_value('timezone', 'Asia/Manila'))
        );
        $jsonLd['validThrough'] = $closingMoment->format(DateTimeInterface::ATOM);
    }
    $baseUrl = rtrim((string) config_value('url', ''), '/');
    if (app_url_is_origin($baseUrl, is_production())) {
        $jsonLd['url'] = $baseUrl . '/jobs/' . rawurlencode((string) $job['slug']) . '/';
    }
}

taascor_page_start([
    'title' => (string) $job['title'],
    'description' => (string) $job['summary'],
    'active' => 'jobs',
    'styles' => ['/assets/css/careers.css'],
    'robots' => $isDemo ? 'noindex,nofollow' : 'index,follow',
    'canonical_path' => '/jobs/' . rawurlencode((string) $job['slug']) . '/',
    'json_ld' => $jsonLd,
]);
?>
<main id="main-content">
    <section class="job-detail-hero">
        <div class="shell">
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <a href="/">Home</a><span aria-hidden="true">/</span><a href="/jobs/">Jobs</a><span aria-hidden="true">/</span><span aria-current="page"><?= taascor_escape((string) $job['title']) ?></span>
            </nav>
            <?php if ($isDemo): ?>
                <div class="demo-banner" role="note"><strong>Demonstration role</strong><span>This synthetic vacancy validates the local workflow and is not open for real applications.</span></div>
            <?php endif; ?>
            <div class="job-title-grid">
                <div>
                    <p class="eyebrow"><?= taascor_escape((string) $job['employment_type']) ?></p>
                    <h1><?= taascor_escape((string) $job['title']) ?></h1>
                    <p class="hero-lede"><?= taascor_escape((string) $job['summary']) ?></p>
                </div>
                <dl class="job-facts">
                    <div><dt>Hiring organization</dt><dd><?= taascor_escape((string) $job['company']) ?></dd></div>
                    <div><dt>Location</dt><dd><?= taascor_escape((string) $job['location']) ?></dd></div>
                    <div><dt>Employment type</dt><dd><?= taascor_escape((string) $job['employment_type']) ?></dd></div>
                    <div><dt>Function</dt><dd><?= taascor_escape((string) $job['function_area']) ?></dd></div>
                    <div><dt>Shift</dt><dd><?= taascor_escape((string) $job['shift_pattern']) ?></dd></div>
                    <div><dt>Published</dt><dd><?= taascor_escape(substr((string) $job['published_at'], 0, 10)) ?></dd></div>
                    <?php if ($job['openings']): ?><div><dt>Openings</dt><dd><?= (int) $job['openings'] ?></dd></div><?php endif; ?>
                    <?php if ($job['closing_date']): ?><div><dt>Closing date</dt><dd><?= taascor_escape((string) $job['closing_date']) ?></dd></div><?php endif; ?>
                    <div><dt>Publication state</dt><dd>Published</dd></div>
                </dl>
            </div>
        </div>
    </section>

    <section class="job-detail-body">
        <div class="shell job-detail-grid">
            <article class="job-copy">
                <p class="section-kicker">Role overview</p>
                <h2>What this role is for</h2>
                <p><?= nl2br(taascor_escape((string) $job['description'])) ?></p>

                <h2>Essential requirements</h2>
                <p><?= nl2br(taascor_escape((string) $job['requirements'])) ?></p>

                <h2>Before you apply</h2>
                <ul class="role-checklist">
                    <li>Confirm the worksite and employment type fit your circumstances.</li>
                    <li>Use an email address and mobile number you can access during the recruitment process.</li>
                    <li>Share only the information requested at the current application stage.</li>
                    <li>Use the official application and portal paths for documents and status updates.</li>
                </ul>

                <div class="notice-panel">
                    <strong>Requirements are stage-specific.</strong>
                    <p>Government identifiers, references, background checks and medical information are not part of the initial application. If a later approved stage requires them, the applicant portal will explain the purpose and next action.</p>
                </div>

                <div class="notice-panel fraud-notice">
                    <strong>Recruitment safety.</strong>
                    <p>Treat requests for upfront payment, passwords, one-time passcodes or bank credentials as suspicious. Stop and verify the route through the published TAASCOR website before sharing anything.</p>
                    <a class="text-link" href="/legal/anti-fraud/">Review the recruitment safety guide</a>
                </div>
            </article>
            <aside class="apply-rail" aria-label="Apply for this role">
                <span class="apply-code">ROLE / <?= str_pad((string) $job['id'], 4, '0', STR_PAD_LEFT) ?></span>
                <h2>Carry this exact role into your application.</h2>
                <p>You will not be asked to choose the company or position again.</p>
                <a class="button" href="/apply/<?= rawurlencode((string) $job['slug']) ?>/">Start application</a>
                <a class="button button-outline" href="/account/login.php?next=<?= rawurlencode('/apply/' . (string) $job['slug'] . '/') ?>">Sign in to continue</a>
                <p class="rail-note">Application data is handled under the applicant privacy notice. Demonstration submissions remain local and synthetic.</p>
            </aside>
        </div>
    </section>

    <?php if ($relatedJobs !== []): ?>
        <section class="related-jobs" aria-labelledby="related-jobs-title">
            <div class="shell">
                <div class="jobs-heading">
                    <div><p class="section-kicker">Related opportunities</p><h2 id="related-jobs-title">Continue with current published roles.</h2></div>
                    <p>Matched by function, then recency</p>
                </div>
                <div class="related-job-grid">
                    <?php foreach ($relatedJobs as $relatedJob): ?>
                        <article class="related-job-card">
                            <div class="job-flags">
                                <span><?= taascor_escape((string) $relatedJob['employment_type']) ?></span>
                                <span><?= taascor_escape((string) $relatedJob['function_area']) ?></span>
                                <?php if ((bool) $relatedJob['is_demo']): ?><span class="demo-flag">Demo role</span><?php endif; ?>
                            </div>
                            <h3><a href="/jobs/<?= rawurlencode((string) $relatedJob['slug']) ?>/"><?= taascor_escape((string) $relatedJob['title']) ?></a></h3>
                            <p><?= taascor_escape((string) $relatedJob['summary']) ?></p>
                            <div class="related-job-meta"><span><?= taascor_escape((string) $relatedJob['location']) ?></span><a class="text-link" href="/jobs/<?= rawurlencode((string) $relatedJob['slug']) ?>/">Review role</a></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="job-next">
        <div class="shell safety-grid">
            <div><p class="section-kicker">Keep exploring</p><h2>Not the right role?</h2></div>
            <div><p>Return to the governed catalogue and adjust your search. Closed or unapproved roles do not remain active just to fill the page.</p><a class="text-link" href="/jobs/">View all published roles</a></div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

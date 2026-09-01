<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/site/bootstrap.php';

$keyword = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
$location = mb_substr(trim((string) ($_GET['location'] ?? '')), 0, 100);
$employmentType = mb_substr(trim((string) ($_GET['type'] ?? '')), 0, 80);
$functionArea = mb_substr(trim((string) ($_GET['function'] ?? '')), 0, 120);
$shiftPattern = mb_substr(trim((string) ($_GET['shift'] ?? '')), 0, 120);
$sort = (string) ($_GET['sort'] ?? 'recent');
if (!in_array($sort, ['recent', 'relevant'], true)) {
    $sort = 'recent';
}
$catalogueError = null;
$jobs = [];

try {
    $jobs = list_published_jobs();
} catch (Throwable $exception) {
    $catalogueError = 'The local job catalogue is not initialized yet.';
}

$locations = array_values(array_unique(array_filter(array_map(
    static fn (array $job): string => (string) ($job['location'] ?? ''),
    $jobs
))));
sort($locations, SORT_NATURAL | SORT_FLAG_CASE);

$employmentTypes = array_values(array_unique(array_filter(array_map(
    static fn (array $job): string => (string) ($job['employment_type'] ?? ''),
    $jobs
))));
sort($employmentTypes, SORT_NATURAL | SORT_FLAG_CASE);

$functionAreas = array_values(array_unique(array_filter(array_map(
    static fn (array $job): string => (string) ($job['function_area'] ?? ''),
    $jobs
))));
sort($functionAreas, SORT_NATURAL | SORT_FLAG_CASE);

$shiftPatterns = array_values(array_unique(array_filter(array_map(
    static fn (array $job): string => (string) ($job['shift_pattern'] ?? ''),
    $jobs
))));
sort($shiftPatterns, SORT_NATURAL | SORT_FLAG_CASE);

$filteredJobs = array_values(array_filter($jobs, static function (array $job) use ($keyword, $location, $employmentType, $functionArea, $shiftPattern): bool {
    if ($keyword !== '') {
        $haystack = mb_strtolower(implode(' ', [
            (string) ($job['title'] ?? ''),
            (string) ($job['company'] ?? ''),
            (string) ($job['location'] ?? ''),
            (string) ($job['function_area'] ?? ''),
            (string) ($job['shift_pattern'] ?? ''),
            (string) ($job['summary'] ?? ''),
            (string) ($job['requirements'] ?? ''),
        ]));
        if (!str_contains($haystack, mb_strtolower($keyword))) {
            return false;
        }
    }

    if ($location !== '' && (string) ($job['location'] ?? '') !== $location) {
        return false;
    }

    if ($employmentType !== '' && (string) ($job['employment_type'] ?? '') !== $employmentType) {
        return false;
    }

    if ($functionArea !== '' && (string) ($job['function_area'] ?? '') !== $functionArea) {
        return false;
    }

    if ($shiftPattern !== '' && (string) ($job['shift_pattern'] ?? '') !== $shiftPattern) {
        return false;
    }

    return true;
}));

if ($sort === 'relevant') {
    $needle = mb_strtolower($keyword);
    usort($filteredJobs, static function (array $left, array $right) use ($needle): int {
        $score = static function (array $job) use ($needle): int {
            if ($needle === '') {
                return 0;
            }
            $title = mb_strtolower((string) ($job['title'] ?? ''));
            $function = mb_strtolower((string) ($job['function_area'] ?? ''));
            $summary = mb_strtolower((string) ($job['summary'] ?? ''));
            return (str_contains($title, $needle) ? 4 : 0)
                + (str_contains($function, $needle) ? 2 : 0)
                + (str_contains($summary, $needle) ? 1 : 0);
        };
        return $score($right) <=> $score($left)
            ?: strcasecmp((string) $left['title'], (string) $right['title']);
    });
}

$hasOnlyDemoJobs = $jobs !== [] && count(array_filter(
    $jobs,
    static fn (array $job): bool => !(bool) ($job['is_demo'] ?? false)
)) === 0;

taascor_page_start([
    'title' => 'Find work',
    'description' => 'Explore TAASCOR job opportunities through a clear, mobile-ready application journey.',
    'active' => 'jobs',
    'styles' => ['/assets/css/careers.css'],
    'robots' => $hasOnlyDemoJobs ? 'noindex,nofollow' : 'index,follow',
    'canonical_path' => '/jobs/',
]);
?>
<main id="main-content">
    <section class="careers-hero">
        <div class="shell careers-hero-grid">
            <div>
                <p class="eyebrow">Find work</p>
                <h1>Opportunity is easier to trust when the route is clear.</h1>
                <p class="hero-lede">Search current roles, understand the work before applying, and carry the exact job context from first click through submission.</p>
            </div>
            <div class="career-signal" aria-hidden="true">
                <span class="signal-step">Discover</span>
                <span class="signal-step">Review</span>
                <span class="signal-step">Apply</span>
                <span class="signal-step">Track</span>
            </div>
        </div>
    </section>

    <?php if ($hasOnlyDemoJobs): ?>
        <div class="shell demo-banner" role="note">
            <strong>Local demonstration catalogue</strong>
            <span>These synthetic roles validate the experience and are not real vacancies. Approved jobs will replace them from the governed job source.</span>
        </div>
    <?php endif; ?>

    <section class="jobs-scene" aria-labelledby="jobs-title">
        <div class="shell">
            <div class="jobs-heading">
                <div>
                    <p class="section-kicker">Opportunity network</p>
                    <h2 id="jobs-title">Published roles</h2>
                </div>
                <p><strong><?= count($filteredJobs) ?></strong> <?= count($filteredJobs) === 1 ? 'role' : 'roles' ?> shown</p>
            </div>

            <form class="job-filters" method="get" action="/jobs/" role="search" aria-label="Filter jobs">
                <div class="field field-wide">
                    <label for="job-keyword">Keyword</label>
                    <input id="job-keyword" name="q" type="search" value="<?= taascor_escape($keyword) ?>" placeholder="Role, company or location" autocomplete="off">
                </div>
                <div class="field">
                    <label for="job-location">Location</label>
                    <select id="job-location" name="location">
                        <option value="">All locations</option>
                        <?php foreach ($locations as $option): ?>
                            <option value="<?= taascor_escape($option) ?>"<?= $location === $option ? ' selected' : '' ?>><?= taascor_escape($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="job-type">Employment type</label>
                    <select id="job-type" name="type">
                        <option value="">All types</option>
                        <?php foreach ($employmentTypes as $option): ?>
                            <option value="<?= taascor_escape($option) ?>"<?= $employmentType === $option ? ' selected' : '' ?>><?= taascor_escape($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="job-function">Function</label>
                    <select id="job-function" name="function">
                        <option value="">All functions</option>
                        <?php foreach ($functionAreas as $option): ?>
                            <option value="<?= taascor_escape($option) ?>"<?= $functionArea === $option ? ' selected' : '' ?>><?= taascor_escape($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="job-shift">Shift</label>
                    <select id="job-shift" name="shift">
                        <option value="">All shifts</option>
                        <?php foreach ($shiftPatterns as $option): ?>
                            <option value="<?= taascor_escape($option) ?>"<?= $shiftPattern === $option ? ' selected' : '' ?>><?= taascor_escape($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="job-sort">Sort</label>
                    <select id="job-sort" name="sort">
                        <option value="recent"<?= $sort === 'recent' ? ' selected' : '' ?>>Most recent</option>
                        <option value="relevant"<?= $sort === 'relevant' ? ' selected' : '' ?>>Most relevant</option>
                    </select>
                </div>
                <button class="button" type="submit">Apply filters</button>
                <?php if ($keyword !== '' || $location !== '' || $employmentType !== '' || $functionArea !== '' || $shiftPattern !== '' || $sort !== 'recent'): ?>
                    <a class="filter-reset" href="/jobs/">Clear filters</a>
                <?php endif; ?>
            </form>

            <?php if ($catalogueError !== null): ?>
                <div class="jobs-empty" role="status">
                    <span class="empty-code">CATALOGUE / SETUP</span>
                    <h3>Job catalogue setup is pending.</h3>
                    <p><?= taascor_escape($catalogueError) ?> Run the approved local setup command, then reload this page.</p>
                </div>
            <?php elseif ($filteredJobs === []): ?>
                <div class="jobs-empty" role="status">
                    <span class="empty-code">NO MATCH / CURRENT FILTERS</span>
                    <h3>No published role matches this search.</h3>
                    <p>Clear one or more filters. Missing results are not treated as zero demand; only owner-approved published jobs appear here.</p>
                    <a class="button button-outline" href="/jobs/">Show all published roles</a>
                </div>
            <?php else: ?>
                <div class="job-list" aria-live="polite">
                    <?php foreach ($filteredJobs as $job): ?>
                        <article class="job-card">
                            <div class="job-card-main">
                                <div class="job-flags">
                                    <span><?= taascor_escape((string) $job['employment_type']) ?></span>
                                    <span><?= taascor_escape((string) $job['function_area']) ?></span>
                                    <span><?= taascor_escape((string) $job['shift_pattern']) ?></span>
                                    <span>Published</span>
                                    <?php if ((bool) $job['is_demo']): ?><span class="demo-flag">Demo role</span><?php endif; ?>
                                </div>
                                <h3><a href="/jobs/<?= rawurlencode((string) $job['slug']) ?>/"><?= taascor_escape((string) $job['title']) ?></a></h3>
                                <p class="job-company"><?= taascor_escape((string) $job['company']) ?></p>
                                <p class="job-summary"><?= taascor_escape((string) $job['summary']) ?></p>
                                <p class="job-requirement"><strong>Essential requirement:</strong> <?= taascor_escape(mb_substr(trim((string) $job['requirements']), 0, 180)) ?><?= mb_strlen(trim((string) $job['requirements'])) > 180 ? '…' : '' ?></p>
                            </div>
                            <div class="job-card-side">
                                <span class="job-location"><?= taascor_escape((string) $job['location']) ?></span>
                                <span class="job-closing">Published <?= taascor_escape(substr((string) $job['published_at'], 0, 10)) ?></span>
                                <?php if ($job['closing_date']): ?><span class="job-closing">Closes <?= taascor_escape((string) $job['closing_date']) ?></span><?php endif; ?>
                                <a class="text-link" href="/jobs/<?= rawurlencode((string) $job['slug']) ?>/">Review the role</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="candidate-path" aria-labelledby="candidate-path-title">
        <div class="shell split">
            <div class="sticky-intro">
                <p class="section-kicker">Candidate path</p>
                <h2 id="candidate-path-title">A shorter first step. More detail only when needed.</h2>
                <p class="section-copy">The application begins with job context and contact information. Verification, references, government identifiers and medical requirements belong to later approved stages—not the first click.</p>
            </div>
            <ol class="process-list">
                <li><h3>Review the actual role</h3><p>Worksite, employment type, role summary and current publication state stay attached to the application.</p></li>
                <li><h3>Create a protected account</h3><p>Your account keeps drafts, submitted applications and candidate-visible status history in one place.</p></li>
                <li><h3>Share only what this stage needs</h3><p>Initial application data is intentionally limited. Later requirements appear as owned tasks with a clear purpose.</p></li>
                <li><h3>Track the next action</h3><p>Status and applicant-visible notes make pending steps explicit without exposing internal or other candidates’ data.</p></li>
            </ol>
        </div>
    </section>

    <section class="career-safety">
        <div class="shell safety-grid">
            <div>
                <p class="section-kicker">Recruitment safety</p>
                <h2>Pause when the channel, fee or request does not look right.</h2>
            </div>
            <div>
                <p>Use the published job and portal paths. Do not send government identifiers, medical records or payments through an unverified message.</p>
                <a class="text-link" href="/legal/anti-fraud/">Read the recruitment safety guide</a>
            </div>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

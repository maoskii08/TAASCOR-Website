<?php

declare(strict_types=1);

$executedFile = realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
if ($executedFile !== false && $executedFile === __FILE__) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex,nofollow');
    echo "Not found.\n";
    exit;
}

require_once dirname(__DIR__) . '/site/bootstrap.php';

/** @return array<string, array<string, mixed>> */
function taascor_industry_details(): array
{
    return [
        'production-throughput' => [
            'title' => 'Production and Throughput Context',
            'description' => 'Frame production work, flow dependencies, schedules, site interfaces, acceptance, and evidence before asking whether a workforce model is suitable.',
            'eyebrow' => 'Industry context / Production and throughput',
            'hero_before' => 'Model the work around the flow, not ',
            'hero_emphasis' => 'the sector label.',
            'lede' => 'Production environments can only be assessed from the actual work, flow, schedule, site, readiness, and decision boundary. This page is a discovery lens, not a statement of TAASCOR capacity, coverage, or availability.',
            'signal_core' => ['Flow', 'context'],
            'signal_nodes' => ['Tasks', 'Demand', 'Handoffs', 'Accept'],
            'scope_title' => 'Map the operating flow before discussing workforce fit.',
            'scope_copy' => 'The scope starts with task families and the way work moves through a line, cell, process, or other production arrangement. It then identifies demand inputs, upstream and downstream dependencies, worker-readiness conditions, supervision, quality acceptance, and change authority.',
            'boundary' => 'This context page does not establish a current TAASCOR production service, worker pool, location, deployment window, site authorization, operating result, safety result, compliance position, or commercial offer.',
            'context' => [
                ['Work content and flow', 'Describe tasks, outputs, exclusions, sequence, process dependencies, expected work conditions, and the point at which completed work is accepted.'],
                ['Demand and schedule', 'Identify the authoritative volume or workload source, shift pattern, peaks, ramp timing, planned interruptions, and the owner permitted to change the requirement.'],
                ['Operating handoffs', 'Map material, equipment, maintenance, quality, supervision, escalation, and downstream handoffs that can constrain the workforce plan.'],
                ['Worker-readiness interface', 'Define objective role requirements, orientation, training, access, equipment, day-to-day direction, and the evidence needed before a person begins assigned work.'],
            ],
            'constraints' => [
                ['Variability and ramp', 'Separate a planning estimate from authorized demand, then document how peaks, downtime, mix changes, and ramp decisions would be handled.'],
                ['Quality and rework', 'Name who sets acceptance criteria, records completion, decides rework, and resolves a disputed result.'],
                ['Site and safe-work interfaces', 'Identify work-area rules, access conditions, equipment boundaries, incident paths, and the accountable owner of each control.'],
                ['Mobilization dependencies', 'Make prerequisites such as approved roles, schedules, facilities, equipment, orientation, and system access visible rather than assuming readiness.'],
                ['Authority and escalation', 'Define who directs daily work, changes demand, approves exceptions, communicates with workers, and accepts the operating outcome.'],
            ],
            'solutions' => [
                ['/solutions/workforce-staffing/', 'Workforce staffing planning', 'Frame roles, shifts, worksite conditions, responsibilities, and mobilization evidence.'],
                ['/solutions/recruitment-sourcing/', 'Recruitment and sourcing planning', 'Translate an authorized role into objective criteria, staged screening, decisions, and candidate communication.'],
                ['/solutions/facility-support/', 'Facility support planning', 'Assess site tasks, work windows, equipment interfaces, controls, and completion evidence where relevant.'],
                ['/solutions/hris-enabled-operations/', 'HRIS-enabled operations planning', 'Map approved workforce sources, role access, exceptions, handoffs, and audit evidence.'],
            ],
            'evidence' => [
                ['Authorized work definition', 'Current task and role descriptions, exclusions, work conditions, acceptance criteria, owners, and effective dates.'],
                ['Demand and schedule sources', 'Named authoritative inputs, shifts, peaks, ramp assumptions, change rights, revision history, and review cadence.'],
                ['Site-readiness record', 'Applicable access, facilities, equipment, orientation, work-area controls, dependencies, owners, and open conditions.'],
                ['Responsibility and acceptance map', 'Approved direction, supervision, quality, issue, change, worker-communication, evidence, and final acceptance boundaries.'],
            ],
            'planner_copy' => 'Bring the tasks, production arrangement, worksite, demand pattern, shifts, target timing, role requirements, dependencies, and unresolved operating constraints. The resulting brief remains subject to evidence and accountable-owner review.',
        ],
        'distribution-fulfilment' => [
            'title' => 'Distribution and Fulfilment Context',
            'description' => 'Frame distribution demand, work zones, schedules, material-handling interfaces, access, acceptance, and evidence before asking whether a workforce model is suitable.',
            'eyebrow' => 'Industry context / Distribution and fulfilment',
            'hero_before' => 'Plan the workforce around ',
            'hero_emphasis' => 'the demand curve.',
            'lede' => 'A distribution or fulfilment label does not reveal the order pattern, work zones, cut-offs, shift waves, equipment interfaces, access rules, or escalation paths. Those engagement-specific facts must be assessed before service fit can be discussed.',
            'signal_core' => ['Demand', 'movement'],
            'signal_nodes' => ['Volume', 'Zones', 'Waves', 'Handoff'],
            'scope_title' => 'Translate volume and movement into an assessable brief.',
            'scope_copy' => 'The scope starts with the workload source and how work moves through receiving, storage, picking, packing, staging, dispatch, returns, or other defined zones. The actual process, terminology, technology, and responsibility split must come from the site owner.',
            'boundary' => 'This context page does not establish a current TAASCOR distribution service, worker pool, location, site, shift coverage, equipment authorization, throughput result, compliance position, or service availability.',
            'context' => [
                ['Volume and service pattern', 'Describe the authorized workload source, forecast horizon, daily and seasonal movement, cut-offs, service windows, and who can revise demand.'],
                ['Work zones and tasks', 'Define areas, task families, movement between zones, exclusions, congestion points, acceptance, and any zone-specific readiness requirement.'],
                ['Shift and access design', 'Map shift waves, breaks, handovers, site entry, transport dependencies, facilities, attendance source, and communication paths.'],
                ['Equipment and system interfaces', 'Identify where equipment, devices, warehouse systems, credentials, training, maintenance, or exception queues affect the work boundary.'],
            ],
            'constraints' => [
                ['Peak volatility', 'Separate forecast, committed volume, and observed workload, then define the authority and lead time for staffing changes.'],
                ['Cut-offs and interdependencies', 'Document carrier, inventory, system, equipment, dock, or upstream dependencies without assigning responsibility by assumption.'],
                ['Authorization and access', 'Identify site access, zone permissions, system roles, equipment authorization, orientation, and evidence required for each role.'],
                ['Exceptions and recovery', 'Define how shortages, delays, damaged items, system interruptions, attendance gaps, and disputed completion would be routed.'],
                ['Acceptance and evidence', 'Name the source, denominator, review owner, exception treatment, and permitted wording behind any future performance measure.'],
            ],
            'solutions' => [
                ['/solutions/workforce-staffing/', 'Workforce staffing planning', 'Frame work zones, roles, shift waves, site conditions, responsibilities, and ramp evidence.'],
                ['/solutions/recruitment-sourcing/', 'Recruitment and sourcing planning', 'Define role criteria, staged screening, candidate communication, and decision ownership.'],
                ['/solutions/hris-enabled-operations/', 'HRIS-enabled operations planning', 'Map approved schedule, attendance, assignment, exception, access, and evidence workflows.'],
                ['/solutions/facility-support/', 'Facility support planning', 'Assess site-support tasks and work-area interfaces only where the approved scope makes them relevant.'],
            ],
            'evidence' => [
                ['Authorized process and zone map', 'Current areas, tasks, exclusions, handoffs, service windows, dependencies, owners, and acceptance points.'],
                ['Demand and shift record', 'Source lineage, forecast and authorization states, peaks, cut-offs, shifts, revision rights, assumptions, and validity period.'],
                ['Readiness and access matrix', 'Role-specific skills, orientation, access, equipment or system permissions, facilities, transport dependencies, and owner sign-off.'],
                ['Exception and acceptance controls', 'Approved event categories, decision rights, escalation, recovery, evidence, measures, denominator, and change process.'],
            ],
            'planner_copy' => 'Bring the work zones, task families, workload pattern, shift waves, cut-offs, target timing, access needs, equipment and system interfaces, and the constraints the workforce plan must resolve.',
        ],
        'office-service-support' => [
            'title' => 'Office and Service-Support Context',
            'description' => 'Frame office and service-support outcomes, workload, skills, channels, schedules, access, confidentiality, decisions, and evidence before asking whether a workforce model is suitable.',
            'eyebrow' => 'Industry context / Office and service support',
            'hero_before' => 'Define the service outcome before ',
            'hero_emphasis' => 'staffing the queue.',
            'lede' => 'Office and service-support work varies by outcome, audience, channel, knowledge, schedule, access, and decision authority. This lens structures those questions without implying a current service, client relationship, or operating result.',
            'signal_core' => ['Service', 'context'],
            'signal_nodes' => ['Outcome', 'Channel', 'Access', 'Escalate'],
            'scope_title' => 'Make service ownership and information boundaries explicit.',
            'scope_copy' => 'The scope begins with the result each role is expected to produce and the people, cases, records, channels, or systems involved. It then defines workload, hours, skills, knowledge, permissions, supervision, escalation, acceptance, and worker-support responsibilities.',
            'boundary' => 'This context page does not establish a current TAASCOR office or service-support offering, named client, location, workforce capacity, response time, data access, service level, outcome, compliance position, or availability.',
            'context' => [
                ['Service outcome and audience', 'Define the requested outcome, service recipient, request types, exclusions, decision boundary, and evidence used to accept completed work.'],
                ['Workload and channels', 'Identify source volumes, queues, seasonality, hours, channels, response expectations, prioritization, and the owner permitted to change demand.'],
                ['Skills and knowledge', 'Describe objective role requirements, language or domain needs, knowledge sources, training ownership, update controls, and quality review.'],
                ['Data and escalation', 'Map records, systems, least-privilege roles, confidentiality boundaries, corrections, complex cases, approval paths, and incident handoffs.'],
            ],
            'constraints' => [
                ['Demand versus service promise', 'Keep workload assumptions separate from approved response or resolution commitments and identify the denominator for any measure.'],
                ['Knowledge and change control', 'Name the authoritative source, publisher, effective date, acknowledgement process, and path for conflicting instructions.'],
                ['Access and confidentiality', 'Collect only justified information and document system, record, recipient, retention, correction, and incident boundaries.'],
                ['Human decision authority', 'Separate information gathering or recommendation from decisions affecting identity, employment, access, money, or another protected interest.'],
                ['Continuity and escalation', 'Define priority, coverage dependencies, backlog treatment, unavailable systems, urgent matters, and the owner of each exception path.'],
            ],
            'solutions' => [
                ['/solutions/recruitment-sourcing/', 'Recruitment and sourcing planning', 'Turn an authorized service role into objective criteria, candidate communication, and human decision steps.'],
                ['/solutions/hr-administration/', 'HR administration planning', 'Map worker records, onboarding, communication, case ownership, policy authority, and escalation.'],
                ['/solutions/hris-enabled-operations/', 'HRIS-enabled operations planning', 'Define data sources, access, approvals, exceptions, integrations, privacy, and operating ownership.'],
                ['/solutions/workforce-staffing/', 'Workforce staffing planning', 'Frame roles, schedules, supervision, work conditions, handoffs, and mobilization dependencies.'],
            ],
            'evidence' => [
                ['Authorized service definition', 'Current outcomes, request types, exclusions, audience, channels, hours, decision boundaries, owners, and review date.'],
                ['Workload and schedule sources', 'Source lineage, queue definitions, volumes, peaks, service assumptions, shifts, change rights, and exception treatment.'],
                ['Knowledge and role-access design', 'Approved role criteria, knowledge sources, training, permissions, privacy purposes, recipients, corrections, and retention.'],
                ['Acceptance and escalation model', 'Named review and decision owners, approved measures, denominator, quality evidence, urgent paths, incidents, and recovery.'],
            ],
            'planner_copy' => 'Bring the service outcomes, request types, audiences, workload sources, channels, schedules, role requirements, systems, information boundaries, and exceptions the proposed operating model needs to address.',
        ],
        'facilities-site-support' => [
            'title' => 'Facilities and Site-Support Context',
            'description' => 'Frame site-support areas, tasks, work windows, access, equipment, safe-work interfaces, incidents, acceptance, and evidence before asking whether a workforce model is suitable.',
            'eyebrow' => 'Industry context / Facilities and site support',
            'hero_before' => 'Treat the site as part of ',
            'hero_emphasis' => 'the service boundary.',
            'lede' => 'Facilities and site-support work depends on the actual areas, tasks, service windows, equipment and material boundaries, access, site rules, incident paths, and acceptance evidence. A category label cannot confirm service fit.',
            'signal_core' => ['Site', 'boundary'],
            'signal_nodes' => ['Areas', 'Windows', 'Controls', 'Evidence'],
            'scope_title' => 'Start with the physical environment and accountable owners.',
            'scope_copy' => 'The scope identifies work areas, tasks and exclusions, expected condition or output, service windows, site dependencies, access, equipment and materials, workforce assumptions, safe-work interfaces, incident authority, inspection, and acceptance.',
            'boundary' => 'This context page does not establish a current TAASCOR facility-support service, site coverage, crew, equipment, certification, response time, inspection outcome, regulatory compliance, safety result, or availability.',
            'context' => [
                ['Areas, tasks, and exclusions', 'Describe each work area, intended task and condition, prohibited work, frequency or trigger, access window, dependencies, and acceptance point.'],
                ['Workforce and supervision', 'Define objective role requirements, schedule, orientation, day-to-day direction, communications, support, replacement, and escalation boundaries.'],
                ['Equipment and materials', 'Identify ownership, provision, suitability decision, storage, issue, maintenance, consumables, utilities, records, and exception handling.'],
                ['Site-control interfaces', 'Map access, permits, site instructions, hazard information, stop-work authority, incidents, inspections, and coordination with other parties.'],
            ],
            'constraints' => [
                ['Access and work windows', 'Record security, permits, escorts, restricted areas, operating interruptions, adjacent work, and who authorizes a change.'],
                ['Site and task variability', 'Assess each location and task from current evidence rather than carrying assumptions across sites, areas, shifts, or service periods.'],
                ['Equipment responsibility', 'Name who selects, provides, inspects, maintains, replaces, stores, and authorizes equipment or materials for the defined work.'],
                ['Incident and exception paths', 'Define immediate authority, stop-work conditions, communication, evidence, investigation handoff, recovery, and approval to resume.'],
                ['Inspection and acceptance', 'Identify the approved condition, method, sample, frequency, evidence, reviewer, denominator, dispute path, and change authority.'],
            ],
            'solutions' => [
                ['/solutions/facility-support/', 'Facility support planning', 'Frame site tasks, schedules, equipment, safe-work interfaces, supervision, acceptance, and readiness evidence.'],
                ['/solutions/workforce-staffing/', 'Workforce staffing planning', 'Define roles, schedules, site conditions, responsibility boundaries, and mobilization dependencies.'],
                ['/solutions/recruitment-sourcing/', 'Recruitment and sourcing planning', 'Translate approved role requirements into objective sourcing and staged screening questions.'],
                ['/solutions/hr-administration/', 'HR administration planning', 'Map onboarding, worker communication, authorized records, case ownership, and escalation.'],
            ],
            'evidence' => [
                ['Current site and work register', 'Named location and areas, tasks, exclusions, work windows, dependencies, access, current owners, and review date.'],
                ['Readiness and responsibility matrix', 'Approved workforce, orientation, supervision, equipment, materials, facilities, access, permits, and open prerequisites.'],
                ['Site-control and incident interfaces', 'Applicable owner-approved instructions, information, stop-work and incident authority, reporting, handoff, evidence, and recovery.'],
                ['Inspection and acceptance method', 'Approved condition, measurement or review method, denominator, evidence, reviewer, issue path, change control, and validity period.'],
            ],
            'planner_copy' => 'Bring the work areas, tasks and exclusions, service windows, expected condition, workforce assumptions, access, equipment and materials, site-control interfaces, dependencies, and acceptance needs for assessment.',
        ],
    ];
}

function taascor_render_industry_detail(string $slug): void
{
    $catalogue = taascor_industry_details();
    if (!isset($catalogue[$slug])) {
        http_response_code(404);
        require dirname(__DIR__) . '/site/404.php';
        exit;
    }

    $industry = $catalogue[$slug];
    $sectionPrefix = 'industry-' . $slug;

    taascor_page_start([
        'title' => (string) $industry['title'],
        'description' => (string) $industry['description'],
        'active' => 'industries',
        'body_class' => 'industry-detail-page',
        'robots' => 'index,follow',
    ]);
    ?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <nav class="breadcrumbs" aria-label="Breadcrumb">
                    <a href="<?= taascor_escape(taascor_url('/')) ?>">Home</a><span aria-hidden="true">/</span>
                    <a href="<?= taascor_escape(taascor_url('/industries/')) ?>">Industries</a><span aria-hidden="true">/</span>
                    <span aria-current="page"><?= taascor_escape((string) $industry['title']) ?></span>
                </nav>
                <p class="eyebrow"><?= taascor_escape((string) $industry['eyebrow']) ?></p>
                <h1 id="<?= taascor_escape($sectionPrefix) ?>-title"><?= taascor_escape((string) $industry['hero_before']) ?><em><?= taascor_escape((string) $industry['hero_emphasis']) ?></em></h1>
                <p class="hero-lede"><?= taascor_escape((string) $industry['lede']) ?></p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Shape a workforce brief</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/industries/')) ?>">View all industry contexts</a>
                </div>
                <p class="hero-note">Discovery context only. Exact scope, capability, geography, capacity, controls, availability, and terms require current evidence and accountable-owner approval.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core"><?php foreach ($industry['signal_core'] as $index => $label): ?><?= $index > 0 ? '<br>' : '' ?><?= taascor_escape((string) $label) ?><?php endforeach; ?></div>
                <?php foreach ($industry['signal_nodes'] as $index => $label): ?>
                    <span class="network-node node-<?= taascor_escape(chr(97 + $index)) ?>"><?= taascor_escape((string) $label) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-scope-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Scope of the lens / 01</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-scope-title"><?= taascor_escape((string) $industry['scope_title']) ?></h2>
                <p class="section-copy"><?= taascor_escape((string) $industry['scope_copy']) ?></p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Fit and evidence to verify', 'review') ?>
                <h3>What this page does not establish</h3>
                <p><?= taascor_escape((string) $industry['boundary']) ?></p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-context-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Operating context / 02</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-context-title">Turn the environment into specific questions.</h2>
                <p class="section-copy">The details below are inputs for discovery. They must be supplied or confirmed by the owner of the work and tested against the proposed responsibility boundary.</p>
            </div>
            <ol class="industry-context-grid">
                <?php foreach ($industry['context'] as $index => [$title, $copy]): ?>
                    <li>
                        <span aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <h3><?= taascor_escape((string) $title) ?></h3>
                        <p><?= taascor_escape((string) $copy) ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="scene" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-constraints-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Constraints to resolve / 03</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-constraints-title">Keep the limiting conditions in the brief.</h2>
                <p class="section-copy">Constraints should stay visible as owned assumptions, dependencies, exclusions, or open decisions. They should not be converted into implied readiness.</p>
            </div>
            <ol class="process-list">
                <?php foreach ($industry['constraints'] as [$title, $copy]): ?>
                    <li><h3><?= taascor_escape((string) $title) ?></h3><p><?= taascor_escape((string) $copy) ?></p></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-solutions-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Relevant planning lenses / 04</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-solutions-title">Connect context to the right scope questions.</h2>
                <p class="section-copy">These links organize the next discussion. Relevance does not establish that a service, feature, worker pool, location, control, or delivery model is currently available.</p>
            </div>
            <div class="industry-solution-grid">
                <?php foreach ($industry['solutions'] as $index => [$href, $title, $copy]): ?>
                    <article class="industry-solution-card">
                        <span class="module-index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <h3><?= taascor_escape((string) $title) ?></h3>
                        <p><?= taascor_escape((string) $copy) ?></p>
                        <a class="text-link" href="<?= taascor_escape(taascor_url((string) $href)) ?>">Review this solution lens<span class="sr-only">: <?= taascor_escape((string) $title) ?></span></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-evidence-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Evidence required / 05</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-evidence-title">Move from category to current, owned records.</h2>
                <p class="section-copy">Before this context can support a proposal or public claim, the applicable evidence needs a source, owner, effective period, review date, approved wording, and correction path.</p>
            </div>
            <div class="module-grid module-grid-two">
                <?php foreach ($industry['evidence'] as $index => [$title, $copy]): ?>
                    <article class="module-card">
                        <span class="module-index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <h3><?= taascor_escape((string) $title) ?></h3>
                        <p><?= taascor_escape((string) $copy) ?></p>
                        <?= taascor_status_tag('Owner approval required', 'review') ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene scene-gold" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-action-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Start with operating context</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-action-title">Build a reviewable workforce brief.</h2>
                <p class="section-copy"><?= taascor_escape((string) $industry['planner_copy']) ?></p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the proof standard</a>
            </div>
        </div>
    </section>
</main>
<?php
    taascor_page_end();
}

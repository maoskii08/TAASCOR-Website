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
function taascor_solution_details(): array
{
    return [
        'workforce-staffing' => [
            'title' => 'Workforce Staffing Planning',
            'description' => 'Frame role demand, worksite conditions, schedules, responsibility boundaries, and mobilization evidence before a workforce-staffing scope is proposed.',
            'eyebrow' => 'Solution lens / Workforce staffing',
            'hero_before' => 'Shape deployment demand before it becomes ',
            'hero_emphasis' => 'a promise.',
            'hero_after' => '',
            'lede' => 'This planning lens turns roles, work conditions, scale, shifts, timing, and operating constraints into questions that accountable owners can assess. It does not assert current capacity, coverage, or service availability.',
            'signal_core' => ['Demand', 'boundary'],
            'signal_nodes' => ['Roles', 'Worksite', 'Schedule', 'Readiness'],
            'intent_title' => 'Define the work and the conditions around it.',
            'intent_copy' => 'The intended output is a reviewable staffing brief: what work is needed, where and when it occurs, what readiness conditions apply, and which decisions must be made before any proposal or mobilization plan is authorized.',
            'intent_boundary' => 'No worker availability, deployment date, location coverage, contracting model, legal status, or operating outcome is promised by this page. Those points require current evidence and engagement-specific approval.',
            'taascor_title' => 'Workforce formation questions',
            'taascor_copy' => 'The proposed TAASCOR-side scope may examine sourcing routes, role readiness, employment and supervision boundaries, mobilization dependencies, workforce-support handoffs, and completion evidence—only where the approved service catalogue supports them.',
            'client_title' => 'Work design and site-readiness questions',
            'client_copy' => 'The proposed employer or worksite scope may define tasks, expected outcomes, demand authority, safe-work conditions, access, equipment, training, day-to-day direction, service acceptance, and the process for changing the requirement.',
            'lifecycle' => [
                ['Capture the operating demand', 'Record roles, estimated headcount, site context, shifts, timing, task boundaries, constraints, and the source authorized to change demand.'],
                ['Test feasibility and evidence', 'Verify which service, capacity, location, employment, operational, and legal facts are current enough for the specific enquiry.'],
                ['Propose the responsibility split', 'Document who sources, employs, supervises, equips, trains, approves, accepts, escalates, and retains evidence for each activity.'],
                ['Review the controlled proposal', 'Separate assumptions, exclusions, dependencies, commercial terms, measures, and unresolved gates before any commitment is represented.'],
                ['Mobilize only after authorization', 'Proceed only when the applicable legal, commercial, privacy, site, safety, data, and operational owners have approved their parts.'],
            ],
            'evidence' => [
                ['Approved service boundary', 'Current service catalogue, responsibility matrix, exclusions, review date, and exact wording approved for this engagement.'],
                ['Authority and employment model', 'Current legal and compliance evidence, approved contracting structure, worker relationship, and accountable owner review.'],
                ['Capacity and location readiness', 'Named capacity owner, site assessment, coverage decision, ramp assumptions, dependencies, and validity period.'],
                ['Mobilization and acceptance controls', 'Approved readiness checklist, escalation path, acceptance evidence, change authority, and review cadence.'],
            ],
            'planner_copy' => 'Bring the roles, worksite context, estimated scale, shifts, desired timing, and the constraint the workforce plan needs to resolve. The brief remains an enquiry until accountable owners approve a proposal.',
        ],
        'recruitment-sourcing' => [
            'title' => 'Recruitment and Sourcing Planning',
            'description' => 'Define role criteria, sourcing boundaries, staged screening, candidate communication, privacy controls, and hiring evidence before a recruitment scope is proposed.',
            'eyebrow' => 'Solution lens / Recruitment and sourcing',
            'hero_before' => 'Build a candidate journey around ',
            'hero_emphasis' => 'the actual role.',
            'hero_after' => '',
            'lede' => 'This planning lens starts with an authorized requisition and objective role context. It maps how sourcing, screening, decisions, candidate communication, and evidence could work without implying a live pipeline or universal checks.',
            'signal_core' => ['Role', 'decision'],
            'signal_nodes' => ['Profile', 'Source', 'Screen', 'Handoff'],
            'intent_title' => 'Make the requisition, criteria, and decision path explicit.',
            'intent_copy' => 'The intended output is a recruitment brief that preserves the exact role, worksite, requirements, timing, decision owners, candidate messages, and stage-specific information needed from discovery through handoff.',
            'intent_boundary' => 'No vacancy, sourcing capacity, time-to-fill, candidate volume, screening result, background check, or hiring outcome is promised here. Regulated or sensitive checks belong only at an approved stage with a documented purpose and process.',
            'taascor_title' => 'Proposed recruitment-workflow questions',
            'taascor_copy' => 'The proposed TAASCOR-side scope may cover role intake, channel selection, candidate communication, objective screening, evidence capture, status handoffs, and exception escalation where the approved recruitment service and privacy design allow it.',
            'client_title' => 'Proposed hiring-owner questions',
            'client_copy' => 'The proposed hiring-owner scope may confirm the requisition, role outcomes, requirements, worksite conditions, interview and decision authority, response expectations, offer ownership, and any client-permission boundary for public job content.',
            'lifecycle' => [
                ['Authorize the role', 'Confirm the requisition owner, title, worksite, employment context, requirements, openings, dates, publication permission, and closure rule.'],
                ['Design the sourcing path', 'Select appropriate channels, audience, messages, anti-fraud controls, accessibility needs, and the owner accountable for each source.'],
                ['Define staged screening', 'Use objective role criteria and collect only the information justified at the current stage; keep regulated checks behind their own approval gate.'],
                ['Record human decisions', 'Identify who reviews evidence, decides progression, documents reasons, communicates status, and resolves exceptions.'],
                ['Handoff with context', 'Carry the approved role, decision, evidence boundary, candidate notice, and next owner into offer, onboarding, or closure.'],
            ],
            'evidence' => [
                ['Authorized requisition', 'Current owner, openings, worksite, dates, employment context, publication state, and automatic closure behavior.'],
                ['Recruitment procedure', 'Approved sourcing and screening SOP, criteria, stage map, human decision rights, exceptions, and quality review.'],
                ['Candidate privacy and safety', 'Approved notices, data fields, purposes, recipient map, retention, rights channel, official recruitment channels, and fee or anti-fraud wording.'],
                ['Communication and handoff controls', 'Approved templates, status ownership, service expectations, escalation route, audit evidence, and downstream acceptance.'],
            ],
            'planner_copy' => 'Bring the role family, worksite, objective requirements, estimated openings, desired timing, and the decisions the hiring process must support. TAASCOR can then assess the proposed recruitment boundary.',
        ],
        'payroll-coordination' => [
            'title' => 'Payroll Coordination Planning',
            'description' => 'Map time inputs, validation, exceptions, payroll-basis preparation, human approvals, release dependencies, and evidence before a payroll-coordination scope is proposed.',
            'eyebrow' => 'Solution lens / Payroll coordination',
            'hero_before' => 'Make every payroll handoff ',
            'hero_emphasis' => 'reviewable.',
            'hero_after' => '',
            'lede' => 'This planning lens separates source records, validation, exception handling, payroll-basis preparation, approval, posting, locking, distribution, and statutory responsibilities. It does not represent a current payroll or remittance control as proven.',
            'signal_core' => ['Source', 'approval'],
            'signal_nodes' => ['Time', 'Validate', 'Basis', 'Release'],
            'intent_title' => 'Trace the path from source record to authorized outcome.',
            'intent_copy' => 'The intended output is a responsibility and control map: which attendance or pay inputs are authoritative, how gaps are resolved, who can approve money-impacting decisions, and which evidence must exist before a downstream release.',
            'intent_boundary' => 'No calculation accuracy, payroll processing, payslip release, statutory filing, remittance, deadline, integration, or service level is claimed here. Exact scope depends on approved systems, calendars, roles, rules, and engagement documentation.',
            'taascor_title' => 'Proposed coordination questions',
            'taascor_copy' => 'The proposed TAASCOR-side scope may map receipt, reconciliation, validation, exception preparation, payroll-basis evidence, status communication, and controlled handoffs where product, payroll, finance, privacy, and service owners approve them.',
            'client_title' => 'Proposed source-and-approval questions',
            'client_copy' => 'The proposed employer or authorized-owner scope may define time sources, pay rules, cut-offs, changes, approval authority, funding or posting dependencies, statutory ownership, acceptance, and the process for correcting an authorized record.',
            'lifecycle' => [
                ['Register authoritative inputs', 'Identify the worker, assignment, schedule, time, rate, policy, and change sources permitted for the engagement and period.'],
                ['Validate receipt and completeness', 'Check expected inputs, timing, format, lineage, duplicates, gaps, and conflicts without treating missing records as measured zero.'],
                ['Resolve exceptions with authority', 'Route unsupported or conflicting items to named owners and retain the decision, rationale, evidence, and effective period.'],
                ['Approve the payroll basis', 'Keep preparation separate from human approval for any identity-, employment-, or money-impacting result.'],
                ['Release and reconcile evidence', 'Define the authorized posting, lock, distribution, statutory, correction, reconciliation, retention, and incident handoffs that are actually in scope.'],
            ],
            'evidence' => [
                ['System and source map', 'Approved inputs, field definitions, lineage, calendars, owners, interfaces, failure paths, and reconciliation rules.'],
                ['Calculation and exception contract', 'Authorized pay rules, rounding, cut-offs, adjustments, approval thresholds, test cases, and decision evidence.'],
                ['Role and release controls', 'Maker-checker boundaries, least-privilege matrix, posting and lock authority, delivery route, audit events, and recovery process.'],
                ['Statutory and records evidence', 'Named obligations, applicable period, reconciled support, exception treatment, retention, privacy controls, and current owner approval.'],
            ],
            'planner_copy' => 'Bring the workforce scope, time and attendance sources, cut-off pattern, approval roles, expected outputs, exceptions, and current systems. The first step is a controlled map—not a payroll guarantee.',
        ],
        'hr-administration' => [
            'title' => 'HR Administration Planning',
            'description' => 'Clarify worker-record, onboarding, communication, case, policy, privacy, and escalation responsibilities before an HR-administration scope is proposed.',
            'eyebrow' => 'Solution lens / HR administration',
            'hero_before' => 'Give workforce support ',
            'hero_emphasis' => 'clear ownership.',
            'hero_after' => '',
            'lede' => 'This planning lens maps worker records, onboarding handoffs, employee communication, case ownership, policy authority, exceptions, and evidence. It does not claim a universal HR process or current administrative capacity.',
            'signal_core' => ['Worker', 'support'],
            'signal_nodes' => ['Record', 'Onboard', 'Case', 'Escalate'],
            'intent_title' => 'Define which owner handles each worker-facing moment.',
            'intent_copy' => 'The intended output is an administration boundary that tells workers and operating teams where a record originates, who may act, which policy applies, what evidence is retained, and how a matter moves when it cannot be resolved at the first step.',
            'intent_boundary' => 'No employment-policy coverage, labor-compliance result, case outcome, response time, record completeness, document availability, or legal conclusion is promised here. Sensitive and statutory records require their own approved purpose and access controls.',
            'taascor_title' => 'Proposed service-administration questions',
            'taascor_copy' => 'The proposed TAASCOR-side scope may examine onboarding coordination, authorized worker records, communications, case intake, evidence handling, workforce-support ownership, and escalations where the approved service and role matrix permit them.',
            'client_title' => 'Proposed policy-and-worksite questions',
            'client_copy' => 'The proposed employer or worksite scope may define role and site policies, day-to-day operating instructions, facilities and access, training, incident ownership, client-required records, escalation contacts, and acceptance of agreed service outputs.',
            'lifecycle' => [
                ['Define the worker and assignment record', 'Identify the authoritative identity, role, assignment, worksite, schedule, status, policy, and effective-period sources.'],
                ['Map onboarding handoffs', 'Assign ownership for instructions, contracts or notices, access, training, site requirements, acknowledgements, and unresolved prerequisites.'],
                ['Design communication and case intake', 'Provide purpose-specific routes, minimum fields, privacy information, response ownership, urgency handling, and accessible alternatives.'],
                ['Resolve with documented authority', 'Separate fact gathering, recommendation, approval, employee communication, appeal, and any legal or disciplinary decision.'],
                ['Close, retain, and review', 'Record the outcome, evidence, access, retention trigger, follow-up, correction route, trend owner, and required periodic review.'],
            ],
            'evidence' => [
                ['Approved administration scope', 'Current service catalogue, policy boundaries, responsibility matrix, exclusions, locations, owners, and review date.'],
                ['Worker-record governance', 'Purpose and field dictionary, source ownership, access matrix, notices, correction route, retention, and deletion controls.'],
                ['Case and decision procedure', 'Approved categories, urgency rules, human authority, escalation, appeal, communications, evidence, and legal review points.'],
                ['Operating acceptance', 'Named service owners, approved measures, denominators, review cadence, exception treatment, and documented acceptance process.'],
            ],
            'planner_copy' => 'Bring the worker population, assignment model, onboarding steps, communication needs, record sources, case types, policy owners, and escalation constraints that the proposed administration scope should resolve.',
        ],
        'facility-support' => [
            'title' => 'Facility Support Planning',
            'description' => 'Frame site-support tasks, schedules, equipment, safe-work interfaces, supervision, acceptance, and readiness evidence before a facility-support scope is proposed.',
            'eyebrow' => 'Solution lens / Facility support',
            'hero_before' => 'Start with the site, the task, and ',
            'hero_emphasis' => 'the operating boundary.',
            'hero_after' => '',
            'lede' => 'This planning lens turns work areas, tasks, schedules, materials, equipment, access, safe-work interfaces, supervision, and acceptance into a site-specific assessment. It does not assert coverage, capacity, or a safety outcome.',
            'signal_core' => ['Site', 'service'],
            'signal_nodes' => ['Task', 'Access', 'Control', 'Accept'],
            'intent_title' => 'Make the work environment part of the service definition.',
            'intent_copy' => 'The intended output is a site-support brief that defines tasks and exclusions, operating windows, dependencies, work-area controls, workforce and equipment assumptions, service acceptance, and the owner of every unresolved readiness item.',
            'intent_boundary' => 'No location, crew, equipment, certification, response time, inspection result, regulatory compliance, or service availability is promised here. Current site and service evidence must support the exact proposed scope.',
            'taascor_title' => 'Proposed service-model questions',
            'taascor_copy' => 'The proposed TAASCOR-side scope may examine workforce formation, scheduling, supervision boundaries, equipment and material responsibilities, work evidence, exceptions, support, and escalation where the approved service catalogue allows it.',
            'client_title' => 'Proposed site-owner questions',
            'client_copy' => 'The proposed site-owner scope may define work areas, tasks, access, utilities, hazards and safe-work rules, permits, equipment interfaces, orientation, service windows, day-to-day coordination, acceptance, and incident authority.',
            'lifecycle' => [
                ['Describe the site and work', 'Record areas, tasks, exclusions, volumes, service windows, constraints, access, dependencies, and the intended acceptance evidence.'],
                ['Assess readiness and interfaces', 'Identify site rules, safe-work responsibilities, permits, equipment, materials, facilities, utilities, training, and external-party dependencies.'],
                ['Propose workforce and supervision', 'Define roles, schedules, skill requirements, supervision boundaries, communications, replacements, and escalation without assuming capacity.'],
                ['Agree service controls', 'Set approved inspection or completion evidence, exception handling, issue ownership, change authority, measures, and review cadence.'],
                ['Authorize mobilization and review', 'Close the applicable site, legal, commercial, privacy, safety, workforce, equipment, and operating gates before work begins.'],
            ],
            'evidence' => [
                ['Approved service catalogue', 'Exact task scope, exclusions, locations, workforce model, supervision boundary, equipment responsibility, and review date.'],
                ['Site readiness record', 'Current survey or assessment, access, facilities, work windows, responsible owners, dependencies, and unresolved conditions.'],
                ['Safe-work and incident interfaces', 'Applicable owner-approved controls, orientations, permits, escalation, reporting, stop-work authority, and evidence requirements.'],
                ['Acceptance and change controls', 'Defined completion evidence, service measures, denominator, issue path, client acceptance, change authority, and periodic review.'],
            ],
            'planner_copy' => 'Bring the work areas, tasks, service windows, estimated workforce, equipment and material expectations, site controls, dependencies, and the operating problem the facility-support brief should assess.',
        ],
        'hris-enabled-operations' => [
            'title' => 'HRIS-Enabled Operations Planning',
            'description' => 'Map workforce sources, role access, approvals, exceptions, integrations, privacy, retention, and operating ownership before an HRIS-enabled scope is proposed.',
            'eyebrow' => 'Solution lens / HRIS-enabled operations',
            'hero_before' => 'Design the workflow before choosing ',
            'hero_emphasis' => 'the screen.',
            'hero_after' => '',
            'lede' => 'This planning lens explores where structured workforce data and controlled system handoffs may support an operating model. It does not assert that a module, integration, automation, security control, or service level is currently available.',
            'signal_core' => ['Workflow', 'evidence'],
            'signal_nodes' => ['Source', 'Role', 'Approve', 'Resolve'],
            'intent_title' => 'Connect each system action to a purpose and owner.',
            'intent_copy' => 'The intended output is a target workflow and boundary map: authoritative sources, roles, decisions, exceptions, integrations, notifications, records, support ownership, retention, and the evidence needed to verify each proposed capability.',
            'intent_boundary' => 'No production status, feature availability, integration, uptime, security certification, privacy compliance, data quality, automated decision, or customer access is promised here. The current product owner and security evidence remain decisive.',
            'taascor_title' => 'Proposed product-and-service questions',
            'taascor_copy' => 'The proposed TAASCOR-side scope may examine module fit, source intake, validations, role permissions, decision queues, audit events, user support, release ownership, and exceptions where product, engineering, security, privacy, and service owners approve them.',
            'client_title' => 'Proposed data-and-operating questions',
            'client_copy' => 'The proposed client or authorized-owner scope may define authoritative data, correction rights, identity and access, approval authority, integrations, service acceptance, user support, retention, incident handoffs, and contract-specific visibility.',
            'lifecycle' => [
                ['Map the operating workflow', 'Identify the event, source, user, decision, output, exception, evidence, and next owner at each transition.'],
                ['Define data and role boundaries', 'Specify approved fields, purposes, recipients, access scopes, corrections, masking, retention, and prohibited public exposure.'],
                ['Assess module and integration fit', 'Verify current product behavior, dependencies, interfaces, failure paths, support ownership, and the difference between configuration and custom work.'],
                ['Design human approval and exceptions', 'Keep identity-, employment-, access-, payroll-, and money-impacting actions under explicit authority with traceable review.'],
                ['Qualify, release, and monitor', 'Test the approved scope with synthetic data, resolve security and privacy gates, define support and rollback, and release only the verified behavior.'],
            ],
            'evidence' => [
                ['Current module inventory', 'Product owner, production status, version, supported workflow, limitations, dependencies, screenshots or demonstrations, and review date.'],
                ['Data and integration contract', 'Authoritative sources, field mapping, validation, lineage, correction, interface security, failure handling, reconciliation, and ownership.'],
                ['Role, privacy, and security review', 'Approved role matrix, purpose and recipient map, authentication, authorization, logging, retention, incident, vendor, and test evidence.'],
                ['Service and release model', 'Availability and support boundary, change authority, UAT, rollout, monitoring, recovery, rollback, acceptance, and ongoing review.'],
            ],
            'planner_copy' => 'Bring the current workflow, users, systems, sources, exceptions, approvals, outputs, integrations, retention needs, and support constraints. The first deliverable is a verified fit assessment, not a feature promise.',
            'secondary_link' => ['/platform/', 'Explore the platform model'],
        ],
    ];
}

function taascor_render_solution_detail(string $slug): void
{
    $catalogue = taascor_solution_details();
    if (!isset($catalogue[$slug])) {
        http_response_code(404);
        require dirname(__DIR__) . '/site/404.php';
        exit;
    }

    $solution = $catalogue[$slug];
    $sectionPrefix = 'solution-' . $slug;

    taascor_page_start([
        'title' => (string) $solution['title'],
        'description' => (string) $solution['description'],
        'active' => 'solutions',
        'body_class' => 'solution-detail-page',
        'robots' => 'index,follow',
    ]);
    ?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-title">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <nav class="breadcrumbs" aria-label="Breadcrumb">
                    <a href="<?= taascor_escape(taascor_url('/')) ?>">Home</a><span aria-hidden="true">/</span>
                    <a href="<?= taascor_escape(taascor_url('/solutions/')) ?>">Solutions</a><span aria-hidden="true">/</span>
                    <span aria-current="page"><?= taascor_escape((string) $solution['title']) ?></span>
                </nav>
                <p class="eyebrow"><?= taascor_escape((string) $solution['eyebrow']) ?></p>
                <h1 id="<?= taascor_escape($sectionPrefix) ?>-title"><?= taascor_escape((string) $solution['hero_before']) ?><em><?= taascor_escape((string) $solution['hero_emphasis']) ?></em><?= taascor_escape((string) $solution['hero_after']) ?></h1>
                <p class="hero-lede"><?= taascor_escape((string) $solution['lede']) ?></p>
                <div class="hero-actions">
                    <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Shape a workforce brief</a>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/solutions/')) ?>">View all solution lenses</a>
                </div>
                <p class="hero-note">Scope intent only. Exact services, responsibilities, evidence, availability, and terms require accountable-owner review and approved documentation.</p>
            </div>
            <div class="network-stage" aria-hidden="true">
                <span class="network-line line-a"></span><span class="network-line line-b"></span><span class="network-line line-c"></span>
                <div class="network-core"><?php foreach ($solution['signal_core'] as $index => $label): ?><?= $index > 0 ? '<br>' : '' ?><?= taascor_escape((string) $label) ?><?php endforeach; ?></div>
                <?php foreach ($solution['signal_nodes'] as $index => $label): ?>
                    <span class="network-node node-<?= taascor_escape(chr(97 + $index)) ?>"><?= taascor_escape((string) $label) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-intent-title">
        <div class="shell split">
            <div class="section-heading">
                <p class="section-kicker">Scope intent / 01</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-intent-title"><?= taascor_escape((string) $solution['intent_title']) ?></h2>
                <p class="section-copy"><?= taascor_escape((string) $solution['intent_copy']) ?></p>
            </div>
            <div class="notice-panel" role="note">
                <?= taascor_status_tag('Evidence and scope verification required', 'review') ?>
                <h3>What this page does not establish</h3>
                <p><?= taascor_escape((string) $solution['intent_boundary']) ?></p>
            </div>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-responsibility-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Proposed responsibility split / 02</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-responsibility-title">Put each decision beside the owner who can make it.</h2>
                <p class="section-copy">These are proposed questions for scoping, not a standing allocation of legal or operational responsibility. The approved proposal, contract, procedures, and applicable law determine the final boundary.</p>
            </div>
            <div class="module-grid module-grid-two">
                <article class="content-panel">
                    <p class="section-kicker">TAASCOR-side questions</p>
                    <h3><?= taascor_escape((string) $solution['taascor_title']) ?></h3>
                    <p><?= taascor_escape((string) $solution['taascor_copy']) ?></p>
                </article>
                <article class="content-panel">
                    <p class="section-kicker">Employer / authorized-owner questions</p>
                    <h3><?= taascor_escape((string) $solution['client_title']) ?></h3>
                    <p><?= taascor_escape((string) $solution['client_copy']) ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="scene" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-lifecycle-title">
        <div class="shell split">
            <div class="section-heading sticky-intro">
                <p class="section-kicker">Proposed lifecycle / 03</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-lifecycle-title">Move from context to authorized action.</h2>
                <p class="section-copy">Each stage should preserve the source, decision, owner, evidence, exception, and next handoff. The sequence remains a proposed model until the relevant owners approve it.</p>
            </div>
            <ol class="process-list">
                <?php foreach ($solution['lifecycle'] as [$title, $copy]): ?>
                    <li><h3><?= taascor_escape((string) $title) ?></h3><p><?= taascor_escape((string) $copy) ?></p></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="scene scene-tinted" aria-labelledby="<?= taascor_escape($sectionPrefix) ?>-evidence-title">
        <div class="shell">
            <div class="section-heading">
                <p class="section-kicker">Evidence still required / 04</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-evidence-title">A credible scope resolves to current records.</h2>
                <p class="section-copy">Before this lens can support a proposal or public capability statement, the applicable evidence needs an owner, source, effective period, approved wording, review date, and defined correction path.</p>
            </div>
            <div class="module-grid module-grid-two">
                <?php foreach ($solution['evidence'] as $index => [$title, $copy]): ?>
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
                <p class="section-kicker">Start with a reviewable brief</p>
                <h2 id="<?= taascor_escape($sectionPrefix) ?>-action-title">Bring the operating context. Keep assumptions visible.</h2>
                <p class="section-copy"><?= taascor_escape((string) $solution['planner_copy']) ?></p>
            </div>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/workforce/')) ?>">Open the Workforce Planner</a>
                <?php if (isset($solution['secondary_link'])): ?>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url((string) $solution['secondary_link'][0])) ?>"><?= taascor_escape((string) $solution['secondary_link'][1]) ?></a>
                <?php else: ?>
                    <a class="button button-outline" href="<?= taascor_escape(taascor_url('/proof/')) ?>">Review the proof standard</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
<?php
    taascor_page_end();
}

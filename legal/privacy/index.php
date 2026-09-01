<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Privacy Framework',
    'description' => 'Review the privacy-by-design framework for TAASCOR’s public website and staged recruitment experience.',
    'active' => 'legal',
    'body_class' => 'legal-page',
    'robots' => 'noindex,nofollow',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="privacy-title">
        <div class="shell hero-copy">
            <p class="eyebrow">Legal and support / Privacy</p>
            <h1 id="privacy-title">Privacy begins with <em>purpose and restraint.</em></h1>
            <p class="hero-lede">This page records the privacy-by-design framework for the pre-release website. It is not the final collection-specific privacy notice and must be approved by TAASCOR’s DPO/Legal owner before production data collection.</p>
        </div>
    </section>

    <section class="scene">
        <div class="shell legal-layout">
            <?php taascor_legal_navigation('privacy'); ?>
            <article class="legal-copy">
                <div class="notice-panel">
                    <strong>Publication status: approval required</strong>
                    <p>The final notice must identify the personal information controller, DPO or privacy contact, processing purposes and bases, recipient classes, retention periods or criteria, rights process, effective date, and notice-version owner. Those facts have not been supplied to this implementation.</p>
                </div>

                <h2>1. Scope of this framework</h2>
                <p>The public supporting pages are designed without advertising trackers, marketing cookies, account state, or embedded third-party media. A web host may still process ordinary request and security logs such as IP address, timestamp, requested route, browser information, and error details. The production host, exact log fields, purpose, access, and retention require infrastructure and privacy approval.</p>
                <p>Recruitment, applicant-account, employee/HRIS, client, and staff systems have different purposes and risk levels. Each system requires its own accurate collection notice and access boundary.</p>

                <h2>2. Staged recruitment collection</h2>
                <p>The first application step should request only what is needed to identify the role, contact the applicant, and assess objective initial fit. Additional information belongs at later stages only when its purpose, necessity, access, recipient, protection, and retention have been approved.</p>
                <ul>
                    <li><strong>Initial intent:</strong> selected job, first and last name, email or mobile, preferred contact, broad location or work eligibility, and objective role-specific responses.</li>
                    <li><strong>Screening:</strong> relevant qualifications, certifications, employment history, or an optional résumé/manual-entry equivalent where justified.</li>
                    <li><strong>Shortlisted verification:</strong> referees or background-check information only with the required instruction, authority, and restricted handling.</li>
                    <li><strong>Conditional offer or onboarding:</strong> full address, government identifiers, medical information, emergency contacts, or statutory records only for a documented later-stage purpose.</li>
                </ul>
                <p>Religion, marital status, family information, medical data, government identifiers, and other sensitive or excessive fields must not be copied into first-touch recruitment merely because they existed in a legacy form.</p>

                <h2>3. Purpose-specific information</h2>
                <p>Every active form must explain, before submission:</p>
                <ul>
                    <li>which categories of personal data it requests and which are optional;</li>
                    <li>the specific processing purpose and the decision owner for its lawful basis;</li>
                    <li>how processing occurs and whether profiling or automated decision-making is used;</li>
                    <li>who may receive or access the information, including principals, clients, vendors, or authorities where applicable;</li>
                    <li>the retention period or the criteria used to determine it;</li>
                    <li>how an individual can exercise their privacy rights or raise a concern.</li>
                </ul>

                <h2>4. Separate notices and choices</h2>
                <p>A privacy notice is not the same as consent. Accuracy certification, acknowledgment of recruitment processing, optional talent-community participation, and optional marketing messages must remain separate and must not be bundled into one ambiguous checkbox.</p>

                <h2>5. Access and protection</h2>
                <p>Applicant, recruiter, HR, payroll, administrator, client, employee, and support roles require separately enumerated permissions that deny access by default. Government identifiers, medical/background information, uploaded files, and payroll-related records require tighter access, masking, private storage, safe delivery, and audit controls.</p>
                <p>Passwords, tokens, one-time codes, government identifiers, medical details, résumé contents, and unnecessary applicant information must not appear in URLs, analytics, routine logs, error messages, support tickets, or public interfaces.</p>

                <h2>6. Retention and deletion</h2>
                <p>No final retention periods are stated here because TAASCOR’s DPO, Recruitment, HR, Legal, Security, and records owners have not yet approved them. Before production, each record type must have a trigger, period, legal-hold rule, archival or anonymization decision, deletion method, responsible owner, and evidence of completion.</p>

                <h2>7. Privacy rights</h2>
                <p>Philippine privacy law provides data subjects with rights that may include being informed, access, objection, erasure or blocking, correction, data portability where applicable, complaint, and damages subject to law. The final TAASCOR process must explain how to submit, verify, track, and appeal a request without collecting excessive verification data.</p>
                <p>For authoritative general guidance, visit the <a href="https://privacy.gov.ph/data-privacy-act/" target="_blank" rel="noopener noreferrer">National Privacy Commission’s Data Privacy Act page<span class="sr-only"> (opens in a new tab)</span></a> and its <a href="https://privacy.gov.ph/the-right-to-be-informed/" target="_blank" rel="noopener noreferrer">right-to-be-informed guidance<span class="sr-only"> (opens in a new tab)</span></a>.</p>

                <h2>8. Contact and production gate</h2>
                <p>A DPO/privacy channel must be formally supplied, approved, monitored and published before this route can accept privacy requests. Review the <a href="/contact/">contact-routing framework</a> for the current gate; do not send identity documents, medical information, government identifiers, account passwords, or one-time codes through an unapproved channel.</p>
                <p class="meta">Framework updated 1 September 2026 / Final notice owner and effective date pending</p>
            </article>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

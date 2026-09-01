<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Website Terms',
    'description' => 'Review the pre-release website-use framework for TAASCOR’s public information, careers, applications, and linked systems.',
    'active' => 'legal',
    'body_class' => 'legal-page',
    'robots' => 'noindex,nofollow',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="terms-title">
        <div class="shell hero-copy">
            <p class="eyebrow">Legal and support / Terms</p>
            <h1 id="terms-title">Clear boundaries for the <em>public experience.</em></h1>
            <p class="hero-lede">These pre-release terms describe the intended website boundary. They require TAASCOR Legal approval before production and do not replace an employment contract, client agreement, application notice, or system-specific policy.</p>
        </div>
    </section>

    <section class="scene">
        <div class="shell legal-layout">
            <?php taascor_legal_navigation('terms'); ?>
            <article class="legal-copy">
                <div class="notice-panel">
                    <strong>Publication status: approval required</strong>
                    <p>Entity details, governing-law wording, effective date, policy owner, dispute channel, and any legally required notices must be completed by TAASCOR Legal before release.</p>
                </div>

                <h2>1. Public information</h2>
                <p>The website is intended to help visitors understand possible workforce-service conversations, discover published career opportunities, reach role-specific systems, and request further information. Website content does not by itself form a client contract, employment offer, appointment, guarantee, service-level commitment, or regulatory representation.</p>

                <h2>2. Evidence-gated content</h2>
                <p>Corporate, regulatory, service, client, location, leadership, platform, and performance statements should appear only after the applicable source, period, ownership, permission, and wording have been approved. Where this pre-release build identifies a verification gap, visitors should not infer either a positive or negative fact from the absence of the claim.</p>

                <h2>3. Career opportunities and applications</h2>
                <p>Only opportunities marked active by the authoritative recruitment source should accept applications. A job page or application acknowledgment is not an offer of employment. Requirements, worksite, schedule, opening count, process, and closing state may change through approved recruitment operations.</p>
                <p>Applicants must submit information that is accurate to the best of their knowledge and must not upload malicious content, information they are not authorized to provide, or unnecessary sensitive data. The application-specific privacy notice governs processing for that submission.</p>

                <h2>4. Accounts and role-specific systems</h2>
                <p>Applicants, employees, clients, and authorized staff use separate access paths. Users are responsible for protecting their own credentials, recovery links, and one-time codes and for reporting suspected unauthorized access. A user must not attempt to access another person’s records, bypass access controls, probe systems, or interfere with availability.</p>

                <h2>5. External destinations</h2>
                <p>The public website may clearly hand users to a separately governed system, such as the existing HRIS. External destinations can have their own terms, privacy notices, security controls, availability, and support ownership. Check the domain before entering credentials.</p>

                <h2>6. Acceptable use</h2>
                <p>Do not use the website to:</p>
                <ul>
                    <li>submit unlawful, fraudulent, misleading, abusive, or infringing content;</li>
                    <li>impersonate another person or misrepresent authority;</li>
                    <li>upload malware or files intended to defeat validation or security controls;</li>
                    <li>scrape personal data, overload services, or attempt unauthorized access;</li>
                    <li>reuse TAASCOR or third-party branding in a way that implies an unapproved relationship.</li>
                </ul>

                <h2>7. Availability and changes</h2>
                <p>Routes, content, vacancies, forms, and linked services may be updated, paused, closed, or corrected. Production incident, maintenance, rollback, and continuity commitments require separately approved operating documentation; no universal uptime or response promise is made on this page.</p>

                <h2>8. Contact</h2>
                <p>For a website-use question, review the <a href="/contact/">contact-routing framework</a>; an official monitored channel remains an approval gate. Do not include passwords, one-time codes, government identifiers, medical information, or payroll records.</p>
                <p class="meta">Framework updated 1 September 2026 / Legal owner and effective date pending</p>
            </article>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

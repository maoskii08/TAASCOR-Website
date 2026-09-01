<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Recruitment Safety',
    'description' => 'Use TAASCOR’s recruitment safety checklist to verify opportunities, protect account credentials, limit sensitive-data sharing, and report suspicious messages.',
    'active' => 'legal',
    'body_class' => 'legal-page',
    'robots' => 'noindex,nofollow',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="safety-title">
        <div class="shell hero-copy">
            <p class="eyebrow">Legal and support / Recruitment safety</p>
            <h1 id="safety-title">Verify first. <em>Protect your path.</em></h1>
            <p class="hero-lede">Fraudulent recruitment can imitate brands, vacancies, recruiters, forms, and login screens. Use a known TAASCOR route and confirm suspicious requests before responding.</p>
            <div class="hero-actions">
                <a class="button" href="<?= taascor_escape(taascor_url('/jobs/')) ?>">Browse the jobs route</a>
                <a class="button button-outline" href="#report">Report a concern</a>
            </div>
        </div>
    </section>

    <section class="scene">
        <div class="shell legal-layout">
            <?php taascor_legal_navigation('anti-fraud'); ?>
            <article class="legal-copy">
                <div class="notice-panel">
                    <strong>Recruitment-fee policy wording requires owner approval</strong>
                    <p>This implementation will not invent an absolute fee statement. TAASCOR Recruitment and Legal must provide the exact approved policy, official channels, escalation owner, and publication wording before production.</p>
                </div>

                <h2>1. Start from a known route</h2>
                <p>Use the website’s Careers page to discover published opportunities. A forwarded screenshot, social-media post, messaging-app account, shortened link, or copied application form is not proof that a vacancy or recruiter is authorized.</p>

                <h2>2. Check the full destination</h2>
                <ul>
                    <li>Read the entire domain before signing in or submitting information.</li>
                    <li>Be cautious when a message creates urgency, secrecy, or pressure to move away from the official application route.</li>
                    <li>Do not ignore browser security warnings or continue through a certificate warning.</li>
                    <li>Confirm unexpected domains, email addresses, phone numbers, bank details, QR codes, and document requests through a known contact point.</li>
                </ul>

                <h2>3. Protect credentials and recovery</h2>
                <p>Do not share a password, one-time code, recovery link, session cookie, or authentication screenshot with a recruiter, support agent, supervisor, colleague, or client. A legitimate support process should not require your password.</p>

                <h2>4. Limit sensitive information</h2>
                <p>The initial application route should not require government identifiers, medical details, family information, or other later-stage records. Do not send these through an unverified chat, personal storage link, or ordinary email request. If a later-stage request is expected, confirm its purpose, recipient, secure upload route, and privacy notice.</p>

                <h2>5. Pause before any payment request</h2>
                <p>Do not transfer money, buy a product, purchase a device or uniform, or disclose bank details based solely on an unsolicited recruitment message. Verify the request and the current approved recruitment-fee policy through a known TAASCOR contact before taking action.</p>

                <h2>6. Preserve useful evidence</h2>
                <p>Without opening suspicious attachments, retain the sender address or profile, full URL, date and time, job title, message text, requested action, and any payment destination. Redact passwords, one-time codes, government identifiers, medical data, and unnecessary personal information from a report.</p>

                <h2 id="report">7. Report a concern</h2>
                <p>Use the <a href="/contact/">contact-routing framework</a> only after an official recruitment-safety channel and monitoring owner are approved. Do not forward executable files or include your password, one-time codes, government identifiers, medical information, bank credentials, or full identity documents.</p>
                <p>If you believe an account has been compromised, change the password through the known service, sign out other sessions where available, and contact the relevant system owner.</p>

                <h2>8. Publication and response gap</h2>
                <p>Before production, TAASCOR must confirm monitored fraud-reporting channels, response ownership, escalation criteria, preservation rules, applicant guidance, and a precise recruitment-fee statement.</p>
                <p class="meta">Safety guidance updated 1 September 2026 / Recruitment and Legal approval pending</p>
            </article>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

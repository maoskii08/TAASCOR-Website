<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/site/bootstrap.php';

taascor_page_start([
    'title' => 'Accessibility',
    'description' => 'Review the accessibility target, supported interaction patterns, known transition gaps, and feedback route for the TAASCOR website.',
    'active' => 'legal',
    'body_class' => 'legal-page',
    'robots' => 'noindex,nofollow',
]);
?>
<main id="main-content" tabindex="-1">
    <section class="page-hero" aria-labelledby="accessibility-title">
        <div class="shell hero-copy">
            <p class="eyebrow">Legal and support / Accessibility</p>
            <h1 id="accessibility-title">The journey should remain clear <em>without spectacle.</em></h1>
            <p class="hero-lede">The new supporting experience targets WCAG 2.2 Level AA-oriented behavior while retaining the TAASCOR cinematic language as progressive enhancement.</p>
        </div>
    </section>

    <section class="scene">
        <div class="shell legal-layout">
            <?php taascor_legal_navigation('accessibility'); ?>
            <article class="legal-copy">
                <div class="notice-panel">
                    <strong>Conformance status: not yet claimed</strong>
                    <p>These supporting routes implement accessibility foundations, but a complete conformance statement requires representative manual and automated evaluation across the full home, careers, application, account, portal, and error journeys.</p>
                </div>

                <h2>1. Accessibility target</h2>
                <p>TAASCOR is designing the integrated website toward the Web Content Accessibility Guidelines (WCAG) 2.2 at Level AA. This is a target and testing standard, not a claim that every current or legacy route already conforms.</p>

                <h2>2. Features in the supporting experience</h2>
                <ul>
                    <li>Semantic header, navigation, main, section, article, table, list, and footer landmarks.</li>
                    <li>A skip link and programmatically focusable main region.</li>
                    <li>Visible keyboard focus and no hover-only required action.</li>
                    <li>A mobile navigation that remains visible and usable when JavaScript is unavailable.</li>
                    <li>Text and controls represented in HTML rather than only in decorative graphics.</li>
                    <li>Reduced-motion behavior that removes animated signal movement and shortens transitions.</li>
                    <li>Responsive layouts designed to avoid horizontal overflow at narrow widths.</li>
                    <li>Forced-color and print treatments for additional fallback coverage.</li>
                </ul>

                <h2>3. Cinematic and motion boundaries</h2>
                <p>Motion may reveal, connect, orient, or invite, but it must not delay required content, trap scrolling, change meaning without an equivalent, or become the only way to reach an action. Decorative network diagrams are hidden from assistive technology because their business meaning is repeated in the surrounding text.</p>

                <h2>4. Forms and account workflows</h2>
                <p>Recruitment and account forms require persistent labels, clear required/optional states, accessible instructions, error summaries plus inline messages, keyboard completion, safe focus movement, status announcements, and recovery that does not depend on color alone. File-upload and privacy steps need equivalent manual-entry or support routes where appropriate.</p>

                <h2>5. Testing still required</h2>
                <ul>
                    <li>Keyboard-only traversal at desktop, tablet, and narrow-phone layouts.</li>
                    <li>Screen-reader evaluation using representative Windows and mobile combinations.</li>
                    <li>Zoom and text-spacing checks without content loss.</li>
                    <li>Contrast verification for all states, including focus, error, disabled, selected, and high-contrast modes.</li>
                    <li>Reduced-motion, no-JavaScript, missing-asset, and slow-network journeys.</li>
                    <li>Complete application, login, recovery, portal, and status workflows with realistic validation messages.</li>
                </ul>

                <h2>6. Feedback and alternative access</h2>
                <p>If a page, control, document, or application step is difficult to use, review the <a href="/contact/">contact-routing framework</a>; an official monitored accessibility channel remains an approval gate. Do not include passwords, one-time codes, government identifiers, medical information, or other unnecessary personal data.</p>
                <p>TAASCOR should confirm the monitored accessibility owner, response target, and alternative application/support process before production.</p>

                <h2>7. Standard reference</h2>
                <p>Read the <a href="https://www.w3.org/TR/WCAG22/" target="_blank" rel="noopener noreferrer">WCAG 2.2 Recommendation<span class="sr-only"> (opens in a new tab)</span></a> from the World Wide Web Consortium.</p>
                <p class="meta">Accessibility framework updated 1 September 2026 / Conformance audit pending</p>
            </article>
        </div>
    </section>
</main>
<?php taascor_page_end(); ?>

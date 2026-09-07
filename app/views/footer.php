<?php

declare(strict_types=1);
?>
</main>
<footer class="portal-footer">
    <p>TAASCOR applicant and workforce portal</p>
    <p>
        <?php if (privacy_collection_is_enabled('applicant') && !privacy_notice_is_draft('applicant')): ?>
            <a href="/apply/privacy.php">Applicant privacy notice</a> ·
        <?php endif; ?>
        <a href="/">Corporate site</a>
    </p>
</footer>
</body>
</html>

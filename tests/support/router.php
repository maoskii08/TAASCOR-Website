<?php

declare(strict_types=1);

// Exercise the same routing and internal-path protections that the local
// application uses. Keeping the QA entrypoint as a thin delegate prevents the
// harness from silently serving files that production routing rejects.
return require dirname(__DIR__, 2) . '/router.php';

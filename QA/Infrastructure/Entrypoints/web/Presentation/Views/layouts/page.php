<?php

declare(strict_types=1);

$message = \QA\Infrastructure\Entrypoints\Web\Presentation\Flash::message();
$success = \QA\Infrastructure\Entrypoints\Web\Presentation\Flash::success();
require __DIR__ . '/header.php';
require __DIR__ . '/menu.php';

$hasFlash = ($success !== '') || ($message !== '');
if ($hasFlash) {
    echo '<div class="flash-stack" role="status">';
    if ($success !== '') {
        echo '<p class="flash flash-success">' . e($success) . '</p>';
    }
    if ($message !== '') {
        echo '<p class="flash flash-error">' . e($message) . '</p>';
    }
    echo '</div>';
}

echo $content ?? '';

require __DIR__ . '/footer.php';

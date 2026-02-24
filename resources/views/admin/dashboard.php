<?php
declare(strict_types=1);

/** @var string $title */
$title = $title ?? 'Dashboard';
$contentView = __DIR__ . '/partials/dashboard-content.php';

require __DIR__ . '/layouts/master.php';

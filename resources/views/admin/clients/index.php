<?php
declare(strict_types=1);

/** @var string $title */
/** @var array<int, array<string, mixed>> $clients */
/** @var string|null $success */
/** @var string|null $error */
/** @var string $csrfToken */

$title = $title ?? 'Müşteriler';
$clients = $clients ?? [];
$success = is_string($success ?? null) ? $success : null;
$error = is_string($error ?? null) ? $error : null;
$csrfToken = $csrfToken ?? ($_SESSION['_csrf_token'] ?? '');
$contentView = __DIR__ . '/partials/index-content.php';

require __DIR__ . '/../layouts/master.php';

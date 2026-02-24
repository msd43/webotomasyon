<?php
declare(strict_types=1);

/** @var string $title */
/** @var string|null $error */
/** @var array<string, mixed> $old */
/** @var string $csrfToken */

$title = $title ?? 'Yeni Müşteri Ekle';
$error = is_string($error ?? null) ? $error : null;
$old = is_array($old ?? null) ? $old : [];
$csrfToken = $csrfToken ?? ($_SESSION['_csrf_token'] ?? '');
$contentView = __DIR__ . '/partials/create-content.php';

require __DIR__ . '/../layouts/master.php';

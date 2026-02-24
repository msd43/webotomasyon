<?php
declare(strict_types=1);

/** @var string $title */
/** @var array<string, mixed> $client */
/** @var string|null $error */
/** @var string $csrfToken */

$title = $title ?? 'Müşteri Düzenle';
$client = is_array($client ?? null) ? $client : [];
$error = is_string($error ?? null) ? $error : null;
$csrfToken = $csrfToken ?? ($_SESSION['_csrf_token'] ?? '');
$contentView = __DIR__ . '/partials/edit-content.php';

require __DIR__ . '/../layouts/master.php';

<?php
declare(strict_types=1);

$title = $title ?? 'Ürünler';
$products = $products ?? [];
$success = is_string($success ?? null) ? $success : null;
$error = is_string($error ?? null) ? $error : null;
$csrfToken = $csrfToken ?? ($_SESSION['_csrf_token'] ?? '');
$contentView = __DIR__ . '/partials/index-content.php';
require __DIR__ . '/../layouts/master.php';

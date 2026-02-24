<?php
declare(strict_types=1);

$title = $title ?? 'Ürün Düzenle';
$product = is_array($product ?? null) ? $product : [];
$groups = $groups ?? [];
$error = is_string($error ?? null) ? $error : null;
$csrfToken = $csrfToken ?? ($_SESSION['_csrf_token'] ?? '');
$contentView = __DIR__ . '/partials/edit-content.php';
require __DIR__ . '/../layouts/master.php';

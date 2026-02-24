<?php
declare(strict_types=1);

/** @var string $title */
/** @var string $contentView */
$title = $title ?? 'Admin Paneli';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | MasterVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8R2D4VY6gKf8zE4p4Y0P5h5jM90mQ1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="min-h-screen flex">
        <aside class="w-72 bg-slate-900/95 border-r border-slate-800 p-6 flex flex-col">
            <div class="mb-10">
                <a href="/admin/dashboard" class="inline-flex items-center gap-3 group">
                    <span class="h-10 w-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center font-bold shadow-lg shadow-indigo-500/30">MV</span>
                    <span class="text-xl font-semibold tracking-tight text-white group-hover:text-indigo-300 transition">MasterVault</span>
                </a>
            </div>

            <nav class="space-y-2 text-sm font-medium">
                <a href="/admin/dashboard" class="flex items-center gap-3 rounded-2xl px-4 py-3 bg-indigo-500/15 text-indigo-300 border border-indigo-500/20">
                    <i class="fa-solid fa-gauge-high w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/clients" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-300 hover:bg-indigo-500/15 hover:text-indigo-300 transition">
                    <i class="fa-solid fa-users w-5"></i>
                    <span>Müşteriler</span>
                </a>
                <a href="/admin/product-groups" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-300 hover:bg-indigo-500/15 hover:text-indigo-300 transition">
                    <i class="fa-solid fa-box-open w-5"></i>
                    <span>Katalog Grupları</span>
                </a>
                <a href="/admin/products" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-300 hover:bg-indigo-500/15 hover:text-indigo-300 transition">
                    <i class="fa-solid fa-cubes w-5"></i>
                    <span>Ürünler</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-300 hover:bg-indigo-500/15 hover:text-indigo-300 transition">
                    <i class="fa-solid fa-cart-shopping w-5"></i>
                    <span>Siparişler</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-300 hover:bg-indigo-500/15 hover:text-indigo-300 transition">
                    <i class="fa-solid fa-file-invoice-dollar w-5"></i>
                    <span>Faturalar</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-300 hover:bg-indigo-500/15 hover:text-indigo-300 transition">
                    <i class="fa-solid fa-headset w-5"></i>
                    <span>Destek</span>
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-20 border-b border-slate-800 bg-slate-900/60 backdrop-blur px-8 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-slate-100"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

                <div class="flex items-center gap-4">
                    <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-800 text-slate-200 hover:bg-indigo-500/20 hover:text-indigo-300 transition">
                        <i class="fa-regular fa-bell"></i>
                        <span class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-rose-500"></span>
                    </button>

                    <div class="flex items-center gap-3 rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2">
                        <img src="https://ui-avatars.com/api/?name=Sistem+Yoneticisi&background=1e1b4b&color=ffffff" alt="Profil" class="h-9 w-9 rounded-xl">
                        <div class="leading-tight">
                            <p class="text-sm font-semibold text-slate-100">Sistem Yöneticisi</p>
                            <p class="text-xs text-slate-400">Super Admin</p>
                        </div>
                    </div>

                    <a href="/logout" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:from-indigo-400 hover:to-violet-500 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Çıkış Yap</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 p-8 bg-slate-950">
                <?php
                if (!isset($contentView) || !is_string($contentView) || !is_file($contentView)) {
                    throw new RuntimeException('Admin içerik görünümü bulunamadı.');
                }

                require $contentView;
                ?>
            </main>
        </div>
    </div>
</body>
</html>

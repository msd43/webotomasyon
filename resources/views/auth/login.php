<?php
declare(strict_types=1);

/** @var string|null $error */
/** @var string $csrfToken */
$error = $error ?? null;
$csrfToken = $csrfToken ?? ($_SESSION['_csrf_token'] ?? '');
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterVault | Güvenli Giriş</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca'
                        }
                    },
                    boxShadow: {
                        premium: '0 25px 50px -12px rgba(79, 70, 229, 0.45)'
                    }
                }
            }
        };
    </script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -left-28 top-16 h-80 w-80 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 bottom-10 h-72 w-72 rounded-full bg-violet-500/20 blur-3xl"></div>

        <main class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <section class="w-full max-w-md rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900/90 to-slate-800/70 p-8 shadow-premium backdrop-blur-xl">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 font-bold text-white shadow-lg shadow-indigo-500/30">
                        MV
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight">MasterVault Giriş</h1>
                    <p class="mt-2 text-sm text-slate-300">Hesabınıza güvenli şekilde erişin.</p>
                </div>

                <?php if (is_string($error) && $error !== ''): ?>
                    <div class="mb-5 rounded-2xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/login" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">E-posta Adresi</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            class="block w-full rounded-2xl border border-slate-600/70 bg-slate-900/80 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                            placeholder="ornek@mastervault.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Şifre</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="block w-full rounded-2xl border border-slate-600/70 bg-slate-900/80 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                            <input
                                type="checkbox"
                                name="remember_me"
                                value="1"
                                class="h-4 w-4 rounded border-slate-500 bg-slate-800 text-indigo-500 focus:ring-indigo-500/50"
                            >
                            <span>Beni Hatırla</span>
                        </label>
                        <a href="#" class="text-sm font-medium text-indigo-300 transition hover:text-indigo-200">Şifremi Unuttum</a>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-3 text-sm font-semibold text-white transition duration-200 hover:from-indigo-400 hover:to-violet-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900"
                    >
                        Güvenli Giriş Yap
                    </button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>

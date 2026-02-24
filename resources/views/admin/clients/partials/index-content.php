<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Müşteri Yönetimi</h2>
            <p class="mt-1 text-sm text-slate-400">Tüm müşteri kayıtlarını buradan yönetebilirsiniz.</p>
        </div>
        <a href="/admin/clients/create" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-5 py-3 text-sm font-semibold text-white hover:from-indigo-400 hover:to-violet-500 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Yeni Müşteri Ekle</span>
        </a>
    </div>

    <?php if (is_string($success) && $success !== ''): ?>
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (is_string($error) && $error !== ''): ?>
        <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/80 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Ad Soyad</th>
                        <th class="px-4 py-3 text-left font-semibold">Şirket</th>
                        <th class="px-4 py-3 text-left font-semibold">E-posta</th>
                        <th class="px-4 py-3 text-left font-semibold">Durum</th>
                        <th class="px-4 py-3 text-right font-semibold">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    <?php if ($clients === []): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Kayıtlı müşteri bulunamadı.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clients as $client): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3"><?= htmlspecialchars((string) $client['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars((string) $client['first_name'] . ' ' . (string) $client['last_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars((string) ($client['company'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars((string) $client['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3">
                                    <?php if ((int) ($client['status'] ?? 0) === 1): ?>
                                        <span class="inline-flex rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-300">Aktif</span>
                                    <?php else: ?>
                                        <span class="inline-flex rounded-full bg-slate-700 px-3 py-1 text-xs font-semibold text-slate-300">Pasif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="/admin/clients/edit?id=<?= urlencode((string) $client['id']) ?>" class="inline-flex items-center gap-1 rounded-xl border border-indigo-500/30 px-3 py-1.5 text-xs font-semibold text-indigo-300 hover:bg-indigo-500/10 transition">
                                            <i class="fa-solid fa-pen"></i>
                                            <span>Düzenle</span>
                                        </a>
                                        <form method="POST" action="/admin/clients/delete" onsubmit="return confirm('Müşteriyi silmek istediğinize emin misiniz?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $client['id'], ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-rose-500/30 px-3 py-1.5 text-xs font-semibold text-rose-300 hover:bg-rose-500/10 transition">
                                                <i class="fa-solid fa-trash"></i>
                                                <span>Sil</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

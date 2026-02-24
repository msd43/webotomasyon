<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-2xl font-bold text-white">Ürünler</h2><p class="text-sm text-slate-400 mt-1">Katalog ürünlerini yönetin.</p></div>
        <a href="/admin/products/create" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-5 py-3 text-sm font-semibold text-white"><i class="fa-solid fa-plus"></i><span>Yeni Ürün Ekle</span></a>
    </div>

    <?php if (is_string($success) && $success !== ''): ?><div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-emerald-200 text-sm"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if (is_string($error) && $error !== ''): ?><div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-rose-200 text-sm"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900">
        <table class="min-w-full text-sm divide-y divide-slate-800">
            <thead class="text-slate-300">
                <tr><th class="px-4 py-3 text-left">ID</th><th class="px-4 py-3 text-left">Ad</th><th class="px-4 py-3 text-left">Grup</th><th class="px-4 py-3 text-left">Tip</th><th class="px-4 py-3 text-left">Fiyat</th><th class="px-4 py-3 text-left">Döngü</th><th class="px-4 py-3 text-right">İşlemler</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-200">
                <?php if ($products === []): ?><tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Kayıtlı ürün yok.</td></tr>
                <?php else: foreach ($products as $product): ?>
                    <tr>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) $product['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) ($product['group_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) $product['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars(number_format((float) $product['price'], 2, '.', ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) $product['billing_cycle'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="/admin/products/edit?id=<?= urlencode((string) $product['id']) ?>" class="px-3 py-1.5 rounded-xl border border-indigo-500/30 text-indigo-300 text-xs">Düzenle</a><form method="POST" action="/admin/products/delete" onsubmit="return confirm('Ürünü silmek istediğinize emin misiniz?');"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= htmlspecialchars((string) $product['id'], ENT_QUOTES, 'UTF-8') ?>"><button class="px-3 py-1.5 rounded-xl border border-rose-500/30 text-rose-300 text-xs" type="submit">Sil</button></form></div></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

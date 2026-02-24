<div class="space-y-6 max-w-3xl">
    <h2 class="text-2xl font-bold text-white">Ürün Grubu Ekle</h2>
    <?php if (is_string($error) && $error !== ''): ?><div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-rose-200 text-sm"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="POST" action="/admin/product-groups/store" class="rounded-3xl border border-slate-800 bg-slate-900 p-6 space-y-5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div><label class="block mb-2 text-sm text-slate-200">Ad</label><input class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100" name="name" required value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
        <div><label class="block mb-2 text-sm text-slate-200">Slug</label><input class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100" name="slug" required value="<?= htmlspecialchars((string) ($old['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
        <div><label class="block mb-2 text-sm text-slate-200">Açıklama</label><textarea class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100" name="description" rows="4"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></div>
        <div><label class="block mb-2 text-sm text-slate-200">Durum</label><select name="status" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100"><option value="1">Aktif</option><option value="0">Pasif</option></select></div>
        <div class="flex gap-3"><button class="px-5 py-3 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white" type="submit">Kaydet</button><a href="/admin/product-groups" class="px-5 py-3 rounded-2xl border border-slate-700 text-slate-300">Geri</a></div>
    </form>
</div>

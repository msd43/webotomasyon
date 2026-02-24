<div class="space-y-6 max-w-4xl">
    <div>
        <h2 class="text-2xl font-bold text-white">Müşteri Düzenle</h2>
        <p class="mt-1 text-sm text-slate-400">Müşteri bilgilerini güncelleyin.</p>
    </div>

    <?php if (is_string($error) && $error !== ''): ?>
        <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/clients/update" class="rounded-3xl border border-slate-800 bg-slate-900 p-6 space-y-5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($client['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Ad</label>
                <input type="text" name="first_name" required value="<?= htmlspecialchars((string) ($client['first_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Soyad</label>
                <input type="text" name="last_name" required value="<?= htmlspecialchars((string) ($client['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">E-posta</label>
                <input type="email" name="email" required value="<?= htmlspecialchars((string) ($client['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Telefon</label>
                <input type="text" name="phone" value="<?= htmlspecialchars((string) ($client['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Şirket</label>
                <input type="text" name="company" value="<?= htmlspecialchars((string) ($client['company'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Şehir</label>
                <input type="text" name="city" value="<?= htmlspecialchars((string) ($client['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Ülke</label>
                <input type="text" name="country" value="<?= htmlspecialchars((string) ($client['country'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200">Durum</label>
                <select name="status" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <option value="1" <?= ((string) ($client['status'] ?? '1') === '1') ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= ((string) ($client['status'] ?? '1') === '0') ? 'selected' : '' ?>>Pasif</option>
                </select>
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-200">Adres</label>
            <textarea name="address" rows="4" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"><?= htmlspecialchars((string) ($client['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-5 py-3 text-sm font-semibold text-white hover:from-indigo-400 hover:to-violet-500 transition">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Güncelle</span>
            </button>
            <a href="/admin/clients" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-800 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Geri Dön</span>
            </a>
        </div>
    </form>
</div>

<?php $hasCustomAvatar = user_has_custom_avatar($user); ?>
<div class="grid gap-6 xl:grid-cols-3">
  <section class="rounded-[30px] bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-1">
    <div class="profile-hero-card rounded-[28px] p-6 shadow-lg">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.25em] text-white/70">Profil Saya</div>
          <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-white"><?= e($user['name']) ?></h1>
          <div class="mt-2 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white"><?= e(role_label($user['role_code'])) ?></div>
        </div>
        <div class="shrink-0">
          <?= render_user_avatar($user, 'h-20 w-20 rounded-[26px] ring-4 ring-white/20', 'text-3xl') ?>
        </div>
      </div>
      <div class="mt-5 grid gap-3 text-sm text-white/90">
        <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3"><i class="fa-solid fa-user"></i><span>@<?= e($user['username']) ?></span></div>
        <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3"><i class="fa-solid fa-envelope"></i><span><?= e($user['email'] ?: '-') ?></span></div>
        <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3"><i class="fa-solid fa-phone"></i><span><?= e(format_phone($user['phone'] ?? '')) ?></span></div>
        <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3"><i class="fa-solid fa-building"></i><span><?= e($user['branch_name'] ?: 'Global') ?></span></div>
      </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
      <div class="mb-5 flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
          <i class="fa-solid fa-shield-heart"></i>
        </div>
        <div class="font-bold text-slate-900">Ganti Password</div>
      </div>
      <form method="post" action="<?= site_url('profile/updatePassword') ?>" class="space-y-4">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Password saat ini</label>
          <input type="password" name="current_password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Password baru</label>
          <input type="password" name="new_password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi password baru</label>
          <input type="password" name="new_password_confirmation" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
        </div>
        <button class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow hover:-translate-y-0.5 hover:bg-slate-800">
          <i class="fa-solid fa-key"></i> Simpan Password
        </button>
      </form>
    </div>
  </section>

  <section class="rounded-[30px] bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
    <div class="mb-6">
      <div class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Pengaturan Akun</div>
      <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Edit Profil</h2>
    </div>

    <form method="post" action="<?= site_url('profile/update') ?>" enctype="multipart/form-data" class="space-y-6">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

      <div class="grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
          <div class="text-sm font-semibold text-slate-700">Foto profil</div>
          <div class="mt-4 flex justify-center">
            <?= render_user_avatar($user, 'h-40 w-40 rounded-[30px] ring-1 ring-slate-200', 'text-6xl') ?>
          </div>
          <div class="mt-4 text-xs leading-6 text-slate-500">JPG, PNG, WEBP · Maks. 2 MB</div>
          <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="mt-4 block w-full text-sm text-slate-500 file:mr-3 file:rounded-2xl file:border-0 file:bg-sky-50 file:px-4 file:py-2.5 file:font-semibold file:text-sky-700 hover:file:bg-sky-100">
          <?php if ($hasCustomAvatar): ?>
            <button type="submit" formaction="<?= site_url('profile/removePhoto') ?>" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-100" onclick="return confirm('Hapus foto profil saat ini?')">
              <i class="fa-solid fa-trash"></i> Hapus Foto
            </button>
          <?php endif; ?>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</label>
            <input type="text" name="name" value="<?= e($user['name']) ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
            <input type="text" name="username" value="<?= e($user['username']) ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="<?= e($user['email']) ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor telepon</label>
            <input type="text" name="phone" value="<?= e(format_phone($user['phone'] ?? '')) ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="XXXX-XXXX-XXXX">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Jenis kelamin</label>
            <select name="gender" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
              <option value="">Pilih jenis kelamin</option>
              <option value="L" <?= ($user['gender'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
              <option value="P" <?= ($user['gender'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
            <input type="text" value="<?= e(role_label($user['role_code'])) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-500 outline-none" readonly>
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
            <textarea name="address" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"><?= e($user['address'] ?? '') ?></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Bio</label>
            <textarea name="bio" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"><?= e($user['bio'] ?? '') ?></textarea>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Cabang</label>
            <input type="text" value="<?= e($user['branch_name'] ?: 'Global / Semua Cabang') ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-500 outline-none" readonly>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:-translate-y-0.5 hover:bg-sky-700">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Profil
        </button>
      </div>
    </form>
  </section>
</div>

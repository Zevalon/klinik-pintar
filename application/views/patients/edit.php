<div class="mb-6 flex items-start justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Edit Pasien</h1>
    
  </div>
  <a href="<?= site_url('patients') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-soft hover:border-brand-200 hover:text-brand-700">
    <i class="fa-solid fa-arrow-left"></i> Kembali
  </a>
</div>

<div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-5 flex items-center gap-4">
    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-brand-50 text-xl font-bold text-brand-700"><?= e(initials($patient['name'])) ?></div>
    <div>
      <div class="text-lg font-bold text-slate-900"><?= e($patient['name']) ?></div>
      <div class="text-sm text-slate-500">No RM: <?= e($patient['medical_record_no']) ?></div>
    </div>
  </div>

  <form method="post" action="<?= site_url('patients/update/'.$patient['id']) ?>" class="space-y-5">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <div class="grid gap-5 md:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Nama</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="name" value="<?= e($patient['name']) ?>" required>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">NIK</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="nik" value="<?= e($patient['nik']) ?>">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Gender</label>
        <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="gender">
          <option value="L" <?= $patient['gender']=='L'?'selected':'' ?>>Laki-laki</option>
          <option value="P" <?= $patient['gender']=='P'?'selected':'' ?>>Perempuan</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Tanggal Lahir</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" type="date" name="birth_date" value="<?= e($patient['birth_date']) ?>">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">No. HP</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="phone" value="<?= e($patient['phone'] ? format_phone($patient['phone']) : '') ?>">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Tipe Pasien</label>
        <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="patient_type">
          <option value="umum" <?= $patient['patient_type']=='umum'?'selected':'' ?>>Umum</option>
          <option value="rujukan" <?= $patient['patient_type']=='rujukan'?'selected':'' ?>>Rujukan</option>
          <option value="kontrol" <?= $patient['patient_type']=='kontrol'?'selected':'' ?>>Kontrol</option>
        </select>
      </div>
    </div>
    <div>
      <label class="mb-1 block text-sm font-medium text-slate-600">Alamat</label>
      <textarea class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="address"><?= e($patient['address']) ?></textarea>
    </div>
    <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-floppy-disk"></i> Update Pasien</button>
  </form>
</div>

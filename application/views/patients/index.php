<div class="mb-6 flex items-end justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Data Pasien</h1>
    
  </div>
  <?php if (role_in(['super_admin','branch_admin','front_office'])): ?>
  <button type="button" data-modal-open="patientCreateModal" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-user-plus"></i> Tambah Pasien</button>
  <?php endif; ?>
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Daftar Pasien Cabang</h2>
      
    </div>
    <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Total <?= count($patients) ?> pasien</div>
  </div>
  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">No RM</th>
            <th class="px-4 py-3 font-semibold">Nama</th>
            <th class="px-4 py-3 font-semibold">Gender</th>
            <th class="px-4 py-3 font-semibold">NIK</th>
            <th class="px-4 py-3 font-semibold">No. HP</th>
            <th class="px-4 py-3 font-semibold">Tipe</th>
            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($patients as $p): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 font-mono text-xs font-semibold text-slate-700"><?= e($p['medical_record_no']) ?></td>
            <td class="px-4 py-3"><div class="font-semibold text-slate-800"><?= e($p['name']) ?></div><div class="text-xs text-slate-500"><?= e(format_date_id($p['birth_date'])) ?></div></td>
            <td class="px-4 py-3 text-slate-700"><?= e(gender_label($p['gender'])) ?></td>
            <td class="px-4 py-3 text-slate-700"><?= e($p['nik']) ?></td>
            <td class="px-4 py-3 text-slate-700"><?= e(format_phone($p['phone'])) ?></td>
            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"><?= e(patient_type_label($p['patient_type'])) ?></span></td>
            <td class="px-4 py-3 text-right">
              <div class="flex justify-end gap-2">
                <?php if (role_in(['super_admin','owner','branch_admin','doctor','nurse'])): ?>
                <a class="inline-flex items-center gap-2 rounded-xl border border-brand-100 bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700 hover:border-brand-200 hover:text-brand-900" href="<?= site_url('medicalrecords/show/'.$p['id']) ?>">
                  <i class="fa-solid fa-notes-medical"></i> Rekam Medis
                </a>
                <?php endif; ?>
                <?php if (role_in(['super_admin','branch_admin','front_office'])): ?>
                <a class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-sky-200 hover:text-sky-700" href="<?= site_url('patients/update/'.$p['id']) ?>" data-patient-edit='<?= e(json_encode($p)) ?>'>
                  <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
                <form method="post" action="<?= site_url('patients/delete/'.$p['id']) ?>" class="ajax-form" data-confirm="Hapus pasien ini?" data-reset-on-success="false">
                  <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                  <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50"><i class="fa-solid fa-trash"></i> Hapus</button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($patients)): ?><tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data pasien.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<div id="patientCreateModal" class="fixed inset-0 z-50 hidden bg-slate-950/50 p-4">
  <div class="mx-auto mt-10 max-w-3xl rounded-3xl bg-white p-6 shadow-2xl">
    <div class="mb-4 flex items-center justify-between"><h3 class="text-lg font-bold">Tambah Pasien Baru</h3><button type="button" data-modal-close="patientCreateModal" class="rounded-xl px-3 py-2 text-slate-500 hover:bg-slate-100"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
      <div class="font-semibold">Cek duplikasi terlebih dahulu</div>
      <p class="mt-1">Cari berdasarkan nama, NIK, nomor RM, atau no. HP. Bila pasien ditemukan, gunakan data yang sudah ada dan lanjutkan ke antrian.</p>
      <form method="get" action="<?= site_url('patients/searchExisting') ?>" class="patient-search-form mt-3 flex flex-col gap-3 md:flex-row" data-target="#modal-patient-search-result" data-mode="list">
        <input type="text" name="keyword" class="w-full rounded-2xl border border-amber-200 px-4 py-3 outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100" placeholder="Nama / NIK / RM / No. HP" required>
        <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-amber-500 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-600"><i class="fa-solid fa-magnifying-glass"></i> Cek Data</button>
      </form>
      <div id="modal-patient-search-result" class="mt-3"></div>
    </div>
    <form method="post" action="<?= site_url('patients/store') ?>" class="ajax-form space-y-4" data-close-modal="patientCreateModal">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div class="grid gap-4 md:grid-cols-2">
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="name" placeholder="Nama pasien" required>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="nik" placeholder="NIK">
      </div>
      <div class="grid gap-4 md:grid-cols-3"><select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="gender"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select><input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="date" name="birth_date"><input class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="phone" placeholder="0812-3456-7890"></div>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3" name="address" placeholder="Alamat"></textarea>
      <select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="patient_type"><option value="umum">Umum</option><option value="rujukan">Rujukan</option><option value="kontrol">Kontrol</option></select>
      <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Setelah pasien disimpan, lanjutkan pendaftaran ke menu antrian sesuai poli tujuan.</div>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-user-plus"></i> Simpan Pasien</button>
    </form>
  </div>
</div>

<div id="patientEditModal" class="fixed inset-0 z-50 hidden bg-slate-950/50 p-4">
  <div class="mx-auto mt-10 max-w-2xl rounded-3xl bg-white p-6 shadow-2xl">
    <div class="mb-4 flex items-center justify-between"><h3 class="text-lg font-bold">Edit Pasien</h3><button type="button" data-modal-close="patientEditModal" class="rounded-xl px-3 py-2 text-slate-500 hover:bg-slate-100"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="patient-edit-form" method="post" action="#" class="ajax-form space-y-4" data-close-modal="patientEditModal" data-reset-on-success="false">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div class="grid gap-4 md:grid-cols-2">
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="name" placeholder="Nama pasien" required>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="nik" placeholder="NIK">
      </div>
      <div class="grid gap-4 md:grid-cols-3"><select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="gender"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select><input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="date" name="birth_date"><input class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="phone" placeholder="0812-3456-7890"></div>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3" name="address" placeholder="Alamat"></textarea>
      <select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="patient_type"><option value="umum">Umum</option><option value="rujukan">Rujukan</option><option value="kontrol">Kontrol</option></select>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-floppy-disk"></i> Update Pasien</button>
    </form>
  </div>
</div>

<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Data Poli Cabang</h1>
    <div class="mt-1 text-sm text-slate-500">Kelola data poli untuk cabang aktif <span class="font-semibold text-slate-700"><?= e($branch_name) ?></span>.</div>
  </div>
  <button type="button" data-modal-open="clinicCreateModal" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700">
    <i class="fa-solid fa-plus"></i> Tambah Poli
  </button>
</div>

<?php if (role_in(['super_admin', 'owner'])): ?>
  <div class="mb-6 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 shadow-sm">
    <div class="font-semibold">Tips untuk pengguna global</div>
    <div class="mt-1">Untuk mengelola poli cabang lain, ubah dulu <span class="font-semibold">Cabang Aktif</span> dari menu <span class="font-semibold">Cabang</span> di pojok kanan atas atau sidebar.</div>
  </div>
<?php endif; ?>

<div class="mb-6 grid gap-4 md:grid-cols-3">
  <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-soft">
    <div class="text-xs uppercase tracking-wide text-slate-500">Total Poli</div>
    <div class="mt-2 text-3xl font-extrabold text-slate-900"><?= (int)$summary['total'] ?></div>
  </div>
  <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-emerald-700">Poli Aktif</div>
    <div class="mt-2 text-3xl font-extrabold text-emerald-900"><?= (int)$summary['active'] ?></div>
  </div>
  <div class="rounded-3xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-rose-700">Poli Nonaktif</div>
    <div class="mt-2 text-3xl font-extrabold text-rose-900"><?= (int)$summary['inactive'] ?></div>
  </div>
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Daftar Poli Cabang</h2>
      <p class="mt-1 text-sm text-slate-500">Perubahan data poli akan langsung memengaruhi pilihan poli pada pendaftaran dan antrian cabang aktif.</p>
    </div>
    <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Cabang aktif: <span class="font-semibold text-slate-800"><?= e($branch_name) ?></span></div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Nama Poli</th>
            <th class="px-4 py-3 font-semibold">Status Data</th>
            <th class="px-4 py-3 font-semibold">Status Antrian</th>
            <th class="px-4 py-3 font-semibold text-center">Antrian Hari Ini</th>
            <th class="px-4 py-3 font-semibold text-center">Total Kunjungan</th>
            <th class="px-4 py-3 font-semibold">Terakhir Diubah</th>
            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach ($clinics as $clinic): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($clinic['name']) ?></div>
                <div class="mt-1 text-xs text-slate-500">ID poli #<?= (int)$clinic['id'] ?></div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= (int)$clinic['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>">
                  <?= (int)$clinic['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td class="px-4 py-3"><?= clinic_state_badge($clinic['queue_state']) ?></td>
              <td class="px-4 py-3 text-center">
                <div class="font-bold text-slate-900"><?= (int)$clinic['queues_today'] ?></div>
                <?php if ((int)$clinic['open_queues_today'] > 0): ?>
                  <div class="text-xs text-orange-600"><?= (int)$clinic['open_queues_today'] ?> aktif</div>
                <?php else: ?>
                  <div class="text-xs text-slate-400">0 aktif</div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-center font-semibold text-slate-700"><?= (int)$clinic['visits_total'] ?></td>
              <td class="px-4 py-3 text-slate-600">
                <?= e(format_datetime_id($clinic['updated_at'] ?: $clinic['created_at'])) ?>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <a href="<?= site_url('clinics/update/'.$clinic['id']) ?>" data-clinic-edit='<?= e(json_encode($clinic)) ?>' class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-sky-200 hover:text-sky-700">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </a>

                  <?php if ((int)$clinic['is_active']): ?>
                    <form method="post" action="<?= site_url('clinics/delete/'.$clinic['id']) ?>" class="ajax-form" data-confirm="Nonaktifkan poli ini? Poli nonaktif tidak akan muncul pada pendaftaran dan antrian.">
                      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                      <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                        <i class="fa-solid fa-trash"></i> Hapus
                      </button>
                    </form>
                  <?php else: ?>
                    <form method="post" action="<?= site_url('clinics/restore/'.$clinic['id']) ?>" class="ajax-form">
                      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                      <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                        <i class="fa-solid fa-rotate-left"></i> Aktifkan
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($clinics)): ?>
            <tr>
              <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data poli untuk cabang ini.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<div id="clinicCreateModal" class="fixed inset-0 z-50 hidden bg-slate-950/50 p-4">
  <div class="mx-auto mt-10 max-w-2xl rounded-3xl bg-white p-6 shadow-2xl">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Tambah Poli Baru</h3>
        <p class="mt-1 text-sm text-slate-500">Data poli baru akan ditambahkan ke cabang aktif <span class="font-semibold text-slate-700"><?= e($branch_name) ?></span>.</p>
      </div>
      <button type="button" data-modal-close="clinicCreateModal" class="rounded-xl px-3 py-2 text-slate-500 hover:bg-slate-100"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <form method="post" action="<?= site_url('clinics/store') ?>" class="ajax-form space-y-4" data-close-modal="clinicCreateModal">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Nama poli</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="name" placeholder="Contoh: Poli Umum" required>
      </div>
      <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
        Aktifkan poli setelah disimpan
      </label>
      <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Poli aktif akan muncul pada halaman pendaftaran pasien dan menu antrian cabang aktif.</div>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Poli
      </button>
    </form>
  </div>
</div>

<div id="clinicEditModal" class="fixed inset-0 z-50 hidden bg-slate-950/50 p-4">
  <div class="mx-auto mt-10 max-w-2xl rounded-3xl bg-white p-6 shadow-2xl">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Edit Data Poli</h3>
        <p class="mt-1 text-sm text-slate-500">Perbarui nama atau status poli sesuai kebutuhan operasional.</p>
      </div>
      <button type="button" data-modal-close="clinicEditModal" class="rounded-xl px-3 py-2 text-slate-500 hover:bg-slate-100"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <form id="clinic-edit-form" method="post" action="#" class="ajax-form space-y-4" data-close-modal="clinicEditModal" data-reset-on-success="false">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Nama poli</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="name" placeholder="Nama poli" required>
      </div>
      <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
        Poli aktif
      </label>
      <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
          <div class="text-xs uppercase tracking-wide text-slate-500">Status Antrian Saat Ini</div>
          <div id="clinic-edit-queue-state" class="mt-2 font-semibold text-slate-800">-</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
          <div class="text-xs uppercase tracking-wide text-slate-500">Antrian Aktif Hari Ini</div>
          <div id="clinic-edit-open-queues" class="mt-2 font-semibold text-slate-800">0</div>
        </div>
      </div>
      <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">Jika poli masih memiliki antrian aktif hari ini, sistem akan menolak proses nonaktifkan demi menjaga alur pelayanan.</div>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700">
        <i class="fa-solid fa-floppy-disk"></i> Update Poli
      </button>
    </form>
  </div>
</div>

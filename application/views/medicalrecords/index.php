<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Rekam Medis Pasien</h1>
    <p class="mt-1 text-sm text-slate-500">Riwayat pemeriksaan, ringkasan kondisi khusus, dan monitoring kontrol rutin per pasien.</p>
  </div>
  <form method="get" action="<?= site_url('medicalrecords') ?>" class="flex w-full max-w-2xl flex-col gap-3 sm:flex-row">
    <input type="text" name="keyword" value="<?= e($keyword) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Cari nama pasien / RM / NIK / no. HP">
    <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
  </form>
</div>

<div class="mb-6 grid gap-4 md:grid-cols-3">
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Total pasien terdaftar</div>
    <div class="mt-2 text-3xl font-black text-slate-900"><?= count($patients) ?></div>
    <div class="mt-2 text-xs text-slate-500">Menampilkan hasil sesuai pencarian dan cabang aktif.</div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Pasien butuh monitoring</div>
    <?php $monitoringCount = 0; foreach ($patients as $row) { if (!empty($row['active_monitoring'])) { $monitoringCount++; } } ?>
    <div class="mt-2 text-3xl font-black text-slate-900"><?= $monitoringCount ?></div>
    <div class="mt-2 text-xs text-slate-500">Program kontrol aktif bisa dipantau dari halaman detail pasien.</div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Pasien dengan kunjungan tersimpan</div>
    <?php $visitedCount = 0; foreach ($patients as $row) { if (!empty($row['total_visits'])) { $visitedCount++; } } ?>
    <div class="mt-2 text-3xl font-black text-slate-900"><?= $visitedCount ?></div>
    <div class="mt-2 text-xs text-slate-500">Rekam medis longitudinal tersedia untuk kunjungan yang sudah tercatat.</div>
  </div>
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Daftar pasien untuk ditinjau</h2>
      <p class="mt-1 text-sm text-slate-500">Gunakan halaman detail untuk membuka riwayat lengkap, menandai alergi, dan membuat program kontrol.</p>
    </div>
    <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Total <?= count($patients) ?> pasien</div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Pasien</th>
            <th class="px-4 py-3 font-semibold">No RM</th>
            <th class="px-4 py-3 font-semibold">Kontak</th>
            <th class="px-4 py-3 font-semibold">Kunjungan Terakhir</th>
            <th class="px-4 py-3 font-semibold">Diagnosis Terakhir</th>
            <th class="px-4 py-3 font-semibold">Monitoring</th>
            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($patients as $patient): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($patient['name']) ?></div>
                <div class="text-xs text-slate-500"><?= e(gender_label($patient['gender'])) ?> · <?= e(patient_age_label($patient['birth_date'])) ?></div>
              </td>
              <td class="px-4 py-3 font-mono text-xs font-semibold text-slate-700"><?= e($patient['medical_record_no']) ?></td>
              <td class="px-4 py-3 text-slate-700">
                <div><?= e($patient['phone'] ? format_phone($patient['phone']) : '-') ?></div>
                <div class="text-xs text-slate-500"><?= e($patient['nik'] ?: '-') ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700">
                <div><?= e(format_datetime_id($patient['last_visit_date'] ?? null)) ?></div>
                <div class="text-xs text-slate-500">Total <?= (int)($patient['total_visits'] ?? 0) ?> kunjungan</div>
              </td>
              <td class="px-4 py-3 text-slate-700">
                <div class="max-w-xs"><?= e($patient['last_diagnosis'] ?: '-') ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700">
                <?php if (!empty($patient['active_monitoring'])): ?>
                  <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700"><?= (int)$patient['active_monitoring'] ?> program aktif</div>
                  <div class="mt-1 text-xs text-slate-500">Kontrol terdekat <?= e(format_date_id($patient['nearest_control_date'] ?? null)) ?></div>
                <?php else: ?>
                  <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Belum ada</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-right">
                <a href="<?= site_url('medicalrecords/show/'.$patient['id']) ?>" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-sky-700">
                  <i class="fa-solid fa-notes-medical"></i> Buka Rekam Medis
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($patients)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">Tidak ada pasien yang cocok dengan pencarian.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

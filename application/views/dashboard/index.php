<?php
$renderCard = function($label, $value, $icon, $tone) {
?>
  <div class="overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-200">
    <div class="flex items-center justify-between p-5">
      <div>
        <div class="text-sm font-medium text-slate-500"><?= e($label) ?></div>
        <div class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900"><?= $value ?></div>
      </div>
      <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br <?= e($tone) ?> text-xl text-white shadow-lg">
        <i class="<?= e($icon) ?>"></i>
      </div>
    </div>
  </div>
<?php
};

$emptyState = function($message) {
?>
  <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500"><?= e($message) ?></div>
<?php
};

$qtyLabel = function($value) {
    $formatted = number_format((float)$value, 2, ',', '.');
    return preg_replace('/,00$/', '', $formatted);
};

$incomingMovementTypes = ['opening', 'purchase', 'adjustment_in', 'transfer_in', 'return_in'];
?>
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900"><?= e($dashboardTitle ?? 'Dashboard') ?></h1>
    <p class="mt-1 text-sm text-slate-500"><?= e($dashboardDescription ?? '') ?></p>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500 shadow-soft">
    <div class="font-semibold text-slate-700">Waktu server</div>
    <div><?= e(format_datetime_id(now())) ?></div>
  </div>
</div>

<?php if (($dashboardType ?? 'admin') === 'admin'): ?>
  <?php
  $cards = [
      ['label' => 'Pasien Hari Ini', 'value' => (int)($stats['patients_today'] ?? 0), 'icon' => 'fa-solid fa-hospital-user', 'tone' => 'from-sky-500 to-cyan-400'],
      ['label' => 'Antrian Menunggu', 'value' => (int)($stats['queue_waiting'] ?? 0), 'icon' => 'fa-solid fa-users-line', 'tone' => 'from-amber-500 to-orange-400'],
      ['label' => 'Resep Diproses', 'value' => (int)($stats['prescriptions_waiting'] ?? 0), 'icon' => 'fa-solid fa-pills', 'tone' => 'from-violet-500 to-fuchsia-400'],
      ['label' => 'Pendapatan Hari Ini', 'value' => currency($stats['revenue_today'] ?? 0), 'icon' => 'fa-solid fa-wallet', 'tone' => 'from-emerald-500 to-green-400'],
  ];
  ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php foreach ($cards as $card) { $renderCard($card['label'], $card['value'], $card['icon'], $card['tone']); } ?>
  </div>

  <?php if (!empty($branch_stats)): ?>
  <div class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900">Performa Seluruh Cabang</h2>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200">
      <div class="overflow-x-auto scrollbar-soft">
        <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-3 font-semibold">Cabang</th>
              <th class="px-4 py-3 font-semibold">Kota</th>
              <th class="px-4 py-3 font-semibold">Kunjungan Hari Ini</th>
              <th class="px-4 py-3 font-semibold">Pendapatan Hari Ini</th>
              <th class="px-4 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach ($branch_stats as $branch): ?>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-slate-800"><?= e($branch['name']) ?></td>
                <td class="px-4 py-3 text-slate-600"><?= e($branch['city']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($branch['visits_today']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= currency($branch['revenue_today']) ?></td>
                <td class="px-4 py-3">
                  <a href="<?= site_url('branches/switch/'.$branch['id']) ?>" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-sky-700">
                    <i class="fa-solid fa-location-crosshairs"></i> Aktifkan
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Pendapatan vs Pengeluaran</h2>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 p-4">
          <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700"><i class="fa-solid fa-money-bill-trend-up text-emerald-600"></i> Pendapatan</div>
          <div class="space-y-3">
            <?php foreach (($finance ?? []) as $row): ?>
              <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                <span class="text-sm text-slate-600"><?= e(format_date_id($row['paid_date'])) ?></span>
                <span class="text-sm font-semibold text-slate-900"><?= currency($row['total']) ?></span>
              </div>
            <?php endforeach; ?>
            <?php if (empty($finance)) $emptyState('Belum ada pembayaran.'); ?>
          </div>
        </div>
        <div class="rounded-2xl border border-slate-200 p-4">
          <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700"><i class="fa-solid fa-file-invoice-dollar text-rose-600"></i> Pengeluaran</div>
          <div class="space-y-3">
            <?php foreach (($expenses ?? []) as $row): ?>
              <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                <span class="text-sm text-slate-600"><?= e(format_date_id($row['expense_day'])) ?></span>
                <span class="text-sm font-semibold text-slate-900"><?= currency($row['total']) ?></span>
              </div>
            <?php endforeach; ?>
            <?php if (empty($expenses)) $emptyState('Belum ada pengeluaran.'); ?>
          </div>
        </div>
      </div>
    </section>

    <section class="space-y-6">
      <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
        <h2 class="text-lg font-bold text-slate-900">Alert Stok Minimum</h2>
        <div class="mt-4 space-y-3">
          <?php foreach (($alerts ?? []) as $row): ?>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
              <div class="font-semibold text-slate-900"><?= e($row['name']) ?></div>
              <div class="mt-1 text-sm text-slate-600">Stok <strong><?= e($qtyLabel($row['stock'])) ?></strong> · Minimum <?= e($qtyLabel($row['min_stock'])) ?></div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($alerts)) $emptyState('Tidak ada alert stok.'); ?>
        </div>
      </div>

      <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
        <h2 class="text-lg font-bold text-slate-900">Kunjungan per Poli</h2>
        <div class="mt-4 space-y-3">
          <?php foreach (($visits_by_clinic ?? []) as $row): ?>
            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="font-medium text-slate-700"><?= e($row['clinic_name']) ?></span>
                <span class="font-semibold text-slate-900"><?= e($row['total']) ?> pasien</span>
              </div>
              <div class="h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-gradient-to-r from-brand-500 to-cyan-400" style="width: <?= max(8, min(100, ((int)$row['total'] * 12))) ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($visits_by_clinic)) $emptyState('Belum ada kunjungan hari ini.'); ?>
        </div>
      </div>
    </section>
  </div>

<?php elseif ($dashboardType === 'front_office'): ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
      $renderCard('Registrasi Hari Ini', (int)($stats['registrations_today'] ?? 0), 'fa-solid fa-user-plus', 'from-sky-500 to-cyan-400');
      $renderCard('Antrian Menunggu', (int)($stats['queue_waiting'] ?? 0), 'fa-solid fa-users', 'from-amber-500 to-orange-400');
      $renderCard('Sedang Dipanggil', (int)($stats['queue_called'] ?? 0), 'fa-solid fa-bullhorn', 'from-indigo-500 to-violet-400');
      $renderCard('Kunjungan Selesai', (int)($stats['visits_completed_today'] ?? 0), 'fa-solid fa-circle-check', 'from-emerald-500 to-green-400');
    ?>
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Registrasi Pasien Terbaru</h2>
        <a href="<?= site_url('patients') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Buka Data Pasien
        </a>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">Pasien</th>
                <th class="px-4 py-3 font-semibold">No. RM</th>
                <th class="px-4 py-3 font-semibold">Telepon</th>
                <th class="px-4 py-3 font-semibold">Terdaftar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach (($recentRegistrations ?? []) as $patient): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-semibold text-slate-800"><?= e($patient['name']) ?></td>
                  <td class="px-4 py-3 text-slate-600"><?= e($patient['medical_record_no']) ?></td>
                  <td class="px-4 py-3 text-slate-600"><?= e(format_phone($patient['phone'] ?? '')) ?></td>
                  <td class="px-4 py-3 text-slate-600"><?= e(format_datetime_id($patient['created_at'] ?? $patient['updated_at'] ?? null)) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (empty($recentRegistrations)) $emptyState('Belum ada registrasi pasien pada cabang ini.'); ?>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Ringkasan Antrian per Poli</h2>
        <a href="<?= site_url('queues') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Buka Antrian
        </a>
      </div>
      <div class="space-y-3">
        <?php foreach (($queueByClinic ?? []) as $row): ?>
          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="font-semibold text-slate-900"><?= e($row['clinic_name']) ?></div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
              <div class="rounded-xl bg-amber-50 px-3 py-2 text-amber-700">Menunggu <strong><?= (int)$row['waiting_total'] ?></strong></div>
              <div class="rounded-xl bg-sky-50 px-3 py-2 text-sky-700">Dipanggil <strong><?= (int)$row['called_total'] ?></strong></div>
              <div class="rounded-xl bg-violet-50 px-3 py-2 text-violet-700">Diperiksa <strong><?= (int)$row['examined_total'] ?></strong></div>
              <div class="rounded-xl bg-slate-100 px-3 py-2 text-slate-700">Pending <strong><?= (int)$row['pending_total'] ?></strong></div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($queueByClinic)) $emptyState('Belum ada poli aktif atau antrian hari ini.'); ?>
      </div>
    </section>
  </div>

  <section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900">Kontrol Rutin dalam 7 Hari</h2>
      <a href="<?= site_url('medicalrecords') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
        <i class="fa-solid fa-arrow-right"></i> Buka Rekam Medis
      </a>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <?php foreach (($upcomingControls ?? []) as $item): ?>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-semibold uppercase tracking-wide text-brand-600"><?= e(format_date_id($item['next_control_date'])) ?></div>
          <div class="mt-2 font-bold text-slate-900"><?= e($item['patient_name']) ?></div>
          <div class="text-sm text-slate-500">RM <?= e($item['medical_record_no']) ?></div>
          <div class="mt-2 text-sm text-slate-600"><?= e($item['program_name']) ?></div>
          <div class="mt-1 text-xs text-slate-500"><?= e($item['clinic_name'] ?: '-') ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($upcomingControls)) $emptyState('Tidak ada pasien kontrol terjadwal dalam 7 hari ke depan.'); ?>
  </section>

<?php elseif ($dashboardType === 'doctor'): ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
      $renderCard('Pasien Dipanggil', (int)($stats['queue_called'] ?? 0), 'fa-solid fa-bullhorn', 'from-sky-500 to-cyan-400');
      $renderCard('Sedang Diperiksa', (int)($stats['queue_examined'] ?? 0), 'fa-solid fa-stethoscope', 'from-violet-500 to-fuchsia-400');
      $renderCard('Pemeriksaan Selesai Saya', (int)($stats['completed_today'] ?? 0), 'fa-solid fa-user-doctor', 'from-emerald-500 to-green-400');
      $renderCard('Kontrol 7 Hari', (int)($stats['followup_due'] ?? 0), 'fa-solid fa-calendar-check', 'from-amber-500 to-orange-400');
    ?>
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Pasien untuk Ditangani Hari Ini</h2>
        <a href="<?= site_url('queues') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Buka Antrian
        </a>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">Antrian</th>
                <th class="px-4 py-3 font-semibold">Pasien</th>
                <th class="px-4 py-3 font-semibold">Poli</th>
                <th class="px-4 py-3 font-semibold">Status</th>
                <th class="px-4 py-3 font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach (($openClinicalVisits ?? []) as $visit): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-semibold text-slate-800"><?= e($visit['queue_number'] ?: '-') ?></td>
                  <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800"><?= e($visit['patient_name']) ?></div>
                    <div class="text-xs text-slate-500">RM <?= e($visit['medical_record_no']) ?></div>
                  </td>
                  <td class="px-4 py-3 text-slate-600"><?= e($visit['clinic_name'] ?: '-') ?></td>
                  <td class="px-4 py-3"><?= status_badge($visit['visit_status']) ?></td>
                  <td class="px-4 py-3">
                    <a href="<?= ($visit['visit_status'] === 'examined') ? site_url('visits/show/'.$visit['visit_id']) : site_url('queues') ?>" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-sky-700">
                      <i class="fa-solid fa-arrow-right"></i> <?= $visit['visit_status'] === 'examined' ? 'Buka Pemeriksaan' : 'Lihat Antrian' ?>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (empty($openClinicalVisits)) $emptyState('Tidak ada pasien aktif yang menunggu pemeriksaan saat ini.'); ?>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Kontrol Mendatang</h2>
        <a href="<?= site_url('medicalrecords') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Rekam Medis
        </a>
      </div>
      <div class="space-y-3">
        <?php foreach (($upcomingControls ?? []) as $item): ?>
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-brand-600"><?= e(format_date_id($item['next_control_date'])) ?></div>
            <div class="mt-1 font-semibold text-slate-900"><?= e($item['patient_name']) ?></div>
            <div class="text-sm text-slate-500">RM <?= e($item['medical_record_no']) ?> · <?= e($item['clinic_name'] ?: '-') ?></div>
            <div class="mt-2 text-sm text-slate-600"><?= e($item['program_name']) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($upcomingControls)) $emptyState('Tidak ada jadwal kontrol dalam 7 hari ke depan.'); ?>
      </div>
    </section>
  </div>

  <section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900">Riwayat Pemeriksaan Terbaru</h2>
      <a href="<?= site_url('visits') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
        <i class="fa-solid fa-arrow-right"></i> Daftar Pemeriksaan
      </a>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <?php foreach (($recentClinicalVisits ?? []) as $visit): ?>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-semibold uppercase tracking-wide text-brand-600"><?= e(format_date_id($visit['visit_date'])) ?></div>
          <div class="mt-2 font-bold text-slate-900"><?= e($visit['patient_name']) ?></div>
          <div class="text-sm text-slate-500">RM <?= e($visit['medical_record_no']) ?></div>
          <div class="mt-2 text-sm text-slate-600"><?= e($visit['clinic_name'] ?: '-') ?></div>
          <div class="mt-1 text-sm text-slate-500"><?= e($visit['diagnosis_name'] ?: 'Belum ada diagnosis utama') ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($recentClinicalVisits)) $emptyState('Belum ada riwayat pemeriksaan selesai yang dapat ditampilkan.'); ?>
  </section>

<?php elseif ($dashboardType === 'nurse'): ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
      $renderCard('Pasien Dipanggil', (int)($stats['queue_called'] ?? 0), 'fa-solid fa-bullhorn', 'from-sky-500 to-cyan-400');
      $renderCard('Sedang Diperiksa', (int)($stats['queue_examined'] ?? 0), 'fa-solid fa-user-nurse', 'from-violet-500 to-fuchsia-400');
      $renderCard('Vital Sign Terisi', (int)($stats['vitals_filled_today'] ?? 0), 'fa-solid fa-heart-pulse', 'from-emerald-500 to-green-400');
      $renderCard('Kontrol 7 Hari', (int)($stats['followup_due'] ?? 0), 'fa-solid fa-calendar-check', 'from-amber-500 to-orange-400');
    ?>
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Antrian Klinis Hari Ini</h2>
        <a href="<?= site_url('queues') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Kelola Antrian
        </a>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">Antrian</th>
                <th class="px-4 py-3 font-semibold">Pasien</th>
                <th class="px-4 py-3 font-semibold">Poli</th>
                <th class="px-4 py-3 font-semibold">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach (($openClinicalVisits ?? []) as $visit): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-semibold text-slate-800"><?= e($visit['queue_number'] ?: '-') ?></td>
                  <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800"><?= e($visit['patient_name']) ?></div>
                    <div class="text-xs text-slate-500">RM <?= e($visit['medical_record_no']) ?></div>
                  </td>
                  <td class="px-4 py-3 text-slate-600"><?= e($visit['clinic_name'] ?: '-') ?></td>
                  <td class="px-4 py-3"><?= status_badge($visit['visit_status']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (empty($openClinicalVisits)) $emptyState('Tidak ada antrian klinis aktif untuk saat ini.'); ?>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Kontrol Mendatang</h2>
        <a href="<?= site_url('medicalrecords') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Rekam Medis
        </a>
      </div>
      <div class="space-y-3">
        <?php foreach (($upcomingControls ?? []) as $item): ?>
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-brand-600"><?= e(format_date_id($item['next_control_date'])) ?></div>
            <div class="mt-1 font-semibold text-slate-900"><?= e($item['patient_name']) ?></div>
            <div class="text-sm text-slate-500">RM <?= e($item['medical_record_no']) ?></div>
            <div class="mt-2 text-sm text-slate-600"><?= e($item['program_name']) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($upcomingControls)) $emptyState('Tidak ada kontrol rutin terdekat.'); ?>
      </div>
    </section>
  </div>

  <section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900">Vital Sign Terbaru</h2>
      <a href="<?= site_url('visits') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
        <i class="fa-solid fa-arrow-right"></i> Buka Pemeriksaan
      </a>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <?php foreach (($recentVitals ?? []) as $vital): ?>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="font-bold text-slate-900"><?= e($vital['patient_name']) ?></div>
          <div class="text-sm text-slate-500">RM <?= e($vital['medical_record_no']) ?> · <?= e($vital['clinic_name'] ?: '-') ?></div>
          <div class="mt-3 space-y-1 text-sm text-slate-600">
            <div>TD: <?= e($vital['blood_pressure'] ?: '-') ?></div>
            <div>Suhu: <?= e($vital['temperature'] ?: '-') ?></div>
            <div>Nadi: <?= e($vital['pulse'] ?: '-') ?></div>
            <div>BB/TB: <?= e($vital['weight'] ?: '-') ?> / <?= e($vital['height'] ?: '-') ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($recentVitals)) $emptyState('Belum ada data vital sign yang tersimpan.'); ?>
  </section>

<?php elseif ($dashboardType === 'pharmacist'): ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
      $renderCard('Resep Menunggu', (int)($stats['draft_total'] ?? 0), 'fa-solid fa-prescription-bottle-medical', 'from-sky-500 to-cyan-400');
      $renderCard('Sedang Disiapkan', (int)($stats['prepared_total'] ?? 0), 'fa-solid fa-mortar-pestle', 'from-violet-500 to-fuchsia-400');
      $renderCard('Sudah Diserahkan', (int)($stats['dispensed_today'] ?? 0), 'fa-solid fa-hand-holding-medical', 'from-emerald-500 to-green-400');
      $renderCard('Alert Stok', (int)($stats['low_stock_total'] ?? 0), 'fa-solid fa-triangle-exclamation', 'from-amber-500 to-orange-400');
    ?>
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Resep yang Perlu Diproses</h2>
        <a href="<?= site_url('pharmacy') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Buka Farmasi
        </a>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">Pasien</th>
                <th class="px-4 py-3 font-semibold">Poli</th>
                <th class="px-4 py-3 font-semibold">Status</th>
                <th class="px-4 py-3 font-semibold">Jumlah Item</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach (($recentPrescriptions ?? []) as $row): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800"><?= e($row['patient_name']) ?></div>
                    <div class="text-xs text-slate-500">RM <?= e($row['medical_record_no']) ?></div>
                  </td>
                  <td class="px-4 py-3 text-slate-600"><?= e($row['clinic_name'] ?: '-') ?></td>
                  <td class="px-4 py-3"><?= status_badge($row['status']) ?></td>
                  <td class="px-4 py-3 text-slate-600"><?= (int)$row['item_count'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (empty($recentPrescriptions)) $emptyState('Belum ada resep pada cabang aktif.'); ?>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Obat Stok Minimum</h2>
        <a href="<?= site_url('inventory') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Inventory
        </a>
      </div>
      <div class="space-y-3">
        <?php foreach (($lowStockItems ?? []) as $item): ?>
          <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <div class="font-semibold text-slate-900"><?= e($item['name']) ?></div>
            <div class="mt-1 text-sm text-slate-600">Stok <?= e($qtyLabel($item['stock'])) ?> / Minimum <?= e($qtyLabel($item['min_stock'])) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($lowStockItems)) $emptyState('Tidak ada item yang berada di bawah batas minimum.'); ?>
      </div>
    </section>
  </div>

  <section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900">Obat Paling Banyak Keluar Hari Ini</h2>
      <a href="<?= site_url('pharmacy') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
        <i class="fa-solid fa-arrow-right"></i> Detail Farmasi
      </a>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
      <?php foreach (($topMedicines ?? []) as $item): ?>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="font-bold text-slate-900"><?= e($item['name']) ?></div>
          <div class="mt-2 text-sm text-slate-600">Total keluar <strong><?= e($qtyLabel($item['total_qty'])) ?></strong></div>
          <div class="mt-1 text-sm text-slate-500"><?= (int)$item['prescription_count'] ?> resep</div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($topMedicines)) $emptyState('Belum ada obat yang diserahkan hari ini.'); ?>
  </section>

<?php elseif ($dashboardType === 'cashier'): ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
      $renderCard('Tagihan Siap Proses', (int)($stats['ready_total'] ?? 0), 'fa-solid fa-file-invoice', 'from-sky-500 to-cyan-400');
      $renderCard('Transaksi Hari Ini', (int)($stats['transactions_today'] ?? 0), 'fa-solid fa-receipt', 'from-violet-500 to-fuchsia-400');
      $renderCard('Pendapatan Hari Ini', currency($stats['revenue_today'] ?? 0), 'fa-solid fa-wallet', 'from-emerald-500 to-green-400');
      $renderCard('Rata-rata Transaksi', currency($stats['avg_transaction'] ?? 0), 'fa-solid fa-chart-column', 'from-amber-500 to-orange-400');
    ?>
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Tagihan Siap Ditagih</h2>
        <a href="<?= site_url('billing') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Buka Kasir
        </a>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">Pasien</th>
                <th class="px-4 py-3 font-semibold">Poli</th>
                <th class="px-4 py-3 font-semibold">Invoice</th>
                <th class="px-4 py-3 font-semibold">Tagihan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach (($readyInvoices ?? []) as $row): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800"><?= e($row['patient_name']) ?></div>
                    <div class="text-xs text-slate-500">RM <?= e($row['medical_record_no']) ?></div>
                  </td>
                  <td class="px-4 py-3 text-slate-600"><?= e($row['clinic_name'] ?: '-') ?></td>
                  <td class="px-4 py-3 text-slate-600"><?= e($row['invoice_no'] ?: 'Belum dibuat') ?></td>
                  <td class="px-4 py-3 text-slate-700 font-semibold"><?= $row['invoice_id'] ? currency($row['grand_total']) : 'Siapkan invoice' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (empty($readyInvoices)) $emptyState('Tidak ada tagihan yang menunggu proses kasir.'); ?>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Metode Pembayaran Hari Ini</h2>
        <a href="<?= site_url('billing') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Detail
        </a>
      </div>
      <div class="space-y-3">
        <?php foreach (($paymentMethods ?? []) as $method): ?>
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-semibold text-slate-900"><?= e(ucfirst($method['payment_method'])) ?></div>
                <div class="text-sm text-slate-500"><?= (int)$method['transaction_count'] ?> transaksi</div>
              </div>
              <div class="text-sm font-semibold text-slate-900"><?= currency($method['total_amount']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($paymentMethods)) $emptyState('Belum ada transaksi yang dicatat hari ini.'); ?>
      </div>
    </section>
  </div>

  <section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900">Pembayaran Terbaru</h2>
      <a href="<?= site_url('billing') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
        <i class="fa-solid fa-arrow-right"></i> Buka Kasir
      </a>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <?php foreach (($recentPayments ?? []) as $payment): ?>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-semibold uppercase tracking-wide text-brand-600"><?= e(format_datetime_id($payment['paid_at'])) ?></div>
          <div class="mt-2 font-bold text-slate-900"><?= e($payment['patient_name']) ?></div>
          <div class="text-sm text-slate-500">RM <?= e($payment['medical_record_no']) ?> · <?= e($payment['clinic_name'] ?: '-') ?></div>
          <div class="mt-3 text-lg font-extrabold text-slate-900"><?= currency($payment['amount']) ?></div>
          <div class="text-sm text-slate-500"><?= e(ucfirst($payment['payment_method'])) ?> · <?= e($payment['invoice_no']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($recentPayments)) $emptyState('Belum ada pembayaran yang tercatat.'); ?>
  </section>

<?php elseif ($dashboardType === 'inventory'): ?>
  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
      $renderCard('Obat Aktif', (int)($stats['medicine_total'] ?? 0), 'fa-solid fa-capsules', 'from-sky-500 to-cyan-400');
      $renderCard('Alert Stok', (int)($stats['low_stock_total'] ?? 0), 'fa-solid fa-triangle-exclamation', 'from-amber-500 to-orange-400');
      $renderCard('Stok Masuk Hari Ini', e($qtyLabel($stats['stock_in_today'] ?? 0)), 'fa-solid fa-arrow-right-to-bracket', 'from-emerald-500 to-green-400');
      $renderCard('Stok Keluar Hari Ini', e($qtyLabel($stats['stock_out_today'] ?? 0)), 'fa-solid fa-arrow-right-from-bracket', 'from-violet-500 to-fuchsia-400');
    ?>
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Item Perlu Restok</h2>
        <a href="<?= site_url('inventory') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Inventory
        </a>
      </div>
      <div class="space-y-3">
        <?php foreach (($lowStockItems ?? []) as $item): ?>
          <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <div class="font-semibold text-slate-900"><?= e($item['name']) ?></div>
            <div class="mt-1 text-sm text-slate-600">Stok <?= e($qtyLabel($item['stock'])) ?> <?= e($item['unit']) ?> · Minimum <?= e($qtyLabel($item['min_stock'])) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($lowStockItems)) $emptyState('Tidak ada item yang perlu restok saat ini.'); ?>
      </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Mutasi Stok Terbaru</h2>
        <a href="<?= site_url('inventory') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700">
          <i class="fa-solid fa-arrow-right"></i> Kelola Stok
        </a>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">Waktu</th>
                <th class="px-4 py-3 font-semibold">Obat</th>
                <th class="px-4 py-3 font-semibold">Jenis</th>
                <th class="px-4 py-3 font-semibold">Qty</th>
                <th class="px-4 py-3 font-semibold">Nilai</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach (($recentStockMovements ?? []) as $move): ?>
                <?php $isIncoming = in_array($move['movement_type'], $incomingMovementTypes, true); ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 text-slate-600"><?= e(format_datetime_id($move['created_at'])) ?></td>
                  <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800"><?= e($move['medicine_name']) ?></div>
                    <div class="text-xs text-slate-500"><?= e($move['unit']) ?></div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold <?= $isIncoming ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700' ?>">
                      <?= e(str_replace('_', ' ', $move['movement_type'])) ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 font-semibold <?= $isIncoming ? 'text-emerald-700' : 'text-violet-700' ?>"><?= $isIncoming ? '+' : '-' ?><?= e($qtyLabel($move['qty'])) ?></td>
                  <td class="px-4 py-3 text-slate-700"><?= currency(((float)$move['qty']) * ((float)$move['unit_cost'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php if (empty($recentStockMovements)) $emptyState('Belum ada mutasi stok yang tercatat.'); ?>
    </section>
  </div>
<?php endif; ?>

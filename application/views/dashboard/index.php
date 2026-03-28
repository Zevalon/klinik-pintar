<?php
$cards = [
    ['label' => 'Pasien Hari Ini', 'value' => $stats['patients_today'], 'icon' => 'fa-solid fa-hospital-user', 'tone' => 'from-sky-500 to-cyan-400'],
    ['label' => 'Antrian Menunggu', 'value' => $stats['queue_waiting'], 'icon' => 'fa-solid fa-users-line', 'tone' => 'from-amber-500 to-orange-400'],
    ['label' => 'Resep Diproses', 'value' => $stats['prescriptions_waiting'], 'icon' => 'fa-solid fa-pills', 'tone' => 'from-violet-500 to-fuchsia-400'],
    ['label' => 'Pendapatan Hari Ini', 'value' => currency($stats['revenue_today']), 'icon' => 'fa-solid fa-wallet', 'tone' => 'from-emerald-500 to-green-400'],
];
?>
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard Klinik Pintar</h1>
    
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500 shadow-soft">
    <div class="font-semibold text-slate-700">Waktu server</div>
    <div><?= e(format_datetime_id(now())) ?></div>
  </div>
</div>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
  <?php foreach($cards as $card): ?>
    <div class="overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-200">
      <div class="flex items-center justify-between p-5">
        <div>
          <div class="text-sm font-medium text-slate-500"><?= e($card['label']) ?></div>
          <div class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900"><?= e($card['value']) ?></div>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br <?= e($card['tone']) ?> text-xl text-white shadow-lg">
          <i class="<?= e($card['icon']) ?>"></i>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if(!empty($branch_stats)): ?>
<div class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Performa Seluruh Cabang</h2>
      
    </div>
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
          <?php foreach($branch_stats as $branch): ?>
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
      <div>
        <h2 class="text-lg font-bold text-slate-900">Pendapatan vs Pengeluaran</h2>
        
      </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
      <div class="rounded-2xl border border-slate-200 p-4">
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700"><i class="fa-solid fa-money-bill-trend-up text-emerald-600"></i> Pendapatan</div>
        <div class="space-y-3">
          <?php foreach($finance as $row): ?>
            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
              <span class="text-sm text-slate-600"><?= e(format_date_id($row['paid_date'])) ?></span>
              <span class="text-sm font-semibold text-slate-900"><?= currency($row['total']) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if(empty($finance)): ?><div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">Belum ada pembayaran.</div><?php endif; ?>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 p-4">
        <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700"><i class="fa-solid fa-file-invoice-dollar text-rose-600"></i> Pengeluaran</div>
        <div class="space-y-3">
          <?php foreach($expenses as $row): ?>
            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
              <span class="text-sm text-slate-600"><?= e(format_date_id($row['expense_day'])) ?></span>
              <span class="text-sm font-semibold text-slate-900"><?= currency($row['total']) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if(empty($expenses)): ?><div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">Belum ada pengeluaran.</div><?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="space-y-6">
    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <h2 class="text-lg font-bold text-slate-900">Alert Stok Minimum</h2>
      
      <div class="space-y-3">
        <?php foreach($alerts as $row): ?>
          <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <div class="font-semibold text-slate-900"><?= e($row['name']) ?></div>
            <div class="mt-1 text-sm text-slate-600">Stok <strong><?= e($row['stock']) ?></strong> · Minimum <?= e($row['min_stock']) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if(empty($alerts)): ?><div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">Tidak ada alert stok.</div><?php endif; ?>
      </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <h2 class="text-lg font-bold text-slate-900">Kunjungan per Poli</h2>
      
      <div class="space-y-3">
        <?php foreach($visits_by_clinic as $row): ?>
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
        <?php if(empty($visits_by_clinic)): ?><div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">Belum ada kunjungan hari ini.</div><?php endif; ?>
      </div>
    </div>
  </section>
</div>

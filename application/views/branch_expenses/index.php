<?php
$totalExpense = 0;
foreach ($expenseRows as $row) { $totalExpense += (float)$row['total_amount']; }
$groupOptions = ['day' => 'Harian', 'month' => 'Bulanan', 'year' => 'Tahunan'];
?>
<div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Pengeluaran Cabang</h1>
    
  </div>
  <a href="<?= site_url('branchexpenses/exportPdf?start=' . urlencode($filters['start']) . '&end=' . urlencode($filters['end']) . '&group=' . urlencode($filters['group'])) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-slate-800">
    <i class="fa-solid fa-file-pdf"></i> Unduh PDF
  </a>
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
    <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-rose-50 via-white to-white p-5">
      <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-rose-100 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-rose-700">
        <i class="fa-solid fa-wallet"></i> Input Baru
      </div>
      <h2 class="text-xl font-bold text-slate-900">Input Pengeluaran</h2>
      
      <form method="post" action="<?= site_url('branchexpenses/store') ?>" class="ajax-form mt-5 space-y-4">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal pengeluaran</label>
          <input type="date" name="expense_date" value="<?= e(today()) ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</label>
          <input name="category" value="operasional" placeholder="Kategori pengeluaran" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Deskripsi</label>
          <textarea name="description" placeholder="Deskripsi pengeluaran" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required></textarea>
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nominal</label>
          <input name="amount" placeholder="Nominal pengeluaran" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
        </div>
        <button class="inline-flex items-center gap-2 rounded-2xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-rose-700"><i class="fa-solid fa-plus"></i> Simpan Pengeluaran</button>
      </form>
    </div>

    <div class="space-y-6">
      <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-sky-700">
              <i class="fa-solid fa-chart-column"></i> Rekap pengeluaran
            </div>
            <h2 class="mt-3 text-xl font-bold text-slate-900">Filter & Ringkasan</h2>
            
          </div>
          <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 lg:min-w-[240px]">
            <div class="text-sm font-medium text-rose-700">Total Pengeluaran</div>
            <div class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900"><?= currency($totalExpense) ?></div>
          </div>
        </div>

        <form method="get" class="mt-5 grid gap-4 rounded-3xl border border-slate-200 bg-white p-4 lg:grid-cols-4">
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mulai</label>
            <input type="date" name="start" value="<?= e($filters['start']) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Sampai</label>
            <input type="date" name="end" value="<?= e($filters['end']) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mode laporan</label>
            <select name="group" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
              <?php foreach($groupOptions as $key => $label): ?>
                <option value="<?= $key ?>" <?= $filters['group'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="flex items-end">
            <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
          </div>
        </form>
      </div>

      <div class="grid gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
          <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="text-lg font-bold text-slate-900">Rekap Periode</h3>
            
          </div>
          <div class="overflow-x-auto scrollbar-soft">
            <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
              <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                  <th class="px-4 py-3 font-semibold">Periode</th>
                  <th class="px-4 py-3 font-semibold">Jumlah Transaksi</th>
                  <th class="px-4 py-3 font-semibold">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach($expenseRows as $row): ?>
                  <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-700"><?= e($row['period_label']) ?></td>
                    <td class="px-4 py-3 text-slate-700"><?= e($row['transaction_count']) ?></td>
                    <td class="px-4 py-3 font-semibold text-slate-900"><?= currency($row['total_amount']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if(empty($expenseRows)): ?><tr><td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data pengeluaran pada rentang ini.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
          <div class="border-b border-slate-200 px-5 py-4">
            <h3 class="text-lg font-bold text-slate-900">Detail Transaksi</h3>
            
          </div>
          <div class="overflow-x-auto scrollbar-soft">
            <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
              <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                  <th class="px-4 py-3 font-semibold">Tanggal</th>
                  <th class="px-4 py-3 font-semibold">Kategori</th>
                  <th class="px-4 py-3 font-semibold">Deskripsi</th>
                  <th class="px-4 py-3 font-semibold">Dibuat Oleh</th>
                  <th class="px-4 py-3 font-semibold">Jumlah</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach($expenseItems as $row): ?>
                  <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-700"><?= e(format_date_id($row['expense_date'])) ?></td>
                    <td class="px-4 py-3 text-slate-700"><?= e($row['category']) ?></td>
                    <td class="px-4 py-3 text-slate-700"><?= e($row['description']) ?></td>
                    <td class="px-4 py-3 text-slate-700"><?= e($row['created_by_name'] ?: '-') ?></td>
                    <td class="px-4 py-3 font-semibold text-slate-900"><?= currency($row['amount']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if(empty($expenseItems)): ?><tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada detail pengeluaran pada rentang ini.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

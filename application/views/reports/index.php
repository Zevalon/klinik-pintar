<?php
$financeTotalIncome = 0;
foreach ($incomeRows as $row) { $financeTotalIncome += (float)$row['total_amount']; }
$financeTotalExpense = 0;
foreach ($expenseRows as $row) { $financeTotalExpense += (float)$row['total_amount']; }
$stockInQty = 0;
foreach ($stockInRows as $row) { $stockInQty += (float)$row['total_qty']; }
$stockOutQty = 0;
foreach ($stockOutRows as $row) { $stockOutQty += (float)$row['total_qty']; }
$groupOptions = ['day' => 'Harian', 'month' => 'Bulanan', 'year' => 'Tahunan'];
?>
<div class="mb-6">
  <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Laporan Keuangan & Stok</h1>
  
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-5 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Laporan Keuangan</h2>
      
    </div>
    <form method="get" class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-4 xl:min-w-[760px]">
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mulai</label>
        <input type="date" name="finance_start" value="<?= e($financeFilters['start']) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Sampai</label>
        <input type="date" name="finance_end" value="<?= e($financeFilters['end']) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mode laporan</label>
        <select name="finance_group" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          <?php foreach($groupOptions as $key => $label): ?>
            <option value="<?= $key ?>" <?= $financeFilters['group'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-end">
        <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
      </div>
      <input type="hidden" name="stock_start" value="<?= e($stockFilters['start']) ?>">
      <input type="hidden" name="stock_end" value="<?= e($stockFilters['end']) ?>">
      <input type="hidden" name="stock_group" value="<?= e($stockFilters['group']) ?>">
    </form>
  </div>

  <div class="mb-6 grid gap-4 md:grid-cols-3">
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
      <div class="text-sm text-emerald-700">Total Pendapatan</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= currency($financeTotalIncome) ?></div>
    </div>
    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
      <div class="text-sm text-rose-700">Total Pengeluaran</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= currency($financeTotalExpense) ?></div>
    </div>
    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
      <div class="text-sm text-sky-700">Selisih</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= currency($financeTotalIncome - $financeTotalExpense) ?></div>
    </div>
  </div>

  <div class="grid gap-6 xl:grid-cols-2">
    <div class="rounded-3xl border border-slate-200 p-5">
      <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">Laporan Pendapatan <?= e($groupOptions[$financeFilters['group']]) ?></h3>
        
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-3 font-semibold">Periode</th>
              <th class="px-4 py-3 font-semibold">Jumlah Transaksi</th>
              <th class="px-4 py-3 font-semibold">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach($incomeRows as $row): ?>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-700"><?= e($row['period_label']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($row['transaction_count']) ?></td>
                <td class="px-4 py-3 font-semibold text-slate-900"><?= currency($row['total_amount']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if(empty($incomeRows)): ?><tr><td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data pendapatan pada rentang ini.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="rounded-3xl border border-slate-200 p-5">
      <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">Laporan Pengeluaran <?= e($groupOptions[$financeFilters['group']]) ?></h3>
        
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
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
  </div>

  <div class="mt-6 grid gap-6 xl:grid-cols-3">
    <div class="rounded-3xl border border-slate-200 p-5 xl:col-span-1">
      <h3 class="text-lg font-bold text-slate-900">Tambah Pengeluaran Klinik</h3>
      
      <form method="post" action="<?= site_url('reports/storeExpense') ?>" class="ajax-form mt-4 space-y-4">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <input type="date" name="expense_date" value="<?= e(today()) ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        <input name="category" value="operasional" placeholder="Kategori pengeluaran" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        <textarea name="description" placeholder="Deskripsi pengeluaran" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required></textarea>
        <input name="amount" placeholder="Nominal pengeluaran" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" required>
        <button class="inline-flex items-center gap-2 rounded-2xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-rose-700"><i class="fa-solid fa-plus"></i> Simpan Pengeluaran</button>
      </form>
    </div>

    <div class="rounded-3xl border border-slate-200 p-5 xl:col-span-2">
      <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">Detail Pengeluaran</h3>
        
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
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
</section>

<section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-5 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Laporan Stok</h2>
      
    </div>
    <form method="get" class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-4 xl:min-w-[760px]">
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mulai</label>
        <input type="date" name="stock_start" value="<?= e($stockFilters['start']) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Sampai</label>
        <input type="date" name="stock_end" value="<?= e($stockFilters['end']) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Mode laporan</label>
        <select name="stock_group" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          <?php foreach($groupOptions as $key => $label): ?>
            <option value="<?= $key ?>" <?= $stockFilters['group'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-end">
        <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
      </div>
      <input type="hidden" name="finance_start" value="<?= e($financeFilters['start']) ?>">
      <input type="hidden" name="finance_end" value="<?= e($financeFilters['end']) ?>">
      <input type="hidden" name="finance_group" value="<?= e($financeFilters['group']) ?>">
    </form>
  </div>

  <div class="mb-6 grid gap-4 md:grid-cols-3">
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
      <div class="text-sm text-emerald-700">Total Barang Masuk</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e(rtrim(rtrim(number_format($stockInQty, 2, ',', '.'), '0'), ',')) ?></div>
    </div>
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
      <div class="text-sm text-amber-700">Total Barang Keluar</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= e(rtrim(rtrim(number_format($stockOutQty, 2, ',', '.'), '0'), ',')) ?></div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
      <div class="text-sm text-slate-600">Alert Restock Aktif</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900"><?= count($alerts) ?></div>
    </div>
  </div>

  <div class="grid gap-6 xl:grid-cols-2">
    <div class="rounded-3xl border border-slate-200 p-5">
      <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">Laporan Barang Masuk <?= e($groupOptions[$stockFilters['group']]) ?></h3>
        
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-3 font-semibold">Periode</th>
              <th class="px-4 py-3 font-semibold">Jumlah Transaksi</th>
              <th class="px-4 py-3 font-semibold">Total Qty</th>
              <th class="px-4 py-3 font-semibold">Nilai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach($stockInRows as $row): ?>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-700"><?= e($row['period_label']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($row['transaction_count']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e(rtrim(rtrim(number_format((float)$row['total_qty'], 2, ',', '.'), '0'), ',')) ?></td>
                <td class="px-4 py-3 font-semibold text-slate-900"><?= currency($row['total_value']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if(empty($stockInRows)): ?><tr><td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data barang masuk pada rentang ini.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="rounded-3xl border border-slate-200 p-5">
      <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">Laporan Barang Keluar <?= e($groupOptions[$stockFilters['group']]) ?></h3>
        
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-3 font-semibold">Periode</th>
              <th class="px-4 py-3 font-semibold">Jumlah Transaksi</th>
              <th class="px-4 py-3 font-semibold">Total Qty</th>
              <th class="px-4 py-3 font-semibold">Nilai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach($stockOutRows as $row): ?>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-700"><?= e($row['period_label']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($row['transaction_count']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e(rtrim(rtrim(number_format((float)$row['total_qty'], 2, ',', '.'), '0'), ',')) ?></td>
                <td class="px-4 py-3 font-semibold text-slate-900"><?= currency($row['total_value']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if(empty($stockOutRows)): ?><tr><td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data barang keluar pada rentang ini.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="mb-6 flex items-start justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Kartu Stok Obat</h1>
    
  </div>
  <a href="<?= site_url('inventory') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-soft hover:border-brand-200 hover:text-brand-700"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<div class="mb-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="grid gap-4 md:grid-cols-3">
    <div>
      <div class="text-sm text-slate-500">Nama Obat</div>
      <div class="mt-1 text-xl font-bold text-slate-900"><?= e($medicine['name']) ?></div>
    </div>
    <div>
      <div class="text-sm text-slate-500">Stok Saat Ini</div>
      <div class="mt-1 text-xl font-bold text-slate-900"><?= e($stock) ?></div>
    </div>
    <div>
      <div class="text-sm text-slate-500">Satuan</div>
      <div class="mt-1 text-xl font-bold text-slate-900"><?= e($medicine['unit']) ?></div>
    </div>
  </div>
</div>

<div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Tanggal</th>
            <th class="px-4 py-3 font-semibold">Tipe Mutasi</th>
            <th class="px-4 py-3 font-semibold">Qty</th>
            <th class="px-4 py-3 font-semibold">Biaya</th>
            <th class="px-4 py-3 font-semibold">Catatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($cards as $row): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 text-slate-700"><?= e(format_datetime_id($row['created_at'])) ?></td>
            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"><?= e($row['movement_type']) ?></span></td>
            <td class="px-4 py-3 text-slate-700"><?= e($row['qty']) ?></td>
            <td class="px-4 py-3 text-slate-700"><?= currency($row['unit_cost']) ?></td>
            <td class="px-4 py-3 text-slate-700"><?= e($row['notes']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($cards)): ?><tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada mutasi stok.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

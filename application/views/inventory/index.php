<div class="mb-6">
  <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Inventory & Stok Obat</h1>
  
</div>

<div class="ajax-form grid gap-6 xl:grid-cols-3">
  <section class="space-y-6 xl:col-span-1">
    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4">
        <h2 class="text-lg font-bold text-slate-900">Tambah Obat</h2>
        
      </div>
      <form method="post" action="<?= site_url('inventory/medicineStore') ?>" class="ajax-form space-y-4">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="name" placeholder="Nama obat" required>
        <div class="grid gap-4 md:grid-cols-2">
          <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit" placeholder="Satuan" value="strip">
          <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="min_stock" placeholder="Stok minimum" value="10">
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="buy_price" placeholder="Harga beli">
          <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="sell_price" placeholder="Harga jual">
        </div>
        <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-plus"></i> Simpan Obat</button>
      </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4">
        <h2 class="text-lg font-bold text-slate-900">Tambah Stok Masuk</h2>
        
      </div>
      <form method="post" action="<?= site_url('inventory/stockIn') ?>" class="ajax-form space-y-4">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="medicine_id" data-suggest-select="medicine" data-suggest-placeholder="Ketik nama obat..." required>
          <option value="">Pilih Obat</option>
          <?php foreach($items as $i): ?><option value="<?= $i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?>
        </select>
        <div class="grid gap-4 md:grid-cols-2">
          <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="qty" placeholder="Jumlah masuk" required>
          <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit_cost" placeholder="Biaya per unit">
        </div>
        <textarea class="min-h-[100px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="notes" placeholder="Catatan penerimaan stok"></textarea>
        <button class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700"><i class="fa-solid fa-box-open"></i> Simpan Stok</button>
      </form>
    </div>
  </section>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-bold text-slate-900">Daftar Obat Cabang</h2>
        
      </div>
      <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Total <?= count($items) ?> item</div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200">
      <div class="overflow-x-auto scrollbar-soft">
        <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-3 font-semibold">Nama</th>
              <th class="px-4 py-3 font-semibold">Satuan</th>
              <th class="px-4 py-3 font-semibold">Harga Jual</th>
              <th class="px-4 py-3 font-semibold">Min</th>
              <th class="px-4 py-3 font-semibold">Stok</th>
              <th class="px-4 py-3 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach($items as $i): ?>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-slate-800"><?= e($i['name']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($i['unit']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= currency($i['sell_price']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($i['min_stock']) ?></td>
                <td class="px-4 py-3">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= ((float)$i['stock'] <= (float)$i['min_stock']) ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>"><?= e($i['stock']) ?></span>
                </td>
                <td class="px-4 py-3 text-right">
                  <a class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-brand-200 hover:text-brand-700" href="<?= site_url('inventory/stockCard/'.$i['id']) ?>"><i class="fa-solid fa-chart-column"></i> Kartu Stok</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if(empty($items)): ?><tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada obat.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

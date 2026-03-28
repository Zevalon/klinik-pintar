<?php
$pendingCount = count($pendingPrescriptions);
$dispensedCount = count($dispensedPrescriptions);
?>
<div class="mb-6">
  <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Farmasi & Validasi Resep</h1>
  
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Resep Menunggu Validasi</h2>
      
    </div>
    <div class="rounded-2xl bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700">Total <?= $pendingCount ?> resep</div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Pasien</th>
            <th class="px-4 py-3 font-semibold">Poli</th>
            <th class="px-4 py-3 font-semibold">Pemeriksaan</th>
            <th class="px-4 py-3 font-semibold">Ringkasan Resep</th>
            <th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($pendingPrescriptions as $prescription): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($prescription['patient_name']) ?></div>
                <div class="text-xs text-slate-500">No RM <?= e($prescription['medical_record_no']) ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700">
                <div><?= e($prescription['clinic_name'] ?: '-') ?></div>
                <div class="text-xs text-slate-500"><?= e($prescription['diagnosis_name'] ?: 'Belum ada diagnosa') ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700"><?= e(format_datetime_id($prescription['visit_date'])) ?></td>
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($prescription['summary_text']) ?></div>
                <div class="text-xs text-slate-500"><?= (int)$prescription['item_count'] ?> item obat</div>
              </td>
              <td class="px-4 py-3"><?= status_badge($prescription['status']) ?></td>
              <td class="px-4 py-3 text-right">
                <button type="button" data-modal-open="pharmacyModal<?= (int)$prescription['id'] ?>" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-sky-700">
                  <i class="fa-solid fa-pills"></i> Validasi &amp; Serahkan
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($pendingPrescriptions)): ?><tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada resep yang menunggu validasi farmasi.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Riwayat Obat Sudah Diserahkan</h2>
      
    </div>
    <div class="rounded-2xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Total <?= $dispensedCount ?> resep</div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Pasien</th>
            <th class="px-4 py-3 font-semibold">Poli</th>
            <th class="px-4 py-3 font-semibold">Obat Diserahkan</th>
            <th class="px-4 py-3 font-semibold">Waktu Serah</th>
            <th class="px-4 py-3 font-semibold">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($dispensedPrescriptions as $prescription): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($prescription['patient_name']) ?></div>
                <div class="text-xs text-slate-500">No RM <?= e($prescription['medical_record_no']) ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700"><?= e($prescription['clinic_name'] ?: '-') ?></td>
              <td class="px-4 py-3 text-slate-700"><?= e($prescription['summary_text']) ?></td>
              <td class="px-4 py-3 text-slate-700"><?= e(format_datetime_id($prescription['dispensed_at'] ?: $prescription['updated_at'] ?: $prescription['visit_date'])) ?></td>
              <td class="px-4 py-3"><?= status_badge('dispensed') ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($dispensedPrescriptions)): ?><tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada resep yang diserahkan.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php foreach($pendingPrescriptions as $prescription): ?>
  <?php $modalId = 'pharmacyModal' . (int)$prescription['id']; ?>
  <div id="<?= $modalId ?>" class="fixed inset-0 z-50 hidden bg-slate-950/50 p-4">
    <div class="mx-auto mt-6 max-h-[90vh] max-w-6xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl">
      <div class="mb-5 flex flex-col gap-4 border-b border-slate-100 pb-5 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <div class="flex flex-wrap items-center gap-3">
            <h3 class="text-xl font-bold text-slate-900">Validasi Resep Farmasi</h3>
            <?= status_badge($prescription['status']) ?>
          </div>
          <div class="mt-3 text-lg font-semibold text-slate-900"><?= e($prescription['patient_name']) ?></div>
          <div class="mt-1 text-sm text-slate-500">No RM <?= e($prescription['medical_record_no']) ?> · Poli <?= e($prescription['clinic_name'] ?: '-') ?></div>
          <div class="mt-1 text-sm text-slate-500">Diagnosa <?= e($prescription['diagnosis_name'] ?: '-') ?> · Selesai diperiksa <?= e(format_datetime_id($prescription['visit_date'])) ?></div>
        </div>
        <button type="button" data-modal-close="<?= $modalId ?>" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-slate-500 hover:bg-slate-100">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 p-4">
          <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800"><i class="fa-solid fa-notes-medical text-brand-600"></i> Ringkasan Pemeriksaan</div>
          <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Keluhan</div>
              <div class="mt-2 text-sm text-slate-700 leading-6 min-h-[48px]"><?= e($prescription['complaint'] ?: '-') ?></div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diagnosa</div>
              <div class="mt-2 text-sm text-slate-700 leading-6 min-h-[48px]"><?= e($prescription['diagnosis_name'] ?: '-') ?></div>
            </div>
          </div>
        </div>

        <form method="post" action="<?= site_url('pharmacy/dispense/'.$prescription['id']) ?>" class="ajax-form pharmacy-dispense-form space-y-4" data-close-modal="<?= $modalId ?>" data-reset-on-success="false">
          <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

          <div class="rounded-2xl border border-slate-200 p-4 w-full">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <div class="text-sm font-semibold text-slate-800">Obat yang akan diserahkan</div>
              </div>
              <button type="button" class="btn-add-pharmacy-row inline-flex items-center gap-2 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 hover:bg-sky-100">
                <i class="fa-solid fa-plus"></i> Tambah Obat
              </button>
            </div>

              <div class="pharmacy-items space-y-4">
                <?php $rows = !empty($prescription['items']) ? $prescription['items'] : [[]]; ?>
                <?php foreach($rows as $row): ?>
                  <div class="pharmacy-item-row grid gap-3 rounded-2xl border border-slate-200 p-4 md:grid-cols-12">
                    <div class="md:col-span-5">
                      <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="medicine_id[]" data-suggest-select="medicine" data-suggest-placeholder="Ketik nama obat...">
                        <option value="">Pilih obat</option>
                        <?php foreach($medicines as $medicine): ?>
                          <option value="<?= $medicine['id'] ?>" data-price="<?= e($medicine['sell_price']) ?>" <?= (!empty($row['medicine_id']) && (int)$row['medicine_id'] === (int)$medicine['id']) ? 'selected' : '' ?>><?= e($medicine['name']) ?> · stok <?= e(rtrim(rtrim(number_format((float)($medicine['stock'] ?? 0), 2, '.', ''), '0'), '.')) ?> · <?= currency($medicine['sell_price']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="md:col-span-2">
                      <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="qty[]" placeholder="Qty" value="<?= e($row['qty'] ?? '') ?>">
                    </div>
                    <div class="md:col-span-3">
                      <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="dosage[]" placeholder="Aturan pakai" value="<?= e($row['dosage'] ?? '') ?>">
                    </div>
                    <div class="md:col-span-2">
                      <input class="prescription-price w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit_price[]" placeholder="Harga" value="<?= e($row['unit_price'] ?? '') ?>">
                    </div>
                    <div class="md:col-span-12 flex justify-end">
                      <button type="button" class="btn-remove-pharmacy-row inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                        <i class="fa-solid fa-trash"></i> Hapus baris
                      </button>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <template class="pharmacy-row-template">
                <div class="pharmacy-item-row grid gap-3 rounded-2xl border border-slate-200 p-4 md:grid-cols-12">
                  <div class="md:col-span-5">
                    <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="medicine_id[]" data-suggest-select="medicine" data-suggest-placeholder="Ketik nama obat...">
                      <option value="">Pilih obat</option>
                      <?php foreach($medicines as $medicine): ?>
                        <option value="<?= $medicine['id'] ?>" data-price="<?= e($medicine['sell_price']) ?>"><?= e($medicine['name']) ?> · stok <?= e(rtrim(rtrim(number_format((float)($medicine['stock'] ?? 0), 2, '.', ''), '0'), '.')) ?> · <?= currency($medicine['sell_price']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="md:col-span-2">
                    <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="qty[]" placeholder="Qty">
                  </div>
                  <div class="md:col-span-3">
                    <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="dosage[]" placeholder="Aturan pakai">
                  </div>
                  <div class="md:col-span-2">
                    <input class="prescription-price w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit_price[]" placeholder="Harga">
                  </div>
                  <div class="md:col-span-12 flex justify-end">
                    <button type="button" class="btn-remove-pharmacy-row inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                      <i class="fa-solid fa-trash"></i> Hapus baris
                    </button>
                  </div>
                </div>
              </template>
          </div>

          <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700">
              <i class="fa-solid fa-box-open"></i> Validasi &amp; Serahkan Obat
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<script>
(function(){
  if(window.__pharmacyFormBound) return;
  window.__pharmacyFormBound = true;

  document.addEventListener('click', function(event){
    var addButton = event.target.closest('.btn-add-pharmacy-row');
    if(addButton){
      event.preventDefault();
      var form = addButton.closest('.pharmacy-dispense-form');
      if(!form) return;
      var template = form.querySelector('.pharmacy-row-template');
      var container = form.querySelector('.pharmacy-items');
      if(!template || !container) return;
      var fragment = template.content ? template.content.cloneNode(true) : null;
      if(fragment){
        container.appendChild(fragment);
      } else {
        var holder = document.createElement('div');
        holder.innerHTML = template.innerHTML;
        while(holder.firstChild) container.appendChild(holder.firstChild);
      }
      if(window.appInitSuggestSelects){
        window.appInitSuggestSelects(container);
      }
      return;
    }

    var removeButton = event.target.closest('.btn-remove-pharmacy-row');
    if(removeButton){
      event.preventDefault();
      var row = removeButton.closest('.pharmacy-item-row');
      if(row) row.remove();
    }
  });
})();
</script>

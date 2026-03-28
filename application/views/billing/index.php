<?php
$canManageBilling = role_in(['cashier','branch_admin','super_admin']);
$readyCount = count($readyRecords);
$paidCount = count($paidRecords);
?>
<div class="mb-6">
  <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Kasir & Pembayaran Tunai</h1>
  
</div>

<section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Pasien Siap Ditagih</h2>
      
    </div>
    <div class="rounded-2xl bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700">Total <?= $readyCount ?> data</div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Pasien</th>
            <th class="px-4 py-3 font-semibold">No RM</th>
            <th class="px-4 py-3 font-semibold">Poli</th>
            <th class="px-4 py-3 font-semibold">Selesai Pemeriksaan</th>
            <th class="px-4 py-3 font-semibold">Tagihan</th>
            <th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($readyRecords as $record): ?>
            <?php $previewTotal = max(0, (float)$record['estimated_subtotal'] - (float)$record['discount']); ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($record['patient_name']) ?></div>
                <div class="text-xs text-slate-500">No Antrian <?= e($record['queue_number'] ?: '-') ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700"><?= e($record['medical_record_no']) ?></td>
              <td class="px-4 py-3 text-slate-700"><?= e($record['clinic_name'] ?: '-') ?></td>
              <td class="px-4 py-3 text-slate-700"><?= e(format_datetime_id($record['visit_date'])) ?></td>
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900"><?= currency($previewTotal) ?></div>
                <div class="text-xs text-slate-500">Subtotal <?= currency($record['estimated_subtotal']) ?></div>
              </td>
              <td class="px-4 py-3">
                <div><?= status_badge('ready') ?></div>
                <?php if(!empty($record['prescription_id'])): ?><div class="mt-1 text-xs text-slate-500">Obat sudah diverifikasi farmasi</div><?php endif; ?>
              </td>
              <td class="px-4 py-3 text-right">
                <?php if($canManageBilling): ?>
                  <button type="button" data-modal-open="billingPaymentModal<?= (int)$record['visit_id'] ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-700">
                    <i class="fa-solid fa-cash-register"></i> Pembayaran
                  </button>
                <?php else: ?>
                  <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500">Read only</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($readyRecords)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada pasien yang siap ditagih. Pastikan pemeriksaan sudah disimpan dan resep sudah divalidasi farmasi.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Pasien Lunas</h2>
      
    </div>
    <div class="rounded-2xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Total <?= $paidCount ?> data</div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Invoice</th>
            <th class="px-4 py-3 font-semibold">Pasien</th>
            <th class="px-4 py-3 font-semibold">Poli</th>
            <th class="px-4 py-3 font-semibold">Total Bayar</th>
            <th class="px-4 py-3 font-semibold">Waktu Bayar</th>
            <th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($paidRecords as $record): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($record['invoice_no'] ?: 'INV-' . str_pad((string)$record['visit_id'], 5, '0', STR_PAD_LEFT)) ?></div>
                <div class="text-xs text-slate-500">No RM <?= e($record['medical_record_no']) ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700"><?= e($record['patient_name']) ?></td>
              <td class="px-4 py-3 text-slate-700"><?= e($record['clinic_name'] ?: '-') ?></td>
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900"><?= currency($record['grand_total']) ?></div>
                <div class="text-xs text-slate-500">Diterima <?= currency($record['last_payment']['amount'] ?? $record['grand_total']) ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700"><?= e(format_datetime_id($record['paid_at'] ?: ($record['last_payment']['paid_at'] ?? $record['visit_date']))) ?></td>
              <td class="px-4 py-3"><?= status_badge('paid') ?></td>
              <td class="px-4 py-3 text-right">
                <a href="<?= site_url('billing/pdf/'.$record['invoice_id']) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-sky-200 hover:text-sky-700">
                  <i class="fa-solid fa-file-pdf text-rose-600"></i> Unduh PDF Invoice
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($paidRecords)): ?>
            <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada pembayaran lunas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php foreach($readyRecords as $record): ?>
  <?php
    $modalId = 'billingPaymentModal' . (int)$record['visit_id'];
    $previewSubtotal = (float)$record['estimated_subtotal'];
    $previewDiscount = (float)$record['discount'];
    $previewTotal = max(0, $previewSubtotal - $previewDiscount);
    $remainingTotal = max(0, $previewTotal - (float)$record['paid_total']);
    $vitals = $record['vitals'] ?: [];
  ?>
  <div id="<?= $modalId ?>" class="fixed inset-0 z-50 hidden bg-slate-950/50 p-4">
    <div class="mx-auto mt-6 max-h-[90vh] max-w-6xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl">
      <div class="mb-5 flex flex-col gap-4 border-b border-slate-100 pb-5 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <div class="flex flex-wrap items-center gap-3">
            <h3 class="text-xl font-bold text-slate-900">Pembayaran Tunai Pasien</h3>
            <?= status_badge('ready') ?>
          </div>
          <div class="mt-3 text-lg font-semibold text-slate-900"><?= e($record['patient_name']) ?></div>
          <div class="mt-1 text-sm text-slate-500">No RM <?= e($record['medical_record_no']) ?> · Poli <?= e($record['clinic_name'] ?: '-') ?> · No Antrian <?= e($record['queue_number'] ?: '-') ?></div>
          <div class="mt-1 text-sm text-slate-500">Pemeriksaan selesai <?= e(format_datetime_id($record['visit_date'])) ?><?php if(!empty($record['dispensed_at'])): ?> · Farmasi selesai <?= e(format_datetime_id($record['dispensed_at'])) ?><?php endif; ?></div>
        </div>
        <button type="button" data-modal-close="<?= $modalId ?>" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-slate-500 hover:bg-slate-100">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
          <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tekanan Darah</div>
              <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($vitals['blood_pressure'] ?? '-') ?></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Suhu</div>
              <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($vitals['temperature'] ?? '-') ?></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Berat / Tinggi</div>
              <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($vitals['weight'] ?? '-') ?> / <?= e($vitals['height'] ?? '-') ?></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nadi</div>
              <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($vitals['pulse'] ?? '-') ?></div>
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 md:col-span-2">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Keluhan</div>
              <div class="mt-2 text-sm text-slate-700"><?= e($record['complaint'] ?: '-') ?></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diagnosa</div>
              <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($record['diagnosis_name'] ?: '-') ?></div>
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">SOAP / Catatan Medis</div>
              <div class="mt-2 text-sm text-slate-700 whitespace-pre-line"><?= e($record['soap_notes'] ?: '-') ?></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Dokter</div>
              <div class="mt-2 text-sm text-slate-700 whitespace-pre-line"><?= e($record['treatment_notes'] ?: '-') ?></div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800"><i class="fa-solid fa-bandage text-brand-600"></i> Detail Layanan / Tindakan</div>
            <?php if(!empty($record['services'])): ?>
              <div class="overflow-hidden rounded-2xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                  <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                      <th class="px-4 py-3 font-semibold">Layanan</th>
                      <th class="px-4 py-3 font-semibold">Kategori</th>
                      <th class="px-4 py-3 font-semibold">Qty</th>
                      <th class="px-4 py-3 font-semibold text-right">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach($record['services'] as $service): ?>
                      <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?= e($service['service_name']) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= e(ucfirst($service['category'])) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= e(number_format((float)$service['qty'], 0, ',', '.')) ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-900"><?= currency($service['subtotal']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">Belum ada layanan atau tindakan dari halaman pemeriksaan.</div>
            <?php endif; ?>
          </div>

          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800"><i class="fa-solid fa-capsules text-brand-600"></i> Detail Obat / Resep Final Farmasi</div>
            <?php if(!empty($record['medicines'])): ?>
              <div class="overflow-hidden rounded-2xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                  <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                      <th class="px-4 py-3 font-semibold">Obat</th>
                      <th class="px-4 py-3 font-semibold">Qty</th>
                      <th class="px-4 py-3 font-semibold">Aturan Pakai</th>
                      <th class="px-4 py-3 font-semibold text-right">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach($record['medicines'] as $medicine): ?>
                      <?php $lineTotal = (float)$medicine['qty'] * (float)$medicine['unit_price']; ?>
                      <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800"><?= e($medicine['medicine_name']) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= e(number_format((float)$medicine['qty'], 0, ',', '.')) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= e($medicine['dosage'] ?: '-') ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-900"><?= currency($lineTotal) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">Tidak ada resep obat untuk kunjungan ini.</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="xl:col-span-1">
          <?php if($canManageBilling): ?>
            <form method="post" action="<?= site_url('billing/settle/'.$record['visit_id']) ?>" class="ajax-form billing-payment-form space-y-4 rounded-3xl border border-emerald-200 bg-emerald-50/40 p-5" data-close-modal="<?= $modalId ?>" data-reset-on-success="false" data-subtotal="<?= e((string)$previewSubtotal) ?>" data-paid="<?= e((string)$record['paid_total']) ?>">
              <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
              <div>
                <h4 class="text-lg font-bold text-slate-900">Proses Pembayaran</h4>
                
              </div>

              <div class="rounded-2xl border border-white/80 bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="space-y-2 text-sm text-slate-600">
                  <div class="flex items-center justify-between"><span>Subtotal</span><strong class="text-slate-900" data-billing-subtotal><?= currency($previewSubtotal) ?></strong></div>
                  <div class="flex items-center justify-between"><span>Sudah dibayar</span><strong class="text-slate-900" data-billing-paid><?= currency($record['paid_total']) ?></strong></div>
                  <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Diskon</label>
                    <input name="discount" value="<?= e(number_format($previewDiscount, 0, ',', '.')) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="0">
                  </div>
                  <div class="flex items-center justify-between border-t border-slate-200 pt-2"><span>Total Tagihan</span><strong class="text-slate-900" data-billing-total><?= currency($previewTotal) ?></strong></div>
                  <div class="flex items-center justify-between"><span>Sisa yang harus dibayar</span><strong class="text-emerald-700" data-billing-due><?= currency($remainingTotal) ?></strong></div>
                </div>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600">Nominal uang pembayaran cash</label>
                <input name="amount_received" value="<?= e(number_format($remainingTotal, 0, ',', '.')) ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Masukkan nominal uang tunai">
              </div>

              <div data-billing-message class="rounded-2xl bg-white px-4 py-3 text-xs text-slate-500 ring-1 ring-inset ring-slate-200">Masukkan nominal uang tunai yang diterima kasir.</div>

              <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                <i class="fa-solid fa-circle-check"></i> Set Pembayaran Lunas
              </button>
            </form>
          <?php else: ?>
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">Anda sedang melihat data kasir dalam mode read-only. Proses pembayaran hanya tersedia untuk kasir, admin cabang, atau super admin.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<script>
(function(){
  if(window.__billingModalBound) return;
  window.__billingModalBound = true;

  function parseMoney(value){
    if(!value) return 0;
    var normalized = String(value).replace(/\./g,'').replace(/,/g,'.').replace(/[^0-9.-]/g,'');
    var number = parseFloat(normalized);
    return isNaN(number) ? 0 : number;
  }

  function formatRupiah(value){
    return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(value || 0);
  }

  function updateBillingForm(form){
    if(!form) return;

    var subtotal = parseMoney(form.getAttribute('data-subtotal'));
    var paid = parseMoney(form.getAttribute('data-paid'));
    var discountInput = form.querySelector('[name="discount"]');
    var amountInput = form.querySelector('[name="amount_received"]');
    var totalEl = form.querySelector('[data-billing-total]');
    var dueEl = form.querySelector('[data-billing-due]');
    var paidEl = form.querySelector('[data-billing-paid]');
    var messageEl = form.querySelector('[data-billing-message]');

    var discount = discountInput ? parseMoney(discountInput.value) : 0;
    var amount = amountInput ? parseMoney(amountInput.value) : 0;
    var total = Math.max(0, subtotal - discount);
    var due = Math.max(0, total - paid);

    if(totalEl) totalEl.textContent = formatRupiah(total);
    if(dueEl) dueEl.textContent = formatRupiah(due);
    if(paidEl) paidEl.textContent = formatRupiah(paid);
    if(!messageEl) return;

    messageEl.className = 'rounded-2xl bg-white px-4 py-3 text-xs ring-1 ring-inset';

    if(!amountInput || !amountInput.value){
      messageEl.classList.add('text-slate-500','ring-slate-200');
      messageEl.textContent = due > 0 ? 'Masukkan nominal uang tunai yang diterima kasir.' : 'Tagihan sudah terpenuhi.';
      return;
    }

    if(amount < due){
      messageEl.classList.add('text-rose-700','ring-rose-200','bg-rose-50');
      messageEl.textContent = 'Nominal kurang ' + formatRupiah(due - amount) + '.';
      return;
    }

    if(amount > due){
      messageEl.classList.add('text-emerald-700','ring-emerald-200','bg-emerald-50');
      messageEl.textContent = 'Kembalian pasien ' + formatRupiah(amount - due) + '.';
      return;
    }

    messageEl.classList.add('text-sky-700','ring-sky-200','bg-sky-50');
    messageEl.textContent = 'Nominal uang pas sesuai tagihan.';
  }

  document.addEventListener('input', function(event){
    var form = event.target.closest('.billing-payment-form');
    if(!form) return;
    if(event.target.matches('[name="discount"], [name="amount_received"]')){
      updateBillingForm(form);
    }
  });

  document.querySelectorAll('.billing-payment-form').forEach(updateBillingForm);
})();
</script>

<?php
$hasSummary = !empty($visit_services) || !empty($items);
$prescriptionRows = !empty($items) ? array_values($items) : [[]];
?>
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Pemeriksaan Pasien</h1>
    
  </div>
  <a href="<?= site_url('visits') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-soft hover:border-brand-200 hover:text-brand-700">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar kunjungan
  </a>
</div>

<div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-12">
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 md:col-span-2 xl:col-span-6">
    <div class="flex items-start gap-4">
      <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-brand-50 text-xl font-bold text-brand-700"><?= e(initials($visit['patient_name'])) ?></div>
      <div class="min-w-0">
        <div class="text-xl font-bold text-slate-900"><?= e($visit['patient_name']) ?></div>
        <div class="mt-1 text-sm text-slate-500">No RM <?= e($visit['medical_record_no']) ?> · <?= e(gender_label($visit['gender'])) ?></div>
        <div class="mt-1 text-sm text-slate-500"><?= e($visit['phone']) ?> · <?= e(format_date_id($visit['birth_date'])) ?></div>
      </div>
    </div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 xl:col-span-3">
    <div class="text-sm text-slate-500">Poli</div>
    <div class="mt-1 text-lg font-bold text-slate-900"><?= e($visit['clinic_name']) ?></div>
    <div class="mt-2 text-sm text-slate-500">Keluhan awal: <?= e($visit['complaint'] ?: '-') ?></div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 xl:col-span-3">
    <div class="text-sm text-slate-500">Status</div>
    <div class="mt-3"><?= status_badge($visit['status']) ?></div>
    
  </div>
</div>

<form method="post" action="<?= site_url('visits/saveClinical/'.$visit['id']) ?>" class="ajax-form space-y-6" data-reset-on-success="false">
  <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

  <div class="grid gap-6 xl:grid-cols-12">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-4">
      <div class="mb-4 flex items-center gap-2">
        <i class="fa-solid fa-heart-pulse text-brand-600"></i>
        <h2 class="text-lg font-bold text-slate-900">Vital Sign</h2>
      </div>
      <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="blood_pressure" placeholder="Tekanan darah" value="<?= e($vitals['blood_pressure'] ?? '') ?>">
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="temperature" placeholder="Suhu" value="<?= e($vitals['temperature'] ?? '') ?>">
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="weight" placeholder="Berat" value="<?= e($vitals['weight'] ?? '') ?>">
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="height" placeholder="Tinggi" value="<?= e($vitals['height'] ?? '') ?>">
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 sm:col-span-2 xl:col-span-1 2xl:col-span-2" name="pulse" placeholder="Nadi" value="<?= e($vitals['pulse'] ?? '') ?>">
      </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-8">
      <div class="mb-4 flex items-center gap-2">
        <i class="fa-solid fa-notes-medical text-brand-600"></i>
        <h2 class="text-lg font-bold text-slate-900">Diagnosa & Catatan Medis</h2>
      </div>
      <div class="grid gap-4 md:grid-cols-4">
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="icd_code" placeholder="Kode ICD" value="<?= e($diagnosis['icd_code'] ?? '') ?>">
        <input class="md:col-span-3 rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="diagnosis_name" placeholder="Nama diagnosa" value="<?= e($diagnosis['diagnosis_name'] ?? '') ?>">
      </div>
      <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <textarea class="min-h-[150px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="soap_notes" placeholder="SOAP / catatan medis"><?= e($diagnosis['soap_notes'] ?? '') ?></textarea>
        <textarea class="min-h-[150px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="treatment_notes" placeholder="Catatan tindakan"><?= e($diagnosis['treatment_notes'] ?? '') ?></textarea>
      </div>
    </section>
  </div>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center gap-2">
      <i class="fa-solid fa-syringe text-brand-600"></i>
      <h2 class="text-lg font-bold text-slate-900">Layanan / Tindakan</h2>
    </div>
    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
      <?php foreach(array_merge($consultations, $procedures) as $service): ?>
        <?php $checked = false; foreach($visit_services as $savedService) { if ($savedService['service_id'] == $service['id']) { $checked = true; break; } } ?>
        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4 hover:border-brand-200 hover:bg-brand-50/50">
          <input class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" type="checkbox" name="service_id[]" value="<?= $service['id'] ?>" <?= $checked ? 'checked' : '' ?>>
          <span>
            <span class="block font-semibold text-slate-800"><?= e($service['name']) ?></span>
            <span class="text-sm text-slate-500"><?= e(ucfirst($service['category'])) ?> · <?= currency($service['price']) ?></span>
          </span>
        </label>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-capsules text-brand-600"></i>
        <div>
          <h2 class="text-lg font-bold text-slate-900">Resep Obat</h2>
          
        </div>
      </div>
      <button type="button" id="btn-add-medicine-row" class="inline-flex items-center gap-2 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 hover:bg-sky-100">
        <i class="fa-solid fa-plus"></i> Tambah Obat
      </button>
    </div>

    <div id="prescription-rows" class="space-y-4">
      <?php foreach($prescriptionRows as $saved): ?>
        <div class="prescription-row grid gap-3 rounded-2xl border border-slate-200 p-4 md:grid-cols-12">
          <div class="md:col-span-5">
            <select class="medicine-select w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="medicine_id[]" data-suggest-select="medicine" data-suggest-placeholder="Ketik nama obat...">
              <option value="">Pilih obat</option>
              <?php foreach($medicines as $m): ?>
                <option value="<?= $m['id'] ?>" data-price="<?= e($m['sell_price']) ?>" <?= (!empty($saved['medicine_id']) && $saved['medicine_id']==$m['id'])?'selected':'' ?>><?= e($m['name']) ?> · <?= currency($m['sell_price']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="md:col-span-2">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="qty[]" placeholder="Qty" value="<?= e($saved['qty'] ?? '') ?>">
          </div>
          <div class="md:col-span-3">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="dosage[]" placeholder="Aturan pakai" value="<?= e($saved['dosage'] ?? '') ?>">
          </div>
          <div class="md:col-span-2">
            <input class="prescription-price w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit_price[]" placeholder="Harga" value="<?= e($saved['unit_price'] ?? '') ?>">
          </div>
          <div class="md:col-span-12 flex justify-end">
            <button type="button" class="btn-remove-medicine-row inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
              <i class="fa-solid fa-trash"></i> Hapus baris
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <template id="prescription-row-template">
      <div class="prescription-row grid gap-3 rounded-2xl border border-slate-200 p-4 md:grid-cols-12">
        <div class="md:col-span-5">
          <select class="medicine-select w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="medicine_id[]" data-suggest-select="medicine" data-suggest-placeholder="Ketik nama obat...">
            <option value="">Pilih obat</option>
            <?php foreach($medicines as $m): ?>
              <option value="<?= $m['id'] ?>" data-price="<?= e($m['sell_price']) ?>"><?= e($m['name']) ?> · <?= currency($m['sell_price']) ?></option>
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
          <button type="button" class="btn-remove-medicine-row inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
            <i class="fa-solid fa-trash"></i> Hapus baris
          </button>
        </div>
      </div>
    </template>
  </section>

  <div class="flex flex-wrap items-center gap-3">
    <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-floppy-disk"></i> Simpan Diagnosa / Pemeriksaan</button>
    
  </div>

  <?php if($hasSummary): ?>
    <div class="grid gap-6 xl:grid-cols-2">
      <?php if(!empty($visit_services)): ?>
        <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
          <h3 class="text-lg font-bold text-slate-900">Layanan Tersimpan</h3>
          <div class="mt-4 space-y-3">
            <?php foreach($visit_services as $service): ?>
              <div class="rounded-2xl bg-slate-50 p-4">
                <div class="font-semibold text-slate-800"><?= e($service['service_name']) ?></div>
                <div class="mt-1 text-sm text-slate-500"><?= e(ucfirst($service['category'])) ?> · <?= currency($service['subtotal']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if(!empty($items)): ?>
        <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
          <h3 class="text-lg font-bold text-slate-900">Ringkasan Resep</h3>
          <div class="mt-4 space-y-3">
            <?php foreach($items as $it): ?>
              <div class="rounded-2xl bg-slate-50 p-4">
                <div class="font-semibold text-slate-800"><?= e($it['medicine_name']) ?></div>
                <div class="mt-1 text-sm text-slate-500">Qty <?= e($it['qty']) ?> · <?= e($it['dosage']) ?></div>
                <div class="mt-1 text-sm font-semibold text-slate-900"><?= currency($it['unit_price']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</form>

<?php
$hasSummary = !empty($visit_services) || !empty($items);
$prescriptionRows = !empty($items) ? array_values($items) : [[]];
$visit_record = $visit_record ?? [];
$patient_profile = $patient_profile ?? [];
$active_monitoring = $active_monitoring ?? [];
$recent_history = $recent_history ?? [];
?>
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Pemeriksaan Pasien</h1>
    <p class="mt-1 text-sm text-slate-500">Lengkapi rekam medis rinci, tindakan, resep, dan rencana kontrol pasien.</p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <a href="<?= site_url('medicalrecords/show/'.$visit['patient_id']) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-700 shadow-soft hover:border-brand-200 hover:text-brand-900">
      <i class="fa-solid fa-notes-medical"></i> Lihat Rekam Medis Pasien
    </a>
    <a href="<?= site_url('visits') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-soft hover:border-brand-200 hover:text-brand-700">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar kunjungan
    </a>
  </div>
</div>

<div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-12">
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 md:col-span-2 xl:col-span-5">
    <div class="flex items-start gap-4">
      <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-brand-50 text-xl font-bold text-brand-700"><?= e(initials($visit['patient_name'])) ?></div>
      <div class="min-w-0">
        <div class="text-xl font-bold text-slate-900"><?= e($visit['patient_name']) ?></div>
        <div class="mt-1 text-sm text-slate-500">No RM <?= e($visit['medical_record_no']) ?> · <?= e(gender_label($visit['gender'])) ?> · <?= e(patient_age_label($visit['birth_date'])) ?></div>
        <div class="mt-1 text-sm text-slate-500"><?= e($visit['phone'] ? format_phone($visit['phone']) : '-') ?> · <?= e(format_date_id($visit['birth_date'])) ?></div>
        <?php if (!empty($patient_profile['allergy_notes']) || !empty($patient_profile['chronic_conditions'])): ?>
          <div class="mt-3 flex flex-wrap gap-2 text-xs">
            <?php if (!empty($patient_profile['allergy_notes'])): ?><span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 font-semibold text-rose-700">Alergi tercatat</span><?php endif; ?>
            <?php if (!empty($patient_profile['chronic_conditions'])): ?><span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">Komorbid/kronis ada</span><?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 xl:col-span-3">
    <div class="text-sm text-slate-500">Poli & keluhan awal</div>
    <div class="mt-1 text-lg font-bold text-slate-900"><?= e($visit['clinic_name']) ?></div>
    <div class="mt-2 text-sm text-slate-500">Keluhan awal: <?= e($visit['complaint'] ?: '-') ?></div>
    <div class="mt-2 text-xs text-slate-500">Monitoring aktif pasien: <?= count($active_monitoring) ?> program</div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
    <div class="text-sm text-slate-500">Status kunjungan</div>
    <div class="mt-3"><?= status_badge($visit['status']) ?></div>
    <div class="mt-3 text-xs text-slate-500">Dokter pemeriksa akan tercatat saat pemeriksaan disimpan.</div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
    <div class="text-sm text-slate-500">Alert pasien</div>
    <div class="mt-2 text-sm text-slate-700"><?= nl2br(e($patient_profile['alert_notes'] ?? $patient_profile['special_condition_notes'] ?? '-')) ?></div>
  </div>
</div>

<?php if (!empty($patient_profile['allergy_notes']) || !empty($patient_profile['alert_notes']) || !empty($patient_profile['special_condition_notes'])): ?>
  <section class="mb-6 rounded-3xl border border-amber-200 bg-amber-50 p-6 text-amber-900 shadow-soft">
    <div class="flex items-start gap-3">
      <div class="mt-1 text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div>
        <h2 class="text-lg font-bold">Perhatian sebelum tindakan</h2>
        <?php if (!empty($patient_profile['allergy_notes'])): ?><div class="mt-2 text-sm"><span class="font-semibold">Alergi:</span> <?= nl2br(e($patient_profile['allergy_notes'])) ?></div><?php endif; ?>
        <?php if (!empty($patient_profile['alert_notes'])): ?><div class="mt-2 text-sm"><span class="font-semibold">Alert klinis:</span> <?= nl2br(e($patient_profile['alert_notes'])) ?></div><?php endif; ?>
        <?php if (!empty($patient_profile['special_condition_notes'])): ?><div class="mt-2 text-sm"><span class="font-semibold">Kondisi khusus:</span> <?= nl2br(e($patient_profile['special_condition_notes'])) ?></div><?php endif; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

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
        <textarea class="min-h-[150px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="soap_notes" placeholder="SOAP / catatan medis lama (opsional)"><?= e($diagnosis['soap_notes'] ?? '') ?></textarea>
        <textarea class="min-h-[150px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="treatment_notes" placeholder="Catatan tindakan / terapi yang diberikan"><?= e($diagnosis['treatment_notes'] ?? '') ?></textarea>
      </div>
    </section>
  </div>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center gap-2">
      <i class="fa-solid fa-book-medical text-brand-600"></i>
      <h2 class="text-lg font-bold text-slate-900">Rekam Medis Rinci</h2>
    </div>
    <div class="grid gap-4 xl:grid-cols-2">
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="anamnesis" placeholder="Anamnesis lengkap"><?= e($visit_record['anamnesis'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="complaint_history" placeholder="Riwayat keluhan sekarang"><?= e($visit_record['complaint_history'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="subjective_notes" placeholder="Subjective notes"><?= e($visit_record['subjective_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="objective_notes" placeholder="Objective notes"><?= e($visit_record['objective_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="physical_exam" placeholder="Pemeriksaan fisik"><?= e($visit_record['physical_exam'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="assessment_notes" placeholder="Assessment / penilaian klinis"><?= e($visit_record['assessment_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="plan_notes" placeholder="Plan / rencana terapi"><?= e($visit_record['plan_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[130px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="procedure_notes" placeholder="Tindakan / prosedur medis"><?= e($visit_record['procedure_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="diagnosis_secondary" placeholder="Diagnosis sekunder / banding"><?= e($visit_record['diagnosis_secondary'] ?? '') ?></textarea>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="condition_flags" placeholder="Flag kondisi: risiko jatuh, dehidrasi, kontrol gula, sesak, dll."><?= e($visit_record['condition_flags'] ?? '') ?></textarea>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="lab_notes" placeholder="Permintaan / hasil lab"><?= e($visit_record['lab_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="radiology_notes" placeholder="Permintaan / hasil radiologi"><?= e($visit_record['radiology_notes'] ?? '') ?></textarea>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="allergy_confirmation" placeholder="Alergi yang dikonfirmasi saat kunjungan ini"><?= e($visit_record['allergy_confirmation'] ?? '') ?></textarea>
      <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="referral_notes" placeholder="Catatan rujukan / tindak lanjut eksternal"><?= e($visit_record['referral_notes'] ?? '') ?></textarea>
    </div>
    <div class="mt-4 grid gap-4 md:grid-cols-2">
      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Rencana kontrol / edukasi</label>
        <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="control_plan" placeholder="Instruksi pulang, target terapi, edukasi pasien"><?= e($visit_record['control_plan'] ?? '') ?></textarea>
      </div>
      <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
          <input type="checkbox" name="special_condition" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" <?= !empty($visit_record['special_condition']) ? 'checked' : '' ?>>
          Tandai pasien memiliki kondisi khusus pada kunjungan ini
        </label>
        <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="special_condition_details" placeholder="Detail kondisi khusus, observasi, kebutuhan follow-up ketat"><?= e($visit_record['special_condition_details'] ?? '') ?></textarea>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal kontrol berikutnya</label>
          <input type="date" name="next_control_date" value="<?= e($visit_record['next_control_date'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </div>
      </div>
    </div>
  </section>

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
            <input class="prescription-price w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit_price[]" placeholder="Rp 0" value="<?= e($saved['unit_price'] ?? '') ?>">
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
          <input class="prescription-price w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="unit_price[]" placeholder="Rp 0">
        </div>
        <div class="md:col-span-12 flex justify-end">
          <button type="button" class="btn-remove-medicine-row inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
            <i class="fa-solid fa-trash"></i> Hapus baris
          </button>
        </div>
      </div>
    </template>
  </section>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
    <div class="mb-4 flex items-center gap-2">
      <i class="fa-solid fa-calendar-check text-brand-600"></i>
      <h2 class="text-lg font-bold text-slate-900">Monitoring Kontrol Rutin</h2>
    </div>
    <div class="grid gap-4 xl:grid-cols-12">
      <div class="xl:col-span-6 space-y-4">
        <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
          <input type="checkbox" name="monitoring_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
          Tambahkan / perbarui program monitoring rutin dari kunjungan ini
        </label>
        <div class="grid gap-4 md:grid-cols-2">
          <input type="text" name="monitoring_program_name" class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Nama program monitoring">
          <input type="text" name="monitoring_condition_name" class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Kondisi yang dimonitor">
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <input type="text" name="monitoring_frequency_label" class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Frekuensi kontrol">
          <input type="date" name="monitoring_next_control_date" class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </div>
        <textarea name="monitoring_notes" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Catatan target terapi / parameter yang harus dipantau"></textarea>
      </div>
      <div class="xl:col-span-6">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="mb-3 flex items-center justify-between gap-3">
            <div class="text-sm font-semibold text-slate-900">Monitoring aktif pasien saat ini</div>
            <div class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200"><?= count($active_monitoring) ?> aktif</div>
          </div>
          <div class="space-y-3">
            <?php foreach($active_monitoring as $plan): ?>
              <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="font-semibold text-slate-900"><?= e($plan['program_name']) ?></div>
                <div class="mt-1 text-sm text-slate-500"><?= e($plan['condition_name'] ?: '-') ?> · <?= e($plan['frequency_label'] ?: '-') ?></div>
                <div class="mt-1 text-xs text-slate-500">Kontrol berikutnya <?= e(format_date_id($plan['next_control_date'] ?? null)) ?></div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($active_monitoring)): ?>
              <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-4 py-6 text-center text-sm text-slate-500">Belum ada monitoring aktif untuk pasien ini.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="flex flex-wrap items-center gap-3">
    <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-floppy-disk"></i> Simpan Pemeriksaan Lengkap</button>
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

<section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Riwayat Kunjungan Sebelumnya</h2>
      <p class="mt-1 text-sm text-slate-500">Membantu dokter/perawat melihat pola terapi dan kontrol pasien sebelum menutup kunjungan hari ini.</p>
    </div>
    <a href="<?= site_url('medicalrecords/show/'.$visit['patient_id']) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-brand-200 hover:text-brand-700"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Lengkap</a>
  </div>
  <div class="space-y-3">
    <?php foreach($recent_history as $history): ?>
      <div class="rounded-2xl border border-slate-200 p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <div>
            <div class="font-semibold text-slate-900"><?= e(format_datetime_id($history['visit_date'])) ?> · <?= e($history['clinic_name'] ?: '-') ?></div>
            <div class="mt-1 text-sm text-slate-500">Diagnosis <?= e($history['diagnosis_name'] ?: '-') ?> · Dokter <?= e($history['doctor_name'] ?: '-') ?></div>
            <div class="mt-2 text-sm text-slate-700">Keluhan: <?= e($history['complaint'] ?: '-') ?></div>
            <?php if (!empty($history['control_plan'])): ?><div class="mt-1 text-sm text-slate-700">Kontrol/edukasi: <?= e($history['control_plan']) ?></div><?php endif; ?>
          </div>
          <div class="text-sm text-slate-600">
            <div>TD <?= e($history['blood_pressure'] ?: '-') ?></div>
            <div>Suhu <?= e($history['temperature'] ?: '-') ?></div>
            <div>BB <?= e($history['weight'] ?: '-') ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($recent_history)): ?>
      <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada riwayat kunjungan sebelumnya untuk pasien ini.</div>
    <?php endif; ?>
  </div>
</section>

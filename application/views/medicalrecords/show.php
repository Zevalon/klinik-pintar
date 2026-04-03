<?php
$dashboard = $dashboard ?: [];
$profile = $profile ?: [];
$badgeMap = [
  'active' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
  'completed' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
  'cancelled' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
];
?>
<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
  <div>
    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
      <i class="fa-solid fa-shield-heart"></i> Rekam Medis Longitudinal
    </div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900"><?= e($patient['name']) ?></h1>
    <p class="mt-1 text-sm text-slate-500">No RM <?= e($patient['medical_record_no']) ?> · <?= e(gender_label($patient['gender'])) ?> · <?= e(patient_age_label($patient['birth_date'])) ?></p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <a href="<?= site_url('medicalrecords') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-soft hover:border-brand-200 hover:text-brand-700"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <a href="<?= site_url('patients') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-soft hover:border-brand-200 hover:text-brand-700"><i class="fa-solid fa-hospital-user"></i> Data Pasien</a>
  </div>
</div>

<div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Kunjungan total</div>
    <div class="mt-2 text-3xl font-black text-slate-900"><?= (int)($dashboard['total_visits'] ?? 0) ?></div>
    <div class="mt-2 text-xs text-slate-500">Kunjungan terakhir <?= e(format_datetime_id($dashboard['last_visit_date'] ?? null)) ?></div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Diagnosis terakhir</div>
    <div class="mt-2 text-lg font-bold text-slate-900"><?= e($dashboard['last_diagnosis'] ?: '-') ?></div>
    <div class="mt-2 text-xs text-slate-500">Diambil dari kunjungan paling baru yang memiliki diagnosis.</div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Monitoring aktif</div>
    <div class="mt-2 text-3xl font-black text-slate-900"><?= (int)($dashboard['active_monitoring'] ?? 0) ?></div>
    <div class="mt-2 text-xs text-slate-500">Kontrol terdekat <?= e(format_date_id($dashboard['nearest_control_date'] ?? null)) ?></div>
  </div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200">
    <div class="text-sm text-slate-500">Kontak pasien</div>
    <div class="mt-2 text-lg font-bold text-slate-900"><?= e($patient['phone'] ? format_phone($patient['phone']) : '-') ?></div>
    <div class="mt-2 text-xs text-slate-500"><?= e($patient['address'] ?: 'Alamat belum diisi') ?></div>
  </div>
</div>

<?php if (!empty($profile['alert_notes']) || !empty($profile['allergy_notes']) || !empty($profile['special_condition_notes'])): ?>
  <section class="mb-6 rounded-3xl border border-amber-200 bg-amber-50 p-6 text-amber-900 shadow-soft">
    <div class="flex items-start gap-3">
      <div class="mt-1 text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div>
        <h2 class="text-lg font-bold">Peringatan medis pasien</h2>
        <?php if (!empty($profile['allergy_notes'])): ?><div class="mt-2 text-sm"><span class="font-semibold">Alergi:</span> <?= nl2br(e($profile['allergy_notes'])) ?></div><?php endif; ?>
        <?php if (!empty($profile['alert_notes'])): ?><div class="mt-2 text-sm"><span class="font-semibold">Alert klinis:</span> <?= nl2br(e($profile['alert_notes'])) ?></div><?php endif; ?>
        <?php if (!empty($profile['special_condition_notes'])): ?><div class="mt-2 text-sm"><span class="font-semibold">Kondisi khusus:</span> <?= nl2br(e($profile['special_condition_notes'])) ?></div><?php endif; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<div class="grid gap-6 xl:grid-cols-12">
  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-7">
    <div class="mb-4 flex items-center gap-2">
      <i class="fa-solid fa-file-waveform text-brand-600"></i>
      <h2 class="text-lg font-bold text-slate-900">Profil Medis Pasien</h2>
    </div>
    <form method="post" action="<?= site_url('medicalrecords/saveProfile/'.$patient['id']) ?>" class="ajax-form space-y-4" data-reset-on-success="false">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Golongan darah</label>
          <input type="text" name="blood_type" value="<?= e($profile['blood_type'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="A / B / AB / O">
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan kehamilan</label>
          <input type="text" name="pregnancy_notes" value="<?= e($profile['pregnancy_notes'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Mis. hamil trimester 2">
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Riwayat vaksinasi</label>
          <input type="text" name="vaccination_history" value="<?= e($profile['vaccination_history'] ?? '') ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Vaksin penting / booster">
        </div>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Alergi</label>
          <textarea name="allergy_notes" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Obat, makanan, bahan tertentu"><?= e($profile['allergy_notes'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Kondisi kronis / komorbid</label>
          <textarea name="chronic_conditions" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="DM, hipertensi, asma, CKD, dll."><?= e($profile['chronic_conditions'] ?? '') ?></textarea>
        </div>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Riwayat penyakit dahulu</label>
          <textarea name="past_medical_history" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Penyakit/kejadian medis penting sebelumnya"><?= e($profile['past_medical_history'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Riwayat operasi / tindakan besar</label>
          <textarea name="surgery_history" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Operasi caesar, appendektomi, dll."><?= e($profile['surgery_history'] ?? '') ?></textarea>
        </div>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Riwayat keluarga</label>
          <textarea name="family_history" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Riwayat herediter / keluarga"><?= e($profile['family_history'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Obat rutin / terapi berjalan</label>
          <textarea name="medication_history" class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Obat yang rutin dikonsumsi pasien"><?= e($profile['medication_history'] ?? '') ?></textarea>
        </div>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Gaya hidup</label>
          <textarea name="lifestyle_notes" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Merokok, alkohol, pola tidur, pekerjaan, aktivitas fisik"><?= e($profile['lifestyle_notes'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Alert klinis</label>
          <textarea name="alert_notes" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Mis. risiko jatuh, risiko hipoglikemia, isolasi, kontraindikasi"><?= e($profile['alert_notes'] ?? '') ?></textarea>
        </div>
      </div>
      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan kondisi khusus</label>
        <textarea name="special_condition_notes" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Pasien perlu observasi berkala, kontrol jangka panjang, atau penanganan khusus lain"><?= e($profile['special_condition_notes'] ?? '') ?></textarea>
      </div>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-floppy-disk"></i> Simpan Profil Medis</button>
    </form>
  </section>

  <section class="space-y-6 xl:col-span-5">
    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center gap-2">
        <i class="fa-solid fa-calendar-check text-brand-600"></i>
        <h2 class="text-lg font-bold text-slate-900">Program Monitoring / Kontrol</h2>
      </div>
      <form method="post" action="<?= site_url('medicalrecords/storeMonitoring/'.$patient['id']) ?>" class="ajax-form space-y-4" data-reset-on-success="false">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Nama program</label>
          <input type="text" name="program_name" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Mis. Monitoring Diabetes Melitus" required>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Kondisi / diagnosis</label>
            <input type="text" name="condition_name" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Hipertensi, asma, luka kronis, dll.">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Frekuensi kontrol</label>
            <input type="text" name="frequency_label" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Mingguan / 2 minggu / bulanan">
          </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Poli pengampu</label>
            <select name="clinic_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
              <option value="">Pilih poli</option>
              <?php foreach($clinics as $clinic): ?>
                <option value="<?= $clinic['id'] ?>"><?= e($clinic['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal kontrol berikutnya</label>
            <input type="date" name="next_control_date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          </div>
        </div>
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan monitoring</label>
          <textarea name="notes" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" placeholder="Target terapi, parameter yang dievaluasi, instruksi kontrol, dll."></textarea>
        </div>
        <input type="hidden" name="start_date" value="<?= e(today()) ?>">
        <button class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-slate-800"><i class="fa-solid fa-plus"></i> Simpan Program Monitoring</button>
      </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between gap-4">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Ringkasan monitoring aktif</h3>
          <p class="mt-1 text-sm text-slate-500">Program aktif bisa diselesaikan atau dibatalkan bila sudah tidak relevan.</p>
        </div>
        <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600"><?= count($activePlans) ?> aktif</div>
      </div>
      <div class="space-y-3">
        <?php foreach($activePlans as $plan): ?>
          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
              <div>
                <div class="font-semibold text-slate-900"><?= e($plan['program_name']) ?></div>
                <div class="mt-1 text-sm text-slate-500"><?= e($plan['condition_name'] ?: 'Kondisi belum diisi') ?> · <?= e($plan['clinic_name'] ?: 'Poli umum') ?></div>
                <div class="mt-1 text-xs text-slate-500">Frekuensi <?= e($plan['frequency_label'] ?: '-') ?> · Kontrol berikutnya <?= e(format_date_id($plan['next_control_date'] ?? null)) ?></div>
              </div>
              <div class="flex flex-wrap gap-2">
                <form method="post" action="<?= site_url('medicalrecords/setMonitoringStatus/'.$plan['id'].'/completed') ?>" class="ajax-form" data-reset-on-success="false" data-confirm="Tandai program monitoring ini sebagai selesai?">
                  <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                  <button class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"><i class="fa-solid fa-circle-check"></i> Selesaikan</button>
                </form>
                <form method="post" action="<?= site_url('medicalrecords/setMonitoringStatus/'.$plan['id'].'/cancelled') ?>" class="ajax-form" data-reset-on-success="false" data-confirm="Batalkan program monitoring ini?">
                  <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                  <button class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100"><i class="fa-solid fa-ban"></i> Batalkan</button>
                </form>
              </div>
            </div>
            <?php if (!empty($plan['notes'])): ?><div class="mt-3 text-sm text-slate-600"><?= nl2br(e($plan['notes'])) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
        <?php if (empty($activePlans)): ?>
          <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada program monitoring aktif untuk pasien ini.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Daftar semua program monitoring</h2>
      <p class="mt-1 text-sm text-slate-500">Termasuk program yang aktif, selesai, atau dibatalkan.</p>
    </div>
    <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Total <?= count($plans) ?> program</div>
  </div>
  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="6" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Program</th>
            <th class="px-4 py-3 font-semibold">Kondisi</th>
            <th class="px-4 py-3 font-semibold">Poli</th>
            <th class="px-4 py-3 font-semibold">Kontrol</th>
            <th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold">Catatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($plans as $plan): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-800"><?= e($plan['program_name']) ?></div>
                <div class="text-xs text-slate-500">Mulai <?= e(format_date_id($plan['start_date'])) ?></div>
              </td>
              <td class="px-4 py-3 text-slate-700"><?= e($plan['condition_name'] ?: '-') ?></td>
              <td class="px-4 py-3 text-slate-700"><?= e($plan['clinic_name'] ?: '-') ?></td>
              <td class="px-4 py-3 text-slate-700">
                <div><?= e($plan['frequency_label'] ?: '-') ?></div>
                <div class="text-xs text-slate-500">Berikutnya <?= e(format_date_id($plan['next_control_date'] ?? null)) ?></div>
              </td>
              <td class="px-4 py-3"><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?= $badgeMap[$plan['status']] ?? 'bg-slate-100 text-slate-700 ring-slate-600/20' ?>"><?= e(ucfirst($plan['status'])) ?></span></td>
              <td class="px-4 py-3 text-slate-700 max-w-sm"><?= e($plan['notes'] ?: '-') ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($plans)): ?>
            <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada program monitoring pasien.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="mt-6 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
      <h2 class="text-lg font-bold text-slate-900">Timeline Rekam Medis</h2>
      <p class="mt-1 text-sm text-slate-500">Riwayat longitudinal semua kunjungan, vital sign, diagnosis, SOAP, tindakan, resep, dan rencana kontrol.</p>
    </div>
    <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Total <?= count($timeline) ?> kunjungan</div>
  </div>

  <div class="space-y-5">
    <?php foreach($timeline as $row): ?>
      <article class="rounded-3xl border border-slate-200 p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <h3 class="text-lg font-bold text-slate-900"><?= e(format_datetime_id($row['visit_date'])) ?></h3>
              <?= status_badge($row['status']) ?>
              <?php if (!empty($row['special_condition'])): ?><span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Kondisi khusus</span><?php endif; ?>
            </div>
            <div class="mt-1 text-sm text-slate-500">Poli <?= e($row['clinic_name'] ?: '-') ?> · Dokter <?= e($row['doctor_name'] ?: '-') ?> · Antrian <?= e($row['queue_number'] ?: '-') ?></div>
          </div>
          <a href="<?= site_url('visits/show/'.$row['id']) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-brand-200 hover:text-brand-700"><i class="fa-solid fa-stethoscope"></i> Buka Kunjungan</a>
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-12">
          <div class="rounded-2xl bg-slate-50 p-4 xl:col-span-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ringkasan klinis</div>
            <div class="mt-3 space-y-2 text-sm text-slate-700">
              <div><span class="font-semibold text-slate-900">Keluhan awal:</span> <?= nl2br(e($row['complaint'] ?: '-')) ?></div>
              <div><span class="font-semibold text-slate-900">Diagnosis utama:</span> <?= e($row['diagnosis_name'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">Diagnosis sekunder:</span> <?= nl2br(e($row['diagnosis_secondary'] ?: '-')) ?></div>
              <div><span class="font-semibold text-slate-900">ICD:</span> <?= e($row['icd_code'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">Alergi terkonfirmasi:</span> <?= nl2br(e($row['allergy_confirmation'] ?: '-')) ?></div>
            </div>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4 xl:col-span-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Vital sign</div>
            <div class="mt-3 grid gap-2 text-sm text-slate-700 sm:grid-cols-2">
              <div><span class="font-semibold text-slate-900">TD:</span> <?= e($row['blood_pressure'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">Suhu:</span> <?= e($row['temperature'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">BB:</span> <?= e($row['weight'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">TB:</span> <?= e($row['height'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">Nadi:</span> <?= e($row['pulse'] ?: '-') ?></div>
              <div><span class="font-semibold text-slate-900">Rencana kontrol:</span> <?= e(format_date_id($row['next_control_date'] ?? null)) ?></div>
            </div>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4 xl:col-span-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tindakan & resep</div>
            <div class="mt-3 space-y-2 text-sm text-slate-700">
              <div><span class="font-semibold text-slate-900">Layanan:</span> <?= nl2br(e($row['services_text'] ?: '-')) ?></div>
              <div><span class="font-semibold text-slate-900">Obat:</span> <?= nl2br(e($row['medicines_text'] ?: '-')) ?></div>
              <div><span class="font-semibold text-slate-900">Tindakan:</span> <?= nl2br(e($row['procedure_notes'] ?: $row['treatment_notes'] ?: '-')) ?></div>
              <div><span class="font-semibold text-slate-900">Rujukan:</span> <?= nl2br(e($row['referral_notes'] ?: '-')) ?></div>
            </div>
          </div>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">SOAP & anamnesis</div>
            <div class="space-y-3 text-sm text-slate-700">
              <div><span class="font-semibold text-slate-900">Anamnesis:</span><div class="mt-1"><?= nl2br(e($row['anamnesis'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Riwayat keluhan sekarang:</span><div class="mt-1"><?= nl2br(e($row['complaint_history'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Subjective:</span><div class="mt-1"><?= nl2br(e($row['subjective_notes'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Objective:</span><div class="mt-1"><?= nl2br(e($row['objective_notes'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">SOAP lama:</span><div class="mt-1"><?= nl2br(e($row['soap_notes'] ?: '-')) ?></div></div>
            </div>
          </div>
          <div class="rounded-2xl border border-slate-200 p-4">
            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Assessment, plan, dan penunjang</div>
            <div class="space-y-3 text-sm text-slate-700">
              <div><span class="font-semibold text-slate-900">Pemeriksaan fisik:</span><div class="mt-1"><?= nl2br(e($row['physical_exam'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Assessment:</span><div class="mt-1"><?= nl2br(e($row['assessment_notes'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Plan:</span><div class="mt-1"><?= nl2br(e($row['plan_notes'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Lab:</span><div class="mt-1"><?= nl2br(e($row['lab_notes'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Radiologi:</span><div class="mt-1"><?= nl2br(e($row['radiology_notes'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Flag kondisi:</span><div class="mt-1"><?= nl2br(e($row['condition_flags'] ?: '-')) ?></div></div>
              <div><span class="font-semibold text-slate-900">Rencana kontrol/edukasi:</span><div class="mt-1"><?= nl2br(e($row['control_plan'] ?: '-')) ?></div></div>
              <?php if (!empty($row['special_condition_details'])): ?><div><span class="font-semibold text-slate-900">Detail kondisi khusus:</span><div class="mt-1"><?= nl2br(e($row['special_condition_details'])) ?></div></div><?php endif; ?>
            </div>
          </div>
        </div>
      </article>
    <?php endforeach; ?>

    <?php if (empty($timeline)): ?>
      <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500">Belum ada riwayat kunjungan yang tersimpan untuk pasien ini.</div>
    <?php endif; ?>
  </div>
</section>

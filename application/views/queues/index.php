<?php
$waiting = $called = $examined = $pending = 0;
foreach ($queues as $row) {
  if ($row['status'] === 'waiting') $waiting++;
  if ($row['status'] === 'called') $called++;
  if ($row['status'] === 'examined') $examined++;
  if ($row['status'] === 'pending') $pending++;
}
$isFrontOffice = role_in(['front_office','branch_admin','super_admin']);
$canExam = role_in(['doctor','nurse','branch_admin','super_admin']);
?>
<div class="mb-6">
  <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Antrian Pasien</h1>
  
</div>

<div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Menunggu</div><div class="mt-1 text-3xl font-extrabold text-slate-900"><?= $waiting ?></div></div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Dipanggil</div><div class="mt-1 text-3xl font-extrabold text-slate-900"><?= $called ?></div></div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Diperiksa</div><div class="mt-1 text-3xl font-extrabold text-slate-900"><?= $examined ?></div></div>
  <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Pending</div><div class="mt-1 text-3xl font-extrabold text-slate-900"><?= $pending ?></div></div>
</div>

<div class="grid gap-6 <?= $isFrontOffice ? 'xl:grid-cols-3' : 'xl:grid-cols-1' ?>">
  <?php if($isFrontOffice): ?>
  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-1">
    <div class="mb-4">
      <h2 class="text-lg font-bold text-slate-900">Daftarkan ke Antrian</h2>
      
    </div>
    <form method="post" action="<?= site_url('queues/store') ?>" class="ajax-form space-y-4">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Pasien</label>
        <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="patient_id" data-suggest-select="patient" data-suggest-placeholder="Ketik nama pasien / RM / NIK..." required>
          <option value="">Pilih Pasien Terdaftar</option>
          <?php foreach($patients as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['medical_record_no'].' · '.$p['name'].' · '.$p['nik']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Poli</label>
        <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="clinic_id" required>
          <option value="">Pilih Poli</option>
          <?php foreach($clinics as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Jenis Kunjungan</label>
        <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="visit_type">
          <option value="umum">Umum</option>
          <option value="rujukan">Rujukan</option>
          <option value="kontrol">Kontrol</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Keluhan Awal</label>
        <textarea class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="complaint" placeholder="Keluhan utama pasien"></textarea>
      </div>
      
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-ticket"></i> Ambil Nomor Antrian</button>
    </form>
  </section>
  <?php endif; ?>

  <div class="space-y-6 <?= $isFrontOffice ? 'xl:col-span-2' : 'xl:col-span-1' ?>">
    <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <h2 class="text-lg font-bold text-slate-900">Antrian Aktif Hari Ini</h2>
          
        </div>
        <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600">Total <?= count($queues) ?> antrian aktif</div>
      </div>
      <div class="overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto scrollbar-soft">
          <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
              <tr>
                <th class="px-4 py-3 font-semibold">No</th>
                <th class="px-4 py-3 font-semibold">Pasien</th>
                <th class="px-4 py-3 font-semibold">Poli</th>
                <th class="px-4 py-3 font-semibold">Status Antrian</th>
                <th class="px-4 py-3 font-semibold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php foreach($queues as $q): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3"><span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-50 font-bold text-sky-700"><?= e($q['queue_number']) ?></span></td>
                  <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800"><?= e($q['patient_name']) ?></div>
                    <div class="text-xs text-slate-500">NIK <?= e($q['patient_nik']) ?> · Kunjungan <?= e(status_label($q['visit_status'])) ?></div>
                  </td>
                  <td class="px-4 py-3 text-slate-700"><div class="font-semibold"><?= e($q['clinic_name']) ?></div></td>
                  <td class="px-4 py-3"><?= status_badge($q['status']) ?></td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex flex-wrap justify-end gap-2">
                      <?php if($canExam && $q['visit_id'] && in_array($q['status'], ['called','examined'], true)): ?>
                        <form method="post" action="<?= site_url('queues/startExam/'.$q['visit_id']) ?>" class="ajax-form" data-reset-on-success="false">
                          <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                          <button class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-sky-700">
                            <i class="fa-solid fa-stethoscope"></i> <?= $q['status'] === 'examined' ? 'Lanjutkan' : 'Mulai Pemeriksaan' ?>
                          </button>
                        </form>
                      <?php endif; ?>

                      <?php if($isFrontOffice && $q['status'] === 'called'): ?>
                        <form method="post" action="<?= site_url('queues/markPending/'.$q['id']) ?>" class="ajax-form" data-confirm="Ubah antrian ini menjadi pending?" data-reset-on-success="false">
                          <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                          <button class="inline-flex items-center gap-2 rounded-xl border border-orange-200 bg-white px-3 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-50">
                            <i class="fa-solid fa-clock"></i> Pending
                          </button>
                        </form>
                      <?php endif; ?>

                      <?php if($isFrontOffice && $q['status'] === 'pending'): ?>
                        <form method="post" action="<?= site_url('queues/recall/'.$q['id']) ?>" class="ajax-form" data-confirm="Aktifkan kembali antrian ini?" data-reset-on-success="false">
                          <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                          <button class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50">
                            <i class="fa-solid fa-rotate"></i> Aktifkan Lagi
                          </button>
                        </form>
                      <?php endif; ?>

                      <?php if(($isFrontOffice && !in_array($q['status'], ['called','pending'], true)) || (!$isFrontOffice && !$canExam) || ($canExam && !in_array($q['status'], ['called','examined'], true))): ?>
                        <span class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500">-</span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if(empty($queues)): ?><tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Tidak ada antrian aktif hari ini.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
</div>

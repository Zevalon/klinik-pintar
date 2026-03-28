<div class="mb-6 flex items-end justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Pemeriksaan & Diagnosa</h1>
    
  </div>
  <div class="rounded-2xl bg-white px-4 py-3 text-sm text-slate-500 shadow-soft ring-1 ring-slate-200">Total <?= count($visits) ?> kunjungan aktif</div>
</div>

<div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
  <div class="overflow-hidden rounded-2xl border border-slate-200">
    <div class="overflow-x-auto scrollbar-soft">
      <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
          <tr>
            <th class="px-4 py-3 font-semibold">Tanggal</th>
            <th class="px-4 py-3 font-semibold">Pasien</th>
            <th class="px-4 py-3 font-semibold">Poli</th>
            <th class="px-4 py-3 font-semibold">Antrian</th>
            <th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach($visits as $v): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 text-slate-700"><?= e(format_datetime_id($v['visit_date'])) ?></td>
            <td class="px-4 py-3">
              <div class="font-semibold text-slate-800"><?= e($v['patient_name']) ?></div>
              <div class="text-xs text-slate-500">Kunjungan <?= e(patient_type_label($v['visit_type'])) ?></div>
            </td>
            <td class="px-4 py-3 text-slate-700"><?= e($v['clinic_name']) ?></td>
            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700"><?= e($v['queue_number']) ?></span></td>
            <td class="px-4 py-3">
              <div><?= status_badge($v['queue_status'] ?: $v['status']) ?></div>
              <div class="mt-1 text-xs text-slate-500">Visit <?= e(status_label($v['status'])) ?></div>
            </td>
            <td class="px-4 py-3 text-right">
              <?php if(in_array($v['queue_status'], ['called','examined'], true)): ?>
                <a class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-sky-700" href="<?= site_url('visits/show/'.$v['id']) ?>"><i class="fa-solid fa-stethoscope"></i> Pemeriksaan</a>
              <?php else: ?>
                <span class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500">Menunggu dipanggil</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($visits)): ?><tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada kunjungan aktif.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

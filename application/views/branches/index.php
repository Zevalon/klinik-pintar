<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Manajemen Cabang</h1>
    
  </div>
</div>

<div class="ajax-form grid gap-6 xl:grid-cols-3">
  <?php if(role_in(['super_admin'])): ?>
  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-1">
    <div class="mb-4">
      <h2 class="text-lg font-bold text-slate-900">Tambah Cabang Baru</h2>
      
    </div>
    <form method="post" action="<?= site_url('branches/store') ?>" class="ajax-form space-y-4">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Nama cabang</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="name" placeholder="Klinik Pintar Palembang" required>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Kota</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="city" placeholder="Palembang">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Telepon</label>
        <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="phone" placeholder="0711-xxxxxx">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-600">Alamat</label>
        <textarea class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="address" placeholder="Alamat lengkap cabang"></textarea>
      </div>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-plus"></i> Simpan Cabang</button>
    </form>
  </section>
  <?php endif; ?>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 <?= role_in(['super_admin']) ? 'xl:col-span-2' : 'xl:col-span-3' ?>">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-bold text-slate-900">Daftar Cabang</h2>
        
      </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
      <?php foreach($branches as $branch): ?>
        <div class="rounded-3xl border border-slate-200 p-5 transition hover:-translate-y-0.5 hover:shadow-soft">
          <div class="mb-4 flex items-start justify-between gap-4">
            <div>
              <div class="text-lg font-bold text-slate-900"><?= e($branch['name']) ?></div>
              <div class="text-sm text-slate-500"><?= e($branch['city']) ?> · <?= e($branch['phone']) ?></div>
            </div>
            <?php if($active_branch_id == $branch['id']): ?>
              <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Cabang Aktif</span>
            <?php endif; ?>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-slate-50 p-4">
              <div class="text-xs uppercase tracking-wide text-slate-500">Kunjungan Hari Ini</div>
              <div class="mt-1 text-2xl font-bold text-slate-900"><?= e($branch['visits_today']) ?></div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
              <div class="text-xs uppercase tracking-wide text-slate-500">Revenue Hari Ini</div>
              <div class="mt-1 text-lg font-bold text-slate-900"><?= currency($branch['revenue_today']) ?></div>
            </div>
          </div>
          <div class="mt-4 text-sm text-slate-600"><?= e($branch['address']) ?></div>
          <div class="mt-5">
            <a href="<?= site_url('branches/switch/'.$branch['id']) ?>" class="inline-flex items-center gap-2 rounded-2xl <?= $active_branch_id == $branch['id'] ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-sky-600 hover:bg-sky-700' ?> px-4 py-2.5 text-sm font-semibold text-white shadow">
              <i class="fa-solid fa-location-arrow"></i> <?= $active_branch_id == $branch['id'] ? 'Sedang Aktif' : 'Pilih Cabang Ini' ?>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

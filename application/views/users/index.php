<div class="mb-6">
  <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">User & Hak Akses</h1>
  
</div>

<div class="ajax-form grid gap-6 xl:grid-cols-3">
  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-1">
    <div class="mb-4">
      <h2 class="text-lg font-bold text-slate-900">Tambah User</h2>
      
    </div>
    <form method="post" action="<?= site_url('users/store') ?>" class="ajax-form space-y-4">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="name" placeholder="Nama lengkap" required>
      <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="username" placeholder="Username" required>
      <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" type="email" name="email" placeholder="Email">
      <input class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" type="password" name="password" placeholder="Password" required>
      <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="role_id">
        <?php foreach($roles as $r): ?><option value="<?= $r['id'] ?>"><?= e($r['name']) ?></option><?php endforeach; ?>
      </select>
      <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100" name="branch_id">
        <option value="">Ikuti cabang aktif / global</option>
        <?php foreach($branches as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option><?php endforeach; ?>
      </select>
      <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-user-plus"></i> Simpan User</button>
    </form>
  </section>

  <section class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200 xl:col-span-2">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-bold text-slate-900">Daftar User</h2>
        
      </div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200">
      <div class="overflow-x-auto scrollbar-soft">
        <table data-datatable="true" data-page-size="8" class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
              <th class="px-4 py-3 font-semibold">Nama</th>
              <th class="px-4 py-3 font-semibold">Username</th>
              <th class="px-4 py-3 font-semibold">Role</th>
              <th class="px-4 py-3 font-semibold">Cabang</th>
              <th class="px-4 py-3 font-semibold">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php foreach($users as $u): ?>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3">
                  <div class="font-semibold text-slate-800"><?= e($u['name']) ?></div>
                  <div class="text-xs text-slate-500"><?= e($u['email']) ?></div>
                </td>
                <td class="px-4 py-3 text-slate-700"><?= e($u['username']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($u['role_name']) ?></td>
                <td class="px-4 py-3 text-slate-700"><?= e($u['branch_name'] ?: 'Global') ?></td>
                <td class="px-4 py-3">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $u['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>"><?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

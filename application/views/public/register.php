<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pendaftaran Pasien Web</title>
  <script>tailwind=window.tailwind||{};tailwind.config={theme:{extend:{colors:{brand:{50:'#eef8ff',100:'#d9f0ff',500:'#0ea5e9',600:'#0284c7',700:'#0369a1'}},boxShadow:{soft:'0 10px 30px rgba(15,23,42,.08)'}}}};</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>body{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
  <div class="mb-8 grid gap-6 lg:grid-cols-2 lg:items-center">
    <div><div class="mb-4 inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700"><i class="fa-solid fa-heart-pulse"></i> Pendaftaran Web Klinik Pintar</div><h1 class="text-4xl font-black tracking-tight text-slate-900">Daftar kunjungan pasien langsung dari web.</h1></div>
    <div class="grid gap-4 sm:grid-cols-3"><div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Langkah 1</div><div class="mt-1 font-bold text-slate-900">Cari pasien</div></div><div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Langkah 2</div><div class="mt-1 font-bold text-slate-900">Pilih cabang & poli</div></div><div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200"><div class="text-sm text-slate-500">Langkah 3</div><div class="mt-1 font-bold text-slate-900">Kirim pendaftaran</div></div></div>
  </div>

  <div class="mb-6 rounded-[2rem] bg-white p-6 shadow-soft ring-1 ring-slate-200 lg:p-8">
    <div class="mb-4"><h2 class="text-xl font-bold text-slate-900">Cek Pasien Terdaftar</h2></div>
    <div class="grid gap-4 lg:grid-cols-[240px_1fr_auto]">
      <select id="search_branch_id" class="rounded-2xl border border-slate-200 px-4 py-3"><option value="">Pilih cabang</option><?php foreach($branch->allActive() as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['name']) ?> - <?= e($b['city']) ?></option><?php endforeach; ?></select>
      <input id="search_keyword" class="rounded-2xl border border-slate-200 px-4 py-3" placeholder="Cari nama / NIK / HP / No RM">
      <button type="button" id="btn-search-patient" class="rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white">Cari</button>
    </div>
    <div id="search-result" class="mt-4 hidden rounded-3xl border border-slate-200 bg-slate-50 p-4"></div>
  </div>

  <div class="rounded-[2rem] bg-white p-6 shadow-soft ring-1 ring-slate-200 lg:p-8">
    <form method="post" action="<?= site_url('daftar/simpan') ?>" class="grid gap-5 lg:grid-cols-2">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div><label class="mb-1 block text-sm font-medium text-slate-600">Nama lengkap</label><input id="f_name" class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="name" placeholder="Nama lengkap" required></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">NIK</label><input id="f_nik" class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="nik" placeholder="Nomor induk kependudukan"></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">Gender</label><select id="f_gender" class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="gender"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">Tanggal lahir</label><input id="f_birth_date" class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="date" name="birth_date"></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">No. HP</label><input id="f_phone" class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="phone" placeholder="Nomor HP aktif"></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">Tipe pasien</label><select id="f_patient_type" class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="patient_type"><option value="umum">Umum</option><option value="rujukan">Rujukan</option><option value="kontrol">Kontrol</option></select></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">Cabang</label><select id="f_branch_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="branch_id" required><option value="">Pilih cabang</option><?php foreach($branch->allActive() as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['name']) ?> - <?= e($b['city']) ?></option><?php endforeach; ?></select></div>
      <div><label class="mb-1 block text-sm font-medium text-slate-600">Poli</label><select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="clinic_id" required><option value="">Pilih poli</option><?php foreach($clinic->allActive() as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?> (Cabang <?= e($c['branch_id']) ?>)</option><?php endforeach; ?></select></div>
      <div class="lg:col-span-2"><label class="mb-1 block text-sm font-medium text-slate-600">Alamat</label><textarea id="f_address" class="min-h-[110px] w-full rounded-2xl border border-slate-200 px-4 py-3" name="address" placeholder="Alamat lengkap"></textarea></div>
      <div class="lg:col-span-2"><label class="mb-1 block text-sm font-medium text-slate-600">Keluhan utama</label><textarea class="min-h-[120px] w-full rounded-2xl border border-slate-200 px-4 py-3" name="complaint" placeholder="Tuliskan keluhan utama pasien"></textarea></div>
      <div class="lg:col-span-2 flex flex-wrap items-center gap-3 pt-2"><button class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700"><i class="fa-solid fa-paper-plane"></i> Daftar Sekarang</button><a href="<?= site_url('login') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-brand-200 hover:text-brand-700"><i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard</a></div>
    </form>
  </div>
</div>
<script>
document.getElementById('btn-search-patient').addEventListener('click', async function(){
  var branchId=document.getElementById('search_branch_id').value;
  var keyword=document.getElementById('search_keyword').value;
  var result=document.getElementById('search-result');
  var res=await fetch('<?= site_url('daftar/cari-pasien') ?>?branch_id='+encodeURIComponent(branchId)+'&keyword='+encodeURIComponent(keyword),{headers:{'X-Requested-With':'XMLHttpRequest'}});
  var data=await res.json();
  result.classList.remove('hidden');
  if(!data.data||!data.data.length){result.innerHTML='<div class="text-sm text-slate-600">Pasien belum ditemukan. Silakan lanjut isi data pasien baru.</div>';return;}
  result.innerHTML=data.data.map(function(p){return '<button type="button" class="select-patient mb-3 block w-full rounded-2xl border border-slate-200 bg-white p-4 text-left hover:border-brand-300" data-patient=\''+JSON.stringify(p).replace(/'/g,'&#39;')+'\'><div class="font-semibold text-slate-900">'+p.name+' <span class="text-xs text-slate-500">('+p.medical_record_no+')</span></div><div class="text-sm text-slate-600">NIK: '+(p.nik||'-')+' · HP: '+(p.phone||'-')+'</div></button>';}).join('');
});
document.addEventListener('click', function(e){var btn=e.target.closest('.select-patient');if(!btn)return;var p=JSON.parse(btn.dataset.patient);document.getElementById('f_name').value=p.name||'';document.getElementById('f_nik').value=p.nik||'';document.getElementById('f_gender').value=p.gender||'L';document.getElementById('f_birth_date').value=p.birth_date||'';document.getElementById('f_phone').value=p.phone||'';document.getElementById('f_address').value=p.address||'';document.getElementById('f_patient_type').value=p.patient_type||'umum';document.getElementById('f_branch_id').value=document.getElementById('search_branch_id').value;window.scrollTo({top:document.body.scrollHeight/3,behavior:'smooth'});});
</script>
</body>
</html>

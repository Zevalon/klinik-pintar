<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(app_config('app_name')) ?></title>
  <script>
    tailwind = window.tailwind || {};
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#eef8ff',
              100: '#d9f0ff',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              900: '#082f49'
            }
          },
          boxShadow: {
            soft: '0 10px 30px rgba(15, 23, 42, 0.08)'
          }
        }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    :root { color-scheme: light; }
    body {
      font-family: 'Inter', sans-serif;
      background:
        radial-gradient(circle at top left, rgba(14,165,233,.12), transparent 22%),
        radial-gradient(circle at top right, rgba(56,189,248,.12), transparent 18%),
        linear-gradient(180deg, #f8fbff 0%, #f1f5f9 48%, #eef2ff 100%);
    }
    .app-shell { position: relative; isolation: isolate; }
    .app-shell::before,
    .app-shell::after {
      content: '';
      position: fixed;
      z-index: -1;
      width: 22rem;
      height: 22rem;
      border-radius: 999px;
      filter: blur(48px);
      opacity: .45;
      pointer-events: none;
    }
    .app-shell::before { top: -5rem; right: -6rem; background: rgba(14,165,233,.22); }
    .app-shell::after { bottom: -8rem; left: -7rem; background: rgba(59,130,246,.18); }
    .scrollbar-soft::-webkit-scrollbar { width: 8px; height: 8px; }
    .scrollbar-soft::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .bg-brand-600 { background-color: #0284c7 !important; }
    .bg-brand-700 { background-color: #0369a1 !important; }
    .bg-brand-50 { background-color: #eef8ff !important; }
    .text-brand-600 { color: #0284c7 !important; }
    .text-brand-700 { color: #0369a1 !important; }
    .text-brand-900 { color: #082f49 !important; }
    .border-brand-100 { border-color: #d9f0ff !important; }
    .border-brand-200 { border-color: #bae6fd !important; }
    .ring-brand-100 { --tw-ring-color: #d9f0ff !important; }
    .focus\:border-brand-500:focus { border-color: #0ea5e9 !important; }
    .focus\:ring-brand-100:focus { --tw-ring-color: #d9f0ff !important; }
    .hover\:bg-brand-700:hover { background-color: #0369a1 !important; }
    .hover\:text-brand-700:hover { color: #0369a1 !important; }
    .hover\:border-brand-200:hover { border-color: #bae6fd !important; }
    a, button { transition: all .18s ease; }
    .surface-light,
    [class~='bg-white'],
    [class~='bg-slate-50'],
    [class~='bg-slate-100'],
    [class~='bg-brand-50'],
    [class~='bg-sky-50'],
    [class~='bg-emerald-50'],
    [class~='bg-rose-50'],
    [class~='bg-amber-50'] { color: #0f172a; }
    .surface-dark,
    [class~='bg-slate-900'],
    [class~='bg-slate-950'],
    [class~='bg-black'],
    [class*='bg-gradient-to-'],
    [class~='bg-brand-500'],
    [class~='bg-brand-600'],
    [class~='bg-brand-700'],
    [class~='bg-sky-500'],
    [class~='bg-sky-600'],
    [class~='bg-cyan-400'],
    [class~='bg-emerald-500'],
    [class~='bg-emerald-600'],
    [class~='bg-rose-500'],
    [class~='bg-rose-600'],
    [class~='bg-amber-500'],
    [class~='bg-amber-600'] { color: #fff; }
    a[class~='bg-white'], button[class~='bg-white'],
    a[class~='bg-slate-50'], button[class~='bg-slate-50'],
    a[class~='bg-slate-100'], button[class~='bg-slate-100'],
    a[class~='bg-brand-50'], button[class~='bg-brand-50'],
    a[class~='bg-sky-50'], button[class~='bg-sky-50'],
    a[class~='bg-emerald-50'], button[class~='bg-emerald-50'],
    a[class~='bg-rose-50'], button[class~='bg-rose-50'],
    a[class~='bg-amber-50'], button[class~='bg-amber-50'] { color: #334155; }
    a[class~='bg-slate-900'], button[class~='bg-slate-900'],
    a[class~='bg-slate-950'], button[class~='bg-slate-950'],
    a[class*='bg-gradient-to-'], button[class*='bg-gradient-to-'],
    a[class~='bg-brand-500'], button[class~='bg-brand-500'],
    a[class~='bg-brand-600'], button[class~='bg-brand-600'],
    a[class~='bg-brand-700'], button[class~='bg-brand-700'],
    a[class~='bg-sky-500'], button[class~='bg-sky-500'],
    a[class~='bg-sky-600'], button[class~='bg-sky-600'],
    a[class~='bg-emerald-500'], button[class~='bg-emerald-500'],
    a[class~='bg-emerald-600'], button[class~='bg-emerald-600'],
    a[class~='bg-rose-500'], button[class~='bg-rose-500'],
    a[class~='bg-rose-600'], button[class~='bg-rose-600'],
    a[class~='bg-amber-500'], button[class~='bg-amber-500'],
    a[class~='bg-amber-600'], button[class~='bg-amber-600'] { color: #fff; }
    .sidebar-profile-link {
      background: rgba(255,255,255,.06);
      border-color: rgba(255,255,255,.12);
      color: #fff !important;
    }
    .sidebar-profile-link:hover {
      background: rgba(255,255,255,.12);
      color: #fff !important;
    }
    .profile-hero-card {
      background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 55%, #22d3ee 100%);
      color: #fff;
    }
    .btn-disabled-fix[disabled] { opacity: .55; color: #64748b !important; }
    main input:not([type='checkbox']):not([type='radio']):not([type='hidden']),
    main select,
    main textarea {
      background-color: rgba(255,255,255,.98);
      box-shadow: 0 1px 2px rgba(15,23,42,.02);
      transition: box-shadow .18s ease, border-color .18s ease, transform .18s ease;
    }
    main input:not([type='checkbox']):not([type='radio']):not([type='hidden']):focus,
    main select:focus,
    main textarea:focus {
      box-shadow: 0 0 0 4px rgba(14,165,233,.10), 0 10px 20px rgba(15,23,42,.05);
      transform: translateY(-1px);
    }
    main table thead th { letter-spacing: .02em; }
    main table tbody tr { transition: background-color .18s ease, transform .18s ease; }
    main table tbody tr:hover { transform: translateY(-1px); }
    #app-main-content > section,
    #app-main-content > div.rounded-3xl,
    #app-main-content > div.rounded-\[2rem\],
    #app-main-content > div.rounded-\[30px\] {
      backdrop-filter: blur(10px);
    }
  </style>
</head>
<body class="min-h-screen text-slate-800">
<?php
  $user = current_user();
  $menuSections = sidebar_menu_sections_for_role($user['role_code'] ?? '');
?>
<div class="app-shell flex min-h-screen">
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full overflow-y-auto border-r border-slate-200 bg-slate-950 px-5 py-6 text-white shadow-2xl transition-transform duration-300 lg:translate-x-0 scrollbar-soft">
    <div class="mb-8 flex items-center gap-3">
      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-cyan-400 text-xl text-white shadow-lg">
        <i class="fa-solid fa-heart-pulse"></i>
      </div>
      <div>
        <div class="text-xl font-extrabold tracking-tight"><?= e(app_config('app_name')) ?></div>
      </div>
    </div>

    <div class="mb-5 rounded-2xl border border-slate-800 bg-slate-900/80 p-4">
      <div class="flex items-center gap-3">
        <?= render_user_avatar($user, 'h-11 w-11 rounded-2xl ring-1 ring-white/10', 'text-base') ?>
        <div>
          <div class="font-semibold leading-tight"><?= e($user['name'] ?? '-') ?></div>
          <div class="text-xs text-slate-400"><?= e(role_label($user['role_code'] ?? '')) ?></div>
        </div>
      </div>
      <div class="mt-4 grid gap-2 text-xs text-slate-300">
        <div class="flex items-center gap-2"><i class="fa-solid fa-building text-brand-300"></i><span><?= e(current_branch_name()) ?></span></div>
        <div class="flex items-center gap-2"><i class="fa-solid fa-calendar-day text-brand-300"></i><span><?= e(date('d M Y')) ?></span></div>
      </div>
      <a href="<?= site_url('profile') ?>" class="sidebar-profile-link mt-4 inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-semibold">
        <i class="fa-solid fa-id-card-clip"></i> Kelola Profil Saya
      </a>
    </div>

    <nav class="space-y-5">
      <?php foreach ($menuSections as $sectionLabel => $items): ?>
        <div>
          <div class="mb-2 flex items-center gap-3 px-3">
            <div class="h-px flex-1 bg-slate-800"></div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500"><?= e($sectionLabel) ?></div>
            <div class="h-px flex-1 bg-slate-800"></div>
          </div>
          <div class="space-y-1.5">
            <?php foreach ($items as $item): $active = nav_active($item['prefix']) || ($item['prefix'] === 'dashboard' && current_path() === ''); ?>
              <a href="<?= $item['url'] ?>" class="group flex items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition <?= $active ? 'bg-gradient-to-r from-brand-500 to-cyan-400 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-900 hover:text-white' ?>">
                <span class="flex items-center gap-3">
                  <i class="<?= e($item['icon']) ?> w-5 text-center <?= $active ? 'text-white' : 'text-slate-500 group-hover:text-white' ?>"></i>
                  <span><?= e($item['label']) ?></span>
                </span>
                <?php if ($active): ?>
                  <span class="h-2.5 w-2.5 rounded-full bg-white/90 shadow-[0_0_0_4px_rgba(255,255,255,0.14)]"></span>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </nav>

    <div class="mt-8 rounded-2xl border border-brand-500/20 bg-brand-500/10 p-4 text-sm text-brand-50">
      <div class="mb-3 font-semibold">Akses Publik</div>
      <div class="flex flex-wrap gap-2">
      <a href="<?= site_url('daftar') ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/20">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Halaman Daftar
      </a>
      <a href="<?= site_url('monitor-antrian') ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/20">
        <i class="fa-solid fa-volume-high"></i> Monitor Antrian
      </a>
    </div>
    </div>

    <a href="<?= site_url('logout') ?>" class="mt-8 flex items-center gap-3 rounded-2xl border border-slate-800 px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-900 hover:text-white">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Logout</span>
    </a>
  </aside>

  <div class="flex min-h-screen flex-1 flex-col lg:pl-72">
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/85 backdrop-blur-xl">
      <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
          <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm lg:hidden" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div>
            <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400"><?= e(current_page_section()) ?></div>
            <div class="text-xl font-extrabold tracking-tight text-slate-900"><?= e(current_page_label()) ?></div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <?php if (role_in(['super_admin','owner'])): ?>
            <a href="<?= site_url('branches') ?>" class="hidden rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:border-brand-200 hover:text-brand-700 sm:inline-flex sm:items-center sm:gap-2">
              <i class="fa-solid fa-code-branch"></i> Ganti Cabang
            </a>
          <?php endif; ?>
          <div class="hidden rounded-2xl border border-slate-200 bg-white/90 px-4 py-2 text-right shadow-sm sm:block">
            <div class="text-xs font-medium uppercase tracking-wide text-slate-400">Hari ini</div>
            <div class="text-sm font-bold text-slate-900"><?= e(date('d M Y')) ?></div>
          </div>
          <a href="<?= site_url('profile') ?>" class="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm hover:border-brand-200 md:inline-flex">
            <?= render_user_avatar($user, 'h-10 w-10 rounded-2xl', 'text-sm') ?>
            <div class="text-left">
              <div class="text-xs font-medium uppercase tracking-wide text-slate-400">Akun</div>
              <div class="text-sm font-bold text-slate-900">Profil Saya</div>
            </div>
          </a>
          <div class="rounded-2xl border border-brand-100 bg-brand-50 px-4 py-2 text-right shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wide text-brand-600">Cabang aktif</div>
            <div class="text-sm font-bold text-brand-900"><?= e(current_branch_name()) ?></div>
          </div>
        </div>
      </div>
    </header>

    <main id="app-main-content" class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
      <div class="mx-auto w-full max-w-[1500px]">
      <?php if($msg = get_flash('success')): ?>
        <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
          <i class="fa-solid fa-circle-check mt-0.5"></i>
          <div>
            <div class="font-semibold">Berhasil</div>
            <div class="text-sm"><?= e($msg) ?></div>
          </div>
        </div>
      <?php endif; ?>
      <?php if($msg = get_flash('error')): ?>
        <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 shadow-sm">
          <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
          <div>
            <div class="font-semibold">Perlu perhatian</div>
            <div class="text-sm"><?= e($msg) ?></div>
          </div>
        </div>
      <?php endif; ?>

      <?= $content ?>
      </div>
    </main>
  </div>
</div>
<div id="toast-container" class="fixed right-4 top-4 z-[9999] space-y-3"></div>
<script>
(function(){
function toast(message,type){
  type=type||'success';
  var wrap=document.getElementById('toast-container');
  if(!wrap) return;
  var div=document.createElement('div');
  div.className='max-w-sm rounded-2xl px-4 py-3 text-sm font-medium shadow-2xl ring-1 '+(type==='success'?'bg-emerald-50 text-emerald-800 ring-emerald-200':'bg-rose-50 text-rose-800 ring-rose-200');
  div.textContent=message;
  wrap.appendChild(div);
  setTimeout(function(){ div.remove(); }, 3200);
}
window.appOpenModal=function(id){ var el=document.getElementById(id); if(el) el.classList.remove('hidden'); };
window.appCloseModal=function(id){ var el=document.getElementById(id); if(el) el.classList.add('hidden'); };

function normalizeDigits(value){ return String(value||'').replace(/\D+/g,''); }
function formatPhoneJs(value){
  var digits = normalizeDigits(value);
  if(!digits) return '';
  var groups = digits.match(/.{1,4}/g) || [];
  return groups.join('-');
}
function parseMoneyJs(value){
  if(value == null) return 0;
  var raw = String(value).trim().replace(/rp|idr/ig,'').replace(/\s+/g,'');
  raw = raw.replace(/[^0-9,.-]/g,'');
  if(raw.indexOf(',') > -1 && raw.indexOf('.') > -1){
    if(raw.lastIndexOf(',') > raw.lastIndexOf('.')){
      raw = raw.replace(/\./g,'').replace(',', '.');
    } else {
      raw = raw.replace(/,/g,'');
    }
  } else if(raw.indexOf(',') > -1){
    raw = raw.replace(/\./g,'').replace(',', '.');
  } else {
    var dotCount = (raw.match(/\./g) || []).length;
    if(dotCount > 1 || (dotCount === 1 && raw.split('.')[1] && raw.split('.')[1].length === 3)) raw = raw.replace(/\./g,'');
  }
  var num = parseFloat(raw.replace(/(?!^)-/g,''));
  return isNaN(num) ? 0 : num;
}
function formatRupiahJs(value){
  var number = Number(value || 0);
  var hasFraction = Math.abs(number % 1) > 0;
  return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:hasFraction?2:0,maximumFractionDigits:hasFraction?2:0}).format(number);
}
function formatMoneyInputElement(input){
  if(!input) return;
  var digits = String(input.value || '').replace(/[^0-9,.-]/g,'');
  if(!digits){ input.value=''; return; }
  var value = parseMoneyJs(digits);
  input.value = formatRupiahJs(value).replace(/,00$/, '');
}
function formatPhoneInputElement(input){ if(input) input.value = formatPhoneJs(input.value); }
function bindInputFormatters(scope){
  (scope || document).querySelectorAll('input, textarea').forEach(function(input){
    if(input.dataset.formatterBound === '1') return;
    var name = (input.name || '').toLowerCase();
    var placeholder = (input.placeholder || '').toLowerCase();
    var isPhone = /(^|_)(phone|telp|telepon)$/.test(name) || /nomor hp|telepon|no\. hp|whatsapp/.test(placeholder) || input.id === 'f_phone';
    var isMoney = /(^|_)(buy_price|sell_price|unit_cost|amount|amount_received|discount|unit_price)$/.test(name.replace(/\[\]$/,'')) || /harga|biaya|nominal|uang tunai/.test(placeholder);
    if(isPhone){
      input.dataset.formatterBound = '1';
      input.setAttribute('inputmode','numeric');
      input.addEventListener('input', function(){ formatPhoneInputElement(input); });
      formatPhoneInputElement(input);
      return;
    }
    if(isMoney){
      input.dataset.formatterBound = '1';
      input.setAttribute('inputmode','numeric');
      input.addEventListener('input', function(){ formatMoneyInputElement(input); });
      formatMoneyInputElement(input);
    }
  });
}
window.appFormatPhone = formatPhoneJs;
window.appFormatMoneyInput = formatMoneyInputElement;
window.appBindInputFormatters = bindInputFormatters;

function renderPatientSearchResult(target, items, message){
  if(!target) return;
  if(!items || !items.length){
    target.innerHTML = '<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">'+(message||'Data pasien tidak ditemukan. Anda dapat melanjutkan pendaftaran pasien baru.')+'</div>';
    return;
  }
  var html = '<div class="rounded-2xl border border-sky-200 bg-sky-50 p-4"><div class="mb-3 font-semibold text-sky-900">Pasien yang sudah terdaftar ditemukan</div><div class="space-y-3">';
  items.forEach(function(item){
    html += '<div class="flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-sky-100 md:flex-row md:items-center md:justify-between">'
      + '<div><div class="font-semibold text-slate-900">'+(item.name||'-')+'</div>'
      + '<div class="mt-1 text-sm text-slate-500">RM '+(item.medical_record_no||'-')+' · NIK '+(item.nik||'-')+'</div>'
      + '<div class="text-sm text-slate-500">'+(window.appFormatPhone ? (window.appFormatPhone(item.phone||'') || '-') : (item.phone||'-'))+' · '+(item.birth_date||'-')+'</div></div>'
      + '<a href="'+window.APP_SITE_URL+'/queues" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-700"><i class="fa-solid fa-ticket"></i> Lanjut ke Antrian</a>'
      + '</div>';
  });
  html += '</div></div>';
  target.innerHTML = html;
}

function initPatientSearchForms(scope){
  (scope || document).querySelectorAll('.patient-search-form').forEach(function(form){
    if(form.dataset.bound === '1') return;
    form.dataset.bound = '1';
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      var target = document.querySelector(form.dataset.target || '');
      var keywordInput = form.querySelector('[name=keyword]');
      if(keywordInput && !keywordInput.value.trim()){
        toast('Masukkan nama atau NIK pasien terlebih dahulu.','error');
        return;
      }
      var btn = form.querySelector('button');
      if(btn){ btn.disabled=true; btn.dataset.originalText=btn.innerHTML; btn.innerHTML='Mencari...'; }
      try{
        var url = form.action + (form.action.indexOf('?')>-1?'&':'?') + 'keyword=' + encodeURIComponent(keywordInput.value.trim());
        var response = await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}});
        var data = await response.json();
        if(!response.ok || !data.success) throw new Error(data.message || 'Pencarian gagal.');
        renderPatientSearchResult(target, data.items || [], data.message || '');
      }catch(err){
        if(target) target.innerHTML = '<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">'+(err.message||'Pencarian gagal.')+'</div>';
      }finally{
        if(btn){ btn.disabled=false; btn.innerHTML=btn.dataset.originalText||'Cari'; }
      }
    });
  });
}

function initDataTables(scope){
  (scope || document).querySelectorAll('table[data-datatable="true"]').forEach(function(table){
    if(table.dataset.enhanced === '1') return;
    table.dataset.enhanced = '1';
    var wrapper = document.createElement('div');
    wrapper.className = 'space-y-3';
    var toolbar = document.createElement('div');
    toolbar.className = 'flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 md:flex-row md:items-center md:justify-between';
    var search = document.createElement('input');
    search.type = 'search';
    search.placeholder = 'Cari data pada tabel ini...';
    search.className = 'w-full max-w-md rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100';
    var info = document.createElement('div');
    info.className = 'text-sm text-slate-500';
    toolbar.appendChild(search);
    toolbar.appendChild(info);

    var footer = document.createElement('div');
    footer.className = 'flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 md:flex-row md:items-center md:justify-between';
    var countInfo = document.createElement('div');
    var pager = document.createElement('div');
    pager.className = 'flex items-center gap-2';
    var prev = document.createElement('button');
    prev.type='button';
    prev.className='btn-disabled-fix rounded-xl border border-slate-200 bg-white px-3 py-2 font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700';
    prev.textContent='Sebelumnya';
    var pageText = document.createElement('span');
    pageText.className = 'rounded-xl bg-white px-3 py-2 text-slate-600 ring-1 ring-slate-200';
    var next = document.createElement('button');
    next.type='button';
    next.className='btn-disabled-fix rounded-xl border border-slate-200 bg-white px-3 py-2 font-semibold text-slate-600 hover:border-brand-200 hover:text-brand-700';
    next.textContent='Berikutnya';
    pager.appendChild(prev);
    pager.appendChild(pageText);
    pager.appendChild(next);
    footer.appendChild(countInfo);
    footer.appendChild(pager);

    var outer = table.parentNode;
    outer.parentNode.insertBefore(wrapper, outer);
    wrapper.appendChild(toolbar);
    wrapper.appendChild(outer);
    wrapper.appendChild(footer);

    var tbody = table.querySelector('tbody');
    if(!tbody) return;
    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
    var pageSize = parseInt(table.dataset.pageSize || '10', 10);
    var currentPage = 1;

    function filteredRows(){
      var q = search.value.trim().toLowerCase();
      return rows.filter(function(row){
        if(row.cells.length === 1 && /belum ada|tidak ada/i.test(row.textContent)) return q === '';
        return row.textContent.toLowerCase().indexOf(q) !== -1;
      });
    }

    function render(){
      var items = filteredRows();
      var totalPages = Math.max(1, Math.ceil(items.length / pageSize));
      if(currentPage > totalPages) currentPage = totalPages;
      var start = (currentPage - 1) * pageSize;
      var end = start + pageSize;
      rows.forEach(function(row){ row.style.display = 'none'; });
      items.slice(start, end).forEach(function(row){ row.style.display = ''; });
      info.textContent = items.length + ' data ditemukan';
      countInfo.textContent = items.length ? ('Menampilkan ' + (start + 1) + ' - ' + Math.min(end, items.length) + ' dari ' + items.length + ' data') : 'Tidak ada data yang cocok';
      pageText.textContent = 'Hal. ' + currentPage + ' / ' + totalPages;
      prev.disabled = currentPage <= 1;
      next.disabled = currentPage >= totalPages;
      prev.classList.toggle('opacity-50', prev.disabled);
      next.classList.toggle('opacity-50', next.disabled);
    }

    search.addEventListener('input', function(){ currentPage = 1; render(); });
    prev.addEventListener('click', function(){ if(currentPage > 1){ currentPage--; render(); } });
    next.addEventListener('click', function(){ var totalPages = Math.max(1, Math.ceil(filteredRows().length / pageSize)); if(currentPage < totalPages){ currentPage++; render(); } });
    render();
  });
}

function closeSuggestDropdowns(except){
  document.querySelectorAll('.suggest-select-wrapper').forEach(function(wrapper){
    if(except && wrapper === except) return;
    var panel = wrapper.querySelector('.suggest-select-panel');
    var input = wrapper.querySelector('.suggest-select-input');
    if(panel) panel.classList.add('hidden');
    if(input) input.setAttribute('aria-expanded', 'false');
  });
}

function attachSuggestSelect(select){
  if(!select || select.dataset.suggestEnhanced === '1') return;
  select.dataset.suggestEnhanced = '1';
  select.classList.add('hidden');

  var wrapper = document.createElement('div');
  wrapper.className = 'suggest-select-wrapper relative';
  wrapper.dataset.role = select.dataset.suggestSelect || select.name || '';

  var input = document.createElement('input');
  input.type = 'text';
  input.autocomplete = 'off';
  input.className = 'suggest-select-input w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pr-11 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100';
  input.placeholder = select.dataset.suggestPlaceholder || ((select.options[0] && select.options[0].text) ? select.options[0].text : 'Ketik untuk mencari...');
  input.setAttribute('aria-expanded', 'false');

  var button = document.createElement('button');
  button.type = 'button';
  button.className = 'absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center text-slate-400 hover:text-slate-600';
  button.innerHTML = '<i class="fa-solid fa-chevron-down text-xs"></i>';

  var panel = document.createElement('div');
  panel.className = 'suggest-select-panel absolute z-50 mt-2 hidden max-h-64 w-full overflow-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl';
  panel.innerHTML = '<div class="rounded-xl px-3 py-2 text-sm text-slate-500">Mulai ketik untuk melihat pilihan...</div>';

  select.parentNode.insertBefore(wrapper, select);
  wrapper.appendChild(select);
  wrapper.appendChild(input);
  wrapper.appendChild(button);
  wrapper.appendChild(panel);

  function allItems(){
    return Array.prototype.slice.call(select.options).map(function(option, index){
      return {
        value: option.value,
        label: option.text,
        selected: option.selected,
        disabled: option.disabled,
        index: index,
        price: option.getAttribute('data-price') || ''
      };
    });
  }

  function selectedItem(){
    var opt = select.options[select.selectedIndex];
    return opt ? { value: opt.value, label: opt.text } : null;
  }

  function syncFromSelect(){
    var item = selectedItem();
    input.value = item && item.value ? item.label : '';
  }

  function renderItems(keyword){
    var query = (keyword || '').trim().toLowerCase();
    var items = allItems().filter(function(item){
      if(!item.value) return query === '' ? false : item.label.toLowerCase().indexOf(query) !== -1;
      return item.label.toLowerCase().indexOf(query) !== -1;
    });

    if(!items.length){
      panel.innerHTML = '<div class="rounded-xl px-3 py-2 text-sm text-slate-500">Data yang cocok tidak ditemukan.</div>';
      return;
    }

    panel.innerHTML = '';
    items.slice(0, 80).forEach(function(item){
      var option = document.createElement('button');
      option.type = 'button';
      option.className = 'flex w-full items-start justify-between gap-3 rounded-xl px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700';
      option.innerHTML = '<span class="block min-w-0 flex-1 break-words">'+item.label+'</span>' + (item.selected ? '<i class="fa-solid fa-check mt-0.5 text-xs text-sky-600"></i>' : '');
      option.addEventListener('click', function(){
        select.value = item.value;
        syncFromSelect();
        closeSuggestDropdowns();
        select.dispatchEvent(new Event('change', { bubbles: true }));
      });
      panel.appendChild(option);
    });
  }

  function openPanel(){
    closeSuggestDropdowns(wrapper);
    renderItems(input.value);
    panel.classList.remove('hidden');
    input.setAttribute('aria-expanded', 'true');
  }

  input.addEventListener('focus', openPanel);
  input.addEventListener('click', openPanel);
  input.addEventListener('input', function(){
    if(!input.value.trim()){
      select.value = '';
      select.dispatchEvent(new Event('change', { bubbles: true }));
    }
    openPanel();
  });
  input.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      closeSuggestDropdowns();
      input.blur();
      return;
    }
    if(e.key === 'Enter'){
      var first = panel.querySelector('button');
      if(first){
        e.preventDefault();
        first.click();
      }
    }
  });

  button.addEventListener('click', function(){
    if(panel.classList.contains('hidden')) openPanel();
    else closeSuggestDropdowns();
  });

  select.addEventListener('change', syncFromSelect);
  syncFromSelect();
}

window.appInitSuggestSelects = function initSuggestSelects(scope){
  var selector = 'select[data-suggest-select], select[name="patient_id"], select[name="medicine_id"], select[name="medicine_id[]"]';
  (scope || document).querySelectorAll(selector).forEach(function(select){
    attachSuggestSelect(select);
  });
}

function initPrescriptionBuilder(scope){
  var root = scope || document;
  var container = root.querySelector('#prescription-rows');
  var template = root.querySelector('#prescription-row-template');
  var addButton = root.querySelector('#btn-add-medicine-row');
  if(!container || !template || !addButton || addButton.dataset.bound === '1') return;
  addButton.dataset.bound = '1';

  addButton.addEventListener('click', function(){
    var fragment = template.content ? template.content.cloneNode(true) : null;
    if(fragment){
      container.appendChild(fragment);
      window.appInitSuggestSelects(container);
      return;
    }
    var holder = document.createElement('div');
    holder.innerHTML = template.innerHTML;
    while(holder.firstChild) container.appendChild(holder.firstChild);
    window.appInitSuggestSelects(container);
  });
}

function syncMedicinePrice(select){
  if(!select || !select.name || select.name.indexOf('medicine_id') !== 0) return;
  var row = select.closest('.prescription-row');
  if(!row) return;
  var priceInput = row.querySelector('.prescription-price');
  if(!priceInput) return;
  var option = select.options[select.selectedIndex];
  if(!option) return;
  var price = option.getAttribute('data-price') || '';
  if(price){
    priceInput.value = price;
    if(window.appFormatMoneyInput) window.appFormatMoneyInput(priceInput);
  }
}

function validateEnhancedSelects(form){
  var selects = form.querySelectorAll('select[data-suggest-enhanced="1"][required]');
  for(var i=0;i<selects.length;i++){
    var select = selects[i];
    if(select.value) continue;
    var wrapper = select.closest('.suggest-select-wrapper');
    var input = wrapper ? wrapper.querySelector('.suggest-select-input') : null;
    toast((input && input.placeholder ? input.placeholder : 'Silakan pilih data terlebih dahulu.') + '.','error');
    if(input) input.focus();
    return false;
  }
  return true;
}

async function refreshMain(){
  var response = await fetch(window.location.href,{headers:{'X-Requested-With':'XMLHttpRequest'}});
  var html = await response.text();
  var parser = new DOMParser();
  var doc = parser.parseFromString(html,'text/html');
  var next = doc.getElementById('app-main-content');
  var current = document.getElementById('app-main-content');
  if(next && current){
    current.innerHTML = next.innerHTML;
    initDataTables(current);
    initPatientSearchForms(current);
    window.appInitSuggestSelects(current);
    initPrescriptionBuilder(current);
    bindInputFormatters(current);
  }
}

document.addEventListener('click', function(e){
  var openBtn = e.target.closest('[data-modal-open]');
  if(openBtn){
    e.preventDefault();
    appOpenModal(openBtn.getAttribute('data-modal-open'));
  }

  var closeBtn = e.target.closest('[data-modal-close]');
  if(closeBtn){
    e.preventDefault();
    appCloseModal(closeBtn.getAttribute('data-modal-close'));
  }

  var patientEdit = e.target.closest('[data-patient-edit]');
  if(patientEdit){
    e.preventDefault();
    var data = JSON.parse(patientEdit.getAttribute('data-patient-edit'));
    var form = document.getElementById('patient-edit-form');
    if(form){
      form.setAttribute('action', patientEdit.getAttribute('href'));
      form.querySelector('[name=name]').value = data.name || '';
      form.querySelector('[name=nik]').value = data.nik || '';
      form.querySelector('[name=gender]').value = data.gender || 'L';
      form.querySelector('[name=birth_date]').value = data.birth_date || '';
      form.querySelector('[name=phone]').value = data.phone || '';
      if(window.appBindInputFormatters) window.appBindInputFormatters(form);
      form.querySelector('[name=address]').value = data.address || '';
      form.querySelector('[name=patient_type]').value = data.patient_type || 'umum';
      appOpenModal('patientEditModal');
    }
  }

  var clinicEdit = e.target.closest('[data-clinic-edit]');
  if(clinicEdit){
    e.preventDefault();
    var clinicData = JSON.parse(clinicEdit.getAttribute('data-clinic-edit'));
    var clinicForm = document.getElementById('clinic-edit-form');
    var queueStateLabelMap = { idle: 'Idle', calling: 'Memanggil', serving: 'Sedang Diperiksa' };
    if(clinicForm){
      clinicForm.setAttribute('action', clinicEdit.getAttribute('href'));
      clinicForm.querySelector('[name=name]').value = clinicData.name || '';
      clinicForm.querySelector('[name=is_active]').checked = String(clinicData.is_active || '0') === '1';
      var queueStateEl = document.getElementById('clinic-edit-queue-state');
      if(queueStateEl) queueStateEl.textContent = queueStateLabelMap[clinicData.queue_state] || clinicData.queue_state || '-';
      var openQueuesEl = document.getElementById('clinic-edit-open-queues');
      if(openQueuesEl) openQueuesEl.textContent = clinicData.open_queues_today || 0;
      appOpenModal('clinicEditModal');
    }
  }

  var removeMedicineRow = e.target.closest('.btn-remove-medicine-row');
  if(removeMedicineRow){
    e.preventDefault();
    var row = removeMedicineRow.closest('.prescription-row');
    if(row) row.remove();
  }

  if(!e.target.closest('.suggest-select-wrapper')){
    closeSuggestDropdowns();
  }
});

document.addEventListener('change', function(e){
  var select = e.target.closest('select');
  if(!select) return;
  if(select.name === 'medicine_id' || select.name === 'medicine_id[]'){
    syncMedicinePrice(select);
  }
});

document.addEventListener('submit', async function(e){
  var form = e.target.closest('.ajax-form');
  if(!form) return;
  if(form.dataset.confirm && !window.confirm(form.dataset.confirm)){
    e.preventDefault();
    return;
  }
  if(!validateEnhancedSelects(form)){
    e.preventDefault();
    return;
  }
  e.preventDefault();
  var btn = form.querySelector('[type=submit]');
  if(btn){ btn.disabled=true; btn.dataset.originalText=btn.innerHTML; btn.innerHTML='Memproses...'; }
  try{
    var response = await fetch(form.action,{method:form.method||'POST',body:new FormData(form),headers:{'X-Requested-With':'XMLHttpRequest'}});
    var data = await response.json();
    if(!response.ok || !data.success) throw new Error(data.message || 'Terjadi kesalahan.');
    toast(data.message || 'Berhasil disimpan.','success');
    if(data.redirect){ window.location.href = data.redirect; return; }
    if(form.dataset.resetOnSuccess !== 'false') form.reset();
    if(form.dataset.closeModal) appCloseModal(form.dataset.closeModal);
    await refreshMain();
  }catch(err){
    toast(err.message || 'Terjadi kesalahan.','error');
  }finally{
    if(btn){ btn.disabled=false; btn.innerHTML=btn.dataset.originalText||'Simpan'; }
  }
});

window.APP_SITE_URL = '<?= site_url('') ?>'.replace(/\/$/, '');
document.addEventListener('DOMContentLoaded', function(){
  initDataTables(document);
  initPatientSearchForms(document);
  window.appInitSuggestSelects(document);
  initPrescriptionBuilder(document);
  bindInputFormatters(document);
});
})();
</script>
</body>
</html>

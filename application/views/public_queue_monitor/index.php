<?php
$branchLabel = $branch['name'] ?? app_config('app_name');
?>
<div class="min-h-screen bg-slate-100 text-slate-900">
  <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-blue-500 text-white shadow-lg">
    <div class="mx-auto max-w-[1600px] px-4 py-5 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex items-start gap-4">
          <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/15 shadow-inner ring-1 ring-white/20">
            <i class="fa-solid fa-heart-pulse text-3xl"></i>
          </div>
          <div>
            <div class="text-3xl font-bold tracking-tight"><?= e(app_config('app_name')) ?></div>
            <div class="mt-1 text-lg text-blue-50"><?= e($branchLabel) ?></div>
            
          </div>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
          <form method="get" class="flex flex-col gap-2 rounded-3xl bg-white/10 p-3 backdrop-blur sm:flex-row sm:items-center">
            <label class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Cabang</label>
            <select name="branch_id" class="min-w-[240px] rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white outline-none focus:border-white/50">
              <?php foreach($branches as $opt): ?>
                <option value="<?= (int)$opt['id'] ?>" <?= (int)$selectedBranchId === (int)$opt['id'] ? 'selected' : '' ?> class="text-slate-900"><?= e($opt['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow hover:bg-blue-50"><i class="fa-solid fa-rotate"></i> Ganti Cabang</button>
          </form>
          <div class="rounded-3xl bg-white/10 px-5 py-4 text-right backdrop-blur">
            <div id="liveDate" class="text-sm font-medium text-blue-100"></div>
            <div id="liveClock" class="text-4xl font-bold tracking-tight">--:--:--</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mx-auto max-w-[1600px] px-4 py-4 sm:px-6 lg:px-8">
    <div class="grid gap-4 xl:grid-cols-[1.15fr_2fr]">
      <section class="overflow-hidden rounded-[30px] border border-slate-200 bg-gradient-to-br from-indigo-500 to-indigo-700 text-white shadow-xl">
        <div class="border-b border-white/15 px-6 py-5 text-center text-xl font-semibold uppercase tracking-[0.18em] text-indigo-100">Nomor Dipanggil Saat Ini</div>
        <div class="flex min-h-[370px] flex-col items-center justify-center gap-4 px-6 py-8 text-center">
          <div id="heroQueueNumber" class="text-[120px] font-light leading-none tracking-[0.08em] sm:text-[150px]">---</div>
          <div id="heroClinicName" class="text-3xl font-semibold uppercase tracking-wide text-indigo-50">Menunggu Panggilan</div>
          <div id="heroAnnouncementStatus" class="rounded-full bg-white/15 px-5 py-2 text-sm font-semibold text-indigo-50 ring-1 ring-white/15">Belum ada nomor yang sedang dipanggil</div>
        </div>
      </section>

      <section class="relative overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-xl">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(59,130,246,0.18),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(99,102,241,0.18),_transparent_30%)]"></div>
        <div class="relative grid gap-6 px-6 py-6 lg:grid-cols-[1.2fr_0.8fr]">
          <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
              <i class="fa-solid fa-volume-high"></i> Pengumuman antrian otomatis
            </div>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Display Antrian Klinik</h1>
            
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
              <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-medium text-slate-500">Total Poli Aktif</div>
                <div id="summaryClinics" class="mt-2 text-3xl font-extrabold text-slate-900"><?= count($clinics) ?></div>
              </div>
              <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-medium text-slate-500">Sedang Aktif</div>
                <div id="summaryActive" class="mt-2 text-3xl font-extrabold text-slate-900"><?= count(array_filter($clinics, fn($c)=>!empty($c['queue_number']))) ?></div>
              </div>
              <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-medium text-slate-500">Terakhir Sinkron</div>
                <div id="lastSync" class="mt-2 text-xl font-bold text-slate-900"><?= e(date('H:i:s')) ?></div>
              </div>
            </div>
          </div>
          <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Status panggilan</div>
                <div class="mt-2 text-2xl font-bold text-slate-900">Antrian yang dipanggil</div>
              </div>
              <div class="rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Live</div>
            </div>
            <div class="mt-5 space-y-4" id="announcementList"></div>

          </div>
        </div>
      </section>
    </div>

    <section class="mt-5 rounded-[30px] border border-slate-200 bg-white p-5 shadow-xl">
      <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Antrian Per Poli</h2>
          
        </div>
        <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">Refresh otomatis setiap 10 detik</div>
      </div>
      <div id="clinicGrid" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"></div>
    </section>
  </div>
</div>

<script>
window.PUBLIC_QUEUE_BOOTSTRAP = <?= json_encode(['branchId'=>$selectedBranchId,'clinics'=>$clinics], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
(function(){
  var branchId = window.PUBLIC_QUEUE_BOOTSTRAP.branchId;
  var clinics = window.PUBLIC_QUEUE_BOOTSTRAP.clinics || [];
  var grid = document.getElementById('clinicGrid');
  var announcementList = document.getElementById('announcementList');
  var synth = window.speechSynthesis || null;
  var palette = [
    {wrap:'from-fuchsia-500 to-violet-500', chip:'bg-fuchsia-50 text-fuchsia-700', muted:'text-fuchsia-100'},
    {wrap:'from-emerald-500 to-teal-500', chip:'bg-emerald-50 text-emerald-700', muted:'text-emerald-100'},
    {wrap:'from-rose-500 to-red-500', chip:'bg-rose-50 text-rose-700', muted:'text-rose-100'},
    {wrap:'from-sky-500 to-blue-500', chip:'bg-sky-50 text-sky-700', muted:'text-sky-100'},
    {wrap:'from-amber-500 to-orange-500', chip:'bg-amber-50 text-amber-700', muted:'text-amber-100'},
    {wrap:'from-indigo-500 to-blue-600', chip:'bg-indigo-50 text-indigo-700', muted:'text-indigo-100'}
  ];
  var state = {};
  var storageKey = 'queueMonitorState:branch:' + branchId;
  try { state = JSON.parse(localStorage.getItem(storageKey) || '{}'); } catch(e) { state = {}; }

  function formatDateTime(date){
    return new Intl.DateTimeFormat('id-ID', { day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' }).format(date);
  }

  function tickClock(){
    var now = new Date();
    document.getElementById('liveDate').textContent = new Intl.DateTimeFormat('id-ID', { day:'numeric', month:'long', year:'numeric' }).format(now);
    document.getElementById('liveClock').textContent = new Intl.DateTimeFormat('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' }).format(now);
  }

  function queueStatusLabel(item){
    if(item.queue_status === 'called') return 'Sedang dipanggil';
    if(item.queue_status === 'examined') return 'Sedang diperiksa';
    return 'Belum ada antrian aktif';
  }

  function queueStatusBadge(item){
    if(item.queue_status === 'called') return '<span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">Dipanggil</span>';
    if(item.queue_status === 'examined') return '<span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">Sedang diperiksa</span>';
    return '<span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">Siap</span>';
  }

  function determineHero(items){
    return items.find(function(item){ return item.queue_status === 'called'; })
      || items.find(function(item){ return item.queue_status === 'examined'; })
      || null;
  }

  function renderHero(item){
    document.getElementById('heroQueueNumber').textContent = item && item.queue_number ? item.queue_number : '---';
    document.getElementById('heroClinicName').textContent = item ? item.name : 'Menunggu Panggilan';
    document.getElementById('heroAnnouncementStatus').textContent = item
      ? (item.queue_status === 'called' ? 'Nomor ini sedang dipanggil ke poli' : 'Nomor ini sedang diperiksa di poli')
      : 'Belum ada nomor yang sedang dipanggil';
  }

  function renderAnnouncementList(items){
    var active = items.filter(function(item){ return item.queue_number; });
    if(!active.length){
      announcementList.innerHTML = '<div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-5 text-sm text-slate-500">Belum ada antrian aktif di poli mana pun.</div>';
      return;
    }
    announcementList.innerHTML = active.slice(0, 4).map(function(item){
      return '<div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">'
        + '<div class="flex items-start justify-between gap-3">'
        + '<div><div class="text-sm font-semibold text-slate-900">' + item.name + '</div><div class="mt-1 text-xs text-slate-500">' + queueStatusLabel(item) + '</div></div>'
        + queueStatusBadge(item)
        + '</div>'
        + '<div class="mt-3 text-4xl font-extrabold tracking-[0.16em] text-slate-900">' + item.queue_number + '</div>'
        + '<div class="mt-2 text-xs text-slate-500">Pasien: ' + (item.patient_name || '-') + '</div>'
        + '</div>';
    }).join('');
  }

  function render(items){
    document.getElementById('summaryClinics').textContent = items.length;
    document.getElementById('summaryActive').textContent = items.filter(function(item){ return item.queue_number; }).length;
    document.getElementById('lastSync').textContent = new Date().toLocaleTimeString('id-ID');
    renderHero(determineHero(items));
    renderAnnouncementList(items);
    grid.innerHTML = items.map(function(item, index){
      var theme = palette[index % palette.length];
      var statusText = queueStatusLabel(item);
      var currentNumber = item.queue_number || '---';
      var patientText = item.patient_name || 'Belum ada pasien aktif';
      return '<article class="overflow-hidden rounded-[28px] bg-gradient-to-br ' + theme.wrap + ' text-white shadow-lg">'
        + '<div class="border-b border-white/15 px-5 py-4 text-center text-lg font-semibold uppercase tracking-[0.12em]">' + item.name + '</div>'
        + '<div class="px-5 py-8 text-center">'
        + '<div class="text-sm font-semibold uppercase tracking-[0.18em] ' + theme.muted + '">' + statusText + '</div>'
        + '<div class="mt-4 text-[92px] font-light leading-none tracking-[0.08em] sm:text-[110px]">' + currentNumber + '</div>'
        + '<div class="mt-5 inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-semibold ring-1 ring-white/15">' + statusText + '</div>'
        + '</div>'
        + '<div class="grid gap-3 border-t border-white/15 bg-black/10 px-5 py-4 sm:grid-cols-2">'
        + '<div>'
        + '<div class="text-xs uppercase tracking-[0.14em] text-white/70">Pasien aktif</div>'
        + '<div class="mt-1 text-sm font-semibold">' + patientText + '</div>'
        + '</div>'
        + '<div>'
        + '<div class="text-xs uppercase tracking-[0.14em] text-white/70">Antrian berikutnya</div>'
        + '<div class="mt-1 text-sm font-semibold">' + (item.next_queue_number || '-') + ' · Menunggu ' + (item.waiting_total || 0) + '</div>'
        + '</div>'
        + '</div>'
        + '</article>';
    }).join('');
  }

  var speechRunner = Promise.resolve();

  function queueDigitsForSpeech(queueNumber){
    return String(queueNumber || '').split('').filter(Boolean).join('; ');
  }

  function speakSegment(text){
    return new Promise(function(resolve){
      if(!synth || !text){ resolve(); return; }
      try {
        var utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 0.82;
        utter.pitch = 1;
        utter.volume = 1;
        utter.onend = function(){ resolve(); };
        utter.onerror = function(){ resolve(); };
        synth.speak(utter);
      } catch(e) {
        resolve();
      }
    });
  }

  async function speakAnnouncement(item, isLastCall){
    if(!synth || !item || !item.queue_number) return;
    try { synth.cancel(); } catch(e) {}
    var digits = queueDigitsForSpeech(item.queue_number);
    var clinicName = item.name || 'poli tujuan';
    var sentence = isLastCall
      ? 'Panggilan terakhir;. Nomor antrian; ' + digits + ', silahkan menuju ke; ' + clinicName + '.'
      : 'Nomor antrian; ' + digits + ', silahkan menuju ke; ' + clinicName + '.';
    await speakSegment(sentence);
  }

  function enqueueAnnouncement(item, key, nextCount){
    state[key] = state[key] || {count: 0, lastAt: 0, pending: false};
    state[key].pending = true;
    localStorage.setItem(storageKey, JSON.stringify(state));

    speechRunner = speechRunner
      .catch(function(){})
      .then(async function(){
        await speakAnnouncement(item, nextCount >= 3);
        state[key] = state[key] || {count: 0, lastAt: 0, pending: false};
        state[key].count = nextCount;
        state[key].lastAt = Date.now();
        state[key].pending = false;
        localStorage.setItem(storageKey, JSON.stringify(state));
      });
  }

  function processAnnouncements(items){
    var now = Date.now();
    items.forEach(function(item){
      if(item.queue_status !== 'called' || !item.queue_id) return;
      var key = item.announcement_key || ('queue-' + item.queue_id);
      var record = state[key] || {count: 0, lastAt: 0, pending: false};
      if(record.pending) return;
      var elapsed = now - (record.lastAt || 0);
      var nextCount = (record.count || 0) + 1;
      if(record.count === 0 || ((record.count || 0) < 3 && elapsed >= 60000)) {
        enqueueAnnouncement(item, key, nextCount);
      }
    });
    var activeKeys = {};
    items.forEach(function(item){ if(item.announcement_key){ activeKeys[item.announcement_key] = true; } });
    Object.keys(state).forEach(function(key){ if(!activeKeys[key] && !(state[key] && state[key].pending)) delete state[key]; });
    localStorage.setItem(storageKey, JSON.stringify(state));
  }

  async function refresh(){
    try {
      var response = await fetch('<?= site_url('monitor-antrian/data') ?>?branch_id=' + encodeURIComponent(branchId), {headers:{'X-Requested-With':'XMLHttpRequest'}});
      var data = await response.json();
      if(!response.ok || !data.success) throw new Error(data.message || 'Gagal memuat data monitor.');
      clinics = data.clinics || [];
      render(clinics);
      processAnnouncements(clinics);
    } catch(e) {
      console.error(e);
    }
  }

  tickClock();
  setInterval(tickClock, 1000);
  render(clinics);
  processAnnouncements(clinics);
  setInterval(refresh, 10000);
})();
</script>

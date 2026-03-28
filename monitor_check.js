window.PUBLIC_QUEUE_BOOTSTRAP = {branchId:1, clinics:[]};
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

  function wait(ms){
    return new Promise(function(resolve){ setTimeout(resolve, ms); });
  }

  function queueDigitsToWords(queueNumber){
    var digitMap = {
      '0': 'Nol',
      '1': 'Satu',
      '2': 'Dua',
      '3': 'Tiga',
      '4': 'Empat',
      '5': 'Lima',
      '6': 'Enam',
      '7': 'Tujuh',
      '8': 'Delapan',
      '9': 'Sembilan'
    };
    return String(queueNumber || '').split('').map(function(char){
      return Object.prototype.hasOwnProperty.call(digitMap, char) ? digitMap[char] : char;
    }).filter(Boolean);
  }

  function speakSegment(text){
    return new Promise(function(resolve){
      if(!synth || !text){ resolve(); return; }
      try {
        var utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 0.72;
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

    if(isLastCall){
      await speakSegment('Panggilan terakhir');
      await wait(3000);
    }

    await speakSegment('Nomor Antrian');
    await wait(2000);

    var digitWords = queueDigitsToWords(item.queue_number);
    for(var i = 0; i < digitWords.length; i++){
      await speakSegment(digitWords[i]);
      await wait(1000);
    }

    await speakSegment('silahkan masuk ke');
    await wait(1000);
    await speakSegment(item.name || 'poli tujuan');
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
      var response = await fetch('/monitor-antrian/data?branch_id=' + encodeURIComponent(branchId), {headers:{'X-Requested-With':'XMLHttpRequest'}});
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
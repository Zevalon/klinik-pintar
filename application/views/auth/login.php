<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Klinik Pintar</title>
  <script>
    tailwind = window.tailwind || {};
    tailwind.config = {
      theme: {
        extend: {
          colors: { brand: { 50:'#eef8ff',100:'#d9f0ff',500:'#0ea5e9',600:'#0284c7',700:'#0369a1' } },
          boxShadow: { soft: '0 10px 30px rgba(15, 23, 42, 0.08)' }
        }
      }
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="min-h-screen bg-slate-950 text-white">
  <div class="grid min-h-screen lg:grid-cols-2">
    <section class="relative hidden overflow-hidden lg:block">
      <div class="absolute inset-0 bg-gradient-to-br from-brand-600 via-sky-500 to-cyan-400"></div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.35),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(255,255,255,0.18),_transparent_30%)]"></div>
      <div class="relative flex h-full flex-col justify-between p-12">
        <div class="flex items-center gap-4">
          <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/20 text-3xl backdrop-blur"><i class="fa-solid fa-heart-pulse"></i></div>
          <div>
            <div class="text-3xl font-extrabold tracking-tight">Klinik Pintar</div>
            
          </div>
        </div>
        <div class="max-w-lg space-y-6">
          <h1 class="text-5xl font-black leading-tight">Kelola operasional klinik lebih cepat, rapi, dan modern.</h1>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur"><div class="text-sm text-white/70">Modul</div><div class="mt-1 text-2xl font-bold">Antrian, EMR, Farmasi</div></div>
            <div class="rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur"><div class="text-sm text-white/70">Multi Cabang</div><div class="mt-1 text-2xl font-bold">Kontrol pusat & cabang</div></div>
          </div>
        </div>
        <div class="text-sm text-white/70">Pendaftaran pasien publik tersedia melalui halaman web tanpa WhatsApp atau mobile app.</div>
      </div>
    </section>

    <section class="flex items-center justify-center px-6 py-10 sm:px-10">
      <div class="w-full max-w-md rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl">
        <div class="mb-6 text-center lg:text-left">
          <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-sky-500/20 text-2xl text-sky-300 lg:mx-0"><i class="fa-solid fa-user-doctor"></i></div>
          <h2 class="text-3xl font-extrabold tracking-tight">Masuk ke Klinik Pintar</h2>
          <p class="mt-2 text-sm text-slate-300">Gunakan akun petugas untuk mengakses dashboard operasional klinik.</p>
        </div>
        <?php if($msg = get_flash('error')): ?><div class="mb-4 rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200"><?= e($msg) ?></div><?php endif; ?>
        <form method="post" action="<?= site_url('login') ?>" class="space-y-4">
          <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-300">Username</label>
            <input name="username" class="w-full rounded-2xl border border-white/10 bg-slate-900/60 px-4 py-3 outline-none ring-0 transition focus:border-sky-400 focus:ring-4 focus:ring-sky-400/10" required>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-300">Password</label>
            <input name="password" type="password" class="w-full rounded-2xl border border-white/10 bg-slate-900/60 px-4 py-3 outline-none ring-0 transition focus:border-sky-400 focus:ring-4 focus:ring-sky-400/10" required>
          </div>
          <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-sky-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:bg-sky-400"><i class="fa-solid fa-right-to-bracket"></i> Masuk</button>
        </form>
        <div class="mt-6 rounded-2xl border border-white/10 bg-slate-900/40 p-4 text-sm text-slate-300">
          <div class="mb-2 font-semibold text-white">Akun demo</div>
          <div>superadmin / password123</div>
          <div>branchadmin / password123</div>
          <div>frontoffice / password123</div>
          <div>dokter / password123</div>
          <div>farmasi / password123</div>
          <div>kasir / password123</div>
        </div>
      </div>
    </section>
  </div>
</body>
</html>

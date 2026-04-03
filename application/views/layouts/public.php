<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(app_config('app_name')) ?> - Monitor Antrian</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>body{font-family:'Inter',sans-serif;background:radial-gradient(circle at top left,rgba(14,165,233,.15),transparent 18%),linear-gradient(180deg,#f8fbff 0%,#eef2ff 100%)}.public-shell{position:relative;isolation:isolate}.public-shell:before,.public-shell:after{content:'';position:fixed;z-index:-1;width:20rem;height:20rem;border-radius:999px;filter:blur(52px);opacity:.35;pointer-events:none}.public-shell:before{top:-6rem;right:-4rem;background:rgba(59,130,246,.22)}.public-shell:after{bottom:-8rem;left:-5rem;background:rgba(14,165,233,.22)}</style>
</head>
<body class="min-h-screen text-slate-900"><div class="public-shell min-h-screen"><?= $content ?></div></body>
</html>

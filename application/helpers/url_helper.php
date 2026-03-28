<?php
function base_url($path = '') {
    return env_base_url() . ($path ? '/' . ltrim($path, '/') : '');
}

function site_url($path = '') {
    $cfg = app_config();
    $indexPage = trim($cfg['index_page'] ?? 'index.php', '/');
    $base = env_base_url();
    $url = $base . '/' . $indexPage;
    if ($path !== '') {
        $url .= '/' . ltrim($path, '/');
    }
    return $url;
}

function redirect_to($path) {
    $target = preg_match('~^https?://~', $path) ? $path : site_url($path);
    header('Location: ' . $target);
    exit;
}

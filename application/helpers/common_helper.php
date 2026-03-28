<?php
function app_config($key = null) {
    static $config;
    if (!$config) {
        $config = require APP_PATH . '/config/config.php';
    }
    return $key ? ($config[$key] ?? null) : $config;
}

function db_config() {
    static $config;
    if (!$config) {
        $config = require APP_PATH . '/config/database.php';
    }
    return $config;
}

function env_base_url() {
    $cfg = app_config();
    if (!empty($cfg['base_url'])) {
        return rtrim($cfg['base_url'], '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    return rtrim($scheme . '://' . $host . ($script === '/' ? '' : $script), '/');
}

function current_path() {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $scriptBase = $scriptDir === '/' ? '' : $scriptDir;

    if ($scriptBase && strpos($uri, $scriptBase) === 0) {
        $uri = substr($uri, strlen($scriptBase));
    }

    $uri = preg_replace('~^/?index\.php/?~', '', $uri);
    return trim($uri, '/');
}

function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function nav_active($prefix) {
    $path = current_path();
    if ($prefix === '') {
        return $path === '';
    }
    return $path === $prefix || strpos($path, $prefix . '/') === 0;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}

function csrf_token() {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_csrf'] ?? '';
        if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('CSRF token tidak valid.');
        }
    }
}

function set_flash($key, $message) {
    $_SESSION['_flash'][$key] = $message;
}

function get_flash($key) {
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_global_user() {
    $role = $_SESSION['user']['role_code'] ?? null;
    return in_array($role, ['super_admin', 'owner'], true);
}

function current_branch_id() {
    if (is_global_user()) {
        return $_SESSION['active_branch_id'] ?? null;
    }
    return $_SESSION['user']['branch_id'] ?? null;
}

function current_branch_name() {
    if (is_global_user()) {
        return $_SESSION['active_branch_name'] ?? 'Semua Cabang';
    }
    return $_SESSION['user']['branch_name'] ?? '-';
}

function role_in(array $roles) {
    $role = $_SESSION['user']['role_code'] ?? null;
    return in_array($role, $roles, true);
}

function now() {
    return date('Y-m-d H:i:s');
}

function today() {
    return date('Y-m-d');
}

function currency($amount) {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

function format_date_id($date) {
    if (!$date) {
        return '-';
    }
    return date('d M Y', strtotime($date));
}

function format_datetime_id($date) {
    if (!$date) {
        return '-';
    }
    return date('d M Y H:i', strtotime($date));
}

function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

function get_param($key, $default = null) {
    return $_GET[$key] ?? $default;
}

function array_pick($array, $key, $default = null) {
    return $array[$key] ?? $default;
}

function initials($name) {
    $parts = preg_split('/\s+/', trim((string)$name));
    $letters = '';
    foreach ($parts as $part) {
        if ($part !== '') {
            $letters .= strtoupper(substr($part, 0, 1));
        }
        if (strlen($letters) >= 2) {
            break;
        }
    }
    return $letters ?: 'KP';
}

function gender_label($gender) {
    if ($gender === 'L') {
        return 'Laki-laki';
    }
    if ($gender === 'P') {
        return 'Perempuan';
    }
    return $gender ?: '-';
}

function patient_type_label($type) {
    $map = [
        'umum' => 'Umum',
        'rujukan' => 'Rujukan',
        'kontrol' => 'Kontrol',
    ];
    return $map[$type] ?? ucfirst((string)$type);
}

function role_label($role) {
    $map = [
        'super_admin' => 'Super Admin',
        'owner' => 'Owner',
        'branch_admin' => 'Admin Cabang',
        'front_office' => 'Front Office',
        'doctor' => 'Dokter',
        'nurse' => 'Perawat',
        'pharmacist' => 'Farmasi',
        'cashier' => 'Kasir',
        'inventory' => 'Inventory',
    ];
    return $map[$role] ?? ucfirst(str_replace('_', ' ', (string)$role));
}

function status_label($status) {
    $map = [
        'waiting' => 'Menunggu',
        'called' => 'Dipanggil',
        'examined' => 'Diperiksa',
        'done' => 'Selesai',
        'pending' => 'Pending',
        'cancelled' => 'Batal',
        'registered' => 'Terdaftar',
        'completed' => 'Selesai',
        'draft' => 'Draft',
        'ready' => 'Siap Ditagih',
        'prepared' => 'Diproses',
        'dispensed' => 'Diserahkan',
        'unpaid' => 'Belum Bayar',
        'partial' => 'Bayar Sebagian',
        'paid' => 'Lunas',
    ];
    return $map[$status] ?? ucfirst(str_replace('_', ' ', (string)$status));
}

function status_classes($status) {
    $map = [
        'waiting' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
        'called' => 'bg-sky-100 text-sky-700 ring-sky-600/20',
        'registered' => 'bg-slate-100 text-slate-700 ring-slate-600/20',
        'examined' => 'bg-violet-100 text-violet-700 ring-violet-600/20',
        'done' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'pending' => 'bg-orange-100 text-orange-700 ring-orange-600/20',
        'completed' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'dispensed' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'paid' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'partial' => 'bg-orange-100 text-orange-700 ring-orange-600/20',
        'draft' => 'bg-indigo-100 text-indigo-700 ring-indigo-600/20',
        'ready' => 'bg-cyan-100 text-cyan-700 ring-cyan-600/20',
        'prepared' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
        'unpaid' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
        'cancelled' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-700 ring-slate-600/20';
}

function status_badge($status) {
    return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ' . status_classes($status) . '">' . e(status_label($status)) . '</span>';
}

function log_activity($action, $description = '', $recordType = null, $recordId = null) {
    try {
        $user = current_user();
        if (!$user) {
            return;
        }
        $db = DB::conn();
        $stmt = $db->prepare("INSERT INTO activity_logs (branch_id, user_id, action, description, record_type, record_id, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            current_branch_id(),
            $user['id'],
            $action,
            $description,
            $recordType,
            $recordId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            now(),
        ]);
    } catch (Throwable $e) {
        // ignore logging failure
    }
}



function clinic_state_label($state) {
    $map = [
        'idle' => 'Idle',
        'calling' => 'Memanggil',
        'serving' => 'Sedang Diperiksa',
    ];
    return $map[$state] ?? ucfirst((string)$state);
}

function clinic_state_badge($state) {
    $classes = [
        'idle' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
        'calling' => 'bg-sky-100 text-sky-700 ring-sky-600/20',
        'serving' => 'bg-violet-100 text-violet-700 ring-violet-600/20',
    ];
    $class = isset($classes[$state]) ? $classes[$state] : 'bg-slate-100 text-slate-700 ring-slate-600/20';
    return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ' . $class . '">' . e(clinic_state_label($state)) . '</span>';
}

function menu_definitions() {
    return [
        ['key' => 'dashboard', 'section' => 'Ringkasan', 'label' => 'Dashboard', 'icon' => 'fa-solid fa-chart-line', 'url' => site_url('dashboard'), 'prefix' => 'dashboard', 'roles' => ['super_admin','owner','branch_admin','front_office','doctor','nurse','pharmacist','cashier','inventory']],
        ['key' => 'patients', 'section' => 'Pelayanan Klinik', 'label' => 'Pasien', 'icon' => 'fa-solid fa-hospital-user', 'url' => site_url('patients'), 'prefix' => 'patients', 'roles' => ['super_admin','owner','branch_admin','front_office','doctor','nurse']],
        ['key' => 'queues', 'section' => 'Pelayanan Klinik', 'label' => 'Antrian', 'icon' => 'fa-solid fa-users-line', 'url' => site_url('queues'), 'prefix' => 'queues', 'roles' => ['super_admin','owner','branch_admin','front_office','doctor','nurse']],
        ['key' => 'visits', 'section' => 'Pelayanan Klinik', 'label' => 'Pemeriksaan', 'icon' => 'fa-solid fa-stethoscope', 'url' => site_url('visits'), 'prefix' => 'visits', 'roles' => ['super_admin','owner','branch_admin','doctor','nurse']],
        ['key' => 'pharmacy', 'section' => 'Pelayanan Klinik', 'label' => 'Farmasi', 'icon' => 'fa-solid fa-pills', 'url' => site_url('pharmacy'), 'prefix' => 'pharmacy', 'roles' => ['super_admin','owner','branch_admin','pharmacist']],
        ['key' => 'billing', 'section' => 'Pelayanan Klinik', 'label' => 'Kasir', 'icon' => 'fa-solid fa-cash-register', 'url' => site_url('billing'), 'prefix' => 'billing', 'roles' => ['super_admin','owner','branch_admin','cashier']],
        ['key' => 'inventory', 'section' => 'Persediaan & Laporan', 'label' => 'Inventory', 'icon' => 'fa-solid fa-boxes-stacked', 'url' => site_url('inventory'), 'prefix' => 'inventory', 'roles' => ['super_admin','owner','branch_admin','pharmacist','inventory']],
        ['key' => 'finance_reports', 'section' => 'Persediaan & Laporan', 'label' => 'Laporan Keuangan', 'icon' => 'fa-solid fa-file-invoice-dollar', 'url' => site_url('financereports'), 'prefix' => 'financereports', 'roles' => ['super_admin','owner','branch_admin','front_office']],
        ['key' => 'branch_expenses', 'section' => 'Persediaan & Laporan', 'label' => 'Pengeluaran Cabang', 'icon' => 'fa-solid fa-receipt', 'url' => site_url('branchexpenses'), 'prefix' => 'branchexpenses', 'roles' => ['super_admin','owner','branch_admin','front_office']],
        ['key' => 'stock_reports', 'section' => 'Persediaan & Laporan', 'label' => 'Laporan Stok', 'icon' => 'fa-solid fa-file-waveform', 'url' => site_url('stockreports'), 'prefix' => 'stockreports', 'roles' => ['super_admin','owner','branch_admin','pharmacist','inventory']],
        ['key' => 'users', 'section' => 'Pengaturan Sistem', 'label' => 'User', 'icon' => 'fa-solid fa-user-gear', 'url' => site_url('users'), 'prefix' => 'users', 'roles' => ['super_admin','branch_admin']],
        ['key' => 'branches', 'section' => 'Pengaturan Sistem', 'label' => 'Cabang', 'icon' => 'fa-solid fa-building-circle-arrow-right', 'url' => site_url('branches'), 'prefix' => 'branches', 'roles' => ['super_admin','owner']],
    ];
}

function sidebar_menu_for_role($role) {
    $items = [];
    foreach (menu_definitions() as $item) {
        if (in_array($role, $item['roles'], true)) {
            $items[] = $item;
        }
    }
    return $items;
}

function sidebar_menu_sections_for_role($role) {
    $sections = [];
    foreach (sidebar_menu_for_role($role) as $item) {
        $section = $item['section'] ?? 'Menu';
        if (!isset($sections[$section])) {
            $sections[$section] = [];
        }
        $sections[$section][] = $item;
    }
    return $sections;
}

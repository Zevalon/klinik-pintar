<?php
class Controller {
    protected $auth;

    public function __construct() {
        $this->auth = new Auth();
    }

    protected function render($view, $data = [], $layout = 'layouts/main') {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        $layoutFile = APP_PATH . '/views/' . $layout . '.php';
        if (!file_exists($viewFile)) {
            exit('View tidak ditemukan: ' . e($view));
        }
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        include $layoutFile;
    }

    protected function requireLogin() {
        if (!$this->auth->check()) {
            redirect_to('login');
        }
    }

    protected function requireRoles(array $roles) {
        $this->requireLogin();
        if (!$this->auth->roleIn($roles)) {
            if (is_ajax_request()) {
                json_response(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            set_flash('error', 'Akses ditolak untuk role Anda.');
            redirect_to('');
        }
    }

    protected function wantsJson() {
        return is_ajax_request();
    }

    protected function respondSuccess($message) {
        if ($this->wantsJson()) {
            json_response(['success' => true, 'message' => $message]);
        }
        set_flash('success', $message);
    }

    protected function respondError($message, $status = 422) {
        if ($this->wantsJson()) {
            json_response(['success' => false, 'message' => $message], $status);
        }
        set_flash('error', $message);
    }

    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function branchRequired() {
        if (current_branch_id()) {
            return;
        }
        set_flash('error', 'Silakan pilih cabang aktif terlebih dahulu.');
        redirect_to('branches');
    }
}

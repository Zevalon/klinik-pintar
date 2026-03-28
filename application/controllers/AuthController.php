<?php
class AuthController extends Controller {
    public function login() {
        if ($this->auth->check()) {
            redirect_to('');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $username = trim($this->input('username'));
            $password = $this->input('password');
            if ($this->auth->attempt($username, $password)) {
                redirect_to('');
            }
            set_flash('error', 'Username atau password salah.');
            redirect_to('login');
        }

        include APP_PATH . '/views/auth/login.php';
    }

    public function logout() {
        $this->auth->logout();
        redirect_to('login');
    }
}

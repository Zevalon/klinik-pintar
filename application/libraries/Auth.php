<?php
class Auth {
    public function check() {
        return !empty($_SESSION['user']);
    }

    public function user() {
        return $_SESSION['user'] ?? null;
    }

    public function roleIn(array $roles) {
        return in_array($_SESSION['user']['role_code'] ?? '', $roles, true);
    }

    public function attempt($username, $password) {
        $model = new UserModel();
        $user = $model->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if (empty($user['branch_id'])) {
                $branchModel = new BranchModel();
                $branch = $branchModel->firstActive();
                if ($branch) {
                    $_SESSION['active_branch_id'] = $branch['id'];
                    $_SESSION['active_branch_name'] = $branch['name'];
                }
            } else {
                $_SESSION['active_branch_id'] = $user['branch_id'];
                $_SESSION['active_branch_name'] = $user['branch_name'];
            }
            log_activity('login', 'Login ke aplikasi');
            return true;
        }
        return false;
    }

    public function logout() {
        log_activity('logout', 'Logout dari aplikasi');
        unset($_SESSION['user'], $_SESSION['active_branch_id'], $_SESSION['active_branch_name']);
        session_regenerate_id(true);
    }
}

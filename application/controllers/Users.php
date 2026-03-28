<?php
class Users extends Controller {
    public function index() {
        $this->requireRoles(['super_admin', 'branch_admin']);
        $model = new UserModel();
        $this->render('users/index', [
            'users' => $model->allWithRoles(),
            'roles' => $model->roleOptions(),
            'branches' => $model->branchOptions(),
        ]);
    }

    public function store() {
        $this->requireRoles(['super_admin', 'branch_admin']);
        verify_csrf();

        $model = new Model();
        $branchId = $this->input('branch_id') ?: current_branch_id();

        $model->insert('users', [
            'branch_id' => $branchId ?: null,
            'role_id' => $this->input('role_id'),
            'name' => $this->input('name'),
            'username' => $this->input('username'),
            'password' => password_hash($this->input('password'), PASSWORD_DEFAULT),
            'email' => $this->input('email'),
            'is_active' => 1,
            'created_at' => now(),
        ]);

        $this->respondSuccess('User berhasil ditambahkan.');
        if (!$this->wantsJson()) redirect_to('users');
    }
}

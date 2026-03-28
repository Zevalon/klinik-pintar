<?php
class Branches extends Controller {
    public function index() {
        $this->requireRoles(['super_admin', 'owner']);
        $model = new BranchModel();
        $this->render('branches/index', [
            'branches' => $model->withStats(),
            'active_branch_id' => current_branch_id(),
        ]);
    }

    public function switch($id) {
        $this->requireRoles(['super_admin', 'owner']);
        $model = new BranchModel();
        $branch = $model->find((int)$id);
        if (!$branch) {
            set_flash('error', 'Cabang tidak ditemukan.');
            redirect_to('branches');
        }
        $_SESSION['active_branch_id'] = $branch['id'];
        $_SESSION['active_branch_name'] = $branch['name'];
        set_flash('success', 'Cabang aktif berhasil diubah ke ' . $branch['name'] . '.');
        redirect_to('');
    }

    public function store() {
        $this->requireRoles(['super_admin']);
        verify_csrf();
        $model = new Model();
        $model->insert('branches', [
            'name' => $this->input('name'),
            'city' => $this->input('city'),
            'address' => $this->input('address'),
            'phone' => $this->input('phone'),
            'is_active' => 1,
            'created_at' => now(),
        ]);
        $this->respondSuccess('Cabang berhasil ditambahkan.');
        if (!$this->wantsJson()) redirect_to('branches');
    }
}

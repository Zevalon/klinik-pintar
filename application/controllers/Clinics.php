<?php
class Clinics extends Controller {
    public function index() {
        $this->requireRoles(['super_admin', 'owner', 'branch_admin']);
        $this->branchRequired();

        $model = new ClinicModel();
        $this->render('clinics/index', [
            'clinics' => $model->managementList(current_branch_id()),
            'summary' => $model->summaryByBranch(current_branch_id()),
            'branch_name' => current_branch_name(),
        ]);
    }

    public function store() {
        $this->requireRoles(['super_admin', 'owner', 'branch_admin']);
        $this->branchRequired();
        verify_csrf();

        $branchId = (int)current_branch_id();
        $name = trim((string)$this->input('name'));
        if ($name === '') {
            $this->respondError('Nama poli wajib diisi.');
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        $clinicModel = new ClinicModel();
        if ($clinicModel->existsByName($branchId, $name)) {
            $this->respondError('Nama poli sudah ada pada cabang ini. Gunakan nama lain.');
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        $id = $clinicModel->insert('clinics', [
            'branch_id' => $branchId,
            'name' => $name,
            'queue_state' => 'idle',
            'is_active' => $this->input('is_active') ? 1 : 0,
            'created_at' => now(),
        ]);

        log_activity('clinic_create', 'Menambahkan data poli baru', 'clinics', $id);
        $this->respondSuccess('Data poli berhasil ditambahkan.');
        if (!$this->wantsJson()) redirect_to('clinics');
    }

    public function update($id) {
        $this->requireRoles(['super_admin', 'owner', 'branch_admin']);
        $this->branchRequired();
        verify_csrf();

        $branchId = (int)current_branch_id();
        $clinicModel = new ClinicModel();
        $clinic = $clinicModel->findInBranch((int)$id, $branchId);
        if (!$clinic) {
            $this->respondError('Data poli tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        $name = trim((string)$this->input('name'));
        if ($name === '') {
            $this->respondError('Nama poli wajib diisi.');
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        if ($clinicModel->existsByName($branchId, $name, (int)$id)) {
            $this->respondError('Nama poli sudah digunakan pada cabang ini.');
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        $isActive = $this->input('is_active') ? 1 : 0;
        if ((int)$clinic['is_active'] === 1 && $isActive === 0 && $clinicModel->hasOpenQueuesToday($branchId, (int)$id)) {
            $this->respondError('Poli tidak bisa dinonaktifkan karena masih ada antrian aktif hari ini.');
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        $payload = [
            'name' => $name,
            'is_active' => $isActive,
            'updated_at' => now(),
        ];
        if ($isActive === 0) {
            $payload['queue_state'] = 'idle';
        }

        (new Model())->updateById('clinics', (int)$id, $payload);
        log_activity('clinic_update', 'Mengubah data poli', 'clinics', (int)$id);
        $this->respondSuccess('Data poli berhasil diperbarui.');
        if (!$this->wantsJson()) redirect_to('clinics');
    }

    public function delete($id) {
        $this->requireRoles(['super_admin', 'owner', 'branch_admin']);
        $this->branchRequired();
        verify_csrf();

        $branchId = (int)current_branch_id();
        $clinicModel = new ClinicModel();
        $clinic = $clinicModel->findInBranch((int)$id, $branchId);
        if (!$clinic) {
            $this->respondError('Data poli tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        if ($clinicModel->hasOpenQueuesToday($branchId, (int)$id)) {
            $this->respondError('Poli tidak bisa dihapus karena masih ada antrian aktif hari ini.');
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        (new Model())->updateById('clinics', (int)$id, [
            'is_active' => 0,
            'queue_state' => 'idle',
            'updated_at' => now(),
        ]);

        log_activity('clinic_delete', 'Menonaktifkan data poli', 'clinics', (int)$id);
        $this->respondSuccess('Data poli berhasil dinonaktifkan.');
        if (!$this->wantsJson()) redirect_to('clinics');
    }

    public function restore($id) {
        $this->requireRoles(['super_admin', 'owner', 'branch_admin']);
        $this->branchRequired();
        verify_csrf();

        $branchId = (int)current_branch_id();
        $clinicModel = new ClinicModel();
        $clinic = $clinicModel->findInBranch((int)$id, $branchId);
        if (!$clinic) {
            $this->respondError('Data poli tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('clinics');
            return;
        }

        (new Model())->updateById('clinics', (int)$id, [
            'is_active' => 1,
            'queue_state' => $clinic['queue_state'] ?: 'idle',
            'updated_at' => now(),
        ]);

        log_activity('clinic_restore', 'Mengaktifkan kembali data poli', 'clinics', (int)$id);
        $this->respondSuccess('Data poli berhasil diaktifkan kembali.');
        if (!$this->wantsJson()) redirect_to('clinics');
    }
}

<?php
class Patients extends Controller {
    public function index() {
        $this->requireLogin();
        $this->branchRequired();
        $this->render('patients/index', [
            'patients' => (new PatientModel())->allByBranch(current_branch_id()),
        ]);
    }

    public function searchExisting() {
        $this->requireLogin();
        $this->branchRequired();
        $keyword = trim((string)$this->input('keyword', ''));
        $rows = (new PatientModel())->searchByKeyword(current_branch_id(), $keyword);
        json_response([
            'success' => true,
            'items' => $rows,
            'count' => count($rows),
            'message' => count($rows) ? 'Data pasien ditemukan.' : 'Tidak ada pasien yang cocok. Lanjutkan pendaftaran pasien baru.',
        ]);
    }

    public function store() {
        $this->requireRoles(['super_admin','branch_admin','front_office']);
        $this->branchRequired();
        verify_csrf();
        $model = new PatientModel();
        $existing = $model->findExistingForRegistration(current_branch_id(), trim((string)$this->input('nik')), normalize_phone($this->input('phone')), $this->input('birth_date'));
        if ($existing) {
            $this->respondError('Pasien dengan NIK / kombinasi HP dan tanggal lahir yang sama sudah ada. Silakan gunakan data pasien yang sudah terdaftar.');
            if (!$this->wantsJson()) redirect_to('patients');
            return;
        }
        $id = $model->insert('patients', [
            'branch_id' => current_branch_id(),
            'medical_record_no' => $model->createMedicalRecordNo(current_branch_id()),
            'name' => $this->input('name'),
            'nik' => $this->input('nik'),
            'gender' => $this->input('gender'),
            'birth_date' => $this->input('birth_date'),
            'phone' => normalize_phone($this->input('phone')),
            'address' => $this->input('address'),
            'patient_type' => $this->input('patient_type', 'umum'),
            'registration_source' => 'internal',
            'created_at' => now(),
        ]);
        log_activity('patient_create', 'Menambahkan pasien baru', 'patients', $id);
        $this->respondSuccess('Pasien berhasil ditambahkan.');
        if (!$this->wantsJson()) redirect_to('patients');
    }

    public function update($id) {
        $this->requireLogin();
        $this->branchRequired();
        verify_csrf();
        $patientModel = new PatientModel();
        $patient = $patientModel->find((int)$id);
        if (!$patient || $patient['branch_id'] != current_branch_id()) {
            $this->respondError('Pasien tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('patients');
            return;
        }
        $model = new Model();
        $model->updateById('patients', (int)$id, [
            'name' => $this->input('name'),
            'nik' => $this->input('nik'),
            'gender' => $this->input('gender'),
            'birth_date' => $this->input('birth_date'),
            'phone' => normalize_phone($this->input('phone')),
            'address' => $this->input('address'),
            'patient_type' => $this->input('patient_type'),
            'updated_at' => now(),
        ]);
        log_activity('patient_update', 'Mengubah data pasien', 'patients', (int)$id);
        $this->respondSuccess('Data pasien berhasil diperbarui.');
        if (!$this->wantsJson()) redirect_to('patients');
    }

    public function delete($id) {
        $this->requireRoles(['super_admin','branch_admin','front_office']);
        $this->branchRequired();
        verify_csrf();
        $patientModel = new PatientModel();
        $patient = $patientModel->find((int)$id);
        if (!$patient || $patient['branch_id'] != current_branch_id()) {
            $this->respondError('Pasien tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('patients');
            return;
        }
        $model = new Model();
        $model->deleteWhere('patients', 'id=?', [(int)$id]);
        log_activity('patient_delete', 'Menghapus data pasien', 'patients', (int)$id);
        $this->respondSuccess('Data pasien berhasil dihapus.');
        if (!$this->wantsJson()) redirect_to('patients');
    }
}

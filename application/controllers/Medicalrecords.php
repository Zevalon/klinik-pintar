<?php
class Medicalrecords extends Controller {
    protected function authorize() {
        $this->requireRoles(['doctor', 'nurse', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();
    }

    protected function patientOrFail($patientId) {
        $patient = (new PatientModel())->find((int)$patientId);
        if (!$patient || (int)$patient['branch_id'] !== (int)current_branch_id()) {
            if ($this->wantsJson()) {
                json_response(['success' => false, 'message' => 'Pasien tidak ditemukan.'], 404);
            }
            set_flash('error', 'Pasien tidak ditemukan.');
            redirect_to('medicalrecords');
        }
        return $patient;
    }

    public function index() {
        $this->authorize();
        $keyword = trim((string)$this->input('keyword', ''));
        $model = new MedicalRecordModel();
        $this->render('medicalrecords/index', [
            'keyword' => $keyword,
            'patients' => $model->patientSummaryList(current_branch_id(), $keyword),
        ]);
    }

    public function show($patientId) {
        $this->authorize();
        $patient = $this->patientOrFail((int)$patientId);
        $model = new MedicalRecordModel();
        $this->render('medicalrecords/show', [
            'patient' => $patient,
            'profile' => $model->profile((int)$patientId),
            'dashboard' => $model->patientDashboard((int)$patientId),
            'plans' => $model->monitoringPlansByPatient((int)$patientId),
            'activePlans' => $model->activeMonitoringByPatient((int)$patientId),
            'timeline' => $model->patientTimeline((int)$patientId, 50),
            'clinics' => (new ClinicModel())->byBranch(current_branch_id()),
        ]);
    }

    public function saveProfile($patientId) {
        $this->authorize();
        verify_csrf();
        $patient = $this->patientOrFail((int)$patientId);
        $model = new MedicalRecordModel();
        $model->saveProfile((int)$patient['id'], [
            'blood_type' => $this->input('blood_type'),
            'allergy_notes' => $this->input('allergy_notes'),
            'chronic_conditions' => $this->input('chronic_conditions'),
            'past_medical_history' => $this->input('past_medical_history'),
            'surgery_history' => $this->input('surgery_history'),
            'family_history' => $this->input('family_history'),
            'medication_history' => $this->input('medication_history'),
            'vaccination_history' => $this->input('vaccination_history'),
            'lifestyle_notes' => $this->input('lifestyle_notes'),
            'pregnancy_notes' => $this->input('pregnancy_notes'),
            'alert_notes' => $this->input('alert_notes'),
            'special_condition_notes' => $this->input('special_condition_notes'),
        ]);
        log_activity('medical_profile_save', 'Memperbarui profil medis pasien', 'patients', (int)$patient['id']);
        $message = 'Profil medis pasien berhasil diperbarui.';
        if ($this->wantsJson()) {
            json_response(['success' => true, 'message' => $message, 'redirect' => site_url('medicalrecords/show/' . (int)$patient['id'])]);
        }
        set_flash('success', $message);
        redirect_to('medicalrecords/show/' . (int)$patient['id']);
    }

    public function storeMonitoring($patientId) {
        $this->authorize();
        verify_csrf();
        $patient = $this->patientOrFail((int)$patientId);
        $programName = trim((string)$this->input('program_name'));
        if ($programName === '') {
            $this->respondError('Nama program monitoring wajib diisi.');
            if (!$this->wantsJson()) redirect_to('medicalrecords/show/' . (int)$patient['id']);
            return;
        }
        $model = new MedicalRecordModel();
        $model->upsertMonitoringPlan([
            'patient_id' => (int)$patient['id'],
            'branch_id' => (int)current_branch_id(),
            'clinic_id' => $this->input('clinic_id') ? (int)$this->input('clinic_id') : null,
            'doctor_user_id' => current_user()['id'] ?? null,
            'program_name' => $programName,
            'condition_name' => $this->input('condition_name'),
            'frequency_label' => $this->input('frequency_label'),
            'start_date' => $this->input('start_date') ?: today(),
            'next_control_date' => $this->input('next_control_date') ?: null,
            'notes' => $this->input('notes'),
            'status' => 'active',
        ]);
        log_activity('monitoring_plan_save', 'Menyimpan program monitoring pasien', 'patients', (int)$patient['id']);
        $message = 'Program monitoring pasien berhasil disimpan.';
        if ($this->wantsJson()) {
            json_response(['success' => true, 'message' => $message, 'redirect' => site_url('medicalrecords/show/' . (int)$patient['id'])]);
        }
        set_flash('success', $message);
        redirect_to('medicalrecords/show/' . (int)$patient['id']);
    }

    public function setMonitoringStatus($planId, $status) {
        $this->authorize();
        verify_csrf();
        $allowed = ['active', 'completed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $this->respondError('Status monitoring tidak valid.');
            if (!$this->wantsJson()) redirect_to('medicalrecords');
            return;
        }
        $model = new MedicalRecordModel();
        $plan = $model->findMonitoringPlan((int)$planId);
        if (!$plan) {
            $this->respondError('Program monitoring tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('medicalrecords');
            return;
        }
        $patient = $this->patientOrFail((int)$plan['patient_id']);
        $model->updateMonitoringStatus((int)$planId, $status);
        log_activity('monitoring_plan_status', 'Mengubah status program monitoring pasien menjadi ' . $status, 'patient_monitoring_plans', (int)$planId);
        $message = 'Status program monitoring berhasil diperbarui.';
        if ($this->wantsJson()) {
            json_response(['success' => true, 'message' => $message, 'redirect' => site_url('medicalrecords/show/' . (int)$patient['id'])]);
        }
        set_flash('success', $message);
        redirect_to('medicalrecords/show/' . (int)$patient['id']);
    }
}

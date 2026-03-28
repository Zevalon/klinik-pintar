<?php
class Queues extends Controller {
    public function index() {
        $this->requireLogin();
        $this->branchRequired();
        $queue = new QueueModel();
        $clinic = new ClinicModel();
        $patient = new PatientModel();
        $this->render('queues/index', [
            'queues' => $queue->allToday(current_branch_id()),
            'clinics' => $clinic->byBranch(current_branch_id()),
            'patients' => $patient->allByBranch(current_branch_id()),
        ]);
    }

    public function store() {
        $this->requireRoles(['front_office', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();
        $queueModel = new QueueModel();
        $model = new Model();
        $branchId = current_branch_id();
        $clinicId = (int)$this->input('clinic_id');
        $patientId = (int)$this->input('patient_id');

        $existingOpen = $model->one("SELECT q.id FROM queues q WHERE q.branch_id=? AND q.patient_id=? AND DATE(q.queue_date)=CURDATE() AND q.status IN ('waiting','called','examined','pending') LIMIT 1", [$branchId, $patientId]);
        if ($existingOpen) {
            $this->respondError('Pasien sudah memiliki antrian aktif hari ini.');
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }

        $queueNo = $queueModel->createNumber($branchId, $clinicId);
        $queueId = $model->insert('queues', [
            'branch_id' => $branchId,
            'patient_id' => $patientId,
            'clinic_id' => $clinicId,
            'queue_date' => now(),
            'queue_number' => $queueNo,
            'status' => 'waiting',
            'created_at' => now(),
        ]);
        $visitId = $model->insert('visits', [
            'branch_id' => $branchId,
            'queue_id' => $queueId,
            'patient_id' => $patientId,
            'clinic_id' => $clinicId,
            'doctor_user_id' => null,
            'visit_date' => now(),
            'complaint' => $this->input('complaint'),
            'visit_type' => $this->input('visit_type', 'umum'),
            'status' => 'registered',
            'created_at' => now(),
        ]);

        $queueModel->syncFlow($branchId, $clinicId);
        log_activity('queue_create', 'Membuat antrian pasien', 'queues', $queueId);
        log_activity('visit_create', 'Membuat kunjungan pasien', 'visits', $visitId);
        $this->respondSuccess('Antrian berhasil dibuat. Sistem akan otomatis memanggil pasien bila poli sedang idle.');
        if (!$this->wantsJson()) redirect_to('queues');
    }

    public function markPending($id) {
        $this->requireRoles(['front_office', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();
        $queueModel = new QueueModel();
        $queue = $queueModel->find((int)$id);
        if (!$queue || $queue['branch_id'] != current_branch_id()) {
            $this->respondError('Antrian tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        if (!in_array($queue['status'], ['called', 'waiting'], true)) {
            $this->respondError('Hanya antrian menunggu atau dipanggil yang bisa dipending.');
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        $queueModel->setStatus((int)$id, 'pending');
        $queueModel->setVisitStatusByQueue((int)$id, 'registered');
        $queueModel->syncFlow(current_branch_id(), $queue['clinic_id']);
        log_activity('queue_pending', 'Mengubah antrian menjadi pending', 'queues', (int)$id);
        $this->respondSuccess('Antrian dipindahkan ke status pending.');
        if (!$this->wantsJson()) redirect_to('queues');
    }

    public function recall($id) {
        $this->requireRoles(['front_office', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();
        $queueModel = new QueueModel();
        $queue = $queueModel->find((int)$id);
        if (!$queue || $queue['branch_id'] != current_branch_id()) {
            $this->respondError('Antrian tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        if ($queue['status'] !== 'pending') {
            $this->respondError('Hanya antrian pending yang dapat diaktifkan kembali.');
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        $queueModel->setStatus((int)$id, 'waiting');
        $queueModel->setVisitStatusByQueue((int)$id, 'registered');
        $queueModel->syncFlow(current_branch_id(), $queue['clinic_id']);
        log_activity('queue_recall', 'Mengaktifkan kembali antrian pending', 'queues', (int)$id);
        $this->respondSuccess('Antrian diaktifkan kembali.');
        if (!$this->wantsJson()) redirect_to('queues');
    }

    public function startExam($visitId) {
        $this->requireRoles(['doctor', 'nurse', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();
        $model = new Model();
        $visit = $model->one("SELECT * FROM visits WHERE id=?", [(int)$visitId]);
        if (!$visit || $visit['branch_id'] != current_branch_id()) {
            $this->respondError('Kunjungan tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        $queueModel = new QueueModel();
        $queue = $queueModel->find((int)$visit['queue_id']);
        if (!$queue) {
            $this->respondError('Antrian tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        if (!in_array($queue['status'], ['called', 'examined'], true)) {
            $this->respondError('Pasien belum berstatus dipanggil.');
            if (!$this->wantsJson()) redirect_to('queues');
            return;
        }
        $queueModel->setStatus($queue['id'], 'examined');
        $model->updateById('visits', $visit['id'], [
            'doctor_user_id' => current_user()['id'],
            'status' => 'examined',
            'updated_at' => now(),
        ]);
        (new ClinicModel())->setQueueState($queue['clinic_id'], 'serving');
        log_activity('queue_start_exam', 'Dokter memulai pemeriksaan pasien', 'visits', (int)$visitId);
        if ($this->wantsJson()) {
            json_response(['success' => true, 'message' => 'Pemeriksaan dimulai.', 'redirect' => site_url('visits/show/' . (int)$visitId)]);
        }
        redirect_to('visits/show/' . (int)$visitId);
    }
}

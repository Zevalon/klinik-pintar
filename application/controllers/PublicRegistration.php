<?php
class PublicRegistration extends Controller {
    public function index() {
        $branch = new BranchModel();
        $clinic = new ClinicModel();
        include APP_PATH . '/views/public/register.php';
    }

    public function searchPatient() {
        $model = new PatientModel();
        $branchId = (int)$this->input('branch_id');
        $keyword = trim((string)$this->input('keyword'));
        $keywordDigits = digits_only($keyword);
        $phoneLike = $keywordDigits === '' ? '__PHONE_NO_MATCH__' : '%' . $keywordDigits . '%';
        if (!$branchId || $keyword === '') {
            json_response(['success' => true, 'data' => []]);
        }
        $rows = $model->all("SELECT id, medical_record_no, name, nik, phone, birth_date, gender, address, patient_type FROM patients WHERE branch_id=? AND (name LIKE ? OR nik LIKE ? OR phone LIKE ? OR REPLACE(phone,'-','') LIKE ? OR medical_record_no LIKE ?) ORDER BY id DESC LIMIT 10", [$branchId, '%'.$keyword.'%', '%'.$keyword.'%', '%'.$keyword.'%', $phoneLike, '%'.$keyword.'%']);
        json_response(['success' => true, 'data' => $rows]);
    }

    public function store() {
        verify_csrf();
        $model = new PatientModel();
        $generic = new Model();
        $queueModel = new QueueModel();
        $branchId = (int)$this->input('branch_id');
        $clinicId = (int)$this->input('clinic_id');
        $existing = $model->findExistingForRegistration($branchId, trim((string)$this->input('nik')), normalize_phone($this->input('phone')), $this->input('birth_date'));
        if ($existing) {
            $patientId = $existing['id'];
        } else {
            $patientId = $model->insert('patients', [
                'branch_id' => $branchId,
                'medical_record_no' => $model->createMedicalRecordNo($branchId),
                'name' => $this->input('name'),
                'nik' => $this->input('nik'),
                'gender' => $this->input('gender'),
                'birth_date' => $this->input('birth_date'),
                'phone' => normalize_phone($this->input('phone')),
                'address' => $this->input('address'),
                'patient_type' => $this->input('patient_type', 'umum'),
                'registration_source' => 'web',
                'created_at' => now(),
            ]);
        }
        $queueNo = $queueModel->createNumber($branchId, $clinicId);
        $queueId = $generic->insert('queues', [
            'branch_id' => $branchId,
            'patient_id' => $patientId,
            'clinic_id' => $clinicId,
            'queue_date' => now(),
            'queue_number' => $queueNo,
            'status' => 'waiting',
            'created_at' => now(),
        ]);
        $generic->insert('visits', [
            'branch_id' => $branchId,
            'queue_id' => $queueId,
            'patient_id' => $patientId,
            'clinic_id' => $clinicId,
            'visit_date' => now(),
            'complaint' => $this->input('complaint'),
            'visit_type' => $this->input('patient_type', 'umum'),
            'status' => 'registered',
            'created_at' => now(),
        ]);
        echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Pendaftaran Berhasil</title><script>tailwind=window.tailwind||{};tailwind.config={theme:{extend:{colors:{brand:{50:"#eef8ff",100:"#d9f0ff",500:"#0ea5e9",600:"#0284c7",700:"#0369a1"}}}}};</script><script src="https://cdn.tailwindcss.com"></script><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><style>body{font-family:Inter,sans-serif}</style></head><body class="min-h-screen bg-slate-100"><div class="mx-auto flex min-h-screen max-w-3xl items-center px-4 py-10"><div class="w-full rounded-[2rem] bg-white p-8 text-center shadow-xl ring-1 ring-slate-200"><div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-3xl text-emerald-600"><i class="fa-solid fa-circle-check"></i></div><h1 class="text-3xl font-black tracking-tight text-slate-900">Pendaftaran berhasil</h1><p class="mt-3 text-slate-600">Data pasien berhasil dikirim ke sistem Klinik Pintar.</p><div class="mx-auto mt-6 max-w-md rounded-3xl bg-slate-50 p-5 text-left"><div class="text-sm text-slate-500">Nomor antrian</div><div class="mt-1 text-4xl font-black text-slate-900">' . e($queueNo) . '</div></div><div class="mt-8 flex flex-wrap items-center justify-center gap-3"><a class="inline-flex items-center gap-2 rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-brand-700" href="' . site_url('daftar') . '"><i class="fa-solid fa-plus"></i> Daftar Pasien Lain</a><a class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-brand-200 hover:text-brand-700" href="' . site_url('login') . '"><i class="fa-solid fa-right-to-bracket"></i> Login Petugas</a></div></div></div></body></html>';
    }
}

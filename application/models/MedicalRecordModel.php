<?php
class MedicalRecordModel extends Model {
    protected static $schemaEnsured = false;

    public function __construct() {
        parent::__construct();
        $this->ensureSchema();
    }

    public function ensureSchema() {
        if (self::$schemaEnsured) {
            return;
        }

        $this->exec("CREATE TABLE IF NOT EXISTS patient_medical_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL UNIQUE,
            blood_type VARCHAR(10) DEFAULT NULL,
            allergy_notes TEXT DEFAULT NULL,
            chronic_conditions TEXT DEFAULT NULL,
            past_medical_history TEXT DEFAULT NULL,
            surgery_history TEXT DEFAULT NULL,
            family_history TEXT DEFAULT NULL,
            medication_history TEXT DEFAULT NULL,
            vaccination_history TEXT DEFAULT NULL,
            lifestyle_notes TEXT DEFAULT NULL,
            pregnancy_notes TEXT DEFAULT NULL,
            alert_notes TEXT DEFAULT NULL,
            special_condition_notes TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            CONSTRAINT fk_patient_medical_profiles_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE IF NOT EXISTS medical_records (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visit_id INT NOT NULL UNIQUE,
            patient_id INT NOT NULL,
            branch_id INT NOT NULL,
            clinic_id INT DEFAULT NULL,
            doctor_user_id INT DEFAULT NULL,
            anamnesis TEXT DEFAULT NULL,
            complaint_history TEXT DEFAULT NULL,
            physical_exam TEXT DEFAULT NULL,
            subjective_notes TEXT DEFAULT NULL,
            objective_notes TEXT DEFAULT NULL,
            assessment_notes TEXT DEFAULT NULL,
            plan_notes TEXT DEFAULT NULL,
            diagnosis_secondary TEXT DEFAULT NULL,
            procedure_notes TEXT DEFAULT NULL,
            lab_notes TEXT DEFAULT NULL,
            radiology_notes TEXT DEFAULT NULL,
            allergy_confirmation TEXT DEFAULT NULL,
            condition_flags TEXT DEFAULT NULL,
            control_plan TEXT DEFAULT NULL,
            next_control_date DATE DEFAULT NULL,
            referral_notes TEXT DEFAULT NULL,
            special_condition TINYINT(1) NOT NULL DEFAULT 0,
            special_condition_details TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            CONSTRAINT fk_medical_records_visit FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_medical_records_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_medical_records_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_medical_records_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT fk_medical_records_doctor FOREIGN KEY (doctor_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE IF NOT EXISTS patient_monitoring_plans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            branch_id INT NOT NULL,
            clinic_id INT DEFAULT NULL,
            created_from_visit_id INT DEFAULT NULL,
            doctor_user_id INT DEFAULT NULL,
            program_name VARCHAR(150) NOT NULL,
            condition_name VARCHAR(150) DEFAULT NULL,
            frequency_label VARCHAR(100) DEFAULT NULL,
            start_date DATE NOT NULL,
            end_date DATE DEFAULT NULL,
            next_control_date DATE DEFAULT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            notes TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,
            CONSTRAINT fk_patient_monitoring_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_patient_monitoring_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_patient_monitoring_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT fk_patient_monitoring_visit FOREIGN KEY (created_from_visit_id) REFERENCES visits(id) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT fk_patient_monitoring_doctor FOREIGN KEY (doctor_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        self::$schemaEnsured = true;
    }

    public function profile($patientId) {
        return $this->one("SELECT * FROM patient_medical_profiles WHERE patient_id=?", [(int)$patientId]);
    }

    public function saveProfile($patientId, array $data) {
        $existing = $this->profile((int)$patientId);
        $payload = [
            'patient_id' => (int)$patientId,
            'blood_type' => $data['blood_type'] ?? null,
            'allergy_notes' => $data['allergy_notes'] ?? null,
            'chronic_conditions' => $data['chronic_conditions'] ?? null,
            'past_medical_history' => $data['past_medical_history'] ?? null,
            'surgery_history' => $data['surgery_history'] ?? null,
            'family_history' => $data['family_history'] ?? null,
            'medication_history' => $data['medication_history'] ?? null,
            'vaccination_history' => $data['vaccination_history'] ?? null,
            'lifestyle_notes' => $data['lifestyle_notes'] ?? null,
            'pregnancy_notes' => $data['pregnancy_notes'] ?? null,
            'alert_notes' => $data['alert_notes'] ?? null,
            'special_condition_notes' => $data['special_condition_notes'] ?? null,
            'updated_at' => now(),
        ];
        if ($existing) {
            unset($payload['patient_id']);
            return $this->updateById('patient_medical_profiles', $existing['id'], $payload);
        }
        $payload['created_at'] = now();
        return $this->insert('patient_medical_profiles', $payload);
    }

    public function visitRecord($visitId) {
        return $this->one("SELECT * FROM medical_records WHERE visit_id=?", [(int)$visitId]);
    }

    public function saveVisitRecord($visitId, array $visit, array $data) {
        $existing = $this->visitRecord((int)$visitId);
        $payload = [
            'visit_id' => (int)$visitId,
            'patient_id' => (int)$visit['patient_id'],
            'branch_id' => (int)$visit['branch_id'],
            'clinic_id' => !empty($visit['clinic_id']) ? (int)$visit['clinic_id'] : null,
            'doctor_user_id' => !empty($data['doctor_user_id']) ? (int)$data['doctor_user_id'] : (!empty($visit['doctor_user_id']) ? (int)$visit['doctor_user_id'] : null),
            'anamnesis' => $data['anamnesis'] ?? null,
            'complaint_history' => $data['complaint_history'] ?? null,
            'physical_exam' => $data['physical_exam'] ?? null,
            'subjective_notes' => $data['subjective_notes'] ?? null,
            'objective_notes' => $data['objective_notes'] ?? null,
            'assessment_notes' => $data['assessment_notes'] ?? null,
            'plan_notes' => $data['plan_notes'] ?? null,
            'diagnosis_secondary' => $data['diagnosis_secondary'] ?? null,
            'procedure_notes' => $data['procedure_notes'] ?? null,
            'lab_notes' => $data['lab_notes'] ?? null,
            'radiology_notes' => $data['radiology_notes'] ?? null,
            'allergy_confirmation' => $data['allergy_confirmation'] ?? null,
            'condition_flags' => $data['condition_flags'] ?? null,
            'control_plan' => $data['control_plan'] ?? null,
            'next_control_date' => $data['next_control_date'] ?? null,
            'referral_notes' => $data['referral_notes'] ?? null,
            'special_condition' => !empty($data['special_condition']) ? 1 : 0,
            'special_condition_details' => $data['special_condition_details'] ?? null,
            'updated_at' => now(),
        ];

        if ($existing) {
            unset($payload['visit_id'], $payload['patient_id'], $payload['branch_id'], $payload['clinic_id']);
            return $this->updateById('medical_records', $existing['id'], $payload);
        }

        $payload['created_at'] = now();
        return $this->insert('medical_records', $payload);
    }

    public function upsertMonitoringPlan(array $data) {
        $patientId = (int)($data['patient_id'] ?? 0);
        $programName = trim((string)($data['program_name'] ?? ''));
        $conditionName = trim((string)($data['condition_name'] ?? ''));
        if ($patientId <= 0 || $programName === '') {
            return null;
        }

        $existing = $this->one(
            "SELECT * FROM patient_monitoring_plans WHERE patient_id=? AND status='active' AND program_name=? AND COALESCE(condition_name,'')=? ORDER BY id DESC LIMIT 1",
            [$patientId, $programName, $conditionName]
        );

        $payload = [
            'patient_id' => $patientId,
            'branch_id' => (int)($data['branch_id'] ?? 0),
            'clinic_id' => !empty($data['clinic_id']) ? (int)$data['clinic_id'] : null,
            'created_from_visit_id' => !empty($data['created_from_visit_id']) ? (int)$data['created_from_visit_id'] : null,
            'doctor_user_id' => !empty($data['doctor_user_id']) ? (int)$data['doctor_user_id'] : null,
            'program_name' => $programName,
            'condition_name' => $conditionName ?: null,
            'frequency_label' => trim((string)($data['frequency_label'] ?? '')) ?: null,
            'start_date' => $data['start_date'] ?: today(),
            'end_date' => $data['end_date'] ?: null,
            'next_control_date' => $data['next_control_date'] ?: null,
            'status' => $data['status'] ?? 'active',
            'notes' => trim((string)($data['notes'] ?? '')) ?: null,
            'updated_at' => now(),
        ];

        if ($existing) {
            unset($payload['patient_id'], $payload['branch_id']);
            $this->updateById('patient_monitoring_plans', $existing['id'], $payload);
            return (int)$existing['id'];
        }

        $payload['created_at'] = now();
        return $this->insert('patient_monitoring_plans', $payload);
    }

    public function monitoringPlansByPatient($patientId) {
        return $this->all(
            "SELECT mp.*, c.name clinic_name, u.name doctor_name
             FROM patient_monitoring_plans mp
             LEFT JOIN clinics c ON c.id=mp.clinic_id
             LEFT JOIN users u ON u.id=mp.doctor_user_id
             WHERE mp.patient_id=?
             ORDER BY FIELD(mp.status,'active','completed','cancelled'), COALESCE(mp.next_control_date, mp.start_date) ASC, mp.id DESC",
            [(int)$patientId]
        );
    }

    public function activeMonitoringByPatient($patientId) {
        return $this->all(
            "SELECT mp.*, c.name clinic_name, u.name doctor_name
             FROM patient_monitoring_plans mp
             LEFT JOIN clinics c ON c.id=mp.clinic_id
             LEFT JOIN users u ON u.id=mp.doctor_user_id
             WHERE mp.patient_id=? AND mp.status='active'
             ORDER BY COALESCE(mp.next_control_date, mp.start_date) ASC, mp.id DESC",
            [(int)$patientId]
        );
    }

    public function findMonitoringPlan($id) {
        return $this->one("SELECT * FROM patient_monitoring_plans WHERE id=?", [(int)$id]);
    }

    public function updateMonitoringStatus($id, $status) {
        return $this->updateById('patient_monitoring_plans', (int)$id, [
            'status' => $status,
            'updated_at' => now(),
            'end_date' => in_array($status, ['completed', 'cancelled'], true) ? today() : null,
        ]);
    }

    public function patientSummaryList($branchId, $keyword = '') {
        $params = [(int)$branchId];
        $where = "WHERE p.branch_id=?";
        $keyword = trim((string)$keyword);
        if ($keyword !== '') {
            $where .= " AND (p.name LIKE ? OR p.medical_record_no LIKE ? OR p.nik LIKE ? OR p.phone LIKE ? OR REPLACE(p.phone,'-','') LIKE ?)";
            $like = '%' . $keyword . '%';
            $keywordDigits = digits_only($keyword);
            $digitsLike = $keywordDigits === '' ? '__PHONE_NO_MATCH__' : '%' . $keywordDigits . '%';
            array_push($params, $like, $like, $like, $like, $digitsLike);
        }

        return $this->all(
            "SELECT p.*,
                    COUNT(DISTINCT v.id) AS total_visits,
                    MAX(v.visit_date) AS last_visit_date,
                    SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT d.diagnosis_name ORDER BY v.visit_date DESC SEPARATOR '||'), '||', 1) AS last_diagnosis,
                    COUNT(DISTINCT CASE WHEN mp.status='active' THEN mp.id END) AS active_monitoring,
                    MIN(CASE WHEN mp.status='active' THEN mp.next_control_date END) AS nearest_control_date
             FROM patients p
             LEFT JOIN visits v ON v.patient_id=p.id
             LEFT JOIN diagnoses d ON d.visit_id=v.id
             LEFT JOIN patient_monitoring_plans mp ON mp.patient_id=p.id
             {$where}
             GROUP BY p.id
             ORDER BY COALESCE(MAX(v.visit_date), p.created_at) DESC, p.id DESC",
            $params
        );
    }

    public function patientTimeline($patientId, $limit = 30) {
        $limit = max(1, (int)$limit);
        return $this->all(
            "SELECT v.*, c.name clinic_name, u.name doctor_name,
                    q.queue_number,
                    d.icd_code, d.diagnosis_name, d.soap_notes, d.treatment_notes,
                    vs.blood_pressure, vs.temperature, vs.weight, vs.height, vs.pulse,
                    mr.anamnesis, mr.complaint_history, mr.physical_exam, mr.subjective_notes, mr.objective_notes,
                    mr.assessment_notes, mr.plan_notes, mr.diagnosis_secondary, mr.procedure_notes, mr.lab_notes,
                    mr.radiology_notes, mr.allergy_confirmation, mr.condition_flags, mr.control_plan,
                    mr.next_control_date, mr.referral_notes, mr.special_condition, mr.special_condition_details,
                    (
                        SELECT GROUP_CONCAT(CONCAT(s.name, ' (', vs2.qty, ')') SEPARATOR ', ')
                        FROM visit_services vs2
                        JOIN services s ON s.id=vs2.service_id
                        WHERE vs2.visit_id=v.id
                    ) AS services_text,
                    (
                        SELECT GROUP_CONCAT(CONCAT(m.name, ' x', pi.qty, IF(pi.dosage IS NOT NULL AND pi.dosage <> '', CONCAT(' - ', pi.dosage), '')) SEPARATOR ', ')
                        FROM prescriptions p2
                        JOIN prescription_items pi ON pi.prescription_id=p2.id
                        JOIN medicines m ON m.id=pi.medicine_id
                        WHERE p2.visit_id=v.id
                    ) AS medicines_text
             FROM visits v
             LEFT JOIN clinics c ON c.id=v.clinic_id
             LEFT JOIN users u ON u.id=v.doctor_user_id
             LEFT JOIN queues q ON q.id=v.queue_id
             LEFT JOIN diagnoses d ON d.visit_id=v.id
             LEFT JOIN vital_signs vs ON vs.visit_id=v.id
             LEFT JOIN medical_records mr ON mr.visit_id=v.id
             WHERE v.patient_id=?
             ORDER BY v.visit_date DESC, v.id DESC
             LIMIT {$limit}",
            [(int)$patientId]
        );
    }

    public function patientDashboard($patientId) {
        return $this->one(
            "SELECT p.id,
                    COUNT(DISTINCT v.id) AS total_visits,
                    MAX(v.visit_date) AS last_visit_date,
                    COUNT(DISTINCT CASE WHEN DATE(v.visit_date)=CURDATE() THEN v.id END) AS visits_today,
                    COUNT(DISTINCT CASE WHEN mp.status='active' THEN mp.id END) AS active_monitoring,
                    MIN(CASE WHEN mp.status='active' THEN mp.next_control_date END) AS nearest_control_date,
                    SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT d.diagnosis_name ORDER BY v.visit_date DESC SEPARATOR '||'), '||', 1) AS last_diagnosis
             FROM patients p
             LEFT JOIN visits v ON v.patient_id=p.id
             LEFT JOIN diagnoses d ON d.visit_id=v.id
             LEFT JOIN patient_monitoring_plans mp ON mp.patient_id=p.id
             WHERE p.id=?
             GROUP BY p.id",
            [(int)$patientId]
        );
    }
}

<?php
class VisitModel extends Model {
    public function allOpen($branchId) {
        return $this->all("
            SELECT v.*, p.name patient_name, c.name clinic_name, q.queue_number, q.status AS queue_status
            FROM visits v
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            LEFT JOIN queues q ON q.id=v.queue_id
            WHERE v.branch_id=? AND v.status <> 'completed'
            ORDER BY FIELD(v.status,'examined','called','registered'), v.id ASC
        ", [$branchId]);
    }

    public function find($id) {
        return $this->one("
            SELECT v.*, p.name patient_name, p.medical_record_no, p.gender, p.birth_date, p.phone, c.name clinic_name
            FROM visits v
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            WHERE v.id=?
        ", [$id]);
    }

    public function diagnosis($visitId) {
        return $this->one("SELECT * FROM diagnoses WHERE visit_id=?", [$visitId]);
    }

    public function vitals($visitId) {
        return $this->one("SELECT * FROM vital_signs WHERE visit_id=?", [$visitId]);
    }

    public function prescription($visitId) {
        return $this->one("SELECT * FROM prescriptions WHERE visit_id=?", [$visitId]);
    }

    public function prescriptionItems($visitId) {
        return $this->all("
            SELECT pi.*, m.name medicine_name, p.id prescription_id, p.status prescription_status
            FROM prescriptions p
            JOIN prescription_items pi ON pi.prescription_id=p.id
            JOIN medicines m ON m.id=pi.medicine_id
            WHERE p.visit_id=?
            ORDER BY pi.id ASC
        ", [$visitId]);
    }

    public function services($visitId) {
        return $this->all("
            SELECT vs.*, s.name service_name, s.category
            FROM visit_services vs
            JOIN services s ON s.id=vs.service_id
            WHERE vs.visit_id=?
            ORDER BY vs.id ASC
        ", [$visitId]);
    }
}

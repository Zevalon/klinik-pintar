<?php
class BillingModel extends Model {
    public function cashierQueue($branchId) {
        return $this->all("
            SELECT v.id AS visit_id, v.branch_id, v.visit_date, v.complaint, v.visit_type, v.status AS visit_status,
                   p.name patient_name, p.medical_record_no, p.nik, p.gender, p.phone,
                   c.name clinic_name, q.queue_number,
                   d.icd_code, d.diagnosis_name, d.soap_notes, d.treatment_notes,
                   pr.id AS prescription_id, pr.status AS prescription_status, pr.dispensed_at,
                   i.id AS invoice_id, i.invoice_no, i.subtotal, i.discount, i.grand_total, i.status AS invoice_status,
                   i.created_at AS invoice_created_at, i.updated_at AS invoice_updated_at, i.paid_at
            FROM visits v
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            LEFT JOIN queues q ON q.id=v.queue_id
            LEFT JOIN diagnoses d ON d.visit_id=v.id
            LEFT JOIN prescriptions pr ON pr.visit_id=v.id
            LEFT JOIN invoices i ON i.visit_id=v.id
            WHERE v.branch_id=?
              AND v.status='completed'
              AND (
                    pr.id IS NULL
                    OR pr.status='dispensed'
                    OR COALESCE(i.status, '')='paid'
                  )
            ORDER BY CASE WHEN COALESCE(i.status, 'ready')='paid' THEN 2 ELSE 1 END,
                     COALESCE(i.updated_at, i.created_at, pr.dispensed_at, v.updated_at, v.created_at, v.visit_date) DESC,
                     v.id DESC
        ", [$branchId]);
    }

    public function invoiceByVisit($visitId) {
        return $this->one("SELECT * FROM invoices WHERE visit_id=?", [$visitId]);
    }

    public function invoiceItems($invoiceId) {
        return $this->all("SELECT * FROM invoice_items WHERE invoice_id=? ORDER BY id ASC", [$invoiceId]);
    }

    public function payments($invoiceId) {
        return $this->all("SELECT * FROM payments WHERE invoice_id=? ORDER BY id ASC", [$invoiceId]);
    }

    public function totalPaid($invoiceId) {
        $row = $this->one("SELECT COALESCE(SUM(amount), 0) total FROM payments WHERE invoice_id=?", [$invoiceId]);
        return (float)($row['total'] ?? 0);
    }

    public function findDetailed($invoiceId, $branchId) {
        return $this->one("
            SELECT i.*, v.visit_date, v.visit_type, v.complaint,
                   p.name patient_name, p.medical_record_no, p.nik, p.gender, p.birth_date, p.phone, p.address,
                   b.name branch_name, b.city branch_city, b.address branch_address, b.phone branch_phone,
                   c.name clinic_name
            FROM invoices i
            JOIN visits v ON v.id=i.visit_id
            JOIN patients p ON p.id=v.patient_id
            JOIN branches b ON b.id=i.branch_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            WHERE i.id=? AND i.branch_id=?
            LIMIT 1
        ", [(int)$invoiceId, (int)$branchId]);
    }
}

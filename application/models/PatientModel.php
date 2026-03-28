<?php
class PatientModel extends Model {
    public function allByBranch($branchId) {
        return $this->all("SELECT * FROM patients WHERE branch_id=? ORDER BY id DESC", [$branchId]);
    }

    public function searchByKeyword($branchId, $keyword) {
        $keyword = trim((string)$keyword);
        if ($keyword === '') {
            return [];
        }
        $like = '%' . $keyword . '%';
        return $this->all("SELECT * FROM patients WHERE branch_id=? AND (name LIKE ? OR nik LIKE ? OR medical_record_no LIKE ? OR phone LIKE ?) ORDER BY name ASC LIMIT 20", [$branchId, $like, $like, $like, $like]);
    }

    public function createMedicalRecordNo($branchId) {
        $row = $this->one("SELECT COUNT(*) total FROM patients WHERE branch_id=?", [$branchId]);
        return sprintf('RM-%03d-%05d', $branchId, ((int)$row['total']) + 1);
    }

    public function find($id) {
        return $this->one("SELECT * FROM patients WHERE id=?", [$id]);
    }

    public function findExistingForRegistration($branchId, $nik, $phone, $birthDate) {
        $conditions = [];
        $params = [$branchId];
        $sql = "SELECT * FROM patients WHERE branch_id=? AND (";
        if ($nik) {
            $conditions[] = "nik=?";
            $params[] = $nik;
        }
        if ($phone && $birthDate) {
            $conditions[] = "(phone=? AND birth_date=?)";
            $params[] = $phone;
            $params[] = $birthDate;
        }
        if (!$conditions) {
            return null;
        }
        $sql .= implode(' OR ', $conditions) . ") ORDER BY id DESC LIMIT 1";
        return $this->one($sql, $params);
    }
}

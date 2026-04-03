<?php
class ClinicModel extends Model {
    public function allActive() {
        return $this->all("SELECT * FROM clinics WHERE is_active=1 ORDER BY branch_id, name");
    }

    public function byBranch($branchId, $onlyActive = true) {
        $sql = "SELECT * FROM clinics WHERE branch_id=?";
        $params = [(int)$branchId];
        if ($onlyActive) {
            $sql .= " AND is_active=1";
        }
        $sql .= " ORDER BY name";
        return $this->all($sql, $params);
    }

    public function managementList($branchId) {
        return $this->all("\n            SELECT c.*,\n                   (SELECT COUNT(*) FROM queues q WHERE q.branch_id=c.branch_id AND q.clinic_id=c.id AND DATE(q.queue_date)=CURDATE()) AS queues_today,\n                   (SELECT COUNT(*) FROM queues q WHERE q.branch_id=c.branch_id AND q.clinic_id=c.id AND DATE(q.queue_date)=CURDATE() AND q.status IN ('waiting','called','examined','pending')) AS open_queues_today,\n                   (SELECT COUNT(*) FROM visits v WHERE v.branch_id=c.branch_id AND v.clinic_id=c.id) AS visits_total\n            FROM clinics c\n            WHERE c.branch_id=?\n            ORDER BY c.is_active DESC, c.name ASC\n        ", [(int)$branchId]);
    }

    public function summaryByBranch($branchId) {
        return [
            'total' => (int)($this->one("SELECT COUNT(*) AS total FROM clinics WHERE branch_id=?", [(int)$branchId])['total'] ?? 0),
            'active' => (int)($this->one("SELECT COUNT(*) AS total FROM clinics WHERE branch_id=? AND is_active=1", [(int)$branchId])['total'] ?? 0),
            'inactive' => (int)($this->one("SELECT COUNT(*) AS total FROM clinics WHERE branch_id=? AND is_active=0", [(int)$branchId])['total'] ?? 0),
        ];
    }

    public function find($id) {
        return $this->one("SELECT * FROM clinics WHERE id=?", [(int)$id]);
    }

    public function findInBranch($id, $branchId) {
        return $this->one("SELECT * FROM clinics WHERE id=? AND branch_id=?", [(int)$id, (int)$branchId]);
    }

    public function existsByName($branchId, $name, $excludeId = null) {
        $sql = "SELECT id FROM clinics WHERE branch_id=? AND LOWER(name)=LOWER(?)";
        $params = [(int)$branchId, trim((string)$name)];
        if ($excludeId) {
            $sql .= " AND id<>?";
            $params[] = (int)$excludeId;
        }
        $sql .= " LIMIT 1";
        return (bool)$this->one($sql, $params);
    }

    public function hasOpenQueuesToday($branchId, $clinicId) {
        $row = $this->one("SELECT COUNT(*) AS total FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE() AND status IN ('waiting','called','examined','pending')", [(int)$branchId, (int)$clinicId]);
        return (int)($row['total'] ?? 0) > 0;
    }

    public function setQueueState($clinicId, $state) {
        return $this->exec("UPDATE clinics SET queue_state=?, updated_at=? WHERE id=?", [$state, now(), (int)$clinicId]);
    }
}

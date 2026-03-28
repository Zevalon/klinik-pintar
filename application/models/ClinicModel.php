<?php
class ClinicModel extends Model {
    public function allActive() {
        return $this->all("SELECT * FROM clinics WHERE is_active=1 ORDER BY branch_id, name");
    }

    public function byBranch($branchId) {
        return $this->all("SELECT * FROM clinics WHERE branch_id=? AND is_active=1 ORDER BY name", [$branchId]);
    }

    public function find($id) {
        return $this->one("SELECT * FROM clinics WHERE id=?", [$id]);
    }

    public function setQueueState($clinicId, $state) {
        return $this->exec("UPDATE clinics SET queue_state=?, updated_at=? WHERE id=?", [$state, now(), (int)$clinicId]);
    }
}

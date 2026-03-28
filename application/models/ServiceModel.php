<?php
class ServiceModel extends Model {
    public function allByBranch($branchId) {
        return $this->all("SELECT * FROM services WHERE branch_id=? AND is_active=1 ORDER BY category, name", [$branchId]);
    }

    public function consultationServices($branchId) {
        return $this->all("SELECT * FROM services WHERE branch_id=? AND is_active=1 AND category='consultation' ORDER BY name", [$branchId]);
    }

    public function procedureServices($branchId) {
        return $this->all("SELECT * FROM services WHERE branch_id=? AND is_active=1 AND category='procedure' ORDER BY name", [$branchId]);
    }

    public function find($id) {
        return $this->one("SELECT * FROM services WHERE id=?", [$id]);
    }
}

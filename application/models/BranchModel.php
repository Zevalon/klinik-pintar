<?php
class BranchModel extends Model {
    public function allActive() {
        return $this->all("SELECT * FROM branches WHERE is_active=1 ORDER BY name");
    }

    public function firstActive() {
        return $this->one("SELECT * FROM branches WHERE is_active=1 ORDER BY id ASC LIMIT 1");
    }

    public function find($id) {
        return $this->one("SELECT * FROM branches WHERE id=?", [$id]);
    }

    public function withStats() {
        return $this->all("
            SELECT b.*,
                   (SELECT COUNT(*) FROM visits v WHERE v.branch_id=b.id AND DATE(v.visit_date)=CURDATE()) AS visits_today,
                   (SELECT COALESCE(SUM(p.amount),0) FROM payments p WHERE p.branch_id=b.id AND DATE(p.paid_at)=CURDATE()) AS revenue_today
            FROM branches b
            ORDER BY b.name
        ");
    }
}

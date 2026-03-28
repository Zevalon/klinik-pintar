<?php
class MedicineModel extends Model {
    public function allByBranch($branchId) {
        return $this->all("SELECT * FROM medicines WHERE branch_id=? ORDER BY name", [$branchId]);
    }

    public function find($id) {
        return $this->one("SELECT * FROM medicines WHERE id=?", [$id]);
    }

    public function stockOnHand($medicineId, $branchId) {
        $row = $this->one("
            SELECT COALESCE(SUM(
                CASE
                    WHEN movement_type IN ('opening','purchase','adjustment_in','transfer_in','return_in') THEN qty
                    ELSE -qty
                END
            ),0) stock
            FROM stock_movements
            WHERE medicine_id=? AND branch_id=?
        ", [$medicineId, $branchId]);
        return (float)($row['stock'] ?? 0);
    }

    public function stockCards($branchId, $medicineId) {
        return $this->all("
            SELECT *
            FROM stock_movements
            WHERE branch_id=? AND medicine_id=?
            ORDER BY id DESC
            LIMIT 100
        ", [$branchId, $medicineId]);
    }
}

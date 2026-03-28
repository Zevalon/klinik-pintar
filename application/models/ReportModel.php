<?php
class ReportModel extends Model {
    public function dashboard($branchId) {
        return [
            'patients_today' => $this->one("SELECT COUNT(*) total FROM visits WHERE branch_id=? AND DATE(visit_date)=CURDATE()", [$branchId])['total'] ?? 0,
            'queue_waiting' => $this->one("SELECT COUNT(*) total FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='waiting'", [$branchId])['total'] ?? 0,
            'prescriptions_waiting' => $this->one("SELECT COUNT(*) total FROM prescriptions WHERE branch_id=? AND status IN ('draft','prepared')", [$branchId])['total'] ?? 0,
            'revenue_today' => $this->one("SELECT COALESCE(SUM(amount),0) total FROM payments WHERE branch_id=? AND DATE(paid_at)=CURDATE()", [$branchId])['total'] ?? 0,
        ];
    }



    public function dailyFinance($branchId, $limit = 30) {
        $limit = (int)$limit;
        if ($limit < 1) { $limit = 30; }
        return $this->all("
            SELECT DATE(paid_at) AS paid_date, COALESCE(SUM(amount),0) AS total
            FROM payments
            WHERE branch_id=? AND DATE(paid_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(paid_at)
            ORDER BY paid_date DESC
            LIMIT {$limit}
        ", [$branchId, max(0, $limit - 1)]);
    }

    public function dailyExpenses($branchId, $limit = 30) {
        $limit = (int)$limit;
        if ($limit < 1) { $limit = 30; }
        return $this->all("
            SELECT DATE(expense_date) AS expense_day, COALESCE(SUM(amount),0) AS total
            FROM expenses
            WHERE branch_id=? AND DATE(expense_date) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(expense_date)
            ORDER BY expense_day DESC
            LIMIT {$limit}
        ", [$branchId, max(0, $limit - 1)]);
    }

    public function visitSummaryToday($branchId) {
        return $this->all("
            SELECT c.name AS clinic_name, COUNT(*) AS total
            FROM visits v
            LEFT JOIN clinics c ON c.id=v.clinic_id
            WHERE v.branch_id=? AND DATE(v.visit_date)=CURDATE()
            GROUP BY v.clinic_id, c.name
            ORDER BY total DESC, clinic_name ASC
        ", [$branchId]);
    }

    public function stockAlerts($branchId) {
        return $this->all("
            SELECT m.*,
                   COALESCE((
                       SELECT SUM(CASE WHEN movement_type IN ('opening','purchase','adjustment_in','transfer_in','return_in') THEN qty ELSE -qty END)
                       FROM stock_movements sm
                       WHERE sm.medicine_id=m.id AND sm.branch_id=m.branch_id
                   ),0) stock
            FROM medicines m
            WHERE m.branch_id=?
            HAVING stock <= min_stock
            ORDER BY stock ASC
            LIMIT 10
        ", [$branchId]);
    }

    public function expensesToday($branchId) {
        return $this->one("
            SELECT COALESCE(SUM(amount),0) total
            FROM expenses
            WHERE branch_id=? AND DATE(expense_date)=CURDATE()
        ", [$branchId])['total'] ?? 0;
    }

    public function incomeSummary($branchId, $startDate, $endDate, $group = 'day') {
        [$expr, $labelExpr] = $this->periodExpressions('paid_at', $group);
        return $this->all("
            SELECT {$expr} AS period_key,
                   {$labelExpr} AS period_label,
                   COUNT(*) AS transaction_count,
                   COALESCE(SUM(amount),0) AS total_amount
            FROM payments
            WHERE branch_id=? AND DATE(paid_at) BETWEEN ? AND ?
            GROUP BY period_key, period_label
            ORDER BY period_key DESC
        ", [$branchId, $startDate, $endDate]);
    }

    public function expenseSummary($branchId, $startDate, $endDate, $group = 'day') {
        [$expr, $labelExpr] = $this->periodExpressions('expense_date', $group);
        return $this->all("
            SELECT {$expr} AS period_key,
                   {$labelExpr} AS period_label,
                   COUNT(*) AS transaction_count,
                   COALESCE(SUM(amount),0) AS total_amount
            FROM expenses
            WHERE branch_id=? AND DATE(expense_date) BETWEEN ? AND ?
            GROUP BY period_key, period_label
            ORDER BY period_key DESC
        ", [$branchId, $startDate, $endDate]);
    }

    public function expenseItems($branchId, $startDate, $endDate) {
        return $this->all("
            SELECT e.*, u.name AS created_by_name
            FROM expenses e
            LEFT JOIN users u ON u.id=e.created_by
            WHERE e.branch_id=? AND DATE(e.expense_date) BETWEEN ? AND ?
            ORDER BY e.expense_date DESC, e.id DESC
            LIMIT 200
        ", [$branchId, $startDate, $endDate]);
    }

    public function stockSummary($branchId, $startDate, $endDate, $group = 'day', $direction = 'in') {
        [$expr, $labelExpr] = $this->periodExpressions('created_at', $group);
        $incoming = "('opening','purchase','adjustment_in','transfer_in','return_in')";
        $where = $direction === 'out'
            ? "movement_type NOT IN {$incoming}"
            : "movement_type IN {$incoming}";

        return $this->all("
            SELECT {$expr} AS period_key,
                   {$labelExpr} AS period_label,
                   COUNT(*) AS transaction_count,
                   COALESCE(SUM(qty),0) AS total_qty,
                   COALESCE(SUM(qty * unit_cost),0) AS total_value
            FROM stock_movements
            WHERE branch_id=?
              AND DATE(created_at) BETWEEN ? AND ?
              AND {$where}
            GROUP BY period_key, period_label
            ORDER BY period_key DESC
        ", [$branchId, $startDate, $endDate]);
    }

    private function periodExpressions($column, $group) {
        switch ($group) {
            case 'month':
                return [
                    "DATE_FORMAT({$column}, '%Y-%m')",
                    "DATE_FORMAT({$column}, '%m-%Y')",
                ];
            case 'year':
                return [
                    "DATE_FORMAT({$column}, '%Y')",
                    "DATE_FORMAT({$column}, '%Y')",
                ];
            case 'day':
            default:
                return [
                    "DATE_FORMAT({$column}, '%Y-%m-%d')",
                    "DATE_FORMAT({$column}, '%d-%m-%Y')",
                ];
        }
    }
}

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

    public function stockAlerts($branchId, $limit = 10) {
        $limit = max(1, (int)$limit);
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
            ORDER BY stock ASC, m.name ASC
            LIMIT {$limit}
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

    public function frontOfficeStats($branchId) {
        return [
            'registrations_today' => $this->scalar("SELECT COUNT(*) FROM patients WHERE branch_id=? AND DATE(COALESCE(created_at, updated_at))=CURDATE()", [$branchId]),
            'queue_waiting' => $this->scalar("SELECT COUNT(*) FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='waiting'", [$branchId]),
            'queue_called' => $this->scalar("SELECT COUNT(*) FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='called'", [$branchId]),
            'visits_completed_today' => $this->scalar("SELECT COUNT(*) FROM visits WHERE branch_id=? AND DATE(visit_date)=CURDATE() AND status='completed'", [$branchId]),
        ];
    }

    public function recentRegistrations($branchId, $limit = 8) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT p.*
            FROM patients p
            WHERE p.branch_id=?
            ORDER BY COALESCE(p.updated_at, p.created_at) DESC, p.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function clinicQueueSummaryToday($branchId) {
        return $this->all("
            SELECT c.id,
                   c.name AS clinic_name,
                   SUM(CASE WHEN q.status='waiting' THEN 1 ELSE 0 END) AS waiting_total,
                   SUM(CASE WHEN q.status='called' THEN 1 ELSE 0 END) AS called_total,
                   SUM(CASE WHEN q.status='examined' THEN 1 ELSE 0 END) AS examined_total,
                   SUM(CASE WHEN q.status='pending' THEN 1 ELSE 0 END) AS pending_total,
                   COUNT(q.id) AS total_queue
            FROM clinics c
            LEFT JOIN queues q ON q.clinic_id=c.id AND q.branch_id=c.branch_id AND DATE(q.queue_date)=CURDATE()
            WHERE c.branch_id=? AND c.is_active=1
            GROUP BY c.id, c.name
            ORDER BY c.name ASC
        ", [$branchId]);
    }

    public function upcomingControls($branchId, $days = 7, $limit = 8) {
        $days = max(1, (int)$days);
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT mp.*, p.name AS patient_name, p.medical_record_no, c.name AS clinic_name, u.name AS doctor_name
            FROM patient_monitoring_plans mp
            JOIN patients p ON p.id=mp.patient_id
            LEFT JOIN clinics c ON c.id=mp.clinic_id
            LEFT JOIN users u ON u.id=mp.doctor_user_id
            WHERE mp.branch_id=?
              AND mp.status='active'
              AND mp.next_control_date IS NOT NULL
              AND mp.next_control_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL {$days} DAY)
            ORDER BY mp.next_control_date ASC, mp.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function clinicalStats($branchId, $doctorUserId = null) {
        $params = [$branchId];
        $completedSql = "SELECT COUNT(*) FROM visits WHERE branch_id=? AND DATE(visit_date)=CURDATE() AND status='completed'";
        if ($doctorUserId) {
            $completedSql .= " AND doctor_user_id=?";
            $params[] = (int)$doctorUserId;
        }

        return [
            'queue_called' => $this->scalar("SELECT COUNT(*) FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='called'", [$branchId]),
            'queue_examined' => $this->scalar("SELECT COUNT(*) FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='examined'", [$branchId]),
            'completed_today' => $this->scalar($completedSql, $params),
            'followup_due' => $this->scalar("SELECT COUNT(*) FROM patient_monitoring_plans WHERE branch_id=? AND status='active' AND next_control_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)", [$branchId]),
        ];
    }

    public function nurseStats($branchId) {
        return [
            'queue_called' => $this->scalar("SELECT COUNT(*) FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='called'", [$branchId]),
            'queue_examined' => $this->scalar("SELECT COUNT(*) FROM queues WHERE branch_id=? AND DATE(queue_date)=CURDATE() AND status='examined'", [$branchId]),
            'vitals_filled_today' => $this->scalar("SELECT COUNT(*) FROM vital_signs vs JOIN visits v ON v.id=vs.visit_id WHERE v.branch_id=? AND DATE(v.visit_date)=CURDATE()", [$branchId]),
            'followup_due' => $this->scalar("SELECT COUNT(*) FROM patient_monitoring_plans WHERE branch_id=? AND status='active' AND next_control_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)", [$branchId]),
        ];
    }

    public function openClinicalVisits($branchId, $limit = 10) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT v.id AS visit_id, v.visit_date, v.status AS visit_status, v.visit_type,
                   p.name AS patient_name, p.medical_record_no, p.phone,
                   c.name AS clinic_name, q.queue_number, q.status AS queue_status,
                   d.diagnosis_name
            FROM visits v
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            LEFT JOIN queues q ON q.id=v.queue_id
            LEFT JOIN diagnoses d ON d.visit_id=v.id
            WHERE v.branch_id=?
              AND DATE(v.visit_date)=CURDATE()
              AND v.status IN ('registered','called','examined')
            ORDER BY FIELD(v.status,'examined','called','registered'), v.visit_date ASC, v.id ASC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function recentClinicalVisits($branchId, $doctorUserId = null, $limit = 8) {
        $limit = max(1, (int)$limit);
        $sql = "
            SELECT v.id AS visit_id, v.visit_date, v.status AS visit_status,
                   p.name AS patient_name, p.medical_record_no,
                   c.name AS clinic_name, d.diagnosis_name
            FROM visits v
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            LEFT JOIN diagnoses d ON d.visit_id=v.id
            WHERE v.branch_id=? AND v.status='completed'";
        $params = [$branchId];
        if ($doctorUserId) {
            $sql .= " AND v.doctor_user_id=?";
            $params[] = (int)$doctorUserId;
        }
        $sql .= " ORDER BY v.visit_date DESC, v.id DESC LIMIT {$limit}";
        return $this->all($sql, $params);
    }

    public function recentVitals($branchId, $limit = 8) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT vs.*, v.id AS visit_id, v.visit_date, p.name AS patient_name, p.medical_record_no, c.name AS clinic_name
            FROM vital_signs vs
            JOIN visits v ON v.id=vs.visit_id
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            WHERE v.branch_id=?
            ORDER BY COALESCE(vs.updated_at, vs.created_at) DESC, vs.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function pharmacyStats($branchId) {
        return [
            'draft_total' => $this->scalar("SELECT COUNT(*) FROM prescriptions WHERE branch_id=? AND status='draft'", [$branchId]),
            'prepared_total' => $this->scalar("SELECT COUNT(*) FROM prescriptions WHERE branch_id=? AND status='prepared'", [$branchId]),
            'dispensed_today' => $this->scalar("SELECT COUNT(*) FROM prescriptions WHERE branch_id=? AND status='dispensed' AND DATE(dispensed_at)=CURDATE()", [$branchId]),
            'low_stock_total' => $this->lowStockCount($branchId),
        ];
    }

    public function recentPrescriptions($branchId, $limit = 8) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT p.id, p.status, p.created_at, p.updated_at, p.dispensed_at,
                   v.id AS visit_id, v.visit_date,
                   pa.name AS patient_name, pa.medical_record_no,
                   c.name AS clinic_name,
                   COUNT(pi.id) AS item_count
            FROM prescriptions p
            JOIN visits v ON v.id=p.visit_id
            JOIN patients pa ON pa.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            LEFT JOIN prescription_items pi ON pi.prescription_id=p.id
            WHERE p.branch_id=?
            GROUP BY p.id, p.status, p.created_at, p.updated_at, p.dispensed_at, v.id, v.visit_date, pa.name, pa.medical_record_no, c.name
            ORDER BY CASE WHEN p.status='dispensed' THEN 2 ELSE 1 END,
                     COALESCE(p.updated_at, p.created_at, v.visit_date) DESC,
                     p.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function topDispensedMedicinesToday($branchId, $limit = 6) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT m.name, SUM(pi.qty) AS total_qty, COUNT(DISTINCT p.id) AS prescription_count
            FROM prescriptions p
            JOIN prescription_items pi ON pi.prescription_id=p.id
            JOIN medicines m ON m.id=pi.medicine_id
            WHERE p.branch_id=?
              AND p.status='dispensed'
              AND DATE(p.dispensed_at)=CURDATE()
            GROUP BY m.id, m.name
            ORDER BY total_qty DESC, prescription_count DESC, m.name ASC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function cashierStats($branchId) {
        return [
            'ready_total' => $this->scalar("
                SELECT COUNT(*)
                FROM visits v
                LEFT JOIN prescriptions pr ON pr.visit_id=v.id
                LEFT JOIN invoices i ON i.visit_id=v.id
                WHERE v.branch_id=?
                  AND v.status='completed'
                  AND (pr.id IS NULL OR pr.status='dispensed' OR COALESCE(i.status, '')='paid')
                  AND COALESCE(i.status, 'ready') <> 'paid'
            ", [$branchId]),
            'transactions_today' => $this->scalar("SELECT COUNT(*) FROM payments WHERE branch_id=? AND DATE(paid_at)=CURDATE()", [$branchId]),
            'revenue_today' => $this->scalar("SELECT COALESCE(SUM(amount),0) FROM payments WHERE branch_id=? AND DATE(paid_at)=CURDATE()", [$branchId]),
            'avg_transaction' => $this->scalar("SELECT COALESCE(AVG(amount),0) FROM payments WHERE branch_id=? AND DATE(paid_at)=CURDATE()", [$branchId]),
        ];
    }

    public function readyInvoices($branchId, $limit = 8) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT v.id AS visit_id, v.visit_date,
                   p.name AS patient_name, p.medical_record_no,
                   c.name AS clinic_name,
                   i.id AS invoice_id, i.invoice_no, i.grand_total, i.status AS invoice_status,
                   pr.status AS prescription_status
            FROM visits v
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            LEFT JOIN prescriptions pr ON pr.visit_id=v.id
            LEFT JOIN invoices i ON i.visit_id=v.id
            WHERE v.branch_id=?
              AND v.status='completed'
              AND (pr.id IS NULL OR pr.status='dispensed' OR COALESCE(i.status, '')='paid')
              AND COALESCE(i.status, 'ready') <> 'paid'
            ORDER BY COALESCE(i.updated_at, i.created_at, pr.dispensed_at, v.updated_at, v.visit_date) DESC, v.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function recentPayments($branchId, $limit = 8) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT pay.*, i.invoice_no, i.grand_total,
                   v.id AS visit_id, v.visit_date,
                   p.name AS patient_name, p.medical_record_no,
                   c.name AS clinic_name
            FROM payments pay
            JOIN invoices i ON i.id=pay.invoice_id
            JOIN visits v ON v.id=i.visit_id
            JOIN patients p ON p.id=v.patient_id
            LEFT JOIN clinics c ON c.id=v.clinic_id
            WHERE pay.branch_id=?
            ORDER BY pay.paid_at DESC, pay.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }

    public function paymentMethodSummaryToday($branchId) {
        return $this->all("
            SELECT payment_method, COUNT(*) AS transaction_count, COALESCE(SUM(amount),0) AS total_amount
            FROM payments
            WHERE branch_id=? AND DATE(paid_at)=CURDATE()
            GROUP BY payment_method
            ORDER BY total_amount DESC, transaction_count DESC
        ", [$branchId]);
    }

    public function inventoryStats($branchId) {
        return [
            'medicine_total' => $this->scalar("SELECT COUNT(*) FROM medicines WHERE branch_id=? AND is_active=1", [$branchId]),
            'low_stock_total' => $this->lowStockCount($branchId),
            'stock_in_today' => $this->scalar("SELECT COALESCE(SUM(qty),0) FROM stock_movements WHERE branch_id=? AND DATE(created_at)=CURDATE() AND movement_type IN ('opening','purchase','adjustment_in','transfer_in','return_in')", [$branchId]),
            'stock_out_today' => $this->scalar("SELECT COALESCE(SUM(qty),0) FROM stock_movements WHERE branch_id=? AND DATE(created_at)=CURDATE() AND movement_type NOT IN ('opening','purchase','adjustment_in','transfer_in','return_in')", [$branchId]),
        ];
    }

    public function recentStockMovements($branchId, $limit = 8) {
        $limit = max(1, (int)$limit);
        return $this->all("
            SELECT sm.*, m.name AS medicine_name, m.unit
            FROM stock_movements sm
            JOIN medicines m ON m.id=sm.medicine_id
            WHERE sm.branch_id=?
            ORDER BY sm.created_at DESC, sm.id DESC
            LIMIT {$limit}
        ", [$branchId]);
    }


    private function lowStockCount($branchId) {
        $row = $this->one("
            SELECT COUNT(*) AS total
            FROM (
                SELECT m.id,
                       COALESCE((
                           SELECT SUM(CASE WHEN movement_type IN ('opening','purchase','adjustment_in','transfer_in','return_in') THEN qty ELSE -qty END)
                           FROM stock_movements sm
                           WHERE sm.medicine_id=m.id AND sm.branch_id=m.branch_id
                       ),0) AS stock,
                       m.min_stock
                FROM medicines m
                WHERE m.branch_id=?
            ) items
            WHERE stock <= min_stock
        ", [$branchId]);
        return (int)($row['total'] ?? 0);
    }

    private function scalar($sql, array $params = []) {
        $row = $this->one($sql, $params);
        if (!$row) {
            return 0;
        }
        $value = array_shift($row);
        return is_numeric($value) ? (float)$value : $value;
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

<?php
class Reports extends Controller {
    public function index() {
        $this->requireLogin();
        $this->branchRequired();

        $report = new ReportModel();
        $financeGroup = $this->sanitizeGroup($this->input('finance_group', 'day'));
        $stockGroup = $this->sanitizeGroup($this->input('stock_group', 'day'));
        $financeStart = $this->sanitizeDate($this->input('finance_start', date('Y-m-01')), date('Y-m-01'));
        $financeEnd = $this->sanitizeDate($this->input('finance_end', today()), today());
        $stockStart = $this->sanitizeDate($this->input('stock_start', date('Y-m-01')), date('Y-m-01'));
        $stockEnd = $this->sanitizeDate($this->input('stock_end', today()), today());

        if ($financeStart > $financeEnd) {
            [$financeStart, $financeEnd] = [$financeEnd, $financeStart];
        }
        if ($stockStart > $stockEnd) {
            [$stockStart, $stockEnd] = [$stockEnd, $stockStart];
        }

        $this->render('reports/index', [
            'financeFilters' => [
                'start' => $financeStart,
                'end' => $financeEnd,
                'group' => $financeGroup,
            ],
            'stockFilters' => [
                'start' => $stockStart,
                'end' => $stockEnd,
                'group' => $stockGroup,
            ],
            'incomeRows' => $report->incomeSummary(current_branch_id(), $financeStart, $financeEnd, $financeGroup),
            'expenseRows' => $report->expenseSummary(current_branch_id(), $financeStart, $financeEnd, $financeGroup),
            'expenseItems' => $report->expenseItems(current_branch_id(), $financeStart, $financeEnd),
            'stockInRows' => $report->stockSummary(current_branch_id(), $stockStart, $stockEnd, $stockGroup, 'in'),
            'stockOutRows' => $report->stockSummary(current_branch_id(), $stockStart, $stockEnd, $stockGroup, 'out'),
            'alerts' => $report->stockAlerts(current_branch_id()),
        ]);
    }

    public function storeExpense() {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $expenseId = $model->insert('expenses', [
            'branch_id' => current_branch_id(),
            'expense_date' => $this->sanitizeDate($this->input('expense_date', today()), today()),
            'category' => trim((string)$this->input('category', 'operasional')),
            'description' => trim((string)$this->input('description')),
            'amount' => $this->parseMoneyInput($this->input('amount', 0)),
            'created_by' => current_user()['id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        log_activity('expense_create', 'Mencatat pengeluaran klinik', 'expenses', $expenseId);
        $this->respondSuccess('Pengeluaran berhasil dicatat.');
        if (!$this->wantsJson()) redirect_to('reports');
    }

    public function closeCash() {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();
        $model = new Model();
        $report = new ReportModel();
        $cashSales = $model->one("SELECT COALESCE(SUM(amount),0) total FROM payments WHERE branch_id=? AND DATE(paid_at)=CURDATE() AND payment_method='cash'", [current_branch_id()])['total'] ?? 0;
        $nonCashSales = $model->one("SELECT COALESCE(SUM(amount),0) total FROM payments WHERE branch_id=? AND DATE(paid_at)=CURDATE() AND payment_method<>'cash'", [current_branch_id()])['total'] ?? 0;
        $expenses = $report->expensesToday(current_branch_id());
        $actualCash = (float)$this->parseMoneyInput($this->input('actual_cash', 0));
        $expected = (float)$cashSales - (float)$expenses;
        $difference = $actualCash - $expected;
        $existing = $model->one("SELECT * FROM daily_cash_closings WHERE branch_id=? AND closing_date=CURDATE()", [current_branch_id()]);
        $payload = [
            'opening_cash' => $this->parseMoneyInput($this->input('opening_cash', 0)),
            'cash_sales' => $cashSales,
            'non_cash_sales' => $nonCashSales,
            'expenses_total' => $expenses,
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'closed_by' => current_user()['id'],
            'closed_at' => now(),
            'updated_at' => now(),
        ];
        if ($existing) {
            $model->updateById('daily_cash_closings', $existing['id'], $payload);
            $closingId = $existing['id'];
        } else {
            $payload['branch_id'] = current_branch_id();
            $payload['closing_date'] = today();
            $payload['created_at'] = now();
            $closingId = $model->insert('daily_cash_closings', $payload);
        }
        log_activity('cash_closing', 'Melakukan closing kas harian', 'daily_cash_closings', $closingId);
        $this->respondSuccess('Closing kas harian berhasil disimpan.');
        if (!$this->wantsJson()) redirect_to('reports');
    }

    public function exportPdf() {
        $this->requireLogin();
        $this->branchRequired();
        $report = new ReportModel();
        $start = date('Y-m-01');
        $end = today();
        $incomeRows = $report->incomeSummary(current_branch_id(), $start, $end, 'day');
        $expenseRows = $report->expenseSummary(current_branch_id(), $start, $end, 'day');
        $stockInRows = $report->stockSummary(current_branch_id(), $start, $end, 'day', 'in');
        $stockOutRows = $report->stockSummary(current_branch_id(), $start, $end, 'day', 'out');
        $incomeRows = $report->incomeSummary(current_branch_id(), $start, $end, 'day');
        $expenseRows = $report->expenseSummary(current_branch_id(), $start, $end, 'day');
        SimplePdf::downloadFinanceReport([
            'branch_name' => current_branch_name(),
            'period_text' => date('d-m-Y', strtotime($start)) . ' s/d ' . date('d-m-Y', strtotime($end)),
            'group_label' => 'Harian',
        ], $incomeRows, $expenseRows, 'laporan-klinik-pintar.pdf');
    }

    private function sanitizeGroup($group) {
        return in_array($group, ['day', 'month', 'year'], true) ? $group : 'day';
    }

    private function sanitizeDate($date, $fallback) {
        $date = trim((string)$date);
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $fallback;
        }
        return $date;
    }

    private function parseMoneyInput($value) {
        $value = trim((string)$value);
        if ($value === '') {
            return 0;
        }
        $value = str_replace(['Rp', 'rp', ' '], '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return (float)preg_replace('/[^0-9\.-]/', '', $value);
    }
}

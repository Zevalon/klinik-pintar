<?php
class BranchExpenses extends Controller {
    public function index() {
        $this->requireRoles(['front_office', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();
        $report = new ReportModel();
        $start = $this->sanitizeDate($this->input('start', date('Y-m-01')), date('Y-m-01'));
        $end = $this->sanitizeDate($this->input('end', today()), today());
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        $group = $this->sanitizeGroup($this->input('group', 'day'));
        $this->render('branch_expenses/index', [
            'filters' => ['start' => $start, 'end' => $end, 'group' => $group],
            'expenseRows' => $report->expenseSummary(current_branch_id(), $start, $end, $group),
            'expenseItems' => $report->expenseItems(current_branch_id(), $start, $end),
        ]);
    }

    public function store() {
        $this->requireRoles(['front_office', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();
        verify_csrf();
        $model = new Model();
        $expenseId = $model->insert('expenses', [
            'branch_id' => current_branch_id(),
            'expense_date' => $this->sanitizeDate($this->input('expense_date', today()), today()),
            'category' => trim((string)$this->input('category', 'operasional')),
            'description' => trim((string)$this->input('description')),
            'amount' => parse_money_input($this->input('amount', 0)),
            'created_by' => current_user()['id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        log_activity('expense_create', 'Mencatat pengeluaran klinik', 'expenses', $expenseId);
        $this->respondSuccess('Pengeluaran cabang berhasil dicatat.');
        if (!$this->wantsJson()) redirect_to('branchexpenses');
    }

    public function exportPdf() {
        $this->requireRoles(['front_office', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();
        $report = new ReportModel();
        $start = $this->sanitizeDate($this->input('start', date('Y-m-01')), date('Y-m-01'));
        $end = $this->sanitizeDate($this->input('end', today()), today());
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        $group = $this->sanitizeGroup($this->input('group', 'day'));
        $expenseRows = $report->expenseSummary(current_branch_id(), $start, $end, $group);
        $expenseItems = $report->expenseItems(current_branch_id(), $start, $end);
        SimplePdf::downloadExpenseReport([
            'branch_name' => current_branch_name(),
            'period_text' => date('d-m-Y', strtotime($start)) . ' s/d ' . date('d-m-Y', strtotime($end)),
            'group_label' => ucfirst($group === 'day' ? 'harian' : ($group === 'month' ? 'bulanan' : 'tahunan')),
        ], $expenseRows, $expenseItems, 'laporan-pengeluaran-cabang.pdf');
    }

    private function sanitizeGroup($group) { return in_array($group, ['day', 'month', 'year'], true) ? $group : 'day'; }
    private function sanitizeDate($date, $fallback) {
        $date = trim((string)$date);
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return $fallback;
        return $date;
    }
}


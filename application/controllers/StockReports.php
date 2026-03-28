<?php
class StockReports extends Controller {
    public function index() {
        $this->requireRoles(['pharmacist', 'inventory', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();

        $report = new ReportModel();
        $group = $this->sanitizeGroup($this->input('group', 'day'));
        $start = $this->sanitizeDate($this->input('start', date('Y-m-01')), date('Y-m-01'));
        $end = $this->sanitizeDate($this->input('end', today()), today());
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $this->render('stock_reports/index', [
            'filters' => ['start' => $start, 'end' => $end, 'group' => $group],
            'stockInRows' => $report->stockSummary(current_branch_id(), $start, $end, $group, 'in'),
            'stockOutRows' => $report->stockSummary(current_branch_id(), $start, $end, $group, 'out'),
            'alerts' => $report->stockAlerts(current_branch_id()),
        ]);
    }

    public function exportPdf() {
        $this->requireRoles(['pharmacist', 'inventory', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();
        $report = new ReportModel();
        $start = $this->sanitizeDate($this->input('start', date('Y-m-01')), date('Y-m-01'));
        $end = $this->sanitizeDate($this->input('end', today()), today());
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        $group = $this->sanitizeGroup($this->input('group', 'day'));
        $stockInRows = $report->stockSummary(current_branch_id(), $start, $end, $group, 'in');
        $stockOutRows = $report->stockSummary(current_branch_id(), $start, $end, $group, 'out');
        SimplePdf::downloadStockReport([
            'branch_name' => current_branch_name(),
            'period_text' => date('d-m-Y', strtotime($start)) . ' s/d ' . date('d-m-Y', strtotime($end)),
            'group_label' => ucfirst($group === 'day' ? 'harian' : ($group === 'month' ? 'bulanan' : 'tahunan')),
        ], $stockInRows, $stockOutRows, 'laporan-stok-klinik-pintar.pdf');
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
}

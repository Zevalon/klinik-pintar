<?php
class Dashboard extends Controller {
    public function index() {
        $this->requireLogin();
        $this->branchRequired();

        $report = new ReportModel();
        $branch = new BranchModel();

        $data = [
            'stats' => $report->dashboard(current_branch_id()),
            'finance' => $report->dailyFinance(current_branch_id()),
            'expenses' => $report->dailyExpenses(current_branch_id()),
            'alerts' => $report->stockAlerts(current_branch_id()),
            'visits_by_clinic' => $report->visitSummaryToday(current_branch_id()),
            'branch_stats' => is_global_user() ? $branch->withStats() : [],
        ];
        $this->render('dashboard/index', $data);
    }
}

<?php
class PublicQueueMonitor extends Controller {
    public function index() {
        $queueModel = new QueueModel();
        $branchModel = new BranchModel();
        $branchId = (int)$this->input('branch_id', 0);
        if ($branchId <= 0) {
            $first = $branchModel->firstActive();
            $branchId = (int)($first['id'] ?? 0);
        }
        $branch = $branchModel->find($branchId);
        if (!$branch) {
            http_response_code(404);
            echo 'Cabang tidak ditemukan.';
            return;
        }

        $this->render('public_queue_monitor/index', [
            'branch' => $branch,
            'branches' => $queueModel->publicBranchOptions(),
            'clinics' => $queueModel->publicMonitorData($branchId),
            'selectedBranchId' => $branchId,
        ], 'layouts/public');
    }

    public function data() {
        $queueModel = new QueueModel();
        $branchModel = new BranchModel();
        $branchId = (int)$this->input('branch_id', 0);
        if ($branchId <= 0) {
            $first = $branchModel->firstActive();
            $branchId = (int)($first['id'] ?? 0);
        }
        $branch = $branchModel->find($branchId);
        if (!$branch) {
            json_response(['success' => false, 'message' => 'Cabang tidak ditemukan.'], 404);
        }
        json_response([
            'success' => true,
            'branch' => $branch,
            'timestamp' => now(),
            'clinics' => $queueModel->publicMonitorData($branchId),
        ]);
    }
}

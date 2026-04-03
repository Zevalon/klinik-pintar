<?php
class Dashboard extends Controller {
    public function index() {
        $this->requireLogin();
        $this->branchRequired();

        $role = current_user()['role_code'] ?? '';
        $userId = (int)(current_user()['id'] ?? 0);
        $branchId = current_branch_id();

        $report = new ReportModel();
        $branch = new BranchModel();
        new MedicalRecordModel();

        $data = [
            'dashboardType' => 'admin',
            'dashboardTitle' => 'Dashboard Operasional Cabang',
            'dashboardDescription' => 'Ringkasan menyeluruh operasional klinik untuk cabang aktif.',
        ];

        switch ($role) {
            case 'front_office':
                $data['dashboardType'] = 'front_office';
                $data['dashboardTitle'] = 'Dashboard Front Office';
                $data['dashboardDescription'] = 'Fokus pada registrasi pasien, antrian, dan kontrol kunjungan yang perlu dipersiapkan.';
                $data['stats'] = $report->frontOfficeStats($branchId);
                $data['recentRegistrations'] = $report->recentRegistrations($branchId, 8);
                $data['queueByClinic'] = $report->clinicQueueSummaryToday($branchId);
                $data['upcomingControls'] = $report->upcomingControls($branchId, 7, 8);
                break;

            case 'doctor':
                $data['dashboardType'] = 'doctor';
                $data['dashboardTitle'] = 'Dashboard Dokter';
                $data['dashboardDescription'] = 'Menampilkan pasien yang perlu ditangani, progres pemeriksaan, dan kontrol lanjutan.';
                $data['stats'] = $report->clinicalStats($branchId, $userId);
                $data['openClinicalVisits'] = $report->openClinicalVisits($branchId, 10);
                $data['recentClinicalVisits'] = $report->recentClinicalVisits($branchId, $userId, 8);
                $data['upcomingControls'] = $report->upcomingControls($branchId, 7, 8);
                break;

            case 'nurse':
                $data['dashboardType'] = 'nurse';
                $data['dashboardTitle'] = 'Dashboard Perawat';
                $data['dashboardDescription'] = 'Ringkasan triase, vital sign, dan pasien yang sedang berjalan pada cabang aktif.';
                $data['stats'] = $report->nurseStats($branchId);
                $data['openClinicalVisits'] = $report->openClinicalVisits($branchId, 10);
                $data['recentVitals'] = $report->recentVitals($branchId, 8);
                $data['upcomingControls'] = $report->upcomingControls($branchId, 7, 8);
                break;

            case 'pharmacist':
                $data['dashboardType'] = 'pharmacist';
                $data['dashboardTitle'] = 'Dashboard Farmasi';
                $data['dashboardDescription'] = 'Fokus pada resep yang harus diproses, obat yang sering keluar, dan alert stok minimum.';
                $data['stats'] = $report->pharmacyStats($branchId);
                $data['recentPrescriptions'] = $report->recentPrescriptions($branchId, 8);
                $data['lowStockItems'] = $report->stockAlerts($branchId, 8);
                $data['topMedicines'] = $report->topDispensedMedicinesToday($branchId, 6);
                break;

            case 'cashier':
                $data['dashboardType'] = 'cashier';
                $data['dashboardTitle'] = 'Dashboard Kasir';
                $data['dashboardDescription'] = 'Memusatkan perhatian pada tagihan siap dibayar, pemasukan harian, dan riwayat pembayaran.';
                $data['stats'] = $report->cashierStats($branchId);
                $data['readyInvoices'] = $report->readyInvoices($branchId, 8);
                $data['recentPayments'] = $report->recentPayments($branchId, 8);
                $data['paymentMethods'] = $report->paymentMethodSummaryToday($branchId);
                break;

            case 'inventory':
                $data['dashboardType'] = 'inventory';
                $data['dashboardTitle'] = 'Dashboard Inventory';
                $data['dashboardDescription'] = 'Menampilkan kondisi persediaan, mutasi stok hari ini, dan item yang perlu segera diisi ulang.';
                $data['stats'] = $report->inventoryStats($branchId);
                $data['lowStockItems'] = $report->stockAlerts($branchId, 8);
                $data['recentStockMovements'] = $report->recentStockMovements($branchId, 10);
                break;

            case 'super_admin':
            case 'owner':
            case 'branch_admin':
            default:
                $data['dashboardType'] = 'admin';
                $data['dashboardTitle'] = 'Dashboard Operasional Cabang';
                $data['dashboardDescription'] = 'Ringkasan menyeluruh layanan klinik, keuangan, farmasi, dan stok pada cabang aktif.';
                $data['stats'] = $report->dashboard($branchId);
                $data['finance'] = $report->dailyFinance($branchId);
                $data['expenses'] = $report->dailyExpenses($branchId);
                $data['alerts'] = $report->stockAlerts($branchId);
                $data['visits_by_clinic'] = $report->visitSummaryToday($branchId);
                $data['branch_stats'] = is_global_user() ? $branch->withStats() : [];
                break;
        }

        $this->render('dashboard/index', $data);
    }
}

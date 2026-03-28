<?php
class Inventory extends Controller {
    public function index() {
        $this->requireRoles(['pharmacist', 'inventory', 'branch_admin', 'super_admin']);
        $this->branchRequired();

        $med = new MedicineModel();
        $items = $med->allByBranch(current_branch_id());
        foreach ($items as &$item) {
            $item['stock'] = $med->stockOnHand($item['id'], current_branch_id());
        }

        $this->render('inventory/index', ['items' => $items]);
    }

    public function medicineStore() {
        $this->requireRoles(['pharmacist', 'inventory', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $medicineId = $model->insert('medicines', [
            'branch_id' => current_branch_id(),
            'name' => $this->input('name'),
            'unit' => $this->input('unit'),
            'buy_price' => $this->input('buy_price', 0),
            'sell_price' => $this->input('sell_price', 0),
            'min_stock' => $this->input('min_stock', 0),
            'is_active' => 1,
            'created_at' => now(),
        ]);

        log_activity('medicine_create', 'Menambah master obat', 'medicines', $medicineId);
        $this->respondSuccess('Master obat berhasil ditambahkan.');
        if (!$this->wantsJson()) redirect_to('inventory');
    }

    public function stockIn() {
        $this->requireRoles(['pharmacist', 'inventory', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $movementId = $model->insert('stock_movements', [
            'branch_id' => current_branch_id(),
            'medicine_id' => $this->input('medicine_id'),
            'movement_type' => 'purchase',
            'reference_type' => 'manual',
            'reference_id' => null,
            'qty' => $this->input('qty'),
            'unit_cost' => $this->input('unit_cost', 0),
            'notes' => $this->input('notes'),
            'created_at' => now(),
        ]);

        log_activity('stock_in', 'Mencatat stok masuk', 'stock_movements', $movementId);
        $this->respondSuccess('Stok masuk berhasil dicatat.');
        if (!$this->wantsJson()) redirect_to('inventory');
    }

    public function stockCard($medicineId) {
        $this->requireRoles(['pharmacist', 'inventory', 'branch_admin', 'super_admin']);
        $this->branchRequired();

        $medicineModel = new MedicineModel();
        $medicine = $medicineModel->find((int)$medicineId);
        if (!$medicine || $medicine['branch_id'] != current_branch_id()) {
            set_flash('error', 'Obat tidak ditemukan.');
            redirect_to('inventory');
        }

        $this->render('inventory/stock_card', [
            'medicine' => $medicine,
            'cards' => $medicineModel->stockCards(current_branch_id(), (int)$medicineId),
            'stock' => $medicineModel->stockOnHand((int)$medicineId, current_branch_id()),
        ]);
    }
}

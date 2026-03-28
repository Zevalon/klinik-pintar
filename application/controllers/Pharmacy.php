<?php
class Pharmacy extends Controller {
    public function index() {
        $this->requireRoles(['pharmacist', 'branch_admin', 'super_admin']);
        $this->branchRequired();

        $model = new Model();
        $medicineModel = new MedicineModel();
        $rows = $model->all("
            SELECT p.*, v.visit_date, v.complaint, v.status AS visit_status,
                   pa.name AS patient_name, pa.medical_record_no,
                   c.name AS clinic_name, d.diagnosis_name
            FROM prescriptions p
            JOIN visits v ON v.id = p.visit_id
            JOIN patients pa ON pa.id = v.patient_id
            LEFT JOIN clinics c ON c.id = v.clinic_id
            LEFT JOIN diagnoses d ON d.visit_id = v.id
            WHERE p.branch_id = ? AND v.status = 'completed'
            ORDER BY CASE WHEN p.status = 'dispensed' THEN 2 ELSE 1 END,
                     COALESCE(p.updated_at, p.created_at, v.updated_at, v.visit_date) DESC,
                     p.id DESC
        ", [current_branch_id()]);

        $pending = [];
        $dispensed = [];
        foreach ($rows as $row) {
            $items = $model->all("
                SELECT pi.*, m.name AS medicine_name
                FROM prescription_items pi
                JOIN medicines m ON m.id = pi.medicine_id
                WHERE pi.prescription_id = ?
                ORDER BY pi.id ASC
            ", [(int)$row['id']]);

            $row['items'] = $items;
            $row['item_count'] = count($items);
            $row['summary_text'] = !empty($items) ? implode(', ', array_map(function($item){
                return $item['medicine_name'] . ' x' . rtrim(rtrim(number_format((float)$item['qty'], 2, '.', ''), '0'), '.');
            }, array_slice($items, 0, 3))) : 'Tidak ada item obat.';

            if ($row['status'] === 'dispensed') {
                $dispensed[] = $row;
            } else {
                $pending[] = $row;
            }
        }

        $medicines = $medicineModel->allByBranch(current_branch_id());
        foreach ($medicines as &$medicine) {
            $medicine['stock'] = $medicineModel->stockOnHand((int)$medicine['id'], current_branch_id());
        }
        unset($medicine);

        $this->render('pharmacy/index', [
            'pendingPrescriptions' => $pending,
            'dispensedPrescriptions' => $dispensed,
            'medicines' => $medicines,
        ]);
    }

    public function dispense($id) {
        $this->requireRoles(['pharmacist', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $medicineModel = new MedicineModel();
        $pres = $model->one("SELECT * FROM prescriptions WHERE id=?", [(int)$id]);

        if (!$pres || (int)$pres['branch_id'] !== (int)current_branch_id()) {
            $this->respondError('Resep tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('pharmacy');
            return;
        }

        if ($pres['status'] === 'dispensed') {
            $this->respondError('Resep ini sudah pernah divalidasi dan diserahkan.');
            if (!$this->wantsJson()) redirect_to('pharmacy');
            return;
        }

        try {
            $model->begin();

            $submittedItems = [];
            $stockNeed = [];
            $medicineIds = $_POST['medicine_id'] ?? [];
            $qtys = $_POST['qty'] ?? [];
            $dosages = $_POST['dosage'] ?? [];
            $prices = $_POST['unit_price'] ?? [];

            foreach ($medicineIds as $idx => $medicineId) {
                $medicineId = (int)$medicineId;
                if ($medicineId <= 0) {
                    continue;
                }

                $medicine = $medicineModel->find($medicineId);
                if (!$medicine || (int)$medicine['branch_id'] !== (int)$pres['branch_id']) {
                    throw new Exception('Salah satu obat yang dipilih tidak valid untuk cabang aktif.');
                }

                $qty = (float)($qtys[$idx] ?? 0);
                if ($qty <= 0) {
                    throw new Exception('Qty obat harus lebih besar dari nol.');
                }

                $rawPrice = (string)($prices[$idx] ?? $medicine['sell_price']);
                $rawPrice = str_replace('.', '', $rawPrice);
                $rawPrice = str_replace(',', '.', $rawPrice);
                $unitPrice = (float)preg_replace('/[^0-9\.-]/', '', $rawPrice);
                if ($unitPrice < 0) {
                    $unitPrice = 0;
                }

                $submittedItems[] = [
                    'medicine_id' => $medicineId,
                    'qty' => $qty,
                    'dosage' => trim((string)($dosages[$idx] ?? '')),
                    'unit_price' => $unitPrice,
                    'medicine_name' => $medicine['name'],
                ];
                $stockNeed[$medicineId] = ($stockNeed[$medicineId] ?? 0) + $qty;
            }

            foreach ($stockNeed as $medicineId => $qtyNeed) {
                $stock = $medicineModel->stockOnHand((int)$medicineId, (int)$pres['branch_id']);
                if ($stock < $qtyNeed) {
                    $medicine = $medicineModel->find((int)$medicineId);
                    throw new Exception('Stok obat ' . ($medicine['name'] ?? ('#' . $medicineId)) . ' tidak cukup. Tersedia ' . rtrim(rtrim(number_format((float)$stock, 2, '.', ''), '0'), '.') . '.');
                }
            }

            $model->exec("DELETE FROM prescription_items WHERE prescription_id=?", [(int)$id]);
            foreach ($submittedItems as $item) {
                $model->insert('prescription_items', [
                    'prescription_id' => (int)$id,
                    'medicine_id' => $item['medicine_id'],
                    'qty' => $item['qty'],
                    'dosage' => $item['dosage'],
                    'unit_price' => $item['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($submittedItems as $item) {
                $model->insert('stock_movements', [
                    'branch_id' => $pres['branch_id'],
                    'medicine_id' => $item['medicine_id'],
                    'movement_type' => 'prescription_out',
                    'reference_type' => 'prescription',
                    'reference_id' => (int)$id,
                    'qty' => $item['qty'],
                    'unit_cost' => $item['unit_price'],
                    'notes' => 'Obat diserahkan ke pasien dari resep #' . (int)$id,
                    'created_at' => now(),
                ]);
            }

            $model->updateById('prescriptions', (int)$id, [
                'status' => 'dispensed',
                'dispensed_at' => now(),
                'updated_at' => now(),
            ]);

            $model->commit();
            log_activity('pharmacy_dispense', 'Validasi dan serahkan obat resep ke pasien', 'prescriptions', (int)$id);
            $this->respondSuccess('Resep berhasil divalidasi farmasi. Data obat sudah diperbarui dan pasien sekarang dapat diproses di kasir.');
        } catch (Throwable $e) {
            $model->rollBack();
            $this->respondError('Gagal memvalidasi resep: ' . $e->getMessage());
        }

        if (!$this->wantsJson()) redirect_to('pharmacy');
    }
}

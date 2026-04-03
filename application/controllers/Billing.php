<?php
class Billing extends Controller {
    public function index() {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();

        $billing = new BillingModel();
        $visitModel = new VisitModel();
        $records = $billing->cashierQueue(current_branch_id());
        $readyRecords = [];
        $paidRecords = [];

        foreach ($records as &$record) {
            $record['services'] = $visitModel->services((int)$record['visit_id']);
            $record['medicines'] = $visitModel->prescriptionItems((int)$record['visit_id']);
            $record['vitals'] = $visitModel->vitals((int)$record['visit_id']);

            $estimatedSubtotal = 0;
            foreach ($record['services'] as $service) {
                $estimatedSubtotal += (float)$service['subtotal'];
            }
            foreach ($record['medicines'] as $medicine) {
                $estimatedSubtotal += (float)$medicine['qty'] * (float)$medicine['unit_price'];
            }

            $record['invoice_status'] = $record['invoice_status'] ?: 'ready';
            $record['estimated_subtotal'] = $estimatedSubtotal;
            $record['subtotal'] = $record['invoice_id'] ? (float)$record['subtotal'] : $estimatedSubtotal;
            $record['discount'] = $record['invoice_id'] ? (float)$record['discount'] : 0;
            $record['grand_total'] = $record['invoice_id'] ? (float)$record['grand_total'] : max(0, $estimatedSubtotal);
            $record['payments'] = $record['invoice_id'] ? $billing->payments((int)$record['invoice_id']) : [];
            $record['last_payment'] = !empty($record['payments']) ? end($record['payments']) : null;
            $record['paid_total'] = $record['invoice_id'] ? $billing->totalPaid((int)$record['invoice_id']) : 0;
            $record['remaining_total'] = max(0, (float)$record['grand_total'] - (float)$record['paid_total']);
            $record['change_amount'] = $record['last_payment'] ? max(0, (float)$record['last_payment']['amount'] - (float)$record['remaining_total']) : 0;
            $record['billing_status'] = $record['invoice_status'] === 'paid' ? 'paid' : 'ready';

            if ($record['billing_status'] === 'paid') {
                $paidRecords[] = $record;
            } else {
                $readyRecords[] = $record;
            }
        }
        unset($record);

        $this->render('billing/index', [
            'readyRecords' => $readyRecords,
            'paidRecords' => $paidRecords,
        ]);
    }

    public function generate($visitId) {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $billing = new BillingModel();
        $visitModel = new VisitModel();
        $visit = $visitModel->find((int)$visitId);

        if (!$visit || $visit['branch_id'] != current_branch_id()) {
            $this->respondError('Kunjungan tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        if ($visit['status'] !== 'completed') {
            $this->respondError('Invoice hanya bisa dibuat setelah pemeriksaan selesai disimpan.');
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        try {
            $model->begin();
            $invoice = $this->prepareInvoice($model, $billing, $visitModel, $visit, parse_money_input($this->input('discount', 0)));
            $model->commit();
            log_activity('invoice_generate', 'Menyiapkan invoice dari hasil pemeriksaan pasien', 'invoices', $invoice['id']);
            $this->respondSuccess('Invoice kasir berhasil disiapkan dengan total tagihan ' . currency($invoice['grand_total']) . '.');
        } catch (Throwable $e) {
            $model->rollBack();
            $this->respondError('Gagal membuat invoice: ' . $e->getMessage());
        }

        if (!$this->wantsJson()) redirect_to('billing');
    }

    public function settle($visitId) {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $billing = new BillingModel();
        $visitModel = new VisitModel();
        $visit = $visitModel->find((int)$visitId);

        if (!$visit || $visit['branch_id'] != current_branch_id()) {
            $this->respondError('Kunjungan tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        if ($visit['status'] !== 'completed') {
            $this->respondError('Pembayaran hanya bisa diproses setelah pemeriksaan selesai disimpan.');
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        $discount = parse_money_input($this->input('discount', 0));
        $amountReceived = parse_money_input($this->input('amount_received', 0));

        try {
            $model->begin();
            $invoice = $this->prepareInvoice($model, $billing, $visitModel, $visit, $discount);
            $existingPaid = $billing->totalPaid((int)$invoice['id']);
            $remainingTotal = max(0, (float)$invoice['grand_total'] - (float)$existingPaid);

            if ($remainingTotal > 0 && $amountReceived <= 0) {
                throw new Exception('Masukkan nominal uang tunai yang diterima kasir.');
            }

            if ($amountReceived < $remainingTotal) {
                throw new Exception('Nominal tunai kurang ' . currency($remainingTotal - $amountReceived) . '.');
            }

            $paymentId = null;
            if ($amountReceived > 0) {
                $paymentId = $model->insert('payments', [
                    'branch_id' => $invoice['branch_id'],
                    'invoice_id' => (int)$invoice['id'],
                    'payment_method' => 'cash',
                    'amount' => $amountReceived,
                    'paid_at' => now(),
                    'created_at' => now(),
                ]);
            }

            $totalPaidAfter = $existingPaid + $amountReceived;
            $status = $totalPaidAfter >= (float)$invoice['grand_total'] ? 'paid' : 'partial';

            $model->updateById('invoices', (int)$invoice['id'], [
                'status' => $status,
                'paid_at' => $status === 'paid' ? now() : null,
                'updated_at' => now(),
            ]);

            $model->commit();

            $change = max(0, $amountReceived - $remainingTotal);
            if ($paymentId) {
                log_activity('payment_create', 'Mencatat pembayaran tunai invoice', 'payments', (int)$paymentId);
            }
            if ($amountReceived <= 0 && $remainingTotal <= 0) {
                $message = 'Pembayaran berhasil diproses dan status invoice menjadi lunas karena total tagihan bernilai nol.';
            } else {
                $message = 'Pembayaran tunai berhasil dicatat dan status invoice menjadi lunas.';
                $message .= $change > 0 ? ' Kembalian ' . currency($change) . '.' : ' Uang diterima pas tanpa kembalian.';
            }
            $this->respondSuccess($message);
        } catch (Throwable $e) {
            $model->rollBack();
            $this->respondError('Gagal memproses pembayaran: ' . $e->getMessage());
        }

        if (!$this->wantsJson()) redirect_to('billing');
    }

    public function pay($id) {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $billing = new BillingModel();
        $invoice = $model->one("SELECT * FROM invoices WHERE id=?", [(int)$id]);
        if (!$invoice || $invoice['branch_id'] != current_branch_id()) {
            $this->respondError('Invoice tidak ditemukan.', 404);
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        if ($invoice['status'] === 'paid') {
            $this->respondError('Invoice ini sudah lunas.');
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        $amountReceived = parse_money_input($this->input('amount_received', $this->input('amount', $invoice['grand_total'])));
        $existingPaid = $billing->totalPaid((int)$invoice['id']);
        $remainingTotal = max(0, (float)$invoice['grand_total'] - $existingPaid);

        if ($remainingTotal > 0 && $amountReceived <= 0) {
            $this->respondError('Masukkan nominal tunai yang diterima kasir.');
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        if ($amountReceived < $remainingTotal) {
            $this->respondError('Nominal tunai kurang ' . currency($remainingTotal - $amountReceived) . '.');
            if (!$this->wantsJson()) redirect_to('billing');
            return;
        }

        $paymentId = null;
        if ($amountReceived > 0) {
            $paymentId = $model->insert('payments', [
                'branch_id' => $invoice['branch_id'],
                'invoice_id' => (int)$id,
                'payment_method' => 'cash',
                'amount' => $amountReceived,
                'paid_at' => now(),
                'created_at' => now(),
            ]);
        }

        $model->updateById('invoices', (int)$id, [
            'status' => 'paid',
            'paid_at' => now(),
            'updated_at' => now(),
        ]);

        $change = max(0, $amountReceived - $remainingTotal);
        if ($paymentId) {
            log_activity('payment_create', 'Mencatat pembayaran tunai invoice', 'payments', (int)$paymentId);
        }
        if ($amountReceived <= 0 && $remainingTotal <= 0) {
            $message = 'Pembayaran berhasil diproses dan invoice ditandai lunas karena total tagihan bernilai nol.';
        } else {
            $message = 'Pembayaran tunai berhasil dicatat.';
            $message .= $change > 0 ? ' Kembalian ' . currency($change) . '.' : ' Uang diterima pas tanpa kembalian.';
        }
        $this->respondSuccess($message);
        if (!$this->wantsJson()) redirect_to('billing');
    }

    public function pdf($id) {
        $this->requireRoles(['cashier', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();

        $billing = new BillingModel();
        $invoice = $billing->findDetailed((int)$id, current_branch_id());
        if (!$invoice) {
            set_flash('error', 'Invoice tidak ditemukan.');
            redirect_to('billing');
        }

        if ($invoice['status'] !== 'paid') {
            set_flash('error', 'Invoice PDF hanya dapat diunduh setelah pembayaran lunas.');
            redirect_to('billing');
        }

        $items = $billing->invoiceItems((int)$id);
        $payments = $billing->payments((int)$id);
        SimplePdf::downloadInvoice($invoice, $items, $payments, 'invoice-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $invoice['invoice_no']) . '.pdf');
    }

    private function prepareInvoice($model, $billing, $visitModel, $visit, $discount = 0) {
        $existingInvoice = $billing->invoiceByVisit((int)$visit['id']);
        if ($existingInvoice && $existingInvoice['status'] === 'paid') {
            throw new Exception('Invoice yang sudah lunas tidak dapat diubah lagi.');
        }

        if (!$existingInvoice) {
            $invoiceId = $model->insert('invoices', [
                'branch_id' => $visit['branch_id'],
                'visit_id' => (int)$visit['id'],
                'invoice_no' => 'INV-' . date('Ymd') . '-' . str_pad((string)$visit['id'], 5, '0', STR_PAD_LEFT),
                'subtotal' => 0,
                'discount' => 0,
                'grand_total' => 0,
                'status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $invoiceId = (int)$existingInvoice['id'];
            $model->exec("DELETE FROM invoice_items WHERE invoice_id=?", [$invoiceId]);
        }

        $subtotal = 0;
        $itemCount = 0;

        $serviceRows = $visitModel->services((int)$visit['id']);
        foreach ($serviceRows as $serviceRow) {
            $lineTotal = (float)$serviceRow['subtotal'];
            $subtotal += $lineTotal;
            $itemCount++;
            $model->insert('invoice_items', [
                'invoice_id' => $invoiceId,
                'item_type' => $serviceRow['category'],
                'description' => $serviceRow['service_name'],
                'qty' => $serviceRow['qty'],
                'unit_price' => $serviceRow['unit_price'],
                'subtotal' => $lineTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $medicineRows = $visitModel->prescriptionItems((int)$visit['id']);
        foreach ($medicineRows as $item) {
            $lineTotal = (float)$item['qty'] * (float)$item['unit_price'];
            $subtotal += $lineTotal;
            $itemCount++;
            $model->insert('invoice_items', [
                'invoice_id' => $invoiceId,
                'item_type' => 'medicine',
                'description' => $item['medicine_name'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $lineTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($itemCount === 0) {
            throw new Exception('Belum ada layanan tindakan atau obat yang bisa ditagihkan dari halaman pemeriksaan.');
        }

        $discount = max(0, (float)$discount);
        $grandTotal = max(0, $subtotal - $discount);
        $totalPaid = $billing->totalPaid($invoiceId);
        $status = $totalPaid >= $grandTotal && $totalPaid > 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
        $paidAt = $status === 'paid' ? ($existingInvoice['paid_at'] ?? now()) : null;

        $model->updateById('invoices', $invoiceId, [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'grand_total' => $grandTotal,
            'status' => $status,
            'paid_at' => $paidAt,
            'updated_at' => now(),
        ]);

        return $model->one("SELECT * FROM invoices WHERE id=? LIMIT 1", [$invoiceId]);
    }
}


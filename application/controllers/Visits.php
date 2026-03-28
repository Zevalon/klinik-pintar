<?php
class Visits extends Controller {
    public function index() {
        $this->requireRoles(['doctor', 'nurse', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();

        $visit = new VisitModel();
        $this->render('visits/index', ['visits' => $visit->allOpen(current_branch_id())]);
    }

    public function show($id) {
        $this->requireRoles(['doctor', 'nurse', 'branch_admin', 'super_admin', 'owner']);
        $this->branchRequired();

        $visitModel = new VisitModel();
        $medicine = new MedicineModel();
        $service = new ServiceModel();

        $visit = $visitModel->find((int)$id);
        if (!$visit || $visit['branch_id'] != current_branch_id()) {
            set_flash('error', 'Data kunjungan tidak ditemukan.');
            redirect_to('visits');
        }

        $this->render('visits/show', [
            'visit' => $visit,
            'diagnosis' => $visitModel->diagnosis((int)$id),
            'vitals' => $visitModel->vitals((int)$id),
            'items' => $visitModel->prescriptionItems((int)$id),
            'visit_services' => $visitModel->services((int)$id),
            'medicines' => $medicine->allByBranch(current_branch_id()),
            'consultations' => $service->consultationServices(current_branch_id()),
            'procedures' => $service->procedureServices(current_branch_id()),
        ]);
    }

    public function saveClinical($id) {
        $this->requireRoles(['doctor', 'nurse', 'branch_admin', 'super_admin']);
        $this->branchRequired();
        verify_csrf();

        $model = new Model();
        $visitModel = new VisitModel();
        $visit = $visitModel->find((int)$id);
        if (!$visit || $visit['branch_id'] != current_branch_id()) {
            set_flash('error', 'Data kunjungan tidak ditemukan.');
            redirect_to('visits');
        }

        try {
            $model->begin();

            $existingVitals = $visitModel->vitals((int)$id);
            $vitalData = [
                'visit_id' => (int)$id,
                'blood_pressure' => $this->input('blood_pressure'),
                'temperature' => $this->input('temperature'),
                'weight' => $this->input('weight'),
                'height' => $this->input('height'),
                'pulse' => $this->input('pulse'),
                'updated_at' => now(),
            ];
            if ($existingVitals) {
                $model->updateById('vital_signs', $existingVitals['id'], $vitalData);
            } else {
                $vitalData['created_at'] = now();
                $model->insert('vital_signs', $vitalData);
            }

            $existingDiagnosis = $visitModel->diagnosis((int)$id);
            $diagData = [
                'visit_id' => (int)$id,
                'doctor_user_id' => current_user()['id'],
                'icd_code' => $this->input('icd_code'),
                'diagnosis_name' => $this->input('diagnosis_name'),
                'soap_notes' => $this->input('soap_notes'),
                'treatment_notes' => $this->input('treatment_notes'),
                'updated_at' => now(),
            ];
            if ($existingDiagnosis) {
                $model->updateById('diagnoses', $existingDiagnosis['id'], $diagData);
            } else {
                $diagData['created_at'] = now();
                $model->insert('diagnoses', $diagData);
            }

            $model->exec("DELETE FROM visit_services WHERE visit_id=?", [(int)$id]);
            $serviceIds = $_POST['service_id'] ?? [];
            $serviceModel = new ServiceModel();
            foreach ($serviceIds as $serviceId) {
                if (!$serviceId) {
                    continue;
                }
                $service = $serviceModel->find((int)$serviceId);
                if (!$service) {
                    continue;
                }
                $model->insert('visit_services', [
                    'visit_id' => (int)$id,
                    'service_id' => $service['id'],
                    'qty' => 1,
                    'unit_price' => $service['price'],
                    'subtotal' => $service['price'],
                    'created_at' => now(),
                ]);
            }

            $pres = $visitModel->prescription((int)$id);
            $prescriptionId = $pres['id'] ?? null;
            $medicineModel = new MedicineModel();
            $preparedItems = [];

            if (!empty($_POST['medicine_id']) && is_array($_POST['medicine_id'])) {
                foreach ($_POST['medicine_id'] as $idx => $medicineId) {
                    $medicineId = (int)$medicineId;
                    if ($medicineId <= 0) {
                        continue;
                    }

                    $medicine = $medicineModel->find($medicineId);
                    if (!$medicine || (int)$medicine['branch_id'] !== (int)$visit['branch_id']) {
                        continue;
                    }

                    $qty = (float)($_POST['qty'][$idx] ?? 0);
                    if ($qty <= 0) {
                        $qty = 1;
                    }

                    $unitPrice = $_POST['unit_price'][$idx] ?? ($medicine['sell_price'] ?? 0);
                    $unitPrice = (float)str_replace(',', '.', preg_replace('/[^0-9,.-]/', '', (string)$unitPrice));
                    if ($unitPrice < 0) {
                        $unitPrice = 0;
                    }

                    $preparedItems[] = [
                        'medicine_id' => $medicineId,
                        'qty' => $qty,
                        'dosage' => trim((string)($_POST['dosage'][$idx] ?? '')),
                        'unit_price' => $unitPrice,
                    ];
                }
            }

            if (!empty($preparedItems)) {
                if (!$prescriptionId) {
                    $prescriptionId = $model->insert('prescriptions', [
                        'branch_id' => $visit['branch_id'],
                        'visit_id' => (int)$id,
                        'status' => 'draft',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $model->updateById('prescriptions', $prescriptionId, [
                        'status' => 'draft',
                        'updated_at' => now(),
                    ]);
                }

                $model->exec("DELETE FROM prescription_items WHERE prescription_id=?", [$prescriptionId]);
                foreach ($preparedItems as $item) {
                    $model->insert('prescription_items', [
                        'prescription_id' => $prescriptionId,
                        'medicine_id' => $item['medicine_id'],
                        'qty' => $item['qty'],
                        'dosage' => $item['dosage'],
                        'unit_price' => $item['unit_price'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($prescriptionId) {
                $model->exec("DELETE FROM prescription_items WHERE prescription_id=?", [$prescriptionId]);
                $model->deleteWhere('prescriptions', 'id=?', [$prescriptionId]);
            }

            $model->updateById('visits', (int)$id, [
                'doctor_user_id' => current_user()['id'],
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            $queue = $model->one("SELECT * FROM queues WHERE id=?", [$visit['queue_id']]);
            if ($queue) {
                $model->updateById('queues', $queue['id'], ['status' => 'done', 'updated_at' => now()]);
            }

            $model->commit();
            if ($queue) {
                (new QueueModel())->syncFlow($visit['branch_id'], $queue['clinic_id']);
            }
            log_activity('visit_completed', 'Menyimpan pemeriksaan pasien dan meneruskan resep ke farmasi sebelum kasir.', 'visits', (int)$id);
            $this->respondSuccess('Data pemeriksaan berhasil disimpan. Jika ada resep, data pasien sekarang masuk ke halaman farmasi untuk validasi obat. Setelah obat divalidasi/diserahkan, data akan muncul di kasir.');
        } catch (Throwable $e) {
            $model->rollBack();
            $this->respondError('Gagal menyimpan pemeriksaan: ' . $e->getMessage());
        }

        if (!$this->wantsJson()) redirect_to('visits/show/' . (int)$id);
    }
}

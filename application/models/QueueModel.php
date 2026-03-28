<?php
class QueueModel extends Model {
    public function allToday($branchId) {
        return $this->all("
            SELECT q.*, p.name patient_name, p.nik patient_nik, c.name clinic_name, c.queue_state clinic_state, v.id AS visit_id, v.status AS visit_status
            FROM queues q
            JOIN patients p ON p.id=q.patient_id
            JOIN clinics c ON c.id=q.clinic_id
            LEFT JOIN visits v ON v.queue_id=q.id
            WHERE q.branch_id=? AND DATE(q.queue_date)=CURDATE() AND q.status IN ('waiting','called','examined','pending')
            ORDER BY c.name ASC,
                CASE q.status
                    WHEN 'examined' THEN 1
                    WHEN 'called' THEN 2
                    WHEN 'waiting' THEN 3
                    WHEN 'pending' THEN 4
                    ELSE 5
                END,
                q.id ASC
        ", [$branchId]);
    }

    public function createNumber($branchId, $clinicId) {
        $row = $this->one("SELECT COUNT(*) total FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE()", [$branchId, $clinicId]);
        return sprintf('%03d', ((int)$row['total']) + 1);
    }

    public function find($id) {
        return $this->one("
            SELECT q.*, p.name patient_name, c.name clinic_name
            FROM queues q
            JOIN patients p ON p.id=q.patient_id
            JOIN clinics c ON c.id=q.clinic_id
            WHERE q.id=?
        ", [$id]);
    }

    public function activeQueue($branchId, $clinicId) {
        return $this->one("SELECT * FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE() AND status IN ('called','examined') ORDER BY FIELD(status,'examined','called'), id ASC LIMIT 1", [$branchId, $clinicId]);
    }

    public function nextWaiting($branchId, $clinicId) {
        return $this->one("SELECT * FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE() AND status='waiting' ORDER BY id ASC LIMIT 1", [$branchId, $clinicId]);
    }

    public function setStatus($queueId, $status) {
        return $this->exec("UPDATE queues SET status=?, updated_at=? WHERE id=?", [$status, now(), (int)$queueId]);
    }

    public function setVisitStatusByQueue($queueId, $status) {
        return $this->exec("UPDATE visits SET status=?, updated_at=? WHERE queue_id=?", [$status, now(), (int)$queueId]);
    }

    public function clinicState($branchId, $clinicId) {
        $row = $this->one("SELECT status FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE() AND status IN ('called','examined') ORDER BY FIELD(status,'examined','called'), id ASC LIMIT 1", [$branchId, $clinicId]);
        if (!$row) {
            return 'idle';
        }
        return $row['status'] === 'examined' ? 'serving' : 'calling';
    }


    public function publicMonitorData($branchId) {
        $clinics = $this->all("
            SELECT c.id, c.name, c.queue_state,
                   q.id AS queue_id, q.queue_number, q.status AS queue_status, q.updated_at AS queue_updated_at, q.queue_date,
                   p.name AS patient_name
            FROM clinics c
            LEFT JOIN queues q ON q.id = (
                SELECT q2.id FROM queues q2
                WHERE q2.branch_id = c.branch_id
                  AND q2.clinic_id = c.id
                  AND DATE(q2.queue_date) = CURDATE()
                  AND q2.status IN ('called','examined')
                ORDER BY FIELD(q2.status,'examined','called'), q2.id ASC
                LIMIT 1
            )
            LEFT JOIN patients p ON p.id = q.patient_id
            WHERE c.branch_id = ? AND c.is_active = 1
            ORDER BY c.name ASC
        ", [$branchId]);

        foreach ($clinics as &$clinic) {
            $next = $this->one("SELECT queue_number FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE() AND status='waiting' ORDER BY id ASC LIMIT 1", [$branchId, (int)$clinic['id']]);
            $waiting = $this->one("SELECT COUNT(*) AS total FROM queues WHERE branch_id=? AND clinic_id=? AND DATE(queue_date)=CURDATE() AND status='waiting'", [$branchId, (int)$clinic['id']]);
            $clinic['next_queue_number'] = $next['queue_number'] ?? null;
            $clinic['waiting_total'] = (int)($waiting['total'] ?? 0);
            $clinic['announcement_key'] = $clinic['queue_id'] ? ('queue-' . $clinic['queue_id'] . '-' . ($clinic['queue_updated_at'] ?: $clinic['queue_date'])) : null;
        }
        unset($clinic);

        return $clinics;
    }

    public function publicBranchOptions() {
        return $this->all("SELECT id, name FROM branches WHERE is_active=1 ORDER BY name ASC");
    }

    public function syncFlow($branchId, $clinicId) {
        $clinic = new ClinicModel();
        $active = $this->activeQueue($branchId, $clinicId);
        if ($active) {
            $clinic->setQueueState($clinicId, $active['status'] === 'examined' ? 'serving' : 'calling');
            return $active;
        }

        $next = $this->nextWaiting($branchId, $clinicId);
        if ($next) {
            $this->setStatus($next['id'], 'called');
            $this->setVisitStatusByQueue($next['id'], 'called');
            $clinic->setQueueState($clinicId, 'calling');
            return $this->find($next['id']);
        }

        $clinic->setQueueState($clinicId, 'idle');
        return null;
    }
}

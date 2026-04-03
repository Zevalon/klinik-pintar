<?php
class UserModel extends Model {
    public function ensureProfileSchema() {
        $columns = [
            'phone' => "ALTER TABLE users ADD COLUMN phone VARCHAR(30) DEFAULT NULL AFTER email",
            'gender' => "ALTER TABLE users ADD COLUMN gender VARCHAR(20) DEFAULT NULL AFTER phone",
            'address' => "ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER gender",
            'bio' => "ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL AFTER address",
            'photo_path' => "ALTER TABLE users ADD COLUMN photo_path VARCHAR(255) DEFAULT NULL AFTER bio",
        ];

        foreach ($columns as $column => $sql) {
            if (!$this->columnExists($column)) {
                $this->exec($sql);
            }
        }
    }

    protected function columnExists($column) {
        $row = $this->one("SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = ?", [$column]);
        return !empty($row['total']);
    }

    public function findByUsername($username) {
        $this->ensureProfileSchema();
        return $this->one("
            SELECT u.*, r.code AS role_code, r.name AS role_name, b.name AS branch_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.username = ? AND u.is_active = 1
        ", [$username]);
    }

    public function findByIdWithRole($id) {
        $this->ensureProfileSchema();
        return $this->one("
            SELECT u.*, r.code AS role_code, r.name AS role_name, b.name AS branch_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.id = ?
        ", [(int)$id]);
    }

    public function allWithRoles() {
        $this->ensureProfileSchema();
        return $this->all("
            SELECT u.*, r.name AS role_name, r.code AS role_code, b.name AS branch_name
            FROM users u
            JOIN roles r ON r.id=u.role_id
            LEFT JOIN branches b ON b.id=u.branch_id
            ORDER BY u.id DESC
        ");
    }

    public function roleOptions() {
        return $this->all("SELECT * FROM roles ORDER BY id");
    }

    public function branchOptions() {
        return $this->all("SELECT * FROM branches WHERE is_active=1 ORDER BY name");
    }

    public function isUsernameTaken($username, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE username = ?";
        $params = [$username];
        if ($excludeId) {
            $sql .= " AND id <> ?";
            $params[] = (int)$excludeId;
        }
        return (bool)$this->one($sql, $params);
    }

    public function isEmailTaken($email, $excludeId = null) {
        if ($email === null || $email === '') {
            return false;
        }
        $sql = "SELECT id FROM users WHERE email = ?";
        $params = [$email];
        if ($excludeId) {
            $sql .= " AND id <> ?";
            $params[] = (int)$excludeId;
        }
        return (bool)$this->one($sql, $params);
    }

    public function updateProfile($id, array $data) {
        return $this->updateById('users', (int)$id, $data);
    }

    public function updatePassword($id, $passwordHash) {
        return $this->updateById('users', (int)$id, [
            'password' => $passwordHash,
            'updated_at' => now(),
        ]);
    }
}

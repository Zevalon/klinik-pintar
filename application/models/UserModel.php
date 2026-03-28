<?php
class UserModel extends Model {
    public function findByUsername($username) {
        return $this->one("
            SELECT u.*, r.code AS role_code, r.name AS role_name, b.name AS branch_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.username = ? AND u.is_active = 1
        ", [$username]);
    }

    public function allWithRoles() {
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
}

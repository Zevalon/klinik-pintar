<?php
class Model {
    protected $db;

    public function __construct() {
        $this->db = DB::conn();
    }

    public function all($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($params));
        return $stmt->fetchAll();
    }

    public function one($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($params));
        return $stmt->fetch();
    }

    public function exec($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_values($params));
    }

    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array();
        foreach ($columns as $c) { $placeholders[] = ':' . $c; }
        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function updateById($table, $id, $data) {
        $sets = [];
        foreach ($data as $k => $v) {
            $sets[] = "$k = :$k";
        }
        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $sets) . ' WHERE id = :id';
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function deleteWhere($table, $where, $params = []) {
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE {$where}");
        return $stmt->execute(array_values($params));
    }

    public function begin() {
        $this->db->beginTransaction();
    }

    public function commit() {
        $this->db->commit();
    }

    public function rollBack() {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }
}

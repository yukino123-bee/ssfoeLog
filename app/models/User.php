<?php
/**
 * User Model
 */

class User {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function create($firstname, $lastname, $email, $password, $role = 'user') {
        $fullname = "$firstname $lastname";
        $stmt = $this->db->prepare("INSERT INTO users (firstname, lastname, fullname, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstname, $lastname, $fullname, $email, $password, $role);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        $types = "";

        foreach ($data as $key => $value) {
            if (in_array($key, ['firstname', 'lastname', 'fullname', 'email', 'password', 'role', 'phone', 'address', 'profile_image'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
                $types .= "s";
            }
        }

        if (empty($fields)) return false;

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $values[] = $id;
        $types .= "i";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAdmins() {
        return $this->db->query("SELECT * FROM users WHERE role = 'admin'");
    }

    public function getClients() {
        return $this->db->query("SELECT * FROM users WHERE role = 'user'");
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

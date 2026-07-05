<?php
/**
 * Announcement Model
 */

class Announcement {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function create($title, $content, $priority = 'Normal', $audience = 'All Beneficiaries') {
        $stmt = $this->db->prepare("INSERT INTO announcements (title, content, priority, audience) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $content, $priority, $audience);
        return $stmt->execute();
    }

    public function getAll($limit = 100) {
        $stmt = $this->db->prepare("SELECT * FROM announcements ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        $types = "";

        foreach ($data as $key => $value) {
            if (in_array($key, ['title', 'content', 'priority', 'audience'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
                $types .= "s";
            }
        }

        if (empty($fields)) return false;

        $sql = "UPDATE announcements SET " . implode(", ", $fields) . " WHERE id = ?";
        $values[] = $id;
        $types .= "i";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }
}

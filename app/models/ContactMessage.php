<?php
/**
 * ContactMessage Model
 */

class ContactMessage {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
        $this->ensureTablePresent();
    }

    public function ensureTablePresent() {
        $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        return $this->db->query($sql);
    }

    public function create($name, $email, $subject, $message) {
        $stmt = $this->db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            error_log("ContactMessage::create prepare failed: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if (!$stmt->execute()) {
            error_log("ContactMessage::create execute failed: " . $stmt->error);
            return false;
        }
        return true;
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getUnreadCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
        if ($result) {
            return $result->fetch_assoc()['count'];
        }
        return 0;
    }
}

<?php
/**
 * Inquiry Model - Handles contact form submissions
 */

class Inquiry {
    private $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", 
            $data['name'], 
            $data['email'], 
            $data['subject'], 
            $data['message']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM inquiries ORDER BY created_at DESC");
        $inquiries = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $inquiries[] = $row;
            }
        }
        return $inquiries;
    }

    public function getUnreadCount() {
        $result = $this->db->query("SELECT COUNT(*) as cnt FROM inquiries WHERE is_read = 0");
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['cnt'];
        }
        return 0;
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE inquiries SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function markAllAsRead() {
        $stmt = $this->db->prepare("UPDATE inquiries SET is_read = 1 WHERE is_read = 0");
        return $stmt->execute();
    }
}

<?php
/**
 * Report Model
 */

class Report {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function getSummary() {
        $result = $this->db->query("SELECT status, COUNT(*) as count FROM requests GROUP BY status");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDetailedReport($filters = []) {
        $query = "SELECT * FROM requests WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query .= " AND request_type = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND created_at >= ?";
            $params[] = $filters['date_from'] . " 00:00:00";
            $types .= "s";
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND created_at <= ?";
            $params[] = $filters['date_to'] . " 23:59:59";
            $types .= "s";
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

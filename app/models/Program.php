<?php
/**
 * Program Model
 * Handles program management including CRUD and statistics
 */

class Program {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    /**
     * Get all programs
     */
    public function getAll() {
        $sql = "SELECT * FROM programs ORDER BY name ASC";
        return $this->db->query($sql);
    }

    /**
     * Get program by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM programs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get program by name
     */
    public function getByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM programs WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get active programs only
     */
    public function getActive() {
        $sql = "SELECT * FROM programs WHERE status = 'active' ORDER BY name ASC";
        return $this->db->query($sql);
    }

    /**
     * Create new program
     */
    public function create($data) {
        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO programs (name, description, icon, category, status, required_documents, custom_fields, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $name = $data['name'];
            $description = $data['description'] ?? null;
            $icon = $data['icon'] ?? null;
            $category = $data['category'] ?? null;
            $required_docs = isset($data['required_documents']) ? json_encode($data['required_documents']) : json_encode([]);
            $custom_fields = isset($data['custom_fields']) ? json_encode($data['custom_fields']) : json_encode([]);
            $created_by = isset($data['created_by']) ? $data['created_by'] : null;
            $status = $data['status'] ?? 'active';

            $stmt->bind_param(
                "sssssssi",
                $name,
                $description,
                $icon,
                $category,
                $status,
                $required_docs,
                $custom_fields,
                $created_by
            );

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $this->db->commit();
            return $this->db->insert_id;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Update program
     */
    public function update($id, $data) {
        $this->db->begin_transaction();
        try {
            $fields = [];
            $values = [];
            $types = "";

            foreach (['name', 'description', 'icon', 'category', 'status'] as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                    $types .= "s";
                }
            }

            if (isset($data['required_documents'])) {
                $fields[] = "required_documents = ?";
                $values[] = json_encode($data['required_documents']);
                $types .= "s";
            }

            if (isset($data['custom_fields'])) {
                $fields[] = "custom_fields = ?";
                $values[] = json_encode($data['custom_fields']);
                $types .= "s";
            }

            if (empty($fields)) {
                return true;
            }

            $sql = "UPDATE programs SET " . implode(", ", $fields) . ", updated_at = NOW() WHERE id = ?";
            $values[] = $id;
            $types .= "i";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$values);

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Delete program
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM programs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Toggle program status
     */
    public function toggleStatus($id) {
        $program = $this->getById($id);
        if (!$program) {
            return false;
        }

        $newStatus = $program['status'] === 'active' ? 'inactive' : 'active';
        return $this->update($id, ['status' => $newStatus]);
    }

    /**
     * Get program statistics
     */
    public function getStats($programName = null) {
        if ($programName) {
            // Map program names to internal request types (e.g., 'Burial Assistance' -> 'burial')
            $internalTypeName = strtolower(strtok($programName, ' '));
            
            $sql = "SELECT
                        COUNT(*) as stats_total,
                        COUNT(CASE WHEN LOWER(status) = 'pending' THEN 1 END) as stats_pending,
                        COUNT(CASE WHEN LOWER(status) = 'approved' THEN 1 END) as stats_approved,
                        COUNT(CASE WHEN LOWER(status) = 'rejected' THEN 1 END) as stats_rejected,
                        COUNT(CASE WHEN LOWER(status) = 'completed' THEN 1 END) as stats_completed,
                        AVG(DATEDIFF(NOW(), created_at)) as avg_days_pending
                    FROM requests WHERE LOWER(request_type) = ? OR LOWER(request_type) = ?";
            $stmt = $this->db->prepare($sql);
            $lowerName = strtolower($programName);
            $stmt->bind_param("ss", $lowerName, $internalTypeName);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } else {
            // Get stats for all programs
            $sql = "SELECT
                        request_type,
                        COUNT(*) as stats_total,
                        COUNT(CASE WHEN status = 'pending' THEN 1 END) as stats_pending,
                        COUNT(CASE WHEN status = 'approved' THEN 1 END) as stats_approved,
                        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as stats_rejected,
                        COUNT(CASE WHEN status = 'completed' THEN 1 END) as stats_completed
                    FROM requests GROUP BY request_type";
            return $this->db->query($sql);
        }
    }

    /**
     * Get program with additional stats
     */
    public function getProgramWithStats($id) {
        $program = $this->getById($id);
        if (!$program) {
            return null;
        }

        $stats = $this->getStats($program['name']);
        if ($stats) {
            $program = array_merge($program, $stats);
        }

        return $program;
    }

    /**
     * Update program statistics cache
     */
    public function updateStats($programName, $stats) {
        // Update the programs table with stats
        $stmt = $this->db->prepare(
            "UPDATE programs SET
                stats_total = ?,
                stats_pending = ?,
                stats_approved = ?,
                stats_rejected = ?,
                stats_completed = ?
            WHERE name = ?"
        );

        // Extract stats into variables to prevent pass-by-reference notices
        $total = $stats['stats_total'] ?? 0;
        $pending = $stats['stats_pending'] ?? 0;
        $approved = $stats['stats_approved'] ?? 0;
        $rejected = $stats['stats_rejected'] ?? 0;
        $completed = $stats['stats_completed'] ?? 0;

        $stmt->bind_param(
            "iiiiss",
            $total,
            $pending,
            $approved,
            $rejected,
            $completed,
            $programName
        );

        return $stmt->execute();
    }

    /**
     * Get all programs with their statistics
     */
    public function getAllWithStats() {
        $result = [];
        $programs = $this->getAll();

        if ($programs) {
            while ($program = $programs->fetch_assoc()) {
                $stats = $this->getStats($program['name']);
                $program = array_merge($program, $stats ?? []);
                $result[] = $program;
            }
        }

        return $result;
    }

    /**
     * Get program requests
     */
    public function getRequests($programName, $filters = []) {
        $sql = "SELECT r.*,
                CASE
                    WHEN r.request_type = 'educational' THEN (SELECT school FROM req_educational WHERE request_id = r.id LIMIT 1)
                    WHEN r.request_type = 'medical' THEN (SELECT patientName FROM req_medical WHERE request_id = r.id LIMIT 1)
                    WHEN r.request_type = 'burial' THEN (SELECT deceasedName FROM req_burial WHERE request_id = r.id LIMIT 1)
                    ELSE ''
                END as related_entity
            FROM requests r
            WHERE r.request_type = ?";

        $params = [$programName];
        $types = "s";

        if (isset($filters['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (isset($filters['date_from'])) {
            $sql .= " AND DATE(r.created_at) >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }

        if (isset($filters['date_to'])) {
            $sql .= " AND DATE(r.created_at) <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }

        $sql .= " ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result();
    }
}

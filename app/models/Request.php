<?php
/**
 * Request Model - Master-Detail Architecture
 */

class Request {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM requests ORDER BY created_at DESC");
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE email = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByEmailAndTypeWithDetails($email, $type) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE LOWER(email) = LOWER(?) AND request_type = ? ORDER BY created_at DESC");
        $stmt->bind_param("ss", $email, $type);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $allowedTypes = ['educational', 'medical', 'burial', 'employment', 'transportation'];
        
        foreach ($results as &$master) {
            $table = "req_" . strtolower($master['request_type']);
            if (in_array($master['request_type'], $allowedTypes)) {
                $stmt_detail = $this->db->prepare("SELECT * FROM $table WHERE request_id = ?");
                if ($stmt_detail) {
                    $stmt_detail->bind_param("i", $master['id']);
                    $stmt_detail->execute();
                    $detail = $stmt_detail->get_result()->fetch_assoc();
                    if ($detail) {
                        unset($detail['id'], $detail['request_id']);
                        $master['details'] = json_encode($detail);
                    } else {
                        $master['details'] = !empty($master['details']) ? $master['details'] : json_encode([]);
                    }
                } else {
                    $master['details'] = !empty($master['details']) ? $master['details'] : json_encode([]);
                }
            } else {
                $master['details'] = !empty($master['details']) ? $master['details'] : json_encode([]);
            }
        }
        
        return $results;
    }

    public function getByIdentifier($identifier) {
        $searchTerm = "%$identifier%";
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE fullname LIKE ? OR email LIKE ? OR details LIKE ? ORDER BY created_at DESC");
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByReferenceNumber($ref) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE reference_number = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $ref);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $status = defined('STATUS_PENDING') ? STATUS_PENDING : 'pending';
        $detailPayload = $data['details'] ?? [];
        $jsonDetails = json_encode(
            is_array($detailPayload) ? $detailPayload : [],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        // Prefer storing JSON on master row so admin dashboards always see the application.
        $stmt = $this->db->prepare(
            "INSERT INTO requests (reference_number, fullname, email, request_type, status, details) VALUES (?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) {
            // Older DB without `details` column
            $stmt = $this->db->prepare(
                "INSERT INTO requests (reference_number, fullname, email, request_type, status) VALUES (?, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                error_log('Request::create prepare failed: ' . $this->db->error);
                return false;
            }
            $stmt->bind_param(
                'sssss',
                $data['reference_number'],
                $data['fullname'],
                $data['email'],
                $data['request_type'],
                $status
            );
        } else {
            $stmt->bind_param(
                'ssssss',
                $data['reference_number'],
                $data['fullname'],
                $data['email'],
                $data['request_type'],
                $status,
                $jsonDetails
            );
        }

        if (!$stmt->execute()) {
            error_log('Request::create execute failed: ' . $stmt->error);
            return false;
        }

        $request_id = (int) $this->db->insert_id;

        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/Notification.php';
        $userModel = new User();
        $notifModel = new Notification();
        $admins = $userModel->getAdmins();
        $message = 'New ' . ucfirst($data['request_type']) . ' application from ' . $data['fullname'];
        $link = base_url('admin/requests/view?id=' . $request_id);
        if ($admins) {
            while ($admin = $admins->fetch_assoc()) {
                $notifModel->create($admin['id'], $message, $link);
            }
        }

        // Detail tables are best-effort — failures no longer hide the application from the admin list.
        try {
            $saved = $this->saveDetails($request_id, $data['request_type'], is_array($detailPayload) ? $detailPayload : []);
            if (!$saved) {
                error_log('Request::create saveDetails returned false for request id ' . $request_id . ' — master row and JSON details are still stored.');
            }
        } catch (Throwable $e) {
            error_log('Request::create saveDetails exception for request id ' . $request_id . ': ' . $e->getMessage());
        }

        return true;
    }

    private function saveDetails($request_id, $type, $details) {
        $allowedTypes = ['educational', 'medical', 'burial', 'employment', 'transportation'];
        if (!in_array($type, $allowedTypes)) {
            return true; // Rely on JSON in master row for custom programs
        }

        // One detail table per program (matches database/complete_schema.sql)
        $table = "req_" . strtolower($type);
        
        $fields = ['request_id'];
        $values = [$request_id];
        $types = 'i';
        
        foreach ($details as $key => $val) {
            if ($val !== '' && $val !== null) {
                // Ensure key is safe from SQL injection
                $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
                $fields[] = $safeKey;
                $values[] = $val;
                $types .= 's';
            }
        }
        
        $placeholders = implode(", ", array_fill(0, count($fields), "?"));
        $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES ($placeholders)";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare Failed in saveDetails: " . $this->db->error);
            return false;
        }
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function updateStatus($id, $status) {
        // Whitelist allowed statuses
        $allowed = ['pending', 'approved', 'rejected', 'completed'];
        if (!in_array($status, $allowed)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $master = $stmt->get_result()->fetch_assoc();
        
        if ($master) {
            $type = $master['request_type'];
            $allowedTypes = ['educational', 'medical', 'burial', 'employment', 'transportation'];

            $table = "req_" . strtolower($type);

            if (in_array($type, $allowedTypes)) {
                $stmt_detail = $this->db->prepare("SELECT * FROM $table WHERE request_id = ?");
                if (!$stmt_detail) {
                    error_log("Prepare Failed in getById for table $table: " . $this->db->error);
                    $master['details'] = !empty($master['details']) ? $master['details'] : json_encode([]);
                    return $master;
                }
                $stmt_detail->bind_param("i", $id);
                $stmt_detail->execute();
                $detail = $stmt_detail->get_result()->fetch_assoc();

                if ($detail) {
                    unset($detail['id'], $detail['request_id']);
                    $master['details'] = json_encode($detail);
                } else {
                    $master['details'] = !empty($master['details']) ? $master['details'] : json_encode([]);
                }
            } else {
                $master['details'] = !empty($master['details']) ? $master['details'] : json_encode([]);
            }
        }
        
        return $master;
    }

    public function hasDuplicateActiveRequest($user_id, $request_type) {
        $stmt = $this->db->prepare(
            "SELECT id FROM requests WHERE user_id = ? AND request_type = ? AND status = 'pending' LIMIT 1"
        );
        $stmt->bind_param("is", $user_id, $request_type);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getStats() {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0,
            'by_type' => [],
            'daily' => [],
            'monthly' => []
        ];

        // Status counts
        $res = $this->db->query("SELECT status, count(*) as count FROM requests GROUP BY status");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $status = strtolower($row['status']);
                if (isset($stats[$status])) {
                    $stats[$status] = (int)$row['count'];
                }
                $stats['total'] += (int)$row['count'];
            }
        }

        // Program counts
        $res = $this->db->query("SELECT request_type, count(*) as count FROM requests GROUP BY request_type");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $stats['by_type'][$row['request_type']] = (int)$row['count'];
            }
        }
        
        // Ensure all types exist in by_type
        $types = ['educational', 'medical', 'burial', 'employment', 'transportation'];
        foreach($types as $t) {
            if(!isset($stats['by_type'][$t])) $stats['by_type'][$t] = 0;
        }

        // Daily Activity (Last 7 days)
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('D', strtotime($date));
            $sql = "SELECT count(*) as count FROM requests WHERE DATE(created_at) = '$date'";
            $q = $this->db->query($sql);
            $stats['daily'][$dayName] = ($q && ($row = $q->fetch_assoc())) ? (int) $row['count'] : 0;
        }

        // Monthly Activity (Last 6 months)
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M', strtotime($month . "-01"));
            $sql = "SELECT count(*) as count FROM requests WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'";
            $q = $this->db->query($sql);
            $stats['monthly'][$monthName] = ($q && ($row = $q->fetch_assoc())) ? (int) $row['count'] : 0;
        }

        return $stats;
    }

    public function isRequestLogsTablePresent(): bool {
        try {
            $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'request_logs' LIMIT 1";
            $result = $this->db->query($sql);
            return $result && $result->num_rows > 0;
        } catch (Throwable $e) {
            error_log('Request::isRequestLogsTablePresent — ' . $e->getMessage());
            return false;
        }
    }

    public function ensureRequestLogsTablePresent(): bool {
        if ($this->isRequestLogsTablePresent()) {
            return true;
        }

        $sql = "CREATE TABLE IF NOT EXISTS request_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            request_id INT NOT NULL,
            action_by INT NOT NULL,
            status_from VARCHAR(32) NULL,
            status_to VARCHAR(32) NOT NULL,
            remarks TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
            FOREIGN KEY (action_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_request (request_id),
            INDEX idx_created (created_at DESC)
        )";

        if (!$this->db->query($sql)) {
            error_log('Request::ensureRequestLogsTablePresent failed: ' . $this->db->error);
            return false;
        }

        return true;
    }

    public function getUserStats($user_id) {
        $stats = [
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
            'last_activity' => null
        ];

        $stmt = $this->db->prepare("SELECT status, COUNT(*) as count, MAX(created_at) as last_date FROM requests WHERE user_id = ? GROUP BY status");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $status = strtolower($row['status']);
                if (isset($stats[$status])) {
                    $stats[$status] = (int)$row['count'];
                }
                if ($stats['last_activity'] === null || $row['last_date'] > $stats['last_activity']) {
                    $stats['last_activity'] = $row['last_date'];
                }
            }
        }

        return $stats;
    }

    public function logStatusChange($request_id, $admin_id, $from, $to, $remarks = '') {
        if (!$this->ensureRequestLogsTablePresent()) {
            error_log('request_logs table missing and could not be created automatically.');
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO request_logs (request_id, action_by, status_from, status_to, remarks) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            error_log('request_logs prepare failed (did you run database/migrations/002_request_logs_and_access.sql?): ' . $this->db->error);
            return false;
        }
        $stmt->bind_param("iisss", $request_id, $admin_id, $from, $to, $remarks);
        return $stmt->execute();
    }

    public function getLogsByRequestId($request_id) {
        if (!$this->ensureRequestLogsTablePresent()) {
            return false;
        }

        $stmt = $this->db->prepare("
            SELECT rl.*, u.fullname AS admin_name
            FROM request_logs rl
            LEFT JOIN users u ON rl.action_by = u.id
            WHERE rl.request_id = ?
            ORDER BY rl.created_at DESC
        ");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getLogs() {
        if (!$this->ensureRequestLogsTablePresent()) {
            return false;
        }

        $sql = "
            SELECT rl.*, r.fullname, r.request_type, u.fullname AS admin_name
            FROM request_logs rl
            JOIN requests r ON rl.request_id = r.id
            JOIN users u ON rl.action_by = u.id
            ORDER BY rl.created_at DESC
        ";
        $res = $this->db->query($sql);
        if (!$res) {
            error_log('request_logs query failed: ' . $this->db->error);
        }
        return $res;
    }

    public function hasDuplicateActiveRequestByEmail($email, $request_type) {
        // Check for duplicate active requests by email
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM requests WHERE email = ? AND request_type = ? AND status = 'pending'");
        $stmt->bind_param("ss", $email, $request_type);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    public function getPendingCount() {
        $res = $this->db->query("SELECT COUNT(*) as count FROM requests WHERE status = 'pending'");
        if ($res) {
            return (int)$res->fetch_assoc()['count'];
        }
        return 0;
    }
}

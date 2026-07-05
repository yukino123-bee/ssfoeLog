<?php

class ClientApplyAccess {
    protected $db;

    /** @var bool|null */
    private $tablePresent = null;

    public function __construct() {
        $this->db = get_db_connection();
    }

    /**
     * True when client_apply_access exists (cached per instance).
     */
    public function isTablePresent(): bool {
        if ($this->tablePresent !== null) {
            return $this->tablePresent;
        }
        try {
            $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'client_apply_access' LIMIT 1";
            $result = $this->db->query($sql);
            $this->tablePresent = $result && $result->num_rows > 0;
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::isTablePresent — ' . $e->getMessage());
            $this->tablePresent = false;
        }
        return $this->tablePresent;
    }

    /**
     * Create client_apply_access if missing. Returns false if DB user cannot CREATE or creation fails.
     */
    public function ensureTablePresent(): bool {
        if ($this->isTablePresent()) {
            return true;
        }

        $sql = "CREATE TABLE IF NOT EXISTS client_apply_access (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fullname VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            mobile_number VARCHAR(20) NULL,
            referral_code VARCHAR(32) NOT NULL UNIQUE,
            access_pin VARCHAR(12) NULL,
            status ENUM('pending','approved','rejected') DEFAULT 'pending',
            admin_note TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_mobile (mobile_number),
            INDEX idx_status (status)
        )";

        try {
            if (!$this->db->query($sql)) {
                error_log('ClientApplyAccess::ensureTablePresent failed: ' . $this->db->error);
                $this->tablePresent = false;
                return false;
            }
            $this->tablePresent = true;
            return true;
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::ensureTablePresent — ' . $e->getMessage());
            $this->tablePresent = false;
            return false;
        }
    }

    /**
     * @return array{id:int, referral_code:string, access_pin:string, error?:string}|false
     */
    public function createOrGetPending($fullname, $mobile_number) {
        if (!$this->ensureTablePresent()) {
            return false;
        }
        
        $fullname = trim($fullname);
        $mobile_number = trim($mobile_number);

        // Check rate limit: max 2 times a day per number
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS count FROM client_apply_access WHERE mobile_number = ? AND DATE(created_at) = CURDATE()"
            );
            $stmt->bind_param('s', $mobile_number);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            if ($res && $res['count'] >= 1) {
                return ['error' => 'You have reached the daily limit of 1 request for this number.'];
            }
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::createOrGetPending (rate limit check) — ' . $e->getMessage());
        }

        try {
            for ($i = 0; $i < 5; $i++) {
                $code = strtoupper(substr(bin2hex(random_bytes(6)), 0, 10));
                
                $stmt = $this->db->prepare(
                    "INSERT INTO client_apply_access (fullname, email, mobile_number, referral_code, status) VALUES (?, '', ?, ?, 'pending')"
                );
                $stmt->bind_param('sss', $fullname, $mobile_number, $code);
                
                if ($stmt->execute()) {
                    return [
                        'id' => (int) $this->db->insert_id,
                        'referral_code' => $code,
                        'access_pin' => null,
                        'existing' => false,
                    ];
                }
            }
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::createOrGetPending — ' . $e->getMessage());
        }
        return false;
    }

    public function findByReferralAndPin($referral, $pin, $mobile_number) {
        if (!$this->ensureTablePresent()) {
            return null;
        }
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM client_apply_access WHERE referral_code = ? AND access_pin = ? AND mobile_number = ? AND status = 'approved' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) LIMIT 1"
            );
            $stmt->bind_param('sss', $referral, $pin, $mobile_number);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::findByReferralAndPin — ' . $e->getMessage());
            return null;
        }
    }

    public function getById($id) {
        if (!$this->ensureTablePresent()) {
            return null;
        }
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM client_apply_access WHERE id = ? LIMIT 1"
            );
            $stmt->bind_param('i', $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::getById — ' . $e->getMessage());
            return null;
        }
    }

    public function getByEmail($email) {
        if (!$this->ensureTablePresent()) {
            return [];
        }
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM client_apply_access WHERE email = ? ORDER BY id DESC"
            );
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = [];
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::getByEmail — ' . $e->getMessage());
            return [];
        }
    }

    public function getByMobileNumber($mobile_number) {
        if (!$this->ensureTablePresent()) {
            return [];
        }
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM client_apply_access WHERE mobile_number = ? ORDER BY id DESC"
            );
            $stmt->bind_param('s', $mobile_number);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = [];
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::getByMobileNumber — ' . $e->getMessage());
            return [];
        }
    }

    public function countPending(): int {
        if (!$this->ensureTablePresent()) {
            return 0;
        }
        try {
            $res = $this->db->query("SELECT COUNT(*) AS c FROM client_apply_access WHERE status = 'pending'");
            if ($res && ($row = $res->fetch_assoc())) {
                return (int) $row['c'];
            }
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::countPending — ' . $e->getMessage());
        }
        return 0;
    }

    public function getAllForAdmin() {
        if (!$this->ensureTablePresent()) {
            return [];
        }
        try {
            $res = $this->db->query(
                "SELECT * FROM client_apply_access ORDER BY FIELD(status,'pending','approved','rejected'), created_at DESC"
            );
            if (!$res) {
                return [];
            }
            $rows = [];
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::getAllForAdmin — ' . $e->getMessage());
            return [];
        }
    }

    public function setStatus($id, $status, $access_pin = null, $admin_note = null) {
        if (!$this->ensureTablePresent()) {
            return false;
        }
        $allowed = ['approved', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $id = (int) $id;
        $note = $admin_note ?? '';

        try {
            if ($status === 'approved') {
                $pin = $access_pin ?? str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $stmt = $this->db->prepare(
                    'UPDATE client_apply_access SET status = ?, access_pin = ?, admin_note = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
                );
                $stmt->bind_param('sssi', $status, $pin, $note, $id);
                return $stmt->execute();
            }

            $stmt = $this->db->prepare(
                'UPDATE client_apply_access SET status = ?, access_pin = NULL, admin_note = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
            );
            $stmt->bind_param('ssi', $status, $note, $id);
            return $stmt->execute();
        } catch (Throwable $e) {
            error_log('ClientApplyAccess::setStatus — ' . $e->getMessage());
            return false;
        }
    }
}

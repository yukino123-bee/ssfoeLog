<?php
/**
 * Audit Log Model
 * Tracks all security-sensitive operations for audit trail
 */

class AuditLog {
    protected $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    /**
     * Log an action to the audit trail
     * 
     * @param int $user_id The user performing the action
     * @param string $action The action performed
     * @param string $resource_type Type of resource (user, request, program, etc)
     * @param int $resource_id ID of the resource
     * @param string $details JSON details of the action
     * @param string $ip_address IP address of the requester
     * @return bool Success status
     */
    public function log($user_id, $action, $resource_type, $resource_id, $details = '', $ip_address = '') {
        if (empty($ip_address)) {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        $stmt = $this->db->prepare(
            "INSERT INTO audit_logs (user_id, action, resource_type, resource_id, details, ip_address) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log('AuditLog::log prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param('issis s', $user_id, $action, $resource_type, $resource_id, $details, $ip_address);
        
        if (!$stmt->execute()) {
            error_log('AuditLog::log execute failed: ' . $stmt->error);
            return false;
        }

        return true;
    }

    /**
     * Log a login attempt
     * 
     * @param int $user_id User ID
     * @param bool $success Whether login was successful
     * @param string $email Email used for login
     * @return bool Success status
     */
    public function logLogin($user_id, $success, $email = '') {
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        $details = json_encode(['email' => $email, 'success' => $success]);
        return $this->log($user_id ?? 0, $action, 'user', $user_id ?? 0, $details);
    }

    /**
     * Log logout
     * 
     * @param int $user_id User ID
     * @return bool Success status
     */
    public function logLogout($user_id) {
        return $this->log($user_id, 'LOGOUT', 'user', $user_id);
    }

    /**
     * Log request status change
     * 
     * @param int $user_id Admin user ID
     * @param int $request_id Request ID
     * @param string $old_status Previous status
     * @param string $new_status New status
     * @return bool Success status
     */
    public function logRequestStatusChange($user_id, $request_id, $old_status, $new_status) {
        $action = 'REQUEST_STATUS_CHANGED';
        $details = json_encode(['old_status' => $old_status, 'new_status' => $new_status]);
        return $this->log($user_id, $action, 'request', $request_id, $details);
    }

    /**
     * Log user creation
     * 
     * @param int $user_id Admin user ID
     * @param int $created_user_id New user ID
     * @param string $email New user email
     * @param string $role New user role
     * @return bool Success status
     */
    public function logUserCreation($user_id, $created_user_id, $email, $role) {
        $action = 'USER_CREATED';
        $details = json_encode(['email' => $email, 'role' => $role]);
        return $this->log($user_id, $action, 'user', $created_user_id, $details);
    }

    /**
     * Log user modification
     * 
     * @param int $user_id Admin user ID
     * @param int $modified_user_id User being modified
     * @param array $changes Fields that were changed
     * @return bool Success status
     */
    public function logUserModification($user_id, $modified_user_id, $changes = []) {
        $action = 'USER_MODIFIED';
        $details = json_encode($changes);
        return $this->log($user_id, $action, 'user', $modified_user_id, $details);
    }

    /**
     * Log failed CSRF validation
     * 
     * @param int $user_id User ID (0 if not authenticated)
     * @param string $endpoint The endpoint accessed
     * @return bool Success status
     */
    public function logCSRFFailure($user_id, $endpoint) {
        $action = 'CSRF_VALIDATION_FAILED';
        $details = json_encode(['endpoint' => $endpoint]);
        return $this->log($user_id ?? 0, $action, 'security', 0, $details);
    }

    /**
     * Log suspicious activity
     * 
     * @param int $user_id User ID
     * @param string $activity_type Type of suspicious activity
     * @param string $description Description of the activity
     * @return bool Success status
     */
    public function logSuspiciousActivity($user_id, $activity_type, $description) {
        $action = 'SUSPICIOUS_ACTIVITY_' . strtoupper($activity_type);
        $details = json_encode(['description' => $description]);
        return $this->log($user_id ?? 0, $action, 'security', 0, $details);
    }

    /**
     * Get audit logs for a specific user
     * 
     * @param int $user_id User ID
     * @param int $limit Number of records to return
     * @return array Array of audit logs
     */
    public function getByUser($user_id, $limit = 100) {
        $stmt = $this->db->prepare(
            "SELECT * FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->bind_param('ii', $user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get audit logs for a specific resource
     * 
     * @param string $resource_type Resource type
     * @param int $resource_id Resource ID
     * @param int $limit Number of records
     * @return array Array of audit logs
     */
    public function getByResource($resource_type, $resource_id, $limit = 100) {
        $stmt = $this->db->prepare(
            "SELECT * FROM audit_logs WHERE resource_type = ? AND resource_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->bind_param('sii', $resource_type, $resource_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all audit logs
     * 
     * @param int $limit Number of records
     * @param int $offset Offset for pagination
     * @return array Array of audit logs
     */
    public function getAll($limit = 100, $offset = 0) {
        $stmt = $this->db->prepare(
            "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get suspicious activities
     * 
     * @param int $limit Number of records
     * @return array Array of suspicious activities
     */
    public function getSuspiciousActivities($limit = 100) {
        $query = "SELECT * FROM audit_logs WHERE action LIKE '%SUSPICIOUS%' OR action = 'CSRF_VALIDATION_FAILED' OR action = 'LOGIN_FAILED' ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Delete old audit logs (older than specified days)
     * 
     * @param int $days Number of days to keep
     * @return bool Success status
     */
    public function deleteOldLogs($days = 90) {
        $date = date('Y-m-d H:i:s', strtotime("-$days days"));
        $stmt = $this->db->prepare("DELETE FROM audit_logs WHERE created_at < ?");
        $stmt->bind_param('s', $date);
        return $stmt->execute();
    }
}

<?php
/**
 * AdminController - Handles administrative tasks
 */

class AdminController {
    
    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $stats = $requestModel->getStats();
        
        // Ensure standard keys exist in stats to prevent view errors
        $stats['total'] = $stats['total'] ?? 0;
        $stats['pending'] = $stats['pending'] ?? 0;
        $stats['approved'] = $stats['approved'] ?? 0;
        $stats['rejected'] = $stats['rejected'] ?? 0;
        $stats['completed'] = $stats['completed'] ?? 0;
        if (!isset($stats['by_type'])) $stats['by_type'] = [];
        
        // Ensure program breakdown keys exist
        $types = ['educational', 'medical', 'burial', 'employment', 'transportation'];
        foreach($types as $t) {
            if(!isset($stats['by_type'][$t])) $stats['by_type'][$t] = 0;
        }

        $res = $requestModel->getAll();
        $all_requests = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $all_requests[] = $row;
            }
        }
        
        $recent_requests = array_slice($all_requests, 0, 5);

        $db = get_db_connection();
        $r = $db->query("SELECT COUNT(DISTINCT email) AS cnt FROM requests WHERE DATE(created_at) = CURDATE()");
        $active_today = ($r && ($row = $r->fetch_assoc())) ? (int)$row['cnt'] : 0;

        $r2 = $db->query("SELECT COUNT(*) AS cnt FROM requests WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
        $new_clients_month = ($r2 && ($row = $r2->fetch_assoc())) ? (int)$row['cnt'] : 0;

        $r3 = $db->query("SELECT COUNT(*) AS c FROM requests WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
        $submissions_this_month = ($r3 && ($row = $r3->fetch_assoc())) ? (int)$row['c'] : 0;
        $r4 = $db->query("SELECT COUNT(*) AS c FROM requests WHERE created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01') AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')");
        $submissions_last_month = ($r4 && ($row = $r4->fetch_assoc())) ? (int)$row['c'] : 0;
        if ($submissions_last_month > 0) {
            $mom_submissions_pct = round((($submissions_this_month - $submissions_last_month) / $submissions_last_month) * 100, 1);
        } else {
            $mom_submissions_pct = $submissions_this_month > 0 ? 100.0 : 0.0;
        }

        $program_breakdown = [];
        foreach ($types as $t) {
            $program_breakdown[$t] = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'completed' => 0];
        }
        $rb = $db->query("SELECT request_type, status, COUNT(*) AS c FROM requests GROUP BY request_type, status");
        if ($rb) {
            while ($row = $rb->fetch_assoc()) {
                $t = $row['request_type'];
                $st = strtolower($row['status']);
                if (!isset($program_breakdown[$t]) || !isset($program_breakdown[$t][$st])) {
                    continue;
                }
                $program_breakdown[$t][$st] = (int)$row['c'];
                $program_breakdown[$t]['total'] += (int)$row['c'];
            }
        }


        $title = "Admin Dashboard - " . APP_NAME;
        require_once APP_PATH . '/views/admin/dashboard.php';
    }

    public function requests() {
        $filter_type = $_GET['type'] ?? 'all';
        
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $res = $requestModel->getAll();
        $all_requests = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $all_requests[] = $row;
            }
        }

        $requests = $all_requests;
        // Server-side filtering removed to allow JS-based tab switching with all data
        // $if ($filter_type !== 'all' && !empty($filter_type)) { ... }

        $title = ucfirst($filter_type) . " Requests - " . APP_NAME;
        require_once APP_PATH . '/views/admin/requests.php';
    }

    public function viewRequest() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect(base_url('admin/requests'));
        }

        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $request = $requestModel->getById($id);
        
        if (!$request) {
            redirect(base_url('admin/requests'));
        }

        $logs = [];
        $logRes = $requestModel->getLogsByRequestId($id);
        if ($logRes) {
            while ($row = $logRes->fetch_assoc()) {
                $logs[] = $row;
            }
        }

        $title = "Request Details - " . APP_NAME;
        require_once APP_PATH . '/views/admin/request_view.php';
    }

    public function systemStatus() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'toggle_maintenance') {
                $mode = isset($_POST['maintenance']) && $_POST['maintenance'] === 'on' ? '1' : '0';
                file_put_contents(ROOT_PATH . '/storage/maintenance.flag', $mode);
                $_SESSION['success_message'] = $mode === '1' ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.';
            }

            header('Location: ' . base_url('admin/system-status'));
            exit;
        }

        $maintenanceMode = file_exists(ROOT_PATH . '/storage/maintenance.flag') && file_get_contents(ROOT_PATH . '/storage/maintenance.flag') === '1';

        $title = 'System Status - ' . APP_NAME;
        require_once APP_PATH . '/views/admin/system_status.php';
    }

    public function reports() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $stats = $requestModel->getStats();
        
        $stats['total'] = $stats['total'] ?? 0;
        $stats['pending'] = $stats['pending'] ?? 0;
        $stats['approved'] = $stats['approved'] ?? 0;
        $stats['rejected'] = $stats['rejected'] ?? 0;
        $stats['completed'] = $stats['completed'] ?? 0;
        if (!isset($stats['by_type'])) $stats['by_type'] = [];
        
        $res = $requestModel->getAll();
        $all_requests = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $all_requests[] = $row;
            }
        }

        $report_type = $_GET['report_type'] ?? 'individual';

        // REPORT 1: Individual search
        $individual_search = trim($_GET['search'] ?? '');
        $individual_results = [];
        if ($individual_search !== '') {
            $individual_results = array_values(array_filter($all_requests, function($r) use ($individual_search) {
                return stripos($r['fullname'], $individual_search) !== false
                    || stripos($r['email'], $individual_search) !== false;
            }));
        }

        // REPORT 2: Per Program
        $program_filter  = $_GET['program'] ?? 'all';
        $program_status  = $_GET['prog_status'] ?? 'all';
        $program_types   = ['educational','medical','burial','employment','transportation'];
        $program_data    = [];
        foreach ($program_types as $pt) {
            $rows = array_values(array_filter($all_requests, fn($r) => $r['request_type'] === $pt));
            $program_data[$pt] = [
                'total'     => count($rows),
                'pending'   => count(array_filter($rows, fn($r) => $r['status']==='pending')),
                'approved'  => count(array_filter($rows, fn($r) => $r['status']==='approved')),
                'rejected'  => count(array_filter($rows, fn($r) => $r['status']==='rejected')),
                'completed' => count(array_filter($rows, fn($r) => $r['status']==='completed')),
                'rows'      => $rows,
            ];
        }
        $program_requests = $all_requests;
        if ($program_filter !== 'all') {
            $program_requests = array_values(array_filter($program_requests, fn($r) => $r['request_type'] === $program_filter));
        }
        if ($program_status !== 'all') {
            $program_requests = array_values(array_filter($program_requests, fn($r) => $r['status'] === $program_status));
        }

        // Legacy (keep for backwards compat)
        $filters  = ['type'=>'all','status'=>'all','date_from'=>'','date_to'=>''];
        $requests = $all_requests;

        $title = "Reports & Analytics - " . APP_NAME;
        require_once APP_PATH . '/views/admin/reports.php';
    }

    public function exportCSV() { $this->exportReport(); }

    public function exportReport() {
        if (session_status() === PHP_SESSION_NONE) { @session_start(); }
        $report_type = $_GET['report_type'] ?? 'individual';
        $format      = $_GET['format']      ?? 'excel';
        
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $res = $requestModel->getAll();
        $all_requests = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $all_requests[] = $row;
            }
        }
        
        $data = $all_requests;
        if ($report_type === 'individual' && !empty($_GET['search'])) {
            $s = $_GET['search'];
            $data = array_values(array_filter($data, fn($r) => stripos($r['fullname'],$s)!==false || stripos($r['email'],$s)!==false));
        }
        if ($report_type === 'program' && !empty($_GET['program']) && $_GET['program'] !== 'all') {
            $data = array_values(array_filter($data, fn($r) => $r['request_type'] === $_GET['program']));
        }
        if ($report_type === 'program' && !empty($_GET['prog_status']) && $_GET['prog_status'] !== 'all') {
            $data = array_values(array_filter($data, fn($r) => $r['status'] === $_GET['prog_status']));
        }
        $filename = 'ssfo_' . $report_type . '_report_' . date('Y-m-d');
        if ($format === 'html') {
            $title = "Exported Report - " . APP_NAME;
            require_once APP_PATH . '/views/admin/report_export_view.php';
            exit;
        } elseif ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table border="1"><thead><tr>';
            foreach (['ID','Full Name','Email','Request Type','Status','Date Submitted'] as $h) {
                echo '<th style="background:#d32f2f;color:#fff;font-weight:bold;padding:6px 12px;">'.htmlspecialchars($h).'</th>';
            }
            echo '</tr></thead><tbody>';
            foreach ($data as $row) {
                echo '<tr>';
                echo '<td style="padding:5px 10px;">'.$row['id'].'</td>';
                echo '<td style="padding:5px 10px;">'.htmlspecialchars($row['fullname']).'</td>';
                echo '<td style="padding:5px 10px;">'.htmlspecialchars($row['email']).'</td>';
                echo '<td style="padding:5px 10px;">'.htmlspecialchars(ucfirst($row['request_type'])).'</td>';
                echo '<td style="padding:5px 10px;">'.htmlspecialchars(ucfirst($row['status'])).'</td>';
                echo '<td style="padding:5px 10px;">'.htmlspecialchars(date('M d, Y', strtotime($row['created_at']))).'</td>';
                echo '</tr>';
            }
            echo '</tbody></table></body></html>';
        } else {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID','Full Name','Email','Request Type','Status','Date Submitted']);
            foreach ($data as $row) {
                fputcsv($output, [$row['id'],$row['fullname'],$row['email'],$row['request_type'],$row['status'],$row['created_at']]);
            }
            fclose($output);
        }
        exit;
    }


    public function exportLogsCSV() {
        if (session_status() === PHP_SESSION_NONE) { @session_start(); }
        
        $db = get_db_connection();
        $dateFilter = $_GET['date'] ?? '';
        
        $where = "1=1";
        if (!empty($dateFilter)) {
            $where .= " AND DATE(l.created_at) = '" . $db->real_escape_string($dateFilter) . "'";
        }
        
        $sql = "SELECT l.id, l.created_at, r.fullname as applicant, r.request_type, 
                       u.fullname as admin_name, l.status_from as old_status, l.status_to as new_status, l.remarks
                FROM request_logs l
                LEFT JOIN requests r ON l.request_id = r.id
                LEFT JOIN users u ON l.action_by = u.id
                WHERE $where
                ORDER BY l.created_at DESC";
                
        $res = $db->query($sql);

        $filename = 'ssfo_request_logs_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Log ID', 'Date & Time', 'Applicant', 'Program', 'Processed By', 'Old Status', 'New Status', 'Remarks']);

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                fputcsv($output, [
                    $row['id'],
                    $row['created_at'],
                    $row['applicant'],
                    $row['request_type'],
                    $row['admin_name'],
                    $row['old_status'],
                    $row['new_status'],
                    $row['remarks']
                ]);
            }
        }

        fclose($output);
        exit;
    }

    public function updateStatus() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        $remarks = $_POST['remarks'] ?? 'Status updated by administrator';
        $adminId = $_SESSION['user_id'] ?? 0;
        
        if ($id && $status) {
            require_once APP_PATH . '/models/Request.php';
            $requestModel = new Request();
            
            // Get request details before updating
            $request = $requestModel->getById($id);
            $oldStatus = $request['status'] ?? null;
            
            if ($requestModel->updateStatus($id, $status)) {
                // Log the change
                $requestModel->logStatusChange($id, $adminId, $oldStatus, $status, $remarks);
                
                $_SESSION['success_message'] = "Status updated successfully";
                
                // Email notification
                if ($request && !empty($request['email'])) {
                    require_once APP_PATH . '/helpers/email.php';
                    if ($status === 'approved') {
                        send_approval_notice($request['email'], $request['fullname'], $request['request_type'], $id, $request['reference_number']);
                    } else {
                        notify_client_status_update($request['email'], $request['fullname'], $request['request_type'], $status, $request['reference_number']);
                    }
                }
            } else {
                $_SESSION['error_message'] = "Failed to update status";
            }
        } else {
            $_SESSION['error_message'] = "Invalid request parameters";
        }
        
        header('Location: ' . base_url('admin/requests/view?id=' . $id));
        exit;
    }


    public function programs() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        require_once APP_PATH . '/models/Program.php';
        $programModel = new Program();

        $allPrograms = $programModel->getAllWithStats();

        $title = "Program Management - " . APP_NAME;
        require_once APP_PATH . '/views/admin/programs.php';
    }

    public function announcements() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        require_once APP_PATH . '/models/Announcement.php';
        $announcementModel = new Announcement();

        $action = $_POST['action'] ?? $_GET['action'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'create') {
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $priority = $_POST['priority'] ?? 'Normal';
                $audience = $_POST['audience'] ?? 'All Beneficiaries';

                if (!empty($title) && !empty($content)) {
                    if ($announcementModel->create($title, $content, $priority, $audience)) {
                        $_SESSION['success_message'] = "Announcement created successfully!";
                    } else {
                        $_SESSION['error_message'] = "Failed to create announcement.";
                    }
                } else {
                    $_SESSION['error_message'] = "Title and content are required.";
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id && $announcementModel->delete($id)) {
                    $_SESSION['success_message'] = "Announcement deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Failed to delete announcement.";
                }
            }

            redirect(base_url('admin/announcements'));
        }

        // getAll() returns an array of rows, not a mysqli_result
        $announcements = $announcementModel->getAll();
        if (!is_array($announcements)) {
            $announcements = [];
        }

        $title = "Announcements - " . APP_NAME;
        require_once APP_PATH . '/views/admin/announcements.php';
    }

    public function editAnnouncement() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect(base_url('admin/announcements'));
        }

        require_once APP_PATH . '/models/Announcement.php';
        $announcementModel = new Announcement();
        $announcement = $announcementModel->findById($id);

        if (!$announcement) {
            $_SESSION['error_message'] = "Announcement not found.";
            redirect(base_url('admin/announcements'));
        }

        $title = "Edit Announcement - " . APP_NAME;
        require_once APP_PATH . '/views/admin/announcements_edit.php';
    }

    public function updateAnnouncement() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/announcements'));
        }

        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $priority = $_POST['priority'] ?? 'Normal';
        $audience = $_POST['audience'] ?? 'All Beneficiaries';

        if ($id > 0 && !empty($title) && !empty($content)) {
            require_once APP_PATH . '/models/Announcement.php';
            $announcementModel = new Announcement();

            $updateData = [
                'title' => $title,
                'content' => $content,
                'priority' => $priority,
                'audience' => $audience
            ];

            if ($announcementModel->update($id, $updateData)) {
                $_SESSION['success_message'] = "Announcement updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to update announcement.";
            }
        } else {
            $_SESSION['error_message'] = "Title and content are required.";
        }

        redirect(base_url('admin/announcements'));
    }

    public function editProgram() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect(base_url('admin/programs'));
        }

        require_once APP_PATH . '/models/Program.php';
        $programModel = new Program();
        $program = $programModel->getById($id);

        if (!$program) {
            $_SESSION['error_message'] = "Program not found.";
            redirect(base_url('admin/programs'));
        }

        // Decode JSON fields
        $program['required_documents'] = json_decode($program['required_documents'] ?? '[]', true);
        $program['custom_fields'] = json_decode($program['custom_fields'] ?? '[]', true);

        $title = "Edit Program - " . APP_NAME;
        require_once APP_PATH . '/views/admin/programs_edit.php';
    }

    public function createProgram() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/programs'));
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        $required_documents = isset($_POST['required_documents']) && is_array($_POST['required_documents']) 
            ? json_encode($_POST['required_documents']) 
            : '[]';
            
        $custom_fields = isset($_POST['custom_fields']) && is_array($_POST['custom_fields']) 
            ? json_encode($_POST['custom_fields']) 
            : '[]';

        if (empty($name)) {
            $_SESSION['error_message'] = "Program name is required.";
            redirect(base_url('admin/programs'));
        }

        require_once APP_PATH . '/models/Program.php';
        $programModel = new Program();

        // Check for duplicate
        if ($programModel->getByName($name)) {
            $_SESSION['error_message'] = "A program with this name already exists.";
            redirect(base_url('admin/programs'));
        }

        $programData = [
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'icon' => $icon,
            'status' => $status,
            'required_documents' => $required_documents,
            'custom_fields' => $custom_fields,
            'created_by' => $_SESSION['user_id'] ?? 0
        ];

        if ($programModel->create($programData)) {
            $_SESSION['success_message'] = "Program created successfully!";
            redirect(base_url('admin/programs'));
        } else {
            $_SESSION['error_message'] = "Failed to create program.";
            redirect(base_url('admin/programs'));
        }
    }

    public function updateProgram() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/programs'));
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect(base_url('admin/programs'));
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $status = $_POST['status'] ?? 'active';

        $required_documents = isset($_POST['required_documents']) && is_array($_POST['required_documents']) 
            ? json_encode($_POST['required_documents']) 
            : '[]';
            
        $custom_fields = isset($_POST['custom_fields']) && is_array($_POST['custom_fields']) 
            ? json_encode($_POST['custom_fields']) 
            : '[]';

        if (empty($name)) {
            $_SESSION['error_message'] = "Program name is required.";
            redirect(base_url('admin/programs/edit?id=' . $id));
        }

        require_once APP_PATH . '/models/Program.php';
        $programModel = new Program();

        // Check for duplicate name (excluding current program)
        $existing = $programModel->getByName($name);
        if ($existing && $existing['id'] != $id) {
            $_SESSION['error_message'] = "A different program with this name already exists.";
            redirect(base_url('admin/programs/edit?id=' . $id));
        }

        $updateData = [
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'icon' => $icon,
            'status' => $status,
            'required_documents' => $required_documents,
            'custom_fields' => $custom_fields
        ];

        if ($programModel->update($id, $updateData)) {
            $_SESSION['success_message'] = "Program updated successfully!";
            redirect(base_url('admin/programs'));
        } else {
            $_SESSION['error_message'] = "Failed to update program.";
            redirect(base_url('admin/programs/edit?id=' . $id));
        }
    }

    public function deleteProgram() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/programs'));
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect(base_url('admin/programs'));
        }

        require_once APP_PATH . '/models/Program.php';
        $programModel = new Program();

        if ($programModel->delete($id)) {
            $_SESSION['success_message'] = "Program deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete program.";
        }

        redirect(base_url('admin/programs'));
    }

    public function profile() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        require_once APP_PATH . '/models/User.php';
        $userModel = new User();
        $admin = $userModel->findById($_SESSION['user_id'] ?? 0);
        
        if (!$admin || $admin['role'] !== 'admin') {
            redirect(base_url('login'));
        }

        // Fetch admin info
        $db = get_db_connection();
        $stmt = $db->prepare("SELECT * FROM admin_info WHERE user_id = ?");
        $stmt->bind_param("i", $admin['id']);
        $stmt->execute();
        $adminInfo = $stmt->get_result()->fetch_assoc() ?: [];

        $title = "Admin Profile - " . APP_NAME;
        require_once APP_PATH . '/views/admin/profile.php';
    }

    public function updateProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/profile'));
        }

        require_once APP_PATH . '/models/User.php';
        $userModel = new User();
        $adminId = $_SESSION['user_id'] ?? 0;
        
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $department = trim($_POST['department'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        if (empty($fullname) || empty($email)) {
            $_SESSION['error_message'] = "Full name and email are required.";
            redirect(base_url('admin/profile'));
        }

        $updateData = [
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update user
        if ($userModel->update($adminId, $updateData)) {
            $_SESSION['user_fullname'] = $fullname; // Update session
            
            // Update admin_info
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT id FROM admin_info WHERE user_id = ?");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $exists = $stmt->get_result()->fetch_assoc();
            
            if ($exists) {
                $stmt = $db->prepare("UPDATE admin_info SET department = ?, position = ?, phone = ?, bio = ? WHERE user_id = ?");
                $stmt->bind_param("ssssi", $department, $position, $phone, $bio, $adminId);
                $stmt->execute();
            } else {
                $stmt = $db->prepare("INSERT INTO admin_info (user_id, department, position, phone, bio) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $adminId, $department, $position, $phone, $bio);
                $stmt->execute();
            }

            $_SESSION['success_message'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update profile.";
        }

        redirect(base_url('admin/profile'));
    }

    public function notifications() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        require_once APP_PATH . '/models/Notification.php';
        $notificationModel = new Notification();
        $adminId = $_SESSION['user_id'] ?? 0;
        
        // Mark all as read if requested
        if (isset($_POST['mark_all_read']) && $_POST['mark_all_read'] == '1') {
            $notificationModel->markAllAsRead($adminId);
            redirect(base_url('admin/notifications'));
        }
        
        $notificationsRes = $notificationModel->getByUser($adminId);
        $notifications = [];
        if ($notificationsRes) {
            while ($row = $notificationsRes->fetch_assoc()) {
                $notifications[] = $row;
            }
        }
        
        $title = "Notifications - " . APP_NAME;
        require_once APP_PATH . '/views/admin/notifications.php'; // We might need to create this view
    }

    public function notificationsAjax() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        header('Content-Type: application/json');
        
        require_once APP_PATH . '/models/Notification.php';
        $notificationModel = new Notification();
        $adminId = $_SESSION['user_id'] ?? 0;
        
        $unreadCount = $notificationModel->getUnreadCount($adminId);
        $notificationsRes = $notificationModel->getByUser($adminId);
        
        $notifications = [];
        if ($notificationsRes) {
            $count = 0;
            while ($row = $notificationsRes->fetch_assoc()) {
                if ($count >= 10) break; // Only send top 10 for dropdown
                // Format time difference
                $time = date('M d, g:i A', strtotime($row['created_at']));
                $row['time'] = $time;
                $notifications[] = $row;
                $count++;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'unreadCount' => $unreadCount,
            'notifications' => $notifications
        ]);
        exit;
    }

    public function markNotificationRead() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        $id = $_POST['id'] ?? 0;
        if ($id) {
            require_once APP_PATH . '/models/Notification.php';
            $notificationModel = new Notification();
            $notificationModel->markAsRead($id);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }

}


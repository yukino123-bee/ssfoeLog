<?php
/**
 * RequestController - Handles client requests and programs
 */

class RequestController {

    public function landing() {
        require_once APP_PATH . '/models/Announcement.php';
        $announcementModel = new Announcement();
        $announcements = $announcementModel->getAll(5); // Fetch top 5 recent announcements
        
        $title = "Communifund Assistance System - Get Started";
        require_once APP_PATH . '/views/client/landing.php';
    }

    public function track() {
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        
        $identifier = '';
        $requests = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = $_POST['identifier'] ?? '';
            $identifier = is_string($input) ? strtoupper(trim($input)) : '';
            if (preg_match('/^[A-F0-9]{10}$/', $identifier)) {
                $requests = $requestModel->getPublicStatusByReferenceNumber($identifier);
            }
        } elseif (isset($_SESSION['track_reference_once']) && is_string($_SESSION['track_reference_once'])) {
            $identifier = strtoupper(trim($_SESSION['track_reference_once']));
            unset($_SESSION['track_reference_once']);
            if (preg_match('/^[A-F0-9]{10}$/', $identifier)) {
                $requests = $requestModel->getPublicStatusByReferenceNumber($identifier);
            }
        }
        
        $title = "Track Your Request";
        require_once APP_PATH . '/views/client/track_status.php';
    }

    public function viewDetails() {
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        
        $email = '';
        $reference = '';
        $category = '';
        $requests = [];
        $searched = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
            $reference = is_string($_POST['reference'] ?? null) ? strtoupper(trim($_POST['reference'])) : '';
            $category = is_string($_POST['category'] ?? null) ? trim($_POST['category']) : '';
            $allowedCategories = ['educational', 'medical', 'burial', 'employment', 'transportation'];
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)
                && preg_match('/^[A-F0-9]{10}$/', $reference)
                && in_array($category, $allowedCategories, true)) {
                $requests = $requestModel->getByEmailTypeAndReferenceWithDetails($email, $category, $reference);
                $searched = true;
                if (!empty($requests)) {
                    session_regenerate_id(true);
                }
                foreach ($requests as $matchedRequest) {
                    $this->authorizeRequestForSession((int) $matchedRequest['id']);
                }
            }
        }
        
        $title = "View Request Details";
        require_once APP_PATH . '/views/client/view_details.php';
    }

    public function educational() {
        $title = "Educational Assistance";
        $request_type = "educational";
        require_once APP_PATH . '/views/client/submit_request.php';
    }

    public function medical() {
        $title = "Medical Assistance";
        $request_type = "medical";
        require_once APP_PATH . '/views/client/submit_request.php';
    }

    public function burial() {
        $title = "Burial Assistance";
        $request_type = "burial";
        require_once APP_PATH . '/views/client/submit_request.php';
    }

    public function employment() {
        $title = "Employment Assistance";
        $request_type = "employment";
        require_once APP_PATH . '/views/client/submit_request.php';
    }

    public function transportation() {
        $title = "Transportation Assistance";
        $request_type = "transportation";
        require_once APP_PATH . '/views/client/submit_request.php';
    }

    public function submit() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect(base_url('client'));
            }

            require_once APP_PATH . '/models/Request.php';
            $requestModel = new Request();

            $firstname = is_string($_POST['firstname'] ?? null) ? trim($_POST['firstname']) : '';
            $middlename = is_string($_POST['middlename'] ?? null) ? trim($_POST['middlename']) : '';
            $lastname = is_string($_POST['lastname'] ?? null) ? trim($_POST['lastname']) : '';
            $fullname = trim($firstname . (empty($middlename) ? "" : " " . $middlename) . " " . $lastname);
            $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
            $request_type = $_POST['request_type'] ?? 'educational';

            $allowedRequestTypes = ['educational', 'medical', 'burial', 'employment', 'transportation'];
            if (!in_array($request_type, $allowedRequestTypes, true)) {
                $_SESSION['error_message'] = 'Invalid assistance program selected.';
                redirect(base_url('client'));
            }

            if ($requestModel->hasDuplicateActiveRequestByEmail($email, $request_type)) {
                $_SESSION['error_message'] = "You already have a pending or approved " . ucfirst($request_type) . " request. Please wait for it to be resolved before submitting a new one.";
                redirect(base_url('client/' . $request_type));
            }

            $details = $_POST;
            unset($details['firstname'], $details['middlename'], $details['lastname'], $details['email'], $details['request_type'], $details['csrf_token'], $details['dob']);

            $validation_errors = validate_input($_POST, [
                'firstname' => ['required' => true],
                'lastname' => ['required' => true],
                'email' => ['required' => true, 'email' => true],
                'request_type' => ['required' => true],
                'contact' => ['required' => true, 'min_length' => 11, 'max_length' => 11],
            ]);
            if ($validation_errors) {
                $_SESSION['error_message'] = implode(' ', $validation_errors);
                redirect(base_url('client/' . $request_type));
            }

            $uploadDir = ROOT_PATH . '/storage/uploads/requests/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0750, true);
            }
            $maxSize = 10 * 1024 * 1024; // 10MB per file
            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                'image/heic' => 'heic',
                'image/heif' => 'heif',
                'image/bmp' => 'bmp',
                'image/x-ms-bmp' => 'bmp',
            ];

            foreach ($_FILES as $key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    if (!is_uploaded_file($file['tmp_name']) || $file['size'] <= 0 || $file['size'] > $maxSize) {
                        $_SESSION['error_message'] = "File too large for $key.";
                        redirect(base_url('client/' . $request_type));
                    }
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                    if (!isset($allowedMimes[$mime])) {
                        $_SESSION['error_message'] = "Invalid file content for $key. Only images are allowed.";
                        redirect(base_url('client/' . $request_type));
                    }
                    $ext = $allowedMimes[$mime];
                    $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $key);
                    $newName = $request_type . '_' . bin2hex(random_bytes(16)) . '_' . $safeKey . '.' . $ext;
                    $targetPath = $uploadDir . $newName;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $details[$key . '_path'] = 'requests/' . $newName;
                    } else {
                        $_SESSION['error_message'] = "Failed to upload $key.";
                        redirect(base_url('client/' . $request_type));
                    }
                } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                    $errorMsg = "File upload error for $key.";
                    if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
                        $maxSizeStr = ini_get('upload_max_filesize');
                        $errorMsg = "The uploaded file for $key exceeds the maximum allowed size ($maxSizeStr). Please upload a smaller file.";
                    }
                    $_SESSION['error_message'] = $errorMsg;
                    redirect(base_url('client/' . $request_type));
                }
            }

            $reference_number = strtoupper(bin2hex(random_bytes(5)));

            $data = [
                'reference_number' => $reference_number,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'fullname' => $fullname,
                'email' => $email,
                'request_type' => $request_type,
                'details' => $details,
            ];

            if ($requestModel->create($data)) {
                $createdRequest = $requestModel->getByReferenceNumber($reference_number);
                if (!empty($createdRequest[0]['id'])) {
                    session_regenerate_id(true);
                    $this->authorizeRequestForSession((int) $createdRequest[0]['id']);
                }
                $_SESSION['success_message'] = "Your request has been submitted successfully! Your Reference Number is: " . $reference_number . ". Please save this number to track your request.";
                $_SESSION['track_reference_once'] = $reference_number;
                redirect(base_url('client/track'));
            }
            $_SESSION['error_message'] = "There was an error processing your request. Please try again.";
            redirect(base_url('client/' . $request_type));
        } catch (Throwable $e) {
            error_log("Error in RequestController::submit: " . $e->getMessage());
            $_SESSION['error_message'] = 'A system error occurred. Please try again later.';
            redirect(base_url('client/' . ($request_type ?? 'educational')));
        }
    }

    public function proof() {
        $requestId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$requestId) {
            http_response_code(400);
            exit('Request identifier is missing.');
        }

        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $request = $requestModel->getById((int) $requestId);
        $id = $request ? $request['id'] : 0;

        if (!$request) {
            $_SESSION['error_message'] = "Request not found.";
            redirect(base_url('client/track'));
        }

        if (!$this->canAccessRequest((int) $id)) {
            $_SESSION['error_message'] = 'Verify your email, category, and reference number before viewing the approval notice.';
            redirect(base_url('client/details'));
        }
        
        if ($request['status'] !== 'approved') {
            $_SESSION['error_message'] = "This application is currently in " . strtoupper($request['status']) . " status. You can only view and print the Proof of Approval once it has been fully APPROVED by the Communifund Assistance System administrator.";
            redirect(base_url('client/track'));
        }

        $title = "Proof of Approval - #" . str_pad($id, 6, '0', STR_PAD_LEFT);
        require_once APP_PATH . '/views/client/proof_of_approval.php';
    }

    public function document() {
        $requestId = filter_var($_GET['request_id'] ?? null, FILTER_VALIDATE_INT);
        $field = is_string($_GET['field'] ?? null) ? $_GET['field'] : '';
        if (!$requestId || !preg_match('/^[A-Za-z0-9_-]+_path$/', $field) || !$this->canAccessRequest((int) $requestId)) {
            http_response_code(403);
            exit('Document access denied.');
        }

        require_once APP_PATH . '/models/Request.php';
        $request = (new Request())->getById((int) $requestId);
        $details = json_decode($request['details'] ?? '{}', true);
        $storedPath = is_array($details) && is_string($details[$field] ?? null) ? $details[$field] : '';
        $filename = basename($storedPath);
        if ($filename === '' || $filename !== basename(str_replace('\\', '/', $storedPath))) {
            http_response_code(404);
            exit('Document not found.');
        }

        $candidates = [
            ROOT_PATH . '/storage/uploads/requests/' . $filename,
            ROOT_PATH . '/public/uploads/requests/' . $filename,
        ];
        $file = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                $file = $candidate;
                break;
            }
        }
        if ($file === null) {
            http_response_code(404);
            exit('Document not found.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($filename));
        header('Content-Length: ' . filesize($file));
        header('X-Content-Type-Options: nosniff');
        readfile($file);
        exit;
    }

    private function authorizeRequestForSession(int $requestId): void {
        $_SESSION['authorized_requests'][$requestId] = time() + 1800;
    }

    private function canAccessRequest(int $requestId): bool {
        if (($_SESSION['role'] ?? null) === ROLE_ADMIN && !empty($_SESSION['user_id'])) {
            return true;
        }
        $expiresAt = $_SESSION['authorized_requests'][$requestId] ?? 0;
        if (!is_int($expiresAt) || $expiresAt < time()) {
            unset($_SESSION['authorized_requests'][$requestId]);
            return false;
        }
        return true;
    }

    public function announcementsAjax() {
        header('Content-Type: application/json');
        
        require_once APP_PATH . '/models/Announcement.php';
        $announcementModel = new Announcement();
        $announcementsRes = $announcementModel->getAll(10); // Fetch top 10
        
        $announcements = [];
        $unreadCount = 0; // We can't really track unread for anonymous users, but we can show recent ones
        
        if ($announcementsRes) {
            foreach ($announcementsRes as $row) {
                if ($row['audience'] === 'All Beneficiaries') {
                    $time = date('M d, g:i A', strtotime($row['created_at']));
                    $row['time'] = $time;
                    $row['is_read'] = 1; // Mark read so they don't look unread, or 0 if we want them highlighted
                    $announcements[] = $row;
                }
            }
        }
        
        // Let's just say there are 0 "unread" badge notifications for anonymous, or we can highlight all of them.
        // We'll leave unreadCount 0 so the red badge doesn't stay permanently for anonymous users.
        
        echo json_encode([
            'status' => 'success',
            'unreadCount' => 0,
            'notifications' => $announcements
        ]);
        exit;
    }

    public function submitContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('client#contact'));
        }

        require_once APP_PATH . '/models/Inquiry.php';
        $inquiryModel = new Inquiry();

        $name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
        $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
        $subject = is_string($_POST['subject'] ?? null) ? trim($_POST['subject']) : '';
        $message = is_string($_POST['message'] ?? null) ? trim($_POST['message']) : '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === ''
            || strlen($name) > 150 || strlen($email) > 254 || strlen($subject) > 200 || strlen($message) > 5000) {
            $_SESSION['error_message'] = "Please fill in all required fields.";
            redirect(base_url('client#contact'));
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        if ($inquiryModel->create($data)) {
            $_SESSION['success_message'] = "Your message has been sent successfully. We will get back to you soon!";
        } else {
            $_SESSION['error_message'] = "Failed to send your message. Please try again later.";
        }

        redirect(base_url('client#contact'));
    }
}

<?php
/**
 * RequestController - Handles client requests and programs
 */

class RequestController {

    public function landing() {
        require_once APP_PATH . '/models/Announcement.php';
        $announcementModel = new Announcement();
        $announcements = $announcementModel->getAll(5); // Fetch top 5 recent announcements
        
        $title = "SSFO eLog - Get Started";
        require_once APP_PATH . '/views/client/landing.php';
    }

    public function track() {
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        
        $identifier = '';
        $requests = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['identifier'] ?? '');
            if (!empty($identifier)) {
                $requests = $requestModel->getByIdentifier($identifier);
            }
        } elseif (isset($_GET['identifier'])) {
            $identifier = trim($_GET['identifier']);
            if (!empty($identifier)) {
                $requests = $requestModel->getByIdentifier($identifier);
            }
        }
        
        $title = "Track Your Request";
        require_once APP_PATH . '/views/client/track_status.php';
    }

    public function viewDetails() {
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        
        $email = '';
        $category = '';
        $requests = [];
        $searched = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $category = trim($_POST['category'] ?? '');
            
            if (!empty($email) && !empty($category)) {
                $requests = $requestModel->getByEmailAndTypeWithDetails($email, $category);
                $searched = true;
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

            $firstname = trim($_POST['firstname'] ?? '');
            $middlename = trim($_POST['middlename'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $fullname = trim($firstname . (empty($middlename) ? "" : " " . $middlename) . " " . $lastname);
            $email = trim($_POST['email'] ?? '');
            $request_type = $_POST['request_type'] ?? 'educational';

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

            $uploadDir = ROOT_PATH . '/public/uploads/requests/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif', 'bmp'];
            $maxSize = 10 * 1024 * 1024; // 10MB per file

            foreach ($_FILES as $key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedTypes)) {
                        $_SESSION['error_message'] = "Invalid file type for $key. Only images are allowed.";
                        redirect(base_url('client/' . $request_type));
                    }
                    if ($file['size'] > $maxSize) {
                        $_SESSION['error_message'] = "File too large for $key.";
                        redirect(base_url('client/' . $request_type));
                    }
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                    $allowedMimes = [
                        'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp',
                        'image/gif', 'image/heic', 'image/heif', 'image/bmp', 'image/x-ms-bmp'
                    ];
                    $browserMime = strtolower($file['type']);
                    if (!in_array($mime, $allowedMimes) && !in_array($browserMime, $allowedMimes) && strpos($mime, 'image/') !== 0 && strpos($browserMime, 'image/') !== 0) {
                        $_SESSION['error_message'] = "Invalid file content for $key. Only images are allowed.";
                        redirect(base_url('client/' . $request_type));
                    }
                    $newName = $request_type . '_' . time() . '_' . $key . '.' . $ext;
                    $targetPath = $uploadDir . $newName;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $details[$key . '_path'] = 'uploads/requests/' . $newName;
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

            $reference_number = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));

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
                $_SESSION['success_message'] = "Your request has been submitted successfully! Your Reference Number is: " . $reference_number . ". Please save this number to track your request.";
                redirect(base_url('client/track?reference_number=' . $reference_number));
            }
            $_SESSION['error_message'] = "There was an error processing your request. Please try again.";
            redirect(base_url('client/' . $request_type));
        } catch (Throwable $e) {
            error_log("Error in RequestController::submit: " . $e->getMessage(), 3, ROOT_PATH . '/storage/logs/error.log');
            $_SESSION['error_message'] = "System Error: " . $e->getMessage();
            redirect(base_url('client/' . ($request_type ?? 'educational')));
        }
    }

    public function proof() {
        $ref = $_GET['ref'] ?? null;
        if (!$ref) {
            die("Reference Number is missing.");
        }

        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        $requests = $requestModel->getByReferenceNumber($ref);
        $request = !empty($requests) ? $requests[0] : null;
        $id = $request ? $request['id'] : 0;

        if (!$request) {
            $_SESSION['error_message'] = "Request not found.";
            redirect(base_url('client/track'));
        }
        
        if ($request['status'] !== 'approved') {
            $_SESSION['error_message'] = "This application is currently in " . strtoupper($request['status']) . " status. You can only view and print the Proof of Approval once it has been fully APPROVED by the SSFO administrator.";
            redirect(base_url('client/track?reference_number=' . urlencode($request['reference_number'] ?? '')));
        }

        $title = "Proof of Approval - #" . str_pad($id, 6, '0', STR_PAD_LEFT);
        require_once APP_PATH . '/views/client/proof_of_approval.php';
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

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
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

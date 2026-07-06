<?php
/**
 * RequestController - Handles client requests and programs
 */

class RequestController {

    public function landing() {
        $title = "SSFO eLog - Get Started";
        require_once APP_PATH . '/views/client/landing.php';
    }

    public function track() {
        require_once APP_PATH . '/models/Request.php';
        $requestModel = new Request();
        
        $reference_number = '';
        $requests = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reference_number = trim($_POST['reference_number'] ?? '');
            if (!empty($reference_number)) {
                $requests = $requestModel->getByReferenceNumber($reference_number);
            }
        } elseif (isset($_GET['reference_number'])) {
            $reference_number = trim($_GET['reference_number']);
            if (!empty($reference_number)) {
                $requests = $requestModel->getByReferenceNumber($reference_number);
            }
        }
        
        $title = "Track Your Request";
        require_once APP_PATH . '/views/client/track_status.php';
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
}

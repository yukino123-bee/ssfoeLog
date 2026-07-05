<?php
/**
 * Global Helper Functions
 */

/**
 * Redirect to a given URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Base URL helper
 */
function base_url($path = '') {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('\\', '/', dirname($script_name));
    if ($base_dir === '/') $base_dir = '';
    
    return '/' . ltrim(rtrim($base_dir, '/') . "/" . ltrim($path, '/'), '/');
}




/**
 * Asset URL helper
 */
function asset_url($path = '') {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate input data
 */
function validate_input($data, $rules) {
    $errors = [];
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            $errors[] = "$field is required.";
        }
        if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "$field must be a valid email.";
        }
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[] = "$field must be at least {$rule['min_length']} characters.";
        }
    }
    return $errors;
}

/**
 * Get status badge CSS classes
 */
function getStatusBadgeClass($status) {
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-900',
        'approved' => 'bg-emerald-100 text-emerald-900',
        'rejected' => 'bg-red-100 text-red-900',
        'completed' => 'bg-blue-100 text-blue-900',
    ];
    return $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get status icon
 */
function getStatusIcon($status) {
    $statusIcons = [
        'pending' => 'fa-clock',
        'approved' => 'fa-check-circle',
        'rejected' => 'fa-times-circle',
        'completed' => 'fa-check-double'
    ];
    return $statusIcons[$status] ?? 'fa-question-circle';
}

/**
 * Format date in consistent format
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format number with thousands separator
 */
function formatNumber($number) {
    return number_format($number);
}

/**
 * Maintenance flag: storage/maintenance.flag contents "1" when enabled (admin settings).
 */
function is_maintenance_mode() {
    $f = ROOT_PATH . '/storage/maintenance.flag';
    return file_exists($f) && trim((string) file_get_contents($f)) === '1';
}

/**
 * Block public/beneficiary area; admins and login still work.
 */
function maintenance_should_block_path($path) {
    if (!is_maintenance_mode()) {
        return false;
    }
    if ($path === '/login' || $path === '/logout' || $path === '/register') {
        return false;
    }
    if (strpos($path, '/admin') === 0) {
        return false;
    }
    return true;
}

/**
 * Beneficiary unlocked program applications (referral + PIN after admin approval).
 */
function client_has_program_apply_access() {
    return !empty($_SESSION['beneficiary_program_access']);
}

/**
 * Turn application detail JSON into labeled rows for admin UI (files vs text).
 *
 * @return array<int, array{label:string,value:string,type:string,href?:string}>
 */
function program_detail_display_rows($details_json) {
    $data = json_decode($details_json ?? '{}', true);
    if (!is_array($data)) {
        return [];
    }

    static $labelMap = [
        'firstname' => 'First Name',
        'middlename' => 'Middle Name',
        'lastname' => 'Last Name',
        'age' => 'Age',
        'sex' => 'Sex',
        'contact' => 'Contact number',
        'address' => 'Address',
        'school' => 'School',
        'schoolType' => 'School type',
        'enrollment' => 'Enrollment document',
        'registration' => 'Registration document',
        'indigency' => 'Indigency document',
        'schoolid' => 'School ID document',
        'validid' => 'Valid ID document',
        'grades' => 'Grades document',
        'statement' => 'Statement document',
        'employmentType' => 'Employment type',
        'patientName' => 'Patient name',
        'patientAge' => 'Patient age',
        'patientSex' => 'Patient sex',
        'medicalCertificate' => 'Medical certificate',
        'barangayIndigency' => 'Barangay indigency',
        'validId1' => 'Valid ID (1)',
        'validId2' => 'Valid ID (2)',
        'hospitalBill' => 'Hospital bill',
        'authorization' => 'Authorization',
        'letterRequest' => 'Letter of request',
        'socialCaseStudy' => 'Social case study',
        'deceasedName' => 'Deceased name',
        'deceasedAge' => 'Deceased age',
        'deceasedSex' => 'Deceased sex',
        'dateOfDeath' => 'Date of death',
        'placeOfDeath' => 'Place of death',
        'causeOfDeath' => 'Cause of death',
        'deathCertificate' => 'Death certificate',
        'pds' => 'PDS',
        'resume' => 'Résumé',
        'recommendation' => 'Recommendation',
        'endorsement' => 'Endorsement',
        'purpose' => 'Purpose',
        'destination' => 'Destination',
        'travelDate' => 'Travel date',
        'driverName' => 'Driver name',
        'driverContact' => 'Driver contact',
        'driverLicense' => 'Driver license',
        'assistance_type' => 'Assistance type',
    ];

    $rows = [];
    foreach ($data as $key => $value) {
        if ($value === '' || $value === null) {
            continue;
        }
        $isPath = is_string($key) && substr($key, -5) === '_path';
        if ($isPath) {
            $base = substr($key, 0, -5);
            $label = $labelMap[$base] ?? ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', str_replace('_', ' ', $base)));
            $file = is_string($value) ? $value : '';
            $rows[] = [
                'label' => trim($label) . ' (document)',
                'value' => $file,
                'type' => 'file',
                'href' => $file !== '' ? base_url($file) : '',
            ];
        } else {
            $lbl = $labelMap[$key] ?? ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', str_replace('_', ' ', $key)));
            $rows[] = [
                'label' => trim($lbl),
                'value' => is_scalar($value) ? (string) $value : json_encode($value),
                'type' => 'text',
            ];
        }
    }
    return $rows;
}

/**
 * Check if there are any active announcements
 */
function has_announcements() {
    $db = get_db_connection();
    $result = $db->query("SELECT id FROM announcements LIMIT 1");
    $has = $result && $result->num_rows > 0;
    $db->close();
    return $has;
}

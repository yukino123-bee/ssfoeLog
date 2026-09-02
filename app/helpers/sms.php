<?php
/**
 * SMS Helper Functions
 * Communifund Assistance System
 */

/**
 * Validate if a number is a valid Philippine mobile number
 * Valid formats: 09xxxxxxxxx (11 digits) or 639xxxxxxxxx / +639xxxxxxxxx (12 digits)
 */
function is_valid_ph_number($number) {
    if (empty($number)) {
        return false;
    }
    // Remove all non-numeric characters
    $clean = preg_replace('/[^0-9]/', '', (string)$number);

    // Matches 09xxxxxxxxx (11 digits) or 639xxxxxxxxx (12 digits)
    return (bool)preg_match('/^(09|639)\d{9}$/', $clean);
}

/**
 * Format number to Philippine standard (09xxxxxxxx)
 */
function format_ph_number($number) {
    if (empty($number)) {
        return '';
    }
    $clean = preg_replace('/[^0-9]/', '', (string)$number);
    
    // 639171234567 -> 09171234567
    if (strlen($clean) === 12 && substr($clean, 0, 3) === '639') {
        return '09' . substr($clean, 3);
    }
    
    return $clean;
}

/**
 * Check if SMS rate limit is exceeded for a recipient
 */
function is_sms_rate_limited($number) {
    $maxAttempts = defined('SMS_RATE_LIMIT_ATTEMPTS') ? SMS_RATE_LIMIT_ATTEMPTS : 3;
    $decayMinutes = defined('SMS_RATE_LIMIT_DECAY_MINUTES') ? SMS_RATE_LIMIT_DECAY_MINUTES : 10;
    
    $limitDir = defined('ROOT_PATH') ? ROOT_PATH . '/storage/rate_limits' : __DIR__ . '/../../storage/rate_limits';
    if (!is_dir($limitDir)) {
        @mkdir($limitDir, 0750, true);
    }
    
    $file = $limitDir . '/sms_' . md5($number) . '.json';
    if (!file_exists($file)) {
        return false;
    }
    
    $data = json_decode(@file_get_contents($file), true);
    if (!$data || !isset($data['attempts'], $data['first_attempt'])) {
        return false;
    }
    
    if ((time() - $data['first_attempt']) > ($decayMinutes * 60)) {
        @unlink($file);
        return false;
    }
    
    return $data['attempts'] >= $maxAttempts;
}

/**
 * Record an SMS attempt for rate limiting
 */
function record_sms_attempt($number) {
    $limitDir = defined('ROOT_PATH') ? ROOT_PATH . '/storage/rate_limits' : __DIR__ . '/../../storage/rate_limits';
    if (!is_dir($limitDir)) {
        @mkdir($limitDir, 0750, true);
    }
    
    $file = $limitDir . '/sms_' . md5($number) . '.json';
    $now = time();
    $data = ['attempts' => 1, 'first_attempt' => $now];
    
    if (file_exists($file)) {
        $existing = json_decode(@file_get_contents($file), true);
        if ($existing && isset($existing['first_attempt']) && ($now - $existing['first_attempt']) <= ((defined('SMS_RATE_LIMIT_DECAY_MINUTES') ? SMS_RATE_LIMIT_DECAY_MINUTES : 10) * 60)) {
            $data['attempts'] = ($existing['attempts'] ?? 0) + 1;
            $data['first_attempt'] = $existing['first_attempt'];
        }
    }
    
    @file_put_contents($file, json_encode($data));
}

/**
 * Send SMS using configured driver (log, unisms, semaphore)
 */
function send_sms($number, $message) {
    if (!defined('SMS_ENABLED') || !SMS_ENABLED) {
        error_log("SMS not sent: SMS_ENABLED is false.");
        return false;
    }

    if (!is_valid_ph_number($number)) {
        error_log("SMS not sent: Invalid Philippine mobile number ($number).");
        return false;
    }

    $formattedNumber = format_ph_number($number);
    $driver = defined('SMS_DRIVER') ? strtolower(SMS_DRIVER) : 'unisms';

    // Check Rate Limiting
    if (is_sms_rate_limited($formattedNumber)) {
        error_log("SMS not sent: Rate limit exceeded for recipient $formattedNumber.");
        return false;
    }

    // LOG Driver: Log SMS to file for local testing/development
    if ($driver === 'log') {
        $logDir = defined('ROOT_PATH') ? ROOT_PATH . '/storage/logs' : __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0750, true);
        }
        $logFile = $logDir . '/sms.log';
        $senderName = defined('SMS_SENDER_NAME') ? SMS_SENDER_NAME : 'UnisoftSMS';
        $logEntry = sprintf("[%s] TO: %s | SENDER: %s | MESSAGE: %s\n", date('Y-m-d H:i:s'), $formattedNumber, $senderName, $message);
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
        error_log("SMS logged to $logFile for $formattedNumber");
        record_sms_attempt($formattedNumber);
        return true;
    }

    if (!defined('SMS_API_KEY') || empty(SMS_API_KEY)) {
        error_log("SMS not sent: SMS_API_KEY is missing for driver $driver.");
        return false;
    }

    $sent = false;
    $response = '';
    $httpCode = 0;

    if ($driver === 'semaphore') {
        $url = "https://api.semaphore.co/api/v4/messages";
        $params = [
            'apikey'     => SMS_API_KEY,
            'number'     => $formattedNumber,
            'message'    => $message,
            'sendername' => defined('SMS_SENDER_NAME') ? SMS_SENDER_NAME : ''
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $sent = ($httpCode >= 200 && $httpCode < 300);
    } else {
        // Default: UniSMS API
        $url = "https://unismsapi.com/api/sms";
        $params = [
            'recipient' => $formattedNumber,
            'content'   => $message,
        ];
        if (defined('SMS_SENDER_NAME') && !empty(SMS_SENDER_NAME)) {
            $params['sender_id'] = SMS_SENDER_NAME;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, SMS_API_KEY . ":");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $sent = ($httpCode >= 200 && $httpCode < 300);
    }

    if ($sent) {
        record_sms_attempt($formattedNumber);
        return true;
    } else {
        error_log("SMS API Error ($driver): HTTP $httpCode - Response: $response");
        return false;
    }
}

/**
 * Notify client of status update via SMS
 */
function notify_client_status_sms($number, $fullname, $request_type, $status, $reference = '') {
    $statusLower = strtolower($status);
    
    if ($statusLower === 'approved') {
        return notify_client_approval_sms($number, $fullname, $request_type, $reference);
    } elseif ($statusLower === 'rejected') {
        return notify_client_rejection_sms($number, $fullname, $request_type, $reference);
    }

    $templatePath = defined('ROOT_PATH') ? ROOT_PATH . '/storage/sms_template.json' : __DIR__ . '/../../storage/sms_template.json';
    $message = "Good day {fullname}! Your request for {request_type} assistance (Ref: {ref}) status has been updated to {status}. Please track your status on the portal for details. - Communifund";

    if (file_exists($templatePath)) {
        $config = json_decode(file_get_contents($templatePath), true);
        if (!empty($config['update_template'])) {
            $message = $config['update_template'];
        }
    }

    $typeFormatted = ucfirst($request_type);
    $message = str_replace('{fullname}', $fullname, $message);
    $message = str_replace('{request_type}', $typeFormatted, $message);
    $message = str_replace('{status}', strtoupper($status), $message);
    $message = str_replace('{ref}', $reference ?: 'N/A', $message);
    $message = str_replace('{reference}', $reference ?: 'N/A', $message);

    return send_sms($number, $message);
}

/**
 * Notify client of approval via SMS
 */
function notify_client_approval_sms($number, $fullname, $request_type, $reference = '') {
    $templatePath = defined('ROOT_PATH') ? ROOT_PATH . '/storage/sms_template.json' : __DIR__ . '/../../storage/sms_template.json';
    $message = "Good day {fullname}! We are pleased to inform you that your request for {request_type} assistance (Ref: {ref}) has been APPROVED through Communifund Assistance System (CAS). Check portal for Notice of Approval. Thank you!";
    
    if (file_exists($templatePath)) {
        $config = json_decode(file_get_contents($templatePath), true);
        if (!empty($config['approval_template'])) {
            $message = $config['approval_template'];
        }
    }
    
    $typeFormatted = ucfirst($request_type);
    $message = str_replace('{fullname}', $fullname, $message);
    $message = str_replace('{request_type}', $typeFormatted, $message);
    $message = str_replace('{ref}', $reference ?: 'N/A', $message);
    $message = str_replace('{reference}', $reference ?: 'N/A', $message);
    
    return send_sms($number, $message);
}

/**
 * Notify client of rejection via SMS
 */
function notify_client_rejection_sms($number, $fullname, $request_type, $reference = '') {
    $templatePath = defined('ROOT_PATH') ? ROOT_PATH . '/storage/sms_template.json' : __DIR__ . '/../../storage/sms_template.json';
    $message = "Good day {fullname}. Regrettably, your request for {request_type} assistance (Ref: {ref}) was NOT APPROVED upon review. Please visit the Communifund office for further inquiries. - CAS";

    if (file_exists($templatePath)) {
        $config = json_decode(file_get_contents($templatePath), true);
        if (!empty($config['rejection_template'])) {
            $message = $config['rejection_template'];
        }
    }

    $typeFormatted = ucfirst($request_type);
    $message = str_replace('{fullname}', $fullname, $message);
    $message = str_replace('{request_type}', $typeFormatted, $message);
    $message = str_replace('{ref}', $reference ?: 'N/A', $message);
    $message = str_replace('{reference}', $reference ?: 'N/A', $message);

    return send_sms($number, $message);
}

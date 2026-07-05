<?php
/**
 * SMS Helper Functions
 */

/**
 * Validate if a number is a valid Philippine mobile number
 */
function is_valid_ph_number($number) {
    // Remove all non-numeric characters
    $number = preg_replace('/[^0-9]/', '', $number);

    // Matches: 09xxxxxxxxx (11 digits) or 639xxxxxxxxx (12 digits)
    if (preg_match('/^(09|\+639|639)\d{9}$/', $number)) {
        return true;
    }
    return false;
}

/**
 * Format number to Philippine standard (09xxxxxxxx)
 */
function format_ph_number($number) {
    $number = preg_replace('/[^0-9]/', '', $number);
    if (strlen($number) == 12 && substr($number, 0, 3) == '639') {
        return '09' . substr($number, 3);
    }
    if (strlen($number) == 13 && substr($number, 0, 4) == '+639') {
        return '09' . substr($number, 4);
    }
    return $number;
}

/**
 * Send SMS using UniSMS API
 */
function send_sms($number, $message) {
    if (!SMS_ENABLED || empty(SMS_API_KEY)) {
        error_log("SMS not sent: SMS_ENABLED is false or SMS_API_KEY is missing.");
        return false;
    }

    if (!is_valid_ph_number($number)) {
        error_log("SMS not sent: Invalid Philippine mobile number ($number).");
        return false;
    }

    $formattedNumber = format_ph_number($number);

    $url = "https://unismsapi.com/api/sms";
    $params = [
        'recipient' => $formattedNumber,
        'content' => $message,
    ];
    
    // We will not send sender_id by default as UniSMS rejects unregistered sender IDs
    // UniSMS will use the account's default sender ID automatically.
    /*
    if (defined('SMS_SENDER_NAME') && !empty(SMS_SENDER_NAME) && strtolower(SMS_SENDER_NAME) !== 'ssfo') {
        $params['sender_id'] = SMS_SENDER_NAME;
    }
    */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // UniSMS uses Basic Auth with Secret Key as username and empty password
    curl_setopt($ch, CURLOPT_USERPWD, SMS_API_KEY . ":");
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    // Optional: timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Disable SSL verification for local dev environments
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    } else {
        error_log("SMS API Error: HTTP $httpCode - Response: $response");
        return false;
    }
}

/**
 * Notify client of approval via SMS
 */
function notify_client_approval_sms($number, $fullname, $request_type) {
    $templatePath = ROOT_PATH . '/storage/sms_template.json';
    $message = "Good day {fullname}! We are pleased to inform you that your request for {request_type} assistance has been APPROVED by the Support Services Facilitators Office (SSFO). You may check your email or track your status on the portal for your Notice of Approval. Thank you!";
    
    if (file_exists($templatePath)) {
        $config = json_decode(file_get_contents($templatePath), true);
        if (!empty($config['approval_template'])) {
            $message = $config['approval_template'];
        }
    }
    
    $message = str_replace('{fullname}', $fullname, $message);
    $message = str_replace('{request_type}', $request_type, $message);
    
    return send_sms($number, $message);
}

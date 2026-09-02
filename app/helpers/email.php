<?php
/**
 * Email Helper Functions
 */

/**
 * Send a simple email
 */
function send_email($to, $subject, $message) {
    $headers = "From: " . APP_NAME . " <noreply@communifund.gov>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // @ suppresses mail() connection warnings so HTTP redirects still work; log instead
    $sent = @mail($to, $subject, $message, $headers);
    if (!$sent) {
        error_log('send_email: mail() failed for ' . $to . ' (check SMTP/php.ini or use a real mail relay)');
    }
    return $sent;
}

/**
 * Send status update notification to client
 */
function notify_client_status_update($email, $fullname, $request_type, $status, $reference_number) {
    $subject = "Communifund Assistance System - Request Status Update";

    $safeName = htmlspecialchars($fullname, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeType = htmlspecialchars($request_type, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeStatus = htmlspecialchars($status, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $message = "
    <html>
    <head>
        <title>Request Status Update</title>
    </head>
    <body>
        <h2>Dear {$safeName},</h2>
        <p>Your <strong>{$safeType}</strong> request status has been updated to: <strong>{$safeStatus}</strong></p>
        <p>You can check the details by visiting: <a href='" . base_url('client/track') . "'>Track Your Request</a> and entering your reference number.</p>
        <p>Thank you for using the Communifund Assistance System.</p>
        <br>
        <p>Best regards,<br>Communifund Assistance System Team</p>
    </body>
    </html>
    ";

    return send_email($email, $subject, $message);
}

/**
 * Send formal Notice of Approval
 */
function send_approval_notice($email, $fullname, $request_type, $request_id, $reference_number) {
    $subject = "Notice of Approval - Communifund Assistance System";
    $approval_id = "CAS-" . str_pad($request_id, 6, '0', STR_PAD_LEFT);
    $track_url = base_url('client/track');
    $details_url = base_url('client/details');
    $safeName = htmlspecialchars($fullname, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $safeType = htmlspecialchars(ucfirst($request_type), ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $message = "
    <html>
    <head>
        <style>
            body { font-family: sans-serif; line-height: 1.6; color: #333; }
            .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-top: 5px solid #d32f2f; }
            .header { text-align: center; margin-bottom: 30px; }
            .approval-id { font-size: 24px; font-weight: bold; color: #d32f2f; margin: 10px 0; }
            .footer { margin-top: 40px; font-size: 12px; color: #777; text-align: center; }
            .button { display: inline-block; padding: 12px 24px; background-color: #d32f2f; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>NOTICE OF APPROVAL</h1>
            </div>
            <p>Dear <strong>{$safeName}</strong>,</p>
            <p>We are pleased to inform you that your request for <strong>{$safeType} Assistance</strong> has been officially <strong>APPROVED</strong>.</p>
            
            <p>Your Approval Reference ID is:</p>
            <div class='approval-id'>{$approval_id}</div>

            <p>You may now download or print your official Proof of Approval by clicking the button below:</p>
            <p style='text-align: center;'>
                <a href='{$details_url}' class='button'>VERIFY AND DOWNLOAD PROOF OF APPROVAL</a>
            </p>

            <p>If you have any questions, please visit our office or track your request status online at: <a href='{$track_url}'>{$track_url}</a></p>
            
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " Communifund Assistance System</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return send_email($email, $subject, $message);
}

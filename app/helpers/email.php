<?php
/**
 * Email Helper Functions
 */

/**
 * Send a simple email
 */
function send_email($to, $subject, $message) {
    $headers = "From: " . APP_NAME . " <noreply@ssfo.com>\r\n";
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
    $subject = "SSFO eLog - Request Status Update";

    $message = "
    <html>
    <head>
        <title>Request Status Update</title>
    </head>
    <body>
        <h2>Dear {$fullname},</h2>
        <p>Your <strong>{$request_type}</strong> request status has been updated to: <strong>{$status}</strong></p>
        <p>You can check the details by visiting: <a href='" . base_url('client/track?reference_number=' . urlencode($reference_number)) . "'>Track Your Request</a></p>
        <p>Thank you for using SSFO eLog.</p>
        <br>
        <p>Best regards,<br>SSFO Team</p>
    </body>
    </html>
    ";

    return send_email($email, $subject, $message);
}

/**
 * Send formal Notice of Approval
 */
function send_approval_notice($email, $fullname, $request_type, $request_id, $reference_number) {
    $subject = "Notice of Approval - SSFO eLog";
    $approval_id = "SSFO-" . str_pad($request_id, 6, '0', STR_PAD_LEFT);
    $track_url = base_url('client/track?reference_number=' . urlencode($reference_number));
    $proof_url = base_url('client/proof?ref=' . urlencode($reference_number));

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
            <p>Dear <strong>{$fullname}</strong>,</p>
            <p>We are pleased to inform you that your request for <strong>" . ucfirst($request_type) . " Assistance</strong> has been officially <strong>APPROVED</strong>.</p>
            
            <p>Your Approval Reference ID is:</p>
            <div class='approval-id'>{$approval_id}</div>

            <p>You may now download or print your official Proof of Approval by clicking the button below:</p>
            <p style='text-align: center;'>
                <a href='{$proof_url}' class='button'>DOWNLOAD PROOF OF APPROVAL</a>
            </p>

            <p>If you have any questions, please visit our office or track your request status online at: <a href='{$track_url}'>{$track_url}</a></p>
            
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " SSFO eLog</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return send_email($email, $subject, $message);
}
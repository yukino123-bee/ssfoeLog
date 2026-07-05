<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; margin: 0; padding: 0; background: #f4f4f4; }
        .page { width: 210mm; min-height: 297mm; padding: 20mm; margin: 10mm auto; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 2px solid #d32f2f; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #d32f2f; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0; font-size: 14px; opacity: 0.8; }
        .content { line-height: 1.8; }
        .noa-title { text-align: center; font-size: 22px; font-weight: bold; text-decoration: underline; margin-bottom: 40px; }
        .details-box { margin: 30px 0; border: 1px solid #ddd; padding: 20px; background: #fafafa; }
        .details-row { display: flex; margin-bottom: 10px; }
        .details-label { width: 150px; font-weight: bold; }
        .approval-id { font-size: 32px; color: #d32f2f; font-weight: bold; text-align: center; margin: 40px 0; border: 2px dashed #d32f2f; padding: 20px; }
        .footer { margin-top: 100px; text-align: center; font-size: 14px; }
        .signature-area { display: flex; justify-content: space-around; margin-top: 80px; }
        .signature-line { width: 200px; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-weight: bold; }
        .print-btn { position: fixed; bottom: 20px; right: 20px; padding: 15px 30px; background: #d32f2f; color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        @media print {
            .print-btn { display: none; }
            body { background: white; }
            .page { margin: 0; box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>SSFO eLog Program</h1>
            <p>Social Services & Financial Office (SSFO)</p>
            <p>Provincial Government Building, Philippines</p>
        </div>

        <div class="content">
            <div class="noa-title">NOTICE OF APPROVAL</div>

            <p>Date: <?php echo date('F d, Y'); ?></p>

            <p>To: <strong><?php echo htmlspecialchars($request['fullname']); ?></strong></p>
            <p>Email: <?php echo htmlspecialchars($request['email']); ?></p>

            <p>Greetings,</p>
            <p>We are pleased to inform you that after a thorough review of your application and supporting documents, your request for <strong><?php echo ucfirst($request['request_type']); ?> Assistance</strong> has been officially <strong>APPROVED</strong>.</p>
            
            <p>Please present this notice and your valid ID at the SSFO window for further instructions regarding the disbursement or availment of your assistance.</p>

            <div class="details-box">
                <div class="details-row">
                    <span class="details-label">Program Type:</span>
                    <span><?php echo ucfirst($request['request_type']); ?> Assistance</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Date Submitted:</span>
                    <span><?php echo date('M d, Y', strtotime($request['created_at'])); ?></span>
                </div>
                <div class="details-row">
                    <span class="details-label">Control No:</span>
                    <span>#<?php echo str_pad($request['id'], 6, '0', STR_PAD_LEFT); ?></span>
                </div>
            </div>

            <p style="text-align: center; font-weight: bold;">Approval Reference ID:</p>
            <div class="approval-id">
                SSFO-<?php echo str_pad($request['id'], 6, '0', STR_PAD_LEFT); ?>
            </div>

            <div class="signature-area">
                <div>
                    <div class="signature-line">OFFICE ADMINISTRATOR</div>
                    <p style="font-size: 12px; text-align: center;">Verified and Signed</p>
                </div>
                <div>
                    <div class="signature-line">HEAD OF SSFO</div>
                    <p style="font-size: 12px; text-align: center;">Approved for Release</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>This document serves as official proof of approval for the specified assistance request.</p>
            <p>&copy; <?php echo date('Y'); ?> SSFO eLog. All rights reserved.</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> PRINT THIS NOTICE
    </button>
</body>
</html>

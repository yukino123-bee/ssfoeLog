<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

$outputDirectory = dirname(__DIR__) . '/reports';
if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0750, true) && !is_dir($outputDirectory)) {
    throw new RuntimeException('Unable to create report directory.');
}

$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(11);

$phpWord->addTitleStyle(1, ['bold' => true, 'size' => 18, 'color' => '1F2937'], ['alignment' => Jc::CENTER, 'spaceAfter' => 120]);
$phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'color' => '991B1B'], ['spaceBefore' => 240, 'spaceAfter' => 100]);
$phpWord->addTitleStyle(3, ['bold' => true, 'size' => 12, 'color' => '1F2937'], ['spaceBefore' => 160, 'spaceAfter' => 80]);
$phpWord->addParagraphStyle('Body', ['spaceAfter' => 100, 'lineHeight' => 1.15]);
// Use a plain paragraph with a Unicode bullet. PhpWord's indent measurements
// can be interpreted differently by LibreOffice and previously pushed the text
// outside the printable page area.
$phpWord->addParagraphStyle('Bullet', ['spaceAfter' => 60]);

$section = $phpWord->addSection([
    'marginTop' => 900,
    'marginBottom' => 900,
    'marginLeft' => 1000,
    'marginRight' => 1000,
]);

$section->addTitle('Communifund Assistance System Security Gap Analysis and Hardening Summary', 1);
$section->addText('Prepared: August 20, 2026', ['italic' => true, 'color' => '4B5563'], ['alignment' => Jc::CENTER, 'spaceAfter' => 240]);

$section->addTitle('Executive Summary', 2);
$section->addText(
    'The Communifund Assistance System source code and configuration were reviewed for weaknesses involving authentication, authorization, personal-data handling, file uploads, sessions, network communication, rate limiting, database access, and dependency security. The initial application-code risk was assessed as High. After hardening, the application-code risk is Medium. Operational risk remains High until historical database backups are removed safely from source-control history and any previously deployed default administrator account is disabled or rotated.',
    [],
    'Body'
);
$section->addText(
    'The changes substantially reduce the chance that an unauthorized person can obtain applicant names, email addresses, medical or identity documents, approval records, administrator credentials, or SMS API credentials. They also improve resistance to brute-force activity, session theft, concurrency failures, insecure deployments, and accidental exposure of database information.',
    [],
    'Body'
);

$section->addTitle('Security Improvements Completed', 2);

$improvements = [
    ['Default administrator removed', 'The source no longer creates or displays the known admin@communifund.local / admin123 credential. This prevents immediate administrative takeover when a database is initialized from the project files.'],
    ['Private document storage', 'New applicant documents are stored under storage/uploads/requests rather than the public web directory. Direct access to legacy public uploads is blocked by Apache rules and the PHP development router.'],
    ['Authorized document delivery', 'Documents are delivered through an application endpoint that checks whether the requester is an authenticated administrator or an applicant session that successfully verified the request. File names are selected from trusted request metadata, preventing arbitrary path access.'],
    ['Reduced anonymous tracking data', 'Reference-only tracking now returns only the application identifier, program type, status, and dates. It no longer exposes names, email addresses, detailed form answers, document paths, or administrator remarks.'],
    ['Stronger applicant verification', 'Viewing full request details requires the correct email address, category, and reference number. Successful verification creates a short-lived authorization lasting 30 minutes and rotates the session identifier.'],
    ['Protected approval notices', 'Proof-of-approval pages now require a previously verified session. Reference numbers were removed from tracking URLs and email links, reducing leakage through browser history, copied links, server logs, and referrer information.'],
    ['Secure SMS transport', 'TLS certificate and hostname verification are enabled for the SMS service. This prevents a network attacker from impersonating the provider and stealing the SMS API key or altering messages.'],
    ['Protected account changes', 'Changing an administrator email address or password requires the current password. New passwords must satisfy the configured complexity policy, and password changes rotate the session identifier.'],
    ['Record ownership checks', 'A notification can be marked as read only when it belongs to the logged-in administrator. This prevents one account from modifying another administrator’s notification records by guessing an ID.'],
    ['Trusted proxy controls', 'The application accepts forwarded HTTPS information only from explicitly configured proxy addresses. Attackers can no longer influence secure-cookie decisions by sending a forged forwarding header directly.'],
    ['Safer database configuration', 'The weak example database password was removed. A production deployment now refuses to connect when DB_PASS is empty, reducing the risk of silently running with missing or weak credentials.'],
    ['Concurrency-safe rate limiting', 'Rate-limit records are updated under an exclusive file lock and fail closed if storage is unavailable. Concurrent requests can no longer overwrite each other as easily to bypass request limits.'],
    ['Safer HTML email', 'Applicant-controlled names, request types, and statuses are encoded before being inserted into HTML email, reducing HTML injection and misleading email content.'],
];

foreach ($improvements as [$title, $description]) {
    $run = $section->addTextRun('Bullet');
    $run->addText('• ' . $title . ': ', ['bold' => true]);
    $run->addText($description);
}

$section->addTitle('How the Changes Prevent Data Theft', 2);
$dataProtections = [
    'Applicant files can no longer be retrieved merely by knowing or discovering a public upload URL. Access is checked on every download.',
    'Anonymous tracking reveals only the minimum information needed to communicate progress, limiting the value of a leaked reference number.',
    'Full personal and program-specific details require three matching pieces of information and a valid session.',
    'Short-lived request authorization limits the useful lifetime of an unattended or copied browser session.',
    'Session rotation after successful verification makes session-fixation attacks more difficult.',
    'Administrator credential changes require knowledge of the current password, reducing damage from a briefly unattended admin session.',
    'TLS verification protects API credentials and private notification content while they travel to the SMS provider.',
    'Database startup safeguards reduce accidental production use of blank or predictable credentials.',
    'Email and view encoding reduce the chance that stored applicant input can execute code in an administrator’s browser.',
];
foreach ($dataProtections as $item) {
    $section->addText('• ' . $item, [], 'Bullet');
}

$section->addTitle('How the Changes Prevent System Failure', 2);
$failureProtections = [
    'Atomic rate-limit updates prevent corrupted counters and inconsistent behavior when many requests arrive simultaneously.',
    'Fail-closed rate limiting returns a controlled temporary-unavailable response instead of silently disabling abuse protection.',
    'Upload size and MIME validation continue to reduce storage exhaustion and malicious-file risks.',
    'Private upload directories use restricted permissions and are created automatically during setup, reducing deployment errors.',
    'Production database configuration now fails clearly when a required password is absent instead of operating in an unintended insecure state.',
    'Prepared database statements, request-type allowlists, status allowlists, CSRF protection, and role middleware continue to reduce malformed or unauthorized database changes.',
    'Generic error pages prevent internal exception details from being exposed while application errors are recorded in server logs.',
    'Dependency and syntax checks reduce the likelihood of deployment failure caused by invalid PHP or known vulnerable packages.',
];
foreach ($failureProtections as $item) {
    $section->addText('• ' . $item, [], 'Bullet');
}

$section->addTitle('Remaining Risks and Required Actions', 2);

$section->addTitle('1. Database backups in Git — Critical operational action', 3);
$section->addText(
    'Six SQL backup files remain tracked under storage/backups. The application now ignores future backup files, but .gitignore does not erase files already committed or copied. These backups may contain user accounts, password hashes, application records, contact information, and document locations.',
    [],
    'Body'
);
$section->addText('Required action: preserve any legally required backup in an approved encrypted backup system, remove the SQL files from the repository and its history, rotate all credentials represented in them, review repository access, and assess whether the exposure triggers an incident-reporting or privacy-notification obligation.', [], 'Body');

$section->addTitle('2. Previously deployed default account — Critical operational action', 3);
$section->addText('Removing the account from the seeder does not change an existing database. Every deployed environment must be checked for admin@communifund.local. Disable or remove it, or immediately assign a unique random password and review its activity history.', [], 'Body');

$section->addTitle('3. Content Security Policy — Medium', 3);
$section->addText('The current interface still depends on inline scripts, event handlers, and styles, so the Content Security Policy permits unsafe-inline. A later frontend refactor should move inline code to static assets and introduce per-response nonces. This would provide stronger protection if an output-encoding defect is introduced in the future.', [], 'Body');

$section->addTitle('4. Administrator multi-factor authentication — Medium', 3);
$section->addText('Administrator accounts are still protected by only a password. TOTP or WebAuthn should be required, especially for report exports, document access, and credential changes.', [], 'Body');

$section->addTitle('5. Central session revocation — Medium', 3);
$section->addText('A password change rotates the current session but cannot invalidate sessions already active on other devices. A database-backed session registry or account security-version field should be added so all other sessions can be revoked after a credential change.', [], 'Body');

$section->addTitle('6. Data retention and encryption — Medium', 3);
$section->addText('The system processes identity, education, medical, burial, employment, and transportation-assistance records. Management should define retention periods, automate secure deletion, encrypt document and backup storage, restrict exports, and audit every document view and export.', [], 'Body');

$section->addTitle('7. Multi-server rate limiting — Medium', 3);
$section->addText('The improved file limiter is appropriate for a single application server. If the system is deployed across multiple servers, counters should move to a shared atomic store such as Redis.', [], 'Body');

$section->addTitle('Deployment Checklist', 2);
$checklist = [
    'Set APP_ENV=production and configure a unique DB_PASS.',
    'Set TRUSTED_PROXIES only to the actual reverse-proxy addresses; otherwise leave it empty.',
    'Serve only the public directory as the web root.',
    'For Nginx or another non-Apache server, explicitly deny /uploads/ even though new uploads are private.',
    'Set the local .env file permission to 0600 and upload/storage directories to the minimum required service-account permissions.',
    'Confirm that admin@communifund.local does not exist in the production database.',
    'Remove historical SQL backups from Git through an approved history-rewrite process and rotate exposed credentials.',
    'Test applicant verification, document downloads, approval proof, admin login, password changes, exports, SMS delivery, and rate limiting over production HTTPS.',
    'Enable centralized security logging and alerts for repeated login failures, bulk downloads, exports, and administrator credential changes.',
];
foreach ($checklist as $item) {
    $section->addText('☐ ' . $item, [], 'Bullet');
}

$section->addTitle('Verification Results', 2);
$section->addText('All PHP source files passed syntax validation. composer.json passed validation with only a non-security license metadata warning. Composer reported no known security advisories for the locked dependencies. Source diff validation passed, and targeted searches confirmed removal of the known default password disclosure, disabled SMS TLS verification, and reference-bearing tracking links.', [], 'Body');
$section->addText('A complete live browser and database integration test was not performed because the assessment environment did not permit binding a local test-server port. Production acceptance testing is therefore still required before deployment.', [], 'Body');

$section->addTitle('Conclusion', 2);
$section->addText(
    'The completed changes materially strengthen the Communifund Assistance System against unauthorized document access, personal-data disclosure, administrator takeover, network interception, rate-limit bypass, and insecure deployment defaults. The most important remaining work is operational: remove sensitive backups from repository history, rotate any exposed credentials, eliminate previously deployed default accounts, and establish formal privacy, retention, auditing, and incident-response procedures.',
    [],
    'Body'
);

$outputPath = $outputDirectory . '/Communifund_Assistance_System_Security_Gap_Analysis_and_Hardening_Summary.docx';
$phpWord->save($outputPath, 'Word2007');

$odtOutputPath = $outputDirectory . '/Communifund_Assistance_System_Security_Report_LibreOffice_v2.odt';
$phpWord->save($odtOutputPath, 'ODText');

echo $outputPath . PHP_EOL;
echo $odtOutputPath . PHP_EOL;

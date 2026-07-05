# SSFO Test Execution Summary Report

**Date**: 2026-07-04
**OS**: Windows
**PHP Version**: 8.5.0

## 1. Executive Summary
An execution of the automated verification/scratch tests for the Support Services Facilitators Office (SSFO) system was carried out. Since the project does not utilize a standard testing framework (e.g. PHPUnit or Pest), verification was conducted by executing the test files in the `scratch/` directory.

- **Total Scripts Run**: 8
- **Passes**: 6
- **Failures**: 2
- **Success Rate**: 75.0%

---

## 2. Test Execution Details

| Script Name | Purpose | Status | Details / Issues |
| :--- | :--- | :--- | :--- |
| `test_db.php` | Verifies database connection and queries the `users` table | **PASS** | Connected and retrieved 6 users successfully. |
| `check_tables.php` | Lists database tables | **FAIL** | Failed (Exit Code 255): Hardcoded credentials `'manok123'` caused an `Access denied` error. |
| `fix_passwords.php` | Updates all user passwords in database to `'admin123'` hash | **PASS** | Completed successfully, database updated. |
| `test_export.php` | Checks if request details contains path keys | **PASS** | Retrieved 8 requests successfully. |
| `test_export2.php` | Validates details JSON payload and disk paths | **PASS** | Verified 8 requests successfully. |
| `test_hash.php` | Verifies static password hash against string `'admin123'` | **FAIL** | Failed (Exit Code 0, Output `bool(false)`): The hardcoded hash does not match `'admin123'`. |
| `test_phpword.php` | Checks PhpWord image insertion | **PASS** | Successfully verified image addition to PhpWord section. |
| `test_word_gen.php` | Generates a Word document dynamically from DB requests | **PASS** | Executed successfully (no crash), printed "No files found." |

---

## 3. Discovered Bugs and System Discrepancies

### Bug 1: Hardcoded Database Credentials in `check_tables.php`
- **Location**: `scratch/check_tables.php` (Line 2)
- **Description**: The script hardcodes the password `'manok123'` for connection. This causes the script to fail when the actual database password set in `.env` is different (resulting in `mysqli_sql_exception: Access denied`).
- **Impact**: Prevents execution of the table check utility.

### Bug 2: Hash Verification Failure in `test_hash.php`
- **Location**: `scratch/test_hash.php` (Lines 2-3)
- **Description**: The hardcoded bcrypt hash (`$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWDeblLxW6G.KLym`) does not match the password `'admin123'`. As a result, the password verification fails.
- **Impact**: The test fails to verify password logic correctly.

### Bug 3: Database Column Mismatch (`Unknown column 'dob'`) in Form Submissions
- **Location**: `app/controllers/RequestController.php` (Line 156) & `app/models/Request.php` (Line 112)
- **Description**: In the client submission flow, the `dob` (Date of Birth) input is collected from the form but is not unset from the `$details` payload in `RequestController::submit()`. When `saveDetails()` in the `Request` model tries to insert the details dynamically into the program-specific detail tables (e.g. `req_educational`), it attempts to write to a non-existent `dob` column, triggering a database warning/error (`Unknown column 'dob' in 'field list'`).
- **Impact**: The detail table row is not created in the database for client-submitted requests, meaning program-specific data is not stored in detail tables, only in the master `requests` table JSON `details` field.

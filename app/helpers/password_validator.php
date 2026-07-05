<?php
/**
 * Password Validation Helper
 * Validates password complexity requirements
 */

/**
 * Validate password meets complexity requirements
 * 
 * Requirements:
 * - Minimum 8 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one digit
 * - At least one special character
 * 
 * @param string $password The password to validate
 * @return bool True if valid, false otherwise
 */
function is_strong_password($password) {
    $requirements = [
        'length' => strlen($password) >= 8,
        'uppercase' => preg_match('/[A-Z]/', $password),
        'lowercase' => preg_match('/[a-z]/', $password),
        'digit' => preg_match('/[0-9]/', $password),
        'special' => preg_match('/[!@#$%^&*()_\-+=\[\]{};\':"\\|,.<>\/?]/', $password),
    ];
    
    return array_reduce($requirements, function($carry, $item) {
        return $carry && $item;
    }, true);
}

/**
 * Validate password and return detailed errors
 * 
 * @param string $password The password to validate
 * @return array Array with 'valid' boolean and 'errors' array
 */
function validate_password($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter (A-Z)';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter (a-z)';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one digit (0-9)';
    }
    
    if (!preg_match('/[!@#$%^&*()_\-+=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $errors[] = 'Password must contain at least one special character (!@#$%^&*()_-+=[]{};\':"|,.<>/?)'
        ;
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
    ];
}

/**
 * Get password strength indicator
 * 
 * @param string $password The password to check
 * @return array Array with 'strength' (weak/medium/strong) and 'score' (0-100)
 */
function get_password_strength($password) {
    $score = 0;
    
    // Length scoring
    if (strlen($password) >= 8) $score += 10;
    if (strlen($password) >= 10) $score += 10;
    if (strlen($password) >= 12) $score += 10;
    if (strlen($password) >= 16) $score += 10;
    
    // Complexity scoring
    if (preg_match('/[a-z]/', $password)) $score += 10;
    if (preg_match('/[A-Z]/', $password)) $score += 10;
    if (preg_match('/[0-9]/', $password)) $score += 10;
    if (preg_match('/[!@#$%^&*()_\-+=\[\]{};\':"\\|,.<>\/?]/', $password)) $score += 10;
    
    // Character variety scoring
    if (preg_match('/[a-zA-Z0-9]/', $password) && preg_match('/[!@#$%^&*()_\-+=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $score += 10;
    }
    
    if ($score >= 100) $score = 100;
    
    if ($score < 40) {
        $strength = 'weak';
    } elseif ($score < 70) {
        $strength = 'medium';
    } else {
        $strength = 'strong';
    }
    
    return [
        'strength' => $strength,
        'score' => $score,
    ];
}

/**
 * Get password requirements as HTML
 * 
 * @return string HTML with password requirements
 */
function get_password_requirements() {
    return '
    <div class="password-requirements">
        <p class="font-semibold mb-2">Password Requirements:</p>
        <ul class="list-disc list-inside text-sm">
            <li>Minimum 8 characters</li>
            <li>At least one uppercase letter (A-Z)</li>
            <li>At least one lowercase letter (a-z)</li>
            <li>At least one digit (0-9)</li>
            <li>At least one special character (!@#$%^&* etc)</li>
        </ul>
    </div>';
}

/**
 * Check if password was previously used (check hashes)
 * 
 * @param string $plain_password Plain password
 * @param array $previous_hashes Array of previous password hashes
 * @return bool True if password matches a previous hash
 */
function password_was_used_before($plain_password, $previous_hashes = []) {
    foreach ($previous_hashes as $hash) {
        if (password_verify($plain_password, $hash)) {
            return true;
        }
    }
    return false;
}

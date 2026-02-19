<?php
require_once 'contact_schema.php';
require_once 'sanitize_helper.php';
require_once 'csv_handler.php';

/**
 * Field length limits for data validation
 */
$FIELD_LIMITS = [
    'first_name' => 50,
    'last_name' => 50,
    'company' => 100,
    'email' => 254,
    'phone' => 20,
    'address_1' => 100,
    'address_2' => 100,
    'city' => 50,
    'province' => 50,
    'postal_code' => 10,
    'country' => 50,
    'notes' => 1000,
    'tank_number' => 50,
];

/**
 * Required fields for a valid contact
 */
$REQUIRED_FIELDS = ['company', 'first_name', 'last_name'];

/**
 * ✅ ENHANCED: Validate contact data with comprehensive checks
 */
function validateContact(array $contact): array {
    global $FIELD_LIMITS, $REQUIRED_FIELDS;
    
    $schema = require __DIR__ . '/contact_schema.php';
    $errors = [];

    foreach ($schema as $field) {
        $value = isset($contact[$field]) ? trim($contact[$field]) : '';

        // 1. Required field check
        if (in_array($field, $REQUIRED_FIELDS)) {
            if (empty($value)) {
                $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                $errors[$field] = "$fieldLabel is required.";
                continue;  // Skip further validation for this field
            }
        }

        // Skip validation on empty optional fields
        if (empty($value)) {
            continue;
        }

        // 2. ✅ Length validation (ENHANCED)
        if (isset($FIELD_LIMITS[$field])) {
            if (strlen($value) > $FIELD_LIMITS[$field]) {
                $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                $errors[$field] = "$fieldLabel cannot exceed {$FIELD_LIMITS[$field]} characters "
                                 . "(you have " . strlen($value) . ").";
            }
        }

        // 3. ✅ Format validation (ENHANCED)
        switch ($field) {
            case 'email':
                // Validate email format AND check for common mistakes
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Invalid email address format.";
                }
                // Check for common typos
                else if (preg_match('/@(.+)\.c($|[^o])/', $value)) {
                    $errors[$field] = "Email domain appears invalid. Did you mean .com, .ca, etc?";
                }
                break;

            case 'phone':
                // Allow formats: +1-555-123-4567, 555.123.4567, (555) 123-4567, 5551234567, etc.
                if (!preg_match('/^[\d\s\-\+\(\)\.]{7,20}$/', $value)) {
                    $errors[$field] = "Invalid phone number format. Use digits, spaces, and dashes.";
                }
                break;

            case 'postal_code':
                // Allow Canadian (K1A 0B1) or US (12345) formats
                if (!preg_match('/^[A-Z0-9\s\-]{3,10}$/i', $value)) {
                    $errors[$field] = "Invalid postal code format.";
                }
                break;

            case 'country':
                // Allow alphanumeric countries - no special validation needed for now
                if (preg_match('/[<>"\']/', $value)) {
                    $errors[$field] = "Country name contains invalid characters.";
                }
                break;

            case 'website':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$field] = "Invalid website URL. Must start with http:// or https://";
                }
                break;
        }
    }

    return $errors;
}

/**
 * ✅ ENHANCED: Sanitize contact data before storage
 * Removes dangerous content and normalizes data
 */
function sanitizeContact(array $contact): array {
    $schema = require __DIR__ . '/contact_schema.php';
    $sanitized = [];

    foreach ($schema as $field) {
        $value = isset($contact[$field]) ? $contact[$field] : '';
        
        // Convert to string if not already
        $value = (string)$value;
        
        // 1. Trim whitespace
        $value = trim($value);
        
        // 2. Remove null bytes (security risk)
        $value = str_replace("\0", '', $value);
        
        // 3. Normalize line endings
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        
        // 4. For most fields, remove HTML tags
        if ($field !== 'notes') {
            $value = strip_tags($value);
        }
        
        // 5. For notes, allow some safe HTML but remove dangerous tags
        if ($field === 'notes') {
            // Remove script tags and event handlers
            $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', '', $value);
            $value = preg_replace('/on[a-z]+\s*=\s*["\']?[^"\'>\s]+["\']?/gi', '', $value);
        }
        
        // 6. For email, normalize to lowercase
        if ($field === 'email') {
            $value = strtolower($value);
        }
        
        // 7. For phone, normalize spacing/dashes
        if ($field === 'phone') {
            // Keep only digits, spaces, dashes, plus, parens, dots
            $value = preg_replace('/[^0-9\s\-\+\(\)\.]/', '', $value);
        }
        
        $sanitized[$field] = $value;
    }

    return $sanitized;
}

/**
 * ✅ NEW: Validate email uniqueness (for duplicate detection)
 */
require_once 'db_mysql.php';
function isEmailUnique($email, $excludeId = null): bool {
    if (empty($email)) {
        return true;  // Empty email is not a duplicate
    }
    $conn = get_mysql_connection();
    $email = strtolower(trim($email));
    if ($excludeId) {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM contacts WHERE LOWER(email) = ? AND id != ?');
        $stmt->bind_param('si', $email, $excludeId);
    } else {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM contacts WHERE LOWER(email) = ?');
        $stmt->bind_param('s', $email);
    }
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $count == 0;
}

/**
 * ✅ NEW: Comprehensive validation with error details
 */
function validateAndSanitizeContact(array $contact): array {
    // First, sanitize the input
    $contact = sanitizeContact($contact);
    
    // Then validate
    $errors = validateContact($contact);
    
    return [
        'contact' => $contact,
        'errors' => $errors,
        'isValid' => empty($errors),
    ];
}

?>

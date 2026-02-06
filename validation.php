<?php
function isValidEmail($email) {
    return empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidPhone($phone) {
    return empty($phone) || preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone);
}

function validateContact($row) {
    $errors = [];

    // Company is required
    if (empty(trim($row['company']))) {
        $errors['company'] = 'Company name is required.';
    }

    // Email format check (only if provided)
    if (!isValidEmail($row['email'])) {
        $errors['email'] = 'Invalid email format.';
    }

    // Phone format check (only if provided)
    if (!isValidPhone($row['phone'])) {
        $errors['phone'] = 'Invalid phone number.';
    }

    return $errors;
}
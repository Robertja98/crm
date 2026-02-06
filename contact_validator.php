<?php
require_once 'contact_schema.php';

function validateContact(array $contact): array {
    $schema = require __DIR__ . '/contact_schema.php';
    $requiredFields = ['company'];
    $errors = [];

    foreach ($schema as $field) {
        $value = trim($contact[$field] ?? '');

        // Required field check
        if (in_array($field, $requiredFields) && $value === '') {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            continue;
        }

        // Field-specific validation
        switch ($field) {
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Invalid email format.";
                }
                break;

            case 'phone':
                if ($value && !preg_match('/^\+?[0-9\s\-().]{7,}$/', $value)) {
                    $errors[$field] = "Invalid phone number.";
                }
                break;

            case 'website':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$field] = "Invalid website URL.";
                }
                break;

            // Add more field-specific rules here if needed
        }
    }

    return $errors;
}

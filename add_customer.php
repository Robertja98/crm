<?php
// DEBUG: Show all errors and log to /tmp/add_customer_php_error.log
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/add_customer_php_error.log');

include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once 'csv_handler.php';
require_once 'contact_validator.php';

$contactFile = 'contacts.csv';
$contactSchema = require __DIR__ . '/contact_schema.php';
$errors = [];

$timestamp = date('Y-m-d H:i:s');

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newContact = [
        'id' => uniqid(),
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'company' => trim($_POST['company'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'province' => trim($_POST['province'] ?? ''),
        'postal_code' => trim($_POST['postal_code'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'created_at' => $timestamp,
        'last_modified' => $timestamp,
        'is_customer' => 'yes'
    ];

    // Fill in any missing schema fields
    foreach ($contactSchema as $field) {
        if (!isset($newContact[$field])) {
            $newContact[$field] = '';
        }
    }

    // Validate contact
    $validationErrors = validateContact($newContact);
    if (!empty($validationErrors)) {
        foreach ($validationErrors as $field => $msg) {
            $errors[] = "$field: $msg";
        }
    }

    // Check for duplicate email
    if (!empty($newContact['email'])) {
        $contacts = readCSV($contactFile, $contactSchema);
        foreach ($contacts as $contact) {
            if ($contact['email'] === $newContact['email']) {
                $errors[] = 'A contact with this email already exists.';
                break;
            }
        }
    }

    if (empty($errors)) {
        $contacts = readCSV($contactFile, $contactSchema);
        if (!is_array($contacts)) {
            $contacts = [];
        }
        $contacts[] = $newContact;
        writeCSV($contactFile, $contacts, $contactSchema);

        // Redirect to the new contact
        header('Location: contact_view.php?id=' . urlencode($newContact['id']));
        exit;
    }
}
?>

<div class="container">
  <h2>Add New Customer</h2>

  <?php if (!empty($errors)): ?>
    <div style="background-color: #ffe6e6; border: 1px solid red; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
      <strong style="color:red;">❌ Validation Errors:</strong>
      <ul style="color:red; margin-top: 10px;">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)): ?>
    <div style="background-color: #e6ffe6; border: 1px solid green; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
      <strong style="color:green;">✅ Customer saved successfully! Redirecting...</strong>
    </div>
  <?php endif; ?>

  <form method="POST">
    <fieldset style="margin-bottom:20px;">
      <legend><strong>Customer Details</strong></legend>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px;">
        <div>
          <label for="first_name"><strong>First Name:</strong></label><br>
          <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        </div>
        <div>
          <label for="last_name"><strong>Last Name:</strong></label><br>
          <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>
        <div>
          <label for="company"><strong>Company:</strong></label><br>
          <input type="text" name="company" id="company" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
        </div>
        <div>
          <label for="email"><strong>Email:</strong></label><br>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div>
          <label for="phone"><strong>Phone:</strong></label><br>
          <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div>
          <label for="address"><strong>Address:</strong></label><br>
          <input type="text" name="address" id="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>
        <div>
          <label for="city"><strong>City:</strong></label><br>
          <input type="text" name="city" id="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
        </div>
        <div>
          <label for="province"><strong>Province:</strong></label><br>
          <input type="text" name="province" id="province" value="<?= htmlspecialchars($_POST['province'] ?? '') ?>">
        </div>
        <div>
          <label for="postal_code"><strong>Postal Code:</strong></label><br>
          <input type="text" name="postal_code" id="postal_code" value="<?= htmlspecialchars($_POST['postal_code'] ?? '') ?>">
        </div>
        <div>
          <label for="country"><strong>Country:</strong></label><br>
          <input type="text" name="country" id="country" value="<?= htmlspecialchars($_POST['country'] ?? '') ?>">
        </div>
        <div style="grid-column: 1 / -1;">
          <label for="notes"><strong>Notes:</strong></label><br>
          <textarea name="notes" id="notes" rows="3"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
        </div>
      </div>
    </fieldset>

    <div style="margin-top:20px;">
      <button type="submit" class="btn-outline">💾 Save Customer</button>
      <a href="customers_list.php" class="btn-outline">⬅ Back to Customer List</a>
    </div>
  </form>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>

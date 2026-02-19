<?php
$pageTitle = 'Import Contacts';
$currentPage = basename(__FILE__);
include_once 'layout_start.php';
require_once 'csv_handler.php';
require_once 'contact_validator.php';

$schema = require __DIR__ . '/contact_schema.php';

// Constants for import validation
define('MAX_BATCH_SIZE', 1000);
define('MAX_FILE_SIZE', 5242880); // 5MB

?>

<div class="container">
  <h2>Import Contacts</h2>

  <!-- Upload Form with CSRF Token -->
  <form method="POST" enctype="multipart/form-data" class="contact-form">
    <?php echo renderCSRFInput(); ?>
    <label for="csv_file">Upload CSV File:</label>
    <input type="file" name="csv_file" accept=".csv" required>
    <small style="display:block; margin-top:5px; color:#666;">Max size: 5MB, Max rows: <?php echo MAX_BATCH_SIZE; ?></small>

    <!-- Buttons aligned horizontally -->
    <div style="margin-top: 10px;">
      <button type="submit">Preview Import</button>
      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name']) && isset($_SESSION['import_valid']) && $_SESSION['import_valid'] === true): ?>
        <button type="button" class="btn-confirm" onclick="showConfirmModal()">Commit Import</button>
      <?php endif; ?>
    </div>
  </form>

  <?php
  // Initialize import validation status
  $_SESSION['import_valid'] = false;
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
      // CSRF protection
      if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
          showError('CSRF token validation failed. Please try again.');
      } elseif (!is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
          showError('Invalid file upload.');
      } elseif ($_FILES['csv_file']['size'] > MAX_FILE_SIZE) {
          showError('File size exceeds 5MB limit.');
      } else {
          $tmp = $_FILES['csv_file']['tmp_name'];
          
          // Read CSV and validate structure
          $rows = array_map('str_getcsv', file($tmp));
          if (empty($rows)) {
              showError('CSV file is empty.');
          } else {
              $header = array_map('trim', array_shift($rows));
              
              // Check batch size
              if (count($rows) > MAX_BATCH_SIZE) {
                  showError('Import contains ' . count($rows) . ' rows. Maximum allowed: ' . MAX_BATCH_SIZE . ' rows.');
              } else {
                  // Validate each contact
                  $preview = [];
                  $validation_errors = [];
                  $duplicate_emails = [];
                  $existing_emails = [];
                  $row_number = 2; // CSV rows start at 2 (after header)
                  
                  // Load existing emails for duplicate checking
                  if (file_exists('contacts.csv')) {
                      $existing_contacts = readCSV('contacts.csv', $schema);
                      $existing_emails = array_column($existing_contacts, 'email');
                  }
                  
                  foreach ($rows as $row) {
                      $contact = array_combine($header, $row);
                      
                      // Field existence check
                      foreach ($schema as $field) {
                          if (!isset($contact[$field])) {
                              $contact[$field] = '';
                          }
                      }
                      
                      // Sanitize contact data
                      $contact = sanitizeContact($contact);
                      $contact['id'] = uniqid();
                      
                      // Validate contact
                      $contact_errors = validateContact($contact);
                      if (!empty($contact_errors)) {
                          $validation_errors[$row_number] = $contact_errors;
                      }
                      
                      // Check for duplicate email
                      if (!empty($contact['email'])) {
                          $email_lower = strtolower($contact['email']);
                          if (in_array($email_lower, $existing_emails, true)) {
                              $duplicate_emails[$row_number] = $contact['email'];
                          } elseif (isset($duplicate_emails[$row_number])) {
                              // Email is duplicate within import itself
                              if (!in_array($email_lower, array_column($preview, 'email'), true)) {
                                  // First occurrence, add to preview
                                  $preview[] = $contact;
                              } else {
                                  // Duplicate within this import
                                  $duplicate_emails[$row_number] = $contact['email'];
                              }
                          } else {
                              $preview[] = $contact;
                          }
                      } else {
                          $preview[] = $contact;
                      }
                      
                      $row_number++;
                  }
                  
                  // Display validation summary
                  $total_rows = count($rows);
                  $valid_rows = count($preview) - count($validation_errors);
                  $error_count = count($validation_errors) + count($duplicate_emails);
                  
                  echo "<div style='margin-top: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 4px;'>";
                  echo "<h3>Import Summary</h3>";
                  echo "<p><strong>Total rows:</strong> " . $total_rows . "</p>";
                  echo "<p><strong>Valid rows:</strong> <span style='color: green;'>" . $valid_rows . "</span></p>";
                  echo "<p><strong>Issues found:</strong> <span style='color: " . ($error_count > 0 ? 'red' : 'green') . ";'>" . $error_count . "</span></p>";
                  
                  if (empty($validation_errors) && empty($duplicate_emails)) {
                      echo "<p style='color: green; font-weight: bold;'>✓ All contacts are valid and ready to import</p>";
                      $_SESSION['import_valid'] = true;
                  } else {
                      echo "<p style='color: orange; font-weight: bold;'>⚠ Please review issues below before importing</p>";
                  }
                  echo "</div>";
                  
                  // Display validation errors
                  if (!empty($validation_errors) || !empty($duplicate_emails)) {
                      echo "<div style='margin-top: 15px; padding: 15px; background-color: #ffe6e6; border-radius: 4px; border-left: 4px solid #cc0000;'>";
                      echo "<h3>Issues Found</h3>";
                      
                      if (!empty($validation_errors)) {
                          echo "<p><strong>Validation Errors:</strong></p>";
                          echo "<ul>";
                          foreach ($validation_errors as $row_num => $errors) {
                              echo "<li><strong>Row " . $row_num . ":</strong> " . implode(", ", $errors) . "</li>";
                          }
                          echo "</ul>";
                      }
                      
                      if (!empty($duplicate_emails)) {
                          echo "<p><strong>Duplicate Emails (already exist or repeated in import):</strong></p>";
                          echo "<ul>";
                          foreach ($duplicate_emails as $row_num => $email) {
                              echo "<li><strong>Row " . $row_num . ":</strong> " . htmlspecialchars($email) . "</li>";
                          }
                          echo "</ul>";
                      }
                      echo "</div>";
                  }
                  
                  // Display preview table only for valid contacts
                  if (!empty($preview)) {
                      echo "<h3 style='margin-top: 20px;'>Preview - Valid Contacts (" . count($preview) . ")</h3>";
                      echo "<form method='POST' action='commit_import.php' id='commitForm'>";
                      echo renderCSRFInput();
                      echo "<table class='spec-table' style='font-size: 12px;'><thead><tr>";
                      foreach ($schema as $col) {
                          echo "<th>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $col))) . "</th>";
                      }
                      echo "</tr></thead><tbody>";
                      foreach ($preview as $contact) {
                          echo "<tr>";
                          foreach ($schema as $col) {
                              $value = $contact[$col] ?? '';
                              echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . (strlen($value) > 50 ? '...' : '') . "</td>";
                          }
                          echo "</tr>";
                      }
                      echo "</tbody></table>";
                      echo "</form>";
                      
                      $_SESSION['import_preview'] = $preview;
                  }
              }
          }
      }
  }
  ?>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <h3>Confirm Import</h3>
    <p>This will permanently add these contacts to the system. Proceed?</p>
    <button onclick="document.getElementById('commitForm').submit()">Yes, Commit</button>
    <button onclick="hideConfirmModal()">Cancel</button>
  </div>
</div>

<!-- Modal Styles & Script -->
<style>
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;
}
.modal-box {
  background: #fff; padding: 20px 30px; border-radius: 8px; text-align: center;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
.modal-box button {
  margin: 10px; padding: 8px 16px; border: none; border-radius: 4px;
  background: #0077cc; color: #fff; cursor: pointer;
}
.modal-box button:hover {
  background: #005fa3;
}
</style>

<script>
function showConfirmModal() {
  document.getElementById('confirmModal').style.display = 'flex';
}
function hideConfirmModal() {
  document.getElementById('confirmModal').style.display = 'none';
}
</script>

<?php include_once 'layout_end.php'; ?>

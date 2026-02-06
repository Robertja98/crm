<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Import Contacts';
$currentPage = basename(__FILE__);
include_once 'layout_start.php';
include_once 'navbar.php';
require_once 'csv_handler.php';

$schema = require __DIR__ . '/contact_schema.php';
?>

<div class="container">
  <h2>Import Contacts</h2>

  <!-- Upload Form -->
  <form method="POST" enctype="multipart/form-data" class="contact-form">
    <label for="csv_file">Upload CSV File:</label>
    <input type="file" name="csv_file" accept=".csv" required>

    <!-- Buttons aligned horizontally -->
    <div style="margin-top: 10px;">
      <button type="submit">Preview Import</button>
      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])): ?>
        <button type="button" class="btn-confirm" onclick="showConfirmModal()">Commit Import</button>
      <?php endif; ?>
    </div>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
      $tmp = $_FILES['csv_file']['tmp_name'];
      $rows = array_map('str_getcsv', file($tmp));
      $header = array_map('trim', array_shift($rows));
      $preview = [];

      foreach ($rows as $row) {
          $contact = array_combine($header, $row);
          $contact['id'] = uniqid();

          foreach ($schema as $field) {
              if (!isset($contact[$field])) {
                  $contact[$field] = '';
              }
          }

          $preview[] = $contact;
      }

      $_SESSION['import_preview'] = $preview;

      echo "<h3>Preview Contacts</h3>";

      if (empty($preview)) {
          echo "<p style='color:red;'>No contacts to preview.</p>";
      } else {
          echo "<form method='POST' action='commit_import.php' id='commitForm'>";
          echo "<table class='spec-table'><thead><tr>";
          foreach ($schema as $col) {
              echo "<th>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $col))) . "</th>";
          }
          echo "</tr></thead><tbody>";
          foreach ($preview as $contact) {
              echo "<tr>";
              foreach ($schema as $col) {
                  echo "<td>" . htmlspecialchars($contact[$col] ?? '') . "</td>";
              }
              echo "</tr>";
          }
          echo "</tbody></table>";
          echo "</form>"; // ✅ Form ends here, submit triggered by modal
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

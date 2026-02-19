<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
require_once 'layout_start.php';
require_once 'equipment_mysql.php';
$schema = require __DIR__ . '/equipment_schema.php';

// Helper: Fetch equipment by ID
function fetch_equipment_by_id($id, $schema) {
  $conn = get_mysql_connection();
  $fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
  $stmt = $conn->prepare("SELECT $fields FROM equipment WHERE equipment_id = ?");
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result ? $result->fetch_assoc() : null;
  $stmt->close();
  $conn->close();
  return $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $conn = get_mysql_connection();
  $fields = [];
  $values = [];
  $types = '';
  $errors = [];
  foreach ($schema as $field) {
    $fields[] = '`' . $field . '`';
    $val = $_POST[$field] ?? null;
    // Handle INT fields
    if (in_array($field, ['customer_id', 'contact_id', 'contract_id', 'regeneration_id'])) {
      $val = ($val === '' || !is_numeric($val)) ? null : (int)$val;
      $types .= 'i';
    }
    // Handle DECIMAL/DOUBLE fields
    elseif (in_array($field, ['purchase_value', 'tank_size'])) {
      $val = ($val === '' || !is_numeric($val)) ? null : (float)$val;
      $types .= 'd';
    }
    // Handle DATE fields
    elseif (in_array($field, ['install_date', 'purchase_date', 'last_service_date', 'next_service_date', 'warranty_expiry', 'created_date', 'modified_date'])) {
      $val = trim($val ?? '');
      $val = ($val === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) ? null : $val;
      $types .= 's';
    }
    else {
      $types .= 's';
    }
    $values[] = $val;
  }
  // Validate equipment_id
  $equipmentIdIdx = array_search('equipment_id', $schema);
  $equipmentId = $values[$equipmentIdIdx];
  if ($equipmentId === null || $equipmentId === '') {
    $errors[] = 'Equipment ID is required and cannot be empty.';
  } else {
    // Check for duplicate if inserting
    if (empty($_POST['edit_mode'])) {
      $stmtCheck = $conn->prepare('SELECT COUNT(*) FROM equipment WHERE equipment_id = ?');
      $stmtCheck->bind_param('s', $equipmentId);
      $stmtCheck->execute();
      $stmtCheck->bind_result($count);
      $stmtCheck->fetch();
      $stmtCheck->close();
      if ($count > 0) {
        $errors[] = 'Equipment ID already exists. Please use a unique ID.';
      }
    }
  }
  if (empty($errors)) {
    if (!empty($_POST['equipment_id']) && isset($_POST['edit_mode'])) {
      // Update existing
      $set = [];
      for ($j = 1; $j < count($fields); $j++) {
        $set[] = $fields[$j] . ' = ?';
      }
      $sql = "UPDATE equipment SET " . implode(', ', $set) . " WHERE equipment_id = ?";
      $updateValues = array_slice($values, 1);
      $updateValues[] = $_POST['equipment_id'];
      $updateTypes = str_repeat('s', count($updateValues));
      $stmt = $conn->prepare($sql);
      $stmt->bind_param($updateTypes, ...$updateValues);
      $stmt->execute();
      $stmt->close();
    } else {
      // Insert new
      $fieldsStr = implode(',', $fields);
      $placeholders = implode(',', array_fill(0, count($fields), '?'));
      $sql = "INSERT INTO equipment ($fieldsStr) VALUES ($placeholders)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param($types, ...$values);
      $stmt->execute();
      $stmt->close();
    }
    $conn->close();

    // After insert/update, redirect to equipment list
    if (ob_get_length()) {
      ob_end_clean();
    }
    header('Location: equipment_list.php');
    exit;
  } else {
    // Show errors above the form
    echo '<div style="color:red; margin:10px 0;"><b>Error:</b><ul>';
    foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>';
    echo '</ul></div>';
  }
}

// Debug Section removed

// If editing, load existing data
$equipment = null;
$edit_mode = false;
if (isset($_GET['id'])) {
    $equipment = fetch_equipment_by_id($_GET['id'], $schema);
    $edit_mode = true;
}
?>
<div class="container">
  <h2><?= $edit_mode ? 'Edit Equipment' : 'Add Equipment' ?></h2>
  <form method="POST">
    <?php foreach ($schema as $field): ?>
      <div class="form-group">
        <label for="<?= $field ?>"><?= ucwords(str_replace('_', ' ', $field)) ?></label>
        <input type="text" class="form-control" name="<?= $field ?>" id="<?= $field ?>" value="<?= htmlspecialchars($equipment[$field] ?? '') ?>" <?= $field === 'equipment_id' && $edit_mode ? 'readonly' : '' ?> />
      </div>
    <?php endforeach; ?>
    <?php if ($edit_mode): ?>
      <input type="hidden" name="edit_mode" value="1" />
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="equipment_list.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<?php require_once 'layout_end.php'; ?>

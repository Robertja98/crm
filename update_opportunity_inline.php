<?php
require_once 'db_mysql.php';
require_once 'sanitize_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['error' => 'Invalid request']);
  exit;
}

$id = $_POST['id'] ?? null;
$field = $_POST['field'] ?? null;
$value = $_POST['value'] ?? null;

if (!$id || !$field) {
  echo json_encode(['error' => 'Missing parameters']);
  exit;
}

$allowedFields = ['contact_id', 'value', 'stage', 'probability', 'expected_close'];
if (!in_array($field, $allowedFields)) {
  echo json_encode(['error' => 'Invalid field']);
  exit;
}


$conn = get_mysql_connection();
$old_stmt = $conn->prepare("SELECT $field FROM opportunities WHERE id = ?");
$old_stmt->bind_param('i', $id);
$old_stmt->execute();
$old_stmt->bind_result($old_value);
$old_stmt->fetch();
$old_stmt->close();

$stmt = $conn->prepare("UPDATE opportunities SET $field = ? WHERE id = ?");
if (!$stmt) {
  echo json_encode(['error' => 'DB error']);
  exit;
}
$stmt->bind_param('si', $value, $id);
if ($stmt->execute()) {
  // Log edit
  require_once 'opportunity_edit_log.php';
  $user_id = $_SESSION['user_id'] ?? 'system';
  log_opportunity_edit($id, $field, $old_value, $value, $user_id);
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['error' => 'Update failed']);
}
$stmt->close();
$conn->close();

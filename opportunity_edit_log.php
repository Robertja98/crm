<?php
require_once 'db_mysql.php';

function opportunity_edit_log_table_exists(mysqli $conn): bool {
  $result = $conn->query("SHOW TABLES LIKE 'opportunity_edit_log'");
  if (!$result) {
    return false;
  }
  $exists = $result->num_rows > 0;
  $result->free();
  return $exists;
}

function log_opportunity_edit($opportunity_id, $field, $old_value, $new_value, $user_id) {
  $conn = get_mysql_connection();
  if (!opportunity_edit_log_table_exists($conn)) {
    $conn->close();
    return;
  }
  $stmt = $conn->prepare("INSERT INTO opportunity_edit_log (opportunity_id, field, old_value, new_value, user_id, edited_at) VALUES (?, ?, ?, ?, ?, NOW())");
  if (!$stmt) {
    $conn->close();
    return;
  }
  $stmt->bind_param('sssss', $opportunity_id, $field, $old_value, $new_value, $user_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

function fetch_opportunity_edits($opportunity_id) {
  $conn = get_mysql_connection();
  if (!opportunity_edit_log_table_exists($conn)) {
    $conn->close();
    return [];
  }
  $stmt = $conn->prepare("SELECT * FROM opportunity_edit_log WHERE opportunity_id = ? ORDER BY edited_at DESC");
  if (!$stmt) {
    $conn->close();
    return [];
  }
  $stmt->bind_param('s', $opportunity_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $rows = [];
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  $stmt->close();
  $conn->close();
  return $rows;
}

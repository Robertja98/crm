<?php
require_once 'db_mysql.php';

function log_opportunity_edit($opportunity_id, $field, $old_value, $new_value, $user_id) {
  $conn = get_mysql_connection();
  $stmt = $conn->prepare("INSERT INTO opportunity_edit_log (opportunity_id, field, old_value, new_value, user_id, edited_at) VALUES (?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param('issss', $opportunity_id, $field, $old_value, $new_value, $user_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

function fetch_opportunity_edits($opportunity_id) {
  $conn = get_mysql_connection();
  $stmt = $conn->prepare("SELECT * FROM opportunity_edit_log WHERE opportunity_id = ? ORDER BY edited_at DESC");
  $stmt->bind_param('i', $opportunity_id);
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

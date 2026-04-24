<?php
require_once __DIR__ . '/../db_mysql.php';

$conn = get_mysql_connection();

$sql = "CREATE TABLE IF NOT EXISTS `opportunity_edit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `opportunity_id` int NOT NULL,
  `field` varchar(64) NOT NULL,
  `old_value` text NULL,
  `new_value` text NULL,
  `user_id` varchar(128) NOT NULL,
  `edited_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_opportunity_edit_log_opportunity_id` (`opportunity_id`),
  KEY `idx_opportunity_edit_log_edited_at` (`edited_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    fwrite(STDERR, "Failed to create opportunity_edit_log table: " . $conn->error . PHP_EOL);
    $conn->close();
    exit(1);
}

echo "opportunity_edit_log table is ready" . PHP_EOL;
$conn->close();

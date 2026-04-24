CREATE TABLE IF NOT EXISTS `opportunity_edit_log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

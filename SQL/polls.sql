-- Advanced Polls System for TorrentBits 2025
CREATE TABLE IF NOT EXISTS `polls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `description` text,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` int(11) NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `status` enum('active','closed','archived') NOT NULL DEFAULT 'active',
  `allow_multiple` tinyint(1) NOT NULL DEFAULT 0,
  `allow_change_vote` tinyint(1) NOT NULL DEFAULT 0,
  `min_class_view` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `min_class_vote` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `min_class_create` tinyint(3) unsigned NOT NULL DEFAULT 4,
  `show_results_before_vote` tinyint(1) NOT NULL DEFAULT 0,
  `total_votes` int(10) unsigned NOT NULL DEFAULT 0,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(10) unsigned NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `option_order` int(10) unsigned NOT NULL DEFAULT 0,
  `vote_count` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `voted_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_user_option` (`poll_id`,`user_id`,`option_id`),
  KEY `poll_id` (`poll_id`),
  KEY `user_id` (`user_id`),
  KEY `option_id` (`option_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


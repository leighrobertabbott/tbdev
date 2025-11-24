-- Advanced Features Database Schema

-- User Achievements Table
CREATE TABLE IF NOT EXISTS `user_achievements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `achievement_id` varchar(50) NOT NULL,
  `awarded_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_achievement` (`user_id`, `achievement_id`),
  KEY `user_id` (`user_id`),
  KEY `achievement_id` (`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add achievement_points column to users table if it doesn't exist
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE, so we check first
-- This will be handled by the installer, but for manual SQL execution:
-- Check columns exist before running these:
-- ALTER TABLE `users` ADD COLUMN `achievement_points` int(10) unsigned NOT NULL DEFAULT '0' AFTER `reputation`;
-- ALTER TABLE `users` ADD COLUMN `two_factor_secret` varchar(32) NOT NULL DEFAULT '' AFTER `secret`;
-- ALTER TABLE `users` ADD COLUMN `two_factor_backup` varchar(255) NOT NULL DEFAULT '' AFTER `two_factor_secret`;
-- ALTER TABLE `users` ADD COLUMN `two_factor_enabled` enum('yes','no') NOT NULL DEFAULT 'no' AFTER `two_factor_backup`;

-- User Activity Log (for analytics)
CREATE TABLE IF NOT EXISTS `user_activity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved Searches
CREATE TABLE IF NOT EXISTS `saved_searches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `query` text NOT NULL,
  `filters` text,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Torrent Collections (user-created lists)
CREATE TABLE IF NOT EXISTS `collections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `is_public` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Collection Items
CREATE TABLE IF NOT EXISTS `collection_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(10) unsigned NOT NULL,
  `torrent_id` int(10) unsigned NOT NULL,
  `added_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection_torrent` (`collection_id`, `torrent_id`),
  KEY `collection_id` (`collection_id`),
  KEY `torrent_id` (`torrent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Follows (follow other users)
CREATE TABLE IF NOT EXISTS `user_follows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` int(10) unsigned NOT NULL,
  `following_id` int(10) unsigned NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `follower_following` (`follower_id`, `following_id`),
  KEY `follower_id` (`follower_id`),
  KEY `following_id` (`following_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Background Jobs Queue
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL DEFAULT 'default',
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reserved_at` int(11) unsigned DEFAULT NULL,
  `available_at` int(11) unsigned NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `queue` (`queue`),
  KEY `available_at` (`available_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed Jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


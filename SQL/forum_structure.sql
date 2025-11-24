-- Forum Sections (like "GENERAL SECTION", "OPEN DISCUSSION")
CREATE TABLE IF NOT EXISTS `forum_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `minclassread` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Update forums table to support sections and subforums
-- These will be handled by the installer's error handling for duplicate columns
ALTER TABLE `forums` ADD COLUMN IF NOT EXISTS `section_id` int(10) unsigned DEFAULT NULL AFTER `sort`;
ALTER TABLE `forums` ADD COLUMN IF NOT EXISTS `parent_id` int(10) unsigned DEFAULT NULL AFTER `section_id`;
ALTER TABLE `forums` ADD COLUMN IF NOT EXISTS `last_post_id` int(10) unsigned DEFAULT NULL AFTER `topiccount`;
ALTER TABLE `forums` ADD COLUMN IF NOT EXISTS `last_post_user` int(10) unsigned DEFAULT NULL AFTER `last_post_id`;
ALTER TABLE `forums` ADD COLUMN IF NOT EXISTS `last_post_time` int(11) DEFAULT NULL AFTER `last_post_user`;

-- Add indexes (will be ignored if they already exist)
ALTER TABLE `forums` ADD KEY IF NOT EXISTS `section_id` (`section_id`);
ALTER TABLE `forums` ADD KEY IF NOT EXISTS `parent_id` (`parent_id`);

-- Update topics table to use 'forum' instead of 'forumid' if needed
-- (checking if column exists and updating if necessary)


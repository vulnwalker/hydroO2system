-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `category` (`id`, `name`) VALUES
(1,	'WEb\r\n');

DROP TABLE IF EXISTS `master`;
CREATE TABLE `master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;


DROP TABLE IF EXISTS `master_tree`;
CREATE TABLE `master_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `record_left` int(11) DEFAULT NULL,
  `record_right` int(11) DEFAULT NULL,
  `record_depth` int(11) DEFAULT NULL,
  `record_ordering` int(11) DEFAULT NULL,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;


DROP TABLE IF EXISTS `portfolio`;
CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` text NOT NULL,
  `images` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `portfolio` (`id`, `project`, `images`) VALUES
(1,	'test',	'test.jpg');

DROP TABLE IF EXISTS `sys_modules`;
CREATE TABLE `sys_modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned DEFAULT '0',
  `namespace` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('APP','MODULE','COMPONENT','PLUGIN') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'MODULE',
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_namespace` (`namespace`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Modules';

INSERT INTO `sys_modules` (`id`, `id_parent`, `namespace`, `type`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(1,	0,	'App\\',	'APP',	'PUBLISH',	'2017-05-11 23:12:51',	0,	'2017-10-17 09:34:51',	0,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_modules_navigations`;
CREATE TABLE `sys_modules_navigations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned DEFAULT '0',
  `id_sys_module` int(10) unsigned DEFAULT NULL,
  `position` enum('TOOLBAR','SIDEBAR','NAVBAR','FOOTER') COLLATE utf8_unicode_ci DEFAULT 'SIDEBAR',
  `label` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `href` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attributes` text COLLATE utf8_unicode_ci,
  `settings` text COLLATE utf8_unicode_ci,
  `metadata` text COLLATE utf8_unicode_ci,
  `record_left` int(11) DEFAULT NULL,
  `record_right` int(11) DEFAULT NULL,
  `record_depth` int(11) DEFAULT NULL,
  `record_ordering` int(11) DEFAULT NULL,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_module` (`id_sys_module`) USING BTREE,
  CONSTRAINT `sys_modules_navigations_ibfk_1` FOREIGN KEY (`id_sys_module`) REFERENCES `sys_modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Modules Navigations';

INSERT INTO `sys_modules_navigations` (`id`, `id_parent`, `id_sys_module`, `position`, `label`, `description`, `href`, `attributes`, `settings`, `metadata`, `record_left`, `record_right`, `record_depth`, `record_ordering`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(1,	NULL,	1,	'SIDEBAR',	'Dashboard',	NULL,	'dashboard',	NULL,	'{\"heading\":\"Dashboard\",\"icon\":\"fa fa-dashboard\"}',	NULL,	1,	2,	0,	NULL,	'PUBLISH',	'2017-05-22 14:54:06',	NULL,	'2017-10-20 08:40:02',	NULL,	NULL,	NULL),
(2,	NULL,	1,	'SIDEBAR',	'Administrator',	NULL,	'#',	NULL,	'{\"heading\":\"Administrator\",\"icon\":\"fa fa-user\"}',	NULL,	3,	10,	0,	NULL,	'PUBLISH',	'2017-10-20 15:39:17',	NULL,	'2017-10-20 08:39:20',	NULL,	NULL,	NULL),
(3,	2,	1,	'SIDEBAR',	'Users',	NULL,	'#',	NULL,	'{\"heading\":\"Users\",\"icon\":\"fa fa-users\"}',	NULL,	4,	9,	1,	NULL,	'PUBLISH',	'2017-10-20 15:39:20',	NULL,	'2017-10-20 08:39:22',	NULL,	NULL,	NULL),
(4,	3,	1,	'SIDEBAR',	'Manage',	NULL,	'administrator/users/manage',	NULL,	NULL,	NULL,	5,	6,	2,	NULL,	'PUBLISH',	'2017-10-20 15:39:23',	NULL,	'2017-10-20 08:39:25',	NULL,	NULL,	NULL),
(5,	3,	1,	'SIDEBAR',	'Roles',	NULL,	'administrator/users/roles',	NULL,	NULL,	NULL,	7,	8,	2,	NULL,	'PUBLISH',	'2017-10-20 15:39:25',	NULL,	'2017-10-20 08:39:27',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_modules_settings`;
CREATE TABLE `sys_modules_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_module` int(10) unsigned DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_module` (`id_sys_module`) USING BTREE,
  CONSTRAINT `sys_modules_settings_ibfk_1` FOREIGN KEY (`id_sys_module`) REFERENCES `sys_modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Modules Settings';


DROP TABLE IF EXISTS `sys_modules_users_roles`;
CREATE TABLE `sys_modules_users_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_module` int(10) unsigned NOT NULL,
  `id_parent` int(10) unsigned DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_module` (`id_sys_module`) USING BTREE,
  KEY `idx_parent` (`id_parent`) USING BTREE,
  CONSTRAINT `sys_modules_users_roles_ibfk_1` FOREIGN KEY (`id_parent`) REFERENCES `sys_modules_users_roles` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `sys_modules_users_roles_ibfk_2` FOREIGN KEY (`id_sys_module`) REFERENCES `sys_modules` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Modules Users Roles';

INSERT INTO `sys_modules_users_roles` (`id`, `id_sys_module`, `id_parent`, `code`, `label`, `description`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(1,	1,	NULL,	'DEVELOPER',	'Developer',	'somebody with access to the site content management system with development features and all other features',	'PUBLISH',	'2017-05-09 11:50:39',	0,	'2017-05-09 18:54:12',	0,	NULL,	NULL),
(2,	1,	NULL,	'ADMINISTRATOR',	'Administrator',	'somebody with access to the site content management system with administrators features and all other features',	'PUBLISH',	'2017-05-09 11:51:30',	0,	'2017-09-19 05:59:57',	0,	NULL,	NULL),
(3,	1,	NULL,	'USER',	'User',	'somebody who has access to all the administration features within content management system',	'PUBLISH',	'2017-05-09 11:52:10',	0,	'2017-09-19 06:00:12',	0,	NULL,	NULL),
(4,	1,	3,	'EDITOR',	'Editor',	'somebody who can publish and manage posts including the posts of other users.',	'PUBLISH',	'2017-05-09 11:54:29',	0,	'2017-09-19 06:00:16',	0,	NULL,	NULL),
(5,	1,	3,	'AUTHOR',	'Author',	'somebody who can publish and manage their own posts.',	'PUBLISH',	'2017-05-09 11:54:29',	0,	'2017-09-19 06:00:18',	0,	NULL,	NULL),
(6,	1,	3,	'CONTRIBUTOR',	'Contributor',	'somebody who can write and manage their own posts but cannot publish them.',	'PUBLISH',	'2017-05-09 11:57:16',	0,	'2017-09-19 06:00:20',	0,	NULL,	NULL),
(7,	1,	3,	'SUBSCRIBER',	'Subscriber',	'somebody who can only manage their profile.',	'PUBLISH',	'2017-05-09 11:57:20',	0,	'2017-09-19 06:00:22',	0,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_modules_users_roles_access`;
CREATE TABLE `sys_modules_users_roles_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_module_user_role` int(10) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `segments` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permission` enum('GRANTED','DENIED') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'GRANTED',
  `privileges` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '11111111' COMMENT 'Users roles access privileges based-on c-r-u-d-i-e-p-s masking privilges',
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_module_user_role` (`id_sys_module_user_role`) USING BTREE,
  KEY `idx_segments` (`segments`) USING HASH,
  CONSTRAINT `sys_modules_users_roles_access_ibfk_1` FOREIGN KEY (`id_sys_module_user_role`) REFERENCES `sys_modules_users_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Modules Users Roles Access & Privileges';

INSERT INTO `sys_modules_users_roles_access` (`id`, `id_sys_module_user_role`, `label`, `segments`, `permission`, `privileges`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(1,	2,	'Dashboard',	'dashboard',	'GRANTED',	'11111110',	'PUBLISH',	'2017-09-18 17:12:51',	0,	'2017-10-17 09:45:01',	0,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_users`;
CREATE TABLE `sys_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `msisdn` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sso` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`) USING BTREE,
  UNIQUE KEY `idx_username` (`username`) USING BTREE,
  UNIQUE KEY `idx_msisdn` (`msisdn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Users Accounts';

INSERT INTO `sys_users` (`id`, `email`, `msisdn`, `username`, `password`, `pin`, `sso`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(2,	'developer@example.com',	'082117490099',	'developer',	'$2y$10$vYKenQGxxHbIxxP2yDFI6OYhzrs5kSZYvZ.OYF1wQcfFplPtfs7wa',	'$2y$10$JgTyhrpLUwUe88Jp40PpJeCcMjyHOLQtVJvOwOb3eLvMP1XPJXQGK',	'',	'PUBLISH',	NULL,	NULL,	'2017-11-15 06:22:13',	NULL,	NULL,	NULL),
(4,	'',	'',	'administrator',	'$2y$12$OunzxOXsSXDIcPxdN5/27eumoKqePtNHaIL9qO2ORs3XXJ7tlYohi',	'$2y$12$Att2qwucjX.dgYSagagl4e3VbInLSYpuir4Ts5kSk8qhnukQZVCNm',	'',	'PUBLISH',	NULL,	NULL,	'2017-12-11 15:09:26',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_users_profiles`;
CREATE TABLE `sys_users_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_user` int(10) unsigned NOT NULL,
  `name_first` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_middle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_last` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_display` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cover` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('MALE','FEMALE','UNDEFINED') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'UNDEFINED',
  `birthplace` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date NOT NULL,
  `marital` enum('SINGLE','MARRIED','DIVORCED','UNDEFINED') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'UNDEFINED',
  `religion` enum('HINDU','BUDDHA','MOSLEM','CHRISTIAN','CATHOLIC','UNDEFINED') COLLATE utf8_unicode_ci DEFAULT 'UNDEFINED',
  `biography` text COLLATE utf8_unicode_ci,
  `metadata` text COLLATE utf8_unicode_ci,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_user` (`id_sys_user`) USING BTREE,
  CONSTRAINT `sys_users_profiles_ibfk_1` FOREIGN KEY (`id_sys_user`) REFERENCES `sys_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO `sys_users_profiles` (`id`, `id_sys_user`, `name_first`, `name_middle`, `name_last`, `name_display`, `avatar`, `cover`, `gender`, `birthplace`, `birthday`, `marital`, `religion`, `biography`, `metadata`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(1,	2,	'John',	NULL,	'Doe',	'John Doe',	NULL,	NULL,	'UNDEFINED',	NULL,	'2018-01-16',	'UNDEFINED',	'UNDEFINED',	'',	'',	'PUBLISH',	'2017-09-18 08:33:19',	NULL,	'2018-01-16 09:14:00',	NULL,	NULL,	NULL),
(2,	0,	'Jane',	NULL,	'Doe',	'Jane Doe',	'avatar.jpg',	NULL,	'MALE',	NULL,	'2018-01-16',	'SINGLE',	'UNDEFINED',	NULL,	NULL,	'PUBLISH',	'2017-09-18 08:33:13',	NULL,	'2018-01-16 09:14:04',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_users_roles`;
CREATE TABLE `sys_users_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_module_user_role` int(10) unsigned DEFAULT NULL,
  `id_sys_user` int(10) unsigned DEFAULT NULL,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_module_user_role` (`id_sys_module_user_role`) USING BTREE,
  KEY `idx_id_sys_user` (`id_sys_user`) USING BTREE,
  CONSTRAINT `sys_users_roles_ibfk_1` FOREIGN KEY (`id_sys_module_user_role`) REFERENCES `sys_modules_users_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sys_users_roles_ibfk_2` FOREIGN KEY (`id_sys_user`) REFERENCES `sys_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Users Accounts Roles';

INSERT INTO `sys_users_roles` (`id`, `id_sys_module_user_role`, `id_sys_user`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(1,	1,	2,	'PUBLISH',	'2017-09-19 23:20:38',	NULL,	'2017-09-19 16:20:41',	NULL,	NULL,	NULL),
(2,	2,	0,	'PUBLISH',	'2017-09-19 23:20:42',	NULL,	'2017-09-19 16:20:45',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_users_roles_access`;
CREATE TABLE `sys_users_roles_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_user_role` int(10) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `segments` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permission` enum('GRANTED','DENIED') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'GRANTED',
  `privileges` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '11111111' COMMENT 'Users roles access privileges based-on c-r-u-d-i-e-p-s masking privilges',
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_segments` (`segments`) USING HASH,
  KEY `idx_id_sys_user_role` (`id_sys_user_role`) USING BTREE,
  CONSTRAINT `sys_users_roles_access_ibfk_1` FOREIGN KEY (`id_sys_user_role`) REFERENCES `sys_users_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='O2System Framework - System Modules Users Roles Access & Privileges';

INSERT INTO `sys_users_roles_access` (`id`, `id_sys_user_role`, `label`, `segments`, `permission`, `privileges`, `record_status`, `record_create_timestamp`, `record_create_user`, `record_update_timestamp`, `record_update_user`, `record_delete_timestamp`, `record_delete_user`) VALUES
(2,	1,	'Personal',	'personal',	'GRANTED',	'11111111',	'PUBLISH',	'2017-09-19 15:31:56',	0,	'2017-09-20 11:16:05',	0,	NULL,	NULL);

DROP TABLE IF EXISTS `sys_users_settings`;
CREATE TABLE `sys_users_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_user` int(10) unsigned NOT NULL,
  `id_sys_module` int(10) unsigned DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_sys_user` (`id_sys_user`) USING BTREE,
  KEY `idx_sys_module` (`id_sys_module`) USING BTREE,
  CONSTRAINT `sys_users_settings_ibfk_1` FOREIGN KEY (`id_sys_module`) REFERENCES `sys_modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sys_users_settings_ibfk_2` FOREIGN KEY (`id_sys_user`) REFERENCES `sys_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;


DROP TABLE IF EXISTS `sys_users_signatures`;
CREATE TABLE `sys_users_signatures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sys_user` int(10) unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `record_status` enum('DELETE','TRASH','DRAFT','UNPUBLISH','PUBLISH') COLLATE utf8_unicode_ci DEFAULT 'PUBLISH',
  `record_create_timestamp` datetime DEFAULT NULL,
  `record_create_user` int(10) unsigned DEFAULT NULL,
  `record_update_timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `record_update_user` int(10) unsigned DEFAULT NULL,
  `record_delete_timestamp` datetime DEFAULT NULL,
  `record_delete_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`) USING BTREE,
  KEY `idx_id_sys_user` (`id_sys_user`) USING BTREE,
  CONSTRAINT `fk_sys_users_credentials-sys_users` FOREIGN KEY (`id_sys_user`) REFERENCES `sys_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;


-- 2018-06-25 02:35:00
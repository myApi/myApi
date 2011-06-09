CREATE TABLE IF NOT EXISTS `#__myapi_users` (
	`userId` int(255) NOT NULL auto_increment,
	`uid` bigint(255) unsigned NOT NULL,
	`update_status` int(1) NOT NULL,
	`status_text` text NOT NULL,
	`access_token` varchar(255) NOT NULL,
	`avatar` varchar(255) default NULL,
	PRIMARY KEY  (`userId`),
	UNIQUE KEY `userId` (`userId`),
	UNIQUE KEY `uid` (`uid`)
);
CREATE TABLE IF NOT EXISTS `#__myapi_pages` (
	`pageId` bigint(255) unsigned NOT NULL DEFAULT '0',
	`access_token` varchar(255) DEFAULT NULL,
	`name` varchar(255) DEFAULT NULL,
	`link` varchar(255) DEFAULT NULL,
	`category` varchar(255) DEFAULT NULL,
	`owner` bigint(255) unsigned DEFAULT NULL,
	PRIMARY KEY (`pageId`),
	FULLTEXT KEY `name` (`name`)
);
CREATE TABLE IF NOT EXISTS `#__myapi_comment_mail` (
	`href` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`href`),
	UNIQUE KEY `href` (`href`)
);
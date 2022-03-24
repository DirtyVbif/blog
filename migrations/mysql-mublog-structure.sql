DROP TABLE IF EXISTS `entities_comments`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `users_status_access_levels`;
DROP TABLE IF EXISTS `users_sessions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `users_statuses`;
DROP TABLE IF EXISTS `log`;
DROP TABLE IF EXISTS `entities_skill_data`;
DROP TABLE IF EXISTS `entities_feedback_data`;
DROP TABLE IF EXISTS `entities_article_data`;
DROP TABLE IF EXISTS `entities`;
DROP TABLE IF EXISTS `entities_types`;
DROP TABLE IF EXISTS `access_levels`;

-- Дамп структуры для таблица mublog.access_levels
CREATE TABLE `access_levels` (
  `alid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'User access level unique id',
  `label` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Description of access level',
  PRIMARY KEY (`alid`) USING BTREE,
  UNIQUE KEY `access` (`label`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_types
CREATE TABLE `entities_types` (
  `etid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity type unique id',
  `name` char(12) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Entity type name',
  PRIMARY KEY (`etid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities
CREATE TABLE `entities` (
  `eid` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity unique id',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Entity create time',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Entity update time',
  `etid` tinyint unsigned NOT NULL COMMENT 'Entity type id',
  PRIMARY KEY (`eid`),
  KEY `type_id` (`etid`),
  CONSTRAINT `fk_entity_type_id` FOREIGN KEY (`etid`) REFERENCES `entities_types` (`etid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_article_data
CREATE TABLE `entities_article_data` (
  `eid` smallint unsigned NOT NULL COMMENT 'entity unique id',
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `summary` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alias` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'Published status',
  `preview_src` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '/images/article-preview-default.webp',
  `preview_alt` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'article preview image',
  `author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'mublog.site',
  `views` smallint unsigned NOT NULL DEFAULT '1' COMMENT 'Number of article views',
  `rating` smallint NOT NULL DEFAULT '1' COMMENT 'Article voting rating',
  PRIMARY KEY (`eid`) USING BTREE,
  UNIQUE KEY `alias` (`alias`) USING BTREE,
  CONSTRAINT `fk_article_eid` FOREIGN KEY (`eid`) REFERENCES `entities` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_feedback_data
CREATE TABLE `entities_feedback_data` (
  `eid` smallint unsigned NOT NULL COMMENT 'entity unique id',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `headers` json NOT NULL,
  `result` tinyint unsigned NOT NULL DEFAULT '0',
  `ip` varchar(45) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`eid`) USING BTREE,
  CONSTRAINT `fk_feedback_eid` FOREIGN KEY (`eid`) REFERENCES `entities` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_skill_data
CREATE TABLE `entities_skill_data` (
  `eid` smallint unsigned NOT NULL,
  `title` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `icon_src` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `icon_alt` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`eid`),
  CONSTRAINT `fk_skill_entity_id` FOREIGN KEY (`eid`) REFERENCES `entities` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.log
CREATE TABLE `log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `output` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.users_statuses
CREATE TABLE `users_statuses` (
  `usid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'User status unique id',
  `status` char(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status parameter',
  `label` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status readable name',
  PRIMARY KEY (`usid`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.users
CREATE TABLE `users` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'User unique id',
  `mail` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User mail adress as login',
  `pwhash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Users'' password hash',
  `nickname` char(32) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User nickname',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usid` tinyint unsigned DEFAULT '2' COMMENT 'Users'' status id',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `fk_user_status_id` (`usid`),
  CONSTRAINT `fk_user_status_id` FOREIGN KEY (`usid`) REFERENCES `users_statuses` (`usid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.users_sessions
CREATE TABLE `users_sessions` (
  `sesid` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL COMMENT 'User unique id',
  `token` char(32) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User session token',
  `agent_hash` char(32) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User agent md5 hash',
  `browser` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `platform` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` char(45) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`sesid`),
  UNIQUE KEY `token` (`token`),
  KEY `uid` (`uid`),
  KEY `agent_hash` (`agent_hash`),
  CONSTRAINT `fk_session_user_id` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.users_status_access_levels
CREATE TABLE `users_status_access_levels` (
  `alid` tinyint unsigned NOT NULL COMMENT 'Access level id',
  `usid` tinyint unsigned NOT NULL COMMENT 'User status id',
  PRIMARY KEY (`alid`,`usid`),
  KEY `fk_accsess_level_status_id` (`usid`),
  CONSTRAINT `fk_access_level_id` FOREIGN KEY (`alid`) REFERENCES `access_levels` (`alid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_accsess_level_status_id` FOREIGN KEY (`usid`) REFERENCES `users_statuses` (`usid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.comments
CREATE TABLE `comments` (
  `cid` int unsigned NOT NULL AUTO_INCREMENT,
  `pid` int unsigned DEFAULT NULL,
  `uid` int unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`cid`),
  KEY `fk_comment_parent_id` (`pid`),
  KEY `fk_comment_user_id` (`uid`),
  CONSTRAINT `fk_comment_parent_id` FOREIGN KEY (`pid`) REFERENCES `comments` (`cid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_comment_user_id` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_comments
CREATE TABLE `entities_comments` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `eid` smallint unsigned NOT NULL COMMENT 'entity  unique id',
  `cid` int unsigned NOT NULL COMMENT 'comment unique id',
  `deleted` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'is comment deleted',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entity_comment` (`eid`,`cid`),
  KEY `fk_entity_comment_id` (`cid`),
  CONSTRAINT `fk_entity_comment_id` FOREIGN KEY (`cid`) REFERENCES `comments` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_entity_id` FOREIGN KEY (`eid`) REFERENCES `entities` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

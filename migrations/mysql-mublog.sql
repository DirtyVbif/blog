-- Дамп структуры для таблица mublog.access_levels
DROP TABLE IF EXISTS `access_levels`;
CREATE TABLE IF NOT EXISTS `access_levels` (
  `alid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'User access level unique id',
  `label` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Description of access level',
  PRIMARY KEY (`alid`) USING BTREE,
  UNIQUE KEY `access` (`label`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.access_levels: ~5 rows (приблизительно)
INSERT INTO `access_levels` (`alid`, `label`) VALUES
	(2, 'All users'),
	(4, 'For admins and webmasters'),
	(1, 'For guests and anonymous users'),
	(3, 'For registered users'),
	(5, 'Only for webmasters');

-- Дамп структуры для таблица mublog.comments
DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
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
  KEY `fk_comment_user_id` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities
DROP TABLE IF EXISTS `entities`;
CREATE TABLE IF NOT EXISTS `entities` (
  `eid` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity unique id',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Entity create time',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Entity update time',
  `etid` tinyint unsigned NOT NULL COMMENT 'Entity type id',
  PRIMARY KEY (`eid`),
  KEY `type_id` (`etid`)
  ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.entities: ~4 rows (приблизительно)
INSERT INTO `entities` (`eid`, `created`, `updated`, `etid`) VALUES
	(1, '2022-02-10 00:02:16', '2022-03-02 15:47:45', 1);

-- Дамп структуры для таблица mublog.entities_article_data
DROP TABLE IF EXISTS `entities_article_data`;
CREATE TABLE IF NOT EXISTS `entities_article_data` (
  `eid` smallint unsigned NOT NULL COMMENT 'entity unique id',
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `summary` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alias` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'Published status',
  `preview_src` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '/images/article-preview-default.png',
  `preview_alt` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'article preview image',
  `author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'mublog.site',
  `views` smallint NOT NULL DEFAULT '0' COMMENT 'Number of article views',
  PRIMARY KEY (`eid`) USING BTREE,
  UNIQUE KEY `alias` (`alias`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.entities_article_data: ~0 rows (приблизительно)
INSERT INTO `entities_article_data` (`eid`, `title`, `summary`, `body`, `alias`, `status`, `preview_src`, `preview_alt`, `author`, `views`) VALUES
	(1, 'Набор полезных навыков для веб-разработчика от Андреаса Мэлсена', 'Веб-разработчик из Германии Андреас Мэлсен создал сайт, на котором он собрал множество полезных инcтрументов для веб-разработчиков и поделился с нами.', '&lt;p&gt;Веб-разработчик из Германии &lt;a href=&quot;https://andreasbm.github.io/&quot; target=&quot;_blank&quot;&gt;Андреас Мэлсен&lt;/a&gt; создал очень полезный сайт, &lt;a href=&quot;https://andreasbm.github.io/web-skills/&quot; target=&quot;_blank&quot;&gt;Web Skills&lt;/a&gt;, на котором он собрал и представил множество полезных ссылок на документации и статьи для изучения различных инструментов, которые могут очень пригодиться и быть полезными в веб-разработке.&lt;/p&gt;\r\n\r\n&lt;a href=&quot;https://andreasbm.github.io/web-skills/&quot; target=&quot;_blank&quot;&gt;\r\n    &lt;img src=&quot;/images/content/220210-image-web-skills-demo.gif&quot; alt=&quot;Web Skills Demo&quot; width=&quot;800&quot; height=&quot;512&quot;&gt;\r\n&lt;/a&gt;\r\n\r\n&lt;p&gt;На данном сайте выполнено визуальное представление полезных навыков по мнению Андреаса. Он рассказывает, что собранные им инструменты - результат 10-летнего опыта в веб-разработке и с которыми он, так или иначе, сталкивался лично. Новичкам же автор не рекомендует воспринимать свою библиотеку знаний, как руководство к изучению всего, что там представлено, а воспринимать это скорее как то, что возможно им придётся изучить в будущем. Так же Андреас утверждает, что на его сайте собрано гораздо больше, чем может потребоваться в повседневной разработке проектов, по этому не стоит пугаться столь огромного количества представленных материалов.&lt;/p&gt;', 'nabor-poleznih-navikov-dlya-veb-razrabotchika-ot-andreasa-melsena', 1, '/images/content/220210-preview-01.webp', 'Web Skills', 'mublog.site', 0);

-- Дамп структуры для таблица mublog.entities_comments
DROP TABLE IF EXISTS `entities_comments`;
CREATE TABLE IF NOT EXISTS `entities_comments` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `eid` smallint unsigned NOT NULL COMMENT 'entity  unique id',
  `cid` int unsigned NOT NULL COMMENT 'comment unique id',
  `deleted` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'is comment deleted',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entity_comment` (`eid`,`cid`),
  KEY `fk_entity_comment_id` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_feedback_data
DROP TABLE IF EXISTS `entities_feedback_data`;
CREATE TABLE IF NOT EXISTS `entities_feedback_data` (
  `eid` smallint unsigned NOT NULL COMMENT 'entity unique id',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `headers` json NOT NULL,
  `result` tinyint unsigned NOT NULL DEFAULT '0',
  `ip` varchar(45) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`eid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.entities_types
DROP TABLE IF EXISTS `entities_types`;
CREATE TABLE IF NOT EXISTS `entities_types` (
  `etid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity type unique id',
  `name` char(12) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Entity type name',
  PRIMARY KEY (`etid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.entities_types: ~2 rows (приблизительно)
INSERT INTO `entities_types` (`etid`, `name`) VALUES
	(1, 'article'),
	(2, 'feedback');

-- Дамп структуры для таблица mublog.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'User unique id',
  `mail` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User mail adress as login',
  `pwhash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Users'' password hash',
  `nickname` char(32) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User nickname',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usid` tinyint unsigned DEFAULT '2' COMMENT 'Users'' status id',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `fk_user_status_id` (`usid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.users: ~1 rows (приблизительно)
INSERT INTO `users` (`uid`, `mail`, `pwhash`, `nickname`, `created`, `usid`) VALUES
	(1, 'admin@mublog.site', '$argon2i$v=19$m=65536,t=4,p=1$RUc1TnhjQmFEdklMOUxxbA$Jqty69JewOT3ybQR1eUGEDkFP14vRAQVm/vZcB31T2M', 'mublog.site', '1999-12-31 21:00:00', 4);

-- Дамп структуры для таблица mublog.users_sessions
DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
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
  KEY `agent_hash` (`agent_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп структуры для таблица mublog.users_statuses
DROP TABLE IF EXISTS `users_statuses`;
CREATE TABLE IF NOT EXISTS `users_statuses` (
  `usid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'User status unique id',
  `status` char(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status parameter',
  `label` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status readable name',
  PRIMARY KEY (`usid`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.users_statuses: ~4 rows (приблизительно)
INSERT INTO `users_statuses` (`usid`, `status`, `label`) VALUES
	(1, 'anonym', 'Guest'),
	(2, 'user', 'Registered user'),
	(3, 'admin', 'Administrator user'),
	(4, 'master', 'Webmaster');

-- Дамп структуры для таблица mublog.users_status_access_levels
DROP TABLE IF EXISTS `users_status_access_levels`;
CREATE TABLE IF NOT EXISTS `users_status_access_levels` (
  `alid` tinyint unsigned NOT NULL COMMENT 'Access level id',
  `usid` tinyint unsigned NOT NULL COMMENT 'User status id',
  PRIMARY KEY (`alid`,`usid`),
  KEY `fk_accsess_level_status_id` (`usid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.users_status_access_levels: ~11 rows (приблизительно)
INSERT INTO `users_status_access_levels` (`alid`, `usid`) VALUES
	(1, 1),
	(2, 1),
	(2, 2),
	(3, 2),
	(2, 3),
	(3, 3),
	(4, 3),
	(2, 4),
	(3, 4),
	(4, 4),
	(5, 4);

ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comment_parent_id`
  FOREIGN KEY (`pid`)
  REFERENCES `comments` (`cid`)
  ON DELETE SET NULL
  ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comment_user_id`
  FOREIGN KEY (`uid`)
  REFERENCES `users` (`uid`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE `entities_article_data`
  ADD CONSTRAINT `fk_article_eid`
  FOREIGN KEY (`eid`)
  REFERENCES `entities` (`eid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `entities_comments`
  ADD CONSTRAINT `fk_entity_comment_id`
  FOREIGN KEY (`cid`)
  REFERENCES `comments` (`cid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entity_id`
  FOREIGN KEY (`eid`)
  REFERENCES `entities` (`eid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `entities_feedback_data`
  ADD CONSTRAINT `fk_feedback_eid`
  FOREIGN KEY (`eid`)
  REFERENCES `entities` (`eid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_status_id`
  FOREIGN KEY (`usid`)
  REFERENCES `users_statuses` (`usid`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE `users_sessions`
  ADD CONSTRAINT `fk_session_user_id`
  FOREIGN KEY (`uid`)
  REFERENCES `users` (`uid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `users_status_access_levels`
  ADD CONSTRAINT `fk_access_level_id`
  FOREIGN KEY (`alid`)
  REFERENCES `access_levels` (`alid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_accsess_level_status_id`
  FOREIGN KEY (`usid`)
  REFERENCES `users_statuses` (`usid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;


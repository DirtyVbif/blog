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
	(2, 'feedback'),
  (3, 'skill');

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

-- Дамп данных таблицы mublog.entities: ~9 rows (приблизительно)
INSERT INTO `entities` (`eid`, `created`, `updated`, `etid`) VALUES
	(1, '2022-02-10 00:02:16', '2022-03-02 15:47:45', 1),
  (2, '2022-03-17 20:55:29', '2022-03-17 20:55:29', 3),
	(3, '2022-03-17 23:21:56', '2022-03-17 23:21:56', 3),
	(4, '2022-03-18 13:04:58', '2022-03-18 13:04:58', 3),
	(5, '2022-03-18 13:11:22', '2022-03-18 13:11:22', 3),
	(6, '2022-03-18 13:13:15', '2022-03-18 13:13:15', 3),
	(7, '2022-03-18 13:25:53', '2022-03-18 13:25:53', 3),
	(8, '2022-03-18 13:27:11', '2022-03-18 13:27:11', 3),
	(9, '2022-03-18 13:30:28', '2022-03-18 13:30:28', 3),
	(10, '2022-03-20 17:52:04', '2022-03-20 17:52:04', 3);

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

-- Дамп структуры для таблица mublog.entities_skill_data
DROP TABLE IF EXISTS `entities_skill_data`;
CREATE TABLE `entities_skill_data` (
	`eid` SMALLINT(5) UNSIGNED NOT NULL,
	`title` VARCHAR(256) NOT NULL COLLATE 'utf8mb4_general_ci',
	`body` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`icon_src` VARCHAR(256) NOT NULL COLLATE 'utf8mb4_general_ci',
	`icon_alt` VARCHAR(256) NOT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`eid`) USING BTREE,
	CONSTRAINT `fk_skill_entity_id` FOREIGN KEY (`eid`) REFERENCES `entities` (`eid`) ON UPDATE CASCADE ON DELETE CASCADE
) COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- Дамп данных таблицы mublog.entities_skill_data: ~9 rows (приблизительно)
INSERT INTO `entities_skill_data` (`eid`, `title`, `body`, `icon_src`, `icon_alt`) VALUES
	(2, 'PHP', '<p>Этот язык я начал изучать с версии 7.0. На данный момент мои знания php сводятся к знанию отличий между php 5, 7 и 8, использованию ООП, pattern\'ов и моделей, вроде MVC. Понимаю такие страшные вещи, как наследование, инкапсуляция и полиморфизм.</p>\r\n\r\n<p>На практике использую версию 8.1 и типизацию значений по максимуму. Так же соблюдаю code-style и нейминг согласно PSR. Использую <code>composer autoload</code> для PSR-4. Для отладки использую xdebug модуль. Умею в настройку php.ini.</p>\r\n\r\n<p>В качестве примера доступен проект этого сайта <a href="https://github.com/DirtyVbif/blog" target="_blank">на github\'e</a>, движок которого написан мной с нуля с применением многих принципов и паттернов.</p>', '/images/logo-php.svg', 'php icon'),
	(3, 'JavaScript', '<p>Все же знакомы с Джимом Керри? Именно такие результаты гугл пытается подсунуть при попытках решить какую-либо задачу. И конечно же <code>2 + 2 = 22</code>. Так же стараюсь придерживаться разумного баланса процедурного и ООП стилей.</p>\r\n\r\n<p>Считаю, что js может быть слишком много и не понимаю людей, которые декларируют абсолютно все переменные как <code>var</code>. Стараюсь использовать использовать его аккуратно и использую <code>let | const | var</code> по необходимости и в зависимости от области видимости.</p>\r\n\r\n<p>Поверхностно знаком с фреймворками Vue и React, но сам их не использую. jQuery конечно же знаю, но предпочитаю ванильный js. Кстати, данный слайдер, с описанием моего стека, реализован отдельным классом на ванильном js. Посмотреть можно <a href="https://github.com/DirtyVbif/blog/tree/features/assets/libraries/ItemProjector/js" target="_blank">тут</a>.</p>', '/images/logo-js.svg', 'javascript icon'),
	(4, 'HTML5 / CSS3', '<p>HTML 5 потому что мне знакомы семантические теги и места их применения. Стараюсь делать валидную вёрстку с использованием атрибутов <code>role | aria-label</code> там, где это необходимо.</p>\r\n\r\n<p>Таблицу стилей стараюсь делать максимально гибкой, без использования inline-стилей, придерживаясь именных классов с использованием методологии BEM. Так же понимаю принципы создания адаптивной вёрстки, в чём можно убедиться на примере этого сайта.</p>\r\n\r\n<p>Активно использую препроцессор SASS/SCSS, компилируя его с использованием <code>npm gulp</code>. Примером так же будет проект этого сайта <a href="https://github.com/DirtyVbif/blog" target="_blank">на github\'e</a> с исходным кодом <a href="https://github.com/DirtyVbif/blog/tree/main/templates" target="_blank">шаблонов</a> с вёрсткой и <a href="https://github.com/DirtyVbif/blog/tree/main/assets/css" target="_blank">файлов scss</a>.</p>', '/images/logo-html-css.svg', 'html css icon'),
	(5, 'MySQL / PgSQL', '<p>Нормальная форма для меня не имеет ничего общего с внешним видом. Это полезные принципы проектирования структуры хранения данных в реляционных БД. Умею составлять запросы с использованием языка DML, а так же запросы на языке DDL для изменения структуры данных.</p>\r\n\r\n<p>Стараюсь использовать соответствующие типы данных, для уменьшения излишнего объёма данных. То есть обдумано стараюсь выбирать нужный тип, например, из <code>TINYINT | SMALLINT | INT | BIGINT</code> в зависимости от возможных значений для поля. Расширить всегда можно в последствии.</p>\r\n\r\n<p>На данный момент этот сайт использует MySQL драйвер 8й версии, но так же я провёл адаптацию ядра сайта для использования PostgreSQL, которую все считаю &laquo;настоящей&raquo; базой данный. С объектно-реляционной БД PostgreSQL я так же знаком, но для этого сайта она была бы излишне функциональна, по этому выбор пал на MySQL, которого более чем хватает для нужд блога.</p>', '/images/logo-sql.webp', 'sql icon'),
	(6, 'CMS Drupal', '<p>С этим чудом, иначе эту CMS не назвать, я познакомился довольно плотно за 2 года разработки и поддержи нескольких проектов, использующих 7 и 8 версию. Умею разрабатывать и настраивать темизацию, делать собственные модули, использовать хуки, а так же хорошо знаком с разделом администрирования (админка) и многими популярными модулями для расширения функционала сайта. Для решения задач всегда стараюсь найти <code>drupal way</code> вместо типичных костылей в хуках.</p>', '/images/logo-drupal.svg', 'drupal icon'),
	(7, 'Laravel', '<p>Удобный php-фреймворк для проектов разного уровня сложности, так как он достаточно гибкий для дорабатывания и адаптации его для задач разного уровня сложности.</p>\r\n\r\n<p>В то же время этот фреймворк довольно просто и отлично подходит для решения простых задач без лишней головной боли. Готовых проектов на данном фреймворке я не имею, так же как и практического опыта, но познакомиться с ним успел хорошо, так что на данный момент я имею представления и определённое понимание о разработке проектов на Laravel.</p>', '/images/logo-laravel.svg', 'laravel icon'),
	(8, 'Terminal', '<p>ssh, docker, composer, npm, sudo, git - вполне обычные для меня вещи. Регулярно пользуюсь composer\'ом и npm при разработке веб-проектов, а так же при работе с drupal и laravel познакомился с такими интерфейсами, как drush и artisan.</p>\r\n\r\n<p>Так же я реализовал собственный cli-интерфейс для разработки этого сайта, который реализует, на данный момент, небольшое количество методов, например для генерации файла настроек или создания новых классов и библиотек. Код и реализацию можно увидеть всё <a href="https://github.com/DirtyVbif/blog/tree/main/app/forge" target="_blank">в том же репозитории</a>.</p>', '/images/logo-terminal.svg', 'terminal icon'),
	(9, 'AMP & Turbo', '<p>Технология AMP и Yandex.Turbo принципиально разные вещи, хотя преследуют одну и ту же цель. На данный момент мой сайт не использует подобных технологий, но адаптацию вёрстки под AMP для гугл и вывод контента в поток данных для Yandex.Turbo я успел изучить сполна, пока разрабатывал проекты на друпале.</p>', '/images/logo-amp-turbo.svg', 'amp turbo icon'),
	(10, 'Composer', '<p>composer text</p>', '/images/logo-composer.webp', 'The Composer Official Logo: a male orchestra conductor with both arms in the air and his head tilted down, reading music sheets');

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

-- Дамп структуры для таблица mublog.log
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(16) NOT NULL COLLATE 'utf8mb4_general_ci',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`output` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`data` JSON NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `type` (`type`) USING BTREE
) COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

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


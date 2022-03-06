-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 10 2022 г., 03:27
-- Версия сервера: 8.0.24
-- Версия PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mublog`
--

-- --------------------------------------------------------

--
-- Структура таблицы `access_levels`
--

CREATE TABLE `access_levels` (
  `alid` tinyint UNSIGNED NOT NULL COMMENT 'User access level unique id',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Description of access level'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `access_levels`
--

INSERT INTO `access_levels` (`alid`, `label`) VALUES
(2, 'All users'),
(4, 'For admins and webmasters'),
(1, 'For guests and anonymous users'),
(3, 'For registered users'),
(5, 'Only for webmasters');

-- --------------------------------------------------------

--
-- Структура таблицы `access_levels_statuses`
--

CREATE TABLE `access_levels_statuses` (
  `alid` tinyint UNSIGNED NOT NULL COMMENT 'Access level id',
  `usid` tinyint UNSIGNED NOT NULL COMMENT 'User status id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `access_levels_statuses`
--

INSERT INTO `access_levels_statuses` (`alid`, `usid`) VALUES
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

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

CREATE TABLE `articles` (
  `id` smallint UNSIGNED NOT NULL,
  `title` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `summary` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `alias` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` int UNSIGNED DEFAULT NULL COMMENT 'Published unix timestamp',
  `updated` int UNSIGNED DEFAULT NULL COMMENT 'Last update unix timespamp',
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Published status',
  `preview_src` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '/images/article-preview-default.png',
  `preview_alt` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'mublog.site',
  `views` smallint NOT NULL DEFAULT '0' COMMENT 'Number of article views'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `articles`
--

INSERT INTO `articles` (`id`, `title`, `summary`, `body`, `alias`, `created`, `updated`, `status`, `preview_src`, `preview_alt`, `author`, `views`) VALUES
(1, 'Набор полезных навыков для веб-разработчика от Андреаса Мэлсена', 'Веб-разработчик из Германии Андреас Мэлсен создал сайт, на котором он собрал множество полезных инcтрументов для веб-разработчиков и поделился с нами.', '&lt;p&gt;Веб-разработчик из Германии &lt;a href=&quot;https://andreasbm.github.io/&quot; target=&quot;_blank&quot;&gt;Андреас Мэлсен&lt;/a&gt; создал очень полезный сайт, &lt;a href=&quot;https://andreasbm.github.io/web-skills/&quot; target=&quot;_blank&quot;&gt;Web Skills&lt;/a&gt;, на котором он собрал и представил множество полезных ссылок на документации и статьи для изучения различных инструментов, которые могут очень пригодиться и быть полезными в веб-разработке.&lt;/p&gt;\r\n\r\n&lt;a href=&quot;https://andreasbm.github.io/web-skills/&quot; target=&quot;_blank&quot;&gt;\r\n    &lt;img src=&quot;/images/content/220210-image-web-skills-demo.gif&quot; alt=&quot;Web Skills Demo&quot; width=&quot;800&quot; height=&quot;512&quot;&gt;\r\n&lt;/a&gt;\r\n\r\n&lt;p&gt;На данном сайте выполнено визуальное представление полезных навыков по мнению Андреаса. Он рассказывает, что собранные им инструменты - результат 10-летнего опыта в веб-разработке и с которыми он, так или иначе, сталкивался лично. Новичкам же автор не рекомендует воспринимать свою библиотеку знаний, как руководство к изучению всего, что там представлено, а воспринимать это скорее как то, что возможно им придётся изучить в будущем. Так же Андреас утверждает, что на его сайте собрано гораздо больше, чем может потребоваться в повседневной разработке проектов, по этому не стоит пугаться столь огромного количества представленных материалов.&lt;/p&gt;', 'nabor-poleznih-navikov-dlya-veb-razrabotchika-ot-andreasa-melsena', 1644451336, 1644452710, 1, '/images/content/220210-preview-01.jpg', 'Web Skills', 'mublog.site', 0);

--
-- Триггеры `articles`
--
DELIMITER $$
CREATE TRIGGER `articles_before_insert` BEFORE INSERT ON `articles` FOR EACH ROW BEGIN
   IF NEW.created IS NULL THEN
		SET NEW.created = UNIX_TIMESTAMP();
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `articles_before_update` BEFORE UPDATE ON `articles` FOR EACH ROW BEGIN
   IF NEW.updated IS NULL THEN
		SET NEW.updated = UNIX_TIMESTAMP();
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `article_comments`
--

CREATE TABLE `article_comments` (
  `aid` smallint UNSIGNED NOT NULL,
  `cid` int UNSIGNED NOT NULL,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `cid` int UNSIGNED NOT NULL,
  `pid` int UNSIGNED DEFAULT NULL,
  `created` int UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `ip` char(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Триггеры `comments`
--
DELIMITER $$
CREATE TRIGGER `comments_before_insert` BEFORE INSERT ON `comments` FOR EACH ROW BEGIN
   IF NEW.created IS NULL THEN
		SET NEW.created = UNIX_TIMESTAMP();
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `mailer_sended_mails`
--

CREATE TABLE `mailer_sended_mails` (
  `id` smallint UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `headers` json NOT NULL,
  `result` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contains all sended mails from webform';

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `uid` int UNSIGNED NOT NULL COMMENT 'User unique id',
  `mail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User mail adress as login',
  `pwhash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Users'' password hash',
  `nickname` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User nickname',
  `registered` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'User register timestamp',
  `usid` tinyint UNSIGNED DEFAULT '2' COMMENT 'Users'' status id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`uid`, `mail`, `pwhash`, `nickname`, `registered`, `usid`) VALUES
(1, 'admin@mublog.site', '$argon2i$v=19$m=65536,t=4,p=1$RUc1TnhjQmFEdklMOUxxbA$Jqty69JewOT3ybQR1eUGEDkFP14vRAQVm/vZcB31T2M', 'mublog.site', 0, 4);

--
-- Триггеры `users`
--
DELIMITER $$
CREATE TRIGGER `users_before_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
   IF NEW.registered IS NULL THEN
		SET NEW.registered = UNIX_TIMESTAMP();
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `users_sessions`
--

CREATE TABLE `users_sessions` (
  `uid` int UNSIGNED NOT NULL COMMENT 'User unique id',
  `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User session token',
  `agent_hash` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User agent md5 hash',
  `browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `updated` int UNSIGNED NOT NULL COMMENT 'Users'' last action timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Триггеры `users_sessions`
--
DELIMITER $$
CREATE TRIGGER `users_sessions_before_insert` BEFORE INSERT ON `users_sessions` FOR EACH ROW BEGIN
   IF NEW.updated IS NULL THEN
		SET NEW.updated = UNIX_TIMESTAMP();
	END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_sessions_before_update` BEFORE UPDATE ON `users_sessions` FOR EACH ROW BEGIN
   IF NEW.updated IS NULL THEN
		SET NEW.updated = UNIX_TIMESTAMP();
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `users_statuses_list`
--

CREATE TABLE `users_statuses_list` (
  `usid` tinyint UNSIGNED NOT NULL COMMENT 'User status unique id',
  `status` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status parameter',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status readable name'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users_statuses_list`
--

INSERT INTO `users_statuses_list` (`usid`, `status`, `label`) VALUES
(1, 'anonym', 'Guest'),
(2, 'user', 'Registered user'),
(3, 'admin', 'Administrator user'),
(4, 'master', 'Webmaster');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `access_levels`
--
ALTER TABLE `access_levels`
  ADD PRIMARY KEY (`alid`) USING BTREE,
  ADD UNIQUE KEY `access` (`label`) USING BTREE;

--
-- Индексы таблицы `access_levels_statuses`
--
ALTER TABLE `access_levels_statuses`
  ADD PRIMARY KEY (`alid`,`usid`),
  ADD KEY `fk_accsess_level_status_id` (`usid`);

--
-- Индексы таблицы `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alias` (`alias`) USING BTREE;

--
-- Индексы таблицы `article_comments`
--
ALTER TABLE `article_comments`
  ADD UNIQUE KEY `article_comment_id` (`aid`,`cid`),
  ADD KEY `fk_comment_id` (`cid`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `fk_comment_parent_id` (`pid`);

--
-- Индексы таблицы `mailer_sended_mails`
--
ALTER TABLE `mailer_sended_mails`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD KEY `fk_user_status_id` (`usid`);

--
-- Индексы таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `uid` (`uid`),
  ADD KEY `agent_hash` (`agent_hash`),
  ADD KEY `uptime` (`updated`) USING BTREE;

--
-- Индексы таблицы `users_statuses_list`
--
ALTER TABLE `users_statuses_list`
  ADD PRIMARY KEY (`usid`),
  ADD UNIQUE KEY `status` (`status`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `access_levels`
--
ALTER TABLE `access_levels`
  MODIFY `alid` tinyint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User access level unique id', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `articles`
--
ALTER TABLE `articles`
  MODIFY `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `cid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `mailer_sended_mails`
--
ALTER TABLE `mailer_sended_mails`
  MODIFY `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `uid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User unique id', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users_statuses_list`
--
ALTER TABLE `users_statuses_list`
  MODIFY `usid` tinyint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User status unique id', AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `access_levels_statuses`
--
ALTER TABLE `access_levels_statuses`
  ADD CONSTRAINT `fk_access_level_id` FOREIGN KEY (`alid`) REFERENCES `access_levels` (`alid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_accsess_level_status_id` FOREIGN KEY (`usid`) REFERENCES `users_statuses_list` (`usid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `article_comments`
--
ALTER TABLE `article_comments`
  ADD CONSTRAINT `fk_article_id` FOREIGN KEY (`aid`) REFERENCES `articles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_comment_id` FOREIGN KEY (`cid`) REFERENCES `comments` (`cid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comment_parent_id` FOREIGN KEY (`pid`) REFERENCES `comments` (`cid`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_status_id` FOREIGN KEY (`usid`) REFERENCES `users_statuses_list` (`usid`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  ADD CONSTRAINT `fk_session_user_id` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

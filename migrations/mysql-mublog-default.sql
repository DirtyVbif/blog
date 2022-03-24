-- Дамп данных таблицы mublog.access_levels: ~5 rows (приблизительно)
INSERT INTO `access_levels` (`alid`, `label`) VALUES
	(2, 'All users'),
	(4, 'For admins and webmasters'),
	(1, 'For guests and anonymous users'),
	(3, 'For registered users'),
	(5, 'Only for webmasters');

-- Дамп данных таблицы mublog.entities_types: ~2 rows (приблизительно)
INSERT INTO `entities_types` (`etid`, `name`) VALUES
	(1, 'article'),
	(2, 'feedback'),
	(3, 'skill');

-- Дамп данных таблицы mublog.users_statuses: ~4 rows (приблизительно)
INSERT INTO `users_statuses` (`usid`, `status`, `label`) VALUES
	(1, 'anonym', 'Guest'),
	(2, 'user', 'Registered user'),
	(3, 'admin', 'Administrator user'),
	(4, 'master', 'Webmaster');

-- Дамп данных таблицы mublog.users: ~1 rows (приблизительно)
INSERT INTO `users` (`uid`, `mail`, `pwhash`, `nickname`, `created`, `usid`) VALUES
	(1, 'admin@mublog.site', '$argon2i$v=19$m=65536,t=4,p=1$RUc1TnhjQmFEdklMOUxxbA$Jqty69JewOT3ybQR1eUGEDkFP14vRAQVm/vZcB31T2M', 'mublog.site', '1999-12-31 21:00:00', 4);

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
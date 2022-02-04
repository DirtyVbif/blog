-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               8.0.24 - MySQL Community Server - GPL
-- Операционная система:         Win64
-- HeidiSQL Версия:              11.3.0.6376
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Дамп структуры для таблица mublog.access_levels
DROP TABLE IF EXISTS `access_levels`;
CREATE TABLE IF NOT EXISTS `access_levels` (
  `alid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'User access level unique id',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Description of access level',
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

-- Дамп структуры для таблица mublog.access_levels_statuses
DROP TABLE IF EXISTS `access_levels_statuses`;
CREATE TABLE IF NOT EXISTS `access_levels_statuses` (
  `alid` tinyint unsigned NOT NULL COMMENT 'Access level id',
  `usid` tinyint unsigned NOT NULL COMMENT 'User status id',
  PRIMARY KEY (`alid`,`usid`),
  KEY `fk_accsess_level_status_id` (`usid`),
  CONSTRAINT `fk_access_level_id` FOREIGN KEY (`alid`) REFERENCES `access_levels` (`alid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_accsess_level_status_id` FOREIGN KEY (`usid`) REFERENCES `users_statuses_list` (`usid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.access_levels_statuses: ~11 rows (приблизительно)
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

-- Дамп структуры для таблица mublog.articles
DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `summary` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` int unsigned DEFAULT NULL COMMENT 'Published unix timestamp',
  `updated` int unsigned DEFAULT NULL COMMENT 'Last update unix timespamp',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'Published status',
  `preview_src` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '/images/article-preview-default.png',
  `preview_alt` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.articles: ~3 rows (приблизительно)
INSERT INTO `articles` (`id`, `title`, `summary`, `body`, `alias`, `created`, `updated`, `status`, `preview_src`, `preview_alt`) VALUES
	(1, 'Какой-то заголовок для какой-то новости в блоге', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Obcaecati saepe minima eligendi nobis explicabo quas a architecto officia velit ducimus pariatur consequuntur quos ipsam nemo laboriosam asperiores, neque, iusto accusamus!', '<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Obcaecati placeat reprehenderit hic pariatur quia velit magnam, soluta iusto quod distinctio totam harum? Ipsam ea, molestias quae ullam blanditiis iste voluptate? Culpa tempora dolorem dolor omnis, ipsum ad porro, aspernatur laboriosam excepturi placeat numquam ipsam delectus aliquid rerum dignissimos labore sed aliquam rem. Nostrum autem, aperiam impedit quo odio consequuntur laborum perferendis, ut enim provident adipisci voluptas dignissimos numquam corporis. Eum eaque obcaecati corrupti illo, soluta nisi quod ratione iste eligendi at alias ea maiores enim mollitia! Error quisquam incidunt voluptates, illum vitae aut animi itaque sit placeat sed repellat amet, harum, optio illo saepe pariatur omnis atque minima doloribus. Iste dolore quis a molestias itaque, cum autem alias non veniam eius tenetur repudiandae asperiores aliquid molestiae quod. Totam eaque officiis aut soluta quasi esse ea laudantium laboriosam, reiciendis repellendus incidunt consequuntur impedit? Sint quis commodi, facere ad natus quisquam mollitia consequuntur vero, tempore recusandae necessitatibus! Quos maiores veritatis sunt quae maxime earum id voluptates at dignissimos odit, praesentium laborum expedita. Ipsam nihil atque eveniet, dolores eos amet pariatur repellendus. Sunt, voluptates nisi. Iusto nisi minus veritatis saepe, reprehenderit nostrum aspernatur esse impedit, eaque laudantium ullam reiciendis eveniet pariatur omnis minima. Laborum molestiae reprehenderit vel recusandae vitae nobis, quae velit in, provident dolor ad voluptates atque, esse maxime culpa at? Debitis voluptatem sint quae vero, pariatur mollitia quod natus praesentium in quis unde ut saepe blanditiis iusto dolorum non molestiae ipsa libero nostrum ex porro quas soluta. Iusto, aspernatur consectetur! Iste?</p><p>Eveniet tempora quaerat possimus ad pariatur accusantium nulla maxime nobis, sapiente officia optio quia, voluptatem distinctio hic ea est dolores tempore iure culpa corporis! Consequuntur provident dolore nihil quasi hic eum in eius quas porro nesciunt pariatur ex, commodi, iste fuga dignissimos nisi necessitatibus, deleniti quo? Debitis natus fuga consectetur error, non culpa alias ut quam cum repudiandae, ex veniam beatae, iusto hic laboriosam quisquam laborum. Quo suscipit amet quia, dolore beatae veritatis rem natus neque. Omnis soluta aliquid quaerat aut ad necessitatibus quos dignissimos dicta nesciunt minima magni doloremque cumque minus reiciendis, quae odio deserunt voluptas quibusdam mollitia a voluptatem fugiat. Vel eos sapiente id, consectetur quaerat distinctio neque eaque commodi, placeat deleniti laudantium. Nihil ut id vitae consequuntur iste dolorum eligendi, distinctio at modi ea, placeat pariatur, reiciendis cupiditate architecto quasi magnam voluptatibus vero. Molestias fugit ipsam quas neque, doloribus accusamus, possimus quidem dicta praesentium inventore ex omnis veritatis unde sit consequuntur harum tempore! Sit non debitis officiis, doloremque delectus qui modi iste ab at? Minima nam sapiente neque unde tempora, odit recusandae voluptatem nihil atque ex. Sint excepturi reiciendis nam, odio cum ut quibusdam libero nesciunt exercitationem nobis. Odit omnis est voluptates quam consequatur veritatis laborum temporibus ex amet voluptate? Quod, adipisci veniam. Nesciunt praesentium explicabo dolorem in vel, beatae odio velit laborum ex iste architecto a odit quod ipsam placeat hic suscipit nemo laudantium quasi exercitationem dolorum nostrum mollitia cumque. Quidem mollitia, reiciendis aperiam, earum expedita repellat rem dolorum suscipit aliquam molestias quibusdam officia assumenda neque?</p><p>Inventore nesciunt incidunt numquam minus architecto id possimus velit? Provident numquam magnam quia nobis repudiandae, aperiam unde fuga incidunt! Architecto dignissimos beatae rerum et sit delectus libero quisquam necessitatibus veritatis a nesciunt expedita corrupti harum illo, quo omnis quasi asperiores vero officia culpa quidem blanditiis nemo assumenda. Voluptatem perspiciatis vel consectetur provident dicta error ducimus quia at ipsum! Reprehenderit eum earum odit reiciendis cum tenetur! Ipsum, inventore. Expedita unde libero corrupti atque, officia corporis, enim aliquid ratione architecto similique est sed praesentium? Porro minus dolores nihil illum atque vero consequatur sapiente facere quidem ratione, molestias laboriosam quibusdam aspernatur, temporibus ullam, beatae necessitatibus dicta impedit dolor sed a? Id culpa fugit eos quos, dolorum quia eligendi impedit maiores. Molestiae libero distinctio aut quis! Temporibus fuga eligendi commodi eum consequuntur quo velit ratione molestiae voluptatum consectetur aliquam omnis molestias exercitationem reprehenderit illo repellat accusamus soluta natus corporis corrupti accusantium sunt, odit modi debitis? Nobis odio facere ratione quas ducimus quaerat reiciendis quos autem. Quidem ipsum quibusdam provident totam quasi necessitatibus quaerat numquam perspiciatis voluptate quis soluta, expedita dolore facere illo alias tempora neque, veniam est ad nemo maxime! Facilis numquam temporibus quia, autem provident sapiente repellat repellendus incidunt culpa molestiae animi aliquam voluptates velit odit ad tempora suscipit quos quibusdam? Recusandae, voluptatibus, beatae cumque laborum velit ipsum ea assumenda et quod est earum quibusdam commodi rerum mollitia incidunt consectetur dolore dolorum perspiciatis? Quasi velit quibusdam assumenda, officia adipisci aut numquam vitae modi. Odio aliquid perferendis eos non reprehenderit similique suscipit tempora voluptates!</p><p>Necessitatibus quasi ipsam minus labore illum cupiditate quidem doloremque nesciunt, consequatur aliquid harum nobis doloribus eum, commodi reiciendis odio in est fuga cum hic nostrum enim dolorum? Inventore tempore placeat, quisquam dolore esse excepturi ullam expedita deserunt sint eaque magni sequi dolorem assumenda error sed eveniet recusandae quaerat voluptatem id facilis, voluptatum corrupti officia nemo fugit. Iure voluptates repudiandae error qui obcaecati dolorum dicta totam harum iusto delectus, dolores ipsam nemo rem architecto earum a sint quam recusandae enim quia quibusdam atque distinctio quae saepe! Fuga recusandae modi repudiandae voluptas autem adipisci beatae voluptatibus accusantium minus, voluptatem quam consequuntur eligendi rerum veniam dignissimos voluptates delectus tempora quis. Architecto iure soluta at aut voluptatem delectus quae quasi! Est, asperiores voluptate qui necessitatibus cumque doloremque temporibus? Optio inventore nemo eligendi dolore esse ullam enim vitae aliquam, nostrum ad tenetur autem sed rem pariatur dolorum quasi quam ex odio deserunt ducimus! Sequi quas praesentium officia, earum accusantium mollitia unde labore ratione nemo impedit illum autem tenetur, libero aliquid. Impedit deleniti asperiores non sequi distinctio quo, assumenda laudantium explicabo eaque sunt sint error vitae quaerat culpa dolorum ut! Error, dolorem. Commodi unde corrupti officia, quibusdam quis odit eveniet quae necessitatibus voluptates eos doloribus esse reprehenderit minus quaerat quod accusamus, quia ducimus? Fuga officia illo, nostrum mollitia, corrupti quisquam magni eligendi reprehenderit enim, omnis sit ducimus hic? Inventore beatae sunt minima nulla adipisci fuga nemo iste, deleniti natus nihil illo aperiam eos quasi repellendus quod. Incidunt temporibus numquam, aperiam deserunt facere quidem doloremque cumque aspernatur.</p><p>Tempore odio corporis, voluptates maiores nisi in, officiis et labore quis omnis velit, cumque enim aliquid a cum ea deleniti tempora reiciendis porro. Laborum officiis assumenda quas, autem aliquid, consectetur corporis iure illum sequi molestiae dolorem praesentium fuga tempora neque aspernatur fugiat non! Corporis ratione optio atque amet esse facilis minima quasi nobis, sunt reprehenderit, laudantium, mollitia autem doloremque excepturi cum quo fugiat! Repellat illo et labore! Repellat tenetur natus debitis nobis nam voluptate commodi itaque quia, cupiditate eius saepe voluptatem soluta quod qui eligendi ut. Nesciunt, a aliquid blanditiis quibusdam ab, odit praesentium minima laboriosam placeat officiis sint inventore suscipit quia omnis pariatur odio ex quas tenetur officia sunt? A blanditiis, veritatis vel libero voluptatibus quae! Dolor velit iusto sunt officia perferendis. Quaerat aut assumenda praesentium, quos iste quasi voluptatum repellat consequuntur nobis voluptas eveniet velit neque sit qui facilis ipsum eius ea veritatis magnam blanditiis sunt vero accusamus. Fuga magnam quisquam recusandae fugit distinctio perferendis reiciendis fugiat alias voluptates maxime, obcaecati illo ea dolores culpa nobis neque ab corporis iusto sequi, blanditiis iure sed. Tenetur, sed ipsum! Soluta repudiandae doloremque incidunt temporibus rem fuga. Ad provident cumque facilis beatae sunt temporibus, sed blanditiis vitae quae corrupti pariatur aut officia eos recusandae, iusto, iste vero adipisci. Ipsum quibusdam consectetur doloribus aperiam! Deserunt, voluptatum impedit natus nemo nulla nesciunt minima, neque laborum quasi quisquam id omnis. Molestias soluta obcaecati eius similique cum fugiat maxime doloribus sit, sint pariatur! Reprehenderit qui facere necessitatibus quibusdam, veniam nulla illum harum consequatur adipisci itaque.</p>', 'kakoyi-to-zagolovok-dlya-kakoyi-to-novosti-v-bloge', 1641857177, 1644002146, 1, '/images/article-preview-default.png', NULL),
	(9, 'Очередной заголовок для записи в журнал', 'Optio voluptas voluptatum neque sit commodi odio quasi tempore hic necessitatibus quos corporis saepe molestias accusantium asperiores numquam perferendis, architecto labore. Vitae eaque pariatur cupiditate molestias rerum. A, molestiae!', '<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Obcaecati placeat reprehenderit hic pariatur quia velit magnam, soluta iusto quod distinctio totam harum? Ipsam ea, molestias quae ullam blanditiis iste voluptate? Culpa tempora dolorem dolor omnis, ipsum ad porro, aspernatur laboriosam excepturi placeat numquam ipsam delectus aliquid rerum dignissimos labore sed aliquam rem. Nostrum autem, aperiam impedit quo odio consequuntur laborum perferendis, ut enim provident adipisci voluptas dignissimos numquam corporis. Eum eaque obcaecati corrupti illo, soluta nisi quod ratione iste eligendi at alias ea maiores enim mollitia! Error quisquam incidunt voluptates, illum vitae aut animi itaque sit placeat sed repellat amet, harum, optio illo saepe pariatur omnis atque minima doloribus. Iste dolore quis a molestias itaque, cum autem alias non veniam eius tenetur repudiandae asperiores aliquid molestiae quod. Totam eaque officiis aut soluta quasi esse ea laudantium laboriosam, reiciendis repellendus incidunt consequuntur impedit? Sint quis commodi, facere ad natus quisquam mollitia consequuntur vero, tempore recusandae necessitatibus! Quos maiores veritatis sunt quae maxime earum id voluptates at dignissimos odit, praesentium laborum expedita. Ipsam nihil atque eveniet, dolores eos amet pariatur repellendus. Sunt, voluptates nisi. Iusto nisi minus veritatis saepe, reprehenderit nostrum aspernatur esse impedit, eaque laudantium ullam reiciendis eveniet pariatur omnis minima. Laborum molestiae reprehenderit vel recusandae vitae nobis, quae velit in, provident dolor ad voluptates atque, esse maxime culpa at? Debitis voluptatem sint quae vero, pariatur mollitia quod natus praesentium in quis unde ut saepe blanditiis iusto dolorum non molestiae ipsa libero nostrum ex porro quas soluta. Iusto, aspernatur consectetur! Iste?</p><p>Eveniet tempora quaerat possimus ad pariatur accusantium nulla maxime nobis, sapiente officia optio quia, voluptatem distinctio hic ea est dolores tempore iure culpa corporis! Consequuntur provident dolore nihil quasi hic eum in eius quas porro nesciunt pariatur ex, commodi, iste fuga dignissimos nisi necessitatibus, deleniti quo? Debitis natus fuga consectetur error, non culpa alias ut quam cum repudiandae, ex veniam beatae, iusto hic laboriosam quisquam laborum. Quo suscipit amet quia, dolore beatae veritatis rem natus neque. Omnis soluta aliquid quaerat aut ad necessitatibus quos dignissimos dicta nesciunt minima magni doloremque cumque minus reiciendis, quae odio deserunt voluptas quibusdam mollitia a voluptatem fugiat. Vel eos sapiente id, consectetur quaerat distinctio neque eaque commodi, placeat deleniti laudantium. Nihil ut id vitae consequuntur iste dolorum eligendi, distinctio at modi ea, placeat pariatur, reiciendis cupiditate architecto quasi magnam voluptatibus vero. Molestias fugit ipsam quas neque, doloribus accusamus, possimus quidem dicta praesentium inventore ex omnis veritatis unde sit consequuntur harum tempore! Sit non debitis officiis, doloremque delectus qui modi iste ab at? Minima nam sapiente neque unde tempora, odit recusandae voluptatem nihil atque ex. Sint excepturi reiciendis nam, odio cum ut quibusdam libero nesciunt exercitationem nobis. Odit omnis est voluptates quam consequatur veritatis laborum temporibus ex amet voluptate? Quod, adipisci veniam. Nesciunt praesentium explicabo dolorem in vel, beatae odio velit laborum ex iste architecto a odit quod ipsam placeat hic suscipit nemo laudantium quasi exercitationem dolorum nostrum mollitia cumque. Quidem mollitia, reiciendis aperiam, earum expedita repellat rem dolorum suscipit aliquam molestias quibusdam officia assumenda neque?</p><p>Inventore nesciunt incidunt numquam minus architecto id possimus velit? Provident numquam magnam quia nobis repudiandae, aperiam unde fuga incidunt! Architecto dignissimos beatae rerum et sit delectus libero quisquam necessitatibus veritatis a nesciunt expedita corrupti harum illo, quo omnis quasi asperiores vero officia culpa quidem blanditiis nemo assumenda. Voluptatem perspiciatis vel consectetur provident dicta error ducimus quia at ipsum! Reprehenderit eum earum odit reiciendis cum tenetur! Ipsum, inventore. Expedita unde libero corrupti atque, officia corporis, enim aliquid ratione architecto similique est sed praesentium? Porro minus dolores nihil illum atque vero consequatur sapiente facere quidem ratione, molestias laboriosam quibusdam aspernatur, temporibus ullam, beatae necessitatibus dicta impedit dolor sed a? Id culpa fugit eos quos, dolorum quia eligendi impedit maiores. Molestiae libero distinctio aut quis! Temporibus fuga eligendi commodi eum consequuntur quo velit ratione molestiae voluptatum consectetur aliquam omnis molestias exercitationem reprehenderit illo repellat accusamus soluta natus corporis corrupti accusantium sunt, odit modi debitis? Nobis odio facere ratione quas ducimus quaerat reiciendis quos autem. Quidem ipsum quibusdam provident totam quasi necessitatibus quaerat numquam perspiciatis voluptate quis soluta, expedita dolore facere illo alias tempora neque, veniam est ad nemo maxime! Facilis numquam temporibus quia, autem provident sapiente repellat repellendus incidunt culpa molestiae animi aliquam voluptates velit odit ad tempora suscipit quos quibusdam? Recusandae, voluptatibus, beatae cumque laborum velit ipsum ea assumenda et quod est earum quibusdam commodi rerum mollitia incidunt consectetur dolore dolorum perspiciatis? Quasi velit quibusdam assumenda, officia adipisci aut numquam vitae modi. Odio aliquid perferendis eos non reprehenderit similique suscipit tempora voluptates!</p><p>Necessitatibus quasi ipsam minus labore illum cupiditate quidem doloremque nesciunt, consequatur aliquid harum nobis doloribus eum, commodi reiciendis odio in est fuga cum hic nostrum enim dolorum? Inventore tempore placeat, quisquam dolore esse excepturi ullam expedita deserunt sint eaque magni sequi dolorem assumenda error sed eveniet recusandae quaerat voluptatem id facilis, voluptatum corrupti officia nemo fugit. Iure voluptates repudiandae error qui obcaecati dolorum dicta totam harum iusto delectus, dolores ipsam nemo rem architecto earum a sint quam recusandae enim quia quibusdam atque distinctio quae saepe! Fuga recusandae modi repudiandae voluptas autem adipisci beatae voluptatibus accusantium minus, voluptatem quam consequuntur eligendi rerum veniam dignissimos voluptates delectus tempora quis. Architecto iure soluta at aut voluptatem delectus quae quasi! Est, asperiores voluptate qui necessitatibus cumque doloremque temporibus? Optio inventore nemo eligendi dolore esse ullam enim vitae aliquam, nostrum ad tenetur autem sed rem pariatur dolorum quasi quam ex odio deserunt ducimus! Sequi quas praesentium officia, earum accusantium mollitia unde labore ratione nemo impedit illum autem tenetur, libero aliquid. Impedit deleniti asperiores non sequi distinctio quo, assumenda laudantium explicabo eaque sunt sint error vitae quaerat culpa dolorum ut! Error, dolorem. Commodi unde corrupti officia, quibusdam quis odit eveniet quae necessitatibus voluptates eos doloribus esse reprehenderit minus quaerat quod accusamus, quia ducimus? Fuga officia illo, nostrum mollitia, corrupti quisquam magni eligendi reprehenderit enim, omnis sit ducimus hic? Inventore beatae sunt minima nulla adipisci fuga nemo iste, deleniti natus nihil illo aperiam eos quasi repellendus quod. Incidunt temporibus numquam, aperiam deserunt facere quidem doloremque cumque aspernatur.</p><p>Tempore odio corporis, voluptates maiores nisi in, officiis et labore quis omnis velit, cumque enim aliquid a cum ea deleniti tempora reiciendis porro. Laborum officiis assumenda quas, autem aliquid, consectetur corporis iure illum sequi molestiae dolorem praesentium fuga tempora neque aspernatur fugiat non! Corporis ratione optio atque amet esse facilis minima quasi nobis, sunt reprehenderit, laudantium, mollitia autem doloremque excepturi cum quo fugiat! Repellat illo et labore! Repellat tenetur natus debitis nobis nam voluptate commodi itaque quia, cupiditate eius saepe voluptatem soluta quod qui eligendi ut. Nesciunt, a aliquid blanditiis quibusdam ab, odit praesentium minima laboriosam placeat officiis sint inventore suscipit quia omnis pariatur odio ex quas tenetur officia sunt? A blanditiis, veritatis vel libero voluptatibus quae! Dolor velit iusto sunt officia perferendis. Quaerat aut assumenda praesentium, quos iste quasi voluptatum repellat consequuntur nobis voluptas eveniet velit neque sit qui facilis ipsum eius ea veritatis magnam blanditiis sunt vero accusamus. Fuga magnam quisquam recusandae fugit distinctio perferendis reiciendis fugiat alias voluptates maxime, obcaecati illo ea dolores culpa nobis neque ab corporis iusto sequi, blanditiis iure sed. Tenetur, sed ipsum! Soluta repudiandae doloremque incidunt temporibus rem fuga. Ad provident cumque facilis beatae sunt temporibus, sed blanditiis vitae quae corrupti pariatur aut officia eos recusandae, iusto, iste vero adipisci. Ipsum quibusdam consectetur doloribus aperiam! Deserunt, voluptatum impedit natus nemo nulla nesciunt minima, neque laborum quasi quisquam id omnis. Molestias soluta obcaecati eius similique cum fugiat maxime doloribus sit, sint pariatur! Reprehenderit qui facere necessitatibus quibusdam, veniam nulla illum harum consequatur adipisci itaque.</p>', 'ocherednoyi-zagolovok-dlya-zapisi-v-jurnal', 1642880477, 1644002156, 1, '/images/article-preview-default.png', NULL),
	(10, 'Ещё какая-то запись в блоге и заголовок', 'Nobis maxime modi ipsum, architecto fuga non cum accusamus culpa a exercitationem et illum provident, placeat qui voluptatibus vel est molestias soluta inventore eius. Inventore, ipsa recusandae.', '<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Obcaecati placeat reprehenderit hic pariatur quia velit magnam, soluta iusto quod distinctio totam harum? Ipsam ea, molestias quae ullam blanditiis iste voluptate? Culpa tempora dolorem dolor omnis, ipsum ad porro, aspernatur laboriosam excepturi placeat numquam ipsam delectus aliquid rerum dignissimos labore sed aliquam rem. Nostrum autem, aperiam impedit quo odio consequuntur laborum perferendis, ut enim provident adipisci voluptas dignissimos numquam corporis. Eum eaque obcaecati corrupti illo, soluta nisi quod ratione iste eligendi at alias ea maiores enim mollitia! Error quisquam incidunt voluptates, illum vitae aut animi itaque sit placeat sed repellat amet, harum, optio illo saepe pariatur omnis atque minima doloribus. Iste dolore quis a molestias itaque, cum autem alias non veniam eius tenetur repudiandae asperiores aliquid molestiae quod. Totam eaque officiis aut soluta quasi esse ea laudantium laboriosam, reiciendis repellendus incidunt consequuntur impedit? Sint quis commodi, facere ad natus quisquam mollitia consequuntur vero, tempore recusandae necessitatibus! Quos maiores veritatis sunt quae maxime earum id voluptates at dignissimos odit, praesentium laborum expedita. Ipsam nihil atque eveniet, dolores eos amet pariatur repellendus. Sunt, voluptates nisi. Iusto nisi minus veritatis saepe, reprehenderit nostrum aspernatur esse impedit, eaque laudantium ullam reiciendis eveniet pariatur omnis minima. Laborum molestiae reprehenderit vel recusandae vitae nobis, quae velit in, provident dolor ad voluptates atque, esse maxime culpa at? Debitis voluptatem sint quae vero, pariatur mollitia quod natus praesentium in quis unde ut saepe blanditiis iusto dolorum non molestiae ipsa libero nostrum ex porro quas soluta. Iusto, aspernatur consectetur! Iste?</p><p>Eveniet tempora quaerat possimus ad pariatur accusantium nulla maxime nobis, sapiente officia optio quia, voluptatem distinctio hic ea est dolores tempore iure culpa corporis! Consequuntur provident dolore nihil quasi hic eum in eius quas porro nesciunt pariatur ex, commodi, iste fuga dignissimos nisi necessitatibus, deleniti quo? Debitis natus fuga consectetur error, non culpa alias ut quam cum repudiandae, ex veniam beatae, iusto hic laboriosam quisquam laborum. Quo suscipit amet quia, dolore beatae veritatis rem natus neque. Omnis soluta aliquid quaerat aut ad necessitatibus quos dignissimos dicta nesciunt minima magni doloremque cumque minus reiciendis, quae odio deserunt voluptas quibusdam mollitia a voluptatem fugiat. Vel eos sapiente id, consectetur quaerat distinctio neque eaque commodi, placeat deleniti laudantium. Nihil ut id vitae consequuntur iste dolorum eligendi, distinctio at modi ea, placeat pariatur, reiciendis cupiditate architecto quasi magnam voluptatibus vero. Molestias fugit ipsam quas neque, doloribus accusamus, possimus quidem dicta praesentium inventore ex omnis veritatis unde sit consequuntur harum tempore! Sit non debitis officiis, doloremque delectus qui modi iste ab at? Minima nam sapiente neque unde tempora, odit recusandae voluptatem nihil atque ex. Sint excepturi reiciendis nam, odio cum ut quibusdam libero nesciunt exercitationem nobis. Odit omnis est voluptates quam consequatur veritatis laborum temporibus ex amet voluptate? Quod, adipisci veniam. Nesciunt praesentium explicabo dolorem in vel, beatae odio velit laborum ex iste architecto a odit quod ipsam placeat hic suscipit nemo laudantium quasi exercitationem dolorum nostrum mollitia cumque. Quidem mollitia, reiciendis aperiam, earum expedita repellat rem dolorum suscipit aliquam molestias quibusdam officia assumenda neque?</p><p>Inventore nesciunt incidunt numquam minus architecto id possimus velit? Provident numquam magnam quia nobis repudiandae, aperiam unde fuga incidunt! Architecto dignissimos beatae rerum et sit delectus libero quisquam necessitatibus veritatis a nesciunt expedita corrupti harum illo, quo omnis quasi asperiores vero officia culpa quidem blanditiis nemo assumenda. Voluptatem perspiciatis vel consectetur provident dicta error ducimus quia at ipsum! Reprehenderit eum earum odit reiciendis cum tenetur! Ipsum, inventore. Expedita unde libero corrupti atque, officia corporis, enim aliquid ratione architecto similique est sed praesentium? Porro minus dolores nihil illum atque vero consequatur sapiente facere quidem ratione, molestias laboriosam quibusdam aspernatur, temporibus ullam, beatae necessitatibus dicta impedit dolor sed a? Id culpa fugit eos quos, dolorum quia eligendi impedit maiores. Molestiae libero distinctio aut quis! Temporibus fuga eligendi commodi eum consequuntur quo velit ratione molestiae voluptatum consectetur aliquam omnis molestias exercitationem reprehenderit illo repellat accusamus soluta natus corporis corrupti accusantium sunt, odit modi debitis? Nobis odio facere ratione quas ducimus quaerat reiciendis quos autem. Quidem ipsum quibusdam provident totam quasi necessitatibus quaerat numquam perspiciatis voluptate quis soluta, expedita dolore facere illo alias tempora neque, veniam est ad nemo maxime! Facilis numquam temporibus quia, autem provident sapiente repellat repellendus incidunt culpa molestiae animi aliquam voluptates velit odit ad tempora suscipit quos quibusdam? Recusandae, voluptatibus, beatae cumque laborum velit ipsum ea assumenda et quod est earum quibusdam commodi rerum mollitia incidunt consectetur dolore dolorum perspiciatis? Quasi velit quibusdam assumenda, officia adipisci aut numquam vitae modi. Odio aliquid perferendis eos non reprehenderit similique suscipit tempora voluptates!</p><p>Necessitatibus quasi ipsam minus labore illum cupiditate quidem doloremque nesciunt, consequatur aliquid harum nobis doloribus eum, commodi reiciendis odio in est fuga cum hic nostrum enim dolorum? Inventore tempore placeat, quisquam dolore esse excepturi ullam expedita deserunt sint eaque magni sequi dolorem assumenda error sed eveniet recusandae quaerat voluptatem id facilis, voluptatum corrupti officia nemo fugit. Iure voluptates repudiandae error qui obcaecati dolorum dicta totam harum iusto delectus, dolores ipsam nemo rem architecto earum a sint quam recusandae enim quia quibusdam atque distinctio quae saepe! Fuga recusandae modi repudiandae voluptas autem adipisci beatae voluptatibus accusantium minus, voluptatem quam consequuntur eligendi rerum veniam dignissimos voluptates delectus tempora quis. Architecto iure soluta at aut voluptatem delectus quae quasi! Est, asperiores voluptate qui necessitatibus cumque doloremque temporibus? Optio inventore nemo eligendi dolore esse ullam enim vitae aliquam, nostrum ad tenetur autem sed rem pariatur dolorum quasi quam ex odio deserunt ducimus! Sequi quas praesentium officia, earum accusantium mollitia unde labore ratione nemo impedit illum autem tenetur, libero aliquid. Impedit deleniti asperiores non sequi distinctio quo, assumenda laudantium explicabo eaque sunt sint error vitae quaerat culpa dolorum ut! Error, dolorem. Commodi unde corrupti officia, quibusdam quis odit eveniet quae necessitatibus voluptates eos doloribus esse reprehenderit minus quaerat quod accusamus, quia ducimus? Fuga officia illo, nostrum mollitia, corrupti quisquam magni eligendi reprehenderit enim, omnis sit ducimus hic? Inventore beatae sunt minima nulla adipisci fuga nemo iste, deleniti natus nihil illo aperiam eos quasi repellendus quod. Incidunt temporibus numquam, aperiam deserunt facere quidem doloremque cumque aspernatur.</p><p>Tempore odio corporis, voluptates maiores nisi in, officiis et labore quis omnis velit, cumque enim aliquid a cum ea deleniti tempora reiciendis porro. Laborum officiis assumenda quas, autem aliquid, consectetur corporis iure illum sequi molestiae dolorem praesentium fuga tempora neque aspernatur fugiat non! Corporis ratione optio atque amet esse facilis minima quasi nobis, sunt reprehenderit, laudantium, mollitia autem doloremque excepturi cum quo fugiat! Repellat illo et labore! Repellat tenetur natus debitis nobis nam voluptate commodi itaque quia, cupiditate eius saepe voluptatem soluta quod qui eligendi ut. Nesciunt, a aliquid blanditiis quibusdam ab, odit praesentium minima laboriosam placeat officiis sint inventore suscipit quia omnis pariatur odio ex quas tenetur officia sunt? A blanditiis, veritatis vel libero voluptatibus quae! Dolor velit iusto sunt officia perferendis. Quaerat aut assumenda praesentium, quos iste quasi voluptatum repellat consequuntur nobis voluptas eveniet velit neque sit qui facilis ipsum eius ea veritatis magnam blanditiis sunt vero accusamus. Fuga magnam quisquam recusandae fugit distinctio perferendis reiciendis fugiat alias voluptates maxime, obcaecati illo ea dolores culpa nobis neque ab corporis iusto sequi, blanditiis iure sed. Tenetur, sed ipsum! Soluta repudiandae doloremque incidunt temporibus rem fuga. Ad provident cumque facilis beatae sunt temporibus, sed blanditiis vitae quae corrupti pariatur aut officia eos recusandae, iusto, iste vero adipisci. Ipsum quibusdam consectetur doloribus aperiam! Deserunt, voluptatum impedit natus nemo nulla nesciunt minima, neque laborum quasi quisquam id omnis. Molestias soluta obcaecati eius similique cum fugiat maxime doloribus sit, sint pariatur! Reprehenderit qui facere necessitatibus quibusdam, veniam nulla illum harum consequatur adipisci itaque.</p>', 'eshyo-kakaya-to-zapis-v-bloge-i-zagolovok', 1643913502, 1644002153, 1, '/images/article-preview-default.png', NULL);

-- Дамп структуры для таблица mublog.mailer_sended_mails
DROP TABLE IF EXISTS `mailer_sended_mails`;
CREATE TABLE IF NOT EXISTS `mailer_sended_mails` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `headers` json NOT NULL,
  `result` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contains all sended mails from webform';

-- Дамп данных таблицы mublog.mailer_sended_mails: ~2 rows (приблизительно)
INSERT INTO `mailer_sended_mails` (`id`, `subject`, `message`, `timestamp`, `headers`, `result`) VALUES
	(1, 'Сообщение с сайта от пользователя 02.02 22:13', 'Отправлено в: 2022-02-02 22:13:20\r\nТекст сообщения:\r\nTest contact form message', 1643829200, '{"Date": "Wed, 02 Feb 2022 22:13:20 +0300", "From": "test@domain.zone", "Reply-To": "test@domain.zone", "X-Mailer": "PHP/8.1.1", "Timestamp": 1643829200}', 1),
	(2, 'Сообщение с сайта от пользователя 02.02 22:15', 'Отправлено в: 2022-02-02 22:15:38\r\nТекст сообщения:\r\nTest contact form message', 1643829338, '{"Date": "Wed, 02 Feb 2022 22:15:38 +0300", "From": "test@domain.zone", "Reply-To": "test@domain.zone", "X-Mailer": "PHP/8.1.1", "Timestamp": 1643829338}', 1);

-- Дамп структуры для таблица mublog.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'User unique id',
  `mail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User mail adress as login',
  `pwhash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Users'' password hash',
  `nickname` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User nickname',
  `registered` int unsigned NOT NULL DEFAULT '0' COMMENT 'User register timestamp',
  `usid` tinyint unsigned DEFAULT '2' COMMENT 'Users'' status id',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `fk_user_status_id` (`usid`),
  CONSTRAINT `fk_user_status_id` FOREIGN KEY (`usid`) REFERENCES `users_statuses_list` (`usid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.users: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.users_sessions
DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `uid` int unsigned NOT NULL COMMENT 'User unique id',
  `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User session token',
  `agent_hash` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User agent md5 hash',
  `browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `updated` int unsigned NOT NULL COMMENT 'Users'' last action timestamp',
  UNIQUE KEY `token` (`token`),
  KEY `uid` (`uid`),
  KEY `agent_hash` (`agent_hash`),
  KEY `uptime` (`updated`) USING BTREE,
  CONSTRAINT `fk_session_user_id` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.users_sessions: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.users_statuses_list
DROP TABLE IF EXISTS `users_statuses_list`;
CREATE TABLE IF NOT EXISTS `users_statuses_list` (
  `usid` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT 'User status unique id',
  `status` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status parameter',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User status readable name',
  PRIMARY KEY (`usid`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Дамп данных таблицы mublog.users_statuses_list: ~4 rows (приблизительно)
INSERT INTO `users_statuses_list` (`usid`, `status`, `label`) VALUES
	(1, 'anonym', 'Guest'),
	(2, 'user', 'Registered user'),
	(3, 'admin', 'Administrator user'),
	(4, 'master', 'Webmaster');

-- Дамп структуры для триггер mublog.articles_before_insert
DROP TRIGGER IF EXISTS `articles_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `articles_before_insert` BEFORE INSERT ON `articles` FOR EACH ROW BEGIN
   IF NEW.created IS NULL THEN
		SET NEW.created = UNIX_TIMESTAMP();
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Дамп структуры для триггер mublog.articles_before_update
DROP TRIGGER IF EXISTS `articles_before_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `articles_before_update` BEFORE UPDATE ON `articles` FOR EACH ROW BEGIN
   IF NEW.updated IS NULL THEN
		SET NEW.updated = UNIX_TIMESTAMP();
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Дамп структуры для триггер mublog.users_before_insert
DROP TRIGGER IF EXISTS `users_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `users_before_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
   IF NEW.registered IS NULL THEN
		SET NEW.registered = UNIX_TIMESTAMP();
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Дамп структуры для триггер mublog.users_sessions_before_insert
DROP TRIGGER IF EXISTS `users_sessions_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `users_sessions_before_insert` BEFORE INSERT ON `users_sessions` FOR EACH ROW BEGIN
   IF NEW.updated IS NULL THEN
		SET NEW.updated = UNIX_TIMESTAMP();
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Дамп структуры для триггер mublog.users_sessions_before_update
DROP TRIGGER IF EXISTS `users_sessions_before_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `users_sessions_before_update` BEFORE UPDATE ON `users_sessions` FOR EACH ROW BEGIN
   IF NEW.updated IS NULL THEN
		SET NEW.updated = UNIX_TIMESTAMP();
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

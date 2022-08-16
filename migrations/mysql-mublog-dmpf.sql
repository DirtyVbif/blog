-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.4.19-MariaDB - mariadb.org binary distribution
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

-- Дамп структуры для таблица mublog.dmpf_conditions
DROP TABLE IF EXISTS `dmpf_conditions`;
CREATE TABLE IF NOT EXISTS `dmpf_conditions` (
    `cid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(24) NOT NULL,
    `vtid` tinyint(3) unsigned NOT NULL DEFAULT 1,
    `parameters` varchar(56) DEFAULT NULL,
    `use_operator` tinyint(3) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`cid`),
    UNIQUE KEY `code` (`code`,`vtid`,`parameters`) USING BTREE,
    KEY `fk_dmpf_condition_value_type_id` (`vtid`) USING BTREE,
    CONSTRAINT `fk_dmpf_condition_value_type_id` FOREIGN KEY (`vtid`) REFERENCES `dmpf_value_types` (`vtid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='list of available poe loot filter condition';

-- Дамп данных таблицы mublog.dmpf_conditions: ~36 rows (приблизительно)
INSERT INTO `dmpf_conditions` (`cid`, `code`, `vtid`, `parameters`, `use_operator`) VALUES
                                                                                         (1, 'AreaLevel', 2, '0;100', 1),
                                                                                         (2, 'ItemLevel', 2, '0;100', 1),
                                                                                         (3, 'DropLevel', 2, '0;100', 1),
                                                                                         (4, 'Quality', 2, '1;n', 1),
                                                                                         (5, 'Rarity', 5, 'Normal;Magic;Rare;Unique', 1),
                                                                                         (6, 'Class', 1, 'dmpf_value_classes', 0),
                                                                                         (7, 'BaseType', 1, 'dmpf_value_base_types', 0),
                                                                                         (8, 'Prophecy', 1, 'dmpf_value_prophecies', 0),
                                                                                         (9, 'LinkedSockets', 2, '0;6', 1),
                                                                                         (10, 'SocketGroup', 4, NULL, 1),
                                                                                         (11, 'Sockets', 4, NULL, 1),
                                                                                         (12, 'Height', 2, '1;4', 1),
                                                                                         (13, 'Width', 2, '1;2', 1),
                                                                                         (14, 'HasExplicitMod', 1, 'dmpf_value_explicit_mods', 0),
                                                                                         (15, 'AnyEnchantment', 3, NULL, 0),
                                                                                         (16, 'HasEnchantment', 1, 'dmpf_value_enchantments', 0),
                                                                                         (17, 'EnchantmentPassiveNode', 1, 'dmpf_value_enchantments', 0),
                                                                                         (18, 'EnchantmentPassiveNum', 2, '0;n', 1),
                                                                                         (19, 'StackSize', 2, '1;n', 1),
                                                                                         (20, 'GemLevel', 2, '1;n', 1),
                                                                                         (21, 'GemQualityType', 5, 'Superior;Divergent;Anomalous;Phantasmal', 0),
                                                                                         (22, 'AlternateQuality', 3, NULL, 0),
                                                                                         (23, 'Replica', 3, NULL, 0),
                                                                                         (24, 'Identified', 3, NULL, 0),
                                                                                         (25, 'Corrupted', 3, NULL, 0),
                                                                                         (26, 'CorruptedMods', 2, '0;n', 1),
                                                                                         (27, 'Mirrored', 3, NULL, 0),
                                                                                         (28, 'ElderItem', 3, NULL, 0),
                                                                                         (29, 'ShaperItem', 3, NULL, 0),
                                                                                         (30, 'HasInfluence', 5, 'Shaper;Elder;Crusader;Hunter;Redeemer;Warlord;None', 0),
                                                                                         (31, 'FracturedItem', 3, NULL, 0),
                                                                                         (32, 'SynthesisedItem', 3, NULL, 0),
                                                                                         (33, 'ElderMap', 3, NULL, 0),
                                                                                         (34, 'ShapedMap', 3, NULL, 0),
                                                                                         (35, 'BlightedMap', 3, NULL, 0),
                                                                                         (36, 'MapTier', 2, '1;17', 1);

-- Дамп структуры для таблица mublog.dmpf_filters
DROP TABLE IF EXISTS `dmpf_filters`;
CREATE TABLE IF NOT EXISTS `dmpf_filters` (
    `fid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `label` varchar(128) NOT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `uid` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`fid`),
    KEY `fk_dmpf_filter_user_id` (`uid`),
    CONSTRAINT `fk_dmpf_filter_user_id` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filters: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_blocks
DROP TABLE IF EXISTS `dmpf_filter_blocks`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_blocks` (
    `fbid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `label` varchar(128) NOT NULL,
    PRIMARY KEY (`fbid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_blocks: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_blocks_items
DROP TABLE IF EXISTS `dmpf_filter_blocks_items`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_blocks_items` (
    `fbid` smallint(5) unsigned NOT NULL,
    `fiid` smallint(5) unsigned NOT NULL,
    `order` tinyint(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (`fbid`,`fiid`),
    KEY `fk_dmpf_filter_block_items_id` (`fiid`),
    CONSTRAINT `fk_dmpf_filter_block_items_id` FOREIGN KEY (`fiid`) REFERENCES `dmpf_filter_items` (`fiid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dmpf_filter_item_blocks_id` FOREIGN KEY (`fbid`) REFERENCES `dmpf_filter_blocks` (`fbid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_blocks_items: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_filters_sections
DROP TABLE IF EXISTS `dmpf_filter_filters_sections`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_filters_sections` (
    `fid` smallint(5) unsigned NOT NULL,
    `fsid` smallint(5) unsigned NOT NULL,
    `number` char(2) NOT NULL DEFAULT '01',
    PRIMARY KEY (`fid`,`fsid`),
    KEY `fk_dmpf_filter_filter_sections_id` (`fsid`),
    CONSTRAINT `fk_dmpf_filter_filter_sections_id` FOREIGN KEY (`fsid`) REFERENCES `dmpf_filter_sections` (`fsid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dmpf_filter_section_filters_id` FOREIGN KEY (`fid`) REFERENCES `dmpf_filters` (`fid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_filters_sections: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_items
DROP TABLE IF EXISTS `dmpf_filter_items`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_items` (
    `fiid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `show` tinyint(3) unsigned NOT NULL DEFAULT 0,
    `label` varchar(64) DEFAULT NULL,
    PRIMARY KEY (`fiid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_items: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_items_values
DROP TABLE IF EXISTS `dmpf_filter_items_values`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_items_values` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `fiid` smallint(5) unsigned NOT NULL,
    `cid` tinyint(3) unsigned DEFAULT NULL,
    `sid` tinyint(3) unsigned DEFAULT NULL,
    `value` varchar(512) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fiid_cid_sid` (`fiid`,`cid`,`sid`),
    KEY `fk_dmpf_filter_item_conditions_id` (`cid`),
    KEY `fk_dmpf_filter_item_styles_id` (`sid`),
    CONSTRAINT `fk_dmpf_filter_item_conditions_id` FOREIGN KEY (`cid`) REFERENCES `dmpf_conditions` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dmpf_filter_item_styles_id` FOREIGN KEY (`sid`) REFERENCES `dmpf_styles` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dmpf_filter_value_items_id` FOREIGN KEY (`fiid`) REFERENCES `dmpf_filter_items` (`fiid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_items_values: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_sections
DROP TABLE IF EXISTS `dmpf_filter_sections`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_sections` (
    `fsid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `label` varchar(128) NOT NULL,
    PRIMARY KEY (`fsid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_sections: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_filter_sections_blocks
DROP TABLE IF EXISTS `dmpf_filter_sections_blocks`;
CREATE TABLE IF NOT EXISTS `dmpf_filter_sections_blocks` (
    `fsid` smallint(5) unsigned NOT NULL,
    `fbid` smallint(5) unsigned NOT NULL,
    `number` char(2) DEFAULT '00',
    PRIMARY KEY (`fsid`,`fbid`),
    KEY `fk_dmpf_filter_section_blocks_id` (`fbid`),
    CONSTRAINT `fk_dmpf_filter_block_sections_id` FOREIGN KEY (`fsid`) REFERENCES `dmpf_filter_sections` (`fsid`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dmpf_filter_section_blocks_id` FOREIGN KEY (`fbid`) REFERENCES `dmpf_filter_blocks` (`fbid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_filter_sections_blocks: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_styles
DROP TABLE IF EXISTS `dmpf_styles`;
CREATE TABLE IF NOT EXISTS `dmpf_styles` (
    `sid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(24) NOT NULL,
    `vtid` tinyint(3) unsigned NOT NULL DEFAULT 1,
    `parameters` varchar(40) DEFAULT NULL,
    PRIMARY KEY (`sid`),
    UNIQUE KEY `code_vid_parameters` (`code`,`vtid`,`parameters`) USING BTREE,
    KEY `fk_dmpf_style_value_type_id` (`vtid`) USING BTREE,
    CONSTRAINT `fk_dmpf_style_value_type_id` FOREIGN KEY (`vtid`) REFERENCES `dmpf_value_types` (`vtid`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='list of available poe filter styles';

-- Дамп данных таблицы mublog.dmpf_styles: ~15 rows (приблизительно)
INSERT INTO `dmpf_styles` (`sid`, `code`, `vtid`, `parameters`) VALUES
                                                                     (9, 'CustomAlertSound', 4, NULL),
                                                                     (7, 'DisableDropSound', 7, NULL),
                                                                     (8, 'EnableDropSound', 7, NULL),
                                                                     (15, 'MinimapIcon', 1, 'dmpf_value_colors'),
                                                                     (16, 'MinimapIcon', 1, 'dmpf_value_shapes'),
                                                                     (14, 'MinimapIcon', 5, '-1:disable;0:small;1:medium;2:large'),
                                                                     (6, 'PlayAlertSound', 2, '0;300'),
                                                                     (5, 'PlayAlertSound', 2, '1;16'),
                                                                     (13, 'PlayAlertSoundPositional', 2, '0;300'),
                                                                     (12, 'PlayAlertSoundPositional', 2, '1;16'),
                                                                     (11, 'PlayEffect', 1, 'dmpf_value_colors'),
                                                                     (3, 'SetBackgroundColor', 6, NULL),
                                                                     (1, 'SetBorderColor', 6, NULL),
                                                                     (4, 'SetFontSize', 1, NULL),
                                                                     (2, 'SetTextColor', 6, NULL);

-- Дамп структуры для таблица mublog.dmpf_value_base_types
DROP TABLE IF EXISTS `dmpf_value_base_types`;
CREATE TABLE IF NOT EXISTS `dmpf_value_base_types` (
    `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(32) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_base_types: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_value_classes
DROP TABLE IF EXISTS `dmpf_value_classes`;
CREATE TABLE IF NOT EXISTS `dmpf_value_classes` (
    `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(36) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_classes: ~134 rows (приблизительно)
INSERT INTO `dmpf_value_classes` (`id`, `code`) VALUES
                                                     (48, 'Abyss Jewel'),
                                                     (112, 'AbyssJewel'),
                                                     (87, 'Active Skill Gem'),
                                                     (19, 'Active Skill Gems'),
                                                     (73, 'Amulet'),
                                                     (5, 'Amulets'),
                                                     (142, 'Armor'),
                                                     (57, 'Atlas Region Upgrade Item'),
                                                     (120, 'AtlasRegionUpgradeItem'),
                                                     (90, 'Belt'),
                                                     (22, 'Belts'),
                                                     (66, 'Blueprint'),
                                                     (91, 'Body Armour'),
                                                     (25, 'Body Armours'),
                                                     (24, 'Boots'),
                                                     (82, 'Bow'),
                                                     (14, 'Bows'),
                                                     (75, 'Claw'),
                                                     (7, 'Claws'),
                                                     (61, 'Contract'),
                                                     (32, 'Critical Utility Flasks'),
                                                     (4, 'Currency'),
                                                     (76, 'Dagger'),
                                                     (8, 'Daggers'),
                                                     (50, 'Delve Socketable Currency'),
                                                     (56, 'Delve Stackable Socketable Currency'),
                                                     (114, 'DelveSocketableCurrency'),
                                                     (119, 'DelveStackableSocketableCurrency'),
                                                     (40, 'Divination Card'),
                                                     (104, 'DivinationCard'),
                                                     (69, 'Expedition Logbook'),
                                                     (134, 'ExpeditionLogbook'),
                                                     (35, 'Fishing Rods'),
                                                     (100, 'FishingRod'),
                                                     (135, 'Flasks'),
                                                     (140, 'Gems'),
                                                     (23, 'Gloves'),
                                                     (59, 'Harvest Seed'),
                                                     (124, 'HarvestInfrastructure'),
                                                     (122, 'HarvestObject'),
                                                     (125, 'HarvestPlantBooster'),
                                                     (123, 'HarvestSeed'),
                                                     (65, 'Heist Brooch'),
                                                     (64, 'Heist Cloak'),
                                                     (62, 'Heist Gear'),
                                                     (68, 'Heist Target'),
                                                     (63, 'Heist Tool'),
                                                     (131, 'HeistBlueprint'),
                                                     (126, 'HeistContract'),
                                                     (130, 'HeistEquipmentReward'),
                                                     (128, 'HeistEquipmentTool'),
                                                     (129, 'HeistEquipmentUtility'),
                                                     (127, 'HeistEquipmentWeapon'),
                                                     (133, 'HeistObjective'),
                                                     (92, 'Helmet'),
                                                     (26, 'Helmets'),
                                                     (37, 'Hideout Doodads'),
                                                     (102, 'HideoutDoodad'),
                                                     (3, 'Hybrid Flasks'),
                                                     (72, 'HybridFlask'),
                                                     (51, 'Incubator'),
                                                     (49, 'Incursion Item'),
                                                     (113, 'IncursionItem'),
                                                     (39, 'Jewel'),
                                                     (137, 'Jewellery'),
                                                     (41, 'Labyrinth Item'),
                                                     (43, 'Labyrinth Map Item'),
                                                     (42, 'Labyrinth Trinket'),
                                                     (105, 'LabyrinthItem'),
                                                     (107, 'LabyrinthMapItem'),
                                                     (106, 'LabyrinthTrinket'),
                                                     (109, 'Leaguestone'),
                                                     (45, 'Leaguestones'),
                                                     (1, 'Life Flasks'),
                                                     (70, 'LifeFlask'),
                                                     (2, 'Mana Flasks'),
                                                     (71, 'ManaFlask'),
                                                     (99, 'Map'),
                                                     (36, 'Map Fragments'),
                                                     (101, 'MapFragment'),
                                                     (33, 'Maps'),
                                                     (58, 'Metamorph Sample'),
                                                     (121, 'MetamorphosisDNA'),
                                                     (103, 'Microtransaction'),
                                                     (38, 'Microtransactions'),
                                                     (44, 'Misc Map Items'),
                                                     (108, 'MiscMapItem'),
                                                     (141, 'Off-hand'),
                                                     (80, 'One Hand Axe'),
                                                     (12, 'One Hand Axes'),
                                                     (81, 'One Hand Mace'),
                                                     (13, 'One Hand Maces'),
                                                     (78, 'One Hand Sword'),
                                                     (10, 'One Hand Swords'),
                                                     (138, 'One Handed Weapon'),
                                                     (136, 'Other'),
                                                     (46, 'Pantheon Soul'),
                                                     (110, 'PantheonSoul'),
                                                     (47, 'Piece'),
                                                     (29, 'Quest Items'),
                                                     (95, 'QuestItem'),
                                                     (89, 'Quiver'),
                                                     (21, 'Quivers'),
                                                     (74, 'Ring'),
                                                     (6, 'Rings'),
                                                     (117, 'Rune Dagger'),
                                                     (54, 'Rune Daggers'),
                                                     (96, 'Sceptre'),
                                                     (30, 'Sceptres'),
                                                     (60, 'Seed Enhancer'),
                                                     (52, 'Shard'),
                                                     (53, 'Shard Heart'),
                                                     (93, 'Shield'),
                                                     (27, 'Shields'),
                                                     (28, 'Stackable Currency'),
                                                     (94, 'StackableCurrency'),
                                                     (83, 'Staff'),
                                                     (15, 'Staves'),
                                                     (88, 'Support Skill Gem'),
                                                     (20, 'Support Skill Gems'),
                                                     (79, 'Thrusting One Hand Sword'),
                                                     (11, 'Thrusting One Hand Swords'),
                                                     (132, 'Trinket'),
                                                     (67, 'Trinkets'),
                                                     (85, 'Two Hand Axe'),
                                                     (17, 'Two Hand Axes'),
                                                     (86, 'Two Hand Mace'),
                                                     (18, 'Two Hand Maces'),
                                                     (84, 'Two Hand Sword'),
                                                     (16, 'Two Hand Swords'),
                                                     (139, 'Two Handed Weapon'),
                                                     (34, 'Unarmed'),
                                                     (111, 'UniqueFragment'),
                                                     (115, 'UniqueShard'),
                                                     (116, 'UniqueShardBase'),
                                                     (31, 'Utility Flasks'),
                                                     (97, 'UtilityFlask'),
                                                     (98, 'UtilityFlaskCritical'),
                                                     (77, 'Wand'),
                                                     (9, 'Wands'),
                                                     (118, 'Warstaff'),
                                                     (55, 'Warstaves');

-- Дамп структуры для таблица mublog.dmpf_value_colots
DROP TABLE IF EXISTS `dmpf_value_colots`;
CREATE TABLE IF NOT EXISTS `dmpf_value_colots` (
    `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` char(12) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_colots: ~11 rows (приблизительно)
INSERT INTO `dmpf_value_colots` (`id`, `code`) VALUES
                                                    (3, 'Blue'),
                                                    (4, 'Brown'),
                                                    (7, 'Cyan'),
                                                    (2, 'Green'),
                                                    (8, 'Grey'),
                                                    (9, 'Orange'),
                                                    (10, 'Pink'),
                                                    (11, 'Purple'),
                                                    (1, 'Red'),
                                                    (5, 'White'),
                                                    (6, 'Yellow');

-- Дамп структуры для таблица mublog.dmpf_value_enchantments
DROP TABLE IF EXISTS `dmpf_value_enchantments`;
CREATE TABLE IF NOT EXISTS `dmpf_value_enchantments` (
    `id` smallint(5) unsigned NOT NULL DEFAULT 0,
    `code` varchar(32) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_enchantments: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_value_explicit_mods
DROP TABLE IF EXISTS `dmpf_value_explicit_mods`;
CREATE TABLE IF NOT EXISTS `dmpf_value_explicit_mods` (
    `id` smallint(5) unsigned NOT NULL DEFAULT 0,
    `code` varchar(32) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_explicit_mods: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_value_prophecies
DROP TABLE IF EXISTS `dmpf_value_prophecies`;
CREATE TABLE IF NOT EXISTS `dmpf_value_prophecies` (
    `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(32) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_prophecies: ~0 rows (приблизительно)

-- Дамп структуры для таблица mublog.dmpf_value_shapes
DROP TABLE IF EXISTS `dmpf_value_shapes`;
CREATE TABLE IF NOT EXISTS `dmpf_value_shapes` (
    `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` char(16) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_shapes: ~12 rows (приблизительно)
INSERT INTO `dmpf_value_shapes` (`id`, `code`) VALUES
                                                    (1, 'Circle'),
                                                    (7, 'Cross'),
                                                    (2, 'Diamond'),
                                                    (3, 'Hexagon'),
                                                    (10, 'Kite'),
                                                    (8, 'Moon'),
                                                    (11, 'Pentagon'),
                                                    (9, 'Raindrop'),
                                                    (4, 'Square'),
                                                    (5, 'Start'),
                                                    (6, 'Triangle'),
                                                    (12, 'UpsideDownHouse');

-- Дамп структуры для таблица mublog.dmpf_value_types
DROP TABLE IF EXISTS `dmpf_value_types`;
CREATE TABLE IF NOT EXISTS `dmpf_value_types` (
    `vtid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(12) NOT NULL,
    PRIMARY KEY (`vtid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы mublog.dmpf_value_types: ~7 rows (приблизительно)
INSERT INTO `dmpf_value_types` (`vtid`, `code`) VALUES
                                                     (1, 'datalist'),
                                                     (2, 'range'),
                                                     (3, 'boolean'),
                                                     (4, 'custom'),
                                                     (5, 'list'),
                                                     (6, 'color'),
                                                     (7, 'none');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

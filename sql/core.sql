/*
SQLyog Ultimate v9.33 GA
MySQL - 5.6.9-rc-log : Database - empty
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `action` */

DROP TABLE IF EXISTS `action`;

CREATE TABLE `action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `rowRequired` enum('y','n') NOT NULL DEFAULT 'y',
  `type` enum('p','s','o') NOT NULL DEFAULT 'p',
  `display` tinyint(1) NOT NULL DEFAULT '1',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `rowRequired` (`rowRequired`),
  KEY `type` (`type`),
  KEY `toggle1` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

/*Data for the table `action` */

insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (1,'Список','index','n','s',0,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (2,'Детали','form','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (3,'Сохранить','save','y','s',0,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (4,'Удалить','delete','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (5,'Выше','up','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (6,'Ниже','down','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (7,'Статус','toggle','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (18,'Обновить кэш','cache','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (19,'Обновить sitemap.xml','sitemap','n','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (20,'Авторизация','login','y','o',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (33,'Автор','author','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (34,'PHP','php','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (35,'JS','js','y','s',1,'y');
insert  into `action`(`id`,`title`,`alias`,`rowRequired`,`type`,`display`,`toggle`) values (36,'Экспорт','export','y','s',1,'y');

/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profileId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `profileId` (`profileId`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `admin` */

insert  into `admin`(`id`,`profileId`,`title`,`email`,`password`,`toggle`) values (1,1,'Павел Перминов','pavel.perminov.23@gmail.com','*8E1219CD047401C6FEAC700B47F5DA846A57ABD4','y');
insert  into `admin`(`id`,`profileId`,`title`,`email`,`password`,`toggle`) values (14,12,'Василий Теркин','vasily.terkin@gmail.com','*85012D571AE8732730DE98314CF04A3BB2269508','n');

/*Table structure for table `alteredfield` */

DROP TABLE IF EXISTS `alteredfield`;

CREATE TABLE `alteredfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  `mode` enum('hidden','readonly','inherit','regular','required') NOT NULL DEFAULT 'inherit',
  `title` varchar(255) NOT NULL DEFAULT '',
  `impact` enum('all','only','except') NOT NULL DEFAULT 'all',
  `profileIds` varchar(255) NOT NULL DEFAULT '',
  `rename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `impact` (`impact`),
  KEY `profileIds` (`profileIds`),
  KEY `mode` (`mode`)
) ENGINE=MyISAM AUTO_INCREMENT=216 DEFAULT CHARSET=utf8;

/*Data for the table `alteredfield` */

insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (84,146,961,'','hidden','Аккаунт активирован','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (85,146,962,'','hidden','Код активации','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (43,146,1108,'','hidden','Настройки','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (94,146,1577,'','hidden','Код','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (93,146,1576,'','hidden','Дата последнего запроса','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (92,146,1575,'','hidden','Смена пароля','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (91,146,1162,'','hidden','ID пользователя в этой соц.сети','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (90,146,1163,'','hidden','Какая','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (89,146,1161,'','hidden','Социальные сети','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (88,146,698,'','hidden','Подписался на рассылку','all','','');
insert  into `alteredfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`mode`,`title`,`impact`,`profileIds`,`rename`) values (215,232,1515,'','readonly','Тип','except','1','');

/*Table structure for table `changelog` */

DROP TABLE IF EXISTS `changelog`;

CREATE TABLE `changelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `key` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `was` text NOT NULL,
  `now` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `monthId` int(11) NOT NULL DEFAULT '0',
  `changerType` int(11) NOT NULL DEFAULT '0',
  `changerId` int(11) NOT NULL DEFAULT '0',
  `profileId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `key` (`key`),
  KEY `fieldId` (`fieldId`),
  KEY `monthId` (`monthId`),
  KEY `changerType` (`changerType`),
  KEY `changerId` (`changerId`),
  KEY `profileId` (`profileId`),
  FULLTEXT KEY `was` (`was`),
  FULLTEXT KEY `now` (`now`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `changelog` */

/*Table structure for table `columntype` */

DROP TABLE IF EXISTS `columntype`;

CREATE TABLE `columntype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `canStoreRelation` enum('y','n') NOT NULL DEFAULT 'n',
  `elementId` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `canStoreRelation` (`canStoreRelation`),
  KEY `elementId` (`elementId`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `columntype` */

insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (1,'Строка','VARCHAR(255)','y','22,23,1,7');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (3,'Число','INT(11)','y','3,21,4,5,23,1,18');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (4,'Текст','TEXT','y','6,7,8,13,1');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (5,'Цена','DECIMAL(11,2)','n','24');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (6,'Дата','DATE','n','12');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (7,'Год','YEAR','n','');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (8,'Время','TIME','n','17');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (9,'Момент','DATETIME','n','19');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (10,'Одно значение из набора','ENUM','n','5,23');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (11,'Набор значений','SET','n','23,1,6,7');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (12,'Правда/Ложь','BOOLEAN','n','9');
insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (13,'Цвет','VARCHAR(10)','n','11');

/*Table structure for table `consider` */

DROP TABLE IF EXISTS `consider`;

CREATE TABLE `consider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `consider` int(11) NOT NULL DEFAULT '0',
  `foreign` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `fieldId` (`fieldId`),
  KEY `consider` (`consider`),
  KEY `foreign` (`foreign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `consider` */

/*Table structure for table `element` */

DROP TABLE IF EXISTS `element`;

CREATE TABLE `element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `storeRelationAbility` set('none','one','many') NOT NULL DEFAULT 'none',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `storeRelationAbility` (`storeRelationAbility`),
  KEY `storeRelationAbility_2` (`storeRelationAbility`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

/*Data for the table `element` */

insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (1,'Строка','string','none,many',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (4,'Приоритет отображения','move','none',1);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (5,'Радио-кнопки','radio','one',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (6,'Текст','textarea','none,many',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (7,'Чекбоксы','multicheck','many',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (9,'Чекбокс','check','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (11,'Цвет','color','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (12,'Календарь','calendar','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (13,'HTML-редактор','html','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (14,'Файл','upload','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (16,'Группа полей','span','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (17,'Время','time','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (18,'Число','number','none,one',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (19,'Момент','datetime','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (22,'Скрытое поле','hidden','none',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (23,'Список','combo','one,many',0);
insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (24,'Цена','price','none',0);

/*Table structure for table `entity` */

DROP TABLE IF EXISTS `entity`;

CREATE TABLE `entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `extends` varchar(255) NOT NULL DEFAULT 'Indi_Db_Table',
  `system` enum('n','y','o') NOT NULL DEFAULT 'n',
  `useCache` tinyint(1) NOT NULL DEFAULT '0',
  `titleFieldId` int(11) NOT NULL DEFAULT '0',
  `spaceScheme` enum('none','date','datetime','date-time','date-timeId','date-dayQty','datetime-minuteQty','date-time-minuteQty','date-timeId-minuteQty','date-timespan') NOT NULL DEFAULT 'none',
  `spaceFields` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `system` (`system`),
  KEY `titleFieldId` (`titleFieldId`),
  KEY `spaceScheme` (`spaceScheme`),
  KEY `spaceFields` (`spaceFields`)
) ENGINE=MyISAM AUTO_INCREMENT=314 DEFAULT CHARSET=utf8;

/*Data for the table `entity` */

insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (1,'Тип столбца','columnType','Indi_Db_Table','y',0,1,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (2,'Сущность','entity','Indi_Db_Table','y',1,4,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (3,'Раздел','section','Indi_Db_Table','y',0,20,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (4,'Элемент управления','element','Indi_Db_Table','y',0,64,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (5,'Поле','field','Indi_Db_Table','y',1,7,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (6,'Значение из набора','enumset','Indi_Db_Table','y',0,16,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (7,'Действие','action','Indi_Db_Table','y',0,31,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (8,'Действие в разделе','section2action','Indi_Db_Table','y',0,27,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (9,'Столбец грида','grid','Indi_Db_Table','y',0,34,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (10,'Роль','profile','Indi_Db_Table','y',0,36,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (11,'Пользователь CMS','admin','Indi_Db_Table','y',0,39,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (20,'Копия','resize','Indi_Db_Table','y',0,107,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (25,'Статическая страница','staticpage','Indi_Db_Table','o',0,131,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (90,'Параметр настройки элемента управления','possibleElementParam','Indi_Db_Table','y',0,472,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (91,'Параметр настройки элемента управления, в контексте поля сущности','param','Indi_Db_Table','y',0,477,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (101,'Раздел фронтенда','fsection','Indi_Db_Table','y',1,559,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (195,'Фильтр','search','Indi_Db_Table','y',0,1443,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (128,'Фидбэк','feedback','Indi_Db_Table','o',0,678,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (129,'Подписчик','subscriber','Indi_Db_Table','o',0,682,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (130,'Пользователь','user','Indi_Db_Table','o',0,685,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (146,'Действие, возможное для использования в разделе фронтенда','faction','Indi_Db_Table','y',1,857,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (147,'Действие в разделе фронтенда','fsection2faction','Indi_Db_Table','y',1,860,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (307,'Язык','lang','Indi_Db_Table','y',0,2236,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (160,'Посетитель','visitor','Indi_Db_Table','o',0,1100,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (162,'Компонент SEO-урла','url','Indi_Db_Table','y',0,0,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (171,'Поле, измененное в рамках раздела','alteredField','Indi_Db_Table','y',0,1342,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (204,'Статический элемент','staticblock','Indi_Db_Table','o',0,1485,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (205,'Пункт меню','menu','Indi_Db_Table','o',0,1490,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (301,'Компонент содержимого meta-тега','metatag','Indi_Db_Table','y',0,0,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (309,'Уведомление','notice','Indi_Db_Table','y',0,2254,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (310,'Получатель уведомлений','noticeGetter','Indi_Db_Table','y',0,2275,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (308,'Зависимость','consider','Indi_Db_Table','y',0,2247,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (311,'Год','year','Indi_Db_Table','y',0,2286,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (312,'Месяц','month','Indi_Db_Table','y',0,2289,'none','');
insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`,`spaceScheme`,`spaceFields`) values (313,'Корректировка','changeLog','Indi_Db_Table','y',0,2296,'none','');

/*Table structure for table `enumset` */

DROP TABLE IF EXISTS `enumset`;

CREATE TABLE `enumset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=1061 DEFAULT CHARSET=utf8;

/*Data for the table `enumset` */

insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1,3,'Нет','n',1);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (2,3,'Да','y',2);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (5,22,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',5);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (6,22,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',6);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (9,29,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',9);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (10,29,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',10);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (11,37,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',11);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (12,37,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',12);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (13,42,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',13);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (14,42,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',14);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (62,66,'Нет','none',62);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (63,66,'Только с одним значением ключа','one',63);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (64,66,'С набором значений ключей','many',64);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (87,111,'Поменять, но с сохранением пропорций','p',87);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (88,111,'Поменять','c',88);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (89,111,'Не менять','o',89);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (91,114,'Ширины','width',91);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (92,114,'Высоты','height',92);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (95,137,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',95);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (96,137,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключена','n',96);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (112,345,'Да','y',112);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (113,345,'Нет','n',113);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (122,0,'Всем друзьям, кроме указанных в разделе \"Исключения из правил доступа на просмотр блога\"','ae',122);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (162,455,'Переменная сущность','e',294);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (163,455,'Фильтрация','с',165);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (181,470,'Нет','none',164);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (183,470,'Да, для энного количества значений ключей','many',296);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (184,470,'Да, но для только одного значения ключа','one',295);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (187,455,'Отсутствует','u',148);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (213,557,'По возрастанию','ASC',297);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (214,557,'По убыванию','DESC',182);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (219,594,'Да','y',299);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (220,594,'Нет','n',300);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (227,612,'Проектная','n',301);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (228,612,'<span style=\'color: red\'>Системная</span>','y',186);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (241,689,'Мужской','m',427);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (242,689,'Женский','f',309);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (572,1365,'<font color=lime>Типовое</font>','o',461);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (571,1365,'<font color=red>Системное</font>','s',460);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (570,1365,'Проектное','p',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (580,1445,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (581,1445,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',464);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (979,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',979);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (980,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',980);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (981,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',981);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (982,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',982);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (328,0,'Очень плохо','1',254);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (480,1040,'Одностроковый','s',408);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (478,1027,'Для jQuery.post()','j',407);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (479,1040,'Обычный','r',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (477,1027,'Обычное','r',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (574,1366,'<font color=red>Системный</font>','s',462);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (573,1366,'Проектный','p',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (575,1366,'<font color=lime>Типовой</font>','o',463);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (458,1009,'SQL-выражению','e',396);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (457,1009,'Одному из имеющихся столбцов','c',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (459,1011,'По возрастанию','ASC',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (460,1011,'По убыванию','DESC',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (484,1074,'Над записью','r',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (485,1074,'Над набором записей','rs',411);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (486,1074,'Только независимые множества, если нужно','n',412);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (509,689,'Не&nbsp;указан','n',192);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (567,1364,'Проектное','p',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (568,1364,'<font color=red>Системное</font>','s',458);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (569,1364,'<font color=lime>Типовое</font>','o',459);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (516,1163,'Никакая','n',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (517,1163,'Facebook','fb',432);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (518,1163,'Вконтакте','vk',433);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (519,1163,'Twitter','tw',434);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (976,2194,'many','many',976);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (977,2193,'Откл. поле','171',977);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (975,2194,'one','one',975);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (973,2193,'действие в разделе','8',973);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (972,2193,'действие','7',972);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (969,2176,'Запись','row',969);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (968,2176,'Действие','action',968);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (967,2176,'Раздел','section',967);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (974,2194,'none','none',974);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (566,612,'<font color=lime>Типовая</font>','o',457);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (582,1488,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (583,1488,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',465);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (584,1491,'Нет','n',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (585,1491,'Да','y',466);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (586,1494,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (587,1494,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',467);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (594,1515,'HTML','html',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (595,1515,'Строка','string',471);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (596,1515,'Текст','textarea',472);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (597,1495,'Да','y',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (598,1495,'Нет','n',473);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (608,1533,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',478);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (607,1533,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (962,2172,'Title','title',962);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (963,2172,'Keywords','keywords',963);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (964,2172,'Description','description',964);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (965,2173,'Статический','static',965);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (966,2173,'Динамический','dynamic',966);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (960,2159,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (961,2159,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',489);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (983,2202,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',983);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (984,2202,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',984);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (985,2203,'Всем пользователям','all',985);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (986,2203,'Только выбранным','only',986);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (987,2203,'Всем, кроме выбранных','except',987);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (988,2205,'Всем пользователям','all',988);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (989,2205,'Только выбранным','only',989);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (990,2205,'Всем, кроме выбранных','except',990);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (991,2207,'Все пользователи','all',991);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (992,2207,'Только выбранные','only',992);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (993,2207,'Все, кроме выбранных','except',993);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (994,2210,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','0',994);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (995,2210,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','1',995);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (996,2212,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Авто','auto',996);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (997,2212,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Отображать','yes',997);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (998,2212,'<span class=\"i-color-box\" style=\"background: red;\"></span>Не отображать','no',998);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (999,2213,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Авто','auto',999);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1000,2213,'<span class=\"i-color-box\" style=\"background: yellow;\"></span>Отдельным запросом','yes',1000);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1001,2213,'<span class=\"i-color-box\" style=\"background: lime;\"></span>В том же запросе','no',1001);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1002,2214,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','auto',1002);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1003,2214,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1003);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1036,2267,'Увеличение','inc',1036);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1037,2267,'Уменьшение','dec',1037);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1032,2258,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',1032);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1033,2258,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',1033);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1034,2261,'Одинаковое для всех получателей','event',1034);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1035,2261,'Неодинаковое, зависит от получателя','getter',1035);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1010,2238,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1010);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1011,2238,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1011);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1012,2239,'<span class=\"i-color-box\" style=\"background: white;\"></span>Выключена','n',1012);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1013,2239,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',1013);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1014,2241,'Существующей','existing',1014);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1015,2241,'Новой','new',1015);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1016,2241,'Любой','any',1016);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1017,2243,'Нет','none',1017);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1018,2243,'DATE','date',1018);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1019,2243,'DATETIME','datetime',1019);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1020,2243,'DATE, TIME','date-time',1020);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1021,2243,'DATE, timeId','date-timeId',1021);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1022,2243,'DATE, dayQty','date-dayQty',1022);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1023,2243,'DATETIME, minuteQty','datetime-minuteQty',1023);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1024,2243,'DATE, TIME, minuteQty','date-time-minuteQty',1024);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1025,2243,'DATE, timeId, minuteQty','date-timeId-minuteQty',1025);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1026,2243,'DATE, hh:mm-hh:mm','date-timespan',1026);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1027,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',1031);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1028,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',1030);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1029,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/inherit.png);\"></span>Без изменений','inherit',1027);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1030,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',1028);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1031,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',1029);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1038,2267,'Изменение','evt',1038);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1039,2276,'Общий','event',1039);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1040,2276,'Раздельный','getter',1040);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1041,2281,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1041);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1042,2281,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1042);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1043,2282,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1043);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1044,2282,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1044);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1045,2283,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1045);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1046,2283,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1046);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1047,2285,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1047);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1048,2285,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1048);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1049,2288,'Январь','01',1049);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1050,2288,'Февраль','02',1050);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1051,2288,'Март','03',1051);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1052,2288,'Апрель','04',1052);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1053,2288,'Май','05',1053);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1054,2288,'Июнь','06',1054);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1055,2288,'Июль','07',1055);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1056,2288,'Август','08',1056);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1057,2288,'Сентябрь','09',1057);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1058,2288,'Октябрь','10',1058);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1059,2288,'Ноябрь','11',1059);
insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`move`) values (1060,2288,'Декабрь','12',1060);

/*Table structure for table `faction` */

DROP TABLE IF EXISTS `faction`;

CREATE TABLE `faction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `maintenance` enum('r','rs','n') NOT NULL DEFAULT 'r',
  `type` enum('o','s','p') NOT NULL DEFAULT 'p',
  PRIMARY KEY (`id`),
  KEY `maintenance` (`maintenance`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

/*Data for the table `faction` */

insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (1,'По умолчанию','index','rs','s');
insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (2,'Просмотр','details','r','s');
insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (3,'Изменить','form','r','s');
insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (5,'Добавить','create','n','s');
insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (6,'Активация аккаунта','activation','n','o');
insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (36,'Регистрация','registration','n','o');
insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (37,'Восстановление доступа','changepasswd','n','o');

/*Table structure for table `feedback` */

DROP TABLE IF EXISTS `feedback`;

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

/*Data for the table `feedback` */

/*Table structure for table `field` */

DROP TABLE IF EXISTS `field`;

CREATE TABLE `field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `columnTypeId` int(11) NOT NULL DEFAULT '0',
  `elementId` int(11) NOT NULL DEFAULT '0',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  `relation` int(11) NOT NULL DEFAULT '0',
  `satellite` int(11) NOT NULL DEFAULT '0',
  `dependency` enum('e','с','u') NOT NULL DEFAULT 'u',
  `storeRelationAbility` enum('none','many','one') NOT NULL DEFAULT 'none',
  `alternative` varchar(255) NOT NULL DEFAULT '',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `satellitealias` varchar(255) NOT NULL DEFAULT '',
  `mode` enum('regular','required','readonly','hidden') NOT NULL DEFAULT 'regular',
  `tooltip` text NOT NULL,
  `l10n` enum('n','y') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `columnTypeId` (`columnTypeId`),
  KEY `elementId` (`elementId`),
  KEY `relation` (`relation`),
  KEY `satellite` (`satellite`),
  KEY `dependency` (`dependency`),
  KEY `storeRelationAbility` (`storeRelationAbility`),
  KEY `mode` (`mode`),
  KEY `l10n` (`l10n`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=2301 DEFAULT CHARSET=utf8;

/*Data for the table `field` */

insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1,1,'Наименование','title',1,1,'',1,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2,1,'Тип столбца MySQL','type',1,1,'',2,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (3,1,'Пригоден для хранения внешних ключей','canStoreRelation',10,5,'n',3,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (4,2,'Наименование','title',1,1,'',4,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (5,2,'Таблица БД','table',1,1,'',5,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (6,5,'Сущность, в структуру которой входит это поле','entityId',3,23,'0',6,2,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (7,5,'Наименование поля','title',1,1,'',7,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (8,5,'Наименование соответствующего полю столбца в  таблице БД','alias',1,1,'',8,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (9,5,'Тип столбца MySQL','columnTypeId',3,23,'0',12,1,10,'с','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (10,5,'Элемент управления','elementId',3,23,'0',11,4,470,'с','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (11,5,'Значение по умолчанию','defaultValue',1,1,'',13,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (12,5,'Ключи какой сущности будут храниться в этом поле','relation',3,23,'0',428,2,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (14,5,'Положение в списке','move',3,4,'0',414,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (15,6,'Поле','fieldId',3,23,'0',15,5,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (16,6,'Наименование','title',4,1,'',16,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (17,6,'Псевдоним','alias',1,1,'',17,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (18,3,'Подчинен разделу','sectionId',3,23,'0',19,3,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (19,3,'Cущность, закрепленная за разделом','entityId',3,23,'0',25,2,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2198,195,'Содержательность','consistence',12,9,'1',2198,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (20,3,'Наименование','title',1,1,'',18,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (21,3,'Контроллер','alias',1,1,'',21,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (22,3,'Статус','toggle',10,5,'y',20,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (23,3,'Положение в списке','move',3,4,'',443,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (25,3,'Записей на странице','rowsOnPage',3,1,'25',1278,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (26,8,'Раздел, за которым закреплено действие','sectionId',3,23,'',1,3,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (27,8,'Действие','actionId',3,23,'',1,7,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (28,8,'Профили пользователей, имеющих доступ к этому действию в этом разделе','profileIds',1,7,'14',1,10,0,'u','many','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (29,8,'Статус','toggle',10,5,'y',1,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (30,8,'Положение в списке','move',3,4,'',1,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (31,7,'Наименование','title',1,1,'',26,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (32,7,'Псевдоним','alias',1,1,'',27,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (33,9,'Раздел','sectionId',3,23,'',28,3,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (34,9,'Поле','fieldId',3,23,'',29,5,33,'с','one','entityId','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (35,9,'Очередность отображения столбца в гриде','move',3,4,'',2133,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (36,10,'Наименование','title',1,1,'',31,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (37,10,'Статус','toggle',10,5,'y',32,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (38,11,'Профиль','profileId',3,23,'',33,10,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (39,11,'Фамилия Имя','title',1,1,'',34,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (40,11,'Email (используется в качестве логина)','email',1,1,'',35,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (41,11,'Пароль','password',1,1,'',36,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (42,11,'Статус','toggle',10,5,'y',37,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (64,4,'Наименование','title',1,1,'',53,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (65,4,'Псевдоним','alias',1,1,'',54,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (66,4,'Способен работать с внешними ключами','storeRelationAbility',11,23,'none',55,6,0,'u','many','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1445,195,'Статус','toggle',10,5,'y',1316,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (92,4,'Скрывать при генерации формы','hidden',12,9,'0',72,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (106,20,'Поле','fieldId',3,23,'0',86,5,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (107,20,'Наименование','title',1,1,'',87,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (108,20,'Псевдоним','alias',1,1,'',88,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (109,20,'Ширина','masterDimensionValue',3,18,'0',91,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (110,20,'Высота','slaveDimensionValue',3,18,'0',93,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (111,20,'Размер','proportions',10,5,'o',89,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (112,20,'Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>','slaveDimensionLimitation',12,9,'1',92,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (114,20,'При расчете пропорций отталкиваться от','masterDimensionAlias',10,5,'width',90,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (131,25,'Наименование','title',1,1,'',101,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (133,25,'Псевдоним','alias',1,1,'',102,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (137,25,'Статус','toggle',10,5,'y',2208,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (345,7,'Для выполнения действия необходимо выбрать стоку','rowRequired',10,5,'y',308,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (377,6,'Порядок отображения','move',3,4,'0',338,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (383,5,'Столбец-satellite','satellite',3,23,'0',2199,5,6,'с','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (454,5,'Динамическое обновление','span',0,16,'',775,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (455,5,'Тип зависимости','dependency',10,5,'u',2197,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (470,5,'Предназначено для хранения ключей','storeRelationAbility',10,23,'none',10,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (471,90,'Элемент управления','elementId',3,23,'0',429,4,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (472,90,'Наименование','title',1,1,'',430,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (473,90,'Псевдоним','alias',1,1,'',431,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (474,90,'Значение по умолчанию','defaultValue',1,1,'',432,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (475,1,'Пригоден для работы с элементами управления','elementId',1,23,'',433,4,0,'u','many','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (476,91,'В контексте какого поля','fieldId',3,23,'0',434,5,0,'u','one','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (477,91,'Параметр настройки','possibleParamId',3,23,'0',435,90,476,'с','one','elementId','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (478,91,'Значение параметра','value',4,6,'',436,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (502,3,'От какого класса наследовать класс контроллера','extends',1,1,'Indi_Controller_Admin',23,0,0,'u','none','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (503,3,'Сортировка','defaultSortField',3,23,'0',514,5,19,'с','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2212,8,'South-панель','south',10,23,'auto',2212,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (555,2,'От какого класса наследовать','extends',1,1,'Indi_Db_Table',512,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (557,3,'Направление сортировки','defaultSortDirection',10,5,'ASC',1277,6,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (559,101,'Наименование','title',1,1,'',517,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (560,101,'Псевдоним','alias',1,1,'',519,0,0,'u','none','0','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (563,101,'Прикрепленная сущность','entityId',3,23,'0',520,2,0,'u','one','0','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (566,5,'Если имя столбца-satellite не найдено, то по какому другому столбцу фильтровать','alternative',1,1,'',2239,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (581,101,'Соответствующий раздел бэкенда','sectionId',3,23,'0',534,3,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (585,101,'Порядок отображения соответствующего пункта в меню','move',3,4,'0',950,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (594,20,'Изменить оттенок','changeColor',10,5,'n',545,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (595,20,'Оттенок','color',13,11,'',546,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (612,2,'Тип','system',10,23,'n',559,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (678,128,'Имя','title',1,1,'',625,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (679,128,'Email','email',1,1,'',626,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (680,128,'Сообщение','message',4,6,'',627,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (681,128,'Дата','date',9,19,'<?=date(\'Y-m-d H:i:s\')?>',628,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (682,129,'Email','title',1,1,'',629,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (683,129,'Дата','date',6,12,'0000-00-00',630,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (684,130,'Email','email',1,1,'',633,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (685,130,'ФИО','title',1,1,'',632,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (686,130,'Пароль','password',1,1,'',634,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (689,130,'Пол','gender',10,5,'n',969,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (690,130,'Дата рождения','birth',6,12,'0000-00-00',1100,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (691,130,'Дата регистрации','registration',6,12,'<?=date(\'Y-m-d\')?>',636,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1364,7,'Тип','type',10,5,'p',2099,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1365,146,'Тип','type',10,5,'p',1293,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (698,130,'Подписался на рассылку','subscribed',12,9,'0',1443,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (699,130,'Последний визит','lastVisit',9,19,'',640,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1444,195,'Порядок отображения','move',3,4,'0',1315,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1442,195,'Раздел','sectionId',3,23,'0',1313,3,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1443,195,'Поле прикрепленной к разделу сущности','fieldId',3,23,'0',1314,5,1442,'с','one','entityId','`elementId` NOT IN (4,14,16,20,22)','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1441,2,'Включить в кэш','useCache',12,23,'0',1312,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (754,5,'Статическая фильтрация','filter',1,1,'',523,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (767,3,'Статическая фильтрация','filter',1,1,'',461,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (828,5,'Псевдоним поля для использования в satellite-функциональности','satellitealias',1,1,'',701,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (857,146,'Наименование','title',1,1,'',803,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (858,146,'Псевдоним','alias',1,1,'',804,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (859,147,'Раздел фронтенда','fsectionId',3,23,'0',805,101,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (860,147,'Действие','factionId',3,23,'0',806,146,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (868,101,'Вышестоящий раздел','fsectionId',3,5,'0',516,101,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (869,101,'Статическая фильтрация','filter',1,1,'',814,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (960,101,'Количество строк для отображения по умолчанию','defaultLimit',3,18,'20',951,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (961,130,'Аккаунт активирован','activated',12,9,'0',637,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (962,130,'Код активации','activationCode',1,1,'',639,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1366,3,'Тип','type',10,5,'p',22,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1009,101,'По умолчанию сортировка по','orderBy',10,5,'c',952,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1010,101,'Столбец сортировки','orderColumn',3,23,'0',953,5,563,'с','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1011,101,'Направление сортировки','orderDirection',10,5,'ASC',981,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1012,101,'SQL-выражение','orderExpression',1,1,'',982,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1027,147,'Тип','type',10,5,'r',968,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1028,130,'Аватар','avatar',0,14,'',1099,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1040,101,'Тип','type',10,5,'r',538,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1041,101,'Где брать идентификатор','where',1,1,'',983,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1042,101,'Действие по умолчанию','index',1,1,'',1403,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1074,146,'Выполнять maintenance()','maintenance',10,5,'r',1015,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1100,160,'Id сессии','title',1,1,'',1040,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1101,160,'Дата последней активности','lastActivity',9,19,'0000-00-00 00:00:00',1041,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1102,160,'Пользователь','userId',3,23,'0',1042,130,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1108,130,'Настройки','settings',0,16,'',1101,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1161,130,'Социальные сети','socialNetworks',0,16,'',1444,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1162,130,'ID пользователя в этой соц.сети','identifier',1,1,'',1707,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1163,130,'Какая','sn',10,5,'n',1445,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1191,147,'Не указывать действие при создании seo-урлов из системных','blink',12,9,'0',1259,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1192,162,'Раздел фронтенда','fsectionId',3,23,'0',1127,101,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1193,162,'Действие в разделе фронтенда','fsection2factionId',3,23,'0',1128,147,1192,'с','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1194,162,'Компонент','entityId',3,23,'0',1129,2,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1195,162,'Очередность','move',3,4,'0',1130,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1196,162,'Префикс','prefix',1,1,'',1131,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2196,9,'Вышестоящий столбец','gridId',3,23,'0',30,9,0,'u','one','','`sectionId` = \"<?=$this->sectionId?>\"','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2193,130,'set','entityId',11,7,'7',2195,6,0,'u','many','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2194,130,'asd','storeRelationAbility',11,7,'none',2193,6,0,'u','many','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2190,130,'тест','test',3,23,'0',2194,5,2193,'с','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2191,130,'testIds','testIds',1,7,'',2191,4,0,'u','many','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2184,195,'Игнорировать optionTemplate','ignoreTemplate',12,9,'1',2183,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2195,130,'Цвет','color',13,11,'',2190,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2183,195,'Статическая фильтрация','filter',1,1,'',2167,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2176,301,'Источник','source',10,23,'section',2176,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2177,301,'Сущность','entityId',3,23,'0',2177,2,0,'u','one','','`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2178,301,'Поле','fieldId',3,23,'0',2178,5,2177,'с','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2179,301,'Префикс','prefix',1,1,'',2179,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2180,301,'Постфикс','postfix',1,1,'',2180,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2181,301,'Порядок отображения','move',3,4,'0',2181,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2182,195,'Значение по умолчанию','defaultValue',1,1,'',2182,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1325,147,'Переименовать действие при генерации seo-урла','rename',12,9,'0',1260,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1326,147,'Псевдоним','alias',1,1,'',1261,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1327,147,'Настройки SEO','seoSettings',0,16,'',1126,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1337,10,'Cущность, экземпляры которой тоже будут иметь доступ к CMS с данным профилем','entityId',3,23,'0',1271,2,0,'u','one','','`system`!=\'y\'','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1341,171,'Раздел','sectionId',3,23,'0',1275,3,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1342,171,'Поле','fieldId',3,23,'0',1276,5,1341,'с','one','entityId','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2251,171,'Изменить свойства поля','alter',0,16,'',2207,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1345,3,'Отключить кнопку Add','disableAdd',12,9,'0',2211,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1509,204,'Ширина','detailsHtmlWidth',3,18,'0',1383,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1532,171,'Значение по умолчанию','defaultValue',1,1,'',2252,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1485,204,'Наименование','title',1,1,'',1356,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1486,204,'Псевдоним','alias',1,1,'',1357,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1487,204,'Значение','detailsHtml',4,13,'',1382,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1488,204,'Статус','toggle',10,5,'y',1358,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1489,205,'Вышестояший пункт','menuId',3,23,'0',1359,205,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1490,205,'Наименование','title',1,1,'',1360,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1491,205,'Связан со статической страницей','linked',10,5,'n',1361,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1492,205,'Статическая страница','staticpageId',3,23,'0',1362,25,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1493,205,'Ссылка','url',1,1,'',1363,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1494,205,'Статус','toggle',10,23,'y',1364,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1495,205,'Отображать в нижнем меню','bottom',10,5,'y',1365,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1496,205,'Порядок отображения','move',3,4,'0',1366,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1814,25,'Контент','details',4,13,'',1667,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1510,204,'Контент','detailsSpan',0,16,'',1379,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1511,204,'Высота','detailsHtmlHeight',3,18,'200',1384,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1513,204,'Css класс для body','detailsHtmlBodyClass',1,1,'',1385,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1514,204,'Css стили','detailsHtmlStyle',4,6,'',1386,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1515,204,'Тип','type',10,5,'html',1380,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1516,204,'Значение','detailsString',1,1,'',1387,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1517,204,'Значение','detailsTextarea',4,6,'',1557,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2173,301,'Тип компонента','type',10,23,'static',2173,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2174,301,'Указанный вручную','content',1,1,'',2174,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2175,301,'Шагов вверх','up',3,18,'0',2175,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2172,301,'Тэг','tag',10,23,'title',2172,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2171,301,'Действие','fsection2factionId',3,23,'0',2171,147,2170,'с','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2170,301,'Раздел','fsectionId',3,23,'0',2170,101,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1533,101,'Статус','toggle',10,5,'y',1429,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1554,3,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',310,5,19,'с','one','','`relation`!=\"0\"','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1560,101,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',815,5,868,'с','one','entityId','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1562,101,'От какого класса наследовать класс контроллера','extends',1,1,'',1431,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1575,130,'Смена пароля','changepasswd',0,16,'',1708,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1576,130,'Дата последнего запроса','changepasswdDate',9,19,'0000-00-00 00:00:00',1709,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1577,130,'Код','changepasswdCode',1,1,'',1710,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1658,195,'Альтернативное наименование','alt',1,1,'',1520,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2132,10,'Порядок отображения','move',3,4,'0',2132,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (1886,9,'Изменить название столбца на','alterTitle',1,1,'',2165,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2100,7,'Отображать в панели действий','display',12,9,'1',2100,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2131,10,'Dashboard','dashboard',1,1,'',2131,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2159,9,'Статус','toggle',10,5,'y',2196,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2161,171,'Режим','mode',10,23,'inherit',2251,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2166,91,'Auto title','title',1,1,'',2166,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2163,2,'Заголовочное поле','titleFieldId',3,23,'0',2163,5,0,'u','one','','`entityId` = \"<?=$this->id?>\" AND `columnTypeId` != \"0\"','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2164,8,'Auto title','title',1,1,'',2164,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2165,9,'Auto title','title',1,1,'',2205,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2167,195,'Auto title','title',1,1,'',2184,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2168,147,'Auto title','title',1,1,'',2242,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2169,171,'Auto title','title',1,1,'',1402,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2197,5,'Режим','mode',10,23,'regular',9,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2199,5,'Подсказка','tooltip',4,6,'',95,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2200,9,'Подсказка','tooltip',4,6,'',2206,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2203,195,'Доступ','access',10,5,'all',2203,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2202,7,'Статус','toggle',10,23,'y',2202,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2204,195,'Выбранные','profileIds',1,7,'',2204,10,0,'u','many','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2205,9,'Доступ','access',10,5,'all',2210,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2206,9,'Выбранные','profileIds',1,7,'',2253,10,0,'u','many','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2207,171,'Влияние','impact',10,5,'all',2134,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2208,25,'Родительская страница','staticpageId',3,23,'0',1666,25,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2209,8,'Переименовать','rename',1,1,'',2209,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2210,9,'Редактор','editor',10,5,'0',2200,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2211,3,'Группировка','groupBy',3,23,'0',462,5,19,'с','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2213,3,'Режим подгрузки данных','rowsetSeparate',10,23,'auto',2213,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2214,8,'Автосайз окна','fitWindow',10,23,'auto',2214,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2215,10,'Максимальное количество окон','maxWindows',3,18,'15',2215,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2270,309,'Заголовок','tplDecSubj',4,1,'',2270,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2269,309,'Текст','tplIncBody',4,6,'',2269,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2268,309,'Заголовок','tplIncSubj',4,1,'',2268,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2267,309,'Назначение','tplFor',10,23,'inc',2267,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2266,309,'Сообщение','tpl',0,16,'',2266,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2263,309,'Цвет фона','bg',13,11,'212#d9e5f3',2263,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2264,309,'Цвет текста','fg',13,11,'216#044099',2264,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2265,309,'Подсказка','tooltip',4,6,'',2265,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2262,309,'Пункты меню','sectionId',1,23,'',2262,3,2255,'с','many','','FIND_IN_SET(`sectionId`, \"<?=Indi::model(\'Section\')->fetchAll(\'`sectionId` = \"0\"\')->column(\'id\', true)?>\")','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2255,309,'Сущность','entityId',3,23,'0',2255,2,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2256,309,'Событие / PHP','event',1,1,'',2256,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2257,309,'Получатели','profileId',1,23,'',2257,10,0,'u','many','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2258,309,'Статус','toggle',10,23,'y',2258,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2259,309,'Счетчик','qty',0,16,'',2259,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2260,309,'Отображение / SQL','qtySql',1,1,'',2260,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2261,309,'Направление изменения','qtyDiffRelyOn',10,23,'event',2261,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2254,309,'Наименование','title',1,1,'',2254,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2236,307,'Наименование','title',1,1,'',2236,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2237,307,'Ключ','alias',1,1,'',2237,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2238,307,'Статус','toggle',10,23,'y',2238,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2239,5,'Мультиязычность','l10n',10,23,'n',413,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2240,147,'Разрешено не передавать id в uri','allowNoid',12,9,'0',2168,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2241,147,'Над записью','row',10,5,'existing',2240,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2242,147,'Где брать идентификатор','where',1,1,'',2241,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2243,2,'Паттерн комплекта календарных полей','spaceScheme',10,23,'none',2243,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2244,2,'Комплект календарных полей','spaceFields',1,23,'',2244,5,0,'u','many','','`entityId` = \"<?=$this->id?>\"','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2245,308,'Сущность','entityId',3,23,'0',2245,2,0,'u','one','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2246,308,'Поле','fieldId',3,23,'0',2246,5,0,'u','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2247,308,'От какого поля зависит','consider',3,23,'0',2247,5,2246,'с','one','entityId','`id` != \"<?=$this->fieldId?>\" AND `columnTypeId` != \"0\"','entityId','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2248,308,'Поле по ключу','foreign',3,23,'0',2248,5,2247,'с','one','relation','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2249,308,'Auto title','title',1,1,'',2249,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2250,171,'Выбранные','profileIds',1,7,'',2169,10,0,'u','many','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2252,171,'Наименование','rename',1,1,'',2250,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2253,9,'Ключ','alias',1,1,'',1738,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2271,309,'Текст','tplDecBody',4,6,'',2271,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2272,309,'Заголовок','tplEvtSubj',4,1,'',2272,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2273,309,'Сообщение','tplEvtBody',4,6,'',2273,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2274,310,'Уведомление','noticeId',3,23,'0',2274,309,0,'u','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2275,310,'Роль','profileId',3,23,'0',2275,10,0,'u','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2276,310,'Критерий','criteriaRelyOn',10,5,'event',2276,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2277,310,'Общий','criteriaEvt',1,1,'',2277,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2278,310,'Для увеличения','criteriaInc',1,1,'',2278,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2279,310,'Для уменьшения','criteriaDec',1,1,'',2279,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2280,310,'Ауто титле','title',1,1,'',2280,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2281,310,'Дублирование на почту','email',10,23,'n',2281,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2282,310,'Дублирование в ВК','vk',10,23,'n',2282,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2283,310,'Дублирование по SMS','sms',10,23,'n',2283,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2284,310,'Критерий','criteria',1,1,'',2284,0,0,'u','none','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2285,310,'Дублирование на почту','mail',10,23,'n',2285,6,0,'u','one','','','','hidden','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2286,311,'Наименование','title',1,1,'',2286,0,0,'u','none','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2287,312,'Год','yearId',3,23,'0',2287,311,0,'u','one','','','','required','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2288,312,'Месяц','month',10,23,'01',2288,6,0,'u','one','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2289,312,'Наименование','title',1,1,'',2289,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2290,312,'Порядок','move',3,4,'0',2290,0,0,'u','none','','','','regular','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2291,313,'Сущность','entityId',3,23,'0',2291,2,0,'u','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2292,313,'Объект','key',3,23,'0',2292,0,2291,'e','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2293,313,'Что изменено','fieldId',3,23,'0',2293,5,2291,'с','one','','`columnTypeId` != \"0\"','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2294,313,'Было','was',4,13,'',2294,0,0,'u','none','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2295,313,'Стало','now',4,13,'',2295,0,0,'u','none','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2296,313,'Когда','datetime',9,19,'0000-00-00 00:00:00',2296,0,0,'u','none','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2297,313,'Месяц','monthId',3,23,'0',2297,312,0,'u','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2298,313,'Тип автора','changerType',3,23,'0',2298,2,0,'u','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2299,313,'Автор','changerId',3,23,'0',2299,0,2298,'e','one','','','','readonly','','n');
insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`,`mode`,`tooltip`,`l10n`) values (2300,313,'Роль','profileId',3,23,'0',2300,10,0,'u','one','','','','readonly','','n');

/*Table structure for table `fsection` */

DROP TABLE IF EXISTS `fsection`;

CREATE TABLE `fsection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `defaultLimit` int(11) NOT NULL DEFAULT '20',
  `orderBy` enum('e','c') NOT NULL DEFAULT 'c',
  `orderColumn` int(11) NOT NULL DEFAULT '0',
  `orderDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `orderExpression` varchar(255) NOT NULL DEFAULT '',
  `type` enum('s','r') NOT NULL DEFAULT 'r',
  `where` varchar(255) NOT NULL DEFAULT '',
  `index` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('n','y') NOT NULL DEFAULT 'y',
  `parentSectionConnector` int(11) NOT NULL DEFAULT '0',
  `extends` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `sectionId` (`sectionId`),
  KEY `fsectionId` (`fsectionId`),
  KEY `type` (`type`),
  KEY `parentSectionConnector` (`parentSectionConnector`),
  KEY `orderBy` (`orderBy`),
  KEY `orderColumn` (`orderColumn`),
  KEY `orderDirection` (`orderDirection`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

/*Data for the table `fsection` */

insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (8,'Пользователи','users',130,146,28,0,'',20,'c',0,'ASC','','r','','','y',0,'');
insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (37,'Статические страницы','static',25,30,39,0,'',20,'c',0,'ASC','','r','','','y',0,'');
insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (22,'Фидбэк','feedback',128,144,44,0,'',20,'c',0,'ASC','','s','\"\"','add','n',0,'');
insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (26,'Мой профиль','myprofile',130,0,22,39,'',20,'c',0,'ASC','','s','`id` = \'\'','form','y',0,'My');
insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (39,'Главная','index',25,0,8,0,'',20,'c',0,'ASC','','r','','','y',0,'');
insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (41,'Карта сайта','sitemap',101,113,41,0,'`toggle`=\"y\"',20,'c',585,'ASC','','r','','','y',0,'');

/*Table structure for table `fsection2faction` */

DROP TABLE IF EXISTS `fsection2faction`;

CREATE TABLE `fsection2faction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `factionId` int(11) NOT NULL DEFAULT '0',
  `type` enum('j','r') NOT NULL DEFAULT 'r',
  `blink` tinyint(1) NOT NULL DEFAULT '0',
  `rename` tinyint(1) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `allowNoid` tinyint(1) NOT NULL DEFAULT '0',
  `row` enum('existing','new','any') NOT NULL DEFAULT 'existing',
  `where` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `factionId` (`factionId`),
  KEY `type` (`type`),
  KEY `row` (`row`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;

/*Data for the table `fsection2faction` */

insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (126,8,37,'r',0,0,'','Восстановление доступа',0,'existing','');
insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (129,22,1,'r',0,0,'','По умолчанию',0,'existing','');
insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (124,26,3,'r',0,0,'','Изменить',0,'existing','');
insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (127,37,2,'r',0,0,'','Просмотр',0,'existing','');
insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (123,39,1,'r',0,0,'','По умолчанию',0,'existing','');
insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (128,41,1,'r',0,0,'','По умолчанию',0,'existing','');
insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`type`,`blink`,`rename`,`alias`,`title`,`allowNoid`,`row`,`where`) values (125,8,6,'r',0,0,'','Активация аккаунта',0,'existing','');

/*Table structure for table `grid` */

DROP TABLE IF EXISTS `grid`;

CREATE TABLE `grid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `alterTitle` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `title` varchar(255) NOT NULL DEFAULT '',
  `gridId` int(11) NOT NULL DEFAULT '0',
  `tooltip` text NOT NULL,
  `access` enum('all','only','except') NOT NULL DEFAULT 'all',
  `profileIds` varchar(255) NOT NULL DEFAULT '',
  `editor` enum('0','1') NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `toggle` (`toggle`),
  KEY `gridId` (`gridId`),
  KEY `access` (`access`),
  KEY `profileIds` (`profileIds`),
  KEY `editor` (`editor`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=2417 DEFAULT CHARSET=utf8;

/*Data for the table `grid` */

insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1,2,1,1,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2,2,2,2,'','y','Тип столбца MySQL',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (3,2,3,3,'','y','Пригоден для хранения внешних ключей',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (4,5,4,4,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (5,5,5,5,'','y','Таблица БД',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (6,6,7,6,'','y','Наименование поля',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (7,6,8,7,'','y','Наименование соответствующего полю столбца в  таблице БД',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (8,6,9,8,'','y','Тип столбца MySQL',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (9,6,10,9,'','y','Элемент управления',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (10,6,11,11,'','y','Значение по умолчанию',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (11,6,12,13,'','y','Ключи какой сущности будут храниться в этом поле',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (13,6,14,2321,'','y','Положение в списке',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (14,7,19,16,'Сущность','y','Cущность, закрепленная за разделом',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (15,7,20,14,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (16,7,21,15,'','y','Контроллер',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (17,7,22,17,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (18,7,23,31,'','y','Положение в списке',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (20,7,25,2327,'','y','Записей на странице',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (23,8,27,23,'','y','Действие',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (24,8,29,24,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (25,8,30,2324,'','y','Положение в списке',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (26,10,31,26,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (27,10,32,27,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (29,11,2165,29,'Столбец','y','Столбец',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (30,11,35,2320,'','y','Очередность отображения столбца в гриде',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (32,13,36,32,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (33,13,37,33,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (34,12,16,34,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (35,12,17,35,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (36,14,39,36,'','y','Фамилия Имя',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (37,14,40,37,'','y','Email (используется в качестве логина)',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (38,14,41,38,'','y','Пароль',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (39,14,42,39,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (42,16,65,43,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (43,16,66,44,'','y','Способен работать с внешними ключами',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (46,16,64,42,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (89,22,107,56,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (90,22,108,57,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (91,22,109,59,'','y','Ширина',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (92,22,110,60,'','y','Высота',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (93,22,111,58,'','y','Размер',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (94,22,112,61,'','y','Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (130,30,131,61,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (132,30,133,63,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2365,379,0,2365,'Взятый из контекста','y','Взятый из контекста',2362,'','all','','0','context');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2363,379,2179,2363,'','y','Префикс',2362,'','all','','1','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (136,30,137,67,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (341,12,377,253,'','y','Порядок отображения',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (375,100,472,289,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (376,100,473,290,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (377,100,474,291,'','y','Значение по умолчанию',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (378,101,477,292,'','y','Параметр настройки',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (379,101,478,293,'','y','Значение параметра',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (383,8,28,2326,'','y','Профили пользователей, имеющих доступ к этому действию в этом разделе',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1335,7,1366,926,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1334,113,1040,925,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1333,172,1365,924,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1332,10,1364,923,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (420,113,559,328,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (421,113,560,329,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (424,113,563,330,'','y','Прикрепленная сущность',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (443,5,612,347,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (489,144,678,388,'','y','Имя',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (490,144,679,389,'','y','Email',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (491,144,681,391,'','y','Дата',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (492,145,682,391,'','y','Email',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (493,145,683,392,'','y','Дата',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (494,146,684,990,'','y','Email',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1470,146,685,392,'','y','ФИО',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1382,224,1444,929,'','y','Порядок отображения',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1384,224,1445,928,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (832,172,857,624,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (833,172,858,625,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (834,173,860,626,'','y','Действие',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1383,224,1443,926,'','y','Поле прикрепленной к разделу сущности',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1053,189,1102,772,'','y','Пользователь',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1052,189,1101,771,'','y','Дата последней активности',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1051,189,1100,770,'','y','Id сессии',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1039,172,1074,759,'','y','Выполнять maintenance()',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (979,173,1027,728,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1066,191,1194,782,'','y','Компонент',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1067,191,1195,783,'','y','Очередность',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1068,191,1196,784,'','y','Префикс',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2319,146,699,2319,'','y','Последний визит',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2312,224,2183,2312,'','y','Статическая фильтрация',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2311,224,2182,2311,'','y','Значение по умолчанию',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2362,379,0,2362,'Компонент','y','Компонент',0,'','all','','0','component');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2361,379,2172,2361,'','y','Тэг',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2360,379,2181,2360,'','y','Порядок отображения',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1231,201,1342,851,'','y','Поле',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2356,201,2252,988,'','y','Наименование',0,'','all','','1','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1439,232,1515,965,'','y','Тип',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2359,379,2173,2359,'','y','Тип компонента',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1421,232,1485,962,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1422,232,1486,963,'','y','Псевдоним',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1423,232,1488,1132,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1449,113,1533,989,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1448,201,1532,2356,'','y','Значение по умолчанию',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1515,113,585,1036,'','y','Порядок отображения соответствующего пункта в меню',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1656,11,1886,1156,'','y','Изменить название столбца на',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1767,146,691,1222,'','y','Дата регистрации',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (1954,224,1658,1945,'','y','Альтернативное наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2280,201,2161,1946,'','y','Режим',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2320,11,2159,2325,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2321,6,2197,10,'','y','Режим',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2322,11,34,30,'','y','Поле',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2323,10,2202,2323,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2324,8,2209,2328,'','y','Переименовать',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2325,11,2210,2416,'','y','Редактор',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2326,8,2212,25,'РSП','y','РSП',0,'Режим отображения south-панели','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2327,7,2213,18,'РПД','y','РПД',0,'Режим подгрузки данных','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2328,8,2214,297,'','y','Автосайз окна',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2329,13,2215,2329,'МКО','y','МКО',0,'Максимальное количество окон','all','','1','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2415,390,2283,2415,'SMS','y','Дублирование по SMS',0,'Дублирование по SMS','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2394,389,2259,2394,'','y','Счетчик',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2395,389,2260,2395,'','y','Отображение / SQL',2394,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2396,389,2256,2396,'','y','Событие / PHP',2394,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2397,389,2262,2397,'','y','Пункты меню',2394,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2398,389,2263,2398,'','y','Цвет фона',2394,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2399,389,2264,2399,'','y','Цвет текста',2394,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2416,11,2253,2322,'','y','Ключ',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2390,389,2254,2390,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2391,389,2255,2391,'','y','Сущность',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2392,389,2257,2392,'','y','Получатели',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2393,389,2258,2393,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2349,387,2236,2348,'','y','Наименование',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2350,387,2237,2349,'','y','Ключ',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2351,387,2238,2350,'','y','Статус',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2352,6,2239,2352,'l10n','y','Мультиязычность',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2353,388,2247,2352,'','y','От какого поля зависит',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2354,388,2248,2353,'','y','Поле по ключу',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2357,201,2207,2357,'','y','Влияние',0,'','all','','1','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2358,201,2250,2358,'','y','Выбранные',0,'','all','','1','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2364,379,2174,2364,'','y','Указанный вручную',2362,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2366,379,2175,2366,'Уровень','y','Шагов вверх',2365,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2367,379,2176,2367,'','y','Источник',2365,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2368,379,2178,2368,'','y','Поле',2365,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2369,379,2180,2369,'','y','Постфикс',2362,'','all','','1','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2414,390,2282,2414,'VK','y','Дублирование в ВК',0,'Дублирование во ВКонтакте','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2413,390,2281,2413,'Email','y','Дублирование на почту',0,'Дублирование на почту','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2412,390,2277,2412,'','y','Общий',0,'','all','','0','');
insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`,`gridId`,`tooltip`,`access`,`profileIds`,`editor`,`alias`) values (2411,390,2275,2411,'','y','Роль',0,'','all','','0','');

/*Table structure for table `lang` */

DROP TABLE IF EXISTS `lang`;

CREATE TABLE `lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `lang` */

insert  into `lang`(`id`,`title`,`alias`,`toggle`) values (1,'Русский','ru','y');
insert  into `lang`(`id`,`title`,`alias`,`toggle`) values (2,'Engish','en','y');

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `linked` enum('n','y') NOT NULL DEFAULT 'n',
  `staticpageId` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `bottom` enum('y','n') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menuId` (`menuId`),
  KEY `linked` (`linked`),
  KEY `staticpageId` (`staticpageId`),
  KEY `toggle` (`toggle`),
  KEY `bottom` (`bottom`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `menu` */

insert  into `menu`(`id`,`menuId`,`title`,`linked`,`staticpageId`,`url`,`toggle`,`bottom`,`move`) values (1,0,'Курсы','n',0,'/courses','y','y',1);
insert  into `menu`(`id`,`menuId`,`title`,`linked`,`staticpageId`,`url`,`toggle`,`bottom`,`move`) values (5,0,'Сотрудничество','y',9,'','y','y',5);

/*Table structure for table `metatag` */

DROP TABLE IF EXISTS `metatag`;

CREATE TABLE `metatag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `tag` enum('title','keywords','description') NOT NULL DEFAULT 'title',
  `type` enum('static','dynamic') NOT NULL DEFAULT 'static',
  `content` varchar(255) NOT NULL DEFAULT '',
  `up` int(11) NOT NULL DEFAULT '0',
  `source` enum('section','action','row') NOT NULL DEFAULT 'section',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(255) NOT NULL DEFAULT '',
  `postfix` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `fsection2factionId` (`fsection2factionId`),
  KEY `tag` (`tag`),
  KEY `type` (`type`),
  KEY `source` (`source`),
  KEY `entityId` (`entityId`),
  KEY `fieldId` (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `metatag` */

insert  into `metatag`(`id`,`fsectionId`,`fsection2factionId`,`tag`,`type`,`content`,`up`,`source`,`entityId`,`fieldId`,`prefix`,`postfix`,`move`) values (2,37,127,'title','dynamic','',0,'row',25,131,'','',2);

/*Table structure for table `month` */

DROP TABLE IF EXISTS `month`;

CREATE TABLE `month` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `yearId` int(11) NOT NULL DEFAULT '0',
  `month` enum('01','02','03','04','05','06','07','08','09','10','11','12') NOT NULL DEFAULT '01',
  `title` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `yearId` (`yearId`),
  KEY `month` (`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `month` */

/*Table structure for table `notice` */

DROP TABLE IF EXISTS `notice`;

CREATE TABLE `notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `event` varchar(255) NOT NULL DEFAULT '',
  `profileId` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `qtySql` varchar(255) NOT NULL DEFAULT '',
  `qtyDiffRelyOn` enum('event','getter') NOT NULL DEFAULT 'event',
  `sectionId` varchar(255) NOT NULL DEFAULT '',
  `bg` varchar(10) NOT NULL DEFAULT '212#d9e5f3',
  `fg` varchar(10) NOT NULL DEFAULT '216#044099',
  `tooltip` text NOT NULL,
  `tplFor` enum('inc','dec','evt') NOT NULL DEFAULT 'inc',
  `tplIncSubj` text NOT NULL,
  `tplIncBody` text NOT NULL,
  `tplDecSubj` text NOT NULL,
  `tplDecBody` text NOT NULL,
  `tplEvtSubj` text NOT NULL,
  `tplEvtBody` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `profileId` (`profileId`),
  KEY `toggle` (`toggle`),
  KEY `qtyDiffRelyOn` (`qtyDiffRelyOn`),
  KEY `sectionId` (`sectionId`),
  KEY `tplFor` (`tplFor`),
  FULLTEXT KEY `tooltip` (`tooltip`),
  FULLTEXT KEY `tplIncSubj` (`tplIncSubj`),
  FULLTEXT KEY `tplIncBody` (`tplIncBody`),
  FULLTEXT KEY `tplDecSubj` (`tplDecSubj`),
  FULLTEXT KEY `tplDecBody` (`tplDecBody`),
  FULLTEXT KEY `tplEvtSubj` (`tplEvtSubj`),
  FULLTEXT KEY `tplEvtBody` (`tplEvtBody`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `notice` */

/*Table structure for table `noticegetter` */

DROP TABLE IF EXISTS `noticegetter`;

CREATE TABLE `noticegetter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noticeId` int(11) NOT NULL DEFAULT '0',
  `profileId` int(11) NOT NULL DEFAULT '0',
  `criteriaRelyOn` enum('event','getter') NOT NULL DEFAULT 'event',
  `criteriaEvt` varchar(255) NOT NULL DEFAULT '',
  `criteriaInc` varchar(255) NOT NULL DEFAULT '',
  `criteriaDec` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` enum('n','y') NOT NULL DEFAULT 'n',
  `vk` enum('n','y') NOT NULL DEFAULT 'n',
  `sms` enum('n','y') NOT NULL DEFAULT 'n',
  `criteria` varchar(255) NOT NULL DEFAULT '',
  `mail` enum('n','y') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `noticeId` (`noticeId`),
  KEY `profileId` (`profileId`),
  KEY `criteriaRelyOn` (`criteriaRelyOn`),
  KEY `email` (`email`),
  KEY `vk` (`vk`),
  KEY `sms` (`sms`),
  KEY `mail` (`mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `noticegetter` */

/*Table structure for table `param` */

DROP TABLE IF EXISTS `param`;

CREATE TABLE `param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `possibleParamId` int(11) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  KEY `possibleParamId` (`possibleParamId`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;

/*Data for the table `param` */

insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (90,1487,13,'/css/style.css','Путь к css-нику для подцепки редактором');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (134,1814,5,'true','Во всю ширину');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (102,1487,5,'true','Во всю ширину');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (127,109,4,'px','Единица измерения');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (128,110,4,'px','Единица измерения');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (160,19,21,'system','Группировка опций по столбцу');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (156,18,21,'type','Группировка опций по столбцу');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (158,2190,21,'entityId','Группировка опций по столбцу');
insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (159,12,21,'system','Группировка опций по столбцу');

/*Table structure for table `possibleelementparam` */

DROP TABLE IF EXISTS `possibleelementparam`;

CREATE TABLE `possibleelementparam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

/*Data for the table `possibleelementparam` */

insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (3,18,'Максимальная длина в символах','maxlength','5');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (4,18,'Единица измерения','measure','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (5,13,'Во всю ширину','wide','0');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (6,7,'Количество столбцов','cols','1');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (7,13,'Высота в пикселях','height','200');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (28,19,'Отображаемый формат времени','displayTimeFormat','H:i');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (27,19,'Отображаемый формат даты','displayDateFormat','Y-m-d');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (26,12,'Отображаемый формат','displayFormat','Y-m-d');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (11,13,'Ширина в пикселях','width','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (12,13,'Css класс для body','bodyClass','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (13,13,'Путь к css-нику для подцепки редактором','contentsCss','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (14,13,'Стили','style','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (15,14,'Включать наименование поля в имя файла при download-е','appendFieldTitle','true');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (16,14,'Включать наименование сущности в имя файла при download-е','prependEntityTitle','true');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (17,13,'Путь к js-нику для подцепки редактором','contentsJs','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (18,13,'Скрипт','script','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (19,13,'Скрипт обработки исходного кода','sourceStripper','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (20,18,'Только для чтения','readonly','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (21,23,'Группировка опций по столбцу','groupBy','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (22,23,'Шаблон содержимого опции','optionTemplate','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (23,23,'Высота опции','optionHeight','14');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (24,23,'Дополнительно передавать параметры (в виде атрибутов)','optionAttrs','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (25,23,'Отключить лукап','noLookup','false');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (29,23,'Плейсхолдер','placeholder','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (30,1,'Только для чтения','readonly','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (31,1,'Максимальная длина в символах','maxlength','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (37,23,'Заголовочное поле','titleColumn','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (32,23,'Во всю ширину','wide','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (33,1,'Маска','inputMask','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (34,1,'Vtype','vtype','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (35,19,'When','when','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (36,12,'When','when','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (38,5,'Заголовочное поле','titleColumn','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (39,7,'Заголовочное поле','titleColumn','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (40,6,'Разрешенные теги','allowedTags','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (41,14,'Допустимые типы','allowTypes','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (42,1,'Шэйдинг','shade','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (43,1,'Обновлять локализации для других языков','refreshL10nsOnUpdate','');
insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (44,6,'Обновлять локализации для других языков','refreshL10nsOnUpdate','');

/*Table structure for table `profile` */

DROP TABLE IF EXISTS `profile`;

CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `dashboard` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  `maxWindows` int(11) NOT NULL DEFAULT '15',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  KEY `entityId` (`entityId`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `profile` */

insert  into `profile`(`id`,`title`,`toggle`,`entityId`,`dashboard`,`move`,`maxWindows`) values (1,'Конфигуратор','y',0,'',1,15);
insert  into `profile`(`id`,`title`,`toggle`,`entityId`,`dashboard`,`move`,`maxWindows`) values (12,'Администратор','y',0,'',12,15);
insert  into `profile`(`id`,`title`,`toggle`,`entityId`,`dashboard`,`move`,`maxWindows`) values (17,'Пользователь','y',130,'',17,15);

/*Table structure for table `resize` */

DROP TABLE IF EXISTS `resize`;

CREATE TABLE `resize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `masterDimensionValue` int(11) NOT NULL DEFAULT '0',
  `slaveDimensionValue` int(11) NOT NULL DEFAULT '0',
  `proportions` enum('p','c','o') NOT NULL DEFAULT 'o',
  `slaveDimensionLimitation` tinyint(1) NOT NULL DEFAULT '1',
  `masterDimensionAlias` enum('width','height') NOT NULL DEFAULT 'width',
  `changeColor` enum('y','n') NOT NULL DEFAULT 'n',
  `color` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  KEY `proportions` (`proportions`),
  KEY `masterDimensionAlias` (`masterDimensionAlias`),
  KEY `changeColor` (`changeColor`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;

/*Data for the table `resize` */

/*Table structure for table `search` */

DROP TABLE IF EXISTS `search`;

CREATE TABLE `search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `alt` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `ignoreTemplate` tinyint(1) NOT NULL DEFAULT '1',
  `consistence` tinyint(1) NOT NULL DEFAULT '1',
  `access` enum('all','only','except') NOT NULL DEFAULT 'all',
  `profileIds` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `toggle` (`toggle`),
  KEY `access` (`access`),
  KEY `profileIds` (`profileIds`)
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

/*Data for the table `search` */

insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (13,5,612,13,'y','','Тип','','',1,1,'all','');
insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (54,146,691,55,'y','','Дата регистрации','','',1,1,'all','');
insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (55,146,689,56,'y','','Пол','','',1,1,'all','');
insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (114,5,1441,114,'y','','Включить в кэш','','',1,1,'all','');
insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (116,7,1366,116,'y','','Тип','','',1,1,'all','');
insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (117,7,19,117,'y','','Cущность, закрепленная за разделом','','',1,1,'all','');
insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`,`consistence`,`access`,`profileIds`) values (118,7,22,118,'y','','Статус','','',1,1,'all','');

/*Table structure for table `section` */

DROP TABLE IF EXISTS `section`;

CREATE TABLE `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  `rowsOnPage` int(11) NOT NULL DEFAULT '25',
  `extends` varchar(255) NOT NULL DEFAULT 'Indi_Controller_Admin',
  `defaultSortField` int(11) NOT NULL DEFAULT '0',
  `defaultSortDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `disableAdd` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('s','p','o') NOT NULL DEFAULT 'p',
  `parentSectionConnector` int(11) NOT NULL DEFAULT '0',
  `groupBy` int(11) NOT NULL DEFAULT '0',
  `rowsetSeparate` enum('auto','yes','no') NOT NULL DEFAULT 'auto',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `entityId` (`entityId`),
  KEY `toggle` (`toggle`),
  KEY `defaultSortField` (`defaultSortField`),
  KEY `defaultSortDirection` (`defaultSortDirection`),
  KEY `type` (`type`),
  KEY `parentSectionConnector` (`parentSectionConnector`),
  KEY `groupBy` (`groupBy`),
  KEY `rowsetSeparate` (`rowsetSeparate`)
) ENGINE=MyISAM AUTO_INCREMENT=391 DEFAULT CHARSET=utf8;

/*Data for the table `section` */

insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (1,0,0,'Конфигурация','configuration','y',367,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (2,1,1,'Столбцы','columnTypes','y',6,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (5,1,2,'Сущности','entities','y',4,25,'Indi_Controller_Admin',4,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (6,5,5,'Поля в структуре','fields','y',7,25,'Indi_Controller_Admin',14,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (7,1,3,'Разделы','sections','y',2,25,'Indi_Controller_Admin',23,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (8,7,8,'Действия','sectionActions','y',8,25,'Indi_Controller_Admin',30,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (10,1,7,'Действия','actions','n',9,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (11,7,9,'Столбцы грида','grid','y',10,25,'Indi_Controller_Admin',35,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (12,6,6,'Возможные значения','enumset','y',11,25,'Indi_Controller_Admin',377,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (13,1,10,'Роли','profiles','y',5,25,'Indi_Controller_Admin',2132,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (14,13,11,'Пользователи','admins','y',13,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (16,1,4,'Элементы','controlElements','y',14,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (22,6,20,'Копии изображения','resize','y',19,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (29,0,0,'Контент','','n',113,25,'Indi_Controller_Admin',0,'ASC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (30,378,25,'Страницы','staticpages','n',316,25,'Indi_Controller_Admin',131,'ASC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (100,16,90,'Возможные параметры настройки','possibleParams','y',90,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (101,6,91,'Параметры','params','y',91,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (112,0,0,'Фронтенд','','y',371,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (113,112,101,'Разделы','fsections','y',104,25,'Indi_Controller_Admin',585,'ASC','<?=$_SESSION[\'admin\'][\'profileId\']==1?\'1\':\'`toggle`=\"y\"\'?>',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (143,0,0,'Обратная связь','','n',358,25,'Indi_Controller_Admin',0,'ASC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (144,143,128,'Фидбэк','feedback','n',135,25,'Indi_Controller_Admin',681,'DESC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (145,143,129,'Подписчики','subscribers','n',165,25,'Indi_Controller_Admin',682,'ASC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (146,143,130,'Пользователи','users','n',225,25,'Indi_Controller_Admin',685,'DESC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (172,112,146,'Действия','factions','y',185,25,'Indi_Controller_Admin',857,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (173,113,147,'Действия','fsection2factions','y',161,25,'Indi_Controller_Admin',860,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (189,143,160,'Посетители','visitors','n',176,25,'Indi_Controller_Admin',1101,'DESC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (191,173,162,'Компоненты SEO-урла','seoUrl','y',178,25,'Indi_Controller_Admin',1195,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (201,7,171,'Измененные поля','alteredFields','y',188,25,'Indi_Controller_Admin',1342,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (224,7,195,'Фильтры','search','y',192,25,'Indi_Controller_Admin',1444,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (229,29,205,'Меню','menu','n',134,25,'Indi_Controller_Admin',1496,'ASC','',1,'p',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (232,378,204,'Элементы','staticblocks','n',232,25,'Indi_Controller_Admin',1485,'ASC','',0,'o',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (379,173,301,'Компоненты meta-тегов','metatitles','y',379,25,'Indi_Controller_Admin_Meta',2181,'ASC','',0,'o',0,2172,'yes');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (378,0,0,'Статика','','n',144,30,'Indi_Controller_Admin',0,'ASC','',0,'p',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (389,1,309,'Уведомления','notices','y',389,25,'Indi_Controller_Admin',2254,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (390,389,310,'Получатели','noticeGetters','y',390,25,'Indi_Controller_Admin',2275,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (387,1,307,'Языки','lang','y',387,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');
insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`,`groupBy`,`rowsetSeparate`) values (388,6,308,'Зависимости','consider','y',388,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto');

/*Table structure for table `section2action` */

DROP TABLE IF EXISTS `section2action`;

CREATE TABLE `section2action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `actionId` int(11) NOT NULL DEFAULT '0',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  `profileIds` varchar(255) NOT NULL DEFAULT '14',
  `title` varchar(255) NOT NULL DEFAULT '',
  `rename` varchar(255) NOT NULL DEFAULT '',
  `south` enum('auto','yes','no') NOT NULL DEFAULT 'auto',
  `fitWindow` enum('auto','n') NOT NULL DEFAULT 'auto',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `sectionId_2` (`sectionId`),
  KEY `actionId` (`actionId`),
  KEY `profileIds` (`profileIds`),
  KEY `toggle` (`toggle`),
  KEY `south` (`south`),
  KEY `fitWindow` (`fitWindow`)
) ENGINE=MyISAM AUTO_INCREMENT=1579 DEFAULT CHARSET=utf8;

/*Data for the table `section2action` */

insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1,2,1,'y',1,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (2,2,2,'y',2,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (3,2,3,'y',3,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (5,2,4,'n',5,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (6,5,1,'y',6,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (7,5,2,'y',7,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (8,5,3,'y',8,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (9,5,4,'y',9,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (10,6,1,'y',10,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (11,6,2,'y',11,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (12,6,3,'y',12,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (13,6,4,'y',13,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (14,7,1,'y',14,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (15,7,2,'y',15,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (16,7,3,'y',16,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (17,7,4,'y',17,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (18,6,5,'y',18,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (19,6,6,'y',19,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (20,8,1,'y',20,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (21,8,2,'y',21,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (22,8,3,'y',22,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (23,8,4,'y',23,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (24,8,5,'y',24,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (25,8,6,'y',25,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (26,7,5,'y',26,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (27,7,6,'y',27,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (28,10,1,'y',28,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (29,10,2,'y',29,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (30,10,3,'y',30,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (32,11,1,'y',31,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (33,11,2,'y',32,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (34,11,3,'y',33,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (35,11,4,'y',34,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (36,11,5,'y',35,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (37,11,6,'y',36,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (38,13,1,'y',37,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (39,13,2,'y',38,'1','Детали','','auto','n');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (40,13,3,'y',39,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (41,12,1,'y',40,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (42,12,2,'y',41,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (43,12,3,'y',42,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (44,12,4,'y',43,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (45,14,1,'y',44,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (46,14,2,'y',45,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (47,14,3,'y',46,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (48,14,4,'y',47,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (52,16,1,'y',51,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (53,16,2,'y',52,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (54,16,3,'y',53,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (67,7,7,'y',58,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (68,10,7,'y',59,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (69,11,7,'y',60,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (74,22,1,'y',65,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (75,22,2,'y',66,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (76,22,3,'y',67,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (77,22,4,'y',68,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (99,30,1,'y',69,'12,1,17','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (100,30,2,'y',70,'12,1,17','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (101,30,3,'y',71,'12,1,17','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (102,30,4,'y',72,'12,1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (103,30,7,'y',73,'12,1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (329,12,5,'y',299,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (330,12,6,'y',300,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (373,100,1,'y',343,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (374,100,2,'y',344,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (375,100,3,'y',345,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (376,100,4,'y',346,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (377,101,1,'y',347,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (378,101,2,'y',348,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (379,101,3,'y',349,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (380,101,4,'y',350,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (808,13,4,'y',589,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (806,10,4,'y',587,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (429,113,1,'y',399,'1,2','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (430,113,2,'y',400,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (431,113,3,'y',401,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (432,113,4,'y',402,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (874,5,18,'n',1546,'1','Обновить кэш','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (532,144,1,'y',133,'1,2,4','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (533,144,2,'y',134,'1,2,4','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (534,144,3,'y',135,'1,2','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (535,144,4,'y',136,'1,2','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (536,145,1,'y',134,'1,2,4','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (537,145,2,'y',135,'1,2,4','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (538,145,3,'y',136,'1,2','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (539,145,4,'y',137,'1,2','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (540,146,1,'y',135,'12,1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (541,146,2,'y',136,'12,1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (542,146,3,'y',137,'12,1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (543,146,4,'y',138,'12,1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (833,16,4,'n',598,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (875,224,1,'y',608,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (876,224,2,'y',609,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (877,224,4,'y',611,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (878,224,3,'y',610,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (879,224,5,'y',612,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (880,224,6,'y',613,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (881,224,7,'y',614,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (642,172,1,'y',161,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (643,172,2,'y',162,'1,2,4','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (644,172,3,'y',163,'1,2','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (645,172,4,'y',164,'1,2','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (646,173,1,'y',162,'1,2','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (647,173,2,'y',163,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (648,173,3,'y',164,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (649,173,4,'y',165,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (730,189,1,'y',524,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (731,189,2,'y',525,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (732,189,3,'y',526,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (733,189,4,'y',527,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (740,191,1,'y',534,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (741,191,2,'y',535,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (742,191,3,'y',536,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (743,191,4,'y',537,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (744,191,5,'y',538,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (745,191,6,'y',539,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1548,7,34,'y',1548,'1','PHP','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1547,5,34,'y',1559,'1','PHP','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1546,5,33,'y',1547,'1','Автор','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1528,379,6,'y',1528,'1,12','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (789,201,1,'y',583,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (790,201,2,'y',584,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (791,201,3,'y',585,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (792,201,4,'y',586,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (898,229,1,'y',631,'1,2,9','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (899,229,2,'y',632,'1,2,9','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (900,229,3,'y',633,'1,2,9','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (901,229,4,'y',634,'1,2,9','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (910,232,1,'y',643,'12,1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (911,232,2,'y',644,'1,12','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (912,232,3,'y',645,'1,12','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (913,232,4,'y',646,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (921,229,5,'y',654,'1,2,9','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (922,229,6,'y',655,'1,2,9','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (933,229,7,'y',666,'1,2,9','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1527,379,5,'y',1527,'1,12','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (939,232,7,'y',672,'12,1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (946,113,7,'y',679,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (947,113,5,'y',680,'1,2','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (948,113,6,'y',681,'1,2','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (949,113,19,'y',682,'1,2','Обновить sitemap.xml','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1268,8,7,'y',1268,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1295,13,5,'y',1291,'1','Выше','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1296,13,6,'y',1292,'1','Ниже','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1522,14,7,'y',1293,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1545,13,7,'y',1545,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1549,7,35,'y',1549,'1','JS','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1544,229,20,'y',1544,'12','Авторизация','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1526,379,4,'y',1526,'1,12','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1525,379,3,'y',1525,'1,12','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1524,379,2,'y',1524,'1,12','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1523,379,1,'y',1523,'1,12','Список','','auto','n');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1577,390,3,'y',1577,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1576,390,2,'y',1576,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1575,390,1,'y',1575,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1574,389,7,'y',1574,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1570,389,1,'y',1570,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1571,389,2,'y',1571,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1572,389,3,'y',1572,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1573,389,4,'y',1573,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1559,5,7,'y',607,'1','Статус','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1560,387,1,'y',1560,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1561,387,2,'y',1561,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1562,387,3,'y',1562,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1563,387,4,'y',1563,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1564,388,1,'y',1564,'1','Список','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1565,388,2,'y',1565,'1','Детали','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1566,388,3,'y',1566,'1','Сохранить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1567,388,4,'y',1567,'1','Удалить','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1568,7,36,'y',1568,'1','Экспорт','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1569,5,36,'y',1569,'1','Экспорт','','auto','auto');
insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`,`rename`,`south`,`fitWindow`) values (1578,390,4,'y',1578,'1','Удалить','','auto','auto');

/*Table structure for table `staticblock` */

DROP TABLE IF EXISTS `staticblock`;

CREATE TABLE `staticblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `detailsHtml` text NOT NULL,
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `detailsHtmlWidth` int(11) NOT NULL DEFAULT '0',
  `detailsHtmlHeight` int(11) NOT NULL DEFAULT '200',
  `detailsHtmlBodyClass` varchar(255) NOT NULL DEFAULT '',
  `detailsHtmlStyle` text NOT NULL,
  `type` enum('html','string','textarea') NOT NULL DEFAULT 'html',
  `detailsString` varchar(255) NOT NULL DEFAULT '',
  `detailsTextarea` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  KEY `type` (`type`),
  FULLTEXT KEY `detailsHtml` (`detailsHtml`),
  FULLTEXT KEY `detailsHtmlStyle` (`detailsHtmlStyle`),
  FULLTEXT KEY `detailsTextarea` (`detailsTextarea`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

/*Data for the table `staticblock` */

insert  into `staticblock`(`id`,`title`,`alias`,`detailsHtml`,`toggle`,`detailsHtmlWidth`,`detailsHtmlHeight`,`detailsHtmlBodyClass`,`detailsHtmlStyle`,`type`,`detailsString`,`detailsTextarea`) values (21,'sd','hello','Hello','y',443,81,'','','html','','');

/*Table structure for table `staticpage` */

DROP TABLE IF EXISTS `staticpage`;

CREATE TABLE `staticpage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `details` text NOT NULL,
  `staticpageId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  KEY `staticpageId` (`staticpageId`),
  FULLTEXT KEY `details` (`details`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `staticpage` */

insert  into `staticpage`(`id`,`title`,`alias`,`toggle`,`details`,`staticpageId`) values (9,'Страница не найдена','404','y','<strong>initial value1231231aaa</strong>',0);

/*Table structure for table `subscriber` */

DROP TABLE IF EXISTS `subscriber`;

CREATE TABLE `subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

/*Data for the table `subscriber` */

/*Table structure for table `tablename1` */

DROP TABLE IF EXISTS `tablename1`;

CREATE TABLE `tablename1` (
  `test` year(4) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

/*Data for the table `tablename1` */

insert  into `tablename1`(`test`,`id`) values (0000,29);
insert  into `tablename1`(`test`,`id`) values (0000,30);

/*Table structure for table `url` */

DROP TABLE IF EXISTS `url`;

CREATE TABLE `url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `fsection2factionId` (`fsection2factionId`),
  KEY `entityId` (`entityId`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

/*Data for the table `url` */

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `gender` enum('n','f','m') NOT NULL DEFAULT 'n',
  `birth` date NOT NULL DEFAULT '0000-00-00',
  `registration` date NOT NULL DEFAULT '0000-00-00',
  `subscribed` tinyint(1) NOT NULL DEFAULT '0',
  `lastVisit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `activationCode` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '0',
  `sn` enum('n','fb','vk','tw') NOT NULL DEFAULT 'n',
  `changepasswdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changepasswdCode` varchar(255) NOT NULL DEFAULT '',
  `test` int(11) NOT NULL DEFAULT '0',
  `testIds` varchar(255) NOT NULL DEFAULT '',
  `entityId` set('7','8','171') NOT NULL DEFAULT '7',
  `storeRelationAbility` set('many','one','none') NOT NULL DEFAULT 'none',
  `color` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `gender` (`gender`),
  KEY `sn` (`sn`),
  KEY `test` (`test`),
  KEY `testIds` (`testIds`),
  KEY `set` (`entityId`),
  KEY `storeRelationAbility` (`storeRelationAbility`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;

/*Data for the table `user` */

insert  into `user`(`id`,`email`,`title`,`password`,`gender`,`birth`,`registration`,`subscribed`,`lastVisit`,`activated`,`activationCode`,`identifier`,`sn`,`changepasswdDate`,`changepasswdCode`,`test`,`testIds`,`entityId`,`storeRelationAbility`,`color`) values (109,'asd@asd.asd','Первонах1','','n','0000-00-00','0000-00-00',0,'2014-09-24 07:59:38',0,'','0','n','0000-00-00 00:00:00','',0,'','7,8,171','','');

/*Table structure for table `visitor` */

DROP TABLE IF EXISTS `visitor`;

CREATE TABLE `visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `lastActivity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=555197 DEFAULT CHARSET=utf8;

/*Data for the table `visitor` */

insert  into `visitor`(`id`,`title`,`lastActivity`,`userId`) values (555196,'3v0s0pvgep3itif9nq82eif0h0','2014-02-03 19:15:02',0);

/*Table structure for table `year` */

DROP TABLE IF EXISTS `year`;

CREATE TABLE `year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `year` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

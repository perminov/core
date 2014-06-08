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
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `rowRequired` enum('y','n') NOT NULL DEFAULT 'y',
  `javascript` text NOT NULL,
  `type` enum('p','s','o') NOT NULL DEFAULT 'p',
  `display` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `rowRequired` (`rowRequired`),
  KEY `type` (`type`),
  FULLTEXT KEY `javascript` (`javascript`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

/*Data for the table `action` */

insert  into `action`(`id`,`title`,`alias`,`toggle`,`rowRequired`,`javascript`,`type`,`display`) values (1,'Список','index','y','y','','s',0),(2,'Детали','form','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','s',1),(3,'Сохранить','save','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','s',0),(4,'Удалить','delete','y','y','var actionAlias = this.actionAlias;\r\nExt.MessageBox.show({\r\n  title:indi.lang.I_ACTION_DELETE_CONFIRM_TITLE,\r\n  msg: indi.lang.I_ACTION_DELETE_CONFIRM_MSG,\r\n  buttons: Ext.MessageBox.YESNO,\r\n  icon: Ext.MessageBox.QUESTION,\r\n  fn: function(answer, arg2){\r\n     if (answer == \"yes\") {\r\n        indi.load(indi.trail.item().section.href + actionAlias+\"/id/\"+row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");\r\n     }\r\n  }\r\n});','s',1),(5,'Выше','up','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','s',1),(6,'Ниже','down','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','s',1),(7,'Статус','toggle','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','s',1),(18,'Обновить кэш','cache','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','s',1),(19,'Обновить sitemap.xml','sitemap','y','n','$.post(\'/sitemap/index/update\',function(response){\r\n  Ext.MessageBox.show({\r\n    title: \'Сообщение\',\r\n    msg: response,\r\n    buttons: Ext.MessageBox.OK,\r\n    icon: Ext.MessageBox.INFO\r\n  });\r\n});\r\nreturn false;','s',1),(20,'Авторизация','login','y','y','indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/ph/\" + Indi.trail.item().section.primaryHash + \"/aix/\" + aix + \"/\");','o',1);

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

insert  into `admin`(`id`,`profileId`,`title`,`email`,`password`,`toggle`) values (1,1,'Павел Перминов','pavel.perminov.23@gmail.com','vt6yhg5','y'),(14,12,'Василий Теркин','vasily.terkin@gmail.com','vasily','n');

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

insert  into `columntype`(`id`,`title`,`type`,`canStoreRelation`,`elementId`) values (1,'Строка','VARCHAR(255)','y','22,23,1,7'),(3,'Число','INT(11)','y','3,21,4,5,23,1,18'),(4,'Текст','TEXT','y','6,7,8,13'),(5,'Цена','DOUBLE(7,2)','n',''),(6,'Дата','DATE','n','12'),(7,'Год','YEAR','n',''),(8,'Время','TIME','n','17'),(9,'Момент','DATETIME','n','19'),(10,'Одно значение из набора','ENUM','n','5,23'),(11,'Набор значений','SET','n','23,1,6,7'),(12,'Правда/Ложь','BOOLEAN','n','9'),(13,'Цвет','VARCHAR(10)','n','11');

/*Table structure for table `disabledfield` */

DROP TABLE IF EXISTS `disabledfield`;

CREATE TABLE `disabledfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  `displayInForm` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=214 DEFAULT CHARSET=utf8;

/*Data for the table `disabledfield` */

insert  into `disabledfield`(`id`,`sectionId`,`fieldId`,`defaultValue`,`displayInForm`,`title`) values (87,146,1028,'',0,'Аватар'),(84,146,961,'',0,'Аккаунт активирован'),(35,146,685,'',0,'ФИО'),(85,146,962,'',0,'Код активации'),(86,146,699,'',0,'Последний визит'),(43,146,1108,'',0,'Настройки'),(94,146,1577,'',0,'Код'),(93,146,1576,'',0,'Дата последнего запроса'),(92,146,1575,'',0,'Смена пароля'),(91,146,1162,'',0,'ID пользователя в этой соц.сети'),(90,146,1163,'',0,'Какая'),(89,146,1161,'',0,'Социальные сети'),(88,146,698,'',0,'Подписался на рассылку'),(208,379,2181,'',0,'Порядок отображения'),(209,379,2172,'title',0,'Тэг'),(210,380,2181,'',0,'Порядок отображения'),(211,380,2172,'keywords',0,'Тэг'),(212,381,2181,'',0,'Порядок отображения'),(213,381,2172,'description',0,'Тэг');

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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

/*Data for the table `element` */

insert  into `element`(`id`,`title`,`alias`,`storeRelationAbility`,`hidden`) values (1,'Строка','string','none,many',0),(4,'Приоритет отображения','move','none',1),(5,'Радио-кнопки','radio','one',0),(6,'Текст','textarea','none,many',0),(7,'Чекбоксы','multicheck','many',0),(9,'Чекбокс','check','none',0),(11,'Цвет','color','none',0),(12,'Календарь','calendar','none',0),(13,'HTML-редактор','html','none',0),(14,'Файл','upload','none',0),(16,'Группа полей','span','none',0),(17,'Время','time','none',0),(18,'Число','number','none,one',0),(19,'Момент','datetime','none',0),(22,'Скрытое поле','hidden','none',0),(23,'Список','combo','one,many',0);

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
  PRIMARY KEY (`id`),
  KEY `system` (`system`),
  KEY `titleFieldId` (`titleFieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=302 DEFAULT CHARSET=utf8;

/*Data for the table `entity` */

insert  into `entity`(`id`,`title`,`table`,`extends`,`system`,`useCache`,`titleFieldId`) values (1,'Тип столбца','columnType','Indi_Db_Table','y',0,1),(2,'Сущность','entity','Indi_Db_Table','y',1,4),(3,'Раздел','section','Indi_Db_Table','y',0,20),(4,'Элемент управления','element','Indi_Db_Table','y',0,64),(5,'Поле','field','Indi_Db_Table','y',1,7),(6,'Значение из набора','enumset','Indi_Db_Table','y',0,16),(7,'Действие','action','Indi_Db_Table','y',0,31),(8,'Действие в разделе','section2action','Indi_Db_Table','y',0,27),(9,'Столбец грида','grid','Indi_Db_Table','y',0,34),(10,'Профиль','profile','Indi_Db_Table','y',0,36),(11,'Пользователь CMS','admin','Indi_Db_Table','y',0,39),(20,'Копия','resize','Indi_Db_Table','y',0,107),(25,'Статическая страница','staticpage','Indi_Db_Table','o',0,131),(90,'Параметр настройки элемента управления','possibleElementParam','Indi_Db_Table','y',0,472),(91,'Параметр настройки элемента управления, в контексте поля сущности','param','Indi_Db_Table','y',0,477),(101,'Раздел фронтенда','fsection','Indi_Db_Table','y',1,559),(195,'Фильтр','search','Indi_Db_Table','y',0,1443),(128,'Фидбэк','feedback','Indi_Db_Table','o',0,678),(129,'Подписчик','subscriber','Indi_Db_Table','o',0,682),(130,'Пользователь','user','Indi_Db_Table','o',0,685),(146,'Действие, возможное для использования в разделе фронтенда','faction','Indi_Db_Table','y',1,857),(147,'Действие в разделе фронтенда','fsection2faction','Indi_Db_Table','y',1,860),(160,'Посетитель','visitor','Indi_Db_Table','o',0,1100),(162,'Компонент SEO-урла','url','Indi_Db_Table','y',0,0),(171,'Отключенное поле','disabledField','Indi_Db_Table','y',0,1342),(204,'Статический элемент','staticblock','Indi_Db_Table','o',0,1485),(205,'Пункт меню','menu','Indi_Db_Table','o',0,1490),(301,'Компонент содержимого meta-тега','metatag','Indi_Db_Table','y',0,0);

/*Table structure for table `enumset` */

DROP TABLE IF EXISTS `enumset`;

CREATE TABLE `enumset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `javascript` text NOT NULL,
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  FULLTEXT KEY `javascript` (`javascript`)
) ENGINE=MyISAM AUTO_INCREMENT=970 DEFAULT CHARSET=utf8;

/*Data for the table `enumset` */

insert  into `enumset`(`id`,`fieldId`,`title`,`alias`,`javascript`,`move`) values (1,3,'Нет','n','',1),(2,3,'Да','y','',2),(5,22,'Включен','y','',5),(6,22,'<span style=\'color: red\'>Выключен</span>','n','',6),(9,29,'Включено','y','',9),(10,29,'<span style=\'color: red\'>Выключено</span>','n','',10),(11,37,'Включен','y','',11),(12,37,'<span style=\'color: red\'>Выключен</span>','n','',12),(13,42,'Включен','y','',13),(14,42,'<span style=\'color: red\'>Выключен</span>','n','',14),(62,66,'Нет','none','',62),(63,66,'Только с одним значением ключа','one','',63),(64,66,'С набором значений ключей','many','',64),(87,111,'Поменять, но с сохранением пропорций','p','show(\'tr-masterDimensionAlias,tr-masterDimensionValue,tr-slaveDimensionLimitation\'); if(document.getElementById(\'slaveDimensionLimitation\').value==0){ hide(\'tr-slaveDimensionValue\');} else {show(\'tr-slaveDimensionValue\');};',87),(88,111,'Поменять','c','hide(\'tr-masterDimensionAlias,tr-slaveDimensionLimitation\');show(\'tr-masterDimensionValue,tr-slaveDimensionValue\');$(\'#masterDimensionAliasWidth\').click();',88),(89,111,'Не менять','o','hide(\'tr-masterDimensionAlias,tr-masterDimensionValue,tr-slaveDimensionLimitation,tr-slaveDimensionValue\');',89),(91,114,'Ширины','width','',91),(92,114,'Высоты','height','',92),(95,137,'Включена','y','',95),(96,137,'<span style=\'color: red\'>Выключена</span>','n','',96),(112,345,'Да','y','',112),(113,345,'Нет','n','',113),(122,0,'Всем друзьям, кроме указанных в разделе \"Исключения из правил доступа на просмотр блога\"','ae','',122),(162,455,'Переменная сущность','e','show(\'tr-satellite\');hide(\'tr-alternative\');',294),(163,455,'Фильтрация','с','show(\'tr-satellite,tr-alternative\');',165),(181,470,'Нет','none','hide(\'tr-relation,tr-filter,tr-satellitealias,tr-span,tr-satellite,tr-dependency,tr-alternative\');',164),(183,470,'Да, для энного количества значений ключей','many','show(\'tr-relation,tr-filter,tr-satellitealias,tr-span,tr-satellite,tr-dependency,tr-alternative\');',296),(184,470,'Да, но для только одного значения ключа','one','show(\'tr-relation,tr-filter,tr-satellitealias,tr-span,tr-satellite,tr-dependency,tr-alternative\');',295),(187,455,'Отсутствует','u','hide(\'tr-satellite,tr-alternative\');$(\'#satellite\').val(0).change();',148),(213,557,'По возрастанию','ASC','',297),(214,557,'По убыванию','DESC','',182),(219,594,'Да','y','show(\'tr-color\');',299),(220,594,'Нет','n','hide(\'tr-color\');',300),(227,612,'Проектная','n','hide(\'tr-useCache\');$(\'#useCache1\').attr(\'checked\', false);',301),(228,612,'<span style=\'color: red\'>Системная</span>','y','show(\'tr-useCache\');',186),(241,689,'Мужской','m','',427),(242,689,'Женский','f','',309),(572,1365,'<font color=lime>Типовое</font>','o','',461),(571,1365,'<font color=red>Системное</font>','s','',460),(570,1365,'Проектное','p','',0),(580,1445,'Включено','y','',0),(581,1445,'<span style=\'color: red\'>Выключено</span>','n','',464),(328,0,'Очень плохо','1','',254),(480,1040,'Одностроковый','s','hide(\'tr-orderColumn,tr-orderDirection,tr-filter,tr-rppId,tr-move,tr-defaultLimit,tr-orderBy,tr-orderExpression\');show(\'tr-where,tr-index\');',408),(478,1027,'Для jQuery.post()','j','',407),(479,1040,'Обычный','r','show(\'tr-filter,tr-rppId,tr-move,tr-defaultLimit,tr-orderBy,tr-orderColumn,tr-orderDirection,tr-orderExpression\');hide(\'tr-where,tr-index\');',0),(477,1027,'Обычное','r','',0),(574,1366,'<font color=red>Системный</font>','s','',462),(573,1366,'Проектный','p','',0),(575,1366,'<font color=lime>Типовой</font>','o','',463),(458,1009,'SQL-выражению','e','if($(\'#typeR\').attr(\'checked\')) hide(\'tr-orderColumn,tr-orderDirection\');show(\'tr-orderExpression\');',396),(457,1009,'Одному из имеющихся столбцов','c','if($(\'#typeR\').attr(\'checked\')) show(\'tr-orderColumn,tr-orderDirection\');hide(\'tr-orderExpression\');',0),(459,1011,'По возрастанию','ASC','',0),(460,1011,'По убыванию','DESC','',0),(484,1074,'Над записью','r','',0),(485,1074,'Над набором записей','rs','',411),(486,1074,'Только независимые множества, если нужно','n','',412),(509,689,'Не&nbsp;указан','n','',192),(567,1364,'Проектное','p','',0),(568,1364,'<font color=red>Системное</font>','s','',458),(569,1364,'<font color=lime>Типовое</font>','o','',459),(516,1163,'Никакая','n','hide(\'tr-identifier\');',0),(517,1163,'Facebook','fb','show(\'tr-identifier\');',432),(518,1163,'Вконтакте','vk','show(\'tr-identifier\');',433),(519,1163,'Twitter','tw','show(\'tr-identifier\');',434),(969,2176,'Запись','row','if($(\'#source\').val() == \'row\') show(\'tr-fieldId\');else hide(\'tr-fieldId\');$(\'#entityId\').val(Indi.combo.form.store.entityId.ids[0]).change();',969),(968,2176,'Действие','action','hide(\'tr-fieldId\');',968),(967,2176,'Раздел','section','hide(\'tr-fieldId\');',967),(566,612,'<font color=lime>Типовая</font>','o','hide(\'tr-useCache\');$(\'#useCache1\').attr(\'checked\', false);',457),(582,1488,'Включен','y','',0),(583,1488,'<span style=\'color: red\'>Выключен</span>','n','',465),(584,1491,'Нет','n','hide(\'tr-staticpageId\');show(\'tr-url\');',0),(585,1491,'Да','y','show(\'tr-staticpageId\');hide(\'tr-url\');$(\'#url\').val(\'\');',466),(586,1494,'Включен','y','',0),(587,1494,'<span style=\'color: red\'>Выключен</span>','n','',467),(594,1515,'HTML','html','show(\'tr-detailsHtml,tr-detailsHtmlWide,tr-detailsHtmlWidth,tr-detailsHtmlHeight,tr-detailsHtmlBodyClass,tr-detailsHtmlStyle\');hide(\'tr-detailsString,tr-detailsTextarea\');',0),(595,1515,'Строка','string','hide(\'tr-detailsHtml,tr-detailsHtmlWide,tr-detailsHtmlWidth,tr-detailsHtmlHeight,tr-detailsHtmlBodyClass,tr-detailsHtmlStyle,tr-detailsTextarea\');show(\'tr-detailsString\');',471),(596,1515,'Текст','textarea','hide(\'tr-detailsHtml,tr-detailsHtmlWide,tr-detailsHtmlWidth,tr-detailsHtmlHeight,tr-detailsHtmlBodyClass,tr-detailsHtmlStyle,tr-detailsString\');show(\'tr-detailsTextarea\');',472),(597,1495,'Да','y','',0),(598,1495,'Нет','n','',473),(608,1533,'<span style=\'color:red;\'>Выключен</span>','n','',478),(607,1533,'Включен','y','',0),(962,2172,'&lt;title&gt;..&lt;/title&gt;','title','',962),(963,2172,'&lt;meta name=\"keywords\" content=\"..\"/&gt;','keywords','',963),(964,2172,'&lt;meta name=\"description\" content=\"..\"/&gt;','description','',964),(965,2173,'Статический','static','hide(\'tr-fieldId,tr-source,tr-up\');show(\'tr-content\');',965),(966,2173,'Динамический','dynamic','show(\'tr-source,tr-up\');hide(\'tr-content\');if($(\'#source\').val() == \'row\') show(\'tr-fieldId\');else hide(\'tr-fieldId\');',966),(960,2159,'Включен','y','',0),(961,2159,'<span style=\'color: red\'>Выключен</span>','n','',489);

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

insert  into `faction`(`id`,`title`,`alias`,`maintenance`,`type`) values (1,'По умолчанию','index','rs','s'),(2,'Просмотр','details','r','s'),(3,'Изменить','form','r','s'),(5,'Добавить','create','n','s'),(6,'Активация аккаунта','activation','n','o'),(36,'Регистрация','registration','n','o'),(37,'Восстановление доступа','changepasswd','n','o');

/*Table structure for table `feedback` */

DROP TABLE IF EXISTS `feedback`;

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
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
  `javascript` text NOT NULL,
  `satellite` int(11) NOT NULL DEFAULT '0',
  `dependency` enum('e','с','u') NOT NULL DEFAULT 'u',
  `storeRelationAbility` enum('none','many','one') NOT NULL DEFAULT 'none',
  `alternative` varchar(255) NOT NULL DEFAULT '',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `satellitealias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `columnTypeId` (`columnTypeId`),
  KEY `elementId` (`elementId`),
  KEY `relation` (`relation`),
  KEY `satellite` (`satellite`),
  KEY `dependency` (`dependency`),
  KEY `storeRelationAbility` (`storeRelationAbility`),
  FULLTEXT KEY `javascript` (`javascript`)
) ENGINE=MyISAM AUTO_INCREMENT=2185 DEFAULT CHARSET=utf8;

/*Data for the table `field` */

insert  into `field`(`id`,`entityId`,`title`,`alias`,`columnTypeId`,`elementId`,`defaultValue`,`move`,`relation`,`javascript`,`satellite`,`dependency`,`storeRelationAbility`,`alternative`,`filter`,`satellitealias`) values (1,1,'Наименование','title',1,1,'',1,0,'',0,'u','none','0','',''),(2,1,'Тип столбца MySQL','type',1,1,'',2,0,'',0,'u','none','0','',''),(3,1,'Пригоден для хранения внешних ключей','canStoreRelation',10,5,'n',3,6,'',0,'u','one','0','',''),(4,2,'Наименование','title',1,1,'',4,0,'',0,'u','none','0','',''),(5,2,'Таблица БД','table',1,1,'',5,0,'',0,'u','none','0','',''),(6,5,'Сущность, в структуру которой входит это поле','entityId',3,23,'0',6,2,'',0,'u','one','0','',''),(7,5,'Наименование поля','title',1,1,'',7,0,'',0,'u','none','0','',''),(8,5,'Наименование соответствующего полю столбца в  таблице БД','alias',1,1,'',8,0,'',0,'u','none','0','',''),(9,5,'Тип столбца MySQL','columnTypeId',3,23,'0',11,1,'',10,'с','one','0','',''),(10,5,'Элемент управления','elementId',3,23,'0',10,4,'',470,'с','one','0','',''),(11,5,'Значение по умолчанию','defaultValue',1,1,'',12,0,'',0,'u','none','0','',''),(12,5,'Ключи какой сущности будут храниться в этом поле','relation',3,23,'0',344,2,'',0,'u','one','0','',''),(14,5,'Положение в списке','move',3,4,'0',13,0,'',0,'u','none','0','',''),(15,6,'Поле','fieldId',3,23,'0',15,5,'',0,'u','one','0','',''),(16,6,'Наименование','title',1,1,'',16,0,'',0,'u','none','0','',''),(17,6,'Псевдоним','alias',1,1,'',17,0,'',0,'u','none','0','',''),(18,3,'Подчинен разделу','sectionId',3,23,'0',19,3,'',0,'u','one','0','',''),(19,3,'Cущность, с которой будет работать раздел','entityId',3,23,'0',25,2,'',0,'u','one','0','',''),(1344,3,'Настройки формы','formSettings',0,16,'',1294,0,'',0,'u','none','','',''),(20,3,'Наименование','title',1,1,'',18,0,'',0,'u','none','0','',''),(21,3,'Контроллер','alias',1,1,'',21,0,'',0,'u','none','0','',''),(22,3,'Статус','toggle',10,5,'y',20,6,'',0,'u','one','0','',''),(23,3,'Положение в списке','move',3,4,'',443,0,'',0,'u','none','0','',''),(25,3,'Количество строк на странице','rowsOnPage',3,1,'25',1277,0,'',0,'u','none','0','',''),(26,8,'Раздел, за которым закреплено действие','sectionId',3,23,'',1,3,'',0,'u','one','0','',''),(27,8,'Действие','actionId',3,23,'',1,7,'',0,'u','one','0','',''),(28,8,'Профили пользователей, имеющих доступ к этому действию в этом разделе','profileIds',1,7,'14',1,10,'',0,'u','many','0','',''),(29,8,'Статус','toggle',10,5,'y',1,6,'',0,'u','one','0','',''),(30,8,'Положение в списке','move',3,4,'',1,0,'',0,'u','none','0','',''),(31,7,'Наименование','title',1,1,'',26,0,'',0,'u','none','0','',''),(32,7,'Псевдоним','alias',1,1,'',27,0,'',0,'u','none','0','',''),(33,9,'Раздел','sectionId',3,23,'',28,3,'',0,'u','one','0','',''),(34,9,'Столбец','fieldId',3,23,'',29,5,'',33,'с','one','entityId','',''),(35,9,'Очередность отображения столбца в гриде','move',3,4,'',30,0,'',0,'u','none','0','',''),(36,10,'Наименование','title',1,1,'',31,0,'',0,'u','none','0','',''),(37,10,'Статус','toggle',10,5,'y',32,6,'',0,'u','one','0','',''),(38,11,'Профиль','profileId',3,23,'',33,10,'',0,'u','one','0','',''),(39,11,'Фамилия Имя','title',1,1,'',34,0,'',0,'u','none','0','',''),(40,11,'Email (используется в качестве логина)','email',1,1,'',35,0,'',0,'u','none','0','',''),(41,11,'Пароль','password',1,1,'',36,0,'',0,'u','none','0','',''),(42,11,'Статус','toggle',10,5,'y',37,6,'',0,'u','one','0','',''),(64,4,'Наименование','title',1,1,'',53,0,'',0,'u','none','0','',''),(65,4,'Псевдоним','alias',1,1,'',54,0,'',0,'u','none','0','',''),(66,4,'Способен работать с внешними ключами','storeRelationAbility',11,23,'none',55,6,'',0,'u','many','0','',''),(1445,195,'Статус','toggle',10,5,'y',1316,6,'',0,'u','one','','',''),(92,4,'Скрывать при генерации формы','hidden',12,9,'0',72,0,'',0,'u','none','0','',''),(106,20,'Поле','fieldId',3,23,'0',86,5,'',0,'u','one','0','',''),(107,20,'Наименование','title',1,1,'',87,0,'',0,'u','none','0','',''),(108,20,'Псевдоним','alias',1,1,'',88,0,'',0,'u','none','0','',''),(109,20,'Ширина','masterDimensionValue',3,18,'0',91,0,'',0,'u','none','0','',''),(110,20,'Высота','slaveDimensionValue',3,18,'0',93,0,'',0,'u','none','0','',''),(111,20,'Размер','proportions',10,5,'o',89,6,'',0,'u','one','0','',''),(112,20,'Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>','slaveDimensionLimitation',12,9,'1',92,0,'if($(\'#proportionsP\').hasClass(\'checked\')){if(!$(this).hasClass(\'checked\')) hide(\'tr-slaveDimensionValue\'); else show(\'tr-slaveDimensionValue\');}',0,'u','none','0','',''),(114,20,'При расчете пропорций отталкиваться от','masterDimensionAlias',10,5,'width',90,6,'$(\'#td-left-masterDimensionValue\').text(this.value==\'height\' ? \'Высота:\':\'Ширина:\');$(\'#td-left-slaveDimensionValue\').text(this.value!=\'height\' ? \'Высота:\':\'Ширина:\');$(\'#slaveDimensionTitle\').text(this.value!=\'height\' ? \'высоту\':\'ширину\');',0,'u','one','0','',''),(115,6,'Javascript-сценарий при выборе этого значения','javascript',4,6,'',94,0,'',0,'u','none','0','',''),(116,5,'Javascript-сценарий при изменении значения поля','javascript',4,6,'',95,0,'',0,'u','none','0','',''),(131,25,'Наименование','title',1,1,'',101,0,'',0,'u','none','0','',''),(133,25,'Псевдоним','alias',1,1,'',102,0,'',0,'u','none','0','',''),(137,25,'Статус','toggle',10,5,'y',1667,6,'',0,'u','one','','',''),(345,7,'Для выполнения действия необходимо выбрать стоку','rowRequired',10,5,'y',308,6,'',0,'u','one','0','',''),(346,7,'Javascript','javascript',4,6,'indi.load(indi.trail.item().section.href + this.actionAlias + \"/id/\" + row.id + \"/\");',1292,0,'',0,'u','none','0','',''),(347,3,'Javascript для выполнения после загрузки грида','javascript',4,6,'',1279,0,'',0,'u','none','0','',''),(377,6,'Порядок отображения','move',3,4,'0',338,0,'',0,'u','none','0','',''),(383,5,'Столбец-satellite','satellite',3,23,'0',701,5,'',6,'с','one','0','',''),(454,5,'Динамическое обновление','span',0,16,'',428,0,'',0,'u','none','0','',''),(455,5,'Тип зависимости','dependency',10,5,'u',523,6,'',0,'u','one','0','',''),(470,5,'Предназначено для хранения ключей','storeRelationAbility',10,23,'none',9,6,'',0,'u','one','0','',''),(471,90,'Элемент управления','elementId',3,23,'0',429,4,'',0,'u','one','0','',''),(472,90,'Наименование','title',1,1,'',430,0,'',0,'u','none','0','',''),(473,90,'Псевдоним','alias',1,1,'',431,0,'',0,'u','none','0','',''),(474,90,'Значение по умолчанию','defaultValue',1,1,'',432,0,'',0,'u','none','0','',''),(475,1,'Пригоден для работы с элементами управления','elementId',1,23,'',433,4,'',0,'u','many','0','',''),(476,91,'В контексте какого поля','fieldId',3,23,'0',434,5,'',0,'u','one','0','',''),(477,91,'Параметр настройки','possibleParamId',3,23,'0',435,90,'',476,'с','one','elementId','',''),(478,91,'Значение параметра','value',4,6,'',436,0,'',0,'u','none','0','',''),(485,3,'Javascript для выполнения после загрузки формы','javascriptForm',4,6,'',1423,0,'',0,'u','none','0','',''),(502,3,'От какого класса наследовать класс контроллера','extends',1,1,'Indi_Controller_Admin',23,0,'',0,'u','none','0','',''),(503,3,'По умолчанию сортировать грид по столбцу','defaultSortField',3,23,'0',462,5,'',19,'с','one','0','',''),(504,3,'Настройки грида','grid',0,16,'',460,0,'',0,'u','none','0','',''),(555,2,'От какого класса наследовать','extends',1,1,'Indi_Db_Table',512,0,'',0,'u','none','0','',''),(557,3,'Направление сортировки','defaultSortDirection',10,5,'ASC',514,6,'',0,'u','none','0','',''),(559,101,'Наименование','title',1,1,'',517,0,'',0,'u','none','0','',''),(560,101,'Псевдоним','alias',1,1,'',519,0,'',0,'u','none','0','',''),(563,101,'Прикрепленная сущность','entityId',3,23,'0',520,2,'',0,'u','one','0','',''),(566,5,'Если имя столбца-satellite не найдено, то по какому другому столбцу фильтровать','alternative',1,1,'',775,0,'',0,'u','none','','',''),(581,101,'Соответствующий раздел бэкенда','sectionId',3,23,'0',534,3,'',0,'u','one','','',''),(585,101,'Порядок отображения соответствующего пункта в меню','move',3,4,'0',950,0,'',0,'u','none','','',''),(594,20,'Изменить оттенок','changeColor',10,5,'n',545,6,'',0,'u','one','','',''),(595,20,'Оттенок','color',13,11,'',546,0,'',0,'u','none','','',''),(612,2,'Тип','system',10,5,'n',559,6,'',0,'u','one','','',''),(678,128,'Имя','title',1,1,'',625,0,'',0,'u','none','','',''),(679,128,'Email','email',1,1,'',626,0,'',0,'u','none','','',''),(680,128,'Сообщение','message',4,6,'',627,0,'',0,'u','none','','',''),(681,128,'Дата','date',6,12,'0000-00-00',628,0,'',0,'u','none','','',''),(682,129,'Email','title',1,1,'',629,0,'',0,'u','none','','',''),(683,129,'Дата','date',6,12,'0000-00-00',630,0,'',0,'u','none','','',''),(684,130,'Email','email',1,1,'',633,0,'',0,'u','none','','',''),(685,130,'ФИО','title',1,1,'',632,0,'',0,'u','none','','',''),(686,130,'Пароль','password',1,1,'',634,0,'',0,'u','none','','',''),(689,130,'Пол','gender',10,5,'n',969,6,'',0,'u','one','','',''),(690,130,'Дата рождения','birth',6,12,'0000-00-00',1099,0,'',0,'u','none','','',''),(691,130,'Дата регистрации','registration',6,12,'<?=date(\'Y-m-d\')?>',636,0,'',0,'u','none','','',''),(1364,7,'Тип','type',10,5,'p',2099,6,'',0,'u','one','','',''),(1365,146,'Тип','type',10,5,'p',1293,6,'',0,'u','one','','',''),(698,130,'Подписался на рассылку','subscribed',12,9,'0',1443,0,'',0,'u','none','','',''),(699,130,'Последний визит','lastVisit',9,19,'',640,0,'',0,'u','none','','',''),(1444,195,'Порядок отображения','move',3,4,'0',1315,0,'',0,'u','none','','',''),(1442,195,'Раздел','sectionId',3,23,'0',1313,3,'',0,'u','one','','',''),(1443,195,'Поле прикрепленной к разделу сущности','fieldId',3,23,'0',1314,5,'',1442,'с','one','entityId','`elementId` NOT IN (4,14,16,20,22)',''),(1441,2,'Включить в кэш','useCache',12,9,'0',1312,0,'',0,'u','none','','',''),(754,5,'Статическая фильтрация','filter',1,1,'',413,0,'',0,'u','none','','',''),(767,3,'Статическая фильтрация','filter',1,1,'',461,0,'',0,'u','none','','',''),(828,5,'Псевдоним поля для использования в satellite-функциональности','satellitealias',1,1,'',414,0,'',0,'u','none','','',''),(857,146,'Наименование','title',1,1,'',803,0,'',0,'u','none','','',''),(858,146,'Псевдоним','alias',1,1,'',804,0,'',0,'u','none','','',''),(859,147,'Раздел фронтенда','fsectionId',3,23,'0',805,101,'',0,'u','one','','',''),(860,147,'Действие','factionId',3,23,'0',806,146,'',0,'u','one','','',''),(868,101,'Вышестоящий раздел','fsectionId',3,23,'0',516,101,'',0,'u','one','','',''),(869,101,'Статическая фильтрация','filter',1,1,'',814,0,'',0,'u','none','','',''),(960,101,'Количество строк для отображения по умолчанию','defaultLimit',3,18,'20',951,0,'',0,'u','none','','',''),(961,130,'Аккаунт активирован','activated',12,9,'0',637,0,'',0,'u','none','','',''),(962,130,'Код активации','activationCode',1,1,'',639,0,'',0,'u','none','','',''),(980,147,'Использовать версию верстки №','imposition',3,18,'0',921,0,'',0,'u','none','','',''),(1366,3,'Тип','type',10,5,'p',22,6,'',0,'u','one','','',''),(1009,101,'По умолчанию сортировка по','orderBy',10,5,'c',952,6,'',0,'u','one','','',''),(1010,101,'Столбец сортировки','orderColumn',3,23,'0',953,5,'',563,'с','one','','',''),(1011,101,'Направление сортировки','orderDirection',10,5,'ASC',981,6,'',0,'u','one','','',''),(1012,101,'SQL-выражение','orderExpression',1,1,'',982,0,'',0,'u','none','','',''),(1027,147,'Тип','type',10,5,'r',968,6,'',0,'u','one','','',''),(1028,130,'Аватар','avatar',0,14,'',1100,0,'',0,'u','none','','',''),(1040,101,'Тип','type',10,5,'r',538,6,'',0,'u','one','','',''),(1041,101,'Где брать идентификатор','where',1,1,'',983,0,'',0,'u','none','','',''),(1042,101,'Действие по умолчанию','index',1,1,'',1403,0,'',0,'u','none','','',''),(1074,146,'Выполнять maintenance()','maintenance',10,5,'r',1015,6,'',0,'u','one','','',''),(1100,160,'Id сессии','title',1,1,'',1040,0,'',0,'u','none','','',''),(1101,160,'Дата последней активности','lastActivity',9,19,'0000-00-00 00:00:00',1041,0,'',0,'u','none','','',''),(1102,160,'Пользователь','userId',3,23,'0',1042,130,'',0,'u','one','','',''),(1108,130,'Настройки','settings',0,16,'',1101,0,'',0,'u','none','','',''),(1161,130,'Социальные сети','socialNetworks',0,16,'',1444,0,'',0,'u','none','','',''),(1162,130,'ID пользователя в этой соц.сети','identifier',1,1,'',1707,0,'',0,'u','none','','',''),(1163,130,'Какая','sn',10,5,'n',1445,6,'',0,'u','one','','',''),(1191,147,'Не указывать действие при создании seo-урлов из системных','blink',12,9,'0',1259,0,'if($(this).attr(\'checked\')) hide(\'tr-rename,tr-alias\');else {show(\'tr-rename\');if($(\'#rename1\').attr(\'checked\')) show(\'tr-alias\')}',0,'u','none','','',''),(1192,162,'Раздел фронтенда','fsectionId',3,23,'0',1127,101,'',0,'u','one','','',''),(1193,162,'Действие в разделе фронтенда','fsection2factionId',3,23,'0',1128,147,'',1192,'с','one','','',''),(1194,162,'Компонент','entityId',3,23,'0',1129,2,'',0,'u','one','','',''),(1195,162,'Очередность','move',3,4,'0',1130,0,'',0,'u','none','','',''),(1196,162,'Префикс','prefix',1,1,'',1131,0,'',0,'u','none','','',''),(2184,195,'Игнорировать optionTemplate','ignoreTemplate',12,9,'1',2183,0,'',0,'u','none','','',''),(2183,195,'Статическая фильтрация','filter',1,1,'',2167,0,'',0,'u','none','','',''),(2176,301,'Источник','source',10,23,'section',2176,6,'',0,'u','one','','',''),(2177,301,'Сущность','entityId',3,23,'0',2177,2,'',0,'u','one','','`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)',''),(2178,301,'Свойство','fieldId',3,23,'0',2178,5,'',2177,'с','one','','',''),(2179,301,'Префикс','prefix',1,1,'',2179,0,'',0,'u','none','','',''),(2180,301,'Постфикс','postfix',1,1,'',2180,0,'',0,'u','none','','',''),(2181,301,'Порядок отображения','move',3,18,'0',2181,0,'',0,'u','none','','',''),(2182,195,'Значение по умолчанию','defaultValue',1,1,'',2182,0,'',0,'u','none','','',''),(1325,147,'Переименовать действие при генерации seo-урла','rename',12,9,'0',1260,0,'if($(this).attr(\'checked\')) show(\'tr-alias\'); else hide(\'tr-alias\');',0,'u','none','','',''),(1326,147,'Псевдоним','alias',1,1,'',1261,0,'',0,'u','none','','',''),(1327,147,'Настройки SEO','seoSettings',0,16,'',1126,0,'',0,'u','none','','',''),(1337,10,'Cущность, экземпляры которой тоже будут иметь доступ к CMS с данным профилем','entityId',3,23,'0',1271,2,'',0,'u','one','','`system`!=\'y\'',''),(1341,171,'Раздел','sectionId',3,23,'0',1275,3,'',0,'u','one','','',''),(1342,171,'Поле, которое должно быть отключено','fieldId',3,23,'0',1276,5,'',1341,'с','one','entityId','',''),(1345,3,'Отключить кнопку Add','disableAdd',12,9,'0',1278,0,'',0,'u','none','','',''),(1509,204,'Ширина','detailsHtmlWidth',3,18,'0',1383,0,'CKEDITOR.instances[\'detailsHtml\'].resize(parseInt(this.value)+52);',0,'u','none','','',''),(1532,171,'Значение по умолчанию','defaultValue',1,1,'',1402,0,'',0,'u','none','','',''),(1485,204,'Наименование','title',1,1,'',1356,0,'',0,'u','none','','',''),(1486,204,'Псевдоним','alias',1,1,'',1357,0,'',0,'u','none','','',''),(1487,204,'Значение','detailsHtml',4,13,'',1382,0,'',0,'u','none','','',''),(1488,204,'Статус','toggle',10,5,'y',1358,6,'',0,'u','one','','',''),(1489,205,'Вышестояший пункт','menuId',3,23,'0',1359,205,'',0,'u','one','','',''),(1490,205,'Наименование','title',1,1,'',1360,0,'',0,'u','none','','',''),(1491,205,'Связан со статической страницей','linked',10,5,'n',1361,6,'',0,'u','one','','',''),(1492,205,'Статическая страница','staticpageId',3,23,'0',1362,25,'',0,'u','one','','',''),(1493,205,'Ссылка','url',1,1,'',1363,0,'',0,'u','none','','',''),(1494,205,'Статус','toggle',10,23,'y',1364,6,'',0,'u','one','','',''),(1495,205,'Отображать в нижнем меню','bottom',10,5,'y',1365,6,'',0,'u','one','','',''),(1496,205,'Порядок отображения','move',3,4,'0',1366,0,'',0,'u','none','','',''),(1814,25,'Контент','details',4,13,'',1666,0,'',0,'u','none','','',''),(1510,204,'Контент','detailsSpan',0,16,'',1379,0,'',0,'u','none','','',''),(1511,204,'Высота','detailsHtmlHeight',3,18,'200',1384,0,'CKEDITOR.instances[\'detailsHtml\'].resize(parseInt($(\'detailsHtmlWidth\').val()),parseInt(this.value)+106);',0,'u','none','','',''),(1513,204,'Css класс для body','detailsHtmlBodyClass',1,1,'',1385,0,'',0,'u','none','','',''),(1514,204,'Css стили','detailsHtmlStyle',4,6,'',1386,0,'',0,'u','none','','',''),(1515,204,'Тип','type',10,5,'html',1380,6,'',0,'u','one','','',''),(1516,204,'Значение','detailsString',1,1,'',1387,0,'',0,'u','none','','',''),(1517,204,'Значение','detailsTextarea',4,6,'',1557,0,'',0,'u','none','','',''),(2173,301,'Тип компонента','type',10,23,'static',2173,6,'',0,'u','one','','',''),(2174,301,'Компонент','content',1,1,'',2174,0,'',0,'u','none','','',''),(2175,301,'Шагов вверх','up',3,18,'0',2175,0,'if(parseInt($(this).val()) < 0) $(this).val(0); else if (parseInt($(this).val()) > Indi.combo.form.store.entityId.data.length-1) $(this).val(Indi.combo.form.store.entityId.data.length-1); else {$.post(Indi.trail.item().section.href+\'/form/\',{up: $(this).val(), fsectionId: $(\'#fsectionId\').val()}, function(json){if(json.state == \'ok\')$(\'#entityId\').val(json.entityId).change();else Ext.MessageBox.show({title: \'Ошибка\', message: json.error})},\'json\')}',0,'u','none','','',''),(2172,301,'Тэг','tag',10,23,'title',2172,6,'',0,'u','one','','',''),(2171,301,'Действие','fsection2factionId',3,23,'0',2171,147,'',2170,'с','one','','',''),(2170,301,'Раздел','fsectionId',3,23,'0',2170,101,'',0,'u','one','','',''),(1533,101,'Статус','toggle',10,5,'y',1429,6,'',0,'u','one','','',''),(1554,3,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',310,5,'',19,'с','one','','`relation`!=\"0\"',''),(1560,101,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',815,5,'',868,'с','one','entityId','',''),(1562,101,'От какого класса наследовать класс контроллера','extends',1,1,'',1431,0,'',0,'u','none','','',''),(1575,130,'Смена пароля','changepasswd',0,16,'',1708,0,'',0,'u','none','','',''),(1576,130,'Дата последнего запроса','changepasswdDate',9,19,'0000-00-00 00:00:00',1709,0,'',0,'u','none','','',''),(1577,130,'Код','changepasswdCode',1,1,'',1710,0,'',0,'u','none','','',''),(1658,195,'Альтернативное наименование','alt',1,1,'',1520,0,'',0,'u','none','','',''),(2132,10,'Порядок отображения','move',3,4,'0',2132,0,'',0,'u','none','','',''),(1886,9,'Изменить название столбца на','alterTitle',1,1,'',1738,0,'',0,'u','none','','',''),(2100,7,'Отображать в панели действий грида','display',12,9,'1',2100,0,'',0,'u','none','','',''),(2131,10,'Домашняя страница','home',4,13,'',2131,0,'',0,'u','none','','',''),(2159,9,'Статус','toggle',10,5,'y',2133,6,'',0,'u','one','','',''),(2161,171,'Отображать в форме','displayInForm',12,9,'0',2134,0,'',0,'u','none','','',''),(2166,91,'Auto title','title',1,1,'',2166,0,'',0,'u','none','','',''),(2163,2,'Заголовочное поле','titleFieldId',3,23,'0',2163,5,'',0,'u','one','','`entityId` = \"<?=$this->id?>\" AND `columnTypeId` != \"0\"',''),(2164,8,'Auto title','title',1,1,'',2164,0,'',0,'u','none','','',''),(2165,9,'Auto title','title',1,1,'',2165,0,'',0,'u','none','','',''),(2167,195,'Auto title','title',1,1,'',2184,0,'',0,'u','none','','',''),(2168,147,'Auto title','title',1,1,'',2168,0,'',0,'u','none','','',''),(2169,171,'Auto title','title',1,1,'',2169,0,'',0,'u','none','','','');

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

insert  into `fsection`(`id`,`title`,`alias`,`entityId`,`sectionId`,`move`,`fsectionId`,`filter`,`defaultLimit`,`orderBy`,`orderColumn`,`orderDirection`,`orderExpression`,`type`,`where`,`index`,`toggle`,`parentSectionConnector`,`extends`) values (8,'Пользователи','users',130,146,28,0,'',20,'c',0,'ASC','','r','','','y',0,''),(37,'Статические страницы','static',25,30,39,0,'',20,'c',0,'ASC','','r','','','y',0,''),(22,'Фидбэк','feedback',128,144,44,0,'',20,'c',0,'ASC','','s','\"\"','add','n',0,''),(26,'Мой профиль','myprofile',130,0,22,0,'',20,'c',0,'ASC','','s','`id` = \'<?=$_SESSION[\'user\'][\'id\']?>\'','form','y',0,'My'),(39,'Главная','index',0,0,8,0,'',20,'c',0,'ASC','','r','','','y',0,''),(41,'Карта сайта','sitemap',101,113,41,0,'`toggle`=\"y\"',20,'c',585,'ASC','','r','','','y',0,'');

/*Table structure for table `fsection2faction` */

DROP TABLE IF EXISTS `fsection2faction`;

CREATE TABLE `fsection2faction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `factionId` int(11) NOT NULL DEFAULT '0',
  `imposition` int(11) NOT NULL DEFAULT '0',
  `type` enum('j','r') NOT NULL DEFAULT 'r',
  `blink` tinyint(1) NOT NULL DEFAULT '0',
  `rename` tinyint(1) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `factionId` (`factionId`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;

/*Data for the table `fsection2faction` */

insert  into `fsection2faction`(`id`,`fsectionId`,`factionId`,`imposition`,`type`,`blink`,`rename`,`alias`,`title`) values (126,8,37,0,'r',0,0,'','Восстановление доступа'),(129,22,1,0,'r',0,0,'','По умолчанию'),(124,26,3,0,'r',0,0,'','Изменить'),(127,37,2,0,'r',0,0,'','Просмотр'),(123,39,1,0,'r',0,0,'','По умолчанию'),(128,41,1,0,'r',0,0,'','По умолчанию'),(125,8,6,0,'r',0,0,'','Активация аккаунта');

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
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=2313 DEFAULT CHARSET=utf8;

/*Data for the table `grid` */

insert  into `grid`(`id`,`sectionId`,`fieldId`,`move`,`alterTitle`,`toggle`,`title`) values (1,2,1,1,'','y','Наименование'),(2,2,2,2,'','y','Тип столбца MySQL'),(3,2,3,3,'','y','Пригоден для хранения внешних ключей'),(4,5,4,4,'','y','Наименование'),(5,5,5,5,'','y','Таблица БД'),(6,6,7,6,'','y','Наименование поля'),(7,6,8,7,'','y','Наименование соответствующего полю столбца в  таблице БД'),(8,6,9,8,'','y','Тип столбца MySQL'),(9,6,10,9,'','y','Элемент управления'),(10,6,11,10,'','y','Значение по умолчанию'),(11,6,12,11,'','y','Ключи какой сущности будут храниться в этом поле'),(13,6,14,13,'','y','Положение в списке'),(14,7,19,16,'Сущность','y','Cущность, с которой будет работать раздел'),(15,7,20,14,'','y','Наименование'),(16,7,21,15,'','y','Контроллер'),(17,7,22,17,'','y','Статус'),(18,7,23,18,'','y','Положение в списке'),(20,7,25,926,'','y','Количество строк на странице'),(23,8,27,23,'','y','Действие'),(24,8,29,24,'','y','Статус'),(25,8,30,25,'','y','Положение в списке'),(26,10,31,26,'','y','Наименование'),(27,10,32,27,'','y','Псевдоним'),(29,11,34,29,'','y','Столбец'),(30,11,35,1156,'','y','Очередность отображения столбца в гриде'),(32,13,36,32,'','y','Наименование'),(33,13,37,33,'','y','Статус'),(34,12,16,34,'','y','Наименование'),(35,12,17,35,'','y','Псевдоним'),(36,14,39,36,'','y','Фамилия Имя'),(37,14,40,37,'','y','Email (используется в качестве логина)'),(38,14,41,38,'','y','Пароль'),(39,14,42,39,'','y','Статус'),(42,16,65,43,'','y','Псевдоним'),(43,16,66,44,'','y','Способен работать с внешними ключами'),(46,16,64,42,'','y','Наименование'),(89,22,107,56,'','y','Наименование'),(90,22,108,57,'','y','Псевдоним'),(91,22,109,59,'','y','Ширина'),(92,22,110,60,'','y','Высота'),(93,22,111,58,'','y','Размер'),(94,22,112,61,'','y','Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>'),(130,30,131,61,'','y','Наименование'),(132,30,133,63,'','y','Псевдоним'),(2283,379,2174,1950,'Статический','y','Компонент'),(2284,379,2175,1954,'','y','Шагов вверх'),(136,30,137,67,'','y','Статус'),(341,12,377,253,'','y','Порядок отображения'),(375,100,472,289,'','y','Наименование'),(376,100,473,290,'','y','Псевдоним'),(377,100,474,291,'','y','Значение по умолчанию'),(378,101,477,292,'','y','Параметр настройки'),(379,101,478,293,'','y','Значение параметра'),(383,8,28,297,'','y','Профили пользователей, имеющих доступ к этому действию в этом разделе'),(1335,7,1366,31,'','y','Тип'),(1334,113,1040,925,'','y','Тип'),(1333,172,1365,924,'','y','Тип'),(1332,10,1364,923,'','y','Тип'),(420,113,559,328,'','y','Наименование'),(421,113,560,329,'','y','Псевдоним'),(424,113,563,330,'','y','Прикрепленная сущность'),(443,5,612,347,'','y','Тип'),(489,144,678,388,'','y','Имя'),(490,144,679,389,'','y','Email'),(491,144,681,391,'','y','Дата'),(492,145,682,391,'','y','Email'),(493,145,683,392,'','y','Дата'),(494,146,684,990,'','y','Email'),(1470,146,685,392,'','y','ФИО'),(1382,224,1444,929,'','y','Порядок отображения'),(1384,224,1445,928,'','y','Статус'),(832,172,857,624,'','y','Наименование'),(833,172,858,625,'','y','Псевдоним'),(834,173,860,626,'','y','Действие'),(1383,224,1443,926,'','y','Поле прикрепленной к разделу сущности'),(1053,189,1102,772,'','y','Пользователь'),(1052,189,1101,771,'','y','Дата последней активности'),(1051,189,1100,770,'','y','Id сессии'),(1039,172,1074,759,'','y','Выполнять maintenance()'),(979,173,1027,728,'','y','Тип'),(1066,191,1194,782,'','y','Компонент'),(1067,191,1195,783,'','y','Очередность'),(1068,191,1196,784,'','y','Префикс'),(2312,224,2183,2312,'','y','Статическая фильтрация'),(2311,224,2182,2311,'','y','Значение по умолчанию'),(2310,381,2181,1973,'','y','Порядок отображения'),(2309,381,2180,1967,'','y','Постфикс'),(2308,381,2179,1966,'','y','Префикс'),(2305,381,2176,1971,'','y','Источник'),(2307,381,2178,1970,'Динамический','y','Свойство'),(2303,381,2174,1968,'Статический','y','Компонент'),(2304,381,2175,1972,'','y','Шагов вверх'),(2302,381,2173,1965,'','y','Тип компонента'),(2300,380,2181,1964,'','y','Порядок отображения'),(2299,380,2180,1958,'','y','Постфикс'),(2298,380,2179,1957,'','y','Префикс'),(2295,380,2176,1962,'','y','Источник'),(2297,380,2178,1961,'Динамический','y','Свойство'),(2293,380,2174,1959,'Статический','y','Компонент'),(2294,380,2175,1963,'','y','Шагов вверх'),(2292,380,2173,1956,'','y','Тип компонента'),(2288,379,2179,1948,'','y','Префикс'),(2289,379,2180,1949,'','y','Постфикс'),(2290,379,2181,1955,'','y','Порядок отображения'),(2287,379,2178,1951,'Динамический','y','Свойство'),(2285,379,2176,1952,'','y','Источник'),(1231,201,1342,851,'','y','Поле, которое должно быть отключено'),(1439,232,1515,965,'','y','Тип'),(2282,379,2173,1947,'','y','Тип компонента'),(1421,232,1485,962,'','y','Наименование'),(1422,232,1486,963,'','y','Псевдоним'),(1423,232,1488,1132,'','y','Статус'),(1449,113,1533,989,'','y','Статус'),(1448,201,1532,988,'','y','Значение по умолчанию'),(1515,113,585,1036,'','y','Порядок отображения соответствующего пункта в меню'),(1656,11,1886,30,'','y','Изменить название столбца на'),(1767,146,691,1222,'','y','Дата регистрации'),(1954,224,1658,1945,'','y','Альтернативное наименование'),(2280,201,2161,1946,'','y','Отображать в форме');

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

insert  into `menu`(`id`,`menuId`,`title`,`linked`,`staticpageId`,`url`,`toggle`,`bottom`,`move`) values (1,0,'Курсы','n',0,'/courses','y','y',1),(5,0,'Сотрудничество','y',9,'','y','y',5);

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
) ENGINE=MyISAM AUTO_INCREMENT=156 DEFAULT CHARSET=utf8;

/*Data for the table `param` */

insert  into `param`(`id`,`fieldId`,`possibleParamId`,`value`,`title`) values (90,1487,13,'/css/style.css','Путь к css-нику для подцепки редактором'),(38,980,4,'','Единица измерения'),(146,2131,12,'x-panel-body-default x-body','Css класс для body'),(145,2131,5,'true','Во всю ширину'),(134,1814,5,'true','Во всю ширину'),(102,1487,5,'true','Во всю ширину'),(148,2131,13,'[\"/library/extjs4/resources/css/ext-all.css\", \"/css/admin/layout.css\"]','Путь к css-нику для подцепки редактором'),(149,2131,14,'.x-panel-body-default{border: 0 !important; padding: 10px;}','Стили'),(127,109,4,'px','Единица измерения'),(128,110,4,'px','Единица измерения');

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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

/*Data for the table `possibleelementparam` */

insert  into `possibleelementparam`(`id`,`elementId`,`title`,`alias`,`defaultValue`) values (3,18,'Максимальная длина в символах','maxlength','5'),(4,18,'Единица измерения','measure',''),(5,13,'Во всю ширину','wide','0'),(6,7,'Количество столбцов','cols','1'),(7,13,'Высота в пикселях','height','200'),(28,19,'Отображаемый формат времени','displayTimeFormat','H:i'),(27,19,'Отображаемый формат даты','displayDateFormat','Y-m-d'),(26,12,'Отображаемый формат','displayFormat','Y-m-d'),(11,13,'Ширина в пикселях','width',''),(12,13,'Css класс для body','bodyClass',''),(13,13,'Путь к css-нику для подцепки редактором','contentsCss',''),(14,13,'Стили','style',''),(15,14,'Включать наименование поля в имя файла при download-е','appendFieldTitle','true'),(16,14,'Включать наименование сущности в имя файла при download-е','prependEntityTitle','true'),(17,13,'Путь к js-нику для подцепки редактором','contentsJs',''),(18,13,'Скрипт','script',''),(19,13,'Скрипт обработки исходного кода','sourceStripper',''),(20,18,'Только для чтения','readonly',''),(21,23,'Группировка опций по столбцу','groupBy',''),(22,23,'Шаблон содержимого опции','optionTemplate',''),(23,23,'Высота опции','optionHeight','14'),(24,23,'Дополнительно передавать параметры (в виде атрибутов)','optionAttrs',''),(25,23,'Отключить лукап','noLookup','false'),(29,23,'Плейсхолдер','placeholder',''),(30,1,'Только для чтения','readonly',''),(31,1,'Максимальная длина в символах','maxlength','');

/*Table structure for table `profile` */

DROP TABLE IF EXISTS `profile`;

CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `home` text NOT NULL,
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  KEY `entityId` (`entityId`),
  FULLTEXT KEY `homepage` (`home`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `profile` */

insert  into `profile`(`id`,`title`,`toggle`,`entityId`,`home`,`move`) values (1,'Конфигуратор','y',0,'',1),(12,'Администратор','y',0,'',12);

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
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

/*Data for the table `search` */

insert  into `search`(`id`,`sectionId`,`fieldId`,`move`,`toggle`,`alt`,`title`,`defaultValue`,`filter`,`ignoreTemplate`) values (13,5,612,13,'y','','Тип','','',1),(54,146,691,55,'y','','Дата регистрации','','',1),(55,146,689,56,'y','','Пол','','',1);

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
  `javascript` text NOT NULL,
  `javascriptForm` text NOT NULL,
  `extends` varchar(255) NOT NULL DEFAULT 'Indi_Controller_Admin',
  `defaultSortField` int(11) NOT NULL DEFAULT '0',
  `defaultSortDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `disableAdd` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('s','p','o') NOT NULL DEFAULT 'p',
  `parentSectionConnector` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `entityId` (`entityId`),
  KEY `toggle` (`toggle`),
  KEY `defaultSortField` (`defaultSortField`),
  KEY `defaultSortDirection` (`defaultSortDirection`),
  KEY `type` (`type`),
  KEY `parentSectionConnector` (`parentSectionConnector`),
  FULLTEXT KEY `javascript` (`javascript`),
  FULLTEXT KEY `javascriptForm` (`javascriptForm`)
) ENGINE=MyISAM AUTO_INCREMENT=382 DEFAULT CHARSET=utf8;

/*Data for the table `section` */

insert  into `section`(`id`,`sectionId`,`entityId`,`title`,`alias`,`toggle`,`move`,`rowsOnPage`,`javascript`,`javascriptForm`,`extends`,`defaultSortField`,`defaultSortDirection`,`filter`,`disableAdd`,`type`,`parentSectionConnector`) values (1,0,0,'Конфигурация','','y',367,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(2,1,1,'Столбцы','columnTypes','y',4,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(3,4,0,'Выход','logout','y',319,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(4,0,0,'Бэкенд','','y',372,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(5,1,2,'Сущности','entities','y',2,25,'','','Indi_Controller_Admin',4,'ASC','',0,'s',0),(6,5,5,'Поля в структуре','fields','y',7,25,'','','Indi_Controller_Admin',14,'ASC','',0,'s',0),(7,4,3,'Разделы','sections','y',5,25,'','','Indi_Controller_Admin',23,'ASC','',0,'s',0),(8,7,8,'Действия','sectionActions','y',8,25,'','','Indi_Controller_Admin',30,'ASC','',0,'s',0),(10,4,7,'Действия','actions','y',9,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(11,7,9,'Столбцы грида','grid','y',10,25,'','','Indi_Controller_Admin',35,'ASC','',0,'s',0),(12,6,6,'Возможные значения','enumset','y',11,25,'','','Indi_Controller_Admin',377,'ASC','',0,'s',0),(13,4,10,'Профили','profiles','y',6,25,'','','Indi_Controller_Admin',2132,'ASC','',0,'s',0),(14,13,11,'Пользователи','admins','y',13,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(16,1,4,'Элементы управления','controlElements','y',14,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(22,6,20,'Копии изображения','resize','y',19,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(29,0,0,'Контент','','y',113,25,'','','Indi_Controller_Admin',0,'ASC','',0,'o',0),(30,378,25,'Страницы','staticpages','y',232,25,'','','Indi_Controller_Admin',131,'ASC','',0,'o',0),(100,16,90,'Возможные параметры настройки','possibleParams','y',90,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(101,6,91,'Параметры','params','y',91,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(112,0,0,'Фронтенд','','y',371,25,'','','Indi_Controller_Admin',0,'ASC','',0,'s',0),(113,112,101,'Разделы','fsections','y',104,25,'','','Indi_Controller_Admin',585,'ASC','<?=$_SESSION[\'admin\'][\'profileId\']==1?\'1\':\'`toggle`=\"y\"\'?>',0,'s',0),(143,0,0,'Обратная связь','','y',358,25,'','','Indi_Controller_Admin',0,'ASC','',0,'o',0),(144,143,128,'Фидбэк','feedback','n',135,25,'','','Indi_Controller_Admin',681,'DESC','',0,'o',0),(145,143,129,'Подписчики','subscribers','n',165,25,'','','Indi_Controller_Admin',682,'ASC','',0,'o',0),(146,143,130,'Пользователи','users','y',225,25,'','','Indi_Controller_Admin',685,'DESC','',0,'o',0),(172,112,146,'Действия','factions','y',185,25,'','','Indi_Controller_Admin',857,'ASC','',0,'s',0),(173,113,147,'Действия','fsection2factions','y',161,25,'','','Indi_Controller_Admin',860,'ASC','',0,'s',0),(189,143,160,'Посетители','visitors','n',176,25,'','','Indi_Controller_Admin',1101,'DESC','',0,'o',0),(191,173,162,'Компоненты SEO-урла','seoUrl','y',178,25,'','','Indi_Controller_Admin',1195,'ASC','',0,'s',0),(381,173,301,'Компоненты meta description','metadescription','y',381,25,'','hide(\'tr-entityId\');','Indi_Controller_Admin_Meta',2181,'ASC','`tag`= \"description\"',0,'o',0),(380,173,301,'Компоненты meta keywords','metakeywords','y',380,25,'','hide(\'tr-entityId\');','Indi_Controller_Admin_Meta',2181,'ASC','`tag`= \"keywords\"',0,'o',0),(201,7,171,'Отключенные поля','disabledFields','y',188,25,'','','Indi_Controller_Admin',1342,'ASC','',0,'s',0),(224,7,195,'Фильтры','search','y',192,25,'','','Indi_Controller_Admin',1444,'ASC','',0,'s',0),(229,29,205,'Меню','menu','n',134,25,'','','Indi_Controller_Admin',1496,'ASC','',0,'p',0),(232,378,204,'Элементы','staticblocks','y',316,25,'','','Indi_Controller_Admin',1485,'ASC','',0,'o',0),(379,173,301,'Компоненты title','metatitles','y',379,25,'','hide(\'tr-entityId\');','Indi_Controller_Admin_Meta',2181,'ASC','`tag`= \"title\"',0,'o',0),(378,0,0,'Статика','','y',144,30,'','','Indi_Controller_Admin',0,'ASC','',0,'p',0);

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
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `sectionId_2` (`sectionId`),
  KEY `actionId` (`actionId`),
  KEY `profileIds` (`profileIds`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM AUTO_INCREMENT=1541 DEFAULT CHARSET=utf8;

/*Data for the table `section2action` */

insert  into `section2action`(`id`,`sectionId`,`actionId`,`toggle`,`move`,`profileIds`,`title`) values (1,2,1,'y',1,'1','Список'),(2,2,2,'y',2,'1','Детали'),(3,2,3,'y',3,'1','Сохранить'),(4,3,1,'y',4,'1','Список'),(5,2,4,'n',5,'1','Удалить'),(6,5,1,'y',6,'1','Список'),(7,5,2,'y',7,'1','Детали'),(8,5,3,'y',8,'1','Сохранить'),(9,5,4,'y',9,'1','Удалить'),(10,6,1,'y',10,'1','Список'),(11,6,2,'y',11,'1','Детали'),(12,6,3,'y',12,'1','Сохранить'),(13,6,4,'y',13,'1','Удалить'),(14,7,1,'y',14,'1','Список'),(15,7,2,'y',15,'1','Детали'),(16,7,3,'y',16,'1','Сохранить'),(17,7,4,'y',17,'1','Удалить'),(18,6,5,'y',18,'1','Выше'),(19,6,6,'y',19,'1','Ниже'),(20,8,1,'y',20,'1','Список'),(21,8,2,'y',21,'1','Детали'),(22,8,3,'y',22,'1','Сохранить'),(23,8,4,'y',23,'1','Удалить'),(24,8,5,'y',24,'1','Выше'),(25,8,6,'y',25,'1','Ниже'),(26,7,5,'y',26,'1','Выше'),(27,7,6,'y',27,'1','Ниже'),(28,10,1,'y',28,'1','Список'),(29,10,2,'y',29,'1','Детали'),(30,10,3,'y',30,'1','Сохранить'),(32,11,1,'y',31,'1','Список'),(33,11,2,'y',32,'1','Детали'),(34,11,3,'y',33,'1','Сохранить'),(35,11,4,'y',34,'1','Удалить'),(36,11,5,'y',35,'1','Выше'),(37,11,6,'y',36,'1','Ниже'),(38,13,1,'y',37,'1','Список'),(39,13,2,'y',38,'1','Детали'),(40,13,3,'y',39,'1','Сохранить'),(41,12,1,'y',40,'1','Список'),(42,12,2,'y',41,'1','Детали'),(43,12,3,'y',42,'1','Сохранить'),(44,12,4,'y',43,'1','Удалить'),(45,14,1,'y',44,'1','Список'),(46,14,2,'y',45,'1','Детали'),(47,14,3,'y',46,'1','Сохранить'),(48,14,4,'y',47,'1','Удалить'),(52,16,1,'y',51,'1','Список'),(53,16,2,'y',52,'1','Детали'),(54,16,3,'y',53,'1','Сохранить'),(67,7,7,'y',58,'1','Статус'),(68,10,7,'y',59,'1','Статус'),(69,11,7,'y',60,'1','Статус'),(74,22,1,'y',65,'1','Список'),(75,22,2,'y',66,'1','Детали'),(76,22,3,'y',67,'1','Сохранить'),(77,22,4,'y',68,'1','Удалить'),(99,30,1,'y',69,'12,1','Список'),(100,30,2,'y',70,'12,1','Детали'),(101,30,3,'y',71,'12,1','Сохранить'),(102,30,4,'y',72,'12,1','Удалить'),(103,30,7,'y',73,'12,1','Статус'),(329,12,5,'y',299,'1','Выше'),(330,12,6,'y',300,'1','Ниже'),(373,100,1,'y',343,'1','Список'),(374,100,2,'y',344,'1','Детали'),(375,100,3,'y',345,'1','Сохранить'),(376,100,4,'y',346,'1','Удалить'),(377,101,1,'y',347,'1','Список'),(378,101,2,'y',348,'1','Детали'),(379,101,3,'y',349,'1','Сохранить'),(380,101,4,'y',350,'1','Удалить'),(808,13,4,'y',589,'1','Удалить'),(806,10,4,'n',587,'1','Удалить'),(429,113,1,'y',399,'1,2','Список'),(430,113,2,'y',400,'1','Детали'),(431,113,3,'y',401,'1','Сохранить'),(432,113,4,'y',402,'1','Удалить'),(874,5,18,'y',607,'1','Обновить кэш'),(532,144,1,'y',133,'1,2,4','Список'),(533,144,2,'y',134,'1,2,4','Детали'),(534,144,3,'y',135,'1,2','Сохранить'),(535,144,4,'y',136,'1,2','Удалить'),(536,145,1,'y',134,'1,2,4','Список'),(537,145,2,'y',135,'1,2,4','Детали'),(538,145,3,'y',136,'1,2','Сохранить'),(539,145,4,'y',137,'1,2','Удалить'),(540,146,1,'y',135,'12,1','Список'),(541,146,2,'y',136,'12,1','Детали'),(542,146,3,'y',137,'12,1','Сохранить'),(543,146,4,'y',138,'12,1','Удалить'),(833,16,4,'n',598,'1','Удалить'),(875,224,1,'y',608,'1','Список'),(876,224,2,'y',609,'1','Детали'),(877,224,4,'y',611,'1','Удалить'),(878,224,3,'y',610,'1','Сохранить'),(879,224,5,'y',612,'1','Выше'),(880,224,6,'y',613,'1','Ниже'),(881,224,7,'y',614,'1','Статус'),(642,172,1,'y',161,'1','Список'),(643,172,2,'y',162,'1,2,4','Детали'),(644,172,3,'y',163,'1,2','Сохранить'),(645,172,4,'y',164,'1,2','Удалить'),(646,173,1,'y',162,'1,2','Список'),(647,173,2,'y',163,'1','Детали'),(648,173,3,'y',164,'1','Сохранить'),(649,173,4,'y',165,'1','Удалить'),(730,189,1,'y',524,'1','Список'),(731,189,2,'y',525,'1','Детали'),(732,189,3,'y',526,'1','Сохранить'),(733,189,4,'y',527,'1','Удалить'),(740,191,1,'y',534,'1','Список'),(741,191,2,'y',535,'1','Детали'),(742,191,3,'y',536,'1','Сохранить'),(743,191,4,'y',537,'1','Удалить'),(744,191,5,'y',538,'1','Выше'),(745,191,6,'y',539,'1','Ниже'),(1537,381,3,'y',1537,'1,12','Сохранить'),(1536,381,2,'y',1536,'1,12','Детали'),(1535,381,1,'y',1535,'1,12','Список'),(1534,380,6,'y',1534,'1,12','Ниже'),(1533,380,5,'y',1533,'1,12','Выше'),(1531,380,3,'y',1531,'1,12','Сохранить'),(1530,380,2,'y',1530,'1,12','Детали'),(1529,380,1,'y',1529,'1,12','Список'),(1528,379,6,'y',1528,'1,12','Ниже'),(789,201,1,'y',583,'1','Список'),(790,201,2,'y',584,'1','Детали'),(791,201,3,'y',585,'1','Сохранить'),(792,201,4,'y',586,'1','Удалить'),(898,229,1,'y',631,'1,2,9','Список'),(899,229,2,'y',632,'1,2,9','Детали'),(900,229,3,'y',633,'1,2,9','Сохранить'),(901,229,4,'y',634,'1,2,9','Удалить'),(910,232,1,'y',643,'12,1','Список'),(911,232,2,'y',644,'12,1','Детали'),(912,232,3,'y',645,'12,1','Сохранить'),(913,232,4,'y',646,'1','Удалить'),(921,229,5,'y',654,'1,2,9','Выше'),(922,229,6,'y',655,'1,2,9','Ниже'),(933,229,7,'y',666,'1,2,9','Статус'),(1527,379,5,'y',1527,'1,12','Выше'),(939,232,7,'y',672,'12,1','Статус'),(946,113,7,'y',679,'1','Статус'),(947,113,5,'y',680,'1,2','Выше'),(948,113,6,'y',681,'1,2','Ниже'),(949,113,19,'y',682,'1,2','Обновить sitemap.xml'),(1268,8,7,'y',1268,'1','Статус'),(1295,13,5,'y',1291,'1','Выше'),(1296,13,6,'y',1292,'1','Ниже'),(1522,14,7,'y',1293,'1','Статус'),(1540,381,6,'y',1540,'1,12','Ниже'),(1539,381,5,'y',1539,'1,12','Выше'),(1538,381,4,'y',1538,'1,12','Удалить'),(1532,380,4,'y',1532,'1,12','Удалить'),(1526,379,4,'y',1526,'1,12','Удалить'),(1525,379,3,'y',1525,'1,12','Сохранить'),(1524,379,2,'y',1524,'1,12','Детали'),(1523,379,1,'y',1523,'1,12','Список');

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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

/*Data for the table `staticblock` */

/*Table structure for table `staticpage` */

DROP TABLE IF EXISTS `staticpage`;

CREATE TABLE `staticpage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  FULLTEXT KEY `details` (`details`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `staticpage` */

insert  into `staticpage`(`id`,`title`,`alias`,`toggle`,`details`) values (9,'Страница не найдена','404','y','<h1>404</h1>\r\n');

/*Table structure for table `subscriber` */

DROP TABLE IF EXISTS `subscriber`;

CREATE TABLE `subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

/*Data for the table `subscriber` */

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
  PRIMARY KEY (`id`),
  KEY `gender` (`gender`),
  KEY `sn` (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;

/*Data for the table `user` */

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

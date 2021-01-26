-- MySQL dump 10.13  Distrib 5.6.9-rc, for Win64 (x86_64)
--
-- Host: localhost    Database: empty
-- ------------------------------------------------------
-- Server version	5.6.9-rc-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `action`
--

DROP TABLE IF EXISTS `action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
INSERT INTO `action` VALUES (1,'Список','index','n','s',0,'y'),(2,'Детали','form','y','s',1,'y'),(3,'Сохранить','save','y','s',0,'y'),(4,'Удалить','delete','y','s',1,'y'),(5,'Выше','up','y','s',1,'y'),(6,'Ниже','down','y','s',1,'y'),(7,'Статус','toggle','y','s',1,'y'),(18,'Обновить кэш','cache','y','s',1,'y'),(19,'Обновить sitemap.xml','sitemap','n','s',1,'y'),(20,'Авторизация','login','y','s',1,'y'),(33,'Автор','author','y','s',1,'y'),(34,'PHP','php','y','s',1,'y'),(35,'JS','js','y','s',1,'y'),(36,'Экспорт','export','y','s',1,'y'),(37,'Перейти','goto','y','s',1,'y'),(38,'','rwu','n','s',0,'y'),(39,'Активировать','activate','y','s',1,'y'),(40,'Доступные языки','dict','n','s',1,'y'),(41,'Запустить','run','y','s',1,'y'),(42,'График','chart','y','s',1,'y'),(43,'Вординги','wordings','y','s',1,'y'),(44,'Перезапустить','restart','n','s',1,'y');
/*!40000 ALTER TABLE `action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profileId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `demo` enum('n','y') NOT NULL DEFAULT 'n',
  `uiedit` enum('n','y') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `profileId` (`profileId`),
  KEY `toggle` (`toggle`),
  KEY `demo` (`demo`),
  KEY `uiedit` (`uiedit`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,1,'Павел Перминов','pavel.perminov.23@gmail.com','*8E1219CD047401C6FEAC700B47F5DA846A57ABD4','y','n','y'),(14,12,'Василий Теркин','vasily.terkin@gmail.com','*85012D571AE8732730DE98314CF04A3BB2269508','n','n','n');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alteredfield`
--

DROP TABLE IF EXISTS `alteredfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `elementId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `impact` (`impact`),
  KEY `profileIds` (`profileIds`),
  KEY `mode` (`mode`),
  KEY `elementId` (`elementId`)
) ENGINE=MyISAM AUTO_INCREMENT=219 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alteredfield`
--

LOCK TABLES `alteredfield` WRITE;
/*!40000 ALTER TABLE `alteredfield` DISABLE KEYS */;
INSERT INTO `alteredfield` VALUES (215,232,1515,'','readonly','Тип','except','1','',0),(216,394,2377,'','inherit','Результат','all','','',1);
/*!40000 ALTER TABLE `alteredfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `changelog`
--

DROP TABLE IF EXISTS `changelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `changelog`
--

LOCK TABLES `changelog` WRITE;
/*!40000 ALTER TABLE `changelog` DISABLE KEYS */;
/*!40000 ALTER TABLE `changelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `columntype`
--

DROP TABLE IF EXISTS `columntype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `columntype`
--

LOCK TABLES `columntype` WRITE;
/*!40000 ALTER TABLE `columntype` DISABLE KEYS */;
INSERT INTO `columntype` VALUES (1,'Строка','VARCHAR(255)','y','22,23,1,7'),(3,'Число','INT(11)','y','4,5,23,1,18'),(4,'Текст','TEXT','y','6,7,13,1'),(5,'Цена','DECIMAL(11,2)','n','24'),(6,'Дата','DATE','n','12'),(7,'Год','YEAR','n',''),(8,'Время','TIME','n','17'),(9,'Момент','DATETIME','n','19'),(10,'Одно значение из набора','ENUM','n','5,23'),(11,'Набор значений','SET','n','23,1,6,7'),(12,'Правда/Ложь','BOOLEAN','n','9'),(13,'Цвет','VARCHAR(10)','n','11');
/*!40000 ALTER TABLE `columntype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consider`
--

DROP TABLE IF EXISTS `consider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `consider` int(11) NOT NULL DEFAULT '0',
  `foreign` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `required` enum('n','y') NOT NULL DEFAULT 'n',
  `connector` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `fieldId` (`fieldId`),
  KEY `consider` (`consider`),
  KEY `foreign` (`foreign`),
  KEY `required` (`required`),
  KEY `connector` (`connector`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consider`
--

LOCK TABLES `consider` WRITE;
/*!40000 ALTER TABLE `consider` DISABLE KEYS */;
INSERT INTO `consider` VALUES (1,308,2307,2246,12,'Поле','y',0),(2,5,9,10,0,'Элемент управления','y',0),(3,5,10,470,0,'Предназначено для хранения ключей','y',0),(4,9,34,33,19,'Раздел','y',0),(23,9,2313,34,12,'Поле','y',6),(24,195,2314,1443,12,'Поле прикрепленной к разделу сущности','y',6),(6,91,477,476,10,'В контексте какого поля','y',0),(7,3,503,19,0,'Сущность','y',0),(8,195,1443,1442,19,'Раздел','y',0),(9,101,1010,563,0,'Прикрепленная сущность','y',0),(10,162,1193,1192,0,'Раздел фронтенда','y',0),(11,301,2178,2177,0,'Сущность','y',0),(12,171,1342,1341,19,'Раздел','y',0),(13,301,2171,2170,0,'Раздел','y',0),(14,3,1554,19,0,'Сущность','y',0),(15,101,1560,868,563,'Вышестоящий раздел','y',0),(16,3,2211,19,0,'Сущность','y',0),(17,309,2262,2255,0,'Сущность','y',0),(18,308,2247,2246,6,'Поле','y',0),(19,308,2248,2247,12,'От какого поля зависит','y',6),(20,313,2292,2291,0,'Сущность','y',0),(21,313,2293,2291,0,'Сущность','y',0),(22,313,2299,2298,0,'Тип автора','y',0),(25,3,2322,19,0,'Сущность','y',0),(26,3,2323,2322,0,'Плитка','y',106),(27,9,1886,2165,0,'Auto title','y',0),(28,195,1658,2167,0,'Auto title','y',0),(29,8,2209,2164,0,'Auto title','y',0),(30,171,2252,2169,0,'Auto title','y',0),(31,9,2200,34,2199,'Поле','y',0),(32,314,2382,2342,0,'Этап','y',0),(33,314,2382,2343,0,'Статус','y',0),(34,8,2164,27,31,'Действие','y',0),(35,9,2165,34,7,'Поле','y',0),(36,91,2166,477,472,'Параметр настройки','y',0),(37,195,2167,1443,7,'Поле прикрепленной к разделу сущности','y',0),(38,147,2168,860,857,'Действие','y',0),(39,171,2169,1342,7,'Поле','y',0),(40,310,2280,2275,36,'Роль','y',0),(41,308,2249,2247,7,'От какого поля зависит','y',0),(42,318,2399,2398,1337,'Роль','y',0),(43,318,2407,2405,19,'Раздел','y',0),(44,318,2408,2406,0,'Сущность','y',0);
/*!40000 ALTER TABLE `consider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element`
--

DROP TABLE IF EXISTS `element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element`
--

LOCK TABLES `element` WRITE;
/*!40000 ALTER TABLE `element` DISABLE KEYS */;
INSERT INTO `element` VALUES (1,'Строка','string','none,many',0),(4,'Порядок','move','none',1),(5,'Радио-кнопки','radio','one',0),(6,'Текст','textarea','none,many',0),(7,'Чекбоксы','multicheck','many',0),(9,'Чекбокс','check','none',0),(11,'Цвет','color','none',0),(12,'Календарь','calendar','none',0),(13,'HTML-редактор','html','none',0),(14,'Файл','upload','none',0),(16,'Группа полей','span','none',0),(17,'Время','time','none',0),(18,'Число','number','none,one',0),(19,'Момент','datetime','none',0),(22,'Скрытое поле','hidden','none',0),(23,'Список','combo','one,many',0),(24,'Цена','price','none',0);
/*!40000 ALTER TABLE `element` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity`
--

DROP TABLE IF EXISTS `entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `filesGroupBy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `system` (`system`),
  KEY `titleFieldId` (`titleFieldId`),
  KEY `spaceScheme` (`spaceScheme`),
  KEY `spaceFields` (`spaceFields`),
  KEY `filesGroupBy` (`filesGroupBy`)
) ENGINE=MyISAM AUTO_INCREMENT=321 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity`
--

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` VALUES (1,'Тип столбца','columnType','Indi_Db_Table','y',0,2,'none','',0),(2,'Сущность','entity','Indi_Db_Table','y',1,4,'none','',0),(3,'Раздел','section','Indi_Db_Table','y',0,20,'none','',0),(4,'Элемент управления','element','Indi_Db_Table','y',0,64,'none','',0),(5,'Поле','field','Indi_Db_Table','y',1,7,'none','',0),(6,'Значение из набора','enumset','Indi_Db_Table','y',0,16,'none','',0),(7,'Действие','action','Indi_Db_Table','y',0,31,'none','',0),(8,'Действие в разделе','section2action','Indi_Db_Table','y',0,27,'none','',0),(9,'Столбец грида','grid','Indi_Db_Table','y',0,34,'none','',0),(10,'Роль','profile','Indi_Db_Table','y',0,36,'none','',0),(11,'Администратор','admin','Indi_Db_Table','y',0,39,'none','',0),(20,'Копия','resize','Indi_Db_Table','y',0,107,'none','',0),(25,'Статическая страница','staticpage','Indi_Db_Table','o',0,131,'none','',0),(90,'Возможный параметр','possibleElementParam','Indi_Db_Table','y',0,472,'none','',0),(91,'Параметр','param','Indi_Db_Table','y',0,477,'none','',0),(101,'Раздел фронтенда','fsection','Indi_Db_Table','o',1,559,'none','',0),(195,'Фильтр','search','Indi_Db_Table','y',0,1443,'none','',0),(128,'Фидбэк','feedback','Indi_Db_Table','o',0,678,'none','',0),(320,'Товар','prod','Indi_Db_Table','n',0,2416,'none','',0),(146,'Действие, возможное для использования в разделе фронтенда','faction','Indi_Db_Table','o',1,857,'none','',0),(147,'Действие в разделе фронтенда','fsection2faction','Indi_Db_Table','o',1,860,'none','',0),(307,'Язык','lang','Indi_Db_Table','y',0,2236,'none','',0),(162,'Компонент SEO-урла','url','Indi_Db_Table','o',0,0,'none','',0),(171,'Поле, измененное в рамках раздела','alteredField','Indi_Db_Table','y',0,1342,'none','',0),(204,'Статический элемент','staticblock','Indi_Db_Table','o',0,1485,'none','',0),(205,'Пункт меню','menu','Indi_Db_Table','o',0,1490,'none','',0),(301,'Компонент содержимого meta-тега','metatag','Indi_Db_Table','o',0,0,'none','',0),(309,'Уведомление','notice','Indi_Db_Table','y',0,2254,'none','',0),(310,'Получатель уведомлений','noticeGetter','Indi_Db_Table','y',0,2275,'none','',0),(308,'Зависимость','consider','Indi_Db_Table','y',0,2247,'none','',0),(311,'Год','year','Indi_Db_Table','y',0,2286,'none','',0),(312,'Месяц','month','Indi_Db_Table','y',0,2289,'none','',0),(313,'Корректировка','changeLog','Indi_Db_Table','y',0,2296,'none','',0),(314,'Очередь задач','queueTask','Indi_Db_Table','y',0,2336,'none','',0),(315,'Сегмент очереди','queueChunk','Indi_Db_Table','y',0,2359,'none','',0),(316,'Элемент очереди','queueItem','Indi_Db_Table','y',0,0,'none','',0),(318,'Рилтайм','realtime','Indi_Db_Table','y',0,2409,'none','',0);
/*!40000 ALTER TABLE `entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enumset`
--

DROP TABLE IF EXISTS `enumset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enumset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=1182 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enumset`
--

LOCK TABLES `enumset` WRITE;
/*!40000 ALTER TABLE `enumset` DISABLE KEYS */;
INSERT INTO `enumset` VALUES (1,3,'Нет','n',1),(2,3,'Да','y',2),(5,22,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',5),(6,22,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',6),(9,29,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',9),(10,29,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',10),(11,37,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',11),(12,37,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключена','n',12),(13,42,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',13),(14,42,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',14),(62,66,'Нет','none',62),(63,66,'Только с одним значением ключа','one',63),(64,66,'С набором значений ключей','many',64),(87,111,'Поменять, но с сохранением пропорций','p',87),(88,111,'Поменять','c',88),(89,111,'Не менять','o',89),(91,114,'Ширины','width',91),(92,114,'Высоты','height',92),(95,137,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',95),(96,137,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключена','n',96),(112,345,'Да','y',112),(113,345,'Нет','n',113),(122,0,'Всем друзьям, кроме указанных в разделе \"Исключения из правил доступа на просмотр блога\"','ae',122),(181,470,'<span class=\"i-color-box\" style=\"background: white;\"></span>Нет','none',164),(183,470,'<span class=\"i-color-box\" style=\"background: url(/i/admin/btn-icon-multikey.png);\"></span>Да, несколько ключей','many',296),(184,470,'<span class=\"i-color-box\" style=\"background: url(/i/admin/btn-icon-login.png);\"></span>Да, но только один ключ','one',295),(1076,2159,'<span class=\"i-color-box\" style=\"background: lightgray; border: 1px solid blue;\"></span>Скрыт, но показан в развороте','e',1076),(213,557,'<span class=\"i-color-box\" style=\"background: url(resources/images/grid/sort_asc.png) -5px -1px;\"></span>По возрастанию','ASC',297),(214,557,'<span class=\"i-color-box\" style=\"background: url(resources/images/grid/sort_desc.png) -5px -1px;\"></span>По убыванию','DESC',182),(219,594,'Да','y',299),(220,594,'Нет','n',300),(227,612,'Проектная','n',301),(228,612,'<span style=\'color: red\'>Системная</span>','y',186),(572,1365,'<font color=lime>Типовое</font>','o',461),(571,1365,'<font color=red>Системное</font>','s',460),(570,1365,'Проектное','p',0),(580,1445,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(581,1445,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',464),(979,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',979),(980,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',980),(981,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',981),(982,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',982),(328,0,'Очень плохо','1',254),(480,1040,'Одностроковый','s',408),(478,1027,'Для jQuery.post()','j',407),(479,1040,'Обычный','r',0),(477,1027,'Обычное','r',0),(574,1366,'<font color=red>Системная</font>','s',462),(573,1366,'Проектная','p',0),(575,1366,'<font color=lime>Публичная</font>','o',463),(458,1009,'SQL-выражению','e',396),(457,1009,'Одному из имеющихся столбцов','c',0),(459,1011,'По возрастанию','ASC',0),(460,1011,'По убыванию','DESC',0),(484,1074,'Над записью','r',0),(485,1074,'Над набором записей','rs',411),(486,1074,'Только независимые множества, если нужно','n',412),(567,1364,'Проектное','p',0),(568,1364,'<font color=red>Системное</font>','s',458),(569,1364,'<font color=lime>Типовое</font>','o',459),(969,2176,'Запись','row',969),(968,2176,'Действие','action',968),(967,2176,'Раздел','section',967),(566,612,'<font color=lime>Публичная</font>','o',457),(582,1488,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(583,1488,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',465),(584,1491,'Нет','n',0),(585,1491,'Да','y',466),(586,1494,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(587,1494,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',467),(594,1515,'HTML','html',0),(595,1515,'Строка','string',471),(596,1515,'Текст','textarea',472),(597,1495,'Да','y',0),(598,1495,'Нет','n',473),(608,1533,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',478),(607,1533,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(962,2172,'Title','title',962),(963,2172,'Keywords','keywords',963),(964,2172,'Description','description',964),(965,2173,'Статический','static',965),(966,2173,'Динамический','dynamic',966),(960,2159,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(961,2159,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',489),(983,2202,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',983),(984,2202,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',984),(985,2203,'Всем','all',985),(986,2203,'Никому, кроме','only',986),(987,2203,'Всем, кроме','except',987),(988,2205,'Всем','all',988),(989,2205,'Никому, кроме','only',989),(990,2205,'Всем, кроме','except',990),(991,2207,'Все','all',991),(992,2207,'Никто, кроме','only',992),(993,2207,'Все, кроме','except',993),(994,2210,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','0',994),(995,2210,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','1',995),(996,2212,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Авто','auto',996),(997,2212,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Отображать','yes',997),(998,2212,'<span class=\"i-color-box\" style=\"background: red;\"></span>Не отображать','no',998),(999,2213,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/inherit.png);\"></span>Авто','auto',999),(1000,2213,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Отдельным запросом','yes',1000),(1001,2213,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>В том же запросе','no',1001),(1002,2214,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','auto',1002),(1003,2214,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1003),(1036,2267,'Увеличение','inc',1036),(1037,2267,'Уменьшение','dec',1037),(1032,2258,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',1032),(1033,2258,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',1033),(1034,2261,'Одинаковое для всех получателей','event',1034),(1035,2261,'Неодинаковое, зависит от получателя','getter',1035),(1010,2238,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1010),(1011,2238,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1011),(1012,2239,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключена','n',1012),(1013,2239,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включена','y',1088),(1014,2241,'Существующей','existing',1014),(1015,2241,'Новой','new',1015),(1016,2241,'Любой','any',1016),(1017,2243,'Нет','none',1017),(1018,2243,'DATE','date',1018),(1019,2243,'DATETIME','datetime',1019),(1020,2243,'DATE, TIME','date-time',1020),(1021,2243,'DATE, timeId','date-timeId',1021),(1022,2243,'DATE, dayQty','date-dayQty',1022),(1023,2243,'DATETIME, minuteQty','datetime-minuteQty',1023),(1024,2243,'DATE, TIME, minuteQty','date-time-minuteQty',1024),(1025,2243,'DATE, timeId, minuteQty','date-timeId-minuteQty',1025),(1026,2243,'DATE, hh:mm-hh:mm','date-timespan',1026),(1027,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',1031),(1028,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',1030),(1029,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/inherit.png);\"></span>Без изменений','inherit',1027),(1030,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',1028),(1031,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',1029),(1038,2267,'Изменение','evt',1038),(1039,2276,'Общий','event',1039),(1040,2276,'Раздельный','getter',1040),(1041,2281,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1041),(1042,2281,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1042),(1043,2282,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1043),(1044,2282,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1044),(1045,2283,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1045),(1046,2283,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1046),(1047,2285,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1047),(1048,2285,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1048),(1049,2288,'Январь','01',1049),(1050,2288,'Февраль','02',1050),(1051,2288,'Март','03',1051),(1052,2288,'Апрель','04',1052),(1053,2288,'Май','05',1053),(1054,2288,'Июнь','06',1054),(1055,2288,'Июль','07',1055),(1056,2288,'Август','08',1056),(1057,2288,'Сентябрь','09',1057),(1058,2288,'Октябрь','10',1058),(1059,2288,'Ноябрь','11',1059),(1060,2288,'Декабрь','12',1060),(1061,2301,'Всем пользователям','all',1061),(1062,2301,'Только выбранным','only',1062),(1063,2301,'Всем кроме выбранных','except',1063),(1064,2301,'Никому','none',1064),(1065,2159,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Скрыт','h',1065),(1066,2304,'Пусто','none',1066),(1067,2304,'Сумма','sum',1067),(1068,2304,'Среднее','average',1068),(1069,2304,'Минимум','min',1069),(1070,2304,'Максимум','max',1070),(1071,2304,'Текст','text',1071),(1072,2306,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1072),(1073,2306,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Да','y',1073),(1074,2308,'Обычные','normal',1075),(1075,2308,'Зафиксированные','locked',1074),(1077,2316,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1077),(1078,2316,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1078),(1079,22,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Скрыт','h',1079),(1080,2318,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1080),(1081,2318,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1081),(1082,2319,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1082),(1083,2319,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1083),(1084,2321,'Проектное','p',1084),(1085,2321,'<font color=red>Системное</font>','s',1085),(1086,2324,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1086),(1087,2324,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включено','y',1087),(1088,2239,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1013),(1089,2239,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1089),(1090,2325,'Ничего','noth',1090),(1091,2325,'Чтото','smth',1091),(1092,2328,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1092),(1093,2328,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1093),(1094,2328,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1094),(1095,2328,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1095),(1096,2329,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1096),(1097,2329,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1097),(1098,2329,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1098),(1099,2329,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1099),(1100,2331,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1100),(1101,2331,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1101),(1102,2331,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1102),(1103,2331,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1103),(1104,2332,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1104),(1105,2332,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1105),(1106,2332,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1106),(1107,2332,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1107),(1108,2333,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1108),(1109,2333,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1109),(1110,2333,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1110),(1111,2333,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1111),(1112,2334,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1112),(1113,2334,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1113),(1114,2334,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1114),(1115,2334,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1115),(1116,2342,'Оценка масштабов','count',1116),(1117,2342,'Создание очереди','items',1117),(1118,2342,'Процессинг очереди','queue',1118),(1119,2342,'Применение результатов','apply',1119),(1120,2343,'Ожидание','waiting',1120),(1121,2343,'В работе','progress',1121),(1122,2343,'Завершено','finished',1122),(1123,2346,'Ожидание','waiting',1123),(1124,2346,'В работе','progress',1124),(1125,2346,'Завершено','finished',1125),(1126,2349,'Ожидание','waiting',1126),(1127,2349,'В работе','progress',1127),(1128,2349,'Завершено','finished',1128),(1129,2353,'Ожидание','waiting',1129),(1130,2353,'В работе','progress',1130),(1131,2353,'Завершено','finished',1131),(1132,2353,'Не требуется','noneed',1132),(1133,2356,'Ожидание','waiting',1133),(1134,2356,'В работе','progress',1134),(1135,2356,'Завершено','finished',1135),(1136,2362,'Ожидание','waiting',1136),(1137,2362,'В работе','progress',1137),(1138,2362,'Завершено','finished',1138),(1139,2365,'Ожидание','waiting',1139),(1140,2365,'В работе','progress',1140),(1141,2365,'Завершено','finished',1141),(1142,2368,'Ожидание','waiting',1142),(1143,2368,'В работе','progress',1143),(1144,2368,'Завершено','finished',1144),(1145,2368,'Не требуется','noneed',1145),(1146,2371,'Ожидание','waiting',1146),(1147,2371,'В работе','progress',1147),(1148,2371,'Завершено','finished',1148),(1149,2378,'Добавлен','items',1149),(1150,2378,'Обработан','queue',1150),(1151,2378,'Применен','apply',1151),(1152,2381,'Не указана','none',1152),(1153,2381,'AdminSystemUi','adminSystemUi',1153),(1154,2381,'AdminCustomUi','adminCustomUi',1154),(1155,2381,'AdminCustomData','adminCustomData',1155),(1156,2384,'Проектная','p',1156),(1158,2386,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключена','n',1158),(1157,2384,'<font color=red>Системная</font>','s',1157),(1159,2386,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1159),(1160,2386,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включена','y',1160),(1161,2386,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1161),(1169,2397,'Сессия','session',1169),(1170,2397,'Вкладка','channel',1170),(1171,2397,'Контекст','context',1171),(1172,2410,'Не применимо','none',1172),(1173,2410,'Набор записей','rowset',1173),(1174,2410,'Одна запись','row',1174),(1176,1345,'<span class=\"i-color-box\" style=\"background: transparent;\"></span>Нет','0',1176),(1177,1345,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-create-deny.png);\"></span>Да','1',1177),(1178,2312,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-single-select.png);\"></span>Нет','0',1178),(1179,2312,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-multi-select.png);\"></span>Да','1',1179),(1180,2310,'<span class=\"i-color-box\" style=\"background: transparent;\"></span>Нет','0',1180),(1181,2310,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-numberer.png);\"></span>Да','1',1181);
/*!40000 ALTER TABLE `enumset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faction`
--

DROP TABLE IF EXISTS `faction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faction`
--

LOCK TABLES `faction` WRITE;
/*!40000 ALTER TABLE `faction` DISABLE KEYS */;
INSERT INTO `faction` VALUES (1,'По умолчанию','index','rs','s'),(2,'Просмотр','details','r','s'),(3,'Изменить','form','r','s'),(5,'Добавить','create','n','s'),(6,'Активация аккаунта','activation','n','o'),(36,'Регистрация','registration','n','o'),(37,'Восстановление доступа','changepasswd','n','o');
/*!40000 ALTER TABLE `faction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field`
--

DROP TABLE IF EXISTS `field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `storeRelationAbility` enum('none','many','one') NOT NULL DEFAULT 'none',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `mode` enum('regular','required','readonly','hidden') NOT NULL DEFAULT 'regular',
  `tooltip` text NOT NULL,
  `l10n` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `columnTypeId` (`columnTypeId`),
  KEY `elementId` (`elementId`),
  KEY `relation` (`relation`),
  KEY `storeRelationAbility` (`storeRelationAbility`),
  KEY `mode` (`mode`),
  KEY `l10n` (`l10n`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=2431 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field`
--

LOCK TABLES `field` WRITE;
/*!40000 ALTER TABLE `field` DISABLE KEYS */;
INSERT INTO `field` VALUES (1,1,'Наименование','title',1,1,'',1,0,'none','','required','','n'),(2,1,'Тип столбца MySQL','type',1,1,'',2,0,'none','','required','','n'),(3,1,'Можно хранить ключи','canStoreRelation',10,5,'n',3,6,'one','','regular','','n'),(4,2,'Наименование','title',1,1,'',4,0,'none','','required','','n'),(5,2,'Таблица БД','table',1,1,'',5,0,'none','','required','','n'),(6,5,'Сущность','entityId',3,23,'0',6,2,'one','','readonly','','n'),(2413,5,'Внешние ключи','fk',0,16,'',9,0,'none','','regular','','n'),(7,5,'Наименование','title',1,1,'',7,0,'none','','required','','n'),(8,5,'Псевдоним','alias',1,1,'',8,0,'none','','required','','n'),(2420,195,'Доступ','accesss',0,16,'',2204,0,'none','','regular','','n'),(9,5,'Тип столбца MySQL','columnTypeId',3,23,'0',523,1,'one','','regular','','n'),(10,5,'Элемент управления','elementId',3,23,'0',413,4,'one','','required','','n'),(11,5,'Значение по умолчанию','defaultValue',1,1,'',2412,0,'none','','regular','','n'),(12,5,'Ключи какой сущности','relation',3,23,'0',11,2,'one','','regular','','n'),(14,5,'Порядок','move',3,4,'0',2414,0,'none','','regular','','n'),(15,6,'Поле','fieldId',3,23,'0',15,5,'one','','required','','n'),(16,6,'Наименование','title',4,1,'',16,0,'none','','required','','n'),(17,6,'Псевдоним','alias',1,1,'',17,0,'none','','required','','n'),(18,3,'Вышестоящий раздел','sectionId',3,23,'0',22,3,'one','','regular','','n'),(19,3,'Сущность','entityId',3,23,'0',514,2,'one','','regular','','n'),(2304,9,'Внизу','summaryType',10,23,'none',2308,6,'one','','regular','','n'),(2198,195,'Непустой результат','consistence',12,9,'1',2419,0,'none','','regular','','n'),(20,3,'Наименование','title',1,1,'',18,0,'none','','required','','n'),(21,3,'Контроллер','alias',1,1,'',19,0,'none','','required','','n'),(22,3,'Статус','toggle',10,23,'y',20,6,'one','','regular','','n'),(23,3,'Порядок','move',3,4,'',1278,0,'none','','regular','','n'),(25,3,'Количество записей на странице','rowsOnPage',3,1,'25',2310,0,'none','','regular','','n'),(26,8,'Раздел','sectionId',3,23,'',1,3,'one','','readonly','','n'),(27,8,'Действие','actionId',3,23,'',1,7,'one','','required','','n'),(28,8,'Доступ','profileIds',1,7,'14',1,10,'many','','regular','','n'),(2428,3,'Записи','store',0,16,'',2429,0,'none','','hidden','','n'),(29,8,'Статус','toggle',10,23,'y',1,6,'one','','regular','','n'),(30,8,'Порядок','move',3,4,'',1,0,'none','','regular','','n'),(31,7,'Наименование','title',1,1,'',26,0,'none','','required','','n'),(32,7,'Псевдоним','alias',1,1,'',27,0,'none','','required','','n'),(33,9,'Раздел','sectionId',3,23,'',28,3,'one','','readonly','','n'),(34,9,'Поле','fieldId',3,23,'',29,5,'one','','required','','n'),(35,9,'Порядок','move',3,4,'',2165,0,'none','','regular','','n'),(36,10,'Наименование','title',1,1,'',31,0,'none','','required','','n'),(37,10,'Статус','toggle',10,23,'y',1271,6,'one','','regular','','n'),(38,11,'Роль','profileId',3,23,'',33,10,'one','','readonly','','n'),(39,11,'Имя','title',1,1,'',34,0,'none','','required','','n'),(40,11,'Логин','email',1,1,'',35,0,'none','','required','','n'),(41,11,'Пароль','password',1,1,'',36,0,'none','','required','','n'),(42,11,'Статус','toggle',10,23,'y',37,6,'one','','regular','','n'),(64,4,'Наименование','title',1,1,'',53,0,'none','','required','','n'),(65,4,'Псевдоним','alias',1,1,'',54,0,'none','','required','','n'),(66,4,'Совместимость с внешними ключами','storeRelationAbility',11,23,'none',55,6,'many','','regular','','n'),(1445,195,'Статус','toggle',10,23,'y',2184,6,'one','','regular','','n'),(92,4,'Не отображать в формах','hidden',12,9,'0',72,0,'none','','regular','','n'),(106,20,'Поле','fieldId',3,23,'0',86,5,'one','','required','','n'),(107,20,'Наименование','title',1,1,'',87,0,'none','','required','','n'),(108,20,'Псевдоним','alias',1,1,'',88,0,'none','','required','','n'),(109,20,'Ширина','masterDimensionValue',3,18,'0',91,0,'none','','regular','','n'),(110,20,'Высота','slaveDimensionValue',3,18,'0',93,0,'none','','regular','','n'),(111,20,'Размер','proportions',10,5,'o',89,6,'one','','regular','','n'),(112,20,'Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>','slaveDimensionLimitation',12,9,'1',92,0,'none','','regular','','n'),(114,20,'При расчете пропорций отталкиваться от','masterDimensionAlias',10,5,'width',90,6,'one','','regular','','n'),(131,25,'Наименование','title',1,1,'',101,0,'none','','required','','n'),(133,25,'Псевдоним','alias',1,1,'',102,0,'none','','required','','n'),(137,25,'Статус','toggle',10,5,'y',2208,6,'one','','regular','','n'),(345,7,'Для выполнения действия необходимо выбрать стоку','rowRequired',10,5,'y',308,6,'one','','regular','','n'),(377,6,'Порядок','move',3,4,'0',338,0,'none','','regular','','n'),(470,5,'Хранит ключи','storeRelationAbility',10,23,'none',10,6,'one','','regular','','n'),(471,90,'Элемент управления','elementId',3,23,'0',429,4,'one','','readonly','','n'),(472,90,'Наименование','title',1,1,'',430,0,'none','','required','','n'),(473,90,'Псевдоним','alias',1,1,'',431,0,'none','','required','','n'),(474,90,'Значение по умолчанию','defaultValue',1,1,'',432,0,'none','','regular','','n'),(475,1,'Совместимые элементы управления','elementId',1,23,'',433,4,'many','','required','','n'),(476,91,'В контексте какого поля','fieldId',3,23,'0',434,5,'one','','required','','n'),(477,91,'Параметр настройки','possibleParamId',3,23,'0',435,90,'one','','required','','n'),(478,91,'Значение параметра','value',4,6,'',436,0,'none','','regular','','n'),(502,3,'PHP','extendsPhp',1,1,'Indi_Controller_Admin',461,0,'none','','regular','Родительский класс PHP','n'),(2309,3,'JS','extendsJs',1,1,'Indi.lib.controller.Controller',443,0,'none','','regular','Родительский класс JS','n'),(503,3,'Сортировка','defaultSortField',3,23,'0',2303,5,'one','','regular','','n'),(2212,8,'Южная панель','south',10,23,'auto',2212,6,'one','','regular','','n'),(555,2,'Родительский класс PHP','extends',1,1,'Indi_Db_Table',512,0,'none','','required','','n'),(557,3,'Направление сортировки','defaultSortDirection',10,23,'ASC',2309,6,'one','','regular','','n'),(559,101,'Наименование','title',1,1,'',517,0,'none','','required','','n'),(560,101,'Псевдоним','alias',1,1,'',519,0,'none','','required','','n'),(563,101,'Прикрепленная сущность','entityId',3,23,'0',520,2,'one','','regular','','n'),(581,101,'Соответствующий раздел бэкенда','sectionId',3,23,'0',534,3,'one','','regular','','n'),(585,101,'Порядок отображения соответствующего пункта в меню','move',3,4,'0',950,0,'none','','regular','','n'),(594,20,'Изменить оттенок','changeColor',10,5,'n',545,6,'one','','regular','','n'),(595,20,'Оттенок','color',13,11,'',546,0,'none','','regular','','n'),(612,2,'Фракция','system',10,23,'n',559,6,'one','','regular','','n'),(678,128,'Имя','title',1,1,'',625,0,'none','','required','','n'),(679,128,'Email','email',1,1,'',626,0,'none','','required','','n'),(680,128,'Сообщение','message',4,6,'',627,0,'none','','required','','n'),(681,128,'Дата','date',9,19,'<?=date(\'Y-m-d H:i:s\')?>',628,0,'none','','regular','','n'),(1364,7,'Тип','type',10,5,'p',2099,6,'one','','regular','','n'),(1365,146,'Тип','type',10,5,'p',1293,6,'one','','regular','','n'),(1444,195,'Порядок','move',3,4,'0',1316,0,'none','','regular','','n'),(1442,195,'Раздел','sectionId',3,23,'0',1313,3,'one','','readonly','','n'),(1443,195,'Поле','fieldId',3,23,'0',1314,5,'one','`elementId` NOT IN (4,14,16,20,22)','required','','n'),(2419,195,'Флаги','flags',0,16,'',2317,0,'none','','regular','','n'),(1441,2,'Включить в кэш','useCache',12,23,'0',1312,0,'none','','hidden','','n'),(754,5,'Статическая фильтрация','filter',1,1,'',12,0,'none','','regular','','n'),(767,3,'Фильтрация через SQL WHERE','filter',1,1,'',2213,0,'none','','regular','','n'),(857,146,'Наименование','title',1,1,'',803,0,'none','','required','','n'),(858,146,'Псевдоним','alias',1,1,'',804,0,'none','','required','','n'),(859,147,'Раздел фронтенда','fsectionId',3,23,'0',805,101,'one','','required','','n'),(860,147,'Действие','factionId',3,23,'0',806,146,'one','','required','','n'),(868,101,'Вышестоящий раздел','fsectionId',3,5,'0',516,101,'one','','regular','','n'),(869,101,'Статическая фильтрация','filter',1,1,'',814,0,'none','','regular','','n'),(960,101,'Количество строк для отображения по умолчанию','defaultLimit',3,18,'20',951,0,'none','','regular','','n'),(1366,3,'Фракция','type',10,23,'p',21,6,'one','','regular','','n'),(1009,101,'По умолчанию сортировка по','orderBy',10,5,'c',952,6,'one','','regular','','n'),(1010,101,'Столбец сортировки','orderColumn',3,23,'0',953,5,'one','','regular','','n'),(1011,101,'Направление сортировки','orderDirection',10,5,'ASC',981,6,'one','','regular','','n'),(1012,101,'SQL-выражение','orderExpression',1,1,'',982,0,'none','','regular','','n'),(1027,147,'Тип','type',10,5,'r',968,6,'one','','regular','','n'),(1040,101,'Тип','type',10,5,'r',538,6,'one','','regular','','n'),(1041,101,'Где брать идентификатор','where',1,1,'',983,0,'none','','regular','','n'),(1042,101,'Действие по умолчанию','index',1,1,'',1403,0,'none','','regular','','n'),(1074,146,'Выполнять maintenance()','maintenance',10,5,'r',1015,6,'one','','regular','','n'),(2418,320,'Роль','profileId',3,23,'0',2418,10,'one','','regular','','n'),(2416,320,'Наименование','title',1,1,'',2416,0,'none','','regular','','n'),(2417,320,'Цена','price',5,24,'0.00',2417,0,'none','','regular','','n'),(2414,5,'Элемент управления','el',0,16,'',13,0,'none','','regular','','n'),(1191,147,'Не указывать действие при создании seo-урлов из системных','blink',12,9,'0',1259,0,'none','','regular','','n'),(1192,162,'Раздел фронтенда','fsectionId',3,23,'0',1127,101,'one','','required','','n'),(1193,162,'Действие в разделе фронтенда','fsection2factionId',3,23,'0',1128,147,'one','','required','','n'),(1194,162,'Компонент','entityId',3,23,'0',1129,2,'one','','required','','n'),(1195,162,'Очередность','move',3,4,'0',1130,0,'none','','regular','','n'),(1196,162,'Префикс','prefix',1,1,'',1131,0,'none','','regular','','n'),(2196,9,'Вышестоящий столбец','gridId',3,23,'0',1738,9,'one','`sectionId` = \"<?=$this->sectionId?>\"','regular','','n'),(2310,3,'Включить нумерацию записей','rownumberer',10,23,'0',2323,6,'one','','regular','','n'),(2184,195,'Игнорировать шаблон опций','ignoreTemplate',12,9,'1',2421,0,'none','','regular','','n'),(2183,195,'Фильтрация','filter',1,1,'',1520,0,'none','','regular','','n'),(2176,301,'Источник','source',10,23,'section',2176,6,'one','','regular','','n'),(2177,301,'Сущность','entityId',3,23,'0',2177,2,'one','`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)','regular','','n'),(2178,301,'Поле','fieldId',3,23,'0',2178,5,'one','','regular','','n'),(2179,301,'Префикс','prefix',1,1,'',2179,0,'none','','regular','','n'),(2180,301,'Постфикс','postfix',1,1,'',2180,0,'none','','regular','','n'),(2181,301,'Порядок отображения','move',3,4,'0',2181,0,'none','','regular','','n'),(2182,195,'Значение по умолчанию','defaultValue',1,1,'',2167,0,'none','','regular','','n'),(1325,147,'Переименовать действие при генерации seo-урла','rename',12,9,'0',1260,0,'none','','regular','','n'),(1326,147,'Псевдоним','alias',1,1,'',1261,0,'none','','regular','','n'),(1327,147,'Настройки SEO','seoSettings',0,16,'',1126,0,'none','','regular','','n'),(1337,10,'Сущность пользователей','entityId',3,23,'11',2131,2,'one','`system`= \"n\"','regular','','n'),(2422,9,'Доступ','accesss',0,16,'',2315,0,'none','','regular','','n'),(1341,171,'Раздел','sectionId',3,23,'0',1275,3,'one','','readonly','','n'),(1342,171,'Поле','fieldId',3,23,'0',1276,5,'one','','required','','n'),(2251,171,'Изменить свойства','alter',0,16,'',2250,0,'none','','regular','','n'),(1345,3,'Запретить создание новых записей','disableAdd',10,23,'0',2211,6,'one','','regular','','n'),(1509,204,'Ширина','detailsHtmlWidth',3,18,'0',1383,0,'none','','regular','','n'),(1532,171,'Значение по умолчанию','defaultValue',1,1,'',2424,0,'none','','regular','','n'),(1485,204,'Наименование','title',1,1,'',1356,0,'none','','required','','n'),(1486,204,'Псевдоним','alias',1,1,'',1357,0,'none','','required','','n'),(1487,204,'Значение','detailsHtml',4,13,'',1382,0,'none','','regular','','n'),(1488,204,'Статус','toggle',10,5,'y',1358,6,'one','','regular','','n'),(1489,205,'Вышестояший пункт','menuId',3,23,'0',1359,205,'one','','regular','','n'),(1490,205,'Наименование','title',1,1,'',1360,0,'none','','required','','n'),(1491,205,'Связан со статической страницей','linked',10,5,'n',1361,6,'one','','regular','','n'),(1492,205,'Статическая страница','staticpageId',3,23,'0',1362,25,'one','','regular','','n'),(1493,205,'Ссылка','url',1,1,'',1363,0,'none','','regular','','n'),(1494,205,'Статус','toggle',10,23,'y',1364,6,'one','','regular','','n'),(1495,205,'Отображать в нижнем меню','bottom',10,5,'y',1365,6,'one','','regular','','n'),(1496,205,'Порядок отображения','move',3,4,'0',1366,0,'none','','regular','','n'),(1814,25,'Контент','details',4,13,'',1667,0,'none','','regular','','n'),(1510,204,'Контент','detailsSpan',0,16,'',1379,0,'none','','regular','','n'),(1511,204,'Высота','detailsHtmlHeight',3,18,'200',1384,0,'none','','regular','','n'),(1513,204,'Css класс для body','detailsHtmlBodyClass',1,1,'',1385,0,'none','','regular','','n'),(1514,204,'Css стили','detailsHtmlStyle',4,6,'',1386,0,'none','','regular','','n'),(1515,204,'Тип','type',10,5,'html',1380,6,'one','','regular','','n'),(1516,204,'Значение','detailsString',1,1,'',1387,0,'none','','regular','','n'),(1517,204,'Значение','detailsTextarea',4,6,'',1557,0,'none','','regular','','n'),(2173,301,'Тип компонента','type',10,23,'static',2173,6,'one','','regular','','n'),(2174,301,'Указанный вручную','content',1,1,'',2174,0,'none','','regular','','n'),(2175,301,'Шагов вверх','up',3,18,'0',2175,0,'none','','regular','','n'),(2172,301,'Тэг','tag',10,23,'title',2172,6,'one','','regular','','n'),(2171,301,'Действие','fsection2factionId',3,23,'0',2171,147,'one','','required','','n'),(2170,301,'Раздел','fsectionId',3,23,'0',2170,101,'one','','required','','n'),(1533,101,'Статус','toggle',10,5,'y',1429,6,'one','','regular','','n'),(1554,3,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',1277,5,'one','`storeRelationAbility`!=\"none\"','regular','','n'),(1560,101,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',815,5,'one','','regular','','n'),(1562,101,'От какого класса наследовать класс контроллера','extends',1,1,'',1431,0,'none','','regular','','n'),(2412,5,'MySQL','mysql',0,16,'',428,0,'none','','regular','','n'),(1658,195,'Переименовать','alt',1,1,'',2198,0,'none','','regular','','n'),(2132,10,'Порядок','move',3,4,'0',2215,0,'none','','regular','','n'),(1886,9,'Переименовать','alterTitle',1,1,'',2210,0,'none','','regular','','n'),(2100,7,'Отображать в панели действий','display',12,9,'1',2100,0,'none','','regular','','n'),(2131,10,'Дэшборд','dashboard',1,1,'',2132,0,'none','','regular','','n'),(2159,9,'Статус','toggle',10,23,'y',2205,6,'one','','regular','','n'),(2161,171,'Режим','mode',10,23,'inherit',2252,6,'one','','regular','','n'),(2166,91,'Auto title','title',1,1,'',2166,0,'none','','hidden','','n'),(2163,2,'Заголовочное поле','titleFieldId',3,23,'0',2163,5,'one','`entityId` = \"<?=$this->id?>\" AND `columnTypeId` != \"0\"','regular','','n'),(2164,8,'Auto title','title',1,1,'',2164,0,'none','','hidden','','n'),(2165,9,'Auto title','title',1,1,'',2196,0,'none','','hidden','','n'),(2167,195,'Auto title','title',1,1,'',2182,0,'none','','hidden','','n'),(2168,147,'Auto title','title',1,1,'',2242,0,'none','','hidden','','n'),(2169,171,'Auto title','title',1,1,'',1402,0,'none','','hidden','','n'),(2197,5,'Режим','mode',10,23,'regular',95,6,'one','','regular','','n'),(2199,5,'Подсказка','tooltip',4,6,'',414,0,'none','','regular','','n'),(2200,9,'Подсказка','tooltip',4,6,'',2304,0,'none','','regular','','n'),(2203,195,'Доступ','access',10,23,'all',2311,6,'one','','regular','','n'),(2202,7,'Статус','toggle',10,23,'y',2202,6,'one','','regular','','n'),(2204,195,'Кроме','profileIds',1,7,'',2314,10,'many','','regular','','n'),(2205,9,'Доступ','access',10,23,'all',2422,6,'one','','regular','','n'),(2206,9,'Кроме','profileIds',1,7,'',2423,10,'many','','regular','','n'),(2207,171,'Влияние','impact',10,23,'all',2169,6,'one','','regular','','n'),(2208,25,'Родительская страница','staticpageId',3,23,'0',1666,25,'one','','regular','','n'),(2209,8,'Переименовать','rename',1,1,'',2209,0,'none','','regular','','n'),(2210,9,'Редактор','editor',10,23,'0',2206,6,'one','','regular','','n'),(2211,3,'Группировка','groupBy',3,23,'0',2425,5,'one','','regular','','n'),(2213,3,'Режим подгрузки','rowsetSeparate',10,23,'auto',2302,6,'one','','regular','','n'),(2214,8,'Автосайз окна','fitWindow',10,23,'auto',2214,6,'one','','regular','','n'),(2215,10,'Максимальное количество окон','maxWindows',3,18,'15',2319,0,'none','','regular','','n'),(2270,309,'Заголовок','tplDecSubj',4,1,'',2271,0,'none','','regular','','n'),(2269,309,'Текст','tplIncBody',4,6,'',2270,0,'none','','regular','','n'),(2268,309,'Заголовок','tplIncSubj',4,1,'',2269,0,'none','','regular','','n'),(2267,309,'Назначение','tplFor',10,23,'inc',2268,6,'one','','regular','','n'),(2266,309,'Сообщение','tpl',0,16,'',2267,0,'none','','regular','','n'),(2263,309,'Цвет фона','bg',13,11,'212#d9e5f3',2264,0,'none','','regular','','n'),(2264,309,'Цвет текста','fg',13,11,'216#044099',2265,0,'none','','regular','','n'),(2265,309,'Подсказка','tooltip',4,6,'',2266,0,'none','','regular','','n'),(2262,309,'Пункты меню','sectionId',1,23,'',2263,3,'many','FIND_IN_SET(`sectionId`, \"<?=Indi::model(\'Section\')->fetchAll(\'`sectionId` = \"0\"\')->column(\'id\', true)?>\")','regular','','n'),(2255,309,'Сущность','entityId',3,23,'0',2256,2,'one','','required','','n'),(2256,309,'Событие / PHP','event',1,1,'',2257,0,'none','','regular','','n'),(2257,309,'Получатели','profileId',1,23,'',2258,10,'many','','required','','n'),(2258,309,'Статус','toggle',10,23,'y',2259,6,'one','','regular','','n'),(2259,309,'Счетчик','qty',0,16,'',2260,0,'none','','regular','','n'),(2260,309,'Отображение / SQL','qtySql',1,1,'',2261,0,'none','','required','','n'),(2261,309,'Направление изменения','qtyDiffRelyOn',10,23,'event',2262,6,'one','','regular','','n'),(2254,309,'Наименование','title',1,1,'',2254,0,'none','','required','','n'),(2236,307,'Наименование','title',1,1,'',2236,0,'none','','required','','n'),(2237,307,'Ключ','alias',1,1,'',2237,0,'none','','required','','n'),(2238,307,'Статус','toggle',10,23,'y',2238,6,'one','','regular','','n'),(2239,5,'Мультиязычность','l10n',10,23,'n',2413,6,'one','','regular','','n'),(2240,147,'Разрешено не передавать id в uri','allowNoid',12,9,'0',2168,0,'none','','regular','','n'),(2241,147,'Над записью','row',10,5,'existing',2240,6,'one','','regular','','n'),(2242,147,'Где брать идентификатор','where',1,1,'',2241,0,'none','','regular','','n'),(2243,2,'Паттерн комплекта календарных полей','spaceScheme',10,23,'none',2243,6,'one','','regular','','n'),(2244,2,'Комплект календарных полей','spaceFields',1,23,'',2244,5,'many','`entityId` = \"<?=$this->id?>\"','regular','','n'),(2245,308,'Сущность','entityId',3,23,'0',2245,2,'one','','hidden','','n'),(2246,308,'Поле','fieldId',3,23,'0',2246,5,'one','','readonly','','n'),(2247,308,'От какого поля зависит','consider',3,23,'0',2247,5,'one','`id` != \"<?=$this->fieldId?>\" AND `columnTypeId` != \"0\"','required','','n'),(2248,308,'Поле по ключу','foreign',3,23,'0',2248,5,'one','','regular','','n'),(2249,308,'Auto title','title',1,1,'',2249,0,'none','','hidden','','n'),(2250,171,'Кроме','profileIds',1,7,'',2207,10,'many','','regular','','n'),(2252,171,'Наименование','rename',1,1,'',2251,0,'none','','regular','','n'),(2424,171,'Влияние','impactt',0,16,'',2134,0,'none','','regular','','n'),(2271,309,'Текст','tplDecBody',4,6,'',2272,0,'none','','regular','','n'),(2272,309,'Заголовок','tplEvtSubj',4,1,'',2273,0,'none','','regular','','n'),(2273,309,'Сообщение','tplEvtBody',4,6,'',2321,0,'none','','regular','','n'),(2274,310,'Уведомление','noticeId',3,23,'0',2275,309,'one','','readonly','','n'),(2275,310,'Роль','profileId',3,23,'0',2276,10,'one','','readonly','','n'),(2276,310,'Критерий','criteriaRelyOn',10,5,'event',2277,6,'one','','regular','','n'),(2277,310,'Общий','criteriaEvt',1,1,'',2278,0,'none','','regular','','n'),(2278,310,'Для увеличения','criteriaInc',1,1,'',2279,0,'none','','regular','','n'),(2279,310,'Для уменьшения','criteriaDec',1,1,'',2280,0,'none','','regular','','n'),(2280,310,'Ауто титле','title',1,1,'',2281,0,'none','','hidden','','n'),(2281,310,'Дублирование на почту','email',10,23,'n',2282,6,'one','','regular','','n'),(2282,310,'Дублирование в ВК','vk',10,23,'n',2283,6,'one','','regular','','n'),(2283,310,'Дублирование по SMS','sms',10,23,'n',2284,6,'one','','regular','','n'),(2284,310,'Критерий','criteria',1,1,'',2285,0,'none','','hidden','','n'),(2285,310,'Дублирование на почту','mail',10,23,'n',2316,6,'one','','hidden','','n'),(2286,311,'Наименование','title',1,1,'',2286,0,'none','','required','','n'),(2287,312,'Год','yearId',3,23,'0',2287,311,'one','','required','','n'),(2288,312,'Месяц','month',10,23,'01',2288,6,'one','','regular','','n'),(2289,312,'Наименование','title',1,1,'',2289,0,'none','','regular','','n'),(2290,312,'Порядок','move',3,4,'0',2290,0,'none','','regular','','n'),(2291,313,'Сущность','entityId',3,23,'0',2291,2,'one','','readonly','','n'),(2292,313,'Объект','key',3,23,'0',2292,0,'one','','readonly','','n'),(2293,313,'Что изменено','fieldId',3,23,'0',2293,5,'one','`columnTypeId` != \"0\"','readonly','','n'),(2294,313,'Было','was',4,13,'',2294,0,'none','','readonly','','n'),(2295,313,'Стало','now',4,13,'',2295,0,'none','','readonly','','n'),(2296,313,'Когда','datetime',9,19,'0000-00-00 00:00:00',2296,0,'none','','readonly','','n'),(2297,313,'Месяц','monthId',3,23,'0',2297,312,'one','','readonly','','n'),(2298,313,'Тип автора','changerType',3,23,'0',2298,2,'one','','readonly','','n'),(2299,313,'Автор','changerId',3,23,'0',2299,0,'one','','readonly','','n'),(2300,313,'Роль','profileId',3,23,'0',2300,10,'one','','readonly','','n'),(2301,3,'Разворачивать пункт меню','expand',10,23,'all',23,6,'one','','regular','','n'),(2302,3,'Выбранные','expandRoles',1,23,'',25,10,'many','','regular','','n'),(2303,3,'Доступ','roleIds',1,23,'',2428,10,'many','','hidden','','n'),(2305,9,'Текст','summaryText',1,1,'',2313,0,'none','','regular','','n'),(2306,308,'Обязательное','required',10,23,'n',2306,6,'one','','regular','','n'),(2307,308,'Коннектор','connector',3,23,'0',2307,5,'one','','regular','','n'),(2308,9,'Группа','group',10,23,'normal',2253,6,'one','','regular','','n'),(2311,195,'Разрешить сброс','allowClear',12,9,'1',2420,0,'none','','regular','','n'),(2312,3,'Выделение более одной записи','multiSelect',10,23,'0',2322,6,'one','','regular','','n'),(2313,9,'Поле по ключу','further',3,23,'0',30,5,'one','','regular','','n'),(2314,195,'Поле по ключу','further',3,23,'0',1315,5,'one','','regular','','n'),(2315,9,'Ширина','width',3,18,'0',2305,0,'none','','regular','','n'),(2316,310,'Статус','toggle',10,23,'y',2274,6,'one','','regular','','n'),(2317,195,'Подсказка','tooltip',4,6,'',2203,0,'none','','regular','','n'),(2318,11,'Демо-режим','demo',10,23,'n',2318,6,'one','','regular','','n'),(2319,10,'Демо-режим','demo',10,23,'n',2384,6,'one','','regular','','n'),(2320,2,'Группировать файлы','filesGroupBy',3,23,'0',2320,5,'one','`entityId` = \"<?=$this->id?>\" AND `storeRelationAbility` = \"one\"','regular','','n'),(2321,309,'Тип','type',10,23,'p',2255,6,'one','','regular','','n'),(2322,3,'Плитка','tileField',3,23,'0',2426,5,'one','`elementId` = \"14\"','regular','','n'),(2323,3,'Превью','tileThumb',3,23,'0',2427,20,'one','','regular','','n'),(2324,11,'Правки UI','uiedit',10,23,'n',2324,6,'one','','regular','','n'),(2325,307,'Состояние','state',10,23,'noth',2325,6,'one','','readonly','','n'),(2326,307,'Админка','admin',0,16,'',2326,0,'none','','regular','','n'),(2327,307,'Система','adminSystem',0,16,'',2327,0,'none','','regular','','n'),(2328,307,'Интерфейс','adminSystemUi',10,5,'n',2328,6,'one','','regular','','n'),(2329,307,'Константы','adminSystemConst',10,5,'n',2329,6,'one','','regular','','n'),(2330,307,'Проект','adminCustom',0,16,'',2330,0,'none','','regular','','n'),(2331,307,'Интерфейс','adminCustomUi',10,5,'n',2331,6,'one','','regular','','n'),(2332,307,'Константы','adminCustomConst',10,5,'n',2332,6,'one','','regular','','n'),(2333,307,'Данные','adminCustomData',10,5,'n',2333,6,'one','','regular','','n'),(2334,307,'Шаблоны','adminCustomTmpl',10,5,'n',2334,6,'one','','regular','','n'),(2335,307,'Порядок','move',3,4,'0',2335,0,'none','','regular','','n'),(2336,314,'Задача','title',1,1,'',2336,0,'none','','required','','n'),(2337,314,'Создана','datetime',9,19,'<?=date(\'Y-m-d H:i:s\')?>',2337,0,'none','','readonly','','n'),(2338,314,'Параметры','params',4,6,'',2338,0,'none','','regular','','n'),(2339,314,'Процесс','proc',0,16,'',2339,0,'none','','regular','','n'),(2340,314,'Начат','procSince',9,19,'0000-00-00 00:00:00',2340,0,'none','','regular','','n'),(2341,314,'PID','procID',3,18,'0',2341,0,'none','','readonly','','n'),(2342,314,'Этап','stage',10,23,'count',2342,6,'one','','regular','','n'),(2343,314,'Статус','state',10,23,'waiting',2343,6,'one','','regular','','n'),(2344,314,'Сегменты','chunk',3,18,'0',2345,0,'none','','regular','','n'),(2345,314,'Оценка','count',0,16,'',2346,0,'none','','regular','','n'),(2346,314,'Статус','countState',10,23,'waiting',2347,6,'one','','readonly','','n'),(2347,314,'Размер','countSize',3,18,'0',2348,0,'none','','readonly','','n'),(2348,314,'Создание','items',0,16,'',2349,0,'none','','regular','','n'),(2349,314,'Статус','itemsState',10,23,'waiting',2350,6,'one','','readonly','','n'),(2350,314,'Размер','itemsSize',3,18,'0',2351,0,'none','','readonly','','n'),(2351,314,'Байт','itemsBytes',3,18,'0',2352,0,'none','','regular','','n'),(2352,314,'Процессинг','queue',0,16,'',2353,0,'none','','regular','','n'),(2353,314,'Статус','queueState',10,23,'waiting',2354,6,'one','','readonly','','n'),(2354,314,'Размер','queueSize',3,18,'0',2355,0,'none','','regular','','n'),(2355,314,'Применение','apply',0,16,'',2356,0,'none','','regular','','n'),(2356,314,'Статус','applyState',10,23,'waiting',2357,6,'one','','readonly','','n'),(2357,314,'Размер','applySize',3,18,'0',2382,0,'none','','readonly','','n'),(2358,315,'Очередь задач','queueTaskId',3,23,'0',2358,314,'one','','regular','','n'),(2359,315,'Расположение','location',1,1,'',2359,0,'none','','regular','','n'),(2360,315,'Условие выборки','where',4,6,'',2362,0,'none','','regular','','n'),(2361,315,'Оценка','count',0,16,'',2363,0,'none','','regular','','n'),(2362,315,'Статус','countState',10,23,'waiting',2364,6,'one','','readonly','','n'),(2363,315,'Размер','countSize',3,18,'0',2365,0,'none','','readonly','','n'),(2364,315,'Создание','items',0,16,'',2366,0,'none','','regular','','n'),(2365,315,'Статус','itemsState',10,23,'waiting',2367,6,'one','','readonly','','n'),(2366,315,'Размер','itemsSize',3,18,'0',2368,0,'none','','readonly','','n'),(2367,315,'Процессинг','queue',0,16,'',2369,0,'none','','regular','','n'),(2368,315,'Статус','queueState',10,23,'waiting',2371,6,'one','','readonly','','n'),(2369,315,'Размер','queueSize',3,18,'0',2372,0,'none','','regular','','n'),(2370,315,'Применение','apply',0,16,'',2379,0,'none','','regular','','n'),(2371,315,'Статус','applyState',10,23,'waiting',2380,6,'one','','readonly','','n'),(2372,315,'Размер','applySize',3,18,'0',2381,0,'none','','readonly','','n'),(2373,316,'Очередь','queueTaskId',3,18,'0',2373,314,'one','','readonly','','n'),(2374,316,'Сегмент','queueChunkId',3,23,'0',2374,315,'one','','regular','','n'),(2375,316,'Таргет','target',1,1,'',2375,0,'none','','readonly','','n'),(2376,316,'Значение','value',4,1,'',2376,0,'none','','readonly','','n'),(2377,316,'Результат','result',4,13,'',2377,0,'none','','regular','','n'),(2378,316,'Статус','stage',10,5,'items',2378,6,'one','','regular','','n'),(2379,315,'Порядок','move',3,4,'0',2383,0,'none','','regular','','n'),(2380,315,'Родительский сегмент','queueChunkId',3,18,'0',2360,315,'one','','readonly','','n'),(2381,315,'Фракция','fraction',10,23,'none',2361,6,'one','','regular','','n'),(2382,314,'Этап - Статус','stageState',1,1,'',2344,0,'none','','hidden','','n'),(2383,315,'Байт','itemsBytes',3,18,'0',2370,0,'none','','regular','','n'),(2384,10,'Тип','type',10,5,'p',32,6,'one','','regular','','n'),(2385,171,'Элемент','elementId',3,23,'0',2385,4,'one','','regular','','n'),(2386,8,'Мультиязычность','l10n',10,23,'n',2386,6,'one','','regular','','n'),(2396,318,'Родительская запись','realtimeId',3,23,'0',2396,318,'one','','regular','','n'),(2397,318,'Тип','type',10,23,'session',2397,6,'one','','regular','','n'),(2398,318,'Роль','profileId',3,23,'0',2398,10,'one','','regular','','n'),(2399,318,'Пользователь','adminId',3,23,'0',2399,0,'one','','regular','','n'),(2400,318,'Токен','token',1,1,'',2400,0,'none','','regular','','n'),(2401,318,'Начало','spaceSince',9,19,'<?=date(\'Y-m-d H:i:s\')?>',2401,0,'none','','regular','','n'),(2402,318,'Конец','spaceUntil',9,19,'0000-00-00 00:00:00',2402,0,'none','','regular','','n'),(2403,318,'Длительность','spaceFrame',3,18,'0',2403,0,'none','','regular','','n'),(2404,318,'Язык','langId',3,23,'0',2404,307,'one','','regular','','n'),(2405,318,'Раздел','sectionId',3,23,'0',2405,3,'one','','regular','','n'),(2406,318,'Сущность','entityId',3,23,'0',2406,2,'one','','regular','','n'),(2407,318,'Записи','entries',1,23,'',2407,0,'many','','regular','','n'),(2408,318,'Поля','fields',4,23,'',2408,5,'many','','regular','','n'),(2409,318,'Запись','title',1,1,'',2409,0,'none','','hidden','','n'),(2410,318,'Режим','mode',10,23,'none',2410,6,'one','','regular','','n'),(2411,318,'Scope','scope',4,6,'',2411,0,'none','','regular','','n'),(2421,195,'Отображение','display',0,16,'',2183,0,'none','','regular','','n'),(2423,9,'Отображение','display',0,16,'',2200,0,'none','','regular','','n'),(2425,3,'Родительские классы','extends',0,16,'',310,0,'none','','regular','','n'),(2426,3,'Источник записей','data',0,16,'',462,0,'none','','regular','','n'),(2427,3,'Отображение записей','display',0,16,'',2312,0,'none','','regular','','n'),(2429,3,'Подгрузка записей','load',0,16,'',2301,0,'none','','regular','','n'),(2430,3,'Параметры','params',0,16,'',2430,0,'none','','hidden','','n');
/*!40000 ALTER TABLE `field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fsection`
--

DROP TABLE IF EXISTS `fsection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fsection`
--

LOCK TABLES `fsection` WRITE;
/*!40000 ALTER TABLE `fsection` DISABLE KEYS */;
INSERT INTO `fsection` VALUES (37,'Статические страницы','static',25,30,39,0,'',20,'c',0,'ASC','','r','','','y',0,''),(22,'Фидбэк','feedback',128,144,44,0,'',20,'c',0,'ASC','','s','\"\"','add','n',0,''),(39,'Главная','index',25,0,8,0,'',20,'c',0,'ASC','','r','','','y',0,''),(41,'Карта сайта','sitemap',101,113,41,0,'`toggle`=\"y\"',20,'c',585,'ASC','','r','','','y',0,'');
/*!40000 ALTER TABLE `fsection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fsection2faction`
--

DROP TABLE IF EXISTS `fsection2faction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fsection2faction`
--

LOCK TABLES `fsection2faction` WRITE;
/*!40000 ALTER TABLE `fsection2faction` DISABLE KEYS */;
INSERT INTO `fsection2faction` VALUES (129,22,1,'r',0,0,'','По умолчанию',0,'existing',''),(127,37,2,'r',0,0,'','Просмотр',0,'existing',''),(123,39,1,'r',0,0,'','По умолчанию',0,'existing',''),(128,41,1,'r',0,0,'','По умолчанию',0,'existing','');
/*!40000 ALTER TABLE `fsection2faction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grid`
--

DROP TABLE IF EXISTS `grid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `alterTitle` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n','h','e') NOT NULL DEFAULT 'y',
  `title` varchar(255) NOT NULL DEFAULT '',
  `gridId` int(11) NOT NULL DEFAULT '0',
  `tooltip` text NOT NULL,
  `access` enum('all','only','except') NOT NULL DEFAULT 'all',
  `profileIds` varchar(255) NOT NULL DEFAULT '',
  `editor` enum('0','1') NOT NULL DEFAULT '0',
  `summaryType` enum('none','sum','average','min','max','text') NOT NULL DEFAULT 'none',
  `summaryText` varchar(255) NOT NULL DEFAULT '',
  `group` enum('normal','locked') NOT NULL DEFAULT 'normal',
  `further` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `toggle` (`toggle`),
  KEY `gridId` (`gridId`),
  KEY `access` (`access`),
  KEY `profileIds` (`profileIds`),
  KEY `editor` (`editor`),
  KEY `summaryType` (`summaryType`),
  KEY `group` (`group`),
  KEY `further` (`further`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=2658 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grid`
--

LOCK TABLES `grid` WRITE;
/*!40000 ALTER TABLE `grid` DISABLE KEYS */;
INSERT INTO `grid` VALUES (1,2,1,1,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(2,2,2,2,'','y','Тип столбца MySQL',0,'','all','','0','none','','normal',0,0),(3,2,3,3,'','y','Пригоден для хранения внешних ключей',0,'','all','','0','none','','normal',0,0),(4,5,4,4,'','y','Наименование',0,'','all','','1','none','','normal',0,0),(5,5,5,5,'','y','Таблица БД',0,'','all','','1','none','','normal',0,0),(6,6,7,6,'','y','Наименование',0,'','all','','1','none','','normal',0,0),(7,6,8,7,'Псевдоним','y','Псевдоним',0,'','all','','1','none','','normal',0,0),(8,6,9,2352,'Тип столбца','y','Тип столбца MySQL',2609,'','all','','1','none','','normal',0,0),(9,6,10,2610,'Элемент','y','Элемент управления',2613,'','all','','1','none','','normal',0,0),(10,6,11,2609,'По умолчанию','y','DEFAULT',2609,'','all','','1','none','','normal',0,0),(11,6,12,10,'Сущность','y','Ключи какой сущности',2611,'','all','','1','none','','normal',0,0),(2610,6,470,9,'','y','Хранит ключи',2611,'','all','','0','none','','normal',0,0),(13,6,14,2613,'','y','Порядок',0,'','all','','0','none','','normal',0,0),(14,7,19,2460,'Сущность','y','Сущность',2642,'','all','','0','none','','normal',0,0),(15,7,20,14,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(16,7,21,15,'','y','Контроллер',2641,'','all','','1','none','','normal',0,0),(17,7,22,16,'','y','Статус',2655,'','all','','0','none','','normal',0,0),(18,7,23,18,'','y','Порядок',0,'','all','','0','none','','normal',0,0),(20,7,25,2644,'','y','Количество записей на странице',2654,'','all','','1','none','','normal',0,0),(23,8,27,23,'','y','Действие',0,'','all','','0','none','','normal',0,0),(24,8,29,297,'','y','Статус',0,'','all','','0','none','','normal',0,0),(25,8,30,2564,'','y','Порядок',0,'','all','','0','none','','normal',0,0),(26,10,31,26,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(27,10,32,27,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(29,11,2165,29,'Столбец','y','Auto title',0,'','all','','0','none','','normal',0,0),(30,11,35,2320,'','y','Порядок',0,'','all','','0','none','','normal',0,0),(32,13,36,32,'','y','Наименование',0,'','all','','1','none','','normal',0,0),(33,13,37,2329,'','y','Статус',0,'','all','','0','none','','normal',0,0),(34,12,16,34,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(35,12,17,35,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(36,14,39,36,'','y','Фамилия Имя',0,'','all','','1','none','','normal',0,0),(37,14,40,37,'','y','Email (используется в качестве логина)',0,'','all','','1','none','','normal',0,0),(38,14,41,38,'','y','Пароль',0,'','all','','1','none','','normal',0,0),(39,14,42,39,'','y','Статус',0,'','all','','0','none','','normal',0,0),(42,16,65,43,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(43,16,66,44,'','y','Способен работать с внешними ключами',0,'','all','','0','none','','normal',0,0),(46,16,64,42,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(89,22,107,56,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(90,22,108,57,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(91,22,109,59,'','y','Ширина',0,'','all','','0','none','','normal',0,0),(92,22,110,60,'','y','Высота',0,'','all','','0','none','','normal',0,0),(93,22,111,58,'','y','Размер',0,'','all','','0','none','','normal',0,0),(94,22,112,61,'','y','Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>',0,'','all','','0','none','','normal',0,0),(130,30,131,61,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(132,30,133,63,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(2365,379,0,2365,'Взятый из контекста','y','Взятый из контекста',2362,'','all','','0','none','','normal',0,0),(2363,379,2179,2363,'','y','Префикс',2362,'','all','','1','none','','normal',0,0),(136,30,137,67,'','y','Статус',0,'','all','','0','none','','normal',0,0),(341,12,377,253,'','y','Порядок отображения',0,'','all','','0','none','','normal',0,0),(375,100,472,289,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(376,100,473,290,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(377,100,474,291,'','y','Значение по умолчанию',0,'','all','','0','none','','normal',0,0),(378,101,477,292,'','y','Параметр настройки',0,'','all','','0','none','','normal',0,0),(379,101,478,293,'','y','Значение параметра',0,'','all','','0','none','','normal',0,0),(383,8,28,25,'','y','Доступ',0,'','all','','0','none','','normal',0,0),(2641,7,2425,2459,'Привязка к коду','y','Родительские классы',2655,'','all','','0','none','','normal',0,100),(1335,7,1366,17,'','y','Фракция',2655,'','all','','1','none','','normal',0,0),(1334,113,1040,925,'','y','Тип',0,'','all','','0','none','','normal',0,0),(1333,172,1365,924,'','y','Тип',0,'','all','','0','none','','normal',0,0),(1332,10,1364,923,'','y','Тип',0,'','all','','0','none','','normal',0,0),(420,113,559,328,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(421,113,560,329,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(424,113,563,330,'','y','Прикрепленная сущность',0,'','all','','0','none','','normal',0,0),(443,5,612,347,'','y','Тип',0,'','all','','0','none','','normal',0,0),(489,144,678,388,'','y','Имя',0,'','all','','0','none','','normal',0,0),(490,144,679,389,'','y','Email',0,'','all','','0','none','','normal',0,0),(491,144,681,391,'','y','Дата',0,'','all','','0','none','','normal',0,0),(2657,16,92,2657,'','y','Не отображать в формах',0,'','all','','0','none','','normal',0,0),(1382,224,1444,2312,'','y','Порядок',2630,'','all','','0','none','','normal',0,0),(1384,224,1445,929,'','y','Статус',2630,'','all','','0','none','','normal',0,0),(832,172,857,624,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(833,172,858,625,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(834,173,860,626,'','y','Действие',0,'','all','','0','none','','normal',0,0),(1383,224,1443,926,'','y','Поле',0,'','all','','1','none','','normal',0,0),(2625,224,2311,2625,'РС','y','Разрешить сброс',2628,'','all','','1','none','','normal',0,0),(2656,2,475,2656,'','y','Совместимые элементы управления',0,'','all','','1','none','','normal',0,0),(1039,172,1074,759,'','y','Выполнять maintenance()',0,'','all','','0','none','','normal',0,0),(979,173,1027,728,'','y','Тип',0,'','all','','0','none','','normal',0,0),(1066,191,1194,782,'','y','Компонент',0,'','all','','0','none','','normal',0,0),(1067,191,1195,783,'','y','Очередность',0,'','all','','0','none','','normal',0,0),(1068,191,1196,784,'','y','Префикс',0,'','all','','0','none','','normal',0,0),(2627,224,2198,2627,'НР','y','Содержательность',2628,'','all','','1','none','','normal',0,0),(2312,224,2183,1945,'','y','Фильтрация',0,'','all','','1','none','','normal',0,0),(2311,224,2182,2311,'Значение<br>по умолчанию','y','Значение по умолчанию',0,'','all','','1','none','','normal',0,0),(2362,379,0,2362,'Компонент','y','Компонент',0,'','all','','0','none','','normal',0,0),(2361,379,2172,2361,'','y','Тэг',0,'','all','','0','none','','normal',0,0),(2360,379,2181,2360,'','y','Порядок отображения',0,'','all','','0','none','','normal',0,0),(1231,201,1342,851,'','y','Поле',0,'','all','','0','none','','normal',0,0),(2356,201,2252,988,'','y','Наименование',2638,'','all','','1','none','','normal',0,0),(1439,232,1515,965,'','y','Тип',0,'','all','','0','none','','normal',0,0),(2359,379,2173,2359,'','y','Тип компонента',0,'','all','','0','none','','normal',0,0),(1421,232,1485,962,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(1422,232,1486,963,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0),(1423,232,1488,1132,'','y','Статус',0,'','all','','0','none','','normal',0,0),(1449,113,1533,989,'','y','Статус',0,'','all','','0','none','','normal',0,0),(1448,201,1532,2639,'','y','Значение по умолчанию',2638,'','all','','1','none','','normal',0,0),(1515,113,585,1036,'','y','Порядок отображения соответствующего пункта в меню',0,'','all','','0','none','','normal',0,0),(1656,11,1886,2419,'','y','Переименовать',2637,'','all','','1','none','','normal',0,0),(1954,224,1658,2621,'','y','Переименовать',2630,'','all','','1','none','','normal',0,0),(2280,201,2161,1946,'','y','Режим',2638,'','all','','0','none','','normal',0,0),(2320,11,2159,1156,'','y','Статус',2637,'','all','','0','none','','normal',0,0),(2321,6,2197,13,'','y','Режим',2613,'','all','','0','none','','normal',0,0),(2322,11,34,30,'','y','Поле',0,'','all','','1','none','','normal',0,0),(2323,10,2202,2323,'','y','Статус',0,'','all','','0','none','','normal',0,0),(2324,8,2209,24,'','y','Переименовать',0,'','all','','0','none','','normal',0,0),(2325,11,2210,2416,'','y','Редактор',2637,'','all','','0','none','','normal',0,0),(2326,8,2212,2324,'ЮП','y','Южная панель',0,'Режим отображения южной панели','all','','0','none','','normal',0,0),(2327,7,2213,2641,'РПД','y','Режим подгрузки',2654,'Режим подгрузки данных','all','','0','none','','normal',0,0),(2328,8,2214,2326,'','y','Автосайз окна',0,'','all','','0','none','','normal',0,0),(2329,13,2215,2464,'МКО','y','Максимальное количество окон',0,'Максимальное количество окон','all','','1','none','','normal',0,0),(2415,390,2283,2462,'SMS','y','Дублирование по SMS',0,'Дублирование по SMS','all','','0','none','','normal',0,0),(2394,389,2259,2394,'','y','Счетчик',0,'','all','','0','none','','normal',0,0),(2395,389,2260,2395,'','y','Отображение / SQL',2394,'','all','','0','none','','normal',0,0),(2396,389,2256,2396,'','y','Событие / PHP',2394,'','all','','0','none','','normal',0,0),(2397,389,2262,2397,'','y','Пункты меню',2394,'','all','','0','none','','normal',0,0),(2398,389,2263,2398,'','y','Цвет фона',2394,'','all','','0','none','','normal',0,0),(2399,389,2264,2399,'','y','Цвет текста',2394,'','all','','0','none','','normal',0,0),(2390,389,2254,2390,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(2391,389,2255,2391,'','y','Сущность',0,'','all','','0','none','','normal',0,0),(2392,389,2257,2392,'','y','Получатели',0,'','all','','0','none','','normal',0,0),(2393,389,2258,2393,'','y','Статус',0,'','all','','0','none','','normal',0,0),(2468,387,2237,2468,'','y','Ключ',0,'','all','','0','none','','normal',0,0),(2467,387,2236,2467,'','y','Наименование',0,'','all','','0','none','','normal',0,0),(2352,6,2239,2611,'l10n','y','Мультиязычность',2609,'','all','','0','none','','normal',0,0),(2353,388,2247,2352,'','y','От какого поля зависит',0,'','all','','0','none','','normal',0,0),(2354,388,2248,2353,'','y','Поле по ключу',0,'','all','','0','none','','normal',0,0),(2357,201,2207,2357,'Роли','y','Влияние',2640,'','all','','1','none','','normal',0,0),(2358,201,2250,2358,'','y','Кроме',2640,'','all','','1','none','','normal',0,0),(2364,379,2174,2364,'','y','Указанный вручную',2362,'','all','','0','none','','normal',0,0),(2366,379,2175,2366,'Уровень','y','Шагов вверх',2365,'','all','','0','none','','normal',0,0),(2367,379,2176,2367,'','y','Источник',2365,'','all','','0','none','','normal',0,0),(2368,379,2178,2368,'','y','Поле',2365,'','all','','0','none','','normal',0,0),(2369,379,2180,2369,'','y','Постфикс',2362,'','all','','1','none','','normal',0,0),(2414,390,2282,2415,'VK','y','Дублирование в ВК',0,'Дублирование во ВКонтакте','all','','0','none','','normal',0,0),(2413,390,2281,2414,'Email','y','Дублирование на почту',0,'Дублирование на почту','all','','0','none','','normal',0,0),(2412,390,2277,2413,'','y','Общий',0,'','all','','0','none','','normal',0,0),(2411,390,2275,2412,'','y','Роль',0,'','all','','0','none','','normal',0,0),(2417,388,2306,2417,'[ ! ]','y','Обязательное',0,'Обязательное','all','','0','none','','normal',0,0),(2418,388,2307,2418,'','y','Коннектор',0,'','all','','0','none','','normal',0,0),(2419,11,2308,2461,'','y','Группа',2637,'','all','','0','none','','normal',0,0),(2450,391,12,2450,'Сущность','y','Ключи какой сущности',2619,'','all','','1','none','','normal',0,0),(2449,391,470,2449,'','y','Хранит ключи',2619,'','all','','0','none','','normal',0,0),(2622,224,2203,2622,'','y','Кому',2629,'','all','','1','none','','normal',0,0),(2623,224,2204,2623,'','y','Выбранные',2629,'','all','','1','none','','normal',0,0),(2447,391,11,2447,'По умолчанию','y','Значение по умолчанию',2620,'','all','','1','none','','normal',0,0),(2446,391,9,2446,'Тип столбца','y','Тип столбца MySQL',2620,'','all','','1','none','','normal',0,0),(2445,391,8,2440,'Псевдоним','y','Псевдоним',0,'','all','','1','none','','normal',0,0),(2618,391,2414,2448,'','y','Элемент управления',0,'','all','','0','none','','normal',0,0),(2624,224,2317,2624,'','y','Подсказка',2630,'','all','','1','none','','normal',0,0),(2443,391,2199,2443,'','y','Подсказка',2618,'','all','','1','none','','normal',0,0),(2441,391,2197,2441,'','y','Режим',2618,'','all','','0','none','','normal',0,0),(2442,391,10,2442,'Элемент','y','Элемент управления',2618,'','all','','1','none','','normal',0,0),(2438,391,6,2438,'Сущность','y','Сущность, в структуру которой входит это поле',0,'Сущность, в структуру которой входит это поле','all','','0','none','','normal',0,0),(2439,391,7,2439,'','y','Наименование',0,'','all','','1','none','','normal',0,0),(2619,391,2413,2444,'','y','Внешние ключи',0,'','all','','0','none','','normal',0,0),(2451,391,754,2451,'Фильтрация','y','Статическая фильтрация',2619,'','all','','1','none','','normal',0,0),(2621,224,2314,928,'','y','Поле по ключу',0,'','all','','1','none','','normal',0,0),(2452,391,2239,2619,'l10n','y','Мультиязычность',2620,'Мультиязычность','all','','0','none','','normal',0,0),(2453,391,14,2620,'','y','Порядок',0,'','all','','0','none','','normal',0,0),(2459,7,502,926,'','y','PHP',2641,'','all','','1','none','','normal',0,50),(2460,7,2309,2327,'','y','JS',2641,'','all','','1','none','','normal',0,50),(2461,11,2315,2632,'','y','Ширина',2637,'','all','','1','none','','normal',0,0),(2462,390,2316,2411,'','y','Статус',0,'','all','','0','none','','normal',0,0),(2463,14,2318,2463,'Демо','y','Демо-режим',0,'Демо-режим','all','','0','none','','normal',0,0),(2464,13,2319,2563,'Демо','y','Демо-режим',0,'Демо-режим','all','','0','none','','normal',0,0),(2465,5,2320,2465,'','y','Группировать файлы',0,'','all','','1','none','','normal',0,0),(2466,14,2324,2466,'','y','Правки UI',0,'','all','','0','none','','normal',0,0),(2469,387,2326,2469,'','y','Админка',0,'','all','','0','none','','normal',0,0),(2470,387,2238,2470,'','y','Статус',2469,'','all','','0','none','','normal',0,0),(2471,387,2327,2471,'','y','Система',2469,'','all','','0','none','','normal',0,0),(2472,387,2328,2472,'','y','Интерфейс',2471,'','all','','0','none','','normal',0,0),(2473,387,2329,2473,'','y','Константы',2471,'','all','','0','none','','normal',0,0),(2474,387,2330,2474,'','y','Проект',2469,'','all','','0','none','','normal',0,0),(2475,387,2331,2475,'','y','Интерфейс',2474,'','all','','0','none','','normal',0,0),(2476,387,2332,2476,'','y','Константы',2474,'','all','','0','none','','normal',0,0),(2477,387,2333,2477,'','y','Данные',2474,'','all','','0','none','','normal',0,0),(2478,387,2334,2478,'','y','Шаблоны',2474,'','all','','0','none','','normal',0,0),(2479,387,2335,2479,'','y','Порядок',0,'','all','','0','none','','normal',0,0),(2519,392,2353,2519,'','y','Статус',2518,'','all','','0','none','','normal',0,0),(2518,392,2352,2518,'','y','Процессинг',0,'','all','','0','none','','normal',0,0),(2514,392,2348,2514,'','y','Создание',0,'','all','','0','none','','normal',0,0),(2515,392,2349,2515,'','y','Статус',2514,'','all','','0','none','','normal',0,0),(2517,392,2351,2517,'','y','Байт',2514,'','all','','0','sum','','normal',0,0),(2516,392,2350,2516,'','y','Размер',2514,'','all','','0','none','','normal',0,0),(2511,392,2345,2511,'','y','Оценка',0,'','all','','0','none','','normal',0,0),(2512,392,2346,2512,'','y','Статус',2511,'','all','','0','none','','normal',0,0),(2513,392,2347,2513,'','y','Размер',2511,'','all','','0','none','','normal',0,0),(2508,392,2339,2508,'','y','Процесс',0,'','all','','0','none','','normal',0,0),(2509,392,2341,2509,'','y','PID',2508,'','all','','0','none','','normal',0,0),(2510,392,2340,2510,'','y','Начат',2508,'','all','','0','none','','normal',0,0),(2505,392,2342,2505,'','h','Этап',0,'','all','','0','none','','normal',0,0),(2506,392,2343,2506,'','h','Статус',0,'','all','','0','none','','normal',0,0),(2507,392,2344,2507,'','y','Сегменты',0,'','all','','0','none','','normal',0,0),(2502,392,2337,2502,'','y','Создана',0,'','all','','0','none','','normal',0,0),(2503,392,2336,2503,'','y','Задача',0,'','all','','0','none','','normal',0,0),(2504,392,2338,2504,'','y','Параметры',0,'','all','','0','none','','normal',0,0),(2520,392,2354,2520,'','y','Размер',2518,'','all','','0','none','','normal',0,0),(2521,392,2355,2521,'','y','Применение',0,'','all','','0','none','','normal',0,0),(2522,392,2356,2522,'','y','Статус',2521,'','all','','0','none','','normal',0,0),(2523,392,2357,2523,'','y','Размер',2521,'','all','','0','none','','normal',0,0),(2548,393,2367,2548,'','y','Процессинг',0,'','all','','0','none','','normal',0,0),(2546,393,2365,2546,'','y','Статус',2545,'','all','','0','none','','normal',0,0),(2547,393,2366,2547,'','y','Размер',2545,'','all','','0','sum','','normal',0,0),(2543,393,2362,2543,'','y','Статус',2542,'','all','','0','none','','normal',0,0),(2544,393,2363,2544,'','y','Размер',2542,'','all','','0','sum','','normal',0,0),(2545,393,2364,2545,'','y','Создание',0,'','all','','0','none','','normal',0,0),(2541,393,2360,2541,'','y','Условие выборки',0,'','all','','0','none','','normal',0,0),(2542,393,2361,2542,'','y','Оценка',0,'','all','','0','none','','normal',0,0),(2538,393,0,2538,'','n','',0,'','all','','0','none','','normal',0,0),(2539,393,0,2539,'','n','',0,'','all','','0','none','','normal',0,0),(2540,393,2359,2540,'','y','Расположение',0,'','all','','0','none','','normal',0,0),(2549,393,2368,2549,'','y','Статус',2548,'','all','','0','none','','normal',0,0),(2550,393,2369,2550,'','y','Размер',2548,'','all','','0','sum','','normal',0,0),(2551,393,2370,2551,'','y','Применение',0,'','all','','0','none','','normal',0,0),(2552,393,2371,2552,'','y','Статус',2551,'','all','','0','none','','normal',0,0),(2553,393,2372,2553,'','y','Размер',2551,'','all','','0','sum','','normal',0,0),(2561,394,2378,2561,'','y','Статус',0,'','all','','0','none','','normal',0,0),(2560,394,2377,2560,'','y','Результат',0,'','all','','1','none','','normal',0,0),(2559,394,2376,2559,'','y','Значение',0,'','all','','0','none','','normal',0,0),(2558,394,2375,2558,'','y','Таргет',0,'','all','','0','none','','normal',0,0),(2562,393,2383,2562,'','y','Байт',2545,'','all','','0','sum','','normal',0,0),(2563,13,2384,33,'','y','Тип',0,'','all','','0','none','','normal',0,0),(2564,8,2386,2328,'','y','Мультиязычность',0,'','all','','0','none','','normal',0,0),(2626,224,2184,2626,'ИШ','y','Игнорировать шаблон, заданный в параметрах настроек поля',2628,'','all','','1','none','','normal',0,0),(2620,391,2412,2452,'','y','MySQL',0,'','all','','0','none','','normal',0,0),(2613,6,2414,2321,'Элемент управления','y','Элемент управления',0,'','all','','0','none','','normal',0,0),(2611,6,2413,8,'','y','Внешние ключи',0,'','all','','0','none','','normal',0,0),(2612,6,754,11,'Фильтрация','y','Статическая фильтрация',2611,'','all','','1','none','','normal',0,0),(2609,6,2412,2612,'','y','MySQL',0,'','all','','0','none','','normal',0,0),(2606,396,2406,2606,'','h','Сущность',0,'','all','','0','none','','normal',0,0),(2605,396,2404,2605,'','h','Язык',0,'','all','','0','none','','normal',0,0),(2604,396,2403,2604,'','h','Длительность',0,'','all','','0','none','','normal',0,0),(2603,396,2402,2603,'','h','Конец',0,'','all','','0','none','','normal',0,0),(2602,396,2401,2602,'','y','Начало',0,'','all','','0','none','','normal',0,0),(2601,396,2399,2601,'','y','Пользователь',0,'','all','','0','none','','normal',0,0),(2600,396,2398,2600,'','h','Роль',0,'','all','','0','none','','normal',0,0),(2599,396,2397,2599,'','h','Тип',0,'','all','','0','none','','normal',0,0),(2598,396,2405,2598,'','h','Раздел',0,'','all','','0','none','','normal',0,0),(2597,396,2400,2597,'','h','Токен',0,'','all','','0','none','','normal',0,0),(2596,396,2409,2596,'','y','Запись',0,'','all','','0','none','','normal',0,0),(2607,396,2407,2607,'','n','Записи',0,'','all','','0','none','','normal',0,0),(2608,396,2408,2608,'','h','Поля',0,'','all','','0','none','','normal',0,0),(2614,6,2199,2614,'','y','Подсказка',2613,'','all','','1','none','','normal',0,0),(2615,13,1337,2615,'','y','Сущность пользователей',0,'','all','','1','none','','normal',0,0),(2616,13,2131,2616,'','y','Дэшборд',0,'','all','','1','none','','normal',0,0),(2617,13,2132,2617,'','y','Порядок отображения',0,'','all','','0','none','','normal',0,0),(2628,224,2419,2630,'','y','Флаги',0,'','all','','0','none','','normal',0,0),(2629,224,2420,2629,'','y','Доступ',0,'','all','','0','none','','normal',0,0),(2630,224,2421,2628,'','y','Отображение',0,'','all','','0','none','','normal',0,0),(2631,11,2313,2325,'','y','Поле по ключу',0,'','all','','1','none','','normal',0,0),(2632,11,2200,2631,'','y','Подсказка',2637,'','all','','1','none','','normal',0,0),(2633,11,2304,2633,'','y','Внизу',2637,'','all','','1','none','','normal',0,0),(2634,11,2205,2635,'Кому','y','Доступ',2636,'','all','','1','none','','normal',0,0),(2635,11,2206,2636,'','y','Кроме',2636,'','all','','1','none','','normal',0,0),(2636,11,2422,2637,'','y','Доступ',0,'','all','','0','none','','normal',0,0),(2637,11,2423,2634,'','y','Отображение',0,'','all','','0','none','','normal',0,0),(2638,201,2251,2638,'','y','Изменить свойства',0,'','all','','0','none','','normal',0,0),(2639,201,2385,2356,'','y','Элемент',2638,'','all','','1','none','','normal',0,0),(2640,201,2424,2640,'','y','Влияние',0,'','all','','0','none','','normal',0,0),(2642,7,2426,31,'Источник','y','Источник записей',2653,'','all','','0','none','','normal',0,0),(2643,7,1345,2647,'ЗСН','y','Запретить создание новых записей',2642,'','all','','0','none','','normal',0,0),(2644,7,767,2643,'','y','Фильтрация',2642,'','all','','1','none','','normal',0,0),(2645,7,503,2642,'','y','Сортировка',2654,'','all','','1','none','','normal',0,0),(2646,7,557,2645,'','h','Направление сортировки',2654,'','all','','0','none','','normal',0,0),(2647,7,2427,2654,'Отображение','y','Отображение записей',2653,'','all','','0','none','','normal',0,0),(2648,7,2312,2648,'ВБО','y','Выделение более одной',2647,'','all','','0','none','','normal',0,0),(2649,7,2310,2649,'ВН','y','Включить нумерацию',2647,'','all','','0','none','','normal',0,0),(2650,7,2211,2650,'','y','Группировка',2647,'','all','','1','none','','normal',0,0),(2651,7,2322,2651,'','h','Плитка',2647,'','all','','0','none','','normal',0,0),(2652,7,2323,2652,'','h','Превью',2647,'','all','','0','none','','normal',0,0),(2653,7,2428,2655,'','y','Записи',0,'','all','','0','none','','normal',0,0),(2654,7,2429,2646,'','y','Подгрузка',2653,'','all','','0','none','','normal',0,0),(2655,7,2430,2653,'Свойства','y','Параметры',0,'','all','','0','none','','normal',0,100);
/*!40000 ALTER TABLE `grid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `state` enum('noth','smth') NOT NULL DEFAULT 'noth',
  `adminSystemUi` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  `adminSystemConst` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  `adminCustomUi` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  `adminCustomConst` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  `adminCustomData` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  `adminCustomTmpl` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  KEY `state` (`state`),
  KEY `adminSystemUi` (`adminSystemUi`),
  KEY `adminSystemConst` (`adminSystemConst`),
  KEY `adminCustomUi` (`adminCustomUi`),
  KEY `adminCustomConst` (`adminCustomConst`),
  KEY `adminCustomData` (`adminCustomData`),
  KEY `adminCustomTmpl` (`adminCustomTmpl`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lang`
--

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
INSERT INTO `lang` VALUES (1,'Русский','ru','y','smth','y','y','n','n','n','n',1),(2,'Engish','en','y','smth','n','y','n','n','n','n',2);
/*!40000 ALTER TABLE `lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,0,'Курсы','n',0,'/courses','y','y',1),(5,0,'Сотрудничество','y',9,'','y','y',5);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metatag`
--

DROP TABLE IF EXISTS `metatag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metatag`
--

LOCK TABLES `metatag` WRITE;
/*!40000 ALTER TABLE `metatag` DISABLE KEYS */;
INSERT INTO `metatag` VALUES (2,37,127,'title','dynamic','',0,'row',25,131,'','',2);
/*!40000 ALTER TABLE `metatag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `month`
--

DROP TABLE IF EXISTS `month`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `month`
--

LOCK TABLES `month` WRITE;
/*!40000 ALTER TABLE `month` DISABLE KEYS */;
/*!40000 ALTER TABLE `month` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notice`
--

DROP TABLE IF EXISTS `notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `type` enum('p','s') NOT NULL DEFAULT 'p',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `profileId` (`profileId`),
  KEY `toggle` (`toggle`),
  KEY `qtyDiffRelyOn` (`qtyDiffRelyOn`),
  KEY `sectionId` (`sectionId`),
  KEY `tplFor` (`tplFor`),
  KEY `type` (`type`),
  FULLTEXT KEY `tooltip` (`tooltip`),
  FULLTEXT KEY `tplIncSubj` (`tplIncSubj`),
  FULLTEXT KEY `tplIncBody` (`tplIncBody`),
  FULLTEXT KEY `tplDecSubj` (`tplDecSubj`),
  FULLTEXT KEY `tplDecBody` (`tplDecBody`),
  FULLTEXT KEY `tplEvtSubj` (`tplEvtSubj`),
  FULLTEXT KEY `tplEvtBody` (`tplEvtBody`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notice`
--

LOCK TABLES `notice` WRITE;
/*!40000 ALTER TABLE `notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticegetter`
--

DROP TABLE IF EXISTS `noticegetter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `noticeId` (`noticeId`),
  KEY `profileId` (`profileId`),
  KEY `criteriaRelyOn` (`criteriaRelyOn`),
  KEY `email` (`email`),
  KEY `vk` (`vk`),
  KEY `sms` (`sms`),
  KEY `mail` (`mail`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticegetter`
--

LOCK TABLES `noticegetter` WRITE;
/*!40000 ALTER TABLE `noticegetter` DISABLE KEYS */;
/*!40000 ALTER TABLE `noticegetter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `param`
--

DROP TABLE IF EXISTS `param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM AUTO_INCREMENT=168 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param`
--

LOCK TABLES `param` WRITE;
/*!40000 ALTER TABLE `param` DISABLE KEYS */;
INSERT INTO `param` VALUES (90,1487,13,'/css/style.css','Путь к css-нику для подцепки редактором'),(134,1814,5,'true','Во всю ширину'),(102,1487,5,'true','Во всю ширину'),(127,109,4,'px','Единица измерения'),(128,110,4,'px','Единица измерения'),(160,19,21,'system','Группировка опций по столбцу'),(156,18,21,'type','Группировка опций по столбцу'),(162,2315,4,'px','Единица измерения'),(159,12,21,'system','Группировка опций по столбцу'),(161,2196,21,'group','Группировка опций по столбцу'),(163,2385,29,'Без изменений','Плейсхолдер'),(164,2401,28,'H:i:s','Отображаемый формат времени'),(165,2401,27,'Y-m-d','Отображаемый формат даты'),(166,1443,24,'storeRelationAbility','Дополнительно передавать параметры (в виде атрибутов)'),(167,34,24,'storeRelationAbility','Дополнительно передавать параметры (в виде атрибутов)');
/*!40000 ALTER TABLE `param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `possibleelementparam`
--

DROP TABLE IF EXISTS `possibleelementparam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `possibleelementparam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `possibleelementparam`
--

LOCK TABLES `possibleelementparam` WRITE;
/*!40000 ALTER TABLE `possibleelementparam` DISABLE KEYS */;
INSERT INTO `possibleelementparam` VALUES (3,18,'Максимальная длина в символах','maxlength','5'),(4,18,'Единица измерения','measure',''),(5,13,'Во всю ширину','wide','0'),(6,7,'Количество столбцов','cols','1'),(7,13,'Высота в пикселях','height','200'),(28,19,'Отображаемый формат времени','displayTimeFormat','H:i'),(27,19,'Отображаемый формат даты','displayDateFormat','Y-m-d'),(26,12,'Отображаемый формат','displayFormat','Y-m-d'),(11,13,'Ширина в пикселях','width',''),(12,13,'Css класс для body','bodyClass',''),(13,13,'Путь к css-нику для подцепки редактором','contentsCss',''),(14,13,'Стили','style',''),(15,14,'Включать наименование поля в имя файла при download-е','appendFieldTitle','true'),(16,14,'Включать наименование сущности в имя файла при download-е','prependEntityTitle','true'),(17,13,'Путь к js-нику для подцепки редактором','contentsJs',''),(18,13,'Скрипт','script',''),(19,13,'Скрипт обработки исходного кода','sourceStripper',''),(20,18,'Только для чтения','readonly',''),(21,23,'Группировка опций по столбцу','groupBy',''),(22,23,'Шаблон содержимого опции','optionTemplate',''),(23,23,'Высота опции','optionHeight','14'),(24,23,'Дополнительно передавать параметры (в виде атрибутов)','optionAttrs',''),(25,23,'Отключить лукап','noLookup','false'),(29,23,'Плейсхолдер','placeholder',''),(30,1,'Только для чтения','readonly',''),(31,1,'Максимальная длина в символах','maxlength',''),(37,23,'Заголовочное поле','titleColumn',''),(32,23,'Во всю ширину','wide',''),(33,1,'Маска','inputMask',''),(34,1,'Vtype','vtype',''),(35,19,'When','when',''),(36,12,'When','when',''),(38,5,'Заголовочное поле','titleColumn',''),(39,7,'Заголовочное поле','titleColumn',''),(40,6,'Разрешенные теги','allowedTags',''),(41,14,'Допустимые типы','allowTypes',''),(42,1,'Шэйдинг','shade',''),(43,1,'Обновлять локализации для других языков','refreshL10nsOnUpdate',''),(44,6,'Обновлять локализации для других языков','refreshL10nsOnUpdate','');
/*!40000 ALTER TABLE `possibleelementparam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prod`
--

DROP TABLE IF EXISTS `prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(11,2) NOT NULL DEFAULT '0.00',
  `profileId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `profileId` (`profileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prod`
--

LOCK TABLES `prod` WRITE;
/*!40000 ALTER TABLE `prod` DISABLE KEYS */;
/*!40000 ALTER TABLE `prod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `entityId` int(11) NOT NULL DEFAULT '11',
  `dashboard` varchar(255) NOT NULL DEFAULT '',
  `move` int(11) NOT NULL DEFAULT '0',
  `maxWindows` int(11) NOT NULL DEFAULT '15',
  `demo` enum('n','y') NOT NULL DEFAULT 'n',
  `type` enum('p','s') NOT NULL DEFAULT 'p',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`),
  KEY `entityId` (`entityId`),
  KEY `demo` (`demo`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,'Конфигуратор','y',11,'',1,15,'n','s'),(12,'Администратор','y',11,'',12,15,'n','p');
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queuechunk`
--

DROP TABLE IF EXISTS `queuechunk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queuechunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queueTaskId` int(11) NOT NULL DEFAULT '0',
  `location` varchar(255) NOT NULL DEFAULT '',
  `where` text NOT NULL,
  `countState` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `countSize` int(11) NOT NULL DEFAULT '0',
  `itemsState` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `itemsSize` int(11) NOT NULL DEFAULT '0',
  `queueState` enum('waiting','progress','finished','noneed') NOT NULL DEFAULT 'waiting',
  `queueSize` int(11) NOT NULL DEFAULT '0',
  `applyState` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `applySize` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `queueChunkId` int(11) NOT NULL DEFAULT '0',
  `fraction` enum('none','adminSystemUi','adminCustomUi','adminCustomData') NOT NULL DEFAULT 'none',
  `itemsBytes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `queueTaskId` (`queueTaskId`),
  KEY `countState` (`countState`),
  KEY `itemsState` (`itemsState`),
  KEY `queueState` (`queueState`),
  KEY `applyState` (`applyState`),
  KEY `queueChunkId` (`queueChunkId`),
  KEY `fraction` (`fraction`),
  FULLTEXT KEY `where` (`where`)
) ENGINE=MyISAM AUTO_INCREMENT=493 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queuechunk`
--

LOCK TABLES `queuechunk` WRITE;
/*!40000 ALTER TABLE `queuechunk` DISABLE KEYS */;
INSERT INTO `queuechunk` VALUES (4,4,'profile:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,4,0,'none',0),(5,4,'noticeGetter:title','`profileId` = \"12\"','finished',0,'finished',0,'waiting',0,'waiting',0,5,4,'none',0),(6,5,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,6,0,'none',0),(7,5,'grid:title','`fieldId` = \"7\"','finished',2,'finished',2,'waiting',0,'waiting',0,7,6,'none',0),(8,5,'search:title','`fieldId` = \"7\"','finished',0,'finished',0,'waiting',0,'waiting',0,8,6,'none',0),(9,5,'alteredField:title','`fieldId` = \"7\"','finished',0,'finished',0,'waiting',0,'waiting',0,9,6,'none',0),(10,5,'consider:title','`consider` = \"7\"','finished',0,'finished',0,'waiting',0,'waiting',0,10,6,'none',0),(11,6,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,11,0,'none',0),(12,6,'grid:title','`fieldId` = \"8\"','finished',2,'finished',2,'waiting',0,'waiting',0,12,11,'none',0),(13,6,'search:title','`fieldId` = \"8\"','finished',0,'finished',0,'waiting',0,'waiting',0,13,11,'none',0),(14,6,'alteredField:title','`fieldId` = \"8\"','finished',0,'finished',0,'waiting',0,'waiting',0,14,11,'none',0),(15,6,'consider:title','`consider` = \"8\"','finished',0,'finished',0,'waiting',0,'waiting',0,15,11,'none',0),(16,7,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,16,0,'none',0),(17,7,'grid:title','`fieldId` = \"8\"','finished',2,'finished',2,'waiting',0,'waiting',0,17,16,'none',0),(18,7,'search:title','`fieldId` = \"8\"','finished',0,'finished',0,'waiting',0,'waiting',0,18,16,'none',0),(19,7,'alteredField:title','`fieldId` = \"8\"','finished',0,'finished',0,'waiting',0,'waiting',0,19,16,'none',0),(20,7,'consider:title','`consider` = \"8\"','finished',0,'finished',0,'waiting',0,'waiting',0,20,16,'none',0),(21,8,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,21,0,'none',0),(22,8,'grid:title','`fieldId` = \"470\"','finished',1,'finished',1,'waiting',0,'waiting',0,22,21,'none',0),(23,8,'search:title','`fieldId` = \"470\"','finished',0,'finished',0,'waiting',0,'waiting',0,23,21,'none',0),(24,8,'alteredField:title','`fieldId` = \"470\"','finished',0,'finished',0,'waiting',0,'waiting',0,24,21,'none',0),(25,8,'consider:title','`consider` = \"470\"','finished',1,'finished',1,'waiting',0,'waiting',0,25,21,'none',0),(26,9,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,26,0,'none',0),(27,9,'grid:title','`fieldId` = \"470\"','finished',1,'finished',1,'waiting',0,'waiting',0,27,26,'none',0),(28,9,'search:title','`fieldId` = \"470\"','finished',0,'finished',0,'waiting',0,'waiting',0,28,26,'none',0),(29,9,'alteredField:title','`fieldId` = \"470\"','finished',0,'finished',0,'waiting',0,'waiting',0,29,26,'none',0),(30,9,'consider:title','`consider` = \"470\"','finished',1,'finished',1,'waiting',0,'waiting',0,30,26,'none',0),(31,10,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,31,0,'none',0),(32,10,'grid:title','`fieldId` = \"14\"','finished',2,'finished',2,'waiting',0,'waiting',0,32,31,'none',0),(33,10,'search:title','`fieldId` = \"14\"','finished',0,'finished',0,'waiting',0,'waiting',0,33,31,'none',0),(34,10,'alteredField:title','`fieldId` = \"14\"','finished',0,'finished',0,'waiting',0,'waiting',0,34,31,'none',0),(35,10,'consider:title','`consider` = \"14\"','finished',0,'finished',0,'waiting',0,'waiting',0,35,31,'none',0),(36,11,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,36,0,'none',0),(37,11,'grid:title','`fieldId` = \"12\"','finished',2,'finished',2,'waiting',0,'waiting',0,37,36,'none',0),(38,11,'search:title','`fieldId` = \"12\"','finished',1,'finished',1,'waiting',0,'waiting',0,38,36,'none',0),(39,11,'alteredField:title','`fieldId` = \"12\"','finished',0,'finished',0,'waiting',0,'waiting',0,39,36,'none',0),(40,11,'consider:title','`consider` = \"12\"','finished',0,'finished',0,'waiting',0,'waiting',0,40,36,'none',0),(41,12,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,41,0,'none',0),(42,12,'grid:title','`fieldId` = \"11\"','finished',2,'finished',2,'waiting',0,'waiting',0,42,41,'none',0),(43,12,'search:title','`fieldId` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,43,41,'none',0),(44,12,'alteredField:title','`fieldId` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,44,41,'none',0),(45,12,'consider:title','`consider` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,45,41,'none',0),(46,13,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,46,0,'none',0),(47,13,'grid:alterTitle','`title` = \"7\"','finished',0,'finished',0,'waiting',0,'waiting',0,47,46,'none',0),(48,14,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,48,0,'none',0),(49,14,'grid:alterTitle','`title` = \"13\"','finished',0,'finished',0,'waiting',0,'waiting',0,49,48,'none',0),(50,15,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,50,0,'none',0),(51,15,'grid:alterTitle','`title` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,51,50,'none',0),(52,16,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,52,0,'none',0),(53,16,'grid:alterTitle','`title` = \"10\"','finished',0,'finished',0,'waiting',0,'waiting',0,53,52,'none',0),(54,17,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,54,0,'none',0),(55,17,'grid:title','`fieldId` = \"6\"','finished',1,'finished',1,'waiting',0,'waiting',0,55,54,'none',0),(56,17,'search:title','`fieldId` = \"6\"','finished',1,'finished',1,'waiting',0,'waiting',0,56,54,'none',0),(57,17,'alteredField:title','`fieldId` = \"6\"','finished',0,'finished',0,'waiting',0,'waiting',0,57,54,'none',0),(58,17,'consider:title','`consider` = \"6\"','finished',0,'finished',0,'waiting',0,'waiting',0,58,54,'none',0),(59,18,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,59,0,'none',0),(60,18,'grid:alterTitle','`title` = \"6\"','finished',0,'finished',0,'waiting',0,'waiting',0,60,59,'none',0),(61,19,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,61,0,'none',0),(62,19,'grid:title','`fieldId` = \"2414\"','finished',1,'finished',1,'waiting',0,'waiting',0,62,61,'none',0),(63,19,'search:title','`fieldId` = \"2414\"','finished',0,'finished',0,'waiting',0,'waiting',0,63,61,'none',0),(64,19,'alteredField:title','`fieldId` = \"2414\"','finished',0,'finished',0,'waiting',0,'waiting',0,64,61,'none',0),(65,19,'consider:title','`consider` = \"2414\"','finished',0,'finished',0,'waiting',0,'waiting',0,65,61,'none',0),(66,20,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,66,0,'none',0),(67,20,'grid:title','`fieldId` = \"2414\"','finished',1,'finished',1,'waiting',0,'waiting',0,67,66,'none',0),(68,20,'search:title','`fieldId` = \"2414\"','finished',0,'finished',0,'waiting',0,'waiting',0,68,66,'none',0),(69,20,'alteredField:title','`fieldId` = \"2414\"','finished',0,'finished',0,'waiting',0,'waiting',0,69,66,'none',0),(70,20,'consider:title','`consider` = \"2414\"','finished',0,'finished',0,'waiting',0,'waiting',0,70,66,'none',0),(71,21,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,71,0,'none',0),(72,21,'grid:alterTitle','`title` = \"2613\"','finished',0,'finished',0,'waiting',0,'waiting',0,72,71,'none',0),(73,22,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,73,0,'none',0),(74,22,'grid:title','`fieldId` = \"11\"','finished',2,'finished',2,'waiting',0,'waiting',0,74,73,'none',0),(75,22,'search:title','`fieldId` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,75,73,'none',0),(76,22,'alteredField:title','`fieldId` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,76,73,'none',0),(77,22,'consider:title','`consider` = \"11\"','finished',0,'finished',0,'waiting',0,'waiting',0,77,73,'none',0),(78,23,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,78,0,'none',0),(79,23,'grid:title','`fieldId` = \"555\"','finished',0,'finished',0,'waiting',0,'waiting',0,79,78,'none',0),(80,23,'search:title','`fieldId` = \"555\"','finished',0,'finished',0,'waiting',0,'waiting',0,80,78,'none',0),(81,23,'alteredField:title','`fieldId` = \"555\"','finished',0,'finished',0,'waiting',0,'waiting',0,81,78,'none',0),(82,23,'consider:title','`consider` = \"555\"','finished',0,'finished',0,'waiting',0,'waiting',0,82,78,'none',0),(83,24,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,83,0,'none',0),(84,24,'grid:title','`fieldId` = \"475\"','finished',0,'finished',0,'waiting',0,'waiting',0,84,83,'none',0),(85,24,'search:title','`fieldId` = \"475\"','finished',0,'finished',0,'waiting',0,'waiting',0,85,83,'none',0),(86,24,'alteredField:title','`fieldId` = \"475\"','finished',0,'finished',0,'waiting',0,'waiting',0,86,83,'none',0),(87,24,'consider:title','`consider` = \"475\"','finished',0,'finished',0,'waiting',0,'waiting',0,87,83,'none',0),(88,25,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,88,0,'none',0),(89,25,'grid:title','`fieldId` = \"3\"','finished',1,'finished',1,'waiting',0,'waiting',0,89,88,'none',0),(90,25,'search:title','`fieldId` = \"3\"','finished',0,'finished',0,'waiting',0,'waiting',0,90,88,'none',0),(91,25,'alteredField:title','`fieldId` = \"3\"','finished',0,'finished',0,'waiting',0,'waiting',0,91,88,'none',0),(92,25,'consider:title','`consider` = \"3\"','finished',0,'finished',0,'waiting',0,'waiting',0,92,88,'none',0),(93,26,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,93,0,'none',0),(94,26,'grid:title','`fieldId` = \"92\"','finished',0,'finished',0,'waiting',0,'waiting',0,94,93,'none',0),(95,26,'search:title','`fieldId` = \"92\"','finished',0,'finished',0,'waiting',0,'waiting',0,95,93,'none',0),(96,26,'alteredField:title','`fieldId` = \"92\"','finished',0,'finished',0,'waiting',0,'waiting',0,96,93,'none',0),(97,26,'consider:title','`consider` = \"92\"','finished',0,'finished',0,'waiting',0,'waiting',0,97,93,'none',0),(98,27,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,98,0,'none',0),(99,27,'grid:title','`fieldId` = \"66\"','finished',1,'finished',1,'waiting',0,'waiting',0,99,98,'none',0),(100,27,'search:title','`fieldId` = \"66\"','finished',0,'finished',0,'waiting',0,'waiting',0,100,98,'none',0),(101,27,'alteredField:title','`fieldId` = \"66\"','finished',0,'finished',0,'waiting',0,'waiting',0,101,98,'none',0),(102,27,'consider:title','`consider` = \"66\"','finished',0,'finished',0,'waiting',0,'waiting',0,102,98,'none',0),(103,28,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,103,0,'none',0),(104,28,'grid:title','`fieldId` = \"2131\"','finished',0,'finished',0,'waiting',0,'waiting',0,104,103,'none',0),(105,28,'search:title','`fieldId` = \"2131\"','finished',0,'finished',0,'waiting',0,'waiting',0,105,103,'none',0),(106,28,'alteredField:title','`fieldId` = \"2131\"','finished',0,'finished',0,'waiting',0,'waiting',0,106,103,'none',0),(107,28,'consider:title','`consider` = \"2131\"','finished',0,'finished',0,'waiting',0,'waiting',0,107,103,'none',0),(108,29,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,108,0,'none',0),(109,29,'grid:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,109,108,'none',0),(110,29,'search:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,110,108,'none',0),(111,29,'alteredField:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,111,108,'none',0),(112,29,'consider:title','`consider` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,112,108,'none',0),(113,30,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,113,0,'none',0),(114,30,'grid:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,114,113,'none',0),(115,30,'search:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,115,113,'none',0),(116,30,'alteredField:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,116,113,'none',0),(117,30,'consider:title','`consider` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,117,113,'none',0),(118,31,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,118,0,'none',0),(119,31,'grid:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,119,118,'none',0),(120,31,'search:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,120,118,'none',0),(121,31,'alteredField:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,121,118,'none',0),(122,31,'consider:title','`consider` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,122,118,'none',0),(123,32,'profile:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,123,0,'none',0),(124,32,'noticeGetter:title','`profileId` = \"12\"','finished',0,'finished',0,'waiting',0,'waiting',0,124,123,'none',0),(125,33,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,125,0,'none',0),(126,33,'grid:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,126,125,'none',0),(127,33,'search:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,127,125,'none',0),(128,33,'alteredField:title','`fieldId` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,128,125,'none',0),(129,33,'consider:title','`consider` = \"1337\"','finished',0,'finished',0,'waiting',0,'waiting',0,129,125,'none',0),(130,34,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,130,0,'none',0),(131,34,'grid:title','`fieldId` = \"40\"','finished',2,'finished',2,'waiting',0,'waiting',0,131,130,'none',0),(132,34,'search:title','`fieldId` = \"40\"','finished',0,'finished',0,'waiting',0,'waiting',0,132,130,'none',0),(133,34,'alteredField:title','`fieldId` = \"40\"','finished',0,'finished',0,'waiting',0,'waiting',0,133,130,'none',0),(134,34,'consider:title','`consider` = \"40\"','finished',0,'finished',0,'waiting',0,'waiting',0,134,130,'none',0),(135,35,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,135,0,'none',0),(136,35,'grid:title','`fieldId` = \"39\"','finished',2,'finished',2,'waiting',0,'waiting',0,136,135,'none',0),(137,35,'search:title','`fieldId` = \"39\"','finished',0,'finished',0,'waiting',0,'waiting',0,137,135,'none',0),(138,35,'alteredField:title','`fieldId` = \"39\"','finished',0,'finished',0,'waiting',0,'waiting',0,138,135,'none',0),(139,35,'consider:title','`consider` = \"39\"','finished',0,'finished',0,'waiting',0,'waiting',0,139,135,'none',0),(140,36,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,140,0,'none',0),(141,36,'grid:title','`fieldId` = \"38\"','finished',0,'finished',0,'waiting',0,'waiting',0,141,140,'none',0),(142,36,'search:title','`fieldId` = \"38\"','finished',0,'finished',0,'waiting',0,'waiting',0,142,140,'none',0),(143,36,'alteredField:title','`fieldId` = \"38\"','finished',0,'finished',0,'waiting',0,'waiting',0,143,140,'none',0),(144,36,'consider:title','`consider` = \"38\"','finished',0,'finished',0,'waiting',0,'waiting',0,144,140,'none',0),(145,37,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,145,0,'none',0),(146,37,'grid:alterTitle','`title` = \"2445\"','finished',0,'finished',0,'waiting',0,'waiting',0,146,145,'none',0),(147,38,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,147,0,'none',0),(148,38,'grid:alterTitle','`title` = \"2444\"','finished',0,'finished',0,'waiting',0,'waiting',0,148,147,'none',0),(149,39,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,149,0,'none',0),(150,39,'grid:alterTitle','`title` = \"2440\"','finished',0,'finished',0,'waiting',0,'waiting',0,150,149,'none',0),(151,40,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,151,0,'none',0),(152,40,'grid:alterTitle','`title` = \"2453\"','finished',0,'finished',0,'waiting',0,'waiting',0,152,151,'none',0),(153,41,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,153,0,'none',0),(154,41,'grid:alterTitle','`title` = \"2448\"','finished',0,'finished',0,'waiting',0,'waiting',0,154,153,'none',0),(155,42,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,155,0,'none',0),(156,42,'grid:alterTitle','`title` = \"2449\"','finished',0,'finished',0,'waiting',0,'waiting',0,156,155,'none',0),(157,43,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,157,0,'none',0),(158,43,'grid:alterTitle','`title` = \"2450\"','finished',0,'finished',0,'waiting',0,'waiting',0,158,157,'none',0),(159,44,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,159,0,'none',0),(160,44,'grid:alterTitle','`title` = \"2439\"','finished',0,'finished',0,'waiting',0,'waiting',0,160,159,'none',0),(161,45,'search:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,161,0,'none',0),(162,45,'search:alt','`title` = \"122\"','finished',0,'finished',0,'waiting',0,'waiting',0,162,161,'none',0),(163,46,'search:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,163,0,'none',0),(164,46,'search:alt','`title` = \"120\"','finished',0,'finished',0,'waiting',0,'waiting',0,164,163,'none',0),(165,47,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,165,0,'none',0),(166,47,'grid:title','`fieldId` = \"1658\"','finished',1,'finished',1,'waiting',0,'waiting',0,166,165,'none',0),(167,47,'search:title','`fieldId` = \"1658\"','finished',0,'finished',0,'waiting',0,'waiting',0,167,165,'none',0),(168,47,'alteredField:title','`fieldId` = \"1658\"','finished',0,'finished',0,'waiting',0,'waiting',0,168,165,'none',0),(169,47,'consider:title','`consider` = \"1658\"','finished',0,'finished',0,'waiting',0,'waiting',0,169,165,'none',0),(170,48,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,170,0,'none',0),(171,48,'grid:title','`fieldId` = \"2183\"','finished',1,'finished',1,'waiting',0,'waiting',0,171,170,'none',0),(172,48,'search:title','`fieldId` = \"2183\"','finished',0,'finished',0,'waiting',0,'waiting',0,172,170,'none',0),(173,48,'alteredField:title','`fieldId` = \"2183\"','finished',0,'finished',0,'waiting',0,'waiting',0,173,170,'none',0),(174,48,'consider:title','`consider` = \"2183\"','finished',0,'finished',0,'waiting',0,'waiting',0,174,170,'none',0),(175,49,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,175,0,'none',0),(176,49,'grid:title','`fieldId` = \"1443\"','finished',1,'finished',1,'waiting',0,'waiting',0,176,175,'none',0),(177,49,'search:title','`fieldId` = \"1443\"','finished',0,'finished',0,'waiting',0,'waiting',0,177,175,'none',0),(178,49,'alteredField:title','`fieldId` = \"1443\"','finished',0,'finished',0,'waiting',0,'waiting',0,178,175,'none',0),(179,49,'consider:title','`consider` = \"1443\"','finished',2,'finished',2,'waiting',0,'waiting',0,179,175,'none',0),(180,50,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,180,0,'none',0),(181,50,'grid:alterTitle','`title` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,181,180,'none',0),(182,51,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,182,0,'none',0),(183,51,'grid:alterTitle','`title` = \"1954\"','finished',0,'finished',0,'waiting',0,'waiting',0,183,182,'none',0),(184,52,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,184,0,'none',0),(185,52,'grid:title','`fieldId` = \"1444\"','finished',1,'finished',1,'waiting',0,'waiting',0,185,184,'none',0),(186,52,'search:title','`fieldId` = \"1444\"','finished',0,'finished',0,'waiting',0,'waiting',0,186,184,'none',0),(187,52,'alteredField:title','`fieldId` = \"1444\"','finished',0,'finished',0,'waiting',0,'waiting',0,187,184,'none',0),(188,52,'consider:title','`consider` = \"1444\"','finished',0,'finished',0,'waiting',0,'waiting',0,188,184,'none',0),(189,53,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,189,0,'none',0),(190,53,'grid:alterTitle','`title` = \"1383\"','finished',0,'finished',0,'waiting',0,'waiting',0,190,189,'none',0),(191,54,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,191,0,'none',0),(192,54,'grid:title','`fieldId` = \"2184\"','finished',1,'finished',1,'waiting',0,'waiting',0,192,191,'none',0),(193,54,'search:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,193,191,'none',0),(194,54,'alteredField:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,194,191,'none',0),(195,54,'consider:title','`consider` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,195,191,'none',0),(196,55,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,196,0,'none',0),(197,55,'grid:title','`fieldId` = \"2184\"','finished',1,'finished',1,'waiting',0,'waiting',0,197,196,'none',0),(198,55,'search:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,198,196,'none',0),(199,55,'alteredField:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,199,196,'none',0),(200,55,'consider:title','`consider` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,200,196,'none',0),(201,56,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,201,0,'none',0),(202,56,'grid:alterTitle','`title` = \"2626\"','finished',0,'finished',0,'waiting',0,'waiting',0,202,201,'none',0),(203,57,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,203,0,'none',0),(204,57,'grid:title','`fieldId` = \"2203\"','finished',1,'finished',1,'waiting',0,'waiting',0,204,203,'none',0),(205,57,'search:title','`fieldId` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,205,203,'none',0),(206,57,'alteredField:title','`fieldId` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,206,203,'none',0),(207,57,'consider:title','`consider` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,207,203,'none',0),(208,58,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,208,0,'none',0),(209,58,'grid:title','`fieldId` = \"2203\"','finished',1,'finished',1,'waiting',0,'waiting',0,209,208,'none',0),(210,58,'search:title','`fieldId` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,210,208,'none',0),(211,58,'alteredField:title','`fieldId` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,211,208,'none',0),(212,58,'consider:title','`consider` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,212,208,'none',0),(213,59,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,213,0,'none',0),(214,59,'grid:alterTitle','`title` = \"2622\"','finished',0,'finished',0,'waiting',0,'waiting',0,214,213,'none',0),(215,60,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,215,0,'none',0),(216,60,'grid:title','`fieldId` = \"2204\"','finished',1,'finished',1,'waiting',0,'waiting',0,216,215,'none',0),(217,60,'search:title','`fieldId` = \"2204\"','finished',0,'finished',0,'waiting',0,'waiting',0,217,215,'none',0),(218,60,'alteredField:title','`fieldId` = \"2204\"','finished',0,'finished',0,'waiting',0,'waiting',0,218,215,'none',0),(219,60,'consider:title','`consider` = \"2204\"','finished',0,'finished',0,'waiting',0,'waiting',0,219,215,'none',0),(220,61,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,220,0,'none',0),(221,61,'grid:title','`fieldId` = \"2203\"','finished',1,'finished',1,'waiting',0,'waiting',0,221,220,'none',0),(222,61,'search:title','`fieldId` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,222,220,'none',0),(223,61,'alteredField:title','`fieldId` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,223,220,'none',0),(224,61,'consider:title','`consider` = \"2203\"','finished',0,'finished',0,'waiting',0,'waiting',0,224,220,'none',0),(225,62,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,225,0,'none',0),(226,62,'grid:title','`fieldId` = \"2184\"','finished',1,'finished',1,'waiting',0,'waiting',0,226,225,'none',0),(227,62,'search:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,227,225,'none',0),(228,62,'alteredField:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,228,225,'none',0),(229,62,'consider:title','`consider` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,229,225,'none',0),(230,63,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,230,0,'none',0),(231,63,'grid:title','`fieldId` = \"2184\"','finished',1,'finished',1,'waiting',0,'waiting',0,231,230,'none',0),(232,63,'search:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,232,230,'none',0),(233,63,'alteredField:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,233,230,'none',0),(234,63,'consider:title','`consider` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,234,230,'none',0),(235,64,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,235,0,'none',0),(236,64,'grid:title','`fieldId` = \"2184\"','finished',1,'finished',1,'waiting',0,'waiting',0,236,235,'none',0),(237,64,'search:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,237,235,'none',0),(238,64,'alteredField:title','`fieldId` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,238,235,'none',0),(239,64,'consider:title','`consider` = \"2184\"','finished',0,'finished',0,'waiting',0,'waiting',0,239,235,'none',0),(240,65,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,240,0,'none',0),(241,65,'grid:alterTitle','`title` = \"1382\"','finished',0,'finished',0,'waiting',0,'waiting',0,241,240,'none',0),(242,66,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,242,0,'none',0),(243,66,'grid:title','`fieldId` = \"35\"','finished',1,'finished',1,'waiting',0,'waiting',0,243,242,'none',0),(244,66,'search:title','`fieldId` = \"35\"','finished',0,'finished',0,'waiting',0,'waiting',0,244,242,'none',0),(245,66,'alteredField:title','`fieldId` = \"35\"','finished',0,'finished',0,'waiting',0,'waiting',0,245,242,'none',0),(246,66,'consider:title','`consider` = \"35\"','finished',0,'finished',0,'waiting',0,'waiting',0,246,242,'none',0),(247,67,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,247,0,'none',0),(248,67,'grid:title','`fieldId` = \"1886\"','finished',1,'finished',1,'waiting',0,'waiting',0,248,247,'none',0),(249,67,'search:title','`fieldId` = \"1886\"','finished',0,'finished',0,'waiting',0,'waiting',0,249,247,'none',0),(250,67,'alteredField:title','`fieldId` = \"1886\"','finished',0,'finished',0,'waiting',0,'waiting',0,250,247,'none',0),(251,67,'consider:title','`consider` = \"1886\"','finished',0,'finished',0,'waiting',0,'waiting',0,251,247,'none',0),(252,68,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,252,0,'none',0),(253,68,'grid:title','`fieldId` = \"2198\"','finished',1,'finished',1,'waiting',0,'waiting',0,253,252,'none',0),(254,68,'search:title','`fieldId` = \"2198\"','finished',0,'finished',0,'waiting',0,'waiting',0,254,252,'none',0),(255,68,'alteredField:title','`fieldId` = \"2198\"','finished',0,'finished',0,'waiting',0,'waiting',0,255,252,'none',0),(256,68,'consider:title','`consider` = \"2198\"','finished',0,'finished',0,'waiting',0,'waiting',0,256,252,'none',0),(257,69,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,257,0,'none',0),(258,69,'grid:title','`fieldId` = \"377\"','finished',1,'finished',1,'waiting',0,'waiting',0,258,257,'none',0),(259,69,'search:title','`fieldId` = \"377\"','finished',0,'finished',0,'waiting',0,'waiting',0,259,257,'none',0),(260,69,'alteredField:title','`fieldId` = \"377\"','finished',0,'finished',0,'waiting',0,'waiting',0,260,257,'none',0),(261,69,'consider:title','`consider` = \"377\"','finished',0,'finished',0,'waiting',0,'waiting',0,261,257,'none',0),(262,70,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,262,0,'none',0),(263,70,'grid:alterTitle','`title` = \"1656\"','finished',0,'finished',0,'waiting',0,'waiting',0,263,262,'none',0),(264,71,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,264,0,'none',0),(265,71,'grid:title','`fieldId` = \"2206\"','finished',1,'finished',1,'waiting',0,'waiting',0,265,264,'none',0),(266,71,'search:title','`fieldId` = \"2206\"','finished',0,'finished',0,'waiting',0,'waiting',0,266,264,'none',0),(267,71,'alteredField:title','`fieldId` = \"2206\"','finished',0,'finished',0,'waiting',0,'waiting',0,267,264,'none',0),(268,71,'consider:title','`consider` = \"2206\"','finished',0,'finished',0,'waiting',0,'waiting',0,268,264,'none',0),(269,72,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,269,0,'none',0),(270,72,'grid:alterTitle','`title` = \"2635\"','finished',0,'finished',0,'waiting',0,'waiting',0,270,269,'none',0),(271,73,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,271,0,'none',0),(272,73,'grid:alterTitle','`title` = \"30\"','finished',0,'finished',0,'waiting',0,'waiting',0,272,271,'none',0),(273,74,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,273,0,'none',0),(274,74,'grid:title','`fieldId` = \"2251\"','finished',0,'finished',0,'waiting',0,'waiting',0,274,273,'none',0),(275,74,'search:title','`fieldId` = \"2251\"','finished',0,'finished',0,'waiting',0,'waiting',0,275,273,'none',0),(276,74,'alteredField:title','`fieldId` = \"2251\"','finished',0,'finished',0,'waiting',0,'waiting',0,276,273,'none',0),(277,74,'consider:title','`consider` = \"2251\"','finished',0,'finished',0,'waiting',0,'waiting',0,277,273,'none',0),(278,75,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,278,0,'none',0),(279,75,'grid:title','`fieldId` = \"2207\"','finished',1,'finished',1,'waiting',0,'waiting',0,279,278,'none',0),(280,75,'search:title','`fieldId` = \"2207\"','finished',0,'finished',0,'waiting',0,'waiting',0,280,278,'none',0),(281,75,'alteredField:title','`fieldId` = \"2207\"','finished',0,'finished',0,'waiting',0,'waiting',0,281,278,'none',0),(282,75,'consider:title','`consider` = \"2207\"','finished',0,'finished',0,'waiting',0,'waiting',0,282,278,'none',0),(283,76,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,283,0,'none',0),(284,76,'grid:title','`fieldId` = \"2250\"','finished',1,'finished',1,'waiting',0,'waiting',0,284,283,'none',0),(285,76,'search:title','`fieldId` = \"2250\"','finished',0,'finished',0,'waiting',0,'waiting',0,285,283,'none',0),(286,76,'alteredField:title','`fieldId` = \"2250\"','finished',0,'finished',0,'waiting',0,'waiting',0,286,283,'none',0),(287,76,'consider:title','`consider` = \"2250\"','finished',0,'finished',0,'waiting',0,'waiting',0,287,283,'none',0),(288,77,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,288,0,'none',0),(289,77,'grid:title','`fieldId` = \"2207\"','finished',1,'finished',1,'waiting',0,'waiting',0,289,288,'none',0),(290,77,'search:title','`fieldId` = \"2207\"','finished',0,'finished',0,'waiting',0,'waiting',0,290,288,'none',0),(291,77,'alteredField:title','`fieldId` = \"2207\"','finished',0,'finished',0,'waiting',0,'waiting',0,291,288,'none',0),(292,77,'consider:title','`consider` = \"2207\"','finished',0,'finished',0,'waiting',0,'waiting',0,292,288,'none',0),(293,78,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,293,0,'none',0),(294,78,'grid:alterTitle','`title` = \"2358\"','finished',0,'finished',0,'waiting',0,'waiting',0,294,293,'none',0),(295,79,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,295,0,'none',0),(296,79,'grid:title','`fieldId` = \"28\"','finished',1,'finished',1,'waiting',0,'waiting',0,296,295,'none',0),(297,79,'search:title','`fieldId` = \"28\"','finished',0,'finished',0,'waiting',0,'waiting',0,297,295,'none',0),(298,79,'alteredField:title','`fieldId` = \"28\"','finished',0,'finished',0,'waiting',0,'waiting',0,298,295,'none',0),(299,79,'consider:title','`consider` = \"28\"','finished',0,'finished',0,'waiting',0,'waiting',0,299,295,'none',0),(300,80,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,300,0,'none',0),(301,80,'grid:title','`fieldId` = \"30\"','finished',1,'finished',1,'waiting',0,'waiting',0,301,300,'none',0),(302,80,'search:title','`fieldId` = \"30\"','finished',0,'finished',0,'waiting',0,'waiting',0,302,300,'none',0),(303,80,'alteredField:title','`fieldId` = \"30\"','finished',0,'finished',0,'waiting',0,'waiting',0,303,300,'none',0),(304,80,'consider:title','`consider` = \"30\"','finished',0,'finished',0,'waiting',0,'waiting',0,304,300,'none',0),(305,81,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,305,0,'none',0),(306,81,'grid:alterTitle','`title` = \"383\"','finished',0,'finished',0,'waiting',0,'waiting',0,306,305,'none',0),(307,82,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,307,0,'none',0),(308,82,'grid:alterTitle','`title` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,308,307,'none',0),(309,83,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,309,0,'none',0),(310,83,'grid:title','`fieldId` = \"2212\"','finished',1,'finished',1,'waiting',0,'waiting',0,310,309,'none',0),(311,83,'search:title','`fieldId` = \"2212\"','finished',0,'finished',0,'waiting',0,'waiting',0,311,309,'none',0),(312,83,'alteredField:title','`fieldId` = \"2212\"','finished',0,'finished',0,'waiting',0,'waiting',0,312,309,'none',0),(313,83,'consider:title','`consider` = \"2212\"','finished',0,'finished',0,'waiting',0,'waiting',0,313,309,'none',0),(314,84,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,314,0,'none',0),(315,84,'grid:alterTitle','`title` = \"2326\"','finished',0,'finished',0,'waiting',0,'waiting',0,315,314,'none',0),(316,85,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,316,0,'none',0),(317,85,'grid:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,317,316,'none',0),(318,85,'search:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,318,316,'none',0),(319,85,'alteredField:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,319,316,'none',0),(320,85,'consider:title','`consider` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,320,316,'none',0),(321,86,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,321,0,'none',0),(322,86,'grid:title','`fieldId` = \"2309\"','finished',1,'finished',1,'waiting',0,'waiting',0,322,321,'none',0),(323,86,'search:title','`fieldId` = \"2309\"','finished',0,'finished',0,'waiting',0,'waiting',0,323,321,'none',0),(324,86,'alteredField:title','`fieldId` = \"2309\"','finished',0,'finished',0,'waiting',0,'waiting',0,324,321,'none',0),(325,86,'consider:title','`consider` = \"2309\"','finished',0,'finished',0,'waiting',0,'waiting',0,325,321,'none',0),(326,87,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,326,0,'none',0),(327,87,'grid:title','`fieldId` = \"502\"','finished',1,'finished',1,'waiting',0,'waiting',0,327,326,'none',0),(328,87,'search:title','`fieldId` = \"502\"','finished',0,'finished',0,'waiting',0,'waiting',0,328,326,'none',0),(329,87,'alteredField:title','`fieldId` = \"502\"','finished',0,'finished',0,'waiting',0,'waiting',0,329,326,'none',0),(330,87,'consider:title','`consider` = \"502\"','finished',0,'finished',0,'waiting',0,'waiting',0,330,326,'none',0),(331,88,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,331,0,'none',0),(332,88,'grid:title','`fieldId` = \"18\"','finished',0,'finished',0,'waiting',0,'waiting',0,332,331,'none',0),(333,88,'search:title','`fieldId` = \"18\"','finished',0,'finished',0,'waiting',0,'waiting',0,333,331,'none',0),(334,88,'alteredField:title','`fieldId` = \"18\"','finished',0,'finished',0,'waiting',0,'waiting',0,334,331,'none',0),(335,88,'consider:title','`consider` = \"18\"','finished',0,'finished',0,'waiting',0,'waiting',0,335,331,'none',0),(336,89,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,336,0,'none',0),(337,89,'grid:title','`fieldId` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,337,336,'none',0),(338,89,'search:title','`fieldId` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,338,336,'none',0),(339,89,'alteredField:title','`fieldId` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,339,336,'none',0),(340,89,'consider:title','`consider` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,340,336,'none',0),(341,90,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,341,0,'none',0),(342,90,'grid:title','`fieldId` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,342,341,'none',0),(343,90,'search:title','`fieldId` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,343,341,'none',0),(344,90,'alteredField:title','`fieldId` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,344,341,'none',0),(345,90,'consider:title','`consider` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,345,341,'none',0),(346,91,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,346,0,'none',0),(347,91,'grid:title','`fieldId` = \"2213\"','finished',1,'finished',1,'waiting',0,'waiting',0,347,346,'none',0),(348,91,'search:title','`fieldId` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,348,346,'none',0),(349,91,'alteredField:title','`fieldId` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,349,346,'none',0),(350,91,'consider:title','`consider` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,350,346,'none',0),(351,92,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,351,0,'none',0),(352,92,'grid:title','`fieldId` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,352,351,'none',0),(353,92,'search:title','`fieldId` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,353,351,'none',0),(354,92,'alteredField:title','`fieldId` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,354,351,'none',0),(355,92,'consider:title','`consider` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,355,351,'none',0),(356,93,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,356,0,'none',0),(357,93,'grid:title','`fieldId` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,357,356,'none',0),(358,93,'search:title','`fieldId` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,358,356,'none',0),(359,93,'alteredField:title','`fieldId` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,359,356,'none',0),(360,93,'consider:title','`consider` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,360,356,'none',0),(361,94,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,361,0,'none',0),(362,94,'grid:title','`fieldId` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,362,361,'none',0),(363,94,'search:title','`fieldId` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,363,361,'none',0),(364,94,'alteredField:title','`fieldId` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,364,361,'none',0),(365,94,'consider:title','`consider` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,365,361,'none',0),(366,95,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,366,0,'none',0),(367,95,'grid:title','`fieldId` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,367,366,'none',0),(368,95,'search:title','`fieldId` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,368,366,'none',0),(369,95,'alteredField:title','`fieldId` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,369,366,'none',0),(370,95,'consider:title','`consider` = \"2426\"','finished',0,'finished',0,'waiting',0,'waiting',0,370,366,'none',0),(371,96,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,371,0,'none',0),(372,96,'grid:title','`fieldId` = \"2213\"','finished',1,'finished',1,'waiting',0,'waiting',0,372,371,'none',0),(373,96,'search:title','`fieldId` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,373,371,'none',0),(374,96,'alteredField:title','`fieldId` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,374,371,'none',0),(375,96,'consider:title','`consider` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,375,371,'none',0),(376,97,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,376,0,'none',0),(377,97,'grid:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,377,376,'none',0),(378,97,'search:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,378,376,'none',0),(379,97,'alteredField:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,379,376,'none',0),(380,97,'consider:title','`consider` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,380,376,'none',0),(381,98,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,381,0,'none',0),(382,98,'grid:title','`fieldId` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,382,381,'none',0),(383,98,'search:title','`fieldId` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,383,381,'none',0),(384,98,'alteredField:title','`fieldId` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,384,381,'none',0),(385,98,'consider:title','`consider` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,385,381,'none',0),(386,99,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,386,0,'none',0),(387,99,'grid:title','`fieldId` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,387,386,'none',0),(388,99,'search:title','`fieldId` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,388,386,'none',0),(389,99,'alteredField:title','`fieldId` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,389,386,'none',0),(390,99,'consider:title','`consider` = \"2427\"','finished',0,'finished',0,'waiting',0,'waiting',0,390,386,'none',0),(391,100,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,391,0,'none',0),(392,100,'grid:title','`fieldId` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,392,391,'none',0),(393,100,'search:title','`fieldId` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,393,391,'none',0),(394,100,'alteredField:title','`fieldId` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,394,391,'none',0),(395,100,'consider:title','`consider` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,395,391,'none',0),(396,101,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,396,0,'none',0),(397,101,'grid:title','`fieldId` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,397,396,'none',0),(398,101,'search:title','`fieldId` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,398,396,'none',0),(399,101,'alteredField:title','`fieldId` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,399,396,'none',0),(400,101,'consider:title','`consider` = \"1554\"','finished',0,'finished',0,'waiting',0,'waiting',0,400,396,'none',0),(401,102,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,401,0,'none',0),(402,102,'grid:alterTitle','`title` = \"2327\"','finished',0,'finished',0,'waiting',0,'waiting',0,402,401,'none',0),(403,103,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,403,0,'none',0),(404,103,'grid:title','`fieldId` = \"2213\"','finished',1,'finished',1,'waiting',0,'waiting',0,404,403,'none',0),(405,103,'search:title','`fieldId` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,405,403,'none',0),(406,103,'alteredField:title','`fieldId` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,406,403,'none',0),(407,103,'consider:title','`consider` = \"2213\"','finished',0,'finished',0,'waiting',0,'waiting',0,407,403,'none',0),(408,104,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,408,0,'none',0),(409,104,'grid:alterTitle','`title` = \"2460\"','finished',0,'finished',0,'waiting',0,'waiting',0,409,408,'none',0),(410,105,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,410,0,'none',0),(411,105,'grid:alterTitle','`title` = \"2459\"','finished',0,'finished',0,'waiting',0,'waiting',0,411,410,'none',0),(412,106,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,412,0,'none',0),(413,106,'grid:alterTitle','`title` = \"2327\"','finished',0,'finished',0,'waiting',0,'waiting',0,413,412,'none',0),(414,107,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,414,0,'none',0),(415,107,'grid:title','`fieldId` = \"23\"','finished',1,'finished',1,'waiting',0,'waiting',0,415,414,'none',0),(416,107,'search:title','`fieldId` = \"23\"','finished',0,'finished',0,'waiting',0,'waiting',0,416,414,'none',0),(417,107,'alteredField:title','`fieldId` = \"23\"','finished',0,'finished',0,'waiting',0,'waiting',0,417,414,'none',0),(418,107,'consider:title','`consider` = \"23\"','finished',0,'finished',0,'waiting',0,'waiting',0,418,414,'none',0),(419,108,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,419,0,'none',0),(420,108,'grid:alterTitle','`title` = \"18\"','finished',0,'finished',0,'waiting',0,'waiting',0,420,419,'none',0),(421,109,'field:tooltip','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,421,0,'none',0),(422,109,'grid:tooltip','`fieldId` = \"2309\"','finished',1,'finished',1,'waiting',0,'waiting',0,422,421,'none',0),(423,110,'field:tooltip','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,423,0,'none',0),(424,110,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'waiting',0,'waiting',0,424,423,'none',0),(425,111,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,425,0,'none',0),(426,111,'grid:title','`fieldId` = \"1366\"','finished',1,'finished',1,'waiting',0,'waiting',0,426,425,'none',0),(427,111,'search:title','`fieldId` = \"1366\"','finished',1,'finished',1,'waiting',0,'waiting',0,427,425,'none',0),(428,111,'alteredField:title','`fieldId` = \"1366\"','finished',0,'finished',0,'waiting',0,'waiting',0,428,425,'none',0),(429,111,'consider:title','`consider` = \"1366\"','finished',0,'finished',0,'waiting',0,'waiting',0,429,425,'none',0),(430,112,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,430,0,'none',0),(431,112,'grid:alterTitle','`title` = \"1335\"','finished',0,'finished',0,'waiting',0,'waiting',0,431,430,'none',0),(432,113,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,432,0,'none',0),(433,113,'grid:title','`fieldId` = \"1345\"','finished',1,'finished',1,'waiting',0,'waiting',0,433,432,'none',0),(434,113,'search:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,434,432,'none',0),(435,113,'alteredField:title','`fieldId` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,435,432,'none',0),(436,113,'consider:title','`consider` = \"1345\"','finished',0,'finished',0,'waiting',0,'waiting',0,436,432,'none',0),(437,114,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,437,0,'none',0),(438,114,'grid:title','`fieldId` = \"557\"','finished',1,'finished',1,'waiting',0,'waiting',0,438,437,'none',0),(439,114,'search:title','`fieldId` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,439,437,'none',0),(440,114,'alteredField:title','`fieldId` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,440,437,'none',0),(441,114,'consider:title','`consider` = \"557\"','finished',0,'finished',0,'waiting',0,'waiting',0,441,437,'none',0),(442,115,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,442,0,'none',0),(443,115,'grid:alterTitle','`title` = \"2646\"','finished',0,'finished',0,'waiting',0,'waiting',0,443,442,'none',0),(444,116,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,444,0,'none',0),(445,116,'grid:alterTitle','`title` = \"2643\"','finished',0,'finished',0,'waiting',0,'waiting',0,445,444,'none',0),(446,117,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,446,0,'none',0),(447,117,'grid:title','`fieldId` = \"25\"','finished',1,'finished',1,'waiting',0,'waiting',0,447,446,'none',0),(448,117,'search:title','`fieldId` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,448,446,'none',0),(449,117,'alteredField:title','`fieldId` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,449,446,'none',0),(450,117,'consider:title','`consider` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,450,446,'none',0),(451,118,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,451,0,'none',0),(452,118,'grid:title','`fieldId` = \"25\"','finished',1,'finished',1,'waiting',0,'waiting',0,452,451,'none',0),(453,118,'search:title','`fieldId` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,453,451,'none',0),(454,118,'alteredField:title','`fieldId` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,454,451,'none',0),(455,118,'consider:title','`consider` = \"25\"','finished',0,'finished',0,'waiting',0,'waiting',0,455,451,'none',0),(456,119,'grid:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,456,0,'none',0),(457,119,'grid:alterTitle','`title` = \"20\"','finished',0,'finished',0,'waiting',0,'waiting',0,457,456,'none',0),(458,120,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,458,0,'none',0),(459,120,'grid:title','`fieldId` = \"2312\"','finished',1,'finished',1,'waiting',0,'waiting',0,459,458,'none',0),(460,120,'search:title','`fieldId` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,460,458,'none',0),(461,120,'alteredField:title','`fieldId` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,461,458,'none',0),(462,120,'consider:title','`consider` = \"2312\"','finished',0,'finished',0,'waiting',0,'waiting',0,462,458,'none',0),(463,121,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,463,0,'none',0),(464,121,'grid:title','`fieldId` = \"2429\"','finished',1,'finished',1,'waiting',0,'waiting',0,464,463,'none',0),(465,121,'search:title','`fieldId` = \"2429\"','finished',0,'finished',0,'waiting',0,'waiting',0,465,463,'none',0),(466,121,'alteredField:title','`fieldId` = \"2429\"','finished',0,'finished',0,'waiting',0,'waiting',0,466,463,'none',0),(467,121,'consider:title','`consider` = \"2429\"','finished',0,'finished',0,'waiting',0,'waiting',0,467,463,'none',0),(468,122,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,468,0,'none',0),(469,122,'grid:title','`fieldId` = \"2310\"','finished',1,'finished',1,'waiting',0,'waiting',0,469,468,'none',0),(470,122,'search:title','`fieldId` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,470,468,'none',0),(471,122,'alteredField:title','`fieldId` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,471,468,'none',0),(472,122,'consider:title','`consider` = \"2310\"','finished',0,'finished',0,'waiting',0,'waiting',0,472,468,'none',0),(473,123,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,473,0,'none',0),(474,123,'grid:title','`fieldId` = \"2132\"','finished',1,'finished',1,'waiting',0,'waiting',0,474,473,'none',0),(475,123,'search:title','`fieldId` = \"2132\"','finished',0,'finished',0,'waiting',0,'waiting',0,475,473,'none',0),(476,123,'alteredField:title','`fieldId` = \"2132\"','finished',0,'finished',0,'waiting',0,'waiting',0,476,473,'none',0),(477,123,'consider:title','`consider` = \"2132\"','finished',0,'finished',0,'waiting',0,'waiting',0,477,473,'none',0),(478,124,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,478,0,'none',0),(479,124,'grid:title','`fieldId` = \"26\"','finished',0,'finished',0,'waiting',0,'waiting',0,479,478,'none',0),(480,124,'search:title','`fieldId` = \"26\"','finished',0,'finished',0,'waiting',0,'waiting',0,480,478,'none',0),(481,124,'alteredField:title','`fieldId` = \"26\"','finished',0,'finished',0,'waiting',0,'waiting',0,481,478,'none',0),(482,124,'consider:title','`consider` = \"26\"','finished',0,'finished',0,'waiting',0,'waiting',0,482,478,'none',0),(483,125,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,483,0,'none',0),(484,125,'grid:title','`fieldId` = \"612\"','finished',1,'finished',1,'waiting',0,'waiting',0,484,483,'none',0),(485,125,'search:title','`fieldId` = \"612\"','finished',1,'finished',1,'waiting',0,'waiting',0,485,483,'none',0),(486,125,'alteredField:title','`fieldId` = \"612\"','finished',0,'finished',0,'waiting',0,'waiting',0,486,483,'none',0),(487,125,'consider:title','`consider` = \"612\"','finished',0,'finished',0,'waiting',0,'waiting',0,487,483,'none',0),(488,126,'field:title','FALSE','finished',0,'finished',0,'waiting',0,'waiting',0,488,0,'none',0),(489,126,'grid:title','`fieldId` = \"767\"','finished',1,'finished',1,'waiting',0,'waiting',0,489,488,'none',0),(490,126,'search:title','`fieldId` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,490,488,'none',0),(491,126,'alteredField:title','`fieldId` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,491,488,'none',0),(492,126,'consider:title','`consider` = \"767\"','finished',0,'finished',0,'waiting',0,'waiting',0,492,488,'none',0);
/*!40000 ALTER TABLE `queuechunk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queueitem`
--

DROP TABLE IF EXISTS `queueitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queueitem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queueTaskId` int(11) NOT NULL DEFAULT '0',
  `queueChunkId` int(11) NOT NULL DEFAULT '0',
  `target` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `result` text NOT NULL,
  `stage` enum('items','queue','apply') NOT NULL DEFAULT 'items',
  PRIMARY KEY (`id`),
  KEY `queueTaskId` (`queueTaskId`),
  KEY `queueChunkId` (`queueChunkId`),
  KEY `stage` (`stage`),
  FULLTEXT KEY `value` (`value`),
  FULLTEXT KEY `result` (`result`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queueitem`
--

LOCK TABLES `queueitem` WRITE;
/*!40000 ALTER TABLE `queueitem` DISABLE KEYS */;
INSERT INTO `queueitem` VALUES (1,5,7,'6','Наименование поля','','items'),(2,5,7,'2439','Наименование поля','','items'),(3,6,12,'7','Наименование соответствующего полю столбца в  таблице БД','','items'),(4,6,12,'2445','Наименование соответствующего полю столбца в  таблице БД','','items'),(5,7,17,'7','Наименование соответствующего полю столбца в  таблице БД','','items'),(6,7,17,'2445','Наименование соответствующего полю столбца в  таблице БД','','items'),(7,8,22,'2449','Предназначено для хранения ключей','','items'),(8,8,25,'3','Предназначено для хранения ключей','','items'),(9,9,27,'2449','Предназначено для хранения ключей','','items'),(10,9,30,'3','Предназначено для хранения ключей','','items'),(11,10,32,'13','Положение в списке','','items'),(12,10,32,'2453','Положение в списке','','items'),(13,11,37,'11','Ключи какой сущности будут храниться в этом поле','','items'),(14,11,37,'2450','Ключи какой сущности будут храниться в этом поле','','items'),(15,11,38,'122','Ключи какой сущности будут храниться в этом поле','','items'),(16,12,42,'10','Значение по умолчанию','','items'),(17,12,42,'2447','Значение по умолчанию','','items'),(18,17,55,'2438','Сущность, в структуру которой входит это поле','','items'),(19,17,56,'120','Сущность, в структуру которой входит это поле','','items'),(20,19,62,'2613','Элемент','','items'),(21,20,67,'2613','Элемент','','items'),(22,22,74,'10','DEFAULT','','items'),(23,22,74,'2447','Значение по умолчанию','','items'),(24,25,89,'3','Пригоден для хранения внешних ключей','','items'),(25,27,99,'43','Способен работать с внешними ключами','','items'),(26,34,131,'37','Email (используется в качестве логина)','','items'),(27,34,131,'2566','Email (используется в качестве логина)','','items'),(28,35,136,'36','Фамилия Имя','','items'),(29,35,136,'2565','Фамилия Имя','','items'),(30,47,166,'1954','Альтернативное наименование','','items'),(31,48,171,'2312','Статическая фильтрация','','items'),(32,49,176,'1383','Поле прикрепленной к разделу сущности','','items'),(33,49,179,'24','Поле прикрепленной к разделу сущности','','items'),(34,49,179,'37','Поле прикрепленной к разделу сущности','','items'),(35,52,185,'1382','Порядок отображения','','items'),(36,54,192,'2626','Игнорировать optionTemplate','','items'),(37,55,197,'2626','Игнорировать optionTemplate','','items'),(38,57,204,'2622','Доступ','','items'),(39,58,209,'2622','Доступ','','items'),(40,60,216,'2623','Выбранные','','items'),(41,61,221,'2622','Кому','','items'),(42,62,226,'2626','Игнорировать шаблон, заданный в параметрах настроек поля','','items'),(43,63,231,'2626','Игнорировать шаблон, заданный в параметрах настроек поля','','items'),(44,64,236,'2626','Игнорировать шаблон, заданный в параметрах настроек поля','','items'),(45,66,243,'30','Очередность отображения столбца в гриде','','items'),(46,67,248,'1656','Изменить название столбца на','','items'),(47,68,253,'2627','Содержательность','','items'),(48,69,258,'341','Порядок отображения','','items'),(49,71,265,'2635','Выбранные','','items'),(50,75,279,'2357','Влияние','','items'),(51,76,284,'2358','Выбранные','','items'),(52,77,289,'2357','Влияние','','items'),(53,79,296,'383','Профили пользователей, имеющих доступ к этому действию в этом разделе','','items'),(54,80,301,'25','Положение в списке','','items'),(55,83,310,'2326','South-панель','','items'),(56,86,322,'2460','Родительский класс JS','','items'),(57,87,327,'2459','Родительский класс PHP','','items'),(58,91,347,'2327','Режим подгрузки данных','','items'),(59,96,372,'2327','Режим подгрузки данных','','items'),(60,103,404,'2327','Подгрузка','','items'),(61,107,415,'18','Положение в списке','','items'),(62,109,422,'2460','','','items'),(63,110,424,'2459','','','items'),(64,111,426,'1335','Тип','','items'),(65,111,427,'116','Тип','','items'),(66,113,433,'2643','Запретить создание новых','','items'),(67,114,438,'2646','Направление','','items'),(68,117,447,'20','Записей на странице','','items'),(69,118,452,'20','Записей на странице','','items'),(70,120,459,'2648','Выделение более одной','','items'),(71,121,464,'2654','Подгрузка','','items'),(72,122,469,'2649','Включить нумерацию','','items'),(73,123,474,'2617','Порядок отображения','','items'),(74,125,484,'443','Тип','','items'),(75,125,485,'13','Тип','','items'),(76,126,489,'2644','Фильтрация','','items');
/*!40000 ALTER TABLE `queueitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queuetask`
--

DROP TABLE IF EXISTS `queuetask`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queuetask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text NOT NULL,
  `procSince` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `procID` int(11) NOT NULL DEFAULT '0',
  `stage` enum('count','items','queue','apply') NOT NULL DEFAULT 'count',
  `state` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `chunk` int(11) NOT NULL DEFAULT '0',
  `countState` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `countSize` int(11) NOT NULL DEFAULT '0',
  `itemsState` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `itemsSize` int(11) NOT NULL DEFAULT '0',
  `itemsBytes` int(11) NOT NULL DEFAULT '0',
  `queueState` enum('waiting','progress','finished','noneed') NOT NULL DEFAULT 'waiting',
  `queueSize` int(11) NOT NULL DEFAULT '0',
  `applyState` enum('waiting','progress','finished') NOT NULL DEFAULT 'waiting',
  `applySize` int(11) NOT NULL DEFAULT '0',
  `stageState` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stage` (`stage`),
  KEY `state` (`state`),
  KEY `countState` (`countState`),
  KEY `itemsState` (`itemsState`),
  KEY `queueState` (`queueState`),
  KEY `applyState` (`applyState`),
  FULLTEXT KEY `params` (`params`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queuetask`
--

LOCK TABLES `queuetask` WRITE;
/*!40000 ALTER TABLE `queuetask` DISABLE KEYS */;
INSERT INTO `queuetask` VALUES (4,'UsagesUpdate','2020-11-28 20:08:31','{\"source\":\"ru\",\"fraction\":\"adminCustomUi\",\"table\":\"profile\",\"entry\":\"12\",\"affected\":{\"title\":\"Клиент\"},\"considerIdA\":[\"40\"]}','2020-11-28 20:08:31',22884,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(5,'UsagesUpdate','2021-01-21 19:20:13','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"7\",\"affected\":{\"title\":\"Наименование\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:20:13',14996,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(6,'UsagesUpdate','2021-01-21 19:22:51','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"8\",\"affected\":{\"title\":\"Наименование\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:22:51',20880,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(7,'UsagesUpdate','2021-01-21 19:25:00','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"8\",\"affected\":{\"title\":\"Псевдоним\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:25:00',2036,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(8,'UsagesUpdate','2021-01-21 19:27:09','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"470\",\"affected\":{\"title\":\"Ключи\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:27:09',14692,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(9,'UsagesUpdate','2021-01-21 19:27:36','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"470\",\"affected\":{\"title\":\"Хранит ключи\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:27:36',8704,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(10,'UsagesUpdate','2021-01-21 19:31:33','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"14\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:31:33',15428,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(11,'UsagesUpdate','2021-01-21 19:31:50','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"12\",\"affected\":{\"title\":\"Ключи какой сущности\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 19:31:50',21128,'items','finished',5,'finished',3,'finished',3,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(12,'UsagesUpdate','2021-01-21 20:16:39','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"11\",\"affected\":{\"title\":\"DEFAULT\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 20:16:39',10836,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(13,'UsagesUpdate','2021-01-21 20:22:58','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"7\",\"affected\":{\"title\":\"Псевдоним\",\"gridId\":\"2609\"},\"considerIdA\":[\"27\"]}','2021-01-21 20:22:58',14056,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(14,'UsagesUpdate','2021-01-21 20:23:05','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"13\",\"affected\":{\"move\":\"2352\",\"title\":\"Порядок\"},\"considerIdA\":[\"27\"]}','2021-01-21 20:23:05',5372,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(15,'UsagesUpdate','2021-01-21 20:23:06','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"11\",\"affected\":{\"move\":\"2321\",\"title\":\"Ключи какой сущности\"},\"considerIdA\":[\"27\"]}','2021-01-21 20:23:06',13360,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(16,'UsagesUpdate','2021-01-21 20:23:07','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"10\",\"affected\":{\"move\":\"13\",\"title\":\"DEFAULT\"},\"considerIdA\":[\"27\"]}','2021-01-21 20:23:07',5596,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(17,'UsagesUpdate','2021-01-21 20:25:11','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"6\",\"affected\":{\"title\":\"Сущность\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 20:25:11',15216,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(18,'UsagesUpdate','2021-01-21 20:54:37','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"6\",\"affected\":{\"title\":\"Наименование\",\"editor\":\"1\"},\"considerIdA\":[\"27\"]}','2021-01-21 20:54:38',13028,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(19,'UsagesUpdate','2021-01-21 21:15:02','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2414\",\"affected\":{\"title\":\"Элемент умравления\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 21:15:02',20344,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(20,'UsagesUpdate','2021-01-21 21:15:08','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2414\",\"affected\":{\"title\":\"Элемент управления\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-21 21:15:08',13048,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(21,'UsagesUpdate','2021-01-21 21:15:37','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2613\",\"affected\":{\"alterTitle\":\"Элемент управления\",\"title\":\"Элемент управления\"},\"considerIdA\":[\"27\"]}','2021-01-21 21:15:38',9112,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(22,'UsagesUpdate','2021-01-22 01:15:44','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"11\",\"affected\":{\"title\":\"Значение по умолчанию\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 01:15:44',8352,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(23,'UsagesUpdate','2021-01-22 01:30:02','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"555\",\"affected\":{\"title\":\"Родительский класс PHP\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 01:30:03',4164,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(24,'UsagesUpdate','2021-01-22 01:51:58','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"475\",\"affected\":{\"title\":\"Совместимые элементы управления\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 01:51:59',11272,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(25,'UsagesUpdate','2021-01-22 01:52:17','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"3\",\"affected\":{\"title\":\"Можно хранить ключи\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 01:52:18',10552,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(26,'UsagesUpdate','2021-01-22 01:55:12','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"92\",\"affected\":{\"title\":\"Не отображать в формах\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 01:55:13',13508,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(27,'UsagesUpdate','2021-01-22 01:55:40','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"66\",\"affected\":{\"title\":\"Совместимость с внешними ключами\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 01:55:41',13092,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(28,'UsagesUpdate','2021-01-22 02:01:41','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2131\",\"affected\":{\"title\":\"Дэшборд\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:01:42',9676,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(29,'UsagesUpdate','2021-01-22 02:02:35','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1337\",\"affected\":{\"title\":\"Cущность, экземпляры которой являются пользователями\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:02:36',12048,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(30,'UsagesUpdate','2021-01-22 02:05:51','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1337\",\"affected\":{\"title\":\"Пользовательская сущность\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:05:52',10796,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(31,'UsagesUpdate','2021-01-22 02:06:20','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1337\",\"affected\":{\"title\":\"Сущность аккаунтов\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:06:20',5828,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(32,'UsagesUpdate','2021-01-22 02:08:50','{\"source\":\"ru\",\"fraction\":\"adminCustomUi\",\"table\":\"profile\",\"entry\":\"12\",\"affected\":{\"title\":\"Администратор\"},\"considerIdA\":[\"40\"]}','2021-01-22 02:08:51',7812,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(33,'UsagesUpdate','2021-01-22 02:09:29','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1337\",\"affected\":{\"title\":\"Сущность пользователей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:09:30',9896,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(34,'UsagesUpdate','2021-01-22 02:26:46','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"40\",\"affected\":{\"title\":\"Логин\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:26:47',648,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(35,'UsagesUpdate','2021-01-22 02:26:51','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"39\",\"affected\":{\"title\":\"Имя\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:26:51',12988,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(36,'UsagesUpdate','2021-01-22 02:30:07','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"38\",\"affected\":{\"title\":\"Роль\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 02:30:07',16280,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(37,'UsagesUpdate','2021-01-22 02:34:26','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2445\",\"affected\":{\"title\":\"Псевдоним\",\"gridId\":\"0\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:34:26',2588,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(38,'UsagesUpdate','2021-01-22 02:34:26','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2444\",\"affected\":{\"move\":\"2445\",\"title\":\"\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:34:26',7548,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(39,'UsagesUpdate','2021-01-22 02:34:26','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2440\",\"affected\":{\"move\":\"2444\",\"title\":\"\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:34:26',4912,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(40,'UsagesUpdate','2021-01-22 02:38:07','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2453\",\"affected\":{\"move\":\"2618\",\"title\":\"Порядок\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:38:07',12876,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(41,'UsagesUpdate','2021-01-22 02:38:11','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2448\",\"affected\":{\"move\":\"2452\",\"title\":\"\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:38:12',13424,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(42,'UsagesUpdate','2021-01-22 02:40:29','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2449\",\"affected\":{\"title\":\"Хранит ключи\",\"gridId\":\"2619\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:40:29',15132,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(43,'UsagesUpdate','2021-01-22 02:40:47','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2450\",\"affected\":{\"alterTitle\":\"\",\"title\":\"Ключи какой сущности\",\"gridId\":\"2619\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:40:48',12500,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(44,'UsagesUpdate','2021-01-22 02:43:41','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2439\",\"affected\":{\"alterTitle\":\"\",\"title\":\"Наименование\"},\"considerIdA\":[\"27\"]}','2021-01-22 02:43:42',3316,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(45,'UsagesUpdate','2021-01-22 02:57:04','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"search\",\"entry\":\"122\",\"affected\":{\"move\":\"123\",\"title\":\"Ключи какой сущности\"},\"considerIdA\":[\"28\"]}','2021-01-22 02:57:05',5140,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(46,'UsagesUpdate','2021-01-22 02:57:04','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"search\",\"entry\":\"120\",\"affected\":{\"move\":\"121\",\"title\":\"Сущность\"},\"considerIdA\":[\"28\"]}','2021-01-22 02:57:05',1180,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(47,'UsagesUpdate','2021-01-22 03:10:13','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1658\",\"affected\":{\"title\":\"Переименовать\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 03:10:14',10716,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(48,'UsagesUpdate','2021-01-22 03:10:27','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2183\",\"affected\":{\"title\":\"Фильтрация\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 03:10:28',12504,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(49,'UsagesUpdate','2021-01-22 03:12:14','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1443\",\"affected\":{\"title\":\"Поле\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 03:12:14',8920,'items','finished',5,'finished',3,'finished',3,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(50,'UsagesUpdate','2021-01-22 03:13:46','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2312\",\"affected\":{\"move\":\"2621\",\"title\":\"Фильтрация\"},\"considerIdA\":[\"27\"]}','2021-01-22 03:13:47',5976,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(51,'UsagesUpdate','2021-01-22 03:13:51','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"1954\",\"affected\":{\"move\":\"2311\",\"title\":\"Переименовать\"},\"considerIdA\":[\"27\"]}','2021-01-22 03:13:51',2284,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(52,'UsagesUpdate','2021-01-22 03:14:26','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1444\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 03:14:26',9320,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(53,'UsagesUpdate','2021-01-22 03:17:58','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"1383\",\"affected\":{\"title\":\"Поле\",\"editor\":\"1\"},\"considerIdA\":[\"27\"]}','2021-01-22 03:17:59',11388,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(54,'UsagesUpdate','2021-01-22 04:18:40','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2184\",\"affected\":{\"title\":\"Игнорировать шаблон\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 04:18:41',6988,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(55,'UsagesUpdate','2021-01-22 04:19:17','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2184\",\"affected\":{\"title\":\"Игнорировать шаблон, заданный в параметрах настроек поля\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 04:19:17',16724,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(56,'UsagesUpdate','2021-01-22 04:19:51','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2626\",\"affected\":{\"title\":\"Игнорировать шаблон, заданный в параметрах настроек поля\",\"gridId\":\"2628\"},\"considerIdA\":[\"27\"]}','2021-01-22 04:19:52',15016,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(57,'UsagesUpdate','2021-01-22 04:25:20','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2203\",\"affected\":{\"title\":\"Rvje\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 04:25:20',13780,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(58,'UsagesUpdate','2021-01-22 04:25:25','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2203\",\"affected\":{\"title\":\"Кому\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 04:25:25',7028,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(59,'UsagesUpdate','2021-01-22 04:26:08','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2622\",\"affected\":{\"title\":\"Кому\",\"gridId\":\"2629\"},\"considerIdA\":[\"27\"]}','2021-01-22 04:26:08',12484,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(60,'UsagesUpdate','2021-01-22 04:28:18','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2204\",\"affected\":{\"title\":\"Кроме\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 04:28:19',15476,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(61,'UsagesUpdate','2021-01-22 04:56:51','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2203\",\"affected\":{\"title\":\"Доступ\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 04:56:52',3200,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(62,'UsagesUpdate','2021-01-22 05:02:38','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2184\",\"affected\":{\"title\":\"Игнорировать шаблон, заданный в настройках поля\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:02:38',5168,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(63,'UsagesUpdate','2021-01-22 05:02:53','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2184\",\"affected\":{\"title\":\"Игнорировать шаблон опций, заданный в настройках поля\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:02:53',3448,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(64,'UsagesUpdate','2021-01-22 05:03:45','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2184\",\"affected\":{\"title\":\"Игнорировать шаблон опций\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:03:45',3028,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(65,'UsagesUpdate','2021-01-22 05:30:30','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"1382\",\"affected\":{\"title\":\"Порядок\",\"gridId\":\"2630\"},\"considerIdA\":[\"27\"]}','2021-01-22 05:30:30',13348,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(66,'UsagesUpdate','2021-01-22 05:42:20','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"35\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:42:20',3060,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(67,'UsagesUpdate','2021-01-22 05:42:26','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1886\",\"affected\":{\"title\":\"Переименовать\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:42:26',3844,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(68,'UsagesUpdate','2021-01-22 05:45:00','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2198\",\"affected\":{\"title\":\"Непустой результат\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:45:00',15780,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(69,'UsagesUpdate','2021-01-22 05:59:45','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"377\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 05:59:46',2392,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(70,'UsagesUpdate','2021-01-22 15:46:42','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"1656\",\"affected\":{\"title\":\"Переименовать\",\"editor\":\"1\"},\"considerIdA\":[\"27\"]}','2021-01-22 15:46:42',17732,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(71,'UsagesUpdate','2021-01-22 15:49:45','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2206\",\"affected\":{\"title\":\"Кроме\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 15:49:45',12860,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(72,'UsagesUpdate','2021-01-22 15:51:37','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2635\",\"affected\":{\"move\":\"2636\",\"title\":\"Кроме\"},\"considerIdA\":[\"27\"]}','2021-01-22 15:51:37',15372,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(73,'UsagesUpdate','2021-01-22 15:56:55','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"30\",\"affected\":{\"title\":\"Порядок\",\"editor\":\"1\"},\"considerIdA\":[\"27\"]}','2021-01-22 15:56:56',12168,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(74,'UsagesUpdate','2021-01-22 16:40:12','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2251\",\"affected\":{\"title\":\"Изменить свойства\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 16:40:12',3448,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(75,'UsagesUpdate','2021-01-22 16:45:10','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2207\",\"affected\":{\"title\":\"Роли\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 16:45:10',8756,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(76,'UsagesUpdate','2021-01-22 16:45:21','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2250\",\"affected\":{\"title\":\"Кроме\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 16:45:21',15652,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(77,'UsagesUpdate','2021-01-22 16:47:58','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2207\",\"affected\":{\"title\":\"Влияние\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 16:47:58',10800,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(78,'UsagesUpdate','2021-01-22 16:52:19','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2358\",\"affected\":{\"title\":\"Кроме\",\"gridId\":\"2640\"},\"considerIdA\":[\"27\"]}','2021-01-22 16:52:20',11652,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(79,'UsagesUpdate','2021-01-22 17:08:06','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"28\",\"affected\":{\"title\":\"Доступ\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:08:06',18316,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(80,'UsagesUpdate','2021-01-22 17:08:21','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"30\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:08:21',12080,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(81,'UsagesUpdate','2021-01-22 17:16:58','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"383\",\"affected\":{\"title\":\"Доступ\"},\"considerIdA\":[\"27\"]}','2021-01-22 17:16:59',16768,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(82,'UsagesUpdate','2021-01-22 17:16:58','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"25\",\"affected\":{\"move\":\"2328\",\"title\":\"Порядок\"},\"considerIdA\":[\"27\"]}','2021-01-22 17:16:59',17436,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(83,'UsagesUpdate','2021-01-22 17:21:40','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2212\",\"affected\":{\"title\":\"Южная панель\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:21:40',11620,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(84,'UsagesUpdate','2021-01-22 17:22:20','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2326\",\"affected\":{\"alterTitle\":\"ЮП\",\"title\":\"Южная панель\"},\"considerIdA\":[\"27\"]}','2021-01-22 17:22:20',14060,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(85,'UsagesUpdate','2021-01-22 17:46:14','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1345\",\"affected\":{\"title\":\"Запретить создание записей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:46:14',9528,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(86,'UsagesUpdate','2021-01-22 17:47:05','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2309\",\"affected\":{\"title\":\"JS\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:47:05',17024,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(87,'UsagesUpdate','2021-01-22 17:47:10','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"title\":\"PHP\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:47:10',13908,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(88,'UsagesUpdate','2021-01-22 17:48:21','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"18\",\"affected\":{\"title\":\"Вышестоящий раздел\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:48:22',18052,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(89,'UsagesUpdate','2021-01-22 17:48:50','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1554\",\"affected\":{\"title\":\"Связь по полю\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:48:50',14344,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(90,'UsagesUpdate','2021-01-22 17:49:49','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"767\",\"affected\":{\"title\":\"Фильтрация\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:49:49',3064,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(91,'UsagesUpdate','2021-01-22 17:52:49','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2213\",\"affected\":{\"title\":\"Подгрузка данных\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:52:49',1640,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(92,'UsagesUpdate','2021-01-22 17:57:31','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2427\",\"affected\":{\"title\":\"Отображение данных\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:57:31',4284,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(93,'UsagesUpdate','2021-01-22 17:58:50','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2310\",\"affected\":{\"title\":\"Включить нумерацию\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 17:58:50',17652,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(94,'UsagesUpdate','2021-01-22 18:00:03','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2426\",\"affected\":{\"title\":\"Источник данных\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:00:04',17012,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(95,'UsagesUpdate','2021-01-22 18:03:08','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2426\",\"affected\":{\"title\":\"Источник записей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:03:08',16408,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(96,'UsagesUpdate','2021-01-22 18:03:17','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2213\",\"affected\":{\"title\":\"Подгрузка\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:03:17',2652,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(97,'UsagesUpdate','2021-01-22 18:04:29','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1345\",\"affected\":{\"title\":\"Запретить создание новых\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:04:29',3712,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(98,'UsagesUpdate','2021-01-22 18:04:36','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2312\",\"affected\":{\"title\":\"Выделение более одной\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:04:36',6980,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(99,'UsagesUpdate','2021-01-22 18:04:52','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2427\",\"affected\":{\"title\":\"Отображение записей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:04:52',14344,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(100,'UsagesUpdate','2021-01-22 18:12:56','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"557\",\"affected\":{\"title\":\"Направление\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:12:56',14488,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(101,'UsagesUpdate','2021-01-22 18:20:12','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1554\",\"affected\":{\"title\":\"Связь с вышестоящим разделом по полю\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:20:13',432,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(102,'UsagesUpdate','2021-01-22 18:31:27','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2327\",\"affected\":{\"move\":\"31\",\"title\":\"Подгрузка\"},\"considerIdA\":[\"27\"]}','2021-01-22 18:31:27',7156,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(103,'UsagesUpdate','2021-01-22 18:32:16','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2213\",\"affected\":{\"title\":\"Режим подгрузки\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:32:16',8448,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(104,'UsagesUpdate','2021-01-22 18:33:04','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2460\",\"affected\":{\"move\":\"2641\",\"title\":\"JS\"},\"considerIdA\":[\"27\"]}','2021-01-22 18:33:04',1944,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(105,'UsagesUpdate','2021-01-22 18:33:05','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2459\",\"affected\":{\"move\":\"2460\",\"title\":\"PHP\"},\"considerIdA\":[\"27\"]}','2021-01-22 18:33:06',14988,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(106,'UsagesUpdate','2021-01-22 18:33:09','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2327\",\"affected\":{\"move\":\"926\",\"title\":\"Режим подгрузки\"},\"considerIdA\":[\"27\"]}','2021-01-22 18:33:09',6800,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(107,'UsagesUpdate','2021-01-22 18:37:05','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"23\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 18:37:05',18068,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(108,'UsagesUpdate','2021-01-22 18:37:21','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"18\",\"affected\":{\"title\":\"Порядок\",\"editor\":\"1\"},\"considerIdA\":[\"27\"]}','2021-01-22 18:37:21',18312,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(109,'UsagesUpdate','2021-01-22 18:56:00','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2309\",\"affected\":{\"tooltip\":\"Родительский класс JS\"},\"considerIdA\":[\"31\"]}','2021-01-22 18:56:01',13896,'items','finished',2,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(110,'UsagesUpdate','2021-01-22 18:56:16','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP\"},\"considerIdA\":[\"31\"]}','2021-01-22 18:56:16',17596,'items','finished',2,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(111,'UsagesUpdate','2021-01-22 19:12:00','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1366\",\"affected\":{\"title\":\"Фракция\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 19:12:00',14128,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(112,'UsagesUpdate','2021-01-22 19:14:17','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"1335\",\"affected\":{\"title\":\"Фракция\",\"editor\":\"1\"},\"considerIdA\":[\"27\"]}','2021-01-22 19:14:17',14816,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(113,'UsagesUpdate','2021-01-22 22:48:29','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"1345\",\"affected\":{\"title\":\"Запретить создание новых записей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-22 22:48:29',12792,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(114,'UsagesUpdate','2021-01-23 00:53:09','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"557\",\"affected\":{\"title\":\"Направление сортировки\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-23 00:53:09',14444,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(115,'UsagesUpdate','2021-01-23 02:11:38','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2646\",\"affected\":{\"toggle\":\"n\",\"title\":\"Направление сортировки\"},\"considerIdA\":[\"27\"]}','2021-01-23 02:11:38',13300,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(116,'UsagesUpdate','2021-01-23 02:12:52','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2643\",\"affected\":{\"move\":\"2641\",\"title\":\"Запретить создание новых записей\"},\"considerIdA\":[\"27\"]}','2021-01-23 02:12:52',17900,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(117,'UsagesUpdate','2021-01-23 20:58:15','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"25\",\"affected\":{\"title\":\"Количество Записей на странице\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-23 20:58:15',7704,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(118,'UsagesUpdate','2021-01-23 20:58:23','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"25\",\"affected\":{\"title\":\"Количество записей на странице\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-23 20:58:24',6264,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(119,'UsagesUpdate','2021-01-23 20:58:36','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"20\",\"affected\":{\"title\":\"Количество записей на странице\",\"tooltip\":\"\"},\"considerIdA\":[\"27\"]}','2021-01-23 20:58:37',19552,'items','finished',2,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(120,'UsagesUpdate','2021-01-23 22:50:04','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2312\",\"affected\":{\"title\":\"Выделение более одной записи\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-23 22:50:04',9160,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(121,'UsagesUpdate','2021-01-24 00:59:08','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2429\",\"affected\":{\"title\":\"Подгрузка записей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-24 00:59:08',10544,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(122,'UsagesUpdate','2021-01-24 01:00:10','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2310\",\"affected\":{\"title\":\"Включить нумерацию записей\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-24 01:00:10',13816,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(123,'UsagesUpdate','2021-01-24 01:07:44','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2132\",\"affected\":{\"title\":\"Порядок\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-24 01:07:44',6168,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(124,'UsagesUpdate','2021-01-24 01:13:44','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"26\",\"affected\":{\"title\":\"Раздел\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-24 01:13:45',280,'items','finished',5,'finished',0,'finished',0,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(125,'UsagesUpdate','2021-01-24 02:10:08','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"612\",\"affected\":{\"title\":\"Фракция\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-24 02:10:09',3764,'items','finished',5,'finished',2,'finished',2,0,'waiting',0,'waiting',0,'Создание очереди - Завершено'),(126,'UsagesUpdate','2021-01-24 02:27:22','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"767\",\"affected\":{\"title\":\"Фильтрация через SQL WHERE\"},\"considerIdA\":[\"35\",\"37\",\"39\",\"41\"]}','2021-01-24 02:27:22',19236,'items','finished',5,'finished',1,'finished',1,0,'waiting',0,'waiting',0,'Создание очереди - Завершено');
/*!40000 ALTER TABLE `queuetask` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `realtime`
--

DROP TABLE IF EXISTS `realtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `realtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `realtimeId` int(11) NOT NULL DEFAULT '0',
  `type` enum('session','channel','context') NOT NULL DEFAULT 'session',
  `profileId` int(11) NOT NULL DEFAULT '0',
  `adminId` int(11) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `spaceSince` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `spaceUntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `spaceFrame` int(11) NOT NULL DEFAULT '0',
  `langId` int(11) NOT NULL DEFAULT '0',
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `entries` varchar(255) NOT NULL DEFAULT '',
  `fields` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `mode` enum('none','rowset','row') NOT NULL DEFAULT 'none',
  `scope` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `realtimeId` (`realtimeId`),
  KEY `type` (`type`),
  KEY `profileId` (`profileId`),
  KEY `adminId` (`adminId`),
  KEY `langId` (`langId`),
  KEY `sectionId` (`sectionId`),
  KEY `entityId` (`entityId`),
  KEY `entries` (`entries`),
  KEY `mode` (`mode`),
  FULLTEXT KEY `fields` (`fields`),
  FULLTEXT KEY `scope` (`scope`)
) ENGINE=MyISAM AUTO_INCREMENT=896 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `realtime`
--

LOCK TABLES `realtime` WRITE;
/*!40000 ALTER TABLE `realtime` DISABLE KEYS */;
INSERT INTO `realtime` VALUES (895,0,'session',1,1,'4ginbictid1bj749fl6of60r90','2021-01-24 15:55:16','0000-00-00 00:00:00',0,1,0,0,'','','Сессия - 4ginbictid1bj749fl6of60r90, Русский','none','');
/*!40000 ALTER TABLE `realtime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resize`
--

DROP TABLE IF EXISTS `resize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resize`
--

LOCK TABLES `resize` WRITE;
/*!40000 ALTER TABLE `resize` DISABLE KEYS */;
/*!40000 ALTER TABLE `resize` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search`
--

DROP TABLE IF EXISTS `search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `allowClear` tinyint(1) NOT NULL DEFAULT '1',
  `further` int(11) NOT NULL DEFAULT '0',
  `tooltip` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `toggle` (`toggle`),
  KEY `access` (`access`),
  KEY `profileIds` (`profileIds`),
  KEY `further` (`further`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search`
--

LOCK TABLES `search` WRITE;
/*!40000 ALTER TABLE `search` DISABLE KEYS */;
INSERT INTO `search` VALUES (13,5,612,13,'y','','Тип','','',1,1,'all','',1,0,''),(114,5,1441,114,'n','','Включить в кэш','','',1,1,'all','',1,0,''),(116,7,1366,116,'y','','Тип','','',1,1,'all','',1,0,''),(117,7,19,117,'y','','Сущность','','',1,1,'all','',1,0,''),(119,7,2303,119,'y','','Доступ','','',1,1,'all','',1,0,''),(118,7,22,118,'y','','Статус','','',1,1,'all','',1,0,''),(120,391,6,121,'y','Сущность','Сущность','','',1,1,'all','',1,0,''),(133,391,470,132,'y','','Хранит ключи','','',1,1,'all','',1,0,''),(121,391,2197,134,'y','','Режим','','',1,1,'all','',1,0,''),(122,391,12,133,'y','','Ключи какой сущности','','',1,1,'all','',1,0,''),(134,391,9,122,'y','','Тип столбца MySQL','','',1,1,'all','',1,0,''),(123,391,10,135,'y','Элемент','Элемент управления','','',1,1,'all','',1,0,''),(125,387,2325,125,'y','','Состояние','','',1,1,'all','',1,0,''),(126,387,2238,126,'y','','Статус','','',1,1,'all','',1,0,''),(127,394,2378,127,'y','','Статус','','',1,1,'all','',1,0,''),(128,396,2397,128,'y','','Тип','','',1,1,'all','',1,0,''),(129,396,2398,129,'y','','Роль','','',1,1,'all','',1,0,''),(130,396,2404,130,'y','','Язык','','',1,1,'all','',1,0,''),(131,396,2399,131,'y','','Пользователь','','',1,1,'all','',1,0,''),(132,391,6,120,'y','','Сущность','','',1,1,'all','',1,612,''),(135,391,2239,123,'y','','Мультиязычность','','',1,1,'all','',1,0,''),(136,7,2213,136,'y','','Режим подгрузки','','',1,1,'all','',1,0,''),(137,5,2243,137,'y','','Паттерн комплекта календарных полей','','',1,1,'all','',1,0,'');
/*!40000 ALTER TABLE `search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n','h') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  `rowsOnPage` int(11) NOT NULL DEFAULT '25',
  `extendsPhp` varchar(255) NOT NULL DEFAULT 'Indi_Controller_Admin',
  `defaultSortField` int(11) NOT NULL DEFAULT '0',
  `defaultSortDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `filter` varchar(255) NOT NULL DEFAULT '',
  `disableAdd` enum('0','1') NOT NULL DEFAULT '0',
  `type` enum('s','p','o') NOT NULL DEFAULT 'p',
  `parentSectionConnector` int(11) NOT NULL DEFAULT '0',
  `groupBy` int(11) NOT NULL DEFAULT '0',
  `rowsetSeparate` enum('auto','yes','no') NOT NULL DEFAULT 'auto',
  `expand` enum('all','only','except','none') NOT NULL DEFAULT 'all',
  `expandRoles` varchar(255) NOT NULL DEFAULT '',
  `roleIds` varchar(255) NOT NULL DEFAULT '',
  `extendsJs` varchar(255) NOT NULL DEFAULT 'Indi.lib.controller.Controller',
  `rownumberer` enum('0','1') NOT NULL DEFAULT '0',
  `multiSelect` enum('0','1') NOT NULL DEFAULT '0',
  `tileField` int(11) NOT NULL DEFAULT '0',
  `tileThumb` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `entityId` (`entityId`),
  KEY `toggle` (`toggle`),
  KEY `defaultSortField` (`defaultSortField`),
  KEY `defaultSortDirection` (`defaultSortDirection`),
  KEY `type` (`type`),
  KEY `parentSectionConnector` (`parentSectionConnector`),
  KEY `groupBy` (`groupBy`),
  KEY `rowsetSeparate` (`rowsetSeparate`),
  KEY `expand` (`expand`),
  KEY `expandRoles` (`expandRoles`),
  KEY `roleIds` (`roleIds`),
  KEY `tileField` (`tileField`),
  KEY `tileThumb` (`tileThumb`),
  KEY `disableAdd` (`disableAdd`),
  KEY `multiSelect` (`multiSelect`),
  KEY `rownumberer` (`rownumberer`)
) ENGINE=MyISAM AUTO_INCREMENT=397 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (1,0,0,'Конфигурация','configuration','y',367,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(2,1,1,'Столбцы','columnTypes','y',6,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(5,1,2,'Сущности','entities','y',4,25,'Indi_Controller_Admin',4,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(6,5,5,'Поля в структуре','fields','y',7,100,'Indi_Controller_Admin_Exportable',14,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','1',0,0),(7,1,3,'Разделы','sections','y',2,25,'Indi_Controller_Admin_Exportable',23,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(8,7,8,'Действия','sectionActions','y',8,25,'Indi_Controller_Admin_Multinew',30,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(10,1,7,'Действия','actions','h',9,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(11,7,9,'Столбцы грида','grid','y',10,25,'Indi_Controller_Admin_Multinew',35,'ASC','','0','s',0,2308,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(12,6,6,'Возможные значения','enumset','y',11,25,'Indi_Controller_Admin_Exportable',377,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(13,1,10,'Роли','profiles','y',5,25,'Indi_Controller_Admin',2132,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(14,13,11,'Пользователи','admins','y',13,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(16,1,4,'Элементы','controlElements','y',14,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(22,6,20,'Копии изображения','resize','y',19,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(30,378,25,'Страницы','staticpages','n',316,25,'Indi_Controller_Admin',131,'ASC','','0','o',0,0,'auto','all','','12,1','Indi.lib.controller.Controller','0','0',0,0),(100,16,90,'Возможные параметры','possibleParams','y',90,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(101,6,91,'Параметры','params','y',91,25,'Indi_Controller_Admin_Exportable',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(112,0,0,'Фронтенд','','y',371,25,'Indi_Controller_Admin',0,'ASC','','0','o',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(113,112,101,'Разделы','fsections','y',104,25,'Indi_Controller_Admin',585,'ASC','<?=$_SESSION[\'admin\'][\'profileId\']==1?\'1\':\'`toggle`=\"y\"\'?>','0','o',0,0,'auto','all','','1,2','Indi.lib.controller.Controller','0','0',0,0),(143,0,0,'Обратная связь','','n',358,25,'Indi_Controller_Admin',0,'ASC','','0','o',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(144,143,128,'Фидбэк','feedback','n',135,25,'Indi_Controller_Admin',681,'DESC','','0','o',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller','0','0',0,0),(172,112,146,'Действия','factions','y',185,25,'Indi_Controller_Admin',857,'ASC','','0','o',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller','0','0',0,0),(173,113,147,'Действия','fsection2factions','y',161,25,'Indi_Controller_Admin',860,'ASC','','0','o',0,0,'auto','all','','1,2','Indi.lib.controller.Controller','0','0',0,0),(191,173,162,'Компоненты SEO-урла','seoUrl','y',178,25,'Indi_Controller_Admin',1195,'ASC','','0','o',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(201,7,171,'Измененные поля','alteredFields','y',188,25,'Indi_Controller_Admin_Multinew',1342,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(224,7,195,'Фильтры','search','y',192,25,'Indi_Controller_Admin_Multinew',1444,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(232,378,204,'Элементы','staticblocks','n',232,25,'Indi_Controller_Admin',1485,'ASC','','0','o',0,0,'auto','all','','12,1','Indi.lib.controller.Controller','0','0',0,0),(379,173,301,'Компоненты meta-тегов','metatitles','y',379,25,'Indi_Controller_Admin_Meta',2181,'ASC','','0','o',0,2172,'yes','all','','1,12','Indi.lib.controller.Controller','0','0',0,0),(378,0,0,'Статика','','n',144,30,'Indi_Controller_Admin',0,'ASC','','0','o',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(389,1,309,'Уведомления','notices','y',389,25,'Indi_Controller_Admin',2254,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(390,389,310,'Получатели','noticeGetters','y',390,25,'Indi_Controller_Admin',2275,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(387,1,307,'Языки','lang','y',387,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,2325,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(388,6,308,'Зависимости','consider','y',388,25,'Indi_Controller_Admin_Exportable',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(391,1,5,'Все поля','fieldsAll','y',391,25,'Indi_Controller_Admin',0,'ASC','','1','s',0,6,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(392,1,314,'Очереди задач','queueTask','y',392,25,'Indi_Controller_Admin',2337,'DESC','','1','s',0,2382,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(393,392,315,'Сегменты очереди','queueChunk','y',393,25,'Indi_Controller_Admin',2379,'ASC','','1','s',0,2381,'no','all','','1','Indi.lib.controller.Controller','1','0',0,0),(394,393,316,'Элементы очереди','queueItem','y',394,25,'Indi_Controller_Admin',0,'ASC','','1','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(396,1,318,'Рилтайм','realtime','y',396,25,'Indi_Controller_Admin',2401,'ASC','','0','s',0,2399,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section2action`
--

DROP TABLE IF EXISTS `section2action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `l10n` enum('n','qy','y','qn') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `sectionId_2` (`sectionId`),
  KEY `actionId` (`actionId`),
  KEY `profileIds` (`profileIds`),
  KEY `toggle` (`toggle`),
  KEY `south` (`south`),
  KEY `fitWindow` (`fitWindow`),
  KEY `l10n` (`l10n`)
) ENGINE=MyISAM AUTO_INCREMENT=1614 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section2action`
--

LOCK TABLES `section2action` WRITE;
/*!40000 ALTER TABLE `section2action` DISABLE KEYS */;
INSERT INTO `section2action` VALUES (1,2,1,'y',1,'1','Список','','auto','auto','n'),(2,2,2,'y',2,'1','Детали','','auto','auto','n'),(3,2,3,'y',3,'1','Сохранить','','auto','auto','n'),(5,2,4,'n',5,'1','Удалить','','auto','auto','n'),(6,5,1,'y',6,'1','Список','','auto','auto','n'),(7,5,2,'y',7,'1','Детали','','auto','auto','n'),(8,5,3,'y',8,'1','Сохранить','','auto','auto','n'),(9,5,4,'y',9,'1','Удалить','','auto','auto','n'),(10,6,1,'y',10,'1','Список','','auto','auto','n'),(11,6,2,'y',11,'1','Детали','','auto','auto','n'),(12,6,3,'y',12,'1','Сохранить','','auto','auto','n'),(13,6,4,'y',13,'1','Удалить','','auto','auto','n'),(14,7,1,'y',14,'1','Список','','auto','auto','n'),(15,7,2,'y',15,'1','Детали','','auto','auto','n'),(16,7,3,'y',16,'1','Сохранить','','auto','auto','n'),(17,7,4,'y',17,'1','Удалить','','auto','auto','n'),(18,6,5,'y',18,'1','Выше','','auto','auto','n'),(19,6,6,'y',19,'1','Ниже','','auto','auto','n'),(20,8,1,'y',20,'1','Список','','auto','auto','n'),(21,8,2,'y',21,'1','Детали','','auto','auto','n'),(22,8,3,'y',22,'1','Сохранить','','auto','auto','n'),(23,8,4,'y',23,'1','Удалить','','auto','auto','n'),(24,8,5,'y',24,'1','Выше','','auto','auto','n'),(25,8,6,'y',25,'1','Ниже','','auto','auto','n'),(26,7,5,'y',26,'1','Выше','','auto','auto','n'),(27,7,6,'y',27,'1','Ниже','','auto','auto','n'),(28,10,1,'y',28,'1','Список','','auto','auto','n'),(29,10,2,'y',29,'1','Детали','','auto','auto','n'),(30,10,3,'y',30,'1','Сохранить','','auto','auto','n'),(32,11,1,'y',31,'1','Список','','auto','auto','n'),(33,11,2,'y',32,'1','Детали','','auto','auto','n'),(34,11,3,'y',33,'1','Сохранить','','auto','auto','n'),(35,11,4,'y',34,'1','Удалить','','auto','auto','n'),(36,11,5,'y',35,'1','Выше','','auto','auto','n'),(37,11,6,'y',36,'1','Ниже','','auto','auto','n'),(38,13,1,'y',37,'1','Список','','auto','auto','n'),(39,13,2,'y',38,'1','Детали','','auto','auto','n'),(40,13,3,'y',39,'1','Сохранить','','auto','auto','n'),(41,12,1,'y',40,'1','Список','','auto','auto','n'),(42,12,2,'y',41,'1','Детали','','auto','auto','n'),(43,12,3,'y',42,'1','Сохранить','','auto','auto','n'),(44,12,4,'y',43,'1','Удалить','','auto','auto','n'),(45,14,1,'y',44,'1','Список','','auto','auto','n'),(46,14,2,'y',45,'1','Детали','','auto','auto','n'),(47,14,3,'y',46,'1','Сохранить','','auto','auto','n'),(48,14,4,'y',47,'1','Удалить','','auto','auto','n'),(52,16,1,'y',51,'1','Список','','auto','auto','n'),(53,16,2,'y',52,'1','Детали','','auto','auto','n'),(54,16,3,'y',53,'1','Сохранить','','auto','auto','n'),(67,7,7,'y',58,'1','Статус','','auto','auto','n'),(68,10,7,'y',59,'1','Статус','','auto','auto','n'),(69,11,7,'y',60,'1','Статус','','auto','auto','n'),(74,22,1,'y',65,'1','Список','','auto','auto','n'),(75,22,2,'y',66,'1','Детали','','auto','auto','n'),(76,22,3,'y',67,'1','Сохранить','','auto','auto','n'),(77,22,4,'y',68,'1','Удалить','','auto','auto','n'),(99,30,1,'y',69,'12,1','Список','','auto','auto','n'),(100,30,2,'y',70,'12,1','Детали','','auto','auto','n'),(101,30,3,'y',71,'12,1','Сохранить','','auto','auto','n'),(102,30,4,'y',72,'12,1','Удалить','','auto','auto','n'),(103,30,7,'y',73,'12,1','Статус','','auto','auto','n'),(329,12,5,'y',299,'1','Выше','','auto','auto','n'),(330,12,6,'y',300,'1','Ниже','','auto','auto','n'),(373,100,1,'y',343,'1','Список','','auto','auto','n'),(374,100,2,'y',344,'1','Детали','','auto','auto','n'),(375,100,3,'y',345,'1','Сохранить','','auto','auto','n'),(376,100,4,'y',346,'1','Удалить','','auto','auto','n'),(377,101,1,'y',347,'1','Список','','auto','auto','n'),(378,101,2,'y',348,'1','Детали','','auto','auto','n'),(379,101,3,'y',349,'1','Сохранить','','auto','auto','n'),(380,101,4,'y',350,'1','Удалить','','auto','auto','n'),(808,13,4,'y',589,'1','Удалить','','auto','auto','n'),(806,10,4,'y',587,'1','Удалить','','auto','auto','n'),(429,113,1,'y',399,'1,2','Список','','auto','auto','n'),(430,113,2,'y',400,'1','Детали','','auto','auto','n'),(431,113,3,'y',401,'1','Сохранить','','auto','auto','n'),(432,113,4,'y',402,'1','Удалить','','auto','auto','n'),(1593,387,6,'y',1593,'1','Ниже','','auto','auto','n'),(532,144,1,'y',133,'1,2,4','Список','','auto','auto','n'),(533,144,2,'y',134,'1,2,4','Детали','','auto','auto','n'),(534,144,3,'y',135,'1,2','Сохранить','','auto','auto','n'),(535,144,4,'y',136,'1,2','Удалить','','auto','auto','n'),(833,16,4,'n',598,'1','Удалить','','auto','auto','n'),(875,224,1,'y',608,'1','Список','','auto','auto','n'),(876,224,2,'y',609,'1','Детали','','auto','auto','n'),(877,224,4,'y',611,'1','Удалить','','auto','auto','n'),(878,224,3,'y',610,'1','Сохранить','','auto','auto','n'),(879,224,5,'y',612,'1','Выше','','auto','auto','n'),(880,224,6,'y',613,'1','Ниже','','auto','auto','n'),(881,224,7,'y',614,'1','Статус','','auto','auto','n'),(642,172,1,'y',161,'1','Список','','auto','auto','n'),(643,172,2,'y',162,'1,2,4','Детали','','auto','auto','n'),(644,172,3,'y',163,'1,2','Сохранить','','auto','auto','n'),(645,172,4,'y',164,'1,2','Удалить','','auto','auto','n'),(646,173,1,'y',162,'1,2','Список','','auto','auto','n'),(647,173,2,'y',163,'1','Детали','','auto','auto','n'),(648,173,3,'y',164,'1','Сохранить','','auto','auto','n'),(649,173,4,'y',165,'1','Удалить','','auto','auto','n'),(740,191,1,'y',534,'1','Список','','auto','auto','n'),(741,191,2,'y',535,'1','Детали','','auto','auto','n'),(742,191,3,'y',536,'1','Сохранить','','auto','auto','n'),(743,191,4,'y',537,'1','Удалить','','auto','auto','n'),(744,191,5,'y',538,'1','Выше','','auto','auto','n'),(745,191,6,'y',539,'1','Ниже','','auto','auto','n'),(1548,7,34,'y',1548,'1','PHP','','auto','auto','n'),(1547,5,34,'y',1559,'1','PHP','','auto','auto','n'),(1592,387,5,'y',1592,'1','Выше','','auto','auto','n'),(1528,379,6,'y',1528,'1,12','Ниже','','auto','auto','n'),(789,201,1,'y',583,'1','Список','','auto','auto','n'),(790,201,2,'y',584,'1','Детали','','auto','auto','n'),(791,201,3,'y',585,'1','Сохранить','','auto','auto','n'),(792,201,4,'y',586,'1','Удалить','','auto','auto','n'),(910,232,1,'y',643,'12,1','Список','','auto','auto','n'),(911,232,2,'y',644,'1,12','Детали','','auto','auto','n'),(912,232,3,'y',645,'1,12','Сохранить','','auto','auto','n'),(913,232,4,'y',646,'1','Удалить','','auto','auto','n'),(1527,379,5,'y',1527,'1,12','Выше','','auto','auto','n'),(939,232,7,'y',672,'12,1','Статус','','auto','auto','n'),(946,113,7,'y',679,'1','Статус','','auto','auto','n'),(947,113,5,'y',680,'1,2','Выше','','auto','auto','n'),(948,113,6,'y',681,'1,2','Ниже','','auto','auto','n'),(949,113,19,'y',682,'1,2','Обновить sitemap.xml','','auto','auto','n'),(1268,8,7,'y',1268,'1','Статус','','auto','auto','n'),(1295,13,5,'y',1291,'1','Выше','','auto','auto','n'),(1296,13,6,'y',1292,'1','Ниже','','auto','auto','n'),(1522,14,7,'y',1293,'1','Статус','','auto','auto','n'),(1545,13,7,'y',1545,'1','Статус','','auto','auto','n'),(1549,7,35,'y',1549,'1','JS','','auto','auto','n'),(1526,379,4,'y',1526,'1,12','Удалить','','auto','auto','n'),(1525,379,3,'y',1525,'1,12','Сохранить','','auto','auto','n'),(1524,379,2,'y',1524,'1,12','Детали','','auto','auto','n'),(1523,379,1,'y',1523,'1,12','Список','','auto','n','n'),(1577,390,3,'y',1577,'1','Сохранить','','auto','auto','n'),(1576,390,2,'y',1576,'1','Детали','','auto','auto','n'),(1575,390,1,'y',1575,'1','Список','','auto','auto','n'),(1574,389,7,'y',1574,'1','Статус','','auto','auto','n'),(1570,389,1,'y',1570,'1','Список','','auto','auto','n'),(1571,389,2,'y',1571,'1','Детали','','auto','auto','n'),(1572,389,3,'y',1572,'1','Сохранить','','auto','auto','n'),(1573,389,4,'y',1573,'1','Удалить','','auto','auto','n'),(1559,5,7,'y',607,'1','Статус','','auto','auto','n'),(1560,387,1,'y',1560,'1','Список','','auto','auto','n'),(1561,387,2,'y',1561,'1','Детали','','auto','auto','n'),(1562,387,3,'y',1562,'1','Сохранить','','auto','auto','n'),(1563,387,4,'y',1563,'1','Удалить','','auto','auto','n'),(1564,388,1,'y',1564,'1','Список','','auto','auto','n'),(1565,388,2,'y',1565,'1','Детали','','auto','auto','n'),(1566,388,3,'y',1566,'1','Сохранить','','auto','auto','n'),(1567,388,4,'y',1567,'1','Удалить','','auto','auto','n'),(1568,7,36,'y',1568,'1','Экспорт','','auto','auto','n'),(1569,5,36,'y',1569,'1','Экспорт','','auto','auto','n'),(1578,390,4,'y',1578,'1','Удалить','','auto','auto','n'),(1579,391,1,'y',1579,'1','Список','','auto','auto','n'),(1580,391,2,'y',1580,'1','Детали','','auto','auto','n'),(1581,391,3,'y',1582,'1','Сохранить','','auto','auto','n'),(1582,6,39,'y',1581,'1','Активировать','Выбрать режим','auto','auto','n'),(1583,8,36,'y',1583,'1','Экспорт','','auto','auto','n'),(1584,11,36,'y',1584,'1','Экспорт','','auto','auto','n'),(1585,201,36,'y',1585,'1','Экспорт','','auto','auto','n'),(1586,224,36,'y',1586,'1','Экспорт','','auto','auto','n'),(1587,6,36,'y',1587,'1','Экспорт','','auto','auto','n'),(1588,12,36,'y',1588,'1','Экспорт','','auto','auto','n'),(1589,22,36,'y',1589,'1','Экспорт','','auto','auto','n'),(1590,101,36,'y',1590,'1','Экспорт','','auto','auto','n'),(1591,388,36,'y',1591,'1','Экспорт','','auto','auto','n'),(1594,387,40,'y',1594,'1','Доступные языки','','auto','auto','n'),(1595,392,1,'y',1595,'1','Список','','auto','auto','n'),(1596,392,2,'y',1596,'1','Детали','','auto','auto','n'),(1597,392,4,'y',1597,'1','Удалить','','auto','auto','n'),(1598,392,41,'y',1598,'1','Запустить','','auto','auto','n'),(1599,393,1,'y',1599,'1','Список','','auto','auto','n'),(1600,393,2,'y',1600,'1','Детали','','auto','auto','n'),(1601,394,1,'y',1601,'1','Список','','auto','auto','n'),(1602,394,3,'y',1602,'1','Сохранить','','auto','auto','n'),(1603,387,43,'y',1603,'1','Вординги','','auto','auto','n'),(1609,396,2,'y',1609,'1','Детали','','auto','auto','n'),(1610,396,1,'y',1610,'1','Список','','auto','auto','n'),(1611,396,3,'y',1611,'1','Сохранить','','auto','auto','n'),(1612,396,4,'y',1612,'1','Удалить','','auto','auto','n'),(1613,396,44,'y',1613,'1','Перезапустить','Перезагрузить websocket-сервер','auto','auto','n');
/*!40000 ALTER TABLE `section2action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staticblock`
--

DROP TABLE IF EXISTS `staticblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staticblock`
--

LOCK TABLES `staticblock` WRITE;
/*!40000 ALTER TABLE `staticblock` DISABLE KEYS */;
INSERT INTO `staticblock` VALUES (21,'sd','hello','Hello','y',443,81,'','','html','','');
/*!40000 ALTER TABLE `staticblock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staticpage`
--

DROP TABLE IF EXISTS `staticpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staticpage`
--

LOCK TABLES `staticpage` WRITE;
/*!40000 ALTER TABLE `staticpage` DISABLE KEYS */;
INSERT INTO `staticpage` VALUES (9,'Страница не найдена','404','y','<strong>initial value1231231aaa</strong>',0);
/*!40000 ALTER TABLE `staticpage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `url`
--

DROP TABLE IF EXISTS `url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `url`
--

LOCK TABLES `url` WRITE;
/*!40000 ALTER TABLE `url` DISABLE KEYS */;
/*!40000 ALTER TABLE `url` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `year`
--

DROP TABLE IF EXISTS `year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `year`
--

LOCK TABLES `year` WRITE;
/*!40000 ALTER TABLE `year` DISABLE KEYS */;
/*!40000 ALTER TABLE `year` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-01-26 17:22:47

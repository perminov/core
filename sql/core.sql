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
  `title` text NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `rowRequired` enum('y','n') NOT NULL DEFAULT 'y',
  `type` enum('p','s','o') NOT NULL DEFAULT 'p',
  `display` enum('0','1') NOT NULL DEFAULT '1',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `rowRequired` (`rowRequired`),
  KEY `type` (`type`),
  KEY `toggle1` (`toggle`),
  KEY `display` (`display`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
INSERT INTO `action` VALUES (1,'{\"ru\":\"Список\",\"en\":\"List\"}','index','n','s','0','y'),(2,'{\"ru\":\"Детали\",\"en\":\"Details\"}','form','y','s','1','y'),(3,'{\"ru\":\"Сохранить\",\"en\":\"Save\"}','save','y','s','0','y'),(4,'{\"ru\":\"Удалить\",\"en\":\"Delete\"}','delete','y','s','1','y'),(5,'{\"ru\":\"Выше\",\"en\":\"Higher\"}','up','y','s','1','y'),(6,'{\"ru\":\"Ниже\",\"en\":\"Below\"}','down','y','s','1','y'),(7,'{\"ru\":\"Статус\",\"en\":\"Status\"}','toggle','y','s','1','y'),(18,'{\"ru\":\"Обновить кэш\",\"en\":\"Refresh cache\"}','cache','y','s','1','y'),(20,'{\"ru\":\"Авторизация\",\"en\":\"Authorization\"}','login','y','s','1','y'),(33,'{\"ru\":\"Автор\",\"en\":\"Author\"}','author','y','s','1','y'),(34,'{\"ru\":\"PHP\",\"en\":\"PHP\"}','php','y','s','1','y'),(35,'{\"ru\":\"JS\",\"en\":\"Js\"}','js','y','s','1','y'),(36,'{\"ru\":\"Экспорт\",\"en\":\"Export\"}','export','y','s','1','y'),(37,'{\"ru\":\"Перейти\",\"en\":\"Go to\"}','goto','y','s','1','y'),(38,'{\"ru\":\"\",\"en\":\"\"}','rwu','n','s','0','y'),(39,'{\"ru\":\"Активировать\",\"en\":\"Activate\"}','activate','y','s','1','y'),(40,'{\"ru\":\"Доступные языки\",\"en\":\"Available languages\"}','dict','n','s','1','y'),(41,'{\"ru\":\"Запустить\",\"en\":\"Run\"}','run','y','s','1','y'),(42,'{\"ru\":\"График\",\"en\":\"Schedule\"}','chart','y','s','1','y'),(43,'{\"ru\":\"Вординги\",\"en\":\"Wordings\"}','wordings','y','s','1','y'),(44,'{\"ru\":\"Перезапустить\",\"en\":\"Restart\"}','restart','n','s','1','y'),(45,'{\"ru\":\"Копировать\",\"en\":\"Copy\"}','copy','y','s','1','y');
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
INSERT INTO `admin` VALUES (1,1,'Павел Перминов','pavel.perminov.23@gmail.com','*8E1219CD047401C6FEAC700B47F5DA846A57ABD4','y','n','y'),(14,12,'John Smith','admin','*4ACFE3202A5FF5CF467898FC58AAB1D615029441','y','n','n');
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
) ENGINE=MyISAM AUTO_INCREMENT=221 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alteredfield`
--

LOCK TABLES `alteredfield` WRITE;
/*!40000 ALTER TABLE `alteredfield` DISABLE KEYS */;
INSERT INTO `alteredfield` VALUES (215,232,1515,'','readonly','Тип','except','1','',0),(216,394,2377,'','inherit','Результат','all','','',1),(219,6,2435,'','hidden','Экземпляр','all','','',0),(220,405,6,'4','inherit','Сущность','all','','',0);
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
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consider`
--

LOCK TABLES `consider` WRITE;
/*!40000 ALTER TABLE `consider` DISABLE KEYS */;
INSERT INTO `consider` VALUES (1,308,2307,2246,12,'Поле','y',0),(2,5,9,10,0,'Элемент управления','y',0),(3,5,10,470,0,'Предназначено для хранения ключей','y',0),(4,9,34,33,19,'Раздел','y',0),(23,9,2313,34,12,'Поле','y',6),(24,195,2314,1443,12,'Поле прикрепленной к разделу сущности','y',6),(47,91,2166,2433,7,'Параметр','y',0),(7,3,503,19,0,'Сущность','y',0),(8,195,1443,1442,19,'Раздел','y',0),(9,101,1010,563,0,'Прикрепленная сущность','y',0),(10,162,1193,1192,0,'Раздел фронтенда','y',0),(11,301,2178,2177,0,'Сущность','y',0),(12,171,1342,1341,19,'Раздел','y',0),(13,301,2171,2170,0,'Раздел','y',0),(14,3,1554,19,0,'Сущность','y',0),(15,101,1560,868,563,'Вышестоящий раздел','y',0),(16,3,2211,19,0,'Сущность','y',0),(17,309,2262,2255,0,'Сущность','y',0),(18,308,2247,2246,6,'Поле','y',0),(19,308,2248,2247,12,'От какого поля зависит','y',6),(20,313,2292,2291,0,'Сущность','y',0),(21,313,2293,2291,0,'Сущность','y',0),(22,313,2299,2298,0,'Тип автора','y',0),(25,3,2322,19,0,'Сущность','y',0),(26,3,2323,2322,0,'Плитка','y',106),(27,9,1886,2165,0,'Auto title','y',0),(28,195,1658,2167,0,'Auto title','y',0),(29,8,2209,2164,0,'Auto title','y',0),(30,171,2252,2169,0,'Auto title','y',0),(31,9,2200,34,2199,'Поле','y',0),(32,314,2382,2342,0,'Этап','y',0),(33,314,2382,2343,0,'Статус','y',0),(34,8,2164,27,31,'Действие','y',0),(35,9,2165,34,7,'Поле','y',0),(37,195,2167,1443,7,'Поле прикрепленной к разделу сущности','y',0),(38,147,2168,860,857,'Действие','y',0),(39,171,2169,1342,7,'Поле','y',0),(40,310,2280,2275,36,'Роль','y',0),(41,308,2249,2247,7,'От какого поля зависит','y',0),(42,318,2399,2398,1337,'Роль','y',0),(43,318,2407,2405,19,'Раздел','y',0),(44,318,2408,2406,0,'Сущность','y',0),(45,91,2433,476,10,'Поле','y',2435),(46,5,2435,6,0,'Сущность','y',0);
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
) ENGINE=MyISAM AUTO_INCREMENT=322 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity`
--

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` VALUES (1,'Тип столбца','columnType','Indi_Db_Table','y',0,2,'none','',0),(2,'Сущность','entity','Indi_Db_Table','y',1,4,'none','',0),(3,'Раздел','section','Indi_Db_Table','y',0,20,'none','',0),(4,'Элемент управления','element','Indi_Db_Table','y',0,64,'none','',0),(5,'Поле','field','Indi_Db_Table','y',1,7,'none','',0),(6,'Значение из набора','enumset','Indi_Db_Table','y',0,16,'none','',0),(7,'Действие','action','Indi_Db_Table','y',0,31,'none','',0),(8,'Действие в разделе','section2action','Indi_Db_Table','y',0,27,'none','',0),(9,'Столбец грида','grid','Indi_Db_Table','y',0,34,'none','',0),(10,'Роль','profile','Indi_Db_Table','y',0,36,'none','',0),(11,'Администратор','admin','Indi_Db_Table','y',0,39,'none','',0),(20,'Копия','resize','Indi_Db_Table','y',0,107,'none','',0),(25,'Статическая страница','staticpage','Indi_Db_Table','o',0,131,'none','',0),(91,'Параметр','param','Indi_Db_Table','y',0,2433,'none','',0),(101,'Раздел фронтенда','fsection','Indi_Db_Table','o',1,559,'none','',0),(195,'Фильтр','search','Indi_Db_Table','y',0,1443,'none','',0),(128,'Фидбэк','feedback','Indi_Db_Table','o',0,678,'none','',0),(146,'Действие, возможное для использования в разделе фронтенда','faction','Indi_Db_Table','o',1,857,'none','',0),(147,'Действие в разделе фронтенда','fsection2faction','Indi_Db_Table','o',1,860,'none','',0),(307,'Язык','lang','Indi_Db_Table','y',0,2236,'none','',0),(162,'Компонент SEO-урла','url','Indi_Db_Table','o',0,0,'none','',0),(171,'Поле, измененное в рамках раздела','alteredField','Indi_Db_Table','y',0,1342,'none','',0),(204,'Статический элемент','staticblock','Indi_Db_Table','o',0,1485,'none','',0),(205,'Пункт меню','menu','Indi_Db_Table','o',0,1490,'none','',0),(301,'Компонент содержимого meta-тега','metatag','Indi_Db_Table','o',0,0,'none','',0),(309,'Уведомление','notice','Indi_Db_Table','y',0,2254,'none','',0),(310,'Получатель уведомлений','noticeGetter','Indi_Db_Table','y',0,2275,'none','',0),(308,'Зависимость','consider','Indi_Db_Table','y',0,2247,'none','',0),(311,'Год','year','Indi_Db_Table','y',0,2286,'none','',0),(312,'Месяц','month','Indi_Db_Table','y',0,2289,'none','',0),(313,'Корректировка','changeLog','Indi_Db_Table','y',0,2296,'none','',0),(314,'Очередь задач','queueTask','Indi_Db_Table','y',0,2336,'none','',0),(315,'Сегмент очереди','queueChunk','Indi_Db_Table','y',0,2359,'none','',0),(316,'Элемент очереди','queueItem','Indi_Db_Table','y',0,0,'none','',0),(318,'Рилтайм','realtime','Indi_Db_Table','y',0,2409,'none','',0);
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
) ENGINE=MyISAM AUTO_INCREMENT=1193 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enumset`
--

LOCK TABLES `enumset` WRITE;
/*!40000 ALTER TABLE `enumset` DISABLE KEYS */;
INSERT INTO `enumset` VALUES (1,3,'Нет','n',1),(2,3,'Да','y',2),(5,22,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',5),(6,22,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',6),(9,29,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',9),(10,29,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',10),(11,37,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',11),(12,37,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключена','n',12),(13,42,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',13),(14,42,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',14),(62,66,'Нет','none',62),(63,66,'Только с одним значением ключа','one',63),(64,66,'С набором значений ключей','many',64),(87,111,'Поменять, но с сохранением пропорций','p',87),(88,111,'Поменять','c',88),(89,111,'Не менять','o',89),(91,114,'Ширины','width',91),(92,114,'Высоты','height',92),(95,137,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',95),(96,137,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключена','n',96),(112,345,'Да','y',112),(113,345,'Нет','n',113),(122,0,'Всем друзьям, кроме указанных в разделе \"Исключения из правил доступа на просмотр блога\"','ae',122),(181,470,'<span class=\"i-color-box\" style=\"background: white;\"></span>Нет','none',164),(183,470,'<span class=\"i-color-box\" style=\"background: url(/i/admin/btn-icon-multikey.png);\"></span>Да, несколько ключей','many',296),(184,470,'<span class=\"i-color-box\" style=\"background: url(/i/admin/btn-icon-login.png);\"></span>Да, но только один ключ','one',295),(1076,2159,'<span class=\"i-color-box\" style=\"background: lightgray; border: 1px solid blue;\"></span>Скрыт, но показан в развороте','e',1076),(213,557,'<span class=\"i-color-box\" style=\"background: url(resources/images/grid/sort_asc.png) -5px -1px;\"></span>По возрастанию','ASC',297),(214,557,'<span class=\"i-color-box\" style=\"background: url(resources/images/grid/sort_desc.png) -5px -1px;\"></span>По убыванию','DESC',182),(219,594,'Да','y',299),(220,594,'Нет','n',300),(227,612,'Проектная','n',301),(228,612,'<span style=\'color: red\'>Системная</span>','y',186),(572,1365,'<font color=lime>Типовое</font>','o',461),(571,1365,'<font color=red>Системное</font>','s',460),(570,1365,'Проектное','p',0),(580,1445,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(581,1445,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',464),(979,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',979),(980,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',980),(981,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',981),(982,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',982),(328,0,'Очень плохо','1',254),(480,1040,'Одностроковый','s',408),(478,1027,'Для jQuery.post()','j',407),(479,1040,'Обычный','r',0),(477,1027,'Обычное','r',0),(574,1366,'<font color=red>Системная</font>','s',462),(573,1366,'Проектная','p',0),(575,1366,'<font color=lime>Публичная</font>','o',463),(458,1009,'SQL-выражению','e',396),(457,1009,'Одному из имеющихся столбцов','c',0),(459,1011,'По возрастанию','ASC',0),(460,1011,'По убыванию','DESC',0),(484,1074,'Над записью','r',0),(485,1074,'Над набором записей','rs',411),(486,1074,'Только независимые множества, если нужно','n',412),(567,1364,'Проектная','p',0),(568,1364,'<font color=red>Системная</font>','s',458),(569,1364,'<font color=lime>Публичная</font>','o',459),(969,2176,'Запись','row',969),(968,2176,'Действие','action',968),(967,2176,'Раздел','section',967),(566,612,'<font color=lime>Публичная</font>','o',457),(582,1488,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(583,1488,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',465),(584,1491,'Нет','n',0),(585,1491,'Да','y',466),(586,1494,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(587,1494,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',467),(594,1515,'HTML','html',0),(595,1515,'Строка','string',471),(596,1515,'Текст','textarea',472),(597,1495,'Да','y',0),(598,1495,'Нет','n',473),(608,1533,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',478),(607,1533,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(962,2172,'Title','title',962),(963,2172,'Keywords','keywords',963),(964,2172,'Description','description',964),(965,2173,'Статический','static',965),(966,2173,'Динамический','dynamic',966),(960,2159,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(961,2159,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',489),(983,2202,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',983),(984,2202,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',984),(985,2203,'Всем','all',985),(986,2203,'Никому, кроме','only',986),(987,2203,'Всем, кроме','except',987),(988,2205,'Всем','all',988),(989,2205,'Никому, кроме','only',989),(990,2205,'Всем, кроме','except',990),(991,2207,'Все','all',991),(992,2207,'Никто, кроме','only',992),(993,2207,'Все, кроме','except',993),(994,2210,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','0',994),(995,2210,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','1',995),(996,2212,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Авто','auto',996),(997,2212,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Отображать','yes',997),(998,2212,'<span class=\"i-color-box\" style=\"background: red;\"></span>Не отображать','no',998),(999,2213,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/inherit.png);\"></span>Авто','auto',999),(1000,2213,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Отдельным запросом','yes',1000),(1001,2213,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>В том же запросе','no',1001),(1002,2214,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','auto',1002),(1003,2214,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1003),(1036,2267,'Увеличение','inc',1036),(1037,2267,'Уменьшение','dec',1037),(1032,2258,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',1032),(1033,2258,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',1033),(1034,2261,'Одинаковое для всех получателей','event',1034),(1035,2261,'Неодинаковое, зависит от получателя','getter',1035),(1010,2238,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1010),(1011,2238,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1011),(1012,2239,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключена','n',1012),(1013,2239,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включена','y',1088),(1014,2241,'Существующей','existing',1014),(1015,2241,'Новой','new',1015),(1016,2241,'Любой','any',1016),(1017,2243,'Нет','none',1017),(1018,2243,'DATE','date',1018),(1019,2243,'DATETIME','datetime',1019),(1020,2243,'DATE, TIME','date-time',1020),(1021,2243,'DATE, timeId','date-timeId',1021),(1022,2243,'DATE, dayQty','date-dayQty',1022),(1023,2243,'DATETIME, minuteQty','datetime-minuteQty',1023),(1024,2243,'DATE, TIME, minuteQty','date-time-minuteQty',1024),(1025,2243,'DATE, timeId, minuteQty','date-timeId-minuteQty',1025),(1026,2243,'DATE, hh:mm-hh:mm','date-timespan',1026),(1027,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',1031),(1028,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',1030),(1029,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/inherit.png);\"></span>Без изменений','inherit',1027),(1030,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',1028),(1031,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',1029),(1038,2267,'Изменение','evt',1038),(1039,2276,'Общий','event',1039),(1040,2276,'Раздельный','getter',1040),(1041,2281,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1041),(1042,2281,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1042),(1043,2282,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1043),(1044,2282,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1044),(1045,2283,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1045),(1046,2283,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1046),(1047,2285,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1047),(1048,2285,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1048),(1049,2288,'Январь','01',1049),(1050,2288,'Февраль','02',1050),(1051,2288,'Март','03',1051),(1052,2288,'Апрель','04',1052),(1053,2288,'Май','05',1053),(1054,2288,'Июнь','06',1054),(1055,2288,'Июль','07',1055),(1056,2288,'Август','08',1056),(1057,2288,'Сентябрь','09',1057),(1058,2288,'Октябрь','10',1058),(1059,2288,'Ноябрь','11',1059),(1060,2288,'Декабрь','12',1060),(1061,2301,'Всем пользователям','all',1061),(1062,2301,'Только выбранным','only',1062),(1063,2301,'Всем кроме выбранных','except',1063),(1064,2301,'Никому','none',1064),(1065,2159,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Скрыт','h',1065),(1066,2304,'Пусто','none',1066),(1067,2304,'Сумма','sum',1067),(1068,2304,'Среднее','average',1068),(1069,2304,'Минимум','min',1069),(1070,2304,'Максимум','max',1070),(1071,2304,'Текст','text',1071),(1072,2306,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1072),(1073,2306,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Да','y',1073),(1074,2308,'Обычные','normal',1075),(1075,2308,'Зафиксированные','locked',1074),(1077,2316,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1077),(1078,2316,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1078),(1079,22,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Скрыт','h',1079),(1080,2318,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1080),(1081,2318,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1081),(1082,2319,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1082),(1083,2319,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1083),(1084,2321,'Проектное','p',1084),(1085,2321,'<font color=red>Системное</font>','s',1085),(1086,2324,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1086),(1087,2324,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включено','y',1087),(1088,2239,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1013),(1089,2239,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1089),(1090,2325,'Ничего','noth',1090),(1091,2325,'Чтото','smth',1091),(1092,2328,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1092),(1093,2328,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1093),(1094,2328,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1094),(1095,2328,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1095),(1096,2329,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1096),(1097,2329,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1097),(1098,2329,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1098),(1099,2329,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1099),(1100,2331,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1100),(1101,2331,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1101),(1102,2331,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1102),(1103,2331,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1103),(1104,2332,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1104),(1105,2332,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1105),(1106,2332,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1106),(1107,2332,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1107),(1108,2333,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1108),(1109,2333,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1109),(1110,2333,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1110),(1111,2333,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1111),(1112,2334,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1112),(1113,2334,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1113),(1114,2334,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1114),(1115,2334,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1115),(1116,2342,'Оценка масштабов','count',1116),(1117,2342,'Создание очереди','items',1117),(1118,2342,'Процессинг очереди','queue',1118),(1119,2342,'Применение результатов','apply',1119),(1120,2343,'Ожидание','waiting',1120),(1121,2343,'В работе','progress',1121),(1122,2343,'Завершено','finished',1122),(1123,2346,'Ожидание','waiting',1123),(1124,2346,'В работе','progress',1124),(1125,2346,'Завершено','finished',1125),(1126,2349,'Ожидание','waiting',1126),(1127,2349,'В работе','progress',1127),(1128,2349,'Завершено','finished',1128),(1129,2353,'Ожидание','waiting',1129),(1130,2353,'В работе','progress',1130),(1131,2353,'Завершено','finished',1131),(1132,2353,'Не требуется','noneed',1132),(1133,2356,'Ожидание','waiting',1133),(1134,2356,'В работе','progress',1134),(1135,2356,'Завершено','finished',1135),(1136,2362,'Ожидание','waiting',1136),(1137,2362,'В работе','progress',1137),(1138,2362,'Завершено','finished',1138),(1139,2365,'Ожидание','waiting',1139),(1140,2365,'В работе','progress',1140),(1141,2365,'Завершено','finished',1141),(1142,2368,'Ожидание','waiting',1142),(1143,2368,'В работе','progress',1143),(1144,2368,'Завершено','finished',1144),(1145,2368,'Не требуется','noneed',1145),(1146,2371,'Ожидание','waiting',1146),(1147,2371,'В работе','progress',1147),(1148,2371,'Завершено','finished',1148),(1149,2378,'Добавлен','items',1149),(1150,2378,'Обработан','queue',1150),(1151,2378,'Применен','apply',1151),(1152,2381,'Не указана','none',1152),(1153,2381,'AdminSystemUi','adminSystemUi',1153),(1154,2381,'AdminCustomUi','adminCustomUi',1154),(1155,2381,'AdminCustomData','adminCustomData',1155),(1156,2384,'Проектная','p',1156),(1158,2386,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключена','n',1158),(1157,2384,'<font color=red>Системная</font>','s',1157),(1159,2386,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1159),(1160,2386,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включена','y',1160),(1161,2386,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1161),(1169,2397,'Сессия','session',1169),(1170,2397,'Вкладка','channel',1170),(1171,2397,'Контекст','context',1171),(1172,2410,'Не применимо','none',1172),(1173,2410,'Набор записей','rowset',1173),(1174,2410,'Одна запись','row',1174),(1176,1345,'<span class=\"i-color-box\" style=\"background: transparent;\"></span>Нет','0',1176),(1177,1345,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-create-deny.png);\"></span>Да','1',1177),(1178,2312,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-single-select.png);\"></span>Нет','0',1178),(1179,2312,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-multi-select.png);\"></span>Да','1',1179),(1180,2310,'<span class=\"i-color-box\" style=\"background: transparent;\"></span>Нет','0',1180),(1181,2310,'<span class=\"i-color-box\" style=\"background: url(resources/images/icons/btn-icon-numberer.png);\"></span>Да','1',1181),(1182,2100,'Нет','0',1182),(1183,2100,'Да','1',1183),(1184,2432,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1184),(1185,2432,'<span class=\"i-color-box\" style=\"background: blue;\"></span> Да','y',1185),(1186,2451,'Месяц','month',1186),(1187,2451,'День недели','week',1187),(1188,2465,'Нет','none',1188),(1189,2465,'Email','email',1189),(1190,2465,'URL','url',1190),(1191,2478,'Месяц','month',1191),(1192,2478,'День недели','week',1192);
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
  `entry` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `columnTypeId` (`columnTypeId`),
  KEY `elementId` (`elementId`),
  KEY `relation` (`relation`),
  KEY `storeRelationAbility` (`storeRelationAbility`),
  KEY `mode` (`mode`),
  KEY `l10n` (`l10n`),
  KEY `entry` (`entry`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=2479 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field`
--

LOCK TABLES `field` WRITE;
/*!40000 ALTER TABLE `field` DISABLE KEYS */;
INSERT INTO `field` VALUES (1,1,'Наименование','title',1,1,'',1,0,'none','','required','','n',0),(2,1,'Тип столбца MySQL','type',1,1,'',2,0,'none','','required','','n',0),(3,1,'Можно хранить ключи','canStoreRelation',10,5,'n',3,6,'one','','regular','','n',0),(4,2,'Наименование','title',1,1,'',4,0,'none','','required','','n',0),(5,2,'Таблица БД','table',1,1,'',5,0,'none','','required','','n',0),(6,5,'Сущность','entityId',3,23,'0',6,2,'one','','readonly','','n',0),(2413,5,'Внешние ключи','fk',0,16,'',10,0,'none','','regular','','n',0),(7,5,'Наименование','title',1,1,'',8,0,'none','','required','','n',0),(8,5,'Псевдоним','alias',1,1,'',9,0,'none','','required','','n',0),(2420,195,'Доступ','accesss',0,16,'',2204,0,'none','','regular','','n',0),(9,5,'Тип столбца MySQL','columnTypeId',3,23,'0',2412,1,'one','','regular','','n',0),(10,5,'Элемент управления','elementId',3,23,'0',414,4,'one','','required','','n',0),(11,5,'Значение по умолчанию','defaultValue',1,1,'',2413,0,'none','','regular','','n',0),(12,5,'Ключи какой сущности','relation',3,23,'0',12,2,'one','','regular','','n',0),(14,5,'Порядок','move',3,4,'0',2435,0,'none','','regular','','n',0),(15,6,'Поле','fieldId',3,23,'0',15,5,'one','','readonly','','n',0),(16,6,'Наименование','title',4,1,'',16,0,'none','','required','','n',0),(17,6,'Псевдоним','alias',1,1,'',17,0,'none','','required','','n',0),(18,3,'Вышестоящий раздел','sectionId',3,23,'0',22,3,'one','','regular','','n',0),(19,3,'Сущность','entityId',3,23,'0',514,2,'one','','regular','','n',0),(2304,9,'Внизу','summaryType',10,23,'none',2308,6,'one','','regular','','n',0),(2198,195,'Непустой результат','consistence',12,9,'1',2419,0,'none','','regular','','n',0),(20,3,'Наименование','title',4,1,'',18,0,'none','','required','','y',0),(21,3,'Контроллер','alias',1,1,'',19,0,'none','','required','','n',0),(22,3,'Статус','toggle',10,23,'y',20,6,'one','','regular','','n',0),(23,3,'Порядок','move',3,4,'',1278,0,'none','','regular','','n',0),(25,3,'Количество записей на странице','rowsOnPage',3,1,'25',2310,0,'none','','regular','','n',0),(26,8,'Раздел','sectionId',3,23,'',1,3,'one','','readonly','','n',0),(27,8,'Действие','actionId',3,23,'',1,7,'one','','required','','n',0),(28,8,'Доступ','profileIds',1,7,'14',1,10,'many','','regular','','n',0),(2428,3,'Записи','store',0,16,'',2429,0,'none','','hidden','','n',0),(29,8,'Статус','toggle',10,23,'y',1,6,'one','','regular','','n',0),(30,8,'Порядок','move',3,4,'',1,0,'none','','regular','','n',0),(31,7,'Наименование','title',4,1,'',26,0,'none','','required','','y',0),(32,7,'Псевдоним','alias',1,1,'',27,0,'none','','required','','n',0),(33,9,'Раздел','sectionId',3,23,'',28,3,'one','','readonly','','n',0),(34,9,'Поле','fieldId',3,23,'',29,5,'one','','required','','n',0),(35,9,'Порядок','move',3,4,'',2165,0,'none','','regular','','n',0),(36,10,'Наименование','title',1,1,'',31,0,'none','','required','','n',0),(37,10,'Статус','toggle',10,23,'y',1271,6,'one','','regular','','n',0),(38,11,'Роль','profileId',3,23,'',33,10,'one','','readonly','','n',0),(39,11,'Имя','title',1,1,'',34,0,'none','','required','','n',0),(40,11,'Логин','email',1,1,'',35,0,'none','','required','','n',0),(41,11,'Пароль','password',1,1,'',36,0,'none','','required','','n',0),(42,11,'Статус','toggle',10,23,'y',37,6,'one','','regular','','n',0),(64,4,'Наименование','title',1,1,'',53,0,'none','','required','','n',0),(65,4,'Псевдоним','alias',1,1,'',2456,0,'none','','required','','n',0),(66,4,'Совместимость с внешними ключами','storeRelationAbility',11,23,'none',2457,6,'many','','regular','','n',0),(1445,195,'Статус','toggle',10,23,'y',2184,6,'one','','regular','','n',0),(92,4,'Не отображать в формах','hidden',12,9,'0',2460,0,'none','','regular','','n',0),(106,20,'Поле','fieldId',3,23,'0',86,5,'one','','required','','n',0),(107,20,'Наименование','title',1,1,'',87,0,'none','','required','','n',0),(108,20,'Псевдоним','alias',1,1,'',88,0,'none','','required','','n',0),(109,20,'Ширина','masterDimensionValue',3,18,'0',91,0,'none','','regular','','n',0),(110,20,'Высота','slaveDimensionValue',3,18,'0',93,0,'none','','regular','','n',0),(111,20,'Размер','proportions',10,5,'o',89,6,'one','','regular','','n',0),(112,20,'Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>','slaveDimensionLimitation',12,9,'1',92,0,'none','','regular','','n',0),(114,20,'При расчете пропорций отталкиваться от','masterDimensionAlias',10,5,'width',90,6,'one','','regular','','n',0),(131,25,'Наименование','title',1,1,'',101,0,'none','','required','','n',0),(133,25,'Псевдоним','alias',1,1,'',102,0,'none','','required','','n',0),(137,25,'Статус','toggle',10,5,'y',2208,6,'one','','regular','','n',0),(345,7,'Нужно выбрать запись','rowRequired',10,5,'y',308,6,'one','','regular','','n',0),(377,6,'Порядок','move',3,4,'0',338,0,'none','','regular','','n',0),(470,5,'Хранит ключи','storeRelationAbility',10,23,'none',11,6,'one','','regular','','n',0),(2475,4,'Максимальная длина в символах','maxlength',3,18,'0',2473,0,'none','','regular','','n',1),(2474,4,'Во всю ширину','wide',12,9,'0',2462,0,'none','','regular','','n',23),(2473,4,'Заголовочное поле','titleColumn',3,23,'0',2463,5,'one','','regular','','n',23),(475,1,'Совместимые элементы управления','elementId',1,23,'',433,4,'many','','required','','n',0),(476,91,'Поле','fieldId',3,23,'0',434,5,'one','','required','','n',0),(2472,4,'Формат отображения','displayFormat',1,1,'H:i',2476,0,'none','','regular','','n',17),(2470,4,'Во всю ширину','wide',12,9,'0',2475,0,'none','','regular','','n',6),(502,3,'PHP','extendsPhp',1,1,'Indi_Controller_Admin',461,0,'none','','regular','Родительский класс PHP','n',0),(2309,3,'JS','extendsJs',1,1,'Indi.lib.controller.Controller',443,0,'none','','regular','Родительский класс JS','n',0),(503,3,'Сортировка','defaultSortField',3,23,'0',2303,5,'one','','regular','','n',0),(2212,8,'Южная панель','south',10,23,'auto',2212,6,'one','','regular','','n',0),(555,2,'Родительский класс PHP','extends',1,1,'Indi_Db_Table',512,0,'none','','required','','n',0),(557,3,'Направление сортировки','defaultSortDirection',10,23,'ASC',2309,6,'one','','regular','','n',0),(559,101,'Наименование','title',1,1,'',517,0,'none','','required','','n',0),(560,101,'Псевдоним','alias',1,1,'',519,0,'none','','required','','n',0),(563,101,'Прикрепленная сущность','entityId',3,23,'0',520,2,'one','','regular','','n',0),(581,101,'Соответствующий раздел бэкенда','sectionId',3,23,'0',534,3,'one','','regular','','n',0),(585,101,'Порядок отображения соответствующего пункта в меню','move',3,4,'0',950,0,'none','','regular','','n',0),(594,20,'Изменить оттенок','changeColor',10,5,'n',545,6,'one','','regular','','n',0),(595,20,'Оттенок','color',13,11,'',546,0,'none','','regular','','n',0),(612,2,'Фракция','system',10,23,'n',559,6,'one','','regular','','n',0),(678,128,'Имя','title',1,1,'',625,0,'none','','required','','n',0),(679,128,'Email','email',1,1,'',626,0,'none','','required','','n',0),(680,128,'Сообщение','message',4,6,'',627,0,'none','','required','','n',0),(681,128,'Дата','date',9,19,'<?=date(\'Y-m-d H:i:s\')?>',628,0,'none','','regular','','n',0),(1364,7,'Фракция','type',10,5,'p',2099,6,'one','','regular','','n',0),(1365,146,'Тип','type',10,5,'p',1293,6,'one','','regular','','n',0),(1444,195,'Порядок','move',3,4,'0',1316,0,'none','','regular','','n',0),(1442,195,'Раздел','sectionId',3,23,'0',1313,3,'one','','readonly','','n',0),(1443,195,'Поле','fieldId',3,23,'0',1314,5,'one','`elementId` NOT IN (4,14,16,20,22)','required','','n',0),(2419,195,'Флаги','flags',0,16,'',2317,0,'none','','regular','','n',0),(1441,2,'Включить в кэш','useCache',12,23,'0',1312,0,'none','','hidden','','n',0),(754,5,'Статическая фильтрация','filter',1,1,'',13,0,'none','','regular','','n',0),(767,3,'Фильтрация через SQL WHERE','filter',1,1,'',2213,0,'none','','regular','','n',0),(857,146,'Наименование','title',1,1,'',803,0,'none','','required','','n',0),(858,146,'Псевдоним','alias',1,1,'',804,0,'none','','required','','n',0),(859,147,'Раздел фронтенда','fsectionId',3,23,'0',805,101,'one','','required','','n',0),(860,147,'Действие','factionId',3,23,'0',806,146,'one','','required','','n',0),(868,101,'Вышестоящий раздел','fsectionId',3,5,'0',516,101,'one','','regular','','n',0),(869,101,'Статическая фильтрация','filter',1,1,'',814,0,'none','','regular','','n',0),(960,101,'Количество строк для отображения по умолчанию','defaultLimit',3,18,'20',951,0,'none','','regular','','n',0),(1366,3,'Фракция','type',10,23,'p',21,6,'one','','regular','','n',0),(1009,101,'По умолчанию сортировка по','orderBy',10,5,'c',952,6,'one','','regular','','n',0),(1010,101,'Столбец сортировки','orderColumn',3,23,'0',953,5,'one','','regular','','n',0),(1011,101,'Направление сортировки','orderDirection',10,5,'ASC',981,6,'one','','regular','','n',0),(1012,101,'SQL-выражение','orderExpression',1,1,'',982,0,'none','','regular','','n',0),(1027,147,'Тип','type',10,5,'r',968,6,'one','','regular','','n',0),(1040,101,'Тип','type',10,5,'r',538,6,'one','','regular','','n',0),(1041,101,'Где брать идентификатор','where',1,1,'',983,0,'none','','regular','','n',0),(1042,101,'Действие по умолчанию','index',1,1,'',1403,0,'none','','regular','','n',0),(1074,146,'Выполнять maintenance()','maintenance',10,5,'r',1015,6,'one','','regular','','n',0),(2414,5,'Элемент управления','el',0,16,'',95,0,'none','','regular','','n',0),(1191,147,'Не указывать действие при создании seo-урлов из системных','blink',12,9,'0',1259,0,'none','','regular','','n',0),(1192,162,'Раздел фронтенда','fsectionId',3,23,'0',1127,101,'one','','required','','n',0),(1193,162,'Действие в разделе фронтенда','fsection2factionId',3,23,'0',1128,147,'one','','required','','n',0),(1194,162,'Компонент','entityId',3,23,'0',1129,2,'one','','required','','n',0),(1195,162,'Очередность','move',3,4,'0',1130,0,'none','','regular','','n',0),(1196,162,'Префикс','prefix',1,1,'',1131,0,'none','','regular','','n',0),(2196,9,'Вышестоящий столбец','gridId',3,23,'0',1738,9,'one','`sectionId` = \"<?=$this->sectionId?>\"','regular','','n',0),(2310,3,'Включить нумерацию записей','rownumberer',10,23,'0',2323,6,'one','','regular','','n',0),(2184,195,'Игнорировать шаблон опций','ignoreTemplate',12,9,'1',2421,0,'none','','regular','','n',0),(2183,195,'Фильтрация','filter',1,1,'',1520,0,'none','','regular','','n',0),(2176,301,'Источник','source',10,23,'section',2176,6,'one','','regular','','n',0),(2177,301,'Сущность','entityId',3,23,'0',2177,2,'one','`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)','regular','','n',0),(2178,301,'Поле','fieldId',3,23,'0',2178,5,'one','','regular','','n',0),(2179,301,'Префикс','prefix',1,1,'',2179,0,'none','','regular','','n',0),(2180,301,'Постфикс','postfix',1,1,'',2180,0,'none','','regular','','n',0),(2181,301,'Порядок отображения','move',3,4,'0',2181,0,'none','','regular','','n',0),(2182,195,'Значение по умолчанию','defaultValue',1,1,'',2167,0,'none','','regular','','n',0),(1325,147,'Переименовать действие при генерации seo-урла','rename',12,9,'0',1260,0,'none','','regular','','n',0),(1326,147,'Псевдоним','alias',1,1,'',1261,0,'none','','regular','','n',0),(1327,147,'Настройки SEO','seoSettings',0,16,'',1126,0,'none','','regular','','n',0),(1337,10,'Сущность пользователей','entityId',3,23,'11',2131,2,'one','`system`= \"n\"','regular','','n',0),(2422,9,'Доступ','accesss',0,16,'',2315,0,'none','','regular','','n',0),(1341,171,'Раздел','sectionId',3,23,'0',1275,3,'one','','readonly','','n',0),(1342,171,'Поле','fieldId',3,23,'0',1276,5,'one','','required','','n',0),(2251,171,'Изменить свойства','alter',0,16,'',2250,0,'none','','regular','','n',0),(1345,3,'Запретить создание новых записей','disableAdd',10,23,'0',2211,6,'one','','regular','','n',0),(1509,204,'Ширина','detailsHtmlWidth',3,18,'0',1383,0,'none','','regular','','n',0),(1532,171,'Значение по умолчанию','defaultValue',1,1,'',2424,0,'none','','regular','','n',0),(1485,204,'Наименование','title',1,1,'',1356,0,'none','','required','','n',0),(1486,204,'Псевдоним','alias',1,1,'',1357,0,'none','','required','','n',0),(1487,204,'Значение','detailsHtml',4,13,'',1382,0,'none','','regular','','n',0),(1488,204,'Статус','toggle',10,5,'y',1358,6,'one','','regular','','n',0),(1489,205,'Вышестояший пункт','menuId',3,23,'0',1359,205,'one','','regular','','n',0),(1490,205,'Наименование','title',1,1,'',1360,0,'none','','required','','n',0),(1491,205,'Связан со статической страницей','linked',10,5,'n',1361,6,'one','','regular','','n',0),(1492,205,'Статическая страница','staticpageId',3,23,'0',1362,25,'one','','regular','','n',0),(1493,205,'Ссылка','url',1,1,'',1363,0,'none','','regular','','n',0),(1494,205,'Статус','toggle',10,23,'y',1364,6,'one','','regular','','n',0),(1495,205,'Отображать в нижнем меню','bottom',10,5,'y',1365,6,'one','','regular','','n',0),(1496,205,'Порядок отображения','move',3,4,'0',1366,0,'none','','regular','','n',0),(1814,25,'Контент','details',4,13,'',1667,0,'none','','regular','','n',0),(1510,204,'Контент','detailsSpan',0,16,'',1379,0,'none','','regular','','n',0),(1511,204,'Высота','detailsHtmlHeight',3,18,'200',1384,0,'none','','regular','','n',0),(1513,204,'Css класс для body','detailsHtmlBodyClass',1,1,'',1385,0,'none','','regular','','n',0),(1514,204,'Css стили','detailsHtmlStyle',4,6,'',1386,0,'none','','regular','','n',0),(1515,204,'Тип','type',10,5,'html',1380,6,'one','','regular','','n',0),(1516,204,'Значение','detailsString',1,1,'',1387,0,'none','','regular','','n',0),(1517,204,'Значение','detailsTextarea',4,6,'',1557,0,'none','','regular','','n',0),(2173,301,'Тип компонента','type',10,23,'static',2173,6,'one','','regular','','n',0),(2174,301,'Указанный вручную','content',1,1,'',2174,0,'none','','regular','','n',0),(2175,301,'Шагов вверх','up',3,18,'0',2175,0,'none','','regular','','n',0),(2172,301,'Тэг','tag',10,23,'title',2172,6,'one','','regular','','n',0),(2171,301,'Действие','fsection2factionId',3,23,'0',2171,147,'one','','required','','n',0),(2170,301,'Раздел','fsectionId',3,23,'0',2170,101,'one','','required','','n',0),(1533,101,'Статус','toggle',10,5,'y',1429,6,'one','','regular','','n',0),(1554,3,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',1277,5,'one','`storeRelationAbility`!=\"none\"','regular','','n',0),(1560,101,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',815,5,'one','','regular','','n',0),(1562,101,'От какого класса наследовать класс контроллера','extends',1,1,'',1431,0,'none','','regular','','n',0),(2412,5,'MySQL','mysql',0,16,'',523,0,'none','','regular','','n',0),(1658,195,'Переименовать','alt',1,1,'',2198,0,'none','','regular','','n',0),(2132,10,'Порядок','move',3,4,'0',2215,0,'none','','regular','','n',0),(1886,9,'Переименовать','alterTitle',1,1,'',2210,0,'none','','regular','','n',0),(2100,7,'Отображать в панели действий','display',10,23,'1',2100,6,'one','','regular','','n',0),(2131,10,'Дэшборд','dashboard',1,1,'',2132,0,'none','','regular','','n',0),(2159,9,'Статус','toggle',10,23,'y',2205,6,'one','','regular','','n',0),(2161,171,'Режим','mode',10,23,'inherit',2252,6,'one','','regular','','n',0),(2166,91,'Auto title','title',1,1,'',2166,0,'none','','hidden','','n',0),(2163,2,'Заголовочное поле','titleFieldId',3,23,'0',2163,5,'one','`entityId` = \"<?=$this->id?>\" AND `columnTypeId` != \"0\"','regular','','n',0),(2164,8,'Auto title','title',4,1,'',2164,0,'none','','hidden','','y',0),(2165,9,'Auto title','title',1,1,'',2196,0,'none','','hidden','','n',0),(2167,195,'Auto title','title',1,1,'',2182,0,'none','','hidden','','n',0),(2168,147,'Auto title','title',1,1,'',2242,0,'none','','hidden','','n',0),(2169,171,'Auto title','title',1,1,'',1402,0,'none','','hidden','','n',0),(2197,5,'Режим','mode',10,23,'regular',413,6,'one','','regular','','n',0),(2199,5,'Подсказка','tooltip',4,6,'',428,0,'none','','regular','','n',0),(2200,9,'Подсказка','tooltip',4,6,'',2304,0,'none','','regular','','n',0),(2203,195,'Доступ','access',10,23,'all',2311,6,'one','','regular','','n',0),(2202,7,'Статус','toggle',10,23,'y',2202,6,'one','','regular','','n',0),(2204,195,'Кроме','profileIds',1,7,'',2314,10,'many','','regular','','n',0),(2205,9,'Доступ','access',10,23,'all',2422,6,'one','','regular','','n',0),(2206,9,'Кроме','profileIds',1,7,'',2423,10,'many','','regular','','n',0),(2207,171,'Влияние','impact',10,23,'all',2169,6,'one','','regular','','n',0),(2208,25,'Родительская страница','staticpageId',3,23,'0',1666,25,'one','','regular','','n',0),(2209,8,'Переименовать','rename',4,1,'',2209,0,'none','','regular','','y',0),(2210,9,'Редактор','editor',10,23,'0',2206,6,'one','','regular','','n',0),(2211,3,'Группировка','groupBy',3,23,'0',2425,5,'one','','regular','','n',0),(2213,3,'Режим подгрузки','rowsetSeparate',10,23,'auto',2302,6,'one','','regular','','n',0),(2214,8,'Автосайз окна','fitWindow',10,23,'auto',2214,6,'one','','regular','','n',0),(2215,10,'Максимальное количество окон','maxWindows',3,18,'15',2319,0,'none','','regular','','n',0),(2270,309,'Заголовок','tplDecSubj',4,1,'',2271,0,'none','','regular','','n',0),(2269,309,'Текст','tplIncBody',4,6,'',2270,0,'none','','regular','','n',0),(2268,309,'Заголовок','tplIncSubj',4,1,'',2269,0,'none','','regular','','n',0),(2267,309,'Назначение','tplFor',10,23,'inc',2268,6,'one','','regular','','n',0),(2266,309,'Сообщение','tpl',0,16,'',2267,0,'none','','regular','','n',0),(2263,309,'Цвет фона','bg',13,11,'212#d9e5f3',2264,0,'none','','regular','','n',0),(2264,309,'Цвет текста','fg',13,11,'216#044099',2265,0,'none','','regular','','n',0),(2265,309,'Подсказка','tooltip',4,6,'',2266,0,'none','','regular','','n',0),(2262,309,'Пункты меню','sectionId',1,23,'',2263,3,'many','FIND_IN_SET(`sectionId`, \"<?=m(\'Section\')->fetchAll(\'`sectionId` = \"0\"\')->column(\'id\', true)?>\")','regular','','n',0),(2255,309,'Сущность','entityId',3,23,'0',2256,2,'one','','required','','n',0),(2256,309,'Событие / PHP','event',1,1,'',2257,0,'none','','regular','','n',0),(2257,309,'Получатели','profileId',1,23,'',2258,10,'many','','required','','n',0),(2258,309,'Статус','toggle',10,23,'y',2259,6,'one','','regular','','n',0),(2259,309,'Счетчик','qty',0,16,'',2260,0,'none','','regular','','n',0),(2260,309,'Отображение / SQL','qtySql',1,1,'',2261,0,'none','','required','','n',0),(2261,309,'Направление изменения','qtyDiffRelyOn',10,23,'event',2262,6,'one','','regular','','n',0),(2254,309,'Наименование','title',1,1,'',2254,0,'none','','required','','n',0),(2236,307,'Наименование','title',1,1,'',2236,0,'none','','required','','n',0),(2237,307,'Ключ','alias',1,1,'',2237,0,'none','','required','','n',0),(2238,307,'Статус','toggle',10,23,'y',2238,6,'one','','regular','','n',0),(2239,5,'Мультиязычность','l10n',10,23,'n',2414,6,'one','','regular','','n',0),(2240,147,'Разрешено не передавать id в uri','allowNoid',12,9,'0',2168,0,'none','','regular','','n',0),(2241,147,'Над записью','row',10,5,'existing',2240,6,'one','','regular','','n',0),(2242,147,'Где брать идентификатор','where',1,1,'',2241,0,'none','','regular','','n',0),(2243,2,'Паттерн комплекта календарных полей','spaceScheme',10,23,'none',2243,6,'one','','regular','','n',0),(2244,2,'Комплект календарных полей','spaceFields',1,23,'',2244,5,'many','`entityId` = \"<?=$this->id?>\"','regular','','n',0),(2245,308,'Сущность','entityId',3,23,'0',2245,2,'one','','hidden','','n',0),(2246,308,'Поле','fieldId',3,23,'0',2246,5,'one','','readonly','','n',0),(2247,308,'От какого поля зависит','consider',3,23,'0',2247,5,'one','`id` != \"<?=$this->fieldId?>\" AND `columnTypeId` != \"0\"','required','','n',0),(2248,308,'Поле по ключу','foreign',3,23,'0',2248,5,'one','','regular','','n',0),(2249,308,'Auto title','title',1,1,'',2249,0,'none','','hidden','','n',0),(2250,171,'Кроме','profileIds',1,7,'',2207,10,'many','','regular','','n',0),(2252,171,'Наименование','rename',1,1,'',2251,0,'none','','regular','','n',0),(2424,171,'Влияние','impactt',0,16,'',2134,0,'none','','regular','','n',0),(2271,309,'Текст','tplDecBody',4,6,'',2272,0,'none','','regular','','n',0),(2272,309,'Заголовок','tplEvtSubj',4,1,'',2273,0,'none','','regular','','n',0),(2273,309,'Сообщение','tplEvtBody',4,6,'',2321,0,'none','','regular','','n',0),(2274,310,'Уведомление','noticeId',3,23,'0',2275,309,'one','','readonly','','n',0),(2275,310,'Роль','profileId',3,23,'0',2276,10,'one','','readonly','','n',0),(2276,310,'Критерий','criteriaRelyOn',10,5,'event',2277,6,'one','','regular','','n',0),(2277,310,'Общий','criteriaEvt',1,1,'',2278,0,'none','','regular','','n',0),(2278,310,'Для увеличения','criteriaInc',1,1,'',2279,0,'none','','regular','','n',0),(2279,310,'Для уменьшения','criteriaDec',1,1,'',2280,0,'none','','regular','','n',0),(2280,310,'Ауто титле','title',1,1,'',2281,0,'none','','hidden','','n',0),(2281,310,'Дублирование на почту','email',10,23,'n',2282,6,'one','','regular','','n',0),(2282,310,'Дублирование в ВК','vk',10,23,'n',2283,6,'one','','regular','','n',0),(2283,310,'Дублирование по SMS','sms',10,23,'n',2284,6,'one','','regular','','n',0),(2284,310,'Критерий','criteria',1,1,'',2285,0,'none','','hidden','','n',0),(2285,310,'Дублирование на почту','mail',10,23,'n',2316,6,'one','','hidden','','n',0),(2286,311,'Наименование','title',1,1,'',2286,0,'none','','required','','n',0),(2287,312,'Год','yearId',3,23,'0',2287,311,'one','','required','','n',0),(2288,312,'Месяц','month',10,23,'01',2288,6,'one','','regular','','n',0),(2289,312,'Наименование','title',1,1,'',2289,0,'none','','regular','','n',0),(2290,312,'Порядок','move',3,4,'0',2290,0,'none','','regular','','n',0),(2291,313,'Сущность','entityId',3,23,'0',2291,2,'one','','readonly','','n',0),(2292,313,'Объект','key',3,23,'0',2292,0,'one','','readonly','','n',0),(2293,313,'Что изменено','fieldId',3,23,'0',2293,5,'one','`columnTypeId` != \"0\"','readonly','','n',0),(2294,313,'Было','was',4,13,'',2294,0,'none','','readonly','','n',0),(2295,313,'Стало','now',4,13,'',2295,0,'none','','readonly','','n',0),(2296,313,'Когда','datetime',9,19,'0000-00-00 00:00:00',2296,0,'none','','readonly','','n',0),(2297,313,'Месяц','monthId',3,23,'0',2297,312,'one','','readonly','','n',0),(2298,313,'Тип автора','changerType',3,23,'0',2298,2,'one','','readonly','','n',0),(2299,313,'Автор','changerId',3,23,'0',2299,0,'one','','readonly','','n',0),(2300,313,'Роль','profileId',3,23,'0',2300,10,'one','','readonly','','n',0),(2301,3,'Разворачивать пункт меню','expand',10,23,'all',23,6,'one','','regular','','n',0),(2302,3,'Выбранные','expandRoles',1,23,'',25,10,'many','','regular','','n',0),(2303,3,'Доступ','roleIds',1,23,'',2428,10,'many','','hidden','','n',0),(2305,9,'Текст','summaryText',1,1,'',2313,0,'none','','regular','','n',0),(2306,308,'Обязательное','required',10,23,'n',2306,6,'one','','regular','','n',0),(2307,308,'Коннектор','connector',3,23,'0',2307,5,'one','','regular','','n',0),(2308,9,'Группа','group',10,23,'normal',2253,6,'one','','regular','','n',0),(2311,195,'Разрешить сброс','allowClear',12,9,'1',2420,0,'none','','regular','','n',0),(2312,3,'Выделение более одной записи','multiSelect',10,23,'0',2322,6,'one','','regular','','n',0),(2313,9,'Поле по ключу','further',3,23,'0',30,5,'one','','regular','','n',0),(2314,195,'Поле по ключу','further',3,23,'0',1315,5,'one','','regular','','n',0),(2315,9,'Ширина','width',3,18,'0',2305,0,'none','','regular','','n',0),(2316,310,'Статус','toggle',10,23,'y',2274,6,'one','','regular','','n',0),(2317,195,'Подсказка','tooltip',4,6,'',2203,0,'none','','regular','','n',0),(2318,11,'Демо-режим','demo',10,23,'n',2318,6,'one','','regular','','n',0),(2319,10,'Демо-режим','demo',10,23,'n',2384,6,'one','','regular','','n',0),(2320,2,'Группировать файлы','filesGroupBy',3,23,'0',2320,5,'one','`entityId` = \"<?=$this->id?>\" AND `storeRelationAbility` = \"one\"','regular','','n',0),(2321,309,'Тип','type',10,23,'p',2255,6,'one','','regular','','n',0),(2322,3,'Плитка','tileField',3,23,'0',2426,5,'one','`elementId` = \"14\"','regular','','n',0),(2323,3,'Превью','tileThumb',3,23,'0',2427,20,'one','','regular','','n',0),(2324,11,'Правки UI','uiedit',10,23,'n',2324,6,'one','','regular','','n',0),(2325,307,'Состояние','state',10,23,'noth',2325,6,'one','','readonly','','n',0),(2326,307,'Админка','admin',0,16,'',2326,0,'none','','regular','','n',0),(2327,307,'Система','adminSystem',0,16,'',2327,0,'none','','regular','','n',0),(2328,307,'Интерфейс','adminSystemUi',10,5,'n',2328,6,'one','','regular','','n',0),(2329,307,'Константы','adminSystemConst',10,5,'n',2329,6,'one','','regular','','n',0),(2330,307,'Проект','adminCustom',0,16,'',2330,0,'none','','regular','','n',0),(2331,307,'Интерфейс','adminCustomUi',10,5,'n',2331,6,'one','','regular','','n',0),(2332,307,'Константы','adminCustomConst',10,5,'n',2332,6,'one','','regular','','n',0),(2333,307,'Данные','adminCustomData',10,5,'n',2333,6,'one','','regular','','n',0),(2334,307,'Шаблоны','adminCustomTmpl',10,5,'n',2334,6,'one','','regular','','n',0),(2335,307,'Порядок','move',3,4,'0',2335,0,'none','','regular','','n',0),(2336,314,'Задача','title',1,1,'',2336,0,'none','','required','','n',0),(2337,314,'Создана','datetime',9,19,'<?=date(\'Y-m-d H:i:s\')?>',2337,0,'none','','readonly','','n',0),(2338,314,'Параметры','params',4,6,'',2338,0,'none','','regular','','n',0),(2339,314,'Процесс','proc',0,16,'',2339,0,'none','','regular','','n',0),(2340,314,'Начат','procSince',9,19,'0000-00-00 00:00:00',2340,0,'none','','regular','','n',0),(2341,314,'PID','procID',3,18,'0',2341,0,'none','','readonly','','n',0),(2342,314,'Этап','stage',10,23,'count',2342,6,'one','','regular','','n',0),(2343,314,'Статус','state',10,23,'waiting',2343,6,'one','','regular','','n',0),(2344,314,'Сегменты','chunk',3,18,'0',2345,0,'none','','regular','','n',0),(2345,314,'Оценка','count',0,16,'',2346,0,'none','','regular','','n',0),(2346,314,'Статус','countState',10,23,'waiting',2347,6,'one','','readonly','','n',0),(2347,314,'Размер','countSize',3,18,'0',2348,0,'none','','readonly','','n',0),(2348,314,'Создание','items',0,16,'',2349,0,'none','','regular','','n',0),(2349,314,'Статус','itemsState',10,23,'waiting',2350,6,'one','','readonly','','n',0),(2350,314,'Размер','itemsSize',3,18,'0',2351,0,'none','','readonly','','n',0),(2351,314,'Байт','itemsBytes',3,18,'0',2352,0,'none','','regular','','n',0),(2352,314,'Процессинг','queue',0,16,'',2353,0,'none','','regular','','n',0),(2353,314,'Статус','queueState',10,23,'waiting',2354,6,'one','','readonly','','n',0),(2354,314,'Размер','queueSize',3,18,'0',2355,0,'none','','regular','','n',0),(2355,314,'Применение','apply',0,16,'',2356,0,'none','','regular','','n',0),(2356,314,'Статус','applyState',10,23,'waiting',2357,6,'one','','readonly','','n',0),(2357,314,'Размер','applySize',3,18,'0',2382,0,'none','','readonly','','n',0),(2358,315,'Очередь задач','queueTaskId',3,23,'0',2358,314,'one','','regular','','n',0),(2359,315,'Расположение','location',1,1,'',2359,0,'none','','regular','','n',0),(2360,315,'Условие выборки','where',4,6,'',2362,0,'none','','regular','','n',0),(2361,315,'Оценка','count',0,16,'',2363,0,'none','','regular','','n',0),(2362,315,'Статус','countState',10,23,'waiting',2364,6,'one','','readonly','','n',0),(2363,315,'Размер','countSize',3,18,'0',2365,0,'none','','readonly','','n',0),(2364,315,'Создание','items',0,16,'',2366,0,'none','','regular','','n',0),(2365,315,'Статус','itemsState',10,23,'waiting',2367,6,'one','','readonly','','n',0),(2366,315,'Размер','itemsSize',3,18,'0',2368,0,'none','','readonly','','n',0),(2367,315,'Процессинг','queue',0,16,'',2369,0,'none','','regular','','n',0),(2368,315,'Статус','queueState',10,23,'waiting',2371,6,'one','','readonly','','n',0),(2369,315,'Размер','queueSize',3,18,'0',2372,0,'none','','regular','','n',0),(2370,315,'Применение','apply',0,16,'',2379,0,'none','','regular','','n',0),(2371,315,'Статус','applyState',10,23,'waiting',2380,6,'one','','readonly','','n',0),(2372,315,'Размер','applySize',3,18,'0',2381,0,'none','','readonly','','n',0),(2373,316,'Очередь','queueTaskId',3,18,'0',2373,314,'one','','readonly','','n',0),(2374,316,'Сегмент','queueChunkId',3,23,'0',2374,315,'one','','regular','','n',0),(2375,316,'Таргет','target',1,1,'',2375,0,'none','','readonly','','n',0),(2376,316,'Значение','value',4,1,'',2376,0,'none','','readonly','','n',0),(2377,316,'Результат','result',4,13,'',2377,0,'none','','regular','','n',0),(2378,316,'Статус','stage',10,5,'items',2378,6,'one','','regular','','n',0),(2379,315,'Порядок','move',3,4,'0',2383,0,'none','','regular','','n',0),(2380,315,'Родительский сегмент','queueChunkId',3,18,'0',2360,315,'one','','readonly','','n',0),(2381,315,'Фракция','fraction',10,23,'none',2361,6,'one','','regular','','n',0),(2382,314,'Этап - Статус','stageState',1,1,'',2344,0,'none','','hidden','','n',0),(2383,315,'Байт','itemsBytes',3,18,'0',2370,0,'none','','regular','','n',0),(2384,10,'Тип','type',10,5,'p',32,6,'one','','regular','','n',0),(2385,171,'Элемент','elementId',3,23,'0',2385,4,'one','','regular','','n',0),(2386,8,'Мультиязычность','l10n',10,23,'n',2386,6,'one','','regular','','n',0),(2396,318,'Родительская запись','realtimeId',3,23,'0',2396,318,'one','','regular','','n',0),(2397,318,'Тип','type',10,23,'session',2397,6,'one','','regular','','n',0),(2398,318,'Роль','profileId',3,23,'0',2398,10,'one','','regular','','n',0),(2399,318,'Пользователь','adminId',3,23,'0',2399,0,'one','','regular','','n',0),(2400,318,'Токен','token',1,1,'',2400,0,'none','','regular','','n',0),(2401,318,'Начало','spaceSince',9,19,'<?=date(\'Y-m-d H:i:s\')?>',2401,0,'none','','regular','','n',0),(2402,318,'Конец','spaceUntil',9,19,'0000-00-00 00:00:00',2402,0,'none','','regular','','n',0),(2403,318,'Длительность','spaceFrame',3,18,'0',2403,0,'none','','regular','','n',0),(2404,318,'Язык','langId',3,23,'0',2404,307,'one','','regular','','n',0),(2405,318,'Раздел','sectionId',3,23,'0',2405,3,'one','','regular','','n',0),(2406,318,'Сущность','entityId',3,23,'0',2406,2,'one','','regular','','n',0),(2407,318,'Записи','entries',1,23,'',2407,0,'many','','regular','','n',0),(2408,318,'Поля','fields',4,23,'',2408,5,'many','','regular','','n',0),(2409,318,'Запись','title',1,1,'',2409,0,'none','','hidden','','n',0),(2410,318,'Режим','mode',10,23,'none',2410,6,'one','','regular','','n',0),(2411,318,'Scope','scope',4,6,'',2411,0,'none','','regular','','n',0),(2421,195,'Отображение','display',0,16,'',2183,0,'none','','regular','','n',0),(2423,9,'Отображение','display',0,16,'',2200,0,'none','','regular','','n',0),(2425,3,'Родительские классы','extends',0,16,'',310,0,'none','','regular','','n',0),(2426,3,'Источник записей','data',0,16,'',462,0,'none','','regular','','n',0),(2427,3,'Отображение записей','display',0,16,'',2312,0,'none','','regular','','n',0),(2429,3,'Подгрузка записей','load',0,16,'',2301,0,'none','','regular','','n',0),(2430,3,'Параметры','params',0,16,'',2430,0,'none','','hidden','','n',0),(2432,9,'При изменении ячейки обновлять всю строку','rowReqIfAffected',10,23,'n',2432,6,'one','','regular','','n',0),(2433,91,'Параметр','cfgField',3,23,'0',2433,5,'one','`entityId` = \"4\"','regular','','n',0),(2434,91,'Значение','cfgValue',4,6,'',2434,0,'none','','regular','','n',0),(2435,5,'Экземпляр','entry',3,23,'0',7,0,'one','','regular','','n',0),(2436,4,'Шаблон содержимого опции','optionTemplate',4,6,'',2461,0,'none','','regular','','n',23),(2437,4,'Высота опции','optionHeight',3,18,'14',2464,0,'none','','regular','','n',23),(2438,4,'Плейсхолдер','placeholder',1,1,'',2465,0,'none','','regular','','n',23),(2439,4,'Группировка опций по столбцу','groupBy',3,23,'0',2466,5,'one','','regular','','n',23),(2440,4,'Дополнительно передавать параметры (в виде атрибутов)','optionAttrs',1,23,'',2472,5,'many','','regular','','n',23),(2441,4,'Отключить лукап','noLookup',12,9,'0',2474,0,'none','','regular','','n',23),(2442,4,'Заголовочное поле','titleColumn',3,23,'0',2453,5,'one','','regular','','n',7),(2443,4,'Во всю ширину','wide',12,9,'0',2454,0,'none','','regular','','n',13),(2444,4,'Максимальная длина в символах','maxlength',3,18,'5',2443,0,'none','','regular','','n',18),(2445,4,'Маска','inputMask',1,1,'',2459,0,'none','','regular','','n',1),(2446,4,'Шейдинг','shade',12,9,'0',2458,0,'none','','regular','','n',1),(2447,4,'Обновлять локализации для других языков','refreshL10nsOnUpdate',12,9,'0',2451,0,'none','','regular','','n',6),(2448,4,'Разрешенные теги','allowedTags',1,1,'',2450,0,'none','','regular','','n',6),(2449,4,'Количество столбцов','cols',3,18,'1',2452,0,'none','','regular','','n',7),(2450,4,'Отображаемый формат','displayFormat',1,1,'Y-m-d',2455,0,'none','','regular','','n',12),(2451,4,'Когда','when',11,23,'',2447,6,'many','','regular','','n',19),(2452,4,'Высота в пикселях','height',3,18,'200',2442,0,'none','','regular','','n',13),(2453,4,'Ширина в пикселях','width',3,18,'0',2441,0,'none','','regular','','n',13),(2454,4,'Css класс для body','bodyClass',1,1,'',2440,0,'none','','regular','','n',13),(2455,4,'Путь к css-нику для подцепки редактором','contentsCss',4,6,'',2439,0,'none','','regular','','n',13),(2456,4,'Стили','style',4,6,'',2438,0,'none','','regular','','n',13),(2457,4,'Путь к js-нику для подцепки редактором','contentsJs',4,6,'',2437,0,'none','','regular','','n',13),(2458,4,'Скрипт','script',4,6,'',2436,0,'none','','regular','','n',13),(2459,4,'Скрипт обработки исходного кода','sourceStripper',4,6,'',72,0,'none','','regular','','n',13),(2460,4,'Включать наименование поля в имя файла при загрузке','appendFieldTitle',12,9,'1',54,0,'none','','regular','','n',14),(2461,4,'Включать наименование сущности в имя файла при download-е','prependEntityTitle',12,9,'1',55,0,'none','','regular','','n',14),(2462,4,'Единица измерения','measure',1,1,'',2444,0,'none','','regular','','n',18),(2463,4,'Отображаемый формат времени','displayTimeFormat',1,1,'H:i',2445,0,'none','','regular','','n',19),(2464,4,'Отображаемый формат даты','displayDateFormat',1,1,'Y-m-d',2448,0,'none','','regular','','n',19),(2465,4,'Валидация','vtype',10,23,'none',2449,6,'one','','regular','','n',1),(2466,4,'Допустимые типы','allowTypes',1,1,'',2446,0,'none','','regular','Укажите список расширений и/или группы расширений, через запятую: \r\n- image: gif,png,jpeg,jpg\r\n- office: doc,pdf,docx,xls,xlsx,txt,odt,ppt,pptx\r\n- draw: psd,ai,cdr\r\n- archive: zip,rar,7z,gz,tar','n',14),(2476,4,'Обновлять локализации для других языков','refreshL10nsOnUpdate',12,9,'0',2470,0,'none','','regular','','n',1),(2477,4,'Заголовочное поле','titleColumn',3,23,'0',2477,5,'one','','regular','','n',5),(2478,4,'Когда','when',11,23,'',2478,6,'one','','regular','','n',12);
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
  `rowReqIfAffected` enum('n','y') NOT NULL DEFAULT 'n',
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
  KEY `rowReqIfAffected` (`rowReqIfAffected`),
  FULLTEXT KEY `tooltip` (`tooltip`)
) ENGINE=MyISAM AUTO_INCREMENT=2706 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grid`
--

LOCK TABLES `grid` WRITE;
/*!40000 ALTER TABLE `grid` DISABLE KEYS */;
INSERT INTO `grid` VALUES (1,2,1,1,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(2,2,2,2,'','y','Тип столбца MySQL',0,'','all','','0','none','','normal',0,0,'n'),(3,2,3,3,'','y','Пригоден для хранения внешних ключей',0,'','all','','0','none','','normal',0,0,'n'),(4,5,4,4,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(5,5,5,5,'','y','Таблица БД',0,'','all','','1','none','','normal',0,0,'n'),(6,6,7,6,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(7,6,8,7,'Псевдоним','y','Псевдоним',0,'','all','','1','none','','normal',0,0,'n'),(8,6,9,2352,'Тип столбца','y','Тип столбца MySQL',2609,'','all','','1','none','','normal',0,0,'n'),(9,6,10,2610,'Элемент','y','Элемент управления',2613,'','all','','1','none','','normal',0,0,'n'),(10,6,11,2609,'По умолчанию','y','DEFAULT',2609,'','all','','1','none','','normal',0,0,'n'),(11,6,12,10,'Сущность','y','Ключи какой сущности',2611,'','all','','1','none','','normal',0,0,'n'),(2610,6,470,9,'','y','Хранит ключи',2611,'','all','','0','none','','normal',0,0,'n'),(13,6,14,2613,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(14,7,19,2460,'','y','Сущность',2642,'','all','','0','none','','normal',0,0,'n'),(15,7,20,14,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(16,7,21,15,'','y','Контроллер',2641,'','all','','1','none','','normal',0,0,'n'),(17,7,22,16,'','y','Статус',2655,'','all','','0','none','','normal',0,0,'n'),(18,7,23,18,'','y','Порядок',0,'','all','','1','none','','normal',0,0,'n'),(20,7,25,2644,'','y','Количество записей на странице',2654,'','all','','1','none','','normal',0,0,'n'),(23,8,27,23,'','y','Действие',0,'','all','','0','none','','normal',0,0,'n'),(24,8,29,297,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(25,8,30,2564,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(26,10,31,26,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(27,10,32,27,'','y','Псевдоним',0,'','all','','1','none','','normal',0,0,'n'),(29,11,2165,29,'Столбец','y','Auto title',0,'','all','','0','none','','normal',0,0,'n'),(30,11,35,2320,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(32,13,36,32,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(33,13,37,2329,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(34,12,16,34,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(35,12,17,35,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(36,14,39,36,'','y','Фамилия Имя',0,'','all','','1','none','','normal',0,0,'n'),(37,14,40,37,'','y','Email (используется в качестве логина)',0,'','all','','1','none','','normal',0,0,'n'),(38,14,41,38,'','y','Пароль',0,'','all','','1','none','','normal',0,0,'n'),(39,14,42,39,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(42,16,65,43,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(43,16,66,44,'','y','Способен работать с внешними ключами',0,'','all','','0','none','','normal',0,0,'n'),(46,16,64,42,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(89,22,107,56,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(90,22,108,57,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(91,22,109,59,'','y','Ширина',0,'','all','','0','none','','normal',0,0,'n'),(92,22,110,60,'','y','Высота',0,'','all','','0','none','','normal',0,0,'n'),(93,22,111,58,'','y','Размер',0,'','all','','0','none','','normal',0,0,'n'),(94,22,112,61,'','y','Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>',0,'','all','','0','none','','normal',0,0,'n'),(130,30,131,61,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(132,30,133,63,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(2365,379,0,2365,'Взятый из контекста','y','Взятый из контекста',2362,'','all','','0','none','','normal',0,0,'n'),(2363,379,2179,2363,'','y','Префикс',2362,'','all','','1','none','','normal',0,0,'n'),(136,30,137,67,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(341,12,377,253,'','y','Порядок отображения',0,'','all','','0','none','','normal',0,0,'n'),(383,8,28,25,'','y','Доступ',0,'','all','','0','none','','normal',0,0,'n'),(2641,7,2425,2459,'Привязка к коду','y','Родительские классы',2655,'','all','','0','none','','normal',0,100,'n'),(1335,7,1366,17,'','y','Фракция',2655,'','all','','1','none','','normal',0,0,'n'),(1334,113,1040,925,'','y','Тип',0,'','all','','0','none','','normal',0,0,'n'),(1333,172,1365,924,'','y','Тип',0,'','all','','0','none','','normal',0,0,'n'),(1332,10,1364,923,'','y','Фракция',0,'','all','','1','none','','normal',0,0,'n'),(420,113,559,328,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(421,113,560,329,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(424,113,563,330,'','y','Прикрепленная сущность',0,'','all','','0','none','','normal',0,0,'n'),(443,5,612,347,'','y','Тип',0,'','all','','0','none','','normal',0,0,'n'),(489,144,678,388,'','y','Имя',0,'','all','','0','none','','normal',0,0,'n'),(490,144,679,389,'','y','Email',0,'','all','','0','none','','normal',0,0,'n'),(491,144,681,391,'','y','Дата',0,'','all','','0','none','','normal',0,0,'n'),(2657,16,92,2657,'','y','Не отображать в формах',0,'','all','','0','none','','normal',0,0,'n'),(1382,224,1444,2312,'','y','Порядок',2630,'','all','','0','none','','normal',0,0,'n'),(1384,224,1445,929,'','y','Статус',2630,'','all','','0','none','','normal',0,0,'n'),(832,172,857,624,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(833,172,858,625,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(834,173,860,626,'','y','Действие',0,'','all','','0','none','','normal',0,0,'n'),(1383,224,1443,926,'','y','Поле',0,'','all','','1','none','','normal',0,0,'n'),(2625,224,2311,2625,'РС','y','Разрешить сброс',2628,'','all','','1','none','','normal',0,0,'n'),(2656,2,475,2656,'','y','Совместимые элементы управления',0,'','all','','1','none','','normal',0,0,'n'),(1039,172,1074,759,'','y','Выполнять maintenance()',0,'','all','','0','none','','normal',0,0,'n'),(979,173,1027,728,'','y','Тип',0,'','all','','0','none','','normal',0,0,'n'),(1066,191,1194,782,'','y','Компонент',0,'','all','','0','none','','normal',0,0,'n'),(1067,191,1195,783,'','y','Очередность',0,'','all','','0','none','','normal',0,0,'n'),(1068,191,1196,784,'','y','Префикс',0,'','all','','0','none','','normal',0,0,'n'),(2627,224,2198,2627,'НР','y','Содержательность',2628,'','all','','1','none','','normal',0,0,'n'),(2312,224,2183,1945,'','y','Фильтрация',0,'','all','','1','none','','normal',0,0,'n'),(2311,224,2182,2311,'Значение<br>по умолчанию','y','Значение по умолчанию',0,'','all','','1','none','','normal',0,0,'n'),(2362,379,0,2362,'Компонент','y','Компонент',0,'','all','','0','none','','normal',0,0,'n'),(2361,379,2172,2361,'','y','Тэг',0,'','all','','0','none','','normal',0,0,'n'),(2360,379,2181,2360,'','y','Порядок отображения',0,'','all','','0','none','','normal',0,0,'n'),(1231,201,1342,851,'','y','Поле',0,'','all','','0','none','','normal',0,0,'n'),(2356,201,2252,988,'','y','Наименование',2638,'','all','','1','none','','normal',0,0,'n'),(1439,232,1515,965,'','y','Тип',0,'','all','','0','none','','normal',0,0,'n'),(2359,379,2173,2359,'','y','Тип компонента',0,'','all','','0','none','','normal',0,0,'n'),(1421,232,1485,962,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(1422,232,1486,963,'','y','Псевдоним',0,'','all','','0','none','','normal',0,0,'n'),(1423,232,1488,1132,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(1449,113,1533,989,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(1448,201,1532,2639,'','y','Значение по умолчанию',2638,'','all','','1','none','','normal',0,0,'n'),(1515,113,585,1036,'','y','Порядок отображения соответствующего пункта в меню',0,'','all','','0','none','','normal',0,0,'n'),(1656,11,1886,2419,'','y','Переименовать',2637,'','all','','1','none','','normal',0,0,'n'),(1954,224,1658,2621,'','y','Переименовать',2630,'','all','','1','none','','normal',0,0,'n'),(2280,201,2161,1946,'','y','Режим',2638,'','all','','0','none','','normal',0,0,'n'),(2320,11,2159,1156,'','y','Статус',2637,'','all','','0','none','','normal',0,0,'n'),(2321,6,2197,13,'','y','Режим',2613,'','all','','0','none','','normal',0,0,'n'),(2322,11,34,30,'','y','Поле',0,'','all','','1','none','','normal',0,0,'n'),(2323,10,2202,2659,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(2324,8,2209,24,'','y','Переименовать',0,'','all','','0','none','','normal',0,0,'n'),(2325,11,2210,2416,'','y','Редактор',2637,'','all','','0','none','','normal',0,0,'n'),(2326,8,2212,2324,'ЮП','y','Южная панель',0,'Режим отображения южной панели','all','','0','none','','normal',0,0,'n'),(2327,7,2213,2641,'','y','Режим подгрузки',2654,'Режим подгрузки данных','all','','0','none','','normal',0,0,'n'),(2328,8,2214,2326,'','y','Автосайз окна',0,'','all','','0','none','','normal',0,0,'n'),(2329,13,2215,2464,'МКО','y','Максимальное количество окон',0,'Максимальное количество окон','all','','1','none','','normal',0,0,'n'),(2415,390,2283,2462,'SMS','y','Дублирование по SMS',0,'Дублирование по SMS','all','','0','none','','normal',0,0,'n'),(2394,389,2259,2394,'','y','Счетчик',0,'','all','','0','none','','normal',0,0,'n'),(2395,389,2260,2395,'','y','Отображение / SQL',2394,'','all','','0','none','','normal',0,0,'n'),(2396,389,2256,2396,'','y','Событие / PHP',2394,'','all','','0','none','','normal',0,0,'n'),(2397,389,2262,2397,'','y','Пункты меню',2394,'','all','','0','none','','normal',0,0,'n'),(2398,389,2263,2398,'','y','Цвет фона',2394,'','all','','0','none','','normal',0,0,'n'),(2399,389,2264,2399,'','y','Цвет текста',2394,'','all','','0','none','','normal',0,0,'n'),(2390,389,2254,2390,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(2391,389,2255,2391,'','y','Сущность',0,'','all','','0','none','','normal',0,0,'n'),(2392,389,2257,2392,'','y','Получатели',0,'','all','','0','none','','normal',0,0,'n'),(2393,389,2258,2393,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(2468,387,2237,2468,'','y','Ключ',0,'','all','','0','none','','normal',0,0,'n'),(2467,387,2236,2467,'','y','Наименование',0,'','all','','0','none','','normal',0,0,'n'),(2352,6,2239,2611,'l10n','y','Мультиязычность',2609,'','all','','0','none','','normal',0,0,'n'),(2353,388,2247,2352,'','y','От какого поля зависит',0,'','all','','0','none','','normal',0,0,'n'),(2354,388,2248,2353,'','y','Поле по ключу',0,'','all','','0','none','','normal',0,0,'n'),(2357,201,2207,2357,'Роли','y','Влияние',2640,'','all','','1','none','','normal',0,0,'n'),(2358,201,2250,2358,'','y','Кроме',2640,'','all','','1','none','','normal',0,0,'n'),(2364,379,2174,2364,'','y','Указанный вручную',2362,'','all','','0','none','','normal',0,0,'n'),(2366,379,2175,2366,'Уровень','y','Шагов вверх',2365,'','all','','0','none','','normal',0,0,'n'),(2367,379,2176,2367,'','y','Источник',2365,'','all','','0','none','','normal',0,0,'n'),(2368,379,2178,2368,'','y','Поле',2365,'','all','','0','none','','normal',0,0,'n'),(2369,379,2180,2369,'','y','Постфикс',2362,'','all','','1','none','','normal',0,0,'n'),(2414,390,2282,2415,'VK','y','Дублирование в ВК',0,'Дублирование во ВКонтакте','all','','0','none','','normal',0,0,'n'),(2413,390,2281,2414,'Email','y','Дублирование на почту',0,'Дублирование на почту','all','','0','none','','normal',0,0,'n'),(2412,390,2277,2413,'','y','Общий',0,'','all','','0','none','','normal',0,0,'n'),(2411,390,2275,2412,'','y','Роль',0,'','all','','0','none','','normal',0,0,'n'),(2417,388,2306,2417,'[ ! ]','y','Обязательное',0,'Обязательное','all','','0','none','','normal',0,0,'n'),(2418,388,2307,2418,'','y','Коннектор',0,'','all','','0','none','','normal',0,0,'n'),(2419,11,2308,2461,'','y','Группа',2637,'','all','','0','none','','normal',0,0,'n'),(2450,391,12,2450,'Сущность','y','Ключи какой сущности',2619,'','all','','1','none','','normal',0,0,'n'),(2449,391,470,2449,'','y','Хранит ключи',2619,'','all','','0','none','','normal',0,0,'n'),(2622,224,2203,2622,'','y','Кому',2629,'','all','','1','none','','normal',0,0,'n'),(2623,224,2204,2623,'','y','Выбранные',2629,'','all','','1','none','','normal',0,0,'n'),(2447,391,11,2447,'По умолчанию','y','Значение по умолчанию',2620,'','all','','1','none','','normal',0,0,'n'),(2446,391,9,2446,'Тип столбца','y','Тип столбца MySQL',2620,'','all','','1','none','','normal',0,0,'n'),(2445,391,8,2440,'Псевдоним','y','Псевдоним',0,'','all','','1','none','','normal',0,0,'n'),(2618,391,2414,2448,'','y','Элемент управления',0,'','all','','0','none','','normal',0,0,'n'),(2624,224,2317,2624,'','y','Подсказка',2630,'','all','','1','none','','normal',0,0,'n'),(2443,391,2199,2443,'','y','Подсказка',2618,'','all','','1','none','','normal',0,0,'n'),(2441,391,2197,2441,'','y','Режим',2618,'','all','','0','none','','normal',0,0,'n'),(2442,391,10,2442,'Элемент','y','Элемент управления',2618,'','all','','1','none','','normal',0,0,'n'),(2438,391,6,2438,'Сущность','y','Сущность, в структуру которой входит это поле',0,'Сущность, в структуру которой входит это поле','all','','0','none','','normal',0,0,'n'),(2439,391,7,2439,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(2619,391,2413,2444,'','y','Внешние ключи',0,'','all','','0','none','','normal',0,0,'n'),(2451,391,754,2451,'Фильтрация','y','Статическая фильтрация',2619,'','all','','1','none','','normal',0,0,'n'),(2621,224,2314,928,'','y','Поле по ключу',0,'','all','','1','none','','normal',0,0,'n'),(2452,391,2239,2619,'l10n','y','Мультиязычность',2620,'Мультиязычность','all','','0','none','','normal',0,0,'n'),(2453,391,14,2620,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(2459,7,502,926,'','y','PHP',2641,'','all','','1','none','','normal',0,50,'y'),(2460,7,2309,2327,'','y','JS',2641,'','all','','1','none','','normal',0,50,'y'),(2461,11,2315,2632,'','y','Ширина',2637,'','all','','1','none','','normal',0,0,'n'),(2462,390,2316,2411,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(2463,14,2318,2463,'Демо','y','Демо-режим',0,'Демо-режим','all','','0','none','','normal',0,0,'n'),(2464,13,2319,2563,'Демо','y','Демо-режим',0,'Демо-режим','all','','0','none','','normal',0,0,'n'),(2465,5,2320,2465,'','y','Группировать файлы',0,'','all','','1','none','','normal',0,0,'n'),(2466,14,2324,2466,'','y','Правки UI',0,'','all','','0','none','','normal',0,0,'n'),(2469,387,2326,2469,'','y','Админка',0,'','all','','0','none','','normal',0,0,'n'),(2470,387,2238,2470,'','y','Статус',2469,'','all','','0','none','','normal',0,0,'n'),(2471,387,2327,2471,'','y','Система',2469,'','all','','0','none','','normal',0,0,'n'),(2472,387,2328,2472,'','y','Интерфейс',2471,'','all','','0','none','','normal',0,0,'n'),(2473,387,2329,2473,'','y','Константы',2471,'','all','','0','none','','normal',0,0,'n'),(2474,387,2330,2474,'','y','Проект',2469,'','all','','0','none','','normal',0,0,'n'),(2475,387,2331,2475,'','y','Интерфейс',2474,'','all','','0','none','','normal',0,0,'n'),(2476,387,2332,2476,'','y','Константы',2474,'','all','','0','none','','normal',0,0,'n'),(2477,387,2333,2477,'','y','Данные',2474,'','all','','0','none','','normal',0,0,'n'),(2478,387,2334,2478,'','y','Шаблоны',2474,'','all','','0','none','','normal',0,0,'n'),(2479,387,2335,2479,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(2519,392,2353,2519,'','y','Статус',2518,'','all','','0','none','','normal',0,0,'n'),(2518,392,2352,2518,'','y','Процессинг',0,'','all','','0','none','','normal',0,0,'n'),(2514,392,2348,2514,'','y','Создание',0,'','all','','0','none','','normal',0,0,'n'),(2515,392,2349,2515,'','y','Статус',2514,'','all','','0','none','','normal',0,0,'n'),(2517,392,2351,2517,'','y','Байт',2514,'','all','','0','sum','','normal',0,0,'n'),(2516,392,2350,2516,'','y','Размер',2514,'','all','','0','none','','normal',0,0,'n'),(2511,392,2345,2511,'','y','Оценка',0,'','all','','0','none','','normal',0,0,'n'),(2512,392,2346,2512,'','y','Статус',2511,'','all','','0','none','','normal',0,0,'n'),(2513,392,2347,2513,'','y','Размер',2511,'','all','','0','none','','normal',0,0,'n'),(2508,392,2339,2508,'','y','Процесс',0,'','all','','0','none','','normal',0,0,'n'),(2509,392,2341,2509,'','y','PID',2508,'','all','','0','none','','normal',0,0,'n'),(2510,392,2340,2510,'','y','Начат',2508,'','all','','0','none','','normal',0,0,'n'),(2505,392,2342,2505,'','h','Этап',0,'','all','','0','none','','normal',0,0,'n'),(2506,392,2343,2506,'','h','Статус',0,'','all','','0','none','','normal',0,0,'n'),(2507,392,2344,2507,'','y','Сегменты',0,'','all','','0','none','','normal',0,0,'n'),(2502,392,2337,2502,'','y','Создана',0,'','all','','0','none','','normal',0,0,'n'),(2503,392,2336,2503,'','y','Задача',0,'','all','','0','none','','normal',0,0,'n'),(2504,392,2338,2504,'','y','Параметры',0,'','all','','0','none','','normal',0,0,'n'),(2520,392,2354,2520,'','y','Размер',2518,'','all','','0','none','','normal',0,0,'n'),(2521,392,2355,2521,'','y','Применение',0,'','all','','0','none','','normal',0,0,'n'),(2522,392,2356,2522,'','y','Статус',2521,'','all','','0','none','','normal',0,0,'n'),(2523,392,2357,2523,'','y','Размер',2521,'','all','','0','none','','normal',0,0,'n'),(2548,393,2367,2548,'','y','Процессинг',0,'','all','','0','none','','normal',0,0,'n'),(2546,393,2365,2546,'','y','Статус',2545,'','all','','0','none','','normal',0,0,'n'),(2547,393,2366,2547,'','y','Размер',2545,'','all','','0','sum','','normal',0,0,'n'),(2543,393,2362,2543,'','y','Статус',2542,'','all','','0','none','','normal',0,0,'n'),(2544,393,2363,2544,'','y','Размер',2542,'','all','','0','sum','','normal',0,0,'n'),(2545,393,2364,2545,'','y','Создание',0,'','all','','0','none','','normal',0,0,'n'),(2541,393,2360,2541,'','y','Условие выборки',0,'','all','','0','none','','normal',0,0,'n'),(2542,393,2361,2542,'','y','Оценка',0,'','all','','0','none','','normal',0,0,'n'),(2538,393,0,2538,'','n','',0,'','all','','0','none','','normal',0,0,'n'),(2539,393,0,2539,'','n','',0,'','all','','0','none','','normal',0,0,'n'),(2540,393,2359,2540,'','y','Расположение',0,'','all','','0','none','','normal',0,0,'n'),(2549,393,2368,2549,'','y','Статус',2548,'','all','','0','none','','normal',0,0,'n'),(2550,393,2369,2550,'','y','Размер',2548,'','all','','0','sum','','normal',0,0,'n'),(2551,393,2370,2551,'','y','Применение',0,'','all','','0','none','','normal',0,0,'n'),(2552,393,2371,2552,'','y','Статус',2551,'','all','','0','none','','normal',0,0,'n'),(2553,393,2372,2553,'','y','Размер',2551,'','all','','0','sum','','normal',0,0,'n'),(2561,394,2378,2561,'','y','Статус',0,'','all','','0','none','','normal',0,0,'n'),(2560,394,2377,2560,'','y','Результат',0,'','all','','1','none','','normal',0,0,'n'),(2559,394,2376,2559,'','y','Значение',0,'','all','','0','none','','normal',0,0,'n'),(2558,394,2375,2558,'','y','Таргет',0,'','all','','0','none','','normal',0,0,'n'),(2562,393,2383,2562,'','y','Байт',2545,'','all','','0','sum','','normal',0,0,'n'),(2563,13,2384,33,'','y','Тип',0,'','all','','0','none','','normal',0,0,'n'),(2564,8,2386,2328,'','y','Мультиязычность',0,'','all','','0','none','','normal',0,0,'n'),(2626,224,2184,2626,'ИШ','y','Игнорировать шаблон, заданный в параметрах настроек поля',2628,'','all','','1','none','','normal',0,0,'n'),(2620,391,2412,2452,'','y','MySQL',0,'','all','','0','none','','normal',0,0,'n'),(2613,6,2414,2321,'Элемент управления','y','Элемент управления',0,'','all','','0','none','','normal',0,0,'n'),(2611,6,2413,8,'','y','Внешние ключи',0,'','all','','0','none','','normal',0,0,'n'),(2612,6,754,11,'Фильтрация','y','Статическая фильтрация',2611,'','all','','1','none','','normal',0,0,'n'),(2609,6,2412,2612,'','y','MySQL',0,'','all','','0','none','','normal',0,0,'n'),(2606,396,2406,2606,'','h','Сущность',0,'','all','','0','none','','normal',0,0,'n'),(2605,396,2404,2605,'','h','Язык',0,'','all','','0','none','','normal',0,0,'n'),(2604,396,2403,2604,'','h','Длительность',0,'','all','','0','none','','normal',0,0,'n'),(2603,396,2402,2603,'','h','Конец',0,'','all','','0','none','','normal',0,0,'n'),(2602,396,2401,2602,'','y','Начало',0,'','all','','0','none','','normal',0,0,'n'),(2601,396,2399,2601,'','y','Пользователь',0,'','all','','0','none','','normal',0,0,'n'),(2600,396,2398,2600,'','h','Роль',0,'','all','','0','none','','normal',0,0,'n'),(2599,396,2397,2599,'','h','Тип',0,'','all','','0','none','','normal',0,0,'n'),(2598,396,2405,2598,'','h','Раздел',0,'','all','','0','none','','normal',0,0,'n'),(2597,396,2400,2597,'','h','Токен',0,'','all','','0','none','','normal',0,0,'n'),(2596,396,2409,2596,'','y','Запись',0,'','all','','0','none','','normal',0,0,'n'),(2607,396,2407,2607,'','n','Записи',0,'','all','','0','none','','normal',0,0,'n'),(2608,396,2408,2608,'','h','Поля',0,'','all','','0','none','','normal',0,0,'n'),(2614,6,2199,2614,'','y','Подсказка',2613,'','all','','1','none','','normal',0,0,'n'),(2615,13,1337,2615,'','y','Сущность пользователей',0,'','all','','1','none','','normal',0,0,'n'),(2616,13,2131,2616,'','y','Дэшборд',0,'','all','','1','none','','normal',0,0,'n'),(2617,13,2132,2617,'','y','Порядок отображения',0,'','all','','0','none','','normal',0,0,'n'),(2628,224,2419,2630,'','y','Флаги',0,'','all','','0','none','','normal',0,0,'n'),(2629,224,2420,2629,'','y','Доступ',0,'','all','','0','none','','normal',0,0,'n'),(2630,224,2421,2628,'','y','Отображение',0,'','all','','0','none','','normal',0,0,'n'),(2631,11,2313,2325,'','y','Поле по ключу',0,'','all','','1','none','','normal',0,0,'n'),(2632,11,2200,2631,'','y','Подсказка',2637,'','all','','1','none','','normal',0,0,'n'),(2633,11,2304,2633,'','y','Внизу',2637,'','all','','1','none','','normal',0,0,'n'),(2634,11,2205,2635,'Кому','y','Доступ',2636,'','all','','1','none','','normal',0,0,'n'),(2635,11,2206,2636,'','y','Кроме',2636,'','all','','1','none','','normal',0,0,'n'),(2636,11,2422,2637,'','y','Доступ',0,'','all','','0','none','','normal',0,0,'n'),(2637,11,2423,2634,'','y','Отображение',0,'','all','','0','none','','normal',0,0,'n'),(2638,201,2251,2638,'','y','Изменить свойства',0,'','all','','0','none','','normal',0,0,'n'),(2639,201,2385,2356,'','y','Элемент',2638,'','all','','1','none','','normal',0,0,'n'),(2640,201,2424,2640,'','y','Влияние',0,'','all','','0','none','','normal',0,0,'n'),(2642,7,2426,31,'Источник','y','Источник записей',2653,'','all','','0','none','','normal',0,0,'n'),(2643,7,1345,2704,'','y','Запретить создание новых записей',2642,'','all','','0','none','','normal',0,0,'n'),(2644,7,767,2647,'','y','Фильтрация через SQL WHERE',2642,'','all','','1','none','','normal',0,0,'n'),(2645,7,503,2642,'','y','Сортировка',2654,'','all','','1','none','','normal',0,0,'n'),(2646,7,557,2645,'','h','Направление сортировки',2654,'','all','','0','none','','normal',0,0,'y'),(2647,7,2427,2654,'Отображение','y','Отображение записей',2653,'','all','','0','none','','normal',0,0,'n'),(2648,7,2312,2648,'','y','Выделение более одной записи',2647,'','all','','0','none','','normal',0,0,'n'),(2649,7,2310,2649,'','y','Включить нумерацию записей',2647,'','all','','0','none','','normal',0,0,'n'),(2650,7,2211,2650,'','y','Группировка',2647,'','all','','1','none','','normal',0,0,'n'),(2651,7,2322,2651,'','h','Плитка',2647,'','all','','0','none','','normal',0,0,'n'),(2652,7,2323,2652,'','h','Превью',2647,'','all','','0','none','','normal',0,0,'n'),(2653,7,2428,2655,'','y','Записи',0,'','all','','0','none','','normal',0,0,'n'),(2654,7,2429,2646,'','y','Подгрузка',2653,'','all','','0','none','','normal',0,0,'n'),(2655,7,2430,2653,'Свойства','y','Параметры',0,'','all','','0','none','','normal',0,100,'n'),(2659,10,2100,2658,'','y','Отображать в панели действий',0,'','all','','1','none','','normal',0,0,'n'),(2658,10,345,2323,'','y','Нужно выбрать запись',0,'','all','','1','none','','normal',0,0,'n'),(2676,11,2432,2676,'','y','При изменении ячейки обновлять всю строку',0,'','all','','0','none','','normal',0,0,'n'),(2677,101,2433,2677,'','y','Параметр',0,'','all','','0','none','','normal',0,0,'n'),(2678,101,2434,2678,'','y','Значение',0,'','all','','1','none','','normal',0,0,'n'),(2679,405,7,2679,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(2680,405,8,2680,'Псевдоним','y','Псевдоним',0,'','all','','1','none','','normal',0,0,'n'),(2681,405,2413,2681,'','y','Внешние ключи',0,'','all','','0','none','','normal',0,0,'n'),(2682,405,470,2682,'','y','Хранит ключи',2681,'','all','','0','none','','normal',0,0,'n'),(2683,405,12,2683,'Сущность','y','Ключи какой сущности',2681,'','all','','1','none','','normal',0,0,'n'),(2684,405,754,2684,'Фильтрация','y','Статическая фильтрация',2681,'','all','','1','none','','normal',0,0,'n'),(2685,405,2414,2685,'Элемент управления','y','Элемент управления',0,'','all','','0','none','','normal',0,0,'n'),(2686,405,2197,2686,'','y','Режим',2685,'','all','','0','none','','normal',0,0,'n'),(2687,405,10,2687,'Элемент','y','Элемент управления',2685,'','all','','1','none','','normal',0,0,'n'),(2688,405,2199,2688,'','y','Подсказка',2685,'','all','','1','none','','normal',0,0,'n'),(2689,405,2412,2689,'','y','MySQL',0,'','all','','0','none','','normal',0,0,'n'),(2690,405,9,2690,'Тип столбца','y','Тип столбца MySQL',2689,'','all','','1','none','','normal',0,0,'n'),(2691,405,11,2691,'По умолчанию','y','Значение по умолчанию',2689,'','all','','1','none','','normal',0,0,'n'),(2692,405,2239,2692,'l10n','y','Мультиязычность',2689,'','all','','0','none','','normal',0,0,'n'),(2693,405,14,2693,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(2694,406,16,2694,'','y','Наименование',0,'','all','','1','none','','normal',0,0,'n'),(2695,406,17,2695,'','y','Псевдоним',0,'','all','','1','none','','normal',0,0,'n'),(2696,406,377,2696,'','y','Порядок',0,'','all','','0','none','','normal',0,0,'n'),(2697,407,476,2701,'','y','Поле',0,'','all','','0','none','','normal',0,0,'n'),(2700,407,2166,2705,'','h','Auto title',0,'','all','','0','none','','normal',0,0,'n'),(2701,407,2433,2702,'','y','Параметр',0,'','all','','0','none','','normal',0,0,'n'),(2702,407,2434,2703,'','y','Значение',0,'','all','','0','none','','normal',0,0,'n'),(2705,407,476,2700,'','y','Поле',0,'','all','','0','none','','normal',6,0,'n');
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
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lang`
--

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
INSERT INTO `lang` VALUES (1,'Русский','ru','y','smth','y','y','n','n','n','n',1),(2,'Engish','en','y','smth','y','y','y','y','y','y',2),(3,'Afrikaans','af','n','noth','n','n','n','n','n','n',3),(4,'Albanian','sq','n','noth','n','n','n','n','n','n',4),(5,'Amharic','am','n','noth','n','n','n','n','n','n',5),(6,'Arabic','ar','n','noth','n','n','n','n','n','n',6),(7,'Armenian','hy','n','noth','n','n','n','n','n','n',7),(8,'Azerbaijani','az','n','noth','n','n','n','n','n','n',8),(9,'Basque','eu','n','noth','n','n','n','n','n','n',9),(10,'Belarusian','be','n','noth','n','n','n','n','n','n',10),(11,'Bengali','bn','n','noth','n','n','n','n','n','n',11),(12,'Bosnian','bs','n','noth','n','n','n','n','n','n',12),(13,'Bulgarian','bg','n','noth','n','n','n','n','n','n',13),(14,'Catalan','ca','n','noth','n','n','n','n','n','n',14),(15,'Cebuano','ceb','n','noth','n','n','n','n','n','n',15),(16,'Chichewa','ny','n','noth','n','n','n','n','n','n',16),(17,'Chinese (Simplified)','zh-CN','n','smth','n','y','n','n','n','n',17),(18,'Chinese (Traditional)','zh-TW','n','noth','n','n','n','n','n','n',18),(19,'Corsican','co','n','noth','n','n','n','n','n','n',19),(20,'Croatian','hr','n','noth','n','n','n','n','n','n',20),(21,'Czech','cs','n','noth','n','n','n','n','n','n',21),(22,'Danish','da','n','noth','n','n','n','n','n','n',22),(23,'Dutch','nl','n','noth','n','n','n','n','n','n',23),(24,'Esperanto','eo','n','noth','n','n','n','n','n','n',24),(25,'Estonian','et','n','noth','n','n','n','n','n','n',25),(26,'Filipino','tl','n','noth','n','n','n','n','n','n',26),(27,'Finnish','fi','n','noth','n','n','n','n','n','n',27),(28,'French','fr','n','noth','n','n','n','n','n','n',28),(29,'Frisian','fy','n','noth','n','n','n','n','n','n',29),(30,'Galician','gl','n','noth','n','n','n','n','n','n',30),(31,'Georgian','ka','n','noth','n','n','n','n','n','n',31),(32,'German','de','n','smth','n','y','n','n','n','n',32),(33,'Greek','el','n','noth','n','n','n','n','n','n',33),(34,'Gujarati','gu','n','noth','n','n','n','n','n','n',34),(35,'Haitian Creole','ht','n','noth','n','n','n','n','n','n',35),(36,'Hausa','ha','n','noth','n','n','n','n','n','n',36),(37,'Hawaiian','haw','n','noth','n','n','n','n','n','n',37),(38,'Hebrew','iw','n','noth','n','n','n','n','n','n',38),(39,'Hindi','hi','n','noth','n','n','n','n','n','n',39),(40,'Hmong','hmn','n','noth','n','n','n','n','n','n',40),(41,'Hungarian','hu','n','noth','n','n','n','n','n','n',41),(42,'Icelandic','is','n','noth','n','n','n','n','n','n',42),(43,'Igbo','ig','n','noth','n','n','n','n','n','n',43),(44,'Indonesian','id','n','noth','n','n','n','n','n','n',44),(45,'Irish','ga','n','noth','n','n','n','n','n','n',45),(46,'Italian','it','n','noth','n','n','n','n','n','n',46),(47,'Japanese','ja','n','smth','n','y','n','n','n','n',47),(48,'Javanese','jw','n','noth','n','n','n','n','n','n',48),(49,'Kannada','kn','n','noth','n','n','n','n','n','n',49),(50,'Kazakh','kk','n','noth','n','n','n','n','n','n',50),(51,'Khmer','km','n','noth','n','n','n','n','n','n',51),(52,'Kinyarwanda','rw','n','noth','n','n','n','n','n','n',52),(53,'Korean','ko','n','noth','n','n','n','n','n','n',53),(54,'Kurdish (Kurmanji)','ku','n','noth','n','n','n','n','n','n',54),(55,'Kyrgyz','ky','n','noth','n','n','n','n','n','n',55),(56,'Lao','lo','n','noth','n','n','n','n','n','n',56),(57,'Latin','la','n','noth','n','n','n','n','n','n',57),(58,'Latvian','lv','n','noth','n','n','n','n','n','n',58),(59,'Lithuanian','lt','n','noth','n','n','n','n','n','n',59),(60,'Luxembourgish','lb','n','noth','n','n','n','n','n','n',60),(61,'Macedonian','mk','n','noth','n','n','n','n','n','n',61),(62,'Malagasy','mg','n','noth','n','n','n','n','n','n',62),(63,'Malay','ms','n','noth','n','n','n','n','n','n',63),(64,'Malayalam','ml','n','noth','n','n','n','n','n','n',64),(65,'Maltese','mt','n','noth','n','n','n','n','n','n',65),(66,'Maori','mi','n','noth','n','n','n','n','n','n',66),(67,'Marathi','mr','n','noth','n','n','n','n','n','n',67),(68,'Mongolian','mn','n','noth','n','n','n','n','n','n',68),(69,'Myanmar (Burmese)','my','n','noth','n','n','n','n','n','n',69),(70,'Nepali','ne','n','noth','n','n','n','n','n','n',70),(71,'Norwegian','no','n','noth','n','n','n','n','n','n',71),(72,'Odia (Oriya)','or','n','noth','n','n','n','n','n','n',72),(73,'Pashto','ps','n','noth','n','n','n','n','n','n',73),(74,'Persian','fa','n','noth','n','n','n','n','n','n',74),(75,'Polish','pl','n','noth','n','n','n','n','n','n',75),(76,'Portuguese','pt','n','noth','n','n','n','n','n','n',76),(77,'Punjabi','pa','n','noth','n','n','n','n','n','n',77),(78,'Romanian','ro','n','noth','n','n','n','n','n','n',78),(79,'Samoan','sm','n','noth','n','n','n','n','n','n',79),(80,'Scots Gaelic','gd','n','noth','n','n','n','n','n','n',80),(81,'Serbian','sr','n','noth','n','n','n','n','n','n',81),(82,'Sesotho','st','n','noth','n','n','n','n','n','n',82),(83,'Shona','sn','n','noth','n','n','n','n','n','n',83),(84,'Sindhi','sd','n','noth','n','n','n','n','n','n',84),(85,'Sinhala','si','n','noth','n','n','n','n','n','n',85),(86,'Slovak','sk','n','noth','n','n','n','n','n','n',86),(87,'Slovenian','sl','n','noth','n','n','n','n','n','n',87),(88,'Somali','so','n','noth','n','n','n','n','n','n',88),(89,'Spanish','es','n','smth','n','y','n','n','n','n',89),(90,'Sundanese','su','n','noth','n','n','n','n','n','n',90),(91,'Swahili','sw','n','noth','n','n','n','n','n','n',91),(92,'Swedish','sv','n','noth','n','n','n','n','n','n',92),(93,'Tajik','tg','n','noth','n','n','n','n','n','n',93),(94,'Tamil','ta','n','noth','n','n','n','n','n','n',94),(95,'Tatar','tt','n','noth','n','n','n','n','n','n',95),(96,'Telugu','te','n','noth','n','n','n','n','n','n',96),(97,'Thai','th','n','smth','n','y','n','n','n','n',97),(98,'Turkish','tr','n','noth','n','n','n','n','n','n',98),(99,'Turkmen','tk','n','noth','n','n','n','n','n','n',99),(100,'Ukrainian','uk','n','noth','n','n','n','n','n','n',100),(101,'Urdu','ur','n','noth','n','n','n','n','n','n',101),(102,'Uyghur','ug','n','noth','n','n','n','n','n','n',102),(103,'Uzbek','uz','n','noth','n','n','n','n','n','n',103),(104,'Vietnamese','vi','n','noth','n','n','n','n','n','n',104),(105,'Welsh','cy','n','noth','n','n','n','n','n','n',105),(106,'Xhosa','xh','n','noth','n','n','n','n','n','n',106),(107,'Yiddish','yi','n','noth','n','n','n','n','n','n',107),(108,'Yoruba','yo','n','noth','n','n','n','n','n','n',108),(109,'Zulu','zu','n','noth','n','n','n','n','n','n',109),(110,'Hebrew','he','n','noth','n','n','n','n','n','n',110),(111,'Chinese (Simplified)','zh','n','noth','n','n','n','n','n','n',111);
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
  `title` varchar(255) NOT NULL DEFAULT '',
  `cfgField` int(11) NOT NULL DEFAULT '0',
  `cfgValue` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  KEY `cfgField` (`cfgField`),
  FULLTEXT KEY `cfgValue` (`cfgValue`)
) ENGINE=MyISAM AUTO_INCREMENT=172 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param`
--

LOCK TABLES `param` WRITE;
/*!40000 ALTER TABLE `param` DISABLE KEYS */;
INSERT INTO `param` VALUES (90,1487,'Путь к css-нику для подцепки редактором',2455,'/css/style.css'),(134,1814,'Во всю ширину',2443,'1'),(102,1487,'Во всю ширину',2443,'1'),(127,109,'Единица измерения',2462,'px'),(128,110,'Единица измерения',2462,'px'),(160,19,'Группировка опций по столбцу',2439,'612'),(156,18,'Группировка опций по столбцу',2439,'1366'),(162,2315,'Единица измерения',2462,'px'),(159,12,'Группировка опций по столбцу',2439,'612'),(161,2196,'Группировка опций по столбцу',2439,'2308'),(163,2385,'Плейсхолдер',2438,'Без изменений'),(164,2401,'Отображаемый формат времени',2463,'H:i:s'),(165,2401,'Отображаемый формат даты',2464,'Y-m-d'),(166,1443,'Дополнительно передавать параметры (в виде атрибутов)',2440,'470'),(167,34,'Дополнительно передавать параметры (в виде атрибутов)',2440,'470');
/*!40000 ALTER TABLE `param` ENABLE KEYS */;
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
INSERT INTO `profile` VALUES (1,'Разработчик','y',11,'',1,15,'n','s'),(12,'Administrator','y',11,'',12,15,'n','p');
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
) ENGINE=MyISAM AUTO_INCREMENT=638 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queuechunk`
--

LOCK TABLES `queuechunk` WRITE;
/*!40000 ALTER TABLE `queuechunk` DISABLE KEYS */;
INSERT INTO `queuechunk` VALUES (545,145,'action:title','`type` = \"s\"','finished',22,'finished',22,'finished',22,'finished',22,1,0,'none',162),(543,144,'section2action:title','`sectionId` IN (1,2,5,6,7,8,10,11,12,13,14,16,22,101,201,224,389,390,387,388,391,392,393,394,396,405,406,407)','finished',156,'finished',156,'noneed',0,'finished',156,2,0,'none',0),(544,144,'section2action:rename','`sectionId` IN (1,2,5,6,7,8,10,11,12,13,14,16,22,101,201,224,389,390,387,388,391,392,393,394,396,405,406,407)','finished',156,'finished',156,'noneed',0,'finished',156,3,0,'none',0),(542,144,'action:title','`type` = \"s\"','finished',22,'finished',22,'noneed',0,'finished',22,1,0,'none',0),(546,145,'section2action:title','`sectionId` IN (1,2,5,6,7,8,10,11,12,13,14,16,22,101,201,224,389,390,387,388,391,392,393,394,396,405,406,407)','finished',156,'finished',156,'finished',156,'finished',156,2,0,'none',0),(547,145,'section2action:rename','`sectionId` IN (1,2,5,6,7,8,10,11,12,13,14,16,22,101,201,224,389,390,387,388,391,392,393,394,396,405,406,407)','finished',156,'finished',156,'finished',156,'finished',156,3,0,'none',56),(548,146,'section:title','`type` = \"s\"','finished',28,'finished',28,'finished',28,'finished',28,548,0,'adminSystemUi',316),(549,146,'section:title','`type` = \"p\"','finished',1,'finished',1,'finished',1,'finished',1,549,0,'adminCustomUi',8),(550,147,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,550,0,'none',0),(551,147,'grid:title','`fieldId` = \"2414\"','finished',3,'finished',3,'finished',3,'finished',3,551,550,'none',0),(552,147,'search:title','`fieldId` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,552,550,'none',0),(553,147,'alteredField:title','`fieldId` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,553,550,'none',0),(554,147,'consider:title','`consider` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,554,550,'none',0),(555,147,'param:title','`cfgField` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,555,550,'none',0),(556,148,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,556,0,'none',0),(557,148,'grid:title','`fieldId` = \"2414\"','finished',3,'finished',3,'finished',3,'finished',3,557,556,'none',0),(558,148,'search:title','`fieldId` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,558,556,'none',0),(559,148,'alteredField:title','`fieldId` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,559,556,'none',0),(560,148,'consider:title','`consider` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,560,556,'none',0),(561,148,'param:title','`cfgField` = \"2414\"','finished',0,'finished',0,'finished',0,'finished',0,561,556,'none',0),(562,149,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,562,0,'none',0),(563,149,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,563,562,'none',0),(564,150,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,564,0,'none',0),(565,150,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,565,564,'none',0),(566,151,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,566,0,'none',0),(567,151,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,567,566,'none',0),(568,152,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,568,0,'none',0),(569,152,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,569,568,'none',0),(570,153,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,570,0,'none',0),(571,153,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,571,570,'none',0),(572,154,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,572,0,'none',0),(573,154,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,573,572,'none',0),(574,155,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,574,0,'none',0),(575,155,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,575,574,'none',0),(576,156,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,576,0,'none',0),(577,156,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,577,576,'none',0),(578,157,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,578,0,'none',0),(579,157,'grid:title','`fieldId` = \"19\"','finished',1,'finished',1,'finished',1,'finished',1,579,578,'none',0),(580,157,'search:title','`fieldId` = \"19\"','finished',1,'finished',1,'finished',1,'finished',1,580,578,'none',0),(581,157,'alteredField:title','`fieldId` = \"19\"','finished',0,'finished',0,'finished',0,'finished',0,581,578,'none',0),(582,157,'consider:title','`consider` = \"19\"','finished',4,'finished',4,'finished',4,'finished',4,582,578,'none',0),(583,157,'param:title','`cfgField` = \"19\"','finished',0,'finished',0,'finished',0,'finished',0,583,578,'none',0),(584,158,'grid:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,584,0,'none',0),(585,158,'grid:alterTitle','`title` = \"14\"','finished',0,'finished',0,'finished',0,'finished',0,585,584,'none',0),(586,159,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,586,0,'none',0),(587,159,'grid:title','`fieldId` = \"19\"','finished',1,'finished',1,'finished',1,'finished',1,587,586,'none',0),(588,159,'search:title','`fieldId` = \"19\"','finished',1,'finished',1,'finished',1,'finished',1,588,586,'none',0),(589,159,'alteredField:title','`fieldId` = \"19\"','finished',0,'finished',0,'finished',0,'finished',0,589,586,'none',0),(590,159,'consider:title','`consider` = \"19\"','finished',4,'finished',4,'finished',4,'finished',4,590,586,'none',0),(591,159,'param:title','`cfgField` = \"19\"','finished',0,'finished',0,'finished',0,'finished',0,591,586,'none',0),(592,160,'grid:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,592,0,'none',0),(593,160,'grid:alterTitle','`title` = \"14\"','finished',0,'finished',0,'finished',0,'finished',0,593,592,'none',0),(594,161,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,594,0,'none',0),(595,161,'grid:tooltip','`fieldId` = \"767\"','finished',1,'finished',1,'finished',1,'finished',1,595,594,'none',0),(596,162,'grid:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,596,0,'none',0),(597,162,'grid:alterTitle','`title` = \"2644\"','finished',0,'finished',0,'finished',0,'finished',0,597,596,'none',0),(598,163,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,598,0,'none',0),(599,163,'grid:tooltip','`fieldId` = \"767\"','finished',1,'finished',1,'finished',1,'finished',1,599,598,'none',0),(600,164,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,600,0,'none',0),(601,164,'grid:title','`fieldId` = \"767\"','finished',1,'finished',1,'finished',1,'finished',1,601,600,'none',0),(602,164,'search:title','`fieldId` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,602,600,'none',0),(603,164,'alteredField:title','`fieldId` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,603,600,'none',0),(604,164,'consider:title','`consider` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,604,600,'none',0),(605,164,'param:title','`cfgField` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,605,600,'none',0),(606,165,'grid:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,606,0,'none',0),(607,165,'grid:alterTitle','`title` = \"2644\"','finished',0,'finished',0,'finished',0,'finished',0,607,606,'none',0),(608,166,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,608,0,'none',0),(609,166,'grid:title','`fieldId` = \"767\"','finished',1,'finished',1,'finished',1,'finished',1,609,608,'none',0),(610,166,'search:title','`fieldId` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,610,608,'none',0),(611,166,'alteredField:title','`fieldId` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,611,608,'none',0),(612,166,'consider:title','`consider` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,612,608,'none',0),(613,166,'param:title','`cfgField` = \"767\"','finished',0,'finished',0,'finished',0,'finished',0,613,608,'none',0),(614,167,'grid:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,614,0,'none',0),(615,167,'grid:alterTitle','`title` = \"2644\"','finished',0,'finished',0,'finished',0,'finished',0,615,614,'none',0),(616,168,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,616,0,'none',0),(617,168,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,617,616,'none',0),(618,169,'field:tooltip','FALSE','finished',0,'finished',0,'finished',0,'finished',0,618,0,'none',0),(619,169,'grid:tooltip','`fieldId` = \"502\"','finished',1,'finished',1,'finished',1,'finished',1,619,618,'none',0),(620,170,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,620,0,'none',0),(621,170,'grid:title','`fieldId` = \"5\"','finished',1,'finished',1,'finished',1,'finished',1,621,620,'none',0),(622,170,'search:title','`fieldId` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,622,620,'none',0),(623,170,'alteredField:title','`fieldId` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,623,620,'none',0),(624,170,'consider:title','`consider` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,624,620,'none',0),(625,170,'param:title','`cfgField` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,625,620,'none',0),(626,171,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,626,0,'none',0),(627,171,'grid:title','`fieldId` = \"5\"','finished',1,'finished',1,'finished',1,'finished',1,627,626,'none',0),(628,171,'search:title','`fieldId` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,628,626,'none',0),(629,171,'alteredField:title','`fieldId` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,629,626,'none',0),(630,171,'consider:title','`consider` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,630,626,'none',0),(631,171,'param:title','`cfgField` = \"5\"','finished',0,'finished',0,'finished',0,'finished',0,631,626,'none',0),(632,172,'field:title','FALSE','finished',0,'finished',0,'finished',0,'finished',0,632,0,'none',0),(633,172,'grid:title','`fieldId` = \"476\"','finished',2,'finished',2,'finished',2,'finished',2,633,632,'none',0),(634,172,'search:title','`fieldId` = \"476\"','finished',1,'finished',1,'finished',1,'finished',1,634,632,'none',0),(635,172,'alteredField:title','`fieldId` = \"476\"','finished',0,'finished',0,'finished',0,'finished',0,635,632,'none',0),(636,172,'consider:title','`consider` = \"476\"','finished',1,'finished',1,'finished',1,'finished',1,636,632,'none',0),(637,172,'param:title','`cfgField` = \"476\"','finished',0,'finished',0,'finished',0,'finished',0,637,632,'none',0);
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
) ENGINE=MyISAM AUTO_INCREMENT=1517 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queueitem`
--

LOCK TABLES `queueitem` WRITE;
/*!40000 ALTER TABLE `queueitem` DISABLE KEYS */;
INSERT INTO `queueitem` VALUES (800,144,542,'42','Schedule','','apply'),(798,144,542,'40','Available languages','','apply'),(797,144,542,'39','Activate','','apply'),(794,144,542,'36','Export','','apply'),(793,144,542,'35','Js','','apply'),(791,144,542,'33','Author','','apply'),(783,144,542,'2','Details','','apply'),(784,144,542,'3','Save','','apply'),(785,144,542,'4','Delete','','apply'),(969,144,544,'11','','','apply'),(967,144,544,'9','','','apply'),(965,144,544,'7','','','apply'),(963,144,544,'5','','','apply'),(961,144,544,'2','','','apply'),(848,144,543,'47','Save','','apply'),(830,144,543,'28','List','','apply'),(810,144,543,'8','Save','','apply'),(1099,144,544,'1629','','','apply'),(1098,144,544,'1628','','','apply'),(1097,144,544,'1627','','','apply'),(1096,144,544,'1617','','','apply'),(1095,144,544,'1615','','','apply'),(1094,144,544,'1614','','','apply'),(1093,144,544,'1613','Reload websocket server','','apply'),(1092,144,544,'1612','','','apply'),(1091,144,544,'1611','','','apply'),(1090,144,544,'1610','','','apply'),(1089,144,544,'1609','','','apply'),(1088,144,544,'1603','','','apply'),(1087,144,544,'1602','','','apply'),(1086,144,544,'1601','','','apply'),(1085,144,544,'1600','','','apply'),(1084,144,544,'1599','','','apply'),(1083,144,544,'1598','','','apply'),(1082,144,544,'1597','','','apply'),(1081,144,544,'1596','','','apply'),(1080,144,544,'1595','','','apply'),(1079,144,544,'1594','','','apply'),(1078,144,544,'1593','','','apply'),(1077,144,544,'1592','','','apply'),(1076,144,544,'1591','','','apply'),(1075,144,544,'1590','','','apply'),(1074,144,544,'1589','','','apply'),(1073,144,544,'1588','','','apply'),(1072,144,544,'1587','','','apply'),(1071,144,544,'1586','','','apply'),(1070,144,544,'1585','','','apply'),(1069,144,544,'1584','','','apply'),(1068,144,544,'1583','','','apply'),(1067,144,544,'1582','Select mode','','apply'),(1066,144,544,'1581','','','apply'),(1065,144,544,'1580','','','apply'),(1064,144,544,'1579','','','apply'),(1063,144,544,'1578','','','apply'),(1062,144,544,'1577','','','apply'),(1061,144,544,'1576','','','apply'),(1060,144,544,'1575','','','apply'),(1059,144,544,'1574','','','apply'),(1058,144,544,'1573','','','apply'),(1057,144,544,'1572','','','apply'),(1056,144,544,'1571','','','apply'),(1055,144,544,'1570','','','apply'),(1054,144,544,'1569','','','apply'),(1053,144,544,'1568','','','apply'),(1052,144,544,'1567','','','apply'),(1051,144,544,'1566','','','apply'),(1050,144,544,'1565','','','apply'),(1049,144,544,'1564','','','apply'),(1048,144,544,'1563','','','apply'),(1047,144,544,'1562','','','apply'),(1046,144,544,'1561','','','apply'),(1045,144,544,'1560','','','apply'),(1044,144,544,'1559','','','apply'),(1043,144,544,'1549','','','apply'),(1042,144,544,'1548','','','apply'),(1041,144,544,'1547','','','apply'),(1040,144,544,'1545','','','apply'),(1039,144,544,'1522','','','apply'),(1038,144,544,'1296','','','apply'),(1037,144,544,'1295','','','apply'),(1036,144,544,'1268','','','apply'),(1035,144,544,'881','','','apply'),(1034,144,544,'880','','','apply'),(1033,144,544,'879','','','apply'),(1032,144,544,'878','','','apply'),(1031,144,544,'877','','','apply'),(1030,144,544,'876','','','apply'),(1029,144,544,'875','','','apply'),(1028,144,544,'833','','','apply'),(1027,144,544,'808','','','apply'),(1026,144,544,'806','','','apply'),(1025,144,544,'792','','','apply'),(1024,144,544,'791','','','apply'),(1023,144,544,'790','','','apply'),(1022,144,544,'789','','','apply'),(1021,144,544,'380','','','apply'),(1020,144,544,'379','','','apply'),(1019,144,544,'378','','','apply'),(1018,144,544,'377','','','apply'),(1017,144,544,'330','','','apply'),(1016,144,544,'329','','','apply'),(1015,144,544,'77','','','apply'),(1014,144,544,'76','','','apply'),(1013,144,544,'75','','','apply'),(1012,144,544,'74','','','apply'),(1011,144,544,'69','','','apply'),(1010,144,544,'68','','','apply'),(1009,144,544,'67','','','apply'),(1008,144,544,'54','','','apply'),(1007,144,544,'53','','','apply'),(1006,144,544,'52','','','apply'),(1005,144,544,'48','','','apply'),(1004,144,544,'47','','','apply'),(1003,144,544,'46','','','apply'),(1002,144,544,'45','','','apply'),(1001,144,544,'44','','','apply'),(1000,144,544,'43','','','apply'),(999,144,544,'42','','','apply'),(998,144,544,'41','','','apply'),(996,144,544,'39','','','apply'),(997,144,544,'40','','','apply'),(994,144,544,'37','','','apply'),(993,144,544,'36','','','apply'),(992,144,544,'35','','','apply'),(991,144,544,'34','','','apply'),(990,144,544,'33','','','apply'),(989,144,544,'32','','','apply'),(988,144,544,'30','','','apply'),(987,144,544,'29','','','apply'),(986,144,544,'28','','','apply'),(985,144,544,'27','','','apply'),(983,144,544,'25','','','apply'),(982,144,544,'24','','','apply'),(981,144,544,'23','','','apply'),(980,144,544,'22','','','apply'),(979,144,544,'21','','','apply'),(978,144,544,'20','','','apply'),(977,144,544,'19','','','apply'),(976,144,544,'18','','','apply'),(975,144,544,'17','','','apply'),(974,144,544,'16','','','apply'),(973,144,544,'15','','','apply'),(972,144,544,'14','','','apply'),(808,144,543,'6','List','','apply'),(807,144,543,'5','Delete','','apply'),(806,144,543,'3','Save','','apply'),(805,144,543,'2','Details','','apply'),(804,144,543,'1','List','','apply'),(803,144,542,'45','Copy','','apply'),(802,144,542,'44','Restart','','apply'),(801,144,542,'43','Wordings','','apply'),(799,144,542,'41','Run','','apply'),(796,144,542,'38','','','apply'),(795,144,542,'37','Go to','','apply'),(792,144,542,'34','PHP','','apply'),(790,144,542,'20','Authorization','','apply'),(789,144,542,'18','Refresh cache','','apply'),(788,144,542,'7','Status','','apply'),(787,144,542,'6','Below','','apply'),(786,144,542,'5','Higher','','apply'),(782,144,542,'1','List','','apply'),(971,144,544,'13','','','apply'),(970,144,544,'12','','','apply'),(968,144,544,'10','','','apply'),(966,144,544,'8','','','apply'),(964,144,544,'6','','','apply'),(962,144,544,'3','','','apply'),(960,144,544,'1','','','apply'),(959,144,543,'1645','Delete','','apply'),(958,144,543,'1644','Save','','apply'),(957,144,543,'1643','Details','','apply'),(956,144,543,'1642','List','','apply'),(955,144,543,'1641','Export','','apply'),(954,144,543,'1640','Below','','apply'),(953,144,543,'1639','Higher','','apply'),(952,144,543,'1638','Delete','','apply'),(951,144,543,'1637','Save','','apply'),(950,144,543,'1636','Details','','apply'),(949,144,543,'1635','List','','apply'),(948,144,543,'1634','Export','','apply'),(947,144,543,'1633','Activate','','apply'),(946,144,543,'1632','Below','','apply'),(945,144,543,'1631','Higher','','apply'),(944,144,543,'1630','Delete','','apply'),(943,144,543,'1629','Save','','apply'),(942,144,543,'1628','Details','','apply'),(941,144,543,'1627','List','','apply'),(940,144,543,'1617','Authorization','','apply'),(939,144,543,'1615','Copy','','apply'),(938,144,543,'1614','Export','','apply'),(937,144,543,'1613','Restart','','apply'),(936,144,543,'1612','Delete','','apply'),(935,144,543,'1611','Save','','apply'),(934,144,543,'1610','List','','apply'),(933,144,543,'1609','Details','','apply'),(932,144,543,'1603','Wordings','','apply'),(931,144,543,'1602','Save','','apply'),(930,144,543,'1601','List','','apply'),(929,144,543,'1600','Details','','apply'),(928,144,543,'1599','List','','apply'),(927,144,543,'1598','Run','','apply'),(926,144,543,'1597','Delete','','apply'),(925,144,543,'1596','Details','','apply'),(924,144,543,'1595','List','','apply'),(923,144,543,'1594','Available languages','','apply'),(922,144,543,'1593','Below','','apply'),(921,144,543,'1592','Higher','','apply'),(920,144,543,'1591','Export','','apply'),(919,144,543,'1590','Export','','apply'),(918,144,543,'1589','Export','','apply'),(917,144,543,'1588','Export','','apply'),(916,144,543,'1587','Export','','apply'),(915,144,543,'1586','Export','','apply'),(914,144,543,'1585','Export','','apply'),(913,144,543,'1584','Export','','apply'),(912,144,543,'1583','Export','','apply'),(911,144,543,'1582','Activate','','apply'),(910,144,543,'1581','Save','','apply'),(909,144,543,'1580','Details','','apply'),(908,144,543,'1579','List','','apply'),(907,144,543,'1578','Delete','','apply'),(906,144,543,'1577','Save','','apply'),(905,144,543,'1576','Details','','apply'),(904,144,543,'1575','List','','apply'),(903,144,543,'1574','Status','','apply'),(902,144,543,'1573','Delete','','apply'),(901,144,543,'1572','Save','','apply'),(900,144,543,'1571','Details','','apply'),(899,144,543,'1570','List','','apply'),(898,144,543,'1569','Export','','apply'),(897,144,543,'1568','Export','','apply'),(896,144,543,'1567','Delete','','apply'),(895,144,543,'1566','Save','','apply'),(894,144,543,'1565','Details','','apply'),(893,144,543,'1564','List','','apply'),(892,144,543,'1563','Delete','','apply'),(891,144,543,'1562','Save','','apply'),(890,144,543,'1561','Details','','apply'),(889,144,543,'1560','List','','apply'),(888,144,543,'1559','Status','','apply'),(887,144,543,'1549','Js','','apply'),(886,144,543,'1548','PHP','','apply'),(885,144,543,'1547','PHP','','apply'),(884,144,543,'1545','Status','','apply'),(883,144,543,'1522','Status','','apply'),(882,144,543,'1296','Below','','apply'),(881,144,543,'1295','Higher','','apply'),(880,144,543,'1268','Status','','apply'),(879,144,543,'881','Status','','apply'),(878,144,543,'880','Below','','apply'),(877,144,543,'879','Higher','','apply'),(876,144,543,'878','Save','','apply'),(875,144,543,'877','Delete','','apply'),(874,144,543,'876','Details','','apply'),(873,144,543,'875','List','','apply'),(872,144,543,'833','Delete','','apply'),(871,144,543,'808','Delete','','apply'),(870,144,543,'806','Delete','','apply'),(869,144,543,'792','Delete','','apply'),(868,144,543,'791','Save','','apply'),(867,144,543,'790','Details','','apply'),(866,144,543,'789','List','','apply'),(865,144,543,'380','Delete','','apply'),(864,144,543,'379','Save','','apply'),(863,144,543,'378','Details','','apply'),(862,144,543,'377','List','','apply'),(861,144,543,'330','Below','','apply'),(860,144,543,'329','Higher','','apply'),(859,144,543,'77','Delete','','apply'),(858,144,543,'76','Save','','apply'),(857,144,543,'75','Details','','apply'),(856,144,543,'74','List','','apply'),(855,144,543,'69','Status','','apply'),(854,144,543,'68','Status','','apply'),(853,144,543,'67','Status','','apply'),(852,144,543,'54','Save','','apply'),(851,144,543,'53','Details','','apply'),(850,144,543,'52','List','','apply'),(849,144,543,'48','Delete','','apply'),(847,144,543,'46','Details','','apply'),(846,144,543,'45','List','','apply'),(845,144,543,'44','Delete','','apply'),(844,144,543,'43','Save','','apply'),(843,144,543,'42','Details','','apply'),(842,144,543,'41','List','','apply'),(841,144,543,'40','Save','','apply'),(840,144,543,'39','Details','','apply'),(839,144,543,'38','List','','apply'),(838,144,543,'37','Below','','apply'),(837,144,543,'36','Higher','','apply'),(836,144,543,'35','Delete','','apply'),(835,144,543,'34','Save','','apply'),(834,144,543,'33','Details','','apply'),(833,144,543,'32','List','','apply'),(832,144,543,'30','Save','','apply'),(831,144,543,'29','Details','','apply'),(829,144,543,'27','Below','','apply'),(828,144,543,'26','Higher','','apply'),(827,144,543,'25','Below','','apply'),(826,144,543,'24','Higher','','apply'),(825,144,543,'23','Delete','','apply'),(824,144,543,'22','Save','','apply'),(823,144,543,'21','Details','','apply'),(822,144,543,'20','List','','apply'),(821,144,543,'19','Below','','apply'),(820,144,543,'18','Higher','','apply'),(819,144,543,'17','Delete','','apply'),(818,144,543,'16','Save','','apply'),(817,144,543,'15','Details','','apply'),(816,144,543,'14','List','','apply'),(815,144,543,'13','Delete','','apply'),(814,144,543,'12','Save','','apply'),(813,144,543,'11','Details','','apply'),(812,144,543,'10','List','','apply'),(811,144,543,'9','Delete','','apply'),(809,144,543,'7','Details','','apply'),(995,144,544,'38','','','apply'),(984,144,544,'26','','','apply'),(1100,144,544,'1630','','','apply'),(1101,144,544,'1631','','','apply'),(1102,144,544,'1632','','','apply'),(1103,144,544,'1633','Select mode','','apply'),(1104,144,544,'1634','','','apply'),(1105,144,544,'1635','','','apply'),(1106,144,544,'1636','','','apply'),(1107,144,544,'1637','','','apply'),(1108,144,544,'1638','','','apply'),(1109,144,544,'1639','','','apply'),(1110,144,544,'1640','','','apply'),(1111,144,544,'1641','','','apply'),(1112,144,544,'1642','','','apply'),(1113,144,544,'1643','','','apply'),(1114,144,544,'1644','','','apply'),(1115,144,544,'1645','','','apply'),(1116,145,545,'1','Список','List','apply'),(1117,145,545,'2','Детали','Details','apply'),(1118,145,545,'3','Сохранить','Save','apply'),(1119,145,545,'4','Удалить','Delete','apply'),(1120,145,545,'5','Выше','Higher','apply'),(1121,145,545,'6','Ниже','Below','apply'),(1122,145,545,'7','Статус','Status','apply'),(1123,145,545,'18','Обновить кэш','Refresh cache','apply'),(1124,145,545,'20','Авторизация','Authorization','apply'),(1125,145,545,'33','Автор','Author','apply'),(1126,145,545,'34','PHP','PHP','apply'),(1127,145,545,'35','JS','Js','apply'),(1128,145,545,'36','Экспорт','Export','apply'),(1129,145,545,'37','Перейти','Go to','apply'),(1130,145,545,'38','','','apply'),(1131,145,545,'39','Активировать','Activate','apply'),(1132,145,545,'40','Доступные языки','Available languages','apply'),(1133,145,545,'41','Запустить','Run','apply'),(1134,145,545,'42','График','Schedule','apply'),(1135,145,545,'43','Вординги','Wordings','apply'),(1136,145,545,'44','Перезапустить','Restart','apply'),(1137,145,545,'45','Копировать','Copy','apply'),(1138,145,546,'1','Список','List','apply'),(1139,145,546,'2','Детали','Details','apply'),(1140,145,546,'3','Сохранить','Save','apply'),(1141,145,546,'5','Удалить','Delete','apply'),(1142,145,546,'6','Список','List','apply'),(1143,145,546,'7','Детали','Details','apply'),(1144,145,546,'8','Сохранить','Save','apply'),(1145,145,546,'9','Удалить','Delete','apply'),(1146,145,546,'10','Список','List','apply'),(1147,145,546,'11','Детали','Details','apply'),(1148,145,546,'12','Сохранить','Save','apply'),(1149,145,546,'13','Удалить','Delete','apply'),(1150,145,546,'14','Список','List','apply'),(1151,145,546,'15','Детали','Details','apply'),(1152,145,546,'16','Сохранить','Save','apply'),(1153,145,546,'17','Удалить','Delete','apply'),(1154,145,546,'18','Выше','Higher','apply'),(1155,145,546,'19','Ниже','Below','apply'),(1156,145,546,'20','Список','List','apply'),(1157,145,546,'21','Детали','Details','apply'),(1158,145,546,'22','Сохранить','Save','apply'),(1159,145,546,'23','Удалить','Delete','apply'),(1160,145,546,'24','Выше','Higher','apply'),(1161,145,546,'25','Ниже','Below','apply'),(1162,145,546,'26','Выше','Higher','apply'),(1163,145,546,'27','Ниже','Below','apply'),(1164,145,546,'28','Список','List','apply'),(1165,145,546,'29','Детали','Details','apply'),(1166,145,546,'30','Сохранить','Save','apply'),(1167,145,546,'32','Список','List','apply'),(1168,145,546,'33','Детали','Details','apply'),(1169,145,546,'34','Сохранить','Save','apply'),(1170,145,546,'35','Удалить','Delete','apply'),(1171,145,546,'36','Выше','Higher','apply'),(1172,145,546,'37','Ниже','Below','apply'),(1173,145,546,'38','Список','List','apply'),(1174,145,546,'39','Детали','Details','apply'),(1175,145,546,'40','Сохранить','Save','apply'),(1176,145,546,'41','Список','List','apply'),(1177,145,546,'42','Детали','Details','apply'),(1178,145,546,'43','Сохранить','Save','apply'),(1179,145,546,'44','Удалить','Delete','apply'),(1180,145,546,'45','Список','List','apply'),(1181,145,546,'46','Детали','Details','apply'),(1182,145,546,'47','Сохранить','Save','apply'),(1183,145,546,'48','Удалить','Delete','apply'),(1184,145,546,'52','Список','List','apply'),(1185,145,546,'53','Детали','Details','apply'),(1186,145,546,'54','Сохранить','Save','apply'),(1187,145,546,'67','Статус','Status','apply'),(1188,145,546,'68','Статус','Status','apply'),(1189,145,546,'69','Статус','Status','apply'),(1190,145,546,'74','Список','List','apply'),(1191,145,546,'75','Детали','Details','apply'),(1192,145,546,'76','Сохранить','Save','apply'),(1193,145,546,'77','Удалить','Delete','apply'),(1194,145,546,'329','Выше','Higher','apply'),(1195,145,546,'330','Ниже','Below','apply'),(1196,145,546,'377','Список','List','apply'),(1197,145,546,'378','Детали','Details','apply'),(1198,145,546,'379','Сохранить','Save','apply'),(1199,145,546,'380','Удалить','Delete','apply'),(1200,145,546,'789','Список','List','apply'),(1201,145,546,'790','Детали','Details','apply'),(1202,145,546,'791','Сохранить','Save','apply'),(1203,145,546,'792','Удалить','Delete','apply'),(1204,145,546,'806','Удалить','Delete','apply'),(1205,145,546,'808','Удалить','Delete','apply'),(1206,145,546,'833','Удалить','Delete','apply'),(1207,145,546,'875','Список','List','apply'),(1208,145,546,'876','Детали','Details','apply'),(1209,145,546,'877','Удалить','Delete','apply'),(1210,145,546,'878','Сохранить','Save','apply'),(1211,145,546,'879','Выше','Higher','apply'),(1212,145,546,'880','Ниже','Below','apply'),(1213,145,546,'881','Статус','Status','apply'),(1214,145,546,'1268','Статус','Status','apply'),(1215,145,546,'1295','Выше','Higher','apply'),(1216,145,546,'1296','Ниже','Below','apply'),(1217,145,546,'1522','Статус','Status','apply'),(1218,145,546,'1545','Статус','Status','apply'),(1219,145,546,'1547','PHP','PHP','apply'),(1220,145,546,'1548','PHP','PHP','apply'),(1221,145,546,'1549','JS','Js','apply'),(1222,145,546,'1559','Статус','Status','apply'),(1223,145,546,'1560','Список','List','apply'),(1224,145,546,'1561','Детали','Details','apply'),(1225,145,546,'1562','Сохранить','Save','apply'),(1226,145,546,'1563','Удалить','Delete','apply'),(1227,145,546,'1564','Список','List','apply'),(1228,145,546,'1565','Детали','Details','apply'),(1229,145,546,'1566','Сохранить','Save','apply'),(1230,145,546,'1567','Удалить','Delete','apply'),(1231,145,546,'1568','Экспорт','Export','apply'),(1232,145,546,'1569','Экспорт','Export','apply'),(1233,145,546,'1570','Список','List','apply'),(1234,145,546,'1571','Детали','Details','apply'),(1235,145,546,'1572','Сохранить','Save','apply'),(1236,145,546,'1573','Удалить','Delete','apply'),(1237,145,546,'1574','Статус','Status','apply'),(1238,145,546,'1575','Список','List','apply'),(1239,145,546,'1576','Детали','Details','apply'),(1240,145,546,'1577','Сохранить','Save','apply'),(1241,145,546,'1578','Удалить','Delete','apply'),(1242,145,546,'1579','Список','List','apply'),(1243,145,546,'1580','Детали','Details','apply'),(1244,145,546,'1581','Сохранить','Save','apply'),(1245,145,546,'1582','Активировать','Activate','apply'),(1246,145,546,'1583','Экспорт','Export','apply'),(1247,145,546,'1584','Экспорт','Export','apply'),(1248,145,546,'1585','Экспорт','Export','apply'),(1249,145,546,'1586','Экспорт','Export','apply'),(1250,145,546,'1587','Экспорт','Export','apply'),(1251,145,546,'1588','Экспорт','Export','apply'),(1252,145,546,'1589','Экспорт','Export','apply'),(1253,145,546,'1590','Экспорт','Export','apply'),(1254,145,546,'1591','Экспорт','Export','apply'),(1255,145,546,'1592','Выше','Higher','apply'),(1256,145,546,'1593','Ниже','Below','apply'),(1257,145,546,'1594','Доступные языки','Available languages','apply'),(1258,145,546,'1595','Список','List','apply'),(1259,145,546,'1596','Детали','Details','apply'),(1260,145,546,'1597','Удалить','Delete','apply'),(1261,145,546,'1598','Запустить','Run','apply'),(1262,145,546,'1599','Список','List','apply'),(1263,145,546,'1600','Детали','Details','apply'),(1264,145,546,'1601','Список','List','apply'),(1265,145,546,'1602','Сохранить','Save','apply'),(1266,145,546,'1603','Вординги','Wordings','apply'),(1267,145,546,'1609','Детали','Details','apply'),(1268,145,546,'1610','Список','List','apply'),(1269,145,546,'1611','Сохранить','Save','apply'),(1270,145,546,'1612','Удалить','Delete','apply'),(1271,145,546,'1613','Перезапустить','Restart','apply'),(1272,145,546,'1614','Экспорт','Export','apply'),(1273,145,546,'1615','Копировать','Copy','apply'),(1274,145,546,'1617','Авторизация','Authorization','apply'),(1275,145,546,'1627','Список','List','apply'),(1276,145,546,'1628','Детали','Details','apply'),(1277,145,546,'1629','Сохранить','Save','apply'),(1278,145,546,'1630','Удалить','Delete','apply'),(1279,145,546,'1631','Выше','Higher','apply'),(1280,145,546,'1632','Ниже','Below','apply'),(1281,145,546,'1633','Активировать','Activate','apply'),(1282,145,546,'1634','Экспорт','Export','apply'),(1283,145,546,'1635','Список','List','apply'),(1284,145,546,'1636','Детали','Details','apply'),(1285,145,546,'1637','Сохранить','Save','apply'),(1286,145,546,'1638','Удалить','Delete','apply'),(1287,145,546,'1639','Выше','Higher','apply'),(1288,145,546,'1640','Ниже','Below','apply'),(1289,145,546,'1641','Экспорт','Export','apply'),(1290,145,546,'1642','Список','List','apply'),(1291,145,546,'1643','Детали','Details','apply'),(1292,145,546,'1644','Сохранить','Save','apply'),(1293,145,546,'1645','Удалить','Delete','apply'),(1294,145,547,'1','','','apply'),(1295,145,547,'2','','','apply'),(1296,145,547,'3','','','apply'),(1297,145,547,'5','','','apply'),(1298,145,547,'6','','','apply'),(1299,145,547,'7','','','apply'),(1300,145,547,'8','','','apply'),(1301,145,547,'9','','','apply'),(1302,145,547,'10','','','apply'),(1303,145,547,'11','','','apply'),(1304,145,547,'12','','','apply'),(1305,145,547,'13','','','apply'),(1306,145,547,'14','','','apply'),(1307,145,547,'15','','','apply'),(1308,145,547,'16','','','apply'),(1309,145,547,'17','','','apply'),(1310,145,547,'18','','','apply'),(1311,145,547,'19','','','apply'),(1312,145,547,'20','','','apply'),(1313,145,547,'21','','','apply'),(1314,145,547,'22','','','apply'),(1315,145,547,'23','','','apply'),(1316,145,547,'24','','','apply'),(1317,145,547,'25','','','apply'),(1318,145,547,'26','','','apply'),(1319,145,547,'27','','','apply'),(1320,145,547,'28','','','apply'),(1321,145,547,'29','','','apply'),(1322,145,547,'30','','','apply'),(1323,145,547,'32','','','apply'),(1324,145,547,'33','','','apply'),(1325,145,547,'34','','','apply'),(1326,145,547,'35','','','apply'),(1327,145,547,'36','','','apply'),(1328,145,547,'37','','','apply'),(1329,145,547,'38','','','apply'),(1330,145,547,'39','','','apply'),(1331,145,547,'40','','','apply'),(1332,145,547,'41','','','apply'),(1333,145,547,'42','','','apply'),(1334,145,547,'43','','','apply'),(1335,145,547,'44','','','apply'),(1336,145,547,'45','','','apply'),(1337,145,547,'46','','','apply'),(1338,145,547,'47','','','apply'),(1339,145,547,'48','','','apply'),(1340,145,547,'52','','','apply'),(1341,145,547,'53','','','apply'),(1342,145,547,'54','','','apply'),(1343,145,547,'67','','','apply'),(1344,145,547,'68','','','apply'),(1345,145,547,'69','','','apply'),(1346,145,547,'74','','','apply'),(1347,145,547,'75','','','apply'),(1348,145,547,'76','','','apply'),(1349,145,547,'77','','','apply'),(1350,145,547,'329','','','apply'),(1351,145,547,'330','','','apply'),(1352,145,547,'377','','','apply'),(1353,145,547,'378','','','apply'),(1354,145,547,'379','','','apply'),(1355,145,547,'380','','','apply'),(1356,145,547,'789','','','apply'),(1357,145,547,'790','','','apply'),(1358,145,547,'791','','','apply'),(1359,145,547,'792','','','apply'),(1360,145,547,'806','','','apply'),(1361,145,547,'808','','','apply'),(1362,145,547,'833','','','apply'),(1363,145,547,'875','','','apply'),(1364,145,547,'876','','','apply'),(1365,145,547,'877','','','apply'),(1366,145,547,'878','','','apply'),(1367,145,547,'879','','','apply'),(1368,145,547,'880','','','apply'),(1369,145,547,'881','','','apply'),(1370,145,547,'1268','','','apply'),(1371,145,547,'1295','','','apply'),(1372,145,547,'1296','','','apply'),(1373,145,547,'1522','','','apply'),(1374,145,547,'1545','','','apply'),(1375,145,547,'1547','','','apply'),(1376,145,547,'1548','','','apply'),(1377,145,547,'1549','','','apply'),(1378,145,547,'1559','','','apply'),(1379,145,547,'1560','','','apply'),(1380,145,547,'1561','','','apply'),(1381,145,547,'1562','','','apply'),(1382,145,547,'1563','','','apply'),(1383,145,547,'1564','','','apply'),(1384,145,547,'1565','','','apply'),(1385,145,547,'1566','','','apply'),(1386,145,547,'1567','','','apply'),(1387,145,547,'1568','','','apply'),(1388,145,547,'1569','','','apply'),(1389,145,547,'1570','','','apply'),(1390,145,547,'1571','','','apply'),(1391,145,547,'1572','','','apply'),(1392,145,547,'1573','','','apply'),(1393,145,547,'1574','','','apply'),(1394,145,547,'1575','','','apply'),(1395,145,547,'1576','','','apply'),(1396,145,547,'1577','','','apply'),(1397,145,547,'1578','','','apply'),(1398,145,547,'1579','','','apply'),(1399,145,547,'1580','','','apply'),(1400,145,547,'1581','','','apply'),(1401,145,547,'1582','Выбрать режим','Select mode','apply'),(1402,145,547,'1583','','','apply'),(1403,145,547,'1584','','','apply'),(1404,145,547,'1585','','','apply'),(1405,145,547,'1586','','','apply'),(1406,145,547,'1587','','','apply'),(1407,145,547,'1588','','','apply'),(1408,145,547,'1589','','','apply'),(1409,145,547,'1590','','','apply'),(1410,145,547,'1591','','','apply'),(1411,145,547,'1592','','','apply'),(1412,145,547,'1593','','','apply'),(1413,145,547,'1594','','','apply'),(1414,145,547,'1595','','','apply'),(1415,145,547,'1596','','','apply'),(1416,145,547,'1597','','','apply'),(1417,145,547,'1598','','','apply'),(1418,145,547,'1599','','','apply'),(1419,145,547,'1600','','','apply'),(1420,145,547,'1601','','','apply'),(1421,145,547,'1602','','','apply'),(1422,145,547,'1603','','','apply'),(1423,145,547,'1609','','','apply'),(1424,145,547,'1610','','','apply'),(1425,145,547,'1611','','','apply'),(1426,145,547,'1612','','','apply'),(1427,145,547,'1613','Перезагрузить websocket-сервер','Reload websocket server','apply'),(1428,145,547,'1614','','','apply'),(1429,145,547,'1615','','','apply'),(1430,145,547,'1617','','','apply'),(1431,145,547,'1627','','','apply'),(1432,145,547,'1628','','','apply'),(1433,145,547,'1629','','','apply'),(1434,145,547,'1630','','','apply'),(1435,145,547,'1631','','','apply'),(1436,145,547,'1632','','','apply'),(1437,145,547,'1633','Выбрать режим','Select mode','apply'),(1438,145,547,'1634','','','apply'),(1439,145,547,'1635','','','apply'),(1440,145,547,'1636','','','apply'),(1441,145,547,'1637','','','apply'),(1442,145,547,'1638','','','apply'),(1443,145,547,'1639','','','apply'),(1444,145,547,'1640','','','apply'),(1445,145,547,'1641','','','apply'),(1446,145,547,'1642','','','apply'),(1447,145,547,'1643','','','apply'),(1448,145,547,'1644','','','apply'),(1449,145,547,'1645','','','apply'),(1450,146,548,'1','Конфигурация','{\"en\":\"Configuration\"}','apply'),(1451,146,548,'2','Столбцы','{\"en\":\"Columns\"}','apply'),(1452,146,548,'5','Сущности','{\"en\":\"Entities\"}','apply'),(1453,146,548,'6','Поля в структуре','{\"en\":\"Fields in structure\"}','apply'),(1454,146,548,'7','Разделы','{\"en\":\"Sections\"}','apply'),(1455,146,548,'8','Действия','{\"en\":\"Actions\"}','apply'),(1456,146,548,'10','Действия','{\"en\":\"Actions\"}','apply'),(1457,146,548,'11','Столбцы грида','{\"en\":\"Grid columns\"}','apply'),(1458,146,548,'12','Возможные значения','{\"en\":\"Possible values\"}','apply'),(1459,146,548,'13','Роли','{\"en\":\"Roles\"}','apply'),(1460,146,548,'14','Пользователи','{\"en\":\"Users\"}','apply'),(1461,146,548,'16','Элементы','{\"en\":\"The elements\"}','apply'),(1462,146,548,'22','Копии изображения','{\"en\":\"Image Copies\"}','apply'),(1463,146,548,'101','Параметры','{\"en\":\"Options\"}','apply'),(1464,146,548,'201','Измененные поля','{\"en\":\"Modified fields\"}','apply'),(1465,146,548,'224','Фильтры','{\"en\":\"Filters\"}','apply'),(1466,146,548,'387','Языки','{\"en\":\"Languages\"}','apply'),(1467,146,548,'388','Зависимости','{\"en\":\"Dependencies\"}','apply'),(1468,146,548,'389','Уведомления','{\"en\":\"Notifications\"}','apply'),(1469,146,548,'390','Получатели','{\"en\":\"Recipients\"}','apply'),(1470,146,548,'391','Все поля','{\"en\":\"All fields\"}','apply'),(1471,146,548,'392','Очереди задач','{\"en\":\"Task queues\"}','apply'),(1472,146,548,'393','Сегменты очереди','{\"en\":\"Queue segments\"}','apply'),(1473,146,548,'394','Элементы очереди','{\"en\":\"Queue items\"}','apply'),(1474,146,548,'396','Рилтайм','{\"en\":\"Riltime\"}','apply'),(1475,146,548,'405','Возможные настройки','{\"en\":\"Possible settings\"}','apply'),(1476,146,548,'406','Возможные значения','{\"en\":\"Possible values\"}','apply'),(1477,146,548,'407','Все параметры','{\"en\":\"All parameters\"}','apply'),(1478,146,549,'403','Database','{\"en\":\"Database\"}','apply'),(1479,147,551,'2613','Элемент управления','Элемент управления1','apply'),(1480,147,551,'2618','Элемент управления','Элемент управления1','apply'),(1481,147,551,'2685','Элемент управления','Элемент управления1','apply'),(1482,148,557,'2613','Элемент управления1','Элемент управления','apply'),(1483,148,557,'2618','Элемент управления1','Элемент управления','apply'),(1484,148,557,'2685','Элемент управления1','Элемент управления','apply'),(1485,149,563,'2459','','','apply'),(1486,150,565,'2459','','','apply'),(1487,151,567,'2459','','','apply'),(1488,152,569,'2459','','','apply'),(1489,153,571,'2459','','','apply'),(1490,154,573,'2459','','','apply'),(1491,155,575,'2459','','','apply'),(1492,156,577,'2459','','','apply'),(1493,157,579,'14','Сущность1','Сущность1','apply'),(1494,157,580,'117','Сущность','Сущность1','apply'),(1495,157,582,'7','Сущность','Сущность1','apply'),(1496,157,582,'14','Сущность','Сущность1','apply'),(1497,157,582,'16','Сущность','Сущность1','apply'),(1498,157,582,'25','Сущность','Сущность1','apply'),(1499,159,587,'14','Сущность','Сущность','apply'),(1500,159,588,'117','Сущность1','Сущность','apply'),(1501,159,590,'7','Сущность1','Сущность','apply'),(1502,159,590,'14','Сущность1','Сущность','apply'),(1503,159,590,'16','Сущность1','Сущность','apply'),(1504,159,590,'25','Сущность1','Сущность','apply'),(1505,161,595,'2644','','','apply'),(1506,163,599,'2644','','','apply'),(1507,164,601,'2644','Фильтрация через SQL WHERE12','Фильтрация через SQL WHERE12','apply'),(1508,166,609,'2644','Фильтрация через SQL WHERE','Фильтрация через SQL WHERE','apply'),(1509,168,617,'2459','','','apply'),(1510,169,619,'2459','','','apply'),(1511,170,621,'5','Таблица БД','Таблица БД12','apply'),(1512,171,627,'5','Таблица БД12','Таблица БД','apply'),(1513,172,633,'2697','В контексте какого поля','Поле','apply'),(1514,172,633,'2703','В контексте какого поля','Поле','apply'),(1515,172,634,'143','В контексте какого поля','Поле','apply'),(1516,172,636,'45','В контексте какого поля','Поле','apply');
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
) ENGINE=MyISAM AUTO_INCREMENT=173 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queuetask`
--

LOCK TABLES `queuetask` WRITE;
/*!40000 ALTER TABLE `queuetask` DISABLE KEYS */;
INSERT INTO `queuetask` VALUES (145,'L10n_AdminSystemUi','2021-02-15 03:15:49','{\"source\":\"ru\",\"target\":\"en\"}','2021-02-15 03:15:49',14144,'apply','finished',3,'finished',334,'finished',334,218,'finished',334,'finished',334,'Применение результатов - Завершено'),(146,'L10n_FieldToggleL10n','2021-02-15 03:17:01','{\"field\":\"section:title\",\"source\":\"ru\",\"target\":{\"adminSystemUi\":\"en\",\"adminCustomUi\":\"en\"}}','2021-02-15 03:17:02',7200,'apply','finished',2,'finished',29,'finished',29,324,'finished',29,'finished',29,'Применение результатов - Завершено'),(144,'L10n_AdminSystemUi','2021-02-15 03:14:40','{\"source\":\"en\",\"toggle\":\"n\"}','2021-02-15 03:14:40',20212,'apply','finished',3,'finished',334,'finished',334,0,'noneed',0,'finished',334,'Применение результатов - Завершено'),(147,'UsagesUpdate','2021-03-25 01:39:57','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2414\",\"affected\":{\"title\":\"Элемент управления1\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-03-25 01:39:57',11192,'apply','finished',6,'finished',3,'finished',3,0,'finished',3,'finished',3,'Применение результатов - Завершено'),(148,'UsagesUpdate','2021-03-25 01:40:53','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"2414\",\"affected\":{\"title\":\"Элемент управления\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-03-25 01:40:53',11756,'apply','finished',6,'finished',3,'finished',3,0,'finished',3,'finished',3,'Применение результатов - Завершено'),(149,'UsagesUpdate','2021-04-01 22:22:19','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP1\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:22:20',14412,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(150,'UsagesUpdate','2021-04-01 22:23:08','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:23:08',21320,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(151,'UsagesUpdate','2021-04-01 22:24:50','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP1\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:24:51',10844,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(152,'UsagesUpdate','2021-04-01 22:26:35','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP--\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:26:35',21128,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(153,'UsagesUpdate','2021-04-01 22:37:19','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP1\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:37:19',2900,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(154,'UsagesUpdate','2021-04-01 22:37:22','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:37:22',18192,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(155,'UsagesUpdate','2021-04-01 22:55:34','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP-\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:55:34',8236,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(156,'UsagesUpdate','2021-04-01 22:55:49','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP\"},\"considerIdA\":[\"31\"]}','2021-04-01 22:55:49',15644,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(157,'UsagesUpdate','2021-04-01 23:11:33','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"19\",\"affected\":{\"title\":\"Сущность1\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-01 23:11:33',22228,'apply','finished',6,'finished',6,'finished',6,0,'finished',6,'finished',6,'Применение результатов - Завершено'),(158,'UsagesUpdate','2021-04-01 23:11:33','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"14\",\"affected\":{\"alterTitle\":\"\",\"title\":\"Сущность1\"},\"considerIdA\":[\"27\"]}','2021-04-01 23:11:33',10808,'apply','finished',2,'finished',0,'finished',0,0,'finished',0,'finished',0,'Применение результатов - Завершено'),(159,'UsagesUpdate','2021-04-01 23:13:04','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"19\",\"affected\":{\"title\":\"Сущность\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-01 23:13:04',12856,'apply','finished',6,'finished',6,'finished',6,0,'finished',6,'finished',6,'Применение результатов - Завершено'),(160,'UsagesUpdate','2021-04-01 23:13:04','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"14\",\"affected\":{\"alterTitle\":\"\",\"title\":\"Сущность\"},\"considerIdA\":[\"27\"]}','2021-04-01 23:13:04',19188,'apply','finished',2,'finished',0,'finished',0,0,'finished',0,'finished',0,'Применение результатов - Завершено'),(161,'UsagesUpdate','2021-04-01 23:17:01','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"767\",\"affected\":{\"tooltip\":\"Фильтрация через SQL WHERE1\"},\"considerIdA\":[\"31\"]}','2021-04-01 23:17:01',14692,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(162,'UsagesUpdate','2021-04-01 23:17:01','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2644\",\"affected\":{\"title\":\"Фильтрация через SQL WHERE\"},\"considerIdA\":[\"27\"]}','2021-04-01 23:17:01',22192,'apply','finished',2,'finished',0,'finished',0,0,'finished',0,'finished',0,'Применение результатов - Завершено'),(163,'UsagesUpdate','2021-04-01 23:19:05','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"767\",\"affected\":{\"tooltip\":\"\"},\"considerIdA\":[\"31\"]}','2021-04-01 23:19:06',2652,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(164,'UsagesUpdate','2021-04-01 23:44:01','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"767\",\"affected\":{\"title\":\"Фильтрация через SQL WHERE12\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-01 23:44:01',5008,'apply','finished',6,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(165,'UsagesUpdate','2021-04-01 23:44:01','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2644\",\"affected\":{\"alterTitle\":\"\",\"title\":\"Фильтрация через SQL WHERE12\"},\"considerIdA\":[\"27\"]}','2021-04-01 23:44:02',17996,'apply','finished',2,'finished',0,'finished',0,0,'finished',0,'finished',0,'Применение результатов - Завершено'),(166,'UsagesUpdate','2021-04-01 23:44:43','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"767\",\"affected\":{\"title\":\"Фильтрация через SQL WHERE\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-01 23:44:44',18632,'apply','finished',6,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(167,'UsagesUpdate','2021-04-01 23:44:43','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"grid\",\"entry\":\"2644\",\"affected\":{\"alterTitle\":\"\",\"title\":\"Фильтрация через SQL WHERE\"},\"considerIdA\":[\"27\"]}','2021-04-01 23:44:44',21884,'apply','finished',2,'finished',0,'finished',0,0,'finished',0,'finished',0,'Применение результатов - Завершено'),(168,'UsagesUpdate','2021-04-02 00:02:19','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP1\"},\"considerIdA\":[\"31\"]}','2021-04-02 00:02:19',21328,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(169,'UsagesUpdate','2021-04-02 00:03:07','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"502\",\"affected\":{\"tooltip\":\"Родительский класс PHP\"},\"considerIdA\":[\"31\"]}','2021-04-02 00:03:07',16964,'apply','finished',2,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(170,'UsagesUpdate','2021-04-02 15:46:52','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"5\",\"affected\":{\"title\":\"Таблица БД12\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-02 15:46:53',15508,'apply','finished',6,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(171,'UsagesUpdate','2021-04-02 15:47:12','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"5\",\"affected\":{\"title\":\"Таблица БД\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-02 15:47:12',21888,'apply','finished',6,'finished',1,'finished',1,0,'finished',1,'finished',1,'Применение результатов - Завершено'),(172,'UsagesUpdate','2021-04-03 15:55:03','{\"source\":\"ru\",\"fraction\":\"adminSystemUi\",\"table\":\"field\",\"entry\":\"476\",\"affected\":{\"title\":\"Поле\"},\"considerIdA\":[\"47\",\"35\",\"37\",\"39\",\"41\"]}','2021-04-03 15:55:04',20768,'apply','finished',6,'finished',4,'finished',4,0,'finished',4,'finished',4,'Применение результатов - Завершено');
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
) ENGINE=MyISAM AUTO_INCREMENT=1388 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `realtime`
--

LOCK TABLES `realtime` WRITE;
/*!40000 ALTER TABLE `realtime` DISABLE KEYS */;
INSERT INTO `realtime` VALUES (1284,0,'session',1,1,'ebr6vddn3eocra3ujgprh38o15','2021-04-02 20:53:50','0000-00-00 00:00:00',0,1,0,0,'','','Сессия - ebr6vddn3eocra3ujgprh38o15, Русский','none',''),(1285,1284,'channel',1,1,'MGQGyfdSKFkjUFADNye1sQ==','2021-04-02 20:53:50','0000-00-00 00:00:00',0,1,0,0,'','','Вкладка - MGQGyfdSKFkjUFADNye1sQ==','none',''),(1292,1285,'context',1,1,'i-section-params-action-index-parentrow-2268','2021-04-02 21:00:38','0000-00-00 00:00:00',0,1,101,91,'','2433,2434','Конфигурация » Сущности » Уведомление » Поля в структуре » Заголовок » Параметры','rowset','{\"primary\":{\"parent\":\"`fieldId` = \\u00222268\\u0022\"},\"filters\":\"[]\",\"keyword\":\"\",\"order\":\"[]\",\"page\":1,\"found\":\"0\",\"WHERE\":\"`fieldId` = \\u00222268\\u0022\",\"ORDER\":null,\"hash\":\"0091240785\",\"pgupLast\":null,\"rowsOnPage\":\"25\",\"tree\":false,\"rowReqIfAffected\":\"\"}'),(1387,1285,'context',1,1,'i-section-lang-action-index','2021-04-03 21:55:24','0000-00-00 00:00:00',0,1,387,307,'1,2,17,32,47,89,97','2236,2237,2326,2238,2327,2328,2329,2330,2331,2332,2333,2334,2335,2325','Конфигурация » Языки','rowset','{\"primary\":[],\"filters\":\"[{\\u0022state\\u0022:\\u0022smth\\u0022}]\",\"keyword\":null,\"order\":\"\",\"page\":\"1\",\"found\":\"7\",\"WHERE\":\"`state` = \'smth\'\",\"ORDER\":null,\"hash\":\"d41d8cd98f\",\"pgupLast\":null,\"rowsOnPage\":\"25\",\"tree\":false,\"rowReqIfAffected\":\"\"}');
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
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search`
--

LOCK TABLES `search` WRITE;
/*!40000 ALTER TABLE `search` DISABLE KEYS */;
INSERT INTO `search` VALUES (13,5,612,13,'y','','Тип','','',1,1,'all','',1,0,''),(114,5,1441,114,'n','','Включить в кэш','','',1,1,'all','',1,0,''),(116,7,1366,116,'y','','Тип','','',1,1,'all','',1,0,''),(117,7,19,117,'y','','Сущность','','',1,1,'all','',1,0,''),(119,7,2303,119,'y','','Доступ','','',1,1,'all','',1,0,''),(118,7,22,118,'y','','Статус','','',1,1,'all','',1,0,''),(120,391,6,121,'y','Сущность','Сущность','','',1,1,'all','',1,0,''),(133,391,470,132,'y','','Хранит ключи','','',1,1,'all','',1,0,''),(121,391,2197,134,'y','','Режим','','',1,1,'all','',1,0,''),(122,391,12,133,'y','','Ключи какой сущности','','',1,1,'all','',1,0,''),(134,391,9,122,'y','','Тип столбца MySQL','','',1,1,'all','',1,0,''),(123,391,10,135,'y','Элемент','Элемент управления','','',1,1,'all','',1,0,''),(125,387,2325,125,'y','','Состояние','smth','',1,1,'all','',1,0,''),(126,387,2238,126,'y','','Статус','','',1,1,'all','',1,0,''),(127,394,2378,127,'y','','Статус','','',1,1,'all','',1,0,''),(128,396,2397,128,'y','','Тип','','',1,1,'all','',1,0,''),(129,396,2398,129,'y','','Роль','','',1,1,'all','',1,0,''),(130,396,2404,130,'y','','Язык','','',1,1,'all','',1,0,''),(131,396,2399,131,'y','','Пользователь','','',1,1,'all','',1,0,''),(132,391,6,120,'y','','Сущность','','',1,1,'all','',1,612,''),(135,391,2239,123,'y','','Мультиязычность','','',1,1,'all','',1,0,''),(136,7,2213,136,'y','','Режим подгрузки','','',1,1,'all','',1,0,''),(137,5,2243,137,'y','','Паттерн комплекта календарных полей','','',1,1,'all','',1,0,''),(140,10,2202,140,'y','','Статус','','',1,1,'all','',1,0,''),(139,10,1364,139,'y','','Тип','','',1,1,'all','',1,0,''),(141,10,345,141,'y','','Нужно выбрать запись','','',1,1,'all','',1,0,''),(142,10,2100,142,'y','','Отображать в панели действий','','',1,1,'all','',1,0,''),(143,407,476,143,'y','','Поле','','',1,1,'all','',1,6,'');
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
  `title` text NOT NULL,
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
  KEY `rownumberer` (`rownumberer`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=408 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (1,0,0,'{\"ru\":\"Конфигурация\",\"en\":\"Configuration\"}','configuration','y',367,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(2,1,1,'{\"ru\":\"Столбцы\",\"en\":\"Columns\"}','columnTypes','n',6,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(5,1,2,'{\"ru\":\"Сущности\",\"en\":\"Entities\"}','entities','y',4,50,'Indi_Controller_Admin_Exportable',4,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(6,5,5,'{\"ru\":\"Поля в структуре\",\"en\":\"Fields in structure\"}','fields','y',7,100,'Indi_Controller_Admin_Exportable',14,'ASC','`entry` = \"0\"','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','1',0,0),(7,1,3,'{\"ru\":\"Разделы\",\"en\":\"Sections\"}','sections','y',2,50,'Indi_Controller_Admin_Exportable',23,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(8,7,8,'{\"ru\":\"Действия\",\"en\":\"Actions\"}','sectionActions','y',8,25,'Indi_Controller_Admin_Multinew',30,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(10,1,7,'{\"ru\":\"Действия\",\"en\":\"Actions\"}','actions','n',9,25,'Indi_Controller_Admin_Exportable',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(11,7,9,'{\"ru\":\"Столбцы грида\",\"en\":\"Grid columns\"}','grid','y',10,25,'Indi_Controller_Admin_Multinew',35,'ASC','','0','s',0,2308,'no','all','','1','Indi.lib.controller.Controller','0','1',0,0),(12,6,6,'{\"ru\":\"Возможные значения\",\"en\":\"Possible values\"}','enumset','y',11,25,'Indi_Controller_Admin_Exportable',377,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(13,1,10,'{\"ru\":\"Роли\",\"en\":\"Roles\"}','profiles','y',5,25,'Indi_Controller_Admin',2132,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(14,13,11,'{\"ru\":\"Пользователи\",\"en\":\"Users\"}','admins','y',13,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(16,1,4,'{\"ru\":\"Элементы\",\"en\":\"The elements\"}','controlElements','y',14,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(22,6,20,'{\"ru\":\"Копии изображения\",\"en\":\"Image Copies\"}','resize','y',19,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(30,378,25,'Страницы','staticpages','n',316,25,'Indi_Controller_Admin',131,'ASC','','0','o',0,0,'auto','all','','12,1','Indi.lib.controller.Controller','0','0',0,0),(101,6,91,'{\"ru\":\"Параметры\",\"en\":\"Options\"}','params','y',91,25,'Indi_Controller_Admin_CfgValue',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(112,0,0,'Фронтенд','','n',371,25,'Indi_Controller_Admin',0,'ASC','','0','o',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(113,112,101,'Разделы','fsections','y',104,25,'Indi_Controller_Admin',585,'ASC','<?=$_SESSION[\'admin\'][\'profileId\']==1?\'1\':\'`toggle`=\"y\"\'?>','0','o',0,0,'auto','all','','1,2','Indi.lib.controller.Controller','0','0',0,0),(143,0,0,'Обратная связь','','n',358,25,'Indi_Controller_Admin',0,'ASC','','0','o',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(144,143,128,'Фидбэк','feedback','n',135,25,'Indi_Controller_Admin',681,'DESC','','0','o',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller','0','0',0,0),(403,0,0,'{\"ru\":\"Database\",\"en\":\"Database\"}','db','y',403,25,'Indi_Controller_Admin',0,'ASC','','0','p',0,0,'auto','all','','12,1','Indi.lib.controller.Controller','0','0',0,0),(172,112,146,'Действия','factions','y',185,25,'Indi_Controller_Admin',857,'ASC','','0','o',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller','0','0',0,0),(173,113,147,'Действия','fsection2factions','y',161,25,'Indi_Controller_Admin',860,'ASC','','0','o',0,0,'auto','all','','1,2','Indi.lib.controller.Controller','0','0',0,0),(191,173,162,'Компоненты SEO-урла','seoUrl','y',178,25,'Indi_Controller_Admin',1195,'ASC','','0','o',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(201,7,171,'{\"ru\":\"Измененные поля\",\"en\":\"Modified fields\"}','alteredFields','y',188,25,'Indi_Controller_Admin_Multinew',1342,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(224,7,195,'{\"ru\":\"Фильтры\",\"en\":\"Filters\"}','search','y',192,25,'Indi_Controller_Admin_Multinew',1444,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(232,378,204,'Элементы','staticblocks','n',232,25,'Indi_Controller_Admin',1485,'ASC','','0','o',0,0,'auto','all','','12,1','Indi.lib.controller.Controller','0','0',0,0),(379,173,301,'Компоненты meta-тегов','metatitles','y',379,25,'Indi_Controller_Admin_Meta',2181,'ASC','','0','o',0,2172,'yes','all','','1,12','Indi.lib.controller.Meta','0','0',0,0),(378,0,0,'Статика','','n',144,30,'Indi_Controller_Admin',0,'ASC','','0','o',0,0,'auto','all','','','Indi.lib.controller.Controller','0','0',0,0),(389,1,309,'{\"ru\":\"Уведомления\",\"en\":\"Notifications\"}','notices','y',389,25,'Indi_Controller_Admin',2254,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(390,389,310,'{\"ru\":\"Получатели\",\"en\":\"Recipients\"}','noticeGetters','y',390,25,'Indi_Controller_Admin',2275,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(387,1,307,'{\"ru\":\"Языки\",\"en\":\"Languages\"}','lang','y',387,25,'Indi_Controller_Admin',0,'ASC','','0','s',0,2325,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(388,6,308,'{\"ru\":\"Зависимости\",\"en\":\"Dependencies\"}','consider','y',388,25,'Indi_Controller_Admin_Exportable',0,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(391,1,5,'{\"ru\":\"Все поля\",\"en\":\"All fields\"}','fieldsAll','y',391,25,'Indi_Controller_Admin',0,'ASC','','1','s',0,6,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(392,1,314,'{\"ru\":\"Очереди задач\",\"en\":\"Task queues\"}','queueTask','y',392,25,'Indi_Controller_Admin',2337,'DESC','','1','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(393,392,315,'{\"ru\":\"Сегменты очереди\",\"en\":\"Queue segments\"}','queueChunk','y',393,25,'Indi_Controller_Admin',2379,'ASC','','1','s',0,2381,'no','all','','1','Indi.lib.controller.Controller','1','0',0,0),(394,393,316,'{\"ru\":\"Элементы очереди\",\"en\":\"Queue items\"}','queueItem','y',394,25,'Indi_Controller_Admin',0,'ASC','','1','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0),(396,1,318,'{\"ru\":\"Рилтайм\",\"en\":\"Riltime\"}','realtime','y',396,25,'Indi_Controller_Admin',2401,'ASC','','0','s',0,2399,'auto','all','','1','Indi.lib.controller.Controller','0','1',0,0),(405,16,5,'{\"ru\":\"Возможные настройки\",\"en\":\"Possible settings\"}','elementCfgField','y',405,100,'Indi_Controller_Admin_CfgField',14,'ASC','','0','s',2435,0,'no','all','','1','Indi.lib.controller.Field','0','1',0,0),(406,405,6,'{\"ru\":\"Возможные значения\",\"en\":\"Possible values\"}','elementCfgFieldEnumset','y',406,25,'Indi_Controller_Admin_Exportable',377,'ASC','','0','s',0,0,'no','all','','1','Indi.lib.controller.Controller','0','0',0,0),(407,1,91,'{\"ru\":\"Все параметры\",\"en\":\"All parameters\"}','paramsAll','y',407,25,'Indi_Controller_Admin_CfgValue',0,'ASC','','0','s',0,0,'auto','all','','1','Indi.lib.controller.Controller','0','0',0,0);
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
  `title` text NOT NULL,
  `rename` text NOT NULL,
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
  KEY `l10n` (`l10n`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `rename` (`rename`)
) ENGINE=MyISAM AUTO_INCREMENT=1646 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section2action`
--

LOCK TABLES `section2action` WRITE;
/*!40000 ALTER TABLE `section2action` DISABLE KEYS */;
INSERT INTO `section2action` VALUES (1,2,1,'y',1,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(2,2,2,'y',2,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(3,2,3,'y',3,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(5,2,4,'n',5,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(6,5,1,'y',6,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(7,5,2,'y',7,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(8,5,3,'y',8,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(9,5,4,'y',9,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(10,6,1,'y',10,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(11,6,2,'y',11,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(12,6,3,'y',12,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(13,6,4,'y',13,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(14,7,1,'y',14,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(15,7,2,'y',15,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(16,7,3,'y',16,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(17,7,4,'y',17,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(18,6,5,'y',18,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(19,6,6,'y',19,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(20,8,1,'y',20,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(21,8,2,'y',21,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(22,8,3,'y',22,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(23,8,4,'y',23,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(24,8,5,'y',24,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(25,8,6,'y',25,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(26,7,5,'y',26,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(27,7,6,'y',27,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(28,10,1,'y',28,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(29,10,2,'y',29,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(30,10,3,'y',30,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(32,11,1,'y',31,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(33,11,2,'y',32,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(34,11,3,'y',33,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(35,11,4,'y',34,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(36,11,5,'y',35,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(37,11,6,'y',36,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(38,13,1,'y',37,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(39,13,2,'y',38,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(40,13,3,'y',39,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(41,12,1,'y',40,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(42,12,2,'y',41,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(43,12,3,'y',42,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(44,12,4,'y',43,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(45,14,1,'y',44,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(46,14,2,'y',45,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(47,14,3,'y',46,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(48,14,4,'y',47,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(52,16,1,'y',51,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(53,16,2,'y',52,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(54,16,3,'y',53,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(67,7,7,'y',58,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(68,10,7,'y',59,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(69,11,7,'y',60,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(74,22,1,'y',65,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(75,22,2,'y',66,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(76,22,3,'y',67,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(77,22,4,'y',68,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(99,30,1,'y',69,'12,1','Список','','auto','auto','n'),(100,30,2,'y',70,'12,1','Детали','','auto','auto','n'),(101,30,3,'y',71,'12,1','Сохранить','','auto','auto','n'),(102,30,4,'y',72,'12,1','Удалить','','auto','auto','n'),(103,30,7,'y',73,'12,1','Статус','','auto','auto','n'),(329,12,5,'y',299,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(330,12,6,'y',300,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(377,101,1,'y',347,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(378,101,2,'y',348,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(379,101,3,'y',349,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(380,101,4,'y',350,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(808,13,4,'y',589,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(806,10,4,'y',587,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(429,113,1,'y',399,'1,2','Список','','auto','auto','n'),(430,113,2,'y',400,'1','Детали','','auto','auto','n'),(431,113,3,'y',401,'1','Сохранить','','auto','auto','n'),(432,113,4,'y',402,'1','Удалить','','auto','auto','n'),(1593,387,6,'y',1593,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(532,144,1,'y',133,'1,2,4','Список','','auto','auto','n'),(533,144,2,'y',134,'1,2,4','Детали','','auto','auto','n'),(534,144,3,'y',135,'1,2','Сохранить','','auto','auto','n'),(535,144,4,'y',136,'1,2','Удалить','','auto','auto','n'),(1621,403,4,'y',1621,'12,1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1620,403,3,'y',1620,'12,1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1619,403,2,'y',1619,'12,1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(833,16,4,'n',598,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(875,224,1,'y',608,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(876,224,2,'y',609,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(877,224,4,'y',611,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(878,224,3,'y',610,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(879,224,5,'y',612,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(880,224,6,'y',613,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(881,224,7,'y',614,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(642,172,1,'y',161,'1','Список','','auto','auto','n'),(643,172,2,'y',162,'1,2,4','Детали','','auto','auto','n'),(644,172,3,'y',163,'1,2','Сохранить','','auto','auto','n'),(645,172,4,'y',164,'1,2','Удалить','','auto','auto','n'),(646,173,1,'y',162,'1,2','Список','','auto','auto','n'),(647,173,2,'y',163,'1','Детали','','auto','auto','n'),(648,173,3,'y',164,'1','Сохранить','','auto','auto','n'),(649,173,4,'y',165,'1','Удалить','','auto','auto','n'),(1618,403,1,'y',1618,'12,1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1615,7,45,'y',1615,'1','{\"ru\":\"Копировать\",\"en\":\"Copy\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1614,10,36,'y',1614,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(740,191,1,'y',534,'1','Список','','auto','auto','n'),(741,191,2,'y',535,'1','Детали','','auto','auto','n'),(742,191,3,'y',536,'1','Сохранить','','auto','auto','n'),(743,191,4,'y',537,'1','Удалить','','auto','auto','n'),(744,191,5,'y',538,'1','Выше','','auto','auto','n'),(745,191,6,'y',539,'1','Ниже','','auto','auto','n'),(1548,7,34,'y',1548,'1','{\"ru\":\"PHP\",\"en\":\"PHP\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1547,5,34,'y',1559,'1','{\"ru\":\"PHP\",\"en\":\"PHP\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1592,387,5,'y',1592,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1528,379,6,'y',1528,'1,12','Ниже','','auto','auto','n'),(789,201,1,'y',583,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(790,201,2,'y',584,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(791,201,3,'y',585,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(792,201,4,'y',586,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(910,232,1,'y',643,'12,1','Список','','auto','auto','n'),(911,232,2,'y',644,'1,12','Детали','','auto','auto','n'),(912,232,3,'y',645,'1,12','Сохранить','','auto','auto','n'),(913,232,4,'y',646,'1','Удалить','','auto','auto','n'),(1527,379,5,'y',1527,'1,12','Выше','','auto','auto','n'),(939,232,7,'y',672,'12,1','Статус','','auto','auto','n'),(946,113,7,'y',679,'1','Статус','','auto','auto','n'),(947,113,5,'y',680,'1,2','Выше','','auto','auto','n'),(948,113,6,'y',681,'1,2','Ниже','','auto','auto','n'),(1617,14,20,'y',1617,'1','{\"ru\":\"Авторизация\",\"en\":\"Authorization\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1268,8,7,'y',1268,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1295,13,5,'y',1291,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1296,13,6,'y',1292,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1522,14,7,'y',1293,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1545,13,7,'y',1545,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1549,7,35,'y',1549,'1','{\"ru\":\"JS\",\"en\":\"Js\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1526,379,4,'y',1526,'1,12','Удалить','','auto','auto','n'),(1525,379,3,'y',1525,'1,12','Сохранить','','auto','auto','n'),(1524,379,2,'y',1524,'1,12','Детали','','auto','auto','n'),(1523,379,1,'y',1523,'1,12','Список','','auto','n','n'),(1577,390,3,'y',1577,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1576,390,2,'y',1576,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1575,390,1,'y',1575,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1574,389,7,'y',1574,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1570,389,1,'y',1570,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1571,389,2,'y',1571,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1572,389,3,'y',1572,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1573,389,4,'y',1573,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1559,5,7,'y',607,'1','{\"ru\":\"Статус\",\"en\":\"Status\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1560,387,1,'y',1560,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1561,387,2,'y',1561,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1562,387,3,'y',1562,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1563,387,4,'y',1563,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1564,388,1,'y',1564,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1565,388,2,'y',1565,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1566,388,3,'y',1566,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1567,388,4,'y',1567,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1568,7,36,'y',1568,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1569,5,36,'y',1569,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1578,390,4,'y',1578,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1579,391,1,'y',1579,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1580,391,2,'y',1580,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1581,391,3,'y',1582,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1582,6,39,'y',1581,'1','{\"ru\":\"Активировать\",\"en\":\"Activate\"}','{\"ru\":\"Выбрать режим\",\"en\":\"Select mode\"}','auto','auto','n'),(1583,8,36,'y',1583,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1584,11,36,'y',1584,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1585,201,36,'y',1585,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1586,224,36,'y',1586,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1587,6,36,'y',1587,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1588,12,36,'y',1588,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1589,22,36,'y',1589,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1590,101,36,'y',1590,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1591,388,36,'y',1591,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1594,387,40,'y',1594,'1','{\"ru\":\"Доступные языки\",\"en\":\"Available languages\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1595,392,1,'y',1595,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1596,392,2,'y',1596,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1597,392,4,'y',1597,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1598,392,41,'y',1598,'1','{\"ru\":\"Запустить\",\"en\":\"Run\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1599,393,1,'y',1599,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1600,393,2,'y',1600,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1601,394,1,'y',1601,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1602,394,3,'y',1602,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1603,387,43,'y',1603,'1','{\"ru\":\"Вординги\",\"en\":\"Wordings\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1609,396,2,'y',1609,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1610,396,1,'y',1610,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1611,396,3,'y',1611,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1612,396,4,'y',1612,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1613,396,44,'y',1613,'1','{\"ru\":\"Перезапустить\",\"en\":\"Restart\"}','{\"ru\":\"Перезагрузить websocket-сервер\",\"en\":\"Reload websocket server\"}','auto','auto','n'),(1627,405,1,'y',1627,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1628,405,2,'y',1628,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1629,405,3,'y',1629,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1630,405,4,'y',1630,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1631,405,5,'y',1631,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1632,405,6,'y',1632,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1633,405,39,'y',1633,'1','{\"ru\":\"Активировать\",\"en\":\"Activate\"}','{\"ru\":\"Выбрать режим\",\"en\":\"Select mode\"}','auto','auto','n'),(1634,405,36,'y',1634,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1635,406,1,'y',1635,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1636,406,2,'y',1636,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1637,406,3,'y',1637,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1638,406,4,'y',1638,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1639,406,5,'y',1639,'1','{\"ru\":\"Выше\",\"en\":\"Higher\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1640,406,6,'y',1640,'1','{\"ru\":\"Ниже\",\"en\":\"Below\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1641,406,36,'y',1641,'1','{\"ru\":\"Экспорт\",\"en\":\"Export\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1642,407,1,'y',1642,'1','{\"ru\":\"Список\",\"en\":\"List\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1643,407,2,'y',1643,'1','{\"ru\":\"Детали\",\"en\":\"Details\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1644,407,3,'y',1644,'1','{\"ru\":\"Сохранить\",\"en\":\"Save\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n'),(1645,407,4,'y',1645,'1','{\"ru\":\"Удалить\",\"en\":\"Delete\"}','{\"ru\":\"\",\"en\":\"\"}','auto','auto','n');
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

-- Dump completed on 2021-04-03 21:56:41

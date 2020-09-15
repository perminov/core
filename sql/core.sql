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
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
INSERT INTO `action` VALUES (1,'Список','index','n','s',0,'y'),(2,'Детали','form','y','s',1,'y'),(3,'Сохранить','save','y','s',0,'y'),(4,'Удалить','delete','y','s',1,'y'),(5,'Выше','up','y','s',1,'y'),(6,'Ниже','down','y','s',1,'y'),(7,'Статус','toggle','y','s',1,'y'),(18,'Обновить кэш','cache','y','s',1,'y'),(19,'Обновить sitemap.xml','sitemap','n','s',1,'y'),(20,'Авторизация','login','y','s',1,'y'),(33,'Автор','author','y','s',1,'y'),(34,'PHP','php','y','s',1,'y'),(35,'JS','js','y','s',1,'y'),(36,'Экспорт','export','y','s',1,'y'),(37,'Перейти','goto','y','s',1,'y'),(38,'','rwu','n','s',0,'y'),(39,'Активировать','activate','y','s',1,'y'),(40,'Доступные языки','dict','n','s',1,'y'),(41,'Запустить','run','y','s',1,'y'),(42,'График','chart','y','s',1,'y'),(43,'Вординги','wordings','y','s',1,'y');
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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,1,'Павел Перминов','pavel.perminov.23@gmail.com','*8E1219CD047401C6FEAC700B47F5DA846A57ABD4','y','n','n'),(14,12,'Василий Теркин','vasily.terkin@gmail.com','*85012D571AE8732730DE98314CF04A3BB2269508','n','n','n');
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
) ENGINE=MyISAM AUTO_INCREMENT=217 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alteredfield`
--

LOCK TABLES `alteredfield` WRITE;
/*!40000 ALTER TABLE `alteredfield` DISABLE KEYS */;
INSERT INTO `alteredfield` VALUES (84,146,961,'','hidden','Аккаунт активирован','all','','',0),(85,146,962,'','hidden','Код активации','all','','',0),(43,146,1108,'','hidden','Настройки','all','','',0),(94,146,1577,'','hidden','Код','all','','',0),(93,146,1576,'','hidden','Дата последнего запроса','all','','',0),(92,146,1575,'','hidden','Смена пароля','all','','',0),(91,146,1162,'','hidden','ID пользователя в этой соц.сети','all','','',0),(90,146,1163,'','hidden','Какая','all','','',0),(89,146,1161,'','hidden','Социальные сети','all','','',0),(88,146,698,'','hidden','Подписался на рассылку','all','','',0),(215,232,1515,'','readonly','Тип','except','1','',0),(216,394,2377,'','inherit','Результат','all','','',1);
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
INSERT INTO `columntype` VALUES (1,'Строка','VARCHAR(255)','y','22,23,1,7'),(3,'Число','INT(11)','y','3,21,4,5,23,1,18'),(4,'Текст','TEXT','y','6,7,8,13,1'),(5,'Цена','DECIMAL(11,2)','n','24'),(6,'Дата','DATE','n','12'),(7,'Год','YEAR','n',''),(8,'Время','TIME','n','17'),(9,'Момент','DATETIME','n','19'),(10,'Одно значение из набора','ENUM','n','5,23'),(11,'Набор значений','SET','n','23,1,6,7'),(12,'Правда/Ложь','BOOLEAN','n','9'),(13,'Цвет','VARCHAR(10)','n','11');
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
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consider`
--

LOCK TABLES `consider` WRITE;
/*!40000 ALTER TABLE `consider` DISABLE KEYS */;
INSERT INTO `consider` VALUES (1,308,2307,2246,12,'Поле','y',0),(2,5,9,10,0,'Элемент управления','y',0),(3,5,10,470,0,'Предназначено для хранения ключей','y',0),(4,9,34,33,19,'Раздел','y',0),(23,9,2313,34,12,'Поле','y',6),(24,195,2314,1443,12,'Поле прикрепленной к разделу сущности','y',6),(6,91,477,476,10,'В контексте какого поля','y',0),(7,3,503,19,0,'Сущность','y',0),(8,195,1443,1442,19,'Раздел','y',0),(9,101,1010,563,0,'Прикрепленная сущность','y',0),(10,162,1193,1192,0,'Раздел фронтенда','y',0),(11,301,2178,2177,0,'Сущность','y',0),(12,171,1342,1341,19,'Раздел','y',0),(13,301,2171,2170,0,'Раздел','y',0),(14,3,1554,19,0,'Сущность','y',0),(15,101,1560,868,563,'Вышестоящий раздел','y',0),(16,3,2211,19,0,'Сущность','y',0),(17,309,2262,2255,0,'Сущность','y',0),(18,308,2247,2246,6,'Поле','y',0),(19,308,2248,2247,12,'От какого поля зависит','y',6),(20,313,2292,2291,0,'Сущность','y',0),(21,313,2293,2291,0,'Сущность','y',0),(22,313,2299,2298,0,'Тип автора','y',0),(25,3,2322,19,0,'Сущность','y',0),(26,3,2323,2322,0,'Плитка','y',106),(27,9,1886,2165,0,'Auto title','y',0),(28,195,1658,2167,0,'Auto title','y',0),(29,8,2209,2164,0,'Auto title','y',0),(30,171,2252,2169,0,'Auto title','y',0),(31,9,2200,34,2199,'Поле','y',0),(32,314,2382,2342,0,'Этап','y',0),(33,314,2382,2343,0,'Статус','y',0),(34,8,2164,27,31,'Действие','y',0),(35,9,2165,34,7,'Поле','y',0),(36,91,2166,477,472,'Параметр настройки','y',0),(37,195,2167,1443,7,'Поле прикрепленной к разделу сущности','y',0),(38,147,2168,860,857,'Действие','y',0),(39,171,2169,1342,7,'Поле','y',0),(40,310,2280,2275,36,'Роль','y',0),(41,308,2249,2247,7,'От какого поля зависит','y',0);
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
INSERT INTO `element` VALUES (1,'Строка','string','none,many',0),(4,'Приоритет отображения','move','none',1),(5,'Радио-кнопки','radio','one',0),(6,'Текст','textarea','none,many',0),(7,'Чекбоксы','multicheck','many',0),(9,'Чекбокс','check','none',0),(11,'Цвет','color','none',0),(12,'Календарь','calendar','none',0),(13,'HTML-редактор','html','none',0),(14,'Файл','upload','none',0),(16,'Группа полей','span','none',0),(17,'Время','time','none',0),(18,'Число','number','none,one',0),(19,'Момент','datetime','none',0),(22,'Скрытое поле','hidden','none',0),(23,'Список','combo','one,many',0),(24,'Цена','price','none',0);
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
) ENGINE=MyISAM AUTO_INCREMENT=317 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity`
--

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` VALUES (1,'Тип столбца','columnType','Indi_Db_Table','y',0,2,'none','',0),(2,'Сущность','entity','Indi_Db_Table','y',1,4,'none','',0),(3,'Раздел','section','Indi_Db_Table','y',0,20,'none','',0),(4,'Элемент управления','element','Indi_Db_Table','y',0,64,'none','',0),(5,'Поле','field','Indi_Db_Table','y',1,7,'none','',0),(6,'Значение из набора','enumset','Indi_Db_Table','y',0,16,'none','',0),(7,'Действие','action','Indi_Db_Table','y',0,31,'none','',0),(8,'Действие в разделе','section2action','Indi_Db_Table','y',0,27,'none','',0),(9,'Столбец грида','grid','Indi_Db_Table','y',0,34,'none','',0),(10,'Роль','profile','Indi_Db_Table','y',0,36,'none','',0),(11,'Пользователь CMS','admin','Indi_Db_Table','y',0,39,'none','',0),(20,'Копия','resize','Indi_Db_Table','y',0,107,'none','',0),(25,'Статическая страница','staticpage','Indi_Db_Table','o',0,131,'none','',0),(90,'Параметр настройки элемента управления','possibleElementParam','Indi_Db_Table','y',0,472,'none','',0),(91,'Параметр настройки элемента управления, в контексте поля сущности','param','Indi_Db_Table','y',0,477,'none','',0),(101,'Раздел фронтенда','fsection','Indi_Db_Table','o',1,559,'none','',0),(195,'Фильтр','search','Indi_Db_Table','y',0,1443,'none','',0),(128,'Фидбэк','feedback','Indi_Db_Table','o',0,678,'none','',0),(129,'Подписчик','subscriber','Indi_Db_Table','o',0,682,'none','',0),(130,'Пользователь','user','Indi_Db_Table','o',0,685,'none','',0),(146,'Действие, возможное для использования в разделе фронтенда','faction','Indi_Db_Table','o',1,857,'none','',0),(147,'Действие в разделе фронтенда','fsection2faction','Indi_Db_Table','o',1,860,'none','',0),(307,'Язык','lang','Indi_Db_Table','y',0,2236,'none','',0),(160,'Посетитель','visitor','Indi_Db_Table','o',0,1100,'none','',0),(162,'Компонент SEO-урла','url','Indi_Db_Table','o',0,0,'none','',0),(171,'Поле, измененное в рамках раздела','alteredField','Indi_Db_Table','y',0,1342,'none','',0),(204,'Статический элемент','staticblock','Indi_Db_Table','o',0,1485,'none','',0),(205,'Пункт меню','menu','Indi_Db_Table','o',0,1490,'none','',0),(301,'Компонент содержимого meta-тега','metatag','Indi_Db_Table','o',0,0,'none','',0),(309,'Уведомление','notice','Indi_Db_Table','y',0,2254,'none','',0),(310,'Получатель уведомлений','noticeGetter','Indi_Db_Table','y',0,2275,'none','',0),(308,'Зависимость','consider','Indi_Db_Table','y',0,2247,'none','',0),(311,'Год','year','Indi_Db_Table','y',0,2286,'none','',0),(312,'Месяц','month','Indi_Db_Table','y',0,2289,'none','',0),(313,'Корректировка','changeLog','Indi_Db_Table','y',0,2296,'none','',0),(314,'Очередь задач','queueTask','Indi_Db_Table','y',0,2336,'none','',0),(315,'Сегмент очереди','queueChunk','Indi_Db_Table','y',0,2359,'none','',0),(316,'Элемент очереди','queueItem','Indi_Db_Table','y',0,0,'none','',0);
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
) ENGINE=MyISAM AUTO_INCREMENT=1162 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enumset`
--

LOCK TABLES `enumset` WRITE;
/*!40000 ALTER TABLE `enumset` DISABLE KEYS */;
INSERT INTO `enumset` VALUES (1,3,'Нет','n',1),(2,3,'Да','y',2),(5,22,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',5),(6,22,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',6),(9,29,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',9),(10,29,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',10),(11,37,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',11),(12,37,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',12),(13,42,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',13),(14,42,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',14),(62,66,'Нет','none',62),(63,66,'Только с одним значением ключа','one',63),(64,66,'С набором значений ключей','many',64),(87,111,'Поменять, но с сохранением пропорций','p',87),(88,111,'Поменять','c',88),(89,111,'Не менять','o',89),(91,114,'Ширины','width',91),(92,114,'Высоты','height',92),(95,137,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включена','y',95),(96,137,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключена','n',96),(112,345,'Да','y',112),(113,345,'Нет','n',113),(122,0,'Всем друзьям, кроме указанных в разделе \"Исключения из правил доступа на просмотр блога\"','ae',122),(181,470,'<span class=\"i-color-box\" style=\"background: white;\"></span>Нет','none',164),(183,470,'<span class=\"i-color-box\" style=\"background: url(/i/admin/btn-icon-multikey.png);\"></span>Да, для энного количества значений ключей','many',296),(184,470,'<span class=\"i-color-box\" style=\"background: url(/i/admin/btn-icon-login.png);\"></span>Да, но для только одного значения ключа','one',295),(1076,2159,'<span class=\"i-color-box\" style=\"background: lightgray; border: 1px solid blue;\"></span>Скрыт, но показан в развороте','e',1076),(213,557,'По возрастанию','ASC',297),(214,557,'По убыванию','DESC',182),(219,594,'Да','y',299),(220,594,'Нет','n',300),(227,612,'Проектная','n',301),(228,612,'<span style=\'color: red\'>Системная</span>','y',186),(241,689,'Мужской','m',427),(242,689,'Женский','f',309),(572,1365,'<font color=lime>Типовое</font>','o',461),(571,1365,'<font color=red>Системное</font>','s',460),(570,1365,'Проектное','p',0),(580,1445,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(581,1445,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',464),(979,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',979),(980,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',980),(981,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',981),(982,2197,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',982),(328,0,'Очень плохо','1',254),(480,1040,'Одностроковый','s',408),(478,1027,'Для jQuery.post()','j',407),(479,1040,'Обычный','r',0),(477,1027,'Обычное','r',0),(574,1366,'<font color=red>Системный</font>','s',462),(573,1366,'Проектный','p',0),(575,1366,'<font color=lime>Типовой</font>','o',463),(458,1009,'SQL-выражению','e',396),(457,1009,'Одному из имеющихся столбцов','c',0),(459,1011,'По возрастанию','ASC',0),(460,1011,'По убыванию','DESC',0),(484,1074,'Над записью','r',0),(485,1074,'Над набором записей','rs',411),(486,1074,'Только независимые множества, если нужно','n',412),(509,689,'Не&nbsp;указан','n',192),(567,1364,'Проектное','p',0),(568,1364,'<font color=red>Системное</font>','s',458),(569,1364,'<font color=lime>Типовое</font>','o',459),(516,1163,'Никакая','n',0),(517,1163,'Facebook','fb',432),(518,1163,'Вконтакте','vk',433),(519,1163,'Twitter','tw',434),(969,2176,'Запись','row',969),(968,2176,'Действие','action',968),(967,2176,'Раздел','section',967),(566,612,'<font color=lime>Типовая</font>','o',457),(582,1488,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(583,1488,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',465),(584,1491,'Нет','n',0),(585,1491,'Да','y',466),(586,1494,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(587,1494,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',467),(594,1515,'HTML','html',0),(595,1515,'Строка','string',471),(596,1515,'Текст','textarea',472),(597,1495,'Да','y',0),(598,1495,'Нет','n',473),(608,1533,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',478),(607,1533,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(962,2172,'Title','title',962),(963,2172,'Keywords','keywords',963),(964,2172,'Description','description',964),(965,2173,'Статический','static',965),(966,2173,'Динамический','dynamic',966),(960,2159,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',0),(961,2159,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',489),(983,2202,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',983),(984,2202,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',984),(985,2203,'Всем пользователям','all',985),(986,2203,'Только выбранным','only',986),(987,2203,'Всем, кроме выбранных','except',987),(988,2205,'Всем пользователям','all',988),(989,2205,'Только выбранным','only',989),(990,2205,'Всем, кроме выбранных','except',990),(991,2207,'Все пользователи','all',991),(992,2207,'Только выбранные','only',992),(993,2207,'Все, кроме выбранных','except',993),(994,2210,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','0',994),(995,2210,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','1',995),(996,2212,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Авто','auto',996),(997,2212,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Отображать','yes',997),(998,2212,'<span class=\"i-color-box\" style=\"background: red;\"></span>Не отображать','no',998),(999,2213,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Авто','auto',999),(1000,2213,'<span class=\"i-color-box\" style=\"background: yellow;\"></span>Отдельным запросом','yes',1000),(1001,2213,'<span class=\"i-color-box\" style=\"background: lime;\"></span>В том же запросе','no',1001),(1002,2214,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','auto',1002),(1003,2214,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1003),(1036,2267,'Увеличение','inc',1036),(1037,2267,'Уменьшение','dec',1037),(1032,2258,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включено','y',1032),(1033,2258,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключено','n',1033),(1034,2261,'Одинаковое для всех получателей','event',1034),(1035,2261,'Неодинаковое, зависит от получателя','getter',1035),(1010,2238,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1010),(1011,2238,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1011),(1012,2239,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключена','n',1012),(1013,2239,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включена','y',1088),(1014,2241,'Существующей','existing',1014),(1015,2241,'Новой','new',1015),(1016,2241,'Любой','any',1016),(1017,2243,'Нет','none',1017),(1018,2243,'DATE','date',1018),(1019,2243,'DATETIME','datetime',1019),(1020,2243,'DATE, TIME','date-time',1020),(1021,2243,'DATE, timeId','date-timeId',1021),(1022,2243,'DATE, dayQty','date-dayQty',1022),(1023,2243,'DATETIME, minuteQty','datetime-minuteQty',1023),(1024,2243,'DATE, TIME, minuteQty','date-time-minuteQty',1024),(1025,2243,'DATE, timeId, minuteQty','date-timeId-minuteQty',1025),(1026,2243,'DATE, hh:mm-hh:mm','date-timespan',1026),(1027,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/hidden.png);\"></span>Скрытое','hidden',1031),(1028,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/readonly.png);\"></span>Только чтение','readonly',1030),(1029,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/inherit.png);\"></span>Без изменений','inherit',1027),(1030,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/regular.png);\"></span>Обычное','regular',1028),(1031,2161,'<span class=\"i-color-box\" style=\"background: url(/i/admin/field/required.png);\"></span>Обязательное','required',1029),(1038,2267,'Изменение','evt',1038),(1039,2276,'Общий','event',1039),(1040,2276,'Раздельный','getter',1040),(1041,2281,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1041),(1042,2281,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1042),(1043,2282,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1043),(1044,2282,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1044),(1045,2283,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1045),(1046,2283,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1046),(1047,2285,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1047),(1048,2285,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1048),(1049,2288,'Январь','01',1049),(1050,2288,'Февраль','02',1050),(1051,2288,'Март','03',1051),(1052,2288,'Апрель','04',1052),(1053,2288,'Май','05',1053),(1054,2288,'Июнь','06',1054),(1055,2288,'Июль','07',1055),(1056,2288,'Август','08',1056),(1057,2288,'Сентябрь','09',1057),(1058,2288,'Октябрь','10',1058),(1059,2288,'Ноябрь','11',1059),(1060,2288,'Декабрь','12',1060),(1061,2301,'Всем пользователям','all',1061),(1062,2301,'Только выбранным','only',1062),(1063,2301,'Всем кроме выбранных','except',1063),(1064,2301,'Никому','none',1064),(1065,2159,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Скрыт','h',1065),(1066,2304,'Пусто','none',1066),(1067,2304,'Сумма','sum',1067),(1068,2304,'Среднее','average',1068),(1069,2304,'Минимум','min',1069),(1070,2304,'Максимум','max',1070),(1071,2304,'Текст','text',1071),(1072,2306,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1072),(1073,2306,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Да','y',1073),(1074,2308,'Обычные','normal',1074),(1075,2308,'Зафиксированные','locked',1075),(1077,2316,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Включен','y',1077),(1078,2316,'<span class=\"i-color-box\" style=\"background: red;\"></span>Выключен','n',1078),(1079,22,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Скрыт','h',1079),(1080,2318,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1080),(1081,2318,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1081),(1082,2319,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Нет','n',1082),(1083,2319,'<span class=\"i-color-box\" style=\"background: lime;\"></span>Да','y',1083),(1084,2321,'Проектное','p',1084),(1085,2321,'<font color=red>Системное</font>','s',1085),(1086,2324,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключено','n',1086),(1087,2324,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включено','y',1087),(1088,2239,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1013),(1089,2239,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1089),(1090,2325,'Ничего','noth',1090),(1091,2325,'Чтото','smth',1091),(1092,2328,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1092),(1093,2328,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1093),(1094,2328,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1094),(1095,2328,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1095),(1096,2329,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1096),(1097,2329,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1097),(1098,2329,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1098),(1099,2329,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1099),(1100,2331,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1100),(1101,2331,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1101),(1102,2331,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1102),(1103,2331,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1103),(1104,2332,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1104),(1105,2332,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1105),(1106,2332,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1106),(1107,2332,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1107),(1108,2333,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1108),(1109,2333,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1109),(1110,2333,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1110),(1111,2333,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1111),(1112,2334,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключен','n',1112),(1113,2334,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1113),(1114,2334,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включен','y',1114),(1115,2334,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1115),(1116,2342,'Оценка масштабов','count',1116),(1117,2342,'Создание очереди','items',1117),(1118,2342,'Процессинг очереди','queue',1118),(1119,2342,'Применение результатов','apply',1119),(1120,2343,'Ожидание','waiting',1120),(1121,2343,'В работе','progress',1121),(1122,2343,'Завершено','finished',1122),(1123,2346,'Ожидание','waiting',1123),(1124,2346,'В работе','progress',1124),(1125,2346,'Завершено','finished',1125),(1126,2349,'Ожидание','waiting',1126),(1127,2349,'В работе','progress',1127),(1128,2349,'Завершено','finished',1128),(1129,2353,'Ожидание','waiting',1129),(1130,2353,'В работе','progress',1130),(1131,2353,'Завершено','finished',1131),(1132,2353,'Не требуется','noneed',1132),(1133,2356,'Ожидание','waiting',1133),(1134,2356,'В работе','progress',1134),(1135,2356,'Завершено','finished',1135),(1136,2362,'Ожидание','waiting',1136),(1137,2362,'В работе','progress',1137),(1138,2362,'Завершено','finished',1138),(1139,2365,'Ожидание','waiting',1139),(1140,2365,'В работе','progress',1140),(1141,2365,'Завершено','finished',1141),(1142,2368,'Ожидание','waiting',1142),(1143,2368,'В работе','progress',1143),(1144,2368,'Завершено','finished',1144),(1145,2368,'Не требуется','noneed',1145),(1146,2371,'Ожидание','waiting',1146),(1147,2371,'В работе','progress',1147),(1148,2371,'Завершено','finished',1148),(1149,2378,'Добавлен','items',1149),(1150,2378,'Обработан','queue',1150),(1151,2378,'Применен','apply',1151),(1152,2381,'Не указана','none',1152),(1153,2381,'AdminSystemUi','adminSystemUi',1153),(1154,2381,'AdminCustomUi','adminCustomUi',1154),(1155,2381,'AdminCustomData','adminCustomData',1155),(1156,2384,'Проектная','p',1156),(1158,2386,'<span class=\"i-color-box\" style=\"background: lightgray;\"></span>Выключена','n',1158),(1157,2384,'<font color=red>Системная</font>','s',1157),(1159,2386,'<span class=\"i-color-box\" style=\"background: lightgray; border: 3px solid blue;\"></span>В очереди на включение','qy',1159),(1160,2386,'<span class=\"i-color-box\" style=\"background: blue;\"></span>Включена','y',1160),(1161,2386,'<span class=\"i-color-box\" style=\"background: blue; border: 3px solid lightgray;\"></span>В очереди на выключение','qn',1161);
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
) ENGINE=MyISAM AUTO_INCREMENT=2387 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field`
--

LOCK TABLES `field` WRITE;
/*!40000 ALTER TABLE `field` DISABLE KEYS */;
INSERT INTO `field` VALUES (1,1,'Наименование','title',1,1,'',1,0,'none','','required','','n'),(2,1,'Тип столбца MySQL','type',1,1,'',2,0,'none','','required','','n'),(3,1,'Пригоден для хранения внешних ключей','canStoreRelation',10,5,'n',3,6,'one','','regular','','n'),(4,2,'Наименование','title',1,1,'',4,0,'none','','required','','n'),(5,2,'Таблица БД','table',1,1,'',5,0,'none','','required','','n'),(6,5,'Сущность, в структуру которой входит это поле','entityId',3,23,'0',6,2,'one','','regular','','n'),(7,5,'Наименование поля','title',1,1,'',7,0,'none','','required','','n'),(8,5,'Наименование соответствующего полю столбца в  таблице БД','alias',1,1,'',8,0,'none','','required','','n'),(9,5,'Тип столбца MySQL','columnTypeId',3,23,'0',12,1,'one','','regular','','n'),(10,5,'Элемент управления','elementId',3,23,'0',11,4,'one','','required','','n'),(11,5,'Значение по умолчанию','defaultValue',1,1,'',13,0,'none','','regular','','n'),(12,5,'Ключи какой сущности будут храниться в этом поле','relation',3,23,'0',428,2,'one','','regular','','n'),(14,5,'Положение в списке','move',3,4,'0',414,0,'none','','regular','','n'),(15,6,'Поле','fieldId',3,23,'0',15,5,'one','','required','','n'),(16,6,'Наименование','title',4,1,'',16,0,'none','','required','','n'),(17,6,'Псевдоним','alias',1,1,'',17,0,'none','','required','','n'),(18,3,'Подчинен разделу','sectionId',3,23,'0',19,3,'one','','regular','','n'),(19,3,'Сущность','entityId',3,23,'0',461,2,'one','','regular','','n'),(2304,9,'Внизу','summaryType',10,23,'none',2304,6,'one','','regular','','n'),(2198,195,'Содержательность','consistence',12,9,'1',2204,0,'none','','regular','','n'),(20,3,'Наименование','title',1,1,'',18,0,'none','','required','','n'),(21,3,'Контроллер','alias',1,1,'',23,0,'none','','required','','n'),(22,3,'Статус','toggle',10,5,'y',22,6,'one','','regular','','n'),(23,3,'Положение в списке','move',3,4,'',514,0,'none','','regular','','n'),(25,3,'Записей на странице','rowsOnPage',3,1,'25',2303,0,'none','','regular','','n'),(26,8,'Раздел, за которым закреплено действие','sectionId',3,23,'',1,3,'one','','required','','n'),(27,8,'Действие','actionId',3,23,'',1,7,'one','','required','','n'),(28,8,'Профили пользователей, имеющих доступ к этому действию в этом разделе','profileIds',1,7,'14',1,10,'many','','regular','','n'),(29,8,'Статус','toggle',10,5,'y',1,6,'one','','regular','','n'),(30,8,'Положение в списке','move',3,4,'',1,0,'none','','regular','','n'),(31,7,'Наименование','title',1,1,'',26,0,'none','','required','','n'),(32,7,'Псевдоним','alias',1,1,'',27,0,'none','','required','','n'),(33,9,'Раздел','sectionId',3,23,'',28,3,'one','','required','','n'),(34,9,'Поле','fieldId',3,23,'',29,5,'one','','regular','','n'),(35,9,'Очередность отображения столбца в гриде','move',3,4,'',2165,0,'none','','regular','','n'),(36,10,'Наименование','title',1,1,'',31,0,'none','','required','','n'),(37,10,'Статус','toggle',10,5,'y',32,6,'one','','regular','','n'),(38,11,'Профиль','profileId',3,23,'',33,10,'one','','required','','n'),(39,11,'Фамилия Имя','title',1,1,'',34,0,'none','','required','','n'),(40,11,'Email (используется в качестве логина)','email',1,1,'',35,0,'none','','required','','n'),(41,11,'Пароль','password',1,1,'',36,0,'none','','required','','n'),(42,11,'Статус','toggle',10,5,'y',37,6,'one','','regular','','n'),(64,4,'Наименование','title',1,1,'',53,0,'none','','required','','n'),(65,4,'Псевдоним','alias',1,1,'',54,0,'none','','required','','n'),(66,4,'Способен работать с внешними ключами','storeRelationAbility',11,23,'none',55,6,'many','','regular','','n'),(1445,195,'Статус','toggle',10,5,'y',1520,6,'one','','regular','','n'),(92,4,'Скрывать при генерации формы','hidden',12,9,'0',72,0,'none','','regular','','n'),(106,20,'Поле','fieldId',3,23,'0',86,5,'one','','required','','n'),(107,20,'Наименование','title',1,1,'',87,0,'none','','required','','n'),(108,20,'Псевдоним','alias',1,1,'',88,0,'none','','required','','n'),(109,20,'Ширина','masterDimensionValue',3,18,'0',91,0,'none','','regular','','n'),(110,20,'Высота','slaveDimensionValue',3,18,'0',93,0,'none','','regular','','n'),(111,20,'Размер','proportions',10,5,'o',89,6,'one','','regular','','n'),(112,20,'Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>','slaveDimensionLimitation',12,9,'1',92,0,'none','','regular','','n'),(114,20,'При расчете пропорций отталкиваться от','masterDimensionAlias',10,5,'width',90,6,'one','','regular','','n'),(131,25,'Наименование','title',1,1,'',101,0,'none','','required','','n'),(133,25,'Псевдоним','alias',1,1,'',102,0,'none','','required','','n'),(137,25,'Статус','toggle',10,5,'y',2208,6,'one','','regular','','n'),(345,7,'Для выполнения действия необходимо выбрать стоку','rowRequired',10,5,'y',308,6,'one','','regular','','n'),(377,6,'Порядок отображения','move',3,4,'0',338,0,'none','','regular','','n'),(470,5,'Предназначено для хранения ключей','storeRelationAbility',10,23,'none',10,6,'one','','regular','','n'),(471,90,'Элемент управления','elementId',3,23,'0',429,4,'one','','required','','n'),(472,90,'Наименование','title',1,1,'',430,0,'none','','required','','n'),(473,90,'Псевдоним','alias',1,1,'',431,0,'none','','required','','n'),(474,90,'Значение по умолчанию','defaultValue',1,1,'',432,0,'none','','regular','','n'),(475,1,'Пригоден для работы с элементами управления','elementId',1,23,'',433,4,'many','','required','','n'),(476,91,'В контексте какого поля','fieldId',3,23,'0',434,5,'one','','required','','n'),(477,91,'Параметр настройки','possibleParamId',3,23,'0',435,90,'one','','required','','n'),(478,91,'Значение параметра','value',4,6,'',436,0,'none','','regular','','n'),(502,3,'Родительский класс PHP','extendsPhp',1,1,'Indi_Controller_Admin',443,0,'none','','regular','','n'),(2309,3,'Родительский класс JS','extendsJs',1,1,'Indi.lib.controller.Controller',310,0,'none','','regular','','n'),(503,3,'Сортировка','defaultSortField',3,23,'0',2211,5,'one','','regular','','n'),(2212,8,'South-панель','south',10,23,'auto',2212,6,'one','','regular','','n'),(555,2,'От какого класса наследовать','extends',1,1,'Indi_Db_Table',512,0,'none','','required','','n'),(557,3,'Направление сортировки','defaultSortDirection',10,5,'ASC',2302,6,'one','','regular','','n'),(559,101,'Наименование','title',1,1,'',517,0,'none','','required','','n'),(560,101,'Псевдоним','alias',1,1,'',519,0,'none','','required','','n'),(563,101,'Прикрепленная сущность','entityId',3,23,'0',520,2,'one','','regular','','n'),(581,101,'Соответствующий раздел бэкенда','sectionId',3,23,'0',534,3,'one','','regular','','n'),(585,101,'Порядок отображения соответствующего пункта в меню','move',3,4,'0',950,0,'none','','regular','','n'),(594,20,'Изменить оттенок','changeColor',10,5,'n',545,6,'one','','regular','','n'),(595,20,'Оттенок','color',13,11,'',546,0,'none','','regular','','n'),(612,2,'Тип','system',10,23,'n',559,6,'one','','regular','','n'),(678,128,'Имя','title',1,1,'',625,0,'none','','required','','n'),(679,128,'Email','email',1,1,'',626,0,'none','','required','','n'),(680,128,'Сообщение','message',4,6,'',627,0,'none','','required','','n'),(681,128,'Дата','date',9,19,'<?=date(\'Y-m-d H:i:s\')?>',628,0,'none','','regular','','n'),(682,129,'Email','title',1,1,'',629,0,'none','','regular','','n'),(683,129,'Дата','date',6,12,'0000-00-00',630,0,'none','','regular','','n'),(684,130,'Email','email',1,1,'',633,0,'none','','regular','','n'),(685,130,'ФИО','title',1,1,'',632,0,'none','','regular','','n'),(686,130,'Пароль','password',1,1,'',634,0,'none','','regular','','n'),(689,130,'Пол','gender',10,5,'n',969,6,'one','','regular','','n'),(690,130,'Дата рождения','birth',6,12,'0000-00-00',1100,0,'none','','regular','','n'),(691,130,'Дата регистрации','registration',6,12,'<?=date(\'Y-m-d\')?>',636,0,'none','','regular','','n'),(1364,7,'Тип','type',10,5,'p',2099,6,'one','','regular','','n'),(1365,146,'Тип','type',10,5,'p',1293,6,'one','','regular','','n'),(698,130,'Подписался на рассылку','subscribed',12,9,'0',1443,0,'none','','regular','','n'),(699,130,'Последний визит','lastVisit',9,19,'',640,0,'none','','regular','','n'),(1444,195,'Порядок отображения','move',3,4,'0',1316,0,'none','','regular','','n'),(1442,195,'Раздел','sectionId',3,23,'0',1313,3,'one','','required','','n'),(1443,195,'Поле прикрепленной к разделу сущности','fieldId',3,23,'0',1315,5,'one','`elementId` NOT IN (4,14,16,20,22)','required','','n'),(1441,2,'Включить в кэш','useCache',12,23,'0',1312,0,'none','','regular','','n'),(754,5,'Статическая фильтрация','filter',1,1,'',523,0,'none','','regular','','n'),(767,3,'Статическая фильтрация','filter',1,1,'',1277,0,'none','','regular','','n'),(857,146,'Наименование','title',1,1,'',803,0,'none','','required','','n'),(858,146,'Псевдоним','alias',1,1,'',804,0,'none','','required','','n'),(859,147,'Раздел фронтенда','fsectionId',3,23,'0',805,101,'one','','required','','n'),(860,147,'Действие','factionId',3,23,'0',806,146,'one','','required','','n'),(868,101,'Вышестоящий раздел','fsectionId',3,5,'0',516,101,'one','','regular','','n'),(869,101,'Статическая фильтрация','filter',1,1,'',814,0,'none','','regular','','n'),(960,101,'Количество строк для отображения по умолчанию','defaultLimit',3,18,'20',951,0,'none','','regular','','n'),(961,130,'Аккаунт активирован','activated',12,9,'0',637,0,'none','','regular','','n'),(962,130,'Код активации','activationCode',1,1,'',639,0,'none','','regular','','n'),(1366,3,'Тип','type',10,5,'p',25,6,'one','','regular','','n'),(1009,101,'По умолчанию сортировка по','orderBy',10,5,'c',952,6,'one','','regular','','n'),(1010,101,'Столбец сортировки','orderColumn',3,23,'0',953,5,'one','','regular','','n'),(1011,101,'Направление сортировки','orderDirection',10,5,'ASC',981,6,'one','','regular','','n'),(1012,101,'SQL-выражение','orderExpression',1,1,'',982,0,'none','','regular','','n'),(1027,147,'Тип','type',10,5,'r',968,6,'one','','regular','','n'),(1028,130,'Аватар','avatar',0,14,'',1099,0,'none','','regular','','n'),(1040,101,'Тип','type',10,5,'r',538,6,'one','','regular','','n'),(1041,101,'Где брать идентификатор','where',1,1,'',983,0,'none','','regular','','n'),(1042,101,'Действие по умолчанию','index',1,1,'',1403,0,'none','','regular','','n'),(1074,146,'Выполнять maintenance()','maintenance',10,5,'r',1015,6,'one','','regular','','n'),(1100,160,'Id сессии','title',1,1,'',1040,0,'none','','regular','','n'),(1101,160,'Дата последней активности','lastActivity',9,19,'0000-00-00 00:00:00',1041,0,'none','','regular','','n'),(1102,160,'Пользователь','userId',3,23,'0',1042,130,'one','','regular','','n'),(1108,130,'Настройки','settings',0,16,'',1101,0,'none','','regular','','n'),(1161,130,'Социальные сети','socialNetworks',0,16,'',1444,0,'none','','regular','','n'),(1162,130,'ID пользователя в этой соц.сети','identifier',1,1,'',1707,0,'none','','regular','','n'),(1163,130,'Какая','sn',10,5,'n',1445,6,'one','','regular','','n'),(1191,147,'Не указывать действие при создании seo-урлов из системных','blink',12,9,'0',1259,0,'none','','regular','','n'),(1192,162,'Раздел фронтенда','fsectionId',3,23,'0',1127,101,'one','','required','','n'),(1193,162,'Действие в разделе фронтенда','fsection2factionId',3,23,'0',1128,147,'one','','required','','n'),(1194,162,'Компонент','entityId',3,23,'0',1129,2,'one','','required','','n'),(1195,162,'Очередность','move',3,4,'0',1130,0,'none','','regular','','n'),(1196,162,'Префикс','prefix',1,1,'',1131,0,'none','','regular','','n'),(2196,9,'Вышестоящий столбец','gridId',3,23,'0',1738,9,'one','`sectionId` = \"<?=$this->sectionId?>\"','regular','','n'),(2310,3,'Включить нумерацию строк','rownumberer',12,9,'0',2322,0,'none','','regular','','n'),(2184,195,'Игнорировать optionTemplate','ignoreTemplate',12,9,'1',2198,0,'none','','regular','','n'),(2183,195,'Статическая фильтрация','filter',1,1,'',2182,0,'none','','regular','','n'),(2176,301,'Источник','source',10,23,'section',2176,6,'one','','regular','','n'),(2177,301,'Сущность','entityId',3,23,'0',2177,2,'one','`id` IN (<?=$this->foreign(\'fsectionId\')->entityRoute(true)?>)','regular','','n'),(2178,301,'Поле','fieldId',3,23,'0',2178,5,'one','','regular','','n'),(2179,301,'Префикс','prefix',1,1,'',2179,0,'none','','regular','','n'),(2180,301,'Постфикс','postfix',1,1,'',2180,0,'none','','regular','','n'),(2181,301,'Порядок отображения','move',3,4,'0',2181,0,'none','','regular','','n'),(2182,195,'Значение по умолчанию','defaultValue',1,1,'',2184,0,'none','','regular','','n'),(1325,147,'Переименовать действие при генерации seo-урла','rename',12,9,'0',1260,0,'none','','regular','','n'),(1326,147,'Псевдоним','alias',1,1,'',1261,0,'none','','regular','','n'),(1327,147,'Настройки SEO','seoSettings',0,16,'',1126,0,'none','','regular','','n'),(1337,10,'Cущность, экземпляры которой тоже будут иметь доступ к CMS с данным профилем','entityId',3,23,'0',1271,2,'one','`system`!=\'y\'','regular','','n'),(1341,171,'Раздел','sectionId',3,23,'0',1275,3,'one','','required','','n'),(1342,171,'Поле','fieldId',3,23,'0',1276,5,'one','','required','','n'),(2251,171,'Изменить свойства поля','alter',0,16,'',2207,0,'none','','regular','','n'),(1345,3,'Отключить кнопку Add','disableAdd',12,9,'0',2309,0,'none','','regular','','n'),(1509,204,'Ширина','detailsHtmlWidth',3,18,'0',1383,0,'none','','regular','','n'),(1532,171,'Значение по умолчанию','defaultValue',1,1,'',2385,0,'none','','regular','','n'),(1485,204,'Наименование','title',1,1,'',1356,0,'none','','required','','n'),(1486,204,'Псевдоним','alias',1,1,'',1357,0,'none','','required','','n'),(1487,204,'Значение','detailsHtml',4,13,'',1382,0,'none','','regular','','n'),(1488,204,'Статус','toggle',10,5,'y',1358,6,'one','','regular','','n'),(1489,205,'Вышестояший пункт','menuId',3,23,'0',1359,205,'one','','regular','','n'),(1490,205,'Наименование','title',1,1,'',1360,0,'none','','required','','n'),(1491,205,'Связан со статической страницей','linked',10,5,'n',1361,6,'one','','regular','','n'),(1492,205,'Статическая страница','staticpageId',3,23,'0',1362,25,'one','','regular','','n'),(1493,205,'Ссылка','url',1,1,'',1363,0,'none','','regular','','n'),(1494,205,'Статус','toggle',10,23,'y',1364,6,'one','','regular','','n'),(1495,205,'Отображать в нижнем меню','bottom',10,5,'y',1365,6,'one','','regular','','n'),(1496,205,'Порядок отображения','move',3,4,'0',1366,0,'none','','regular','','n'),(1814,25,'Контент','details',4,13,'',1667,0,'none','','regular','','n'),(1510,204,'Контент','detailsSpan',0,16,'',1379,0,'none','','regular','','n'),(1511,204,'Высота','detailsHtmlHeight',3,18,'200',1384,0,'none','','regular','','n'),(1513,204,'Css класс для body','detailsHtmlBodyClass',1,1,'',1385,0,'none','','regular','','n'),(1514,204,'Css стили','detailsHtmlStyle',4,6,'',1386,0,'none','','regular','','n'),(1515,204,'Тип','type',10,5,'html',1380,6,'one','','regular','','n'),(1516,204,'Значение','detailsString',1,1,'',1387,0,'none','','regular','','n'),(1517,204,'Значение','detailsTextarea',4,6,'',1557,0,'none','','regular','','n'),(2173,301,'Тип компонента','type',10,23,'static',2173,6,'one','','regular','','n'),(2174,301,'Указанный вручную','content',1,1,'',2174,0,'none','','regular','','n'),(2175,301,'Шагов вверх','up',3,18,'0',2175,0,'none','','regular','','n'),(2172,301,'Тэг','tag',10,23,'title',2172,6,'one','','regular','','n'),(2171,301,'Действие','fsection2factionId',3,23,'0',2171,147,'one','','required','','n'),(2170,301,'Раздел','fsectionId',3,23,'0',2170,101,'one','','required','','n'),(1533,101,'Статус','toggle',10,5,'y',1429,6,'one','','regular','','n'),(1554,3,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',462,5,'one','`storeRelationAbility`!=\"none\"','regular','','n'),(1560,101,'Связь с вышестоящим разделом по полю','parentSectionConnector',3,23,'0',815,5,'one','','regular','','n'),(1562,101,'От какого класса наследовать класс контроллера','extends',1,1,'',1431,0,'none','','regular','','n'),(1575,130,'Смена пароля','changepasswd',0,16,'',1708,0,'none','','regular','','n'),(1576,130,'Дата последнего запроса','changepasswdDate',9,19,'0000-00-00 00:00:00',1709,0,'none','','regular','','n'),(1577,130,'Код','changepasswdCode',1,1,'',1710,0,'none','','regular','','n'),(1658,195,'Альтернативное наименование','alt',1,1,'',2167,0,'none','','regular','','n'),(2132,10,'Порядок отображения','move',3,4,'0',2132,0,'none','','regular','','n'),(1886,9,'Изменить название столбца на','alterTitle',1,1,'',2196,0,'none','','regular','','n'),(2100,7,'Отображать в панели действий','display',12,9,'1',2100,0,'none','','regular','','n'),(2131,10,'Dashboard','dashboard',1,1,'',2131,0,'none','','regular','','n'),(2159,9,'Статус','toggle',10,5,'y',2200,6,'one','','regular','','n'),(2161,171,'Режим','mode',10,23,'inherit',2251,6,'one','','regular','','n'),(2166,91,'Auto title','title',1,1,'',2166,0,'none','','hidden','','n'),(2163,2,'Заголовочное поле','titleFieldId',3,23,'0',2163,5,'one','`entityId` = \"<?=$this->id?>\" AND `columnTypeId` != \"0\"','regular','','n'),(2164,8,'Auto title','title',1,1,'',2164,0,'none','','hidden','','n'),(2165,9,'Auto title','title',1,1,'',2206,0,'none','','hidden','','n'),(2167,195,'Auto title','title',1,1,'',2203,0,'none','','hidden','','n'),(2168,147,'Auto title','title',1,1,'',2242,0,'none','','hidden','','n'),(2169,171,'Auto title','title',1,1,'',1402,0,'none','','hidden','','n'),(2197,5,'Режим','mode',10,23,'regular',9,6,'one','','regular','','n'),(2199,5,'Подсказка','tooltip',4,6,'',95,0,'none','','regular','','n'),(2200,9,'Подсказка','tooltip',4,6,'',2253,0,'none','','regular','','n'),(2203,195,'Доступ','access',10,5,'all',2311,6,'one','','regular','','n'),(2202,7,'Статус','toggle',10,23,'y',2202,6,'one','','regular','','n'),(2204,195,'Выбранные','profileIds',1,7,'',2314,10,'many','','regular','','n'),(2205,9,'Доступ','access',10,5,'all',2313,6,'one','','regular','','n'),(2206,9,'Выбранные','profileIds',1,7,'',2315,10,'many','','regular','','n'),(2207,171,'Влияние','impact',10,5,'all',2134,6,'one','','regular','','n'),(2208,25,'Родительская страница','staticpageId',3,23,'0',1666,25,'one','','regular','','n'),(2209,8,'Переименовать','rename',1,1,'',2209,0,'none','','regular','','n'),(2210,9,'Редактор','editor',10,5,'0',2205,6,'one','','regular','','n'),(2211,3,'Группировка','groupBy',3,23,'0',1278,5,'one','','regular','','n'),(2213,3,'Режим подгрузки данных','rowsetSeparate',10,23,'auto',2310,6,'one','','regular','','n'),(2214,8,'Автосайз окна','fitWindow',10,23,'auto',2214,6,'one','','regular','','n'),(2215,10,'Максимальное количество окон','maxWindows',3,18,'15',2215,0,'none','','regular','','n'),(2270,309,'Заголовок','tplDecSubj',4,1,'',2271,0,'none','','regular','','n'),(2269,309,'Текст','tplIncBody',4,6,'',2270,0,'none','','regular','','n'),(2268,309,'Заголовок','tplIncSubj',4,1,'',2269,0,'none','','regular','','n'),(2267,309,'Назначение','tplFor',10,23,'inc',2268,6,'one','','regular','','n'),(2266,309,'Сообщение','tpl',0,16,'',2267,0,'none','','regular','','n'),(2263,309,'Цвет фона','bg',13,11,'212#d9e5f3',2264,0,'none','','regular','','n'),(2264,309,'Цвет текста','fg',13,11,'216#044099',2265,0,'none','','regular','','n'),(2265,309,'Подсказка','tooltip',4,6,'',2266,0,'none','','regular','','n'),(2262,309,'Пункты меню','sectionId',1,23,'',2263,3,'many','FIND_IN_SET(`sectionId`, \"<?=Indi::model(\'Section\')->fetchAll(\'`sectionId` = \"0\"\')->column(\'id\', true)?>\")','regular','','n'),(2255,309,'Сущность','entityId',3,23,'0',2256,2,'one','','required','','n'),(2256,309,'Событие / PHP','event',1,1,'',2257,0,'none','','regular','','n'),(2257,309,'Получатели','profileId',1,23,'',2258,10,'many','','required','','n'),(2258,309,'Статус','toggle',10,23,'y',2259,6,'one','','regular','','n'),(2259,309,'Счетчик','qty',0,16,'',2260,0,'none','','regular','','n'),(2260,309,'Отображение / SQL','qtySql',1,1,'',2261,0,'none','','required','','n'),(2261,309,'Направление изменения','qtyDiffRelyOn',10,23,'event',2262,6,'one','','regular','','n'),(2254,309,'Наименование','title',1,1,'',2254,0,'none','','required','','n'),(2236,307,'Наименование','title',1,1,'',2236,0,'none','','required','','n'),(2237,307,'Ключ','alias',1,1,'',2237,0,'none','','required','','n'),(2238,307,'Статус','toggle',10,23,'y',2238,6,'one','','regular','','n'),(2239,5,'Мультиязычность','l10n',10,23,'n',413,6,'one','','regular','','n'),(2240,147,'Разрешено не передавать id в uri','allowNoid',12,9,'0',2168,0,'none','','regular','','n'),(2241,147,'Над записью','row',10,5,'existing',2240,6,'one','','regular','','n'),(2242,147,'Где брать идентификатор','where',1,1,'',2241,0,'none','','regular','','n'),(2243,2,'Паттерн комплекта календарных полей','spaceScheme',10,23,'none',2243,6,'one','','regular','','n'),(2244,2,'Комплект календарных полей','spaceFields',1,23,'',2244,5,'many','`entityId` = \"<?=$this->id?>\"','regular','','n'),(2245,308,'Сущность','entityId',3,23,'0',2245,2,'one','','hidden','','n'),(2246,308,'Поле','fieldId',3,23,'0',2246,5,'one','','readonly','','n'),(2247,308,'От какого поля зависит','consider',3,23,'0',2247,5,'one','`id` != \"<?=$this->fieldId?>\" AND `columnTypeId` != \"0\"','required','','n'),(2248,308,'Поле по ключу','foreign',3,23,'0',2248,5,'one','','regular','','n'),(2249,308,'Auto title','title',1,1,'',2249,0,'none','','hidden','','n'),(2250,171,'Выбранные','profileIds',1,7,'',2169,10,'many','','regular','','n'),(2252,171,'Наименование','rename',1,1,'',2250,0,'none','','regular','','n'),(2253,9,'Ключ','alias',1,1,'',2133,0,'none','','regular','','n'),(2271,309,'Текст','tplDecBody',4,6,'',2272,0,'none','','regular','','n'),(2272,309,'Заголовок','tplEvtSubj',4,1,'',2273,0,'none','','regular','','n'),(2273,309,'Сообщение','tplEvtBody',4,6,'',2321,0,'none','','regular','','n'),(2274,310,'Уведомление','noticeId',3,23,'0',2275,309,'one','','readonly','','n'),(2275,310,'Роль','profileId',3,23,'0',2276,10,'one','','readonly','','n'),(2276,310,'Критерий','criteriaRelyOn',10,5,'event',2277,6,'one','','regular','','n'),(2277,310,'Общий','criteriaEvt',1,1,'',2278,0,'none','','regular','','n'),(2278,310,'Для увеличения','criteriaInc',1,1,'',2279,0,'none','','regular','','n'),(2279,310,'Для уменьшения','criteriaDec',1,1,'',2280,0,'none','','regular','','n'),(2280,310,'Ауто титле','title',1,1,'',2281,0,'none','','hidden','','n'),(2281,310,'Дублирование на почту','email',10,23,'n',2282,6,'one','','regular','','n'),(2282,310,'Дублирование в ВК','vk',10,23,'n',2283,6,'one','','regular','','n'),(2283,310,'Дублирование по SMS','sms',10,23,'n',2284,6,'one','','regular','','n'),(2284,310,'Критерий','criteria',1,1,'',2285,0,'none','','hidden','','n'),(2285,310,'Дублирование на почту','mail',10,23,'n',2316,6,'one','','hidden','','n'),(2286,311,'Наименование','title',1,1,'',2286,0,'none','','required','','n'),(2287,312,'Год','yearId',3,23,'0',2287,311,'one','','required','','n'),(2288,312,'Месяц','month',10,23,'01',2288,6,'one','','regular','','n'),(2289,312,'Наименование','title',1,1,'',2289,0,'none','','regular','','n'),(2290,312,'Порядок','move',3,4,'0',2290,0,'none','','regular','','n'),(2291,313,'Сущность','entityId',3,23,'0',2291,2,'one','','readonly','','n'),(2292,313,'Объект','key',3,23,'0',2292,0,'one','','readonly','','n'),(2293,313,'Что изменено','fieldId',3,23,'0',2293,5,'one','`columnTypeId` != \"0\"','readonly','','n'),(2294,313,'Было','was',4,13,'',2294,0,'none','','readonly','','n'),(2295,313,'Стало','now',4,13,'',2295,0,'none','','readonly','','n'),(2296,313,'Когда','datetime',9,19,'0000-00-00 00:00:00',2296,0,'none','','readonly','','n'),(2297,313,'Месяц','monthId',3,23,'0',2297,312,'one','','readonly','','n'),(2298,313,'Тип автора','changerType',3,23,'0',2298,2,'one','','readonly','','n'),(2299,313,'Автор','changerId',3,23,'0',2299,0,'one','','readonly','','n'),(2300,313,'Роль','profileId',3,23,'0',2300,10,'one','','readonly','','n'),(2301,3,'Разворачивать пункт меню','expand',10,5,'all',20,6,'one','','regular','','n'),(2302,3,'Выбранные','expandRoles',1,23,'',21,10,'many','','regular','','n'),(2303,3,'Доступ','roleIds',1,23,'',2312,10,'many','','hidden','','n'),(2305,9,'Текст','summaryText',1,1,'',2305,0,'none','','regular','','n'),(2306,308,'Обязательное','required',10,23,'n',2306,6,'one','','regular','','n'),(2307,308,'Коннектор','connector',3,23,'0',2307,5,'one','','regular','','n'),(2308,9,'Группа','group',10,5,'normal',2308,6,'one','','regular','','n'),(2311,195,'Разрешить сброс','allowClear',12,9,'1',2183,0,'none','','regular','','n'),(2312,3,'Выделение более одной записи','multiSelect',12,9,'0',2323,0,'none','','regular','','n'),(2313,9,'Поле по ключу','further',3,23,'0',30,5,'one','','regular','','n'),(2314,195,'Поле по ключу','further',3,23,'0',1314,5,'one','','regular','','n'),(2315,9,'Ширина','width',3,18,'0',2210,0,'none','','regular','','n'),(2316,310,'Статус','toggle',10,23,'y',2274,6,'one','','regular','','n'),(2317,195,'Подсказка','tooltip',4,6,'',2317,0,'none','','regular','','n'),(2318,11,'Демо-режим','demo',10,23,'n',2318,6,'one','','regular','','n'),(2319,10,'Демо-режим','demo',10,23,'n',2319,6,'one','','regular','','n'),(2320,2,'Группировать файлы','filesGroupBy',3,23,'0',2320,5,'one','`entityId` = \"<?=$this->id?>\" AND `storeRelationAbility` = \"one\"','regular','','n'),(2321,309,'Тип','type',10,23,'p',2255,6,'one','','regular','','n'),(2322,3,'Плитка','tileField',3,23,'0',2213,5,'one','`elementId` = \"14\"','regular','','n'),(2323,3,'Превью','tileThumb',3,23,'0',2301,20,'one','','regular','','n'),(2324,11,'Правки UI','uiedit',10,23,'n',2324,6,'one','','regular','','n'),(2325,307,'Состояние','state',10,23,'noth',2325,6,'one','','readonly','','n'),(2326,307,'Админка','admin',0,16,'',2326,0,'none','','regular','','n'),(2327,307,'Система','adminSystem',0,16,'',2327,0,'none','','regular','','n'),(2328,307,'Интерфейс','adminSystemUi',10,5,'n',2328,6,'one','','regular','','n'),(2329,307,'Константы','adminSystemConst',10,5,'n',2329,6,'one','','regular','','n'),(2330,307,'Проект','adminCustom',0,16,'',2330,0,'none','','regular','','n'),(2331,307,'Интерфейс','adminCustomUi',10,5,'n',2331,6,'one','','regular','','n'),(2332,307,'Константы','adminCustomConst',10,5,'n',2332,6,'one','','regular','','n'),(2333,307,'Данные','adminCustomData',10,5,'n',2333,6,'one','','regular','','n'),(2334,307,'Шаблоны','adminCustomTmpl',10,5,'n',2334,6,'one','','regular','','n'),(2335,307,'Порядок','move',3,4,'0',2335,0,'none','','regular','','n'),(2336,314,'Задача','title',1,1,'',2336,0,'none','','required','','n'),(2337,314,'Создана','datetime',9,19,'<?=date(\'Y-m-d H:i:s\')?>',2337,0,'none','','readonly','','n'),(2338,314,'Параметры','params',4,6,'',2338,0,'none','','regular','','n'),(2339,314,'Процесс','proc',0,16,'',2339,0,'none','','regular','','n'),(2340,314,'Начат','procSince',9,19,'0000-00-00 00:00:00',2340,0,'none','','regular','','n'),(2341,314,'PID','procID',3,18,'0',2341,0,'none','','readonly','','n'),(2342,314,'Этап','stage',10,23,'count',2342,6,'one','','regular','','n'),(2343,314,'Статус','state',10,23,'waiting',2343,6,'one','','regular','','n'),(2344,314,'Сегменты','chunk',3,18,'0',2345,0,'none','','regular','','n'),(2345,314,'Оценка','count',0,16,'',2346,0,'none','','regular','','n'),(2346,314,'Статус','countState',10,23,'waiting',2347,6,'one','','readonly','','n'),(2347,314,'Размер','countSize',3,18,'0',2348,0,'none','','readonly','','n'),(2348,314,'Создание','items',0,16,'',2349,0,'none','','regular','','n'),(2349,314,'Статус','itemsState',10,23,'waiting',2350,6,'one','','readonly','','n'),(2350,314,'Размер','itemsSize',3,18,'0',2351,0,'none','','readonly','','n'),(2351,314,'Байт','itemsBytes',3,18,'0',2352,0,'none','','regular','','n'),(2352,314,'Процессинг','queue',0,16,'',2353,0,'none','','regular','','n'),(2353,314,'Статус','queueState',10,23,'waiting',2354,6,'one','','readonly','','n'),(2354,314,'Размер','queueSize',3,18,'0',2355,0,'none','','regular','','n'),(2355,314,'Применение','apply',0,16,'',2356,0,'none','','regular','','n'),(2356,314,'Статус','applyState',10,23,'waiting',2357,6,'one','','readonly','','n'),(2357,314,'Размер','applySize',3,18,'0',2382,0,'none','','readonly','','n'),(2358,315,'Очередь задач','queueTaskId',3,23,'0',2358,314,'one','','regular','','n'),(2359,315,'Расположение','location',1,1,'',2359,0,'none','','regular','','n'),(2360,315,'Условие выборки','where',4,6,'',2362,0,'none','','regular','','n'),(2361,315,'Оценка','count',0,16,'',2363,0,'none','','regular','','n'),(2362,315,'Статус','countState',10,23,'waiting',2364,6,'one','','readonly','','n'),(2363,315,'Размер','countSize',3,18,'0',2365,0,'none','','readonly','','n'),(2364,315,'Создание','items',0,16,'',2366,0,'none','','regular','','n'),(2365,315,'Статус','itemsState',10,23,'waiting',2367,6,'one','','readonly','','n'),(2366,315,'Размер','itemsSize',3,18,'0',2368,0,'none','','readonly','','n'),(2367,315,'Процессинг','queue',0,16,'',2369,0,'none','','regular','','n'),(2368,315,'Статус','queueState',10,23,'waiting',2371,6,'one','','readonly','','n'),(2369,315,'Размер','queueSize',3,18,'0',2372,0,'none','','regular','','n'),(2370,315,'Применение','apply',0,16,'',2379,0,'none','','regular','','n'),(2371,315,'Статус','applyState',10,23,'waiting',2380,6,'one','','readonly','','n'),(2372,315,'Размер','applySize',3,18,'0',2381,0,'none','','readonly','','n'),(2373,316,'Очередь','queueTaskId',3,18,'0',2373,314,'one','','readonly','','n'),(2374,316,'Сегмент','queueChunkId',3,23,'0',2374,315,'one','','regular','','n'),(2375,316,'Таргет','target',1,1,'',2375,0,'none','','readonly','','n'),(2376,316,'Значение','value',4,1,'',2376,0,'none','','readonly','','n'),(2377,316,'Результат','result',4,13,'',2377,0,'none','','regular','','n'),(2378,316,'Статус','stage',10,5,'items',2378,6,'one','','regular','','n'),(2379,315,'Порядок','move',3,4,'0',2383,0,'none','','regular','','n'),(2380,315,'Родительский сегмент','queueChunkId',3,18,'0',2360,315,'one','','readonly','','n'),(2381,315,'Фракция','fraction',10,23,'none',2361,6,'one','','regular','','n'),(2382,314,'Этап - Статус','stageState',1,1,'',2344,0,'none','','hidden','','n'),(2383,315,'Байт','itemsBytes',3,18,'0',2370,0,'none','','regular','','n'),(2384,10,'Тип','type',10,5,'p',2384,6,'one','','regular','','n'),(2385,171,'Элемент','elementId',3,23,'0',2252,4,'one','','regular','','n'),(2386,8,'Мультиязычность','l10n',10,23,'n',2386,6,'one','','regular','','n');
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
INSERT INTO `fsection` VALUES (8,'Пользователи','users',130,146,28,0,'',20,'c',0,'ASC','','r','','','y',0,''),(37,'Статические страницы','static',25,30,39,0,'',20,'c',0,'ASC','','r','','','y',0,''),(22,'Фидбэк','feedback',128,144,44,0,'',20,'c',0,'ASC','','s','\"\"','add','n',0,''),(26,'Мой профиль','myprofile',130,0,22,39,'',20,'c',0,'ASC','','s','`id` = \'\'','form','y',0,'My'),(39,'Главная','index',25,0,8,0,'',20,'c',0,'ASC','','r','','','y',0,''),(41,'Карта сайта','sitemap',101,113,41,0,'`toggle`=\"y\"',20,'c',585,'ASC','','r','','','y',0,'');
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
INSERT INTO `fsection2faction` VALUES (126,8,37,'r',0,0,'','Восстановление доступа',0,'existing',''),(129,22,1,'r',0,0,'','По умолчанию',0,'existing',''),(124,26,3,'r',0,0,'','Изменить',0,'existing',''),(127,37,2,'r',0,0,'','Просмотр',0,'existing',''),(123,39,1,'r',0,0,'','По умолчанию',0,'existing',''),(128,41,1,'r',0,0,'','По умолчанию',0,'existing',''),(125,8,6,'r',0,0,'','Активация аккаунта',0,'existing','');
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
  `alias` varchar(255) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM AUTO_INCREMENT=2565 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grid`
--

LOCK TABLES `grid` WRITE;
/*!40000 ALTER TABLE `grid` DISABLE KEYS */;
INSERT INTO `grid` VALUES (1,2,1,1,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(2,2,2,2,'','y','Тип столбца MySQL',0,'','all','','0','','none','','normal',0,0),(3,2,3,3,'','y','Пригоден для хранения внешних ключей',0,'','all','','0','','none','','normal',0,0),(4,5,4,4,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(5,5,5,5,'','y','Таблица БД',0,'','all','','0','','none','','normal',0,0),(6,6,7,6,'','y','Наименование поля',0,'','all','','0','','none','','normal',0,0),(7,6,8,7,'','y','Наименование соответствующего полю столбца в  таблице БД',0,'','all','','0','','none','','normal',0,0),(8,6,9,8,'','y','Тип столбца MySQL',0,'','all','','0','','none','','normal',0,0),(9,6,10,9,'','y','Элемент управления',0,'','all','','0','','none','','normal',0,0),(10,6,11,11,'','y','Значение по умолчанию',0,'','all','','0','','none','','normal',0,0),(11,6,12,13,'','y','Ключи какой сущности будут храниться в этом поле',0,'','all','','0','','none','','normal',0,0),(13,6,14,2321,'','y','Положение в списке',0,'','all','','0','','none','','normal',0,0),(14,7,19,16,'Сущность','y','Сущность',0,'','all','','0','','none','','normal',0,0),(15,7,20,14,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(16,7,21,15,'','y','Контроллер',0,'','all','','0','','none','','normal',0,0),(17,7,22,17,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(18,7,23,31,'','y','Положение в списке',0,'','all','','0','','none','','normal',0,0),(20,7,25,2327,'СНС','y','Записей на странице',0,'Строк на странице','all','','0','','none','','normal',0,0),(23,8,27,23,'','y','Действие',0,'','all','','0','','none','','normal',0,0),(24,8,29,25,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(25,8,30,2326,'','y','Положение в списке',0,'','all','','0','','none','','normal',0,0),(26,10,31,26,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(27,10,32,27,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(29,11,2165,29,'Столбец','y','Auto title',0,'','all','','0','','none','','normal',0,0),(30,11,35,2320,'','y','Очередность отображения столбца в гриде',0,'','all','','0','','none','','normal',0,0),(32,13,36,32,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(33,13,37,2329,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(34,12,16,34,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(35,12,17,35,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(36,14,39,36,'','y','Фамилия Имя',0,'','all','','0','','none','','normal',0,0),(37,14,40,37,'','y','Email (используется в качестве логина)',0,'','all','','0','','none','','normal',0,0),(38,14,41,38,'','y','Пароль',0,'','all','','0','','none','','normal',0,0),(39,14,42,39,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(42,16,65,43,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(43,16,66,44,'','y','Способен работать с внешними ключами',0,'','all','','0','','none','','normal',0,0),(46,16,64,42,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(89,22,107,56,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(90,22,108,57,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(91,22,109,59,'','y','Ширина',0,'','all','','0','','none','','normal',0,0),(92,22,110,60,'','y','Высота',0,'','all','','0','','none','','normal',0,0),(93,22,111,58,'','y','Размер',0,'','all','','0','','none','','normal',0,0),(94,22,112,61,'','y','Ограничить пропорциональную <span id=\"slaveDimensionTitle\">высоту</span>',0,'','all','','0','','none','','normal',0,0),(130,30,131,61,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(132,30,133,63,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(2365,379,0,2365,'Взятый из контекста','y','Взятый из контекста',2362,'','all','','0','context','none','','normal',0,0),(2363,379,2179,2363,'','y','Префикс',2362,'','all','','1','','none','','normal',0,0),(136,30,137,67,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(341,12,377,253,'','y','Порядок отображения',0,'','all','','0','','none','','normal',0,0),(375,100,472,289,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(376,100,473,290,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(377,100,474,291,'','y','Значение по умолчанию',0,'','all','','0','','none','','normal',0,0),(378,101,477,292,'','y','Параметр настройки',0,'','all','','0','','none','','normal',0,0),(379,101,478,293,'','y','Значение параметра',0,'','all','','0','','none','','normal',0,0),(383,8,28,2328,'','y','Профили пользователей, имеющих доступ к этому действию в этом разделе',0,'','all','','0','','none','','normal',0,0),(1335,7,1366,926,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(1334,113,1040,925,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(1333,172,1365,924,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(1332,10,1364,923,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(420,113,559,328,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(421,113,560,329,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(424,113,563,330,'','y','Прикрепленная сущность',0,'','all','','0','','none','','normal',0,0),(443,5,612,347,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(489,144,678,388,'','y','Имя',0,'','all','','0','','none','','normal',0,0),(490,144,679,389,'','y','Email',0,'','all','','0','','none','','normal',0,0),(491,144,681,391,'','y','Дата',0,'','all','','0','','none','','normal',0,0),(492,145,682,391,'','y','Email',0,'','all','','0','','none','','normal',0,0),(493,145,683,392,'','y','Дата',0,'','all','','0','','none','','normal',0,0),(494,146,684,990,'','y','Email',0,'','all','','0','','none','','normal',0,0),(1470,146,685,392,'','y','ФИО',0,'','all','','0','','none','','normal',0,0),(1382,224,1444,929,'','y','Порядок отображения',0,'','all','','0','','none','','normal',0,0),(1384,224,1445,928,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(832,172,857,624,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(833,172,858,625,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(834,173,860,626,'','y','Действие',0,'','all','','0','','none','','normal',0,0),(1383,224,1443,926,'','y','Поле прикрепленной к разделу сущности',0,'','all','','0','','none','','normal',0,0),(1053,189,1102,772,'','y','Пользователь',0,'','all','','0','','none','','normal',0,0),(1052,189,1101,771,'','y','Дата последней активности',0,'','all','','0','','none','','normal',0,0),(1051,189,1100,770,'','y','Id сессии',0,'','all','','0','','none','','normal',0,0),(1039,172,1074,759,'','y','Выполнять maintenance()',0,'','all','','0','','none','','normal',0,0),(979,173,1027,728,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(1066,191,1194,782,'','y','Компонент',0,'','all','','0','','none','','normal',0,0),(1067,191,1195,783,'','y','Очередность',0,'','all','','0','','none','','normal',0,0),(1068,191,1196,784,'','y','Префикс',0,'','all','','0','','none','','normal',0,0),(2319,146,699,2319,'','y','Последний визит',0,'','all','','0','','none','','normal',0,0),(2312,224,2183,2312,'','y','Статическая фильтрация',0,'','all','','0','','none','','normal',0,0),(2311,224,2182,2311,'','y','Значение по умолчанию',0,'','all','','0','','none','','normal',0,0),(2362,379,0,2362,'Компонент','y','Компонент',0,'','all','','0','component','none','','normal',0,0),(2361,379,2172,2361,'','y','Тэг',0,'','all','','0','','none','','normal',0,0),(2360,379,2181,2360,'','y','Порядок отображения',0,'','all','','0','','none','','normal',0,0),(1231,201,1342,851,'','y','Поле',0,'','all','','0','','none','','normal',0,0),(2356,201,2252,988,'','y','Наименование',0,'','all','','1','','none','','normal',0,0),(1439,232,1515,965,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(2359,379,2173,2359,'','y','Тип компонента',0,'','all','','0','','none','','normal',0,0),(1421,232,1485,962,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(1422,232,1486,963,'','y','Псевдоним',0,'','all','','0','','none','','normal',0,0),(1423,232,1488,1132,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(1449,113,1533,989,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(1448,201,1532,2356,'','y','Значение по умолчанию',0,'','all','','0','','none','','normal',0,0),(1515,113,585,1036,'','y','Порядок отображения соответствующего пункта в меню',0,'','all','','0','','none','','normal',0,0),(1656,11,1886,1156,'','y','Изменить название столбца на',0,'','all','','0','','none','','normal',0,0),(1767,146,691,1222,'','y','Дата регистрации',0,'','all','','0','','none','','normal',0,0),(1954,224,1658,1945,'','y','Альтернативное наименование',0,'','all','','0','','none','','normal',0,0),(2280,201,2161,1946,'','y','Режим',0,'','all','','0','','none','','normal',0,0),(2320,11,2159,2325,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(2321,6,2197,10,'','y','Режим',0,'','all','','0','','none','','normal',0,0),(2322,11,34,30,'','y','Поле',0,'','all','','0','','none','','normal',0,0),(2323,10,2202,2323,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(2324,8,2209,2564,'','y','Переименовать',0,'','all','','0','','none','','normal',0,0),(2325,11,2210,2416,'','y','Редактор',0,'','all','','0','','none','','normal',0,0),(2326,8,2212,297,'РSП','y','South-панель',0,'Режим отображения south-панели','all','','0','','none','','normal',0,0),(2327,7,2213,18,'РПД','y','Режим подгрузки данных',0,'Режим подгрузки данных','all','','0','','none','','normal',0,0),(2328,8,2214,2324,'','y','Автосайз окна',0,'','all','','0','','none','','normal',0,0),(2329,13,2215,2464,'МКО','y','Максимальное количество окон',0,'Максимальное количество окон','all','','1','','none','','normal',0,0),(2415,390,2283,2462,'SMS','y','Дублирование по SMS',0,'Дублирование по SMS','all','','0','','none','','normal',0,0),(2394,389,2259,2394,'','y','Счетчик',0,'','all','','0','','none','','normal',0,0),(2395,389,2260,2395,'','y','Отображение / SQL',2394,'','all','','0','','none','','normal',0,0),(2396,389,2256,2396,'','y','Событие / PHP',2394,'','all','','0','','none','','normal',0,0),(2397,389,2262,2397,'','y','Пункты меню',2394,'','all','','0','','none','','normal',0,0),(2398,389,2263,2398,'','y','Цвет фона',2394,'','all','','0','','none','','normal',0,0),(2399,389,2264,2399,'','y','Цвет текста',2394,'','all','','0','','none','','normal',0,0),(2416,11,2253,2322,'','y','Ключ',0,'','all','','0','','none','','normal',0,0),(2390,389,2254,2390,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(2391,389,2255,2391,'','y','Сущность',0,'','all','','0','','none','','normal',0,0),(2392,389,2257,2392,'','y','Получатели',0,'','all','','0','','none','','normal',0,0),(2393,389,2258,2393,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(2468,387,2237,2468,'','y','Ключ',0,'','all','','0','','none','','normal',0,0),(2467,387,2236,2467,'','y','Наименование',0,'','all','','0','','none','','normal',0,0),(2352,6,2239,2352,'l10n','y','Мультиязычность',0,'','all','','0','','none','','normal',0,0),(2353,388,2247,2352,'','y','От какого поля зависит',0,'','all','','0','','none','','normal',0,0),(2354,388,2248,2353,'','y','Поле по ключу',0,'','all','','0','','none','','normal',0,0),(2357,201,2207,2357,'','y','Влияние',0,'','all','','1','','none','','normal',0,0),(2358,201,2250,2358,'','y','Выбранные',0,'','all','','1','','none','','normal',0,0),(2364,379,2174,2364,'','y','Указанный вручную',2362,'','all','','0','','none','','normal',0,0),(2366,379,2175,2366,'Уровень','y','Шагов вверх',2365,'','all','','0','','none','','normal',0,0),(2367,379,2176,2367,'','y','Источник',2365,'','all','','0','','none','','normal',0,0),(2368,379,2178,2368,'','y','Поле',2365,'','all','','0','','none','','normal',0,0),(2369,379,2180,2369,'','y','Постфикс',2362,'','all','','1','','none','','normal',0,0),(2414,390,2282,2415,'VK','y','Дублирование в ВК',0,'Дублирование во ВКонтакте','all','','0','','none','','normal',0,0),(2413,390,2281,2414,'Email','y','Дублирование на почту',0,'Дублирование на почту','all','','0','','none','','normal',0,0),(2412,390,2277,2413,'','y','Общий',0,'','all','','0','','none','','normal',0,0),(2411,390,2275,2412,'','y','Роль',0,'','all','','0','','none','','normal',0,0),(2417,388,2306,2417,'[ ! ]','y','Обязательное',0,'Обязательное','all','','0','','none','','normal',0,0),(2418,388,2307,2418,'','y','Коннектор',0,'','all','','0','','none','','normal',0,0),(2419,11,2308,2419,'','y','Группа',0,'','all','','0','','none','','normal',0,0),(2450,391,12,2450,'Сущность','y','Ключи какой сущности будут храниться в этом поле',2448,'Ключи какой сущности будут храниться в этом поле','all','','0','','none','','normal',0,0),(2449,391,470,2449,'Режим','y','Предназначено для хранения ключей',2448,'Предназначено для хранения ключей','all','','0','','none','','normal',0,0),(2448,391,0,2448,'Ключи','y','Ключи',0,'','all','','0','fk','none','','normal',0,0),(2447,391,11,2447,'По умолчанию','y','Значение по умолчанию',2444,'Значение по умолчанию','all','','0','','none','','normal',0,0),(2446,391,9,2446,'Тип','y','Тип столбца MySQL',2444,'Тип столбца MySQL','all','','0','','none','','normal',0,0),(2445,391,8,2445,'Имя','y','Наименование соответствующего полю столбца в  таблице БД',2444,'','all','','0','','none','','normal',0,0),(2444,391,0,2444,'MySQL','y','MySQL',0,'','all','','0','mysql','none','','normal',0,0),(2443,391,2199,2443,'','y','Подсказка',2440,'','all','','0','','none','','normal',0,0),(2441,391,2197,2441,'','y','Режим',2440,'','all','','0','','none','','normal',0,0),(2442,391,10,2442,'UI','y','Элемент управления',2440,'Элемент управления','all','','0','','none','','normal',0,0),(2438,391,6,2438,'Сущность','y','Сущность, в структуру которой входит это поле',0,'Сущность, в структуру которой входит это поле','all','','0','','none','','normal',0,0),(2439,391,7,2439,'Наименование','y','Наименование поля',0,'','all','','1','','none','','normal',0,0),(2440,391,0,2440,'Отображение','y','Отображение',0,'','all','','0','view','none','','normal',0,0),(2451,391,754,2451,'Фильтрация','y','Статическая фильтрация',2448,'Статическая фильтрация','all','','0','','none','','normal',0,0),(2452,391,2239,2452,'l10n','y','Мультиязычность',0,'Мультиязычность','all','','0','','none','','normal',0,0),(2453,391,14,2453,'','y','Положение в списке',0,'','all','','0','','none','','normal',0,0),(2459,7,502,2459,'','y','Родительский класс PHP',0,'','all','','1','','none','','normal',0,0),(2460,7,2309,2460,'','y','Родительский класс JS',0,'','all','','1','','none','','normal',0,0),(2461,11,2315,2461,'','y','Ширина',0,'','all','','1','','none','','normal',0,0),(2462,390,2316,2411,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(2463,14,2318,2463,'Демо','y','Демо-режим',0,'Демо-режим','all','','0','','none','','normal',0,0),(2464,13,2319,2563,'Демо','y','Демо-режим',0,'Демо-режим','all','','0','','none','','normal',0,0),(2465,5,2320,2465,'','y','Группировать файлы',0,'','all','','1','','none','','normal',0,0),(2466,14,2324,2466,'','y','Правки UI',0,'','all','','0','','none','','normal',0,0),(2469,387,2326,2469,'','y','Админка',0,'','all','','0','','none','','normal',0,0),(2470,387,2238,2470,'','y','Статус',2469,'','all','','0','','none','','normal',0,0),(2471,387,2327,2471,'','y','Система',2469,'','all','','0','','none','','normal',0,0),(2472,387,2328,2472,'','y','Интерфейс',2471,'','all','','0','','none','','normal',0,0),(2473,387,2329,2473,'','y','Константы',2471,'','all','','0','','none','','normal',0,0),(2474,387,2330,2474,'','y','Проект',2469,'','all','','0','','none','','normal',0,0),(2475,387,2331,2475,'','y','Интерфейс',2474,'','all','','0','','none','','normal',0,0),(2476,387,2332,2476,'','y','Константы',2474,'','all','','0','','none','','normal',0,0),(2477,387,2333,2477,'','y','Данные',2474,'','all','','0','','none','','normal',0,0),(2478,387,2334,2478,'','y','Шаблоны',2474,'','all','','0','','none','','normal',0,0),(2479,387,2335,2479,'','y','Порядок',0,'','all','','0','','none','','normal',0,0),(2519,392,2353,2519,'','y','Статус',2518,'','all','','0','','none','','normal',0,0),(2518,392,2352,2518,'','y','Процессинг',0,'','all','','0','','none','','normal',0,0),(2514,392,2348,2514,'','y','Создание',0,'','all','','0','','none','','normal',0,0),(2515,392,2349,2515,'','y','Статус',2514,'','all','','0','','none','','normal',0,0),(2517,392,2351,2517,'','y','Байт',2514,'','all','','0','','sum','','normal',0,0),(2516,392,2350,2516,'','y','Размер',2514,'','all','','0','','none','','normal',0,0),(2511,392,2345,2511,'','y','Оценка',0,'','all','','0','','none','','normal',0,0),(2512,392,2346,2512,'','y','Статус',2511,'','all','','0','','none','','normal',0,0),(2513,392,2347,2513,'','y','Размер',2511,'','all','','0','','none','','normal',0,0),(2508,392,2339,2508,'','y','Процесс',0,'','all','','0','','none','','normal',0,0),(2509,392,2341,2509,'','y','PID',2508,'','all','','0','','none','','normal',0,0),(2510,392,2340,2510,'','y','Начат',2508,'','all','','0','','none','','normal',0,0),(2505,392,2342,2505,'','h','Этап',0,'','all','','0','','none','','normal',0,0),(2506,392,2343,2506,'','h','Статус',0,'','all','','0','','none','','normal',0,0),(2507,392,2344,2507,'','y','Сегменты',0,'','all','','0','','none','','normal',0,0),(2502,392,2337,2502,'','y','Создана',0,'','all','','0','','none','','normal',0,0),(2503,392,2336,2503,'','y','Задача',0,'','all','','0','','none','','normal',0,0),(2504,392,2338,2504,'','y','Параметры',0,'','all','','0','','none','','normal',0,0),(2520,392,2354,2520,'','y','Размер',2518,'','all','','0','','none','','normal',0,0),(2521,392,2355,2521,'','y','Применение',0,'','all','','0','','none','','normal',0,0),(2522,392,2356,2522,'','y','Статус',2521,'','all','','0','','none','','normal',0,0),(2523,392,2357,2523,'','y','Размер',2521,'','all','','0','','none','','normal',0,0),(2548,393,2367,2548,'','y','Процессинг',0,'','all','','0','','none','','normal',0,0),(2546,393,2365,2546,'','y','Статус',2545,'','all','','0','','none','','normal',0,0),(2547,393,2366,2547,'','y','Размер',2545,'','all','','0','','sum','','normal',0,0),(2543,393,2362,2543,'','y','Статус',2542,'','all','','0','','none','','normal',0,0),(2544,393,2363,2544,'','y','Размер',2542,'','all','','0','','sum','','normal',0,0),(2545,393,2364,2545,'','y','Создание',0,'','all','','0','','none','','normal',0,0),(2541,393,2360,2541,'','y','Условие выборки',0,'','all','','0','','none','','normal',0,0),(2542,393,2361,2542,'','y','Оценка',0,'','all','','0','','none','','normal',0,0),(2538,393,0,2538,'','n','',0,'','all','','0','entityId','none','','normal',0,0),(2539,393,0,2539,'','n','',0,'','all','','0','fieldId','none','','normal',0,0),(2540,393,2359,2540,'','y','Расположение',0,'','all','','0','','none','','normal',0,0),(2549,393,2368,2549,'','y','Статус',2548,'','all','','0','','none','','normal',0,0),(2550,393,2369,2550,'','y','Размер',2548,'','all','','0','','sum','','normal',0,0),(2551,393,2370,2551,'','y','Применение',0,'','all','','0','','none','','normal',0,0),(2552,393,2371,2552,'','y','Статус',2551,'','all','','0','','none','','normal',0,0),(2553,393,2372,2553,'','y','Размер',2551,'','all','','0','','sum','','normal',0,0),(2561,394,2378,2561,'','y','Статус',0,'','all','','0','','none','','normal',0,0),(2560,394,2377,2560,'','y','Результат',0,'','all','','1','','none','','normal',0,0),(2559,394,2376,2559,'','y','Значение',0,'','all','','0','','none','','normal',0,0),(2558,394,2375,2558,'','y','Таргет',0,'','all','','0','','none','','normal',0,0),(2562,393,2383,2562,'','y','Байт',2545,'','all','','0','','sum','','normal',0,0),(2563,13,2384,33,'','y','Тип',0,'','all','','0','','none','','normal',0,0),(2564,8,2386,24,'','y','Мультиязычность',0,'','all','','0','','none','','normal',0,0);
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
) ENGINE=MyISAM AUTO_INCREMENT=164 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param`
--

LOCK TABLES `param` WRITE;
/*!40000 ALTER TABLE `param` DISABLE KEYS */;
INSERT INTO `param` VALUES (90,1487,13,'/css/style.css','Путь к css-нику для подцепки редактором'),(134,1814,5,'true','Во всю ширину'),(102,1487,5,'true','Во всю ширину'),(127,109,4,'px','Единица измерения'),(128,110,4,'px','Единица измерения'),(160,19,21,'system','Группировка опций по столбцу'),(156,18,21,'type','Группировка опций по столбцу'),(162,2315,4,'px','Единица измерения'),(159,12,21,'system','Группировка опций по столбцу'),(161,2196,21,'group','Группировка опций по столбцу'),(163,2385,29,'Без изменений','Плейсхолдер');
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
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
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
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `entityId` int(11) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,'Конфигуратор','y',11,'',1,15,'n','s'),(12,'Администратор','y',11,'',12,15,'n','p'),(17,'Пользователь','y',130,'',17,15,'n','p');
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queuechunk`
--

LOCK TABLES `queuechunk` WRITE;
/*!40000 ALTER TABLE `queuechunk` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queueitem`
--

LOCK TABLES `queueitem` WRITE;
/*!40000 ALTER TABLE `queueitem` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queuetask`
--

LOCK TABLES `queuetask` WRITE;
/*!40000 ALTER TABLE `queuetask` DISABLE KEYS */;
/*!40000 ALTER TABLE `queuetask` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search`
--

LOCK TABLES `search` WRITE;
/*!40000 ALTER TABLE `search` DISABLE KEYS */;
INSERT INTO `search` VALUES (13,5,612,13,'y','','Тип','','',1,1,'all','',1,0,''),(54,146,691,55,'y','','Дата регистрации','','',1,1,'all','',1,0,''),(55,146,689,56,'y','','Пол','','',1,1,'all','',1,0,''),(114,5,1441,114,'y','','Включить в кэш','','',1,1,'all','',1,0,''),(116,7,1366,116,'y','','Тип','','',1,1,'all','',1,0,''),(117,7,19,117,'y','','Сущность','','',1,1,'all','',1,0,''),(119,7,2303,119,'y','','Доступ','','',1,1,'all','',1,0,''),(118,7,22,118,'y','','Статус','','',1,1,'all','',1,0,''),(120,391,6,120,'y','Сущность','Сущность, в структуру которой входит это поле','','',1,1,'all','',1,0,''),(121,391,2197,121,'y','','Режим','','',1,1,'all','',1,0,''),(122,391,12,122,'y','Ключи','Ключи какой сущности будут храниться в этом поле','','',1,1,'all','',1,0,''),(123,391,10,123,'y','Элемент','Элемент управления','','',1,1,'all','',1,0,''),(125,387,2325,125,'y','','Состояние','','',1,1,'all','',1,0,''),(126,387,2238,126,'y','','Статус','','',1,1,'all','',1,0,''),(127,394,2378,127,'y','','Статус','','',1,1,'all','',1,0,'');
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
  `disableAdd` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('s','p','o') NOT NULL DEFAULT 'p',
  `parentSectionConnector` int(11) NOT NULL DEFAULT '0',
  `groupBy` int(11) NOT NULL DEFAULT '0',
  `rowsetSeparate` enum('auto','yes','no') NOT NULL DEFAULT 'auto',
  `expand` enum('all','only','except','none') NOT NULL DEFAULT 'all',
  `expandRoles` varchar(255) NOT NULL DEFAULT '',
  `roleIds` varchar(255) NOT NULL DEFAULT '',
  `extendsJs` varchar(255) NOT NULL DEFAULT 'Indi.lib.controller.Controller',
  `rownumberer` tinyint(1) NOT NULL DEFAULT '0',
  `multiSelect` tinyint(1) NOT NULL DEFAULT '0',
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
  KEY `tileThumb` (`tileThumb`)
) ENGINE=MyISAM AUTO_INCREMENT=395 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (1,0,0,'Конфигурация','configuration','y',367,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','','Indi.lib.controller.Controller',0,0,0,0),(2,1,1,'Столбцы','columnTypes','y',6,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(5,1,2,'Сущности','entities','y',4,25,'Indi_Controller_Admin',4,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(6,5,5,'Поля в структуре','fields','y',7,25,'Indi_Controller_Admin_Exportable',14,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(7,1,3,'Разделы','sections','y',2,25,'Indi_Controller_Admin_Exportable',23,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(8,7,8,'Действия','sectionActions','y',8,25,'Indi_Controller_Admin_Multinew',30,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(10,1,7,'Действия','actions','n',9,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(11,7,9,'Столбцы грида','grid','y',10,25,'Indi_Controller_Admin_Multinew',35,'ASC','',0,'s',0,2308,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(12,6,6,'Возможные значения','enumset','y',11,25,'Indi_Controller_Admin_Exportable',377,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(13,1,10,'Роли','profiles','y',5,25,'Indi_Controller_Admin',2132,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(14,13,11,'Пользователи','admins','y',13,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(16,1,4,'Элементы','controlElements','y',14,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(22,6,20,'Копии изображения','resize','y',19,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(29,0,0,'Контент','','n',113,25,'Indi_Controller_Admin',0,'ASC','',0,'o',0,0,'auto','all','','','Indi.lib.controller.Controller',0,0,0,0),(30,378,25,'Страницы','staticpages','n',316,25,'Indi_Controller_Admin',131,'ASC','',0,'o',0,0,'auto','all','','12,1,17','Indi.lib.controller.Controller',0,0,0,0),(100,16,90,'Возможные параметры настройки','possibleParams','y',90,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(101,6,91,'Параметры','params','y',91,25,'Indi_Controller_Admin_Exportable',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(112,0,0,'Фронтенд','','y',371,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,0,'auto','all','','','Indi.lib.controller.Controller',0,0,0,0),(113,112,101,'Разделы','fsections','y',104,25,'Indi_Controller_Admin',585,'ASC','<?=$_SESSION[\'admin\'][\'profileId\']==1?\'1\':\'`toggle`=\"y\"\'?>',0,'s',0,0,'auto','all','','1,2','Indi.lib.controller.Controller',0,0,0,0),(143,0,0,'Обратная связь','','n',358,25,'Indi_Controller_Admin',0,'ASC','',0,'o',0,0,'auto','all','','','Indi.lib.controller.Controller',0,0,0,0),(144,143,128,'Фидбэк','feedback','n',135,25,'Indi_Controller_Admin',681,'DESC','',0,'o',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller',0,0,0,0),(145,143,129,'Подписчики','subscribers','n',165,25,'Indi_Controller_Admin',682,'ASC','',0,'o',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller',0,0,0,0),(146,143,130,'Пользователи','users','n',225,25,'Indi_Controller_Admin',685,'DESC','',0,'o',0,0,'auto','all','','12,1','Indi.lib.controller.Controller',0,0,0,0),(172,112,146,'Действия','factions','y',185,25,'Indi_Controller_Admin',857,'ASC','',0,'s',0,0,'auto','all','','1,2,4','Indi.lib.controller.Controller',0,0,0,0),(173,113,147,'Действия','fsection2factions','y',161,25,'Indi_Controller_Admin',860,'ASC','',0,'s',0,0,'auto','all','','1,2','Indi.lib.controller.Controller',0,0,0,0),(189,143,160,'Посетители','visitors','n',176,25,'Indi_Controller_Admin',1101,'DESC','',0,'o',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(191,173,162,'Компоненты SEO-урла','seoUrl','y',178,25,'Indi_Controller_Admin',1195,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(201,7,171,'Измененные поля','alteredFields','y',188,25,'Indi_Controller_Admin_Multinew',1342,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(224,7,195,'Фильтры','search','y',192,25,'Indi_Controller_Admin_Multinew',1444,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(229,29,205,'Меню','menu','n',134,25,'Indi_Controller_Admin',1496,'ASC','',1,'p',0,0,'auto','all','','1,2,9,12','Indi.lib.controller.Controller',0,0,0,0),(232,378,204,'Элементы','staticblocks','n',232,25,'Indi_Controller_Admin',1485,'ASC','',0,'o',0,0,'auto','all','','12,1','Indi.lib.controller.Controller',0,0,0,0),(379,173,301,'Компоненты meta-тегов','metatitles','y',379,25,'Indi_Controller_Admin_Meta',2181,'ASC','',0,'o',0,2172,'yes','all','','1,12','Indi.lib.controller.Controller',0,0,0,0),(378,0,0,'Статика','','n',144,30,'Indi_Controller_Admin',0,'ASC','',0,'p',0,0,'auto','all','','','Indi.lib.controller.Controller',0,0,0,0),(389,1,309,'Уведомления','notices','y',389,25,'Indi_Controller_Admin',2254,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(390,389,310,'Получатели','noticeGetters','y',390,25,'Indi_Controller_Admin',2275,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(387,1,307,'Языки','lang','y',387,25,'Indi_Controller_Admin',0,'ASC','',0,'s',0,2325,'auto','all','','1','Indi.lib.controller.Controller',0,1,0,0),(388,6,308,'Зависимости','consider','y',388,25,'Indi_Controller_Admin_Exportable',0,'ASC','',0,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(391,1,5,'Все поля','fieldsAll','y',391,25,'Indi_Controller_Admin',0,'ASC','',1,'s',0,6,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0),(392,1,314,'Очереди задач','queueTask','y',392,25,'Indi_Controller_Admin',2337,'DESC','',1,'s',0,2382,'auto','all','','1','Indi.lib.controller.Controller',0,1,0,0),(393,392,315,'Сегменты очереди','queueChunk','y',393,25,'Indi_Controller_Admin',2379,'ASC','',1,'s',0,2381,'no','all','','1','Indi.lib.controller.Controller',1,0,0,0),(394,393,316,'Элементы очереди','queueItem','y',394,25,'Indi_Controller_Admin',0,'ASC','',1,'s',0,0,'auto','all','','1','Indi.lib.controller.Controller',0,0,0,0);
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
) ENGINE=MyISAM AUTO_INCREMENT=1604 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section2action`
--

LOCK TABLES `section2action` WRITE;
/*!40000 ALTER TABLE `section2action` DISABLE KEYS */;
INSERT INTO `section2action` VALUES (1,2,1,'y',1,'1','Список','','auto','auto','n'),(2,2,2,'y',2,'1','Детали','','auto','auto','n'),(3,2,3,'y',3,'1','Сохранить','','auto','auto','n'),(5,2,4,'n',5,'1','Удалить','','auto','auto','n'),(6,5,1,'y',6,'1','Список','','auto','auto','n'),(7,5,2,'y',7,'1','Детали','','auto','auto','n'),(8,5,3,'y',8,'1','Сохранить','','auto','auto','n'),(9,5,4,'y',9,'1','Удалить','','auto','auto','n'),(10,6,1,'y',10,'1','Список','','auto','auto','n'),(11,6,2,'y',11,'1','Детали','','auto','auto','n'),(12,6,3,'y',12,'1','Сохранить','','auto','auto','n'),(13,6,4,'y',13,'1','Удалить','','auto','auto','n'),(14,7,1,'y',14,'1','Список','','auto','auto','n'),(15,7,2,'y',15,'1','Детали','','auto','auto','n'),(16,7,3,'y',16,'1','Сохранить','','auto','auto','n'),(17,7,4,'y',17,'1','Удалить','','auto','auto','n'),(18,6,5,'y',18,'1','Выше','','auto','auto','n'),(19,6,6,'y',19,'1','Ниже','','auto','auto','n'),(20,8,1,'y',20,'1','Список','','auto','auto','n'),(21,8,2,'y',21,'1','Детали','','auto','auto','n'),(22,8,3,'y',22,'1','Сохранить','','auto','auto','n'),(23,8,4,'y',23,'1','Удалить','','auto','auto','n'),(24,8,5,'y',24,'1','Выше','','auto','auto','n'),(25,8,6,'y',25,'1','Ниже','','auto','auto','n'),(26,7,5,'y',26,'1','Выше','','auto','auto','n'),(27,7,6,'y',27,'1','Ниже','','auto','auto','n'),(28,10,1,'y',28,'1','Список','','auto','auto','n'),(29,10,2,'y',29,'1','Детали','','auto','auto','n'),(30,10,3,'y',30,'1','Сохранить','','auto','auto','n'),(32,11,1,'y',31,'1','Список','','auto','auto','n'),(33,11,2,'y',32,'1','Детали','','auto','auto','n'),(34,11,3,'y',33,'1','Сохранить','','auto','auto','n'),(35,11,4,'y',34,'1','Удалить','','auto','auto','n'),(36,11,5,'y',35,'1','Выше','','auto','auto','n'),(37,11,6,'y',36,'1','Ниже','','auto','auto','n'),(38,13,1,'y',37,'1','Список','','auto','auto','n'),(39,13,2,'y',38,'1','Детали','','auto','n','n'),(40,13,3,'y',39,'1','Сохранить','','auto','auto','n'),(41,12,1,'y',40,'1','Список','','auto','auto','n'),(42,12,2,'y',41,'1','Детали','','auto','auto','n'),(43,12,3,'y',42,'1','Сохранить','','auto','auto','n'),(44,12,4,'y',43,'1','Удалить','','auto','auto','n'),(45,14,1,'y',44,'1','Список','','auto','auto','n'),(46,14,2,'y',45,'1','Детали','','auto','auto','n'),(47,14,3,'y',46,'1','Сохранить','','auto','auto','n'),(48,14,4,'y',47,'1','Удалить','','auto','auto','n'),(52,16,1,'y',51,'1','Список','','auto','auto','n'),(53,16,2,'y',52,'1','Детали','','auto','auto','n'),(54,16,3,'y',53,'1','Сохранить','','auto','auto','n'),(67,7,7,'y',58,'1','Статус','','auto','auto','n'),(68,10,7,'y',59,'1','Статус','','auto','auto','n'),(69,11,7,'y',60,'1','Статус','','auto','auto','n'),(74,22,1,'y',65,'1','Список','','auto','auto','n'),(75,22,2,'y',66,'1','Детали','','auto','auto','n'),(76,22,3,'y',67,'1','Сохранить','','auto','auto','n'),(77,22,4,'y',68,'1','Удалить','','auto','auto','n'),(99,30,1,'y',69,'12,1,17','Список','','auto','auto','n'),(100,30,2,'y',70,'12,1,17','Детали','','auto','auto','n'),(101,30,3,'y',71,'12,1,17','Сохранить','','auto','auto','n'),(102,30,4,'y',72,'12,1','Удалить','','auto','auto','n'),(103,30,7,'y',73,'12,1','Статус','','auto','auto','n'),(329,12,5,'y',299,'1','Выше','','auto','auto','n'),(330,12,6,'y',300,'1','Ниже','','auto','auto','n'),(373,100,1,'y',343,'1','Список','','auto','auto','n'),(374,100,2,'y',344,'1','Детали','','auto','auto','n'),(375,100,3,'y',345,'1','Сохранить','','auto','auto','n'),(376,100,4,'y',346,'1','Удалить','','auto','auto','n'),(377,101,1,'y',347,'1','Список','','auto','auto','n'),(378,101,2,'y',348,'1','Детали','','auto','auto','n'),(379,101,3,'y',349,'1','Сохранить','','auto','auto','n'),(380,101,4,'y',350,'1','Удалить','','auto','auto','n'),(808,13,4,'y',589,'1','Удалить','','auto','auto','n'),(806,10,4,'y',587,'1','Удалить','','auto','auto','n'),(429,113,1,'y',399,'1,2','Список','','auto','auto','n'),(430,113,2,'y',400,'1','Детали','','auto','auto','n'),(431,113,3,'y',401,'1','Сохранить','','auto','auto','n'),(432,113,4,'y',402,'1','Удалить','','auto','auto','n'),(1593,387,6,'y',1593,'1','Ниже','','auto','auto','n'),(532,144,1,'y',133,'1,2,4','Список','','auto','auto','n'),(533,144,2,'y',134,'1,2,4','Детали','','auto','auto','n'),(534,144,3,'y',135,'1,2','Сохранить','','auto','auto','n'),(535,144,4,'y',136,'1,2','Удалить','','auto','auto','n'),(536,145,1,'y',134,'1,2,4','Список','','auto','auto','n'),(537,145,2,'y',135,'1,2,4','Детали','','auto','auto','n'),(538,145,3,'y',136,'1,2','Сохранить','','auto','auto','n'),(539,145,4,'y',137,'1,2','Удалить','','auto','auto','n'),(540,146,1,'y',135,'12,1','Список','','auto','auto','n'),(541,146,2,'y',136,'12,1','Детали','','auto','auto','n'),(542,146,3,'y',137,'12,1','Сохранить','','auto','auto','n'),(543,146,4,'y',138,'12,1','Удалить','','auto','auto','n'),(833,16,4,'n',598,'1','Удалить','','auto','auto','n'),(875,224,1,'y',608,'1','Список','','auto','auto','n'),(876,224,2,'y',609,'1','Детали','','auto','auto','n'),(877,224,4,'y',611,'1','Удалить','','auto','auto','n'),(878,224,3,'y',610,'1','Сохранить','','auto','auto','n'),(879,224,5,'y',612,'1','Выше','','auto','auto','n'),(880,224,6,'y',613,'1','Ниже','','auto','auto','n'),(881,224,7,'y',614,'1','Статус','','auto','auto','n'),(642,172,1,'y',161,'1','Список','','auto','auto','n'),(643,172,2,'y',162,'1,2,4','Детали','','auto','auto','n'),(644,172,3,'y',163,'1,2','Сохранить','','auto','auto','n'),(645,172,4,'y',164,'1,2','Удалить','','auto','auto','n'),(646,173,1,'y',162,'1,2','Список','','auto','auto','n'),(647,173,2,'y',163,'1','Детали','','auto','auto','n'),(648,173,3,'y',164,'1','Сохранить','','auto','auto','n'),(649,173,4,'y',165,'1','Удалить','','auto','auto','n'),(730,189,1,'y',524,'1','Список','','auto','auto','n'),(731,189,2,'y',525,'1','Детали','','auto','auto','n'),(732,189,3,'y',526,'1','Сохранить','','auto','auto','n'),(733,189,4,'y',527,'1','Удалить','','auto','auto','n'),(740,191,1,'y',534,'1','Список','','auto','auto','n'),(741,191,2,'y',535,'1','Детали','','auto','auto','n'),(742,191,3,'y',536,'1','Сохранить','','auto','auto','n'),(743,191,4,'y',537,'1','Удалить','','auto','auto','n'),(744,191,5,'y',538,'1','Выше','','auto','auto','n'),(745,191,6,'y',539,'1','Ниже','','auto','auto','n'),(1548,7,34,'y',1548,'1','PHP','','auto','auto','n'),(1547,5,34,'y',1559,'1','PHP','','auto','auto','n'),(1592,387,5,'y',1592,'1','Выше','','auto','auto','n'),(1528,379,6,'y',1528,'1,12','Ниже','','auto','auto','n'),(789,201,1,'y',583,'1','Список','','auto','auto','n'),(790,201,2,'y',584,'1','Детали','','auto','auto','n'),(791,201,3,'y',585,'1','Сохранить','','auto','auto','n'),(792,201,4,'y',586,'1','Удалить','','auto','auto','n'),(898,229,1,'y',631,'1,2,9','Список','','auto','auto','n'),(899,229,2,'y',632,'1,2,9','Детали','','auto','auto','n'),(900,229,3,'y',633,'1,2,9','Сохранить','','auto','auto','n'),(901,229,4,'y',634,'1,2,9','Удалить','','auto','auto','n'),(910,232,1,'y',643,'12,1','Список','','auto','auto','n'),(911,232,2,'y',644,'1,12','Детали','','auto','auto','n'),(912,232,3,'y',645,'1,12','Сохранить','','auto','auto','n'),(913,232,4,'y',646,'1','Удалить','','auto','auto','n'),(921,229,5,'y',654,'1,2,9','Выше','','auto','auto','n'),(922,229,6,'y',655,'1,2,9','Ниже','','auto','auto','n'),(933,229,7,'y',666,'1,2,9','Статус','','auto','auto','n'),(1527,379,5,'y',1527,'1,12','Выше','','auto','auto','n'),(939,232,7,'y',672,'12,1','Статус','','auto','auto','n'),(946,113,7,'y',679,'1','Статус','','auto','auto','n'),(947,113,5,'y',680,'1,2','Выше','','auto','auto','n'),(948,113,6,'y',681,'1,2','Ниже','','auto','auto','n'),(949,113,19,'y',682,'1,2','Обновить sitemap.xml','','auto','auto','n'),(1268,8,7,'y',1268,'1','Статус','','auto','auto','n'),(1295,13,5,'y',1291,'1','Выше','','auto','auto','n'),(1296,13,6,'y',1292,'1','Ниже','','auto','auto','n'),(1522,14,7,'y',1293,'1','Статус','','auto','auto','n'),(1545,13,7,'y',1545,'1','Статус','','auto','auto','n'),(1549,7,35,'y',1549,'1','JS','','auto','auto','n'),(1544,229,20,'y',1544,'12','Авторизация','','auto','auto','n'),(1526,379,4,'y',1526,'1,12','Удалить','','auto','auto','n'),(1525,379,3,'y',1525,'1,12','Сохранить','','auto','auto','n'),(1524,379,2,'y',1524,'1,12','Детали','','auto','auto','n'),(1523,379,1,'y',1523,'1,12','Список','','auto','n','n'),(1577,390,3,'y',1577,'1','Сохранить','','auto','auto','n'),(1576,390,2,'y',1576,'1','Детали','','auto','auto','n'),(1575,390,1,'y',1575,'1','Список','','auto','auto','n'),(1574,389,7,'y',1574,'1','Статус','','auto','auto','n'),(1570,389,1,'y',1570,'1','Список','','auto','auto','n'),(1571,389,2,'y',1571,'1','Детали','','auto','auto','n'),(1572,389,3,'y',1572,'1','Сохранить','','auto','auto','n'),(1573,389,4,'y',1573,'1','Удалить','','auto','auto','n'),(1559,5,7,'y',607,'1','Статус','','auto','auto','n'),(1560,387,1,'y',1560,'1','Список','','auto','auto','n'),(1561,387,2,'y',1561,'1','Детали','','auto','auto','n'),(1562,387,3,'y',1562,'1','Сохранить','','auto','auto','n'),(1563,387,4,'y',1563,'1','Удалить','','auto','auto','n'),(1564,388,1,'y',1564,'1','Список','','auto','auto','n'),(1565,388,2,'y',1565,'1','Детали','','auto','auto','n'),(1566,388,3,'y',1566,'1','Сохранить','','auto','auto','n'),(1567,388,4,'y',1567,'1','Удалить','','auto','auto','n'),(1568,7,36,'y',1568,'1','Экспорт','','auto','auto','n'),(1569,5,36,'y',1569,'1','Экспорт','','auto','auto','n'),(1578,390,4,'y',1578,'1','Удалить','','auto','auto','n'),(1579,391,1,'y',1579,'1','Список','','auto','auto','n'),(1580,391,2,'y',1580,'1','Детали','','auto','auto','n'),(1581,391,3,'y',1582,'1','Сохранить','','auto','auto','n'),(1582,6,39,'y',1581,'1','Активировать','Выбрать режим','auto','auto','n'),(1583,8,36,'y',1583,'1','Экспорт','','auto','auto','n'),(1584,11,36,'y',1584,'1','Экспорт','','auto','auto','n'),(1585,201,36,'y',1585,'1','Экспорт','','auto','auto','n'),(1586,224,36,'y',1586,'1','Экспорт','','auto','auto','n'),(1587,6,36,'y',1587,'1','Экспорт','','auto','auto','n'),(1588,12,36,'y',1588,'1','Экспорт','','auto','auto','n'),(1589,22,36,'y',1589,'1','Экспорт','','auto','auto','n'),(1590,101,36,'y',1590,'1','Экспорт','','auto','auto','n'),(1591,388,36,'y',1591,'1','Экспорт','','auto','auto','n'),(1594,387,40,'y',1594,'1','Доступные языки','','auto','auto','n'),(1595,392,1,'y',1595,'1','Список','','auto','auto','n'),(1596,392,2,'y',1596,'1','Детали','','auto','auto','n'),(1597,392,4,'y',1597,'1','Удалить','','auto','auto','n'),(1598,392,41,'y',1598,'1','Запустить','','auto','auto','n'),(1599,393,1,'y',1599,'1','Список','','auto','auto','n'),(1600,393,2,'y',1600,'1','Детали','','auto','auto','n'),(1601,394,1,'y',1601,'1','Список','','auto','auto','n'),(1602,394,3,'y',1602,'1','Сохранить','','auto','auto','n'),(1603,387,43,'y',1603,'1','Вординги','','auto','auto','n');
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
-- Table structure for table `subscriber`
--

DROP TABLE IF EXISTS `subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber`
--

LOCK TABLES `subscriber` WRITE;
/*!40000 ALTER TABLE `subscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber` ENABLE KEYS */;
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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (109,'asd@asd.asd','Первонах1','','n','0000-00-00','0000-00-00',0,'2014-09-24 07:59:38',0,'','0','n','0000-00-00 00:00:00','');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor`
--

DROP TABLE IF EXISTS `visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `lastActivity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=555197 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor`
--

LOCK TABLES `visitor` WRITE;
/*!40000 ALTER TABLE `visitor` DISABLE KEYS */;
INSERT INTO `visitor` VALUES (555196,'3v0s0pvgep3itif9nq82eif0h0','2014-02-03 19:15:02',0);
/*!40000 ALTER TABLE `visitor` ENABLE KEYS */;
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

-- Dump completed on 2020-09-16  0:40:28

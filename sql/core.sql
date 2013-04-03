-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 26 2013 г., 21:52
-- Версия сервера: 5.6.9-rc-log
-- Версия PHP: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `empty`
--

-- --------------------------------------------------------

--
-- Структура таблицы `action`
--

DROP TABLE IF EXISTS `action`;
CREATE TABLE IF NOT EXISTS `action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `condition` text NOT NULL,
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `display` enum('y','n') NOT NULL DEFAULT 'y',
  `rowRequired` enum('y','n') NOT NULL DEFAULT 'y',
  `javascript` text NOT NULL,
  `type` enum('p','s','o') NOT NULL DEFAULT 'p',
  PRIMARY KEY (`id`),
  KEY `rowRequired` (`rowRequired`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Дамп данных таблицы `action`
--

INSERT INTO `action` (`id`, `title`, `alias`, `condition`, `toggle`, `display`, `rowRequired`, `javascript`, `type`) VALUES
(1, 'Список', 'index', 'true', 'y', 'n', 'y', '', 's'),
(2, 'Детали', 'form', 'true', 'y', 'y', 'y', '', 's'),
(3, 'Сохранить', 'save', 'true', 'y', 'n', 'y', '', 's'),
(4, 'Удалить', 'delete', 'true', 'y', 'y', 'y', 'Ext.MessageBox.confirm(''Confirm'', ''Вы уверены?'', function(btn){\r\n  if (btn == ''yes'') loadContent(url);\r\n});\r\nreturn false;', 's'),
(5, 'Выше', 'up', 'true', 'y', 'y', 'y', '', 's'),
(6, 'Ниже', 'down', 'true', 'y', 'y', 'y', '', 's'),
(7, 'Статус', 'toggle', 'true', 'y', 'y', 'y', '', 's'),
(18, 'Обновить кэш', 'cache', 'true', 'y', 'y', 'y', '', 's'),
(19, 'Обновить sitemap.xml', 'sitemap', 'true', 'y', 'y', 'n', '$.post(''/sitemap/index/update'',function(response){\r\n  Ext.MessageBox.show({\r\n    title: ''Сообщение'',\r\n    msg: response,\r\n    buttons: Ext.MessageBox.OK,\r\n    icon: Ext.MessageBox.INFO\r\n  });\r\n});\r\nreturn false;', 's');

-- --------------------------------------------------------

--
-- Структура таблицы `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profileId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `profileId` (`profileId`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `admin`
--

INSERT INTO `admin` (`id`, `profileId`, `title`, `email`, `password`, `toggle`) VALUES
(1, 1, 'Павел Перминов', 'pavel.perminov.23@gmail.com', 'vt6yhg5', 'y'),
(10, 10, 'Иванов Иван', 'demo', 'demo', 'y'),
(3, 2, 'Наталья', 'info@it-pub.ru', 'sole6dhf', 'y'),
(9, 9, 'Василий Теркин', 'vasily.terkin@gmail.com', 'medal', 'y');

-- --------------------------------------------------------

--
-- Структура таблицы `columntype`
--

DROP TABLE IF EXISTS `columntype`;
CREATE TABLE IF NOT EXISTS `columntype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `canStoreRelation` enum('y','n') NOT NULL DEFAULT 'n',
  `elementId` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `canStoreRelation` (`canStoreRelation`),
  KEY `elementId` (`elementId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `columntype`
--

INSERT INTO `columntype` (`id`, `title`, `type`, `canStoreRelation`, `elementId`) VALUES
(1, 'Строка', 'VARCHAR(255)', 'y', '1,7,8,22'),
(3, 'Число', 'INT(11)', 'y', '1,3,4,5,18,21'),
(4, 'Текст', 'TEXT', 'y', '6,7,8,13'),
(5, 'Цена', 'DOUBLE(7,2)', 'n', '10'),
(6, 'Дата', 'DATE', 'n', '12'),
(7, 'Год', 'YEAR', 'n', ''),
(8, 'Время', 'TIME', 'n', '17'),
(9, 'Момент', 'DATETIME', 'n', '19'),
(10, 'Одно значение из набора', 'ENUM', 'n', '3,5'),
(11, 'Набор значений', 'SET', 'n', '1,6,7,8'),
(12, 'Правда/Ложь', 'BOOLEAN', 'n', '9'),
(13, 'Цвет', 'VARCHAR(7)', 'n', '11');

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `title`, `alias`, `value`) VALUES
(1, 'Проект', 'project', 'Indi Engine');

-- --------------------------------------------------------

--
-- Структура таблицы `dependentcount`
--

DROP TABLE IF EXISTS `dependentcount`;
CREATE TABLE IF NOT EXISTS `dependentcount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `where` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `sectionId` (`sectionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Дамп данных таблицы `dependentcount`
--

INSERT INTO `dependentcount` (`id`, `fsectionId`, `sectionId`, `title`, `alias`, `fsection2factionId`, `where`) VALUES
(10, 8, 149, 'Сообщения', 'messages', 3, ''),
(11, 26, 155, 'Новые сообщения', 'messages', 30, '`read`=''n''');

-- --------------------------------------------------------

--
-- Структура таблицы `dependentcountfordependentrowset`
--

DROP TABLE IF EXISTS `dependentcountfordependentrowset`;
CREATE TABLE IF NOT EXISTS `dependentcountfordependentrowset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `dependentRowsetId` int(11) NOT NULL DEFAULT '0',
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `where` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dependentrowset`
--

DROP TABLE IF EXISTS `dependentrowset`;
CREATE TABLE IF NOT EXISTS `dependentrowset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `returnAs` enum('o','a') NOT NULL DEFAULT 'o',
  `limit` int(11) NOT NULL DEFAULT '0',
  `orderBy` enum('c','e') NOT NULL DEFAULT 'c',
  `orderColumn` int(11) NOT NULL DEFAULT '0',
  `orderDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `orderExpression` varchar(255) NOT NULL,
  `where` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `entityId` (`entityId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Дамп данных таблицы `dependentrowset`
--

INSERT INTO `dependentrowset` (`id`, `fsectionId`, `entityId`, `title`, `alias`, `fsection2factionId`, `returnAs`, `limit`, `orderBy`, `orderColumn`, `orderDirection`, `orderExpression`, `where`) VALUES
(3, 8, 121, 'Классный отдых', 'holiday', 3, 'o', 0, 'c', 0, 'ASC', '', ''),
(4, 8, 136, 'Любимые места', 'favoritePlaces', 3, 'o', 0, 'c', 0, 'ASC', '', ''),
(73, 37, 203, 'Блоки', 'blocks', 84, 'o', 0, 'c', 1484, 'ASC', '', '`toggle`="y"'),
(75, 41, 147, 'Действия', 'actions', 89, 'o', 0, 'c', 0, 'ASC', '', '`type`="r"');

-- --------------------------------------------------------

--
-- Структура таблицы `disabledfield`
--

DROP TABLE IF EXISTS `disabledfield`;
CREATE TABLE IF NOT EXISTS `disabledfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `defaultValue` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Дамп данных таблицы `disabledfield`
--

INSERT INTO `disabledfield` (`id`, `sectionId`, `fieldId`, `defaultValue`) VALUES
(32, 233, 38, '9');

-- --------------------------------------------------------

--
-- Структура таблицы `element`
--

DROP TABLE IF EXISTS `element`;
CREATE TABLE IF NOT EXISTS `element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `storeRelationAbility` set('none','one','many') NOT NULL DEFAULT 'none',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `storeRelationAbility` (`storeRelationAbility`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Дамп данных таблицы `element`
--

INSERT INTO `element` (`id`, `title`, `alias`, `storeRelationAbility`, `hidden`) VALUES
(1, 'Однострочное текстовое поле', 'string', 'none,many', 0),
(3, 'Выпадающий список', 'select', 'one', 0),
(4, 'Приоритет отображения', 'move', 'none', 1),
(5, 'Набор радио-кнопок', 'radio', 'one', 0),
(6, 'Многострочное текстовое поле', 'textarea', 'none,many', 0),
(7, 'Набор чекбоксов', 'multicheck', 'many', 0),
(8, 'Многострочный выпадающий список', 'multiselect', 'many', 0),
(9, 'Чекбокс', 'check', 'none', 0),
(10, 'Цена', 'price', 'none', 0),
(11, 'Выбор цвета', 'color', 'none', 0),
(12, 'Календарь', 'calendar', 'none', 0),
(13, 'HTML-редактор', 'html', 'none', 0),
(14, 'Загрузка файла', 'upload', 'none', 0),
(15, 'Размер в пикселях', 'dimension', 'none', 0),
(16, 'Группа полей', 'span', 'none', 0),
(17, 'Время', 'time', 'none', 0),
(18, 'Число', 'number', 'none,one', 0),
(19, 'Момент', 'datetime', 'none', 0),
(20, 'Автокомплит', 'autocomplete', 'one', 0),
(21, 'Динамический выпадающий список', 'dselect', 'one', 0),
(22, 'Скрытое поле', 'hidden', 'none', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `entity`
--

DROP TABLE IF EXISTS `entity`;
CREATE TABLE IF NOT EXISTS `entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `extends` varchar(255) NOT NULL DEFAULT 'Indi_Db_Table',
  `system` enum('n','y','o') NOT NULL DEFAULT 'n',
  `useCache` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `system` (`system`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=209 ;

--
-- Дамп данных таблицы `entity`
--

INSERT INTO `entity` (`id`, `title`, `table`, `extends`, `system`, `useCache`) VALUES
(1, 'Тип столбца', 'columnType', 'Indi_Db_Table', 'y', 0),
(2, 'Сущность', 'entity', 'Indi_Db_Table', 'y', 1),
(3, 'Раздел', 'section', 'Indi_Db_Table', 'y', 0),
(4, 'Элемент управления', 'element', 'Indi_Db_Table', 'y', 0),
(5, 'Поле', 'field', 'Indi_Db_Table', 'y', 1),
(6, 'Значение из набора', 'enumset', 'Indi_Db_Table', 'y', 0),
(7, 'Действие', 'action', 'Indi_Db_Table', 'y', 0),
(8, 'Действие в разделе', 'section2action', 'Indi_Db_Table', 'y', 0),
(9, 'Столбец грида', 'grid', 'Indi_Db_Table', 'y', 0),
(10, 'Профиль', 'profile', 'Indi_Db_Table', 'y', 0),
(11, 'Пользователь CMS', 'admin', 'Indi_Db_Table', 'y', 0),
(20, 'Копия', 'resize', 'Indi_Db_Table', 'y', 0),
(25, 'Статическая страница', 'staticpage', 'Indi_Db_Table', 'o', 0),
(90, 'Параметр настройки элемента управления', 'possibleElementParam', 'Indi_Db_Table', 'y', 0),
(91, 'Параметр настройки элемента управления, в контексте поля сущности', 'param', 'Indi_Db_Table', 'y', 0),
(94, 'Параметр настройки', 'config', 'Indi_Db_Table', 'y', 0),
(101, 'Раздел фронтенда', 'fsection', 'Indi_Db_Table', 'y', 1),
(102, 'Количества строк на странице', 'rpp', 'Indi_Db_Table', 'y', 0),
(103, 'Фильтр', 'filter', 'Indi_Db_Table', 'y', 0),
(104, 'Сортировать по', 'orderBy', 'Indi_Db_Table', 'y', 1),
(108, 'Зависимое количество', 'dependentCount', 'Indi_Db_Table', 'y', 1),
(110, 'Джойн по внешнему ключу', 'joinFk', 'Indi_Db_Table', 'y', 1),
(111, 'Зависимое множество', 'dependentRowset', 'Indi_Db_Table', 'y', 1),
(195, 'Поле, доступное для поиска', 'search', 'Indi_Db_Table', 'y', 0),
(128, 'Фидбэк', 'feedback', 'Indi_Db_Table', 'o', 0),
(129, 'Подписчик', 'subscriber', 'Indi_Db_Table', 'o', 0),
(130, 'Пользователь', 'user', 'Indi_Db_Table', 'o', 0),
(146, 'Действие, возможное для использования в разделе фронтенда', 'faction', 'Indi_Db_Table', 'y', 1),
(147, 'Действие в разделе фронтенда', 'fsection2faction', 'Indi_Db_Table', 'y', 1),
(155, 'Независимое множество', 'independentRowset', 'Indi_Db_Table', 'y', 1),
(156, 'Джойн по внешнему ключу для независимого множества', 'joinFkForIndependentRowset', 'Indi_Db_Table', 'y', 1),
(158, 'Джойн по внешнему ключу для зависимого множества', 'joinFkForDependentRowset', 'Indi_Db_Table', 'y', 1),
(159, 'Зависимое количество для каждого элемента зависимого множества', 'dependentCountForDependentRowset', 'Indi_Db_Table', 'y', 1),
(160, 'Посетитель', 'visitor', 'Indi_Db_Table', 'o', 0),
(161, 'Конфиг фронтенда', 'fconfig', 'Indi_Db_Table', 'y', 1),
(162, 'Компонент SEO-урла', 'url', 'Indi_Db_Table', 'y', 0),
(164, 'Компонент &lt;title&gt;', 'seoTitle', 'Indi_Db_Table', 'y', 1),
(165, 'Компонент &lt;meta keywords&gt;', 'seoKeyword', 'Indi_Db_Table', 'y', 1),
(166, 'Компонент &lt;meta description&gt;', 'seoDescription', 'Indi_Db_Table', 'y', 1),
(168, 'Субдомен', 'subdomain', 'Indi_Db_Table', 'y', 0),
(171, 'Отключенное поле', 'disabledField', 'Indi_Db_Table', 'y', 0),
(204, 'Кусок', 'staticblock', 'Indi_Db_Table', 'o', 0),
(205, 'Пункт меню', 'menu', 'Indi_Db_Table', 'o', 0),
(207, 'Исключение из правил формирования meta-тегов', 'metaExclusion', 'Indi_Db_Table', 'y', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `enumset`
--

DROP TABLE IF EXISTS `enumset`;
CREATE TABLE IF NOT EXISTS `enumset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `javascript` text NOT NULL,
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=609 ;

--
-- Дамп данных таблицы `enumset`
--

INSERT INTO `enumset` (`id`, `fieldId`, `title`, `alias`, `javascript`, `move`) VALUES
(1, 3, 'Нет', 'n', '', 1),
(2, 3, 'Да', 'y', '', 2),
(5, 22, 'Включен', 'y', '', 5),
(6, 22, '<span style=''color: red''>Выключен</span>', 'n', '', 6),
(9, 29, 'Включено', 'y', '', 9),
(10, 29, '<span style=''color: red''>Выключено</span>', 'n', '', 10),
(11, 37, 'Включен', 'y', '', 11),
(12, 37, '<span style=''color: red''>Выключен</span>', 'n', '', 12),
(13, 42, 'Включен', 'y', '', 13),
(14, 42, '<span style=''color: red''>Выключен</span>', 'n', '', 14),
(62, 66, 'Нет', 'none', '', 62),
(63, 66, 'Только с одним значением ключа', 'one', '', 63),
(64, 66, 'С набором значений ключей', 'many', '', 64),
(87, 111, 'Поменять, но с сохранением пропорций', 'p', 'show(''tr-masterDimensionAlias,tr-masterDimensionValue,tr-slaveDimensionLimitation''); if(document.getElementById(''slaveDimensionLimitation'').value==0){ hide(''tr-slaveDimensionValue'');} else {show(''tr-slaveDimensionValue'');};', 87),
(88, 111, 'Поменять', 'c', 'hide(''tr-masterDimensionAlias,tr-slaveDimensionLimitation'');show(''tr-masterDimensionValue,tr-slaveDimensionValue'');$(''#masterDimensionAliasWidth'').click();', 88),
(89, 111, 'Не менять', 'o', 'hide(''tr-masterDimensionAlias,tr-masterDimensionValue,tr-slaveDimensionLimitation,tr-slaveDimensionValue'');', 89),
(91, 114, 'Ширины', 'width', '', 91),
(92, 114, 'Высоты', 'height', '', 92),
(95, 137, 'Включена', 'y', '', 95),
(96, 137, '<span style=''color: red''>Выключена</span>', 'n', '', 96),
(112, 345, 'Да', 'y', '', 112),
(113, 345, 'Нет', 'n', '', 113),
(122, 0, 'Всем друзьям, кроме указанных в разделе "Исключения из правил доступа на просмотр блога"', 'ae', '', 122),
(162, 455, 'Переменная сущность', 'e', '', 294),
(163, 455, 'Фильтрация', 'с', '', 148),
(181, 470, 'Нет', 'none', 'hide(''tr-relation,tr-filter,tr-satellitealias,tr-span,tr-satellite,tr-dependency,tr-alternative'');', 164),
(183, 470, 'Да, для энного количества значений ключей', 'many', 'show(''tr-relation,tr-filter,tr-satellitealias,tr-span,tr-satellite,tr-dependency,tr-alternative'');', 296),
(184, 470, 'Да, но для только одного значения ключа', 'one', 'show(''tr-relation,tr-filter,tr-satellitealias,tr-span,tr-satellite,tr-dependency,tr-alternative'');', 295),
(187, 455, 'Фильтрация (частный случай)', 'u', '', 165),
(213, 557, 'По возрастанию', 'ASC', '', 297),
(214, 557, 'По убыванию', 'DESC', '', 182),
(215, 567, 'Все возможные', 'a', '', 298),
(216, 567, 'Только уже используемые', 'u', '', 183),
(219, 594, 'Да', 'y', 'show(''tr-color'');', 299),
(220, 594, 'Нет', 'n', 'hide(''tr-color'');', 300),
(227, 612, 'Проектная', 'n', 'hide(''tr-useCache'');$(''#useCache1'').attr(''checked'', false);', 301),
(228, 612, '<span style=''color: red''>Системная</span>', 'y', 'show(''tr-useCache'');', 186),
(241, 689, 'Мужской', 'm', '', 427),
(242, 689, 'Женский', 'f', '', 309),
(572, 1365, '<font color=lime>Типовое</font>', 'o', '', 461),
(571, 1365, '<font color=red>Системное</font>', 's', '', 460),
(570, 1365, 'Проектное', 'p', '', 0),
(580, 1445, 'Включено', 'y', '', 0),
(581, 1445, '<span style=''color: red''>Выключено</span>', 'n', '', 464),
(538, 1260, 'Статический', 's', 'show(''tr-static'');hide(''tr-entityId,tr-fieldId,tr-where,tr-sibling'');', 0),
(537, 1252, 'Из sibling-компонента', 's', 'show(''tr-sibling'');', 442),
(533, 1247, 'Динамический', 'd', 'hide(''tr-static'');show(''tr-entityId,tr-fieldId,tr-where'');if($(''#whereS'').attr(''checked'')) show(''tr-sibling''); else hide(''tr-sibling'');', 441),
(532, 1247, 'Статический', 's', 'show(''tr-static'');hide(''tr-entityId,tr-fieldId,tr-where,tr-sibling'');', 0),
(536, 1252, 'Из контекста', 'c', 'hide(''tr-sibling'');', 0),
(531, 1243, 'Кастомному sql-выражению', 'e', 'hide(''tr-fieldId'');show(''tr-expression'');', 440),
(530, 1243, 'Полю прикрепленной к разделу сущности', 'f', 'show(''tr-fieldId'');hide(''tr-expression'');', 0),
(328, 0, 'Очень плохо', '1', '', 254),
(480, 1040, 'Одностроковый', 's', 'hide(''tr-orderColumn,tr-orderDirection,tr-filter,tr-rppId,tr-move,tr-defaultLimit,tr-orderBy,tr-orderExpression'');show(''tr-where,tr-index'');', 408),
(478, 1027, 'Для jQuery.post()', 'j', '', 407),
(479, 1040, 'Обычный', 'r', 'show(''tr-filter,tr-rppId,tr-move,tr-defaultLimit,tr-orderBy,tr-orderColumn,tr-orderDirection,tr-orderExpression'');hide(''tr-where,tr-index'');', 0),
(477, 1027, 'Обычное', 'r', '', 0),
(406, 940, 'Объекта', 'o', '', 0),
(407, 940, 'Массива', 'a', '', 356),
(445, 985, 'Одному из имеющихся столбцов', 'c', 'show(''tr-fieldId,tr-orderDirection'');hide(''tr-expression'');', 0),
(446, 985, 'Кастомное sql-выражение', 's', 'hide(''tr-fieldId,tr-orderDirection'');show(''tr-expression'');', 391),
(574, 1366, '<font color=red>Системный</font>', 's', '', 462),
(573, 1366, 'Проектный', 'p', '', 0),
(575, 1366, '<font color=lime>Типовой</font>', 'o', '', 463),
(449, 989, 'По возрастанию', 'asc', '', 0),
(450, 989, 'По убыванию', 'desc', '', 393),
(451, 992, 'Объекта', 'o', '', 0),
(452, 992, 'Массива', 'a', '', 394),
(453, 997, 'Объекта', 'o', '', 0),
(454, 997, 'Массива', 'a', '', 395),
(455, 998, 'Массива', 'a', '', 0),
(458, 1009, 'SQL-выражению', 'e', 'if($(''#typeR'').attr(''checked'')) hide(''tr-orderColumn,tr-orderDirection'');show(''tr-orderExpression'');', 396),
(456, 998, 'Объекта', 'o', '', 0),
(457, 1009, 'Одному из имеющихся столбцов', 'c', 'if($(''#typeR'').attr(''checked'')) show(''tr-orderColumn,tr-orderDirection'');hide(''tr-orderExpression'');', 0),
(459, 1011, 'По возрастанию', 'ASC', '', 0),
(460, 1011, 'По убыванию', 'DESC', '', 0),
(461, 1013, 'Одному из имеющихся столбцов', 'c', 'show(''tr-orderColumn,tr-orderDirection'');hide(''tr-orderExpression'');', 0),
(462, 1013, 'SQL-выражению', 'e', 'hide(''tr-orderColumn,tr-orderDirection'');show(''tr-orderExpression'');', 397),
(463, 1015, 'По возрастанию', 'ASC', '', 0),
(464, 1015, 'По убыванию', 'DESC', '', 398),
(467, 1022, 'Объекта', 'o', '', 0),
(468, 1022, 'Массива', 'a', '', 401),
(484, 1074, 'Над записью', 'r', '', 0),
(485, 1074, 'Над набором записей', 'rs', '', 411),
(486, 1074, 'Только независимые множества, если нужно', 'n', '', 412),
(487, 1075, '=', 'e', '', 0),
(488, 1075, 'LIKE %?%', 'l', '', 413),
(489, 1075, 'BETWEEN', 'b', '', 414),
(509, 689, 'Не указан', 'n', '', 192),
(567, 1364, 'Проектное', 'p', '', 0),
(568, 1364, '<font color=red>Системное</font>', 's', '', 458),
(569, 1364, '<font color=lime>Типовое</font>', 'o', '', 459),
(512, 1155, 'Значение', 'v', 'show(''tr-value'');', 0),
(513, 1155, 'Ветка', 'b', 'hide(''tr-value'');', 430),
(516, 1163, 'Никакая', 'n', 'hide(''tr-identifier'');', 0),
(517, 1163, 'Facebook', 'fb', 'show(''tr-identifier'');', 432),
(518, 1163, 'Вконтакте', 'vk', 'show(''tr-identifier'');', 433),
(519, 1163, 'Мой Мир@Mail.ru', 'mm', 'show(''tr-identifier'');', 434),
(539, 1260, 'Динамический', 'd', 'hide(''tr-static'');show(''tr-entityId,tr-fieldId,tr-where'');if($(''#whereS'').attr(''checked'')) show(''tr-sibling''); else hide(''tr-sibling'');', 443),
(540, 1264, 'Из контекста', 'c', 'hide(''tr-sibling'');', 0),
(541, 1264, 'Из sibling-компонента', 's', 'show(''tr-sibling'');', 444),
(542, 1272, 'Статический', 's', 'show(''tr-static'');hide(''tr-entityId,tr-fieldId,tr-where,tr-sibling'');', 0),
(543, 1272, 'Динамический', 'd', 'hide(''tr-static'');show(''tr-entityId,tr-fieldId,tr-where'');if($(''#whereS'').attr(''checked'')) show(''tr-sibling''); else hide(''tr-sibling'');', 445),
(544, 1276, 'Из контекста', 'c', 'hide(''tr-sibling'');', 0),
(545, 1276, 'Из sibling-компонента', 's', 'show(''tr-sibling'');', 446),
(560, 1317, 'И', 'a', '', 0),
(561, 1317, 'ИЛИ', 'o', '', 454),
(562, 1322, 'И', 'a', '', 0),
(563, 1322, 'ИЛИ', 'o', '', 455),
(564, 1324, 'И', 'a', '', 0),
(565, 1324, 'ИЛИ', 'o', '', 456),
(566, 612, '<font color=lime>Типовая</font>', 'o', 'hide(''tr-useCache'');$(''#useCache1'').attr(''checked'', false);', 457),
(582, 1488, 'Включен', 'y', '', 0),
(583, 1488, '<span style=''color: red''>Выключен</span>', 'n', '', 465),
(584, 1491, 'Нет', 'n', 'hide(''tr-staticpageId'');show(''tr-url'');', 0),
(585, 1491, 'Да', 'y', 'show(''tr-staticpageId'');hide(''tr-url'');$(''#url'').val('''');', 466),
(586, 1494, 'Включен', 'y', '', 0),
(587, 1494, '<span style=''color: red''>Выключен</span>', 'n', '', 467),
(594, 1515, 'HTML-код', 'html', 'show(''tr-detailsHtml,tr-detailsHtmlWide,tr-detailsHtmlWidth,tr-detailsHtmlHeight,tr-detailsHtmlBodyClass,tr-detailsHtmlStyle'');hide(''tr-detailsString,tr-detailsTextarea'');', 0),
(595, 1515, 'Строка', 'string', 'hide(''tr-detailsHtml,tr-detailsHtmlWide,tr-detailsHtmlWidth,tr-detailsHtmlHeight,tr-detailsHtmlBodyClass,tr-detailsHtmlStyle,tr-detailsTextarea'');show(''tr-detailsString'');', 471),
(596, 1515, 'Текст', 'textarea', 'hide(''tr-detailsHtml,tr-detailsHtmlWide,tr-detailsHtmlWidth,tr-detailsHtmlHeight,tr-detailsHtmlBodyClass,tr-detailsHtmlStyle,tr-detailsString'');show(''tr-detailsTextarea'');', 472),
(608, 1533, '<span style=''color:red;''>Выключен</span>', 'n', '', 478),
(607, 1533, 'Включен', 'y', '', 0),
(605, 1528, 'Включено', 'y', '', 0),
(606, 1528, '<span style=''color:red;''>Выключено</span>', 'n', '', 477);

-- --------------------------------------------------------

--
-- Структура таблицы `faction`
--

DROP TABLE IF EXISTS `faction`;
CREATE TABLE IF NOT EXISTS `faction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `maintenance` enum('r','rs','n') NOT NULL DEFAULT 'r',
  `type` enum('o','s','p') NOT NULL DEFAULT 'p',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Дамп данных таблицы `faction`
--

INSERT INTO `faction` (`id`, `title`, `alias`, `maintenance`, `type`) VALUES
(1, 'По умолчанию', 'index', 'rs', 's'),
(2, 'Просмотр', 'details', 'r', 's'),
(3, 'Изменить', 'form', 'r', 's'),
(5, 'Добавить', 'add', 'n', 's'),
(6, 'Активация', 'activation', 'n', 'o'),
(36, 'Регистрация', 'registration', 'n', 'o');

-- --------------------------------------------------------

--
-- Структура таблицы `fconfig`
--

DROP TABLE IF EXISTS `fconfig`;
CREATE TABLE IF NOT EXISTS `fconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `type` enum('v','b') NOT NULL DEFAULT 'v',
  `value` varchar(255) NOT NULL,
  `fconfigId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Дамп данных таблицы `fconfig`
--

INSERT INTO `fconfig` (`id`, `title`, `alias`, `type`, `value`, `fconfigId`) VALUES
(11, 'Включить использование кэша для некоторых системных сущностей', 'useCache', 'v', 'false', 0),
(9, 'Включить режим seo-урлов', 'enableSeoUrls', 'v', 'true', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Структура таблицы `field`
--

DROP TABLE IF EXISTS `field`;
CREATE TABLE IF NOT EXISTS `field` (
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
  `dependency` enum('e','с','u') NOT NULL DEFAULT 'e',
  `storeRelationAbility` enum('none','many','one') NOT NULL DEFAULT 'none',
  `alternative` varchar(255) NOT NULL DEFAULT '',
  `filter` varchar(255) NOT NULL,
  `satellitealias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entityId` (`entityId`),
  KEY `columnTypeId` (`columnTypeId`),
  KEY `elementId` (`elementId`),
  KEY `relation` (`relation`),
  KEY `satellite` (`satellite`),
  KEY `dependency` (`dependency`),
  KEY `storeRelationAbility` (`storeRelationAbility`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1536 ;

--
-- Дамп данных таблицы `field`
--

INSERT INTO `field` (`id`, `entityId`, `title`, `alias`, `columnTypeId`, `elementId`, `defaultValue`, `move`, `relation`, `javascript`, `satellite`, `dependency`, `storeRelationAbility`, `alternative`, `filter`, `satellitealias`) VALUES
(1, 1, 'Наименование', 'title', 1, 1, '', 1, 0, '', 0, 'e', 'none', '0', '', ''),
(2, 1, 'Тип столбца MySQL', 'type', 1, 1, '', 2, 0, '', 0, 'e', 'none', '0', '', ''),
(3, 1, 'Пригоден для хранения внешних ключей', 'canStoreRelation', 10, 5, 'n', 3, 6, '', 0, 'e', 'one', '0', '', ''),
(4, 2, 'Наименование', 'title', 1, 1, '', 4, 0, '', 0, 'e', 'none', '0', '', ''),
(5, 2, 'Таблица БД', 'table', 1, 1, '', 5, 0, '', 0, 'e', 'none', '0', '', ''),
(6, 5, 'Сущность, в структуру которой входит это поле', 'entityId', 3, 3, '0', 6, 2, '', 0, 'e', 'one', '0', '', ''),
(7, 5, 'Наименование поля', 'title', 1, 1, '', 7, 0, '', 0, 'e', 'none', '0', '', ''),
(8, 5, 'Наименование соответствующего полю столбца в  таблице БД', 'alias', 1, 1, '', 8, 0, '', 0, 'e', 'none', '0', '', ''),
(9, 5, 'Тип столбца MySQL', 'columnTypeId', 3, 3, '0', 11, 1, '', 10, 'с', 'one', '0', '', ''),
(10, 5, 'Элемент управления', 'elementId', 3, 3, '0', 10, 4, '', 470, 'с', 'one', '0', '', ''),
(11, 5, 'Значение по умолчанию', 'defaultValue', 1, 1, '', 12, 0, '', 0, 'e', 'none', '0', '', ''),
(12, 5, 'Ключи какой сущности будут храниться в этом поле', 'relation', 3, 3, '0', 344, 2, '', 0, 'e', 'one', '0', '', ''),
(14, 5, 'Положение в списке', 'move', 3, 4, '0', 13, 0, '', 0, 'e', 'none', '0', '', ''),
(15, 6, 'Поле', 'fieldId', 3, 3, '0', 15, 5, '', 0, 'e', 'one', '0', '', ''),
(16, 6, 'Наименование', 'title', 1, 1, '', 16, 0, '', 0, 'e', 'none', '0', '', ''),
(17, 6, 'Псевдоним', 'alias', 1, 1, '', 17, 0, '', 0, 'e', 'none', '0', '', ''),
(18, 3, 'Подчинен разделу', 'sectionId', 3, 3, '0', 19, 3, '', 0, 'e', 'one', '0', '', ''),
(19, 3, 'Cущность, с которой будет работать раздел', 'entityId', 3, 3, '0', 25, 2, '', 0, 'e', 'one', '0', '', ''),
(1344, 3, 'Настройки формы', 'formSettings', 0, 16, '', 1279, 0, '', 0, 'e', 'none', '', '', ''),
(20, 3, 'Наименование', 'title', 1, 1, '', 18, 0, '', 0, 'e', 'none', '0', '', ''),
(21, 3, 'Контроллер', 'alias', 1, 1, '', 21, 0, '', 0, 'e', 'none', '0', '', ''),
(22, 3, 'Статус', 'toggle', 10, 5, 'y', 20, 6, '', 0, 'e', 'one', '0', '', ''),
(23, 3, 'Положение в списке', 'move', 3, 4, '', 310, 0, '', 0, 'e', 'none', '0', '', ''),
(25, 3, 'Количество строк на странице', 'rowsOnPage', 3, 1, '30', 514, 0, '', 0, 'e', 'none', '0', '', ''),
(26, 8, 'Раздел, за которым закреплено действие', 'sectionId', 3, 3, '', 1, 3, '', 0, 'e', 'one', '0', '', ''),
(27, 8, 'Действие', 'actionId', 3, 3, '', 1, 7, '', 0, 'e', 'one', '0', '', ''),
(28, 8, 'Профили пользователей, имеющих доступ к этому действию в этом разделе', 'profileIds', 1, 7, '1,2,3', 1, 10, '', 0, 'e', 'many', '0', '', ''),
(29, 8, 'Статус', 'toggle', 10, 5, 'y', 1, 6, '', 0, 'e', 'one', '0', '', ''),
(30, 8, 'Положение в списке', 'move', 3, 4, '', 1, 0, '', 0, 'e', 'none', '0', '', ''),
(31, 7, 'Наименование', 'title', 1, 1, '', 26, 0, '', 0, 'e', 'none', '0', '', ''),
(32, 7, 'Псевдоним', 'alias', 1, 1, '', 27, 0, '', 0, 'e', 'none', '0', '', ''),
(33, 9, 'Раздел', 'sectionId', 3, 3, '', 28, 3, '', 0, 'e', 'one', '0', '', ''),
(34, 9, 'Столбец', 'fieldId', 3, 3, '', 29, 5, '', 33, 'с', 'one', 'entityId', '', ''),
(35, 9, 'Очередность отображения столбца в гриде', 'move', 3, 4, '', 30, 0, '', 0, 'e', 'none', '0', '', ''),
(36, 10, 'Наименование', 'title', 1, 1, '', 31, 0, '', 0, 'e', 'none', '0', '', ''),
(37, 10, 'Статус', 'toggle', 10, 5, 'y', 32, 6, '', 0, 'e', 'one', '0', '', ''),
(38, 11, 'Профиль', 'profileId', 3, 3, '', 33, 10, '', 0, 'e', 'one', '0', '', ''),
(39, 11, 'Фамилия Имя', 'title', 1, 1, '', 34, 0, '', 0, 'e', 'none', '0', '', ''),
(40, 11, 'Email (используется в качестве логина)', 'email', 1, 1, '', 35, 0, '', 0, 'e', 'none', '0', '', ''),
(41, 11, 'Пароль', 'password', 1, 1, '', 36, 0, '', 0, 'e', 'none', '0', '', ''),
(42, 11, 'Статус', 'toggle', 10, 5, 'y', 37, 6, '', 0, 'e', 'one', '0', '', ''),
(64, 4, 'Наименование', 'title', 1, 1, '', 53, 0, '', 0, 'e', 'none', '0', '', ''),
(65, 4, 'Псевдоним', 'alias', 1, 1, '', 54, 0, '', 0, 'e', 'none', '0', '', ''),
(66, 4, 'Способен работать с внешними ключами', 'storeRelationAbility', 11, 7, 'none', 55, 6, '', 0, 'e', 'none', '0', '', ''),
(1445, 195, 'Статус', 'toggle', 10, 5, 'y', 1316, 6, '', 0, 'e', 'one', '', '', ''),
(92, 4, 'Скрывать при генерации формы', 'hidden', 12, 9, '0', 72, 0, '', 0, 'e', 'none', '0', '', ''),
(106, 20, 'Поле', 'fieldId', 3, 3, '0', 86, 5, '', 0, 'e', 'one', '0', '', ''),
(107, 20, 'Наименование', 'title', 1, 1, '', 87, 0, '', 0, 'e', 'none', '0', '', ''),
(108, 20, 'Псевдоним', 'alias', 1, 1, '', 88, 0, '', 0, 'e', 'none', '0', '', ''),
(109, 20, 'Ширина', 'masterDimensionValue', 3, 15, '0', 91, 0, '', 0, 'e', 'none', '0', '', ''),
(110, 20, 'Высота', 'slaveDimensionValue', 3, 15, '0', 93, 0, '', 0, 'e', 'none', '0', '', ''),
(111, 20, 'Размер', 'proportions', 10, 5, 'o', 89, 6, '', 0, 'e', 'one', '0', '', ''),
(112, 20, 'Ограничить пропорциональную <span id="slaveDimensionTitle">высоту</span>', 'slaveDimensionLimitation', 12, 9, '1', 92, 0, 'if($(''#proportionsP'').hasClass(''checked'')){if(!$(this).hasClass(''checked'')) hide(''tr-slaveDimensionValue''); else show(''tr-slaveDimensionValue'');}', 0, 'e', 'none', '0', '', ''),
(114, 20, 'При расчете пропорций отталкиваться от', 'masterDimensionAlias', 10, 5, 'width', 90, 6, '$(''#td-left-masterDimensionValue'').text(this.value==''height'' ? ''Высота:'':''Ширина:'');$(''#td-left-slaveDimensionValue'').text(this.value!=''height'' ? ''Высота:'':''Ширина:'');$(''#slaveDimensionTitle'').text(this.value!=''height'' ? ''высоту'':''ширину'');', 0, 'e', 'one', '0', '', ''),
(115, 6, 'Javascript-сценарий при выборе этого значения', 'javascript', 4, 6, '', 94, 0, '', 0, 'e', 'none', '0', '', ''),
(116, 5, 'Javascript-сценарий при изменении значения поля', 'javascript', 4, 6, '', 95, 0, '', 0, 'e', 'none', '0', '', ''),
(131, 25, 'Наименование', 'title', 1, 1, '', 96, 0, '', 0, 'e', 'none', '0', '', ''),
(133, 25, 'Псевдоним', 'alias', 1, 1, '', 97, 0, '', 0, 'e', 'none', '0', '', ''),
(1476, 25, 'Контент', 'details', 4, 13, '', 101, 0, '', 0, 'e', 'none', '', '', ''),
(137, 25, 'Статус', 'toggle', 10, 5, 'y', 102, 6, '', 0, 'e', 'one', '0', '', ''),
(345, 7, 'Для выполнения действия необходимо выбрать стоку', 'rowRequired', 10, 5, 'y', 308, 6, '', 0, 'e', 'one', '0', '', ''),
(346, 7, 'Javascript', 'javascript', 4, 6, '', 309, 0, '', 0, 'e', 'none', '0', '', ''),
(347, 3, 'Javascript для выполнения после загрузки грида', 'javascript', 4, 6, '', 1278, 0, '', 0, 'e', 'none', '0', '', ''),
(377, 6, 'Порядок отображения', 'move', 3, 4, '0', 338, 0, '', 0, 'e', 'none', '0', '', ''),
(383, 5, 'Столбец-satellite', 'satellite', 3, 3, '0', 523, 5, '', 0, 'e', 'one', '0', '', ''),
(454, 5, 'Динамическое обновление', 'span', 0, 16, '', 428, 0, '', 0, 'e', 'none', '0', '', ''),
(455, 5, 'Тип зависимости', 'dependency', 10, 5, 'e', 701, 6, '', 0, 'e', 'one', '0', '', ''),
(470, 5, 'Предназначено для хранения ключей', 'storeRelationAbility', 10, 3, 'none', 9, 6, '', 0, 'e', 'one', '0', '', ''),
(471, 90, 'Элемент управления', 'elementId', 3, 3, '0', 429, 4, '', 0, 'e', 'one', '0', '', ''),
(472, 90, 'Наименование', 'title', 1, 1, '', 430, 0, '', 0, 'e', 'none', '0', '', ''),
(473, 90, 'Псевдоним', 'alias', 1, 1, '', 431, 0, '', 0, 'e', 'none', '0', '', ''),
(474, 90, 'Значение по умолчанию', 'defaultValue', 1, 1, '', 432, 0, '', 0, 'e', 'none', '0', '', ''),
(475, 1, 'Пригоден для работы с элементами управления', 'elementId', 1, 7, '', 433, 4, '', 0, 'e', 'many', '0', '', ''),
(476, 91, 'В контексте какого поля', 'fieldId', 3, 3, '0', 434, 5, '', 0, 'e', 'one', '0', '', ''),
(477, 91, 'Параметр настройки', 'possibleParamId', 3, 3, '0', 435, 90, '', 476, 'с', 'one', 'elementId', '', ''),
(478, 91, 'Значение параметра', 'value', 4, 6, '', 436, 0, '', 0, 'e', 'none', '0', '', ''),
(485, 3, 'Javascript для выполнения после загрузки формы', 'javascriptForm', 4, 6, '', 1294, 0, '', 0, 'e', 'none', '0', '', ''),
(502, 3, 'От какого класса наследовать класс контроллера', 'extends', 1, 1, 'Indi_Controller_Admin', 23, 0, '', 0, 'e', 'none', '0', '', ''),
(503, 3, 'По умолчанию сортировать грид по столбцу', 'defaultSortField', 3, 3, '0', 461, 5, '', 19, 'с', 'one', '0', '', ''),
(504, 3, 'Настройки грида', 'grid', 0, 16, '', 443, 0, '', 0, 'e', 'none', '0', '', ''),
(507, 94, 'Наименование', 'title', 1, 1, '', 465, 0, '', 0, 'e', 'none', '0', '', ''),
(508, 94, 'Псевдоним', 'alias', 1, 1, '', 466, 0, '', 0, 'e', 'none', '0', '', ''),
(509, 94, 'Значение', 'value', 1, 1, '', 467, 0, '', 0, 'e', 'none', '0', '', ''),
(555, 2, 'От какого класса наследовать', 'extends', 1, 1, 'Indi_Db_Table', 512, 0, '', 0, 'e', 'none', '0', '', ''),
(557, 3, 'Направление сортировки', 'defaultSortDirection', 10, 5, 'ASC', 462, 6, '', 0, 'e', 'none', '0', '', ''),
(559, 101, 'Наименование', 'title', 1, 1, '', 517, 0, '', 0, 'e', 'none', '0', '', ''),
(560, 101, 'Псевдоним', 'alias', 1, 1, '', 519, 0, '', 0, 'e', 'none', '0', '', ''),
(561, 102, 'Количества', 'title', 1, 1, '', 518, 0, '', 0, 'e', 'none', '0', '', ''),
(562, 101, 'Набор возможных количеств строк для выбора постраничного отображения', 'rppId', 3, 3, '1', 815, 102, '', 0, 'e', 'one', '0', '', ''),
(563, 101, 'Прикрепленная сущность', 'entityId', 3, 3, '0', 520, 2, '', 0, 'e', 'one', '0', '', ''),
(564, 103, 'К какому разделу фронтенда относится', 'fsectionId', 3, 3, '0', 521, 101, '', 0, 'e', 'one', '0', '', ''),
(565, 103, 'Фильтр по какому полю сущности, прикрепленной к разделу', 'fieldId', 3, 3, '0', 522, 5, '', 564, 'с', 'one', 'entityId', '', ''),
(566, 5, 'Если имя столбца-satellite не найдено, то по какому другому столбцу фильтровать', 'alternative', 1, 1, '', 775, 0, '', 0, 'e', 'none', '', '', ''),
(567, 103, 'В фильтре отображать опции', 'displayOptions', 10, 5, 'a', 526, 6, '', 0, 'e', 'none', '', '', ''),
(568, 103, 'Наименование', 'title', 1, 1, '', 524, 0, '', 0, 'e', 'none', '', '', ''),
(569, 103, 'Псевдоним', 'alias', 1, 1, '', 525, 0, '', 0, 'e', 'none', '', '', ''),
(570, 104, 'К какому разделу фронтенда относится', 'fsectionId', 3, 3, '0', 528, 101, '', 0, 'e', 'one', '', '', ''),
(571, 104, 'Поле', 'fieldId', 3, 3, '0', 530, 5, '', 570, 'с', 'one', 'entityId', '', ''),
(1243, 104, 'Сортировка по', 'orderBy', 10, 5, 'f', 529, 6, '', 0, 'e', 'one', '', '', ''),
(572, 104, 'Очередность отображения', 'move', 3, 4, '0', 1178, 0, '', 0, 'e', 'none', '', '', ''),
(573, 104, 'Наименование поля, отображаемое в фронтэнде', 'title', 1, 1, '', 527, 0, '', 0, 'e', 'none', '', '', ''),
(580, 108, 'К какому разделу фронтенда относится', 'fsectionId', 3, 3, '0', 533, 101, '', 0, 'e', 'one', '', '', ''),
(581, 101, 'Соответствующий раздел бэкенда', 'sectionId', 3, 3, '0', 534, 3, '', 0, 'e', 'one', '', '', ''),
(582, 108, 'Соответствующий зависимый раздел бэкенда', 'sectionId', 3, 3, '0', 536, 3, '', 0, 'с', 'one', 'sectionId', '', ''),
(583, 108, 'Наименование', 'title', 1, 1, '', 537, 0, '', 0, 'e', 'none', '', '', ''),
(584, 108, 'Псевдоним', 'alias', 1, 1, '', 807, 0, '', 0, 'e', 'none', '', '', ''),
(585, 101, 'Порядок отображения соответствующего пункта в меню', 'move', 3, 4, '0', 901, 0, '', 0, 'e', 'none', '', '', ''),
(594, 20, 'Изменить оттенок', 'changeColor', 10, 5, 'n', 545, 6, '', 0, 'e', 'one', '', '', ''),
(595, 20, 'Оттенок', 'color', 13, 11, '', 546, 0, '', 0, 'e', 'none', '', '', ''),
(596, 110, 'К какому разделу фронтенда относится', 'fsectionId', 3, 3, '0', 547, 101, '', 0, 'e', 'one', '', '', ''),
(597, 110, 'Для какого поля прикрепленной сущности делать JOIN', 'fieldId', 3, 3, '0', 809, 5, '', 596, 'с', 'one', 'entityId', '', ''),
(598, 110, 'Наименование', 'title', 1, 1, '', 549, 0, '', 0, 'e', 'none', '', '', ''),
(602, 111, 'К какому разделу фронтенда относится', 'fsectionId', 3, 3, '0', 551, 101, '', 0, 'e', 'one', '', '', ''),
(603, 111, 'Множество записей какой сущности', 'entityId', 3, 3, '0', 808, 2, '', 0, 'e', 'one', '', '', ''),
(606, 111, 'Наименование', 'title', 1, 1, '', 553, 0, '', 0, 'e', 'none', '', '', ''),
(607, 111, 'Псевдоним', 'alias', 1, 1, '', 554, 0, '', 0, 'e', 'none', '', '', ''),
(612, 2, 'Тип', 'system', 10, 5, 'n', 559, 6, '', 0, 'e', 'one', '', '', ''),
(678, 128, 'Имя', 'title', 1, 1, '', 625, 0, '', 0, 'e', 'none', '', '', ''),
(679, 128, 'Email', 'email', 1, 1, '', 626, 0, '', 0, 'e', 'none', '', '', ''),
(680, 128, 'Сообщение', 'message', 4, 6, '', 627, 0, '', 0, 'e', 'none', '', '', ''),
(681, 128, 'Дата', 'date', 6, 12, '', 628, 0, '', 0, 'e', 'none', '', '', ''),
(682, 129, 'Email', 'title', 1, 1, '', 629, 0, '', 0, 'e', 'none', '', '', ''),
(683, 129, 'Дата', 'date', 6, 12, '', 630, 0, '', 0, 'e', 'none', '', '', ''),
(684, 130, 'Ник', 'title', 1, 1, '', 631, 0, '', 0, 'e', 'none', '', '', ''),
(685, 130, 'Email', 'email', 1, 1, '', 632, 0, '', 0, 'e', 'none', '', '', ''),
(686, 130, 'Пароль', 'password', 1, 1, '', 633, 0, '', 0, 'e', 'none', '', '', ''),
(687, 130, 'Настоящее имя', 'name', 1, 1, '', 640, 0, '', 0, 'e', 'none', '', '', ''),
(688, 130, 'Фамилия', 'surname', 1, 1, '', 641, 0, '', 0, 'e', 'none', '', '', ''),
(689, 130, 'Пол', 'gender', 10, 5, 'n', 642, 6, '', 0, 'e', 'one', '', '', ''),
(690, 130, 'Дата рождения', 'birth', 6, 12, '', 644, 0, '', 0, 'e', 'none', '', '', ''),
(691, 130, 'Дата регистрации', 'registration', 6, 12, '', 634, 0, '', 0, 'e', 'none', '', '', ''),
(1364, 7, 'Тип', 'type', 10, 5, 'p', 1292, 6, '', 0, 'e', 'one', '', '', ''),
(1365, 146, 'Тип', 'type', 10, 5, 'p', 1293, 6, '', 0, 'e', 'one', '', '', ''),
(698, 130, 'Подписался на рассылку', 'subscribed', 12, 9, '0', 969, 0, '', 0, 'e', 'none', '', '', ''),
(699, 130, 'Последний визит', 'lastVisit', 9, 19, '', 637, 0, '', 0, 'e', 'none', '', '', ''),
(1444, 195, 'Порядок отображения', 'move', 3, 4, '0', 1315, 0, '', 0, 'e', 'none', '', '', ''),
(1442, 195, 'Раздел', 'sectionId', 3, 3, '0', 1313, 3, '', 0, 'e', 'one', '', '', ''),
(1443, 195, 'Поле прикрепленной к разделу сущности', 'fieldId', 3, 3, '0', 1314, 5, '', 1442, 'с', 'one', 'entityId', '', ''),
(1441, 2, 'Включить в кэш', 'useCache', 12, 9, '0', 1312, 0, '', 0, 'e', 'none', '', '', ''),
(754, 5, 'Статическая фильтрация', 'filter', 1, 1, '', 413, 0, '', 0, 'e', 'none', '', '', ''),
(767, 3, 'Статическая фильтрация', 'filter', 1, 1, '', 460, 0, '', 0, 'e', 'none', '', '', ''),
(828, 5, 'Псевдоним поля для использования в satellite-функциональности', 'satellitealias', 1, 1, '', 414, 0, '', 0, 'e', 'none', '', '', ''),
(857, 146, 'Наименование', 'title', 1, 1, '', 803, 0, '', 0, 'e', 'none', '', '', ''),
(858, 146, 'Псевдоним', 'alias', 1, 1, '', 804, 0, '', 0, 'e', 'none', '', '', ''),
(859, 147, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 805, 101, '', 0, 'e', 'one', '', '', ''),
(860, 147, 'Действие', 'factionId', 3, 3, '0', 806, 146, '', 0, 'e', 'one', '', '', ''),
(861, 108, 'Действие, при выполнении которого требуется посчитать количество', 'fsection2factionId', 3, 3, '0', 535, 147, '', 580, 'с', 'one', '', '', ''),
(862, 111, 'Действие, при выполнении которого требуется выдернуть множество', 'fsection2factionId', 3, 3, '0', 552, 147, '', 602, 'с', 'one', '', '', ''),
(863, 110, 'Действие, при выполнении которого требуется выдернуть foreign row', 'fsection2factionId', 3, 3, '0', 548, 147, '', 596, 'с', 'one', '', '', ''),
(868, 101, 'Вышестоящий раздел', 'fsectionId', 3, 3, '0', 516, 101, '', 0, 'e', 'one', '', '', ''),
(869, 101, 'Статическая фильтрация', 'filter', 1, 1, '', 814, 0, '', 0, 'e', 'none', '', '', ''),
(940, 111, 'Выдергивать в виде', 'returnAs', 10, 5, 'o', 885, 6, '', 0, 'e', 'none', '', '', ''),
(960, 101, 'Количество строк для отображения по умолчанию', 'defaultLimit', 3, 18, '20', 950, 0, '', 0, 'e', 'none', '', '', ''),
(961, 130, 'Аккаунт активирован', 'activated', 12, 9, '0', 635, 0, '', 0, 'e', 'none', '', '', ''),
(962, 130, 'Код активации', 'activationCode', 1, 1, '', 636, 0, '', 0, 'e', 'none', '', '', ''),
(980, 147, 'Использовать версию верстки №', 'imposition', 3, 18, '0', 921, 0, '', 0, 'e', 'none', '', '', ''),
(981, 155, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 922, 101, '', 0, 'e', 'one', '', '', ''),
(982, 155, 'Действие в разделе фронтенда, при котором вытаскивать rowset', 'fsection2factionId', 3, 3, '0', 923, 147, '', 981, 'с', 'one', '', '', ''),
(983, 155, 'Множество записаей какой сущности', 'entityId', 3, 3, '0', 925, 2, '', 0, 'e', 'one', '', '', ''),
(984, 155, 'Статическая фильтрация', 'filter', 1, 1, '', 928, 0, '', 0, 'e', 'none', '', '', ''),
(985, 155, 'Сортировка по', 'orderBy', 10, 5, 'c', 930, 6, '', 0, 'e', 'one', '', '', ''),
(986, 155, 'Столбец', 'fieldId', 3, 3, '0', 931, 5, '', 983, 'с', 'one', '', '', ''),
(987, 155, 'Выражение', 'expression', 1, 1, '', 933, 0, '', 0, 'e', 'none', '', '', ''),
(1366, 3, 'Тип', 'type', 10, 5, 'p', 22, 6, '', 0, 'e', 'one', '', '', ''),
(989, 155, 'Направление сортировки', 'orderDirection', 10, 5, 'asc', 932, 6, '', 0, 'e', 'one', '', '', ''),
(990, 155, 'Ограничение (LIMIT)', 'limit', 1, 1, '', 1290, 0, '', 0, 'e', 'none', '', '', ''),
(991, 155, 'Псевдоним', 'alias', 1, 1, '', 924, 0, '', 0, 'e', 'none', '', '', ''),
(992, 155, 'Выдергивать в виде', 'returnAs', 10, 5, 'o', 1291, 6, '', 0, 'e', 'one', '', '', ''),
(993, 156, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 934, 101, '', 0, 'e', 'one', '', '', ''),
(994, 156, 'Действие в разделе фронтенда', 'fsection2factionId', 3, 3, '0', 935, 147, '', 993, 'с', 'one', '', '', ''),
(995, 156, 'Независимое множество', 'independentRowsetId', 3, 3, '0', 936, 155, '', 994, 'с', 'one', '', '', ''),
(996, 156, 'Для какого поля делать джойн', 'fieldId', 3, 3, '0', 937, 5, '', 995, 'с', 'one', 'entityId', '', ''),
(997, 156, 'Выдергивать в виде', 'returnAs', 10, 5, 'o', 938, 6, '', 0, 'e', 'one', '', '', ''),
(998, 110, 'Выдергивать в виде', 'returnAs', 10, 5, 'o', 939, 6, '', 0, 'e', 'one', '', '', ''),
(1000, 111, 'Ограничение (LIMIT)', 'limit', 3, 18, '0', 1012, 0, '', 0, 'e', 'none', '', '', ''),
(1009, 101, 'По умолчанию сортировка по', 'orderBy', 10, 5, 'c', 951, 6, '', 0, 'e', 'one', '', '', ''),
(1010, 101, 'Столбец сортировки', 'orderColumn', 3, 3, '0', 952, 5, '', 563, 'с', 'one', '', '', ''),
(1011, 101, 'Направление сортировки', 'orderDirection', 10, 5, 'ASC', 953, 6, '', 0, 'e', 'one', '', '', ''),
(1012, 101, 'SQL-выражение', 'orderExpression', 1, 1, '', 981, 0, '', 0, 'e', 'none', '', '', ''),
(1013, 111, 'По умолчанию сортировка по', 'orderBy', 10, 5, 'c', 954, 6, '', 0, 'e', 'one', '', '', ''),
(1014, 111, 'Столбец сортировки', 'orderColumn', 3, 3, '0', 955, 5, '', 603, 'с', 'one', '', '', ''),
(1015, 111, 'Направление сортировки', 'orderDirection', 10, 5, 'ASC', 956, 6, '', 0, 'e', 'one', '', '', ''),
(1016, 111, 'SQL-выражение', 'orderExpression', 1, 1, '', 957, 0, '', 0, 'e', 'none', '', '', ''),
(1018, 158, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 959, 101, '', 0, 'e', 'one', '', '', ''),
(1019, 158, 'Действие в разделе фронтенда', 'fsection2factionId', 3, 3, '0', 960, 147, '', 1018, 'с', 'one', '', '', ''),
(1020, 158, 'Зависимое множество', 'dependentRowsetId', 3, 3, '0', 961, 111, '', 1019, 'с', 'one', '', '', ''),
(1021, 158, 'Для какого поля делать джойн', 'fieldId', 3, 3, '0', 962, 5, '', 1020, 'с', 'one', 'entityId', '', ''),
(1022, 158, 'Выдергивать в виде', 'returnAs', 10, 5, 'o', 963, 6, '', 0, 'e', 'one', '', '', ''),
(1027, 147, 'Тип', 'type', 10, 5, 'r', 968, 6, '', 0, 'e', 'one', '', '', ''),
(1028, 130, 'Аватар', 'avatar', 0, 14, '', 902, 0, '', 0, 'e', 'none', '', '', ''),
(1040, 101, 'Тип', 'type', 10, 5, 'r', 538, 6, '', 0, 'e', 'one', '', '', ''),
(1041, 101, 'Где брать идентификатор', 'where', 1, 1, '', 982, 0, '', 0, 'e', 'none', '', '', ''),
(1042, 101, 'Действие по умолчанию', 'index', 1, 1, '', 983, 0, '', 0, 'e', 'none', '', '', ''),
(1044, 108, 'Условие', 'where', 1, 1, '', 985, 0, '', 0, 'e', 'none', '', '', ''),
(1071, 111, 'Дополнительное условие выборки', 'where', 1, 1, '', 941, 0, '', 0, 'e', 'none', '', '', ''),
(1074, 146, 'Выполнять maintenance()', 'maintenance', 10, 5, 'r', 1015, 6, '', 0, 'e', 'one', '', '', ''),
(1075, 103, 'Тип', 'type', 10, 5, 'e', 1016, 6, '', 0, 'e', 'one', '', '', ''),
(1082, 159, 'К какому разделу фронтенда относится', 'fsectionId', 3, 3, '0', 1023, 101, '', 0, 'e', 'one', '', '', ''),
(1083, 159, 'Действие, при выполнении которого требуется посчитать количество', 'fsection2factionId', 3, 3, '0', 1024, 147, '', 1082, 'с', 'one', '', '', ''),
(1084, 159, 'Зависимое множество, для каждого элемента которого нужно посчитать зависимое количество', 'dependentRowsetId', 3, 3, '0', 1025, 111, '', 1083, 'с', 'one', '', '', ''),
(1085, 159, 'Соответствующий зависимый раздел бэкенда', 'sectionId', 3, 3, '0', 1026, 3, '', 0, 'e', 'one', '', '', ''),
(1086, 159, 'Наименование', 'title', 1, 1, '', 1027, 0, '', 0, 'e', 'none', '', '', ''),
(1087, 159, 'Псевдоним', 'alias', 1, 1, '', 1028, 0, '', 0, 'e', 'none', '', '', ''),
(1088, 159, 'Условие', 'where', 1, 1, '', 1029, 0, '', 0, 'e', 'none', '', '', ''),
(1100, 160, 'Id сессии', 'title', 1, 1, '', 1040, 0, '', 0, 'e', 'none', '', '', ''),
(1101, 160, 'Дата последней активности', 'lastActivity', 9, 19, '', 1041, 0, '', 0, 'e', 'none', '', '', ''),
(1102, 160, 'Пользователь', 'userId', 3, 3, '0', 1042, 130, '', 0, 'e', 'one', '', '', ''),
(1103, 160, 'Скрытый', 'hidden', 12, 9, '0', 1043, 0, '', 0, 'e', 'none', '', '', ''),
(1107, 130, 'Информация', 'private', 0, 16, '', 639, 0, '', 0, 'e', 'none', '', '', ''),
(1108, 130, 'Настройки', 'settings', 0, 16, '', 903, 0, '', 0, 'e', 'none', '', '', ''),
(1153, 161, 'Наименование', 'title', 1, 1, '', 1091, 0, '', 0, 'e', 'none', '', '', ''),
(1154, 161, 'Псевдоним', 'alias', 1, 1, '', 1092, 0, '', 0, 'e', 'none', '', '', ''),
(1155, 161, 'Тип', 'type', 10, 5, 'v', 1094, 6, '', 0, 'e', 'one', '', '', ''),
(1156, 161, 'Значение', 'value', 1, 1, '', 1096, 0, '', 0, 'e', 'none', '', '', ''),
(1158, 161, 'Вышестоящая ветка', 'fconfigId', 3, 3, '0', 1093, 161, '', 0, 'e', 'one', '', '', ''),
(1161, 130, 'Социальные сети', 'socialNetworks', 0, 16, '', 1099, 0, '', 0, 'e', 'none', '', '', ''),
(1162, 130, 'ID пользователя в этой соц.сети', 'identifier', 1, 1, '', 1101, 0, '', 0, 'e', 'none', '', '', ''),
(1163, 130, 'Какая', 'sn', 10, 5, 'n', 1100, 6, '', 0, 'e', 'one', '', '', ''),
(1191, 147, 'Не указывать действие при создании seo-урлов из системных', 'blink', 12, 9, '0', 1259, 0, 'if($(this).attr(''checked'')) hide(''tr-rename,tr-alias'');else {show(''tr-rename'');if($(''#rename1'').attr(''checked'')) show(''tr-alias'')}', 0, 'e', 'none', '', '', ''),
(1192, 162, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 1127, 101, '', 0, 'e', 'one', '', '', ''),
(1193, 162, 'Действие в разделе фронтенда', 'fsection2factionId', 3, 3, '0', 1128, 147, '', 1192, 'с', 'one', '', '', ''),
(1194, 162, 'Компонент', 'entityId', 3, 3, '0', 1129, 2, '', 0, 'e', 'one', '', '', ''),
(1195, 162, 'Очередность', 'move', 3, 4, '0', 1130, 0, '', 0, 'e', 'none', '', '', ''),
(1196, 162, 'Префикс', 'prefix', 1, 1, '', 1131, 0, '', 0, 'e', 'none', '', '', ''),
(1244, 104, 'SQL-выражение', 'expression', 1, 1, '', 1177, 0, '', 0, 'e', 'none', '', '', ''),
(1245, 164, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 1179, 101, '', 0, 'e', 'one', '', '`toggle`="y"', ''),
(1246, 164, 'Действие в разделе фронтенда', 'fsection2factionId', 3, 3, '0', 1180, 147, '', 1245, 'с', 'one', '', '', ''),
(1247, 164, 'Тип', 'type', 10, 5, 's', 1183, 6, '', 0, 'e', 'one', '', '', ''),
(1248, 164, 'Наименование', 'title', 1, 1, '', 1181, 0, '', 0, 'e', 'none', '', '', ''),
(1249, 164, 'Содержимое', 'static', 1, 1, '', 1185, 0, '', 0, 'e', 'none', '', '', ''),
(1250, 164, 'Сущность, чье поле будет использовано', 'entityId', 3, 3, '0', 1186, 2, '', 0, 'e', 'one', '', '`system` IN ("n","o") OR `id`="101"', ''),
(1251, 164, 'Содержимое какого поля должно быть компонентом', 'fieldId', 3, 3, '0', 1187, 5, '', 1250, 'с', 'one', '', '', ''),
(1252, 164, 'Где брать идентификатор', 'where', 10, 5, 'c', 1188, 6, '', 0, 'e', 'one', '', '', ''),
(1253, 164, 'Sibling-компонент', 'sibling', 3, 3, '0', 1189, 164, '', 1246, 'с', 'one', '', '', 'fsection2factionId'),
(1254, 164, 'Порядок отображения', 'move', 3, 4, '0', 1190, 0, '', 0, 'e', 'none', '', '', ''),
(1255, 164, 'Префикс', 'prefix', 1, 1, '', 1250, 0, '', 0, 'e', 'none', '', '', ''),
(1256, 164, 'Постфикс', 'postfix', 1, 1, '', 1251, 0, '', 0, 'e', 'none', '', '', ''),
(1257, 165, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 1191, 101, '', 0, 'e', 'one', '', '`toggle`="y"', ''),
(1258, 165, 'Действие в разделе фронтенда', 'fsection2factionId', 3, 3, '0', 1192, 147, '', 1257, 'с', 'one', '', '', ''),
(1259, 165, 'Наименование', 'title', 1, 1, '', 1193, 0, '', 0, 'e', 'none', '', '', ''),
(1260, 165, 'Тип', 'type', 10, 5, 's', 1195, 6, '', 0, 'e', 'one', '', '', ''),
(1261, 165, 'Содержимое', 'static', 1, 1, '', 1197, 0, '', 0, 'e', 'none', '', '', ''),
(1262, 165, 'Сущность, чье поле будет использовано', 'entityId', 3, 3, '0', 1198, 2, '', 0, 'e', 'one', '', '`system`IN ("n","o") OR `id`="101"', ''),
(1263, 165, 'Содержимое какого поля должно быть компонентом', 'fieldId', 3, 3, '0', 1199, 5, '', 1262, 'с', 'one', '', '', ''),
(1264, 165, 'Где брать идентификатор', 'where', 10, 5, 'c', 1200, 6, '', 0, 'e', 'one', '', '', ''),
(1265, 165, 'Sibling-компонент', 'sibling', 3, 3, '0', 1201, 165, '', 1258, 'с', 'one', '', '', 'fsection2factionId'),
(1266, 165, 'Порядок отображения', 'move', 3, 4, '0', 1202, 0, '', 0, 'e', 'none', '', '', ''),
(1267, 165, 'Префикс', 'prefix', 1, 1, '', 1255, 0, '', 0, 'e', 'none', '', '', ''),
(1268, 165, 'Постфикс', 'postfix', 1, 1, '', 1256, 0, '', 0, 'e', 'none', '', '', ''),
(1269, 166, 'Раздел фронтенда', 'fsectionId', 3, 3, '0', 1203, 101, '', 0, 'e', 'one', '', '`toggle`="y"', ''),
(1270, 166, 'Действие в разделе фронтенда', 'fsection2factionId', 3, 3, '0', 1204, 147, '', 1269, 'с', 'one', '', '', ''),
(1271, 166, 'Наименование', 'title', 1, 1, '', 1205, 0, '', 0, 'e', 'none', '', '', ''),
(1272, 166, 'Тип', 'type', 10, 5, 's', 1207, 6, '', 0, 'e', 'one', '', '', ''),
(1273, 166, 'Содержимое', 'static', 1, 1, '', 1209, 0, '', 0, 'e', 'none', '', '', ''),
(1274, 166, 'Сущность, чье поле будет использовано', 'entityId', 3, 3, '0', 1210, 2, '', 0, 'e', 'one', '', '`system` IN ("n","o") OR `id`="101"', ''),
(1275, 166, 'Содержимое какого поля должно быть компонентом', 'fieldId', 3, 3, '0', 1211, 5, '', 1274, 'с', 'one', '', '', ''),
(1276, 166, 'Где брать идентификатор', 'where', 10, 5, 'c', 1212, 6, '', 0, 'e', 'one', '', '', ''),
(1277, 166, 'Sibling-компонент', 'sibling', 3, 3, '0', 1213, 166, '', 1270, 'с', 'one', '', '', 'fsection2factionId'),
(1278, 166, 'Порядок отображения', 'move', 3, 4, '0', 1214, 0, '', 0, 'e', 'none', '', '', ''),
(1279, 166, 'Префикс', 'prefix', 1, 1, '', 1257, 0, '', 0, 'e', 'none', '', '', ''),
(1280, 166, 'Постфикс', 'postfix', 1, 1, '', 1258, 0, '', 0, 'e', 'none', '', '', ''),
(1316, 164, 'Задействовать только если найден идентификатор для компонента', 'seoTitleId', 3, 3, '0', 1182, 164, '', 1246, 'с', 'one', '', '', ''),
(1317, 164, 'Востребованность', 'need', 10, 5, 'a', 1184, 6, '', 0, 'e', 'one', '', '', ''),
(1321, 165, 'Задействовать только если найден идентификатор для компонента', 'seoKeywordId', 3, 3, '0', 1194, 165, '', 1258, 'с', 'one', '', '', ''),
(1322, 165, 'Востребованность', 'need', 10, 5, 'a', 1196, 6, '', 0, 'e', 'one', '', '', ''),
(1323, 166, 'Задействовать только если найден идентификатор для компонента', 'seoDescriptionId', 3, 3, '0', 1206, 166, '', 1270, 'с', 'one', '', '', ''),
(1324, 166, 'Востребованность', 'need', 10, 5, 'a', 1208, 6, '', 0, 'e', 'one', '', '', ''),
(1325, 147, 'Переименовать действие при генерации seo-урла', 'rename', 12, 9, '0', 1260, 0, 'if($(this).attr(''checked'')) show(''tr-alias''); else hide(''tr-alias'');', 0, 'e', 'none', '', '', ''),
(1326, 147, 'Псевдоним', 'alias', 1, 1, '', 1261, 0, '', 0, 'e', 'none', '', '', ''),
(1327, 147, 'Настройки SEO', 'seoSettings', 0, 16, '', 1126, 0, '', 0, 'e', 'none', '', '', ''),
(1331, 168, 'Раздел, который нужно вынести на поддомен', 'fsectionId', 3, 3, '0', 1265, 101, '', 0, 'e', 'one', '', '`fsectionId`=0', ''),
(1337, 10, 'Cущность, экземпляры которой тоже будут иметь доступ к CMS с данным профилем', 'entityId', 3, 3, '0', 1271, 2, '', 0, 'e', 'one', '', '`system`!=''y''', ''),
(1341, 171, 'Раздел', 'sectionId', 3, 3, '0', 1275, 3, '', 0, 'e', 'one', '', '', ''),
(1342, 171, 'Поле, которое должно быть отключено', 'fieldId', 3, 3, '0', 1276, 5, '', 1341, 'с', 'one', 'entityId', '', ''),
(1345, 3, 'Отключить кнопку Add', 'disableAdd', 12, 9, '0', 1277, 0, '', 0, 'e', 'none', '', '', ''),
(1357, 155, 'Вычисляемые столбцы', 'calculatedColumns', 1, 1, '', 927, 0, '', 0, 'e', 'none', '', '', ''),
(1509, 204, 'Ширина', 'detailsHtmlWidth', 3, 18, '0', 1382, 0, 'CKEDITOR.instances[''detailsHtml''].resize(parseInt(this.value)+52);', 0, 'e', 'none', '', '', ''),
(1532, 171, 'Значение по умолчанию', 'defaultValue', 1, 1, '', 1402, 0, '', 0, 'e', 'none', '', '', ''),
(1485, 204, 'Наименование', 'title', 1, 1, '', 1355, 0, '', 0, 'e', 'none', '', '', ''),
(1486, 204, 'Псевдоним', 'alias', 1, 1, '', 1356, 0, '', 0, 'e', 'none', '', '', ''),
(1487, 204, 'Контент', 'detailsHtml', 4, 13, '', 1380, 0, '', 0, 'e', 'none', '', '', ''),
(1488, 204, 'Статус', 'toggle', 10, 5, 'y', 1357, 6, '', 0, 'e', 'one', '', '', ''),
(1489, 205, 'Вышестояший пункт', 'menuId', 3, 3, '0', 1359, 205, '', 0, 'e', 'one', '', '', ''),
(1490, 205, 'Наименование', 'title', 1, 1, '', 1360, 0, '', 0, 'e', 'none', '', '', ''),
(1491, 205, 'Связан со статической страницей', 'linked', 10, 5, 'n', 1361, 6, '', 0, 'e', 'one', '', '', ''),
(1492, 205, 'Статическая страница', 'staticpageId', 3, 3, '0', 1362, 25, '', 0, 'e', 'one', '', '', ''),
(1493, 205, 'Ссылка', 'url', 1, 1, '', 1363, 0, '', 0, 'e', 'none', '', '', ''),
(1494, 205, 'Статус', 'toggle', 10, 3, 'y', 1364, 6, '', 0, 'e', 'one', '', '', ''),
(1535, 7, 'Условия выполнение действия', 'condition', 4, 6, '', 1404, 0, '', 0, 'e', 'none', '', '', ''),
(1496, 205, 'Порядок отображения', 'move', 3, 4, '0', 1366, 0, '', 0, 'e', 'none', '', '', ''),
(1510, 204, 'Содержимое', 'detailsSpan', 0, 16, '', 1358, 0, '', 0, 'e', 'none', '', '', ''),
(1511, 204, 'Высота', 'detailsHtmlHeight', 3, 18, '200', 1383, 0, 'CKEDITOR.instances[''detailsHtml''].resize(parseInt($(''detailsHtmlWidth'').val()),parseInt(this.value)+106);', 0, 'e', 'none', '', '', ''),
(1513, 204, 'Css класс', 'detailsHtmlBodyClass', 1, 1, '', 1384, 0, '', 0, 'e', 'none', '', '', ''),
(1514, 204, 'Стили', 'detailsHtmlStyle', 4, 6, '', 1385, 0, '', 0, 'e', 'none', '', '', ''),
(1515, 204, 'Тип', 'type', 10, 5, 'html', 1379, 6, '', 0, 'e', 'one', '', '', ''),
(1516, 204, 'Значение', 'detailsString', 1, 1, '', 1386, 0, '', 0, 'e', 'none', '', '', ''),
(1517, 204, 'Значение', 'detailsTextarea', 4, 6, '', 1387, 0, '', 0, 'e', 'none', '', '', ''),
(1523, 207, 'Сущность, для экземпляра которой будет исключение', 'entityId', 3, 3, '0', 1393, 2, '', 0, 'e', 'one', '', 'FIND_IN_SET(`id`,"''.current(Indi_Db_Table::getDefaultAdapter()->query(''SELECT GROUP_CONCAT(`entityId`) FROM `fsection` WHERE `toggle`="y"'')->fetch()).''")', ''),
(1524, 207, 'Экземпляр', 'identifier', 3, 21, '0', 1394, 0, '', 1523, 'e', 'one', '', '', ''),
(1525, 207, '&laquo;title&raquo;', 'seoTitle', 1, 1, '', 1395, 0, '', 0, 'e', 'none', '', '', ''),
(1526, 207, '&laquo;meta keywords&raquo;', 'seoKeyword', 4, 6, '', 1396, 0, '', 0, 'e', 'none', '', '', ''),
(1527, 207, '&laquo;meta description&raquo;', 'seoDescription', 4, 6, '', 1397, 0, '', 0, 'e', 'none', '', '', ''),
(1528, 207, 'Статус', 'toggle', 10, 5, 'y', 1398, 6, '', 0, 'e', 'one', '', '', ''),
(1533, 101, 'Статус', 'toggle', 10, 5, 'y', 1403, 6, '', 0, 'e', 'one', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `filter`
--

DROP TABLE IF EXISTS `filter`;
CREATE TABLE IF NOT EXISTS `filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `displayOptions` enum('a','u') NOT NULL DEFAULT 'a',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `type` enum('e','l','b') NOT NULL DEFAULT 'e',
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `fieldId` (`fieldId`),
  KEY `displayOptions` (`displayOptions`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fsection`
--

DROP TABLE IF EXISTS `fsection`;
CREATE TABLE IF NOT EXISTS `fsection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `rppId` int(11) NOT NULL DEFAULT '1',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `filter` varchar(255) NOT NULL,
  `defaultLimit` int(11) NOT NULL DEFAULT '20',
  `orderBy` enum('e','c') NOT NULL DEFAULT 'c',
  `orderColumn` int(11) NOT NULL DEFAULT '0',
  `orderDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `orderExpression` varchar(255) NOT NULL,
  `type` enum('s','r') NOT NULL DEFAULT 'r',
  `where` varchar(255) NOT NULL,
  `index` varchar(255) NOT NULL,
  `toggle` enum('n','y') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `rppId` (`rppId`),
  KEY `entityId` (`entityId`),
  KEY `sectionId` (`sectionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Дамп данных таблицы `fsection`
--

INSERT INTO `fsection` (`id`, `title`, `alias`, `rppId`, `entityId`, `sectionId`, `move`, `fsectionId`, `filter`, `defaultLimit`, `orderBy`, `orderColumn`, `orderDirection`, `orderExpression`, `type`, `where`, `index`, `toggle`) VALUES
(8, 'Пользователи', 'users', 1, 130, 146, 8, 0, '', 20, 'c', 0, 'ASC', '', 'r', '', '', 'n'),
(37, 'Статические страницы', 'static', 1, 25, 30, 28, 0, '', 20, 'c', 0, 'ASC', '', 'r', '', '', 'y'),
(22, 'Фидбэк', 'feedback', 1, 128, 144, 22, 0, '', 20, 'c', 0, 'ASC', '', 's', '""', 'add', 'n'),
(26, 'Личный кабинет', 'myprofile', 1, 130, 0, 25, 0, '', 20, 'c', 0, 'ASC', '', 's', '$_SESSION[''userId'']', 'form', 'n'),
(39, 'Главная', 'index', 1, 0, 0, 26, 0, '', 20, 'c', 0, 'ASC', '', 'r', '', '', 'y'),
(41, 'Карта сайта', 'sitemap', 1, 101, 113, 29, 0, '`toggle`="y"', 20, 'c', 585, 'ASC', '', 'r', '', '', 'y');

-- --------------------------------------------------------

--
-- Структура таблицы `fsection2faction`
--

DROP TABLE IF EXISTS `fsection2faction`;
CREATE TABLE IF NOT EXISTS `fsection2faction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `factionId` int(11) NOT NULL DEFAULT '0',
  `imposition` int(11) NOT NULL DEFAULT '0',
  `type` enum('j','r') NOT NULL DEFAULT 'r',
  `blink` tinyint(1) NOT NULL DEFAULT '0',
  `rename` tinyint(1) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90 ;

--
-- Дамп данных таблицы `fsection2faction`
--

INSERT INTO `fsection2faction` (`id`, `fsectionId`, `factionId`, `imposition`, `type`, `blink`, `rename`, `alias`) VALUES
(3, 8, 2, 0, 'r', 0, 0, ''),
(12, 8, 6, 0, 'r', 0, 0, ''),
(21, 22, 1, 0, 'r', 0, 0, ''),
(30, 26, 3, 0, 'r', 0, 0, ''),
(84, 37, 2, 0, 'r', 0, 0, ''),
(83, 8, 36, 0, 'r', 0, 0, ''),
(86, 39, 1, 0, 'r', 0, 0, ''),
(89, 41, 1, 0, 'r', 0, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `grid`
--

DROP TABLE IF EXISTS `grid`;
CREATE TABLE IF NOT EXISTS `grid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `fieldId` (`fieldId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1450 ;

--
-- Дамп данных таблицы `grid`
--

INSERT INTO `grid` (`id`, `sectionId`, `fieldId`, `move`) VALUES
(1, 2, 1, 1),
(2, 2, 2, 2),
(3, 2, 3, 3),
(4, 5, 4, 4),
(5, 5, 5, 5),
(6, 6, 7, 6),
(7, 6, 8, 7),
(8, 6, 9, 8),
(9, 6, 10, 9),
(10, 6, 11, 10),
(11, 6, 12, 11),
(13, 6, 14, 13),
(14, 7, 19, 16),
(15, 7, 20, 14),
(16, 7, 21, 15),
(17, 7, 22, 17),
(18, 7, 23, 18),
(20, 7, 25, 926),
(23, 8, 27, 23),
(24, 8, 29, 24),
(25, 8, 30, 25),
(26, 10, 31, 26),
(27, 10, 32, 27),
(29, 11, 34, 29),
(30, 11, 35, 30),
(32, 13, 36, 32),
(33, 13, 37, 33),
(34, 12, 16, 34),
(35, 12, 17, 35),
(36, 14, 39, 36),
(37, 14, 40, 37),
(38, 14, 41, 38),
(39, 14, 42, 39),
(42, 16, 65, 43),
(43, 16, 66, 44),
(46, 16, 64, 42),
(89, 22, 107, 56),
(90, 22, 108, 57),
(91, 22, 109, 59),
(92, 22, 110, 60),
(93, 22, 111, 58),
(94, 22, 112, 61),
(130, 30, 131, 61),
(132, 30, 133, 63),
(1442, 235, 1523, 983),
(136, 30, 137, 67),
(341, 12, 377, 253),
(375, 100, 472, 289),
(376, 100, 473, 290),
(377, 100, 474, 291),
(378, 101, 477, 292),
(379, 101, 478, 293),
(383, 8, 28, 297),
(1335, 7, 1366, 31),
(1334, 113, 1040, 925),
(1333, 172, 1365, 924),
(1332, 10, 1364, 923),
(398, 105, 507, 309),
(399, 105, 508, 310),
(400, 105, 509, 311),
(420, 113, 559, 328),
(421, 113, 560, 329),
(422, 114, 561, 329),
(1097, 194, 1259, 808),
(424, 113, 563, 330),
(425, 115, 565, 334),
(426, 115, 567, 760),
(427, 115, 568, 332),
(428, 115, 569, 333),
(429, 116, 571, 338),
(430, 116, 572, 795),
(431, 116, 573, 336),
(433, 119, 582, 339),
(434, 119, 583, 340),
(435, 119, 584, 341),
(440, 122, 597, 345),
(441, 122, 598, 344),
(442, 123, 603, 750),
(443, 5, 612, 347),
(489, 144, 678, 388),
(490, 144, 679, 389),
(491, 144, 681, 391),
(492, 145, 682, 391),
(493, 145, 683, 392),
(494, 146, 684, 392),
(495, 146, 685, 393),
(497, 146, 687, 395),
(498, 146, 688, 396),
(500, 146, 690, 398),
(501, 146, 691, 399),
(1382, 224, 1444, 929),
(509, 146, 699, 407),
(1384, 224, 1445, 928),
(1325, 182, 1357, 707),
(1086, 193, 1247, 798),
(832, 172, 857, 624),
(833, 172, 858, 625),
(834, 173, 860, 626),
(913, 182, 983, 706),
(914, 182, 984, 708),
(915, 182, 985, 709),
(916, 182, 986, 730),
(917, 182, 987, 922),
(920, 183, 996, 712),
(931, 122, 998, 722),
(930, 183, 997, 721),
(928, 185, 1021, 719),
(929, 185, 1022, 720),
(1383, 224, 1443, 926),
(1054, 189, 1103, 773),
(1053, 189, 1102, 772),
(1052, 189, 1101, 771),
(1051, 189, 1100, 770),
(1046, 188, 1088, 766),
(1045, 188, 1087, 764),
(1044, 188, 1086, 763),
(1043, 188, 1085, 765),
(1040, 115, 1075, 335),
(1039, 172, 1074, 759),
(1037, 123, 1000, 757),
(1036, 123, 1071, 751),
(1035, 123, 1016, 756),
(1034, 123, 1015, 755),
(1061, 190, 1153, 778),
(979, 173, 1027, 728),
(978, 182, 991, 705),
(1033, 123, 1014, 754),
(1032, 123, 1013, 753),
(1031, 123, 940, 752),
(1030, 123, 607, 749),
(1029, 123, 606, 346),
(1024, 119, 1044, 744),
(1062, 190, 1154, 779),
(1064, 190, 1156, 780),
(1066, 191, 1194, 782),
(1067, 191, 1195, 783),
(1068, 191, 1196, 784),
(1084, 116, 1244, 794),
(1083, 116, 1243, 337),
(1087, 193, 1248, 797),
(1088, 193, 1249, 800),
(1089, 193, 1250, 801),
(1090, 193, 1251, 802),
(1091, 193, 1252, 803),
(1092, 193, 1253, 804),
(1093, 193, 1254, 805),
(1094, 193, 1255, 806),
(1095, 193, 1256, 836),
(1098, 194, 1260, 809),
(1099, 194, 1261, 811),
(1100, 194, 1262, 812),
(1101, 194, 1263, 813),
(1102, 194, 1264, 814),
(1103, 194, 1265, 815),
(1104, 194, 1266, 816),
(1105, 194, 1267, 817),
(1106, 194, 1268, 837),
(1108, 195, 1271, 819),
(1109, 195, 1272, 820),
(1110, 195, 1273, 822),
(1111, 195, 1274, 823),
(1112, 195, 1275, 824),
(1113, 195, 1276, 825),
(1114, 195, 1277, 826),
(1115, 195, 1278, 827),
(1116, 195, 1279, 828),
(1117, 195, 1280, 838),
(1125, 193, 1317, 799),
(1126, 194, 1322, 810),
(1127, 195, 1324, 821),
(1178, 198, 1331, 845),
(1231, 201, 1342, 851),
(1444, 235, 1525, 984),
(1439, 232, 1515, 965),
(1445, 235, 1528, 985),
(1443, 235, 1524, 982),
(1407, 229, 1490, 950),
(1408, 229, 1491, 951),
(1409, 229, 1492, 952),
(1410, 229, 1493, 953),
(1411, 229, 1494, 954),
(1413, 229, 1496, 956),
(1421, 232, 1485, 962),
(1422, 232, 1486, 963),
(1423, 232, 1488, 980),
(1449, 113, 1533, 989),
(1425, 233, 39, 966),
(1426, 233, 40, 967),
(1427, 233, 41, 968),
(1428, 233, 42, 969),
(1448, 201, 1532, 988);

-- --------------------------------------------------------

--
-- Структура таблицы `independentrowset`
--

DROP TABLE IF EXISTS `independentrowset`;
CREATE TABLE IF NOT EXISTS `independentrowset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `filter` varchar(255) NOT NULL,
  `orderBy` enum('c','s') NOT NULL DEFAULT 'c',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `expression` varchar(255) NOT NULL,
  `orderDirection` enum('asc','desc') NOT NULL DEFAULT 'asc',
  `limit` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `returnAs` enum('o','a') NOT NULL DEFAULT 'o',
  `calculatedColumns` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;

-- --------------------------------------------------------

--
-- Структура таблицы `joinfk`
--

DROP TABLE IF EXISTS `joinfk`;
CREATE TABLE IF NOT EXISTS `joinfk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `returnAs` enum('a','o') NOT NULL DEFAULT 'o',
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `fieldId` (`fieldId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

--
-- Дамп данных таблицы `joinfk`
--

INSERT INTO `joinfk` (`id`, `fsectionId`, `fieldId`, `title`, `fsection2factionId`, `returnAs`) VALUES
(9, 8, 695, 'Стиль', 3, 'o'),
(10, 8, 693, 'Город', 3, 'o'),
(62, 26, 693, 'Город', 30, 'a'),
(63, 26, 692, 'Страна', 30, 'a'),
(99, 39, 1456, 'Статус курса', 86, 'a');

-- --------------------------------------------------------

--
-- Структура таблицы `joinfkfordependentrowset`
--

DROP TABLE IF EXISTS `joinfkfordependentrowset`;
CREATE TABLE IF NOT EXISTS `joinfkfordependentrowset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `dependentRowsetId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `returnAs` enum('o','a') NOT NULL DEFAULT 'o',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Дамп данных таблицы `joinfkfordependentrowset`
--

INSERT INTO `joinfkfordependentrowset` (`id`, `fsectionId`, `fsection2factionId`, `dependentRowsetId`, `fieldId`, `returnAs`) VALUES
(40, 41, 89, 75, 860, 'o');

-- --------------------------------------------------------

--
-- Структура таблицы `joinfkforindependentrowset`
--

DROP TABLE IF EXISTS `joinfkforindependentrowset`;
CREATE TABLE IF NOT EXISTS `joinfkforindependentrowset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `independentRowsetId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `returnAs` enum('o','a') NOT NULL DEFAULT 'o',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Структура таблицы `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `linked` enum('n','y') NOT NULL DEFAULT 'n',
  `staticpageId` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `menu`
--

INSERT INTO `menu` (`id`, `menuId`, `title`, `linked`, `staticpageId`, `url`, `toggle`, `move`) VALUES
(2, 0, 'Контакты', 'y', 1, '', 'y', 2),
(6, 0, 'О компании', 'y', 10, '', 'y', 6);

-- --------------------------------------------------------

--
-- Структура таблицы `metaexclusion`
--

DROP TABLE IF EXISTS `metaexclusion`;
CREATE TABLE IF NOT EXISTS `metaexclusion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `identifier` int(11) NOT NULL DEFAULT '0',
  `seoTitle` varchar(255) NOT NULL,
  `seoKeyword` text NOT NULL,
  `seoDescription` text NOT NULL,
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `metaexclusion`
--

INSERT INTO `metaexclusion` (`id`, `entityId`, `identifier`, `seoTitle`, `seoKeyword`, `seoDescription`, `toggle`) VALUES
(1, 25, 10, 'кастомный заголовок страницы О компании', '', '', 'n');

-- --------------------------------------------------------

--
-- Структура таблицы `orderby`
--

DROP TABLE IF EXISTS `orderby`;
CREATE TABLE IF NOT EXISTS `orderby` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `orderBy` enum('e','f') NOT NULL DEFAULT 'f',
  `expression` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fsectionId` (`fsectionId`),
  KEY `fieldId` (`fieldId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Структура таблицы `param`
--

DROP TABLE IF EXISTS `param`;
CREATE TABLE IF NOT EXISTS `param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `possibleParamId` int(11) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  KEY `possibleParamId` (`possibleParamId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;

--
-- Дамп данных таблицы `param`
--

INSERT INTO `param` (`id`, `fieldId`, `possibleParamId`, `value`) VALUES
(90, 1487, 13, '/css/style.css'),
(38, 980, 4, ''),
(39, 1000, 4, 'записей'),
(40, 12, 1, 'system'),
(41, 19, 1, 'system'),
(42, 563, 1, 'system'),
(43, 603, 1, 'system'),
(46, 983, 1, 'system'),
(64, 1194, 1, 'system'),
(108, 1476, 17, '["/js/jquery-1.9.1.min.js"]'),
(102, 1487, 5, 'true'),
(107, 1476, 13, '["/css/style.css","/css/adjust.css"]'),
(106, 1476, 5, 'true');

-- --------------------------------------------------------

--
-- Структура таблицы `possibleelementparam`
--

DROP TABLE IF EXISTS `possibleelementparam`;
CREATE TABLE IF NOT EXISTS `possibleelementparam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `defaultValue` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Дамп данных таблицы `possibleelementparam`
--

INSERT INTO `possibleelementparam` (`id`, `elementId`, `title`, `alias`, `defaultValue`) VALUES
(1, 3, 'Группировать опции по столбцу', 'groupBy', ''),
(2, 3, 'Условие для столбца группировки', 'groupByRequirement', ''),
(3, 18, 'Максимальная длина в символах', 'maxlength', '5'),
(4, 18, 'Единица измерения', 'measure', ''),
(5, 13, 'Во всю ширину', 'wide', '0'),
(6, 7, 'Количество столбцов', 'cols', '1'),
(7, 13, 'Высота в пикселях', 'height', '200'),
(8, 20, 'Паттерн для дополнения содержимого опций', 'appendPattern', ''),
(9, 20, 'JS при выборе опции', 'js', ''),
(10, 20, 'Дополнительно передавать значения полей', 'additionalData', ''),
(11, 13, 'Ширина в пикселях', 'width', ''),
(12, 13, 'Css класс для body', 'bodyClass', ''),
(13, 13, 'Путь к css-нику для подцепки редактором', 'contentsCss', ''),
(14, 13, 'Стили', 'style', ''),
(15, 14, 'Включать наименование поля в имя файла при download-е', 'appendFieldTitle', 'true'),
(16, 14, 'Включать наименование сущности в имя файла при download-е', 'prependEntityTitle', 'true'),
(17, 13, 'Путь к js-нику для подцепки редактором', 'contentsJs', ''),
(18, 13, 'Скрипт', 'script', ''),
(19, 13, 'Скрипт обработки исходного кода', 'sourceStripper', '');

-- --------------------------------------------------------

--
-- Структура таблицы `profile`
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `entityId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `profile`
--

INSERT INTO `profile` (`id`, `title`, `toggle`, `entityId`) VALUES
(1, 'Конфигуратор', 'y', 0),
(2, 'Администратор', 'y', 0),
(9, 'Модератор', 'y', 0),
(10, 'Демо', 'y', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `resize`
--

DROP TABLE IF EXISTS `resize`;
CREATE TABLE IF NOT EXISTS `resize` (
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
  `color` varchar(7) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fieldId` (`fieldId`),
  KEY `proportions` (`proportions`),
  KEY `masterDimensionAlias` (`masterDimensionAlias`),
  KEY `changeColor` (`changeColor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=142 ;

--
-- Дамп данных таблицы `resize`
--

INSERT INTO `resize` (`id`, `fieldId`, `title`, `alias`, `masterDimensionValue`, `slaveDimensionValue`, `proportions`, `slaveDimensionLimitation`, `masterDimensionAlias`, `changeColor`, `color`) VALUES
(20, 0, 'Для списка', 'thumb', 90, 67, 'c', 1, 'width', 'n', ''),
(54, 1028, 'Для списка отзывов', 'review', 100, 100, 'p', 1, 'height', 'n', '');

-- --------------------------------------------------------

--
-- Структура таблицы `rpp`
--

DROP TABLE IF EXISTS `rpp`;
CREATE TABLE IF NOT EXISTS `rpp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `rpp`
--

INSERT INTO `rpp` (`id`, `title`) VALUES
(1, '10,20,30,40,50'),
(2, '1,2,3,4,5');

-- --------------------------------------------------------

--
-- Структура таблицы `search`
--

DROP TABLE IF EXISTS `search`;
CREATE TABLE IF NOT EXISTS `search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `search`
--

INSERT INTO `search` (`id`, `sectionId`, `fieldId`, `move`, `toggle`) VALUES
(1, 5, 4, 1, 'y'),
(2, 5, 612, 2, 'y'),
(3, 5, 1441, 3, 'y');

-- --------------------------------------------------------

--
-- Структура таблицы `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  `rowsOnPage` int(11) NOT NULL DEFAULT '30',
  `javascript` text NOT NULL,
  `javascriptForm` text NOT NULL,
  `extends` varchar(255) NOT NULL DEFAULT 'Indi_Controller_Admin',
  `defaultSortField` int(11) NOT NULL DEFAULT '0',
  `defaultSortDirection` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `filter` varchar(255) NOT NULL,
  `disableAdd` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('s','p','o') NOT NULL DEFAULT 'p',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `entityId` (`entityId`),
  KEY `toggle` (`toggle`),
  KEY `defaultSortField` (`defaultSortField`),
  KEY `defaultSortDirection` (`defaultSortDirection`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=237 ;

--
-- Дамп данных таблицы `section`
--

INSERT INTO `section` (`id`, `sectionId`, `entityId`, `title`, `alias`, `toggle`, `move`, `rowsOnPage`, `javascript`, `javascriptForm`, `extends`, `defaultSortField`, `defaultSortDirection`, `filter`, `disableAdd`, `type`) VALUES
(1, 0, 0, 'Конфигурация', '', 'y', 131, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(2, 1, 1, 'Столбцы', 'columnTypes', 'y', 4, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(3, 4, 0, 'Выход', 'logout', 'y', 201, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(4, 0, 0, 'Бэкенд', '', 'y', 191, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(5, 1, 2, 'Сущности', 'entities', 'y', 2, 100, '', '', 'Indi_Controller_Admin', 4, 'ASC', '', 0, 's'),
(6, 5, 5, 'Поля в структуре', 'fields', 'y', 7, 30, '', '', 'Indi_Controller_Admin', 14, 'ASC', '', 0, 's'),
(7, 4, 3, 'Разделы', 'sections', 'y', 5, 30, '', '', 'Indi_Controller_Admin', 23, 'ASC', '', 0, 's'),
(8, 7, 8, 'Действия', 'sectionActions', 'y', 8, 30, '', '', 'Indi_Controller_Admin', 30, 'ASC', '', 0, 's'),
(10, 4, 7, 'Действия', 'actions', 'y', 9, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(11, 7, 9, 'Столбцы грида', 'grid', 'y', 10, 30, '', '', 'Indi_Controller_Admin', 35, 'ASC', '', 0, 's'),
(12, 6, 6, 'Возможные значения', 'enumset', 'y', 11, 30, '', '', 'Indi_Controller_Admin', 377, 'ASC', '', 0, 's'),
(13, 4, 10, 'Профили', 'profiles', 'y', 6, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(14, 13, 11, 'Пользователи', 'admins', 'y', 13, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(16, 1, 4, 'Элементы управления', 'controlElements', 'y', 14, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(22, 6, 20, 'Копии изображения', 'resize', 'y', 19, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(29, 0, 0, 'Контент', '', 'y', 113, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 'o'),
(30, 29, 25, 'Статические страницы', 'staticpages', 'y', 200, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 'o'),
(100, 16, 90, 'Возможные параметры настройки', 'possibleParams', 'y', 90, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(101, 6, 91, 'Параметры', 'params', 'y', 91, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(105, 4, 94, 'Настройки', 'config', 'y', 95, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(112, 0, 0, 'Фронтенд', '', 'y', 144, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(113, 112, 101, 'Разделы', 'fsections', 'y', 104, 30, '', '', 'Indi_Controller_Admin', 585, 'ASC', '`toggle`="y"', 0, 's'),
(114, 112, 102, 'Строк постранично', 'rpp', 'y', 177, 30, '', '', 'Indi_Controller_Admin', 561, 'ASC', '', 0, 's'),
(115, 113, 103, 'Фильтры', 'filters', 'y', 105, 30, '', '', 'Indi_Controller_Admin', 565, 'ASC', '', 0, 's'),
(116, 113, 104, 'Сортировать по', 'orderBy', 'y', 106, 30, '', '', 'Indi_Controller_Admin', 572, 'ASC', '', 0, 's'),
(119, 173, 108, 'Зависимые количества', 'counts', 'y', 169, 30, '', '', 'Indi_Controller_Admin', 583, 'ASC', '', 0, 's'),
(122, 173, 110, 'Джойны по внешним ключам', 'joins', 'y', 111, 30, '', '', 'Indi_Controller_Admin', 597, 'ASC', '', 0, 's'),
(123, 173, 111, 'Зависимые множества', 'rowsets', 'y', 108, 30, '', '', 'Indi_Controller_Admin', 603, 'ASC', '', 0, 's'),
(143, 0, 0, 'Обратная связь', '', 'n', 125, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 'o'),
(144, 143, 128, 'Фидбэк', 'feedback', 'n', 135, 30, '', '', 'Indi_Controller_Admin', 681, 'DESC', '', 0, 'o'),
(145, 143, 129, 'Подписчики', 'subscribers', 'n', 165, 30, '', '', 'Indi_Controller_Admin', 682, 'ASC', '', 0, 'o'),
(146, 143, 130, 'Пользователи', 'users', 'n', 134, 30, '', '', 'Indi_Controller_Admin', 691, 'DESC', '', 0, 'o'),
(172, 112, 146, 'Действия', 'factions', 'y', 185, 30, '', '', 'Indi_Controller_Admin', 857, 'ASC', '', 0, 's'),
(173, 113, 147, 'Действия', 'fsection2factions', 'y', 161, 30, '', '', 'Indi_Controller_Admin', 860, 'ASC', '', 0, 's'),
(182, 173, 155, 'Независимые множества', 'independentRowsets', 'y', 110, 30, '', '', 'Indi_Controller_Admin', 983, 'ASC', '', 0, 's'),
(183, 182, 156, 'Джойны по внешним ключам', 'joinFkForIndependentRowsets', 'y', 170, 30, '', '', 'Indi_Controller_Admin', 996, 'ASC', '', 0, 's'),
(185, 123, 158, 'Джойны по внешним ключам', 'joinFkForDependentRowsets', 'y', 172, 30, '', '', 'Indi_Controller_Admin', 0, 'ASC', '', 0, 's'),
(188, 123, 159, 'Зависимые количества', 'countsForDependentRowsets', 'y', 175, 30, '', '', 'Indi_Controller_Admin', 1086, 'ASC', '', 0, 's'),
(189, 143, 160, 'Посетители', 'visitors', 'n', 176, 30, '', '', 'Indi_Controller_Admin', 1101, 'DESC', '', 0, 'o'),
(190, 112, 161, 'Настройки', 'fconfig', 'y', 203, 30, '', '', 'Indi_Controller_Admin', 1153, 'ASC', '', 0, 's'),
(191, 173, 162, 'Компоненты SEO-урла', 'seoUrl', 'y', 178, 30, '', '', 'Indi_Controller_Admin', 1195, 'ASC', '', 0, 's'),
(193, 173, 164, 'Компоненты &laquo;title&raquo;', 'seoTitle', 'y', 180, 30, '', '', 'Indi_Controller_Admin', 1254, 'ASC', '', 0, 's'),
(194, 173, 165, 'Компоненты &laquo;meta keywords&raquo;', 'seoKeywords', 'y', 181, 30, '', '', 'Indi_Controller_Admin', 1266, 'ASC', '', 0, 's'),
(195, 173, 166, 'Компоненты &laquo;meta description&raquo;', 'seoDescription', 'y', 182, 30, '', '', 'Indi_Controller_Admin', 1278, 'ASC', '', 0, 's'),
(198, 112, 168, 'Субдомены', 'subdomains', 'y', 103, 30, '', '', 'Indi_Controller_Admin', 1331, 'ASC', '', 0, 's'),
(201, 7, 171, 'Отключенные поля', 'disabledFields', 'y', 188, 30, '', '', 'Indi_Controller_Admin', 1342, 'ASC', '', 0, 's'),
(224, 7, 195, 'Поля, доступные для поиска', 'search', 'y', 192, 30, '', '', 'Indi_Controller_Admin', 1444, 'ASC', '', 0, 's'),
(229, 29, 205, 'Меню', 'menu', 'y', 197, 30, '', '', 'Indi_Controller_Admin', 1496, 'ASC', '', 0, 'p'),
(232, 29, 204, 'Куски', 'staticblocks', 'y', 202, 30, '', '', 'Indi_Controller_Admin', 1485, 'ASC', '', 1, 'o'),
(233, 4, 11, 'Модераторы', 'moderators', 'y', 186, 30, '', '', 'Indi_Controller_Admin', 39, 'ASC', '`profileId`="9"', 0, 'p'),
(235, 112, 207, 'Meta - исключения', 'metaExclusions', 'y', 160, 30, '', '', 'Indi_Controller_Admin', 1523, 'ASC', '', 0, 's');

-- --------------------------------------------------------

--
-- Структура таблицы `section2action`
--

DROP TABLE IF EXISTS `section2action`;
CREATE TABLE IF NOT EXISTS `section2action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL DEFAULT '0',
  `actionId` int(11) NOT NULL DEFAULT '0',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `move` int(11) NOT NULL DEFAULT '0',
  `profileIds` varchar(255) NOT NULL DEFAULT '1,2,3',
  PRIMARY KEY (`id`),
  KEY `sectionId` (`sectionId`),
  KEY `sectionId_2` (`sectionId`),
  KEY `actionId` (`actionId`),
  KEY `profileIds` (`profileIds`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=950 ;

--
-- Дамп данных таблицы `section2action`
--

INSERT INTO `section2action` (`id`, `sectionId`, `actionId`, `toggle`, `move`, `profileIds`) VALUES
(1, 2, 1, 'y', 1, '1'),
(2, 2, 2, 'y', 2, '1'),
(3, 2, 3, 'y', 3, '1'),
(4, 3, 1, 'y', 4, '1,2,9,10'),
(5, 2, 4, 'n', 5, '1'),
(6, 5, 1, 'y', 6, '1'),
(7, 5, 2, 'y', 7, '1'),
(8, 5, 3, 'y', 8, '1'),
(9, 5, 4, 'y', 9, '1'),
(10, 6, 1, 'y', 10, '1'),
(11, 6, 2, 'y', 11, '1'),
(12, 6, 3, 'y', 12, '1'),
(13, 6, 4, 'y', 13, '1'),
(14, 7, 1, 'y', 14, '1'),
(15, 7, 2, 'y', 15, '1'),
(16, 7, 3, 'y', 16, '1'),
(17, 7, 4, 'y', 17, '1'),
(18, 6, 5, 'y', 18, '1'),
(19, 6, 6, 'y', 19, '1'),
(20, 8, 1, 'y', 20, '1'),
(21, 8, 2, 'y', 21, '1'),
(22, 8, 3, 'y', 22, '1'),
(23, 8, 4, 'y', 23, '1'),
(24, 8, 5, 'y', 24, '1'),
(25, 8, 6, 'y', 25, '1'),
(26, 7, 5, 'y', 26, '1'),
(27, 7, 6, 'y', 27, '1'),
(28, 10, 1, 'y', 28, '1'),
(29, 10, 2, 'y', 29, '1'),
(30, 10, 3, 'y', 30, '1'),
(32, 11, 1, 'y', 31, '1'),
(33, 11, 2, 'y', 32, '1'),
(34, 11, 3, 'y', 33, '1'),
(35, 11, 4, 'y', 34, '1'),
(36, 11, 5, 'y', 35, '1'),
(37, 11, 6, 'y', 36, '1'),
(38, 13, 1, 'y', 37, '1'),
(39, 13, 2, 'y', 38, '1'),
(40, 13, 3, 'y', 39, '1'),
(41, 12, 1, 'y', 40, '1'),
(42, 12, 2, 'y', 41, '1'),
(43, 12, 3, 'y', 42, '1'),
(44, 12, 4, 'y', 43, '1'),
(45, 14, 1, 'y', 44, '1'),
(46, 14, 2, 'y', 45, '1'),
(47, 14, 3, 'y', 46, '1'),
(48, 14, 4, 'y', 47, '1'),
(52, 16, 1, 'y', 51, '1'),
(53, 16, 2, 'y', 52, '1'),
(54, 16, 3, 'y', 53, '1'),
(67, 7, 7, 'y', 58, '1'),
(68, 10, 7, 'y', 59, '1'),
(69, 11, 7, 'y', 60, '1'),
(74, 22, 1, 'y', 65, '1'),
(75, 22, 2, 'y', 66, '1'),
(76, 22, 3, 'y', 67, '1'),
(77, 22, 4, 'y', 68, '1'),
(99, 30, 1, 'y', 69, '1,2,10'),
(100, 30, 2, 'y', 70, '1,2,10'),
(101, 30, 3, 'y', 71, '1,2'),
(102, 30, 4, 'y', 72, '1,2'),
(103, 30, 7, 'y', 73, '1,2,4'),
(329, 12, 5, 'y', 299, '1'),
(330, 12, 6, 'y', 300, '1'),
(373, 100, 1, 'y', 343, '1'),
(374, 100, 2, 'y', 344, '1'),
(375, 100, 3, 'y', 345, '1'),
(376, 100, 4, 'y', 346, '1'),
(377, 101, 1, 'y', 347, '1'),
(378, 101, 2, 'y', 348, '1'),
(379, 101, 3, 'y', 349, '1'),
(380, 101, 4, 'y', 350, '1'),
(808, 13, 4, 'y', 589, '1'),
(807, 105, 4, 'n', 588, '1'),
(806, 10, 4, 'n', 587, '1'),
(397, 105, 1, 'y', 367, '1'),
(398, 105, 2, 'y', 368, '1'),
(399, 105, 3, 'y', 369, '1'),
(429, 113, 1, 'y', 399, '1,2'),
(430, 113, 2, 'y', 400, '1'),
(431, 113, 3, 'y', 401, '1'),
(432, 113, 4, 'y', 402, '1'),
(433, 114, 1, 'y', 403, '1'),
(434, 114, 2, 'y', 404, '1'),
(435, 114, 3, 'y', 405, '1'),
(436, 114, 4, 'y', 406, '1'),
(437, 115, 1, 'y', 407, '1'),
(438, 115, 2, 'y', 408, '1'),
(439, 115, 3, 'y', 409, '1'),
(440, 115, 4, 'y', 410, '1'),
(441, 116, 1, 'y', 411, '1'),
(442, 116, 2, 'y', 412, '1'),
(443, 116, 3, 'y', 413, '1'),
(444, 116, 4, 'y', 414, '1'),
(445, 116, 5, 'y', 415, '1'),
(446, 116, 6, 'y', 416, '1'),
(451, 119, 1, 'y', 421, '1'),
(452, 119, 2, 'y', 422, '1'),
(453, 119, 3, 'y', 423, '1'),
(454, 119, 4, 'y', 424, '1'),
(461, 122, 1, 'y', 431, '1'),
(462, 122, 2, 'y', 432, '1'),
(463, 122, 3, 'y', 433, '1'),
(464, 122, 4, 'y', 434, '1'),
(465, 123, 1, 'y', 435, '1'),
(466, 123, 2, 'y', 436, '1'),
(467, 123, 3, 'y', 437, '1'),
(468, 123, 4, 'y', 438, '1'),
(874, 5, 18, 'y', 607, '1'),
(532, 144, 1, 'y', 133, '1,2,4'),
(533, 144, 2, 'y', 134, '1,2,4'),
(534, 144, 3, 'y', 135, '1,2'),
(535, 144, 4, 'y', 136, '1,2'),
(536, 145, 1, 'y', 134, '1,2,4'),
(537, 145, 2, 'y', 135, '1,2,4'),
(538, 145, 3, 'y', 136, '1,2'),
(539, 145, 4, 'y', 137, '1,2'),
(540, 146, 1, 'y', 135, '1,2,4'),
(541, 146, 2, 'y', 136, '1,2,4'),
(542, 146, 3, 'y', 137, '1,2'),
(543, 146, 4, 'y', 138, '1,2'),
(833, 16, 4, 'n', 598, '1'),
(875, 224, 1, 'y', 608, '1'),
(876, 224, 2, 'y', 609, '1'),
(877, 224, 4, 'y', 611, '1'),
(878, 224, 3, 'y', 610, '1'),
(879, 224, 5, 'y', 612, '1'),
(880, 224, 6, 'y', 613, '1'),
(881, 224, 7, 'y', 614, '1'),
(642, 172, 1, 'y', 161, '1'),
(643, 172, 2, 'y', 162, '1,2,4'),
(644, 172, 3, 'y', 163, '1,2'),
(645, 172, 4, 'y', 164, '1,2'),
(646, 173, 1, 'y', 162, '1,2'),
(647, 173, 2, 'y', 163, '1'),
(648, 173, 3, 'y', 164, '1'),
(649, 173, 4, 'y', 165, '1'),
(699, 182, 1, 'y', 493, '1'),
(700, 182, 2, 'y', 494, '1'),
(701, 182, 3, 'y', 495, '1'),
(702, 182, 4, 'y', 496, '1'),
(703, 183, 1, 'y', 497, '1'),
(704, 183, 2, 'y', 498, '1'),
(705, 183, 3, 'y', 499, '1'),
(706, 183, 4, 'y', 500, '1'),
(712, 185, 1, 'y', 506, '1'),
(713, 185, 2, 'y', 507, '1'),
(714, 185, 3, 'y', 508, '1'),
(715, 185, 4, 'y', 509, '1'),
(726, 188, 1, 'y', 520, '1'),
(727, 188, 2, 'y', 521, '1'),
(728, 188, 3, 'y', 522, '1'),
(729, 188, 4, 'y', 523, '1'),
(730, 189, 1, 'y', 524, '1'),
(731, 189, 2, 'y', 525, '1'),
(732, 189, 3, 'y', 526, '1'),
(733, 189, 4, 'y', 527, '1'),
(736, 190, 1, 'y', 530, '1'),
(737, 190, 2, 'y', 531, '1'),
(738, 190, 3, 'y', 532, '1'),
(739, 190, 4, 'n', 533, '1'),
(740, 191, 1, 'y', 534, '1'),
(741, 191, 2, 'y', 535, '1'),
(742, 191, 3, 'y', 536, '1'),
(743, 191, 4, 'y', 537, '1'),
(744, 191, 5, 'y', 538, '1'),
(745, 191, 6, 'y', 539, '1'),
(751, 193, 1, 'y', 545, '1,2,3,4,5'),
(752, 193, 2, 'y', 546, '1,2,3,4,5'),
(753, 193, 3, 'y', 547, '1,2,3,5'),
(754, 193, 4, 'y', 548, '1,2,3,5'),
(755, 193, 5, 'y', 549, '1,2,3,5'),
(756, 193, 6, 'y', 550, '1,2,3,5'),
(757, 194, 1, 'y', 551, '1,2,3,4,5'),
(758, 194, 2, 'y', 552, '1,2,3,4,5'),
(759, 194, 3, 'y', 553, '1,2,3,5'),
(760, 194, 4, 'y', 554, '1,2,3,5'),
(761, 194, 5, 'y', 555, '1,2,3,5'),
(762, 194, 6, 'y', 556, '1,2,3,5'),
(763, 195, 1, 'y', 557, '1,2,3,4,5'),
(764, 195, 2, 'y', 558, '1,2,3,4,5'),
(765, 195, 3, 'y', 559, '1,2,3,5'),
(766, 195, 4, 'y', 560, '1,2,3,5'),
(767, 195, 5, 'y', 561, '1,2,3,5'),
(768, 195, 6, 'y', 562, '1,2,3,5'),
(776, 198, 1, 'y', 570, '1'),
(777, 198, 2, 'y', 571, '1'),
(778, 198, 3, 'y', 572, '1'),
(779, 198, 4, 'y', 573, '1'),
(789, 201, 1, 'y', 583, '1'),
(790, 201, 2, 'y', 584, '1'),
(791, 201, 3, 'y', 585, '1'),
(792, 201, 4, 'y', 586, '1'),
(898, 229, 1, 'y', 631, '1,2,9'),
(899, 229, 2, 'y', 632, '1,2,9'),
(900, 229, 3, 'y', 633, '1,2,9'),
(901, 229, 4, 'y', 634, '1,2,9'),
(910, 232, 1, 'y', 643, '1,2,9'),
(911, 232, 2, 'y', 644, '1,2,9'),
(912, 232, 3, 'y', 645, '1,2,9'),
(913, 232, 4, 'y', 646, '1'),
(914, 233, 1, 'y', 647, '1,2'),
(915, 233, 2, 'y', 648, '1,2'),
(916, 233, 3, 'y', 649, '1,2'),
(917, 233, 4, 'y', 650, '1,2'),
(918, 233, 7, 'y', 651, '1,2'),
(921, 229, 5, 'y', 654, '1,2,9'),
(922, 229, 6, 'y', 655, '1,2,9'),
(933, 229, 7, 'y', 666, '1,2,9'),
(934, 235, 1, 'y', 667, '1,2'),
(935, 235, 2, 'y', 668, '1,2'),
(936, 235, 3, 'y', 669, '1,2'),
(937, 235, 4, 'y', 670, '1,2'),
(938, 235, 7, 'y', 671, '1,2'),
(939, 232, 7, 'y', 672, '1,2,9'),
(946, 113, 7, 'y', 679, '1'),
(947, 113, 5, 'y', 680, '1,2'),
(948, 113, 6, 'y', 681, '1,2'),
(949, 113, 19, 'y', 682, '1,2');

-- --------------------------------------------------------

--
-- Структура таблицы `seodescription`
--

DROP TABLE IF EXISTS `seodescription`;
CREATE TABLE IF NOT EXISTS `seodescription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `type` enum('s','d') NOT NULL DEFAULT 's',
  `static` varchar(255) NOT NULL,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `where` enum('c','s') NOT NULL DEFAULT 'c',
  `sibling` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(255) NOT NULL,
  `postfix` varchar(255) NOT NULL,
  `seoDescriptionId` int(11) NOT NULL DEFAULT '0',
  `need` enum('a','o') NOT NULL DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Структура таблицы `seokeyword`
--

DROP TABLE IF EXISTS `seokeyword`;
CREATE TABLE IF NOT EXISTS `seokeyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `type` enum('s','d') NOT NULL DEFAULT 's',
  `static` varchar(255) NOT NULL,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `where` enum('c','s') NOT NULL DEFAULT 'c',
  `sibling` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(255) NOT NULL,
  `postfix` varchar(255) NOT NULL,
  `seoKeywordId` int(11) NOT NULL DEFAULT '0',
  `need` enum('a','o') NOT NULL DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Структура таблицы `seotitle`
--

DROP TABLE IF EXISTS `seotitle`;
CREATE TABLE IF NOT EXISTS `seotitle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `type` enum('d','s') NOT NULL DEFAULT 's',
  `title` varchar(255) NOT NULL,
  `static` varchar(255) NOT NULL,
  `entityId` int(11) NOT NULL DEFAULT '0',
  `fieldId` int(11) NOT NULL DEFAULT '0',
  `where` enum('s','c') NOT NULL DEFAULT 'c',
  `sibling` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(255) NOT NULL,
  `postfix` varchar(255) NOT NULL,
  `seoTitleId` int(11) NOT NULL DEFAULT '0',
  `need` enum('a','o') NOT NULL DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

--
-- Дамп данных таблицы `seotitle`
--

INSERT INTO `seotitle` (`id`, `fsectionId`, `fsection2factionId`, `type`, `title`, `static`, `entityId`, `fieldId`, `where`, `sibling`, `move`, `prefix`, `postfix`, `seoTitleId`, `need`) VALUES
(59, 22, 21, 's', '', 'Обратная связь', 0, 0, 'c', 0, 57, '', '', 0, 'a'),
(70, 37, 84, 'd', 'Название страницы', '', 25, 131, 'c', 0, 63, '', '', 0, 'a'),
(73, 41, 89, 'd', 'Название раздела', '', 101, 559, 'c', 0, 66, '', '', 0, 'a');

-- --------------------------------------------------------

--
-- Структура таблицы `staticblock`
--

DROP TABLE IF EXISTS `staticblock`;
CREATE TABLE IF NOT EXISTS `staticblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `detailsHtml` text NOT NULL,
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `detailsHtmlWidth` int(11) NOT NULL DEFAULT '0',
  `detailsHtmlHeight` int(11) NOT NULL DEFAULT '200',
  `detailsHtmlBodyClass` varchar(255) NOT NULL,
  `detailsHtmlStyle` text NOT NULL,
  `type` enum('html','string','textarea') NOT NULL DEFAULT 'html',
  `detailsString` varchar(255) NOT NULL,
  `detailsTextarea` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `staticblock`
--

INSERT INTO `staticblock` (`id`, `title`, `alias`, `detailsHtml`, `toggle`, `detailsHtmlWidth`, `detailsHtmlHeight`, `detailsHtmlBodyClass`, `detailsHtmlStyle`, `type`, `detailsString`, `detailsTextarea`) VALUES
(1, 'Seo-текст внизу главной страницы', 'index-page-seo-text', '<p>Идейные соображения высшего порядка, а также сложившаяся структура организации в значительной степени обуславливает создание системы обучения кадров, соответствует насущным потребностям. Значимость этих проблем настолько очевидна, что дальнейшее развитие различных форм деятельности требуют от нас анализа модели развития. С другой стороны новая модель организационной деятельности в значительной степени обуславливает создание направлений прогрессивного развития. Идейные соображения высшего порядка, а также новая модель организационной деятельности позволяет оценить значение дальнейших направлений развития. Идейные соображения высшего порядка, а также постоянный количественный рост и сфера нашей активности способствует подготовки и реализации систем массового участия. Товарищи! постоянное информационно-пропагандистское обеспечение нашей деятельности позволяет выполнять важные задания по разработке позиций, занимаемых участниками в отношении поставленных задач.</p>\r\n', 'y', 890, 200, 'text', 'body{background-image: none;}', 'html', '', ''),
(2, 'Блок "Попробуйте бесплатно"', 'index-page-try-free', '<div class="free">\r\n	<div class="title_free">\r\n		Попробуйте бесплатно!</div>\r\n	<p>\r\n		Отправьте нам любую вашу фотографию и получите бесплатную оценку от нашего преподавателя.</p>\r\n	<a href="#"><span>Отправить фото</span><em>Бесплатно. Правда.</em></a></div>\r\n<p>\r\n	&nbsp;</p>\r\n', 'y', 400, 250, '', '.free{margin:0}', 'html', '', ''),
(3, 'Блок отзывов над слайдером', 'index-page-reviews', '<div class="comment">\r\n<div class="first_comment"><a class="first_comment_img" href="#"><img alt="" src="/www/data/upload/fck/Image/img.jpg" style="width: 123px; height: 84px;" /></a> <span class="comment_text">Индивидуальная работа с преподавателем! Всё было супер! Спасибо!</span> <em>Екатерина,<br />\r\nвыпускница</em></div>\r\n<!--end first_comment -->\r\n\r\n<div class="second_comment"><a class="first_comment_img" href="#"><img alt="" src="/www/data/upload/fck/Image/img2.jpg" style="width: 95px; height: 62px;" /></a> <span class="comment_text">Люблю фотографировать пейзажи. И вас научу!</span> <em>Стас, преподаватель</em></div>\r\n<!--end first_comment --></div>\r\n\r\n<p>&nbsp;</p>\r\n', 'y', 800, 230, '', '.comment{float: none; margin-left:5px;}', 'html', '', ''),
(4, 'Cлоган', 'slogan', '', 'y', 0, 0, '', '', 'textarea', '', 'Фотошкола\r\nонлайн'),
(5, 'Текст копирайта', 'copyright', '<p class="cop">\r\n	Все права защищены.<br />\r\n	<br />\r\n	Любое копирование без уведомления администрации запрещено и преследуется по<br />\r\n	законам Российской Федерации.</p>\r\n', 'y', 685, 100, '', '.cop{float: none;} body{background: url(/images/footer.png) repeat-x 0 -80px;}', 'html', '', ''),
(6, 'Блок отзывов и работ выпускников на странице описания курса', 'course-details-sidebar', '<div class="sidebar_title sidebar_title_second">Отзывы о курсе</div>\r\n\r\n<div class="com"><img alt="" src="/www/data/upload/fck/Image/img.jpg" style="width: 44px; height: 44px;" /> <span>&ldquo;</span>\r\n\r\n<p>Идейные соображения высшего порядка, а также сложившаяся структура организации</p>\r\n</div>\r\n<!--end com --><span class="com_h">&nbsp;</span>\r\n\r\n<div class="com_name">Иванова Марина, 22 года</div>\r\n\r\n<div class="line_sb">&nbsp;</div>\r\n\r\n<div class="sidebar_title"><a href="#">Работы выпускников курса</a></div>\r\n\r\n<div class="jobs"><a href="#"><img alt="" src="/www/data/upload/fck/Image/img.jpg" style="width: 251px; height: 168px;" /></a></div>\r\n<!--end sidebar -->\r\n\r\n<div class="line_sb">&nbsp;</div>\r\n', 'y', 270, 0, '', 'body{background: none;margin-left:10px;}', 'html', '', ''),
(7, 'Путь к favicon', 'favicon-path', '', 'y', 0, 200, '', '', 'string', '/www/data/upload/fck/Image/favicon2.ico', '');

-- --------------------------------------------------------

--
-- Структура таблицы `staticpage`
--

DROP TABLE IF EXISTS `staticpage`;
CREATE TABLE IF NOT EXISTS `staticpage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `toggle` enum('y','n') NOT NULL DEFAULT 'y',
  `details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `toggle` (`toggle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `staticpage`
--

INSERT INTO `staticpage` (`id`, `title`, `alias`, `toggle`, `details`) VALUES
(1, 'Контакты', 'contacts', 'y', 'Здесь будут контакты'),
(6, 'Страница не найдена', '404', 'y', 'Запрашиваемая вами страница не найдена'),
(10, 'О компании', 'about', 'y', 'Здесь будет о компании');

-- --------------------------------------------------------

--
-- Структура таблицы `subdomain`
--

DROP TABLE IF EXISTS `subdomain`;
CREATE TABLE IF NOT EXISTS `subdomain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `subscriber`
--

DROP TABLE IF EXISTS `subscriber`;
CREATE TABLE IF NOT EXISTS `subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Структура таблицы `url`
--

DROP TABLE IF EXISTS `url`;
CREATE TABLE IF NOT EXISTS `url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fsectionId` int(11) NOT NULL DEFAULT '0',
  `fsection2factionId` int(11) NOT NULL DEFAULT '0',
  `entityId` int(11) NOT NULL DEFAULT '0',
  `move` int(11) NOT NULL DEFAULT '0',
  `prefix` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `gender` enum('m','f','n') NOT NULL DEFAULT 'n',
  `birth` date NOT NULL,
  `registration` date NOT NULL,
  `subscribed` tinyint(1) NOT NULL DEFAULT '0',
  `lastVisit` datetime NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `activationCode` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `sn` enum('n','fb','vk','mm') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `gender` (`gender`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `visitor`
--

DROP TABLE IF EXISTS `visitor`;
CREATE TABLE IF NOT EXISTS `visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `lastActivity` datetime NOT NULL,
  `userId` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=528982 ;

--
-- Дамп данных таблицы `visitor`
--

INSERT INTO `visitor` (`id`, `title`, `lastActivity`, `userId`, `hidden`) VALUES
(528979, 'kkgirqg2lmrd4hgcuerlbju0q4', '2013-03-26 21:36:31', 0, 0),
(528981, '', '2013-03-26 21:36:36', 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

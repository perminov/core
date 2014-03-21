<?php
define('I_LOGIN_BOX_USERNAME', 'Пользователь');
define('I_LOGIN_BOX_PASSWORD', 'Пароль');
define('I_LOGIN_BOX_ENTER', 'Вход');
define('I_LOGIN_BOX_RESET', 'Сброс');
define('I_LOGIN_ERROR_MSGBOX_TITLE', 'Ошибка');
define('I_LOGIN_ERROR_ENTER_YOUR_USERNAME', 'Укажите имя пользователя');
define('I_LOGIN_ERROR_ENTER_YOUR_PASSWORD', 'Укажите пароль');

define('I_LOGIN_ERROR_NO_SUCH_ACCOUNT', 'Нет такого аккаунта');
define('I_LOGIN_ERROR_WRONG_PASSWORD', 'Вы ввели неправильный пароль');
define('I_LOGIN_ERROR_ACCOUNT_IS_OFF', 'Ваш аккаунт отключен');
define('I_LOGIN_ERROR_PROFILE_IS_OFF', 'Тип вашего аккаунта отключен');
define('I_LOGIN_ERROR_NO_ACCESSIBLE_SECTIONS', 'В системе пока нет ни одного доступного для вас раздела');

define('I_THROW_OUT_ACCOUNT_DELETED', 'Ваш аккаунт только что был удален');
define('I_THROW_OUT_PASSWORD_CHANGED', 'Ваш пароль только что был изменен');
define('I_THROW_OUT_ACCOUNT_IS_OFF', 'Ваш аккаунт только что был отключен');
define('I_THROW_OUT_PROFILE_IS_OFF', 'Тип вашего аккаунта только что был отключен');
define('I_THROW_OUT_NO_ACCESSIBLE_SECTIONS', 'В системе не осталось ни одного доступного для вас раздела');

define('I_ACCESS_ERROR_NO_SUCH_SECTION', 'Нет такого раздела');
define('I_ACCESS_ERROR_SECTION_IS_OFF', 'Этот раздел выключен');
define('I_ACCESS_ERROR_NO_SUCH_ACTION', 'Нет такого действия');
define('I_ACCESS_ERROR_ACTION_IS_OFF', 'Это действие выключено');
define('I_ACCESS_ERROR_NO_SUCH_ACTION_IN_SUCH_SECTION', 'Нет такого действия в этом разделе');
define('I_ACCESS_ERROR_ACTION_IS_OFF_IN_SUCH_SECTION', 'Это действие выключено в этом разделе');
define('I_ACCESS_ERROR_ACTION_IS_NOT_ACCESSIBLE', 'У вас нет прав на это действие в этом разделе');
define('I_ACCESS_ERROR_ONE_OF_PARENT_SECTIONS_IS_OFF', 'Один из вышестоящих разделов для этого раздела отключен');

define('I_YES', 'Да');
define('I_NO', 'Нет');

define('I_LOGOUT', 'Выход');

define('I_MENU', 'Меню');
define('ACTION_CREATE', 'Создать');
define('GRID_WARNING_SELECTROW_MSG', 'Выберите строку');
define('GRID_WARNING_SELECTROW_TITLE', 'Сообщение');
define('GRID_SUBSECTIONS_LABEL', 'Подразделы');
define('GRID_SUBSECTIONS_EMPTY_OPTION', '--Выберите--');
define('GRID_SUBSECTIONS_SEARCH_LABEL', 'Искать');
define('BUTTON_BACK', 'Вернуться');
define('BUTTON_SAVE', 'Сохранить');

define('FORM_UPLOAD_REMAIN', 'Оставить');
define('FORM_UPLOAD_DELETE', 'Удалить');
define('FORM_UPLOAD_REPLACE', 'Заменить');
define('FORM_UPLOAD_REPLACE_WITH', 'на');
define('FORM_UPLOAD_NO', 'Отсутствует');
define('FORM_UPLOAD_BROWSE', 'Выбрать');
define('FORM_UPLOAD_ORIGINAL', 'Оригинал');

define('FORM_DATETIME_HOURS', 'часов');
define('FORM_DATETIME_MINUTES', 'минут');
define('FORM_DATETIME_SECONDS', 'секунд');

define('COMBO_OF', 'из');

define('FORM_SELECT_EMPTY_OPTION', 'Выберите');

define('ENUMSET_DELETE_DENIED_LASTVALUE', 'Нельзя удалять последнее значение из набора возможных');
define('ENUMSET_DELETE_DENIED_DEFAULTVALUE', 'Нельзя удалять значение по умолчанию');

define('GRID_FILTER', 'Фильтры');
define('GRID_FILTER_CHECKBOX_YES', 'Да');
define('GRID_FILTER_CHECKBOX_NO', 'Нет');
define('GRID_FILTER_OPTION_DEFAULT', 'Неважно');
define('GRID_FILTER_DATE_FROM', 'с');
define('GRID_FILTER_DATE_UNTIL', 'по');
define('GRID_FILTER_NUMBER_FROM', 'от');
define('GRID_FILTER_NUMBER_TO', 'до');

define('MSGBOX_CONFIRM_TITLE', 'Подтверждение');
define('MSGBOX_CONFIRM_MESSAGE', 'Вы уверены?');

define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_TITLE', 'Запись не найдена');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_START', 'Среди набора записей, доступных в рамках данного раздела,');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_SPM', ' с учетом текущих параметров поиска');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_END', ' - нет записи с таким ID');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_TITLE', 'Запись #');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_OF', 'из ');

define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_TITLE', 'Запись не найдена');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_START', 'Среди набора записей, доступных в рамках данного раздела,');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_SPM', ' с учетом текущих параметров поиска');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_END', ' - нет записи с таким порядковым номером, но на момент загрузки формы она была');
define('I_ACTION_FORM_TOPBAR_NAVTOSUBSECTION_NO_SUBSECTIONS', 'Отсутствуют');
define('I_ACTION_FORM_TOPBAR_NAVTOSUBSECTION_SELECT', '--Выберите--');

define('I_ACTION_INDEX_KEYWORD_LABEL', 'Искать');
define('I_ACTION_INDEX_SUBSECTIONS_LABEL', 'Подразделы');
define('I_ACTION_INDEX_SUBSECTIONS_VALUE', '--Выберите--');
define('I_ACTION_INDEX_SUBSECTIONS_NO', 'Отсутствуют');
define('I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE', 'Сообщение');
define('I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG', 'Выберите строку');
define('I_ACTION_INDEX_FILTER_TOOLBAR_TITLE', 'Фильтры');
define('I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM', 'от');
define('I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO', 'до');
define('I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM', 'c');
define('I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO', 'по');
define('I_ACTION_INDEX_FILTER_TOOLBAR_CHECK_YES', 'Да');
define('I_ACTION_INDEX_FILTER_TOOLBAR_CHECK_NO', 'Нет');
define('I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_TITLE', 'Сброс всех фильтров');
define('I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_MSG', 'Фильтры уже сброшены или на текущий момент не используются вовсе');

define('I_ACTION_DELETE_CONFIRM_TITLE', 'Подтверждение');
define('I_ACTION_DELETE_CONFIRM_MSG', 'Вы уверены?');

define('I_TOTAL', 'Всего');
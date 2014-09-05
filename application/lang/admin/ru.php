<?php
define('I_URI_ERROR_SECTION_FORMAT', 'Имя раздела имеет неправильный формат');
define('I_URI_ERROR_ACTION_FORMAT', 'Имя действия имеет неправильный формат');
define('I_URI_ERROR_ID_FORMAT', 'Параметр \'id\' должен быть имеет целым положительным числом');

define('I_LOGIN_BOX_USERNAME', 'Пользователь');
define('I_LOGIN_BOX_PASSWORD', 'Пароль');
define('I_LOGIN_BOX_REMEMBER', 'Запомнить');
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
define('I_ACCESS_ERROR_ROW_ADDING_DISABLED', 'Право на создание записей недоступно в этом разделе');
define('I_ACCESS_ERROR_ROW_DOESNT_EXIST', 'Нет записи с таким id в этом разделе');

define('I_DOWNLOAD_ERROR_NO_ID', 'Идентификатор объекта или не указан, или не является числом');
define('I_DOWNLOAD_ERROR_NO_FIELD', 'Идентификатор поля или не указан, или не является числом');
define('I_DOWNLOAD_ERROR_NO_SUCH_FIELD', 'Нет поля с таким идентификатором');
define('I_DOWNLOAD_ERROR_FIELD_DOESNT_DEAL_WITH_FILES', 'Поле с этим идентификатором не работает с файлами');
define('I_DOWNLOAD_ERROR_NO_SUCH_ROW', 'Нет объекта с таким идентификатором');
define('I_DOWNLOAD_ERROR_NO_FILE', 'Файл, загруженный в указанное поле для указанного объекта - не существует');
define('I_DOWNLOAD_ERROR_FILEINFO_FAILED', 'Не удалось получить сведения о файле');

define('I_ENUMSET_DEFAULT_VALUE_BLANK_TITLE', 'Заголовок для значения по умолчанию \'%s\'');
define('I_ENUMSET_ERROR_VALUE_ALREADY_EXISTS', 'Значение "%s" уже присутствует в списке возможных значений');
define('I_ENUMSET_ERROR_VALUE_LAST', 'Значение "%s" - последнее оставшееся значение в списке возможных, и поэтому не может быть удалено');

define('I_YES', 'Да');
define('I_NO', 'Нет');

define('I_HOME', 'Начало');
define('I_LOGOUT', 'Выход');
define('I_MENU', 'Меню');
define('I_CREATE', 'Создать новую запись');
define('I_BACK', 'Вернуться');
define('I_SAVE', 'Сохранить');
define('I_TOTAL', 'Всего');
define('I_EXPORT_EXCEL', 'Экспортировать в Excel');
define('I_NAVTO_ROWSET', 'Вернуться к списку');
define('I_NAVTO_ID', 'Перейти к записи по ID');
define('I_AUTOSAVE', 'Автосохранять перед переходами');
define('I_NAVTO_PREV', 'Перейти к предыдущей записи');
define('I_NAVTO_SIBLING', 'Перейти к любой другой записи');
define('I_NAVTO_NEXT', 'Перейти к следующей записи');
define('I_NAVTO_CREATE', 'Перейти к созданию новой записи');
define('I_NAVTO_NESTED', 'Перейти к списку вложенных записей');
define('I_NAVTO_ROWINDEX', 'Перейти к записи #');

define('I_ROWSAVE_ERROR_VALUE_CANT_BE_OBJECT', 'Значением поля "%s" не может быть объект');
define('I_ROWSAVE_ERROR_VALUE_CANT_BE_ARRAY', 'Значением поля "%s" не может быть массив');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_INT11', 'Значение "%s" поля "%s" должно быть целым числом, имеющим не более 11 разрядов');
define('I_ROWSAVE_ERROR_VALUE_IS_NOT_ALLOWED', 'Значение "%s" поля "%s" отсутствует в списке допустимых значений');
define('I_ROWSAVE_ERROR_VALUE_CONTAINS_UNALLOWED_ITEMS', 'В поле "%s" присутствуют недопустимые значения: "%s"');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_LIST_OF_NON_ZERO_DECIMALS', 'Значение "%s" поля "%s" содержит как минимум один элемент не являющийся ненулевым целым числом');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_BOOLEAN', 'Значение "%s" поля "%s" должно быть "1" или "0"');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_COLOR', 'Значение "%s" поля "%s" не является цветом в форматах #rrggbb или hue#rrggbb');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DATE', 'Значение "%s" поля "%s" не является датой');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_VALID_DATE', 'Значение "%s" поля "%s" не является корректной датой');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_TIME', 'Значение "%s" поля "%s" не является временем в формате ЧЧ:ММ:СС');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_VALID_TIME', 'Время "%s" поля "%s" не является корректным временем');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_DATE', 'Значение "%s", указанное в поле "%s" в качестве даты - не является датой');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_VALID_DATE', 'Дата "%s", указанная в поле "%s" - должна быть корректной');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_TIME', 'Значение "%s", указанное в поле "%s" в качестве времени - не является временем');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_VALID_TIME', 'Время "%s", указанное в поле "%s" - должно быть корректным');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DOUBLE72', 'Значение "%s" поля "%s" должно быть числом имеющим не более 5 разрядов в целочисленной части, и не более 2-х - в дробной');
define('I_ROWSAVE_ERROR_VALUE_SHOULD_BE_YEAR', 'Значение "%s" поля "%s" не является годом в формате ГГГГ');
define('I_ROWSAVE_ERROR_VALUE_TREECOLUMN_INVALID', 'Текущая запись не может быть указана как родительская для самой себя в поле "%"');
define('I_ROWFILE_ERROR_MKDIR', 'Создание директории "%s" в папке "%s" не удалось, несмотря на то что папка доступна для записи');
define('I_ROWFILE_ERROR_UPPER_DIR_NOT_WRITABLE', 'Создание директории "%s" в папке "%s" не удалось, так как эта папка недоступна для записи');
define('I_ROWFILE_ERROR_TARGET_DIR_NOT_WRITABLE', 'Директория "%s", необходимая для загрузки, - существует, но недоступна для записи');
define('I_ROWFILE_ERROR_NONEXISTENT_ROW', 'Нельзя работать с файлами, относящимися к несуществующим записям');

define('I_UPLOAD_ERR_INI_SIZE', 'Размер файла, выбранного для загрузки в поле "%s", превысил максимально допустимый размер, заданный директивой upload_max_filesize конфигурационного файла php.ini');
define('I_UPLOAD_ERR_FORM_SIZE', 'Размер файла, выбанного для загрузки в поле "%s" превысил значение MAX_FILE_SIZE, указанное в HTML-форме');
define('I_UPLOAD_ERR_PARTIAL', 'Файл, выбранный для загрузки в поле "%s" был получен только частично');
define('I_UPLOAD_ERR_NO_FILE', 'Файл, выбранный в поле "%s" -  не был загружен');
define('I_UPLOAD_ERR_NO_TMP_DIR', 'На сервере отсутствует временная папка для загрузки файла из поля "%s"');
define('I_UPLOAD_ERR_CANT_WRITE', 'Файл, выбранный для загрузки в поле "%s", не удалось записать на жесткий диск сервера');
define('I_UPLOAD_ERR_EXTENSION', 'Одно из PHP-расширений, работающих на сервере, остановило загрузку файла из поля "%s"');
define('I_UPLOAD_ERR_UNKNOWN', 'Загрузка файла в поле "%s" не удалась из-за неизвестной ошибки');

define('I_WGET_ERR_ZEROSIZE', 'Загрузка файла в поле "%s" с использованием веб-ссылки не удалась, так как этот файл пустой');

define('I_FORM_UPLOAD_SAVETOHDD', 'Сохранить на диск');
define('I_FORM_UPLOAD_ORIGINAL', 'Показать оригинал');
define('I_FORM_UPLOAD_NOCHANGE', 'Оставить');
define('I_FORM_UPLOAD_DELETE', 'Удалить');
define('I_FORM_UPLOAD_REPLACE', 'Заменить');
define('I_FORM_UPLOAD_REPLACE_WITH', 'на');
define('I_FORM_UPLOAD_NOFILE', 'Отсутствует');
define('I_FORM_UPLOAD_BROWSE', 'Выбрать');
define('I_FORM_UPLOAD_MODE_TIP', 'Загрузить по веб-ссылке');
define('I_FORM_UPLOAD_MODE_LOCAL_PLACEHOLDER', 'файл с вашего ПК..');
define('I_FORM_UPLOAD_MODE_REMOTE_PLACEHOLDER', 'файл по веб-ссылке..');

define('I_FORM_DATETIME_HOURS', 'часов');
define('I_FORM_DATETIME_MINUTES', 'минут');
define('I_FORM_DATETIME_SECONDS', 'секунд');
define('I_COMBO_OF', 'из');

define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_TITLE', 'Запись не найдена');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_START', 'Среди набора записей, доступных в рамках данного раздела,');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_SPM', ' с учетом текущих параметров поиска');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_END', ' - нет записи с таким ID');
define('I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE', 'Запись #');
define('I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_OF', 'из ');

define('I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_NOT_FOUND_MSGBOX_TITLE', 'Запись не найдена');
define('I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_NOT_FOUND_MSGBOX_MSG_START', 'Среди набора записей, доступных в рамках данного раздела,');
define('I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_NOT_FOUND_MSGBOX_MSG_SPM', ' с учетом текущих параметров поиска');
define('I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_NOT_FOUND_MSGBOX_MSG_END', ' - нет записи с таким порядковым номером, но на момент загрузки формы она была');
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
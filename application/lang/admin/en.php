<?php
define('I_LOGIN_BOX_USERNAME', 'Username');
define('I_LOGIN_BOX_PASSWORD', 'Password');
define('I_LOGIN_BOX_ENTER', 'Enter');
define('I_LOGIN_BOX_RESET', 'Reset');
define('I_LOGIN_ERROR_MSGBOX_TITLE', 'Error');
define('I_LOGIN_ERROR_ENTER_YOUR_USERNAME', 'Enter your username');
define('I_LOGIN_ERROR_ENTER_YOUR_PASSWORD', 'Enter your password');

define('I_LOGIN_ERROR_NO_SUCH_ACCOUNT', 'Such account does not exist');
define('I_LOGIN_ERROR_WRONG_PASSWORD', 'Wrong password');
define('I_LOGIN_ERROR_ACCOUNT_IS_OFF', 'Your account is switched off');
define('I_LOGIN_ERROR_PROFILE_IS_OFF', 'Your account is of type, that is switched off');
define('I_LOGIN_ERROR_NO_ACCESSIBLE_SECTIONS', 'There is no sections accessible for you yet');

define('I_THROW_OUT_ACCOUNT_DELETED', 'Your account had just been deleted');
define('I_THROW_OUT_PASSWORD_CHANGED', 'Your password had just been changed');
define('I_THROW_OUT_ACCOUNT_IS_OFF', 'Your account had just beed switched off');
define('I_THROW_OUT_PROFILE_IS_OFF', 'Your account is of type, that had just been switched off');
define('I_THROW_OUT_NO_ACCESSIBLE_SECTIONS', 'Now there is no sections remaining accessible for you');

define('I_ACCESS_ERROR_NO_SUCH_SECTION', 'Such section does not exist');
define('I_ACCESS_ERROR_SECTION_IS_OFF', 'Section is switched off');
define('I_ACCESS_ERROR_NO_SUCH_ACTION', 'Such action does not exist');
define('I_ACCESS_ERROR_ACTION_IS_OFF', 'This action is switched off');
define('I_ACCESS_ERROR_NO_SUCH_ACTION_IN_SUCH_SECTION', 'This action does not exist in this section');
define('I_ACCESS_ERROR_ACTION_IS_OFF_IN_SUCH_SECTION', 'This action is switched off in this section');
define('I_ACCESS_ERROR_ACTION_IS_NOT_ACCESSIBLE', 'You have no rights on this action in this section');
define('I_ACCESS_ERROR_ONE_OF_PARENT_SECTIONS_IS_OFF', 'One of parent sections for current section - is switched off');

define('I_YES', 'Yes');
define('I_NO', 'No');

define('I_LOGOUT', 'Logout');

define('I_MENU', 'Menu');
define('ACTION_CREATE', 'Create');
define('GRID_WARNING_SELECTROW_MSG', 'Select a row');
define('GRID_WARNING_SELECTROW_TITLE', 'Message');
define('GRID_SUBSECTIONS_LABEL', 'Subsections');
define('GRID_SUBSECTIONS_EMPTY_OPTION', '--Select--');
define('GRID_SUBSECTIONS_SEARCH_LABEL', 'Search');
define('BUTTON_BACK', 'Back');
define('BUTTON_SAVE', 'Save');

define('FORM_UPLOAD_REMAIN', 'No change');
define('FORM_UPLOAD_DELETE', 'Delete');
define('FORM_UPLOAD_REPLACE', 'Replace');
define('FORM_UPLOAD_REPLACE_WITH', 'with');
define('FORM_UPLOAD_NO', 'No');
define('FORM_UPLOAD_BROWSE', 'Browse');
define('FORM_UPLOAD_ORIGINAL', 'Original');

define('FORM_DATETIME_HOURS', 'hours');
define('FORM_DATETIME_MINUTES', 'minutes');
define('FORM_DATETIME_SECONDS', 'seconds');

define('COMBO_OF', 'of');

define('FORM_SELECT_EMPTY_OPTION', 'Select');

define('ENUMSET_DELETE_DENIED_LASTVALUE', 'Deleting of last existing item from the set is not allowed');
define('ENUMSET_DELETE_DENIED_DEFAULTVALUE', 'Deleting of default value is not allowed');

define('GRID_FILTER', 'Options');
define('GRID_FILTER_CHECKBOX_YES', 'Yes');
define('GRID_FILTER_CHECKBOX_NO', 'No');
define('GRID_FILTER_OPTION_DEFAULT', 'Any');
define('GRID_FILTER_DATE_FROM', 'from');
define('GRID_FILTER_DATE_UNTIL', 'until');
define('GRID_FILTER_NUMBER_FROM', 'between');
define('GRID_FILTER_NUMBER_TO', 'and');

define('MSGBOX_CONFIRM_TITLE', 'Confirm');
define('MSGBOX_CONFIRM_MESSAGE', 'Are you sure?');

define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_TITLE', 'Row is not found');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_START', 'The current section\'s scope of available rows');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_SPM', ', in view with applied search options -');
define('I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_END', ' does not contain a row with such an ID');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_TITLE', 'Row #');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_OF', 'of ');

define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_TITLE', 'Row is not found');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_START', 'The scope of rows that are available in current section,');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_SPM', ' in view with applied search options');
define('I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_END', ' - does not contain a row with such an index, but it recently did');
define('I_ACTION_FORM_TOPBAR_NAVTOSUBSECTION_NO_SUBSECTIONS', 'No');
define('I_ACTION_FORM_TOPBAR_NAVTOSUBSECTION_SELECT', '--Select--');

define('I_ACTION_INDEX_KEYWORD_LABEL', 'Search');
define('I_ACTION_INDEX_SUBSECTIONS_LABEL', 'Subsections');
define('I_ACTION_INDEX_SUBSECTIONS_VALUE', '--Select--');
define('I_ACTION_INDEX_SUBSECTIONS_NO', 'No');
define('I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE', 'Message');
define('I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG', 'Select a row');
define('I_ACTION_INDEX_FILTER_TOOLBAR_TITLE', 'Options');
define('I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM', 'between');
define('I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO', 'and');
define('I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM', 'from');
define('I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO', 'until');
define('I_ACTION_INDEX_FILTER_TOOLBAR_CHECK_YES', 'Yes');
define('I_ACTION_INDEX_FILTER_TOOLBAR_CHECK_NO', 'No');
define('I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_TITLE', 'Nothing to be emptied');
define('I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_MSG', 'Options are already empty or not used at all');


define('I_ACTION_DELETE_CONFIRM_TITLE', 'Confirm');
define('I_ACTION_DELETE_CONFIRM_MSG', 'Are you sure?');

define('I_TOTAL', 'Total');
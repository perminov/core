<?php

// Displays phpinfo if needed
if(isset($_GET['info'])){phpinfo();die();}

// Set up STD server variable in case if multiple IndiEngine projects
// are running within same document root, and there is one project that
// is located in DOCUMENT_ROOT and others are in subfolders, so STD server
// variable is passed WITH 'REDIRECT_' prefix, which is not covered by engine
if (!$_SERVER['STD'] && $_SERVER['REDIRECT_STD']) $_SERVER['STD'] = $_SERVER['REDIRECT_STD'];

// Setup $_SERVER['STD'] as php constant, for being easier accessible
define('STD', $_SERVER['STD']);

// Setup $GLOBALS['cmsOnlyMode'] as php constant, for being easier accessible
define('COM', $GLOBALS['cmsOnlyMode']);

// Setup PRE constant, representing total url shift for all urls in cms area
define('PRE', STD . (COM ? '' : '/admin'));

// Setup DOC constant, representing $_SERVER['DOCUMENT_ROOT'] environment variable, with no right-side slash
define('DOC', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));

// Setup URI constant, representing $_SERVER['REQUEST_URI'] environment variable, for short-hand accessibility
define('URI', rtrim($_SERVER['REQUEST_URI'], '/'));

// Set up error reporting
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 'On');

// Set include path. Here we add more include paths, in case if some stuff is related to front module only,
// but required to be available in admin module.
$dirs = array('../www/', (COM || preg_match('~^' . preg_quote(STD, '~') . '/admin\b~', URI) ? '' : '../coref/'), '../core/');
$subs = array('library', 'application/controllers', 'application/models'); $p = PATH_SEPARATOR;
foreach($dirs as $d) if ($d) foreach($subs as $s) $inc[] = $d . $s; $inc[] = get_include_path();
set_include_path(implode($p, $inc));

// Load misc functions
require('func.php');

// Register autoloader
spl_autoload_register('autoloader');

// Set up error handlers for fatal errors, and other errors
register_shutdown_function('ehandler');
set_error_handler('ehandler');

// Performance detection. 'mt' mean 'microtime'
$mt = 0; function mt(){$m = microtime();list($mc, $s) = explode(' ', $m); $n = $s + $mc; $ret = $n - $GLOBALS['last']; $GLOBALS['last'] = $n; return $ret;} mt();

// Memory usage detection
$mu = 0; function mu(){$m = memory_get_usage(); $ret = $m - $GLOBALS['mu']; $GLOBALS['mu'] = $m; return number_format($ret);} mu();

// Load config and setup DB interface
Indi::ini('application/config.ini');
if (function_exists('geoip_country_code_by_name')
    && geoip_country_code_by_name($_SERVER['REMOTE_ADDR']) == 'GB')
        Indi::ini('lang')->admin = 'en';
Indi::cache();
Indi::db(Indi::ini()->db);

// Save config and global request data to registry
Indi::post($_POST);
Indi::get($_GET);
Indi::files($_FILES);
unset($_POST, $_GET, $_FILES);

// Dispatch uri request
Indi::uri()->dispatch();
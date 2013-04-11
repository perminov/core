<?php
// Displays phpinfo if needed
if(isset($_GET['info'])){phpinfo();die();}

// Set up error reporting
error_reporting(E_ALL^E_NOTICE);
ini_set('display_errors', 'On');

// Set include path
$dirs = array('../www/', '../core/'); $subs = array('library', 'application/controllers', 'application/models'); $p = PATH_SEPARATOR;
foreach($dirs as $d) foreach($subs as $s) $inc[] = $d . $s; $inc[] = get_include_path(); set_include_path(implode($p, $inc));

// Set autoloading
function autoloader($class){if (preg_match('/Admin_[a-zA-z]*Controller$/',$class)) $class = lcfirst($class);$classFile = str_replace('_','/',$class).'.php';if(!@include_once ($classFile)) if (strpos($class, 'admin') === false) echo "";}
spl_autoload_register('autoloader');

// Load misc features
require('Misc.php');

// Performance detection
$last = 0; function mt(){$m = microtime();list($mc, $s) = explode(' ', $m); $n = $s + $mc; $ret = $n - $GLOBALS['last']; $GLOBALS['last'] = $n; return $ret;} mt();

// Load config
$config = Misc::ini('application/config.ini');

// Filter globals
Indi_Registry::set('post', $_POST);
Indi_Registry::set('get', $_GET);
Indi_Registry::set('files', $_FILES);
Indi_Registry::set('config', $config);
unset($_POST, $_GET, $_FILES);

// Setup DB interface
$db = Indi_Db::factory($config['db']);
Indi_Db_Table::setDefaultAdapter($db);
$db->query('SET NAMES utf8');
$db->query('SET CHARACTER SET utf8');

// Dispatch uri request
$uri = new Indi_Uri(); $uri->dispatch();

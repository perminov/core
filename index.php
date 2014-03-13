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

// Performance detection. '$mt' mean 'microtime'
$mt = 0; function mt(){$m = microtime();list($mc, $s) = explode(' ', $m); $n = $s + $mc; $ret = $n - $GLOBALS['last']; $GLOBALS['last'] = $n; return $ret;} mt();

// Memory usage detection
$mu = 0; function mu(){$m = memory_get_usage(); $ret = $m - $GLOBALS['mu']; $GLOBALS['mu'] = $m; return $ret;} mu();

// Load config and setup DB interface
$config = Indi::ini('application/config.ini');
Indi::db($config->db);

// Save config and global request data to registry
Indi::registry('post', $_POST);
Indi::registry('get', $_GET);
Indi::registry('files', $_FILES);
Indi::registry('config', $config);
unset($_POST, $_GET, $_FILES);
/*mt();
$t = Indi::model('Search')
    ->fetchAll('`sectionId` = "394" AND `toggle` = "y"', 'move')
    ->foreign(array('fieldId:setParams()' => array('columnTypeId' => 'elementId', 'elementId')))
    ->select('126,127,130', 'id');
//    ->exclude('126');
d(Indi_Db::$queryCount);
d($t);
die('ss');*/

// Dispatch uri request
$uri = new Indi_Uri(); $uri->dispatch();

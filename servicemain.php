<?php
//Include XOOPS Global Includes
error_reporting(E_ALL); //Enable Error Reporting
$xoopsOption['nocommon'] = 1;

if (defined('XOOPS_TEST')) {
  //require XOOPS_TEST_ROOT_PATH.'/mainfile.php';
  require('../../../mainfile.php');
  
  
} else {
  require('../../mainfile.php');
}          

require_once('include/functions.php');
require_once('include/consts.php');
require_once XOOPS_ROOT_PATH.'/kernel/object.php';
require_once XOOPS_ROOT_PATH.'/class/criteria.php';
require_once XOOPS_ROOT_PATH.'/include/functions.php';
include_once XOOPS_ROOT_PATH.'/class/logger.php';
include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsuser.php';  

define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache");
define("XOOPS_UPLOAD_PATH", XOOPS_ROOT_PATH."/uploads");
define("XOOPS_THEME_PATH", XOOPS_ROOT_PATH."/themes");
define("XOOPS_COMPILE_PATH", XOOPS_ROOT_PATH."/templates_c");
define("XOOPS_THEME_URL", XOOPS_URL."/themes");
define("XOOPS_UPLOAD_URL", XOOPS_URL."/uploads");

//if (!defined('XOOPS_CONF_USER'))
//  define('XOOPS_CONF_USER', 2);

//Initialize XOOPS Logger
$xoopsLogger =& XoopsLogger::instance();
$xoopsLogger->startTime();  

//Initialize DB Connection
include_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';
$xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();  
//
$module_handler =& xoops_gethandler('module');      
$xoopsModule =& $module_handler->getByDirname(REPORTS_MODULE_DIR); 
$module =& $module_handler;
//
$config_handler =& xoops_gethandler('config');
$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
$xoopsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
//

if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1 || $xoopsModule->getVar( 'hasnotification' ) == 1) {
    $xoopsModuleConfig =& $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
}

//Include XOOPS Language Files
if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
    include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
} else {
    include_once XOOPS_ROOT_PATH."/language/english/global.php";
}

if ( file_exists(XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/".$xoopsConfig['language']."/main.php") ) {
    include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/".$xoopsConfig['language']."/main.php";
} else {
    include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/language/english/main.php";
}
?>

<?php
error_reporting(E_ALL);

require_once('../include/consts.php');
require_once('../include/functions.php');

require(XSTREAMER_ADMIN_PATH.'/admin_buttons.php');
require_once(XOOPS_ROOT_PATH.'/class/template.php');

if (file_exists(XSTREAMER_BASE_PATH."/language/".$xoopsConfig['language']."/main.php") ) {
	include XSTREAMER_BASE_PATH."/language/".$xoopsConfig['language']."/main.php";
} else {
    include XSTREAMER_BASE_PATH."/language/english/main.php";
}

if (file_exists(XSTREAMER_BASE_PATH."/language/".$xoopsConfig['language']."/modinfo.php") ) {
	include XSTREAMER_BASE_PATH."/language/".$xoopsConfig['language']."/modinfo.php";
} else {
    include XSTREAMER_BASE_PATH."/language/english/modinfo.php";
}

//require_once('../include/images.php');
 
global $xoopsModule; 
$module_id = $xoopsModule->getVar('mid');

$oAdminButton = new AdminButtons(); 
$oAdminButton->AddTitle(sprintf(_MI_XSTREAMER_ADMIN_TITLE, $xoopsModule->getVar('name')));
$oAdminButton->AddButton(_MI_XSTREAMER_MENU_MANAGE_CATEGORY, 'index.php?class=category', 'manCat');
$oAdminButton->AddButton(_MI_XSTREAMER_MENU_MANAGE_VIDEOS, 'index.php?class=video', 'manVideo');
$oAdminButton->AddTopLink(_MI_XSTREAMER_MENU_PREFERENCES, XOOPS_URL .'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='. $module_id);
$oAdminButton->AddTopLink(_MI_XSTREAMER_UPDATE_MODULE, XOOPS_URL .'/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module='.XSTREAMER_MODULE_DIR);
//
$myts = &MyTextSanitizer::getInstance();

global $xoopsTpl; 
if ( !isset($xoopsTpl) ) {
  $xoopsTpl = new XoopsTpl(); }

?>

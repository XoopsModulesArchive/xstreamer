<?php

include '../../mainfile.php';
require_once('include/functions.php');

$com_itemid = isset($_GET['com_itemid']) ? intval($_GET['com_itemid']) : 0;
if ($com_itemid > 0) {
	// Get video name
  $hVideo =& getXTModuleHandler('video');
  $oVideo =& $hVideo->get($com_itemid);
  $com_replytitle = $oVideo->name();
  //
  include XOOPS_ROOT_PATH.'/include/comment_new.php';
}
?>

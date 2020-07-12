<?php

// comment callback functions
if (!defined('XOOPS_ROOT_PATH')) {
  die("XOOPS root path not defined");
}

require_once('functions.php');

function news_com_update($videoID, $total){
  $hVideo =& getXTModuleHandler('video');
  //
  $oVideo = $hVideo->get($videoID);
  return $hVideo->updateComments($oVideo,$total);
}

function news_com_approve(&$comment){
  // notification mail here
}
?>
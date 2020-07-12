<?php

if (!defined('XOOPS_XMLRPC')) 
  define('XOOPS_XMLRPC', 1);

require_once("../../mainfile.php");
require_once("include/functions.php");

//disable debugging if on as it breaks xajax
error_reporting(0);
if (isset($xoopsErrorHandler))
  $xoopsErrorHandler->activate(false);
if (isset($xoopsLogger) && is_object($xoopsLogger)) {
  $xoopsLogger->activated = false;
}


function onVote($videoID, $rating) {
  //first send a waiting 
  $objResponse = new xajaxResponse(); 
  $objResponse->addAssign("unit_ul","innerHTML", '<div class="loading"></div>');
  $objResponse->addScript('xajax_onVoteReload('.$videoID.','.$rating.');');
  return $objResponse;
}
////////////////////////////////////////////////////
function onVoteReload($videoID,$rating) {
  //now save and reload
  $hVideo =& getXTModuleHandler('video');
  //
  if ($oVideo =& $hVideo->get($videoID)) {
    $oVideo->addVote($rating);
    $hVideo->insert($oVideo);
    //render voting bar and spit out
    $newVote =& $oVideo->getVoteBar(true);
  } else {
    $newVote = 'Invalid Video';
  }
  // 
  $objResponse = new xajaxResponse(); 
  $objResponse->addAssign("mainRatingBlock","innerHTML", $newVote);
  //
  return $objResponse;
}
////////////////////////////////////////////////////
//function onVideoPlay($videoID) {
//  $hVideo =& getXTModuleHandler('video');
//  $oVideo =& $hVideo->get($videoID);
//  //increment played counter
//  $hVideo->incrementPlayed($oVideo);
//  //
//  $objResponse = new xajaxResponse(); 
//  $objResponse->addAssign("mainVideoBlock","innerHTML", $oVideo->getVideoPlayer());
//  $objResponse->addAssign("videoName","innerHTML", $oVideo->getVideoLinkTitle());
//  $objResponse->addAssign("mainRatingBlock","innerHTML", $oVideo->getVoteBar());
//  $objResponse->addScript('renderPlayer('.$oVideo->ID().','.$oVideo->videoFileSize().')');
//  //
//  return $objResponse;
//}

$hAjax =& getXTModuleHandler('ajax');
$oAjax =& $hAjax->create();
//
$oAjax->registerFunction('onVote');
$oAjax->registerFunction('onVoteReload');
//$oAjax->registerFunction('onVideoPlay');
//
$oAjax->processRequests();


?>

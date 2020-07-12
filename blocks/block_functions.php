<?php

require_once(XSTREAMER_BASE_PATH.'/include/functions.php');

//get list of categories
function b_xstreamer_categories() {
  $hCat =& getXTModuleHandler('category');
  //
  $oCats =& $hCat->getCategoryNameOrder();
  $block = array();
  foreach($oCats as $key=>$oCategory) {
    $block[$key]['id']   = $oCategory->ID();
    $block[$key]['name'] = $oCategory->name();
  }
  return $block;
}
//////////////////////////////////////////////////
function b_xstreamer_most_viewed() {
  $hVideo =& getXTModuleHandler('video');
  $aoVids =& $hVideo->getMostViewed();
  $block = array();
  //
  foreach($aoVids as $key=>$oVideo) {
    $block[$key]['id'] = $oVideo->ID();
    $block[$key]['name'] = $oVideo->name();
    $block[$key]['views'] = $oVideo->views();
  }
  return $block;
}
/////////////////////////////////////////////////
function b_xstreamer_highest_rated() {
  $hVideo =& getXTModuleHandler('video');
  $aoVids =& $hVideo->getHighestRated();
  $block = array();
  //
  foreach($aoVids as $key=>$oVideo) {
    $block[$key]['id'] = $oVideo->ID();
    $block[$key]['name'] = $oVideo->name();
    $block[$key]['rating'] = $oVideo->rating();
  }
  return $block;
}
////////////////////////////////////////////////
function b_xstreamer_most_commented() {
  $hVideo =& getXTModuleHandler('video');
  $aoVids =& $hVideo->getMostCommented();
  $block = array();
  //
  foreach($aoVids as $key=>$oVideo) {
    $block[$key]['id'] = $oVideo->ID();
    $block[$key]['name'] = $oVideo->name();
    $block[$key]['comments'] = $oVideo->comments();
  }
  return $block;
}
///////////////////////////////////////////////
function b_xstreamer_most_rated() {
  $hVideo =& getXTModuleHandler('video');
  $aoVids =& $hVideo->getMostRated();
  $block = array();
  //
  foreach($aoVids as $key=>$oVideo) {
    $block[$key]['id'] = $oVideo->ID();
    $block[$key]['name'] = $oVideo->name();
    $block[$key]['votes'] = $oVideo->votes();
  }
  return $block;
}
///////////////////////////////////////////////
function b_xstreamer_best_videos() {
  $hVideo =& getXTModuleHandler('video');
  $aoVids =& $hVideo->getBestRated();
  $block = array();
  //
  foreach($aoVids as $key=>$oVideo) {
    $block[$key]['id'] = $oVideo->ID();
    $block[$key]['name'] = $oVideo->name();
    $block[$key]['rating'] = $oVideo->rating();
  }
  return $block;
}

?>


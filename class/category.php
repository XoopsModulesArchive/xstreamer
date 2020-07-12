<?php

require_once('PSPBaseObject.php');

class xStreamerCategory extends XTBaseObject {

  function xStreamerCategory($id = null) {
    $this->initVar('id', XOBJ_DTYPE_INT, null, false);
    $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 255);
    //
    if (isset($id)) {
      if (is_array($id)) {
        $this->assignVars($id);
      }
    } else {
      $this->setNew();
    }
  }
  ///////////////////////////////////////////////
  function name() {
    return $this->getVar('name');
  }
}

class xStreamerCategoryHandler extends XTBaseObjectHandler {
  //cons
  function xStreamerCategoryHandler(&$db)  {
    $this->persistClass($db, 'xStreamerCategory', 'xstreamer_category'); 
  }
  ///////////////////////////////////////////////////
  function &getInstance(&$db)
  {
      static $instance;
      if(!isset($instance)) {
          $instance = new xStreamerCategoryHandler($db);
      }
      return $instance;
  }
  ///////////////////////////////////////////////////
  function &getCategoriesSelect() {
    $hCommon =& getXTModuleHandler('common');
    //
    $crit =& new Criteria('id');
    $crit->setSort('name');
    //
    $objs =& $this->getObjects($crit);
    $ary  =& $hCommon->objectsToSelect($objs,'name');
    return $ary;
  }
  ///////////////////////////////////////////////////
  function getCategoryNameOrder() {
    $crit =& new Criteria('id');
    $crit->setSort('name');
    $objs =& $this->getObjects($crit);
    return $objs;
  }
}

?>

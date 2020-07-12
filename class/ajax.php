<?php

require_once('PSPBaseObject.php');
require_once 'xajax/xajax.inc.php';

class xStreamerajax extends xajax {
  
  function xStreamerajax() {
    parent::xajax();
    //
    //$this->bDebug = true;
  }
  //////////////////////////////////////////////////
  function getHeaderCode($location = 'class/xajax') {
    return parent::getJavascript($location);
  }
  /////////////////////////////////////////////////
  function registerFunction($function,$url = null, $method = XAJAX_POST) {
    if (isset($url))
      $this->sRequestURI = $url;
    //      
    parent::registerFunction($function,$method);
  }
}


class xStreamerajaxHandler extends XTBaseObjectHandler {
  //cons
  function xStreamerajaxHandler(&$db)  {
    $this->classname = 'xStreamerajax';
    $this->db = $db;
  }
  ///////////////////////////////////////////////////
  function &getInstance(&$db)
  {
      static $instance;
      if(!isset($instance)) {
          $instance = new xStreamerajaxHandler($db);
      }
      return $instance;
  }
}

?>

<?php

require_once('PSPBaseObject.php');

class xStreamerVote extends XTBaseObject {

  function xStreamerVote($id = null) {
    $this->initVar('id', XOBJ_DTYPE_INT, null, false);
    $this->initVar('video_id', XOBJ_DTYPE_INT, null, false);
    $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
    $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false,15);
    $this->initVar('rating', XOBJ_DTYPE_INT, null, false);
    $this->initVar('date_voted', XOBJ_DTYPE_INT, null, false);
    //
    if (isset($id)) {
      if (is_array($id)) {
        $this->assignVars($id);
      }
    } else {
      $this->setNew();
    }
  }
}

class xStreamerVoteHandler extends XTBaseObjectHandler {
  //cons
  function xStreamerVoteHandler(&$db)  {
    $this->persistClass($db, 'xStreamerVote', 'xstreamer_vote'); 
  }
  ///////////////////////////////////////////////////
  function &getInstance(&$db)
  {
      static $instance;
      if(!isset($instance)) {
          $instance = new xStreamerVoteHandler($db); 
      }
      return $instance;
  }
  //////////////////////////////////////////////////
  function addVideoVote($oVideo,$rating) {
    global $xoopsUser;
    //
    $obj =& $this->create();
    $obj->setVar('video_id',$oVideo->ID());
    $obj->setVar('date_voted',time());
    $obj->setVar('ip',getenv("REMOTE_ADDR"));
    $obj->setVar('rating',$rating);
    //
    if ($xoopsUser)
      $obj->setVar('uid',$xoopsUser->uid());
    //
    return $this->insert($obj);
  }
}

?>

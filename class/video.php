<?php

require_once('PSPBaseObject.php');

class xStreamerVideo extends XTBaseObject {

  function xStreamerVideo($id = null) {
    $this->initVar('id', XOBJ_DTYPE_INT, null, false);
    $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
    $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 255, '', 'Video Name');
    $this->initVar('width', XOBJ_DTYPE_INT, null, false);
    $this->initVar('height', XOBJ_DTYPE_INT, null, false);
    $this->initVar('filepath', XOBJ_DTYPE_TXTBOX, null, true, 255, '', 'FLV File Path');
    $this->initVar('description', XOBJ_DTYPE_TXTBOX, null, false, 64000);
    $this->initVar('date_added', XOBJ_DTYPE_INT, null, true);
    $this->initVar('rating', XOBJ_PSP_DTYPE_FLOAT, 0, false);
    $this->initVar('views', XOBJ_DTYPE_INT, 0, false);
    $this->initVar('votes', XOBJ_DTYPE_INT, 0, false);
    $this->initVar('comments', XOBJ_DTYPE_INT, 0, false);
    $this->initVar('visible', XOBJ_DTYPE_INT, 1, false);
    //
    if (isset($id)) {
      if (is_array($id)) {
        $this->assignVars($id);
      }
    } else {
      $this->setNew();
    }
  }
  ////////////////////////////////////////////////////
  function name() {
    return $this->getVar('name');
  }
  ////////////////////////////////////////////////////
  function filePath() {
    return $this->getVar('filepath');
  }
  ////////////////////////////////////////////////////
  function categoryID() {
    return $this->getVar('category_id');
  }
  ////////////////////////////////////////////////////
  function width() {
    $hCommon =& getXTModuleHandler('common');
    //
    if ($this->getVar('width') > 0)
      return $this->getVar('width');
    else
     return $hCommon->getModuleOption('defPlayerWidth'); 
  }
  ////////////////////////////////////////////////////
  function height() {
    $hCommon =& getXTModuleHandler('common');
    //
    if ($this->getVar('height') > 0)
      return $this->getVar('height');
    else
     return $hCommon->getModuleOption('defPlayerHeight'); 
  }
  ////////////////////////////////////////////////////
  function rating() {
    if ($this->getVar('votes') > 0)
      return $this->getVar('rating')/$this->getVar('votes');
    else
      return 0;
  }
  ////////////////////////////////////////////////////
  function totalRating() {
    return $this->getVar('rating');
  }
  ////////////////////////////////////////////////////
  function views() {
    return $this->getVar('views');
  }
  ////////////////////////////////////////////////////
  function comments() {
    return $this->getVar('comments');
  }
  ////////////////////////////////////////////////////
  function votes() {
    return $this->getVar('votes');
  }
  ////////////////////////////////////////////////////
  function videoFileSize() {
    return filesize($this->getVar('filepath'));
  }
  ////////////////////////////////////////////////////
  function getVideoLinkTitle() {
    return '<a href="http://xprojects.co.uk">xStreamer Video Player</a> : Playing : '.$this->name();
  }
  ////////////////////////////////////////////////////
  function categoryName() {
    if ($oCat =& $this->category())
      return $oCat->name();
  }
  ///////////////////////////////////////////////////
  function &category() { 
    $hCat =& getXTModuleHandler('category');
    $oCat = $hCat->get($this->getVar('category_id'));
    //
    if (is_object($oCat))
      return $oCat;
    else
      return false;
  }
  ////////////////////////////////////////////////////
  function &getArray() {
    $ary =& parent::getArray();
    $ary['width'] = $this->width();
    $ary['height'] = $this->height();
    $ary['rating'] = $this->rating();
    $ary['videoFileSize'] = $this->videoFileSize();
    $ary['categoryName']  = $this->categoryName();
    //
    return $ary;
  }
  ////////////////////////////////////////////////////
  function setVarsFromArray($post) {
    if ($this->isNew()) {
      $this->setVar('date_added',time());
    }
    parent::setVarsFromArray($post);
  }
  ////////////////////////////////////////////////////
  function validate() {
    $hThis =& getXTModuleHandler('video');
    //When creating a new video
    if (($this->isNew()) or ($hThis->checkIfChangedAgainstDB($this, 'name'))) { 
      if ($hThis->videoNameExists($this->name()))
        $this->setErrors('video','Video with this name already exists.');     
    } 
    //more checks
    if ($this->name() == '')
      $this->setErrors('name','Video name is required.');
    if (!file_exists($this->filePath()))
      $this->setErrors('filepath','File not found at this location. Check file path and file permissions.');
    if (!($this->categoryID() > 0))
      $this->setErrors('category_id','Category is required');
  }
  //////////////////////////////////////////////////
  function incrementVotes() {
    $this->setVar('votes',$this->getVar('votes')+1);
  }
  //////////////////////////////////////////////////
  function addVote($rating) {
    $hVote =& getXTModuleHandler('vote');
    //
    $this->incrementVotes();
    $this->setVar('rating',$this->getVar('rating') + $rating);
    //
    return $hVote->addVideoVote($this,$rating);
  }
  ///////////////////////////////////////////////////
  function getVoteBar($thankYou = false) {
    global $xoopsTpl;
    require_once(XOOPS_ROOT_PATH.'/class/template.php');
    //
    $tpl =& new XoopsTpl();
    if (is_object($xoopsTpl))
      $tpl->assign($xoopsTpl->get_template_vars());
    // 
    $tpl->assign('xstreamer_video',$this->getArray());
    $tpl->assign('xstreamer_thank_you',$thankYou); 
    $tpl->assign('xstreamer_read_only',$thankYou); 
    //
    return $tpl->fetch('db:xstreamer_voting_bar.html');
  }
  /////////////////////////////////////////////////
  function getVideoPlayer() {
    global $xoopsTpl;
    require_once(XOOPS_ROOT_PATH.'/class/template.php');
    //
    $tpl =& new XoopsTpl();
    if (is_object($xoopsTpl))
      $tpl->assign($xoopsTpl->get_template_vars());
    // 
    $tpl->assign('xstreamer_video',$this->getArray());
    //
    return $tpl->fetch('db:xstreamer_player_code.html');
  }
  /////////////////////////////////////////////////
  function streamVideo($position=null) {
    require_once(XSTREAMER_CLASS_PATH.'/video/video.php');
    //
    $hCommon =& getXTModuleHandler('common');
    //
    if (!($position > 0)) {
      $hThis   =& getXTModuleHandler('video');
      $hThis->incrementPlayed($this);
    }
    //
    $bandwidth  = $hCommon->getModuleOption('bandwidthThrottleValue');
    //
    $video =& new Video();
    $video->setFile($this->filePath()); 
    $video->setBitrate($bandwidth*1024);
    $video->enableThrottle($hCommon->getModuleOption('bandwidthThrottle') == 1);  
    $video->streamVideo($position);  
  }
  
}

class xStreamerVideoHandler extends XTBaseObjectHandler {
  //cons
  function xStreamerVideoHandler(&$db)  {
    $this->persistClass($db, 'xStreamerVideo', 'xstreamer_video'); 
  }
  ///////////////////////////////////////////////////
  function &getInstance(&$db)  {
    static $instance;
    if(!isset($instance)) {
      $instance = new xStreamerVideoHandler($db);
    }
    return $instance;
  }
  //////////////////////////////////////////////////
  function &getArray($crit = null) {
    $hCat =& getXTModuleHandler('category');
    //always sort by name
    if (!isset($crit)) {
      $crit = new Criteria('id');
      $crit->setSort('name');
    }
    //
    $thisTable = $this->prefixedTable();
    $catTable  = $hCat->prefixedTable();
    //
    $sql = "select v.*, c.name categoryName from $thisTable v inner join $catTable c on
              v.category_id = c.id";
    if (isset($crit))
      $this->postProcessSQL($sql,$crit);
    //
    $ary =& $this->sqlToArray($sql); 
    return $ary;
  }
  //////////////////////////////////////////////////
  function captureDebug($sql) {
    //print_r($this->db);
  }
  //////////////////////////////////////////////////
  function &getVisibleSelect() {
    $ary = array(1 => 'yes', 0 => 'no');
    return $ary;
  }
  //////////////////////////////////////////////////
  function videoNameExists($name) {
    $crit =& new Criteria('name',$name);
    return $this->getCount($crit) > 0;
  }
  //////////////////////////////////////////////////
  function &getFirstCategoryVideo($cat) {
    if(is_object($cat))
      $catID = $cat->ID();
    else
      $catID = $cat;
    //
    $crit =& new CriteriaCompo(new Criteria('category_id',$catID)); 
    $crit->add(new Criteria('visible',1));
    $objs =& $this->getObjects($crit); 
    //
    if (count($objs) > 0) 
      return $objs[0];
    else
      return false;
  }
  /////////////////////////////////////////////////
  function incrementPlayed($oVideo) {
    $oVideo->setVar('views',$oVideo->getVar('views')+1);
    return $this->insert($oVideo,true);
  }
  ////////////////////////////////////////////////
  function updateComments($oVideo,$count) {
    $oVideo->setVar('comments',$count);
    return $this->insert($oVideo);
  }
  ////////////////////////////////////////////////
  function &getVideosByCategory($cat) {
    is_object($cat) ? $catID = $cat->ID() : $catID = $cat;
    $crit =& new CriteriaCompo(new Criteria('category_id',$catID));
    $crit->add(new Criteria('visible',1));
    $crit->setSort('name');  
    $ary =& $this->getArray($crit);
    //
    return $ary;
  }
  ////////////////////////////////////////////////
  function getHighestRated() {
    $thisTable = $this->prefixedTable();
    $sql = "select v.id, v.name, v.rating/v.votes as AvgRating 
            from $thisTable where n.visible = 1
            order by AvgRating DESC";
    $objs =& $this->sqlToObjects($sql,10);
    //
    return $objs;
  }
  ////////////////////////////////////////////////
  function &getTopList($sort,$limit=10) {
    $crit =& new Criteria('visible', 1);
    $crit->setSort($sort);
    $crit->setOrder('DESC');
    $crit->setLimit($limit);
    $obj =& $this->getObjects($crit);
    //
    return $obj;
  }
  ////////////////////////////////////////////////
  function getMostViewed() {
    $obj =& $this->getTopList('views');
    return $obj;
  }
  ////////////////////////////////////////////////
  function getMostCommented() {
    $obj =& $this->getTopList('comments');
    return $obj;
  }
  ////////////////////////////////////////////////
  function getMostRated() {
    $obj =& $this->getTopList('votes');
    return $obj;
  }
  ////////////////////////////////////////////////
  function getBestRated() {
    $obj =& $this->getTopList('rating');
    return $obj;
  }
}

?>

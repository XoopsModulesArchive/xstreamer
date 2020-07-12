<?php
  $configArray = array( 'defaultAdminClass' => 'category',
                        'defaultFrontClass' => 'video',
                        'adminPath'    => XSTREAMER_ADMIN_PATH,
                        'styleSheet'   => XSTREAMER_BASE_URL.'/style.css',
                        'video'     => array ( 'manageTemplate' => 'xstreamer_admin_video_index.html',
                                               'frontTemplate'  => 'xstreamer_video_index.html',
                                               'navigatePage'   => 'manVideo',
                                               'saveSuccessMsg' => _MI_XSTREAMER_SAVE_VIDEO_SUCCESS,
                                               'saveFailMsg'    => _MI_XSTREAMER_SAVE_VIDEO_FAIL,
                                               'saveRedirect'   => 'index.php?class=video',
                                               'confirmDelete'  => _MI_XSTREAMER_DELETE_VIDEO_CONFIRM
                                            ),
                        'category'  => array ( 'manageTemplate' => 'xstreamer_admin_category_index.html',
                                               'navigatePage'   => 'manCat',
                                               'saveSuccessMsg' => _MI_XSTREAMER_SAVE_CAT_SUCCESS,
                                               'saveFailMsg'    => _MI_XSTREAMER_SAVE_CAT_FAIL,
                                               'saveRedirect'   => 'index.php?class=category',
                                               'confirmDelete'  => _MI_XSTREAMER_DELETE_CAT_CONFIRM
                                            ));
                      
//overriding classes
class processVideo extends requestItem { 
  //override template assignment virtual method
  function assignTemplates() { 
    global $xoopsTpl;
    //
    $hCat =& getXTModuleHandler('category');
    $hCommon =& getXTModuleHandler('common');
    //
    $aCategories =& $hCat->getCategoriesSelect();
    //
    $xoopsTpl->assign('xstreamer_category_select',$aCategories);
    //admin only
    if ($this->inAdmin()) {
      parent::assignTemplates();
      $xoopsTpl->assign('xstreamer_video_visible_select',$this->_handler->getVisibleSelect());
    } else {
      //front end
      if (isset($_GET['cat_id']))
        $_SESSION['cat_id'] = $_GET['cat_id'];
      else if ($this->_object->ID() > 0)
        $_SESSION['cat_id'] = $this->_object->categoryID();
      else if ($catID = key($aCategories))
        $_SESSION['cat_id'] = key($aCategories); 
      //
      if ($this->_object->ID() > 0) { 
        $oVideo =& $this->_object;
      } else if ($_SESSION['cat_id'] > 0) {
        if (!($oVideo =& $this->_handler->getFirstCategoryVideo($_SESSION['cat_id']))) {
          $xoopsTpl->append('xstreamer_error','No Videos have been defined under category '.current($aCategories));
          return;
        } 
      } else {
        $xoopsTpl->append('xstreamer_error','No Categories have been defined.');
        return;
      }
      $this->_object =& $oVideo; 
      parent::assignTemplates(); //override xstreamer_videos
      $xoopsTpl->assign('xstreamer_videos',$this->_handler->getVideosByCategory($_SESSION['cat_id']));
      $xoopsTpl->assign('xstreamer_enable_rating',$hCommon->getModuleOption('defEnableRating') == 1);
      $xoopsTpl->assign('xstreamer_enable_comments',$hCommon->getModuleOption('defEnableComment') == 1);
      //add css for rating
      $this->_appendModuleHeader('<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/xstreamer/styles/rating.css" />');
//      echo '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/xstreamer/styles/rating.css" />';
      //render comments
      global $xoopsModule, $xoopsModuleConfig, $xoopsConfig, $xoopsUser;
      if (!isset($_GET['id']))
        $_GET['id'] = $oVideo->ID();
      include_once XOOPS_ROOT_PATH.'/include/comment_view.php';
    }
  }
  //////////////////////////////////////////////////////////
  function extraFrontProcessing() {
    global $xoTheme, $xoopsTpl, $xoops_module_header; 
    //
    $hAjax =& getXTModuleHandler('ajax');
    //
    $oAjax =& $hAjax->create(); 
    $oAjax->registerFunction('onVote',XOOPS_URL.'/modules/xstreamer/rpc.php');
    $oAjax->registerFunction('onVoteReload',XOOPS_URL.'/modules/xstreamer/rpc.php');
    //
    $xoops_module_header = $oAjax->getHeaderCode(); 
    $this->_appendModuleHeader($xoops_module_header);
    //$xoopsTpl->assign('xoops_module_header',$xoops_module_header);
  }
  //////////////// op Commands //////////////////////////////
  function onGetVideo() { 
    //responds to an op=getVideo GET
    $oVideo =& $this->_object; //for clarity
    $oVideo->streamVideo($_GET['position']);
    //
    return false;
  }
}  

                  

?>
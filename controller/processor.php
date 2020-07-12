<?php

class requestItem {
  //private
  var $_command;
  var $_handler;
  var $_object;
  var $_config;
  var $_class;
  var $_module;
  var $_adminPath;
  var $_styleSheet;
  var $_template;
  var $_admin;
  //cons
  function requestItemInit($module, $class) {  
    $this->_module  = $module;
    $this->_class   = $class;
    //
    $this->_processConfig();
    $this->_processRequest();
  }
  ///////////// private methods ////////////////////
  function _processConfig() { 
    //controller.php defines the configArray.. read it and process
    global $configArray, $xoTheme, $xoopsTpl;  
    //
    if (!isset($this->_class ))
      die('class variable not set');
    //
    $this->_config  = $configArray;
    //
    if (!isset($configArray[$this->_class]))
      die('class '.$this->_class.' not defined in $configArray in controller.php file.');
    // 
    $this->_handler    = getXTModuleHandler($this->_class); 
    $this->_styleSheet = $configArray['styleSheet'];  
    //
    if (isset($xoTheme) && is_object($xoTheme)) { 
      $xoTheme->addStyleSheet($this->_getStyleSheet(false));
    } else if (isset($xoopsTpl)) {
      $this->_appendModuleHeader($this->_getStyleSheet());
    }
  }
  /////////////////////////////////////////////////
  function _appendModuleHeader($header) {
    global $xoopsTpl, $xoopsOption;
    //
    $tmp = isset($xoopsTpl->_tpl_vars['xoops_module_header']) ? $xoopsTpl->_tpl_vars['xoops_module_header'] : '';
    $tmp .= $header; 
    //
    $xoopsTpl->assign('xoops_module_header', $tmp);
    $xoopsOption["xoops_module_header"] = $tmp; //for Xoops 2.2.x
      //print_r($xoopsTpl);
  }
  /////////////////////////////////////////////////
  function _processRequest() { 
    if (isset($_REQUEST['id']))
      $id = $_REQUEST['id'];
    //
    if (isset($id) && ($id > 0))
      $this->_object =& $this->_handler->get($id);
    else
      $this->_object =& $this->_handler->create();
  }
  ////////////////////////////////////////////////////
  function _getConfig($config) {
    return $this->_config[$this->_class][$config];
  }
  ////////////////////////////////////////////////////
  function _getID() {
    if (isset($this->_object))
      return $this->_object->ID();
    else
      return null;
  }
  //////////////////////////////////////////////////////////
  function _getStyleSheet($full = true) {
    if (is_array($this->_styleSheet)) {
      $tmp = '';
      foreach($this->_styleSheet as $key=>$style) {
        $tmp .= $full ? '<link rel="stylesheet" type="text/css" media="all" href="'.$style.'" />' : $style;
      }
      return $tmp;
    } else {
      return $full ? '<link rel="stylesheet" type="text/css" media="all" href="'.$this->_styleSheet.'" />' : $this->_styleSheet; 
//      return '<link rel="stylesheet" type="text/css" media="all" href="'.$this->_styleSheet.'" />';
    }
  }
  //////////////////////////////////////////////////////////
  function _setupPage() {  
    if ($this->_template == '')
      $this->_template = $this->getTemplate();
    //  
    $this->assignThemeHeader();
    $this->assignTemplates();  
    //extra processing in either admin or front end
    if ($this->inAdmin())
      $this->extraAdminProcessing();
    else
      $this->extraFrontProcessing();  
  }
  //////////////////////////////////////////////////////////
  function _displayErrors() {
    global $xoopsTpl;
    //
    $xoopsTpl->assign($this->_module.'_'.$this->_class.'_error',$this->_object->getErrors());
    $this->_setupPage();
  }
  //////////////////////////////////////////////////////////
  function _processGet() { 
    if (!($this->_processOpCommand() === false))
      $this->_setupPage();
  }
  //////////////////////////////////////////////////////////
  function _processPost() {
    if (!($this->_processOpCommand() === false)) {
      //post is either saving or deleting.
      if (isset($_POST['submit'])) {//saving $this->_handler->insert($this->_object)
        $this->doSave();
        if (!$this->_object->hasErrors()) {    
          if ($this->_handler->insert($this->_object)) {
            redirect_header($this->_getConfig('saveRedirect'),3,$this->_getConfig('saveSuccessMsg'));
          } else
            die($this->_getConfig('saveFailMsg')); 
        } else { //have errors.. must display main index form plus populate error smarty variables
          if ($this->inAdmin())
            $this->displayAdminPage($this->_displayErrors());
          else
            $this->displayFrontPage($this->_displayErrors());
        }
      } else if (isset($_POST['confirm']))  { //confirm delete
        $this->doDeleteConfirm();
      } else if (isset($_POST['confirm_submit']) && ($_POST['confirm_submit'] == 'delete'))  {
        $this->doDelete();
      } else 
        die('Unrecognised _processPost command');
    }
  }
  ///////////////////////////////////////////
  function _processOpCommand() {
    if (isset($_REQUEST['op'])) {
      //got an op command... delegate to an onOpCommand method.. ie if op=showList then deletegate to onShowList
      $command = ucFirst($_REQUEST['op']);
      $method  = 'on'.$command;  
      if (method_exists($this,$method)) { 
        return $this->$method();
      }
    } else
      return true;
  }
  /////////////// protected methods //////////////////
  function &getHandler() {
    return $this->_handler;
  }
  ////////////////////////////////////////////
  function &getObject() {
    return $this->_object;
  }
  ///////////////////////////////////////////
  function getTemplate() {
    if ($this->inAdmin())
      return $this->_getConfig('manageTemplate');
    else
      return $this->_getConfig('frontTemplate');
  }
  //////////////// virtual public methods //////////////////
  function extraFrontProcessing() {
    //abstract
  }
  //////////////////////////////////////////////////////////
  function extraAdminProcessing() {
    //abstract
  }
  //////////////////////////////////////////////////////////
  function assignThemeHeader() {
    global $xoopsTpl, $oAdminButton; 
    //
    if ($this->inAdmin())
      $xoopsTpl->assign($this->_module.'_admin_navigation',$oAdminButton->renderButtons($this->_getConfig('navigatePage')));
  }
  //////////////////////////////////////////
  function assignTemplates() { 
    global $xoopsTpl; 
    //
    $xoopsTpl->assign($this->_module.'_'.$this->_class,$this->_object->getArray()); 
    $xoopsTpl->assign($this->_module.'_'.$this->_class.'s',$this->_handler->getArray()); 
  }
  ///////////////////////////////////////////
  function doSave() {  
     $this->_object->setVarsFromArray($_POST);
  }
  ///////////////////////////////////////////
  function doDeleteConfirm() {
    $ary = array('id' => $this->_getID(), 'class' => $this->_class);
    xoops_cp_header();
    xoops_confirm($ary,'index.php',$this->_getConfig('confirmDelete'),'delete');
    xoops_cp_footer();
  }
  ///////////////////////////////////////////
  function doDelete() { 
    if ($this->_handler->delete($this->_object))
      redirect_header($this->_getConfig('saveRedirect'),3,$this->_getConfig('saveSuccessMsg'));
    else
      die($this->_getConfig('saveFailMsg'));    
  }
  ///////////////// public methods /////////////////////////
  function setAdminPath($path) {
    $this->_adminPath = $path;
  }
  /////////////////////////////////////////////////////////
  function setInAdmin($switch) {
    $this->_admin = $switch;
  }
  /////////////////////////////////////////////////////////
  function inAdmin() {
    return $this->_admin;
  }
  /////////////////////////////////////////////////////////
  function process() { 
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      if ($this->inAdmin())
        $this->displayAdminPage($this->_processGet());
      else
        $this->displayFrontPage($this->_processGet());
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST')
      $this->_processPost();
  }
  ////////////////////////////////////////////////////////
  function setTemplate($template = null) {
    global $xoopsOption;
    //
    if (isset($template)) {
      $this->_template = $template;
      $xoopsOption['template_main'] = $template;
    }
    else
      $xoopsOption['template_main'] = $this->_template;
  }
  ////////////////////////////////////////////////////////
  function displayAdminPage($function) { 
    global $xoopsTpl, $xoopsOption; 
    //
    xoops_cp_header();
    //
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
      echo $this->_getStyleSheet();
    //
    $function;
    //
    $this->setTemplate(); 
    $this->renderAdminPage();
    //$xoopsTpl->display('db:'.$this->_template);
    //$xoopsOption['template_main'] = $this->_template;
    //if (isset($xoopsOption['template_main'])) { 
      //require($this->_adminPath.'/admin_footer.php');
    //}
    xoops_cp_footer();
  }  
  ////////////////////////////////////////////////////////
  function renderAdminPage() {
    global $xoopsTpl;
    //Xoops 2.2.x links xoops_cp_footer to the main footer.php class. A side effect of 
    //this is that $xoopsTpl->display('db:'.$this->_template) will render our templates twice.
    //get Xoops version
    $hModule =& xoops_gethandler('module');
    $oSystem = $hModule->getByDirName('system'); 
    //
    if (!($oSystem->getVar('version') > 200)) //Xoops 2.2.x
      $xoopsTpl->display('db:'.$this->_template);
  }
  ////////////////////////////////////////////////////////
  function displayFrontPage($function) {
    global  $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin; 
    //
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
      echo $this->_getStyleSheet();
    //
    $function;
    //
    $this->setTemplate();
    //if (isset($xoopsOption['template_main'])) { 
      include(XOOPS_ROOT_PATH . "/footer.php");
    //}
  }  
  
}
//////////////////////////////////////////////////////////////////////////////////////////
require_once('controller.php');

class requestProcesser {
  var $_module;
  var $_class;
  var $_controller;
  var $_admin=false;
  /////////////////////////////////////
  function requestProcesser($module) {
    if (!isset($module))
      die('module name not specified during requestProcess creation');
    //
    $this->_module    =  $module;
  }
  ////////////////////////////////////
  function _setup() {
    global $configArray;
    // 
    if (isset($_REQUEST['class']) && ($_REQUEST['class'] <> '')) 
      $this->_class = strtolower($_REQUEST['class']);
    else if ($this->_admin) {
      if (isset($configArray['defaultAdminClass']))
        $this->_class = strtolower($configArray['defaultAdminClass']);
      else
        die('defaultAdminClass not set in controller');
    } else {
      if (isset($configArray['defaultFrontClass'])) {
        $this->_class = strtolower($configArray['defaultFrontClass']);
        //we've set a default front class... finally check for a defaultFrontOp
        if (isset($configArray['defaultFrontOp']) && (!(isset($_REQUEST['op']))))
          $_REQUEST['op'] = $configArray['defaultFrontOp'];
      }
      else
        Die('defaultFrontClass not set in controller');
    } 
    //  
    $overrideClass = 'process'.ucfirst($this->_class);
    //
    if (class_exists($overrideClass))
      $this->_controller =& new $overrideClass();
    else
      $this->_controller =& new requestItem();
    //
    $this->_controller->setAdminPath($configArray['adminPath']);
    $this->_controller->setInAdmin($this->_admin);
  }
  ////////////////////////////////////
  function setInAdmin($switch = true) {
    $this->_admin = $switch;
  }
  ////////////////////////////////////
  function processRequest() {
    //
    $this->_setup();
    $this->_controller->requestItemInit($this->_module, $this->_class);
    $this->_controller->process();   
  }
   
}
  


?>

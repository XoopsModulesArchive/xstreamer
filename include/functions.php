<?php

require_once("consts.php");


function &getXTModuleHandler($name, $module_dir = null, $db = null ) {
    static $handlers;
    // if $module_dir is not specified
    if (!isset($module_dir)) {
      $module_dir = trim(XSTREAMER_MODULE_DIR);
    } 
    $name = (!isset($name)) ? $module_dir : trim($name); 
    //    
    if (!isset($handlers[$module_dir][$name])) { 
        if ( file_exists( $hnd_file = XOOPS_ROOT_PATH . "/modules/{$module_dir}/class/{$name}.php" ) ) {
            require_once $hnd_file;
        }
        $class = XSTREAMER_MODULE_ID.ucfirst($name).'Handler';
        if (class_exists($class)) {
        	  if (!isset($db))
        	    $db = $GLOBALS['xoopsDB'];
        	  //
            $handlers[$module_dir][$name] =& new $class( $db );
        }
    }
    if (!isset($handlers[$module_dir][$name])) {
        trigger_error('Handler does not exist<br />Module: '.$module_dir.'<br />Name: '.$name, E_USER_ERROR);
    }
    if ( isset( $handlers[$module_dir][$name] ) ) {
      return $handlers[$module_dir][$name];
    }
    $obj = false;
    return $obj;
}
//////////////////////////////////////////////////////////////
function &getXTXoopsHandler($name, $db = null) {
  static $handlers;
  //
  $name = strtolower(trim($name));
  //
  if (!isset($db))
    $db = $GLOBALS['xoopsDB'];
  //if using a different database then unset
  if (isset($handlers[$name]) && (!($handlers[$name]->db == $db)) )
    unset($handlers[$name]);
  //
  if (!isset($handlers[$name])) {
      if ( file_exists( $hnd_file = XOOPS_ROOT_PATH.'/kernel/'.$name.'.php' ) ) {
          require_once $hnd_file;
      }
      $class = 'Xoops'.ucfirst($name).'Handler';
      if (class_exists($class)) {
          $handlers[$name] =& new $class($db);
      }
  }
  if (!isset($handlers[$name]) ) {
      trigger_error('Class <b>'.$class.'</b> does not exist<br />Handler Name: '.$name, E_USER_ERROR);
  }
  if ( isset( $handlers[$name] ) ) {
    return $handlers[$name];
  }
  //
  $obj = false;
  return $obj;
}
//////////////////////////////////////////////////////////////////////
function GetXTBacktrace($var = false) { 
   echo "<div><br /><table border='1'>";
   $sOut=""; $aCallstack=debug_backtrace();
  
   echo "<thead><tr><th>file</th><th>line</th><th>function</th>";
   if ($var)
     echo '<th>args</th>';
   //
   echo "</tr></thead>";
   foreach($aCallstack as $aCall) {
       if (!isset($aCall['file'])) $aCall['file'] = '[PHP Kernel]';
       if (!isset($aCall['line'])) $aCall['line'] = '';

       echo "<tr><td>{$aCall["file"]}</td><td>{$aCall["line"]}</td>".
            "<td>{$aCall["function"]}</td>";
       if ($var) 
         echo "<td><pre>".print_r($aCall['args'],true)."</pre></td>";
       
       echo "</tr>";
   }
   echo "</table></div></p>";
}


?>

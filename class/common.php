<?php

require_once('PSPBaseObject.php');

class xStreamerCommon extends XoopsObject {

  function xStreamerCommon($id = null) {
  }
}


class xStreamerCommonHandler extends XTBaseObjectHandler {
  //cons
  function xStreamerCommonHandler (&$db)
  { $this->classname = 'xstreamercommon';
    $this->db =& $db;
  }
  ///////////////////////////////////////////////////
  function &getInstance(&$db)
  {
      static $instance;
      if(!isset($instance)) {
          $instance = new xStreamerCommonHandler ($db);
      }
      return $instance;
  }
  //////////////////////////////////////////////////////////
  function getModuleOption($option, $repmodule=XSTREAMER_MODULE_DIR)
  {
      global $xoopsModuleConfig, $xoopsModule;
      static $tbloptions= Array();
      if(is_array($tbloptions) && array_key_exists($option,$tbloptions)) {
          return $tbloptions[$option];
      }

      $retval=false;
      if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive')))
      {
          if(isset($xoopsModuleConfig[$option])) {
              $retval= $xoopsModuleConfig[$option];
          }

      } else {
          $module_handler =& xoops_gethandler('module');
          $module =& $module_handler->getByDirname($repmodule);
          $config_handler =& xoops_gethandler('config');
          if ($module) {
              $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
              if(isset($moduleConfig[$option])) {
                  $retval= $moduleConfig[$option];
              }
          }
      }
      $tbloptions[$option]=$retval;
      return $retval;
  }
  //////////////////////////////////////////////////////////
  function getDateField($name, $date = null) {
    if (!isset($date)) {
      $date = time();
    } else if ($date < 0) {
      $date = null;
    }
    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    //
    if (class_exists('XoopsFormCalendar')) {
      $cal =& new XoopsFormCalendar($name, $name, $date, array(), array('value'=>date("Y-m-d",$date)));
      return $cal->render();
    } else {
      include_once XOOPS_ROOT_PATH.'/include/calendarjs.php';
      if ($date > -1)
       $date = date("Y-m-d", $date);
      else
       $date = '';
      //
      return "<input type='text' name='$name' id='$name' size='11' maxlength='11' value='".$date."' /><input type='reset' value=' ... ' onclick='return showCalendar(\"".$name."\");'>";
    }
  }
  //////////////////////////////////////////////////////////
  function getTimeField($name, $date = null) {
    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    //
    if (!isset($date)) {
      $date = time();
    }
		$datetime = getDate($date);
		$div      = 30;      //echo  $datetime['hours'] * 3600 + 60 * 30 * ceil($datetime['minutes'] / 30);
    //
		$timearray = array();
		for ($i = 0; $i < 24; $i++) {
			for ($j = 0; $j < 60; $j = $j + $div) {
				$key = ($i * 3600) + ($j * 60);
				$timearray[$key] = ($j != 0) ? $i.':'.$j : $i.':0'.$j;
			}
		}
		ksort($timearray);
		$timeselect = new XoopsFormSelect('', $name.'[time]', $datetime['hours'] * 3600 + 60 * $div * ceil($datetime['minutes'] / $div));
		$timeselect->addOptionArray($timearray);
		//
		return $timeselect->render();
  }
  ///////////////////////////////////////////
  function xoopsUserByEmail($email) {
    $hUser =& xoops_gethandler('user');
    //
    $crit  =& new Criteria('email',$email);
    $user  =& $hUser->getObjects($crit);
    //
    if (count($user) > 0) {
      $user = reset($user);
      return $user;
    } else {
      return false;
    }
  }
  ///////////////////////////////////////////
  function xoopsUserbyUName($uname) {
    $hUser =& xoops_gethandler('user');
    //
    $crit  =& new Criteria('uname',$uname);
    $user  =& $hUser->getObjects($crit);
    //
    if (count($user) > 0) {
      $user = reset($user);
      return $user;
    } else {
      return false;
    }
  }
  ///////////////////////////////////////////
  function deleteUser($user) {
    $usersTable = $this->db->prefix('users');
    $uid        = $user->uid();
    //
    $sql = "delete from $usersTable where uid = $uid";
    //
    $this->db->queryF($sql);
  }
  ///////////////////////////////////////////
  function &getRowArray($hObject, $array) {
    $obj =& new $hObject->classname($array);
    return $obj->getArray();
  }
  //////////////////////////////////////////////////////////////////////////
  function validEmail($email,&$error) {
    $valid =& new ValidateEmail($email);
    $error = $valid->getError();
    return $valid->isValid();
  }
  //////////////////////////////////////////////////////////////////////////
  function getToken() {
    return $GLOBALS['xoopsSecurity']->createToken(1200);
  }
  //////////////////////////////////////////////////////////////////////////
  function &validateInteger($value, $field, $required = true, $minval = -1, $maxval = -1) {
    $valid =& new ValidateInteger($value, $field, $required, $minval, $maxval);
    if ($valid->isValid()) {
      $res = false;
      return $res;
    } else {
      return $valid->getErrors();
    }
  }
  //////////////////////////////////////////////////////////////////////////
  function &validateFloat($value, $field, $required = true, $minval = -1, $maxval = -1) {
    $valid =& new ValidateFloat($value, $field, $required, $minval, $maxval);
    if ($valid->isValid()) {
      $res = false;
      return $res;
    } else {
      return $valid->getErrors();
    }
  }
  //////////////////////////////////////////////////////////////////////////
  function manageOnline($module) {
    global $xoopsUser;
    //
    $hModule =& xoops_gethandler('module');
    $hOnline =& xoops_gethandler('online');
    //
    $aModule =& $hModule->getByDirname($module);
    //
    if (isset($aModule)) {
      if ($xoopsUser) {
        $uid   = $xoopsUser->uid();
        $uname = $xoopsUser->uname();
      } else {
        $uid    = 0;
        $uname = '';
      }
      //
      $hOnline->write($uid,$uname,time(),$aModule->getVar('mid'),getenv("REMOTE_ADDR"));
    }
  }
  //////////////////////////////////////////////////////////////////////////
  function &getGroupEmails($groupid) {
    $userTable  = $this->db->prefix('users');
    $groupTable = $this->db->prefix('groups_users_link');
    //
    $sql = "select distinct email from $userTable u inner join $groupTable g on
              u.uid = g.uid
            where g.groupid = $groupid";
    //
    $aEmails = array();
    if ($res = $this->db->query($sql)) {
      while($row = $this->db->fetchArray($res)) {
        $aEmails[] = $row['email'];
      }
    } else {
      echo $sql;
    }
    //
    return $aEmails;
  }
  //////////////////////////////////////////////////////////////////////////
  function &getRegisterdUsers($groupid = null) {
    $userTable  = $this->db->prefix('users');
    $groupTable = $this->db->prefix('groups_users_link');
    //
    if (!isset($groupid)) {
      $groupid = XOOPS_GROUP_USERS;
    }
    //
    $sql = "select distinct u.uid, u.uname from $userTable u inner join $groupTable g on
              u.uid = g.uid
            where g.groupid = $groupid
            order by u.uname";
    //
    $aUsers = array(0 => '');
    if ($res = $this->db->query($sql)) {
      while($row = $this->db->fetchArray($res)) {
        $aUsers[$row['uid']] = $row['uname'];
      }
    } else {
      echo $sql;
    }
    //
    return $aUsers;
  }
  //////////////////////////////////////////////////////////////////////////
  function getSmartyVar($name) {
    global $xoopsTpl;
    //
    if ( isset($xoopsTpl->_tpl_vars[$name]) ) {
      return $xoopsTpl->_tpl_vars[$name];
    } else {
      return false;
    }
  }
  //////////////////////////////////////////////////////////////////////////
  function tableExists($table) {
    $bRetVal = false;
    //Verifies that a MySQL table exists
    $realname = $this->db->prefix($table);
    $ret = mysql_list_tables(XOOPS_DB_NAME, $this->db->conn);
    while (list($m_table)=$this->db->fetchRow($ret)) {
        if ($m_table ==  $realname) {
            $bRetVal = true;
            break;
        }
    }
    $this->db->freeRecordSet($ret);
    return ($bRetVal);
  }
  //////////////////////////////////////////////////////////////////////////
  function fieldExists($table, $field) {
    $realname = $this->db->prefix($table);
    $sql = "select * from $realname";
    $ret = false;
    //
    if ($res = $this->db->query($sql)) {
      $fields = mysql_num_fields($res);
      for ($i=0;$i<$fields;$i++) {
        if (mysql_field_name($res,$i) == $field) {
          return true;
        }
      }
    }
    return $ret;
  }
  //////////////////////////////////////////////////////////////////////////
  function fieldCount($table) {
    $realname = $this->db->prefix($table);
    $sql = "select * from $realname";
    //
    if ($res = $this->db->query($sql)) {
      $fields = mysql_num_fields($res);
      return $fields;
    } else {
      return 0;
    }
  }
  //////////////////////////////////////////////////////////////////////////
  function arrayToString($array) {
   $retval = '';
   $null_value = "^^^";
   $i = 1;
   foreach ($array as $index => $value) {
     if (!$value) {
       $value = $null_value;
     }
     if (is_array($value)) {
       $retval .= $this->arrayToString($value) . '|||';
     } else {
       $retval .= $index . '|' . $value;
       if ($i < count($array))
         $retval .= '||';
     }
     $i++;
   }
   return $retval;
  }
  /////////////////////////////////////////////////////////////////////////
  function &addToCriteria($crit, $newCrit) { //print_r($crit);
    $funcCrit =& new CriteriaCompo();
    //
    foreach($crit->criteriaElements as $key=>$obj) {
      if (!($obj->column == $newCrit->column)) {
        $tmp = $obj;  //so we don't assign be reference
        $funcCrit->add($tmp);//echo '<PRE>'.print_r($funcCrit,true).'</PRE>';
      }
    }
    $funcCrit->add($newCrit); //
    //copy over sorts and orders
    $funcCrit->setSort($crit->getSort());
    $funcCrit->setOrder($crit->getOrder());
    //
    //unset($crit);
    return $funcCrit; //echo '<PRE>'.print_r($funcCrit,true).'</PRE>';
  }
  ////////////////////////////////////////////////////////////////////////
  function &rebuildByID($inAry, $idField) {
    $outAry = array();
    for ($i = 0; $i < sizeof($inAry); $i++) {
    	$outAry[$inAry[$i][$idField]] = $inAry[$i];
    }
    return $outAry;
  }
  ////////////////////////////////////////////////////////////////////////
  function &rebuildGroupBy($inAry, $groupBy) {
    $outAry = array();
    for ($i = 0; $i < sizeof($inAry); $i++) {
    	$outAry[$inAry[$i][$groupBy]][] = $inAry[$i];
    }
    return $outAry;
  }
  ///////////////////////////////////////////////////
  function parseConstants($body) {
    global $xoopsConfig;
    //
    $hModule =& xoops_gethandler('module');
    $module =& $hModule->getByDirname(MULTISITE_MODULE_DIR);
    //
    $tags = array();
    $tags['X_MODULE'] = $module->getVar('name');
    $tags['X_SITEURL'] = XOOPS_URL;
    $tags['X_SITENAME'] = $xoopsConfig['sitename'];
    $tags['X_ADMINMAIL'] = $xoopsConfig['adminmail'];
    $tags['X_MODULE_URL'] = XOOPS_URL .'/modules/' . $module->getVar('dirname') .'/';
    //
    foreach($tags as $k=>$v){
        $body = preg_replace('/{'.$k.'}/', $v, $body);
    }
    return $body;
  }
  ///////////////////////////////////////////////////
  function &objectsToArray($objs, $keyField = 'id') {
    $i   = 0;
    $ary = array();
    foreach($objs as $key=>$obj) {
      $vars =& $obj->getVars();
      foreach($vars as $key=>$array) {
        $ary[$obj->getVar($keyField)][$key] = $array['value'];
      }
      $i++;
    }
    return $ary;
  }
  ////////////////////////////////////////////////////
  function &arrayByKey($array) {
    $ary = array();
    foreach($array as $key=>$value) {
      $ary[$value] = $value;
    }
    return $ary;
  }
  //////////////////////////////////////////////////////////////////////////
  function GetElapsedTime($time)
  {
      //Define the units of measure
      $units = array('years' => (365*60*60*24) /*Value of Unit expressed in seconds*/,
          'weeks' => (7*60*60*24),
          'days' => (60*60*24),
          'hours' => (60*60),
          'minutes' => 60,
          'seconds' => 1);

      $local_time   = $time;
      $elapsed_time = array();

      //Calculate the total for each unit measure
      foreach($units as $key=>$single_unit) {
        $elapsed_time[$key] = floor($local_time / $single_unit);
        $local_time -= ($elapsed_time[$key] * $single_unit);
      }
      return $elapsed_time;
  }
  //////////////////////////////////////////////////////////////////////////
  function FormatTime($time)
  {
    $values = $this->GetElapsedTime($time);
    foreach($values as $key=>$value) {
      $$key = $value;
    }
    $ret = array();
    if ($years) {
      $ret[] = $years . 'y';
    }
    if ($weeks) {
      $ret[] = $weeks . 'w';
    }
    if ($days) {
      $ret[] = $days . 'd';
    }
    if ($hours) {
      $ret[] = $hours . 'h';
    }
    if ($minutes) {
      $ret[] = $minutes . 'm';
    }
//    if ($seconds) {
//      $ret[] = $seconds . 's';
//    }

    return implode(':', $ret);
  }
  //////////////////////////////////////////////////////////////////////////
  function FormatLongTime($time)
  {
    $values = $this->GetElapsedTime($time);
    foreach($values as $key=>$value) {
      $$key = $value;
    }
    $ret = array();
    if ($years) {
      $ret[] = $years . ' ' . ($years == 1 ? 'Year' : 'Years');
    }
    if ($weeks) {
      $ret[] = $weeks . ' ' . ($weeks == 1 ? 'Week' : 'Weekss');
    }
    if ($days) {
      $ret[] = $days . ' ' . ($days == 1 ? 'Day' : 'Days');
    }
    if ($hours) {
      $ret[] = $hours . ' ' . ($hours == 1 ? 'Hour' : 'Hours');
    }
    if ($minutes) {
      $ret[] = $minutes . ' ' . ($minutes == 1 ? 'Minute' : 'Minutes');
    }
//    if ($seconds) {
//      $ret[] = $seconds . ' ' . ($seconds == 1 ? 'Second' : 'Seconds');
//    }

    return implode(', ', $ret);
  }
  //////////////////////////////////////////////////////////////////////
  function valueInArray($value, $aArray) {
    foreach($aArray as $key=>$thisValue) {
      if ($value == $thisValue)
        return true;
    }
    return false;
  }
  //////////////////////////////////////////////////////////////////////
  function &invertArray($input) {
    $ary = array();
    foreach($input as $key=>$value) {
      $ary[$value] = $key;
    }
    return $ary;
  }
  /////////////////////////////////////////////////////////////////////
  function &arrayIndex($aInput, $keyField) {
    $out = array();
    foreach ($aInput as $key=>$value) {
      $out[] = $value[$keyField];
    }
    return $out;
  }
  /////////////////////////////////////////////////////////////////////
  function addToArrayByID(&$aInput, $key, $value, $aKey, $aValues) {
    foreach($aInput as $k=>$v){
      if ($v[$key] == $value)
        $aInput[$k][$aKey] = $aValues;
    }
  }
  /////////////////////////////////////////////////////////////////////
  function &objectsToSelect($objects, $valueField, $none = false) {
    $ary = array();
    //
    if ($none)
      $ary[0] = 'None';
    //
    foreach($objects as $key=>$object) {
      $ary[$object->ID()] = $object->getVar($valueField);
    }
    return $ary;
  }
  /////////////////////////////////////////////////////////////////////
  function copyDirectory($source, $dest, $overwrite = false){
    if ($handle = opendir($source)){        // if the folder exploration is sucsessful, continue
     while(false !== ($file = readdir($handle))){ // as long as storing the next file to $file is successful, continue
       if($file != '.' && $file != '..'){
         $path = $source . '/' . $file;
         if(is_file($path)){
           if(!is_file( $dest . '/' . $file) || $overwrite)
             if ( (strpos($dest,'english') === false) || ( (strpos($dest,'english') !== false) && (!file_exists($dest . '/' . $file))  ) )
               if(!@copy($path, $dest . '/' . $file)){ 
                 echo '<font color="red">File ('.$path.') could not be copied to ('.$dest.'/'.$file.'), likely a permissions problem.</font>';
               } else {
                 echo "copied file <i>$path</i> <strong>to</strong> <i>$dest/$file</i><br/>"; 
               }
         } else if(is_dir($path)){
           if(!is_dir($dest . '/' . $file))
             mkdir($dest . '/' . $file); // make subdirectory before subdirectory is copied
           $this->copyDirectory($path, $dest . '/' . $file, $overwrite); //recurse!
         }
       }
     }
     closedir($handle);
   }
}  


}

?>

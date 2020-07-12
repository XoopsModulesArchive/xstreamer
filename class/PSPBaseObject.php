<?php

define('XOBJ_PSP_DTYPE_FLOAT',20);

Class XTBaseObject extends XoopsObject {
  //cons
  function XTBaseObject(){
    parent::XoopsObject();
  }
  //////////////////////////////////////
  function ID() {
    return $this->getVar('id');
  }
  ///////////////////////////////////////////////////////
  function initVar($key, $data_type, $value = null, $required = false, $maxlength = null, $options = '', $pretty = '') {
    parent::initVar($key, $data_type, $value, $required, $maxlength, $options);
    $this->vars[$key]['pretty'] = $pretty;
  }
  ///////////////////////////////////////////////////////
  function &getArray(){
    $ary = array();
    $vars =& $this->getVars();
    foreach($vars as $key => $value) {
      $ary[$key] = $value['value'];
    }
    return $ary;
  }
  ///////////////////////////////////////////////////////
//  function setVar($key, $value, $not_gpc = false)  {
//    if (!empty($key) && isset($value) && isset($this->vars[$key])) {
//      if ($value <> $key['value']) { 
//        $this->vars[$key]['changed'] = true;
//        $this->setDirty();
//      }
//      $this->vars[$key]['value'] =& $value;
//      $this->vars[$key]['not_gpc'] = $not_gpc;
//    }
//  }
  ///////////////////////////////////////////////////////
  function setVarsFromArray($post) {
    $vars =& $this->getVars();
    //
    foreach ($post as $key => $value) {
      if (isset($vars[$key])) {
        $this->setVar($key, $value);
      }
    }
    //next validate
    $this->validate();
  }
  //////////////////////////////////////////////////////
  function validate() {
    //abstract class filled by child
  }
  ///////////////////////////////////////////////////////
  function hasErrors() {
    return count($this->_errors) > 0;
  }
  ///////////////////////////////////////////////////////
  function &getErrors() {
    return $this->_errors;
  }
  ///////////////////////////////////////////////////////
  function setErrors($key, $value) {
    $this->_errors[$key] = trim($value); //echo 'key='.$key.', value='.$value.', ';
  }
  ///////////////////////////////////////////////////////
  function cleanVars()
  {
    $ts =& MyTextSanitizer::getInstance();
    foreach ($this->vars as $k => $v) {
      $cleanv = $v['value'];
          if (!$v['changed']) {
          } else {
              $cleanv = is_string($cleanv) ? trim($cleanv) : $cleanv;
              switch ($v['data_type']) {
              case XOBJ_DTYPE_TXTBOX:
                  if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                      $this->setErrors($k,"$v[pretty] is required.");
                      continue;
                  }
                  if (isset($v['maxlength']) && strlen($cleanv) > intval($v['maxlength'])) {
                      $this->setErrors($k,"$v[pretty] must be shorter than ".intval($v['maxlength'])." characters.");
                      continue;
                  }
                  if (!$v['not_gpc']) {
                      $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                  } else {
                      $cleanv = $ts->censorString($cleanv);
                  }
                  break;
              case XOBJ_DTYPE_TXTAREA:
                  if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                      $this->setErrors($k,"$v[pretty] is required.");
                      continue;
                  }
                  if (!$v['not_gpc']) {
                      $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                  } else {
                      $cleanv = $ts->censorString($cleanv);
                  }
                  break;
              case XOBJ_DTYPE_SOURCE:
                  if (!$v['not_gpc']) {
                      $cleanv = $ts->stripSlashesGPC($cleanv);
                  } else {
                      $cleanv = $cleanv;
                  }
                  break;
              case XOBJ_DTYPE_INT:
                  $cleanv = intval($cleanv);
                  break;
              case XOBJ_PSP_DTYPE_ARRAY:
                  $cleanv = floatval($cleanv);
                  break;
              case XOBJ_DTYPE_EMAIL:
                  if ($v['required'] && $cleanv == '') {
                      $this->setErrors($k,"$v[pretty] is required.");
                      continue;
                  }
                  if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$cleanv)) {
                      $this->setErrors($k,"Invalid Email");
                      continue;
                  }
                  if (!$v['not_gpc']) {
                      $cleanv = $ts->stripSlashesGPC($cleanv);
                  }
                  break;
              case XOBJ_DTYPE_URL:
                  if ($v['required'] && $cleanv == '') {
                      $this->setErrors($k,"$v[pretty] is required.");
                      continue;
                  }
                  if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                      $cleanv = 'http://' . $cleanv;
                  }
                  if (!$v['not_gpc']) {
                      $cleanv =& $ts->stripSlashesGPC($cleanv);
                  }
                  break;
              case XOBJ_DTYPE_ARRAY:
                  $cleanv = serialize($cleanv);
                  break;
              case XOBJ_DTYPE_STIME:
              case XOBJ_DTYPE_MTIME:
              case XOBJ_DTYPE_LTIME:
                  if (strlen($cleanv) == 0)
                    $cleanv = 'null';
                  else {
                    $cleanv = !is_string($cleanv) ? intval($cleanv) : strtotime($cleanv);
                  }
                  break;
              default:
                  break;
              }
          }
          $this->cleanVars[$k] =& $cleanv;
          unset($cleanv);
      }
      //now validate
      $this->validate();
      //
      if (count($this->_errors) > 0) {
          return false;
      }
      $this->unsetDirty();
      return true;
  }
}

Class XTBaseObjectHandler extends XoopsObjectHandler {
  var $_dbtable;
  var $_primKey;
  //
  var $classname;
  ///////////////////////////////////////////////////////////////////
  ///////////////////////// abstract methods ////////////////////////
  function captureDebug($sql) {
    //abstract.. override to capture debug messages when failed to save data.
  }
  //////////////////////////////////////////////////////////////
  function persistClass(&$db, $className, $tableName, $primaryKeys = 'id') {
    $this->classname = $className;
    $this->_dbtable  = $tableName;
    $this->_primKey  = $primaryKeys;
    $this->db        = $db;
  }
  //////////////////////////////////////////////////////////////
  function &create()
  {
    $obj =& new $this->classname();
    return $obj;
  }
  //////////////////////////////////////////////////////////////
  function &get($id)
  {
      $obj = false;
      $id = intval($id);
      if($id > 0) {
          $sql = $this->_selectQuery(new Criteria('id', $id));
          if(!$result = $this->db->query($sql)) {
              return $obj;
          }
          $numrows = $this->db->getRowsNum($result);
          if($numrows == 1) {
              $obj = new $this->classname($this->db->fetchArray($result));
              return $obj;
          }
      }
      return $obj;
  }
  ///////////////////////////////////////////////////////
  function checkIfChangedAgainstDB($object, $field) {
    //check value in class against value in database
    $obj  =& $this->get($object->ID());
    return $obj->getVar($field) <> $object->getVar($field);
  }
  ///////////////////////////////////////////////////////////////////
  function postProcessSQL(&$sql, $criteria) {
    if(isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
      if ($criteria->renderWhere() != '') {
        $sql .= ' ' .$criteria->renderWhere();
      }
      if($criteria->groupby != ''){
        $sql .= $criteria->getGroupby();
      }
      if($criteria->getSort() != '') {
        $sql .= ' ORDER BY ' . $criteria->getSort() . '
                ' .$criteria->getOrder();
      }
      if($criteria->getLimit() != '') {
        if ($criteria->getStart() != '')
          $start = $criteria->getStart().',';
         else
          $start = '';
        //
        $sql .= ' LIMIT ' . $start . $criteria->getLimit();
      }

    }
  }
  ///////////////////////////////////////////////////////////////////
  function &getArray($criteria = null, $id_as_key = false) {
    $ret = array();
    $limit  = $start = 0;
    $sql = $this->_selectQuery($criteria);
    if (isset($criteria)) {
      $limit = $criteria->getLimit();
      $start = $criteria->getStart();
    }
    $result = $this->db->query($sql, $limit, $start);
    // If no records from db, return empty array
    if (!$result) {
        return $ret;
    }
    // Add each returned record to the result array
    while ($myrow = $this->db->fetchArray($result)) {
        if (!$id_as_key) {
            $ret[] = $myrow;
        } else {
            $ret[$myrow['id']] = $myrow;
        }
    }
    return $ret;
  }
  //////////////////////////////////////////////////////////////
  function &getObjects($criteria = null, $id_as_key = false)  {
    $ret    = array();
    $limit  = $start = 0;
    $sql    = $this->_selectQuery($criteria);
    if (isset($criteria)) {
        $limit = $criteria->getLimit();
        $start = $criteria->getStart();
    } 

    $result = $this->db->query($sql, $limit, $start);
    // If no records from db, return empty array
    if (!$result) {
        return $ret;
    }

    // Add each returned record to the result array
    while ($myrow = $this->db->fetchArray($result)) {
        $obj = new $this->classname($myrow);
        if (!$id_as_key) {
            $ret[] =& $obj;
        } else {
            $ret[$obj->getVar('id')] =& $obj;
        }
        unset($obj);
    }
    return $ret;
  }
  /////////////////////////////////////////////////////////
  function &sqlToArray($sql, $limit = null) {
    $ary = array();
    if ($res = $this->db->query($sql,$limit)) {
      while ($row = $this->db->fetchArray($res)) {
        $ary[] = $row;
      }
    }
    return $ary; 
  }
  ////////////////////////////////////////////////////////
  function &sqlToObjects($sql) {
    $ary = array();
    if ($res = $this->db->query($sql)) {
      while ($row = $this->db->fetchArray($res)) {
        $obj = new $this->classname($row);
        $ary[] =& $obj;
        unset($obj);
      }
    }
    return $ary;
  }
  //////////////////////////////////////////////////////////////
  function _selectQuery($criteria = null)   {
   $sql = sprintf('SELECT * FROM %s', $this->db->prefix($this->_dbtable));
   if(isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
       $sql .= ' ' .$criteria->renderWhere();
       if($criteria->getSort() != '') {
           $sql .= ' ORDER BY ' . $criteria->getSort() . '
               ' .$criteria->getOrder();
       }
   }
   return $sql;
  }
  //////////////////////////////////////////////////////////////
  function getCount($criteria = null)
  {
   $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix($this->_dbtable);
   if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
    $sql .= ' '.$criteria->renderWhere();
   }
   if (!$result =& $this->db->query($sql)) {
    return 0;
   }
   list($count) = $this->db->fetchRow($result);
   return $count;
  }
  //////////////////////////////////////////////////////////////
  function delete(&$obj, $force = false) {
    if (strcasecmp($this->classname, get_class($obj)) != 0) {
     return false;
    }

    $sql = sprintf("DELETE FROM %s WHERE id = %u", $this->db->prefix($this->_dbtable), $obj->getVar('id'));
    //echo $sql;
    if ($force) {
      $result = $this->db->queryF($sql);
    } else {
      $result = $this->db->query($sql);
    }
    if (!$result) {
     return false;
    }
    return true;
  }
  //////////////////////////////////////////////////////////////
  function deleteByID($pid, $force = false) {
    $sql = sprintf("DELETE FROM %s WHERE id = %u", $this->db->prefix($this->_dbtable), $pid);
    if ($force) {
      $result = $this->db->queryF($sql);
    } else {
      $result = $this->db->query($sql);
    }
    if (!$result) {
     return false;
    }
    return true;
  }
  ///////////////////////////////////////////////////////////////////
  function prefixedTable() {
    return $this->db->prefix($this->_dbtable);
  }
  //////////////////////////////////////////////////////////////
  function insert(&$obj, $force = false) { 
    $result = true;
    // Make sure object is of correct type
    if (strcasecmp($this->classname, get_class($obj)) != 0) {
      $obj->setErrors(get_class($obj)." Differs from ".$this->className);
    }

    // Make sure object needs to be stored in DB
    if (!$obj->isDirty()) {
      $obj->setErrors("Not dirty");
    } 

    // Make sure object fields are filled with valid values
    if (!$obj->cleanVars()) {
      return false;
    } 
    //have to use field definition information to construct either an insert or update statement.
    foreach ($obj->cleanVars as $k => $v) {
      if ($obj->vars[$k]['data_type'] == XOBJ_DTYPE_INT) {
          $cleanvars[$k] = intval($v);
      } elseif ( is_array( $v ) ) {
        $cleanvars[ $k ] = $this->db->quoteString( implode( ',', $v ) );
      } else {
        $cleanvars[$k] = $this->db->quoteString($v);
      }
    }
    //
    $thisTable = $this->prefixedTable();
    if ($obj->isNew()) {
      if (!is_array($this->_primKey)) {
        if ($cleanvars[$this->_primKey] < 1) {
          $cleanvars[$this->_primKey] = $this->db->genId($this->_dbtable.'_'.$this->_primKey.'_seq');
        }
      }
      
      $sql = "INSERT INTO $thisTable"." (".implode(',', array_keys($cleanvars)).") VALUES (".implode(',', array_values($cleanvars)) .")";
    } else {
      $sql = "UPDATE $thisTable SET";
      foreach ($cleanvars as $key => $value) {
        if ((!is_array($this->_primKey) && $key == $this->_primKey) || (is_array($this->_primKey) && in_array($key, $this->_primKey))) {
          continue;
        }
        if (isset($notfirst) ) {
          $sql .= ",";
        }
        $sql .= " ".$key." = ".$value;
        $notfirst = true;
      }
      if (is_array($this->_primKey)) {
        $whereclause = "";
        for ($i = 0; $i < count($this->_primKey); $i++) {
          if ($i > 0) {
            $whereclause .= " AND ";
          }
          $whereclause .= $this->_primKey[$i]." = ".$obj->getVar($this->_primKey[$i]);
        }
      }
      else {
        $whereclause = $this->_primKey." = ".$obj->getVar($this->_primKey);
      }
      $sql .= " WHERE ".$whereclause;
    } 
    //save    
    if (false != $force) {
      $result = $this->db->queryF($sql);
    } else {
      $result = $this->db->query($sql);
    }
    if (!$result) {
      $this->captureDebug($sql);
      return false;
    }
    if ($obj->isNew() && !is_array($this->_primKey)) {
      $obj->assignVar($this->_primKey, $this->db->getInsertId());
    }
    return true;    
  }

}


?>

<?php

require_once("../../mainfile.php");
require_once(XOOPS_ROOT_PATH . "/header.php");
require_once("include/consts.php");
require_once("include/functions.php");
require_once('controller/processor.php');
//create controller and process requests
$processor =& new requestProcesser('xstreamer');
$processor->processRequest();


?>
<?php   

include('../../../include/cp_header.php');
include_once('admin_header.php');
require_once('../controller/processor.php');

//create controller and process requests
$processor =& new requestProcesser('xstreamer');
$processor->setInAdmin();
$processor->processRequest();

?>

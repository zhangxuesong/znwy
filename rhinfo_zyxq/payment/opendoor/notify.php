<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';	
//$input = file_get_contents('php://input');
//$post = json_decode($input,true);
//$_SERVER
//$_POST
mylogging('opendoor', var_export($_POST, true));
if(!empty($_POST)){		
	exit('success');
}
exit('fail');
?>
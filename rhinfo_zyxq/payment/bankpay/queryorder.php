<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';
require '../../vendor/rhinfo/payapi.php';
exit('success');
if(!empty($_POST)){	
	if(!empty($_POST['out_trade_no'])){
		$params = array('tid'=>$_POST['out_trade_no']);
		$wechat = array(
			'bankmerchid' => $_POST['bankmerchid'],
			'ymfurl' => $_POST['ymfurl'],
			'bankkey' => $_POST['bankkey']
		);
		$res = repeat_bank_order_query($params,$wechat,4);	
		if (is_error($res)) {
		}
		else{ 			
			my_async_curl($_POST['notifyurl'],array('uniontid'=>$_POST['uniontid']));
		}
	}
	exit('success');
}
exit('fail');
?>
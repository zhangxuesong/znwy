<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';
require '../../vendor/rhinfo/payapi.php';

if(!empty($_GPC)){	
	if(!empty($_GPC['out_trade_no'])){
		$params = array('uniontid'=>$_GPC['out_trade_no']);
		$wechat = array(
			'bankmerchid' => $_GPC['bankmerchid'],
			'ymfurl' => $_GPC['ymfurl'],
			'bankkey' => $_GPC['bankkey']
		);
		$res = repeat_bank_order_query($params,$wechat,4);	
		if (is_error($res)) {
			mylogging('queryrsd_info',var_export($res,true));
			exit('fail');
		}
		else{ 			
			my_async_curl($_GPC['notifyurl'],array('uniontid'=>$_GPC['uniontid']));
			exit('success');
		}
	}
}
mylogging('queryrsd_info',var_export($_GPC,true));
exit('fail');
?>
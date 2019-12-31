<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';
require '../../vendor/rhinfo/payapi.php';
if(!empty($_GPC)){	
	if(!empty($_GPC['orderno'])){
		$params = array('orderno'=>$_GPC['orderno'],'out_trade_no'=>$_GPC['out_trade_no']);
		$wechat = array(
			'starorg' => $_GPC['starorg'],
			'starmerchid' => $_GPC['starmerchid'],
			'startrm' => $_GPC['startrm'],
			'starkey' => $_GPC['starkey']
		);
		$res = repeat_bank_order_query($params, $wechat,5);		
		if (is_error($res)) {
			mylogging('querystar_error',var_export($res,true));
			exit('fail');
		}
		else{ 			
			my_async_curl($_GPC['notifyurl'],array('uniontid'=>$_GPC['out_trade_no']));
			exit('success');
		}
	}
	mylogging('paystar_fail',var_export($_GPC,true));
	exit('fail');
}
mylogging('paystar_error','参数不正确');
exit('fail');
?>
<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';
require '../../vendor/rhinfo/payapi.php';
if(!empty($_GPC)){	
	if(!empty($_GPC['out_trade_no'])){
		$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
		$log = pdo_fetch($sql,array(':uniontid'=>$_GPC['out_trade_no']));	
		if(!empty($log) && $log['status'] == '0') {
			$params = array('tid'=>$_GPC['out_trade_no'],'fee'=>$log['fee']);
			$wechat = array(
				'appid' => $_GPC['appid'],			
				'mch_id' => $_GPC['mch_id'],
				'sub_mch_id' => $_GPC['sub_mch_id'],
				'apikey' => $_GPC['apikey']
			);		
			$res =  repeat_wechat_order_query($params, $wechat);
			if (is_error($res)) {
				mylogging('wechat_query_error','已经支付成功，请确认');
				exit('fail');
			}
			else{ 			
				my_async_curl($_GPC['notifyurl'],array('uniontid'=>$_GPC['out_trade_no']));
				exit('success');
			}
		}
	}	
}
mylogging('wechat_query_info',var_export($_GPC,true));
exit('fail');
?>
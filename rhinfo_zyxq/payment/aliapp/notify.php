<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';

if(!empty($_POST) && !empty($_POST['out_trade_no'])) {
	$out_trade_no = $_POST['out_trade_no'];	
	load()->web('common');
	$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
	$log = pdo_fetch($sql,array(':uniontid'=>$out_trade_no));	

	if(!empty($log) && $log['status'] == '0') {
		load()->web('common');
		$_W['uniacid'] =  $_W['weid'] = $log['uniacid'];
		$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
		$_W['acid'] = $_W['uniaccount']['acid'];		
		$record = array();
		$record['status'] = '1';
		pdo_update('rhinfo_zyxq_wepay_log',$record, array('plid' => $log['plid']));
		$site = WeUtility::createModuleSite('rhinfo_zyxq');
		if(!is_error($site)) {
			$method = 'payResult';
			if (method_exists($site,$method)) {
				$ret = array();
				$ret['uniacid'] = $log['uniacid'];
				$ret['acid'] = $log['acid'];
				$ret['result'] = 'success';
				$ret['type'] = $log['type'];
				$ret['from'] = 'notify';
				$ret['tid'] = $log['tid'];
				$ret['uniontid'] = $log['uniontid'];
				$ret['trade_type'] = 0;
				$ret['follow'] = 1;
				$ret['user'] = $log['openid'];
				$ret['fee'] = $log['fee'];
				$ret['tag'] = $log['tag'];
				$ret['is_usecard'] = $log['is_usecard'];
				$ret['card_type'] = $log['card_type'];
				$ret['card_fee'] = $log['card_fee'];
				$ret['card_id'] = $log['card_id'];
				$ret['paytime'] = time();			
				$site->$method($ret);
				exit('success');
			}
		}
	}
}
exit('fail');
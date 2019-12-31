<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
if(!empty($_POST)) {
	$out_trade_no = $_POST['out_trade_no'];	
	load()->web('common');
	load()->classs('coupon');
	$_W['uniacid'] = $_W['weid'] = intval($_POST['body']);
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	$_W['acid'] = $_W['uniaccount']['acid'];
	$setting = uni_setting($_W['uniacid'], array('payment'));
	$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
	$syscfg = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
	if ($_POST['body'] == 'site_store') {
		$setting['payment'] = setting_load('store_pay');
		$setting['payment'] = $setting['payment']['store_pay'];
	}
	if(is_array($setting['payment']) || !empty($syscfg['isalipay'])) {
		$alipay = $setting['payment']['alipay'];
		$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
		$params = array();
		$params[':uniontid'] = $out_trade_no;
		$log = pdo_fetch($sql, $params);
		$tag = unserialize($log['tag']);
		
		$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
		$property = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':pid'=>$tag['pid']));

		$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
		$region = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':rid'=>$tag['rid']));
		
		if($region['ispay']==1 && !empty($region['aliaccount'])){	
			$alipay['account'] = $region['aliaccount'];
			$alipay['partner'] = $region['alipartner'];
			$alipay['secret'] = $region['alisecret'];	
		}
		else{
			if($property['ispay']==1 && !empty($property['aliaccount'])){
				$alipay['account'] = $property['aliaccount'];
				$alipay['partner'] = $property['alipartner'];
				$alipay['secret'] = $property['alisecret'];
			}
			else{
				if($syscfg['isalipay']>0){
					$alipay['account'] = $syscfg['aliaccount'];
					$alipay['partner'] = $syscfg['alipartner'];
					$alipay['secret'] = $syscfg['alisecret'];
				}
			}
		}
		
		if(!empty($alipay)) {
			$prepares = array();
			foreach($_POST as $key => $value) {
				if($key != 'sign' && $key != 'sign_type') {
					$prepares[] = "{$key}={$value}";
				}
			}
			sort($prepares);
			$string = implode($prepares, '&');
			$string .= $alipay['secret'];
			$sign = md5($string);
			if($sign == $_POST['sign']) {
				$_POST['query_type'] = 'notify';
				WeUtility::logging('pay-alipay', var_export($_POST, true));
				$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
				$params = array();
				$params[':uniontid'] = $out_trade_no;
				$log = pdo_fetch($sql, $params);
				if(!empty($log) && $log['status'] == '0') {
					$log['transaction_id'] = $_POST['trade_no'];
					$record = array();
					$record['status'] = '1';
					pdo_update('rhinfo_zyxq_wepay_log', $record, array('plid' => $log['plid']));
					
					$site = WeUtility::createModuleSite('rhinfo_zyxq');
					if(!is_error($site)) {
						$method = 'payResult';
						if (method_exists($site, $method)) {
							$ret = array();
							$ret['uniacid'] = $log['uniacid'];
							$ret['result'] = 'success';
							$ret['type'] = $log['type'];
							$ret['from'] = 'notify';
							$ret['tid'] = $log['tid'];
							$ret['uniontid'] = $log['uniontid'];
							$ret['transaction_id'] = $log['transaction_id'];
							$ret['user'] = $log['openid'];
							$ret['fee'] = $log['fee'];
							$ret['is_usecard'] = $log['is_usecard'];
							$ret['card_type'] = $log['card_type'];
							$ret['card_fee'] = $log['card_fee'];
							$ret['card_id'] = $log['card_id'];
							$site->$method($ret);
							exit('success');
						}
					}
				}
			}
		}
	}
}
exit('fail');

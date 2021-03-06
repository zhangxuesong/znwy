<?php
error_reporting(0);
define('IN_MOBILE', true);
if (empty($_GET['out_trade_no'])) {
	exit('request failed.');
}
require '../../../../framework/bootstrap.inc.php';
load()->app('common');
load()->app('template');
$_W['uniacid'] = $_W['weid'] = intval($_GET['body']);
$setting = uni_setting($_W['uniacid'], array('payment'));
if ($_GET['exterface'] == 'create_direct_pay_by_user') {
	$setting['payment'] = setting_load('store_pay');
	$setting['payment'] = $setting['payment']['store_pay'];
}
$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
$syscfg = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
if (!is_array($setting['payment']) && empty($syscfg['isalipay'])) {
	exit('request failed.');
}
$alipay = $setting['payment']['alipay'];
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
$params = array();
$params[':uniontid'] = $_GET['out_trade_no'];
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

if (empty($alipay)) {
	exit('request failed.');
}
$prepares = array();
foreach ($_GET as $key => $value) {
	if ($key != 'sign' && $key != 'sign_type') {
		$prepares[] = "{$key}={$value}";
	}
}
sort($prepares);
$string = implode($prepares, '&');
$string .= $alipay['secret'];
$sign = md5($string);
if($sign == $_GET['sign']){
	$_GET['query_type'] = 'return';
	WeUtility::logging('pay-alipay', var_export($_GET, true));
	if($_GET['is_success'] == 'T' && ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS')) {
		$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
		$params = array();
		$params[':uniontid'] = $_GET['out_trade_no'];
		$log = pdo_fetch($sql, $params);
		if (!empty($log)) {
			$site = WeUtility::createModuleSite($log['module']);
			$method = 'payResult';			
			if ($log['status'] == 0 && ($_GET['total_fee'] == $log['card_fee'])) {
				$log['transaction_id'] = $_GET['trade_no'];
				$record = array();
				$record['status'] = '1';
				pdo_update('rhinfo_zyxq_wepay_log', $record, array('plid' => $log['plid']));
				/*
				if ($log['is_usecard'] == 1 && !empty($log['encrypt_code'])) {
					$coupon_info = pdo_get('coupon', array('id' => $log['card_id']), array('id'));
					$coupon_record = pdo_get('coupon_record', array('code' => $log['encrypt_code'], 'status' => '1'));
					load()->model('activity');
					$status = activity_coupon_use($coupon_info['id'], $coupon_record['id'], $log['module']);
				}*/
				if (!is_error($site)) {
					$site->uniacid = $_W['uniacid'];
					$site->inMobile = true;
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
					}
				}
			}
			if(!is_error($site)){
				$ret['uniontid'] = $log['uniontid'];
				$ret['result'] = 'success';
				$ret['from'] = 'return';
				$site->$method($ret);
				exit;
			}
		} else {
			$order = pdo_get('site_store_order', array('orderid' => $_GET['out_trade_no']));
			if (!empty($order)) {
				if ($order['type'] == 1) {
					pdo_update('site_store_order', array('type' => 3), array('orderid' => $_GET['out_trade_no']));
				}
				cache_delete(cache_system_key($order['uniacid'] . ':site_store_buy_modules'));
				cache_build_account_modules($order['uniacid']);
				header('Location: ./index.php?c=site&a=entry&direct=1&m=store&do=orders');
			}
		}
	}
} else {
	message('支付异常，请返回微信客户端查看订单状态或是联系管理员', '', 'error');
}
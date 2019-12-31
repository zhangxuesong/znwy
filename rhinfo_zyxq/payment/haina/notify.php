<?php
define('IN_MOBILE', true);
libxml_disable_entity_loader(true);
require '../../../../framework/bootstrap.inc.php';
$input = file_get_contents('php://input');
$isxml = true;
if (!empty($input) && empty($_GET['out_trade_no'])) {
	$obj = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);
	if (empty($data)) {
		$result = array(
			'return_code' => 'FAIL',
			'return_msg' => ''
		);
		echo array2xml($result);
		exit;
	}
	if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
		$result = array(
			'return_code' => 'FAIL',
			'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']
		);
		echo array2xml($result);
		exit;
	}
	$get = $data;
} else {
	$isxml = false;
	$get = $_GET;
}
load()->web('common');
$_W['uniacid'] = $_W['weid'] = intval($get['attach']);
$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
$_W['acid'] = $_W['uniaccount']['acid'];
$setting = uni_setting($_W['uniacid'], array('payment'));
$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
$syscfg = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
if(is_array($setting['payment']) || !empty($syscfg['iswxpay'])) {		
	WeUtility::logging('pay', var_export($get, true));
	$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
	$params = array();
	$params[':uniontid'] = $get['out_trade_no'];
	$log = pdo_fetch($sql, $params);
	$tag = unserialize($log['tag']);			
	if(!empty($log) && $log['status'] == '0') {
		$log['tag'] = iunserializer($log['tag']);
		$log['tag']['transaction_id'] = $get['transaction_id'];
		$log['uid'] = $log['tag']['uid'];
		$record = array();
		$record['status'] = '1';
		$record['tag'] = iserializer($log['tag']);
		pdo_update('rhinfo_zyxq_wepay_log', $record, array('plid' => $log['plid']));
		$site = WeUtility::createModuleSite('rhinfo_zyxq');
		if(!is_error($site)) {
			$method = 'payResult';
			if (method_exists($site, $method)) {
				$ret = array();
				$ret['uniacid'] = $log['uniacid'];
				$ret['acid'] = $log['acid'];
				$ret['result'] = 'success';
				$ret['type'] = $log['type'];
				$ret['from'] = 'notify';
				$ret['tid'] = $log['tid'];
				$ret['uniontid'] = $log['uniontid'];
				$ret['transaction_id'] = $log['transaction_id'];
				$ret['trade_type'] = $get['trade_type'];
				$ret['follow'] = $get['is_subscribe'] == 'Y' ? 1 : 0;
				$ret['user'] = empty($get['openid']) ? $log['openid'] : $get['openid'];
				$ret['fee'] = $log['fee'];
				$ret['tag'] = $log['tag'];
				$ret['is_usecard'] = $log['is_usecard'];
				$ret['card_type'] = $log['card_type'];
				$ret['card_fee'] = $log['card_fee'];
				$ret['card_id'] = $log['card_id'];
				if(!empty($get['time_end'])) {
					$ret['paytime'] = strtotime($get['time_end']);
				}
				else{
					$ret['paytime'] = TIMESTAMP;
				}
				$site->$method($ret);
				if($isxml) {
					$result = array(
						'return_code' => 'SUCCESS',
						'return_msg' => 'OK'
					);
					echo array2xml($result);
					exit;
				}
				else {
					exit('success');
				}
			}
		}
	}
}
if($isxml) {
	$result = array(
		'return_code' => 'FAIL',
		'return_msg' => ''
	);
	echo array2xml($result);
	exit;
}
 else {
	exit('fail');
}

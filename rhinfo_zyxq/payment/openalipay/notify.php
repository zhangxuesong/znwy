<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
require '../../vendor/rhinfo/rhinfo.php';
require '../../vendor/rhinfo/payapi.php';
//$input = file_get_contents('php://input');
//$post = json_decode($input,true);
//$_SERVER
//$_POST
mylogging('alipay', var_export($_POST, true));
if(!empty($_POST)) {		
	load()->web('common');		
	$sql = "select * from ".tablename("rhinfo_zyxq_room").' where lifepay_hid = :hid ';
	$room = pdo_fetch($sql,array(':hid'=>$_POST['body']));		
	$_W['uniacid'] = $_W['weid'] = $room['weid'];
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	$_W['acid'] = $_W['uniaccount']['acid'];
	
	$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
	$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));

	$sql = "select * from ".tablename("rhinfo_zyxq_region").' where weid = :weid and id = :rid ';
	$region = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':rid'=>$room['rid']));
	
	$trade_no = $_POST['trade_no'];
	
	$set = array();	
	$set['app_id'] = $sysset['alipay_appid'];
	$set['prikey'] = $sysset['alipay_rsa2'];								
	$set['app_auth_token'] = $region['lifepay_token'];	
	$set['method'] = "pay.result.query";
	$params = '{
		"trade_no":"'.$trade_no.'"
		  }';
	$res = my_alipay_life($set,$params);
	if(is_error($res)){	  
	   exit($res['message']);
	}
	else{
		$res = json_decode($res,1);						
		$res = $res['alipay_eco_cplife_pay_result_query_response'];
		if($res['code']!=='10000'){
			if(!empty($res['sub_code'])){
				exit($res['sub_msg'].$res['sub_code']);
			}
			else{
				exit($res['msg'].$res['code']);
			}			
		}
		$result = $res['assoc_bill_details'];
		$feebill = array();
		$billid= '';
		foreach($result as $k=>$value){
			if($k==0){
				$sql = 'select * from '.tablename("rhinfo_zyxq_feebill").' where weid=:weid and id=:id';							
				$feebill = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':id'=>$value['bill_entry_id']));
			}
			$billid .= $value['bill_entry_id'].',';
		}
		
		$billid = substr($billid, 0, -1);
		$sql = 'select * from '.tablename("rhinfo_zyxq_member").' where weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and status=0 and deleted=0';							
		$member = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':pid'=>$feebill['pid'],':rid'=>$feebill['rid'],':bid'=>$feebill['bid'],':tid'=>$feebill['tid'],':hid'=>$feebill['hid']));
								
		$paynopre = !empty($sysset['paynopre'])?$sysset['paynopre']:'Property';
		$sql = "select max(payno) from". tablename('rhinfo_zyxq_paylog')." where weid = :weid and payno like '".$paynopre."%'";
		$payno = pdo_fetchcolumn($sql,array(':weid'=>$_W['uniacid']));
		$payno = createnum(substr($payno,strlen($paynopre),14));
		$payno = $paynopre.$payno;
		$myps = array();
		$myps['module'] =  'rhinfo_zyxq';
		$myps['ordersn'] =  $payno;
		$myps['pid'] =  $feebill['pid'];
		$myps['rid'] =  $feebill['rid'];
		$myps['title'] = $feebill['title'];
		$myps['payopenid'] = $member['openid'];
		$myps['payuser'] = $member['uid'];	
		$myps['billid']	= $base64_encode($billid);
		$myps['feetype'] = 1;	
		$myps['creditfee'] = 0;	
	}
		
	$_POST['query_type'] = 'notify';
	WeUtility::logging('pay-alilifepay', var_export($_POST, true));
	
	$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
	$params = array();
	$params[':uniontid'] = $trade_no;
	$log = pdo_fetch($sql, $params);	
		
	if (empty($log)) {
		$log = array(
			'uniacid' => $_W['uniacid'],
			'acid' => $_W['acid'],
			'uniontid' => $trade_no,
			'type'=> 'alipaylife',
			'openid' => !empty($member['openid'])?$member['openid']:$res['buyer_user_id'],
			'module' => 'rhinfo_zyxq',
			'tid' => $trade_no,
			'fee' => $res['total_amount'],
			'card_fee' => $res['total_amount'],
			'status' => '1',
			'is_usecard' => '0',
			'card_id' => 0,
			'uid' => $member['uid'],
			'pid' => $myps['pid'],
			'rid' => $myps['rid'],
			'feetype' => $myps['feetype'],
			'tag' => serialize($myps),
			'paytime'=>time()
		);
		pdo_insert('rhinfo_zyxq_wepay_log', $log);
	}
		
	$site = WeUtility::createModuleSite('rhinfo_zyxq');
	if(!is_error($site)) {
		$method = 'payResult';
		if (method_exists($site, $method)) {
			$ret = array();
			$ret['weid'] = $_W['uniacid'];
			$ret['uniacid'] = $_W['uniacid'];
			$ret['result'] = 'success';
			$ret['type'] = 'alipaylife';
			$ret['from'] = 'notify';
			$ret['tid'] = $trade_no;
			$ret['uniontid'] = $trade_no;
			$ret['transaction_id'] = $trade_no;
			$ret['user'] = $res['buyer_user_id'];
			$ret['fee'] = $res['total_amount'];
			$ret['is_usecard'] = 0;
			$ret['card_type'] = 0;
			$ret['card_fee'] = $res['total_amount'];
			$ret['card_id'] = 0;
			$site->$method($ret);
			exit('success');
		}
	}
}
exit('fail');
?>
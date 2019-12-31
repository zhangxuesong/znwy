<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';
$input = file_get_contents('php://input');
$post = json_decode($input,true);
//mylogging('parklock', var_export($post, true));
if(!empty($post)){	
	if($post['event']=='lockUpdate'){			
		$sql = "select * from ".tablename("rhinfo_zyxq_parkinglock").' where lockmac = :lockmac';
		$parkinglock = pdo_fetch($sql,array(':lockmac'=>$post['lockMac']));	
		load()->web('common');
		$_W['uniacid'] = $_W['weid'] = $parkinglock['weid'];
		$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
		$_W['acid'] = $_W['uniaccount']['acid'];
	
		$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
		$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
		
		$sql = "select * from ".tablename("rhinfo_zyxq_parklocklog").' where lockid = :lockid  order by id desc';
		$parklocklog = pdo_fetch($sql,array(':lockid'=>$parkinglock['id']));	
		
		load()->classs('weixin.account');
		load()->func('communication');
		$obj = new WeiXinAccount();
		$access_token = $obj->fetch_available_token();			
		$directurl = '';
		$onlineState = $post['onlineState']?'在线':'离线';
		$eleState = '电量等级'.$post['eleState'];
		$csbState = $post['csbState']?'有车':'无车';
		$buzzerState = $post['buzzerState']?'蜂鸣器开':'蜂鸣器关';
		if($post['updownState']==1){
			$updownState = '已升起';
		}
		elseif($post['updownState']==2){
			$updownState = '已下降';
		}
		else{
			$updownState = '变化中';
		}
		$topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
		$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
		$postdata = array(
			'first' => array(
					'value' => '车位锁状态变化通知'
				),
			'keyword1' => array(
					'value' => '亲，您的车位锁'.$onlineState.'，'.$updownState,
					'color' => $topcolor
				),
			'keyword2'	=> array(
					'value' => date('Y-m-d H:i'),
					'color' => $textcolor
				),
			'keyword3'    => array(
					'value' => $eleState.' '.$buzzerState.' '.$csbState,
					'color' => $textcolor
				),
			'remark'    => array(
					'value' => '车位锁状态发生变化，请确认!',
				)
		);
		$data = array(
				'touser' => $parkinglock['openid'],
				'template_id' => $sysset['tplid1'],
				'url' => $directurl,
				'topcolor' => $topcolor,
				'data' => $postdata,
			);
		$json = json_encode($data);
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
		ihttp_post($url,$json);
		if($parkinglock['uid'] == $parklocklog['uid']){
		}
		else{
			$data['touser'] = $parklocklog['openid'];
			$json = json_encode($data);
			ihttp_post($url,$json);	
		}				
	}		
	elseif($post['event']=='gatewayUpdate'){
		load()->classs('weixin.account');
		load()->func('communication');
		$obj = new WeiXinAccount();
		$access_token = $obj->fetch_available_token();			
		$directurl = '';
		$postdata = array(
			'first' => array(
					'value' => '网关状态变化通知'
				),
			'keyword1' => array(
					'value' => '网关编号'.$post['gateway_id'],
					'color' => $topcolor
				),
			'keyword2'	=> array(
					'value' => date('Y-m-d H:i'),
					'color' => $textcolor
				),
			'keyword3'    => array(
					'value' => $post['isOnline']?'在线':'离线',
					'color' => $textcolor
				),
			'remark'    => array(
					'value' => '网关状态发生变化，请检查!',
				)
		);
		$data = array(
				'touser' => $parkinglock['openid'],
				'template_id' => $sysset['tplid1'],
				'url' => $directurl,
				'topcolor' => $topcolor,
				'data' => $postdata,
			);
		$json = json_encode($data);
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
	//	$res = ihttp_post($url,$json);				
	}
	exit('success');
}
exit('fail');
?>
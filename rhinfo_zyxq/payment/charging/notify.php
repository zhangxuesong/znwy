<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';	
require '../../vendor/rhinfo/rhinfo.php';
require '../../vendor/rhinfo/devapi.php';
$input = file_get_contents('php://input');
$post = json_decode($input,true);
//mylogging('charging', var_export($_POST, true));
if(!empty($_POST) || !empty($post)) {
	load()->web('common');
	$isnotify = false;
	$backpay = false;
	if($_POST['action']=='upload_stop_charging'){		
		$sql = "select * from ".tablename("rhinfo_zycj_charging_log").' where out_trade_no = :out_trade_no ';
		$charging_log = pdo_fetch($sql,array(':out_trade_no'=>$_POST['out_trade_no']));	
		
		$_W['uniacid'] = $_W['weid'] = $charging['weid'];
		$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
		$_W['acid'] = $_W['uniaccount']['acid'];			
		
		$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
		$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));

		if($charging['trade_no'] == $_POST['trade_no']){
			$isnotify = true;
			pdo_update('rhinfo_zycj_charging_log',array('status'=>1),array('weid'=>$_W['uniacid'],'id'=>$charging['id']));
			$stop_reason = array('0' => '购买的充电时间用完了', '1' => '用户手动停止（拔插头，或是按了停止按钮）','2'=>'充电满了，自动停止','11'=>'设备或是端口出现问题，被迫停止');	
			$topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
			$postdata = array(
				'first' => array(
						'value' => '智能充电通知'
					),
				'keyword1' => array(
						'value' => '您的充电已经结束了',
						'color' => $topcolor
					),
				'keyword2'	=> array(
						'value' => date('Y-m-d H:i'),
						'color' => $textcolor
					),
				'keyword3'    => array(
						'value' => $stop_reason[$_POST['stop_reason']] ,
						'color' => $textcolor
					),
				'remark'    => array(
						'value' => '您的充电已经结束,请现场确认!',
					)
			);
		}
	}
	if(!empty($_POST['Msg_type'])){
		if($_POST['Msg_type']=='mx10_status'){	
		
	    }
		elseif($_POST['Msg_type']=='Send_Ins'){	
			$isnotify = true;	
			$sql = "select * from ".tablename("rhinfo_zycj_charging").' where devicesn = :devicesn ';
			$charging = pdo_fetch($sql,array(':devicesn'=>$_POST['Device_code']));	
			
			$_W['uniacid'] = $_W['weid'] = $charging['weid'];
			$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
			$_W['acid'] = $_W['uniaccount']['acid'];
			
			$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
			$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
			
			$sql = "select * from ".tablename("rhinfo_zycj_charging_log").' where chargid = :chargid and port=:port order by id desc';
			$myport = preg_replace('/^0*/','',$_POST['Device_port']);
			$charging_log = pdo_fetch($sql,array(':chargid'=>$charging['id'],':port'=>$myport));	
            $topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
			if($_POST['Status']=='Success'){
				pdo_update('rhinfo_zycj_charging_log',array('status'=>1,'starttime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));				
				$postdata = array(
					'first' => array(
							'value' => '智能充电通知'
						),
					'keyword1' => array(
							'value' => '充电开始',
							'color' => $topcolor
						),
					'keyword2'	=> array(
							'value' => date('Y-m-d H:i'),
							'color' => $textcolor
						),
					'keyword3'    => array(
							'value' => '充电端口'.$myport.'，还剩充电时间'.intval($_POST['Port_time']).'分钟',
							'color' => $textcolor
						),
					'remark'    => array(
							'value' => '您的爱车已经开始充电，请耐心等待!',
						)
				);
			}
			else{                				
				$postdata = array(
					'first' => array(
							'value' => '智能充电通知'
						),
					'keyword1' => array(
							'value' => '充电异常',
							'color' => $topcolor
						),
					'keyword2'	=> array(
							'value' => date('Y-m-d H:i'),
							'color' => $textcolor
						),
					'keyword3'    => array(
							'value' => '充电端口'.$myport.'，'.intval($_POST['Send_pluse']).'个脉冲' ,
							'color' => $textcolor
						),
					'remark'    => array(
							'value' => '您的爱车充电异常，请检查!',
						)
				);
				$url = "paycloud2/Api.php";				
				$post_data = array(
					'action' => 'SendIns',					
					'token' => $sysset['mx_appkey'],
					'appid' => $sysset['mx_appid'],
					'port' => $_POST['Device_Port'],
					'pluse' => $_POST['Send_pluse'],
					'device_code' => $_POST['Device_code']
				);				
				$rs = Mx_httpPost($url,$post_data);			
			}		
		}		
		elseif($_POST['Msg_type']=='Channel_status'){
			$isnotify = true;
			$sql = "select * from ".tablename("rhinfo_zycj_charging").' where devicesn = :devicesn ';
			$charging = pdo_fetch($sql,array(':devicesn'=>$_POST['Device_code']));	
			
			$_W['uniacid'] = $_W['weid'] = $charging['weid'];
			$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
			$_W['acid'] = $_W['uniaccount']['acid'];			
			
			$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
			$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
			
			$sql = "select * from ".tablename("rhinfo_zycj_charging_log").' where chargid = :chargid and port=:port order by id desc ';
			$myport = preg_replace('/^0*/', '',$_POST['Device_port']);
			$charging_log = pdo_fetch($sql,array(':chargid'=>$charging['id'],':port'=>$myport));	
            $topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];
			if($_POST['Status']=='Success'){
				 if($_POST['Port_status']=='1'){	
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '充电中',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '充电端口'.$myport.'，还剩充电时间'.intval($_POST['Port_time']).'分钟' ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '您的爱车充电中，请耐心等待!',
							)
					);					
				 }
				 elseif($_POST['Port_status']=='2'){					    
						pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));				
						$postdata = array(
							'first' => array(
									'value' => '智能充电通知'
								),
							'keyword1' => array(
									'value' => '充电结束',
									'color' => $topcolor
								),
							'keyword2'	=> array(
									'value' => date('Y-m-d H:i'),
									'color' => $textcolor
								),
							'keyword3'    => array(
									'value' => '充电端口'.$myport.'，充满自动结束充电' ,
									'color' => $textcolor
								),
							'remark'    => array(
									'value' => '您的爱车已充满!',
								)
						);
						if($charging['paytype']==2){
							$backpay = true;
						}
						$url = "paycloud2/Api.php";				
						$post_data = array(
							'action' => 'Restor_Port',					
							'token' => $sysset['mx_appkey'],
							'appid' => $sysset['mx_appid'],
							'port' => $_POST['Device_Port'],
							'device_code' => $_POST['Device_code']
						);				
					//	$rs = Mx_httpPost($url,$post_data);		
					}				
				 elseif($_POST['Port_status']=='3'){
						if($charging['paytype']==2){
							pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));
							$backpay = true;
						}
						$postdata = array(
							'first' => array(
									'value' => '智能充电通知'
								),
							'keyword1' => array(
									'value' => '充电超功率',
									'color' => $topcolor
								),
							'keyword2'	=> array(
									'value' => date('Y-m-d H:i'),
									'color' => $textcolor
								),
							'keyword3'    => array(
									'value' => '充电端口'.$myport.'，还剩充电时间'.intval($_POST['Port_time']).'分钟',
									'color' => $textcolor
								),
							'remark'    => array(
									'value' => '您的爱车已超出所承受功率!',
								)
						);
				 }
				 elseif($_POST['Port_status']=='0'){
					if($charging['paytype']==2){
						pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));
						$backpay = true;
					}
					else{
						if(intval($_POST['Channel_time'])==0){
							pdo_update('rhinfo_zycj_charging_log',array('status'=>0),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id'])); 
						}
						else{
							pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id'])); 
						}
					}
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '充电异常',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '充电端口'.$myport.'，还剩充电时间'.intval($_POST['Port_time']).'分钟' ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '您的爱车充电可能被拔掉，请尽快确认!',
							)
					);
				}
			}
		}
		elseif($_POST['Msg_type']=='Set_parameters'){
			if($_POST['Status']=='Success'){
				$data = array(
					'weid' => $_W['weid'],
					'pid' => 0,							
					'do' => 'charging',
					'op' => 'rule',
					'title' => '设定档位和价格',
					'content' => '设定档位成功'.$_POST['Device_time'].'分钟'.$_POST['Device_Current'].'mA',
					'ip' => $_W['clientip'],
					'cuid' => 0,
					'ctime' => time()
				);
				pdo_insert("rhinfo_zyxq_syslog", $data);
			}
			else{
				$url = "paycloud2/Api.php";
				$post_data = array(
					'action' => 'Set_Device',					
					'token' => $sysset['mx_appkey'],
					'appid' => $sysset['mx_appid'],
					'current' => '3',
					'times' => '60',
					'device_code' => $_POST['Device_code'],
				);
			   	$rs=Mx_httpPost($url,$post_data);	
			}
		}
		elseif($_POST['Msg_type']=='Scan_terminal'){
			$sql = "select * from ".tablename("rhinfo_zycj_charging").' where devicesn = :devicesn ';
			$charging = pdo_fetch($sql,array(':devicesn'=>$_POST['Device_code']));	
			
			$_W['uniacid'] = $_W['weid'] = $charging['weid'];
			$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
			$_W['acid'] = $_W['uniaccount']['acid'];			
			
			$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
			$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
			if($_POST['Status']=='Success'){
				if($_POST['Port_status']=='02'){  
					$url = "paycloud2/Api.php";				
					$post_data = array(
						'action' => 'Restor_Port',					
						'token' => $sysset['mx_appkey'],
						'appid' => $sysset['mx_appid'],
						'port' => $_POST['Device_Port'],
						'device_code' => $_POST['Device_code']
					);				
				//	$rs = Mx_httpPost($url,$post_data);		
				}				
			}
		}
		elseif($_POST['Msg_type']=='IC_consume'){
			$sql = "select * from ".tablename("rhinfo_zycj_charging").' where devicesn = :devicesn ';
			$charging = pdo_fetch($sql,array(':devicesn'=>$_POST['Device_code']));	
			
			$_W['uniacid'] = $_W['weid'] = $charging['weid'];
			$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
			$_W['acid'] = $_W['uniaccount']['acid'];			
			
			$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
			$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
			if($_POST['Status']=='Success'){
				if($_POST['Port_status']=='0'){  
					$mycard = pdo_get('rhinfo_zycj_charging_vipcard',array('weid'=>$_W['uniacid'],'cardno'=>$_POST['Ic_card'],'status'=>1));
					load()->model('mc');
					$openid = mc_uid2openid($mycard['uid']);
					$fee = 0;
					$plus = 1;
				//	$res = mc_credit_update($mycard['uid'], 'credit2', -$fee , array(0, '智能充电','rhinfo_zyxq'));
					$charging_log = array(
						'weid' => $_W['uniacid'],
						'chargid' => $charging['id'],
						'title' => $charging['title'],
						'port'=> $_POST['Device_Port'],
						'openid' => $openid,
						'out_trade_no' => 'ok',					
						'fee' => 0,
						'hour' => intval($_POST['Time_left']/60),
						'plus' => intval($_POST['Time_left']/60),
						'status' => 1,
						'uid' => $_W['member']['uid'],
						'ctime' => TIMESTAMP
					);
					if ($res){				
				//		pdo_insert('rhinfo_zycj_charging_log', $charging_log);
					}
					$url = "paycloud2/Api.php";				
					$post_data = array(
						'action' => 'SendIns',					
						'token' => $sysset['mx_appkey'],
						'appid' => $sysset['mx_appid'],
						'port' => $_POST['Device_Port'],
						'pluse' => sprintf("%02d",$plus),
						'device_code' => $_POST['Device_code']
					);				
				//	$rs = Mx_httpPost($url,$post_data);		
				}				
			}
		}
		else{
			mylogging('charging', var_export($_POST, true));
		}
	}
	if(!empty($_SERVER['HTTP_API'])){
		if(!empty($post['equipCd'])){
		//	mylogging('input', var_export($post, true));
			$sql = "select * from ".tablename("rhinfo_zycj_charging").' where devicesn = :devicesn ';
			$charging = pdo_fetch($sql,array(':devicesn'=>$post['equipCd']));	
			
			$_W['uniacid'] = $_W['weid'] = $charging['weid'];
			$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
			$_W['acid'] = $_W['uniaccount']['acid'];
			
			$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
			$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
			
			$sql = "select * from ".tablename("rhinfo_zycj_charging_log").' where chargid = :chargid and port=:port order by id desc';
			$charging_log = pdo_fetch($sql,array(':chargid'=>$charging['id'],':port'=>$post['port']));
			$topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
			if($_SERVER['HTTP_API']=='net.equip.charge.slow.async.notice.finish'){
				 $isnotify = true;
				 if($post['reason']=='1'){	
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '用户终止',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '还剩充电时间'.intval($post['value']).'分钟' ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '您的爱车充电终止，请确认!',
							)
					);					
				 }
				 elseif($post['reason']=='0'){
					 $postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '充电时间已到',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '还剩充电时间0分钟' ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '您的爱车充电时间已到，请确认!',
							)
					);					
				 }
				 elseif($post['reason']=='2'){					    
						pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));				
						$postdata = array(
							'first' => array(
									'value' => '智能充电通知'
								),
							'keyword1' => array(
									'value' => '充电结束',
									'color' => $topcolor
								),
							'keyword2'	=> array(
									'value' => date('Y-m-d H:i'),
									'color' => $textcolor
								),
							'keyword3'    => array(
									'value' => '充满自动结束充电' ,
									'color' => $textcolor
								),
							'remark'    => array(
									'value' => '您的爱车已充满!',
								)
						);		
					}				
				 elseif($post['reason']=='4'){
						if($charging['paytype']==2){
							pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));
							$backpay = true;
						}
						$postdata = array(
							'first' => array(
									'value' => '智能充电通知'
								),
							'keyword1' => array(
									'value' => '充电超功率',
									'color' => $topcolor
								),
							'keyword2'	=> array(
									'value' => date('Y-m-d H:i'),
									'color' => $textcolor
								),
							'keyword3'    => array(
									'value' => '还剩充电时间'.intval($_POST['Port_time']).'分钟',
									'color' => $textcolor
								),
							'remark'    => array(
									'value' => '您的爱车已超出所承受功率!',
								)
						);
				 }
				 elseif($post['reason']=='3'){
					if($charging['paytype']==2){
						pdo_update('rhinfo_zycj_charging_log',array('status'=>2,'endtime'=>time()),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));
						$backpay = true;
					}
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '充电异常',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '还剩充电时间'.intval($post['value']).'分钟' ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '您的爱车充电可能被拔掉，请尽快确认!',
							)
					);
				}
			}
			elseif($_SERVER['HTTP_API']=='net.equip.charge.slow.async.notice.fault'){
				if($post['errorCode']=='1'){	
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '端口输出故障',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '端口'.$post['port'] ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '端口输出故障!',
							)
					);					
				 }
				elseif($post['errorCode']=='2'){	
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '机器整体充电功率过大',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '端口'.$post['port'] ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '机器整体充电功率过大，请确认!',
							)
					);					
				 }
				elseif($post['errorCode']=='3'){	
					$postdata = array(
						'first' => array(
								'value' => '智能充电通知'
							),
						'keyword1' => array(
								'value' => '电源故障',
								'color' => $topcolor
							),
						'keyword2'	=> array(
								'value' => date('Y-m-d H:i'),
								'color' => $textcolor
							),
						'keyword3'    => array(
								'value' => '端口'.$post['port'] ,
								'color' => $textcolor
							),
						'remark'    => array(
								'value' => '电源故障，请确认!',
							)
					);					
				 }
			}
		}
	}
	$siteurl =  empty($sysset['siteurl'])?'//'.$_SERVER['HTTP_HOST']:$sysset['siteurl'];
	if($isnotify && !empty($sysset['tplid1'])){
		load()->classs('weixin.account');
		load()->func('communication');
		$obj = new WeiXinAccount();
		$access_token = $obj->fetch_available_token();			
		$directurl = $siteurl.'app/index.php?i='.$_W['uniacid'].'&c=entry&op=my&do=charging&m=rhinfo_zyxq';
		$data = array(
				'touser' => $charging_log['openid'],
				'template_id' => $sysset['tplid1'],
				'url' => $directurl,
				'topcolor' => $topcolor,
				'data' => $postdata,
			);
		$json = json_encode($data);
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
		$res = ihttp_post($url,$json);
	}
	if($backpay){
		load()->model('mc');
		$timediff = time() - $charging_log['starttime'];  
		$hours = round($timediff/3600,2);  
		$sql = 'select * from '.tablename("rhinfo_zycj_charging_rule").' where weid=:weid and chargid = :chargid order by hour';
		$rules = pdo_fetch($sql, array(':weid'=>$_W['uniacid'],':chargid'=>$charging['id']));	
		$price = empty($rules)?1:$rules['price']/$rules['hour'];
		$fee = round($hours*$price,0);
		$res = mc_credit_update($charging_log['uid'], 'credit2', -$fee, array(0, '智能充电','rhinfo_zyxq'));
		if($res){
			pdo_update('rhinfo_zycj_charging_log',array('status'=>1,'fee'=>$fee),array('weid'=>$_W['uniacid'],'id'=>$charging_log['id']));				
			$crediturl = $siteurl.'app/index.php?i='.$_W['uniacid'].'&c=entry&op=credit2&do=service&m=rhinfo_zyxq';
			mc_notice_credit2($charging_log['openid'],$charging_log['uid'], $fee, '智能充电',$crediturl,'谢谢支持，点击查看详情');
		}
	}	
	exit('success');
}
exit('fail');
?>
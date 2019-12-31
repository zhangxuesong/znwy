<?php
error_reporting(0);
define('IN_MOBILE', true);
require '../../../../framework/bootstrap.inc.php';
require '../../vendor/rhinfo/rhinfo.php';	
require '../../vendor/rhinfo/model.php';	
require '../../vendor/rhinfo/payapi.php';	
$post = array();
parse_str($_SERVER['QUERY_STRING'],$post); //CTPCAR

if(!empty($post)){	
	if(!empty($post['parkno'])){
		$sql = 'select * from '.tablename("rhinfo_zyxq_parkinglot").' where pc_plotid=:pc_plotid ';
		$park = pdo_fetch($sql, array(':pc_plotid'=>$post['parkno']));		
		load()->web('common');
		$_W['uniacid'] = $_W['weid'] = $park['weid'];
		$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
		$_W['acid'] = $_W['uniaccount']['acid'];
	
		$sql = "select * from ".tablename("rhinfo_zyxq_sysset").' where weid = :weid';
		$sysset = pdo_fetch($sql,array(':weid'=>$_W['uniacid']));
				
		$isnotice = false;
		$siteurl =  empty($sysset['siteurl'])?'//'.$_SERVER['HTTP_HOST']:$sysset['siteurl'];
		if($post['taskname']=='incar'){//入场
			//通知车主
			$sql = 'select * from '.tablename("rhinfo_zyxq_car").' where weid=:weid and carno=:carno';
			$mycar = pdo_fetch($sql, array(':weid'=>$_W['weid'],':carno'=>strtoupper($post['key1value'])));
			$openid = !empty($mycar['openid'])?$mycar['openid']:0;
			$isnotice = true;
			$directurl = $siteurl.'app/index.php?i='.$_W['uniacid'].'&c=entry&op=pay&do=car&parkid='.$park['id'].'&m=rhinfo_zyxq';
			$topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
			$postdata = array(
				'first' => array(
						'value' => '您的爱车已入场'
					),
				'keyword1' => array(
						'value' => '亲，您的车辆已入'.$park['title'],
						'color' => $topcolor
					),
				'keyword2'	=> array(
						'value' => date('Y-m-d H:i'),
						'color' => $textcolor
					),
				'keyword3'    => array(
						'value' => $park['address'],
						'color' => $textcolor
					),
				'remark'    => array(
						'value' => strtoupper($post['key1value']).',感谢您的支持，谢谢!',
					)
			);
			$iodata = array('weid'=>$_W['uniacid'],'parklotid'=>$park['id'],'ioid'=>0,'io'=>1,'carno'=>strtoupper($post['key1value']),'intime'=>time(),'status'=>1,'ctime'=>time());
			pdo_insert('rhinfo_zyxq_car_iolog',$iodata);
		}		
		elseif($post['taskname']=='waitout'){
			//通知缴费人
			$sql = 'select * from '.tablename("rhinfo_zyxq_parkpay_log").' where weid=:weid and carno=:carno and category=1 order by id desc';
			$mycar = pdo_fetch($sql, array(':weid'=>$_W['weid'],':carno'=>strtoupper($post['key1value'])));
			load()->model('mc');
			$openid = mc_uid2openid($mycar['cuid']);
			$isnotice = true;
			$directurl = $siteurl.'app/index.php?i='.$_W['uniacid'].'&c=entry&op=pay&do=car&parkid='.$park['id'].'&m=rhinfo_zyxq';
			$topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
			$postdata = array(
				'first' => array(
						'value' => '您的爱车等待出场'
					),
				'keyword1' => array(
						'value' => '亲，您的车辆缴费中...',
						'color' => $topcolor
					),
				'keyword2'	=> array(
						'value' => date('Y-m-d H:i'),
						'color' => $textcolor
					),
				'keyword3'    => array(
						'value' => $park['address'],
						'color' => $textcolor
					),
				'remark'    => array(
						'value' => strtoupper($post['key1value']).',感谢您的支持，谢谢!',
					)
			);
		}
		elseif($post['taskname']=='outcar'){
			//通知缴费人
			$sql = 'select * from '.tablename("rhinfo_zyxq_parkpay_log").' where weid=:weid and carno=:carno and category=1 order by id desc';
			$mycar = pdo_fetch($sql, array(':weid'=>$_W['weid'],':carno'=>strtoupper($post['key1value'])));
			load()->model('mc');
			$openid = mc_uid2openid($mycar['cuid']);
			//通知车主
			if(empty($openid)){
				$sql = 'select * from '.tablename("rhinfo_zyxq_car").' where weid=:weid and carno=:carno';
				$mycar = pdo_fetch($sql, array(':weid'=>$_W['weid'],':carno'=>strtoupper($post['key1value'])));
				$openid = !empty($mycar['openid'])?$mycar['openid']:0;
			}
			$isnotice = true;
			$directurl = '';
			$topcolor = empty($sysset['topcolor'])? '#FF683F' : $sysset['topcolor'];
			$textcolor = empty($sysset['textcolor'])? '#000' : $sysset['textcolor'];	
			$postdata = array(
				'first' => array(
						'value' => '您的爱车已出场'
					),
				'keyword1' => array(
						'value' => '亲，您的车辆已出'.$park['title'],
						'color' => $topcolor
					),
				'keyword2'	=> array(
						'value' => date('Y-m-d H:i'),
						'color' => $textcolor
					),
				'keyword3'    => array(
						'value' => $park['address'],
						'color' => $textcolor
					),
				'remark'    => array(
						'value' => strtoupper($post['key1value']).',感谢您的支持，谢谢!',
					)
			);
			$iodata = array('weid'=>$_W['uniacid'],'parklotid'=>$park['id'],'ioid'=>0,'io'=>2,'carno'=>strtoupper($post['key1value']),'outtime'=>time(),'status'=>1,'ctime'=>time());
			pdo_insert('rhinfo_zyxq_car_iolog',$iodata);
		}
		elseif($post['taskname']=='paypost' && !empty($post['key2value']) && !empty($post['payfee'])){			
			$site = WeUtility::createModuleSite('rhinfo_zyxq');
			if(!is_error($site)) {
				$method = 'my_scancode_pay';
				if (method_exists($site, $method)){				
					$sql = 'select * from '.tablename("rhinfo_zyxq_car").' where weid=:weid and carno=:carno';
					$mycar = pdo_fetch($sql, array(':weid'=>$_W['weid'],':carno'=>strtoupper($post['key1value'])));
					$openid = !empty($mycar['openid'])?$mycar['openid']:0;
					$paynopre = !empty($sysset['paynopre'])?$sysset['paynopre']:'Pay';
					$sql = "select max(payno) from". tablename('rhinfo_zyxq_paylog')." where weid = :weid and payno like '".$paynopre."%'";
					$payno = pdo_fetchcolumn($sql,array(':weid'=>$_W['uniacid']));
					$payno = createnum(substr($payno,strlen($paynopre),14));
					$payno = $paynopre.$payno;
					$moduleid = pdo_fetchcolumn("SELECT mid FROM ".tablename('modules')." WHERE name = :name", array(':name' => 'rhinfo_zyxq'));
					$moduleid = empty($moduleid) ? '000000' : sprintf("%06d", $moduleid);
					$uniontid = date('YmdHis').$moduleid.random(8,1);
					
					$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
					$property = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':pid'=>$park['pid']));
					$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
					$region = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':rid'=>$park['rid']));
					
					$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_car_iolog') . ' WHERE weid=:weid and io=1 and status=1 and carno=:carno and parklotid=:parkid order by intime desc';
					$car_inlog = pdo_fetch($sql,array(':weid'=>$_W['uniacid'],':carno'=>strtoupper($post['key1value']),':parkid'=>$park['id']));
					
					$myps = array();				
					$myps['module'] =  "rhinfo_zyxq";
					$myps['ordersn'] =  $payno;
					$myps['title'] = strtoupper($post['key1value']);	
					$myps['payuser'] = !empty($mycar['uid'])?$mycar['uid']:'0';	
					$myps['feetype'] = 15;
					$myps['openid'] = $openid;	
					$myps['pid'] = $park['pid'];
					$myps['rid'] = $park['rid'];
					$myps['parkid'] = $park['id'];
					$myps['recordid'] = strtoupper($post['key2value']);
					$myps['intime'] = $car_inlog['intime'];
					$myps['carno'] = strtoupper($post['key1value']);
					
					$log = array(
						'uniacid' => $_W['uniacid'],
						'acid' => $_W['acid'],
						'uniontid' => $uniontid,
						'type'=> 'wechat',
						'openid' => $openid,
						'module' => "rhinfo_zyxq",
						'tid' => $payno.random(8,1),
						'fee' => $post['payfee']/100,
						'card_fee' => $post['payfee']/100,
						'status' => 0,
						'is_usecard' => 0,
						'card_id' => 0,
						'uid' => $myps['payuser'],
						'pid' => $park['pid'],
						'rid' => $park['rid'],
						'feetype' => $myps['feetype'],
						'tag' => serialize($myps),
						'paytime'=> TIMESTAMP
					);
					if(substr($post['authcode'], 0, 2) == '28'){
						$log['type'] = 'alipay';
					}
					else{
						$log['type'] = 'wechat';
					}
					pdo_insert('rhinfo_zyxq_wepay_log', $log);
					$logid  = pdo_insertid();
					
					$params = array('title' =>$myps['title'], 'tid' =>$log['tid'], 'fee' => $log['fee']);
					$params['out_trade_no'] = $params['tid'];
					$params['total_amount'] = $params['fee'];
					$params['subject'] = $params['title'];
					$params['body'] = $_W['uniacid'] . ':2';	
					$params['logid'] = $logid;
					$params['auth_code'] = $post['authcode'];
					$params['clientip'] = $_W['clientip'];
					$params['uniacid'] = $_W['uniacid'];
					$params['cfrom'] = 'carscan';
					$res = $site->$method($params,$property,$region);
					if($res['errno']==1){
						mylogging('carscan_error', var_export($res, true));
						exit('fail');	 
					}
					else{
						pdo_update("rhinfo_zyxq_wepay_log",array('status'=>1),array('uniacid'=>$_W['uniacid'],'plid'=>$logid));
						$result_method = 'payResult';
						if (method_exists($site, $result_method)){							
							$ret = array();
							$ret['uniacid'] = $_W['uniacid'];
							$ret['acid'] = $_W['acid'];
							$ret['result'] = 'success';
							$ret['type'] = $log['type'];
							$ret['from'] = 'notify';
							$ret['tid'] = $log['tid'];
							$ret['uniontid'] = $log['uniontid'];
							$ret['payfrom'] = 'obeyscan';
							$site->$result_method($ret);
						}
					}
				}
			}
		}
		if($isnotice && !empty($openid)){			
			load()->classs('weixin.account');
			load()->func('communication');
			$obj = new WeiXinAccount();
			$access_token = $obj->fetch_available_token();		
			$data = array(
					'touser' => $openid,
					'template_id' => $sysset['tplid1'],
					'url' => $directurl,
					'topcolor' => $topcolor,
					'data' => $postdata,
				);
			$json = json_encode($data);
			$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
			ihttp_post($url,$json);	
		}
	}		
	exit('success');
}
exit('fail');
?>
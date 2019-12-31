<?php  if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
global $_W;
global $_GPC;
$operation=(!empty($_GPC['op'])?$_GPC['op']:'list');
$category=(!empty($_GPC['category'])?$_GPC['category']:1);
$this->my_check_web();
$mywe=$this->mywe;
$navtitle='房屋管理';
$mydo='room';
$tablename='rhinfo_zyxq_room';
$condition=' weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid = :tid';
$pindex=max(1,intval($_GPC['page']));
$psize=150;
$limit=' LIMIT '.(($pindex-1)*$psize).','.$psize;
$pid=$_GPC['pid'];
$rid=$_GPC['rid'];
$bid=$_GPC['bid'];
$tid=$_GPC['tid'];
$params=array(':weid'=>$mywe['weid'],':pid'=>$pid,':rid'=>$rid,':bid'=>$bid,':tid'=>$tid);
$rights=$this->myrights(2,'room','list');
$sql='select * from '.tablename('rhinfo_zyxq_region').' where weid = :weid and pid = :pid and id = :rid';
$region=pdo_fetch($sql,array(':weid'=>$mywe['weid'],':pid'=>$pid,':rid'=>$rid));
$sql='select title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
$building=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$pid,':rid'=>$rid,':bid'=>$bid));
$sql='select title from '.tablename('rhinfo_zyxq_unit').' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id = :tid';
$unit=pdo_fetchcolumn($sql,$params);
$navtitle=$region['title'].' > '.$building.' > '.$unit;
if ($operation=='list') 
{
	$current='房屋列表';
	$sql='SELECT COUNT(*) FROM '.tablename($tablename).' where '.$condition;
	$total=pdo_fetchcolumn($sql,$params);
	if ($total>0) 
	{
		$sql='select * from '.tablename('rhinfo_zyxq_unit').' where '.$condition.' ORDER BY `ID` ASC '.$limit;
		$data=pdo_fetchall($sql,$params);
		$pager=pagination($total,$pindex,$psize);
		$k=0;
		while ($k<count($data)) 
		{
			$condition=$condition.' and tid = :tid';
			$params[':tid']=$data[$k]['id'];
			if (!empty($region['roomfix'])) 
			{
				$sql='select id,pid,rid,bid,tid,floor,concat(title,"'.$region['roomfix'].'") as title from '.tablename($tablename).' where '.$condition.' ORDER BY title ASC ';
			}
			else 
			{
				$sql='select * from '.tablename($tablename).' where '.$condition.' ORDER BY title*1 ASC ';
			}
			$data[$k]['rooms']=pdo_fetchall($sql,$params);
			$k=$k+1;
		}
	}
	include($this->mywtpl('list'));
}
else 
{
	if ($operation=='batchadd') 
	{
		$current='批量新增房屋';
		if ($_W['ispost']) 
		{
			$startnum=intval($_GPC['startnum']);
			$endnum=intval($_GPC['endnum']);
			$floors=intval($_GPC['floors']);
			$digit=strlen($_GPC['endnum']);
			if ($startnum>=$endnum) 
			{
				$k=1;
				while ($k<=$floors) 
				{
					$title=$k.sprintf('%0'.$digit.'d',$startnum);
					$sql='SELECT count(*) FROM '.tablename($tablename).' WHERE weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid = :tid and title = :title';
					$count=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$pid,':rid'=>$rid,':bid'=>$bid,':tid'=>$tid,':title'=>$title));
					if ($count<=0) 
					{
						$data=array('weid'=>$mywe['weid'],'pid'=>$pid,'rid'=>$rid,'bid'=>$bid,'tid'=>$tid,'floor'=>$k,'title'=>$title,'remark'=>$_GPC['remark'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
						pdo_insert($tablename,$data);
						$id=pdo_insertid();
						$this->mysyslog($pid,$mydo,$operation,$current,$current.'id='.$id);
					}
					$k=$k+1;
				}
			}
			else 
			{
				$k=1;
				while ($k<=$floors) 
				{
					$n=$startnum;
					while ($n<=$endnum) 
					{
						$title=$k.sprintf('%0'.$digit.'d',$n);
						$sql='SELECT count(*) FROM '.tablename($tablename).' WHERE weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid = :tid and title = :title';
						$count=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$pid,':rid'=>$rid,':bid'=>$bid,':tid'=>$tid,':title'=>$title));
						if ($count<=0) 
						{
							$data=array('weid'=>$mywe['weid'],'pid'=>$pid,'rid'=>$rid,'bid'=>$bid,'tid'=>$tid,'floor'=>$k,'title'=>$title,'remark'=>$_GPC['remark'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
							pdo_insert($tablename,$data);
							$id=pdo_insertid();
							$this->mysyslog($pid,$mydo,$operation,$current,$current.'id='.$id);
						}
						$n=$n+1;
					}
					$k=$k+1;
				}
			}
			header('Location:'.$this->createWeburl('unit',array('category'=>$category,'op'=>'list','pid'=>$pid,'rid'=>$rid,'bid'=>$bid)).$mywe['direct']);
			exit(0);
		}
		include($this->mywtpl('batchadd'));
	}
	else 
	{
		if ($operation=='add') 
		{
			$current='新增房屋';
			if ($_W['ispost']) 
			{
				$data=array('weid'=>$mywe['weid'],'pid'=>$pid,'rid'=>$rid,'bid'=>$bid,'tid'=>$tid,'floor'=>$_GPC['floor'],'title'=>$_GPC['title'],'buildarea'=>$_GPC['buildarea'],'usearea'=>$_GPC['usearea'],'addarea'=>$_GPC['addarea'],'ownername'=>$_GPC['ownername'],'mobile'=>$_GPC['mobile'],'mobile1'=>$_GPC['mobile1'],'paydate'=>strtotime($_GPC['paydate']),'billdate'=>strtotime($_GPC['billdate']),'isfree'=>$_GPC['isfree'],'freestart'=>strtotime($_GPC['freestart']),'freeend'=>strtotime($_GPC['freeend']),'isdiscount'=>$_GPC['isdiscount'],'watermeter'=>$_GPC['watermeter'],'electmeter'=>$_GPC['electmeter'],'gasmeter'=>$_GPC['gasmeter'],'remark'=>$_GPC['remark'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
				pdo_insert($tablename,$data);
				$id=pdo_insertid();
				$this->mysyslog($pid,$mydo,$operation,$current,$current.'id='.$id);
				header('Location:'.$this->createWeburl('unit',array('category'=>$category,'op'=>'list','pid'=>$pid,'rid'=>$rid,'bid'=>$bid,'tid'=>$tid)).$mywe['direct']);
				exit(0);
			}
			include($this->mywtpl('post'));
		}
		else 
		{
			if ($operation=='edit') 
			{
				$current='编辑房屋';
				$id=intval($_GPC['id']);
				if ($_W['ispost']) 
				{
					$data=array('floor'=>$_GPC['floor'],'title'=>$_GPC['title'],'buildarea'=>$_GPC['buildarea'],'usearea'=>$_GPC['usearea'],'addarea'=>$_GPC['addarea'],'ownername'=>$_GPC['ownername'],'mobile'=>$_GPC['mobile'],'mobile1'=>$_GPC['mobile1'],'paydate'=>strtotime($_GPC['paydate']),'billdate'=>strtotime($_GPC['billdate']),'isfree'=>$_GPC['isfree'],'freestart'=>strtotime($_GPC['freestart']),'freeend'=>strtotime($_GPC['freeend']),'isdiscount'=>$_GPC['isdiscount'],'watermeter'=>$_GPC['watermeter'],'electmeter'=>$_GPC['electmeter'],'gasmeter'=>$_GPC['gasmeter'],'remark'=>$_GPC['remark']);
					$glue='AND';
					$result=pdo_update($tablename,$data,array('id'=>$id,'weid'=>$mywe['weid']),$glue);
					$this->mysyslog($pid,$mydo,$operation,$current,$current.'id='.$id);
					header('Location:'.$this->createWeburl('unit',array('category'=>$category,'op'=>'list','pid'=>$pid,'rid'=>$rid,'bid'=>$bid,'tid'=>$tid)).$mywe['direct']);
					exit(0);
				}
				$sql='select * from '.tablename($tablename).' where id = :id and weid = :weid';
				$item=pdo_fetch($sql,array(':id'=>$id,':weid'=>$mywe['weid']));
				include($this->mywtpl('post'));
			}
			else 
			{
				if ($operation=='delete') 
				{
					$current='删除房屋';
					$id=intval($_GPC['id']);
					$sql='select * from '.tablename('rhinfo_zyxq_room').' where weid=:weid and id=:hid';
					$room=pdo_fetch($sql,array(':weid'=>$mywe['weid'],':hid'=>$id));
					$sql='select count(*) from '.tablename('rhinfo_zyxq_feebill').' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ';
					$count=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$room['rid'],':bid'=>$room['bid'],':tid'=>$room['tid'],':hid'=>$id));
					if ($count>0) 
					{
						echo '账单已存在!';
					}
					else 
					{
						$glue='AND';
						$result=pdo_delete('rhinfo_zyxq_room_mp',array('hid'=>$id,'weid'=>$mywe['weid']),$glue);
						$glue='AND';
						$result=pdo_delete($tablename,array('id'=>$id,'weid'=>$mywe['weid']),$glue);
						if (!empty($result)) 
						{
							echo 'ok';
						}
						else 
						{
							echo '删除失败!';
						}
					}
					$this->mysyslog($pid,$mydo,$operation,$current,$current.'id='.$id);
					exit(0);
				}
				else 
				{
					if ($operation=='check') 
					{
						if ($_W['isajax']) 
						{
							if ($_GPC['post']=='add') 
							{
								$sql='SELECT count(*) FROM '.tablename($tablename).' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and bid = :bid and tid = :tid ';
								$count=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':title'=>$_GPC['title'],':pid'=>$pid,':rid'=>$rid,':bid'=>$bid,':tid'=>$tid));
							}
							else 
							{
								$sql='SELECT count(*) FROM '.tablename($tablename).' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and bid = :bid and tid = :tid and id <> :id';
								$count=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':title'=>$_GPC['title'],':pid'=>$pid,':rid'=>$rid,':bid'=>$bid,':tid'=>$tid,':id'=>$_GPC['id']));
							}
							if ($count>0) 
							{
								echo '房屋已存在!';
							}
							else 
							{
								echo 'ok';
							}
							exit(0);
						}
					}
					else 
					{
						if ($operation=='info') 
						{
							$cate=$_GPC['cate'];
							if ($_W['ispost']) 
							{
								$condition='weid = :weid and rid = :rid ';
								if (!empty($_GPC['bid'])) 
								{
									$condition .=' and bid='.$_GPC['bid'];
								}
								if (!empty($_GPC['tid'])) 
								{
									$condition .=' and tid='.$_GPC['tid'];
									$select_buildings=pdo_getall('rhinfo_zyxq_building',array('weid'=>$mywe['weid'],'rid'=>$_GPC['rid']),array(0=>'id',1=>'title'));
								}
								if (!empty($_GPC['keyword'])) 
								{
									if (intval($_GPC['keyword'])>10000000000) 
									{
										$condition .=' and (mobile LIKE \'%'.$_GPC['keyword'].'%\' or mobile1 LIKE \'%'.$_GPC['keyword'].'%\')';
									}
									else 
									{
										$condition .=' and (title LIKE \''.$_GPC['keyword'].'%\' or ownername LIKE \'%'.$_GPC['keyword'].'%\')';
									}
								}
								$sql='select * from '.tablename($tablename).' where '.$condition.' limit 1';
								$item=pdo_fetch($sql,array(':weid'=>$mywe['weid'],':rid'=>$rid));
								$sql='select * from '.tablename('rhinfo_zyxq_region').' where weid = :weid and id = :rid';
								$region=pdo_fetch($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid']));
								$sql='select title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid and id = :bid';
								$building=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid']));
								$sql='select title from '.tablename('rhinfo_zyxq_unit').' where id = :id and weid = :weid and rid=:rid';
								$unit=pdo_fetchcolumn($sql,array(':id'=>$item['tid'],':weid'=>$mywe['weid'],':rid'=>$item['rid']));
							}
							else 
							{
								$hid=intval($_GPC['hid']);
								$sql='select * from '.tablename($tablename).' where id = :id and weid = :weid';
								$item=pdo_fetch($sql,array(':id'=>$hid,':weid'=>$mywe['weid']));
								$sql='select * from '.tablename('rhinfo_zyxq_region').' where weid = :weid and id = :rid';
								$region=pdo_fetch($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid']));
							}
							if (!empty($_GPC['rid'])) 
							{
								$select_buildings=pdo_getall('rhinfo_zyxq_building',array('weid'=>$mywe['weid'],'rid'=>$_GPC['rid']),array(0=>'id',1=>'title'));
							}
							if (!empty($_GPC['bid'])) 
							{
								$select_units=pdo_getall('rhinfo_zyxq_unit',array('weid'=>$mywe['weid'],'rid'=>$_GPC['rid'],'bid'=>$_GPC['bid']),array(0=>'id',1=>'title'));
							}
							if ($item['isfree']==1) 
							{
								$item['statustxt']='免费';
							}
							else 
							{
								if ($item['isfree']==2) 
								{
									$item['statustxt']='空置';
								}
								else 
								{
									if ($item['isfree']==3) 
									{
										$item['statustxt']='异常';
									}
									else 
									{
										$item['statustxt']='收费';
									}
								}
							}
							$paytype=$this->paytype;
							$pindex=max(1,intval($_GPC['page']));
							$psize=(empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize']);
							$psize=100;
							$limit=' LIMIT '.(($pindex-1)*$psize).','.$psize;
							$sql='select * from '.tablename('rhinfo_zyxq_member').' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and isowner=1';
							$member=pdo_fetch($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							load()->model('mc');
							$fans=mc_fansinfo($member['uid']);
							$sql='select count(*) from '.tablename('rhinfo_zyxq_member').' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and category=1';
							$member_total=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$sql='SELECT sum(fee) FROM '.tablename('rhinfo_zyxq_feebill').' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and status=1 and category=1';
							$totalfee=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$totalfee=(empty($totalfee) ? 0 : $totalfee);
							$sql='select * from '.tablename('rhinfo_zyxq_room_mp').' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ';
							$room_mp=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$sql='select count(*) from '.tablename('rhinfo_zyxq_feebill').' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and status=1 and category=1';
							$total=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							if ($total>0) 
							{
								$sql='select * from '.tablename('rhinfo_zyxq_feebill').' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and status=1 and category=1 ORDER BY
						`ID` DESC '.$limit;
								$feebill=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
								$pager=pagination($total,$pindex,$psize);
							}
							$sql='select count(*) from '.tablename('rhinfo_zyxq_feebill').' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and status=2 and category=1';
							$paytotal=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$sql='select * from '.tablename('rhinfo_zyxq_feebill').' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and status=2 and category=1 ORDER BY
					`PAYDATE` DESC,`ID` DESC '.$limit;
							$payfeebill=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$k=0;
							while ($k<count($payfeebill)) 
							{
								$payfeebill[$k]['paytype']=$paytype[$payfeebill[$k]['paytype']];
								$k=$k+1;
							}
							$paypager=pagination($paytotal,$pindex,$psize);
							$sql='select * from '.tablename('rhinfo_zyxq_room_abnlog').' where weid=:weid and hid=:hid ORDER BY `ID` ASC ';
							$abnlog=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':hid'=>$item['id']));
							$sql='select * from '.tablename('rhinfo_zyxq_room_chglog').' where weid=:weid and hid=:hid ORDER BY `ID` ASC ';
							$chglog=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':hid'=>$item['id']));
							$room_rela=array();
							if (!empty($item['mobile'])) 
							{
								$sql='select * from '.tablename('rhinfo_zyxq_room').' where weid = :weid and rid=:rid and (mobile=:mobile or mobile1=:mobile) and id<>:hid';
								$room_rela=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':mobile'=>$item['mobile'],':hid'=>$item['id']));
								$k=0;
								while ($k<count($room_rela)) 
								{
									if ($room_rela[$k]['isfree']==1) 
									{
										$room_rela[$k]['statustxt']='免费';
									}
									else 
									{
										if ($room_rela[$k]['isfree']==2) 
										{
											$room_rela[$k]['statustxt']='空置';
										}
										else 
										{
											if ($room_rela[$k]['isfree']==3) 
											{
												$room_rela[$k]['statustxt']='异常';
											}
											else 
											{
												$room_rela[$k]['statustxt']='收费';
											}
										}
									}
									$sql='select title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid and id = :bid';
									$room_rela[$k]['building']=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':rid'=>$room_rela[$k]['rid'],':bid'=>$room_rela[$k]['bid']));
									$sql='select title from '.tablename('rhinfo_zyxq_unit').' where id = :id and weid = :weid and rid=:rid';
									$room_rela[$k]['unit']=pdo_fetchcolumn($sql,array(':id'=>$room_rela[$k]['tid'],':weid'=>$mywe['weid'],':rid'=>$room_rela[$k]['rid']));
									$k=$k+1;
								}
							}
							$sql='select * from '.tablename('rhinfo_zyxq_repair').' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ';
							$repair=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$k=0;
							while ($k<count($repair)) 
							{
								if ($repair[$k]['cid']==0) 
								{
									$repair[$k]['catename']='其他';
								}
								else 
								{
									$sql='SELECT title FROM '.tablename('rhinfo_zyxq_category').' where weid and :weid and id = :cid';
									$repair[$k]['catename']=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':cid'=>$repair[$k]['cid']));
								}
								$k=$k+1;
							}
							$sql='select * from '.tablename('rhinfo_zyxq_suggest').' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ';
							$suggest=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid'],':hid'=>$item['id']));
							$k=0;
							while ($k<count($suggest)) 
							{
								if ($suggest[$k]['cid']==0) 
								{
									$suggest[$k]['catename']='其他';
								}
								else 
								{
									$sql='SELECT title FROM '.tablename('rhinfo_zyxq_category').' where weid and :weid and id = :cid';
									$suggest[$k]['catename']=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':cid'=>$suggest[$k]['cid']));
								}
								$k=$k+1;
							}
							$parking=array();
							if (!empty($item['mobile'])) 
							{
								$sql='select *,0 as \'relaid\' from '.tablename('rhinfo_zyxq_parking').' where weid = :weid and rid=:rid and (mobile=:mobile or mobile1=:mobile)';
								$parking1=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':mobile'=>$item['mobile']));
							}
							$sql='select a.*,b.id as \'relaid\' from '.tablename('rhinfo_zyxq_parking').' as a left join '.tablename('rhinfo_zyxq_room_parking').' as b on a.id = b.parkingid where a.weid = :weid and a.rid=:rid and b.hid=:hid';
							$parking=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':hid'=>$item['id']));
							if (!empty($parking1)) 
							{
								$parking=array_merge($parking1,$parking);
							}
							$rcondition=$this->wyrcondition();
							$mybuilding=array();
							$sql='select id,title from '.tablename('rhinfo_zyxq_region').' where weid = :weid'.$rcondition;
							$regions=pdo_fetchall($sql,array(':weid'=>$mywe['weid']));
							$k=0;
							while ($k<count($regions)) 
							{
								$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
								$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$regions[$k]['id']));
								$mybuilding[$regions[$k]['id']]=$buildings;
								$n=0;
								while ($n<count($buildings)) 
								{
									$sql='select id,title from '.tablename('rhinfo_zyxq_unit').' where weid = :weid and rid = :rid and bid = :bid';
									$units=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$regions[$k]['id'],':bid'=>$buildings[$n]['id']));
									$myunit[$buildings[$n]['id']]=$units;
									$n=$n+1;
								}
								$k=$k+1;
							}
							$relafeebill_room=array();
							$relafeebill_shop=array();
							$relafeebill_garage=array();
							$relafeebill_parking=array();
							if (!empty($item['mobile'])) 
							{
								$sql='select f.* from '.tablename('rhinfo_zyxq_feebill').' as f left join '.tablename('rhinfo_zyxq_room').' as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and (r.mobile=:mobile or r.mobile1=:mobile) and f.status=1 and f.category=1 and r.id<>:hid ORDER BY
					f.ID ASC ';
								$relafeebill_room=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':mobile'=>$item['mobile'],':hid'=>$item['id']));
								$sql='select f.* from '.tablename('rhinfo_zyxq_feebill').' as f left join '.tablename('rhinfo_zyxq_shop').' as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and (r.mobile=:mobile or r.mobile1=:mobile) and f.status=1 and f.category=2 and r.id<>:hid ORDER BY
					f.ID ASC ';
								$relafeebill_shop=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':mobile'=>$item['mobile'],':hid'=>$item['id']));
								$sql='select f.* from '.tablename('rhinfo_zyxq_feebill').' as f left join '.tablename('rhinfo_zyxq_garage').' as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and (r.mobile=:mobile or r.mobile1=:mobile) and f.status=1 and f.category=3 and r.id<>:hid ORDER BY
					f.ID ASC ';
								$relafeebill_garage=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':mobile'=>$item['mobile'],':hid'=>$item['id']));
								$sql='select f.* from '.tablename('rhinfo_zyxq_feebill').' as f left join '.tablename('rhinfo_zyxq_parking').' as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and (r.mobile=:mobile or r.mobile1=:mobile) and f.status=1 and f.category=4 and r.id<>:hid ORDER BY
					f.ID ASC ';
								$relafeebill_parking=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':mobile'=>$item['mobile'],':hid'=>$item['id']));
							}
							$relafeebill=array_merge($relafeebill_room,$relafeebill_shop,$relafeebill_garage,$relafeebill_parking);
							$k=0;
							while ($k<count($relafeebill)) 
							{
								$relafeebill[$k]['paydate']=($relafeebill[$k]['paydate']?date('Y-m-d H:i',$relafeebill[$k]['paydate']):'');
								$relafeebill[$k]['paytype']=$paytype[$relafeebill[$k]['paytype']];
								$k=$k+1;
							}
							if (($region['lifepay_type']=='1' && !empty($item['lifepay_hid']))) 
							{
								$lifepays=array();
								$set=array();
								$set['app_id']=$this->syspub['alipay_appid'];
								$set['prikey']=$this->syspub['alipay_rsa2'];
								$set['app_auth_token']=$region['lifepay_token'];
								$set['method']='bill.batchquery';
								$params="{\\r\\n\\t\\t\\t\\t\"community_id\":\"".$region['lifepay_rid']."\",\\r\\n\\t\\t\\t\\t\"out_room_id\":\"".$item['lifepay_hid']."\",\\r\\n\\t\\t\\t\\t\"page_num\":1,\\r\\n\\t\\t\\t\\t\"page_size\":100\\r\\n\\t\\t\\t\\t  }";
								$res=my_alipay_life($set,$params);
								if (!is_error($res)) 
								{
									$res=json_decode($res,1);
									$res=$res['alipay_eco_cplife_bill_batchquery_response'];
									if ($res['code']!==10000) 
									{
										if (!empty($res['sub_code'])) 
										{
										}
										else 
										{
										}
									}
									else 
									{
										$lifepays=$res['bill_result_set'];
									}
								}
							}
							$rights=$this->myrights(3,'fee','list');
							$feebillitems=pdo_getall('rhinfo_zyxq_feebillitem',array('weid'=>$mywe['weid'],'rid'=>$item['rid'],'bid'=>$item['bid'],'hid'=>$item['id'],'category'=>1));
							$room_feeitems=pdo_getall('rhinfo_zyxq_room_feeitem',array('weid'=>$mywe['weid'],'hid'=>$item['id']));
							include($this->mywtpl('info'));
						}
						else 
						{
							if ($operation=='addcost') 
							{
								$current='新增收支记录';
								$category=(empty($_GPC['category']) ? 1 : $_GPC['category']);
								if ($category==3) 
								{
									$current='新增装修保证金';
								}
								if ($category==4) 
								{
									$current='新增预收款项';
								}
								if ($_W['isajax']) 
								{
									if (($_GPC['io']==1 || $_GPC['io']==2)) 
									{
										$sql='select title from '.tablename('rhinfo_zyxq_costitem').' where id = :id and weid = :weid';
										$title=pdo_fetchcolumn($sql,array(':id'=>$_GPC['itemid'],':weid'=>$mywe['weid']));
										$status=1;
										$itemid=$_GPC['itemid'];
									}
									else 
									{
										if ($_GPC['io']==3) 
										{
											$title='装修保证金';
											$status=1;
											$itemid=0;
											$cate=0;
										}
										else 
										{
											if ($_GPC['io']==4) 
											{
												$title='预收款项';
												$status=1;
												$itemid=0;
												$cate=$_GPC['cate'];
												if ($cate==1) 
												{
													$title='预收物业费';
												}
												else 
												{
													if ($cate==2) 
													{
														$title='预收电费';
													}
													else 
													{
														if ($cate==3) 
														{
															$title='预收水费';
														}
													}
												}
											}
										}
									}
									$data=array('weid'=>$mywe['weid'],'pid'=>$_GPC['pid'],'rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'tid'=>$_GPC['tid'],'hid'=>$_GPC['hid'],'io'=>$_GPC['io'],'cate'=>$cate,'title'=>$title,'itemid'=>$itemid,'money'=>$_GPC['money'],'handling'=>$_GPC['handling'],'handledate'=>strtotime($_GPC['handledate']),'remark'=>$_GPC['remark'],'publicity'=>$_GPC['publicity'],'status'=>$status,'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
									pdo_insert('rhinfo_zyxq_costdetail',$data);
									$id=pdo_insertid();
									$str=' + ';
									if ($_GPC['io']==3) 
									{
										$str=' set deposit = deposit '.$str.$_GPC['money'];
									}
									else 
									{
										if ($_GPC['io']==4) 
										{
											if ($cate==1) 
											{
												$str=' set prepayment = prepayment '.$str.$_GPC['money'];
											}
											else 
											{
												if ($cate==2) 
												{
													$str=' set preelectric = preelectric '.$str.$_GPC['money'];
												}
												else 
												{
													if ($cate==3) 
													{
														$str=' set prewater = prewater '.$str.$_GPC['money'];
													}
												}
											}
										}
									}
									$sql='update '.tablename('rhinfo_zyxq_room').$str.' where weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
									pdo_query($sql,array(':weid'=>$mywe['weid'],':pid'=>$_GPC['pid'],':rid'=>$_GPC['rid'],':bid'=>$_GPC['bid'],':tid'=>$_GPC['tid'],':hid'=>$_GPC['hid']));
									$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
									echo $id;
									exit(0);
								}
								$rcondition=$this->wyrcondition();
								$myproperty=$this->myproperty();
								$myregion=array();
								$myitem=array();
								$mybuilding=array();
								$myunit=array();
								$myroom=array();
								$k=0;
								while ($k<count($myproperty)) 
								{
									$sql='select id,title from '.tablename('rhinfo_zyxq_region').' where weid = :weid and pid = :pid '.$rcondition;
									$regions=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$myproperty[$k]['id']));
									$myregion[$myproperty[$k]['id']]=$regions;
									$m=0;
									while ($m<count($regions)) 
									{
										if ($category==1) 
										{
											$sql='select id,title from '.tablename('rhinfo_zyxq_costitem').' where status = 1 and io=1 and weid=:weid and pid=:pid and rid=:rid';
											$items=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$myproperty[$k]['id'],':rid'=>$regions[$m]['id']));
											$myitem[$regions[$m]['id']]=$items;
										}
										else 
										{
											if ($category==2) 
											{
												$sql='select id,title from '.tablename('rhinfo_zyxq_costitem').' where status = 1 and io=2 and weid=:weid and pid=:pid and rid=:rid';
												$items=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$myproperty[$k]['id'],':rid'=>$regions[$m]['id']));
												$myitem[$regions[$m]['id']]=$items;
											}
										}
										$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and pid = :pid and rid = :rid ';
										$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$myproperty[$k]['id'],':rid'=>$regions[$m]['id']));
										$mybuilding[$regions[$m]['id']]=$buildings;
										$n=0;
										while ($n<count($buildings)) 
										{
											$sql='select id,title from '.tablename('rhinfo_zyxq_unit').' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
											$units=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$myproperty[$k]['id'],':rid'=>$regions[$m]['id'],':bid'=>$buildings[$n]['id']));
											$myunit[$buildings[$n]['id']]=$units;
											$j=0;
											while ($j<count($units)) 
											{
												$sql='select id,title from '.tablename('rhinfo_zyxq_room').' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid';
												$rooms=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$myproperty[$k]['id'],':rid'=>$regions[$m]['id'],':bid'=>$buildings[$n]['id'],':tid'=>$units[$j]['id']));
												$myroom[$units[$j]['id']]=$rooms;
												$j=$j+1;
											}
											$n=$n+1;
										}
										$m=$m+1;
									}
									$k=$k+1;
								}
								$sql='select * from '.tablename('rhinfo_zyxq_room').' where id = :id and weid = :weid';
								$item=pdo_fetch($sql,array(':id'=>$_GPC['hid'],':weid'=>$mywe['weid']));
								$sql='select id,title from '.tablename('rhinfo_zyxq_region').' where weid = :weid and pid = :pid';
								$eregions=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid']));
								$sql='select id,title from '.tablename('rhinfo_zyxq_costitem').' where weid = :weid and pid = :pid and rid = :rid and status = 1 and io=:io';
								$eitems=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':io'=>$item['io']));
								$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and pid = :pid and rid = :rid';
								$ebuildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid']));
								$sql='select id,title from '.tablename('rhinfo_zyxq_unit').' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
								$eunits=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':bid'=>$item['bid']));
								$sql='select id,title from '.tablename('rhinfo_zyxq_room').' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid';
								$erooms=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':pid'=>$item['pid'],':rid'=>$item['rid'],':bid'=>$item['bid'],':tid'=>$item['tid']));
								include($this->mywtpl('postcost'));
							}
							else 
							{
								if ($operation=='billdate') 
								{
									if (!empty($_GPC['enterdate'])) 
									{
										if ($_GPC['cate']==1) 
										{
											$res=pdo_update('rhinfo_zyxq_feebillitem',array('paydate'=>strtotime($_GPC['enterdate'])),array('weid'=>$mywe['weid'],'id'=>$_GPC['id']));
										}
										else 
										{
											$res=pdo_update('rhinfo_zyxq_feebillitem',array('billdate'=>strtotime($_GPC['enterdate'])),array('weid'=>$mywe['weid'],'id'=>$_GPC['id']));
										}
										if ($res) 
										{
											exit('ok');
										}
										else 
										{
											exit('修改失败');
										}
									}
									exit('非法操作');
								}
								else 
								{
									if ($operation=='delfeeitem') 
									{
										$current='删除账单收费项目';
										$id=intval($_GPC['id']);
										$result=pdo_delete('rhinfo_zyxq_feebillitem',array('id'=>$id,'weid'=>$mywe['weid']));
										if (!empty($result)) 
										{
											echo 'ok';
										}
										else 
										{
											echo '删除失败!';
										}
										$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
										exit(0);
									}
									else 
									{
										if ($operation=='delcustom') 
										{
											$current='删除自定收费';
											$id=intval($_GPC['id']);
											$result=pdo_delete('rhinfo_zyxq_room_feeitem',array('id'=>$id,'weid'=>$mywe['weid']));
											if (!empty($result)) 
											{
												echo 'ok';
											}
											else 
											{
												echo '删除失败!';
											}
											$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
											exit(0);
										}
										else 
										{
											if ($operation=='addcustom') 
											{
												$current='自定收费';
												if ($_W['isajax']) 
												{
													$data=array('weid'=>$mywe['weid'],'pid'=>$_GPC['pid'],'rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'tid'=>$_GPC['tid'],'hid'=>$_GPC['hid'],'title'=>$_GPC['title'],'calmethod'=>$_GPC['calmethod'],'qty'=>$_GPC['qty'],'measure'=>$_GPC['measure'],'price'=>$_GPC['price'],'paymonths'=>$_GPC['paymonths'],'billdate'=>strtotime($_GPC['billdate']),'remark'=>$_GPC['remark'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
													pdo_insert('rhinfo_zyxq_room_feeitem',$data);
													$id=pdo_insertid();
													$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
													exit($id);
												}
												$sql='select * from '.tablename('rhinfo_zyxq_room').' where id = :id and weid = :weid';
												$item=pdo_fetch($sql,array(':id'=>$_GPC['hid'],':weid'=>$mywe['weid']));
												include($this->mywtpl());
											}
											else 
											{
												if ($operation=='editcustom') 
												{
													$current='自定收费';
													if ($_W['isajax']) 
													{
														$data=array('title'=>$_GPC['title'],'calmethod'=>$_GPC['calmethod'],'qty'=>$_GPC['qty'],'measure'=>$_GPC['measure'],'price'=>$_GPC['price'],'paymonths'=>$_GPC['paymonths'],'billdate'=>strtotime($_GPC['billdate']),'remark'=>$_GPC['remark']);
														pdo_update('rhinfo_zyxq_room_feeitem',$data,array('weid'=>$mywe['weid'],'id'=>$_GPC['id']));
														$id=pdo_insertid();
														$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
														exit($id);
													}
													$sql='select * from '.tablename('rhinfo_zyxq_room_feeitem').' where id = :id and weid = :weid';
													$custom_item=pdo_fetch($sql,array(':id'=>$_GPC['id'],':weid'=>$mywe['weid']));
													$sql='select * from '.tablename('rhinfo_zyxq_room').' where id = :id and weid = :weid';
													$item=pdo_fetch($sql,array(':id'=>$custom_item['hid'],':weid'=>$mywe['weid']));
													include($this->mywtpl());
												}
												else 
												{
													if ($operation=='delparking') 
													{
														$current='删除关联车位';
														$id=intval($_GPC['id']);
														$result=pdo_delete('rhinfo_zyxq_room_parking',array('id'=>$id,'weid'=>$mywe['weid']));
														if (!empty($result)) 
														{
															echo 'ok';
														}
														else 
														{
															echo '删除失败!';
														}
														$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
														exit(0);
													}
													else 
													{
														if ($operation=='addparking') 
														{
															$current='关联车位';
															if ($_W['isajax']) 
															{
																$data=array('weid'=>$mywe['weid'],'pid'=>$_GPC['pid'],'rid'=>$_GPC['rid'],'hid'=>$_GPC['hid'],'parkingid'=>$_GPC['parkingid'],'remark'=>$_GPC['remark'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
																pdo_insert('rhinfo_zyxq_room_parking',$data);
																$id=pdo_insertid();
																$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
																exit($id);
															}
															$sql='select * from '.tablename('rhinfo_zyxq_room').' where id = :id and weid = :weid';
															$item=pdo_fetch($sql,array(':id'=>$_GPC['hid'],':weid'=>$mywe['weid']));
															$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid=:weid and rid=:rid and category = 2 ORDER BY title ASC ';
															$locations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid']));
															$parkings=array();
															$k=0;
															while ($k<count($locations)) 
															{
																$sql='select id,title,ownername from '.tablename('rhinfo_zyxq_parking').' where weid = :weid and rid = :rid and lid=:lid';
																$parkings[$locations[$k]['id']]=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':lid'=>$locations[$k]['id']));
																$k=$k+1;
															}
															include($this->mywtpl());
														}
														else 
														{
															if ($operation=='editparking') 
															{
																$current='关联车位';
																if ($_W['isajax']) 
																{
																	$data=array('parkingid'=>$_GPC['parkingid'],'remark'=>$_GPC['remark']);
																	pdo_update('rhinfo_zyxq_room_parking',$data,array('weid'=>$mywe['weid'],'id'=>$_GPC['id']));
																	$id=pdo_insertid();
																	$this->mysyslog($mywe['pid'],$mydo,$operation,$current,$current.'id='.$id);
																	exit($id);
																}
																$sql='select * from '.tablename('rhinfo_zyxq_room_parking').' where id = :id and weid = :weid';
																$room_parking=pdo_fetch($sql,array(':id'=>$_GPC['id'],':weid'=>$mywe['weid']));
																$sql='select * from '.tablename('rhinfo_zyxq_parking').' where id = :id and weid = :weid';
																$parking=pdo_fetch($sql,array(':id'=>$room_parking['parkingid'],':weid'=>$mywe['weid']));
																$sql='select * from '.tablename('rhinfo_zyxq_room').' where id = :id and weid = :weid';
																$item=pdo_fetch($sql,array(':id'=>$room_parking['hid'],':weid'=>$mywe['weid']));
																$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid=:weid and rid=:rid and category = 2 ORDER BY title ASC ';
																$locations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid']));
																$parkings=array();
																$k=0;
																while ($k<count($locations)) 
																{
																	$sql='select id,title,ownername from '.tablename('rhinfo_zyxq_parking').' where weid = :weid and rid = :rid and lid=:lid';
																	$parkings[$locations[$k]['id']]=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':lid'=>$locations[$k]['id']));
																	$k=$k+1;
																}
																$sql='select id,title,ownername from '.tablename('rhinfo_zyxq_parking').' where weid = :weid and rid = :rid and lid=:lid';
																$eparkings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$item['rid'],':lid'=>$parking['lid']));
																include($this->mywtpl());
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
?>
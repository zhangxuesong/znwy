<?php
//加密方式：mfenc加密，代码还原率99%。
//此程序由【PHPJM.CC|PHP解密在线】http://www.phpjm.cc (VIP会员功能）在线逆向还原，QQ：1187328898 
?>
<?php  if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
date_default_timezone_set('Asia/Shanghai');
global $_W;
global $_GPC;
$operation=(!empty($_GPC['op'])?$_GPC['op']:'index');
$this->my_check_web();
$mywe=$this->mywe;
$mydo='guide';
$condition=' weid = :weid ';
$params=array(':weid'=>$mywe['weid']);
$pindex=max(1,intval($_GPC['page']));
$psize=20;
$limit=' LIMIT '.(($pindex-1)*$psize).','.$psize;
$navtitle='向导操作';
$calmethod=$this->calmethod;
$region=pdo_get('rhinfo_zyxq_region',array('weid'=>$mywe['weid'],'id'=>$_GPC['rid']));
if ($operation=='index') 
{
	$navs=array();
	$rights=$this->myrights(2,'category','list');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'1、基础设置','level'=>0,'category'=>'base');
	}
	else 
	{
		$navs[]=array('text'=>'1、基础设置','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(2,'room','list');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'2、房屋信息','level'=>0,'category'=>'room');
	}
	else 
	{
		$navs[]=array('text'=>'2、房屋信息','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(2,'shop','list');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'3、商铺信息','level'=>0,'category'=>'shop');
	}
	else 
	{
		$navs[]=array('text'=>'3、商铺信息','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(2,'parking','list');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'4、车位信息','level'=>0,'category'=>'parking');
	}
	else 
	{
		$navs[]=array('text'=>'4、车位信息','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(2,'building','list');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'5、储物间','level'=>0,'category'=>'garage');
	}
	else 
	{
		$navs[]=array('text'=>'5、储物间','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(3,'fee','item');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'6、收费项目','level'=>0,'category'=>'feeitem');
	}
	else 
	{
		$navs[]=array('text'=>'6、收费项目','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(3,'fee','bill');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'7、物业账单','level'=>0,'category'=>'feebill');
	}
	else 
	{
		$navs[]=array('text'=>'7、物业账单','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(3,'fee','three');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'8、抄表账单','level'=>0,'category'=>'threebill');
	}
	else 
	{
		$navs[]=array('text'=>'8、抄表账单','level'=>0,'category'=>'nogrant');
	}
	$rights=$this->myrights(3,'fee','bill');
	if (!empty($rights)) 
	{
		$navs[]=array('text'=>'9、临时账单','level'=>0,'category'=>'tempbill');
	}
	else 
	{
		$navs[]=array('text'=>'9、临时账单','level'=>0,'category'=>'nogrant');
	}
	$navs[]=array('text'=>'10、视频教程','level'=>0,'category'=>'manual');
	include($this->mywtpl());
}
else 
{
	if ($operation=='guide') 
	{
		include($this->mywtpl());
	}
	else 
	{
		if ($operation=='grant') 
		{
			if ($_W['isajax']) 
			{
				if ($_GPC['myop']=='region') 
				{
					$rights=$this->myrights(2,$_GPC['myop'],'list');
					if (!empty($rights)) 
					{
						exit('ok');
					}
					else 
					{
						exit('抱歉，您还没有权限');
					}
				}
				else 
				{
					if ($_GPC['myop']=='building' || $_GPC['myop']=='room' || $_GPC['myop']=='shop' || $_GPC['myop']=='parking') 
					{
						$rights=$this->myrights(2,$_GPC['myop'],'list');
						if (!empty($rights)) 
						{
							exit('ok');
						}
						else 
						{
							exit('抱歉，您还没有权限');
						}
					}
					else 
					{
						if ($_GPC['myop']=='category') 
						{
							$rights=$this->myrights(2,$_GPC['myop'],'list');
							if (!empty($rights)) 
							{
								exit('ok');
							}
							else 
							{
								exit('抱歉，您还没有权限');
							}
						}
						else 
						{
							if ($_GPC['myop']=='feeitem') 
							{
								$rights=$this->myrights(3,'fee','item');
								if (!empty($rights)) 
								{
									exit('ok');
								}
								else 
								{
									exit('抱歉，您还没有权限');
								}
							}
							else 
							{
								if ($_GPC['myop']=='feebill') 
								{
									$rights=$this->myrights(3,'fee','bill');
									if (!empty($rights)) 
									{
										exit('ok');
									}
									else 
									{
										exit('抱歉，您还没有权限');
									}
								}
								else 
								{
									if ($_GPC['myop']=='feethree') 
									{
										$rights=$this->myrights(3,'fee','three');
										if (!empty($rights)) 
										{
											exit('ok');
										}
										else 
										{
											exit('抱歉，您还没有权限');
										}
									}
									else 
									{
										if ($_GPC['myop']=='feetask') 
										{
											$rights=$this->myrights(3,'feecal','list');
											if (!empty($rights)) 
											{
												exit('ok');
											}
											else 
											{
												exit('抱歉，您还没有权限');
											}
										}
										else 
										{
											if ($_GPC['myop']=='feepay') 
											{
												$rights=$this->myrights(3,'fee','list');
												if (!empty($rights)) 
												{
													exit('ok');
												}
												else 
												{
													exit('抱歉，您还没有权限');
												}
											}
											else 
											{
												if ($_GPC['myop']=='paybill') 
												{
													$rights=$this->myrights(3,'feecalb','paybill');
													if (!empty($rights)) 
													{
														exit('ok');
													}
													else 
													{
														exit('抱歉，您还没有权限');
													}
												}
												else 
												{
													if ($_GPC['myop']=='articlecate') 
													{
														$rights=$this->myrights(12,'article','category');
														if (!empty($rights)) 
														{
															exit('ok');
														}
														else 
														{
															exit('抱歉，您还没有权限');
														}
													}
													else 
													{
														if ($_GPC['myop']=='articlelist') 
														{
															$rights=$this->myrights(12,'article','list');
															if (!empty($rights)) 
															{
																exit('ok');
															}
															else 
															{
																exit('抱歉，您还没有权限');
															}
														}
														else 
														{
															if ($_GPC['myop']=='activity') 
															{
																$rights=$this->myrights(12,'activity','list');
																if (!empty($rights)) 
																{
																	exit('ok');
																}
																else 
																{
																	exit('抱歉，您还没有权限');
																}
															}
															else 
															{
																if ($_GPC['myop']=='team') 
																{
																	$rights=$this->myrights(5,'team','list');
																	if (!empty($rights)) 
																	{
																		exit('ok');
																	}
																	else 
																	{
																		exit('抱歉，您还没有权限');
																	}
																}
																else 
																{
																	if ($_GPC['myop']=='notify') 
																	{
																		$rights=$this->myrights(5,'notify','list');
																		if (!empty($rights)) 
																		{
																			exit('ok');
																		}
																		else 
																		{
																			exit('抱歉，您还没有权限');
																		}
																	}
																	else 
																	{
																		if ($_GPC['myop']=='repair') 
																		{
																			$rights=$this->myrights(5,'repair','list');
																			if (!empty($rights)) 
																			{
																				exit('ok');
																			}
																			else 
																			{
																				exit('抱歉，您还没有权限');
																			}
																		}
																		else 
																		{
																			if ($_GPC['myop']=='repairp') 
																			{
																				$rights=$this->myrights(5,'repairp','list');
																				if (!empty($rights)) 
																				{
																					exit('ok');
																				}
																				else 
																				{
																					exit('抱歉，您还没有权限');
																				}
																			}
																			else 
																			{
																				if ($_GPC['myop']=='suggest') 
																				{
																					$rights=$this->myrights(5,'suggest','list');
																					if (!empty($rights)) 
																					{
																						exit('ok');
																					}
																					else 
																					{
																						exit('抱歉，您还没有权限');
																					}
																				}
																				else 
																				{
																					if ($_GPC['myop']=='door') 
																					{
																						$rights=$this->myrights(5,'door','list');
																						if (!empty($rights)) 
																						{
																							exit('ok');
																						}
																						else 
																						{
																							exit('抱歉，您还没有权限');
																						}
																					}
																					else 
																					{
																						if ($_GPC['myop']=='elevator') 
																						{
																							$rights=$this->myrights(5,'elevator','list');
																							if (!empty($rights)) 
																							{
																								exit('ok');
																							}
																							else 
																							{
																								exit('抱歉，您还没有权限');
																							}
																						}
																						else 
																						{
																							if ($_GPC['myop']=='car') 
																							{
																								$rights=$this->myrights(7,'car','list');
																								if (!empty($rights)) 
																								{
																									exit('ok');
																								}
																								else 
																								{
																									exit('抱歉，您还没有权限');
																								}
																							}
																							else 
																							{
																								if ($_GPC['myop']=='carlist') 
																								{
																									$rights=$this->myrights(7,'car','carlist');
																									if (!empty($rights)) 
																									{
																										exit('ok');
																									}
																									else 
																									{
																										exit('抱歉，您还没有权限');
																									}
																								}
																								else 
																								{
																									if ($_GPC['myop']=='monthcard') 
																									{
																										$rights=$this->myrights(7,'car','monthcarlog');
																										if (!empty($rights)) 
																										{
																											exit('ok');
																										}
																										else 
																										{
																											exit('抱歉，您还没有权限');
																										}
																									}
																									else 
																									{
																										if ($_GPC['myop']=='parkpay') 
																										{
																											$rights=$this->myrights(7,'car','parkpaylog');
																											if (!empty($rights)) 
																											{
																												exit('ok');
																											}
																											else 
																											{
																												exit('抱歉，您还没有权限');
																											}
																										}
																										else 
																										{
																											if ($_GPC['myop']=='patrol') 
																											{
																												$rights=$this->myrights(9,'security','patrol');
																												if (!empty($rights)) 
																												{
																													exit('ok');
																												}
																												else 
																												{
																													exit('抱歉，您还没有权限');
																												}
																											}
																											else 
																											{
																												if ($_GPC['myop']=='secline') 
																												{
																													$rights=$this->myrights(9,'security','list');
																													if (!empty($rights)) 
																													{
																														exit('ok');
																													}
																													else 
																													{
																														exit('抱歉，您还没有权限');
																													}
																												}
																												else 
																												{
																													if ($_GPC['myop']=='devcate') 
																													{
																														$rights=$this->myrights(9,'devpatrol','category');
																														if (!empty($rights)) 
																														{
																															exit('ok');
																														}
																														else 
																														{
																															exit('抱歉，您还没有权限');
																														}
																													}
																													else 
																													{
																														if ($_GPC['myop']=='device') 
																														{
																															$rights=$this->myrights(9,'devpatrol','device');
																															if (!empty($rights)) 
																															{
																																exit('ok');
																															}
																															else 
																															{
																																exit('抱歉，您还没有权限');
																															}
																														}
																														else 
																														{
																															if ($_GPC['myop']=='devtask') 
																															{
																																$rights=$this->myrights(9,'devpatrol','list');
																																if (!empty($rights)) 
																																{
																																	exit('ok');
																																}
																																else 
																																{
																																	exit('抱歉，您还没有权限');
																																}
																															}
																															else 
																															{
																																if ($_GPC['myop']=='memowner') 
																																{
																																	$rights=$this->myrights(6,'member','list');
																																	if (!empty($rights)) 
																																	{
																																		exit('ok');
																																	}
																																	else 
																																	{
																																		exit('抱歉，您还没有权限');
																																	}
																																}
																																else 
																																{
																																	if ($_GPC['myop']=='member') 
																																	{
																																		$rights=$this->myrights(6,'member','userlist');
																																		if (!empty($rights)) 
																																		{
																																			exit('ok');
																																		}
																																		else 
																																		{
																																			exit('抱歉，您还没有权限');
																																		}
																																	}
																																	else 
																																	{
																																		if ($_GPC['myop']=='weixin') 
																																		{
																																			$rights=$this->myrights(6,'member','weixin');
																																			if (!empty($rights)) 
																																			{
																																				exit('ok');
																																			}
																																			else 
																																			{
																																				exit('抱歉，您还没有权限');
																																			}
																																		}
																																		else 
																																		{
																																			if ($_GPC['myop']=='fans') 
																																			{
																																				$rights=$this->myrights(6,'member','fans');
																																				if (!empty($rights)) 
																																				{
																																					exit('ok');
																																				}
																																				else 
																																				{
																																					exit('抱歉，您还没有权限');
																																				}
																																			}
																																			else 
																																			{
																																				if ($_GPC['myop']=='smssms' || $_GPC['myop']=='smsmark' || $_GPC['myop']=='smsfee' || $_GPC['myop']=='smsbase' || $_GPC['myop']=='smsdev' || $_GPC['myop']=='printer') 
																																				{
																																					if (empty($_W['uid'])) 
																																					{
																																						if ($_GPC['myop']=='smsmark' || $_GPC['myop']=='smsdev') 
																																						{
																																							exit('抱歉，您还没有权限');
																																						}
																																					}
																																					$rights=$this->myrights(2,'category','list');
																																					if (!empty($rights)) 
																																					{
																																						exit('ok');
																																					}
																																					else 
																																					{
																																						exit('抱歉，您还没有权限');
																																					}
																																				}
																																				else 
																																				{
																																					if ($_GPC['myop']=='forumfans') 
																																					{
																																						$rights=$this->myrights(11,'forum','member');
																																						if (!empty($rights)) 
																																						{
																																							exit('ok');
																																						}
																																						else 
																																						{
																																							exit('抱歉，您还没有权限');
																																						}
																																					}
																																					else 
																																					{
																																						if ($_GPC['myop']=='forumreply') 
																																						{
																																							$rights=$this->myrights(11,'forum','replylist');
																																							if (!empty($rights)) 
																																							{
																																								exit('ok');
																																							}
																																							else 
																																							{
																																								exit('抱歉，您还没有权限');
																																							}
																																						}
																																						else 
																																						{
																																							if ($_GPC['myop']=='forumpost') 
																																							{
																																								$rights=$this->myrights(11,'forum','postlist');
																																								if (!empty($rights)) 
																																								{
																																									exit('ok');
																																								}
																																								else 
																																								{
																																									exit('抱歉，您还没有权限');
																																								}
																																							}
																																							else 
																																							{
																																								if ($_GPC['myop']=='forumlist') 
																																								{
																																									$rights=$this->myrights(11,'forum','list');
																																									if (!empty($rights)) 
																																									{
																																										exit('ok');
																																									}
																																									else 
																																									{
																																										exit('抱歉，您还没有权限');
																																									}
																																								}
																																								else 
																																								{
																																									if ($_GPC['myop']=='forumcomp') 
																																									{
																																										$rights=$this->myrights(11,'forum','complain');
																																										if (!empty($rights)) 
																																										{
																																											exit('ok');
																																										}
																																										else 
																																										{
																																											exit('抱歉，您还没有权限');
																																										}
																																									}
																																									else 
																																									{
																																										if ($_GPC['myop']=='forummanager') 
																																										{
																																											$rights=$this->myrights(11,'forum','manager');
																																											if (!empty($rights)) 
																																											{
																																												exit('ok');
																																											}
																																											else 
																																											{
																																												exit('抱歉，您还没有权限');
																																											}
																																										}
																																										else 
																																										{
																																											if ($_GPC['myop']=='payreport') 
																																											{
																																												$rights=$this->myrights(4,'report','paylist');
																																												if (!empty($rights)) 
																																												{
																																													exit('ok');
																																												}
																																												else 
																																												{
																																													exit('抱歉，您还没有权限');
																																												}
																																											}
																																											else 
																																											{
																																												if ($_GPC['myop']=='feereport') 
																																												{
																																													$rights=$this->myrights(4,'report','billlist');
																																													if (!empty($rights)) 
																																													{
																																														exit('ok');
																																													}
																																													else 
																																													{
																																														exit('抱歉，您还没有权限');
																																													}
																																												}
																																												else 
																																												{
																																													if ($_GPC['myop']=='bindreport') 
																																													{
																																														$rights=$this->myrights(4,'report','bindlist');
																																														if (!empty($rights)) 
																																														{
																																															exit('ok');
																																														}
																																														else 
																																														{
																																															exit('抱歉，您还没有权限');
																																														}
																																													}
																																													else 
																																													{
																																														if ($_GPC['myop']=='repairreport') 
																																														{
																																															$rights=$this->myrights(4,'report','repairlist');
																																															if (!empty($rights)) 
																																															{
																																																exit('ok');
																																															}
																																															else 
																																															{
																																																exit('抱歉，您还没有权限');
																																															}
																																														}
																																														else 
																																														{
																																															if ($_GPC['myop']=='suggestreport') 
																																															{
																																																$rights=$this->myrights(4,'report','suggestlist');
																																																if (!empty($rights)) 
																																																{
																																																	exit('ok');
																																																}
																																																else 
																																																{
																																																	exit('抱歉，您还没有权限');
																																																}
																																															}
																																															else 
																																															{
																																																if ($_GPC['myop']=='repairpreport') 
																																																{
																																																	$rights=$this->myrights(4,'report','repairplist');
																																																	if (!empty($rights)) 
																																																	{
																																																		exit('ok');
																																																	}
																																																	else 
																																																	{
																																																		exit('抱歉，您还没有权限');
																																																	}
																																																}
																																																else 
																																																{
																																																	if ($_GPC['myop']=='costreport') 
																																																	{
																																																		$rights=$this->myrights(4,'report','costlist');
																																																		if (!empty($rights)) 
																																																		{
																																																			exit('ok');
																																																		}
																																																		else 
																																																		{
																																																			exit('抱歉，您还没有权限');
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
			exit('异常操作');
		}
		else 
		{
			if ($operation=='base') 
			{
				$rights=$this->myrights(2,'category','list');
				$condition .=' and rid = :rid and type = :type ';
				$params[':rid']=$_GPC['rid'];
				$data=array(0=>array('id'=>1,'title'=>'通知分类'),1=>array('id'=>2,'title'=>'报修分类'),2=>array('id'=>3,'title'=>'投诉建议'),3=>array('id'=>5,'title'=>'内部工单'),4=>array('id'=>4,'title'=>'服务团队'));
				$k=0;
				while ($k<count($data)) 
				{
					$params[':type']=$data[$k]['id'];
					$sql='select * from '.tablename('rhinfo_zyxq_category').' where '.$condition.' ORDER BY title*1 ASC ';
					$data[$k]['cate']=pdo_fetchall($sql,$params);
					$k=$k+1;
				}
				include($this->mywtpl());
			}
			else 
			{
				if ($operation=='room') 
				{
					$rights=$this->myrights(2,'room','list');
					$condition .=' and rid = :rid ';
					$params[':rid']=$_GPC['rid'];
					if ($_W['isajax']) 
					{
						if (!empty($_GPC['isfree'])) 
						{
							$condition .=' AND isfree='.$_GPC['isfree'];
						}
						if (!empty($_GPC['bid'])) 
						{
							$condition .=' and bid='.$_GPC['bid'];
							$units=pdo_getall('rhinfo_zyxq_unit',array('weid'=>$mywe['weid'],'rid'=>$_GPC['rid'],'bid'=>$_GPC['bid']),array(0=>'id',1=>'title'));
						}
						if (!empty($_GPC['tid'])) 
						{
							$condition .=' and tid='.$_GPC['tid'];
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
					}
					$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_room').' where '.$condition;
					$total=pdo_fetchcolumn($sql,$params);
					if ($total>0) 
					{
						$sql='select * from '.tablename('rhinfo_zyxq_room').' where '.$condition." ORDER BY `ID` ASC ".$limit;
						$data=pdo_fetchall($sql,$params);
						$k=0;
						while ($k<count($data)) 
						{
							$sql='select title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
							$building=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$data[$k]['pid'],':rid'=>$data[$k]['rid'],':bid'=>$data[$k]['bid']));
							$data[$k]['building']=$building;
							$sql='select title from '.tablename('rhinfo_zyxq_unit').' where id = :id and weid = :weid and pid=:pid and rid=:rid';
							$unit=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['tid'],':weid'=>$mywe['weid'],':pid'=>$data[$k]['pid'],':rid'=>$data[$k]['rid']));
							$data[$k]['unit']=$unit;
							if (!empty($region['roomfix'])) 
							{
								$data[$k]['title']=$data[$k]['title'].$region['roomfix'];
							}
							$k=$k+1;
						}
						$url=$this->createWebUrl($mydo,array('op'=>'room','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'tid'=>$_GPC['tid'],'isfree'=>$_GPC['isfree'],'keyword'=>$_GPC['keyword'])).$mywe['direct'];
						$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>1,'callbackfuncname'=>'mypage'));
					}
					$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
					$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
					$myunit=array();
					$n=0;
					while ($n<count($buildings)) 
					{
						$sql='select id,title from '.tablename('rhinfo_zyxq_unit').' where weid = :weid and rid = :rid and bid = :bid';
						$units=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id'],':bid'=>$buildings[$n]['id']));
						$myunit[$buildings[$n]['id']]=$units;
						$n=$n+1;
					}
					include($this->mywtpl());
				}
				else 
				{
					if ($operation=='parking') 
					{
						$rights=$this->myrights(2,'parking','list');
						$condition .=' and rid = :rid ';
						$params[':rid']=$_GPC['rid'];
						if ($_W['isajax']) 
						{
							if (!empty($_GPC['isfree'])) 
							{
								$condition .=' AND isfree='.$_GPC['isfree'];
							}
							if (!empty($_GPC['bid'])) 
							{
								$condition .=' and lid='.$_GPC['bid'];
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
						}
						$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_parking').' where '.$condition;
						$total=pdo_fetchcolumn($sql,$params);
						if ($total>0) 
						{
							$sql='select * from '.tablename('rhinfo_zyxq_parking').' where '.$condition." ORDER BY `ID` ASC ".$limit;
							$data=pdo_fetchall($sql,$params);
							$k=0;
							while ($k<count($data)) 
							{
								$sql='select title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
								$building=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$data[$k]['pid'],':rid'=>$data[$k]['rid'],':lid'=>$data[$k]['lid']));
								$data[$k]['building']=$building;
								$k=$k+1;
							}
							$url=$this->createWebUrl($mydo,array('op'=>'parking','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'isfree'=>$_GPC['isfree'],'keyword'=>$_GPC['keyword'])).$mywe['direct'];
							$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>1,'callbackfuncname'=>'mypage'));
						}
						$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=2';
						$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
						include($this->mywtpl());
					}
					else 
					{
						if ($operation=='shop') 
						{
							$rights=$this->myrights(2,'shop','list');
							$condition .=' and rid = :rid ';
							$params[':rid']=$_GPC['rid'];
							if ($_W['isajax']) 
							{
								if (!empty($_GPC['isfree'])) 
								{
									$condition .=' AND isfree='.$_GPC['isfree'];
								}
								if (!empty($_GPC['bid'])) 
								{
									$condition .=' and lid='.$_GPC['bid'];
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
							}
							$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_shop').' where '.$condition;
							$total=pdo_fetchcolumn($sql,$params);
							if ($total>0) 
							{
								$sql='select * from '.tablename('rhinfo_zyxq_shop').' where '.$condition." ORDER BY `ID` ASC ".$limit;
								$data=pdo_fetchall($sql,$params);
								$k=0;
								while ($k<count($data)) 
								{
									$sql='select title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
									$building=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$data[$k]['pid'],':rid'=>$data[$k]['rid'],':lid'=>$data[$k]['lid']));
									$data[$k]['building']=$building;
									$k=$k+1;
								}
								$url=$this->createWebUrl($mydo,array('op'=>'shop','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'isfree'=>$_GPC['isfree'],'keyword'=>$_GPC['keyword'])).$mywe['direct'];
								$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>1,'callbackfuncname'=>'mypage'));
							}
							$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=1';
							$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
							include($this->mywtpl());
						}
						else 
						{
							if ($operation=='garage') 
							{
								$rights=$this->myrights(2,'building','list');
								$condition .=' and rid = :rid ';
								$params[':rid']=$_GPC['rid'];
								if ($_W['isajax']) 
								{
									if (!empty($_GPC['isfree'])) 
									{
										$condition .=' AND isfree='.$_GPC['isfree'];
									}
									if (!empty($_GPC['bid'])) 
									{
										$condition .=' and bid='.$_GPC['bid'];
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
								}
								$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_garage').' where '.$condition;
								$total=pdo_fetchcolumn($sql,$params);
								if ($total>0) 
								{
									$sql='select * from '.tablename('rhinfo_zyxq_garage').' where '.$condition." ORDER BY `ID` ASC ".$limit;
									$data=pdo_fetchall($sql,$params);
									$k=0;
									while ($k<count($data)) 
									{
										$sql='select title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
										$building=pdo_fetchcolumn($sql,array(':weid'=>$mywe['weid'],':pid'=>$data[$k]['pid'],':rid'=>$data[$k]['rid'],':bid'=>$data[$k]['bid']));
										$data[$k]['building']=$building;
										if (!empty($region['roomfix'])) 
										{
											$data[$k]['title']=$data[$k]['title'].$region['roomfix'];
										}
										$k=$k+1;
									}
									$url=$this->createWebUrl($mydo,array('op'=>'garage','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'isfree'=>$_GPC['isfree'],'keyword'=>$_GPC['keyword'])).$mywe['direct'];
									$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>1,'callbackfuncname'=>'mypage'));
								}
								$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
								$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
								include($this->mywtpl());
							}
							else 
							{
								if ($operation=='feeitem') 
								{
									$rights=$this->myrights(3,'fee','item');
									$condition .=' and rid = :rid ';
									$params[':rid']=$_GPC['rid'];
									if ($_W['isajax']) 
									{
										if (!empty($_GPC['category'])) 
										{
											$_GPC['category']=($_GPC['category']==9 ? '' : $_GPC['category']);
											$condition .=' and category='.$_GPC['category'];
										}
										if (!empty($_GPC['keyword'])) 
										{
											$condition .=' and (title LIKE \''.$_GPC['keyword'].'%\')';
										}
									}
									$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_feeitem').' where '.$condition;
									$total=pdo_fetchcolumn($sql,$params);
									if ($total>0) 
									{
										$sql='select * from '.tablename('rhinfo_zyxq_feeitem').' where '.$condition." ORDER BY `ID` ASC ";
										$data=pdo_fetchall($sql,$params);
										$k=0;
										while ($k<count($data)) 
										{
											$sql='select title from '.tablename('rhinfo_zyxq_feelocation').' where id=:id and weid = :weid ';
											$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
											$data[$k]['building']=(empty($building)?empty($data[$k]['category']):(empty($building) ? '依楼宇建立' : $building));
											$data[$k]['building']=(empty($data[$k]['building']) ? '无' : $data[$k]['building']);
											switch($data[$k]['calmethod'])
											{
												case 1:$data[$k]['calmethod']='按建筑面积';
												break;
												case 2:$data[$k]['calmethod']='按使用面积';
												break;
												case 3:$data[$k]['calmethod']='按附加面积';
												break;
												case 4:$data[$k]['calmethod']='按住户';
												break;
												case 5:$data[$k]['calmethod']='按车位';
												break;
												case 6:$data[$k]['calmethod']='按使用数量';
												break;
												case 7:$data[$k]['calmethod']='按承租人';
												break;
												case 8:$data[$k]['calmethod']='按承租面积';
												break;
											}
											$k=$k+1;
										}
									}
									include($this->mywtpl());
								}
								else 
								{
									if ($operation=='feebill') 
									{
										$rights=$this->myrights(3,'fee','bill');
										$condition .=' and rid = :rid ';
										$params[':rid']=$_GPC['rid'];
										if ($_W['isajax']) 
										{
											if ($_GPC['bid']) 
											{
												$condition .=' AND bid= '.$_GPC['bid'];
											}
											if ($_GPC['feebilltype']) 
											{
												$condition .=' AND category = '.$_GPC['feebilltype'];
											}
											if (!empty($_GPC['keyword'])) 
											{
												$condition .=' AND (title LIKE \'%'.$_GPC['keyword'].'%\' OR address LIKE \'%'.$_GPC['keyword'].'%\')';
											}
											if (!empty($_GPC['startdate'])) 
											{
												$starttime=strtotime($_GPC['startdate']);
												$condition .=' and startdate>='.$starttime;
											}
											if (!empty($_GPC['enddate'])) 
											{
												$endtime=strtotime($_GPC['enddate']);
												$condition .=' and enddate<='.strtotime('+1 days',$endtime);
											}
										}
										$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_feebill').' where feetype = 1 and '.$condition;
										$total=pdo_fetchcolumn($sql,$params);
										if ($total>0) 
										{
											$sql='select * from '.tablename('rhinfo_zyxq_feebill').' where feetype = 1 and '.$condition." ORDER BY `ID` ASC ".$limit;
											$data=pdo_fetchall($sql,$params);
											$k=0;
											while ($k<count($data)) 
											{
												if ($data[$k]['category']==2 || $data[$k]['category']==4) 
												{
													$sql='select title from '.tablename('rhinfo_zyxq_location').' where id = :id and weid = :weid';
													$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
													$data[$k]['building']=$building;
													$data[$k]['unit']='';
												}
												else 
												{
													$sql='select title from '.tablename('rhinfo_zyxq_building').' where id = :id and weid = :weid';
													$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
													$data[$k]['building']=$building;
													$sql='select title from '.tablename('rhinfo_zyxq_unit').' where id = :id and weid = :weid';
													$unit=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['tid'],':weid'=>$mywe['weid']));
													$data[$k]['unit']=$unit;
												}
												$data[$k]['daterange']=date('Y-m-d',$data[$k]['startdate']). '~' . date('Y-m-d',$data[$k]['enddate']);
												$k=$k+1;
											}
											$url=$this->createWebUrl($mydo,array('op'=>'feebill','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'feebilltype'=>$_GPC['feebilltype'],'keyword'=>$_GPC['keyword'],'startdate'=>$_GPC['startdate'],'enddate'=>$_GPC['enddate'])).$mywe['direct'];
											$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>'1','callbackfuncname'=>'mypage'));
										}
										$mybuilding=array();
										$myshoplocation=array();
										$myparklocation=array();
										$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
										$mybuilding=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
										$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=1';
										$myshoplocation=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
										$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=2';
										$myparklocation=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
										include($this->mywtpl());
									}
									else 
									{
										if ($operation=='threebill') 
										{
											$rights=$this->myrights(3,'fee','three');
											$condition .=' and rid = :rid ';
											$params[':rid']=$_GPC['rid'];
											if ($_W['isajax']) 
											{
												if ($_GPC['bid']) 
												{
													$condition .=' AND bid= '.$_GPC['bid'];
												}
												if ($_GPC['feebilltype']) 
												{
													$condition .=' AND category = '.$_GPC['feebilltype'];
												}
												if (!empty($_GPC['keyword'])) 
												{
													$condition .=' AND (title LIKE \'%'.$_GPC['keyword'].'%\' OR address LIKE \'%'.$_GPC['keyword'].'%\')';
												}
												if (!empty($_GPC['startdate'])) 
												{
													$starttime=strtotime($_GPC['startdate']);
													$condition .=' and startdate>='.$starttime;
												}
												if (!empty($_GPC['enddate'])) 
												{
													$endtime=strtotime($_GPC['enddate']);
													$condition .=' and enddate<='.strtotime('+1 days',$endtime);
												}
											}
											$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_feebill').' where feetype = 2 and '.$condition;
											$total=pdo_fetchcolumn($sql,$params);
											if ($total>0) 
											{
												$sql='select * from '.tablename('rhinfo_zyxq_feebill').' where feetype = 2 and '.$condition." ORDER BY `ID` ASC ".$limit;
												$data=pdo_fetchall($sql,$params);
												$k=0;
												while ($k<count($data)) 
												{
													if ($data[$k]['category']==2 || $data[$k]['category']==4) 
													{
														$sql='select title from '.tablename('rhinfo_zyxq_location').' where id = :id and weid = :weid';
														$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
														$data[$k]['building']=$building;
														$data[$k]['unit']='';
													}
													else 
													{
														$sql='select title from '.tablename('rhinfo_zyxq_building').' where id = :id and weid = :weid';
														$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
														$data[$k]['building']=$building;
														$sql='select title from '.tablename('rhinfo_zyxq_unit').' where id = :id and weid = :weid';
														$unit=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['tid'],':weid'=>$mywe['weid']));
														$data[$k]['unit']=$unit;
													}
													$data[$k]['daterange']=date('Y-m-d',$data[$k]['startdate']). '~' . date('Y-m-d',$data[$k]['enddate']);
													$k=$k+1;
												}
												$url=$this->createWebUrl($mydo,array('op'=>'threebill','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'feebilltype'=>$_GPC['feebilltype'],'keyword'=>$_GPC['keyword'],'startdate'=>$_GPC['startdate'],'enddate'=>$_GPC['enddate'])).$mywe['direct'];
												$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>1,'callbackfuncname'=>'mypage'));
											}
											$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
											$mybuilding=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
											$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=1';
											$myshoplocation=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
											$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=2';
											$myparklocation=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
											include($this->mywtpl());
										}
										else 
										{
											if ($operation=='tempbill') 
											{
												$rights=$this->myrights(3,'fee','bill');
												$condition .=' and rid = :rid ';
												$params[':rid']=$_GPC['rid'];
												if ($_W['isajax']) 
												{
													if ($_GPC['bid']) 
													{
														$condition .=' AND bid= '.$_GPC['bid'];
													}
													if ($_GPC['feebilltype']) 
													{
														$condition .=' AND category = '.$_GPC['feebilltype'];
													}
													if (!empty($_GPC['keyword'])) 
													{
														$condition .=' AND (title LIKE \'%'.$_GPC['keyword'].'%\' OR address LIKE \'%'.$_GPC['keyword'].'%\')';
													}
													if (!empty($_GPC['startdate'])) 
													{
														$starttime=strtotime($_GPC['startdate']);
														$condition .=' and startdate>='.$starttime;
													}
													if (!empty($_GPC['enddate'])) 
													{
														$endtime=strtotime($_GPC['enddate']);
														$condition .=' and enddate<='.strtotime('+1 days',$endtime);
													}
												}
												$sql='SELECT COUNT(*) FROM '.tablename('rhinfo_zyxq_feebill').' where feetype = 5 and '.$condition;
												$total=pdo_fetchcolumn($sql,$params);
												if ($total>0) 
												{
													$sql='select * from '.tablename('rhinfo_zyxq_feebill').' where feetype = 5 and '.$condition." ORDER BY `ID` ASC ".$limit;
													$data=pdo_fetchall($sql,$params);
													$k=0;
													while ($k<count($data)) 
													{
														if ($data[$k]['category']==2 || $data[$k]['category']==4) 
														{
															$sql='select title from '.tablename('rhinfo_zyxq_location').' where id = :id and weid = :weid';
															$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
															$data[$k]['building']=$building;
															$data[$k]['unit']='';
														}
														else 
														{
															$sql='select title from '.tablename('rhinfo_zyxq_building').' where id = :id and weid = :weid';
															$building=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['bid'],':weid'=>$mywe['weid']));
															$data[$k]['building']=$building;
															$sql='select title from '.tablename('rhinfo_zyxq_unit').' where id = :id and weid = :weid';
															$unit=pdo_fetchcolumn($sql,array(':id'=>$data[$k]['tid'],':weid'=>$mywe['weid']));
															$data[$k]['unit']=$unit;
														}
														$data[$k]['daterange']=date('Y-m-d',$data[$k]['startdate']). '~' . date('Y-m-d',$data[$k]['enddate']);
														$k=$k+1;
													}
													$url=$this->createWebUrl($mydo,array('op'=>'tempbill','rid'=>$_GPC['rid'],'bid'=>$_GPC['bid'],'feebilltype'=>$_GPC['feebilltype'],'keyword'=>$_GPC['keyword'],'startdate'=>$_GPC['startdate'],'enddate'=>$_GPC['enddate'])).$mywe['direct'];
													$pager=pagination($total,$pindex,$psize,$url,array('before'=>5,'after'=>4,'ajaxcallback'=>1,'callbackfuncname'=>'mypage'));
												}
												$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
												$mybuilding=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
												$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=1';
												$myshoplocation=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
												$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=2';
												$myparklocation=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
												include($this->mywtpl());
											}
											else 
											{
												if ($operation=='manual') 
												{
													$videos=pdo_getall('rhinfo_zyxq_operationvideo');
													include($this->mywtpl());
												}
												else 
												{
													if ($operation=='addvideo') 
													{
														if ($_W['isajax']) 
														{
															$videoimage='';
															if (!empty($_FILES['upfile1']['name'])) 
															{
																$tmp_file=$_FILES['upfile1']['tmp_name'];
																$file=$_FILES['upfile1']['name'];
																$file_types=explode('.',$file);
																$file_type=$file_types[count($file_types)-1];
																$path='images/'.intval($_W['uniacid']).'/'.date('Y/m/');
																mkdirs(ATTACHMENT_ROOT.'/'.$path);
																$filename=file_random_name(ATTACHMENT_ROOT.'/'.$path,$file_type);
																$target_file=IA_ROOT.'/attachment/'.$path.$filename;
																$videoimage=$path.$filename;
																if (!copy($tmp_file,$target_file)) 
																{
																	$videoimage='';
																}
															}
															$video='';
															if (!empty($_FILES['upfile2']['name'])) 
															{
																$tmp_file=$_FILES['upfile2']['tmp_name'];
																$file=$_FILES['upfile2']['name'];
																$file_types=explode('.',$file);
																$file_type=$file_types[count($file_types)-1];
																$path='videos/'.intval($_W['uniacid']).'/'.date('Y/m/');
																mkdirs(ATTACHMENT_ROOT.'/'.$path);
																$filename=file_random_name(ATTACHMENT_ROOT.'/'.$path,$file_type);
																$target_file=IA_ROOT.'/attachment/'.$path.$filename;
																$video=$path.$filename;
																if (!copy($tmp_file,$target_file)) 
																{
																	$video='';
																}
															}
															if (empty($video)) 
															{
																exit('视频文件不能为空');
															}
															$data=array('weid'=>$mywe['weid'],'title'=>$_GPC['title'],'image'=>$videoimage,'video'=>$video,'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
															$res=pdo_insert('rhinfo_zyxq_operationvideo',$data);
															if ($res) 
															{
																exit('ok');
															}
															else 
															{
																exit('操作失败');
															}
														}
														include($this->mywtpl());
													}
													else 
													{
														if ($operation=='delvideo') 
														{
															$id=intval($_GPC['id']);
															$glue='AND';
															$result=pdo_delete('rhinfo_zyxq_operationvideo',array('id'=>$id,'weid'=>$mywe['weid']),$glue);
															if (!empty($result)) 
															{
																echo 'ok';
															}
															else 
															{
																echo '删除失败!';
															}
															exit(0);
														}
														else 
														{
															if ($operation=='additem') 
															{
																$current='添加收费项目';
																if ($_W['isajax']) 
																{
																	$bid=0;
																	$bids=$_GPC['bids'];
																	if (!empty($bids)) 
																	{
																		if ($_GPC['category']==1) 
																		{
																			$data_location=array('weid'=>$mywe['weid'],'pid'=>$region['pid'],'rid'=>$_GPC['rid'],'title'=>$_GPC['title'].'分组','category'=>$_GPC['category'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
																			pdo_insert('rhinfo_zyxq_feelocation',$data_location);
																			$flid=pdo_insertid();
																			$bids=$_GPC['bids'];
																			$bidsarray=explode(',',$bids);
																			$k=0;
																			while ($k<count($bidsarray)) 
																			{
																				pdo_update('rhinfo_zyxq_building',array('flid'=>$flid),array('weid'=>$mywe['weid'],'id'=>$bidsarray[$k]));
																				$k=$k+1;
																			}
																			$bid=$flid;
																		}
																		else 
																		{
																			if ($_GPC['category']==2) 
																			{
																				$data_location=array('weid'=>$mywe['weid'],'pid'=>$region['pid'],'rid'=>$_GPC['rid'],'title'=>$_GPC['title'].'分组','category'=>$_GPC['category'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
																				pdo_insert('rhinfo_zyxq_feelocation',$data_location);
																				$flid=pdo_insertid();
																				$bids=$_GPC['bids'];
																				$bidsarray=explode(',',$bids);
																				$k=0;
																				while ($k<count($bidsarray)) 
																				{
																					pdo_update('rhinfo_zyxq_location',array('flid'=>$flid),array('weid'=>$mywe['weid'],'id'=>$bidsarray[$k]));
																					$k=$k+1;
																				}
																				$bid=$flid;
																			}
																		}
																	}
																	else 
																	{
																		if (($_GPC['category']==1 || $_GPC['category']==2)) 
																		{
																			$bid=$_GPC['bid'];
																		}
																	}
																	if (empty($_GPC['paymonths'])) 
																	{
																		$paymonths=1;
																	}
																	else 
																	{
																		$paymonths=($_GPC['paymonths']>12 ? 12 : $_GPC['paymonths']);
																	}
																	$data=array('weid'=>$mywe['weid'],'pid'=>$region['pid'],'rid'=>$_GPC['rid'],'bid'=>$bid,'category'=>$_GPC['category'],'title'=>$_GPC['title'],'measure'=>$_GPC['measure'],'calmethod'=>$_GPC['calmethod'],'price'=>$_GPC['price'],'paymonths'=>$paymonths,'status'=>$_GPC['status'],'isimport'=>$_GPC['isimport'],'remark'=>$_GPC['remark'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
																	$res=pdo_insert('rhinfo_zyxq_feeitem',$data);
																	$id=pdo_insertid();
																	$this->mysyslog($mywe['pid'],'fee',$operation,$current,$current.'id='.$id);
																	if ($_GPC['category']==0) 
																	{
																		$bidsarray=explode(',',$bids);
																		$k=0;
																		while ($k<count($bidsarray)) 
																		{
																			$sql='select title from '.tablename('rhinfo_zyxq_building').' where id = :id and weid = :weid';
																			$building=pdo_fetchcolumn($sql,array(':id'=>$bidsarray[$k],':weid'=>$mywe['weid']));
																			$data_location=array('weid'=>$mywe['weid'],'pid'=>$region['pid'],'rid'=>$_GPC['rid'],'title'=>$building,'category'=>$_GPC['category'],'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
																			pdo_insert('rhinfo_zyxq_feelocation',$data_location);
																			$flid=pdo_insertid();
																			$data_feeitem=array('weid'=>$mywe['weid'],'pid'=>$region['pid'],'rid'=>$_GPC['rid'],'bid'=>$bidsarray[$k],'itemid'=>$id,'flid'=>$flid,'title'=>$building,'cuid'=>$mywe['uid'],'ctime'=>TIMESTAMP);
																			pdo_insert('rhinfo_zyxq_feeitem_building',$data_feeitem);
																			$k=$k+1;
																		}
																	}
																	if ($res) 
																	{
																		exit('ok');
																	}
																	else 
																	{
																		exit('操作失败');
																	}
																}
																$category=$_GPC['category'];
																if ($category=="\\\\60") 
																{
																	$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where  weid=:weid and rid=:rid ORDER BY title,id ASC ';
																	$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																}
																$feeitem_groups=array();
																if ($category==1) 
																{
																	$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where flid=0 and weid=:weid and rid=:rid ORDER BY title,id ASC ';
																	$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																	$sql='select id,title from '.tablename('rhinfo_zyxq_feelocation').' where weid = :weid and rid = :rid and category=:category';
																	$feeitem_groups=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id'],':category'=>$category));
																}
																if ($category==2) 
																{
																	$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where flid=0 and category=1 and weid=:weid and rid=:rid ORDER BY title,id ASC ';
																	$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																	$sql='select id,title from '.tablename('rhinfo_zyxq_feelocation').' where weid = :weid and rid = :rid and category=:category';
																	$feeitem_groups=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id'],':category'=>$category));
																}
																include($this->mywtpl('postitem'));
															}
															else 
															{
																if ($operation=='edititem') 
																{
																	$current='编辑收费项目';
																	$id=intval($_GPC['id']);
																	if ($_W['isajax']) 
																	{
																		if (empty($_GPC['paymonths'])) 
																		{
																			$paymonths=1;
																		}
																		else 
																		{
																			$paymonths=($_GPC['paymonths']>12 ? 12 : $_GPC['paymonths']);
																		}
																		$data=array('bid'=>$_GPC['bid'],'title'=>$_GPC['title'],'measure'=>$_GPC['measure'],'calmethod'=>$_GPC['calmethod'],'price'=>$_GPC['price'],'paymonths'=>$paymonths,'status'=>$_GPC['status'],'isimport'=>$_GPC['isimport'],'remark'=>$_GPC['remark']);
																		$glue='AND';
																		$result=pdo_update('rhinfo_zyxq_feeitem',$data,array('id'=>$id,'weid'=>$mywe['weid']),$glue);
																		$this->mysyslog($region['pid'],$mydo,$operation,$current,$current.'id='.$id);
																		if ($result) 
																		{
																			exit('ok');
																		}
																		else 
																		{
																			exit('操作失败');
																		}
																	}
																	$sql='select * from '.tablename('rhinfo_zyxq_feeitem').' where id = :id and weid = :weid';
																	$item=pdo_fetch($sql,array(':id'=>$id,':weid'=>$mywe['weid']));
																	$category=$item['category'];
																	$feeitem_groups=array();
																	if ($category==1) 
																	{
																		$sql='select id,title from '.tablename('rhinfo_zyxq_feelocation').' where weid = :weid and rid = :rid and category=:category';
																		$feeitem_groups=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id'],':category'=>$category));
																	}
																	if ($category=='2') 
																	{
																		$sql='select id,title from '.tablename('rhinfo_zyxq_feelocation').' where weid = :weid and rid = :rid and category=:category';
																		$feeitem_groups=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id'],':category'=>$category));
																	}
																	include($this->mywtpl('postitem'));
																}
																else 
																{
																	if ($operation=='addbill') 
																	{
																		$category=$_GPC['category'];
																		$sql='select id,title from '.tablename('rhinfo_zyxq_feeitem').' where status = 1 and category=:category and isimport=0 and weid=:weid and rid=:rid';
																		$feeitems=pdo_fetchall($sql,array(':category'=>$category,':weid'=>$mywe['weid'],':rid'=>$region['id']));
																		include($this->mywtpl());
																	}
																	else 
																	{
																		if ($operation=='addtempbill') 
																		{
																			$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid';
																			$buildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																			$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid = :weid and rid = :rid and isbarn=1';
																			$gbuildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																			$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=1';
																			$slocations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																			$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid = :weid and rid = :rid and category=2';
																			$plocations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																			include($this->mywtpl());
																		}
																		else 
																		{
																			if ($operation=='exportthree') 
																			{
																				$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid=:weid and rid=:rid ORDER BY title,id ASC ';
																				$mybuildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																				$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid=:weid and rid=:rid and category=1 ORDER BY title,id ASC ';
																				$mylocations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																				include($this->mywtpl());
																			}
																			else 
																			{
																				if ($operation=='importthree') 
																				{
																					include($this->mywtpl());
																				}
																				else 
																				{
																					if ($operation=='exportbill') 
																					{
																						$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid=:weid and rid=:rid ORDER BY title,id ASC ';
																						$mybuildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																						$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid=:weid and rid=:rid and category=1 ORDER BY title,id ASC ';
																						$mylocations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																						include($this->mywtpl());
																					}
																					else 
																					{
																						if ($operation=='importbill') 
																						{
																							include($this->mywtpl());
																						}
																						else 
																						{
																							if ($operation=='exporttempbill') 
																							{
																								$sql='select id,title from '.tablename('rhinfo_zyxq_building').' where weid=:weid and rid=:rid ORDER BY title,id ASC ';
																								$mybuildings=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																								$sql='select id,title from '.tablename('rhinfo_zyxq_location').' where weid=:weid and rid=:rid and category=1 ORDER BY title,id ASC ';
																								$mylocations=pdo_fetchall($sql,array(':weid'=>$mywe['weid'],':rid'=>$region['id']));
																								include($this->mywtpl());
																							}
																							else 
																							{
																								if ($operation=='importtempbill') 
																								{
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
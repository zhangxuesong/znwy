<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'home';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$mydo = 'home';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getnotice($_W['member']['uid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
} elseif ($this->syscfg['listtype'] == 1) {
    if ($operation == 'list' || $operation == 'blist' || $operation == 'flist' || $operation == 'plist') {
        $fromop = $operation;
        $operation = 'listcity';
    }
}
if ($operation == 'index') {
    $rid = intval($_GPC['rid']);
    if ($_W['isajax']) {
        if ($_GPC['cfrom'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' and id = :rid';
            $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
            $pindex = max(1, intval($_GPC['page']));
            $psize = 5;
            $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $condition .= ' and (rid=:rid or rid=0) and postid=0 and deleted=0 ';
            $params[':rid'] = $rid;
            $condition .= ' and checked=1 ';
            $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . '  where ' . $condition . ' ORDER BY createtime desc ' . $limit;
            $list = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($list))) {
                $list[$k]['avatar'] = tomedia($list[$k]['avatar']);
                $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $list[$k]['id']));
                $list[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=:pid limit 1', array(':pid' => $list[$k]['id']));
                $list[$k]['boardtitle'] = pdo_fetchcolumn('select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where id=:boardid limit 1', array(':boardid' => $list[$k]['boardid']));
                $images = array();
                $images1 = array();
                $images2 = array();
                $rowimages = iunserializer($list[$k]['images']);
                if (is_array($rowimages) && !empty($rowimages)) {
                    $m = 0;
                    while (!($m >= count($rowimages))) {
                        if (count($rowimages) == 4) {
                            if ($m > 1) {
                                $images2[] = tomedia($rowimages[$m]);
                            } else {
                                $images1[] = tomedia($rowimages[$m]);
                            }
                        } elseif (count($rowimages) == 5) {
                            if ($m > 1) {
                                $images2[] = tomedia($rowimages[$m]);
                            } else {
                                $images1[] = tomedia($rowimages[$m]);
                            }
                        } elseif (count($rowimages) == 3) {
                            if ($m > 0) {
                                $images2[] = tomedia($rowimages[$m]);
                            } else {
                                $images1[] = tomedia($rowimages[$m]);
                            }
                        } else {
                            $images1[] = tomedia($rowimages[$m]);
                        }
                        ($m += 1) + -1;
                    }
                }
                $list[$k]['images'] = array_merge($images1, $images2);
                $list[$k]['images1'] = $images1;
                $list[$k]['images2'] = $images2;
                $list[$k]['imagewidth'] = '100';
                $list[$k]['imagewidth1'] = '100';
                $list[$k]['imageheight'] = '100';
                $list[$k]['imageheight1'] = '100';
                if (count($rowimages) == 1) {
                    $list[$k]['imagewidth'] = '99%';
                    $list[$k]['imageheight'] = '150';
                } elseif (count($rowimages) == 2) {
                    $list[$k]['imagewidth'] = '49%';
                    $list[$k]['imageheight'] = '150';
                } elseif (count($rowimages) == 3) {
                    $list[$k]['imagewidth'] = '99%';
                    $list[$k]['imagewidth1'] = '49%';
                    $list[$k]['imageheight'] = '150';
                    $list[$k]['imageheight1'] = '100';
                } elseif (count($rowimages) == 4) {
                    $list[$k]['imagewidth'] = '49%';
                    $list[$k]['imagewidth1'] = '49%';
                    $list[$k]['imageheight'] = '100';
                    $list[$k]['imageheight1'] = '100';
                } elseif (count($rowimages) == 5) {
                    $list[$k]['imagewidth'] = '49%';
                    $list[$k]['imagewidth1'] = '32%';
                    $list[$k]['imageheight'] = '100';
                    $list[$k]['imageheight1'] = '100';
                }
                $list[$k]['imagecount'] = count($rowimages);
                $list[$k]['content'] = replaceContent($list[$k]['content']);
                $list[$k]['createtime'] = timeBefore($list[$k]['createtime']);
                $list[$k]['parent'] = false;
                if (!empty($list[$k]['replyid'])) {
                    $parentPost = $this->getPost($list[$k]['replyid']);
                    $list[$k]['parent'] = array('nickname' => $parentPost['nickname'], 'content' => replaceContent($parentPost['content']));
                }
                $isgood = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid and (openid=:openid or uid=:uid) limit 1', array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['id'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
                $list[$k]['isgood'] = $isgood;
                $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid  limit 1', array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['id']));
                ($k += 1) + -1;
            }
            if (empty($list)) {
                echo '';
            } else {
                include $this->mymtpl('boarditem');
            }
        } elseif ($_GPC['cfrom'] == 2) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' and id = :rid';
            $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
            $pindex = max(1, intval($_GPC['page']));
            $psize = 5;
            $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $condition .= ' and (rid=:rid or rid=0) and status=1 and isrecommand=1 ';
            $params[':rid'] = $rid;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_article') . '  where ' . $condition . ' ORDER BY ctime desc ' . $limit;
            $articles = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($articles))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article_log') . ' where weid=:weid and aid=:aid ';
                $times = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':aid' => $articles[$k]['id']));
                $articles[$k]['times'] = $times;
                $articles[$k]['images'] = iunserializer($articles[$k]['images']);
                ($k += 1) + -1;
            }
            if (empty($articles)) {
                echo '';
            } else {
                include $this->mymtpl('article');
            }
        } else {
            echo '';
        }
        exit(0);
    }
    if ($_W['minirid']) {
        $rid = $_W['minirid'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' and id = :rid';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid=:openid or uid=:uid) and category = :category and deleted=0 and status=0 ';
        $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':category' => 1));
    } elseif ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' limit 1';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $item['id'];
    } else {
        if (empty($rid)) {
            if ($user['rid'] && ($user['isbind'] || $user['isfollow'])) {
                $rid = $user['rid'];
            } else {
                if ($this->syscfg['nobindhome'] == 1) {
                    header('Location:' . $this->createMobileurl($mydo, array('op' => 'blist')));
                } elseif ($this->syscfg['nobindhome'] == 2) {
                    header('Location:' . $this->createMobileurl($mydo, array('op' => 'list')));
                } else {
                    header('Location:' . $this->createMobileurl($mydo, array('op' => 'home')));
                }
                exit(0);
            }
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' and id = :rid';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    }
    $sql = 'select link,thumb,wxappid,wxapppage,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and btype=1 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $advs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid));
    $sql = 'select link,thumb,wxappid,wxapppage,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and  btype=2 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid));
    $sql = 'select link,thumb,wxappid,wxapppage,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and  btype=3 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $cubes = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid));
    if (empty($advs)) {
        $sql = 'select link,thumb,wxappid,wxapppage,bgimage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=1 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $advs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    if (empty($banners)) {
        $sql = 'select link,thumb,wxappid,wxapppage,bgimage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=2 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    if (empty($cubes)) {
        $sql = 'select link,thumb,wxappid,wxapppage,bgimage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=3 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $cubes = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    $sql = 'select link,wxappid,wxapppage,title,thumb,bgimage from ' . tablename('rhinfo_zyxq_regionav') . ' where weid=:weid and pid = :pid and rid = :rid and category=1 and enabled = 1 order by displayorder desc limit 16';
    $navs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid));
    if (empty($navs)) {
        $sql = 'select link,wxappid,wxapppage,title,thumb,bgimage from ' . tablename('rhinfo_zyxq_nav') . ' where weid=:weid and category=1 and enabled = 1 order by displayorder desc limit 16';
        $navs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    $menu1 = array();
    $menu2 = array();
    $i = 0;
    if (count($navs) > 8) {
        $k = 0;
        while (!($k >= count($navs))) {
            if (!($i >= 8)) {
                $menu1[$i] = $navs[$k];
            } else {
                $menu2[$i - 8] = $navs[$k];
            }
            ($i += 1) + -1;
            ($k += 1) + -1;
        }
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and pid = :pid and rid = :rid and category=1 and status > 0 order by id desc limit 5';
    $notices = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid));
    $k = 0;
    while (!($k >= count($notices))) {
        $notices[$k]['link'] = $this->createMobileurl('notice', array('op' => 'detail', 'id' => $notices[$k]['id']));
        ($k += 1) + -1;
    }
    $total_forum = 0;
    $total_article = 0;
    if ($this->syscfg['ishome'] == 1) {
        $condition .= ' and (rid=:rid or rid=0) and postid=0 and deleted=0 ';
        $params[':rid'] = $rid;
        $condition .= ' and checked=1 ';
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where ' . $condition;
        $total_forum = pdo_fetchcolumn($sql, $params);
    } elseif ($this->syscfg['ishome'] == 2) {
        $condition .= ' and (rid=:rid or rid=0) and status=1 and isrecommand=1 ';
        $params[':rid'] = $rid;
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article') . ' where ' . $condition;
        $total_article = pdo_fetchcolumn($sql, $params);
    } elseif ($this->syscfg['ishome'] == 3 || $this->syscfg['ishome'] == 4) {
        $condition1 = $condition . ' and (rid=:rid or rid=0) and postid=0 and deleted=0 ';
        $condition2 = $condition . ' and (rid=:rid or rid=0) and status=1 and isrecommand=1 ';
        $params[':rid'] = $rid;
        $condition1 .= ' and checked=1 ';
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where ' . $condition1;
        $total_forum = pdo_fetchcolumn($sql, $params);
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article') . ' where ' . $condition2;
        $total_article = pdo_fetchcolumn($sql, $params);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id = :pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid']));
    $isbind = 0;
    if ($user['rid'] == $rid) {
        $isbind = 1;
    } else {
        $member_bind = pdo_get('rhinfo_zyxq_member', array('weid' => $_W['uniacid'], 'rid' => $rid, 'uid' => $_W['member']['uid'], 'deleted' => 0, 'status' => 0));
        if (!empty($member_bind)) {
            $isbind = 1;
        } else {
            $region_follow = pdo_get('rhinfo_zyxq_region_follow', array('weid' => $_W['uniacid'], 'rid' => $rid, 'uid' => $_W['member']['uid'], 'deleted' => 0));
            if (!empty($region_follow)) {
                $isbind = 1;
            }
        }
    }
    $user['rtitle'] = $item['title'];
    $_share['title'] = $item['title'];
    $_share['imgUrl'] = tomedia($item['thumb']);
    include $this->mymtpl('index');
} elseif ($operation == 'home') {
    $sql = 'select link,thumb,wxappid,wxapppage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=1 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $advs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,thumb,wxappid,wxapppage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=2 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,thumb,wxappid,wxapppage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=3 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $cubes = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,wxappid,wxapppage,title,thumb from ' . tablename('rhinfo_zyxq_nav') . ' where weid=:weid and category=1 and enabled = 1 order by displayorder desc limit 16';
    $navs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    if (empty($navs) && empty($advs) && empty($cubes)) {
        $rid = $user['rid'];
        if (empty($rid)) {
            $rid = pdo_getcolumn('rhinfo_zyxq_region', array('weid' => $_W['uniacid']), 'id');
        }
        $advs = array();
        $advs[] = array('link' => mymurl('service', array('rid' => $rid)), 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/banner/1.png');
        $advs[] = array('link' => mymurl('forum', array('rid' => $rid)), 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/banner/2.png');
        $advs[] = array('link' => mymurl('article', array('rid' => $rid)), 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/banner/3.png');
        $navs = array();
        $navs[] = array('link' => mymurl('service/property', array('rid' => $rid)), 'title' => '物业介绍', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/1.png');
        $navs[] = array('link' => mymurl('article'), 'title' => '最新动态', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/2.png');
        $navs[] = array('link' => mymurl('fee'), 'title' => '缴费中心', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/3.png');
        $navs[] = array('link' => mymurl('forum', array('rid' => $rid)), 'title' => '社区互动', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/4.png');
        $navs[] = array('link' => mymurl('auth/regproperty'), 'title' => '物业入驻', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/5.png');
        $navs[] = array('link' => mymurl('opendoor', array('id' => $rid)), 'title' => '智能门禁', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/6.png');
        $navs[] = array('link' => mymurl('home/scanbind', array('rid' => $rid)), 'title' => '绑定房产', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/7.png');
        $navs[] = array('link' => mymurl('manage', array('rid' => $rid)), 'title' => '物业中心', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/8.png');
        include $this->mymtpl('haina');
    } else {
        $menu1 = array();
        $menu2 = array();
        $i = 0;
        if (count($navs) > 8) {
            $k = 0;
            while (!($k >= count($navs))) {
                if (!($i >= 8)) {
                    $menu1[$i] = $navs[$k];
                } else {
                    $menu2[$i - 8] = $navs[$k];
                }
                ($i += 1) + -1;
                ($k += 1) + -1;
            }
        }
        include $this->mymtpl('home');
    }
} elseif ($operation == 'list' || $operation == 'blist' || $operation == 'flist' || $operation == 'plist' || $operation == 'rlist' || $operation == 'mlist') {
    $range = $_GPC['range'];
    $lbs = $_GPC['lbs'];
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        if (empty($range)) {
            $range = 10;
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where (category=1 or category=2) and ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $temp_data = array();
        $k = 0;
        while (!($k >= count($data))) {
            $isout = false;
            if (empty($lbs)) {
                if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                    $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                    if (!(0 >= $range) && !($range >= $distance)) {
                        $isout = true;
                    }
                    $data[$k]['distance'] = $distance;
                } else {
                    $data[$k]['distance'] = 100000;
                }
            } else {
                $data[$k]['distance'] = 1000000;
            }
            $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'rid' => $data[$k]['id']));
            if ($_GPC['op'] == 'list' || $_GPC['op'] == 'rlist') {
                if (empty($data[$k]['url'])) {
                    $data[$k]['region_url'] = $this->createMobileurl($mydo, array('op' => 'index', 'rid' => $data[$k]['id']));
                } else {
                    $data[$k]['region_url'] = $data[$k]['url'];
                }
            } elseif ($_GPC['op'] == 'flist') {
                $data[$k]['region_url'] = $this->createMobileurl($mydo, array('op' => 'follow', 'rid' => $data[$k]['id']));
            } elseif ($_GPC['op'] == 'blist') {
                if ($data[$k]['register'] == 1) {
                    $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $data[$k]['id'], 'register' => 1));
                } elseif ($data[$k]['register'] == 2) {
                    $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $data[$k]['id'], 'register' => 2));
                } else {
                    if (!empty($user['mobile'])) {
                        $bind = 'bind';
                    } else {
                        $bind = 'rbind';
                    }
                    $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => $bind, 'rid' => $data[$k]['id']));
                }
            } elseif ($_GPC['op'] == 'plist') {
                if (!empty($user['mobile'])) {
                    $bind = 'pbind';
                } else {
                    $bind = 'prbind';
                }
                $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => $bind, 'rid' => $data[$k]['id']));
            } elseif ($_GPC['op'] == 'mlist') {
                $data[$k]['region_url'] = $this->createMobileurl('car', array('op' => 'addlock', 'rid' => $data[$k]['id']));
            }
            $data[$k]['thumb'] = !empty($data[$k]['thumb']) ? tomedia($data[$k]['thumb']) : '';
            if (!empty($this->syscfg['isdisptel'])) {
                $data[$k]['telphone'] = '';
            }
            if ($isout == false) {
                $temp_data[] = $data[$k];
            }
            ($k += 1) + -1;
        }
        $data = multi_array_sort($temp_data, 'distance');
        $start = ($pindex - 1) * $psize;
        if (!empty($data)) {
            $data = array_slice($data, $start, $psize);
        }
        $total = count($data);
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    if ($_W['minirid']) {
        if ($_GPC['op'] == 'list') {
            $region_url = $this->createMobileurl($mydo, array('op' => 'index', 'rid' => $_W['minirid']));
        } elseif ($_GPC['op'] == 'flist') {
            $region_url = $this->createMobileurl($mydo, array('op' => 'follow', 'rid' => $_W['minirid']));
        } elseif ($_GPC['op'] == 'blist') {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_W['minirid']));
            if ($region['register'] == 1) {
                $region_url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $_W['minirid'], 'register' => 1));
            } elseif ($region['register'] == 2) {
                $region_url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $_W['minirid'], 'register' => 2));
            } else {
                if (!empty($user['mobile'])) {
                    $bind = 'bind';
                } else {
                    $bind = 'rbind';
                }
                $region_url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $_W['minirid']));
            }
        } elseif ($_GPC['op'] == 'plist') {
            if (!empty($user['mobile'])) {
                $bind = 'pbind';
            } else {
                $bind = 'prbind';
            }
            $region_url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $_W['minirid']));
        } elseif ($_GPC['op'] == 'mlist') {
            $region_url = $this->createMobileurl('car', array('op' => 'addlock', 'rid' => $_W['minirid']));
        }
        header('Location:' . $region_url);
        exit(0);
    }
    if ($this->syscfg['bindlocation'] == 2) {
        $lbs = 'none';
        include $this->mymtpl('list');
    } else {
        include $this->mymtpl('plist');
    }
} elseif ($operation == 'follow') {
    $rid = $_GPC['rid'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region_follow') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid)';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (empty($item)) {
        $data = array('weid' => $_W['uniacid'], 'pid' => 0, 'rid' => $rid, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'deleted' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_region_follow', $data);
    } else {
        pdo_update('rhinfo_zyxq_region_follow', array('deleted' => 0), array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'rid' => $rid));
    }
    header('Location:' . $this->createMobileurl($mydo, array('op' => 'index', 'rid' => $rid)));
    exit(0);
} elseif ($operation == 'scanbind') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and (openid=:openid or uid=:uid) and category = :category and deleted=0 and status=0 ';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':category' => 1));
    if ($total > 0) {
        $url = $this->createMobileurl('home', array('op' => 'index', 'rid' => $_GPC['rid']));
    } elseif ($region['register']) {
        $url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $_GPC['rid'], 'register' => $region['register']));
    } else {
        if (!empty($user['mobile'])) {
            $bind = 'bind';
        } else {
            $bind = 'rbind';
        }
        $url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $_GPC['rid']));
    }
    header('Location:' . $url);
    exit(0);
} elseif ($operation == 'frombind') {
    if (!empty($user['rid'])) {
        exit(0);
    }
    if (!empty($user['mobile'])) {
        $bind = 'bind';
    } else {
        $bind = 'rbind';
    }
    $url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $_GPC['rid']));
    include $this->mymtpl('bind');
} elseif ($operation == 'bindlist') {
    if (!empty($user['rid'])) {
        exit(0);
    }
    $url = $this->createMobileurl('home', array('op' => 'blist', 'lbs' => 'none'));
    include $this->mymtpl('bind');
} elseif ($operation == 'map') {
    $id = intval($_GPC['rid']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    if (!empty($this->syscfg['isdisptel'])) {
        $item['telphone'] = '';
    }
    include $this->mymtpl('map');
} elseif ($operation == 'listcity') {
    if ($_W['isajax']) {
        $ch = curl_init();
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $_GPC['lat'] . ',' . $_GPC['lng'] . '&get_poi=0&key=' . $sysconifg['qq_lbskey'];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);
        if ($res['status'] == 0) {
            $result = $res['result'];
            $address_component = $result['address_component'];
            $currcity = $address_component['city'];
            $currcityurl = $this->createMobileurl($mydo, array('op' => 'listregion', 'fromop' => $_GPC['fromop'], 'city' => $currcity, 'lat' => $_GPC['lat'], 'lng' => $_GPC['lng']));
        }
        $sql = 'select distinct city from ' . tablename('rhinfo_zyxq_region') . ' where (category=1 or category=2) and ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $citys = array();
        $k = 0;
        while (!($k >= count($data))) {
            $firstChar = getFirstCharter($data[$k]['city']);
            if (!empty($firstChar)) {
                $data[$k]['cityurl'] = $this->createMobileurl($mydo, array('op' => 'listregion', 'fromop' => $_GPC['fromop'], 'city' => $data[$k]['city'], 'lat' => $_GPC['lat'], 'lng' => $_GPC['lng']));
                $citys[$firstChar][] = $data[$k];
            }
            ($k += 1) + -1;
        }
        ksort($citys);
        include $this->mymtpl('citys');
        exit(0);
    }
    include $this->mymtpl('listcity');
} elseif ($operation == 'listregion') {
    $range = $_GPC['range'];
    $lbs = $_GPC['lbs'];
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        if (empty($range)) {
            $range = 10;
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        if (!empty($_GPC['city'])) {
            $condition .= ' AND city = \'' . $_GPC['city'] . '\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where (category=1 or category=2) and ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $temp_data = array();
        $k = 0;
        while (!($k >= count($data))) {
            $isout = false;
            if (empty($lbs)) {
                if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                    $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                    if (!(0 >= $range) && !($range >= $distance)) {
                        $isout = true;
                    }
                    $data[$k]['distance'] = $distance;
                } else {
                    $data[$k]['distance'] = 100000;
                }
            } else {
                $data[$k]['distance'] = 1000000;
            }
            $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'rid' => $data[$k]['id']));
            if ($_GPC['fromop'] == 'list') {
                if (empty($data[$k]['url'])) {
                    $data[$k]['region_url'] = $this->createMobileurl($mydo, array('op' => 'index', 'rid' => $data[$k]['id']));
                } else {
                    $data[$k]['region_url'] = $data[$k]['url'];
                }
            } elseif ($_GPC['fromop'] == 'flist') {
                $data[$k]['region_url'] = $this->createMobileurl($mydo, array('op' => 'follow', 'rid' => $data[$k]['id']));
            } elseif ($_GPC['fromop'] == 'blist') {
                if ($data[$k]['register'] == 1) {
                    $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $data[$k]['id'], 'register' => 1));
                } elseif ($data[$k]['register'] == 2) {
                    $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $data[$k]['id'], 'register' => 2));
                } else {
                    if (!empty($user['mobile'])) {
                        $bind = 'bind';
                    } else {
                        $bind = 'rbind';
                    }
                    $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => $bind, 'rid' => $data[$k]['id']));
                }
            } elseif ($_GPC['fromop'] == 'plist') {
                if (!empty($user['mobile'])) {
                    $bind = 'pbind';
                } else {
                    $bind = 'prbind';
                }
                $data[$k]['region_url'] = $this->createMobileurl('member', array('op' => $bind, 'rid' => $data[$k]['id']));
            }
            $data[$k]['thumb'] = !empty($data[$k]['thumb']) ? tomedia($data[$k]['thumb']) : '';
            if (!empty($this->syscfg['isdisptel'])) {
                $data[$k]['telphone'] = '';
            }
            if ($isout == false) {
                $temp_data[] = $data[$k];
            }
            ($k += 1) + -1;
        }
        $data = multi_array_sort($temp_data, 'distance');
        $start = ($pindex - 1) * $psize;
        if (!empty($data)) {
            $data = array_slice($data, $start, $psize);
        }
        $total = count($data);
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    if ($this->syscfg['bindlocation'] == 2) {
        $lbs = 'none';
    }
    include $this->mymtpl('listregion');
}
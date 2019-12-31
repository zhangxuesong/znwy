<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$curr = 'service';
$mydo = 'service';
$myurl = $this->createMobileurl($mydo);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
if ($operation == 'index') {
    $user = $this->getnotice($_W['member']['uid']);
} else {
    $user = $this->getmyinfo($_W['member']['uid']);
}
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$myrid = empty($_GPC['rid']) ? $user['rid'] : $_GPC['rid'];
if ($operation == 'index') {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($res) {
        if ($res['arrearagelimit7']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id = :pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid']));
    $sql = 'select link,wxappid,wxapppage,title,thumb from ' . tablename('rhinfo_zyxq_nav') . ' where weid=:weid and category=2 and enabled = 1 order by displayorder desc';
    $navs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,wxappid,wxapppage,title,thumb from ' . tablename('rhinfo_zyxq_regionav') . ' where weid=:weid and pid = :pid and rid = :rid and category=2 and enabled = 1 order by displayorder desc';
    $rnavs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $navs = array_merge($rnavs, $navs);
    $sql = 'select link,wxappid,wxapppage,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and  btype=4 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    if (empty($banners)) {
        $sql = 'select link,wxappid,wxapppage,thumb,0 as `pid`,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=4 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    include $this->template($this->mytpl('service/index'));
} elseif ($operation == 'property') {
    $rid = intval($_GPC['rid']);
    if (empty($rid)) {
        if ($user['rid']) {
            $rid = $user['rid'];
        } else {
            header('Location:' . $this->createMobileurl('home', array('op' => 'list')));
            exit(0);
        }
    }
    $sql = 'select p.* from ' . tablename('rhinfo_zyxq_region') . ' as r left join ' . tablename('rhinfo_zyxq_property') . ' as p on r.pid= p.id where r.weid=:weid and r.id=:rid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $content = stripslashes($property['content']);
    $content = html_entity_decode($content);
    if ($property['website']) {
        header('Location:' . $property['website']);
        exit(0);
    }
    include $this->mymtpl('property');
} elseif ($operation == 'myproperty') {
    $pid = intval($_GPC['pid']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $pid));
    $content = stripslashes($property['content']);
    $content = html_entity_decode($content);
    if ($property['website']) {
        header('Location:' . $property['website']);
        exit(0);
    }
    include $this->mymtpl('property');
} elseif ($operation == 'map') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $item['thumb'] = $item['banner'];
    $url = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $item['city'] . $item['address'] . '&key=' . $sysconifg['qq_lbskey'];
    $res = file_get_contents($url);
    $json = json_decode($res, 1);
    $status = $json['status'];
    if ($status == 0) {
        $result = $json['result']['location'];
        $item['lat'] = $result['lat'];
        $item['lng'] = $result['lng'];
    }
    include $this->mymtpl('map');
} elseif ($operation == 'myset') {
    if ($_W['isajax']) {
        if (empty($_GPC['realname'])) {
            show_json(0, '请输入真实姓名');
        }
        if (empty($_GPC['nickname'])) {
            show_json(0, '请输入昵称');
        }
        $data = array('realname' => $_GPC['realname'], 'nickname' => $_GPC['nickname'], 'avatar' => $_GPC['avatar']);
        if ($this->syscfg['memberfield'] == 1) {
            $sql = 'SELECT `f`.`field`, `f`.`id` AS `fid`, `f`.`required`, `mf`.* FROM ' . tablename('profile_fields') . ' AS `f` LEFT JOIN ' . tablename('mc_member_fields') . " AS `mf` ON `f`.`id` = `mf`.`fieldid` WHERE `mf`.`uniacid` = :uniacid and `mf`.`available`=1 ORDER BY\r\n\t\t\t`displayorder` DESC";
            $fields = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
            $k = 0;
            while (!($k >= count($fields))) {
                if (!($fields[$k]['field'] == 'realname' || $fields[$k]['field'] == 'nickname' || $fields[$k]['field'] == 'avatar' || $fields[$k]['field'] == 'mobile')) {
                    $data[$fields[$k]['field']] = $_GPC[$fields[$k]['field']];
                }
                ($k += 1) + -1;
            }
        }
        pdo_update('mc_members', $data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
        show_json(1, '保存成功');
    }
    $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:weid and uid=:uid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $item['avatar'] = empty($item['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $item['avatar'];
    $sql = 'SELECT `f`.`field`, `f`.`id` AS `fid`, `f`.`required`, `mf`.* FROM ' . tablename('profile_fields') . ' AS `f` LEFT JOIN ' . tablename('mc_member_fields') . " AS `mf` ON `f`.`id` = `mf`.`fieldid` WHERE `mf`.`uniacid` = :uniacid and `mf`.`available`=1 ORDER BY\r\n\t\t\t`displayorder` DESC";
    $fields = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
    include $this->mymtpl('myset');
} elseif ($operation == 'myrepair') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $status = $_GPC['status'];
        if (!empty($status)) {
            if ($status == 1) {
                $condition .= ' and rid=:rid and (openid=:openid or uid=:uid) and status <=' . intval($status);
            } else {
                $condition .= ' and rid=:rid and (openid=:openid or uid=:uid) and status=' . intval($status);
            }
        } else {
            $condition .= ' and rid=:rid and (openid=:openid or uid=:uid)';
        }
        $params[':rid'] = $user['rid'];
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['thumb'] = tomedia($region['thumb']);
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
            if ($list[$k]['status'] == '0' || $list[$k]['status'] == '1') {
                if (empty($list[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $list[$k]['ctime']) / 3600) - $list[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $list[$k]['reporttime']) / 3600) - ($list[$k]['reporttimes'] - 1) * 24;
                }
                if ($category['reporttime'] > 0 && !($category['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '报修处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此报修工单超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $list[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_repair') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $data[$k]['id']));
                }
            }
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['status'] = '已回复';
            } elseif ($list[$k]['status'] == 9) {
                $list[$k]['css'] = 'text-red';
                $list[$k]['status'] = '待审核';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('myrepair');
} elseif ($operation == 'mysuggest') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $status = $_GPC['status'];
        if (!empty($status)) {
            if ($status == 1) {
                $condition .= ' and rid=:rid and (openid=:openid or uid=:uid) and status <=' . intval($status);
            } else {
                $condition .= ' and rid=:rid and (openid=:openid or uid=:uid) and status=' . intval($status);
            }
        } else {
            $condition .= ' and rid=:rid and (openid=:openid or uid=:uid)';
        }
        $params[':rid'] = $user['rid'];
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['thumb'] = tomedia($region['thumb']);
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
            if ($list[$k]['status'] == '0' || $list[$k]['status'] == '1') {
                if (empty($list[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $list[$k]['ctime']) / 3600) - $list[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $list[$k]['reporttime']) / 3600) - ($list[$k]['reporttimes'] - 1) * 24;
                }
                if ($category['reporttime'] > 0 && !($category['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '投诉建议处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，投诉建议内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此投诉建议超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $list[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_suggest') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $data[$k]['id']));
                }
            }
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['status'] = '已回复';
            } elseif ($list[$k]['status'] == 9) {
                $list[$k]['css'] = 'text-red';
                $list[$k]['status'] = '待审核';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('mysuggest');
} elseif ($operation == 'repair') {
    $mcurr = 'steward';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and content like "%' . $_GPC['keyword'] . '%"';
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $user_region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        if (!($user_region['isrepairdisp'] == 1)) {
            if ($_GPC['ismanage'] == 0) {
                $condition .= ' and uid=' . $_W['member']['uid'];
            }
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repair') . ' where status<9 and ' . $condition, $params);
        $condition .= ' order by status, ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repair') . ' where status<9 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
            if ($region['finishdays'] > 0 && $list[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $list[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update('rhinfo_zyxq_repair', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['status'] = 8;
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的报修工单已自动结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给服务人员做个评价吧，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'team'));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($list[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                }
            }
            if ($list[$k]['status'] == '0' || $list[$k]['status'] == '1') {
                if (empty($list[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $list[$k]['ctime']) / 3600) - $list[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $list[$k]['reporttime']) / 3600) - ($list[$k]['reporttimes'] - 1) * 24;
                }
                if ($category['reporttime'] > 0 && !($category['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '报修处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此报修工单超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $list[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_repair') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['id']));
                }
            }
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['bg'] = 'fui-label fui-label-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '已回复';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('repair');
} elseif ($operation == 'suggest') {
    $mcurr = 'steward';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and content like "%' . $_GPC['keyword'] . '%"';
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $user_region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        if (!($user_region['issuggestdisp'] == 1)) {
            if ($_GPC['ismanage'] == 0) {
                $condition .= ' and uid=' . $_W['member']['uid'];
            }
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_suggest') . ' where status<9 and ' . $condition, $params);
        $condition .= ' order by status, ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_suggest') . ' where status<9 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
            if ($region['finishdays'] > 0 && $list[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $list[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update('rhinfo_zyxq_suggest', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['status'] = 8;
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的投诉建议已自动结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给服务人员做个评价吧，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'team'));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($list[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                }
            }
            if ($list[$k]['status'] == '0' || $list[$k]['status'] == '1') {
                if (empty($list[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $list[$k]['ctime']) / 3600) - $list[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $list[$k]['reporttime']) / 3600) - ($list[$k]['reporttimes'] - 1) * 24;
                }
                if ($category['reporttime'] > 0 && !($category['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '投诉建议处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，投诉建议内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此投诉建议超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $list[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_suggest') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['id']));
                }
            }
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['bg'] = 'fui-label fui-label-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '已回复';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('suggest');
} elseif ($operation == 'mycar') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_mycar') . ' where weid = :weid and (openid = :openid or uid=:uid)';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    include $this->mymtpl('mycar');
} elseif ($operation == 'addcar') {
    if ($_W['isajax']) {
        if (empty($_GPC['carno'])) {
            show_json(0, '车牌不能为空');
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and (openid=:openid or uid=:uid) and carno=:carno';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':carno' => $_GPC['carno']));
        if ($total > 0) {
            show_json(0, '车牌已经绑定');
        }
        $data = array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'carno' => $_GPC['carno'], 'checkdate' => $_GPC['checkdate'], 'safeno' => $_GPC['safeno'], 'remark' => $_GPC['remark'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_mycar', $data);
        $id = pdo_insertid();
        if ($id) {
            show_json(1, '添加成功');
        } else {
            show_json(0, '添加失败');
        }
    }
    include $this->mymtpl('addcar');
} elseif ($operation == 'editcar') {
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        if (empty($_GPC['carno'])) {
            show_json(0, '车牌不能为空');
        }
        $data = array('carno' => $_GPC['carno'], 'checkdate' => $_GPC['checkdate'], 'safeno' => $_GPC['safeno'], 'remark' => $_GPC['remark']);
        $res = pdo_update('rhinfo_zyxq_mycar', $data, array('weid' => $_W['uniacid'], 'id' => $id));
        if ($res) {
            show_json(1, '更新成功');
        } else {
            show_json(0, '更新失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $plate = array();
    $strlen = mb_strlen($item['carno'], 'utf-8');
    $i = 0;
    while (!($i >= $strlen)) {
        $plate[] = mb_substr($item['carno'], $i, 1, 'utf-8');
        ($i += 1) + -1;
    }
    include $this->mymtpl('editcar');
} elseif ($operation == 'delcar') {
    $id = intval($_GPC['id']);
    $res = pdo_delete('rhinfo_zyxq_mycar', array('id' => $id, 'weid' => $_W['uniacid']), 'AND');
    if ($res) {
        show_json(1);
    }
    show_json(0, '删除失败');
} elseif ($operation == 'carbill') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and status=1';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':cid' => $_GPC['cid']));
    $tempbillid = array();
    $k = 0;
    while (!($k >= count($list))) {
        array_push($tempbillid, $list[$k]['id']);
        ($k += 1) + -1;
    }
    $billid = implode(',', $tempbillid);
    $sql = 'select sum(fee-payfee) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and status=1';
    $totalfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':cid' => $_GPC['cid']));
    include $this->mymtpl('carbill');
} elseif ($operation == 'hiscarbill') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and status>1';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':cid' => $_GPC['cid']));
    $sql = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and status>1';
    $totalfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':cid' => $_GPC['cid']));
    include $this->mymtpl('hiscarbill');
} elseif ($operation == 'scanopen') {
    $id = intval($_GPC['id']);
    $lotid = intval($_GPC['lotid']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkinglot') . ' where status=1 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $lotid));
    if (!empty($item)) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkingio') . ' where status=1 and weid = :weid and id=:id and lotid=:lotid ';
        $inout = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id, ':lotid' => $lotid));
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_parkingiolog') . ' where status=0 and io=1 and weid = :weid and lotid=:lotid and uid=:uid ';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid, ':uid' => $_W['member']['uid']));
        if ($inout['io'] == 1) {
            if ($total > 0) {
                $this->mymsg('error', '开闸失败', '您已经在停车场', 'close');
                exit(0);
            }
        } elseif ($inout['io'] == 2) {
            if (!($total > 0)) {
                $this->mymsg('error', '开闸失败', '您不在停车场', 'close');
                exit(0);
            }
        } else {
            $this->mymsg('error', '开闸失败', '非法操作', 'close');
            exit(0);
        }
        if ($item['parktype'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid ';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
            $item['pc_type'] = !empty($item['pc_type']) ? $item['pc_type'] : $region['pc_type'];
            if ($item['pc_type'] == 3) {
                if ($inout['io'] == 1) {
                    $set = array('url' => 'app/upOpenCmd.aspx', 'parkno' => $item['pc_plotid'], 'secret' => $item['pc_secret']);
                    $params = array('CarNo' => sprintf('%08d', $_W['member']['uid']), 'CarType' => '临时卡C', 'MachNo' => $inout['pc_ioid'], 'memo' => $_W['member']['nickname'] . $_W['member']['mobile']);
                    $res = etpcar_http_post($set, $params);
                    $res = $res['ReMsg'];
                    if ($res['ErrNo'] == '0000') {
                        $this->parkingiolog($lotid, $id, $inout['io'], array('code' => 0, 'msg' => '开闸成功'));
                        $this->mymsg('success', '开闸成功', '', 'close');
                    } else {
                        $this->mymsg('error', '开闸失败', $res['MsgStr'], 'close');
                    }
                    exit(0);
                } elseif ($inout['io'] == 2) {
                    $set = array('url' => 'app/getTmpFee.aspx', 'parkno' => $item['pc_plotid'], 'secret' => $item['pc_secret']);
                    $params = array('CarNo' => sprintf('%08d', $_W['member']['uid']), 'ParkSize' => 'BIG');
                    $res = etpcar_http_post($set, $params);
                    $order = $res;
                    $res = $res['ReMsg'];
                    if ($res['ErrNo'] == '0000') {
                        $timediff = TIMESTAMP - strtotime($order['InTime']);
                        $mytime = array();
                        $mytime['intime'] = strtotime($order['InTime']);
                        $mytime['days'] = intval($timediff / 86400);
                        $mytime['hours'] = intval($timediff % 86400 / 3600);
                        $mytime['minutes'] = intval($timediff % 86400 % 3600 / 60);
                        $stopfee = $order['MustFee'] / 100;
                        if ($stopfee > 0) {
                            include $this->mymtpl('parkingpay');
                        } else {
                            $set = array('url' => 'app/upOpenCmd.aspx', 'parkno' => $item['pc_plotid'], 'secret' => $item['pc_secret']);
                            $params = array('CarNo' => sprintf('%08d', $_W['member']['uid']), 'CarType' => '临时卡C', 'MachNo' => $inout['pc_ioid'], 'memo' => $_W['member']['nickname'] . $_W['member']['mobile']);
                            $res = etpcar_http_post($set, $params);
                            $res = $res['ReMsg'];
                            if ($res['ErrNo'] == '0000') {
                                pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                                $this->parkingiolog($lotid, $id, $inout['io'], array('code' => 0, 'msg' => '开闸成功'));
                                $this->mymsg('success', '开闸成功', '', 'close');
                            } else {
                                $this->parkingiolog($lotid, $id, $inout['io'], array('code' => 1, 'msg' => $res['MsgStr']));
                                $this->mymsg('error', '开闸失败', $res['MsgStr'], 'close');
                            }
                        }
                    } else {
                        $this->mymsg('error', '温馨提示', '操作异常：' . $res['ErrMsg'], 'close');
                    }
                    exit(0);
                }
            }
        }
        load()->model('mc');
        $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
        $behavior = $setting['creditbehaviors'];
        $creditnames = $setting['creditnames'];
        $credits = mc_credit_fetch($_W['member']['uid'], '*');
        if ($item['lid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and tid=0 and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['lid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            if ($total > 0) {
                if ($item['ischarge'] == 1) {
                    if ($inout['io'] == 1) {
                        if (empty($item['paymethod']) && (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $item['dayfee']))) {
                            $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => empty($credits[$behavior['currency']]) ? $item['dayfee'] : $item['dayfee'] - $credits[$behavior['currency']]));
                            $this->mymsg('error', '开闸失败', '余额不足', $direct_url);
                        } else {
                            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                            if ($res['code'] == '0') {
                                $this->parkingiolog($lotid, $id, $inout['io'], $res);
                                $this->mymsg('success', '开闸成功', '', 'close');
                            } else {
                                $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                            }
                            exit(0);
                        }
                    } else {
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and pid=:pid and  rid = :rid and parklotid=:lotid and carno=:uid and category=2 order by id desc';
                        $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], 'lotid' => $item['id'], ':uid' => $_W['member']['uid']));
                        if (!empty($monthcar)) {
                            if (!(TIMESTAMP > $monthcar['endtime'])) {
                                $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                                if ($res['code'] == '0') {
                                    pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                                    $this->parkingiolog($lotid, $id, $inout['io'], $res);
                                    $this->mymsg('success', '开闸成功', '', 'close');
                                } else {
                                    $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                                }
                                exit(0);
                            }
                        }
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingiolog') . ' where weid=:weid and lotid=:lotid and io=1 and status=0 and uid=:uid';
                        $myin = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid, ':uid' => $_W['member']['uid']));
                        $timediff = TIMESTAMP - $myin['ctime'];
                        $mytime = array();
                        $mytime['intime'] = $myin['ctime'];
                        $mytime['days'] = intval($timediff / 86400);
                        $mytime['hours'] = intval($timediff % 86400 / 3600);
                        $mytime['minutes'] = intval($timediff % 86400 % 3600 / 60);
                        $mystoptime = round($timediff / 3600, 2);
                        $item['minute'] = empty($item['minute']) ? 1 : $item['minute'];
                        if ($item['minute'] > 0 && !(intval($timediff / 60) > $item['minute'])) {
                            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                            if ($res['code'] == '0') {
                                pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                                $this->mymsg('success', '开闸成功', '', 'close');
                            } else {
                                $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                            }
                            exit(0);
                        }
                        $price = $item['price'];
                        $stopstart = date('H', $myin['ctime']);
                        $stopend = date('H', TIMESTAMP);
                        $istoday = 0;
                        if (date('Y-m-d', $myin['ctime']) == date('Y-m-d')) {
                            $istoday = 1;
                        }
                        if ($item['pricerule'] == 1) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($stopstart >= $pricerules[$k]['starttime'] && !($stopstart > $pricerules[$k]['endtime'])) {
                                    $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                    break;
                                }
                                ($k += 1) + -1;
                            }
                        } elseif ($item['pricerule'] == 2) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($mystoptime >= $pricerules[$k]['starttime'] && !($mystoptime > $pricerules[$k]['endtime'])) {
                                    $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                    break;
                                }
                                ($k += 1) + -1;
                            }
                        } elseif ($item['pricerule'] == 3) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $stopprice = 0;
                            if ($istoday == 1) {
                                $k = 0;
                                while (!($k >= count($pricerules))) {
                                    if ($stopstart >= $pricerules[$k]['starttime']) {
                                        if (!($stopstart > $pricerules[$k]['endtime'])) {
                                            $stopprice += $pricerules[$k]['price'];
                                        }
                                    } elseif ($stopend > $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                    ($k += 1) + -1;
                                }
                            }
                            if ($istoday == 0) {
                                $k = 0;
                                while (!($k >= count($pricerules))) {
                                    if (!($stopend > $pricerules[$k]['endtime'])) {
                                        if ($stopend >= $pricerules[$k]['starttime']) {
                                            $stopprice += $pricerules[$k]['price'];
                                        }
                                    } elseif ($stopend > $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                    ($k += 1) + -1;
                                }
                            }
                        }
                        if ($item['unit'] == 1) {
                            $stoptime = round($timediff / 3600, 2);
                            $stoptime -= $mytime['days'] * 24;
                        } else {
                            $stoptime = intval($timediff / 60);
                            $stoptime -= $mytime['days'] * 24 * 60;
                        }
                        if ($item['pricerule'] == 3) {
                            $stopfee = $stopprice;
                        } else {
                            $stopfee = $stoptime * $price * $item['qty'];
                        }
                        if ($mytime['days'] > 0) {
                            $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                            $stopfee += $mytime['days'] * $item['dayfee'];
                        } else {
                            $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                        }
                        if ($item['getfee'] == 1) {
                            $stopfee = round($stopfee, 0);
                        } elseif ($item['getfee'] == 2) {
                            $stopfee = !(intval($stopfee) >= $stopfee) ? intval($stopfee) + 1 : intval($stopfee);
                        } elseif ($item['getfee'] == 3) {
                            $stopfee = intval($stopfee);
                        }
                        if ($item['paymethod'] == 1 && $stopfee > 0) {
                            include $this->mymtpl('parkingpay');
                            exit(0);
                        }
                        if ($credits[$behavior['currency']] >= $stopfee) {
                            $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                            $crediturl = $this->my_mobileurl($crediturl);
                            $res = mc_credit_update($_W['member']['uid'], 'credit2', 0 - $stopfee, array(0, $item['title'] . '-支付开闸', 'rhinfo_zyxq'));
                            if ($res) {
                                mc_notice_credit2($_W['openid'], $_W['member']['uid'], $stopfee, 0, $item['title'] . '-支付开闸', $crediturl, '谢谢支持，点击查看详情');
                            }
                        } else {
                            $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => $stopfee - $credits[$behavior['currency']]));
                            $this->mymsg('error', '开闸失败', '余额不足', $direct_url);
                        }
                        $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                        $this->parkingiolog($lotid, $id, $inout['io'], $res);
                        if ($res['code'] == '0') {
                            pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                            $this->mymsg('success', '开闸成功', '', 'close');
                        } else {
                            $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                        }
                        exit(0);
                    }
                } else {
                    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                    $this->parkingiolog($lotid, $id, $inout['io'], $res);
                    if ($res['code'] == '0') {
                        if ($inout['io'] == 2) {
                            pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                        }
                        $this->mymsg('success', '开闸成功', '', 'close');
                    } else {
                        $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                    }
                    exit(0);
                }
            } else {
                $this->mymsg('error', '开闸失败', '非法操作', 'close');
                exit(0);
            }
        } else {
            if ($item['ischarge'] == 1) {
                if ($inout['io'] == 1) {
                    if (empty($item['paymethod']) && (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $item['dayfee']))) {
                        $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => empty($credits[$behavior['currency']]) ? $item['dayfee'] : $item['dayfee'] - $credits[$behavior['currency']]));
                        $this->mymsg('error', '开闸失败', '余额不足', $direct_url);
                    } else {
                        $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                        if ($res['code'] == '0') {
                            $this->parkingiolog($lotid, $id, $inout['io'], $res);
                            $this->mymsg('success', '开闸成功', '', 'close');
                        } else {
                            $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                        }
                    }
                    exit(0);
                } else {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and pid=:pid and  rid = :rid and parklotid=:lotid and carno=:uid and category=2 order by id desc';
                    $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], 'lotid' => $item['id'], ':uid' => $_W['member']['uid']));
                    if (!empty($monthcar)) {
                        if (!(TIMESTAMP > $monthcar['endtime'])) {
                            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                            if ($res['code'] == '0') {
                                pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                                $this->parkingiolog($lotid, $id, $inout['io'], $res);
                                $this->mymsg('success', '开闸成功', '', 'close');
                            } else {
                                $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                            }
                            exit(0);
                        }
                    }
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingiolog') . ' where weid=:weid and lotid=:lotid and io=1 and status=0 and uid=:uid';
                    $myin = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid, ':uid' => $_W['member']['uid']));
                    $timediff = TIMESTAMP - $myin['ctime'];
                    $mytime = array();
                    $mytime['intime'] = $myin['ctime'];
                    $mytime['days'] = intval($timediff / 86400);
                    $mytime['hours'] = intval($timediff % 86400 / 3600);
                    $mytime['minutes'] = intval($timediff % 86400 % 3600 / 60);
                    $item['minute'] = empty($item['minute']) ? 1 : $item['minute'];
                    if ($item['minute'] > 0 && !(intval($timediff / 60) > $item['minute'])) {
                        $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                        $this->parkingiolog($lotid, $id, $inout['io'], $res);
                        if ($res['code'] == '0') {
                            pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                            $this->mymsg('success', '开闸成功', '', 'close');
                        } else {
                            $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                        }
                        exit(0);
                    }
                    $mystoptime = round($timediff / 3600, 2);
                    $price = $item['price'];
                    $stopstart = date('H', $myin['ctime']);
                    $stopend = date('H', TIMESTAMP);
                    $istoday = 0;
                    if (date('Y-m-d', $myin['ctime']) == date('Y-m-d')) {
                        $istoday = 1;
                    }
                    if ($item['pricerule'] == 1) {
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                        $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                        $k = 0;
                        while (!($k >= count($pricerules))) {
                            if ($stopstart >= $pricerules[$k]['starttime'] && !($stopstart > $pricerules[$k]['endtime'])) {
                                $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                break;
                            }
                            ($k += 1) + -1;
                        }
                    } elseif ($item['pricerule'] == 2) {
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                        $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                        $k = 0;
                        while (!($k >= count($pricerules))) {
                            if ($mystoptime >= $pricerules[$k]['starttime'] && !($mystoptime > $pricerules[$k]['endtime'])) {
                                $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                break;
                            }
                            ($k += 1) + -1;
                        }
                    } elseif ($item['pricerule'] == 3) {
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                        $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                        $stopprice = 0;
                        if ($istoday == 1) {
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($stopstart >= $pricerules[$k]['starttime']) {
                                    if (!($stopstart > $pricerules[$k]['endtime'])) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                } elseif ($stopend > $pricerules[$k]['starttime']) {
                                    $stopprice += $pricerules[$k]['price'];
                                }
                                ($k += 1) + -1;
                            }
                        }
                        if ($istoday == 0) {
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if (!($stopend > $pricerules[$k]['endtime'])) {
                                    if ($stopend >= $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                } elseif ($stopend > $pricerules[$k]['starttime']) {
                                    $stopprice += $pricerules[$k]['price'];
                                }
                                ($k += 1) + -1;
                            }
                        }
                    }
                    if ($item['unit'] == 1) {
                        $stoptime = round($timediff / 3600, 2);
                        $stoptime -= $mytime['days'] * 24;
                    } else {
                        $stoptime = intval($timediff / 60);
                        $stoptime -= $mytime['days'] * 24 * 60;
                    }
                    if ($item['pricerule'] == 3) {
                        $stopfee = $stopprice;
                    } else {
                        $stopfee = $stoptime * $price * $item['qty'];
                    }
                    if ($mytime['days'] > 0) {
                        $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                        $stopfee += $mytime['days'] * $item['dayfee'];
                    } else {
                        $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                    }
                    if ($item['getfee'] == 1) {
                        $stopfee = round($stopfee, 0);
                    } elseif ($item['getfee'] == 2) {
                        $stopfee = !(intval($stopfee) >= $stopfee) ? intval($stopfee) + 1 : intval($stopfee);
                    } elseif ($item['getfee'] == 3) {
                        $stopfee = intval($stopfee);
                    }
                    if ($item['paymethod'] == 1 && $stopfee > 0) {
                        include $this->mymtpl('parkingpay');
                        exit(0);
                    }
                    if ($credits[$behavior['currency']] >= $stopfee) {
                        $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                        $crediturl = $this->my_mobileurl($crediturl);
                        $res = mc_credit_update($_W['member']['uid'], 'credit2', 0 - $stopfee, array(0, $item['title'] . '-支付开闸', 'rhinfo_zyxq'));
                        if ($res) {
                            mc_notice_credit2($_W['openid'], $_W['member']['uid'], $stopfee, 0, $item['title'] . '-支付开闸', $crediturl, '谢谢支持，点击查看详情');
                        }
                    } else {
                        $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => $stopfee - $credits[$behavior['currency']]));
                        $this->mymsg('error', '开闸失败', '余额不足', $direct_url);
                        exit(0);
                    }
                    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                    $this->parkingiolog($lotid, $id, $inout['io'], $res);
                    if ($res['code'] == '0') {
                        pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                        $this->mymsg('success', '开闸成功', '', 'close');
                    } else {
                        $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                    }
                    exit(0);
                }
            } else {
                $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                $this->parkingiolog($lotid, $id, $inout['io'], $res);
                if ($res['code'] == '0') {
                    if ($inout['io'] == 2) {
                        pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                    }
                    $this->mymsg('success', '开闸成功', '', 'close');
                } else {
                    $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                }
            }
            exit(0);
        }
    } else {
        $this->mymsg('error', '开闸失败', '该闸不存在或已停用', 'close');
        exit(0);
    }
} elseif ($operation == 'scanpay') {
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'index')));
        $params = array('money' => $_GPC['money'], 'title' => '停车缴费', 'feetype' => 18, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'parkid' => $_GPC['parkid'], 'ioid' => $_GPC['ioid'], 'recordid' => $_GPC['recordid'], 'intime' => $_GPC['intime']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_single_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_single_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 0 - 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    show_json(0, '非法操作');
} elseif ($operation == 'recharge') {
    if ($_W['isajax']) {
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $params = array('money' => $_GPC['money'], 'title' => '账户充值', 'feetype' => 9, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_platform_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_platform_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $credits = mc_credit_fetch($_W['member']['uid'], '*');
    include $this->mymtpl('recharge');
} elseif ($operation == 'bannerhit') {
    if ($_W['isajax'] && !empty($_GPC['bannerid'])) {
        $data = array('weid' => $_W['uniacid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bannerid' => $_GPC['bannerid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'clicktime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_banner_statistics', $data);
        show_json(1);
    }
    show_json(0);
} elseif ($operation == 'give') {
    if ($_W['ispost']) {
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'givesubmit', 'mobile' => $_GPC['mobile'], 'credit' => $_GPC['credit'])));
        exit(0);
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $credits = mc_credit_fetch($_W['member']['uid'], '*');
    include $this->mymtpl('givemobile');
} elseif ($operation == 'checkmobile') {
    $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and mobile = :mobile';
    $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    if (empty($member)) {
        show_json(0, '未找到用户，请确认手机号是否正确');
    }
    show_json(1);
} elseif ($operation == 'givesubmit') {
    load()->model('mc');
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and uid = :uid';
        $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $_GPC['uid']));
        if (empty($member)) {
            show_json(0, '未找到用户');
        }
        $credit = $_GPC['credit'];
        $url = $this->createMobileurl('service', array('op' => 'credit1'));
        $url = str_replace('addons/rhinfo_zyxq/', '', $this->my_mobileurl($url));
        $res_from = mc_credit_update($_W['member']['uid'], 'credit1', 0 - $credit, array(0, '赠送' . $member['nickname'] . '积分', 'rhinfo_zyxq'));
        if ($res_from) {
            $res_to = mc_credit_update($_GPC['uid'], 'credit1', $credit, array(0, '获得' . $_W['member']['nickname'] . '赠送积分', 'rhinfo_zyxq'));
            if ($res_to) {
                $fan = mc_fansinfo($_GPC['uid'], $_W['acid'], $_W['uniacid']);
                mc_notice_credit1($fan['openid'], $_GPC['uid'], $credit, '获得' . $_W['member']['nickname'] . '赠送积分', $url, '点击查看详情');
            }
            mc_notice_credit1($_W['openid'], $_W['member']['uid'], $credit, '赠送' . $member['nickname'] . '积分', $url, '点击查看详情');
        }
        show_json(1, '赠送成功');
    }
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $credits = mc_credit_fetch($_W['member']['uid'], '*');
    $sql = 'select * from ' . tablename('mc_members') . ' where uniacid = :uniacid and mobile = :mobile';
    $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $_GPC['mobile']));
    include $this->mymtpl('creditgive');
} elseif ($operation == 'smsrecharge') {
    if ($_W['isajax']) {
        if ($this->syscfg['smsprice'] > 0) {
            $fee = $_GPC['smsqty'] * $this->syscfg['smsprice'];
        } else {
            show_json(0, '平台未设定价格');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('manage', array('op' => 'index')));
        $params = array('money' => $fee, 'title' => '短信充值', 'feetype' => 6, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'rid' => $_GPC['rid'], 'smsqty' => $_GPC['smsqty']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_platform_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_platform_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    if (!($this->syscfg['smsprice'] > 0)) {
        $this->mymsg('error', '温馨提示', '平台暂未开启充值服务，可直接使用短信.', 'close');
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('payment'));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    include $this->mymtpl('smsrecharge');
} elseif ($operation == 'prorecharge') {
    if ($_W['isajax']) {
        if (empty($_GPC['pid'])) {
            show_json(0, '未找到物业公司');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id = :pid';
        $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid']));
        $fee = $property['yearprice'];
        if (!($fee >= 0)) {
            show_json(0, '未设续费价格');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('manage', array('op' => 'index')));
        $params = array('money' => $fee, 'title' => '物业续费', 'feetype' => 12, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'pid' => $_GPC['pid'], 'yearprice' => $_GPC['yearprice']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_platform_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_platform_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('payment'));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id = :pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid']));
    include $this->mymtpl('prorecharge');
} elseif ($operation == 'smsstore') {
    if ($_W['isajax']) {
        if ($this->syscfg['smsprice'] > 0) {
            $fee = $_GPC['smsqty'] * $this->syscfg['smsprice'];
        } else {
            show_json(0, '平台未设定价格');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('express', array('op' => 'mindex', 'sid' => $_GPC['sid'])));
        $params = array('money' => $fee, 'title' => '短信充值', 'feetype' => 13, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'sid' => $_GPC['sid'], 'smsqty' => $_GPC['smsqty']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_platform_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_platform_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    if (!($this->syscfg['smsprice'] > 0)) {
        $this->mymsg('error', '温馨提示', '平台暂未开启充值服务，可直接使用短信.', 'close');
    }
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('payment'));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $_GPC['sid']));
    include $this->mymtpl('smsstore');
} elseif ($operation == 'smsexpress') {
    if ($_W['isajax']) {
        if ($this->syscfg['smsprice'] > 0) {
            $fee = $_GPC['smsqty'] * $this->syscfg['smsprice'];
        } else {
            show_json(0, '平台未设定价格');
        }
        $returl = $this->my_mobileurl($this->createMobileUrl('express', array('op' => 'eindex', 'sid' => $_GPC['sid'])));
        $params = array('money' => $fee, 'title' => '短信充值', 'feetype' => 14, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'compid' => $_GPC['compid'], 'expid' => $_GPC['expid'], 'smsqty' => $_GPC['smsqty']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_platform_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_platform_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    if (!($this->syscfg['smsprice'] > 0)) {
        $this->mymsg('error', '温馨提示', '平台暂未开启充值服务，可直接使用短信.', 'close');
    }
    $company = array();
    $person = array();
    load()->model('mc');
    $setting = uni_setting($_W['uniacid'], array('payment'));
    if (!empty($_GPC['compid'])) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id = :compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $_GPC['compid']));
    } elseif (!empty($_GPC['expid'])) {
        $sql = 'select a.*,b.title as "comptitle" from ' . tablename('rhinfo_zycj_express_person') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid=:weid and a.id = :expid';
        $person = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':expid' => $_GPC['expid']));
    } else {
        $this->mymsg('error', '温馨提示', '操作错误，参数不正确.', 'close');
    }
    include $this->mymtpl('smsexpress');
} elseif ($operation == 'credit1' || $operation == 'credit2') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT count(*) FROM ' . tablename('mc_credits_record') . ' WHERE uniacid=:uniacid and uid= :uid and credittype=:credittype';
        $total = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':credittype' => $operation));
        $sql = 'SELECT * FROM ' . tablename('mc_credits_record') . ' WHERE uniacid=:uniacid and uid= :uid and credittype=:credittype ORDER BY createtime DESC ' . $limit;
        $data = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':credittype' => $operation));
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['createtime'] = date('Y-m-d H:i', $data[$k]['createtime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $credits = mc_credit_fetch($_W['member']['uid'], '*');
    $creditnum = 0;
    $curr = 'member';
    if ($operation == 'credit1') {
        $credittitle = $creditnames[$behavior['activity']]['title'];
        $creditnum = empty($credits[$behavior['activity']]) ? 0 : $credits[$behavior['activity']];
    }
    if ($operation == 'credit2') {
        $credittitle = $creditnames[$behavior['currency']]['title'];
        $creditnum = empty($credits[$behavior['currency']]) ? 0 : $credits[$behavior['currency']];
    }
    include $this->mymtpl('credit');
} elseif ($operation == 'parkopen') {
    $id = intval($_GPC['id']);
    $lotid = intval($_GPC['lotid']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkinglot') . ' where status=1 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $lotid));
    if (!empty($item)) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkingio') . ' where status=1 and weid = :weid and id=:id and lotid=:lotid ';
        $inout = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id, ':lotid' => $lotid));
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_parkingiolog') . ' where status=0 and io=1 and weid = :weid and lotid=:lotid and uid=:uid ';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid, ':uid' => $_W['member']['uid']));
        if ($inout['io'] == 1) {
            if ($total > 0) {
                show_json(0, '您已经在停车场');
            }
        } elseif ($inout['io'] == 2) {
            if (!($total > 0)) {
                show_json(0, '您未在停车场');
            }
        } else {
            show_json(0, '非法操作');
        }
        load()->model('mc');
        $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
        $behavior = $setting['creditbehaviors'];
        $creditnames = $setting['creditnames'];
        $credits = mc_credit_fetch($_W['member']['uid'], '*');
        if ($item['lid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and tid=0 and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['lid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            if ($total > 0) {
                if ($item['ischarge'] == 1) {
                    if ($inout['io'] == 1) {
                        if (empty($item['paymethod']) && (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $item['dayfee']))) {
                            $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => empty($credits[$behavior['currency']]) ? $item['dayfee'] : $item['dayfee'] - $credits[$behavior['currency']]));
                            show_json(0, '余额不足');
                        } else {
                            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                            if ($res['code'] == '0') {
                                $this->parkingiolog($lotid, $id, $inout['io'], $res);
                                show_json(1, '开闸成功');
                            } else {
                                show_json(0, '开闸失败');
                            }
                        }
                    } else {
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and pid=:pid and  rid = :rid and parklotid=:lotid and carno=:uid and category=2 order by id desc';
                        $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], 'lotid' => $item['id'], ':uid' => $_W['member']['uid']));
                        if (!empty($monthcar)) {
                            if (!(TIMESTAMP > $monthcar['endtime'])) {
                                $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                                if ($res['code'] == '0') {
                                    pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                                    $this->parkingiolog($lotid, $id, $inout['io'], $res);
                                    show_json(1, '开闸成功');
                                } else {
                                    show_json(0, '开闸失败');
                                }
                            }
                        }
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingiolog') . ' where weid=:weid and lotid=:lotid and io=1 and status=0 and uid=:uid';
                        $myin = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid, ':uid' => $_W['member']['uid']));
                        $timediff = TIMESTAMP - $myin['ctime'];
                        $mytime = array();
                        $mytime['intime'] = $myin['ctime'];
                        $mytime['days'] = intval($timediff / 86400);
                        $mytime['hours'] = intval($timediff % 86400 / 3600);
                        $mytime['minutes'] = intval($timediff % 86400 % 3600 / 60);
                        $item['minute'] = empty($item['minute']) ? 1 : $item['minute'];
                        if ($item['minute'] > 0 && !(intval($timediff / 60) > $item['minute'])) {
                            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                            $this->parkingiolog($lotid, $id, $inout['io'], $res);
                            if ($res['code'] == '0') {
                                pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                                $this->mymsg('success', '开闸成功', '', 'close');
                            } else {
                                $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                            }
                            exit(0);
                        }
                        $mystoptime = round($timediff / 3600, 2);
                        $price = $item['price'];
                        $stopstart = date('H', $myin['ctime']);
                        $stopend = date('H', TIMESTAMP);
                        $istoday = 0;
                        if (date('Y-m-d', $myin['ctime']) == date('Y-m-d')) {
                            $istoday = 1;
                        }
                        if ($item['pricerule'] == 1) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($stopstart >= $pricerules[$k]['starttime'] && !($stopstart > $pricerules[$k]['endtime'])) {
                                    $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                    break;
                                }
                                ($k += 1) + -1;
                            }
                        } elseif ($item['pricerule'] == 2) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $k = 0;
                            while (!($k >= count($pricerules))) {
                                if ($mystoptime >= $pricerules[$k]['starttime'] && !($mystoptime > $pricerules[$k]['endtime'])) {
                                    $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                                    break;
                                }
                                ($k += 1) + -1;
                            }
                        } elseif ($item['pricerule'] == 3) {
                            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                            $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                            $stopprice = 0;
                            if ($istoday == 1) {
                                $k = 0;
                                while (!($k >= count($pricerules))) {
                                    if ($stopstart >= $pricerules[$k]['starttime']) {
                                        if (!($stopstart > $pricerules[$k]['endtime'])) {
                                            $stopprice += $pricerules[$k]['price'];
                                        }
                                    } elseif ($stopend > $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                    ($k += 1) + -1;
                                }
                            }
                            if ($istoday == 0) {
                                $k = 0;
                                while (!($k >= count($pricerules))) {
                                    if (!($stopend > $pricerules[$k]['endtime'])) {
                                        if ($stopend >= $pricerules[$k]['starttime']) {
                                            $stopprice += $pricerules[$k]['price'];
                                        }
                                    } elseif ($stopend > $pricerules[$k]['starttime']) {
                                        $stopprice += $pricerules[$k]['price'];
                                    }
                                    ($k += 1) + -1;
                                }
                            }
                        }
                        if ($item['unit'] == 1) {
                            $stoptime = round($timediff / 3600, 2);
                            $stoptime -= $mytime['days'] * 24;
                        } else {
                            $stoptime = intval($timediff / 60);
                            $stoptime -= $mytime['days'] * 24 * 60;
                        }
                        if ($item['pricerule'] == 3) {
                            $stopfee = $stopprice;
                        } else {
                            $stopfee = $stoptime * $price * $item['qty'];
                        }
                        if ($mytime['days'] > 0) {
                            $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                            $stopfee += $mytime['days'] * $item['dayfee'];
                        } else {
                            $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                        }
                        if ($item['getfee'] == 1) {
                            $stopfee = round($stopfee, 0);
                        } elseif ($item['getfee'] == 2) {
                            $stopfee = !(intval($stopfee) >= $stopfee) ? intval($stopfee) + 1 : intval($stopfee);
                        } elseif ($item['getfee'] == 3) {
                            $stopfee = intval($stopfee);
                        }
                        if ($item['paymethod'] == 1 && $stopfee > 0) {
                            $direct_url = $this->createMobileUrl($mydo, array('op' => 'scanopen', 'id' => $id, 'lotid' => $lotid));
                            show_json(11, array('msg' => '缴费放行', 'url' => $direct_url));
                        }
                        if ($credits[$behavior['currency']] >= $stopfee) {
                            $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                            $crediturl = $this->my_mobileurl($crediturl);
                            $res = mc_credit_update($_W['member']['uid'], 'credit2', 0 - $stopfee, array(0, $item['title'] . '-支付开闸', 'rhinfo_zyxq'));
                            if ($res) {
                                mc_notice_credit2($_W['openid'], $_W['member']['uid'], $stopfee, 0, $item['title'] . '-支付开闸', $crediturl, '谢谢支持，点击查看详情');
                            }
                        } else {
                            $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => $stopfee - $credits[$behavior['currency']]));
                            show_json(2, array('msg' => '余额不足', 'url' => $direct_url));
                        }
                        $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                        $this->parkingiolog($lotid, $id, $inout['io'], $res);
                        if ($res['code'] == '0') {
                            pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                            show_json(1, '开闸成功');
                        } else {
                            show_json(0, '开闸失败');
                        }
                    }
                } else {
                    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                    $this->parkingiolog($lotid, $id, $inout['io'], $res);
                    if ($res['code'] == '0') {
                        if ($inout['io'] == 2) {
                            pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                        }
                        show_json(1, '开闸成功');
                    } else {
                        show_json(0, '开闸失败');
                    }
                }
            } else {
                show_json(0, '非法操作');
            }
        } elseif ($item['ischarge'] == 1) {
            if ($inout['io'] == 1) {
                if (empty($item['paymethod']) && (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $item['dayfee']))) {
                    $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => empty($credits[$behavior['currency']]) ? $item['dayfee'] : $item['dayfee'] - $credits[$behavior['currency']]));
                    show_json(0, '余额不足');
                } else {
                    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                    if ($res['code'] == '0') {
                        $this->parkingiolog($lotid, $id, $inout['io'], $res);
                        show_json(1, '开闸成功');
                    } else {
                        show_json(0, '开闸失败');
                    }
                }
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and pid=:pid and  rid = :rid and parklotid=:lotid and carno=:uid and category=2 order by id desc';
                $monthcar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], 'lotid' => $item['id'], ':uid' => $_W['member']['uid']));
                if (!empty($monthcar)) {
                    if (!(TIMESTAMP > $monthcar['endtime'])) {
                        $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                        if ($res['code'] == '0') {
                            pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                            $this->parkingiolog($lotid, $id, $inout['io'], $res);
                            show_json(1, '开闸成功');
                        } else {
                            show_json(0, '开闸失败');
                        }
                    }
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingiolog') . ' where weid=:weid and lotid=:lotid and io=1 and status=0 and uid=:uid';
                $myin = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid, ':uid' => $_W['member']['uid']));
                $timediff = TIMESTAMP - $myin['ctime'];
                $mytime = array();
                $mytime['intime'] = $myin['ctime'];
                $mytime['days'] = intval($timediff / 86400);
                $mytime['hours'] = intval($timediff % 86400 / 3600);
                $mytime['minutes'] = intval($timediff % 86400 % 3600 / 60);
                $item['minute'] = empty($item['minute']) ? 1 : $item['minute'];
                if ($item['minute'] > 0 && !(intval($timediff / 60) > $item['minute'])) {
                    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id);
                    $this->parkingiolog($lotid, $id, $inout['io'], $res);
                    if ($res['code'] == '0') {
                        pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                        $this->mymsg('success', '开闸成功', '', 'close');
                    } else {
                        $this->mymsg('error', '开闸失败', '请联系物业', 'close');
                    }
                    exit(0);
                }
                $mystoptime = round($timediff / 3600, 2);
                $price = $item['price'];
                $stopstart = date('H', $myin['ctime']);
                $stopend = date('H', TIMESTAMP);
                $istoday = 0;
                if (date('Y-m-d', $myin['ctime']) == date('Y-m-d')) {
                    $istoday = 1;
                }
                if ($item['pricerule'] == 1) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                    $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                    $k = 0;
                    while (!($k >= count($pricerules))) {
                        if ($stopstart >= $pricerules[$k]['starttime'] && !($stopstart > $pricerules[$k]['endtime'])) {
                            $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                            break;
                        }
                        ($k += 1) + -1;
                    }
                } elseif ($item['pricerule'] == 2) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                    $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                    $k = 0;
                    while (!($k >= count($pricerules))) {
                        if ($mystoptime >= $pricerules[$k]['starttime'] && !($mystoptime > $pricerules[$k]['endtime'])) {
                            $price = $pricerules[$k]['price'] > 0 ? $pricerules[$k]['price'] : $price;
                            break;
                        }
                        ($k += 1) + -1;
                    }
                } elseif ($item['pricerule'] == 3) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
                    $pricerules = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':lotid' => $lotid));
                    $stopprice = 0;
                    if ($istoday == 1) {
                        $k = 0;
                        while (!($k >= count($pricerules))) {
                            if ($stopstart >= $pricerules[$k]['starttime']) {
                                if (!($stopstart > $pricerules[$k]['endtime'])) {
                                    $stopprice += $pricerules[$k]['price'];
                                }
                            } elseif ($stopend > $pricerules[$k]['starttime']) {
                                $stopprice += $pricerules[$k]['price'];
                            }
                            ($k += 1) + -1;
                        }
                    }
                    if ($istoday == 0) {
                        $k = 0;
                        while (!($k >= count($pricerules))) {
                            if (!($stopend > $pricerules[$k]['endtime'])) {
                                if ($stopend >= $pricerules[$k]['starttime']) {
                                    $stopprice += $pricerules[$k]['price'];
                                }
                            } elseif ($stopend > $pricerules[$k]['starttime']) {
                                $stopprice += $pricerules[$k]['price'];
                            }
                            ($k += 1) + -1;
                        }
                    }
                }
                if ($item['unit'] == 1) {
                    $stoptime = round($timediff / 3600, 2);
                    $stoptime -= $mytime['days'] * 24;
                } else {
                    $stoptime = intval($timediff / 60);
                    $stoptime -= $mytime['days'] * 24 * 60;
                }
                if ($item['pricerule'] == 3) {
                    $stopfee = $stopprice;
                } else {
                    $stopfee = $stoptime * $price * $item['qty'];
                }
                if ($mytime['days'] > 0) {
                    $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                    $stopfee += $mytime['days'] * $item['dayfee'];
                } else {
                    $stopfee = $item['dayfee'] > 0 && $stopfee > $item['dayfee'] ? $item['dayfee'] : $stopfee;
                }
                if ($item['getfee'] == 1) {
                    $stopfee = round($stopfee, 0);
                } elseif ($item['getfee'] == 2) {
                    $stopfee = !(intval($stopfee) >= $stopfee) ? intval($stopfee) + 1 : intval($stopfee);
                } elseif ($item['getfee'] == 3) {
                    $stopfee = intval($stopfee);
                }
                if ($item['paymethod'] == 1 && $stopfee > 0) {
                    $direct_url = $this->createMobileUrl($mydo, array('op' => 'scanopen', 'id' => $id, 'lotid' => $lotid));
                    show_json(11, array('msg' => '缴费放行', 'url' => $direct_url));
                }
                if ($credits[$behavior['currency']] >= $stopfee) {
                    $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                    $crediturl = $this->my_mobileurl($crediturl);
                    $res = mc_credit_update($_W['member']['uid'], 'credit2', 0 - $stopfee, array(0, $item['title'] . '-支付开闸', 'rhinfo_zyxq'));
                    if ($res) {
                        mc_notice_credit2($_W['openid'], $_W['member']['uid'], $stopfee, 0, $item['title'] . '-支付开闸', $crediturl, '谢谢支持，点击查看详情');
                    }
                } else {
                    $direct_url = $this->createMobileUrl($mydo, array('op' => 'recharge', 'money' => $stopfee - $credits[$behavior['currency']]));
                    show_json(2, array('msg' => '余额不足', 'url' => $direct_url));
                }
                $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
                $this->parkingiolog($lotid, $id, $inout['io'], $res);
                if ($res['code'] == '0') {
                    pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                    show_json(1, '开闸成功');
                } else {
                    show_json(0, '开闸失败');
                }
            }
        } else {
            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $inout['locksn'], $inout['lockid'], 'service', $id, 1);
            $this->parkingiolog($lotid, $id, $inout['io'], $res);
            if ($res['code'] == '0') {
                if ($inout['io'] == 2) {
                    pdo_update('rhinfo_zyxq_parkingiolog', array('status' => 1), array('weid' => $_W['uniacid'], 'lotid' => $lotid, 'io' => 1, 'uid' => $_W['member']['uid']));
                }
                show_json(1, '开闸成功');
            } else {
                show_json(0, '开闸失败');
            }
        }
    } else {
        show_json(0, '该闸不存在或已停用');
    }
} elseif ($operation == 'qrcode') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkingio') . ' where status=1 and weid = :weid and id=:id';
    $inout = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkinglot') . ' where status=1 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $inout['lotid']));
    $sql = 'select doorlock_type,thinmoo_token,mailin_appid,mailin_secret,mailin_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
    if ($item['devtype'] == 1 || $item['devtype'] == 2 || $item['devtype'] == 3) {
        if ($item['doortype'] == 5) {
            $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
            $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_smdqrc', 'device_sncode' => $inout['locksn']);
            $res = mailin_http_post($set, $post_data);
            if ($res['state'] == 1) {
                $fileurl = $this->createqrcode($res['return_data']['owner_qrc']);
                include $this->mymtpl('myqrcode');
            } else {
                $this->mymsg('error', '温馨提示', '生成二维码失败，请联系门禁管理员.', '');
            }
        } elseif ($item['doortype'] == 2) {
            $post_data = array('apiid' => $this->syscfg['bl_apiid'], 'apikey' => $this->syscfg['bl_apikey']);
            $res = Park_GetToken($this->syscfg['siteurl'], $post_data);
            if (!empty($res['access_token'])) {
                $data = array('token' => $res['access_token'], 'devid' => $inout['locksn'], 'lockid' => '01', 'type' => 1);
                $ret = Park_httpPost_face($this->syscfg['siteurl'], $data);
                if ($ret['code'] == '1' || $ret['code'] == '100102') {
                    $fileurl = $this->createqrcode('二维码内容');
                    include $this->mymtpl('myqrcode');
                } else {
                    $this->mymsg('error', '温馨提示', '生成二维码失败，请联系门禁管理员.', '');
                }
            } else {
                $this->mymsg('error', '温馨提示', '获取TOKEN失败，请联系门禁管理员.', '');
            }
        } elseif ($item['doortype'] == 6) {
            $set = array();
            $set['url'] = '/doormaster/server/devices/temp_pwd';
            $set['token'] = $region['thinmoo_token'];
            $set['op'] = 'POST';
            $data = "{\r\n\t\t\t\t \"app_account\":\"" . $user['mobile'] . "\",\r\n\t\t\t\t \"dev_sn_list\":[\"" . $inout['locksn'] . "\"],\r\n\t\t\t\t \"memo\": \"" . $user['nickname'] . "\",\r\n\t\t\t\t \"start_datetime\": \"" . date('YmdHis', TIMESTAMP) . "\", \r\n\t\t\t\t \"end_datetime\": \"" . date('YmdHis', strtotime('+ 15 minutes', TIMESTAMP)) . "\",\r\n\t\t\t\t \"pwd_type\":2,\r\n\t\t\t\t \"use_count\":50\r\n\t\t\t  }";
            $res = thinmoo_http_post($set, $data);
            if ($res['ret'] == '0') {
                $fileurl = $this->createqrcode($res['qrcode_content']);
                include $this->mymtpl('myqrcode');
            } else {
                $this->mymsg('error', '温馨提示', '生成二维码失败，请联系门禁管理员.', '');
            }
        } else {
            $this->mymsg('error', '温馨提示', '生成二维码失败，参数不正确.', '');
        }
    } else {
        $this->mymsg('error', '温馨提示', '生成二维码失败，参数未设置.', '');
    }
}
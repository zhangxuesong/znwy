<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'steward';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$mydo = 'steward';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($this->syscfg['stewarddisplay'] == 1 || empty($this->syscfg['stewarddisplay'])) {
        if ($res) {
            if ($res['arrearagelimit4']) {
                header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
                exit(0);
            }
        }
    } elseif ($res) {
        if ($res['arrearagelimit2'] || $res['arrearagelimit3']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getnotice($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
load()->model('mc');
$fans = array();
if ($operation == 'index') {
    if ($_W['isajax']) {
        $doors = array();
        $doors1 = array();
        $doors2 = array();
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
        $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
        $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        if (!empty($houses)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and status = 1 and lid=0 and bid=0 and tid=0';
            $doors1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
        }
        $k = 0;
        while (!($k >= count($houses))) {
            $sql = 'select lid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid = :pid and rid = :rid and id=:bid';
            $lid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $houses[$k]['bid']));
            $lid = empty($lid) ? 0 : $lid;
            if ($lid > 0) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => 0));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => 0, ':tid' => 0));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
            }
            ($k += 1) + -1;
        }
        $doors = array_merge($doors1, $doors);
        if (!empty($doors)) {
            $doors = multi_array_unique($doors);
        }
        if ($_GPC['latedoor'] == '1') {
            $lat = floatval($_GPC['lat']);
            $lng = floatval($_GPC['lng']);
            $k = 0;
            while (!($k >= count($doors))) {
                if ($lat != 0 && $lng != 0 && !empty($doors[$k]['lat']) && !empty($doors[$k]['lng'])) {
                    $distance = GetDistance($lat, $lng, $doors[$k]['lat'], $doors[$k]['lng'], 2);
                    $doors[$k]['distance'] = $distance;
                } else {
                    $doors[$k]['distance'] = 100000;
                }
                ($k += 1) + -1;
            }
            $data = multi_array_sort($doors, 'distance');
            if (!empty($data)) {
                $latelydoor = $data[0]['id'];
            } else {
                $latelydoor = 0;
            }
            show_json($latelydoor);
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id = :pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid']));
    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and t.status = 1 and c.tplid="1" order by displayorder desc,id asc limit 5';
    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and pid = :pid and rid = :rid and status > 0 order by id desc limit 5';
    $notices = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $user['rid']));
    $k = 0;
    while (!($k >= count($teams))) {
        if (!empty($teams[$k]['avatar'])) {
            $teams[$k]['avatar'] = tomedia($teams[$k]['avatar']);
        } elseif ($teams[$k]['openid'] || $teams[$k]['uid']) {
            $fans = array();
            $teams[$k]['openid'] = empty($teams[$k]['openid']) ? $teams[$k]['uid'] : $teams[$k]['openid'];
            $fans = mc_fansinfo($teams[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $teams[$k]['avatar'] = $fans['avatar'];
        } else {
            $teams[$k]['avatar'] = MODULE_URL . 'static/mobile/images/head.jpg';
        }
        ($k += 1) + -1;
    }
    $level = 0;
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region_comment') . ' where weid=:weid and rid=:rid and FROM_UNIXTIME(ctime, \'%Y%m\') = date_format(curdate(),\'%Y%m\')';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    if ($total > 0) {
        $sql = 'select sum(stars) from ' . tablename('rhinfo_zyxq_region_comment') . ' where weid=:weid and rid=:rid ';
        $stars = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
        $level = $stars / $total;
    }
    if ($this->syscfg['stewarddisplay'] == 1 || empty($this->syscfg['stewarddisplay'])) {
        include $this->mymtpl('index');
    } else {
        $status = 2;
        if ($this->syscfg['stewarddisplay'] == 4) {
            $status = 1;
        }
        include $this->mymtpl('rindex');
    }
} elseif ($operation == 'opendoor') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and status = 1 and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (empty($item)) {
        show_json(1, '设备停用');
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
    if ($region['doordays'] > 0 && $user['isbind'] == 1 && $user['otype'] == 2) {
        $doortime = !empty($user['doortime']) ? $user['doortime'] : $user['ctime'];
        $days = floor((TIMESTAMP - $doortime) / 86400);
        if ($days > $region['doordays']) {
            show_json(1, '亲，您的门禁到期了，请联系物业');
        }
    }
    $lat = $_GPC['mylat'];
    $lng = $_GPC['mylng'];
    if ($this->syscfg['locationdoor'] == 2) {
        if (!empty($this->syscfg['doorrange']) || !empty($item['doorrange'])) {
            $range = $item['doorrange'] > 0 ? $item['doorrange'] : $this->syscfg['doorrange'];
            $range = $range > 0 ? $range : 10;
        } else {
            $range = 10;
        }
        if ($lat != 0 && $lng != 0 && !empty($item['lat']) && !empty($item['lng'])) {
            $distance = GetDistance($lat, $lng, $item['lat'], $item['lng'], 2);
            if ($distance * 1000 > $range) {
                $distances = $distance * 1000;
                show_json(1, '亲，您距离门禁' . $distances . '米');
            }
        } else {
            show_json(1, '正在获取位置...');
        }
    }
    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $item['locksn'], $item['lockid'], 'opendoor', $id, 1);
    $this->opendoorlog($id, $res);
    if ($res['code'] == '0') {
        show_json(0);
    } else {
        show_json(1, $res['msg']);
    }
} elseif ($operation == 'doors') {
    $doors = array();
    $doors1 = array();
    $doors2 = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
    $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (!empty($houses)) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and status = 1 and lid=0 and bid=0 and tid=0';
        $doors1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    }
    $k = 0;
    while (!($k >= count($houses))) {
        $sql = 'select lid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid = :pid and rid = :rid and id=:bid';
        $lid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $houses[$k]['bid']));
        $lid = empty($lid) ? 0 : $lid;
        if ($lid > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => 0, ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        }
        ($k += 1) + -1;
    }
    $doors = array_merge($doors1, $doors);
    if (!empty($doors)) {
        $doors = multi_array_unique($doors);
    }
    $k = 0;
    while (!($k >= count($doors))) {
        if ($doors[$k]['devtype'] == 1 || $doors[$k]['devtype'] == 2) {
            if ($region_item['doorlock_type'] == 1) {
                $set = array();
                $set['url'] = '/doormaster/server/remote_control';
                $set['token'] = $region_item['thinmoo_token'];
                $set['op'] = 'GET';
                $data = "{\r\n\t\t\t\t\t\"devsn_list\":[\"" . $doors[$k]['locksn'] . "\"]\r\n\t\t\t\t  }";
                $res = thinmoo_http_post($set, $data);
                if ($res['ret'] == '0') {
                    $door_data = $res['data'];
                    $lock_data = $door_data[$doors[$k]['locksn']];
                    if ($lock_data['dev_status'] == 'online') {
                        $doors[$k]['isonline'] = '1';
                    } else {
                        $doors[$k]['isonline'] = '2';
                    }
                } else {
                    $doors[$k]['isonline'] = '2';
                }
            }
        } else {
            $res = $this->devstatus($doors[$k]['doortype'], $doors[$k]['locksn']);
            if ($res['code'] == '0') {
                $doors[$k]['isonline'] = '1';
            } else {
                $doors[$k]['isonline'] = '2';
            }
        }
        if ($doors[$k]['doorcate']) {
            if ($doors[$k]['doorcate'] == 1) {
                $doors[$k]['image'] = $region_item['doorimage'] ? tomedia($region_item['doorimage']) : MODULE_URL . 'static/mobile/images/door.png';
            } elseif ($doors[$k]['doorcate'] == 2) {
                $doors[$k]['image'] = $region_item['locationimage'] ? tomedia($region_item['locationimage']) : MODULE_URL . 'static/mobile/images/location.png';
            } elseif ($doors[$k]['doorcate'] == 3) {
                $doors[$k]['image'] = $region_item['unitimage'] ? tomedia($region_item['unitimage']) : MODULE_URL . 'static/mobile/images/unit.png';
            } elseif ($doors[$k]['doorcate'] == 4) {
                $doors[$k]['image'] = $region_item['buildingimage'] ? tomedia($region_item['buildingimage']) : MODULE_URL . 'static/mobile/images/building.png';
            }
        } else {
            if ($doors[$k]['bid'] && $doors[$k]['tid']) {
                $doors[$k]['image'] = $region_item['unitimage'] ? tomedia($region_item['unitimage']) : MODULE_URL . 'static/mobile/images/unit.png';
            }
            if ($doors[$k]['bid'] && empty($doors[$k]['tid'])) {
                $doors[$k]['image'] = $region_item['buildingimage'] ? tomedia($region_item['buildingimage']) : MODULE_URL . 'static/mobile/images/building.png';
            }
            if (empty($doors[$k]['lid']) && empty($doors[$k]['bid']) && empty($doors[$k]['tid'])) {
                $doors[$k]['image'] = $region_item['doorimage'] ? tomedia($region_item['doorimage']) : MODULE_URL . 'static/mobile/images/door.png';
            }
            if ($doors[$k]['lid'] && empty($doors[$k]['bid']) && empty($doors[$k]['tid'])) {
                $doors[$k]['image'] = $region_item['locationimage'] ? tomedia($region_item['locationimage']) : MODULE_URL . 'static/mobile/images/location.png';
            }
        }
        ($k += 1) + -1;
    }
    $visitimage = $region_item['visitimage'] ? tomedia($region_item['visitimage']) : MODULE_URL . 'static/mobile/images/visit.png';
    include $this->template($this->mytpl('steward/doors'));
    exit(0);
} elseif ($operation == 'comment') {
    if ($_W['isajax']) {
        $data = array('rid' => $_GPC['rid'], 'comment' => $_GPC['comment'], 'stars' => $_GPC['stars'], 'weid' => $_W['uniacid'], 'uid' => $user['uid'], 'pid' => $user['pid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_region_comment', $data);
        if ($res) {
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    include $this->template($this->mytpl('steward/comment'));
} elseif ($operation == 'repair') {
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('rid' => $_GPC['rid'], 'content' => $_GPC['content'], 'cid' => $_GPC['cid'], 'images' => $images, 'weid' => $_W['uniacid'], 'uid' => $user['uid'], 'pid' => $user['pid'], 'openid' => $_W['openid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid'], 'address' => $user['address'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_repair', $data);
        $id = pdo_insertid();
        if ($res) {
            $this->send_mycustextmsg($user['rtitle'], '尊敬的' . $user['address'] . '业主及住户，您的报修我们已收到，我们会尽快安排相关人员处理，谢谢！', $_W['openid']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
            $catetpl = pdo_fetch($sql, array(':id' => $_GPC['cid'], ':weid' => $_W['uniacid']));
            if (!empty($catetpl['topcolor'])) {
                $topcolor = $catetpl['topcolor'];
            } else {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            }
            if (!empty($catetpl['textcolor'])) {
                $textcolor = $catetpl['textcolor'];
            } else {
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            }
            $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => $user['ownername'], 'color' => $textcolor), 'keyword2' => array('value' => $user['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $user['address'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
            $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $id));
            $url = $this->my_mobileurl($url);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
            if ($region['repairnotice'] == 1) {
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right1=1 and c.type=4';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid2'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            } else {
                if (!empty($this->syscfg['tplid2'])) {
                    $this->send_mysendtplnotice($catetpl['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
                } else {
                    $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $catetpl['openid']);
                }
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right11=1 and c.type=4';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid2'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            }
            $content = '<FB><center>报修通知单</center></FB>\\n';
            $content .= '房产:' . $user['address'] . '\\n';
            $content .= '报修类别:' . $catetpl['title'] . '\\n';
            $content .= '报修内容:' . $_GPC['content'] . '\\n';
            $content .= '报修时间:' . date('Y-m-d h:m') . '\\n';
            $content .= '报修人:' . $user['realname'] . '\\n';
            $content .= '<right>' . $user['rtitle'] . '</right>';
            $content1 = '<CB>报修通知单</CB><BR>';
            $content1 .= '房产:' . $user['address'] . '<BR>';
            $content1 .= '报修类别:' . $catetpl['title'] . '<BR>';
            $content1 .= '报修内容:' . $_GPC['content'] . '<BR>';
            $content1 .= '报修时间:' . date('Y-m-d h:m') . '<BR>';
            $content1 .= '报修人:' . $user['realname'] . '<BR>';
            $content1 .= '<RIGHT>' . $user['rtitle'] . '</RIGHT>';
            $this->send_print($catetpl['pid'], $catetpl['rid'], 1, urlencode($content), $content1);
            if (!empty($_W['setting']['site']['key']) && $this->syscfg['isworkersound']) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and rid=:rid and uid =0';
                $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id']));
                if (empty($secuser)) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and uid=0';
                    $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid']));
                }
                $k = 0;
                while (!($k >= count($secuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'property_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $secuser[$k]['id'], '您有新的报修消息，请尽快处理.');
                    ($k += 1) + -1;
                }
            }
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $item['id'];
        $pid = $item['pid'];
    } else {
        if (empty($user['rid'])) {
            header('Location:' . $this->createMobileurl('home', array('op' => 'list')));
            exit(0);
        }
        $rid = $user['rid'];
        $pid = $user['pid'];
    }
    if (empty($user['isbind'])) {
        $directurl = $this->createMobileurl('home', array('op' => 'list'));
        $this->mymsg('error', '温馨提示', '抱歉，您还没有绑定房产.', $directurl);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and pid=:pid and rid=:rid';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $pid, ':rid' => $rid));
    include $this->mymtpl('repair');
} elseif ($operation == 'repairreply') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
    $reply_images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
    $status = intval($_GPC['status']);
    $data = array('rid' => $id, 'content' => $_GPC['content'], 'image' => $reply_images, 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'ctime' => TIMESTAMP);
    $res = pdo_insert('rhinfo_zyxq_repair_record', $data);
    if ($res) {
        if (empty($repair['getuid'])) {
            pdo_update('rhinfo_zyxq_repair', array('getuid' => $_W['member']['uid'], 'getopenid' => $_W['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        }
        pdo_update('rhinfo_zyxq_repair', array('status' => $status, 'lasttime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'id' => $id));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $repairtitle = pdo_fetchcolumn('select title from ' . tablename('rhinfo_zyxq_category') . ' where id = :cid and weid = :weid', array(':cid' => $repair['cid'], ':weid' => $_W['uniacid']));
        $serviceuser = pdo_fetchcolumn('select realname from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and rid=:rid and openid=:openid ', array(':weid' => $_W['uniacid'], ':rid' => $repair['rid'], ':openid' => $_W['openid']));
        $serviceuser = $serviceuser ? $serviceuser : $fans['nickname'];
        $postdata = array('first' => array('value' => '尊敬的业主，您的报修已回复', 'color' => $topcolor), 'keyword1' => array('value' => $repair['address'], 'color' => $textcolor), 'keyword2' => array('value' => $repairtitle, 'color' => $textcolor), 'keyword3' => array('value' => $serviceuser, 'color' => $textcolor), 'keyword4' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '如有疑问，请尽快回复工单，谢谢！'));
        $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $id));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid3'])) {
            $this->send_mysendtplnotice($repair['openid'], $this->syscfg['tplid3'], $postdata, $url, $topcolor);
        } else {
            $this->send_mycusnewsmsg('尊敬的业主，您的报修已处理', '如有疑问，请尽快回复工单，谢谢！', $url, '', $repair['openid']);
        }
        show_json(0);
    } else {
        show_json(1, '提交失败');
    }
} elseif ($operation == 'repairfinish') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        $res = pdo_update('rhinfo_zyxq_repair', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid']));
        if ($res) {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的报修已成功结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $repair['address'] . '，报修内容：' . $repair['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给该工单处理做个评价吧，谢谢！'));
            $url = $this->createMobileurl($mydo, array('op' => 'repaircomment', 'repairid' => $id));
            $url = $this->my_mobileurl($url);
            if (!empty($this->syscfg['tplid1'])) {
                $this->send_mysendtplnotice($_W['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
} elseif ($operation == 'repairtrack') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        $reply_images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('rid' => $id, 'content' => $_GPC['content'], 'image' => $reply_images, 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_repair_record', $data);
        if ($res) {
            $this->send_mycustextmsg($user['rtitle'], '尊敬的' . $user['address'] . '业主及住户，您的报修我们已收到，我们会尽快安排相关人员处理，谢谢！', $_W['openid']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
            $catetpl = pdo_fetch($sql, array(':id' => $repair['cid'], ':weid' => $_W['uniacid']));
            if (!empty($catetpl['topcolor'])) {
                $topcolor = $catetpl['topcolor'];
            } else {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            }
            if (!empty($catetpl['textcolor'])) {
                $textcolor = $catetpl['textcolor'];
            } else {
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            }
            $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => $user['ownername'], 'color' => $textcolor), 'keyword2' => array('value' => $user['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $user['address'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
            $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $id));
            $url = $this->my_mobileurl($url);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid']));
            if ($region['repairnotice'] == 1) {
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right1=1 and c.tplid="1"';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid2'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            } elseif (!empty($this->syscfg['tplid2'])) {
                $this->send_mysendtplnotice($catetpl['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $catetpl['openid']);
            }
            $content = '<FB><center>报修通知单</center></FB>\\n';
            $content .= '房产:' . $user['address'] . '\\n';
            $content .= '报修类别:' . $catetpl['title'] . '\\n';
            $content .= '报修内容:' . $_GPC['content'] . '\\n';
            $content .= '报修时间:' . date('Y-m-d h:m') . '\\n';
            $content .= '报修人:' . $user['realname'] . '\\n';
            $content .= '<right>' . $user['rtitle'] . '</right>';
            $content1 = '<CB>报修通知单</CB><BR>';
            $content1 .= '房产:' . $user['address'] . '<BR>';
            $content1 .= '报修类别:' . $catetpl['title'] . '<BR>';
            $content1 .= '报修内容:' . $_GPC['content'] . '<BR>';
            $content1 .= '报修时间:' . date('Y-m-d h:m') . '<BR>';
            $content1 .= '报修人:' . $user['realname'] . '<BR>';
            $content1 .= '<RIGHT>' . $user['rtitle'] . '</RIGHT>';
            $this->send_print($catetpl['pid'], $catetpl['rid'], 1, urlencode($content), $content1);
            if (!empty($_W['setting']['site']['key']) && $this->syscfg['isworkersound']) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and rid=:rid and uid =0';
                $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
                if (empty($secuser)) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and uid=0';
                    $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid']));
                }
                $k = 0;
                while (!($k >= count($secuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'property_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $secuser[$k]['id'], '您有新的报修消息，请尽快处理.');
                    ($k += 1) + -1;
                }
            }
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and pid=:pid and rid=:rid';
    $cate = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $images = iunserializer($repair['images']);
    if ($repair['status'] == 0) {
        $repair['css'] = 'text-danger';
        $repair['statustext'] = '待处理';
    } elseif ($repair['status'] == 1) {
        $repair['css'] = 'text-danger';
        $repair['statustext'] = '待处理';
    } elseif ($repair['status'] == 2) {
        $repair['css'] = 'text-warning';
        $repair['statustext'] = '处理中';
    } elseif ($repair['status'] == 3) {
        $repair['css'] = 'text-green';
        $repair['statustext'] = '已处理';
    } elseif ($repair['status'] == 8) {
        $repair['css'] = 'text-default';
        $repair['statustext'] = '已结案';
    } elseif ($repair['status'] == 5) {
        $repair['css'] = 'text-warning';
        $repair['statustext'] = '已回复';
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair_record') . ' where weid=:weid and rid=:id';
    $repair_records = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $k = 0;
    while (!($k >= count($repair_records))) {
        $repair_records[$k]['images'] = iunserializer($repair_records[$k]['image']);
        $repair_records[$k]['ctime'] = timeBefore($repair_records[$k]['ctime']);
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
    $catetpl = pdo_fetch($sql, array(':id' => $repair['cid'], ':weid' => $_W['uniacid']));
    $isview = false;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and id=:hid';
    $room = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid'], ':hid' => $repair['hid']));
    $housename = $region['title'] . $building . $unit . $room;
    $member = pdo_get('rhinfo_zyxq_member', array('weid' => $_W['uniacid'], 'rid' => $repair['rid'], 'bid' => $repair['bid'], 'tid' => $repair['tid'], 'hid' => $repair['hid'], 'uid' => $repair['uid'], 'deleted' => 0), array('mobile', 'realname'));
    $sql = 'SELECT t.*,c.right1,c.right11 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4 and (t.openid = :openid or t.uid=:uid)';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $repairmethod = 0;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 6 and pid=:pid and rid=:rid';
    $replys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    if ($region['repairnotice'] == 1 && $team['right1'] == 1) {
        $repairmethod = 1;
        include $this->mymtpl('repairreply');
    } elseif ($region['repairnotice'] == 2 && ($catetpl['openid'] == $_W['openid'] || $catetpl['uid'] == $_W['member']['uid'] || $team['right11'] == 1)) {
        $repairmethod = 2;
        $sql = 'SELECT t.*,c.right1 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4';
        $repairusers = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid']));
        include $this->mymtpl('repairreply');
    } elseif ($repair['openid'] == $_W['openid'] || $repair['uid'] == $_W['member']['uid']) {
        include $this->mymtpl('repairtrack');
    } else {
        $isview = true;
        include $this->mymtpl('repairtrack');
    }
} elseif ($operation == 'repairmtrack') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and id=:hid';
    $room = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid'], ':hid' => $repair['hid']));
    $housename = $region . $building . $unit . $room;
    include $this->mymtpl('repairmtrack');
} elseif ($operation == 'repairrob') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['getuid']) {
        show_json(0, '抢单失败');
    } else {
        pdo_update('rhinfo_zyxq_repair', array('getuid' => $_W['member']['uid'], 'getopenid' => $_W['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        show_json(1, '抢单成功');
    }
} elseif ($operation == 'repairtake') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and id=:id';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['teamid']));
    if ($item['getuid']) {
        show_json(0, '派单失败');
    } else {
        pdo_update('rhinfo_zyxq_repair', array('getuid' => $team['uid'], 'getopenid' => $team['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
        $catetpl = pdo_fetch($sql, array(':id' => $item['cid'], ':weid' => $_W['uniacid']));
        if (!empty($catetpl['topcolor'])) {
            $topcolor = $catetpl['topcolor'];
        } else {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        }
        if (!empty($catetpl['textcolor'])) {
            $textcolor = $catetpl['textcolor'];
        } else {
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and uid=:uid';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid'], ':uid' => $item['uid']));
        $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => $member['realname'], 'color' => $textcolor), 'keyword2' => array('value' => $member['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'keyword4' => array('value' => $item['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
        $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $item['id']));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid2'])) {
            $this->send_mysendtplnotice($team['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
        } else {
            $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $team['openid']);
        }
        show_json(1, '派单成功');
    }
} elseif ($operation == 'suggest') {
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? serialize($_GPC['images']) : serialize(array());
        $data = array('rid' => $_GPC['rid'], 'content' => $_GPC['content'], 'cid' => $_GPC['cid'], 'images' => $images, 'weid' => $_W['uniacid'], 'uid' => $user['uid'], 'pid' => $user['pid'], 'openid' => $_W['openid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid'], 'address' => $user['address'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_suggest', $data);
        $id = pdo_insertid();
        if ($res) {
            $this->send_mycustextmsg($user['rtitle'], '尊敬的' . $user['address'] . '业主及住户，您的建议我们已收到，我们会尽快安排相关人员处理，谢谢！', $_W['openid']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
            $catetpl = pdo_fetch($sql, array(':id' => $_GPC['cid'], ':weid' => $_W['uniacid']));
            if (!empty($catetpl['topcolor'])) {
                $topcolor = $catetpl['topcolor'];
            } else {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            }
            if (!empty($catetpl['textcolor'])) {
                $textcolor = $catetpl['textcolor'];
            } else {
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            }
            $postdata = array('first' => array('value' => $user['rtitle'], 'color' => $topcolor), 'keyword1' => array('value' => $user['ownername'], 'color' => $textcolor), 'keyword2' => array('value' => $user['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $user['address'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
            $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $id));
            $url = $this->my_mobileurl($url);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
            if ($region['suggestnotice'] == 1) {
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right2=1 and c.type=4';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid5'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            } else {
                if (!empty($this->syscfg['tplid5'])) {
                    $this->send_mysendtplnotice($catetpl['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
                } else {
                    $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $catetpl['openid']);
                }
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right11=1 and c.type=4';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid5'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            }
            $content = '<FB><center>投诉建议通知单</center></FB>\\n';
            $content .= '房产:' . $user['address'] . '\\n';
            $content .= '建议类别:' . $catetpl['title'] . '\\n';
            $content .= '建议内容:' . $_GPC['content'] . '\\n';
            $content .= '建议时间:' . date('Y-m-d h:m') . '\\n';
            $content .= '建议人:' . $user['realname'] . '\\n';
            $content .= '<right>' . $user['rtitle'] . '</right>';
            $content1 = '<CB>投诉建议通知单</CB><BR>';
            $content1 .= '房产:' . $user['address'] . '<BR>';
            $content1 .= '建议类别:' . $catetpl['title'] . '<BR>';
            $content1 .= '建议内容:' . $_GPC['content'] . '<BR>';
            $content1 .= '建议时间:' . date('Y-m-d h:m') . '<BR>';
            $content1 .= '建议人:' . $user['realname'] . '<BR>';
            $content1 .= '<RIGHT>' . $user['rtitle'] . '</RIGHT>';
            $this->send_print($catetpl['pid'], $catetpl['rid'], 2, urlencode($content), $content1);
            if (!empty($_W['setting']['site']['key']) && $this->syscfg['isworkersound']) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and rid=:rid and uid =0';
                $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id']));
                if (empty($secuser)) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and uid=0';
                    $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid']));
                }
                $k = 0;
                while (!($k >= count($secuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'property_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $secuser[$k]['id'], '您有新的投诉建议，请尽快处理.');
                    ($k += 1) + -1;
                }
            }
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $item['id'];
        $pid = $item['pid'];
    } else {
        if (empty($user['rid'])) {
            header('Location:' . $this->createMobileurl('home', array('op' => 'list')));
            exit(0);
        }
        $rid = $user['rid'];
        $pid = $user['pid'];
    }
    if (empty($user['isbind'])) {
        $directurl = $this->createMobileurl('home', array('op' => 'list'));
        $this->mymsg('error', '温馨提示', '抱歉，您还没有绑定房产.', $directurl);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and pid=:pid and rid=:rid';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $pid, ':rid' => $rid));
    include $this->mymtpl('suggest');
} elseif ($operation == 'suggestreply') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where weid=:weid and id=:id';
    $suggest = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
    $reply_images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
    $status = intval($_GPC['status']);
    $data = array('sid' => $id, 'content' => $_GPC['content'], 'image' => $reply_images, 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'ctime' => TIMESTAMP);
    $res = pdo_insert('rhinfo_zyxq_suggest_record', $data);
    if ($res) {
        if (empty($suggest['getuid'])) {
            pdo_update('rhinfo_zyxq_suggest', array('getuid' => $_W['member']['uid'], 'getopenid' => $_W['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        }
        pdo_update('rhinfo_zyxq_suggest', array('status' => $status, 'lasttime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'id' => $id));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $suggesttitle = pdo_fetchcolumn('select title from ' . tablename('rhinfo_zyxq_category') . ' where id = :cid and weid = :weid', array(':cid' => $suggest['cid'], ':weid' => $_W['uniacid']));
        $serviceuser = pdo_fetchcolumn('select realname from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and rid=:rid and openid=:openid ', array(':weid' => $_W['uniacid'], ':rid' => $suggest['rid'], ':openid' => $_W['openid']));
        $serviceuser = $serviceuser ? $serviceuser : $fans['nickname'];
        $postdata = array('first' => array('value' => '尊敬的业主，您的投诉建议已回复', 'color' => $topcolor), 'keyword1' => array('value' => $suggest['address'], 'color' => $textcolor), 'keyword2' => array('value' => $suggesttitle, 'color' => $textcolor), 'keyword3' => array('value' => $suggest['content'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => $serviceuser, 'color' => $textcolor), 'remark' => array('value' => '如有疑问，请联系，谢谢！'));
        $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $id));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid4'])) {
            $this->send_mysendtplnotice($suggest['openid'], $this->syscfg['tplid4'], $postdata, $url, $topcolor);
        } else {
            $this->send_mycusnewsmsg('尊敬的业主，您的投诉建议已处理', '如有疑问，请联系服务人员，谢谢！', $url, '', $suggest['openid']);
        }
        show_json(0);
    } else {
        show_json(1, '提交失败');
    }
} elseif ($operation == 'suggesttrack') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where weid=:weid and id=:id';
    $suggest = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        $reply_images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('sid' => $id, 'content' => $_GPC['content'], 'image' => $reply_images, 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_suggest_record', $data);
        if ($res) {
            $this->send_mycustextmsg($user['rtitle'], '尊敬的' . $user['address'] . '业主及住户，您的建议我们已收到，我们会尽快安排相关人员处理，谢谢！', $_W['openid']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
            $catetpl = pdo_fetch($sql, array(':id' => $_GPC['cid'], ':weid' => $_W['uniacid']));
            if (!empty($catetpl['topcolor'])) {
                $topcolor = $catetpl['topcolor'];
            } else {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            }
            if (!empty($catetpl['textcolor'])) {
                $textcolor = $catetpl['textcolor'];
            } else {
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            }
            $postdata = array('first' => array('value' => $user['rtitle'], 'color' => $topcolor), 'keyword1' => array('value' => $user['ownername'], 'color' => $textcolor), 'keyword2' => array('value' => $user['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $user['address'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
            $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $id));
            $url = $this->my_mobileurl($url);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $suggest['rid']));
            if ($region['suggestnotice'] == 1) {
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right2=1 and c.tplid="1"';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid5'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            } elseif (!empty($this->syscfg['tplid5'])) {
                $this->send_mysendtplnotice($catetpl['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $catetpl['openid']);
            }
            $content = '<FB><center>投诉建议通知单</center></FB>\\n';
            $content .= '房产:' . $user['address'] . '\\n';
            $content .= '建议类别:' . $catetpl['title'] . '\\n';
            $content .= '建议内容:' . $_GPC['content'] . '\\n';
            $content .= '建议时间:' . date('Y-m-d h:m') . '\\n';
            $content .= '建议人:' . $user['realname'] . '\\n';
            $content .= '<right>' . $user['rtitle'] . '</right>';
            $content1 = '<CB>投诉建议通知单</CB><BR>';
            $content1 .= '房产:' . $user['address'] . '<BR>';
            $content1 .= '建议类别:' . $catetpl['title'] . '<BR>';
            $content1 .= '建议内容:' . $_GPC['content'] . '<BR>';
            $content1 .= '建议时间:' . date('Y-m-d h:m') . '<BR>';
            $content1 .= '建议人:' . $user['realname'] . '<BR>';
            $content1 .= '<RIGHT>' . $user['rtitle'] . '</RIGHT>';
            $this->send_print($catetpl['pid'], $catetpl['rid'], 2, urlencode($content), $content1);
            if (!empty($_W['setting']['site']['key']) && $this->syscfg['isworkersound']) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and rid=:rid and uid =0';
                $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid']));
                if (empty($secuser)) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and uid=0';
                    $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid']));
                }
                $k = 0;
                while (!($k >= count($secuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'property_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $secuser[$k]['id'], '您有新的投诉建议，请尽快处理.');
                    ($k += 1) + -1;
                }
            }
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and pid=:pid and rid=:rid';
    $cate = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid']));
    $images = iunserializer($suggest['images']);
    if ($suggest['status'] == 0) {
        $suggest['css'] = 'text-danger';
        $suggest['statustext'] = '待处理';
    } elseif ($suggest['status'] == 1) {
        $suggest['css'] = 'text-danger';
        $suggest['statustext'] = '待处理';
    } elseif ($suggest['status'] == 2) {
        $suggest['css'] = 'text-warning';
        $suggest['statustext'] = '处理中';
    } elseif ($suggest['status'] == 3) {
        $suggest['css'] = 'text-green';
        $suggest['statustext'] = '已处理';
    } elseif ($suggest['status'] == 8) {
        $suggest['css'] = 'text-default';
        $suggest['statustext'] = '已结案';
    } elseif ($suggest['status'] == 5) {
        $suggest['css'] = 'text-warning';
        $suggest['statustext'] = '已回复';
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest_record') . ' where weid=:weid and sid=:id';
    $suggest_records = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $k = 0;
    while (!($k >= count($suggest_records))) {
        $suggest_records[$k]['images'] = iunserializer($suggest_records[$k]['image']);
        $suggest_records[$k]['ctime'] = timeBefore($suggest_records[$k]['ctime']);
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
    $catetpl = pdo_fetch($sql, array(':id' => $suggest['cid'], ':weid' => $_W['uniacid']));
    $isview = false;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid'], ':bid' => $suggest['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid'], ':bid' => $suggest['bid'], ':tid' => $suggest['tid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and id=:hid';
    $room = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid'], ':bid' => $suggest['bid'], ':tid' => $suggest['tid'], ':hid' => $suggest['hid']));
    $housename = $region['title'] . $building . $unit . $room;
    $member = pdo_get('rhinfo_zyxq_member', array('weid' => $_W['uniacid'], 'rid' => $suggest['rid'], 'bid' => $suggest['bid'], 'tid' => $suggest['tid'], 'hid' => $suggest['hid'], 'uid' => $suggest['uid'], 'deleted' => 0), array('mobile', 'realname'));
    $sql = 'SELECT t.*,c.right2,c.right11 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4 and (t.openid = :openid or t.uid=:uid)';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $suggest['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $suggestmethod = 0;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 6 and pid=:pid and rid=:rid';
    $replys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    if ($region['suggestnotice'] == 1 && $team['right2'] == 1) {
        $suggestmethod = 1;
        include $this->mymtpl('suggestreply');
    } elseif ($catetpl['openid'] == $_W['openid'] || $catetpl['uid'] == $_W['member']['uid'] || $team['right11'] == 1) {
        $suggestmethod = 2;
        $sql = 'SELECT t.*,c.right2 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4 ';
        $suggestusers = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $suggest['rid']));
        include $this->mymtpl('suggestreply');
    } elseif ($suggest['openid'] == $_W['openid'] || $suggest['uid'] == $_W['member']['uid']) {
        include $this->mymtpl('suggesttrack');
    } else {
        $isview = true;
        include $this->mymtpl('suggesttrack');
    }
} elseif ($operation == 'suggestfinish') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where weid=:weid and id=:id';
    $suggest = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        $res = pdo_update('rhinfo_zyxq_suggest', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $suggest['rid']));
        if ($res) {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的投诉建议已成功结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $suggest['address'] . '，投诉内容：' . $suggest['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给该投诉建议处理做个评价吧，谢谢！'));
            $url = $this->createMobileurl($mydo, array('op' => 'suggestcomment', 'suggestid' => $id));
            $url = $this->my_mobileurl($url);
            if (!empty($this->syscfg['tplid1'])) {
                $this->send_mysendtplnotice($_W['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
} elseif ($operation == 'suggestmtrack') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where weid=:weid and id=:id';
    $suggest = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid'], ':bid' => $suggest['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid'], ':bid' => $suggest['bid'], ':tid' => $suggest['tid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and id=:hid';
    $room = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $suggest['pid'], ':rid' => $suggest['rid'], ':bid' => $suggest['bid'], ':tid' => $suggest['tid'], ':hid' => $suggest['hid']));
    $housename = $region . $building . $unit . $room;
    include $this->mymtpl('suggestmtrack');
} elseif ($operation == 'suggestrob') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['getuid']) {
        show_json(0, '抢单失败');
    } else {
        pdo_update('rhinfo_zyxq_suggest', array('getuid' => $_W['member']['uid'], 'getopenid' => $_W['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        show_json(1, '抢单成功');
    }
} elseif ($operation == 'suggesttake') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and id=:id';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['teamid']));
    if ($item['getuid']) {
        show_json(0, '派单失败');
    } else {
        pdo_update('rhinfo_zyxq_suggest', array('getuid' => $team['uid'], 'getopenid' => $team['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
        $catetpl = pdo_fetch($sql, array(':id' => $item['cid'], ':weid' => $_W['uniacid']));
        if (!empty($catetpl['topcolor'])) {
            $topcolor = $catetpl['topcolor'];
        } else {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        }
        if (!empty($catetpl['textcolor'])) {
            $textcolor = $catetpl['textcolor'];
        } else {
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and uid=:uid';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid'], ':uid' => $item['uid']));
        $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => $member['realname'], 'color' => $textcolor), 'keyword2' => array('value' => $member['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'keyword4' => array('value' => $item['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
        $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $item['id']));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid5'])) {
            $this->send_mysendtplnotice($team['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
        } else {
            $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $team['openid']);
        }
        show_json(1, '派单成功');
    }
} elseif ($operation == 'team') {
    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and t.status = 1 and c.tplid="1" order by displayorder desc,id asc';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
    $k = 0;
    while (!($k >= count($list))) {
        if (!empty($list[$k]['avatar'])) {
            $list[$k]['avatar'] = tomedia($list[$k]['avatar']);
        } else {
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            if (!empty($list[$k]['openid'])) {
                $fans = array();
                $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
                $list[$k]['avatar'] = $fans['avatar'];
            } else {
                $list[$k]['avatar'] = MODULE_URL . 'static/mobile/images/head.jpg';
            }
        }
        if (!empty($list[$k]['openid'])) {
            if (pdo_tableexists('messikefu_set')) {
                $sql = 'select count(*) from ' . tablename('messikefu_cservice') . ' where weid=:weid and ctype=1 and content=:openid';
                $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $list[$k]['openid']));
                if ($count > 0) {
                    $list[$k]['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $list[$k]['openid'] . '&do=chat&m=cy163_customerservice';
                } else {
                    $list[$k]['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $list[$k]['openid'] . '&do=sanchat&qudao=zhiyunwuye&m=cy163_customerservice';
                }
            }
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_team_comment') . ' where weid=:weid and teamid=:teamid and updown=1';
        $up = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':teamid' => $list[$k]['id']));
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_team_comment') . ' where weid=:weid and teamid=:teamid and updown=2';
        $down = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':teamid' => $list[$k]['id']));
        $list[$k]['up'] = $up;
        $list[$k]['down'] = $down;
        ($k += 1) + -1;
    }
    include $this->template($this->mytpl('steward/team'));
} elseif ($operation == 'teamperson') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and pid=:pid and rid=:rid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':id' => $id));
    $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
    if (empty($item['avatar'])) {
        $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
        if (!empty($item['openid'])) {
            $fans = array();
            $fans = mc_fansinfo($item['openid'], $_W['acid'], $_W['uniacid']);
            $item['avatar'] = $fans['avatar'];
        } else {
            $item['avatar'] = MODULE_URL . 'static/mobile/images/head.jpg';
        }
    }
    if (!empty($item['openid'])) {
        if (pdo_tableexists('messikefu_set')) {
            $sql = 'select count(*) from ' . tablename('messikefu_cservice') . ' where weid=:weid and ctype=1 and content=:openid';
            $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $item['openid']));
            if ($count > 0) {
                $item['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $item['openid'] . '&do=chat&m=cy163_customerservice';
            } else {
                $item['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $item['openid'] . '&do=sanchat&qudao=zhiyunwuye&m=cy163_customerservice';
            }
        }
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_team_comment') . ' where weid=:weid and teamid=:teamid and updown=1';
    $up = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':teamid' => $item['id']));
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_team_comment') . ' where weid=:weid and teamid=:teamid and updown=2';
    $down = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':teamid' => $item['id']));
    $item['up'] = $up;
    $item['down'] = $down;
    include $this->template($this->mytpl('steward/teamperson'));
} elseif ($operation == 'teamhelp') {
    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and t.status = 1 and c.tplid="1" order by displayorder desc,id asc';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
    $k = 0;
    while (!($k >= count($list))) {
        if (!empty($list[$k]['avatar'])) {
            $list[$k]['avatar'] = tomedia($list[$k]['avatar']);
        } elseif (!empty($list[$k]['openid'])) {
            $fans = array();
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
        } else {
            $list[$k]['avatar'] = MODULE_URL . 'static/mobile/images/head.jpg';
        }
        if (!empty($list[$k]['openid'])) {
            if (pdo_tableexists('messikefu_set')) {
                $sql = 'select count(*) from ' . tablename('messikefu_cservice') . ' where weid=:weid and ctype=1 and content=:openid';
                $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $list[$k]['openid']));
                if ($count > 0) {
                    $list[$k]['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $list[$k]['openid'] . '&do=chat&m=cy163_customerservice';
                } else {
                    $list[$k]['chaturl'] = $siteurl . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&toopenid=' . $list[$k]['openid'] . '&do=sanchat&qudao=zhiyunwuye&m=cy163_customerservice';
                }
            }
        }
        ($k += 1) + -1;
    }
    include $this->template($this->mytpl('steward/teamhelp'));
} elseif ($operation == 'teamcomm') {
    $type = $_GPC['type'];
    $id = intval($_GPC['id']);
    if ($type == '2') {
        $title = '投诉建议';
        $msg = '非常抱歉，给您带来了困扰';
    } else {
        $title = '为他点赞';
        $msg = '谢谢您为他点赞';
    }
    if ($_W['ispost']) {
        $data = array('pid' => $user['pid'], 'rid' => $_GPC['rid'], 'comment' => $_GPC['comment'], 'weid' => $_W['uniacid'], 'teamid' => $id, 'updown' => $type, 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_team_comment', $data);
        if ($res) {
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and pid=:pid and rid=:rid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':id' => $id));
    if (empty($item['avatar'])) {
        $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
        if ($item['openid']) {
            $fans = array();
            $fans = mc_fansinfo($item['openid'], $_W['acid'], $_W['uniacid']);
            $item['avatar'] = $fans['avatar'];
        } else {
            $item['avatar'] = MODULE_URL . 'static/mobile/images/head.jpg';
        }
    }
    include $this->template($this->mytpl('steward/teamcomm'));
} elseif ($operation == 'repaircomment') {
    if ($_W['ispost']) {
        $data = array('repairid' => $_GPC['repairid'], 'comment' => $_GPC['comment'], 'updown' => $_GPC['stars'], 'weid' => $_W['uniacid'], 'uid' => $user['uid'], 'openid' => $_W['openid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_repair_comment', $data);
        if ($res) {
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    include $this->template($this->mytpl('steward/repaircomment'));
} elseif ($operation == 'suggestcomment') {
    if ($_W['ispost']) {
        $data = array('suggestid' => $_GPC['suggestid'], 'comment' => $_GPC['comment'], 'updown' => $_GPC['stars'], 'weid' => $_W['uniacid'], 'uid' => $user['uid'], 'openid' => $_W['openid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_suggest_comment', $data);
        if ($res) {
            show_json(0);
        } else {
            show_json(1, '提交失败');
        }
    }
    include $this->template($this->mytpl('steward/suggestcomment'));
}
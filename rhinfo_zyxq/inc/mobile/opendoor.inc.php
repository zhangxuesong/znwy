<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
date_default_timezone_set('Asia/Shanghai');
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'opendoor';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$mydo = 'opendoor';
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($res) {
        if ($res['arrearagelimit4']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
if ($operation == 'index') {
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $doors = array();
        $doors1 = array();
        $doors2 = array();
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
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' limit 1';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $id = $region['id'];
    } elseif (empty($id)) {
        if ($user['rid']) {
            $id = $user['rid'];
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and (toopenid=:openid or touid=:uid) and status=1 and opentimes>0 and effetime>' . TIMESTAMP;
            $visit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            if ($visit['rid']) {
                $id = $visit['rid'];
            } else {
                header('Location:' . $this->createMobileurl('home', array('op' => 'list')));
                exit(0);
            }
        }
    }
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    $sql = 'select link,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and  btype=5 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $id));
    if (empty($banners)) {
        $sql = 'select link,thumb,0 as `pid`, 0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=5 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and pid=:pid and rid=:rid and (toopenid=:openid or touid=:uid) and status=1 and opentimes>0 and effetime>' . TIMESTAMP;
    $visit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    include $this->mymtpl('index');
} elseif ($operation == 'doors') {
    $doors = array();
    $doors1 = array();
    $doors2 = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
    $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (!empty($houses)) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and status = 1 and lid=0 and bid=0 and tid=0';
        $doors1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    }
    $k = 0;
    while (!($k >= count($houses))) {
        $sql = 'select lid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid = :rid and id=:bid';
        $lid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':bid' => $houses[$k]['bid']));
        $lid = empty($lid) ? 0 : $lid;
        if ($lid > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => 0, ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
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
    $faces = 0;
    $k = 0;
    while (!($k >= count($doors))) {
        $doors[$k]['qrcode'] = '0';
        if ($doors[$k]['devtype'] == 1) {
            $doors[$k]['isonline'] = '1';
            $doors[$k]['qrcode'] = '1';
        } elseif ($doors[$k]['devtype'] == 2 || $doors[$k]['devtype'] == 3) {
            if ($doors[$k]['doortype'] == 5) {
                $doors[$k]['isonline'] = '1';
            }
            if ($doors[$k]['doortype'] == 6) {
                $set = array();
                $set['url'] = '/doormaster/server/remote_control';
                $set['token'] = $region_item['thinmoo_token'];
                $set['op'] = 'GET';
                $dev_data = "{\r\n\t\t\t\t\t\"devsn_list\":[\"" . $doors[$k]['locksn'] . "\"]\r\n\t\t\t\t  }";
                $res = thinmoo_http_post($set, $dev_data);
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
            } else {
                $res = $this->devstatus($doors[$k]['doortype'], $doors[$k]['locksn']);
                if ($res['code'] == '0') {
                    $doors[$k]['isonline'] = '1';
                }
            }
            ($faces += 1) + -1;
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
    $faceimage = MODULE_URL . 'static/mobile/images/face.png';
    include $this->mymtpl('doors');
    exit(0);
} elseif ($operation == 'mydoor') {
    $doors = array();
    $doors1 = array();
    $doors2 = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and status = 1 and lid=0 and bid=0 and tid=0';
    $doors1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
    $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($houses))) {
        $sql = 'select lid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid = :rid and id=:bid';
        $lid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':bid' => $houses[$k]['bid']));
        $lid = empty($lid) ? 0 : $lid;
        if ($lid > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => 0, ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        }
        ($k += 1) + -1;
    }
    $doors = array_merge($doors1, $doors);
    $doors = multi_array_unique($doors);
    $k = 0;
    while (!($k >= count($doors))) {
        if ($doors[$k]['devtype'] == 1) {
            $doors[$k]['isonline'] = '1';
        } elseif ($doors[$k]['devtype'] == 2 || $doors[$k]['devtype'] == 3) {
            if ($doors[$k]['doortype'] == 5) {
                $doors[$k]['isonline'] = '1';
            } elseif ($doors[$k]['doortype'] == 6) {
                $set = array();
                $set['url'] = '/doormaster/server/remote_control';
                $set['token'] = $region_item['thinmoo_token'];
                $set['op'] = 'GET';
                $dev_data = "{\r\n\t\t\t\t\t\"devsn_list\":[\"" . $doors[$k]['locksn'] . "\"]\r\n\t\t\t\t  }";
                $res = thinmoo_http_post($set, $dev_data);
                if ($res['ret'] == '0') {
                    $door_data = $res['data'];
                    $lock_data = $door_data[$data[$k]['locksn']];
                    if ($lock_data['dev_status'] == 'online') {
                        $data[$k]['isonline'] = '1';
                    } else {
                        $data[$k]['isonline'] = '2';
                    }
                } else {
                    $data[$k]['isonline'] = '2';
                }
            } else {
                $res = $this->devstatus($doors[$k]['doortype'], $doors[$k]['locksn']);
                if ($res['code'] == '0') {
                    $doors[$k]['isonline'] = '1';
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
        ($k += 1) + -1;
    }
    include $this->mymtpl('mydoor');
} elseif ($operation == 'invitevisit') {
    $id = intval($_GPC['id']);
    include $this->mymtpl('invitevisit');
} elseif ($operation == 'askvisit') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        $effetime = strtotime('+' . intval($_GPC['effedate']) . ' minutes', TIMESTAMP);
        $myroom = explode('-', $_GPC['room']);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid = :rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0  order by otype limit 3';
        $member = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $myroom[0], ':tid' => $myroom[1], ':hid' => $myroom[2]));
        if (!empty($member) && !empty($item)) {
            $data = array('weid' => $_W['uniacid'], 'doorid' => $id, 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $myroom[0], 'tid' => $myroom[1], 'hid' => $myroom[2], 'title' => $item['title'], 'fromopenid' => $member['openid'], 'fromuid' => $member['uid'], 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid'], 'effedate' => $_GPC['effedate'], 'effetime' => $effetime, 'opentimes' => $_GPC['opentimes'], 'status' => 0, 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_door_visit', $data);
            $subid = pdo_insertid();
            $url = $this->createMobileurl($mydo, array('op' => 'auditvisit', 'id' => $subid, 'reason' => $_GPC['reason']));
            $url = $this->my_mobileurl($url);
            $userinfo = $_W['fans'];
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $userinfo['nickname'], 'color' => $textcolor), 'remark' => array('value' => '有访客到访,' . $_GPC['reason'] . ',请审核或直接开门!'));
            if (!empty($this->syscfg['tplid1'])) {
                $k = 0;
                while (!($k >= count($member))) {
                    $this->send_mysendtplnotice($member[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    ($k += 1) + -1;
                }
            }
            $this->mymsg('success', '申请成功', '', 'close');
        } else {
            $this->mymsg('error', '申请失败', '请联系业主', 'close');
        }
    }
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    if (!empty($item['bid'])) {
        $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid and id=:bid order by title,id';
        $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    } else {
        $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid order by title,id';
        $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
    }
    $mybuilding = $buildings;
    $k = 0;
    while (!($k >= count($buildings))) {
        if (!empty($item['tid'])) {
            $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid and id=:tid order by title,id';
            $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $buildings[$k]['id'], ':tid' => $item['tid']));
        } else {
            $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid order by title,id';
            $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $buildings[$k]['id']));
        }
        $myunit[$buildings[$k]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by title,id';
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $buildings[$k]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('askvisit');
} elseif ($operation == 'askvisit_mobile') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        $effetime = strtotime('+' . intval($_GPC['effedate']) . ' minutes', TIMESTAMP);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid = :rid and mobile=:mobile and deleted=0 and status=0 order by otype';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':mobile' => $_GPC['mobile']));
        if (!empty($member) && !empty($item)) {
            $data = array('weid' => $_W['uniacid'], 'doorid' => $id, 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $member['bid'], 'tid' => $member['tid'], 'hid' => $member['hid'], 'title' => $item['title'], 'fromopenid' => $member['openid'], 'fromuid' => $member['uid'], 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid'], 'effedate' => $_GPC['effedate'], 'effetime' => $effetime, 'opentimes' => $_GPC['opentimes'], 'status' => 0, 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_door_visit', $data);
            $subid = pdo_insertid();
            $url = $this->createMobileurl($mydo, array('op' => 'auditvisit', 'id' => $subid, 'reason' => $_GPC['reason']));
            $url = $this->my_mobileurl($url);
            load()->model('mc');
            $fans = mc_fansinfo($_W['openid'], $_W['acid'], $_W['uniacid']);
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $fans['nickname'], 'color' => $textcolor), 'remark' => array('value' => '有访客到访,' . $_GPC['reason'] . ',请审核或直接开门!'));
            if (!empty($this->syscfg['tplid1']) && $member['openid']) {
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            $this->mymsg('success', '申请成功', '', 'close');
        } else {
            $this->mymsg('error', '申请失败', '请联系业主', 'close');
        }
    }
    include $this->mymtpl('askmobile');
} elseif ($operation == 'auditvisit') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where status=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $url = $this->createMobileurl($mydo, array('op' => 'getvisit', 'id' => $id));
        $url = $this->my_mobileurl($url);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        load()->model('mc');
        $fans = mc_fansinfo($item['fromopenid'], $_W['acid'], $_W['uniacid']);
        if ($_GPC['vstatus'] == 1) {
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请通过审核', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $fans['nickname'], 'color' => $textcolor), 'remark' => array('value' => '您的申请已经通过,请点击进入!'));
            if (!empty($this->syscfg['tplid1']) && $item['toopenid']) {
                $this->send_mysendtplnotice($item['toopenid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                show_json(0, '审核成功');
            }
            show_json(1, '审核失败');
        }
        if ($_GPC['vstatus'] == 2) {
            pdo_update('rhinfo_zyxq_door_visit', array('status' => 1, 'toopenid' => '', 'touid' => 0), array('weid' => $_W['uniacid'], 'id' => $id));
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请被拒绝', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $fans['nickname'], 'color' => $textcolor), 'remark' => array('value' => '您的申请被拒绝,原因：' . $_GPC['reason']));
            if (!empty($this->syscfg['tplid1']) && $item['toopenid']) {
                $this->send_mysendtplnotice($item['toopenid'], $this->syscfg['tplid1'], $postdata, '', $topcolor);
                show_json(0, '拒绝成功');
            }
            show_json(1, '拒绝失败');
        }
    }
    if (empty($item)) {
        $this->mymsg('error', '访问无效', '未找到相关访客申请', 'close');
    }
    load()->model('mc');
    $fans = mc_fansinfo($item['toopenid'], $_W['acid'], $_W['uniacid']);
    include $this->mymtpl('auditvisit');
} elseif ($operation == 'sharevisit') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and status = 1 and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':id' => $id));
    $effetime = strtotime('+' . intval($_GPC['effedate']) . ' minutes', TIMESTAMP);
    if (!empty($item)) {
        $data = array('weid' => $_W['uniacid'], 'doorid' => $id, 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid'], 'title' => $item['title'], 'fromopenid' => $_W['openid'], 'fromuid' => $_W['member']['uid'], 'effedate' => $_GPC['effedate'], 'carno' => $_GPC['carno'], 'effetime' => $effetime, 'opentimes' => $_GPC['opentimes'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_door_visit', $data);
        $subid = pdo_insertid();
        $qrcode_url = $this->createMobileurl($mydo, array('op' => 'getvisit', 'id' => $subid));
        $qrcode_url = $this->my_mobileurl($qrcode_url);
        $_share['title'] = $_W['fans']['nickname'] . '邀请您成为访客';
        $_share['imgUrl'] = MODULE_URL . 'static/mobile/images/key.jpg';
        $_share['desc'] = '快速成为访客，扫码开门.';
        $_share['link'] = $qrcode_url;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and rid=:rid and status=1';
        $parklot = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
        if (!empty($parklot['pc_plotid']) && !empty($_GPC['carno'])) {
            $parklot['pc_type'] = !empty($parklot['pc_type']) ? $parklot['pc_type'] : $region['pc_type'];
            if ($parklot['pc_type'] == 2) {
                $set = array();
                $set['pc_appid'] = $region['pc_appid'];
                $set['pc_secret'] = $region['pc_secret'];
                $set['url'] = 'third_api/reserveParkingSpace.action';
                $params = array('parkId' => $parklot['pc_plotid'], 'carNum' => $_GPC['carno'], 'reserveId' => TIMESTAMP . random(8), 'reserveInTime' => date('Y-m-d H:i:s'), 'reserveOutTime' => date('Y-m-d H:i:s', strtotime('+1 days')), 'paymentStatus' => 2, 'chargingStrategyId' => $parklot['cloudruleid'], 'appUserId' => 1);
                $res = ipms_httpsign_post($set, $params);
                if (!($res['result'] == 'success')) {
                    $this->mysyslog(0, 'error', 'visit', '车位预约' . $_GPC['carno'], $res['resultMsg']);
                }
            } elseif ($parklot['pc_type'] == 3) {
                $set = array('url' => 'app/UpCarSet.aspx', 'parkno' => $parklot['pc_plotid'], 'secret' => $parklot['pc_secret']);
                $params = array('CarNo' => $_GPC['carno'], 'CarSetNo' => random(8), 'StartTime' => date('YmdHis'), 'EndTime' => date('YmdHis', strtotime('+1 days')));
                $res = etpcar_http_post($set, $params);
                $resmsg = $res['ReMsg'];
                if (!($resmsg['ErrNo'] == '0000')) {
                    $this->mysyslog(0, 'error', 'visit', '车位预约' . $_GPC['carno'], $resmsg['ErrMsg']);
                }
            } elseif ($parklot['pc_type'] == 5) {
                $set = array('url' => '/parkCarBook/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $parklot['pc_secret']);
                $params = array('plateNum' => $_GPC['carno'], 'parkNo' => random(8), 'payState' => 1, 'endDate' => strtotime('+1 days'));
                $res = deliyun_http_post($set, $params);
                if (!($res['ecode'] == 0)) {
                    $this->mysyslog(0, 'error', 'visit', '车位预约' . $_GPC['carno'], $res['msg']);
                }
            }
        }
        include $this->template($this->mytpl('opendoor/sharevisit'));
    } else {
        $this->mymsg('error', '邀请访客失败', '未找到对应门禁', 'close');
    }
} elseif ($operation == 'getvisit') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where status=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (!empty($item)) {
        pdo_update('rhinfo_zyxq_door_visit', array('status' => 1, 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'id' => $id));
        if (!($item['effetime'] >= TIMESTAMP)) {
            $this->mymsg('error', '非常抱歉', '访客有效期已过', 'close');
            exit(0);
        }
        if (!($item['opentimes'] >= 1)) {
            $this->mymsg('error', '非常抱歉', '访客开门次数已用完', 'close');
            exit(0);
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and id=:id ';
        $door = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['doorid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
        $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $door['pid'], ':rid' => $door['rid']));
        include $this->mymtpl('scanqrcode');
        exit(0);
    } else {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and id=:id and (toopenid=:openid or touid=:uid) ';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        if (!empty($item)) {
            if (!($item['effetime'] >= TIMESTAMP)) {
                $this->mymsg('error', '非常抱歉', '访客有效期已过', 'close');
                exit(0);
            }
            if (!($item['opentimes'] >= 1)) {
                $this->mymsg('error', '非常抱歉', '访客开门次数已用完', 'close');
                exit(0);
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and id=:id ';
            $door = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['doorid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $door['pid'], ':rid' => $door['rid']));
            include $this->mymtpl('scanqrcode');
        } else {
            $this->mymsg('error', '绑定访客失败', '已经失效，无法绑定访客', 'close');
        }
        exit(0);
    }
} elseif ($operation == 'getvisit1') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where status=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (!empty($item)) {
        if ($item['tid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        } elseif ($item['bid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        } else {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        }
        if ($total > 0) {
            $this->mymsg('error', '绑定访客失败', '您已经拥有房产权限', 'close');
            exit(0);
        }
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and (toopenid=:openid or touid=:uid) and status=1 and id=:id';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':id' => $id));
        if ($total > 0) {
            $this->mymsg('error', '绑定访客失败', '您已经是访客', 'close');
            exit(0);
        }
        pdo_update('rhinfo_zyxq_door_visit', array('status' => 1, 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'id' => $id));
        if (!($item['effetime'] >= TIMESTAMP)) {
            $this->mymsg('error', '开门失败', '访客有效期已过', 'close');
            exit(0);
        }
        if (!($item['opentimes'] >= 1)) {
            $this->mymsg('error', '开门失败', '访客开门次数已用完', 'close');
            exit(0);
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and id=:id ';
        $door = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['doorid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
        $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $door['pid'], ':rid' => $door['rid']));
        if ($door['doorcate']) {
            if ($door['doorcate'] == 1) {
                $door['image'] = $region_item['doorimage'] ? tomedia($region_item['doorimage']) : MODULE_URL . 'static/mobile/images/door.png';
            } elseif ($door['doorcate'] == 2) {
                $door['image'] = $region_item['locationimage'] ? tomedia($region_item['locationimage']) : MODULE_URL . 'static/mobile/images/location.png';
            } elseif ($door['doorcate'] == 3) {
                $door['image'] = $region_item['unitimage'] ? tomedia($region_item['unitimage']) : MODULE_URL . 'static/mobile/images/unit.png';
            } elseif ($door['doorcate'] == 4) {
                $door['image'] = $region_item['buildingimage'] ? tomedia($region_item['buildingimage']) : MODULE_URL . 'static/mobile/images/building.png';
            }
        } else {
            if ($door['bid'] && $door['tid']) {
                $door['image'] = $region_item['unitimage'] ? tomedia($region_item['unitimage']) : MODULE_URL . 'static/mobile/images/unit.png';
            }
            if ($door['bid'] && empty($door['tid'])) {
                $door['image'] = $region_item['buildingimage'] ? tomedia($region_item['buildingimage']) : MODULE_URL . 'static/mobile/images/building.png';
            }
            if (empty($door['lid']) && empty($door['bid']) && empty($door['tid'])) {
                $door['image'] = $region_item['doorimage'] ? tomedia($region_item['doorimage']) : MODULE_URL . 'static/mobile/images/door.png';
            }
            if ($door['lid'] && empty($door['bid']) && empty($door['tid'])) {
                $door['image'] = $region_item['locationimage'] ? tomedia($region_item['locationimage']) : MODULE_URL . 'static/mobile/images/location.png';
            }
        }
        $sql = 'select link,thumb from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and  btype=5 and enabled = 1 order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $door['pid'], ':rid' => $door['rid']));
        if (empty($banners)) {
            $sql = 'select link,thumb from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=5 and enabled = 1 order by displayorder desc';
            $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
        }
        include $this->mymtpl('scanqrcode');
        exit(0);
    } else {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and id=:id and (toopenid=:openid or touid=:uid)';
        $door_visit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        if (!empty($door_visit)) {
            if (!($door_visit['effetime'] >= TIMESTAMP)) {
                $this->mymsg('error', '开门失败', '访客有效期已过', 'close');
                exit(0);
            }
            if (!($door_visit['opentimes'] >= 1)) {
                $this->mymsg('error', '开门失败', '访客开门次数已用完', 'close');
                exit(0);
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and id=:id ';
            $door = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $door_visit['doorid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $door['pid'], ':rid' => $door['rid']));
            if ($door['doorcate']) {
                if ($door['doorcate'] == 1) {
                    $door['image'] = $region_item['doorimage'] ? tomedia($region_item['doorimage']) : MODULE_URL . 'static/mobile/images/door.png';
                } elseif ($door['doorcate'] == 2) {
                    $door['image'] = $region_item['locationimage'] ? tomedia($region_item['locationimage']) : MODULE_URL . 'static/mobile/images/location.png';
                } elseif ($door['doorcate'] == 3) {
                    $door['image'] = $region_item['unitimage'] ? tomedia($region_item['unitimage']) : MODULE_URL . 'static/mobile/images/unit.png';
                } elseif ($door['doorcate'] == 4) {
                    $door['image'] = $region_item['buildingimage'] ? tomedia($region_item['buildingimage']) : MODULE_URL . 'static/mobile/images/building.png';
                }
            } else {
                if ($door['bid'] && $door['tid']) {
                    $door['image'] = $region_item['unitimage'] ? tomedia($region_item['unitimage']) : MODULE_URL . 'static/mobile/images/unit.png';
                }
                if ($door['bid'] && empty($door['tid'])) {
                    $door['image'] = $region_item['buildingimage'] ? tomedia($region_item['buildingimage']) : MODULE_URL . 'static/mobile/images/building.png';
                }
                if (empty($door['lid']) && empty($door['bid']) && empty($door['tid'])) {
                    $door['image'] = $region_item['doorimage'] ? tomedia($region_item['doorimage']) : MODULE_URL . 'static/mobile/images/door.png';
                }
                if ($door['lid'] && empty($door['bid']) && empty($door['tid'])) {
                    $door['image'] = $region_item['locationimage'] ? tomedia($region_item['locationimage']) : MODULE_URL . 'static/mobile/images/location.png';
                }
            }
            $sql = 'select link,thumb from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid = :pid and rid = :rid and  btype=5 and enabled = 1 order by displayorder desc';
            $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $door['pid'], ':rid' => $door['rid']));
            if (empty($banners)) {
                $sql = 'select link,thumb from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=5 and enabled = 1 order by displayorder desc';
                $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
            }
            include $this->mymtpl('scanqrcode1');
        } else {
            $this->mymsg('error', '绑定访客失败', '已经失效，无法绑定访客', 'close');
        }
        exit(0);
    }
} elseif ($operation == 'opendoor') {
    $id = intval($_GPC['id']);
    $visitid = intval($_GPC['visitid']);
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and id=:visitid and (toopenid=:openid or touid=:uid) and status=1';
    $visit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':visitid' => $visitid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (!empty($visit)) {
        if (!($visit['effetime'] >= TIMESTAMP)) {
            show_json(1, '访客有效期已过');
        }
        if (!($visit['opentimes'] >= 1)) {
            show_json(1, '开门次数已用完');
        }
        $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $item['locksn'], $item['lockid'], 'opendoor', $id);
        $this->opendoorlog($id, $res);
    } else {
        show_json(1, '您还没有访客权限');
    }
    if ($res['code'] == '0') {
        $sql = 'update ' . tablename('rhinfo_zyxq_door_visit') . ' set opentimes=opentimes - 1 where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and (toopenid=:openid or touid=:uid) and status=1 ';
        pdo_query($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        show_json(0);
    } else {
        show_json(1, $res['msg']);
    }
} elseif ($operation == 'scanopen') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where status=1 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (!empty($item)) {
        if ($item['tid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and bid=:bid and tid=:tid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        } elseif ($item['bid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and bid=:bid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        } else {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        }
        $res = array();
        if ($total > 0) {
            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $item['locksn'], $item['lockid'], 'opendoor', $id);
            $this->opendoorlog($id, $res);
            if ($res['code'] == '0') {
                $this->mymsg('success', '开门成功', '', 'close');
            } else {
                $this->mymsg('error', '开门失败', '请联系物业', 'close');
            }
            exit(0);
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and rid=:rid and bid=:bid and tid=:tid and (toopenid=:openid or touid=:uid) and doorid=:doorid and status=1';
        $visit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':doorid' => $id));
        if (!empty($visit)) {
            if (!($visit['effetime'] >= TIMESTAMP)) {
                $url = $this->createMobileUrl($mydo, array('op' => 'askvisit', 'id' => $id));
                $this->mymsg('error', '开门失败', '访客有效期已过', $url);
                exit(0);
            }
            if (!($visit['opentimes'] >= 1)) {
                $url = $this->createMobileUrl($mydo, array('op' => 'askvisit', 'id' => $id));
                $this->mymsg('error', '开门失败', '访客开门次数已用完', $url);
                exit(0);
            }
            $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $item['locksn'], $item['lockid'], 'opendoor', $id);
            $this->opendoorlog($id, $res);
            if ($res['code'] == '0') {
                $sql = 'update ' . tablename('rhinfo_zyxq_door_visit') . ' set opentimes=opentimes - 1 where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and (toopenid=:openid or touid=:uid) and doorid=:doorid and status=1 ';
                pdo_query($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':doorid' => $id));
                $this->mymsg('success', '开门成功', '', 'close');
            } else {
                $this->mymsg('error', '开门失败', '请联系物业', 'close');
            }
            exit(0);
        } else {
            $url = $this->createMobileUrl($mydo, array('op' => 'askvisit', 'id' => $id));
            $this->mymsg('error', '开门失败', '您还没有访客权限，点击可申请权限', $url);
            exit(0);
        }
    } else {
        $this->mymsg('error', '开门失败', '该门禁不存在或已停用', 'close');
        exit(0);
    }
} elseif ($operation == 'myvisit') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and rid=:rid and (toopenid=:openid or touid=:uid)and status=1 and opentimes>0 and effetime>' . TIMESTAMP;
    $visits = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($visits))) {
        $door = pdo_get('rhinfo_zyxq_door', array('weid' => $_W['uniacid'], 'id' => $visits[$k]['doorid']), array('devtype'));
        $visits[$k]['qrcode'] = $door['devtype'] == 1 ? 1 : 0;
        ($k += 1) + -1;
    }
    include $this->mymtpl('myvisit');
} elseif ($operation == 'opendoor_visit') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and status = 1 and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (empty($item)) {
        show_json(1, '设备停用');
    }
    $res = $this->scan_opendoor($item['rid'], $item['devtype'], $item['doortype'], $item['locksn'], $item['lockid'], 'opendoor', $id);
    $this->opendoorlog($id, $res);
    if ($res['code'] == '0') {
        show_json(0);
    } else {
        show_json(1, $res['msg']);
    }
} elseif ($operation == 'qrcode') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and status = 1 and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select doorlock_type,thinmoo_token,mailin_appid,mailin_secret,mailin_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
    if ($item['devtype'] == 1 || $item['devtype'] == 2 || $item['devtype'] == 3) {
        if (!empty($_GPC['visitid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and id=:visitid and (toopenid=:openid or touid=:uid) and status=1';
            $visit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':visitid' => $_GPC['visitid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            if (!empty($visit)) {
                if (!($visit['effetime'] >= TIMESTAMP)) {
                    $this->mymsg('error', '温馨提示', '访客有效期已过.', '');
                }
                if (!($visit['opentimes'] >= 1)) {
                    $this->mymsg('error', '温馨提示', '开门次数已用完.', '');
                }
                $sql = 'update ' . tablename('rhinfo_zyxq_door_visit') . ' set opentimes=opentimes - 1 where weid = :weid and id=:visitid ';
                pdo_query($sql, array(':weid' => $_W['uniacid'], ':visitid' => $_GPC['visitid']));
            } else {
                $this->mymsg('error', '温馨提示', '无权限操作.', '');
            }
        }
        if ($item['doortype'] == 5) {
            $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
            $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_smdqrc', 'device_sncode' => $item['locksn']);
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
                $data = array('token' => $res['access_token'], 'devid' => $item['locksn'], 'lockid' => '01', 'type' => 1);
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
            myload()->classs('thinmoo');
            $region;
            $thinmoo = 'ThinMoo';
            $data = "{\r\n\t\t\t\t \"dev_sn_list\":[\"" . $item['locksn'] . "\"],\r\n\t\t\t\t \"memo\": \"" . $user['nickname'] . "\",\r\n\t\t\t\t \"start_datetime\": \"" . date('YmdHis', TIMESTAMP) . "\", \r\n\t\t\t\t \"end_datetime\": \"" . date('YmdHis', strtotime('+ 15 minutes', TIMESTAMP)) . "\",\r\n\t\t\t\t \"pwd_type\":2,\r\n\t\t\t\t \"use_count\":5\r\n\t\t\t  }";
            $res = $thinmoo->open_qrcode($data, 'devices');
            if (is_error($res)) {
                $this->mymsg('error', $res['message'], '');
            } else {
                $fileurl = $this->createqrcode($res['qrcode_content']);
                include $this->mymtpl('myqrcode');
            }
        } else {
            $this->mymsg('error', '温馨提示', '生成二维码失败，参数不正确.', '');
        }
    } else {
        $this->mymsg('error', '温馨提示', '生成二维码失败，参数未设置.', '');
    }
} elseif ($operation == 'myfacedoor') {
    $doors = array();
    $doors1 = array();
    $doors2 = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid = :rid and status = 1 and lid=0 and bid=0 and tid=0 and devtype=3';
    $doors1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
    $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($houses))) {
        $sql = 'select lid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid = :pid and rid = :rid and id=:bid';
        $lid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $houses[$k]['bid']));
        $lid = empty($lid) ? 0 : $lid;
        if ($lid > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid and devtype=3';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid and devtype=3';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => 0, ':tid' => 0));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid and devtype=3';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid and devtype=3';
            $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$k]['bid'], ':tid' => $houses[$k]['tid']));
            if (!empty($doors2)) {
                $doors = array_merge($doors, $doors2);
            }
        }
        ($k += 1) + -1;
    }
    $doors = array_merge($doors1, $doors);
    $doors = multi_array_unique($doors);
    $k = 0;
    while (!($k >= count($doors))) {
        if ($doors[$k]['doortype'] == 2) {
            $doors[$k]['isonline'] = '1';
        } elseif ($doors[$k]['doortype'] == 5) {
            $doors[$k]['isonline'] = '1';
        } elseif ($doors[$k]['doortype'] == 6) {
            $doors[$k]['isonline'] = '1';
        } else {
            $doors[$k]['isonline'] = '2';
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('myfacedoor');
} elseif ($operation == 'regfacedoor') {
    if ($_W['isajax']) {
        if (is_array($_GPC['images'])) {
            $images = $_GPC['images'];
        } else {
            show_json(0, '照片上传不成功');
        }
        $sql = 'select doorlock_type,thinmoo_token,mailin_appid,mailin_secret,mailin_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
        $mobile = $user['mobile'];
        $nickname = $user['nickname'];
        $doors = $this->get_my_doors($user['rid'], $user['bid'], $user['tid'], 3, 0);
        $i = 0;
        $k = 0;
        while (!($k >= count($doors))) {
            if ($_GPC['isother'] == 1) {
                $mobile = $_GPC['mobile'];
                $nickname = $_GPC['nickname'];
                $true_name = $nickname;
                $member_id = $mobile;
                $face = pdo_get('rhinfo_zyxq_door_faces', array('weid' => $_W['uniacid'], 'did' => $doors[$k]['id'], 'uid' => 0, 'mobile' => $mobile));
                if ($face['status'] == 1) {
                    $faceid = $face['id'];
                    pdo_update('rhinfo_zyxq_door_faces', array('faceimg' => $images[0], 'status' => 0), array('weid' => $_W['uniacid'], 'id' => $faceid));
                } else {
                    $data = array('weid' => $_W['uniacid'], 'did' => $doors[$k]['id'], 'uid' => 0, 'mobile' => $mobile, 'nickname' => $nickname, 'faceimg' => $images[0], 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59', 'status' => 0, 'ctime' => TIMESTAMP, 'cuid' => $_W['member']['uid']);
                    pdo_insert('rhinfo_zyxq_door_faces', $data);
                    $faceid = pdo_insertid();
                }
            } else {
                $true_name = !empty($_W['member']['realname']) ? $_W['member']['realname'] : $nickname;
                $member_id = !empty($_W['member']['uid']) ? $_W['uniacid'] . $_W['member']['uid'] : $mobile;
                $face = pdo_get('rhinfo_zyxq_door_faces', array('weid' => $_W['uniacid'], 'did' => $doors[$k]['id'], 'uid' => $_W['member']['uid']));
                if ($face['status'] == 1) {
                    $faceid = $face['id'];
                    pdo_update('rhinfo_zyxq_door_faces', array('faceimg' => $images[0], 'status' => 0), array('weid' => $_W['uniacid'], 'id' => $faceid));
                } else {
                    $data = array('weid' => $_W['uniacid'], 'did' => $doors[$k]['id'], 'uid' => $_W['member']['uid'], 'mobile' => $mobile, 'nickname' => $nickname, 'faceimg' => $images[0], 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59', 'status' => 0, 'ctime' => TIMESTAMP, 'cuid' => $_W['member']['uid']);
                    pdo_insert('rhinfo_zyxq_door_faces', $data);
                    $faceid = pdo_insertid();
                }
            }
            if ($doors[$k]['doortype'] == 2) {
                $faceimg = mybase64EncodeImage(IA_ROOT . '/attachment/' . $images[0]);
                $post_data = array('apiid' => $this->syscfg['bl_apiid'], 'apikey' => $this->syscfg['bl_apikey']);
                $res = Park_GetToken($this->syscfg['siteurl'], $post_data);
                if (!empty($res['access_token'])) {
                    $data = array('token' => $res['access_token'], 'typeid' => 100, 'nickname' => $nickname, 'tel' => $mobile);
                    $ret = Park_httpPost_face($this->syscfg['siteurl'], $data);
                    if ($ret['code'] == '1' || $ret['code'] == '100102') {
                        $data = array('token' => $res['access_token'], 'typeid' => 200, 'tel' => $mobile, 'devid' => $doors[$k]['locksn'], 'lockid' => '01', 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59');
                        $ret1 = Park_httpPost_face($this->syscfg['siteurl'], $data);
                        if ($ret1['code'] == '1') {
                            $data = array('token' => $res['access_token'], 'typeid' => 300, 'tel' => $mobile, 'devid' => $doors[$k]['locksn'], 'filedata' => $faceimg);
                            $result = Park_httpPost_face($this->syscfg['siteurl'], $data);
                            if ($result['code'] == '1') {
                                pdo_update('rhinfo_zyxq_door_faces', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $faceid));
                                $msg = $face['status'] == 1 ? '更新成功' : '注册成功';
                                ($i += 1) + -1;
                            }
                        }
                    }
                }
            } elseif ($doors[$k]['doortype'] == 5) {
                $faceimg = IA_ROOT . '/attachment/' . $images[0];
                $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
                if ($face['status'] == 1) {
                    $post_data = array('m' => 'do', 'f' => 'face', 'a' => 'face_reg', 'face_img' => $faceimg, 'member_id' => $member_id, 'true_name' => $true_name, 'device_sncode' => $doors[$k]['locksn']);
                } else {
                    $post_data = array('m' => 'do', 'f' => 'face', 'a' => 'face_reg', 'face_img' => $faceimg, 'member_id' => $member_id, 'true_name' => $true_name, 'device_sncode' => $doors[$k]['locksn']);
                }
                $res = mailin_http_post_face($set, $post_data);
                if ($res['state'] == 1) {
                    pdo_update('rhinfo_zyxq_door_faces', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $faceid));
                    $msg = $face['status'] == 1 ? '更新成功' : '注册成功';
                    ($i += 1) + -1;
                }
            } elseif ($doors[$k]['doortype'] == 6) {
                myload()->classs('thinmoo');
                $region;
                $thinmoo = 'ThinMoo';
                $faceimg = mybase64EncodeImage(IA_ROOT . '/attachment/' . $images[0], false);
                $set = array();
                if ($face['status'] == 1) {
                    $post_data = "{\r\n\t\t\t\t\t\t \"emp_id\": \"" . $face['userid'] . "\",\r\n\t\t\t\t\t\t \"face_image\": \"" . $faceimg . "\",\r\n\t\t\t\t\t\t \"index\":1,\r\n\t\t\t\t\t\t \"type\":2\r\n\t\t\t\t\t  }";
                    $ret = $thinmoo->reg_face($post_data);
                    if (!is_error($ret)) {
                        pdo_update('rhinfo_zyxq_door_faces', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $faceid));
                        $set['url'] = '/doormaster/server/video_devices_permissions';
                        $post_data = "{\r\n\t\t\t\t\t\t\t \"dev_sn\":\"" . $doors[$k]['locksn'] . "\",\r\n\t\t\t\t\t\t\t \"emp_group\":[" . $face['userid'] . "]\r\n\t\t\t\t\t\t  }";
                        $resp = $thinmoo->add_perm($post_data);
                        if (!is_error($resp)) {
                            ($i += 1) + -1;
                        }
                    }
                } else {
                    $post_data = "{\r\n\t\t\t\t\t\t \"emp_uuid\": \"" . $member_id . "\",\r\n\t\t\t\t\t\t \"emp_name\": \"" . $true_name . "\"\r\n\t\t\t\t\t  }";
                    $ret = $thinmoo->add_user($post_data);
                    if (!is_error($ret)) {
                        $post_data = "{\r\n\t\t\t\t\t\t\t \"emp_id\": \"" . $ret['id'] . "\",\r\n\t\t\t\t\t\t\t \"face_image\": \"" . $faceimg . "\",\r\n\t\t\t\t\t\t\t \"index\":1,\r\n\t\t\t\t\t\t\t \"type\":2\r\n\t\t\t\t\t\t  }";
                        $res = $thinmoo->reg_face($post_data);
                        if (!is_error($res)) {
                            pdo_update('rhinfo_zyxq_door_faces', array('status' => 1, 'userid' => $ret['id']), array('weid' => $_W['uniacid'], 'id' => $faceid));
                            $post_data = "{\r\n\t\t\t\t\t\t\t\t \"dev_sn\":\"" . $doors[$k]['locksn'] . "\",\r\n\t\t\t\t\t\t\t\t \"emp_group\":[" . $ret['id'] . "]\r\n\t\t\t\t\t\t\t  }";
                            $resp = $thinmoo->add_perm($post_data);
                            if (!is_error($resp)) {
                                ($i += 1) + -1;
                            }
                        }
                    }
                }
            }
            ($k += 1) + -1;
        }
        if ($i > 0) {
            show_json(1, '成功认证' . $i . '个门禁');
        } else {
            show_json(0, '认证失败');
        }
    }
    $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $user['rid']), array('title'));
    include $this->mymtpl();
} elseif ($operation == 'facedoor') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where status=1 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        if ($item['devtype'] != 3) {
            show_json(0, '该设备不支持人脸识别');
        }
        $sql = 'select doorlock_type,thinmoo_token,mailin_appid,mailin_secret,mailin_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
        if (is_array($_GPC['images'])) {
            $images = $_GPC['images'];
        } else {
            show_json(0, '照片上传不成功');
        }
        $mobile = $user['mobile'];
        $nickname = $user['nickname'];
        if ($_GPC['isother'] == 1) {
            $mobile = $_GPC['mobile'];
            $nickname = $_GPC['nickname'];
            $true_name = $nickname;
            $member_id = $mobile;
            $face = pdo_get('rhinfo_zyxq_door_faces', array('weid' => $_W['uniacid'], 'did' => $id, 'uid' => 0, 'mobile' => $mobile));
            if ($face['status'] == 1) {
                $faceid = $face['id'];
                pdo_update('rhinfo_zyxq_door_faces', array('faceimg' => $images[0], 'status' => 0), array('weid' => $_W['uniacid'], 'id' => $faceid));
            } else {
                $data = array('weid' => $_W['uniacid'], 'did' => $id, 'uid' => 0, 'mobile' => $mobile, 'nickname' => $nickname, 'faceimg' => $images[0], 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59', 'status' => 0, 'ctime' => TIMESTAMP, 'cuid' => $_W['member']['uid']);
                pdo_insert('rhinfo_zyxq_door_faces', $data);
                $faceid = pdo_insertid();
            }
        } else {
            $true_name = !empty($_W['member']['realname']) ? $_W['member']['realname'] : $nickname;
            $member_id = !empty($_W['member']['uid']) ? $_W['uniacid'] . $_W['member']['uid'] : $mobile;
            $face = pdo_get('rhinfo_zyxq_door_faces', array('weid' => $_W['uniacid'], 'did' => $id, 'uid' => $_W['member']['uid']));
            if ($face['status'] == 1) {
                $faceid = $face['id'];
                pdo_update('rhinfo_zyxq_door_faces', array('faceimg' => $images[0], 'status' => 0), array('weid' => $_W['uniacid'], 'id' => $faceid));
            } else {
                $data = array('weid' => $_W['uniacid'], 'did' => $id, 'uid' => $_W['member']['uid'], 'mobile' => $mobile, 'nickname' => $nickname, 'faceimg' => $images[0], 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59', 'status' => 0, 'ctime' => TIMESTAMP, 'cuid' => $_W['member']['uid']);
                pdo_insert('rhinfo_zyxq_door_faces', $data);
                $faceid = pdo_insertid();
            }
        }
        if ($item['doortype'] == 2) {
            $faceimg = mybase64EncodeImage(IA_ROOT . '/attachment/' . $images[0]);
            $post_data = array('apiid' => $this->syscfg['bl_apiid'], 'apikey' => $this->syscfg['bl_apikey']);
            $res = Park_GetToken($this->syscfg['siteurl'], $post_data);
            if (!empty($res['access_token'])) {
                $data = array('token' => $res['access_token'], 'typeid' => 100, 'nickname' => $nickname, 'tel' => $mobile);
                $ret = Park_httpPost_face($this->syscfg['siteurl'], $data);
                if ($ret['code'] == '1' || $ret['code'] == '100102') {
                    $data = array('token' => $res['access_token'], 'typeid' => 200, 'tel' => $mobile, 'devid' => $item['locksn'], 'lockid' => '01', 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59');
                    $ret1 = Park_httpPost_face($this->syscfg['siteurl'], $data);
                    if ($ret1['code'] == '1') {
                        $data = array('token' => $res['access_token'], 'typeid' => 300, 'tel' => $mobile, 'devid' => $item['locksn'], 'filedata' => $faceimg);
                        $result = Park_httpPost_face($this->syscfg['siteurl'], $data);
                        if ($result['code'] == '1') {
                            pdo_update('rhinfo_zyxq_door_faces', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $faceid));
                            $msg = $face['status'] == 1 ? '更新成功' : '注册成功';
                            show_json(1, $msg);
                        } else {
                            show_json(0, $result['code'] . $result['msg']);
                        }
                    } else {
                        show_json(0, $ret1['code'] . $ret1['msg']);
                    }
                } else {
                    show_json(0, $ret['code'] . $ret['msg']);
                }
            } else {
                show_json(0, '获取TOKEN失败');
            }
        } elseif ($item['doortype'] == 5) {
            $faceimg = IA_ROOT . '/attachment/' . $images[0];
            $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
            if ($face['status'] == 1) {
                $post_data = array('m' => 'do', 'f' => 'face', 'a' => 'face_reg', 'face_img' => $faceimg, 'member_id' => $member_id, 'true_name' => $true_name, 'device_sncode' => $item['locksn']);
            } else {
                $post_data = array('m' => 'do', 'f' => 'face', 'a' => 'face_reg', 'face_img' => $faceimg, 'member_id' => $member_id, 'true_name' => $true_name, 'device_sncode' => $item['locksn']);
            }
            $res = mailin_http_post_face($set, $post_data);
            if ($res['state'] == 1) {
                pdo_update('rhinfo_zyxq_door_faces', array('status' => 1, 'userid' => $res['return_data']['userId']), array('weid' => $_W['uniacid'], 'id' => $faceid));
                $msg = $face['status'] == 1 ? '更新成功' : '注册成功';
                show_json(1, $msg);
            } else {
                show_json(0, $res['return_data']);
            }
        } elseif ($item['doortype'] == 6) {
            myload()->classs('thinmoo');
            $region;
            $thinmoo = 'ThinMoo';
            $faceimg = mybase64EncodeImage(IA_ROOT . '/attachment/' . $images[0], false);
            $set = array();
            if ($face['status'] == 1) {
                $post_data = "{\r\n\t\t\t\t\t \"emp_uuid\": \"" . $member_id . "\",\r\n\t\t\t\t\t \"face_image\": \"" . $faceimg . "\",\r\n\t\t\t\t\t \"index\":1,\r\n\t\t\t\t\t \"type\":2\r\n\t\t\t\t  }";
                $res = $thinmoo->reg_face($post_data);
                if (is_error($res)) {
                    show_json(0, $res['message']);
                }
                pdo_update('rhinfo_zyxq_door_faces', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $faceid));
                $post_data = "{\r\n\t\t\t\t\t \"dev_sn\":\"" . $item['locksn'] . "\",\r\n\t\t\t\t\t \"emp_group\":[" . $res['emp_id'] . "]\r\n\t\t\t\t  }";
                $resp = $thinmoo->add_perm($post_data);
                if (is_error($resp)) {
                    show_json(0, $resp['message']);
                }
                show_json(1, '更新成功');
            } else {
                $post_data = "{\r\n\t\t\t\t\t \"emp_uuid\": \"" . $member_id . "\",\r\n\t\t\t\t\t \"emp_name\": \"" . $true_name . "\"\r\n\t\t\t\t  }";
                $ret = $thinmoo->add_user($post_data);
                if (is_error($ret)) {
                    show_json(0, $ret['message']);
                }
                $post_data = "{\r\n\t\t\t\t\t \"emp_id\": \"" . $ret['id'] . "\",\r\n\t\t\t\t\t \"face_image\": \"" . $faceimg . "\",\r\n\t\t\t\t\t \"index\":1,\r\n\t\t\t\t\t \"type\":2\r\n\t\t\t\t  }";
                $res = $thinmoo->reg_face($post_data);
                if (is_error($res)) {
                    show_json(0, $res['message']);
                }
                pdo_update('rhinfo_zyxq_door_faces', array('status' => 1, 'userid' => $ret['id']), array('weid' => $_W['uniacid'], 'id' => $faceid));
                $post_data = "{\r\n\t\t\t\t\t \"dev_sn\":\"" . $item['locksn'] . "\",\r\n\t\t\t\t\t \"emp_group\":[" . $ret['id'] . "]\r\n\t\t\t\t  }";
                $resp = $thinmoo->add_perm($post_data);
                if (is_error($resp)) {
                    show_json(0, $resp['message']);
                }
                show_json(1, '注册成功');
            }
        } else {
            show_json(0, '该设备不支持人脸识别');
        }
    }
    if (!empty($item)) {
        if ($item['devtype'] != 3) {
            $this->mymsg('error', '温馨提示', '该设备不支持人脸识别', 'close');
        }
        if ($item['tid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and bid=:bid and tid=:tid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        } elseif ($item['bid'] > 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and bid=:bid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        } else {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and (openid = :openid or uid=:uid) and deleted=0 and status=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        }
        if (!($total > 0)) {
            $this->mymsg('error', '温馨提示', '您还没有认证权限', 'close');
        }
    } else {
        $this->mymsg('error', '温馨提示', '未找到人脸识别设备', 'close');
    }
    $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $item['rid']), array('title'));
    include $this->mymtpl('facedoor');
}
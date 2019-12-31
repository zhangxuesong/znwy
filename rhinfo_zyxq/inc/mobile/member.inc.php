<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'member';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$mydo = 'member';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$myurl = $this->createMobileUrl($mydo);
$_share = $this->rhinfo_share();
load()->model('mc');
$user = $this->getnotice($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
if ($operation == 'index') {
    $rid = intval($_GPC['rid']);
    if (!empty($user['mobile'])) {
    }
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $item['id'];
    } elseif (empty($rid)) {
        if ($user['rid']) {
            $rid = $user['rid'];
        }
    }
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
    $behavior = $setting['creditbehaviors'];
    $creditnames = $setting['creditnames'];
    $credits = mc_credit_fetch($_W['member']['uid'], '*');
    $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
    if ($user['rid']) {
        if ($user['lifepay_token'] && $user['lifepay_rid']) {
            $set = array();
            $set['app_id'] = $this->syspub['alipay_appid'];
            $set['prikey'] = $this->syspub['alipay_rsa2'];
            $set['app_auth_token'] = $user['lifepay_token'];
            $set['method'] = 'bill.batchquery';
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and id=:hid';
            $myroom = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':hid' => $user['hid']));
            if ($myroom['lifepay_hid']) {
                $params = "{\r\n\t\t\t\t\t\"community_id\":\"" . $user['lifepay_rid'] . "\",\r\n\t\t\t\t\t\"bill_status\":\"FINISH_PAYMENT\",\r\n\t\t\t\t\t\"out_room_id\":\"" . $myroom['lifepay_hid'] . "\"\r\n\t\t\t\t  }";
                $res = my_alipay_life($set, $params);
                if (!is_error($res)) {
                    $res = json_decode($res, 1);
                    $res = $res['alipay_eco_cplife_bill_batchquery_response'];
                    if ($res['code'] == '10000' && is_array($res['bill_result_set'])) {
                        $lifepays = $res['bill_result_set'];
                        $k = 0;
                        while (!($k >= count($lifepays))) {
                            $update_data = array('payfee' => $lifepays[$k]['bill_entry_amount'], 'paytype' => 4, 'status' => 2, 'paydate' => TIMESTAMP);
                            pdo_update('rhinfo_zyxq_feebill', $update_data, array('weid' => $_W['uniacid'], 'id' => $lifepays[$k]['bill_entry_id'], 'status' => 1));
                            ($k += 1) + -1;
                        }
                    }
                }
            }
        }
        $sql = 'select f.itemid,l.title,l.icon,l.color,l.thumb from ' . tablename('rhinfo_zyxq_feeitem_building') . ' as f left join ' . tablename('rhinfo_zyxq_feeitem') . ' as l on f.itemid=l.id where f.weid=:weid and f.pid=:pid and f.rid=:rid and f.bid=:bid and l.status=1 and l.category=0';
        $items = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid']));
        $sql = 'SELECT distinct f.itemid ,i.title,i.icon,i.color,i.thumb FROM ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_feeitem') . ' as i on f.itemid=i.id where f.status=1 and  f.weid = :weid and f.pid = :pid and f.rid = :rid and f.bid = :bid and f.tid = :tid and f.hid=:hid';
        $items1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':tid' => $user['tid'], ':hid' => $user['hid']));
        if (!empty($items1)) {
            $items = array_merge($items1, $items);
            $items = multi_array_unique($items);
        }
        $k = 0;
        while (!($k >= count($items))) {
            if ($user['feecontrol'] == 1 || $user['feecontrol'] == 2) {
                if ($user['feeshowtype'] == 1) {
                    $items[$k]['url'] = $this->createMobileurl('fee', array('op' => 'yearbill', 'pid' => $user['pid'], 'rid' => $user['rid'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid']));
                } else {
                    $items[$k]['url'] = $this->createMobileurl('fee', array('op' => 'myfeebill', 'pid' => $user['pid'], 'rid' => $user['rid'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid']));
                }
            } elseif ($user['feeshowtype'] == 1) {
                $items[$k]['url'] = $this->createMobileurl('fee', array('op' => 'curryearbill', 'pid' => $user['pid'], 'rid' => $user['rid'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid'], 'itemid' => $items[$k]['itemid']));
            } else {
                $items[$k]['url'] = $this->createMobileurl('fee', array('op' => 'currfee', 'pid' => $user['pid'], 'rid' => $user['rid'], 'bid' => $user['bid'], 'tid' => $user['tid'], 'hid' => $user['hid'], 'itemid' => $items[$k]['itemid']));
            }
            $items[$k]['icon'] = empty($items[$k]['icon']) ? 'icon-money' : $items[$k]['icon'];
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and fee > payfee and weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and itemid=:itemid';
            $items[$k]['total'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':tid' => $user['tid'], ':hid' => $user['hid'], ':itemid' => $items[$k]['itemid']));
            ($k += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and category=3 and tid = 0 and (openid=:openid or uid=:uid) and deleted=0 and status=0';
        $parkings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        $carfeenum = 0;
        $k = 0;
        while (!($k >= count($parkings))) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and status=1';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $parkings[$k]['bid'], ':cid' => $parkings[$k]['hid']));
            $carfeenum += $total;
            ($k += 1) + -1;
        }
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid = :rid and status=1 order by displayorder desc';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    if (empty($list)) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid = :rid and status=1 order by displayorder desc';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => 0, ':rid' => 0));
    }
    $sql = 'select t.*,c.title,c.right1,c.right2,c.right3,c.right4,c.right5,c.right6,c.right7,c.right8,c.right9,c.right10,c.right11,c.right12,c.right13,c.right14,c.right15,c.right16,c.right17,c.right18 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and c.type=4 and (t.openid = :openid or t.uid = :uid) and t.rid=:rid ';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':rid' => $user['rid']));
    if (empty($team)) {
        if (empty($_W['minirid'])) {
            $sql = 'select t.*,c.title,c.right1,c.right2,c.right3,c.right4,c.right5,c.right6,c.right7,c.right8,c.right9,c.right10,c.right11,c.right12,c.right13,c.right14,c.right15,c.right16,c.right17,c.right18 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and c.type=4 and (t.openid = :openid or t.uid = :uid) and t.rid>0';
            $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            if (empty($team)) {
                $sql = 'select t.*,c.title,c.right1,c.right2,c.right3,c.right4,c.right5,c.right6,c.right7,c.right8,c.right9,c.right10,c.right11,c.right12,c.right13,c.right14,c.right15,c.right16,c.right17,c.right18 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and c.type=4 and (t.openid = :openid or t.uid = :uid) and t.rid=0';
                $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
                if (!empty($team)) {
                    $regionarr = explode(',', $team['ridstr']);
                    if (!empty($user['rid']) && !in_array($user['rid'], $regionarr)) {
                        $team = array();
                    }
                } else {
                    $team = array();
                }
            }
        }
    }
    $user['avatar'] = empty($user['avatar']) ? tomedia($fans['avatar']) : tomedia($user['avatar']);
    if (empty($list)) {
        include $this->mymtpl('index');
    } else {
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_pagediy') . ' where weid = :weid and cateid = :cateid and status=1 order by displayorder desc';
            $navs = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':cateid' => $list[$k]['id']));
            $list[$k]['navs'] = $navs;
            ($k += 1) + -1;
        }
        include $this->mymtpl('index1');
    }
} elseif ($operation == 'bind' || $operation == 'rbind' || $operation == 'pbind' || $operation == 'prbind') {
    $rid = intval($_GPC['rid']);
    $i = 0;
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        $verifycode = trim($_GPC['verifycode']);
        $config = $this->module['config'];
        @session_start();
        $key = '__rhinfo_zyxq_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
        if (empty($region)) {
            show_json(0, '小区参数不正确');
        }
        if ($operation == 'bind' || $operation == 'rbind') {
            $bankcard = '';
            if ($region['thirdauth'] == 1) {
                $bankcard = trim($_GPC['bankcard']);
                $realname = trim($_GPC['realname']);
                if (empty($bankcard)) {
                    show_json(0, '银行卡号不能为空');
                } elseif (!(strlen($bankcard) == '16' || strlen($bankcard) == '19')) {
                    show_json(0, '卡号不正确');
                }
            }
            if ($region['doorlock_type'] == 4 && !empty($region['aurine_rid'])) {
                if (empty($_GPC['idcard'])) {
                    show_json(0, '身份证号不能为空');
                }
            }
            $sql = 'select *,"" as "ownername1",0 as "otype" ,0 as "is_room_mp" from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and (mobile=:mobile or mobile1=:mobile)';
            $room_data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            $sql = 'select ro.*,mp.ownername as "ownername1",mp.otype as "otype",1 as "is_room_mp" from ' . tablename('rhinfo_zyxq_room_mp') . ' as mp left join ' . tablename('rhinfo_zyxq_room') . ' as ro on mp.hid=ro.id where mp.weid=:weid and mp.rid=:rid and mp.mobile=:mobile';
            $room_data2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            if (!empty($room_data2)) {
                $room_data = array_merge($room_data2, $room_data);
            }
            $k = 0;
            while (!($k >= count($room_data))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid=:openid or uid=:uid) and hid=:hid and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $room_data[$k]['pid'], ':rid' => $room_data[$k]['rid'], ':bid' => $room_data[$k]['bid'], ':tid' => $room_data[$k]['tid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':hid' => $room_data[$k]['id']));
                if (!($total > 0)) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
                    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $room_data[$k]['pid'], ':rid' => $room_data[$k]['rid'], ':bid' => $room_data[$k]['bid']));
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
                    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $room_data[$k]['pid'], ':rid' => $room_data[$k]['rid'], ':bid' => $room_data[$k]['bid'], ':tid' => $room_data[$k]['tid']));
                    if ($room_data[$k]['is_room_mp']) {
                        $ownername = $room_data[$k]['ownername1'];
                        $isowner = 0;
                        $otype = $room_data[$k]['otype'];
                    } else {
                        $ownername = $room_data[$k]['ownername'];
                        $isowner = 1;
                        $otype = 0;
                    }
                    $isdefault = 1;
                    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid=:openid or uid=:uid) and deleted=0 and status=0 and isdefault=1';
                    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
                    if ($total > 1) {
                        $isdefault = 0;
                    }
                    $address = !empty($region['roomfix']) ? $region['title'] . $building . '-' . $unit . '-' . $room_data[$k]['title'] . $region['roomfix'] : $region['title'] . $building . '-' . $unit . '-' . $room_data[$k]['title'];
                    $member_data = array('weid' => $_W['uniacid'], 'pid' => $room_data[$k]['pid'], 'rid' => $room_data[$k]['rid'], 'bid' => $room_data[$k]['bid'], 'tid' => $room_data[$k]['tid'], 'hid' => $room_data[$k]['id'], 'uid' => $_W['member']['uid'], 'isowner' => $isowner, 'otype' => $otype, 'category' => 1, 'openid' => $_W['openid'], 'realname' => $ownername, 'mobile' => $mobile, 'address' => $address, 'isdefault' => $isdefault, 'status' => 0, 'bankcard' => $bankcard, 'idcard' => $_GPC['idcard'], 'carno' => strtoupper($_GPC['carno']), 'ctime' => TIMESTAMP);
                    $userinfo = $_W['fans'];
                    $rrmember_data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'realname' => $ownername, 'mobile' => $mobile, 'nickname' => $userinfo['nickname'], 'avatar' => $userinfo['avatar'], 'province' => $region['province'], 'city' => $region['city'], 'area' => $region['district'], 'nickname_wechat' => $userinfo['nickname'], 'avatar_wechat' => $userinfo['avatar'], 'createtime' => TIMESTAMP);
                    $rrmember_update = array('realname' => $ownername, 'mobile' => $mobile, 'province' => $region['province'], 'city' => $region['city'], 'area' => $region['district']);
                    $member_address = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'province' => $region['province'], 'city' => $region['city'], 'district' => $region['district'], 'isdefault' => 0, 'username' => $ownername, 'mobile' => $mobile, 'address' => $region['title'] . $building . '-' . $unit . '-' . $room_data[$k]['title']);
                    $rrmember_address = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'province' => $region['province'], 'city' => $region['city'], 'area' => $region['district'], 'isdefault' => 0, 'realname' => $ownername, 'mobile' => $mobile, 'address' => $region['title'] . $building . '-' . $unit . '-' . $room_data[$k]['title']);
                    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and hid=:hid and (openid=:openid or uid=:uid)';
                    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $room_data[$k]['pid'], ':rid' => $room_data[$k]['rid'], ':bid' => $room_data[$k]['bid'], ':tid' => $room_data[$k]['tid'], ':hid' => $room_data[$k]['id'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
                    pdo_insert('rhinfo_zyxq_member', $member_data);
                    $memeber_id = pdo_insertid();
                    ($i += 1) + -1;
                    if ($region['doorlock_type'] == 4 && !empty($region['aurine_rid']) && !empty($room_data[$k]['aurine_hid'])) {
                        $doors = $this->get_my_doors($member_data['rid'], $member_data['bid'], $member_data['tid'], 0, 1);
                        $set = array('url' => 'proprietor/add', 'aurine_appid' => $region['aurine_appid'], 'aurine_secret' => $region['aurine_secret'], 'aurine_token' => $region['aurine_token']);
                        $data = array('name' => trim($_GPC['realname']), 'phone' => $mobile, 'id_number' => trim($_GPC['idcard']), 'community_id' => $region['aurine_rid'], 'room_id' => $room_data[$k]['aurine_hid'], 'role' => $isowner == 1 ? 1 : 2, 'sex' => substr($_GPC['idcard'], 0 - 1, 1) % 2 ? 1 : 2);
                        $res = aurine_http_post($set, $data);
                        if ($res['errorCode'] == 1) {
                            $proprietors = $res['body'];
                            $data = array('authStatus' => 1);
                            pdo_update('rhinfo_zyxq_member', array('proprietor_json' => iserializer($proprietors)), array('weid' => $_W['uniacid'], 'id' => $memeber_id));
                            $m = 0;
                            while (!($m >= count($proprietors))) {
                                $set['url'] = 'proprietor/' . $proprietors[$m]['proprietor_id'] . '/auth';
                                $res = aurine_http_post($set, $data);
                                if ($res['errorCode'] != 1) {
                                    $this->mysyslog(0, 'error', 'yundoor', '授权门禁业主' . $mobile . '失败', $res['errorCode'] . ':' . $res['errorMsg']);
                                }
                                ($m += 1) + -1;
                            }
                            $set['url'] = 'community/' . $region['aurine_rid'];
                            $res = aurine_http_post($set);
                            if ($res['errorCode'] == 1) {
                                $services = $res['body']['service'];
                                $m = 0;
                                while (!($m >= count($proprietors))) {
                                    $n = 0;
                                    while (!($n >= count($services))) {
                                        $set['url'] = 'proprietor/' . $proprietors[$m]['proprietor_id'] . '/device/add';
                                        $data = array('service_id' => $services[$n]['id'], 'service_status' => 1, 'batch' => json_encode($doors));
                                        $res = aurine_http_post($set, $data);
                                        if ($res['errorCode'] != 1) {
                                            $this->mysyslog(0, 'error', 'yundoor', '授权门禁业主' . $mobile . '失败', $res['errorCode'] . ':' . $res['errorMsg']);
                                        }
                                        ($n += 1) + -1;
                                    }
                                    ($m += 1) + -1;
                                }
                            } else {
                                $this->mysyslog(0, 'error', 'yundoor', '授权门禁业主' . $mobile . '失败', $res['errorCode'] . ':' . $res['errorMsg']);
                            }
                        } else {
                            $this->mysyslog(0, 'error', 'yundoor', '添加门禁业主' . $mobile . '失败', $res['errorCode'] . ':' . $res['errorMsg']);
                        }
                    }
                    if (($operation == 'bind' || $operation == 'rbind') && $i == 1) {
                        if ($this->syscfg['iswegroup'] == 1) {
                            if (empty($region['groupid'])) {
                                pdo_insert('mc_groups', array('uniacid' => $_W['uniacid'], 'title' => $region['title']));
                                $groupid = pdo_insertid();
                                pdo_update('rhinfo_zyxq_region', array('groupid' => $groupid), array('weid' => $_W['uniacid'], 'id' => $rid));
                            } else {
                                $groupid = $region['groupid'];
                            }
                        } else {
                            $groupid = 0;
                        }
                        $user_data = array('mobile' => $mobile, 'realname' => $ownername, 'groupid' => $groupid);
                        $res = pdo_update('mc_members', $user_data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), 'AND');
                    }
                    if (!($total > 0)) {
                        if ($region['bindcredit'] > 0) {
                            if ($this->syscfg['isplatformcredit']) {
                                if ($region['onhand'] >= $region['bindcredit']) {
                                    $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                                    $crediturl = $this->my_mobileurl($crediturl);
                                    $sql = 'update ' . tablename('rhinfo_zyxq_property') . ' set onhand = onhand - ' . $region['bindcredit'] . ', outqty= outqty + ' . $region['bindcredit'] . ', outtime = ' . TIMESTAMP . ' where  weid = :weid and id=:pid';
                                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':pid' => $room_data[$k]['pid']));
                                    $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set onhand = onhand - ' . $region['bindcredit'] . ', outqty= outqty + ' . $region['bindcredit'] . ', outtime = ' . TIMESTAMP . ' where  weid = :weid and id=:rid';
                                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':rid' => $room_data[$k]['rid']));
                                    $credit_data = array('weid' => $_W['uniacid'], 'pid' => $room_data[$k]['pid'], 'rid' => $room_data[$k]['rid'], 'io' => 2, 'credit' => $region['bindcredit'], 'title' => '绑定房产送积分', 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'status' => 1, 'cuid' => 0, 'ctime' => TIMESTAMP);
                                    pdo_insert('rhinfo_zycj_region_creditlog', $credit_data);
                                    $res = mc_credit_update($_W['member']['uid'], 'credit1', $region['bindcredit'], array(0, '绑定房产,赠送' . $region['bindcredit'] . '积分', 'rhinfo_zyxq'));
                                    if ($res) {
                                        mc_notice_credit1($_W['openid'], $_W['member']['uid'], $region['bindcredit'], '绑定房产,赠送' . $region['bindcredit'] . '积分', $crediturl, '谢谢支持，点击查看详情');
                                    }
                                }
                            } else {
                                $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                                $crediturl = $this->my_mobileurl($crediturl);
                                $res = mc_credit_update($_W['member']['uid'], 'credit1', $region['bindcredit'], array(0, '绑定房产,赠送' . $region['bindcredit'] . '积分', 'rhinfo_zyxq'));
                                if ($res) {
                                    mc_notice_credit1($_W['openid'], $_W['member']['uid'], $region['bindcredit'], '绑定房产,赠送' . $region['bindcredit'] . '积分', $crediturl, '谢谢支持，点击查看详情');
                                }
                            }
                        }
                        if ($region['bindstrategyid'] > 0) {
                            $redpacket = $this->send_redpacket($region['bindstrategyid'], $_W['openid'], 1);
                            if (!is_error($redpacket)) {
                            }
                            $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_share') . ' where to=:to and weid=:weid and status=1';
                            $redshare = pdo_fetch($sql, array(':to' => $_W['openid'], ':weid' => $_W['uniacid']));
                            if (!empty($redshare)) {
                                $shareres = $this->send_redpacket($redshare['redid'], $redshare['from'], 0);
                                if (!is_error($shareres)) {
                                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                                    $postdata = array('first' => array('value' => '有人通过您的邀请，并成功绑定房产'), 'keyword1' => array('value' => $ownername, 'color' => $topcolor), 'keyword2' => array('value' => $region['title'] . $building . '-' . $unit . '-' . $room_data[$k]['title'], 'color' => $textcolor), 'keyword3' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '恭喜您获得一个答谢红包，点击领取吧'));
                                    if (!empty($this->syscfg['redpackettplid'])) {
                                        $this->send_mysendtplnotice($redshare['from'], $this->syscfg['redpackettplid'], $postdata, $shareres['url'], $topcolor);
                                    }
                                }
                            }
                        }
                        $sql = 'select t.*,m.parentid from ' . tablename('rhinfo_zycj_task_member') . ' as m left join ' . tablename('rhinfo_zycj_task') . ' as t on m.taskid=t.id where m.weid=:weid and m.parentid >0 and m.status=0 and t.category=1 and (m.uid=:uid or m.openid=:openid)';
                        $task = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':openid' => $_W['openid']));
                        if (!empty($task)) {
                            $sql = 'select * from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and id=:parentid';
                            $parent = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':taskid' => $task['id'], ':parentid' => $task['parentid']));
                            if (!empty($parent)) {
                                $sql = 'select * from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and parentid=:parentid and status=0 ';
                                $task_members = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':parentid' => $task['parentid']));
                                if (count($task_members) >= $task['persons']) {
                                    if ($task['rewardtype'] == 1) {
                                        $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                                        $crediturl = $this->my_mobileurl($crediturl);
                                        $res = mc_credit_update($parent['uid'], 'credit1', $task['credit1'], array(0, '邀请绑定房产,完成任务赠送' . $task['credit1'] . '积分', 'rhinfo_zyxq'));
                                        if ($res) {
                                            mc_notice_credit1($parent['openid'], $parent['uid'], $task['credit1'], '邀请绑定房产,完成任务赠送' . $task['credit1'] . '积分', $crediturl, '谢谢支持，点击查看详情');
                                        }
                                    } elseif ($task['rewardtype'] == 2) {
                                        $res = mc_credit_update($parent['uid'], 'credit2', $task['credit2'], array(0, '邀请绑定房产,完成任务赠送' . $task['credit2'] . '元', 'rhinfo_zyxq'));
                                        if ($res) {
                                            mc_notice_credit2($parent['openid'], $parent['uid'], $task['credit2'], '邀请绑定房产,完成任务赠送' . $task['credit2'] . '元', $crediturl, '谢谢支持，点击查看详情');
                                        }
                                    } elseif ($task['rewardtype'] == 3) {
                                        $amount = 0;
                                        if ($task['redcate'] == 1) {
                                            $amount = intval($task['amount']);
                                        } elseif ($task['redcate'] == 2) {
                                            if ($task['minamount'] > 0 && $task['maxamount'] > 0) {
                                                $amount = mt_rand($task['minamount'], $task['maxamount']);
                                            } else {
                                                $amount = 0;
                                            }
                                        }
                                        $params = array('send_name' => empty($task['from']) ? $_W['account']['name'] : $task['from'], 'openid' => $parent['openid'], 'total_amount' => $amount, 'total_num' => 1, 'wishing' => empty($task['wishing']) ? '恭喜发财' : $task['wishing'], 'act_name' => $parent['title'], 'remark' => $task['title'], 'siteip' => $_SERVER['SERVER_ADDR']);
                                        load()->model('payment');
                                        $setting = uni_setting($_W['uniacid'], array('payment'));
                                        if (is_array($setting['payment'])) {
                                            $wechat = $setting['payment']['wechat'];
                                            $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
                                            $row = pdo_fetch($sql, array(':acid' => $wechat['account']));
                                            $mywechat['appid'] = $row['key'];
                                            $mywechat['mch_id'] = $wechat['mchid'];
                                            $mywechat['signkey'] = $wechat['apikey'];
                                            $mywechat['apiclient_cert'] = $this->syscfg['apiclient_cert'];
                                            $mywechat['apiclient_key'] = $this->syscfg['apiclient_key'];
                                            $mywechat['rootca'] = $this->syscfg['rootca'];
                                            $res = my_wechat_paymoney($params, $mywechat);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($this->syscfg['isweaddress']) {
                        pdo_insert('mc_member_address', $member_address);
                    }
                    if ($this->syscfg['isrraddress']) {
                        if (pdo_tableexists('ewei_shop_member_address')) {
                            pdo_insert('ewei_shop_member_address', $rrmember_address);
                        } elseif (pdo_tableexists('zwei_shop_member_address')) {
                            pdo_insert('zwei_shop_member_address', $rrmember_address);
                        }
                        if (pdo_tableexists('ewei_shop_member')) {
                            $rrinfo = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                            if (empty($rrinfo) && !empty($_W['openid'])) {
                                pdo_insert('ewei_shop_member', $rrmember_data);
                            }
                            if (!empty($rrinfo) && !empty($_W['openid'])) {
                                if (empty($rrinfo['mobile']) || empty($rrinfo['realname'])) {
                                    pdo_update('ewei_shop_member', $rrmember_update, array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
                                }
                            }
                        } elseif (pdo_tableexists('zwei_shop_member')) {
                            $rrinfo = pdo_fetch('select * from ' . tablename('zwei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                            if (empty($rrinfo) && !empty($_W['openid'])) {
                                pdo_insert('zwei_shop_member', $rrmember_data);
                            }
                            if (!empty($rrinfo) && !empty($_W['openid'])) {
                                if (empty($rrinfo['mobile']) || empty($rrinfo['realname'])) {
                                    pdo_update('zwei_shop_member', $rrmember_update, array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
                                }
                            }
                        }
                    }
                }
                ($k += 1) + -1;
            }
        }
        if ($operation == 'pbind' || $operation == 'prbind') {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_garage') . ' where weid = :weid and rid = :rid and mobile = :mobile';
            $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            $k = 0;
            while (!($k >= count($data))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=0 and (openid=:openid or uid=:uid) and hid=:hid and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':hid' => $data[$k]['id']));
                if (!($total > 0)) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
                    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
                    $data[$k]['roomname'] = $building . '-' . $data[$k]['title'];
                    $member_data = array('weid' => $_W['uniacid'], 'pid' => $data[$k]['pid'], 'rid' => $data[$k]['rid'], 'bid' => $data[$k]['bid'], 'tid' => 0, 'category' => 2, 'isowner' => 1, 'otype' => 0, 'hid' => $data[$k]['id'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'realname' => $data[$k]['ownername'], 'mobile' => $mobile, 'address' => $region['title'] . $data[$k]['roomname'], 'carno' => strtoupper($_GPC['carno']), 'status' => 0, 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zyxq_member', $member_data);
                    ($i += 1) + -1;
                }
                ($k += 1) + -1;
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and rid = :rid and mobile = :mobile';
            $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            $k = 0;
            while (!($k >= count($data))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=0 and (openid=:openid or uid=:uid) and hid=:hid and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['lid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':hid' => $data[$k]['id']));
                if (!($total > 0)) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
                    $location = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
                    $data[$k]['roomname'] = $location . '-' . $data[$k]['title'];
                    $carno = !empty($data[$k]['carno']) ? $data[$k]['carno'] : $_GPC['carno'];
                    $carno = strtoupper($carno);
                    $member_data = array('weid' => $_W['uniacid'], 'pid' => $data[$k]['pid'], 'rid' => $data[$k]['rid'], 'bid' => $data[$k]['lid'], 'tid' => 0, 'category' => 3, 'isowner' => 1, 'otype' => 0, 'hid' => $data[$k]['id'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'realname' => $data[$k]['ownername'], 'mobile' => $mobile, 'address' => $region['title'] . $data[$k]['roomname'], 'carno' => $carno, 'status' => 0, 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zyxq_member', $member_data);
                    ($i += 1) + -1;
                }
                ($k += 1) + -1;
            }
        }
        if ($operation == 'bind' || $operation == 'rbind') {
            $sql = 'select *,"" as "ownername1",0 as "otype",0 as "is_shop_mp" FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and rid = :rid and (mobile = :mobile or mobile=:mobile)';
            $shop_data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            $sql = 'select sh.*, mp.ownername as "ownername1", mp.otype as "otype", 1 as "is_shop_mp" FROM ' . tablename('rhinfo_zyxq_shop_mp') . ' as mp left join ' . tablename('rhinfo_zyxq_shop') . ' as sh on mp.sid=sh.id where mp.weid = :weid and mp.rid=:rid and mp.mobile=:mobile';
            $shop_data2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            if (!empty($shop_data2)) {
                $shop_data = array_merge($shop_data2, $shop_data);
            }
            $k = 0;
            while (!($k >= count($shop_data))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=0 and openid=:openid and hid=:hid and deleted=0 and status=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $shop_data[$k]['pid'], ':rid' => $shop_data[$k]['rid'], ':bid' => $shop_data[$k]['lid'], ':openid' => $_W['openid'], ':hid' => $shop_data[$k]['id']));
                if (!($total > 0)) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
                    $location = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $shop_data[$k]['pid'], ':rid' => $shop_data[$k]['rid'], ':lid' => $shop_data[$k]['lid']));
                    $shop_data[$k]['roomname'] = $location . '-' . $shop_data[$k]['title'];
                    if ($shop_data[$k]['is_shop_mp']) {
                        $ownername = $shop_data[$k]['ownername1'];
                        $isowner = 0;
                        $otype = $shop_data[$k]['otype'];
                    } else {
                        $ownername = $shop_data[$k]['ownername'];
                        $isowner = 1;
                        $otype = 0;
                    }
                    $member_data = array('weid' => $_W['uniacid'], 'pid' => $shop_data[$k]['pid'], 'rid' => $shop_data[$k]['rid'], 'bid' => $shop_data[$k]['lid'], 'tid' => 0, 'category' => 1, 'isowner' => $isowner, 'otype' => $otype, 'hid' => $shop_data[$k]['id'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'realname' => $ownername, 'mobile' => $mobile, 'address' => $region['title'] . $shop_data[$k]['roomname'], 'carno' => strtoupper($_GPC['carno']), 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zyxq_member', $member_data);
                    ($i += 1) + -1;
                }
                ($k += 1) + -1;
            }
        }
        if ($i > 0) {
            if (!empty($_GPC['carno'])) {
                $car_data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $region['id'], 'carno' => strtoupper($_GPC['carno']), 'title' => strtoupper($_GPC['carno']), 'ownername' => $ownername, 'mobile' => $mobile, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                $mycar_data = array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'carno' => strtoupper($_GPC['carno']), 'ctime' => TIMESTAMP);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid=:weid and pid=:pid and rid=:rid and carno=:carno and deleted=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id'], ':carno' => strtoupper($_GPC['carno'])));
                if ($total == 0) {
                    pdo_insert('rhinfo_zyxq_car', $car_data);
                }
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and (openid=:openid or uid=:uid) and carno=:carno';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':carno' => strtoupper($_GPC['carno'])));
                if ($total == 0) {
                    pdo_insert('rhinfo_zyxq_mycar', $mycar_data);
                }
            }
            if (!empty($_W['setting']['site']['key']) && $this->syscfg['isworkersound']) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and rid=:rid and uid =0';
                $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id']));
                if (empty($secuser)) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and uid=0';
                    $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid']));
                }
                $m = 0;
                while (!($m >= count($secuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'property_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $secuser[$k]['id'], '您有新的绑定人员，请注意确认.');
                    ($m += 1) + -1;
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and uid >0';
                $weuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
                $m = 0;
                while (!($m >= count($weuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'rhinfo_zyxq_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $weuser[$k]['uid'], '您有新的绑定人员，请注意确认.');
                    ($m += 1) + -1;
                }
            }
            if (!empty($redpacket['url'])) {
                show_json(2, array('message' => '绑定成功', 'url' => $redpacket['url']));
            } else {
                show_json(1, '绑定成功');
            }
        } else {
            show_json(0, '绑定不成功或已经绑定过.');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select r.title,r.regbanner,r.regbannerurl, r.bindsuccessurl,r.bindstrategyid,r.thirdauth,r.thirdurl,r.aurine_rid,r.doorlock_type, p.image from ' . tablename('rhinfo_zyxq_region') . ' as r left join ' . tablename('rhinfo_zyxq_property') . ' as p on r.pid= p.id where r.weid=:weid and r.id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    if ($region['thirdauth'] == 1) {
        include $this->mymtpl('thirdauth');
    } elseif ($region['doorlock_type'] == 4 && !empty($region['aurine_rid'])) {
        include $this->mymtpl('doorauth');
    } else {
        include $this->mymtpl($operation);
    }
} elseif ($operation == 'chgbind') {
    $rid = intval($_GPC['rid']);
    if ($_W['ispost']) {
        $mobile = trim($_GPC['mobile']);
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $user_data = array('mobile' => $mobile);
        $res = pdo_update('mc_members', $user_data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), 'AND');
        if ($res) {
            show_json(1, '修改成功!');
        } else {
            show_json(0, '修改失败!');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select r.title,r.regbanner,p.image from ' . tablename('rhinfo_zyxq_region') . ' as r left join ' . tablename('rhinfo_zyxq_property') . ' as p on r.pid= p.id where r.weid=:weid and r.id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('chgbind');
} elseif ($operation == 'verifycode') {
    $mobile = trim($_GPC['mobile']);
    $temp = trim($_GPC['temp']);
    $rid = trim($_GPC['rid']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    if (empty($temp)) {
        show_json(0, '参数错误');
    }
    if (!($temp == 'sms_chgbind')) {
        if (empty($rid)) {
            show_json(0, '参数缺失');
        }
        if ($temp == '1') {
            $i = 0;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and (mobile=:mobile or mobile1=:mobile)';
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            $sql = 'select ro.* from ' . tablename('rhinfo_zyxq_room_mp') . ' as mp left join ' . tablename('rhinfo_zyxq_room') . ' as ro on mp.hid=ro.id where mp.weid=:weid and mp.rid=:rid and mp.mobile=:mobile';
            $rooms1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            if (!empty($rooms1)) {
                $rooms = array_merge($rooms1, $rooms);
            }
            $k = 0;
            while (!($k >= count($rooms))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and (openid=:openid or uid=:uid) and deleted=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $rooms[$k]['bid'], ':tid' => $rooms[$k]['tid'], ':hid' => $rooms[$k]['id'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
                if (!($total > 0)) {
                    ($i += 1) + -1;
                }
                ($k += 1) + -1;
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and rid = :rid and (mobile = :mobile or mobile=:mobile)';
            $shops = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            $sql = 'SELECT sh.* FROM ' . tablename('rhinfo_zyxq_shop_mp') . ' as mp left join ' . tablename('rhinfo_zyxq_shop') . ' as sh on mp.sid=sh.id where mp.weid = :weid and mp.rid=:rid and mp.mobile=:mobile';
            $shops1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            if (!empty($shops1)) {
                $shops = array_merge($shops1, $shops);
            }
            $k = 0;
            while (!($k >= count($shops))) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and bid=:bid and tid=0 and hid=:hid and (openid=:openid or uid=:uid) and deleted=0 ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $shops[$k]['lid'], ':hid' => $shops[$k]['id'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
                if (!($total > 0)) {
                    ($i += 1) + -1;
                }
                ($k += 1) + -1;
            }
            if ($i == 0) {
                show_json(0, '该手机号还未登记，为保证您的房产安全，请联系平台客服');
            }
        }
        if ($temp == '2') {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and rid=:rid and mobile=:mobile';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            if ($total == 0) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_garage') . ' where weid=:weid and rid=:rid and mobile=:mobile';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':mobile' => $mobile));
            }
            if ($total == 0) {
                show_json(0, '该手机号还没登记车位');
            }
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0) && $region['bindverify'] == 1) {
        show_json(0, '可发短信数量为0,请联系物业');
    }
    $key = '__rhinfo_zyxq_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['bindverify'] == 1 && $region['bindverify'] == 1) {
        if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
            $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
        } else {
            show_json(0, '短信参数配置错误');
        }
        if ($ret['status']) {
            $_SESSION[$key] = $code;
            $_SESSION['verifycodesendtime'] = time();
            $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
            pdo_query($sql, array(':weid' => $_W['uniacid'], ':rid' => $region['id']));
            $smslog_data = array('weid' => $_W['uniacid'], 'rid' => $region['id'], 'title' => '绑定房产', 'io' => 2, 'mobile' => $mobile, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
            show_json(1, '短信发送成功');
        }
        $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败');
        show_json(0, $ret['message']);
    } else {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(2, $code);
    }
} elseif ($operation == 'shareverifycode') {
    $mobile = trim($_GPC['mobile']);
    $temp = trim($_GPC['temp']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    if (empty($temp)) {
        show_json(0, '参数错误');
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0)) {
        show_json(0, '可发短信数量为0,请联系物业');
    }
    $key = '__rhinfo_zyxq_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['shareverify'] == 1 && $region['shareverify'] == 1) {
        if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
            $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
        } else {
            show_json(0, '短信参数配置错误');
        }
        if ($ret['status']) {
            $_SESSION[$key] = $code;
            $_SESSION['verifycodesendtime'] = time();
            $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
            pdo_query($sql, array(':weid' => $_W['uniacid'], ':rid' => $region['id']));
            $smslog_data = array('weid' => $_W['uniacid'], 'rid' => $region['id'], 'title' => '邀请绑定', 'io' => 2, 'mobile' => $mobile, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
            show_json(1, '短信发送成功');
        }
        $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败');
        show_json(0, $ret['message']);
    } else {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(0, '请输入验证码:' . $code);
    }
} elseif ($operation == 'myhouse') {
    $category = empty($_GPC['category']) ? 1 : $_GPC['category'];
    if ($_W['minirid']) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and (openid=:openid or uid=:uid) and category = :category and deleted=0 and status=0 ORDER BY isdefault desc, id DESC ';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_W['minirid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':category' => $category));
    } else {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid=:openid or uid=:uid) and category = :category and deleted=0 and status=0 ORDER BY isdefault desc, id DESC ';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':category' => $category));
    }
    $k = 0;
    while (!($k >= count($list))) {
        if ($list[$k]['isowner']) {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where isowner=0 and deleted=0 and category=1 and weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':bid' => $list[$k]['bid'], ':tid' => $list[$k]['tid'], ':hid' => $list[$k]['hid']));
            $list[$k]['count'] = $count;
        }
        ($k += 1) + -1;
    }
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $item['id'];
        if ($category == 1) {
            if ($item['register'] == 1) {
                $url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $rid, 'register' => 1));
            } elseif ($item['register'] == 2) {
                $url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $rid, 'register' => 2));
            } else {
                if (!empty($user['mobile'])) {
                    $bind = 'bind';
                } else {
                    $bind = 'rbind';
                }
                $url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $rid));
            }
        } else {
            if (!empty($user['mobile'])) {
                $bind = 'pbind';
            } else {
                $bind = 'prbind';
            }
            $url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $rid));
        }
    } elseif ($category == 1) {
        $url = $this->createMobileurl('home', array('op' => 'blist'));
    } else {
        $url = $this->createMobileurl('home', array('op' => 'plist'));
    }
    include $this->mymtpl('house');
} elseif ($operation == 'myhousemember') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where isowner=0 and category=1 and weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
    $k = 0;
    while (!($k >= count($list))) {
        if ($list[$k]['openid'] || $list[$k]['uid']) {
            $fans = array();
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('housemember');
} elseif ($operation == 'sharehouse') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid = :openid or uid=:uid) and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':id' => $id));
    if (!empty($item)) {
        $data = array('weid' => $_W['uniacid'], 'parentid' => $id, 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['hid'], 'address' => $item['address'], 'otype' => $_GPC['otype'], 'effedate' => $_GPC['effedate'], 'remark' => $item['remark'], 'status' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_member_sub', $data);
        $subid = pdo_insertid();
        $qrcode_url = $this->createMobileurl($mydo, array('op' => 'gethouse', 'id' => $subid));
        $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
        $qrcode_url = $siteurl . substr($qrcode_url, 2);
        $_share['title'] = $_W['fans']['nickname'] . '邀请您绑定房屋';
        $_share['imgUrl'] = MODULE_URL . 'static/mobile/images/house.jpg';
        $_share['desc'] = '快速绑定房屋，方便快捷享受物业服务.';
        $_share['link'] = $qrcode_url;
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
    include $this->mymtpl('sharehouse');
} elseif ($operation == 'shareotype') {
    $id = intval($_GPC['id']);
    include $this->mymtpl('shareotype');
} elseif ($operation == 'gethouse') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member_sub') . ' where status=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['ispost']) {
        if (!empty($item)) {
            if (!empty($item['effedate']) && !($item['effedate'] >= date('Y-m-d'))) {
                show_json(0, '该邀请已经过期');
            }
            $member_data = array('weid' => $_W['uniacid'], 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['hid'], 'uid' => $_W['member']['uid'], 'otype' => $item['otype'], 'category' => 1, 'openid' => $_W['openid'], 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'address' => $item['address'], 'ctime' => TIMESTAMP);
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and (openid=:openid or uid=:uid) and deleted=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            if ($total > 0) {
                show_json(0, '您已经绑定该房产');
            }
            pdo_insert('rhinfo_zyxq_member', $member_data);
            pdo_update('rhinfo_zyxq_member_sub', array('status' => 1), array('weid' => $_W['uniacid'], 'id' => $id));
            show_json(1);
        } else {
            show_json(0, '该邀请已经失效');
        }
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
    include $this->mymtpl('sharehouse');
} elseif ($operation == 'myparking') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where category=3 and weid = :weid and (openid = :openid or uid=:uid) and deleted=0 and status=0 ORDER BY isdefault desc, id DESC ';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and id=:id';
        $parking = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['hid']));
        $list[$k]['parking'] = $parking;
        $sql = 'select feecontrol,feeshowtype,parklock_type, parklock_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
        if ($parking['category'] == 1) {
            $sql = 'select sum(fee-payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and rid=:rid and bid=:bid and hid=:hid and status=1 and category=4';
            $list[$k]['fee'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid'], ':bid' => $list[$k]['bid'], ':hid' => $list[$k]['hid']));
            if ($region['feeshowtype'] == 1) {
                $list[$k]['url'] = $this->createMobileurl('fee', array('op' => 'yearbill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid']));
            } else {
                $list[$k]['url'] = $this->createMobileurl('fee', array('op' => 'myfeebill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid']));
            }
        } else {
            $sql = 'select sum(fee-payfee) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and status=1';
            $list[$k]['fee'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $parking['pid'], ':rid' => $parking['rid'], ':lid' => $parking['lid'], ':cid' => $parking['id']));
            $list[$k]['url'] = $this->CreateMobileUrl('service', array('op' => 'carbill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'lid' => $list[$k]['lid'], 'cid' => $list[$k]['hid']));
        }
        if ($region['parklock_type'] == 1) {
            $set = array('url' => 'getLock', 'token' => $region['parklock_token']);
            $data = '/' . $parking['lockmac'];
            $res = pshare_http_post2($set, $data);
            if ($res['code'] == 200) {
                $list[$k]['parklock'] = $res['data'];
            }
        }
        ($k += 1) + -1;
    }
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $region['id'];
        if (!empty($user['mobile'])) {
            $bind = 'pbind';
        } else {
            $bind = 'prbind';
        }
        $url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $rid));
    } else {
        $url = $this->createMobileurl('home', array('op' => 'plist'));
    }
    include $this->mymtpl('parking');
} elseif ($operation == 'setdefault') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where id=:id and weid=:weid limit 1';
    $data = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (empty($data)) {
        show_json(0, '房产信息未找到');
    }
    pdo_update('rhinfo_zyxq_member', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
    pdo_update('rhinfo_zyxq_member', array('isdefault' => 1), array('id' => $id, 'weid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
    show_json(1);
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $res = pdo_update('rhinfo_zyxq_member', array('deleted' => 1), array('id' => $id, 'weid' => $_W['uniacid']));
    if ($res) {
        show_json(1);
    }
    show_json(0, '删除失败');
} elseif ($operation == 'follow') {
    $rid = $_GPC['rid'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region_follow') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid) limit 1';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (empty($item)) {
        $data = array('weid' => $_W['uniacid'], 'pid' => 0, 'rid' => $rid, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_region_follow', $data);
        $id = pdo_insertid();
        if ($id) {
            show_json(1, '关注成功!');
        } else {
            show_json(0, '关注失败!');
        }
    } else {
        pdo_update('rhinfo_zyxq_region_follow', array('deleted' => 0), array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'rid' => $rid));
        show_json(1, '关注成功!');
    }
} elseif ($operation == 'myfav') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region_follow') . ' where deleted=0 and weid = :weid and (openid = :openid or uid=:uid)';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
        $list[$k]['thumb'] = $region['thumb'];
        $list[$k]['title'] = $region['title'];
        $list[$k]['address'] = $region['address'];
        if (empty($list[$k]['url'])) {
            $list[$k]['url'] = $this->createMobileUrl('home', array('op' => 'index', 'rid' => $list[$k]['rid']));
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('favorite');
} elseif ($operation == 'removefav') {
    $ids = $_GPC['ids'];
    if (empty($ids) || !is_array($ids)) {
        show_json(0, '参数错误');
    }
    $sql = 'update ' . tablename('rhinfo_zyxq_region_follow') . ' set deleted=1 where weid=:weid and (openid=:openid or uid=:uid) and id in (' . implode(',', $ids) . ')';
    pdo_query($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    show_json(1);
} elseif ($operation == 'removemember') {
    $ids = $_GPC['ids'];
    if (empty($ids) || !is_array($ids)) {
        show_json(0, '参数错误');
    }
    $sql = 'update ' . tablename('rhinfo_zyxq_member') . ' set deleted=1 where weid=:weid and id in (' . implode(',', $ids) . ')';
    pdo_query($sql, array(':weid' => $_W['uniacid']));
    show_json(1);
} elseif ($operation == 'removevisit') {
    $ids = $_GPC['ids'];
    if (empty($ids) || !is_array($ids)) {
        show_json(0, '参数错误');
    }
    $sql = 'delete from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid=:weid and id in (' . implode(',', $ids) . ')';
    pdo_query($sql, array(':weid' => $_W['uniacid']));
    show_json(1);
} elseif ($operation == 'myfee') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid = :openid or uid=:uid) and deleted=0 and status=0 order by category';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'SELECT sum(fee - payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and pid=:pid and rid = :rid and bid=:bid and tid=:tid and hid=:hid and status=1';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':tid' => $list[$k]['tid'], ':bid' => $list[$k]['bid'], ':hid' => $list[$k]['hid']));
        $list[$k]['fee'] = $total;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
        $list[$k]['thumb'] = $region['thumb'];
        $list[$k]['title'] = $region['title'];
        if ($region['feeshowtype'] == 1) {
            $list[$k]['url'] = $this->createMobileurl('fee', array('op' => 'yearbill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid']));
        } else {
            $list[$k]['url'] = $this->createMobileurl('fee', array('op' => 'myfeebill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid']));
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('fee');
} elseif ($operation == 'myfeebill') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and deleted=0 and status=0';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
    if ($member['category'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':hid' => $_GPC['hid']));
    }
    if ($member['category'] == 2) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':id' => $_GPC['hid']));
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid order by startdate';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
    $totalfee = 0;
    $tempbillid = array();
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
        $feeitem = pdo_fetch($sql, array(':id' => $list[$k]['itemid'], ':weid' => $_W['uniacid']));
        if (!empty($feeitem['latedate'])) {
            if ($list[$k]['enddate'] > $feeitem['latedate']) {
                $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
            } else {
                $days = intval((strtotime(date('Y-m-d')) - $feeitem['latedate']) / 86400);
            }
        } else {
            $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
        }
        if ($days > $feeitem['latedays']) {
            if ($region['latemethod'] == 1) {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * ($days - $feeitem['latedays']) / 1000, 0);
            } else {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * $days / 1000, 0);
            }
        }
        $totalfee += $list[$k]['fee'] + $list[$k]['latefee'];
        array_push($tempbillid, $list[$k]['id']);
        ($k += 1) + -1;
    }
    $billid = implode(',', $tempbillid);
    $totalfee = round($totalfee, 2);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid']));
    $iscreditpay = 0;
    $creditset = array();
    if ($region['payrate'] > 0 && $region['paycost'] > 0 && $region['paycredit']) {
        $iscreditpay = 1;
        $creditset['paycost'] = $region['paycost'];
        $creditset['paycredit'] = $region['paycredit'];
        $creditset['payrate'] = $region['payrate'];
    }
    if ($iscreditpay == 1) {
        $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors'));
        $behavior = $setting['creditbehaviors'];
        $credits = mc_credit_fetch($_W['member']['uid'], '*');
        $creditset['mycredit'] = empty($credits[$behavior['activity']]) ? 0 : $credits[$behavior['activity']];
        if ($creditset['mycredit'] >= $creditset['paycredit']) {
            $creditcost = $creditset['mycredit'] * $creditset['paycost'] / $creditset['paycredit'];
            $creditcost = $creditcost > intval($totalfee * $creditset['payrate'] / 100) ? intval($totalfee * $creditset['payrate'] / 100) : $creditcost;
        }
    }
    include $this->mymtpl('feebill');
} elseif ($operation == 'myhisfeebill') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and deleted=0 and status=0';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
    if ($member['category'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':hid' => $_GPC['hid']));
    }
    if ($member['category'] == 2) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':id' => $_GPC['hid']));
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status>1 and weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid order by startdate desc';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and pid=:pid and rid = :rid and bid=:bid and tid=:tid and hid=:hid and status>1';
    $totalfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
    include $this->mymtpl('hisfeebill');
} elseif ($operation == 'currfee') {
    $itemid = $_GPC['itemid'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid=:openid or uid=:uid) and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and deleted=0';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':tid' => $user['tid'], ':hid' => $user['hid']));
    if ($member['category'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':hid' => $user['hid']));
    }
    if ($member['category'] == 2) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :bid and id=:hid';
        $parking = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':id' => $user['hid']));
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and fee - payfee > 0 and weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and itemid=:itemid';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':tid' => $user['tid'], ':hid' => $user['hid'], ':itemid' => $itemid));
    $totalfee = 0;
    $tempbillid = array();
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
        $feeitem = pdo_fetch($sql, array(':id' => $list[$k]['itemid'], ':weid' => $_W['uniacid']));
        if (!empty($feeitem['latedate'])) {
            if ($list[$k]['enddate'] > $feeitem['latedate']) {
                $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
            } else {
                $days = intval((strtotime(date('Y-m-d')) - $feeitem['latedate']) / 86400);
            }
        } else {
            $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
        }
        if ($days > $feeitem['latedays']) {
            if ($region['latemethod'] == 1) {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * ($days - $feeitem['latedays']) / 1000, 0);
            } else {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * $days / 1000, 0);
            }
        }
        $totalfee += $list[$k]['fee'] + $list[$k]['latefee'];
        array_push($tempbillid, $list[$k]['id']);
        ($k += 1) + -1;
    }
    $billid = implode(',', $tempbillid);
    $totalfee = round($totalfee, 2);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid']));
    $iscreditpay = 0;
    $creditset = array();
    if ($region['payrate'] > 0 && $region['paycost'] > 0 && $region['paycredit']) {
        $iscreditpay = 1;
        $creditset['paycost'] = $region['paycost'];
        $creditset['paycredit'] = $region['paycredit'];
        $creditset['payrate'] = $region['payrate'];
    }
    if ($iscreditpay == 1) {
        $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors'));
        $behavior = $setting['creditbehaviors'];
        $credits = mc_credit_fetch($_W['member']['uid'], '*');
        $creditset['mycredit'] = empty($credits[$behavior['activity']]) ? 0 : $credits[$behavior['activity']];
        if ($creditset['mycredit'] >= $creditset['paycredit']) {
            $creditcost = $creditset['mycredit'] * $creditset['paycost'] / $creditset['paycredit'];
            $creditcost = $creditcost > intval($totalfee * $creditset['payrate'] / 100) ? intval($totalfee * $creditset['payrate'] / 100) : $creditcost;
        }
    }
    include $this->mymtpl('currfeebill');
} elseif ($operation == 'reg') {
    $rid = intval($_GPC['rid']);
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select r.title,r.regbanner,r.regbannerurl,r.bindsuccessurl,r.thirdauth,r.thirdurl,r.roomfix, p.image from ' . tablename('rhinfo_zyxq_region') . ' as r left join ' . tablename('rhinfo_zyxq_property') . ' as p on r.pid= p.id where r.weid=:weid and r.id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid order by title,id';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid order by title,id';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title*1';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title*1';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    if ($region['thirdauth'] == 1) {
        include $this->mymtpl('thirdreg');
    } else {
        include $this->mymtpl('reg');
    }
} elseif ($operation == 'selfreg') {
    $rid = intval($_GPC['rid']);
    $roomid = $_GPC['roomid'];
    $roomname = $_GPC['roomname'];
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select r.title,r.regbanner,r.regbannerurl, r.bindsuccessurl,r.bindstrategyid,r.thirdauth,r.thirdurl, r.roomfix,p.image from ' . tablename('rhinfo_zyxq_region') . ' as r left join ' . tablename('rhinfo_zyxq_property') . ' as p on r.pid= p.id where r.weid=:weid and r.id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid order by title,id';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid order by title,id';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title*1';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title*1';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    if ($region['thirdauth'] == 1) {
        include $this->mymtpl('thirdselfreg');
    } else {
        include $this->mymtpl('selfreg');
    }
} elseif ($operation == 'roomreg') {
    $rid = intval($_GPC['rid']);
    if ($_W['ispost']) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
        if (!empty($region)) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid and title like "%' . $_GPC['building'] . '%"';
            $building_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
            if ($total > 0) {
                $bid = $building_item['id'];
            } else {
                pdo_insert('rhinfo_zyxq_building', array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $rid, 'title' => $_GPC['building'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP));
                $bid = pdo_insertid();
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid=:bid and title like "%' . $_GPC['unit'] . '%"';
            $unit_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $bid));
            if ($total > 0) {
                $tid = $unit_item['id'];
            } else {
                pdo_insert('rhinfo_zyxq_unit', array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $rid, 'bid' => $bid, 'title' => $_GPC['unit'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP));
                $tid = pdo_insertid();
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid=:bid and tid=:tid and title like "%' . $_GPC['unit'] . '%"';
            $room_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':bid' => $bid, ':tid' => $tid));
            if ($total > 0) {
                $hid = $room_item['id'];
            } else {
                pdo_insert('rhinfo_zyxq_room', array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $rid, 'bid' => $bid, 'tid' => $tid, 'title' => $_GPC['room'], 'floor' => $_GPC['floor'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP));
                $hid = pdo_insertid();
            }
            $this->mysyslog($region['pid'], '自主注册房产', $operation, '绑定房产', '房产绑定id=' . $hid);
            header('Location:' . $this->createMobileurl($mydo, array('op' => 'selfreg', 'rid' => $rid, 'roomid' => $bid . '-' . $tid . '-' . $hid, 'roomname' => $_GPC['building'] . '-' . $_GPC['unit'] . '-' . $_GPC['room'])));
            exit(0);
        } else {
            $this->mymsg('error', '小区或商圈不存在', '请联系平台客服', 'close');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('roomreg');
} elseif ($operation == 'mybind') {
    $rid = intval($_GPC['rid']);
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
        if ($this->syscfg['iswegroup'] == 1) {
            if (empty($region['groupid'])) {
                pdo_insert('mc_groups', array('uniacid' => $_W['uniacid'], 'title' => $region['title']));
                $groupid = pdo_insertid();
                pdo_update('rhinfo_zyxq_region', array('groupid' => $groupid), array('weid' => $_W['uniacid'], 'id' => $rid));
            } else {
                $groupid = $region['groupid'];
            }
        } else {
            $groupid = 0;
        }
        $myroom = explode('-', $_GPC['myroom']);
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid=:openid or uid=:uid) and hid=:hid and deleted=0';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $rid, ':bid' => $myroom[0], ':tid' => $myroom[1], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':hid' => $myroom[2]));
        if ($total > 0) {
            show_json(0, '您已经绑定过.');
        }
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and id=:bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $myroom[0]));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid=:weid and id=:tid';
        $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':tid' => $myroom[1]));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':hid' => $myroom[2]));
        $ownername = !empty($_GPC['ownername']) ? $_GPC['ownername'] : $user['realname'];
        $otype = intval($_GPC['otype']);
        if ($otype == 0) {
            $isowner = 1;
        } else {
            $isowner = 0;
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid=:openid or uid=:uid) and hid=:hid and deleted=0 and isowner=1';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $rid, ':bid' => $myroom[0], ':tid' => $myroom[1], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':hid' => $myroom[2]));
        if ($total > 0) {
            $isowner = 0;
        }
        if ($region['register'] == 1) {
            $status = 1;
        } else {
            $status = 0;
        }
        $config = $this->module['config'];
        $bankcard = '';
        if ($region['thirdauth'] == 1) {
            $bankcard = trim($_GPC['bankcard']);
            $realname = trim($_GPC['ownername']);
            if (empty($bankcard)) {
                show_json(0, '银行卡号不能为空');
            } elseif (!(strlen($bankcard) == '16' || strlen($bankcard) == '19')) {
                show_json(0, '卡号不正确');
            }
        }
        $isdefault = 1;
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and (openid=:openid or uid=:uid) and deleted=0 and status=0 and isdefault=1';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        if ($total > 1) {
            $isdefault = 0;
        }
        $address = !empty($region['roomfix']) ? $region['title'] . $building . '-' . $unit . '-' . $room['title'] . $region['roomfix'] : $region['title'] . $building . '-' . $unit . '-' . $room['title'];
        $member_data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $rid, 'bid' => $myroom[0], 'tid' => $myroom[1], 'hid' => $myroom[2], 'uid' => $_W['member']['uid'], 'isowner' => $isowner, 'otype' => $otype, 'category' => 1, 'openid' => $_W['openid'], 'realname' => $ownername, 'mobile' => $mobile, 'address' => $address, 'isdefault' => $isdefault, 'bankcard' => $bankcard, 'carno' => strtoupper($_GPC['carno']), 'status' => $status, 'ctime' => TIMESTAMP);
        $userinfo = $_W['fans'];
        $rrmember_data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'realname' => $ownername, 'mobile' => $mobile, 'nickname' => $userinfo['nickname'], 'avatar' => $userinfo['avatar'], 'province' => $region['province'], 'city' => $region['city'], 'area' => $region['district'], 'nickname_wechat' => $userinfo['nickname'], 'avatar_wechat' => $userinfo['avatar'], 'createtime' => TIMESTAMP);
        $rrmember_update = array('realname' => $ownername, 'mobile' => $mobile, 'province' => $region['province'], 'city' => $region['city'], 'area' => $region['district']);
        $member_address = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'province' => $region['province'], 'city' => $region['city'], 'district' => $region['district'], 'isdefault' => 0, 'username' => $ownername, 'mobile' => $mobile, 'address' => $region['title'] . $building . '-' . $unit . '-' . $room['title']);
        $rrmember_address = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'province' => $region['province'], 'city' => $region['city'], 'area' => $region['district'], 'isdefault' => 0, 'realname' => $ownername, 'mobile' => $mobile, 'address' => $address);
        pdo_insert('rhinfo_zyxq_member', $member_data);
        $id = pdo_insertid();
        $user_data = array('mobile' => $mobile, 'realname' => $ownername, 'groupid' => $groupid);
        $res = pdo_update('mc_members', $user_data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']), 'AND');
        $car_data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $region['id'], 'title' => strtoupper($_GPC['carno']), 'carno' => strtoupper($_GPC['carno']), 'ownername' => $ownername, 'mobile' => $mobile, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        $mycar_data = array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'carno' => strtoupper($_GPC['carno']), 'ctime' => TIMESTAMP);
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid=:openid or uid=:uid) and hid=:hid';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $rid, ':bid' => $room[0], ':tid' => $room[1], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':hid' => $room[2]));
        if (!($total > 0)) {
            if (!($region['register'] == 1)) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid=:weid and pid=:pid and rid=:rid and carno=:carno and deleted=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id'], ':carno' => $_GPC['carno']));
                if (!($total > 0)) {
                    pdo_insert('rhinfo_zyxq_car', $car_data);
                }
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and (openid=:openid or uid=:uid) and carno=:carno';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid'], ':carno' => $_GPC['carno']));
                if (!($total > 0)) {
                    pdo_insert('rhinfo_zyxq_mycar', $mycar_data);
                }
                if ($region['bindcredit'] > 0) {
                    mc_credit_update($_W['member']['uid'], 'credit1', $region['bindcredit'], array(0, '绑定房产,赠送' . $region['bindcredit'] . '积分', 'rhinfo_zyxq'));
                    mc_notice_credit1($_W['openid'], $_W['member']['uid'], $region['bindcredit'], '绑定房产,赠送' . $region['bindcredit'] . '积分', $this->createMobileUrl('service', array('op' => 'credit1')), '谢谢支持，点击查看详情');
                }
            }
        }
        if ($this->syscfg['isweaddress']) {
            pdo_insert('mc_member_address', $member_address);
        }
        if ($this->syscfg['isrraddress']) {
            if (pdo_tableexists('ewei_shop_member_address')) {
                pdo_insert('ewei_shop_member_address', $rrmember_address);
            } elseif (pdo_tableexists('zwei_shop_member_address')) {
                pdo_insert('zwei_shop_member_address', $rrmember_address);
            }
            if (pdo_tableexists('ewei_shop_member')) {
                $rrinfo = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                if (empty($rrinfo) && !empty($_W['openid'])) {
                    pdo_insert('ewei_shop_member', $rrmember_data);
                }
                if (!empty($rrinfo) && !empty($_W['openid'])) {
                    if (empty($rrinfo['mobile']) || empty($rrinfo['realname'])) {
                        pdo_update('ewei_shop_member', $rrmember_update, array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
                    }
                }
            } elseif (pdo_tableexists('zwei_shop_member')) {
                $rrinfo = pdo_fetch('select * from ' . tablename('zwei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                if (empty($rrinfo) && !empty($_W['openid'])) {
                    pdo_insert('zwei_shop_member', $rrmember_data);
                }
                if (!empty($rrinfo) && !empty($_W['openid'])) {
                    if (empty($rrinfo['mobile']) || empty($rrinfo['realname'])) {
                        pdo_update('zwei_shop_member', $rrmember_update, array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
                    }
                }
            }
        }
        if ($region['register'] == 1) {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '新绑定信息', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $region['title'] . $building . '-' . $unit . '-' . $room['title'], 'color' => $textcolor), 'remark' => array('value' => '住户姓名:' . $ownername . ',联系电话:' . $mobile . ',请尽快审核!'));
            $url = $this->createMobileUrl('member', array('op' => 'audit', 'id' => $id));
            $url = $this->my_mobileurl($url);
            if (!empty($_W['setting']['site']['key']) && $this->syscfg['isworkersound']) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and rid=:rid and uid =0';
                $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id']));
                if (empty($secuser)) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and pid=:pid and uid=0';
                    $secuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid']));
                }
                $m = 0;
                while (!($m >= count($secuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'property_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $secuser[$m]['id'], '您有新的绑定人员，请注意确认.');
                    ($m += 1) + -1;
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and uid >0';
                $weuser = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
                $m = 0;
                while (!($m >= count($weuser))) {
                    my_send_sound($this->syscfg['workermanurlpost'], 'rhinfo_zyxq_' . $_W['setting']['site']['key'] . $_W['uniacid'] . $weuser[$m]['uid'], '您有新的绑定人员，请注意确认.');
                    ($m += 1) + -1;
                }
            }
            if (!empty($this->syscfg['tplid1']) && $region['openid']) {
                $this->send_mysendtplnotice($region['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            show_json(1, '绑定成功,请等待审核.');
        } elseif ($region['register'] == 2) {
            if ($isowner == 1) {
                $room = pdo_get('rhinfo_zyxq_room', array('weid' => $_W['uniacid'], 'id' => $myroom[2]), array('mobile', 'ownername'));
                if (empty($room['mobile']) || empty($room['ownername'])) {
                    pdo_update('rhinfo_zyxq_room', array('mobile' => $mobile, 'ownername' => $ownername), array('weid' => $_W['uniacid'], 'rid' => $rid, 'id' => $myroom[2]));
                }
            }
            show_json(1, '绑定成功.');
        } else {
            show_json(1, '绑定成功.');
        }
    }
} elseif ($operation == 'audit') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and id = :id';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $member['rid']));
    if ($_W['isajax']) {
        $data = array('status' => $_GPC['mstatus'], 'reason' => $_GPC['reason'], 'audituid' => $_W['member']['uid'], 'auditopenid' => $_W['openid'], 'audituid' => $_W['member']['uid']);
        $res = pdo_update('rhinfo_zyxq_member', $data, array('weid' => $_W['uniacid'], 'id' => $id));
        if ($res) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id = :hid';
            $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $member['pid'], ':rid' => $member['rid'], ':bid' => $member['bid'], ':tid' => $member['tid'], ':hid' => $member['hid']));
            if (empty($room['ownername']) && $member['otype'] == 0) {
                pdo_update('rhinfo_zyxq_room', array('ownername' => $member['realname'], 'mobile' => $member['mobile']), array('weid' => $_W['uniacid'], 'pid' => $member['pid'], 'rid' => $member['rid'], 'bid' => $member['bid'], 'tid' => $member['tid'], 'id' => $member['hid']));
            }
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and openid=:openid and hid=:hid and deleted=1';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $member['pid'], ':rid' => $member['rid'], ':bid' => $member['bid'], ':tid' => $member['tid'], ':openid' => $member['openid'], ':hid' => $member['hid']));
            if ($region['bindcredit'] > 0 && $_GPC['mstatus'] == 0 && $total == 0) {
                $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                $crediturl = $this->my_mobileurl($crediturl);
                mc_credit_update($member['uid'], 'credit1', $region['bindcredit'], array(0, '绑定房产,赠送' . $region['bindcredit'] . '积分', 'rhinfo_zyxq'));
                mc_notice_credit1($member['openid'], $member['uid'], $region['bindcredit'], '绑定房产,赠送' . $region['bindcredit'] . '积分', $crediturl, '谢谢支持，点击查看详情');
            }
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            if ($region['bindstrategyid'] > 0 && $_GPC['mstatus'] == 0 && $total == 0) {
                $redpacket = $this->send_redpacket($region['bindstrategyid'], $member['openid'], 1);
                if (!is_error($redpacket)) {
                }
                $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_share') . ' where (to=:to or touid=:uid) and weid=:weid and status=1';
                $redshare = pdo_fetch($sql, array(':to' => $member['openid'], ':uid' => $member['uid'], ':weid' => $_W['uniacid']));
                if (!empty($redshare)) {
                    $shareres = $this->send_redpacket($redshare['redid'], $redshare['from'], 0);
                    if (!is_error($shareres)) {
                        $postdata = array('first' => array('value' => '有人通过您的邀请，并成功绑定房产'), 'keyword1' => array('value' => $member['realname'], 'color' => $topcolor), 'keyword2' => array('value' => $member['address'], 'color' => $textcolor), 'keyword3' => array('value' => date('Y-m-d H:i', $member['ctime']), 'color' => $textcolor), 'remark' => array('value' => '恭喜您获得一个答谢红包，点击领取吧'));
                        if (!empty($this->syscfg['redpackettplid'])) {
                            $this->send_mysendtplnotice($redshare['from'], $this->syscfg['redpackettplid'], $postdata, $this->my_mobileurl($shareres['url']), $topcolor);
                        }
                    }
                }
            }
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => $_GPC['mstatus'] == 2 ? '审核不通过' : '审核通过', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'remark' => array('value' => $_GPC['mstatus'] == 2 ? '非常抱歉，您绑定的房产审核不通过，原因：' . $_GPC['reason'] : '恭喜您，您绑定的房产已经审核通过!'));
            $url = $this->createMobileUrl('member', array('op' => 'myhouse'));
            $url = $this->my_mobileurl($url);
            $url = $_GPC['mstatus'] == 2 ? '' : $url;
            if (!empty($this->syscfg['tplid1']) && $member['openid']) {
                $url = !empty($redpacket['url']) ? $this->my_mobileurl($redpacket['url']) : $url;
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            if (!($_GPC['mstatus'] == 2)) {
                $car_data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $region['id'], 'title' => $member['carno'], 'carno' => $member['carno'], 'ownername' => $member['realname'], 'mobile' => $member['mobile'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                $mycar_data = array('weid' => $_W['uniacid'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'carno' => $member['carno'], 'ctime' => TIMESTAMP);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid=:weid and pid=:pid and rid=:rid and carno=:carno and deleted=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id'], ':carno' => $member['carno']));
                if (!($total > 0)) {
                    pdo_insert('rhinfo_zyxq_car', $car_data);
                }
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and (openid=:openid or uid=:uid) and carno=:carno';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $member['openid'], ':uid' => $_W['member']['uid'], ':carno' => $member['carno']));
                if (!($total > 0)) {
                    pdo_insert('rhinfo_zyxq_mycar', $mycar_data);
                }
            }
            show_json(1, '审核完成.');
        } else {
            show_json(0, '审核失败.');
        }
    }
    $otype = array('0' => '业主', '1' => '成员', '2' => '租户');
    $sql = 'SELECT t.*,c.right5 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4 and (t.openid = :openid or t.uid=:uid)';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $member['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $member['otype'] = $otype[$member['otype']];
    if ($_W['openid'] == $region['openid'] || $_W['member']['uid'] == $region['uid'] || $team['right5'] == 1) {
        include $this->mymtpl('audit');
    } else {
        $this->mymsg('error', '错误', '抱歉，您没有审核权限.', 'close');
    }
}
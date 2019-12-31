<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
date_default_timezone_set('Asia/Shanghai');
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'visit';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$mydo = 'visit';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
if ($operation == 'index') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and rid=:rid and (toopenid=:openid or touid=:uid) and status=1 and opentimes>0 and effetime>' . TIMESTAMP;
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $rid = !empty($item['rid']) ? $item['rid'] : $_GPC['rid'];
    if (!empty($item)) {
        $qrcode_url = $this->createMobileurl('manager', array('op' => 'visitscan', 'id' => $item['id']));
        $qrcode_url = $this->my_mobileurl($qrcode_url);
    }
    include $this->mymtpl('index');
} elseif ($operation == 'askvisit') {
    if ($_W['ispost']) {
        $effetime = strtotime('+' . intval($_GPC['effedate']) . ' minutes', TIMESTAMP);
        $myroom = explode('-', $_GPC['room']);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid = :rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0  order by otype';
        $member = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':bid' => $myroom[0], ':tid' => $myroom[1], ':hid' => $myroom[2]));
        if (!empty($member)) {
            $data = array('weid' => $_W['uniacid'], 'doorid' => 0, 'pid' => $region['pid'], 'rid' => $_GPC['rid'], 'bid' => $myroom[0], 'tid' => $myroom[1], 'hid' => $myroom[2], 'title' => '访客登记', 'fromopenid' => $member['openid'], 'fromuid' => $member['uid'], 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid'], 'effedate' => $_GPC['effedate'], 'reason' => $_GPC['reason'], 'carno' => $_GPC['carno'], 'effetime' => $effetime, 'opentimes' => $_GPC['opentimes'], 'status' => 0, 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_door_visit', $data);
            $subid = pdo_insertid();
            $url = $this->createMobileurl($mydo, array('op' => 'auditvisit', 'id' => $subid, 'reason' => $_GPC['reason']));
            $url = $this->my_mobileurl($url);
            $userinfo = $_W['fans'];
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $userinfo['nickname'], 'color' => $textcolor), 'remark' => array('value' => '有访客到访,' . $_GPC['reason'] . ',请确认!'));
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
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid order by title,id';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
    $mybuilding = $buildings;
    $k = 0;
    while (!($k >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid order by title,id';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id']));
        $myunit[$buildings[$k]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by title,id';
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and uid=:uid';
    $mycar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $cararr = array();
    if (!empty($mycar['carno'])) {
        $carno = $mycar['carno'];
        $carlen = mb_strlen($carno, 'utf-8');
        $k = 0;
        while (!($k >= $carlen)) {
            $cararr[] = mb_substr($carno, $k, 1, 'utf-8');
            ($k += 1) + -1;
        }
    }
    $replys = pdo_getall('rhinfo_zyxq_category', array('rid' => $_GPC['rid'], 'type' => 7), array('title'));
    include $this->mymtpl('askvisit');
} elseif ($operation == 'askvisit_mobile') {
    if ($_W['ispost']) {
        $effetime = strtotime('+' . intval($_GPC['effedate']) . ' minutes', TIMESTAMP);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid = :rid and mobile=:mobile and deleted=0 and status=0 order by otype';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':mobile' => $_GPC['mobile']));
        if (!empty($member)) {
            $data = array('weid' => $_W['uniacid'], 'doorid' => 0, 'pid' => $region['pid'], 'rid' => $_GPC['rid'], 'bid' => $member['bid'], 'tid' => $member['tid'], 'title' => $member['address'], 'fromopenid' => $member['openid'], 'fromuid' => $member['uid'], 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid'], 'effedate' => $_GPC['effedate'], 'effetime' => $effetime, 'opentimes' => $_GPC['opentimes'], 'reason' => $_GPC['reason'], 'carno' => $_GPC['carno'], 'status' => 0, 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_door_visit', $data);
            $subid = pdo_insertid();
            $url = $this->createMobileurl($mydo, array('op' => 'auditvisit', 'id' => $subid, 'reason' => $_GPC['reason']));
            $url = $this->my_mobileurl($url);
            load()->model('mc');
            $fans = mc_fansinfo($_W['openid'], $_W['acid'], $_W['uniacid']);
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $fans['nickname'], 'color' => $textcolor), 'remark' => array('value' => '有访客到访,' . $_GPC['reason'] . ',请确认!'));
            if (!empty($this->syscfg['tplid1']) && $member['openid']) {
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            $this->mymsg('success', '申请成功', '', 'close');
        } else {
            $this->mymsg('error', '申请失败', '请联系业主', 'close');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and uid=:uid';
    $mycar = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $cararr = array();
    if (!empty($mycar['carno'])) {
        $carno = $mycar['carno'];
        $carlen = mb_strlen($carno, 'utf-8');
        $k = 0;
        while (!($k >= $carlen)) {
            $cararr[] = mb_substr($carno, $k, 1, 'utf-8');
            ($k += 1) + -1;
        }
    }
    $replys = pdo_getall('rhinfo_zyxq_category', array('rid' => $_GPC['rid'], 'type' => 7), array('title'));
    include $this->mymtpl('askmobile');
} elseif ($operation == 'auditvisit') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where status=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    load()->model('mc');
    if ($_W['isajax']) {
        $url = $this->createMobileurl($mydo, array('op' => 'getvisit', 'id' => $id));
        $url = $this->my_mobileurl($url);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:id ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $item['rid']));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $fans = mc_fansinfo($item['fromopenid'], $_W['acid'], $_W['uniacid']);
        if ($_GPC['vstatus'] == 1) {
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '访客申请通过审核', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $fans['nickname'], 'color' => $textcolor), 'remark' => array('value' => '您的申请已经通过,请点击进入!'));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and rid=:rid and status=1';
            $parklot = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
            if (!empty($parklot['pc_plotid']) && !empty($item['carno']) && $parklot['isreserve'] == 1) {
                $parklot['pc_type'] = !empty($parklot['pc_type']) ? $parklot['pc_type'] : $region['pc_type'];
                if ($parklot['pc_type'] == 2) {
                    $set = array();
                    $set['pc_appid'] = $region['pc_appid'];
                    $set['pc_secret'] = $region['pc_secret'];
                    $set['url'] = 'third_api/reserveParkingSpace.action';
                    $params = array('parkId' => $parklot['pc_plotid'], 'carNum' => $item['carno'], 'reserveId' => TIMESTAMP . random(8), 'reserveInTime' => date('Y-m-d H:i:s'), 'reserveOutTime' => date('Y-m-d H:i:s', strtotime('+1 days')), 'paymentStatus' => 2, 'chargingStrategyId' => $parklot['cloudruleid'], 'appUserId' => 1);
                    $res = ipms_httpsign_post($set, $params);
                    if (!($res['result'] == 'success')) {
                        $this->mysyslog(0, 'error', 'visit', '车位预约' . $item['carno'], $res['resultMsg']);
                    }
                } elseif ($parklot['pc_type'] == 3) {
                    $set = array('url' => 'app/UpCarSet.aspx', 'parkno' => $parklot['pc_plotid'], 'secret' => $parklot['pc_secret']);
                    $params = array('CarNo' => $item['carno'], 'CarSetNo' => random(8), 'StartTime' => date('YmdHis'), 'EndTime' => date('YmdHis', strtotime('+1 days')));
                    $res = etpcar_http_post($set, $params);
                    $resmsg = $res['ReMsg'];
                    if (!($resmsg['ErrNo'] == '0000')) {
                        $this->mysyslog(0, 'error', 'visit', '车位预约' . $item['carno'], $resmsg['ErrMsg']);
                    }
                } elseif ($parklot['pc_type'] == 5) {
                    $set = array('url' => '/parkCarBook/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $parklot['pc_secret']);
                    $params = array('plateNum' => $item['carno'], 'parkNo' => random(8), 'payState' => 1, 'endDate' => strtotime('+1 days'));
                    $res = deliyun_http_post($set, $params);
                    if (!($res['ecode'] == 0)) {
                        $this->mysyslog(0, 'error', 'visit', '车位预约' . $item['carno'], $res['msg']);
                    }
                }
            }
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
    $fans = mc_fansinfo($item['toopenid'], $_W['acid'], $_W['uniacid']);
    include $this->mymtpl('auditvisit');
} elseif ($operation == 'getvisit') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where status=0 and weid = :weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (!empty($item)) {
        pdo_update('rhinfo_zyxq_door_visit', array('status' => 1, 'toopenid' => $_W['openid'], 'touid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'id' => $id));
        $rid = $item['rid'];
        if (!($item['effetime'] >= TIMESTAMP)) {
            $this->mymsg('error', '非常抱歉', '访客有效期已过', 'close');
            exit(0);
        }
        if (!($item['opentimes'] >= 1)) {
            $this->mymsg('error', '非常抱歉', '访客开门次数已用完', 'close');
            exit(0);
        }
        $qrcode_url = $this->createMobileurl('manager', array('op' => 'visitscan', 'visitid' => $item['id']));
        $qrcode_url = $this->my_mobileurl($qrcode_url);
        include $this->mymtpl('index');
        exit(0);
    } else {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and id=:id and (toopenid=:openid or touid=:uid) ';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        $rid = $item['rid'];
        if (!empty($item)) {
            if (!($item['effetime'] >= TIMESTAMP)) {
                $this->mymsg('error', '非常抱歉', '访客有效期已过', 'close');
                exit(0);
            }
            if (!($item['opentimes'] >= 1)) {
                $this->mymsg('error', '非常抱歉', '访客开门次数已用完', 'close');
                exit(0);
            }
            $qrcode_url = $this->createMobileurl('manager', array('op' => 'visitscan', 'visitid' => $item['id']));
            $qrcode_url = $this->my_mobileurl($qrcode_url);
            include $this->mymtpl('index');
            exit(0);
        } else {
            $this->mymsg('error', '非常抱歉', '已经失效，无法生成访客二信码', 'close');
        }
        exit(0);
    }
} elseif ($operation == 'myvisit') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and pid=:pid and rid=:rid and (toopenid=:openid or touid=:uid)and status=1 and opentimes>0 and effetime>' . TIMESTAMP;
    $visits = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    include $this->mymtpl('myvisit');
} elseif ($operation == 'visitor') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door_visit') . ' where weid = :weid and fromuid=:uid and status=1 order by id desc limit 20';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['toopenid'] = empty($list[$k]['toopenid']) ? $list[$k]['touid'] : $list[$k]['toopenid'];
        if ($list[$k]['toopenid']) {
            $fans = array();
            $fans = mc_fansinfo($list[$k]['toopenid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
        }
        $list[$k]['parkid'] = pdo_getcolumn('rhinfo_zyxq_parkinglot', array('weid' => $_W['uniacid'], 'rid' => $list[$k]['rid'], 'status' => 1), 'id');
        ($k += 1) + -1;
    }
    include $this->mymtpl('visitor');
}
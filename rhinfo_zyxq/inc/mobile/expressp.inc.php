<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$curr = 'expressp';
$mydo = 'expressp';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
$sid = intval($_GPC['sid']);
if (!empty($sid)) {
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $store['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and uid=:uid and rid=:rid and deleted=0 and status=0';
    $rmember = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':rid' => $store['rid']));
    if (empty($rmember) && !empty($region) && $store['isbind'] == 1) {
        if ($region['register'] == 1) {
            $region_url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $region['id'], 'register' => 1));
        } elseif ($region['register'] == 2) {
            $region_url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $region['id'], 'register' => 2));
        } else {
            if (!empty($user['mobile'])) {
                $bind = 'bind';
            } else {
                $bind = 'rbind';
            }
            $region_url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $region['id']));
        }
        header('Location:' . $region_url);
        exit(0);
    }
    if (empty($user['mobile'])) {
        header('Location:' . $this->createMobileurl('auth', array('op' => 'bind')));
        exit(0);
    }
}
if ($operation == 'index') {
    $res = $this->express_store_rights($sid, $_W['member']['uid']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
    $sql = 'select link,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid=:pid and rid=:rid and btype=12 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now()))  order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        $range = empty($_GPC['range']) ? 500 : $_GPC['range'];
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $temp_data = array();
        $k = 0;
        while (!($k >= count($data))) {
            $isout = false;
            if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                if (!(0 >= $range) && !($range >= $distance)) {
                    $isout = true;
                }
                $data[$k]['distance'] = $distance;
            } else {
                $data[$k]['distance'] = 100000;
            }
            $data[$k]['expressurl'] = $this->createMobileurl($mydo, array('op' => 'index', 'sid' => $data[$k]['id']));
            $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'sid' => $data[$k]['id']));
            $data[$k]['thumb'] = tomedia($data[$k]['thumb']);
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
        show_json(1, array('list' => $data, 'total' => count($data), 'pagesize' => $psize));
    }
    include $this->mymtpl('list');
} elseif ($operation == 'map') {
    $condition .= ' and id = :sid';
    $params[':sid'] = $sid;
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    include $this->mymtpl('map');
} elseif ($operation == 'sendexpress') {
    if ($_W['isajax']) {
        if (empty($_GPC['fromaid'])) {
            show_json(0, '寄件地址不能为空');
        }
        if (empty($_GPC['toaid'])) {
            show_json(0, '收件地址不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and id=:fromaid';
        $sendadd = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':fromaid' => $_GPC['fromaid']));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and id=:toaid';
        $takeadd = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':toaid' => $_GPC['toaid']));
        $data = array('weid' => $_W['uniacid'], 'sid' => $sid, 'contact' => $takeadd['realname'], 'mobile' => $takeadd['mobile'], 'province' => $takeadd['province'], 'city' => $takeadd['city'], 'district' => $takeadd['district'], 'address' => $takeadd['address'], 'fromcontact' => $sendadd['realname'], 'frommobile' => $sendadd['mobile'], 'fromprovince' => $sendadd['province'], 'fromcity' => $sendadd['city'], 'fromdistrict' => $sendadd['district'], 'fromaddress' => $sendadd['address'], 'compid' => $_GPC['compid'], 'title' => $_GPC['title'], 'weight' => $_GPC['weight'], 'qty' => $_GPC['qty'], 'paytype' => $_GPC['paytype'], 'cuid' => $_W['member']['uid'], 'io' => 1, 'remark' => $_GPC['remark'], 'status' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_express', $data);
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :id';
        $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $sid));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $postdata = array('first' => array('value' => $store['title']), 'keyword1' => array('value' => '新的快件已下单,' . $takeadd['city'] . '~' . $sendadd['city'], 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $takeadd['address'] . ' ' . $takeadd['realname'], 'color' => $textcolor), 'remark' => array('value' => '请尽快确认并补价格,谢谢.'));
        $url = $this->createMobileurl('express', array('op' => 'express', 'cate' => 1, 'sid' => $sid));
        $url = $this->my_mobileurl($url);
        if (!empty($store['openid'])) {
            if (!empty($this->syscfg['tplid1'])) {
                $this->send_mysendtplnotice($store['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg('亲，新的快件已下单', '请尽快确认并处理,谢谢.', $url, '', $openid);
            }
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_employee') . ' where weid= :weid and sid=:sid and status=1';
        $employees = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        $k = 0;
        while (!($k >= count($employees))) {
            if (!empty($employees[$k]['openid'])) {
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($employees[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                } else {
                    $this->send_mycusnewsmsg('亲，新的快件已下单', '请尽快确认并处理,谢谢.', $url, '', $openid);
                }
            }
            ($k += 1) + -1;
        }
        show_json(1, '下单成功');
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_send') . ' where weid=:weid and uid = :uid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
    $sid = empty($sid) ? $item['sid'] : $sid;
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    if (empty($item)) {
        pdo_insert('rhinfo_zycj_express_send', array('weid' => $_W['uniacid'], 'sid' => $sid, 'uid' => $_W['member']['uid'], 'status' => 0, 'ctime' => TIMESTAMP));
        $sendid = pdo_insertid();
    } else {
        $sendid = $item['id'];
        pdo_update('rhinfo_zycj_express_send', array('sid' => $sid), array('weid' => $_W['uniacid'], 'id' => $sendid));
    }
    $sendadd = array();
    $takeadd = array();
    if ($_GPC['io'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and id=:aid';
        $sendadd = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':aid' => $_GPC['aid']));
        pdo_update('rhinfo_zycj_express_send', array('fromaid' => $_GPC['aid']), array('weid' => $_W['uniacid'], 'id' => $sendid));
    } elseif ($_GPC['io'] == 2) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and id=:aid';
        $takeadd = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':aid' => $_GPC['aid']));
        pdo_update('rhinfo_zycj_express_send', array('toaid' => $_GPC['aid']), array('weid' => $_W['uniacid'], 'id' => $sendid));
    }
    if (empty($sendadd)) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and id=:aid';
        $sendadd = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':aid' => $item['fromaid']));
    }
    if (empty($takeadd)) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and id=:aid';
        $takeadd = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':aid' => $item['toaid']));
    }
    if (!empty($_GPC['compid'])) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $_GPC['compid']));
        pdo_update('rhinfo_zycj_express_send', array('compid' => $_GPC['compid']), array('weid' => $_W['uniacid'], 'id' => $sendid));
    } else {
        $comps = array();
        if (!empty($sid)) {
            $sql = 'select distinct b.title,b.thumb from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid=:weid and a.sid = :sid limit 8';
            $comps = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
        }
    }
    $labels = pdo_getall('rhinfo_zycj_express_label', array('weid' => $_W['uniacid'], 'category' => 1));
    include $this->mymtpl('send');
} elseif ($operation == 'takeexpress') {
    if ($_W['isajax']) {
        if (empty($_GPC['orderno'])) {
            show_json(0, '取件码不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and id=:expressid';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expressid' => $_GPC['expressid']));
        if ($item['status'] > 0) {
            show_json(0, '此快件已取出');
        }
        $res = pdo_update('rhinfo_zycj_express', array('status' => 1, 'taketime' => TIMESTAMP, 'takeuid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'sid' => $sid, 'id' => $_GPC['expressid']));
        if (!empty($res)) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            if ($_GPC['cfrom'] == 1) {
                $openid = $_W['openid'];
            } else {
                $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
                $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $item['mobile']));
                load()->model('mc');
                $openid = mc_uid2openid($member['uid']);
            }
            if (!empty($openid)) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => $store['title']), 'keyword1' => array('value' => '亲，您的快件已成功取件', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '快件单号' . $item['expresssn'], 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持.'));
                $url = $this->createMobileurl($mydo, array('op' => 'myexpress', 'cate' => 2));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($openid, $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                }
            }
            if (!empty($store['openid'])) {
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid=:weid and id=:cabid';
                $cabinet = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':cabid' => $item['cabid']));
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and id=:stid';
                $cabstloca = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':stid' => $item['stid']));
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                load()->model('mc');
                $fans = mc_fansinfo($store['uid'], $_W['acid'], $_W['uniacid']);
                $postdata = array('first' => array('value' => $fans['nickname'] . '成功领取快件'), 'keyword1' => array('value' => '快件已被成功领取', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '快件单号' . $item['expresssn'] . $cabinet['title'] . $cabstloca['title'], 'color' => $textcolor), 'remark' => array('value' => '请及时确认.'));
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($store['openid'], $this->syscfg['tplid1'], $postdata, '', $topcolor);
                }
            }
            if (!empty($store['ylbid'])) {
                $set = array();
                $set['url'] = 'add.php';
                $params = array('token' => $this->syscfg['ylb_token'], 'id' => $store['ylbid'], 'uid' => 'rhinfo_' . $store['ylbid'], 'price' => substr($item['orderno'], 5, 5));
                $res = ylb_http_post($set, $params);
            }
            show_json(1, '取件成功');
        } else {
            show_json(0, '取件失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid = :sid and mobile=:mobile and io=2 and status=0 ';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':mobile' => $user['mobile']));
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['qrcode'] = $this->createqrcode($list[$k]['orderno']);
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and id = :sid';
        $list[$k]['location'] = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':stid' => $list[$k]['title']));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
        $list[$k]['thumb'] = tomedia($company['thumb']);
        $list[$k]['comptitle'] = $company['title'];
        ($k += 1) + -1;
    }
    include $this->mymtpl('take');
} elseif ($operation == 'quicktake') {
    if ($_W['isajax']) {
        if (empty($_GPC['orderno'])) {
            show_json(0, '取件码不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid=:sid and id=:expressid';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':expressid' => $_GPC['expressid']));
        if ($item['status'] > 0) {
            show_json(0, '此快件已取出');
        }
        $res = pdo_update('rhinfo_zycj_express', array('status' => 1, 'taketime' => TIMESTAMP, 'takeuid' => $_W['member']['uid']), array('weid' => $_W['uniacid'], 'sid' => $sid, 'id' => $_GPC['expressid']));
        if (!empty($res)) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id=:sid';
            $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
            if ($_GPC['cfrom'] == 1) {
                $openid = $_W['openid'];
            } else {
                $sql = 'select * from ' . tablename('mc_members') . ' where uniacid=:uniacid and mobile=:mobile ';
                $member = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':mobile' => $item['mobile']));
                load()->model('mc');
                $openid = mc_uid2openid($member['uid']);
            }
            if (!empty($openid)) {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                $postdata = array('first' => array('value' => $store['title']), 'keyword1' => array('value' => '亲，您的快件已成功取件', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '快件单号' . $item['expresssn'], 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持.'));
                $url = $this->createMobileurl($mydo, array('op' => 'myexpress', 'cate' => 2));
                $url = $this->my_mobileurl($url);
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($openid, $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                }
            }
            if (!empty($store['openid'])) {
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabinet') . ' where weid=:weid and id=:cabid';
                $cabinet = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':cabid' => $item['cabid']));
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and id=:stid';
                $cabstloca = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':stid' => $item['stid']));
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                load()->model('mc');
                $fans = mc_fansinfo($store['uid'], $_W['acid'], $_W['uniacid']);
                $postdata = array('first' => array('value' => $fans['nickname'] . '成功领取快件'), 'keyword1' => array('value' => '快件已被成功领取', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => '快件单号' . $item['expresssn'] . $cabinet['title'] . $cabstloca['title'], 'color' => $textcolor), 'remark' => array('value' => '请及时确认.'));
                if (!empty($this->syscfg['tplid1'])) {
                    $this->send_mysendtplnotice($store['openid'], $this->syscfg['tplid1'], $postdata, '', $topcolor);
                }
            }
            if (!empty($store['ylbid'])) {
                $set = array();
                $set['url'] = 'add.php';
                $params = array('token' => $this->syscfg['ylb_token'], 'id' => $store['ylbid'], 'uid' => 'rhinfo_' . $store['ylbid'], 'price' => substr($item['orderno'], 5, 5));
                $res = ylb_http_post($set, $params);
            }
            show_json(1, '取件成功');
        } else {
            show_json(0, '取件失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and sid = :sid and mobile=:mobile and io=2 and status=0 ';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid, ':mobile' => $user['mobile']));
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['qrcode'] = $this->createqrcode($list[$k]['orderno']);
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and id = :sid';
        $list[$k]['location'] = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':stid' => $list[$k]['title']));
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
        $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
        $list[$k]['thumb'] = tomedia($company['thumb']);
        $list[$k]['comptitle'] = $company['title'];
        ($k += 1) + -1;
    }
    include $this->mymtpl('quicktake');
} elseif ($operation == 'selectstore') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $temp_data = array();
        $k = 0;
        while (!($k >= count($data))) {
            $isout = false;
            if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                if (!(0 >= $range) && !($range >= $distance)) {
                    $isout = true;
                }
                $data[$k]['distance'] = $distance;
            } else {
                $data[$k]['distance'] = 100000;
            }
            $data[$k]['expressurl'] = $this->createMobileurl($mydo, array('op' => 'sendexpress', 'sid' => $data[$k]['id']));
            $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'sid' => $data[$k]['id']));
            $data[$k]['thumb'] = tomedia($data[$k]['thumb']);
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
        show_json(1, array('list' => $data, 'total' => count($data), 'pagesize' => $psize));
    }
    include $this->mymtpl('selectstore');
} elseif ($operation == 'address') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and uid=:uid and io=:io ';
        $params[':uid'] = $_W['member']['uid'];
        $params[':io'] = $_GPC['io'];
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and (mobile like \'%' . $_GPC['keyword'] . '%\' or realname like \'%' . $_GPC['keyword'] . '%\') ';
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_express_address') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where ' . $condition . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'sendexpress', 'aid' => $data[$k]['id'], 'sid' => $sid, 'io' => $_GPC['io']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    include $this->mymtpl('address');
} elseif ($operation == 'postaddress') {
    if ($_W['isajax']) {
        $areas = array();
        if (!empty($_GPC['areas'])) {
            $areas = explode(' ', $_GPC['areas']);
        } else {
            show_json(0, '地区不能为空');
        }
        if (empty($_GPC['address'])) {
            show_json(0, '详细地址不能为空');
        }
        if (!empty($_GPC['aid'])) {
            $data = array('realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'province' => $areas[0], 'city' => $areas[1], 'district' => $areas[2], 'address' => $_GPC['address']);
            pdo_update('rhinfo_zycj_express_address', $data, array('weid' => $_W['uniacid'], 'id' => $_GPC['aid']));
        } else {
            $data = array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'io' => $_GPC['io'], 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'province' => $areas[0], 'city' => $areas[1], 'district' => $areas[2], 'address' => $_GPC['address'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_express_address', $data);
        }
        show_json(1, '保存成功');
    }
    include $this->mymtpl('postaddress');
} elseif ($operation == 'editaddress') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_address') . ' where weid=:weid and uid = :uid and id=:aid';
    $address = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':aid' => $_GPC['aid']));
    $_GPC['io'] = empty($_GPC['io']) ? $_GPC['io'] : $address['io'];
    include $this->mymtpl('postaddress');
} elseif ($operation == 'deladdress') {
    if ($_W['isajax']) {
        $result = pdo_delete('rhinfo_zycj_express_address', array('weid' => $_W['uniacid'], 'id' => $_GPC['aid']));
        if (!empty($result)) {
            show_json(1);
        } else {
            show_json(0, '删除失败');
        }
    }
} elseif ($operation == 'pay') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_express_store') . ' where weid=:weid and id = :sid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    if ($_W['isajax'] && $_GPC['money'] > 0) {
        $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
        $returl = !empty($item['paysuccessurl']) ? $item['paysuccessurl'] : $returl;
        $params = array('money' => $_GPC['money'], 'title' => '向驿站付款', 'feetype' => 10, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'sid' => $sid, 'expressid' => $_GPC['expressid']);
        if ($_GPC['payfrom'] == 1) {
            $res = $this->my_single_pay($params);
        } elseif ($_GPC['payfrom'] == 2) {
            $res = $this->my_single_alipay($params);
        } else {
            show_json(0, '支付参数错误');
        }
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        show_json(1, $res['result']);
    }
    include $this->mymtpl('pay');
} elseif ($operation == 'myexpress') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        if ($_GPC['cate'] == 1) {
            $condition .= ' and (mobile=:mobile or cuid=:cuid)';
            $params[':mobile'] = $user['mobile'];
            $params[':cuid'] = $_W['member']['uid'];
            $condition .= ' and io=1 ';
        } elseif ($_GPC['cate'] == 2) {
            $condition .= ' and mobile=:mobile ';
            $params[':mobile'] = $user['mobile'];
            $condition .= ' and io=2 ';
        } else {
            $condition .= ' and mobile=:mobile ';
            $params[':mobile'] = $user['mobile'];
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zycj_express') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        if ($_GPC['cate'] == 1) {
            $k = 0;
            while (!($k >= count($list))) {
                $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
                $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
                $list[$k]['thumb'] = tomedia($company['thumb']);
                $list[$k]['comptitle'] = $company['title'];
                $list[$k]['taketime'] = empty($list[$k]['taketime']) ? '' : date('Y-m-d H:i', $list[$k]['taketime']);
                $list[$k]['pay'] = 0;
                if ($list[$k]['status'] == 1) {
                    if ($list[$k]['paytype'] == 1) {
                        if ($list[$k]['paystatus'] == 1) {
                            $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'track', 'id' => $list[$k]['id'], 'compcode' => $company['compcode'], 'expresssn' => $list[$k]['expresssn']));
                        } else {
                            $list[$k]['pay'] = 1;
                            $list[$k]['url'] = '';
                        }
                    } else {
                        $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'track', 'id' => $list[$k]['id'], 'compcode' => $company['compcode'], 'expresssn' => $list[$k]['expresssn']));
                    }
                } else {
                    $list[$k]['url'] = '';
                }
                ($k += 1) + -1;
            }
        } elseif ($_GPC['cate'] == 2) {
            $k = 0;
            while (!($k >= count($list))) {
                $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
                $sql = 'select title from ' . tablename('rhinfo_zycj_express_cabstloca') . ' where weid=:weid and cabid=:cabid and id=:stid';
                $list[$k]['local'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':cabid' => $list[$k]['cabid'], ':stid' => $list[$k]['stid']));
                $list[$k]['taketime'] = empty($list[$k]['taketime']) ? '' : date('Y-m-d H:i', $list[$k]['taketime']);
                $list[$k]['qrcode'] = $this->createqrcode($list[$k]['orderno']);
                $sql = 'select * from ' . tablename('rhinfo_zycj_express_company') . ' where weid=:weid and id=:compid';
                $company = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':compid' => $list[$k]['compid']));
                $list[$k]['thumb'] = tomedia($company['thumb']);
                $list[$k]['comptitle'] = $company['title'];
                ($k += 1) + -1;
            }
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    if (empty($user['mobile'])) {
        header('Location:' . $this->createMobileurl('auth', array('op' => 'bind')));
        exit(0);
    }
    $_GPC['cate'] = empty($_GPC['cate']) ? 2 : $_GPC['cate'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_express') . ' where weid=:weid and mobile=:mobile and io=2 and status=0 ';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':mobile' => $user['mobile']));
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['qrcode'] = $this->createqrcode($list[$k]['orderno']);
        ($k += 1) + -1;
    }
    include $this->mymtpl('myexpress');
} elseif ($operation == 'track') {
    $id = intval($_GPC['id']);
    if (!empty($_GPC['expresssn'])) {
        load()->func('communication');
        $list = array();
        $info = array();
        if ($sysconfig['expressapi'] == 1) {
            if (!empty($sysconfig['kd_uid'])) {
                $url = 'http://poll.kuaidi100.com/poll/query.do';
                $params = array('customer' => $sysconfig['kd_uid'], 'param' => json_encode(array('com' => $_GPC['compcode'], 'num' => $_GPC['expresssn'])));
                $params['sign'] = md5($params['param'] . $sysconfig['kd_appkey'] . $sysconfig['kd_uid']);
                $params['sign'] = strtoupper($params['sign']);
                $response = ihttp_post($url, $params);
                $content = $response['content'];
                $info = json_decode($content, true);
            }
        } elseif ($sysconfig['expressapi'] == 2) {
            if (!empty($sysconfig['kd_appkey'])) {
                $url = 'https://api.kuaidi100.com/api?id=' . $sysconfig['kd_appkey'] . '&com=' . $_GPC['compcode'] . '&nu=' . $_GPC['expresssn'];
                $params = array();
                $response = ihttp_post($url, $params);
                $content = $response['content'];
                $info = json_decode($content, true);
            }
        } elseif ($sysconfig['expressapi'] == 3) {
            if (!empty($sysconfig['js_appkey'])) {
                $url = 'https://api.jisuapi.com/bankcardcognition/recognize?appkey=' . $sysconfig['js_appkey'];
                $post = array('type' => 'auto', 'number' => $_GPC['expresssn']);
                $result = my_curlOpen($url, array('post' => $post));
                $jsonarr = json_decode($result, true);
                if ($jsonarr['status'] == 0) {
                    $ret = $jsonarr['result'];
                    $data = $ret['list'];
                    $k = 0;
                    while (!($k >= count($data))) {
                        $info['data'] = array('time' => $data[$k]['time'], 'step' => $data[$k]['status']);
                        ($k += 1) + -1;
                    }
                }
            }
        } else {
            $url = 'https://www.kuaidi100.com/query?type=' . $_GPC['comcode'] . '&postid=' . $_GPC['expresssn'] . '&id=1&valicode=&temp=';
            $response = ihttp_request($url);
            $content = $response['content'];
            $info = json_decode($content, true);
        }
        if (empty($info) && !empty($sysconfig['expressapi'])) {
            $url = 'https://www.kuaidi100.com/query?type=' . $_GPC['comcode'] . '&postid=' . $_GPC['expresssn'] . '&id=1&valicode=&temp=';
            $response = ihttp_request($url);
            $content = $response['content'];
            $info = json_decode($content, true);
        }
        if (!empty($info['data']) && is_array($info['data'])) {
            $data = $info['data'];
            $k = 0;
            while (!($k >= count($data))) {
                $list[] = array('time' => trim($data['time']), 'step' => trim($data['context']));
                ($k += 1) + -1;
            }
        }
        include $this->mymtpl('track');
    } else {
        $this->mymsg('error', '温馨提示', '快递单号不存在');
    }
} elseif ($operation == 'company') {
    $sql = 'select distinct b.* from ' . tablename('rhinfo_zycj_express_storecomp') . ' as a left join ' . tablename('rhinfo_zycj_express_company') . ' as b on a.compid=b.id where a.weid=:weid and a.sid = :sid';
    $company = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':sid' => $sid));
    $k = 0;
    while (!($k >= count($company))) {
        $company[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'sendexpress', 'compid' => $company[$k]['id'], 'sid' => $sid));
        ($k += 1) + -1;
    }
    include $this->mymtpl('company');
} elseif ($operation == 'resolveadd') {
    $address = $_GPC['address'];
    preg_match('/(.*?(省|自治区|北京市|天津市|上海市|重庆市))/', $address, $matches);
    if (count($matches) > 1) {
        $province = $matches[count($matches) - 2];
        $address = str_replace($province, '', $address);
    }
    preg_match('/(.*?(市|自治州|地区|区划|县))/', $address, $matches);
    if (count($matches) > 1) {
        $city = $matches[count($matches) - 2];
        $address = str_replace($city, '', $address);
    }
    preg_match('/(.*?(市|区|县|镇|乡|街道))/', $address, $matches);
    if (count($matches) > 1) {
        $area = $matches[count($matches) - 2];
        $address = str_replace($area, '', $address);
    }
    preg_match('/[0-9]{12}|[0-9]{11}|[0-9]{10}/', $address, $matches);
    if (count($matches) > 0) {
        $mobile = $matches[0];
        $address = str_replace($mobile, '', $address);
    }
    $matches = explode(' ', $address);
    if (count($matches) > 1) {
        $address = $matches[0];
        $realname = $matches[1];
    } else {
        $matches = explode('，', $address);
        if (count($matches) > 1) {
            $address = $matches[0];
            $realname = $matches[1];
        }
    }
    $data = array('province' => isset($province) ? $province : '', 'city' => isset($city) ? $city : '', 'area' => isset($area) ? $area : '', 'mobile' => isset($mobile) ? $mobile : '', 'realname' => isset($realname) ? $realname : '', 'address' => isset($address) ? $address : '');
    show_json(1, array('address' => $data));
}
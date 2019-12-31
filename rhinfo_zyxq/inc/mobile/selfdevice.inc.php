<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'selfdevice';
$mydo = 'selfdevice';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
$res = $this->getarrearage($_W['member']['uid']);
if ($res) {
    if ($res['arrearagelimit9']) {
        header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
        exit(0);
    }
}
if ($operation == 'index') {
    $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and (rid=0 or rid=:rid)';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    $cateid = $_GPC['cateid'];
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        $range = empty($_GPC['range']) ? 500 : $_GPC['range'];
        $condition .= ' and cateid = :cateid ';
        $params['cateid'] = $cateid;
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where status =1 and ' . $condition;
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
            $data[$k]['detailurl'] = $this->createMobileurl($mydo, array('op' => 'detail', 'id' => $data[$k]['id']));
            if (!empty($data[$k]['rid'])) {
                $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'pmap', 'id' => $data[$k]['id'], 'rid' => $data[$k]['rid']));
            } else {
                $data[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'id' => $data[$k]['id']));
            }
            $res = $this->mxdevstatus($data[$k]['devicesn']);
            if ($res['code'] == '0') {
                $data[$k]['online'] = 1;
            } else {
                $data[$k]['online'] = 0;
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
        show_json(1, array('list' => $data, 'total' => count($data), 'pagesize' => $psize));
    }
    include $this->mymtpl('list');
} elseif ($operation == 'detail') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['status'] == 0) {
        $this->mymsg('error', '温馨提示', '抱歉，该设备暂停使用.', 'close');
    }
    $content = stripslashes($item['content']);
    $content = html_entity_decode($content);
    include $this->mymtpl('detail');
} elseif ($operation == 'scan') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['status'] == 0) {
        $this->mymsg('error', '温馨提示', '抱歉，该设备暂停使用.', 'close');
    }
    $res = $this->mxdevstatus($item['devicesn']);
    if ($res['code'] == '0') {
        $item['detailurl'] = $this->createMobileurl($mydo, array('op' => 'detail', 'id' => $item['id']));
        if (!empty($item['rid'])) {
            $item['mapurl'] = $this->createMobileurl($mydo, array('op' => 'pmap', 'id' => $item['id'], 'rid' => $item['rid']));
        } else {
            $item['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'id' => $item['id']));
        }
        include $this->mymtpl('scan');
    } else {
        $this->mymsg('error', '设备不在线', '请联系管理人员', 'close');
    }
} elseif ($operation == 'map') {
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    include $this->mymtpl('map');
} elseif ($operation == 'pmap') {
    $id = intval($_GPC['id']);
    $rid = intval($_GPC['rid']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:id ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and id=:id ';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and rid=:rid and status=1';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('pmap');
} elseif ($operation == 'pay') {
    if ($_W['isajax']) {
        $selfid = $_GPC['selfid'];
        $fee = $_GPC['fee'];
        if (empty($fee) || !($fee > 0)) {
            show_json(0, '价格未设定');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and id=:id ';
        $selfdevice = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $selfid));
        load()->model('mc');
        $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'payment', 'passport'));
        $behavior = $setting['creditbehaviors'];
        $creditnames = $setting['creditnames'];
        $credits = mc_credit_fetch($_W['member']['uid'], '*');
        if ($this->syscfg['devpaytype'] == 1) {
            if (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $fee)) {
                show_json(0, '余额不足，请充值');
            }
        } elseif ($this->syscfg['devpaytype'] == 2) {
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            $sql = 'select paysuccessurl from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid ';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $selfdevice['rid']));
            $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
            $params = array('money' => $fee, 'title' => '自助设备', 'feetype' => 4, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'selfid' => $selfid, 'pid' => $selfdevice['pid'], 'rid' => $selfdevice['rid']);
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
        } elseif (empty($credits[$behavior['currency']]) || !($credits[$behavior['currency']] >= $fee)) {
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            $params = array('money' => $fee, 'title' => '自助设备', 'feetype' => 4, 'iswxapp' => $_GPC['iswxapp'], 'returl' => $returl, 'selfid' => $selfid, 'pid' => $selfdevice['pid'], 'rid' => $selfdevice['rid']);
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
        } else {
            $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
            $crediturl = $this->my_mobileurl($crediturl);
            mc_credit_update($_W['member']['uid'], 'credit2', 0 - $fee, array(0, '自助设备', 'rhinfo_zyxq'));
            $res = $this->mxdevopen($selfdevice['devicesn']);
            $seldevice_log = array('weid' => $_W['uniacid'], 'selfid' => $selfid, 'title' => $selfdevice['title'], 'openid' => $_W['openid'], 'out_trade_no' => $uniontid, 'fee' => $fee, 'status' => 0, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            if ($res['code'] == '0') {
                $seldevice_log['status'] = 1;
                pdo_insert('rhinfo_zycj_selfdevice_log', $seldevice_log);
            } else {
                pdo_insert('rhinfo_zycj_selfdevice_log', $seldevice_log);
                show_json(2, '设备故障，请联系处理');
            }
            mc_notice_credit2($_W['openid'], $_W['member']['uid'], $fee, 0, '自助设备', $crediturl, '点击查看详情');
            show_json(1, array('wechat' => ''));
        }
    }
    show_json(0, '操作异常');
} elseif ($operation == 'my') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' weid=:weid and uid = :uid ';
        $params[':weid'] = $_W['uniacid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ' . $condition . ' ORDER BY ctime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('my');
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$_share = $this->rhinfo_share();
$wechat = array('success' => false);
$billid = $_GPC['billid'];
$fee = $_GPC['fee'];
$nopayfee = 0;
if ($_GPC['feetype'] == 1) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid = :weid and id in (' . $billid . ') limit 1';
    $data = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid = :weid and id in (' . $billid . ')';
    $nopayfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
} elseif ($_GPC['feetype'] == 2) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=1 and weid = :weid and id in (' . $billid . ') limit 1';
    $data = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=1 and weid = :weid and id in (' . $billid . ')';
    $nopayfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
} elseif ($_GPC['feetype'] == 11) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid = :weid and id in (' . $billid . ') limit 1';
    $data = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid = :weid and id in (' . $billid . ')';
    $nopayfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
}
if (!($fee >= $nopayfee)) {
    $this->mymsg('error', '非法操作', '金额不正确', 'close');
}
load()->model('mc');
$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
$behavior = $setting['creditbehaviors'];
$credits = mc_credit_fetch($_W['member']['uid'], '*');
$credit = empty($credits[$behavior['activity']]) ? 0 : $credits[$behavior['activity']];
if ($_GPC['creditfee'] > $credit) {
    $this->mymsg('error', '非法操作', '积分不足' . $credit, 'close');
}
$this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
$paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
$sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
$payno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
$payno = createnum(substr($payno, strlen($paynopre), 14));
$payno = $paynopre . $payno;
if (!empty($data)) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $data['pid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $data['rid']));
    if ($_GPC['feetype'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $data['pid'], ':rid' => $data['rid'], ':bid' => $data['bid'], ':tid' => $data['tid'], ':hid' => $data['hid']));
        $paytitle = $member['address'];
        $payuser = $member['realname'];
        if (empty($member)) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:hid';
            $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $data['pid'], ':rid' => $data['rid'], ':bid' => $data['bid'], ':hid' => $data['hid']));
            $building = pdo_get('rhinfo_zyxq_building', array('weid' => $_W['uniacid'], 'id' => $data['bid']), array('title'));
            $unit = pdo_get('rhinfo_zyxq_unit', array('weid' => $_W['uniacid'], 'id' => $data['tid']), array('title'));
            $paytitle = $region['title'] . $building['title'] . '-' . $unit['title'] . '-' . $room['title'];
            $payuser = $_W['member']['uid'];
        }
    } elseif ($_GPC['feetype'] == 2) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=0 and hid=:hid';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $data['pid'], ':rid' => $data['rid'], ':bid' => $data['lid'], ':hid' => $data['cid']));
        $paytitle = $member['address'];
        $payuser = $member['realname'];
        if (empty($member)) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :bid and id=:hid';
            $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $data['pid'], ':rid' => $data['rid'], ':bid' => $data['bid'], ':hid' => $data['hid']));
            $building = pdo_get('rhinfo_zyxq_location', array('weid' => $_W['uniacid'], 'id' => $data['bid']), array('title'));
            $paytitle = $region['title'] . $building['title'] . '-' . $room['title'];
            $payuser = $_W['member']['uid'];
        }
    } elseif ($_GPC['feetype'] == 11) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_lessee') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lesseeid';
        $lessee = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $data['pid'], ':rid' => $data['rid'], ':lesseeid' => $data['lesseeid']));
        $paytitle = $lessee['title'];
        $payuser = $_W['member']['uid'];
    }
    $params = array('tid' => $payno . random(8, 1), 'ordersn' => $payno, 'title' => $paytitle, 'fee' => $fee, 'user' => $payuser);
    if ($this->syscfg['iswepay'] == 1) {
        $myps = array();
        $myps['module'] = 'rhinfo_zyxq';
        $myps['ordersn'] = $params['tid'];
        $myps['pid'] = $data['pid'];
        $myps['rid'] = $data['rid'];
        $myps['title'] = $params['title'];
        $myps['payuser'] = $payuser;
        $myps['billid'] = @base64_encode($billid);
        $myps['feetype'] = $_GPC['feetype'];
        $myps['creditfee'] = $_GPC['creditfee'];
        $log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'rhinfo_zyxq', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => 0, 'is_usecard' => '0', 'card_id' => 0, 'uid' => $_W['member']['uid'], 'pid' => $myps['pid'], 'rid' => $myps['rid'], 'feetype' => $myps['feetype'], 'tag' => serialize($myps), 'paytime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_wepay_log', $log);
        if ($region['ispay'] == 1) {
            $params['merchid'] = $region['bankmerchid'];
        } else {
            $params['merchid'] = $this->syscfg['ymfmerchid'];
        }
        $this->pay($params);
        exit(0);
    }
    $params['module'] = 'rhinfo_zyxq';
    $params['payopenid'] = empty($_W['openid']) ? $member['openid'] : $_W['openid'];
    $params['payuser'] = $_W['member']['uid'];
    $params['pid'] = $data['pid'];
    $params['rid'] = $data['rid'];
    $params['billid'] = @base64_encode($billid);
    $params['feetype'] = $_GPC['feetype'];
    $params['creditfee'] = $_GPC['creditfee'];
    $setting = uni_setting($_W['uniacid'], 'payment');
    $isalipay = 0;
    if ($region['ispay'] == 1 && $region['isalipay'] == 1) {
        $isalipay = 1;
    } elseif ($property['ispay'] == 1 && $property['isalipay'] == 1) {
        $isalipay = 1;
    } elseif ($this->syscfg['isalipay'] > 0) {
        $isalipay = 1;
    }
    include $this->template($this->mytpl('payment/pay'));
} else {
    $redirect = $this->createMobileurl('member', array('op' => 'index'));
    header('Location:' . $redirect);
    exit(0);
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
load()->model('module');
$moduels = uni_modules();
$params = @json_decode(base64_decode($_GPC['params']), true);
if (empty($params) || !array_key_exists($params['module'], $moduels)) {
    $this->mymsg('error', '支付失败', '访问错误.', '');
    exit(0);
}
load()->model('payment');
$setting = uni_setting($_W['uniacid'], 'payment');
$dos = array();
if (!empty($setting['payment']['wechat']['switch'])) {
    $dos[] = 'wechat';
}
if (!empty($setting['payment']['alipay']['switch'])) {
    $dos[] = 'alipay';
}
$do = $_GPC['op'];
$type = in_array($do, $dos) ? $do : '';
if (empty($type)) {
    $this->mymsg('error', '支付失败', '请到系统选项开启.', '');
    exit(0);
}
load()->model('activity');
if (!empty($type)) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
    $pars = array();
    $pars[':uniacid'] = $_W['uniacid'];
    $pars[':module'] = $params['module'];
    $pars[':tid'] = $params['tid'];
    $log = pdo_fetch($sql, $pars);
    if (!empty($log) && ($type != 'credit' && !empty($_GPC['notify'])) && $log['status'] != '0') {
        message('这个订单已经支付成功, 不需要重复支付.');
    }
    $moduleid = pdo_fetchcolumn('SELECT mid FROM ' . tablename('modules') . ' WHERE name = :name', array(':name' => $params['module']));
    $moduleid = empty($moduleid) ? '000000' : sprintf('%06d', $moduleid);
    $uniontid = date('YmdHis') . $moduleid . random(8, 1);
    $ps = array();
    $ps['tid'] = $params['tid'];
    $ps['uniontid'] = $uniontid;
    $ps['user'] = $params['user'];
    $ps['fee'] = $params['fee'];
    $ps['title'] = $params['title'];
    $myps = array();
    $myps['module'] = $params['module'];
    $myps['ordersn'] = $params['ordersn'];
    $myps['pid'] = $params['pid'];
    $myps['rid'] = $params['rid'];
    $myps['title'] = $params['title'];
    $myps['payuser'] = $params['user'];
    $myps['billid'] = $params['billid'];
    $myps['iswap'] = $this->rhinfo_wap;
    $myps['feetype'] = $params['feetype'];
    if (empty($log)) {
        $log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'uniontid' => $uniontid, 'type' => $type, 'openid' => $_W['openid'], 'module' => $params['module'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'status' => 0, 'is_usecard' => '0', 'card_type' => '0', 'card_id' => 0, 'card_fee' => $params['fee'], 'uid' => $_W['member']['uid'], 'pid' => $myps['pid'], 'rid' => $myps['rid'], 'feetype' => $myps['feetype'], 'tag' => serialize($myps), 'paytime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_wepay_log', $log);
    } else {
        $update_card_log = array('is_usecard' => '0', 'card_type' => '0', 'card_id' => '0', 'card_fee' => $log['fee'], 'type' => $type);
        pdo_update('rhinfo_zyxq_wepay_log', $update_card_log, array('plid' => $log['plid']));
    }
    $ps['title'] = urlencode($params['title']);
    $pay_params = base64_encode(json_encode($ps));
    $my_params = base64_encode(json_encode($myps));
    $auth = sha1($pay_params . $_W['uniacid'] . $_W['config']['setting']['authkey']);
    if ($type == 'wechat') {
        $payurl = $this->createMobileUrl('pay', array('i' => $_W['uniacid'], 'auth' => $auth, 'ps' => $pay_params, 'myps' => $my_params));
    } elseif ($type == 'alipay') {
        $payurl = $this->createMobileUrl('alipay', array('i' => $_W['uniacid'], 'auth' => $auth, 'ps' => $pay_params, 'myps' => $my_params));
    } else {
        message('支付方式错误.');
    }
    header('Location:' . $payurl);
    exit(0);
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
define('IN_MOBILE', true);
global $_W;
global $_GPC;
if (!empty($_GPC['alipayurl'])) {
    echo '<script type="text/javascript" src="../addons/rhinfo_zyxq/payment/alipay/ap.js"></script><script type="text/javascript">_AP.pay("' . base64_decode($_GPC['alipayurl']) . '")</script>';
    exit(0);
}
$ps = $_GPC['ps'];
$myps = $_GPC['myps'];
$params = @json_decode(base64_decode($ps), true);
$myparams = @json_decode(base64_decode($myps), true);
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `tid`=:tid';
$log = pdo_fetch($sql, array(':tid' => $params['tid']));
if (!empty($log) && $log['status'] != '0') {
    $redirect = $this->createMobileUrl('member', array('op' => 'myfee'));
    $this->mymsg('error', '友情提醒', '该账单已经支付成功, 不需要重复支付', $redirect);
}
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_paylog') . ' WHERE weid=:weid and payno=:payno';
$log = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':payno' => $myparams['ordersn']));
if (!empty($log) && $log['status'] != '0') {
    $redirect = $this->createMobileUrl('member', array('op' => 'myfee'));
    $this->mymsg('error', '友情提醒', '该账单已经支付成功, 不需要重复支付', $redirect);
    exit(0);
}
$auth = sha1($ps . $_W['uniacid'] . $_W['config']['setting']['authkey']);
if ($auth != $_GPC['auth']) {
    $this->mymsg('error', '支付失败', '参数传输错误', '');
    exit(0);
}
load()->model('payment');
load()->func('communication');
$setting = uni_setting($_W['uniacid'], array('payment'));
if (!is_array($setting['payment']) && empty($this->syscfg['isalipay'])) {
    $this->mymsg('error', '支付失败', '支付参数错误', '');
    exit(0);
}
$this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
$alipay = $setting['payment']['alipay'];
$alipay['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/alipay/notify.php';
$alipay['return_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/alipay/return.php';
$params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $params['uniontid']);
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
$property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $myparams['pid']));
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
$region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myparams['rid']));
if ($region['ispay'] == 1 && $region['isalipay'] == 1 && !empty($region['aliaccount'])) {
    $alipay['account'] = $region['aliaccount'];
    $alipay['partner'] = $region['alipartner'];
    $alipay['secret'] = $region['alisecret'];
} elseif ($property['ispay'] == 1 && $property['isalipay'] == 1 && !empty($property['aliaccount'])) {
    $alipay['account'] = $property['aliaccount'];
    $alipay['partner'] = $property['alipartner'];
    $alipay['secret'] = $property['alisecret'];
} else {
    $alipay['account'] = $this->syscfg['aliaccount'];
    $alipay['partner'] = $this->syscfg['alipartner'];
    $alipay['secret'] = $this->syscfg['alisecret'];
}
$alipay['payfrom'] = 0;
$ret = my_alipay_build($params, $alipay);
if ($ret['url']) {
    echo '<script type="text/javascript" src="../addons/rhinfo_zyxq/payment/alipay/ap.js"></script><script type="text/javascript">_AP.pay("' . $ret['url'] . '")</script>';
    exit(0);
}
echo "\r\n";
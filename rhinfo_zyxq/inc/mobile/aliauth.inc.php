<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$curr = 'aliauth';
$mydo = 'aliauth';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'openid';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
load()->model('mc');
myload()->classs('aliReply');
myload()->model('member');
if ($operation == 'openid') {
    $sysset = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']), array('alipay_life_appid', 'alipay_life_key'));
    $alipay = iunserializer($sysset['alipay_life_key']);
    $config = array('app_id' => $sysset['alipay_life_appid'], 'merchant_private_key' => alikeytostr($alipay['private'], 1), 'merchant_public_key' => alikeytostr($alipay['public']), 'alipay_public_key' => alikeytostr($alipay['ali_public']), 'charset' => 'GBK', 'gatewayUrl' => 'https://openapi.alipay.com/gateway.do', 'sign_type' => 'RSA2');
    $auth_code = $_GPC['auth_code'];
    $ret_url = base64_decode($_GPC['curr_url']);
    $config;
    $userinfo = 'UserInfo';
    $result = $userinfo->getUserInfo($auth_code);
    if (is_error($result)) {
        $this->mymsg('error', '温馨提示', $result['message'], '');
    }
    $_W['openid_alipay'] = $result->user_id;
    $uid = my_openid2uid($_W['openid_alipay']);
    if (_my_login($uid)) {
        header('location: ' . $ret_url);
    } else {
        $cookie = array();
        $cookie['__rhinfo_uniacid'] = $_W['uniacid'];
        $cookie['__rhinfo_time'] = time();
        $cookie['__rhinfo_alipay_openid'] = $_W['openid_alipay'];
        $session = base64_encode(json_encode($cookie));
        $session_key = '__rhinfo_' . $cookie['__rhinfo_uniacid'] . '_session';
        isetcookie($session_key, $session, 0, true);
        isetcookie('__session', '', 0 - 10000);
        header('location: ' . mymurl('myauth'));
    }
    exit(0);
}
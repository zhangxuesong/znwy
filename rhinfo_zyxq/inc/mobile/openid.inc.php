<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'wxapp';
$syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']), array('wxminiappid', 'wxminiappsecret', 'alipay_app_appid', 'alipay_app_key'));
if ($operation == 'wxapp') {
    $code = $_GPC['code'];
    if (empty($code)) {
        $this->errorResult('登录失败');
    }
    $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $syscfg['wxminiappid'] . '&secret=' . $syscfg['wxminiappsecret'] . '&js_code=' . $code . '&grant_type=authorization_code';
    load()->func('communication');
    $resp = ihttp_request($url);
    if (is_error($resp)) {
        $this->errorResult($resp['message']);
    }
    $arr = @json_decode($resp['content'], true);
    if (!is_array($arr) || !isset($arr['openid'])) {
        $this->errorResult('获取失败');
    }
    $union_fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'unionid' => $arr['unionid'], 'openid !=' => $arr['openid']));
    if (!empty($union_fans['uid'])) {
        $uid = $union_fans['uid'];
        $_W['openid_wxapp'] = $arr['unionid'];
        myload()->model('member');
        _my_login($uid);
    }
    $this->successResult($arr);
} elseif ($operation == 'aliapp') {
    $code = $_GPC['code'];
    if (empty($code)) {
        $this->errorResult('登录失败');
    }
    $alipay = iunserializer($syscfg['alipay_app_key']);
    myload()->classs('aliReply');
    myload()->model('member');
    $config = array('app_id' => $syscfg['alipay_app_appid'], 'merchant_private_key' => alikeytostr($alipay['private'], 1), 'merchant_public_key' => alikeytostr($alipay['public']), 'alipay_public_key' => alikeytostr($alipay['ali_public']), 'charset' => 'GBK', 'gatewayUrl' => 'https://openapi.alipay.com/gateway.do', 'sign_type' => 'RSA2');
    $config;
    $userinfo = 'UserInfo';
    $result = $userinfo->getUserInfo($code);
    if (is_error($result)) {
        $this->errorResult($result['message']);
    }
    $_W['openid_aliapp'] = $result->user_id;
    $uid = my_openid2uid($_W['openid_aliapp']);
    if (_mc_login(array('uid' => $uid))) {
        isetcookie('__uniacid', $_W['uniacid'], time() + 30 * 86400);
        $cookie = array();
        $cookie['__rhinfo_uniacid'] = $_W['uniacid'];
        $cookie['__rhinfo_time'] = time();
        $cookie['__rhinfo_uid'] = $uid;
        $cookie['__rhinfo_alipay_openid'] = $_W['openid_aliapp'];
        $cookie['__rhinfo_aliapp_openid'] = $_W['openid_aliapp'];
        $session = base64_encode(json_encode($cookie));
        $session_key = '__rhinfo_' . $cookie['__rhinfo_uniacid'] . '_session';
        isetcookie($session_key, $session, 0, true);
        isetcookie('__session', '', 0 - 10000);
        $this->successResult($result);
    } else {
        $cookie = array();
        $cookie['__rhinfo_uniacid'] = $_W['uniacid'];
        $cookie['__rhinfo_time'] = time();
        $cookie['__rhinfo_alipay_openid'] = $_W['openid_aliapp'];
        $cookie['__rhinfo_aliapp_openid'] = $_W['openid_aliapp'];
        $session = base64_encode(json_encode($cookie));
        $session_key = '__rhinfo_' . $cookie['__rhinfo_uniacid'] . '_session';
        isetcookie($session_key, $session, 0, true);
        isetcookie('__session', '', 0 - 10000);
        $this->errorResult('login fail');
    }
}
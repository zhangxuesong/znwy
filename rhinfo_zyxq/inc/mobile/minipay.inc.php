<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$params = @json_decode(base64_decode($_GPC['params']), true);
if (empty($params)) {
    return $this->errorResult('1');
}
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
$pars = array();
$pars[':uniacid'] = $_W['uniacid'];
$pars[':module'] = $params['module'];
$pars[':tid'] = $params['tid'];
$log = pdo_fetch($sql, $pars);
if (!empty($log) && ($type != 'credit' && !empty($_GPC['notify'])) && $log['status'] != '0') {
    return $this->errorResult('2');
}
$moduleid = pdo_fetchcolumn('SELECT mid FROM ' . tablename('modules') . ' WHERE name = :name', array(':name' => $params['module']));
$moduleid = empty($moduleid) ? '000000' : sprintf('%06d', $moduleid);
$uniontid = date('YmdHis') . $moduleid . random(8, 1);
$myps = array();
$myps['module'] = $params['module'];
$myps['ordersn'] = $params['ordersn'];
$myps['pid'] = $params['pid'];
$myps['rid'] = $params['rid'];
$myps['title'] = $params['title'];
$myps['payuser'] = $params['payuser'];
$myps['billid'] = $params['billid'];
$myps['feetype'] = $params['feetype'];
$myps['creditfee'] = $params['creditfee'];
load()->model('payment');
$setting = uni_setting($_W['uniacid'], array('payment'));
if (!is_array($setting['payment']) && empty($this->syscfg['iswxpay'])) {
    return $this->errorResult('3');
}
$this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
$wechat = $setting['payment']['wechat'];
$wechat['account'] = !empty($wechat['account']) ? $wechat['account'] : $_W['uniacid'];
$config = $this->module['config'];
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
$property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $params['pid']));
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
$region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $params['rid']));
if (empty($log)) {
    $log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'uniontid' => $uniontid, 'type' => 'wechat', 'openid' => $params['payopenid'], 'module' => $params['module'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => 0, 'is_usecard' => '0', 'card_id' => 0, 'uid' => $_W['member']['uid'], 'pid' => $myps['pid'], 'rid' => $myps['rid'], 'feetype' => $myps['feetype'], 'tag' => serialize($myps), 'paytime' => TIMESTAMP);
    pdo_insert('rhinfo_zyxq_wepay_log', $log);
} else {
    $update_card_log = array('is_usecard' => '0', 'card_type' => '0', 'card_id' => '0', 'card_fee' => $log['fee'], 'type' => 'wechat');
    pdo_update('rhinfo_zyxq_wepay_log', $update_card_log, array('tid' => $log['tid']));
}
$sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
$row = pdo_fetch($sql, array(':acid' => $wechat['account']));
$wechat['appid'] = $row['key'];
$wechat['secret'] = $row['secret'];
$wechat['openid'] = $_GPC['appopenid'];
$wechat['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/wechat/notify.php';
$wechat['trade_type'] = 'JSAPI';
$wechat['from'] = 'wxapp';
$bank_wechat = array();
$pay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $uniontid);
if ($region['ispay'] == 1 && $region['paytype'] == 2 && !empty($region['submerchid'])) {
    if (!empty($config['serviceappid'])) {
        $wechat['appid'] = $config['serviceappid'];
        $wechat['secret'] = $config['serviceappsecret'];
    }
    $wechat['sub_appid'] = $this->syscfg['wxminiappid'];
    $wechat['sub_mch_id'] = trim($region['submerchid']);
    $wechat['signkey'] = $config['merchsecret'];
    $wechat['mchid'] = $config['servicemerchid'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($region['ispay'] == 1 && ($region['paytype'] == 4 || $region['paytype'] == 5)) {
    $bank_wechat['bankmerchid'] = $region['paytype'] == 3 ? $region['bankmerchid'] : $region['rsdbankmerchid'];
    $bank_wechat['openid'] = $wechat['openid'];
    $bank_wechat['appid'] = $this->syscfg['wxminiappid'];
    $bank_wechat['ymfurl'] = $region['paytype'] == 3 ? $region['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
    $bank_wechat['bankkey'] = strpos($region['bankkey'], base64_encode('rhinfo')) !== false ? $region['bankkey'] : md5('rhinfo');
    $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
    $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfee')));
    $bank_wechat['successurl'] = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
    $bank_wechat['starorg'] = $region['starorg'];
    $bank_wechat['starmerchid'] = $region['starmerchid'];
    $bank_wechat['startrm'] = $region['startrm'];
    $bank_wechat['starkey'] = $region['starkey'];
    $res = my_bankPay($pay_params, $bank_wechat, $region['paytype']);
    $wOpt = array();
    if (is_error($res)) {
        return $this->errorResult('支付失败', $res['message']);
    }
    if ($region['paytype'] == 3) {
        return $this->errorResult('支付失败', $res['message']);
    }
    if ($region['paytype'] == 4) {
        $wOpt = json_decode($res['pay_info'], true);
    } elseif ($region['paytype'] == 5) {
        $wOpt['PrepayId'] = $res['PrepayId'];
        $wOpt['appId'] = $res['apiappId'];
        $wOpt['timeStamp'] = $res['apitimeStamp'];
        $wOpt['nonceStr'] = $res['apinonceStr'];
        $wOpt['package'] = $res['apipackage'];
        $wOpt['signType'] = $res['apisignType'];
        $wOpt['paySign'] = $res['apipaySign'];
    }
} elseif ($property['ispay'] == 1 && $property['paytype'] == 2 && !empty($property['submerchid'])) {
    if (!empty($config['serviceappid'])) {
        $wechat['appid'] = $config['serviceappid'];
        $wechat['secret'] = $config['serviceappsecret'];
    }
    $wechat['sub_appid'] = $this->syscfg['wxminiappid'];
    $wechat['sub_mch_id'] = trim($property['submerchid']);
    $wechat['signkey'] = $config['merchsecret'];
    $wechat['mchid'] = $config['servicemerchid'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($property['ispay'] == 1 && ($property['paytype'] == 4 || $property['paytype'] == 5)) {
    $bank_wechat['bankmerchid'] = $property['paytype'] == 3 ? $property['bankmerchid'] : $property['rsdbankmerchid'];
    $bank_wechat['openid'] = $_GPC['appopenid'];
    $bank_wechat['appid'] = $this->syscfg['wxminiappid'];
    $bank_wechat['ymfurl'] = $property['paytype'] == 3 ? $property['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
    $bank_wechat['bankkey'] = strpos($property['bankkey'], base64_encode('rhinfo')) !== false ? $property['bankkey'] : md5('rhinfo');
    $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
    $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfee')));
    $bank_wechat['successurl'] = !empty($property['paysuccessurl']) ? $property['paysuccessurl'] : $returl;
    $bank_wechat['starorg'] = $property['starorg'];
    $bank_wechat['starmerchid'] = $property['starmerchid'];
    $bank_wechat['startrm'] = $property['startrm'];
    $bank_wechat['starkey'] = $property['starkey'];
    $res = my_bankPay($pay_params, $bank_wechat, $property['paytype']);
    $wOpt = array();
    if (is_error($res)) {
        return $this->errorResult('支付失败', $res['message']);
    }
    if ($property['paytype'] == 3) {
        return $this->errorResult('支付失败', $res['message']);
    }
    if ($property['paytype'] == 4) {
        $wOpt = json_decode($res['pay_info'], true);
    } elseif ($property['paytype'] == 5) {
        $wOpt['PrepayId'] = $res['PrepayId'];
        $wOpt['appId'] = $res['apiappId'];
        $wOpt['timeStamp'] = $res['apitimeStamp'];
        $wOpt['nonceStr'] = $res['apinonceStr'];
        $wOpt['package'] = $res['apipackage'];
        $wOpt['signType'] = $res['apisignType'];
        $wOpt['paySign'] = $res['apipaySign'];
    }
} elseif ($this->syscfg['iswxpay'] == 1) {
    $wechat['appid'] = $this->syscfg['wxminiappid'];
    $wechat['secret'] = $this->syscfg['wxminiappsecret'];
    if ($this->syscfg['wxminimerchid']) {
        $wechat['mchid'] = $this->syscfg['wxminimerchid'];
        $wechat['signkey'] = $this->syscfg['wxminimerchsecret'];
    }
    $wechat['sub_mch_id'] = '';
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($this->syscfg['iswxpay'] == 2) {
    if (!empty($this->syscfg['serviceappid'])) {
        $wechat['appid'] = $this->syscfg['serviceappid'];
        $wechat['secret'] = $this->syscfg['serviceappsecret'];
    }
    $wechat['sub_appid'] = $this->syscfg['wxminiappid'];
    $wechat['sub_mch_id'] = trim($this->syscfg['submerchid']);
    $wechat['signkey'] = $this->syscfg['servicemerchsecret'];
    $wechat['mchid'] = $this->syscfg['servicemerchid'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($this->syscfg['iswxpay'] == 4 || $this->syscfg['iswxpay'] == 5) {
    $bank_wechat['bankmerchid'] = $this->syscfg['iswxpay'] == 3 ? $this->syscfg['bankmerchid'] : $this->syscfg['rsdbankmerchid'];
    $bank_wechat['openid'] = $wechat['openid'];
    $bank_wechat['appid'] = $this->syscfg['wxminiappid'];
    $bank_wechat['ymfurl'] = $this->syscfg['iswxpay'] == 3 ? $this->syscfg['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
    $bank_wechat['bankkey'] = strpos($this->syscfg['bankkey'], base64_encode('rhinfo')) !== false ? $this->syscfg['bankkey'] : md5('rhinfo');
    $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
    $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
    $bank_wechat['successurl'] = $returl;
    $bank_wechat['starorg'] = $this->syscfg['starorg'];
    $bank_wechat['starmerchid'] = $this->syscfg['starmerchid'];
    $bank_wechat['startrm'] = $this->syscfg['startrm'];
    $bank_wechat['starkey'] = $this->syscfg['starkey'];
    $res = my_bankPay($pay_params, $bank_wechat, $this->syscfg['iswxpay']);
    $wOpt = array();
    if (is_error($res)) {
        return $this->errorResult('支付失败', $res['message']);
    }
    if ($this->syscfg['iswxpay'] == 3) {
        return $this->errorResult('支付失败', $res['message']);
    }
    if ($this->syscfg['iswxpay'] == 4) {
        $wOpt = json_decode($res['pay_info'], true);
    } elseif ($this->syscfg['iswxpay'] == 5) {
        $wOpt['PrepayId'] = $res['PrepayId'];
        $wOpt['appId'] = $res['apiappId'];
        $wOpt['timeStamp'] = $res['apitimeStamp'];
        $wOpt['nonceStr'] = $res['apinonceStr'];
        $wOpt['package'] = $res['apipackage'];
        $wOpt['signType'] = $res['apisignType'];
        $wOpt['paySign'] = $res['apiPaysign'];
    }
} else {
    $wechat['appid'] = $this->syscfg['wxminiappid'];
    $wechat['secret'] = $this->syscfg['wxminiappsecret'];
    if ($this->syscfg['wxminimerchid']) {
        $wechat['mchid'] = $this->syscfg['wxminimerchid'];
        $wechat['signkey'] = $this->syscfg['wxminimerchsecret'];
    }
    $wechat['sub_mch_id'] = '';
    $wOpt = my_wechat_build($pay_params, $wechat);
}
if (!is_error($wOpt)) {
    return $this->successResult($wOpt);
}
if ($wOpt['message'] == 'invalid out_trade_no' || $wOpt['message'] == 'OUT_TRADE_NO_USED') {
    return $this->errorResult('支付失败,系统已经修复此问题，请重新尝试支付.');
}
return $this->errorResult('支付失败,' . $wOpt['errno'] . ':' . $wOpt['message']);
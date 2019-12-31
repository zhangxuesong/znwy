<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$ps = $_GPC['ps'];
$myps = $_GPC['myps'];
$params = @json_decode(base64_decode($ps), true);
$myparams = @json_decode(base64_decode($myps), true);
if ($_GPC['done'] == '1') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `uniontid`=:uniontid';
    $pars = array();
    $pars[':uniontid'] = $params['uniontid'];
    $log = pdo_fetch($sql, $pars);
    if (!empty($log)) {
        if (!empty($log['tag'])) {
            $tag = unserialize($log['tag']);
            $log['uid'] = $tag['uid'];
        }
        pdo_update('rhinfo_zyxq_wepay_log', array('status' => 1), array('tid' => $params['tid']));
        $ret = array();
        $ret['weid'] = $log['uniacid'];
        $ret['uniacid'] = $log['uniacid'];
        $ret['result'] = 'success';
        $ret['type'] = $log['type'];
        $ret['from'] = 'return';
        $ret['tid'] = $log['tid'];
        $ret['uniontid'] = $log['uniontid'];
        $ret['user'] = $log['openid'];
        $ret['fee'] = $log['fee'];
        $ret['tag'] = $tag;
        $ret['is_usecard'] = $log['is_usecard'];
        $ret['card_type'] = $log['card_type'];
        $ret['card_fee'] = $log['card_fee'];
        $ret['card_id'] = $log['card_id'];
        $this->payResult($ret);
    }
}
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_wepay_log') . ' WHERE `tid`=:tid';
$log = pdo_fetch($sql, array(':tid' => $params['tid']));
if (!empty($log) && $log['status'] != '0') {
    $redirect = $this->createMobileUrl('member', array('op' => 'index'));
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
$this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
load()->model('payment');
$setting = uni_setting($_W['uniacid'], array('payment'));
if (!is_array($setting['payment']) && empty($this->syscfg['iswxpay'])) {
    $this->mymsg('error', '支付失败', '支付参数错误', '');
    exit(0);
}
$wechat = $setting['payment']['wechat'];
$wechat['account'] = !empty($wechat['account']) ? $wechat['account'] : $_W['uniacid'];
$sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
$row = pdo_fetch($sql, array(':acid' => $wechat['account']));
$wechat['appid'] = $row['key'];
$wechat['secret'] = $row['secret'];
$wechat['openid'] = empty($myparams['openid']) ? $_W['fans']['from_user'] : $myparams['openid'];
$wechat['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/wechat/notify.php';
$wechat['trade_type'] = 'JSAPI';
if ($this->syscfg['iswap'] && $this->rhinfo_wap) {
    $wechat['trade_type'] = 'MWEB';
    $wechat['openid'] = '';
}
$pay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $params['uniontid']);
$config = $this->module['config'];
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
$property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $myparams['pid']));
$sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
$region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myparams['rid']));
if ($region['ispay'] == 1 && $region['paytype'] == 2 && !empty($region['submerchid'])) {
    if (!empty($config['serviceappid'])) {
        $wechat['sub_appid'] = $wechat['appid'];
        $wechat['appid'] = $config['serviceappid'];
        $wechat['secret'] = $config['serviceappsecret'];
    }
    $wechat['sub_mch_id'] = trim($region['submerchid']);
    $wechat['signkey'] = $config['merchsecret'];
    $wechat['mchid'] = $config['servicemerchid'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($region['ispay'] == 1 && ($region['paytype'] == 3 || $region['paytype'] == 4 || $region['paytype'] == 5)) {
    $bank_wechat = array();
    $pay_params['clientip'] = $_W['clientip'];
    $bank_wechat['bankmerchid'] = $region['paytype'] == 3 ? $region['bankmerchid'] : $region['rsdbankmerchid'];
    $bank_wechat['openid'] = $myparams['openid'];
    $bank_wechat['appid'] = $wechat['appid'];
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
        $this->mymsg('error', '支付失败', $res['message'], '');
        exit(0);
    }
    if ($region['paytype'] == 3) {
        header('location:' . $res['url']);
        exit(0);
    } elseif ($region['paytype'] == 4) {
        $wOpt = json_decode($res['pay_info'], true);
    } elseif ($region['paytype'] == 5) {
        $wOpt['PrepayId'] = $res['PrepayId'];
        $wOpt['appId'] = $res['apiAppid'];
        $wOpt['timeStamp'] = $res['apiTimestamp'];
        $wOpt['nonceStr'] = $res['apiNoncestr'];
        $wOpt['package'] = $res['apiPackage'];
        $wOpt['signType'] = $res['apiSigntype'];
        $wOpt['paySign'] = $res['apiPaysign'];
    }
} elseif ($property['ispay'] == 1 && $property['paytype'] == 2 && !empty($property['submerchid'])) {
    if (!empty($config['serviceappid'])) {
        $wechat['sub_appid'] = $wechat['appid'];
        $wechat['appid'] = $config['serviceappid'];
        $wechat['secret'] = $config['serviceappsecret'];
    }
    $wechat['sub_mch_id'] = trim($property['submerchid']);
    $wechat['signkey'] = $config['merchsecret'];
    $wechat['mchid'] = $config['servicemerchid'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($property['ispay'] == 1 && ($property['paytype'] == 3 || $property['paytype'] == 4 || $property['paytype'] == 5)) {
    $bank_wechat = array();
    $pay_params['clientip'] = $_W['clientip'];
    $bank_wechat['bankmerchid'] = $property['paytype'] == 3 ? $property['bankmerchid'] : $property['rsdbankmerchid'];
    $bank_wechat['openid'] = $myparams['openid'];
    $bank_wechat['appid'] = $wechat['appid'];
    $bank_wechat['ymfurl'] = $property['paytype'] == 3 ? $property['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
    $bank_wechat['bankkey'] = strpos($property['bankkey'], base64_encode('rhinfo')) !== false ? $property['bankkey'] : md5('rhinfo');
    $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
    $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfee')));
    $bank_wechat['starorg'] = $property['starorg'];
    $bank_wechat['starmerchid'] = $property['starmerchid'];
    $bank_wechat['startrm'] = $property['startrm'];
    $bank_wechat['starkey'] = $property['starkey'];
    $bank_wechat['successurl'] = !empty($property['paysuccessurl']) ? $property['paysuccessurl'] : $returl;
    $res = my_bankPay($pay_params, $bank_wechat, $property['paytype']);
    $wOpt = array();
    if (is_error($res)) {
        $this->mymsg('error', '支付失败', $res['message'], '');
        exit(0);
    }
    if ($property['paytype'] == 3) {
        header('location:' . $res['url']);
        exit(0);
    } elseif ($property['paytype'] == 4) {
        $wOpt = json_decode($res['pay_info'], true);
    } elseif ($property['paytype'] == 5) {
        $wOpt['PrepayId'] = $res['PrepayId'];
        $wOpt['appId'] = $res['apiAppid'];
        $wOpt['timeStamp'] = $res['apiTimestamp'];
        $wOpt['nonceStr'] = $res['apiNoncestr'];
        $wOpt['package'] = $res['apiPackage'];
        $wOpt['signType'] = $res['apiSigntype'];
        $wOpt['paySign'] = $res['apiPaysign'];
    }
} elseif ($this->syscfg['iswap'] && $this->rhinfo_wap) {
    if (!empty($this->syscfg['wxappid'])) {
        $wechat['appid'] = $this->syscfg['wxappid'];
        $wechat['secret'] = $this->syscfg['wxappsecret'];
        $wechat['mchid'] = $this->syscfg['wxmerchid'];
        $wechat['signkey'] = $this->syscfg['wxmerchsecret'];
        $wechat['apikey'] = $this->syscfg['wxmerchsecret'];
        $wechat['sub_mch_id'] = '';
    }
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($this->syscfg['iswxpay'] == 1) {
    $wechat['appid'] = $this->syscfg['appid'];
    $wechat['secret'] = $this->syscfg['secret'];
    $wechat['mchid'] = $this->syscfg['merchid'];
    $wechat['signkey'] = $this->syscfg['merchsecret'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($this->syscfg['iswxpay'] == 2) {
    if (!empty($this->syscfg['serviceappid'])) {
        $wechat['sub_appid'] = $wechat['appid'];
        $wechat['appid'] = $this->syscfg['serviceappid'];
        $wechat['secret'] = $this->syscfg['serviceappsecret'];
    }
    $wechat['sub_mch_id'] = trim($this->syscfg['submerchid']);
    $wechat['signkey'] = $this->syscfg['servicemerchsecret'];
    $wechat['mchid'] = $config['servicemerchid'];
    $wOpt = my_wechat_build($pay_params, $wechat);
} elseif ($this->syscfg['iswxpay'] == 3 || $this->syscfg['iswxpay'] == 4 || $this->syscfg['iswxpay'] == 5) {
    $bank_wechat = array();
    $pay_params['clientip'] = $_W['clientip'];
    $bank_wechat['bankmerchid'] = $this->syscfg['iswxpay'] == 3 ? $this->syscfg['ymfmerchid'] : $this->syscfg['rsdmerchid'];
    $bank_wechat['openid'] = $myparams['openid'];
    $bank_wechat['appid'] = $wechat['appid'];
    $bank_wechat['ymfurl'] = $this->syscfg['iswxpay'] == 3 ? $this->syscfg['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
    $bank_wechat['bankkey'] = strpos($this->syscfg['rsdmerchsecret'], base64_encode('rhinfo')) !== false ? $this->syscfg['rsdmerchsecret'] : md5('rhinfo');
    $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
    $bank_wechat['successurl'] = $payparams['returl'];
    $bank_wechat['starorg'] = $this->syscfg['starorg'];
    $bank_wechat['starmerchid'] = $this->syscfg['starmerchid'];
    $bank_wechat['startrm'] = $this->syscfg['startrm'];
    $bank_wechat['starkey'] = $this->syscfg['starkey'];
    $res = my_bankPay($mypay_params, $bank_wechat, $this->syscfg['iswxpay']);
    $wOpt = array();
    if (is_error($res)) {
        $this->mymsg('error', '支付失败', $res['message'], '');
        exit(0);
    }
    if ($this->syscfg['iswxpay'] == 3) {
        header('location:' . $res['url']);
        exit(0);
    } elseif ($this->syscfg['iswxpay']['paytype'] == 4) {
        $wOpt = json_decode($res['pay_info'], true);
    } elseif ($this->syscfg['iswxpay']['paytype'] == 5) {
        $wOpt['PrepayId'] = $res['PrepayId'];
        $wOpt['appId'] = $res['apiAppid'];
        $wOpt['timeStamp'] = $res['apiTimestamp'];
        $wOpt['nonceStr'] = $res['apiNoncestr'];
        $wOpt['package'] = $res['apiPackage'];
        $wOpt['signType'] = $res['apiSigntype'];
        $wOpt['paySign'] = $res['apiPaysign'];
    }
} else {
    $wOpt = my_wechat_build($pay_params, $wechat);
}
if (is_error($wOpt)) {
    if ($wOpt['message'] == 'invalid out_trade_no' || $wOpt['message'] == 'OUT_TRADE_NO_USED') {
        $this->mymsg('error', '支付失败', '系统已经修复此问题，请重新尝试支付.', '');
        exit(0);
    }
    $this->mymsg('error', '支付失败', $wOpt['errno'] . ':' . $wOpt['message'], '');
    exit(0);
}
if ($this->syscfg['iswap'] && $this->rhinfo_wap && !empty($wOpt['mweb_url'])) {
    $url = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'index')));
    $url = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $url;
    $MWEB_URL = $wOpt['mweb_url'];
    if (!empty($url)) {
        $MWEB_URL .= '&redirect_url=' . urlencode($url);
    }
    header('Location:' . $MWEB_URL);
    exit(0);
}
echo "<script type=\"text/javascript\">\r\n\tdocument.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {\t\t\r\n\t\tWeixinJSBridge.invoke('getBrandWCPayRequest', {\r\n\t\t\t'appId' : '";
echo $wOpt['appId'];
echo "',\r\n\t\t\t'timeStamp': '";
echo $wOpt['timeStamp'];
echo "',\r\n\t\t\t'nonceStr' : '";
echo $wOpt['nonceStr'];
echo "',\r\n\t\t\t'package' : '";
echo $wOpt['package'];
echo "',\r\n\t\t\t'signType' : '";
echo $wOpt['signType'];
echo "',\r\n\t\t\t'paySign' : '";
echo $wOpt['paySign'];
echo "'\r\n\t\t}, function(res) {\t\t\t\r\n\t\t\tif(res.err_msg == 'get_brand_wcpay_request:ok') {\t\t\t\t\r\n\t\t\t\twindow.location.href += '&done=1';\r\n\t\t\t} else {\r\n\t\t\t\thistory.go(-1);\r\n\t\t\t}\r\n\t\t});\r\n\t}, false);\r\n</script>\r\n";
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
$pars[':module'] = 'rhinfo_zyxq';
$pars[':tid'] = $params['tid'];
$log = pdo_fetch($sql, $pars);
if (empty($log)) {
    return $this->errorResult('2');
}
if ($log['status'] == 1) {
    return $this->errorResult('3');
}
$tag = unserialize($log['tag']);
load()->model('payment');
$setting = uni_setting($_W['uniacid'], array('payment'));
if (!is_array($setting['payment']) && empty($this->syscfg['iswxpay'])) {
    return $this->errorResult('4');
}
$this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
$wechat = $setting['payment']['wechat'];
$wechat['account'] = !empty($wechat['account']) ? $wechat['account'] : $_W['uniacid'];
$config = $this->module['config'];
$sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
$row = pdo_fetch($sql, array(':acid' => $wechat['account']));
$wechat['appid'] = $row['key'];
$wechat['secret'] = $row['secret'];
$wechat['openid'] = $_GPC['appopenid'];
$wechat['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/wechat/notify.php';
$wechat['trade_type'] = 'JSAPI';
$wechat['from'] = 'wxapp';
$wechat['sub_mch_id'] = '';
$bank_wechat = array();
$pay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => $params['title'], 'uniontid' => $params['uniontid']);
if ($tag['feetype'] == 8) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business') . ' WHERE weid=:weid and id=:id';
    $business = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $tag['bid']));
    if ($business['ispay'] == 1 && $business['paytype'] == 2 && !empty($business['submerchid'])) {
        $config = $this->module['config'];
        if (!empty($config['serviceappid'])) {
            $wechat['appid'] = $config['serviceappid'];
            $wechat['secret'] = $config['serviceappsecret'];
        }
        $wechat['sub_appid'] = $this->syscfg['wxminiappid'];
        $wechat['sub_mch_id'] = trim($business['submerchid']);
        $wechat['signkey'] = $config['merchsecret'];
        $wechat['mchid'] = $config['servicemerchid'];
        $wOpt = my_wechat_build($pay_params, $wechat);
    } elseif ($business['ispay'] == 1 && ($business['paytype'] == 4 || $business['paytype'] == 5)) {
        $bank_wechat['bankmerchid'] = $business['paytype'] == 3 ? $business['bankmerchid'] : $business['rsdbankmerchid'];
        $bank_wechat['openid'] = $wechat['openid'];
        $bank_wechat['appid'] = $this->syscfg['wxminiappid'];
        $bank_wechat['ymfurl'] = $business['paytype'] == 3 ? $business['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
        $bank_wechat['bankkey'] = strpos($business['bankkey'], base64_encode('rhinfo')) !== false ? $business['bankkey'] : md5('rhinfo');
        $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
        $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfee')));
        $bank_wechat['successurl'] = !empty($business['paysuccessurl']) ? $business['paysuccessurl'] : $returl;
        $bank_wechat['starorg'] = $business['starorg'];
        $bank_wechat['starmerchid'] = $business['starmerchid'];
        $bank_wechat['startrm'] = $business['startrm'];
        $bank_wechat['starkey'] = $business['starkey'];
        $res = my_bankPay($pay_params, $bank_wechat, $business['paytype']);
        $wOpt = array();
        if (is_error($res)) {
            return $this->errorResult('支付失败', $res['message']);
        }
        if ($business['paytype'] == 3) {
            return $this->errorResult('支付失败', $res['message']);
        }
        if ($business['paytype'] == 4) {
            $wOpt = json_decode($res['pay_info'], true);
        } elseif ($business['paytype'] == 5) {
            $wOpt['PrepayId'] = $res['PrepayId'];
            $wOpt['appId'] = $res['apiappId'];
            $wOpt['timeStamp'] = $res['apitimeStamp'];
            $wOpt['nonceStr'] = $res['apinonceStr'];
            $wOpt['package'] = $res['apipackage'];
            $wOpt['signType'] = $res['apisignType'];
            $wOpt['paySign'] = $res['apipaySign'];
        }
    }
} elseif ($tag['feetype'] == 10 || $tag['feetype'] == 17) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_express_store') . ' WHERE weid=:weid and id=:id';
    $express_store = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $tag['sid']));
    if ($express_store['ispay'] == 1 && $express_store['paytype'] == 2 && !empty($express_store['submerchid'])) {
        $config = $this->module['config'];
        if (!empty($config['serviceappid'])) {
            $wechat['appid'] = $config['serviceappid'];
            $wechat['secret'] = $config['serviceappsecret'];
        }
        $wechat['sub_appid'] = $this->syscfg['wxminiappid'];
        $wechat['sub_mch_id'] = trim($express_store['submerchid']);
        $wechat['signkey'] = $config['merchsecret'];
        $wechat['mchid'] = $config['servicemerchid'];
        $wOpt = my_wechat_build($pay_params, $wechat);
    } elseif ($express_store['ispay'] == 1 && ($express_store['paytype'] == 4 || $express_store['paytype'] == 5)) {
        $bank_wechat['bankmerchid'] = $express_store['paytype'] == 3 ? $express_store['bankmerchid'] : $express_store['rsdbankmerchid'];
        $bank_wechat['openid'] = $wechat['openid'];
        $bank_wechat['appid'] = $this->syscfg['wxminiappid'];
        $bank_wechat['ymfurl'] = $express_store['paytype'] == 3 ? $express_store['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
        $bank_wechat['bankkey'] = strpos($express_store['bankkey'], base64_encode('rhinfo')) !== false ? $express_store['bankkey'] : md5('rhinfo');
        $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
        $returl = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'myfee')));
        $bank_wechat['successurl'] = !empty($express_store['paysuccessurl']) ? $express_store['paysuccessurl'] : $returl;
        $bank_wechat['starorg'] = $express_store['starorg'];
        $bank_wechat['starmerchid'] = $express_store['starmerchid'];
        $bank_wechat['startrm'] = $express_store['startrm'];
        $bank_wechat['starkey'] = $express_store['starkey'];
        $res = my_bankPay($pay_params, $bank_wechat, $express_store['paytype']);
        $wOpt = array();
        if (is_error($res)) {
            return $this->errorResult('支付失败', $res['message']);
        }
        if ($express_store['paytype'] == 3) {
            return $this->errorResult('支付失败', $res['message']);
        }
        if ($express_store['paytype'] == 4) {
            $wOpt = json_decode($res['pay_info'], true);
        } elseif ($express_store['paytype'] == 5) {
            $wOpt['PrepayId'] = $res['PrepayId'];
            $wOpt['appId'] = $res['apiappId'];
            $wOpt['timeStamp'] = $res['apitimeStamp'];
            $wOpt['nonceStr'] = $res['apinonceStr'];
            $wOpt['package'] = $res['apipackage'];
            $wOpt['signType'] = $res['apisignType'];
            $wOpt['paySign'] = $res['apipaySign'];
        }
    }
} elseif ($tag['feetype'] == 4 || $tag['feetype'] == 5 || $tag['feetype'] == 15 || $tag['feetype'] == 16 || $tag['feetype'] == 18 || $tag['feetype'] == 19 || $tag['feetype'] == 20) {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:id';
    $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $tag['pid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:id';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $tag['rid']));
    if ($region['ispay'] == 1 && $region['paytype'] == 2 && !empty($region['submerchid'])) {
        $config = $this->module['config'];
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
        $config = $this->module['config'];
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
        $wOpt['appId'] = $res['apiAppid'];
        $wOpt['timeStamp'] = $res['apiTimestamp'];
        $wOpt['nonceStr'] = $res['apiNoncestr'];
        $wOpt['package'] = $res['apiPackage'];
        $wOpt['signType'] = $res['apiSigntype'];
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
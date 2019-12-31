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
if ($_W['isajax']) {
    if (empty($params) || !array_key_exists($params['module'], $moduels)) {
        show_json(0, '支付异常错误');
    }
} elseif (empty($params) || !array_key_exists($params['module'], $moduels)) {
    $this->mymsg('error', '支付失败', '访问错误.', '');
    exit(0);
}
$this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
load()->model('payment');
$setting = uni_setting($_W['uniacid'], 'payment');
$dos = array();
if (!empty($setting['payment']['wechat']['switch']) || !empty($this->syscfg['iswxpay'])) {
    $dos[] = 'wechat';
}
if (!empty($setting['payment']['alipay']['switch']) || !empty($this->syscfg['isalipay'])) {
    $dos[] = 'alipay';
}
$do = $_GPC['op'];
$type = in_array($do, $dos) ? $do : '';
if ($_W['isajax']) {
    if (empty($type)) {
        show_json(0, '相关支付功能还未开启');
    }
} elseif (empty($type)) {
    $this->mymsg('error', '支付失败', '相关支付功能还未开启', '');
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
    if ($_W['isajax']) {
        if (!empty($log) && ($type != 'credit' && !empty($_GPC['notify'])) && $log['status'] != '0') {
            show_json(0, '这个订单已经支付成功, 不需要重复支付');
        }
    } elseif (!empty($log) && ($type != 'credit' && !empty($_GPC['notify'])) && $log['status'] != '0') {
        $redirect = $this->createMobileUrl('member', array('op' => 'index'));
        $this->mymsg('error', '这个订单已经支付成功, 不需要重复支付', $redirect);
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
    $myps['payuser'] = $params['payuser'];
    $myps['billid'] = $params['billid'];
    $myps['feetype'] = $params['feetype'];
    $myps['creditfee'] = $params['creditfee'];
    if (empty($log)) {
        $log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'uniontid' => $uniontid, 'type' => $type, 'openid' => $_W['openid'], 'module' => $params['module'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => 0, 'is_usecard' => '0', 'card_id' => 0, 'uid' => $_W['member']['uid'], 'pid' => $myps['pid'], 'rid' => $myps['rid'], 'feetype' => $myps['feetype'], 'tag' => serialize($myps), 'paytime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_wepay_log', $log);
    } else {
        $update_card_log = array('is_usecard' => '0', 'card_type' => '0', 'card_id' => '0', 'card_fee' => $log['fee'], 'type' => $type);
        pdo_update('rhinfo_zyxq_wepay_log', $update_card_log, array('tid' => $log['tid']));
    }
    $ps['title'] = urlencode($params['title']);
    $myps['openid'] = $params['payopenid'];
    $pay_params = @base64_encode(json_encode($ps));
    $my_params = @base64_encode(json_encode($myps));
    $auth = sha1($pay_params . $_W['uniacid'] . $_W['config']['setting']['authkey']);
    if ($type == 'wechat') {
        if ($_W['isajax']) {
            $wechat = $setting['payment']['wechat'];
            $wechat['account'] = !empty($wechat['account']) ? $wechat['account'] : $_W['uniacid'];
            $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
            $row = pdo_fetch($sql, array(':acid' => $wechat['account']));
            $wechat['appid'] = $row['key'];
            $wechat['secret'] = $row['secret'];
            $wechat['openid'] = $_W['openid'];
            $wechat['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/wechat/notify.php';
            $wechat['trade_type'] = 'JSAPI';
            if ($this->syscfg['iswap'] && $this->rhinfo_wap) {
                $wechat['trade_type'] = 'MWEB';
                $wechat['openid'] = '';
            }
            $pay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $uniontid);
            $config = $this->module['config'];
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
            $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $params['pid']));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $params['rid']));
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
                $bank_wechat['openid'] = $_W['openid'];
                $bank_wechat['appid'] = $wechat['appid'];
                $bank_wechat['ymfurl'] = $region['paytype'] == 3 ? $region['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
                $bank_wechat['bankkey'] = strpos($region['bankkey'], base64_encode('rhinfo')) !== false ? $region['bankkey'] : md5('rhinfo');
                $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
                $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
                $bank_wechat['successurl'] = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
                $bank_wechat['starorg'] = $region['starorg'];
                $bank_wechat['starmerchid'] = $region['starmerchid'];
                $bank_wechat['startrm'] = $region['startrm'];
                $bank_wechat['starkey'] = $region['starkey'];
                $res = my_bankPay($pay_params, $bank_wechat, $region['paytype']);
                $wOpt = array();
                if (is_error($res)) {
                    show_json(0, '支付失败' . $res['message']);
                }
                if ($region['paytype'] == 3) {
                    $wOpt['isymf'] = 1;
                    $wOpt['ymfurl'] = $res['url'];
                    show_json(1, array('wechat' => $wOpt));
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
                $bank_wechat['openid'] = $_W['openid'];
                $bank_wechat['appid'] = $wechat['appid'];
                $bank_wechat['ymfurl'] = $property['paytype'] == 3 ? $property['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
                $bank_wechat['bankkey'] = strpos($property['bankkey'], base64_encode('rhinfo')) !== false ? $property['bankkey'] : md5('rhinfo');
                $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
                $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
                $bank_wechat['starorg'] = $property['starorg'];
                $bank_wechat['starmerchid'] = $property['starmerchid'];
                $bank_wechat['startrm'] = $property['startrm'];
                $bank_wechat['starkey'] = $property['starkey'];
                $bank_wechat['successurl'] = !empty($property['paysuccessurl']) ? $property['paysuccessurl'] : $returl;
                $res = my_bankPay($pay_params, $bank_wechat, $property['paytype']);
                $wOpt = array();
                if (is_error($res)) {
                    show_json(0, '支付失败' . $res['message']);
                }
                if ($property['paytype'] == 3) {
                    $wOpt['isymf'] = 1;
                    $wOpt['ymfurl'] = $res['url'];
                    show_json(1, array('wechat' => $wOpt));
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
                $bank_wechat['openid'] = $_W['openid'];
                $bank_wechat['appid'] = $wechat['appid'];
                $bank_wechat['ymfurl'] = $this->syscfg['iswxpay'] == 3 ? $this->syscfg['ymfurl'] : 'https://pay.swiftpass.cn/pay/gateway';
                $bank_wechat['bankkey'] = strpos($this->syscfg['rsdmerchsecret'], base64_encode('rhinfo')) !== false ? $this->syscfg['rsdmerchsecret'] : md5('rhinfo');
                $bank_wechat['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
                $bank_wechat['successurl'] = $payparams['returl'];
                $bank_wechat['starorg'] = $this->syscfg['starorg'];
                $bank_wechat['starmerchid'] = $this->syscfg['starmerchid'];
                $bank_wechat['startrm'] = $this->syscfg['startrm'];
                $bank_wechat['starkey'] = $this->syscfg['starkey'];
                $res = my_bankPay($pay_params, $bank_wechat, $this->syscfg['iswxpay']);
                $wOpt = array();
                if (is_error($res)) {
                    show_json(0, '支付失败' . $res['message']);
                }
                if ($this->syscfg['iswxpay'] == 3) {
                    $wOpt['isymf'] = 1;
                    $wOpt['ymfurl'] = $res['url'];
                    show_json(1, array('wechat' => $wOpt));
                } elseif ($this->syscfg['iswxpay'] == 4) {
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
                $wOpt = my_wechat_build($pay_params, $wechat);
            }
            if (is_error($wOpt)) {
                if ($wOpt['message'] == 'invalid out_trade_no' || $wOpt['message'] == 'OUT_TRADE_NO_USED') {
                    show_json(0, '支付失败,系统已经修复此问题,请重新尝试支付');
                }
                show_json(0, '支付失败' . $wOpt['errno'] . ':' . $wOpt['message']);
            }
            if ($this->syscfg['iswap'] && $this->rhinfo_wap && !empty($wOpt['mweb_url'])) {
                $url = $this->my_mobileurl($this->createMobileUrl('member', array('op' => 'index')));
                $url = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $url;
                $MWEB_URL = $wOpt['mweb_url'];
                if (!empty($url)) {
                    $MWEB_URL .= '&redirect_url=' . urlencode($url);
                }
                $wOpt['mweb_url'] = $MWEB_URL;
                $wOpt['iswap'] = 1;
            }
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            $wOpt['successurl'] = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
            show_json(1, array('wechat' => $wOpt));
        }
        $payurl = $this->createMobileUrl('pay', array('i' => $_W['uniacid'], 'auth' => $auth, 'ps' => $pay_params, 'myps' => $my_params));
    } elseif ($type == 'alipay') {
        if ($_W['isajax']) {
            $pay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $uniontid);
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
            $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $params['pid']));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $params['rid']));
            $alipay = array();
            $alipay['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/alipay/notify.php';
            $alipay['return_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/alipay/return.php';
            $returl = $this->my_mobileurl($this->createMobileUrl('home', array('op' => 'index')));
            if ($region['ispay'] == 1 && $region['isalipay'] == 1) {
                $returl = !empty($region['paysuccessurl']) ? $region['paysuccessurl'] : $returl;
                if ($region['alipay_type'] == 1) {
                    $alipay['appid'] = $region['alipay_appid'];
                    $alipay['public_key'] = $region['alipay_rsa2'];
                    $alipay['private_key'] = $region['alipay_private'];
                    $alipay['seller_id'] = $region['alipay_seller_id'];
                    $alipay['app_auth_token'] = $region['alipay_app_auth_token'];
                    $alipay['openid'] = $_W['openid_alipay'];
                } elseif ($region['alipay_type'] == 2) {
                    $bank_alipay = array();
                    $mypay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $uniontid, 'clientip' => $_W['clientip']);
                    $bank_alipay['openid'] = $_W['openid_alipay'];
                    $bank_alipay['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
                    $bank_alipay['successurl'] = $returl;
                    $bank_alipay['starorg'] = $region['starorg'];
                    $bank_alipay['starmerchid'] = $region['starmerchid'];
                    $bank_alipay['startrm'] = $region['startrm'];
                    $bank_alipay['starkey'] = $region['starkey'];
                    $res = my_bankPay($mypay_params, $bank_alipay, 8);
                    if (is_error($res)) {
                        show_json(0, $res['message']);
                    }
                    show_json(1, array('alipay' => array('trade_no' => $res['PrepayId'], 'returl' => $returl)));
                }
            } elseif ($property['ispay'] == 1 && $property['isalipay'] == 1) {
                $returl = !empty($property['paysuccessurl']) ? $property['paysuccessurl'] : $returl;
                if ($property['alipay_type'] == 1) {
                    $alipay['appid'] = $property['alipay_appid'];
                    $alipay['public_key'] = $property['alipay_rsa2'];
                    $alipay['private_key'] = $property['alipay_private'];
                    $alipay['seller_id'] = $property['alipay_seller_id'];
                    $alipay['app_auth_token'] = $property['alipay_app_auth_token'];
                    $alipay['openid'] = $_W['openid_alipay'];
                } elseif ($property['alipay_type'] == 2) {
                    $bank_alipay = array();
                    $mypay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $uniontid, 'clientip' => $_W['clientip']);
                    $bank_alipay['openid'] = $_W['openid_alipay'];
                    $bank_alipay['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
                    $bank_alipay['successurl'] = $returl;
                    $bank_alipay['starorg'] = $property['starorg'];
                    $bank_alipay['starmerchid'] = $property['starmerchid'];
                    $bank_alipay['startrm'] = $property['startrm'];
                    $bank_alipay['starkey'] = $property['starkey'];
                    $res = my_bankPay($mypay_params, $bank_alipay, 8);
                    if (is_error($res)) {
                        show_json(0, $res['message']);
                    }
                    show_json(1, array('alipay' => array('trade_no' => $res['PrepayId'], 'returl' => $returl)));
                }
            } elseif ($this->syscfg['alipay_type'] == 1) {
                $alipay['appid'] = $this->syscfg['alipay_appid'];
                $alipay['public_key'] = $this->syscfg['alipay_rsa2'];
                $alipay['private_key'] = $this->syscfg['alipay_private'];
                $alipay['seller_id'] = $this->syscfg['alipay_seller_id'];
                $alipay['app_auth_token'] = $this->syscfg['alipay_app_auth_token'];
                $alipay['openid'] = $_W['openid_alipay'];
            } elseif ($this->syscfg['alipay_type'] == 2) {
                $bank_alipay = array();
                $mypay_params = array('tid' => $params['tid'], 'fee' => $params['fee'], 'user' => $params['user'], 'title' => urldecode($params['title']), 'uniontid' => $uniontid, 'clientip' => $_W['clientip']);
                $bank_alipay['openid'] = $_W['openid_alipay'];
                $bank_alipay['notifyurl'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/bankpay/notify.php';
                $bank_alipay['successurl'] = $returl;
                $bank_alipay['starorg'] = $this->syscfg['starorg'];
                $bank_alipay['starmerchid'] = $this->syscfg['starmerchid'];
                $bank_alipay['startrm'] = $this->syscfg['startrm'];
                $bank_alipay['starkey'] = $this->syscfg['starkey'];
                $res = my_bankPay($mypay_params, $bank_alipay, 8);
                if (is_error($res)) {
                    show_json(0, $res['message']);
                }
                show_json(1, array('alipay' => array('trade_no' => $res['PrepayId'], 'returl' => $returl)));
            }
            $alipay['payfrom'] = 1;
            if (!empty($alipay['appid'])) {
                $alipay['notify_url'] = $this->syscfg['siteurl'] . 'addons/rhinfo_zyxq/payment/aliapp/notify.php';
                if ($this->rhinfo_isalipay) {
                    $ret = my_alipay_build2($pay_params, $alipay, 1);
                } else {
                    $ret = my_alipay_build2($pay_params, $alipay);
                }
            } else {
                show_json(0, '支付失败,请检查支付参数');
            }
            if (is_error($ret)) {
                show_json(0, $ret['message']);
            }
            show_json(1, array('alipay' => array('trade_no' => $ret['trade_no'], 'returl' => $returl)));
        }
        $payurl = $this->createMobileUrl('alipay', array('i' => $_W['uniacid'], 'auth' => $auth, 'ps' => $pay_params, 'myps' => $my_params));
    } else {
        $redirect = $this->createMobileUrl('member', array('op' => 'myfee'));
        $this->mymsg('error', '这个订单已经支付成功, 不需要重复支付', $redirect);
    }
    header('Location:' . $payurl);
    exit(0);
}
<?php

if (!(bool) defined('IN_IA')) {
    exit('Access Denied');
}
function baidu_to_gcj02($lng, $lat)
{
    $url = 'https://apis.map.qq.com/ws/coord/v1/translate?locations=' . $lat . ',' . $lng . '&type=3&key=ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, 1);
    return $result;
}
function yyl_print($partner, $machine_code, $content, $apiKey, $msign, $url)
{
    date_default_timezone_set('Asia/Shanghai');
    $params = array('partner' => $partner, 'machine_code' => $machine_code, 'time' => time());
    ksort($params);
    $stringToBeSigned = $apiKey;
    foreach ($params as $k => $v) {
        $stringToBeSigned .= urldecode($k . $v);
    }
    $stringToBeSigned .= $msign;
    $params['sign'] = strtoupper(md5($stringToBeSigned));
    $params['content'] = $content;
    foreach ($params as $key => $value) {
        $str = $str . $key . '=' . $value . '&';
    }
    $data = rtrim($str, '&');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($curl);
    if (curl_errno($curl)) {
        return 'Errno' . curl_error($curl);
    }
    curl_close($curl);
    return json_decode($tmpInfo, true);
}
function feie_print($user, $sn, $content, $ukey, $pkey, $url)
{
    date_default_timezone_set('Asia/Shanghai');
    include_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/feie/HttpClient.class.php';
    $stime = time();
    $printcontent = array('user' => $user, 'stime' => $stime, 'sig' => sha1($user . $ukey . $stime), 'apiname' => 'Open_printMsg', 'sn' => $sn, 'content' => $content, 'times' => 1);
    $url;
    $client = 'HttpClient';
    if (!$client->post('/Api/Open/', $printcontent)) {
        return array('ret' => 0 - 9, 'msg' => 'error');
    }
    return json_decode($client->getContent(), true);
}
function wmj_httpPost($url, $str)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOP_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $str);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($str)));
    $res = curl_exec($curl);
    curl_close($curl);
    $res = trim($res, '﻿');
    $res = json_decode($res, 1);
    return $res;
}
function Zr_httpPost($url, $data = array())
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $data = curl_exec($curl);
    $res = json_decode($data, 1);
    curl_close($curl);
    return $res;
}
function Park_httpPost($op, $data = array())
{
    header('Content-Type:text/html;Charset=utf-8');
    if ($op == 1) {
        $url = 'http://api.parklinesms.com/service.php';
    } else {
        $url = 'http://api.parklinesms.com/equipment.php';
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $rs = curl_exec($curl);
    $rs = json_decode($rs, 1);
    $rs = json_decode($rs, 1);
    curl_close($curl);
    if ($op == 1) {
        if ($rs['code'] == '0') {
            $res['code'] = '0';
            $res['msg'] = '开门成功!';
        } else {
            $res['code'] = '1';
            $res['msg'] = '开门失败!' . $rs['msg'];
        }
    } elseif (array_key_exists('code', $rs)) {
        $res['code'] = 1;
        $res['msg'] = '离线';
    } elseif ($rs['status'] == '在线') {
        $res['code'] = 0;
        $res['msg'] = '在线';
    } else {
        $res['code'] = 1;
        $res['msg'] = '离线';
    }
    return $res;
}
function Mx_httpPost($url, $data = array())
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($curl);
    $res = json_decode($res, 1);
    curl_close($curl);
    return $res;
}
function my_curlOpen($url, $config = array())
{
    $arr = array('post' => false, 'referer' => $url, 'cookie' => '', 'useragent' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; customie8)', 'timeout' => 20, 'return' => true, 'proxy' => '', 'userpwd' => '', 'nobody' => false, 'header' => array(), 'gzip' => true, 'ssl' => false, 'isupfile' => false);
    $arr = array_merge($arr, $config);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $arr['return']);
    curl_setopt($ch, CURLOPT_NOBODY, $arr['nobody']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $arr['useragent']);
    curl_setopt($ch, CURLOPT_REFERER, $arr['referer']);
    curl_setopt($ch, CURLOPT_TIMEOUT, $arr['timeout']);
    if ($arr['gzip']) {
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    }
    if ($arr['ssl']) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (!empty($arr['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $arr['cookie']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $arr['cookie']);
    }
    if (!empty($arr['proxy'])) {
        curl_setopt($ch, CURLOPT_PROXY, $arr['proxy']);
        if (!empty($arr['userpwd'])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $arr['userpwd']);
        }
    }
    if (!empty($arr['header']['ip'])) {
        array_push($arr['header'], 'X-FORWARDED-FOR:' . $arr['header']['ip'], 'CLIENT-IP:' . $arr['header']['ip']);
    }
    $arr['header'] = array_filter($arr['header']);
    if (!empty($arr['header'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arr['header']);
    }
    if ($arr['post'] != false) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($arr['post']) && $arr['isupfile'] === false) {
            $post = http_build_query($arr['post']);
        } else {
            $post = $arr['post'];
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function my_bankPay($params, $wechat, $banktype)
{
    if ($banktype == 3) {
        return my_JqPay($params, $wechat);
    }
    if (!($banktype == 4)) {
        return error(0 - 1, '访问银行支付失败');
    }
    $res = my_RsdPay($params, $wechat);
    if ($res['status'] == 500) {
        return error(0 - 1, $res['msg']);
    }
    return $res;
}
function my_bankPayCard($params, $wechat, $banktype)
{
    if ($banktype == 3) {
        $res = my_JqPayCard($params, $wechat);
        if (is_error($res)) {
            return $res;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $res['url']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($data, true);
        if (empty($res['orderno'])) {
            return error(0 - 1, '支付错误:orderno为空');
        }
        return $res;
    }
    if (!($banktype == 4)) {
        return error(0 - 1, '访问银行支付失败');
    }
    $res = my_RsdPayCard($params, $wechat);
    if ($res['status'] == 500) {
        return error(0 - 1, $res['msg']);
    }
    return $res;
}
function repeat_bank_order_query($params, $wechat, $banktype)
{
    if ($banktype == 3) {
        $res = my_JqqueryOrder($params, $wechat);
        if (is_error($res)) {
            return $res;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $res['url']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($data, true);
        if ($res['status'] == 1) {
            return my_JqqueryOrder($params, $wechat);
        }
        return error(0 - 1, '支付失败');
    }
    if (!($banktype == 4)) {
        return error(0 - 1, '访问银行支付失败');
    }
    $res = my_RsdqueryOrder($params, $wechat);
    if ($res['status'] == 0) {
        return $res;
    }
    return repeat_bank_order_query($params, $wechat, $banktype);
}
function my_JqPay($params, $wechat)
{
    $host = $wechat['ymfurl'];
    $ret = stristr($host, 'jueqi_ymf');
    $uid = 'rhinfo_zyxq';
    $url = $host . '/index.php?s=/Home/line/getPrikey';
    $url = $url . '/uid/' . $uid;
    $prikeyResult = json_decode(file_get_contents($url), true);
    $prikey = '';
    if (!($prikeyResult['result'] == '1')) {
        return error(0 - 1, '访问一码付失败');
    }
    $prikey = $prikeyResult['data'];
    $selfOrdernum = $params['tid'];
    $openId = $wechat['openid'];
    $customerId = $wechat['bankmerchid'];
    $money = $params['fee'];
    $goodsName = $params['title'];
    $remark = $params['user'];
    $notifyUrl = base64_encode(urlencode($wechat['notifyurl'] . '&uniontid=' . $params['uniontid']));
    $successUrl = base64_encode(urlencode($wechat['successurl']));
    if (!empty($ret)) {
        $url = $host . '/index.php?';
        $url = $url . 'selfOrdernum=' . $selfOrdernum . '&openId=' . $openId . '&customerId=' . $customerId . '&money=' . $money . '&notifyUrl=' . $notifyUrl . '&successUrl=' . $successUrl . '&uid=' . $uid . '&prikey=' . $prikey . '&goodsName=' . $goodsName . '&remark=' . $remark;
    } else {
        $url = $host . '/index.php?s=/Home/line/m_pay';
        $url = $url . '/selfOrdernum/' . $selfOrdernum . '/openId/' . $openId . '/customerId/' . $customerId . '/money/' . $money . '/notifyUrl/' . $notifyUrl . '/successUrl/' . $successUrl . '/uid/' . $uid . '/prikey/' . $prikey . '/goodsName/' . $goodsName . '/remark/' . $remark;
    }
    return array('url' => $url);
}
function my_JqPayCard($params, $wechat)
{
    $host = $wechat['ymfurl'];
    $ret = stristr($host, 'jueqi_ymf');
    $uid = 'rhinfo_zyxq';
    $url = $host . '/index.php?s=/Home/line/getPrikey';
    $url = $url . '/uid/' . $uid;
    $prikeyResult = json_decode(file_get_contents($url), true);
    $prikey = '';
    if (!($prikeyResult['result'] == '1')) {
        return error(0 - 1, '访问一码付失败');
    }
    $prikey = $prikeyResult['data'];
    $selfOrdernum = $params['tid'];
    $customerid = $wechat['bankmerchid'];
    $userid = $wechat['bankmerchid'];
    $ordermoney = $params['fee'];
    $truemoney = $params['fee'];
    $longcode = $params['auth_code'];
    $goodsName = $params['title'];
    $remark = $params['user'];
    if (!empty($ret)) {
        $url = $host . '/index.php?';
        $url = $url . 'selfOrdernum=' . $selfOrdernum . '&userid=' . $userid . '&customerid=' . $customerid . '&ordermoney=' . $ordermoney . '&truemoney=' . $truemoney . '&longcode=' . $longcode . '&uid=' . $uid . '&prikey=' . $prikey . '&goodsName=' . $goodsName . '&datafrom=' . $uid;
    } else {
        $url = $host . '/index.php?s=/Home/line/m_shoukuan';
        $url = $url . '/selfOrdernum/' . $selfOrdernum . '/userid/' . $userid . '/customerid/' . $customerid . '/ordermoney/' . $ordermoney . '/truemoney/' . $truemoney . '/longcode/' . $longcode . '/uid/' . $uid . '/prikey/' . $prikey . '/goodsName/' . $goodsName . '/datafrom/' . $uid;
    }
    return array('url' => $url);
}
function my_JqqueryOrder($params, $wechat)
{
    $host = $wechat['ymfurl'];
    $ret = stristr($host, 'jueqi_ymf');
    $uid = 'rhinfo_zyxq';
    $url = $host . '/index.php?s=/Home/line/getPrikey';
    $url = $url . '/uid/' . $uid;
    $prikeyResult = json_decode(file_get_contents($url), true);
    $prikey = '';
    if (!($prikeyResult['result'] == '1')) {
        return error(0 - 1, '访问一码付失败');
    }
    $orderno = $params['orderno'];
    if (!empty($ret)) {
        $url = $host . '/index.php?';
        $url = $url . 'orderno=' . $orderno . '&uid=' . $uid;
    } else {
        $url = $host . '/index.php?s=/Home/line/m_querypay';
        $url = $url . '/orderno/' . $orderno . '/uid/' . $uid;
    }
    return array('url' => $url);
}
function my_RsdPay($params, $wechat)
{
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/Utils.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/RequestHandler.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/ClientResponseHandler.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/PayHttpClient.class.php';
    $resHandler = null;
    $reqHandler = null;
    $pay = null;
    $resHandler = new ClientResponseHandler();
    $reqHandler = new RequestHandler();
    $pay = new PayHttpClient();
    $reqHandler->setGateUrl($wechat['ymfurl']);
    $reqHandler->setKey(str_replace(base64_encode('rhinfo'), '', $wechat['bankkey']));
    $param['sub_appid'] = $wechat['appid'];
    $param['out_trade_no'] = $params['tid'];
    $param['sub_openid'] = $wechat['openid'];
    $param['body'] = $params['title'];
    $param['attach'] = $params['body'];
    $param['total_fee'] = $params['fee'] * 100;
    $param['mch_create_ip'] = $params['clientip'];
    $reqHandler->setReqParams($param, array('method'));
    $reqHandler->setParameter('service', 'pay.weixin.jspay');
    $reqHandler->setParameter('mch_id', $wechat['bankmerchid']);
    $reqHandler->setParameter('version', '2.0');
    $reqHandler->setParameter('limit_credit_pay', '1');
    $reqHandler->setParameter('op_user_id', 'yida');
    $reqHandler->setParameter('op_shop_id', 'yida1');
    $reqHandler->setParameter('op_device_id', 'yida2');
    $reqHandler->setParameter('device_info', 'yida3');
    $reqHandler->setParameter('is_raw', '1');
    $reqHandler->setParameter('notify_url', $wechat['notifyurl'] . '&uniontid=' . $params['uniontid']);
    $reqHandler->setParameter('callback_url', $wechat['successurl']);
    $reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));
    $reqHandler->createSign();
    $data = Utils::toXml($reqHandler->getAllParameters());
    $pay->setReqContent($reqHandler->getGateURL(), $data);
    if (!$pay->call()) {
        return array('status' => 500, 'msg' => 'Response Code:' . $pay->getResponseCode() . ' Error Info:' . $pay->getErrInfo());
    }
    $resHandler->setContent($pay->getResContent());
    $resHandler->setKey($reqHandler->getKey());
    if (!$resHandler->isTenpaySign()) {
        return array('status' => 500, 'msg' => 'Error Code:' . $resHandler->getParameter('status') . ' Error Message:' . $resHandler->getParameter('message'));
    }
    if ($resHandler->getParameter('status') == 0 && $resHandler->getParameter('result_code') == 0) {
        return array('status' => 0, 'msg' => 'success', 'token_id' => $resHandler->getParameter('token_id'), 'pay_info' => $resHandler->getParameter('pay_info'));
    }
    if ($resHandler->getParameter('status') == 0) {
        return array('status' => 500, 'msg' => '金额必须大于1元');
    }
    return array('status' => 500, 'msg' => 'Error Code:' . $resHandler->getParameter('status') . ' Error Message:' . $resHandler->getParameter('message'));
}
function my_RsdPayCard($params, $wechat)
{
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/Utils.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/RequestHandler.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/ClientResponseHandler.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/PayHttpClient.class.php';
    $resHandler = null;
    $reqHandler = null;
    $pay = null;
    $resHandler = new ClientResponseHandler();
    $reqHandler = new RequestHandler();
    $pay = new PayHttpClient();
    $reqHandler->setGateUrl($wechat['ymfurl']);
    $reqHandler->setKey(str_replace(base64_encode('rhinfo'), '', $wechat['bankkey']));
    $param['auth_code'] = $params['auth_code'];
    $param['out_trade_no'] = $params['tid'];
    $param['body'] = $params['title'];
    $param['attach'] = $params['body'];
    $param['total_fee'] = $params['fee'] * 100;
    $param['mch_create_ip'] = $params['clientip'];
    $reqHandler->setReqParams($param, array('method'));
    if (substr($params['auth_code'], 0, 2) == '28') {
        $reqHandler->setParameter('service', 'pay.alipay.micropayv3');
    } else {
        $reqHandler->setParameter('service', 'unified.trade.micropay');
    }
    $reqHandler->setParameter('mch_id', $wechat['bankmerchid']);
    $reqHandler->setParameter('version', '2.0');
    $reqHandler->setParameter('device_info', 'SPAY_AND');
    $reqHandler->setParameter('op_user_id', $wechat['bankmerchid']);
    $reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));
    $reqHandler->createSign();
    $data = Utils::toXml($reqHandler->getAllParameters());
    $pay->setReqContent($reqHandler->getGateURL(), $data);
    if (!$pay->call()) {
        return array('status' => 500, 'msg' => 'Response Code:' . $pay->getResponseCode() . ' Error Info:' . $pay->getErrInfo());
    }
    $resHandler->setContent($pay->getResContent());
    $resHandler->setKey($reqHandler->getKey());
    if (!$resHandler->isTenpaySign()) {
        return array('status' => 500, 'msg' => 'Error Code:' . $resHandler->getParameter('status') . ' Error Message:' . $resHandler->getParameter('message'));
    }
    if ($resHandler->getParameter('status') == 0 && $resHandler->getParameter('result_code') == 0) {
        return array('status' => 0, 'msg' => 'Success', 'time_end' => $resHandler->getParameter('time_end'), 'transaction_id' => $resHandler->getParameter('transaction_id'));
    }
    if ($resHandler->getParameter('err_code') == 'Auth code invalid' || $resHandler->getParameter('err_code') == 'Auth valid fail') {
        return array('status' => 500, 'msg' => 'Error Code:' . $resHandler->getParameter('err_code') . ' Error Message:' . $resHandler->getParameter('err_msg'));
    }
    return array('status' => 200, 'msg' => 'Error Code:' . $resHandler->getParameter('err_code') . ' Error Message:' . $resHandler->getParameter('err_msg'));
}
function my_RsdqueryOrder($params, $wechat)
{
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/Utils.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/RequestHandler.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/ClientResponseHandler.class.php';
    require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/RsdPay/PayHttpClient.class.php';
    $resHandler = null;
    $reqHandler = null;
    $pay = null;
    $resHandler = new ClientResponseHandler();
    $reqHandler = new RequestHandler();
    $pay = new PayHttpClient();
    $reqHandler->setGateUrl($wechat['ymfurl']);
    $reqHandler->setKey(str_replace(base64_encode('rhinfo'), '', $wechat['bankkey']));
    $param['out_trade_no'] = $params['tid'];
    $reqHandler->setReqParams($param, array('method'));
    $reqParam = $reqHandler->getAllParameters();
    $reqHandler->setParameter('version', '2.0');
    $reqHandler->setParameter('service', 'unified.trade.query');
    $reqHandler->setParameter('mch_id', $wechat['bankmerchid']);
    $reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));
    $reqHandler->createSign();
    $data = Utils::toXml($reqHandler->getAllParameters());
    $pay->setReqContent($reqHandler->getGateURL(), $data);
    if (!$pay->call()) {
        return array('status' => 500, 'msg' => 'Response Code:' . $pay->getResponseCode() . ' Error Info:' . $pay->getErrInfo());
    }
    $resHandler->setContent($pay->getResContent());
    $resHandler->setKey($reqHandler->getKey());
    if (!$resHandler->isTenpaySign()) {
        return array('status' => 500, 'msg' => 'Error Code:' . $resHandler->getParameter('status') . ' Error Message:' . $resHandler->getParameter('message'));
    }
    $res = $resHandler->getAllParameters();
    Utils::dataRecodes('查询订单', $res);
    if ($resHandler->getParameter('status') == 0 && $resHandler->getParameter('result_code') == 0 && $resHandler->getParameter('trade_state') == 'SUCCESS') {
        return array('status' => 0, 'msg' => 'success');
    }
    return array('status' => 200, 'msg' => '查询结果请查看result.txt文件！', 'data' => $res);
}
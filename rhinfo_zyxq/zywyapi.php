<?php

define('IN_MOBILE', true);
require_once '../../framework/bootstrap.inc.php';
global $_W;
global $_GPC;
$token = $_GPC['token'];
$sign = $_GPC['sign'];
$weid = intval($_GPC['i']);
$op = $_GPC['op'];
if (!isset($sign)) {
    exit(json_encode(array('errno' => 1, 'message' => '签名错误sign')));
}
if (!isset($op)) {
    exit(json_encode(array('errno' => 1, 'message' => '操作类型参数缺失op')));
}
if (!isset($weid)) {
    exit(json_encode(array('errno' => 1, 'message' => 'Access Denied.')));
}
$sql = 'select siteurl,api_token,isopenapi from ' . tablename('rhinfo_zyxq_sysset') . ' where weid = :weid';
$sysset = pdo_fetch($sql, array(':weid' => $weid));
if (empty($sysset['isopenapi'])) {
    exit(json_encode(array('errno' => 1, 'message' => 'API接口关闭')));
}
$siteurl = $sysset['siteurl'];
if (empty($siteurl)) {
    exit(json_encode(array('errno' => 1, 'message' => 'url未配置')));
}
$token = $sysset['api_token'];
if (empty($token)) {
    exit(json_encode(array('errno' => 1, 'message' => 'token未生成')));
}
$post = array();
$post = $_GPC;
$package = $post;
ksort($package);
$string1 = '';
foreach ($package as $key => $v) {
    if (!empty($v)) {
        $string1 .= $key . '=' . $v . '&';
    }
}
$string1 .= 'token=' . $sysset['api_token'];
if ($sign == strtoupper(md5($string1))) {
    $post['token'] = $sysset['api_token'];
    $url = $siteurl . 'app/index.php?i=' . $weid . '&c=entry&a=site&op=' . $op . '&do=myapi&m=rhinfo_zyxq';
    load()->func('communication');
    $response = ihttp_request($url, $post);
    if (is_error($response)) {
        exit(json_encode(array('errno' => 1, 'message' => $response['message'])));
    }
    $result = $response['content'];
    exit($result);
} else {
    exit(json_encode(array('errno' => 1, 'message' => 'sign签名错误.')));
}
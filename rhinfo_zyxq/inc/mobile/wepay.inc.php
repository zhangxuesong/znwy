<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$params = @json_decode(base64_decode($_GPC['params']), true);
if (!empty($params)) {
    $this->pay($params);
} else {
    $this->mymsg('error', '支付失败', '数据错误.', '');
}
exit(0);
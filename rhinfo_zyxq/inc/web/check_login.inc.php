<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_web();
if (empty($_W['uid'])) {
    $uniacid = $_GPC['__uniacid'];
    $session_key = '__property_' . $uniacid . '_session';
    $cookie = json_decode(base64_decode($_GPC[$session_key]), true);
    if (!empty($cookie['__property_uid'])) {
        $timeout = $this->syscfg['outtime'] ? $this->syscfg['outtime'] * 60 : 300;
        $now = time();
        if ($now - $cookie['__property_time'] > $timeout) {
            $cookie['__property_time'] = time();
            $cookie['__property_uid'] = 0;
            $session = base64_encode(json_encode($cookie));
            $session_key = '__property_' . $cookie['__property_uniacid'] . '_session';
            isetcookie($session_key, $session, 0, true);
            echo '4';
        } else {
            $cookie['__property_time'] = time();
            $session = base64_encode(json_encode($cookie));
            $session_key = '__property_' . $cookie['__property_uniacid'] . '_session';
            isetcookie($session_key, $session, 0, true);
            echo '2';
        }
    } else {
        echo '3';
    }
} else {
    echo '1';
}
exit(0);
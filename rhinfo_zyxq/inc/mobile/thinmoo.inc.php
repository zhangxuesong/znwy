<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'login';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$url = 'https://www.doormaster.me/web/wxapp/api?method=';
$access_token = $_GPC['access_token'];
if ($operation == 'login') {
    $post_data = array('access_token' => $access_token, 'username' => $_GPC['username'], 'password' => $_GPC['password']);
} elseif ($operation == 'remote_control') {
    $post_data = array('access_token' => $access_token, 'client_id' => $_GPC['client_id'], 'resource' => 'door', 'operation' => 'OPEN', 'data' => $_GPC['data']);
} elseif ($operation == 'temp_pwd') {
    $post_data = array('access_token' => $access_token, 'client_id' => $_GPC['client_id'], 'resource' => 'password', 'operation' => 'POST', 'data' => $_GPC['data']);
} elseif ($operation == 'verifynum') {
    $post_data = array('access_token' => $access_token, 'phone' => $_GPC['phone'], 'type' => $_GPC['type']);
} elseif ($operation == 'forget_pwd') {
    $post_data = array('access_token' => $access_token, 'phone' => $_GPC['phone'], 'new_pwd' => $_GPC['new_pwd']);
} elseif ($operation == 'register') {
    $post_data = array('access_token' => $access_token, 'phone' => $_GPC['phone'], 'password' => $_GPC['password'], 'nickname' => $_GPC['nickname']);
} elseif ($operation == 'rid') {
    $post_data = array('access_token' => $access_token, 'client_id' => $_GPC['client_id']);
} elseif ($operation == 'getdevlist') {
    $set = array();
    $set['url'] = '/doormaster/server/employees';
    $set['token'] = $access_token;
    $set['op'] = 'GET';
    $data = "{\r\n\t\t\"app_account\":" . $_GPC['app_account'] . "\r\n\t  }";
    $res = thinmoo_http_post($set, $data);
    if ($res['ret'] == 0) {
        $ret = $res['data'][0];
        $set['url'] = '/doormaster/server/user_ble_dev_permiss';
        $set['token'] = $access_token;
        $jsonStr = "{\r\n\t\t\t\"access_token\":\"" . $set['token'] . "\",\r\n\t\t\t\"password\":\"" . $ret['account_token_pwd'] . "\",\r\n\t\t\t\"account\":" . $_GPC['app_account'] . "\r\n\t\t}";
        $result = thinmooapp_http_post($set, $jsonStr);
        if ($result['ret'] == 0) {
            $this->successResult($result['data']['dev_ekey_list']);
        } else {
            $this->successResult(array());
        }
    } else {
        $this->successResult(array());
    }
}
$url .= $operation;
load()->func('communication');
$response = ihttp_request($url, $post_data);
if (is_error($response)) {
    $this->errorResult($response['message']);
}
$result = json_decode($response['content'], 1);
if ($result['ret'] == 0) {
    if ($operation == 'login') {
        pdo_update('mc_members', array('wxapp_userno' => $_GPC['username'], 'wxapp_password' => $_GPC['password']), array('uniacid' => $_W['uniacid'], 'uid' => $_GPC['uid']));
    }
    $this->successResult($result);
} else {
    $this->errorResult($result['msg'] . $result['ret']);
}
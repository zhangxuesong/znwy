<?php

load()->model('mc');
function my_member_update($uid, $fields)
{
    global $_W;
    $fields = array_filter($fields);
    if (empty($uid) || empty($fields) || !is_array($fields)) {
        return false;
    }
    $member = pdo_get('rhinfo_zyxq_mc_members', array('weid' => $_W['uniacid'], 'uid' => $uid), array('id'));
    if (!empty($member)) {
        $res = pdo_update('rhinfo_zyxq_mc_members', $fields, array('weid' => $_W['uniacid'], 'id' => $member['id']));
    } else {
        $fields['weid'] = $_W['uniacid'];
        $fields['uid'] = $uid;
        $res = pdo_insert('rhinfo_zyxq_mc_members', $fields);
    }
    return $res;
}
function my_uid2openid($uid)
{
    global $_W;
    $member = pdo_get('rhinfo_zyxq_mc_members', array('weid' => $_W['uniacid'], 'uid' => $uid), array('openid', 'openid_wxapp', 'openid_alipay', 'openid_aliapp', 'openid_haina'));
    return $member;
}
function my_openid2uid($openid)
{
    global $_W;
    if (!empty($openid)) {
        $sql = 'select uid from ' . tablename('rhinfo_zyxq_mc_members') . ' where weid=:weid and (openid =:openid or openid_wxapp =:openid or openid_alipay =:openid or openid_aliapp =:openid or openid_haina =:openid)';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $openid));
        $uid = !empty($member['uid']) ? $member['uid'] : 0;
    } else {
        $uid = 0;
    }
    return $uid;
}
function _my_login($uid)
{
    global $_W;
    if (!_mc_login(array('uid' => $uid))) {
        return false;
    }
    isetcookie('__uniacid', $_W['uniacid'], time() + 30 * 86400);
    $cookie = array();
    $cookie['__rhinfo_uniacid'] = $_W['uniacid'];
    $cookie['__rhinfo_time'] = time();
    $cookie['__rhinfo_uid'] = $uid;
    if (!empty($_W['openid_alipay'])) {
        $cookie['__rhinfo_alipay_openid'] = $_W['openid_alipay'];
    }
    if (!empty($_W['openid_haina'])) {
        $cookie['__rhinfo_haina_openid'] = $_W['openid_haina'];
    }
    if (!empty($_W['openid_wxapp'])) {
        $cookie['__rhinfo_wxapp_openid'] = $_W['openid_wxapp'];
    }
    if (!empty($_W['openid_aliapp'])) {
        $cookie['__rhinfo_aliapp_openid'] = $_W['openid_aliapp'];
    }
    $session = base64_encode(json_encode($cookie));
    $session_key = '__rhinfo_' . $cookie['__rhinfo_uniacid'] . '_session';
    isetcookie($session_key, $session, 0, true);
    isetcookie('__session', '', 0 - 10000);
    return true;
}
function get_mobile_house($rid, $mobile, $type = 'room')
{
    global $_W;
    $house = pdo_get('rhinfo_zyxq_' . $type, array('weid' => $_W['uniacid'], 'rid' => $rid, 'mobile' => $mobile));
    if (empty($house)) {
        $house = pdo_get('rhinfo_zyxq_member', array('weid' => $_W['uniacid'], 'rid' => $rid, 'mobile' => $mobile, 'deleted' => 0));
        $house['title'] = $house['address'];
    } elseif ($type == 'room') {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
        $building = pdo_fetchcolumn($sql, array(':id' => $house['bid'], ':weid' => $_W['uniacid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
        $unit = pdo_fetchcolumn($sql, array(':id' => $house['tid'], ':weid' => $_W['uniacid']));
        $house['title'] = $building . $unit . $house['title'];
    }
    return $house;
}
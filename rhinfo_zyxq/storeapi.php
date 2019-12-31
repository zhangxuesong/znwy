<?php

define('IN_API', true);
require_once '../../framework/bootstrap.inc.php';
global $_W;
global $_GPC;
$token = $_GPC['token'];
$storeno = $_GPC['storeno'];
$fee = intval($_GPC['money']);
$mobile = $_GPC['mobile'];
$weid = $_GPC['i'];
$op = $_GPC['op'];
if (!isset($weid)) {
    $res['code'] = '1';
    $res['msg'] = '公众号参数缺失i';
    exit(json_encode($res));
}
if (!isset($token)) {
    $res['code'] = '1';
    $res['msg'] = '访问密钥缺失token';
    exit(json_encode($res));
}
if (!isset($op)) {
    $res['code'] = '1';
    $res['msg'] = '操作类型参数缺失op';
    exit(json_encode($res));
}
if ($op != 1 || $op != 2) {
    $res['code'] = '1';
    $res['msg'] = '操作类型不正确op';
    exit(json_encode($res));
}
if (!isset($mobile)) {
    $res['code'] = '1';
    $res['msg'] = '手机号码参数缺失mobile';
    exit(json_encode($res));
}
if ($op == 2) {
    if (!isset($storeno)) {
        $res['code'] = '1';
        $res['msg'] = '门店编号参数缺失storeno';
        exit(json_encode($res));
    }
    if (!isset($fee) || empty($fee) || !($fee > 0)) {
        $res['code'] = '1';
        $res['msg'] = '金额参数缺失或不正确money';
        exit(json_encode($res));
    }
}
$sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where weid = :weid and apikey = :apikey';
$business = pdo_fetch($sql, array(':weid' => $weid, ':apikey' => $token));
if (!empty($business)) {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and mobile = :mobile';
    $member = pdo_fetch($sql, array(':weid' => $weid, ':mobile' => $mobile));
    if ($op == 1) {
        if (!empty($member)) {
            $res['code'] = '0';
            $res['msg'] = $member['address'] . ':' . $member['realname'];
        } else {
            $res['code'] = '1';
            $res['msg'] = '身份验证失败，请检查手机号码';
        }
        exit(json_encode($res));
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where weid = :weid';
    $sysset = pdo_fetch($sql, array(':weid' => $weid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $weid, ':rid' => $member['rid']));
    if (!empty($member)) {
        $_W['uniacid'] = $weid;
        $mysiteurl = !empty($sysset['siteurl']) ? $sysset['siteurl'] : $_W['siteroot'];
        $crediturl = $mysiteurl . 'app/index.php?i=1&c=entry&op=credit1&do=member&m=rhinfo_zyxq';
        load()->model('mc');
        if (!empty($business['credit']) && !empty($business['cost'])) {
            if ($fee >= $business['cost']) {
                $credit = intval($fee * $business['credit'] / $business['cost']);
                $res = mc_credit_update($member['uid'], 'credit1', $credit, array(0, '消费送积分', 'rhinfo_zyxq'));
                if ($res) {
                    $sql = 'update ' . tablename('rhinfo_zycj_business') . ' set onhand = onhand - ' . $credit . ', outqty= outqty + ' . $credit . ', outtime = ' . TIMESTAMP . ' where  weid = :weid and id=:bid';
                    pdo_query($sql, array(':weid' => $weid, ':bid' => $business['id']));
                    $credit_data = array('weid' => $weid, 'bid' => $business['id'], 'io' => 2, 'credit' => $credit, 'title' => '消费送积分', 'openid' => $member['openid'], 'storeno' => $storeno, 'status' => 1, 'cuid' => 0, 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zycj_business_creditlog', $credit_data);
                    mc_notice_credit1($member['openid'], $member['uid'], $credit, '消费送积分', $url, '谢谢惠顾，点击查看详情');
                    $res['code'] = '0';
                    $res['msg'] = '成功';
                    exit(json_encode($res));
                } else {
                    $res['code'] = '1';
                    $res['msg'] = '失败';
                    exit(json_encode($res));
                }
            }
        }
        $res['code'] = '1';
        $res['msg'] = '积分不足';
        exit(json_encode($res));
    } else {
        $res['code'] = '1';
        $res['msg'] = '业主身份不正确';
        exit(json_encode($res));
    }
} else {
    $res['code'] = '1';
    $res['msg'] = 'API访问错误，请检查密钥';
    exit(json_encode($res));
}
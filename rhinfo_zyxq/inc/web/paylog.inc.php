<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '支付日志';
$mydo = 'paylog';
$tablename = 'rhinfo_zyxq_wepay_log';
$condition = ' uniacid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$rights = $this->myrights(1, 'paylog', 'list');
$feetype = array('1' => '物业账单', '2' => '车位账单', '4' => '自助设备', '5' => '智能充电', '6' => '主体短信充值', '7' => '商家积分充值', '8' => '向商家付款', '9' => '用户充值', '10' => '向驿站付款', '11' => '租赁账单', '12' => '物业续年费', '13' => '驿站短信充值', '14' => '快递短信充值', '15' => '停车缴费', '16' => '停车月卡', '17' => '快件结算', '18' => '无牌车出入', '19' => '电动车月卡', '20' => '车位租赁', '21' => '车位共享');
if ($operation == 'list') {
    $current = '日志记录';
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (uniontid LIKE \'%' . $_GPC['keyword'] . '%\' or tid LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['feetype'])) {
        $condition .= ' AND feetype = \'' . $_GPC['feetype'] . '\'';
    }
    $paydate = $_GPC['paydate'];
    if ($paydate) {
        $starttime = strtotime($paydate['start']);
        $endtime = strtotime($paydate['end'] . ' 23:59:59');
        $condition .= ' and paytime>=' . $starttime . ' and paytime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    if ($_GPC['status'] == 1) {
        $condition .= ' AND status = 1';
    } elseif ($_GPC['status'] == 2) {
        $condition .= ' AND status = 0';
    }
    if (!empty($_W['uid'])) {
        $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t\t `PLID` DESC " . $limit;
            $data = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
    } else {
        $condition .= $this->myrcondition();
        $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t\t `PLID` DESC " . $limit;
            $data = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
    }
    load()->model('mc');
    $fans = array();
    $k = 0;
    while (!($k >= count($data))) {
        $uid = empty($data[$k]['uid']) ? mc_openid2uid($data[$k]['openid']) : $data[$k]['uid'];
        $fans = mc_fansinfo($uid, 0, $mywe['weid']);
        $data[$k]['realname'] = $fans['nickname'];
        $data[$k]['avatar'] = $fans['avatar'];
        $data[$k]['writeoff'] = 0;
        $data[$k]['notify'] = 0;
        $data[$k]['billids'] = '';
        if ($data[$k]['status'] == 1) {
            $paylog = pdo_get('rhinfo_zyxq_paylog', array('weid' => $mywe['weid'], 'tid' => $data[$k]['uniontid']));
            if (!empty($paylog)) {
                $data[$k]['feetype'] = empty($data[$k]['feetype']) ? $paylog['feetype'] : $data[$k]['feetype'];
                $data[$k]['notify'] = 1;
            }
        }
        $tag = unserialize($data[$k]['tag']);
        if ($data[$k]['feetype'] == 1) {
            $data[$k]['billids'] = $tag['billid'];
            if (!empty($tag['billid'])) {
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and id in(' . base64_decode($tag['billid']) . ') and status=1';
                $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
                if ($count > 0) {
                    $data[$k]['notify'] = 3;
                } else {
                    $data[$k]['notify'] = 2;
                }
            }
        }
        $data[$k]['realname'] = !empty($data[$k]['realname']) ? $data[$k]['realname'] : $tag['title'];
        ($k += 1) + -1;
    }
    include $this->mywtpl();
} elseif ($operation == 'delete') {
    $current = '删除日志';
    $id = intval($_GPC['id']);
    $wepaylog = pdo_get($tablename, array('uniacid' => $mywe['weid'], 'plid' => $id), array('uniontid'));
    $result = pdo_delete($tablename, array('plid' => $id, 'uniacid' => $mywe['weid']));
    if (!empty($result)) {
        pdo_delete('rhinfo_zyxq_paylog', array('weid' => $mywe['weid'], 'tid' => $wepaylog['uniontid']));
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    exit(0);
} elseif ($operation == 'delall') {
    $current = '清除日志';
    $result = pdo_delete($tablename, array('uniacid' => $mywe['weid']));
    if (!empty($result)) {
        pdo_delete('rhinfo_zyxq_paylog', array('weid' => $mywe['weid']));
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    exit(0);
} elseif ($operation == 'viewbill') {
    $current = '账单明细';
    $myret = 1;
    if (!empty($_GPC['billids'])) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and id in(' . base64_decode($_GPC['billids']) . ')';
    }
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $totalfee = 0;
    $totalpayfee = 0;
    $pid = 0;
    $rid = 0;
    $k = 0;
    while (!($k >= count($data))) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
        $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
        $data[$k]['region'] = $region;
        if ($data[$k]['category'] == 2 || $data[$k]['category'] == 4) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            $data[$k]['unit'] = '';
        } else {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $data[$k]['tid'], ':weid' => $mywe['weid']));
            $data[$k]['unit'] = $unit;
        }
        $totalfee += $data[$k]['fee'];
        $totalpayfee += $data[$k]['payfee'];
        if ($k == 0) {
            $pid = $data[$k]['pid'];
            $rid = $data[$k]['rid'];
        }
        ($k += 1) + -1;
    }
    $total = count($data);
    include $this->mywtpl();
}
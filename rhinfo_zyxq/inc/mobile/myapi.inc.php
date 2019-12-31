<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'region';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$res = $this->check_api_token($_GPC['token']);
if (!$res) {
    $this->errorResult('sign签名错误.');
}
$pindex = max(1, intval($_GPC['page']));
$psize = empty($_GPC['psize']) ? 100 : $_GPC['psize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
if ($operation == 'region') {
    if (!empty($_GPC['city'])) {
        $condition .= ' and city = :city';
        $params[':city'] = $_GPC['city'];
    }
    if (!empty($_GPC['district'])) {
        $condition .= ' and district = :district';
        $params[':district'] = $_GPC['district'];
    }
    if (!empty($_GPC['title'])) {
        $condition .= ' and title like "%' . $_GPC['title'] . '%"';
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as rid,title from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'building') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_building') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as bid,title,rid from ' . tablename('rhinfo_zyxq_building') . ' where ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'unit') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    if (!empty($_GPC['bid'])) {
        $condition .= ' and bid = :bid';
        $params[':bid'] = $_GPC['bid'];
    } else {
        $this->errorResult('楼宇参数bid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_unit') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as tid,title,rid,bid from ' . tablename('rhinfo_zyxq_unit') . ' where ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'room') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    if (!empty($_GPC['bid'])) {
        $condition .= ' and bid = :bid';
        $params[':bid'] = $_GPC['bid'];
    } else {
        $this->errorResult('楼宇参数bid不为能空');
    }
    if (!empty($_GPC['tid'])) {
        $condition .= ' and tid = :tid';
        $params[':tid'] = $_GPC['tid'];
    } else {
        $this->errorResult('单元参数tid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as hid,title,rid,bid,tid from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'slocation') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_location') . ' where category = 1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as lid,title,rid from ' . tablename('rhinfo_zyxq_location') . ' where category = 1 and ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'plocation') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_location') . ' where category = 2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as lid,title,rid from ' . tablename('rhinfo_zyxq_location') . ' where category = 2 and ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'shop') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('区域参数lid不为能空');
    }
    if (!empty($_GPC['lid'])) {
        $condition .= ' and lid = :lid';
        $params[':lid'] = $_GPC['lid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as sid,title,rid,lid from ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'parking') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('区域参数lid不为能空');
    }
    if (!empty($_GPC['lid'])) {
        $condition .= ' and lid = :lid';
        $params[':lid'] = $_GPC['lid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as pid,title,rid,lid from ' . tablename('rhinfo_zyxq_parking') . ' where ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'feebill') {
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid = :rid';
        $params[':rid'] = $_GPC['rid'];
    } else {
        $this->errorResult('小区参数rid不为能空');
    }
    if (!empty($_GPC['cate'])) {
        $condition .= ' and category = :category';
        $params[':category'] = $_GPC['cate'];
    } else {
        $this->errorResult('账单类别cate不为能空');
    }
    if (!empty($_GPC['bid'])) {
        $condition .= ' and bid = :bid';
        $params[':bid'] = $_GPC['bid'];
    } else {
        $this->errorResult('楼宇参数bid不为能空');
    }
    if (!empty($_GPC['tid'])) {
        $condition .= ' and tid = :tid';
        $params[':tid'] = $_GPC['tid'];
    } else {
        $this->errorResult('单元参数tid不为能空');
    }
    if (!empty($_GPC['hid'])) {
        $condition .= ' and hid = :hid';
        $params[':hid'] = $_GPC['hid'];
    } else {
        $this->errorResult('房屋参数hid不为能空');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id as billid,title,daterange from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition . $limit;
    $data = pdo_fetchall($sql, $params);
    $result = array('list' => $data, 'total' => $total, 'pagesize' => $psize);
    $this->successResult($result);
} elseif ($operation == 'paybill') {
    $feebillids = explode(',', $_GPC['billids']);
    $creditfee = $_GPC['creditfee'];
    $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Property';
    $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
    $payno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
    $payno = createnum(substr($payno, strlen($paynopre), 14));
    $payno = $paynopre . $payno;
    $data = array();
    $data['weid'] = $_W['uniacid'];
    $data['uid'] = $_GPC['uid'];
    $data['openid'] = $_GPC['openid'];
    $data['title'] = $_GPC['title'];
    $data['paytype'] = $_GPC['paytype'];
    $data['billid'] = $feebillids;
    $data['ctime'] = TIMESTAMP;
    $data['tid'] = $payno . random(8, 1);
    $data['payno'] = empty($_GPC['ordersn']) ? $data['tid'] : $_GPC['ordersn'];
    $data['pid'] = '';
    $data['rid'] = $_GPC['rid'];
    $data['paytime'] = TIMESTAMP;
    $data['status'] = 1;
    $feebill_data = array();
    $feebill_data = array('status' => 2, 'paytype' => $_GPC['paytype'], 'paydate' => TIMESTAMP);
    $i = 0;
    $payfee = 0;
    foreach ($feebillids as $value) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where id = :id and status=1 and rid=:rid and weid = :weid';
        $item = pdo_fetch($sql, array(':id' => $value, ':rid' => $_GPC['rid'], ':weid' => $_W['uniacid']));
        if (!empty($item)) {
            $feebill_data = array();
            $feebill_data = array('status' => 2, 'paytype' => $_GPC['paytype'], 'paydate' => TIMESTAMP);
            if ($creditfee > 0) {
                if ($creditfee > $item['fee']) {
                    $feebill_data['payfee'] = 0;
                    $feebill_data['creditfee'] = $creditfee;
                    $creditfee = 0;
                } else {
                    $feebill_data['payfee'] = $item['fee'] - $creditfee;
                    $feebill_data['creditfee'] = $creditfee;
                    $creditfee -= $item['fee'];
                }
            } else {
                $feebill_data['payfee'] = $item['fee'];
            }
            $payfee += $feebill_data['payfee'];
            $res = pdo_update('rhinfo_zyxq_feebill', $feebill_data, array('id' => $value, 'rid' => $_GPC['rid'], 'weid' => $_W['uniacid']));
            if ($res) {
                pdo_insert('rhinfo_zyxq_paylog', $data);
                ($i += 1) + -1;
            }
        }
    }
    if ($i > 0) {
        $result = array('payfee' => $payfee, 'total' => $i);
        $this->successResult($result);
    } else {
        $this->errorResult('账单核销失败');
    }
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$navtitle = '运营策略';
$current = '平台积分';
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$mydo = 'platform';
$rights = $this->myrights(15, $mydo, 'list');
$sql = 'select * from ' . tablename('rhinfo_zycj_platform_credit') . ' where ' . $condition;
$credit = pdo_fetch($sql, $params);
if ($_GPC['from'] == 'add' && $_W['ispost']) {
    $data = array('weid' => $_W['uniacid'], 'io' => 1, 'credit' => intval($_GPC['credit']), 'title' => $_GPC['title'], 'cuid' => $_W['uid'], 'ctime' => TIMESTAMP);
    pdo_insert('rhinfo_zycj_platform_credit_log', $data);
    $sql = 'update ' . tablename('rhinfo_zycj_platform_credit') . ' set onhand = onhand + ' . intval($_GPC['credit']) . ', inqty = inqty + ' . intval($_GPC['credit']) . ', intime = ' . TIMESTAMP . ' where ' . $condition;
    pdo_query($sql, $params);
    $this->mywebmsg('提示', '发布积分成功！', referer(), 'success');
}
if ($_GPC['from'] == 'sale' && $_W['ispost']) {
    if (!($credit['onhand'] >= intval($_GPC['credit']))) {
        $this->mywebmsg('错误', '平台积分余额不足！', referer(), 'danger');
    }
    $data = array('weid' => $_W['uniacid'], 'io' => 2, 'credit' => intval($_GPC['credit']), 'title' => empty($_GPC['title']) ? '销售积分' : $_GPC['title'], 'cuid' => $_W['uid'], 'ctime' => TIMESTAMP);
    pdo_insert('rhinfo_zycj_platform_credit_log', $data);
    if ($_GPC['payto'] == '1') {
        $sql = 'update ' . tablename('rhinfo_zycj_business') . ' set onhand = onhand + ' . intval($_GPC['credit']) . ', inqty = inqty + ' . intval($_GPC['credit']) . ', intime = ' . TIMESTAMP . ' where weid=:weid and id=:bid';
        pdo_query($sql, array(':weid' => $_W['uniacid'], ':bid' => $_GPC['business']));
        $sql = 'update ' . tablename('rhinfo_zycj_platform_credit') . ' set onhand = onhand - ' . intval($_GPC['credit']) . ', outqty = outqty + ' . intval($_GPC['credit']) . ', outtime = ' . TIMESTAMP . ' where ' . $condition;
        pdo_query($sql, $params);
        $data = array('weid' => $_W['uniacid'], 'bid' => $_GPC['business'], 'io' => 1, 'credit' => intval($_GPC['credit']), 'title' => empty($_GPC['title']) ? '购买积分' : $_GPC['title'], 'fromopenid' => 0, 'toopenid' => 0, 'cuid' => $_W['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_business_creditlog', $data);
    } else {
        $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set onhand = onhand + ' . intval($_GPC['credit']) . ', inqty = inqty + ' . intval($_GPC['credit']) . ', intime = ' . TIMESTAMP . ' where weid=:weid and id=:rid';
        pdo_query($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['region']));
        $sql = 'update ' . tablename('rhinfo_zycj_platform_credit') . ' set onhand = onhand - ' . intval($_GPC['credit']) . ', outqty = outqty + ' . intval($_GPC['credit']) . ', outtime = ' . TIMESTAMP . ' where ' . $condition;
        pdo_query($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['region']));
        $sql = 'update ' . tablename('rhinfo_zyxq_property') . ' set onhand = onhand + ' . intval($_GPC['credit']) . ', inqty = inqty + ' . intval($_GPC['credit']) . ', intime = ' . TIMESTAMP . ' where weid=:weid and id=:pid';
        pdo_query($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid']));
        $data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $_GPC['region'], 'io' => 1, 'credit' => intval($_GPC['credit']), 'title' => $_GPC['title'], 'fromopenid' => 0, 'toopenid' => 0, 'cuid' => $_W['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_region_creditlog', $data);
    }
    $this->mywebmsg('提示', '销售积分成功！', referer(), 'success');
}
if (empty($credit)) {
    pdo_insert('rhinfo_zycj_platform_credit', array('weid' => $_W['uniacid'], 'cuid' => $_W['uid'], 'ctime' => TIMESTAMP));
}
$year_total = array();
$starttime = strtotime(date('Y') . '-01-01 00:00:00');
$endtime = strtotime(date('Y') . '-12-31 23:59:59');
$sql1 = 'select sum(credit) from ' . tablename('rhinfo_zycj_platform_credit_log') . ' where weid = :weid and io=1 and ctime between :starttime and :endtime ';
$sql2 = 'select sum(credit) from ' . tablename('rhinfo_zycj_platform_credit_log') . ' where weid = :weid and io=2 and ctime between :starttime and :endtime ';
$sql3 = 'select sum(credit) from ' . tablename('rhinfo_zycj_platform_credit_log') . ' where weid = :weid and io=3 and ctime between :starttime and :endtime ';
$year_total['inqty'] = pdo_fetchcolumn($sql1, array(':weid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime));
$year_total['outqty'] = pdo_fetchcolumn($sql2, array(':weid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime));
$year_total['cash'] = pdo_fetchcolumn($sql3, array(':weid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime));
$starttime = strtotime('-1 months');
$endtime = TIMESTAMP;
$sql = 'select sum(credit) from ' . tablename('rhinfo_zycj_platform_credit_log') . ' where weid = :weid and io=2 and ctime between :starttime and :endtime ';
$month_outqty = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime));
$sql = 'select sum(onhand) from ' . tablename('rhinfo_zyxq_property') . ' where ' . $condition;
$nocash_total = pdo_fetchcolumn($sql, $params);
$sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where parentid=0 and ' . $condition;
$pcategory = pdo_fetchall($sql, $params);
$category = array();
$business = array();
$k = 0;
while (true) {
    if ($k >= count($pcategory)) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition;
        $regions = pdo_fetchall($sql, $params);
        if ($operation == 'list') {
            $condition .= ' and io = 1 ';
        } elseif ($operation == 'salelist') {
            $condition .= ' and io = 2 ';
        } elseif ($operation == 'cashlist') {
            $condition .= ' and io = 3 ';
        } elseif ($operation == 'retlist') {
            $condition .= ' and io = 4 ';
        }
        $starttime = strtotime('-3 month');
        $endtime = TIMESTAMP;
        if (!empty($_GPC['ctime'])) {
            $starttime = strtotime($_GPC['ctime']['start']);
            $endtime = strtotime($_GPC['ctime']['end'] . ' 23:59:59');
            $condition .= ' and ctime between :starttime and :endtime ';
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_platform_credit_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_platform_credit_log') . ' where ' . $condition . $limit;
        $list = pdo_fetchall($sql, $params);
        load()->model('mc');
        $fans = array();
        $k = 0;
        while (true) {
            if ($k >= count($list)) {
                $pager = pagination($total, $pindex, $psize);
                include $this->mywtpl('list');
                return null;
            }
            $list[$m]['openid'] = empty($list[$m]['openid']) ? $list[$m]['uid'] : $list[$m]['openid'];
            if (!empty($list[$m]['openid'])) {
                $fans = mc_fansinfo($list[$m]['openid'], 0, $_W['uniacid']);
                $list[$m]['avatar'] = $fans['avatar'];
                $list[$m]['nickname'] = $fans['nickname'];
            } else {
                $sql = 'SELECT username FROM ' . tablename('users') . ' where uid=:uid LIMIT 1';
                $list[$m]['username'] = pdo_fetchcolumn($sql, array(':uid' => $list[$m]['cuid']));
            }
            ($k += 1) + -1;
        }
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and parentid=:parentid';
    $categorys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':parentid' => $pcategory[$k]['id']));
    $category[$pcategory[$k]['id']] = $categorys;
    $m = 0;
    while (!($m >= count($categorys))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_business') . ' where cateid=:cateid and ' . $condition;
        $business[$categorys[$m]['id']] = pdo_fetchall($sql, array(':cateid' => $categorys[$m]['id'], ':weid' => $_W['uniacid']));
        ($m += 1) + -1;
    }
    ($k += 1) + -1;
}
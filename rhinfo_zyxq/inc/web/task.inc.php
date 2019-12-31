<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'task';
$tablename = 'rhinfo_zycj_task';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '运营策略';
if ($operation == 'list') {
    $current = '任务列表';
    $myret = 0;
    $rights = $this->myrights(15, $mydo, 'list');
    $condition1 = '';
    if (!empty($_GPC['regionname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['regionname'] . '%\'';
    }
    if (!empty($condition1)) {
        $condition1 = ' and (rid in (select id from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $condition1 . '))';
        $regioncondition .= $condition1;
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '启用';
            } else {
                $data[$k]['statustxt'] = '禁用';
            }
            $qrcode = $this->my_mobileurl($this->createMobileUrl($mydo, array('op' => 'detail', 'id' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '发布任务';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'tasktype' => $_GPC['tasktype'], 'category' => $_GPC['category'], 'thumb' => $_GPC['thumb'], 'rewardtype' => $_GPC['rewardtype'], 'redcate' => $_GPC['redcate'], 'credit1' => $_GPC['credit1'], 'credit2' => $_GPC['credit2'], 'amount' => $_GPC['amount'], 'minamount' => $_GPC['minamount'], 'maxamount' => $_GPC['maxamount'], 'persons' => $_GPC['persons'], 'from' => $_GPC['from'], 'wishing' => $_GPC['wishing'], 'uid' => $_GPC['uid'], 'openid' => $_GPC['openid'], 'remark' => $_GPC['remark'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $task_data = array('weid' => $_W['uniacid'], 'rid' => $_GPC['rid'], 'taskid' => $id, 'parentid' => 0, 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'stauts' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_task_member', $task_data);
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_article_category') . ' where weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑任务';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'category' => $_GPC['category'], 'rewardtype' => $_GPC['rewardtype'], 'redcate' => $_GPC['redcate'], 'credit1' => $_GPC['credit1'], 'credit2' => $_GPC['credit2'], 'amount' => $_GPC['amount'], 'minamount' => $_GPC['minamount'], 'maxamount' => $_GPC['maxamount'], 'persons' => $_GPC['persons'], 'from' => $_GPC['from'], 'wishing' => $_GPC['wishing'], 'uid' => $_GPC['uid'], 'openid' => $_GPC['openid'], 'remark' => $_GPC['remark'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if ($_GPC['tasktype'] == 1) {
            $glue = 'AND';
            pdo_update('rhinfo_zycj_task_member', array('uid' => $_GPC['uid'], 'openid' => $_GPC['openid']), array('taskid' => $id, 'weid' => $mywe['weid'], 'parentid' => 0), 'AND');
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除任务';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'record') {
    $current = '任务记录';
}
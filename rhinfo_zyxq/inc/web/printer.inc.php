<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '基础设置';
$mydo = 'category';
$helpcate = 'list';
$tablename = 'rhinfo_zyxq_printer';
$condition = ' weid = :weid and pid = :pid and rid = :rid ';
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$params = array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid);
$rights = $this->myrights(2, 'category', 'list');
$sql = 'select title,category from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
$region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
$rlist = 'list';
if ($region['category'] == 1) {
    $rlist = 'list';
} elseif ($region['category'] == 2) {
    $rlist = 'blist';
} elseif ($region['category'] == 3) {
    $rlist = 'glist';
} elseif ($region['category'] == 4) {
    $rlist = 'mlist';
} elseif ($region['category'] == 5) {
    $rlist = 'alist';
}
$navtitle = $region['title'] . ' > ' . $navtitle;
if ($operation == 'list') {
    $current = '打印机列表';
    $myret = 1;
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['printbrand'] == 1) {
                $data[$k]['printbrand'] = '易联云打印机';
            } elseif ($data[$k]['printbrand'] == 2) {
                $data[$k]['printbrand'] = '飞鹅打印机';
            }
            if ($data[$k]['printtype'] == 1) {
                $data[$k]['printtype'] = '业主报修';
            } elseif ($data[$k]['printtype'] == 2) {
                $data[$k]['printtype'] = '投诉建议';
            } elseif ($data[$k]['printtype'] == 3) {
                $data[$k]['printtype'] = '业主缴费';
            } else {
                $data[$k]['printtype'] = '不限';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增打印机';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'printtype' => $_GPC['printtype'], 'printbrand' => $_GPC['printbrand'], 'apikey' => $_GPC['apikey'], 'printkey' => $_GPC['printkey'], 'printno' => $_GPC['printno'], 'userid' => $_GPC['userid'], 'url' => $_GPC['url'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, 'printer', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('printer', array('op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑打印机';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('printtype' => $_GPC['printtype'], 'printbrand' => $_GPC['printbrand'], 'apikey' => $_GPC['apikey'], 'printkey' => $_GPC['printkey'], 'printno' => $_GPC['printno'], 'userid' => $_GPC['userid'], 'url' => $_GPC['url'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($_GPC['pid'], 'printer', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('printer', array('op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除打印机';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, 'printer', $operation, $current, $current . 'id=' . $id);
    exit(0);
}
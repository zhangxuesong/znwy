<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'selfwashcar';
$tablename = 'rhinfo_zycj_selfwashcar';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '运营策略';
if ($operation == 'list') {
    $current = '智能设备列表';
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
            $res = $this->mxdevstatus($data[$k]['devicesn']);
            if ($res['code'] == '0') {
                $data[$k]['isonline'] = '在线';
            } else {
                $data[$k]['isonline'] = '离线';
            }
            $qrcode = $this->my_mobileurl($this->createMobileUrl('selfdevice', array('op' => 'scan', 'id' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增智能设备';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'devtype' => $_GPC['devtype'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'address' => $_GPC['address'], 'devicesn' => $_GPC['devicesn'], 'price' => $_GPC['price'], 'content' => htmlspecialchars_decode($_GPC['content']), 'remark' => $_GPC['remark'], 'cateid' => $_GPC['cateid'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        if ($this->syscfg['isdevreg'] == 1) {
            if ($_GPC['devtype'] == 1 && $_GPC['regdevice'] == 1) {
                $res = $this->mxdevreg($_GPC['devicesn']);
                if (!($res['code'] == '0')) {
                    $this->mywebmsg('错误', '无法添加设备，请联系厂商', '', 'danger');
                }
            }
        }
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
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
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_selfdevice_cate') . ' where weid = :weid';
    $cates = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑智能设备';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'devtype' => $_GPC['devtype'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'address' => $_GPC['address'], 'devicesn' => $_GPC['devicesn'], 'price' => $_GPC['price'], 'content' => htmlspecialchars_decode($_GPC['content']), 'remark' => $_GPC['remark'], 'cateid' => $_GPC['cateid'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
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
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zycj_selfdevice_cate') . ' where weid = :weid';
    $cates = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除智能设备';
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
} elseif ($operation == 'catelist') {
    $current = '智能设备分类';
    $myret = 1;
    $rights = $this->myrights(15, $mydo, 'list');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zycj_selfdevice_cate') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfdevice_cate') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('selfdevice', array('op' => 'list', 'cateid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            ($k += 1) + -1;
        }
        $pager = pagination($ptotal, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $navtitle = '设备分类';
    $current = '新增分类';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_selfdevice_cate', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'catelist')) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'cateedit') {
    $navtitle = '分类设备';
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title']);
        pdo_update('rhinfo_zycj_selfdevice_cate', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'catelist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('catepost');
} elseif ($operation == 'catedelete') {
    $current = '删除设备分类';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_selfdevice_cate', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'record') {
    $current = '智能设备记录';
}
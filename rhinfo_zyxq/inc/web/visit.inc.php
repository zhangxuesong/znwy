<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '临时访客';
$mydo = 'visit';
$tablename = 'rhinfo_zyxq_door_visit';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '访客记录';
    $myret = 0;
    $condition1 = '';
    if (!empty($_GPC['regionname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['regionname'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition1 .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition1 .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition1 .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (!empty($condition1)) {
        $condition1 = ' and (rid in (select id from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $condition1 . '))';
        $regioncondition .= $condition1;
    }
    $opentime = $_GPC['visittime'];
    if (!empty($opentime)) {
        $starttime = strtotime($opentime['start']);
        $endtime = strtotime($opentime['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    if (!empty($_GPC['keyword'])) {
        $sql = 'select uid from ' . tablename('mc_members') . ' where realname like :keyword or nickname like :keyword or mobile like :keyword';
        $uid = pdo_fetchcolumn($sql, array(':keyword' => '%' . $_GPC['keyword'] . '%'));
        if (!empty($uid)) {
            $condition .= ' and (touid=:uid or fromuid=:uid)';
            $params[':uid'] = $uid;
        }
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetch($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $data[$k]['tid'], ':weid' => $mywe['weid']));
            $data[$k]['unit'] = $unit;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
            $room = pdo_fetchcolumn($sql, array(':id' => $data[$k]['hid'], ':weid' => $mywe['weid']));
            $data[$k]['room'] = $room;
            $data[$k]['house'] = $building . $unit . $room;
            $fans = mc_fansinfo($data[$k]['touid'], 0, $mywe['weid']);
            $data[$k]['realname'] = $fans['nickname'];
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['ctime'] = date('Y-m-d H:i:s', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'region' => '所属主体', 'title' => '门禁', 'building' => '楼宇', 'unit' => '单元', 'house' => '房屋', 'effedate' => '有效分钟', 'opentimes' => '有效次数', 'realname' => '昵称', 'reason' => '来访事由', 'ctime' => '申请日期');
            export_excel($data, $filter, '临时访客');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'delete') {
    $current = '删除访客';
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
}
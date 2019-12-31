<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'car';
$tablename = 'rhinfo_zyxq_parking';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '车位管理';
$rights = $this->myrights(7, $mydo, $operation);
if ($operation == 'list') {
    $current = '车位信息';
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' or carno LIKE \'%' . $_GPC['keyword'] . '%\' or ownername LIKE \'%' . $_GPC['keyword'] . '%\' or mobile LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['lid'])) {
        $condition .= ' AND lid = ' . $_GPC['lid'];
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where category=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where category=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title,pc_type from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region['title'];
            $data[$k]['pc_type'] = $region['pc_type'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
            $location = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
            $data[$k]['title'] = $location . '-' . $data[$k]['title'];
            if (!($data[$k]['enddate'] >= TIMESTAMP) && !empty($data[$k]['enddate'])) {
                $data[$k]['status'] = 2;
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'carlist') {
    $current = '车辆信息';
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' or carno LIKE \'%' . $_GPC['keyword'] . '%\' or ownername LIKE \'%' . $_GPC['keyword'] . '%\' or mobile LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_car') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_car') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title,pc_type from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region['title'];
            $data[$k]['pc_type'] = $region['pc_type'];
            $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and pid = :pid and rid = :rid and carno = :carno';
            $parking = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':carno' => $data[$k]['carno']));
            if (!empty($parking)) {
                $data[$k]['parking'] = $parking['title'];
            } else {
                $data[$k]['parking'] = '无租车位';
            }
            if (empty($data[$k]['startdate']) && empty($data[$k]['enddate'])) {
                if (!(!empty($parking['startdate']) && !empty($parking['enddate']))) {
                }
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_mycar') . ' where weid = :weid and carno = :carno';
            $car = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':carno' => $data[$k]['carno']));
            $data[$k]['checkdate'] = $car['checkdate'];
            $url = $this->my_mobileurl($this->createMobileUrl('car', array('op' => 'index', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($url);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('carlist');
} elseif ($operation == 'add') {
    $current = '新增车辆';
    if ($_W['ispost']) {
        $carno = strtoupper($_GPC['title']);
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $carno, 'carno' => $carno, 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_car', $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'carlist', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $myparking = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and pid = :pid and rid=:rid';
            $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $myparking[$regions[$m]['id']] = $parkings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('carpost');
} elseif ($operation == 'edit') {
    $current = '编辑车辆';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $carno = strtoupper($_GPC['title']);
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $carno, 'carno' => $carno, 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_car', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'carlist', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $myparking = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and pid = :pid and rid=:rid';
            $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $myparking[$regions[$m]['id']] = $parkings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_car') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and pid = :pid and rid=:rid';
    $eparkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mywtpl('carpost');
} elseif ($operation == 'delete') {
    $current = '删除车辆';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_car', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_car') . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => strtoupper($_GPC['title']), ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_car') . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => strtoupper($_GPC['title']), ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '车辆已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'lease') {
    $myret = 1;
    $current = '车位租赁';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
        $status = 0;
        if (strtotime($_GPC['enddate']) > TIMESTAMP) {
            $status = 1;
        }
        $data = array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'carno' => strtoupper($_GPC['carno']), 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'remark' => $_GPC['remark'], 'price' => $_GPC['price'], 'pricemethod' => $_GPC['pricemethod'], 'paymonths' => $_GPC['paymonths'], 'status' => $status);
        $glue = 'AND';
        pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if (strtotime($_GPC['startdate']) > $item['startdate'] && strtotime($_GPC['startdate']) > $item['startdate']) {
            $parking_log = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'carno' => strtoupper($_GPC['carno']), 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'remark' => $_GPC['remark'], 'cid' => $id, 'cuid' => $mywe['uid'], 'price' => $_GPC['price'], 'paymonths' => $_GPC['paymonths'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_parkinglog', $parking_log);
            $data_bill = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'cid' => $id, 'title' => $_GPC['title'], 'status' => 1, 'remark' => $_GPC['remark']);
            $data_bill['price'] = floatval($_GPC['price']);
            $data_bill['fee'] = $_GPC['pricemethod'] == 1 ? $data_bill['price'] * $_GPC['paymonths'] : $data_bill['price'];
            if ($data_bill['fee'] == 0 || empty($_GPC['startdate'])) {
                $this->mywebmsg('错误', '金额为0或起始日期不正确', '', 'danger');
            }
            $str = '+ ' . $_GPC['paymonths'] . ' months';
            $data_bill['startdate'] = strtotime($_GPC['startdate']);
            $data_bill['enddate'] = strtotime($str, $data_bill['startdate']);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and startdate=:startdate and enddate=:enddate';
            $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':cid' => $id, ':startdate' => $data_bill['startdate'], ':enddate' => $data_bill['enddate']));
            if ($total > 0) {
                $this->mywebmsg('错误', '账单已存在', '', 'danger');
            }
            if ($_GPC['paymonths'] > 1) {
                if ($region['feebillmonth'] == 1) {
                    $data_bill['daterange'] = date('Y-m-d', $data_bill['startdate']) . '~' . date('Y-m-d', $data_bill['enddate']);
                } elseif ($region['feebillmonth'] == 2) {
                    $data_bill['daterange'] = date('Y-m-d', $data_bill['startdate']) . '~' . date('Y-m-d', $data_bill['enddate']);
                } else {
                    $data_bill['daterange'] = date('Y-m', $data_bill['startdate']) . '~' . date('Y-m', $data_bill['enddate']);
                }
            } else {
                $data_bill['daterange'] = date('Y-m', $data['enddate']);
            }
            $data_bill['cuid'] = $mywe['uid'];
            $data_bill['ctime'] = TIMESTAMP;
            pdo_insert('rhinfo_zyxq_carbill', $data_bill);
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid = :weid and pid = :pid and rid = :rid and carno=:carno';
        $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':carno' => $_GPC['carno']));
        if (!($total > 0)) {
            $sql = ' select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and mobile=:mobile and category=1 and deleted=0 and status=1';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':mobile' => $_GPC['mobile']));
            $data_car = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['carno'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'carno' => $_GPC['carno'], 'remark' => $_GPC['remark'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_car', $data_car);
        }
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $location = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $location . '-' . $item['title'];
    $navtitle = $region . '：车位管理';
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and pid = :pid and rid=:rid';
    $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mywtpl('edit');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parkinglog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'leaselog') {
    $current = '租赁记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parkinglog') . ' where weid=:weid and cid=:cid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglog') . " where weid=:weid and cid=:cid ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cid' => $id));
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('leaselog');
} elseif ($operation == 'myparking') {
    $current = '车位信息';
    $myret = 0;
    $condition .= $this->myrcondition();
    if (!empty($_GPC['rid'])) {
        $condition .= ' AND rid=' . $_GPC['rid'];
    }
    if (!empty($_GPC['mobile'])) {
        $condition .= ' AND mobile=' . $_GPC['mobile'];
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC ";
        $data = pdo_fetchall($sql, $params);
    }
    if (!empty($_GPC['hid'])) {
        $sql = 'select a.* from ' . tablename($tablename) . ' as a left join ' . tablename('rhinfo_zyxq_room_parking') . ' as b on a.id = b.parkingid where a.weid=:weid and a.rid=' . $_GPC['rid'] . ' and b.hid=' . $_GPC['hid'] . " ORDER BY\r\n\t\t\t\t\t `ID` ASC ";
        $parking_data = pdo_fetchall($sql, $params);
    }
    $data = array_merge($parking_data, $data);
    $k = 0;
    while (!($k >= count($data))) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
        $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
        $data[$k]['region'] = $region;
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
        $location = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
        $data[$k]['title'] = $location . '-' . $data[$k]['title'];
        if (!($data[$k]['enddate'] >= TIMESTAMP) && !empty($data[$k]['enddate'])) {
            $data[$k]['status'] = 2;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('myparking');
} elseif ($operation == 'mylease') {
    $myret = 1;
    $current = '车位租赁';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $status = 0;
        if (strtotime($_GPC['enddate']) > TIMESTAMP) {
            $status = 1;
        }
        $data = array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'carno' => $_GPC['carno'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'remark' => $_GPC['remark'], 'status' => $status);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if (strtotime($_GPC['startdate']) > $item['startdate'] && strtotime($_GPC['startdate']) > $item['startdate']) {
            $parking_log = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'carno' => $_GPC['carno'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'remark' => $_GPC['remark'], 'cid' => $id, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_parkinglog', $parking_log);
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid = :weid and pid = :pid and rid = :rid and carno=:carno';
        $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':carno' => $_GPC['carno']));
        if (!($total > 0)) {
            $data_car = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['carno'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'carno' => $_GPC['carno'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_car', $data_car);
        }
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $location = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $location . '-' . $item['title'];
    $navtitle = $region . '：车位管理';
    include $this->mywtpl('mylease');
} elseif ($operation == 'myleaselog') {
    $current = '租赁记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parkinglog') . ' where weid=:weid and cid=:cid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglog') . " where weid=:weid and cid=:cid ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cid' => $id));
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('myleaselog');
} elseif ($operation == 'carbill') {
    $current = '车位续费';
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND carno LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
            $data[$k]['location'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('carbill');
} elseif ($operation == 'addbill') {
    $current = '车位账单';
    if ($_W['ispost']) {
        $pid = $_GPC['pid'];
        $rid = $_GPC['rid'];
        $condition = ' weid=:weid and pid=:pid and rid=:rid ';
        $params = array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid);
        $parkingstr = $_GPC['parkings'];
        $i = 0;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
        if (!empty($parkingstr)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where status = 1 and category=0 and weid=:weid and pid=:pid and rid=:rid and id in(' . $parkingstr . ')';
            $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where status = 1 and category=0 and weid=:weid and pid=:pid and rid=:rid';
            $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
        }
        $k = 0;
        while (!($k >= count($parkings))) {
            if (!(!($parkings[$k]['enddate'] >= TIMESTAMP) && !empty($parkings[$k]['enddate']))) {
                $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'lid' => $parkings[$k]['lid'], 'cid' => $parkings[$k]['id'], 'title' => $parkings[$k]['title'], 'status' => 1, 'remark' => $_GPC['remark']);
                $data['price'] = floatval($parkings[$k]['price']);
                $data['fee'] = $parkings[$k]['pricemethod'] == 1 ? $data['price'] * $parkings[$k]['paymonths'] : $data['price'];
                $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid';
                $mybilldate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':lid' => $parkings[$k]['lid'], ':cid' => $parkings[$k]['id']));
                $mybilldate = empty($mybilldate) ? $parkings[$k]['startdate'] : $mybilldate;
                if (!($data['fee'] == 0 || empty($mybilldate))) {
                    $timediff = $mybilldate - TIMESTAMP;
                    $days = intval($timediff / 86400);
                    if (!($days > $parkings[$k]['paymonths'] * 30)) {
                        $str = '+ ' . $parkings[$k]['paymonths'] . ' months';
                        $data['startdate'] = strtotime('+ 1 day', $mybilldate);
                        $data['enddate'] = strtotime($str, $mybilldate);
                        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and cid=:cid and startdate=:startdate and enddate=:enddate';
                        $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':lid' => $parkings[$k]['lid'], ':cid' => $parkings[$k]['id'], ':startdate' => $data['startdate'], ':enddate' => $data['enddate']));
                        if (!($total > 0)) {
                            if ($parkings[$k]['paymonths'] > 1) {
                                if ($region['feebillmonth'] == 1) {
                                    $data['daterange'] = date('Y-m-d', $data['startdate']) . '~' . date('Y-m-d', $data['enddate']);
                                } elseif ($region['feebillmonth'] == 2) {
                                    $data['daterange'] = date('Y-m-d', $data['startdate']) . '~' . date('Y-m-d', $data['enddate']);
                                } else {
                                    $data['daterange'] = date('Y-m', $data['startdate']) . '~' . date('Y-m', $data['enddate']);
                                }
                            } else {
                                $data['daterange'] = date('Y-m', $data['enddate']);
                            }
                            $data['cuid'] = $mywe['uid'];
                            $data['ctime'] = TIMESTAMP;
                            $result = pdo_insert('rhinfo_zyxq_carbill', $data);
                            $id = pdo_insertid();
                            ($i += 1) + -1;
                        }
                    }
                }
            }
            ($k += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, '生成' . $i . '个车位账单');
        $directurl = $this->createWeburl($mydo, array('op' => 'carbill')) . $mywe['direct'];
        $this->mywebmsg('成功', '生成' . $i . '个车位账单', $directurl, 'success');
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $myparking = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=2 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations = array(array('id' => 0, 'title' => '全部'));
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $n = 0;
            while (!($n >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and category=0';
                $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$n]['id']));
                $myparking[$locations[$n]['id']] = $parkings;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('addbill');
} elseif ($operation == 'editbill' || $operation == 'paybill') {
    $current = $operation == 'editbill' ? '编辑账单' : '车位续费';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        if ($_GPC['status'] == 2) {
            exit(0);
        }
        $data = array('fee' => $_GPC['fee'], 'remark' => $_GPC['remark']);
        if ($operation == 'paybill') {
            $data['payfee'] = $_GPC['payfee'];
            $data['status'] = 2;
            $data['paytype'] = intval($_GPC['paytype']);
            $data['paydate'] = TIMESTAMP;
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_carbill', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'carbill')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2';
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2';
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mywtpl('editbill');
} elseif ($operation == 'calpay') {
    $current = '扫码收款';
    $paytype = $this->paytype;
    if ($_W['ispost']) {
        $payfee = floatval($_GPC['payfee']);
        if (!($payfee > 0)) {
            echo '支付金额错误';
            exit(0);
        }
        $itemtitle = '';
        $printdetail = '';
        $printdetail1 = '';
        $senddetail = '';
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
        $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where id = :id and weid = :weid';
        $bill = pdo_fetch($sql, array(':id' => $_GPC['id'], ':weid' => $mywe['weid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where id = :id and weid = :weid';
        $parking = pdo_fetch($sql, array(':id' => $_GPC['cid'], ':weid' => $mywe['weid']));
        $data = array();
        $data['weid'] = $mywe['weid'];
        $data['uid'] = $mywe['uid'];
        $data['openid'] = 0;
        $data['title'] = $region['title'] . '-' . $bill['title'];
        if (!empty($_GPC['scanqrcode'])) {
            if (substr($_GPC['scanqrcode'], 0, 2) == '28') {
                $data['paytype'] = $paytype[2];
                $mypaytype = 2;
            } else {
                $data['paytype'] = $paytype[1];
                $mypaytype = 1;
            }
        } else {
            $data['paytype'] = $paytype[$_GPC['paytype']];
            $mypaytype = $_GPC['paytype'];
        }
        $data['billid'] = $_GPC['id'];
        $data['ctime'] = TIMESTAMP;
        $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
        $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
        $payno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
        $payno = createnum(substr($payno, strlen($paynopre), 14));
        $payno = $paynopre . $payno;
        $data['tid'] = $payno . random(8, 1);
        $data['payno'] = $payno;
        $data['fee'] = $payfee;
        $data['feetype'] = 2;
        $data['status'] = 0;
        $data['pid'] = $_GPC['pid'];
        $data['rid'] = $_GPC['rid'];
        pdo_insert('rhinfo_zyxq_paylog', $data);
        $logid = pdo_insertid();
        if (!empty($_GPC['scanqrcode'])) {
            $params = array();
            $params = array('title' => $data['title'], 'tid' => $data['tid'], 'fee' => $data['fee']);
            $params['uniontid'] = $params['tid'];
            $params['out_trade_no'] = $params['tid'];
            $params['out_trade_no'] = $params['tid'];
            $params['total_amount'] = $params['fee'];
            $params['subject'] = $params['title'];
            $params['body'] = $mywe['weid'] . ':2';
            $params['logid'] = $logid;
            $params['auth_code'] = $_GPC['scanqrcode'];
            $params['clientip'] = $_W['clientip'];
            $res = $this->my_scancode_pay($params, $property, $region);
            if ($res['errno'] == 1) {
                echo $res['message'];
                exit(0);
            }
        }
        $pznopre = 'CAR' . $_GPC['rid'];
        $sql = 'select max(printpznum) from' . tablename('rhinfo_zyxq_carbill') . ' where weid = :weid and rid=:rid and printpznum like \'' . $pznopre . '%\'';
        $pzno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $printpznum = createpznum(substr($pzno, strlen($pznopre), 12));
        $printpznum = $pznopre . $printpznum;
        $bill_data = array('status' => 2, 'paytype' => $mypaytype, 'payfee' => $payfee, 'printpznum' => $printpznum, 'paydate' => TIMESTAMP);
        pdo_update('rhinfo_zyxq_carbill', $bill_data, array('weid' => $mywe['weid'], 'id' => $_GPC['id']));
        $itemtitle .= $bill['title'];
        $printdetail .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . '\\n';
        $printdetail1 .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . '<BR>';
        $senddetail .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . ',';
        $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and rid=:rid and id=:id';
        $mindate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':id' => $_GPC['id']));
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_carbill') . ' where weid=:weid and rid=:rid and id=:id';
        $maxdate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':id' => $_GPC['id']));
        if ($mindate == $maxdate) {
            $daterange = date('Y-m', $maxdate);
            $senddetail = '';
        } else {
            $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
        }
        pdo_update('rhinfo_zyxq_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('weid' => $mywe['weid'], 'id' => $logid));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $_GPC['id']);
        $paymethod = '支付方式:' . $paytype[intval($_GPC['paytype'])] . '\\n';
        $paymethod1 = '支付方式:' . $paytype[intval($_GPC['paytype'])] . '<BR>';
        $content = '<FB><center>缴费通知单</center></FB>\\n';
        $content .= '房产:' . $region['title'] . $bill['title'] . '\\n';
        $content .= '缴费类别:' . $itemtitle . '\\n';
        $content .= '缴费金额:' . $data['fee'] . '元\\n';
        $content .= '缴费周期:' . $daterange . '\\n';
        $content .= '缴费时间:' . date('Y-m-d h:m') . '\\n';
        $content .= '缴费人:' . $parking['ownername'] . '\\n';
        $content1 = '<CB>缴费通知单</CB><BR>';
        $content1 .= '房产:' . $region['title'] . $bill['title'] . '<BR>';
        $content1 .= '缴费类别:' . $itemtitle . '<BR>';
        $content1 .= '缴费金额:' . $data['fee'] . '元<BR>';
        $content1 .= '缴费周期:' . $daterange . '<BR>';
        $content1 .= '缴费时间:' . date('Y-m-d h:m') . '<BR>';
        $content1 .= '缴费人:' . $parking['ownername'] . '<BR>';
        if ($region['isprintfeedetail'] == 1) {
            $content .= $printdetail;
            $content1 .= $printdetail1;
        }
        $content .= $paymethod . '<right>' . $property['title'] . '</right>';
        $content1 .= $paymethod . '<RIGHT>' . $property['title'] . '</RIGHT>';
        $res = $this->send_print($bill['pid'], $bill['rid'], 3, urlencode($content), $content1);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_car') . ' where weid=:weid and rid=:rid and mobile=:mobile and deleted=0 ';
        $car = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':mobile' => $parking['mobile']));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的缴费我们已收到', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $region['title'] . $bill['title'] . ',' . $senddetail . '缴费总额' . $data['fee'] . '元，缴费周期' . $daterange, 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持！'));
        $url = $this->createMobileurl('member', array('op' => 'myparking'));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid1'])) {
            $this->send_mysendtplnotice($car['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and rid=:rid and lid=:lid';
        $parklot = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $bill['rid'], ':lid' => $bill['lid']));
        if (!empty($parklot['pc_plotid']) && !empty($parking['carno']) && $bill['enddate'] > TIMESTAMP) {
            $data_parkpay = array('weid' => $mywe['weid'], 'pid' => $bill['pid'], 'rid' => $bill['rid'], 'parklotid' => $parklot['id'], 'title' => '办理月卡', 'category' => 2, 'carno' => $parking['carno'], 'fee' => $payfee, 'mobile' => $parking['mobile'], 'realname' => $parking['ownername'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $parklot['pc_type'] = !empty($parklot['pc_type']) ? $parklot['pc_type'] : $region['pc_type'];
            if ($parklot['pc_type'] == 1) {
                $set = array();
                $set['userId'] = $region['pc_appid'];
                $set['userKey'] = $region['pc_secret'];
                $params = array();
                $params['searchParamsItems'] = array(array('name' => 'rows', 'value' => 1), array('name' => 'page', 'value' => 1), array('name' => 'plgId', 'value' => $parklot['pc_plotid']), array('name' => 'licensePlate', 'value' => $parking['carno']));
                $res = icar_post_querycarid($set, $params);
                if ($res['successCode'] == '100') {
                    $rows = $res['rows'];
                    if (empty($rows)) {
                        $params = array();
                        $params['updateCreateParamItems'] = array(array('name' => 'plgId', 'value' => $parklot['pc_plotid']), array('name' => 'name', 'value' => $parking['carno']), array('name' => 'licensePlateType', 'value' => 0), array('name' => 'licensePlate', 'value' => $parking['carno']), array('name' => 'expireTime', 'value' => date('Y-m-d H:i:s', $bill['enddate'])));
                        $res = icar_post_createcarid($set, $params);
                    } else {
                        $params = array();
                        $params['updateCreateParamItems'] = array(array('name' => 'id', 'value' => $rows['id']), array('name' => 'name', 'value' => $parking['carno']), array('name' => 'expireTime', 'value' => date('Y-m-d H:i:s', $bill['enddate'])));
                        $res = icar_post_editcarid($set, $params);
                    }
                }
            } elseif ($parklot['pc_type'] == 2) {
                $set = array();
                $set['pc_appid'] = $region['pc_appid'];
                $set['pc_secret'] = $region['pc_secret'];
                $set['url'] = 'api/queryCreatMonthCardData.action';
                $params = array('parkId' => $parklot['pc_plotid'], 'type' => 1, 'carNum' => $parking['carno']);
                $res = ipms_http_post($set, $params);
                if ($res['result'] == 'success') {
                    $res = json_decode(urldecode($res['data']), true);
                    if (empty($res)) {
                        $set['url'] = 'third_api/monthCardAppAction_create.action';
                        $params = array('parkId' => $parklot['pc_plotid'], 'carNum' => $payfee['carno'], 'startTime' => date('Y-m-d'), 'endTime' => date('Y-m-d', $bill['enddate']), 'userPhone' => $parking['mobile'], 'userName' => $parking['ownername'], 'loginUserName' => $parking['mobile'], 'rechangeFee' => $payfee * 100);
                        $res = ipms_httpsign_post($set, $params);
                        $data_parkpay['starttime'] = TIMESTAMP;
                        $data_parkpay['endtime'] = $bill['enddate'];
                    } else {
                        $res = $res['datas'][0];
                        if (!empty($res)) {
                            $cardno = $res['id'];
                            $startdate = date('Y-m-d', strtotime($res['tpStartTime']));
                            $set['url'] = 'third_api/monthCardAppAction_updateMonthCard.action';
                            $params = array('id' => $cardno, 'parkId' => $parklot['pc_plotid'], 'carNum' => $parking['carno'], 'startTime' => $startdate, 'endTime' => date('Y-m-d', $bill['enddate']), 'loginUserName' => $parking['mobile'], 'rechangeFee' => $payfee * 100);
                            $res = ipms_httpsign_post($set, $params);
                            $data_parkpay['starttime'] = strtotime($startdate);
                            $data_parkpay['endtime'] = $bill['enddate'];
                        }
                    }
                }
            } elseif ($parklot['pc_type'] == 3) {
                $set = array('url' => 'app/getLaneList.aspx', 'parkno' => $parklot['pc_plotid'], 'secret' => $parklot['pc_secret']);
                $params = array();
                $res = etpcar_http_post($set, $params);
                $resmsg = $res['ReMsg'];
                if ($resmsg['ErrNo'] == '0000') {
                    $lanelist = $res['LaneList'];
                    $parkios = $lanelist['LaneInfo'];
                    $machlist = '';
                    foreach ($parkios as $vio) {
                        $machlist .= $vio['MachNo'] . '_';
                    }
                    $machlist = substr($machlist, 0, 0 - 1);
                    $set['url'] = 'app/UpMonCar.aspx';
                    $params = array('CarNo' => $parking['carno'], 'UserName' => $parking['ownername'], 'OperType' => 'Add', 'Fee' => 100, 'StartTime' => date('YmdHis', strtotime(date('Y-m-d', $bill['startdate']))), 'EndTime' => date('YmdHis', strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59')), 'CarType' => '月卡A', 'CardStatus' => 0, 'Balance' => 0, 'MachList' => $machlist, 'SetNo' => $parking['title']);
                    $res = etpcar_http_post($set, $params);
                    $res = $res['ReMsg'];
                    if ($res['ErrNo'] == '0000') {
                        $data_parkpay['starttime'] = strtotime($bill['startdate']);
                        $data_parkpay['endtime'] = strtotime($bill['enddate'] . ' 23:59:59');
                    } else {
                        $params = array('CarNo' => $parking['carno'], 'UserName' => $parking['ownername'], 'OperType' => 'Delay', 'Fee' => 100, 'StartTime' => date('YmdHis', strtotime(date('Y-m-d', $bill['startdate']))), 'EndTime' => date('YmdHis', strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59')), 'CarType' => '月卡A', 'CardStatus' => 0, 'Balance' => 0, 'MachList' => $machlist, 'SetNo' => $parking['title']);
                        $res = etpcar_http_post($set, $params);
                        $res = $res['ReMsg'];
                        if ($res['ErrNo'] == '0000') {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate'] . ' 23:59:59');
                        }
                    }
                }
            } elseif ($parklot['pc_type'] == 4) {
                $set = array();
                $set['pc_secret'] = $region['pc_secret'];
                $set['url'] = 'BCStandard/open/pubApi/synchRegisterUser';
                $params = array('parkId' => $parklot['pc_plotid'], 'type' => 1, 'carNum' => $parking['carno']);
                $res = bluecar_http_post($set, $params);
            } elseif ($parklot['pc_type'] == 5) {
                $set = array('url' => 'parking/findCarList/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $parklot['pc_secret']);
                $params = array('plantNum' => $parking['carno'], 'cardTypeId' => 2, 'page' => 1, 'size' => 1);
                $res = deliyun_http_post($set, $params);
                if ($res['ecode'] == 0) {
                    $res = $res['data'];
                    if (!empty($res)) {
                        $set['url'] = 'parking/carDelay/';
                        $params = array('plantNum' => $parking['carno'], 'beginDate' => $res['beginDate'], 'endDate' => date('Y-m-d', $bill['enddate']) . ' 23:59:59', 'payMode' => 1, 'chargeMoney' => 0);
                        $res = deliyun_http_post($set, $params);
                        if ($res['ecode'] == 0) {
                            $data_parkpay['starttime'] = strtotime($res['beginDate']);
                            $data_parkpay['endtime'] = strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59');
                        }
                    } else {
                        $set['url'] = 'parking/saveCar/';
                        $params = array('plantNum' => $parking['carno'], 'beginDate' => date('Y-m-d', TIMESTAMP), 'endDate' => date('Y-m-d', $bill['enddate']) . ' 23:59:59', 'carTypeId' => 2, 'money' => 0, 'pname' => $parking['ownername'], 'mobile' => $parking['mobile']);
                        $res = deliyun_http_post($set, $params);
                        if ($res['ecode'] == 0) {
                            $data_parkpay['starttime'] = TIMESTAMP;
                            $data_parkpay['endtime'] = date('Y-m-d', $bill['enddate']) . ' 23:59:59';
                        }
                    }
                }
            } elseif ($parklot['pc_type'] == 8) {
                $whitelist = pdo_get('rhinfo_zyxq_car_whitelist', array('weid' => $mywe['weid'], 'parklotid' => $parklot['id'], 'carno' => $parking['carno']));
                if (!empty($whitelist)) {
                    $data_white = array('weid' => $mywe['weid'], 'parklotid' => $parklot['id'], 'starttime' => TIMESTAMP, 'endtime' => strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59'), 'carno' => $parking['carno'], 'mobile' => $parking['mobile'], 'realname' => $parking['ownername'], 'idcard' => '', 'status' => 1, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    $data_parkpay['starttime'] = TIMESTAMP;
                    $data_parkpay['endtime'] = strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59');
                    $res = pdo_insert('rhinfo_zyxq_car_whitelist', $data_white);
                } else {
                    $data_white = array('endtime' => strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59'), 'carno' => $parking['carno'], 'mobile' => $parking['mobile'], 'realname' => $parking['ownername'], 'idcard' => '', 'status' => 1, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    $data_parkpay['starttime'] = $whitelist['starttime'];
                    $data_parkpay['endtime'] = strtotime(date('Y-m-d', $bill['enddate']) . ' 23:59:59');
                    $res = pdo_update('rhinfo_zyxq_car_whitelist', $data_white, array('weid' => $mywe['weid'], 'id' => $whitelist['id']));
                }
            }
        }
        pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
        echo 'ok';
        exit(0);
    }
} elseif ($operation == 'delbill') {
    $current = '删除车位账单';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_carbill', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'payprint') {
    $current = '打印赁证';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $sql = 'update ' . tablename('rhinfo_zyxq_carbill') . ' set printtimes = printtimes + 1 where id=:billid and weid = :weid';
        pdo_query($sql, array(':weid' => $mywe['weid'], ':billid' => $id));
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where weid = :weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:pid ';
    $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['totalfee'] = $item['fee'];
    $item['ctotalfee'] = num_to_rmb($item['totalfee']);
    if ($item['printtimes'] == 0 && empty($item['printpznum'])) {
        $pznopre = 'CAR' . $item['rid'] . '-';
        $sql = 'select max(printpznum) from' . tablename('rhinfo_zyxq_carbill') . ' where weid = :weid and rid=:rid and printpznum like \'' . $pznopre . '%\'';
        $pzno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
        $printpznum = createpznum(substr($pzno, strlen($pznopre), 12));
        $printpznum = $pznopre . $printpznum;
        $sql = 'update ' . tablename('rhinfo_zyxq_carbill') . ' set printpznum= \'' . $printpznum . '\' where weid = :weid and id=:id';
        pdo_query($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    } else {
        $printpznum = $item['printpznum'];
    }
    include $this->mywtpl('payprint');
} elseif ($operation == 'movecar') {
    $current = '挪车记录';
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_carmove') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_carmove') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('movecar');
} elseif ($operation == 'moveedit') {
    $current = '查看挪车';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_carmove') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $images = iunserializer($item['images']);
    load()->model('mc');
    $fans = array();
    $fans = mc_fansinfo($item['fromopenid'], 0, $mywe['weid']);
    include $this->mywtpl('moveview');
} elseif ($operation == 'movedelete') {
    $current = '删除挪车记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_carmove', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'monthcard') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and rid=:rid and id=:parklotid';
        $parklot = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':parklotid' => $_GPC['parklotid']));
        if (!empty($parklot['pc_plotid']) && strtotime($_GPC['enddate']) > TIMESTAMP) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
            $parking = array();
            if ($_GPC['cfrom'] == 1) {
                $parking = pdo_get($tablename, array('weid' => $mywe['weid'], 'id' => $_GPC['id']), array('id', 'title'));
            } elseif ($_GPC['cfrom'] == 2) {
                $car = pdo_get('rhinfo_zyxq_car', array('weid' => $mywe['weid'], 'id' => $_GPC['id']), array('id', 'carno'));
                $sql = 'select id,title from ' . tablename($tablename) . ' where weid=:weid and carno=:carno and enddate >:enddate';
                $parking = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':carno' => $car['carno'], ':enddate' => TIMESTAMP));
            }
            $parking['id'] = empty($parking['id']) ? '0' : $parking['id'];
            $parking['title'] = empty($parking['title']) ? '000' : $parking['title'];
            $data_parkpay = array('weid' => $mywe['weid'], 'pid' => $region['pid'], 'rid' => $_GPC['rid'], 'parklotid' => $parklot['id'], 'title' => '办理月卡', 'category' => 2, 'carno' => $_GPC['carno'], 'fee' => 0, 'mobile' => $_GPC['mobile'], 'realname' => $_GPC['ownername'], 'parkingid' => $parking['id'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $parklot['pc_type'] = !empty($parklot['pc_type']) ? $parklot['pc_type'] : $region['pc_type'];
            if ($parklot['pc_type'] == 1) {
                $set = array();
                $set['userId'] = $region['pc_appid'];
                $set['userKey'] = $region['pc_secret'];
                $params = array();
                $params['searchParamsItems'] = array(array('name' => 'rows', 'value' => 1), array('name' => 'page', 'value' => 1), array('name' => 'plgId', 'value' => $parklot['pc_plotid']), array('name' => 'licensePlate', 'value' => $_GPC['carno']));
                $res = icar_post_querycarid($set, $params);
                if ($res['successCode'] == '100') {
                    $rows = $res['rows'];
                    if (empty($rows)) {
                        $params = array();
                        $params['updateCreateParamItems'] = array(array('name' => 'plgId', 'value' => $parklot['pc_plotid']), array('name' => 'name', 'value' => $_GPC['carno']), array('name' => 'licensePlateType', 'value' => 0), array('name' => 'licensePlate', 'value' => $_GPC['carno']), array('name' => 'expireTime', 'value' => $_GPC['enddate'] . ' 00:00:00'));
                        $res = icar_post_createcarid($set, $params);
                        if ($res['successCode'] == '100') {
                            echo 'ok';
                            exit(0);
                        } else {
                            echo $res['result'] . $res['errorCode'];
                            exit(0);
                        }
                    } else {
                        $params = array();
                        $params['updateCreateParamItems'] = array(array('name' => 'id', 'value' => $rows['id']), array('name' => 'name', 'value' => $parking['carno']), array('name' => 'expireTime', 'value' => $_GPC['enddate'] . ' 00:00:00'));
                        $res = icar_post_editcarid($set, $params);
                        if ($res['successCode'] == '100') {
                            echo 'ok';
                            exit(0);
                        } else {
                            echo $res['result'] . $res['errorCode'];
                            exit(0);
                        }
                    }
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
                echo 'ok';
                exit(0);
            } elseif ($parklot['pc_type'] == 2) {
                $set = array();
                $set['pc_appid'] = $region['pc_appid'];
                $set['pc_secret'] = $region['pc_secret'];
                $set['url'] = 'api/queryCreatMonthCardData.action';
                $params = array('parkId' => $parklot['pc_plotid'], 'type' => 1, 'carNum' => $_GPC['carno']);
                $res = ipms_http_post($set, $params);
                if ($res['result'] == 'success') {
                    $res = json_decode(urldecode($res['data']), true);
                    if (empty($res)) {
                        $set['url'] = 'third_api/monthCardAppAction_create.action';
                        $params = array('parkId' => $parklot['pc_plotid'], 'carNum' => $_GPC['carno'], 'startTime' => $_GPC['startdate'], 'endTime' => $_GPC['enddate'], 'userPhone' => $_GPC['mobile'], 'userName' => $_GPC['ownername'], 'loginUserName' => $_GPC['mobile'], 'rechangeFee' => 100);
                        $res = ipms_httpsign_post($set, $params);
                        if ($res['resultCode'] == '0000') {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate']);
                            pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                            pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                            echo 'ok';
                            exit(0);
                        } else {
                            echo $res['resultMsg'];
                            exit(0);
                        }
                    } else {
                        $res = $res['datas'][0];
                        if (!empty($res)) {
                            $cardno = $res['id'];
                            $startdate = date('Y-m-d', strtotime($res['tpStartTime']));
                            $set['url'] = 'third_api/monthCardAppAction_updateMonthCard.action';
                            $params = array('id' => $cardno, 'parkId' => $parklot['pc_plotid'], 'carNum' => $_GPC['carno'], 'startTime' => $startdate, 'endTime' => $_GPC['enddate'], 'loginUserName' => $_GPC['mobile'], 'rechangeFee' => 100);
                            $res = ipms_httpsign_post($set, $params);
                            if ($res['resultCode'] == '0000') {
                                $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                                $data_parkpay['endtime'] = strtotime($_GPC['enddate']);
                                pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                                pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                                echo 'ok';
                                exit(0);
                            } else {
                                echo $res['resultMsg'];
                                exit(0);
                            }
                        } else {
                            echo '未找到月卡信息';
                            exit(0);
                        }
                    }
                } else {
                    echo $res['resultMsg'];
                    exit(0);
                }
                echo '停车场云端编号为空';
                exit(0);
            } elseif ($parklot['pc_type'] == 3) {
                $set = array('url' => 'app/getLaneList.aspx', 'parkno' => $parklot['pc_plotid'], 'secret' => $parklot['pc_secret']);
                $params = array();
                $res = etpcar_http_post($set, $params);
                $resmsg = $res['ReMsg'];
                if ($resmsg['ErrNo'] == '0000') {
                    $lanelist = $res['LaneList'];
                    $parkios = $lanelist['LaneInfo'];
                    $machlist = '';
                    foreach ($parkios as $vio) {
                        $machlist .= $vio['MachNo'] . '_';
                    }
                    $machlist = substr($machlist, 0, 0 - 1);
                    $set['url'] = 'app/UpMonCar.aspx';
                    $params = array('CarNo' => $_GPC['carno'], 'UserName' => $_GPC['ownername'], 'OperType' => 'Add', 'Fee' => 100, 'StartTime' => date('YmdHis', strtotime($_GPC['startdate'])), 'EndTime' => date('YmdHis', strtotime($_GPC['enddate'] . ' 23:59:59')), 'CarType' => '月卡A', 'CardStatus' => 0, 'Balance' => 0, 'MachList' => $machlist, 'SetNo' => $parking['title']);
                    $res = etpcar_http_post($set, $params);
                    $res = $res['ReMsg'];
                    if ($res['ErrNo'] == '0000') {
                        $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                        $data_parkpay['endtime'] = strtotime($_GPC['enddate'] . ' 23:59:59');
                        pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                        pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                        echo 'ok';
                    } else {
                        $params = array('CarNo' => $_GPC['carno'], 'UserName' => $_GPC['ownername'], 'OperType' => 'Delay', 'Fee' => 100, 'StartTime' => date('YmdHis', strtotime($_GPC['startdate'])), 'EndTime' => date('YmdHis', strtotime($_GPC['enddate'] . ' 23:59:59')), 'CarType' => '月卡A', 'CardStatus' => 0, 'Balance' => 0, 'MachList' => $machlist, 'SetNo' => $parking['title']);
                        $res = etpcar_http_post($set, $params);
                        $res = $res['ReMsg'];
                        if ($res['ErrNo'] == '0000') {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate'] . ' 23:59:59');
                            pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                            pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                            echo 'ok';
                        } else {
                            echo '操作失败2' . $res['ErrMsg'];
                        }
                    }
                } else {
                    echo '操作失败1' . $res['ErrMsg'];
                }
                exit(0);
            } elseif ($parklot['pc_type'] == 4) {
                $set = array('url' => 'parking/findCarList/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $parklot['pc_secret']);
                $params = array('plantNum' => $_GPC['carno'], 'cardTypeId' => 2, 'page' => 1, 'size' => 1);
                $res = deliyun_http_post($set, $params);
                if ($res['ecode'] == 0) {
                    $res = $res['data'];
                    if (!empty($res)) {
                        $set['url'] = 'parking/carDelay/';
                        $params = array('plantNum' => $_GPC['carno'], 'beginDate' => $res['beginDate'], 'endDate' => $_GPC['enddate'], 'payMode' => 1, 'chargeMoney' => 1);
                        $res = deliyun_http_post($set, $params);
                        if ($res['ecode'] == 0) {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate']);
                            pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                            pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                            echo 'ok';
                            exit(0);
                        } else {
                            echo $res['msg'];
                            exit(0);
                        }
                    } else {
                        $set['url'] = 'parking/saveCar/';
                        $params = array('plantNum' => $_GPC['carno'], 'beginDate' => $_GPC['startdate'], 'endDate' => $_GPC['enddate'], 'carTypeId' => 2, 'money' => 1, 'pname' => $_GPC['ownername'], 'mobile' => $_GPC['mobile']);
                        $res = deliyun_http_post($set, $params);
                        if ($res['ecode'] == 0) {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate']);
                            pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                            pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                            echo 'ok';
                            exit(0);
                        } else {
                            echo $res['msg'];
                            exit(0);
                        }
                    }
                }
            } elseif ($parklot['pc_type'] == 5) {
                $set = array('url' => 'parking/findCarList/', 'accessKeyID' => $region['pc_appid'], 'accessKeySecret' => $region['pc_secret'], 'commKey' => $parklot['pc_secret']);
                $params = array('plantNum' => $_GPC['carno'], 'cardTypeId' => 2, 'page' => 1, 'size' => 1);
                $res = deliyun_http_post($set, $params);
                if ($res['ecode'] == 0) {
                    $res = $res['data'];
                    if (!empty($res)) {
                        $set['url'] = 'parking/carDelay/';
                        $params = array('plantNum' => $_GPC['carno'], 'beginDate' => $res['beginDate'], 'endDate' => $_GPC['enddate'] . ' 23:59:59', 'payMode' => 1, 'chargeMoney' => 0);
                        $res = deliyun_http_post($set, $params);
                        if ($res['ecode'] == 0) {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate'] . ' 23:59:59');
                            pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                            pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                            echo 'ok';
                        } else {
                            echo '操作失败' . $res['msg'];
                        }
                    } else {
                        $set['url'] = 'parking/saveCar/';
                        $params = array('plantNum' => $_GPC['carno'], 'beginDate' => $_GPC['startdate'], 'endDate' => $_GPC['enddate'] . ' 23:59:59', 'carTypeId' => 2, 'money' => 0, 'pname' => $_GPC['ownername'], 'mobile' => $_GPC['mobile']);
                        $res = deliyun_http_post($set, $params);
                        if ($res['ecode'] == 0) {
                            $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                            $data_parkpay['endtime'] = strtotime($_GPC['enddate'] . ' 23:59:59');
                            pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                            pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                            echo 'ok';
                        } else {
                            echo '操作失败' . $res['msg'];
                        }
                    }
                } else {
                    echo '操作失败' . $res['msg'];
                }
                exit(0);
            } elseif ($parklot['pc_type'] == 8) {
                $whitelist = pdo_get('rhinfo_zyxq_car_whitelist', array('weid' => $mywe['weid'], 'parklotid' => $parklot['id'], 'carno' => $_GPC['carno']));
                if (!empty($whitelist)) {
                    $data_white = array('weid' => $mywe['weid'], 'parklotid' => $parklot['id'], 'starttime' => $whitelist['starttime'], 'endtime' => strtotime($_GPC['enddate'] . ' 23:59:59'), 'carno' => $_GPC['carno'], 'mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname'], 'idcard' => '', 'status' => 1, 'cuid' => $myse['uid'], 'ctime' => TIMESTAMP);
                    $res = pdo_insert('rhinfo_zyxq_car_whitelist', $data_white);
                } else {
                    $data_white = array('starttime' => strtotime($_GPC['startdate']), 'endtime' => strtotime($_GPC['enddate'] . ' 23:59:59'), 'mobile' => $_GPC['mobile'], 'realname' => $_GPC['realname'], 'idcard' => '', 'status' => 1, 'cuid' => $myse['uid']);
                    $res = pdo_update('rhinfo_zyxq_car_whitelist', $data_white, array('weid' => $mywe['weid'], 'id' => $whitelist['id']));
                }
                if ($res) {
                    $data_parkpay['starttime'] = strtotime($_GPC['startdate']);
                    $data_parkpay['endtime'] = strtotime($_GPC['enddate'] . ' 23:59:59');
                    pdo_insert('rhinfo_zyxq_parkpay_log', $data_parkpay);
                    pdo_update($tablename, array('startdate' => $data_parkpay['starttime'], 'enddate' => $data_parkpay['endtime']), array('weid' => $mywe['weid'], 'id' => $parking['id']));
                    echo 'ok';
                } else {
                    exit('操作失败');
                }
            } else {
                echo '未启用云停车';
                exit(0);
            }
        }
    }
    $id = $_GPC['cfrom'] == 1 ? $_GPC['parkid'] : $_GPC['carid'];
    $tablename = $_GPC['cfrom'] == 1 ? $tablename : 'rhinfo_zyxq_car';
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and pid = :pid and rid=:rid';
    $parkings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mywtpl('monthcard');
} elseif ($operation == 'monthcarlog') {
    $current = '月卡记录';
    $myret = 0;
    $condition1 = '';
    $starttime = strtotime('now -30days');
    $endtime = TIMESTAMP;
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (carno LIKE \'%' . $_GPC['keyword'] . '%\' or payno LIKE \'%' . $_GPC['keyword'] . '%\' or mobile LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $mydate = $_GPC['mydate'];
    if ($mydate) {
        $starttime = strtotime($mydate['start']);
        $endtime = strtotime($mydate['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=2 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and rid = :rid and id = :parklotid';
            $parklot = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':parklotid' => $data[$k]['parklotid']));
            $data[$k]['parklot'] = $parklot;
            $data[$k]['starttime'] = !empty($data[$k]['starttime']) ? date('Y-m-d H:i', $data[$k]['starttime']) : '';
            $data[$k]['endtime'] = !empty($data[$k]['endtime']) ? date('Y-m-d H:i', $data[$k]['endtime']) : '';
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        if ($_GPC['export'] == 'export') {
            $filter = array('id' => 'ID', 'region' => '所属主体', 'parklot' => '停车场', 'carno' => '车牌号码', 'realname' => '业主姓名', 'starttime' => '起始时间', 'endtime' => '终止时间', 'fee' => '费用', 'payno' => '交易单号', 'ctime' => '创建时间');
            export_excel($data, $filter, $current);
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('monthcarlog');
} elseif ($operation == 'parkpaylog') {
    $current = '停车缴费';
    $myret = 0;
    $condition1 = '';
    $starttime = strtotime('now -30days');
    $endtime = TIMESTAMP;
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (carno LIKE \'%' . $_GPC['keyword'] . '%\' or payno LIKE \'%' . $_GPC['keyword'] . '%\' or mobile LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $mydate = $_GPC['mydate'];
    if ($mydate) {
        $starttime = strtotime($mydate['start']);
        $endtime = strtotime($mydate['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and rid = :rid and id = :parklotid';
            $parklot = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':parklotid' => $data[$k]['parklotid']));
            $data[$k]['parklot'] = $parklot;
            $data[$k]['starttime'] = !empty($data[$k]['starttime']) ? date('Y-m-d H:i', $data[$k]['starttime']) : '';
            $data[$k]['endtime'] = !empty($data[$k]['endtime']) ? date('Y-m-d H:i', $data[$k]['endtime']) : '';
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':uid' => $data[$k]['cuid']));
            $fans = mc_fansinfo($data[$k]['cuid'], 0, $mywe['weid']);
            if (!empty($member)) {
                $data[$k]['realname'] = $member['realname'];
                $data[$k]['mobile'] = $member['mobile'];
            } else {
                $data[$k]['realname'] = $fans['nickname'];
                $data[$k]['mobile'] = $fans['mobile'];
            }
            ($k += 1) + -1;
        }
        if ($_GPC['export'] == 'export') {
            $filter = array('id' => 'ID', 'region' => '所属主体', 'parklot' => '停车场', 'carno' => '车牌号码', 'realname' => '业主姓名', 'starttime' => '进场时间', 'endtime' => '出场时间', 'fee' => '费用', 'payno' => '交易单号', 'ctime' => '创建时间');
            export_excel($data, $filter, $current);
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('parkpaylog');
} elseif ($operation == 'carbillpay') {
    $current = '缴费记录';
    $paytype = $this->paytype;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $regions = pdo_fetchall($sql, $params);
    if ($_GPC['rid']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    if (!empty($_GPC['paytype'])) {
        $condition .= ' AND paytype = ' . $_GPC['paytype'];
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $paydate = $_GPC['paydate'];
    if ($billdate) {
        $starttime = strtotime($paydate['start']);
        $endtime = strtotime($paydate['end']);
        $condition .= ' and paydate>=' . $starttime . ' and paydate<=' . $endtime;
    } else {
        $starttime = strtotime('now -180days');
        $endtime = TIMESTAMP;
    }
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and ' . $condition;
    $totalfee = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and ' . $condition;
    $totalpayfee = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_carbill') . ' where  status=2 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t`PAYDATE` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $data[$k]['paydate'] = $data[$k]['paydate'] ? date('Y-m-d H:i', $data[$k]['paydate']) : '';
            $data[$k]['paytype'] = $paytype[$data[$k]['paytype']];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
            $data[$k]['location'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
            ($k += 1) + -1;
        }
        if ($_GPC['export'] == 'export') {
            $filter = array('id' => 'ID', 'region' => '所属主体', 'location' => '区域', 'title' => '车位', 'measure' => '计量单位', 'daterange' => '账单周期', 'price' => '单价', 'fee' => '费用（元）', 'payfee' => '实付', 'paytype' => '支付方式', 'paydate' => '付款日期', 'remark' => '备注');
            export_excel($data, $filter, '账单');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('carbillpay');
} elseif ($operation == 'delmonthcar') {
    $current = '删除月卡';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parkpay_log', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'carshare') {
    $current = '车位共享';
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
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND lockmac LIKE \'%' . $_GPC['keyword'] . '%\'';
        $sql = 'select uid from ' . tablename('mc_members') . ' where realname like :keyword or nickname like :keyword or mobile like :keyword';
        $uid = pdo_fetchcolumn($sql, array(':keyword' => '%' . $_GPC['keyword'] . '%'));
        if (!empty($uid)) {
            $condition .= ' and uid=:uid ';
            $params[':uid'] = $uid;
        }
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parkinglock') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglock') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and id = :parkingid';
            $parking = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':parkingid' => $data[$k]['parkingid']));
            $data[$k]['parking'] = empty($parking) ? '无' : $parking;
            if ($region['parklock_type'] == 1) {
                $set = array('url' => 'getLock', 'token' => $region['parklock_token']);
                $lockdata = '/' . $data[$k]['lockmac'];
                $res = pshare_http_post2($set, $lockdata);
                if ($res['code'] == 200) {
                    $data[$k]['parklock'] = $res['data'];
                }
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('carshare');
} elseif ($operation == 'lockdelete') {
    $current = '删除车位锁';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parkinglock', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'parklocklog') {
    $rights = $this->myrights(7, $mydo, 'carshare');
    $current = '操作记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and lockid = :lockid';
    $params[':lockid'] = $id;
    $ctrltime = $_GPC['ctrltime'];
    if (!empty($opentime)) {
        $starttime = strtotime($ctrltime['start']);
        $endtime = strtotime($ctrltime['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    if (!empty($_GPC['keyword'])) {
        $sql = 'select uid from ' . tablename('mc_members') . ' where realname like :keyword or nickname like :keyword or mobile like :keyword';
        $uid = pdo_fetchcolumn($sql, array(':keyword' => '%' . $_GPC['keyword'] . '%'));
        if (!empty($uid)) {
            $condition .= ' and uid=:uid ';
            $params[':uid'] = $uid;
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglock') . ' where weid=:weid and id=:id';
    $parklock = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    $current = $parklock['title'] . $current;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parklocklog') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parklocklog') . ' where ' . $condition . ' order by ctime desc ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $parklock['rid'], ':uid' => $data[$k]['uid']));
            $fans = mc_fansinfo($data[$k]['uid'], 0, $mywe['weid']);
            if (!empty($member)) {
                $data[$k]['realname'] = $member['realname'];
                $data[$k]['address'] = $member['address'];
            } else {
                $data[$k]['realname'] = $fans['nickname'];
                $data[$k]['address'] = $fans['address'];
            }
            $data[$k]['avatar'] = $fans['avatar'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('parklocklog');
} elseif ($operation == 'dellocklog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parklocklog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'remonthcard') {
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and id=:id';
        $parkpay = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $_GPC['id']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and rid=:rid and id=:parklotid';
        $parklot = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $parkpay['rid'], ':parklotid' => $parkpay['parklotid']));
        if (!empty($parklot['pc_plotid']) && $parkpay['endtime'] > TIMESTAMP) {
            $res = $this->pc_remonthcard($parkpay['rid'], $parklot, $parkpay);
            if ($res['code'] == 1) {
                exit('ok');
            } else {
                exit($res['msg']);
            }
        }
    }
    exit('操作异常');
}
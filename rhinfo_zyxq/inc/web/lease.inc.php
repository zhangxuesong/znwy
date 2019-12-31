<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '租赁管理';
$mydo = 'lease';
$tablename = 'rhinfo_zyxq_lease';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$paymethod = array(array('id' => 1, 'title' => '按月份'), array('id' => 2, 'title' => '按季度'), array('id' => 3, 'title' => '按半年'), array('id' => 4, 'title' => '按整年'), array('id' => 5, 'title' => '一次性'));
if ($operation == 'list') {
    $rights = $this->myrights(16, $mydo, 'list');
    $myret = 0;
    $current = '租赁合同';
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :id and weid = :weid';
            $lessee = pdo_fetchcolumn($sql, array(':id' => $data[$k]['lesseeid'], ':weid' => $mywe['weid']));
            $data[$k]['lessee'] = $lessee;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '有效';
            } elseif ($data[$k]['status'] == 2) {
                $data[$k]['statustxt'] = '终止';
            } else {
                $data[$k]['statustxt'] = '未生效';
            }
            if (!($data[$k]['enddate'] >= TIMESTAMP) && !empty($data[$k]['enddate'])) {
                $data[$k]['status'] = '终止';
                pdo_update($tablename, array('status' => 2), array('weid' => $mywe['weid'], 'id' => $data[$k]['id']));
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'plist') {
    $rights = $this->myrights(16, $mydo, 'plist');
    $myret = 0;
    $current = '承租人';
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_lessee') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_lessee') . ' where ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '正常';
            } else {
                $data[$k]['statustxt'] = '停用';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('plist');
} elseif ($operation == 'add') {
    $current = '添加合同';
    if ($_W['ispost']) {
        $location = explode(',', $_GPC['bids']);
        $location = is_array($location) ? iserializer($location) : iserializer(array());
        $attachment = is_array($_GPC['attachment']) ? iserializer($_GPC['attachment']) : iserializer(array());
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'lesseeid' => $_GPC['lesseeid'], 'contact' => $_GPC['contact'], 'mobile' => $_GPC['mobile'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'leaseterm' => $_GPC['leaseterm'], 'area' => $_GPC['area'], 'deposit' => $_GPC['deposit'], 'depositdesc' => $_GPC['depositdesc'], 'totalfee' => $_GPC['totalfee'], 'signdate' => strtotime($_GPC['signdate']), 'attachment' => $attachment, 'location' => $location, 'paymethod' => $_GPC['paymethod'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylessee = array();
    $mylocation = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
            $lessees = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylessee[$regions[$m]['id']] = $lessees;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_leaselocation') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylocation[$regions[$m]['id']] = $locations;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑合同';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $location = explode(',', $_GPC['bids']);
        $location = is_array($location) ? iserializer($location) : iserializer(array());
        $attachment = is_array($_GPC['attachment']) ? iserializer($_GPC['attachment']) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'lesseeid' => $_GPC['lesseeid'], 'contact' => $_GPC['contact'], 'mobile' => $_GPC['mobile'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'leaseterm' => $_GPC['leaseterm'], 'area' => $_GPC['area'], 'deposit' => $_GPC['deposit'], 'depositdesc' => $_GPC['depositdesc'], 'totalfee' => $_GPC['totalfee'], 'signdate' => strtotime($_GPC['signdate']), 'attachment' => $attachment, 'location' => $location, 'paymethod' => $_GPC['paymethod'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        pdo_update($tablename, $data, array('weid' => $mywe['weid'], 'id' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylessee = array();
    $mylocation = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
            $lessees = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylessee[$regions[$m]['id']] = $lessees;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_leaselocation') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylocation[$regions[$m]['id']] = $locations;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $locationarr = iunserializer($item['location']);
    $sql = 'select id, title from ' . tablename('rhinfo_zyxq_leaselocation') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title,id ASC ';
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $count = count($data);
    $locationarr = array_filter($locationarr);
    $locationame = array();
    $m = 0;
    while (!($m >= $count)) {
        if (in_array($data[$m]['id'], $locationarr)) {
            $locationame[] = $data[$m]['title'];
        }
        ($m += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_lessee') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $elessees = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除合同';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zyxq_lease', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '合同状态';
    $id = intval($_GPC['rid']);
    $data = array('status' => $_GPC['status']);
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'addlessee') {
    $current = '添加承租人';
    if ($_W['ispost']) {
        $attachment = is_array($_GPC['attachment']) ? iserializer($_GPC['attachment']) : iserializer(array());
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'lesstype' => $_GPC['lesstype'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'address' => $_GPC['address'], 'mobile' => $_GPC['mobile'], 'certtype' => $_GPC['certtype'], 'certno' => $_GPC['certno'], 'certphoto' => $_GPC['certphoto'], 'attachment' => $attachment, 'operateitem' => $_GPC['operateitem'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_lessee', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'plist')) . $mywe['direct']);
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
    include $this->mywtpl('postlessee');
} elseif ($operation == 'editlessee') {
    $current = '编辑承租人';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $attachment = is_array($_GPC['attachment']) ? iserializer($_GPC['attachment']) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'lesstype' => $_GPC['lesstype'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'address' => $_GPC['address'], 'mobile' => $_GPC['mobile'], 'certtype' => $_GPC['certtype'], 'certno' => $_GPC['certno'], 'certphoto' => $_GPC['certphoto'], 'attachment' => $attachment, 'operateitem' => $_GPC['operateitem'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $result = pdo_update('rhinfo_zyxq_lessee', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'plist')) . $mywe['direct']);
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('postlessee');
} elseif ($operation == 'dellessee') {
    $current = '删除承租人';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zyxq_lessee', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'location') {
    $rights = $this->myrights(16, $mydo, 'plist');
    $myret = 0;
    $current = '租赁区域';
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_leaselocation') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_leaselocation') . ' where ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('location');
} elseif ($operation == 'addlocation') {
    $current = '租赁区域';
    if ($_W['ispost']) {
        $location = explode(',', $_GPC['bids']);
        $location = is_array($location) ? iserializer($location) : iserializer(array());
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'location' => $location, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_leaselocation', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'location')) . $mywe['direct']);
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
        $m = 0;
        while (!($m >= count($regions))) {
            if ($_GPC['category'] == '1') {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            }
            if ($_GPC['category'] == '2') {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_shop') . ' where  weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            }
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('postlocation');
} elseif ($operation == 'editlocation') {
    $current = '租赁区域';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $location = explode(',', $_GPC['bids']);
        $location = is_array($location) ? iserializer($location) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'location' => $location, 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_leaselocation', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'location')) . $mywe['direct']);
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
        $m = 0;
        while (!($m >= count($regions))) {
            if ($_GPC['category'] == '1') {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            }
            if ($_GPC['category'] == '2') {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_shop') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title*1,id ASC ';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            }
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_leaselocation') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $locationarr = iunserializer($item['location']);
    if ($_GPC['category'] == '1') {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title,id ASC ';
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    } else {
        $sql = 'select id, title from ' . tablename('rhinfo_zyxq_shop') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title,id ASC ';
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    }
    $count = count($data);
    $locationarr = array_filter($locationarr);
    $locationame = array();
    $m = 0;
    while (!($m >= $count)) {
        if (in_array($data[$m]['id'], $locationarr)) {
            $locationame[] = $data[$m]['title'];
        }
        ($m += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('postlocation');
} elseif ($operation == 'dellocation') {
    $current = '删除区域';
    $id = $_GPC['id'];
    $result = pdo_delete('rhinfo_zyxq_leaselocation', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'feeitem') {
    $current = '承租收费项目';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $feeitem = explode(',', $_GPC['bids']);
        $feeitem = is_array($feeitem) ? iserializer($feeitem) : iserializer(array());
        $data = array('feeitem' => $feeitem);
        $res = pdo_update($tablename, $data, array('weid' => $mywe['weid'], 'id' => $id));
        if (!empty($res)) {
            $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        }
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $feeitemarr = iunserializer($item['feeitem']);
    $sql = 'select id, title from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and pid=:pid and rid=:rid and  calmethod>5 ORDER BY title,id ASC ';
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $count = count($data);
    $feeitemarr = array_filter($feeitemarr);
    $feeitemname = array();
    $m = 0;
    while (!($m >= $count)) {
        if (in_array($data[$m]['id'], $feeitemarr)) {
            $feeitemname[] = $data[$m]['title'];
        }
        ($m += 1) + -1;
    }
    include $this->mywtpl('feeitem');
} elseif ($operation == 'bill') {
    $rights = $this->myrights(16, $mydo, 'bill');
    $myret = 0;
    $current = '租赁账单';
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '未支付';
            } elseif ($data[$k]['status'] == 2) {
                $data[$k]['statustxt'] = '已支付';
            } else {
                $data[$k]['statustxt'] = '免费';
            }
            $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :leaseid and weid = :weid';
            $data[$k]['lessee'] = pdo_fetchcolumn($sql, array(':leaseid' => $data[$k]['leaseid'], ':weid' => $mywe['weid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('billlist');
} elseif ($operation == 'addbill') {
    $current = '收费账单';
    if ($_W['ispost']) {
        $pid = $_GPC['pid'];
        $rid = $_GPC['rid'];
        $nextenddate = strtotime($_GPC['nextenddate']);
        $condition .= ' and pid = :pid and rid=:rid';
        $params[':pid'] = $_GPC['pid'];
        $params[':rid'] = $_GPC['rid'];
        if (!empty($_GPC['lesseeid'])) {
            $condition .= ' and lesseeid=:lesseeid';
            $params[':lesseeid'] = $_GPC['lesseeid'];
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
        $i = 0;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_lease') . ' where status=1 and ' . $condition;
        $leases = pdo_fetchall($sql, $params);
        $data = array();
        $feebillitem_data = array();
        $m = 0;
        while (!($m >= count($leases))) {
            if (!empty($leases[$m]['leaseterm'])) {
                if ($leases[$m]['enddate'] >= TIMESTAMP) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebillitem') . ' where weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid';
                    $myfeeitem = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':leaseid' => $leases[$m]['id'], ':itemid' => 0));
                    $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'leaseid' => $leases[$m]['id'], 'category' => 0, 'remark' => $_GPC['remark']);
                    $feebillitem_data = $data;
                    $data['lesseeid'] = $leases[$m]['lesseeid'];
                    $data['title'] = '租金';
                    $feebillitem_data['title'] = '租金';
                    if (empty($myfeeitem)) {
                        if ($leases[$m]['paymethod'] == 1) {
                            $month = 1;
                            $data['fee'] = $leases[$m]['totalfee'] / $leases[$m]['leaseterm'];
                        } elseif ($leases[$m]['paymethod'] == 2) {
                            $month = $leases[$m]['leaseterm'] >= 3 ? 3 : $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'] * $month / $leases[$m]['leaseterm'];
                        } elseif ($leases[$m]['paymethod'] == 3) {
                            $month = $leases[$m]['leaseterm'] >= 6 ? 6 : $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'] * $month / $leases[$m]['leaseterm'];
                        } elseif ($leases[$m]['paymethod'] == 4) {
                            $month = $leases[$m]['leaseterm'] >= 12 ? 12 : $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'] * $month / $leases[$m]['leaseterm'];
                        } elseif ($leases[$m]['paymethod'] == 5) {
                            $month = $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'];
                        }
                        $feebillitem_data['months'] = $month;
                        $str = '+ ' . $month . ' months';
                        $mypaydate = $leases[$m]['startdate'];
                        $mybilldate = $mypaydate;
                        $data['startdate'] = $leases[$m]['startdate'];
                        $data['enddate'] = strtotime($str, $data['startdate']);
                    } else {
                        if ($leases[$m]['paymethod'] == 1) {
                            $month = 1;
                            $data['fee'] = $leases[$m]['totalfee'] / $leases[$m]['leaseterm'];
                            if ($leases[$m]['leaseterm'] - $myfeeitem['months'] >= 1) {
                            }
                        } elseif ($leases[$m]['paymethod'] == 2) {
                            $month = $leases[$m]['leaseterm'] >= 3 ? 3 : $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'] * $month / $leases[$m]['leaseterm'];
                            if ($leases[$m]['leaseterm'] - $myfeeitem['months'] >= 1) {
                            }
                        } elseif ($leases[$m]['paymethod'] == 3) {
                            $month = $leases[$m]['leaseterm'] >= 6 ? 6 : $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'] * $month / $leases[$m]['leaseterm'];
                            if ($leases[$m]['leaseterm'] - $myfeeitem['months'] >= 1) {
                            }
                        } elseif ($leases[$m]['paymethod'] == 4) {
                            $month = $leases[$m]['leaseterm'] >= 12 ? 12 : $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'] * $month / $leases[$m]['leaseterm'];
                            if ($leases[$m]['leaseterm'] - $myfeeitem['months'] >= 1) {
                            }
                        } elseif ($leases[$m]['paymethod'] == 5) {
                            $month = $leases[$m]['leaseterm'];
                            $data['fee'] = $leases[$m]['totalfee'];
                        }
                        $feebillitem_data['months'] = $myfeeitem['months'] + $month;
                        $str = '+ ' . $month . ' months';
                        $mypaydate = $myfeeitem['paydate'];
                        $mybilldate = $myfeeitem['billdate'];
                        $data['startdate'] = strtotime('+ 1 day', $mybilldate);
                        $data['enddate'] = strtotime($str, $mybilldate);
                    }
                    $update_billdate = array();
                    $timediff = $nextenddate - $data['enddate'];
                    $days = intval($timediff / 86400);
                    if ($days >= 0) {
                        $feebillitem_data['paydate'] = $data['startdate'];
                        $feebillitem_data['billdate'] = $data['enddate'];
                        $update_billdate['paydate'] = $data['startdate'];
                        $update_billdate['billdate'] = $data['enddate'];
                        $update_billdate['months'] = $feebillitem_data['months'];
                        $price = round($leases[$m]['totalfee'] / $leases[$m]['leaseterm'], 2);
                        $data['cuid'] = $mywe['uid'];
                        $data['ctime'] = TIMESTAMP;
                        $data['feetype'] = 10;
                        $data['price'] = $price;
                        $data['status'] = 1;
                        $data['itemid'] = 0;
                        if ($region['feebillmonth'] == 1) {
                            $data['daterange'] = date('Y-m-d', $data['startdate']) . '~' . date('Y-m-d', $data['enddate']);
                        } elseif ($region['feebillmonth'] == 2) {
                            $data['daterange'] = date('Y-m-d', $data['startdate']) . '~' . date('Y-m-d', $data['enddate']);
                        } elseif ($feeitems[$k]['paymonths'] > 1) {
                            $data['daterange'] = date('Y-m', $data['startdate']) . '~' . date('Y-m', $data['enddate']);
                        } else {
                            $data['daterange'] = date('Y-m', $data['enddate']);
                        }
                        $feebillitem_data['cuid'] = $mywe['uid'];
                        $feebillitem_data['ctime'] = $data['ctime'];
                        $feebillitem_data['itemid'] = 0;
                        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_leasebill') . ' where category=:category and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid and startdate=:startdate and enddate=:enddate';
                        $total = pdo_fetchcolumn($sql, array(':category' => $feeitems['category'], ':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':leaseid' => $lease['leaseid'], ':itemid' => 0, ':startdate' => $data['startdate'], ':enddate' => $data['enddate']));
                        if (!($total > 0)) {
                            $data['fee'] = round($data['fee'], 2);
                            if ($data['fee'] > 0) {
                                $result = pdo_insert('rhinfo_zyxq_leasebill', $data);
                                $id = pdo_insertid();
                            }
                            if ($myfeeitem) {
                                if ($update_billdate) {
                                    pdo_update('rhinfo_zyxq_leasebillitem', $update_billdate, array('id' => $myfeeitem['id']));
                                }
                            } elseif ($result) {
                                pdo_insert('rhinfo_zyxq_leasebillitem', $feebillitem_data);
                            }
                            ($i += 1) + -1;
                        }
                    }
                }
            }
            ($m += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, '生成' . $i . '笔账单');
        $directurl = $this->createWeburl($mydo, array('op' => 'bill')) . $mywe['direct'];
        $this->mywebmsg('成功', '生成' . $i . '笔账单', $directurl, 'success');
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylessee = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 ORDER BY title*1,id ASC ';
            $lessees = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylessee[$regions[$m]['id']] = $lessees;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('addbill');
} elseif ($operation == 'addfee') {
    $current = '收费账单';
    if ($_W['ispost']) {
        $pid = $_GPC['pid'];
        $rid = $_GPC['rid'];
        $nextenddate = strtotime($_GPC['nextenddate']);
        $condition = ' weid=:weid and pid=:pid and rid=:rid';
        $params = array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid);
        $feeitemstr = $_GPC['feeitems'];
        $i = 0;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and calmethod > 5 and weid=:weid and pid=:pid and rid=:rid and id in(' . $feeitemstr . ')';
        $feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
        $k = 0;
        while (!($k >= count($feeitems))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_lease') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 ';
            $leases = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
            $data = array();
            $feebillitem_data = array();
            $m = 0;
            while (!($m >= count($leases))) {
                if ($leases[$m]['enddate'] >= TIMESTAMP) {
                    $leaseitem = iunserializer($leases[$m]['feeitem']);
                    if (in_array($feeitems[$k]['id'], $leaseitem)) {
                        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'leaseid' => $leases[$m]['id'], 'category' => empty($feeitems[$k]['category']) ? 1 : $feeitems[$k]['category'], 'remark' => $_GPC['remark']);
                        $feebillitem_data = $data;
                        $data['lesseeid'] = $leases[$m]['lesseeid'];
                        $paydate = $leases[$m]['startdate'];
                        $billdate = $paydate;
                        $months = $feeitems[$k]['paymonths'] ? $feeitems[$k]['paymonths'] : 1;
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebillitem') . ' where weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid';
                        $myfeeitem = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':leaseid' => $leases[$m]['id'], ':itemid' => $feeitems[$k]['id']));
                        $mypaydate = $myfeeitem['paydate'] ? $myfeeitem['paydate'] : $paydate;
                        $mybilldate = $myfeeitem['billdate'] ? $myfeeitem['billdate'] : $billdate;
                        $leaseitem = iunserializer($leases[$m]['feeitem']);
                        if (in_array($feeitems[$k]['id'], $leaseitem)) {
                            $data['title'] = $feeitems[$k]['title'];
                            $feebillitem_data['title'] = $feeitems[$k]['title'];
                            $str = '+ ' . $feeitems[$k]['paymonths'] . ' months';
                            $update_billdate = array();
                            if (!empty($mypaydate)) {
                                if ($region['feebillmonth'] == 1) {
                                    $data['startdate'] = strtotime('+ 1 day', $mybilldate);
                                    $data['enddate'] = strtotime($str, $mybilldate);
                                } elseif ($region['feebillmonth'] == 2) {
                                    $data['startdate'] = strtotime('+ 1 day', $mybilldate);
                                    $eyear = date('Y', $mybilldate);
                                    $emonth = date('n', $mybilldate);
                                    $eday = date('d', $mybilldate);
                                    if ($emonth + $feeitems[$k]['paymonths'] > 12) {
                                        $eyear += floor(($emonth + $feeitems[$k]['paymonths']) / 12);
                                        $emonth = $emonth + $feeitems[$k]['paymonths'] - 12;
                                    } else {
                                        $emonth += $feeitems[$k]['paymonths'];
                                    }
                                    $data['enddate'] = daycycle($eyear, $emonth, $eday);
                                } else {
                                    $data['startdate'] = strtotime('+ 1 day', $mybilldate);
                                    $end_date = date('Y-m-d', mktime(23, 59, 59, date('m', $mybilldate) + $feeitems[$k]['paymonths'] + 1, 0, date('Y', $mybilldate)));
                                    $data['enddate'] = strtotime($end_date);
                                }
                                $timediff = $nextenddate - $data['enddate'];
                                $days = intval($timediff / 86400);
                                if ($days >= 0) {
                                    $feebillitem_data['paydate'] = $data['startdate'];
                                    $feebillitem_data['billdate'] = $data['enddate'];
                                    $update_billdate['paydate'] = $data['startdate'];
                                    $update_billdate['billdate'] = $data['enddate'];
                                    $price = $feeitems[$k]['price'] > 0 ? $feeitems[$k]['price'] : 0;
                                    if ($feeitems[$k]['calmethod'] == 6) {
                                        $data['fee'] = 0;
                                    } elseif ($feeitems[$k]['calmethod'] == 7) {
                                        $data['fee'] = $price * $feeitems[$k]['paymonths'];
                                    } elseif ($feeitems[$k]['calmethod'] == 8) {
                                        $data['fee'] = $price * $leases[$m]['area'] * $feeitems[$k]['paymonths'];
                                    }
                                    $data['cuid'] = $mywe['uid'];
                                    $data['ctime'] = TIMESTAMP;
                                    $data['measure'] = $feeitems[$k]['measure'];
                                    $data['feetype'] = 10;
                                    $data['price'] = $price;
                                    $data['status'] = 1;
                                    $data['itemid'] = $feeitems[$k]['id'];
                                    if ($region['feebillmonth'] == 1) {
                                        $data['daterange'] = date('Y-m-d', $data['startdate']) . '~' . date('Y-m-d', $data['enddate']);
                                    } elseif ($region['feebillmonth'] == 2) {
                                        $data['daterange'] = date('Y-m-d', $data['startdate']) . '~' . date('Y-m-d', $data['enddate']);
                                    } elseif ($feeitems[$k]['paymonths'] > 1) {
                                        $data['daterange'] = date('Y-m', $data['startdate']) . '~' . date('Y-m', $data['enddate']);
                                    } else {
                                        $data['daterange'] = date('Y-m', $data['enddate']);
                                    }
                                    $feebillitem_data['cuid'] = $mywe['uid'];
                                    $feebillitem_data['ctime'] = $data['ctime'];
                                    $feebillitem_data['itemid'] = $feeitems[$k]['id'];
                                    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_leasebill') . ' where category=:category and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid and startdate=:startdate and enddate=:enddate';
                                    $total = pdo_fetchcolumn($sql, array(':category' => $feeitems['category'], ':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':leaseid' => $lease['leaseid'], ':itemid' => $feeitems[$k]['id'], ':startdate' => $data['startdate'], ':enddate' => $data['enddate']));
                                    if (!($total > 0)) {
                                        if ($feeitems[$k]['feeround'] == 1) {
                                            $data['fee'] = round($data['fee'], 2);
                                        } elseif ($feeitems[$k]['feeround'] == 2) {
                                            $data['fee'] = round($data['fee'], 1);
                                        } elseif ($feeitems[$k]['feeround'] == 3) {
                                            $data['fee'] = round($data['fee'], 0);
                                        } elseif ($feeitems[$k]['feeround'] == 4) {
                                            $data['fee'] = floor($data['fee']);
                                        }
                                        if ($data['fee'] > 0) {
                                            $result = pdo_insert('rhinfo_zyxq_leasebill', $data);
                                            $id = pdo_insertid();
                                        }
                                        if ($myfeeitem) {
                                            if ($update_billdate) {
                                                pdo_update('rhinfo_zyxq_leasebillitem', $update_billdate, array('id' => $myfeeitem['id']));
                                            }
                                        } elseif ($result) {
                                            pdo_insert('rhinfo_zyxq_leasebillitem', $feebillitem_data);
                                        }
                                        ($i += 1) + -1;
                                    }
                                }
                            }
                        }
                    }
                }
                ($m += 1) + -1;
            }
            ($k += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, '生成' . $i . '笔账单');
        $directurl = $this->createWeburl($mydo, array('op' => 'bill')) . $mywe['direct'];
        $this->mywebmsg('成功', '生成' . $i . '笔账单', $directurl, 'success');
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $myfeeitem = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and calmethod > 5 and weid=:weid and pid=:pid and rid=:rid';
            $feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $myfeeitem[$regions[$m]['id']] = $feeitems;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('addfee');
} elseif ($operation == 'editbill') {
    $current = '编辑收费账单';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('fee' => $_GPC['fee'], 'measure' => $_GPC['measure'], 'remark' => $_GPC['remark']);
        $result = pdo_update('rhinfo_zyxq_leasebill', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'bill')) . $mywe['direct']);
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('editbill');
} elseif ($operation == 'delbill') {
    $current = '删除账单';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zyxq_leasebill', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'mybill') {
    $current = '租赁收款';
    $myret = 1;
    $condition .= $this->myrcondition();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $_GPC['rid'], ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1  and leaseid=:leaseid and ' . $condition . " ORDER BY\r\n\t\t\t\t `ID` ASC ";
    $params[':leaseid'] = $_GPC['leaseid'];
    $data = pdo_fetchall($sql, $params);
    $total = count($data);
    $totalfee = 0;
    $k = 0;
    while (!($k >= count($data))) {
        $data[$k]['region'] = $region['title'];
        $totalfee += $data[$k]['fee'];
        $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :lesseeid and weid = :weid';
        $data[$k]['lessee'] = pdo_fetchcolumn($sql, array(':lesseeid' => $data[$k]['lesseeid'], ':weid' => $mywe['weid']));
        ($k += 1) + -1;
    }
    include $this->mywtpl('mybill');
} elseif ($operation == 'print') {
    $current = '打印账单';
    if ($_W['ispost']) {
        $condition .= ' and pid = :pid and rid=:rid';
        $params[':pid'] = $_GPC['pid'];
        $params[':rid'] = $_GPC['rid'];
        if (!empty($_GPC['lesseeid'])) {
            $condition .= ' and lesseeid=:lesseeid';
            $params[':lesseeid'] = $_GPC['lesseeid'];
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:pid ';
        $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_lease') . ' where status=1 and ' . $condition;
        $leases = pdo_fetchall($sql, $params);
        $i = 0;
        $k = 0;
        while (!($k >= count($leases))) {
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid';
            $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $leases[$k]['pid'], ':rid' => $leases[$k]['rid'], ':leaseid' => $leases[$k]['id']));
            $leases[$k]['totalfee'] = empty($totalfee) ? 0 : $totalfee;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid ORDER BY `ID` ASC ';
            $list = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $leases[$k]['pid'], ':rid' => $leases[$k]['rid'], ':leaseid' => $leases[$k]['id']));
            $leases[$k]['billlist'] = $list;
            if (count($list) > 0) {
                ($i += 1) + -1;
            }
            $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :lesseeid and weid = :weid';
            $leases[$k]['lessee'] = pdo_fetchcolumn($sql, array(':lesseeid' => $leases[$k]['lesseeid'], ':weid' => $mywe['weid']));
            ($k += 1) + -1;
        }
        if ($i > 0) {
            include $this->mywtpl('myprint');
            exit(0);
        } else {
            $this->mywebmsg1('错误', '没有可打印数据', '', 'danger');
        }
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylessee = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 ORDER BY title*1,id ASC ';
            $lessees = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylessee[$regions[$m]['id']] = $lessees;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('print');
} elseif ($operation == 'payprint') {
    $current = '打印赁证';
    $billids = $_GPC['billids'];
    if ($_W['isajax']) {
        $sql = 'update ' . tablename('rhinfo_zyxq_leasebill') . ' set printtimes = printtimes + 1 where id in (' . $billids . ') and weid = :weid';
        pdo_query($sql, array(':weid' => $mywe['weid']));
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=:status and id in (' . $billids . ') and weid = :weid';
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':status' => $_GPC['status']));
    $item = array();
    $item['totalfee'] = 0;
    $item['payfee'] = 0;
    $item['latefee'] = 0;
    $k = 0;
    while (!($k >= count($data))) {
        $item['totalfee'] = $item['totalfee'] + $data[$k]['fee'];
        $item['payfee'] = $item['payfee'] + $data[$k]['payfee'];
        if ($k == 0) {
            $item['pid'] = $data[$k]['pid'];
            $item['rid'] = $data[$k]['rid'];
            $item['paydate'] = $data[$k]['paydate'];
            $item['category'] = $data[$k]['category'];
            $item['printpznum'] = $data[$k]['printpznum'];
            $item['printtimes'] = $data[$k]['printtimes'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :lesseeid and weid = :weid';
            $item['lessee'] = pdo_fetchcolumn($sql, array(':lesseeid' => $data[$k]['lesseeid'], ':weid' => $mywe['weid']));
        }
        ($k += 1) + -1;
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:pid ';
    $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if ($item['printtimes'] == 0 && empty($item['printpznum'])) {
        $pznopre = 'LEA' . $item['rid'];
        $sql = 'select max(printpznum) from' . tablename('rhinfo_zyxq_leasebill') . ' where weid = :weid and rid=:rid and printpznum like \'' . $pznopre . '%\'';
        $pzno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
        $printpznum = createpznum(substr($pzno, strlen($pznopre), 12));
        $printpznum = $pznopre . $printpznum;
    } else {
        $printpznum = $item['printpznum'];
    }
    $sql = 'update ' . tablename('rhinfo_zyxq_leasebill') . ' set printpznum= \'' . $printpznum . '\' where id in (' . $billids . ') and weid = :weid';
    pdo_query($sql, array(':weid' => $mywe['weid']));
    $item['ctotalfee'] = num_to_rmb($item['totalfee']);
    include $this->mywtpl('payprint');
} elseif ($operation == 'sendmsg') {
    $current = '缴费通知';
    if ($_W['isajax']) {
        $sendtype = $_GPC['sendtype'];
        $condition .= ' and pid = :pid and rid=:rid';
        $params[':pid'] = $_GPC['pid'];
        $params[':rid'] = $_GPC['rid'];
        if (!empty($_GPC['lesseeid'])) {
            $condition .= ' and lesseeid=:lesseeid';
            $params[':lesseeid'] = $_GPC['lesseeid'];
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
        if ($sendtype == '2') {
            if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0)) {
                show_json(0, '可发短信数量不足');
            }
        }
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_lease') . ' where status=1 and ' . $condition;
        $leases = pdo_fetchall($sql, $params);
        $i = 0;
        $k = 0;
        while (!($k >= count($leases))) {
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid';
            $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $leases[$k]['pid'], ':rid' => $leases[$k]['rid'], ':leaseid' => $leases[$k]['id']));
            $leases[$k]['totalfee'] = empty($totalfee) ? 0 : $totalfee;
            if (!($leases[$k]['totalfee'] > 0)) {
                break;
            }
            $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
            $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and id=:lesseeid';
            $lessee = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':lesseeid' => $leases[$k]['lesseeid']));
            if (!($sendtype == '1')) {
                break;
            }
            if (empty($lessee['openid'])) {
                break;
            }
            $feecolor = empty($this->syscfg['feecolor']) ? '#000' : $this->syscfg['feecolor'];
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $url = $this->createMobileUrl('lease', array('op' => 'feebill', 'pid' => $leases[$k]['pid'], 'rid' => $leases[$k]['rid'], 'leaseid' => $leases[$k]['leaseid']));
            $url = $this->my_mobileurl($url);
            $postdata = array('first' => array('value' => '您好，您本期账单已出。'), 'userName' => array('value' => $lessee['title']), 'address' => array('value' => $region['title']), 'pay' => array('value' => $totalfee, 'color' => $feecolor), 'remark' => array('value' => '请尽快缴纳，如有疑问，请咨询：' . $region['telphone']));
            if (!empty($this->syscfg['tplid12'])) {
                $res = $this->send_mysendtplnotice($lessee['openid'], $this->syscfg['tplid12'], $postdata, $url, $topcolor);
                if ($res == true) {
                    ($i += 1) + -1;
                }
            }
            if ($i == 100) {
                break;
            }
            if ($sendtype == '2') {
                if (!(empty($lessee['mobile']) && empty($leases[$k]['mobile']))) {
                    $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid';
                    $mindate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $leases[$k]['pid'], ':rid' => $leases[$k]['rid'], ':leaseid' => $leases[$k]['id']));
                    $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid';
                    $maxdate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $leases[$k]['pid'], ':rid' => $leases[$k]['rid'], ':leaseid' => $leases[$k]['id']));
                    if ($mindate && $maxdate) {
                        $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
                    } else {
                        $daterange = '本期账单';
                    }
                    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                        if (!empty($leases[$k]['mobile'])) {
                            $res = $this->send_sms($this->syscfg['smstype'], $leases[$k]['mobile'], $this->syscfg['feeid'], array('name' => $region['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                        } elseif (!empty($lessee['mobile'])) {
                            $res = $this->send_sms($this->syscfg['smstype'], $lessee['mobile'], $this->syscfg['feeid'], array('name' => $region['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                        }
                        if ($res['status'] == 1) {
                            $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                            pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                            $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '缴费通知', 'io' => 2, 'mobile' => $leases[$k]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                            pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                            ($i += 1) + -1;
                        }
                    }
                    if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > $i)) {
                        echo '可发短信数量不足';
                        exit(0);
                    }
                }
            }
            ($k += 1) + -1;
        }
        if ($i > 0) {
            echo 'ok';
        } else {
            echo '暂未欠费或者已发送!';
        }
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylessee = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 ORDER BY title*1,id ASC ';
            $lessees = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylessee[$regions[$m]['id']] = $lessees;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('sendmsg');
} elseif ($operation == 'calpay') {
    $current = '综合收款';
    $paytype = $this->paytype;
    $billids = $_GPC['billids'];
    if ($_W['ispost']) {
        $payfees = floatval($_GPC['payfee']);
        $latefees = floatval($_GPC['latefee']);
        if (!($payfees > 0)) {
            echo '支付金额错误';
            exit(0);
        }
        $itemtitle = '';
        $printdetail = '';
        $printdetail1 = '';
        $senddetail = '';
        $billidarray = explode(',', $billids);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
        $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_lease') . ' where id = :id and weid = :weid';
        $lease = pdo_fetch($sql, array(':id' => $_GPC['leaseid'], ':weid' => $mywe['weid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :id and weid = :weid';
        $lessee = pdo_fetch($sql, array(':id' => $_GPC['lesseeid'], ':weid' => $mywe['weid']));
        $data = array();
        $data['weid'] = $mywe['weid'];
        $data['uid'] = $mywe['uid'];
        $data['openid'] = 0;
        $data['title'] = $region['title'];
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
        $data['billid'] = $billids;
        $data['ctime'] = TIMESTAMP;
        $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
        $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
        $payno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
        $payno = createnum(substr($payno, strlen($paynopre), 14));
        $payno = $paynopre . $payno;
        $data['tid'] = $payno . random(8, 1);
        $data['payno'] = $payno;
        $data['fee'] = $payfees;
        $data['status'] = 0;
        $data['feetype'] = 11;
        $data['pid'] = $_GPC['pid'];
        $data['rid'] = $_GPC['rid'];
        pdo_insert('rhinfo_zyxq_paylog', $data);
        $logid = pdo_insertid();
        if (!empty($_GPC['scanqrcode'])) {
            $params = array();
            $params = array('title' => $data['title'], 'tid' => $data['tid'], 'fee' => $data['fee']);
            $params['uniontid'] = $params['tid'];
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
        $pznopre = 'LEA' . $_GPC['rid'] . '-';
        $sql = 'select max(printpznum) from' . tablename('rhinfo_zyxq_leasebill') . ' where weid = :weid and rid=:rid and printpznum like \'' . $pznopre . '%\'';
        $pzno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $printpznum = createpznum(substr($pzno, strlen($pznopre), 12));
        $printpznum = $pznopre . $printpznum;
        $k = 0;
        while (!($k >= count($billidarray))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and id=:billid and weid = :weid';
            $bill = pdo_fetch($sql, array(':billid' => $billidarray[$k], ':weid' => $mywe['weid']));
            if ($payfees > $bill['fee']) {
                $payfee = $bill['fee'];
            } else {
                $payfee = $payfees;
            }
            $payfees -= $payfee;
            $bill_data = array('status' => 2, 'paytype' => $mypaytype, 'payfee' => $payfee, 'printpznum' => $printpznum, 'paydate' => TIMESTAMP, 'remark' => !empty($_GPC['remark']) ? $bill['remark'] . ' ' . $_GPC['remark'] : $bill['remark']);
            pdo_update('rhinfo_zyxq_leasebill', $bill_data, array('weid' => $mywe['weid'], 'id' => $billidarray[$k]));
            $itemtitle .= $bill['title'];
            $printdetail .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . '\\n';
            $printdetail1 .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . '<BR>';
            $senddetail .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . ',';
            ($k += 1) + -1;
        }
        $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_leasebill') . ' where weid=:weid and rid=:rid and id in(' . $billids . ')';
        $mindate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $bill['rid']));
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_leasebill') . ' where weid=:weid and rid=:rid and id in(' . $billids . ')';
        $maxdate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $bill['rid']));
        if ($mindate == $maxdate) {
            $daterange = date('Y-m', $maxdate);
            $senddetail = '';
        } else {
            $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
        }
        pdo_update('rhinfo_zyxq_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('weid' => $mywe['weid'], 'id' => $logid));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $billids);
        $paymethod = '支付方式:' . $paytype[intval($mypaytype)] . '\\n';
        $paymethod1 = '支付方式:' . $paytype[intval($mypaytype)] . '<BR>';
        $content = '<FB><center>缴费通知单</center></FB>\\n';
        $content .= '房产:' . $region['title'] . '\\n';
        $content .= '缴费类别:' . $itemtitle . '\\n';
        $content .= '缴费金额:' . $data['fee'] . '元\\n';
        $content .= '缴费周期:' . $daterange . '\\n';
        $content .= '缴费时间:' . date('Y-m-d h:m') . '\\n';
        $content .= '缴费人:' . $room['ownername'] . '\\n';
        $content1 = '<CB>缴费通知单</CB><BR>';
        $content1 .= '房产:' . $region['title'] . '<BR>';
        $content1 .= '缴费类别:' . $itemtitle . '<BR>';
        $content1 .= '缴费金额:' . $data['fee'] . '元<BR>';
        $content1 .= '缴费周期:' . $daterange . '<BR>';
        $content1 .= '缴费时间:' . date('Y-m-d h:m') . '<BR>';
        $content1 .= '缴费人:' . $room['ownername'] . '<BR>';
        if ($region['isprintfeedetail'] == 1) {
            $content .= $printdetail;
            $content1 .= $printdetail1;
        }
        $content .= $paymethod . '<right>' . $property['title'] . '</right>';
        $content1 .= $paymethod . '<RIGHT>' . $property['title'] . '</RIGHT>';
        $res = $this->send_print($bill['pid'], $bill['rid'], 3, urlencode($content), $content1);
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的缴费我们已收到', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $region['title'] . ',' . $senddetail . '缴费总额' . $data['fee'] . '元，缴费周期' . $daterange, 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持！'));
        $url = $this->createMobileurl('lease', array('op' => 'feebill'));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid1'])) {
            $this->send_mysendtplnotice($lessee['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
        }
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where status=1 and id in (' . $billids . ') and weid = :weid';
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $item = array();
    $item['totalfee'] = 0;
    $item['payfee'] = 0;
    $item['latefee'] = 0;
    $k = 0;
    while (!($k >= count($data))) {
        if ($data[$k]['status'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
            $feeitem = pdo_fetch($sql, array(':id' => $data[$k]['itemid'], ':weid' => $mywe['weid']));
            $item['payfee'] = $item['payfee'] + $data[$k]['fee'];
            $item['pid'] = $data[$k]['pid'];
            $item['rid'] = $data[$k]['rid'];
            $item['leaseid'] = $data[$k]['leaseid'];
            $item['lesseeid'] = $data[$k]['lesseeid'];
        }
        ($k += 1) + -1;
    }
    $item['totalfee'] = $item['payfee'];
    $item['payfee'] = $item['payfee'] + $item['latefee'];
    include $this->mywtpl('calpay');
} elseif ($operation == 'mybill') {
    $current = '打印收费凭证';
    $myret = 1;
    $condition .= $this->myrcondition();
    $params[':id'] = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where id=:id and status=2 and ' . $condition;
    $feebill = pdo_fetch($sql, $params);
    if ($feebill['tid'] > 0) {
        $room = pdo_get('rhinfo_zyxq_room', array('weid' => $mywe['weid'], 'id' => $_GPC['hid']));
        $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_room') . " as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and r.mobile=:mobile and f.status=2 ORDER BY\r\n\t\t\t\t f.ID ASC ";
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $room['mobile']));
        $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_garage') . " as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and r.mobile=:mobile and f.status=2 ORDER BY\r\n\t\t\t\t f.ID ASC ";
        $data_garage = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $room['mobile']));
        $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_parking') . " as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and r.mobile=:mobile and f.status=2 ORDER BY\r\n\t\t\t\t f.ID ASC ";
        $data_parking = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $room['mobile']));
        $data = array_merge_recursive($data, $data_garage, $data_parking);
    } else {
        $shop = pdo_get('rhinfo_zyxq_shop', array('weid' => $mywe['weid'], 'id' => $_GPC['hid']));
        $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_shop') . " as s on f.hid=s.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and s.mobile=:mobile and f.status=2 ORDER BY\r\n\t\t\t\t f.ID ASC ";
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $shop['mobile']));
    }
    if (empty($data)) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where hid=:hid and status=2 and ' . $condition . " ORDER BY\r\n\t\t\t\t `ID` ASC ";
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':hid' => $_GPC['hid']));
    }
    $totalfee = 0;
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
        ($k += 1) + -1;
    }
    include $this->mywtpl('mybill');
}
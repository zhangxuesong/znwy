<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'security';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '安防管理';
if ($operation == 'list') {
    $current = '巡更路线';
    $rights = $this->myrights(9, $mydo, 'list');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_patrolline') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolline') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
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
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('manage', array('op' => 'secmap', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增巡更路线';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'positions' => $_GPC['positions'], 'title' => $_GPC['title'], 'status' => $_GPC['status'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'isimage' => $_GPC['isimage'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_patrolline', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mypatrol = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid = :weid and pid = :pid and rid = :rid';
            $patrols = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mypatrol[$regions[$m]['id']] = $patrols;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑巡更路线';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'positions' => $_GPC['positions'], 'title' => $_GPC['title'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'isimage' => $_GPC['isimage'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_patrolline', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mypatrol = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolline') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid = :weid and pid = :pid and rid = :rid';
            $patrols = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mypatrol[$regions[$m]['id']] = $patrols;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid = :weid and pid = :pid and rid = :rid';
    $epatrols = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $count = count($epatrols);
    $patrolarr = explode(',', $item['positions']);
    $patrolname = array();
    $k = 0;
    while (!($k >= $count)) {
        if (in_array($epatrols[$k]['id'], $patrolarr)) {
            $patrolname[] = $epatrols[$k]['title'];
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除巡更路线';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_patrolline', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'map') {
    $current = '巡更地图';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolline') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $item['rid'], ':weid' => $mywe['weid']));
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid=:weid and id in(' . $item['positions'] . ')';
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl('map');
} elseif ($operation == 'patrol') {
    $current = '巡更分布';
    $myret = 0;
    $rights = $this->myrights(9, $mydo, 'patrol');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_patrolpos') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolpos') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $data[$k]['tid'], ':weid' => $mywe['weid']));
            $data[$k]['unit'] = $unit;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('patrolpos');
} elseif ($operation == 'patroladd') {
    $current = '新增巡更点';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'title' => $_GPC['title'], 'devicesn' => $_GPC['devicesn'], 'distance' => $_GPC['distance'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_patrolpos', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'patrol')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $mybuildingall = array();
    $myunit = array();
    $myunitall = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylocation[$regions[$m]['id']] = $locations;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
            $buildingall = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuildingall[$regions[$m]['id']] = $buildingall;
            $i = 0;
            while (!($i >= count($buildingall))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildingall[$i]['id']));
                $myunitall[$buildingall[$i]['id']] = $units;
                ($i += 1) + -1;
            }
            $n = 0;
            while (!($n >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$n]['id']));
                $mybuilding[$locations[$n]['id']] = $buildings;
                $j = 0;
                while (!($j >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$j]['id']));
                    $myunit[$buildings[$j]['id']] = $units;
                    ($j += 1) + -1;
                }
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('postpatrolpos');
} elseif ($operation == 'patroledit') {
    $current = '编辑巡更点';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'title' => $_GPC['title'], 'devicesn' => $_GPC['devicesn'], 'distance' => $_GPC['distance'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_patrolpos', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'patrol')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $mybuildingall = array();
    $myunit = array();
    $myunitall = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylocation[$regions[$m]['id']] = $locations;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
            $buildingall = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuildingall[$regions[$m]['id']] = $buildingall;
            $i = 0;
            while (!($i >= count($buildingall))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildingall[$i]['id']));
                $myunitall[$buildingall[$i]['id']] = $units;
                ($i += 1) + -1;
            }
            $n = 0;
            while (!($n >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$n]['id']));
                $mybuilding[$locations[$n]['id']] = $buildings;
                $j = 0;
                while (!($j >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$j]['id']));
                    $myunit[$buildings[$j]['id']] = $units;
                    ($j += 1) + -1;
                }
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolpos') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
    $elocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
    $eunits = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    include $this->mywtpl('postpatrolpos');
} elseif ($operation == 'delpatrol') {
    $current = '删除巡更点';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_patrolpos', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'patrolcheck') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_patrolpos') . ' WHERE weid = :weid and rid=:rid and title = :title ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], 'title' => $_GPC['title']));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_patrolpos') . ' WHERE weid = :weid and rid=:rid and  title = :title and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], 'rid' => $_GPC['rid'], ':title' => $_GPC['title'], ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '巡更点名称已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'patrollog') {
    $current = '巡更记录';
    $myret = 1;
    $rights = $this->myrights(9, $mydo, 'patrol');
    $id = intval($_GPC['id']);
    $condition .= ' and patid = :patid';
    $params[':patid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_patrollog') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_patrollog') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        load()->model('mc');
        $k = 0;
        while (!($k >= count($data))) {
            $fans = mc_fansinfo($data[$k]['cuid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['realname'] = $fans['nickname'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('patrollog');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_patrollog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
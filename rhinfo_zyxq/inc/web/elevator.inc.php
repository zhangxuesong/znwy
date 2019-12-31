<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'elevator';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconifg['qq_lbskey']) ? $sysconifg['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zyxq_elevator';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '梯控管理';
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '电梯列表';
    $myret = 0;
    $condition .= $this->myrcondition();
    if (!empty($_GPC['rid']) && !empty($_GPC['offline'])) {
        $condition .= ' and offline=1 and rid=' . $_GPC['rid'];
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $temp_locksn = '';
        $temp_isonline = '';
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
            $url = $this->my_mobileurl($this->createMobileUrl('elevator', array('op' => 'index', 'elevatorid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($url);
            if ($data[$k]['locksn'] == $temp_locksn) {
                $data[$k]['isonline'] = $temp_isonline;
            } else {
                $set = array();
                $set['url'] = '/doormaster/server/remote_control';
                $set['token'] = $region['thinmoo_token'];
                $set['op'] = 'GET';
                $dev_data = "{\r\n\t\t\t\t\t\"devsn_list\":[\"" . $data[$k]['locksn'] . "\"]\r\n\t\t\t\t  }";
                $res = thinmoo_http_post($set, $dev_data);
                if ($res['ret'] == '0') {
                    $door_data = $res['data'];
                    $lock_data = $door_data[$data[$k]['locksn']];
                    if ($lock_data['dev_status'] == 'online') {
                        $data[$k]['isonline'] = '在线';
                    } else {
                        $data[$k]['isonline'] = '离线';
                    }
                } else {
                    $data[$k]['isonline'] = '离线';
                }
                $temp_locksn = $data[$k]['locksn'];
                $temp_isonline = $data[$k]['isonline'];
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '电梯列表';
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetch($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            $location = pdo_fetchcolumn($sql, array(':id' => $data[$k]['lid'], ':weid' => $mywe['weid']));
            $data[$k]['location'] = $location;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $data[$k]['tid'], ':weid' => $mywe['weid']));
            $data[$k]['unit'] = $unit;
            $url = $this->my_mobileurl($this->createMobileUrl('elevator', array('op' => 'index', 'elevatorid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($url);
            $set = array();
            $set['url'] = '/doormaster/server/remote_control';
            $set['token'] = $region['thinmoo_token'];
            $set['op'] = 'GET';
            $dev_data = "{\r\n\t\t\t\t\t\"devsn_list\":[\"" . $data[$k]['locksn'] . "\"]\r\n\t\t\t\t  }";
            $res = thinmoo_http_post($set, $dev_data);
            if ($res['ret'] == '0') {
                $door_data = $res['data'];
                $lock_data = $door_data[$data[$k]['locksn']];
                if ($lock_data['dev_status'] == 'online') {
                    $data[$k]['isonline'] = '在线';
                } else {
                    $data[$k]['isonline'] = '离线';
                }
            } else {
                $data[$k]['isonline'] = '离线';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增梯控';
    if ($_W['ispost']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
        $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid']));
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'title' => $_GPC['title'], 'locksn' => $_GPC['locksn'], 'locktype' => $_GPC['locktype'], 'devtype' => $_GPC['devtype'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'sim' => $_GPC['sim'], 'simdate' => strtotime($_GPC['simdate']), 'remark' => $_GPC['remark'], 'relationfee' => $_GPC['relationfee'], 'status' => $_GPC['status'], 'range' => $_GPC['doorrange'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $msg = '';
        if ($_GPC['locktype'] == 6) {
            $sql = 'select door_pid from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id = :pid';
            $door_pid = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
            $sql = 'select thinmoo_token from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
            $thinmoo_token = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['rid']));
            $set = array();
            $set['url'] = '/doormaster/server/devices';
            $set['token'] = $thinmoo_token;
            $set['op'] = 'POST';
            $data = "{\r\n\t\t\t\t\t \"dev_name\": \"" . $doortitle . "\",\r\n\t\t\t\t\t \"dev_sn\": \"" . $_GPC['locksn'] . "\", \r\n\t\t\t\t\t \"door_no\": 0,\r\n\t\t\t\t\t \"area_id\": " . $door_pid . "\r\n\t\t\t\t  }";
            $res = thinmoo_http_post($set, $data);
            if ($res['ret'] == '0') {
                $msg = '梯控设备添加成功';
            } else {
                $msg = '梯控设备添加失败';
            }
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id . $msg);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $myunit = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid ';
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            $n = 0;
            while (!($n >= count($buildings))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$n]['id']));
                $myunit[$buildings[$n]['id']] = $units;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑梯控';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
        $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid']));
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'title' => $_GPC['title'], 'locksn' => $_GPC['locksn'], 'locktype' => $_GPC['locktype'], 'devtype' => $_GPC['devtype'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'sim' => $_GPC['sim'], 'simdate' => strtotime($_GPC['simdate']), 'relationfee' => $_GPC['relationfee'], 'status' => $_GPC['status'], 'range' => $_GPC['range'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $msg = '';
        if ($_GPC['locktype'] == 6) {
            $sql = 'select door_pid from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id = :pid';
            $door_pid = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
            $sql = 'select thinmoo_token from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
            $thinmoo_token = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
            $set = array();
            $set['url'] = '/doormaster/server/devices';
            $set['token'] = $thinmoo_token;
            $set['op'] = 'PUT';
            $data = "{\r\n\t\t\t\t\t \"dev_name\": \"" . $doortitle . "\",\r\n\t\t\t\t\t \"dev_sn\": \"" . $_GPC['locksn'] . "\", \r\n\t\t\t\t\t \"door_no\": 0,\r\n\t\t\t\t\t \"area_id\": " . $door_pid . "\r\n\t\t\t\t  }";
            $res = thinmoo_http_post($set, $data);
            if ($res['ret'] == '0') {
                $msg = '梯控设备修改成功';
            } else {
                $msg = '梯控设备修改失败';
            }
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id . $msg);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $myunit = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            $n = 0;
            while (!($n >= count($buildings))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$n]['id']));
                $myunit[$buildings[$n]['id']] = $units;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
    $eunits = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除梯控';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $msg = '';
    if ($item['locktype'] == 6) {
        $sql = 'select door_pid from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id = :pid';
        $door_pid = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
        $sql = 'select thinmoo_token from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $thinmoo_token = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
        $set = array();
        $set['url'] = '/doormaster/server/devices';
        $set['token'] = $thinmoo_token;
        $set['op'] = 'DELETE';
        $data = "{\r\n\t\t\t\t\"sns\":[\"" . $item['locksn'] . "\"]\r\n\t\t\t  }";
        $res = thinmoo_http_post($set, $data);
        if ($res['ret'] == '0') {
            $msg = '梯控设备删除成功';
        } else {
            $msg = '梯控设备删除失败';
        }
    }
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id . $msg);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and locksn = :locksn ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':locksn' => $_GPC['locksn']));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and locksn = :locksn and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':locksn' => $_GPC['locksn'], ':id' => $_GPC['id']));
        }
        if ($count > 0 && $this->syscfg['doortype'] !== 2) {
            echo '设备序列号已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'openlog') {
    $current = '梯控记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and eid = :eid';
    $params[':eid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_elevatorlog') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select o.*,d.rid from ' . tablename('rhinfo_zyxq_elevatorlog') . ' as o left join ' . tablename('rhinfo_zyxq_door') . ' as d on o.did = d.id where o.weid=:weid and d.id=:id ' . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':id' => $id));
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':uid' => $data[$k]['uid']));
            $fans = mc_fansinfo($member['uid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['realname'] = $member['realname'];
            $data[$k]['address'] = $member['address'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('openlog');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_elevatorlog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
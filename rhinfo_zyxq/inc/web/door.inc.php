<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'door';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($this->syscfg['qq_lbskey']) ? $this->syscfg['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zyxq_door';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '门禁管理';
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '门禁列表';
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
        myload()->classs('thinmoo');
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
            $url = $this->my_mobileurl($this->createMobileUrl('opendoor', array('op' => 'scanopen', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($url);
            if ($data[$k]['locksn'] == $temp_locksn) {
                $data[$k]['isonline'] = $temp_isonline;
            } else {
                if ($data[$k]['devtype'] == 1) {
                    $data[$k]['isonline'] = '在线';
                } elseif ($data[$k]['devtype'] == 2 || $data[$k]['devtype'] == 3) {
                    if ($data[$k]['doortype'] == 5) {
                        $data[$k]['isonline'] = '在线';
                    } elseif ($data[$k]['doortype'] == 6) {
                        array('thinmoo_token' => $region['thinmoo_token']);
                        $thinmoo = 'ThinMoo';
                        $dev_data = "{\r\n\t\t\t\t\t\t\t\"devsn_list\":[\"" . $data[$k]['locksn'] . "\"]\r\n\t\t\t\t\t\t  }";
                        $res = $thinmoo->dev_status($dev_data);
                        if (is_error($res)) {
                            $data[$k]['isonline'] = '离线';
                        } else {
                            $door_data = $res['data'];
                            $lock_data = $door_data[$data[$k]['locksn']];
                            if ($lock_data['dev_status'] == 'online') {
                                $data[$k]['isonline'] = '在线';
                            } else {
                                $data[$k]['isonline'] = '离线';
                            }
                        }
                    } elseif ($data[$k]['doortype'] == 7) {
                        $device_arr = iunserializer($data[$k]['device_json']);
                        $set = array('url' => 'community/' . $region['aurine_rid'] . '/device/' . $device_arr['divice_id'] . '/data', 'aurine_appid' => $region['aurine_appid'], 'aurine_secret' => $region['aurine_secret'], 'aurine_token' => $region['aurine_token']);
                        $res = aurine_http_post($set);
                        if ($res['errorCode'] == 1 && $res['body']['status'] == 2) {
                            $data[$k]['isonline'] = '在线';
                        } else {
                            $data[$k]['isonline'] = '离线';
                        }
                    } else {
                        $res = $this->devstatus($data[$k]['doortype'], $data[$k]['locksn']);
                        if ($res['code'] == '0') {
                            $data[$k]['isonline'] = '在线';
                        } else {
                            $data[$k]['isonline'] = '离线';
                        }
                    }
                } else {
                    $res = $this->devstatus($data[$k]['doortype'], $data[$k]['locksn']);
                    if ($res['code'] == '0') {
                        $data[$k]['isonline'] = '在线';
                    } else {
                        $data[$k]['isonline'] = '离线';
                    }
                }
                $temp_locksn = $data[$k]['locksn'];
                $temp_isonline = $data[$k]['isonline'];
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增门禁';
    if ($_W['ispost']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
        $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid']));
        if (empty($_GPC['title'])) {
            $doortitle = $building . '-' . $unit . '门';
        } else {
            $doortitle = $_GPC['title'];
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'title' => $doortitle, 'locksn' => $_GPC['locksn'], 'lockid' => $_GPC['lockid'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'sim' => $_GPC['sim'], 'simdate' => strtotime($_GPC['simdate']), 'remark' => $_GPC['remark'], 'relationfee' => $_GPC['relationfee'], 'status' => $_GPC['status'], 'doorcate' => $_GPC['doorcate'], 'doorrange' => $_GPC['doorrange'], 'devtype' => $_GPC['devtype'], 'doortype' => $_GPC['doortype'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        if ($_GPC['regdevice'] == 1) {
            $region = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $_GPC['rid']), array('title', 'aurine_rid', 'aurine_appid', 'aurine_secret', 'aurine_secret', 'thinmoo_token', 'thinmoo_uuid'));
            if ($_GPC['doortype'] == 3) {
                $url = 'postlock.html?appid=' . $this->syscfg['mj_apiid'] . '&appsecret=' . $this->syscfg['mj_apikey'];
                $res = wmj_httpPost($url, $_GPC['locksn']);
                if (!($res['state'] == '1' && $res['state_code'] == '1')) {
                    exit('无法添加门禁，请联系厂商');
                }
            }
            if ($_GPC['doortype'] == 4) {
                $url = 'AccessControl02/Api.php';
                $post_data = array('action' => 'registered', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $_GPC['locksn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $data['mxdevfid'] = $rs[1];
                } else {
                    exit('操作失败' . $rs[1]);
                }
            }
            if ($_GPC['doortype'] == 6) {
                myload()->classs('thinmoo');
                $region;
                $thinmoo = 'ThinMoo';
                if ($_GPC['devtype'] == 1) {
                    $post_data = "{\r\n\t\t\t\t\t\t\t \"dev_name\": \"" . $doortitle . "\",\r\n\t\t\t\t\t\t\t \"dev_sn\": \"" . $_GPC['locksn'] . "\"\r\n\t\t\t\t\t\t  }";
                    $res = $thinmoo->add_door($post_data, 'devices');
                    if (is_error($res)) {
                        exit($res['message']);
                    }
                }
                if ($_GPC['devtype'] == 2 || $_GPC['devtype'] == 3) {
                    $post_data = "{\r\n\t\t\t\t\t\t\t \"dev_name\": \"" . $doortitle . "\",\r\n\t\t\t\t\t\t\t \"dev_sn\": \"" . $_GPC['locksn'] . "\", \r\n\t\t\t\t\t\t\t \"dev_type\": 2,\r\n\t\t\t\t\t\t\t \"area_uuid\":" . $_GPC['rid'] . "\r\n\t\t\t\t\t\t  }";
                    $res = $thinmoo->add_door($post_data);
                    if (is_error($res)) {
                        exit($res['message']);
                    }
                }
            }
            if ($_GPC['doortype'] == 7) {
                $set = array('url' => 'community/' . $region['aurine_rid'] . '/device/add', 'aurine_appid' => $region['aurine_appid'], 'aurine_secret' => $region['aurine_secret'], 'aurine_token' => $region['aurine_token']);
                $device_arr = $_GPC['device_arr'];
                $data = array('name' => $doortitle, 'device_code' => $device_arr['device_code'], 'intercom_type' => $device_arr['intercom_type'], 'device_type' => $device_arr['device_type'], 'serial_no' => $_GPC['locksn']);
                $res = aurine_http_post_json($set, $data);
                if ($res['errorCode'] == 1) {
                    $device_arr['device_id'] = $res['body']['id'];
                    $device_arr['pin'] = $res['body']['pin'];
                    $data['device_json'] = iserializer($device_arr);
                } else {
                    exit('操作失败' . $res['errorMsg']);
                }
            }
        }
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit('ok');
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
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑门禁';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
        $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid']));
        if (empty($_GPC['title'])) {
            $doortitle = $building . '-' . $unit . '门';
        } else {
            $doortitle = $_GPC['title'];
        }
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'title' => $doortitle, 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'sim' => $_GPC['sim'], 'simdate' => strtotime($_GPC['simdate']), 'relationfee' => $_GPC['relationfee'], 'status' => $_GPC['status'], 'doorcate' => $_GPC['doorcate'], 'doorrange' => $_GPC['doorrange'], 'devtype' => $_GPC['devtype'], 'doortype' => $_GPC['doortype'], 'remark' => $_GPC['remark']);
        if ($_GPC['doortype'] == 2) {
            $data['lockid'] = $_GPC['lockid'];
        }
        if ($_GPC['doortype'] != 3) {
            $data['locksn'] = $_GPC['locksn'];
        }
        if ($_GPC['doortype'] == 7) {
            $data['device_json'] = iserializer($_GPC['device_arr']);
        }
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit('ok');
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
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $item['device_arr'] = iunserializer($item['device_json']);
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
    $elocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid=:lid';
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
    $eunits = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除门禁';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($item['doortype'] == 3) {
        $url = 'dellock.html?appid=' . $this->syscfg['mj_apiid'] . '&appsecret=' . $this->syscfg['mj_apikey'];
        $res = wmj_httpPost($url, $item['locksn']);
        if (!($res['state'] == '1' && $res['state_code'] == '1')) {
            echo '删除失败';
        }
    }
    if ($item['doortype'] == 7) {
        $region = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $item['rid']), array('aurine_rid', 'aurine_appid', 'aurine_secret', 'aurine_secret'));
        $set = array('url' => 'community/' . $region['aurine_rid'] . '/device/del', 'aurine_appid' => $region['aurine_appid'], 'aurine_secret' => $region['aurine_secret'], 'aurine_token' => $region['aurine_token']);
        $device_arr = iunserializer($item['device_json']);
        $data = array('device_id' => $device_arr['device_id']);
        $res = aurine_http_post($set, $data);
        if (!($res['errorCode'] == 1)) {
            echo '删除失败';
        }
    }
    if ($item['doortype'] == 6) {
        $region = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $item['rid']), array('thinmoo_token'));
        myload()->classs('thinmoo');
        $region;
        $thinmoo = 'ThinMoo';
        if ($item['devtype'] == 1) {
            $post_data = "{\r\n\t\t\t\t\t\"sns\":[\"" . $item['locksn'] . "\"]\r\n\t\t\t\t  }";
            $res = $thinmoo->delete_door($post_data, 'devices');
            if (is_error($res)) {
                exit($res['message']);
            }
        }
        if ($item['devtype'] == 2 || $item['devtype'] == 3) {
            $post_data = "{\r\n\t\t\t\t\t\"sns\":[\"" . $item['locksn'] . "\"]\r\n\t\t\t\t  }";
            $res = $thinmoo->delete_door($post_data);
            if (is_error($res)) {
                exit($res['message']);
            }
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
            echo '门锁序列号已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'openlog') {
    $current = '开门记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and did = :did';
    $params[':did'] = $id;
    $opentime = $_GPC['opentime'];
    if (!empty($opentime)) {
        $starttime = strtotime($opentime['start']);
        $endtime = strtotime($opentime['end'] . ' 23:59:59');
        $condition .= ' and opentime>=' . $starttime . ' and opentime<=' . $endtime;
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and id=:id';
    $door = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    $current = $door['title'] . $current;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_doorlog') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_doorlog') . ' where ' . $condition . ' order by opentime desc ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $door['rid'], ':uid' => $data[$k]['uid']));
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
    include $this->mywtpl('openlog');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_doorlog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'faces') {
    $current = '人脸照片';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and did = :did';
    $params[':did'] = $id;
    $ctime = $_GPC['ctime'];
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
            $condition .= ' and uid=:uid ';
            $params[':uid'] = $uid;
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and id=:id';
    $door = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    $current = $door['title'] . $current;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_door_faces') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_door_faces') . ' where ' . $condition . ' order by ctime desc ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $door['rid'], ':uid' => $data[$k]['uid']));
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
    include $this->mywtpl('faces');
} elseif ($operation == 'delface') {
    $current = '删除人脸';
    $id = intval($_GPC['id']);
    $sql = 'select b.*,a.faceimg,a.status,a.uid,a.userid from ' . tablename('rhinfo_zyxq_door_faces') . ' as a left join ' . tablename('rhinfo_zyxq_door') . ' as b on a.did=b.id where a.weid=:weid and a.id=:id';
    $door = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    if ($door['devtype'] == 3 && $door['status'] == 1) {
        $region = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $door['rid']), array('mailin_appid', 'mailin_secret', 'mailin_token', 'thinmoo_token'));
        if ($door['doortype'] == 2) {
            $post_data = array('apiid' => $this->syscfg['bl_apiid'], 'apikey' => $this->syscfg['bl_apikey']);
            $res = Park_GetToken($this->syscfg['siteurl'], $post_data);
            if (!empty($res['access_token'])) {
                $member = pdo_get('mc_members', array('uniacid' => $mywe['weid'], 'uid' => $door['uid']), array('mobile'));
                $data = array('token' => $res['access_token'], 'typeid' => 301, 'tel' => $member['mobile'], 'devid' => $door['locksn']);
                $ret = Park_httpPost_face($this->syscfg['siteurl'], $data);
                if ($ret['code'] == 1) {
                    $data['typeid'] = 204;
                    $ret = Park_httpPost_face($this->syscfg['siteurl'], $data);
                }
            }
        } elseif ($door['doortype'] == 5) {
            $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
            $faceimg = IA_ROOT . '/attachment/' . $door['faceimg'];
            $post_data = array('m' => 'do', 'f' => 'face', 'a' => 'face_delete', 'face_img' => $faceimg, 'userId' => $door['userid'], 'device_sncode' => $door['locksn']);
            $res = mailin_http_post_face($set, $post_data);
            if (!($res['state'] == 1)) {
                echo '删除失败!' . $res['return_data'];
                exit(0);
            }
        } elseif ($door['doortype'] == 6) {
            myload()->classs('thinmoo');
            $region;
            $thinmoo = 'ThinMoo';
            $data = "{\r\n\t\t\t\t\t\"emp_id\":\"" . $door['userid'] . "\",\r\n\t\t\t\t\t\"index\":1,\r\n\t\t\t\t\t\"type\":2\r\n\t\t\t\t  }";
            $res = $thinmoo->delete_face($data);
            if (is_error($res)) {
                exit($res['message']);
            }
        }
    }
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_door_faces', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'regface') {
    $current = '注册人脸图像';
    $doorid = intval($_GPC['doorid']);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where status=1 and weid = :weid and id=:id ';
    $door = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $doorid));
    if ($_W['ispost']) {
        $sql = 'select doorlock_type,thinmoo_token,mailin_appid,mailin_secret,mailin_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $door['rid']));
        if (!empty($_FILES['upfile']['name'])) {
            $res = $this->myupload($_FILES['upfile']);
            if (is_error($res)) {
                exit($res['message']);
            } else {
                $fileimg = $res['filename'];
                $faceimg = IA_ROOT . '/attachment/' . $fileimg;
            }
        } else {
            exit('文件不能为空');
        }
        $member = pdo_get('mc_members', array('mobile' => $_GPC['mobile']), array('uid'));
        $member_id = !empty($member['uid']) ? $member['uid'] : $_GPC['mobile'];
        if ($door['doortype'] == 2) {
            $faceimg = mybase64EncodeImage($faceimg);
            $post_data = array('apiid' => $this->syscfg['bl_apiid'], 'apikey' => $this->syscfg['bl_apikey']);
            $res = Park_GetToken($this->syscfg['siteurl'], $post_data);
            if (!empty($res['access_token'])) {
                $data = array('token' => $res['access_token'], 'typeid' => 100, 'nickname' => $_GPC['nickname'], 'tel' => $_GPC['mobile']);
                $ret = Park_httpPost_face($this->syscfg['siteurl'], $data);
                if ($ret['code'] == '1' || $ret['code'] == '100102') {
                    $data = array('token' => $res['access_token'], 'typeid' => 200, 'tel' => $_GPC['mobile'], 'devid' => $door['locksn'], 'lockid' => '01', 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59');
                    $ret1 = Park_httpPost_face($this->syscfg['siteurl'], $data);
                    if ($ret1['code'] == '1') {
                        $data = array('token' => $res['access_token'], 'typeid' => 300, 'tel' => $_GPC['mobile'], 'devid' => $door['locksn'], 'filedata' => $faceimg);
                        $result = Park_httpPost_face($this->syscfg['siteurl'], $data);
                        if ($result['code'] == '1') {
                            $member = pdo_get('mc_members', array('mobile' => $_GPC['mobile']), array('uid'));
                            $data = array('weid' => $mywe['weid'], 'did' => $doorid, 'uid' => $member['uid'], 'mobile' => $_GPC['mobile'], 'nickname' => $_GPC['nickname'], 'faceimg' => $fileimg, 'startdate' => date('Y-m-d', TIMESTAMP), 'starttime' => '0:00', 'enddate' => date('Y-m-d', strtotime('+3 years')), 'endtime' => '23:59', 'status' => 1, 'ctime' => TIMESTAMP, 'cuid' => 0);
                            pdo_insert('rhinfo_zyxq_door_faces', $data);
                            exit('ok');
                        } else {
                            exit($result['code'] . $result['msg']);
                        }
                    } else {
                        exit($ret1['code'] . $ret1['msg']);
                    }
                } else {
                    exit($ret['code'] . $ret['msg']);
                }
            } else {
                exit('获取TOKEN失败');
            }
        } elseif ($door['doortype'] == 5) {
            $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
            $post_data = array('m' => 'do', 'f' => 'face', 'a' => 'face_reg', 'face_img' => $faceimg, 'member_id' => $member_id, 'true_name' => $_GPC['nickname'], 'device_sncode' => $door['locksn']);
            $res = mailin_http_post_face($set, $post_data);
            if ($res['state'] == 1) {
                $data = array('weid' => $mywe['weid'], 'did' => $doorid, 'uid' => $member['uid'], 'mobile' => $_GPC['mobile'], 'nickname' => $_GPC['nickname'], 'faceimg' => $fileimg, 'status' => 1, 'userid' => $res['return_data']['userId'], 'ctime' => TIMESTAMP, 'cuid' => 0);
                pdo_insert('rhinfo_zyxq_door_faces', $data);
                exit('ok');
            } else {
                exit($res['return_data']);
            }
        } elseif ($door['doortype'] == 6) {
            $faceimg = mybase64EncodeImage($faceimg, false);
            myload()->classs('thinmoo');
            $region;
            $thinmoo = 'ThinMoo';
            $post_data = "{\r\n\t\t\t\t\t \"emp_uuid\": \"" . $member_id . "\",\r\n\t\t\t\t\t \"emp_name\": \"" . $_GPC['nickname'] . "\"\r\n\t\t\t\t  }";
            $ret = $thinmoo->add_user($post_data);
            if (is_error($ret)) {
                exit($ret['message']);
            }
            $post_data = "{\r\n\t\t\t\t\t \"emp_id\": \"" . $ret['id'] . "\",\r\n\t\t\t\t\t \"face_image\": \"" . $faceimg . "\",\r\n\t\t\t\t\t \"index\":1,\r\n\t\t\t\t\t \"type\":2\r\n\t\t\t\t  }";
            $res = $thinmoo->reg_face($post_data);
            if (is_error($res)) {
                exit($res['message']);
            }
            $data = array('weid' => $mywe['weid'], 'did' => $doorid, 'uid' => $member['uid'], 'mobile' => $_GPC['mobile'], 'nickname' => $_GPC['nickname'], 'faceimg' => $fileimg, 'status' => 1, 'userid' => $ret['id'], 'ctime' => TIMESTAMP, 'cuid' => 0);
            $post_data = "{\r\n\t\t\t\t\t \"dev_sn\":\"" . $item['locksn'] . "\",\r\n\t\t\t\t\t \"emp_group\":[" . $ret['id'] . "]\r\n\t\t\t\t  }";
            $resp = $thinmoo->add_perm($post_data);
            if (is_error($resp)) {
                exit($resp['message']);
            }
            pdo_insert('rhinfo_zyxq_door_faces', $data);
            exit('ok');
        } else {
            exit('该设备不支持');
        }
    }
    include $this->mywtpl('regface');
}
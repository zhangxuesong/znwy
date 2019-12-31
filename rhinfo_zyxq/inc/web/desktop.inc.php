<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
date_default_timezone_set('Asia/Shanghai');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$this->my_check_web();
$mywe = $this->mywe;
if ($_W['ishaina'] == 1 && !empty($mywe['pid']) && $this->syspub['ishaina'] == 1) {
    $property = pdo_get('rhinfo_zyxq_property', array('weid' => $mywe['weid'], 'id' => $mywe['pid']), array('haina_property_id'));
    if (!empty($property)) {
        myload()->classs('haina');
        $this->syspub;
        $haina = 'HaiNa';
        $haina_params = array('property_id' => $property['haina_property_id']);
        $res = $haina->getCommunitys($haina_params);
        if (!is_error($res)) {
            $resData = $res['data'];
            $i = 0;
            while (!($i >= count($resData))) {
                $is_exist = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'haina_community_id' => $resData[$i]['community_id']), array('haina_community_id'));
                if (empty($is_exist)) {
                    $haina_data = array('weid' => $mywe['weid'], 'pid' => $mywe['pid'], 'category' => 1, 'title' => $resData[$i]['community_name'], 'province' => $resData[$i]['province'], 'city' => $resData[$i]['city'], 'district' => $resData[$i]['district'], 'address' => $resData[$i]['community_address'], 'lng' => $resData[$i]['longitude'], 'lat' => $resData[$i]['latitude'], 'haina_community_id' => $resData[$i]['community_id']);
                    pdo_insert('rhinfo_zyxq_region', $haina_data);
                }
                ($i += 1) + -1;
            }
        }
    }
}
if ($operation == 'doorcheck') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and pid=:pid and rid=:rid';
    $list = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
    $sql = 'select doorlock_type,thinmoo_token from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $_GPC['rid'], ':weid' => $mywe['weid']));
    $i = 0;
    $res = array('code' => 0);
    $k = 0;
    while (!($k >= count($list))) {
        if ($list[$k]['locksn'] == $temp_locksn) {
            $res['code'] = $temp_code;
        } elseif (!($list[$k]['devtype'] == 1)) {
            if ($list[$k]['devtype'] == 2 || $list[$k]['devtype'] == 3) {
                if ($list[$k]['doortype'] == 2) {
                    $res = $this->devstatus($list[$k]['doortype'], $list[$k]['locksn']);
                } elseif ($list[$k]['doortype'] == 5) {
                    $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
                    $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_net_open_key', 'device_sncode' => $list[$k]['locksn']);
                    $result = mailin_http_post($set, $post_data);
                    if ($result['state'] == 1) {
                        $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'net_open', 'device_sncode' => $list[$k]['locksn'], 'net_open_key' => $result['return_data']['net_open_key']);
                        $ret = mailin_http_post($set, $post_data);
                        if ($return['state'] == 1) {
                            $res['code'] = '0';
                            $res['msg'] = '开门成功';
                        } else {
                            $res['code'] = '1';
                            $res['msg'] = $return['state'] . '-' . $return['return'];
                        }
                    } else {
                        $res['code'] = '1';
                        $res['msg'] = $result['state'] . '-' . $result['return_data'];
                    }
                } elseif ($list[$k]['doortype'] == 6) {
                    $set = array();
                    $set['url'] = '/doormaster/server/remote_control';
                    $set['token'] = $region['thinmoo_token'];
                    $set['op'] = 'GET';
                    $dev_data = "{\r\n\t\t\t\t\t\t\t\"devsn_list\":[\"" . $list[$k]['locksn'] . "\"]\r\n\t\t\t\t\t\t  }";
                    $result = thinmoo_http_post($set, $dev_data);
                    if ($result['ret'] == '0') {
                        $door_data = $result['data'];
                        $lock_data = $door_data[$list[$k]['locksn']];
                        if ($lock_data['dev_status'] == 'online') {
                            $res['code'] = '0';
                        } else {
                            $res['code'] = '1';
                        }
                    } else {
                        $res['code'] = '1';
                    }
                }
            } else {
                $res = $this->devstatus($list[$k]['doortype'], $list[$k]['locksn']);
            }
        }
        if ($res['code'] == '0') {
            pdo_update('rhinfo_zyxq_door', array('offline' => 0), array('weid' => $mywe['weid'], 'id' => $list[$k]['id']));
        } else {
            pdo_update('rhinfo_zyxq_door', array('offline' => 1), array('weid' => $mywe['weid'], 'id' => $list[$k]['id']));
            ($i += 1) + -1;
        }
        $temp_locksn = $list[$k]['locksn'];
        $temp_code = $res['code'];
        ($k += 1) + -1;
    }
    echo $i;
    exit(0);
}
if ($operation == 'doortest') {
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and rid=:rid';
    $list = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    $i = 0;
    $k = 0;
    while (!($k >= count($list))) {
        $res = $this->scan_opendoor($_GPC['rid'], $list[$k]['devtype'], $list[$k]['doortype'], $list[$k]['locksn'], $list[$k]['lockid'], 'opendoor', $list[$k]['id'], 1);
        if ($res['code'] == '0') {
            ($i += 1) + -1;
        }
        ($k += 1) + -1;
    }
    echo $i;
    exit(0);
}
if ($operation == 'billlist') {
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    if (!empty($region)) {
        $houses = array(array('text' => '楼宇', 'color' => '#d9534f', 'level' => 0, 'category' => 1), array('text' => '商铺', 'color' => '#428bca', 'level' => 0, 'category' => 2), array('text' => '车位', 'color' => '#5cb85c', 'level' => 0, 'category' => 3), array('text' => '储物间', 'color' => '#f0ad4e', 'level' => 0, 'category' => 4));
        $sql = 'select title as text ,id, 1 as level,1 as category from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $k = 0;
        while (!($k >= count($buildings))) {
            $sql = 'select title as text ,id ,2 as level,1 as category from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
            $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id']));
            $n = 0;
            while (!($n >= count($units))) {
                $sql = 'select CONCAT_WS(\' \',title,ownername) as text ,id , 3 as level,1 as category from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid';
                $rooms = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id'], ':tid' => $units[$n]['id']));
                $units[$n]['nodes'] = $rooms;
                ($n += 1) + -1;
            }
            $buildings[$k]['nodes'] = $units;
            ($k += 1) + -1;
        }
        $houses[0]['nodes'] = $buildings;
        $sql = 'select title as text ,id, 1 as level,2 as category from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and rid = :rid and category=1';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $k = 0;
        while (!($k >= count($buildings))) {
            $sql = 'select CONCAT_WS(\' \',title,ownername) as text ,id ,2 as level,2 as category from ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and rid = :rid and lid = :bid';
            $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id']));
            $buildings[$k]['nodes'] = $units;
            ($k += 1) + -1;
        }
        $houses[1]['nodes'] = $buildings;
        $sql = 'select title as text ,id, 1 as level,3 as category from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and rid = :rid and category=2';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $k = 0;
        while (!($k >= count($buildings))) {
            $sql = 'select CONCAT_WS(\' \',title,ownername) as text ,id ,2 as level,3 as category from ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and rid = :rid and lid = :bid and category=1';
            $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id']));
            $buildings[$k]['nodes'] = $units;
            ($k += 1) + -1;
        }
        $houses[2]['nodes'] = $buildings;
        $sql = 'select title as text ,id, 1 as level,4 as category from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid and isbarn=1';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $k = 0;
        while (!($k >= count($buildings))) {
            $sql = 'select CONCAT_WS(\' \',title,ownername) as text ,id ,2 as level,4 as category from ' . tablename('rhinfo_zyxq_garage') . ' where weid = :weid and rid = :rid and bid = :bid';
            $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $buildings[$k]['id']));
            $buildings[$k]['nodes'] = $units;
            ($k += 1) + -1;
        }
        $houses[3]['nodes'] = $buildings;
    }
    include $this->mywtpl('billlist');
    exit(0);
}
if ($_W['isfounder']) {
    if ($_SERVER['HTTP_HOST'] !== '127.0.0.1') {
        if (file_exists(IA_ROOT . '/addons/rhinfo_zyxq/update.php')) {
            require_once IA_ROOT . '/addons/rhinfo_zyxq/update.php';
            file_delete(IA_ROOT . '/addons/rhinfo_zyxq/update.php');
        }
        if (file_exists(IA_ROOT . '/addons/rhinfo_zyxq/mywebapp.php')) {
            if (file_exists(IA_ROOT . '/addons/rhinfo_zyxq/webapp.php')) {
                file_delete(IA_ROOT . '/addons/rhinfo_zyxq/webapp.php');
                reName(IA_ROOT . '/addons/rhinfo_zyxq/mywebapp.php', IA_ROOT . '/addons/rhinfo_zyxq/webapp.php');
            } else {
                reName(IA_ROOT . '/addons/rhinfo_zyxq/mywebapp.php', IA_ROOT . '/addons/rhinfo_zyxq/webapp.php');
            }
        }
        if (file_exists(IA_ROOT . '/addons/rhinfo_zyxq/template/mywebapp')) {
            require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/rhinfo/fileutil.php';
            $fu = new FileUtil();
            $fu->moveDir(IA_ROOT . '/addons/rhinfo_zyxq/template/mywebapp', IA_ROOT . '/addons/rhinfo_zyxq/template/webapp', true);
        }
        $property_admin_file = MODULE_ROOT . '/property.php';
        if (is_file($property_admin_file)) {
            file_move($property_admin_file, IA_ROOT . '/property.php');
        }
        $miniapp_file = IA_ROOT . '/addons/rhinfo_zyxq/miniapi.php';
        if (file_exists($miniapp_file)) {
            file_move($miniapp_file, IA_ROOT . '/app/miniapi.php');
        }
        $wuye_admin_file = MODULE_ROOT . '/mywuye.php';
        if (is_file($wuye_admin_file)) {
            file_move($wuye_admin_file, IA_ROOT . '/web/property.php');
        }
        $miniwxapp_file = IA_ROOT . '/addons/rhinfo_zyxq/miniwxapp.php';
        if (file_exists($miniwxapp_file)) {
            file_move($miniwxapp_file, IA_ROOT . '/app/miniwxapp.php');
        }
        $aliapi_file = IA_ROOT . '/addons/rhinfo_zyxq/aliapi.php';
        if (file_exists($aliapi_file)) {
            file_move($aliapi_file, IA_ROOT . '/aliapi.php');
        }
        $hnapi_file = IA_ROOT . '/addons/rhinfo_zyxq/hnapi.php';
        if (file_exists($hnapi_file)) {
            file_move($hnapi_file, IA_ROOT . '/hnapi.php');
        }
        $haina_file = IA_ROOT . '/addons/rhinfo_zyxq/haina.php';
        if (file_exists($haina_file)) {
            file_move($haina_file, IA_ROOT . '/app/haina.php');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_version') . ' limit 1';
    $version = pdo_fetch($sql);
    if (empty($version)) {
        $siteip = gethostbyname($_SERVER['HTTP_HOST']);
        pdo_insert('rhinfo_zyxq_version', array('version' => '0.1', 'siteurl' => $_W['siteroot'], 'siteip' => $siteip));
    }
}
$navtitle = '我的桌面';
$mydo = 'desktop';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$company = $this->syscfg;
$company['title'] = empty($company['title']) ? $this->syspub['title'] : $company['title'];
if ($operation == 'nomenu') {
    include $this->mywtpl('index1');
    exit(0);
}
$pindex = max(1, intval($_GPC['page']));
$psize = 1;
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
if (!empty($_W['uid'])) {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid = :weid and uid = :uid';
    $user = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':uid' => $_W['uid']));
    if ($user['gid'] == 0) {
        if (!empty($user['pid'])) {
            $condition .= ' and pid = ' . $user['pid'];
        }
        $rcondition = $condition;
    } elseif ($user['rid']) {
        $rcondition = $condition . ' and id = ' . $user['rid'];
        $condition .= ' and rid = ' . $user['rid'];
    } else {
        if (!empty($mywe['pid'])) {
            $condition .= ' and pid = ' . $mywe['pid'];
        }
        $rcondition = $condition;
    }
    if (!empty($user['pid'])) {
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id=:pid';
        $propertys = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $user['pid']));
    } else {
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid';
        $propertys = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
    }
} else {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid = :weid and id = :uid';
    $user = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':uid' => $mywe['uid']));
    if ($user['gid'] == 0) {
        $condition .= ' and pid = ' . $mywe['pid'];
        $rcondition = $condition;
    } elseif ($user['rid']) {
        $rcondition = $condition . ' and id = ' . $user['rid'];
        $condition .= ' and rid = ' . $user['rid'];
    } else {
        $condition .= ' and pid = ' . $mywe['pid'];
        $rcondition = $condition;
    }
}
if (!empty($_GPC['rid'])) {
    $rcondition .= ' and id = ' . $_GPC['rid'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
    $myregion = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id=:pid';
    $myproperty = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $myregion['pid']));
}
if ($operation == 'region') {
    if (!empty($_GPC['keyword'])) {
        $rcondition .= ' and title like \'%' . $_GPC['keyword'] . '%\' ';
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition . " ORDER BY\r\n\t\t\t\t `PID` ASC ";
    $myregions = pdo_fetchall($sql, $params);
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id=:pid';
        $myregions[$k]['property'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid']));
        ($k += 1) + -1;
    }
    include $this->mywtpl('regions');
    exit(0);
}
$sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
$total = pdo_fetchcolumn($sql, $params);
$sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_region') . ' where category = 1 and ' . $rcondition;
$regions = pdo_fetchcolumn($sql, $params);
$sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_region') . ' where category = 2 and ' . $rcondition;
$businesss = pdo_fetchcolumn($sql, $params);
$sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_region') . ' where category = 3 and ' . $rcondition;
$gardens = pdo_fetchcolumn($sql, $params);
$sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_region') . ' where category = 4 and ' . $rcondition;
$markets = pdo_fetchcolumn($sql, $params);
$sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_region') . ' where category = 5 and ' . $rcondition;
$apartments = pdo_fetchcolumn($sql, $params);
if ($total > 0) {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition . " ORDER BY\r\n\t\t\t\t `ID` ASC " . $limit;
    $data = pdo_fetchall($sql, $params);
    $k = 0;
    while (!($k >= count($data))) {
        $condition .= ' and pid = :pid and rid = :rid';
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_building') . ' where ' . $condition;
        $params[':pid'] = $data[$k]['pid'];
        $params[':rid'] = $data[$k]['id'];
        $data[$k]['buildings'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition;
        $data[$k]['rooms'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition;
        $data[$k]['shops'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_parking') . ' where ' . $condition;
        $data[$k]['parkings'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_garage') . ' where ' . $condition;
        $data[$k]['garages'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_door') . ' where ' . $condition;
        $data[$k]['doors'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_door') . ' where offline=1 and ' . $condition;
        $data[$k]['offline'] = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_building') . ' where ' . $condition . ' ORDER BY title,id ASC ';
        $building_list = pdo_fetchall($sql, $params);
        $m = 0;
        while (!($m >= count($building_list))) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_room') . ' where bid=' . $building_list[$m]['id'] . ' and ' . $condition;
            $building_list[$m]['houses'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and bid=' . $building_list[$m]['id'] . ' and tid>0 and ' . $condition;
            $building_list[$m]['members'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select sum(fee) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=1 and bid=' . $building_list[$m]['id'] . ' and ' . $condition;
            $building_list[$m]['fees'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select bg from ' . tablename('rhinfo_zyxq_location') . ' where id=' . $building_list[$m]['lid'] . ' and ' . $condition;
            $bg = pdo_fetchcolumn($sql, $params);
            if (!empty($bg)) {
                $building_list[$m]['bg'] = $bg;
            } else {
                $building_list[$m]['bg'] = '#ff9900';
            }
            ($m += 1) + -1;
        }
        $data[$k]['building_list'] = $building_list;
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
        $tpaybill = array();
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =0 and ' . $condition;
        $tpaybill['today'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =1 and ' . $condition;
        $tpaybill['yesterday'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . $starttime . ' and paydate<=' . $endtime . ' and ' . $condition;
        $tpaybill['month'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
        $tpaybill['nopay'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . strtotime(date('Y-m') . '-01') . ' and paydate<=' . TIMESTAMP . ' and ' . $condition;
        $tpaybill['lmonth'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . strtotime(date('Y') . '-01-01') . ' and paydate<=' . strtotime(date('Y') . '-12-31') . ' and ' . $condition;
        $tpaybill['year'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =0 and ' . $condition;
        $carbill_today = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =1 and ' . $condition;
        $carbill_yesterday = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and paydate>0 and paydate>=' . $starttime . ' and paydate<=' . $endtime . ' and ' . $condition;
        $carbill_month = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=1 and ' . $condition;
        $carbill_nopay = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_carbill') . ' where status=2 and paydate>0 and paydate>=' . strtotime(date('Y-m') . '-01') . ' and paydate<=' . TIMESTAMP . ' and ' . $condition;
        $carbill_lmonth = pdo_fetchcolumn($sql, $params);
        $tpaybill['today'] = $tpaybill['today'] + $carbill_today;
        $tpaybill['yesterday'] = $tpaybill['yesterday'] + $carbill_yesterday;
        $tpaybill['month'] = $tpaybill['month'] + $carbill_month;
        $tpaybill['nopay'] = $tpaybill['nopay'] + $carbill_nopay;
        $tpaybill['lmonth'] = $tpaybill['lmonth'] + $carbill_lmonth;
        $sql = 'SELECT count(distinct(bid)) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=1 and bid>0 and fee>0 and ' . $condition;
        $tpaybill['buildings'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(distinct(hid)) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=1 and fee>0 and ' . $condition;
        $tpaybill['rooms'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(distinct(hid)) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=2 and fee>0 and ' . $condition;
        $tpaybill['shops'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT count(distinct(hid)) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=3 and fee>0 and ' . $condition;
        $tpaybill['garages'] = pdo_fetchcolumn($sql, $params);
        $data[$k]['tpaybill'] = $tpaybill;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where category = 1 and ' . $condition . ' ORDER BY title,id ASC ';
        $location_list = pdo_fetchall($sql, $params);
        $n = 0;
        while (!($n >= count($location_list))) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_shop') . ' where lid=' . $location_list[$n]['id'] . ' and ' . $condition;
            $location_list[$n]['houses'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where bid=' . $location_list[$n]['id'] . ' and tid=0 and ' . $condition;
            $location_list[$n]['members'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select sum(fee) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=2 and bid=' . $location_list[$n]['id'] . ' and ' . $condition;
            $location_list[$n]['fees'] = pdo_fetchcolumn($sql, $params);
            if (empty($location_list[$n]['bg'])) {
                $location_list[$n]['bg'] = '#ff9900';
            }
            ($n += 1) + -1;
        }
        $data[$k]['location_list'] = $location_list;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where category = 2 and ' . $condition . ' ORDER BY title,id ASC ';
        $carlocation_list = pdo_fetchall($sql, $params);
        $j = 0;
        while (!($j >= count($carlocation_list))) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where lid=' . $carlocation_list[$j]['id'] . ' and ' . $condition;
            $carlocation_list[$j]['houses'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where status>0 and enddate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(enddate),\'%y-%m-%d\')) < 0 and lid=' . $carlocation_list[$j]['id'] . ' and ' . $condition;
            $carlocation_list[$j]['members'] = pdo_fetchcolumn($sql, $params);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where status>0 and enddate>0 and datediff(DATE_FORMAT(FROM_UNIXTIME(enddate),\'%y-%m-%d\'),DATE_FORMAT(NOW(),\'%y-%m-%d\')) >=0 and datediff(DATE_FORMAT(FROM_UNIXTIME(enddate),\'%y-%m-%d\'),DATE_FORMAT(NOW(),\'%y-%m-%d\')) <=30 and lid=' . $carlocation_list[$j]['id'] . ' and ' . $condition;
            $pcount = pdo_fetchcolumn($sql, $params);
            if ($pcount > 0) {
                $carlocation_list[$j]['pcount'] = $pcount;
            } else {
                $sql = 'select sum(fee) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and category=4 and bid=' . $carlocation_list[$j]['id'] . ' and ' . $condition;
                $carlocation_list[$j]['fees'] = pdo_fetchcolumn($sql, $params);
            }
            if (empty($carlocation_list[$j]['bg'])) {
                $carlocation_list[$j]['bg'] = '#ff9900';
            }
            ($j += 1) + -1;
        }
        $data[$k]['carlocation_list'] = $carlocation_list;
        $defaultpid = $data[$k]['pid'];
        $defaultrid = $data[$k]['id'];
        $tcostbill = array();
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition;
        $tcostbill['today1'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition;
        $tcostbill['yesterday1'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
        $tcostbill['month1'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y') . '-01-01') . ' and ctime<=' . strtotime(date('Y') . '-12-31') . ' and ' . $condition;
        $tcostbill['year1'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP . ' and ' . $condition;
        $tcostbill['lmonth1'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition;
        $tcostbill['today2'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition;
        $tcostbill['yesterday2'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
        $tcostbill['month2'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y') . '-01-01') . ' and ctime<=' . strtotime(date('Y') . '-12-31') . ' and ' . $condition;
        $tcostbill['year2'] = pdo_fetchcolumn($sql, $params);
        $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP . ' and ' . $condition;
        $tcostbill['lmonth2'] = pdo_fetchcolumn($sql, $params);
        $data[$k]['tcostbill'] = $tcostbill;
        ($k += 1) + -1;
    }
    $pager = pagination($total, $pindex, $psize);
}
include $this->mywtpl('index');
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'parkinglot';
$tablename = 'rhinfo_zyxq_parkinglot';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$rights = $this->myrights(7, $mydo, 'list');
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$navtitle = '车位管理';
if ($operation == 'list') {
    $current = '车场列表';
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
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '启用';
            } else {
                $data[$k]['statustxt'] = '禁用';
            }
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parkingio') . ' where weid = :weid and lotid = :lotid and io=1 and status=1';
            $data[$k]['inqty'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':lotid' => $data[$k]['id']));
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parkingio') . ' where weid = :weid and lotid = :lotid and io=2 and status=1';
            $data[$k]['outqty'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':lotid' => $data[$k]['id']));
            $qrcode = $this->my_mobileurl($this->createMobileUrl('car', array('op' => 'pay', 'parkid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            $data[$k]['wxqrcode'] = $this->createqrcode($data[$k]['qrurl']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $navtitle = '车位管理';
    $current = '新增停车场';
    if ($_W['isajax']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'ischarge' => $_GPC['ischarge'], 'parktype' => $_GPC['parktype'], 'parkingnum' => $_GPC['parkingnum'], 'cloudid' => $_GPC['cloudid'], 'pc_secret' => $_GPC['pc_secret'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'devtype' => $_GPC['devtype'], 'doortype' => $_GPC['doortype'], 'pc_type' => $_GPC['pc_type'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
        if ($_GPC['pc_type'] == 1) {
            $set = array();
            $set['userId'] = $region['pc_appid'];
            $set['userKey'] = $region['pc_secret'];
            $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id=:pid';
            $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
            if (empty($property['pc_pid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'name', 'value' => $property['title']), array('name' => 'phone', 'value' => $property['telphone']), array('name' => 'linkMan', 'value' => $region['contact']), array('name' => 'address', 'value' => $property['address']));
                $res = icar_post_createpid($set, $params);
                if ($res['successCode'] == '100') {
                    pdo_update('rhinfo_zyxq_property', array('pc_pid' => $res['pcId']), array('weid' => $mywe['weid'], 'id' => $_GPC['pid']));
                    $property['pc_pid'] = $res['pcId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            if (empty($region['pc_rid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'pcId', 'value' => $property['pc_pid']), array('name' => 'name', 'value' => $region['title']), array('name' => 'adConent', 'value' => $region['title'] . ',欢迎您!'));
                $res = icar_post_createrid($set, $params);
                if ($res['successCode'] == '100') {
                    pdo_update('rhinfo_zyxq_region', array('pc_rid' => $res['plgId']), array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
                    $region['pc_rid'] = $res['plgId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            $params = array();
            $params['updateCreateParamItems'] = array(array('name' => 'plgId', 'value' => $region['pc_rid']), array('name' => 'name', 'value' => $_GPC['title']), array('name' => 'parklotNum', 'value' => $_GPC['parkingnum']), array('name' => 'parkLotAddress', 'value' => $region['address']));
            $res = icar_post_createparkid($set, $params);
            if ($res['successCode'] == '100') {
                $data['pc_plotid'] = $res['plId'];
                $data['cloudid'] = $res['plId'];
            } else {
                echo $res['result'] . $res['errorCode'];
                exit(0);
            }
        } elseif ($_GPC['pc_type'] == 2) {
            $set = array();
            $set['pc_appid'] = $region['pc_appid'];
            $set['pc_secret'] = $region['pc_secret'];
            if (empty($region['pc_rid'])) {
                $set['url'] = 'third_api/buildingAppAction_create.action';
                $params = array();
                $params = array('buildingName' => $region['title'], 'provinceName' => $region['province'], 'cityName' => $region['city'], 'districtName' => $region['district'], 'phoneCode' => $region['telphone'], 'manager' => $region['contact']);
                $res = ipms_http_post($set, $params);
                if ($res['result'] == 'success') {
                    $res = json_decode(urldecode($res['data']), true);
                    if ($res['resultCode'] == '0000') {
                        $set['url'] = 'appuser/ipcRecordAction!getIPCBuildingList.action';
                        $params = array();
                        $params = array('buildingName' => $region['title']);
                        $ret = ipms_http_post($set, $params);
                        if ($ret['resultCode'] == '0000') {
                            pdo_update('rhinfo_zyxq_region', array('pc_rid' => $ret['result']['buildingId']), array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
                            $region['pc_rid'] = $ret['result']['buildingId'];
                        } else {
                            echo $ret['resultMsg'] . $ret['resultCode'];
                            exit(0);
                        }
                    } else {
                        echo $res['resultMsg'] . $res['resultCode'];
                        exit(0);
                    }
                }
            }
            $data['pc_plotid'] = $_GPC['cloudid'];
        }
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
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
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid=:rid and category = 2';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $navtitle = '车位管理';
    $current = '编辑停车场';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'ischarge' => $_GPC['ischarge'], 'parktype' => $_GPC['parktype'], 'parkingnum' => $_GPC['parkingnum'], 'cloudid' => $_GPC['cloudid'], 'pc_secret' => $_GPC['pc_secret'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'devtype' => $_GPC['devtype'], 'doortype' => $_GPC['doortype'], 'pc_type' => $_GPC['pc_type'], 'pc_plotid' => $_GPC['cloudid'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
        if ($_GPC['pc_type'] == 1) {
            $set = array();
            $set['userId'] = $region['pc_appid'];
            $set['userKey'] = $region['pc_secret'];
            $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id=:pid';
            $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
            if (empty($property['pc_pid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'name', 'value' => $property['title']), array('name' => 'phone', 'value' => $property['telphone']), array('name' => 'linkMan', 'value' => $region['contact']), array('name' => 'address', 'value' => $property['address']));
                $res = icar_post_createpid($set, $params);
                if ($res['successCode'] == '100') {
                    pdo_update('rhinfo_zyxq_property', array('pc_pid' => $res['pcId']), array('weid' => $mywe['weid'], 'id' => $_GPC['pid']));
                    $property['pc_pid'] = $res['pcId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            if (empty($region['pc_rid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'pcId', 'value' => $property['pc_pid']), array('name' => 'name', 'value' => $region['title']), array('name' => 'adConent', 'value' => $region['title'] . ',欢迎您!'));
                $res = icar_post_createrid($set, $params);
                if ($res['successCode'] == '100') {
                    pdo_update('rhinfo_zyxq_region', array('pc_rid' => $res['plgId']), array('weid' => $mywe['weid'], 'id' => $_GPC['rid']));
                    $region['pc_rid'] = $res['plgId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            if (!empty($item['pc_plotid'])) {
                $params = array();
                $params['id'] = $item['pc_plotid'];
                $params['updateCreateParamItems'] = array(array('name' => 'plgId', 'value' => $region['pc_rid']), array('name' => 'name', 'value' => $_GPC['title']), array('name' => 'parklotNum', 'value' => $_GPC['parkingnum']), array('name' => 'parkLotAddress', 'value' => $region['address']));
                $res = icar_post_updateparkid($set, $params);
                if (!($res['successCode'] == '100')) {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            } else {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'plgId', 'value' => $region['pc_rid']), array('name' => 'name', 'value' => $_GPC['title']), array('name' => 'parklotNum', 'value' => $_GPC['parkingnum']), array('name' => 'parkLotAddress', 'value' => $region['address']));
                $res = icar_post_createparkid($set, $params);
                if ($res['successCode'] == '100') {
                    $data['pc_plotid'] = $res['plId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
        }
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
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
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid=:rid and category = 2';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category = 2';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除停车场';
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
} elseif ($operation == 'ioadd') {
    $navtitle = '停车场管理';
    $lotid = $_GPC['lotid'];
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $parkinglot = pdo_fetch($sql, array(':id' => $lotid, ':weid' => $mywe['weid']));
    $io = empty($_GPC['io']) ? 1 : $_GPC['io'];
    if ($io == 1) {
        $current = '新增入口';
    } else {
        $current = '新增出口';
    }
    if ($_W['isajax']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $parkinglot['pid'], ':rid' => $parkinglot['rid']));
        $parklot['pc_type'] = !empty($parklot['pc_type']) ? $parklot['pc_type'] : $region['pc_type'];
        $k = 0;
        while (!($k >= count($_GPC['ioid']))) {
            $data = array('weid' => $mywe['weid'], 'lotid' => $lotid, 'title' => $_GPC['title'][$k], 'locksn' => $_GPC['locksn'][$k], 'lockid' => '03', 'io' => $io, 'status' => $_GPC['status'][$k], 'pc_ioid' => $_GPC['pc_ioid'][$k], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $update_data = array('title' => $_GPC['title'][$k], 'locksn' => $_GPC['locksn'][$k], 'pc_ioid' => $_GPC['pc_ioid'][$k]);
            if (empty($_GPC['ioid'][$k])) {
                if ($parklot['pc_type'] == 1) {
                    $set = array();
                    $set['userId'] = $region['pc_appid'];
                    $set['userKey'] = $region['pc_secret'];
                    $params = array();
                    $params['updateCreateParamItems'] = array(array('name' => 'plId', 'value' => $parkinglot['pc_plotid']), array('name' => 'csId', 'value' => $parkinglot['pc_ruleid']), array('name' => 'name', 'value' => $_GPC['title'][$k]));
                    $res = icar_post_createioid($set, $params);
                    if ($res['successCode'] == '100') {
                        $data['pc_ioid'] = $res['plgaId'];
                    } else {
                        echo $res['result'] . $res['errorCode'];
                        exit(0);
                    }
                } elseif ($parklot['pc_type'] == 9) {
                    if ($this->syscfg['doortype'] == 3) {
                        $url = 'postlock.html?appid=' . $this->syscfg['apiid'] . '&appsecret=' . $this->syscfg['apikey'];
                        $res = wmj_httpPost($url, $_GPC['locksn'][$key]);
                        echo '无法添加门禁，请联系厂商';
                        exit(0);
                    }
                }
                pdo_insert('rhinfo_zyxq_parkingio', $data);
                $id = pdo_insertid();
                $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingio') . ' where weid = :weid and id = :ioid ';
                $item = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':ioid' => $_GPC['plotid'][$k]));
                if ($parklot['pc_type'] == 1) {
                    if (!empty($item['pc_ioid'])) {
                        $set = array();
                        $set['userId'] = $region['pc_appid'];
                        $set['userKey'] = $region['pc_secret'];
                        $params = array();
                        $params['id'] = $item['pc_ioid'];
                        $params['updateCreateParamItems'] = array(array('name' => 'plId', 'value' => $parkinglot['pc_plotid']), array('name' => 'csId', 'value' => $parkinglot['pc_ruleid']), array('name' => 'name', 'value' => $_GPC['title'][$key]));
                        $res = icar_post_updateioid($set, $params);
                        if (!($res['successCode'] == '100')) {
                            echo $res['result'] . $res['errorCode'];
                            exit(0);
                        }
                    } else {
                        $params = array();
                        $params['updateCreateParamItems'] = array(array('name' => 'plId', 'value' => $parkinglot['pc_plotid']), array('name' => 'csId', 'value' => $parkinglot['pc_ruleid']), array('name' => 'name', 'value' => $_GPC['title'][$key]));
                        $res = icar_post_createioid($set, $params);
                        if ($res['successCode'] == '100') {
                            $update_data['pc_ioid'] = $res['plgaId'];
                        } else {
                            echo $res['result'] . $res['errorCode'];
                            exit(0);
                        }
                    }
                }
                pdo_update('rhinfo_zyxq_parkingio', $update_data, array('id' => $_GPC['ioid'][$k]));
                $this->mysyslog($mywe['pid'], $mydo, $operation, '修改入出口', '修改入出口' . 'id=' . $_GPC['plotid'][$k]);
            }
            ($k += 1) + -1;
        }
        echo 'ok';
        exit(0);
    }
    $sql = 'select title,pc_type from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $parkinglot['rid'], ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingio') . ' where io=:io and lotid = :lotid and weid = :weid';
    $list = pdo_fetchall($sql, array(':io' => $io, ':lotid' => $lotid, ':weid' => $mywe['weid']));
    $k = 0;
    while (!($k >= count($list))) {
        $qrcode = $this->my_mobileurl($this->createMobileUrl('service', array('op' => 'scanopen', 'lotid' => $lotid, 'id' => $list[$k]['id'])));
        $list[$k]['url'] = $qrcode;
        $list[$k]['qrcode'] = $this->createqrcode($qrcode);
        ($k += 1) + -1;
    }
    include $this->mywtpl('inout');
} elseif ($operation == 'iodel') {
    $current = '删除入出口';
    $id = intval($_GPC['id']);
    if (!empty($this->syscfg['apikey']) && $this->syscfg['doortype'] == 3) {
        $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
        $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
        $url = 'dellock.html?appid=' . $this->syscfg['apiid'] . '&appsecret=' . $this->syscfg['apikey'];
        $res = wmj_httpPost($url, $item['locksn']);
        if (!($res['state'] == '1' && $res['state_code'] == '1')) {
            echo '删除失败!';
        }
    }
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parkingio', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'iolog') {
    $current = '进出记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $cate = empty($_GPC['cate']) ? 1 : $_GPC['cate'];
    if ($cate == 2) {
        $condition .= ' and lotid = :lotid ';
        $params[':lotid'] = $id;
        $iodate = $_GPC['iodate'];
        if (!empty($iodate)) {
            $starttime = strtotime($iodate['start']);
            $endtime = strtotime($iodate['end'] . ' 23:59:59');
            $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
        } else {
            $starttime = strtotime('now -30days');
            $endtime = TIMESTAMP;
        }
        if (!empty($_GPC['status'])) {
            $condition .= ' and io = :io';
            $params[':io'] = $_GPC['status'];
        }
        if (!empty($_GPC['keyword'])) {
            $sql = 'select uid from ' . tablename('mc_members') . ' where realname like :keyword or nickname like :keyword or mobile like :keyword';
            $uid = pdo_fetchcolumn($sql, array(':keyword' => '%' . $_GPC['keyword'] . '%'));
            if (!empty($uid)) {
                $condition .= ' and uid=:uid ';
                $params[':uid'] = $uid;
            }
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:id';
        $parkinglot = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
        $current = $parkinglot['title'] . $current;
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_parkingiolog') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        load()->model('mc');
        $fans = array();
        if ($total > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingiolog') . ' where ' . $condition . ' order by ctime desc ' . $limit;
            $data = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($data))) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
                $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $parkinglot['rid'], ':uid' => $data[$k]['uid']));
                $fans = mc_fansinfo($data[$k]['uid'], 0, $mywe['weid']);
                $data[$k]['avatar'] = $fans['avatar'];
                $data[$k]['realname'] = !empty($member['realname']) ? $member['realname'] : $fans['nickname'];
                $data[$k]['address'] = !empty($member['address']) ? $member['address'] : '公共车位';
                ($k += 1) + -1;
            }
            $pager = pagination($total, $pindex, $psize);
        }
    } else {
        $condition .= ' and parklotid = :lotid ';
        $params[':lotid'] = $id;
        $iodate = $_GPC['iodate'];
        if (!empty($iodate)) {
            $starttime = strtotime($iodate['start']);
            $endtime = strtotime($iodate['end'] . ' 23:59:59');
            $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
        } else {
            $starttime = strtotime('now -30days');
            $endtime = TIMESTAMP;
        }
        if (!empty($_GPC['status'])) {
            $condition .= ' and io = :io';
            $params[':io'] = $_GPC['status'];
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and carno like \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and id=:id';
        $parkinglot = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
        $current = $parkinglot['title'] . $current;
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_car_iolog') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $fans = array();
        if ($total > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_car_iolog') . ' where ' . $condition . ' order by ctime desc ' . $limit;
            $data = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
    }
    include $this->mywtpl('iolog');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parkingiolog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'chargerule') {
    $navtitle = '停车场管理';
    $current = '计费规则';
    $lotid = $_GPC['id'];
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $lotid, ':weid' => $mywe['weid']));
    if ($_W['isajax']) {
        $data = array('starttime' => $_GPC['lstarttime'], 'endtime' => $_GPC['lendtime'], 'minute' => $_GPC['minute'], 'qty' => $_GPC['qty'], 'dayfee' => $_GPC['dayfee'], 'getfee' => $_GPC['getfee'], 'price' => $_GPC['lprice'], 'unit' => $_GPC['unit'], 'pricerule' => $_GPC['pricerule'], 'monthcardprice' => $_GPC['monthcardprice'], 'rechargmonths' => $_GPC['rechargmonths'], 'givemonths' => $_GPC['givemonths'], 'monthmethod' => $_GPC['monthmethod'], 'paymethod' => $_GPC['paymethod'], 'authentication' => $_GPC['authentication'], 'cloudruleid' => $_GPC['cloudruleid'], 'year_coupon' => $_GPC['year_coupon'], 'month_coupon' => $_GPC['month_coupon'], 'maxmoney' => $_GPC['maxmoney'], 'maxminutes' => $_GPC['maxminutes'], 'isreserve' => $_GPC['isreserve']);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
        $item['pc_type'] = !empty($item['pc_type']) ? $item['pc_type'] : $region['pc_type'];
        if ($item['pc_type'] == 1) {
            $set = array();
            $set['userId'] = $region['pc_appid'];
            $set['userKey'] = $region['pc_secret'];
            $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where weid = :weid and id=:pid';
            $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
            if (empty($property['pc_pid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'name', 'value' => $property['title']), array('name' => 'phone', 'value' => $property['telphone']), array('name' => 'linkMan', 'value' => $region['contact']), array('name' => 'address', 'value' => $property['address']));
                $res = icar_post_createpid($set, $params);
                if ($res['successCode'] == '100') {
                    pdo_update('rhinfo_zyxq_property', array('pc_pid' => $res['pcId']), array('weid' => $mywe['weid'], 'id' => $item['pid']));
                    $property['pc_pid'] = $res['pcId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            if (empty($region['pc_rid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'pcId', 'value' => $property['pc_pid']), array('name' => 'name', 'value' => $region['title']), array('name' => 'adConent', 'value' => $region['title'] . ',欢迎您!'));
                $res = icar_post_createrid($set, $params);
                if ($res['successCode'] == '100') {
                    pdo_update('rhinfo_zyxq_region', array('pc_rid' => $res['plgId']), array('weid' => $mywe['weid'], 'id' => $item['rid']));
                    $region['pc_rid'] = $res['plgId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            if (empty($item['pc_plotid'])) {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'plgId', 'value' => $region['pc_rid']), array('name' => 'name', 'value' => $_GPC['title']), array('name' => 'parklotNum', 'value' => $_GPC['parkingnum']), array('name' => 'parkLotAddress', 'value' => $region['address']));
                $res = icar_post_createparkid($set, $params);
                if ($res['successCode'] == '100') {
                    $data['pc_plotid'] = $res['plId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
            if (!empty($item['pc_ruleid'])) {
                $params = array();
                $params['id'] = $item['pc_ruleid'];
                $params['updateCreateParamItems'] = array(array('name' => 'name', 'value' => $item['title'] . '计费规则'), array('name' => 'chargeType', 'value' => empty($_GPC['pricerule']) ? 1 : $_GPC['pricerule']), array('name' => 'initiateRate', 'value' => $_GPC['lprice']), array('name' => 'maxRate', 'value' => $_GPC['dayfee']), array('name' => 'freeMinutes', 'value' => $_GPC['minute']), array('name' => 'billingType', 'value' => $_GPC['getfee'] == 1 ? 1 : 2));
                $res = icar_post_updateruleid($set, $params);
                if (!($res['successCode'] == '100')) {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            } else {
                $params = array();
                $params['updateCreateParamItems'] = array(array('name' => 'pcId', 'value' => $property['pc_pid']), array('name' => 'name', 'value' => $item['title'] . '计费规则'), array('name' => 'chargeType', 'value' => empty($_GPC['pricerule']) ? 1 : $_GPC['pricerule']), array('name' => 'initiateRate', 'value' => $_GPC['lprice']), array('name' => 'maxRate', 'value' => $_GPC['dayfee']), array('name' => 'freeMinutes', 'value' => $_GPC['minute']), array('name' => 'billingType', 'value' => $_GPC['getfee'] == 1 ? 1 : 2));
                $res = icar_post_createruleid($set, $params);
                if ($res['successCode'] == '100') {
                    $data['pc_ruleid'] = $res['csId'];
                } else {
                    echo $res['result'] . $res['errorCode'];
                    exit(0);
                }
            }
        }
        $ruleids = $_GPC['ruleid'];
        $starttimes = $_GPC['starttime'];
        $endtimes = $_GPC['endtime'];
        $prices = $_GPC['price'];
        $k = 0;
        while (!($k >= count($prices))) {
            if (!empty($prices[$k])) {
                $data_pricerule = array('weid' => $mywe['weid'], 'lotid' => $lotid, 'starttime' => $starttimes[$k], 'endtime' => $endtimes[$k], 'price' => $prices[$k]);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and id=:ruleid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':ruleid' => $ruleids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zyxq_parkingrule', $data_pricerule, array('weid' => $_W['weid'], 'id' => $ruleids[$k]));
                } else {
                    pdo_insert('rhinfo_zyxq_parkingrule', $data_pricerule);
                }
            }
            ($k += 1) + -1;
        }
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $lotid, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetchcolumn($sql, array(':id' => $item['rid'], ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parkingrule') . ' where weid=:weid and lotid=:lotid ORDER BY starttime,id ASC ';
    $pricerules = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':lotid' => $lotid));
    include $this->mywtpl('price');
} elseif ($operation == 'pricetpl') {
    include $this->mywtpl('pricetpl');
} elseif ($operation == 'delpricerule') {
    $current = '删除价格规则';
    $id = intval($_GPC['ruleid']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_parkingrule', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'qrcode') {
    if ($_W['isajax']) {
        $sql = 'SELECT * FROM ' . tablename($tablename) . ' WHERE weid = :weid and id = :parkid ';
        $parking = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':parkid' => $_GPC['parkid']));
        load()->func('communication');
        $barcode = array('expire_seconds' => '', 'action_name' => '', 'action_info' => array('scene' => array()));
        $qrctype = 2;
        $acid = intval($_W['acid']);
        $uniacccount = WeAccount::create($acid);
        $scene_str = random(32);
        $is_exist = pdo_fetchcolumn('SELECT id FROM ' . tablename('qrcode') . ' WHERE uniacid = :uniacid AND acid = :acid AND scene_str = :scene_str AND model = 2', array(':uniacid' => $_W['uniacid'], ':acid' => $_W['acid'], ':scene_str' => $scene_str));
        if (!empty($is_exist)) {
            echo '场景值已经存在,请重新生成';
            exit(0);
        }
        $barcode['action_info']['scene']['scene_str'] = $scene_str;
        $barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
        $result = $uniacccount->barCodeCreateFixed($barcode);
        if (!is_error($result)) {
            $insert = array('uniacid' => $_W['uniacid'], 'acid' => $acid, 'scene_str' => $barcode['action_info']['scene']['scene_str'], 'keyword' => trim($parking['title']), 'name' => trim($parking['title']), 'model' => $qrctype, 'ticket' => $result['ticket'], 'url' => $result['url'], 'expire' => $result['expire_seconds'], 'createtime' => TIMESTAMP, 'status' => '1', 'type' => 'scene');
            pdo_insert('qrcode', $insert);
            pdo_insert('rule', array('uniacid' => $_W['uniacid'], 'name' => trim($parking['title']), 'module' => 'rhinfo_zyxq', 'status' => 1));
            $ruleid = pdo_insertid();
            pdo_insert('rule_keyword', array('uniacid' => $_W['uniacid'], 'rid' => $ruleid, 'content' => trim($parking['title']), 'type' => 1, 'module' => 'rhinfo_zyxq', 'status' => 1));
            pdo_insert('rhinfo_zyxq_replyrule', array('weid' => $_W['uniacid'], 'replyid' => $ruleid, 'parkid' => $_GPC['parkid'], 'qr' => 1, 'uid' => $mywe['uid'], 'ctime' => TIMESTAMP));
            pdo_update($tablename, array('qrurl' => $result['url']), array('weid' => $_W['uniacid'], 'id' => $_GPC['parkid']));
            echo 'ok';
        } else {
            echo '接口错误:' . $result['errorcode'] . $result['message'];
        }
        exit(0);
    }
}
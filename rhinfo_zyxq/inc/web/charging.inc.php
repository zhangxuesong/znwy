<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'charging';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zycj_charging';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '运营策略';
if ($operation == 'list') {
    $current = '充电站列表';
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
            $data[$k]['online'] = '离线';
            if ($data[$k]['devtype'] == 1) {
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'checkStas', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $data[$k]['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS' && $rs[1] == '0001') {
                    $data[$k]['online'] = '在线';
                }
            } elseif ($data[$k]['devtype'] == 2) {
                $set = array();
                $set['url'] = '/service/deviceStatus/deviceStatus.do';
                $set['yk_appid'] = $this->syscfg['yk_appid'];
                $set['yk_appkey'] = $this->syscfg['yk_appkey'];
                $post_data = array();
                $post_data['device_code'] = $data[$k]['devicesn'];
                $res = ykdev_http_post($set, $post_data);
                if ($res['return_code'] == 1 && $res['result_code'] == 1) {
                    $data[$k]['online'] = $res['status'] == 1 ? '在线' : '离线';
                }
            } elseif ($data[$k]['devtype'] == 3) {
                $set = array();
                $set['url'] = 'net.equip.charge.slow.port.query';
                $set['ds_appid'] = $this->syscfg['ds_appid'];
                $set['ds_appkey'] = $this->syscfg['ds_appkey'];
                $post_data = array();
                $post_data['equipCd'] = $data[$k]['devicesn'];
                $res = posei_http_post($set, $post_data);
                if ($res['code'] == 1) {
                    $data[$k]['online'] = '在线';
                } else {
                    $data[$k]['online'] = '离线';
                }
            } elseif ($data[$k]['devtype'] == 4) {
                $url = 'mx10/Api.php';
                $post_data = array('action' => 'sendFs22', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $data[$k]['devicesn']);
                $post_data['port'] = 'PP';
                $post_data['times'] = '0003';
                $res = Mx_httpPost($url, $post_data);
                if ($res[0] == 'SUCCESS') {
                    $data[$k]['online'] = '在线';
                } else {
                    $data[$k]['online'] = '离线';
                }
            }
            $qrcode = $this->my_mobileurl($this->createMobileUrl('charging', array('op' => 'scan', 'id' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增充电站';
    if ($_W['ispost']) {
        $sql = 'select count(*) from ' . tablename($tablename) . ' where weid = :weid ';
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secacc') . ' where weid = :weid and status=1 ';
        $secacc = pdo_fetch($sql, array(':weid' => $mywe['weid']));
        if ($secacc['chargingqty'] > 0 && $count >= $secacc['chargingqty']) {
            $url = $this->createWebUrl($mydo, array('op' => 'list', 'direct' => 1));
            $this->mywebmsg('错误', '超出数量限制，请联系平台', $url, 'danger');
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'devtype' => $_GPC['devtype'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'address' => $_GPC['address'], 'devicesn' => $_GPC['devicesn'], 'devicesn' => $_GPC['devicesn'], 'ports' => $_GPC['ports'], 'devcnum' => $_GPC['devcnum'], 'paytype' => $_GPC['paytype'], 'remark' => $_GPC['remark'], 'isvipcard' => $_GPC['isvipcard'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        if ($_GPC['regdevice'] == 1) {
            if ($_GPC['devtype'] == 1) {
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'registered', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $_GPC['devicesn']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    $data['mxdevfid'] = $rs[1];
                } else {
                    $this->mywebmsg('错误', '无法添加设备' . $rs[1] . ',请联系厂商', '', 'danger');
                }
            } elseif ($_GPC['devtype'] == 4) {
                $url = 'mx10/Api.php';
                $post_data = array('action' => 'registered', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $_GPC['devicesn'], 'device_port' => $_GPC['ports']);
                $rs_arr = Mx_httpPost($url, $post_data);
                if ($rs_arr[0] == 'SUCCESS') {
                    $data['mxdevfid'] = 0;
                } elseif ($rs_arr[0] == 'ERROR') {
                    if ($rs_arr[1] == 1001) {
                        $msg = 'Appid或Tken异常';
                    } elseif ($rs_arr[1] == 2001) {
                        $msg = '设备编号格式异常';
                    } elseif ($rs_arr[1] == 2002) {
                        $msg = '设备编号长度异常';
                    } elseif ($rs_arr[1] == 2003) {
                        $msg = '设备编号已存在';
                    } elseif ($rs_arr[1] == 2004) {
                        $msg = '设备出库异常';
                    } elseif ($rs_arr[1] == 'ERROR') {
                        $msg = '设备远程联网失败';
                    }
                    $this->mywebmsg('错误', '无法添加设备', $msg, '', 'danger');
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
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_article_category') . ' where weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑充电站';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'devtype' => $_GPC['devtype'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'address' => $_GPC['address'], 'devicesn' => $_GPC['devicesn'], 'devcnum' => $_GPC['devcnum'], 'ports' => $_GPC['ports'], 'paytype' => $_GPC['paytype'], 'isvipcard' => $_GPC['isvipcard'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
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
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除充电站';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        if (!empty($item['mxdevfid'])) {
            if ($_GPC['devtype'] == 1) {
                $url = 'paycloud2/Api.php';
                $post_data = array('action' => 'device_delete', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'fid' => $item['mxdevfid']);
                $rs = Mx_httpPost($url, $post_data);
                if ($rs[0] == 'SUCCESS') {
                    echo 'ok';
                } else {
                    echo '删除失败!' . $rs[1];
                }
                exit(0);
            }
        }
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'rule') {
    $chargid = intval($_GPC['chargid']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $charging = pdo_fetch($sql, array(':id' => $chargid, ':weid' => $mywe['weid']));
    if ($_W['isajax']) {
        $hours = $_GPC['hour'];
        $prices = $_GPC['price'];
        $currents = $_GPC['current'];
        $subids = $_GPC['subid'];
        $i = 0;
        $param = '';
        $k = 0;
        while (!($k >= count($prices))) {
            if (!empty($hours[$k])) {
                $data = array('weid' => $mywe['weid'], 'chargid' => $chargid, 'current' => $currents[$k], 'hour' => $hours[$k], 'price' => $prices[$k], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_rule') . ' where weid=:weid and id=:subid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':subid' => $subids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zycj_charging_rule', $data, array('weid' => $_W['weid'], 'id' => $subids[$k]));
                } else {
                    pdo_insert('rhinfo_zycj_charging_rule', $data);
                }
                ($i += 1) + -1;
            }
            ($k += 1) + -1;
        }
        if ($charging['devtype'] == 1) {
            $url = 'paycloud2/Api.php';
            $post_data = array('action' => 'Set_Device', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'times' => '60', 'current' => '3', 'device_code' => $charging['devicesn']);
            $rs = Mx_httpPost($url, $post_data);
            if ($rs[0] == 'SUCCESS') {
                echo 'ok';
            } else {
                echo '设置设备档位及时间失败';
            }
        } elseif ($charging['devtype'] == 4) {
            $url = 'mx10/Api.php';
            $post_data = array('action' => 'set', 'token' => $this->syscfg['mx_appkey'], 'appid' => $this->syscfg['mx_appid'], 'device_code' => $charging['devicesn']);
            $msg = Mx_httpPost_set($url, $post_data);
            echo $msg;
        } else {
            echo 'ok';
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, '设定价格', '设定价格chargid=' . $chargid);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_charging_rule') . ' where weid=:weid and chargid = :chargid ORDER BY id ASC ';
    $rules = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':chargid' => $chargid));
    include $this->mywtpl('rule');
} elseif ($operation == 'pricetpl') {
    include $this->mywtpl('pricetpl');
} elseif ($operation == 'delprice') {
    $current = '删除价格';
    $id = intval($_GPC['subid']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zycj_charging_rule', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'record') {
    $current = '充电记录';
}
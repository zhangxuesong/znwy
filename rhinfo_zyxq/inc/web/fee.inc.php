<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
date_default_timezone_set('Asia/Shanghai');
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'fee';
$tablename = 'rhinfo_zyxq_feeitem';
$condition = ' weid = :weid ';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '费用管理';
$calmethod = $this->calmethod;
if ($operation == 'bill') {
    $current = '收费账单列表';
    $myret = 0;
    $rights = $this->myrights(3, $mydo, 'bill');
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $regions = pdo_fetchall($sql, $params);
    $mybuilding = array();
    $myshoplocation = array();
    $myparklocation = array();
    $m = 0;
    while (!($m >= count($regions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $mybuilding[$regions[$m]['id']] = $buildings;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=1';
        $shoplocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myshoplocation[$regions[$m]['id']] = $shoplocations;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2';
        $parklocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myparklocation[$regions[$m]['id']] = $parklocations;
        ($m += 1) + -1;
    }
    if ($_GPC['rid']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    if ($_GPC['bid']) {
        $condition .= ' AND bid= ' . $_GPC['bid'];
    }
    if ($_GPC['feebilltype']) {
        $condition .= ' AND category = ' . $_GPC['feebilltype'];
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' OR address LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['startdate'])) {
        $starttime = strtotime($_GPC['startdate']);
        $condition .= ' and startdate>=' . $starttime;
    }
    if (!empty($_GPC['enddate'])) {
        $endtime = strtotime($_GPC['enddate']);
        $condition .= ' and enddate<=' . strtotime('+1 days', $endtime);
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
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
            $data[$k]['daterange'] = date('Y-m-d', $data[$k]['startdate']) . '~' . date('Y-m-d', $data[$k]['enddate']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('billlist');
} elseif ($operation == 'edit') {
    $current = '编辑收费账单';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('fee' => $_GPC['fee'], 'measure' => $_GPC['measure'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_feebill', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'bill')) . $mywe['direct']);
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
            if ($_GPC['category'] == 1 || $_GPC['category'] == 3) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
            } else {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid';
            }
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    if ($_GPC['category'] == 1 || $_GPC['category'] == 3) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
    } else {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=1';
    }
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    include $this->mywtpl('editbill');
} elseif ($operation == 'delete') {
    $current = '删除账单';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid=:pid and  id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if ($region['feebillgrant'] != 1) {
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=:category and itemid=:itemid and status=1';
        $maxenddate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid'], ':category' => $item['category'], ':itemid' => $item['itemid']));
        if (!($item['enddate'] >= $maxenddate)) {
            exit('删除失败，请按日期往前删除!');
        }
    }
    if (!($item['status'] >= 2)) {
        if ($item['isalipay']) {
            $set = array();
            $set['app_id'] = $this->syspub['alipay_appid'];
            $set['prikey'] = $this->syspub['alipay_rsa2'];
            $set['app_auth_token'] = $region['lifepay_token'];
            $set['method'] = 'bill.delete';
            $params = "{\r\n\t\t\t\t\t\"community_id\":\"" . $region['lifepay_rid'] . "\",\r\n\t\t\t\t\t \"bill_entry_id_list\":[\r\n\t\t\t\t\t\t\"" . $id . "\"\r\n\t\t\t\t\t  ]\r\n\t\t\t\t\t  }";
            $res = my_alipay_life($set, $params);
            if (is_error($res)) {
                echo $res['message'];
                exit(0);
            } else {
                $res = json_decode($res, 1);
                $res = $res['alipay_eco_cplife_bill_delete_response'];
                if ($res['code'] !== '10000') {
                    if (!empty($res['sub_code'])) {
                        echo $res['sub_msg'] . $res['sub_code'];
                    } else {
                        echo $res['msg'] . $res['code'];
                    }
                    exit(0);
                }
                $glue = 'AND';
                $result = pdo_delete('rhinfo_zyxq_feebill', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
                if (!empty($result)) {
                    echo 'ok';
                } else {
                    echo '删除失败!';
                }
                $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
                exit(0);
            }
        }
        $glue = 'AND';
        $result = pdo_delete('rhinfo_zyxq_feebill', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    }
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'pay') {
    $current = '单笔收款';
    $id = intval($_GPC['id']);
    $paytype = $this->paytype;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $printdetail = $item['daterange'] . $item['title'] . ':' . $item['fee'] . '\\n';
    $printdetail1 = $item['daterange'] . $item['title'] . ':' . $item['fee'] . '<BR>';
    if ($_W['ispost']) {
        if ($_GPC['status'] == 2) {
            exit(0);
        }
        $payfee = floatval($_GPC['payfee']);
        $latefee = floatval($_GPC['latefee']);
        if (!($payfee > 0)) {
            if ($_GPC['paytype'] != 6) {
                exit('金额错误');
            }
        }
        if ($mypaytype == 6) {
            $payfee = 0;
            $latefee = 0;
        }
        $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
        $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
        $payno = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
        $payno = createnum(substr($payno, strlen($paynopre), 14));
        $payno = $paynopre . $payno;
        $data_fee = array('payfee' => $payfee, 'laterate' => $_GPC['laterate'], 'latefee' => $latefee, 'paytype' => intval($_GPC['paytype']), 'status' => 2, 'paydate' => TIMESTAMP, 'payuid' => $mywe['uid'], 'payno' => $payno, 'payfrom' => 1, 'remark' => $_GPC['remark']);
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :id and weid = :weid';
        $property = pdo_fetchcolumn($sql, array(':id' => $item['pid'], ':weid' => $mywe['weid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
        $region = pdo_fetch($sql, array(':id' => $item['rid'], ':weid' => $mywe['weid']));
        if ($item['category'] == 2) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $item['bid'], ':weid' => $mywe['weid']));
            $unit = '';
            $sql = 'select title,ownername from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
            $room = pdo_fetch($sql, array(':id' => $item['hid'], ':weid' => $mywe['weid']));
        } else {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            $building = pdo_fetchcolumn($sql, array(':id' => $item['bid'], ':weid' => $mywe['weid']));
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $item['tid'], ':weid' => $mywe['weid']));
            $sql = 'select title,ownername from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
            $room = pdo_fetch($sql, array(':id' => $item['hid'], ':weid' => $mywe['weid']));
        }
        $data = array();
        $data['weid'] = $mywe['weid'];
        $data['uid'] = $mywe['uid'];
        $data['openid'] = 0;
        $data['title'] = $region['title'] . $building . $unit . $room['title'];
        $data['billid'] = $id;
        $data['ctime'] = TIMESTAMP;
        if (!empty($_GPC['scanqrcode'])) {
            if (substr($_GPC['scanqrcode'], 0, 2) == '28') {
                $data['paytype'] = $paytype[2];
                $data_fee['paytype'] = 2;
            } else {
                $data['paytype'] = $paytype[1];
                $data_fee['paytype'] = 1;
            }
        } else {
            $data['paytype'] = $paytype[$_GPC['paytype']];
        }
        $data['tid'] = $payno . random(8, 1);
        $data['payno'] = $payno;
        $data['fee'] = $payfee;
        $data['status'] = 1;
        $data['pid'] = $item['pid'];
        $data['rid'] = $item['rid'];
        $data['feetype'] = 1;
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
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_feebill', $data_fee, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if ($_GPC['paytype'] == 8 && $item['category'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
            $feeitem = pdo_fetch($sql, array(':id' => $item['itemid'], ':weid' => $mywe['weid']));
            if ($feeitem['relacost'] > 0) {
                $sql = 'select prepayment,preelectric,prewater from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id=:hid ';
                $premoney = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']));
                if ($feeitem['relacost'] == 1) {
                    $money = $premoney['prepayment'] > $payfee ? $payfee : $premoney['prepayment'];
                    $str = ' set prepayment = prepayment -' . $money;
                } elseif ($feeitem['relacost'] == 2) {
                    $money = $premoney['prewater'] > $payfee ? $payfee : $premoney['prewater'];
                    $str = ' set prewater = prewater -' . $money;
                } elseif ($feeitem['relacost'] == 3) {
                    $money = $premoney['preelectric'] > $payfee ? $payfee : $premoney['preelectric'];
                    $str = ' set preelectric = preelectric -' . $money;
                }
                $sql = 'update ' . tablename('rhinfo_zyxq_room') . $str . ' where weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
                pdo_query($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']));
            }
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        $paymethod = '支付方式:' . $paytype[intval($_GPC['paytype'])] . '微信\\n';
        $paymethod1 = '支付方式:' . $paytype[intval($_GPC['paytype'])] . '微信<BR>';
        $content = '<FB><center>缴费通知单</center></FB>\\n';
        $content .= '房产:' . $region['title'] . $building . $unit . $room['title'] . '\\n';
        $content .= '缴费类别:' . $item['title'] . '\\n';
        $content .= '缴费金额:' . $payfee . '元\\n';
        $content .= '缴费周期:' . $item['daterange'] . '\\n';
        $content .= '缴费时间:' . date('Y-m-d h:m') . '\\n';
        $content .= '缴费人:' . $room['ownername'] . '\\n';
        $content1 = '<CB>缴费通知单</CB><BR>';
        $content1 .= '房产:' . $region['title'] . $building . $unit . $room['title'] . '<BR>';
        $content1 .= '缴费类别:' . $item['title'] . '<BR>';
        $content1 .= '缴费金额:' . $payfee . '元<BR>';
        $content1 .= '缴费周期:' . $item['daterange'] . '<BR>';
        $content1 .= '缴费时间:' . date('Y-m-d h:m') . '<BR>';
        $content1 .= '缴费人:' . $room['ownername'] . '<BR>';
        if ($region['isprintfeedetail'] == 1) {
            $content .= $printdetail;
            $content1 .= $printdetail1;
        }
        $content .= $paymethod . '<right>' . $property . '</right>';
        $content .= $paymethod1 . '<RIGHT>' . $property . '</RIGHT>';
        $this->send_print($item['pid'], $item['rid'], 3, urlencode($content), $conten1);
        if (!empty($_GPC['scanqrcode'])) {
            echo 'ok';
            exit(0);
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            if ($_GPC['category'] == 1 || $_GPC['category'] == 3) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
            } else {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid';
            }
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    if ($_GPC['category'] == 1 || $_GPC['category'] == 3) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
    } else {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid';
    }
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
    $feeitem = pdo_fetch($sql, array(':id' => $item['itemid'], ':weid' => $mywe['weid']));
    $region = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $data[$k]['rid']), array('latemethod'));
    if ($item['status'] == 1) {
        if (!empty($feeitem['latedate'])) {
            if ($item['enddate'] > $feeitem['latedate']) {
                $days = intval((strtotime(date('Y-m-d')) - $item['enddate']) / 86400);
            } else {
                $days = intval((strtotime(date('Y-m-d')) - $feeitem['latedate']) / 86400);
            }
        } else {
            $days = intval((strtotime(date('Y-m-d')) - $item['enddate']) / 86400);
        }
        if ($days > $feeitem['latedays']) {
            if ($region['latemethod'] == 1) {
                $item['latefee'] = round($item['fee'] * $feeitem['laterate'] * ($days - $feeitem['latedays']) / 1000, 0);
            } else {
                $item['latefee'] = round($item['fee'] * $feeitem['laterate'] * $days / 1000, 0);
            }
        }
        $item['payfee'] = $item['fee'] + $item['latefee'];
        $item['latedays'] = $days;
    }
    $premoney = array();
    if ($item['category'] == 1) {
        $sql = 'select prepayment,preelectric,prewater from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id=:hid ';
        $premoney = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']));
    }
    include $this->mywtpl('paybill');
} elseif ($operation == 'mybill') {
    $current = '综合收款';
    $myret = 1;
    $condition .= $this->myrcondition();
    $params[':hid'] = $_GPC['hid'];
    $params[':category'] = $_GPC['category'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $_GPC['rid'], ':weid' => $mywe['weid']));
    if ($region['lifepay_token'] && $region['lifepay_rid']) {
        $set = array();
        $set['app_id'] = $this->syspub['alipay_appid'];
        $set['prikey'] = $this->syspub['alipay_rsa2'];
        $set['app_auth_token'] = $region['lifepay_token'];
        $set['method'] = 'bill.batchquery';
        if ($_GPC['category'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and id=:hid';
            $myroom = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':hid' => $_GPC['hid']));
            $lifeparams = "{\r\n\t\t\t\t\t\"community_id\":\"" . $region['lifepay_rid'] . "\",\r\n\t\t\t\t\t\"bill_status\":\"FINISH_PAYMENT\",\r\n\t\t\t\t\t\"out_room_id\":\"" . $myroom['lifepay_hid'] . "\"\r\n\t\t\t\t  }";
            $res = my_alipay_life($set, $lifeparams);
            if (!is_error($res)) {
                $res = json_decode($res, 1);
                $res = $res['alipay_eco_cplife_bill_batchquery_response'];
                if ($res['code'] == '10000') {
                    $lifepays = $res['bill_result_set'];
                    $k = 0;
                    while (!($k >= count($lifepays))) {
                        $update_data = array('payfee' => $lifepays[$k]['bill_entry_amount'], 'paytype' => 4, 'status' => 2, 'paydate' => TIMESTAMP);
                        pdo_update('rhinfo_zyxq_feebill', $update_data, array('weid' => $mywe['weid'], 'id' => $lifepays[$k]['bill_entry_id'], 'status' => 1));
                        ($k += 1) + -1;
                    }
                }
            }
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where hid=:hid and status=1 and category=:category and ' . $condition . ' ORDER BY itemid ,id ';
    $data = pdo_fetchall($sql, $params);
    $total = count($data);
    $totalfee = 0;
    $k = 0;
    while (!($k >= count($data))) {
        $data[$k]['region'] = $region['title'];
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
} elseif ($operation == 'mytbill') {
    $current = '合并收款';
    $myret = 1;
    $condition .= $this->myrcondition();
    $params[':id'] = $_GPC['id'];
    $params[':category'] = $_GPC['category'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $_GPC['rid'], ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where id=:id and status=1 and category=:category and ' . $condition;
    $feebill = pdo_fetch($sql, $params);
    if ($region['lifepay_token'] && $region['lifepay_rid']) {
        $set = array();
        $set['app_id'] = $this->syspub['alipay_appid'];
        $set['prikey'] = $this->syspub['alipay_rsa2'];
        $set['app_auth_token'] = $region['lifepay_token'];
        $set['method'] = 'bill.batchquery';
        if ($_GPC['category'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and pid=:pid and rid=:rid and id=:hid';
            $myroom = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $region['pid'], ':rid' => $_GPC['rid'], ':hid' => $feebill['hid']));
            $lifeparams = "{\r\n\t\t\t\t\t\"community_id\":\"" . $region['lifepay_rid'] . "\",\r\n\t\t\t\t\t\"bill_status\":\"FINISH_PAYMENT\",\r\n\t\t\t\t\t\"out_room_id\":\"" . $myroom['lifepay_hid'] . "\"\r\n\t\t\t\t  }";
            $res = my_alipay_life($set, $lifeparams);
            if (!is_error($res)) {
                $res = json_decode($res, 1);
                $res = $res['alipay_eco_cplife_bill_batchquery_response'];
                if ($res['code'] == '10000') {
                    $lifepays = $res['bill_result_set'];
                    $k = 0;
                    while (!($k >= count($lifepays))) {
                        $update_data = array('payfee' => $lifepays[$k]['bill_entry_amount'], 'paytype' => 4, 'status' => 2, 'paydate' => TIMESTAMP);
                        pdo_update('rhinfo_zyxq_feebill', $update_data, array('weid' => $mywe['weid'], 'id' => $lifepays[$k]['bill_entry_id'], 'status' => 1));
                        ($k += 1) + -1;
                    }
                }
            }
        }
    }
    if ($feebill['tid'] > 0) {
        $room = pdo_get('rhinfo_zyxq_room', array('weid' => $mywe['weid'], 'id' => $_GPC['hid']));
        if (!empty($room['mobile'])) {
            $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_room') . " as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and r.mobile=:mobile and f.status=1 and f.category=1 ORDER BY\r\n\t\t\t\t\t f.itemid, f.ID ASC ";
            $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $room['mobile']));
            $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_garage') . " as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and r.mobile=:mobile and f.status=1 and f.category=3 ORDER BY\r\n\t\t\t\t\t f.itemid,f.ID ASC ";
            $data_garage = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $room['mobile']));
            $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_parking') . " as r on f.hid=r.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and r.mobile=:mobile and f.status=1 and f.category=4 ORDER BY\r\n\t\t\t\t\t f.itemid,f.ID ASC ";
            $data_parking = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $room['mobile']));
            $data = array_merge_recursive($data, $data_garage, $data_parking);
        }
    } else {
        $shop = pdo_get('rhinfo_zyxq_shop', array('weid' => $mywe['weid'], 'id' => $_GPC['hid']));
        $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_shop') . " as s on f.hid=s.id  where f.weid=:weid and f.pid=:pid and f.rid=:rid and s.mobile=:mobile and f.status=1 and f.category=2 ORDER BY\r\n\t\t\t\t f.itemid,f.ID ASC ";
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $feebill['pid'], ':rid' => $feebill['rid'], ':mobile' => $shop['mobile']));
    }
    if (empty($data)) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where rid=:rid and hid=:hid and status=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t itemid,`ID` ASC ";
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':hid' => $_GPC['hid']));
    }
    $total = count($data);
    $totalfee = 0;
    $k = 0;
    while (!($k >= count($data))) {
        $data[$k]['region'] = $region['title'];
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
} elseif ($operation == 'list') {
    $current = '收银台';
    $myret = 0;
    $rights = $this->myrights(3, $mydo, 'list');
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $regions = pdo_fetchall($sql, $params);
    $mybuilding = array();
    $myshoplocation = array();
    $myparklocation = array();
    $mybbuilding = array();
    $myunit = array();
    $m = 0;
    while (!($m >= count($regions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $mybuilding[$regions[$m]['id']] = $buildings;
        $n = 0;
        while (!($n >= count($buildings))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid';
            $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$n]['id']));
            $myunit[$buildings[$n]['id']] = $units;
            ($n += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and isbarn=1';
        $bbuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $mybbuilding[$regions[$m]['id']] = $bbuildings;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=1';
        $shoplocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myshoplocation[$regions[$m]['id']] = $shoplocations;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2';
        $parklocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myparklocation[$regions[$m]['id']] = $parklocations;
        ($m += 1) + -1;
    }
    if ($_GPC['rid']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    if ($_GPC['bid']) {
        $condition .= ' AND bid= ' . $_GPC['bid'];
    }
    if ($_GPC['feebilltype']) {
        $condition .= ' AND category = ' . $_GPC['feebilltype'];
        if ($_GPC['feebilltype'] == 1 && $_GPC['tid']) {
            $condition .= ' AND tid= ' . $_GPC['tid'];
        }
        $select_building = array();
        $select_unit = array();
        if ($_GPC['feebilltype'] == 1) {
            $select_building = pdo_getall('rhinfo_zyxq_building', array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']), array('id', 'title'));
            $select_unit = pdo_getall('rhinfo_zyxq_unit', array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid']), array('id', 'title'));
        } elseif ($_GPC['feebilltype'] == 2) {
            $select_building = pdo_getall('rhinfo_zyxq_location', array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'category' => 1), array('id', 'title'));
        } elseif ($_GPC['feebilltype'] == 4) {
            $select_building = pdo_getall('rhinfo_zyxq_location', array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'category' => 2), array('id', 'title'));
        } elseif ($_GPC['feebilltype'] == 3) {
            $select_building = pdo_getall('rhinfo_zyxq_building', array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'isbarn' => 1), array('id', 'title'));
        }
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' OR address LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['startdate'])) {
        $starttime = strtotime($_GPC['startdate']);
        $condition .= ' and startdate>=' . $starttime;
    }
    if (!empty($_GPC['enddate'])) {
        $endtime = strtotime($_GPC['enddate']);
        $condition .= ' and enddate<=' . strtotime('+1 days', $endtime);
    }
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $totalfee = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `RID`,`BID`,`TID`,`ADDRESS` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
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
            ($k += 1) + -1;
        }
        if ($_GPC['export'] == 'export') {
            $filter = array('id' => 'ID', 'title' => '收费项目', 'region' => '小区或商圈名称', 'building' => '楼栋', 'unit' => '单元', 'address' => '房屋', 'measure' => '计量单位', 'daterange' => '账单周期', 'price' => '单价', 'fee' => '费用（元）', 'remark' => '备注');
            export_excel($data, $filter, '账单');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('paylist');
} elseif ($operation == 'three') {
    $current = '抄表账单列表';
    $myret = 0;
    $rights = $this->myrights(3, $mydo, 'three');
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $regions = pdo_fetchall($sql, $params);
    $mybuilding = array();
    $myshoplocation = array();
    $myparklocation = array();
    $m = 0;
    while (!($m >= count($regions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $mybuilding[$regions[$m]['id']] = $buildings;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=1';
        $shoplocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myshoplocation[$regions[$m]['id']] = $shoplocations;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2';
        $parklocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myparklocation[$regions[$m]['id']] = $parklocations;
        ($m += 1) + -1;
    }
    if ($_GPC['rid']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    if ($_GPC['bid']) {
        $condition .= ' AND bid= ' . $_GPC['bid'];
    }
    if ($_GPC['feebilltype']) {
        $condition .= ' AND category = ' . $_GPC['feebilltype'];
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' OR address LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['startdate'])) {
        $starttime = strtotime($_GPC['startdate']);
        $condition .= ' and startdate>=' . $starttime;
    }
    if (!empty($_GPC['enddate'])) {
        $endtime = strtotime($_GPC['enddate']);
        $condition .= ' and enddate<=' . strtotime('+1 days', $endtime);
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where feetype = 2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($_GPC['export'] == 'export') {
        $limit = '';
    }
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where feetype = 2 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `RID`,`BID`,`TID`,`ADDRESS` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
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
            $data[$k]['daterange'] = date('Y-m-d', $data[$k]['startdate']) . '~' . date('Y-m-d', $data[$k]['enddate']);
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'title' => '收费项目', 'region' => '小区名称', 'building' => '楼栋', 'room' => '房屋', 'daterange' => '账单日期', 'startqty' => '上期读数', 'endqty' => '本期读数');
            export_excel($data, $filter, '抄表信息');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    } elseif ($_GPC['export'] != '') {
        $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl($mydo, array('op' => 'three')) . $mywe['direct'], 'danger');
    }
    include $this->mywtpl('threelist');
} elseif ($operation == 'item') {
    $current = '收费项目列表';
    $myret = 0;
    $rights = $this->myrights(3, $mydo, 'item');
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
            if ($data[$k]['category'] == 0) {
                $data[$k]['building'] = '依楼宇建立';
            } elseif ($data[$k]['category'] == 1 || $data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_feelocation') . ' where id=:id and weid = :weid ';
                $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
                $data[$k]['building'] = $building;
            } else {
                $data[$k]['building'] = '无';
            }
            if ($data[$k]['calmethod'] == 1) {
                $data[$k]['calmethod'] = '按建筑面积';
            } elseif ($data[$k]['calmethod'] == 2) {
                $data[$k]['calmethod'] = '按使用面积';
            } elseif ($data[$k]['calmethod'] == 3) {
                $data[$k]['calmethod'] = '按附加面积';
            } elseif ($data[$k]['calmethod'] == 4) {
                $data[$k]['calmethod'] = '按住户';
            } elseif ($data[$k]['calmethod'] == 5) {
                $data[$k]['calmethod'] = '按车位';
            } elseif ($data[$k]['calmethod'] == 6) {
                $data[$k]['calmethod'] = '按使用数量';
            } elseif ($data[$k]['calmethod'] == 7) {
                $data[$k]['calmethod'] = '按承租人';
            } elseif ($data[$k]['calmethod'] == 8) {
                $data[$k]['calmethod'] = '按承租面积';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('itemlist');
} elseif ($operation == 'status') {
    $current = '账单状态';
    $id = intval($_GPC['id']);
    $data = array('status' => $_GPC['status']);
    $glue = 'AND';
    $result = pdo_update('rhinfo_zyxq_feebill', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'fromroom') {
    $current = '收费-';
    $myret = 1;
    $condition .= $this->myrcondition();
    $params[':pid'] = $_GPC['pid'];
    $params[':rid'] = $_GPC['rid'];
    if ($_GPC['category'] == 2) {
        $params[':lid'] = $_GPC['lid'];
        $params[':sid'] = $_GPC['sid'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where pid=:pid and rid=:rid and bid=:lid and hid=:sid and status=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t `ID` ASC ";
    } else {
        $params[':bid'] = $_GPC['bid'];
        $params[':hid'] = $_GPC['hid'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where pid=:pid and rid=:rid and bid=:bid and hid=:hid and status=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t `ID` ASC ";
    }
    $data = pdo_fetchall($sql, $params);
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
    $sql = 'select address from ' . tablename('rhinfo_zyxq_feebill') . ' where category=1 and hid=:hid and ' . $condition . ' limit 1';
    $address = pdo_fetchcolumn($sql, $params);
    $current .= $address;
    include $this->mywtpl('mybill1');
} elseif ($operation == 'print') {
    $current = '打印账单';
    if ($_W['ispost']) {
        if ($_GPC['feebilltype'] == 1) {
            $condition .= ' and pid = :pid and rid=:rid';
            $params[':pid'] = $_GPC['pid'];
            $params[':rid'] = $_GPC['rid'];
            if ($_GPC['bid']) {
                $condition .= ' and bid=:bid';
                $params[':bid'] = $_GPC['bid'];
                if ($_GPC['tid']) {
                    $condition .= ' and tid=:tid';
                    $params[':tid'] = $_GPC['tid'];
                    if ($_GPC['roomsid']) {
                        $roomsid = $_GPC['roomsid'];
                        $condition .= ' and id in(' . $roomsid . ')';
                    }
                }
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:pid ';
            $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition;
            $rooms = pdo_fetchall($sql, $params);
            $i = 0;
            $k = 0;
            while (!($k >= count($rooms))) {
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid';
                $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid'], ':tid' => $rooms[$k]['tid'], ':hid' => $rooms[$k]['id']));
                $rooms[$k]['totalfee'] = empty($totalfee) ? 0 : $totalfee;
                $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . " where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ORDER BY\r\n\t\t\t\t\t\t`BID`,`TID`,`HID`,`ID` ASC ";
                $list = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid'], ':tid' => $rooms[$k]['tid'], ':hid' => $rooms[$k]['id']));
                $rooms[$k]['billlist'] = $list;
                $rooms[$k]['address'] = '';
                if (count($list) > 0) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id=:bid';
                    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid']));
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
                    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid'], ':tid' => $rooms[$k]['tid']));
                    $rooms[$k]['address'] = $building . $unit . $list[0]['address'];
                    ($i += 1) + -1;
                }
                if ($region['feeshowtype'] == 1) {
                    $feeqrurl = $this->my_mobileurl($this->createMobileUrl('fee', array('op' => 'yearbill', 'pid' => $rooms[$k]['pid'], 'rid' => $rooms[$k]['rid'], 'bid' => $rooms[$k]['bid'], 'tid' => $rooms[$k]['tid'], 'hid' => $rooms[$k]['id'])));
                } else {
                    $feeqrurl = $this->my_mobileurl($this->createMobileUrl('fee', array('op' => 'myfeebill', 'pid' => $rooms[$k]['pid'], 'rid' => $rooms[$k]['rid'], 'bid' => $rooms[$k]['bid'], 'tid' => $rooms[$k]['tid'], 'hid' => $rooms[$k]['id'])));
                }
                $rooms[$k]['qrurl'] = $this->createqrcode($feeqrurl);
                ($k += 1) + -1;
            }
        }
        if ($_GPC['feebilltype'] == 2) {
            $condition .= ' and pid = :pid and rid=:rid';
            $params[':pid'] = $_GPC['pid'];
            $params[':rid'] = $_GPC['rid'];
            if ($_GPC['lid']) {
                $condition .= ' and lid=:lid';
                $params[':lid'] = $_GPC['lid'];
                if ($_GPC['roomsid']) {
                    $roomsid = $_GPC['roomsid'];
                    $condition .= ' and id in(' . $roomsid . ')';
                }
            }
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id=:pid ';
            $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid']));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition;
            $rooms = pdo_fetchall($sql, $params);
            $i = 0;
            $k = 0;
            while (!($k >= count($rooms))) {
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid';
                $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['lid'], ':tid' => 0, ':hid' => $rooms[$k]['id']));
                $rooms[$k]['totalfee'] = empty($totalfee) ? 0 : $totalfee;
                $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . " where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid ORDER BY\r\n\t\t\t\t\t\t`BID`,`TID`,`HID`,`ID` ASC ";
                $list = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['lid'], ':tid' => 0, ':hid' => $rooms[$k]['id']));
                $rooms[$k]['billlist'] = $list;
                $rooms[$k]['address'] = '';
                if (count($list) > 0) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id=:bid';
                    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['lid']));
                    $rooms[$k]['address'] = $building . $list[0]['address'];
                    ($i += 1) + -1;
                }
                if ($region['feeshowtype'] == 1) {
                    $feeqrurl = $this->my_mobileurl($this->createMobileUrl('fee', array('op' => 'yearbill', 'pid' => $rooms[$k]['pid'], 'rid' => $rooms[$k]['rid'], 'bid' => $rooms[$k]['lid'], 'tid' => 0, 'hid' => $rooms[$k]['id'])));
                } else {
                    $feeqrurl = $this->my_mobileurl($this->createMobileUrl('fee', array('op' => 'myfeebill', 'pid' => $rooms[$k]['pid'], 'rid' => $rooms[$k]['rid'], 'bid' => $rooms[$k]['lid'], 'tid' => 0, 'hid' => $rooms[$k]['id'])));
                }
                $rooms[$k]['qrurl'] = $this->createqrcode($feeqrurl);
                ($k += 1) + -1;
            }
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
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $mylocation = array();
    $myshop = array();
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
                $j = 0;
                while (!($j >= count($units))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid';
                    $rooms = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$n]['id'], ':tid' => $units[$j]['id']));
                    $myroom[$units[$j]['id']] = $rooms;
                    ($j += 1) + -1;
                }
                ($n += 1) + -1;
            }
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=1';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mylocation[$regions[$m]['id']] = $locations;
            $n = 0;
            while (!($n >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $shops = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$n]['id']));
                $myshop[$locations[$n]['id']] = $shops;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('print');
} elseif ($operation == 'sendsinglemsg') {
    $current = '缴费通知';
    $sendtype = $_GPC['sendtype'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
    if ($sendtype == '2') {
        if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0)) {
            echo '可发短信数量为0,请充值';
            exit(0);
        }
    }
    if ($sendtype == '1') {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where isowner=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0 and category=1';
        $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
        if (empty($member)) {
            echo '未绑定微信，发送失败!';
            exit(0);
        }
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=1';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
        $totalfee = empty($totalfee) ? 0 : $totalfee;
        if ($totalfee > 0) {
            $feecolor = empty($this->syscfg['feecolor']) ? '#000' : $this->syscfg['feecolor'];
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            if ($region['isonlinepay'] == 1) {
                $url = $this->createMobileUrl('fee', array('op' => 'yearbill', 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $_GPC['hid']));
            } else {
                $url = $this->createMobileUrl('fee', array('op' => 'myfeebill', 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $_GPC['hid']));
            }
            $url = $this->my_mobileurl($url);
            $postdata = array('first' => array('value' => '您好，您本月物业费账单已出。'), 'userName' => array('value' => $member['realname']), 'address' => array('value' => $member['address']), 'pay' => array('value' => $totalfee, 'color' => $feecolor), 'remark' => array('value' => '请尽快缴纳，如有疑问，请咨询：' . $region['telphone']));
            if (!empty($this->syscfg['tplid12'])) {
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid12'], $postdata, $url, $topcolor);
            }
        } else {
            echo '暂未欠费，发送失败!';
            exit(0);
        }
    }
    if ($sendtype == '2') {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id=:hid ';
        $room = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
        $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=1';
        $mindate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=1';
        $maxdate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
        if ($mindate && $maxdate) {
            $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
        } else {
            $daterange = '本期账单';
        }
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=1';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid']));
        $totalfee = empty($totalfee) ? 0 : $totalfee;
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':bid' => $room['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid and pid=:pid and rid=:rid';
        $unit = pdo_fetchcolumn($sql, array(':id' => $room['tid'], ':weid' => $room['weid'], ':pid' => $room['pid'], ':rid' => $room['rid']));
        if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
            if (!empty($room['mobile']) && $room['isnotice']) {
                $res = $this->send_sms($this->syscfg['smstype'], $room['mobile'], $this->syscfg['feeid'], array('name' => $region['title'] . $building . $unit . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                if ($res['status'] == 1) {
                    $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                    pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                    $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '缴费通知', 'io' => 2, 'mobile' => $room['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                }
            }
            if (!empty($room['mobile1']) && $room['isnotice']) {
                $res = $this->send_sms($this->syscfg['smstype'], $room['mobile1'], $this->syscfg['feeid'], array('name' => $region['title'] . $building . $unit . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid = :weid and and rid = :rid and bid=:bid and tid=:tid and hid=:hid';
            $room_mps = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':bid' => $room['bid'], ':tid' => $room['tid'], ':hid' => $room['id']));
            $m = 0;
            while (!($m >= count($room_mps))) {
                if (!empty($room_mps[$m]['mobile']) && $room_mps[$m]['isnotice'] == 1) {
                    $res = $this->send_sms($this->syscfg['smstype'], $room_mps[$m]['mobile'], $this->syscfg['noticeid'], array('name' => $region['title'] . $building . $unit . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                    if ($res['status'] == 1) {
                        $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                        pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                        $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '缴费通知', 'io' => 2, 'mobile' => $room_mps[$m]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                    }
                }
                ($m += 1) + -1;
            }
        } else {
            echo '短信参数配置错误';
            exit(0);
        }
    }
    echo 'ok';
    exit(0);
} elseif ($operation == 'sendsinglemsg_shop') {
    $current = '缴费通知';
    $sendtype = $_GPC['sendtype'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
    if ($sendtype == '2') {
        if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0)) {
            echo '可发短信数量为0,请充值';
            exit(0);
        }
    }
    if ($sendtype == '1') {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where isowner=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0 and category=1';
        $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        if (empty($member)) {
            echo '未绑定微信，发送失败!';
            exit(0);
        }
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        $totalfee = empty($totalfee) ? 0 : $totalfee;
        if ($totalfee > 0) {
            $feecolor = empty($this->syscfg['feecolor']) ? '#000' : $this->syscfg['feecolor'];
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $url = $this->createMobileUrl('member', array('op' => 'myfeebill', 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['lid'], 'tid' => $_GPC['tid'], 'hid' => $_GPC['sid']));
            $url = $this->my_mobileurl($url);
            $postdata = array('first' => array('value' => '您好，您本月物业费账单已出。'), 'userName' => array('value' => $member['realname']), 'address' => array('value' => $member['address']), 'pay' => array('value' => $totalfee, 'color' => $feecolor), 'remark' => array('value' => '请尽快缴纳，如有疑问，请咨询：' . $region['telphone']));
            if (!empty($this->syscfg['tplid12'])) {
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid12'], $postdata, $url, $topcolor);
            }
        } else {
            echo '暂未欠费，发送失败!';
            exit(0);
        }
    }
    if ($sendtype == '2') {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid=:weid and pid=:pid and rid=:rid and lid=:lid and id=:sid';
        $room = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':sid' => $_GPC['sid']));
        if (empty($room['mobile'])) {
            echo '未登记手机，发送失败！';
            exit(0);
        }
        $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=1';
        $mindate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=1';
        $maxdate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        if ($mindate && $maxdate) {
            $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
        } else {
            $daterange = '本期账单';
        }
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=2';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        $totalfee = empty($totalfee) ? 0 : $totalfee;
        if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
            if (!empty($room['mobile']) && $room['isnotice'] == 1) {
                $res = $this->send_sms($this->syscfg['smstype'], $room['mobile'], $this->syscfg['feeid'], array('name' => $region['title'] . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                if ($res['status'] == 1) {
                    $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                    pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                    $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '缴费通知', 'io' => 2, 'mobile' => $room['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                }
            }
            if (!empty($room['mobile1']) && $room['isnotice'] == 1) {
                $res = $this->send_sms($this->syscfg['smstype'], $room['mobile1'], $this->syscfg['feeid'], array('name' => $region['title'] . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_shop_mp') . ' where weid = :weid and and rid = :rid and lid=:lid and sid=:sid';
            $room_mps = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':lid' => $room['lid'], ':sid' => $room['id']));
            $m = 0;
            while (!($m >= count($room_mps))) {
                if (!empty($room_mps[$m]['mobile']) && $room_mps[$m]['isnotice'] == 1) {
                    $res = $this->send_sms($this->syscfg['smstype'], $room_mps[$m]['mobile'], $this->syscfg['noticeid'], array('name' => $region['title'] . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                    if ($res['status'] == 1) {
                        $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                        pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                        $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '缴费通知', 'io' => 2, 'mobile' => $room_mps[$m]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                    }
                }
                ($m += 1) + -1;
            }
        } else {
            echo '短信参数配置错误';
            exit(0);
        }
    }
    echo 'ok';
    exit(0);
} elseif ($operation == 'sendsinglemsg_park') {
    $current = '缴费通知';
    $sendtype = $_GPC['sendtype'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    if ($sendtype == '2') {
        if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0)) {
            echo '可发短信数量为0,请充值';
            exit(0);
        }
    }
    if ($sendtype == '1') {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where isowner=1 and weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0 and category=3';
        $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        if (empty($member)) {
            echo '未绑定微信，发送失败!';
            exit(0);
        }
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=4 ';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['parkid']));
        $totalfee = empty($totalfee) ? 0 : $totalfee;
        if ($totalfee > 0) {
            $feecolor = empty($this->syscfg['feecolor']) ? '#000' : $this->syscfg['feecolor'];
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $url = $this->createMobileUrl('member', array('op' => 'myfeebill', 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['lid'], 'tid' => $_GPC['tid'], 'hid' => $_GPC['parkid']));
            $url = $this->my_mobileurl($url);
            $postdata = array('first' => array('value' => '您好，您本月物业费账单已出。'), 'userName' => array('value' => $member['realname']), 'address' => array('value' => $member['address']), 'pay' => array('value' => $totalfee, 'color' => $feecolor), 'remark' => array('value' => '请尽快缴纳，如有疑问，请咨询：' . $region['telphone']));
            if (!empty($this->syscfg['tplid12'])) {
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid12'], $postdata, $url, $topcolor);
            }
        } else {
            echo '暂未欠费，发送失败!';
            exit(0);
        }
    }
    if ($sendtype == '2') {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and  rid=:rid and lid=:lid and id=:parkid';
        $room = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':lid' => $_GPC['lid'], ':parkid' => $_GPC['parkid']));
        if (empty($room['mobile'])) {
            echo '未登记手机，发送失败！';
            exit(0);
        }
        $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=4 ';
        $mindate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=4';
        $maxdate = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['parkid']));
        if ($mindate && $maxdate) {
            $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
        } else {
            $daterange = '本期账单';
        }
        $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and category=4';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['lid'], ':tid' => 0, ':hid' => $_GPC['sid']));
        $totalfee = empty($totalfee) ? 0 : $totalfee;
        if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
            if (!empty($room['mobile']) && $room['isnotice'] == 1) {
                $res = $this->send_sms($this->syscfg['smstype'], $room['mobile'], $this->syscfg['feeid'], array('name' => $region['title'] . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
                if ($res['status'] == 1) {
                    $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                    pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                    $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '缴费通知', 'io' => 2, 'mobile' => $room['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                }
            }
            if (!empty($room['mobile1'])) {
                $res = $this->send_sms($this->syscfg['smstype'], $room['mobile1'], $this->syscfg['feeid'], array('name' => $region['title'] . $room['title'], 'money' => $totalfee, 'daterange' => $daterange, 'phone' => $region['telphone']));
            }
        } else {
            echo '短信参数配置错误';
            exit(0);
        }
    }
    echo 'ok';
    exit(0);
} elseif ($operation == 'locationlist') {
    $navtitle = '收费分组';
    $current = $_GPC['category'] == '1' ? ' 楼宇分组列表' : ' 商铺分组列表';
    $myret = 1;
    $rights = $this->myrights(3, $mydo, 'list');
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_feelocation') . ' where category=:category and ' . $condition;
    $params['category'] = $_GPC['category'];
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feelocation') . ' where category=:category and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
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
    include $this->mywtpl('locationlist');
} elseif ($operation == 'roompricelist') {
    $navtitle = '房产个性单价';
    $current = '房产单价列表';
    $myret = 1;
    $rights = $this->myrights(3, $mydo, 'list');
    $condition .= ' and pid=:pid and rid=:rid and itemid=:itemid and category=:category';
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_roomprice') . ' where ' . $condition;
    $params[':pid'] = $_GPC['pid'];
    $params[':rid'] = $_GPC['rid'];
    $params[':itemid'] = $_GPC['itemid'];
    $params[':category'] = $_GPC['category'];
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_roomprice') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['category'] == 2) {
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
                $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
                $room = pdo_fetchcolumn($sql, array(':id' => $data[$k]['hid'], ':weid' => $mywe['weid']));
                $data[$k]['room'] = $room;
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('roompricelist');
}
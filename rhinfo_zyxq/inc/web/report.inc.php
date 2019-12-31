<?php

if (!(bool) defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'paylist';
date_default_timezone_set('Asia/Shanghai');
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'report';
$condition = ' weid = :weid ';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '决策分析';
$rights = $this->myrights(4, $mydo, $operation);
if ($operation == 'paylist') {
    $current = '缴费统计';
    $myret = 0;
    $paytype = $this->paytype;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $category = !empty($_GPC['category']) ? $_GPC['category'] : 1;
    $condition .= ' AND category = ' . $category;
    if (!empty($_GPC['rid'])) {
        if ($category == 1) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and (category=0 or category=:category)';
            $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':category' => $category));
        } else {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and category=:category';
            $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':category' => $category));
        }
    } elseif ($category == 1) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and (category=0 or category=:category)';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $myregions[0]['id'], ':category' => $category));
    } else {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and category=:category';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $myregions[0]['id'], ':category' => $category));
    }
    if (empty($defaultfeeitems)) {
        $defaultfeeitems = array();
        $defaultfeeitems[] = array('id' => 0, 'title' => '无');
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $defaultrid = !empty($_GPC['rid']) ? $_GPC['rid'] : $myregions[0]['id'];
    $defaultrid = !empty($defaultrid) ? $defaultrid : 0;
    $condition .= ' AND rid= ' . $defaultrid;
    $paybill = array();
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =0 and ' . $condition;
    $paybill['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =1 and ' . $condition;
    $paybill['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . $starttime . ' and paydate<=' . $endtime . ' and ' . $condition;
    $paybill['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $paybill['nopay'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and paydate>=' . $starttime . ' and paydate<=' . $endtime;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t`PAYDATE` DESC, `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        load()->model('mc');
        $fans = array();
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            }
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            if ($data[$k]['category'] == 1) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 3) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_garage') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 4) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_parking') . ' where id = :id and weid = :weid';
            }
            $room = pdo_fetchcolumn($sql, array(':id' => $data[$k]['hid'], ':weid' => $mywe['weid']));
            $data[$k]['room'] = $room;
            $data[$k]['paytype'] = $paytype[$data[$k]['paytype']];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = array();
    $color[0] = 'rgba(203,48,48,';
    $color[1] = 'rgba(149,192,0,';
    $color[2] = 'rgba(31,194,121,';
    $color[3] = 'rgba(194,31,194,';
    $color[4] = 'rgba(30,91,214,';
    $color[5] = 'rgba(42,235,219,';
    $color[6] = 'rgba(217,207,13,';
    $color[7] = 'rgba(56,9,16,';
    $color[8] = 'rgba(23,71,4,';
    $color[9] = 'rgba(0,0,0,';
    $k = 0;
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $defaultfeeitems[$m]['color'] = $color[$k];
        $labelcolor = 'fillColor : "' . $color[$k] . '0.1)",strokeColor : "' . $color[$k] . '1)",pointColor : "' . $color[$k] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[$k] . '1)"';
        if ($k == 0) {
            $feeitem_tpl .= 'feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        } else {
            $feeitem_tpl .= ', feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        }
        ($k += 1) + -1;
        ($m += 1) + -1;
    }
    $chartitems = $defaultfeeitems;
    $feeitem_tpl = '{' . $feeitem_tpl . '}';
    $ds_data_temp = '';
    $ds_temp = '';
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $ds_data_temp .= 'ds.feeitem' . $m . '.data = datasets.feeitem' . $m . ';';
        $ds_temp .= 'ds.feeitem' . $m . ',';
        ($m += 1) + -1;
    }
    $stat = array();
    $label = array();
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $label[] = $key;
            $m = 0;
            while (!($m >= count($defaultfeeitems))) {
                $stat['feeitem' . $m][$key] = 0;
                ($m += 1) + -1;
            }
            ($i += 1) + -1;
        }
        $sql = 'select title,paydate,payfee,fee from ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . $starttime . ' and paydate<=' . $endtime . ' and ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $n = 0;
            while (!($n >= count($chart_data))) {
                $key = date('m-d', $chart_data[$n]['paydate']);
                $m = 0;
                while (!($m >= count($defaultfeeitems))) {
                    if ($chart_data[$n]['title'] == $defaultfeeitems[$m]['title']) {
                        $stat['feeitem' . $m][$key] = $stat['feeitem' . $m][$key] + $chart_data[$n]['payfee'];
                    }
                    ($m += 1) + -1;
                }
                ($n += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = $label;
        $out_temp = array();
        $m = 0;
        while (!($m >= count($defaultfeeitems))) {
            $out_temp['feeitem' . $m] = array_values($stat['feeitem' . $m]);
            ($m += 1) + -1;
        }
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('catepaylist');
} elseif ($operation == 'paylist1') {
    $current = '缴费统计';
    $myret = 0;
    $rights = $this->myrights(4, $mydo, 'feelist');
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $myfeeitems = array();
    $defaultrid = 0;
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and pid=:pid and rid=:rid';
        $feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $myfeeitems[$myregions[$k]['id']] = $feeitems;
        if ($k == 0) {
            $defaultrid = $myregions[$k]['id'];
            $defaultfeeitems = $feeitems;
            $select_feeitems = $feeitems;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['myfeeitem']) {
        $condition .= ' AND itemid= \'' . $_GPC['myfeeitem'] . '\'';
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and id=:id';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':id' => $_GPC['myfeeitem']));
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid';
        $select_feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    } elseif ($_GPC['rid']) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $select_feeitems = $defaultfeeitems;
    }
    $defaultfeeitems = empty($defaultfeeitems) ? array(array('id' => 0, 'title' => '无')) : $defaultfeeitems;
    if ($_GPC['rid']) {
        $defaultrid = $_GPC['rid'];
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $condition .= ' AND rid= ' . $defaultrid;
    $paybill = array();
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =0 and ' . $condition;
    $paybill['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =1 and ' . $condition;
    $paybill['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . $starttime . ' and paydate<=' . $endtime . ' and ' . $condition;
    $paybill['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $paybill['nopay'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and paydate>=' . $starttime . ' and paydate<=' . $endtime;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            }
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            if ($data[$k]['category'] == 1) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 3) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_garage') . ' where id = :id and weid = :weid';
            }
            $room = pdo_fetchcolumn($sql, array(':id' => $data[$k]['hid'], ':weid' => $mywe['weid']));
            $data[$k]['room'] = $room;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = array();
    $color[0] = 'rgba(203,48,48,';
    $color[1] = 'rgba(149,192,0,';
    $color[2] = 'rgba(31,194,121,';
    $color[3] = 'rgba(194,31,194,';
    $color[4] = 'rgba(30,91,214,';
    $color[5] = 'rgba(42,235,219,';
    $color[6] = 'rgba(217,207,13,';
    $color[7] = 'rgba(56,9,16,';
    $color[8] = 'rgba(23,71,4,';
    $color[9] = 'rgba(0,0,0,';
    $k = 0;
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $defaultfeeitems[$m]['color'] = $color[$k];
        $labelcolor = 'fillColor : "' . $color[$k] . '0.1)",strokeColor : "' . $color[$k] . '1)",pointColor : "' . $color[$k] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[$k] . '1)"';
        if ($k == 0) {
            $feeitem_tpl .= 'feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        } else {
            $feeitem_tpl .= ', feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        }
        ($k += 1) + -1;
        ($m += 1) + -1;
    }
    $chartitems = $defaultfeeitems;
    $feeitem_tpl = '{' . $feeitem_tpl . '}';
    $ds_data_temp = '';
    $ds_temp = '';
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $ds_data_temp .= 'ds.feeitem' . $m . '.data = datasets.feeitem' . $m . ';';
        $ds_temp .= 'ds.feeitem' . $m . ',';
        ($m += 1) + -1;
    }
    $stat = array();
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $m = 0;
            while (!($m >= count($defaultfeeitems))) {
                $stat['feeitem' . $m][$key] = 0;
                ($m += 1) + -1;
            }
            ($i += 1) + -1;
        }
        $sql = 'select title,paydate,payfee,fee from ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paydate>0 and paydate>=' . $starttime . ' and paydate<=' . $endtime . ' and ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $n = 0;
            while (!($n >= count($chart_data))) {
                $key = date('m-d', $chart_data[$n]['paydate']);
                $m = 0;
                while (!($m >= count($defaultfeeitems))) {
                    if ($chart_data[$n]['title'] == $defaultfeeitems[$m]['title']) {
                        $stat['feeitem' . $m][$key] = $stat['feeitem' . $m][$key] + $chart_data[$n]['payfee'];
                    }
                    ($m += 1) + -1;
                }
                ($n += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = array_keys($stat['feeitem0']);
        $out_temp = array();
        $m = 0;
        while (!($m >= count($defaultfeeitems))) {
            $out_temp['feeitem' . $m] = array_values($stat['feeitem' . $m]);
            ($m += 1) + -1;
        }
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('paylist');
} elseif ($operation == 'billlist') {
    $current = '未缴费统计';
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $category = !empty($_GPC['category']) ? $_GPC['category'] : 1;
    $condition .= ' AND category = ' . $category;
    if (!empty($_GPC['rid'])) {
        if ($category == 1) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and (category=0 or category=:category)';
            $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':category' => $category));
        } else {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and category=:category';
            $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':category' => $category));
        }
    } elseif ($category == 1) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and (category=0 or category=:category)';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $myregions[0]['id'], ':category' => $category));
    } else {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and category=:category';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $myregions[0]['id'], ':category' => $category));
    }
    if (empty($defaultfeeitems)) {
        $defaultfeeitems = array();
        $defaultfeeitems[] = array('id' => 0, 'title' => '无');
    }
    $billdate = $_GPC['billdate'];
    if ($billdate) {
        $starttime = strtotime($billdate);
    } else {
        $starttime = TIMESTAMP;
    }
    $defaultrid = !empty($_GPC['rid']) ? $_GPC['rid'] : $myregions[0]['id'];
    $defaultrid = !empty($defaultrid) ? $defaultrid : 0;
    $condition .= ' AND rid= ' . $defaultrid;
    $paybill = array();
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and datediff(DATE_FORMAT(FROM_UNIXTIME(' . $starttime . '),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(startdate),\'%y-%m-%d\')) >30 and ' . $condition;
    $paybill['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and datediff(DATE_FORMAT(FROM_UNIXTIME(' . $starttime . '),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(startdate),\'%y-%m-%d\')) >120 and ' . $condition;
    $paybill['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and datediff(DATE_FORMAT(FROM_UNIXTIME(' . $starttime . '),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(startdate),\'%y-%m-%d\')) >360 and ' . $condition;
    $paybill['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $paybill['nopay'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and startdate<=' . $starttime;
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $m = 0;
        while (!($m >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$m]['rid'], ':weid' => $mywe['weid']));
            $data[$m]['region'] = $region;
            if ($data[$m]['category'] == 1 || $data[$m]['category'] == 3) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            } elseif ($data[$m]['category'] == 2 || $data[$m]['category'] == 4) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            }
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$m]['bid'], ':weid' => $mywe['weid']));
            $data[$m]['building'] = $building;
            ($m += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = array();
    $color[0] = 'rgba(203,48,48,';
    $color[1] = 'rgba(149,192,0,';
    $color[2] = 'rgba(31,194,121,';
    $color[3] = 'rgba(194,31,194,';
    $color[4] = 'rgba(30,91,214,';
    $color[5] = 'rgba(42,235,219,';
    $color[6] = 'rgba(217,207,13,';
    $color[7] = 'rgba(56,9,16,';
    $color[8] = 'rgba(23,71,4,';
    $color[9] = 'rgba(0,0,0,';
    $k = 0;
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $defaultfeeitems[$m]['color'] = $color[$k];
        $labelcolor = 'fillColor : "' . $color[$k] . '0.1)",strokeColor : "' . $color[$k] . '1)",pointColor : "' . $color[$k] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[$k] . '1)"';
        if ($k == 0) {
            $feeitem_tpl .= 'feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        } else {
            $feeitem_tpl .= ', feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        }
        ($k += 1) + -1;
        ($m += 1) + -1;
    }
    $chartitems = $defaultfeeitems;
    $feeitem_tpl = '{' . $feeitem_tpl . '}';
    $ds_data_temp = '';
    $ds_temp = '';
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $ds_data_temp .= 'ds.feeitem' . $m . '.data = datasets.feeitem' . $m . ';';
        $ds_temp .= 'ds.feeitem' . $m . ',';
        ($m += 1) + -1;
    }
    if ($category == 1) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $defaultrid));
    } elseif ($category == 2) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid=:weid and rid=:rid and category=1';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $defaultrid));
    } elseif ($category == 3) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid and isbarn=1';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $defaultrid));
    } elseif ($category == 4) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid=:weid and rid=:rid and category=2';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $defaultrid));
    }
    if (empty($building)) {
        $buildings = array();
        $buildings[] = array('id' => 0, 'title' => '无');
    }
    $stat = array();
    $label = array();
    if ($_W['isajax']) {
        $m = 0;
        while (!($m >= count($buildings))) {
            $key = $buildings[$m]['id'];
            $label[] = $buildings[$m]['title'];
            $n = 0;
            while (!($n >= count($defaultfeeitems))) {
                $stat['feeitem' . $n][$key] = 0;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        $sql = 'select title,fee,bid from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and  startdate<=' . $starttime . ' and ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $m = 0;
            while (!($m >= count($chart_data))) {
                $key = $chart_data[$m]['bid'];
                $n = 0;
                while (!($n >= count($defaultfeeitems))) {
                    if ($chart_data[$m]['title'] == $defaultfeeitems[$n]['title']) {
                        $stat['feeitem' . $n][$key] = $stat['feeitem' . $n][$key] + $chart_data[$m]['fee'];
                    }
                    ($n += 1) + -1;
                }
                ($m += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = $label;
        $out_temp = array();
        $m = 0;
        while (!($m >= count($defaultfeeitems))) {
            $out_temp['feeitem' . $m] = array_values($stat['feeitem' . $m]);
            ($m += 1) + -1;
        }
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('catebilllist');
} elseif ($operation == 'billlist1') {
    $current = '未缴费统计';
    $myret = 0;
    $rights = $this->myrights(4, $mydo, 'feelist');
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $myfeeitems = array();
    $defaultrid = 0;
    $m = 0;
    while (!($m >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and pid=:pid and rid=:rid';
        $feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$m]['pid'], ':rid' => $myregions[$m]['id']));
        $myfeeitems[$myregions[$m]['id']] = $feeitems;
        if ($m == 0) {
            $defaultrid = $myregions[$m]['id'];
            $defaultfeeitems = $feeitems;
            $select_feeitems = $feeitems;
        }
        ($m += 1) + -1;
    }
    if ($_GPC['myfeeitem']) {
        $condition .= ' AND itemid= \'' . $_GPC['myfeeitem'] . '\'';
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid and id=:id';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':id' => $_GPC['myfeeitem']));
    } elseif ($_GPC['rid']) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_feeitem') . ' where status = 1 and weid=:weid and rid=:rid';
        $defaultfeeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    }
    $defaultfeeitems = empty($defaultfeeitems) ? array(array('id' => 0, 'title' => '无')) : $defaultfeeitems;
    if ($_GPC['rid']) {
        $defaultrid = $_GPC['rid'];
    }
    $billdate = $_GPC['billdate'];
    if ($billdate) {
        $starttime = strtotime($billdate);
    } else {
        $starttime = TIMESTAMP;
    }
    $condition .= ' AND rid= ' . $defaultrid;
    $paybill = array();
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and datediff(DATE_FORMAT(FROM_UNIXTIME(' . $starttime . '),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(startdate),\'%y-%m-%d\')) >30 and ' . $condition;
    $paybill['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and datediff(DATE_FORMAT(FROM_UNIXTIME(' . $starttime . '),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(startdate),\'%y-%m-%d\')) >120 and ' . $condition;
    $paybill['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and datediff(DATE_FORMAT(FROM_UNIXTIME(' . $starttime . '),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(startdate),\'%y-%m-%d\')) >360 and ' . $condition;
    $paybill['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $paybill['nopay'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and startdate<=' . $starttime;
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $m = 0;
        while (!($m >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$m]['rid'], ':weid' => $mywe['weid']));
            $data[$m]['region'] = $region;
            if ($data[$m]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            }
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$m]['bid'], ':weid' => $mywe['weid']));
            $data[$m]['building'] = $building;
            ($m += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = array();
    $color[0] = 'rgba(203,48,48,';
    $color[1] = 'rgba(149,192,0,';
    $color[2] = 'rgba(31,194,121,';
    $color[3] = 'rgba(194,31,194,';
    $color[4] = 'rgba(30,91,214,';
    $color[5] = 'rgba(42,235,219,';
    $color[6] = 'rgba(217,207,13,';
    $color[7] = 'rgba(56,9,16,';
    $color[8] = 'rgba(23,71,4,';
    $color[9] = 'rgba(0,0,0,';
    $k = 0;
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $defaultfeeitems[$m]['color'] = $color[$k];
        $labelcolor = 'fillColor : "' . $color[$k] . '0.1)",strokeColor : "' . $color[$k] . '1)",pointColor : "' . $color[$k] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[$k] . '1)"';
        if ($k == 0) {
            $feeitem_tpl .= 'feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        } else {
            $feeitem_tpl .= ', feeitem' . $k . ':{label:\'' . $defaultfeeitems[$m]['title'] . '\',' . $labelcolor . '}';
        }
        ($k += 1) + -1;
        ($m += 1) + -1;
    }
    $chartitems = $defaultfeeitems;
    $feeitem_tpl = '{' . $feeitem_tpl . '}';
    $ds_data_temp = '';
    $ds_temp = '';
    $m = 0;
    while (!($m >= count($defaultfeeitems))) {
        $ds_data_temp .= 'ds.feeitem' . $m . '.data = datasets.feeitem' . $m . ';';
        $ds_temp .= 'ds.feeitem' . $m . ',';
        ($m += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $defaultrid));
    $buildings = empty($buildings) ? array(array('id' => 0, 'title' => '无楼宇')) : $buildings;
    if ($_GPC['isroom']) {
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and startdate<=' . $starttime . ' and ' . $condition;
        $group_total = pdo_fetchcolumn($sql, $params);
        if ($group_total > 0) {
            $sql = 'select pid,rid,bid,hid,address,sum(fee) as fee from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and  startdate<=' . $starttime . ' and ' . $condition . " group by hid ORDER BY\r\n\t\t\t\t\t\t `fee` DESC " . $limit;
            $group_datas = pdo_fetchall($sql, $params);
            $n = 0;
            while (!($n >= count($group_datas))) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
                $region = pdo_fetchcolumn($sql, array(':id' => $group_datas[$n]['rid'], ':weid' => $mywe['weid']));
                $group_datas[$n]['region'] = $region;
                if ($group_datas[$n]['category'] == 2) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
                } else {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
                }
                $building = pdo_fetchcolumn($sql, array(':id' => $group_datas[$n]['bid'], ':weid' => $mywe['weid']));
                $group_datas[$n]['building'] = $building;
                ($n += 1) + -1;
            }
        }
        $pager = pagination($group_total, $pindex, $psize);
        include $this->mywtpl('billlist');
        exit(0);
    }
    $stat = array();
    $lable = array();
    if ($_W['isajax']) {
        $m = 0;
        while (!($m >= count($buildings))) {
            $key = $buildings[$m]['id'];
            $lable[] = $buildings[$m]['title'];
            $n = 0;
            while (!($n >= count($defaultfeeitems))) {
                $stat['feeitem' . $n][$key] = 0;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        $sql = 'select title,fee,bid from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and  startdate<=' . $starttime . ' and ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $m = 0;
            while (!($m >= count($chart_data))) {
                $key = $chart_data[$m]['bid'];
                $n = 0;
                while (!($n >= count($defaultfeeitems))) {
                    if ($chart_data[$m]['title'] == $defaultfeeitems[$n]['title']) {
                        $stat['feeitem' . $n][$key] = $stat['feeitem' . $n][$key] + $chart_data[$m]['fee'];
                    }
                    ($n += 1) + -1;
                }
                ($m += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = $lable;
        $out_temp = array();
        $m = 0;
        while (!($m >= count($defaultfeeitems))) {
            $out_temp['feeitem' . $m] = array_values($stat['feeitem' . $m]);
            ($m += 1) + -1;
        }
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('billlist');
} elseif ($operation == 'mybill') {
    $condition .= $this->myrcondition();
    if (!empty($_GPC['rid'])) {
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $_GPC['rid'];
    }
    if (!empty($_GPC['category'])) {
        $condition .= ' and category=:category ';
        $params[':category'] = $_GPC['category'];
    }
    if (!empty($_GPC['hid'])) {
        $condition .= ' and hid=:hid ';
        $params[':hid'] = $_GPC['hid'];
    }
    if (!empty($_GPC['status'])) {
        $condition .= ' and status=:status ';
        $params[':status'] = $_GPC['status'];
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t `ID` ASC ";
    $data = pdo_fetchall($sql, $params);
    $total = count($data);
    $totalfee = 0;
    $k = 0;
    while (!($k >= count($data))) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
        $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
        $data[$k]['region'] = $region;
        if ($data[$k]['category'] == 2) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
        } else {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
        }
        $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
        $data[$k]['building'] = $building;
        $totalfee += $data[$k]['fee'];
        ($k += 1) + -1;
    }
    include $this->mywtpl('mybill');
} elseif ($operation == 'bindlist') {
    $current = '绑定统计';
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        ($k += 1) + -1;
    }
    if ($_GPC['bid']) {
        $condition .= ' AND bid= \'' . $_GPC['bid'] . '\'';
    }
    if (!empty($_GPC['rid'])) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $bindroom = array();
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition;
    $bindroom['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition;
    $bindroom['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['month'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where ' . $condition;
    $bindroom['total'] = pdo_fetchcolumn($sql, $params);
    $total = $bindroom['total'];
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
            }
            $building = pdo_fetchcolumn($sql, array(':id' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            $data[$k]['building'] = $building;
            if ($data[$k]['category'] == 1) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 2) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
            } elseif ($data[$k]['category'] == 3) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_garage') . ' where id = :id and weid = :weid';
            }
            $room = pdo_fetchcolumn($sql, array(':id' => $data[$k]['hid'], ':weid' => $mywe['weid']));
            $data[$k]['room'] = $room;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = 'rgba(203,48,48,';
    $labelcolor = 'fillColor : "' . $color . '0.1)",strokeColor : "' . $color . '1)",pointColor : "' . $color . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color . '1)"';
    $lable_tpl .= 'bind:{label:\'绑定人数\',' . $labelcolor . '}';
    $lable_tpl = '{' . $lable_tpl . '}';
    $ds_data_temp = 'ds.bind.data = datasets.bind;';
    $ds_temp = 'ds.bind';
    $stat = array();
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $stat['bind'][$key] = 0;
            ($i += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $k = 0;
            while (!($k >= count($chart_data))) {
                $key = date('m-d', $chart_data[$k]['ctime']);
                $stat['bind'][$key] = $stat['bind'][$key] + 1;
                ($k += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = array_keys($stat['bind']);
        $out['datasets'] = array('bind' => array_values($stat['bind']));
        exit(json_encode($out));
    }
    include $this->mywtpl('bindlist');
} elseif ($operation == 'repairlist') {
    $current = '报修统计';
    $myret = 0;
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    if ($_W['isajax']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
        $repair_array = array(0, 0, 0, 0, 0, 0);
        $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repair') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' group by status';
        $repair = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($repair))) {
            if ($repair[$k]['status'] == 5) {
                $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 9) {
                $repair_array[5] = $repair_array[5] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 0) {
                $repair_array[1] = $repair_array[1] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 8) {
                $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
            } else {
                $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
            }
            ($k += 1) + -1;
        }
        $repair_cates = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_category') . ' where ' . $condition . ' and type=2 order by title asc', $params);
        $repair_cate = array();
        $k = 0;
        while (!($k >= count($repair_cates))) {
            $repair_cate[$repair_cates[$k]['id']] = $repair_cates[$k]['title'];
            ($k += 1) + -1;
        }
        ksort($repair_cate);
        $sql = 'select cid,count(cid) as cate_num from ' . tablename('rhinfo_zyxq_repair') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' group by cid';
        $category = pdo_fetchall($sql, $params);
        $category_array = array();
        $k = 0;
        while (!($k >= count($category))) {
            $category_array[$k] = 0;
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($category))) {
            if (array_key_exists($category[$k]['cid'], $repair_cate)) {
                $category_array[$category[$k]['cid']] = $category[$k]['cate_num'];
            }
            ($k += 1) + -1;
        }
        $count = array_values($category_array);
        $name = array_values($repair_cate);
        $res = array();
        $k = 0;
        while (!($k >= count($count))) {
            $res[$k]['value'] = $count[$k];
            $res[$k]['name'] = $name[$k];
            ($k += 1) + -1;
        }
        $ret = array('ajaxrepair' => $repair_array, 'ajaxcategory' => array('count' => $count, 'name' => $name, 'data' => $res));
        exit(json_encode($ret));
    }
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $defaultrid = 0;
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        if ($k == 0) {
            $defaultrid = $myregions[$k]['id'];
            $defaultbuildings = $buildings;
            $select_buildings = $buildings;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['rid']) {
        $defaultrid = $_GPC['rid'];
    }
    $condition .= ' AND rid= ' . $defaultrid;
    $bindroom = array();
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repair') . ' where (status =0 or status =1) and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repair') . ' where status =2 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repair') . ' where status =3 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repair') . ' where status =5 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['total'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repair') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_repair') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            $data[$k]['regionname'] = $region['title'];
            if ($data[$k]['cid'] == 0) {
                $data[$k]['catename'] = '其他';
            } else {
                $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_category') . ' where weid and :weid and id = :cid';
                $data[$k]['catename'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $data[$k]['cid']));
            }
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('repair');
} elseif ($operation == 'repairplist') {
    $current = '内部工单统计';
    $myret = 0;
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    if ($_W['isajax']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
        $repair_array = array(0, 0, 0, 0, 0, 0);
        $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repairp') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' group by status';
        $repair = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($repair))) {
            if ($repair[$k]['status'] == 5) {
                $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 8) {
                $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 0) {
                $repair_array[1] = $repair_array[1] + intval($repair[$k]['repair_num']);
            } else {
                $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
            }
            ($k += 1) + -1;
        }
        $repair_cates = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_category') . ' where ' . $condition . ' and type=5 order by title asc', $params);
        $repair_cate = array();
        $k = 0;
        while (!($k >= count($repair_cates))) {
            $repair_cate[$repair_cates[$k]['id']] = $repair_cates[$k]['title'];
            ($k += 1) + -1;
        }
        ksort($repair_cate);
        $sql = 'select cid,count(cid) as cate_num from ' . tablename('rhinfo_zyxq_repairp') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' group by cid';
        $category = pdo_fetchall($sql, $params);
        $category_array = array();
        $k = 0;
        while (!($k >= count($category))) {
            $category_array[$k] = 0;
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($category))) {
            if (array_key_exists($category[$k]['cid'], $repair_cate)) {
                $category_array[$category[$k]['cid']] = $category[$k]['cate_num'];
            }
            ($k += 1) + -1;
        }
        $count = array_values($category_array);
        $name = array_values($repair_cate);
        $res = array();
        $k = 0;
        while (!($k >= count($count))) {
            $res[$k]['value'] = $count[$k];
            $res[$k]['name'] = $name[$k];
            ($k += 1) + -1;
        }
        $ret = array('ajaxrepair' => $repair_array, 'ajaxcategory' => array('count' => $count, 'name' => $name, 'data' => $res));
        exit(json_encode($ret));
    }
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $defaultrid = 0;
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        if ($k == 0) {
            $defaultrid = $myregions[$k]['id'];
            $defaultbuildings = $buildings;
            $select_buildings = $buildings;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['rid']) {
        $defaultrid = $_GPC['rid'];
    }
    $condition .= ' AND rid= ' . $defaultrid;
    $bindroom = array();
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repairp') . ' where (status =0 or status =1) and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repairp') . ' where status =2 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repairp') . ' where status =3 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repairp') . ' where status =5 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['total'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_repairp') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            $data[$k]['regionname'] = $region['title'];
            if ($data[$k]['cid'] == 0) {
                $data[$k]['catename'] = '其他';
            } else {
                $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_category') . ' where weid and :weid and id = :cid';
                $data[$k]['catename'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $data[$k]['cid']));
            }
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('repairp');
} elseif ($operation == 'suggestlist') {
    $current = '投诉建议统计';
    $myret = 0;
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    if ($_W['isajax']) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
        $repair_array = array(0, 0, 0, 0, 0, 0);
        $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_suggest') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' group by status';
        $repair = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($repair))) {
            if ($repair[$k]['status'] == 5) {
                $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 9) {
                $repair_array[5] = $repair_array[5] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 8) {
                $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
            } elseif ($repair[$k]['status'] == 0) {
                $repair_array[1] = $repair_array[1] + intval($repair[$k]['repair_num']);
            } else {
                $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
            }
            ($k += 1) + -1;
        }
        $repair_cates = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_category') . ' where ' . $condition . ' and type=3 order by title asc', $params);
        $repair_cate = array();
        $k = 0;
        while (!($k >= count($repair_cates))) {
            $repair_cate[$repair_cates[$k]['id']] = $repair_cates[$k]['title'];
            ($k += 1) + -1;
        }
        ksort($repair_cate);
        $sql = 'select cid,count(cid) as cate_num from ' . tablename('rhinfo_zyxq_suggest') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' group by cid';
        $category = pdo_fetchall($sql, $params);
        $category_array = array();
        $k = 0;
        while (!($k >= count($category))) {
            $category_array[$k] = 0;
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($category))) {
            if (array_key_exists($category[$k]['cid'], $repair_cate)) {
                $category_array[$category[$k]['cid']] = $category[$k]['cate_num'];
            }
            ($k += 1) + -1;
        }
        $count = array_values($category_array);
        $name = array_values($repair_cate);
        $res = array();
        $k = 0;
        while (!($k >= count($count))) {
            $res[$k]['value'] = $count[$k];
            $res[$k]['name'] = $name[$k];
            ($k += 1) + -1;
        }
        $ret = array('ajaxrepair' => $repair_array, 'ajaxcategory' => array('count' => $count, 'name' => $name, 'data' => $res));
        exit(json_encode($ret));
    }
    $rights = $this->myrights(4, $mydo, 'suggestlist');
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $defaultrid = 0;
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        if ($k == 0) {
            $defaultrid = $myregions[$k]['id'];
            $defaultbuildings = $buildings;
            $select_buildings = $buildings;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['rid']) {
        $defaultrid = $_GPC['rid'];
    }
    $condition .= ' AND rid= ' . $defaultrid;
    $bindroom = array();
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_suggest') . ' where (status =0 or status =1) and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_suggest') . ' where status =2 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_suggest') . ' where status =3 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_suggest') . ' where status =5 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $bindroom['total'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_suggest') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            $data[$k]['regionname'] = $region['title'];
            if ($data[$k]['cid'] == 0) {
                $data[$k]['catename'] = '其他';
            } else {
                $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_category') . ' where weid and :weid and id = :cid';
                $data[$k]['catename'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $data[$k]['cid']));
            }
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('suggest');
} elseif ($operation == 'charging') {
    $current = '充电统计';
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $defaultrid = 0;
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        ($k += 1) + -1;
    }
    if (!empty($_GPC['rid'])) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_charging') . ' where weid=:weid and rid=:rid';
        $select_buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $condition1 = ' weid=:weid and chargid in(select distinct id from ' . tablename('rhinfo_zycj_charging') . ' where ' . $condition . ')';
    if (!empty($_GPC['bid'])) {
        $condition1 .= ' AND chargid= \'' . $_GPC['bid'] . '\'';
    }
    $chargingfee = array();
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition1;
    $chargingfee['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition1;
    $chargingfee['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition1;
    $chargingfee['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where ctime>=' . strtotime('-1 year', $starttime) . ' and ctime<=' . $endtime . ' and ' . $condition1;
    $chargingfee['total'] = pdo_fetchcolumn($sql, $params);
    $condition1 .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_log') . ' where fee>0 and ' . $condition1;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where fee>0 and ' . $condition1 . ' ORDER BY `ID` DESC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        load()->model('mc');
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select rid,title from ' . tablename('rhinfo_zycj_charging') . ' where id = :id and weid = :weid';
            $charging = pdo_fetch($sql, array(':id' => $data[$k]['chargid'], ':weid' => $mywe['weid']));
            $data[$k]['charging'] = $charging['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $charging['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $charging['rid'], ':uid' => $data[$k]['uid']));
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
    $color = 'rgba(194,31,194,';
    $ds_data_temp = '';
    $ds_temp = '';
    $lable_tpl = '';
    $labelcolor = 'fillColor : "' . $color . '0.1)",strokeColor : "' . $color . '1)",pointColor : "' . $color . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color . '1)"';
    $lable_tpl .= 'charging:{label:\'充电金额\',' . $labelcolor . '}';
    $lable_tpl = '{' . $lable_tpl . '}';
    $ds_data_temp .= 'ds.charging.data = datasets.charging;';
    $ds_temp .= 'ds.charging,';
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $stat = array();
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $stat['charging'][$key] = 0;
            ($i += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition1;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $k = 0;
            while (!($k >= count($chart_data))) {
                $key = date('m-d', $chart_data[$k]['ctime']);
                $stat['charging'][$key] = $stat['charging'][$key] + $chart_data[$k]['fee'];
                ($k += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = array_keys($stat['charging']);
        $out_temp = array();
        $out_temp['charging'] = array_values($stat['charging']);
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('charging');
} elseif ($operation == 'delcharging') {
    $current = '删除充电记录';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_charging_log', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'selfdevice') {
    $current = '自助设备统计';
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_selfwashcar') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        ($k += 1) + -1;
    }
    if (!empty($_GPC['rid'])) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $condition1 = ' weid=:weid and selfid in(select distinct id from ' . tablename('rhinfo_zycj_selfwashcar') . ' where ' . $condition . ')';
    if ($_GPC['bid']) {
        $condition1 .= ' AND selfid= \'' . $_GPC['bid'] . '\'';
    }
    $devicefee = array();
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition1;
    $devicefee['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition1;
    $devicefee['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition1;
    $chargingfee['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ctime>=' . strtotime('-1 year', $starttime) . ' and ctime<=' . $endtime . ' and ' . $condition1;
    $devicefee['total'] = pdo_fetchcolumn($sql, $params);
    $condition1 .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ' . $condition1;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ' . $condition1 . ' ORDER BY `ID` DESC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select rid,title from ' . tablename('rhinfo_zycj_selfwashcar') . ' where id = :id and weid = :weid';
            $selfwashcar = pdo_fetch($sql, array(':id' => $data[$k]['selfid'], ':weid' => $mywe['weid']));
            $data[$k]['device'] = $selfwashcar['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $selfwashcar['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $selfwashcar['rid'], ':uid' => $data[$k]['uid']));
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
    $color = 'rgba(194,31,194,';
    $ds_data_temp = '';
    $ds_temp = '';
    $lable_tpl = '';
    $labelcolor = 'fillColor : "' . $color . '0.1)",strokeColor : "' . $color . '1)",pointColor : "' . $color . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color . '1)"';
    $lable_tpl .= 'selfdevice:{label:\'消费金额\',' . $labelcolor . '}';
    $lable_tpl = '{' . $lable_tpl . '}';
    $ds_data_temp .= 'ds.selfdevice.data = datasets.selfdevice;';
    $ds_temp .= 'ds.selfdevice,';
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $stat = array();
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $stat['selfdevice'][$key] = 0;
            ($i += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_selfdevice_log') . ' where ' . $condition1;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $n = 0;
            while (!($n >= count($chart_data))) {
                $key = date('m-d', $chart_data[$n]['ctime']);
                $stat['selfdevice'][$key] = $stat['selfdevice'][$key] + $chart_data[$n]['fee'];
                ($n += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = array_keys($stat['selfdevice']);
        $out_temp = array();
        $out_temp['selfdevice'] = array_values($stat['selfdevice']);
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('selfdevice');
} elseif ($operation == 'carpay') {
    $current = '停车缴费';
    $myret = 0;
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        ($k += 1) + -1;
    }
    if (!empty($_GPC['rid'])) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    if (!empty($_GPC['bid'])) {
        $condition .= ' AND parklotid= ' . $_GPC['bid'];
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $carpayfee = array();
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition;
    $carpayfee['today'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition;
    $carpayfee['yesterday'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $carpayfee['month'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ctime>=' . strtotime('-1 year', $starttime) . ' and ctime<=' . $endtime . ' and ' . $condition;
    $carpayfee['total'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ' . $condition . ' ORDER BY `ID` DESC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select rid,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where id = :id and weid = :weid';
            $parklot = pdo_fetch($sql, array(':id' => $data[$k]['parklotid'], ':weid' => $mywe['weid']));
            $data[$k]['parklot'] = $parklot['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid and deleted=0 and status=0';
            $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':uid' => $data[$k]['cuid']));
            $fans = mc_fansinfo($data[$k]['cuid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            if (!empty($member)) {
                $data[$k]['realname'] = $member['realname'];
                $data[$k]['address'] = $member['address'];
            } else {
                $data[$k]['realname'] = $fans['nickname'];
                $data[$k]['address'] = $fans['address'];
            }
            $data[$k]['starttime'] = !empty($data[$k]['starttime']) ? date('Y-m-d H:i', $data[$k]['starttime']) : '';
            $data[$k]['endtime'] = !empty($data[$k]['endtime']) ? date('Y-m-d H:i', $data[$k]['endtime']) : '';
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = array();
    $color[0] = 'rgba(31,194,121,';
    $color[1] = 'rgba(194,31,194,';
    $color[2] = 'rgba(203,48,48,';
    $ds_data_temp = '';
    $ds_temp = '';
    $lable_tpl = '';
    $labelcolor = 'fillColor : "' . $color[0] . '0.1)",strokeColor : "' . $color[0] . '1)",pointColor : "' . $color[0] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[0] . '1)"';
    $lable_tpl .= 'parklot0:{label:\'临时缴费\',' . $labelcolor . '}';
    $labelcolor = 'fillColor : "' . $color[1] . '0.1)",strokeColor : "' . $color[1] . '1)",pointColor : "' . $color[1] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[1] . '1)"';
    $lable_tpl .= ',parklot1:{label:\'办理月卡\',' . $labelcolor . '}';
    $labelcolor = 'fillColor : "' . $color[2] . '0.1)",strokeColor : "' . $color[2] . '1)",pointColor : "' . $color[2] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[2] . '1)"';
    $lable_tpl .= ',parklot2:{label:\'门禁控制\',' . $labelcolor . '}';
    $lable_tpl = '{' . $lable_tpl . '}';
    $ds_data_temp .= 'ds.parklot0.data = datasets.parklot0;';
    $ds_temp .= 'ds.parklot0,';
    $ds_data_temp .= 'ds.parklot1.data = datasets.parklot1;';
    $ds_temp .= 'ds.parklot1,';
    $ds_data_temp .= 'ds.parklot2.data = datasets.parklot2;';
    $ds_temp .= 'ds.parklot2,';
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $stat = array();
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $stat['parklot0'][$key] = 0;
            $stat['parklot1'][$key] = 0;
            $stat['parklot2'][$key] = 0;
            ($i += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $k = 0;
            while (!($k >= count($chart_data))) {
                $key = date('m-d', $chart_data[$k]['ctime']);
                if ($chart_data[$k]['category'] == 1) {
                    $stat['parklot0'][$key] = $stat['parklot0'][$key] + $chart_data[$k]['fee'];
                } elseif ($chart_data[$k]['category'] == 2) {
                    $stat['parklot1'][$key] = $stat['parklot1'][$key] + $chart_data[$k]['fee'];
                } elseif ($chart_data[$k]['category'] == 3) {
                    $stat['parklot2'][$key] = $stat['parklot2'][$key] + $chart_data[$k]['fee'];
                }
                ($k += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = array_keys($stat['parklot0']);
        $out_temp = array();
        $out_temp['parklot0'] = array_values($stat['parklot0']);
        $out_temp['parklot1'] = array_values($stat['parklot1']);
        $out_temp['parklot2'] = array_values($stat['parklot2']);
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('carpay');
} elseif ($operation == 'costlist') {
    $current = '收支统计';
    $myret = 0;
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $myregions = pdo_fetchall($sql, $params);
    $mybuildings = array();
    $defaultrid = 0;
    $k = 0;
    while (!($k >= count($myregions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_costitem') . ' where weid=:weid and pid=:pid and rid=:rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myregions[$k]['pid'], ':rid' => $myregions[$k]['id']));
        $mybuildings[$myregions[$k]['id']] = $buildings;
        if ($k == 0) {
            $defaultrid = $myregions[$k]['id'];
            $select_buildings = $buildings;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['rid']) {
        $defaultrid = $_GPC['rid'];
    }
    $condition .= ' AND rid= ' . $defaultrid;
    if (!empty($_GPC['bid'])) {
        $condition .= ' AND itemid= ' . $_GPC['bid'];
    }
    $billdate = $_GPC['billdate'];
    if (!empty($billdate)) {
        $starttime = strtotime($billdate['start']);
        $endtime1 = strtotime($billdate['end']);
        $endtime = strtotime('+1 days', $endtime1);
    } else {
        $starttime = strtotime('now -30days');
        $endtime1 = strtotime(date('Y-m-d'));
        $endtime = strtotime('+1 days', $endtime1);
    }
    $tcostbill = array();
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition;
    $tcostbill['today1'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition;
    $tcostbill['yesterday1'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $tcostbill['month1'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=1 and status=1 and ctime>=' . strtotime('-1 year', $starttime) . ' and ctime<=' . $endtime . ' and ' . $condition;
    $tcostbill['year1'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =0 and ' . $condition;
    $tcostbill['today2'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =1 and ' . $condition;
    $tcostbill['yesterday2'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>=' . $starttime . ' and ctime<=' . $endtime . ' and ' . $condition;
    $tcostbill['month2'] = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>=' . strtotime('-1 year', $starttime) . ' and ctime<=' . $endtime . ' and ' . $condition;
    $tcostbill['year2'] = pdo_fetchcolumn($sql, $params);
    $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_costdetail') . ' where status=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_costdetail') . ' where status=1 and ' . $condition . ' ORDER BY `ID` DESC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select rid,title from ' . tablename('rhinfo_zyxq_parkinglot') . ' where id = :id and weid = :weid';
            $parklot = pdo_fetch($sql, array(':id' => $data[$k]['parklotid'], ':weid' => $mywe['weid']));
            $data[$k]['parklot'] = $parklot['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $data[$k]['starttime'] = !empty($data[$k]['starttime']) ? date('Y-m-d H:i', $data[$k]['starttime']) : '';
            $data[$k]['endtime'] = !empty($data[$k]['endtime']) ? date('Y-m-d H:i', $data[$k]['endtime']) : '';
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    $color = array();
    $color[0] = 'rgba(31,194,121,';
    $color[1] = 'rgba(203,48,48,';
    $color[2] = 'rgba(149,192,0,';
    $ds_data_temp = '';
    $ds_temp = '';
    $lable_tpl = '';
    $labelcolor = 'fillColor : "' . $color[0] . '0.1)",strokeColor : "' . $color[0] . '1)",pointColor : "' . $color[0] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[0] . '1)"';
    $lable_tpl .= 'parklot0:{label:\'收入\',' . $labelcolor . '}';
    $labelcolor = 'fillColor : "' . $color[1] . '0.1)",strokeColor : "' . $color[1] . '1)",pointColor : "' . $color[1] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[1] . '1)"';
    $lable_tpl .= ',parklot1:{label:\'支出\',' . $labelcolor . '}';
    $labelcolor = 'fillColor : "' . $color[2] . '0.1)",strokeColor : "' . $color[2] . '1)",pointColor : "' . $color[2] . '1)",pointStrokeColor : "#fff",pointHighlightFill : "#fff",pointHighlightStroke : "' . $color[2] . '1)"';
    $lable_tpl .= ',parklot2:{label:\'支出\',' . $labelcolor . '}';
    $lable_tpl = '{' . $lable_tpl . '}';
    $ds_data_temp .= 'ds.parklot0.data = datasets.parklot0;';
    $ds_temp .= 'ds.parklot0,';
    $ds_data_temp .= 'ds.parklot1.data = datasets.parklot1;';
    $ds_temp .= 'ds.parklot1,';
    $ds_data_temp .= 'ds.parklot2.data = datasets.parklot2;';
    $ds_temp .= 'ds.parklot2,';
    if ($_W['isajax']) {
        $num = ($endtime + 1 - $starttime) / 86400;
        $stat = array();
        $i = 0;
        while (!($i >= $num)) {
            $time = $i * 86400 + $starttime;
            $key = date('m-d', $time);
            $stat['parklot0'][$key] = 0;
            $stat['parklot1'][$key] = 0;
            $stat['parklot2'][$key] = 0;
            ($i += 1) + -1;
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_costdetail') . ' where status=1 and ' . $condition;
        $chart_data = pdo_fetchall($sql, $params);
        if (!empty($chart_data)) {
            $k = 0;
            while (!($k >= count($chart_data))) {
                $key = date('m-d', $chart_data[$k]['ctime']);
                if ($chart_data[$k]['io'] == 1) {
                    $stat['parklot0'][$key] = $stat['parklot0'][$key] + $chart_data[$k]['money'];
                } elseif ($chart_data[$k]['io'] == 2) {
                    $stat['parklot1'][$key] = $stat['parklot1'][$key] + $chart_data[$k]['money'];
                } else {
                    $stat['parklot2'][$key] = $stat['parklot2'][$key] + $chart_data[$k]['money'];
                }
                ($k += 1) + -1;
            }
        }
        $out = array();
        $out['label'] = array_keys($stat['parklot0']);
        $out_temp = array();
        $out_temp['parklot0'] = array_values($stat['parklot0']);
        $out_temp['parklot1'] = array_values($stat['parklot1']);
        $out_temp['parklot2'] = array_values($stat['parklot2']);
        $out['datasets'] = $out_temp;
        exit(json_encode($out));
    }
    include $this->mywtpl('costlist');
}
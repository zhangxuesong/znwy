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
$mydo = 'leasea';
$tablename = 'rhinfo_zyxq_lease';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$paymethod = array(array('id' => 1, 'title' => '按月份'), array('id' => 2, 'title' => '按季度'), array('id' => 3, 'title' => '按半年'), array('id' => 4, 'title' => '按整年'), array('id' => 5, 'title' => '一次性'));
$rights = $this->myrights(16, $mydo, $operation);
if ($operation == 'list') {
    $current = '账单统计';
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
    $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=2 and ' . $condition;
    $totalfee = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=2 and ' . $condition;
    $totalpayfee = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where status=2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where  status=2 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t`PAYDATE` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            $data[$k]['paydate'] = $data[$k]['paydate'] ? date('Y-m-d H:i', $data[$k]['paydate']) : '';
            $data[$k]['paytype'] = $paytype[$data[$k]['paytype']];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :leaseid and weid = :weid';
            $data[$k]['lessee'] = pdo_fetchcolumn($sql, array(':leaseid' => $data[$k]['leaseid'], ':weid' => $mywe['weid']));
            ($k += 1) + -1;
        }
        if ($_GPC['export'] == 'export') {
            $filter = array('id' => 'ID', 'title' => '收费项目', 'region' => '所属主体', 'lease' => '承租人', 'measure' => '计量单位', 'daterange' => '账单周期', 'price' => '单价', 'fee' => '费用（元）', 'payfee' => '实付', 'paytype' => '支付方式', 'paydate' => '付款日期', 'remark' => '备注');
            export_excel($data, $filter, '账单');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'exportbill') {
    $current = '导出账单模板';
    if ($_W['ispost']) {
        if ($_GPC['exporttype'] == 1) {
            $condition .= ' and pid = :pid and rid=:rid ';
            $params[':pid'] = $_GPC['pid'];
            $params[':rid'] = $_GPC['rid'];
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' and status=1';
            $data = pdo_fetchall($sql, $params);
            if (empty($data)) {
                $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl('lease', array('op' => 'bill')) . $mywe['direct'], 'danger');
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 and isimport=1 and calmethod >6';
            $feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
            $k = 0;
            while (!($k >= count($data))) {
                if (!(!($data[$k]['enddate'] >= TIMESTAMP) && !empty($data[$k]['enddate']))) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
                    $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :id and weid = :weid';
                    $lessee = pdo_fetchcolumn($sql, array(':id' => $data[$k]['lesseeid'], ':weid' => $mywe['weid']));
                    $data[$k]['region'] = $region;
                    $data[$k]['lessee'] = $lessee;
                    $data[$k]['htno'] = $data[$k]['title'];
                    $data[$k]['startdate'] = '';
                    $data[$k]['enddate'] = '';
                    $data[$k]['exporttype'] = 1;
                    $m = 0;
                    while (!($m >= count($feeitems))) {
                        $data[$k]['title_' . $m] = $feeitems[$m]['title'];
                        $data[$k]['price_' . $m] = $feeitems[$m]['price'];
                        $data[$k]['fee_' . $m] = '';
                        ($m += 1) + -1;
                    }
                    $data[$k]['total'] = '';
                }
                ($k += 1) + -1;
            }
            $filter = array('id' => 'ID', 'region' => '主体名称', 'lesseeid' => '承租人ID', 'lessee' => '承租人', 'htno' => '合同编号', 'area' => '租赁面积', 'exporttype' => '项目类别', 'startdate' => '账单开始日期', 'enddate' => '账单结束日期');
            $n = 0;
            while (!($n >= count($feeitems))) {
                $filter['title_' . $n] = '收费项目_' . $feeitems[$n]['id'];
                $filter['price_' . $n] = '单价';
                $filter['fee_' . $n] = '金额';
                ($n += 1) + -1;
            }
        }
        if ($_GPC['exporttype'] == 2) {
            $condition .= ' and pid = :pid and rid=:rid ';
            $params[':pid'] = $_GPC['pid'];
            $params[':rid'] = $_GPC['rid'];
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' and status=1';
            $data = pdo_fetchall($sql, $params);
            if (empty($data)) {
                $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl('lease', array('op' => 'bill')) . $mywe['direct'], 'danger');
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and pid=:pid and rid=:rid and status=1 and isimport=1 and calmethod =6';
            $feeitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid']));
            $k = 0;
            while (!($k >= count($data))) {
                if (!(!($data[$k]['enddate'] >= TIMESTAMP) && !empty($data[$k]['enddate']))) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
                    $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_lessee') . ' where id = :id and weid = :weid';
                    $lessee = pdo_fetchcolumn($sql, array(':id' => $data[$k]['lesseeid'], ':weid' => $mywe['weid']));
                    $data[$k]['region'] = $region;
                    $data[$k]['lessee'] = $lessee;
                    $data[$k]['htno'] = $data[$k]['title'];
                    $data[$k]['startdate'] = '';
                    $data[$k]['enddate'] = '';
                    $data[$k]['exporttype'] = 2;
                    $m = 0;
                    while (!($m >= count($feeitems))) {
                        $data[$k]['title_' . $m] = $feeitems[$m]['title'];
                        $data[$k]['price_' . $m] = $feeitems[$m]['price'];
                        $data[$k]['startqty_' . $m] = '';
                        $data[$k]['endqty_' . $m] = '';
                        ($m += 1) + -1;
                    }
                    $data[$k]['total'] = '';
                }
                ($k += 1) + -1;
            }
            $filter = array('id' => 'ID', 'region' => '主体名称', 'lesseeid' => '承租人ID', 'lessee' => '承租人', 'htno' => '合同编号', 'area' => '租赁面积', 'exporttype' => '项目类别', 'startdate' => '账单开始日期', 'enddate' => '账单结束日期');
            $n = 0;
            while (!($n >= count($feeitems))) {
                $filter['title_' . $n] = '收费项目_' . $feeitems[$n]['id'];
                $filter['price_' . $n] = '单价';
                $filter['startqty_' . $n] = '上期读数';
                $filter['endqty_' . $n] = '本期读数';
                ($n += 1) + -1;
            }
        }
        $filter['total'] = '合计';
        if ($_GPC['filetype'] == '2') {
            export_csv($data, $filter, $_GPC['title']);
        } else {
            export_excel($data, $filter, $_GPC['title']);
        }
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
    include $this->mywtpl('exportbill');
} elseif ($operation == 'importbill') {
    $current = '导入账单';
    if ($_W['isajax']) {
        $pid = $_GPC['pid'];
        $rid = $_GPC['rid'];
        if (!empty($_FILES['upfile']['name'])) {
            $tmp_file = $_FILES['upfile']['tmp_name'];
            $file_types = explode('.', $_FILES['upfile']['name']);
            $file_type = $file_types[count($file_types) - 1];
            if (strtolower($file_type) != 'csv' && strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
                echo '类型不正确，请重新上传';
                exit(0);
            }
            $savePath = IA_ROOT . '/addons/rhinfo_zyxq/upfile/';
            $str = date('Ymdhis');
            $file_name = $str . '.' . $file_type;
            if (!copy($tmp_file, $savePath . $file_name)) {
                echo '上传失败';
                exit(0);
            }
            if (strtolower($file_type) == 'csv') {
                $res = import_csv($savePath . $file_name);
            } else {
                $res = import_excel($savePath . $file_name);
            }
            $i = 0;
            $feeitems = array();
            if ($_GPC['exporttype'] == 1) {
                $k = 0;
                while (!($k >= count($res))) {
                    if ($k == 0) {
                        $i = 9;
                        while (!($i >= count($res[$k]))) {
                            $pos = strpos($res[$k][$i], '_');
                            $feeitemarr = array();
                            if ($pos) {
                                $feeitemarr[] = substr($res[$k][$i], $pos + 1);
                                $feeitemarr[] = $i;
                                $feeitems[] = $feeitemarr;
                            }
                            $i += 3;
                        }
                    }
                    if (!($_GPC['exporttype'] != $res[$k][6])) {
                        if ($k > 0) {
                            $sql = 'select * from ' . tablename($tablename) . ' where weid=:weid and rid=:rid and id=:hid';
                            $room = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $rid, ':hid' => $res[$k][0]));
                            if (!empty($room)) {
                                $data = array('weid' => $mywe['weid'], 'pid' => $room['pid'], 'rid' => $room['rid'], 'leaseid' => $room['id'], 'lesseeid' => $room['lesseeid'], 'category' => 1, 'remark' => 'EXCEL导入');
                                $feebillitem_data = $data;
                                $m = 0;
                                while (!($m >= count($feeitems))) {
                                    $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and id=:itemid';
                                    $feeitem = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':itemid' => $feeitems[$m][0]));
                                    if ($feeitem['calmethod'] == 7) {
                                        $data['startdate'] = strtotime(trim($res[$k][7]));
                                        $data['enddate'] = strtotime(trim($res[$k][8]));
                                        $price = floatval($res[$k][$feeitems[$m][1] + 1]);
                                        $price = $price > 0 ? $price : $feeitem['price'];
                                        $paymonths = $feeitem['paymonths'];
                                        $data['fee'] = $price * $paymonths * $room['area'];
                                        $data['fee'] = floatval($res[$k][$feeitems[$m][1] + 2]) > 0 ? floatval($res[$k][$feeitems[$m][1] + 2]) : $data['fee'];
                                        $data['daterange'] = $data['startdate'] == $data['enddate'] ? date('Y-m', $data['startdate']) : date('Y-m', $data['startdate']) . '~' . date('Y-m', $data['enddate']);
                                        $feetype = 1;
                                    } elseif ($feeitem['calmethod'] == 8) {
                                        $data['startdate'] = strtotime(trim($res[$k][7]));
                                        $data['enddate'] = strtotime(trim($res[$k][8]));
                                        $price = floatval($res[$k][$feeitems[$m][1] + 1]);
                                        $price = $price > 0 ? $price : $feeitem['price'];
                                        $paymonths = $feeitem['paymonths'];
                                        $data['fee'] = $price * $paymonths;
                                        $data['fee'] = floatval($res[$k][$feeitems[$m][1] + 2]) > 0 ? floatval($res[$k][$feeitems[$m][1] + 2]) : $data['fee'];
                                        $data['daterange'] = $data['startdate'] == $data['enddate'] ? date('Y-m', $data['startdate']) : date('Y-m', $data['startdate']) . '~' . date('Y-m', $data['enddate']);
                                        $feetype = 1;
                                    } else {
                                        echo 'error';
                                        exit(0);
                                    }
                                    $status = 1;
                                    if ($data['fee'] > 0) {
                                        if (!(!is_int($data['startdate']) || !is_int($data['enddate']))) {
                                            $data['title'] = $feeitem['title'];
                                            $feebillitem_data['title'] = $feeitem['title'];
                                            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_leasebill') . ' where category=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid and startdate=:startdate and enddate=:enddate';
                                            $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':leaseid' => $room['id'], ':itemid' => $feeitem['id'], ':startdate' => $data['startdate'], ':enddate' => $data['enddate']));
                                            if (!($total > 0)) {
                                                $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebillitem') . ' where category=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid';
                                                $myfeeitem = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':leaseid' => $room['id'], ':itemid' => $feeitem['id']));
                                                $feebillitem_data['paydate'] = $data['enddate'];
                                                $update_billdate = array();
                                                $update_billdate['paydate'] = $data['enddate'];
                                                $update_billdate['billdate'] = $data['enddate'];
                                                $feebillitem_data['billdate'] = $data['enddate'];
                                                $data['cuid'] = $mywe['uid'];
                                                $data['ctime'] = TIMESTAMP;
                                                $data['measure'] = $feeitem['measure'];
                                                $data['status'] = $status;
                                                $data['feetype'] = $feetype;
                                                $data['price'] = $price;
                                                $data['itemid'] = $feeitem['id'];
                                                $feebillitem_data['cuid'] = $mywe['uid'];
                                                $feebillitem_data['ctime'] = $data['ctime'];
                                                $feebillitem_data['itemid'] = $feeitem['id'];
                                                if ($f['feeround'] == 1) {
                                                    $data['fee'] = round($data['fee'], 2);
                                                } elseif ($f['feeround'] == 2) {
                                                    $data['fee'] = round($data['fee'], 1);
                                                } elseif ($f['feeround'] == 3) {
                                                    $data['fee'] = round($data['fee'], 0);
                                                } elseif ($f['feeround'] == 4) {
                                                    $data['fee'] = floor($data['fee']);
                                                }
                                                if ($data['fee'] > 0) {
                                                    $result = pdo_insert('rhinfo_zyxq_leasebill', $data);
                                                    $id = pdo_insertid();
                                                    ($i += 1) + -1;
                                                }
                                                if ($myfeeitem) {
                                                    if ($update_billdate) {
                                                        pdo_update('rhinfo_zyxq_leasebillitem', $update_billdate, array('id' => $myfeeitem['id']));
                                                    } elseif ($result) {
                                                        pdo_insert('rhinfo_zyxq_leasebillitem', $feebillitem_data);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ($m += 1) + -1;
                                }
                            }
                        }
                    }
                    ($k += 1) + -1;
                }
            }
            if ($_GPC['exporttype'] == 2) {
                $k = 0;
                while (!($k >= count($res))) {
                    if ($k == 0) {
                        $i = 9;
                        while (!($i >= count($res[$k]))) {
                            $pos = strpos($res[$k][$i], '_');
                            $feeitemarr = array();
                            if ($pos) {
                                $feeitemarr[] = substr($res[$k][$i], $pos + 1);
                                $feeitemarr[] = $i;
                                $feeitems[] = $feeitemarr;
                            }
                            $i += 4;
                        }
                    }
                    if (!($_GPC['exporttype'] != $res[$k][6])) {
                        if ($k > 0) {
                            $sql = 'select * from ' . tablename($tablename) . ' where weid=:weid and rid=:rid and id=:hid';
                            $room = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $rid, ':hid' => $res[$k][0]));
                            if (!empty($room)) {
                                $data = array('weid' => $mywe['weid'], 'pid' => $room['pid'], 'rid' => $room['rid'], 'leaseid' => $room['id'], 'lesseeid' => $room['lesseeid'], 'category' => 1, 'remark' => 'EXCEL导入');
                                $feebillitem_data = $data;
                                $m = 0;
                                while (!($m >= count($feeitems))) {
                                    $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and id=:itemid';
                                    $feeitem = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':itemid' => $feeitems[$m][0]));
                                    if ($feeitem['calmethod'] == 6) {
                                        $data['startdate'] = strtotime(trim($res[$k][7]));
                                        $data['enddate'] = strtotime(trim($res[$k][8]));
                                        $price = floatval($res[$k][$feeitems[$m][1] + 1]);
                                        $price = $price > 0 ? $price : $feeitem['price'];
                                        $data['startqty'] = floatval($res[$k][$feeitems[$m][1] + 2]);
                                        $data['endqty'] = floatval($res[$k][$feeitems[$m][1] + 3]);
                                        $threeqty = $data['endqty'] - $data['startqty'];
                                        $data['threeqty'] = $threeqty > 0 ? $threeqty : 0;
                                        $data['fee'] = $data['threeqty'] * $price;
                                        $data['daterange'] = $data['startdate'] == $data['enddate'] ? date('Y-m', $data['startdate']) : date('Y-m', $data['startdate']) . '~' . date('Y-m', $data['enddate']);
                                        $feetype = 2;
                                        if (!($data['endqty'] == 0)) {
                                        }
                                    } else {
                                        echo 'error';
                                        exit(0);
                                    }
                                    $status = 1;
                                    if ($data['fee'] > 0) {
                                        if (!(!is_int($data['startdate']) || !is_int($data['enddate']))) {
                                            $data['title'] = $feeitem['title'];
                                            $feebillitem_data['title'] = $feeitem['title'];
                                            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_leasebill') . ' where category=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid and startdate=:startdate and enddate=:enddate';
                                            $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':leaseid' => $room['id'], ':itemid' => $feeitem['id'], ':startdate' => $data['startdate'], ':enddate' => $data['enddate']));
                                            if (!($total > 0)) {
                                                $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebillitem') . ' where category=1 and weid=:weid and pid=:pid and rid=:rid and leaseid=:leaseid and itemid=:itemid';
                                                $myfeeitem = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $room['pid'], ':rid' => $room['rid'], ':leaseid' => $room['id'], ':itemid' => $feeitem['id']));
                                                $feebillitem_data['paydate'] = $data['enddate'];
                                                $update_billdate = array();
                                                $update_billdate['paydate'] = $data['enddate'];
                                                $update_billdate['billdate'] = $data['enddate'];
                                                $feebillitem_data['billdate'] = $data['enddate'];
                                                $data['cuid'] = $mywe['uid'];
                                                $data['ctime'] = TIMESTAMP;
                                                $data['measure'] = $feeitem['measure'];
                                                $data['status'] = $status;
                                                $data['feetype'] = $feetype;
                                                $data['price'] = $price;
                                                $data['itemid'] = $feeitem['id'];
                                                $feebillitem_data['cuid'] = $mywe['uid'];
                                                $feebillitem_data['ctime'] = $data['ctime'];
                                                $feebillitem_data['itemid'] = $feeitem['id'];
                                                if ($f['feeround'] == 1) {
                                                    $data['fee'] = round($data['fee'], 2);
                                                } elseif ($f['feeround'] == 2) {
                                                    $data['fee'] = round($data['fee'], 1);
                                                } elseif ($f['feeround'] == 3) {
                                                    $data['fee'] = round($data['fee'], 0);
                                                } elseif ($f['feeround'] == 4) {
                                                    $data['fee'] = floor($data['fee']);
                                                }
                                                if ($data['fee'] > 0) {
                                                    $result = pdo_insert('rhinfo_zyxq_leasebill', $data);
                                                    $id = pdo_insertid();
                                                    ($i += 1) + -1;
                                                }
                                                if ($myfeeitem) {
                                                    if ($update_billdate) {
                                                        pdo_update('rhinfo_zyxq_leasebillitem', $update_billdate, array('id' => $myfeeitem['id']));
                                                    } elseif ($result) {
                                                        pdo_insert('rhinfo_zyxq_leasebillitem', $feebillitem_data);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ($m += 1) + -1;
                                }
                            }
                        }
                    }
                    ($k += 1) + -1;
                }
            }
            if ($i > 0) {
                $this->mysyslog($mywe['pid'], $mydo, $operation, $current, '生成' . $i . '笔账单');
                echo 'ok';
            } else {
                echo '导入失败，数据可能已经导入!';
            }
            unlink($savePath . $file_name);
            exit(0);
        } else {
            echo '文件不正确错误!';
            exit(0);
        }
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
    include $this->mywtpl('importbill');
}
==<?php 
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Rhinfo_zyxqModuleWebapp extends WeModuleWebapp
{
    public $syscfg = array();
    public function __construct()
    {
        global $_W;
        global $_GPC;
        $this->syscfg = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']), array('title', 'logo', 'board_password'));
        $this->syscfg['title'] = !empty($this->syscfg['title']) ? $this->syscfg['title'] : $_W['account']['name'];
        $this->syscfg['issys'] = 0;
        if (empty($this->syscfg['logo'])) {
            if (is_file(IA_ROOT . '/attachment/headimg_' . $_W['uniacid'] . '.png')) {
                $this->syscfg['logo'] = tomedia('headimg_' . $_W['uniacid']) . '.png';
            } elseif (is_file(IA_ROOT . '/attachment/headimg_' . $_W['uniacid'] . '.jpg')) {
                $this->syscfg['logo'] = tomedia('headimg_' . $_W['uniacid']) . '.jpg';
            } else {
                $this->syscfg['logo'] = '../addons/rhinfo_zyxq/static/web/ui.admin/images/zylogo.png';
            }
        } else {
            $this->syscfg['logo'] = tomedia($this->syscfg['logo']);
            $this->syscfg['issys'] = 1;
        }
        return null;
    }
    public function doPageHome()
    {
        global $_W;
        global $_GPC;
        $condition = ' weid = :weid and rid =:rid ';
        $params = array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']);
        if ($_GPC['op'] == 'check_pwd') {
            $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $_GPC['rid']), array('board_password'));
            if ($region['board_password'] != $_GPC['password']) {
                echo '密码错误';
            } else {
                echo 'ok';
            }
            exit(0);
        } elseif ($_GPC['op'] == 'bind') {
            $bindarr = array();
            $total = 0;
            $m = 0;
            while (!($m >= 3)) {
                $bindnum = 0;
                $k = 14;
                while ($k >= 0) {
                    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and otype=' . $m . ' and ' . $condition;
                    $bindnum = pdo_fetchcolumn($sql, $params);
                    $bindarr[$m][] = $bindnum;
                    $total += $bindnum;
                    ($k += -1) + 1;
                }
                ($m += 1) + -1;
            }
            $binddata = array();
            $binddata[] = implode(',', $bindarr[0]);
            $binddata[] = implode(',', $bindarr[1]);
            $binddata[] = implode(',', $bindarr[2]);
            include $this->mytpl('chartbind');
            exit(0);
        } elseif ($_GPC['op'] == 'payfee') {
            $sql = 'SELECT distinct itemid,title FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and ' . $condition;
            $feeitems = pdo_fetchall($sql, $params);
            $legend = array();
            $k = 0;
            while (!($k >= count($feeitems))) {
                $legend[] = '\'' . $feeitems[$k]['title'] . '\'';
                ($k += 1) + -1;
            }
            $legend_data = implode(',', $legend);
            $total = 0;
            $m = 0;
            while (!($m >= count($feeitems))) {
                $xAxis_data = array();
                $k = 6;
                while ($k >= 0) {
                    $sql = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =' . $k . ' and itemid=' . $feeitems[$m]['itemid'] . ' and ' . $condition;
                    $fee = pdo_fetchcolumn($sql, $params);
                    $xAxis_data[] = !empty($fee) ? $fee : 0;
                    $total += $fee;
                    ($k += -1) + 1;
                }
                $feeitems[$m]['data'] = implode(',', $xAxis_data);
                ($m += 1) + -1;
            }
            if (empty($feeitems)) {
                $feeitems[]['data'] = '0';
            }
            include $this->mytpl('chartpay');
            exit(0);
        } elseif ($_GPC['op'] == 'nopayfee') {
            $sql = 'SELECT distinct itemid,title FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and ' . $condition;
            $feeitems = pdo_fetchall($sql, $params);
            $total = 0;
            $xAxis_data = array();
            $series_data = array();
            $m = 0;
            while (!($m >= count($feeitems))) {
                $xAxis_data[] = '\'' . $feeitems[$m]['title'] . '\'';
                $sql = 'select sum(fee-payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where itemid=' . $feeitems[$m]['itemid'] . ' and status=1 and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $series_data[] = !empty($fee) ? $fee : 0;
                $total += $fee;
                ($m += 1) + -1;
            }
            if (empty($xAxis_data) || empty($series_data)) {
                $xAxis_data = array('');
                $series_data = array(0);
            }
            include $this->mytpl('chartnopay');
            exit(0);
        } elseif ($_GPC['op'] == 'cost') {
            $costarr = array();
            $intotal = 0;
            $outtotal = 0;
            $inmoney = array();
            $outmoney = array();
            $diffmoney = array();
            $k = 14;
            while ($k >= 0) {
                $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and (io=1 or io=3 or io=4) and ' . $condition;
                $inmoney_temp = pdo_fetchcolumn($sql, $params);
                $inmoney_temp = empty($inmoney_temp) ? 0 : $inmoney_temp;
                $inmoney[] = $inmoney_temp;
                $intotal += $inmoney_temp;
                $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and io=2 and ' . $condition;
                $outmoney_temp = pdo_fetchcolumn($sql, $params);
                $outmoney_temp = empty($outmoney_temp) ? 0 : $outmoney_temp;
                $outmoney[] = 0 - $outmoney_temp;
                $outtotal += $outmoney_temp;
                $diffmoney[] = $inmoney_temp - $outmoney_temp;
                ($k += -1) + 1;
            }
            $money_data = array();
            $money_data[] = implode(',', $inmoney);
            $money_data[] = implode(',', $outmoney);
            $money_data[] = implode(',', $diffmoney);
            include $this->mytpl('chartbudget');
            exit(0);
        } elseif ($_GPC['op'] == 'fix') {
            $fix_data = array();
            $i = 0;
            $total = 0;
            $m = 29;
            while ($m >= 0) {
                $repair_array = array(0, 0, 0, 0, 0, 0);
                $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repair') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $m . ' and ' . $condition . ' group by status';
                $repair = pdo_fetchall($sql, $params);
                $k = 0;
                while (!($k >= count($repair))) {
                    if ($repair[$k]['status'] == 5) {
                        $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                    } elseif ($repair[$k]['status'] == 9) {
                        $repair_array[5] = $repair_array[5] + intval($repair[$k]['repair_num']);
                    } elseif ($repair[$k]['status'] == 8) {
                        $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
                    } elseif ($repair[$k]['status'] == 1) {
                        $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                    } else {
                        $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
                    }
                    $total += intval($repair[$k]['repair_num']);
                    ($k += 1) + -1;
                }
                $fix_data[$i][] = $repair_array;
                $suggest_array = array(0, 0, 0, 0, 0, 0);
                $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_suggest') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $m . ' and ' . $condition . ' group by status';
                $suggest = pdo_fetchall($sql, $params);
                $k = 0;
                while (!($k >= count($suggest))) {
                    if ($suggest[$k]['status'] == 5) {
                        $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['repair_num']);
                    } elseif ($suggest[$k]['status'] == 9) {
                        $suggest_array[5] = $suggest_array[5] + intval($suggest[$k]['repair_num']);
                    } elseif ($suggest[$k]['status'] == 8) {
                        $suggest_array[4] = $suggest_array[4] + intval($suggest[$k]['repair_num']);
                    } elseif ($suggest[$k]['status'] == 0) {
                        $suggest_array[1] = $suggest_array[1] + intval($suggest[$k]['repair_num']);
                    } else {
                        $suggest_array[$suggest[$k]['status']] = $suggest_array[$suggest[$k]['status']] + intval($suggest[$k]['repair_num']);
                    }
                    $total += intval($suggest[$k]['repair_num']);
                    ($k += 1) + -1;
                }
                $fix_data[$i][] = $suggest_array;
                $repairp_array = array(0, 0, 0, 0, 0, 0);
                $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repairp') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $m . ' and ' . $condition . ' group by status';
                $repairp = pdo_fetchall($sql, $params);
                $k = 0;
                while (!($k >= count($repairp))) {
                    if ($repairp[$k]['status'] == 5) {
                        $repairp_array[0] = $repairp_array[0] + intval($repairp[$k]['repair_num']);
                    } elseif ($repairp[$k]['status'] == 8) {
                        $repairp_array[4] = $repairp_array[4] + intval($repairp[$k]['repair_num']);
                    } elseif ($repairp[$k]['status'] == 1) {
                        $repairp_array[0] = $repairp_array[0] + intval($repairp[$k]['repair_num']);
                    } else {
                        $repairp_array[$repairp[$k]['status']] = $repairp_array[$repairp[$k]['status']] + intval($repairp[$k]['repair_num']);
                    }
                    $total += intval($repairp[$k]['repair_num']);
                    ($k += 1) + -1;
                }
                $fix_data[$i][] = $repairp_array;
                ($i += 1) + -1;
                ($m += -1) + 1;
            }
            $series_default = array();
            $series_data = array();
            $k = 0;
            while (!($k >= count($fix_data))) {
                if ($k == 0) {
                    $series_default[] = $fix_data[$k][0];
                    $series_default[] = $fix_data[$k][1];
                    $series_default[] = $fix_data[$k][2];
                } else {
                    $series_data[$k][] = $fix_data[$k][0];
                    $series_data[$k][] = $fix_data[$k][1];
                    $series_data[$k][] = $fix_data[$k][2];
                }
                ($k += 1) + -1;
            }
            include $this->mytpl('chartfix');
            exit(0);
        } elseif ($_GPC['op'] == 'region') {
            $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $_GPC['rid']), array('title', 'roomqty', 'buildarea', 'coveredarea'));
            if (empty($region['roomqty'])) {
                $region['roomqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid', $params);
            }
            $region['buildqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid', $params);
            $region['parkingqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and rid=:rid', $params);
            $region['shopqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_shop') . ' where weid=:weid and rid=:rid', $params);
            $region['garageqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_garage') . ' where weid=:weid and rid=:rid', $params);
            $region['elevatorqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_elevator') . ' where weid=:weid and rid=:rid', $params);
            $qty = array();
            $qty['buildqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid', $params);
            $qty['parkingqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where weid=:weid and rid=:rid', $params);
            $qty['shopqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_shop') . ' where weid=:weid and rid=:rid', $params);
            $qty['garageqty'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_garage') . ' where weid=:weid and rid=:rid', $params);
            include $this->mytpl('chartpop');
            exit(0);
        } elseif ($_GPC['op'] == 'self') {
            $total = 0;
            $charging = array();
            $selfdevice = array();
            $parking = array();
            $condition1 = ' weid=:weid and chargid in(select distinct id from ' . tablename('rhinfo_zycj_charging') . ' where ' . $condition . ')';
            $condition2 = ' weid=:weid and selfid in(select distinct id from ' . tablename('rhinfo_zycj_selfwashcar') . ' where ' . $condition . ')';
            $k = 29;
            while ($k >= 0) {
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition1;
                $charging_fee = pdo_fetchcolumn($sql, $params);
                $charging_fee = empty($charging_fee) ? 0 : $charging_fee;
                $charging[] = $charging_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition2;
                $selfdevice_fee = pdo_fetchcolumn($sql, $params);
                $selfdevice_fee = empty($selfdevice_fee) ? 0 : $selfdevice_fee;
                $selfdevice[] = $selfdevice_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $parking_fee = pdo_fetchcolumn($sql, $params);
                $parking_fee = empty($parking_fee) ? 0 : $parking_fee;
                $parking[] = $parking_fee;
                $total += $charging_fee + $selfdevice_fee + $parking_fee;
                ($k += -1) + 1;
            }
            $selfdata = array();
            $selfdata[] = implode(',', $charging);
            $selfdata[] = implode(',', $selfdevice);
            $selfdata[] = implode(',', $parking);
            include $this->mytpl('chartself');
            exit(0);
        }
        $region = pdo_get('rhinfo_zyxq_region', array('weid' => $_W['uniacid'], 'id' => $_GPC['rid']), array('title', 'board_password'));
        $title = $region['title'];
        include $this->mytpl();
        return null;
    }
    public function doPagePlatform()
    {
        global $_W;
        global $_GPC;
        $syspub = pdo_get('rhinfo_zyxq_syspub');
        if (!empty($syspub['title'])) {
            $this->syscfg['title'] = $syspub['title'];
        }
        if (!empty($syspub['logo'])) {
            $this->syscfg['logo'] = tomedia($syspub['logo']);
            $this->syscfg['issys'] = 1;
        }
        $params = array();
        if ($_GPC['op'] == 'check_pwd') {
            if ($syspub['board_password'] != $_GPC['password']) {
                echo '密码错误';
            } else {
                echo 'ok';
            }
            exit(0);
        } elseif ($_GPC['op'] == 'bind') {
            $bind_array = array(0, 0, 0);
            $total = 0;
            $m = 0;
            while (!($m >= 3)) {
                $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where otype=' . $m;
                $bindnum = pdo_fetchcolumn($sql, $params);
                $bind_array[$m] = $bind_array[$m] + $bindnum;
                $total += $bindnum;
                ($m += 1) + -1;
            }
            include $this->mytpl('chartbind');
            exit(0);
        } elseif ($_GPC['op'] == 'feebill') {
            $feebill_array = array(0, 0, 0);
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype in(1,2,4) ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype=9 ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[1] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype in(3,5,6,7,8) ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[2] = !empty($payfee) ? $payfee : 0;
            $total = $feebill_array[0] + $feebill_array[1] + $feebill_array[2];
            include $this->mytpl('chartfeebill');
            exit(0);
        } elseif ($_GPC['op'] == 'carfee') {
            $carfee_array = array(0, 0);
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=1 ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=2 ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[1] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=3 ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[2] = !empty($payfee) ? $payfee : 0;
            $total = $carfee_array[0] + $carfee_array[1] + $carfee_array[2];
            include $this->mytpl('chartcarfee');
            exit(0);
        } elseif ($_GPC['op'] == 'payfee') {
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =' . $k;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                $total += $fee;
                ($k += -1) + 1;
            }
            $data = implode(',', $xAxis_data);
            include $this->mytpl('chartpay');
            exit(0);
        } elseif ($_GPC['op'] == 'payrate') {
            $feebill_array = array(0, 0);
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1  ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2  ';
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[1] = !empty($payfee) ? $payfee : 0;
            $total = $feebill_array[0] + $feebill_array[1];
            include $this->mytpl('chartpayrate');
            exit(0);
        } elseif ($_GPC['op'] == 'map') {
            $map_array = array(array('name' => '北京', 'value' => 0), array('name' => '北京', 'value' => 0), array('name' => '天津', 'value' => 0), array('name' => '上海', 'value' => 0), array('name' => '重庆', 'value' => 0), array('name' => '河南', 'value' => 0), array('name' => '云南', 'value' => 0), array('name' => '辽宁', 'value' => 0), array('name' => '黑龙江', 'value' => 0), array('name' => '湖南', 'value' => 0), array('name' => '安徽', 'value' => 0), array('name' => '山东', 'value' => 0), array('name' => '新疆', 'value' => 0), array('name' => '江苏', 'value' => 0), array('name' => '浙江', 'value' => 0), array('name' => '江西', 'value' => 0), array('name' => '湖北', 'value' => 0), array('name' => '广西', 'value' => 0), array('name' => '甘肃', 'value' => 0), array('name' => '山西', 'value' => 0), array('name' => '内蒙古', 'value' => 0), array('name' => '陕西', 'value' => 0), array('name' => '吉林', 'value' => 0), array('name' => '福建', 'value' => 0), array('name' => '贵州', 'value' => 0), array('name' => '广东', 'value' => 0), array('name' => '青海', 'value' => 0), array('name' => '西藏', 'value' => 0), array('name' => '四川', 'value' => 0), array('name' => '宁夏', 'value' => 0), array('name' => '海南', 'value' => 0), array('name' => '台湾', 'value' => 0), array('name' => '香港', 'value' => 0), array('name' => '澳门', 'value' => 0));
            $maptemp1_array = $map_array;
            $maptemp2_array = $map_array;
            $m = 0;
            while (!($m >= count($map_array))) {
                $sql = 'select count(*) from ' . tablename('mc_members') . ' where resideprovince like "%' . $map_array[$m]['name'] . '%"';
                $count = pdo_fetchcolumn($sql);
                $maptemp1_array[$m]['value'] = !empty($count) ? $count : 0;
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where province like "%' . $map_array[$m]['name'] . '%"';
                $count = pdo_fetchcolumn($sql);
                $maptemp2_array[$m]['value'] = !empty($count) ? $count : 0;
                ($m += 1) + -1;
            }
            $map1_array = array();
            foreach ($maptemp1_array as $k => $v) {
                if ($v['value'] > 0) {
                    $map1_array[] = $v;
                }
            }
            $map1_array = !empty($map1_array) ? $map1_array : array(array('name' => '', 'value' => '0'));
            $map2_array = array();
            foreach ($maptemp2_array as $k => $v) {
                if ($v['value'] > 0) {
                    $map2_array[] = $v;
                }
            }
            $map2_array = !empty($map2_array) ? $map2_array : array(array('name' => '', 'value' => '0'));
            include $this->mytpl('chartmap');
            exit(0);
        } elseif ($_GPC['op'] == 'member') {
            $member_array = array(0, 0);
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where gender=1';
            $count = pdo_fetchcolumn($sql);
            $member_array[0] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where gender=2';
            $count = pdo_fetchcolumn($sql);
            $member_array[1] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('mc_members');
            $count = pdo_fetchcolumn($sql);
            $member_array[2] = !empty($count) ? $count : 0;
            $total = $member_array[2];
            $member_array[2] = $member_array[2] - $member_array[0] - $member_array[1];
            include $this->mytpl('chartmember');
            exit(0);
        } elseif ($_GPC['op'] == 'owner') {
            $member_array = array(0, 0, 0, 0);
            $sql = 'select count(*) from ' . tablename('mc_members');
            $count = pdo_fetchcolumn($sql);
            $member_array[0] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where category=2';
            $count = pdo_fetchcolumn($sql);
            $member_array[2] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member');
            $count = pdo_fetchcolumn($sql);
            $member_array[1] = $count - $member_array[2];
            $member_array[3] = $member_array[0] - $count;
            $total = $member_array[0];
            include $this->mytpl('chartowner');
            exit(0);
        } elseif ($_GPC['op'] == 'costcomp') {
            $money_array = array(0, 0);
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where (io=1 or io=3 or io=4) and status=1 ';
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[0] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 ';
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[1] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where (io=1 or io=3 or io=4) and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[2] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[3] = !empty($money) ? $money : 0;
            include $this->mytpl('chartcost');
            exit(0);
        } elseif ($_GPC['op'] == 'cost') {
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(money) from ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and (io=1 or io=3 or io=4) ';
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                ($k += -1) + 1;
            }
            $indata = implode(',', $xAxis_data);
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(money) from ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and io=2 ';
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                ($k += -1) + 1;
            }
            $outdata = implode(',', $xAxis_data);
            include $this->mytpl('chartbudget');
            exit(0);
        } elseif ($_GPC['op'] == 'repair') {
            $repair_array = array(0, 0, 0, 0, 0, 0);
            $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repair') . ' group by status';
            $repair = pdo_fetchall($sql, $params);
            $total = 0;
            $k = 0;
            while (!($k >= count($repair))) {
                if ($repair[$k]['status'] == 5) {
                    $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 9) {
                    $repair_array[5] = $repair_array[5] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 8) {
                    $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 1) {
                    $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                } else {
                    $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
                }
                $total += intval($repair[$k]['repair_num']);
                ($k += 1) + -1;
            }
            $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repairp') . ' group by status';
            $repairp = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($repairp))) {
                if ($repairp[$k]['status'] == 5) {
                    $repair_array[0] = $repair_array[0] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 9) {
                    $repair_array[5] = $repair_array[5] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 8) {
                    $repair_array[4] = $repair_array[4] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 1) {
                    $repair_array[0] = $repair_array[0] + intval($repairp[$k]['repair_num']);
                } else {
                    $repair_array[$repairp[$k]['status']] = $repair_array[$repairp[$k]['status']] + intval($repairp[$k]['repair_num']);
                }
                $total += intval($repairp[$k]['repair_num']);
                ($k += 1) + -1;
            }
            include $this->mytpl('chartrepair');
            exit(0);
        } elseif ($_GPC['op'] == 'suggest') {
            $suggest_array = array(0, 0, 0, 0, 0, 0);
            $sql = 'select status,count(status) as suggest_num from ' . tablename('rhinfo_zyxq_suggest') . ' group by status';
            $suggest = pdo_fetchall($sql, $params);
            $total = 0;
            $k = 0;
            while (!($k >= count($suggest))) {
                if ($suggest[$k]['status'] == 5) {
                    $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 9) {
                    $suggest_array[5] = $suggest_array[5] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 8) {
                    $suggest_array[4] = $suggest_array[4] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 1) {
                    $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['suggest_num']);
                } else {
                    $suggest_array[$suggest[$k]['status']] = $suggest_array[$suggest[$k]['status']] + intval($suggest[$k]['suggest_num']);
                }
                $total += intval($suggest[$k]['suggest_num']);
                ($k += 1) + -1;
            }
            include $this->mytpl('chartsuggest');
            exit(0);
        } elseif ($_GPC['op'] == 'door') {
            $xAxis_doordata = array();
            $xAxis_cardata = array();
            $k = 10 * 5;
            while ($k >= 0) {
                $i = $k - 5;
                $j = $k;
                $str = 'TIMESTAMPDIFF(MINUTE,DATE_FORMAT(FROM_UNIXTIME(opentime),\'%Y-%m-%d %H:%i:%s\'),DATE_FORMAT(NOW(),\'%Y-%m-%d %H:%i:%s\'))';
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_doorlog') . ' where ' . $str . '>' . $i . ' and ' . $str . '<' . $j;
                $count = pdo_fetchcolumn($sql, $params);
                $xAxis_doordata[] = !empty($count) ? $count : 0;
                $str = 'TIMESTAMPDIFF(MINUTE,DATE_FORMAT(FROM_UNIXTIME(ctime),\'%Y-%m-%d %H:%i:%s\'),DATE_FORMAT(NOW(),\'%Y-%m-%d %H:%i:%s\'))';
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car_iolog') . ' where ' . $str . '>' . $i . ' and ' . $str . '<' . $j;
                $count = pdo_fetchcolumn($sql, $params);
                $xAxis_cardata[] = !empty($count) ? $count : 0;
                $k -= 5;
            }
            $doordata = implode(',', $xAxis_doordata);
            $cardata = implode(',', $xAxis_cardata);
            include $this->mytpl('chartdoor');
            exit(0);
        } elseif ($_GPC['op'] == 'self') {
            $total = 0;
            $charging = array();
            $selfdevice = array();
            $parking = array();
            $k = 2;
            while ($k >= 0) {
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k;
                $charging_fee = pdo_fetchcolumn($sql, $params);
                $charging_fee = empty($charging_fee) ? 0 : $charging_fee;
                $charging[] = $charging_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k;
                $selfdevice_fee = pdo_fetchcolumn($sql, $params);
                $selfdevice_fee = empty($selfdevice_fee) ? 0 : $selfdevice_fee;
                $selfdevice[] = $selfdevice_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k;
                $parking_fee = pdo_fetchcolumn($sql, $params);
                $parking_fee = empty($parking_fee) ? 0 : $parking_fee;
                $parking[] = $parking_fee;
                $total += $charging_fee + $selfdevice_fee + $parking_fee;
                ($k += -1) + 1;
            }
            $selfdata = array();
            $selfdata[] = implode(',', $parking);
            $selfdata[] = implode(',', $charging);
            $selfdata[] = implode(',', $selfdevice);
            include $this->mytpl('chartself');
            exit(0);
        }
        $stats_qty = array();
        $stats_qty['property'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_property'));
        $stats_qty['region'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where category=1 ');
        $stats_qty['shop'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where category=2 ');
        $stats_qty['building'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_building'));
        $stats_qty['room'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_room'));
        $stats_qty['parking'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parking'));
        $title = $this->syscfg['title'];
        include $this->mytpl('index');
        return null;
    }
    public function doPageAdmin()
    {
        global $_W;
        global $_GPC;
        $condition = ' weid = :weid ';
        $params = array(':weid' => $_W['uniacid']);
        if ($_GPC['op'] == 'check_pwd') {
            if ($this->syscfg['board_password'] != $_GPC['password']) {
                echo '密码错误';
            } else {
                echo 'ok';
            }
            exit(0);
        } elseif ($_GPC['op'] == 'bind') {
            $bind_array = array(0, 0, 0);
            $total = 0;
            $m = 0;
            while (!($m >= 3)) {
                $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where otype=' . $m . ' and ' . $condition;
                $bindnum = pdo_fetchcolumn($sql, $params);
                $bind_array[$m] = $bind_array[$m] + $bindnum;
                $total += $bindnum;
                ($m += 1) + -1;
            }
            include $this->mytpl('chartbind');
            exit(0);
        } elseif ($_GPC['op'] == 'feebill') {
            $feebill_array = array(0, 0, 0);
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype in(1,2,4) and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype=9 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[1] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype in(3,5,6,7,8) and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[2] = !empty($payfee) ? $payfee : 0;
            $total = $feebill_array[0] + $feebill_array[1] + $feebill_array[2];
            include $this->mytpl('chartfeebill');
            exit(0);
        } elseif ($_GPC['op'] == 'carfee') {
            $carfee_array = array(0, 0);
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=1 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=2 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[1] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=3 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[2] = !empty($payfee) ? $payfee : 0;
            $total = $carfee_array[0] + $carfee_array[1] + $carfee_array[2];
            include $this->mytpl('chartcarfee');
            exit(0);
        } elseif ($_GPC['op'] == 'payfee') {
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                $total += $fee;
                ($k += -1) + 1;
            }
            $data = implode(',', $xAxis_data);
            include $this->mytpl('chartpay');
            exit(0);
        } elseif ($_GPC['op'] == 'payrate') {
            $feebill_array = array(0, 0);
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1  and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2  and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[1] = !empty($payfee) ? $payfee : 0;
            $total = $feebill_array[0] + $feebill_array[1];
            include $this->mytpl('chartpayrate');
            exit(0);
        } elseif ($_GPC['op'] == 'map') {
            $map_array = array(array('name' => '北京', 'value' => 0), array('name' => '北京', 'value' => 0), array('name' => '天津', 'value' => 0), array('name' => '上海', 'value' => 0), array('name' => '重庆', 'value' => 0), array('name' => '河南', 'value' => 0), array('name' => '云南', 'value' => 0), array('name' => '辽宁', 'value' => 0), array('name' => '黑龙江', 'value' => 0), array('name' => '湖南', 'value' => 0), array('name' => '安徽', 'value' => 0), array('name' => '山东', 'value' => 0), array('name' => '新疆', 'value' => 0), array('name' => '江苏', 'value' => 0), array('name' => '浙江', 'value' => 0), array('name' => '江西', 'value' => 0), array('name' => '湖北', 'value' => 0), array('name' => '广西', 'value' => 0), array('name' => '甘肃', 'value' => 0), array('name' => '山西', 'value' => 0), array('name' => '内蒙古', 'value' => 0), array('name' => '陕西', 'value' => 0), array('name' => '吉林', 'value' => 0), array('name' => '福建', 'value' => 0), array('name' => '贵州', 'value' => 0), array('name' => '广东', 'value' => 0), array('name' => '青海', 'value' => 0), array('name' => '西藏', 'value' => 0), array('name' => '四川', 'value' => 0), array('name' => '宁夏', 'value' => 0), array('name' => '海南', 'value' => 0), array('name' => '台湾', 'value' => 0), array('name' => '香港', 'value' => 0), array('name' => '澳门', 'value' => 0));
            $maptemp1_array = $map_array;
            $maptemp2_array = $map_array;
            $m = 0;
            while (!($m >= count($map_array))) {
                $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:uniacid and resideprovince like "%' . $map_array[$m]['name'] . '%"';
                $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
                $maptemp1_array[$m]['value'] = !empty($count) ? $count : 0;
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where weid=:uniacid and province like "%' . $map_array[$m]['name'] . '%"';
                $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
                $maptemp2_array[$m]['value'] = !empty($count) ? $count : 0;
                ($m += 1) + -1;
            }
            $map1_array = array();
            foreach ($maptemp1_array as $k => $v) {
                if ($v['value'] > 0) {
                    $map1_array[] = $v;
                }
            }
            $map1_array = !empty($map1_array) ? $map1_array : array(array('name' => '', 'value' => '0'));
            $map2_array = array();
            foreach ($maptemp2_array as $k => $v) {
                if ($v['value'] > 0) {
                    $map2_array[] = $v;
                }
            }
            $map2_array = !empty($map2_array) ? $map2_array : array(array('name' => '', 'value' => '0'));
            include $this->mytpl('chartmap');
            exit(0);
        } elseif ($_GPC['op'] == 'member') {
            $member_array = array(0, 0);
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:uniacid and gender=1';
            $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
            $member_array[0] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:uniacid and gender=2';
            $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
            $member_array[1] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:uniacid';
            $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
            $member_array[2] = !empty($count) ? $count : 0;
            $total = $member_array[2];
            $member_array[2] = $member_array[2] - $member_array[0] - $member_array[1];
            include $this->mytpl('chartmember');
            exit(0);
        } elseif ($_GPC['op'] == 'owner') {
            $member_array = array(0, 0, 0, 0);
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:uniacid';
            $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
            $member_array[0] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:uniacid and category=2';
            $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
            $member_array[2] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:uniacid';
            $count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
            $member_array[1] = $count - $member_array[2];
            $member_array[3] = $member_array[0] - $count;
            $total = $member_array[0];
            include $this->mytpl('chartowner');
            exit(0);
        } elseif ($_GPC['op'] == 'costcomp') {
            $money_array = array(0, 0);
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where (io=1 or io=3 or io=4) and status=1 and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[0] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[1] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where (io=1 or io=3 or io=4) and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP . ' and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[2] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP . ' and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[3] = !empty($money) ? $money : 0;
            include $this->mytpl('chartcost');
            exit(0);
        } elseif ($_GPC['op'] == 'cost') {
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(money) from ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and (io=1 or io=3 or io=4) and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                ($k += -1) + 1;
            }
            $indata = implode(',', $xAxis_data);
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(money) from ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and io=2 and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                ($k += -1) + 1;
            }
            $outdata = implode(',', $xAxis_data);
            include $this->mytpl('chartbudget');
            exit(0);
        } elseif ($_GPC['op'] == 'repair') {
            $repair_array = array(0, 0, 0, 0, 0, 0);
            $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition . ' group by status';
            $repair = pdo_fetchall($sql, $params);
            $total = 0;
            $k = 0;
            while (!($k >= count($repair))) {
                if ($repair[$k]['status'] == 5) {
                    $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 9) {
                    $repair_array[5] = $repair_array[5] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 8) {
                    $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 1) {
                    $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                } else {
                    $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
                }
                $total += intval($repair[$k]['repair_num']);
                ($k += 1) + -1;
            }
            $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repairp') . ' where ' . $condition . ' group by status';
            $repairp = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($repairp))) {
                if ($repairp[$k]['status'] == 5) {
                    $repair_array[0] = $repair_array[0] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 9) {
                    $repair_array[5] = $repair_array[5] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 8) {
                    $repair_array[4] = $repair_array[4] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 1) {
                    $repair_array[0] = $repair_array[0] + intval($repairp[$k]['repair_num']);
                } else {
                    $repair_array[$repairp[$k]['status']] = $repair_array[$repairp[$k]['status']] + intval($repairp[$k]['repair_num']);
                }
                $total += intval($repairp[$k]['repair_num']);
                ($k += 1) + -1;
            }
            include $this->mytpl('chartrepair');
            exit(0);
        } elseif ($_GPC['op'] == 'suggest') {
            $suggest_array = array(0, 0, 0, 0, 0, 0);
            $sql = 'select status,count(status) as suggest_num from ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition . ' group by status';
            $suggest = pdo_fetchall($sql, $params);
            $total = 0;
            $k = 0;
            while (!($k >= count($suggest))) {
                if ($suggest[$k]['status'] == 5) {
                    $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 9) {
                    $suggest_array[5] = $suggest_array[5] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 8) {
                    $suggest_array[4] = $suggest_array[4] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 1) {
                    $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['suggest_num']);
                } else {
                    $suggest_array[$suggest[$k]['status']] = $suggest_array[$suggest[$k]['status']] + intval($suggest[$k]['suggest_num']);
                }
                $total += intval($suggest[$k]['suggest_num']);
                ($k += 1) + -1;
            }
            include $this->mytpl('chartsuggest');
            exit(0);
        } elseif ($_GPC['op'] == 'door') {
            $xAxis_doordata = array();
            $xAxis_cardata = array();
            $k = 10 * 5;
            while ($k >= 0) {
                $i = $k - 5;
                $j = $k;
                $str = 'TIMESTAMPDIFF(MINUTE,DATE_FORMAT(FROM_UNIXTIME(opentime),\'%Y-%m-%d %H:%i:%s\'),DATE_FORMAT(NOW(),\'%Y-%m-%d %H:%i:%s\'))';
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_doorlog') . ' where ' . $str . '>' . $i . ' and ' . $str . '<' . $j . ' and ' . $condition;
                $count = pdo_fetchcolumn($sql, $params);
                $xAxis_doordata[] = !empty($count) ? $count : 0;
                $str = 'TIMESTAMPDIFF(MINUTE,DATE_FORMAT(FROM_UNIXTIME(ctime),\'%Y-%m-%d %H:%i:%s\'),DATE_FORMAT(NOW(),\'%Y-%m-%d %H:%i:%s\'))';
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car_iolog') . ' where ' . $str . '>' . $i . ' and ' . $str . '<' . $j . ' and ' . $condition;
                $count = pdo_fetchcolumn($sql, $params);
                $xAxis_cardata[] = !empty($count) ? $count : 0;
                $k -= 5;
            }
            $doordata = implode(',', $xAxis_doordata);
            $cardata = implode(',', $xAxis_cardata);
            include $this->mytpl('chartdoor');
            exit(0);
        } elseif ($_GPC['op'] == 'self') {
            $total = 0;
            $charging = array();
            $selfdevice = array();
            $parking = array();
            $k = 2;
            while ($k >= 0) {
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $charging_fee = pdo_fetchcolumn($sql, $params);
                $charging_fee = empty($charging_fee) ? 0 : $charging_fee;
                $charging[] = $charging_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $selfdevice_fee = pdo_fetchcolumn($sql, $params);
                $selfdevice_fee = empty($selfdevice_fee) ? 0 : $selfdevice_fee;
                $selfdevice[] = $selfdevice_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $parking_fee = pdo_fetchcolumn($sql, $params);
                $parking_fee = empty($parking_fee) ? 0 : $parking_fee;
                $parking[] = $parking_fee;
                $total += $charging_fee + $selfdevice_fee + $parking_fee;
                ($k += -1) + 1;
            }
            $selfdata = array();
            $selfdata[] = implode(',', $parking);
            $selfdata[] = implode(',', $charging);
            $selfdata[] = implode(',', $selfdevice);
            include $this->mytpl('chartself');
            exit(0);
        }
        $stats_qty = array();
        $stats_qty['property'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_property') . ' where ' . $condition, $params);
        $stats_qty['region'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where category=1 and ' . $condition, $params);
        $stats_qty['shop'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where category=2 and ' . $condition, $params);
        $stats_qty['building'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_building') . ' where ' . $condition, $params);
        $stats_qty['room'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition, $params);
        $stats_qty['parking'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where ' . $condition, $params);
        $title = $this->syscfg['title'];
        include $this->mytpl('index');
        return null;
    }
    public function doPageProperty()
    {
        global $_W;
        global $_GPC;
        $condition = ' weid = :weid and pid = :pid';
        $params = array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid']);
        if ($_GPC['op'] == 'check_pwd') {
            $property = pdo_get('rhinfo_zyxq_property', array('weid' => $_W['uniacid'], 'id' => $_GPC['pid']), array('board_password'));
            if ($property['board_password'] != $_GPC['password']) {
                echo '密码错误';
            } else {
                echo 'ok';
            }
            exit(0);
        } elseif ($_GPC['op'] == 'bind') {
            $bind_array = array(0, 0, 0);
            $total = 0;
            $m = 0;
            while (!($m >= 3)) {
                $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where otype=' . $m . ' and ' . $condition;
                $bindnum = pdo_fetchcolumn($sql, $params);
                $bind_array[$m] = $bind_array[$m] + $bindnum;
                $total += $bindnum;
                ($m += 1) + -1;
            }
            include $this->mytpl('chartbind');
            exit(0);
        } elseif ($_GPC['op'] == 'feebill') {
            $feebill_array = array(0, 0, 0);
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype in(1,2,4) and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype=9 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[1] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2 and paytype in(3,5,6,7,8) and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[2] = !empty($payfee) ? $payfee : 0;
            $total = $feebill_array[0] + $feebill_array[1] + $feebill_array[2];
            include $this->mytpl('chartfeebill');
            exit(0);
        } elseif ($_GPC['op'] == 'carfee') {
            $carfee_array = array(0, 0);
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=1 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=2 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[1] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where category=3 and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $carfee_array[2] = !empty($payfee) ? $payfee : 0;
            $total = $carfee_array[0] + $carfee_array[1] + $carfee_array[2];
            include $this->mytpl('chartcarfee');
            exit(0);
        } elseif ($_GPC['op'] == 'payfee') {
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                $total += $fee;
                ($k += -1) + 1;
            }
            $data = implode(',', $xAxis_data);
            include $this->mytpl('chartpay');
            exit(0);
        } elseif ($_GPC['op'] == 'payrate') {
            $feebill_array = array(0, 0);
            $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1  and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[0] = !empty($payfee) ? $payfee : 0;
            $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=2  and ' . $condition;
            $payfee = pdo_fetchcolumn($sql, $params);
            $feebill_array[1] = !empty($payfee) ? $payfee : 0;
            $total = $feebill_array[0] + $feebill_array[1];
            include $this->mytpl('chartpayrate');
            exit(0);
        } elseif ($_GPC['op'] == 'map') {
            $map_array = array(array('name' => '北京', 'value' => 0), array('name' => '北京', 'value' => 0), array('name' => '天津', 'value' => 0), array('name' => '上海', 'value' => 0), array('name' => '重庆', 'value' => 0), array('name' => '河南', 'value' => 0), array('name' => '云南', 'value' => 0), array('name' => '辽宁', 'value' => 0), array('name' => '黑龙江', 'value' => 0), array('name' => '湖南', 'value' => 0), array('name' => '安徽', 'value' => 0), array('name' => '山东', 'value' => 0), array('name' => '新疆', 'value' => 0), array('name' => '江苏', 'value' => 0), array('name' => '浙江', 'value' => 0), array('name' => '江西', 'value' => 0), array('name' => '湖北', 'value' => 0), array('name' => '广西', 'value' => 0), array('name' => '甘肃', 'value' => 0), array('name' => '山西', 'value' => 0), array('name' => '内蒙古', 'value' => 0), array('name' => '陕西', 'value' => 0), array('name' => '吉林', 'value' => 0), array('name' => '福建', 'value' => 0), array('name' => '贵州', 'value' => 0), array('name' => '广东', 'value' => 0), array('name' => '青海', 'value' => 0), array('name' => '西藏', 'value' => 0), array('name' => '四川', 'value' => 0), array('name' => '宁夏', 'value' => 0), array('name' => '海南', 'value' => 0), array('name' => '台湾', 'value' => 0), array('name' => '香港', 'value' => 0), array('name' => '澳门', 'value' => 0));
            $maptemp1_array = $map_array;
            $maptemp2_array = $map_array;
            $condition = ' and groupid in(select groupid from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid)';
            $m = 0;
            while (!($m >= count($map_array))) {
                $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:weid and resideprovince like "%' . $map_array[$m]['name'] . '%"' . $condition;
                $count = pdo_fetchcolumn($sql, $params);
                $maptemp1_array[$m]['value'] = !empty($count) ? $count : 0;
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid and province like "%' . $map_array[$m]['name'] . '%"';
                $count = pdo_fetchcolumn($sql, $params);
                $maptemp2_array[$m]['value'] = !empty($count) ? $count : 0;
                ($m += 1) + -1;
            }
            $map1_array = array();
            foreach ($maptemp1_array as $k => $v) {
                if ($v['value'] > 0) {
                    $map1_array[] = $v;
                }
            }
            $map1_array = !empty($map1_array) ? $map1_array : array(array('name' => '', 'value' => '0'));
            $map2_array = array();
            foreach ($maptemp2_array as $k => $v) {
                if ($v['value'] > 0) {
                    $map2_array[] = $v;
                }
            }
            $map2_array = !empty($map2_array) ? $map2_array : array(array('name' => '', 'value' => '0'));
            include $this->mytpl('chartmap');
            exit(0);
        } elseif ($_GPC['op'] == 'member') {
            $member_array = array(0, 0);
            $condition = ' and groupid in(select groupid from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid)';
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:weid and gender=1' . $condition;
            $count = pdo_fetchcolumn($sql, $params);
            $member_array[0] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:weid and gender=2' . $condition;
            $count = pdo_fetchcolumn($sql, $params);
            $member_array[1] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:weid' . $condition;
            $count = pdo_fetchcolumn($sql, $params);
            $member_array[2] = !empty($count) ? $count : 0;
            $total = $member_array[2];
            $member_array[2] = $member_array[2] - $member_array[0] - $member_array[1];
            include $this->mytpl('chartmember');
            exit(0);
        } elseif ($_GPC['op'] == 'owner') {
            $member_array = array(0, 0, 0, 0);
            $condition = ' and groupid in(select groupid from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and pid=:pid)';
            $sql = 'select count(*) from ' . tablename('mc_members') . ' where uniacid=:weid' . $condition;
            $count = pdo_fetchcolumn($sql, $params);
            $member_array[0] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and category=2' . $condition;
            $count = pdo_fetchcolumn($sql, $params);
            $member_array[2] = !empty($count) ? $count : 0;
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid' . $condition;
            $count = pdo_fetchcolumn($sql, $params);
            $member_array[1] = $count - $member_array[2];
            $member_array[3] = $member_array[0] - $count;
            $total = $member_array[0];
            include $this->mytpl('chartowner');
            exit(0);
        } elseif ($_GPC['op'] == 'costcomp') {
            $money_array = array(0, 0);
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where (io=1 or io=3 or io=4) and status=1 and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[0] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[1] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where (io=1 or io=3 or io=4) and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP . ' and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[2] = !empty($money) ? $money : 0;
            $sql = 'SELECT sum(money) FROM ' . tablename('rhinfo_zyxq_costdetail') . ' where io=2 and status=1 and ctime>0 and ctime>=' . strtotime(date('Y-m') . '-01') . ' and ctime<=' . TIMESTAMP . ' and ' . $condition;
            $money = pdo_fetchcolumn($sql, $params);
            $money_array[3] = !empty($money) ? $money : 0;
            include $this->mytpl('chartcost');
            exit(0);
        } elseif ($_GPC['op'] == 'cost') {
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(money) from ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and (io=1 or io=3 or io=4) and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                ($k += -1) + 1;
            }
            $indata = implode(',', $xAxis_data);
            $xAxis_data = array();
            $k = 6;
            while ($k >= 0) {
                $sql = 'select sum(money) from ' . tablename('rhinfo_zyxq_costdetail') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and io=2 and ' . $condition;
                $fee = pdo_fetchcolumn($sql, $params);
                $xAxis_data[] = !empty($fee) ? $fee : 0;
                ($k += -1) + 1;
            }
            $outdata = implode(',', $xAxis_data);
            include $this->mytpl('chartbudget');
            exit(0);
        } elseif ($_GPC['op'] == 'repair') {
            $repair_array = array(0, 0, 0, 0, 0, 0);
            $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition . ' group by status';
            $repair = pdo_fetchall($sql, $params);
            $total = 0;
            $k = 0;
            while (!($k >= count($repair))) {
                if ($repair[$k]['status'] == 5) {
                    $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 9) {
                    $repair_array[5] = $repair_array[5] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 8) {
                    $repair_array[4] = $repair_array[4] + intval($repair[$k]['repair_num']);
                } elseif ($repair[$k]['status'] == 1) {
                    $repair_array[0] = $repair_array[0] + intval($repair[$k]['repair_num']);
                } else {
                    $repair_array[$repair[$k]['status']] = $repair_array[$repair[$k]['status']] + intval($repair[$k]['repair_num']);
                }
                $total += intval($repair[$k]['repair_num']);
                ($k += 1) + -1;
            }
            $sql = 'select status,count(status) as repair_num from ' . tablename('rhinfo_zyxq_repairp') . ' where ' . $condition . ' group by status';
            $repairp = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($repairp))) {
                if ($repairp[$k]['status'] == 5) {
                    $repair_array[0] = $repair_array[0] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 9) {
                    $repair_array[5] = $repair_array[5] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 8) {
                    $repair_array[4] = $repair_array[4] + intval($repairp[$k]['repair_num']);
                } elseif ($repairp[$k]['status'] == 1) {
                    $repair_array[0] = $repair_array[0] + intval($repairp[$k]['repair_num']);
                } else {
                    $repair_array[$repairp[$k]['status']] = $repair_array[$repairp[$k]['status']] + intval($repairp[$k]['repair_num']);
                }
                $total += intval($repairp[$k]['repair_num']);
                ($k += 1) + -1;
            }
            include $this->mytpl('chartrepair');
            exit(0);
        } elseif ($_GPC['op'] == 'suggest') {
            $suggest_array = array(0, 0, 0, 0, 0, 0);
            $sql = 'select status,count(status) as suggest_num from ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition . ' group by status';
            $suggest = pdo_fetchall($sql, $params);
            $total = 0;
            $k = 0;
            while (!($k >= count($suggest))) {
                if ($suggest[$k]['status'] == 5) {
                    $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 9) {
                    $suggest_array[5] = $suggest_array[5] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 8) {
                    $suggest_array[4] = $suggest_array[4] + intval($suggest[$k]['suggest_num']);
                } elseif ($suggest[$k]['status'] == 1) {
                    $suggest_array[0] = $suggest_array[0] + intval($suggest[$k]['suggest_num']);
                } else {
                    $suggest_array[$suggest[$k]['status']] = $suggest_array[$suggest[$k]['status']] + intval($suggest[$k]['suggest_num']);
                }
                $total += intval($suggest[$k]['suggest_num']);
                ($k += 1) + -1;
            }
            include $this->mytpl('chartsuggest');
            exit(0);
        } elseif ($_GPC['op'] == 'door') {
            $xAxis_doordata = array();
            $xAxis_cardata = array();
            $k = 10 * 5;
            while ($k >= 0) {
                $i = $k - 5;
                $j = $k;
                $str = 'TIMESTAMPDIFF(MINUTE,DATE_FORMAT(FROM_UNIXTIME(opentime),\'%Y-%m-%d %H:%i:%s\'),DATE_FORMAT(NOW(),\'%Y-%m-%d %H:%i:%s\'))';
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_doorlog') . ' where ' . $str . '>' . $i . ' and ' . $str . '<' . $j . ' and ' . $condition;
                $count = pdo_fetchcolumn($sql, $params);
                $xAxis_doordata[] = !empty($count) ? $count : 0;
                $str = 'TIMESTAMPDIFF(MINUTE,DATE_FORMAT(FROM_UNIXTIME(ctime),\'%Y-%m-%d %H:%i:%s\'),DATE_FORMAT(NOW(),\'%Y-%m-%d %H:%i:%s\'))';
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car_iolog') . ' where ' . $str . '>' . $i . ' and ' . $str . '<' . $j . ' and ' . $condition;
                $count = pdo_fetchcolumn($sql, $params);
                $xAxis_cardata[] = !empty($count) ? $count : 0;
                $k -= 5;
            }
            $doordata = implode(',', $xAxis_doordata);
            $cardata = implode(',', $xAxis_cardata);
            include $this->mytpl('chartdoor');
            exit(0);
        } elseif ($_GPC['op'] == 'self') {
            $total = 0;
            $charging = array();
            $selfdevice = array();
            $parking = array();
            $k = 2;
            while ($k >= 0) {
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_charging_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $charging_fee = pdo_fetchcolumn($sql, $params);
                $charging_fee = empty($charging_fee) ? 0 : $charging_fee;
                $charging[] = $charging_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zycj_selfdevice_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $selfdevice_fee = pdo_fetchcolumn($sql, $params);
                $selfdevice_fee = empty($selfdevice_fee) ? 0 : $selfdevice_fee;
                $selfdevice[] = $selfdevice_fee;
                $sql = 'SELECT sum(fee) FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(ctime),\'%y-%m-%d\')) =' . $k . ' and ' . $condition;
                $parking_fee = pdo_fetchcolumn($sql, $params);
                $parking_fee = empty($parking_fee) ? 0 : $parking_fee;
                $parking[] = $parking_fee;
                $total += $charging_fee + $selfdevice_fee + $parking_fee;
                ($k += -1) + 1;
            }
            $selfdata = array();
            $selfdata[] = implode(',', $parking);
            $selfdata[] = implode(',', $charging);
            $selfdata[] = implode(',', $selfdevice);
            include $this->mytpl('chartself');
            exit(0);
        }
        $stats_qty = array();
        $stats_qty['device'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where ' . $condition, $params);
        $stats_qty['region'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where category=1 and ' . $condition, $params);
        $stats_qty['shop'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where category=2 and ' . $condition, $params);
        $stats_qty['building'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_building') . ' where ' . $condition, $params);
        $stats_qty['room'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition, $params);
        $stats_qty['parking'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where ' . $condition, $params);
        $property = pdo_get('rhinfo_zyxq_property', array('weid' => $_W['uniacid'], 'id' => $_GPC['pid']), array('title', 'logo', 'board_password'));
        $title = $property['title'];
        include $this->mytpl('index');
        return null;
    }
    public function mytpl($filename = '')
    {
        global $_W;
        global $_GPC;
        $filename = empty($filename) ? 'index' : $filename;
        $filename = $_GPC['do'] . '/' . $filename;
        return $this->template($filename);
    }
}
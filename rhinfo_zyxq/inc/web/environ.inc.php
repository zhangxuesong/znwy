<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'clist';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'environ';
$tablename = 'rhinfo_zyxq_environ_arrange';
$condition = ' weid = :weid ';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '环境管理';
$rights = $this->myrights(5, $mydo, 'clist');
if ($operation == 'clist') {
    $current = '保洁记录';
    $myret = 0;
    $condition .= $this->myrcondition();
    $tablename = 'rhinfo_zyxq_environ_record';
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename);
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id,pname,rname,hname,CONCAT(lname,bname,tname) as area,cname,areacont,phone,startdate,simdate,cleantion,img from ' . tablename($tablename) . ' ORDER BY `ID` ASC ' . $limit;
    $data = pdo_fetchall($sql, $params);
    foreach ($data as $k => $v) {
        $arr = explode(':', $data[$k]['img']);
        $data[$k]['img'] = $arr;
    }
    $pager = pagination($total, $pindex, $psize);
    include $this->mywtpl('list');
} elseif ($operation == 'cleaning') {
    $current = '保洁安排';
    $myret = 0;
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename);
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id,pname,rname,CONCAT(lname,"-",bname,"-",tname) as area,hname,areacont,ask,cycle,ctime,CONCAT(startdate," ",starttime) as starttime,CONCAT(simdate," ",endtime) as endtime from ' . tablename($tablename) . ' ORDER BY `ID` ASC ' . $limit;
    $data = pdo_fetchall($sql, $params);
    $pager = pagination($total, $pindex, $psize);
    include $this->mywtpl('arrange');
} elseif ($operation == 'green') {
    $current = '绿化信息';
    $myret = 0;
    $condition .= $this->myrcondition();
    $tablename = 'rhinfo_zyxq_environ_greeninfo';
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename);
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id,pid,pname,rname,CONCAT(lname,"-",bname,"-",tname) as area,hname,vegeid,vegename,kind,treeage,num,startdate,simdate from ' . tablename($tablename) . ' ORDER BY `ID` ASC ' . $limit;
    $data = pdo_fetchall($sql, $params);
    $pager = pagination($total, $pindex, $psize);
    include $this->mywtpl('greeninfo');
} elseif ($operation == 'glist') {
    $current = '绿化记录';
    $myret = 0;
    $condition .= $this->myrcondition();
    $tablename = 'rhinfo_zyxq_environ_grecord';
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename);
    $total = pdo_fetchcolumn($sql, $params);
    $sql = 'select id,pid,pname,rname,CONCAT(lname," ",bname," ",tname) as area,hname,vegeid,vegename,kind,num,planter,phone,img,simdate from ' . tablename($tablename) . ' ORDER BY `ID` ASC ' . $limit;
    $data = pdo_fetchall($sql, $params);
    foreach ($data as $k => $v) {
        $arr = explode(':', $data[$k]['img']);
        $data[$k]['img'] = $arr;
    }
    $pager = pagination($total, $pindex, $psize);
    include $this->mywtpl('glist');
} elseif ($operation == 'radd') {
    $current = '新增保洁记录';
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_record';
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        if ($_GPC['cleantion'] == '1') {
            $cleantion = '已完成';
        } elseif ($_GPC['cleantion'] == '2') {
            $cleantion = '未完成';
        } elseif ($_GPC['cleantion'] == '3') {
            $cleantion = '还在进行中';
        }
        $list = array('pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'cname' => $_GPC['cname'], 'cleantion' => $cleantion, 'areacont' => $_GPC['areacont'], 'phone' => $_GPC['phone'], 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $result = pdo_insert($tablename, $list);
        show_json('1', '添加成功');
        header('Location:' . $this->createWeburl($mydo, array('op' => 'clist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('rpost');
} elseif ($operation == 'redit') {
    $current = '编辑保洁记录';
    $id = intval($_GPC['id']);
    $tablename = 'rhinfo_zyxq_environ_record';
    if ($_W['isajax']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        if ($_GPC['cleantion'] == '1') {
            $cleantion = '已完成';
        } elseif ($_GPC['cleantion'] == '2') {
            $cleantion = '未完成';
        } elseif ($_GPC['cleantion'] == '3') {
            $cleantion = '还在进行中';
        }
        if ($_GPC['lid'] == '0') {
            $sql = 'select lname from ' . tablename($tablename) . ' where id = :id ';
            $na = pdo_fetch($sql, array(':id' => $_GPC['id']));
            $lname = $na['lname'];
        }
        $arr = array('id' => $_GPC['id'], 'pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'cname' => $_GPC['cname'], 'cleantion' => $cleantion, 'areacont' => $_GPC['areacont'], 'phone' => $_GPC['phone'], 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $glue = 'AND';
        $result = pdo_update($tablename, $arr, array('id' => $arr['id']), 'AND');
        if ($result) {
            show_json(1, '修改成功');
        } else {
            show_json(0 - 1, '修改失败');
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'clist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,pid,pname,rid,rname,hid,hname,lid,lname,bid,bname,tid,tname,cname,areacont,phone,img,startdate,simdate,cleantion from ' . tablename($tablename) . ' where id = :id ';
    $item = pdo_fetch($sql, array(':id' => $id));
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('rpost');
} elseif ($operation == 'rsearch') {
    $content = $_GPC['mycontent'];
    $tablename = 'rhinfo_zyxq_environ_record';
    $sql = 'select count(*) as num from ' . tablename($tablename) . ' where pname LIKE :pname';
    $list = pdo_fetch($sql, array(':pname' => '%' . $content . '%'));
    $sql = 'select id,pname,rname,hname,CONCAT(lname,bname,tname) as area,cname,areacont,img,phone,startdate,simdate,cleantion from ' . tablename($tablename) . ' where pname LIKE :pname' . $limit;
    $data = pdo_fetchall($sql, array(':pname' => '%' . $content . '%'));
    if (empty($data)) {
        $sql = 'select count(*) as num from ' . tablename($tablename) . ' where cname LIKE :cname';
        $list = pdo_fetch($sql, array(':cname' => '%' . $content . '%'));
        $sql = 'select id,pname,rname,hname,CONCAT(lname,bname,tname) as area,cname,areacont,phone,img,startdate,simdate,cleantion from ' . tablename($tablename) . ' where cname LIKE :cname' . $limit;
        $data = pdo_fetchall($sql, array(':cname' => '%' . $content . '%'));
    }
    foreach ($data as $k => $v) {
        $arr = explode(':', $data[$k]['img']);
        $data[$k]['img'] = $arr;
    }
    $total = $list['num'];
    $pager = pagination($total, $pindex, $psize);
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    include $this->mywtpl('list');
} elseif ($operation == 'rdelete') {
    $current = '删除保洁记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_environ_record', array('id' => $id), 'AND');
    if ($result) {
        show_json(1, '删除成功');
    } else {
        show_json(0 - 1, '删除失败');
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
    exit(0);
} elseif ($operation == 'add') {
    $current = '新增安排';
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_arrange';
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        if ($_GPC['starttime'] == '' || $_GPC['endtime'] == '') {
            $time1 = ' ';
            $time2 = ' ';
        } else {
            $time1 = $_GPC['starttime'] . ' ' . '到' . ' ';
            $time2 = $_GPC['endtime'];
        }
        if ($_GPC['cycle'] == '1') {
            $cycle = '按天';
            $time = '每天' . ' ' . $time1 . $time2;
        } elseif ($_GPC['cycle'] == '2') {
            $cycle = '按星期';
            if ($_GPC['week'] == '1') {
                $week = '一';
            } elseif ($_GPC['week'] == '2') {
                $week = '二';
            } elseif ($_GPC['week'] == '3') {
                $week = '三';
            } elseif ($_GPC['week'] == '4') {
                $week = '四';
            } elseif ($_GPC['week'] == '5') {
                $week = '五';
            } elseif ($_GPC['week'] == '6') {
                $week = '六';
            } elseif ($_GPC['week'] == '7') {
                $week = '日';
            }
            $time = '每星期' . $week . ' ' . $time1 . $time2;
        } elseif ($_GPC['cycle'] == '3') {
            $cycle = '按月';
            $time = '每' . $_GPC['month'] . '个月' . ' ' . $time1 . $time2;
        } elseif ($_GPC['cycle'] == '4') {
            $cycle = '按年';
            $time = '每年' . $_GPC['month'] . '月' . $_GPC['day'] . '日' . ' ' . $time1 . $time2;
        } else {
            $cycle = '按天';
            $time = '每天' . ' ' . $time1 . $time2;
        }
        $list = array('pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'areacont' => $_GPC['areacont'], 'ask' => $_GPC['ask'], 'cycleid' => $_GPC['cycle'], 'cycle' => $cycle, 'ctime' => $time, 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $result = pdo_insert($tablename, $list);
        show_json('1', '添加成功');
        header('Location:' . $this->createWeburl($mydo, array('op' => 'cleaning')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑安排';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_arrange';
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        if ($_GPC['starttime'] == '' || $_GPC['endtime'] == '') {
            $time1 = '';
            $time2 = '';
        } else {
            $time1 = $_GPC['starttime'] . ' ' . '到' . ' ';
            $time2 = $_GPC['endtime'];
        }
        if ($_GPC['cycle'] == '1') {
            $cycle = '按天';
            $time = '每天' . ' ' . $time1 . $time2;
        } elseif ($_GPC['cycle'] == '2') {
            $cycle = '按星期';
            if ($_GPC['week'] == '1') {
                $week = '一';
            } elseif ($_GPC['week'] == '2') {
                $week = '二';
            } elseif ($_GPC['week'] == '3') {
                $week = '三';
            } elseif ($_GPC['week'] == '4') {
                $week = '四';
            } elseif ($_GPC['week'] == '5') {
                $week = '五';
            } elseif ($_GPC['week'] == '6') {
                $week = '六';
            } elseif ($_GPC['week'] == '7') {
                $week = '日';
            }
            $time = '每星期' . $week . ' ' . $time1 . $time2;
        } elseif ($_GPC['cycle'] == '3') {
            $cycle = '按月';
            $time = '每' . $_GPC['month'] . '个月' . ' ' . $time1 . $time2;
        } elseif ($_GPC['cycle'] == '4') {
            $cycle = '按年';
            $time = '每年' . $_GPC['month'] . '月' . $_GPC['day'] . '日' . ' ' . $time1 . $time2;
        } else {
            $cycle = '按天';
            $time = '每天' . ' ' . $time1 . $time2;
        }
        $arr = array('id' => $_GPC['id'], 'pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'areacont' => $_GPC['areacont'], 'ask' => $_GPC['ask'], 'cycleid' => $_GPC['cycle'], 'cycle' => $cycle, 'ctime' => $time, 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $glue = 'AND';
        $result = pdo_update($tablename, $arr, array('id' => $arr['id']), 'AND');
        if ($result) {
            show_json(1, '修改成功');
        } else {
            show_json(0 - 1, '修改失败');
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,pid,pname,rid,rname,lid,lname,bid,bname,tid,tname,hid,hname,areacont,ask,cycleid as cycle,startdate,starttime,simdate,endtime from ' . tablename($tablename) . ' where id = :id ';
    $item = pdo_fetch($sql, array(':id' => $id));
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'search') {
    $content = $_GPC['mycontent'];
    $sql = 'select count(*) as num from ' . tablename($tablename) . ' where pname LIKE :pname';
    $list = pdo_fetch($sql, array(':pname' => '%' . $content . '%'));
    $sql = 'select id,pname,rname,CONCAT(lname,\'-\',bname,\'-\',tname) as area,hname,areacont,ask,cycle,ctime,CONCAT(startdate,\' \',starttime) as starttime,CONCAT(simdate,\' \',endtime) as endtime from ' . tablename($tablename) . ' where pname LIKE :pname' . $limit;
    $data = pdo_fetchall($sql, array(':pname' => '%' . $content . '%'));
    if (empty($data)) {
        $sql = 'select count(*) as num from ' . tablename($tablename) . ' where hname LIKE :hname';
        $list = pdo_fetch($sql, array(':hname' => '%' . $content . '%'));
        $sql = 'select id,pname,rname,CONCAT(lname,\'-\',bname,\'-\',tname) as area,hname,areacont,ask,cycle,ctime,CONCAT(startdate,\' \',starttime) as starttime,CONCAT(simdate,\' \',endtime) as endtime from ' . tablename($tablename) . ' where hname LIKE :hname' . $limit;
        $data = pdo_fetchall($sql, array(':hname' => '%' . $content . '%'));
    }
    $total = $list['num'];
    $pager = pagination($total, $pindex, $psize);
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    include $this->mywtpl('arrange');
} elseif ($operation == 'delete') {
    $current = '删除安排';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_environ_arrange', array('id' => $id), 'AND');
    if ($result) {
        show_json(1, '删除成功');
    } else {
        show_json(0 - 1, '删除失败');
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
    exit(0);
} elseif ($operation == 'gInfoAdd') {
    $current = '新增绿化信息';
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_greeninfo';
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        $list = array('pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'vegeid' => $_GPC['vegeid'], 'vegename' => $_GPC['vegename'], 'kind' => $_GPC['kind'], 'treeage' => $_GPC['treeage'], 'num' => $_GPC['num'], 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $result = pdo_insert($tablename, $list);
        show_json('1', '添加成功');
        header('Location:' . $this->createWeburl($mydo, array('op' => 'green')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('greenpost');
} elseif ($operation == 'greenedit') {
    $current = '编辑绿化信息';
    $id = intval($_GPC['id']);
    $tablename = 'rhinfo_zyxq_environ_greeninfo';
    if ($_W['isajax']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        $arr = array('id' => $_GPC['id'], 'pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'vegeid' => $_GPC['vegeid'], 'vegename' => $_GPC['vegename'], 'kind' => $_GPC['kind'], 'treeage' => $_GPC['treeage'], 'num' => $_GPC['num'], 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $glue = 'AND';
        $result = pdo_update($tablename, $arr, array('id' => $arr['id']), 'AND');
        if ($result) {
            show_json(1, '修改成功');
        } else {
            show_json(0 - 1, '修改失败');
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'green')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,pid,pname,rid,rname,lid,lname,bid,bname,tid,tname,hid,hname,vegeid,vegename,kind,treeage,num,startdate,simdate from ' . tablename($tablename) . ' where id = :id ';
    $item = pdo_fetch($sql, array(':id' => $id));
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('greenpost');
} elseif ($operation == 'greendel') {
    $current = '删除绿化信息';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_environ_greeninfo', array('id' => $id), 'AND');
    if ($result) {
        $msg = '删除成功';
    } else {
        show_json(0 - 1, array('msg' => '删除失败'));
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id . $msg);
    header('Location:' . $this->createWeburl($mydo, array('op' => 'green')) . $mywe['direct']);
    exit(0);
} elseif ($operation == 'grsearch') {
    $content = $_GPC['mycontent'];
    $tablename = 'rhinfo_zyxq_environ_greeninfo';
    $sql = 'select count(*) as num from ' . tablename($tablename) . ' where pname LIKE :pname';
    $list = pdo_fetch($sql, array(':pname' => '%' . $content . '%'));
    $sql = 'select id,pid,pname,rname,CONCAT(lname,\'-\',bname,\'-\',tname) as area,hname,vegeid,vegename,kind,treeage,num,startdate,simdate from ' . tablename($tablename) . ' where pname LIKE :pname' . $limit;
    $data = pdo_fetchall($sql, array(':pname' => '%' . $content . '%'));
    if (empty($data)) {
        $sql = 'select count(*) as num from ' . tablename($tablename) . ' where kind LIKE :kind';
        $list = pdo_fetch($sql, array(':kind' => '%' . $content . '%'));
        $sql = 'select id,pid,pname,rname,CONCAT(lname,\'-\',bname,\'-\',tname) as area,hname,vegeid,vegename,kind,treeage,num,startdate,simdate from ' . tablename($tablename) . ' where kind LIKE :kind' . $limit;
        $data = pdo_fetchall($sql, array(':kind' => '%' . $content . '%'));
    }
    $total = $list['num'];
    $pager = pagination($total, $pindex, $psize);
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    include $this->mywtpl('greeninfo');
} elseif ($operation == 'gadd') {
    $current = '新增绿化记录';
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_grecord';
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        $list = array('pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'vegeid' => $_GPC['vegeid'], 'vegename' => $_GPC['vegename'], 'kind' => $_GPC['kind'], 'num' => $_GPC['num'], 'planter' => $_GPC['planter'], 'simdate' => $_GPC['simdate']);
        $result = pdo_insert($tablename, $list);
        show_json('1', '添加成功', 'gadd');
        header('Location:' . $this->createWeburl($mydo, array('op' => 'glist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $vegenames = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('gpost');
} elseif ($operation == 'gedit') {
    $current = '编辑绿化记录';
    $id = intval($_GPC['id']);
    $tablename = 'rhinfo_zyxq_environ_grecord';
    if ($_W['isajax']) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $_GPC['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :lid';
        $lname = pdo_fetchcolumn($sql, array(':lid' => $_GPC['lid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :bid';
        $bname = pdo_fetchcolumn($sql, array(':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :tid';
        $tname = pdo_fetchcolumn($sql, array(':tid' => $_GPC['tid']));
        $sql = 'select realname from ' . tablename('rhinfo_zyxq_team') . ' where id = :hid';
        $hname = pdo_fetchcolumn($sql, array(':hid' => $_GPC['hid']));
        if ($_GPC['lid'] == '0') {
            $sql = 'select lname,hname from ' . tablename($tablename) . ' where id = :id ';
            $na = pdo_fetch($sql, array(':id' => $_GPC['id']));
            $lname = $na['lname'];
            $hname = $na['hname'];
        }
        $arr = array('id' => $_GPC['id'], 'pid' => $_GPC['pid'], 'pname' => $pname, 'rid' => $_GPC['rid'], 'rname' => $rname, 'lid' => $_GPC['lid'], 'lname' => $lname, 'bid' => $_GPC['bid'], 'bname' => $bname, 'tid' => $_GPC['tid'], 'tname' => $tname, 'hid' => $_GPC['hid'], 'hname' => $hname, 'vegeid' => $_GPC['vegeid'], 'vegename' => $_GPC['vegename'], 'kind' => $_GPC['kind'], 'num' => $_GPC['num'], 'planter' => $_GPC['planter'], 'simdate' => $_GPC['simdate']);
        $glue = 'AND';
        $result = pdo_update($tablename, $arr, array('id' => $arr['id']), 'AND');
        if ($result) {
            show_json(1, '修改成功', 'edit');
        } else {
            show_json(0 - 1, '修改失败', 'edit');
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'glist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,pid,pname,rid,rname,lid,lname,bid,bname,tid,tname,hid,hname,vegeid,vegename,kind,num,planter,simdate from ' . tablename($tablename) . ' where id = :id ';
    $item = pdo_fetch($sql, array(':id' => $id));
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mylocation = array();
    $mybuilding = array();
    $myunit = array();
    $headpers = array();
    $vegenames = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $locations = array();
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where category=3 and weid = :weid and pid = :pid and rid = :rid';
            $locations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            if (empty($locations)) {
                $locations[] = array('id' => 0, 'title' => '无');
            }
            $mylocation[$regions[$m]['id']] = $locations;
            $j = 0;
            while (!($j >= count($locations))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :lid';
                $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':lid' => $locations[$j]['id']));
                $mybuilding[$locations[$j]['id']] = $buildings;
                $n = 0;
                while (!($n >= count($buildings))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                    $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$m]['id']));
                    $myunit[$buildings[$m]['id']] = $units;
                    $sql = 'select id,realname as title from ' . tablename('rhinfo_zyxq_team') . ' where  weid = :weid and rid = :rid';
                    $headpers = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
                    $myheadper[$regions[$m]['id']] = $headpers;
                    ($n += 1) + -1;
                }
                ($j += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('gpost');
} elseif ($operation == 'gdelete') {
    $current = '删除绿化记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_environ_grecord', array('id' => $id), 'AND');
    if ($result) {
        show_json(1, array('msg' => '删除成功'));
    } else {
        show_json(0 - 1, array('msg' => '删除失败'));
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id . $msg);
    header('Location:' . $this->createWeburl($mydo, array('op' => 'glist')) . $mywe['direct']);
    exit(0);
} elseif ($operation == 'gsearch') {
    $content = $_GPC['mycontent'];
    $tablename = 'rhinfo_zyxq_environ_grecord';
    $sql = 'select count(*) as num from ' . tablename($tablename) . ' where pname LIKE :pname';
    $list = pdo_fetch($sql, array(':pname' => '%' . $content . '%'));
    $sql = 'select id,pid,pname,rname,CONCAT(lname,\'-\',bname,\'-\',tname) as area,hname,vegeid,vegename,kind,num,phone,img,planter,startdate,simdate from ' . tablename($tablename) . ' where pname LIKE :pname' . $limit;
    $data = pdo_fetchall($sql, array(':pname' => '%' . $content . '%'));
    if (empty($data)) {
        $sql = 'select count(*) as num from ' . tablename($tablename) . ' where hname LIKE :hname';
        $list = pdo_fetch($sql, array(':hname' => '%' . $content . '%'));
        $sql = 'select id,pid,pname,rname,CONCAT(lname,\'-\',bname,\'-\',tname) as area,hname,vegeid,vegename,kind,num,phone,img,hname,startdate,simdate from ' . tablename($tablename) . ' where hname LIKE :hname' . $limit;
        $data = pdo_fetchall($sql, array(':hname' => '%' . $content . '%'));
    }
    foreach ($data as $k => $v) {
        $arr = explode(':', $data[$k]['img']);
        $data[$k]['img'] = $arr;
    }
    $total = $list['num'];
    $pager = pagination($total, $pindex, $psize);
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    include $this->mywtpl('glist');
}
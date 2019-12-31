<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'environ';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'record';
$mydo = 'environ';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($this->syscfg['stewarddisplay'] == 1 || empty($this->syscfg['stewarddisplay'])) {
        if ($res) {
            if ($res['arrearagelimit4']) {
                header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
                exit(0);
            }
        }
    } elseif ($res) {
        if ($res['arrearagelimit2'] || $res['arrearagelimit3']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
if ($operation == 'record') {
    $mcurr = 'record';
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_record';
        $uid = $_W['member']['uid'];
        $sql = 'select id,pid,rid,realname,mobile from ' . tablename('rhinfo_zyxq_team') . ' where uid = :uid';
        $users = pdo_fetchall($sql, array(':uid' => $uid));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $users[0]['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $users[0]['rid']));
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $list = array('pid' => $users[0]['pid'], 'pname' => $pname, 'rid' => $users[0]['rid'], 'rname' => $rname, 'lname' => $rname . ' ' . $_GPC['area'], 'cname' => $users[0]['realname'], 'cleantion' => $_GPC['cleantion'], 'areacont' => $_GPC['areacont'], 'phone' => $users[0]['mobile'], 'note' => $_GPC['note'], 'img' => $images, 'uid' => $uid, 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $imgArr = array();
        $arr = explode('"', $list['img']);
        $i = 0;
        while (!($i >= count($arr))) {
            if (strlen($arr[$i]) > 20) {
                $imgArr[$i] = $arr[$i];
                $list['img'] = $imgArr;
            }
            ($i += 1) + -1;
        }
        $str = implode(':', $list['img']);
        $list['img'] = $str;
        $result = pdo_insert($tablename, $list);
    }
} elseif ($operation == 'details') {
    $id = $_GPC['id'];
    $sign = $_GPC['sign'];
    if ($sign == 'cord') {
        $tablename = 'rhinfo_zyxq_environ_record';
        $sql = 'select id,pname,rname,CONCAT(lname,bname,tname) as area,cname,areacont,phone,startdate,simdate,cleantion,img,note from ' . tablename($tablename) . ' where id = :id ';
        $data = pdo_fetchall($sql, array(':id' => $id));
        foreach ($data as $k => $v) {
            $arr = explode(':', $data[$k]['img']);
            $data[$k]['img'] = $arr;
        }
        $data[0]['sign'] = $sign;
    } elseif ($sign == 'green') {
        $tablename = 'rhinfo_zyxq_environ_grecord';
        $sql = 'select id,pid,pname,rname,CONCAT(lname,bname,tname) as area,hname,vegeid,vegename,kind,num,phone,img,note,startdate,simdate from ' . tablename($tablename) . ' where id = :id ';
        $data = pdo_fetchall($sql, array(':id' => $id));
        foreach ($data as $k => $v) {
            $arr = explode(':', $data[$k]['img']);
            $data[$k]['img'] = $arr;
        }
        $data[0]['sign'] = $sign;
    }
    include $this->mymtpl('environ');
} elseif ($operation == 'grecord') {
    $mcurr = 'record';
    if ($_W['isajax']) {
        $tablename = 'rhinfo_zyxq_environ_grecord';
        $uid = $_W['member']['uid'];
        $sql = 'select id,pid,rid,realname,mobile from ' . tablename('rhinfo_zyxq_team') . ' where uid = :uid';
        $users = pdo_fetchall($sql, array(':uid' => $uid));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :pid';
        $pname = pdo_fetchcolumn($sql, array(':pid' => $users[0]['pid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :rid';
        $rname = pdo_fetchcolumn($sql, array(':rid' => $users[0]['rid']));
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $list = array('pid' => $users[0]['pid'], 'pname' => $pname, 'rid' => $users[0]['rid'], 'rname' => $rname, 'lname' => $rname . ' ' . $_GPC['area'], 'hname' => $users[0]['realname'], 'vegeid' => $_GPC['vegeid'], 'vegename' => $_GPC['vegename'], 'kind' => $_GPC['kind'], 'num' => $_GPC['num'], 'phone' => $users[0]['mobile'], 'note' => $_GPC['note'], 'img' => $images, 'uid' => $uid, 'startdate' => $_GPC['startdate'], 'simdate' => $_GPC['simdate']);
        $imgArr = array();
        $arr = explode('"', $list['img']);
        $i = 0;
        while (!($i >= count($arr))) {
            if (strlen($arr[$i]) > 20) {
                $imgArr[$i] = $arr[$i];
                $list['img'] = $imgArr;
            }
            ($i += 1) + -1;
        }
        $str = implode(':', $list['img']);
        $list['img'] = $str;
        $result = pdo_insert($tablename, $list);
    }
    include $this->createMobileUrl('manage', array('op' => 'environ', 'status' => 4));
} elseif ($operation == 'myenviron') {
    $tablename = 'rhinfo_zyxq_environ_record';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' weid=:weid and uid = :uid ';
        $params[':weid'] = $_W['uniacid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition . ' ORDER BY ctime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    if ($_GPC['status'] == 1) {
        $this->createMobileUrl('manage', array('op' => 'environ', 'status' => 1));
    }
    if ($_GPC['status'] == 2) {
        $this->createMobileUrl('manage', array('op' => 'environ', 'status' => 2));
    }
    if ($_GPC['status'] == 3) {
        $sql = 'select id,pname,rname,CONCAT(lname,bname,tname) as area,cname,areacont,phone,startdate,simdate,cleantion,img from ' . tablename($tablename) . ' where uid = :uid ' . ' ORDER BY `ID` ASC ';
        $data = pdo_fetchall($sql, array(':uid' => $_W['member']['uid']));
        $this->createMobileUrl('manage', array('op' => 'environ', 'status' => 3));
    }
    if ($_GPC['status'] == 4) {
        $tablename = 'rhinfo_zyxq_environ_grecord';
        $sql = 'select id,pid,pname,rname,CONCAT(lname,bname,tname) as area,hname,vegeid,vegename,kind,num,phone,img,startdate,simdate from ' . tablename($tablename) . ' where uid = :uid ' . ' ORDER BY `ID` ASC ';
        $data = pdo_fetchall($sql, array(':uid' => $_W['member']['uid']));
        $this->createMobileUrl('manage', array('op' => 'environ', 'status' => 4));
    }
    if (!isset($_GPC['status'])) {
        $sql = 'select id,pname,rname,CONCAT(lname,bname,tname) as area,cname,areacont,phone,startdate,simdate,cleantion,img from ' . tablename($tablename) . ' where uid = :uid ' . ' ORDER BY `ID` ASC ';
        $data = pdo_fetchall($sql, array(':uid' => $_W['member']['uid']));
        $this->createMobileUrl('manage', array('op' => 'environ', 'status' => false));
        include $this->mymtpl('environ');
        exit(0);
    }
    include $this->mymtpl('environ');
}
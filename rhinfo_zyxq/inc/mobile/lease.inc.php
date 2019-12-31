<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'lease';
$mydo = 'lease';
$myurl = $this->createMobileurl($mydo);
$config = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$myrid = empty($_GPC['rid']) ? $user['rid'] : $_GPC['rid'];
$region = pdo_fetch('select id,title from ' . tablename('rhinfo_zyxq_region') . ' where id=:rid and weid=:weid', array(':rid' => $myrid, ':weid' => $_W['uniacid']));
if ($operation == 'index') {
    if ($_W['isajax']) {
        $lessees = pdo_fetchall('select id as value,title as text from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and rid=:rid and status=1', array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid']));
        show_json(1, array('list' => $lessees));
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and (openid = :openid or uid=:uid)';
    $lessee = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if ($lessee['openid'] == $_W['openid'] || $lessee['uid'] == $_W['member']['uid']) {
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'feebill', 'rid' => $lessee['rid'], 'lesseeid' => $lessee['id'])));
        exit(0);
    } else {
        $regions = pdo_fetchall('select id as value,title as text from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid', array(':weid' => $_W['uniacid']));
        $lessees = pdo_fetchall('select id as value,title as text from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and rid=:rid and status=1', array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    }
    include $this->mymtpl('index');
} elseif ($operation == 'feebill') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and rid=:rid and id=:lesseeid';
    $lessee = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], 'lesseeid' => $_GPC['lesseeid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where weid=:weid and rid=:rid and lesseeid=:lesseeid and status=1';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], ':lesseeid' => $_GPC['lesseeid']));
    $sql = 'SELECT sum(fee - payfee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where weid = :weid and rid=:rid and lesseeid=:lesseeid and status=1';
    $totalfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], 'lesseeid' => $_GPC['lesseeid']));
    $tempbillid = array();
    $k = 0;
    while (!($k >= count($list))) {
        array_push($tempbillid, $list[$k]['id']);
        ($k += 1) + -1;
    }
    $billid = implode(',', $tempbillid);
    include $this->mymtpl('feebill');
} elseif ($operation == 'paybill') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_lessee') . ' where weid=:weid and rid=:rid and id=:lesseeid';
    $lessee = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], 'lesseeid' => $_GPC['lesseeid']));
    if ($lessee['openid'] == $_W['openid'] || $lessee['uid'] == $_W['member']['uid']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_leasebill') . ' where weid=:weid and rid=:rid and lesseeid=:lesseeid and status=2';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], 'lesseeid' => $_GPC['lesseeid']));
        $sql = 'SELECT sum(payfee) FROM ' . tablename('rhinfo_zyxq_leasebill') . ' where weid = :weid and rid=:rid and lesseeid=:lesseeid and status=2';
        $totalfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $_GPC['rid'], 'lesseeid' => $_GPC['lesseeid']));
    }
    include $this->mymtpl('hisfeebill');
}
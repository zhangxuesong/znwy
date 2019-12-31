<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'proprietor';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$mydo = 'proprietor';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$myrid = empty($_GPC['rid']) ? $user['rid'] : $_GPC['rid'];
$region = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_region') . ' where id=:rid and weid=:weid', array(':rid' => $myrid, ':weid' => $_W['uniacid']));
if ($operation == 'index') {
    if ($region['status'] == 1) {
        $this->mymsg('error', '温馨提示', '业委会申请中', '');
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'ask', 'rid' => $myrid)));
        exit(0);
    } elseif ($region['status'] == 2) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_pmember') . ' where weid=:weid and rid=:rid and status=1';
        $pmembers = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        $k = 0;
        while (!($k >= count($pmembers))) {
            $pmembers[$k]['openid'] = empty($pmembers[$k]['openid']) ? $pmembers[$k]['uid'] : $pmembers[$k]['openid'];
            $fans = mc_fansinfo($pmembers[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $pmembers[$k]['avatar'] = empty($pmembers[$k]['avatar']) ? $fans['avatar'] : $pmembers[$k]['avatar'];
            ($k += 1) + -1;
        }
    } else {
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'list', 'rid' => $myrid)));
        exit(0);
    }
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $lat = floatval($_GPC['lat']);
        $lng = floatval($_GPC['lng']);
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where category=1 and ' . $condition;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($lat != 0 && $lng != 0 && !empty($data[$k]['lat']) && !empty($data[$k]['lng'])) {
                $distance = GetDistance($lat, $lng, $data[$k]['lat'], $data[$k]['lng'], 2);
                $data[$k]['distance'] = $distance;
            } else {
                $data[$k]['distance'] = 100000;
            }
            $data[$k]['thumb'] = tomedia($data[$k]['thumb']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region_follow') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid) and deleted=0';
            $follow = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $data[$k]['id'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
            $data[$k]['follow'] = !empty($follow) ? 1 : 0;
            if ($data[$k]['status'] == 2) {
                $data[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'index', 'rid' => $data[$k]['id']));
            } else {
                $data[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'ask', 'rid' => $data[$k]['id']));
            }
            ($k += 1) + -1;
        }
        $data = multi_array_sort($data, 'distance');
        $start = ($pindex - 1) * $psize;
        if (!empty($data)) {
            $data = array_slice($data, $start, $psize);
        }
        $total = count($data);
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    include $this->mymtpl('list');
} elseif ($operation == 'member') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_pmember') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['id']));
    $item['avatar'] = empty($item['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $item['avatar'];
    include $this->mymtpl('member');
} elseif ($operation == 'ask') {
    $this->mymsg('error', '温馨提示', '还未创建业委会.', '');
    include $this->mymtpl('ask');
} elseif ($operation == 'follow') {
    $rid = $_GPC['rid'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region_follow') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid)';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if (empty($item)) {
        $data = array('weid' => $_W['uniacid'], 'pid' => 0, 'rid' => $rid, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'deleted' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_region_follow', $data);
    } else {
        pdo_update('rhinfo_zyxq_region_follow', array('deleted' => 0), array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'rid' => $rid));
    }
    show_json(1, '关注成功');
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'elevator';
$mydo = 'elevator';
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
if ($operation == 'index') {
    if ($_W['isajax']) {
        show_json(1, '操作成功');
    }
    $sql = 'select mailin_appid,mailin_secret,mailin_token,doorlock_type,thnmoo_token from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $user['rid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and rid=:rid and id=:bid';
    $building = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':bid' => $user['bid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_unit') . ' where weid=:weid and rid=:rid and id=:tid';
    $unit = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':tid' => $user['tid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and id=:hid';
    $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':hid' => $user['hid']));
    if (!empty($_GPC['elevatorid'])) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_elevator') . ' where weid=:weid and rid=:rid and id=:id';
        $elevator = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':id' => $_GPC['elevatorid']));
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_elevator') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid';
        $elevator = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':bid' => $user['bid'], ':tid' => $user['tid']));
    }
    if (empty($elevator)) {
        $this->mymsg('error', '温馨提示', '请绑定房产自动获取权限', 'close');
    }
    $floors = array();
    $m = 0;
    while (!($m >= $unit['floors'])) {
        $floors[] = $m + 1;
        ($m += 1) + -1;
    }
    if ($elevator['locktype'] == 6) {
        $set = array();
        $set['url'] = '/doormaster/getTempPwd';
        $set['token'] = $region['thinmoo_token'];
        $set['op'] = 'POST';
        $data = "{\n\t\t\t \"devType\":3,\n\t\t\t \"pwdKey\":[\"" . $item['locksn'] . "\"],\n\t\t\t \"doorNo\": 0,\n\t\t\t \"floor\": \"" . $room['floor'] . "\", \n\t\t\t \"endDate\": \"" . date('YmdHis', strtotime('+ 1 hours', TIMESTAMP)) . "\",\n\t\t\t \"pwdType\":2\n\t\t  }";
        $res = thinmoo_http_post($set, $data);
        if ($res['ret'] == '0') {
            $img = base64_decode($res['qrcodeImg']);
            $filepath = time() . random(3, 1);
            $fileurl = MODULE_URL . 'data/' . $filepath . '.jpg';
            $filepath = IA_ROOT . '/addons/rhinfo_zyxq/data/' . $filepath . '.jpg';
            file_put_contents($filepath, $img);
            include $this->mymtpl('myqrcode');
        } else {
            $this->mymsg('error', '温馨提示', '生成二维码失败，请联系门禁管理员.', '');
        }
    } elseif ($elevator['locktype'] == 5) {
        $set = array('mailin_appid' => $region['mailin_appid'], 'mailin_secret' => $region['mailin_secret'], 'mailin_token' => $region['mailin_token']);
        $post_data = array('m' => 'do', 'f' => 'smd', 'a' => 'get_smdqrc', 'device_sncode' => $item['locksn']);
        $result = mailin_http_post($set, $post_data);
        if ($res['state'] == 1) {
            $ret_data = $res['return_data'];
            $fileurl = $this->createqrcode($ret_data['owner_qrc']);
            $invalid_time = $ret_data['qrc_invalid_time'];
            include $this->mymtpl('myqrcode');
        } else {
            $this->mymsg('error', '温馨提示', '生成二维码失败，请联系门禁管理员.', '');
        }
    } elseif (!($elevator['locktype'] == 2)) {
    }
    include $this->mymtpl('index');
}
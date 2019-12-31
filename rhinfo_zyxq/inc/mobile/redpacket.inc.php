<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'redpacket';
$mydo = 'redpacket';
$user = $this->getmyinfo($_W['member']['uid']);
load()->model('mc');
if ($operation == 'index') {
    $id = $_GPC['redid'];
    $mid = $_GPC['redmid'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket') . ' where id=:id and weid=:weid';
    $redpacket = pdo_fetch($sql, array(':id' => $id, ':weid' => $_W['uniacid']));
    if ($_W['isajax']) {
        if ($_GPC['authkey'] == md5(base64_encode($_W['openid']))) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_member') . ' where id=:id and redid=:redid and weid=:weid and status=0';
            $redmember = pdo_fetch($sql, array(':id' => $mid, 'redid' => $id, ':weid' => $_W['uniacid']));
            if (empty($redmember)) {
                show_json(0, '您已经领取过');
            }
            $params = array('send_name' => empty($redpacket['from']) ? $_W['account']['name'] : $redpacket['from'], 'openid' => $redmember['openid'], 'total_amount' => $redmember['amount'], 'total_num' => 1, 'wishing' => empty($redpacket['wishing']) ? '恭喜发财' : $redpacket['wishing'], 'act_name' => $redpacket['title'], 'remark' => empty($redpacket['remark']) ? $redpacket['title'] : $redpacket['remark'], 'siteip' => $_SERVER['SERVER_ADDR']);
            load()->model('payment');
            $setting = uni_setting($_W['uniacid'], array('payment'));
            if (!is_array($setting['payment'])) {
                show_json(0, '支付参数错误');
            }
            $wechat = $setting['payment']['wechat'];
            $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
            $row = pdo_fetch($sql, array(':acid' => $wechat['account']));
            $mywechat['appid'] = $row['key'];
            $mywechat['mch_id'] = $wechat['mchid'];
            $mywechat['signkey'] = $wechat['apikey'];
            $mywechat['apiclient_cert'] = $this->syscfg['apiclient_cert'];
            $mywechat['apiclient_key'] = $this->syscfg['apiclient_key'];
            $mywechat['rootca'] = $this->syscfg['rootca'];
            if ($redpacket['sendtype'] == 1) {
                $res = my_wechat_payredpacket($params, $mywechat);
            } else {
                $res = my_wechat_paymoney($params, $mywechat);
            }
            if (empty($res)) {
                show_json(0, '操作失败，证书是否上传');
            }
            if (is_error($res)) {
                show_json(0, $res['message']);
            }
            pdo_update('rhinfo_zycj_redpacket_member', array('status' => 1), array('id' => $mid, 'weid' => $_W['uniacid']));
            show_json(1);
        } else {
            show_json(0, '验证失败，非法操作');
        }
    }
    $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
    include $this->mymtpl('index');
} elseif ($operation == 'get') {
    $id = $_GPC['redid'];
    $mid = $_GPC['redmid'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket') . ' where id=:id and weid=:weid';
    $redpacket = pdo_fetch($sql, array(':id' => $id, ':weid' => $_W['uniacid']));
    $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_member') . ' where id=:id and redid=:redid and weid=:weid and status=0';
    $redmember = pdo_fetch($sql, array(':id' => $mid, ':redid' => $id, ':weid' => $_W['uniacid']));
    $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_member') . ' where redid=:redid and weid=:weid order by ctime desc limit 10';
    $list = pdo_fetchall($sql, array(':redid' => $id, ':weid' => $_W['uniacid']));
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
        $myfans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
        $list[$k]['avatar'] = $myfans['avatar'];
        $list[$k]['nickname'] = $myfans['nickname'];
        ($k += 1) + -1;
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and (openid=:openid or uid=:uid) and category=1 and status=0 and deleted=0';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $redpacket['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid ';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $redpacket['rid']));
    }
    include $this->mymtpl('get');
} elseif ($operation == 'share') {
    $redid = $_GPC['redid'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket') . ' where id=:id and weid=:weid';
    $redpacket = pdo_fetch($sql, array(':id' => $redid, ':weid' => $_W['uniacid']));
    $data = array('weid' => $_W['uniacid'], 'rid' => $redpacket['rid'], 'redid' => $redid, 'from' => $_W['openid'], 'uid' => $_W['member']['uid'], 'status' => 0, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
    pdo_insert('rhinfo_zycj_redpacket_share', $data);
    $shareid = pdo_insertid();
    $qrcode_url = $this->createMobileurl($mydo, array('op' => 'getshare', 'shareid' => $shareid));
    $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
    $qrcode_url = $siteurl . substr($qrcode_url, 2);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid ';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $redpacket['rid']));
    $_share['title'] = $_W['fans']['nickname'] . '邀请您绑定注册';
    $_share['imgUrl'] = tomedia($region['thumb']);
    $_share['desc'] = '绑定注册送红包活动，先到先得.';
    $_share['link'] = $qrcode_url;
    include $this->mymtpl('share');
} elseif ($operation == 'getshare') {
    $shareid = $_GPC['shareid'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_share') . ' where id=:id and weid=:weid';
    $share = pdo_fetch($sql, array(':id' => $shareid, ':weid' => $_W['uniacid']));
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_redpacket_share') . ' where (to=:to or touid=:uid) and weid=:weid';
    $total = pdo_fetchall($sql, array(':to' => $_W['openid'], ':touid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
    if (($share['from'] !== $_W['openid'] || $share['fromuid'] !== $_W['member']['uid']) && $total == 0) {
        if ($share['status'] == 0) {
            pdo_update('rhinfo_zycj_redpacket_share', array('status' => 1, 'to' => $_W['openid']), array('id' => $shareid, 'weid' => $_W['uniacid']));
        } else {
            $data = array('weid' => $_W['uniacid'], 'rid' => $share['rid'], 'redid' => $share['redid'], 'from' => $share['openid'], 'status' => 1, 'to' => $_W['openid'], 'touid' => $_W['member']['uid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zycj_redpacket_share', $data);
        }
    }
    header('Location:' . $this->createMobileurl('member', array('op' => 'rbind', 'rid' => $share['rid'])));
    exit(0);
}
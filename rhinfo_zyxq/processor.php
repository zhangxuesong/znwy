<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once IA_ROOT . '/addons/rhinfo_zyxq/vendor/rhinfo/doorapi.php';
class Rhinfo_zyxqModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W;
        $content = $this->message['content'];
        $msgtype = $this->message['msgtype'];
        if ($msgtype == 'voice') {
            if (!$this->inContext) {
                $content = mb_substr($content, 0, 0 - 1, 'utf-8');
            }
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where weid = :weid';
        $syscfg = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rule = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE uniacid=:uniacid and module = :module and :content rlike content', array(':uniacid' => $_W['uniacid'], ':module' => 'rhinfo_zyxq', ':content' => $content));
        if (empty($rule)) {
            return $this->respText('未找到对应关键词呢，可咨询平台管理员.');
        }
        $replyrule = pdo_fetch('SELECT * FROM ' . tablename('rhinfo_zyxq_replyrule') . ' WHERE weid = :weid and replyid = :replyid ', array(':weid' => $_W['weid'], ':replyid' => $rule['rid']));
        $url = empty($syscfg['siteurl']) ? $_W['siteroot'] : $syscfg['siteurl'];
        if (!($replyrule['rid'] == 0)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $replyrule['rid']));
            if ($replyrule['qr'] == 1) {
                if ($syscfg['iswegroup'] == 1) {
                    if (empty($region['groupid'])) {
                        pdo_insert('mc_groups', array('uniacid' => $_W['uniacid'], 'title' => $region['title']));
                        $groupid = pdo_insertid();
                        pdo_update('rhinfo_zyxq_region', array('groupid' => $groupid), array('weid' => $_W['uniacid'], 'id' => $rid));
                    } else {
                        $groupid = $region['groupid'];
                    }
                    if (empty($_W['member']['uid'])) {
                        load()->model('mc');
                        $userinfo = mc_oauth_userinfo();
                        $mc = array();
                        $mc['uniacid'] = $_W['uniacid'];
                        $mc['nickname'] = $userinfo['nickname'];
                        $mc['avatar'] = $userinfo['headimgurl'];
                        $mc['gender'] = $userinfo['sex'];
                        $mc['nationality'] = $userinfo['country'];
                        $mc['resideprovince'] = $userinfo['province'];
                        $mc['residecity'] = $userinfo['city'];
                        $mc['groupid'] = $groupid;
                        $res = mc_update($_W['openid'], $mc);
                    } else {
                        $res = pdo_update('mc_members', array('groupid' => $groupid), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
                    }
                }
                $url .= 'app' . substr($this->createMobileUrl('home', array('op' => 'scanbind', 'rid' => $replyrule['rid'])), 1, strlen($this->createMobileUrl('home', array('op' => 'scanbind', 'rid' => $replyrule['rid']))));
            } else {
                $url .= 'app' . substr($this->createMobileUrl('home', array('op' => 'index', 'rid' => $replyrule['rid'])), 1, strlen($this->createMobileUrl('home', array('op' => 'index', 'rid' => $replyrule['rid']))));
            }
            if ($region['replyimage']) {
                $articles[] = array('title' => $region['title'], 'description' => $region['replydesc'] ? $region['replydesc'] : $region['title'], 'picurl' => tomedia($region['replyimage']), 'url' => $url);
            } else {
                $articles[] = array('title' => $region['title'], 'description' => $region['replydesc'] ? $region['replydesc'] : $region['title'], 'picurl' => MODULE_URL . 'static/mobile/images/reply.jpg', 'url' => $url);
            }
            return $this->respNews($articles);
        }
        if (!empty($replyrule['parkid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_parkinglot') . ' where weid = :weid and id=:parkid';
            $park = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':parkid' => $replyrule['parkid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $park['rid']));
            $url .= 'app' . substr($this->createMobileUrl('car', array('op' => 'pay', 'parkid' => $replyrule['parkid'])), 1, strlen($this->createMobileUrl('car', array('op' => 'pay', 'parkid' => $replyrule['parkid']))));
            if ($region['replyimage']) {
                $articles[] = array('title' => $park['title'], 'description' => '快速缴费、续费月卡', 'picurl' => tomedia($region['replyimage']), 'url' => $url);
            } else {
                $articles[] = array('title' => $park['title'], 'description' => '快速缴费、续费月卡', 'picurl' => MODULE_URL . 'static/mobile/images/reply.jpg', 'url' => $url);
            }
            return $this->respNews($articles);
        }
        $i = 1;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' WHERE weid = :weid and openid = :openid and isdefault=1 and deleted=0 limit 1';
        $user = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid']));
        $doors = array();
        $doors1 = array();
        $doors2 = array();
        $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where offline=0 and weid=:weid and pid = :pid and rid = :rid and status = 1 and lid=0 and bid=0 and tid=0';
        $doors1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and pid = :pid and rid = :rid and openid = :openid';
        $houses = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':openid' => $_W['openid']));
        $j = 0;
        while (!($j >= count($houses))) {
            $sql = 'select lid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid = :pid and rid = :rid and id=:bid';
            $lid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':bid' => $houses[$j]['bid']));
            $lid = empty($lid) ? 0 : $lid;
            if ($lid > 0) {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where offline=0 and weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$j]['bid'], ':tid' => 0));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where offline=0 and weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => 0, ':tid' => 0));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where offline=0 and weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$j]['bid'], ':tid' => $houses[$j]['tid']));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where offline=0 and weid=:weid and pid = :pid and rid = :rid and lid=:lid and status = 1 and bid=:bid and tid=:tid';
                $doors2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid'], ':lid' => $lid, ':bid' => $houses[$j]['bid'], ':tid' => $houses[$j]['tid']));
                if (!empty($doors2)) {
                    $doors = array_merge($doors, $doors2);
                }
            }
            ($j += 1) + -1;
        }
        $doors = array_merge($doors1, $doors);
        if (empty($doors)) {
            return $this->respText('主人,您还没有门禁权限呢.');
        }
        if (!(count($doors) > 1)) {
            $res = $this->opendoor($doors[0]['locksn'], $doors[0]['lockid']);
            $this->opendoorlog($door['id'], $res);
            if ($res['code'] == '0') {
                return $this->respText('主人,门已打开了.');
            }
            return $this->respText('主人,开门失败,请联系门禁管理员.');
        }
        if (!$this->inContext) {
            $k = intval(mb_substr($content, 0 - 1, 1, 'utf-8'));
            $reply = "主人,您要开哪扇门呢.\n请直接回复序号:\n";
            $m = 0;
            while (!($m >= count($doors))) {
                if ($i == $k) {
                    $res = $this->opendoor($doors[$m]['locksn'], $doors[$m]['lockid']);
                    $this->opendoorlog($doors[$m]['id'], $res);
                    $this->endContext();
                    if ($res['code'] == '0') {
                        return $this->respText('主人,门已打开了.');
                    }
                    return $this->respText('主人,开门失败,请联系门禁管理员.');
                }
                $reply = $reply . $i . '、' . $door['title'] . "\n";
                $i += 1;
                ($m += 1) + -1;
            }
        } else {
            $k = intval($content);
            $m = 0;
            while (!($m >= count($doors))) {
                if ($i == $k) {
                    $res = $this->opendoor($doors[$m]['locksn'], $doors[$m]['lockid']);
                    $this->opendoorlog($doors[$m]['id'], $res);
                    $this->endContext();
                    if ($res['code'] == '0') {
                        return $this->respText('主人,门已打开了.');
                    }
                    return $this->respText('主人,开门失败,请联系门禁管理员.');
                }
                $i += 1;
                ($m += 1) + -1;
            }
        }
        return $this->respText($reply);
        return !($m >= count($doors));
    }
    private function opendoor($locksn, $lockid)
    {
        global $_W;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where weid = :weid';
        $config = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $apikey = $config['apikey'];
        $apiid = $config['apiid'];
        if ($config['doortype'] == 2) {
            $post_data['apikey'] = $apikey;
            $post_data['deviceid'] = $locksn;
            $post_data['command'] = '01';
            $post_data['lockid'] = $lockid;
            $res = Park_httpPost(1, $post_data);
            return $res;
        }
        if ($config['doortype'] == 3) {
            $url = 'openlock.html?appid=' . $apiid . '&appsecret=' . $apikey;
            $rs = wmj_httpPost($url, $locksn);
            if ($rs['state'] == '1' && $rs['state_code'] == '1') {
                $res['code'] = '0';
                $res['msg'] = '开门成功!';
            } else {
                $res['code'] = '1';
                $res['msg'] = '开门失败!' . $rs['state_msg'];
            }
            return $res;
        }
        if (!($config['doortype'] == 4)) {
            if (!empty($config['lockurl'])) {
                $url = $config['lockurl'];
            } else {
                $url = 'lock.php';
            }
            $post_data['op'] = '1';
            $post_data['token'] = $apikey;
            $post_data['sn'] = $locksn;
            $res = Zr_httpPost($url, $post_data);
            return $res;
        }
        $url = 'AccessControl02/Api.php';
        $post_data = array('action' => 'sendIns', 'token' => $apikey, 'appid' => $apiid, 'pluse' => '0001', 'device_code' => $lockid);
        $rs = Mx_httpPost($url, $post_data);
        if ($rs[0] == 'SUCCESS') {
            if ($rs[1] == 'OK') {
                $res['code'] = '0';
                $res['msg'] = '开门成功!';
            } else {
                $res['code'] = '1';
                $res['msg'] = '开门失败!' . $rs[1];
            }
        } else {
            $res['code'] = '1';
            $res['msg'] = '开门失败!';
        }
        return $res;
    }
    private function opendoorlog($id, $res)
    {
        global $_W;
        $data = array('weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'did' => $id, 'status' => $res['code'], 'result' => $res['msg'], 'opentime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_doorlog', $data);
        return null;
    }
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$curr = 'auth';
$mydo = 'auth';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'login';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$_share = $this->rhinfo_share();
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
load()->model('mc');
myload()->model('member');
if ($operation == 'login') {
    if ($_W['ispost'] && $_W['isajax']) {
        $username = trim($_GPC['username']);
        $password = trim($_GPC['password']);
        $verifycode = trim($_GPC['verifycode']);
        if (empty($username)) {
            show_json(0, '用户名不能为空');
        }
        if (empty($password)) {
            if ($_GPC['isverify']) {
                @session_start();
                $key = '__rhinfo_zyxq_login_verifycodesession_' . $_W['uniacid'] . '_' . $username;
                if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
                    show_json(0, '验证码错误或已过期');
                }
            } else {
                show_json(0, '密码不能为空');
            }
        }
        $sql = 'SELECT `uid`,`salt`,`password` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if (preg_match(REGULAR_MOBILE, $username)) {
            $sql .= ' AND `mobile`=:mobile';
            $pars[':mobile'] = $username;
        } else {
            show_json(0, '请输入正确的手机号码');
        }
        $user = pdo_fetch($sql, $pars);
        if ($_GPC['isverify']) {
            $uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_GPC['__uniacid'];
            $session_key = '__rhinfo_' . $uniacid . '_session';
            $cookie = json_decode(base64_decode($_GPC[$session_key]), true);
        } else {
            $hash = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
            if ($user['password'] != $hash) {
                show_json(0, '密码错误');
            }
        }
        if (empty($user)) {
            show_json(0, '亲，您尚未注册');
        }
        if (_my_login($user['uid'])) {
            show_json(1, '登录成功');
        }
        show_json(0, '未知错误导致登录失败');
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    include $this->mymtpl('login');
} elseif ($operation == 'home') {
    $sql = 'select link,thumb,wxappid,wxapppage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=1 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $advs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,thumb,wxappid,wxapppage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=2 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,thumb,wxappid,wxapppage,0 as `pid` ,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=3 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $cubes = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $sql = 'select link,wxappid,wxapppage,title,thumb from ' . tablename('rhinfo_zyxq_nav') . ' where weid=:weid and category=1 and enabled = 1 order by displayorder desc limit 16';
    $navs = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $menu1 = array();
    $menu2 = array();
    $i = 0;
    if (count($navs) > 8) {
        $k = 0;
        while (!($k >= count($navs))) {
            if (!($i >= 8)) {
                $menu1[$i] = $navs[$k];
            } else {
                $menu2[$i - 8] = $navs[$k];
            }
            ($i += 1) + -1;
            ($k += 1) + -1;
        }
    }
    include $this->mymtpl();
} elseif ($operation == 'mobile_exist') {
    if ($_W['ispost'] && $_W['isajax']) {
        $is_exist = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'mobile' => trim($_GPC['mobile'])));
        if (!empty($is_exist)) {
            show_json(0, '手机号已存在');
        }
        show_json(1, '手机号码不存在');
    }
} elseif ($operation == 'register') {
    if ($_W['ispost'] && $_W['isajax']) {
        $sql = 'SELECT `uid` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $verifycode = trim($_GPC['verifycode']);
        $username = trim($_GPC['mobile']);
        $password = trim($_GPC['pwd']);
        @session_start();
        $key = '__rhinfo_zyxq_login_verifycodesession_' . $_W['uniacid'] . '_' . $username;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        if (preg_match(REGULAR_MOBILE, $username)) {
            $type = 'mobile';
            $sql .= ' AND `mobile`=:mobile';
            $pars[':mobile'] = $username;
        } else {
            show_json(0, '手机号码不正确');
        }
        $user = pdo_fetch($sql, $pars);
        if (!empty($user)) {
            show_json(0, '该手机号已被注册');
        }
        if (!empty($_W['openid'])) {
            $fan = mc_fansinfo($_W['openid'], $_W['acid'], $_W['uniacid']);
            if (!empty($fan)) {
                $map_fans = $fan['tag'];
            }
            if (empty($map_fans) && isset($_SESSION['userinfo'])) {
                $map_fans = unserialize(base64_decode($_SESSION['userinfo']));
            }
        }
        $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
        $data = array('uniacid' => $_W['uniacid'], 'salt' => random(8), 'groupid' => $default_groupid, 'createtime' => TIMESTAMP);
        $data['mobile'] = $username;
        if (!empty($password)) {
            $data['password'] = md5($password . $data['salt'] . $_W['config']['setting']['authkey']);
        }
        if (!empty($map_fans)) {
            $data['nickname'] = strip_emoji($map_fans['nickname']);
            $data['gender'] = $map_fans['sex'];
            $data['residecity'] = $map_fans['city'] ? $map_fans['city'] . '市' : '';
            $data['resideprovince'] = $map_fans['province'] ? $map_fans['province'] . '省' : '';
            $data['nationality'] = $map_fans['country'];
            $data['avatar'] = rtrim($map_fans['headimgurl'], '0') . 132;
        }
        pdo_insert('mc_members', $data);
        $uid = pdo_insertid();
        if (!empty($fan) && !empty($fan['fanid'])) {
            pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fan['fanid']));
        }
        if (_my_login($uid)) {
            show_json(1, '注册成功');
        }
        show_json(0, '未知错误导致注册失败');
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    include $this->mymtpl('register');
} elseif ($operation == 'forget') {
    if ($_W['ispost'] && $_W['isajax']) {
        $username = trim($_GPC['mobile']);
        $password = trim($_GPC['pwd']);
        $sql = 'SELECT `uid`,`salt` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $sql .= ' AND `mobile`=:mobile';
        $pars[':mobile'] = $username;
        $user = pdo_fetch($sql, $pars);
        if (empty($user)) {
            show_json(0, '用户不存在');
        } else {
            $password = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
            mc_update($user['uid'], array('password' => $password));
        }
        show_json(1, '找回成功');
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    include $this->mymtpl('register');
} elseif ($operation == 'verifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    $key = '__rhinfo_zyxq_login_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        show_json(0, '短信参数配置错误');
    }
    if ($ret['status']) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(1, '短信发送成功');
    }
    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败');
    show_json(0, $ret['message']);
} elseif ($operation == 'logout') {
    $_W['member'] = array();
    $_W['openid'] = 0;
    $_SESSION = array();
    isetcookie('__uniacid', '', 0 - 10000);
    $session_key = '__rhinfo_' . $_W['uniacid'] . '_session';
    isetcookie($session_key, '', 0 - 10000);
    isetcookie('__session', '', 0 - 10000);
    header('location: ' . $this->createMobileUrl($mydo));
    exit(0);
} elseif ($operation == 'modify') {
    if ($_W['ispost'] && $_W['isajax']) {
        $username = trim($_GPC['mobile']);
        $password = trim($_GPC['pwd']);
        $sql = 'SELECT `uid`,`salt` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $sql .= ' AND `uid`=:uid';
        $pars[':uid'] = $_W['member']['uid'];
        $user = pdo_fetch($sql, $pars);
        if (empty($user)) {
            show_json(0, '用户不存在');
        } else {
            $password = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
            mc_update($user['uid'], array('password' => $password));
        }
        $_W['member'] = array();
        $_W['openid'] = 0;
        $_SESSION = array();
        isetcookie('__uniacid', '', 0 - 10000);
        $session_key = '__rhinfo_' . $_W['uniacid'] . '_session';
        isetcookie($session_key, '', 0 - 10000);
        isetcookie('__session', '', 0 - 10000);
        show_json(1, '修改成功');
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    include $this->mymtpl('modify');
} elseif ($operation == 'bindverifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    $sql = 'SELECT `uid` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
    $pars = array();
    $pars[':uniacid'] = $_W['uniacid'];
    if (preg_match(REGULAR_MOBILE, $mobile)) {
        $type = 'mobile';
        $sql .= ' AND `mobile`=:mobile';
        $pars[':mobile'] = $mobile;
    } else {
        show_json(0, '手机号码不正确');
    }
    $user = pdo_fetch($sql, $pars);
    if (!empty($user)) {
        show_json(0, '该手机号已被注册');
    }
    $key = '__rhinfo_zyxq_bindmobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        show_json(0, '短信参数配置错误');
    }
    if ($ret['status']) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(1, '短信发送成功');
    }
    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败');
    show_json(0, $ret['message']);
} elseif ($operation == 'bind') {
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        if (empty($mobile)) {
            show_json(0, '请输入手机号');
        }
        $sql = 'SELECT * FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if (preg_match(REGULAR_MOBILE, $mobile)) {
            $type = 'mobile';
            $sql .= ' AND `mobile`=:mobile';
            $pars[':mobile'] = $mobile;
        } else {
            show_json(0, '手机号码不正确');
        }
        $user = pdo_fetch($sql, $pars);
        if (!empty($user)) {
            if ($_GPC['ishb'] == 1) {
                if ($user['credit1'] > 0) {
                    $res = mc_credit_update($_W['member']['uid'], 'credit1', $user['credit1'], array(0, '会员合并', 'rhinfo_zyxq'));
                    if ($res) {
                        $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                        $crediturl = $this->my_mobileurl($crediturl);
                        mc_notice_credit1($_W['openid'], $_W['member']['uid'], $user['credit1'], '会员合并', $crediturl, '谢谢支持，点击查看详情');
                    }
                }
                if ($user['credit2'] > 0) {
                    $res = mc_credit_update($_W['member']['uid'], 'credit2', $user['credit2'], array(0, '会员合并', 'rhinfo_zyxq'));
                    if ($res) {
                        $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                        $crediturl = $this->my_mobileurl($crediturl);
                        mc_notice_credit2($_W['openid'], $_W['member']['uid'], $user['credit2'], '会员合并', $crediturl, '谢谢支持，点击查看详情');
                    }
                }
                pdo_delete('mc_members', array('uid' => $user['uid']));
                show_json(1, '绑定成功');
            } else {
                show_json(2, '该手机号已被注册');
            }
        }
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_bindmobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $pwd = trim($_GPC['pwd']);
        $salt = random(8);
        load()->model('user');
        $password = user_hash($pwd, $salt);
        $user_data = array('mobile' => $mobile, 'password' => $password, 'salt' => $salt);
        $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
        $data['uniacid'] = $_W['uniacid'];
        $data['groupid'] = $default_groupid;
        if (!empty($_W['openid'])) {
            $data['email'] = md5($_W['openid']) . '@we7.cc';
        }
        $res = mc_update($_W['member']['uid'], $user_data);
        if ($res) {
            show_json(1, '绑定成功');
        } else {
            show_json(0, '绑定失败');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    include $this->mymtpl('bind');
} elseif ($operation == 'wxappbind') {
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        if (empty($mobile)) {
            show_json(0, '请输入手机号');
        }
        $sql = 'SELECT * FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if (preg_match(REGULAR_MOBILE, $mobile)) {
            $type = 'mobile';
            $sql .= ' AND `mobile`=:mobile';
            $pars[':mobile'] = $mobile;
        } else {
            show_json(0, '手机号码不正确');
        }
        $user = pdo_fetch($sql, $pars);
        if ($_GPC['ishb'] == 1) {
            if ($user['credit1'] > 0) {
                $res = mc_credit_update($_W['member']['uid'], 'credit1', $user['credit1'], array(0, '会员合并', 'rhinfo_zyxq'));
                if ($res) {
                    $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                    $crediturl = $this->my_mobileurl($crediturl);
                    mc_notice_credit1($_W['openid'], $_W['member']['uid'], $user['credit1'], '会员合并', $crediturl, '谢谢支持，点击查看详情');
                }
            }
            if ($user['credit2'] > 0) {
                $res = mc_credit_update($_W['member']['uid'], 'credit2', $user['credit2'], array(0, '会员合并', 'rhinfo_zyxq'));
                if ($res) {
                    $crediturl = $this->createMobileurl('service', array('op' => 'credit2'));
                    $crediturl = $this->my_mobileurl($crediturl);
                    mc_notice_credit2($_W['openid'], $_W['member']['uid'], $user['credit2'], '会员合并', $crediturl, '谢谢支持，点击查看详情');
                }
            }
            pdo_delete('mc_members', array('uid' => $user['uid']));
            show_json(1, '绑定成功');
        } elseif (!empty($user)) {
            show_json(2, '该手机号已被注册');
        }
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_bindmobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $pwd = trim($_GPC['pwd']);
        $salt = random(8);
        load()->model('user');
        $password = user_hash($pwd, $salt);
        $user_data = array('mobile' => $mobile, 'password' => $password, 'salt' => $salt);
        $res = pdo_update('mc_members', $user_data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
        if (!empty($res)) {
            show_json(1, '绑定成功');
        } else {
            show_json(0, '绑定失败');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    include $this->mymtpl('wxappbind');
} elseif ($operation == 'regproverifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    if (!preg_match(REGULAR_MOBILE, $mobile)) {
        show_json(0, '手机号码不正确');
    }
    if (empty($_W['member']['uid'])) {
        show_json(0, '操作异常');
    } else {
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_property') . ' where muid=:muid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':muid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
        if ($count > 0) {
            show_json(0, '您已经入驻过');
        }
    }
    $key = '__rhinfo_zyxq_regpromobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        show_json(0, '短信参数配置错误');
    }
    if ($ret['status']) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(1, '短信发送成功');
    }
    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败' . $ret['message']);
    show_json(0, $ret['message']);
} elseif ($operation == 'regproperty') {
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        if (empty($mobile)) {
            show_json(0, '请输入手机号');
        }
        $title = $_GPC['property'];
        $userno = $_GPC['userno'];
        $pwd = $_GPC['pwd'];
        if (empty($title)) {
            show_json(0, '物业公司名称不能为空');
        }
        if (empty($userno)) {
            show_json(0, '用户账户不能为空');
        }
        if (empty($pwd)) {
            show_json(0, '登录密码不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where title like \'%' . $title . '%\' and weid = :weid';
        $property = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        if (!empty($property)) {
            show_json(0, '物业公司名称已存在');
        }
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_regpromobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where uid = 0 and userno = :userno and weid = :weid and status=1';
        $user = pdo_fetch($sql, array(':userno' => $userno, ':weid' => $_W['uniacid']));
        if (!empty($user)) {
            show_json(0, '用户代号已存在!');
        }
        $password = md5($pwd);
        $data = array('weid' => $_W['uniacid'], 'title' => $title, 'userno' => $userno, 'username' => $title, 'limitqty' => 1, 'startdate' => TIMESTAMP, 'enddate' => strtotime('+1 months'), 'telphone' => $mobile, 'mobile' => $mobile, 'status' => 1, 'muid' => $_W['member']['uid'], 'cuid' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_property', $data);
        $id = pdo_insertid();
        $this->mysyslog(0, 'property', $operation, $current, $current . 'id=' . $id);
        $user = array('weid' => $_W['uniacid'], 'pid' => $id, 'userno' => $userno, 'username' => $title, 'password' => $password, 'status' => 1, 'cuid' => 0, 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_secuser', $user);
        $userid = pdo_insertid();
        $this->mysyslog(0, 'property', $operation, '物业注册 ', '用户id=' . $userno);
        if (!empty($res)) {
            $pcurl = !empty($this->syscfg['binddomain']) ? $this->syscfg['binddomain'] . '/property.php' : $_W['siteroot'] . 'property.php?i=' . $_W['uniacid'];
            show_json(1, '注册成功，请至PC端访问' . $pcurl);
        } else {
            show_json(0, '注册失败');
        }
    }
    if (empty($_W['member']['uid'])) {
        header('Location:' . $this->createMobileurl('auth', array('op' => 'bind')));
        exit(0);
    } else {
        if ($this->syscfg['isreg'] == 0) {
            $this->mymsg('error', '温馨提示', '本平台未开放物业注册，请直接联系平台.', 'close');
            exit(0);
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_property') . ' where muid=:muid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':muid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
        if ($count > 0) {
            header('Location:' . $this->createMobileurl('manage', array('op' => 'index')));
            exit(0);
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    include $this->mymtpl('regproperty');
} elseif ($operation == 'regbusiness') {
    $current = '商家注册';
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        if (empty($mobile)) {
            show_json(0, '请输入手机号');
        }
        if (empty($_GPC['title'])) {
            show_json(0, '商家名称不能为空');
        }
        if (empty($_GPC['city'])) {
            show_json(0, '所在城市不能为空');
        }
        if (empty($_GPC['address'])) {
            show_json(0, '详细地址不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_business') . ' where title like \'%' . $_GPC['title'] . '%\' and weid = :weid';
        $business = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        if (!empty($business)) {
            show_json(0, '商家名称已存在');
        }
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_regbusimobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $data = array('weid' => $_W['uniacid'], 'title' => $_GPC['title'], 'telphone' => $mobile, 'contact' => !empty($_W['member']['realname']) ? $_W['member']['realname'] : $_W['member']['nickname'], 'mobile' => $mobile, 'province' => $_GPC['province'], 'city' => $_GPC['city'], 'district' => $_GPC['district'], 'address' => $_GPC['address'], 'status' => 2, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'cuid' => 0, 'ctime' => TIMESTAMP);
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $_GPC['city'] . $_GPC['address'] . '&key=' . $sysconifg['qq_lbskey'];
        $res = file_get_contents($url);
        $json = json_decode($res, 1);
        $status = $json['status'];
        if ($status == 0) {
            $result = $json['result']['location'];
            $data['lat'] = $result['lat'];
            $data['lng'] = $result['lng'];
        }
        $res = pdo_insert('rhinfo_zycj_business', $data);
        $id = pdo_insertid();
        $this->mysyslog(0, 'business', $operation, $current, $current . 'id=' . $id);
        if (!empty($res)) {
            show_json(1, '注册成功');
        } else {
            show_json(0, '注册失败');
        }
    }
    if (empty($_W['member']['uid'])) {
        header('Location:' . $this->createMobileurl('auth', array('op' => 'bind')));
        exit(0);
    } else {
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business') . ' where uid=:uid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':uid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
        if ($count > 0) {
            header('Location:' . $this->createMobileurl('business', array('op' => 'mindex')));
            exit(0);
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    include $this->mymtpl('regbusiness');
} elseif ($operation == 'regbusiverifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    if (!preg_match(REGULAR_MOBILE, $mobile)) {
        show_json(0, '手机号码不正确');
    }
    if (empty($_W['member']['uid'])) {
        show_json(0, '操作异常');
    } else {
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business') . ' where muid=:muid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':muid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
        if ($count > 0) {
            show_json(0, '您已经入驻过');
        }
    }
    $key = '__rhinfo_zyxq_regbusimobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        show_json(0, '短信参数配置错误');
    }
    if ($ret['status']) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(1, '短信发送成功');
    }
    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败' . $ret['message']);
    show_json(0, $ret['message']);
} elseif ($operation == 'regregion') {
    $current = '小区注册';
    if ($_W['isajax']) {
        $mobile = trim($_GPC['mobile']);
        if (empty($mobile)) {
            show_json(0, '请输入手机号');
        }
        $title = $_GPC['title'];
        if (empty($title)) {
            show_json(0, '小区名称不能为空');
        }
        if (empty($_GPC['city'])) {
            show_json(0, '所在城市不能为空');
        }
        if (empty($_GPC['address'])) {
            show_json(0, '详细地址不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where title like \'%' . $title . '%\' and weid = :weid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        if (!empty($region)) {
            show_json(0, '小区名称已存在');
        }
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_regionmobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            show_json(0, '验证码错误或已过期');
        }
        $data = array('weid' => $_W['uniacid'], 'pid' => 0, 'title' => $title, 'telphone' => $mobile, 'contact' => !empty($_W['member']['realname']) ? $_W['member']['realname'] : $_W['member']['nickname'], 'mobile' => $mobile, 'province' => $_GPC['province'], 'city' => $_GPC['city'], 'district' => $_GPC['district'], 'address' => $_GPC['address'], 'category' => 1, 'register' => 2, 'muid' => $_W['member']['uid'], 'cuid' => 0, 'ctime' => TIMESTAMP);
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $_GPC['city'] . $_GPC['address'] . '&key=' . $sysconifg['qq_lbskey'];
        $res = file_get_contents($url);
        $json = json_decode($res, 1);
        $status = $json['status'];
        if ($status == 0) {
            $result = $json['result']['location'];
            $data['lat'] = $result['lat'];
            $data['lng'] = $result['lng'];
        }
        $res = pdo_insert('rhinfo_zyxq_region', $data);
        $id = pdo_insertid();
        $this->mysyslog(0, 'region', $operation, $current, $current . 'id=' . $id);
        if (!empty($res)) {
            show_json(1, array('message' => '注册成功', 'rid' => $id));
        } else {
            show_json(0, '注册失败');
        }
    }
    if (empty($_W['member']['uid'])) {
        header('Location:' . $this->createMobileurl('auth', array('op' => 'bind')));
        exit(0);
    } else {
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where muid=:muid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':muid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
        if ($count > 0) {
            show_json(1, '您已经创建了一个小区');
        }
    }
    $sendtime = $_SESSION['verifycodesendtime'];
    if (empty($sendtime) || !($sendtime + 60 >= time())) {
        $endtime = 0;
    } else {
        $endtime = 60 - time() - $sendtime;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
    include $this->mymtpl('regregion');
} elseif ($operation == 'regionverifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        show_json(0, '请输入手机号');
    }
    if (!preg_match(REGULAR_MOBILE, $mobile)) {
        show_json(0, '手机号码不正确');
    }
    if (empty($_W['member']['uid'])) {
        show_json(0, '操作异常');
    } else {
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_region') . ' where muid=:muid and weid = :weid';
        $count = pdo_fetchcolumn($sql, array(':muid' => $_W['member']['uid'], ':weid' => $_W['uniacid']));
        if ($count > 0) {
            show_json(0, '您已经创建了一个小区');
        }
    }
    $key = '__rhinfo_zyxq_regionmobile_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        show_json(0, '短信参数配置错误');
    }
    if ($ret['status']) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        show_json(1, '短信发送成功');
    }
    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败' . $ret['message']);
    show_json(0, $ret['message']);
}
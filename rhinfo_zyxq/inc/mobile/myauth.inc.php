<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$curr = 'myauth';
$mydo = 'myauth';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'login';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$_share = $this->rhinfo_share();
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$myurl = $this->createMobileurl($mydo);
load()->model('mc');
myload()->model('member');
$sysset = pdo_get('rhinfo_zyxq_sysset', array('weid' => $_W['uniacid']));
$this->syscfg = $sysset;
$this->syscfg['siteurl'] = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
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
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $this->rhinfo_isalipay = true;
            $this->rhinfo_wap = true;
        }
        if ($this->rhinfo_isalipay) {
            if (empty($_W['openid_alipay'])) {
                $session_key = '__rhinfo_' . $_W['uniacid'] . '_session';
                $cookie = json_decode(base64_decode($_GPC[$session_key]), true);
                $_W['openid_alipay'] = $cookie['__rhinfo_alipay_openid'];
            }
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
}
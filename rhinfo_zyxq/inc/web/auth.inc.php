<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'login';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '用户登录';
$mydo = 'auth';
$tablename = 'rhinfo_zyxq_property';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
if ($operation == 'login') {
    if ($_W['isajax']) {
        $userno = $_GPC['userno'];
        $password = $_GPC['password'];
        $verify = trim($_GPC['verify']);
        $result = checkcaptcha($verify);
        if (empty($result)) {
            show_json(0, '验证码不正确或已过期，点击图片更换验证码!');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where uid = 0 and userno = :userno and status=1';
        $user = pdo_fetch($sql, array(':userno' => $userno));
        if (empty($user)) {
            show_json(0, '用户代号不存在!');
        } else {
            $password = md5($password);
            if (!($password == $user['password'])) {
                show_json(0, '用户密码不正确!');
            }
        }
        $ret = 1;
        if ($user['weid'] != $mywe['weid']) {
            $mywe['weid'] = $user['weid'];
            $_W['uniacid'] = $user['weid'];
            $ret = 2;
        }
        isetcookie('__uniacid', $user['weid'], 7 * 86400);
        $cookie['__property_uniacid'] = $user['weid'];
        $cookie['__property_time'] = time();
        $cookie['__property_uid'] = $user['id'];
        $session = base64_encode(json_encode($cookie));
        $session_key = '__property_' . $cookie['__property_uniacid'] . '_session';
        isetcookie($session_key, $session, 0, true);
        isetcookie('__session', '', 0 - 10000);
        $data = array('lastvisit' => TIMESTAMP, 'lastip' => $_W['clientip']);
        $glue = 'AND';
        pdo_update('rhinfo_zyxq_secuser', $data, array('id' => $user['id'], 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($user['id'], $mydo, $operation, '物业登录 ', '用户' . $userno);
        show_json($ret, array('myweid' => $user['weid'], 'weid' => $mywe['weid'], 'msg' => '公众号' . $mywe['weid'] . '与用户所属公众号' . $user['weid'] . '不匹配，将自动切换'));
    }
    $uniacid = intval($_GPC['i']);
    $uniacid = !empty($uniacid) ? $uniacid : $_W['uniacid'];
    $cookie_weid = $_GPC['__uniacid'];
    if (!empty($_GPC['user_code']) && $this->syspub['ishaina'] == 1) {
        myload()->classs('haina');
        $this->syspub;
        $haina = 'HaiNa';
        $res = $haina->getUser(array('user_code' => $_GPC['user_code']));
        if (!is_error($res)) {
            if ($res['retcode'] == 0) {
                $resData = $res['data'];
                $property = pdo_get('rhinfo_zyxq_property', array('haina_property_id' => $resData['property_id']), array('weid', 'id', 'title', 'userno'));
                if (!empty($property)) {
                    $user = pdo_get('rhinfo_zyxq_secuser', array('weid' => $property['weid'], 'haina_uid' => $resData['user_id']));
                    if (empty($user)) {
                        $user_data = array('weid' => $property['weid'], 'pid' => $property['id'], 'username' => $resData['nick_name'], 'password' => md5(TIMESTAMP), 'status' => 1, 'uid' => 0, 'haina_uid' => $resData['user_id'], 'cuid' => 0, 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_secuser', $user_data);
                        $user_id = pdo_insertid();
                        pdo_update('rhinfo_zyxq_secuser', array('userno' => 'haina_' . sprintf('%04d', $user_id)), array('weid' => $property['weid'], 'id' => $user_id));
                    } else {
                        $user_id = $user['id'];
                    }
                    isetcookie('__uniacid', $property['weid'], 7 * 86400);
                    $cookie['__property_uniacid'] = $property['weid'];
                    $cookie['__property_time'] = time();
                    $cookie['__property_uid'] = $user_id;
                    $cookie['__property_haina'] = 1;
                    $session = base64_encode(json_encode($cookie));
                    $session_key = '__property_' . $cookie['__property_uniacid'] . '_session';
                    isetcookie($session_key, $session, 0, true);
                    isetcookie('__session', '', 0 - 10000);
                    $_W['uniacid'] = $property['weid'];
                    $_W['ishaina'] = 1;
                } else {
                    $params = array('property_id' => $resData['property_id']);
                    $resp = $haina->getProperty($params);
                    if (is_error($resp)) {
                        exit($resp['message']);
                    }
                    $respData = $resp['data'];
                    $account = pdo_get('account_wechats', array('key' => $respData['wx_app_id']));
                    if (empty($account)) {
                        $name = $respData['nick_name'];
                        $description = $respData['nick_name'];
                        $data = array('name' => $name, 'description' => $description, 'title_initial' => get_first_pinyin($name), 'groupid' => 0);
                        $insert = pdo_insert('uni_account', $data);
                        if ($insert) {
                            $uniacid = pdo_insertid();
                            $template = pdo_fetch('SELECT id,title FROM ' . tablename('site_templates') . ' WHERE name = \'default\'');
                            $styles['uniacid'] = $uniacid;
                            $styles['templateid'] = $template['id'];
                            $styles['name'] = $template['title'] . '_' . random(4);
                            pdo_insert('site_styles', $styles);
                            $styleid = pdo_insertid();
                            $multi['uniacid'] = $uniacid;
                            $multi['title'] = $data['name'];
                            $multi['styleid'] = $styleid;
                            pdo_insert('site_multi', $multi);
                            $multi_id = pdo_insertid();
                            $unisettings['creditnames'] = array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1));
                            $unisettings['creditnames'] = iserializer($unisettings['creditnames']);
                            $unisettings['creditbehaviors'] = array('activity' => 'credit1', 'currency' => 'credit2');
                            $unisettings['creditbehaviors'] = iserializer($unisettings['creditbehaviors']);
                            $unisettings['uniacid'] = $uniacid;
                            $unisettings['default_site'] = $multi_id;
                            $unisettings['sync'] = iserializer(array('switch' => 0, 'acid' => ''));
                            pdo_insert('uni_settings', $unisettings);
                            pdo_insert('mc_groups', array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));
                            $fields = pdo_getall('profile_fields');
                            foreach ($fields as $field) {
                                $data = array('uniacid' => $uniacid, 'fieldid' => $field['id'], 'title' => $field['title'], 'available' => $field['available'], 'displayorder' => $field['displayorder']);
                                pdo_insert('mc_member_fields', $data);
                            }
                            $accountdata = array('uniacid' => $uniacid, 'type' => 1, 'hash' => random(8), 'isconnect' => 1);
                            pdo_insert('account', $accountdata);
                            $acid = pdo_insertid();
                            $account = array();
                            $account['acid'] = $acid;
                            $account['token'] = random(32);
                            $account['encodingaeskey'] = random(43);
                            $account['uniacid'] = $uniacid;
                            $account['name'] = $name;
                            $account['account'] = $respData['alias'];
                            $account['original'] = $respData['alias'];
                            $account['level'] = 1;
                            $account['key'] = $respData['wx_app_id'];
                            pdo_insert('account_wechats', $account);
                            if (!is_error($acid)) {
                                pdo_update('uni_account', array('default_acid' => $acid), array('uniacid' => $uniacid));
                            }
                            $module = array();
                            $module['uniacid'] = $uniacid;
                            $module['module'] = 'rhinfo_zyxq';
                            $module['enabled'] = 1;
                            $module['shotcut'] = 1;
                            $module['displayorder'] = 1;
                            pdo_insert('uni_account_modules', $module);
                        }
                    } else {
                        $uniacid = $account['uniacid'];
                    }
                    $property = pdo_get('rhinfo_zyxq_property', array('haina_property_id' => $resData['property_id']), array('id', 'title'));
                    if (empty($property)) {
                        $res = pdo_insert('rhinfo_zyxq_property', array('weid' => $uniacid, 'title' => $respData['name'], 'haina_property_id' => $resData['property_id']));
                        if ($res) {
                            $property_id = pdo_insertid();
                            $userno = 'admin_' . sprintf('%04d', $property_id);
                            $username = $respData['name'];
                            pdo_update('rhinfo_zyxq_property', array('userno' => $userno, 'username' => $username), array('weid' => $uniacid, 'id' => $property_id));
                            $user = array('weid' => $uniacid, 'pid' => $property_id, 'userno' => $userno, 'username' => $username, 'password' => md5(TIMESTAMP), 'status' => 1, 'uid' => 0, 'cuid' => 0, 'haina_uid' => $resData['user_id'], 'ctime' => TIMESTAMP);
                            pdo_insert('rhinfo_zyxq_secuser', $user);
                            $user_id = pdo_insertid();
                        }
                    }
                    isetcookie('__uniacid', $uniacid, 7 * 86400);
                    $cookie['__property_uniacid'] = $uniacid;
                    $cookie['__property_time'] = time();
                    $cookie['__property_uid'] = $user_id;
                    $cookie['__property_haina'] = 1;
                    $session = base64_encode(json_encode($cookie));
                    $session_key = '__property_' . $cookie['__property_uniacid'] . '_session';
                    isetcookie($session_key, $session, 0, true);
                    isetcookie('__session', '', 0 - 10000);
                    $_W['uniacid'] = $uniacid;
                    $_W['ishaina'] = 1;
                }
                header('Location:' . mywurl('property/index', array('ishaina' => 1, 'direct' => 1), $this->syscfg['isproperty']));
                exit(0);
            }
        }
        exit($res['message']);
    }
    if (empty($uniacid) && empty($cookie_weid)) {
        $domain = $_SERVER['HTTP_HOST'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where binddomain=:binddomain order by id';
        $sysset = pdo_fetch($sql, array(':binddomain' => $domain));
        if (!empty($sysset['weid'])) {
            $uniacid = $sysset['weid'];
        } else {
            $company = pdo_get('rhinfo_zyxq_syspub');
            $sysset['ptitle'] = $company['ptitle'];
            $logo = $company['plogo'];
            if (!empty($logo)) {
                $logo = tomedia($logo);
            }
            $copyright = $company['copyright'];
            if (!empty($copyright)) {
                $copyright = html_entity_decode($copyright);
            }
            include $this->mywtpl('login');
            exit(0);
        }
    }
    if (!empty($uniacid)) {
        isetcookie('__uniacid', $uniacid, 7 * 86400);
    } else {
        $uniacid = $cookie_weid;
    }
    $session_key = '__property_' . $uniacid . '_session';
    $cookie = json_decode(base64_decode($_GPC[$session_key]), true);
    if (!empty($cookie['__property_uid'])) {
        header('Location:' . mywurl('property/index', array('direct' => 1), $this->syscfg['isproperty']));
        exit(0);
    }
    if (!empty($uniacid)) {
        $_W['uniacid'] = $uniacid;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where ' . $condition;
    $sysset = pdo_fetch($sql, array(':weid' => $uniacid));
    $sysset['plogo'] = empty($sysset['plogo']) ? $this->syspub['plogo'] : $sysset['plogo'];
    $sysset['ptitle'] = empty($sysset['ptitle']) ? $this->syspub['ptitle'] : $sysset['ptitle'];
    $sysset['pbackground'] = empty($sysset['pbackground']) ? $this->syspub['pbackground'] : $sysset['pbackground'];
    $sysset['copyright'] = empty($sysset['copyright']) ? $this->syspub['copyright'] : $sysset['copyright'];
    $logo = $sysset['plogo'];
    if (!empty($logo)) {
        $logo = tomedia($logo);
    }
    $copyright = $sysset['copyright'];
    if (!empty($copyright)) {
        $copyright = html_entity_decode($copyright);
    }
    include $this->mywtpl('login');
} elseif ($operation == 'logout') {
    $uniacid = $mywe['weid'];
    if (!empty($uniacid)) {
        $cookie['__property_uniacid'] = $uniacid;
        $cookie['__property_time'] = time();
        $cookie['__property_uid'] = 0;
        $session = base64_encode(json_encode($cookie));
        $session_key = '__property_' . $cookie['__property_uniacid'] . '_session';
        isetcookie($session_key, $session, 0, true);
    }
    header('Location:' . mywurl('auth/login', array('direct' => 1), $this->syscfg['isproperty']));
    exit(0);
} elseif ($operation == 'password') {
    if ($_W['isajax']) {
        $id = trim($_GPC['id']);
        $oldpwd = trim($_GPC['oldpwd']);
        $newpwd = trim($_GPC['newpwd']);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where  weid = :weid and id = :id';
        $user = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
        if (!empty($user)) {
            $password = md5($oldpwd);
            if (!($password == $user['password'])) {
                echo '用户旧密码不正确!';
                exit(0);
            }
        } else {
            echo '用户代号不正确!';
            exit(0);
        }
        $data = array('password' => md5($newpwd));
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_secuser', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if ($result) {
            $cookie['__property_uniacid'] = $mywe['weid'];
            $cookie['__property_time'] = time();
            $cookie['__property_uid'] = 0;
            $session = base64_encode(json_encode($cookie));
            $session_key = '__property_' . $mywe['weid'] . '_session';
            isetcookie($session_key, $session, 0, true);
            echo 'ok';
        } else {
            echo '更新失败';
        }
        exit(0);
    }
    include $this->mywtpl('pwd');
} elseif ($operation == 'reg') {
    if ($_W['isajax']) {
        $title = $_GPC['title'];
        $userno = $_GPC['userno'];
        $password = $_GPC['password'];
        $mobile = $_GPC['mobile'];
        if (empty($title)) {
            exit('物业公司名称不能为空!');
        }
        if (empty($userno)) {
            exit('用户账户不能为空!');
        }
        if (empty($password)) {
            exit('登录密码不能为空!');
        }
        if (empty($mobile)) {
            exit('手机号码不能为空!');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where title like \'%' . $title . '%\'';
        $property = pdo_fetch($sql);
        if (!empty($property)) {
            exit('物业公司名称已存在!');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where ' . $condition;
        $sysset = pdo_fetch($sql, array(':weid' => $mywe['weid']));
        if (empty($sysset)) {
            exit('公众号不存在!');
        }
        $verifycode = trim($_GPC['verifycode']);
        @session_start();
        $key = '__rhinfo_zyxq_regpromobile_verifycodesession_' . $mywe['weid'] . '_' . $mobile;
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $verifycode || !isset($_SESSION['verifycodesendtime']) || !($_SESSION['verifycodesendtime'] + 600 >= time())) {
            exit('验证码错误或已过期');
        }
        $verify = trim($_GPC['verify']);
        $result = checkcaptcha($verify);
        if (empty($result)) {
            exit('验证码不正确或已过期，点击图片更换验证码!');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where uid = 0 and userno = :userno and status=1';
        $user = pdo_fetch($sql, array(':userno' => $userno));
        if (!empty($user)) {
            exit('用户代号已存在!');
        }
        $password = md5($password);
        $data = array('weid' => $mywe['weid'], 'title' => $title, 'userno' => $userno, 'username' => $title, 'limitqty' => 1, 'startdate' => TIMESTAMP, 'enddate' => strtotime('+1 months'), 'telphone' => $mobile, 'mobile' => $mobile, 'status' => 1, 'cuid' => 0, 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($id, $mydo, $operation, $current, $current . 'id=' . $id);
        $user = array('weid' => $mywe['weid'], 'pid' => $id, 'userno' => $userno, 'username' => $title, 'password' => $password, 'status' => 1, 'cuid' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_secuser', $user);
        $userid = pdo_insertid();
        $this->mysyslog($userid, $mydo, $operation, '物业注册 ', '用户' . $userno);
        exit('ok');
    }
    $uniacid = intval($_GPC['i']);
    $uniacid = !empty($uniacid) ? $uniacid : $_W['uniacid'];
    $cookie_weid = $_GPC['__uniacid'];
    if (empty($uniacid) && empty($cookie_weid)) {
        $domain = $_SERVER['HTTP_HOST'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where binddomain=:binddomain order by id';
        $sysset = pdo_fetch($sql, array(':binddomain' => $domain));
        if (!empty($sysset['weid'])) {
            $uniacid = $sysset['weid'];
        } else {
            include $this->mywtpl('login');
            exit(0);
        }
    }
    if (!empty($uniacid)) {
        isetcookie('__uniacid', $uniacid, 7 * 86400);
    } else {
        $uniacid = $cookie_weid;
    }
    if (!empty($uniacid)) {
        $_W['uniacid'] = $uniacid;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysset') . ' where ' . $condition;
    $sysset = pdo_fetch($sql, array(':weid' => $uniacid));
    $sysset['plogo'] = empty($sysset['plogo']) ? $this->syspub['plogo'] : $sysset['plogo'];
    $sysset['ptitle'] = empty($sysset['ptitle']) ? $this->syspub['ptitle'] : $sysset['ptitle'];
    $sysset['pbackground'] = empty($sysset['pbackground']) ? $this->syspub['pbackground'] : $sysset['pbackground'];
    $sysset['copyright'] = empty($sysset['copyright']) ? $this->syspub['copyright'] : $sysset['copyright'];
    $logo = $sysset['plogo'];
    if (!empty($logo)) {
        $logo = tomedia($logo);
    }
    $copyright = $sysset['copyright'];
    if (!empty($copyright)) {
        $copyright = html_entity_decode($copyright);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sysagreement') . ' where weid=:weid';
    $agreement = pdo_fetch($sql, array(':weid' => $uniacid));
    include $this->mywtpl('reg');
} elseif ($operation == 'verifycode') {
    $mobile = trim($_GPC['mobile']);
    if (empty($mobile)) {
        echo '请输入手机号';
        exit(0);
    }
    if (!preg_match(REGULAR_MOBILE, $mobile)) {
        echo '手机号码不正确';
        exit(0);
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_property') . ' where mobile=:mobile and weid = :weid';
    $count = pdo_fetchcolumn($sql, array(':mobile' => $mobile, ':weid' => $mywe['weid']));
    if ($count > 0) {
        echo '您已经入驻过';
        exit(0);
    }
    $key = '__rhinfo_zyxq_regpromobile_verifycodesession_' . $mywe['weid'] . '_' . $mobile;
    @session_start();
    $code = random(5, true);
    if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
        $ret = $this->send_sms($this->syscfg['smstype'], $mobile, $this->syscfg['verifyid'], array('code' => $code));
    } else {
        echo '短信参数配置错误';
        exit(0);
    }
    if ($ret['status'] == 1) {
        $_SESSION[$key] = $code;
        $_SESSION['verifycodesendtime'] = time();
        echo 'ok';
        exit(0);
    }
    $this->mysyslog(0, 'error', $mydo . ':' . $operation, '短信发送' . $mobile, '发送验证码失败' . $ret['message']);
    echo $ret['message'];
    exit(0);
}
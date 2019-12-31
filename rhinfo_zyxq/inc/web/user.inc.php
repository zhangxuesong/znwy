<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '用户管理';
$mydo = 'user';
$tablename = 'rhinfo_zyxq_secuser';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$rights = $this->myrights(1, $mydo, 'list');
if ($operation == 'list') {
    $current = '用户列表';
    $myret = 0;
    if (!empty($_W['uid'])) {
        if (!$_W['isfounder']) {
            $condition .= ' and uid > 0 and cuid = ' . $mywe['uid'];
        }
    } else {
        $condition .= $this->mycondition();
        if ($mywe['ispmanager'] == 1) {
            $condition .= ' and (gid > 0 or cuid=0)';
        } else {
            $condition .= ' and gid > 0 and cuid = ' . $mywe['uid'];
        }
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
        if (!empty($data)) {
            $k = 0;
            while (!($k >= count($data))) {
                if ($data[$k]['status'] == '1') {
                    $data[$k]['status'] = '启用';
                } else {
                    $data[$k]['status'] = '禁用';
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secgroup') . ' where id = :id and weid = :weid';
                $item = pdo_fetch($sql, array(':id' => $data[$k]['gid'], ':weid' => $mywe['weid']));
                if ($item) {
                    $data[$k]['group'] = $item['title'];
                } else {
                    $data[$k]['group'] = '超级管理员';
                }
                $data[$k]['property'] = '管理平台';
                if ($data[$k]['pid'] > 0) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :id and weid = :weid';
                    $property = pdo_fetchcolumn($sql, array(':id' => $data[$k]['pid'], ':weid' => $mywe['weid']));
                    $data[$k]['property'] = $property;
                }
                ($k += 1) + -1;
            }
        }
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '用户列表';
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND username LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    if (!empty($_GPC['status'])) {
        $condition .= ' AND status = \'' . $_GPC['status'] . '\'';
    }
    if (!empty($_W['uid'])) {
        if (!$_W['isfounder']) {
            $condition .= ' and uid > 0 and cuid = ' . $mywe['uid'];
        }
    } else {
        $condition .= $this->mycondition();
        $condition .= ' and gid > 0 and cuid =' . $mywe['uid'];
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
        if (!empty($data)) {
            $k = 0;
            while (!($k >= count($data))) {
                if ($data[$k]['status'] == '1') {
                    $data[$k]['status'] = '启用';
                } else {
                    $data[$k]['status'] = '禁用';
                }
                $sql = 'select * from ' . tablename('rhinfo_zyxq_secgroup') . ' where id = :id and weid = :weid';
                $item = pdo_fetch($sql, array(':id' => $data[$k]['gid'], ':weid' => $mywe['weid']));
                if ($item) {
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where id = :id and weid = :weid';
                    $property = pdo_fetch($sql, array(':id' => $data[$k]['gid'], ':weid' => $mywe['weid']));
                    if ($property) {
                        $data[$k]['group'] = $property['title'] . '-' . $item['title'];
                    } else {
                        $data[$k]['group'] = $item['title'];
                    }
                } else {
                    $data[$k]['group'] = '超级管理员';
                }
                $data[$k]['property'] = '管理平台';
                if ($data[$k]['pid'] > 0) {
                    $sql = 'select title from ' . tablename('rhinfo_zyxq_property') . ' where id = :id and weid = :weid';
                    $property = pdo_fetchcolumn($sql, array(':id' => $data[$k]['pid'], ':weid' => $mywe['weid']));
                    $data[$k]['property'] = $property;
                }
                ($k += 1) + -1;
            }
        }
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增用户';
    if (!empty($_W['uid'])) {
        if ($_W['isajax']) {
            $isexistsuser = false;
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE userno = :userno';
            $count = pdo_fetchcolumn($sql, array(':userno' => $_GPC['userno']));
            $password = md5($_GPC['password']);
            load()->model('user');
            $member = array();
            if (!empty($_GPC['password'])) {
                $member['password'] = $_GPC['password'];
            }
            $member['username'] = trim($_GPC['userno']);
            $member['remark'] = $_GPC['remark'];
            if (!preg_match(REGULAR_USERNAME, $member['username'])) {
                echo '必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点.';
                exit(0);
            }
            if (user_check(array('username' => $member['username']))) {
                $isexistsuser = true;
            }
            if (!empty($_GPC['password'])) {
                if (!(istrlen($member['password']) >= 8)) {
                    echo '必须输入密码，且密码长度不得低于8位.';
                    exit(0);
                }
            }
            $member['groupid'] = $_GPC['usergroup'];
            $member['starttime'] = TIMESTAMP;
            if ($count > 0) {
                echo '用户账号已存在!';
            } else {
                $data = array('weid' => $mywe['weid'], 'userno' => $_GPC['userno'], 'username' => $_GPC['username'], 'password' => $password, 'usergroup' => $_GPC['usergroup'], 'gid' => $_GPC['gid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'status' => $_GPC['status'], 'name' => $_GPC['name'], 'mobilephone' => $_GPC['mobilephone'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                if ($isexistsuser == false) {
                    $uid = user_register($member);
                    $user = array('uniacid' => $mywe['weid'], 'uid' => $uid, 'role' => 'operator');
                    pdo_insert('uni_account_users', $user);
                    $permission = array('uniacid' => $mywe['weid'], 'uid' => $uid, 'type' => 'rhinfo_zyxq', 'permission' => 'rhinfo_zyxq_menu_home');
                    pdo_insert('users_permission', $permission);
                } else {
                    $uid = pdo_fetchcolumn('select uid from ' . tablename('users') . ' where username=:username limit 1', array(':username' => $member['username']));
                    $member['uid'] = $uid;
                    user_update($member);
                }
                $data['uid'] = $uid;
                pdo_insert($tablename, $data);
                $id = pdo_insertid();
                echo 'ok';
                $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
            }
            exit(0);
        }
        $sql = 'select * from ' . tablename('users_group');
        $usergroup = pdo_fetchall($sql);
    } elseif ($_W['isajax']) {
        $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and userno = :userno';
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':userno' => $_GPC['userno']));
        $password = md5($_GPC['password']);
        if ($count > 0) {
            echo '用户账号已存在!';
        } else {
            $sql = 'select pid from ' . tablename('rhinfo_zyxq_secuser') . ' where  weid = :weid and id = :uid';
            $pid = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':uid' => $mywe['uid']));
            $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'userno' => $_GPC['userno'], 'username' => $_GPC['username'], 'password' => $password, 'usergroup' => 0, 'gid' => $_GPC['gid'], 'rid' => $_GPC['rid'], 'status' => $_GPC['status'], 'name' => $_GPC['name'], 'mobilephone' => $_GPC['mobilephone'], 'remark' => $_GPC['remark'], 'uid' => 0, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            pdo_insert($tablename, $data);
            $id = pdo_insertid();
            echo 'ok';
            $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        }
        exit(0);
    }
    if (!empty($_W['uid'])) {
        if ($_W['isfounder']) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_secgroup') . ' where pid=0 and ' . $condition;
            $group = pdo_fetchall($sql, $params);
        } else {
            $condition1 = $condition . ' and id = :pid';
            $condition .= ' and pid = :pid';
            $params[':pid'] = $mywe['pid'];
            $condition .= ' and cuid = ' . $mywe['uid'];
            $sql = 'select * from ' . tablename('rhinfo_zyxq_secgroup') . ' where ' . $condition;
            $group = pdo_fetchall($sql, $params);
        }
    } else {
        $condition1 = $condition . ' and id = :pid';
        $condition .= ' and pid = :pid';
        $params[':pid'] = $mywe['pid'];
        $condition .= ' and cuid = ' . $mywe['uid'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secgroup') . ' where ' . $condition;
        $group = pdo_fetchall($sql, $params);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where ' . $condition1;
    $myproperty = pdo_fetchall($sql, $params);
    if (!empty($_W['uid'])) {
        $myregion = array();
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            ($k += 1) + -1;
        }
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and id=:id';
        $secuser = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $mywe['uid']));
        $myregion = array();
        if (!empty($secuser['rid'])) {
            $k = 0;
            while (!($k >= count($group))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id=:rid';
                $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $group[$k]['pid'], ':rid' => $secuser['rid']));
                $myregion[$group[$k]['id']] = $regions;
                ($k += 1) + -1;
            }
        } else {
            $k = 0;
            while (!($k >= count($group))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
                $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $group[$k]['pid']));
                $myregion[$group[$k]['id']] = $regions;
                ($k += 1) + -1;
            }
        }
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑用户';
    $id = intval($_GPC['id']);
    if (!empty($_W['uid'])) {
        if ($_W['isajax']) {
            $user = user_single($_GPC['uid']);
            if (empty($user)) {
                echo '访问错误, 未找到指定操作员.';
                exit(0);
            }
            $password = md5($_GPC['password']);
            load()->model('user');
            $member = array();
            if (!empty($_GPC['password'])) {
                $member['password'] = $_GPC['password'];
            }
            $member['username'] = trim($_GPC['userno']);
            $member['remark'] = $_GPC['remark'];
            if (!empty($_GPC['password'])) {
                if (!(istrlen($member['password']) >= 8)) {
                    echo '必须输入密码，且密码长度不得低于8位.';
                    exit(0);
                }
            }
            $member['groupid'] = $_GPC['usergroup'];
            $member['uid'] = $_GPC['uid'];
            $member['salt'] = $user['salt'];
            user_update($member);
            if (empty($_GPC['password'])) {
                $data = array('username' => $_GPC['username'], 'usergroup' => $_GPC['usergroup'], 'gid' => $_GPC['gid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'status' => $_GPC['status'], 'name' => $_GPC['name'], 'mobilephone' => $_GPC['mobilephone'], 'remark' => $_GPC['remark']);
            } else {
                $data = array('username' => $_GPC['username'], 'password' => $password, 'usergroup' => $_GPC['usergroup'], 'gid' => $_GPC['gid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'status' => $_GPC['status'], 'name' => $_GPC['name'], 'mobilephone' => $_GPC['mobilephone'], 'remark' => $_GPC['remark']);
            }
            $glue = 'AND';
            pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
            echo 'ok';
            $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
            exit(0);
        }
        $sql = 'select * from ' . tablename('users_group');
        $usergroup = pdo_fetchall($sql);
    } elseif ($_W['isajax']) {
        $password = md5($_GPC['password']);
        if (empty($_GPC['password'])) {
            $data = array('username' => $_GPC['username'], 'usergroup' => $_GPC['usergroup'], 'gid' => $_GPC['gid'], 'rid' => $_GPC['rid'], 'status' => $_GPC['status'], 'name' => $_GPC['name'], 'mobilephone' => $_GPC['mobilephone'], 'remark' => $_GPC['remark']);
        } else {
            $data = array('username' => $_GPC['username'], 'password' => $password, 'usergroup' => $_GPC['usergroup'], 'gid' => $_GPC['gid'], 'rid' => $_GPC['rid'], 'status' => $_GPC['status'], 'name' => $_GPC['name'], 'mobilephone' => $_GPC['mobilephone'], 'remark' => $_GPC['remark']);
        }
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if (!empty($result)) {
            echo 'ok';
        } else {
            echo '更新失败，内容可能没有变化!';
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit(0);
    }
    if (!empty($_W['uid'])) {
        $condition1 = $condition;
        if (!empty($mywe['pid'])) {
            $condition1 .= ' and id = :pid';
            $condition .= ' and pid = :pid';
            $params[':pid'] = $mywe['pid'];
        }
        $condition .= ' and cuid = ' . $mywe['uid'];
    } else {
        $condition1 = $condition . ' and id = :pid';
        $condition .= ' and pid = :pid';
        $params[':pid'] = $mywe['pid'];
        $condition .= ' and cuid = ' . $mywe['uid'];
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_secgroup') . ' where ' . $condition;
    $group = pdo_fetchall($sql, $params);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_property') . ' where ' . $condition1;
    $myproperty = pdo_fetchall($sql, $params);
    if (!empty($_W['uid'])) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
        $region = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
        $myregion = array();
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            ($k += 1) + -1;
        }
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secuser') . ' where weid=:weid and id=:id';
        $secuser = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $mywe['uid']));
        $myregion = array();
        if (!empty($secuser['rid'])) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id=:rid';
            $region = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $mywe['pid'], ':rid' => $secuser['rid']));
            $k = 0;
            while (!($k >= count($group))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id=:rid';
                $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $group[$k]['pid'], ':rid' => $secuser['rid']));
                $myregion[$group[$k]['id']] = $regions;
                ($k += 1) + -1;
            }
        } else {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
            $region = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $mywe['pid']));
            $k = 0;
            while (!($k >= count($group))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
                $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $group[$k]['pid']));
                $myregion[$group[$k]['id']] = $regions;
                ($k += 1) + -1;
            }
        }
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除用户';
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    if (!empty($_W['uid'])) {
        $sql = 'select uid from ' . tablename($tablename) . ' where ' . $condition;
        $uid = pdo_fetchcolumn($sql, $params);
        if (pdo_delete('users', array('uid' => $uid)) === 1) {
            cache_build_account_modules();
            pdo_delete('uni_account_users', array('uid' => $uid));
            pdo_delete('users_profile', array('uid' => $uid));
        }
        $glue = 'AND';
        $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if ($result) {
            echo 'ok';
        } else {
            echo '删除失败!';
        }
    } else {
        $glue = 'AND';
        $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if ($result) {
            echo 'ok';
        } else {
            echo '删除失败!';
        }
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '用户状态';
    $id = intval($_GPC['id']);
    $data = array('status' => $_GPC['status']);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
}
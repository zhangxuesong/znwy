<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '角色管理';
$mydo = 'role';
$tablename = 'rhinfo_zyxq_secgroup';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$rights = $this->myrights(1, $mydo, 'list');
if ($operation == 'list') {
    $current = '角色列表';
    $myret = 0;
    if (!empty($_W['uid'])) {
        if (!$_W['isfounder']) {
            $condition .= ' and pid = :pid';
            $params[':pid'] = $mywe['pid'];
            $condition .= ' and cuid = ' . $mywe['uid'];
        }
    } else {
        $condition .= ' and pid = :pid';
        $params[':pid'] = $mywe['pid'];
        $condition .= ' and cuid = ' . $mywe['uid'];
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
    $current = '角色列表';
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    if (!empty($_GPC['status'])) {
        $condition .= ' AND status = \'' . $_GPC['status'] . '\'';
    }
    if (!empty($_W['uid'])) {
        if (!$_W['isfounder']) {
            $condition .= ' and pid = :pid';
            $params[':pid'] = $mywe['pid'];
            $condition .= ' and cuid = ' . $mywe['uid'];
        }
    } else {
        $condition .= ' and pid = :pid';
        $params[':pid'] = $mywe['pid'];
        $condition .= ' and cuid = ' . $mywe['uid'];
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
    $current = '新增角色';
    if ($_W['isajax']) {
        $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and title = :title';
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $_GPC['pid'], ':title' => $_GPC['title']));
        $menus = $_GPC['menus'];
        $prgs = $_GPC['prgs'];
        $perms = $_GPC['perms'];
        if ($count > 0) {
            echo '用户角色已存在!';
        } else {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'title' => $_GPC['title'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            pdo_insert($tablename, $data);
            $id = pdo_insertid();
            $k = 0;
            while (!($k >= count($menus))) {
                $usys = array('weid' => $mywe['weid'], 'gid' => $id, 'sid' => $menus[$k]);
                pdo_insert('rhinfo_zyxq_secgsys', $usys);
                ($k += 1) + -1;
            }
            $k = 0;
            while (!($k >= count($prgs))) {
                $uprg = array('weid' => $mywe['weid'], 'gid' => $id, 'sid' => substr($prgs[$k], 0, strrpos($prgs[$k], '.')));
                $pid = substr($prgs[$k], strrpos($prgs[$k], '.') + 1, strlen($prgs[$k]) - strrpos($prgs[$k], '.'));
                $uprg['pid'] = $pid;
                $m = 0;
                while (!($m >= count($perms))) {
                    if ($pid == substr($perms[$m], 0, strrpos($perms[$m], '.'))) {
                        $uprg[substr($perms[$m], strrpos($perms[$m], '.') + 1, strlen($perms[$m]) - strrpos($perms[$m], '.'))] = 1;
                    }
                    ($m += 1) + -1;
                }
                pdo_insert('rhinfo_zyxq_secgprg', $uprg);
                ($k += 1) + -1;
            }
            echo 'ok';
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        exit(0);
    }
    $property = $this->myproperty();
    $mymenus = $this->rolemenus();
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑角色';
    $gid = intval($_GPC['id']);
    if ($_W['isajax']) {
        $menus = $_GPC['menus'];
        $prgs = $_GPC['prgs'];
        $perms = $_GPC['perms'];
        $glue = 'AND';
        pdo_delete('rhinfo_zyxq_secgsys', array('gid' => $gid, 'weid' => $mywe['weid']), 'AND');
        $glue = 'AND';
        pdo_delete('rhinfo_zyxq_secgprg', array('gid' => $gid, 'weid' => $mywe['weid']), 'AND');
        $k = 0;
        while (!($k >= count($menus))) {
            $usys = array('weid' => $mywe['weid'], 'gid' => $gid, 'sid' => $menus[$k]);
            pdo_insert('rhinfo_zyxq_secgsys', $usys);
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($prgs))) {
            $sid = substr($prgs[$k], 0, strrpos($prgs[$k], '.'));
            $uprg = array('weid' => $mywe['weid'], 'gid' => $gid, 'sid' => $sid);
            $pid = substr($prgs[$k], strrpos($prgs[$k], '.') + 1, strlen($prgs[$k]) - strrpos($prgs[$k], '.'));
            $uprg['pid'] = $pid;
            $m = 0;
            while (!($m >= count($perms))) {
                if ($pid == substr($perms[$m], 0, strrpos($perms[$m], '.'))) {
                    $uprg[substr($perms[$m], strrpos($perms[$m], '.') + 1, strlen($perms[$m]) - strrpos($perms[$m], '.'))] = 1;
                }
                ($m += 1) + -1;
            }
            pdo_insert('rhinfo_zyxq_secgprg', $uprg);
            ($k += 1) + -1;
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'title' => $_GPC['title'], 'status' => $_GPC['status'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $gid, 'weid' => $mywe['weid']), 'AND');
        echo 'ok';
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $gid);
        exit(0);
    }
    $property = $this->myproperty();
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $gid, ':weid' => $mywe['weid']));
    $mymenus = $this->rolemenus_edit($gid);
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除角色';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    pdo_delete('rhinfo_zyxq_secgprg', array('gid' => $id, 'weid' => $mywe['weid']), 'AND');
    $glue = 'AND';
    pdo_delete('rhinfo_zyxq_secgsys', array('gid' => $id, 'weid' => $mywe['weid']), 'AND');
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '角色状态';
    $id = intval($_GPC['id']);
    $data = array('status' => $_GPC['status']);
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
}
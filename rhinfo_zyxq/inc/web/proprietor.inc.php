<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '事务管理';
$mydo = 'proprietor';
$tablename = 'rhinfo_zyxq_region';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
if ($operation == 'list') {
    $rights = $this->myrights(14, $mydo, 'list');
    $myret = 0;
    $current = '业委会申请';
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where category=1 and status=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where category=1 and status=1 and ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'mlist') {
    $rights = $this->myrights(14, $mydo, 'mlist');
    $myret = 0;
    $current = '业委会成员';
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where category=1 and status=2 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where category=1 and status=2 and ' . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('mlist');
} elseif ($operation == 'add') {
    if ($_GPC['lfrom'] == 'list') {
        $current = '业委会申请';
    } else {
        $current = '创建业委会';
    }
    if ($_W['ispost']) {
        $id = intval($_GPC['rid']);
        $data = array('asktype' => $_GPC['asktype'], 'buildarea' => $_GPC['buildarea'], 'coveredarea' => $_GPC['coveredarea'], 'roomqty' => $_GPC['roomqty'], 'uid' => $mywe['uid']);
        if ($_GPC['lfrom'] == 'list') {
            $data['status'] = 1;
            $data['asktime'] = TIMESTAMP;
        } else {
            $data['status'] = 2;
            $data['propctime'] = TIMESTAMP;
        }
        pdo_update($tablename, $data, array('weid' => $mywe['weid'], 'id' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => $_GPC['lfrom'])) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where category=1 and weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    if ($_GPC['lfrom'] == 'list') {
        $current = '编辑业委会申请';
    } else {
        $current = '编辑业委会';
    }
    $id = intval($_GPC['rid']);
    if ($_W['ispost']) {
        $data = array('asktype' => $_GPC['asktype'], 'buildarea' => $_GPC['buildarea'], 'coveredarea' => $_GPC['coveredarea'], 'roomqty' => $_GPC['roomqty']);
        pdo_update($tablename, $data, array('weid' => $mywe['weid'], 'id' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => $_GPC['lfrom'])) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where category=1 and weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where category=1 and weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除业委会';
    $id = intval($_GPC['rid']);
    $result = pdo_update($tablename, array('status' => 0), array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '申请状态';
    $id = intval($_GPC['rid']);
    $data = array('status' => 2);
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and id= :id and status>0 ';
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and id= :id and status>0 and id <> :id';
        }
        $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':id' => $_GPC['rid']));
        if ($count > 0) {
            echo '该小区已申请!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'listmember') {
    $rights = $this->myrights(14, $mydo, 'mlist');
    $myret = 1;
    $current = '业委会成员';
    $rid = $_GPC['rid'];
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
    $navtitle = $navtitle . ' > ' . $region;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_pmember') . ' where weid=:weid and rid=:rid and status>0';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_pmember') . ' where weid=:weid and rid=:rid and status>0 ORDER BY `ID` ASC ';
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
    }
    include $this->mywtpl('listmember');
} elseif ($operation == 'addmember') {
    $current = '添加成员';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'realname' => $_GPC['realname'], 'nickname' => $_GPC['nickname'], 'level' => $_GPC['level'], 'mobile' => $_GPC['mobile'], 'content' => htmlspecialchars_decode($_GPC['content']), 'avatar' => $_GPC['avatar'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_pmember', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'listmember', 'rid' => $_GPC['rid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
    $navtitle = $navtitle . ' > ' . $region;
    include $this->mywtpl('postmember');
} elseif ($operation == 'editmember') {
    $current = '编辑成员';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('realname' => $_GPC['realname'], 'nickname' => $_GPC['nickname'], 'level' => $_GPC['level'], 'mobile' => $_GPC['mobile'], 'content' => htmlspecialchars_decode($_GPC['content']), 'avatar' => $_GPC['avatar'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $result = pdo_update('rhinfo_zyxq_pmember', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'listmember', 'rid' => $_GPC['rid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_pmember') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
    $navtitle = $navtitle . ' > ' . $region;
    include $this->mywtpl('postmember');
} elseif ($operation == 'delmember') {
    $current = '删除成员';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zyxq_pmember', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'deleteall') {
    $current = '删除全部成员';
    $result = pdo_delete('rhinfo_zyxq_pmember', array('weid' => $mywe['weid'], 'rid' => $_GPC['rid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'rid=' . $rid);
    exit(0);
} elseif ($operation == 'checkmember') {
    if ($_W['isajax'] && $_GPC['level'] == 1) {
        if ($_GPC['post'] == 'addmember') {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_pmember') . ' WHERE weid = :weid and rid= :rid and level=1 ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_pmember') . ' WHERE weid = :weid and rid= :rid and level=1 and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid'], ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '主任已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
    echo 'ok';
    exit(0);
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'team';
$tablename = 'rhinfo_zyxq_team';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '服务团队';
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '人员列表';
    $myret = 0;
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $data[$k]['regionname'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            if ($data[$k]['rid'] == 0) {
                $data[$k]['regionname'] = empty($data[$k]['ridstr']) ? '所属物业全部主体' : '多选主体';
            }
            $data[$k]['regionname'] = !empty($data[$k]['regionname']) ? $data[$k]['regionname'] : '多主体';
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_category') . ' where weid and :weid and id = :cid';
            $data[$k]['catename'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $data[$k]['cid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '人员列表';
    $myret = 0;
    $condition1 = '';
    if (!empty($_GPC['regionname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['regionname'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition1 .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition1 .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition1 .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (!empty($condition1)) {
        $condition1 = ' and (rid in (select id from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $condition1 . '))';
        $regioncondition .= $condition1;
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (realname LIKE \'%' . $_GPC['keyword'] . '%\' OR nickname LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $data[$k]['regionname'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_category') . ' where weid and :weid and id = :cid';
            $data[$k]['catename'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cid' => $data[$k]['cid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '添加人员';
    if ($_W['ispost']) {
        if ($_GPC['cate'] == 2) {
            $rid = 0;
            $ridstr = $_GPC['regions'];
        } else {
            $rid = $_GPC['rid'];
            $ridstr = '0';
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $rid, 'cid' => $_GPC['cid'], 'realname' => $_GPC['realname'], 'nickname' => $_GPC['nickname'], 'mobile' => $_GPC['mobile'], 'entrydate' => strtotime($_GPC['entrydate']), 'content' => $_GPC['content'], 'workyears' => $_GPC['workyears'], 'avatar' => $_GPC['avatar'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'remark' => $_GPC['remark'], 'ridstr' => $ridstr, 'status' => $_GPC['status'], 'displayorder' => $_GPC['displayorder'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mycate = array();
    $cate = !empty($_GPC['cate']) ? $_GPC['cate'] : 1;
    if ($_GPC['cate'] == 2) {
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = 0 and type=4';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $mycate[$myproperty[$k]['id']] = $categorys;
            ($k += 1) + -1;
        }
        include $this->mywtpl('postteam');
    } else {
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            $m = 0;
            while (!($m >= count($regions))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type=4';
                $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
                $mycate[$regions[$m]['id']] = $categorys;
                ($m += 1) + -1;
            }
            ($k += 1) + -1;
        }
        include $this->mywtpl('post');
    }
} elseif ($operation == 'edit') {
    $current = '编辑人员';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        if ($_GPC['cate'] == 2) {
            $rid = 0;
            $ridstr = $_GPC['regions'];
        } else {
            $rid = $_GPC['rid'];
            $ridstr = '0';
        }
        $data = array('pid' => $_GPC['pid'], 'rid' => $rid, 'cid' => $_GPC['cid'], 'realname' => $_GPC['realname'], 'nickname' => $_GPC['nickname'], 'mobile' => $_GPC['mobile'], 'entrydate' => strtotime($_GPC['entrydate']), 'content' => $_GPC['content'], 'workyears' => $_GPC['workyears'], 'avatar' => $_GPC['avatar'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'displayorder' => $_GPC['displayorder'], 'ridstr' => $ridstr, 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mycate = array();
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $cate = !empty($item['rid']) ? 1 : 2;
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    if ($cate == 2) {
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = 0 and type=4';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $mycate[$myproperty[$k]['id']] = $categorys;
            ($k += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
        $count = count($regions);
        $regionarr = explode(',', $item['ridstr']);
        $regionarr = array_filter($regionarr);
        $regionname = array();
        $m = 0;
        while (!($m >= $count)) {
            if (in_array($regions[$m]['id'], $regionarr)) {
                $regionname[] = $regions[$m]['title'];
            }
            ($m += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type=4';
        $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => 0));
        include $this->mywtpl('postteam');
    } else {
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            $m = 0;
            while (!($m >= count($regions))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type=4';
                $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
                $mycate[$regions[$m]['id']] = $categorys;
                ($m += 1) + -1;
            }
            ($k += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type=4';
        $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
        include $this->mywtpl('post');
    }
} elseif ($operation == 'delete') {
    $current = '删除人员';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and realname = :realname ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':realname' => $_GPC['realname']));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and realname = :realname and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':realname' => $_GPC['realname'], ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '该人员已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'evaluate') {
    $current = '评价记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and teamid = :teamid';
    $params[':teamid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_team_comment') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_team_comment') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('evaluate');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_team_comment', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
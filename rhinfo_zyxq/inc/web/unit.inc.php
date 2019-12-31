<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$category = !empty($_GPC['category']) ? $_GPC['category'] : 1;
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '单元管理';
$mydo = 'unit';
$tablename = 'rhinfo_zyxq_unit';
$condition = ' weid = :weid and pid = :pid and rid = :rid and bid = :bid';
$pindex = max(1, intval($_GPC['page']));
$psize = 150;
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$bid = $_GPC['bid'];
$params = array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid);
$rights = $this->myrights(2, $mydo, 'list');
$sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
$region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
$sql = 'select * from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
$building = pdo_fetch($sql, $params);
$navtitle = $region['title'] . ' > ' . $building['title'] . ' > ' . $navtitle;
if ($operation == 'list') {
    $current = '单元-房屋列表';
    $myret = 1;
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $params1 = array();
        $params1 = $params;
        $k = 0;
        while (!($k >= count($data))) {
            $params1[':tid'] = $data[$k]['id'];
            if (!empty($region['roomfix'])) {
                $sql = 'select id,pid,rid,bid,tid,floor,concat(title,"' . $region['roomfix'] . '") as title from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . ' and tid = :tid ORDER BY title ASC ';
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . ' and tid = :tid ORDER BY title*1 ASC ';
            }
            $data[$k]['room'] = pdo_fetchall($sql, $params1);
            $data[$k]['trooms'] = count($data[$k]['room']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_garage') . ' where ' . $condition . ' ORDER BY title*1 ASC ';
        $garage = pdo_fetchall($sql, $params);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增单元';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $_GPC['title'], 'floors' => $_GPC['floors'], 'rooms' => $_GPC['rooms'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $tid = pdo_insertid();
        $m = intval($_GPC['startnum']);
        if ($_GPC['batchroom'] && $m) {
            $uroom = intval($_GPC['rooms']);
            $ufloor = intval($_GPC['floors']);
            $digit = strlen($_GPC['startnum']);
            if ($uroom && $ufloor) {
                $j = 1;
                while (!($j > $uroom)) {
                    $k = 1;
                    while (!($k > $ufloor)) {
                        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'tid' => $tid, 'floor' => $k, 'title' => $k . sprintf('%0' . $digit . 'd', $m), 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_room', $data);
                        ($k += 1) + -1;
                    }
                    $m += 1;
                    ($j += 1) + -1;
                }
            }
        }
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $tid);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid, 'bid' => $bid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑单元';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'floors' => $_GPC['floors'], 'rooms' => $_GPC['rooms']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $m = intval($_GPC['startnum']);
        if ($_GPC['batchroom'] && $m) {
            $uroom = intval($_GPC['rooms']);
            $ufloor = intval($_GPC['floors']);
            $digit = strlen($_GPC['startnum']);
            if ($uroom && $ufloor) {
                $j = 1;
                while (!($j > $uroom)) {
                    $k = 1;
                    while (!($k > $ufloor)) {
                        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'tid' => $id, 'floor' => $k, 'title' => $k . sprintf('%0' . $digit . 'd', $m), 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_room', $data);
                        ($k += 1) + -1;
                    }
                    $m += 1;
                    ($j += 1) + -1;
                }
            }
        }
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid, 'bid' => $bid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'deleteall') {
    $current = '删除所有单元';
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and rid=:rid and bid=:bid ';
    $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $rid, ':bid' => $bid));
    if ($count > 0) {
        echo '账单已存在!';
    } else {
        $glue = 'AND';
        $result = pdo_delete('rhinfo_zyxq_room_mp', array('weid' => $mywe['weid'], 'rid' => $rid, 'bid' => $bid), 'AND');
        $glue = 'AND';
        $result = pdo_delete('rhinfo_zyxq_room', array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid), 'AND');
        $glue = 'AND';
        $result = pdo_delete($tablename, array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid), 'AND');
        if (!empty($result)) {
            echo 'ok';
        } else {
            echo '删除失败!';
        }
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'bid=' . $bid);
    exit(0);
} elseif ($operation == 'delete') {
    $current = '删除单元';
    $id = intval($_GPC['id']);
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid ';
    $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $rid, ':bid' => $bid, ':tid' => $id));
    if ($count > 0) {
        echo '账单已存在!';
    } else {
        $glue = 'AND';
        $result = pdo_delete('rhinfo_zyxq_room_mp', array('weid' => $mywe['weid'], 'rid' => $rid, 'bid' => $bid, 'tid' => $id), 'AND');
        $glue = 'AND';
        $result = pdo_delete('rhinfo_zyxq_room', array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'tid' => $id), 'AND');
        $glue = 'AND';
        $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if (!empty($result)) {
            echo 'ok';
        } else {
            echo '删除失败!';
        }
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and bid = :bid ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and bid = :bid and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid, ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '单元已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
}
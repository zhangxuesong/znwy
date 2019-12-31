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
$navtitle = '储物间管理';
$mydo = 'garage';
$tablename = 'rhinfo_zyxq_garage';
$condition = ' weid = :weid and rid = :rid and bid = :bid ';
$pindex = max(1, intval($_GPC['page']));
$psize = 150;
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$bid = $_GPC['bid'];
$params = array(':weid' => $mywe['weid'], ':rid' => $rid, ':bid' => $bid);
$sql = 'select title,category from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
$region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $rid));
$rlist = 'list';
if ($region['category'] == 1) {
    $rlist = 'list';
} elseif ($region['category'] == 2) {
    $rlist = 'blist';
} elseif ($region['category'] == 3) {
    $rlist = 'glist';
} elseif ($region['category'] == 4) {
    $rlist = 'mlist';
} elseif ($region['category'] == 5) {
    $rlist = 'alist';
}
$sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
$building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid));
$navtitle = $region['title'] . ' > ' . $building;
if ($operation == 'list') {
    $current = '储物间列表';
    $navtitle = $region['title'];
    $myret = 1;
    $rights = $this->myrights(2, 'building', 'list');
    $condition = ' weid = :weid and pid = :pid and rid = :rid ';
    $params = array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_building') . ' where isbarn=1 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_building') . ' where isbarn=1 and ' . $condition . ' ORDER BY title ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
        $k = 0;
        while (!($k >= count($data))) {
            $condition .= ' and bid = :bid';
            $params[':bid'] = $data[$k]['id'];
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title*1 ASC ';
            $data[$k]['garages'] = pdo_fetchall($sql, $params);
            ($k += 1) + -1;
        }
    }
    include $this->mywtpl('list');
} elseif ($operation == 'batchadd') {
    $current = '批量新增储物间';
    if ($_W['ispost']) {
        $startnum = intval($_GPC['startnum']);
        $endnum = intval($_GPC['endnum']);
        $digit = intval($_GPC['digit']);
        if ($startnum >= $endnum) {
            $title = $_GPC['prefix'] . $startnum . $_GPC['suffix'];
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and rid = :rid and bid = :bid and title = :title';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid, ':title' => $title));
            if (!($count > 0)) {
                $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $title, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                pdo_insert($tablename, $data);
                $id = pdo_insertid();
                $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
            }
        } else {
            $n = $startnum;
            while (!($n > $endnum)) {
                if ($digit > 1) {
                    $title = $_GPC['prefix'] . sprintf('%0' . $digit . 'd', $n) . $_GPC['suffix'];
                } else {
                    $title = $_GPC['prefix'] . $n . $_GPC['suffix'];
                }
                $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and rid = :rid and bid = :bid and title = :title';
                $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid, ':title' => $title));
                if (!($count > 0)) {
                    $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $title, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    pdo_insert($tablename, $data);
                    $id = pdo_insertid();
                    $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
                }
                ($n += 1) + -1;
            }
        }
        header('Location:' . $this->createWeburl('unit', array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid, 'bid' => $bid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('batchadd');
} elseif ($operation == 'batchadd1') {
    $navtitle = $region['title'];
    $current = '批量新增储物间';
    if ($_W['ispost']) {
        $startnum = intval($_GPC['startnum']);
        $endnum = intval($_GPC['endnum']);
        $digit = intval($_GPC['digit']);
        if ($startnum >= $endnum) {
            $title = $_GPC['prefix'] . $startnum . $_GPC['suffix'];
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and rid = :rid and bid = :bid and title = :title';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid, ':title' => $title));
            if (!($count > 0)) {
                $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $title, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                pdo_insert($tablename, $data);
                $id = pdo_insertid();
                $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
            }
        } else {
            $n = $startnum;
            while (!($n > $endnum)) {
                if ($digit > 1) {
                    $title = $_GPC['prefix'] . sprintf('%0' . $digit . 'd', $n) . $_GPC['suffix'];
                } else {
                    $title = $_GPC['prefix'] . $n . $_GPC['suffix'];
                }
                $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and rid = :rid and bid = :bid and title = :title';
                $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':bid' => $bid, ':title' => $title));
                if (!($count > 0)) {
                    $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $title, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                    pdo_insert($tablename, $data);
                    $id = pdo_insertid();
                    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
                }
                ($n += 1) + -1;
            }
        }
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and isbarn=1';
    $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid));
    include $this->mywtpl('batchadd1');
} elseif ($operation == 'add') {
    $current = '新增储物间';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $_GPC['title'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'electmeter' => $_GPC['electmeter'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('unit', array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'tid' => $tid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'add1') {
    $current = '新增储物间';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'title' => $_GPC['title'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'electmeter' => $_GPC['electmeter'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post1');
} elseif ($operation == 'edit') {
    $current = '编辑储物间';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'electmeter' => $_GPC['electmeter'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl('unit', array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid, 'bid' => $bid, 'tid' => $tid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'edit1') {
    $current = '编辑储物间';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'electmeter' => $_GPC['electmeter'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post1');
} elseif ($operation == 'delete') {
    $current = '删除储物间';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'deleteall') {
    $current = '删除全部储物间';
    $glue = 'AND';
    $result = pdo_delete($tablename, array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, 'region', $operation, $current, $current . 'rid=' . $rid);
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
            echo '储物间已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'import') {
    $current = '导入储物间';
    if ($_W['isajax']) {
        if (!empty($_FILES['upfile']['name'])) {
            $tmp_file = $_FILES['upfile']['tmp_name'];
            $file_types = explode('.', $_FILES['upfile']['name']);
            $file_type = $file_types[count($file_types) - 1];
            if (strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
                echo '类型不正确，请重新上传';
                exit(0);
            }
            $savePath = IA_ROOT . '/addons/rhinfo_zyxq/upfile/';
            $str = date('Ymdhis');
            $file_name = $str . '.' . $file_type;
            if (!copy($tmp_file, $savePath . $file_name)) {
                echo '上传失败';
                exit(0);
            }
            $res = import_excel($savePath . $file_name);
            $i = 0;
            $k = 0;
            while (!($k >= count($res))) {
                if ($k != 0) {
                    $data['weid'] = $mywe['weid'];
                    $data['pid'] = $pid;
                    $data['rid'] = $rid;
                    $sql = 'select * from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and title = :title';
                    $building = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':title' => $res[$k][0]));
                    if ($building) {
                        $bid = $building['id'];
                        $data['bid'] = $bid;
                        $data['title'] = $res[$k][1];
                        $data['buildarea'] = $res[$k][2];
                        $data['usearea'] = $res[$k][3];
                        $data['addarea'] = $res[$k][4];
                        $data['ownername'] = $res[$k][5];
                        $data['mobile'] = $res[$k][6];
                        $data['mobile1'] = $res[$k][7];
                        $data['isfree'] = $res[$k][8];
                        $data['freestart'] = strtotime($res[$k][9]);
                        $data['freeend'] = strtotime($res[$k][10]);
                        $data['paydate'] = strtotime($res[$k][11]);
                        $data['status'] = 0;
                        $data['electmeter'] = $res[$k][12];
                        $data['remark'] = $res[$k][13];
                        if ($data['title']) {
                            $result = pdo_insert($tablename, $data);
                            ($i += 1) + -1;
                        }
                    }
                }
                ($k += 1) + -1;
            }
            if ($i > 0) {
                echo 'ok';
            } else {
                echo '导入失败!';
            }
            unlink($savePath . $file_name);
            exit(0);
        } else {
            echo '文件不正确错误!';
            exit(0);
        }
    }
    include $this->mywtpl('import');
} elseif ($operation == 'guideimport') {
    include $this->mywtpl();
} elseif ($operation == 'guideedit') {
    $current = '编辑储物间';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $data = array('title' => $_GPC['title'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'electmeter' => $_GPC['electmeter'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, 'region', $operation, $current, $current . 'id=' . $id);
        if ($result) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl();
}
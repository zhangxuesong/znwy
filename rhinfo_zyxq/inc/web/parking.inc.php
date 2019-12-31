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
$navtitle = '车位设置';
$mydo = 'parking';
$tablename = 'rhinfo_zyxq_parking';
$condition = ' weid = :weid and rid = :rid';
$pindex = max(1, intval($_GPC['page']));
$psize = 150;
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$params = array(':weid' => $mywe['weid'], ':rid' => $rid);
$rights = $this->myrights(2, $mydo, 'list');
$sql = 'select title,category from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
$region = pdo_fetch($sql, $params);
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
$navtitle = $region['title'] . ' > ' . $navtitle;
if ($operation == 'list') {
    $current = '车位列表';
    $myret = 1;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_location') . ' where ' . $condition . ' and category = 2';
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where ' . $condition . ' and category = 2 ORDER BY title ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
        $k = 0;
        while (!($k >= count($data))) {
            $condition .= ' and lid = :lid';
            $params[':lid'] = $data[$k]['id'];
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title ASC ';
            $data[$k]['parkings'] = pdo_fetchall($sql, $params);
            ($k += 1) + -1;
        }
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增车位';
    $lid = $_GPC['lid'];
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'isfree' => $_GPC['isfree'], 'lockmac' => $_GPC['lockmac'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        if ($_GPC['category'] == 1) {
            $data['startdate'] = strtotime($_GPC['startdate']);
            $data['enddate'] = strtotime($_GPC['enddate']);
            $data['paydate'] = strtotime($_GPC['paydate']);
        }
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where ' . $condition . ' and category = 2 ORDER BY title ASC ';
    $data = pdo_fetchall($sql, $params);
    include $this->mywtpl('post');
} elseif ($operation == 'batchadd') {
    $current = '批量新增车位';
    if ($_W['ispost']) {
        $startnum = intval($_GPC['startnum']);
        $endnum = intval($_GPC['endnum']);
        $digit = intval($_GPC['digit']);
        if ($startnum >= $endnum) {
            $title = $_GPC['prefix'] . $startnum . $_GPC['suffix'];
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and rid = :rid and title = :title';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':title' => $title));
            if (!($count > 0)) {
                $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'lid' => $_GPC['lid'], 'category' => $_GPC['category'], 'title' => $title, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
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
                $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and pid = :pid and rid = :rid and title = :title';
                $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':title' => $title));
                if (!($count > 0)) {
                    $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'lid' => $_GPC['lid'], 'category' => $_GPC['category'], 'title' => $title, 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where ' . $condition . ' and category = 2 ORDER BY title ASC ';
    $data = pdo_fetchall($sql, $params);
    include $this->mywtpl('batchadd');
} elseif ($operation == 'edit') {
    $current = '编辑车位';
    $id = intval($_GPC['id']);
    $lid = $_GPC['lid'];
    if ($_W['ispost']) {
        $data = array('lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'isfree' => $_GPC['isfree'], 'lockmac' => $_GPC['lockmac'], 'remark' => $_GPC['remark']);
        if ($_GPC['category'] == 1) {
            $data['startdate'] = strtotime($_GPC['startdate']);
            $data['enddate'] = strtotime($_GPC['enddate']);
            $data['paydate'] = strtotime($_GPC['paydate']);
        }
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where ' . $condition . ' and category = 2 ORDER BY title ASC ';
    $data = pdo_fetchall($sql, $params);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'deleteall') {
    $current = '删除全部车位';
    $glue = 'AND';
    $result = pdo_delete($tablename, array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'rid=' . $rid);
    exit(0);
} elseif ($operation == 'delete') {
    $current = '删除车位';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $res = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if ($res) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'addlocation') {
    $current = '车位区域';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'title' => $_GPC['title'], 'category' => 2, 'price' => $_GPC['price'], 'feemonths' => $_GPC['feemonths'], 'finmonths' => $_GPC['finmonths'], 'givemonths' => $_GPC['givemonths'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_location', $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('postlocation');
} elseif ($operation == 'editlocation') {
    $current = '车位区域';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'price' => $_GPC['price'], 'feemonths' => $_GPC['feemonths'], 'finmonths' => $_GPC['finmonths'], 'givemonths' => $_GPC['givemonths'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_location', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('postlocation');
} elseif ($operation == 'dellocation') {
    $current = '删除区域';
    $id = intval($_GPC['id']);
    $res = pdo_delete('rhinfo_zyxq_parking', array('lid' => $id, 'weid' => $mywe['weid']));
    $res = pdo_delete('rhinfo_zyxq_location', array('id' => $id, 'weid' => $mywe['weid']));
    if ($res) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'add') {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid ';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':pid' => $pid, ':rid' => $rid));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename($tablename) . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and id <> :id';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':pid' => $pid, ':rid' => $rid, ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '车位已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'check_location') {
    if ($_W['isajax']) {
        if ($_GPC['post'] == 'addlocation') {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_location') . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and category=2';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':pid' => $pid, ':rid' => $rid));
        } else {
            $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_location') . ' WHERE weid = :weid and title = :title and pid = :pid and rid = :rid and id <> :id and category=2';
            $count = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':title' => $_GPC['title'], ':pid' => $pid, ':rid' => $rid, ':id' => $_GPC['id']));
        }
        if ($count > 0) {
            echo '区域已存在!';
        } else {
            echo 'ok';
        }
        exit(0);
    }
} elseif ($operation == 'import') {
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
            $data = array();
            $k = 0;
            while (!($k >= count($res))) {
                if ($k != 0) {
                    if (!empty($res[$k][1])) {
                        $data['weid'] = $mywe['weid'];
                        $data['pid'] = $pid;
                        $data['rid'] = $rid;
                        $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2 and title = :title';
                        $building = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $pid, ':rid' => $rid, ':title' => $res[$k][0]));
                        if ($building) {
                            $bid = $building['id'];
                        } else {
                            $building_data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'title' => $res[$k][0], 'category' => 2, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                            pdo_insert('rhinfo_zyxq_location', $building_data);
                            $bid = pdo_insertid();
                        }
                        $data['lid'] = $bid;
                        $data['category'] = intval($res[$k][2]);
                        $data['title'] = $res[$k][1];
                        $data['buildarea'] = $res[$k][3];
                        $data['usearea'] = $res[$k][4];
                        $data['addarea'] = $res[$k][5];
                        $data['ownername'] = $res[$k][6];
                        $data['mobile'] = $res[$k][7];
                        $data['mobile1'] = $res[$k][8];
                        $data['carno'] = $res[$k][9];
                        $data['isfree'] = $res[$k][10];
                        $data['freestart'] = strtotime($res[$k][11]);
                        $data['freeend'] = strtotime($res[$k][12]);
                        $data['paydate'] = strtotime($res[$k][13]);
                        $data['status'] = 0;
                        if ($data['category'] == 0) {
                            $data['startdate'] = strtotime($res[$k][14]);
                            $data['enddate'] = strtotime($res[$k][15]);
                            $data['price'] = floatval($res[$k][16]);
                            $data['pricemethod'] = $res[$k][17];
                            if ($data['enddate'] > TIMESTAMP) {
                                $data['status'] = 1;
                            }
                            $data['paymonths'] = intval($res[$k][18]) > 0 ? intval($res[$k][18]) : 1;
                        }
                        $data['remark'] = $res[$k][19];
                        $result = pdo_insert('rhinfo_zyxq_parking', $data);
                        if ($result) {
                            if (!empty($res[$k]['9'])) {
                                $car_data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'title' => $res[$k][9], 'carno' => $res[$k][9], 'ownername' => $res[$k][6], 'mobile' => $res[$k][7], 'remark' => '', 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                                $car_data['startdate'] = !empty($res[$k][14]) ? strtotime($res[$k][14]) : '';
                                $car_data['enddate'] = !empty($res[$k][15]) ? strtotime($res[$k][15]) : '';
                                pdo_insert('rhinfo_zyxq_car', $car_data);
                            }
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
    $current = '编辑车位';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $data = array('lid' => $_GPC['lid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'isfree' => $_GPC['isfree'], 'lockmac' => $_GPC['lockmac'], 'remark' => $_GPC['remark']);
        if ($_GPC['category'] == 1) {
            $data['startdate'] = strtotime($_GPC['startdate']);
            $data['enddate'] = strtotime($_GPC['enddate']);
            $data['paydate'] = strtotime($_GPC['paydate']);
        }
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        if ($result) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_location') . ' where ' . $condition . ' and category = 2 ORDER BY title ASC ';
    $locations = pdo_fetchall($sql, $params);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl();
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'member';
$tablename = 'rhinfo_zyxq_member';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '住户管理';
$rights = $this->myrights(6, $mydo, 'list');
if ($operation == 'list') {
    $current = '住户列表';
    $myret = 0;
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region['title'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid and pid=:pid and rid=:rid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $data[$k]['tid'], ':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['unit'] = $unit;
            if (!empty($region['roomfix'])) {
                $data[$k]['title'] = $data[$k]['title'] . $region['roomfix'];
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '住户列表';
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
        $condition .= ' AND (ownername LIKE \'%' . $_GPC['keyword'] . '%\' OR mobile LIKE \'%' . $_GPC['keyword'] . '%\' OR title LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['isfree'])) {
        $condition .= ' AND isfree=' . $_GPC['isfree'];
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($_GPC['export'] == 'export') {
        $limit = '';
    }
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
            $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid'], ':tid' => $data[$k]['tid']));
            $data[$k]['unit'] = $unit;
            $data[$k]['paydate'] = empty($data[$k]['paydate']) ? '' : date('Y-m-d', $data[$k]['paydate']);
            $data[$k]['mobile1'] = '';
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'region' => '小区或商圈', 'building' => '楼栋', 'unit' => '单元', 'title' => '房屋', 'buildarea' => '建筑面积', 'usearea' => '使用面积', 'addarea' => '附加面积', 'ownername' => '业主姓名', 'mobile1' => '手机号码', 'paydate' => '交费截止日期', 'remark' => '备注');
            export_excel($data, $filter, '业主信息');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    } elseif ($_GPC['export'] != '') {
        $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct'], 'danger');
    }
    include $this->mywtpl('list');
} elseif ($operation == 'import') {
    $current = '人员导入';
    if ($_W['isajax']) {
        if (!empty($_FILES['upfile']['name'])) {
            $tmp_file = $_FILES['upfile']['tmp_name'];
            $file_types = explode('.', $_FILES['upfile']['name']);
            $file_type = $file_types[count($file_types) - 1];
            if (strtolower($file_type) != 'csv' && strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
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
            if (strtolower($file_type) == 'csv') {
                $res = import_csv($savePath . $file_name);
            } else {
                $res = import_excel($savePath . $file_name);
            }
            $i = 0;
            $k = 0;
            while (!($k >= count($res))) {
                if ($k != 0) {
                    $id = $res[$k][0];
                    $data['buildarea'] = $res[$k][5];
                    $data['usearea'] = $res[$k][6];
                    $data['addarea'] = $res[$k][7];
                    $data['ownername'] = $res[$k][8];
                    $data['mobile'] = $res[$k][9];
                    $data['paydate'] = strtotime($res[$k][10]);
                    $data['remark'] = $res[$k][11];
                    if (!empty($data['ownername'])) {
                        $result = pdo_update('rhinfo_zyxq_room', $data, array('weid' => $mywe['weid'], 'id' => $id));
                        if ($result) {
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
} elseif ($operation == 'change') {
    $current = '变更信息';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => '');
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_room', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $room_chg = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $id, 'ownername' => $_GPC['oldownername'], 'mobile' => $_GPC['oldmobile'], 'newownername' => $_GPC['ownername'], 'newmobile' => $_GPC['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_room_chglog', $room_chg);
        pdo_update('rhinfo_zyxq_member', array('deleted' => 1), array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $id));
        pdo_update('rhinfo_zyxq_room_mp', array('deleted' => 1), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $item['title'] = $building . '-' . $unit . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    include $this->mywtpl('change');
} elseif ($operation == 'edit') {
    $current = '登记信息';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'isfree' => $_GPC['isfree'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_room', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $item['title'] = $building . '-' . $unit . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    include $this->mywtpl('edit');
} elseif ($operation == 'dellog') {
    $current = '删除变更记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_room_chglog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'changelog') {
    $current = '变更记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_room_chglog') . ' where weid=:weid and hid=:hid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':hid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room_chglog') . " where weid=:weid and hid=:hid ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':hid' => $id));
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('changelog');
} elseif ($operation == 'weixin') {
    $current = '微信绑定';
    $myret = 0;
    $rights = $this->myrights(6, $mydo, 'weixin');
    $condition .= $this->myrcondition();
    load()->model('mc');
    $fans = array();
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where deleted=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where deleted=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region['title'];
            $data[$k]['thirdauth'] = $region['thirdauth'];
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
            $data[$k]['openid'] = empty($data[$k]['openid']) ? $data[$k]['uid'] : $data[$k]['openid'];
            $fans = mc_fansinfo($data[$k]['openid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = tomedia($fans['avatar']);
            $data[$k]['nickname'] = $fans['nickname'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('wlist');
} elseif ($operation == 'wedit') {
    $current = '修改绑定';
    $id = intval($_GPC['id']);
    $data = array('realname' => $_GPC['realname']);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if ($result) {
        $item = pdo_fetch('select * from ' . tablename($tablename) . ' where weid=:weid and id=:id', array(':weid' => $mywe['weid'], ':id' => $id));
        $glue = 'AND';
        pdo_update('mc_members', $data, array('uid' => $item['uid'], 'uniacid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
    } else {
        echo '修改失败!';
    }
    exit(0);
} elseif ($operation == 'search1') {
    $current = '微信绑定';
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
        $condition .= ' AND (realname LIKE \'%' . $_GPC['keyword'] . '%\' OR mobile LIKE \'%' . $_GPC['keyword'] . '%\' OR address LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['status'])) {
        $condition .= ' AND status = ' . $_GPC['status'];
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    load()->model('mc');
    $fans = array();
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
            $fans = mc_fansinfo($data[$k]['openid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = tomedia($fans['avatar']);
            $data[$k]['bindtime'] = date('Y-m-d', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'address' => '房产', 'realname' => '姓名', 'mobile' => '手机号码', 'bankcard' => '第三方验证', 'remark' => '备注', 'bindtime' => '绑定时间');
            export_csv($data, $filter, '微信绑定');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('wlist');
} elseif ($operation == 'delete') {
    $current = '删除绑定';
    $id = intval($_GPC['id']);
    $result = pdo_update('rhinfo_zyxq_member', array('deleted' => 1), array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'enter') {
    $current = '登记信息';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $data = array('buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_room', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        if ($result) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $item['title'] = $building . '-' . $unit . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    include $this->mywtpl('enter');
} elseif ($operation == 'building_member') {
    $current = '登记成员';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $onames = $_GPC['oname'];
        $omobiles = $_GPC['omobile'];
        $otypes = $_GPC['otype'];
        $subids = $_GPC['subid'];
        $k = 0;
        while (!($k >= count($omobiles))) {
            if (!(empty($omobiles[$k]) || empty($onames[$k]))) {
                $data = array('weid' => $mywe['weid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id'], 'ownername' => $onames[$k], 'mobile' => $omobiles[$k], 'otype' => $otypes[$k]);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid=:weid and id=:subid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':subid' => $subids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zyxq_room_mp', $data, array('weid' => $_W['weid'], 'id' => $subids[$k]));
                } else {
                    pdo_insert('rhinfo_zyxq_room_mp', $data);
                }
            }
            ($k += 1) + -1;
        }
        pdo_update('rhinfo_zyxq_room', array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1']), array('weid' => $_W['weid'], 'id' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $item['title'] = $building . '-' . $unit . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid=:weid and rid=:rid and hid=:hid ORDER BY id ASC ';
    $members = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid'], ':hid' => $item['id']));
    include $this->mywtpl('building_member');
} elseif ($operation == 'member') {
    $current = '登记成员';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $onames = $_GPC['oname'];
        $omobiles = $_GPC['omobile'];
        $otypes = $_GPC['otype'];
        $subids = $_GPC['subid'];
        $k = 0;
        while (!($k >= count($omobiles))) {
            if (!(empty($omobiles[$k]) || empty($onames[$k]))) {
                $data = array('weid' => $mywe['weid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id'], 'ownername' => $onames[$k], 'mobile' => $omobiles[$k], 'otype' => $otypes[$k]);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid=:weid and id=:subid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':subid' => $subids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zyxq_room_mp', $data, array('weid' => $_W['weid'], 'id' => $subids[$k]));
                } else {
                    pdo_insert('rhinfo_zyxq_room_mp', $data);
                }
            }
            ($k += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $item['title'] = $building . '-' . $unit . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid=:weid and rid=:rid and hid=:hid ORDER BY id ASC ';
    $members = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid'], ':hid' => $item['id']));
    include $this->mywtpl('member');
} elseif ($operation == 'membertpl') {
    include $this->mywtpl('membertpl');
} elseif ($operation == 'membertpl1') {
    include $this->mywtpl('membertpl1');
} elseif ($operation == 'delmember') {
    $current = '删除成员';
    $id = intval($_GPC['subid']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_room_mp', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'sdelmember') {
    $current = '删除成员';
    $id = intval($_GPC['subid']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_shop_mp', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'smember') {
    $current = '登记成员';
    $rights = $this->myrights(6, $mydo, 'slist');
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $onames = $_GPC['oname'];
        $omobiles = $_GPC['omobile'];
        $otypes = $_GPC['otype'];
        $subids = $_GPC['subid'];
        $k = 0;
        while (!($k >= count($omobiles))) {
            if (!(empty($omobiles[$k]) || empty($onames[$k]))) {
                $data = array('weid' => $mywe['weid'], 'rid' => $item['rid'], 'lid' => $item['lid'], 'sid' => $item['id'], 'ownername' => $onames[$k], 'mobile' => $omobiles[$k], 'otype' => $otypes[$k]);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_shop_mp') . ' where weid=:weid and id=:subid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':subid' => $subids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zyxq_shop_mp', $data, array('weid' => $_W['weid'], 'id' => $subids[$k]));
                } else {
                    pdo_insert('rhinfo_zyxq_shop_mp', $data);
                }
            }
            ($k += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'slist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：商铺业主';
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop_mp') . ' where weid=:weid and rid=:rid and sid=:sid ORDER BY id ASC ';
    $members = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid'], ':sid' => $item['id']));
    include $this->mywtpl('shopmember');
} elseif ($operation == 'shop_enter') {
    $current = '登记信息';
    $rights = $this->myrights(6, $mydo, 'slist');
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $data = array('shopname' => $_GPC['shopname'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'isdiscount' => $_GPC['isdiscount'], 'remark' => $_GPC['remark']);
        $result = pdo_update('rhinfo_zyxq_shop', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        if ($result) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    include $this->mywtpl('shop_enter');
} elseif ($operation == 'parking_enter') {
    $current = '登记信息';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $data = array('buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'carno' => strtoupper($_GPC['carno']), 'paydate' => strtotime($_GPC['paydate']), 'isfree' => $_GPC['isfree'], 'freestart' => strtotime($_GPC['freestart']), 'freeend' => strtotime($_GPC['freeend']), 'remark' => $_GPC['remark']);
        $result = pdo_update('rhinfo_zyxq_parking', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        if ($result) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_parking') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    include $this->mywtpl('parking_enter');
} elseif ($operation == 'building_shop') {
    $current = '登记成员';
    $rights = $this->myrights(6, $mydo, 'slist');
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        $onames = $_GPC['oname'];
        $omobiles = $_GPC['omobile'];
        $otypes = $_GPC['otype'];
        $subids = $_GPC['subid'];
        $k = 0;
        while (!($k >= count($omobiles))) {
            if (!(empty($omobiles[$k]) || empty($onames[$k]))) {
                $data = array('weid' => $mywe['weid'], 'rid' => $item['rid'], 'lid' => $item['lid'], 'sid' => $item['id'], 'ownername' => $onames[$k], 'mobile' => $omobiles[$k], 'otype' => $otypes[$k]);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_shop_mp') . ' where weid=:weid and id=:subid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':subid' => $subids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zyxq_shop_mp', $data, array('weid' => $_W['weid'], 'id' => $subids[$k]));
                } else {
                    pdo_insert('rhinfo_zyxq_shop_mp', $data);
                }
            }
            ($k += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：业主管理';
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop_mp') . ' where weid=:weid and rid=:rid and sid=:sid ORDER BY id ASC ';
    $members = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid'], ':sid' => $item['id']));
    include $this->mywtpl('building_shop');
} elseif ($operation == 'slist') {
    $current = '住户列表';
    $myret = 0;
    $rights = $this->myrights(6, $mydo, 'slist');
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
        $condition .= ' AND (ownername LIKE \'%' . $_GPC['keyword'] . '%\' OR mobile LIKE \'%' . $_GPC['keyword'] . '%\' OR title LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['isfree'])) {
        $condition .= ' AND isfree=' . $_GPC['isfree'];
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($_GPC['export'] == 'export') {
        $limit = '';
    }
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
            $data[$k]['building'] = $building;
            $data[$k]['paydate'] = empty($data[$k]['paydate']) ? '' : date('Y-m-d', $data[$k]['paydate']);
            $data[$k]['mobile1'] = '';
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'region' => '小区或商圈', 'building' => '区域', 'title' => '商铺编号', 'buildarea' => '建筑面积', 'usearea' => '使用面积', 'addarea' => '附加面积', 'ownername' => '业主姓名', 'mobile1' => '手机号码', 'paydate' => '交费截止日期', 'shopname' => '商铺名称', 'remark' => '备注');
            export_excel($data, $filter, '业主信息');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    } elseif ($_GPC['export'] != '') {
        $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct'], 'danger');
    }
    include $this->mywtpl('shoplist');
} elseif ($operation == 'simport') {
    $current = '人员导入';
    if ($_W['isajax']) {
        if (!empty($_FILES['upfile']['name'])) {
            $tmp_file = $_FILES['upfile']['tmp_name'];
            $file_types = explode('.', $_FILES['upfile']['name']);
            $file_type = $file_types[count($file_types) - 1];
            if (strtolower($file_type) != 'csv' && strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
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
            if (strtolower($file_type) == 'csv') {
                $res = import_csv($savePath . $file_name);
            } else {
                $res = import_excel($savePath . $file_name);
            }
            $i = 0;
            $k = 0;
            while (!($k >= count($res))) {
                if ($k != 0) {
                    $id = $res[$k][0];
                    $data['buildarea'] = $res[$k][5];
                    $data['usearea'] = $res[$k][6];
                    $data['addarea'] = $res[$k][7];
                    $data['ownername'] = $res[$k][8];
                    $data['mobile'] = $res[$k][9];
                    $data['paydate'] = strtotime($res[$k][10]);
                    $data['shopname'] = $res[$k][11];
                    $data['remark'] = $res[$k][12];
                    if ($data['ownername'] && $data['mobile']) {
                        $result = pdo_update('rhinfo_zyxq_shop', $data, array('weid' => $mywe['weid'], 'id' => $id));
                        if ($result) {
                            ($i += 1) + -1;
                        }
                    }
                }
                ($k += 1) + -1;
            }
            if ($i > 0) {
                echo 'ok';
            } else {
                echo '导入失败，请检查手机号或姓名是否为空!';
            }
            unlink($savePath . $file_name);
            exit(0);
        } else {
            echo '文件不正确错误!';
            exit(0);
        }
    }
    include $this->mywtpl('shopimport');
} elseif ($operation == 'schange') {
    $current = '变更信息';
    $rights = $this->myrights(6, $mydo, 'slist');
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => '');
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_shop', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $room_chg = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'sid' => $id, 'ownername' => $_GPC['oldownername'], 'mobile' => $_GPC['oldmobile'], 'newownername' => $_GPC['ownername'], 'newmobile' => $_GPC['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_shop_chglog', $room_chg);
        pdo_update('rhinfo_zyxq_member', array('deleted' => 1), array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['lid'], 'tid' => 0, 'hid' => $id));
        pdo_update('rhinfo_zyxq_shop_mp', array('deleted' => 1), array('weid' => $mywe['weid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'sid' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'slist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：商铺业主';
    include $this->mywtpl('shopchange');
} elseif ($operation == 'sedit') {
    $current = '登记信息';
    $rights = $this->myrights(6, $mydo, 'slist');
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('shopname' => $_GPC['shopname'], 'buildarea' => $_GPC['buildarea'], 'usearea' => $_GPC['usearea'], 'addarea' => $_GPC['addarea'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'paydate' => strtotime($_GPC['paydate']), 'billdate' => strtotime($_GPC['billdate']), 'isfree' => $_GPC['isfree'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_shop', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'slist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：商铺业主';
    include $this->mywtpl('shopedit');
} elseif ($operation == 'sdellog') {
    $current = '删除变更记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_shop_chglog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'schangelog') {
    $current = '变更记录';
    $rights = $this->myrights(6, $mydo, 'slist');
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_shop_chglog') . ' where weid=:weid and sid=:sid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':sid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_shop_chglog') . " where weid=:weid and sid=:sid ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $id));
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('shopchangelog');
} elseif ($operation == 'abn') {
    $current = '异常登记';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('isfree' => $_GPC['isfree']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_room', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $room_abn = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $id, 'content' => $_GPC['content'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_room_abnlog', $room_abn);
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $item['title'] = $building . '-' . $unit . '-' . $item['title'];
    $navtitle = $region . '：房屋管理';
    include $this->mywtpl('abn');
} elseif ($operation == 'abnlog') {
    $current = '异常记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_room_abnlog') . ' where weid=:weid and hid=:hid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':hid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room_abnlog') . " where weid=:weid and hid=:hid ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':hid' => $id));
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('abnlog');
} elseif ($operation == 'delabnlog') {
    $current = '删除异常记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_room_abnlog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'sabn') {
    $current = '异常登记';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('isfree' => $_GPC['isfree']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_shop', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $shop_abn = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'lid' => $_GPC['lid'], 'sid' => $id, 'content' => $_GPC['content'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_shop_abnlog', $shop_abn);
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'slist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $item['region'] = $region;
    $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':lid' => $item['lid']));
    $item['title'] = $building . '-' . $item['title'];
    $navtitle = $region . '：商铺管理';
    include $this->mywtpl('sabn');
} elseif ($operation == 'sabnlog') {
    $current = '异常记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_shop_abnlog') . ' where weid=:weid and sid=:sid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':sid' => $id));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_shop_abnlog') . " where weid=:weid and sid=:sid ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $id));
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('sabnlog');
} elseif ($operation == 'delsabnlog') {
    $current = '删除异常记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_shop_abnlog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'userlist') {
    $current = '成员列表';
    $myret = 0;
    $category = !empty($_GPC['category']) ? $_GPC['category'] : 1;
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
        $condition .= ' AND (ownername LIKE \'%' . $_GPC['keyword'] . '%\' OR mobile LIKE \'%' . $_GPC['keyword'] . '%\' OR title LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['isfree'])) {
        $condition .= ' AND isfree=' . $_GPC['isfree'];
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    if ($category == 1) {
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t\t `ID` ASC " . $limit;
            $data = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($data))) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
                $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
                $data[$k]['region'] = $region;
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
                $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
                $data[$k]['building'] = $building;
                $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid and pid=:pid and rid=:rid';
                $unit = pdo_fetchcolumn($sql, array(':id' => $data[$k]['tid'], ':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
                $data[$k]['unit'] = $unit;
                $sql = 'select * from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid';
                $room_mps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid'], ':tid' => $data[$k]['tid'], ':hid' => $data[$k]['id']));
                $data[$k]['room_mps'] = $room_mps;
                ($k += 1) + -1;
            }
            $pager = pagination($total, $pindex, $psize);
        }
        include $this->mywtpl('userlist1');
    }
    if ($category == 2) {
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t\t `ID` ASC " . $limit;
            $data = pdo_fetchall($sql, $params);
            $k = 0;
            while (!($k >= count($data))) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
                $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
                $data[$k]['region'] = $region;
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
                $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
                $data[$k]['building'] = $building;
                $sql = 'select * from ' . tablename('rhinfo_zyxq_shop_mp') . ' where weid=:weid and rid=:rid and lid=:lid and sid=:sid';
                $shop_mps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid'], ':sid' => $data[$k]['id']));
                $data[$k]['shop_mps'] = $shop_mps;
                ($k += 1) + -1;
            }
            $pager = pagination($total, $pindex, $psize);
        }
        include $this->mywtpl('userlist2');
    }
} elseif ($operation == 'status') {
    $current = '参与缴费';
    $id = intval($_GPC['id']);
    $ret = array();
    if ($_GPC['category'] == 1) {
        $item = pdo_get('rhinfo_zyxq_room', array('weid' => $mywe['weid'], 'id' => $id));
        if ($item['isnotice']) {
            $data = array('isnotice' => 0);
            $ret['isnotice'] = '不参与';
            $ret['css'] = 'label label-default';
        } else {
            $data = array('isnotice' => 1);
            $ret['isnotice'] = '参与';
            $ret['css'] = 'label label-warning';
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_room', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    } else {
        $item = pdo_get('rhinfo_zyxq_room_mp', array('weid' => $mywe['weid'], 'id' => $id));
        if ($item['isnotice']) {
            $data = array('isnotice' => 0);
            $ret['isnotice'] = '不参与';
            $ret['css'] = 'label label-default';
        } else {
            $data = array('isnotice' => 1);
            $ret['isnotice'] = '参与';
            $ret['css'] = 'label label-warning';
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_room_mp', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    }
    if (!empty($result)) {
        $ret['status'] = 1;
    } else {
        $ret['status'] = 0;
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(json_encode($ret));
} elseif ($operation == 'sstatus') {
    $current = '参与状态';
    $id = intval($_GPC['id']);
    $ret = array();
    if ($_GPC['category'] == 1) {
        $item = pdo_get('rhinfo_zyxq_shop', array('weid' => $mywe['weid'], 'id' => $id));
        if ($item['isnotice']) {
            $data = array('isnotice' => 0);
            $ret['isnotice'] = '不参与';
            $ret['css'] = 'label label-default';
        } else {
            $data = array('isnotice' => 1);
            $ret['isnotice'] = '参与';
            $ret['css'] = 'label label-warning';
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_shop', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    } else {
        $item = pdo_get('rhinfo_zyxq_shop_mp', array('weid' => $mywe['weid'], 'id' => $id));
        if ($item['isnotice']) {
            $data = array('isnotice' => 0);
            $ret['isnotice'] = '不参与';
            $ret['css'] = 'label label-default';
        } else {
            $data = array('isnotice' => 1);
            $ret['isnotice'] = '参与';
            $ret['css'] = 'label label-warning';
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_shop_mp', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    }
    if (!empty($result)) {
        $ret['status'] = 1;
    } else {
        $ret['status'] = 0;
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(json_encode($ret));
} elseif ($operation == 'rstatus') {
    $current = '参与状态';
    $id = intval($_GPC['id']);
    $ret = array();
    $data = array('isfree' => 0);
    $ret['isfree'] = '收费 ';
    $ret['css'] = 'label label-success';
    $glue = 'AND';
    $result = pdo_update('rhinfo_zyxq_room', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        $ret['status'] = 1;
    } else {
        $ret['status'] = 0;
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(json_encode($ret));
} elseif ($operation == 'shstatus') {
    $current = '参与状态';
    $id = intval($_GPC['id']);
    $ret = array();
    $data = array('isfree' => 0);
    $ret['isfree'] = '收费 ';
    $ret['css'] = 'label label-success';
    $glue = 'AND';
    $result = pdo_update('rhinfo_zyxq_shop', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        $ret['status'] = 1;
    } else {
        $ret['status'] = 0;
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(json_encode($ret));
} elseif ($operation == 'bindaudit') {
    $current = '审核绑定';
    $id = intval($_GPC['id']);
    $data = array('status' => $_GPC['status'], 'audituid' => $mywe['uid'], 'auditopenid' => 0);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if ($result) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and id = :id';
        $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $member['rid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id = :hid';
        $room = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $member['pid'], ':rid' => $member['rid'], ':bid' => $member['bid'], ':tid' => $member['tid'], ':hid' => $member['hid']));
        if (empty($room['ownername']) && $member['otype'] == 0) {
            pdo_update('rhinfo_zyxq_room', array('ownername' => $member['realname'], 'mobile' => $member['mobile']), array('weid' => $mywe['weid'], 'pid' => $member['pid'], 'rid' => $member['rid'], 'bid' => $member['bid'], 'tid' => $member['tid'], 'id' => $member['hid']));
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and (openid=:openid or uid=:uid) and hid=:hid and deleted=1';
        $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $member['pid'], ':rid' => $member['rid'], ':bid' => $member['bid'], ':tid' => $member['tid'], ':openid' => $member['openid'], ':uid' => $member['uid'], ':hid' => $member['hid']));
        if ($region['bindcredit'] > 0 && $_GPC['status'] == 0 && $total == 0) {
            load()->model('mc');
            $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
            $crediturl = $this->my_mobileurl($crediturl);
            mc_credit_update($member['uid'], 'credit1', $region['bindcredit'], array(0, '绑定房产,赠送' . $region['bindcredit'] . '积分', 'rhinfo_zyxq'));
            mc_notice_credit1($member['openid'], $member['uid'], $region['bindcredit'], '绑定房产,赠送' . $region['bindcredit'] . '积分', $crediturl, '谢谢支持，点击查看详情');
        }
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        if ($region['bindstrategyid'] > 0 && $_GPC['status'] == 0 && $total == 0) {
            $redpacket = $this->send_redpacket($region['bindstrategyid'], $member['openid'], 1);
            if (!is_error($redpacket)) {
            }
            $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_share') . ' where (to=:to or touid=:uid) and weid=:weid and status=1';
            $redshare = pdo_fetch($sql, array(':to' => $member['openid'], ':uid' => $member['uid'], ':weid' => $mywe['weid']));
            if (!empty($redshare)) {
                $shareres = $this->send_redpacket($redshare['redid'], $redshare['from'], 0);
                if (!is_error($shareres)) {
                    $postdata = array('first' => array('value' => '有人通过您的邀请，并成功绑定房产'), 'keyword1' => array('value' => $member['realname'], 'color' => $topcolor), 'keyword2' => array('value' => $member['address'], 'color' => $textcolor), 'keyword3' => array('value' => date('Y-m-d H:i', $member['ctime']), 'color' => $textcolor), 'remark' => array('value' => '恭喜您获得一个答谢红包，点击领取吧'));
                    if (!empty($this->syscfg['redpackettplid'])) {
                        $this->send_mysendtplnotice($redshare['from'], $this->syscfg['redpackettplid'], $postdata, $this->my_mobileurl($shareres['url']), $topcolor);
                    }
                }
            }
        }
        if ($_GPC['status'] == 0) {
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '绑定审核通过', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'remark' => array('value' => '恭喜您，您绑定的房产已经审核通过!'));
            $url = $this->createMobileurl('member', array('op' => 'myhouse'));
            $url = $this->my_mobileurl($url);
            $url = !empty($redpacket['url']) ? $this->my_mobileurl($redpacket['url']) : $url;
            if (!empty($this->syscfg['tplid1'])) {
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            $car_data = array('weid' => $mywe['weid'], 'pid' => $region['pid'], 'rid' => $region['id'], 'title' => $member['carno'], 'carno' => $member['carno'], 'ownername' => $member['realname'], 'mobile' => $member['mobile'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $mycar_data = array('weid' => $_W['uniacid'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'carno' => $member['carno'], 'ctime' => TIMESTAMP);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid=:weid and pid=:pid and rid=:rid and carno=:carno and deleted=0';
            $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $region['pid'], ':rid' => $region['id'], ':carno' => $member['carno']));
            if (!($total > 0)) {
                pdo_insert('rhinfo_zyxq_car', $car_data);
            }
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and (openid=:openid or uid=:uid) and carno=:carno';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $member['openid'], ':uid' => $member['uid'], ':carno' => $member['carno']));
            if (!($total > 0)) {
                pdo_insert('rhinfo_zyxq_mycar', $mycar_data);
            }
        }
        echo 'ok';
    } else {
        echo '审核失败!';
    }
    exit(0);
} elseif ($operation == 'reason') {
    $current = '审核绑定';
    $id = intval($_GPC['id']);
    $data = array('reason' => $_GPC['reason'], 'audituid' => $mywe['uid'], 'auditopenid' => 0);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if ($result) {
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $member = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and id=:id', array(':weid' => $mywe['weid'], ':id' => $id));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $member['rid']));
        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '绑定审核不通过', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'remark' => array('value' => '非常抱歉，您绑定的房产审核不通过，原因：' . $_GPC['reason']));
        $url = '';
        if (!empty($this->syscfg['tplid1'])) {
            $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
        }
        echo 'ok';
    } else {
        echo '审核失败!';
    }
    exit(0);
} elseif ($operation == 'exportroom') {
    $current = '导出业主信息';
    if ($_W['ispost']) {
        $condition .= ' and pid = :pid and rid=:rid and bid in(' . $_GPC['bids'] . ')';
        $params[':pid'] = $_GPC['pid'];
        $params[':rid'] = $_GPC['rid'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where ' . $condition . ' order by rid,bid,tid,floor,title*1';
        $data = pdo_fetchall($sql, $params);
        if (empty($data)) {
            $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct'], 'danger');
        }
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid']));
            $data[$k]['building'] = $building;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
            $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':bid' => $data[$k]['bid'], ':tid' => $data[$k]['tid']));
            $data[$k]['unit'] = $unit;
            $data[$k]['paydate'] = empty($data[$k]['paydate']) ? '' : date('Y-m-d', $data[$k]['paydate']);
            $data[$k]['mobile1'] = '';
            ($k += 1) + -1;
        }
        $filter = array('id' => 'ID', 'region' => '小区或商圈', 'building' => '楼栋', 'unit' => '单元', 'title' => '房屋', 'buildarea' => '建筑面积', 'usearea' => '使用面积', 'addarea' => '附加面积', 'ownername' => '业主姓名', 'mobile1' => '手机号码', 'paydate' => '交费截止日期', 'remark' => '备注');
        if ($_GPC['filetype'] == '2') {
            export_csv($data, $filter, $_GPC['title']);
        } else {
            export_excel($data, $filter, $_GPC['title']);
        }
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and pid=:pid and rid=:rid ORDER BY title,id ASC ';
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('exportroom');
} elseif ($operation == 'exportshop') {
    $current = '导出业主信息';
    if ($_W['ispost']) {
        $condition .= ' and pid = :pid and rid=:rid ';
        $params[':pid'] = $_GPC['pid'];
        $params[':rid'] = $_GPC['rid'];
        $sql = 'select * from ' . tablename('rhinfo_zyxq_shop') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC ";
        $data = pdo_fetchall($sql, $params);
        if (empty($data)) {
            $this->mywebmsg('错误', '没有可导出数据', $this->createWeburl($mydo, array('op' => 'slist')) . $mywe['direct'], 'danger');
        }
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid']));
            $data[$k]['region'] = $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and id = :lid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':lid' => $data[$k]['lid']));
            $data[$k]['building'] = $building;
            $data[$k]['paydate'] = empty($data[$k]['paydate']) ? '' : date('Y-m-d', $data[$k]['paydate']);
            $data[$k]['mobile1'] = '';
            ($k += 1) + -1;
        }
        $filter = array('id' => 'ID', 'region' => '小区或商圈', 'building' => '区域', 'title' => '商铺编号', 'buildarea' => '建筑面积', 'usearea' => '使用面积', 'addarea' => '附加面积', 'ownername' => '业主姓名', 'mobile1' => '手机号码', 'paydate' => '交费截止日期', 'shopname' => '商铺名称', 'remark' => '备注');
        if ($_GPC['filetype'] == '2') {
            export_csv($data, $filter, $_GPC['title']);
        } else {
            export_excel($data, $filter, $_GPC['title']);
        }
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('exportshop');
} elseif ($operation == 'doordate') {
    $id = $_GPC['id'];
    $days = floor((strtotime($_GPC['enddate']) - TIMESTAMP) / 86400);
    if (!($days > 0)) {
        echo '日期输入有误!';
        exit(0);
    }
    $data = array('doortime' => strtotime($_GPC['enddate']));
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if ($result) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and id = :id';
        $member = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $member['rid']));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '门禁延期成功', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'remark' => array('value' => '恭喜您，您的门禁已经延期!'));
        $url = $this->createMobileurl('opendoor', array('op' => 'index'));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid1'])) {
            $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
        }
        echo 'ok';
    } else {
        echo '延期失败!';
    }
    exit(0);
} elseif ($operation == 'fans') {
    $navtitle = '会员管理';
    $current = '会员列表';
    $myret = 0;
    $rights = $this->myrights(6, $mydo, 'fans');
    $condition = ' uniacid = :weid';
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (realname LIKE \'%' . $_GPC['keyword'] . '%\'' . ' OR nickname LIKE \'%' . $_GPC['keyword'] . '%\'' . ' OR mobile LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['groupid'])) {
        $condition .= ' and groupid=:groupid ';
        $params[':groupid'] = $_GPC['groupid'];
    }
    if (!empty($_GPC['startdate'])) {
        $starttime = strtotime($_GPC['startdate']);
        $condition .= ' and createtime>=' . $starttime;
    }
    if (!empty($_GPC['enddate'])) {
        $endtime = strtotime($_GPC['enddate']);
        $condition .= ' and createtime<=' . strtotime('+1 days', $endtime);
    }
    if (!empty($_GPC['follow'])) {
        $_GPC['follow'] = $_GPC['follow'] == 2 ? 0 : $_GPC['follow'];
        $condition .= ' and uid in (select uid from' . tablename('mc_mapping_fans') . ' where uniacid=:weid and follow=:follow)';
        $params[':follow'] = $_GPC['follow'];
    }
    $groups = pdo_getall('mc_groups', array('uniacid' => $mywe['weid']));
    $sql = 'SELECT COUNT(*) FROM ' . tablename('mc_members') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('mc_members') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `UID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('mc_groups') . ' where groupid = :groupid and uniacid = :uniacid';
            $group = pdo_fetchcolumn($sql, array(':groupid' => $data[$k]['groupid'], ':uniacid' => $mywe['weid']));
            $data[$k]['group'] = empty($group) ? '默认会员组' : $group;
            $mc_mapping = pdo_get('mc_mapping_fans', array('uniacid' => $mywe['weid'], 'uid' => $data[$k]['uid']), array('follow'));
            $data[$k]['follow'] = $mc_mapping['follow'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl();
} elseif ($operation == 'chargingcard') {
    if ($_W['ispost']) {
        $cardids = $_GPC['cardid'];
        $cardnos = $_GPC['cardno'];
        $statuss = $_GPC['status'];
        $k = 0;
        while (!($k >= count($cardnos))) {
            if (!empty($cardnos[$k])) {
                if (empty($cardids[$k])) {
                    pdo_insert('rhinfo_zycj_charging_vipcard', array('weid' => $mywe['weid'], 'uid' => $_GPC['uid'], 'cardno' => $cardnos[$k], 'status' => $statuss[$k], 'ctime' => TIMESTAMP));
                } else {
                    pdo_update('rhinfo_zycj_charging_vipcard', array('cardno' => $cardnos[$k], 'status' => $statuss[$k]), array('weid' => $mywe['weid'], 'id' => $cardids[$k]));
                }
            }
            ($k += 1) + -1;
        }
    }
    $chargingcards = pdo_getall('rhinfo_zycj_charging_vipcard', array('weid' => $mywe['weid'], 'uid' => $_GPC['uid']));
    include $this->mywtpl();
} elseif ($operation == 'delchargcard') {
    $current = '删除充电卡';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zycj_charging_vipcard', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'credit') {
    $current = '变更积分余额';
    $uid = intval($_GPC['uid']);
    load()->model('mc');
    $member = pdo_get('mc_members', array('uid' => $uid, 'uniacid' => $mywe['weid']), array('credit1', 'credit2'));
    if (!empty($_GPC['credit1']) && !($_GPC['credit1'] >= 0)) {
        if (!($member['credit1'] >= $_GPC['credit1'])) {
            echo '余额不足';
            exit(0);
        }
    }
    if (!empty($_GPC['credit2']) && !($_GPC['credit2'] >= 0)) {
        if (!($member['credit2'] >= $_GPC['credit2'])) {
            echo '积分不足';
            exit(0);
        }
    }
    if (empty($_GPC['credit1']) && empty($_GPC['credit2'])) {
        echo '请输入变更值';
        exit(0);
    }
    $openid = mc_uid2openid($uid);
    $url = $this->createMobileurl('member', array('op' => 'index'));
    $url = str_replace('addons/rhinfo_zyxq/', '', $this->syscfg['siteurl']) . 'app' . substr($url, 1, strlen($url));
    if (!empty($_GPC['credit1'])) {
        mc_credit_update($uid, 'credit1', $_GPC['credit1'], array(0, '后台充值', 'rhinfo_zyxq'));
        mc_notice_credit1($openid, $uid, $_GPC['credit1'], '后台充值', $url, '谢谢支持，点击查看详情');
    }
    if (!empty($_GPC['credit2'])) {
        mc_credit_update($uid, 'credit2', $_GPC['credit2'], array(0, '后台充值', 'rhinfo_zyxq'));
        mc_notice_recharge($openid, $uid, $_GPC['credit2'], $url, '谢谢支持，点击查看详情');
    }
    echo 'ok';
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'uid=' . $uid);
    exit(0);
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'devpatrol';
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '安防管理';
$checkcycle = array('1' => '按天', '2' => '按星期', '3' => '按月', '4' => '按年');
if ($operation == 'list') {
    $current = '巡检计划';
    $rights = $this->myrights(9, $mydo, 'list');
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
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_devpatrol_task') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_task') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '启用';
            } else {
                $data[$k]['statustxt'] = '禁用';
            }
            $data[$k]['url'] = $this->my_mobileurl(mymurl('manager/devtask', array('id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增巡检计划';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $itemstr = explode(',', $_GPC['devitems']);
        $itemstr = is_array($itemstr) ? iserializer($itemstr) : iserializer(array());
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'cycle' => $_GPC['cycle'], 'day' => $_GPC['day'], 'week' => $_GPC['week'], 'month' => $_GPC['month'], 'year' => $_GPC['year'], 'itemstr' => $itemstr, 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'startdate' => strtotime($_GPC['startdate']), 'remark' => $_GPC['remark'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_devpatrol_task', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mydevitem = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where weid = :weid and pid = :pid and rid = :rid';
            $devitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mydevitem[$regions[$m]['id']] = $devitems;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑巡检计划';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $itemstr = explode(',', $_GPC['devitems']);
        $itemstr = is_array($itemstr) ? iserializer($itemstr) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'cycle' => $_GPC['cycle'], 'day' => $_GPC['day'], 'week' => $_GPC['week'], 'month' => $_GPC['month'], 'year' => $_GPC['year'], 'itemstr' => $itemstr, 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'startdate' => strtotime($_GPC['startdate']), 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_devpatrol_task', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mydevitem = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_task') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where weid = :weid and pid = :pid and rid = :rid';
            $devitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mydevitem[$regions[$m]['id']] = $devitems;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where weid = :weid and rid=:rid';
    $edevitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
    $count = count($edevitems);
    $itemstrarr = iunserializer($item['itemstr']);
    $itemname = array();
    $k = 0;
    while (!($k >= $count)) {
        if (in_array($edevitems[$k]['id'], $itemstrarr)) {
            $itemname[] = $edevitems[$k]['title'];
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除巡更计划';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_devpatrol_task', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'map') {
    $current = '巡检地图';
    $myret = 1;
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolline') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetch($sql, array(':id' => $item['rid'], ':weid' => $mywe['weid']));
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid=:weid and id in(' . $item['positions'] . ')';
    $data = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    include $this->mywtpl('map');
} elseif ($operation == 'category') {
    $current = '巡检类别';
    $myret = 0;
    $rights = $this->myrights(9, $mydo, 'category');
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
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['enabled'] == 1) {
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $current = '新增分类';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'remark' => $_GPC['remark'], 'enabled' => $_GPC['enabled']);
        pdo_insert('rhinfo_zyxq_devpatrol_category', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'category')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'cateedit') {
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'remark' => $_GPC['remark'], 'enabled' => $_GPC['enabled']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_devpatrol_category', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'category')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('catepost');
} elseif ($operation == 'catedelete') {
    $current = '删除分类';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    pdo_delete('rhinfo_zyxq_devpatrol_cateitem', array('cateid' => $id, 'weid' => $mywe['weid']), 'AND');
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_devpatrol_category', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'itemadd') {
    $current = '巡检标准';
    $cateid = $_GPC['id'];
    if ($_W['isajax']) {
        $titles = $_GPC['title'];
        $descs = $_GPC['desc'];
        $displayorders = $_GPC['displayorder'];
        $itemtypes = $_GPC['itemtype'];
        $cateids = $_GPC['cateid'];
        $values = $_GPC['value'];
        $k = 0;
        while (!($k >= count($titles))) {
            if (!empty($titles[$k])) {
                if (empty($descs[$k])) {
                    echo '标准不可为空';
                    exit(0);
                }
                if ($itemtypes[$k] == 'radio' || $itemtypes[$k] == 'checkbox' || $itemtypes[$k] == 'select') {
                    if (empty($values[$k])) {
                        echo '单选、多选、下拉框值不可为空';
                        exit(0);
                    }
                }
            }
            ($k += 1) + -1;
        }
        $k = 0;
        while (!($k >= count($titles))) {
            if (!empty($titles[$k])) {
                $data = array('weid' => $mywe['weid'], 'cateid' => $cateid, 'title' => $titles[$k], 'desc' => $descs[$k], 'displayorder' => $displayorders[$k], 'itemtype' => $itemtypes[$k], 'value' => $values[$k]);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_devpatrol_cateitem') . ' where weid=:weid and id=:cateid ';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['weid'], ':cateid' => $cateids[$k]));
                if ($total > 0) {
                    pdo_update('rhinfo_zyxq_devpatrol_cateitem', $data, array('weid' => $_W['weid'], 'id' => $cateids[$k]));
                } else {
                    pdo_insert('rhinfo_zyxq_devpatrol_cateitem', $data);
                }
            }
            ($k += 1) + -1;
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_devpatrol_cateitem', $data, array('id' => $lotid, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $cateid, ':weid' => $mywe['weid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
    $region = pdo_fetchcolumn($sql, array(':id' => $item['rid'], ':weid' => $mywe['weid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_cateitem') . ' where weid=:weid and cateid=:cateid ORDER BY displayorder,id ASC ';
    $cateitems = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cateid' => $cateid));
    include $this->mywtpl('itemadd');
} elseif ($operation == 'itemtpl') {
    include $this->mywtpl('itemtpl');
} elseif ($operation == 'itemdel') {
    $current = '删除巡检标准';
    $id = intval($_GPC['cateid']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_devpatrol_cateitem', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'device') {
    $current = '巡检项目';
    $myret = 0;
    $rights = $this->myrights(9, $mydo, 'device');
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
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '启用';
            } else {
                $data[$k]['statustxt'] = '禁用';
            }
            $sql = 'select title from ' . tablename('rhinfo_zyxq_devsupplier') . ' where id = :id and weid = :weid';
            $devsupplier = pdo_fetchcolumn($sql, array(':id' => $data[$k]['suppid'], ':weid' => $mywe['weid']));
            $data[$k]['devsupplier'] = $devsupplier;
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('manage', array('op' => 'secdev', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('devlist');
} elseif ($operation == 'devadd') {
    $current = '新增巡检项目';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $itemstr = explode(',', $_GPC['positions']);
        $itemstr = is_array($itemstr) ? iserializer($itemstr) : iserializer(array());
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'suppid' => $_GPC['suppid'], 'cateid' => $_GPC['category'], 'title' => $_GPC['title'], 'spec' => $_GPC['spec'], 'brand' => $_GPC['brand'], 'itemstr' => $itemstr, 'images' => $images, 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'address' => $_GPC['address'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_devpatrol_device', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'device')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devsupplier') . ' where weid = :weid and status=1 ';
    $suppliers = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mycategory = array();
    $mystandard = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where weid = :weid and pid = :pid and rid = :rid and enabled=1';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            $n = 0;
            while (!($n >= count($categorys))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_cateitem') . ' where weid = :weid and cateid = :cateid';
                $standards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cateid' => $categorys[$n]['id']));
                $mystandard[$categorys[$n]['id']] = $standards;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('devpost');
} elseif ($operation == 'devedit') {
    $current = '编辑巡检项目';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $itemstr = explode(',', $_GPC['positions']);
        $itemstr = is_array($itemstr) ? iserializer($itemstr) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'suppid' => $_GPC['suppid'], 'cateid' => $_GPC['category'], 'title' => $_GPC['title'], 'spec' => $_GPC['spec'], 'brand' => $_GPC['brand'], 'itemstr' => $itemstr, 'images' => $images, 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'address' => $_GPC['address'], 'remark' => $_GPC['remark'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_devpatrol_device', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'device')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devsupplier') . ' where weid = :weid and status=1 ';
    $suppliers = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mycategory = array();
    $mystandard = array();
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where weid = :weid and pid = :pid and rid = :rid and enabled=1';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            $n = 0;
            while (!($n >= count($categorys))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_cateitem') . ' where weid = :weid and cateid = :cateid';
                $standards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cateid' => $categorys[$n]['id']));
                $mystandard[$categorys[$n]['id']] = $standards;
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_category') . ' where weid = :weid and pid = :pid and rid = :rid and enabled=1';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_devpatrol_cateitem') . ' where weid = :weid and cateid=:cateid';
    $estandards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':cateid' => $item['cateid']));
    $count = count($estandards);
    $itemstrarr = iunserializer($item['itemstr']);
    $itemname = array();
    $k = 0;
    while (!($k >= $count)) {
        if (in_array($estandards[$k]['id'], $itemstrarr)) {
            $itemname[] = $estandards[$k]['title'];
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('devpost');
} elseif ($operation == 'devdel') {
    $current = '删除巡检项目';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_devpatrol_device', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'supplier') {
    $current = '维保厂商';
    $myret = 1;
    $rights = $this->myrights(9, $mydo, 'device');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_devsupplier') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_devsupplier') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '启用';
            } else {
                $data[$k]['statustxt'] = '禁用';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('supplist');
} elseif ($operation == 'suppadd') {
    $current = '新增厂商';
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_devsupplier', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'supplier')) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('supppost');
} elseif ($operation == 'suppedit') {
    $current = '编辑厂商';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_devsupplier', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'supplier')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devsupplier') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    include $this->mywtpl('supppost');
} elseif ($operation == 'suppdel') {
    $current = '删除维保厂商';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_devsupplier', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'devlog') {
    $current = '巡检记录';
    $myret = 1;
    $rights = $this->myrights(9, $mydo, 'device');
    $id = intval($_GPC['id']);
    $condition .= ' and deviceid = :deviceid';
    $params[':deviceid'] = $id;
    $ctime = $_GPC['ctime'];
    if (!empty($ctime)) {
        $starttime = strtotime($ctime['start']);
        $endtime = strtotime($ctime['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    if (!empty($_GPC['keyword'])) {
        $sql = 'select uid from ' . tablename('mc_members') . ' where realname like :keyword or nickname like :keyword or mobile like :keyword';
        $uid = pdo_fetchcolumn($sql, array(':keyword' => '%' . $_GPC['keyword'] . '%'));
        if (!empty($uid)) {
            $condition .= ' and cuid=:uid ';
            $params[':uid'] = $uid;
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_device') . ' where weid=:weid and id=:id';
    $device = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':id' => $id));
    $current = $device['title'] . $current;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_devpatrol_content') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_content') . ' where ' . $condition . ' order by ctime desc ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $fans = mc_fansinfo($data[$k]['cuid'], 0, $mywe['weid']);
            $data[$k]['realname'] = $fans['nickname'];
            $data[$k]['avatar'] = $fans['avatar'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl();
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $result = pdo_delete('rhinfo_zyxq_devpatrol_content', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
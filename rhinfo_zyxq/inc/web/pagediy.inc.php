<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'pagediy';
$tablename = 'rhinfo_zyxq_pagediy';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '业主中心';
if ($operation == 'list') {
    $current = '链接列表';
    $myret = 0;
    $rights = $this->myrights(8, $mydo, 'list');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            $sql = 'select title from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid=:rid and id=:cateid';
            $category = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':cateid' => $data[$k]['cateid']));
            $data[$k]['category'] = empty($category) ? '无分类' : $category;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增链接';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cateid' => $_GPC['cid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'icon' => $_GPC['icon'], 'url' => $_GPC['link'], 'color' => $_GPC['color'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['cate'] == 1) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid=:rid';
        $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑链接';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cateid' => $_GPC['cid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'icon' => $_GPC['icon'], 'url' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'color' => $_GPC['color'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_article_category') . ' where weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid = :rid';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if ($item['rid'] == 0) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where weid = :weid and pid = :pid and rid=:rid';
        $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除链接';
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
} elseif ($operation == 'category') {
    $current = '链接分类';
    $myret = 1;
    $rights = $this->myrights(8, $mydo, 'list');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $current = '新增分类';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'style' => $_GPC['style'], 'title' => $_GPC['title'], 'smalltitle' => $_GPC['smalltitle'], 'displayorder' => $_GPC['displayorder'], 'status' => $_GPC['status'], 'icon' => $_GPC['icon'], 'url' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'color' => $_GPC['color'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_pagediy_cate', $data);
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
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'style' => $_GPC['style'], 'smalltitle' => $_GPC['smalltitle'], 'displayorder' => $_GPC['displayorder'], 'status' => $_GPC['status'], 'icon' => $_GPC['icon'], 'color' => $_GPC['color'], 'url' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_pagediy_cate', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_pagediy_cate') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    if ($item['rid'] == 0) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'catedelete') {
    $current = '删除动态分类';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_pagediy_cate', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
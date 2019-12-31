<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'article';
$tablename = 'rhinfo_zyxq_article';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '动态管理';
if ($operation == 'list') {
    $current = '资讯列表';
    $myret = 0;
    $rights = $this->myrights(12, $mydo, 'list');
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
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = !empty($region) ? $region : '系统默认';
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            $sql = 'select title from ' . tablename('rhinfo_zyxq_article_category') . ' where weid = :weid and pid = :pid and rid=:rid and id=:cid';
            $category = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':cid' => $data[$k]['cid']));
            $data[$k]['category'] = $category;
            $qrcode = $this->my_mobileurl($this->createMobileUrl('article', array('op' => 'detail', 'id' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增资讯';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cid' => $_GPC['cid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'images' => $images, 'content' => htmlspecialchars_decode($_GPC['content']), 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
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
    if ($cate == 1 || empty($cate)) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_article_category') . ' where weid = :weid and pid = :pid and rid=:rid';
        $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
    } elseif ($cate == 2) {
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
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑资讯';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cid' => $_GPC['cid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'images' => $images, 'content' => htmlspecialchars_decode($_GPC['content']), 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status']);
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
    $item['images'] = iunserializer($item['images']);
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_article_category') . ' where weid = :weid and pid = :pid and rid = :rid';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if (empty($item['rid'])) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除资讯';
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
    $current = '资讯分类';
    $myret = 0;
    $rights = $this->myrights(12, $mydo, 'category');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_article_category') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_article_category') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = !empty($region) ? $region : '系统默认';
            if ($data[$k]['enabled'] == 1) {
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            $qrcode = $this->my_mobileurl($this->createMobileUrl('article', array('op' => 'index', 'cateid' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $current = '新增资讯分类';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        pdo_insert('rhinfo_zyxq_article_category', $data);
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
    $current = '编辑资讯分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_article_category', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_article_category') . ' where id = :id and weid = :weid';
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
    $current = '删除资讯分类';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_article_category', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
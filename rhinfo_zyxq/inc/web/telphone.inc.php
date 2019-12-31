<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'telphone';
$config = $this->module['config'];
$conifg['qq_lbskey'] = !empty($config['qq_lbskey']) ? $config['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zyxq_telphone';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '便民电话';
if ($operation == 'list') {
    $current = '电话列表';
    $myret = 0;
    $rights = $this->myrights(8, $mydo, 'list');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and id=:cateid';
            $category = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':cateid' => $data[$k]['cateid']));
            if (!empty($category['parentid'])) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and id=:parentid';
                $parenttitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':parentid' => $category['parentid']));
                $data[$k]['category'] = $parenttitle . '-' . $category['title'];
            } else {
                $data[$k]['category'] = $category['title'];
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增电话';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'cateid' => $_GPC['cateid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'status' => $_GPC['status'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $category = array();
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and parentid=0';
    $pcategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $k = 0;
    while (!($k >= count($pcategorys))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and parentid=:parentid';
        $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $pcategorys[$k]['id']));
        $category[$pcategorys[$k]['id']] = $categorys;
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑电话';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('cateid' => $_GPC['cateid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'status' => $_GPC['status'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $pcategory = array();
    $category = array();
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select parentid from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and id=:cateid';
    $item['parentcateid'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cateid' => $item['cateid']));
    $category = array();
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and parentid=0';
    $pcategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    $k = 0;
    while (!($k >= count($pcategorys))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and parentid=:parentid';
        $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $pcategorys[$k]['id']));
        $category[$pcategorys[$k]['id']] = $categorys;
        ($k += 1) + -1;
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_telcate') . ' where weid = :weid and parentid=:parentid';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $item['parentcateid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除电话';
    $id = intval($_GPC['id']);
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'catelist') {
    $current = '电话分类';
    $myret = 1;
    $rights = $this->myrights(8, $mydo, 'list');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_telcate') . ' where parentid=0 and ' . $condition;
    $ptotal = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_telcate') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_telcate') . ' where parentid=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $children = array();
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == 1) {
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl($mydo, array('op' => 'telphone', 'cateid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_telcate') . " where parentid=:parentid and weid=:weid ORDER BY\r\n\t\t\t\t\t `ID` ASC ";
            $childdata = pdo_fetchall($sql, array(':parentid' => $data[$k]['id'], ':weid' => $mywe['weid']));
            $m = 0;
            while (!($m >= count($childdata))) {
                if ($childdata[$m]['enabled'] == 1) {
                    $childdata[$m]['status'] = '显示';
                } else {
                    $childdata[$m]['status'] = '隐藏';
                }
                $childdata[$m]['url'] = $this->my_mobileurl($this->createMobileUrl($mydo, array('op' => 'telphone', 'cateid' => $childdata[$m]['id'])));
                $childdata[$m]['qrcode'] = $this->createqrcode($childdata[$m]['url']);
                ($m += 1) + -1;
            }
            $children[$data[$k]['id']] = $childdata;
            ($k += 1) + -1;
        }
        $pager = pagination($ptotal, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $current = '新增分类';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'parentid' => $_GPC['parentid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled']);
        pdo_insert('rhinfo_zyxq_telcate', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'catelist')) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'cateedit') {
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled']);
        pdo_update('rhinfo_zyxq_telcate', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'catelist')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_telcate') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('catepost');
} elseif ($operation == 'catedelete') {
    $current = '删除电话分类';
    $id = intval($_GPC['id']);
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_telcate') . ' where parentid = :id and weid = :weid';
    $total = pdo_fetchcolumn($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($total > 0) {
        echo '删除失败，存在下级分类!';
        exit(0);
    }
    $result = pdo_delete('rhinfo_zyxq_telcate', array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
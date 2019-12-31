<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'question';
$tablename = 'rhinfo_zyxq_question';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '常见问题';
if ($operation == 'list') {
    $current = '问题列表';
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
            $sql = 'select title from ' . tablename('rhinfo_zyxq_qacate') . ' where weid = :weid and id=:cateid';
            $category = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cateid' => $data[$k]['cateid']));
            $data[$k]['category'] = $category;
            $qrcode = $this->my_mobileurl($this->createMobileUrl($mydo, array('op' => 'detail', 'id' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增问题';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'cateid' => $_GPC['cateid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'keywords' => $_GPC['keywords'], 'content' => htmlspecialchars_decode($_GPC['content']), 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_qacate') . ' where ' . $condition;
    $categorys = pdo_fetchall($sql, $params);
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑问题';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('cateid' => $_GPC['cateid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'keywords' => $_GPC['keywords'], 'content' => htmlspecialchars_decode($_GPC['content']), 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status']);
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_qacate') . ' where ' . $condition;
    $categorys = pdo_fetchall($sql, $params);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除问题';
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
    $current = '问题分类';
    $myret = 1;
    $rights = $this->myrights(8, $mydo, 'list');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_qacate') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_qacate') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == 1) {
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            $qrcode = $this->my_mobileurl($this->createMobileUrl($mydo, array('op' => 'question', 'cate' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $current = '新增分类';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        pdo_insert('rhinfo_zyxq_qacate', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'category')) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'cateedit') {
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_qacate', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'category')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_qacate') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('catepost');
} elseif ($operation == 'catedelete') {
    $current = '删除问题分类';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_qacate', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
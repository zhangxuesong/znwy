<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '系统帮助';
$mydo = 'help';
$tablename = 'rhinfo_zyxq_sechelp';
$condition = ' weid = :weid ';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
if ($operation == 'list') {
    $rights = $this->myrights(1, $mydo, 'list');
    $myret = 0;
    $current = '帮助列表';
    $condition = '';
    if (!empty($_GPC['keyword'])) {
        $condition = ' where title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . $condition . ' ORDER BY `ID` ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_secsys') . ' where id = :sid';
            $data[$k]['secsys'] = pdo_fetchcolumn($sql, array(':sid' => $data[$k]['sid']));
            $sql = 'select title from ' . tablename('rhinfo_zyxq_secprg') . ' where id = :pid ';
            $data[$k]['secprg'] = pdo_fetchcolumn($sql, array(':pid' => $data[$k]['pid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '添加帮助';
    if ($_W['ispost']) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_secprg') . ' where id = :pid ';
        $secprg = pdo_fetch($sql, array(':pid' => $_GPC['pid']));
        $data = array('sid' => $_GPC['sid'], 'pid' => $_GPC['pid'], 'do' => $secprg['do'], 'op' => $secprg['op'], 'title' => $_GPC['title'], 'content' => htmlspecialchars_decode($_GPC['content']), 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_secsys') . ' where isdisplay=1 order by displayorder ';
    $secsys = pdo_fetchall($sql);
    $mysecprg = array();
    $k = 0;
    while (!($k >= count($secsys))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_secprg') . ' where sid = :sid and isdismenu=1 and id not in(select distinct pid from ' . tablename('rhinfo_zyxq_sechelp') . ') order by displayorder';
        $secprg = pdo_fetchall($sql, array(':sid' => $secsys[$k]['id']));
        $mysecprg[$secsys[$k]['id']] = $secprg;
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑帮助';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'content' => htmlspecialchars_decode($_GPC['content']));
        pdo_update($tablename, $data, array('id' => $id));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id';
    $item = pdo_fetch($sql, array(':id' => $id));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_secsys') . ' where id = :sid ';
    $secsys = pdo_fetchall($sql, array(':sid' => $item['sid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_secprg') . ' where id = :pid ';
    $esecprg = pdo_fetchall($sql, array(':pid' => $item['pid']));
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除帮助';
    $id = intval($_GPC['id']);
    $result = pdo_delete($tablename, array('id' => $id));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'check') {
    $sql = 'select count(*) from ' . tablename($tablename) . ' where sid = :sid and pid=:pid';
    $count = pdo_fetchcolumn($sql, array(':sid' => $_GPC['sid'], ':pid' => $_GPC['pid']));
    if ($count > 0) {
        echo '帮助文档已存在';
    } else {
        echo 'ok';
    }
    exit(0);
} elseif ($operation == 'help') {
    $current = '快速帮助';
    $condition = ' do=:do and op=:op ';
    $params = array(':do' => $_GPC['fromdo'], ':op' => $_GPC['fromop']);
    $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition;
    $help = pdo_fetch($sql, $params);
    include $this->mywtpl('help');
}
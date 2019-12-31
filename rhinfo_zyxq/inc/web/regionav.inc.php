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
$navtitle = '菜单导航';
$mydo = 'regionav';
$tablename = 'rhinfo_zyxq_regionav';
$condition = ' weid = :weid and rid = :rid ';
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$params = array(':weid' => $mywe['weid'], ':rid' => $rid);
$rights = $this->myrights(2, $mydo, 'list');
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
$navtitle = $region['title'] . ' > ' . $navtitle;
if ($operation == 'list') {
    $current = '导航列表';
    $myret = 1;
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title*1 ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == '1') {
                $data[$k]['status'] = '启用';
            } else {
                $data[$k]['status'] = '禁用';
            }
            if ($data[$k]['category'] == '1') {
                $data[$k]['category'] = '首页导航';
            } elseif ($data[$k]['category'] == '2') {
                $data[$k]['category'] = '服务导航';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '导航列表';
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title*1 ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == '1') {
                $data[$k]['status'] = '启用';
            } else {
                $data[$k]['status'] = '禁用';
            }
            if ($data[$k]['category'] == '1') {
                $data[$k]['category'] = '首页导航';
            } elseif ($data[$k]['category'] == '2') {
                $data[$k]['category'] = '服务导航';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增导航';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'link' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'bgimage' => $_GPC['bgimage'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑导航';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'category' => $_GPC['category'], 'link' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'bgimage' => $_GPC['bgimage'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'deleteall') {
    $current = '删除全部导航';
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
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    exit(0);
} elseif ($operation == 'status') {
    $current = '状态';
    $id = intval($_GPC['id']);
    $data = array('enabled' => $_GPC['status']);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'rlist') {
    $current = '导航列表';
    $myret = 0;
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where weid=:weid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid']));
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where weid=:weid ORDER BY title*1 ASC ' . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == '1') {
                $data[$k]['status'] = '启用';
            } else {
                $data[$k]['status'] = '禁用';
            }
            if ($data[$k]['category'] == '1') {
                $data[$k]['category'] = '首页导航';
            } elseif ($data[$k]['category'] == '2') {
                $data[$k]['category'] = '服务导航';
            }
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $data[$k]['regionname'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('rlist');
} elseif ($operation == 'radd') {
    $current = '新增导航';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'link' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'bgimage' => $_GPC['bgimage'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'rlist')) . $mywe['direct']);
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
    include $this->mywtpl('rpost');
} elseif ($operation == 'redit') {
    $current = '编辑导航';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'category' => $_GPC['category'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'bgimage' => $_GPC['bgimage'], 'link' => $_GPC['link'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'rlist')) . $mywe['direct']);
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
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    include $this->mywtpl('rpost');
}
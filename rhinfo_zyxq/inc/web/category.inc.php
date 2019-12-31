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
$navtitle = '基础设置';
$mydo = 'category';
$tablename = 'rhinfo_zyxq_category';
$condition = ' weid = :weid and rid = :rid ';
$pindex = max(1, intval($_GPC['page']));
$psize = 50;
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$pid = $_GPC['pid'];
$rid = $_GPC['rid'];
$params = array(':weid' => $mywe['weid'], ':rid' => $rid);
$rights = $this->myrights(2, $mydo, 'list');
$sql = 'select title,category,pid from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
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
    $current = '分类列表';
    $myret = 1;
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    $data = array(array('id' => 1, 'title' => '通知分类'), array('id' => 2, 'title' => '报修分类'), array('id' => 3, 'title' => '投诉建议'), array('id' => 5, 'title' => '内部工单'), array('id' => 4, 'title' => '服务团队'), array('id' => 6, 'title' => '快捷回复'), array('id' => 7, 'title' => '访客原因'));
    $condition .= ' and type = :type';
    $k = 0;
    while (!($k >= count($data))) {
        $params[':type'] = $data[$k]['id'];
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title*1 ASC ';
        $data[$k]['cate'] = pdo_fetchall($sql, $params);
        ($k += 1) + -1;
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增类别';
    $type = $_GPC['type'];
    if ($type == 1) {
        $current = '新增通知类别';
    } elseif ($type == 2) {
        $current = '新增报修类别';
    } elseif ($type == 3) {
        $current = '新增建议类别';
    } elseif ($type == 4) {
        $current = '新增人员类别';
    } elseif ($type == 5) {
        $current = '新增工单类别';
    }
    if ($_W['ispost']) {
        if ($type == 1) {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['type'], 'title' => $_GPC['title'], 'remark' => $_GPC['remark'], 'tplid' => $_GPC['tplid'], 'topcolor' => $_GPC['topcolor'], 'textcolor' => $_GPC['textcolor'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        } elseif ($type == 4) {
            $rights = $_GPC['rights'];
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['type'], 'title' => $_GPC['title'], 'tplid' => $_GPC['tplid'], 'remark' => $_GPC['remark'], 'right1' => $rights[0], 'right2' => $rights[1], 'right3' => $rights[2], 'right4' => $rights[3], 'right5' => $rights[4], 'right6' => $rights[5], 'right7' => $rights[6], 'right8' => $rights[7], 'right9' => $rights[8], 'right10' => $rights[9], 'right11' => $rights[10], 'right12' => $rights[11], 'right13' => $rights[12], 'right14' => $rights[13], 'right15' => $rights[14], 'right16' => $rights[15], 'right17' => $rights[16], 'right18' => $rights[17], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        } else {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['type'], 'title' => $_GPC['title'], 'topcolor' => $_GPC['topcolor'], 'textcolor' => $_GPC['textcolor'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'reporttime' => $_GPC['reporttime'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        }
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑类别';
    $id = intval($_GPC['id']);
    $type = $_GPC['type'];
    if ($type == 1) {
        $current = '新增通知类别';
    } elseif ($type == 2) {
        $current = '新增报修类别';
    } elseif ($type == 3) {
        $current = '新增建议类别';
    } elseif ($type == 4) {
        $current = '新增人员类别';
    } elseif ($type == 5) {
        $current = '新增工单类别';
    }
    if ($_W['ispost']) {
        if ($type == 1) {
            $data = array('title' => $_GPC['title'], 'tplid' => $_GPC['tplid'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'topcolor' => $_GPC['topcolor'], 'remark' => $_GPC['remark']);
        } elseif ($type == 4) {
            $rights = $_GPC['rights'];
            $data = array('title' => $_GPC['title'], 'tplid' => $_GPC['tplid'], 'right1' => $rights[0], 'right2' => $rights[1], 'right3' => $rights[2], 'right4' => $rights[3], 'right5' => $rights[4], 'right6' => $rights[5], 'right7' => $rights[6], 'right8' => $rights[7], 'right9' => $rights[8], 'right10' => $rights[9], 'right11' => $rights[10], 'right12' => $rights[11], 'right13' => $rights[12], 'right14' => $rights[13], 'right15' => $rights[14], 'right16' => $rights[15], 'right17' => $rights[16], 'right18' => $rights[17], 'remark' => $_GPC['remark']);
        } else {
            $data = array('title' => $_GPC['title'], 'topcolor' => $_GPC['topcolor'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'reporttime' => $_GPC['reporttime'], 'remark' => $_GPC['remark']);
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_category', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('category' => $category, 'op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select nickname from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:weid';
    $item['nickname'] = pdo_fetchcolumn($sql, array(':openid' => $item['openid'], ':weid' => $mywe['weid']));
    $type = $item['type'];
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除分类';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'delall') {
    $current = '删除分类';
    $glue = 'AND';
    $result = pdo_delete($tablename, array('pid' => $pid, 'rid' => $rid, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'rid=' . $rid);
    exit(0);
} elseif ($operation == 'guideadd') {
    $current = '新增类别';
    $type = $_GPC['type'];
    if ($type == 1) {
        $current = '新增通知类别';
    } elseif ($type == 2) {
        $current = '新增报修类别';
    } elseif ($type == 3) {
        $current = '新增建议类别';
    } elseif ($type == 4) {
        $current = '新增人员类别';
    } elseif ($type == 5) {
        $current = '新增工单类别';
    }
    if ($_W['isajax']) {
        if ($type == 1) {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['type'], 'title' => $_GPC['title'], 'remark' => $_GPC['remark'], 'tplid' => $_GPC['tplid'], 'topcolor' => $_GPC['topcolor'], 'textcolor' => $_GPC['textcolor'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        } elseif ($type == 4) {
            $rights = $_GPC['rights'];
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['type'], 'title' => $_GPC['title'], 'tplid' => $_GPC['tplid'], 'remark' => $_GPC['remark'], 'right1' => $rights[0], 'right2' => $rights[1], 'right3' => $rights[2], 'right4' => $rights[3], 'right5' => $rights[4], 'right6' => $rights[5], 'right7' => $rights[6], 'right8' => $rights[7], 'right9' => $rights[8], 'right10' => $rights[9], 'right11' => $rights[10], 'right12' => $rights[11], 'right13' => $rights[12], 'right14' => $rights[13], 'right15' => $rights[14], 'right16' => $rights[15], 'right17' => $rights[16], 'right18' => $rights[17], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        } else {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['type'], 'title' => $_GPC['title'], 'topcolor' => $_GPC['topcolor'], 'textcolor' => $_GPC['textcolor'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'reporttime' => $_GPC['reporttime'], 'remark' => $_GPC['remark'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        }
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
        exit(0);
    }
    include $this->mywtpl('guidepost');
} elseif ($operation == 'guideedit') {
    $current = '编辑类别';
    $id = intval($_GPC['id']);
    $type = $_GPC['type'];
    if ($type == 1) {
        $current = '新增通知类别';
    } elseif ($type == 2) {
        $current = '新增报修类别';
    } elseif ($type == 3) {
        $current = '新增建议类别';
    } elseif ($type == 4) {
        $current = '新增人员类别';
    } elseif ($type == 5) {
        $current = '新增工单类别';
    }
    if ($_W['isajax']) {
        if ($type == 1) {
            $data = array('title' => $_GPC['title'], 'tplid' => $_GPC['tplid'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'topcolor' => $_GPC['topcolor'], 'remark' => $_GPC['remark']);
        } elseif ($type == 4) {
            $rights = $_GPC['rights'];
            $data = array('title' => $_GPC['title'], 'tplid' => $_GPC['tplid'], 'right1' => $rights[0], 'right2' => $rights[1], 'right3' => $rights[2], 'right4' => $rights[3], 'right5' => $rights[4], 'right6' => $rights[5], 'right7' => $rights[6], 'right8' => $rights[7], 'right9' => $rights[8], 'right10' => $rights[9], 'right11' => $rights[10], 'right12' => $rights[11], 'right13' => $rights[12], 'right14' => $rights[13], 'right15' => $rights[14], 'right16' => $rights[15], 'right17' => $rights[16], 'right18' => $rights[17], 'remark' => $_GPC['remark']);
        } else {
            $data = array('title' => $_GPC['title'], 'topcolor' => $_GPC['topcolor'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'reporttime' => $_GPC['reporttime'], 'remark' => $_GPC['remark']);
        }
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_category', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        echo 'ok';
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $type = $item['type'];
    include $this->mywtpl('guidepost');
} elseif ($operation == 'reply') {
    if ($_W['isajax']) {
        $id = $_GPC['id'];
        if (!empty($id)) {
            $data = array('title' => $_GPC['reply']);
            $res = pdo_update('rhinfo_zyxq_category', $data, array('weid' => $mywe['weid'], 'id' => $id));
        } else {
            $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'type' => $_GPC['replytype'], 'title' => $_GPC['reply'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
            $res = pdo_insert('rhinfo_zyxq_category', $data);
        }
        if ($res) {
            exit('ok');
        } else {
            exit('操作失败');
        }
    }
    exit('异常操作');
}
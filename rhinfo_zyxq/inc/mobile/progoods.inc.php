<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$mydo = 'progoods';
$curr = 'progoods';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$myurl = $this->createMobileurl($mydo);
$myrid = $_GPC['rid'];
$user = $this->getmanager($_W['member']['uid'], $myrid);
if ($_W['isajax']) {
    if ($user['ismanager'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        if (empty($region)) {
            show_json(0, '主体不存在');
        }
    } else {
        show_json(0, '无权限操作');
    }
} else {
    if ($user['ismanager'] == 1) {
        $myrid = $user['rid'];
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        if (empty($region)) {
            $this->mymsg('error', '温馨提示', '主体不存在.', 'close');
        }
    } else {
        $this->mymsg('error', '温馨提示', '本权限仅针对物业服务人员开放.', 'close');
    }
    $k = 0;
    $team = $user['rights'];
    $right = array(10);
    $i = 1;
    while (!($i > 18)) {
        if ($team['right' . $i] == 1 && in_array($i, $right)) {
            ($k += 1) + -1;
        }
        ($i += 1) + -1;
    }
    if ($k == 0 && !in_array($operation, $skipop)) {
        if (!($region['openid'] == $_W['openid'] || $region['uid'] == $_W['member']['uid'])) {
            $this->mymsg('error', '温馨提示', '本权限仅针对物业管理人员开放.', 'close');
        }
    }
}
if ($operation == 'list') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sorttype = $_GPC['sorttype'];
        if ($sorttype == 1) {
            $orderby = ' order by ctime desc';
        } else {
            $orderby = ' order by enddate';
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' or goodssn LIKE \'%' . $_GPC['keyword'] . '%\')';
        }
        if (!empty($_GPC['range'])) {
            $condition .= ' and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(enddate),\'%y-%m-%d\')) <=' . $_GPC['range'] * 30;
        }
        if (!empty($_GPC['cateid'])) {
            $condition .= ' AND  cateid = :cateid ';
            $params['cateid'] = $_GPC['cateid'];
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_progoods') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_progoods') . ' where ' . $condition . $orderby . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'detail', 'id' => $data[$k]['id']));
            $data[$k]['startdate'] = !empty($data[$k]['startdate']) ? date('Y-m-d', $data[$k]['startdate']) : '';
            $data[$k]['enddate'] = !empty($data[$k]['enddate']) ? date('Y-m-d', $data[$k]['enddate']) : '';
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_progoods_cate') . ' where weid = :weid and pid = :pid and rid=:rid';
    $category = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id']));
    include $this->mymtpl('list');
} elseif ($operation == 'detail') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_progoods') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_progoods_cate') . ' where weid = :weid and id=:cateid';
    $item['category'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':cateid' => $item['cateid']));
    include $this->mymtpl('detail');
} elseif ($operation == 'add') {
    if ($_W['isajax']) {
        if (empty($_GPC['cateid'])) {
            show_json(0, '物品分类不能为空');
        }
        if (empty($_GPC['title'])) {
            show_json(0, '物品名称不能为空');
        }
        if (empty($_GPC['goodssn'])) {
            show_json(0, '物品编号不能为空');
        }
        if (empty($_GPC['position'])) {
            show_json(0, '安装位置不能为空');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_progoods') . ' where weid=:weid and goodssn = :goodssn';
        $goods = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':goodssn' => $_GPC['goodssn']));
        if (!empty($goods) > 0) {
            if (!empty($googs['position'])) {
                show_json(0, '已经登记过');
            }
            $data = array('position' => $_GPC['position'], 'memo' => $_GPC['remark'], 'ouid' => $_W['member']['uid'], 'status' => 1, 'otime' => TIMESTAMP);
            $res = pdo_update('rhinfo_zyxq_progoods', $data, array(':weid' => $_W['uniacid'], ':id' => $goods['id']));
        } else {
            $data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'cateid' => $_GPC['cateid'], 'spec' => $_GPC['spec'], 'brand' => $_GPC['brand'], 'position' => $_GPC['position'], 'goodssn' => $_GPC['goodssn'], 'remark' => $_GPC['remark'], 'ouid' => $_W['member']['uid'], 'status' => 1, 'otime' => TIMESTAMP, 'ctime' => TIMESTAMP);
            $res = pdo_insert('rhinfo_zyxq_progoods', $data);
        }
        if ($res) {
            show_json(1, '登记成功');
        } else {
            show_json(0, '登记失败');
        }
    }
    $sql = 'select id as value,title as text from ' . tablename('rhinfo_zyxq_progoods_cate') . ' where weid = :weid and pid = :pid and rid=:rid';
    $cates = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id']));
    include $this->mymtpl('add');
}
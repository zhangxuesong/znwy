<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'notice';
$mydo = 'notice';
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($res) {
        if ($res['arrearagelimit1']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getnotice($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
if ($operation == 'index') {
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($_GPC['psize']) ? 5 : $_GPC['psize'];
        if (!empty($user['rid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid) and deleted=0 and bid>0 and tid > 0 order by isdefault desc';
            $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        }
        if (!empty($item)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid and status>0 and  bid=0 and tid=0 and category=1  order by id desc';
            $data1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid and status>0 and category=1 and ' . $item['bid'] . ' in(bid)  and tid in(0,' . $item['tid'] . ') and hid in(0,' . $item['hid'] . ') order by id desc';
            $data2 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
            $data = array_merge($data1, $data2);
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid and status>0 and bid=0 and tid=0 and category=1 order by id desc';
            $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
        }
        $weekarray = array('日', '一', '二', '三', '四', '五', '六');
        $times = 0;
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['week'] = $weekarray[date('w', $data[$k]['ctime'])];
            $data[$k]['cdate'] = date('m-d', $data[$k]['ctime']);
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_notice_log') . ' where weid=:weid and nid=:nid and uid=:uid limit 1';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':nid' => $data[$k]['id'], ':uid' => $_W['member']['uid']));
            if ($total > 0) {
                $data[$k]['isread'] = '已读';
                $data[$k]['css'] = 'text-default';
            } else {
                $data[$k]['isread'] = '未读';
                $data[$k]['css'] = 'text-danger';
            }
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_notice_log') . ' where weid=:weid and nid=:nid ';
            $times = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':nid' => $data[$k]['id']));
            $data[$k]['times'] = $times;
            ($k += 1) + -1;
        }
        $total = count($data);
        $start = ($pindex - 1) * $psize;
        if (!empty($data)) {
            $data = multi_array_sort($data, 'ctime', SORT_DESC);
            $data = array_slice($data, $start, $psize);
        }
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    include $this->template($this->mytpl('notice/index'));
} elseif ($operation == 'detail') {
    $mcurr = 'index';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $content = stripslashes($item['content']);
    $content = html_entity_decode($content);
    $read = 0;
    if ($_W['member']['uid']) {
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_notice_log') . ' where  weid=:weid and nid = :nid and uid = :uid';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':nid' => $id, ':uid' => $_W['member']['uid']));
        if ($total > 0) {
            $read = 1;
        } else {
            $data = array('weid' => $_W['uniacid'], 'nid' => $id, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_notice_log', $data);
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_notice_log') . ' where  weid=:weid and nid = :nid';
        $times = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':nid' => $id));
    }
    include $this->template($this->mytpl('notice/detail'));
} elseif ($operation == 'status') {
    $id = intval($_GPC['id']);
    $data = array('status' => 1, 'audituid' => $_W['member']['uid'], 'openid' => $_W['openid']);
    $glue = 'AND';
    $result = pdo_update('rhinfo_zyxq_notice', $data, array('id' => $id, 'weid' => $_W['uniacid']), 'AND');
    if (!empty($result)) {
        show_json(1, '审核成功!');
    } else {
        show_json(0, '操作失败!');
    }
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'task';
$mydo = 'task';
$user = $this->getmyinfo($_W['member']['uid']);
$_share = $this->rhinfo_share();
load()->model('mc');
if ($operation == 'index') {
    if ($_W['isajax']) {
        $id = intval($_GPC['id']);
        $sql = 'select * from ' . tablename('rhinfo_zycj_task') . ' where weid=:weid and id=:id';
        $task = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
        $sql = 'select * from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and (openid=:openid or uid=:uid) and parentid=0';
        $taskmember = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':taskid' => $id, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        if (!empty($taskmember)) {
            show_json(0, '您已经领取过');
        }
        $data = array('weid' => $_W['uniacid'], 'rid' => $task['rid'], 'taskid' => $id, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'parentid' => 0, 'status' => 0, 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zycj_task_member', $data);
        $id = pdo_insertid();
        if ($res) {
            show_json(1, '领取成功');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_task') . ' where weid=:weid and (rid=0 or rid=:rid) and tasktype=2 and startdate < UNIX_TIMESTAMP(now()) and  enddate >=UNIX_TIMESTAMP(now())';
    $tasks = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    include $this->mymtpl('index');
} elseif ($operation == 'detail') {
    $id = $_GPC['id'];
    $sql = 'select * from ' . tablename('rhinfo_zycj_task') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['tasktype'] == 1) {
        if ($item['uid'] == $_W['member']['uid'] || $item['openid'] == $_W['openid']) {
            header('Location:' . $this->createMobileurl($mydo, array('op' => 'mytask')));
            exit(0);
        }
        $this->mymsg('error', '温馨提示', '此任务未开放领取.', 'close');
    }
    include $this->mymtpl('detail');
} elseif ($operation == 'mytask') {
    $sql = 'select t.*,m.parentid from ' . tablename('rhinfo_zycj_task_member') . ' as m left join ' . tablename('rhinfo_zycj_task') . ' as t on m.taskid=t.id  where t.weid=:weid and (m.openid=:openid or m.uid=:uid) order by enddate desc';
    $tasks = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $k = 0;
    while (!($k >= count($tasks))) {
        if (TIMESTAMP >= $tasks[$k]['startdate'] && !(TIMESTAMP > $tasks[$k]['enddate'])) {
            $tasks[$k]['statustxt'] = '进行中';
        } else {
            $tasks[$k]['statustxt'] = '已失效';
        }
        if ($tasks[$k]['status'] == 0) {
            $tasks[$k]['statustxt'] = '已失效';
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and parentid=:parentid';
        $tasks[$k]['count'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], 'taskid' => $tasks[$k]['id'], ':parentid' => $tasks[$k]['parentid']));
        ($k += 1) + -1;
    }
    include $this->mymtpl('mytask');
} elseif ($operation == 'share') {
    $id = intval($_GPC['id']);
    $parentid = intval($_GPC['parentid']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_task') . ' where weid=:weid and id=:id';
    $task = pdo_fetch($sql, array(':id' => $id, ':weid' => $_W['uniacid']));
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and parentid=:parentid';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], 'taskid' => $id, ':parentid' => $parentid));
    $qrcode_url = $this->createMobileurl($mydo, array('op' => 'getshare', 'id' => $id, 'parentid' => $parentid));
    $siteurl = !empty($this->syscfg['siteurl']) ? $this->syscfg['siteurl'] : $_W['siteroot'];
    $qrcode_url = $siteurl . substr($qrcode_url, 2);
    if ($task['category'] == 1) {
        $msg = '绑定房产';
    }
    if ($task['category'] == 2) {
        $msg = '到店消费';
    }
    $_share['title'] = $_W['fans']['nickname'] . '邀请您' . $msg;
    $_share['imgUrl'] = tomedia($task['thumb']);
    $_share['desc'] = $msg . '，享有您的专属特权.';
    $_share['link'] = $qrcode_url;
    include $this->mymtpl('share');
} elseif ($operation == 'getshare') {
    $id = intval($_GPC['id']);
    $parentid = intval($_GPC['parentid']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_task') . ' where weid=:weid and id=:id';
    $task = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and (uid=:uid or openid=:openid) and parentid>0';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], 'taskid' => $id, ':uid' => $_W['member']['uid'], ':openid' => $_W['openid']));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and id=:parentid';
        $task_member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], 'taskid' => $id, ':parentid' => $parentid));
        $fans = array();
        $fans = mc_fansinfo($task_member['openid'], $_W['acid'], $_W['uniacid']);
        $this->mymsg('error', '温馨提示', '您已经是' . $fans['nickname'] . '的成员', 'close');
    }
    if ($parentid > 0) {
        $data = array('weid' => $_W['uniacid'], 'rid' => $task['rid'], 'taskid' => $id, 'parentid' => $parentid, 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'stauts' => 0, 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_task_member', $data);
    }
    $url = $this->createMobileurl('member', array('op' => 'blist'));
    if ($task['category'] == 1) {
        if (!empty($task['rid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $task['rid']));
            if ($region['register'] == 1) {
                $url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $task['rid'], 'register' => 1));
            } elseif ($region['register'] == 2) {
                $url = $this->createMobileurl('member', array('op' => 'reg', 'rid' => $task['rid'], 'register' => 2));
            } else {
                if (!empty($user['mobile'])) {
                    $bind = 'bind';
                } else {
                    $bind = 'rbind';
                }
                $url = $this->createMobileurl('member', array('op' => $bind, 'rid' => $task['rid']));
            }
        } else {
            $url = $this->createMobileurl('member', array('op' => 'blist'));
        }
    } elseif ($task['category'] == 2) {
        $url = $this->createMobileurl('business', array('op' => 'list'));
    }
    header('Location:' . $url);
    exit(0);
} elseif ($operation == 'taskinfo') {
    $id = intval($_GPC['id']);
    $parentid = intval($_GPC['parentid']);
    $sql = 'select * from ' . tablename('rhinfo_zycj_task') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and parentid=:parentid';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], 'taskid' => $id, ':parentid' => $parentid));
        $sql = 'select * from ' . tablename('rhinfo_zycj_task_member') . ' where weid=:weid and taskid=:taskid and parentid=:parentid';
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], 'taskid' => $id, ':parentid' => $parentid));
        $k = 0;
        while (!($k >= count($list))) {
            $fans = array();
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
            $list[$k]['nickname'] = $fans['nickname'];
            if ($list[$k]['status'] == 1) {
                $list[$k]['statustxt'] = $item['category'] == 1 ? '已绑定房产' : '已到店消费';
            } else {
                $list[$k]['statustxt'] = $item['category'] == 1 ? '未绑定房产' : '未到店消费';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('taskinfo');
}
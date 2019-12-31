<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$curr = 'article';
$mydo = 'notice';
$user = $this->getmyinfo($_W['member']['uid']);
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($res) {
        if ($res['arrearagelimit6']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
if ($operation == 'index') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['category'] == 2) {
        $url = mymurl('activity/detail', array('id' => $item['id']));
    } elseif ($item['category'] == 1) {
        if ($item['votetype'] == 1) {
            if ($item['votemethod'] == 2) {
                $url = mymurl('activity/votestar', array('id' => $item['id']));
            } else {
                $url = mymurl('activity/votelike', array('id' => $item['id']));
            }
        } elseif ($item['votetype'] == 2) {
            $url = mymurl('activity/voteimage', array('id' => $item['id']));
        } elseif ($item['votetype'] == 3) {
            $url = mymurl('activity/voteimage', array('id' => $item['id']));
        } else {
            $url = mymurl('activity/votetext', array('id' => $item['id']));
        }
    } elseif ($item['category'] == 3) {
        $url = mymurl('activity/question', array('id' => $item['id']));
    } else {
        $url = mymurl('activity/list');
    }
    header('Location:' . $url);
} elseif ($operation == 'list') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and (rid=:rid or rid=0) and status=1 and enddate>:enddate';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid'], 'enddate' => strtotime('-1 months')));
    $k = 0;
    while (!($k >= count($list))) {
        if ($list[$k]['category'] == 2) {
            $list[$k]['url'] = mymurl('activity/detail', array('id' => $list[$k]['id']));
            $list[$k]['catename'] = '报名活动';
        } elseif ($list[$k]['category'] == 1) {
            if ($list[$k]['votetype'] == 1) {
                if ($list[$k]['votemethod'] == 2) {
                    $list[$k]['url'] = mymurl('activity/votestar', array('id' => $list[$k]['id']));
                } else {
                    $list[$k]['url'] = mymurl('activity/votelike', array('id' => $list[$k]['id']));
                }
            } elseif ($list[$k]['votetype'] == 2) {
                $list[$k]['url'] = mymurl('activity/voteimage', array('id' => $list[$k]['id']));
            } elseif ($list[$k]['votetype'] == 3) {
                $list[$k]['url'] = mymurl('activity/voteimage', array('id' => $list[$k]['id']));
            } else {
                $list[$k]['url'] = mymurl('activity/votetext', array('id' => $list[$k]['id']));
            }
            $list[$k]['catename'] = '投票活动';
        } elseif ($list[$k]['category'] == 3) {
            $list[$k]['url'] = mymurl('activity/question', array('id' => $list[$k]['id']));
            $list[$k]['catename'] = '问卷调查';
        }
        $list[$k]['image'] = !empty($list[$k]['image']) ? tomedia($list[$k]['image']) : RHINFO_ZYXQ_STATIC . 'mobile/images/activity.png';
        if (!($list[$k]['enddate'] >= TIMESTAMP)) {
            $list[$k]['url'] = '';
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl();
} elseif ($operation == 'detail') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $item['image'] = !empty($item['image']) ? tomedia($item['image']) : RHINFO_ZYXQ_STATIC . 'mobile/images/activity.png';
    $item['url'] = mymurl('activity/activity', array('id' => $id));
    include $this->mymtpl();
} elseif ($operation == 'activity') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $signextend = iunserializer($item['signextend']);
    if ($_W['isajax']) {
        $res = $this->activity_check_member($item);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        $data = array('weid' => $_W['uniacid'], 'actid' => $id, 'uid' => $_W['member']['uid'], 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'status' => 1, 'ctime' => TIMESTAMP);
        $i = 1;
        $k = 0;
        while (!($k >= count($signextend))) {
            if ($signextend[$k]['ischecked'] == 1) {
                if (empty($_GPC['extend_' . $i])) {
                    show_json(0, $signextend[$k]['itemtitle'] . '不能为空');
                }
            }
            $data['extend_' . $i] = $_GPC['extend_' . $i];
            ($i += 1) + -1;
            ($k += 1) + -1;
        }
        $res = pdo_insert('rhinfo_zyxq_activity_sign', $data);
        if ($res) {
            show_json(1, '提交成功');
        } else {
            show_json(0, '提交失败');
        }
    }
    include $this->mymtpl();
} elseif ($operation == 'votelike') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $res = $this->activity_check_member($item);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        $data = array('weid' => $_W['uniacid'], 'actid' => $id, 'uid' => $_W['member']['uid'], 'status' => 1, 'ctime' => TIMESTAMP);
        $data['voteid'] = $_GPC['mysel'];
        $data['votenum'] = 1;
        pdo_insert('rhinfo_zyxq_activity_vote', $data);
        show_json(1, '提交成功');
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_activity_vote') . ' where weid=:weid and actid = :actid and voteid=:voteid';
    $item['good'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':actid' => $id, ':voteid' => 1));
    $item['bad'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':actid' => $id, ':voteid' => 2));
    include $this->mymtpl();
} elseif ($operation == 'votetext') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $item['image'] = !empty($item['image']) ? tomedia($item['image']) : RHINFO_ZYXQ_STATIC . 'mobile/images/activity.png';
    if ($_W['isajax']) {
        $res = $this->activity_check_member($item);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        $data = array('weid' => $_W['uniacid'], 'actid' => $id, 'uid' => $_W['member']['uid'], 'status' => 1, 'ctime' => TIMESTAMP);
        if (empty($_GPC['mysel'])) {
            show_json(0, '未选择回答');
        }
        $data['voteid'] = $_GPC['mysel'];
        $data['votenum'] = 0;
        pdo_insert('rhinfo_zyxq_activity_vote', $data);
        show_json(1, '提交成功');
    }
    $votetext = iunserializer($item['votetext']);
    include $this->mymtpl();
} elseif ($operation == 'votestar') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $res = $this->activity_check_member($item);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        $data = array('weid' => $_W['uniacid'], 'actid' => $id, 'uid' => $_W['member']['uid'], 'status' => 1, 'ctime' => TIMESTAMP);
        $data['voteid'] = 0;
        $data['votenum'] = $_GPC['praise'];
        pdo_insert('rhinfo_zyxq_activity_vote', $data);
        show_json(1, '评星成功');
    }
    include $this->mymtpl();
} elseif ($operation == 'voteimage') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $item['image'] = !empty($item['image']) ? tomedia($item['image']) : RHINFO_ZYXQ_STATIC . 'mobile/images/activity.png';
    $multimages = iunserializer($item['multimages']);
    if ($_W['isajax']) {
        $res = $this->activity_check_member($item);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        $data = array('weid' => $_W['uniacid'], 'actid' => $id, 'uid' => $_W['member']['uid'], 'votenum' => 0, 'status' => 1, 'ctime' => TIMESTAMP);
        if ($item['votetype'] == 2) {
            if (empty($_GPC['mysel'])) {
                show_json(0, '未选择回答');
            }
            $data['voteid'] = $_GPC['mysel'];
            pdo_insert('rhinfo_zyxq_activity_vote', $data);
            show_json(1, '提交成功');
        } elseif ($item['votetype'] == 3) {
            $i = 1;
            $j = 0;
            $k = 0;
            while (!($k >= count($multimages))) {
                if ($_GPC['mysel_' . $i] == $multimages[$k]['id']) {
                    $data['voteid'] = $multimages[$k]['id'];
                    pdo_insert('rhinfo_zyxq_activity_vote', $data);
                    ($j += 1) + -1;
                }
                ($i += 1) + -1;
                ($k += 1) + -1;
            }
            if ($j > 1) {
                show_json(1, '提交成功');
            } else {
                show_json(0, '提交失败');
            }
        } else {
            show_json(0, '投票类别错误');
        }
    }
    $k = 0;
    while (!($k >= count($multimages))) {
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_activity_vote') . ' where weid=:weid and actid = :actid and voteid=:voteid';
        $multimages[$k]['votenum'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':actid' => $id, ':voteid' => $multimages[$k]['id']));
        ($k += 1) + -1;
    }
    include $this->mymtpl();
} elseif ($operation == 'question') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_activity') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $questions = iunserializer($item['question']);
    if ($_W['isajax']) {
        $res = $this->activity_check_member($item);
        if ($res['errno'] == 1) {
            show_json(0, $res['message']);
        }
        $data = array('weid' => $_W['uniacid'], 'actid' => $id, 'uid' => $_W['member']['uid'], 'status' => 1, 'ctime' => TIMESTAMP);
        $i = 1;
        $k = 0;
        while (!($k >= count($questions))) {
            $data['voteid'] = $questions[$k]['id'];
            $data['votenum'] = $_GPC['q_' . $i];
            pdo_insert('rhinfo_zyxq_activity_vote', $data);
            ($i += 1) + -1;
            ($k += 1) + -1;
        }
        if ($i > 1) {
            show_json(1, '提交成功');
        } else {
            show_json(0, '提交失败');
        }
    }
    $groups = array_chunk($questions, 5);
    include $this->mymtpl();
}
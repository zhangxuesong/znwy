<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$mydo = 'manage';
$curr = 'manage';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$myurl = $this->createMobileurl($mydo);
$skipop = array('repair', 'suggest');
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
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
    $i = 1;
    while (!($i > 18)) {
        if ($team['right' . $i] == 1) {
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
if ($operation == 'index') {
    $mcurr = 'index';
    $weekarray = array('日', '一', '二', '三', '四', '五', '六');
    $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and pid = :pid and rid = :rid and status = 0 ';
    $notices = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $team['pid'], ':rid' => $myrid));
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and status = 1 and deleted=0 ';
    $member_total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $starttime = strtotime('now -7days');
    $endtime = TIMESTAMP;
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and status = 0 and deleted=0 and otype=0 and ctime between :starttime and :endtime ';
    $member_otype_0 = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':starttime' => $starttime, ':endtime' => $endtime));
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and status = 0 and deleted=0 and otype=1 and ctime between :starttime and :endtime ';
    $member_otype_1 = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':starttime' => $starttime, ':endtime' => $endtime));
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid = :rid and status = 0 and deleted=0 and otype=2 and ctime between :starttime and :endtime ';
    $member_otype_2 = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':starttime' => $starttime, ':endtime' => $endtime));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and (openid=:openid or uid=:uid)';
    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $myregions = array();
    $k = 0;
    while (!($k >= count($teams))) {
        if ($teams[$k]['rid'] > 0) {
            $sql = 'SELECT id,title,thumb FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
            $myregion = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $teams[$k]['rid']));
            $myregions[] = $myregion;
        } elseif (!empty($teams[$k]['ridstr'])) {
            $regionarr = explode(',', $teams[$k]['ridstr']);
            $m = 0;
            while (!($m >= count($regionarr))) {
                $sql = 'SELECT id,title,thumb FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
                $myregion = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $regionarr[$m]));
                if (!empty($myregion)) {
                    $myregions[] = $myregion;
                }
                ($m += 1) + -1;
            }
        } else {
            $sql = 'SELECT id,title,thumb FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
            $myregion = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $teams[$k]['pid']));
            if (!empty($myregion)) {
                $myregions[] = $myregion;
            }
        }
        ($k += 1) + -1;
    }
    $myregions = multi_array_unique($myregions);
    include $this->mymtpl('index');
} elseif ($operation == 'notice') {
    $mcurr = 'notice';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $limit = 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid ';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid order by status,ctime desc ' . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
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
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    if (empty($team['right6'])) {
        $this->mymsg('error', '温馨提示', '本权限仅针对物业服务人员开放.', 'close');
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid = :rid and category=1 and status = 0 ';
    $notices = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    include $this->mymtpl('notice');
} elseif ($operation == 'mynotice') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $limit = 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid)';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_notice') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid) order by status,ctime desc ' . $limit;
        $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
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
        show_json(1, array('list' => $data, 'total' => $total, 'pagesize' => $psize));
    }
    include $this->mymtpl('mynotice');
} elseif ($operation == 'get_today') {
    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zyxq_repair') . ' where weid = :weid and rid=:rid and ctime between :ctime1 and :ctime2';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zyxq_suggest') . ' where weid = :weid and rid=:rid and ctime between :ctime1 and :ctime2';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and ctime between :ctime1 and :ctime2';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $today_repair = pdo_fetchcolumn($sql1, $params);
    $today_suggest = pdo_fetchcolumn($sql2, $params);
    $today_member = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('today_repair' => $today_repair, 'today_suggest' => $today_suggest, 'today_member' => $today_member));
} elseif ($operation == 'get_status') {
    $simdate = strtotime('now +30days');
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zyxq_repair') . ' where weid = :weid and rid=:rid and status < 2';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zyxq_suggest') . ' where weid = :weid and rid=:rid and status < 2';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zyxq_door') . ' where weid = :weid and rid=:rid and simdate <=' . $simdate;
    $sql4 = 'select count(*) from ' . tablename('rhinfo_zyxq_repairp') . ' where weid = :weid and rid=:rid and status < 2';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid);
    $repair_status = pdo_fetchcolumn($sql1, $params);
    $suggest_status = pdo_fetchcolumn($sql2, $params);
    $door_status = pdo_fetchcolumn($sql3, $params);
    $repairp_status = pdo_fetchcolumn($sql4, $params);
    show_json(1, array('repair_status' => $repair_status, 'suggest_status' => $suggest_status, 'door_status' => $door_status, 'repairp_status' => $repairp_status));
} elseif ($operation == 'get_member') {
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and otype=0 and deleted=0 and category=1';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and otype=1 and deleted=0 and category=1';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and rid=:rid and otype=2 and deleted=0 and category=1';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid);
    $member_otype0 = pdo_fetchcolumn($sql1, $params);
    $member_otype1 = pdo_fetchcolumn($sql2, $params);
    $member_otype2 = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('member_otype0' => $member_otype0, 'member_otype1' => $member_otype1, 'member_otype2' => $member_otype2));
} elseif ($operation == 'get_fee') {
    $starttime = strtotime('now -30days');
    $endtime = TIMESTAMP;
    $sql1 = 'select sum(fee - payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and rid=:rid and status=1 ';
    $sql2 = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and rid=:rid and status=2 and paydate>0 and paydate between :starttime and :endtime ';
    $sql3 = 'select sum(payfee) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and rid=:rid and status=2 and paydate>0 and datediff(DATE_FORMAT(NOW(),\'%y-%m-%d\'),DATE_FORMAT(FROM_UNIXTIME(paydate),\'%y-%m-%d\')) =0';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid);
    $fee_total = pdo_fetchcolumn($sql1, $params);
    $fee_total = empty($fee_total) ? 0 : $fee_total;
    $payfee_month = pdo_fetchcolumn($sql2, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':starttime' => $starttime, ':endtime' => $endtime));
    $payfee_month = empty($payfee_month) ? 0 : $payfee_month;
    $payfee_today = pdo_fetchcolumn($sql3, $params);
    $payfee_today = empty($payfee_today) ? 0 : $payfee_today;
    show_json(1, array('fee_total' => $fee_total, 'payfee_month' => $payfee_month, 'payfee_today' => $payfee_today));
} elseif ($operation == 'steward') {
    $mcurr = 'steward';
    if (empty($team['right1']) && empty($team['right2']) && empty($team['right'])) {
        $this->mymsg('error', '温馨提示', '本权限仅针对物业服务人员开放.', 'close');
    }
    $status = empty($_GPC['status']) ? 1 : $_GPC['status'];
    include $this->mymtpl('steward');
} elseif ($operation == 'repairsta') {
    $mcurr = 'steward';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $_GPC['rid'];
        $status = $_GPC['status'];
        if (!empty($status)) {
            if ($status == 1) {
                $condition .= ' and status<=' . intval($status);
            } else {
                $condition .= ' and status=' . intval($status);
            }
        }
        if (!empty($_GPC['repairdate'])) {
            $condition .= ' and ctime >=' . strtotime($_GPC['repairdate']);
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repair') . ' where status<9 and ' . $condition, $params);
        $condition .= ' order by status, ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repair') . ' where status<9 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
            if ($region['finishdays'] > 0 && $list[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $list[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update('rhinfo_zyxq_repair', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['status'] = 8;
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的报修工单已自动结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给服务人员做个评价吧，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'team'));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($list[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                }
            }
            if ($list[$k]['status'] == '0' || $list[$k]['status'] == '1') {
                if (empty($list[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $list[$k]['ctime']) / 3600) - $list[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $list[$k]['reporttime']) / 3600) - ($list[$k]['reporttimes'] - 1) * 24;
                }
                if ($category['reporttime'] > 0 && !($category['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '报修处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此报修工单超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $list[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_repair') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['id']));
                }
            }
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['bg'] = 'fui-label fui-label-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '已回复';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('repairsta');
} elseif ($operation == 'myrepair') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $status = $_GPC['status'];
        if (!empty($status)) {
            if ($status == 1) {
                $condition .= ' and rid=:rid and (getopenid=:openid or getuid=:uid) and status <=' . intval($status);
            } else {
                $condition .= ' and rid=:rid and (getopenid=:openid or getuid=:uid) and status=' . intval($status);
            }
        } else {
            $condition .= ' and rid=:rid and (getopenid=:openid or getuid=:uid)';
        }
        $params[':rid'] = $myrid;
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repair') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $fans = array();
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['thumb'] = $fans['avatar'];
            $list[$k]['thumb'] = empty($list[$k]['thumb']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['thumb'];
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-blue';
                $list[$k]['status'] = '已回复';
            } elseif ($list[$k]['status'] == 9) {
                $list[$k]['css'] = 'text-red';
                $list[$k]['status'] = '待审核';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('myrepair');
} elseif ($operation == 'suggeststa') {
    $mcurr = 'steward';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        $status = $_GPC['status'];
        if (!empty($status)) {
            if ($status == 1) {
                $condition .= ' and status<=' . intval($status);
            } else {
                $condition .= ' and status=' . intval($status);
            }
        }
        if (!empty($_GPC['suggestdate'])) {
            $condition .= ' and ctime >=' . strtotime($_GPC['suggestdate']);
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_suggest') . ' where status<9 and ' . $condition, $params);
        $condition .= ' order by status, ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_suggest') . ' where status<9 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
            if ($region['finishdays'] > 0 && $list[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $list[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update('rhinfo_zyxq_suggest', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['status'] = 8;
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的投诉建议已自动结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，报修内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给服务人员做个评价吧，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'team'));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($list[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                }
            }
            if ($list[$k]['status'] == '0' || $list[$k]['status'] == '1') {
                if (empty($list[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $list[$k]['ctime']) / 3600) - $list[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $list[$k]['reporttime']) / 3600) - ($list[$k]['reporttimes'] - 1) * 24;
                }
                if ($category['reporttime'] > 0 && !($category['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '投诉建议处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，投诉建议内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此投诉建议超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $list[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $list[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_suggest') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $list[$k]['id']));
                }
            }
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['bg'] = 'fui-label fui-label-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['bg'] = 'fui-label fui-label-warning';
                $list[$k]['status'] = '已回复';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('suggeststa');
} elseif ($operation == 'mysuggest') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $status = $_GPC['status'];
        if (!empty($status)) {
            if ($status == 1) {
                $condition .= ' and rid=:rid and (getopenid=:openid or getuid=:uid) and status <=' . intval($status);
            } else {
                $condition .= ' and rid=:rid and (getopenid=:openid or getuid=:uid) and status=' . intval($status);
            }
        } else {
            $condition .= ' and rid=:rid and (getopenid=:openid or getuid=:uid)';
        }
        $params[':rid'] = $myrid;
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_suggest') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $fans = array();
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['thumb'] = $fans['avatar'];
            $list[$k]['thumb'] = empty($list[$k]['thumb']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['thumb'];
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $list[$k]['id']));
            if ($list[$k]['status'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 1) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['status'] = '待处理';
            } elseif ($list[$k]['status'] == 2) {
                $list[$k]['css'] = 'text-warning';
                $list[$k]['status'] = '处理中';
            } elseif ($list[$k]['status'] == 3) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['status'] = '已处理';
            } elseif ($list[$k]['status'] == 8) {
                $list[$k]['css'] = 'text-default';
                $list[$k]['status'] = '已结案';
            } elseif ($list[$k]['status'] == 5) {
                $list[$k]['css'] = 'text-blue';
                $list[$k]['status'] = '已回复';
            } elseif ($list[$k]['status'] == 9) {
                $list[$k]['css'] = 'text-red';
                $list[$k]['status'] = '待审核';
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('mysuggest');
} elseif ($operation == 'door') {
    $mcurr = 'steward';
    $data = array();
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $condition .= ' and rid=:rid ';
    $params[':rid'] = $myrid;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' and title like "%' . $_GPC['keyword'] . '%"';
    }
    $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_door') . ' where ' . $condition, $params);
    $condition .= ' order by simdate LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_door') . ' where ' . $condition;
    $list = pdo_fetchall($sql, $params);
    $temp_locksn = '';
    $temp_isonline = '';
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['simdate'] = date('Y-m-d', $list[$k]['simdate']);
        if ($list[$k]['locksn'] == $temp_locksn) {
            $list[$k]['isonline'] = $temp_isonline;
        } else {
            if ($list[$k]['devtype'] == 1 || $list[$k]['devtype'] == 2) {
                if (!($list[$k]['doortype'] == 2)) {
                    if ($list[$k]['doortype'] == 5) {
                        $res['code'] = '0';
                    } else {
                        $set = array();
                        $set['url'] = '/doormaster/server/remote_control';
                        $set['token'] = $region['thinmoo_token'];
                        $set['op'] = 'GET';
                        $data = "{\r\n\t\t\t\t\t\"devsn_list\":[\"" . $list[$k]['locksn'] . "\"]\r\n\t\t\t\t  }";
                        $result = thinmoo_http_post($set, $data);
                        if ($result['ret'] == '0') {
                            $door_data = $result['data'];
                            $lock_data = $door_data[$list[$k]['locksn']];
                            if ($lock_data['dev_status'] == 'online') {
                                $res['code'] = '0';
                            } else {
                                $res['code'] = '1';
                            }
                        } else {
                            $res['code'] = '1';
                        }
                    }
                }
            } elseif ($list[$k]['devtype'] == 3) {
                if ($list[$k]['doortype'] == 2) {
                    $res = $this->devstatus($doors[$k]['doortype'], $doors[$k]['locksn']);
                }
                if ($list[$k]['doortype'] == 5) {
                    $res['code'] = '0';
                }
            } else {
                $res = $this->devstatus($list[$k]['doortype'], $list[$k]['locksn']);
            }
            if ($res['code'] == '0') {
                $list[$k]['isonline'] = '1';
            } else {
                $list[$k]['isonline'] = '2';
            }
            $temp_locksn = $list[$k]['locksn'];
            $temp_isonline = $list[$k]['isonline'];
            $list[$k]['url'] = $this->createMobileurl('manage', array('op' => 'doorlog', 'id' => $list[$k]['id']));
        }
        ($k += 1) + -1;
    }
    show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
} elseif ($operation == 'opendoor') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door') . ' where weid=:weid and status = 1 and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (empty($item)) {
        show_json(0, '设备停用');
    }
    if ($item['devtype'] == 1 || $item['devtype'] == 2) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
        $set = array();
        $set['url'] = '/doormaster/server/remote_control';
        $set['token'] = $region['thinmoo_token'];
        $set['op'] = 'OPEN_DOOR';
        $data = "{\r\n\t\t\t\"dev_sn\":\"" . $item['locksn'] . "\"\r\n\t\t  }";
        $result = thinmoo_http_post($set, $data);
        if ($result['ret'] == '0') {
            $res['code'] = '0';
            $res['msg'] = '开门成功';
        } else {
            $res['code'] = '1';
            $res['msg'] = $res['ret'] . '-' . $res['msg'];
        }
    } elseif ($item['devtype'] == 3) {
        if ($item['doortype'] == 2) {
            $res = $this->opendoor($item['doortype'], $item['locksn'], $item['lockid']);
        }
    } else {
        $res = $this->opendoor($item['doortype'], $item['locksn'], $item['lockid']);
    }
    $this->opendoorlog($id, $res);
    if ($res['code'] == '0') {
        show_json(0);
    } else {
        show_json(0, $res['msg']);
    }
} elseif ($operation == 'doorlog') {
    $mcurr = 'steward';
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and did = :did ';
        $params[':did'] = $id;
        $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_doorlog') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select o.*,d.title from ' . tablename('rhinfo_zyxq_doorlog') . ' as o left join ' . tablename('rhinfo_zyxq_door') . ' as d on o.did = d.id where o.weid=:weid and d.id=:id order by opentime desc ' . $limit;
        $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and uid=:uid';
            $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':uid' => $list[$k]['uid']));
            $fans = mc_fansinfo($list[$k]['uid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
            $list[$k]['avatar'] = empty($list[$k]['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['avatar'];
            $list[$k]['realname'] = $member['realname'];
            $list[$k]['address'] = $member['address'];
            ($k += 1) + -1;
        }
        include $this->mymtpl('ajaxdoorlog');
        exit(0);
    }
    include $this->mymtpl('doorlog');
} elseif ($operation == 'member') {
    $mcurr = 'member';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $_GPC['rid'];
        if (!empty($_GPC['room'])) {
            $room = explode('-', $_GPC['room']);
            $condition .= ' and bid=' . $room[0] . ' and tid=' . $room[1] . ' and hid=' . $room[2];
        }
        if (!($_GPC['status'] == 9)) {
            $condition .= ' and otype = ' . $_GPC['status'];
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition, $params);
        $condition .= ' order by status desc, ctime desc, bid, tid, hid ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['url'] = $this->createMobileurl('manager', array('op' => 'audit', 'id' => $list[$k]['id'], 'rid' => $list[$k]['rid'], 'from' => 1));
            if ($list[$k]['otype'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['otypelabel'] = '业主';
            } elseif ($list[$k]['otype'] == 1) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['otypelabel'] = '成员';
            } elseif ($list[$k]['otype'] == 2) {
                $list[$k]['css'] = 'text-blue';
                $list[$k]['bg'] = 'fui-label fui-label-blue';
                $list[$k]['otypelabel'] = '租户';
            }
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            if ($list[$k]['openid']) {
                $fans = array();
                $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
                $list[$k]['avatar'] = $fans['avatar'];
                $list[$k]['avatar'] = empty($list[$k]['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['avatar'];
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title,id';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title,id';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    if ($_GPC['status'] == 0) {
        $status = 0;
    } else {
        $status = empty($_GPC['status']) ? 9 : $_GPC['status'];
    }
    include $this->mymtpl('member');
} elseif ($operation == 'memberdate') {
    $mcurr = 'member';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $_GPC['rid'];
        if (!empty($_GPC['binddate'])) {
            $condition .= ' and ctime >=' . strtotime($_GPC['binddate']);
        }
        if (!($_GPC['status'] == 9)) {
            $condition .= ' and otype = ' . $_GPC['status'];
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition, $params);
        $condition .= ' order by status desc, ctime desc, bid, tid, hid ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['url'] = $this->createMobileurl('member', array('op' => 'audit', 'id' => $list[$k]['id'], 'from' => 1));
            if ($list[$k]['otype'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['otypelabel'] = '业主';
            } elseif ($list[$k]['otype'] == 1) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['otypelabel'] = '成员';
            } elseif ($list[$k]['otype'] == 2) {
                $list[$k]['css'] = 'text-blue';
                $list[$k]['bg'] = 'fui-label fui-label-blue';
                $list[$k]['otypelabel'] = '租户';
            }
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            if ($list[$k]['openid']) {
                $fans = array();
                $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
                $list[$k]['avatar'] = $fans['avatar'];
                $list[$k]['avatar'] = empty($list[$k]['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['avatar'];
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    if ($_GPC['status'] == 0) {
        $status = 0;
    } else {
        $status = empty($_GPC['status']) ? 9 : $_GPC['status'];
    }
    include $this->mymtpl('memberdate');
} elseif ($operation == 'mymember') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and rid=:rid and (auditopenid=:openid or audituid=:uid)';
        $params[':rid'] = $myrid;
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition, $params);
        $condition .= ' order by status desc, bid, tid, hid, ctime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_member') . ' where deleted=0 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['url'] = $this->createMobileurl('member', array('op' => 'audit', 'id' => $list[$k]['id'], 'from' => '1'));
            if ($list[$k]['otype'] == 0) {
                $list[$k]['css'] = 'text-danger';
                $list[$k]['bg'] = 'fui-label fui-label-danger';
                $list[$k]['otypelabel'] = '业主';
            } elseif ($list[$k]['otype'] == 1) {
                $list[$k]['css'] = 'text-success';
                $list[$k]['bg'] = 'fui-label fui-label-success';
                $list[$k]['otypelabel'] = '成员';
            } elseif ($list[$k]['otype'] == 2) {
                $list[$k]['css'] = 'text-blue';
                $list[$k]['bg'] = 'fui-label fui-label-blue';
                $list[$k]['otypelabel'] = '租户';
            }
            $list[$k]['openid'] = empty($list[$k]['openid']) ? $list[$k]['uid'] : $list[$k]['openid'];
            if ($list[$k]['openid']) {
                $fans = array();
                $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
                $list[$k]['avatar'] = $fans['avatar'];
                $list[$k]['avatar'] = empty($list[$k]['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['avatar'];
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('mymember');
} elseif ($operation == 'fee') {
    $mcurr = 'fee';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['room'])) {
            $room = explode('-', $_GPC['room']);
            $condition .= ' and bid=' . $room[0] . ' and tid=' . $room[1] . ' and hid=' . $room[2];
        }
        if (!empty($_GPC['status'])) {
            $condition .= ' and status = ' . $_GPC['status'];
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition, $params);
        $condition .= ' order by bid,tid,hid,startdate' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $building = '';
        $unit = '';
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['url'] = $this->createMobileUrl('manage', array('op' => 'feebill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid'], 'category' => $list[$k]['category'], 'status' => $list[$k]['status']));
            if ($list[$k]['category'] == 2 || $list[$k]['category'] == 4) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
                $building = pdo_fetchcolumn($sql, array(':id' => $list[$k]['bid'], ':weid' => $_W['uniacid']));
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
                $building = pdo_fetchcolumn($sql, array(':id' => $list[$k]['bid'], ':weid' => $_W['uniacid']));
                $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
                $unit = pdo_fetchcolumn($sql, array(':id' => $list[$k]['tid'], ':weid' => $_W['uniacid']));
            }
            $list[$k]['address'] = $region['title'] . '-' . $building . '-' . $unit . '-' . $list[$k]['address'];
            $list[$k]['paydate'] = empty($list[$k]['paydate']) ? '' : date('Y-m-d', $list[$k]['paydate']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid';
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    $status = empty($_GPC['status']) ? 0 : $_GPC['status'];
    include $this->mymtpl('fee');
} elseif ($operation == 'payfee') {
    $mcurr = 'fee';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['paydate'])) {
            $condition .= ' and paydate >=' . strtotime($_GPC['paydate']);
        }
        $condition .= ' and status >1 ';
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition, $params);
        $condition .= ' order by paydate ,bid,tid,hid ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $building = '';
        $unit = '';
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['url'] = $this->createMobileUrl('manage', array('op' => 'feebill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid'], 'category' => $list[$k]['category'], 'status' => $list[$k]['status']));
            if ($list[$k]['category'] == 2 || $list[$k]['category'] == 4) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
                $building = pdo_fetchcolumn($sql, array(':id' => $list[$k]['bid'], ':weid' => $_W['uniacid']));
            } else {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
                $building = pdo_fetchcolumn($sql, array(':id' => $list[$k]['bid'], ':weid' => $_W['uniacid']));
                $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
                $unit = pdo_fetchcolumn($sql, array(':id' => $list[$k]['tid'], ':weid' => $_W['uniacid']));
            }
            $list[$k]['address'] = $region['title'] . '-' . $building . '-' . $unit . '-' . $list[$k]['address'];
            $list[$k]['paydate'] = empty($list[$k]['paydate']) ? '' : date('Y-m-d', $list[$k]['paydate']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('payfee');
} elseif ($operation == 'feebill') {
    $mcurr = 'fee';
    $building = '';
    $unit = '';
    if ($_GPC['category'] == 2 || $_GPC['category'] == 4) {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_location') . ' where id = :id and weid = :weid';
        $building = pdo_fetchcolumn($sql, array(':id' => $_GPC['bid'], ':weid' => $_W['uniacid']));
    } else {
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where id = :id and weid = :weid';
        $building = pdo_fetchcolumn($sql, array(':id' => $_GPC['bid'], ':weid' => $_W['uniacid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid';
        $unit = pdo_fetchcolumn($sql, array(':id' => $_GPC['tid'], ':weid' => $_W['uniacid']));
    }
    if ($_GPC['category'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':hid' => $_GPC['hid']));
    }
    if ($_GPC['category'] == 2) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_shop') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':id' => $_GPC['hid']));
    }
    if ($_GPC['category'] == 4) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and pid = :pid and rid = :rid and lid = :bid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':id' => $_GPC['hid']));
    }
    $address = $region['title'] . '-' . $building . '-' . $unit . '-' . $room['title'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and status=:status';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid'], ':status' => $_GPC['status']));
    $sql = $_GPC['status'] > 1 ? 'SELECT sum(payfee) as payfee FROM ' : 'SELECT sum(fee) as payfee FROM ';
    $sql .= tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and pid=:pid and rid = :rid and bid=:bid and tid=:tid and hid=:hid and status=:status';
    $totalfee = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $_GPC['pid'], ':rid' => $_GPC['rid'], ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid'], ':status' => $_GPC['status']));
    include $this->mymtpl('feebill');
} elseif ($operation == 'my') {
    $mcurr = 'my';
    if (empty($team['avatar'])) {
        if (!empty($team['openid']) || !empty($team['uid'])) {
            $fans = array();
            load()->model('mc');
            $team['openid'] = empty($team['openid']) ? $team['uid'] : $team['openid'];
            $fans = mc_fansinfo($team['openid'], $_W['acid'], $_W['uniacid']);
            $team['avatar'] = $fans['avatar'];
        } else {
            $team['avatar'] = MODULE_URL . 'static/mobile/images/head.jpg';
        }
    }
    include $this->mymtpl('my');
} elseif ($operation == 'myset') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        if (empty($_GPC['realname'])) {
            show_json(0, '请输入真实姓名');
        }
        if (empty($_GPC['nickname'])) {
            show_json(0, '请输入昵称');
        }
        if (empty($_GPC['mobile'])) {
            show_json(0, '请输入手机号码');
        }
        $data = array('realname' => $_GPC['realname'], 'nickname' => $_GPC['nickname'], 'mobile' => $_GPC['mobile'], 'avatar' => $_GPC['avatar']);
        pdo_update('rhinfo_zyxq_team', $data, array('weid' => $_W['uniacid'], 'rid' => $myrid, 'id' => $_GPC['teamid']));
        show_json(1);
    }
    $team['showavatar'] = empty($team['avatar']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $team['avatar'];
    include $this->mymtpl('myset');
} elseif ($operation == 'myreply') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' p.weid=:weid and p.rid=:rid and (p.openid=:openid or p.uid=:uid) and (p.postid>0 or p.replypostid>0) and p.deleted=0 ';
        $params[':rid'] = $myrid;
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select p.*,b.logo from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition . ' ORDER BY p.createtime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['createtime'] = date('Y-m-d H:i', $list[$k]['createtime']);
            $list[$k]['logo'] = tomedia($list[$k]['logo']);
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_post') . ' where weid=:weid and rid=:rid and id=:postid';
            $list[$k]['posttitle'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':postid' => $list[$k]['postid']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('replys');
} elseif ($operation == 'mypost') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' p.weid=:weid and p.rid=:rid and (p.openid=:openid or p.uid=:uid) and p.postid=0 and p.deleted=0 ';
        $params[':rid'] = $myrid;
        $params[':openid'] = $_W['openid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select p.*,b.logo from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition . ' ORDER BY p.createtime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['createtime'] = date('Y-m-d H:i', $list[$k]['createtime']);
            $list[$k]['logo'] = tomedia($list[$k]['logo']);
            $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $list[$k]['id']));
            $list[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=:pid limit 1', array(':pid' => $list[$k]['id']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('posts');
} elseif ($operation == 'selectroom') {
    $mcurr = 'member';
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title,id';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title,id';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    $type = empty($_GPC['type']) ? 'enterroom' : $_GPC['type'];
    include $this->mymtpl('selectroom');
} elseif ($operation == 'enterroom') {
    $mcurr = 'member';
    $room = $_GPC['room'];
    $roomarr = array();
    $roomarr = explode('-', $_GPC['room']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0], ':tid' => $roomarr[1], ':hid' => $roomarr[2]));
    if ($_W['isajax']) {
        if (empty($_GPC['ownername'])) {
            show_json(0, '请输入真实姓名');
        }
        if (empty($_GPC['mobile'])) {
            show_json(0, '请输入手机号码');
        }
        if ($_GPC['otype'] == 0) {
            $data = array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => $_GPC['mobile1'], 'isnotice' => $_GPC['isnotice']);
            pdo_update('rhinfo_zyxq_room', $data, array('weid' => $_W['uniacid'], 'id' => $item['id']));
        } else {
            $data = array('weid' => $_W['uniacid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id'], 'ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'otype' => $_GPC['otype'], 'isnotice' => $_GPC['isnotice']);
            pdo_insert('rhinfo_zyxq_room_mp', $data);
        }
        show_json(1);
    }
    include $this->mymtpl('enterroom');
} elseif ($operation == 'changeroom') {
    $mcurr = 'member';
    $room = $_GPC['room'];
    $roomarr = array();
    $roomarr = explode('-', $_GPC['room']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0], ':tid' => $roomarr[1], ':hid' => $roomarr[2]));
    if ($_W['isajax']) {
        if (empty($_GPC['ownername'])) {
            show_json(0, '请输入真实姓名');
        }
        if (empty($_GPC['mobile'])) {
            show_json(0, '请输入手机号码');
        }
        $data = array('ownername' => $_GPC['ownername'], 'mobile' => $_GPC['mobile'], 'mobile1' => '');
        $room_chg = array('weid' => $_W['uniacid'], 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id'], 'ownername' => $_GPC['oldownername'], 'mobile' => $_GPC['oldmobile'], 'newownername' => $_GPC['ownername'], 'newmobile' => $_GPC['mobile'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        pdo_update('rhinfo_zyxq_room', $data, array('weid' => $_W['uniacid'], 'id' => $item['id']));
        pdo_insert('rhinfo_zyxq_room_chglog', $room_chg);
        pdo_update('rhinfo_zyxq_member', array('deleted' => 1), array('weid' => $_W['uniacid'], 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id']));
        pdo_update('rhinfo_zyxq_room_mp', array('deleted' => 1), array('weid' => $_W['uniacid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id']));
        $this->mysyslog($item['pid'], $mydo, $operation, '房屋变更', '房屋变更id=' . $id);
        show_json(1);
    }
    include $this->mymtpl('changeroom');
} elseif ($operation == 'abnroom') {
    $mcurr = 'member';
    $room = $_GPC['room'];
    $roomarr = array();
    $roomarr = explode('-', $_GPC['room']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0], ':tid' => $roomarr[1], ':hid' => $roomarr[2]));
    if ($_W['isajax']) {
        if (empty($_GPC['content'])) {
            show_json(0, '请输入异常描述');
        }
        pdo_update('rhinfo_zyxq_room', array('isfree' => $_GPC['isfree']), array('weid' => $_W['uniacid'], 'id' => $item['id']));
        $room_abn = array('weid' => $_W['uniacid'], 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id'], 'content' => $_GPC['content'], 'remark' => $_GPC['remark'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zyxq_room_abnlog', $room_abn);
        $this->mysyslog($item['pid'], $mydo, $operation, '异常登记', '异常登记id=' . $id);
        show_json(1);
    }
    include $this->mymtpl('abnroom');
} elseif ($operation == 'selectfeeitem') {
    $mcurr = 'fee';
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor, title,id';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor, title,id';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    include $this->mymtpl('selectfeeitem');
} elseif ($operation == 'readmeter') {
    $mcurr = 'fee';
    $room = $_GPC['room'];
    $roomarr = array();
    $roomarr = explode('-', $_GPC['room']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0], ':tid' => $roomarr[1], ':hid' => $roomarr[2]));
    if ($_W['isajax']) {
        $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feeitem_building') . ' as f left join ' . tablename('rhinfo_zyxq_feelocation') . ' as l on f.flid=l.id where f.weid=:weid and l.category=0 and f.rid=:rid and f.bid=:bid and f.itemid=:itemid';
        $feeitem = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0], ':itemid' => $_GPC['feeitem']));
        if (!empty($feeitem)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and rid=:rid and id=:itemid';
            $fee_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':itemid' => $_GPC['feeitem']));
            $feeitem['price'] = $feeitem['price'] > 0 ? $feeitem['price'] : $fee_item['price'];
            $feeitem['title'] = $fee_item['title'];
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and rid=:rid and id=:itemid ';
            $feeitem = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':itemid' => $_GPC['feeitem']));
        }
        if (empty($feeitem)) {
            show_json(0, '收费项目错误');
        }
        $condition .= ' and rid=:rid and bid=:bid and tid=:tid and hid=:hid and itemid=:itemid ';
        $params[':rid'] = $item['rid'];
        $params[':bid'] = $item['bid'];
        $params[':tid'] = $item['tid'];
        $params[':hid'] = $item['id'];
        $params[':itemid'] = $_GPC['feeitem'];
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where feetype = 2 and ' . $condition;
        $enddate = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where feetype = 2 and ' . $condition . ' and enddate=:enddate';
        $params[':enddate'] = $enddate;
        $feebill = pdo_fetch($sql, $params);
        if (!empty($feebill)) {
            $data = array('weid' => $_W['uniacid'], 'pid' => $item['pid'], 'rid' => $item['rid'], 'bid' => $item['bid'], 'tid' => $item['tid'], 'hid' => $item['id'], 'address' => $item['title'], 'category' => 1, 'remark' => '手机抄表');
            $data['startdate'] = strtotime(substr($_GPC['daterange'], 0, 7));
            $data['enddate'] = strtotime(substr($_GPC['daterange'], 8, 7));
            $data['startqty'] = $feebill['endqty'];
            $data['endqty'] = floatval($_GPC['endqty']);
            $threeqty = $data['endqty'] - $data['startqty'];
            $data['threeqty'] = $threeqty > 0 ? $threeqty : 0;
            $data['price'] = $feeitem['price'] > 0 ? $feeitem['price'] : 0;
            $data['fee'] = $data['threeqty'] * $data['price'];
            $data['daterange'] = $_GPC['daterange'];
            $data['title'] = $feeitem['title'];
            $data['cuid'] = $_W['member']['uid'];
            $data['ctime'] = TIMESTAMP;
            $data['measure'] = $feeitem['measure'];
            $data['status'] = 1;
            $data['laterate'] = 0;
            $data['latedays'] = 0;
            $data['feetype'] = 2;
            $data['cfrom'] = 2;
            $data['itemid'] = $_GPC['feeitem'];
            if ($data['startdate'] >= $enddate && $data['enddate'] > $enddate) {
                if ($data['fee'] == 0) {
                    show_json(0, '金额为0');
                }
                pdo_insert('rhinfo_zyxq_feebill', $data);
                show_json(1);
            } else {
                show_json(0, '缴费周期错误');
            }
        }
        show_json(0, '还未建立初始读数');
    }
    $sql = 'select f.* from ' . tablename('rhinfo_zyxq_feeitem_building') . ' as f left join ' . tablename('rhinfo_zyxq_feelocation') . ' as l on f.flid=l.id where f.weid=:weid and l.category=0 and f.rid=:rid and f.bid=:bid';
    $feeitems = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0]));
    $temp_feeitems = array();
    if (!empty($feeitems)) {
        $isremove = false;
        $k = 0;
        while (!($k >= count($feeitems))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and rid=:rid and id=:itemid';
            $fee_item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':itemid' => $feeitems[$k]['itemid']));
            $feeitems[$k]['price'] = empty($feeitems[$k]['price']) ? $fee_item['price'] : $feeitems[$k]['price'];
            $feeitems[$k]['title'] = $fee_item['title'];
            if ($fee_item['calmethod'] != 6 || $fee_item['status'] == 2) {
                $isremove = true;
            }
            if ($isremove == false) {
                $temp_feeitems[] = $feeitems[$k];
            }
            ($k += 1) + -1;
        }
        $feeitems = $temp_feeitems;
    }
    if (empty($feeitems)) {
        $sql = 'select flid from ' . tablename('rhinfo_zyxq_building') . ' where weid=:weid and id=:bid';
        $flid = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':bid' => $roomarr[0]));
        $sql = 'select *,id as "itemid" from ' . tablename('rhinfo_zyxq_feeitem') . ' where weid=:weid and rid=:rid and bid=:flid and calmethod=6 and status=1';
        $feeitems = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':flid' => $flid));
    }
    include $this->mymtpl('readmeter');
} elseif ($operation == 'myreadmeter') {
    $mcurr = 'my';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition .= ' and rid=:rid and cuid=:cuid and cfrom=2';
        if (!empty($_GPC['room'])) {
            $room = explode('-', $_GPC['room']);
            $condition .= ' and bid=' . $room[0] . ' and tid=' . $room[1] . ' and hid=' . $room[2];
        }
        $params[':rid'] = $myrid;
        $params[':cuid'] = $_W['member']['uid'];
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc ' . $limit;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['url'] = $this->createMobileUrl('manage', array('op' => 'feebill', 'pid' => $list[$k]['pid'], 'rid' => $list[$k]['rid'], 'bid' => $list[$k]['bid'], 'tid' => $list[$k]['tid'], 'hid' => $list[$k]['hid'], 'category' => $list[$k]['category'], 'status' => $list[$k]['status']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by title,id';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by title,id';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    include $this->mymtpl('myreadmeter');
} elseif ($operation == 'selectcharge') {
    $mcurr = 'fee';
    if ($_W['ispost']) {
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'payfeeitem', 'room' => $_GPC['room'], 'myroom' => $_GPC['myroom'], 'rid' => $myrid)));
        exit(0);
    }
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $sql = 'select title,id from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and rid = :rid';
    $buildings = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $mybuilding = $buildings;
    $m = 0;
    while (!($m >= count($buildings))) {
        $sql = 'select title ,id from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and rid = :rid and bid = :bid';
        $units = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id']));
        $myunit[$buildings[$m]['id']] = $units;
        $n = 0;
        while (!($n >= count($units))) {
            if (!empty($region['roomfix'])) {
                $sql = 'select concat(title,\'' . $region['roomfix'] . '\') as title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor, title,id';
            } else {
                $sql = 'select title , id from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and rid = :rid and bid = :bid and tid=:tid order by floor,title,id';
            }
            $rooms = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $buildings[$m]['id'], ':tid' => $units[$n]['id']));
            $myroom[$units[$n]['id']] = $rooms;
            ($n += 1) + -1;
        }
        ($m += 1) + -1;
    }
    include $this->mymtpl('selectcharge');
} elseif ($operation == 'payfeeitem') {
    $mcurr = 'fee';
    $roomarr = array();
    $roomarr = explode('-', $_GPC['room']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
    $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $roomarr[0], ':tid' => $roomarr[1], ':hid' => $roomarr[2]));
    $sql = 'select f.itemid,l.title from ' . tablename('rhinfo_zyxq_feeitem_building') . ' as f left join ' . tablename('rhinfo_zyxq_feeitem') . ' as l on f.itemid=l.id where f.weid=:weid and f.rid=:rid and f.bid=:bid and l.status=1 and l.category=0';
    $items = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $room['rid'], ':bid' => $room['bid']));
    $sql = 'SELECT distinct f.itemid ,i.title FROM ' . tablename('rhinfo_zyxq_feebill') . ' as f left join ' . tablename('rhinfo_zyxq_feeitem') . ' as i on f.itemid=i.id where f.status=1 and  f.weid = :weid and f.rid = :rid and f.bid = :bid and f.tid = :tid and f.hid=:hid';
    $items1 = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $room['rid'], ':bid' => $room['bid'], ':tid' => $room['tid'], ':hid' => $room['id']));
    if (!empty($items1)) {
        $items = array_merge($items1, $items);
        $items = multi_array_unique($items);
    }
    $k = 0;
    while (!($k >= count($items))) {
        $items[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'currfee', 'pid' => $room['pid'], 'rid' => $room['rid'], 'bid' => $room['bid'], 'tid' => $room['tid'], 'hid' => $room['id'], 'itemid' => $items[$k]['itemid'], 'room' => $_GPC['room'], 'myroom' => $_GPC['myroom']));
        $items[$k]['icon'] = 'icon-money';
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and fee > payfee and weid = :weid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and itemid=:itemid';
        $items[$k]['total'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $room['rid'], ':bid' => $room['bid'], ':tid' => $room['tid'], ':hid' => $room['id'], ':itemid' => $items[$k]['itemid']));
        ($k += 1) + -1;
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and weid = :weid and rid = :rid and bid = :bid and tid=:tid and hid=:hid order by startdate';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $room['rid'], ':bid' => $room['bid'], ':tid' => $room['tid'], ':hid' => $room['id']));
    $tempbillid = array();
    $totalfee = 0;
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
        $feeitem = pdo_fetch($sql, array(':id' => $list[$k]['itemid'], ':weid' => $_W['uniacid']));
        if (!empty($feeitem['latedate'])) {
            if ($list[$k]['enddate'] > $feeitem['latedate']) {
                $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
            } else {
                $days = intval((strtotime(date('Y-m-d')) - $feeitem['latedate']) / 86400);
            }
        } else {
            $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
        }
        if ($days > $feeitem['latedays']) {
            if ($region['latemethod'] == 1) {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * ($days - $feeitem['latedays']) / 1000, 0);
            } else {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * $days / 1000, 0);
            }
        }
        array_push($tempbillid, $list[$k]['id']);
        $totalfee = $totalfee + $list[$k]['fee'] + $list[$k]['latefee'];
        ($k += 1) + -1;
    }
    $billid = implode('-', $tempbillid);
    include $this->mymtpl('payfeeitem');
} elseif ($operation == 'currfee') {
    $itemid = $_GPC['itemid'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and fee - payfee > 0 and weid = :weid and rid = :rid and bid = :bid and tid=:tid and hid=:hid and itemid=:itemid order by startdate';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':bid' => $_GPC['bid'], ':tid' => $_GPC['tid'], ':hid' => $_GPC['hid'], ':itemid' => $itemid));
    $tempbillid = array();
    $totalfee = 0;
    $k = 0;
    while (!($k >= count($list))) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
        $feeitem = pdo_fetch($sql, array(':id' => $list[$k]['itemid'], ':weid' => $_W['uniacid']));
        if (!empty($feeitem['latedate'])) {
            if ($list[$k]['enddate'] > $feeitem['latedate']) {
                $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
            } else {
                $days = intval((strtotime(date('Y-m-d')) - $feeitem['latedate']) / 86400);
            }
        } else {
            $days = intval((strtotime(date('Y-m-d')) - $list[$k]['enddate']) / 86400);
        }
        if ($days > $feeitem['latedays']) {
            if ($region['latemethod'] == 1) {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * ($days - $feeitem['latedays']) / 1000, 0);
            } else {
                $list[$k]['latefee'] = round($list[$k]['fee'] * $feeitem['laterate'] * $days / 1000, 0);
            }
        }
        array_push($tempbillid, $list[$k]['id']);
        $totalfee = $totalfee + $list[$k]['fee'] + $list[$k]['latefee'];
        ($k += 1) + -1;
    }
    $billid = implode('-', $tempbillid);
    include $this->mymtpl('currfeebill');
} elseif ($operation == 'charge') {
    $mcurr = 'fee';
    $fee = $_GPC['fee'];
    $billids = $_GPC['billids'];
    $paytype = $this->paytype;
    $paynopre = !empty($this->syscfg['paynopre']) ? $this->syscfg['paynopre'] : 'Pay';
    $sql = 'select max(payno) from' . tablename('rhinfo_zyxq_paylog') . ' where weid = :weid and payno like \'' . $paynopre . '%\'';
    $payno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
    $payno = createnum(substr($payno, strlen($paynopre), 14));
    $payno = $paynopre . $payno;
    if ($_W['isajax']) {
        $roomarr = array();
        $roomarr = explode('-', $_GPC['room']);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and id=:hid';
        $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $region['id'], ':bid' => $roomarr[0], ':tid' => $roomarr[1], ':hid' => $roomarr[2]));
        $payfees = floatval($_GPC['payfee']);
        if (!($payfees > 0)) {
            echo '支付金额错误';
            exit(0);
        }
        $itemtitle = '';
        $printdetail = '';
        $printdetail1 = '';
        $senddetail = '';
        $billidarray = explode('-', $billids);
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_property') . ' WHERE weid=:weid and id=:pid';
        $property = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $room['pid']));
        $data['weid'] = $_W['uniacid'];
        $data['uid'] = $_W['member']['uid'];
        $data['openid'] = $_W['openid'];
        $data['title'] = $region['title'] . $_GPC['myroom'];
        if ($_GPC['isscan'] == 1 && !empty($_GPC['auth_code'])) {
            if (substr($_GPC['auth_code'], 0, 2) == '28') {
                $data['paytype'] = $paytype[2];
                $mypaytype = 2;
            } else {
                $data['paytype'] = $paytype[1];
                $mypaytype = 1;
            }
        } else {
            $data['paytype'] = $paytype[$_GPC['paytype']];
            $mypaytype = $_GPC['paytype'];
        }
        $data['billid'] = $billid;
        $data['ctime'] = TIMESTAMP;
        $data['tid'] = $payno . random(8, 1);
        $data['payno'] = $payno;
        $data['fee'] = $payfees;
        $data['feetype'] = 101;
        $data['status'] = 0;
        $data['pid'] = $room['pid'];
        $data['rid'] = $room['rid'];
        pdo_insert('rhinfo_zyxq_paylog', $data);
        $logid = pdo_insertid();
        $pznopre = !empty($region['pznopre']) ? $region['pznopre'] : 'FEE';
        $sql = 'select max(printpznum) from' . tablename('rhinfo_zyxq_feebill') . ' where weid = :weid and printpznum like \'' . $pznopre . '%\'';
        $pzno = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
        $printpznum = createpznum(substr($pzno, strlen($pznopre), 12));
        $printpznum = $pznopre . $printpznum;
        if ($_GPC['isscan'] == 1) {
            $params = array('title' => $data['title'], 'tid' => $data['tid'], 'fee' => $data['fee']);
            $params['uniontid'] = $params['tid'];
            $params['total_amount'] = $params['fee'];
            $params['subject'] = $params['title'];
            $params['body'] = $_W['uniacid'] . ':2';
            $params['logid'] = $logid;
            $params['auth_code'] = $_GPC['auth_code'];
            $params['clientip'] = $_W['clientip'];
            $res = $this->my_scancode_pay($params, $property, $region);
            if ($res['errno'] == 1) {
                echo $res['message'];
                exit(0);
            }
        }
        $k = 0;
        while (!($k >= count($billidarray))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feebill') . ' where status=1 and id=:billid and weid = :weid';
            $bill = pdo_fetch($sql, array(':billid' => $billidarray[$k], ':weid' => $_W['uniacid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_feeitem') . ' where id = :id and weid = :weid';
            $feeitem = pdo_fetch($sql, array(':id' => $bill['itemid'], ':weid' => $_W['uniacid']));
            $bill_data = array('status' => 2, 'paytype' => $mypaytype, 'printpznum' => $printpznum, 'paydate' => TIMESTAMP, 'payuid' => $_W['member']['uid'], 'payno' => $payno);
            if (!empty($feeitem['latedate'])) {
                if ($bill['enddate'] > $feeitem['latedate']) {
                    $days = intval((strtotime(date('Y-m-d')) - $bill['enddate']) / 86400);
                } else {
                    $days = intval((strtotime(date('Y-m-d')) - $feeitem['latedate']) / 86400);
                }
            } else {
                $days = intval((strtotime(date('Y-m-d')) - $bill['enddate']) / 86400);
            }
            if ($days > $feeitem['latedays']) {
                if ($region['latemethod'] == 1) {
                    $bill_data['latefee'] = round($bill['fee'] * $feeitem['laterate'] * ($days - $feeitem['latedays']) / 1000, 0);
                } else {
                    $bill_data['latefee'] = round($bill['fee'] * $feeitem['laterate'] * $days / 1000, 0);
                }
                $bill_data['latedays'] = $days;
                $bill_data['laterate'] = $feeitem['laterate'];
                $bill['latefee'] = $bill_data['latefee'];
            }
            $bill_data['payfee'] = $bill['fee'] + $bill['latefee'];
            if ($payfees > $bill_data['payfee']) {
                $payfee = $bill_data['payfee'];
            } else {
                $payfee = $payfees;
            }
            $payfees -= $payfee;
            $bill_data['payfee'] = $payfee;
            pdo_update('rhinfo_zyxq_feebill', $bill_data, array('weid' => $_W['uniacid'], 'id' => $billidarray[$k]));
            $itemtitle .= $bill['title'];
            $printdetail .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . '\\n';
            $printdetail1 .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . '<BR>';
            $senddetail .= $bill['daterange'] . $bill['title'] . ':' . $bill['fee'] . ',';
            if ($bill['isalipay']) {
                $set = array();
                $set['app_id'] = $this->syspub['alipay_appid'];
                $set['prikey'] = $this->syspub['alipay_rsa2'];
                $set['app_auth_token'] = $region['lifepay_token'];
                $set['method'] = 'bill.delete';
                $params = "{\r\n\t\t\t\t\t\"community_id\":\"" . $region['lifepay_rid'] . "\",\r\n\t\t\t\t\t \"bill_entry_id_list\":[\r\n\t\t\t\t\t\t\"" . $bill['id'] . "\"\r\n\t\t\t\t\t\t]\r\n\t\t\t\t\t  }";
                my_alipay_life($set, $params);
            }
            ($k += 1) + -1;
        }
        $sql = 'select min(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and rid=:rid and id in(' . $billids . ')';
        $mindate = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $bill['rid']));
        $sql = 'select max(enddate) from ' . tablename('rhinfo_zyxq_feebill') . ' where weid=:weid and rid=:rid and id in(' . $billids . ')';
        $maxdate = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $bill['rid']));
        if ($mindate == $maxdate) {
            $daterange = date('Y-m', $maxdate);
            $senddetail = '';
        } else {
            $daterange = date('Y-m', $mindate) . '~' . date('Y-m', $maxdate);
        }
        pdo_update('rhinfo_zyxq_paylog', array('status' => 1, 'paytime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'id' => $logid));
        $paymethod = '支付方式:' . $paytype[intval($mypaytype)] . '\\n';
        $paymethod1 = '支付方式:' . $paytype[intval($mypaytype)] . '<BR>';
        $content = '<FB><center>缴费通知单</center></FB>\\n';
        $content .= '房产:' . $region['title'] . $_GPC['myroom'] . '\\n';
        $content .= '缴费类别:' . $itemtitle . '\\n';
        $content .= '缴费金额:' . $data['fee'] . '元\\n';
        $content .= '缴费周期:' . $daterange . '\\n';
        $content .= '缴费时间:' . date('Y-m-d h:m') . '\\n';
        $content .= '业主:' . $room['ownername'] . '\\n';
        $content1 = '<CB>缴费通知单</CB>\\n';
        $content1 .= '房产:' . $region['title'] . $_GPC['myroom'] . '<BR>';
        $content1 .= '缴费类别:' . $itemtitle . '<BR>';
        $content1 .= '缴费金额:' . $data['fee'] . '元<BR>';
        $content1 .= '缴费周期:' . $daterange . '\\n';
        $content1 .= '缴费时间:' . date('Y-m-d h:m') . '<BR>';
        $content1 .= '业主:' . $room['ownername'] . '<BR>';
        if ($region['isprintfeedetail'] == 1) {
            $content .= $printdetail;
            $content1 .= $printdetail1;
        }
        $content .= $paymethod . '<right>' . $property['title'] . '</right>';
        $content .= $paymethod1 . '<RIGHT>' . $property['title'] . '</RIGHT>';
        $this->send_print($bill['pid'], $bill['rid'], 3, urlencode($content), $content1);
        $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid=:weid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0 ';
        $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $room['rid'], ':bid' => $room['bid'], ':tid' => $room['tid'], ':hid' => $room['id']));
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的缴费我们已收到', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $region['title'] . $_GPC['myroom'] . ',' . $senddetail . '缴费总额' . $data['fee'] . '元，缴费周期' . $daterange, 'color' => $textcolor), 'remark' => array('value' => '感谢您的支持！'));
        $url = $this->createMobileurl('member', array('op' => 'myfee'));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid1'])) {
            $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
        }
        show_json(1, '收款成功');
    }
    include $this->mymtpl('charge');
} elseif ($operation == 'report') {
    include $this->mymtpl('report');
} elseif ($operation == 'secline') {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolline') . ' where weid=:weid and rid=:rid and status=1 ORDER BY starttime,id ';
    $list = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    $k = 0;
    while (!($k >= count($list))) {
        if (!empty($list[$k]['starttime']) && !empty($list[$k]['endtime'])) {
            $list[$k]['sectime'] = $list[$k]['starttime'] . '~' . $list[$k]['endtime'];
            if (date('H:i', TIMESTAMP) >= $list[$k]['starttime'] && !(date('H:i', TIMESTAMP) > $list[$k]['endtime'])) {
                $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'secmap', 'id' => $list[$k]['id']));
            } else {
                $list[$k]['url'] = '';
            }
        } else {
            $list[$k]['sectime'] = '不限时间';
            $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'secmap', 'id' => $list[$k]['id']));
        }
        ($k += 1) + -1;
    }
    include $this->mymtpl('secline');
} elseif ($operation == 'secmap') {
    $id = intval($_GPC['id']);
    if ($_W['isajax']) {
        $patid = intval($_GPC['patid']);
        $data_log = array('weid' => $_W['uniacid'], 'lineid' => $id, 'patid' => $patid, 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        if (!empty($_GPC['lat']) && !empty($_GPC['lng'])) {
            $ch = curl_init();
            $url = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $_GPC['lat'] . ',' . $_GPC['lng'] . '&get_poi=0&key=' . $sysconifg['qq_lbskey'];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($ch);
            curl_close($ch);
            $res = json_decode($res, true);
            if ($res['status'] == 0) {
                $result = $res['result'];
                $data_log['position'] = $result['formatted_addresses']['recommend'] ? $result['formatted_addresses']['recommend'] : $result['addresses'];
            }
        } else {
            show_json(0, '定位不成功');
        }
        $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid=:weid and id=:id';
        $patrolpos = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $patid));
        if ($patrolpos['lat'] != 0 && $patrolpos['lng'] != 0 && !empty($_GPC['lat']) && !empty($_GPC['lng'])) {
            $distance = GetDistance($_GPC['lat'], $_GPC['lng'], $patrolpos['lat'], $patrolpos['lng'], 2);
            if ($distance * 1000 > $patrolpos['distance'] && $patrolpos['distance'] > 0) {
                show_json(0, '亲，请您靠近打更点');
            } else {
                $data_log['distance'] = $distance * 1000;
            }
        } else {
            $data_log['distance'] = 0;
        }
        if ($_GPC['isimage'] == 1) {
            load()->func('file');
            $access_token = WeAccount::token();
            $media_id = $_GPC['media_id'];
            if (empty($media_id)) {
                show_json(0, '获取微信媒体参数失败');
            }
            $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
            $path = 'images/' . intval($_W['uniacid']) . '/' . date('Y/m/');
            $ext = 'jpg';
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);
            $target_temp = '../attachment/' . $path . 'temp_' . $filename;
            $tmp_file = IA_ROOT . '/attachment/' . $path . 'temp_' . $filename;
            $target_file = IA_ROOT . '/attachment/' . $path . $filename;
            $ch = curl_init($url);
            $fp = fopen($target_temp, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            if (file_exists($target_temp)) {
                $imagesize = $this->syscfg['imagesize'] > 1024 ? 640 : $this->syscfg['imagesize'];
                file_image_thumb($tmp_file, $target_file, $imagesize);
                unlink($tmp_file);
                $pathname = $path . $filename;
                $fileurl = trim($_W['attachurl'] . $pathname);
                if (!empty($_W['setting']['remote']['type'])) {
                    $remotestatus = file_remote_upload($pathname);
                    if (is_error($remotestatus)) {
                        $result['message'] = $remote['message'];
                        show_json(0, $result['message']);
                    } else {
                        $remoteurl = tomedia($pathname);
                        $fileurl = $remoteurl;
                    }
                }
                $data_log['image'] = $pathname;
                pdo_insert('core_attachment', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'filename' => 'rhinfo_zyxq_' . date('Ymd') . random(5, true) . '.jpg', 'attachment' => $pathname, 'type' => 1, 'createtime' => TIMESTAMP));
                pdo_insert('rhinfo_zyxq_patrollog', $data_log);
                show_json(1, '打更成功');
            } else {
                show_json(0, '上传图片失败');
            }
        } else {
            pdo_insert('rhinfo_zyxq_patrollog', $data_log);
            show_json(1, '打更成功');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolline') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $_W['uniacid']));
    $sql = 'select id,title,lat,lng from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid=:weid and id in(' . $item['positions'] . ')';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    $positionsarr = explode(',', $item['positions']);
    include $this->mymtpl('map');
} elseif ($operation == 'poscheck') {
    $patid = intval($_GPC['patid']);
    if (empty($_GPC['lat']) || empty($_GPC['lng'])) {
        show_json(0, '定位不成功');
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_patrolpos') . ' where weid=:weid and id=:id';
    $patrolpos = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $patid));
    if ($patrolpos['lat'] != 0 && $patrolpos['lng'] != 0 && !empty($_GPC['lat']) && !empty($_GPC['lng'])) {
        $distance = GetDistance($_GPC['lat'], $_GPC['lng'], $patrolpos['lat'], $patrolpos['lng'], 2);
        if ($distance * 1000 > $patrolpos['distance'] && $patrolpos['distance'] > 0) {
            show_json(0, '亲，请您靠近打更点');
        }
    }
    show_json(1);
} elseif ($operation == 'environ') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' weid=:weid and uid = :uid ';
        $params[':weid'] = $_W['uniacid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition . ' ORDER BY ctime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('environ');
} elseif ($operation == 'mysec') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' weid=:weid and uid = :uid ';
        $params[':weid'] = $_W['uniacid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition . ' ORDER BY ctime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('mysec');
} elseif ($operation == 'myenv') {
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' weid=:weid and uid = :uid ';
        $params[':weid'] = $_W['uniacid'];
        $params[':uid'] = $_W['member']['uid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select * from ' . tablename('rhinfo_zycj_charging_log') . ' where ' . $condition . ' ORDER BY ctime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('myenv');
}
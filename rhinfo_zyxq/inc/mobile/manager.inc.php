<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'carpay';
$mydo = 'manager';
$curr = 'manager';
$condition = ' weid = :weid ';
$params = array(':weid' => $_W['uniacid']);
$myurl = $this->createMobileurl($mydo);
$myrid = $_GPC['rid'];
$user = $this->getmanager($_W['member']['uid'], $myrid);
if ($_W['isajax']) {
    if ($user['ismanager'] == 1) {
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        $user['rtitle'] = $region['title'];
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
        $user['rtitle'] = $region['title'];
        if (empty($region)) {
            $this->mymsg('error', '温馨提示', '主体不存在.', 'close');
        }
    } else {
        $this->mymsg('error', '温馨提示', '本权限仅针对物业服务人员开放.', 'close');
    }
    $k = 0;
    $team = $user['rights'];
    $right = array(1, 3, 5, 11, 12, 13, 14, 15);
    $i = 1;
    while (!($i > 18)) {
        if ($team['right' . $i] == 1 && in_array($i, $right)) {
            ($k += 1) + -1;
        }
        ($i += 1) + -1;
    }
    if ($k == 0) {
        if (!($region['openid'] == $_W['openid'] || $region['uid'] == $_W['member']['uid'])) {
            $this->mymsg('error', '温馨提示', '本权限仅针对物业管理人员开放.', 'close');
        }
    }
}
if ($operation == 'carpay') {
    $status = $_GPC['status'];
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['paydate'])) {
            $condition .= ' and ctime<' . strtotime('+1 days', strtotime($_GPC['paydate']));
        }
        if ($status > 0) {
            $condition .= ' and category=:category ';
            $params[':category'] = $status;
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parkpay_log') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $fans = mc_fansinfo($list[$k]['cuid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['avatar'] = $fans['avatar'];
            $list[$k]['ctime'] = date('Y-m-d H:i:s', $list[$k]['ctime']);
            if ($list[$k]['category'] == 2) {
                $list[$k]['starttime'] = date('Y-m-d', $list[$k]['starttime']);
                $list[$k]['endtime'] = date('Y-m-d', $list[$k]['endtime']);
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('carpay');
} elseif ($operation == 'get_carpay') {
    $ctime1 = strtotime(date('Y-m-d', TIMESTAMP));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
    $sql1 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and rid=:rid and ctime between :ctime1 and :ctime2';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $today_carpay = pdo_fetchcolumn($sql1, $params);
    $today_carpay = empty($today_carpay) ? 0 : $today_carpay;
    $ctime1 = strtotime(date('Y-m-d', strtotime('-1 days')));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP));
    $sql2 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and rid=:rid and ctime between :ctime1 and :ctime2';
    $yestoday_carpay = pdo_fetchcolumn($sql2, $params);
    $yestoday_carpay = empty($yestoday_carpay) ? 0 : $yestoday_carpay;
    $ctime1 = strtotime(date('Y-m-d', strtotime('-1 months')));
    $ctime2 = strtotime(date('Y-m-d', TIMESTAMP + 1 * 3600 * 24));
    $sql3 = 'select sum(fee) from ' . tablename('rhinfo_zyxq_parkpay_log') . ' where weid = :weid and rid=:rid and ctime between :ctime1 and :ctime2';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid, ':ctime1' => $ctime1, ':ctime2' => $ctime2);
    $month_carpay = pdo_fetchcolumn($sql3, $params);
    $month_carpay = empty($month_carpay) ? 0 : $month_carpay;
    show_json(1, array('today_carpay' => $today_carpay, 'yestoday_carpay' => $yestoday_carpay, 'month_carpay' => $month_carpay));
} elseif ($operation == 'parking') {
    $status = $_GPC['status'];
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid and category=0 ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and (title like \'%' . $_GPC['keyword'] . '%\' or mobile like \'%' . $_GPC['keyword'] . '%\')';
        }
        if ($status == 3) {
            $condition .= ' and status=:status and enddate<' . strtotime('+1 months');
            $params[':status'] = 1;
        } else {
            $condition .= ' and status=:status ';
            $params[':status'] = $status;
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where category=0 and ' . $condition, $params);
        $condition .= ' order by title*1,id LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_parking') . ' where category=0 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['ctime'] = date('Y-m-d H:i:s', $list[$k]['ctime']);
            if ($list[$k]['status'] > 0) {
                $list[$k]['startdate'] = date('Y-m-d', $list[$k]['startdate']);
                $list[$k]['enddate'] = date('Y-m-d', $list[$k]['enddate']);
            }
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('parking');
} elseif ($operation == 'get_parking') {
    $sql1 = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and rid=:rid and category=0 and status=0';
    $sql2 = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and rid=:rid and category=0 and status=1';
    $sql3 = 'select count(*) from ' . tablename('rhinfo_zyxq_parking') . ' where weid = :weid and rid=:rid and category=0 and status=2';
    $params = array(':weid' => $_W['uniacid'], ':rid' => $myrid);
    $parking0 = pdo_fetchcolumn($sql1, $params);
    $parking1 = pdo_fetchcolumn($sql2, $params);
    $parking2 = pdo_fetchcolumn($sql3, $params);
    show_json(1, array('parking0' => $parking0, 'parking1' => $parking1, 'parking2' => $parking2));
} elseif ($operation == 'devpatrol') {
    include $this->mymtpl();
} elseif ($operation == 'devtasklist') {
    $condition .= ' and (rid=:rid or rid=0)';
    $params[':rid'] = $myrid;
    if (!empty($_GPC['status'])) {
        $condition .= ' and cycle=:cycle';
        $params[':cycle'] = $_GPC['status'];
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_task') . ' where ' . $condition;
    $list = pdo_fetchall($sql, $params);
    if (empty($list)) {
        exit('');
    }
    $k = 0;
    while (!($k >= count($list))) {
        if ($list[$k]['cycle'] == '1') {
            $list[$k]['image'] = RHINFO_ZYXQ_LOCAL . 'static/mobile/images/day.png';
        } elseif ($list[$k]['cycle'] == '2') {
            $list[$k]['image'] = RHINFO_ZYXQ_LOCAL . 'static/mobile/images/week.png';
        } elseif ($list[$k]['cycle'] == '3') {
            $list[$k]['image'] = RHINFO_ZYXQ_LOCAL . 'static/mobile/images/month.png';
        } elseif ($list[$k]['cycle'] == '4') {
            $list[$k]['image'] = RHINFO_ZYXQ_LOCAL . 'static/mobile/images/year.png';
        }
        $list[$k]['url'] = mymurl('manager/devtask', array('taskid' => $list[$k]['id']));
        ($k += 1) + -1;
    }
    include $this->mymtpl();
} elseif ($operation == 'devtask') {
    $condition .= ' and id=:taskid ';
    $params[':taskid'] = $_GPC['taskid'];
    $sql = 'select * from ' . tablename('rhinfo_zyxq_devpatrol_task') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    $devitems = iunserializer($item['itemstr']);
    $check_items = array();
    $k = 0;
    while (!($k >= count($devitems))) {
        $device = pdo_get('rhinfo_zyxq_devpatrol_device', array('weid' => $_W['uniacid'], 'id' => $devitems[$k]));
        $supplier = pdo_get('rhinfo_zyxq_devsupplier', array('weid' => $_W['uniacid'], 'id' => $device['suppid']), array('title', 'thumb'));
        $device['supptitle'] = $supplier['title'];
        $device['suppthumb'] = $supplier['thumb'];
        $device['url'] = mymurl('manager/devdetail', array('devid' => $devitems[$k], 'taskid' => $_GPC['taskid']));
        $check_items[] = $device;
        ($k += 1) + -1;
    }
    include $this->mymtpl();
} elseif ($operation == 'devdetail') {
    $device = pdo_get('rhinfo_zyxq_devpatrol_device', array('weid' => $_W['uniacid'], 'id' => $_GPC['devid']));
    $itemarr = iunserializer($device['itemstr']);
    if ($_W['isajax']) {
        $data = array('weid' => $_W['uniacid'], 'taskid' => $_GPC['taskid'], 'deviceid' => $_GPC['devid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
        $i = 0;
        $k = 0;
        while (!($k >= count($itemarr))) {
            $cateitem = pdo_get('rhinfo_zyxq_devpatrol_cateitem', array('weid' => $_W['uniacid'], 'id' => $itemarr[$k]));
            if (empty($_GPC['item_' . $itemarr[$k]])) {
                show_json(0, $cateitem['title'] . '不能为空');
            }
            $data['itemtype'] = $cateitem['itemtype'];
            $data['itemtitle'] = $cateitem['title'];
            $data['itemdesc'] = $cateitem['desc'];
            if ($cateitem['itemtype'] == 'image') {
                $data['itemimage'] = $_GPC['item_' . $itemarr[$k]];
            } else {
                $data['itemcontent'] = $_GPC['item_' . $itemarr[$k]];
            }
            $res = pdo_insert('rhinfo_zyxq_devpatrol_content', $data);
            if ($res) {
                ($i += 1) + -1;
            }
            ($k += 1) + -1;
        }
        if ($i > 0) {
            show_json(1, '提交成功');
        } else {
            show_json(0, '提交失败');
        }
    }
    $supplier = pdo_get('rhinfo_zyxq_devsupplier', array('weid' => $_W['uniacid'], 'id' => $device['suppid']), array('title'));
    $cateitems = array();
    $k = 0;
    while (!($k >= count($itemarr))) {
        $cateitem = pdo_get('rhinfo_zyxq_devpatrol_cateitem', array('weid' => $_W['uniacid'], 'id' => $itemarr[$k]));
        if ($cateitem['itemtype'] == 'radio' || $cateitem['itemtype'] == 'checkbox' || $cateitem['itemtype'] == 'select') {
            $cateitem['itemvalue'] = explode('|', $cateitem['value']);
        }
        $cateitems[] = $cateitem;
        ($k += 1) + -1;
    }
    include $this->mymtpl();
} elseif ($operation == 'selfdevice') {
    include $this->mymtpl();
} elseif ($operation == 'repairp') {
    $mcurr = 'steward';
    if ($_W['isajax']) {
        $data = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition .= ' and rid=:rid ';
        $params[':rid'] = $myrid;
        if (!empty($_GPC['keyword'])) {
            $condition .= ' and content like "%' . $_GPC['keyword'] . '%"';
        }
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repairp') . ' where status<9 and ' . $condition, $params);
        $condition .= ' order by status, ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repairp') . ' where status<9 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 5 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $list[$k]['id']));
            if ($region['finishdays'] > 0 && $list[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $list[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update('rhinfo_zyxq_repair', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['status'] = 8;
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
                        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '内部工单处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，内部工单内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此内部工单超时未处理，请速安排处理，谢谢！'));
                        $url = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $list[$k]['id']));
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
                        $sql = 'update ' . tablename('rhinfo_zyxq_repairp') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                        pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $data[$k]['id']));
                    }
                }
            }
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
    include $this->mymtpl('repairp');
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
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repairp') . ' where status<9 and ' . $condition, $params);
        $condition .= ' order by status, ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repairp') . ' where status<9 and ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid']));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 5 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $list[$k]['id']));
            if ($region['finishdays'] > 0 && $list[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $list[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update('rhinfo_zyxq_repairp', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $list[$k]['id']));
                    $list[$k]['status'] = 8;
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
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '内部工单处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $list[$k]['address'] . '，内部工单内容：' . $list[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此内部工单超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $list[$k]['id']));
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
                    $sql = 'update ' . tablename('rhinfo_zyxq_repairp') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $data[$k]['id']));
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
} elseif ($operation == 'repair') {
    if ($_W['isajax'] && !empty($_GPC['cid'])) {
        $images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('rid' => $_GPC['rid'], 'content' => $_GPC['content'], 'cid' => $_GPC['cid'], 'images' => $images, 'weid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'pid' => $region['pid'], 'openid' => $_W['openid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'address' => '', 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_repairp', $data);
        $id = pdo_insertid();
        if ($res) {
            show_json(1, array('id' => $id, 'msg' => '提交成功'));
        } else {
            show_json(0, '提交失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 5 and rid=:rid';
    $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
    include $this->mymtpl('repair');
} elseif ($operation == 'repairreply') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $fans = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
    $reply_images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
    $status = intval($_GPC['status']);
    $data = array('rid' => $id, 'content' => $_GPC['content'], 'image' => $reply_images, 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'ctime' => TIMESTAMP);
    $res = pdo_insert('rhinfo_zyxq_repairp_record', $data);
    if ($res) {
        if (empty($repair['getuid'])) {
            pdo_update('rhinfo_zyxq_repairp', array('getuid' => $_W['member']['uid'], 'getopenid' => $_W['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        }
        pdo_update('rhinfo_zyxq_repairp', array('status' => $status, 'lasttime' => TIMESTAMP), array('weid' => $_W['uniacid'], 'id' => $id));
        show_json(1);
    } else {
        show_json(0, '提交失败');
    }
} elseif ($operation == 'repairfinish') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $res = pdo_update('rhinfo_zyxq_repairp', array('status' => 8), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid']));
        if ($res) {
            show_json(1);
        } else {
            show_json(0, '提交失败');
        }
    }
} elseif ($operation == 'repairtrack') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        $reply_images = is_array($_GPC['images']) ? iserializer($_GPC['images']) : iserializer(array());
        $data = array('rid' => $id, 'content' => $_GPC['content'], 'image' => $reply_images, 'weid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['member']['uid'], 'nickname' => $user['nickname'], 'headimgurl' => $user['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_repairp_record', $data);
        if ($res) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
            $catetpl = pdo_fetch($sql, array(':id' => $repair['cid'], ':weid' => $_W['uniacid']));
            if (!empty($catetpl['topcolor'])) {
                $topcolor = $catetpl['topcolor'];
            } else {
                $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            }
            if (!empty($catetpl['textcolor'])) {
                $textcolor = $catetpl['textcolor'];
            } else {
                $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            }
            $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => $user['ownername'], 'color' => $textcolor), 'keyword2' => array('value' => $user['mobile'], 'color' => $textcolor), 'keyword3' => array('value' => $user['address'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快去处理.'));
            $url = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $id));
            $url = $this->my_mobileurl($url);
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid']));
            if ($region['repairpnotice'] == 1) {
                $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.pid = :pid and t.rid = :rid and c.right1=1 and c.tplid="1"';
                $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
                $k = 0;
                while (!($k >= count($teams))) {
                    if (!empty($this->syscfg['tplid2'])) {
                        $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
                    } else {
                        $this->send_mycusnewsmsg($catetpl['title'], '请尽快去处理.', $url, '', $teams[$k]['openid']);
                    }
                    ($k += 1) + -1;
                }
            } elseif (!empty($this->syscfg['tplid2'])) {
                $this->send_mysendtplnotice($catetpl['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg($catetpl['title'], '请尽快去处理.', $url, '', $catetpl['openid']);
            }
            show_json(1);
        } else {
            show_json(0, '提交失败');
        }
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 5 and pid=:pid and rid=:rid';
    $cate = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $user['pid'], ':rid' => $user['rid']));
    $images = iunserializer($repair['images']);
    if ($repair['status'] == 0) {
        $repair['css'] = 'text-danger';
        $repair['statustext'] = '待处理';
    } elseif ($repair['status'] == 1) {
        $repair['css'] = 'text-danger';
        $repair['statustext'] = '待处理';
    } elseif ($repair['status'] == 2) {
        $repair['css'] = 'text-warning';
        $repair['statustext'] = '处理中';
    } elseif ($repair['status'] == 3) {
        $repair['css'] = 'text-green';
        $repair['statustext'] = '已处理';
    } elseif ($repair['status'] == 8) {
        $repair['css'] = 'text-default';
        $repair['statustext'] = '已结案';
    } elseif ($repair['status'] == 5) {
        $repair['css'] = 'text-warning';
        $repair['statustext'] = '已回复';
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp_record') . ' where weid=:weid and rid=:id';
    $repair_records = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $k = 0;
    while (!($k >= count($repair_records))) {
        $repair_records[$k]['images'] = iunserializer($repair_records[$k]['image']);
        $repair_records[$k]['ctime'] = timeBefore($repair_records[$k]['ctime']);
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
    $catetpl = pdo_fetch($sql, array(':id' => $repair['cid'], ':weid' => $_W['uniacid']));
    $isview = false;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and id=:hid';
    $room = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid'], ':hid' => $repair['hid']));
    $housename = $region['title'] . $building . $unit . $room;
    $sql = 'SELECT t.*,c.right1,c.right11 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4 and (t.openid = :openid or t.uid=:uid)';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
    $repairmethod = 0;
    if ($region['repairpnotice'] == 1 && $team['right1'] == 1) {
        $repairmethod = 1;
        include $this->mymtpl('repairreply');
    } elseif (($region['repairpnotice'] == 2 || $region['repairpnotice'] == 3) && $team['right11'] == 1) {
        $repairmethod = 2;
        $sql = 'SELECT t.*,c.right1 FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid = :rid and c.type=4';
        $repairusers = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $repair['rid']));
        include $this->mymtpl('repairreply');
    } else {
        include $this->mymtpl('repairtrack');
    }
} elseif ($operation == 'repairmtrack') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $repair = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
    $region = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
    $building = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and id=:tid';
    $unit = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid']));
    $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid and id=:hid';
    $room = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $repair['pid'], ':rid' => $repair['rid'], ':bid' => $repair['bid'], ':tid' => $repair['tid'], ':hid' => $repair['hid']));
    $housename = $region . $building . $unit . $room;
    include $this->mymtpl('repairmtrack');
} elseif ($operation == 'repairrob') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($item['getuid']) {
        show_json(0, '抢单失败');
    } else {
        pdo_update('rhinfo_zyxq_repairp', array('getuid' => $_W['member']['uid'], 'getopenid' => $_W['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        show_json(1, '抢单成功');
    }
} elseif ($operation == 'repairtake') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and id=:id';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $_GPC['teamid']));
    if ($item['getuid']) {
        show_json(0, '派单失败');
    } else {
        pdo_update('rhinfo_zyxq_repairp', array('getuid' => $team['uid'], 'getopenid' => $team['openid']), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
        $catetpl = pdo_fetch($sql, array(':id' => $item['cid'], ':weid' => $_W['uniacid']));
        if (!empty($catetpl['topcolor'])) {
            $topcolor = $catetpl['topcolor'];
        } else {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        }
        if (!empty($catetpl['textcolor'])) {
            $textcolor = $catetpl['textcolor'];
        } else {
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        }
        $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => '物业内部工单', 'color' => $textcolor), 'keyword2' => array('value' => '物业内部工单', 'color' => $textcolor), 'keyword3' => array('value' => '物业内部工单', 'color' => $textcolor), 'keyword4' => array('value' => $item['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快去处理.'));
        $url = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $item['id']));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid2'])) {
            $this->send_mysendtplnotice($team['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
        } else {
            $this->send_mycusnewsmsg($catetpl['title'], '请尽快去处理.', $url, '', $team['openid']);
        }
        show_json(1, '派单成功');
    }
} elseif ($operation == 'repairtakemuti') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and id in(' . $_GPC['teamid'] . ')';
    $teams = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    if (!empty($item['getmutiuid'])) {
        show_json(0, '派单失败');
    } else {
        pdo_update('rhinfo_zyxq_repairp', array('getmutiuid' => $_GPC['teamid']), array('weid' => $_W['uniacid'], 'id' => $id));
        $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
        $catetpl = pdo_fetch($sql, array(':id' => $item['cid'], ':weid' => $_W['uniacid']));
        if (!empty($catetpl['topcolor'])) {
            $topcolor = $catetpl['topcolor'];
        } else {
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        }
        if (!empty($catetpl['textcolor'])) {
            $textcolor = $catetpl['textcolor'];
        } else {
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        }
        $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => $_W['member']['nickname'], 'color' => $textcolor), 'keyword2' => array('value' => '物业内部工单', 'color' => $textcolor), 'keyword3' => array('value' => '物业内部工单', 'color' => $textcolor), 'keyword4' => array('value' => $item['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快去处理.'));
        $url = $this->createMobileurl($mydo, array('op' => 'repairtrack', 'id' => $item['id']));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid2'])) {
            $k = 0;
            while (!($k >= count($teams))) {
                $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
                ($k += 1) + -1;
            }
        } else {
            $k = 0;
            while (!($k >= count($teams))) {
                $this->send_mycusnewsmsg($catetpl['title'], '请尽快去处理.', $url, '', $teams[$k]['openid']);
                ($k += 1) + -1;
            }
        }
        show_json(1, '派单成功');
    }
} elseif ($operation == 'visitscan') {
    $visitid = intval($_GPC['visitid']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_door_visit') . ' where weid=:weid and id=:id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $visitid));
    if ($_W['isajax']) {
        if (!($item['effetime'] >= TIMESTAMP)) {
            show_json(0, '访客有效期已过');
        }
        if (!($item['opentimes'] >= 1)) {
            show_json(0, '访客次数已用完');
        }
        $sql = 'update ' . tablename('rhinfo_zyxq_door_visit') . ' set opentimes=opentimes - 1 where weid = :weid and id=:id and status=1';
        pdo_query($sql, array(':weid' => $_W['uniacid'], ':id' => $visitid));
        show_json(1, '放行确认成功');
    }
    include $this->mymtpl('visitscan');
} elseif ($operation == 'myrepairp') {
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
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_repairp') . ' where ' . $condition, $params);
        $condition .= ' order by ctime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_repairp') . ' where ' . $condition;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 5 and pid=:pid and rid=:rid and id=:cid';
            $category = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['pid'], ':rid' => $list[$k]['rid'], ':cid' => $list[$k]['cid']));
            $fans = array();
            $fans = mc_fansinfo($list[$k]['openid'], $_W['acid'], $_W['uniacid']);
            $list[$k]['thumb'] = $fans['avatar'];
            $list[$k]['thumb'] = empty($list[$k]['thumb']) ? MODULE_URL . 'static/mobile/images/head.jpg' : $list[$k]['thumb'];
            $list[$k]['ctime'] = date('Y-m-d H:i', $list[$k]['ctime']);
            $list[$k]['title'] = $category['title'];
            $list[$k]['url'] = $this->createMobileurl('manager', array('op' => 'repairtrack', 'id' => $list[$k]['id']));
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
    include $this->mymtpl('myrepairp');
} elseif ($operation == 'audit') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and id = :id';
    $member = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if ($_W['isajax']) {
        if ($_GPC['mstatus'] == 9) {
            $res = pdo_update('rhinfo_zyxq_member', array('deleted' => 1), array('weid' => $_W['uniacid'], 'id' => $id));
            if ($res) {
                show_json(1, '操作成功');
            } else {
                show_json(0, '操作失败');
            }
        }
        $data = array('status' => $_GPC['mstatus'], 'reason' => $_GPC['reason'], 'audituid' => $_W['member']['uid'], 'auditopenid' => $_W['openid'], 'audituid' => $_W['member']['uid']);
        $res = pdo_update('rhinfo_zyxq_member', $data, array('weid' => $_W['uniacid'], 'id' => $id));
        if ($res) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid=:pid and rid=:rid and bid=:bid and tid=:tid and id = :hid';
            $room = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':pid' => $member['pid'], ':rid' => $member['rid'], ':bid' => $member['bid'], ':tid' => $member['tid'], ':hid' => $member['hid']));
            if (empty($room['ownername']) && $member['otype'] == 0) {
                pdo_update('rhinfo_zyxq_room', array('ownername' => $member['realname'], 'mobile' => $member['mobile']), array('weid' => $_W['uniacid'], 'pid' => $member['pid'], 'rid' => $member['rid'], 'bid' => $member['bid'], 'tid' => $member['tid'], 'id' => $member['hid']));
            }
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid = :rid and bid=:bid and tid=:tid and openid=:openid and hid=:hid and deleted=1';
            $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $member['pid'], ':rid' => $member['rid'], ':bid' => $member['bid'], ':tid' => $member['tid'], ':openid' => $member['openid'], ':hid' => $member['hid']));
            if ($region['bindcredit'] > 0 && $_GPC['mstatus'] == 0 && $total == 0) {
                $crediturl = $this->createMobileurl('service', array('op' => 'credit1'));
                $crediturl = $this->my_mobileurl($crediturl);
                mc_credit_update($member['uid'], 'credit1', $region['bindcredit'], array(0, '绑定房产,赠送' . $region['bindcredit'] . '积分', 'rhinfo_zyxq'));
                mc_notice_credit1($member['openid'], $member['uid'], $region['bindcredit'], '绑定房产,赠送' . $region['bindcredit'] . '积分', $crediturl, '谢谢支持，点击查看详情');
            }
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            if ($region['bindstrategyid'] > 0 && $_GPC['mstatus'] == 0 && $total == 0) {
                $redpacket = $this->send_redpacket($region['bindstrategyid'], $member['openid'], 1);
                if (!is_error($redpacket)) {
                }
                $sql = 'select * from ' . tablename('rhinfo_zycj_redpacket_share') . ' where (to=:to or touid=:uid) and weid=:weid and status=1';
                $redshare = pdo_fetch($sql, array(':to' => $member['openid'], ':uid' => $member['uid'], ':weid' => $_W['uniacid']));
                if (!empty($redshare)) {
                    $shareres = $this->send_redpacket($redshare['redid'], $redshare['from'], 0);
                    if (!is_error($shareres)) {
                        $postdata = array('first' => array('value' => '有人通过您的邀请，并成功绑定房产'), 'keyword1' => array('value' => $member['realname'], 'color' => $topcolor), 'keyword2' => array('value' => $member['address'], 'color' => $textcolor), 'keyword3' => array('value' => date('Y-m-d H:i', $member['ctime']), 'color' => $textcolor), 'remark' => array('value' => '恭喜您获得一个答谢红包，点击领取吧'));
                        if (!empty($this->syscfg['redpackettplid'])) {
                            $this->send_mysendtplnotice($redshare['from'], $this->syscfg['redpackettplid'], $postdata, $this->my_mobileurl($shareres['url']), $topcolor);
                        }
                    }
                }
            }
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => $_GPC['mstatus'] == 2 ? '审核不通过' : '审核通过', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $member['address'], 'color' => $textcolor), 'remark' => array('value' => $_GPC['mstatus'] == 2 ? '非常抱歉，您绑定的房产审核不通过，原因：' . $_GPC['reason'] : '恭喜您，您绑定的房产已经审核通过!'));
            $url = $this->createMobileUrl('member', array('op' => 'myhouse'));
            $url = $this->my_mobileurl($url);
            $url = $_GPC['mstatus'] == 2 ? '' : $url;
            if (!empty($this->syscfg['tplid1']) && $member['openid']) {
                $url = !empty($redpacket['url']) ? $this->my_mobileurl($redpacket['url']) : $url;
                $this->send_mysendtplnotice($member['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
            }
            if (!($_GPC['mstatus'] == 2)) {
                $car_data = array('weid' => $_W['uniacid'], 'pid' => $region['pid'], 'rid' => $region['id'], 'title' => $member['carno'], 'carno' => $member['carno'], 'ownername' => $member['realname'], 'mobile' => $member['mobile'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'cuid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
                $mycar_data = array('weid' => $_W['uniacid'], 'uid' => $member['uid'], 'openid' => $member['openid'], 'carno' => $member['carno'], 'ctime' => TIMESTAMP);
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_car') . ' where weid=:weid and pid=:pid and rid=:rid and carno=:carno and deleted=0';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $region['pid'], ':rid' => $region['id'], ':carno' => $member['carno']));
                if (!($total > 0)) {
                    pdo_insert('rhinfo_zyxq_car', $car_data);
                }
                $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_mycar') . ' where weid=:weid and (openid=:openid or uid=:uid) and carno=:carno';
                $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':openid' => $member['openid'], ':uid' => $_W['member']['uid'], ':carno' => $member['carno']));
                if (!($total > 0)) {
                    pdo_insert('rhinfo_zyxq_mycar', $mycar_data);
                }
            }
            show_json(1, '审核完成.');
        } else {
            show_json(0, '审核失败.');
        }
    }
    $otype = array('0' => '业主', '1' => '成员', '2' => '租户');
    $member['otype'] = $otype[$member['otype']];
    include $this->mymtpl('audit');
}
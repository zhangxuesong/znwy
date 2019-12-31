<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'suggest';
$tablename = 'rhinfo_zyxq_suggest';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '投诉管理';
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '投诉列表';
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'select id,title,pid from ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
    $regions = pdo_fetchall($sql, $params);
    $mybuilding = array();
    $myshoplocation = array();
    $myparklocation = array();
    $mycategory = array();
    $m = 0;
    while (!($m >= count($regions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
        $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $mybuilding[$regions[$m]['id']] = $buildings;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=1';
        $shoplocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myshoplocation[$regions[$m]['id']] = $shoplocations;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_location') . ' where weid = :weid and pid = :pid and rid = :rid and category=2';
        $parklocations = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $regions[$m]['pid'], ':rid' => $regions[$m]['id']));
        $myparklocation[$regions[$m]['id']] = $parklocations;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and rid = :rid and type=3';
        $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $regions[$m]['id']));
        $mycategory[$regions[$m]['id']] = $categorys;
        ($m += 1) + -1;
    }
    if (!empty($_GPC['rid'])) {
        $condition .= ' AND rid= ' . $_GPC['rid'];
    }
    if (!empty($_GPC['bid'])) {
        $condition .= ' AND bid= ' . $_GPC['bid'];
    }
    if (!empty($_GPC['cid'])) {
        $condition .= ' AND cid = ' . $_GPC['cid'];
    }
    if (!empty($_GPC['reporttimes'])) {
        if ($_GPC['reporttimes'] == 2) {
            $condition .= ' AND reporttimes > 0 ';
        } else {
            $condition .= ' AND reporttimes = 0 ';
        }
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (content LIKE \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['status'])) {
        if ($_GPC['status'] == 1) {
            $condition .= ' AND status <= ' . $_GPC['status'];
        } else {
            $condition .= ' AND status = ' . $_GPC['status'];
        }
    }
    $suggestdate = $_GPC['suggestdate'];
    if ($suggestdate) {
        $starttime = strtotime($suggestdate['start']);
        $endtime = strtotime($suggestdate['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        if (!empty($mywe['rid'])) {
            $myregion = pdo_get('rhinfo_zyxq_region', array('weid' => $mywe['weid'], 'id' => $mywe['rid']), array('repairsort'));
            if ($myretion['repairsort'] == 1) {
                $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' order by id desc ' . $limit;
            } else {
                $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' order by status,id desc ' . $limit;
            }
        } else {
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' order by status,id desc ' . $limit;
        }
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid and :weid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
            $data[$k]['regionname'] = $region['title'];
            $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_category') . ' where weid and :weid and id = :cid';
            $mycategory = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':cid' => $data[$k]['cid']));
            $data[$k]['catename'] = $mycategory['title'];
            $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and (openid=:openid or uid=:uid)';
            $team = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':openid' => $data[$k]['getopenid'], ':uid' => $data[$k]['getuid']));
            if (!empty($team)) {
                $data[$k]['getrealname'] = $team['realname'];
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest_record') . ' where weid = :weid and sid = :sid and (openid<>:openid or uid<>:uid)';
                $record = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':sid' => $data[$k]['id'], ':openid' => $data[$k]['openid'], ':uid' => $data[$k]['uid']));
                $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid)';
                $team = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':openid' => $record['openid'], ':uid' => $record['uid']));
                if (!empty($team)) {
                    $data[$k]['getrealname'] = $team['realname'];
                } else {
                    $data[$k]['getrealname'] = '';
                }
            }
            if ($region['finishdays'] > 0 && $data[$k]['status'] == 3) {
                $timediff = TIMESTAMP - $data[$k]['lasttime'];
                $days = intval($timediff / 86400);
                if ($days > $region['finishdays']) {
                    pdo_update($tablename, array('status' => 8), array('weid' => $mywe['weid'], 'id' => $data[$k]['id']));
                    $data[$k]['status'] = 8;
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '您的投诉建议已自动结案', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $data[$k]['address'] . '，报修内容：' . $data[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '快去给该投诉建议处理做个评价吧，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'suggestcomment', 'suggestid' => $data[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    if (!empty($this->syscfg['tplid1'])) {
                        $this->send_mysendtplnotice($data[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                    }
                }
            }
            if ($data[$k]['status'] == '0' || $data[$k]['status'] == '1') {
                if (empty($data[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $data[$k]['ctime']) / 3600) - $data[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $data[$k]['reporttime']) / 3600) - ($data[$k]['reporttimes'] - 1) * 24;
                }
                if ($mycategory['reporttime'] > 0 && !($mycategory['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '投诉建议处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $data[$k]['address'] . '，投诉建议内容：' . $data[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此投诉建议超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $data[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $data[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_suggest') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $mywe['weid'], ':id' => $data[$k]['id']));
                }
            }
            if ($data[$k]['status'] == '0' || $data[$k]['status'] == '1') {
                $data[$k]['statustxt'] = '待处理';
                $data[$k]['getrealname'] = '';
            } elseif ($data[$k]['status'] == '2') {
                $data[$k]['statustxt'] = '处理中';
            } elseif ($data[$k]['status'] == '3') {
                $data[$k]['statustxt'] = '已处理';
            } elseif ($data[$k]['status'] == '8') {
                $data[$k]['statustxt'] = '已结案';
            } elseif ($data[$k]['status'] == '5') {
                $data[$k]['statustxt'] = '已回复';
            } elseif ($data[$k]['status'] == '9') {
                $data[$k]['statustxt'] = '不显示';
            }
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'regionname' => '所属主体', 'address' => '房产', 'catename' => '投诉类别', 'content' => '投诉内容', 'nickname' => '投诉人', 'getrealname' => '处理人', 'statustxt' => '状态', 'reporttimes' => '超时次数', 'ctime' => '投诉时间');
            export_excel($data, $filter, '投诉明细');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'view') {
    $current = '查看投诉';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $images = iunserializer($item['images']);
    $sql = 'select title from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 3 and id=:id';
    $cname = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':id' => $item['cid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest_record') . ' where weid = :weid and sid = :sid';
    $records = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':sid' => $item['id']));
    $k = 0;
    while (!($k >= count($records))) {
        $records[$k]['images'] = iunserializer($records[$k]['image']);
        ($k += 1) + -1;
    }
    include $this->mywtpl('view');
} elseif ($operation == 'delete') {
    $current = '删除投诉';
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
} elseif ($operation == 'deletepost') {
    $current = '删除投诉回复';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_suggest_record', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'add') {
    $current = '新增投诉建议';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'tid' => $_GPC['tid'], 'hid' => $_GPC['hid'], 'cid' => $_GPC['cid'], 'content' => $_GPC['content'], 'status' => $_GPC['status'], 'uid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id=:rid';
        $region = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and id=:bid';
        $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':bid' => $_GPC['bid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and id=:tid';
        $unit = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':tid' => $_GPC['tid']));
        $sql = 'select title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and id=:hid';
        $room = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':hid' => $_GPC['hid']));
        $str = $region;
        $str .= !empty($building) ? '-' . $building : '';
        $str .= !empty($unit) ? '-' . $unit : '';
        $str .= !empty($room) ? '-' . $room : '';
        $data['address'] = $str;
        pdo_insert('rhinfo_zyxq_suggest', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mycate = array();
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type=2';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycate[$regions[$m]['id']] = $categorys;
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid ';
            $buildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mybuilding[$regions[$m]['id']] = $buildings;
            $n = 0;
            while (!($n >= count($buildings))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
                $units = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$n]['id']));
                $myunit[$buildings[$n]['id']] = $units;
                $j = 0;
                while (!($j >= count($units))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid';
                    $rooms = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id'], ':bid' => $buildings[$n]['id'], ':tid' => $units[$j]['id']));
                    $myroom[$units[$j]['id']] = $rooms;
                    ($j += 1) + -1;
                }
                ($n += 1) + -1;
            }
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'send') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['isajax']) {
        $teamstr = str_replace('_', ',', $_GPC['teamstr']);
        $sql = 'select * FROM ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and rid=:rid and id in(' . $teamstr . ')';
        $send_teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
        $i = 0;
        $k = 0;
        while (!($k >= count($send_teams))) {
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
            $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => '后台推送', 'color' => $textcolor), 'keyword2' => array('value' => '后台推送', 'color' => $textcolor), 'keyword3' => array('value' => $item['address'], 'color' => $textcolor), 'keyword4' => array('value' => $item['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请尽快联系业主处理.'));
            $url = $this->my_mobileurl($this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $id)));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
            if (!empty($this->syscfg['tplid5'])) {
                $this->send_mysendtplnotice($send_teams[$k]['openid'], $this->syscfg['tplid5'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg($catetpl['title'], '请尽快联系业主处理.', $url, '', $send_teams[$k]['openid']);
            }
            if ($region['suggestverify'] == 1 && !empty($this->syscfg['suggestid'])) {
                $member = pdo_get('rhinfo_zyxq_member', array('weid' => $_W['uniacid'], 'rid' => $item['rid'], 'uid' => $item['uid']), array('mobile'));
                $this->send_sms($this->syscfg['smstype'], $send_teams[$k]['mobile'], $this->syscfg['suggestid'], array('content' => $item['content'], 'phone' => $member['mobile']));
            }
            ($i += 1) + -1;
            ($k += 1) + -1;
        }
        if ($i > 0) {
            echo 'ok';
        } else {
            echo '发送失败.';
        }
        exit(0);
    }
    $sql = 'select t.* FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and c.right2=1 and c.type=4 and t.rid=:rid';
    $list_teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
    include $this->mywtpl('send');
} elseif ($operation == 'evaluate') {
    $current = '评价记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and suggestid = :suggestid';
    $params[':suggestid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_suggest_comment') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_suggest_comment') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('evaluate');
} elseif ($operation == 'dellog') {
    $current = '删除记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_suggest_comment', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'suggest') {
    $current = '处理投诉建议';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        load()->model('mc');
        $fans = mc_fansinfo($_GPC['openid'], 0, $mywe['weid']);
        $data = array('sid' => $id, 'content' => $_GPC['content'], 'weid' => $mywe['weid'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'nickname' => $fans['nickname'], 'uid' => $_GPC['uid'], 'headimgurl' => $fans['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_suggest_record', $data);
        if ($res) {
            pdo_update('rhinfo_zyxq_suggest', array('status' => $_GPC['status'], 'lasttime' => TIMESTAMP), array('weid' => $mywe['weid'], 'id' => $id));
            $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
            $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
            $suggesttitle = pdo_fetchcolumn('select title from ' . tablename('rhinfo_zyxq_category') . ' where id = :cid and weid = :weid', array(':cid' => $item['cid'], ':weid' => $mywe['weid']));
            $serviceuser = pdo_fetchcolumn('select realname from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid) ', array(':weid' => $mywe['weid'], ':rid' => $item['rid'], ':openid' => $_GPC['openid'], ':uid' => $_GPC['uid']));
            $serviceuser = $serviceuser ? $serviceuser : $fans['nickname'];
            $postdata = array('first' => array('value' => '尊敬的业主，您的投诉建议已回复', 'color' => $topcolor), 'keyword1' => array('value' => $item['address'], 'color' => $textcolor), 'keyword2' => array('value' => $suggesttitle, 'color' => $textcolor), 'keyword3' => array('value' => $item['content'], 'color' => $textcolor), 'keyword4' => array('value' => $_GPC['content'], 'color' => $textcolor), 'keyword5' => array('value' => $serviceuser, 'color' => $textcolor), 'remark' => array('value' => '如有疑问，请联系，谢谢！'));
            $url = $this->createMobileurl('steward', array('op' => 'suggesttrack', 'id' => $id));
            $url = $this->my_mobileurl($url);
            if (!empty($this->syscfg['tplid4'])) {
                $this->send_mysendtplnotice($item['openid'], $this->syscfg['tplid4'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg('尊敬的业主，您的投诉建议已处理', '如有疑问，请联系服务人员，谢谢！', $url, '', $item['openid']);
            }
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $images = iunserializer($item['images']);
    $sql = 'select title from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and id=:id';
    $cname = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':id' => $item['cid']));
    include $this->mywtpl('suggest');
}
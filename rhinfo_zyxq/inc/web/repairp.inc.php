<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'repairp';
$tablename = 'rhinfo_zyxq_repairp';
$condition = ' weid = :weid ';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '内部工单';
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '内部工单列表';
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
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and rid = :rid and type=5';
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
            $condition .= ' AND reporttimes = 0 ';
        } else {
            $condition .= ' AND reporttimes > 0 ';
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
    $repairdate = $_GPC['repairdate'];
    if ($repairdate) {
        $starttime = strtotime($repairdate['start']);
        $endtime = strtotime($repairdate['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
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
                $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp_record') . ' where weid = :weid and rid = :rid and (openid<>:openid or uid<>:uid)';
                $record = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['id'], ':openid' => $data[$k]['openid'], ':uid' => $data[$k]['uid']));
                $sql = 'select * from ' . tablename('rhinfo_zyxq_team') . ' where weid=:weid and rid=:rid and (openid=:openid or uid=:uid)';
                $team = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid'], ':openid' => $record['openid'], ':uid' => $record['uid']));
                if (!empty($team)) {
                    $data[$k]['getrealname'] = $team['realname'];
                } else {
                    $data[$k]['getrealname'] = '';
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
            if ($data[$k]['status'] == '0' || $data[$k]['status'] == '1') {
                if (empty($data[$k]['reporttime'])) {
                    $hours = floor((TIMESTAMP - $data[$k]['ctime']) / 3600) - $data[$k]['reporttimes'] * 24;
                } else {
                    $hours = floor((TIMESTAMP - $data[$k]['reporttime']) / 3600) - ($data[$k]['reporttimes'] - 1) * 24;
                }
                if ($mycategory['reporttime'] > 0 && !($mycategory['reporttime'] >= $hours)) {
                    $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
                    $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
                    $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '报修工单处理超时提醒', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $data[$k]['address'] . '，报修内容：' . $data[$k]['content'], 'color' => $textcolor), 'remark' => array('value' => '此报修工单超时未处理，请速安排处理，谢谢！'));
                    $url = $this->createMobileurl('steward', array('op' => 'repairtrack', 'id' => $data[$k]['id']));
                    $url = $this->my_mobileurl($url);
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 ';
                    $teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $data[$k]['rid']));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $res = $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'select t.* from ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.weid=:weid and t.rid = :rid and c.right18=1 and ' . $data[$k]['rid'] . ' in(t.ridstr)';
                    $teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => 0));
                    $m = 0;
                    while (!($m >= count($teams))) {
                        if (!empty($this->syscfg['tplid1'])) {
                            $res = $this->send_mysendtplnotice($teams[$m]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                        }
                        ($m += 1) + -1;
                    }
                    $sql = 'update ' . tablename('rhinfo_zyxq_repairp') . ' set reporttimes = reporttimes + 1 , reporttime = ' . TIMESTAMP . ' where weid=:weid and id=:id ';
                    pdo_query($sql, array(':weid' => $mywe['weid'], ':id' => $data[$k]['id']));
                }
            }
            $data[$k]['ctime'] = date('Y-m-d H:i', $data[$k]['ctime']);
            ($k += 1) + -1;
        }
        if ($_GPC['export'] != '') {
            $filter = array('id' => 'ID', 'regionname' => '所属主体', 'address' => '房产', 'catename' => '工单类别', 'content' => '工单内容', 'nickname' => '派单人', 'getrealname' => '处理人', 'statustxt' => '状态', 'ctime' => '工单时间');
            export_excel($data, $filter, '工单明细');
            exit(0);
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'view') {
    $current = '查看工单';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $images = iunserializer($item['images']);
    $sql = 'select title from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 5 and id=:id';
    $cname = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':id' => $item['cid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_repairp_record') . ' where weid = :weid and rid = :rid';
    $records = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['id']));
    $k = 0;
    while (!($k >= count($records))) {
        $records[$k]['images'] = iunserializer($data[$k]['image']);
        ($k += 1) + -1;
    }
    include $this->mywtpl('view');
} elseif ($operation == 'delete') {
    $current = '删除工单';
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
    $current = '删除工单回复';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_repairp_record', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'add') {
    $current = '新增工单';
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
        pdo_insert('rhinfo_zyxq_repairp', $data);
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
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type=5';
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
            $postdata = array('first' => array('value' => $catetpl['title'], 'color' => $topcolor), 'keyword1' => array('value' => '后台推送', 'color' => $textcolor), 'keyword2' => array('value' => '后台推送', 'color' => $textcolor), 'keyword3' => array('value' => $item['address'], 'color' => $textcolor), 'keyword4' => array('value' => $item['content'], 'color' => $textcolor), 'keyword5' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'remark' => array('value' => '请速处理.'));
            $url = $this->my_mobileurl($this->createMobileurl('manager', array('op' => 'repairtrack', 'id' => $id)));
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $item['rid']));
            if (!empty($this->syscfg['tplid2'])) {
                $this->send_mysendtplnotice($send_teams[$k]['openid'], $this->syscfg['tplid2'], $postdata, $url, $topcolor);
            } else {
                $this->send_mycusnewsmsg($catetpl['title'], '请速处理.', $url, '', $send_teams[$k]['openid']);
            }
            if ($region['repairverify'] == 1 && !empty($this->syscfg['repairid'])) {
                $team = pdo_get('rhinfo_zyxq_team', array('weid' => $_W['uniacid'], 'rid' => $item['rid'], 'uid' => $item['uid']), array('mobile'));
                $this->send_sms($this->syscfg['smstype'], $send_teams[$k]['mobile'], $this->syscfg['repairid'], array('content' => $item['content'], 'phone' => $team['mobile']));
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
    $sql = 'select t.* FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and c.right1=1 and c.type=4 and t.rid=:rid';
    $list_teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
    include $this->mywtpl('send');
} elseif ($operation == 'repair') {
    $current = '处理工单';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($_W['ispost']) {
        load()->model('mc');
        $fans = mc_fansinfo($_GPC['openid'], 0, $mywe['weid']);
        $data = array('rid' => $id, 'content' => $_GPC['content'], 'weid' => $mywe['weid'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'ctime' => TIMESTAMP);
        $res = pdo_insert('rhinfo_zyxq_repairp_record', $data);
        if ($res) {
            pdo_update('rhinfo_zyxq_repairp', array('status' => $_GPC['status'], 'lasttime' => TIMESTAMP), array('weid' => $mywe['weid'], 'id' => $id));
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $images = iunserializer($item['images']);
    $sql = 'select title from ' . tablename('rhinfo_zyxq_category') . ' where weid=:weid and type = 2 and id=:id';
    $cname = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':id' => $item['cid']));
    include $this->mywtpl('repair');
}
<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'notify';
$tablename = 'rhinfo_zyxq_notice';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '通知管理';
$rights = $this->myrights(5, $mydo, 'list');
if ($operation == 'list') {
    $current = '通知列表';
    $myret = 0;
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $rcondition .= ' and id = :rid';
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
            $params[':rid'] = $data[$k]['rid'];
            $data[$k]['regionname'] = pdo_fetchcolumn($sql, $params);
            $data[$k]['category'] = $data[$k]['category'] == 2 ? '短信通知' : '微信通知';
            $url = $this->my_mobileurl($this->createMobileUrl('notice', array('op' => 'detail', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($url);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '通知列表';
    $myret = 0;
    $condition1 = '';
    if (!empty($_GPC['regionname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['regionname'] . '%\'';
    }
    $area = $_GPC['area'];
    if (!empty($area)) {
        if ($area['province']) {
            $condition1 .= ' AND province = :province';
            $params[':province'] = $area['province'];
        }
        if ($reside['city']) {
            $condition1 .= ' AND city = :city';
            $params[':city'] = $area['city'];
        }
        if ($area['district']) {
            $condition1 .= ' AND district = :district';
            $params[':district'] = $area['district'];
        }
    }
    if (!empty($condition1)) {
        $condition1 = ' and (rid in (select id from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . $condition1 . '))';
        $regioncondition .= $condition1;
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $rcondition = $this->wyrcondition();
    $rcondition = $condition . $rcondition;
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $rcondition .= ' and id = :rid';
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'SELECT title FROM ' . tablename('rhinfo_zyxq_region') . ' where ' . $rcondition;
            $params[':rid'] = $data[$k]['rid'];
            $data[$k]['regionname'] = pdo_fetchcolumn($sql, $params);
            $url = $this->my_mobileurl($this->createMobileUrl('notice', array('op' => 'detail', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($url);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '发布通知';
    if ($_W['ispost']) {
        if ($_GPC['range'] == 1) {
            $bid = $_GPC['mulbid'];
        } elseif ($_GPC['range'] == 2) {
            $bid = $_GPC['bid'];
        } elseif ($_GPC['range'] == 3) {
            $bid = $_GPC['bid'];
        } else {
            $bid = 0;
        }
        $timerange = $_GPC['timerange'];
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $bid, 'tid' => $_GPC['tid'], 'hid' => $_GPC['hid'], 'cid' => $_GPC['cid'], 'title' => $_GPC['title'], 'content' => htmlspecialchars_decode($_GPC['content']), 'range' => $_GPC['range'], 'remark' => $_GPC['remark'], 'reason' => $_GPC['reason'], 'stime' => strtotime($timerange['start']), 'etime' => strtotime($timerange['end']), 'category' => $_GPC['category'], 'status' => 0, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $topcolor = empty($this->syscfg['topcolor']) ? '#FF683F' : $this->syscfg['topcolor'];
        $textcolor = empty($this->syscfg['textcolor']) ? '#000' : $this->syscfg['textcolor'];
        $sql = 'SELECT * FROM ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $sql = 'select t.* FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and t.rid=:rid and c.type=4 and c.right6=1';
        $teams = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':rid' => $_GPC['rid']));
        $content = str_replace(array('<br>', '&nbsp;'), array("\n", ' '), htmlspecialchars_decode($_GPC['content']));
        $content = strip_tags($content, '<a>');
        if (mb_strlen($content, 'utf-8') > 100) {
            $content = mb_substr($content, 0, 100, 'utf-8') . '...';
        }
        $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => '新通知需要审核', 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $_GPC['category'] == 1 ? $content : $_GPC['remark'], 'color' => $textcolor), 'remark' => array('value' => '请审核，谢谢！'));
        $url = $this->createMobileurl('notice', array('op' => 'detail', 'id' => $id));
        $url = $this->my_mobileurl($url);
        if (!empty($this->syscfg['tplid1'])) {
            $k = 0;
            while (!($k >= count($teams))) {
                $this->send_mysendtplnotice($teams[$k]['openid'], $this->syscfg['tplid1'], $postdata, $url, $topcolor);
                ($k += 1) + -1;
            }
        }
        $this->mysyslog($id, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $mycategory = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
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
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where type=1 and weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    if ($_GPC['category'] == 2) {
        include $this->mywtpl('postsms');
    } else {
        include $this->mywtpl('post');
    }
} elseif ($operation == 'edit') {
    $current = '编辑通知';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        if ($_GPC['range'] == 1) {
            $bid = $_GPC['mulbid'];
        } elseif ($_GPC['range'] == 2) {
            $bid = $_GPC['bid'];
        } elseif ($_GPC['range'] == 3) {
            $bid = $_GPC['bid'];
        } else {
            $bid = 0;
        }
        $timerange = $_GPC['timerange'];
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $bid, 'tid' => $_GPC['tid'], 'hid' => $_GPC['hid'], 'cid' => $_GPC['cid'], 'title' => $_GPC['title'], 'content' => htmlspecialchars_decode($_GPC['content']), 'reason' => $_GPC['reason'], 'stime' => strtotime($timerange['start']), 'etime' => strtotime($timerange['end']), 'range' => $_GPC['range'], 'category' => $_GPC['category'], 'remark' => $_GPC['remark']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $mybuilding = array();
    $myunit = array();
    $myroom = array();
    $mycategory = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
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
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where type = 1 and weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid';
    $ebuildings = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_unit') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid';
    $eunits = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid = :rid and bid = :bid and tid=:tid';
    $erooms = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_category') . ' where weid = :weid and pid = :pid and rid = :rid and type = 1';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if ($item['category'] == 2) {
        include $this->mywtpl('postsms');
    } else {
        include $this->mywtpl('post');
    }
} elseif ($operation == 'delete') {
    $current = '删除通知';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'status') {
    $current = '通知状态';
    $id = intval($_GPC['id']);
    $data = array('status' => $_GPC['status'], 'audituid' => $mywe['uid'], 'openid' => 0);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'send') {
    $current = '通知状态';
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select banner,title from ' . tablename('rhinfo_zyxq_property') . ' where weid=:weid and id = :pid';
    $property = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid';
    $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
    if ($item['category'] == 2) {
        if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > 0)) {
            echo '可发短信数量为0,请充值';
            exit(0);
        }
        $condition = ' and mobile not in (select distinct openid from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where weid=' . $mywe['weid'] . ' and nid=' . $item['id'] . ')';
        if ($item['range'] == 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and mobile>0 and isnotice=1 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
            $location = '整个小区或商圈';
        } elseif ($item['range'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and mobile>0 and isnotice=1 and bid in(' . $item['bid'] . ')' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
            $location = '若干楼栋（含您的楼栋）';
        } elseif ($item['range'] == 2) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and mobile>0 and isnotice=1' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']);
            $location = '您所在的楼栋单元';
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and id=:hid and mobile>0 and isnotice=1' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']);
            $location = '您的房屋';
        }
        $rooms = pdo_fetchall($sql, $params);
        $i = 0;
        $k = 0;
        while (!($k >= count($rooms))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid and id = :rid';
            $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid']));
            $sql = 'select title from ' . tablename('rhinfo_zyxq_building') . ' where weid = :weid and pid = :pid and rid = :rid and id = :bid';
            $building = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid']));
            $sql = 'select title from ' . tablename('rhinfo_zyxq_unit') . ' where id = :id and weid = :weid and pid=:pid and rid=:rid';
            $unit = pdo_fetchcolumn($sql, array(':id' => $rooms[$k]['tid'], ':weid' => $rooms[$k]['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid']));
            if ($this->syscfg['smstype'] == '1' || $this->syscfg['smstype'] == '2' || $this->syscfg['smstype'] == '3' || $this->syscfg['smstype'] == '4') {
                if (!empty($rooms[$k]['mobile'])) {
                    $res = $this->send_sms($this->syscfg['smstype'], $rooms[$k]['mobile'], $this->syscfg['noticeid'], array('title' => $item['title'], 'name' => $region['title'] . $building . $unit . $rooms[$k]['title'], 'content' => $item['remark'], 'phone' => $region['telphone']));
                    if ($res['status'] == 1) {
                        $sendlog = array('weid' => $mywe['weid'], 'nid' => $item['id'], 'openid' => $rooms[$k]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_notice_sendlog', $sendlog);
                        $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                        pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                        $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '管理通知', 'io' => 2, 'mobile' => $rooms[$k]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                        ($i += 1) + -1;
                    }
                }
                if (!empty($rooms[$k]['mobile1'])) {
                    $res = $this->send_sms($this->syscfg['smstype'], $rooms[$k]['mobile1'], $this->syscfg['noticeid'], array('title' => $item['title'], 'name' => $region['title'] . $building . $unit . $rooms[$k]['title'], 'content' => $item['remark'], 'phone' => $region['telphone']));
                    if ($res['status'] == 1) {
                        $sendlog = array('weid' => $mywe['weid'], 'nid' => $item['id'], 'openid' => $rooms[$k]['mobile1'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_notice_sendlog', $sendlog);
                        $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                        pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                        $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '管理通知', 'io' => 2, 'mobile' => $rooms[$k]['mobile1'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                        ($i += 1) + -1;
                    }
                }
            }
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid = :weid and and rid = :rid and bid=:bid and tid=:tid and hid=:hid and mobile>0 and isnotice=1' . $condition;
            $room_mps = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid'], ':tid' => $rooms[$k]['tid'], ':hid' => $rooms[$k]['id']));
            $m = 0;
            while (!($m >= count($room_mps))) {
                if (!empty($room_mps[$m]['mobile'])) {
                    $res = $this->send_sms($this->syscfg['smstype'], $room_mps[$m]['mobile'], $this->syscfg['noticeid'], array('title' => $item['title'], 'name' => $region['title'] . $building . $unit . $room['title'], 'content' => $item['remark'], 'phone' => $region['telphone']));
                    if ($res['status'] == 1) {
                        $sendlog = array('weid' => $mywe['weid'], 'nid' => $item['id'], 'openid' => $room_mps[$m]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_notice_sendlog', $sendlog);
                        $sql = 'update ' . tablename('rhinfo_zyxq_region') . ' set smsqty = smsqty - 1 where weid=:weid and id=:rid and smsqty>0';
                        pdo_query($sql, array(':weid' => $mywe['weid'], ':rid' => $region['id']));
                        $smslog_data = array('weid' => $mywe['weid'], 'rid' => $region['id'], 'title' => '管理通知', 'io' => 2, 'mobile' => $room_mps[$m]['mobile'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                        pdo_insert('rhinfo_zyxq_region_smslog', $smslog_data);
                        ($i += 1) + -1;
                    }
                }
                ($m += 1) + -1;
            }
            if ($i == 100) {
                break;
            }
            if ($this->syscfg['smsprice'] > 0 && !($region['smsqty'] > $i)) {
                echo '可发短信数量不足';
                exit(0);
            }
            ($k += 1) + -1;
        }
        if (count($rooms) == $i || !(count($rooms) >= 100) || !($i >= 100)) {
            $data = array('status' => 3);
            $glue = 'AND';
            $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
            if (!empty($result)) {
                echo 'ok';
            } else {
                echo '操作失败!';
            }
        } else {
            echo 'send';
        }
    } else {
        $url = $this->my_mobileurl($this->createMobileurl('notice', array('op' => 'detail', 'id' => $id)));
        $thumb_url = !empty($property['banner']) ? tomedia($property['banner']) : '';
        $content = str_replace(array('<br>', '&nbsp;'), array("\n", ' '), $item['content']);
        $content = strip_tags($content, '<a>');
        if (mb_strlen($content, 'utf-8') > 100) {
            $content = mb_substr($content, 0, 100, 'utf-8') . '...';
        }
        $condition = ' and openid not in (select distinct openid from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where weid=' . $mywe['weid'] . ' and nid=' . $item['id'] . ')';
        if ($item['range'] == 0) {
            $sql = 'select uid,openid from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
            $location = '整个小区或商圈';
            $follows = pdo_fetchall('select uid,openid from ' . tablename('rhinfo_zyxq_region_follow') . ' where weid = :weid and pid = :pid and rid=:rid and deleted=0 ' . $condition, $params);
        } elseif ($item['range'] == 1) {
            $sql = 'select uid,openid from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and bid in(' . $item['bid'] . ') and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
            $location = '若干楼栋（含您的楼栋）';
        } elseif ($item['range'] == 2) {
            $sql = 'select uid,openid from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']);
            $location = '您所在的楼栋单元';
        } elseif ($item['range'] == 3) {
            $sql = 'select uid,openid from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']);
            $location = '您的房屋';
        }
        if ($item['range'] == 9) {
            $condition = ' and f.openid not in (select distinct openid from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where weid=' . $mywe['weid'] . ' and nid=' . $item['id'] . ')';
            $sql = 'select f.uid,f.openid from ' . tablename('mc_members') . ' as m left join ' . tablename('mc_mapping_fans') . ' as f on m.uid = f.uid where m.uniacid = :uniacid and m.groupid = :groupid and f.follow=1 ' . $condition;
            $params = array(':uniacid' => $mywe['weid'], ':groupid' => $region['groupid']);
            $location = '整个小区或商圈';
        }
        $members = pdo_fetchall($sql, $params);
        if (!empty($follows)) {
            $members = array_merge($follows, $members);
            if (!empty($members)) {
                $members = multi_array_unique($members);
            }
        }
        $sql = 'select tplid,topcolor from ' . tablename('rhinfo_zyxq_category') . ' where id = :id and weid = :weid';
        $catetpl = pdo_fetch($sql, array(':id' => $item['cid'], ':weid' => $mywe['weid']));
        $catetplid = $catetpl['tplid'];
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
        if ($catetplid == 'tplid1') {
            $postdata = array('first' => array('value' => empty($region['tplnoticefirst']) ? $region['title'] : $region['tplnoticefirst']), 'keyword1' => array('value' => $item['title'], 'color' => $topcolor), 'keyword2' => array('value' => date('Y-m-d H:i', TIMESTAMP), 'color' => $textcolor), 'keyword3' => array('value' => $content, 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        } elseif ($catetplid == 'tplid6') {
            $postdata = array('first' => array('value' => $item['title'], 'color' => $topcolor), 'time' => array('value' => date('Y-m-d H:i', $item['stime']) . '~' . date('Y-m-d H:i', $item['etime']), 'color' => $textcolor), 'location' => array('value' => $location, 'color' => $textcolor), 'reason' => array('value' => $item['reason'], 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        } elseif ($catetplid == 'tplid7') {
            $postdata = array('first' => array('value' => $item['title'], 'color' => $topcolor), 'time' => array('value' => date('Y-m-d H:i', $item['stime']) . '~' . date('Y-m-d H:i', $item['etime']), 'color' => $textcolor), 'location' => array('value' => $location, 'color' => $textcolor), 'reason' => array('value' => $item['reason'], 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        } elseif ($catetplid == 'tplid8') {
            $postdata = array('first' => array('value' => $item['title'], 'color' => $topcolor), 'time' => array('value' => date('Y-m-d H:i', $item['stime']) . '~' . date('Y-m-d H:i', $item['etime']), 'color' => $textcolor), 'location' => array('value' => $location, 'color' => $textcolor), 'reason' => array('value' => $item['reason'], 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        } elseif ($catetplid == 'tplid10') {
            $postdata = array('first' => array('value' => $item['title'], 'color' => $topcolor), 'time' => array('value' => date('Y-m-d H:i', $item['stime']) . '~' . date('Y-m-d H:i', $item['etime']), 'color' => $textcolor), 'location' => array('value' => $location, 'color' => $textcolor), 'reason' => array('value' => $item['reason'], 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        } elseif ($catetplid == 'tplid11') {
            $postdata = array('first' => array('value' => $item['title'], 'color' => $topcolor), 'time' => array('value' => date('Y-m-d H:i', $item['stime']) . '~' . date('Y-m-d H:i', $item['etime']), 'color' => $textcolor), 'location' => array('value' => $location, 'color' => $textcolor), 'reason' => array('value' => $item['reason'], 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        } elseif ($catetplid == 'tplid9') {
            $postdata = array('first' => array('value' => $item['title'], 'color' => $topcolor), 'scope' => array('value' => $location, 'color' => $textcolor), 'time' => array('value' => date('Y-m-d H:i', $item['stime']) . '~' . date('Y-m-d H:i', $item['etime']), 'color' => $textcolor), 'method' => array('value' => $item['reason'], 'color' => $textcolor), 'remark' => array('value' => $item['remark']));
        }
        $i = 0;
        $j = 0;
        while (!($j >= count($members))) {
            if (!empty($this->syscfg[$catetplid])) {
                $res = $this->send_mysendtplnotice($members[$j]['openid'], $this->syscfg[$catetplid], $postdata, $url, $topcolor);
            } else {
                $res = $this->send_mycusnewsmsg($item['title'], $item['remark'], $url, $thumb_url, $members[$j]['openid']);
            }
            if ($res == true) {
                $sendlog = array('weid' => $mywe['weid'], 'nid' => $item['id'], 'openid' => $members[$j]['openid'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                pdo_insert('rhinfo_zyxq_notice_sendlog', $sendlog);
                ($i += 1) + -1;
            }
            if ($i == 100) {
                break;
            }
            ($j += 1) + -1;
        }
        if (count($members) == $i || !(count($members) >= 100) || !($i >= 100)) {
            $data = array('status' => 3);
            $glue = 'AND';
            $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
            if (!empty($result)) {
                echo 'ok';
            } else {
                echo '操作失败!';
            }
        } else {
            echo 'send';
        }
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . '-id=' . $id);
    exit(0);
} elseif ($operation == 'sendcount') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($item['category'] == 2) {
        $condition = ' and mobile not in (select distinct openid from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where weid=' . $mywe['weid'] . ' and nid=' . $item['id'] . ')';
        if ($item['range'] == 0) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and mobile>0 and isnotice=1' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
        } elseif ($item['range'] == 1) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and mobile>0 and isnotice=1 and  bid in(' . $item['bid'] . ')' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
        } elseif ($item['range'] == 2) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and mobile>0 and isnotice=1' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']);
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_room') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and id=:hid and mobile>0 and isnotice=1' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']);
        }
        $i = 0;
        $rooms = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($rooms))) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_room_mp') . ' where weid = :weid and and rid = :rid and bid=:bid and tid=:tid and hid=:hid and mobile>0 and isnotice=1' . $condition;
            $room_mps = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $rooms[$k]['pid'], ':rid' => $rooms[$k]['rid'], ':bid' => $rooms[$k]['bid'], ':tid' => $rooms[$k]['tid'], ':hid' => $rooms[$k]['id']));
            $i += $room_mps;
            ($k += 1) + -1;
        }
        $count = count($rooms) + $i;
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id = :rid';
        $region = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':rid' => $item['rid']));
        $condition = ' and openid not in (select distinct openid from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where weid=' . $mywe['weid'] . ' and nid=' . $item['id'] . ')';
        if ($item['range'] == 0) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
        } elseif ($item['range'] == 1) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and bid in(' . $item['bid'] . ') and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']);
        } elseif ($item['range'] == 2) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid']);
        } elseif ($item['range'] == 3) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and bid=:bid and tid=:tid and hid=:hid and deleted=0 and status=0 ' . $condition;
            $params = array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':bid' => $item['bid'], ':tid' => $item['tid'], ':hid' => $item['hid']);
        }
        if ($item['range'] == 9) {
            $condition = ' and f.openid not in (select distinct openid from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where weid=' . $mywe['weid'] . ' and nid=' . $item['id'] . ')';
            $sql = 'select f.* from ' . tablename('mc_members') . ' as m left join ' . tablename('mc_mapping_fans') . ' as f on m.uid = f.uid where m.uniacid = :uniacid and m.groupid = :groupid and f.follow=1 ' . $condition;
            $params = array(':uniacid' => $mywe['weid'], ':groupid' => $region['groupid']);
        }
        $count = pdo_fetchcolumn($sql, $params);
    }
    echo $count;
    exit(0);
} elseif ($operation == 'readlog') {
    $current = '阅读记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and nid = :nid ';
    $params[':nid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_notice_log') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_notice_log') . ' where ' . $condition . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $fans = mc_fansinfo($data[$k]['openid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['nickname'] = $fans['nickname'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('read');
} elseif ($operation == 'delread') {
    $current = '删除点击';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_notice_log', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'sendlog') {
    $current = '发送记录';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and nid = :nid ';
    $params[':nid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_notice_sendlog') . ' where ' . $condition . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $fans = mc_fansinfo($data[$k]['openid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['nickname'] = $fans['nickname'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('send');
} elseif ($operation == 'delsend') {
    $current = '删除点击';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_notice_sendlog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
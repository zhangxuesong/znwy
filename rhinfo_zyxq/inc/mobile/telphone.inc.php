<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$curr = 'telphone';
$mydo = 'telphone';
$myurl = $this->createMobileurl($mydo);
$sysconfig = $this->module['config'];
$sysconifg['qq_lbskey'] = !empty($sysconfig['qq_lbskey']) ? $sysconfig['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$myrid = empty($_GPC['rid']) ? $user['rid'] : $_GPC['rid'];
if ($operation == 'index') {
    $category = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_telcate') . ' where enabled=1 and parentid=0 and weid=:weid order by displayorder desc limit 8 ', array(':weid' => $_W['uniacid']));
    $sql = 'select link,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and rid = :rid and btype=11 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    if (empty($banners)) {
        $sql = 'select link,thumb,0 as `pid`, 0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=11 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    $cate = intval($_GPC['cate']);
    $keyword = trim($_GPC['keyword']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $condition = ' q.weid=:weid and q.status=1 and c.enabled=1 ';
    if (!empty($cate)) {
        $category = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_telcate') . ' where id=:id and and weid=:weid limit 1 ', array(':id' => $cate, ':weid' => $_W['uniacid']));
        if ($category['parentid'] == 0) {
            $condition .= ' and q.cateid in(select distinct id from ' . tablename('rhinfo_zyxq_telcate') . ' where parentid=' . $cate . ' and weid=:weid) ';
        } else {
            $condition .= ' and q.cateid=' . $cate . ' ';
        }
    }
    if (!empty($keyword)) {
        $condition .= ' and (q.title like \'%' . $keyword . '%\') ';
    }
    if (!empty($_GPC['lat']) && !empty($_GPC['lng'])) {
        $data = array();
        $data['location'] = $_GPC['lat'] . ',' . $_GPC['lng'];
        $data['key'] = $sysconifg['qq_lbskey'];
        $data['get_poi'] = 1;
        load()->func('communication');
        $response = ihttp_request('https://apis.map.qq.com/ws/geocoder/v1/' . '?' . http_build_query($data, '', '&'), array(), array('CURLOPT_FOLLOWLOCATION' => 0));
        if (empty($response['headers']['Location'])) {
            $res = json_decode($response['content'], 1);
            if ($res['status'] == '0') {
                $res = $res['result'];
                $res = $res['address_component'];
                $condition .= ' and district = \'' . $res['district'] . '\' ';
            } else {
                $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
                $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
                $condition .= ' and district = \'' . $region['district'] . '\' ';
            }
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
            $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
            $condition .= ' and district = \'' . $region['district'] . '\' ';
        }
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
        $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':rid' => $myrid));
        $condition .= ' and district = \'' . $region['district'] . '\' ';
    }
    $sql = 'SELECT q.*, c.title as catename FROM ' . tablename('rhinfo_zyxq_telphone') . ' q left join' . tablename('rhinfo_zyxq_telcate') . ' `c` on c.id=q.cateid and c.weid=q.weid where  1 and ' . $condition . ' ORDER BY q.displayorder DESC,q.id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_telphone') . ' q left join' . tablename('rhinfo_zyxq_telcate') . ' c on c.id=q.cateid and c.weid=q.weid where  1 and ' . $condition . ' ', $params);
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['thumb'] = tomedia($list[$k]['thumb']);
        if (!empty($list[$k]['lat']) && !empty($list[$k]['lng'])) {
            $list[$k]['mapurl'] = $this->createMobileurl($mydo, array('op' => 'map', 'id' => $list[$k]['id']));
        } else {
            $list[$k]['mapurl'] = '';
        }
        ($k += 1) + -1;
    }
    show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
} elseif ($operation == 'telphone') {
    $cate = intval($_GPC['cate']);
    $category = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_telcate') . ' where id=:id and enabled=1 and weid=:weid limit 1 ', array(':id' => $cate, ':weid' => $_W['uniacid']));
    if ($category['parentid'] == 0) {
        $categorys = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_telcate') . ' where enabled=1 and weid=:weid and parentid=:parentid ', array(':weid' => $_W['uniacid'], ':parentid' => $category['id']));
    } else {
        $categorys = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_telcate') . ' where enabled=1 and weid=:weid and parentid=:parentid ', array(':weid' => $_W['uniacid'], ':parentid' => $category['parentid']));
    }
    include $this->mymtpl('list');
} elseif ($operation == 'map') {
    $id = intval($_GPC['id']);
    $condition .= ' and id = :id';
    $params[':id'] = $id;
    $sql = 'select * from ' . tablename('rhinfo_zyxq_telphone') . ' where ' . $condition;
    $item = pdo_fetch($sql, $params);
    include $this->mymtpl('map');
}
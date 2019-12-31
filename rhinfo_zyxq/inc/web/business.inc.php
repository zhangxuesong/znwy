<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'business';
$config = $this->module['config'];
$conifg['qq_lbskey'] = !empty($config['qq_lbskey']) ? $config['qq_lbskey'] : 'ID5BZ-5IUWP-T3WDM-VFYWQ-WQPKF-RUFMN';
$tablename = 'rhinfo_zycj_business';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$navtitle = '周边商家';
if ($operation == 'list') {
    $current = '商家列表';
    $myret = 0;
    $rights = $this->myrights(13, $mydo, 'list');
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
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['statustxt'] = '显示';
            } elseif ($data[$k]['status'] == 2) {
                $data[$k]['statustxt'] = '待审核';
            } elseif ($data[$k]['status'] == 3) {
                $data[$k]['statustxt'] = '审核不通过';
            } else {
                $data[$k]['statustxt'] = '隐藏';
            }
            $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid=:rid and id=:cateid';
            $category = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':cateid' => $data[$k]['cateid']));
            if (!empty($category['parentid'])) {
                $sql = 'select title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid=:rid and id=:parentid';
                $parenttitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':parentid' => $category['parentid']));
                $data[$k]['category'] = $parenttitle . '-' . $category['title'];
            } else {
                $data[$k]['category'] = $category['title'];
            }
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'detail', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            $data[$k]['payurl'] = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'pay', 'id' => $data[$k]['id'])));
            $data[$k]['payqrcode'] = $this->createqrcode($data[$k]['payurl']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增商家';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        $images = is_array($_GPC['banner']) ? iserializer($_GPC['banner']) : iserializer(array());
        $regions = explode(',', $_GPC['regions']);
        $regions = is_array($regions) ? iserializer($regions) : iserializer(array());
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cateid' => $_GPC['cateid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'banner' => $images, 'content' => htmlspecialchars_decode($_GPC['content']), 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status'], 'regions' => $regions, 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'url' => $_GPC['url'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'isstore' => $_GPC['isstore'], 'apikey' => $_GPC['apikey'], 'onhand' => 0, 'inqty' => 0, 'outqty' => 0, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $pcategory = array();
    $category = array();
    if ($cate == 1 || empty($cate)) {
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid=:rid and parentid=0';
        $pcategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
        $pcategory = $pcategorys;
        $k = 0;
        while (!($k >= count($pcategorys))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and parentid=:parentid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $pcategorys[$k]['id']));
            $category[$pcategorys[$k]['id']] = $categorys;
            ($k += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
    } elseif ($cate == 2) {
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            $m = 0;
            while (!($m >= count($regions))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid=:rid and parentid=0';
                $pcategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
                $pcategory[$regions[$m]['id']] = $pcategorys;
                $n = 0;
                while (!($n >= count($pcategorys))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and parentid=:parentid';
                    $categorys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':parentid' => $pcategorys[$n]['id']));
                    $category[$pcategorys[$n]['id']] = $categorys;
                    ($n += 1) + -1;
                }
                ($m += 1) + -1;
            }
            ($k += 1) + -1;
        }
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑商家';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $images = is_array($_GPC['banner']) ? iserializer($_GPC['banner']) : iserializer(array());
        $regions = explode(',', $_GPC['regions']);
        $regions = is_array($regions) ? iserializer($regions) : iserializer(array());
        $area = $_GPC['area'];
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cateid' => $_GPC['cateid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'banner' => $images, 'content' => htmlspecialchars_decode($_GPC['content']), 'isrecommand' => $_GPC['isrecommand'], 'regions' => $regions, 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'url' => $_GPC['url'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'isstore' => $_GPC['isstore'], 'apikey' => $_GPC['apikey'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $pcategory = array();
    $category = array();
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $item['banner'] = iunserializer($item['banner']);
    $sql = 'select parentid from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and id=:cateid';
    $item['parentcateid'] = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':cateid' => $item['cateid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    if (empty($item['rid'])) {
        $cate = 1;
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid=:rid and parentid=0';
        $pcategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
        $pcategory = $pcategorys;
        $k = 0;
        while (!($k >= count($pcategorys))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and parentid=:parentid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $pcategorys[$k]['id']));
            $category[$pcategorys[$k]['id']] = $categorys;
            ($k += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid']));
        $count = count($regions);
        $regionarr = iunserializer($item['regions']);
        $regionarr = array_filter($regionarr);
        $regionname = array();
        $m = 0;
        while (!($m >= $count)) {
            if (in_array($regions[$m]['id'], $regionarr)) {
                $regionname[] = $regions[$m]['title'];
            }
            ($m += 1) + -1;
        }
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid = :rid and parentid=:parentid';
        $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':parentid' => $item['parentcateid']));
    } else {
        $k = 0;
        while (!($k >= count($myproperty))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
            $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
            $myregion[$myproperty[$k]['id']] = $regions;
            $m = 0;
            while (!($m >= count($regions))) {
                $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid=:rid and parentid=0';
                $pcategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
                $pcategory[$regions[$m]['id']] = $pcategorys;
                $n = 0;
                while (!($n >= count($pcategorys))) {
                    $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and parentid=:parentid';
                    $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $pcategorys[$n]['id']));
                    $category[$pcategorys[$n]['id']] = $categorys;
                    ($n += 1) + -1;
                }
                ($m += 1) + -1;
            }
            ($k += 1) + -1;
        }
        $cate = 2;
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and pid = :pid and rid = :rid and parentid=:parentid';
        $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':parentid' => $item['parentcateid']));
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除商家';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'catelist') {
    $current = '商家分类';
    $myret = 0;
    $rights = $this->myrights(13, $mydo, 'catelist');
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
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zycj_business_cate') . ' where parentid=0 and ' . $condition;
    $ptotal = pdo_fetchcolumn($sql, $params);
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zycj_business_cate') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where parentid=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $children = array();
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            if ($data[$k]['enabled'] == 1) {
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'list', 'cateid' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . " where parentid=:parentid and weid=:weid ORDER BY\r\n\t\t\t\t\t `ID` ASC ";
            $childdata = pdo_fetchall($sql, array(':parentid' => $data[$k]['id'], ':weid' => $mywe['weid']));
            $m = 0;
            while (!($m >= count($childdata))) {
                $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
                $region = pdo_fetchcolumn($sql, array(':id' => $childdata[$m]['rid'], ':weid' => $mywe['weid']));
                $childdata[$m]['region'] = empty($region) ? '系统默认' : $region;
                if ($childdata[$m]['enabled'] == 1) {
                    $childdata[$m]['status'] = '显示';
                } else {
                    $childdata[$m]['status'] = '隐藏';
                }
                $childdata[$m]['url'] = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'list', 'cateid' => $childdata[$m]['id'])));
                $childdata[$m]['qrcode'] = $this->createqrcode($childdata[$m]['url']);
                ($m += 1) + -1;
            }
            $children[$data[$k]['id']] = $childdata;
            ($k += 1) + -1;
        }
        $pager = pagination($ptotal, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $navtitle = '商家分类';
    $current = '新增分类';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        if (!empty($_GPC['parentid'])) {
            $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where id = :id and weid = :weid';
            $item = pdo_fetch($sql, array(':id' => $_GPC['parentid'], ':weid' => $mywe['weid']));
            $pid = $item['pid'];
            $rid = $item['rid'];
        } else {
            $pid = $_GPC['pid'];
            $rid = $_GPC['rid'];
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'parentid' => $_GPC['parentid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        pdo_insert('rhinfo_zycj_business_cate', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'catelist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'cateedit') {
    $navtitle = '商家分类';
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        $glue = 'AND';
        pdo_update('rhinfo_zycj_business_cate', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $glue = 'AND';
        pdo_update('rhinfo_zycj_business_cate', array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid']), array('parentid' => $id, 'weid' => $mywe['weid']), 'AND');
        $sql = 'select id,title from ' . tablename('rhinfo_zycj_business_cate') . ' where weid = :weid and parentid=:parentid';
        $cateids = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':parentid' => $id));
        $k = 0;
        while (!($k >= count($cateids))) {
            $glue = 'AND';
            pdo_update('rhinfo_zycj_business', array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid']), array('cateid' => $cateids[$k]['id'], 'weid' => $mywe['weid']), 'AND');
            ($k += 1) + -1;
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'catelist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_cate') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    if ($item['rid'] == 0) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('catepost');
} elseif ($operation == 'catedelete') {
    $current = '删除商家分类';
    $id = intval($_GPC['id']);
    $sql = 'select count(*) from ' . tablename('rhinfo_zycj_business_cate') . ' where parentid = :id and weid = :weid';
    $total = pdo_fetchcolumn($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    if ($total > 0) {
        echo '删除失败，存在下级分类!';
        exit(0);
    }
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zycj_business_cate', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'pay') {
    $myret = 0;
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('paytype' => $_GPC['paytype'], 'submerchid' => $_GPC['submerchid'], 'cost' => $_GPC['cost'], 'credit' => $_GPC['credit'], 'bankmerchid' => $_GPC['bankmerchid'], 'ymfurl' => $_GPC['ymfurl'], 'ymfkey' => $_GPC['ymfkey'], 'paysuccessurl' => $_GPC['paysuccessurl'], 'ispay' => $_GPC['ispay'], 'rsdbankmerchid' => $_GPC['rsdbankmerchid'], 'bankkey' => $_GPC['bankkey'], 'creditrule' => $_GPC['creditrule'], 'retcredit' => $_GPC['retcredit'], 'starkey' => $_GPC['starkey'], 'starorg' => $_GPC['starorg'], 'startrm' => $_GPC['startrm'], 'starmerchid' => $_GPC['starmerchid'], 'ylbid' => $_GPC['ylbid'], 'isalipay' => $_GPC['isalipay'], 'aliaccount' => $_GPC['aliaccount'], 'alipartner' => $_GPC['alipartner'], 'alisecret' => $_GPC['alisecret'], 'alipay_type' => $_GPC['alipay_type'], 'alipay_appid' => $_GPC['alipay_appid'], 'alipay_rsa2' => $_GPC['alipay_rsa2'], 'alipay_private' => $_GPC['alipay_private'], 'alipay_seller_id' => $_GPC['alipay_seller_id'], 'alipay_app_auth_token' => $_GPC['alipay_app_auth_token']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        if (!empty($_GPC['ylbid'])) {
            $set = array();
            $set['url'] = 'list_bind.php';
            $params = array('id' => $_GPC['ylbid']);
            $res = ylb_http_post($set, $params);
            if ($res['total_count'] == 0) {
                $set = array();
                $set['url'] = 'bind.php';
                $params = array('token' => $this->syscfg['ylb_token'], 'id' => $_GPC['ylbid'], 'm' => 1, 'uid' => 'rhinfo_' . $_GPC['ylbid']);
                $res = ylb_http_post($set, $params);
            }
        }
        $this->mysyslog($id, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')));
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $navtitle = $item['title'];
    $current = '支付参数';
    include $this->mywtpl('pay');
} elseif ($operation == 'status') {
    $current = '支付状态';
    $id = intval($_GPC['id']);
    $data = array('ispay' => $_GPC['ispay']);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . $_GPC['ispay'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'storelist') {
    $current = '门店列表';
    $myret = 1;
    $rights = $this->myrights(13, $mydo, 'list');
    $bid = $_GPC['bid'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business') . ' where id = :bid and' . $condition;
    $business = pdo_fetch($sql, array(':bid' => $bid, ':weid' => $mywe['weid']));
    $navtitle = $navtitle . ' > ' . $business['title'];
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_business_store') . ' where bid = :bid and' . $condition;
    $total = pdo_fetchcolumn($sql, array(':bid' => $bid, ':weid' => $mywe['weid']));
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_store') . ' where bid = :bid and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, array(':bid' => $bid, ':weid' => $mywe['weid']));
        $k = 0;
        while (!($k >= count($data))) {
            $data[$k]['payurl'] = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'pay', 'id' => $bid, 'storeid' => $data[$k]['id'])));
            $data[$k]['payqrcode'] = $this->createqrcode($data[$k]['payurl']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('storelist');
} elseif ($operation == 'storeadd') {
    $current = '新增门店';
    $bid = $_GPC['bid'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'bid' => $bid, 'title' => $_GPC['title'], 'storeno' => $_GPC['storeno'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_business_store', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'storelist', 'bid' => $bid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business') . ' where id = :bid and' . $condition;
    $business = pdo_fetch($sql, array(':bid' => $bid, ':weid' => $mywe['weid']));
    $navtitle = $navtitle . ' > ' . $business['title'];
    include $this->mywtpl('store');
} elseif ($operation == 'storeedit') {
    $current = '修改门店';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('title' => $_GPC['title'], 'storeno' => $_GPC['storeno'], 'contact' => $_GPC['contact'], 'telphone' => $_GPC['telphone'], 'mobile' => $_GPC['mobile'], 'province' => $area['province'], 'city' => $area['city'], 'district' => $area['district'], 'address' => $_GPC['address'], 'lng' => $_GPC['lng'], 'lat' => $_GPC['lat'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'starttime' => $_GPC['starttime'], 'endtime' => $_GPC['endtime']);
        pdo_update('rhinfo_zycj_business_store', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'storelist', 'bid' => $_GPC['bid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_store') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $bid = $item['bid'];
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business') . ' where id = :bid and' . $condition;
    $business = pdo_fetch($sql, array(':bid' => $item['bid'], ':weid' => $mywe['weid']));
    $navtitle = $navtitle . ' > ' . $business['title'];
    include $this->mywtpl('store');
} elseif ($operation == 'storedelete') {
    $current = '删除门店';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zycj_business_store', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'actlist') {
    $current = '活动列表';
    $myret = 0;
    $rights = $this->myrights(13, $mydo, 'actlist');
    $condition1 = '';
    if (!empty($mywe['pid'])) {
        $condition1 .= ' and pid=' . $mywe['pid'];
    }
    if (!empty($_GPC['businessname'])) {
        $condition1 .= ' AND title LIKE \'%' . $_GPC['businessname'] . '%\'';
    }
    if (!empty($condition1)) {
        $condition1 = ' and (bid in (select id from ' . tablename('rhinfo_zycj_business') . ' where ' . $condition . $condition1 . '))';
        $condition .= $condition1;
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zycj_business_activity') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_activity') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zycj_business') . ' where id=:bid and weid=:weid';
            $data[$k]['business'] = pdo_fetchcolumn($sql, array(':bid' => $data[$k]['bid'], ':weid' => $mywe['weid']));
            if ($data[$k]['starttime'] > TIMESTAMP && !empty($data[$k]['starttime'])) {
                $data[$k]['status'] = 0;
            }
            if (!($data[$k]['starttime'] > TIMESTAMP) && $data[$k]['endtime'] >= TIMESTAMP && !empty($data[$k]['starttime']) && !empty($data[$k]['endtime'])) {
                $data[$k]['status'] = 1;
            }
            if (!($data[$k]['endtime'] >= TIMESTAMP) && !empty($data[$k]['endtime'])) {
                $data[$k]['status'] = 2;
            }
            $data[$k]['url'] = $this->my_mobileurl($this->createMobileUrl('business', array('op' => 'actdetail', 'id' => $data[$k]['id'])));
            $data[$k]['qrcode'] = $this->createqrcode($data[$k]['url']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('actlist');
} elseif ($operation == 'actadd') {
    $current = '发布活动';
    $bid = $_GPC['bid'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('weid' => $mywe['weid'], 'bid' => $bid, 'title' => $_GPC['title'], 'starttime' => strtotime($_GPC['starttime']), 'endtime' => strtotime($_GPC['endtime']), 'style' => $_GPC['style'], 'strategyid' => $_GPC['strategyid'], 'poster' => $_GPC['poster'], 'content' => htmlspecialchars_decode($_GPC['content']), 'signup' => $_GPC['signup'], 'signuptime' => strtotime($_GPC['signuptime']), 'signupmsg' => $_GPC['signupmsg'], 'signuptitle' => $_GPC['signuptitle'], 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert('rhinfo_zycj_business_activity', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'actlist')) . $mywe['direct']);
        exit(0);
    }
    if (!empty($mywe['pid'])) {
        $condition .= ' and pid=:pid';
        $params[':pid'] = $mywe['pid'];
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business') . ' where ' . $condition;
    $business = pdo_fetchall($sql, $params);
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_redpacket') . ' where pid=0 and rid=0 and status=1 and ' . $condition;
    $redpackets = pdo_fetchall($sql, $params);
    include $this->mywtpl('activity');
} elseif ($operation == 'actedit') {
    $current = '修改活动';
    $id = $_GPC['id'];
    if ($_W['ispost']) {
        $area = $_GPC['area'];
        $data = array('bid' => $_GPC['bid'], 'title' => $_GPC['title'], 'starttime' => strtotime($_GPC['starttime']), 'endtime' => strtotime($_GPC['endtime']), 'style' => $_GPC['style'], 'strategyid' => $_GPC['strategyid'], 'poster' => $_GPC['poster'], 'content' => htmlspecialchars_decode($_GPC['content']), 'signup' => $_GPC['signup'], 'signupmsg' => $_GPC['signupmsg'], 'signuptitle' => $_GPC['signuptitle'], 'signuptime' => strtotime($_GPC['signuptime']));
        pdo_update('rhinfo_zycj_business_activity', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'actlist', 'bid' => $_GPC['bid'])) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename('rhinfo_zycj_business_activity') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_redpacket') . ' where pid=0 and rid=0 and status=1 and ' . $condition;
    $redpackets = pdo_fetchall($sql, $params);
    if (!empty($mywe['pid'])) {
        $condition .= ' and pid=:pid';
        $params[':pid'] = $mywe['pid'];
    }
    $sql = 'SELECT * FROM ' . tablename('rhinfo_zycj_business') . ' where ' . $condition;
    $business = pdo_fetchall($sql, $params);
    include $this->mywtpl('activity');
} elseif ($operation == 'actdelete') {
    $current = '删除商家活动';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zycj_business_activity', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'signuplog') {
    $current = '活动报名记录';
    $myret = 1;
    $rights = $this->myrights(13, $mydo, 'actlist');
    $aid = intval($_GPC['aid']);
    $condition .= ' and aid = :aid';
    $params[':aid'] = $aid;
    $signupdate = $_GPC['signupdate'];
    if ($signupdate) {
        $starttime = strtotime($signupdate['start']);
        $endtime = strtotime($signupdate['end']);
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    $condition .= ' and signtime>=' . $starttime . ' and signtime<=' . $endtime;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zycj_business_activitylog') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        if ($_GPC['export'] == 'export') {
            $limit = '';
        }
        $sql = 'select * from ' . tablename('rhinfo_zycj_business_activitylog') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        load()->model('mc');
        $k = 0;
        while (!($k >= count($data))) {
            $fans = mc_fansinfo($data[$k]['uid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['signtime'] = date('Y-m-d H:i', $data[$k]['signtime']);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
        if ($_GPC['export'] == 'export') {
            $filter = array('id' => 'ID', 'realname' => '姓名', 'mobile' => '手机号码', 'signtime' => '报名时间');
            export_excel($data, $filter, '报名明细');
            exit(0);
        }
    }
    include $this->mywtpl('signuplog');
} elseif ($operation == 'delsignlog') {
    $current = '删除活动报名记录';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zycj_business_activitylog', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
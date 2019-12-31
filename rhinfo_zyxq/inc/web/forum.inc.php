<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'forum';
$tablename = 'rhinfo_zyxq_sns_board';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
if ($operation == 'list') {
    $navtitle = '版块管理';
    $current = '版块列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'list');
    $condition .= $this->myrcondition();
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
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            $data[$k]['postcount'] = $this->getPostCount($data[$k]['id']);
            $data[$k]['followcount'] = $this->getFollowCount($data[$k]['id']);
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid = :weid and pid = :pid and rid=:rid and id=:cid';
            $category = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':cid' => $data[$k]['cid']));
            $data[$k]['category'] = $category;
            $qrcode = $this->my_mobileurl($this->createMobileUrl('forum', array('op' => 'board', 'id' => $data[$k]['id'])));
            $data[$k]['url'] = $qrcode;
            $data[$k]['qrcode'] = $this->createqrcode($qrcode);
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $navtitle = '版块管理';
    $current = '版块列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'list');
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
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            $data[$k]['postcount'] = $this->getPostCount($data[$k]['id']);
            $data[$k]['followcount'] = $this->getFollowCount($data[$k]['id']);
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid = :weid and pid = :pid and rid=:rid and id=:cid';
            $category = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':cid' => $data[$k]['cid']));
            $data[$k]['category'] = $category;
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $navtitle = '版块管理';
    $current = '新增版块';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        if ($cate == 1) {
            $pid = 0;
            $rid = 0;
        } else {
            $pid = $_GPC['pid'];
            $rid = $_GPC['rid'];
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'cid' => $_GPC['cid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'logo' => $_GPC['logo'], 'banner' => $_GPC['banner'], 'desc' => $_GPC['desc'], 'noimage' => $_GPC['noimage'], 'needcheck' => $_GPC['needcheck'], 'needcheckmanager' => $_GPC['needcheckmanager'], 'needcheckreply' => $_GPC['needcheckreply'], 'needcheckreplymanager' => $_GPC['needcheckreplymanager'], 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status']);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        if ($_GPC['openid'] || $_GPC['uid']) {
            load()->model('mc');
            $fans = array();
            $fans = mc_fansinfo($_GPC['openid'], 0, $mywe['weid']);
            $data_manager = array('weid' => $mywe['weid'], 'pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $id, 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'enabled' => 1);
            pdo_insert('rhinfo_zyxq_sns_manager', $data_manager);
        }
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    if ($cate == 1) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid = :weid and pid = :pid and rid=:rid';
        $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $navtitle = '版块管理';
    $current = '编辑版块';
    $rights = $this->myrights(11, $mydo, 'list');
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'cid' => $_GPC['cid'], 'displayorder' => $_GPC['displayorder'], 'title' => $_GPC['title'], 'logo' => $_GPC['logo'], 'banner' => $_GPC['banner'], 'desc' => $_GPC['desc'], 'noimage' => $_GPC['noimage'], 'needcheck' => $_GPC['needcheck'], 'needcheckmanager' => $_GPC['needcheckmanager'], 'needcheckreply' => $_GPC['needcheckreply'], 'needcheckreplymanager' => $_GPC['needcheckreplymanager'], 'isrecommand' => $_GPC['isrecommand'], 'status' => $_GPC['status']);
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $category = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid = :weid and pid = :pid and rid=:rid';
            $categorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $mycategory[$regions[$m]['id']] = $categorys;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid = :weid and pid = :pid and rid = :rid';
    $ecategorys = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if ($item['rid'] == 0) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('post');
} elseif ($operation == 'delete') {
    $current = '删除版块';
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
} elseif ($operation == 'compdelete') {
    $current = '删除投诉';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_sns_complain', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'managerdelete') {
    $current = '删除版主';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_sns_manager', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'catelist') {
    $navtitle = '分类管理';
    $current = '分类列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'catelist');
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_category') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_category') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
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
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'catesearch') {
    $navtitle = '分类管理';
    $current = '分类列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'catelist');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_category') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_category') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
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
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('catelist');
} elseif ($operation == 'cateadd') {
    $navtitle = '分类管理';
    $current = '新增分类';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        if ($cate == 1) {
            $pid = 0;
            $rid = 0;
        } else {
            $pid = $_GPC['pid'];
            $rid = $_GPC['rid'];
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        pdo_insert('rhinfo_zyxq_sns_category', $data);
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
    $navtitle = '分类管理';
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'isrecommand' => $_GPC['isrecommand']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_sns_category', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_category') . ' where id = :id and weid = :weid';
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
    $current = '删除分类';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_sns_category', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'postlist') {
    $navtitle = '话题管理';
    $current = '话题列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'postlist');
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=0 and ' . $condition . " ORDER BY `CREATETIME` DESC,\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and id=:boardid';
            $boardtitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':boardid' => $data[$k]['boardid']));
            $data[$k]['boardtitle'] = $boardtitle;
            if ($data[$k]['checked'] == 1) {
                $data[$k]['checked'] = '已审';
            } else {
                $data[$k]['checked'] = '待审';
            }
            if ($data[$k]['deleted'] == 1) {
                $data[$k]['deleted'] = '隐藏';
            } else {
                $data[$k]['deleted'] = '显示';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('postlist');
} elseif ($operation == 'postsearch') {
    $navtitle = '话题管理';
    $current = '话题列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'postlist');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
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
        $condition .= $condition1;
    }
    $condition .= $this->myrcondition();
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['pid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and id=:boardid';
            $boardtitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':boardid' => $data[$k]['boardid']));
            $data[$k]['boardtitle'] = $boardtitle;
            if ($data[$k]['checked'] == 1) {
                $data[$k]['checked'] = '已审';
            } else {
                $data[$k]['checked'] = '待审';
            }
            if ($data[$k]['deleted'] == 1) {
                $data[$k]['deleted'] = '隐藏';
            } else {
                $data[$k]['deleted'] = '显示';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('postlist');
} elseif ($operation == 'postedit') {
    $navtitle = '话题管理';
    $current = '编辑话题';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $imagelist = iserializer($_GPC['images']);
        $data = array('title' => $_GPC['title'], 'images' => $imagelist, 'checked' => $_GPC['checked'], 'deleted' => $_GPC['deleted']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_sns_post', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'postlist')) . $mywe['direct']);
        exit(0);
    }
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $imagelist = iunserializer($item['images']);
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select address from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and (openid=:openid or uid=:uid)';
    $address = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':openid' => $item['openid'], ':uid' => $item['uid']));
    include $this->mywtpl('postpost');
} elseif ($operation == 'compedit') {
    $navtitle = '投诉管理';
    $current = '编辑投诉';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $imagelist = iserializer($_GPC['images']);
        $data = array('title' => $_GPC['title'], 'images' => $imagelist, 'checked' => $_GPC['checked'], 'deleted' => $_GPC['deleted']);
        $glue = 'AND';
        $result = pdo_update('rhinfo_zyxq_sns_post', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'postlist')) . $mywe['direct']);
        exit(0);
    }
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $imagelist = iunserializer($item['images']);
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid';
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select address from ' . tablename('rhinfo_zyxq_member') . ' where weid = :weid and pid = :pid and rid=:rid and (openid=:openid or uid=:uid)';
    $address = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid'], ':openid' => $item['openid'], ':uid' => $item['uid']));
    include $this->mywtpl('comppost');
} elseif ($operation == 'postdelete') {
    $current = '删除话题';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_sns_post', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'postlist1') {
    $navtitle = '话题管理';
    $current = '话题列表';
    $myret = 1;
    $rights = $this->myrights(11, $mydo, 'postlist');
    $condition .= $this->myrcondition();
    $boardid = intval($_GPC['id']);
    $condition .= ' and boardid=:boardid ';
    $params[':boardid'] = $boardid;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND content LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $createtime = $_GPC['createtime'];
    if ($createtime) {
        $starttime = strtotime($createtime['start']);
        $endtime = strtotime($createtime['end']);
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    $condition .= ' and createtime >=' . $starttime . ' and createtime<=' . $endtime;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['pid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and id=:boardid';
            $boardtitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':boardid' => $data[$k]['boardid']));
            $data[$k]['boardtitle'] = $boardtitle;
            if ($data[$k]['checked'] == 1) {
                $data[$k]['checked'] = '已审';
            } else {
                $data[$k]['checked'] = '待审';
            }
            if ($data[$k]['deleted'] == 1) {
                $data[$k]['deleted'] = '隐藏';
            } else {
                $data[$k]['deleted'] = '显示';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('postlist1');
} elseif ($operation == 'replylist') {
    $navtitle = '回复管理';
    $current = '回复列表';
    $rights = $this->myrights(11, $mydo, 'replylist');
    $condition .= $this->myrcondition();
    $postid = intval($_GPC['id']);
    if ($postid) {
        $condition .= ' and postid=:postid ';
        $params[':postid'] = $postid;
        $myret = 1;
    } else {
        $condition .= ' and postid >0 ';
        $myret = 0;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_post') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and id=:boardid';
            $boardtitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':boardid' => $data[$k]['boardid']));
            $data[$k]['boardtitle'] = $boardtitle;
            if ($data[$k]['checked'] == 1) {
                $data[$k]['checked'] = '已审';
            } else {
                $data[$k]['checked'] = '待审';
            }
            if ($data[$k]['deleted'] == 1) {
                $data[$k]['deleted'] = '隐藏';
            } else {
                $data[$k]['deleted'] = '显示';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('replylist');
} elseif ($operation == 'complain') {
    $navtitle = '投诉管理';
    $current = '投诉列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'complain');
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND complaint_text LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
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
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_complain') . ' where deleted=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_complain') . ' where deleted=0 and ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . ' where weid = :weid and id=:postid';
            $post = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':postid' => $data[$k]['postsid']));
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $post['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and id=:boardid';
            $boardtitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':boardid' => $post['boardid']));
            $data[$k]['boardtitle'] = $boardtitle . '-' . $post['title'];
            if ($data[$k]['checked'] == 1) {
                $data[$k]['checked'] = '已审';
            } else {
                $data[$k]['checked'] = '待审';
            }
            if ($data[$k]['deleted'] == 1) {
                $data[$k]['deleted'] = '隐藏';
            } else {
                $data[$k]['deleted'] = '显示';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('complain');
} elseif ($operation == 'compcateadd') {
    $navtitle = '投诉管理';
    $current = '新增分类';
    $myret = 0;
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        if ($cate == 1) {
            $pid = 0;
            $rid = 0;
        } else {
            $pid = $_GPC['pid'];
            $rid = $_GPC['rid'];
        }
        if (!empty($_GPC['catid'])) {
            $k = 0;
            while (!($k >= count($_GPC['catid']))) {
                $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'title' => $_GPC['title'][$k], 'displayorder' => $_GPC['displayorder'][$k], 'status' => $_GPC['status'][$k]);
                if (empty($_GPC['catid'][$k])) {
                    pdo_insert('rhinfo_zyxq_sns_complaincate', $data);
                    $id = pdo_insertid();
                    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
                } else {
                    pdo_update('rhinfo_zyxq_sns_complaincate', $data, array('id' => $_GPC['catid'][$k]));
                    $this->mysyslog($mywe['pid'], $mydo, $operation, '修改分类', '修改分类' . 'id=' . $id);
                }
                ($k += 1) + -1;
            }
        }
        header('Location:' . $this->createWeburl($mydo, array('op' => 'compcatelist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $condition .= $this->myrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('compcateadd');
} elseif ($operation == 'compcateedit') {
    $navtitle = '投诉管理';
    $current = '编辑分类';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'title' => $_GPC['title'], 'displayorder' => $_GPC['displayorder'], 'status' => $_GPC['status']);
        $result = pdo_update('rhinfo_zyxq_sns_complaincate', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'compcatelist')) . $mywe['direct']);
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_complaincate') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    if ($item['rid'] == 0) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('compcateedit');
} elseif ($operation == 'compcatelist') {
    $navtitle = '投诉管理';
    $current = ' 投诉分类';
    $myret = 1;
    $rights = $this->myrights(11, $mydo, 'complain');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_complaincate') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_complaincate') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            if ($data[$k]['status'] == 1) {
                $data[$k]['status'] = '显示';
            } else {
                $data[$k]['status'] = '隐藏';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('compcatelist');
} elseif ($operation == 'compcatedelete') {
    $current = '删除分类';
    $id = intval($_GPC['id']);
    $result = pdo_update('rhinfo_zyxq_sns_complaincate', array('deleted' => 1), array('id' => $id, 'weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'manager') {
    $navtitle = '版主管理';
    $current = '版主列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'manager');
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
        $condition .= ' AND nickname LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_manager') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_manager') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and pid = :pid and rid=:rid and id=:boardid';
            $boardtitle = pdo_fetchcolumn($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':boardid' => $data[$k]['bid']));
            $data[$k]['boardtitle'] = $boardtitle;
            if ($data[$k]['enabled'] == 1) {
                $data[$k]['enabled'] = '启用';
            } else {
                $data[$k]['enabled'] = '禁用';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('manager');
} elseif ($operation == 'manageradd') {
    $navtitle = '版主管理';
    $current = '新增版主';
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        load()->model('mc');
        $fans = array();
        $_GPC['openid'] = empty($_GPC['openid']) ? $_GPC['uid'] : $_GPC['openid'];
        $fans = mc_fansinfo($_GPC['openid'], 0, $mywe['weid']);
        if ($cate == 1) {
            $pid = 0;
            $rid = 0;
        } else {
            $pid = $_GPC['pid'];
            $rid = $_GPC['rid'];
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'bid' => $_GPC['bid'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'enabled' => $_GPC['enabled']);
        pdo_insert('rhinfo_zyxq_sns_manager', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'manager')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $myboard = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and pid = :pid and rid=:rid';
            $boards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $myboard[$regions[$m]['id']] = $boards;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    if ($cate == 1) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and pid = :pid and rid=:rid';
        $eboards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => 0, ':rid' => 0));
    }
    include $this->mywtpl('managerpost');
} elseif ($operation == 'manageredit') {
    $navtitle = '版主管理';
    $current = '编辑版主';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        load()->model('mc');
        $fans = array();
        $_GPC['openid'] = empty($_GPC['openid']) ? $_GPC['uid'] : $_GPC['openid'];
        $fans = mc_fansinfo($_GPC['openid'], 0, $mywe['weid']);
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'bid' => $_GPC['bid'], 'openid' => $_GPC['openid'], 'uid' => $_GPC['uid'], 'nickname' => $fans['nickname'], 'headimgurl' => $fans['avatar'], 'enabled' => $_GPC['enabled']);
        pdo_update('rhinfo_zyxq_sns_manager', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'manager')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $myboard = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        $m = 0;
        while (!($m >= count($regions))) {
            $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and pid = :pid and rid=:rid';
            $boards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id'], ':rid' => $regions[$m]['id']));
            $myboard[$regions[$m]['id']] = $boards;
            ($m += 1) + -1;
        }
        ($k += 1) + -1;
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_manager') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    load()->model('mc');
    $fans = array();
    $item['openid'] = empty($item['openid']) ? $item['uid'] : $item['openid'];
    $fans = mc_fansinfo($item['openid'], 0, $mywe['weid']);
    $item['nickname1'] = $fans['nickname'];
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_board') . ' where weid = :weid and pid = :pid and rid = :rid';
    $eboards = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid'], ':rid' => $item['rid']));
    if ($item['rid'] == 0) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('managerpost');
} elseif ($operation == 'member') {
    $navtitle = '社区会员';
    $current = '会员列表';
    $myret = 0;
    $rights = $this->myrights(11, $mydo, 'member');
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
        $condition .= ' AND complaint_text LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    $condition .= $this->myrcondition();
    if (!empty($regioncondition)) {
        $condition .= $regioncondition;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_member') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_member') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` DESC " . $limit;
        $data = pdo_fetchall($sql, $params);
        load()->model('mc');
        $fans = array();
        $k = 0;
        while (!($k >= count($data))) {
            $sql = 'select title from ' . tablename('rhinfo_zyxq_region') . ' where id = :id and weid = :weid';
            $region = pdo_fetchcolumn($sql, array(':id' => $data[$k]['rid'], ':weid' => $mywe['weid']));
            $data[$k]['region'] = empty($region) ? '系统默认' : $region;
            $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_level') . ' where weid=:weid and pid=:pid and rid=:rid and id=:level';
            $level = pdo_fetch($sql, array(':weid' => $mywe['weid'], ':pid' => $data[$k]['pid'], ':rid' => $data[$k]['rid'], ':level' => $data[$k]['level']));
            $data[$k]['level'] = $level['levelname'];
            $vale['levelbg'] = $level['bg'];
            $fans = mc_fansinfo($data[$k]['openid'], 0, $mywe['weid']);
            $data[$k]['nickname'] = $fans['nickname'];
            $data[$k]['avatar'] = $fans['avatar'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('member');
} elseif ($operation == 'memberdelete') {
    $current = '删除会员';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_sns_member', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
} elseif ($operation == 'memberblack') {
    $current = '会员状态';
    $id = intval($_GPC['id']);
    $data = array('isblack' => $_GPC['isblack']);
    $glue = 'AND';
    $result = pdo_update('rhinfo_zyxq_sns_member', $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog($_GPC['pid'], $mydo, $operation, $current, $current . $_GPC['isblack'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'leveladd') {
    $navtitle = '社区会员';
    $current = '新增等级';
    $myret = 0;
    $cate = $_GPC['cate'];
    if ($_W['ispost']) {
        if ($cate == 1) {
            $pid = 0;
            $rid = 0;
        } else {
            $pid = $_GPC['pid'];
            $rid = $_GPC['rid'];
        }
        $data = array('weid' => $mywe['weid'], 'pid' => $pid, 'rid' => $rid, 'levelname' => $_GPC['levelname'], 'post' => $_GPC['post'], 'color' => $_GPC['color'], 'bg' => $_GPC['bg'], 'enabled' => $_GPC['enabled']);
        pdo_insert('rhinfo_zyxq_sns_level', $data);
        $id = pdo_insertid();
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'levellist')) . $mywe['direct']);
        exit(0);
    }
    $rcondition = $this->wyrcondition();
    $condition .= $this->myrcondition();
    $myproperty = $this->myproperty();
    $myregion = array();
    $k = 0;
    while (!($k >= count($myproperty))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
        $regions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $myproperty[$k]['id']));
        $myregion[$myproperty[$k]['id']] = $regions;
        ($k += 1) + -1;
    }
    include $this->mywtpl('levelpost');
} elseif ($operation == 'leveledit') {
    $navtitle = '社区会员';
    $current = '编辑等级';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('pid' => $_GPC['pid'], 'rid' => $_GPC['rid'], 'levelname' => $_GPC['levelname'], 'post' => $_GPC['post'], 'color' => $_GPC['color'], 'bg' => $_GPC['bg'], 'enabled' => $_GPC['enabled']);
        $result = pdo_update('rhinfo_zyxq_sns_level', $data, array('id' => $id, 'weid' => $mywe['weid']));
        $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'levellist')) . $mywe['direct']);
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
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_level') . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid = :weid and pid = :pid ' . $rcondition;
    $eregions = pdo_fetchall($sql, array(':weid' => $mywe['weid'], ':pid' => $item['pid']));
    if ($item['rid'] == 0) {
        $cate = 1;
    } else {
        $cate = 2;
    }
    include $this->mywtpl('levelpost');
} elseif ($operation == 'levellist') {
    $navtitle = '社区会员';
    $current = ' 会员等级';
    $myret = 1;
    $rights = $this->myrights(11, $mydo, 'member');
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
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_sns_level') . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_level') . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t `ID` ASC " . $limit;
        $data = pdo_fetchall($sql, $params);
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
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('levellist');
} elseif ($operation == 'leveldelete') {
    $current = '删除等级';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_sns_level', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
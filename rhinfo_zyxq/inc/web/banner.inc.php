<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '广告管理';
$mydo = 'banner';
$tablename = 'rhinfo_zyxq_banner';
$condition = ' weid = :weid ';
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$params = array(':weid' => $mywe['weid']);
$rights = $this->myrights(8, $mydo, 'list');
if ($operation == 'list') {
    $current = '广告列表';
    $myret = 0;
    $boardcate = $_GPC['boardcate'];
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title*1 ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == '1') {
                $data[$k]['status'] = '启用';
            } else {
                $data[$k]['status'] = '禁用';
            }
            if (!($data[$k]['enddate'] >= TIMESTAMP) && !empty($data[$k]['enddate'])) {
                $data[$k]['status'] = '过期';
            }
            if ($data[$k]['btype'] == '1') {
                $data[$k]['btype'] = '首页头部幻灯';
            } elseif ($data[$k]['btype'] == '2') {
                $data[$k]['btype'] = '首页广告轮播';
            } elseif ($data[$k]['btype'] == '3') {
                $data[$k]['btype'] = '首页图片魔方';
            } elseif ($data[$k]['btype'] == '4') {
                $data[$k]['btype'] = '就近服务幻灯';
            } elseif ($data[$k]['btype'] == '5') {
                $data[$k]['btype'] = '智能门禁幻灯';
            } elseif ($data[$k]['btype'] == '6') {
                $data[$k]['btype'] = '缴费中心幻灯';
            } elseif ($data[$k]['btype'] == '7') {
                $data[$k]['btype'] = '最新动态幻灯';
            } elseif ($data[$k]['btype'] == '8') {
                $data[$k]['btype'] = '周边商家幻灯';
            } elseif ($data[$k]['btype'] == '9') {
                $data[$k]['btype'] = '社区论坛幻灯';
            } elseif ($data[$k]['btype'] == '10') {
                $data[$k]['btype'] = '常见问题幻灯';
            } elseif ($data[$k]['btype'] == '11') {
                $data[$k]['btype'] = '便民电话幻灯';
            } elseif ($data[$k]['btype'] == '12') {
                $data[$k]['btype'] = '快递驿站幻灯';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'search') {
    $current = '广告列表';
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
    }
    if (!empty($_GPC['startdate'])) {
        $starttime = strtotime($_GPC['startdate']);
        $condition .= ' and startdate>=' . $starttime;
    }
    if (!empty($_GPC['enddate'])) {
        $endtime = strtotime($_GPC['enddate']);
        $condition .= ' and enddate<=' . strtotime('+1 days', $endtime);
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    if ($total > 0) {
        $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . ' ORDER BY title*1 ASC ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['enabled'] == '1') {
                $data[$k]['status'] = '启用';
            } else {
                $data[$k]['status'] = '禁用';
            }
            if ($data[$k]['btype'] == '1') {
                $data[$k]['btype'] = '首页头部幻灯';
            } elseif ($data[$k]['btype'] == '2') {
                $data[$k]['btype'] = '首页广告轮播';
            } elseif ($data[$k]['btype'] == '3') {
                $data[$k]['btype'] = '首页图片魔方';
            } elseif ($data[$k]['btype'] == '4') {
                $data[$k]['btype'] = '就近服务幻灯';
            } elseif ($data[$k]['btype'] == '5') {
                $data[$k]['btype'] = '智能门禁幻灯';
            } elseif ($data[$k]['btype'] == '6') {
                $data[$k]['btype'] = '缴费中心幻灯';
            } elseif ($data[$k]['btype'] == '7') {
                $data[$k]['btype'] = '最新动态幻灯';
            } elseif ($data[$k]['btype'] == '8') {
                $data[$k]['btype'] = '周边商家幻灯';
            } elseif ($data[$k]['btype'] == '9') {
                $data[$k]['btype'] = '社区论坛幻灯';
            } elseif ($data[$k]['btype'] == '10') {
                $data[$k]['btype'] = '常见问题幻灯';
            } elseif ($data[$k]['btype'] == '11') {
                $data[$k]['btype'] = '便民电话幻灯';
            } elseif ($data[$k]['btype'] == '12') {
                $data[$k]['btype'] = '快递驿站幻灯';
            }
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('list');
} elseif ($operation == 'add') {
    $current = '新增广告';
    if ($_W['ispost']) {
        $data = array('weid' => $mywe['weid'], 'title' => $_GPC['title'], 'btype' => $_GPC['btype'], 'link' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'bgimage' => $_GPC['bgimage'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']), 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
        pdo_insert($tablename, $data);
        $id = pdo_insertid();
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list')) . $mywe['direct']);
        exit(0);
    }
    include $this->mywtpl('post');
} elseif ($operation == 'edit') {
    $current = '编辑广告';
    $id = intval($_GPC['id']);
    if ($_W['ispost']) {
        $data = array('title' => $_GPC['title'], 'btype' => $_GPC['btype'], 'link' => $_GPC['link'], 'wxappid' => $_GPC['wxappid'], 'wxapppage' => $_GPC['wxapppage'], 'bgimage' => $_GPC['bgimage'], 'thumb' => $_GPC['thumb'], 'displayorder' => $_GPC['displayorder'], 'enabled' => $_GPC['enabled'], 'startdate' => strtotime($_GPC['startdate']), 'enddate' => strtotime($_GPC['enddate']));
        $glue = 'AND';
        $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
        $this->mysyslog($pid, $mydo, $operation, $current, $current . 'id=' . $id);
        header('Location:' . $this->createWeburl($mydo, array('op' => 'list', 'pid' => $pid, 'rid' => $rid)) . $mywe['direct']);
        exit(0);
    }
    $sql = 'select * from ' . tablename($tablename) . ' where id = :id and weid = :weid';
    $item = pdo_fetch($sql, array(':id' => $id, ':weid' => $mywe['weid']));
    include $this->mywtpl('post');
} elseif ($operation == 'deleteall') {
    $current = '删除全部广告';
    $glue = 'AND';
    $result = pdo_delete($tablename, array('weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($pid, $mydo, $operation, $current, $current . 'rid=' . $rid);
    exit(0);
} elseif ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    exit(0);
} elseif ($operation == 'status') {
    $current = '状态';
    $id = intval($_GPC['id']);
    $data = array('enabled' => $_GPC['status']);
    $glue = 'AND';
    $result = pdo_update($tablename, $data, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '操作失败!';
    }
    $this->mysyslog(0, $mydo, $operation, $current, $current . $_GPC['status'] . '-id=' . $id);
    exit(0);
} elseif ($operation == 'bannerhit') {
    $current = '广告点击';
    $myret = 1;
    $id = intval($_GPC['id']);
    $condition .= ' and bannerid = :bannerid ';
    $params[':bannerid'] = $id;
    $sql = 'SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_banner_statistics') . ' where pid=0 and rid=0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    load()->model('mc');
    $fans = array();
    if ($total > 0) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_banner_statistics') . ' where pid=0 and rid=0 and ' . $condition . ' order by clicktime desc ' . $limit;
        $data = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($data))) {
            $fans = mc_fansinfo($data[$k]['uid'], 0, $mywe['weid']);
            $data[$k]['avatar'] = $fans['avatar'];
            $data[$k]['nickname'] = $fans['nickname'];
            ($k += 1) + -1;
        }
        $pager = pagination($total, $pindex, $psize);
    }
    include $this->mywtpl('hit');
} elseif ($operation == 'delhit') {
    $current = '删除点击';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete('rhinfo_zyxq_banner_statistics', array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    $this->mysyslog($mywe['pid'], $mydo, $operation, $current, $current . 'id=' . $id);
    exit(0);
}
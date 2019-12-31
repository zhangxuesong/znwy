<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'fans';
$this->my_check_web();
$mywe = $this->mywe;
$mydo = 'query';
$condition = ' uniacid = :weid ';
$params = array(':weid' => $mywe['weid']);
if ($operation == 'fans') {
    $kwd = trim($_GPC['keyword']);
    if ($_W['ispost'] && !empty($kwd)) {
        if (!empty($kwd)) {
            $condition .= ' AND (`realname` LIKE :keyword or `nickname` LIKE :keyword or `mobile` LIKE :keyword)';
            $params[':keyword'] = '%' . $kwd . '%';
        }
        $sql = 'SELECT uid,nickname,realname,mobile,avatar FROM ' . tablename('mc_members') . ' WHERE ' . $condition;
        $data = pdo_fetchall($sql, $params);
        load()->model('mc');
        $fans = array();
        if (!empty($data)) {
            $k = 0;
            while (!($k >= count($data))) {
                $sql = 'select openid from ' . tablename('mc_mapping_fans') . ' where uid=:uid and uniacid=:weid';
                $openid = pdo_fetchcolumn($sql, array(':uid' => $data[$k]['uid'], ':weid' => $mywe['weid']));
                $data[$k]['openid'] = $openid;
                $fans = mc_fansinfo($data[$k]['openid'], 0, $mywe['weid']);
                $data[$k]['avatar'] = $fans['avatar'];
                ($k += 1) + -1;
            }
            exit(json_encode($data));
        } else {
            echo 'none';
            exit(0);
        }
    }
    include $this->mywtpl('fans');
} elseif ($operation == 'mfans') {
    $kwd = trim($_GPC['keyword']);
    if ($_W['ispost'] && !empty($kwd)) {
        if (!empty($kwd)) {
            $condition .= ' AND (`realname` LIKE :keyword or `nickname` LIKE :keyword or `mobile` LIKE :keyword)';
            $params[':keyword'] = '%' . $kwd . '%';
        }
        $sql = 'SELECT uid,nickname,realname,mobile,avatar FROM ' . tablename('mc_members') . ' WHERE ' . $condition;
        $data = pdo_fetchall($sql, $params);
        if ($data) {
            $k = 0;
            while (!($k >= count($data))) {
                $data[$k]['avatar'] = tomedia($data[$k]['avatar']);
                $sql = 'select openid from ' . tablename('mc_mapping_fans') . ' where uid=:uid and uniacid=:weid';
                $openid = pdo_fetchcolumn($sql, array(':uid' => $data[$k]['uid'], ':weid' => $mywe['weid']));
                $data[$k]['openid'] = $openid;
                ($k += 1) + -1;
            }
            exit(json_encode($data));
        } else {
            echo 'none';
            exit(0);
        }
    }
    include $this->mywtpl('mfans');
} elseif ($operation == 'icon') {
    include $this->mywtpl('selecticon');
} elseif ($operation == 'url') {
    include $this->mywtpl('selecturl');
} elseif ($operation == 'msg') {
    $navtitle = '系统管理';
    $current = '最新消息';
    include $this->mywtpl('newmsg');
} elseif ($operation == 'initpage') {
    if ($_W['ispost']) {
        $category = $_GPC['category'];
        $title = $_GPC['title'];
        $link = $_GPC['link'];
        $thumb = $_GPC['thumb'];
        if (!empty($category)) {
            $k = 0;
            while (!($k >= count($category))) {
                $data = array();
                $data = array('weid' => $mywe['weid'], 'title' => $title[$k], 'link' => $link[$k], 'thumb' => $thumb[$k], 'enabled' => 1, 'cuid' => $mywe['uid'], 'ctime' => TIMESTAMP);
                if ($category[$k] == 1) {
                    $data['btype'] = 1;
                    $data['startdate'] = strtotime(date('Y-m-d', TIMESTAMP));
                    $data['enddate'] = strtotime('+1 years', $data['startdate']);
                    pdo_insert('rhinfo_zyxq_banner', $data);
                } else {
                    $data['category'] = 1;
                    pdo_insert('rhinfo_zyxq_nav', $data);
                }
                ($k += 1) + -1;
            }
        }
    }
    $navtitle = '页面管理';
    $current = '初始默认页面';
    $advs = array();
    $advs[] = array('link' => mymurl('service'), 'title' => 'banner1', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/banner/1.png');
    $advs[] = array('link' => mymurl('forum'), 'title' => 'banner2', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/banner/2.png');
    $advs[] = array('link' => mymurl('article'), 'title' => 'banner3', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/banner/3.png');
    $navs = array();
    $navs[] = array('link' => mymurl('service/property'), 'title' => '物业介绍', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/1.png');
    $navs[] = array('link' => mymurl('article'), 'title' => '最新动态', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/2.png');
    $navs[] = array('link' => mymurl('fee'), 'title' => '缴费中心', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/3.png');
    $navs[] = array('link' => mymurl('forum'), 'title' => '社区互动', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/4.png');
    $navs[] = array('link' => mymurl('auth/regproperty'), 'title' => '物业入驻', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/5.png');
    $navs[] = array('link' => mymurl('opendoor'), 'title' => '智能门禁', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/6.png');
    $navs[] = array('link' => mymurl('home/scanbind'), 'title' => '绑定房产', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/7.png');
    $navs[] = array('link' => mymurl('manage'), 'title' => '物业中心', 'thumb' => RHINFO_ZYXQ_STATIC . 'mobile/images/nav/8.png');
    include $this->mywtpl();
}
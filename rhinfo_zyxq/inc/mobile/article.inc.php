<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$curr = 'article';
$mydo = 'article';
$user = $this->getmyinfo($_W['member']['uid']);
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($res) {
        if ($res['arrearagelimit6']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$_share['desc'] = '关注最新资讯，掌控最新动态';
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
if ($operation == 'index') {
    $rid = intval($_GPC['rid']);
    $cateid = intval($_GPC['cateid']);
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($_GPC['psize']) ? 5 : $_GPC['psize'];
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        if (empty($cateid)) {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_article') . ' where weid=:weid and (rid=:rid or rid=0) and status=1 order by isrecommand,ctime desc ' . $limit;
            $articles = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
        } else {
            $sql = 'select * from ' . tablename('rhinfo_zyxq_article') . ' where weid=:weid and (rid=:rid or rid=0) and cid=:cateid and status=1 order by isrecommand,ctime desc ' . $limit;
            $articles = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':cateid' => $cateid));
        }
        $times = 0;
        $k = 0;
        while (!($k >= count($articles))) {
            $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article_log') . ' where weid=:weid and aid=:aid ';
            $times = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':aid' => $articles[$k]['id']));
            $articles[$k]['times'] = $times;
            $articles[$k]['images'] = iunserializer($articles[$k]['images']);
            ($k += 1) + -1;
        }
        if (empty($articles)) {
            echo '';
        } else {
            include $this->mymtpl('article');
        }
        exit(0);
    }
    if ($this->syscfg['isoneregion'] == 1) {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' limit 1';
        $item = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        $rid = $item['id'];
    } else {
        $rid = empty($rid) ? $user['rid'] : $rid;
    }
    $sql = 'select link,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and rid = :rid and  btype=7 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    if (empty($banners)) {
        $sql = 'select link,thumb,0 as `pid`,0 as `rid` ,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=7 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article') . ' where weid=:weid and (rid=:rid or rid=0) and status=1';
    $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $sql = 'select * from ' . tablename('rhinfo_zyxq_article_category') . ' where weid=:weid and (rid=:rid or rid=0) and enabled=1 ';
    $categorys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    include $this->mymtpl('index');
} elseif ($operation == 'detail') {
    $id = intval($_GPC['id']);
    $sql = 'select * from ' . tablename('rhinfo_zyxq_article') . ' where weid=:weid and id = :id';
    $item = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    $content = stripslashes($item['content']);
    $content = html_entity_decode($content);
    $read = 0;
    if ($_W['member']['uid']) {
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article_log') . ' where  weid=:weid and aid = :aid and uid = :uid';
        $total = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':aid' => $id, ':uid' => $_W['member']['uid']));
        if ($total > 0) {
            $read = 1;
        } else {
            $data = array('weid' => $_W['uniacid'], 'aid' => $id, 'uid' => $_W['member']['uid'], 'ctime' => TIMESTAMP);
            pdo_insert('rhinfo_zyxq_article_log', $data);
        }
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_article_log') . ' where  weid=:weid and aid = :aid';
        $times = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':aid' => $id));
    }
    $myurl = mymurl('article/detail', array('id' => $id));
    $_share['title'] = $item['title'];
    $_share['link'] = $this->my_mobileurl($myurl);
    include $this->mymtpl('detail');
}
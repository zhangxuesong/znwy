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
$curr = 'question';
$mydo = 'question';
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
$sql = 'select * from ' . tablename('rhinfo_zyxq_secprg') . ' where do=:do and op=:op ';
$secprg = pdo_fetch($sql, array(':do' => $mydo, ':op' => 'list'));
if ($operation == 'index') {
    $category = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_qacate') . ' where isrecommand=1 and enabled=1 and weid=:weid order by displayorder desc limit 8 ', array(':weid' => $_W['uniacid']));
    $sql = 'select link,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and rid = :rid and btype=10 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $user['rid']));
    if (empty($banners)) {
        $sql = 'select link,thumb,0 as `pid`, 0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=10 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    include $this->mymtpl('index');
} elseif ($operation == 'list') {
    $cate = intval($_GPC['cate']);
    $keyword = trim($_GPC['keyword']);
    $isrecommand = intval($_GPC['isrecommand']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $condition = ' q.weid=:weid and q.status=1 and c.enabled=1 ';
    if (!empty($cate)) {
        $condition .= ' and q.cateid=' . $cate . ' ';
    }
    if (!empty($isrecommand)) {
        $condition .= ' and q.isrecommand=1 ';
    }
    if (!empty($keyword)) {
        $condition .= ' and ((q.title like \'%' . $keyword . '%\') or (q.keywords like \'%' . $keyword . '%\')) ';
    }
    $sql = 'SELECT q.*, c.title as catename FROM ' . tablename('rhinfo_zyxq_question') . ' q left join' . tablename('rhinfo_zyxq_qacate') . ' `c` on c.id=q.cateid and c.weid=q.weid where  1 and ' . $condition . ' ORDER BY q.displayorder DESC,q.id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rhinfo_zyxq_question') . ' q left join' . tablename('rhinfo_zyxq_qacate') . ' c on c.id=q.cateid and c.weid=q.weid where  1 and ' . $condition . ' ', $params);
    if (!empty($total)) {
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['content'] = iunserializer($list[$k]['content']);
            $list[$k]['content'] = htmlspecialchars_decode($list[$k]['content']);
            $list[$k]['content'] = mylazy($list[$k]['content']);
            ($k += 1) + -1;
        }
    }
    show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
} elseif ($operation == 'detail') {
    $id = intval($_GPC['id']);
    $item = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_question') . ' where id=:id and status=1 and weid=:weid limit 1', array(':id' => $id, ':weid' => $_W['uniacid']));
    $item['content'] = iunserializer($item['content']);
    $item['content'] = htmlspecialchars_decode($item['content']);
    $item['content'] = mylazy($item['content']);
    include $this->mymtpl('detail');
} elseif ($operation == 'question') {
    $cate = intval($_GPC['cate']);
    $category = pdo_fetch('select * from ' . tablename('rhinfo_zyxq_qacate') . ' where id=:id and enabled=1 and weid=:weid limit 1 ', array(':id' => $cate, ':weid' => $_W['uniacid']));
    include $this->mymtpl('list');
} elseif ($operation == 'category') {
    $category = pdo_fetchall('select * from ' . tablename('rhinfo_zyxq_qacate') . ' where enabled=1 and weid=:weid order by displayorder desc ', array(':weid' => $_W['uniacid']));
    include $this->mymtpl('category');
}
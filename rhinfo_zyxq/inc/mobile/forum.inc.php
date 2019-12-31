<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_mobile();
$this->checkmember();
$curr = 'home';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$condition = ' weid = :weid';
$params = array(':weid' => $_W['uniacid']);
$mydo = 'forum';
if (!$_W['isajax']) {
    $res = $this->getarrearage($_W['member']['uid']);
    if ($res) {
        if ($res['arrearagelimit5']) {
            header('Location:' . $this->createMobileurl('fee', array('op' => 'index')));
            exit(0);
        }
    }
}
$myurl = $this->createMobileurl($mydo);
$_share = $this->rhinfo_share();
$user = $this->getmyinfo($_W['member']['uid']);
if ($_W['minirid']) {
    $user['rid'] = $_W['minirid'];
}
$rid = $_GPC['rid'];
if ($rid) {
    $sql = 'select * from ' . tablename('rhinfo_zyxq_region') . ' where ' . $condition . ' and id = :id';
    $region = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $rid));
    if ($region) {
        $pid = $region['pid'];
    } elseif ($user['rid'] && $user['isbind']) {
        $pid = $user['pid'];
        $rid = $user['rid'];
    } else {
        $directurl = $this->createMobileurl('home', array('op' => 'list'));
        $this->mymsg('error', '友情提醒', '抱歉，您还没有绑定该小区房产.', $directurl);
    }
} elseif ($user['rid'] && $user['isbind']) {
    $pid = $user['pid'];
    $rid = $user['rid'];
} else {
    $sql = 'select * FROM ' . tablename('rhinfo_zyxq_team') . ' as t left join ' . tablename('rhinfo_zyxq_category') . ' as c on t.cid=c.id where t.status=1 and t.weid = :weid and (t.openid=:openid or t.uid=:uid) and c.type=4 and c.right15=1 ';
    $team = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_['member']['uid']));
    if (!empty($team)) {
        $pid = $team['pid'];
        $rid = $team['rid'];
    } else {
        $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_manager') . ' where ' . $condition . ' and (openid = :openid or uid=:uid)';
        $sns_manager = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':openid' => $_W['openid'], ':uid' => $_W['member']['uid']));
        if (!empty($sns_manager)) {
            $pid = $sns_manager['pid'];
            $rid = $sns_manager['rid'];
        } else {
            $this->mymsg('error', '友情提醒', '抱歉，您还没有绑定该小区房产.', '');
        }
    }
}
if (!empty($pid) && !empty($rid)) {
    $this->check_snsmember($pid, $rid);
}
if ($operation == 'index') {
    $sql = 'select link,wxappid,wxapppage,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and rid=:rid and btype=9 and boardcate=0 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now()))  order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    if (empty($banners)) {
        $sql = 'select link,wxappid,wxapppage,thumb,0 as `pid`,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=9 and enabled = 1 and (enddate=0 or enddate >=UNIX_TIMESTAMP(now())) order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid=:weid and (rid=:rid or rid=0) and enabled=1 and isrecommand=1 order by displayorder desc';
    $category = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $sql = 'select sb.id,sb.title,sb.logo,sb.desc  from ' . tablename('rhinfo_zyxq_sns_board') . ' as sb left join ' . tablename('rhinfo_zyxq_sns_category') . ' as sc on sc.id = sb.cid where sb.weid=:weid and (sb.rid=:rid or sb.rid=0) and sb.isrecommand=1 and sb.status=1 and sc.enabled = 1 order by sb.displayorder desc';
    $recommands = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $k = 0;
    while (!($k >= count($recommands))) {
        $recommands[$k]['postcount'] = $this->getPostCount($recommands[$k]['id']);
        $recommands[$k]['followcount'] = $this->getFollowCount($recommands[$k]['id']);
        ($k += 1) + -1;
    }
    include $this->mymtpl('index');
} elseif ($operation == 'cateindex') {
    $cid = intval($_GPC['cid']);
    $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid=:weid and rid=:rid and id=:cid and enabled=1 order by displayorder desc';
    $catetitle = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid, ':cid' => $cid));
    $sql = 'select link,wxappid,wxapppage,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and pid=:pid and rid=:rid and btype=9 and boardcate=:cid and enabled = 1 order by displayorder desc';
    $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid, ':cid' => $cid));
    if (empty($banners)) {
        $sql = 'select link,wxappid,wxapppage,thumb,pid,rid,id from ' . tablename('rhinfo_zyxq_rbanner') . ' where weid=:weid and rid=:rid and btype=9 and boardcate=0 and enabled = 1 order by displayorder desc';
        $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
        if (empty($banners)) {
            $sql = 'select link,wxappid,wxapppage,thumb,0 as `pid`,0 as `rid`,id from ' . tablename('rhinfo_zyxq_banner') . ' where weid=:weid and btype=9 and enabled = 1 order by displayorder desc';
            $banners = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
        }
    }
    $sql = 'select sb.id,sb.title,sb.logo,sb.`desc`  from ' . tablename('rhinfo_zyxq_sns_board') . ' as sb left join ' . tablename('rhinfo_zyxq_sns_category') . ' as sc on sc.id = sb.cid where sb.weid=:weid and (sb.pid=:pid or sb.pid=0) and (sb.rid=:rid or sb.rid=0) and sb.cid=:cid and sb.status=1 and sc.enabled = 1 order by sb.displayorder desc';
    $recommands = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':pid' => $item['pid'], ':rid' => $rid, ':cid' => $cid));
    $k = 0;
    while (!($k >= count($recommands))) {
        $recommands[$k]['postcount'] = $this->getPostCount($recommands[$k]['id']);
        $recommands[$k]['followcount'] = $this->getFollowCount($recommands[$k]['id']);
        ($k += 1) + -1;
    }
    include $this->mymtpl('cateindex');
} elseif ($operation == 'boardlist') {
    $condition = ' sb.weid=:weid and sb.status=1';
    $condition .= ' and (sb.pid=:pid or sb.pid=0) and (sb.rid = :rid or sb.rid=0)';
    $params[':pid'] = $pid;
    $params[':rid'] = $rid;
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        if (!empty($_GPC['keyword'])) {
            $condition .= ' AND sb.title LIKE \'%' . $_GPC['keyword'] . '%\'';
        }
        $sql = 'SELECT sb.id,sb.title,sb.logo FROM ' . tablename('rhinfo_zyxq_sns_board') . ' as sb left join ' . tablename('rhinfo_zyxq_sns_category') . ' as sc on sc.id = sb.cid  WHERE ' . $condition . ' and sb.status=1 and sc.enabled = 1  ORDER BY sb.displayorder DESC ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['postcount'] = $this->getPostCount($list[$k]['id']);
            $list[$k]['followcount'] = $this->getFollowCount($list[$k]['id']);
            $list[$k]['logo'] = tomedia($list[$k]['logo']);
            $list[$k]['url'] = $this->createMobileUrl($mydo, array('op' => 'board', 'id' => $list[$k]['id']));
            ($k += 1) + -1;
        }
        $sql = 'SELECT count(*) FROM ' . tablename('rhinfo_zyxq_sns_board') . ' as sb left join ' . tablename('rhinfo_zyxq_sns_category') . ' as sc on sc.id = sb.cid  WHERE ' . $condition . ' and sb.status=1 and sc.enabled = 1';
        $total = pdo_fetchcolumn($sql, $params);
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('boardlist');
} elseif ($operation == 'board') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        $this->mymsg('error', '未找到版块!', '');
    }
    $board = $this->getBoard($id, $pid, $rid);
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    $postcount = $this->getPostCount($board['id']);
    $followcount = $this->getFollowCount($board['id']);
    $isfollow = $this->isFollow($board['id']);
    $tops = $this->getTops($board['id']);
    $sql = 'SELECT id,title FROM ' . tablename('rhinfo_zyxq_sns_complaincate') . ' WHERE weid = :weid and rid=:rid ORDER BY displayorder asc';
    $catelist = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $condition .= ' and (rid=:rid or rid=0) and boardid=:bid and postid=0 and deleted=0 ';
    $params[':rid'] = $rid;
    $params[':bid'] = $id;
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where deleted = 0 and ' . $condition;
    $total = pdo_fetchcolumn($sql, $params);
    include $this->mymtpl('board');
} elseif ($operation == 'postlist') {
    $bid = intval($_GPC['bid']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $condition .= ' and (rid=:rid or rid=0) and boardid=:bid and postid=0 and deleted=0 ';
    $params[':rid'] = $rid;
    $params[':bid'] = $bid;
    $isManager = $this->isManager($pid, $rid, $bid, $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        $condition .= ' and `checked`=1';
    }
    $sql = 'select * from ' . tablename('rhinfo_zyxq_sns_post') . '  where deleted=0 and' . $condition . ' ORDER BY createtime desc ' . $limit;
    $list = pdo_fetchall($sql, $params);
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['avatar'] = tomedia($list[$k]['avatar']);
        $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $list[$k]['id']));
        $list[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=:pid limit 1', array(':pid' => $list[$k]['id']));
        $list[$k]['boardtitle'] = pdo_fetchcolumn('select title from ' . tablename('rhinfo_zyxq_sns_board') . ' where id=:boardid limit 1', array(':boardid' => $list[$k]['boardid']));
        $images = array();
        $images1 = array();
        $images2 = array();
        $rowimages = iunserializer($list[$k]['images']);
        if (is_array($rowimages) && !empty($rowimages)) {
            $m = 0;
            while (!($m >= count($rowimages))) {
                if (count($rowimages) == 4) {
                    if ($m > 1) {
                        $images2[] = tomedia($rowimages[$m]);
                    } else {
                        $images1[] = tomedia($rowimages[$m]);
                    }
                } elseif (count($rowimages) == 5) {
                    if ($m > 1) {
                        $images2[] = tomedia($rowimages[$m]);
                    } else {
                        $images1[] = tomedia($rowimages[$m]);
                    }
                } elseif (count($rowimages) == 3) {
                    if ($m > 0) {
                        $images2[] = tomedia($rowimages[$m]);
                    } else {
                        $images1[] = tomedia($rowimages[$m]);
                    }
                } else {
                    $images1[] = tomedia($rowimages[$m]);
                }
                ($m += 1) + -1;
            }
        }
        $list[$k]['images'] = array_merge($images1, $images2);
        $list[$k]['images1'] = $images1;
        $list[$k]['images2'] = $images2;
        if (count($rowimages) == 1) {
            $list[$k]['imagewidth'] = '99%';
            $list[$k]['imageheight'] = '150';
        } elseif (count($rowimages) == 2) {
            $list[$k]['imagewidth'] = '49%';
            $list[$k]['imageheight'] = '150';
        } elseif (count($rowimages) == 3) {
            $list[$k]['imagewidth'] = '99%';
            $list[$k]['imagewidth1'] = '49%';
            $list[$k]['imageheight'] = '150';
            $list[$k]['imageheight1'] = '100';
        } elseif (count($rowimages) == 4) {
            $list[$k]['imagewidth'] = '49%';
            $list[$k]['imagewidth1'] = '49%';
            $list[$k]['imageheight'] = '100';
            $list[$k]['imageheight1'] = '100';
        } elseif (count($rowimages) == 5) {
            $list[$k]['imagewidth'] = '49%';
            $list[$k]['imagewidth1'] = '32%';
            $list[$k]['imageheight'] = '100';
            $list[$k]['imageheight1'] = '100';
        }
        $list[$k]['imagecount'] = count($rowimages);
        $list[$k]['content'] = replaceContent($list[$k]['content']);
        $list[$k]['createtime'] = timeBefore($list[$k]['createtime']);
        $list[$k]['parent'] = false;
        if (!empty($list[$k]['replyid'])) {
            $parentPost = $this->getPost($list[$k]['replyid']);
            $list[$k]['parent'] = array('nickname' => $parentPost['nickname'], 'content' => replaceContent($parentPost['content']));
        }
        $isgood = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid and openid=:openid limit 1', array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['id'], ':openid' => $_W['openid']));
        $list[$k]['isgood'] = $isgood;
        $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid  limit 1', array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['id']));
        $list[$k]['member'] = array('id' => $member['id']);
        $list[$k]['level'] = $level;
        $list[$k]['floor'] = ($pindex - 1) * $psize + $k + 2;
        $list[$k]['isAuthor'] = $list[$k]['openid'] == $_W['openid'];
        $list[$k]['isManager'] = $this->isManager($pid, $rid, $bid, $_W['openid']);
        ($k += 1) + -1;
    }
    include $this->mymtpl('boarditem');
} elseif ($operation == 'postsubmit') {
    $bid = intval($_GPC['bid']);
    if ($bid == '*') {
        $boardid = explode('-', $_GPC['boardid']);
        $bid = $boardid[2];
    }
    if (empty($bid)) {
        show_json(0, '未建立版块');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    $title = trim($_GPC['title']);
    $len = istrlen($title);
    if (!($len >= 3)) {
        show_json(0, '标题最少3个汉字或字符哦~');
    }
    if (!(25 >= $len)) {
        show_json(0, '标题最多25个汉字或字符哦~');
    }
    $content = trim($_GPC['content']);
    $len = istrlen($content);
    if (!($len >= 3)) {
        show_json(0, '内容最少3个汉字或字符哦~');
    }
    if (!(1000 >= $len)) {
        show_json(0, '内容最多1000个汉字或字符哦~');
    }
    $checked = 0;
    if ($ismanager) {
        $checked = $board['needcheckmanager'] ? 0 : 1;
    } else {
        $checked = $board['needcheck'] ? 0 : 1;
    }
    if ($issupermanager) {
        $checked = 1;
    }
    if (is_array($_GPC['images'])) {
        $imgcount = count($_GPC['images']);
        if ($imgcount > 5) {
            show_json(0, '话题图片最多上传5张');
        }
    }
    $time = time();
    $data = array('weid' => $_W['uniacid'], 'boardid' => $bid, 'pid' => $pid, 'rid' => $rid, 'openid' => $_W['openid'], 'createtime' => $time, 'avatar' => $user['avatar'], 'nickname' => $user['nickname'], 'replytime' => $time, 'title' => trim($_GPC['title']), 'content' => trim($_GPC['content']), 'images' => is_array($_GPC['images']) ? iserializer($_GPC['images']) : serialize(array()), 'checked' => $checked);
    pdo_insert('rhinfo_zyxq_sns_post', $data);
    show_json(1, array('checked' => $checked));
} elseif ($operation == 'follow') {
    $bid = intval($_GPC['bid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $follow = pdo_fetch('select id from ' . tablename('rhinfo_zyxq_sns_board_follow') . ' where bid=:bid and openid=:openid limit 1', array(':bid' => $bid, ':openid' => $_W['openid']));
    if (!empty($follow)) {
        pdo_delete('rhinfo_zyxq_sns_board_follow', array('id' => $follow['id']));
        show_json(1, array('isfollow' => false));
    } else {
        $follow = array('weid' => $_W['uniacid'], 'bid' => $bid, 'openid' => $_W['openid'], 'ctime' => time());
        pdo_insert('rhinfo_zyxq_sns_board_follow', $follow);
        show_json(1, array('isfollow' => true));
    }
} elseif ($operation == 'like') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isgood = 1;
    $like = pdo_fetch('select id from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid and (uid =:uid or openid=:openid) limit 1', array(':pid' => $postid, ':uid' => $_W['member']['uid'], ':openid' => $_W['openid']));
    if (!empty($like)) {
        $isgood = 0;
        pdo_delete('rhinfo_zyxq_sns_like', array('id' => $like['id']));
    } else {
        $like = array('weid' => $_W['uniacid'], 'pid' => $postid, 'uid' => $_W['member']['uid'], 'openid' => $_W['openid']);
        pdo_insert('rhinfo_zyxq_sns_like', $like);
    }
    $time = time();
    pdo_update('rhinfo_zyxq_sns_post', array('replytime' => $time), array('id' => $postid, 'weid' => $_W['uniacid']));
    $goodcount = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $postid));
    show_json(1, array('isgood' => $isgood, 'good' => $goodcount));
} elseif ($operation == 'complain') {
    $id = intval($_GPC['id']);
    $sql = 'SELECT id,pid,openid FROM ' . tablename('rhinfo_zyxq_sns_post') . ' WHERE weid = :weid AND id = :id AND deleted = 0 ';
    $posts = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $id));
    if (empty($posts)) {
        show_json(0, '您要投诉的话题或评论不存在');
    }
    $type = intval($_GPC['type']);
    if (empty($type)) {
        show_json(0, '请选择投诉类别');
    }
    $content = trim($_GPC['content']);
    $len = istrlen($content);
    if (!($len >= 3)) {
        show_json(0, '内容最少3个汉字或字符哦~');
    }
    if (!(500 >= $len)) {
        show_json(0, '内容最多500个汉字或字符哦~');
    }
    $data = array('weid' => $_W['uniacid'], 'pid' => $pid, 'rid' => $rid, 'type' => $type, 'postsid' => $id, 'defendant' => $posts['openid'], 'complainant' => $_W['openid'], 'complaint_type' => $type, 'complaint_text' => $content, 'createtime' => time(), 'images' => is_array($_GPC['images']) ? iserializer($_GPC['images']) : serialize(array()));
    pdo_insert('rhinfo_zyxq_sns_complain', $data);
    $insert_id = pdo_insertid();
    if (empty($insert_id)) {
        show_json(0, '提交投诉失败，请重试');
    }
    show_json(1);
} elseif ($operation == 'comment') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        $this->message('参数错误');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        $this->message('未找到话题!');
    }
    $post['avatar'] = tomedia($post['avatar']);
    $board = $this->getBoard($post['boardid'], $pid, $rid);
    if (empty($board)) {
        $this->message('未找到版块!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    $post['content'] = replaceContent($post['content']);
    $post['content'] = htmlspecialchars_decode($post['content']);
    $rowimages = iunserializer($post['images']);
    $images = array();
    $images1 = array();
    $images2 = array();
    if (is_array($rowimages) && !empty($rowimages)) {
        $m = 0;
        while (!($m >= count($rowimages))) {
            if (count($rowimages) == 4) {
                if ($m > 1) {
                    $images2[] = tomedia($rowimages[$m]);
                } else {
                    $images1[] = tomedia($rowimages[$m]);
                }
            } elseif (count($rowimages) == 5) {
                if ($m > 1) {
                    $images2[] = tomedia($rowimages[$m]);
                } else {
                    $images1[] = tomedia($rowimages[$m]);
                }
            } elseif (count($rowimages) == 3) {
                if ($m > 0) {
                    $images2[] = tomedia($rowimages[$m]);
                } else {
                    $images1[] = tomedia($rowimages[$m]);
                }
            } else {
                $images1[] = tomedia($rowimages[$m]);
            }
            ($m += 1) + -1;
        }
    }
    $images = array_merge($images1, $images2);
    if (count($rowimages) == 1) {
        $imagewidth = '99%';
        $imageheight = '150';
    } elseif (count($rowimages) == 2) {
        $imagewidth = '49%';
        $imageheight = '150';
    } elseif (count($rowimages) == 3) {
        $imagewidth = '99%';
        $imagewidth1 = '49%';
        $imageheight = '150';
        $imageheight1 = '100';
    } elseif (count($rowimages) == 4) {
        $imagewidth = '49%';
        $imagewidth1 = '49%';
        $imageheight = '100';
        $imageheight1 = '100';
    } elseif (count($rowimages) == 5) {
        $imagewidth = '49%';
        $imagewidth1 = '32%';
        $imageheight = '100';
        $imageheight1 = '100';
    }
    pdo_update('rhinfo_zyxq_sns_post', array('views' => $post['views'] + 1), array('id' => $post['id']));
    $goodcount = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $post['id']));
    $replycount = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=:pid and deleted=0 and checked=1 limit 1', array(':pid' => $post['id']));
    $isgood = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid and openid=:openid  limit 1', array(':weid' => $_W['uniacid'], ':pid' => $post['id'], ':openid' => $_W['openid']));
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_complaincate') . ' where weid=:weid and (rid=:rid or rid=0) order by displayorder';
    $catelist = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $canpost = true;
    $condition .= ' and (rid=:rid or rid=0) and boardid=:bid and postid = :postid and deleted=0 ';
    $params[':rid'] = $rid;
    $params[':bid'] = $bid;
    $params[':postid'] = $postid;
    $isManager = $this->isManager($pid, $rid, $bid, $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        $condition .= ' and checked=1';
    }
    $total = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where ' . $condition, $params);
    include $this->mymtpl('comment');
} elseif ($operation == 'reply') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    $rpostid = intval($_GPC['rpid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    $content = trim($_GPC['content']);
    $len = istrlen($content);
    if (!($len >= 3)) {
        show_json(0, '内容最少3个汉字或字符哦~');
    }
    if (!(500 >= $len)) {
        show_json(0, '内容最多500个汉字或字符哦~');
    }
    $checked = 0;
    if ($ismanager) {
        $checked = $board['needcheckreplymanager'] ? 0 : 1;
    } else {
        $checked = $board['needcheckreply'] ? 0 : 1;
    }
    if ($issupermanager) {
        $checked = 1;
    }
    $time = time();
    $data = array('weid' => $_W['uniacid'], 'pid' => $pid, 'rid' => $rid, 'title' => '回复', 'boardid' => $bid, 'postid' => $postid, 'replypostid' => $rpostid, 'openid' => $_W['openid'], 'avatar' => $user['avatar'], 'nickname' => $user['nickname'], 'createtime' => $time, 'replytime' => $time, 'content' => trim($_GPC['content']), 'images' => is_array($_GPC['images']) ? iserializer($_GPC['images']) : serialize(array()), 'checked' => $checked);
    pdo_insert('rhinfo_zyxq_sns_post', $data);
    pdo_update('rhinfo_zyxq_sns_post', array('replytime' => $time), array('id' => $pid, 'weid' => $_W['uniacid']));
    if ($checked) {
        $content = replaceContent($data['content']);
        $content = mb_substr($content, 0, 15) . '...';
    }
    show_json(1, array('checked' => $checked));
} elseif ($operation == 'delete') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        show_json(0, '无权删除');
    }
    pdo_update('rhinfo_zyxq_sns_post', array('deleted' => 1, 'deletedtime' => time()), array('id' => $postid));
    show_json(1);
} elseif ($operation == 'check') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        show_json(0, '无权审核');
    }
    if (!$post['checked']) {
        pdo_update('rhinfo_zyxq_sns_post', array('checked' => 1, 'checktime' => time()), array('id' => $postid));
    }
    show_json(1);
} elseif ($operation == 'best') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        show_json(0, '无权设置精华');
    }
    $isbest = 1;
    if ($post['isboardbest']) {
        $isbest = 0;
        pdo_update('rhinfo_zyxq_sns_post', array('isboardbest' => 0), array('id' => $postid));
    } else {
        pdo_update('rhinfo_zyxq_sns_post', array('isboardbest' => 1), array('id' => $postid));
    }
    show_json(1, array('isbest' => $isbest));
} elseif ($operation == 'top') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        show_json(0, '无权设置置顶');
    }
    $istop = 1;
    if ($post['isboardtop']) {
        $istop = 0;
        pdo_update('rhinfo_zyxq_sns_post', array('isboardtop' => 0), array('id' => $postid));
    } else {
        pdo_update('rhinfo_zyxq_sns_post', array('isboardtop' => 1), array('id' => $postid));
    }
    show_json(1, array('istop' => $istop));
} elseif ($operation == 'allbest') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager) {
        show_json(0, '无权设置全站精华');
    }
    $isbest = 1;
    if ($post['isbest']) {
        $isbest = 0;
        pdo_update('rhinfo_zyxq_sns_post', array('isbest' => 0), array('id' => $postid));
    } else {
        pdo_update('rhinfo_zyxq_sns_post', array('isbest' => 1), array('id' => $postid));
    }
    show_json(1, array('isbest' => $isbest));
} elseif ($operation == 'alltop') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager) {
        show_json(0, '无权设置全站置顶');
    }
    $istop = 1;
    if ($post['istop']) {
        $istop = 0;
        pdo_update('rhinfo_zyxq_sns_post', array('istop' => 0), array('id' => $postid));
    } else {
        pdo_update('rhinfo_zyxq_sns_post', array('istop' => 1), array('id' => $postid));
    }
    show_json(1, array('istop' => $istop));
} elseif ($operation == 'checkpost') {
    $postid = intval($_GPC['postid']);
    $post = pdo_fetch('select pid,nickname from ' . tablename('rhinfo_zyxq_sns_post') . ' where id = ' . $postid . ' ');
    if (empty($post)) {
        show_json(0, '该话题或评论不存在');
    }
    show_json(1, array('post' => $post));
} elseif ($operation == 'replylist') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $limit = 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $condition .= ' and (rid=:rid or rid=0) and boardid=:bid and postid = :postid and deleted=0 ';
    $params[':rid'] = $rid;
    $params[':bid'] = $bid;
    $params[':postid'] = $postid;
    $isManager = $this->isManager($pid, $rid, $bid, $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        $condition .= ' and checked=1';
    }
    $sql = 'select id,replypostid,title,createtime,content,images ,openid, nickname,avatar,checked from ' . tablename('rhinfo_zyxq_sns_post') . '  where ' . $condition . ' ORDER BY createtime asc ' . $limit;
    $list = pdo_fetchall($sql, $params);
    $k = 0;
    while (!($k >= count($list))) {
        $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $list[$k]['id']));
        $list[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where pid=:pid limit 1', array(':pid' => $list[$k]['id']));
        $images = array();
        $rowimages = iunserializer($list[$k]['images']);
        if (is_array($rowimages) && !empty($rowimages)) {
            $m = 0;
            while (!($m >= count($rowimages))) {
                if (!(count($images) > 2)) {
                    $images[] = tomedia($rowimages[$m]);
                }
                ($m += 1) + -1;
            }
        }
        $list[$k]['images'] = $images;
        $list[$k]['imagewidth'] = '32%';
        $list[$k]['imagecount'] = count($rowimages);
        $list[$k]['content'] = replaceContent($list[$k]['content']);
        $list[$k]['createtime'] = timeBefore($list[$k]['createtime']);
        $list[$k]['parent'] = false;
        if (!empty($list[$k]['replypostid'])) {
            $parentPost = $this->getPost($list[$k]['replypostid']);
            $list[$k]['parent'] = array('nickname' => $parentPost['nickname'], 'content' => replaceContent($parentPost['content']));
        }
        $isgood = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid and openid=:openid limit 1', array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['id'], ':openid' => $_W['openid']));
        $list[$k]['isgood'] = $isgood;
        $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where weid=:weid and pid=:pid  limit 1', array(':weid' => $_W['uniacid'], ':pid' => $list[$k]['id']));
        $list[$k]['floor'] = ($pindex - 1) * $psize + $k + 2;
        $list[$k]['isAuthor'] = $list[$k]['openid'] == $_W['openid'];
        $list[$k]['isManager'] = $this->isManager($pid, $rid, $bid, $list[$k]['openid']);
        ($k += 1) + -1;
    }
    include $this->mymtpl('replyitem');
} elseif ($operation == 'myreply') {
    $curr = 'member';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' p.weid=:weid and p.pid=:pid and p.rid=:rid and p.openid=:openid and (p.postid>0 or p.replypostid>0) and p.deleted=0 ';
        $params[':pid'] = $pid;
        $params[':rid'] = $rid;
        $params[':openid'] = $_W['openid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select p.*,b.logo from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition . ' ORDER BY p.createtime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['createtime'] = date('Y-m-d H:i', $list[$k]['createtime']);
            $list[$k]['logo'] = tomedia($list[$k]['logo']);
            $sql = 'select title from ' . tablename('rhinfo_zyxq_sns_post') . ' where weid=:weid and pid=:pid and rid=:rid and id=:postid';
            $list[$k]['posttitle'] = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid'], ':pid' => $pid, ':rid' => $rid, ':postid' => $list[$k]['postid']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('replys');
} elseif ($operation == 'mypost') {
    $curr = 'member';
    if ($_W['isajax']) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $condition = ' p.weid=:weid and p.pid=:pid and p.rid=:rid and p.openid=:openid and p.postid=0 and p.deleted=0 ';
        $params[':pid'] = $pid;
        $params[':rid'] = $rid;
        $params[':openid'] = $_W['openid'];
        $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        $sql = 'select p.*,b.logo from ' . tablename('rhinfo_zyxq_sns_post') . ' as p left join ' . tablename('rhinfo_zyxq_sns_board') . ' as b on p.boardid=b.id where ' . $condition . ' ORDER BY p.createtime desc ' . $limit;
        $list = pdo_fetchall($sql, $params);
        $k = 0;
        while (!($k >= count($list))) {
            $list[$k]['createtime'] = date('Y-m-d H:i', $list[$k]['createtime']);
            $list[$k]['logo'] = tomedia($list[$k]['logo']);
            $list[$k]['goodcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_like') . ' where pid=:pid limit 1', array(':pid' => $list[$k]['id']));
            $list[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('rhinfo_zyxq_sns_post') . ' where postid=:pid limit 1', array(':pid' => $list[$k]['id']));
            ($k += 1) + -1;
        }
        show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
    }
    include $this->mymtpl('posts');
} elseif ($operation == 'delmycomm') {
    $bid = intval($_GPC['bid']);
    $postid = intval($_GPC['pid']);
    if (empty($bid)) {
        show_json(0, '参数错误');
    }
    $board = $this->getBoard($bid, $pid, $rid);
    if (empty($board)) {
        show_json(0, '未找到版块!');
    }
    $post = $this->getPost($postid);
    if (empty($post)) {
        show_json(0, '未找到话题!');
    }
    $isManager = $this->isManager($pid, $rid, $board['id'], $_W['openid']);
    $isSuperManager = $this->isSuperManager($pid, $rid, $_W['openid']);
    if (!$isManager && !$isSuperManager) {
        show_json(0, '无权删除');
    }
    pdo_update('rhinfo_zyxq_sns_post', array('deleted' => 1, 'deletedtime' => time()), array('id' => $postid));
    show_json(1);
} elseif ($operation == 'releasepost') {
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $regions = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $regions[] = array('id' => 0, 'title' => '系统默认');
    array_multisort(array_column($regions, 'id'), SORT_ASC, $regions);
    $cates = array();
    $boards = array();
    $i = 0;
    while (!($i >= count($regions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid=:weid and (rid=:rid or rid=0) and enabled=1';
        $categorys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $regions[$i]['id']));
        $cates[$regions[$i]['id']] = $categorys;
        $k = 0;
        while (!($k >= count($categorys))) {
            $sql = 'SELECT id,title FROM ' . tablename('rhinfo_zyxq_sns_board') . ' WHERE weid=:weid and weid=:weid and (rid=:rid or rid=0) and status=1 and cid=:cid';
            $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $regions[$i]['id'], ':cid' => $categorys[$k]['id']));
            $boards[$categorys[$k]['id']] = $data;
            ($k += 1) + -1;
        }
        ($i += 1) + -1;
    }
    include $this->mymtpl('releasepost');
} elseif ($operation == 'sreleasepost') {
    include $this->mymtpl('sreleasepost');
} elseif ($operation == 'selectboard') {
    if ($_W['ispost']) {
        $boardid = $_GPC['boardid'];
        $boardarr = array();
        $boardarr = explode('-', $_GPC['boardid']);
        header('Location:' . $this->createMobileurl($mydo, array('op' => 'sreleasepost', 'rid' => $rid, 'boradid' => $boardarr[2])));
        exit(0);
    }
    $sql = 'select id,title from ' . tablename('rhinfo_zyxq_region') . ' where weid=:weid and id=:rid';
    $regions = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $rid));
    $sql = 'select count(*) from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid=:weid and rid=0 and enabled=1';
    $count = pdo_fetchcolumn($sql, array(':weid' => $_W['uniacid']));
    if ($count > 0) {
        $regionsys = array();
        $regionsys[] = array('id' => 0, 'title' => '系统默认');
        $regions = array_merge($regionsys, $regions);
    }
    $cates = array();
    $boards = array();
    $i = 0;
    while (!($i >= count($regions))) {
        $sql = 'select id,title from ' . tablename('rhinfo_zyxq_sns_category') . ' where weid=:weid and (rid=:rid or rid=0) and enabled=1';
        $categorys = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $regions[$i]['id']));
        $cates[$regions[$i]['id']] = $categorys;
        $k = 0;
        while (!($k >= count($categorys))) {
            $sql = 'SELECT id,title FROM ' . tablename('rhinfo_zyxq_sns_board') . ' WHERE weid=:weid and weid=:weid and (rid=:rid or rid=0) and status=1 and cid=:cid';
            $data = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':rid' => $regions[$i]['id'], ':cid' => $categorys[$k]['id']));
            $boards[$categorys[$k]['id']] = $data;
            ($k += 1) + -1;
        }
        ($i += 1) + -1;
    }
    include $this->mymtpl('selectboard');
}
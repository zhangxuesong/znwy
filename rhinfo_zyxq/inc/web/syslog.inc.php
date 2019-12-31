<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
$this->my_check_web();
$mywe = $this->mywe;
$navtitle = '系统日志';
$mydo = 'syslog';
$tablename = 'rhinfo_zyxq_syslog';
$condition = ' weid = :weid';
$params = array(':weid' => $mywe['weid']);
$pindex = max(1, intval($_GPC['page']));
$psize = empty($this->syscfg['pagesize']) ? 10 : $this->syscfg['pagesize'];
$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$rights = $this->myrights(1, 'syslog', 'list');
if ($operation == 'list') {
    $current = '日志记录';
    $myret = 0;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND (title LIKE \'%' . $_GPC['keyword'] . '%\' or content \'%' . $_GPC['keyword'] . '%\')';
    }
    if (!empty($_GPC['dotype'])) {
        $condition .= ' AND do = \'' . $_GPC['dotype'] . '\'';
    }
    $ctime = $_GPC['ctime'];
    if (!empty($ctime)) {
        $starttime = strtotime($ctime['start']);
        $endtime = strtotime($ctime['end'] . ' 23:59:59');
        $condition .= ' and ctime>=' . $starttime . ' and ctime<=' . $endtime;
    } else {
        $starttime = strtotime('now -30days');
        $endtime = TIMESTAMP;
    }
    if (!empty($_W['uid'])) {
        $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t\t `ID` DESC " . $limit;
            $data = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
    } else {
        $condition .= $this->myrcondition();
        $sql = 'SELECT COUNT(*) FROM ' . tablename($tablename) . ' where ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'select * from ' . tablename($tablename) . ' where ' . $condition . " ORDER BY\r\n\t\t\t\t\t\t `ID` DESC " . $limit;
            $data = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
    }
    if ($total > 0) {
        $k = 0;
        while (!($k >= count($data))) {
            if ($data[$k]['do'] == 'error') {
                $data[$k]['do'] = '异常错误';
            } else {
                $sql = 'select b.title from ' . tablename('rhinfo_zyxq_secprg') . ' as a left join ' . tablename('rhinfo_zyxq_secsys') . ' as b on a.sid=b.id where a.do=:do';
                $data[$k]['do'] = pdo_fetchcolumn($sql, array(':do' => $data[$k]['do']));
            }
            ($k += 1) + -1;
        }
    }
    $mydo = $this->mydo();
    include $this->mywtpl('list');
} elseif ($operation == 'delete') {
    $current = '删除日志';
    $id = intval($_GPC['id']);
    $glue = 'AND';
    $result = pdo_delete($tablename, array('id' => $id, 'weid' => $mywe['weid']), 'AND');
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    exit(0);
} elseif ($operation == 'delall') {
    $current = '清除日志';
    $result = pdo_delete($tablename, array('weid' => $mywe['weid']));
    if (!empty($result)) {
        echo 'ok';
    } else {
        echo '删除失败!';
    }
    exit(0);
}
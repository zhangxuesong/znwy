<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Rhinfo_zyxqModule extends WeModule
{
    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        if (!empty($rid)) {
            $replyrule = pdo_fetch('SELECT * FROM ' . tablename('rhinfo_zyxq_replyrule') . ' WHERE weid=:weid and replyid = :replyid ', array(':weid' => $_W['uniacid'], ':replyid' => $rid));
        }
        $regions = pdo_fetchall('SELECT id,title FROM ' . tablename('rhinfo_zyxq_region') . ' WHERE weid=:weid ', array(':weid' => $_W['uniacid']));
        include $this->template('replyrule');
        return null;
    }
    public function fieldsFormValidate($rid = 0)
    {
        return '';
    }
    public function fieldsFormSubmit($rid)
    {
        global $_W;
        global $_GPC;
        $reply_id = intval($_GPC['reply_id']);
        $data = array('replyid' => $rid, 'weid' => $_W['uniacid'], 'rid' => $_GPC['regionid'], 'uid' => $_W['uid'], 'ctime' => TIMESTAMP);
        if (empty($reply_id)) {
            pdo_insert('rhinfo_zyxq_replyrule', $data);
        } else {
            pdo_update('rhinfo_zyxq_replyrule', $data, array('id' => $reply_id));
        }
        return true;
    }
    public function ruleDeleted($rid)
    {
        pdo_delete('rhinfo_zyxq_replyrule', array('replyid' => $rid));
        return null;
    }
    public function settingsDisplay($settings)
    {
        global $_W;
        global $_GPC;
        if (checksubmit()) {
            $dat = array('serviceappid' => $_GPC['serviceappid'], 'serviceappsecret' => $_GPC['serviceappsecret'], 'servicemerchid' => $_GPC['servicemerchid'], 'merchsecret' => $_GPC['merchsecret'], 'qq_lbskey' => $_GPC['qq_lbskey'], 'bd_lbsak' => $_GPC['bd_lbsak'], 'js_appkey' => $_GPC['js_appkey'], 'expressapi' => $_GPC['expressapi'], 'kd_uid' => $_GPC['kd_uid'], 'yt_qq' => $_GPC['yt_qq'], 'yt_appid' => $_GPC['yt_appid'], 'yt_secretid' => $_GPC['yt_secretid'], 'yt_secretkey' => $_GPC['yt_secretkey']);
            if ($this->saveSettings($dat)) {
                message('保存成功', 'refresh');
            }
        }
        include $this->template('setting');
        return null;
    }
    public function welcomeDisplay()
    {
        global $_W;
        $module_bind = pdo_fetch('SELECT * FROM ' . tablename('modules_bindings') . ' WHERE module = :module and entry=:entry and do=:do ', array(':module' => 'rhinfo_zyxq', ':entry' => 'menu', ':do' => 'home'));
        if (!empty($module_bind)) {
            $url = $_W['siteroot'] . 'web/index.php?i=' . $_W['uniacid'] . '&c=site&a=entry&eid=' . $module_bind['eid'] . '&version_id=0';
            header('location:' . $url);
            exit(0);
        }
        return null;
    }
}
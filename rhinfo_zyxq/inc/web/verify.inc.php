<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W;
global $_GPC;
$this->my_check_web();
if ($_W['ispost']) {
    if (!empty($_FILES['upfile']['name'])) {
        $tmp_file = $_FILES['upfile']['tmp_name'];
        $file = $_FILES['upfile']['name'];
        $file_types = explode('.', $file);
        $file_type = $file_types[count($file_types) - 1];
        if (strtolower($file_type) != 'txt') {
            message('文件类型错误', 'refresh', 'error');
        }
        $savePath = IA_ROOT . '/';
        if (!copy($tmp_file, $savePath . $file)) {
            message('上传失败，请重新上传', 'refresh', 'error');
        }
    } else {
        message('上传失败，文件不能为空', 'refresh', 'error');
    }
    $url = $_W['siteroot'] . 'web/index.php?c=home&a=welcome&do=ext&m=rhinfo_zyxq';
    message('上传成功', $url, 'success');
}
include $this->mywtpl('verify');
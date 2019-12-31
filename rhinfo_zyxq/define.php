<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
define('RHINFO_ZYXQ_DEBUG', false);
!defined('RHINFO_ZYXQ_PATH') && define('RHINFO_ZYXQ_PATH', IA_ROOT . '/addons/rhinfo_zyxq/');
!defined('RHINFO_ZYXQ_DATA') && define('RHINFO_ZYXQ_DATA', RHINFO_ZYXQ_PATH . 'data/');
!defined('RHINFO_ZYXQ_VENDOR') && define('RHINFO_ZYXQ_VENDOR', RHINFO_ZYXQ_PATH . 'vendor/');
!defined('RHINFO_ZYXQ_LOCAL') && define('RHINFO_ZYXQ_LOCAL', '../addons/rhinfo_zyxq/');
!defined('RHINFO_ZYXQ_URL') && define('RHINFO_ZYXQ_URL', $_W['siteroot'] . 'addons/rhinfo_zyxq/');
!defined('RHINFO_ZYXQ_STATIC') && define('RHINFO_ZYXQ_STATIC', RHINFO_ZYXQ_URL . 'static/');
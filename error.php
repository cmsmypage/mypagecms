<?php
$abspath = preg_replace('/\\\/', '/', dirname(__FILE__));
if (file_exists($abspath . '/app/configs/config.php')) {
    require $abspath . '/app/configs/config.php';
}
if(!defined('ADMIN_FOLDER')){
    define('ADMIN_FOLDER','admin');
}
session_name('MyPage' .sha1(__SECURITY_SALT__ . __SECURITY_CIPHER_SEED__));
@session_start();
require ABSPATH . '/lib/helper/url-helper.php';
if (!empty($_SESSION ['PF_ERROR'])) {
    unset($_SESSION ['PF_ERROR']);
} else {
    header("Location: " . site_url() . RELATIVE_PATH);
}
?>
<br />
<br />
<br />
<div>
    <center>
        <h1>Hệ thống đang có lỗi xảy ra!</h1>
    </center>
</div>
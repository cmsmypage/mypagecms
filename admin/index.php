<?php

/**
 *
 * @package		MyPage
 * @author		MyPage Team 
 * @copyright	MyPage Team
 * @link		http://www.MyPage.com
 * @since		Version 1.0
 * @filesource
 *
 */
$abspath = preg_replace('/\\\/', '/', dirname(dirname(__FILE__)));
if (!file_exists($abspath . '/app/configs/config.php')) {
    define('PF_VERSION','1.0');
    require $abspath . '/lib/functions.php';
    redirect_to_install($abspath);
}

require $abspath . '/app/configs/config.php';

if(!defined('ADMIN_FOLDER')){
    define('ADMIN_FOLDER','admin');
}

if(!defined('DB_PREFIX')){
    define('DB_PREFIX','vi_');
}
session_name('MyPage' .sha1(__SECURITY_SALT__ . __SECURITY_CIPHER_SEED__));
@session_start();
require ABSPATH . '/lib/helper/url-helper.php';
require ABSPATH . '/lib/error-handler-class.php';
require ABSPATH . '/lib/pf-class.php';
require ABSPATH . '/app/plugins/default/user/class/authentication/auth.php';
require ABSPATH . '/lib/option.php';
require ABSPATH . '/app/plugins/default/user/class/pf-user.php';
require ABSPATH . '/lib/functions.php';
require ABSPATH . '/lib/common/libs/image/simple_image.php';

// require mvc library
require ABSPATH . '/lib/mvc/pf-base-object.php';
require ABSPATH . '/lib/mvc/pf-session-class.php';
require ABSPATH . '/lib/mvc/pf-post-class.php';
require ABSPATH . '/lib/mvc/pf-get-class.php';
require ABSPATH . '/lib/mvc/pf-request-class.php';
require ABSPATH . '/lib/mvc/pf-controller-class.php';
require ABSPATH . '/lib/mvc/pf-shortcode-class.php';
require ABSPATH . '/lib/mvc/pf-widget-class.php';
require ABSPATH . '/lib/mvc/pf-model-class.php';
require ABSPATH . '/lib/mvc/pf-view-class.php';


/**
 * Blocking Blacklist
 */
$blacklist = get_option('ip_blacklist');
$arr = explode("\n",$blacklist);
if(in_array(get_client_ip(),$arr)){
    exit("Thank you for visiting our website but your IP has been banned!");
}
/**
 * Configuration
 */
$setting = Pf::setting();
define('DEFAULT_LOCALE', $setting->get_element_value('general', 'site_language'));
define('HTML_LANGUAGE', $setting->get_element_value('general', 'html_language'));
define('NUM_PER_PAGE', $setting->get_element_value('general', 'items_per_page'));
date_default_timezone_set(get_configuration('time_zone'));
if ($setting->get_element_value('general', 'enable_log') == 1) {
    new Pf_Error_Handler();
}
require ABSPATH . '/lib/paginator-class.php';
require ABSPATH . '/lib/helper/form-helper.php';
require ABSPATH . '/lib/File_Gettext/File/Gettext.php';
require ABSPATH . '/lib/helper/l10n-helper.php';
require ABSPATH . '/lib/plugin-class.php';


if (is_null(Pf::auth()->get_session("user-id"))&&Pf::auth()->check_cookie("id")){
    set_session(Pf::auth()->get_cookie("id"));
}
if (!is_login()) {
    header("Location: " . site_url() . RELATIVE_PATH . '/'.ADMIN_FOLDER.'/user.php?page=login&ref=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
} elseif (is_User()) {
    header("Location: " . site_url() . RELATIVE_PATH . '/user/profile');
}
global $_admin_css;

admin_css('media/assets/bootstrap/css/bootstrap.min.css');
admin_css('media/assets/font-awesome/css/font-awesome.min.css');
admin_css(ADMIN_FOLDER.'/themes/default/assets/admin-lte/css/ionicons.min.css');
admin_css(ADMIN_FOLDER.'/themes/default/assets/admin-lte/css/AdminLTE.css');
admin_css(ADMIN_FOLDER.'/themes/default/assets/css/admin.css');
admin_css('media/assets/bootstrap-table/css/bootstrap-table.css');

global $_admin_js;

admin_js('media/assets/jquery/jquery-1.11.1.min.js');
admin_js('media/assets/bootstrap/js/bootstrap.min.js');
admin_js(ADMIN_FOLDER.'/themes/default/assets/admin-lte/js/jquery-ui-1.10.3.min.js');
admin_js(ADMIN_FOLDER.'/themes/default/assets/admin-lte/js/AdminLTE/app.js');
admin_js(ADMIN_FOLDER.'/themes/default/assets/admin-lte/js/plugins/iCheck/icheck.min.js');
admin_js('media/assets/bootstrap-table/js/bootstrap-table.js');

global $_admin_menu;
global $_admin_toolbar_button;

$_admin_menu = array();
$_admin_toolbar_button = array();

global $_admin_plugin_content;
$_admin_plugin_content = '';

global $locale;

$locale = (!empty($_GET ['lang'])) ? $_GET ['lang'] : DEFAULT_LOCALE;
$_SESSION['lang'] = $locale;

load_admin_plugins(DEFAULT_PLUGIN_PATH);
load_active_plugins();
load_theme_language('admin_theme','','default');
load_includes_language();
$theme = get_option('active_theme');
load_theme_language($theme.'-theme','',$theme);

$charset = get_configuration('charset_html');

global $admin_page;
$admin_page = (isset($_GET ['admin-page'])) ? $_GET ['admin-page'] : 'dashboard';
if($admin_page=='dashboard'){
    $_GET['admin-page'] = $admin_page;
}
global $sub_page;
$sub_page = (isset($_GET ['sub_page'])) ? $_GET ['sub_page'] : '';
$m2 = (isset($_admin_menu [$admin_page])) ? $_admin_menu [$admin_page] : array();
if (!empty($m2)) {
    $_call_back = (isset($m2 ['callback'])) ? $m2 ['callback'] : '';
    if (!empty($m2 ['sub']) && !empty($sub_page) && !empty($m2 ['sub'] [$sub_page])) {
        if (!empty($m2 ['sub'] [$sub_page] ['callback'])) {
            $_call_back = $m2 ['sub'] [$sub_page] ['callback'];
        }
    }

    if (class_exists($m2['plugin_class']) && trim($_call_back) != '') {
        $p_obj = new $m2['plugin_class'];
        $controller_array = explode('_', strtolower($_call_back));
        $method = $_call_back;
        $_call_back = array($p_obj, $method);
        ob_start();
        if (method_exists($p_obj, $method) && is_callable($_call_back, true)) {
            call_user_func($_call_back);
        }
        $reflector = new ReflectionClass($p_obj);
        $plugin_path = dirname($reflector->getFileName());
        $controller_file = implode('-', $controller_array).'-controller.php';
        $controller_class = '';
        foreach ($controller_array as $v){
            $controller_class .= ucfirst($v).'_';
        }
        $controller_class .= 'Controller';
        
        if (is_file($plugin_path.'/admin/controllers/'.$controller_file)){
            require $plugin_path.'/admin/controllers/'.$controller_file;
            $controller_obj = new $controller_class;
            $action = (!empty($_GET[$controller_obj->action]))?$_GET[$controller_obj->action]:'index';
            $_call_back = array($controller_obj,$action);
            if (method_exists($controller_obj, $action) && is_callable($_call_back, true)) {
                call_user_func($_call_back);
            }
        }
        
        $_admin_plugin_content = ob_get_contents();
        ob_end_clean();
        
    } else {
        // Error: Plugin load fails
    }
}

if (is_ajax()) {
    die($_admin_plugin_content);
}

require ABSPATH . '/'.ADMIN_FOLDER.'/themes/default/template.php';

if (true === DEBUG) {
    Pf::database()->show_debug_console();
}


<?php
/**
 *
 * @package		MyPage
 * @author		PhuocPham
 * @copyright   MyPage Team
 * @link		http://www.MyPage.vn
 * @since		Version 1.0
 * @filesource
 *
 */

$abspath = preg_replace('/\\\/', '/', dirname(__FILE__)); 
if (!file_exists($abspath . '/app/configs/config.php')) {
    define('PF_VERSION', '1.0');
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
session_name('MyPage' . sha1(__SECURITY_SALT__ . __SECURITY_CIPHER_SEED__));
@session_start();
require ABSPATH . '/lib/helper/url-helper.php';
require ABSPATH . '/lib/error-handler-class.php';
require ABSPATH . '/lib/pf-class.php';
require ABSPATH . '/lib/option.php';
require ABSPATH . '/lib/functions.php';
require ABSPATH . '/app/plugins/default/user/class/authentication/auth.php';
require ABSPATH . '/app/plugins/default/user/class/pf-user.php';
require ABSPATH . '/lib/plugin-class.php';
require ABSPATH . '/lib/File_Gettext/File/Gettext.php';
require ABSPATH . '/lib/File_Gettext/File/Gettext/MO.php';
require ABSPATH . '/lib/File_Gettext/File/Gettext/PO.php';
require ABSPATH . '/lib/helper/l10n-helper.php';
require ABSPATH . '/lib/helper/form-helper.php';
require_once ABSPATH . '/lib/paginator-class.php';
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

global $pattern_templates;
global $_public_css;
global $_public_js;

if (is_null(Pf::auth()->get_session("user-id")) && Pf::auth()->check_cookie("id")) {
    set_session(Pf::auth()->get_cookie("id"));
}

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
define('DEFAULT_LOCALE', get_configuration('site_language'));
define('HTML_LANGUAGE', $setting->get_element_value('general', 'html_language'));
define('NUM_PER_PAGE', get_configuration('items_per_page'));
date_default_timezone_set(get_configuration('time_zone'));
if (get_configuration('enable_log') == 1) {
    new Pf_Error_Handler();
}
/*
 * lang
 */
global $locale;
$locale = (!empty($_GET ['lang'])) ? $_GET ['lang'] : DEFAULT_LOCALE;
$_SESSION['lang'] = $locale;
load_includes_language();
/*
 * Charset
 * */
$charset = get_configuration('charset_html');
$exception = false;
//Check site offline
if (get_configuration('site_offline') && !(!empty($_POST) && _get('ajax') == '1') && !is_admin()) {
    $url = $_GET['pf_page_url'] = get_page_url_by_id(get_configuration('site_offline_page'));
    $reqUrl = $url;
    $exception = 'pf-default-site-offline';
} else {
    // Get page url.
    $reqUrl = $_SERVER ['REQUEST_URI'];

    if (strpos($reqUrl, '/index.php') !== false) {
        $reqUrl = substr($reqUrl, strlen(RELATIVE_PATH . '/index.php/'), strlen($reqUrl) + 1);
    } else {
        $reqUrl = substr($reqUrl, strlen(RELATIVE_PATH . '/'), strlen($reqUrl));
    }
    if (get_configuration('accessibility') == 'onlymember' && !is_login()) {
        $allow_reg = get_configuration("allow_reg","pf_user");
        $register = ($allow_reg == 1 && preg_match('/\buser\/user-action:register\b/i',$reqUrl));
        $forgot = (preg_match('/\buser\/user-action:forgot\b/i',$reqUrl));
        $reset = (preg_match('/\buser\/user-page:reset\b/i',$reqUrl));
        $activation = (preg_match('/\buser\/user-page:activation\b/i',$reqUrl));
        if(!($register||$forgot||$reset||$activation)){
            $_GET['ref'] = $reqUrl;
            $reqUrl = 'user';
        }
    }
    if (strpos($reqUrl, '?') !== false) {
        $reqUrl = explode('?', $reqUrl);
        $reqUrl = $reqUrl[0];
    }

    $reqUrl = trim($reqUrl);
    if (empty($reqUrl)) {
        // Set default page.
        $page_default = get_page_url_by_id(get_configuration('default_page'));
        if (trim($page_default) != '') {
            $reqUrl = $page_default;
        }
        $exception = 'pf-default-homepage';
    }
    $reqs = explode('/', $reqUrl);
    $reqUrl = '';
    $slash = '';
    $page_flag = true;
    if (count($reqs) > 0) {
        foreach ($reqs as $v) {
            if (trim($v) != '') {
                $rs = explode(':', $v);
                if (count($rs) == 2) {
                    $page_flag = false;
                    $_GET[$rs[0]] = $rs[1];
                    if (!empty($_REQUEST[$rs[0]])) {
                        $_REQUEST[$rs[0]] = $_GET[$rs[0]];
                    }
                }else if (count($rs) == 1 && $page_flag == true){
                    $reqUrl .= $slash.$rs[0];
                    $slash = '/';
                }
            }
        }
    }
    $_GET['pf_page_url'] = $reqUrl;
}

// get page
$db = Pf::database();

$db->select('*', ''.DB_PREFIX.'pages', 'page_url = ? and page_status = 1', array(
    $reqUrl
        ), '', 1);
$rs = $db->fetch_assoc();

if (empty($rs)){
    while (strripos($reqUrl, '/') !== false){
        $reqUrl = substr($reqUrl, 0, strripos($reqUrl, '/'));
        $db->select('*', ''.DB_PREFIX.'pages', 'page_url = ? and page_status = 1', array(
            $reqUrl
        ), '', 1);
        $rs = $db->fetch_assoc();
        if (!empty($rs)){
            break;
        }
    }
}

// Page not found. Get page error
if (empty($rs)) {
    if ($exception !== false) {
        $db->select('*', ''.DB_PREFIX.'pages', 'page_url = ? and page_status = 1', array(
            $exception
                ), '', 1);
        $rs = $db->fetch_assoc();
    }
    if (empty($rs)) {
        $db->select('*', ''.DB_PREFIX.'pages', 'page_url = ? and page_status = 1', array(
            get_page_url_by_id(get_configuration('error_page'))
                ), '', 1);
        $rs = $db->fetch_assoc();
        $exception = 'pf-default-error';
    }
    if (empty($rs)) {
        $db->select('*', ''.DB_PREFIX.'pages', 'page_url = ? and page_status = 1', array(
            $exception
                ), '', 1);
        $rs = $db->fetch_assoc();
    }
}
if (!empty($rs)) {
    // Check visible page
    if (!check_visible_page($rs['page_visible'])) {
        header('Location: ' . public_base_url() . get_page_url_by_id(get_configuration('error_page')));
        exit();
    }
    // Background
    $background_data = json_decode($rs['page_theme_options'], true);

    $background_option = !empty($background_data['background']) && (!empty($background_data['data']['color']) || !empty($background_data['data']['image'])) ? $background_data['background'] : '';
    $wrapper_background = !empty($background_data['wrapper_background']) ? $background_data['wrapper_background'] : '';


    // Set info head (Title, Keyword, Description)
    $head_info = array(
        'title' => get_configuration('site_name') .' - '.(!empty($rs['page_meta_title']) ? $rs['page_meta_title'] : ''),
        'keywords' => !empty($rs['page_meta_keywords']) ? $rs['page_meta_keywords'] : get_configuration('site_meta_keywords'),
        'description' => !empty($rs['page_meta_description']) ? $rs['page_meta_description'] : get_configuration('site_meta_description'),
        'charset' => empty($charset) ? "utf-8" : $charset
    );
    
    set_head_info($head_info);

    load_admin_plugins(DEFAULT_PLUGIN_PATH, 'public_init');
    load_active_plugins('public_init');
    if (is_ajax()) {
        $tags = Pf::shortcode()->get_tags();
        foreach ($tags as $ns => $tag) {
            if (!empty($_GET[$ns . '_code'])) {
                die(call_user_func($tag[$_GET[$ns . '_code']], $_REQUEST, null, $_GET[$ns . '_code']));
            }
        }
    }
    // get layout
    $layout = array();
    $layouts = get_option('layouts');
    foreach ($layouts as $v) {
        if ($v['id'] == $rs['page_layout']) {
            $layout = $v;
            break;
        }
    }
    if (get_configuration('accessibility') == 'onlymember' && !is_login()) {
        $layout = array(
            'layout_name' => '',
            'pattern' => 1,
            'json_data' => '{}',
            'setting_data' => Array
            (
            )
        );
    }
    $widgets = array();
    if (!empty($layout['json_data'])) {
        $widgets = json_decode($layout['json_data'], true);
        $active_widgets = get_option('active_widgets');
    }
    $setting_data = array();
    if (!empty($layout['setting_data'])) {
        $setting_data = $layout['setting_data'];
    }
    // get footer
    $footer = get_option('footer');
    $widgets_footer = array();
    if (!empty($footer['json_data'])) {
        $widgets_footer = json_decode($footer['json_data'], true);
    }
    $setting_footer = array();
    if (!empty($footer['setting_data'])) {
        $setting_footer = $footer['setting_data'];
    }

    // Check layout
    if (!empty($layout)) {
        // Get active theme
        $theme = get_option('active_theme');
        $layout ['layout_type'] = (isset($layout ['layout_type']))?$layout ['layout_type']:'1';
        // Check theme
        if (is_file(ABSPATH . '/app/themes/' . $theme . '/' . $theme . '.php')) {
            load_theme_language($theme . '-theme', '', $theme);
            require ABSPATH . '/app/themes/' . $theme . '/' . $theme . '.php';
            
            if (!empty($rs['page_content'])){
                  $rs['page_content'] = Pf::shortcode()->exec($rs['page_content']);
            }
            $html = null;
            if (is_file(ABSPATH . '/app/themes/' . $theme . '/patterns.php') && $layout['pattern'] == 1) {
                ob_start();
                require ABSPATH . '/app/themes/' . $theme . '/patterns.php';
                $html = ob_get_contents();
                ob_get_clean();
            }else if (is_file(ABSPATH . '/app/themes/' . $theme . '/index.php')) {
                ob_start();
                require ABSPATH . '/app/themes/' . $theme . '/index.php';
                $html = ob_get_contents();
                ob_get_clean();
            } 
            
            if ($html !== null) {
                echo load_css_js($html, $theme);
            }else{
                // Error: Pattern template not found.
            }
        } else {
            // Error: Them not found
        }
    } else {
        // Error: Layout not found
    }
}
if (true === DEBUG) {
    Pf::database()->show_debug_console();
}

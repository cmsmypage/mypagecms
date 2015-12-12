<?php
$abspath = preg_replace('/\\\/', '/', dirname(dirname(__FILE__)));
if (!file_exists($abspath . '/app/configs/config.php')) {
    require $abspath . '/lib/functions.php';
    redirect_to_install($abspath);
}
require $abspath . '/app/configs/config.php';
session_name('MyPage' .sha1(__SECURITY_SALT__ . __SECURITY_CIPHER_SEED__));
@session_start();
require ABSPATH . '/lib/helper/url-helper.php';
require ABSPATH . '/lib/error-handler-class.php';
require ABSPATH . '/lib/pf-class.php';
require ABSPATH . '/app/plugins/default/user/class/authentication/auth.php';
require ABSPATH . '/app/plugins/default/user/class/pf-user.php';
require ABSPATH . '/lib/option.php';
require ABSPATH . '/lib/functions.php';

require ABSPATH . '/lib/paginator-class.php';
require ABSPATH . '/lib/helper/form-helper.php';
require ABSPATH . '/lib/File_Gettext/File/Gettext.php';
require ABSPATH . '/lib/helper/l10n-helper.php';
require ABSPATH . '/lib/plugin-class.php';

/**
 * Blocking Blacklist
 */
$blacklist = get_option('ip_blacklist');
$arr = explode("\n",$blacklist);
if(in_array(get_client_ip(),$arr)){
    exit("Thank you for visiting our website but your IP has been banned!");
}

$sitename   =   Pf::setting()->get_element_value('general','site_name');
if (is_login() == TRUE) {
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        $auth = new Auth();
        $auth->destroy_session();
        
        if($auth->check_cookie('id')){
            $auth->destroy_cookie('id');
        }
        header('location: ?page=login');
    } else {
        header('location: index.php');
    }
} else {
    if (!isset($_GET['page'])) {
        if (!empty($_GET['ref'])) {
            header('location: ?page=login&ref=' . urlencode($_GET['ref']));
        } else
            header('location: ?page=login');
    }
}

global $locale;

define('DEFAULT_LOCALE', get_configuration('site_language'));
$locale = (!empty($_GET ['lang'])) ? $_GET ['lang'] : DEFAULT_LOCALE;
$_SESSION['lang'] = $locale;

load_includes_language();
load_language('user','user');
?>
<html class="bg-blue">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $sitename." | ".__('Login','user'); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="../media/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="../media/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="themes/default/assets/admin-lte/css/AdminLTE.css" rel="stylesheet" type="text/css" />
                <!-- jQuery 2.0.2 -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../media/assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-blue">
<?php
    require ABSPATH . "/app/plugins/default/user/backend/login.php";
?>
    </body>
</html>
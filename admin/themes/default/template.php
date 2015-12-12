<!DOCTYPE html>
<html lang="<?php echo HTML_LANGUAGE;?>">
    <head>
        <meta charset="<?php echo!empty($charset) ? $charset : "utf-8"; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <title><?php echo htmlspecialchars(Pf::setting()->get_element_value('general', 'site_name')); ?></title>

        <!-- CSS -->
        <?php
        global $_admin_css;
        foreach ($_admin_css as $css) {
            $relative_path = RELATIVE_PATH;
            if ($css[1] != '') {
                $relative_path = (strpos($css[1], '/plugins/default/') !== false) ? RELATIVE_DEFAULT_PLUGIN_PATH : RELATIVE_PLUGIN_PATH;
            }
            echo '<link href="' . $relative_path . '/' . preg_replace('/\\\/', '/', $css[0]) . '" rel="stylesheet">' . " \n\t";
        }
        ?>
        <!-- Javascript -->
        <?php
        global $_admin_js;
        $value = '';

        foreach ($_admin_js as $js) {
            $relative_path = RELATIVE_PATH;
            if ($js[1] != '') {
                $relative_path = (strpos($js[1], '/plugins/default/') !== false) ? RELATIVE_DEFAULT_PLUGIN_PATH : RELATIVE_PLUGIN_PATH;
            }
            echo '<script src="' . $relative_path . '/' . preg_replace('/\\\/', '/', $js[0]) . '"></script>' . " \n\t";
        }
        ?>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
    <script src="themes/default/assets/js/html5shiv.js"></script>
    <script src="themes/default/assets/js/respond.min.js"></script>
    <![endif]-->
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?php echo admin_url('act&admin-page&', false); ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                MyPage
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only"><?php echo __('Hide admin menu', 'admin_theme'); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-left">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="http://demo-cms.MyPage.com/document/cms/" title="<?php echo __('Documentation', 'admin_theme'); ?>" target="_blank" class="icon-front-page">
                                                                <i class="fa fa-book fa-2x"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo site_url () . RELATIVE_PATH; ?>" title="<?php echo __('Home Page', 'admin_theme'); ?>" target="_blank" class="icon-front-page">
                                                                <i class="fa fa-home fa-2x"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" data-toggle="dropdown" class="col-sm-12">
                                <div class="col-sm-4">
                                    <div class="img-circle header-div">
                                        <?php echo user_avatar(current_user('user-id'), '', 'header-img'); ?></div>
                                </div>
                                <div class="col-sm-8">
                                    <center><?php echo __('Welcome', 'admin_theme'); ?>,<br/> <?php echo current_user('user-name'); ?><span><i class="caret"></i></span></center>


                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <div class="img-circle avatar-div"><?php echo user_avatar(current_user('user-id'), '', 'avatar-img'); ?></div>
                                    <p>
                                        <?php echo current_user('user-name'); ?>
                                        <small><?php echo current_user('user-firstname') . ' ' . current_user('user-lastname'); ?></small>
                                    </p>
                                </li>                                
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?php echo admin_url('admin-page=user&sub_page=user&action=change_profile', false); ?>" class="btn btn-default btn-flat"><?php echo __('Profile', 'admin_theme'); ?></a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo site_url() . RELATIVE_PATH . '/' . ADMIN_FOLDER . '/user.php?action=logout' ?>" class="btn btn-default btn-flat"><?php echo __('Sign out', 'admin_theme'); ?></a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <aside class="left-side sidebar-offcanvas" id="sidebar-menu">
                <?php require dirname(__FILE__).'/sidebar-menu.php';?>
            </aside>
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <div style="height:40px;">
                    <div data-spy="affix" data-offset-top="50" id="header-toolbar">
                        <section class="content-header">
                            <h1>
                                &nbsp;
                                <div style="position:absolute; top:10px; right:15px;">
                                    <?php global $_admin_toolbar_button; ?>
                                    <?php foreach ($_admin_toolbar_button as $button) { ?>
                                        <?php echo $button; ?>
                                    <?php } ?>
                                </div>
                            </h1>
                        </section>
                    </div>
                </div>
                <!-- Main content -->
                <section class="content" id="main-content">
                    <?php
                    global $_admin_plugin_content;
                    echo $_admin_plugin_content;
                    ?>
                </section><!-- /.content -->
                <!-- Ticket #494 -->
                <div class="footer" style="background:#FFFFFF;">
                    <div class="inner">
                        Copyright &copy; <?php echo date("Y"); ?> <strong>MyPage CMS</strong> Version <?php echo PF_VERSION; ?>
                    </div>
                </div>
                <!-- end Ticket #494-->
            </aside><!-- /.right-side -->

        </div><!-- ./wrapper -->

        <script>
            $(document).ready(function() {
<?php if (is_admin()) { ?>
                    $(".left-side .sidebar-menu").sortable({
                        handle: ".item-handler",
                        update: function(event, ui) {
                            var menus = [];
                            $(".left-side .sidebar-menu >li").each(function() {
                                menus[menus.length] = $(this).attr('id');
                            });
                            $.post('<?php echo admin_url(array('admin-page' => 'configuration', 'sub_page' => 'settings', 'action' => 'admin_menu_order'), false); ?>', {'menus': menus}, function() {

                            }, 'html');
                        }
                    });
<?php } ?>
                $('#header-toolbar').on('affix.bs.affix', function() {
                    $(this).width($('.right-side').width());
                });
                $('#header-toolbar').on('affix-top.bs.affix', function() {
                    $(this).width($('.right-side').width());
                });

                $(window).on('resize', function() {
                    $('#header-toolbar').width($('.right-side').width());
                });

                $('.navbar-btn').click(function() {
                    $('#header-toolbar').width($('.right-side').width());
                });
            });
        </script>
        <style type="text/css">
            .header-img{
                max-width: 60px;
                font-size: 50px
            }
            .header-div{
                overflow: hidden;
                width: 40px;
                height: 40px;
                align-content: center;
                margin: auto;
            }
            @media (max-width: 766px){
                .header-div{
                    display: none;
                }
            }
            .navbar-nav > li > a {
                padding-top: 0px;
                padding-bottom: 0px;
            }
            .navbar-nav > li > a {
                padding-top: 5px;
                padding-bottom: 5px;
                line-height: 20px;
            }
            .avatar-div {
                overflow: hidden;
                width: 90px;
                height: 90px;
                align-content: center;
                margin: auto;
            }
            .avatar-img {
                width: auto;
                font-size: 100px;
                max-width: 140px;
            }
            .skin-blue .sidebar > .sidebar-menu > li.active_tmp > a {
                color: #222;
                background: #f9f9f9;
            }
            .sidebar-menu>li.active_tmp:after {
                -moz-border-bottom-colors: none;
                -moz-border-left-colors: none;
                -moz-border-right-colors: none;
                -moz-border-top-colors: none;
                /*border-color: #3C8DBC;*/
                border-color: #CCCCCC;
                border-image: none;
                border-style: solid;
                border-width: 0 2px 0 0;
                bottom: 0;
                content: "";
                display: inline-block;
                position: absolute;
                right: -2px;
                top: -1px;
                z-index: 1;
            }
        </style>
    </body>
</html>
                            
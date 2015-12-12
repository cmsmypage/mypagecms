<?php 
global $admin_page;
global $sub_page;
global $_admin_menu;

$menu_settings = get_option('admin_menu_setting');
if (!is_array($menu_settings)){
    $menu_settings = array();
}

$admin_menu_order = array();
foreach ($menu_settings as $k => $v){
    $admin_menu_order[] = $k;
}

if (!empty($admin_menu_order) && is_array($admin_menu_order)) {
    $new_menu = array();
    foreach ($admin_menu_order as $k) {
        if (!empty($_admin_menu[$k])) {
            $new_menu[$k] = $_admin_menu[$k];
            unset($_admin_menu[$k]);
        }
    }
    if (!empty($_admin_menu)) {
        foreach ($_admin_menu as $k => $v) {
            $new_menu[$k] = $v;
        }
    }
    $_admin_menu = $new_menu;
}

$str_lang = '';
if (!empty($_REQUEST['lang'])) {
    $str_lang = '&lang=' . $_REQUEST['lang'];
}
?>
<section class="sidebar">                    
    <h3 style="text-shadow: -1px -1px #666, 1px 1px #FFF; color:#CCCCCC; margin-top:13px; margin-bottom:13px; text-align:center;">
        <?php echo __('Admin Menu', 'admin_theme'); ?>
    </h3>

    <ul class="sidebar-menu">
        <?php
        foreach ($_admin_menu as $k => $m) {
            if (!key_exists($k, $menu_settings)){
                $icon = (!empty($m['icon_class'])) ? '<i class="' . $m['icon_class'] . '"></i>' : '';
            }else{
                if ($menu_settings[$k]['visibility'] == 'hide') continue;
                $icon = '<i class="' . $menu_settings[$k]['icon'] . '" style="color:'.$menu_settings[$k]['icon_color'].'"></i>';
            }
            $mnu_class = "";
            if (!empty($m['sub'])) $mnu_class .= ' treeview';

            if ($admin_page == $k) $mnu_class .= ' active';
        ?>
            <li <?php if (!empty($mnu_class)) echo 'class="' . $mnu_class . '"'; ?> id="<?php echo $k; ?>">
                <a href="<?php echo admin_url('admin-page=' . $k . $str_lang, false); ?>">
                    <span><?php echo $icon; ?> <?php echo $m['name'] ?></span>
                    <?php if (!empty($m['sub'])) { ?><i class="fa fa-angle-left pull-right"></i><?php } ?>
                </a>
                <?php if (!empty($m['sub'])) { ?>
                    <ul class="treeview-menu"<?php if ($admin_page == $k) { ?> style="display: block;"<?php } ?>>
                        <?php
                        foreach ($m['sub'] as $k1 => $m1) {
                            $icon = (!empty($m1['icon_class'])) ? '<i class="' . $m1['icon_class'] . '"></i>' : '';
                            ?>
                            <li <?php if ($admin_page == $k && $sub_page == $k1) { ?> class="active" <?php } ?>>
                                <a href="<?php echo admin_url('admin-page=' . $k . '&sub_page=' . $k1 . $str_lang, false); ?>"><?php echo $icon; ?> <?php echo $m1['name'] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <div id="sidebar-menu-more" class="more-menu"> More <i class="fa fa-caret-down"></i></div>
    <ul class="sidebar-menu" id="sidebar-hidden-menu" style="display:none;">
        <?php
        foreach ($_admin_menu as $k => $m) {
            if (empty($menu_settings[$k]) || empty($menu_settings[$k]['visibility']) || $menu_settings[$k]['visibility'] == 'show') continue;
            $icon = '<i class="' . $menu_settings[$k]['icon'] . '" style="color:'.$menu_settings[$k]['icon_color'].'"></i>';
            $mnu_class = "";
            if (!empty($m['sub'])) $mnu_class .= ' treeview';

            if ($admin_page == $k) $mnu_class .= ' active';
        ?>
            <li <?php if (!empty($mnu_class)) echo 'class="' . $mnu_class . '"'; ?> id="<?php echo $k; ?>">
                <a href="<?php echo admin_url('admin-page=' . $k . $str_lang, false); ?>">
                    <span><?php echo $icon; ?> <?php echo $m['name'] ?></span>
                    <?php if (!empty($m['sub'])) { ?><i class="fa fa-angle-left pull-right"></i><?php } ?>
                </a>
                <?php if (!empty($m['sub'])) { ?>
                    <ul class="treeview-menu"<?php if ($admin_page == $k) { ?> style="display: block;"<?php } ?>>
                        <?php
                        foreach ($m['sub'] as $k1 => $m1) {
                            $icon = (!empty($m1['icon_class'])) ? '<i class="' . $m1['icon_class'] . '"></i>' : '';
                            ?>
                            <li <?php if ($admin_page == $k && $sub_page == $k1) { ?> class="active" <?php } ?>>
                                <a href="<?php echo admin_url('admin-page=' . $k . '&sub_page=' . $k1 . $str_lang, false); ?>"><?php echo $icon; ?> <?php echo $m1['name'] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</section>
<script>
if ($('#sidebar-hidden-menu li').length > 0){
	$('#sidebar-menu-more').show();
}else{
	$('#sidebar-menu-more').hide();
}

$('#sidebar-menu-more').click(function(){
	var _this = $(this);
	if (_this.hasClass('more-menu')){
		_this.attr('class','less-menu')
		_this.html('Less <i class="fa fa-caret-up"></i>');
	}else{
		_this.attr('class','more-menu')
		_this.html('More <i class="fa fa-caret-down"></i>');
	}
	$('#sidebar-hidden-menu').toggle("slow",function(){});
});
</script>
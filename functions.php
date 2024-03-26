<?php
add_action('wp_enqueue_scripts', 'socialv_enqueue_styles', 99);



function socialv_enqueue_styles()
{
    $css_path='/include/css';
    $js_path='/include/js';

    $parent_style = 'parent-style'; // This is 'socialv-style' for the socialv theme.
    wp_enqueue_style('parent-style', get_stylesheet_directory_uri() . '/style.css'); 
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
    wp_enqueue_script('summ-general-js', get_stylesheet_directory_uri() .$js_path.'/summ-general.js', true);
    wp_enqueue_script('summ-achievement-description-js', get_stylesheet_directory_uri() .$js_path.'/summ-achievement-description.js', true);

    wp_enqueue_script('delete-activity', get_stylesheet_directory_uri() .$js_path.'/tree-love-back-admin-panel-delete-activity.js', true);
    wp_localize_script('delete-activity', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));  
}

function admin_enqueue_styles()
{   
    $css_path='/include/css';
    $js_path='/include/js';

    wp_enqueue_style('custom-admin-panel', get_stylesheet_directory_uri() . $css_path.'/tree-love-back-admin-panel.css');
    wp_enqueue_script('delete-activity', get_stylesheet_directory_uri() .$js_path.'/tree-love-back-admin-panel-delete-activity.js', true);
    wp_localize_script('delete-activity', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    

}
add_action('admin_enqueue_scripts', 'admin_enqueue_styles', 99);


function socialv_child_theme_setup()
{
    load_child_theme_textdomain('socialv', get_stylesheet_directory() . '/languages');
}




$php_path='/include/php';
/* general mechanism start */
$general_dir=$php_path.'/general';
require_once get_stylesheet_directory() . $general_dir.'/summ-unique-cookie.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-code.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-code-backup.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-excel-import.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-member-mechanism.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-group-mechanism.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-delete-mechanism.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-db-functions.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-image-functions.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-achivement-addtional-setting.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-media-library-render.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-activity-render.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-activity-filter.php';
//require_once get_stylesheet_directory() . $general_dir.'/bubbypress_features.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-member-rule.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-member-account-setting.php';
require_once get_stylesheet_directory() . $general_dir.'/summ-sms-function.php';

/* general mechanism end */


/* client admin panel start */
$client_admin_panel_dir=$php_path.'/client-admin-panel';
require_once get_stylesheet_directory() . $client_admin_panel_dir.'/summ-tree-admin-panel.php';
require_once get_stylesheet_directory() . $client_admin_panel_dir.'/summ-clinet-admin-panel-general.php';
/* client admin panel end */



/* achivement award start */
$award_logic_dir=$php_path.'/award-logic';
require_once get_stylesheet_directory() . $award_logic_dir.'/summ-foodbank-award.php';
require_once get_stylesheet_directory() . $award_logic_dir.'/summ-taifu-award.php';
require_once get_stylesheet_directory() . $award_logic_dir.'/summ-tree-love-award.php';
require_once get_stylesheet_directory() . $award_logic_dir.'/summ-award-mechanism.php';
/* achivement award end */




/* login redirect start */
$phone_login_dir=$php_path.'/phone-login-page';
require_once get_stylesheet_directory() . $phone_login_dir .'/summ-taitra-special-activity-php';
require_once get_stylesheet_directory() . $phone_login_dir .'/summ-guppy-charity-sustainability-project.php';
require_once get_stylesheet_directory() . $phone_login_dir .'/summ-taifu-login.php';
require_once get_stylesheet_directory() . $phone_login_dir .'/summ-phone-login-and-awawd.php';
require_once get_stylesheet_directory() . $phone_login_dir .'/summ-tree-love-login.php';
/* login redirect end */


/* filter bar */
function show_group_sub_menu()
{
    $group_id = bp_get_current_group_id(); 
    if (!$group_id ) {
        return ;
    }
    $custom_css = "
   #item-nav,#subnav{
	display: block; 
}";

    // 在 HTML 中输出 CSS 样式
    echo "<style type='text/css'>$custom_css</style>";
}

add_action('wp_footer', 'show_group_sub_menu');


// maintainance
function custom_maintenance_mode() {
    if (!current_user_can('activate_plugins')) {
        wp_die('<h1>網站維護中</h1><p>我們正在進行網站維護，請稍後再試。</p>', '網站維護中');
    }
}

//add_action('get_header', 'custom_maintenance_mode');




?>
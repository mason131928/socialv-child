<?php
/**
 * SocialvChild Sum Member machanism
 *
 * @package YourPackage
 * @author Lucas
 */

/**
 * Function for post_form_hello action.
 */


//Catch requests to the admin home page
function redirect_on_home()
{

	if (!current_user_can('subscriber')) {
		return;
	}
	if (!is_admin()) {
		return;
	}
	remove_menu_page('index.php');
	remove_menu_page('profile.php');

    $admin_access=check_group_admin();
	if (!$admin_access){
	wp_redirect(home_url());
    exit;
	}
	update_user_meta(get_current_user_id(), 'admin_color', 'light');

  $currentURL = home_url(sanitize_url($_SERVER['REQUEST_URI']));
  $adminURL = get_admin_url();
  //Only redirect if we are on empty /wp-admin/
  if ($currentURL != $adminURL) {
    return;
  }
//   $userRedirect = get_edit_profile_url(get_current_user_id());
//   $url = $userRedirect;


$url='admin.php?page=tree-dashboard';
  wp_safe_redirect($url);
  exit();
}
add_filter('admin_menu', 'redirect_on_home', 10);

function check_group_admin() {



	$arg=array(
        //'group_id'            => bp_get_current_group_id(),
		'group_id'            => 12,
        'per_page'            => false,
        'page'                => false,
        'exclude_admins_mods' => true,
        'exclude_banned'      => true,
        'exclude'             => false,
        'group_role'          => array('admin' ),
        'search_terms'        => false,
        'type'                => 'last_joined',
	);
	$user_array=groups_get_group_members($arg);
	$current_user_id = get_current_user_id();

	if ($current_user_id && is_array($user_array['members'])) {
		foreach ($user_array['members'] as $user) {
			if ($user->ID == $current_user_id) {
				return 1;
				break; 
			}
		}
	}
	return 0;
}



add_action( 'admin_menu', 'summost_add_menu' );

function summost_add_menu() {

	if (current_user_can('administrator')) {
		return;
	}


	is_subscriber();
	if (!check_group_admin()){
	wp_redirect(home_url());
    exit;
	}

	add_menu_page( 'Summost', '小樹傳愛', 'read', 'tree-dashboard', 'render_tree_dashboard' );
	add_menu_page( 'Summost', '登出', 'read', 'logout', 'logout' );

	do_action( 'adimin_summost_menu')	;

}


	function example_admin_bar_remove_logo() {
		is_subscriber();
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'wp-logo' );
		$wp_admin_bar->remove_menu( 'my-account' );
		$wp_admin_bar->remove_menu( 'bp-notifications' );

	}
	add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );
	



	function is_subscriber() {
	if (!current_user_can('subscriber')) {
		return;
	}
	if (!is_admin()) {
		return;
	}
	}


		function logout() {
			echo '<script>location.href = "' . wp_logout_url() . '"; </script>';

		}
	
	

		function custom_login_logo() {
			echo '<style type="text/css">
					.login h1 a {
					background-image: url(' . get_site_url(). '/wp-content/uploads/2023/11/cropped-cropped-%E5%A5%BD%E4%BA%8B%E9%81%93logo-%E6%96%B0-e1698832099553.png) !important;
					width: 300px !important; /* 根据您的实际需求调整宽度 */
					height: 100px !important; /* 根据您的实际需求调整高度 */
					background-size: contain !important;
				}
			</style>';
		}
		
		add_action('login_head', 'custom_login_logo');
		function custom_login_logo_url() {
			return home_url(); // 将这里的 home_url() 替换为您希望的自定义链接
		}
		
		add_filter('login_headerurl', 'custom_login_logo_url');

		function render_tree_dashboard() {

			echo '<h1 style="padding:20px 0px 20px 0px;">小樹傳愛</h1>';

			global $wpdb, $ct_table;

			$all_achievement = $wpdb->get_results("SELECT DISTINCT achievement_id FROM summ_gamipress_log_extra_data");
			$searchQuery = "SELECT COUNT(*) FROM summ_gamipress_log_extra_data";
			$whereQuery=get_achievement_where_sql_query("treelove-badge");

			
			// 如果有符合条件的 achievement_id，将其添加到 SQL 查询中
			if (!empty($whereQuery)) {
				$searchQuery .= " WHERE $whereQuery";
			}
			
			// $searchQuery 现在包含了构建好的 SQL 查询
			


			$badge_count = $wpdb->get_var("$searchQuery");
			
			//error_log( print_r("SELECT COUNT(*) FROM summ_gamipress_log_extra_data WHERE 1=1 $whereQuery"), true) ;

			echo '<div style="padding:20px 0px 20px 0px;">';


			$badge_url = add_query_arg(array('data-page' => 1), admin_url('admin.php?page=tree-dashboard'));
			echo '<a href="' . esc_url($badge_url) . '">' . '所有活動' . '(' . $badge_count . ')</a> ';


			// 顯示各個徽章的超連結和數量
			foreach ($all_achievement as $badge) {
				$badge_id = $badge->achievement_id;
				$post_type = get_post_type($badge_id);
				if($post_type!=='treelove-badge'){
					continue;
				}
				$badge_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM summ_gamipress_log_extra_data WHERE achievement_id = %d", $badge_id));
				
				// 使用 admin_url 生成管理頁面的 URL
				$badge_url = add_query_arg(array('badge_id' => $badge_id, 'data-page' => 1), admin_url('admin.php?page=tree-dashboard'));
				echo '<a href="' . esc_url($badge_url) . '">' . get_the_title($badge_id) . '(' . $badge_count . ')</a> ';
			}
			echo '</div>';



			if (isset($_GET['badge_id'])) {
				$selected_badge_id = intval($_GET['badge_id']);
				$table_output = do_shortcode('[custom_table badge_id="' . $selected_badge_id . '"]');
				echo $table_output;
			} else {
				// 如果沒有指定徽章，默認顯示所有徽章的內容
				$table_output = do_shortcode('[custom_table]');
				echo $table_output;
			}

		}
	

		function custom_table_shortcode($atts) {
			global $wpdb;
		

			$whereQuery=get_achievement_where_sql_query("treelove-badge");

			// 設定每頁顯示的項目數
			$items_per_page = 50;
			
			// 從 URL 中獲取當前頁碼
			$current_page = isset($_GET['data-page']) ? absint($_GET['data-page']) : 1;

			// 計算分頁的偏移量
			$offset = ($current_page - 1) * $items_per_page;
			// 查詢從 summ_gamipress_log_extra_data 表中獲取數據
		    if (isset($atts['badge_id'])) {
        $badge_id = intval($atts['badge_id']);
        $query = $wpdb->prepare("SELECT achievement_id, user_id, pictures, activity_id FROM summ_gamipress_log_extra_data WHERE achievement_id = %d  LIMIT %d, %d", $badge_id, $offset, $items_per_page);
    } else {
        // 否則顯示所有內容
        $query = $wpdb->prepare("SELECT achievement_id, user_id, pictures, activity_id FROM summ_gamipress_log_extra_data WHERE $whereQuery LIMIT %d, %d", $offset, $items_per_page);
    }
			$table_data = $wpdb->get_results($query, ARRAY_A);
		
			if (empty($table_data)) {
				return '未找到數據'; // 如果未找到數據，可以自定義此消息
			}
		
			// 使用 HTML 表格標記構建表格
			ob_start();
		
			echo '<table id="user-picutre-list">';
			echo '<tr>';
			foreach (array_keys($table_data[0]) as $column) {
				switch ($column) {
					case "phone_number":
						$column_output = '電話號碼';
						break;
			
					case "user_id":
						$column_output = '使用者暱稱';
						break;
			
					case "pictures":
						$column_output = '活動花絮';
						break;

					case "activity_id":
						$column_output = '刪除';
						break;
				case "achievement_id":
							$column_output = '活動名稱';
							break;
					default:
						$column_output = $column;
						break;
				}
				echo '<th>' . esc_html($column_output) . '</th>';
			}
			echo '</tr>';
			
			foreach ($table_data as $row) {
				echo '<tr>';
				$achievement_id='';
				$media_id='';

				foreach ($row as $column => $cell) {
					if ($column === "achievement_id") {
						$achievement_id=$cell;
						
				echo '<td>' . get_the_title($cell) . '</td>';
					}elseif ($column === "pictures") {
						if(null !== $cell){
							$media_id=$cell;
							$cell=wp_get_attachment_url($cell);
							echo '<td><a href="' . esc_url($cell) . '" target="_blank"><img src="' . esc_url($cell) . '" alt="活動花絮"></a></td>';
						}else{
							echo'<td>'. summ_get_feature_image_by_achievement_id($achievement_id).'</td>';

						}

					} elseif ($column === "user_id") {
						$user = get_user_by('ID', $cell);
						echo'<td>'. $user->user_login.'</td>';
					} elseif ($column === "activity_id") {
						
						echo '<td><a href="" data-media-id="' . $media_id . '" class="deleteLogLink">刪除</a></td>';
					}else {
						echo '<td>' . esc_html($cell) . '</td>';
					}
				}
				echo '</tr>';
			}
			
			echo '</table>';
			
		
			// 分頁鏈接
			$total_items = $wpdb->get_var("SELECT COUNT(*) FROM summ_gamipress_log_extra_data  WHERE $whereQuery");

		    if (isset($atts['badge_id'])) {
				$total_items = $wpdb->get_var("SELECT COUNT(*) FROM summ_gamipress_log_extra_data WHERE achievement_id = '" . esc_sql($atts['badge_id']) . "'");
			}
			$total_pages = ceil($total_items / $items_per_page);
		
			echo paginate_links(array(
				'base' => add_query_arg('data-page', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo; 上一頁'),
				'next_text' => __('下一頁 &raquo;'),
				'total' => $total_pages,
				'current' => $current_page,
			));
		
			$output = ob_get_clean(); // 結束緩存並返回輸出
		
			return $output;
		}
		add_shortcode('custom_table', 'custom_table_shortcode');
		
		


		function delete_log_callback() {
			// 获取传递的活动ID
			$media_id = $_POST['media_id'];

			// 删除活动
			mpp_delete_media($media_id);
				echo 'success';

		
			// 终止 AJAX 请求
			wp_die();
		}
		add_action('wp_ajax_backend_delete_log', 'delete_log_callback');



function admin_menu($content)
{


    $admin_access=check_group_admin();
	
	if (!$admin_access){
		return $content;
		}

        $content .= '<li class="menu-item">
        <a href="' . esc_url('/wp-admin/') . '">
        <i class="iconly-Profile icli admin-panel"></i>
            <span class="menu-title">' . esc_html__('後台管理', 'socialv') . '</span>
        </a>
    </li>';
    

    return $content;

}
 add_filter('socialv_user_menu_filter_content_data', 'admin_menu', 1);

function get_achievement_where_sql_query($achievement_title=""){
	global $wpdb;

	$all_achievement = $wpdb->get_results("SELECT DISTINCT achievement_id FROM summ_gamipress_log_extra_data");
	$searchQuery = "SELECT COUNT(*) FROM summ_gamipress_log_extra_data";
	$whereQuery = "";
	
	foreach ($all_achievement as $achievement) {
		$achievement_id = $achievement->achievement_id;
		$post_type = get_post_type($achievement_id);
	
		if ($post_type === $achievement_title) {
			$whereQuery .= " OR achievement_id = $achievement_id";
		}
	}
	
	// 在循环结束后，去掉前导的 " OR "
	$whereQuery = ltrim($whereQuery, ' OR ');
return $whereQuery;
}
<?php

   
// 將summ的 徽章編號  where sql
function add_activity_where_condition($where_conditions){
    $filter_id='';
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    
        $url_parts = parse_url($referer);
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $query_params);
            if (isset($query_params['achieved_id'])) {
                $achieved_id = $query_params['achieved_id'];
                $filter_id=$achieved_id;
            }
        } 
            
        if(isset($_GET['achieved_id'])){
           $filter_id=$_GET['achieved_id'];
        }
        // echo 'filter_id'. $filter_id.'<br />';
         //echo 'bp_get_activity_id'. bp_get_activity_id().'<br />';
         //echo 'summ_gamipress_log_extra_data_activity_id_with_achievement_id'. summ_gamipress_log_extra_data_activity_id_with_achievement_id(bp_get_activity_id()).'<br />';
    
        if(!empty($filter_id)){
            $where_conditions['filter_sql'].=" AND b.achievement_id = '$filter_id'";
        }
      

		// error_log( print_r('$add_activity_where_condition', true) );
        // error_log( print_r($where_conditions, true) );

        return $where_conditions;
}

add_filter('bp_activity_get_where_conditions','add_activity_where_condition');



// 將summ的 資料表加入 join sql
function add_activity_join_condition($join_sql){
  
    $join_sql .= 'INNER JOIN summ_gamipress_log_extra_data b ON a.id = b.activity_id';

            return $join_sql;
    }

add_filter('bp_activity_get_join_sql','add_activity_join_condition');

function show_member_achievements_filter(){

    $user_id = bp_displayed_user_id(); // 或者使用你想查詢的使用者ID
    if ( $user_id==0){
        return ;
    }

    // 獲取使用者的所有成就
    $args = array(
        'user_id'          => $user_id,     					// The given user's ID
        'site_id'          => get_current_blog_id(), 	// The given site's ID
        'achievement_id'   => false, 					// A specific achievement's post ID
        'achievement_type' => false, 					// A specific achievement type
        'since'            => 0,     					// A specific timestamp to use in place of $limit_in_days
        'limit'            => -1,    					// Limit of achievements to return
        'groupby'          => false,    				// Group by clause, setting it to 'post_id' or 'achievement_id' will prevent duplicated achievements
    );


    $user_achievements = gamipress_get_user_achievements($args);
    $achieved_ids = array(); // 用來存放已經獲得的成就編號
    
    if (!empty($user_achievements)) {
        foreach ($user_achievements as $achievement) {
            // 這裡假設 stdClass 對象中包含 ID 屬性
            $achieved_ids[] = $achievement->ID;
        }
    }
    
    // 移除陣列中的重複值
    $unique_achieved_ids = array_unique($achieved_ids);
    $output=render_achievements_filter($unique_achieved_ids);
    echo $output;

}

function show_group_achievements_filter($group_id){
    if(empty($group_id)){
    return;
}


    $group_id = intval(  $group_id );
	$summ_achievement_slug_to_group =  groups_get_groupmeta( $group_id, 'summ_achievement_slug_to_group' );

    if(empty($summ_achievement_slug_to_group)){
        return;
    }
        $output='';
        // 找到post_type为'achievement-type'的所有帖子
        $achievement_type_posts = new WP_Query(
            array(
            'post_type' => 'achievement-type',
            'posts_per_page' => -1,
            )
        );
        
        // 创建一个数组来存储post_name
        $achievement_type_names = array();
        
        if ($achievement_type_posts->have_posts()) {
            while ($achievement_type_posts->have_posts()) {
                $achievement_type_posts->the_post();
                $achievement_type_names[] = get_post_field('post_name');
            }
            wp_reset_postdata(); // 重置查�������
        }

        // 创建<select>元素
        $achieved_ids = array(); // 用來存放團體所有的成就編號
        
        // ���历$achievement_type_names数组
        foreach ($achievement_type_names as $post_name) {
            // 找到post_type为$post_name的所������帖子
        
            //手動限定某類別的徽章
            if($post_name!=$summ_achievement_slug_to_group) {
                continue;
            }
        
        
            $related_posts = new WP_Query(
                array(
                'post_type' => $post_name,
                'posts_per_page' => -1,
                )
            );
        
            if ($related_posts->have_posts()) {
                while ($related_posts->have_posts()) {
                    
                    $related_posts->the_post();  
                    if('publish'!==get_post_field('post_status')){
                        continue;
                    }
                    // ���出<option>���素
                    $achieved_ids[] = get_the_ID();
                }
                wp_reset_postdata(); // 重置查询
            }

        }
        
        $unique_achieved_ids = array_unique($achieved_ids);
        $output=render_achievements_filter($unique_achieved_ids);
          echo  $output;
    }
function render_achievements_filter($unique_achieved_ids){
    $output='';

    // 輸出獲得過的成就編號
    if (!empty($unique_achieved_ids)) {
        $output= '';
        //$output.= '依照徽章進行篩選：';
        $output.= '<form id="achievements_filter_form" method="GET">';
        $output.= '<select name="achieved_id" id="achieved_id">';
        if(isset($_GET['achieved_id'])&& $_GET['achieved_id']!==''){
            $output.= '<option value="'.$_GET['achieved_id'].'">'.get_the_title($_GET['achieved_id']).'</option>';
        }
        $output.= '<option value="">請選擇活動名稱</option>';
        $output.= '<option value="">所有貼文</option>';
        foreach ($unique_achieved_ids as $value) {
            $output.= '<option value="'.$value.'">'.get_the_title($value).'</option>';
        }
        $output.= '</select>';
        $output.= '<input type="submit" value="提交">';
        $output.= '</form>';
    } 

    return $output;
}

function add_acheivement_filter_select_option(){

    $output="";
    $group_id = bp_get_current_group_id(); 
    if(0!==$group_id){
        $output=show_group_achievements_filter($group_id);
    }else{
        $output=show_member_achievements_filter(); 
    }
    echo $output;
}
add_action('bp_before_group_activity_post_form', 'add_acheivement_filter_select_option', 10, 3);
add_action('bp_before_member_activity_post_form', 'add_acheivement_filter_select_option', 10, 3);

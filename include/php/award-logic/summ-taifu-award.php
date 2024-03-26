<?php


function taifu_achievement_award($content, $user_id, $activity_id)
{
   
	$achievement_id = '';

	if ( isset( $_POST['achievement_type_select'] ) ) {
		$achievement_id = $_POST['achievement_type_select'];
	}

	if ( isset( $_COOKIE['achievement_id'] ) ) {
		$achievement_id = $_COOKIE['achievement_id'];
	}

	if ( empty( $achievement_id ) || $achievement_id !== '12688' ) {
		return;
	}

    if (!isset($_POST['mpp-attached-media'])) {
        echo '<script>alert("請附上一張照片");</script>';
        $args = array(
            'id'                => $activity_id,
        );
    
        bp_activity_delete($args);
        return;
    }

    $location = '現場';
  
    if (!mpp_activity_has_media($activity_id)) {
        return;
    }
   
    
    gamipress_award_achievement_to_user($achievement_id, $user_id); 
        $current_user = wp_get_current_user();
        $username = $current_user->user_login; // 获取用户名
        $month = date('n');
        $day = date('d');
        $year = date('Y');

        $attached_media_array = explode(',', $_POST['mpp-attached-media']);

        $pictures='';

    foreach ($attached_media_array as $index => $media) {
        $pictures .= mpp_get_media_src('original', $media);

        // 檢查是否是最後一筆資料
        if ($index < count($attached_media_array) - 1) {
            $pictures .= ','; // 如果不是最後一筆，添加逗號
        }
    }
        GLOBAL $wpdb;
                    $table_name = 'wp_gamipress_user_earnings';  
                    $query = "SELECT * FROM $table_name where user_id = $user_id and post_id = 12688 ORDER BY user_earning_id DESC LIMIT 1";
                    $result = $wpdb->get_results($query);

    if (!$result) {
        echo 'data add faild';
        wp_die();
    }    
    $user_earning_id = $result[0]->user_earning_id;
    $date = $result[0]->date;

    $table_name      = 'summ_gamipress_log_extra_data';
    $args  = array(
        'user_earning_id'              => $user_earning_id,
        'achievement_id'              => $achievement_id,
        'user_id'             		   => $user_id,
        'activity_id'                  => $activity_id,
        'datetime'                     => $date,
        'carbon_token_unit'            => 1,
        'carbon_token'                 => 1,
        'carbon_token_granted_once'    => 0,
        'gooddeed_token_unit'          => 1,
        'gooddeed_token'               => 1,
        'gooddeed_token_granted_once'  => 100,
        'completed_numbers'            => 1,
        'carbon_token_granted_total'   => 0,
        'gooddeed_token_granted_total' => 100,
        'pictures'                     => $_POST['mpp-attached-media'],
        'location' 					   =>  $location,

    );
    add_summ_gamipress_log_extra_data($args);

    gamipress_award_points_to_user( $user_id, 100, 'gooddeed-token' );

    $tree_love_womens_day_total_gooddeed_token = get_option( 'tree_love_womens_day_total_gooddeed_token', 0 );
    $tree_love_womens_day_total_gooddeed_token = $tree_love_womens_day_total_gooddeed_token + 5;
    update_option( 'tree_love_womens_day_total_gooddeed_token', $tree_love_womens_day_total_gooddeed_token );

                    $log_data_time = $wpdb->get_results("SELECT date_recorded FROM wp_bp_activity WHERE id='$activity_id' ");
                    $time=$log_data_time[0]->date_recorded;
                $arg=array(
                    'id'                => $activity_id,                  // Pass an existing activity ID to update an existing entry.
                  // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
                    'content'           => $content,    
                    'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
                    'user_id'           => $user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
        
                    'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
                    'item_id'           => 7,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
                    'recorded_time'     =>  $time, // The GMT time that this activity was recorded.
                    'error_type'        => 'bool',
                );
                bp_activity_add($arg);

                $success_message='謝謝您的分享，已將100點好人幣，發送給您';
                   
                echo '<script>alert("'. $success_message.'");</script>';
                echo '<script>location.reload();</script>';

                

} 



    add_action('bp_activity_posted_update', 'taifu_achievement_award', 10, 3);

<?php

/**
 * Function for 'achievement_id_award_content' action.
 *
 * @param mixed $content  The content parameter.
 * @param mixed $user_id  The user_id parameter.
 * @param mixed $activity_id  The activity_id parameter.
 */
function achievement_id_award_content( $content, $user_id, $activity_id ) {

	/**
	 *
	 * error_log(print_r($_POST, true));
	 *  */

	$achievement_id = '';

	if ( isset( $_POST['achievement_type_select'] ) ) {
		$achievement_id = $_POST['achievement_type_select'];
	}

	if ( isset( $_COOKIE['achievement_id'] ) ) {
		$achievement_id = $_COOKIE['achievement_id'];
	}

	if ( empty( $achievement_id ) || $achievement_id !== '9970' ) {
		return;
	}

	$args = array(
		'id' => $activity_id,
	);
	if ( ! isset( $_POST['mpp-attached-media'] ) ) {
		echo '<script>alert("本活動需上傳照片，一次最少一張，一日最多十張");</script>';
		bp_activity_delete( $args );

	}

	$attached_media_array = explode( ',', $_POST['mpp-attached-media'] );

	$achievement_id＿earned_count = '';
	// $pictures =  mpp_get_media_src('thumbnail', "10123");

	$currentDateTime = new DateTime();
	$currentDateTime->setTimezone( new DateTimeZone( 'Asia/Taipei' ) );
	$currentDateTime2 = new DateTime();
	$currentDateTime2->setTimezone( new DateTimeZone( 'Asia/Taipei' ) );

	// 获取前一天的日期时间
	$beforeDateTime = $currentDateTime->modify( '1 day' );
	$sinceDateTime  = $currentDateTime2->modify( '0 day' );

	// 格式化前一天的日期时间为所需的格式
	$before = $beforeDateTime->format( 'Y-m-d' );
	$since  = $sinceDateTime->format( 'Y-m-d' );

	$achievement_id_earned_count = gamipress_get_earnings_count(
		array(
			'user_id'        => $user_id,
			'achievement_id' => $achievement_id,
			'before'         => $before,
			'since'          => $since,

		)
	);

	if ( $achievement_id_earned_count + count( $attached_media_array ) > 10 ) {

		$over_images_message = '一日上限為十張，您本日已上傳' . $achievement_id_earned_count . '張擁抱照片，此次上傳' . count( $attached_media_array ) . '張，已超出數量，請重新上傳。';

		echo '<script>alert("' . $over_images_message . '");</script>';
		bp_activity_delete( $args );
		return;
	}

	if ( ! mpp_activity_has_media( $activity_id ) ) {
		return;
	}

	for ( $i = 0; $i < count( $attached_media_array ); $i++ ) {
		tree_love_do_award( $activity_id, $achievement_id, $user_id, $attached_media_array[ $i ] );
	}
	$todays_total_award_number = $achievement_id_earned_count + count( $attached_media_array );
	$todays_total_award_point  = $todays_total_award_number * 5;
	
	GLOBAL $wpdb;

	$log_data_time = $wpdb->get_results("SELECT date_recorded FROM wp_bp_activity WHERE id='$activity_id' ");
	$time=$log_data_time[0]->date_recorded;

	$arg=array(
		'id'                => $activity_id,                  // Pass an existing activity ID to update an existing entry.
	  // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
		'content'			=> $content,
	  'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
		'user_id'           => $user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.

		'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
		'item_id'           => 12,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
		'recorded_time'     =>  $time, // The GMT time that this activity was recorded.
		'error_type'        => 'bool',
	);
	bp_activity_add($arg);


	
	$success_message = '恭喜您：您本日已上傳' . $todays_total_award_number . '張擁抱照片，獲得' . $todays_total_award_point . '點好人幣，目前已累計捐款' . $todays_total_award_point . '元，一日上限為十張照片。';

	echo '<script>alert("' . $success_message . '");</script>';
	echo '<script>location.reload();</script>';

}


	add_action( 'bp_activity_posted_update', 'achievement_id_award_content', 9999, 3 );


function tree_love_do_award( $activity_id, $achievement_id, $user_id, $attached_media ) {

	gamipress_award_achievement_to_user( $achievement_id, $user_id );
	$tree_love_womens_day_total_finish_numbers = get_option( 'tree_love_womens_day_total_finish_numbers', 0 );
	$tree_love_womens_day_total_finish_numbers = $tree_love_womens_day_total_finish_numbers + 1;
	update_option( 'tree_love_womens_day_total_finish_numbers', $tree_love_womens_day_total_finish_numbers );

	$current_user = wp_get_current_user();
	$username     = $current_user->user_login; // 获取用户名
	$month        = date( 'n' );
	$day          = date( 'd' );
	$year         = date( 'Y' );
	$location     = '現場';
	global $wpdb;
				$table_name = 'wp_gamipress_user_earnings';
				$query      = "SELECT * FROM $table_name where user_id = $user_id and post_id = 9970 ORDER BY user_earning_id DESC LIMIT 1";
				$result     = $wpdb->get_results( $query );

	if ( ! $result ) {
		echo '新增資料失敗';
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
					'gooddeed_token_granted_once'  => 5,
					'completed_numbers'            => 1,
					'carbon_token_granted_total'   => 0,
					'gooddeed_token_granted_total' => 5,
					'pictures'                     => $attached_media,
					'location' 					   => $location,
				);
				add_summ_gamipress_log_extra_data($args);

				gamipress_award_points_to_user( $user_id, 5, 'gooddeed-token' );

				$tree_love_womens_day_total_gooddeed_token = get_option( 'tree_love_womens_day_total_gooddeed_token', 0 );
				$tree_love_womens_day_total_gooddeed_token = $tree_love_womens_day_total_gooddeed_token + 5;
				update_option( 'tree_love_womens_day_total_gooddeed_token', $tree_love_womens_day_total_gooddeed_token );
}

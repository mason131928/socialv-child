<?php

/**
 * Function for 'my_check_login' action.
 */
function my_check_login() {

	// Check verificated_username
	if ( ! isset( $_COOKIE['verificated_username'] ) ) {
		return;
	}

	// Check achievement_id
	if ( ! isset( $_COOKIE['achievement_id'] ) ) {
		return;
	}

	$username_phone = $_COOKIE['verificated_username'];
	?>
	<script>
			document.cookie = 'verificated_username=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
	</script>
	<?php
	
	// Login user.

		$user = get_user_by( 'login', $username_phone );

        if (empty($user)) {
			$users_by_phone = get_users(array(
				'meta_key' => 'summost_phone_number',
				'meta_value' => $username_phone,
				'number' => 1,
				'count_total' => false
			));


        // 检查是否找到用户对象
        if (!empty($users_by_phone)) {
            $user = reset($users_by_phone); // 获取第一个用户对象
    
            // 如果用户输入的密码与数据库中的密码匹配，则返回用户对象
		
		if ( $user ) {
			wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login );
			update_user_meta( $user->ID, 'summost_phone_auth', 'yes' );
			}
        	}
        }


		/**
		 * tree love achievement_id 
		 * */
		
		$achievement_id_array = array('9968', '11072','11384','11385','14455','16370','19588');
		 if (in_array($_COOKIE['achievement_id'], $achievement_id_array)) {
			tree_love_redirect($user);
		 }	
		

		/**
		 * taifu achievement_id 
		 * */

		$achievement_id_array = array('12665');
		 if (in_array($_COOKIE['achievement_id'], $achievement_id_array)) {
			taifu_redirect($user);
		}



}
	add_action( 'init', 'my_check_login' );



	/* login general functions start*/
	function summ_award_achievement_to_user($user){
		gamipress_award_achievement_to_user( $_COOKIE['achievement_id'], $user->ID );

		$achievement_id= $_COOKIE['achievement_id'];

		global $wpdb;
		$table_name = 'wp_gamipress_user_earnings';
		$query      = "SELECT * FROM $table_name where user_id = $user->ID and post_id = $achievement_id ORDER BY user_earning_id DESC LIMIT 1";
		$result     = $wpdb->get_results( $query );

		if ( ! $result ) {
				echo '新增資料失敗';
				wp_die();
			}

		return $result;
	}


	function summ_gamipress_time_to_buddypress($date){
		$originalDateTime = new DateTime($date);
		$timeInterval = new DateInterval('PT8H'); // PT8H 表示8小时的时间间隔
		$modifiedDateTime = $originalDateTime->sub($timeInterval);
		return $modifiedDateTime;
	}

	function summm_change_achievement_id($target_achievement_id=null){

		if($target_achievement_id==null){
			return ;
		} 

		?>
		<script>
				var expirationDate = new Date();
				expirationDate.setTime(expirationDate.getTime() + 60 * 60 * 1000); // 60 minutes * 60 seconds * 1000 milliseconds
				document.cookie = 'achievement_id='+'<?php echo $target_achievement_id; ?>'+'; expires=' + expirationDate.toUTCString() + '; path=/';
		</script>
			<?php
	}
	/* login general functions end*/

	

	/* tree love login */

	function tree_love_redirect($user){
		/*check whether join or not*/
		$achievement_id＿earned_count = gamipress_get_earnings_count(
			array(
				'user_id'        => $user->ID,
				'achievement_id' => $_COOKIE['achievement_id'],
			)
		);
		/* if already join change change achievement id, don't award*/
		if ( $achievement_id＿earned_count > 0) {
			summm_change_achievement_id(9970);
			return;
		}

		/* if not join yet, do award */
		$result=summ_award_achievement_to_user($user);	
			$user_earning_id = $result[0]->user_earning_id;
			$date = $result[0]->date;
			$modifiedDateTime=summ_gamipress_time_to_buddypress($date);
	
		/* add activity */
			$arg=array(
				'id'                => false,                  // Pass an existing activity ID to update an existing entry.
				'action'            => '',                     // The activity action - e.g. "Jon Doe posted an update".
				'content'           => '',                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
				'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
				'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
				'primary_link'      => '',                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
				'user_id'           => $user->ID,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
				'item_id'           => 12,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
				'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
				'recorded_time'     => $modifiedDateTime->format('Y-m-d H:i:s'), // The GMT time that this activity was recorded.
				'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
				'is_spam'           => false,                  // Is this activity item to be marked as spam?
				'error_type'        => 'bool',
			);
			$activity_id = bp_activity_add($arg);

		/* join group */
	
			groups_join_group(12,$user->ID);
		
		/* add db log */


						$args  = array(
							'user_earning_id'              => $user_earning_id,
							'achievement_id'              => $_COOKIE['achievement_id'],
							'user_id'             		   => $user->ID,
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
							'gooddeed_token_granted_total' => 0,
							'pictures' => Null,
							'location' 					   => '嘉義縣表演藝術中心全園區',


						);
						add_summ_gamipress_log_extra_data($args);

		/* change cookie achievement_id */
			summm_change_achievement_id(9970);

	}




	function taifu_redirect($user){
		/*check whether join or not*/
		$achievement_id＿earned_count = gamipress_get_earnings_count(
			array(
				'user_id'        => $user->ID,
				'achievement_id' => $_COOKIE['achievement_id'],
			)
		);
		/* if already join change change achievement id, don't award*/
		if ( $achievement_id＿earned_count > 1 ) {
			summm_change_achievement_id(9970);
			return;
		}

		/* if not join, do award */
		$result=summ_award_achievement_to_user($user);	
			$user_earning_id = $result[0]->user_earning_id;
			$date = $result[0]->date;
			$modifiedDateTime=summ_gamipress_time_to_buddypress($date);
	
		/* add activity */
		$arg=array(
			'id'                => false,                  // Pass an existing activity ID to update an existing entry.
			'action'            => '',                     // The activity action - e.g. "Jon Doe posted an update".
			'content'           => '參加公益創新故事分享會',                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
			'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
			'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
			'primary_link'      => '',                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
			'user_id'           => $user->ID,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
			'item_id'           => 7,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
			'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
			'recorded_time'     => $modifiedDateTime->format('Y-m-d H:i:s'), // The GMT time that this activity was recorded.
			'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
			'is_spam'           => false,                  // Is this activity item to be marked as spam?
			'error_type'        => 'bool',
		);
			$activity_id = bp_activity_add($arg);

		/* join group */
	
			groups_join_group(7,$user->ID);

		/* point award */
		gamipress_award_points_to_user( $user->ID, 100, 'gooddeed-token' );

		/* add db log */
			$args  = array(
			'user_earning_id'              => $user_earning_id,
			'achievement_id'              => $achievement_id,
			'user_id'             		   => $user->ID,
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
			'pictures' => Null,
			'location' 					   => 'QR-CODE',


		);
		add_summ_gamipress_log_extra_data($args);

		/* change cookie achievement_id */
			summm_change_achievement_id(12688);

	}
<?php
/**
 * SocialvChild Sum Member machanism
 *
 * @package YourPackage
 * @author Lucas
 */

/**
 * Function for custom_phone_verification_shortcode action.
 */
function taifu_conference_phone_verification_shortcode() {


	if (is_user_logged_in() && (current_user_can('administrator')) ) {
		return;
	}

	/**
	 * The following action is commented out intentionally because...
	 * error_log(print_r($_POST, true));
	 *  */

	unique_cookie();

	if ( is_user_logged_in() && isset( $_COOKIE['achievement_id'] ) ) {
		$current_user = wp_get_current_user();
		$username     = $current_user->user_login; // 获取用户名
		echo '<h2 style="text-align:center;">感謝您參與此事創新交流分享會</h2><br />';
		echo '<h2 style="text-align:center;">頁面跳轉中⋯</h2><br />';
		echo '<script>window.location.href = "/members/' . $username . '";</script>';
		return;
	}

	ob_start();

	// 檢查用戶是否已經點擊了「取得驗證碼」按鈕.
	if ( isset( $_POST['get_verification_code'] ) ) {

		if ( ! isset( $_POST['phone_number'] ) ) {
			echo '請輸入電話號碼。';

			return;
		}
		$phone_number = $_POST['phone_number'];

		?>
		<script>
		var expirationDate = new Date();
		expirationDate.setFullYear(expirationDate.getFullYear() + 1);
		document.cookie = 'phone_number='+'<?php echo $_POST['phone_number']; ?>'+'; expires=' + expirationDate.toUTCString() + '; path=/';
		</script>
		<?php

		/**
		 * The following action is commented out intentionally because...
		 * 檢查是否已經超過一分鐘，以避免多次請求
		 * if (!isset($_COOKIE['last_verification_request']) || (time() - $_COOKIE['last_verification_request']) >= 60) {
		 *  */

// 		if ( isset( $_COOKIE['last_verification_request'] ) && ( time() - $_COOKIE['last_verification_request'] ) < 180 ) {
// 				echo '請稍等三分鐘再嘗試。';
// 				return;
// 		}

			// 生成六碼隨機碼.
			$verification_code = rand( 100000, 999999 );
			// 將驗證碼和用戶ID存儲到custom_phone_auth表中.
			$cookie_id = $_COOKIE['unique_user_id']; // 假設你有一個名為'user_id'的Cookie來存儲用戶ID
			// 執行SQL語句，將數據插入custom_phone_auth表中.
			global $wpdb;
			$table_name = 'summ_phone_auth';
			$query      = "SELECT * FROM $table_name where cookie_id = '$cookie_id' ";
			$result     = $wpdb->get_results( $query );

		// 已有cookie_id 更新驗證碼.
		if ( $result ) {
			$data_to_update = array(
				'code' => $verification_code,
			);

			// 设置 WHERE 子句以匹配特定的 cookie_id.
			$where_clause = array(
				'cookie_id' => $cookie_id,
			);
			$wpdb->update( $table_name, $data_to_update, $where_clause );
		}

		// 未有cookie_id 寫入驗證碼.
		if ( ! $result ) {
			$data_to_insert = array(
				'cookie_id' => $cookie_id,
				'code'      => $verification_code,
			);
			$wpdb->insert( $table_name, $data_to_insert );

		}
			$strOnlineSend  = 'http://www.smsgo.com.tw/sms_gw/sendsms.aspx?';
			$strOnlineSend .= 'username=mason@guppy3.com';
		$strOnlineSend     .= '&password=079cda5b';
			$strOnlineSend .= "&dstaddr=$phone_number";
			$strOnlineSend .= '&encoding=BIG5';
			$strOnlineSend .= '&smbody=' . urlencode( '台灣台復新創學會，歡迎您加入好事道平台，您的驗證碼為：' . $verification_code );
			$strOnlineSend .= '&response=' . urlencode( 'http://localhost:8888/index3.php' );
			/**
			 * The following action is commented out intentionally because...
			 * echo ($strOnlineSend);
			 *  */
			$file = @fopen( $strOnlineSend, 'r' );
			// 設置最後一次驗證請求的時間.
			$auth_duration = time();

		?>
			<script>
			setTimeout(function() {
				jQuery("#get_verification_code").css("display", "none");
				}, 100); // 500毫秒（0.5秒）
			var expirationDate = new Date();
			expirationDate.setFullYear(expirationDate.getFullYear() + 1);
			document.cookie = 'last_verification_request='+'<?php echo $auth_duration; ?>'+'; expires=' + expirationDate.toUTCString() + '; path=/';

			//var countdown = 180;
			var countdown = 0;

			var countdownInterval = setInterval(function() {
			jQuery("#countdown").html("剩餘時間: " + countdown + "秒");
			countdown--;
			if (countdown < 0) {
			clearInterval(countdownInterval);
			jQuery("#countdown").html("");
			jQuery("#phone_input").css("display", "block");
			jQuery("#get_verification_code").css("display", "");
			}
			}, 1000);

			</script>
			<?php
	}

	if ( isset( $_POST['submit_verification_code'] ) ) {

		if ( ! isset( $_COOKIE['phone_number'] ) ) {
			echo '請先取得驗證碼';
			return;
		}

		if ( ! isset( $_POST['achievement_type_select'] ) ) {
			echo '請選擇參與的活動';
			return;
		}

		// 使用戶提交了驗證碼.
		$entered_code = intval( $_POST['verification_code'] );
		$cookie_id    = $_COOKIE['unique_user_id'];
		// 檢查是否存在匹配的驗證碼.
		global $wpdb;
		$verification_data = $wpdb->get_row(
			$wpdb->prepare( 'SELECT * FROM summ_phone_auth WHERE cookie_id = %s AND code = %d', $cookie_id, $entered_code )
		);
		$phone_number      = $_COOKIE['phone_number'];

		if ( $verification_data ) {
			// 刪除驗證數據.
			$wpdb->delete( 'summ_phone_auth', array( 'id' => $verification_data->id ) );

			$userdata = array(
				'user_login' => $phone_number, // 用户名.
				'user_pass'  => $phone_number, // 密码.

				'nickname'   => $phone_number, // 昵称.
				'role'       => 'subscriber', // 角色 (subscriber, author, editor, administrator, etc.).
			);

			$user_id = wp_insert_user( $userdata );

			$achievement_id       = '';
			$achievement_id       = $_POST['achievement_type_select'];
			$verification_message = '驗證成功，歡迎來到好事道';

			?>
				<script>
				var expirationDate = new Date();
				expirationDate.setFullYear(expirationDate.getFullYear() + 1);
				document.cookie = 'verificated_username='+'<?php echo $phone_number; ?>'+'; expires=' + expirationDate.toUTCString() + '; path=/';
				var expirationDate2 = new Date();
				expirationDate2.setTime(expirationDate2.getTime() + 60 * 60 * 1000); // 60 minutes * 60 seconds * 1000 milliseconds
				document.cookie = 'achievement_id='+'<?php echo $achievement_id; ?>'+'; expires=' + expirationDate2.toUTCString() + '; path=/';
				document.cookie = 'last_verification_request=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
				document.cookie = 'phone_number=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
				document.cookie = 'welcome_message=taifu; expires=' + expirationDate2.toUTCString() + '; path=/';
				location.reload();

				</script>
				<?php

		}

		if ( isset( $_COOKIE['welcome_message'] ) && isset( $_COOKIE['verificated_username'] ) ) {
			echo '<script>window.location.href = "/members/' . $user->ID . '";</script>';
			return;
		}

		if ( ! $verification_data ) {
			$verification_message = '驗證碼錯誤';
		}
	}

	if ( isset( $_POST['phone_number'] ) ) {
		$phone_number = $_POST['phone_number'];
	}

	if ( ! isset( $_POST['phone_number'] ) ) {
		$phone_number = '';
	}

	if ( ! isset( $verification_message ) ) {
		$verification_message = '';
	}
	?>
	<form method="post" id="phone_auth_form">
	<p><?php echo $verification_message; ?></p>

	<p>1.請輸入手機號碼：</p>
			<input style="" type="text" name="phone_number" pattern="09\d{8}" value="<?php echo $phone_number; ?>" required> <input id="get_verification_code" type="submit" name="get_verification_code" style="" value="取得驗證碼">  <p id="countdown"></p>
	</form>

		<form method="post" id="phone_auth_form2">
	   
		<p>2.請選擇參與活動：</p>
		<?php // echo  show_select(); ?>

		<?php

		$output  = '<select name="achievement_type_select">';
		$output .= '<option value="12665">參加創新故事交流會</option>';
		$output .= '</select>';

		echo $output;

		?>
		   

		<p><label for="verification_code">3.請輸入驗證碼：</label></p>
			<input style="" type="text" name="verification_code" maxlength="6" required>
			<input type="hidden" name="phone_number" id="verification_code" value="<?php echo $phone_number; ?>">
			<input type="submit" style="" name="submit_verification_code" value="送出驗證碼">
	</form>
	<?php
	return ob_get_clean();
}


	add_shortcode( 'taifu_conference_phone_verification', 'taifu_conference_phone_verification_shortcode' );


/**
 * Function for 'my_check_login' action.
 */
function taifu_my_check_login() {




	if ( ! isset( $_COOKIE['verificated_username'] ) ) {
		return;
	}

	$username_phone = $_COOKIE['verificated_username'];
	?>
	<script>
document.cookie = 'verificated_username=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
	</script>
	<?php

	/**
	 * The following action is commented out intentionally because...
	 * if (!isset($_COOKIE['verificated_username_exist'])) {
	 *
	 * $user = wp_signon(
	 * array(
	 * 'user_login' => $username_phone,
	 * 'user_password' => $username_phone,
	 * 'remember' => true // 如果要保持登录状态，请设置为true
	 * )
	 * );
	 *
	 * }
	 *  */

	// Login user.

	if ( isset( $_COOKIE['verificated_username'] ) ) {
		$user = get_user_by( 'login', $username_phone );
		if ( $user ) {
			wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login );

			update_user_meta( $user->ID, 'summost_phone_auth', 'yes' );
		}

		// Award after login.
		if ( ! isset( $_COOKIE['achievement_id'] ) ) {
			return;
		}

		$achievement_id＿earned_count = gamipress_get_earnings_count(
			array(
				'user_id'        => $user->ID,
				'achievement_id' => $_COOKIE['achievement_id'],
			)
		);
		/**
		 * The following action is commented out intentionally because...
		 * error_log(print_r($_COOKIE['achievement_id'], true));
		 *  */
		$achievement_id_array = array('12665');
		 if (in_array($_COOKIE['achievement_id'], $achievement_id_array)) {

			$achievement_id = $_COOKIE['achievement_id'];
			if ( 1 > $achievement_id＿earned_count ) {
				gamipress_award_achievement_to_user( $_COOKIE['achievement_id'], $user->ID );

				$current_user = wp_get_current_user();
				$username     = $current_user->user_login; // 获取用户名.
				global $wpdb;
							$table_name = 'wp_gamipress_user_earnings';
							$query      = "SELECT * FROM $table_name where user_id = $user->ID and post_id = $achievement_id ORDER BY user_earning_id DESC LIMIT 1";
							$result     = $wpdb->get_results( $query );

				if ( ! $result ) {
					echo '新增資料失敗';
					wp_die();
				}
				
				$user_earning_id = $result[0]->user_earning_id;
				$date = $result[0]->date;
				$originalDateTime = new DateTime($date);
				$timeInterval = new DateInterval('PT8H'); // PT8H 表示8小时的时间间隔
				$modifiedDateTime = $originalDateTime->sub($timeInterval);
				

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
				groups_join_group(7,$user->ID);
				gamipress_award_points_to_user( $user->ID, 100, 'gooddeed-token' );


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

							/**
							 * The following action is commented out intentionally because...
							 * gamipress_award_points_to_user($user_id, 0, 'gooddeed-token');
							 *  */

			}

			?>
			<script>
					var expirationDate = new Date();
					expirationDate.setTime(expirationDate.getTime() + 60 * 60 * 1000); // 60 minutes * 60 seconds * 1000 milliseconds
					document.cookie = 'achievement_id=12688; expires=' + expirationDate.toUTCString() + '; path=/';
			</script>
				<?php
		}
	}
}
	//add_action( 'template_redirect', 'taifu_my_check_login' );



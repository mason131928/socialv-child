<?php


/**
 * add user account change column
 */
function summ_user_avatar_image_save() {


	if ( ! bp_is_post_request() ) {
		return;
	}

	if ( ! isset( $_POST['summ-avatar-image-submit'] ) ) {
		return;
	}
	if ( ! isset( $_POST['summ-avatar-image'] ) ) {
		return;
	}

    check_admin_referer( 'bp_summ_user_avatar_image' );

	$user_id       = bp_displayed_user_id(); // The ID of the user being displayed.

    error_log( print_r(123123, true) );

}

add_action( 'bp_actions', 'summ_user_avatar_image_save');







/**
 * add user phone change column
 */
function summ_user_phone_setting_save() {

	if ( ! bp_is_post_request() ) {
		return;
	}
    if ( ! isset( $_POST['phone-code-submit'] ) ) {
        return;
    }
//取得驗證碼部分
	if ( isset( $_POST['phone'] ) ) {
        if ( ! isset( $_POST['phone-code-submit'] ) ) {
            return;
        }
        check_admin_referer( 'bp_settings_general' );

	$user_id       = bp_displayed_user_id(); // The ID of the user being displayed.
        
        // 通过电话号码获取用户对象
        $user_by_phone = get_users(array(
            'meta_key' => 'summost_phone_number',
            'meta_value' => $_POST['phone'],
            'number' => 1,
            'count_total' => false
        ));
    
        // 检查是否找到用户对象
        if (!empty($user_by_phone)) {
            $message['account-change']='手機已被使用，請更改其他帳號';
            $feedback_type='error';
            bp_core_add_message( implode( "\n",   $message ), $feedback_type );
        }
        
        if (empty($user_by_phone)) {
            
        $message['account-change']='請輸入收到之驗證碼';
        $feedback_type='success';
    	bp_core_add_message( implode( "\n",   $message ), $feedback_type );
       $phone_auth_array['code']=rand( 100000, 999999 );
       $phone_auth_array['phone']=$_POST['phone'];
       $phone_auth_array['success_date']='';
        update_user_meta($user_id, 'summ_account_phone_change', $phone_auth_array);
        send_sms($phone_auth_array['phone'],$phone_auth_array['code'],'來自好事道，這是您要更改的手機號碼驗證碼');			

        }
         

	}


//確認驗證碼，更改手機號碼
	if ( isset( $_POST['auth-code'] ) ) {
        if ( ! isset( $_POST['auth-code-submit'] ) ) {
            return;
        }
        check_admin_referer( 'bp_settings_general' );


        
	$user_id       = bp_displayed_user_id(); // The ID of the user being displayed.
    $phone_auth_array = get_user_meta($user_id, 'summ_account_phone_change', true);
    //error_log( print_r($phone_auth_array, true) );

    
        // 检查是否找到用户对象
        if ($_POST['auth-code']!==$phone_auth_array['code']) {
            $message['account-change']='驗證碼錯誤';
            $feedback_type='error';
            bp_core_add_message( implode( "\n",   $message ), $feedback_type );
        }
        
        if ((int)$_POST['auth-code']===(int)$phone_auth_array['code']) {
         
        $message['account-change']='手機更改成功';
        $feedback_type='success';
    	bp_core_add_message( implode( "\n",   $message ), $feedback_type );
       $phone_auth_array['success_date']=current_time('mysql');
        update_user_meta($user_id, 'summost_phone_number', $phone_auth_array['phone']);
        update_user_meta($user_id, 'summ_account_phone_change', $phone_auth_array);

        }
	}
    
}

add_action( 'bp_actions', 'summ_user_phone_setting_save');


/**
 * add user account change column
 */
function summ_user_account_setting_save() {


	if ( ! bp_is_post_request() ) {
		return;
	}

	if ( ! isset( $_POST['account-change'] ) ) {
		return;
	}
	if ( ! isset( $_POST['account'] ) ) {
		return;
	}
    
    check_admin_referer( 'bp_settings_general' );

	$user_id       = bp_displayed_user_id(); // The ID of the user being displayed.

    if (!username_exists($_POST['account'])) {
        $message['account-change']='帳號更改成功';
        $feedback_type='success';
    	bp_core_add_message( implode( "\n",   $message ), $feedback_type );
        wp_update_user(array('ID' => $user_id, 'user_login' => $_POST['account']));
        $account_has_changed = update_user_meta($user_id, 'summ_account_has_changed', current_time('mysql'));


        global $wpdb;
        $wpdb->update(
           $wpdb->users,
           array(
               'user_login' => $_POST['account'],
               'user_nicename' =>  $_POST['account']
           ),
           array('ID' => $user_id)
       );
       echo '<script>alert("帳號更改成功，請您重新登入帳號");</script>';
       echo '<script>location.href = "' . home_url() . '"; </script>';

    //    $redirect_to = home_url();
    //    bp_core_redirect( $redirect_to );
    } else {
        // 如果用户名已存在，请处理错误
        $message['account-change']='帳號已被使用，請更改其他帳號';
        $feedback_type='error';
    	bp_core_add_message( implode( "\n",   $message ), $feedback_type );

    }
	//bp_core_add_message( implode( "\n",   $message ), $feedback_type );

	// Set the feedback.



}

add_action( 'bp_actions', 'summ_user_account_setting_save');

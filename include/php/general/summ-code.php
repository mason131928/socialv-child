<?php

//test code start




function copy_account_to_phone_field() {
    $users = get_users();
    global $wpdb;

    //$wpdb->update($wpdb->users, array('user_login' => 'lucas'), array('ID' => 2));


    // 循环遍历每个用户
    $amount=0;
    foreach ($users as $user) {
        // 检查用户是否以 "09" 开头
        if (substr($user->user_login, 0, 2) === '09') {
             // 1.複製帳號到手機
            update_user_meta($user->ID, 'summost_phone_number', $user->user_login);
            $amount++;
            //error_log( print_r($user->user_login.' '. $amount,1));
            //error_log( print_r($user->ID.' '. $amount,1));

            //2.更新帳號
            $current_time = (int)time();
            $new_username= $current_time+(int)$user->ID;
            $new_username= 'gdm'.$new_username;

            global $wpdb;
            $wpdb->update(
               $wpdb->users,
               array(
                   'user_login' => $new_username,
                   'user_nicename' => $new_username
               ),
               array('ID' => $user->ID)
           );

          //  error_log( print_r($user->user_login.' '. $amount,1));

        }
    }
    //error_log( print_r($amount,1));
    $unique_id = uniqid();
    $current_time = time();
    $user_query = new WP_User_Query( array(
        'role'   => 'subscriber', // 可以根据用户角色来过滤
        'fields' => 'ID', // 只获取用户的ID
    ));


}



//add_action( 'get_sidebar', 'copy_account_to_phone_field' );





//test code end 


<?php

/**
 * let user do normal login through custom field phone
 */
function login_by_phone_login($user, $username, $password) {
        // 检查是否已经有用户登录
        if ($user instanceof WP_User) {
            return $user;
        }
    
        // 通过用户名获取用户对象
        $user_by_username = get_user_by('login', $username);
    
        // 检查是否找到用户对象，以及用户对象中是否有电话号码
        if ($user_by_username && !empty($user_by_username->phone)) {
            // 如果用户输入的密码与数据库中的密码匹配，则返回用户对象
            if (wp_check_password($password, $user_by_username->user_pass, $user_by_username->ID)) {
                return $user_by_username;
            }
        }
    
        // 通过电话号码获取用户对象
        $user_by_phone = get_users(array(
            'meta_key' => 'summost_phone_number',
            'meta_value' => $username,
            'number' => 1,
            'count_total' => false
        ));
    
        // 检查是否找到用户对象
        if (!empty($user_by_phone)) {
            $user_by_phone = reset($user_by_phone); // 获取第一个用户对象
    
            // 如果用户输入的密码与数据库中的密码匹配，则返回用户对象
            if (wp_check_password($password, $user_by_phone->user_pass, $user_by_phone->ID)) {
                return $user_by_phone;
            }
        }
    
        // 登录失败
        return new WP_Error('invalid_username_or_password', __('Invalid username, email, or phone number'));
    }
    
add_action('authenticate', 'login_by_phone_login', 10, 3);


/**
* create a new member / user account by phone, change to random acccount, nicename, and marked thier display name.
*/
function summ_add_member_by_phone($phone_number){


    //check exist or not
        //search array 
        $users_by_phone = get_users(array(
            'meta_key' => 'summost_phone_number',
            'meta_value' => $phone_number,
            'number' => 1,
            'count_total' => false
        ));


    // search user
    if (!empty($users_by_phone)) {
        $user = reset($users_by_phone); // 获取第一个用户对象

    // 如果用户输入的密码与数据库中的密码匹配，则返回用户对象
    if ( $user ) {
        return $user->ID;
        }
     }
    
    //user not exist, create new one.
    $current_time = (int)time();
    $user_count = count_users();
    $new_username= $current_time+(int)$user_count['total_users'];
    $new_username= 'gdm'.$new_username;
	$userdata = array(
        'user_login' => $new_username, // 用户名.
        'user_pass'  => $phone_number, // 密码.
        'nickname'   => $new_str = substr_replace($phone_number, 'xxx', -3),
        'role'       => 'subscriber', // 角色 (subscriber, author, editor, administrator, etc.).
    );
	$user_id = wp_insert_user( $userdata );
    update_user_meta($user_id, 'summost_phone_number', $phone_number);
    return $user_id;
}



    
/**
 * new user custom field phone
 */
function add_extra_profile_fields($user) {
?>
<!--  * phone custom field  -->
    <h3>手機號碼</h3>

    <table class="form-table">
        <tr>
            <th><label for="phone"></label></th>
            <td>
                <input type="text" name="summost_phone_number" id="phone" value="<?php echo esc_attr(get_the_author_meta('summost_phone_number', $user->ID)); ?>" class="regular-text" /><br />
                <span class="description">手機號碼</span>
            </td>
        </tr>
    </table>
<?php
}



/**
 * new user custom field phone
 */
add_action('show_user_profile', 'add_extra_profile_fields',0);
add_action('edit_user_profile', 'add_extra_profile_fields',0);


 /**
 * save user custom field phone
 */
function save_extra_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'summost_phone_number', $_POST['summost_phone_number']);
}


/**
 * save user custom field phone
 */

add_action('personal_options_update', 'save_extra_profile_fields');
add_action('edit_user_profile_update', 'save_extra_profile_fields');

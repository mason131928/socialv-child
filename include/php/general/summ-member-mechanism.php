<?php
/*
手機號碼去識別化
*/
function de_identification_phone_number($account){
    return 123;
}


 //add_filter( 'bp_core_get_user_displayname', 'de_identification_phone_number' );



/**
* 
*/
function post_form_achievement_list()
{

    //cookie users
    if (isset($_COOKIE['achievement_id']) && isset($_COOKIE['welcome_message'])) {
        if('treehug'==$_COOKIE['welcome_message']) {
            $welcome_message= "擁抱照片對象不可重複，上傳成功將獲得5點好人幣<br />照片經審核，跟擁抱無關，平台有權扣除點數與紀錄";
        }
        if('taifu'==$_COOKIE['welcome_message']) {
            $welcome_message = "歡迎您參與公益創新故事分享會<br />活動結束後請與直播畫面自拍<br />附上您此次活動的心得並上傳，即可獲得100點好人幣<br />立即前往線上直播：<br /><a href='https://www.youtube.com/watch?v=vYcgYy8mWkM'>https://www.youtube.com/watch?v=vYcgYy8mWkM</a><br /";
        }
        echo   $welcome_message;
        echo  '<br />';
        echo  '<br />';
    }
    if (isset($_COOKIE['achievement_id'])) {
        return;
    }
    if(bp_is_group()) {
        return;
    }



    // admin user
    if (is_user_logged_in() && (current_user_can('administrator') || get_current_user_id()==158) ) { 
        $output = '發文前，請選擇一項活動行為 *請踴躍寫下您的感想<br />';
        echo  $output;

        echo  '<br />';  
        echo show_select();
        echo  '<br />';
        echo  '<br />';
        echo '<div id="achievement_description"></div>';
        echo  '<br />';

        return;

    }

        //normal user  

        $output = '發文前，請選擇一項活動行為 *請踴躍寫下您的感想<br />';
        echo  '<br />';
        $output .= '<select id="hihihi" name="achievement_type_select" >';


        //tree love 
        $tree_achievement_id_array = array('9968', '11072','11384','11385','14455');
        foreach ($tree_achievement_id_array as $tree_achievement_id) {
            $user = wp_get_current_user();
            $tree_love_access='';
            $achievement_id＿earned_count = gamipress_get_earnings_count(
                array(
                    'user_id'        => $user->ID,
                    'achievement_id' => $tree_achievement_id,
                )
            );
            if($achievement_id＿earned_count>0){
                $tree_love_access=1;
            }
            }

        if ( $tree_love_access=1 ) {
            $output .= '<option value="9970">完成日常擁抱</option>';
		}


         //tree love 
        $taifu_achievement_id_array = array('12665');
        foreach ($taifu_achievement_id_array as $taifu_achievement_id) {
          $user = wp_get_current_user();
          $taifu_access='';
          $achievement_id＿earned_count = gamipress_get_earnings_count(
            array(
             'user_id'        => $user->ID,
             'achievement_id' => $taifu_achievement_id,
            )
             );

             if($achievement_id＿earned_count>0){
                $taifu_access=1;
                    }
             }

             if ( $taifu_access ) {
                $output .= '<option value="12688">創新交流會-心得分享</option>';
            }



        $output .= '</select>';

        echo  $output;


        echo  '<br />';
        echo  '<br />';
        echo '<div id="achievement_description"></div>';
        echo  '<br />';

}
    add_action('bp_before_activity_post_form', 'post_form_achievement_list');


function show_select()
{
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
    $output .= '<select name="achievement_type_select">';
    
    // ���历$achievement_type_names数组
    foreach ($achievement_type_names as $post_name) {
        // 找到post_type为$post_name的所������帖子
    
        //手動限定某類別的徽章
        // if($post_name!='tree-love-womens-day') {
        //     continue;
        // }
    
    
        $related_posts = new WP_Query(
            array(
            'post_type' => $post_name,
            'posts_per_page' => -1,
            )
        );
    
        if ($related_posts->have_posts()) {
            while ($related_posts->have_posts()) {
                $related_posts->the_post();
                // ���出<option>���素
                $output .= '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
            }
            wp_reset_postdata(); // 重置查询
        }
    }
    
    $output .= '</select>';
    return  $output;
}
    



function activites_affiche_infos( $retval )
{

    //error_log(print_r($retval, true));
    if (bp_is_active('activity') ) {
        $retval['action'] = array(        
        'activity_share',    
        'mpp_media_upload', 
        'activity_update',      
   
        );
        return $retval;    
    }
}

add_filter('bp_after_has_activities_parse_args', 'activites_affiche_infos');



function my_custom_function()
{


    if(isset($_POST['action'])&& 'socialv_ajax_login'==$_POST['action']) {

    }

}

add_action('wp_login', 'my_custom_function');


function change_the_title()
{

    if ("member"==bp_activity_get_current_context()) {
        return '好事道平台';
    }
}

add_filter('pre_get_document_title', 'change_the_title');

function below_user_icon()
{
    if (isset($_COOKIE['achievement_id']) && isset($_COOKIE['welcome_message'])) {
        $welcome_message='';
        if('treehug'==$_COOKIE['welcome_message']) {
            $welcome_message= "<br />上傳一張親子、家人擁抱的照片<br />小樹傳愛協會就捐款新台幣5元<br />目標募百萬心擁抱<br />作為學童心靈教育基金";
        }
        echo   $welcome_message;
    }

    if(get_current_user_id()!==bp_displayed_user_id()) {
        return;
    }

    $user_id = bp_displayed_user_id();
    $achievement_id_earned_count=gamipress_get_earnings_count(
        array(
        'user_id'            => $user_id,
        'achievement_id'    =>  9970,
        )
    );
    if(0<$achievement_id_earned_count) {



        if(!isset($welcome_message)) {
            $welcome_message= "<br />上傳一張親子、家人擁抱的照片<br />小樹傳愛協會就捐款新台幣5元<br />目標募百萬心擁抱<br />作為學童心靈教育基金";
            echo   $welcome_message;
        }
        $total_point=$achievement_id_earned_count*5;
        echo '<b><br /><br />每日擁抱累計張數為'.$achievement_id_earned_count.'張<br />累計捐款金額為'.$total_point.'元</b>';
        
        $tree_hug_delete_count=get_user_meta($user_id, 'tree_hug_delete_count', 1);
        if(!empty($tree_hug_delete_count)) {
            echo '<div id="delete-message">';
            echo '<b style="color:red;"><br /><br />您有'.$tree_hug_delete_count.'張照片不符活動規定，已刪除並退回點數，有疑問請聯絡管理員</b>';
            echo '<br /><button id="clearDeleteMessageBtn">清除此訊息</button>';
            echo '</div>';

        }



    }
}

add_action('bp_before_member_header_meta', 'below_user_icon', 1);

function menu_ajustment($content)
{
    //     $content .= '<li class="menu-item">
    //     <a href="' . esc_url('/groups/') . '">
    //     <i class="iconly-Profile icli group"></i>
    //         <span class="menu-title">' . esc_html__('好事團體', 'socialv') . '</span>
    //     </a>
    // </li>';
    


    //     $content .= '<li class="menu-item">
    // <a href="' . esc_url('/members/') . '">
    //     <i class="iconly-Profile icli"></i>
    //     <span class="menu-title">' . esc_html__('好事會員', 'socialv') . '</span>
    // </a>
    // </li>';


//     $content .= '<li class="menu-item">
//     <a href="' . esc_url('/point-logs/') . '">
//     <i class="iconly-Profile icli point-log"></i>
//     <span class="menu-title">' . esc_html__('好事功德榜', 'socialv') . '</span>
// </a>
// </li>';





    //$content .='<script>jQuery(".user-profile-menu li:eq(1), .user-profile-menu li:eq(3), .user-profile-menu li:eq(4)").remove();</script>';

    return $content;

}
add_filter('socialv_user_menu_filter_content_data', 'menu_ajustment', 1);





function phone_auth()
{
  

    unique_cookie();



    if (!is_user_logged_in()) {
        echo "此為登入用戶驗證頁面";
        return;
    }


    $user_id = get_current_user_id(); 
    $login_check = get_user_meta($user_id, 'summost_phone_auth', 1);
    
    if ($login_check) {
        echo "您已通過手機認證";
        return;
    }


    ob_start();
    $verification_code_requested = false;

    // 檢查用戶是否已經點擊了「取得驗證碼」按鈕
    if (isset($_POST['get_verification_code'])) {
        $phone_number =  $_POST['phone_number'];

        // 檢查是否已經超過一分鐘，以避免多次請求
        // if (!isset($_COOKIE['last_verification_request']) || (time() - $_COOKIE['last_verification_request']) >= 60) {

        if (!isset($_COOKIE['last_verification_request']) || (time() - $_COOKIE['last_verification_request']) >= 60) {
            // 生成六碼隨機碼
            $verification_code = rand(100000, 999999);
            // 將驗證碼和用戶ID存儲到custom_phone_auth表中
            $cookie_id = $_COOKIE['unique_user_id']; // 假設你有一個名為'user_id'的Cookie來存儲用戶ID
            
            // 執行SQL語句，將數據插入custom_phone_auth表中
            GLOBAL $wpdb;
            $table_name ='summ_phone_auth';
            $query = "SELECT * FROM $table_name where cookie_id = '$cookie_id' ";
            $result = $wpdb->get_results($query);

                    
            if ($result) {
                $data_to_update = array(
                'code' => $verification_code,
                );
                
                // 设置 WHERE 子句以匹配特定的 cookie_id
                $where_clause = array(
                'cookie_id' => $cookie_id,
                );
                $wpdb->update($table_name, $data_to_update, $where_clause);
            } 
            


            if (!$result) {
                $data_to_insert = array(
                'cookie_id' =>  $cookie_id,
                'code' =>  $verification_code,
                );
                $wpdb->insert($table_name, $data_to_insert);
    
            } 
            $strOnlineSend = "http://www.smsgo.com.tw/sms_gw/sendsms.aspx?";
            $strOnlineSend .= "username=mason@guppy3.com"; $strOnlineSend .= "&password=079cda5b";
            $strOnlineSend .= "&dstaddr=$phone_number";
            $strOnlineSend .= "&encoding=BIG5";
            $strOnlineSend .= "&smbody=".urlencode("歡迎加入好事道，您的驗證碼為：".$verification_code);
            $strOnlineSend .= "&response=".urlencode("http://localhost:8888/index3.php"); 
            //echo ($strOnlineSend);
            $file = @fopen($strOnlineSend, "r");
            
            // 設置最後一次驗證請求的時間
            $auth_duration=time();

            ?>
            <script>
setTimeout(function() {
    jQuery("#get_verification_code").css("display", "none");
}, 100); // 500毫秒（0.5秒）
            var expirationDate = new Date();
            expirationDate.setFullYear(expirationDate.getFullYear() + 1);
            document.cookie = 'last_verification_request='+'<?php echo $auth_duration; ?>'+'; expires=' + expirationDate.toUTCString() + '; path=/';

           var countdown = 60;
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
            $verification_code_requested = true;
        } else {
            echo '請稍等一分鐘再嘗試。';
        }
    } elseif (isset($_POST['submit_verification_code'])) {
        $phone_number =  $_POST['phone_number'];

        // 使用戶提交了驗證碼
        $entered_code = intval($_POST['verification_code']);
        $cookie_id = $_COOKIE['unique_user_id'];
        // 檢查是否存在匹配的驗證碼
        global $wpdb;
        $verification_data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM summ_phone_auth WHERE cookie_id = %s AND code = %d", $cookie_id, $entered_code)
        );

        if ($verification_data) {
         

            // 刪除驗證數據
            $wpdb->delete('summ_phone_auth', array('id' => $verification_data->id));
             

            $user_id = get_current_user_id(); 

            update_user_meta($user_id, 'summost_phone_auth', "yes");

            $user_created=1;
        } else {
            echo '驗證失敗，請檢查輸入的驗證碼。';
        }
    }

    // 顯示HTML表單
    if (isset($user_created)) {
        if ($user_created===1) {
      
            echo '手機驗證成功，歡迎來到好事到。';
            $user_created=0;
          
            return;
        }
        if ($user_created===2) {
            
            return;
        }
    }
    if (!isset($phone_number)) {
        $phone_number="";
    }
    ?>
    <form method="post">
        首次登入請驗證您的手機號碼
    <br>

    <p>請輸入手機號碼：</p>
            <input style="width:400px;border-color: black;" type="text" name="phone_number" pattern="09\d{8}" value="<?php echo $phone_number?>" required> <input id="get_verification_code" type="submit" name="get_verification_code" style="width:300px;border-color: black;" value="取得驗證碼">  <p id="countdown"></p>
            <br>
    </form>

        <form method="post">
        <p><label for="verification_code">驗證碼：</label></p>
            <input style="width:400px;border-color: black;" type="text" name="verification_code" required>
            <input type="hidden" name="phone_number" id="verification_code" value="<?php echo $phone_number; ?>">
            <input type="submit" style="width:300px; border-color: black;" name="submit_verification_code" value="提交驗證碼">
    </form>
    <?php
    return ob_get_clean();


}
    add_shortcode('phone_auth', 'phone_auth');




    // This will suppress empty email errors when submitting the user form
    add_action('user_profile_update_errors', 'my_user_profile_update_errors', 10, 3);

function my_user_profile_update_errors($errors, $update, $user)
{
    $errors->remove('empty_email');
}

    // This will remove javascript required validation for email input
    // It will also remove the '(required)' text in the label
    // Works for new user, user profile and edit user forms
    add_action('user_new_form', 'my_user_new_form', 10, 1);
    add_action('show_user_profile', 'my_user_new_form', 10, 1);
    add_action('edit_user_profile', 'my_user_new_form', 10, 1);
function my_user_new_form($form_type)
{
    ?>
<script type="text/javascript">
jQuery('#email').closest('tr').removeClass('form-required').find('.description').remove();
// Uncheck send new user email option by default
    <?php if (isset($form_type) && $form_type === 'add-new-user') : ?>
jQuery('#send_user_notification').removeAttr('checked');
    <?php endif; ?>
</script>
        <?php
}




// 添加自定义列标题
function custom_members_list_columns($columns) {
    $columns['join_date'] = '註冊時間';
    return $columns;
}
add_filter('manage_users_columns', 'custom_members_list_columns');

// 显示加入时间的内容
function custom_members_list_column_content($value, $column_name, $user_id) {
    if ($column_name === 'join_date') {
        $user = get_userdata($user_id);
        $join_date = $user->user_registered;
        return date('Y-m-d H:i:s', strtotime($join_date));
    }
    return $value;
}
add_action('manage_users_custom_column', 'custom_members_list_column_content', 10, 3);







function check_user_phone_auth()
{
    if (!is_user_logged_in()) {
        return;
    }

    if (current_user_can('administrator') ) {
        return;
    }

    global $post;
    $slug = $post->post_name;

    if (!isset($slug)) {
        return;
    }
    $user_id = get_current_user_id(); 
    $login_check = get_user_meta($user_id, 'summost_phone_auth', 1);
    
    if ($login_check) {
        return;
    }

    if ($slug!=="phone-auth") {
        echo '<script>window.location.href = "/phone-auth/";</script>';
        return;
    }

}

   add_action('wp_footer', 'check_user_phone_auth', 10);

function enqueue_slick_scripts()
{
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
}
    
    add_action('wp_enqueue_scripts', 'enqueue_slick_scripts');
    





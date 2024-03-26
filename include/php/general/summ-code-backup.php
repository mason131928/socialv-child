<?php

//fix 嘉義女兒節活動
function add_media2() {


//     $storage = mpp_local_storage();
// //     error_log( print_r( 'get_upload_dir', true) );

// // error_log( print_r(    $storage->get_upload_dir(array()), true) );
// $media_id = 16013; // Replace 123 with the actual ID

// $media_post = get_post($media_id);

// error_log( print_r(   $media_post, true) );

    // $default = array(
    //     "title" => "class",
    //     "description" => "",
    //     "gallery_id" => 15909,
    //     "user_id" => 2,
    //     "is_remote" => false,
    //     "type" => "photo",
    //     "mime_type" => "image/jpeg",
    //     "src" => "/Users/lucas/Local Sites/gooddeeddao/app/public/wp-content/uploads/2024/02/class.png",
    //     "url" => "http://gooddeeddao.local/wp-content/uploads/2024/02/class.png",
    //     "status" => "public",
    //     "comment_status" => "open",
    //     "storage_method" => "local",
    //     "component_id" => 2,
    //     "component" => "members",
    //     "context" => "activity",
    //     "is_orphan" => 1
    // );
    
   // mpp_add_media($default);

    
//     error_log( print_r('add_media2', true) );
//     error_log( print_r('mpp_get_wall_gallery_id', true) );

//  	$gallery_id = mpp_get_wall_gallery_id( array(
// 		'component'		=> 'members',
// 		'component_id'	=> 2,
// 		'media_type'	=> 'photo',
// 	) );
//     error_log( print_r('gallery_id', true) );

//     error_log( print_r($gallery_id, true) );
//     error_log( print_r(    mpp_get_wall_gallery_id($gallery_id), true) );



// $old_file_path = '/Users/lucas/Local Sites/gooddeeddao/app/public/wp-content/uploads/2024/02/class.png';

// $new_file_path = '/Users/lucas/Local Sites/gooddeeddao/app/public/wp-content/uploads/mediapress/members/2/16021/class.png';
// // 執行檔案移動
// if (rename($old_file_path, $new_file_path)) {
//     echo "檔案移動成功。";
// } else {
//     echo "檔案移動失敗。";
// }

$user_id=2;
error_log( print_r('_mpp_wall_photo_gallery_id', true) );

$gallery_id=(int) mpp_get_user_meta( $user_id, '_mpp_wall_photo_gallery_id', true );
error_log( print_r($gallery_id, true) );
    
if(0==$gallery_id){
    error_log( print_r('mpp_create_gallery', true) );

    $gallery_id=mpp_create_gallery( array(
        'creator_id'	=> 2,
        'title'			=> 'Wall photo Gallery',
        'description'	=> '',
        'status'		=> 'public',
        'component'		=> 'members',
        'component_id'	=> 2,
    ) );
    error_log( print_r( $gallery_id, true) );

    mpp_update_user_meta( 2, '_mpp_wall_photo_gallery_id', $gallery_id );
    error_log( print_r( $gallery_id, true) );

}

error_log( print_r($gallery_id, true) );


error_log( print_r('wordpress file  path', true) );
$upload_dir = wp_upload_dir();
$upload_dir=$upload_dir['basedir'];
error_log( print_r($upload_dir, true) );



 $mpp_folder_path = $upload_dir.'/mediapress/members/'.$user_id.'/'.$gallery_id;
 if (!file_exists($mpp_folder_path)) {
    // Create the folder if it doesn't exist
    if (!mkdir($mpp_folder_path, 0755, true)) {
        // Failed to create directory
        die('Failed to create folder...');
    }
    echo 'Folder created successfully.';
} else {
    echo 'Folder already exists.';
}
$attachment_id = 16030; // 替換為實際的附件 ID

// 使用附件 ID 取得附件的 metadata
$attachment_metadata = wp_get_attachment_metadata($attachment_id);
error_log( print_r($attachment_metadata, true) );
$file_name = basename($attachment_metadata['file']);
error_log( print_r($file_name, true) );
$image_path = get_attached_file($attachment_id);

// 获取文件名
$image_filename = basename($image_path);

// 获取文件路径（不包括文件名）
$image_directory = dirname($image_path);
error_log( print_r($image_filename, true) );
error_log( print_r($image_directory, true) );


// 檢查是否有 metadata 返回，以及是否包含圖片尺寸資訊
if ($attachment_metadata && isset($attachment_metadata['sizes'])) {
    // 迭代所有的圖片尺寸
    foreach ($attachment_metadata['sizes'] as $size => $data) {
        $log_message = "縮圖尺寸：$size\n";
        $log_message .= "縮圖寬度：{$data['width']}\n";
        $log_message .= "縮圖高度：{$data['height']}\n";
        $log_message .= "縮圖檔案：{$data['file']}\n";
        $log_message .= "------------------------------------------\n";

        // 寫入到錯誤日誌中
        error_log($log_message);
    }
} else {
    $log_message = "找不到附件 {$attachment_id} 的相關資訊。\n";
    // 寫入到錯誤日誌中
    error_log( print_r($log_message, true) );

}
}
//add_action( 'wp_footer', 'add_media2' );




function add_media() {
/*check whether join or not*/
$achievement_id='11072';

?>
<script>
var expirationDate = new Date();
expirationDate.setFullYear(expirationDate.getFullYear() + 1);
var expirationDate2 = new Date();
expirationDate2.setTime(expirationDate2.getTime() + 60 * 60 * 1000); // 60 minutes * 60 seconds * 1000 milliseconds
document.cookie = 'achievement_id='+'<?php echo $achievement_id; ?>'+'; expires=' + expirationDate2.toUTCString() + '; path=/';

</script>
<?php



// 设置查询参数
$args = array(
    'date_query' => array(
        array(
            'column'    => 'user_registered', // 指定查询的字段为注册日期
            'after'     => '2024-01-23',       // 你想要的日期
            'inclusive' => true,
        ),
    ),
);

// 创建用户查询对象
$user_query = new WP_User_Query($args);

// 获取符合条件的用户
$users = $user_query->get_results();

// 检查是否有匹配的用户
if (!empty($users)) {

    foreach ($users as $user) {
    
$achievement_id＿earned_count = gamipress_get_earnings_count(
    array(
        'user_id'        => $user->ID,
        'achievement_id' => '11072',
    )
);
/* if already join change change achievement id, don't award*/
if ( $achievement_id＿earned_count <1) {




    gamipress_award_achievement_to_user( $_COOKIE['achievement_id'], $user->ID );
    global $wpdb;
    $table_name = 'wp_gamipress_user_earnings';
    $achievement_id= $_COOKIE['achievement_id'];
    $user_registered_date = date('Y-m-d H:i:s', strtotime($user->user_registered));
    
    // Use proper SQL syntax for the update query
    $query = $wpdb->prepare(
        "UPDATE $table_name SET date = %s WHERE user_id = %d AND post_id = %d ORDER BY user_earning_id DESC LIMIT 1",
        $user_registered_date,
        $user->ID,
        $achievement_id
    );
    //error_log( print_r($query, true) );
    
    // Run the query
    $result = $wpdb->query($query);
    
    
    $query      = "SELECT * FROM $table_name where user_id = $user->ID and post_id = $achievement_id ORDER BY user_earning_id DESC LIMIT 1";
    $result     = $wpdb->get_results( $query );
    
    
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
                        'achievement_id'              => '11072',
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
                        'location' 					   => 'QR-CODE',
    
    
                    );
                    add_summ_gamipress_log_extra_data($args);
    
    




}


        
}
} else {
    echo '未找到符合条件的用户。';
}



}
     

	
//add_action( 'get_sidebar', 'add_media' );



function fix_tree_hug_activty_id_and_log() {


    global $wpdb;

    // 查询 summ_gamipress_log_extra_data 表中的所有记录
    $log_data = $wpdb->get_results("SELECT * FROM wp_gamipress_user_earnings where post_id = '9968'");

    foreach ($log_data as $log_entry) {
        // 获取 image_id 列的值
        $date = $log_entry->date;
        $user_earning_id = $log_entry->user_earning_id;

            $wpdb->update(
                'summ_gamipress_log_extra_data',
                array('datetime' => $date),
                array('user_earning_id' => $user_earning_id),
            );


            $formatted_date=summ_gamipress_time_to_buddypress($date);
            $formatted_date2=$formatted_date->format('Y-m-d H:i:s');

        $arg=array(
            'id'                => false,                  // Pass an existing activity ID to update an existing entry.
            'action'            => '',                     // The activity action - e.g. "Jon Doe posted an update".
            'content'           => '',                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
            'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
            'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
            'primary_link'      => '',                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
            'user_id'           => $log_entry->user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
            'item_id'           => '12',                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
            'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
            'recorded_time'     => $formatted_date2, // The GMT time that this activity was recorded.
            //'recorded_time'     => bp_core_current_time(), // The GMT time that this activity was recorded.
            'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
            'is_spam'           => false,                  // Is this activity item to be marked as spam?
            'error_type'        => 'bool',
        );
	$activity_id = bp_activity_add($arg);
    //error_log( print_r($activity_id, true) );

        if ($activity_id) {
            // 更新 summ_gamipress_log_extra_data 表中的 activity_id 列
            $wpdb->update(
                'summ_gamipress_log_extra_data',
                array('activity_id' => $activity_id),
                array('user_earning_id' => $log_entry->user_earning_id),
            );
        }
    }

	
}
//add_action( 'the_post', 'fix_tree_hug_activty_id_and_log' );






function activity_change_to_group() {




    global $wpdb;

    // 查询 summ_gamipress_log_extra_data 表中的所有记录
    $log_data = $wpdb->get_results("SELECT * FROM summ_gamipress_log_extra_data where activity_id <> 0");

    foreach ($log_data as $log_entry) {
        //error_log( print_r($log_entry->activity_id, true) );

        $achievement_id_array = array('9968', '11072','11384','11385','9969','9970');
        if (in_array($log_entry->achievement_id, $achievement_id_array)) {

            $log_data_time = $wpdb->get_results("SELECT * FROM wp_gamipress_user_earnings WHERE user_earning_id='$log_entry->user_earning_id' ");
            $time=$log_data_time[0]->date;
            error_log( print_r($time, true) );


        $arg=array(
            'id'                => $log_entry->activity_id,                  // Pass an existing activity ID to update an existing entry.
          // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
            'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
            'user_id'           => $log_entry->user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.

            'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
            'item_id'           => 12,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
            'recorded_time'     =>  $time, // The GMT time that this activity was recorded.
            'error_type'        => 'bool',
        );
        bp_activity_add($arg);
        
        $wpdb->query("UPDATE summ_gamipress_log_extra_data SET datetime = '$time' WHERE activity_id = '$log_entry->activity_id'");

        groups_join_group(12,$log_entry->user_id);

        }
    }
     }

	

//add_action( 'wp_footer', 'activity_change_to_group' );



//temprator code for future using
function create_tree_hug_activty() {


    global $wpdb;

    // 查询 summ_gamipress_log_extra_data 表中的所有记录
    $log_data = $wpdb->get_results("SELECT * FROM summ_gamipress_log_extra_data where pictures = 'https://gooddeeddao.com/wp-content/uploads/activity-pics/tree-hug-357/group-photo.jpeg'");

    foreach ($log_data as $log_entry) {
        // 获取 image_id 列的值
        $image_id = $log_entry->pictures;
$fullDate=$log_entry->year.'-'.$log_entry->month.'-'.$log_entry->day.' 00:00:00';
$formatted_date = date('Y-m-d H:i:s', strtotime($fullDate));
        $arg=array(
            'id'                => false,                  // Pass an existing activity ID to update an existing entry.
            'action'            => '',                     // The activity action - e.g. "Jon Doe posted an update".
            'content'           => '',                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
            'component'         => 'activity',                  // The name/ID of the component e.g. groups, profile, mycomponent.
            'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
            'primary_link'      => '',                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
            'user_id'           => $log_entry->user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
            'item_id'           => false,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
            'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
            'recorded_time'     => $formatted_date, // The GMT time that this activity was recorded.
            //'recorded_time'     => bp_core_current_time(), // The GMT time that this activity was recorded.
            'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
            'is_spam'           => false,                  // Is this activity item to be marked as spam?
            'error_type'        => 'bool',
        );
	$activity_id = bp_activity_add($arg);

        if ($activity_id) {
            // 更新 summ_gamipress_log_extra_data 表中的 activity_id 列
            $wpdb->update(
                'summ_gamipress_log_extra_data',
                array('activity_id' => $activity_id, 'pictures' => NULL),
                array('id' => $log_entry->id),
            );
        }
    }

	
}
//add_action( 'wp_footer', 'create_tree_hug_activty' );


function update_activity_id() {

    global $wpdb;

    // 查询 summ_gamipress_log_extra_data 表中的所有记录
    $log_data = $wpdb->get_results("SELECT * FROM summ_gamipress_log_extra_data");

    foreach ($log_data as $log_entry) {
        // 获取 image_id 列的值
        $image_id = $log_entry->pictures;

        // 查询 wp_bp_activity_meta 表中的记录
        $meta_activity_id = $wpdb->get_var($wpdb->prepare(
            "SELECT activity_id FROM wp_bp_activity_meta WHERE meta_key = '_mpp_attached_media_id' AND meta_value = %d",
            $image_id
        ));

        if ($meta_activity_id) {
            // 更新 summ_gamipress_log_extra_data 表中的 activity_id 列
            $wpdb->update(
                'summ_gamipress_log_extra_data',
                array('activity_id' => $meta_activity_id),
                array('id' => $log_entry->id)
            );
        }
    }
}


 //add_action('wp_footer', 'update_activity_id');


/**
 * Callback function for 'mpp_media_deleted' action.
 *
 * @param int $gallery_id The ID of the gallery.
 */
function change_imagelink_to_id( ) {

    global $wpdb;
   
    // 獲取 WordPress 媒體庫中的所有圖片連結
    $media_links = $wpdb->get_col("SELECT guid FROM $wpdb->posts WHERE post_type = 'attachment'");
    
    // 獲取 summ_gamipress_log_extra_data 表中的所有記錄
    $log_data = $wpdb->get_results("SELECT * FROM summ_gamipress_log_extra_data");
    
    // 遍歷每一條記錄
    foreach ($log_data as $log_entry) {
        $original_picture_link = $log_entry->pictures;
    
        // 遍歷新增的圖片連結和對應的媒體 ID
        foreach ($new_picture_links as $new_link => $new_media_id) {
            // 比較圖片連結是否匹配
            if ($original_picture_link === $new_link) {
                // 更新 summ_gamipress_log_extra_data 表中的 pictures 欄位
                $wpdb->update(
                    'summ_gamipress_log_extra_data',
                    array('pictures' => $new_media_id),
                    array('id' => $log_entry->id)
                );
    
                break; // 當找到匹配時，停止遍歷新增的圖片連結
            }
        }
    
        // 遍歷 WordPress 媒體庫中的每個圖片連結
        foreach ($media_links as $media_link) {
            // 比較圖片連結是否匹配
            if ($original_picture_link === $media_link) {
                // 更新 summ_gamipress_log_extra_data 表中的 pictures 欄位
                $wpdb->update(
                    'summ_gamipress_log_extra_data',
                    array('pictures' => $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $media_link))),
                    array('id' => $log_entry->id)
                );
    
                break; // 當找到匹配時，停止遍歷 WordPress 媒體庫中的其他連結
            }
        }
    }
    
	
}

 //add_action('wp_footer', 'change_imagelink_to_id');





function update_user_id()
{

    global $wpdb;

    // Define table names
    $table_log_extra_data ='summ_gamipress_log_extra_data';
    $table_user_earnings = 'wp_users';
    
    // Get user_earning_id values from summ_gamipress_log_extra_data
    $phone_numbers = $wpdb->get_col("SELECT DISTINCT phone_number FROM $table_log_extra_data");
    
    // Update achievement_id in summ_gamipress_log_extra_data based on user_earning_id
    foreach ($phone_numbers as $phone_number) {
        // Get post_id from wp_gamipress_user_earnings
        $user_id = $wpdb->get_var("SELECT ID FROM $table_user_earnings WHERE user_login = '$phone_number' LIMIT 1");
        // Update summ_gamipress_log_extra_data

        $data_to_update = array(
            'user_id' => $user_id,  // 将 'code' 字段更新为 9968
        );
        
        // 设置 WHERE 子句以匹配特定的 cookie_id
        $where_clause = array(
            'phone_number' => $phone_number,
        );
        
        $wpdb->update($table_log_extra_data, $data_to_update, $where_clause);



    }
    }


  
//add_action('wp_footer', 'update_user_id');







function update_achievement_id()
{

    global $wpdb;

    // Define table names
    $table_log_extra_data ='summ_gamipress_log_extra_data';
    $table_user_earnings = 'wp_gamipress_user_earnings';
    
    // Get user_earning_id values from summ_gamipress_log_extra_data
    $user_earning_ids = $wpdb->get_col("SELECT DISTINCT user_earning_id FROM $table_log_extra_data");
    
    // Update achievement_id in summ_gamipress_log_extra_data based on user_earning_id
    foreach ($user_earning_ids as $user_earning_id) {
        // Get post_id from wp_gamipress_user_earnings
        $post_id = $wpdb->get_var("SELECT post_id FROM $table_user_earnings WHERE user_earning_id = $user_earning_id LIMIT 1");
        //error_log( print_r($post_id, true) );

        // Update summ_gamipress_log_extra_data

        $data_to_update = array(
            'achievement_id' => $post_id,  // 将 'code' 字段更新为 9968
        );
        
        // 设置 WHERE 子句以匹配特定的 cookie_id
        $where_clause = array(
            'user_earning_id' => $user_earning_id,
        );
        
        $wpdb->update($table_log_extra_data, $data_to_update, $where_clause);



    }
    }


  
//add_action('wp_footer', 'update_achievement_id');








function hello333($gallery_id)
{

    //error_log(print_r($gallery_id, true));
}
    


  
//add_action('mpp_media_deleted', 'hello333', 10, 1);

/*如果其他地方cookie沒顯示，要放在這個檔案內*/ 
// function set_cookie()
// {
// 			unique_cookie();
// }
 //add_action('wp_footer', 'set_cookie');



function hello22($output, $older_date, $newer_date)
{
    
}


add_filter('bp_core_time_since', 'hello22', 10, 3);


function hello3($activities,$r)
{
    // error_log(print_r($activities, true));
    // error_log(print_r(11111, true));

    // error_log(print_r($r, true));

}
add_action('bp_activity_after_delete', 'hello3', 10, 2);



function hello2($user_id, $achievement_id, $trigger, $site_id, $args)
{
    // error_log(print_r('after_log', true));
    // error_log(print_r($user_id, true));
    // error_log(print_r($achievement_id, true));
    // error_log(print_r($trigger, true));

}
    add_action('gamipress_award_achievement', 'hello2', 20, 5);


function media_ids($media_ids)
{
    // error_log(print_r($media_ids, true));
    // error_log(print_r(123, true));


}
    //add_action('mpp_activity_media_marked_attached', 'media_ids');


function content_filter($activity_content)
{
        
    if (isset($_COOKIE['welcome_message'])) {
        // $activity_content= $_COOKIE['welcome_message'].$activity_content;
    }
    return $activity_content;
}
    //add_filter('bp_activity_new_update_content', 'content_filter');



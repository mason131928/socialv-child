<?php
function remove_delete_button_on_activity_oprion( $can_delete, $activity)
{
   // error_log(print_r($can_delete, true));
    if(current_user_can('administrator')) {
        return  $can_delete=1;
    }
    //for all roles now.
    $can_delete=0;
    return $can_delete;
}
    
add_filter('bp_activity_user_can_delete', 'remove_delete_button_on_activity_oprion', 10, 2);


function clean_log_and_revoke_point($media_id)
{

// 设置要删除的 media_id
$media_id_to_delete = $media_id;

global $wpdb;

// 从数据库获取包含指定 media_id 的所有记录

$log_data = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT pictures, id, achievement_id, user_id, user_earning_id, gooddeed_token_granted_total, carbon_token_granted_total FROM summ_gamipress_log_extra_data WHERE pictures = %s",
        $media_id_to_delete
    )
);
// 如果有匹配的记录，执行删除和写回操作
if (!empty($log_data)) {
    foreach ($log_data as $log_row) {
        // 获取原始的 pictures 字段值
                    gamipress_revoke_achievement_to_user($log_row->achievement_id, $log_row->user_id, $log_row->user_earning_id);
                    gamipress_deduct_points_to_user($log_row->user_id, $log_row->carbon_token_granted_total, 'carbon-token');
                    gamipress_deduct_points_to_user($log_row->user_id, $log_row->gooddeed_token_granted_total, 'gooddeed-token');
                    set_delete_message($log_row->achievement_id,$log_row->user_id);
                $wpdb->query(
                    $wpdb->prepare(
                        "DELETE FROM `summ_gamipress_log_extra_data` WHERE `id` = %d",
                        $log_row->id
                    )
                );            
             }
            



        
    }
}






 add_action('mpp_before_media_delete', 'clean_log_and_revoke_point', 10, 1);





 
 //文章刪除連動刪除圖片
 function before_activty_delete( $args){
    $activity_id = $args["id"];
    global $wpdb;
    $log_data = $wpdb->get_results("SELECT pictures,user_earning_id,user_id,achievement_id,carbon_token_granted_total,gooddeed_token_granted_total FROM summ_gamipress_log_extra_data where activity_id = '$activity_id' ");
    if(null!==$log_data){
		$media_ids=mpp_activity_get_attached_media_ids($activity_id);
        if(!empty($media_ids));
        update_option('summ_media_ids_waiting_delete_activity_'.$activity_id,$media_ids);
	}
}


add_action( 'bp_before_activity_delete', 'before_activty_delete' );

function after_activty_deleted($activity_ids)
{

    foreach ($activity_ids as $activity_id) {

    $media_ids=get_option('summ_media_ids_waiting_delete_activity_'.$activity_id);
    delete_option('summ_media_ids_waiting_delete_activity_'.$activity_id);

    if(!empty($media_ids)){

    foreach ($media_ids as $media_id) {
         mpp_delete_media($media_id);
        }
    }

    global $wpdb;
	$log_data = $wpdb->get_results("SELECT pictures,user_earning_id,user_id,achievement_id,carbon_token_granted_total,gooddeed_token_granted_total FROM summ_gamipress_log_extra_data where activity_id = '$activity_id' ");
    if(null==$log_data){
		return;
	}
    foreach ($log_data as $log_row) {
        gamipress_revoke_achievement_to_user($log_row->achievement_id, $log_row->user_id, $log_row->user_earning_id);
        gamipress_deduct_points_to_user($log_row->user_id, $log_row->carbon_token_granted_total, 'carbon-token');
        gamipress_deduct_points_to_user($log_row->user_id, $log_row->gooddeed_token_granted_total, 'gooddeed-token');
        set_delete_message($log_row->achievement_id,$log_row->user_id);
}

$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM `summ_gamipress_log_extra_data` WHERE `activity_id` = %d",
        $activity_id
    )
); 

}
}

add_action('bp_activity_deleted_activities', 'after_activty_deleted');




function clean_admin_delete_image_count()
{
        delete_user_meta(get_current_user_id(), 'tree_hug_delete_count');
}
add_action('wp_ajax_clean_admin_delete_image_count', 'clean_admin_delete_image_count');
add_action('wp_ajax_nopriv_clean_admin_delete_image_count', 'clean_admin_delete_image_count');




function set_delete_message($achievement_id,$user_id)
{
    
       if($achievement_id==='9970'){
        $tree_hug_delete_count = get_user_meta( $user_id, 'tree_hug_delete_count', 1 );

        if ( ! empty( $tree_hug_delete_count ) ) {
            ++$tree_hug_delete_count;
        }
        if ( empty( $tree_hug_delete_count ) ) {
            $tree_hug_delete_count = 1;
        }
        update_user_meta( $user_id, 'tree_hug_delete_count', $tree_hug_delete_count );

       }
}



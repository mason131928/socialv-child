<?php


function render_acheivement_image() {

	$media_ids = mpp_activity_get_attached_media_ids( bp_get_activity_id() );

	if ( !empty( $media_ids ) ) {
		return;
	}
	global $wpdb;

	$activity_id = bp_get_activity_id();
	$log_data = $wpdb->get_results("SELECT achievement_id FROM summ_gamipress_log_extra_data where activity_id = '$activity_id' LIMIT 1");
	
	if(null==$log_data){
		return;
	}
	
	if(null==summ_get_feature_image_by_achievement_id($log_data[0]->achievement_id)){
		return;
	}
	$output= summ_get_feature_image_by_achievement_id($log_data[0]->achievement_id);
	echo $output;
}


add_action( 'bp_activity_entry_content', 'render_acheivement_image' );



function render_achievement_description() {

	$output='';

	global $wpdb;

	$activity_id = bp_get_activity_id();
	$log_data = $wpdb->get_results("SELECT achievement_id FROM summ_gamipress_log_extra_data where activity_id = '$activity_id' LIMIT 1");
	
	if(null==$log_data){
		return;
	}
	

	$summ_achievement_description = get_post_meta($log_data[0]->achievement_id, 'summ_achievement_description', true);
	$output.=esc_textarea($summ_achievement_description);
	$output.='<br />';
	echo nl2br($output);
}





add_action( 'bp_activity_entry_content', 'render_achievement_description',1 );

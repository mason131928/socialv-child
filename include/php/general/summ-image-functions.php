<?php
/*functions for db related */

/* Add a log to summ_gamipress_log_extra_data */
function summ_get_feature_image_by_achievement_id($achievement_id){
	if (has_post_thumbnail($achievement_id)) {
		$thumbnail_id = get_post_thumbnail_id($achievement_id);
		$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'full')[0];
		$column_output = '<a href="' . $thumbnail_url . '" target="_blank">';
		$column_output .= '<img src="' . esc_url($thumbnail_url) . '" alt="一張圖片"">';
		$column_output .= '</a>';
		return $column_output;
	} 
	return ;
}


function change_imagelink_form_mediapress_to_default($image_src,$size,$media){

	$source_url = wp_get_attachment_url($media->id);

	return $source_url;
}
add_filter('mpp_get_media_default_cover_image_src','change_imagelink_form_mediapress_to_default',20,3);


//批次匯入時將原先上傳的檔案從liberry傳到medipress資料夾
function move_image_path_from_default_to_medipress_folder($image_src,$size,$media){

	$source_url = wp_get_attachment_url($media->id);

	return $source_url;
}

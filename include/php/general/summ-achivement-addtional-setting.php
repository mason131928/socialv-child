<?php

/**
 * Add custom meta box for pin group to top
 */
function add_achievement_slug_to_group() {
    add_meta_box(
        'summ_achievement_slug_to_group',
        '請輸入徽章Slug',
        'summ_achievement_slug_to_group_callback',
		get_current_screen()->id,
        'advanced',
        'high'
    );
}
add_action('bp_groups_admin_meta_boxes', 'add_achievement_slug_to_group');



/**
 * Callback function to render the content of the custom meta box
 */
function summ_achievement_slug_to_group_callback($post) {

	$group_id = intval( $_GET['gid'] );
	$summ_achievement_slug_to_group =  groups_get_groupmeta( $group_id, 'summ_achievement_slug_to_group' );

    ?>
    <label for="summ_achievement_slug_to_group">
    <input type="text" id="summ_achievement_slug_to_group" name="summ_achievement_slug_to_group" value="<?php echo $summ_achievement_slug_to_group; ?>">
        輸入徽章設定的Slug(若有，必須輸入)
    </label>
    <?php
}

/**
 * Save pin to top value
 */
add_action( 'bp_group_admin_edit_after', 'summ_achievement_slug_to_group_save_meta' );
function summ_achievement_slug_to_group_save_meta( $group_id ) {
	$summ_achievement_slug_to_group =  $_POST['summ_achievement_slug_to_group'] ;
	groups_update_groupmeta( $group_id, 'summ_achievement_slug_to_group', $summ_achievement_slug_to_group );
}



// 在 admin_menu 頁面新增 meta box
add_action('admin_menu', 'add_achievement_event_title_meta_box');

function add_achievement_event_title_meta_box() {
    $achivement_slug_array = gamipress_get_achievement_types_slugs();
    foreach ($achivement_slug_array as $key => $value) {
    //徽章活動標題
    add_meta_box(
        'achievement-event-title-meta-box',
        'Achievement Event Title',
        'render_achievement_event_title_meta_box',
        $value,  // 可以改為其他 post types 如 page, custom_post_type, 等等
        'normal',
        'high'
    );
    //徽章文字敘述
    add_meta_box(
        'achievement-description',
        'Achievement Description',
        'render_achievement_description_title_meta_box',
        $value,  // 可以改為其他 post types 如 page, custom_post_type, 等等
        'normal',
        'high'
    );
}
}

// 渲染 徽章活動標題 meta box 內容
function render_achievement_event_title_meta_box($post) {
    // 取得已儲存的值
    $achievement_event_title_value = get_post_meta($post->ID, '_achievement_event_title_value', true);

    // 輸出表單
    ?>
    <label for="achievement-event-title">請輸入活動標題：</label>
    <input type="text" id="achievement-event-title" name="achievement_event_title" value="<?php echo esc_attr($achievement_event_title_value); ?>" style="width: 100%;" />
    <?php
}


// 渲染Achievement Description  meta box 內容
function render_achievement_description_title_meta_box($post) {
    // 取得已儲存的值
    $summ_achievement_description = get_post_meta($post->ID, 'summ_achievement_description', true);

    // 輸出表單
    ?>
    <label for="summ_achievement_description">請輸入活動敘述：</label>
    <textarea id="summ_achievement_description" name="summ_achievement_description" rows="5" style="width: 100%;" /><?php echo esc_textarea($summ_achievement_description); ?></textarea>
    <?php
}

// 儲存 meta box 輸入值
add_action('save_post', 'save_achievement_event_title_meta_box');

function save_achievement_event_title_meta_box($post_id) {

    $achievement_event_title = isset($_POST['achievement_event_title']) ? sanitize_text_field($_POST['achievement_event_title']) : '';
    update_post_meta($post_id, '_achievement_event_title_value', $achievement_event_title);

    $summ_achievement_description = isset($_POST['summ_achievement_description']) ? wp_kses_post($_POST['summ_achievement_description']) : '';
    update_post_meta($post_id, 'summ_achievement_description', $summ_achievement_description);
}

// 加入 nonce 以確保安全性
add_action('post_submitbox_misc_actions', 'add_achievement_event_title_meta_box_nonce');

function add_achievement_event_title_meta_box_nonce() {
    wp_nonce_field('achievement_event_title_nonce', 'achievement_event_title_meta_box_nonce');
}


//addtional custom field for group 


function gamipress_achievement_type_meta_boxes1()
{

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_1';

    // Achievement Type Data
    gamipress_add_meta_box(
        'achievement-type-data2',
        __('Achievement Type Data2', 'gamipress'),
        'achievement-type',
        array(
        'post_title' => array(
        'name'     => __('Singular Name', 'gamipress'),
        'desc'     => __('The singular name for this achievement type.', 'gamipress'),
        'type'     => 'text_medium',
        ),
        $prefix . 'plural_name' => array(
        'name'     => __('Plural Name', 'gamipress'),
        'desc'     => __('The plural name for this achievement type.', 'gamipress'),
        'type'     => 'text_medium',
        ),
        'post_name' => array(
        'name'     => __('Slug', 'gamipress'),
        'desc'     => '<span class="gamipress-permalink hide-if-no-js">' . site_url() . '/<strong class="gamipress-post-name"></strong>/</span><br>' . __('Slug is used for internal references, as some shortcode attributes, to completely differentiate this achievement type from any other (leave blank to automatically generate one).', 'gamipress'),
        'type'     => 'text_medium',
        'attributes' => array(
            'maxlength' => 20
        )
        ),
        ),
        array( 'priority' => 'high', )
    );

}
    //add_action('gamipress_init_achievement-type_meta_boxes', 'gamipress_achievement_type_meta_boxes1');



    function add_addtional_achievement_meta_boxes()
    {
    
        // Start with an underscore to hide fields from custom fields list
        $prefix = '_summ_';
    
    
        // Achievement Type Data
        gamipress_add_meta_box(
            'summ-addtional-data',
            __('額外資訊', 'gamipress'),
            'achievement-type',
            array(
             $prefix . 'group_id' => array(
            'name'     => __('所屬團體id', 'gamipress'),
            'desc'     => __('必須輸入，所屬團體的id', 'gamipress'),
            'type'     => 'text_medium',
            )
            ),
            array( 'priority' => 'high', )
        );
    
    }
    //add_action('gamipress_init_achievement-type_meta_boxes', 'add_addtional_achievement_meta_boxes');
    
    
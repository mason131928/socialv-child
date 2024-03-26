<?php
use function SocialV\Utility\socialv;

/**
 * Add custom meta box for pin group to top
 */
function pin_to_top_meta_box() {
    add_meta_box(
        'pin_to_top_meta_box',
        'Pin Group',
        'pin_to_top_meta_box_callback',
		get_current_screen()->id,
        'side',
        'core'
    );
}
add_action('bp_groups_admin_meta_boxes', 'pin_to_top_meta_box');

/**
 * Callback function to render the content of the custom meta box
 */
function pin_to_top_meta_box_callback($post) {

	$group_id = intval( $_GET['gid'] );
	$pin_to_top_data = intval( groups_get_groupmeta( $group_id, 'pin_to_top_data' ) );

    ?>
    <label for="pin_to_top_data">
        <input type="checkbox" id="pin_to_top_data" name="pin_to_top_data" value="1" <?php checked($pin_to_top_data); ?>>
        Pin
    </label>
    <?php
}

/**
 * Save pin to top value
 */
add_action( 'bp_group_admin_edit_after', 'pin_to_top_data_save_meta' );
function pin_to_top_data_save_meta( $group_id ) {
	$pin_to_top_data = intval( $_POST['pin_to_top_data'] );
	groups_update_groupmeta( $group_id, 'pin_to_top_data', $pin_to_top_data );
}

/**
 * Exclide pinned posts from all group filters
 */
function exclude_pinned_groups( $args ) {

	 //prevent this from affecting the wp_admin
	if ( is_admin() ) {
        return $args; 
    }

	// Fetch groups where pin_to_top_data is set to 1
	$pinned_groups_args = array(
        'meta_query' => array(
            array(
                'key'     => 'pin_to_top_data',
                'value'   => '1',
                'compare' => '=',
                'type'    => 'NUMERIC',
            ),
        ),
        'fields'     => 'ids',
    );

    // Add your group IDs to exclude
    $excluded_group_id = groups_get_groups($pinned_groups_args)['groups'];

    // Check if 'exclude' parameter is already set, if not, initialize it as an array
    if ( ! isset( $args['exclude'] ) ) {
        $args['exclude'] = array();
    }

    // Add the group ID to the exclude array
    $args['exclude'] = $excluded_group_id;

    return $args;
}

// add_filter( 'bp_before_has_groups_parse_args', 'exclude_pinned_groups' );


function show_pinned(){

	//to prevent the pinned group filter from taking effect
	remove_filter('bp_before_has_groups_parse_args', 'exclude_pinned_groups');

	$pinned_groups_args = array(
        'meta_query' => array(
            array(
                'key'     => 'pin_to_top_data',
                'value'   => '1',
                'compare' => '=',
                'type'    => 'NUMERIC', 
            ),
        ),

    );
	if ( bp_has_groups ( $pinned_groups_args ) ) { 

		// do_action('bp_before_directory_groups_list');
		echo ' <div id="groups-list" class="socialv-groups-lists socialv-bp-main-box row" style="background-color: #ebebeb; padding-top: 1em; padding-bottom: 0em; margin-bottom: 1em; ">';
		echo '<p> Pinned Groups </p>';
		while ( bp_groups() ) : bp_the_group(); ?>

		<div <?php bp_group_class(array('item-entry col-md-6 d-flex flex-column')); ?>>
			<div class="socialv-card socialv-group-info h-100">
				<div class="top-bg-image">
					<?php echo socialv()->socialv_group_banner_img(bp_get_group_id(), 'groups'); ?>
					<?php if (bp_get_group_status() == 'private') {
						echo '<div class="status"><i class="iconly-Lock icli"></i></div>';
					} ?>
				</div>
				<div class="text-center">
					<div class="group-header">
						<?php if (!bp_disable_group_avatar_uploads()) : ?>
							<div class="group-icon">
								<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar('width=90&height=90&class=rounded'); ?></a>
							</div>
						<?php endif; ?>
						<div class="group-name">
							<h5 class="title"><?php bp_group_link(); ?></h5>
						</div>
					</div>
					<div class="socialv-group-details d-inline-block">
						<ul class="list-inline">
							<li class="d-inline-block">
								<a href="<?php bp_group_permalink(); ?>"><span class="post-icon"><i class="iconly-Paper icli"></i></span><span class="item-number"><?php echo socialv()->socialv_group_posts_count(bp_get_group_id()); ?></span><span class="item-text"><?php echo ((socialv()->socialv_group_posts_count(bp_get_group_id()) == 1) ? esc_html__('Post', 'socialv') : esc_html__('Posts', 'socialv')); ?></span></a>
							</li>
							<li class="d-inline-block">
								<a href="<?php bp_group_permalink(); ?>members">
									<span class="member-icon"><i class="iconly-User2 icli"></i></span>
									<span class="item-text">
										<?php
										echo ((bp_get_group_total_members(false) == 1) ? esc_html__('Member', 'socialv') : esc_html__('Members', 'socialv'));
										?>
									</span>

									<span class="item-number"><?php echo bp_get_group_total_members(false); ?></span>
								</a>
							</li>
						</ul>
					</div>

					<ul class="group-member member-thumb list-inline list-img-group">
						<?php
						$total_members = BP_Groups_Group::get_total_member_count(bp_get_group_id());
						if ($total_members == 1) {
							echo '<li><span>' . esc_html_e('No Members', 'socialv') . '</span></li>';
						} else {
							if (bp_group_has_members('group_id=' . bp_get_group_id() . '&per_page=4&exclude_admins_mods=false')) : ?>
								<?php while (bp_group_members()) : bp_group_the_member(); ?>
									<li><a href="<?php bp_member_permalink(); ?>"><?php bp_group_member_avatar_thumb(); ?></a></li>
								<?php endwhile; ?>
								<li><a href="<?php bp_group_permalink(); ?>members"><i class="icon-add"></i></a></li>
						<?php endif;
						} ?>
					</ul>
					<?php
					do_action('bp_directory_groups_item');
					if (groups_is_user_admin(get_current_user_id(), bp_get_group_id())) {
						echo ((count(groups_get_group_admins(bp_get_group_id())) > 1) ? '<div class="group-admin-main-button">' : '');
					}
					do_action('bp_directory_groups_actions');
					if (groups_is_user_admin(get_current_user_id(), bp_get_group_id())) {
						echo ((count(groups_get_group_admins(bp_get_group_id())) > 1) ? '</div>' : '');
					}
					?>
				</div>
			</div>
		</div>
		<?php
					endwhile;
					echo '</div> ';
		//return the pinned group filter
		add_filter( 'bp_before_has_groups_parse_args', 'exclude_pinned_groups' );
	} 
	
}

//updated post count meta when a post is added
function add_group_post($activity){


	if ( $activity->type === 'activity_update') {

		$new_meta_value = get_total_group_posts($activity->item_id);

		update_group_meta_data($activity->item_id, $new_meta_value);
	}	
	
}
add_action('bp_activity_after_save', 'add_group_post', 10, 1);


//update post count meta when a post is deleted
function delete_group_post($activity){
	// get item id from the activity
	$activity = new BP_Activity_Activity($activity['id']);
	$group_id = $activity->item_id;
	if ( $activity->type === 'activity_update') {

		$new_meta_value = get_total_group_posts($group_id) - 1;
		
		update_group_meta_data($group_id, $new_meta_value);
	}

}
add_action('bp_before_activity_delete', 'delete_group_post');



function update_group_meta_data($group_id,$new_meta_value) {
	groups_update_groupmeta($group_id, 'total_posts_count', $new_meta_value);
}

function get_total_group_posts($group_id){
	global $wpdb;

	$group_id = $group_id;

	$query = $wpdb->prepare(
		"SELECT COUNT(*) 
		FROM {$wpdb->prefix}bp_activity 
		WHERE component = 'groups' 
		AND item_id = %d 
		AND type = 'activity_update'",
		$group_id
	);

	$post_count = $wpdb->get_var($query);

	return $post_count;
}


function custom_groups_filter() {
	?>
	<option value="custom_posts_count"><?php _e('Sort by Posts Count', 'textdomain'); ?></option>
	<?php
}
add_action('bp_groups_directory_order_options', 'custom_groups_filter');


function apply_custom_sorting($args) {
	// if (isset($_REQUEST['groups_orderby']) && 'custom_posts_count' === $_REQUEST['groups_orderby']) {
	
		$args['meta_key'] = 'total_posts_count';
		$args['orderby']   = 'meta_value_num';
		$args['order']     = 'DESC';

		return $args;
	// }
}
add_action('bp_after_has_groups_parse_args', 'apply_custom_sorting');
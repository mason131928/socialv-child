<?php
/**
 * BuddyPress - Groups Admin - Group Settings
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<h2 class="bp-screen-reader-text"><?php esc_html_e( 'Manage Group Settings', 'socialv' ); ?></h2>

<?php

/**
 * Fires before the group settings admin display.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_group_settings_admin' ); ?>

<fieldset class="group-create-privacy">

	<h4 class="socialv-setting-title"><?php esc_html_e( 'Privacy Options', 'socialv' ); ?></h4>

	<div class="radio">
		<div class="radio-data-box">
			<label for="group-status-public"><input type="radio" name="group-status" id="group-status-public" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="public-group-description" /> <?php esc_html_e( 'This is a public group', 'socialv' ); ?></label>

			<ul id="public-group-description" class="socialv-group-data mb-0">
				<li><?php esc_html_e( 'Any site member can join this group.', 'socialv' ); ?></li>
				<li><?php esc_html_e( 'This group will be listed in the groups directory and in search results.', 'socialv' ); ?></li>
				<li><?php esc_html_e( 'Group content and activity will be visible to any site member.', 'socialv' ); ?></li>
			</ul>
		</div>

		<div class="radio-data-box">
			<label for="group-status-private"><input type="radio" name="group-status" id="group-status-private" value="private"<?php if ( 'private' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="private-group-description" /> <?php esc_html_e( 'This is a private group', 'socialv' ); ?></label>

			<ul id="private-group-description" class="socialv-group-data mb-0">
				<li><?php esc_html_e( 'Only users who request membership and are accepted can join the group.', 'socialv' ); ?></li>
				<li><?php esc_html_e( 'This group will be listed in the groups directory and in search results.', 'socialv' ); ?></li>
				<li><?php esc_html_e( 'Group content and activity will only be visible to members of the group.', 'socialv' ); ?></li>
			</ul>
		</div>

		<div class="radio-data-box">
			<label for="group-status-hidden"><input type="radio" name="group-status" id="group-status-hidden" value="hidden"<?php if ( 'hidden' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="hidden-group-description" /> <?php esc_html_e('This is a hidden group', 'socialv' ); ?></label>

			<ul id="hidden-group-description" class="socialv-group-data mb-0">
				<li><?php esc_html_e( 'Only users who are invited can join the group.', 'socialv' ); ?></li>
				<li><?php esc_html_e( 'This group will not be listed in the groups directory or search results.', 'socialv' ); ?></li>
				<li><?php esc_html_e( 'Group content and activity will only be visible to members of the group.', 'socialv' ); ?></li>
			</ul>
		</div>

	</div>

</fieldset>

<?php // Group type selection ?>
<?php if ( $group_types = bp_groups_get_group_types( array( 'show_in_create_screen' => true ), 'objects' ) ): ?>

	<fieldset class="group-create-types">
		<h4><?php esc_html_e( 'Group Types', 'socialv' ); ?></h4>

		<p><?php esc_html_e( 'Select the types this group should be a part of.', 'socialv' ); ?></p>

		<?php foreach ( $group_types as $type ) : ?>
			<div class="checkbox">
				<label for="<?php printf( 'group-type-%s', $type->name ); ?>">
					<input type="checkbox" name="group-types[]" id="<?php printf( 'group-type-%s', $type->name ); ?>" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( bp_groups_has_group_type( bp_get_current_group_id(), $type->name ) ); ?>/> <?php echo esc_html( $type->labels['name'] ); ?>
					<?php
						if ( ! empty( $type->description ) ) {
							printf( __( '&ndash; %s', 'socialv' ), '<span class="bp-group-type-desc">' . esc_html( $type->description ) . '</span>' );
						}
					?>
				</label>
			</div>

		<?php endforeach; ?>

	</fieldset>

<?php endif; ?>


<?php if ( bp_is_active( 'groups', 'invitations' ) ): ?>

	<fieldset class="group-create-invitations">

		<h4><?php esc_html_e( 'Group Invitations', 'socialv' ); ?></h4>

		<p><?php esc_html_e( 'Which members of this group are allowed to invite others?', 'socialv' ); ?></p>

		<div class="radio invitations-list">
			<div class="radio-data-box">
				<label for="group-invite-status-members">
					<input type="radio" name="group-invite-status" id="group-invite-status-members" value="members"<?php bp_group_show_invite_status_setting( 'members' ); ?> /> <?php esc_html_e( 'All group members', 'socialv' ); ?>
				</label>
			</div>

			<div class="radio-data-box">
				<label for="group-invite-status-mods"><input type="radio" name="group-invite-status" id="group-invite-status-mods" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ); ?> /> <?php esc_html_e( 'Group admins and mods only', 'socialv' ); ?></label>
			</div>
			<div class="radio-data-box">
				<label for="group-invite-status-admins"><input type="radio" name="group-invite-status" id="group-invite-status-admins" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ); ?> /> <?php esc_html_e( 'Group admins only', 'socialv' ); ?></label>
			</div>
		</div>

	</fieldset>

<?php endif; ?>

<?php

/**
 * Fires after the group settings admin display.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_group_settings_admin' ); ?>
<div class="form-edit-btn">
	<div class="submit">
		<input type="submit" value="<?php esc_attr_e( 'Save Changes', 'socialv' ); ?>" id="save" class="btn socialv-btn-success" name="save" />
	</div>
</div>
<?php wp_nonce_field( 'groups_edit_group_settings' );

<?php

/**
 * BuddyPress - Members Single Profile
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

 $current_user = wp_get_current_user();
 $account_has_changed = get_user_meta($current_user->ID, 'summ_account_has_changed', true);
 $auth_phone_array = get_user_meta($current_user->ID, 'summ_account_phone_change', true);

 //error_log( print_r($_POST, true) );

?>


<div class="card-inner">
	<?php do_action('bp_before_member_settings_template'); ?>
	<div id="template-notices" role="alert" aria-atomic="true">
		<?php
		do_action('template_notices'); ?>

	</div>
	<div class="card-head card-header-border d-flex align-items-center justify-content-between">
		<div class="head-title">
			<h4 class="card-title"><?php esc_html_e('Account settings', 'socialv'); ?></h4>
		</div>
	</div>
	您的使用者帳號為：<?php echo $current_user->user_login?>

	<?php if (empty($account_has_changed)) : ?>

		<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form1" id="settings-form">
	更改使用者帳號(只可更改一次)<br />
	<input type="text" name="account" id="account" class="form-control" size="16" value="" class="settings-input small password-entry" <?php bp_form_field_attributes('1aaa'); ?> placeholder="<?php esc_attr_e('將取代目前的使用者帳號，包括您的頁面帳號連結以及您登入時可使用手機與此帳號', 'socialv'); ?>" />
	<div class="form-edit-btn">
			<div class="submit">
				<input type="submit" name="account-change" value="<?php esc_attr_e('Save Changes', 'socialv'); ?>" id="submit" class="auto btn socialv-btn-success" />
			</div>
		</div>
		<?php wp_nonce_field('bp_settings_general'); ?>

</form>
	<?php endif; ?>

	<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form1" id="settings-form">

	更改手機號碼<br />
	<input type="text" name="phone" id="phone" pattern="09\d{8}" maxlength="10" class="form-control" size="16" value="" class="settings-input small password-entry" <?php bp_form_field_attributes('dfff'); ?> placeholder="<?php esc_attr_e('將取代目前您登入時使用手機', 'socialv'); ?>" />
	
	<div class="form-edit-btn">
			<div class="submit">
				<input type="submit" name="phone-code-submit" value="<?php esc_attr_e('Save Changes', 'socialv'); ?>" id="submit" class="auto btn socialv-btn-success" />
			</div>
		</div>
		<?php wp_nonce_field('bp_settings_general'); ?>

</form>

<?php if (!empty($auth_phone_array)) : ?>

<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form1" id="settings-form">

	您要驗證的手機號碼為：<?php echo $auth_phone_array['phone']?><br />
	<input type="text" name="auth-code" id="phone"  maxlength="6" class="form-control" size="16" value="" class="settings-input small password-entry" <?php bp_form_field_attributes('dfff'); ?> placeholder="<?php esc_attr_e('請輸入您收到的驗證碼', 'socialv'); ?>" />
	
	<div class="form-edit-btn">
			<div class="submit">
				<input type="submit" name="auth-code-submit" value="<?php esc_attr_e('Save Changes', 'socialv'); ?>" id="submit" class="auto btn socialv-btn-success" />
			</div>
		</div>
		<?php wp_nonce_field('bp_settings_general'); ?>

</form>
<?php endif; ?>


	<br />
	<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form1" id="settings-form">
		<div class="form-floating">
		更改email<br />
			<input type="email" name="email" id="email" class="form-control" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" <?php bp_form_field_attributes('email'); ?> placeholder="<?php esc_attr_e('Account Email', 'socialv'); ?>" />
			<!-- <label for="email"><?php //esc_html_e('Account Email', 'socialv'); ?></label> -->
		</div>
		<div class="form-floating">
			<?php if (!is_super_admin()) : ?>
				<input type="password" name="pwd" id="pwd" class="form-control" size="16" value="" class="settings-input small" <?php bp_form_field_attributes('password'); ?> placeholder="<?php esc_attr_e('Current Password (required to update email or change current password)', 'socialv'); ?>" />
				<label for="pwd"><?php esc_html_e('Current Password (required to update email or change current password)', 'socialv'); ?></label>
			<?php endif; ?>
		</div>
		<div class="form-floating">
			<input type="password" name="pass1" id="pass1" class="form-control" size="16" value="" class="settings-input small password-entry" <?php bp_form_field_attributes('password'); ?> placeholder="<?php esc_attr_e('Change Password (leave blank for no change)', 'socialv'); ?>" />
			<label for="pass1"><?php esc_html_e('Change Password (leave blank for no change)', 'socialv'); ?></label>
		</div>
		<div id="pass-strength-result"></div>
		<div class="form-floating">
			<input type="password" name="pass2" id="pass2" class="form-control" size="16" value="" class="settings-input small password-entry-confirm" <?php bp_form_field_attributes('password'); ?> placeholder="<?php esc_attr_e('Repeat New Password', 'socialv'); ?>" />
			<label for="pass2"><?php esc_html_e('Repeat New Password', 'socialv'); ?></label>
		</div>
		<?php

		/**
		 * Fires before the display of the submit button for user general settings saving.
		 *
		 * @since 1.5.0
		 */
		do_action('bp_core_general_settings_before_submit'); ?>
		<div class="form-edit-btn">
			<div class="submit">
				<input type="submit" name="submit" value="<?php esc_attr_e('Save Changes', 'socialv'); ?>" id="submit" class="auto btn socialv-btn-success" />
			</div>
		</div>

		<?php
		/**
		 * Fires after the display of the submit button for user general settings saving.
		 *
		 * @since 1.5.0
		 */
		do_action('bp_core_general_settings_after_submit'); ?>

		<?php wp_nonce_field('bp_settings_general'); ?>

	</form>



				

</div>
<?php
do_action('bp_after_member_settings_template');

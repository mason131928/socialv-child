<?php

/**
 * BuddyPress - Activity Loop
 *
 * @package    BuddyPress
 * @subpackage bp-legacy
 * @version    3.0.0
 */

/**
 * Fires before the start of the activity loop.
 *
 * @since 1.2.0
 */
do_action('bp_before_activity_loop');

?>

<?php if (bp_has_activities(bp_ajax_querystring('activity'))) : ?>

    <?php if (empty($_POST['page'])) : ?>
        <ul id="activity-stream" class="activity-list  socialv-list-post">
       


    <?php endif; ?>
            
    <?php do_action("socialv_before_activity_loop"); ?>

            
    <?php while (bp_activities()) : bp_the_activity(); ?>

    <?php  
//檢查是否符合filter的徽章編號，不符合時會continue

//echo '徽章編號'.summ_gamipress_log_extra_data_activity_id_with_achievement_id(bp_get_activity_id());


 ?>
        <?php bp_get_template_part('activity/entry'); ?>

    <?php endwhile; ?>
    <?php if (bp_activity_has_more_items()) : ?>
            <li class="load-more">
            <?php if (!isset($_GET['achieved_id'])) : ?>
            <a class="socialv-loader" href="<?php bp_activity_load_more_link() ?>"></a>
            <?php endif; ?>
            <?php if (isset($_GET['achieved_id'])) : ?>
            <a class="socialv-loader" href="<?php  bp_get_activity_load_more_link().'&achieved_id='.$_GET['achieved_id'] ?>"></a>
            <?php endif; ?>

            </li>

    <?php endif; ?>

    <?php if (empty($_POST['page'])) : ?>

        </ul>

    <?php endif; ?>

<?php else : ?>

    <div id="message" class="info">
        <p><?php esc_html_e('Sorry, there was no activity found. Please try a different filter.', 'socialv'); ?></p>
    </div>

<?php endif; ?>

<?php

/**
 * Fires after the finish of the activity loop.
 *
 * @since 1.2.0
 */
do_action('bp_after_activity_loop'); ?>

<?php if (empty($_POST['page'])) : ?>

    <form name="activity-loop-form" id="activity-loop-form" method="post">

    <?php wp_nonce_field('activity_filter', '_wpnonce_activity_filter'); ?>

    </form>

<?php endif;
echo '<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("li.load-more > a").each(function(){
		var href = jQuery(this).attr("href");
		var newHref = href.replace(/&offset_lower=\d+/g, "");
		jQuery(this).attr("href", newHref);
	});
});
</script>';

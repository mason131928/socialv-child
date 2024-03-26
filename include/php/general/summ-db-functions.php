<?php

/*functions for db related */

/* Add a log to summ_gamipress_log_extra_data */
function add_summ_gamipress_log_extra_data($args = array()) {
    if (!empty($args['pictures'])) {
    if (strpos($args['pictures'], ',') !== false) {
        $args['pictures']='multiple';
    }
    }
    global $wpdb;
    $table_name      = 'summ_gamipress_log_extra_data';
    $defaults =  array(
            'user_earning_id'              => 0,
            'achievement_id'              => 0,
            'activity_id'                  => 0,
            'user_id'                      => 0,
            'datetime'                     => bp_core_current_time(),
            'carbon_token_unit'            => 0,
            'carbon_token'                 => 0,
            'carbon_token_granted_once'    => 0,
            'gooddeed_token_unit'          => 0,
            'gooddeed_token'               => 0,
            'gooddeed_token_granted_once'  => 0,
            'completed_numbers'            => 0,
            'carbon_token_granted_total'   => 0,
            'gooddeed_token_granted_total' => 0,
            'pictures'                     => NULL,
            'location' 					   => '',

        );

    $data_to_insert = wp_parse_args($args, $defaults);

    $result = $wpdb->insert( $table_name, $data_to_insert );

    return $result;

}



/* Add a log to summ_gamipress_log_extra_data */
function summ_gamipress_log_extra_data_activity_id_with_achievement_id($activity_id = '', $achievement_id='') {
    global $wpdb;
    $table_name      = 'summ_gamipress_log_extra_data';

    if (!empty($activity_id)) {
        $query = $wpdb->prepare("
        SELECT achievement_id
        FROM $table_name
        WHERE activity_id = %d
    ", $activity_id);
    
    $achievement_id = $wpdb->get_var($query);
    if ($achievement_id !== null) {
        return $achievement_id;
    } else {
        return ;
    }
    }
    


}
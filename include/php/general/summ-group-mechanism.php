<?php
function general_group_menu_item()
{     
        $group_id = bp_get_current_group_id(); 
    if (!$group_id ) {
        return ;
    }

    //error_log(print_r(bp_get_group_permalink(), true));

        $group_slug = bp_get_current_group_slug(); 
    // error_log(print_r($group_slug, true));
    if (!$group_slug ){
        return ;
    }
        
    $bp = buddypress();
    $single_item_component = bp_current_component();
    if (!isset($bp->{$single_item_component}->nav)) {
        return;
    }
    $bp->{$single_item_component}->nav-> delete_nav('send-invites', "$group_slug");
        
    $bp->{$single_item_component}->nav-> delete_nav('media', "$group_slug");



    if('jenjishiang'!==$group_slug) {
        return;
    }


        $secondary_nav_items = $bp->{$single_item_component}->nav->get_secondary(array( 'parent_slug' => "$group_slug" ));    
        //error_log(print_r($secondary_nav_items, true));

        $donate=array(
        'name' => '捐款',
        'link' => 'https://www.jjs.org.tw/civicrm/contribute/transact?reset=1&id=6',
            'slug' => 'donate',
            'parent_slug' =>  $group_slug,
            'css_id' => 'user-points',
            'position' => 60,
            'user_has_access' => 1,
            'no_access_url' => 'http://gooddeeddao.local/groups/wisehelp/',
            'screen_function' => 'groups_screen_group_test',
            'show_in_admin_bar' => '',
            'secondary' => 1
        );
          $donate3=array(
                    'name' => '預約共運',
                    'link' => '/efoodbank-donate/',
                        'slug' => 'donate-list',
                        'parent_slug' =>  $group_slug,
                        'css_id' => 'donate',
                        'position' => 80,
                        'user_has_access' => 1,
                        'no_access_url' => 'http://gooddeeddao.local/groups/wisehelp/',
                        'screen_function' => 'groups_screen_group_test',
                        'show_in_admin_bar' => '',
                        'secondary' => 1
                    );
          $current_group = $bp->groups->current_group;
                
          
        bp_core_new_subnav_item(
            array(
                            'position' => 70,
                            'slug' => 'donate-goods',
                            'name' => __('捐贈物資', 'summ-code'),
                            'parent_slug' => $current_group->slug,
                            'parent_url' => bp_get_group_permalink($current_group),
                            'screen_function' => 'render_donate_goods',
                            'item_css_id' => 'donate-goods',

                        ), 'groups'
        );

                    bp_core_new_subnav_item(
                        array(
                            'position' => 80,
                            'slug' => 'donate-list',
                            'name' => __('物資造冊', 'summ-code'),
                            'parent_slug' => $current_group->slug,
                            'parent_url' => bp_get_group_permalink($current_group),
                            'screen_function' => 'render_donate_list',
                        'user_has_access' => groups_is_user_member(get_current_user_id(), $current_group->id),

                        ), 'groups'
                    );
                $bp->{$single_item_component}->nav-> add_nav($donate);
                $bp->{$single_item_component}->nav-> add_nav($donate3);

}

    add_action('bp_actions', 'general_group_menu_item', 20);




function render_donate_list_content()
{
    ?>
    <form id="donate-list-form" action="" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('excel-upload-nonce', 'excel-upload-nonce'); ?>
    <br /><br />
    <label for="excel-file">選擇 Excel 檔案：</label><br />
    <input type="file" name="excel-file" id="choose-donate-list" accept=".xlsx, .xls, .csv"><br />
    <input type="submit" name="submit-excel" id="upload-donate-list" value="上傳 Excel">
</form>

    <?php
    if (isset($_POST['submit-excel'])) {
        // 處理上傳邏輯

        if (!isset($_POST['excel-upload-nonce']) ||  !wp_verify_nonce($_POST['excel-upload-nonce'], 'excel-upload-nonce') ) {
            return;
        }

        if (isset($_FILES['excel-file']) && $_FILES['excel-file']['error'] == 0) {
            $file_info = wp_handle_upload($_FILES['excel-file'], array('test_form' => false));
    
            if (!isset($file_info['error'])) {
                // 上傳成功，可以進一步處理 Excel 檔案，例如將其匯入資料庫
                // 這裡可以使用第三方函式庫（如 PHPExcel）來處理 Excel
                echo 'Excel 檔案上傳成功！';
    
                // 如果需要將上傳的檔案儲存到媒體庫，可以使用下面的代碼
                $attachment = array(
                    'post_title' => $file_info['file'],
                    'post_content' => '',
                    'post_status' => 'inherit',
                    'post_mime_type' => $file_info['type']
                );
    
                $attach_id = wp_insert_attachment($attachment, $file_info['file']);
                include_once ABSPATH . 'wp-admin/includes/image.php';
                $attach_data = wp_generate_attachment_metadata($attach_id, $file_info['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);



            } else {
                echo '無法上傳 Excel 檔案。';
            }
        }
    }
}
function render_donate_list()
{

    // Call Tab Content.
    add_action('bp_template_content', 'render_donate_list_content');

    // Load Tab Template
    bp_core_load_template('buddypress/groups/single/plugins');

}



function render_donate_goods_content()
{
        $shortcode_output = do_shortcode('[contact-form-7 id="03c9d44" title="捐贈物資"]');

    // 将短代码的输出添加到您的函数中
    $output = "<h2>捐贈表單</h2><br>";
    $output .= $shortcode_output;

    echo $output;
}
function render_donate_goods()
{

    // Call Tab Content.
    add_action('bp_template_content', 'render_donate_goods_content');

    // Load Tab Template
    bp_core_load_template('buddypress/groups/single/plugins');

}


function jenjishiang_group_menu_item()
{     
    $group_slug = bp_get_current_group_slug(); 

    if('jenjishiang'!==$group_slug) {
        return;
    }
    $bp = buddypress();
    $single_item_component = bp_current_component();


        $secondary_nav_items = $bp->{$single_item_component}->nav->get_secondary(array( 'parent_slug' => "$group_slug" ));    
        //error_log(print_r($secondary_nav_items, true));

        $donate=array(
        'name' => '捐款',
        'link' => 'https://www.jjs.org.tw/civicrm/contribute/transact?reset=1&id=6',
            'slug' => 'donate',
            'parent_slug' =>  $group_slug,
            'css_id' => 'user-points',
            'position' => 60,
            'user_has_access' => 1,
            'no_access_url' => 'http://gooddeeddao.local/groups/wisehelp/',
            'screen_function' => 'groups_screen_group_test',
            'show_in_admin_bar' => '',
            'secondary' => 1
        );
          $donate3=array(
                    'name' => '預約共運',
                    'link' => 'http://lovecar.gooddeeddao.com:443/form',
                        'slug' => 'donate-list1',
                        'parent_slug' =>  $group_slug,
                        'css_id' => 'user-points',
                        'position' => 80,
                        'user_has_access' => 1,
                        'no_access_url' => 'http://gooddeeddao.local/groups/wisehelp/',
                        'screen_function' => 'groups_screen_group_test',
                        'show_in_admin_bar' => '',
                        'secondary' => 1
                    );
          $current_group = $bp->groups->current_group;
                
          
        bp_core_new_subnav_item(
            array(
                            'position' => 70,
                            'slug' => 'donate-goods',
                            'name' => __('捐贈物資', 'summ-code'),
                            'parent_slug' => $current_group->slug,
                            'parent_url' => bp_get_group_permalink($current_group),
                            'screen_function' => 'render_donate_goods',
                            'item_css_id' => 'user-points',

                        ), 'groups'
        );

                    bp_core_new_subnav_item(
                        array(
                            'position' => 80,
                            'slug' => 'donate-list',
                            'name' => __('物資造冊', 'summ-code'),
                            'parent_slug' => $current_group->slug,
                            'parent_url' => bp_get_group_permalink($current_group),
                            'screen_function' => 'render_donate_list',
                        'user_has_access' => groups_is_user_member(get_current_user_id(), $current_group->id),

                        ), 'groups'
                    );
                $bp->{$single_item_component}->nav-> add_nav($donate);
                $bp->{$single_item_component}->nav-> add_nav($donate3);

}

    add_action('bp_actions', 'jenjishiang_group_menu_item', 20);





function guppy＿efoodbank_group_menu_item()
{     
    $group_slug = bp_get_current_group_slug(); 

    if('guppy-efoodbank'!==$group_slug) {
        return;
    }

    $bp = buddypress();
    $single_item_component = bp_current_component();

        $secondary_nav_items = $bp->{$single_item_component}->nav->get_secondary(array( 'parent_slug' => "$group_slug" ));    
        //error_log(print_r($secondary_nav_items, true));

        $donate=array(
        'name' => '捐款',
        'link' => 'https://www.jjs.org.tw/civicrm/contribute/transact?reset=1&id=6',
            'slug' => 'donate',
            'parent_slug' =>  $group_slug,
            'css_id' => 'donate-money',
            'position' => 60,
            'user_has_access' => 1,
            'no_access_url' => 'http://gooddeeddao.local/groups/wisehelp/',
            'screen_function' => 'groups_screen_group_test',
            'show_in_admin_bar' => '',
            'secondary' => 1
        );
          $donate3=array(
                    'name' => '預約共運',
                    'link' => '/efoodbank-donate/',
                        'slug' => 'share-car',
                        'parent_slug' =>  $group_slug,
                        'css_id' => 'share-car',
                        'position' => 80,
                        'user_has_access' => 1,
                        'no_access_url' => 'http://gooddeeddao.local/groups/wisehelp/',
                        'screen_function' => 'groups_screen_group_test',
                        'show_in_admin_bar' => '',
                        'secondary' => 1
                    );
          $current_group = $bp->groups->current_group;
                
          
        bp_core_new_subnav_item(
            array(
                            'position' => 70,
                            'slug' => 'donate-goods',
                            'name' => __('捐贈物資', 'summ-code'),
                            'parent_slug' => $current_group->slug,
                            'parent_url' => bp_get_group_permalink($current_group),
                            'screen_function' => 'render_donate_goods',
                            'item_css_id' => 'donate-goods',

                        ), 'groups'
        );

                    bp_core_new_subnav_item(
                        array(
                            'position' => 80,
                            'slug' => 'donate-list',
                            'name' => __('物資造冊', 'summ-code'),
                            'parent_slug' => $current_group->slug,
                            'parent_url' => bp_get_group_permalink($current_group),
                            'screen_function' => 'render_donate_list',
                        'user_has_access' => groups_is_user_member(get_current_user_id(), $current_group->id),

                        ), 'groups'
                    );
                $bp->{$single_item_component}->nav-> add_nav($donate);
                $bp->{$single_item_component}->nav-> add_nav($donate3);

}

    add_action('bp_actions', 'guppy＿efoodbank_group_menu_item', 20);

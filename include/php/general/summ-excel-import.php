<?php

function add_custom_menu_page()
{
    add_menu_page(
        '匯入excel', // 菜单的标题
        '匯入excel', // 菜单的文本
        'manage_options', // 用户角色要求
        'import-excel', // 菜单的标识
        'import_excel_page', // 显示页面的回调函数
        'dashicons-upload', // 菜单图标（可选）
        25 // 菜单位置
    );
}

add_action('admin_menu', 'add_custom_menu_page');

function import_excel_page()
{

    if(isset($_POST['submit'])) {
        //error_log(print_r('有文件', true));

        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
            // 有文件上传
            //error_log(print_r('有文件', true));

            $file = $_FILES['excel_file'];
            $achievement_id =  $_POST['achievement_type_select'];

            // 检查文件类型
            $file_type = $file['type'];
            if ($file_type === 'application/vnd.ms-excel' || $file_type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'  || $file_type === 'text/csv') {
                // 文件类型有效，继续处理
    
                $upload_dir = wp_upload_dir(); // 获取WordPress上传目录
    
                // 生成唯一的文件名
                $file_name = wp_unique_filename($upload_dir['path'], $_FILES['excel_file']['name']);
    
                // 移动文件到上传目录
                if (move_uploaded_file($file['tmp_name'], $upload_dir['path'] . '/' . $file_name)) {
                    // 处理文件成功，您可以在这里执行任何所需的操作
    
                    // 例如，将文件路径保存到数据库
                    update_option('uploaded_excel_file', $upload_dir['path'] . '/' . $file_name);
                   
                    include_once CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php';

                    $upload_dir = wp_upload_dir(); // 获取上传目录信息
                
                    $uploads_basedir = $upload_dir['basedir']; // 服务器上的基本目录路径
                    $uploads_dir = $upload_dir['path']; // 服务器上的完整目录路径
                    $importResult = "";
                   
                    //$excelFilePath = $uploads_dir.'/gamipress-user-points-export.csv';
                    $excelFilePath = get_option('uploaded_excel_file');
                
                    try {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);
                    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                        die('无法加载Excel文件: ' . $e->getMessage());
                    }
                
                    $worksheet = $spreadsheet->getActiveSheet();
                
                    // 循环遍历工作表中的每一行，并获取单元格的值
                    $isFirstRow = true; // 用于跟踪是否是第一行

                    foreach ($worksheet->getRowIterator() as $row) {
                        if ($isFirstRow) {
                            $isFirstRow = false;
                            continue; // 跳过第一行
                        }
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false); // 也包括空单元格
                       


                        //每一列開始處理前的變數設置
                        $date = '';
                        $time = '';
                        $year = '';
                        $carbon_token_unit= '';
                        $carbon_token    = '';
                        $carbon_token_granted_once    = '';
                        $gooddeed_token_unit    = '';
                        $gooddeed_token= '';
                        $gooddeed_token_granted_once    = '';
                        $completed_numbers = '';
                        $carbon_token_granted_total = '';
                        $gooddeed_token_granted_total = '';
                        $location = '';
                        $post_content = '';
                        $group_id = '';


                        $pictures = '';
                        // 初始化一个变量来存储使用者搜索结果
                         $user_found = false;
                         $column_number = 0;
                         $user_id=false;
                        foreach ($cellIterator as $cell) {
                            //$cellValue = $cell->getValue();

                            $cellValue = $cell->getCalculatedValue();

                            $column_number++; // 增加列号
  
                            //手機號碼
                            if (1===  $column_number) {
                                if($cellValue==null){
                                    break;
                                }

                                $users = get_users();


                                 // 要搜索的用户名
                                $search_username = $cellValue;

                                if (substr($search_username, 0, 1) !== '0') {
                                    $search_username = "0" . $search_username;
                                }
                                // 遍历所有用户
                                $user = get_user_by('login', $search_username);

                                    //search array 
                                    $users_by_phone = get_users(array(
                                        'meta_key' => 'summost_phone_number',
                                        'meta_value' => $search_username,
                                        'number' => 1,
                                        'count_total' => false
                                    ));
                                    // search user
                                    if (!empty($users_by_phone)) {
                                        $user = reset($users_by_phone); // 获取第一个用户对象
                                    }
                                    if ($user) {
                                        $user_found = true;
                                        $user_id= $user->ID;
                                    }
                                if (!$user) {
                                    $importResult .= '使用者手機: ' . $search_username . ' 未找到，創立新用戶。<br />';
                                    $current_time = (int)time();
                                    $user_count = count_users();
                                    $new_username= $current_time+(int)$user_count['total_users'];
                                    $new_username= 'gdm'.$new_username;
                                    $userdata = array(
                                        'user_login' => $new_username, // 用户名.
                                        'user_pass'  => $search_username, // 密码.
                                        'role'       => 'subscriber', // 角色 (subscriber, author, editor, administrator, etc.).
                                    );
                                    $user_id = wp_insert_user( $userdata );
                                    update_user_meta($user_id, 'summost_phone_number', $search_username);
                                    $user_found = true;

                                }
                            }
              

                            //配對使用者是否有符合使用者暱稱
                            if (2 ===  $column_number) {
                                $user = get_user_by('ID', $user_id);
                                if ($user) {
                                    $first_name = get_user_meta($user_id, 'first_name', true);
                                    if ('-'===$first_name) {
                                        $first_name=$cellValue;
                                        update_user_meta($user_id, 'first_name', $cellValue);
                                        $user_id = wp_update_user(array( 'ID' => $user_id, 'display_name' => $cellValue ));
                                    }

                                    if ($user->user_login===$first_name) {
                                        $first_name=$cellValue;
                                        update_user_meta($user_id, 'first_name', $cellValue);
                                        $user_id = wp_update_user(array( 'ID' => $user_id, 'display_name' => $cellValue ));
                                    }

                                    $search_nickname = get_user_meta($user_id, 'nickname', true);
                                    if ('-'===$search_nickname) {
                                        $search_nickname=$cellValue;
                                        update_user_meta($user_id, 'nickname', $cellValue);
                                    }
                                    $search_nickname = get_user_meta($user_id, 'nickname', true);
                                    if ($user->user_login===$search_nickname) {
                                        $search_nickname=$cellValue;
                                        update_user_meta($user_id, 'nickname', $cellValue);
                                    }
                                }
                            }
                            if (3 ===  $column_number) {
                               // check format depends on php or excel file                            
                                // $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                                // $date = date('Y-m-d', $date);

                                $date = $cellValue;   
                            }
                            if (4===  $column_number) {
                                $time = $cellValue;
                            }
                            if (5 ===  $column_number) {
                                $carbon_token_unit = $cellValue;
                            }
                            if (6 ===  $column_number) {
                                $carbon_token = $cellValue;
                            }
                            if (7===  $column_number) {
                                $carbon_token_granted_once = $cellValue;
                            }
                            if (8===  $column_number) {
                                $gooddeed_token_unit = $cellValue;
                            }
                            if (9===  $column_number) {
                                $gooddeed_token = $cellValue;
                            }
                            if (10===  $column_number) {
                                $gooddeed_token_granted_once = $cellValue;
                            }
                            if (11===  $column_number) {
                                $completed_numbers = $cellValue;
                            }
                            if (12===  $column_number) {
                                $carbon_token_granted_total = $cellValue;
                            }
                            if (13===  $column_number) {
                                $gooddeed_token_granted_total = $cellValue;
                            }
                            if (14===  $column_number) {
                                $location = $cellValue;
                            }
                            if (15===  $column_number) {
                                $post_content = $cellValue;
                                if(empty($post_content)) {
                                    $post_content = '';
                                }
                            }
                            if (16===  $column_number) {
                                if(empty($cellValue)) {
                                    $pictures = NULL;
                                }
                                if(!empty($cellValue)) {
                                    /*import id to media*/
                                    $pictures =  $cellValue;
                                }
                            }
                            if (17===  $column_number) {
                                $group_id = $cellValue;
                            }
                        }
                        //在這裡執行SQL操作與點數執行

                        if (!$user_found) {
                            continue;
                        }
                        gamipress_award_achievement_to_user($achievement_id, $user_id);
                        GLOBAL $wpdb;
                        $table_name = 'wp_gamipress_user_earnings';  
                        $query = "SELECT * FROM $table_name where user_id = $user_id and post_id = $achievement_id ORDER BY user_earning_id DESC LIMIT 1";
                        $result = $wpdb->get_results($query);

                        if (!$result) {
                            echo '新增資料失敗';
                            wp_die();
                        }    
                        $user_earning_id = $result[0]->user_earning_id;    
                        $table_name ='summ_gamipress_log_extra_data';
                        //更新徽章日期
                        if(empty($time)){
                            $time ='00:00:00';
                        }
                        $date = $date.' '.$time;
                        $sql = $wpdb->prepare("UPDATE wp_gamipress_user_earnings SET date = %s WHERE user_earning_id = %s", $date, $user_earning_id);
                        $wpdb->query($sql);

                        //有團體id,加入團體，貼文同步團體
                        if(!empty($group_id)){
                        $arg=array(
                            'id'                => false,                  // Pass an existing activity ID to update an existing entry.
                            'action'            => '',                     // The activity action - e.g. "Jon Doe posted an update".
                            'content'           => $post_content,                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
                            'component'         => 'groups',                  // The name/ID of the component e.g. groups, profile, mycomponent.
                            'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
                            'primary_link'      => '',                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
                            'user_id'           => $user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
                            'item_id'           => $group_id,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
                            'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
                            'recorded_time'     => $date, // The GMT time that this activity was recorded.
                            'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
                            'is_spam'           => false,                  // Is this activity item to be marked as spam?
                            'error_type'        => 'bool',
                        );
							    groups_join_group((int)$group_id,$user_id);

						}

                        //沒有團體id,不加入團體，貼文留在個人塗鴉牆

                        if(empty($group_id)){
                            $arg=array(
                                'id'                => false,                  // Pass an existing activity ID to update an existing entry.
                                'action'            => '',                     // The activity action - e.g. "Jon Doe posted an update".
                                'content'           => $post_content,                     // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!".
                                'component'         => 'activity',                  // The name/ID of the component e.g. groups, profile, mycomponent.
                                'type'              => 'activity_update',                  // The activity type e.g. activity_update, profile_updated.
                                'primary_link'      => '',                     // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink).
                                'user_id'           => $user_id,  // Optional: The user to record the activity for, can be false if this activity is not for a user.
                                'item_id'           => false,                  // Optional: The ID of the specific item being recorded, e.g. a blog_id.
                                'secondary_item_id' => false,                  // Optional: A second ID used to further filter e.g. a comment_id.
                                'recorded_time'     => $date, // The GMT time that this activity was recorded.
                                'hide_sitewide'     => false,                  // Should this be hidden on the sitewide activity stream?
                                'is_spam'           => false,                  // Is this activity item to be marked as spam?
                                'error_type'        => 'bool',
                            );                       

						}


                         $activity_id = bp_activity_add($arg);
                        //照片不是空值，寫入照片到文章
                         if (null !== $pictures){
                            //有','表示多張照片
                            $gallery_id=(int) mpp_get_user_meta( $user_id, '_mpp_wall_photo_gallery_id', true );
                            if(0==$gallery_id){                            
                                $gallery_id=mpp_create_gallery( array(
                                    'creator_id'	=> $user_id,
                                    'title'			=> 'Wall photo Gallery',
                                    'description'	=> '',
                                    'status'		=> 'public',
                                    'component'		=> 'members',
                                    'component_id'	=> $user_id,
                                ) );
                                mpp_update_user_meta( $user_id, '_mpp_wall_photo_gallery_id', $gallery_id );                            
                            }
                           if (strpos($pictures, ',') !== false) {
                            $pictures_array = explode(',', $pictures);
                            //插入media id 到activity
                            mpp_activity_update_attached_media_ids($activity_id, $pictures_array);
                            //插入mediapress所需欄位
                            foreach ( $pictures_array as $pictures_id ) {
                                //增加圖庫計數
                                mpp_gallery_increment_media_count($gallery_id);
                                 //封面圖檢查與更新
                                 if(empty(mpp_get_gallery_meta( $gallery_id, '_mpp_cover_id', true ))){       
                                    mpp_update_gallery_meta( $gallery_id, '_mpp_cover_id', $pictures_id );
                                }               

                                //更新relationship   
                                global $wpdb;
                                $wpdb->insert(
                                    $wpdb->term_relationships,
                                    array(
                                        'object_id'        => $pictures_id,
                                        'term_taxonomy_id' => '6',
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->term_relationships,
                                    array(
                                        'object_id'        => $pictures_id,
                                        'term_taxonomy_id' => '13',
                                    )
                                );
                                $wpdb->insert(
                                    $wpdb->term_relationships,
                                    array(
                                        'object_id'        => $pictures_id,
                                        'term_taxonomy_id' => '4',
                                    )
                                );
                                add_post_meta($pictures_id, '_mpp_is_mpp_media', 1);
                                add_post_meta($pictures_id, '_mpp_context', 'activity');
                                add_post_meta($pictures_id, '_mpp_component_id', $user_id);

                                wp_update_post(array(
                                    'ID' => $pictures_id,
                                    'post_parent' => $gallery_id,
                                ));

                            }
                        }

                        if (strpos($pictures, ',') == false) {
                            //增加圖庫計數
                            mpp_gallery_increment_media_count($gallery_id);
                            //封面圖檢查與更新
                            if(empty(mpp_get_gallery_meta( $gallery_id, '_mpp_cover_id', true ))){       
                                mpp_update_gallery_meta( $gallery_id, '_mpp_cover_id', $pictures );
                            }    
                              //更新relationship   
                              global $wpdb;
                              $wpdb->insert(
                                  $wpdb->term_relationships,
                                  array(
                                      'object_id'        => $pictures,
                                      'term_taxonomy_id' => '6',
                                  )
                              );
                              $wpdb->insert(
                                  $wpdb->term_relationships,
                                  array(
                                      'object_id'        => $pictures,
                                      'term_taxonomy_id' => '13',
                                  )
                              );
                              $wpdb->insert(
                                  $wpdb->term_relationships,
                                  array(
                                      'object_id'        => $pictures,
                                      'term_taxonomy_id' => '4',
                                  )
                              );
                            bp_activity_add_meta( $activity_id, '_mpp_attached_media_id', $pictures );
                            add_post_meta($pictures, '_mpp_is_mpp_media', 1);
                            add_post_meta($pictures, '_mpp_context', 'activity');
                            add_post_meta($pictures, '_mpp_component_id', $user_id);
                            wp_update_post(array(
                                'ID' => $pictures,
                                'post_parent' => $gallery_id,
                            ));
                        }
                    }
                        $args  = array(
                            'user_earning_id'              => $user_earning_id,
                            'achievement_id'              => $achievement_id,
                            'user_id'             		   => $user_id,
                            'activity_id'                  => $activity_id,
                            'datetime'                     => $date,
                            'carbon_token_unit'            => $carbon_token_unit,
                            'carbon_token'                 => $carbon_token,
                            'carbon_token_granted_once'    => $carbon_token_granted_once,
                            'gooddeed_token_unit'          => $gooddeed_token_unit,
                            'gooddeed_token'               => $gooddeed_token,
                            'gooddeed_token_granted_once'  => $gooddeed_token_granted_once,
                            'completed_numbers'            => $completed_numbers,
                            'carbon_token_granted_total'   => $carbon_token_granted_total,
                            'gooddeed_token_granted_total' => $gooddeed_token_granted_total,
                            'pictures'                     => $pictures,
                            'location'                     => $location,
                        );
                        $result = add_summ_gamipress_log_extra_data($args);

                        if (!$result) {
                            echo '數據插入失敗'; 
                            wp_die();
                        }
                        if (!$user_found) {
                            continue;
                        }

                        gamipress_award_points_to_user($user_id, $carbon_token_granted_total, 'carbon-token');
                        gamipress_award_points_to_user($user_id, $gooddeed_token_granted_total, 'gooddeed-token');

                        //$importResult .=  'excel編號: '.$excel_id.'，使用者帳號: ' . $search_username . ' 已成功匯入。<br />';

                    }
                
                
          
                    if(""===$importResult) {
                        echo '數據已成功匯入資料庫';

                    }else{
                        echo '使用者有更新，請檢查是否正確<br />';
                        echo $importResult;
                    }

                } else {
                    echo '文件上傳失敗';
                }
            } else {
                // 无效的文件类型
                echo '錯誤的文件副檔名';
            }
        }
    }

    function show_select_excel_panel()
    {
        $output='';
        // 找到post_type为'achievement-type'的所有帖子
        $achievement_type_posts = new WP_Query(
            array(
            'post_type' => 'achievement-type',
            'posts_per_page' => -1,
            )
        );

        // 创建一个数组来存储post_name
        $achievement_type_names = array();

        if ($achievement_type_posts->have_posts()) {
            while ($achievement_type_posts->have_posts()) {
                $achievement_type_posts->the_post();
                $achievement_type_names[] = get_post_field('post_name');
            }
            wp_reset_postdata(); // 重置查询
        }

        // 创建<select>元素
        $output .= '<select name="achievement_type_select">';

        // 遍历$achievement_type_names数组
        foreach ($achievement_type_names as $post_name) {
            // 找到post_type为$post_name的所有帖子
            $related_posts = new WP_Query(
                array(
                'post_type' => $post_name,
                'posts_per_page' => -1,
                )
            );

            if ($related_posts->have_posts()) {
                while ($related_posts->have_posts()) {
                    $related_posts->the_post();
                    // 输出<option>元素
                    $output .= '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
                }
                    wp_reset_postdata(); // 重置查询
            }
        }

        $output .= '</select>';
        return  $output;
    }
    
    ?>
    <h2>匯入檔案</h2>
    <form method="post" enctype="multipart/form-data">
    <input type="file" name="excel_file">
    <?php echo show_select_excel_panel() ?>
    <input type="submit" name="submit" value="上傳excel文件">
</form>
    <?php


}

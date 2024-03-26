<?php
/**
 * User Earnings template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/earnings.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;
$user = get_user_by('ID', $a['user_id']);
if(isset($_GET['user_id'])){
    $a['query']->set('user_id',$_GET['user_id']);
    $user = get_user_by('ID', $_GET['user_id']);
};

$useraccount= $user->user_login;
// Execute the query
$user_earnings = $a['query']->get_results();
?>

<div class="gamipress-earnings">

    <div class="gamipress-earnings-atts">

        <?php
        /**
         * Before render earnings atts
         *
         * @since 1.4.9
         *
         * @param array $template_args Template received arguments
         */
        do_action('gamipress_before_render_earnings_atts', $a); ?>

        <?php // Hidden fields for ajax request
        echo gamipress_array_as_hidden_inputs($a, array( 'query' )); ?>

        <?php
        /**
         * After render earnings atts
         *
         * @since 1.4.9
         *
         * @param array $template_args Template received arguments
         */
        do_action('gamipress_after_render_earnings_atts', $a); ?>

    </div>

    <?php
    /**
     * Before render earnings
     *
     * @since 1.0.0
     *
     * @param array $template_args Template received arguments
     */
    do_action('gamipress_before_render_earnings', $a); ?>

    <?php if($a['query']->found_results > 0 ) : ?>

        <?php
        /**
         * Earnings columns
         *
         * @since 1.0.0
         *
         * @param array $columns            Earnings table columns to be rendered
         * @param array $template_args      Template received arguments
         */
        $columns = apply_filters('gamipress_earnings_columns', $a['columns'], $a);
        //$columns['month']='活動月份';
        //$columns['day']='活動日期';
        //$columns['year']='活動年份';
        $columns['gooddeed_token_granted_total']='好人幣';
        $columns['carbon_token_granted_total']='碳幣';
        $columns['completed_numbers']='總完成次數';
        $columns['group']='活動單位';
        $columns['location']='活動地點';
        $columns['pictures']='活動花絮';

        unset($columns['points']);
        //unset($columns['date']);
        unset($columns['thumbnail']);


        ?>

        <table id="gamipress-earnings-table" class="gamipress-earnings-table <?php if($a['force_responsive'] === 'yes' ) : ?>gamipress-earnings-force-responsive<?php 
       endif;?>">

            <thead>

                <tr>

                    <?php foreach( $columns as $column_name => $column_label ) : ?>
                     
                        <?php
                        if($column_name==='description') {
                            $column_label='好事紀錄';
                        }
                        if($column_name==='date') {
                            $column_label='日期';
                        }
                    
                        ?>

                        <th class="gamipress-earnings-col gamipress-earnings-col-<?php echo esc_attr($column_name); ?>"><?php echo $column_label; ?></th>
                    <?php endforeach ?>

                </tr>

            </thead>
            <tbody>

            <?php foreach( $user_earnings as $user_earning ) : ?>
            
                <?php // Skip earnings that post assigned has been deleted
                if(! gamipress_post_exists($user_earning->post_id) ) { continue; 
                } ?>

                <tr>
                <?php  
                $achievement_id = '';
                $activity_id = '';
                $date = '';
                $month = '';
                $year = '';
                $completed_numbers = '';
                $carbon_token_granted_total = '';
                $gooddeed_token_granted_total = '';
                $location = '';
                $pictures = '';
                $table_name = 'summ_gamipress_log_extra_data';  
                         $query = "SELECT * FROM $table_name where user_earning_id = $user_earning->user_earning_id";
                         $results = $wpdb->get_results($query);
                if ($results) {
                    foreach ($results as $row) {
                                    $date = $row->datetime;
                                    $completed_numbers = $row->completed_numbers;
                                    $carbon_token_granted_total = $row->carbon_token_granted_total;
                                    $gooddeed_token_granted_total = $row->gooddeed_token_granted_total;
                                    $location = $row->location;
                                    $pictures = $row->pictures;
                                    $achievement_id = $row->achievement_id;
                                    $activity_id = $row->activity_id;

                    }
                }
          

                //其他資料


                $group_name='';
                $post_type = get_post_type($user_earning->post_id);
                 $args = array(
                    'post_type' => 'achievement-type', // 文章类型，如果需要的话
                    'name'=> $post_type,
                 );
                
                 $post_query = new WP_Query($args);
                
                 if ($post_query->have_posts()) {
                     while ($post_query->have_posts()) {
                         $post_query->the_post();
                         $group_name= get_post_meta(get_the_id(), '_gamipress_plural_name', 1); // 输出文章标题
                     }
                 } else {
                     echo '没有匹配的文章。';
                 }
                
                 // 重置查询
                 wp_reset_postdata();
          
               
                    ?>                      

                    <?php foreach( $columns as $column_name => $column_label ) : ?>                      
                        <?php
                        /**
                         * Render earnings column
                         *
                         * @since 1.0.0
                         *
                         * @see gamipress_earnings_render_column()
                         *
                         * @param string    $column_output  Default column output
                         * @param string    $column_name    The column name
                         * @param stdClass  $user_earning   The column name
                         * @param array     $template_args  Template received arguments
                         *
                         * @return string
                         */
                        $column_output = apply_filters('gamipress_earnings_render_column', '', $column_name, $user_earning, $a);
                        $css_class = 'gamipress-earnings-col gamipress-earnings-col-' . $column_name;

                       


                        if('date' === $column_name) {
                            $originalDateTime = new DateTime($date);
                            $column_output =  $originalDateTime->format('Y-m-d');

                        }
                        if('completed_numbers' === $column_name) {
                            $column_output = $completed_numbers;
                        }
                        if('carbon_token_granted_total' === $column_name) {
                            $column_output = $carbon_token_granted_total;
                        }
                        if('gooddeed_token_granted_total' === $column_name) {
                            $column_output = $gooddeed_token_granted_total;
                        }

                        if('group' === $column_name) {
                            $column_output = $group_name;

                        }
                        if('location' === $column_name) {
                            $column_output = $location;
                        }
                        if('pictures' === $column_name) {

                            if (!is_null($pictures)) {
                                // 將字串轉換為陣列
                               if('multiple'===$pictures) {

                                $pictures_ids = mpp_activity_get_attached_media_ids($activity_id);

                                   // 檢查是否有多張圖片
                                   if (count($pictures_ids) > 1) {
                                    $column_output='';
                                    // 有多張圖片的情況

                                    // 在這裡可以按需執行相應的操作
                                    // 例如，遍歷陣列，顯示每個圖片
                                    $column_output .= '<div class="custom-slider">';
                                    $column_output .= '<div class="slider-container">';
                                    $column_output .= '<ul class="slider-list">';
                                    foreach ($pictures_ids as $pictures_id) {
                                        $picture = mpp_get_media_src( 'original', $pictures_id );
                                        $column_output .= '<li class="slider-item ">';
                                        $column_output  .= '<a href="' . $picture . '" target="_blank">';
                                        $column_output .= '<img src="' . $picture . '" alt="一張圖片">';
                                        $column_output .= '</a>';
                                        $column_output .= '</li>';
                                        // 在這裡可以進一步處理每個圖片                                       
                                    }
                                    $column_output .= '</ul>';
                                    $column_output .= '</div>';
                                    $column_output .= '<button class="prev-button"><</button>';
                                    $column_output .= '<button class="next-button">></button>';
                                    $column_output .= '</div>';
                                } else {
                                    // 只有一張圖片的情況
                                    $picture = mpp_get_media_src( 'original', $pictures_ids[0] );
                                    $column_output = '<a href="' . $picture . '" target="_blank">';
                                    $column_output .= '<img src="' . $picture . '"alt="一張圖片">';
                                    $column_output .= '</a>';
                                    // 在這裡可以進一步處理唯一的圖片
                                }
                            } 
                            if('multiple'!==$pictures) {
                                $picture = mpp_get_media_src( 'original', $pictures );
                                $column_output = '<a href="' . $picture . '" target="_blank">';
                                $column_output .= '<img src="' . $picture . '"alt="一張圖片">';
                                $column_output .= '</a>';
                            }
                            }
                            if (is_null($pictures)) {
                                $column_output= summ_get_feature_image_by_achievement_id($achievement_id);
                                if (null===$column_output) {
                                    $column_output='暫無圖片';
                                }
                                }

                        }
                        if('description' === $column_name) {

                            $achievement_event_title_value = get_post_meta($achievement_id, '_achievement_event_title_value', true);
                            $site_url = site_url();

                           // $cleaned_output = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '$1', $column_output);

                            $column_output =  get_the_title($achievement_id).'<br />';
                            $column_output .=  $achievement_event_title_value;
                            $column_output ='<a href="' . esc_url($site_url) .'/members/'.$useraccount.'/activity/'.$activity_id. '">' . $column_output . '</a>';


                        }


                        if(empty($column_output) ) {
                            $column_output = '-';
                            $css_class .= ' gamipress-earnings-col-empty';
                        }

                        $mobile_label='<span class="point-list-mobile-label" style="display:none;">'.$column_label.':</span>';

                        if('Description'==$column_label) {
                            $mobile_label='';
                        }
                        if('Date'==$column_label) {
                            $mobile_label='<span class="point-list-mobile-label" style="display:none;">活動日期:</span>';
                        }

                        if('活動花絮'==$column_label) {
                            $mobile_label='';
                        }
                        ?>

                        <td class="<?php echo esc_attr($css_class); ?>" data-label="<?php echo esc_attr($column_label); ?>"><?php echo $mobile_label; ?><?php echo $column_output; ?></td>
                     
                        <?php     //error_log(print_r($user_earning->user_earning_id, true)); ?>
                
                    <?php endforeach ?>

                </tr>

            <?php endforeach; ?>

            </tbody>
            <script>
        // // 使用 jQuery 选择器选择指定 class 的 <a> 链接
        // jQuery('.gamipress-earnings-col-description a').each(function() {
        //     // 获取链接的文本内容
        //     var text = jQuery(this).text();
        //     // 用链接的文本内容替换链接
        //     jQuery(this).replaceWith(text);
        // });
    </script>
        </table><!-- .gamipress-earnings-table -->
       
       <!-- remove a tag -->
    
        <?php // Pagination
        if($a['pagination'] === 'yes' ) : ?>

            <?php
            /**
             * Before render earnings list pagination
             *
             * @since 1.4.9
             *
             * @param array $template_args Template received arguments
             */
            do_action('gamipress_before_render_earnings_list_pagination', $a); ?>

            <div id="gamipress-earnings-pagination" class="gamipress-earnings-pagination navigation">
                <?php echo paginate_links(
                    array(
                    'base'    => str_replace(999999, '%#%', esc_url(get_pagenum_link(999999))),
                    'format'  => '?paged=%#%',
                    'current' => max(1, get_query_var('paged')),
                    'total'   => ceil($a['query']->found_results / $a['limit'])
                    ) 
                ); ?>
            </div>

            <?php
            /**
             * After render earnings list pagination
             *
             * @since 1.4.9
             *
             * @param array $template_args Template received arguments
             */
            do_action('gamipress_after_render_earnings_list_pagination', $a); ?>

            <?php // Loading spinner ?>
            <div id="gamipress-earnings-spinner" class="gamipress-spinner" style="display: none;"></div>

        <?php endif; ?>

    <?php else : ?>

        <p id="gamipress-earnings-no-results"><?php echo __('You have not earned anything yet.', 'gamipress'); ?></p>

    <?php endif; ?>

    <?php
    /**
     * After render earnings
     *
     * @since 1.0.0
     *
     * @param array $template_args Template received arguments
     */
    do_action('gamipress_after_render_earnings', $a); ?>

</div>

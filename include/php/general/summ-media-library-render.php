<?php
// 添加自定义列到媒体库列表
function custom_media_columns($columns) {
    $columns['media_id'] = 'Media ID';
    return $columns;
}
add_filter('manage_media_columns', 'custom_media_columns');

// 显示媒体ID值
function custom_media_column_content($column_name, $id) {
    if ($column_name === 'media_id') {
        echo $id;
    }
}
add_action('manage_media_custom_column', 'custom_media_column_content', 10, 2);

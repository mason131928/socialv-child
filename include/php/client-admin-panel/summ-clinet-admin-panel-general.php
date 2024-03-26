<?php
function custom_admin_footer_text($text) {
    return '歡迎使用好事道平台';
}

add_filter('admin_footer_text', 'custom_admin_footer_text');


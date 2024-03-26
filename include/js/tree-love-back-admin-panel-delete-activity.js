jQuery(document).ready(function ($) {
    // 给删除活动的链接绑定点击事件
    $('.deleteLogLink').on('click', function (e) {
        e.preventDefault();

        // 获取活动ID
        var activityId = $(this).data('activity-id');
        var mediaId = $(this).data('media-id');
        // 发送 AJAX 请求
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'backend_delete_log',
                media_id: mediaId
            },
            success: function (response) {
                // 处理成功响应
                if (response === 'success') {
                    // 在这里执行删除成功后的操作，比如刷新页面或更新 UI
                    location.reload()
                } else {
                    // 处理失败响应
                    alert('Failed to delete log.');
                }
            },
            error: function (error) {
                // 处理 AJAX 请求错误
                console.log('Error:', error);
            }
        });
    });
});


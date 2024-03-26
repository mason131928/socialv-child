
/* user menu items */
jQuery(document).ready(function () {
    jQuery("#mpp-activity-upload-buttons").on("click", function (e) {
        // setTimeout(function () {
        //     jQuery('#mpp-upload-media-button-activity').click();
        // }, 1000);

    });


    jQuery(".socialv-head-buttons-inner li:eq(0)").remove();
    jQuery(".socialv-head-buttons-inner li:eq(1)").remove();

    jQuery(".socialv-head-buttons-inner li:eq(3)").remove();
    //jQuery(".socialv-head-buttons-inner li:eq(2)").remove();


    jQuery('#clearDeleteMessageBtn').on('click', function () {
        // 获取当前用户的 ID
        // 发送 AJAX 请求
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl, // WordPress 提供的 AJAX 处理 URL
            data: {
                action: 'clean_admin_delete_image_count',
            },
            success: function (response) {
                // 处理成功响应
                jQuery('#delete-message').remove();
            }
        });
    });

})


/* log-point slider */
jQuery(document).ready(function ($) {

    $(".prev-button").on("click", function () {

        var currentSlide = $(this).parent().find('.slider-item:visible');
        var prevSlide = currentSlide.prev('.slider-item');
        if (prevSlide.length === 0) {
            prevSlide = $(this).parent().find('.slider-item').last();
        }
        currentSlide.hide();
        prevSlide.show();


    });

    $(".next-button").on("click", function () {
        var currentSlide = $(this).parent().find('.slider-item:visible');
        var nextSlide = currentSlide.next('.slider-item');
        if (nextSlide.length === 0) {
            nextSlide = $(this).parent().find('.slider-item').first();
        }
        currentSlide.hide();
        nextSlide.show();

    });

});




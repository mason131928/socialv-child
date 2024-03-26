
/* user menu items */
jQuery(document).ready(function () {
    jQuery('select[name="achievement_type_select"]').on('change', function () {
        // 在这里执行你的操作
        var selectedAchievement = jQuery(this).val();
        var achievementDescription = '';
        // 每日抱抱
        if (selectedAchievement == 9970) {
            achievementDescription = "上傳一張親子、家人擁抱的照片<br />小樹傳愛協會就捐款新台幣5元<br />目標募百萬心擁抱<br />作為學童心靈教育基金<br />擁抱照片對象不可重複，上傳成功將獲得5點好人幣<br />照片經審核，跟擁抱無關，平台有權扣除點數與紀錄"
        }

        jQuery('#achievement_description').html(achievementDescription);

    });

})


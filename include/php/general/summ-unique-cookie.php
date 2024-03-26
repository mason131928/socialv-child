<?php
function unique_cookie()
{
    if (!isset($_COOKIE['unique_user_id'])) {
        $unique_user_id = uniqid();
        
        ?>
        <script>

        var expirationDate = new Date();
        expirationDate.setFullYear(expirationDate.getFullYear() + 1);
        document.cookie = 'unique_user_id='+'<?php echo $unique_user_id; ?>'+'; expires=' + expirationDate.toUTCString() + '; path=/';
</script>
            <?php
    } 
}

function set_cookie()
{
        unique_cookie();
}
 add_action('init', 'set_cookie');

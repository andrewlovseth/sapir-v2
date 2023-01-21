<?php

    $footer = get_field('footer', 'options');
    $banner = $footer['banner'];

?>

<div class="footer-banner site-banner">
    <div class="image">
        <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
    </div>
</div>
<?php

    $header = get_field('header', 'options');
    $banner = $header['banner'];

?>

<div class="header-banner site-banner">
    <div class="image">
        <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
    </div>
</div>
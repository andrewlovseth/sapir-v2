<?php

    $header = get_field('header', 'options');
    $logo = $header['logo'];

?>

<div class="footer-logo">
    <a href="<?php echo site_url('/'); ?>" aria-label="SAPIR Journal home">
        <?php echo get_svg($logo['url']); ?>
    </a>
</div>
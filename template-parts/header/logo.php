<?php

    $header = get_field('header', 'options');
    $logo = $header['logo'];
    $tagline = $header['tagline'];

?>

<div class="site-logo">
    <a href="<?php echo site_url('/'); ?>">
        <?php echo get_svg($logo['url']); ?>
    </a>
</div>
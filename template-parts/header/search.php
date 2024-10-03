<?php

    $link = get_field('subscribe_link', 'options');

?>

<div class="search">

    <?php 
        if( $link ): 
        $link_url = $link['url'];
        $link_title = $link['title'];
        $link_target = $link['target'] ? $link['target'] : '_self';
    ?>

        <div class="cta">
            <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>" class="about-link newsletter-link"><?php echo esc_html($link_title); ?></a>
        </div>

    <?php endif; ?>

    <a href="<?php echo site_url('/about/'); ?>" class="about-link">About Us</a>

    <a href="#" class="js-search-trigger search__icon">
        <?php get_template_part('svg/icon-search'); ?>
    </a>

    <div class="search__modal">   
        <?php get_template_part('template-parts/global/search-form'); ?>
    </div>
</div>
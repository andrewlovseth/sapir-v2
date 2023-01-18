<div class="search">
    <a href="<?php echo site_url('/about/'); ?>" class="about-link">About Us</a>

    <a href="#" class="js-search-trigger search__icon">
        <?php get_template_part('svg/icon-search'); ?>
    </a>

    <div class="search__modal">
        <div class="search__close js-search-close">
            <?php get_template_part('svg/icon-close'); ?>
        </div>
    
        <?php get_template_part('template-parts/global/search-form'); ?>
    </div>
</div>
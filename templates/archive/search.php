<?php

    $search = get_field('search');
    $icon = $search['icon'];
    $header = $search['header'];

?>


<section class="search grid" id="search">

    <div class="section-header">
        <div class="icon">
            <?php echo get_svg($icon['url']); ?>
        </div>

        <h3 class="section-title-alt"><?php echo $header; ?></h3>
    </div>
    
    <?php get_template_part('template-parts/global/search-form'); ?>

</section>
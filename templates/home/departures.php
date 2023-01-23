<?php

    $departures = get_field('departures');
    $header = $departures['header'];
    $posts = $departures['posts'];
    $banner = $departures['banner'];

?>

<section class="departures">


    <div class="departures__header">
        <div class="banner banner__header">
            <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
        </div>

        <h2 class="section-title"><?php echo $header; ?></h2>
    </div>

    <div class="departures__body">
        <?php if($posts): ?>
            <?php foreach( $posts as $p ): ?>
            
                <?php
                    $args = ['p' => $p];
                    get_template_part('template-parts/global/teaser/teaser-small', null, $args);
                ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="departures__footer">
        <div class="banner banner__footer">
            <?php echo wp_get_attachment_image($banner['ID'], 'full'); ?>
        </div>    
    </div>


</section>
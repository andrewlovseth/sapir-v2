<?php 

    $image_type = get_sub_field('image_type');
    $graphic = get_sub_field('graphic');
    $authors = get_sub_field('authors');

    $date_string = get_sub_field('date');
    $date = DateTime::createFromFormat('Ymd', $date_string);
    $title = get_sub_field('title');
    $time = get_sub_field('time');
    $description = get_sub_field('description');
    $link = get_sub_field('link');
?>


<div class="event">

    <div class="event__image">
        <?php if($image_type == 'authors'): ?>

            <div class="authors">
                <?php if($authors): ?>
                    <?php foreach($authors as $a): ?>
                        <div class="author">
                            <div class="photo">
                                <?php echo get_the_post_thumbnail($a->ID, 'thumbnail'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>

            <div class="graphic">
                <?php echo wp_get_attachment_image($graphic['ID'], 'full'); ?>
            </div>

        <?php endif; ?>
    </div>

    <div class="event__info">
        <div class="event__meta">
            <span class="date"><?php echo $date->format('l, F j, Y'); ?></span>
            <?php if($time): ?>
                <span class="time"><?php the_sub_field('time'); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="event__headline">
            <h3 class="event__title"><?php echo $title; ?></h3>
        </div>

        <div class="event__description copy copy-2">
            <?php echo $description; ?>
        </div>

        <?php
            $args = ['link' => $link];
            get_template_part('template-parts/global/cta', null, $args);
        ?>    
    </div>
    
</div>

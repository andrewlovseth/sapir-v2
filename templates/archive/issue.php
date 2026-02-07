<?php

    $issue = get_field('issue');
    $header = $issue['header'];

?>

<section class="issue grid" id="issue">

    <div class="section-header">
        <span class="upper-header"><?php echo $header; ?></span>
    </div>

    <?php
        $args = array(
            'post_type' => 'issue',
            'posts_per_page' => 50
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) : ?>

        <div class="issue__gallery">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php 
                    $cover = get_field('cover');
                    $title = get_the_title();
                    $volume = get_field('volume');
                    $season = get_field('season');
                ?>
        
                <div class="issue__gallery-item">
                    <a href="<?php the_permalink(); ?>" class="issue__link">
                        <div class="issue__cover">
                            <?php if($cover): ?>
                                <?php echo wp_get_attachment_image($cover['ID'], 'full'); ?>
                            <?php endif; ?>
                        </div>

                        <div class="info">
                            <h3 class="issue__title"><?php echo $title; ?></h3>

                            <div class="issue__meta">
                                <span class="volume"><?php echo $volume; ?></span> <span class="season"><?php echo $season; ?></span>
                            </div>
                        </div>


                    </a>
                </div>

            <?php endwhile; ?>
        </div>

    <?php endif; wp_reset_postdata(); ?>

</section>
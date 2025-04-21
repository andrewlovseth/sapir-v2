<section class="salons-archive | grid">

    <div class="section-header">
        <h5 class="upper-header">Resources for Past Issues</h5>
    </div>

    <?php
        $args = array(
            'post_type' => 'salons',
            'posts_per_page' => 50
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) : ?>

        <div class="issue__gallery">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php
                
                    $issue = get_field('issue');
                    $cover = get_field('cover', $issue->ID);
                    $title = get_the_title();
                    $volume = get_field('volume', $issue->ID);
                    $season = get_field('season', $issue->ID);
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
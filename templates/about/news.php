<section class="news-section grid">
    <div class="section-header">
        <h2 class="section-title-alt">News</h2>
    </div>

    <?php
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 25
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

            <article <?php post_class('news'); ?>>
                <div class="news__meta">
                    <span class="date"><?php echo get_the_time('F j, Y') ?></span>
                </div>
                
                <div class="news__headline">
                    <h3 class="news__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                </div>

                <div class="news__copy copy copy-2">
                    <p><?php echo get_the_excerpt(); ?></p>
                </div>
                
                <div class="news__cta cta">
                    <a href="<?php the_permalink(); ?>" class="btn small-upper">Read more</a>
                </div>
            </article>

    <?php endwhile; endif; wp_reset_postdata(); ?>
</section>
<section class="archive-results-list grid">
    <?php if ( have_posts() ):?>
        <div class="archive-results-list__summary">
            <h5 class="upper-header">Podcast</h5>
            <h2 class="archive-results-list__title">SAPIR Conversations</h2>
        </div>

        <div class="archive-results-list__header">
            <div class="count">
                <?php 
                    if($wp_query->found_posts == 1) {
                        $str = ' result';
                    } else {
                        $str = ' results';
                    }

                    echo $wp_query->found_posts . $str;
                ?>
            </div>

        </div>

        <?php while ( have_posts() ): the_post(); ?>

            <?php
                $args = ['p' => $post->ID];
                get_template_part('template-parts/global/teaser/teaser-conversation', null, $args);
            ?>

        <?php endwhile; ?>

        <?php get_template_part('template-parts/global/pagination'); ?>

    <?php else: ?>

        <div class="archive-results-list__summary">
            <h5 class="upper-header">There are 0 results for</h5>
            <h2 class="archive-results-list__title">“<?php echo get_search_query(); ?>”</h2>
        </div>

    <?php endif; ?>
    

</section>
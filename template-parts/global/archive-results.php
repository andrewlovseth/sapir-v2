<?php

    global $wp_query;

    if(is_tag()) {
        $title = single_tag_title("", false);
        $header = 'Theme';
    } else {
        $title = '“' . get_search_query() . '”';
        $header = 'Showing Results for';
    }

?>

<section class="archive-results-list grid">
    <?php if ( have_posts() ):?>
        <div class="archive-results-list__summary">
            <h5 class="upper-header"><?php echo $header; ?></h5>
            <h2 class="archive-results-list__title"><?php echo $title; ?></h2>
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
                get_template_part('template-parts/global/teaser/teaser-search-result', null, $args);
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


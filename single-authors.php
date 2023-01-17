<?php

$args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key' => 'author',
            'value' => '"' . get_the_ID() . '"',
            'compare' => 'LIKE'
        )
    )
);

$query = new WP_Query( $args );

get_header(); ?>


    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <section class="author-header grid">
            <?php if(get_the_post_thumbnail()): ?>
                <div class="author-header__photo">
                    <?php echo get_the_post_thumbnail($post, 'medium'); ?>
                </div>
            <?php endif; ?>

            <h1 class="author-header__title"><?php the_title(); ?></h1>

            <div class="author-header__copy copy copy-3">
                <?php the_content(); ?>
            </div>
        </section>

    <?php endwhile; endif; ?>

    <section class="archive-results-list grid">

        <div class="archive-results-list__header">
            <div class="count">
                <?php 
                    if($query->found_posts == 1) {
                        $str = ' result';
                    } else {
                        $str = ' results';
                    }

                    echo $query->found_posts . $str;
                ?>
            </div>
        </div>

        <?php if($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>

            <?php
                $args = ['p' => $post->ID];
                get_template_part('template-parts/global/teaser/teaser-search-result', null, $args);
            ?>

        <?php endwhile; endif; wp_reset_postdata(); ?>
    </section>


<?php get_footer(); ?>


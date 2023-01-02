<?php

$author_id = get_the_ID();

get_header(); ?>

    <section class="page-header grid">
        <h1 class="title"><?php the_title(); ?></h1>
    </section>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
        <section class="bio grid">
            <?php the_content(); ?>
        </section>
    <?php endwhile; endif; ?>

    <section class="index-section articles grid">
        <div class="section-header">
            <h2 class="sub-title">Articles</h2>
        </div>

        <div class="index-columns">
            <?php
                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 20,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'meta_query' => array(
                        array(
                            'key' => 'author',
                            'value' => '"' . $author_id . '"',
                            'compare' => 'LIKE'
                        )
                    )
                );
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

                <div class="article">
                    <?php $issue = get_field('issue'); if($issue): ?>
                        <h4><a href="<?php echo get_permalink($issue->ID); ?>"><?php the_field('volume', $issue); ?> &middot; <?php the_field('season', $issue); ?></a></h4>
                    <?php endif; ?>
                 
                    <?php if(get_field('display_title')): ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_field('display_title'); ?></a></h3>
                    <?php else: ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php endif; ?>
                </div>

            <?php endwhile; endif; wp_reset_postdata(); ?>
        </div>
    </section>


<?php get_footer(); ?>


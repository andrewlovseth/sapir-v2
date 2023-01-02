<?php get_header(); ?>

    <section class="page-header grid">
        <h1 class="title">Issues</h1>
    </section>

    <section class="issue-index grid">

        <?php
            $args = array(
                'post_type' => 'issue',
                'posts_per_page' => 25
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

            <div class="issue <?php echo sanitize_title_with_dashes(get_field('volume')); ?>">
                <h4><?php the_field('volume'); ?> &middot; <?php the_field('season'); ?> </h4>
                <h3 class="sub-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            </div>

        <?php endwhile; endif; wp_reset_postdata(); ?>    

    </section>

    <?php get_footer(); ?>
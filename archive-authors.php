<?php get_header(); ?>

    <section class="page-header grid">
        <h1 class="title">Authors</h1>
    </section>


    <section class="index-section authors grid">
        <div class="index-columns">
            <?php
                $args = array(
                    'post_type' => 'authors',
                    'posts_per_page' => 100,
                    'orderby' => 'title',
                    'order' => 'ASC'
                );
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

                <div class="author">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </div>

            <?php endwhile; endif; wp_reset_postdata(); ?>
        </div>
    </section>

<?php get_footer(); ?>
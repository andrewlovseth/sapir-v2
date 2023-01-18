<?php get_header(); ?>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <article <?php post_class('grid news'); ?>>
            <section class="news__header">
                <div class="news__meta">
                    <span class="date"><?php echo get_the_time('F j, Y') ?></span>
                </div>

                <div class="news__headline">
                    <h1 class="news__title"><?php the_title(); ?></h1>
                </div>
            </section>

            <section class="news__body copy copy-2">
                <?php the_content(); ?>
            </section>            
        </article>

    <?php endwhile; endif; ?>

<?php get_footer(); ?>
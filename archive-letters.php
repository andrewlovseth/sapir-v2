<?php get_header(); ?>

    <section class="page-header grid">
        <h1 class="title">Letters to the Editor</h1>
    </section>

    <section class="letters-list">

        <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

            <article class="letter grid">
                <div class="date-time">
                    <h4><?php the_time('F j, Y'); ?></h4>
                </div>

                <div class="letter-header">
                    <h2 class="letter-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                    <?php if(get_field('teaser_copy')): ?>
                        <div class="teaser copy">
                            <?php the_field('teaser'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

        <?php endwhile; endif; ?>

    </section>

<?php get_footer(); ?>


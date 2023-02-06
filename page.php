<?php get_header(); ?>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <article class="grid default-page">
            <section class="page-header default-page__header">
                <h1 class="title"><?php the_title(); ?></h1>
            </section>

            <section class="page-body default-page__body copy copy-2">
                <?php the_content(); ?>
            </section>
        </article>

    <?php endwhile; endif; ?>

<?php get_footer(); ?>
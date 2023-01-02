<?php get_header(); ?>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('grid post'); ?>>
            <section class="article-header">
                <div class="meta">
                    <span class="date"><?php the_time('j M Y'); ?></span>
                </div>

                <div class="headline">
                    <h1 class="title"><?php the_title(); ?></h1>
                </div>
            </section>

            <section class="article-body">
                <?php the_content(); ?>
            </section>            
        </article>

    <?php endwhile; endif; ?>

<?php get_footer(); ?>
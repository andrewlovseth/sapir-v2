<?php get_header(); ?>

    <section class="grid page-header">
        <h1 class="title">News</h1>
    </section>


    <section class="grid posts">
        
        <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="meta">
                    <span class="date"><?php the_time('j M Y'); ?></span>
                </div>
                
                <div class="info">
                    <div class="headline">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </div>

                    <div class="excerpt">
                    <p><?php echo get_the_excerpt(); ?></p>
                    </div>
                </div>
                
            </article>

        <?php endwhile; endif; ?>
        
    </section>

<?php get_footer(); ?>


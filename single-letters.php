<?php get_header(); ?>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <article class="letter-to-the-editor grid">
            <div class="letter-header">
                <h2 class="letter-title"><?php the_title(); ?></h2>
            </div>
    
            <?php get_template_part('templates/single-letters/response'); ?>

            <?php get_template_part('templates/single-letters/letter'); ?>

            <div class="back">
                <a href="<?php echo site_url('/letters/'); ?>">Back to Letters to the Editor</a>
            </div>
        </article>
   
    <?php endwhile; endif; ?>

<?php get_footer(); ?>
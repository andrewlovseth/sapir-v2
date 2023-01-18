<?php get_header(); ?>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <article class="letter grid">            
            <?php get_template_part('templates/single-letters/header'); ?>
    
            <?php get_template_part('templates/single-letters/editors-note'); ?>

            <?php get_template_part('templates/single-letters/letter'); ?>

            <?php get_template_part('templates/single-letters/back'); ?>
        </article>
   
    <?php endwhile; endif; ?>

<?php get_footer(); ?>
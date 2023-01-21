<?php 

/*
  
    Template Name: Newsletter

*/

get_header(); ?>

    <section class="newsletter grid">
        <?php get_template_part('templates/newsletter/info'); ?>

        <?php get_template_part('templates/newsletter/latest-issue'); ?>
        
        <?php get_template_part('templates/newsletter/archive'); ?>
    </section>

<?php get_footer(); ?>
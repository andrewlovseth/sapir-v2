<?php 

/*
  
    Template Name: Landing Page (Issue)

*/

get_header(); ?>

    <section class="landing-page landing-page__issue grid">
        
        <?php get_template_part('templates/landing-page-issue/cover'); ?>

        <?php get_template_part('templates/landing-page-issue/contents'); ?>
        
    </section>

<?php get_footer(); ?>
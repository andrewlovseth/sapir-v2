<?php 

/*
  
    Template Name: Home

*/

get_header(); ?>

    <?php get_template_part('templates/home/latest'); ?>

    <?php get_template_part('templates/home/newsletter'); ?>

    <?php get_template_part('templates/home/explore'); ?>
    
    <?php get_template_part('templates/home/curated-articles'); ?>

    <?php get_template_part('templates/home/quote'); ?>

<?php get_footer(); ?>
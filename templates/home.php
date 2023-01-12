<?php 

/*
  
    Template Name: Home

*/

get_header(); ?>

    <?php get_template_part('templates/home/latest'); ?>

    <?php get_template_part('templates/home/explore'); ?>

    <?php get_template_part('template-parts/global/subscribe'); ?>

    <?php get_template_part('templates/home/most-read-departures'); ?>

    <?php get_template_part('templates/home/quote'); ?>


<?php get_footer(); ?>
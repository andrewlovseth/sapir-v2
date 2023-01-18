<?php 

/*
  
    Template Name: Contact

*/

get_header(); ?>

    <?php get_template_part('template-parts/global/page-header'); ?>

    <section class="page-body grid">
        <?php get_template_part('templates/contact/contact-form'); ?>

        <?php get_template_part('templates/contact/faqs'); ?>
    </section>

<?php get_footer(); ?>
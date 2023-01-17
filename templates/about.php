<?php 

/*
  
    Template Name: About

*/

get_header(); ?>

    <?php get_template_part('template-parts/global/page-header'); ?>

    <section class="about grid">
        <div class="about__copy copy copy-2">
            <?php the_field('about'); ?>
        </div>

        <?php if(have_rows('masthead')): ?>
            <div class="about__masthead">
                <?php while(have_rows('masthead')): the_row(); ?>

                    <div class="role">
                        <span class="name"><?php the_sub_field('name'); ?></span>
                        <span class="job-title"><?php the_sub_field('title'); ?></span>
                    </div>

                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>


<?php get_footer(); ?>


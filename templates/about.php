<?php 

/*
  
    Template Name: About

*/

get_header(); ?>

    <?php get_template_part('template-parts/global/page-header'); ?>

    <section class="about-content grid">
        <article class="post">
            <div class="info article-body letters-body">
                <?php the_field('about'); ?>
            </div>
        </article>

        <div class="masthead">
            <div class="section-header">
                <h3 class="sub-title">Masthead</h3>
            </div>

            <div class="list">
                <?php if(have_rows('masthead')): while(have_rows('masthead')): the_row(); ?>
    
                    <div class="role">
                        <span class="name"><?php the_sub_field('name'); ?></span>
                        <span class="job-title"><?php the_sub_field('title'); ?></span>
                    </div>

                <?php endwhile; endif; ?>
            </div>
        </div>
    </section>


<?php get_footer(); ?>


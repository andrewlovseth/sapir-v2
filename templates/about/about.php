<?php

    $about = get_field('about');

    $newsletter = get_field('newsletter', 'options');
    $embed = $newsletter['embed'];
?>

<section class="about grid">
    <div class="about__copy copy copy-2">
        <?php echo $about; ?>

        <div class="newsletter">
            <?php echo do_shortcode($embed); ?>
        </div>

        <?php get_template_part('templates/about/quote'); ?>        
    </div>

    <?php if(have_rows('masthead')): ?>
        <div class="about__masthead">
            <?php while(have_rows('masthead')): the_row(); ?>

                <div class="role">
                    <span class="name"><?php echo get_sub_field('name'); ?></span>
                    <span class="job-title"><?php echo get_sub_field('title'); ?></span>
                </div>

            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</section>
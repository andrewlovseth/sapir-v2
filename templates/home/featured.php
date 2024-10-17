<section class="below-fold grid">
    <div class="below-fold__wrapper">
        <?php if(have_rows('featured')): ?>
            <div class="featured">
                <?php while(have_rows('featured')) : the_row(); ?>

                    <?php get_template_part('templates/home/featured-conversation'); ?>

                    <?php get_template_part('templates/home/featured-event'); ?>

                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <?php get_template_part('templates/home/newsletter'); ?>
    </div>
</section>



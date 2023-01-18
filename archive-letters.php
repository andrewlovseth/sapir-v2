<?php get_header(); ?>

    <section class="page-header grid">
        <h1 class="section-title-alt">Letters to the Editor</h1>
    </section>

    <?php if ( have_posts() ): ?>
        <section class="letters grid">

            <?php while ( have_posts() ): the_post(); ?>

                <?php get_template_part('templates/archive-letters/letter'); ?>

            <?php endwhile; ?>
            
            <?php get_template_part('template-parts/global/pagination'); ?>
        </section>
    <?php endif; ?>

<?php get_footer(); ?>


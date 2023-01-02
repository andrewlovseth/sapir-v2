<?php

// Article Class Name
$className = 'post grid';
if( get_field('dropcap') ) {
    $className .= ' dropcap';
}

$issue = get_field('issue'); 
$volume = get_field('volume', $issue->ID);
$issue_slug = sanitize_title_with_dashes($volume);
if($issue_slug) {
    $className .= ' ' . $issue_slug;
}

get_header(); ?>

    <?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

        <article class="<?php echo esc_attr($className); ?>">
            <?php get_template_part('templates/single-post/article-header'); ?>

            <?php get_template_part('templates/single-post/article-body'); ?>

            <?php get_template_part('templates/single-post/article-footer'); ?>
        </article>
   
    <?php endwhile; endif; ?>

<?php get_footer(); ?>
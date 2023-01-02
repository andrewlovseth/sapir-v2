<?php

$issue = get_field('issue'); 
$volume = get_field('volume', $issue->ID);
$issue_slug = sanitize_title_with_dashes($volume);

get_header(); ?>

    <section class="table-of-contents grid <?php echo $issue_slug; ?>">

        <?php get_template_part('templates/single-issue/header'); ?>

        <?php get_template_part('templates/single-issue/contents'); ?>

    </section>

<?php get_footer(); ?>
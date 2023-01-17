<?php

$issue = get_field('issue'); 
$volume = get_field('volume', $issue);
$issue_slug = sanitize_title_with_dashes($volume);

get_header(); ?>

    <section class="issue <?php echo $issue_slug; ?> grid">

        <?php get_template_part('templates/single-issue/header'); ?>

        <?php get_template_part('templates/single-issue/contents'); ?>

    </section>

<?php get_footer(); ?>
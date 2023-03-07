<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $p = $args['p']; 
    }

    if(get_field('display_title', $p)) {
        $title = get_field('display_title', $p);
    } else {
        $title = get_the_title($p);
    }

    $permalink = get_permalink($p);
    $authors = get_field('author', $p);
    $authors_count = count((array)$authors);

    $interviewers = get_field('interviewers', $p);    
    $interviewers_count = count((array)$interviewers);
?>


<article class="teaser teaser-small">
    <?php
        $args = ['title' => $title, 'permalink' => $permalink];
        get_template_part('template-parts/global/teaser/headline', null, $args);
    ?>

    <?php
        $args = ['authors' => $authors, 'authors_count' => $authors_count];
        get_template_part('template-parts/global/teaser/authors', null, $args);
    ?>

    <?php
        $args = ['interviewers' => $interviewers, 'interviewers_count' => $interviewers_count];
        get_template_part('template-parts/global/teaser/interviewers', null, $args);
    ?>
</article>
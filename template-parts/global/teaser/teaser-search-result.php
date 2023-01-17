<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $p = $args['p']; 
    }

    $issue = get_field('issue', $p);
    $volume = get_field('volume', $issue->ID);
    $season = get_field('season', $issue->ID);


    if(get_field('display_title', $p)) {
        $title = get_field('display_title', $p);
    } else {
        $title = get_the_title($p);
    }

    $permalink = get_permalink($p);
    $dek = get_field('dek', $p);
    $authors = get_field('author', $p);    
    $authors_count = count((array)$authors);
?>


<article class="teaser teaser-large teaser-search-result">
    <?php
        $args = ['issue'=> $issue, 'volume' => $volume, 'season' => $season];
        get_template_part('template-parts/global/teaser/search-results-meta', null, $args);
    ?>

    <?php
        $args = ['title' => $title, 'permalink' => $permalink];
        get_template_part('template-parts/global/teaser/headline', null, $args);
    ?>
    
    <?php
        $args = ['dek' => $dek];
        get_template_part('template-parts/global/teaser/dek', null, $args);
    ?>
    
    <?php
        $args = ['authors' => $authors, 'authors_count' => $authors_count];
        get_template_part('template-parts/global/teaser/authors', null, $args);
    ?>
</article>

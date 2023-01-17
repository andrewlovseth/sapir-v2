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

    $alt_display = get_sub_field('alternate_display', $p);
    $status = get_post_status($p);

    $className = 'teaser teaser-small teaser-contents';

    if( $alt_display ) {
        $className .= ' alt';
    }
    
    if( $status == 'draft' || $status == 'future' ) {
        $className .= ' draft';
    }            

?>


<article class="<?php echo $className; ?>">
    <?php
        $args = ['title' => $title, 'permalink' => $permalink];
        get_template_part('template-parts/global/teaser/headline', null, $args);
    ?>

    <?php
        $args = ['authors' => $authors, 'authors_count' => $authors_count];
        get_template_part('template-parts/global/teaser/authors', null, $args);
    ?>

    <?php
        $args = ['status' => $status, 'p' => $p];
        get_template_part('template-parts/global/teaser/status', null, $args);
    ?>
</article>
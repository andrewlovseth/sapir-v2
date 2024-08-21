<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $title = $args['title']; 
        $permalink = $args['permalink']; 
    }
?>

<div class="teaser__headline">
    <h3 class="teaser__title"><a class="teaser__title-link" href="<?php echo $permalink; ?>"><?php echo $title; ?></a></h3>
</div>
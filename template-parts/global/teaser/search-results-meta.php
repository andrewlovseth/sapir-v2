<?php
    $args = wp_parse_args($args);

    if(!empty($args)) {
        $issue = $args['issue']; 
        $volume = $args['volume']; 
        $season = $args['season']; 
    }
?>

<div class="teaser__meta">
    <a href="<?php echo get_permalink($issue->ID); ?>">
        <div class="teaser__meta-wrapper">
            <span class="volume"><?php echo $volume; ?></span> <span class="season"><?php echo $season; ?></span>
        </div>
    </a>
</div>
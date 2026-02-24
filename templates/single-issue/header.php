<?php

    $volume = get_field('volume');
    $season = get_field('season');
    $title = get_the_title();

?>

<div class="issue__header">

    <div class="issue__info">
        <div class="issue__headline">
            <span class="pre-header upper-header">Contents of <?php echo get_field('issue_sub_header') ?: 'The Issue On'; ?></span>
            <h1 class="issue__title"><?php echo $title ?></h1>
        </div>

        <div class="ornament">
            <?php get_template_part('svg/icon-ornament'); ?>
        </div>

        <div class="issue__meta">
            <span class="volume"><?php echo $volume; ?></span> <span class="season"><?php echo $season; ?></span>        
        </div>

    </div>
</div>
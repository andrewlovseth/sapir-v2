<?php

    $latest = get_field('latest');
    $current_issue = $latest['current_issue'];
    $current_issue_id = $current_issue->ID;

    $issue_title = get_the_title($current_issue_id);
    $issue_link = get_permalink($current_issue_id);
    $volume = get_field('volume', $current_issue_id);
    $season = get_field('season', $current_issue_id);
    $cover = get_field('cover', $current_issue_id);

?>


<div class="issue">
    <div class="header">
        <h3>
            <span class="upper-header">The Issue On</span>
            <a href="<?php echo $issue_link; ?>" class="issue__title"><?php echo $issue_title; ?></a>
        </h3>

        <div class="divider"></div>

        <div class="meta">
            <span class="small-upper volume"><?php echo $volume; ?></span>
            <span class="small-upper season"><?php echo $season; ?></span>
        </div>
    </div>

    <div class="cover">
        <a href="<?php echo $issue_link; ?>">
            <?php echo wp_get_attachment_image($cover['ID'], 'medium'); ?>
        </a>
    </div>

    <div class="cta center">
        <a href="<?php echo $issue_link; ?>" class="btn small-upper">Read it now</a>
    </div>

</div>
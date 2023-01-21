<?php

    $latest_issue = get_field('latest_issue');
    $issue = $latest_issue['issue'];
    $copy = $latest_issue['copy'];

    $link = get_permalink($issue->ID);
    $cover = get_field('cover', $issue->ID);

?>

<div class="latest-issue">
    <div class="latest-issue__cover">
        <a href="<?php echo $link; ?>">
            <?php echo wp_get_attachment_image($cover['ID'], 'medium'); ?>
        </a>
    </div>

    <div class="latest-issue__copy copy copy-1">
        <?php echo $copy; ?>
    </div>

    <div class="latest-issue__cta cta">
        <a href="<?php echo $link; ?>" class="btn small-upper">Read it now</a>
    </div>
</div>
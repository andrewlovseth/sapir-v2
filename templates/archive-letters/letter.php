<?php

    $date = get_the_time('F j, Y');
    $permalink = get_the_permalink();
    $title = get_the_title();
    $teaser = get_field('teaser_copy');

?>

<article class="letter">
    <div class="letter__meta">
        <span class="date"><?php echo $date; ?></span>
    </div>

    <div class="letter__header">
        <h3 class="letter__title"><a href="<?php echo $permalink; ?>"><?php echo $title; ?></a></h3>
    </div>

    <?php if($teaser): ?>
        <div class="letter__teaser copy copy-2">
            <?php echo $teaser; ?>
        </div>
    <?php endif; ?>
</article>
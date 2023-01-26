<?php

    $quote = get_field('quote');
    $header = $quote['header'];
    $text = $quote['text'];
    $source = $quote['source'];

?>

<div class="quote">
    <blockquote>
        <div class="quote__header">
            <h2 class="section-title sans-serif"><?php echo $header; ?></h2>
        </div>

        <div class="quote__text">
            <?php echo $text; ?>
        </div>

        <div class="quote__meta">
            <span class="quote__source">&mdash; <?php echo $source; ?>
        </div>
    </blockquote>
</div>
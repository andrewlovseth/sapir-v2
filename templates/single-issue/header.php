<?php

    $cover = get_field('cover');
    $volume = get_field('volume');
    $season = get_field('season');
    $title = get_the_title();
    $pdf_link = get_field('full_issue_pdf');


?>

<div class="issue__header">
    <div class="cover">
        <?php echo wp_get_attachment_image($cover['ID'], 'full'); ?>
    </div>

    <div class="issue__info">
        <div class="issue__meta">
            <span class="volume"><?php echo $volume; ?></span> <span class="season"><?php echo $season; ?></span>        
        </div>

        <div class="issue__headline">
            <h1 class="issue__title"><?php echo $title ?></h1>
        </div>

        <?php if($pdf_link): ?>
            <div class="issue__cta cta">
                <a href="<?php echo $pdf_link; ?>" target="window" class="btn small-upper">Download PDF</a>
            </div>
        <?php endif; ?>
    </div>
</div>
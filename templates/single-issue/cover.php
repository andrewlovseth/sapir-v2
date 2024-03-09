<?php

    $cover = get_field('cover');
    $pdf_link = get_field('full_issue_pdf');

?>

<div class="issue__cover">
    <div class="issue__cover-image">
        <?php echo wp_get_attachment_image($cover['ID'], 'full'); ?>
    </div>

    <?php if($pdf_link): ?>
        <div class="issue__cta cta">
            <a href="<?php echo $pdf_link; ?>" target="window" class="btn small-upper">Download PDF</a>
        </div>
    <?php endif; ?>

    <div class="newsletter-form">
        <?php echo do_shortcode('[optin-monster-inline slug="sktztimql2qui8uzuxll"]'); ?>
    </div>
</div>
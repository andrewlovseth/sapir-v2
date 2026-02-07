<?php

    $newsletter = get_field('newsletter');
    $icon = $newsletter['icon'];
    $headline = $newsletter['headline'];
    $embed = $newsletter['embed'];


?>

<section class="newsletter">
    <div class="newsletter__container">
        <div class="newsletter__content">
            <div class="newsletter__icon">
                <?php echo wp_get_attachment_image($icon['ID'], 'full'); ?>
            </div>

            <div class="newsletter__copy copy copy-2">
                <p><?php echo $headline; ?></p>
            </div>

            <?php if($embed): ?>
                <?php echo do_shortcode($embed); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php

    $info = get_field('info');
    $headline = $info['headline'];
    $copy = $info['copy'];

    $newsletter = get_field('newsletter', 'options');
    $embed = $newsletter['embed'];


?>

<div class="info">
    <div class="info__header">
        <h1 class="info__title"><?php echo $headline; ?></h1>
    </div>

    <div class="info__copy copy copy-1">
        <?php echo $copy; ?>
    </div>
    
    <?php get_template_part('template-parts/global/inline-newsletter-form-default'); ?>
</div>
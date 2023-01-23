<?php

    $letters = get_field('letters', 'options');
    $icon = $letters['icon'];
    $page_title = $letters['page_title'];
    $copy = $letters['copy'];

?>

<section class="page-header grid">
    <div class="icon">
        <?php echo wp_get_attachment_image($icon['ID'], 'medium'); ?>
    </div>
    
    <h1 class="section-title-alt"><?php echo $page_title; ?></h1>

    <div class="copy copy-2">
        <?php echo $copy; ?>
    </div>
</section>
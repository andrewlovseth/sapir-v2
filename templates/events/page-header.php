<?php

    $page_header = get_field('page_header');
    $icon = $page_header['icon'];
    $page_title = $page_header['page_title'];

?>

<section class="page-header grid">
    <div class="icon">
        <?php echo wp_get_attachment_image($icon['ID'], 'medium'); ?>
    </div>
    
    <h1 class="section-title-alt"><?php echo $page_title; ?></h1>
</section>
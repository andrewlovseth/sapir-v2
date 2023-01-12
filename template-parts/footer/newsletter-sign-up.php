<?php

    $newsletter = get_field('newsletter', 'options');
    $icon = $newsletter['icon'];
    $headline = $newsletter['headline'];
    $embed = $newsletter['embed'];


?>

<section class="newsletter grid">
    <div class="newsletter__wrapper">

        <div class="section-header">
            <?php echo get_svg($icon['url']); ?>
            <h3><?php echo $headline; ?></h3>
        </div>

        <?php echo $embed; ?>

    </div>
</section>
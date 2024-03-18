<?php

    $newsletter = get_field('newsletter', 'options');
    $icon = $newsletter['icon'];
    $headline = $newsletter['headline'];
    $embed = $newsletter['embed'];


?>

<section class="newsletter grid">
    <div class="newsletter__wrapper">

        <?php get_template_part('template-parts/global/inline-newsletter-form-slim'); ?>

    </div>
</section>
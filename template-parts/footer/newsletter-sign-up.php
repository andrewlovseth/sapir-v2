<?php

    $newsletter = get_field('newsletter', 'options');
    $headline = $newsletter['headline'];
    $embed = $newsletter['embed'];

?>

<section class="newsletter-sign-up grid">
    <div class="newsletter-wrapper">
        <div class="section-header">
            <h3><?php echo $headline; ?></h3>
        </div>

        <?php echo $embed; ?>
    </div>
</section>
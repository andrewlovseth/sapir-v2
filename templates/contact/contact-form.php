<div class="contact-form">
    <div class="form-header">
        <?php the_field('copy'); ?>
    </div>

    <?php
        $shortcode = get_field('shortcode');
        echo do_shortcode($shortcode);
    ?>
</div>
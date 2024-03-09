<?php

    $shortcode = get_field('default_inline_form', 'options');

?>

<div class="inline-newsletter-form__default">
    <?php echo do_shortcode($shortcode); ?>
</div>
<?php

    $shortcode = get_field('default_inline_slim_form', 'options');

?>

<div class="inline-newsletter-form__slim">
    <?php echo do_shortcode($shortcode); ?>
</div>
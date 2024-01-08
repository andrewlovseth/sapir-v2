<?php

/*

    Newsletter Signup

*/

// Create id attribute allowing for custom "anchor" value.
$id = 'newsletter-signup-' . $block['id'];

if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'newsletter-signup';

if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}
if( $is_preview ) {
    $className .= ' is-admin';
}

$newsletter = get_field('newsletter', 'options');
$icon = $newsletter['icon'];
$copy = $newsletter['block_copy'];
$embed = $newsletter['embed'];

?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <div class="newsletter-signup__info">
        <div class="newsletter-signup__icon">
            <?php echo wp_get_attachment_image($icon['ID'], 'full'); ?>
        </div>

        <div class="newsletter-signup__copy">
            <?php echo $copy; ?>
        </div>
    </div>

    <div class="newsletter-signup__form">
        <?php echo $embed; ?>
    </div>
</div>
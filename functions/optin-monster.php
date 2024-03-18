<?php

function wp_insert_shortcode_after_fifth_paragraph($content) {
    if (!is_single()) return $content; // Ensure this runs on single posts only

    $paragraphs = explode('</p>', $content);
    $paragraphCount = count($paragraphs);
    $shortcode = get_field('default_inline_slim_form', 'options');

    // Only proceed if there are more than 9 paragraphs
    if ($paragraphCount > 9) {
        $paragraphs[9] .= do_shortcode($shortcode); // Insert the shortcode after the fifth paragraph
    }

    return implode('</p>', $paragraphs);
}

add_filter('the_content', 'wp_insert_shortcode_after_fifth_paragraph');
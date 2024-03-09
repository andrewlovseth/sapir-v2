<?php

function wp_insert_shortcode_after_fifth_paragraph($content) {
    if (!is_single()) return $content; // Ensure this runs on single posts only

    $paragraphs = explode('</p>', $content);
    $paragraphCount = count($paragraphs);

    // Only proceed if there are more than 5 paragraphs
    if ($paragraphCount > 5) {
        $paragraphs[5] .= get_template_part('template-parts/global/inline-newsletter-form-default'); // Insert the shortcode after the fifth paragraph
    }

    return implode('</p>', $paragraphs);
}

add_filter('the_content', 'wp_insert_shortcode_after_fifth_paragraph');
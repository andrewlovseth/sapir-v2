<?php

function wrap_sapir_in_span( $content ) {
    // Use preg_replace with the 'i' modifier for case-insensitive matching.
    $content = preg_replace( '/\bsapir\b/i', '<span class="small-caps">$0</span>', $content );
    return $content;
}

add_filter( 'the_content', 'wrap_sapir_in_span' );
<?php

function wrap_sapir_in_span( $content ) {
    // Use preg_replace with the 'i' modifier for case-insensitive matching.
    $content = preg_replace( '/\bsapir\b/i', '<span class="small-caps">$0</span>', $content );


    $pattern = '/(A\.M\.|P\.M\.)/i';
    $replacement = '<span class="small-caps lower">$0</span>';
    $content = preg_replace( $pattern, $replacement, $content );

    return $content;
}
add_filter( 'the_title', 'wrap_sapir_in_span', 10 );

add_filter( 'the_content', 'wrap_sapir_in_span', 10 );
add_filter( 'get_the_excerpt', 'wrap_sapir_in_span', 10 );

add_filter( 'acf/load_value/key=field_63c78da692d45', 'wrap_sapir_in_span', 10 );
add_filter( 'acf/load_value/key=field_63cc5065418fe', 'wrap_sapir_in_span', 10 );
add_filter( 'acf/load_value/key=field_6086ed537d153', 'wrap_sapir_in_span', 10 );
add_filter( 'acf/load_value/key=field_63bf69d90e5ad', 'wrap_sapir_in_span', 10 );




function wrap_sapir_in_span_array( $content ) {
    // Use preg_replace with the 'i' modifier for case-insensitive matching.
    $content = preg_replace( '/\bsapir\b/i', '<span class="small-caps">$0</span>', $content );

    return $content;
}


add_filter('acf/load_value/key=field_63c59df0c760d', 'wrap_sapir_in_span_array');

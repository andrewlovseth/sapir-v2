<?php

function wrap_sapir_in_span( $content ) {
    // Only operate on text outside of HTML tags so attributes like href are not modified.
    $parts = preg_split( '/(<[^>]+>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
    if ( $parts === false ) {
        $parts = array( $content );
    }

    foreach ( $parts as $index => $part ) {
        // Even indexes are text nodes when splitting with the above regex
        if ( $index % 2 === 0 ) {
            $part = preg_replace( '/\bsapir\b/i', '<span class="small-caps">$0</span>', $part );
            $part = preg_replace( '/(A\.M\.|P\.M\.)/i', '<span class="small-caps lower">$0</span>', $part );
            $parts[ $index ] = $part;
        }
    }

    return implode( '', $parts );
}

// Only run content filters on frontend, not in admin
if (!is_admin()) {
    add_filter( 'the_title', 'wrap_sapir_in_span', 10 );
    add_filter( 'the_content', 'wrap_sapir_in_span', 10 );
    add_filter( 'get_the_excerpt', 'wrap_sapir_in_span', 10 );
}

// Only run ACF load filters on frontend or specific admin contexts
function wrap_sapir_in_span_conditional($content) {
    // Skip processing in admin except for specific contexts where it might be needed
    if (is_admin() && !wp_doing_ajax() && !isset($_GET['action'])) {
        return $content;
    }
    return wrap_sapir_in_span($content);
}

add_filter( 'acf/load_value/key=field_63c78da692d45', 'wrap_sapir_in_span_conditional', 10 );
add_filter( 'acf/load_value/key=field_63cc5065418fe', 'wrap_sapir_in_span_conditional', 10 );
add_filter( 'acf/load_value/key=field_6086ed537d153', 'wrap_sapir_in_span_conditional', 10 );
add_filter( 'acf/load_value/key=field_63bf69d90e5ad', 'wrap_sapir_in_span_conditional', 10 );
add_filter( 'acf/load_value/key=field_690a9ab19aff8', 'wrap_sapir_in_span_conditional', 10 );

add_filter( 'acf/load_value/key=field_690a9aac9aff7', 'wrap_sapir_in_span_conditional', 10 );


function wrap_sapir_in_span_array( $content ) {
    // Use preg_replace with the 'i' modifier for case-insensitive matching.
    $content = preg_replace( '/\bsapir\b/i', '<span class="small-caps">$0</span>', $content );

    return $content;
}

// Only run array filter on frontend
if (!is_admin()) {
    add_filter('acf/load_value/key=field_63c59df0c760d', 'wrap_sapir_in_span_array');
}

<?php

/*
	Enqueue Styles & Scripts
*/


// Preconnect to external origins (DNS + TLS handshake early)
function sapir_preconnect_hints() {
    echo '<link rel="preconnect" href="https://use.typekit.net" crossorigin>';
    echo '<link rel="preconnect" href="https://code.jquery.com" crossorigin>';
    if (is_singular('post')) {
        echo '<link rel="preconnect" href="https://www.sefaria.org" crossorigin>';
    }
}
add_action('wp_head', 'sapir_preconnect_hints', 1);

// Enqueue custom styles and scripts
function bearsmith_enqueue_styles_and_scripts() {
    // Register and noConflict jQuery 3.4.1
    wp_register_script( 'jquery.3.4.1', 'https://code.jquery.com/jquery-3.4.1.min.js', array(), null, true );
    wp_add_inline_script( 'jquery.3.4.1', 'var jQuery = $.noConflict(true);' );


	$uri = get_stylesheet_directory_uri();
    $dir = get_stylesheet_directory();

    $script_last_updated_at = filemtime($dir . '/js/site.js');
    $style_last_updated_at = filemtime($dir . '/style.css');

    // TypeKit loaded from HTML (not CSS @import) so browser discovers it in parallel with style.css
    wp_enqueue_style( 'adobe-fonts', 'https://use.typekit.net/pmv6jwg.css' );
    wp_add_inline_style( 'adobe-fonts',
        '@font-face { font-family: psfournier-std; font-display: swap; }' .
        '@font-face { font-family: psfournier-std-grand; font-display: swap; }'
    );
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', '', $style_last_updated_at );

    // Add plugins.js & site.js (with jQuery dependency)
    wp_enqueue_script( 'custom-plugins', get_stylesheet_directory_uri() . '/js/plugins.js', array( 'jquery.3.4.1' ), $script_last_updated_at, true );
    wp_enqueue_script( 'custom-site', get_stylesheet_directory_uri() . '/js/site.js', array( 'jquery.3.4.1' ), $script_last_updated_at, true );

    // Newsletter form handler (no jQuery dependency)
    $newsletter_last_updated = filemtime($dir . '/js/newsletter.js');
    wp_enqueue_script( 'sapir-newsletter', get_stylesheet_directory_uri() . '/js/newsletter.js', array(), $newsletter_last_updated, true );
}
add_action( 'wp_enqueue_scripts', 'bearsmith_enqueue_styles_and_scripts' );

// Sefaria linker — auto-links Torah references in article text
function sapir_enqueue_sefaria() {
    if (!is_singular('post')) return;
    wp_enqueue_script('sefaria-linker', 'https://www.sefaria.org/linker.js', array(), null, true);
    wp_add_inline_script('sefaria-linker', 'sefaria.link({selector: ".site-content .article-body, .simple-epigraph"});');
}
add_action('wp_enqueue_scripts', 'sapir_enqueue_sefaria');

// Load TypeKit CSS without blocking render — downloads in background, swaps on load
add_filter('style_loader_tag', 'sapir_nonblocking_typekit', 10, 4);
function sapir_nonblocking_typekit($tag, $handle, $href, $media) {
    if ($handle !== 'adobe-fonts') return $tag;
    return str_replace("media='all'", "media='print' onload=\"this.media='all'\"", $tag);
}
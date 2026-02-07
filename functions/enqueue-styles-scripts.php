<?php

/*
	Enqueue Styles & Scripts
*/


// Enqueue custom styles and scripts
function bearsmith_enqueue_styles_and_scripts() {
    // Register and noConflict jQuery 3.4.1
    wp_register_script( 'jquery.3.4.1', 'https://code.jquery.com/jquery-3.4.1.min.js' );
    wp_add_inline_script( 'jquery.3.4.1', 'var jQuery = $.noConflict(true);' );


	$uri = get_stylesheet_directory_uri();
    $dir = get_stylesheet_directory();

    $script_last_updated_at = filemtime($dir . '/js/site.js');
    $style_last_updated_at = filemtime($dir . '/style.css');

    // Add style.css and third-party css
    // wp_enqueue_style( 'adobe-fonts', 'https://use.typekit.net/vcx3lxt.css' );
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', '', $style_last_updated_at );

    // Add plugins.js & site.js (with jQuery dependency)
    wp_enqueue_script( 'custom-plugins', get_stylesheet_directory_uri() . '/js/plugins.js', array( 'jquery.3.4.1' ), $script_last_updated_at, true );
    wp_enqueue_script( 'custom-site', get_stylesheet_directory_uri() . '/js/site.js', array( 'jquery.3.4.1' ), $script_last_updated_at, true );

    // Newsletter form handler (no jQuery dependency)
    $newsletter_last_updated = filemtime($dir . '/js/newsletter.js');
    wp_enqueue_script( 'sapir-newsletter', get_stylesheet_directory_uri() . '/js/newsletter.js', array(), $newsletter_last_updated, true );
}
add_action( 'wp_enqueue_scripts', 'bearsmith_enqueue_styles_and_scripts' );
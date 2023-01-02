<?php

/*
	Theme Support
*/


// Remove Admin bar from front-end
show_admin_bar(false);


// Theme Support for title tags, post thumbnails, HTML5 elements, feed links
add_theme_support('title-tag');


//Enable support for Post Thumbnails on posts and pages.
add_theme_support('post-thumbnails');


// Set Thumbnail Sizes
update_option( 'thumbnail_size_w', 400 );
update_option( 'thumbnail_size_h', 400 );
update_option( 'thumbnail_crop', 1 );

// Switch default core markup for search form, comment form, and comments to output valid HTML5.
add_theme_support('html5', array(
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption'
));


// Add default posts and comments RSS feed links to head.
add_theme_support( 'automatic-feed-links' );


// Add support for core custom logo.
add_theme_support('custom-logo', array(
    'height'      => 250,
    'width'       => 250,
    'flex-width'  => true,
    'flex-height' => true,
));


// Add wp_body_open
if ( ! function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
}


// Add SVG Support
function bearsmith_add_svg_support($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'bearsmith_add_svg_support');


// Remove Comment
function bearsmith_remove_comments_from_admin_menu() {
  remove_menu_page( 'edit-comments.php' );
}
add_action('admin_menu', 'bearsmith_remove_comments_from_admin_menu');


// Remove unneccesarry header info
function bearsmith_remove_header_info() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'wp_resource_hints', 2 );
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'adjacent_posts_rel_link');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'bearsmith_remove_header_info');




// Remove WP-embed.js
function bearsmith_remove_wp_embed_js() {
    if (!is_admin()) {
        wp_deregister_script('wp-embed');
    }
}
add_action('init', 'bearsmith_remove_wp_embed_js');



function bearsmith_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Articles';
    $submenu['edit.php'][5][0] = 'All Articles';
    $submenu['edit.php'][10][0] = 'Add Article';
}
add_action( 'admin_menu', 'bearsmith_change_post_label' );

function bearsmith_change_post_object() {
    global $wp_post_types;
    $labels = $wp_post_types['post']->labels;
    $labels->name = 'Articles';
    $labels->singular_name = 'Articles';
    $labels->add_new = 'Add Article';
    $labels->add_new_item = 'Add Article';
    $labels->edit_item = 'Edit Article';
    $labels->new_item = 'Article';
    $labels->view_item = 'View Articles';
    $labels->search_items = 'Search Articles';
    $labels->not_found = 'No Articles found';
    $labels->not_found_in_trash = 'No Articles found in Trash';
    $labels->all_items = 'All Articles';
    $labels->menu_name = 'Articles';
    $labels->name_admin_bar = 'Articles';

    $wp_post_types['post']->menu_icon = 'dashicons-text-page';
} 
add_action( 'init', 'bearsmith_change_post_object' );
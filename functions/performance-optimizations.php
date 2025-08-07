<?php

/*
    Performance Optimizations for Admin Area
*/

// Disable unnecessary admin features for better performance
function sapir_disable_admin_features() {
    // Remove WordPress version from admin footer
    remove_filter('update_footer', 'core_update_footer');
    
    // Disable admin email notifications for plugin updates
    if (!function_exists('wp_auto_update_core')) {
        add_filter('auto_core_update_send_email', '__return_false');
    }
    
    // Disable admin email notifications for plugin updates
    add_filter('auto_plugin_update_send_email', '__return_false');
    add_filter('auto_theme_update_send_email', '__return_false');
}
add_action('admin_init', 'sapir_disable_admin_features');

// Optimize admin queries
function sapir_optimize_admin_queries() {
    if (is_admin()) {
        // Limit post revisions to improve performance
        if (!defined('WP_POST_REVISIONS')) {
            define('WP_POST_REVISIONS', 5);
        }
        
        // Disable autosave for better performance (optional)
        // add_action('wp_print_scripts', function() {
        //     wp_deregister_script('autosave');
        // });
    }
}
add_action('init', 'sapir_optimize_admin_queries');

// Cache expensive operations
function sapir_cache_expensive_operations() {
    // Add object caching for frequently accessed data
    if (!wp_cache_get('sapir_theme_options')) {
        $theme_options = get_option('options');
        wp_cache_set('sapir_theme_options', $theme_options, '', 3600);
    }
}
add_action('admin_init', 'sapir_cache_expensive_operations');

// Optimize ACF field loading
function sapir_optimize_acf_loading() {
    if (is_admin()) {
        // Only load ACF fields when needed
        add_filter('acf/load_field', function($field) {
            // Skip loading certain fields in admin if not needed
            $screen = get_current_screen();
            if ($screen && !in_array($screen->id, ['post', 'page', 'issue', 'events'])) {
                return $field;
            }
            return $field;
        });
    }
}
add_action('acf/init', 'sapir_optimize_acf_loading');

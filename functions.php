<?php

require_once( plugin_dir_path( __FILE__ ) . '/functions/theme-support.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/enqueue-styles-scripts.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/acf.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/register-blocks.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/disable-gutenberg-editor.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/sapir.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/disable-author-pages.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/optin-monster.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/performance-optimizations.php');

require_once( plugin_dir_path( __FILE__ ) . '/functions/newsletter-shortcode.php');

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once( plugin_dir_path( __FILE__ ) . '/functions/cli-commands.php');
}

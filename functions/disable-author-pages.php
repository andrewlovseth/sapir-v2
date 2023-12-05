<?php


// Disable author pages
function disable_author_pages() {
    if (is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        // Redirect to homepage or another page
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'disable_author_pages');
<?php

/*
    Advanced Custom Fields
*/

// Order Relationship fields
function bearsmith_relationship_order_by_date($args, $field, $post_id) {
    // Only modify if we're in a context where ordering matters
    if (is_admin() && !wp_doing_ajax()) {
        // In admin, only apply ordering for specific contexts
        $screen = get_current_screen();
        if ($screen && in_array($screen->id, ['post', 'page', 'issue', 'events'])) {
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
        }
    } else {
        // On frontend, always apply ordering
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }
    return $args;
}
add_filter('acf/fields/relationship/query', 'bearsmith_relationship_order_by_date', 10, 3);


// Custom back-end styles
function bearsmith_acf_styles() {
    ?>

        <style type="text/css">
            .acf-relationship .list {
                height: 400px;
            }
        </style>

    <?php
}
add_action('acf/input/admin_head', 'bearsmith_acf_styles');

// ... existing code ...

// Order Post Object field by date
function bearsmith_post_object_order_by_date($args, $field, $post_id) {
    if ($field['key'] === 'field_606e240de3df9' || $field['key'] === 'field_6802cba160511') {
        // Only apply ordering in specific contexts to improve performance
        if (!is_admin() || wp_doing_ajax()) {
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
        }
    }
    return $args;
}
add_filter('acf/fields/post_object/query', 'bearsmith_post_object_order_by_date', 10, 3);
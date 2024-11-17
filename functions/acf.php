<?php

/*
    Advanced Custom Fields
*/


// Add options pages
if(function_exists('acf_add_options_page')) {
    acf_add_options_page();
    acf_add_options_sub_page('Header');
    acf_add_options_sub_page('Footer');
    acf_add_options_sub_page('Code Snippets');
    acf_add_options_sub_page('Conversations');
    acf_add_options_sub_page('Letters');
    acf_add_options_sub_page('Email');

}


// Order Relationship fields
function bearsmith_relationship_order_by_date($args, $field, $post_id) {
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
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
    if ($field['key'] === 'field_606e240de3df9') {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }
    return $args;
}
add_filter('acf/fields/post_object/query', 'bearsmith_post_object_order_by_date', 10, 3);
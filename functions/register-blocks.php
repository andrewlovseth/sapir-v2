<?php

/*
    Register Blocks
*/



add_action('acf/init', 'my_register_blocks');
function my_register_blocks() {

    if( function_exists('acf_register_block_type') ) {

        acf_register_block_type(array(
            'name'              => 'newsletter-signup',
            'title'             => __('Newsletter Signup'),
            'description'       => __('Block that embeds the default newsletter signup form.'),
            'render_template'   => 'blocks/newsletter-signup/newsletter-signup.php',
            'category'          => 'layout',
            'icon'              => 'email',
            'align'             => 'full',
        ));

    }
}

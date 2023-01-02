<?php

/*
    Register Blocks
*/


/*

add_action('acf/init', 'my_register_blocks');
function my_register_blocks() {

    if( function_exists('acf_register_block_type') ) {

        acf_register_block_type(array(
            'name'              => 'block-name',
            'title'             => __('Block Title'),
            'description'       => __('Description of custom block.'),
            'render_template'   => 'blocks/block-dir/block-file.php',
            'category'          => 'layout',
            'icon'              => 'XXXXXX',
            'align'             => 'full',
        ));

    }
}

 */
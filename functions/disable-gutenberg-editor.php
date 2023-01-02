<?php

/*
	Disable Gutenberg Editor
*/


// Templates and Page IDs without editor
function bearsmith_disable_editor( $id = false ) {

	$excluded_templates = array(
		//'templates/home.php',
	);

	$excluded_ids = array(
		// 1,		
	);

	if( empty( $id ) )
		return false;

	$id = intval( $id );
	$template = get_page_template_slug( $id );

	return in_array( $id, $excluded_ids ) || in_array( $template, $excluded_templates );
}


// Disable Gutenberg by template
function bearsmith_disable_gutenberg( $can_edit, $post_type ) {

	if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
		return $can_edit;

	if( bearsmith_disable_editor( $_GET['post'] ) )
		$can_edit = false;

	return $can_edit;

}
add_filter( 'gutenberg_can_edit_post_type', 'bearsmith_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'bearsmith_disable_gutenberg', 10, 2 );


// Disable Classic Editor by template
function bearsmith_disable_classic_editor() {

	$screen = get_current_screen();
	if( 'page' !== $screen->id || ! isset( $_GET['post']) )
		return;

	if( bearsmith_disable_editor( $_GET['post'] ) ) {
		remove_post_type_support( 'page', 'editor' );
	}

}
add_action( 'admin_head', 'bearsmith_disable_classic_editor' );



// Disabled Gutenberg by CPT
function bearsmith_disable_gutenberg_cpt( $current_status, $post_type ) {

    // Disabled post types
    $disabled_post_types = array( 'page', 'issue', 'events' );

    // Change $can_edit to false for any post types in the disabled post types array
    if ( in_array( $post_type, $disabled_post_types, true ) ) {
        $current_status = false;
    }

    return $current_status;
}
add_filter( 'use_block_editor_for_post_type', 'bearsmith_disable_gutenberg_cpt', 10, 2 );
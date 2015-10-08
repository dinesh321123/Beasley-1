<?php

// action hooks
add_action( 'init', 'gmr_advertisers_register_post_type' );
add_action( 'save_post', 'gmr_advertisers_save_post' );

/**
 * Registers advertisers post type.
 *
 * @action init
 */
function gmr_advertisers_register_post_type() {
	register_post_type( GMR_ADVERTISER_CPT, array(
		'label'                => 'Advertisers',
		'public'               => true,
		'has_archive'          => 'advertisers',
		'show_in_nav_menus'    => false,
		'menu_position'        => 41,
		'capability_type'      => array( 'advertiser', 'advertisers' ),
		'map_meta_cap'         => true,
		'hierarchical'         => true,
		'menu_icon'            => 'dashicons-tickets-alt',
		'supports'             => array( 'title', 'editor', 'thumbnail' ),
		'register_meta_box_cb' => 'gmr_advertisers_register_meta_boxes',
		'labels'               => array(
			'name'               => 'Advertisers',
			'singular_name'      => 'Advertiser',
			'menu_name'          => 'Advertisers',
			'name_admin_bar'     => 'Advertiser',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Advertiser',
			'new_item'           => 'New Advertiser',
			'edit_item'          => 'Edit Advertiser',
			'view_item'          => 'View Advertiser',
			'all_items'          => 'Advertisers',
			'search_items'       => 'Search Advertisers',
			'parent_item_colon'  => 'Parent Advertisers:',
			'not_found'          => 'No advertisers found.',
			'not_found_in_trash' => 'No advertisers found in Trash.',
		),
		'rewrite' => array( 'slug' => 'advertisers' ),
	) );
}

/**
 * Registers advertisers meta boxes.
 */
function gmr_advertisers_register_meta_boxes() {
	add_meta_box( 'advertiser-link', 'Advertiser URL', 'gmr_advertisers_render_link_metabox', GMR_ADVERTISER_CPT, 'side', 'core' );
}

/**
 * Renders link meta box.
 *
 * @param WP_Post $post The advertiser post object.
 */
function gmr_advertisers_render_link_metabox( WP_Post $post ) {
	$link = get_post_meta( $post->ID, 'advertiser_link', true );

	wp_nonce_field( 'gmr_advertiser_link', '_gmr_link_nonce', false );
	echo '<input type="url" name="advertiser_link" class="widefat" value="', esc_attr( $link ), '">';
}

/**
 * Saves advertiser meta data.
 *
 * @action save_post
 * @param int $post_id The advertiser post id.
 */
function gmr_advertisers_save_post( $post_id ) {
	// if this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// verify that the nonce is valid.
	if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_gmr_link_nonce' ), 'gmr_advertiser_link' ) ) {
		return;
	}

	// check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// save advertiser link.
	$link = filter_input( INPUT_POST, 'advertiser_link', FILTER_VALIDATE_URL );
	if ( $link ) {
		update_post_meta( $post_id, 'advertiser_link', $link );
	} else {
		delete_post_meta( $post_id, 'advertiser_link' );
	}
}

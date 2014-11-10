<?php
/*
 * Plugin Name: Greater Media Live Link
 * Description: Adds Live Link functionality.
 * Author:      10up
 * Author URI:  http://10up.com/
 */

// constants
define( 'GMR_LIVE_LINK_CPT', 'gmr-live-link' );

// action hooks
add_action( 'init', 'gmr_ll_register_post_type', PHP_INT_MAX );
add_action( 'save_post', 'gmr_ll_save_redirect_meta_box_data' );
add_action( 'manage_' . GMR_LIVE_LINK_CPT . '_posts_custom_column', 'gmr_ll_render_custom_column', 10, 2 );
add_action( 'admin_action_gmr_ll_copy', 'gmr_ll_handle_copy_post_to_live_link' );
add_action( 'gmr_quickpost_submitbox_misc_actions', 'gmr_ll_add_quickpost_checkbox' );
add_action( 'gmr_quickpost_post_created', 'gmr_ll_create_quickpost_live_link' );
add_action( 'wp_ajax_gmr_live_link_suggest', 'gmr_ll_live_link_suggest' );

// filter hooks
add_filter( 'manage_' . GMR_LIVE_LINK_CPT . '_posts_columns', 'gmr_ll_filter_columns_list' );
add_filter( 'post_row_actions', 'gmr_ll_add_post_action', 10, 2 );
add_filter( 'page_row_actions', 'gmr_ll_add_post_action', 10, 2 );
add_filter( 'gmr_show_widget_item_post_types', 'gmr_ll_add_show_widget_post_types' );
add_filter( 'gmr_show_widget_item', 'gmr_ll_output_show_widget_live_link_item' );

/**
 * Sends post title suggestions for live link redirect field.
 *
 * @action wp_ajax_gmr_live_link_suggest
 */
function gmr_ll_live_link_suggest() {
	$query = new WP_Query();
	
	$results = $query->query( array(
		's'           => wp_unslash( $_GET['q'] ),
		'post_type'   => gmr_ll_get_suggestion_post_types(),
		'post_status' => 'publish',
	) );

	echo implode( wp_list_pluck( $results, 'post_title' ), PHP_EOL );
	exit;
}

/**
 * Returns post types for live link suggestion.
 *
 * @return array The array of post types.
 */
function gmr_ll_get_suggestion_post_types() {
	return apply_filters( 'gmr_live_link_suggestion_post_types', array( 'post', 'page' ) );
}

/**
 * Adds checkbox to create live link to quickpost popup form.
 *
 * @action gmr_quickpost_submitbox_misc_actions
 */
function gmr_ll_add_quickpost_checkbox() {
	?><p>
		<label>
			Create Live Link: 
			<input type="checkbox" name="gmr_create_live_link" value="1">
		</label>
	</p><?php
}

/**
 * Registers Live Link post type.
 *
 * @action init
 * @uses 'gmr_live_link_taxonomies' filter to filter supported taxonomies.
 */
function gmr_ll_register_post_type() {
	register_post_type( GMR_LIVE_LINK_CPT, array(
		'public'               => false,
		'show_ui'              => true,
		'rewrite'              => false,
		'query_var'            => false,
		'can_export'           => false,
		'menu_position'        => 5,
		'menu_icon'            => 'dashicons-admin-links',
		'supports'             => array( 'title', 'post-formats', 'thumbnail' ),
		'taxonomies'           => apply_filters( 'gmr_live_link_taxonomies', array() ),
		'register_meta_box_cb' => 'gmr_ll_register_meta_boxes',
		'label'                => 'Live Links',
		'labels'               => array(
			'name'               => 'Live Links',
			'singular_name'      => 'Live Link',
			'menu_name'          => 'Live Links',
			'name_admin_bar'     => 'Live Link',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Link',
			'new_item'           => 'New Link',
			'edit_item'          => 'Edit Link',
			'view_item'          => 'View Link',
			'all_items'          => 'All Links',
			'search_items'       => 'Search Links',
			'parent_item_colon'  => 'Parent Links:',
			'not_found'          => 'No links found.',
			'not_found_in_trash' => 'No links found in Trash.',
		),
	) );
}

/**
 * Registers meta boxes for the Live Link post type.
 *
 * @param WP_Post $post The current post instance.
 */
function gmr_ll_register_meta_boxes( WP_Post $post ) {
	add_meta_box( 'gmr-ll-redirect', 'Redirect To', 'gmr_ll_render_redirect_meta_box', null, 'normal', 'high' );
}

/**
 * Rendres "Redirect To" meta box.
 *
 * @param WP_Post $post The current post instance.
 */
function gmr_ll_render_redirect_meta_box( WP_Post $post ) {
	wp_enqueue_script( 'suggest' );

	$redirect_url = get_post_meta( $post->ID, 'redirect', true );
	if ( is_numeric( $redirect_url ) ) {
		$post = get_post( $redirect_url );
		$redirect_url = $post ? $post->post_title : '';
	}

	wp_nonce_field( 'gmr-ll-redirect', 'gmr_ll_redirect_nonce', false );

	?><script type="text/javascript">
		(function ($) {
			$(document).ready(function() {
				$('input[name="gmr_ll_redirect"]').each(function() {
					$(this).suggest(ajaxurl + '?action=gmr_live_link_suggest', {
						delay: 500,
						minchars: 2,
						multiple: false
					});
				});
			});
		})(jQuery);
	</script>
	
	<input type="text" class="widefat" name="gmr_ll_redirect" value="<?php echo esc_attr( $redirect_url ) ?>">
	<p class="description">Enter external link or start typing a post title to see a list of suggestions.</p><?php
}

/**
 * Saves redirection link.
 *
 * @action save_post
 * @param int $post_id The post id.
 */
function gmr_ll_save_redirect_meta_box_data( $post_id ) {
	// validate nonce
	$doing_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	$valid_nonce = wp_verify_nonce( filter_input( INPUT_POST, 'gmr_ll_redirect_nonce' ), 'gmr-ll-redirect' );
	if ( $doing_autosave || ! $valid_nonce ) {
		return;
	}

	// check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// save redirect link
	$redirect = filter_input( INPUT_POST, 'gmr_ll_redirect' );
	if ( ! is_numeric( $redirect ) && ! filter_var( $redirect, FILTER_VALIDATE_URL ) ) {
		$post = get_page_by_title( $redirect, OBJECT, gmr_ll_get_suggestion_post_types() );
		if ( ! $post ) {
			return;
		}

		$redirect = $post->ID;
	}
	
	update_post_meta( $post_id, 'redirect', $redirect );

	// deactivate this action to prevent infinite loop
	remove_action( 'save_post', 'gmr_ll_save_redirect_meta_box_data' );

	// set live link post parent to its realted post id
	if ( is_numeric( $redirect ) ) {
		$post = get_post( $post_id );
		$post->post_parent = $redirect;

		wp_update_post( $post->to_array() );
	}
}

/**
 * Adds redirect column to the live links table.
 *
 * @filter manage_gmr-live-link_posts_custom_column
 * @param array $columns Initial array of columns.
 * @return array The array of columns.
 */
function gmr_ll_filter_columns_list( $columns ) {
	$cut_mark = array_search( 'title', array_keys( $columns ) ) + 1;

	$columns = array_merge(
		array_slice( $columns, 0, $cut_mark ),
		array( 'redirect' => 'Redirect To' ),
		array_slice( $columns, $cut_mark )
	);

	return $columns;
}

/**
 * Renders custom columns for the live links table.
 *
 * @action manage_gmr-live-link_posts_columns
 * @param string $column_name The column name which is gonna be rendered.
 * @param int $post_id The post id.
 */
function gmr_ll_render_custom_column( $column_name, $post_id ) {
	if ( 'redirect' == $column_name ) {
		$link = gmr_ll_get_redirect_link( $post_id );
		if ( $link ) {
			printf( '<a href="%s" target="_blank">%s</a>', esc_url( $link ), esc_html( $link ) );
		}
	}
}

/**
 * Returns live link redirect.
 *
 * @param int $live_link_id The live link post id.
 * @return string|boolean The redirect URL on success, otherwise FALSE.
 */
function gmr_ll_get_redirect_link( $live_link_id ) {
	$live_link = get_post( $live_link_id );
	if ( ! $live_link || GMR_LIVE_LINK_CPT != $live_link->post_type ) {
		return false;
	}

	if ( $live_link->post_parent > 0 ) {
		return get_permalink( $live_link->post_parent );
	}
	
	$redirect = get_post_meta( $live_link_id, 'redirect', true );
	if ( is_numeric( $redirect ) ) {
		$post = get_post( $redirect );
		if ( $post ) {
			return get_permalink( $post );
		}
	} elseif ( filter_var( $redirect, FILTER_VALIDATE_URL ) ) {
		return $redirect;
	}

	return false;
}

/**
 * Adds "copy live link" action.
 *
 * @filter page_row_actions
 * @filter post_row_actions
 *
 * @uses 'gmr_live_link_add_copy_action' to determine whether or not to add copy action.
 *
 * @param array $actions The initial array of post actions.
 * @param WP_Post $post The post object.
 * @return array The array of post actions.
 */
function gmr_ll_add_post_action( $actions, WP_Post $post ) {
	// check whether or not we need to add copy link
	if ( ! apply_filters( 'gmr_live_link_add_copy_action', GMR_LIVE_LINK_CPT != $post->post_type, $post ) ) {
		return $actions;
	}

	// add copy action 
	$link = admin_url( 'admin.php?action=gmr_ll_copy&post_id=' . $post->ID );
	$link = wp_nonce_url( $link, 'gmr-ll-copy' );

	$actions['gmr-live-link'] = '<a href="' . esc_url( $link ) . '">Copy Live Link</a>';
	
	return $actions;
}

/**
 * Copies selected post to live links list and redirects to live link edit page.
 *
 * @action admin_action_gmr_ll_copy
 * @uses 'gmr_live_link_copy_post' action to perform additional action after copying.
 */
function gmr_ll_handle_copy_post_to_live_link() {
	check_admin_referer( 'gmr-ll-copy' );

	$post_id = filter_input( INPUT_GET, 'post_id', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
	if ( ! $post_id || ! ( $post = get_post( $post_id ) ) || $post->post_type == GMR_LIVE_LINK_CPT ) {
		wp_die( 'The post was not found.' );
	}

	$ll_id = gmr_ll_copy_post_to_live_link( $post->ID );
	wp_redirect( get_edit_post_link( $ll_id, 'redirect' ) );
	exit;
}

/**
 * Creates live link for a post created via quickpost form.
 *
 * @action gmr_quickpost_post_created
 * @param int $post_id The new post id.
 */
function gmr_ll_create_quickpost_live_link( $post_id ) {
	if ( filter_input( INPUT_POST, 'gmr_create_live_link', FILTER_VALIDATE_BOOLEAN ) ) {
		gmr_ll_copy_post_to_live_link( $post_id );
	}
}

/**
 * Copies post to live link.
 *
 * @param int $post_id The post id.
 * @return int The live link id.
 */
function gmr_ll_copy_post_to_live_link( $post_id ) {
	$post = get_post( $post_id );
	if ( $post->post_type == GMR_LIVE_LINK_CPT ) {
		return false;
	}
	
	$args = array(
		'post_status' => 'publish',
		'post_type'   => GMR_LIVE_LINK_CPT,
		'post_title'  => $post->post_title,
		'post_parent' => $post_id,
	);

	$ll_id = wp_insert_post( $args );
	if ( $ll_id ) {
		// set redirect anchor
		add_post_meta( $ll_id, 'redirect', $post_id );

		// copy format
		$format = get_post_format( $post );
		if ( ! empty( $format ) ) {
			set_post_format( $ll_id, $format );
		}

		// copy thumbnail
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( ! empty( $thumbnail_id ) ) {
			set_post_thumbnail( $ll_id, $thumbnail_id );
		}

		do_action( 'gmr_live_link_copy_post', $ll_id, $post_id );
	}

	return $ll_id;
}

/**
 * Registers live link post type for shows widget.
 *
 * @filter gmr_show_widget_item_post_types
 * @param array $post_types The post types array.
 * @return array The post types array.
 */
function gmr_ll_add_show_widget_post_types( $post_types ) {
	$post_types[] = GMR_LIVE_LINK_CPT;
	return $post_types;
}

/**
 * Returns shows widget item output.
 * 
 * @filter gmr_show_widget_item
 * @return string The item html.
 */
function gmr_ll_output_show_widget_live_link_item( $item ) {
	$link = gmr_ll_get_redirect_link( get_the_ID() );
	if ( $link ) {
		$item = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $link ),
			get_the_title()
		);
	}
	
	return $item;
}

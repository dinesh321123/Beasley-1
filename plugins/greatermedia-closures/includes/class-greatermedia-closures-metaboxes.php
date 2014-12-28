<?php
/**
 * Created by Eduard
 * Date: 28.12.2014 3:14
 */

class GreaterMediaClosuresMetaboxes {

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ) );
	}

	public static function add_meta_box( $post_type ) {
		if( GreaterMediaClosuresCPT::CLOSURE_CPT_SLUG == $post_type ) {
			add_meta_box(
				'gmedia_closure_details'
				,__( 'Closure Details', 'greatermedia' )
				,array( __CLASS__, 'render_closures_general_location' )
				,$post_type
				,'advanced'
				,'high'
			);
		}
	}

	public static function save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['gmedia_closures_metabox_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['gmedia_closures_metabox_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'gmedia_closures_metabox' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		if( isset( $_POST['gmedia_closure_general_location'] ) ) {
			$location = sanitize_text_field( $_POST['gmedia_closure_general_location'] );
			// Update the meta field.
			update_post_meta( $post_id, 'gmedia_closure_general_location', $location );
		}

		if( isset( $_POST['gmedia_closure_entity_type'] ) ) {
			$gmedia_closure_entity_type = sanitize_text_field( $_POST['gmedia_closure_entity_type'] );
			// Update the meta field.
			update_post_meta( $post_id, 'gmedia_closure_entity_type', $gmedia_closure_entity_type );
		}

		if( isset( $_POST['gmedia_closure_type'] ) ) {
			$gmedia_closure_type = sanitize_text_field( $_POST['gmedia_closure_type'] );
			// Update the meta field.
			update_post_meta( $post_id, 'gmedia_closure_type', $gmedia_closure_type );
		}

	}

	public static function render_closures_general_location( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'gmedia_closures_metabox', 'gmedia_closures_metabox_nonce' );


		// get old data
		$closure_type = get_post_meta( $post->ID, 'gmedia_closure_type', true );
		$closure_entity_type = get_post_meta( $post->ID, 'gmedia_closure_entity_type', true );
		$closure_general_location = sanitize_text_field( get_post_meta( $post->ID, 'gmedia_closure_general_location', true ) );

		// general location metabox
		echo '<table>';
		echo '<tr>';
		echo '<td><label for="gmedia_closure_general_location">General Location:<label></td><td><input id="gmedia_closure_general_location" name="gmedia_closure_general_location" type="text" value="' . esc_html( $closure_general_location ) . '" /></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label for="gmedia_closure_type">Type:<label></td>';
		echo '<td>';
		$args = array(
			'hide_empty' => 0,
			'taxonomy'  => GreaterMediaClosuresCPT::CLOSURE_TYPE_SLUG,
			'name'  => 'gmedia_closure_type',
			'id'  => 'gmedia_closure_type',
			'selected' => $closure_type
		);
		wp_dropdown_categories( $args );
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label for="gmedia_closure_entity_type">Closure Type:<label></td>';
		echo '<td>';
		$args = array(
			'hide_empty' => 0,
			'taxonomy'  => GreaterMediaClosuresCPT::CLOSURE_ENTITY_TYPE_SLUG,
			'name'  => 'gmedia_closure_entity_type',
			'id'  => 'gmedia_closure_entity_type',
			'selected' => $closure_entity_type
		);
		wp_dropdown_categories( $args );
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
}

GreaterMediaClosuresMetaboxes::init();
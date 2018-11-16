<?php

namespace Bbgi\Image;

class Attributes extends \Bbgi\Module {

	/**
	 * Registers the module.
	 *
	 * @access public
	 */
	public function register() {
		add_filter( 'attachment_fields_to_edit', $this( 'attachment_field_credit' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', $this( 'attachment_field_credit_save' ), 10, 2 );
	}

	/**
	 * Add Image Attribution fields to media uploader
	 *
	 * @access public
	 * @param array $form_fields Fields to include in attachment form
	 * @param \WP_Post $post Attachment record in database
	 * @return array Modified form fields
	 */
	public function attachment_field_credit( $form_fields, $post ) {
		$form_fields['gmr_image_attribution'] = array(
			'value' => get_post_meta( $post->ID, 'gmr_image_attribution', true ),
			'label' => 'Attribution',
			'input' => 'text',
			'helps' => 'Add attribution that will displayed with the image on the frontend',
		);

		return $form_fields;
	}

	/**
	 * Save values of Image Attribution in the media uploader
	 *
	 * @access public
	 * @param array $post The post data for database
	 * @param array $attachment Attachment fields from $_POST form
	 * @return array Modified post data
	 */
	public function attachment_field_credit_save( $post, $attachment ) {
		delete_post_meta( $post['ID'], 'gmr_image_attribution' );
		if ( ! empty( $attachment['gmr_image_attribution'] ) ) {
			$value = sanitize_text_field( $attachment['gmr_image_attribution'] );
			update_post_meta( $post['ID'], 'gmr_image_attribution', $value );
		}

		return $post;
	}

}

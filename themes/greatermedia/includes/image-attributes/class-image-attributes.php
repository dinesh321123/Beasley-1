<?php

/**
 * Class GMRImageAttr
 *
 * add a custom field to images for image attribution in the media modal and media library
 */
class GMRImageAttr {

	public static function init() {
		add_filter( 'attachment_fields_to_edit', array( __CLASS__, 'attachment_field_credit' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( __CLASS__, 'attachment_field_credit_save' ), 10, 2 );
	}

	/**
	 * Add Image Attribution fields to media uploader
	 *
	 * @param array $form_fields Fields to include in attachment form
	 * @param WP_Post $post Attachment record in database
	 *
	 * @return array Modified form fields
	 */
	public static function attachment_field_credit( $form_fields, $post ) {
		$gmr_img_attr = get_image_attribution($post->ID);

		$form_fields['gmr_image_attribution'] = array(
			'value' => $gmr_img_attr ? $gmr_img_attr : '',
			'label' => __( 'Image Attribution' ),
			'input' => 'text',
			'helps' =>  __( 'If provided, image attribution will display' ),
		);

		return $form_fields;
	}


	/**
	 * Save values of Image Attribution in the media uploader
	 *
	 * @param array $post The post data for database
	 * @param array $attachment Attachment fields from $_POST form
	 *
	 * @return array Modified post data
	 */
	public static function attachment_field_credit_save( $post, $attachment ) {

		if( isset( $attachment['gmr_image_attribution'] ) ) {

			$image_attribute = wp_filter_post_kses( $attachment['gmr_image_attribution'] );

			update_post_meta( $post['ID'], 'gmr_image_attribution', $image_attribute );

		}
		
		return $post;
	}
}

GMRImageAttr::init();

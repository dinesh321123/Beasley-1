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
		add_filter( 'image_send_to_editor', array( __CLASS__, 'insert_image_with_id' ), 10, 2 );
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
		$gmr_img_attr = get_image_attribution( $post->ID );

		$form_fields['gmr_image_attribution'] = array(
			'value' => $gmr_img_attr ? $gmr_img_attr : '',
			'label' => 'Image Attribution',
			'input' => 'text',
			'helps' => 'If provided, image attribution will display',
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
		if ( isset( $attachment['gmr_image_attribution'] ) ) {
			$image_attribute = wp_filter_post_kses( $attachment['gmr_image_attribution'] );
			update_post_meta( $post['ID'], 'gmr_image_attribution', $image_attribute );
		}
		
		return $post;
	}

	/**
     * Add image attribution to the inserted image.
     */
    public static function insert_image_with_id( $html, $image_id ) {
		$attribution = image_attribution( $image_id, false );
		if ( $attribution ) {
			$attribution = str_replace( array( '<div', '</div>' ), array( '<span', '</span>' ), $attribution );
			
			if ( stripos( $html, '</a>' ) > 0 ) {
				$html = str_replace( '</a>', $attribution . '</a>', $html );
				$html = str_replace( '<a ', '<a style="position:relative;display:inline-block;" ', $html );
			} else {
				$html = '<span style="position:relative;display:inline-block">' . $html . $attribution . '</span>';
			}
		}
		
        return $html;
    }
	
}

GMRImageAttr::init();

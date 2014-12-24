<?php

class GreaterMediaUserGeneratedGallery extends GreaterMediaUserGeneratedContent {

	const POST_FORMAT = 'gallery';

	function __construct( $post_id = null ) {

		parent::__construct( $post_id );

	}

	/**
	 * Get gallery attachments for this post
	 *
	 * @return array
	 */
	protected function get_attachments() {

		$attachment_data = get_post_gallery( $this->post_id, false );
		$attachment_ids  = explode( ',', $attachment_data['ids'] );

		$attachments = array();
		foreach ( $attachment_ids as $attachment_index => $attachment_id ) {
			if ( ! empty( $attachment_id ) && ! empty( $attachment_data['src'][$attachment_index] ) ) {
				$attachments[$attachment_id] = $attachment_data['src'][$attachment_index];
			}
		}

		return $attachments;

	}

	/**
	 * Render a representation of this post appropriate for displaying in the moderation queue
	 *
	 * @return string html
	 */
	public function render_moderation_row() {

		$attachments = $this->get_attachments();

		$html = '<div class="ugc-moderation-data">';

		foreach ( $attachments as $attachment_id => $attachment_src ) {

			$delete_url = home_url( sprintf( 'ugc/%d/gallery/%d/delete', $this->post_id, $attachment_id ) );

			$html .= '<div class="ugc-moderation-gallery-thumb">' .
				'<a href="' . wp_nonce_url( $delete_url, 'trash-ugc-gallery_' . $attachment_id ) . '" class="trash"><div class="dashicons dashicons-trash"></div></a>' .
				'<img src="' .
				esc_attr( $attachment_src ) .
				'" />' .
				'</div>';
		}

		$html .= '</div>';

		return $html;

	}

	/**
	 * Render a preview of this UGC suitable for use in the admin
	 *
	 * @return string html
	 */
	public function render_preview() {

		$attachments = $this->get_attachments();

		$html = '<div class="ugc-gallery-preview">';

		foreach ( $attachments as $attachment_id => $attachment_src ) {

			$delete_url = home_url( sprintf( 'ugc/%d/gallery/%d/delete', $this->post_id, $attachment_id ) );

			$html .= '<div class="ugc-gallery-preview-thumb">' .
				'<img src="' .
				esc_attr( $attachment_src ) .
				'" />' .
				'</div>';
		}

		$html .= '</div>';

		return $html;

	}


}

GreaterMediaUserGeneratedContent::register_subclass( 'gallery', 'GreaterMediaUserGeneratedGallery' );

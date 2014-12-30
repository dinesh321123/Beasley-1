<?php

class GreaterMediaUserGeneratedImage extends GreaterMediaUserGeneratedContent {

	function __construct( $post_id = null ) {

		parent::__construct( $post_id );

	}

	/**
	 * Returns post format.
	 * 
	 * @access protected
	 * @return string The post format.
	 */
	protected function get_post_format() {
		return 'image';
	}

	public static function first_image( $post_content ) {

		$first_img = get_post_thumbnail_id();
		if ( $first_img ) {
			$first_img = wp_get_attachment_image_src( $first_img );
			if ( ! empty( $first_img ) ) {
				return current( $first_img );
			}
		}

		$first_img = '';
		if ( preg_match( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches ) ) {
			$first_img = $matches[1];
		}

		return $first_img;
		
	}

	/**
	 * Render a representation of this post appropriate for displaying in the moderation queue
	 *
	 * @return string html
	 */
	public function render_moderation_row() {

		$html = '';

		$first_image = $this->first_image( $this->post->post_content );

		$html .= '<div class="ugc-moderation-data">' .
			'<div class="ugc-moderation-gallery-thumb">' .
			'<img src="' .
			esc_attr( $first_image ) .
			'" />' .
			'</div>' .
			'</div>';

		return $html;

	}

	/**
	 * Approves this ugc entry.
	 */
	public function approve() {
		parent::approve();

		$attachment_id = get_post_thumbnail_id( $this->post_id );
		if ( $attachment_id ) {
			$attachment = get_post( $attachment_id );
			if ( $attachment ) {
				$attachment->post_status = 'inherit';
				wp_update_post( $attachment->to_array() );
			}
		}
	}

	/**
	 * Render a preview of this UGC suitable for use in the admin
	 *
	 * @return string html
	 */
	public function render_preview() {

		$html = '';

		$first_image = $this->first_image( $this->post->post_content );

		$html .= '<div class="ugc-gallery-preview">' .
			'<div class="ugc-moderation-gallery-thumb">' .
			'<img src="' .
			esc_attr( $first_image ) .
			'" />' .
			'</div>' .
			'</div>';

		return $html;

	}

}

GreaterMediaUserGeneratedContent::register_subclass( 'image', 'GreaterMediaUserGeneratedImage' );
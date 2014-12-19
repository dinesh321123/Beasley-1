<?php

class GreaterMediaUserGeneratedImage extends GreaterMediaUserGeneratedContent {

	const POST_FORMAT = 'image';

	function __construct( $post_id = null ) {

		parent::__construct( $post_id );

	}

	public static function first_image( $post_content ) {

		$matches = array();
		$output  = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches );
		if ( isset( $matches[1][0] ) ) {
			$first_img = $matches[1][0];
		}

		if ( empty( $first_img ) ) {
			return '';
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
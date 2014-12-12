<?php

class GreaterMediaUserGeneratedLink extends GreaterMediaUserGeneratedContent {

	const POST_FORMAT = 'link';

	function __construct( $post_id = null ) {

		parent::__construct( $post_id );

	}

	public static function retrieve_link_preview( $post_content ) {

		$embed_content = wp_oembed_get( $post_content );
		if ( false === $embed_content ) {
			$first_link = self::first_link( $post_content );
			if ( ! empty( $first_link ) ) {
				$embed_content = '<a href="' . esc_attr( $first_link['url'] ) . '" target="_blank">' .
					wp_strip_all_tags( $first_link['title'] ) .
					'</a>';
			} else {
				// Default to trying to sanitize what's in the body
				$embed_content = wp_strip_all_tags( $post_content );
			}
		}

		$embed_content = '<span class="ugc-moderation-embed">' .
			$embed_content .
			'</span>';

		return $embed_content;

	}

	public static function first_link( $post_content ) {

		$matches = array();
		$output  = preg_match_all( '/<a.+href=[\'"]([^\'"]+)[\'"].*>(.*)<\/a>/i', $post_content, $matches );

		if ( isset( $matches[1][0] ) ) {
			$first_link_url = $matches[1][0];
		}

		if ( empty( $first_link_url ) ) {
			return '';
		}

		if ( isset( $matches[2][0] ) ) {
			$first_link_title = $matches[2][0];
		}

		$retval = array(
			'url'   => $first_link_url,
			'title' => $first_link_title,
		);

		return $retval;

	}

	/**
	 * Render a representation of this post appropriate for displaying in the moderation queue
	 *
	 * @return string html
	 */
	public function render_moderation_row() {

		$html = $this->retrieve_link_preview( $this->post->post_content );

		return $html;

	}

	/**
	 * Render a preview of this UGC suitable for use in the admin
	 *
	 * @return string html
	 */
	public function render_preview() {

		$html = $this->retrieve_link_preview( $this->post->post_content );

		return $html;

	}

}

GreaterMediaUserGeneratedContent::register_subclass( 'link', 'GreaterMediaUserGeneratedLink' );
<?php
/**
 * Yoast SEO customizations
 */
namespace Bbgi;

class Seo extends \Bbgi\Module {

	/**
	 * Registers module.
	 *
	 * @access public
	 */
	public function register() {
		// add_filter( 'wpseo_opengraph_image_size', $this( 'update_og_image_size' ) ); // deactivated for now
		add_filter( 'wpseo_twitter_image', $this( 'update_twitter_image' ) );
	}

	/**
	 * Updates image size for OperGraph data.
	 *
	 * @access public
	 * @return string The proper size for OG image.
	 */
	public function update_og_image_size() {
		return 'large';
	}

	/**
	 * Updates image for the Twitter card to use proper image size.
	 *
	 * @access public
	 * @param string $img The original image URL.
	 * @return string Updated image URL.
	 */
	public function update_twitter_image( $img ) {
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post && has_post_thumbnail( $post ) ) {
				$url = bbgi_get_post_thumbnail_url( $post, false, 600, 314, 'crop', array( 'width' => false, 'height' => true ) );
				if ( parse_url( $url, PHP_URL_PATH ) == parse_url( $img, PHP_URL_PATH ) ) {
					return $url;
				} else {
					return add_query_arg( array(
						'width'     => 600,
						'maxheight' => 314,
						'mode'      => 'crop',
					), $img );
				}
			}
		}

		return $img;
	}

}

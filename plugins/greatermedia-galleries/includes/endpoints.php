<?php
/**
 * Provides customized rewrites for the galleries/albums category page, at /photos/<category>
 *
 * Static class for consistency w/ the rest of galleries.
 *
 * Class GreaterMediaGalleryEndpoints
 */

class GreaterMediaGalleryEndpoints {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_rewrite_tag' ) );
		add_action( 'init', array( __CLASS__, 'add_rewrites' ) );
		add_filter( 'template_include', array( __CLASS__, 'filter_template' ) );
	}

	public static function add_rewrite_tag() {
		add_rewrite_tag( '%photos_category%', '([^&]+)' );
	}

	public static function add_rewrites() {
		// Supports the /photos/<category> endpoint
		$rule = sprintf( '%1$s/([^/]+)/?$', 'photos' );
		\add_rewrite_rule( $rule, 'index.php?photos_category=$matches[1]', 'top' );
	}

	/**
	 * Filters the template we are using to also include archive-photos-{category} if it is available.
	 *
	 * Templates will be loaded in the following priority:
	 *  - archive-photos-<category>.php
	 *  - archive-photos.php
	 *  - archive.php
	 *
	 * @param string $template Path to the current template that has been selected.
	 *
	 * @return string The final template that we are going to use.
	 */
	public static function filter_template( $template ) {
		$cat = get_query_var( 'photos_category' );
		if ( $cat && term_exists( $cat, 'category' ) ) {
			$templates = array();

			$templates[] = "archive-photos-{$cat}.php";
			$templates[] = "archive-photos.php";
			$templates[] = "archive.php";

			return get_query_template( 'archive', $templates );
		}

		return $template;
	}
}

GreaterMediaGalleryEndpoints::init();

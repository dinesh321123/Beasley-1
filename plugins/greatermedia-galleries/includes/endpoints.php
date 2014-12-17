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
		add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
	}

	public static function add_rewrite_tag() {
		add_rewrite_tag( '%photos_category%', '([^&]+)' );
		add_rewrite_tag( '%photos_category_page%', '([0-9]+)' );
	}

	public static function add_rewrites() {
		// Supports the /photos/<category> endpoint
		$rule = sprintf( '%1$s/([^/]+)(/page/([0-9]+))?/?$', 'photos' );
		\add_rewrite_rule( $rule, 'index.php?photos_category=$matches[1]&photos_category_page=$matches[3]', 'top' );
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

	public static function pre_get_posts( \WP_Query $query ) {
		if ( $query->is_main_query() && $query->get( 'photos_category' ) ) {
			$query->set( 'post_type', array( GreaterMediaGalleryCPT::GALLERY_POST_TYPE, GreaterMediaGalleryCPT::ALBUM_POST_TYPE ) );
			$query->set( 'tax_query', array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => $query->get( 'photos_category' ),
				),
			) );

			$current_page = $query->get( 'photos_category_page' ) ?: 1;
			$query->set( 'paged', $current_page );

			$query->is_home = false; // For whatever reason, this was true
		}
	}
}

GreaterMediaGalleryEndpoints::init();

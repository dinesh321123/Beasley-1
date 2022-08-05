<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class GeneralSettingsFrontRendering {

	function __construct()
	{
		$this->init();
	}

	public static function init() {
		// Register scripts
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
		add_action('pre_get_posts', array( __CLASS__, 'author_pre_get_posts') );
		add_filter('query_vars', array( __CLASS__, 'add_query_vars'));

		add_action( 'template_redirect', array( __CLASS__,'show_404_for_disabled_feeds' ) );
		add_action( 'template_redirect', array( __CLASS__,'feed_headers' ) );
		add_filter( 'body_class', array( __CLASS__, 'category_archive_class' )  );
	}
	function category_archive_class( $classes ) {
		// Set the custom class for category archive styling
		if(is_archive() && is_category()) {
			$classes[] = 'category-archive-page';
		}
		return $classes;
	}
	function feed_headers(){
		echo 'in';exit;
		if ( !is_feed()) {
			return;
		}
		global $post;
		global $wp_query;
		$obj = get_queried_object();
		if ( empty($obj) ||  $wp_query->is_feed( 'current_homepage' )) {
			$headerCacheTag[] = $_SERVER['HTTP_HOST'].'-'.'home';
		} else if (is_archive()) {
			$urlCatArray = explode(',',$wp_query->query['category_name']);;

			$categories = get_categories();
			$categoriesSlug = wp_list_pluck($categories, 'slug' );

			array_walk($categoriesSlug, function ($value, $key) use ($urlCatArray, &$headerCacheTag){
				if(in_array($value,$urlCatArray)) {
					$headerCacheTag[] =   "feed" . "-" . $value;
				}
			});
			$obj = get_queried_object();

			if (isset($obj->slug)) {
				$headerCacheTag[] = "archive" . "-" . $obj->slug;
			}
			if (isset($wp_query->query['post_type'])) {
				$headerCacheTag[] = "feed-" . $wp_query->query['post_type'];
			}
		}  else {
			$currentPostType	= "";
			$currentPostSlug	= "";
			if ( get_post_type() ) :
				$currentPostType = get_post_type();
				$headerCacheTag[] = 'feed-'.$currentPostType;
				if ($currentPostType == "episode") {
					$headerCacheTag[] = "feed-podcast";
				}
			endif;
			if (  isset( $post->post_name ) && $post->post_name != "" ) :
				$currentPostSlug = "-".$post->post_name;
			endif;
			$headerCacheTag[] = 'feed-'.$currentPostType.$currentPostSlug;
		}


		header("Cache-Tag: " . implode(",", $headerCacheTag) , true);
		header("X-Cache-BBGI-Tag: " . implode(",", $headerCacheTag) , true);


	}
	function show_404_for_disabled_feeds() {
		if ( is_feed() && is_singular() && in_array( get_post_type(), GeneralSettingsFrontRendering::restrict_feeds_posttype_list() ) ) {
			global $wp_query;

			$wp_query->set_404();	// Mark the current query as a 404
			status_header(404);	// Return 404 HTTP status code instead of the default 200
			header('Content-Type: text/html; charset=utf-8');	// By default, this page returns XML, so we change the Content-Type header // Because we want to show a 404 page
			get_template_part( 404 );	// Render the 404 template
			exit();	// You should exit from the script after that
		}
	}

	function restrict_feeds_posttype_list() {
		return (array) apply_filters( 'restrict-feeds-for-posttypes', array( 'post', 'affiliate_marketing', 'gmr_gallery', 'contest', 'tribe_events', 'listicle_cpt' ) );
	}
	function author_pre_get_posts($query) {
		if ( !is_admin() && $query->is_main_query() ) {
			if ($query->is_author()) {
				$query->set( 'posts_per_page', 16 );
				$query->set('post_type', array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'));
			}
		}
	}
	function add_query_vars( $author_query_vars ) {
		$author_query_vars[] = 'author_id';
		return $author_query_vars;
	}

	/**
	 * Registers Affiliate Marketing scripts to use on the front end.
	 *
	 * @static
	 * @access public
	 * @action wp_enqueue_scripts
	 */
	public static function register_scripts() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'additional-front-script', GENERAL_SETTINGS_CPT_URL . "/assets/js/front_script{$min}.js", array( 'jquery' ), GENERAL_SETTINGS_CPT_VERSION, true );
	}

	/**
	 * Gets an array of meta data for the Affiliate Marketing
	 * @param $post
	 * @return Array
	 */
	function get_post_metadata_from_post( $value, $post ) {
		$field = get_post_meta( $post->ID, $value, true );

		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}


}

// GeneralSettingsFrontRendering::init();
new GeneralSettingsFrontRendering();

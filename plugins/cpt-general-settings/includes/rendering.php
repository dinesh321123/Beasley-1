<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class GeneralSettingsFrontRendering {

	public static function init() {
		// Register scripts
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
		add_action('pre_get_posts', array( __CLASS__, 'author_pre_get_posts') );
		
		add_action( 'template_redirect', array( __CLASS__,'show_404_for_disabled_feeds' ) );
		add_filter( 'body_class', array( __CLASS__, 'category_archive_class' )  );
		add_action('wp_head',  array( __CLASS__,'pushly_notification_script' ) );
		add_filter( 'after_set_parsely_page', array( __CLASS__,'filter_parsely_metadata' ), 10, 3 );

		// Register the AJAX action for logged-in and non-logged-in users
		add_action('wp_ajax_get_image_attribution', array( __CLASS__, 'get_image_attribution_callback' ) );
		add_action('wp_ajax_nopriv_get_image_attribution', array( __CLASS__, 'get_image_attribution_callback' ) );
	}

	/**
	 * Callback function for handling the AJAX request
	 */
	public static function get_image_attribution_callback() {
		// Prepare the response
		$response = array( 'attribution' => '' );
		$image_attribution = isset($_POST['image_attribution']) ? $_POST['image_attribution'] : "";

		if( $image_attribution ) {
			$response = array( 'attribution' => $image_attribution );
		}

		// Return the response as JSON
		wp_send_json($response);
	}

	public static function category_archive_class( $classes ) {
		// Set the custom class for category archive styling
		if(is_archive() && is_category()) {
			$classes[] = 'category-archive-page';
		}
		return $classes;
	}

	public static function filter_parsely_metadata( $parsely_metadata, $post, $parsely_options ) {
		// override primary author for parsely
		$primary_author = get_field( 'primary_author_cpt', $post );
		$primary_author = $primary_author ? $primary_author : $post->post_author;
		$show_author = get_the_author_meta( 'display_name', $primary_author );
		if( !empty( $show_author ) ) {
			$parsely_metadata['creator'] = [
				$show_author
			];
			$parsely_metadata['author'] = array(
				(object) [
					"@type" => "Person",
					"name" => $show_author
				]
			);
		}

		// Make Shopping as primary section for Must haves
		$has_post_category = has_category( "shopping", $post->ID );
		if( $has_post_category && $post->post_type == 'affiliate_marketing' ) {
			$parsely_metadata['articleSection'] = "Shopping";
		}

		return $parsely_metadata;
	}

	public static function  show_404_for_disabled_feeds() {
		if ( is_feed() && is_singular() && in_array( get_post_type(), GeneralSettingsFrontRendering::restrict_feeds_posttype_list() ) ) {
			global $wp_query;

			$wp_query->set_404();	// Mark the current query as a 404
			status_header(404);	// Return 404 HTTP status code instead of the default 200
			header('Content-Type: text/html; charset=utf-8');	// By default, this page returns XML, so we change the Content-Type header // Because we want to show a 404 page
			get_template_part( 404 );	// Render the 404 template
			exit();	// You should exit from the script after that
		}
	}

	public static function  restrict_feeds_posttype_list() {
		return (array) apply_filters( 'restrict-feeds-for-posttypes', array( 'post', 'affiliate_marketing', 'gmr_gallery', 'contest', 'tribe_events', 'listicle_cpt' ) );
	}
	public static function author_pre_get_posts($query) {
		if ( !is_admin() && $query->is_main_query() ) {
			if ($query->is_author()) {
				$query->set( 'posts_per_page', 16 );
				$query->set('post_type', array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'));
			}
		}
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
		$domainKey    = get_option( 'pushly_domain_key');
		if ( !is_admin() && !empty($domainKey)) {
			wp_enqueue_script( 'pushly-notification-script', "https://cdn.p-n.io/pushly-sdk.min.js?domain_key=".$domainKey, [], GENERAL_SETTINGS_CPT_VERSION );
		}
		wp_enqueue_script( 'additional-front-script', GENERAL_SETTINGS_CPT_URL . "assets/js/front_script{$min}.js", array( 'jquery' ), GENERAL_SETTINGS_CPT_VERSION, true );

		// Localize the AJAX URL
		wp_localize_script( 'additional-front-script', 'my_ajax_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		));


	}

	/**
	 * Gets an array of meta data for the Affiliate Marketing
	 * @param $post
	 * @return Array
	 */
	public static function get_post_metadata_from_post( $value, $post ) {
		$field = get_post_meta( $post->ID, $value, true );

		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}
	/**
	 * Gets an array of meta data for the Affiliate Marketing
	 * @param $post
	 * @return Array
	 */
	public static function pushly_notification_script() {
		$domainKey    = get_option( 'pushly_domain_key');
		if ( !is_admin() && !empty($domainKey)) {
			echo "<script>
			window.PushlySDK = window.PushlySDK || [];
			function pushly() { window.PushlySDK.push(arguments) }
			pushly('load', {
			domainKey: '".$domainKey."',
			sw: '".GENERAL_SETTINGS_CPT_URL."assets/js/pushly-sdk-worker.js',
		  });
		</script>";
		}
	}


}

GeneralSettingsFrontRendering::init();

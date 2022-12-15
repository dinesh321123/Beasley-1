<?php
/**
 * Last updated posts endpoint
 * http://985thesportshub.beasley.test/wp-json/last_updated_posts/v1/last_posts?last_days=30
 * @package Bbgi
 */
namespace Bbgi\Endpoints;

use Bbgi\Module;
use Bbgi\Util;

class LastUpdatedPosts extends Module {
	use Util;
	private static $_fields = array(
		'megamenu_recent_posts_expiration'  => 'Megamenu Recent Posts Expiration',
	);
	public function register() {
		// Register the custom rest endpoint
		add_action( 'rest_api_init', [ $this, 'register_routes_lup' ] );
	}

	/**
	 * Register our custom routes.
	 *
	 * @return void
	 */
	public function register_routes_lup() {
		register_rest_route(
			'last_updated_posts/v1',
			'last_posts',
			[
				'methods' 	=> \WP_REST_Server::READABLE,
				'callback'	=> [ $this, 'last_updated_posts_callback' ],
				'args' 		=> [
					'last_days'    => [
						'type'     => 'string',
						'required' => false,
					],
				]
			]
		);
	}
	public function last_updated_posts_callback( $request )
	{
		// require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php' );
		// require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php' );
		global $wpdb;

		$post_type_list				= implode("','",$this->lup_posttype_list());
		$blog_id					= "wp_".get_current_blog_id()."_posts";
		$last_date					= date('Y-m-d', strtotime('today - 30 days'));
		// $sql			= "SELECT * FROM wp_15_posts WHERE post_status = 'publish' AND post_type IN ('post', 'page') AND post_modified_gmt > '$last_date' ORDER BY post_modified_gmt DESC";
		$sql						= "SELECT * FROM $blog_id WHERE post_status = 'publish' AND post_type IN ('$post_type_list') AND post_modified_gmt > '$last_date' ORDER BY post_modified_gmt DESC";
		$results					= $wpdb->get_results($sql);
		$last_updated_posts_result	= array();
		if( !empty($results) && count($results) != 0 ) {
			$last_updated_posts_result['status'] = 200;
			$all_posts = array();
			foreach ($results as $value) {
				$all_posts[$value->ID] = array(
					'title' => $value->post_title,
					'permalink' => get_permalink($value->ID),
					'updated_date' => $value->post_modified_gmt,
				);
			}
			$last_updated_posts_result['last_updated_posts'] = json_encode($all_posts);
			// $response = array( 'status' => 200 , 'error' => $last_updated_posts_result );
		} else {
			$last_updated_posts_result = array( 'status' => error , 'error' => 'There are no last updated posts at this time.' );
		}
		return $last_updated_posts_result;
	}
	public function lup_posttype_list() {
		return $result	= (array) apply_filters( 'last-updated-post-types-filter', array( 'post', 'listicle_cpt', 'affiliate_marketing', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'announcement', 'contest', 'podcast', 'episode', 'content-kit' )  );
	}
}

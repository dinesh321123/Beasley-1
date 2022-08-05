<?php
/**
 * Mega menu recent posts endpoint
 * http://985thesportshub.beasley.test/wp-json/megamenu_recent_posts/v1/get_posts?per_page=1&cat=8&show_thumb=1&thumb_size=1
 * @package Bbgi
 */
namespace Bbgi\Endpoints;

use Bbgi\Module;
use Bbgi\Util;

class MegamenuRecentPosts extends Module {
	use Util;
	private static $_fields = array(
		'megamenu_recent_posts_expiration'  => 'Megamenu Recent Posts Expiration',
	);
	public function register() {
		// Register the custom rest endpoint
		add_action( 'rest_api_init', [ $this, 'register_routes_mrp' ] );
		// Network level settings for Megamenu Recent Posts Expiration
		add_action( 'wpmu_options', [ $this, 'megamenu_rp_network_settings' ] );
		add_action( 'update_wpmu_options', [ $this, 'save_network_settings' ] );
	}
	/**
	 * Saves network settings.
	 */
	public function save_network_settings() {
		foreach ( self::$_fields as $id => $label ) {
			$value = filter_input( INPUT_POST, $id );
			$value = sanitize_text_field( $value );
			update_site_option( $id, $value );
		}
	}

	/**
	 * Shows upload file size network settings
	 */
	public function megamenu_rp_network_settings() {
		?><h2>Megamenu recent posts expiration</h2>
		<table id="menu" class="form-table">
		<?php foreach ( self::$_fields as $id => $label ) : ?>
		<?php
			$getMegaMenuRecentPostsExpiration = get_site_option( $id );
			$value =  isset( $getMegaMenuRecentPostsExpiration ) && $getMegaMenuRecentPostsExpiration != "" ? $getMegaMenuRecentPostsExpiration : 15 ;
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $label ); ?></th>
				<td>
					<label>
						<input type="number" class="regular-text" name="<?php echo esc_attr( $id ); ?>" min="1" style="width: 100px" value="<?php echo esc_attr( $value ); ?>"> Seconds
						<p class="description" id="first-comment-url-desc" style="display: none;">Set the cache to expire the data after 15 seconds</p>
					</label>
				</td>
			</tr>
		<?php endforeach; ?>
		</table><?php
	}

	/**
	 * Register our custom routes.
	 *
	 * @return void
	 */
	public function register_routes_mrp() {
		register_rest_route(
			'megamenu_recent_posts/v1',
			'get_posts',
			[
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_recent_posts_callback' ],
				'args' => [
					'per_page'       => [
						'type'     => 'string',
						'required' => true,
					],
					/* 'page'       => [
						'type'     => 'string',
						'required' => true,
					], */
					'cat'       => [
						'type'     => 'string',
						'required' => true,
					],
					'show_thumb'       => [
						'type'     => 'string',
						'required' => true,
					],
					'thumb_size'       => [
							'type'     => 'string',
							'required' => true,
					],
				]
			]
		);
	}
	public function get_recent_posts_callback( $request )
	{
		$args				= array( 'post_status' => 'publish' );
		$args['post_type']	= array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing' );
		$args['category']	= isset($request['cat']) && !empty($request['cat']) ? sanitize_text_field($request['cat']) : array() ;
		$args['posts_per_page']	= isset($request['per_page']) && !empty($request['per_page']) ? (int)$request['per_page'] : '4' ;
		if(isset($request['page']) && !empty($request['page'])){
			$args['paged']	= (int)$request['page'];
		}
		$found				= false;
		$key				= md5('megamenu_recent_posts'.implode('_',$args) );
		$recent_post_result	= wp_cache_get( $key, 'bbgi', false, $found );
		// echo " - Found variable - ", $found, " - ";
		if ( ! $found ) {
			$recent_post_result = $this->megamenu_recent_post( $args, $request, $key);
			// Set the cache to expire the data after 15 seconds
			$getMegaMenuRecentPostsExpiration = get_site_option( 'megamenu_recent_posts_expiration', 120 );
			wp_cache_set( $key, $recent_post_result, 'bbgi', $getMegaMenuRecentPostsExpiration );
		}

		$response = rest_ensure_response( $recent_post_result );
		$response->set_headers([
				'Cache-Tag' => 'content,navigation',
				'Cloudflare-CDN-Cache-Control' => 'max-age=300',
				'Cache-Control' => 'public, max-age=60'
		]);

		return $response;
	}
	public function megamenu_recent_post( $args, $request, $key ) {
		$recent_posts_array = wp_get_recent_posts( $args );
		$recent_post_result = array();

		if ( count($recent_posts_array) > 0 ) {
			$recent_post_result['status'] = 200;
			// echo "Created new cache setup for ". $key ." Key";
			foreach ($recent_posts_array as $key => $recent_posts) {
				// print_r($recent_posts);
				$post_data = array(
					'title' 	=> $recent_posts['post_title'],
					'permalink' 	=> get_permalink( $recent_posts['ID'] ),
				);
				// $categories = get_the_category( $recent_posts['ID'] );
				// print_r($categories);

				if( isset($request['show_thumb']) && $request['show_thumb'] == 1 ) {
					// echo get_the_post_thumbnail( $recent_posts['ID'], 'full' );
					// $post_data['thumbnail'] = get_the_post_thumbnail_url( $recent_posts['ID'],'full' );
					$post_data['thumbnail_show'] = $request['show_thumb'];
					$post_data['thumbnail_size'] = isset( $request['thumb_size'] ) && $request['thumb_size'] != "" ? $request['thumb_size'] : 'full' ;

					$image_id = get_post_thumbnail_id($recent_posts['ID']);
					$alttext = get_post_meta($image_id, 'wp_attachment_image_alt', true);
					$imgsrc = get_the_post_thumbnail_url($recent_posts['ID'], 'full') . "?maxwidth=345&maxheight=259&anchor=middlecenter&quality=95&zoom=1.5";

					$img = printf('<img src="%s" width="%s" height="%s" loading="lazy" alt="%s" />', $imgsrc, "345", "259", $alttext);

					$post_data['thumbnail'] = $img;
				}
				$recent_post_result['recent_posts'][] = $post_data;
			}
		} else {
			$recent_post_result = array( 'status' => null , 'error' => 'There are no recent posts at this time.' );
		}
		wp_reset_query();
		return $recent_post_result;
	}
}

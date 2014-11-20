<?php
/**
 * Created by Eduard
 * Date: 06.11.2014 18:19
 */

class BlogData {

	public static $taxonomies = array( 'post_tag', 'collection');
	public static $content_site_id = 1;

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register_subscription_setting' ) );
		self::$content_site_id = defined( 'GMR_CONTENT_SITE_ID' ) ? GMR_CONTENT_SITE_ID : self::$content_site_id;
		add_action( 'wp_ajax_syndicate-now', array( __CLASS__, 'syndicate_now' ) );
	}

	public static function run( $syndication_id ) {
			$result = self::QueryContentSite( $syndication_id );

			$taxonomy_names = get_object_taxonomies( 'post', 'objects' );
			$defaults = array(
				'status'    =>  get_post_meta( $syndication_id, 'subscription_post_status', true ),
			);

			foreach( $taxonomy_names as $taxonomy ) {
				$label = $taxonomy->name;

				// Use get_post_meta to retrieve an existing value from the database.
				$terms = get_post_meta( $syndication_id, 'subscription_default_terms-' . $label, true );
				$terms = explode( ',', $terms );
				$defaults[ $label ] = $terms;

			}

			foreach ( $result as $single_post ) {
				self::ImportPosts(
					$single_post['post_obj']
					, $single_post['post_metas']
					, $defaults
					, $single_post['featured']
					, $single_post['attachments']
					, $single_post['galleries']
				);
			}
	}

	public static function syndicate_now() {

		// get nonce from ajax post
		$nonce = $_POST['syndication_nonce'];

		// verify nonce, with predifined
		if ( ! wp_verify_nonce( $nonce, 'perform-syndication-nonce' ) ) {
			die ( ':P' );
		}

		// run syndication
		if( isset( $_POST['syndication_id'] ) && is_numeric( $_POST['syndication_id'] ) ) {
			$syndication_id = intval( $_POST['syndication_id'] );

			self::run( $syndication_id );
		}

		die();
	}

	// Rgister setting to store last syndication timestamp
	public static function register_subscription_setting() {
		register_setting( 'syndication_last_performed', 'syndication_last_performed', 'intval' );
	}

	/**
	 * Get all terms of all taxonomies from the content site
	 *
	 * @return array
	 */
	public static function getTerms() {
		global $switched;

		$terms = array();
		switch_to_blog( self::$content_site_id );

		foreach( self::$taxonomies as $taxonomy ) {
			if( taxonomy_exists( $taxonomy ) ) {
				$args = array(
					'get'        => 'all',
					'hide_empty' => false
				);

				$terms[ $taxonomy ] = get_terms( $taxonomy, $args );
			}
		}

		restore_current_blog();

		return $terms;
	}


	/**
	 * Get all active subscription ordered by default post status
	 * Defualt post status "Draft" has higher priority as it's less public
	 *
	 * @return array of post objects
	 */
	public static function GetActiveSubscriptions() {

		$args = array(
			'post_type' => 'subscription',
			'post_status' => 'publish',
			'meta_key' => 'subscription_post_status',
			'orderby' => 'meta_value',
			'order' => 'ASC',
		);

		$active_subscriptions = get_posts( $args );

		return $active_subscriptions;
	}

	/**
	 * Query content site and return full query result
	 *
	 * @param int    $subscription_id
	 * @param string $post_type
	 *
	 * @return array WP_Post objects
	 */
	public static function QueryContentSite( $subscription_id, $post_type = 'post' ) {
		global $switched;

		$result = array();

		$last_queried = get_option( 'syndication_last_performed', 0);

		// query args
		$args = array(
			'post_type'     =>  $post_type,
			'post_status'   =>  'publish',
			'posts_per_page' => -1,
			'tax_query'     =>  array(
				'relation'  =>  'OR'
			),
			'date_query'    => array(
				'after'     => $last_queried
			)
		);

		// get filters to query content site
		foreach( self::$taxonomies as $taxonomy ) {
			$subscription_filter = get_post_meta( $subscription_id, 'subscription_filter_terms-' . $taxonomy, true );
			$tax_query['taxonomy'] = $taxonomy;
			$tax_query['field'] = 'name';
			$tax_query['terms'] = explode( ',', $subscription_filter );
			array_push( $args['tax_query'], $tax_query );
		}

		// switch to content site
		switch_to_blog( self::$content_site_id );

		// get all postst matching filters
		$query = get_posts( $args );
		
		// get all metas
		foreach ( $query as $single_result ) {
			$metas	= get_metadata( $post_type, $single_result->ID, true );
			$media = get_attached_media( 'image', $single_result->ID );
			$featured = wp_get_attachment_image_src( get_post_thumbnail_id( $single_result->ID ), 'full' );
			$galleries = get_post_galleries( $single_result->ID, false );

			$result[] = array(
				'post_obj'      =>  $single_result,
				'post_metas'    =>  $metas,
				'attachments'   =>  $media,
				'featured'      =>  $featured[0],
				'galleries'      =>  $galleries
			);
		}

		restore_current_blog();

		return $result;
	}

	/**
	 * Import posts from content site and all related media
	 *
	 * @param object $post WP_POST object
	 * @param array  $metas
	 * @param array  $defaults
	 * @param string $featured
	 * @param array  $attachments
	 *
	 * @return int|\WP_Error
	 */
	public static function ImportPosts( $post, $metas = [], $defaults, $featured = null, $attachments = [], $galleries = [] ) {

		$post_name = sanitize_title( $post->post_name );
		$post_title = sanitize_text_field( $post->post_title );
		$post_type = sanitize_text_field( $post->post_type );
		$post_status = sanitize_text_field( $defaults['status'] );

		// prepare arguments for wp_insert_post
		$args = array(
			'post_title'    =>  $post_title,
			'post_content'  =>  $post->post_content,
			'post_type'     =>  $post_type,
			'post_name'     =>  $post_name,
			'post_status'   =>  $post_status
		);

		// create unique meta value for imported post
		$post_hash = trim( $post->post_title ) . $post->post_modified;
		$post_hash = md5( $post_hash );


		// query to check whether post already exist
		$meta_query_args = array(
			'meta_key'     => 'syndication_old_name',
			'meta_value'   => $post_name,
			'post_status' => 'any',
		);

		$existing = get_posts( $meta_query_args );

		$updated = 0;
		$post_id = 0;

		// check whether post with that name exist
		if( !empty( $existing ) ) {
			$post_id = intval( $existing[0]->ID );
			$hash_value = get_post_meta( $post_id, 'syndication_import', true );

			if( $hash_value != $post_hash ) {
				// post has been updated, override existing one
				$args['ID'] = $post_id;
				wp_insert_post( $args );
				if( !empty( $metas ) ) {
					foreach ( $metas as $meta_key => $meta_value ) {
						$meta_value = sanitize_text_field( $meta_value );
						update_post_meta( $post_id, $meta_key, $meta_value );
					}
				}
				$updated = 1;
			}
		} else {
			$post_id = wp_insert_post( $args );
			if( is_numeric( $post_id ) && !empty( $metas ) ) {
				foreach ( $metas as $meta_key => $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
			$updated = 1;
		}

		/**
		 * Post has been updated or created, assign default terms
		 * Import featured and attached images
		 */
		if( $updated ) {

			update_post_meta( $post_id, 'syndication_import', $post_hash );
			update_post_meta( $post_id, 'syndication_old_name', $post_name );
			$post_data = array(
				'id' => intval( $post->ID ),
				'blog_id' => self::$content_site_id
			);
			update_post_meta( $post_id, 'syndication_old_data', serialize( $post_data ) );

			self::AssignDefaultTerms( $post_id, $defaults );

			if( !is_null( $featured) ) {
				$featured = esc_url_raw( $featured );
				self::ImportMedia( $post_id, $featured, true );
			}

			if( !is_null( $attachments ) ) {
				self::ImportAttachedImages( $post_id, $attachments );
			}

			self::ReplaceGalleryID( $post_id, $galleries );
		}

		return $post_id;
	}

	/**
	 * Set post default terms
	 *
	 * @param $post_id
	 * @param $defaults - associative array with $taxonomy => array( 'term_ids' )
	 */
	public static function AssignDefaultTerms( $post_id, $defaults ) {
		if( !empty( $defaults ) ) {
			foreach ( $defaults as $taxonomy => $default_terms ) {
				if( $post_id && taxonomy_exists( $taxonomy ) ) {
					if( is_taxonomy_hierarchical( $taxonomy) ) {
						wp_set_post_terms( $post_id, $default_terms, $taxonomy );
					} else {
						$term_obj = get_term_by( 'id', absint( $default_terms[0] ), $taxonomy );
						wp_set_post_terms( $post_id, $term_obj->name, $taxonomy );
					}
				}
			}
		}
	}

	/**
	 * Import all attached images
	 *
	 * @param $post_id
	 * @param $attachments
	 */
	public static function ImportAttachedImages( $post_id, $attachments) {
		foreach ( $attachments as $attachment ) {
			$filename = esc_url_raw( $attachment->guid );
			self::ImportMedia( $post_id, $filename, false, $attachment->ID );
		}
	}

	/**
	 * Helper function to import images
	 * Reused code from
	 * http://codex.wordpress.org/Function_Reference/media_handle_sideload
	 *
	 * @param int    $post_id  - Post ID of the post to assign featured image if $featured is true
	 * @param string $filename - URL of the image to upload
	 * @param bool   $featured - Imported image should be featured or not
	 *
	 * @return int|object
	 */
	public static function ImportMedia( $post_id = 0, $filename, $featured = false, $old_id = 0 ) {
		$id = 0;
		$old_id = intval( $old_id );
		$tmp = download_url( $filename );

		if( $old_id == 0 && $featured == true ) {
			$meta_query_args = array(
				'meta_key'     => 'syndication_attachment_old_url',
				'meta_value'   => esc_url_raw( $filename ),
				'post_type' => 'attachment',
			);
		} else {
			$meta_query_args = array(
				'meta_key'     => 'syndication_attachment_old_id',
				'meta_value'   => $old_id,
				'post_type' => 'attachment',
			);
		}

		// query to check whether post already exist
		$existing = get_posts( $meta_query_args );

		if( empty($existing) ) {
			preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|Jpeg|JPEG|gif|GIF|png|PNG)/', $filename, $matches );

			// make sure we have a match.  This won't be set for PDFs and .docs
			if ( $matches && isset( $matches[0] ) ) {
				$file_array['name'] = basename( $matches[0] );
				$file_array['tmp_name'] = $tmp;

				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink( $file_array['tmp_name'] );
					$file_array['tmp_name'] = '';
				}

				// do the validation and storage stuff
				$id = media_handle_sideload( $file_array, $post_id, null );

				// If error storing permanently, unlink
				if ( is_wp_error( $id ) ) {
					@unlink( $file_array['tmp_name'] );
				} else {
					@unlink( $file_array['tmp_name'] );
					if( $featured == true && $post_id != 0 ) {
						set_post_thumbnail( $post_id, $id );
					}
				}
			} else {
				@unlink( $tmp );
			}

			update_post_meta( $id, 'syndication_attachment_old_id', $old_id );
			update_post_meta( $id, 'syndication_attachment_old_url', esc_url_raw( $filename ) );

		} else {
			$id = $existing[0]->ID;
			if( $featured == true && $post_id != 0 ) {
				set_post_thumbnail( $post_id, $id );
			}
		}

		return $id;
	}

	/**
	 * Replace the ids in post content to mathc the new attachments
	 *
	 * @param int   $post_id
	 * @param array $galleries
	 */
	private static function ReplaceGalleryID( $post_id, $galleries = [] ) {

		// get post content
		$post = get_post( $post_id);
		$content = $post->post_content;

		if( !empty( $galleries ) ) {
			foreach ( $galleries as $gallery ) {
				$new_gallery = '[gallery ids="';
				$old_ids     = explode( ",", $gallery["ids"] );
				foreach ( $gallery['src'] as $index => $image_src ) {
					$meta_query_args = array(
						'meta_key'   => 'syndication_attachment_old_url',
						'meta_value' => esc_url_raw( $image_src ),
						'post_type'  => 'attachment',
					);

					$existing = get_posts( $meta_query_args );

					if ( ! empty( $existing ) ) {
						$new_gallery .= $existing[0]->ID . ",";
					} else {
						$new_id = self::ImportMedia( 0, $image_src, false, $old_ids[ $index ] );
						$new_gallery .= $new_id . ",";
					}
				}

				$new_gallery .= "\"]";

				// replace old gallery with new gallery
				$content = str_replace( '[gallery ids="' . $gallery["ids"] . '"]', $new_gallery, $content );

				// update new post
				wp_update_post(
					array(
						'ID'            =>  $post_id,
						'post_content'  =>  $content
					)
				);
			}
		}
	}
}

BlogData::init();
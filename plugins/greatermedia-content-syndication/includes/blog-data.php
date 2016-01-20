<?php
/**
 * Created by Eduard
 * Date: 06.11.2014 18:19
 */

class BlogData {

	public static $taxonomies = array(
		'category'      =>  'single',
		'post_tag'      =>  'multiple',
		'collection'    =>  'single',
	);

	public static $content_site_id;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'get_content_site_id' ), 1 );
		add_action( 'wp_ajax_syndicate-now', array( __CLASS__, 'syndicate_now' ) );
		add_action( 'admin_notices', array( __CLASS__, 'add_notice_for_undefined' ) );
	}

	public static function add_notice_for_undefined() {
		if( !defined( 'GMR_CONTENT_SITE_ID' ) ) {
		?>
		<div class="error">
			<p>
				No Content Factory site ID is defined!
				Using default ID <?php echo self::$content_site_id; ?>!
				Please define GMR_CONTENT_SITE_ID in config
			</p>

		</div>
		<?php
		}
	}

	public static function get_content_site_id() {
		if ( defined( 'GMR_CONTENT_SITE_ID' ) ) {
			self::$content_site_id = GMR_CONTENT_SITE_ID;
		} elseif ( is_multisite() ) {
			self::$content_site_id = get_current_site()->blog_id;
		} else {
			self::$content_site_id = 1;
		}
	}

	public static function syndicate_now() {
		// verify nonce, with predifined
		if ( ! wp_verify_nonce( $_POST['syndication_nonce'], 'perform-syndication-nonce' ) ) {
			die( ':P' );
		}

		// run syndication
		$syndication_id = filter_input( INPUT_POST, 'syndication_id', FILTER_VALIDATE_INT );
		$total = $syndication_id > 0 ? self::run( $syndication_id ) : 0;

		echo (int) $total;
		exit;
	}

	public static function run( $syndication_id, $offset = 0 ) {
		global $edit_flow, $gmrs_editflow_custom_status_disabled;

		// disable editflow influence
		if ( $edit_flow && ! empty( $edit_flow->custom_status ) && is_a( $edit_flow->custom_status, 'EF_Custom_Status' ) ) {
			$gmrs_editflow_custom_status_disabled = true;
			remove_filter( 'wp_insert_post_data', array( $edit_flow->custom_status, 'fix_custom_status_timestamp' ), 10, 2 );
		}

		$result = self::QueryContentSite( $syndication_id, '', '', $offset );
		$taxonomy_names = SyndicationCPT::$support_default_tax;
		$defaults = array(
			'status' =>  get_post_meta( $syndication_id, 'subscription_post_status', true ),
		);

		$max_pages = $result['max_pages'];
		$total_posts = $result['found_posts'];

		unset( $result['max_pages'] );
		unset( $result['found_posts'] );

		foreach( $taxonomy_names as $taxonomy ) {
			$taxonomy = get_taxonomy( $taxonomy );
			$label = $taxonomy->name;

			// Use get_post_meta to retrieve an existing value from the database.
			$terms = get_post_meta( $syndication_id, 'subscription_default_terms-' . $label, true );
			$terms = explode( ',', $terms );
			$defaults[ $label ] = $terms;
		}

		$imported_post_ids = array();

		$my_home_url = trailingslashit( home_url() );
		switch_to_blog( self::$content_site_id );
		$content_home_url = trailingslashit( home_url() );
		restore_current_blog();

		foreach ( $result as $single_post ) {
			$post_id = self::ImportPosts(
				$single_post['post_obj']
				, $single_post['post_metas']
				, $defaults
				, $single_post['featured']
				, $single_post['attachments']
				, $single_post['gallery_attachments']
				, $single_post['galleries']
				, $single_post['term_tax']
			);

			if ( $post_id > 0 ) {
				array_push( $imported_post_ids, $post_id );
				self::NormalizeLinks( $post_id, $my_home_url, $content_home_url );
			}
		}

		$imported_post_ids = implode( ',', $imported_post_ids );

		self::add_or_update( 'syndication_imported_posts', $imported_post_ids );
		set_transient( 'syndication_imported_posts', $imported_post_ids, WEEK_IN_SECONDS * 4 );

		$offset += 1;
		if( $max_pages > $offset )  {
			self::run( $syndication_id, $offset );
		}

		//update_option( 'syndication_last_performed', current_time( 'timestamp', 1 ) );
		update_post_meta( $syndication_id, 'syndication_last_performed', current_time( 'timestamp', 1 ) );

		return $total_posts;
	}

	/**
	 * Query content site and return full query result
	 *
	 * @param int $subscription_id
	 * @param string $start_date
	 *
	 * @return array WP_Post objects
	 */
	public static function QueryContentSite( $subscription_id , $start_date = '', $end_date = '', $offset = 0 ) {
		$result = array();

		if ( $start_date == '' ) {
			//$last_queried = get_option( 'syndication_last_performed', 0);
			$last_queried = get_post_meta( $subscription_id, 'syndication_last_performed', true );
			if ( $last_queried ) {
				$last_queried = date( 'Y-m-d H:i:s', $last_queried );
			} else {
				$last_queried = date( 'Y-m-d H:i:s', 0 );
			}
		} else {
			$last_queried = $start_date;
		}

		$post_type = get_post_meta( $subscription_id, 'subscription_type', true );
		if ( empty( $post_type ) ) {
			$post_type = SyndicationCPT::$supported_subscriptions;
		}

		// query args
		$args = array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'future', 'private' ),
			'posts_per_page' => 500,
			'offset'         => $offset * 500,
			'tax_query'      => array(),
			'date_query'     => array(
				'column' => 'post_modified_gmt',
				'after'  => $last_queried,
			),
		);

		if ( ! empty( $end_date ) ) {
			$args['date_query']['before'] = $end_date;
		}

		$enabled_taxonomy = get_post_meta( $subscription_id, 'subscription_enabled_filter', true );
		$enabled_taxonomy = sanitize_text_field( $enabled_taxonomy );

		// get filters to query content site
		$subscription_filter = get_post_meta( $subscription_id, 'subscription_filter_terms-' . $enabled_taxonomy, true );
		$subscription_filter = sanitize_text_field( $subscription_filter );

		if ( $subscription_filter != '' ) {
			if ( self::$taxonomies[$enabled_taxonomy] == 'multiple' ) {
				$subscription_filter = explode( ',', $subscription_filter );
				$args['tax_query']['relation'] = 'AND';
			}

			$tax_query['taxonomy'] = $enabled_taxonomy;
			$tax_query['field'] = 'slug';
			$tax_query['terms'] = $subscription_filter;
			$tax_query['operator'] = 'AND';

			array_push( $args['tax_query'], $tax_query );
		}

		// switch to content site
		switch_to_blog( self::$content_site_id );

		// get all postst matching filters
		$wp_custom_query = new WP_Query( $args );

		// get all metas
		foreach ( $wp_custom_query->posts as $single_result ) {
			$result[] = self::PostDataExtractor( $post_type, $single_result );
		}

		$result['max_pages'] = $wp_custom_query->max_num_pages;
		$result['found_posts'] = $wp_custom_query->found_posts;

		restore_current_blog();

		return $result;
	}

	// Rgister setting to store last syndication timestamp
	public static function PostDataExtractor( $post_type, $single_result ) {
		$metas = get_metadata( 'post', $single_result->ID, '', true );
		$media = get_attached_media( 'image', $single_result->ID );

		$featured_src = '';
		$featured_id = get_post_thumbnail_id( $single_result->ID );
		if ( $featured_id ) {
			$featured_src = wp_get_attachment_image_src( $featured_id, 'full' );
			if ( $featured_src ) {
				$featured_src = $featured_src[0];
			}
		}
		
		$galleries = get_post_galleries( $single_result->ID, false );
		foreach ( $galleries as &$gallery ) {
			if ( ! empty( $gallery['ids'] ) ) {
				$image_ids = array_filter( array_map( 'intval', explode( ',', $gallery['ids'] ) ) );
				$gallery['ids'] = implode( ',', $image_ids );
				$gallery['src'] = array();
				
				foreach ( $image_ids as $image_id ) {
					$image_src = wp_get_attachment_image_src( $image_id, 'full' );
					if ( ! empty( $image_src ) ) {
						$gallery['src'][] = $image_src[0];
					}
				}
			}
		}

		$attachments = array();
		if ( 'gmr_gallery' == $post_type ) {
			$attachments = get_post_meta( $single_result->ID, 'gallery-image' );
			$attachments = array_filter( array_map( 'get_post', $attachments ) );
		}

		$term_tax = array();
		$taxonomies = get_object_taxonomies( $single_result );
		foreach ( $taxonomies as $taxonomy ) {
			$term_tax[$taxonomy][] = wp_get_object_terms( $single_result->ID, $taxonomy, array( "fields" => "names" ) );
		}

		return array(
			'post_obj'            => $single_result,
			'post_metas'          => $metas,
			'attachments'         => $media,
			'gallery_attachments' => $attachments,
			'featured'            => $featured_id ? array( $featured_id, $featured_src ) : null,
			'galleries'           => $galleries,
			'term_tax'            => $term_tax
		);
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
	public static function ImportPosts( $post, $metas, $defaults, $featured, $attachments, $gallery_attachments, $galleries, $term_tax, $force_update = false ) {
		if ( ! $post ) {
			return;
		}
		
		$post_name = sanitize_title( $post->post_name );
		$post_title = sanitize_text_field( $post->post_title );
		$post_type = sanitize_text_field( $post->post_type );
		$post_status = sanitize_text_field( $defaults['status'] );

		// prepare arguments for wp_insert_post
		$args = array(
			'post_title'        => $post_title,
			'post_content'      => $post->post_content,
			'post_author'       => $post->post_author,
			'post_type'         => $post_type,
			'post_name'         => $post_name,
			'post_status'       => ! empty( $post_status ) ? $post_status : $post->post_status,
			'post_date'         => $post->post_date,
			'post_date_gmt'     => $post->post_date_gmt,
			'post_modified'     => $post->post_modified,
			'post_modified_gmt' => $post->post_modified_gmt,
		);

		if ( 'publish' == $post_status ) {
			$args['post_modified'] = current_time( 'mysql' );
			$args['post_modified_gmt'] = current_time( 'mysql', 1 );
		}

		// create unique meta value for imported post
		$post_hash = trim( $post->post_title ) . $post->post_modified;
		$post_hash = md5( $post_hash );


		// query to check whether post already exist
		$meta_query_args = array(
			'meta_key'    => 'syndication_old_name',
			'meta_value'  => $post_name,
			'post_status' => 'any',
			'post_type'   => $post_type
		);

		$existing = get_posts( $meta_query_args );

		$updated = 0;
		$post_id = 0;

		// check whether post with that name exist
		if ( ! empty( $existing ) ) {
			$post_id = intval( $existing[0]->ID );
			$hash_value = get_post_meta( $post_id, 'syndication_import', true );
			if ( $hash_value != $post_hash || $force_update ) {
				// post has been updated, override existing one
				$args['ID'] = $post_id;
				
				wp_update_post( $args );
				if ( ! empty( $metas ) ) {
					foreach ( $metas as $meta_key => $meta_value ) {
						update_post_meta( $post_id, $meta_key, $meta_value[0] );
					}
				}
				$updated = 2;
			}
		} else {
			$post_id = wp_insert_post( $args );
			if ( is_numeric( $post_id ) && ! empty( $metas ) ) {
				foreach ( $metas as $meta_key => $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value[0] );
				}
			}
			$updated = 1;
		}

		/**
		 * Post has been updated or created, assign default terms
		 * Import featured and attached images
		 */
		if ( $updated > 0 ) {
			update_post_meta( $post_id, 'syndication_import', $post_hash );
			update_post_meta( $post_id, 'syndication_old_name', $post_name );
			update_post_meta( $post_id, 'syndication_old_data', serialize( array(
				'id' => intval( $post->ID ),
				'blog_id' => self::$content_site_id
			) ) );

			if ( ! empty( $term_tax ) ) {
				foreach ( $term_tax as $taxonomy => $terms ) {
					if ( ! empty( $terms[0] ) && taxonomy_exists( $taxonomy ) ) {
						if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
							wp_set_object_terms( $post_id, $terms[0], $taxonomy, true );
						} else {
							foreach ( $terms[0] as $term_name ) {
								$term_id = term_exists( $term_name, $taxonomy );
								if ( $term_id ) {
									$category = get_term_by( 'name', $term_name, $taxonomy );
									wp_set_object_terms( $post_id, $category->term_id, $taxonomy, true );
								} else {
									$new_term = wp_insert_term( $term_name, $taxonomy );
									if ( ! is_wp_error( $new_term ) ) {
										wp_set_object_terms( $post_id, $new_term['term_id'], $taxonomy, true );
									}
								}
							}
						}
					}
				}
			}

			if ( $updated == 1 ) {
				self::AssignDefaultTerms( $post_id, $defaults );
			}

			$uncategorized = get_term_by( 'name', 'Uncategorized', 'category' );
			if ( $uncategorized ) {
				wp_remove_object_terms( $post_id, $uncategorized->term_id, 'category' );
			}

			if ( ! is_null( $featured ) ) {
				self::ImportMedia( $post_id, esc_url_raw( $featured[1] ), true, $featured[0] );
			}

			if ( ! is_null( $attachments ) ) {
				self::ImportAttachedImages( $post_id, $attachments );
			}

			self::ReplaceGalleryID( $post_id, $galleries );

			if ( 'gmr_gallery' == $post_type ) {
				delete_post_meta( $post_id, 'gallery-image' );
				$imported = self::ImportAttachedImages( $post_id, $gallery_attachments );
				foreach ( $imported as $attachment ) {
					add_post_meta( $post_id, 'gallery-image', $attachment );
				}
			}
		}

		clean_post_cache( $post_id );

		return $post_id;
	}

	/**
	 * Updates links in a post content to lead to a proper site.
	 *
	 * @param int $post_id The new post id.
	 * @param string $my_home_url The current site home URL.
	 * @param string $content_home_url The content site home URL.
	 */
	private static function NormalizeLinks( $post_id, $my_home_url, $content_home_url ) {
		$post = get_post( $post_id );

		// we need to properly update image src and class name
		$imgs = array();
		if ( preg_match_all( '#\<img .*?>#is', $post->post_content, $imgs ) ) {
			foreach ( $imgs[0] as $img ) {
				$attrs = array();
				if ( preg_match_all( '#(\w+)=[\'"](.*?)[\'"]#is', $img, $attrs ) ) {
					$attrs = array_combine( $attrs[1], $attrs[2] );
					if ( isset( $attrs['src'] ) ) {
						$new_src = str_replace( $content_home_url, $my_home_url, $attrs['src'] );
						$post->post_content = str_replace( $attrs['src'], $new_src, $post->post_content );
					}

					$class = array();
					if ( isset( $attrs['class'] ) && preg_match( '#wp-image-(\d+)#i', $attrs['class'], $class ) ) {
						$attachment = get_posts( array(
							'meta_key'   => 'syndication_attachment_old_id',
							'meta_value' => $class[1],
							'post_type'  => 'attachment',
						) );

						if ( ! empty( $attachment ) ) {
							$post->post_content = str_replace( $class[0], 'wp-image-' . $attachment[0]->ID, $post->post_content );
						}
					}
				}
			}
		}

		// update else links
		$post->post_content = str_replace( $content_home_url, $my_home_url, $post->post_content );

		// save changes
		wp_update_post( $post->to_array() );
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
						wp_set_post_terms( $post_id, $default_terms, $taxonomy, true );
					} else {
						$term_obj = get_term_by( 'id', absint( $default_terms[0] ), $taxonomy );
						wp_set_post_terms( $post_id, $term_obj->name, $taxonomy, true );
					}
				}
			}
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
	public static function ImportMedia( $post_id, $filename, $featured = false, $old_id = 0 ) {

		require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
		require_once( ABSPATH . 'wp-admin' . '/includes/file.php' );
		require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );

		$id = 0;
		$old_id = intval( $old_id );
		$tmp = download_url( $filename );

		if( $old_id == 0 && $featured == true ) {
			$meta_query_args = array(
				'meta_key'   => 'syndication_attachment_old_url',
				'meta_value' => esc_url_raw( $filename ),
				'post_type'  => 'attachment',
			);
		} else {
			$meta_query_args = array(
				'meta_key'   => 'syndication_attachment_old_id',
				'meta_value' => $old_id,
				'post_type'  => 'attachment',
			);
		}

		// query to check whether post already exist
		$existing = get_posts( $meta_query_args );
		if ( empty( $existing ) ) {
			preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $filename, $matches );

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

			if ( ! is_wp_error( $id ) ) {
				update_post_meta( $id, 'syndication_attachment_old_id', $old_id );
				update_post_meta( $id, 'syndication_attachment_old_url', esc_url_raw( $filename ) );

				self::updateImageCaption( $id, $old_id );
			}
		} else {
			$id = $existing[0]->ID;
			if ( $featured == true && $post_id != 0 ) {
				set_post_thumbnail( $post_id, $id );
			}

			self::updateImageCaption( $id, $old_id );
		}

		if ( ! empty( $old_id ) ) {
			switch_to_blog( self::$content_site_id );
			$attribution = get_post_meta( $old_id, 'gmr_image_attribution', true );
			restore_current_blog();

			if ( ! empty( $attribution ) ) {
				add_post_meta( $id, 'gmr_image_attribution', $attribution );
			}
		}

		return $id;
	}

	/**
	 * Import all attached images
	 *
	 * @param $post_id
	 * @param $attachments
	 */
	public static function ImportAttachedImages( $post_id, $attachments) {
		$imported = array();
		foreach ( $attachments as $attachment ) {
			$filename = esc_url_raw( $attachment->guid );
			$imported[] = self::ImportMedia( $post_id, $filename, false, $attachment->ID );
		}

		return $imported;
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

		if ( ! empty( $galleries ) ) {
			foreach ( $galleries as $gallery ) {
				if ( ! isset( $gallery['ids'] ) ) {
					continue;
				}

				$new_gallery_ids = '';
				$old_ids = explode( ",", $gallery["ids"] );
				foreach ( $gallery['src'] as $index => $image_src ) {
					$meta_query_args = array(
						'meta_key'   => 'syndication_attachment_old_url',
						'meta_value' => esc_url_raw( $image_src ),
						'post_type'  => 'attachment',
					);

					$existing = get_posts( $meta_query_args );

					if ( ! empty( $existing ) ) {
						$new_gallery_ids .= $existing[0]->ID . ",";
						if ( ! empty( $old_ids[ $index ] ) ) {
							self::updateImageCaption( $existing[0]->ID, $old_ids[ $index ] );
						}
					} elseif ( ! empty( $old_ids[ $index ] ) ) {
						$new_id = self::ImportMedia( $post_id, $image_src, false, $old_ids[ $index ] );
						if ( $new_id && ! is_wp_error( $new_id ) ) {
							$new_gallery_ids .= $new_id . ",";
						}
					}
				}

				// replace old gallery with new gallery
				$content = preg_replace( '/(\[gallery.*ids=*)\"([0-9,]*)(\".*\])/', '$1"' . $new_gallery_ids . '$3', $content  );

				// update new post
				wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );
			}
		}
	}

	/**
	 * Updates image caption.
	 */
	private static function updateImageCaption( $new_id, $old_id ) {
		if ( self::$content_site_id ) {
			switch_to_blog( self::$content_site_id );
			$old_post = get_post( $old_id, ARRAY_A );
			restore_current_blog();

			if ( ! empty( $old_post ) ) {
				$new_post = get_post( $new_id, ARRAY_A );
				if ( ! empty( $new_post ) ) {
					$new_post['post_excerpt'] = $old_post['post_excerpt'];
					wp_update_post( $new_post );
				}
			}
		}
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

		foreach( self::$taxonomies as $taxonomy => $type ) {
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
	 * Adds or udpates the existing option
	 *
	 * @param $name  Option name
	 * @param $value New value of the option
	 *
	 * @return bool  Returns whether option has been added or updated
	 */
	public static function add_or_update( $name, $value ) {
		$success = add_option( $name, $value, '', 'no' );

		if ( ! $success ) {
			$success = update_option( $name, $value );
		}

		return $success;
	}
	
}

BlogData::init();
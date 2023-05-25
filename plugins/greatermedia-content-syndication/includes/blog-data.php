<?php
/**
 * Created by Eduard
 * Date: 06.11.2014 18:19
 */

class BlogData {

	public static $taxonomies = array(
			'category'      =>  'single',
			'collection'    =>  'single',
	);

	public static $log = array();

	public static $syndication_id;

	public static $content_site_id;

	/**
	 * Returns the old attachment id meta key. Keyed on content site id to
	 * address,
	 *
	 * https://tenup.teamwork.com/#/tasks/18643043?c=8629088
	 */
	public static function get_attachment_old_id_key() {
		return "syndication_" . self::$content_site_id . '_attachment_old_id';
	}

	/**
	 * Returns the old attachment url meta key. Keyed on content site id to
	 * address,
	 *
	 * https://tenup.teamwork.com/#/tasks/18643043?c=8629088
	 */
	public static function get_attachment_old_url_key() {
		return "syndication_" . self::$content_site_id . '_attachment_old_url';
	}

	/**
	 * Did we use the "Syndicate Now" Button
	 *
	 * @var bool
	 */
	public static $syndicate_now = false;

	/**
	 * Unique ID for this syndication process. Added to logs, to make tracking a single event easier
	 *
	 * @var string
	 */
	public static $syndication_uniqid = '';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'get_content_site_id' ), 1 );
		add_action( 'wp_ajax_syndicate-now', array( __CLASS__, 'syndicate_now' ) );
		add_action( 'admin_notices', array( __CLASS__, 'add_notice_for_undefined' ) );
		add_action( 'ep_sync_post_on_transition', array( __CLASS__, 'sync_post_on_transition' ) );

		self::$syndication_uniqid = uniqid( "SYN", true );
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

	/**
	 * Set content site id
	 *
	 * @param $id int
	 */
	public static function set_content_site_id( $id ) {
		self::$content_site_id = $id;
	}

	public static function syndicate_now() {
		// verify nonce, with predifined
		if ( ! wp_verify_nonce( $_POST['syndication_nonce'], 'perform-syndication-nonce' ) ) {
			error_log( self::syndication_log_prefix()."Nonce did not verify for Syndicate Now Button"."\n" );
			die( ':P' );
		}

		// run syndication
		error_log( self::syndication_log_prefix()." Starting 'Syndicate Now' Process \n" );

		self::$syndicate_now = true;
		$syndication_id = filter_input( INPUT_POST, 'syndication_id', FILTER_VALIDATE_INT );
		$total = $syndication_id > 0 ? self::run( $syndication_id ) : 0;

		// Remove syndication lock when running manually
		delete_post_meta( $syndication_id, 'subscription_running' );

		if ( ! is_numeric( $total ) ) {
			self::log( "A non numerical response was received from self::run in " . __FILE__ . ":" . __LINE__ . ". Response was " . var_export( $total, true ) );
		}

		if ($total !== 0) {
			error_log( self::syndication_log_prefix()." Syndication complete! \n" );
		}

		wp_send_json( array(
				'total' => (int) $total,
				'unique_id' => self::$syndication_uniqid,
		) );
		exit;
	}

	/**
	 * If available, will set HyperDB to query the master DB, to avoid any issues with lagging replicas
	 */
	public static function set_primary_db() {
		global $wpdb;

		if ( method_exists( $wpdb, 'send_reads_to_masters' ) ) {
			$wpdb->send_reads_to_masters();
		}

	}

	public static function run( $syndication_id, $offset = 0, $force = false ) {
		$result = false;

		self::$syndication_id = $syndication_id;
		self::$log = array();
		self::set_primary_db();

		try {
			if ( is_null( $syndication_id ) ) {
				self::log( "Syndication ID is Null " . __FILE__ . ":" . __LINE__ );
				return false;
			}

			global $edit_flow, $gmrs_editflow_custom_status_disabled;

			if ( ! defined( 'WP_IMPORTING' ) ) {
				define( 'WP_IMPORTING', true );
			}

			$is_running = get_post_meta( $syndication_id, 'subscription_running', true );

			if ( $is_running ) {
				error_log( self::syndication_log_prefix()." Syndication is running. Halting... ".$syndication_id."\n" );
				$four_hours_ago = strtotime( '-4 hour' );
				if ( $is_running <= $four_hours_ago ) {
					// Delete the lock so the job can run again. We should also send an alert.
					delete_post_meta( $syndication_id, 'subscription_running' );
				}
				return 0;
			} else {
				error_log( self::syndication_log_prefix()." Syndication has started \n" );
				add_post_meta( $syndication_id, 'subscription_running', current_time( 'timestamp', 1 ) );
			}

			// disable editflow influence
			if ( $edit_flow && ! empty( $edit_flow->custom_status ) && is_a( $edit_flow->custom_status, 'EF_Custom_Status' ) ) {
				$gmrs_editflow_custom_status_disabled = true;
				remove_filter( 'wp_insert_post_data', array( $edit_flow->custom_status, 'fix_custom_status_timestamp' ), 10, 2 );
			}

			$result = self::_run( $syndication_id, $offset, $force );
		} catch ( Exception $e ) {
			error_log( self::syndication_log_prefix()."[EXCEPTION]: ".$e->getMessage() ."\n" );
		}

		// self::flush_log(); // uncomment it if you need debugging log

		return $result;
	}

	private static function _run( $syndication_id, $offset = 0, $force = false ) {
		// error_log( self::syndication_log_prefix(). "Start querying content site with offset = ". $offset ."...\n" );

		// Get the current time before we start querying, so that we know next time we use this value it was the value
		// from before querying for content
		$last_run = current_time( 'timestamp', 1 );

		$result = self::QueryContentSite( $syndication_id, '', '', $offset );
		$taxonomy_names = SyndicationCPT::$support_default_tax;
		$defaults = array(
				'status'         =>  get_post_meta( $syndication_id, 'subscription_post_status', true ),
				'last_performed' => get_post_meta( $syndication_id, 'syndication_last_performed', true ),
		);

		$max_pages = $result['max_pages'];
		$total_posts = $result['found_posts'];

		unset( $result['max_pages'] );
		unset( $result['found_posts'] );

		error_log( self::syndication_log_prefix(). "Found $total_posts posts ($max_pages max pages) from content site" ."\n" );

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

		// @remove after debugging
		foreach ( $result as $single_post ) {
			try {
				$post_id = self::ImportPosts(
						$single_post['post_obj'],
						$single_post['post_metas'],
						$defaults,
						$single_post['featured'],
						$single_post['attachments'],
						$single_post['gallery_attachments'],
						$single_post['galleries'],
						$single_post['page_metas'],
						$single_post['listicle_metas'],
						$single_post['am_metas'],
						$single_post['am_item_photo_attachment'],
						$single_post['show_metas'],
						$single_post['show_logo_metas'],
						$single_post['term_tax'],
						$force
				);

				if ( $post_id > 0 ) {
					array_push( $imported_post_ids, $post_id );
					self::NormalizeLinks( $post_id, $my_home_url, $content_home_url );
					error_log( self::syndication_log_prefix(). " Success for import of post \"".$single_post['post_obj']->post_title."\" ($post_id)" );
				}
			} catch( Exception $e ) {
				self::log( "[EXCEPTION-DURING-IMPORT_POST]: %s", $e->getMessage() );
				error_log( self::syndication_log_prefix(). " Error During post import: ".$e->getMessage() );
			}
		}

		$imported_post_ids = implode( ',', $imported_post_ids );

		self::add_or_update( 'syndication_imported_posts', $imported_post_ids );
		set_transient( 'syndication_imported_posts', $imported_post_ids, WEEK_IN_SECONDS * 4 );

		// Only allow iterating past the first 10 results with wp-cli (edge case)
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$offset += 1;
			if( $max_pages > $offset )  {
				self::_run( $syndication_id, $offset );
			}
		}

		// self::log( "Finished processing content with offset %s", $offset );

		update_post_meta( $syndication_id, 'syndication_last_performed', intval( $last_run ) );
		delete_post_meta( $syndication_id, 'subscription_running' );
		/* add_post_meta( $syndication_id, 'subscription_items_onhalt', $total_posts);
		self::log( "Total syndicated variable set for syndication is %s", $total_posts ); */

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

		$post_type = get_post_meta( $subscription_id, 'subscription_type', true );
		if ( empty( $post_type ) ) {
			$post_type = SyndicationCPT::$supported_subscriptions;
		}

		$subscription_source = absint( get_post_meta( $subscription_id, 'subscription_source', true ) );
		if ( $subscription_source > 0 ) {
			BlogData::set_content_site_id( $subscription_source );
			self::log( "Switch to Custom Source Site : %d", self::$content_site_id );

		} else {
			//Set it to default in case of empty / Legacy subscriptions
			self::get_content_site_id();
			self::log( "Switch to default source site : %d", self::$content_site_id );
		}

		if ( (int) self::$content_site_id === (int) get_current_blog_id() ) {
			self::log( "Stopping syndication, source site equal to destination" );

			return [ 'max_pages' => 0, 'found_posts' => 0 ];
		}

		// Should only be the first time - only pull in 10 posts: https://basecamp.com/1778700/projects/8324102/todos/315096975#comment_546418020
		// Avoids cases where we try and pull in the entire history of posts and it locks up
		$posts_per_page = defined( 'WP_CLI' ) && WP_CLI ? 500 : 200;

		// query args
		$args = array(
				'post_type'      => $post_type,
				'post_status'    => SyndicationCPT::$supported_syndication_statuses,
				'posts_per_page' => $posts_per_page,
				'offset'         => $offset * $posts_per_page,
				'tax_query'      => array(),
				'date_query'     => array(
						'column' => 'post_modified_gmt',
				),
				'orderby' => 'date',
				'order' => 'DESC',
		);

		$start_date = apply_filters( 'beasley_syndication_query_start_date', $start_date );

		if ( $start_date == '' ) {
			$last_queried = get_post_meta( $subscription_id, 'syndication_last_performed', true );
			if ( $last_queried ) {
				$args['date_query']['after'] = date( 'Y-m-d H:i:s', ( $last_queried - 60 ) ); // Subtracting 60 so that we pull in slightly more posts than we need, in case timing is off.
			} else {
				$args['orderby'] = 'date';
				$args['order'] = 'DESC';
			}
		} else {
			$args['date_query']['after'] = $start_date;
		}

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

			$tax_query = array();
			$tax_query['taxonomy'] = $enabled_taxonomy;
			$tax_query['field'] = 'name';
			$tax_query['terms'] = $subscription_filter;
			$tax_query['operator'] = 'AND';

			array_push( $args['tax_query'], $tax_query );
		}

		if ( self::$syndicate_now ) {
			self::log_variable( $args, "Query Args for Syndicate Now" );
		}

		// self::log( "Site ID before switching: " . get_current_blog_id() );

		// switch to content site
		switch_to_blog( self::$content_site_id );

		// self::log( "Site ID after switching: " . get_current_blog_id() );

		// get all postst matching filters
		$wp_custom_query = new WP_Query( $args );

		// self::log( "SQL Query: " . preg_replace( '#\s+#', ' ', $wp_custom_query->request ) );

		// get all metas
		foreach ( $wp_custom_query->posts as $single_result ) {
			$result[] = self::PostDataExtractor( $single_result );
		}

		$result['max_pages'] = $wp_custom_query->max_num_pages;
		$result['found_posts'] = $wp_custom_query->found_posts;

		restore_current_blog();

		return $result;
	}

	// Rgister setting to store last syndication timestamp
	public static function PostDataExtractor( $single_result ) {
		$metas = get_metadata( 'post', $single_result->ID, '', true );
		$metas['embed'] = isset( $metas['_thumbnail_id'][0] ) && $metas['_thumbnail_id'][0] != "" ? self::am_get_metavalue( 'embed', $metas['_thumbnail_id'][0] ) : array() ;
		$media = get_attached_media( 'image', $single_result->ID );
		foreach ( $media as $attachment ) {
			$attachment->alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
			$attachment->attribution = get_post_meta( $attachment->ID, 'gmr_image_attribution', true );
		}

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
		if ( 'gmr_gallery' == $single_result->post_type ) {
			$attachments = get_post_meta( $single_result->ID, 'gallery-image' );
			$attachments = array_filter( array_map( 'get_post', $attachments ) );
			foreach ( $attachments as $attachment ) {
				$attachment->guid = wp_get_attachment_image_url( $attachment->ID, 'full' );
				$attachment->alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
				$attachment->attribution = get_post_meta( $attachment->ID, 'gmr_image_attribution', true );
			}
		}

		$page_metas = array();
		if ( 'page' == $single_result->post_type ) {
			$page_post = get_post($single_result->ID);
			$page_metas['_wp_page_template'] = self::am_get_metavalue( '_wp_page_template', $single_result->ID );
			$page_metas['menu_order'] = $page_post->menu_order;
			$page_parent_slug_var = "";
			if ( isset( $page_post->post_parent ) && $page_post->post_parent != "" )
			{
				$page_parent_slug_array = get_post( $page_post->post_parent );
				$page_parent_slug_var = $page_parent_slug_array->post_name;
			}
			$page_metas['post_parent'] = $page_parent_slug_var;
		}

		$listicle_metas = array();
		if ( 'listicle_cpt' == $single_result->post_type ) {
			$listicle_metas['cpt_item_name'] = self::listicle_ge_metavalue( 'cpt_item_name', $single_result->ID  );
			$listicle_metas['cpt_item_description'] = self::listicle_ge_metavalue( 'cpt_item_description', $single_result->ID );
			$listicle_metas['cpt_item_order'] = self::listicle_ge_metavalue( 'cpt_item_order', $single_result->ID  );
			$listicle_metas['cpt_item_type'] = self::listicle_ge_metavalue( 'cpt_item_type', $single_result->ID  );
		}

		$am_metas = array();
		$am_metas_photo_array = array();
		if ( 'affiliate_marketing' == $single_result->post_type ) {
			$am_metas['am_item_photo'] = self::am_get_metavalue( 'am_item_photo', $single_result->ID  );

			foreach ( $am_metas['am_item_photo'] as $am_item_photoid ) {
				$am_item_postdata = get_post( $am_item_photoid );
				$am_metas_photo_array[] = isset( $am_item_photoid ) && $am_item_photoid != "" ? $am_item_postdata : "" ;
			}

			foreach ( $am_metas_photo_array as $am_meta_item_val ) {
				if( ! empty ($am_meta_item_val) ) {
					$am_meta_item_val->guid = wp_get_attachment_image_url( $am_meta_item_val->ID, 'full' );
					$am_meta_item_val->alt = get_post_meta( $am_meta_item_val->ID, '_wp_attachment_image_alt', true );
					$am_meta_item_val->attribution = get_post_meta( $am_meta_item_val->ID, 'gmr_image_attribution', true );
				}
			}

			$am_metas['am_item_photo'] = array_filter( array_map( 'get_post', $am_metas['am_item_photo'] ) );
			foreach ( $am_metas['am_item_photo'] as $am_meta_item_photo ) {
				$am_meta_item_photo->guid = wp_get_attachment_image_url( $am_meta_item_photo->ID, 'full' );
				$am_meta_item_photo->alt = get_post_meta( $am_meta_item_photo->ID, '_wp_attachment_image_alt', true );
				$am_meta_item_photo->attribution = get_post_meta( $am_meta_item_photo->ID, 'gmr_image_attribution', true );
			}

			$am_metas['am_item_imagetype'] = self::am_get_metavalue( 'am_item_imagetype', $single_result->ID  );
			$am_metas['am_item_imagecode'] = self::am_get_metavalue( 'am_item_imagecode', $single_result->ID  );
			$am_metas['am_item_order'] = self::am_get_metavalue( 'am_item_order', $single_result->ID  );
			$am_metas['am_item_unique_order'] = self::am_get_metavalue( 'am_item_unique_order', $single_result->ID  );
			$am_metas['am_item_name'] = self::am_get_metavalue( 'am_item_name', $single_result->ID  );
			$am_metas['am_item_description'] = self::am_get_metavalue( 'am_item_description', $single_result->ID );
			$am_metas['am_item_buttontext'] = self::am_get_metavalue( 'am_item_buttontext', $single_result->ID  );
			$am_metas['am_item_buttonurl'] = self::am_get_metavalue( 'am_item_buttonurl', $single_result->ID  );
			$am_metas['am_item_getitnowtext'] = self::am_get_metavalue( 'am_item_getitnowtext', $single_result->ID  );
			$am_metas['am_item_getitnowfromname'] = self::am_get_metavalue( 'am_item_getitnowfromname', $single_result->ID  );
			$am_metas['am_item_getitnowfromurl'] = self::am_get_metavalue( 'am_item_getitnowfromurl', $single_result->ID  );
			$am_metas['am_item_type'] = self::am_get_metavalue( 'am_item_type', $single_result->ID  );
		}

		$show_metas				= array();
		$show_logo_metas		= array();
		$show_featured_metas	= array();
		$show_favorite_metas	= array();
		if ( 'show' == $single_result->post_type ) {
			$image_id = intval( get_post_meta( $single_result->ID, 'logo_image', true ) );
			if( $image_id )
			{
				$show_logo_postdata = get_post( $image_id );
				$show_logo_metas[] = isset( $image_id ) && $image_id != "" ? $show_logo_postdata : "" ;
			}
			foreach ( $show_logo_metas as $show_logo_meta ) {
				if( ! empty ( $show_logo_meta ) ) {
					$show_logo_meta->guid = wp_get_attachment_image_url( $show_logo_meta->ID, 'full' );
					$show_logo_meta->alt = get_post_meta( $show_logo_meta->ID, '_wp_attachment_image_alt', true );
					$show_logo_meta->attribution = get_post_meta( $show_logo_meta->ID, 'gmr_image_attribution', true );
				}
			}
			// Fetch featured meta array
			$gmr_featured_post_ids = self::am_get_metavalue( 'gmr_featured_post_ids', $single_result->ID  );
			if( isset( $gmr_featured_post_ids ) && $gmr_featured_post_ids != "" )
			{
				$gmr_featured_post_ids_array = explode (",", $gmr_featured_post_ids);
				foreach( $gmr_featured_post_ids_array as $gmr_featured_post_id )
				{
					// $show_featured_metas[] = get_post( $gmr_featured_post_id );
					$show_metas['show_featured_metas'][] = get_post( $gmr_featured_post_id );
				}
			}
			// Fetch favorite meta array
			$gmr_favorite_post_ids = self::am_get_metavalue( 'gmr_favorite_post_ids', $single_result->ID );
			if( isset( $gmr_favorite_post_ids ) && $gmr_favorite_post_ids != "" )
			{
				$gmr_favorite_post_ids_array = explode (",", $gmr_favorite_post_ids);
				foreach( $gmr_favorite_post_ids_array as $gmr_favorite_post_id )
				{
					// $show_favorite_metas[] = get_post( $gmr_favorite_post_id );
					$show_metas['show_favorite_metas'][] = get_post( $gmr_favorite_post_id );
				}
			}
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
				'page_metas' 		  => $page_metas,
				'listicle_metas' 	  => $listicle_metas,
				'am_metas'			  => $am_metas,
				'am_item_photo_attachment'			  => $am_metas_photo_array,
				'show_metas'		  => $show_metas,
				'show_logo_metas'	  => $show_logo_metas,
				'featured'            => $featured_id ? array( $featured_id, $featured_src ) : null,
				'galleries'           => $galleries,
				'term_tax'            => $term_tax,
		);
	}

	public static function listicle_ge_metavalue( $value, $postid ) {
		$field = get_post_meta( $postid, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}

	public static function am_get_metavalue( $value, $postid ) {
		$field = get_post_meta( $postid, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}

	/**
	 * Sync post on transition.
	 *
	 * @param array $params Parameters.
	 */
	public static function sync_post_on_transition( $params ) {
		$post_id = $params['post_id'];
	
		if ( class_exists( '\ElasticPress\Indexables' ) ) {
			error_log ( "Indexing Logs: Running index on document $post_id after syndication complete." );
			\ElasticPress\Indexables::factory()->get( 'post' )->index( $post_id );
		}
	}

	/**
	 * Import posts from content site and all related media
	 *
	 * @param \WP_Post $post
	 * @param array  $metas
	 * @param array  $defaults
	 * @param string $featured
	 * @param array  $attachments
	 *
	 * @return int|\WP_Error
	 */
	public static function ImportPosts( $post, $metas, $defaults, $featured, $attachments, $gallery_attachments, $galleries, $page_metas, $listicle_metas,$am_metas, $am_item_photo_attachment, $show_metas, $show_logo_metas, $term_tax, $force_update = false ) {
		if ( ! $post ) {
			return;
		}

		self::log( 'Start importing "%s" (%s) post...', $post->post_title, $post->ID );
		error_log( self::syndication_log_prefix(). " Start importing \"$post->post_title\" ($post->ID) post..." );

		$post_name = sanitize_title( $post->post_name );
		$post_title = sanitize_text_field( $post->post_title );
		$post_type = sanitize_text_field( $post->post_type );
		$post_status = sanitize_text_field( $defaults['status'] );

		// create unique meta value for imported post
		$post_hash = md5( trim( $post->post_title ) . $post->post_modified );

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
				'meta_input'        => array(
						'syndication_import'   => $post_hash,
						'syndication_old_name' => $post_name,
						'syndication_old_data' => serialize( array( 'id' => intval( $post->ID ), 'blog_id' => self::$content_site_id ) ),
				),
		);

		if ( 'publish' == $post_status ) {
			$args['post_modified'] = current_time( 'mysql' );
			$args['post_modified_gmt'] = current_time( 'mysql', 1 );
		}

		if ( 'page' == $post_type ) {
			$post_parent = "";
			if ( isset( $page_metas['post_parent'] ) && $page_metas['post_parent'] != "" )
			{
				// $pageIdBySlug = get_page_by_path( $page_metas['post_parent'], OBJECT, 'page' );
				$pageIdBySlug = get_page_by_path( 'the-content-factory-page', OBJECT, 'page' );
				$post_parent = isset( $pageIdBySlug->ID ) && $pageIdBySlug->ID != "" ? $pageIdBySlug->ID : "" ;
				echo "<pre>", print_r( $pageIdBySlug ), print_r( $page_metas['post_parent'] ), "</pre>"; exit;
			}
			$args['post_parent'] = $post_parent;
			// echo "<pre>", print_r( $pageIdBySlug ), print_r( $args['post_parent'] ), "</pre>"; exit;
			$args['menu_order'] = $page_metas['menu_order'];
		}

		if ( ! empty( $metas ) ) {
			$exclude = array(
					'_edit_lock',
					'_edit_last',
					'_pingme',
					'_encloseme',
					'syndication-detached',
					'embed'
			);

			foreach ( $metas as $meta_key => $meta_value ) {
				if ( ! in_array( $meta_key, $exclude ) ) {
					$args['meta_input'][ $meta_key ] = $meta_value[0];
				}
			}
		}

		$featured_id = false;
		if ( ! is_null( $featured ) ) {
			$featured_id = self::ImportMedia( null, $featured[1], $featured[0] );
			if ( is_wp_error( $featured_id ) ) {
				error_log( self::syndication_log_prefix(). "Error during import feature media for $post_title: \"$featured_id->get_error_message()\" (" . json_encode( $featured ) .")\n" );
			} else {
				error_log( self::syndication_log_prefix(). "Imported feature media for $post_title: \"$featured_id\" (" . json_encode( $featured ) .")\n" );
			}
		}

		// query to check whether post already exist
		$meta_query_args = array(
				'meta_key'    => 'syndication_old_name',
				'meta_value'  => $post_name,
				'post_status' => 'any',
				'post_type'   => $post_type
		);

		$updated = 0;
		$post_id = 0;

		// check whether post with that name exist
		$existing = get_posts( $meta_query_args );
		if ( ! empty( $existing ) ) {
			$existing_post = current( $existing );
			$post_id = intval( $existing_post->ID );

			// update existing post only if it hasn't been updated manually
			$detached = get_post_meta( $post_id, 'syndication-detached', true );
			$detached = apply_filters( 'beasley_syndication_post_is_detached', filter_var( $detached, FILTER_VALIDATE_BOOLEAN ) );
			if ( $detached !== true ) {
				$hash_value = get_post_meta( $post_id, 'syndication_import', true );
				if ( $hash_value != $post_hash || $force_update ) {
					// post has been updated, override existing one
					$args['ID'] = $post_id;
					unset( $args['post_status'] );

					wp_update_post( $args );
					$updated = 2;

					self::log( 'Post %s already exists in the destination site, so it has been updated...', $post_id );
				} else {
					self::log( "Post %s content hasn't been modified since last import... Skipping...", $post_id );
				}
			} else {
				self::log( "Post %s was modified on the child site, and is detached from syndication. Skipping...", $post_id );
			}
		} else {
			$post_id = wp_insert_post( $args );
			$updated = 1;

			self::log( 'New post (%s) has been created in the destination site.', $post_id );
		}

		/**
		 * Post has been updated or created, assign default terms
		 * Import featured and attached images
		 */
		if ( $updated > 0 ) {
			update_post_meta( $post_id, 'syndication_post_id', self::$syndication_id );

			if ( ! is_wp_error( $featured_id ) && is_int( $featured_id ) ) {
				$new_thumbnail_id = set_post_thumbnail( $post_id, $featured_id );
				if(!$new_thumbnail_id) {
					error_log( self::syndication_log_prefix(). "Error during Set Post Thumbnail \"$featured_id\" for post $post_title (\"$post_id\"): (" . json_encode( $featured ) .") \n" );
				} else {
					error_log( self::syndication_log_prefix(). "Success for Set Post Thumbnail \"$featured_id\" for $post_title (\"$post_id\"): (" . json_encode( $featured ) .") \n" );
				}
			}

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

			if ( $updated == 1 || $force_update ) {
				self::AssignDefaultTerms( $post_id, $defaults );
			}

			$uncategorized = get_term_by( 'name', 'Uncategorized', 'category' );
			if ( $uncategorized ) {
				wp_remove_object_terms( $post_id, $uncategorized->term_id, 'category' );
			}

			if ( ! is_null( $attachments ) ) {
				// error_log( self::syndication_log_prefix(). " Attachments - Start multiple attachments import process for Post ID $post_id" ."\n" );
				self::ImportAttachedImages( $post_id, $attachments );
			}

			self::ReplaceGalleryID( $post_id, $galleries );

			if ( 'gmr_gallery' == $post_type ) {
				delete_post_meta( $post_id, 'gallery-image' );
				$imported = self::ImportAttachedImages( $post_id, $gallery_attachments );
				foreach ( $imported as $attachment ) {
					if ( is_numeric( $attachment ) ) {
						add_post_meta( $post_id, 'gallery-image', $attachment );
					}
				}
			}

			if( is_array($metas['embed']) && !empty ( $metas['embed'] ) ){
				delete_post_meta( $post_id, 'embed' );

				$_thumbnail_id = self::am_get_metavalue( '_thumbnail_id', $post_id );
				update_post_meta( $_thumbnail_id, 'embed', $metas['embed'] );
			}

			if ( 'show' == $post_type ) {
				delete_post_meta( $post_id, 'gmr_featured_post_ids' );
				delete_post_meta( $post_id, 'gmr_favorite_post_ids' );
				delete_post_meta( $post_id, 'logo_image' );

				//Fetch Logo ID
				$logo_import = self::ImportAttachedImages( $post_id, $show_logo_metas );

				$show_featured_metas = isset( $show_metas['show_featured_metas'] ) ? $show_metas['show_featured_metas'] : "" ;
				$show_favorite_metas = isset( $show_metas['show_favorite_metas'] ) ? $show_metas['show_favorite_metas'] : "" ;
				// echo "<pre>", print_r($show_featured_metas), print_r($show_favorite_metas), print_r($show_logo_metas), "</pre>"; exit;
				$gmr_featured_post_ids_array = array();
				if( !empty($show_featured_metas) )
				{
					foreach( $show_featured_metas as $show_featured_meta ){
						// fetch new postid as per old postid
						$show_featured_new_post = get_page_by_path( $show_featured_meta->post_name, OBJECT, $show_featured_meta->post_type );
						if( isset( $show_featured_new_post ) && !empty( $show_featured_new_post ) ){
							$gmr_featured_post_ids_array[] = $show_featured_new_post->ID;
						}
						// echo "<pre>", print_r($show_featured_meta->post_name), print_r($new_post_data), "</pre>";
					}
				}
				$gmr_favorite_post_ids_array = array();
				if( !empty($show_favorite_metas) )
				{
					foreach( $show_favorite_metas as $show_favorite_meta ){
						// fetch new postid as per old postid
						$show_favorite_new_post = get_page_by_path( $show_favorite_meta->post_name, OBJECT, $show_favorite_meta->post_type );
						if( isset( $show_favorite_new_post ) && !empty( $show_favorite_new_post ) ){
							$gmr_favorite_post_ids_array[] = $show_favorite_new_post->ID;
						}
						// echo "<pre>", print_r($show_favorite_meta->post_name), print_r($new_post_data), "</pre>";
					}
				}
				$gmr_featured_post_ids = implode(',', $gmr_featured_post_ids_array);
				$gmr_favorite_post_ids = implode(',', $gmr_favorite_post_ids_array);
				// echo "<pre>", print_r($gmr_favorite_post_ids), print_r($gmr_featured_post_ids), "</pre>";

				update_post_meta( $post_id, 'gmr_featured_post_ids', $gmr_featured_post_ids );
				update_post_meta( $post_id, 'gmr_favorite_post_ids', $gmr_favorite_post_ids );
				foreach ( $logo_import as $logo_attachment ) {
					if ( is_numeric( $logo_attachment ) ) {
						update_post_meta( $post_id, 'logo_image', $logo_attachment );
					}
				}
			}

			if ( 'listicle_cpt' == $post_type ) {
				delete_post_meta( $post_id, 'cpt_item_name' );
				delete_post_meta( $post_id, 'cpt_item_order' );
				delete_post_meta( $post_id, 'cpt_item_description' );
				delete_post_meta( $post_id, 'cpt_item_type' );

				update_post_meta( $post_id, 'cpt_item_name', $listicle_metas['cpt_item_name'] );
				update_post_meta( $post_id, 'cpt_item_order', $listicle_metas['cpt_item_order'] );
				update_post_meta( $post_id, 'cpt_item_description', $listicle_metas['cpt_item_description'] );
				update_post_meta( $post_id, 'cpt_item_type', $listicle_metas['cpt_item_type'] );
			}

			if ( 'page' == $post_type ) {
				delete_post_meta( $post_id, '_wp_page_template' );
				update_post_meta( $post_id, '_wp_page_template',$page_metas['_wp_page_template'] );
			}

			if ( 'affiliate_marketing' == $post_type ) {
				delete_post_meta( $post_id, 'am_item_name' );
				delete_post_meta( $post_id, 'am_item_photo' );
				delete_post_meta( $post_id, 'am_item_imagetype' );
				delete_post_meta( $post_id, 'am_item_imagecode' );
				delete_post_meta( $post_id, 'am_item_order' );
				delete_post_meta( $post_id, 'am_item_unique_order' );
				delete_post_meta( $post_id, 'am_item_description' );
				delete_post_meta( $post_id, 'am_item_buttontext' );
				delete_post_meta( $post_id, 'am_item_buttonurl' );
				delete_post_meta( $post_id, 'am_item_getitnowtext' );
				delete_post_meta( $post_id, 'am_item_getitnowfromname' );
				delete_post_meta( $post_id, 'am_item_getitnowfromurl' );
				delete_post_meta( $post_id, 'am_item_type' );

				update_post_meta( $post_id, 'am_item_name', $am_metas['am_item_name'] );
				// $am_item_photo_import = self::ImportAttachedImages( $post_id, $am_metas['am_item_photo'] );
				// $am_item_photo_import = self::ImportAttachedImages( $post_id, $am_item_photo_attachment );
				$am_item_photo_import = array();
				foreach ( $am_item_photo_attachment as $am_item_photo_abject ) {
					if( !empty($am_item_photo_abject) && isset( $am_item_photo_abject->ID ) && $am_item_photo_abject->ID != "" ){
						$filename = esc_url_raw( $am_item_photo_abject->guid );
						$id = self::ImportMedia( $post_id, $filename, $am_item_photo_abject->ID, $am_item_photo_abject );
						$am_item_photo_import[] = is_numeric( $id ) ? $id : "" ;
					} else {
						$am_item_photo_import[] = "";
					}
				}
				update_post_meta( $post_id, 'am_item_photo', $am_item_photo_import );
				update_post_meta( $post_id, 'am_item_imagetype', $am_metas['am_item_imagetype'] );
				update_post_meta( $post_id, 'am_item_imagecode', $am_metas['am_item_imagecode'] );
				update_post_meta( $post_id, 'am_item_order', $am_metas['am_item_order'] );
				update_post_meta( $post_id, 'am_item_unique_order', $am_metas['am_item_unique_order'] );
				update_post_meta( $post_id, 'am_item_description', $am_metas['am_item_description'] );
				update_post_meta( $post_id, 'am_item_buttontext', $am_metas['am_item_buttontext'] );
				update_post_meta( $post_id, 'am_item_buttonurl', $am_metas['am_item_buttonurl'] );
				update_post_meta( $post_id, 'am_item_getitnowtext', $am_metas['am_item_getitnowtext'] );
				update_post_meta( $post_id, 'am_item_getitnowfromname', $am_metas['am_item_getitnowfromname'] );
				update_post_meta( $post_id, 'am_item_getitnowfromurl', $am_metas['am_item_getitnowfromurl'] );
				update_post_meta( $post_id, 'am_item_type', $am_metas['am_item_type'] );
			}
		}

		if ( function_exists( 'wp_async_task_add' ) ) {
			wp_async_task_add( 'ep_sync_post_on_transition', array( 'post_id' => $post_id ), 'high' );
		}
		
		clean_post_cache( $post_id );

		/**
		 * Fires once a post has been saved.
		 *
		 * @param int $post_ID Post ID.
		 * @param WP_Post $post Post object.
		 * @param bool $update Whether this is an existing post being updated or not.
		 */
		do_action( 'greatermedia-post-update', $post_id, $post, $updated );

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
								'meta_key'   => self::get_attachment_old_id_key(),
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
						if ( is_a( $term_obj, '\WP_Term' ) ) {
							wp_set_post_terms( $post_id, $term_obj->name, $taxonomy, true );
						}
					}
				}
			}
		}
	}

	/**
	 * Borrowed from S3 Uploads plugin cli command for migrating attachements
	 *
	 * @param int    $id - Post ID of the image
	 *
	 * @return null
	 */
	public static function MigrateAttachmentToS3( $id ) {

		// If theres no class them return silently
		if ( ! class_exists( 'S3_Uploads' ) ) {
			return;
		}

		// Ensure things are active
		$instance = S3_Uploads::get_instance();
		if ( ! s3_uploads_enabled() ) {
			$instance->setup();
		}

		$old_upload_dir = $instance->get_original_upload_dir();
		$upload_dir = wp_upload_dir();

		$files = array( get_post_meta( $id, '_wp_attached_file', true ) );

		$meta_data = wp_get_attachment_metadata( $id );

		if ( ! empty( $meta_data['sizes'] ) ) {
			foreach ( $meta_data['sizes'] as $file ) {
				$files[] = path_join( dirname( $meta_data['file'] ), $file['file'] );
			}
		}

		foreach ( $files as $file ) {
			if ( file_exists( $path = $old_upload_dir['basedir'] . '/' . $file ) ) {
				if ( ! copy( $path, $upload_dir['basedir'] . '/' . $file ) ) {
					error_log( "Unable to path:" . print_r( $path, true ), 3, '/var/www/html/wordpress/debug.log' );
					error_log( "Unable to upload_dir:" . print_r( $upload_dir, true ), 3, '/var/www/html/wordpress/debug.log' );
					error_log( "Unable to file:" . print_r( $file, true ), 3, '/var/www/html/wordpress/debug.log' );
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
	 * @param int    $original_id - Original ID of attachment
	 *
	 * @return int|object
	 */
	public static function ImportMedia( $post_id, $filename, $original_id = 0, $original_post = null ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		// add_filter('https_ssl_verify', '__return_false');

		$tmp = download_url( $filename );

		if ( is_wp_error( $tmp ) ) {
			error_log( self::syndication_log_prefix(). "ImportMedia( $post_id / $original_id ), Failed to Download $filename : " . $tmp->get_error_message() ."\n" );
			return $tmp;
		}

		$id = 0;
		$original_id = intval( $original_id );
		if ( empty( $original_id ) ) {
			$meta_query_args = array(
					'meta_key'   => self::get_attachment_old_url_key(),
					'meta_value' => $filename,
					'post_type'  => 'attachment',
			);
		} else {
			$meta_query_args = array(
					'meta_key'   => self::get_attachment_old_id_key(),
					'meta_value' => $original_id,
					'post_type'  => 'attachment',
			);
		}

		// query to check whether post already exist
		$existing = get_posts( $meta_query_args );
		if ( empty( $existing ) ) {
			preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $filename, $matches );

			// make sure we have a match.  This won't be set for PDFs and .docs
			if ( $matches && isset( $matches[0] ) ) {
				$file_array = array();
				$file_array['name'] = basename( $matches[0] );
				$file_array['tmp_name'] = $tmp;
				$file_array['size'] = filesize( $tmp );

				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					// NOTE: Appears to be an invalid code path - if $tmp is a WP_Error,
					// we already return early.
					@unlink( $file_array['tmp_name'] );
					$file_array['tmp_name'] = '';
				}

				$post_data = array();
				if ( is_a( $original_post, '\WP_Post' ) ) {
					$post_data['post_title'] = $original_post->post_title;
					$post_data['post_content'] = $original_post->post_content;
					$post_data['post_excerpt'] = $original_post->post_excerpt;
				}

				// do the validation and storage stuff
				$id = media_handle_sideload( $file_array, $post_id, null, $post_data );
				// error_log( self::syndication_log_prefix(). "Start Import single image..." ."\n" );

				// If error storing permanently, unlink
				if ( is_wp_error( $id ) ) {
					error_log( self::syndication_log_prefix(). "ImportMedia( $post_id / $original_id ), Media Sideload Failed $filename : " . $id->get_error_message() ."\n" );
					@unlink( $file_array['tmp_name'] );
				} else {
					// error_log( self::syndication_log_prefix(). "Single image ID is $id " ."\n" );
					// Try to migrate the post attachment to S3 if it failed for whatever reason
					self::MigrateAttachmentToS3( $id );
					@unlink( $file_array['tmp_name'] );
				}
			} else {
				@unlink( $tmp );
			}

			if ( ! is_wp_error( $id ) ) {
				update_post_meta( $id, self::get_attachment_old_id_key(), $original_id );
				update_post_meta( $id, self::get_attachment_old_url_key(), esc_url_raw( $filename ) );

				self::updateImageCaption( $id, $original_id );
			}
		} else {
			$id = $existing[0]->ID;

			// Try to migrate the post attachment to S3 if it failed for whatever reason
			self::MigrateAttachmentToS3( $id );
			self::updateImageCaption( $id, $original_id );
		}

		if ( ! empty( $original_id ) ) {
			switch_to_blog( self::$content_site_id );
			$attribution = get_post_meta( $original_id, 'gmr_image_attribution', true );
			$alttext = get_post_meta( $original_id, '_wp_attachment_image_alt', true );
			restore_current_blog();

			if ( ! empty( $attribution ) ) {
				update_post_meta( $id, 'gmr_image_attribution', $attribution );
			}

			if ( ! empty( $alttext ) ) {
				update_post_meta( $id, '_wp_attachment_image_alt', $alttext );
			}
		}

		self::log( "Media file (%s) has been imported...", $filename );

		/**
		 * Removes temporary download file if it exists. This was causing
		 * disk space to fill up on prod.
		 *
		 * https://tenup.teamwork.com/#/tasks/18632495
		 */
		if ( file_exists( $tmp ) && is_file( $tmp ) ) {
			@unlink( $tmp );
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
		// error_log( self::syndication_log_prefix(). "Start multiple image import process for Post ID $post_id" ."\n" );
		foreach ( $attachments as $attachment ) {
			$filename = esc_url_raw( $attachment->guid );
			$id = self::ImportMedia( $post_id, $filename, $attachment->ID, $attachment );
			if ( is_numeric( $id ) ) {
				$imported[] = $id;
			}
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
							'meta_key'   => self::get_attachment_old_url_key(),
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
						$new_id = self::ImportMedia( $post_id, $image_src, $old_ids[ $index ] );
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
			$old_alt  = get_post_meta( $old_id, '_wp_attachment_image_alt', true );
			$old_attr = get_post_meta( $old_id, 'gmr_image_attribution', true );

			restore_current_blog();

			if ( ! empty( $old_post ) ) {
				$new_post = get_post( $new_id, ARRAY_A );
				if ( ! empty( $new_post ) ) {
					$new_post['post_excerpt'] = $old_post['post_excerpt'];
					$new_post['post_title']   = $old_post['post_title'];
					$new_post['post_content'] = $old_post['post_content'];

					wp_update_post( $new_post );

					if ( ! empty( $old_alt ) ) {
						update_post_meta( $new_id, '_wp_attachment_image_alt', $old_alt );
					}

					if ( ! empty( $old_attr ) ) {
						update_post_meta( $new_id, 'gmr_image_attribution', $old_attr );
					}
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
	 * Get all terms of all taxonomies from the content site
	 *
	 * @return array
	 */
	public static function verifySourceSubscription( $source_site_id, $subscription_filter_terms_data, $enabled_filter_taxonomy, $current_blog_id ) {
		global $switched;
		global $wpdb;

		$existingSubs = array();
		switch_to_blog( self::$content_site_id );

		$args = array();
		if( isset( $subscription_filter_terms_data ) ) {
			$args = array(
					'meta_query' => array(
							array(
									'key' => 'subscription_filter_terms-'.$enabled_filter_taxonomy,
									'value' => $subscription_filter_terms_data,
							),
							array(
									'key' => 'subscription_source',	/*Rupesh Test this source side id data*/
									'value' => $current_blog_id,		/*Here need to applied current blog id and its WMMR*/
							)
					)
			);
		}

		$defaults = array(
				'post_type'      => 'subscription',
				'post_status'    => 'any',
		);

		$args = wp_parse_args( $args, $defaults );

		$existingSubs = get_posts( $args );
		// echo $wpdb->last_query; exit;
		// echo "<pre>", print_r($existingSubs). '</pre>'; exit;

		restore_current_blog();

		return $existingSubs;
	}

	/**
	 * Get all active subscription ordered by default post status
	 * Defualt post status "Draft" has higher priority as it's less public
	 *
	 * @return array of post objects
	 */
	public static function GetActiveSubscriptions( $args = array() ) {
		$defaults = array(
				'post_type'      => 'subscription',
				'post_status'    => 'publish',
				'meta_key'       => 'subscription_post_status',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'posts_per_page' => 200,
		);

		$args = wp_parse_args( $args, $defaults );

		return get_posts( $args );
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

	/**
	 * Adds message to the log file.
	 *
	 * @param string $message
	 */
	public static function log() {
		$syndication_id = self::$syndication_id;
		$uniqid = self::$syndication_uniqid;
		$site_id = get_current_blog_id();
		$message = func_num_args() > 1
				? vsprintf( func_get_arg( 0 ), array_slice( func_get_args(), 1 ) )
				: func_get_arg( 0 );

		$message = "[SYNDICATION:{$syndication_id} SITE:{$site_id} {$uniqid}] {$message}";
		self::$log[] = $message;
		syslog( LOG_ERR, $message );
		// error_log( $message );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( $message );
		}
	}

	public static function syndication_log_prefix() {
		$syndication_id = filter_input( INPUT_POST, 'syndication_id', FILTER_VALIDATE_INT );
		$uniqid		=	self::$syndication_uniqid;
		$site_id	=	get_current_blog_id();
		$site_name	=	get_blog_option( $site_id, 'blogname' );

		$result = "[SyndicationID:{$syndication_id} SiteID:{$site_id} SiteName:$site_name UniqID:{$uniqid}]";

		return $result;
	}

	public static function log_variable( $var, $context = '' ) {
		self::log( "$context \n" . print_r( $var, true ) );
	}

	/**
	 * Records log into error log file and clears log array.
	 */
	public static function flush_log() {
		$delimiter = PHP_EOL . '    ';
		if ( is_array( self::$log ) && count( self::$log ) > 0 ) {
			error_log( $delimiter . implode( $delimiter, self::$log ) );
		}

		self::$log = array();
	}

}

BlogData::init();

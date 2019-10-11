<?php
/*
Plugin Name: Feed Pull MRSS
Description: Extends Feed Pull plugin by adding ability to extract media information from RSS feeds.
Version: 2.0.0
Author: 10up
Author URI: http://10up.com/
*/

/**
 * Catches SimpleXML element to use in the next steps.
 *
 * @global SimpleXMLElement $fpmrss_feed_item Current SimpleXML element.
 *
 * @param array $pre_filter_post_value The array of post data.
 * @param SimpleXMLElement $xml_element The current xml element.
 *
 * @return array The inital post data.
 */
function fpmrss_catch_feed_item_xml( $pre_filter_post_value, $xml_element ) {
	global $fpmrss_feed_item;
	$fpmrss_feed_item = $xml_element;

	return $pre_filter_post_value;
}

add_filter( 'fp_post_args', 'fpmrss_catch_feed_item_xml', 10, 2 );

/**
 * Extracts media group element.
 *
 * @param SimpleXMLElement $element Current RSS element.
 *
 * @return SimpleXMLElement Media group element on success, otherwise FALSE.
 */
function fpmrss_get_media_group( SimpleXMLElement $element ) {
	$group = $element->xpath( 'media:group' );
	if ( ! empty( $group ) ) {
		$group = current( $group );
	} else {
		$group = $element->xpath( 'media:content' );
		if ( ! empty( $group ) ) {
			$group = current( $group );
		}
	}

	return $group;
}

/**
 * Extracts media thumbnail from an RSS element.
 *
 * @param SimpleXMLElement $element Current RSS element.
 * @param string           $media_field which media field to use.
 *
 * @return string Thumbnail URL on success, otherwise FALSE.
 */
function fpmrss_extract_media_thumbnail( SimpleXMLElement $element, $media_field = 'thumbnail' ) {
	$group = fpmrss_get_media_group( $element );
	if ( ! is_a( $group, 'SimpleXMLElement' ) ) {
		$group = $element;
	}

	$thumbnail_url = false;
	$thumbnails    = $group->xpath( 'media:' . $media_field );
	if ( empty( $thumbnails ) && $element != $group ) {
		$thumbnails = $element->xpath( 'media:' . $media_field );
	}

	if ( empty( $thumbnails ) ) {
		//Fetch itunes image from feed if present
		$thumbnails = $element->xpath( 'itunes:image' );
	}

	if ( empty( $thumbnails ) ) {
		$thumbnails = $element->xpath( $media_field );
	}

	if ( ! empty( $thumbnails ) ) {
		$max_width = 0;
		foreach ( $thumbnails as $thumbnail ) {
			$key = 'url';
			if ( empty( $thumbnail[ $key ] ) || ! filter_var( $thumbnail[ $key ], FILTER_VALIDATE_URL ) ) {
				$key = 'href';
				if ( empty( $thumbnail[ $key ] ) || ! filter_var( $thumbnail[ $key ], FILTER_VALIDATE_URL ) ) {
					continue;
				}
			}

			$current_width = intval( $thumbnail['width'] );
			if ( ! empty( $current_width ) && $max_width >= $current_width ) {
				continue;
			}

			$thumbnail_url = strval( $thumbnail[ $key ] );
			$max_width     = $current_width;
		}
	}

	return $thumbnail_url;
}

/**
 * Extracts media player from an RSS element.
 *
 * @param SimpleXMLElement $element Current RSS elemenet.
 *
 * @return string Player value on success, otherwise FALSE.
 */
function fpmrss_extract_media_player( SimpleXMLElement $element ) {
	$group = fpmrss_get_media_group( $element );
	if ( ! is_a( $group, 'SimpleXMLElement' ) ) {
		$group = $element;
	}

	$player_value = false;
	$player       = $group->xpath( 'media:player' );
	if ( empty( $player ) && $group != $element ) {
		$player = $element->xpath( 'media:player' );
	}

	if ( ! empty( $player ) ) {
		$player       = current( $player );
		$player_value = trim( strval( $player ) );
		if ( empty( $player_value ) && ! empty( $player['url'] ) && filter_var( $player['url'], FILTER_VALIDATE_URL ) ) {
			$player_value = strval( $player['url'] );
		}
	}

	if ( empty( $player_value ) ) {
		$contents = $group->xpath( 'media:content' );
		if ( empty( $contents ) && $group != $element ) {
			$contents = $element->xpath( 'media:content' );
		}

		if ( ! empty( $contents ) ) {
			$max_width = 0;
			foreach ( $contents as $content ) {
				if ( empty( $content['url'] ) || ! filter_var( $content['url'], FILTER_VALIDATE_URL ) ) {
					continue;
				}

				$current_width = intval( $content['width'] );
				if ( ! empty( $current_width ) && $max_width >= $current_width ) {
					continue;
				}

				$player_value = strval( $content['url'] );
				$max_width    = $current_width;
			}
		}
	}

	if ( ! empty( $player_value ) && filter_var( $player_value, FILTER_VALIDATE_URL ) ) {
		$domain = parse_url( $player_value, PHP_URL_HOST );
		if ( preg_match( '#youtu\.?be#i', $domain ) || preg_match( '#vimeo\.com#i', $domain ) ) {
			$oembed = wp_oembed_get( $player_value );
			if ( $oembed ) {
				$player_value = $oembed;
			}
		}
	}

	return $player_value;
}

/**
 * Fetches thumbnail image for a media item.
 *
 * @global SimpleXMLElement $fpmrss_feed_item The current SimpleXML element.
 *
 * @param int $post_id Newly imported post id.
 * @param int $feed_id The feed id.
 */
function fpmrss_fetch_media_data( $post_id, $feed_id ) {
	global $fpmrss_feed_item;

	if ( is_wp_error( $post_id ) ) {
		return;
	}

	// do nothing if an xml element is not caught
	if ( ! $fpmrss_feed_item ) {
		return;
	}



	// set show
	$show = get_post_meta( $feed_id, 'fpmrss-show', true );
	if ( $show && function_exists( 'TDS\get_related_term' ) ) {
		$term = TDS\get_related_term( $show );
		if ( $term ) {
			wp_set_object_terms( $post_id, $term->term_id, ShowsCPT::SHOW_TAXONOMY );
		}
	}


	//set podcast
	$podcast = get_post_meta( $feed_id, 'fpmrss-podcast', true );
	if ( $podcast && 'episode' === get_post_type( $post_id ) ) {
		wp_update_post( array( 'ID' => $post_id, 'post_parent' => $podcast ) );
	}

	$media_field = get_post_meta( $feed_id, 'fpmrss-featured-image', true );

	// fetch thumbnail
	$thumbnail = fpmrss_extract_media_thumbnail( $fpmrss_feed_item, $media_field );
	if ( $thumbnail ) {
		fpmrss_import_thumbnails( array( array( $thumbnail, $post_id ) ) );
	}

	// fetch player
	$player = fpmrss_extract_media_player( $fpmrss_feed_item );
	if ( $player ) {
		// check if redirect header exists for a video
		if ( filter_var( $player, FILTER_VALIDATE_URL ) ) {
			$response = wp_remote_head( $player );
			if ( ! is_wp_error( $response ) ) {
				$location = wp_remote_retrieve_header( $response, 'location' );
				if ( ! empty( $location ) ) {
					$player = $location;
				}
			}
		}

		// we need to convert a link or an embed code into the player shortcode
		$matches = array();
		if ( filter_var( $player, FILTER_VALIDATE_URL ) && preg_match( '#^https?\:\/\/.*?\.ooyala\.com\/(.+?)\/(.+?)\/?$#i', $player, $matches ) ) {
			$player = "[ooyala code=\"{$matches[1]}\"]";
		} elseif ( preg_match( '#embedCode\=(.+?)[\&\"\']#is', $player, $matches ) ) {
			$player = "[ooyala code=\"{$matches[1]}\"]";
		}

		// set player meta
		update_post_meta( $post_id, 'gmr-player', $player );
		set_post_format( $post_id, 'video' );
	}


	$player = get_post_meta( $post_id, 'gmr-podcast-audio', true );
	if ( ! empty( $player ) ) {
		if ( filter_var( $player, FILTER_VALIDATE_URL ) ) {
			$post = get_post( $post_id );
			//Remove query args from audio player url
			//to make shortcode working !!
			$player_arr = parse_url( $player );
			$query      = $player_arr['query'];
			$player     = str_replace( array( $query, '?' ), '', $player );

			$player = '[audio src="' . $player . '"][/audio]';
			$content = $post->post_content . PHP_EOL . '<!--more-->' . PHP_EOL . $player;
			wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );
			delete_post_meta( $post_id, 'gmr-player' ); //remove unwated player code
		}
	}

	$fpmrss_feed_item = null;
}

add_action( 'fp_handled_post', 'fpmrss_fetch_media_data', 10, 2 );


/**
 * Performs thumbnails import.
 *
 * @param array $thumbnails Array of arrays of thumbnail urls and post ids.
 */
function fpmrss_import_thumbnails( $thumbnails ) {
	include_once ABSPATH . 'wp-admin/includes/image.php';
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';

	foreach ( $thumbnails as $thumbnail ) {
		$thumbnail_id = fpmrss_download_image( $thumbnail[0], $thumbnail[1] );
		if ( $thumbnail_id && ! is_wp_error( $thumbnail_id ) ) {
			set_post_thumbnail( $thumbnail[1], $thumbnail_id );
		}
	}
}

add_action( 'fpmrss_import_thumbnails', 'fpmrss_import_thumbnails' );

/**
 * Downloads remote image or just returns attachment ID if it has been already imported.
 *
 * @param string $image The remote image URL.
 * @param int $post_id The post id to which the image would be assigned.
 *
 * @return int The attachment ID on success, otherwise FALSE.
 */
function fpmrss_download_image( $image, $post_id ) {
	$query = new WP_Query( array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'meta_query'  => array(
			array(
				'key'     => 'fp_orig_image',
				'value'   => $image,
				'compare' => '='
			),
		),
	) );

	if ( $query->have_posts() ) {
		return $query->next_post()->ID;
	}

	// Download file to temp location
	$tmp = download_url( $image );
	if ( is_wp_error( $tmp ) ) {
		return false;
	}

	// Set variables for storage, fix file filename for query strings
	$matches = array();
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image, $matches );
	$file_array['name']     = fpmrss_generate_image_name( $image );
	$file_array['tmp_name'] = $tmp;
	$file_array['error']    = 0;

	$post_data = array();
	$post      = get_post( $post_id );
	if ( $post ) {
		$post_data['post_author'] = $post->post_author;
	}

	$image_id = media_handle_sideload( $file_array, $post_id, null, $post_data );
	if ( is_wp_error( $image_id ) ) {
		@unlink( $file_array['tmp_name'] );
	} else {
		add_post_meta( $image_id, 'fp_orig_image', $image );
	}

	return $image_id;
}

/**
 * Generates image name based on its file path.
 *
 * @param string $image The image path.
 *
 * @return string The image name.
 */
function fpmrss_generate_image_name( $image ) {
	$file_name = '';
	$matches   = array();

	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image, $matches );
	if ( is_array( $matches ) && count( $matches ) ) {
		$file_name = str_replace( '%20', '-', basename( $matches[0] ) );
	} else {
		$sizes = getimagesize( $image );
		if ( is_array( $sizes ) && isset( $sizes['mime'] ) && ! empty( $sizes['mime'] ) ) {
			$ext       = image_type_to_extension( $sizes[2] );
			$file_name = substr( sanitize_title( pathinfo( $image, PATHINFO_FILENAME ) ), 0, 254 ) . $ext;
		}
	}

	return $file_name;
}

/**
 * Appends player content if it is available for the current post.
 *
 * @param string $content The initial content of the post.
 *
 * @return string Updated post content if player code available, otherwise initial value.
 */
function fpmrss_update_content( $content ) {
	$player = get_post_meta( get_the_ID(), 'gmr-player', true );
	if ( ! empty( $player ) ) {
		if ( filter_var( $player, FILTER_VALIDATE_URL ) ) {
			$player = "[embed]{$player}[/embed]";
		}
		$content = $player . PHP_EOL . PHP_EOL . $content;
	}

	return $content;
}

add_action( 'the_content', 'fpmrss_update_content', 1 );

/**
 * Filters ooyala player arguments to set proper player id.
 *
 * @param array $args Initial ooyala player arguments.
 *
 * @return array Filtered ooyala player arguments.
 */
function fpmrss_filter_ooyala_args( $args ) {
	$video_id = get_the_ID();

	$player_id = fpmrss_extract_ooyala_post_meta( $video_id, 'fpmrss-ooyala-player-id' );
	if ( $player_id ) {
		$args['player_id'] = $player_id;
	}

	$ad_set = fpmrss_extract_ooyala_post_meta( $video_id, 'fpmrss-ooyala-ad-set' );
	if ( $ad_set ) {
		$args['ad_set'] = $ad_set;
	}

	return $args;
}

add_filter( 'ooyala_default_query_args', 'fpmrss_filter_ooyala_args' );

/**
 * Fetches post meta if available, if not, then tries to extract it from its feed.
 *
 * @param int $post_id The ooyala video post id.
 * @param string $meta_key The meta key to fetch.
 *
 * @return mixed The meta value.
 */
function fpmrss_extract_ooyala_post_meta( $post_id, $meta_key ) {
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if ( empty( $meta_value ) ) {
		$parent_id = get_post_meta( $post_id, 'fp_source_feed_id', true );
		if ( $parent_id ) {
			$meta_value = get_post_meta( $parent_id, $meta_key, true );
		}
	}

	return $meta_value;
}

/**
 * Registers Ooyala settings metabox.
 *
 * @global string $typenow The current post type.
 */
function fpmrss_add_meta_box() {
	global $typenow;

	$post_id = get_the_ID();
	$feed_id = 'fp_feed' == $typenow
		? $post_id
		: get_post_meta( $post_id, 'fp_source_feed_id', true );

	if ( ! empty( $feed_id ) ) {
		$feed_url = get_post_meta( $feed_id, 'fp_feed_url', true );
		if ( filter_var( $feed_url, FILTER_VALIDATE_URL ) && preg_match( '#ooyala\.com$#i', parse_url( $feed_url, PHP_URL_HOST ) ) ) {
			add_meta_box( 'fpmrss-ooyala', 'Ooyala Settings', 'fpmrss_render_ooyala_metabox', $typenow, 'side', 'core' );
		}

		if ( 'fp_feed' == $typenow ) {
			add_meta_box( 'fpmrss-fetch-content', 'Fetch content', 'fpmrss_render_fetch_metabox', $typenow, 'side', 'core' );
			add_meta_box( 'fpmrss-shows', 'Show', 'fpmrss_render_shows_metabox', $typenow, 'side', 'core' );
		}

		add_action( 'edit_form_top', 'fpmrss_render_nonce_field' );
	}
}

add_action( 'add_meta_boxes', 'fpmrss_add_meta_box' );

/**
 * Renders nonce field.
 */
function fpmrss_render_nonce_field() {
	wp_nonce_field( 'fpmrss', 'fpmrss_nonce', false );
}

/**
 * Renders Ooyala settings meta box.
 *
 * @param WP_Post $post The post object.
 */
function fpmrss_render_ooyala_metabox( $post ) {
	$transient_ttl = 5 * MINUTE_IN_SECONDS;

	$ooyala     = get_option( 'ooyala' );
	$ooyala_api = null;
	if ( class_exists( 'OoyalaApi' ) && ! empty( $ooyala['api_key'] ) && ! empty( $ooyala['api_secret'] ) ) {
		$ooyala_api = new OoyalaApi( $ooyala['api_key'], $ooyala['api_secret'] );
	}

	$selected_player = get_post_meta( $post->ID, 'fpmrss-ooyala-player-id', true );
	$players         = get_transient( 'gmr_ooyala_players' );
	if ( $players === false ) {
		$players = array();
		if ( $ooyala_api ) {
			$response = $ooyala_api->get( 'players' );
			if ( ! empty( $response->items ) ) {
				foreach ( $response->items as $ad_set ) {
					$players[ $ad_set->id ] = $ad_set->name;
				}
			}
		}

		set_transient( 'gmr_ooyala_players', $players, $transient_ttl );
	}

	echo '<p>';
	echo '<label for="fpmrss-ooyala-player-id">Player ID:</label>';
	echo '<select id="fpmrss-ooyala-player-id" name="fpmrss-ooyala-player-id" class="widefat">';
	echo '<option value="">';
	echo 'fp_feed' == $post->post_type
		? '--- player by default ---'
		: '--- player defined in the feed ---';
	echo '</option>';
	foreach ( $players as $id => $name ) :
		echo '<option value="', esc_attr( $id ), '"', selected( $id, $selected_player, false ), '>';
		echo esc_html( $name );
		echo '</option>';
	endforeach;
	echo '</select>';
	echo '</p>';

	$selected_ad_set = get_post_meta( $post->ID, 'fpmrss-ooyala-ad-set', true );
	$ad_sets         = get_transient( 'gmr_ooyala_ad_sets' );
	if ( $ad_sets === false ) {
		$ad_sets = array();
		if ( $ooyala_api ) {
			$response = $ooyala_api->get( 'ad_sets' );
			if ( ! empty( $response->items ) ) {
				foreach ( $response->items as $ad_set ) {
					$ad_sets[ $ad_set->id ] = $ad_set->name;
				}
			}
		}

		set_transient( 'gmr_ooyala_ad_sets', $ad_sets, $transient_ttl );
	}

	echo '<p>';
	echo '<label for="fpmrss-ooyala-ad-set">Ad Set:</label>';
	echo '<select id="fpmrss-ooyala-ad-set" name="fpmrss-ooyala-ad-set" class="widefat">';
	echo '<option value="">';
	echo 'fp_feed' == $post->post_type
		? '--- no add set ---'
		: '--- ad set defined in the feed ---';
	echo '</option>';
	foreach ( $ad_sets as $id => $name ) :
		echo '<option value="', esc_attr( $id ), '"', selected( $id, $selected_ad_set, false ), '>';
		echo esc_html( $name );
		echo '</option>';
	endforeach;
	echo '</select>';
	echo '</p>';
}

/**
 * Renders "Fetch content" metabox.
 *
 * @param \WP_Post $post
 */
function fpmrss_render_fetch_metabox( $post ) {
	$url_field   = get_post_meta( $post->ID, 'fpmrss-content-url', true );
	$xpath_field = get_post_meta( $post->ID, 'fpmrss-content-xpath', true );
	$media_field = get_post_meta( $post->ID, 'fpmrss-featured-image', true );

	echo '<p>';
	echo '<label>';
	echo '<b>Source Field (XPath)</b><br>';
	echo '<input type="text" class="widefat" name="fpmrss-content-url" value="', esc_attr( $url_field ), '"><br>';
	echo '<span class="description">Enter field name which contains a link to original article.</span>';
	echo '</label>';
	echo '</p>';
	echo '<p>';
	echo '<label>';
	echo '<b>Content XPath</b><br>';
	echo '<input type="text" class="widefat" name="fpmrss-content-xpath" value="', esc_attr( $xpath_field ), '"><br>';
	echo '<span class="description">Enter xpath to extract content from remote page.</span>';
	echo '</label>';
	echo '</p>';
	echo '<b>Featured Image</b><br>';
	echo '<input type="text" class="widefat" name="fpmrss-featured-image" value="', esc_attr( $media_field ), '"><br>';
	echo '<span class="description">Which image use as featured. Defaults to thumbnail (media:thumbnail).</span>';
	echo '</label>';
	echo '</p>';
}

/**
 * Renders Shows metabox.
 *
 * @param WP_Post $post The post object.
 */
function fpmrss_render_shows_metabox( $post ) {
	$selected_show = get_post_meta( $post->ID, 'fpmrss-show', true );
	$shows         = new WP_Query( array(
		'post_type'           => ShowsCPT::SHOW_CPT,
		'post_status'         => 'publish',
		'posts_per_page'      => 500,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'orderby'             => 'title',
		'order'               => 'ASC',
	) );

	echo '<p>';
	echo '<select id="fpmrss-show" name="fpmrss-show" class="widefat">';
	echo '<option value="">---</option>';
	while ( $shows->have_posts() ) :
		$show = $shows->next_post();
		echo '<option value="', esc_attr( $show->ID ), '"', selected( $show->ID, $selected_show, false ), '>';
		echo esc_html( $show->post_title );
		echo '</option>';
	endwhile;
	echo '</select>';
	echo '</p>';
}

/**
 * Saves post settings.
 *
 * @param int $post_id The post id.
 */
function fpmrss_save_settings( $post_id ) {
	$doing_autosave   = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	$has_capabilities = current_user_can( 'edit_post', $post_id );
	$is_revision      = 'revision' == get_post_type( $post_id );

	if ( $doing_autosave || ! $has_capabilities || $is_revision ) {
		return;
	}

	$nonce = filter_input( INPUT_POST, 'fpmrss_nonce' );
	if ( $nonce && wp_verify_nonce( $nonce, 'fpmrss' ) ) {
		$fields = array(
			'fpmrss-ooyala-player-id',
			'fpmrss-ooyala-ad-set',
			'fpmrss-show',
			'fpmrss-podcast',
			'fpmrss-content-url',
			'fpmrss-content-xpath',
			'fpmrss-featured-image'
		);
		foreach ( $fields as $field ) {
			$value = wp_kses_post( filter_input( INPUT_POST, $field ) );
			$value = trim( $value );

			if ( ! empty( $value ) ) {
				update_post_meta( $post_id, $field, $value );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}
	}
}

add_action( 'save_post', 'fpmrss_save_settings' );

/**
 * Fetches post content.
 *
 * @param array $postargs
 * @param \SimpleXMLElement $post
 * @param int $feed_id
 *
 * @return array
 */
function fpmrss_fetch_post_content( $postargs, $post, $feed_id ) {
	$url_field      = get_post_meta( $feed_id, 'fpmrss-content-url', true );
	$xpath_selector = get_post_meta( $feed_id, 'fpmrss-content-xpath', true );
	if ( ! empty( $url_field ) && ! empty( $xpath_selector ) ) {
		$elements = $post->xpath( $url_field );
		if ( ! empty( $elements ) ) {
			$url = (string) current( $elements );
			if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$response = wp_remote_get( $url, array() );
				if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
					$use_errors = libxml_use_internal_errors( true );

					$doc = new \DOMDocument();
					$doc->loadHTML( wp_remote_retrieve_body( $response ) );

					$xpath    = new \DOMXPath( $doc );
					$elements = $xpath->evaluate( $xpath_selector );
					if ( ! empty( $elements ) ) {
						$content = array();
						foreach ( $elements as $element ) {
							$content[] = trim( $element->textContent );
						}

						$postargs['post_content'] = implode( PHP_EOL . PHP_EOL, $content );
					}

					libxml_use_internal_errors( $use_errors );
				}
			}
		}
	}

	$fields = array( 'post_content', 'post_excerpt', 'post_title' );
	foreach ( $fields as $field ) {
		if ( ! empty( $postargs[ $field ] ) ) {
			$postargs[ $field ] = htmlspecialchars_decode( $postargs[ $field ] );
		}
	}

	return $postargs;
}

add_filter( 'fp_post_args', 'fpmrss_fetch_post_content', 10, 3 );
/**
 * Register meta box(es).
 */
function fpmrss_audio_register_metabox() {
	add_meta_box( 'fp_content_details_episodes', __( 'Podcast', 'feed-pull' ), 'fpmrss_audio_podcast_metabox', 'fp_feed', 'side', 'low' );
}

add_action( 'add_meta_boxes', 'fpmrss_audio_register_metabox', 99 );

/**
 * Renders Shows metabox.
 * checkout fpmrss_save_settings for save
 *
 * @param WP_Post $post The post object.
 */
function fpmrss_audio_podcast_metabox( $post ) {
	$selected_podcast = get_post_meta( $post->ID, 'fpmrss-podcast', true );
	$podcasts         = new WP_Query( array(
		'post_type'           => 'podcast',
		'post_status'         => 'publish',
		'posts_per_page'      => 1000,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'orderby'             => 'title',
		'order'               => 'ASC',
	) );
	?>
	<p>
		<select id="fpmrss-podcast" name="fpmrss-podcast" class="widefat">';
			<option value="">---</option>
			<?php while ( $podcasts->have_posts() ) :
				$podcast = $podcasts->next_post(); ?>
				<option value="<?php echo esc_attr( $podcast->ID ); ?>" <?php selected( $podcast->ID, $selected_podcast ); ?>>
					<?php echo esc_html( $podcast->post_title ); ?>
				</option>
			<?php endwhile; ?>
		</select>
	</p>
	<p class="description">Use <code>gmr-podcast-audio</code> meta key to store podcast episode mp3 file.</p>
	<?php
}

/**
 * Move feed pull to jobs server.
 */
function fpmrss_feed_pull() {
	if ( function_exists( 'wp_async_task_add' ) ) {
		wp_async_task_add( 'fp_async_feed_pull', array() );

		if ( class_exists( '\FP_Cron' ) ) {
			$cron = \FP_Cron::factory();
			remove_action( 'fp_feed_pull', array( $cron, 'pull' ) );
		}
	}
}

add_action( 'fp_feed_pull', 'fpmrss_feed_pull', 1 );

/**
 * Processes feed pull on the jobs server.
 */
function fp_async_feed_pull() {
	remove_action( 'fp_feed_pull', 'fpmrss_feed_pull', 1 );
	do_action( 'fp_feed_pull' );
}

add_action( 'fp_async_feed_pull', 'fp_async_feed_pull' );

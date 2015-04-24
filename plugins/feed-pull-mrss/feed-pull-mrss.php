<?php
/*
Plugin Name: Feed Pull MRSS
Description: Extends Feed Pull plugin by adding ability to extract media information from RSS feeds.
Version: 1.0.0
Author: 10up
Author URI: http://10up.com/
*/

/**
 * Catches SimpleXML element to use in the next steps.
 *
 * @global SimpleXMLElement $fpmrss_feed_item Current SimpleXML element.
 * @param array $pre_filter_post_value The array of post data.
 * @param SimpleXMLElement $xml_element The current xml element.
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
 * @return string Thumbnail URL on success, otherwise FALSE.
 */
function fpmrss_extract_media_thumbnail( SimpleXMLElement $element ) {
	$group = fpmrss_get_media_group( $element );
	if ( ! is_a( $group, 'SimpleXMLElement' ) ) {
		$group = $element;
	}
	
	$thumbnail_url = false;
	$thumbnails = $group->xpath( 'media:thumbnail' );
	if ( empty( $thumbnails ) && $element != $group ) {
		$thumbnails = $element->xpath( 'media:thumbnail' );
	}
	
	if ( ! empty( $thumbnails ) ) {
		$max_width = 0;
		foreach ( $thumbnails as $thumbnail ) {
			if ( empty( $thumbnail['url'] ) || ! filter_var( $thumbnail['url'], FILTER_VALIDATE_URL ) ) {
				continue;
			}

			$current_width = intval( $thumbnail['width'] );
			if ( ! empty( $current_width ) && $max_width >= $current_width ) {
				continue;
			}

			$thumbnail_url = strval( $thumbnail['url'] );
			$max_width = $current_width;
		}
	}

	return $thumbnail_url;
}

/**
 * Extracts media player from an RSS element.
 *
 * @param SimpleXMLElement $element Current RSS elemenet.
 * @return string Player value on success, otherwise FALSE.
 */
function fpmrss_extract_media_player( SimpleXMLElement $element ) {
	$group = fpmrss_get_media_group( $element );
	if ( ! is_a( $group, 'SimpleXMLElement' ) ) {
		$group = $element;
	}

	$player_value = false;
	$player = $group->xpath( 'media:player' );
	if ( empty( $player ) && $group != $element ) {
		$player = $element->xpath( 'media:player' );
	}
	
	if ( ! empty( $player ) ) {
		$player = current( $player );
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
				$max_width = $current_width;
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
 * @global array $fpmrss_feed_thumbnails The array of feed thumbnails to import.
 * @param int $post_id Newly imported post id.
 * @param int $feed_id The feed id.
 */
function fpmrss_fetch_media_data( $post_id, $feed_id ) {
	global $fpmrss_feed_item, $fpmrss_feed_thumbnails;

	// do nothing if an xml element is not caught
	if ( ! $fpmrss_feed_item ) {
		return;
	}

	// init feed thumbnails array if it isn't
	if ( ! is_array( $fpmrss_feed_thumbnails ) ) {
		$fpmrss_feed_thumbnails = array();
	}

	// fetch thumbnail
	$thumbnail = fpmrss_extract_media_thumbnail( $fpmrss_feed_item );
	if ( $thumbnail ) {
		$fpmrss_feed_thumbnails[] = array( $thumbnail, $post_id );
	}

	// fetch player
	$player = fpmrss_extract_media_player( $fpmrss_feed_item );
	if ( $player ) {
		// check if redirect header exists for a video
		if ( filter_var( $player, FILTER_VALIDATE_URL ) ) {
			$response = wp_remote_head( $player );
			$location = wp_remote_retrieve_header( $response, 'location' );
			if ( ! empty( $location ) ) {
				$player = $location;
			}
		}

		// we need to switch a link on a video to player embed code
		if ( filter_var( $player, FILTER_VALIDATE_URL ) && preg_match( '#^https?\:\/\/.*?\.ooyala\.com\/(.+?)\/(.+?)\/?$#i', $player, $matches ) ) {
$player = <<<OOYALA_PLAYER
<script src="http://player.ooyala.com/player.js?embedCode={$matches[1]}&embedType=player.jsMRSS&videoPcode={$matches[2]}&width=480&height=270"></script>
<noscript>
  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="ooyalaPlayer_5j2v5o_nn9rxe" width="480" height="270" codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
	<param name="movie" value="http://player.ooyala.com/player_v2.swf?embedCode={$matches[1]}&keepEmbedCode=true&videoPcode={$matches[2]}"/>
	<param name="bgcolor" value="#000000"/>
	<param name="allowScriptAccess" value="always"/>
	<param name="allowFullScreen" value="true"/>
	<param name="flashvars" value="embedCode={$matches[1]}&embedType=noscriptObjectTagMRSS&videoPcode={$matches[2]}&width=480&height=270"/>
	<embed src="http://player.ooyala.com/player_v2.swf?embedCode={$matches[1]}&keepEmbedCode=true&videoPcode={$matches[2]}"
		bgcolor="#000000"
		width="480"
		height="270"
		name="ooyalaPlayer_572t57_nn9rxe" align="middle" play="true" loop="false"
		allowScriptAccess="always" allowFullScreen="true"
		type="application/x-shockwave-flash"
		flashvars="embedCode={$matches[1]}&embedType=noscriptObjectTagMRSS&videoPcode={$matches[2]}&width=480&height=270"
		pluginspage="http://www.adobe.com/go/getflashplayer">
	</embed>
  </object>
</noscript>
OOYALA_PLAYER;
		}

		// set player meta
		update_post_meta( $post_id, 'gmr-player', $player );
		set_post_format( $post_id, 'video' );
	}

	// copy Ooyala metas if available
	$metas = array( 'fpmrss-ooyala-player-id', 'fpmrss-ooyala-ad-set' );
	foreach ( $metas as $meta ) {
		$value = get_post_meta( $feed_id, $meta, true );
		if ( ! empty( $value ) ) {
			update_post_meta( $post_id, $meta, $value );
		}
	}

	$fpmrss_feed_item = null;
}
add_action( 'fp_created_post', 'fpmrss_fetch_media_data', 10, 2 );
add_action( 'fp_updated_post', 'fpmrss_fetch_media_data', 10, 2 );

/**
 * Lauches async task to import thumbnails.
 *
 * @global array $fpmrss_feed_thumbnails The array of feed thumbnails to import.
 */
function fpmrss_launch_async_thumbnails_import() {
	global $fpmrss_feed_thumbnails;

	// do nothing if feed thumbnails array if empty
	if ( empty( $fpmrss_feed_thumbnails ) ) {
		return;
	}

	// try to launch async task if available, otherwise schedule single event
	wp_schedule_single_event( current_time( 'timestamp', 1 ), 'fpmrss_import_thumbnails', array( $fpmrss_feed_thumbnails ) );
}
add_action( 'fp_post_feed_pull', 'fpmrss_launch_async_thumbnails_import' );

/**
 * Performs thumbnails import.
 *
 * @param array $thumbnails Array of arrays of thumbnail urls and post ids.
 */
function fpmrss_import_thumbnails( $thumbnails ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

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
	$file_array['name'] = fpmrss_generate_image_name( $image );
	$file_array['tmp_name'] = $tmp;
	$file_array['error'] = 0;

	$post_data = array();
	$post = get_post( $post_id );
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
 * @return string The image name.
 */
function fpmrss_generate_image_name( $image ) {
	$file_name = '';
	$matches = array();

	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image, $matches );
	if ( is_array( $matches ) && count( $matches ) ) {
		$file_name = str_replace( '%20', '-', basename( $matches[0] ) );
	} else {
		$sizes = getimagesize( $image );
		if ( is_array( $sizes ) && isset( $sizes['mime'] ) && ! empty( $sizes['mime'] ) ) {
			$ext = image_type_to_extension( $sizes[2] );
			$file_name = substr( sanitize_title( pathinfo( $image, PATHINFO_FILENAME ) ), 0, 254 ) . $ext;
		}
	}
	
	return $file_name;
}

/**
 * Appends player content if it is available for the current post.
 *
 * @param string $content The initial content of the post.
 * @return string Updated post content if player code available, otherwise initial value.
 */
function fpmrss_update_content( $content ) {
	$player = get_post_meta( get_the_ID(), 'gmr-player', true );
	if ( ! empty( $player ) ) {
		$content = $player . PHP_EOL . PHP_EOL . $content;
	}
	
	return $content;
}
add_action( 'the_content', 'fpmrss_update_content', 1 );

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
	}
}
add_action( 'add_meta_boxes', 'fpmrss_add_meta_box' );

/**
 * Renders Ooyala settings meta box.
 *
 * @param WP_Post $feed The feed object.
 */
function fpmrss_render_ooyala_metabox( $feed ) {
	$player_id = get_post_meta( $feed->ID, 'fpmrss-ooyala-player-id', true );
	$ad_set = get_post_meta( $feed->ID, 'fpmrss-ooyala-ad-set', true );

	wp_nonce_field( 'fpmrss-ooyala', 'fpmrss_ooyala_nonce', false );

	echo '<p>';
		echo '<label for="fpmrss-ooyala-player-id">Player ID:</label>';
		echo '<input type="text" id="fpmrss-ooyala-player-id" class="widefat" name="fpmrss-ooyala-player-id" value="', esc_attr( $player_id ), '">';
	echo '</p>';
	echo '<p>';
		echo '<label for="fpmrss-ooyala-ad-set">Ad Set:</label>';
		echo '<input type="text" id="fpmrss-ooyala-ad-set" class="widefat" name="fpmrss-ooyala-ad-set" value="', esc_attr( $ad_set ), '">';
	echo '</p>';
}

/**
 * Saves post settings.
 * 
 * @param int $post_id The post id.
 */
function fpmrss_save_settings( $post_id ) {
	$doing_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	$has_capabilities = current_user_can( 'edit_post', $post_id );
	$is_revision = 'revision' == get_post_type( $post_id );
	
	if ( $doing_autosave || ! $has_capabilities || $is_revision ) {
		return;
	}

	$ooyala_nonce = filter_input( INPUT_POST, 'fpmrss_ooyala_nonce' );
	if ( $ooyala_nonce && wp_verify_nonce( $ooyala_nonce, 'fpmrss-ooyala' ) ) {
		$fields = array( 'fpmrss-ooyala-player-id', 'fpmrss-ooyala-ad-set' );
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
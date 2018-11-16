<?php

/**
 * Plugin Name: Omny Studio
 * Description: Podcasts and episodes integration with Omny Studio
 * Version:     1.0.0
 * Author:      10up Inc
 * Author URI:  http://10up.com/
 */

new WP_Embed;

define( 'OMNY_STUDIO_VERSION', '20180319.1' );

function omny_init() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		$location = array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'podcast',
				),
			),
		);

		acf_add_local_field_group( array(
			'key'                   => 'group_5a7b2b84a6adb',
			'title'                 => 'Omny Studio',
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => 1,
			'location'              => $location,
			'fields'                => array(
				array(
					'key'   => 'omny_program_id',
					'label' => 'Program ID',
					'name'  => 'omny_program_id',
					'type'  => 'text',
				),
			),
		) );
	}

	wp_embed_register_handler( 'omny', '#https://omny.fm/shows/.*#i', 'omny_render_embed' );
}

function omny_render_embed( $matches, $attr, $url ) {
	$key = apply_filters( 'omny_embed_key', $url, $matches, $attr );

	$embed = wp_cache_get( $key, 'omny' );
	if ( empty( $embed ) ) {
		$embed = '';
		$response = wp_remote_get( 'https://omny.fm/oembed?url=' . urlencode( $url ) );
		if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
			$body = wp_remote_retrieve_body( $response );
			if ( ! empty( $body ) ) {
				$body = json_decode( $body, true );
				if ( ! empty( $body['html'] ) ) {
					$replace = sprintf(
						'<iframe data-title="%s" data-author="%s" ',
						! empty( $body['title'] ) ? $body['title'] : '',
						! empty( $body['author_name'] ) ? $body['author_name'] : ''
					);

					$embed = str_replace( '<iframe ', $replace, $body['html'] );
					$embed = apply_filters( 'omny_embed_html', $embed, $body );

					wp_cache_set( $key, $embed, 'omny', HOUR_IN_SECONDS );
				}
			}
		}
	}

	return $embed;
}

function omny_enqueue_scripts() {
	wp_register_script( 'playerjs', '//cdn.embed.ly/player-0.1.0.min.js', null, null, true );
	wp_register_script( 'omny', plugins_url( '/player.js', __FILE__ ), array( 'jquery', 'playerjs' ), OMNY_STUDIO_VERSION, true );
}

function omny_register_scheduled_events() {
	if ( ! wp_next_scheduled( 'omny_start_import_episodes' ) ) {
		wp_schedule_single_event( current_time( 'timestamp', 1 ) + 15 * MINUTE_IN_SECONDS, 'omny_start_import_episodes' );
	}
}

function omny_register_settings( $group, $page ) {
	add_settings_section( 'omny_settings', 'Omny Studio', '__return_false', $page );
	add_settings_field( 'omny_token', 'Access Token', 'bbgi_input_field', $page, 'omny_settings', 'name=omny_token' );

	register_setting( $group, 'omny_token', 'sanitize_text_field' );
}

function omny_api_request( $url, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'headers' => array(),
		'method'  => 'GET',
	) );

	$args['headers']['Authorization'] = 'OmnyToken ' . get_option( 'omny_token' );

	// @see: https://api.omnystudio.com/api-docs/index
	$response = wp_remote_request( "https://api.omnystudio.com/v0/{$url}", $args );
	if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
		return new \WP_Error();
	}

	$body = wp_remote_retrieve_body( $response );
	$json = json_decode( $body, true );

	return $json;
}

function omny_start_import_episodes() {
	if ( function_exists( 'wp_async_task_add' ) ) {
		wp_async_task_add( 'omny_run_import_episodes', array(), 'high' );
	} else {
		omny_run_import_episodes();
	}
}

function omny_run_import_episodes( $args = array(), $assoc_args = array() ) {
	$token = trim( get_option( 'omny_token' ) );
	if ( empty( $token ) ) {
		return;
	}

	$is_wp_cli = defined( 'WP_CLI' ) && WP_CLI;

	delete_option( 'omny_last_import_finished' );
	update_option( 'omny_last_import_started', time(), 'no' );

	$page = 1;
	$per_page = 100;
	if ( $is_wp_cli ) {
		if ( ! empty( $assoc_args['page'] ) && $assoc_args['page'] > 0 ) {
			$page = intval( $assoc_args['page'] );
		}

		if ( ! empty( $assoc_args['per_page'] ) && $assoc_args['per_page'] > 0 ) {
			$per_page = intval( $assoc_args['per_page'] );
		}
	}

	$clips = omny_get_clips( $page, $per_page, $is_wp_cli );
	$clips_map = omny_get_clips_map( $clips );

	$post_ids = array();
	foreach ( $clips as $clip ) {
		if ( ! empty( $clips_map[ $clip['Id'] ] ) ) {
			$is_wp_cli && \WP_CLI::log( sprintf( 'Skipping %s (%s) episode...', $clip['Title'], $clip['Id'] ) );
			continue;
		}

		$published_utc = strtotime( $clip['PublishedUtc'] );
		$date_gmt = date( 'Y-m-d H:i:s', $published_utc );
		$date = get_date_from_gmt( $date_gmt );

		switch ( $clip['Visibility'] ) {
			case 'Private':
				$status = 'private';
				break;
			default:
				$status = 'publish';
				break;
		}

		if ( $published_utc > time() ) {
			$status = 'future';
		}

		$args = array(
			'post_title'    => html_entity_decode( $clip['Title'] ),
			'post_content'  => sprintf( '[embed]%s[/embed]%s%s', $clip['PublishedUrl'], PHP_EOL, html_entity_decode( $clip['Description'] ) ),
			'post_status'   => $status,
			'post_type'     => 'episode',
			'ping_status'   => 'closed',
			'post_parent'   => $clip['PodcastId'],
			'post_date'     => $date,
			'post_date_gmt' => $date_gmt,
			'guid'          => $clip['Id'],
			'meta_input'    => array(
				'omny-clip-id'     => $clip['Id'],
				'omny-embed-url'   => $clip['EmbedUrl'],
				'omny-publish-url' => $clip['PublishedUrl'],
				'omny-audio-url'   => $clip['AudioUrl'],
				'omny-image-url'   => $clip['ImageUrl'],
				'omny-duration'    => $clip['DurationSeconds'],
			),
		);

		$post_id = wp_insert_post( $args, true );
		if ( ! is_wp_error( $post_id ) ) {
			set_post_thumbnail( $post_id, get_post_thumbnail_id( $clip['PodcastId'] ) );
			$is_wp_cli && \WP_CLI::success( sprintf( 'Imported %s (%s) episode...', $clip['Title'], $clip['Id'] ) );
		}
	}

	update_option( 'omny_last_import_finished', time(), 'no' );
}

function omny_import_clip_image( $image_url, $post_id ) {
	global $wpdb;

	if ( ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
		return;
	}

	$url = omny_get_image_url( $image_url );
	$key = 'omny-image-id-' . $url;
	$attachment_id = wp_cache_get( $key, 'omny-studio' );
	if ( $attachment_id === false ) {
		$query = $wpdb->prepare( "SELECT `ID` FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND `guid` = %s LIMIT 1", $url );
		$attachment_id = $wpdb->get_var( $query );
		if ( $attachment_id > 0 ) {
			wp_cache_set( $key, $attachment_id, 'omny-studio' );
		}
	}

	if ( empty( $attachment_id ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$file_array = array();
		$file_array['name'] = md5( $url ) . '.jpg';
		$file_array['tmp_name'] = download_url( $url );
		if ( ! is_wp_error( $file_array['tmp_name'] ) ) {
			$attachment_id = media_handle_sideload( $file_array, $post_id, null, array( 'guid' => $url ) );
		}
	}

	if ( $attachment_id > 0 ) {
		set_post_thumbnail( $post_id, intval( $attachment_id ) );
	}
}

function omny_get_image_url( $image_url ) {
	$key = 'omny-image-url-' . $image_url;
	$url = wp_cache_get( $key, 'omny-studio' );
	if ( $url === false ) {
		$url = $image_url;
		$response = wp_remote_head( $url, array( 'redirection' => 0 ) );
		if ( ! is_wp_error( $response ) ) {
			$headers = wp_remote_retrieve_headers( $response );
			if ( ! empty( $headers['location'] ) ) {
				if ( preg_match( '#^/[^/]#', $headers['location'] ) ) {
					$parsed = parse_url( $url );
					$replace = $parsed['path'];

					if ( ! empty( $parsed['query'] ) ) {
						$replace .= '?' . $parsed['query'];
					}

					if ( ! empty( $parsed['fragment'] ) ) {
						$replace .= '#' . $parsed['fragment'];
					}

					$url = str_replace( $replace, $headers['location'], $url );
				} else {
					$url = $headers['location'];
				}
			}
		}

		wp_cache_set( $key, $url, 'omny-studio' );
	}

	return preg_replace( '#\?.*#', '', $url );
}

function omny_get_clips( $page, $per_page, $is_wp_cli = false ) {
	$clips = array();

	$cursor = filter_var( $page, FILTER_VALIDATE_INT, array(
		'options' => array(
			'min_range' => 1,
			'default'   => 1,
		),
	) );

	$page_size = filter_var( $per_page, FILTER_VALIDATE_INT, array(
		'options' => array(
			'min_range' => 1,
			'max_range' => 100,
			'default'   => 10,
		),
	) );

	foreach ( get_posts( 'post_type=podcast&posts_per_page=100' ) as $podcast ) {
		$program_id = get_post_meta( $podcast->ID, 'omny_program_id', true );
		if ( empty( $program_id ) ) {
			$is_wp_cli && \WP_CLI::log( sprintf( 'Skipping %s (%s) podcast...', $podcast->post_title, $podcast->ID ) );
			continue;
		}

		$response = omny_api_request( "programs/{$program_id}/clips?cursor={$cursor}&pageSize={$page_size}" );
		if ( is_wp_error( $response ) ) {
			continue;
		}

		foreach ( $response['Clips'] as $clip ) {
			if ( ! empty( $clip['PublishState'] ) && strtolower( $clip['PublishState'] ) == 'published' ) {
				$clip['PodcastId'] = $podcast->ID;
				$clips[] = $clip;
			}
		}
	}

	return $clips;
}

function omny_get_clips_map( $clips ) {
	global $wpdb;

	$clips_map = array();
	$clip_ids = array_filter( array_map( 'trim', wp_list_pluck( $clips, 'Id' ) ) );

	$query = sprintf(
		'SELECT `meta_value`, `post_id` FROM %s WHERE `meta_key` = "omny-clip-id" AND `meta_value` IN ("%s")',
		$wpdb->postmeta,
		implode( '", "', array_map( 'esc_sql', $clip_ids ) )
	);

	foreach ( $wpdb->get_results( $query, ARRAY_A ) as $row ) {
		$clips_map[ $row['meta_value'] ] = $row['post_id'];
	}

	return $clips_map;
}

function omny_get_episode_audio_url( $url, $post ) {
	if ( empty( $url ) ) {
		$url = get_post_meta( $post->ID, 'omny-audio-url', true );
		$url = filter_var( $url, FILTER_VALIDATE_URL );
	}

	return $url;
}

add_action( 'init', 'omny_init' );
add_action( 'admin_init', 'omny_register_scheduled_events' );
add_action( 'bbgi_register_settings', 'omny_register_settings', 5, 2 );
add_action( 'omny_start_import_episodes', 'omny_start_import_episodes' );
add_action( 'omny_run_import_episodes', 'omny_run_import_episodes' );
add_action( 'wp_enqueue_scripts', 'omny_enqueue_scripts', 1 );

add_filter( 'beasley-episode-audio-url', 'omny_get_episode_audio_url', 10, 2 );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'omny import', 'omny_run_import_episodes' );
}

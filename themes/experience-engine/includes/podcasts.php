<?php

add_filter( 'omny_embed_html', 'ee_update_omny_embed', 10, 2 );
add_filter( 'ee_post_thumbnail_id', 'ee_update_episode_thumbnail', 10, 2 );

if ( ! function_exists( 'ee_get_episodes_query' ) ) :
	function ee_get_episodes_query( $podcast = null, $args = array() ) {
		$podcast = get_post( $podcast );
		$args = wp_parse_args( $args );

		return new \WP_Query( array_merge( $args, array(
			'post_type'   => 'episode',
			'post_parent' => $podcast->ID,
		) ) );
	}
endif;

if ( ! function_exists( 'ee_get_episodes_count' ) ) :
	function ee_get_episodes_count( $podcast = null ) {
		$podcast = get_post( $podcast );
		$key = 'podcast-episodes-count-' . $podcast->ID;

		$count = wp_cache_get( $key, 'experience-engine' );
		if ( ! $count ) {
			$query = ee_get_episodes_query( $podcast, array(
				'posts_per_page' => 1,
				'fields'         => 'ids',
			) );

			$count = $query->found_posts;
			wp_cache_set( $key, $count, 'experience-engine', 15 * MINUTE_IN_SECONDS );
		}

		return $count;
	}
endif;

if ( ! function_exists( 'ee_get_episode_player' ) ) :
	function ee_get_episode_player( $episode = null ) {
		global $ee_feed_now;

		$episode = get_post( $episode );
		if ( ! is_a( $episode, '\WP_Post' ) ) {
			return null;
		}

		if ( ! empty( $episode->media ) && ! empty( $episode->media['url'] ) ) {
			$url = explode( '?', $episode->media['url'] );
			return ee_get_lazy_audio( current( $url ), $episode->post_title, ! empty( $ee_feed_now['title'] ) ? $ee_feed_now['title'] : '' );
		}

		if ( preg_match( '#\[(embed|audio).*?\].*?\[\/(embed|audio)\]#i', $episode->post_content, $matches ) && $matches[1] == $matches[2] ) {
			$shortcode = $matches[0];
		}

		if ( $shortcode ) {
			remove_filter( 'the_content', 'wpautop' );
			$content = apply_filters( 'the_content', $shortcode );
			add_filter( 'the_content', 'wpautop' );

			return $content;
		}

		return null;
	}
endif;

if ( ! function_exists( 'ee_the_episode_player' ) ) :
	function ee_the_episode_player( $episode = null ) {
		echo ee_get_episode_player( $episode );
	}
endif;

if ( ! function_exists( 'ee_the_latest_episode' ) ) :
	function ee_the_latest_episode( $podcast = null ) {
		$podcast = get_post( $podcast );
		$key = 'podcast-latest-episodes-' . $podcast->ID;

		$episode = wp_cache_get( $key, 'experience-engine' );
		if ( ! $episode ) {
			$query = ee_get_episodes_query( $podcast, array(
				'posts_per_page' => 1,
				'fields'         => 'ids',
			) );

			$episode = $query->next_post();
			wp_cache_set( $key, $episode, 'experience-engine', 15 * MINUTE_IN_SECONDS );
		}

		if ( $episode ) {
			$episode = get_post( $episode );
			$player = ee_get_episode_player( $episode );
			if ( $player ) {
				echo $player; ?>
					<p class="latest">
						Play Latest (<?php ee_the_date( $episode ); ?>)
					</p><?php
			}
		}
	}
endif;

if ( ! function_exists( 'ee_get_episode_meta' ) ) :
	function ee_get_episode_meta( $episode, $meta_key ) {
		$episode = get_post( $episode );
		if ( ! is_a( $episode, '\WP_Post' ) ) {
			return false;
		}

		switch ( $meta_key ) {
			case 'duration':
				if ( ! empty( $episode->media ) && ! empty( $episode->media['duration'] ) ) {
					return preg_match( '/00\:(\d{2}\:\d{2})/', $episode->media['duration'], $matches )
						? $matches[1]
						: $episode->media['duration'];
				}

				$duration = intval( get_post_meta( $episode->ID, 'duration', true ) );
				if ( ! $duration ) {
					$duration = intval( get_post_meta( $episode->ID, 'omny-duration', true ) );
				}

				if ( ! $duration ) {
					return false;
				}

				$duration = date( 'H:i:s', strtotime( '2000-01-01' ) + $duration  );
				if ( preg_match( '/00\:(\d{2}\:\d{2})/', $duration, $matches ) ) {
					$duration = $matches[1];
				}

				return $duration;

			case 'download':
				$download = false;
				if ( ! empty( $episode->media ) && ! empty( $episode->media['url'] ) ) {
					$download = $episode->media['url'];
				}

				if ( ! filter_var( $download, FILTER_VALIDATE_URL ) ) {
					$download = get_post_meta( $episode->ID, 'omny-audio-url', true );
				}

				if ( filter_var( $download, FILTER_VALIDATE_URL ) ) {
					$download = add_query_arg( 'download', 'true', $download );
					return $download;
				}
				break;
		}

		return false;
	}
endif;

if ( ! function_exists( 'ee_get_podcast_meta' ) ) :
	function ee_get_podcast_meta( $podcast, $meta_key ) {
		$podcast = get_post( $podcast );
		if ( ! is_a( $podcast, '\WP_Post' ) ) {
			return false;
		}

		switch ( $meta_key ) {
			case 'feed_url':
				$feed_url = get_post_meta( $podcast->ID, 'gmp_podcast_feed', true );
				if ( ! filter_var( $feed_url, FILTER_VALIDATE_URL ) ) {
					$feed_url = add_query_arg( array(
						'feed'           => 'podcast',
						'podcast_series' => rawurldecode( $podcast->post_name ),
					), home_url( '/' ) );
				}
				return $feed_url;
			case 'itunes_url':
				return get_post_meta( $podcast->ID, 'gmp_podcast_itunes_url', true );
			case 'google_play_url':
				return get_post_meta( $podcast->ID, 'gmp_podcast_google_play_url', true );
		}

		return false;
	}
endif;

if ( ! function_exists( 'ee_update_omny_embed' ) ) :
	function ee_update_omny_embed( $embed, $args ) {
		$audio = get_post_meta( get_the_ID(), 'omny-audio-url', true );
		if ( filter_var( $audio, FILTER_VALIDATE_URL ) ) {
			return ee_get_lazy_audio( $audio, $args['title'], $args['author_name'] );
		}

		$embed = str_replace( 'iframe', 'div', $embed );
		$embed = str_replace( '<div ', '<div class="omny-embed" ', $embed );

		return $embed;
	}
endif;

if ( ! function_exists( 'ee_update_episode_thumbnail' ) ) :
	function ee_update_episode_thumbnail( $thumbnail_id, $post ) {
		$episode = get_post( $post );
		if ( is_a( $episode, '\WP_Post' ) && $episode->post_type == 'episode' ) {
			$image = false;
			if ( $thumbnail_id ) {
				$image = wp_get_attachment_image_src( $thumbnail_id );
			}

			if ( ! $image && $episode->post_parent ) {
				$podcast = get_post( $episode->post_parent );
				if ( is_a( $podcast, '\WP_Post' ) && $podcast->post_type == 'podcast' && has_post_thumbnail( $podcast ) ) {
					$thumbnail_id = get_post_thumbnail_id( $podcast );
				}
			}
		}

		return $thumbnail_id;
	}
endif;

if ( ! function_exists( 'ee_the_episode_download' ) ) :
	function ee_the_episode_download( $classes = '' ) {
		$download = ee_get_episode_meta( null, 'download' );
		if ( $download ) {
			echo '<a class="btn -empty ', esc_attr( $classes ), '" href="', esc_url( $download ), '" target="_blank" rel="noopener">Download</a>';
		}
	}
endif;

if ( ! function_exists( 'ee_get_lazy_audio' ) ) :
	function ee_get_lazy_audio( $url, $title, $author ) {
		return sprintf( 
			'<div class="lazy-audio" data-src="%s" data-title="%s" data-author="%s"></div>',
			esc_attr( $url ),
			esc_attr( $title ),
			esc_attr( $author )
		);
	}
endif;

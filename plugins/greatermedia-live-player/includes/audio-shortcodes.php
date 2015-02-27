<?php

/**
 * Class GMLP_Player
 */
class GMR_Audio_Shortcodes {

	public static function init() {
		add_filter( 'wp_audio_shortcode', array( __CLASS__, 'custom_audio_styling' ), 10, 5 );
	}

	/**
	 * Essentially a cached version of attachment_url_to_postid
	 *
	 * @param $url
	 *
	 * @return bool|int
	 */
	public static function get_id_from_url( $url ) {
		$hash = md5( $url );
		$key = 'audio-' . $hash . '-postid';

		$result = wp_cache_get( $key );

		if ( $result !== false ) {
			return $result;
		}

		$post_id = attachment_url_to_postid( $url );

		if ( $post_id ) {
			wp_cache_set( $key, $post_id );
			return $post_id;
		} else {
			wp_cache_set( $key, 0 );
			return false;
		}
	}

	public static function custom_audio_styling( $html, $atts, $audio, $post_id, $library ) {
		if ( is_admin() ) {
			return $html;
		}

		/*
		 * Spec supports mp3 only, as do the browsers we're trying to use audio element with.
		 * Anything else will just use media element, rather than the live player
		 * support could be expanded later if necessary, checking types with wp_get_audio_extensions() to know supported
		 * audio types in core, but we'd likely need better browser support, or some fixes to mediaelement so that
		 * it works better (at all) when not attached to a real element in the visible DOM
		 */
		if ( ! isset( $atts['mp3'] ) || empty( $atts['mp3'] ) ) {
			$new_html = '<div class="gmr-mediaelement">';
			$new_html .= $html;
			$new_html .= '</div>';

			return $new_html;
		}

		$mp3_src = $atts['mp3'];
		if ( ! function_exists( 'wp_read_audio_metadata' ) ) {
			include_once trailingslashit( ABSPATH ) . 'wp-admin/includes/media.php';
		}

		$metadata_defaults = array(
			'title' => '',
			'length_formatted' => '',
			'artist' => '',
		);

		/*
		 * Breakdown on how we get title data.
		 *
		 * Try to map a MP3 url to a post ID - If that matches, we use THAT title. Cache either way, to reduce DB impact.
		 *
		 * Fall back to parsing mp3 file for metadata.
		 *
		 * Fall back to nothing.
		 */
		$att_post_id = self::get_id_from_url( $mp3_src );
		if ( $att_post_id ) {
			$att_post = get_post( $att_post_id );
		}
		if ( $att_post_id && $att_post ) {
			$metadata = wp_get_attachment_metadata( $att_post_id );
			$metadata['title'] = $att_post->post_title;
		} else if ( function_exists( 'wp_read_audio_metadata' ) ) {
			$fileinfo = parse_url( $mp3_src );
			$file_path = ABSPATH . $fileinfo['path'];
			$metadata = wp_read_audio_metadata( $file_path );
			$metadata = wp_parse_args( $metadata, $metadata_defaults );
		} else {
			$metadata = $metadata_defaults;
		}

		$hash = md5( $mp3_src );

		if (empty($metadata['title'])) {
			$title = get_the_title();
		} else {
			$title = $metadata['title'];
		}

		$is_podcast_archive = is_post_type_archive( GMP_CPT::PODCAST_POST_TYPE );

		$parent_podcast_id = wp_get_post_parent_id( $post_id );
		if( $parent_podcast_id ) {
			$parent_podcast = get_post( $parent_podcast_id );
			$itunes_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_itunes_url', true );
		} else {
			$parent_podcast = false;
		}

		if( $is_podcast_archive ) {
			$parent_podcast_id = $post_id;
			$itunes_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_itunes_url', true );
			$args = array(
					'numberposts' => 500,
					'offset' => 0,
					'orderby' => 'post_date',
					'order' => 'DESC',
					'post_type' => GMP_CPT::EPISODE_POST_TYPE,
					'post_status' => 'publish',
					'post_parent' => $post_id
			);

			$episodes =  wp_get_recent_posts( $args );
			$recent_episode = $episodes[0];
			$episode_date = strtotime( $recent_episode['post_date'] );
		}

		//get podcast featured image
		$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $parent_podcast_id ) );

		$series = get_post( $parent_podcast_id );
		$series_slug = $series->post_name;
		$feed_url = esc_url_raw( get_post_meta( $parent_podcast_id, 'gmp_podcast_feed', true ) );
		if( !$feed_url || $feed_url == '' || strlen( $feed_url ) == 0 ) {
			$feed_url = home_url( '/' ) . '?feed=podcast&podcast_series=' . $series_slug;
		}

		$downloadable = get_post_meta( $post_id, 'gmp_audio_downloadable', true );
		$new_html = '';

		$is_podcast = is_singular( array( ShowsCPT::SHOW_CPT, GMP_CPT::PODCAST_POST_TYPE ) );

		// podcast archive details

		if ( $is_podcast || $is_podcast_archive ) {
			$new_html .= '<div class="podcast-player">';
		} else {
			$new_html .= '<div class="podcast-player podcast-player--compact">';
		}
		$new_html .= '<div class="podcast__play mp3-' . esc_attr( $hash ) . '">'; // Hash is used to ensure the inline audio can always match state of live player, even when the player is the buttons that are clicked
		$new_html .= '<div class="podcast__cover"  style="background-image: url(' . $featured_image . ');">';
		if ( $is_podcast ) {
			$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . get_the_title() . '" data-mp3-artist="' . esc_html( $parent_podcast->post_title ) . ' - ' . get_the_time( 'n.j.y' ) . '" data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		} elseif(  $is_podcast_archive ) {
			$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . get_the_title() . '" data-mp3-artist="' . esc_html( get_the_title() ) . ' - ' . get_the_time( 'n.j.y' ) . '" data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		} else {
			$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . esc_attr( $title ) . '" data-mp3-artist=" " data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		}
		$new_html .= '<button class="podcast__btn--pause"></button>';
		$new_html .= '</div>';
		if ( $is_podcast || $is_podcast_archive ) {
			$new_html .= '<div id="audio__time" class="audio__time">';
			$new_html .= '<div id="audio__progress-bar" class="audio__progress-bar">';
			$new_html .= '<span id="audio__progress" class="audio__progress"></span>';
			$new_html .= '</div>';
			$new_html .= '<div id="audio__time--elapsed" class="audio__time--elapsed"></div>';
			$new_html .= '<div id="audio__time--remaining" class="audio__time--remaining"></div>';
			$new_html .= '</div>';
		}

		$new_html .= '<span class="podcast__runtime">';

		if ( isset( $metadata['length_formatted'] ) ) {
			$new_html .= esc_html( $metadata['length_formatted'] );
		}

		$new_html .= '</span>';

		if( ( $is_podcast || $is_podcast_archive ) && ( $downloadable == 'on' || $downloadable == '' ) ) {
			$new_html .= '<div class="podcast__download">';
			if( !is_singular( GMP_CPT::PODCAST_POST_TYPE ) ) {
				if( $parent_podcast_id && ( $is_podcast || $is_podcast_archive ) ) {
					if( $itunes_url != '' ) {
						$new_html .= '<a class="podcast__subscribe show-mobile" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe in iTunes</a>';
					}
					$new_html .= '<a class="podcast__rss show-mobile" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast Feed</a>';
				}
			}
			if( ! $is_podcast_archive) {
				$new_html .= '<a href="' . esc_attr( $mp3_src ) . '" download="' . esc_attr( $mp3_src ) . '" class="podcast__download--btn" download>Download</a>';
			}
			$new_html .= '</div>';
		}
		// if( $featured_image ) {
		// 	$new_html .= '<img src="' . $featured_image . '" class="podcast__img">';
		// }
		$new_html .= '</div>';
		$new_html .= '<div class="podcast__meta">';
		if ( $is_podcast ) {
			$new_html .= '<time class="podcast__date" datetime="' . get_the_time( 'c' ) . '">' . get_the_time( 'F j' ) . '</time>';
			$new_html .= '<h3 class="podcast__title">' . get_the_title() . '</h3>';
		} elseif( $is_podcast_archive ) {
			$new_html .= '<h3 class="podcast__title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
			$new_html .= '<time class="podcast__date" datetime="' . date( 'c', $episode_date ) . '">LATEST EPISODE: ' . date( 'F j', $episode_date ) . '</time>';
		} else {
			$new_html .= '<h3 class="podcast__title">' . esc_html( $title ) . '</h3>';
		}
		if( $parent_podcast_id && $is_podcast && !is_singular( GMP_CPT::PODCAST_POST_TYPE ) ) {
			$new_html .= '<div class="podcast__parent--title podcast__parent--title--mobile show-mobile"><a href="' . esc_url( get_permalink( $parent_podcast->ID) )  . '">'. esc_html( $parent_podcast->post_title ) . '</a></div>';
			$new_html .= '<div class="podcast__parent hide-mobile"><div class="podcast__parent--title"><a href="' . esc_url( get_permalink( $parent_podcast->ID) )  . '">'. esc_html( $parent_podcast->post_title ) . '</a></div>';
			if( $itunes_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe in iTunes</a>';
			}
			$new_html .= '<a class="podcast__rss" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast Feed</a>';
			$new_html .= '</div>';
		} elseif( $is_podcast_archive ) {
			$new_html .= '<div class="podcast__parent--title podcast__parent--title--mobile show-mobile">'. esc_html( $recent_episode['post_title'] ) . '</div>';
			$new_html .= '<div class="podcast__parent hide-mobile"><div class="podcast__parent--title">'. esc_html( $recent_episode['post_title'] ) . '</div>';
			if( $itunes_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe in iTunes</a>';
			}
			$new_html .= '<a class="podcast__rss" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast Feed</a>';
			$new_html .= '</div>';
		}
		if ( $is_podcast || $is_podcast_archive ) {
			$new_html .= '<div class="podcast__desc">' . get_the_excerpt() . '</div>' ;
		}

		if ( $is_podcast_archive ) {
			$new_html .= '<div class="podcast__archive--episode-count">' . count( $episodes ) . ' Episodes</div>';
		}
		if ( ! $is_podcast && !$is_podcast_archive ) {
			$new_html .= '<div id="audio__time" class="audio__time">';
			$new_html .= '<div id="audio__progress-bar" class="audio__progress-bar">';
			$new_html .= '<span id="audio__progress" class="audio__progress"></span>';
			$new_html .= '</div>';
			$new_html .= '<div id="audio__time--elapsed" class="audio__time--elapsed"></div>';
			$new_html .= '<div id="audio__time--remaining" class="audio__time--remaining"></div>';
			$new_html .= '</div>';
		}
		$new_html .= '<a href="' . esc_attr( $mp3_src ) . '" download="' . esc_attr( $mp3_src ) . '" class="podcast__download--fallback" download>Download Podcast</a>';
		$new_html .= '</div>'; // .podcast__meta
		$new_html .= '<div class="gmr-mediaelement-fallback">' . $html . '</div>';
		$new_html .= '</div>'; // .podcast-player

		update_post_meta( $post_id, 'enclosure', esc_attr( $mp3_src ) );

		if ( isset( $metadata['length_formatted'] ) ) {
			update_post_meta( $post_id, 'duration', esc_html( $metadata['length_formatted'] ) );
		}

		return $new_html;
	}

}

GMR_Audio_Shortcodes::init();

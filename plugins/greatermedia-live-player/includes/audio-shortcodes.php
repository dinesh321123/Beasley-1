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

		// Use local URL instead of S3, to ensure we get the correct post.
		if ( false !== strpos( $url, 'amazonaws' ) || false !== strpos( $url, 'files.greatermedia.com' ) ) {
			$url = strstr( $url, 'sites' );
			$url = str_replace( 'sites/' . get_current_blog_id() . '/', '', $url );
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
		global $podcast_episodes_query;

		if ( is_admin() ) {
			return $html;
		}

		if ( is_feed() ) {
			return ' ';
		}

//		/*
//		 * Spec supports mp3 only, as do the browsers we're trying to use audio element with.
//		 * Anything else will just use media element, rather than the live player
//		 * support could be expanded later if necessary, checking types with wp_get_audio_extensions() to know supported
//		 * audio types in core, but we'd likely need better browser support, or some fixes to mediaelement so that
//		 * it works better (at all) when not attached to a real element in the visible DOM
//		 */
//		if ( ! isset( $atts['mp3'] ) || empty( $atts['mp3'] ) ) {
//			$new_html = '<div class="gmr-mediaelement">';
//			$new_html .= $html;
//			$new_html .= '</div>';
//
//			return $new_html;
//		}

		$formats = array( 'mp3', 'ogg', 'wma', 'm4a', 'wav' );
		foreach ( $formats as $format ) {
			if ( ! empty( $atts[ $format ] ) && filter_var( $atts[ $format ], FILTER_VALIDATE_URL ) ) {
				$mp3_src = $atts[ $format ];
				break;
			}
		}

		// Sometimes, we just have "src" instead of something more specific
		if ( empty( $mp3_src ) && isset( $atts['src'] ) && ! empty( $atts['src'] ) && filter_var( $atts['src'], FILTER_VALIDATE_URL ) ) {
			$mp3_src = $atts['src'];
		}

		if ( ! function_exists( 'wp_read_audio_metadata' ) ) {
			include_once trailingslashit( ABSPATH ) . 'wp-admin/includes/media.php';
		}

		$metadata_defaults = array(
			'title'            => '',
			'length_formatted' => '',
			'artist'           => '',
		);

		/* Don't look for data if the mp3 source is absent */
		if ( empty( $mp3_src ) ) {
			return $html;
		}

		$current_blog_id = get_current_blog_id();

		/* Fix URLs on older podcasts.
		 * This is a "quick fix", ideally we'd create a script to update the DB.
		*/
		if ( class_exists( 'S3_Uploads' ) ) {
			// Ensure we have an S3 URL and not local, fixes issues with old URLs prior to S3
			$site_url = trailingslashit( get_site_url( $current_blog_id, '', 'http' ) );
			if ( false !== strpos( $mp3_src, $site_url ) ) {
				$upload_dir = wp_upload_dir();
				// Prod and stage includes the s3 URL, which is incorrect since we have them mapped
				$upload_dir['baseurl'] = str_replace( '.s3.amazonaws.com', '', $upload_dir['baseurl'] );
				$mp3_src = str_replace( $site_url . 'wp-content/uploads/' . 'sites/' . $current_blog_id, $upload_dir['baseurl'], $mp3_src );
			}
		}

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

		if ( empty( $metadata['title'] ) ) {
			$title = get_the_title();
		} else {
			$title = $metadata['title'];
		}

		$is_podcast = is_singular( array( ShowsCPT::SHOW_CPT, 'podcast' ) );
		$is_podcast_archive = is_post_type_archive( 'podcast' );
		$is_episode = get_post_type($post_id) == 'episode' ? true : false;
		$is_home = is_home();

		$parent_podcast = false;
		$parent_podcast_id = wp_get_post_parent_id( $post_id );
		if ( $parent_podcast_id ) {
			$parent_podcast = get_post( $parent_podcast_id );
			$itunes_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_itunes_url', true );
			$google_play_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_google_play_url', true );
		}

		if ( $is_podcast_archive ) {
			$itunes_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_itunes_url', true );
			$google_play_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_google_play_url', true );
			$episode_date = strtotime( get_post_field( 'post_date', null ) );
		}

		//get podcast featured image
		$featured_image = false;
		if ( $is_episode ) {
			$featured_image = get_post_thumbnail_id( $post_id );
			if ( $featured_image ) {
				$featured_image = wp_get_attachment_url( $featured_image );
			}
		}

		if ( ! $featured_image ) {
			$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $parent_podcast_id ) );
		}

		$series = get_post( $parent_podcast_id );
		$series_slug = $series->post_name;
		$feed_url = esc_url_raw( get_post_meta( $parent_podcast_id, 'gmp_podcast_feed', true ) );
		if ( ! $feed_url || $feed_url == '' || strlen( $feed_url ) == 0 ) {
			$feed_url = home_url( '/' ) . '?feed=podcast&podcast_series=' . $series_slug;
		}

		$downloadable = get_post_meta( $post_id, 'gmp_audio_downloadable', true );
		$downloadable = filter_var( $downloadable, FILTER_VALIDATE_BOOLEAN );
		$new_html = '';

		// podcast archive details

		if ( $is_podcast || $is_podcast_archive || $is_home ) {
			$new_html .= '<div class="podcast-player">';
		} else {
			$new_html .= '<div class="podcast-player podcast-player--compact">';
		}
		
		$new_html .= '<div class="podcast__play mp3-' . esc_attr( $hash ) . '">'; // Hash is used to ensure the inline audio can always match state of live player, even when the player is the buttons that are clicked
		$new_html .= '<div class="podcast__cover"  style="background-image: url(' . $featured_image . ');">';

		if ( $is_podcast ) {
			$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . get_the_title() . '" data-mp3-artist="' . esc_html( $parent_podcast->post_title ) . ' - ' . get_the_time( 'n.j.y' ) . '" data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		} elseif ( $is_podcast_archive || $is_home ) {
			$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . get_the_title() . '" data-mp3-artist="' . esc_html( get_the_title() ) . ' - ' . get_the_time( 'n.j.y' ) . '" data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		} else {
			$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . esc_attr( $title ) . '" data-mp3-artist=" " data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		}

		$new_html .= '<button class="podcast__btn--pause"></button>';
		$new_html .= '</div>';

		if ( $is_podcast || $is_podcast_archive || $is_home ) {
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

		if ( ( $is_podcast || $is_podcast_archive || $is_home ) && $downloadable ) {
			$new_html .= '<div class="podcast__download">';
			if ( ! is_singular( 'podcast' ) ) {
				if ( $parent_podcast_id && ( $is_podcast || $is_podcast_archive || $is_home ) ) {
					if ( $itunes_url != '' ) {
						$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe in iTunes</a>';
					}
					if ( $google_play_url != '' ) {
						$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $google_play_url ) . '" target="_blank">Subscribe in Google Play</a>';
					}
					$new_html .= self::filter_episode_content(
						$post_id,
						'<a class="podcast__rss" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast Feed</a>'
					);
				}

				if ( $is_podcast_archive && $podcast_episodes_query ) {
					$new_html .= '<div class="podcast__archive--episode-count">' . $podcast_episodes_query->found_posts . ' Episodes</div>';
				}
			}
			
			if ( ! $is_podcast_archive ) {
				$new_html .= '<a href="' . esc_attr( $mp3_src ) . '" download="' . esc_attr( $mp3_src ) . '" class="podcast__download--btn" download>Download</a>';
			}

			$new_html .= '</div>';
		}

		$new_html .= '</div>';
		$new_html .= '<div class="podcast__meta">';
		if ( $is_podcast || $is_home ) {
			$new_html .= '<time class="podcast__date" datetime="' . get_the_time( 'c' ) . '">' . get_the_time( 'F j, Y' ) . '</time>';
			$new_html .= '<h3 class="podcast__title"><a href="' . esc_url( get_the_permalink( get_the_ID() ) ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
		} elseif ( $is_podcast_archive ) {
			$parent_title = esc_html( $parent_podcast->post_title );
			$new_html .= '<h3 class="podcast__title"><a href="' . esc_url( get_the_permalink( $parent_podcast ) ) . '">' . esc_html( $parent_title ) . '</a></h3>';
			$new_html .= '<a class="podcast__rss" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast Feed</a>';
			if ( $itunes_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe in iTunes</a>';
			}
			if ( $google_play_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $google_play_url ) . '" target="_blank">Subscribe in Google Play</a>';
			}

		} else {
			$new_html .= '<h3 class="podcast__title">' . esc_html( $title ) . '</h3>';
		}

		if ( $is_podcast_archive && $podcast_episodes_query ) {
			$new_html .= '<div class="podcast__archive--episode-count">' . $podcast_episodes_query->found_posts . ' Episodes</div>';
		}

		if ( $parent_podcast_id && $is_podcast && !is_singular( 'podcast' ) || $is_home ) {
			$new_html .= '<div class="podcast__parent"><div class="podcast__parent--title"><a href="' . esc_url( get_permalink( $parent_podcast->ID ) ) . '">' . esc_html( $parent_podcast->post_title ) . '</a></div>';
			if ( $itunes_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe in iTunes</a>';
			}
			if ( $google_play_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $google_play_url ) . '" target="_blank">Subscribe in Google Play</a>';
			}
			$new_html .= self::filter_episode_content(
				$post_id,
				'<a class="podcast__rss" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast Feed</a>'
			);
			$new_html .= '</div>';
		} elseif ( $is_podcast_archive ) {

			$new_html .= '<div class="podcast__parent"><div class="podcast__parent--title">Latest Episode: ' . get_the_title() . '</div>';
			$new_html .= '<time class="podcast__date" datetime="' . date( 'c', $episode_date ) . '">' . date( 'F j, Y', $episode_date ) . '</time>';

			$new_html .= '</div>';
		}

		if ( $is_podcast || $is_podcast_archive || $is_home ) {
			$new_html .= '<div class="podcast__desc">' . get_the_excerpt() . '</div>';
		}

		if ( ! $is_podcast && ! $is_podcast_archive || ! $is_home ) {
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

	public static function filter_episode_content( $episode_id, $content ) {
		global $post;

		$old_post = $post;
		$episode  = get_post( $episode_id );
		$post     = $episode;
		$content  = apply_filters( 'the_secondary_content', $content );
		$post     = $old_post;

		return $content;
	}

}

GMR_Audio_Shortcodes::init();

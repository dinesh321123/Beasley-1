<?php

/**
 * Class GMLP_Player
 */
class GMR_Audio_Shortcodes {

	public static function init() {
		add_filter( 'wp_audio_shortcode', array( __CLASS__, 'custom_audio_styling' ), 10, 5 );
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

		if ( function_exists( 'wp_read_audio_metadata' ) ) {
			$fileinfo = parse_url( $mp3_src );
			$file_path = ABSPATH . $fileinfo['path'];
			$metadata = wp_read_audio_metadata( $file_path );
			$metadata = wp_parse_args( $metadata, $metadata_defaults );
		} else {
			$metadata = $metadata_defaults;
		}

		ob_start();

		$hash = md5( $mp3_src );
		$parent_podcast_id = wp_get_post_parent_id( $post_id );
		if( $parent_podcast_id ) {
			$parent_podcast = get_post( $parent_podcast_id );
			$itunes_url = get_post_meta( $parent_podcast_id, 'gmp_podcast_itunes_url', true );
		} else {
			$parent_podcast = false;
		}

		$series = get_post( $parent_podcast_id );
		$series_slug = $series->post_name;
		$feed_url = home_url( '/' ) . '?feed=podcast&podcast_series=' . $series_slug;

		$downloadable = get_post_meta( $post_id, 'gmp_audio_downloadable', true );
		$new_html = '';

		$new_html .= '<div class="podcast__play mp3-' . esc_attr( $hash ) . '">'; // Hash is used to ensure the inline audio can always match state of live player, even when the player is the buttons that are clicked
		$new_html .= '<button class="podcast__btn--play" data-mp3-src="' . esc_attr( $mp3_src ) . '" data-mp3-title="' . esc_attr( $metadata['title'] ) . '" data-mp3-artist="' . esc_attr( $metadata['artist'] ) . '" data-mp3-hash="' . esc_attr( $hash ) . '"></button>';
		$new_html .= '<button class="podcast__btn--pause"></button>';
		$new_html .= '<span class="podcast__runtime">' . esc_html( $metadata['length_formatted'] ) . '</span>';
		if( $downloadable == 'on' || $downloadable == '' ) {
			$new_html .= '<div class="podcast__download">';
			$new_html .= '<a href="' . esc_attr( $mp3_src ) . '" download="' . esc_attr( $mp3_src ) . '" class="podcast__download--btn" download>Download</a>';
			$new_html .= '</div>';
		}
		$new_html .= '</div>';
		$new_html .= '<div class="podcast__meta">';
		$new_html .= '<time class="podcast__date" datetime="' . get_the_time( 'c' ) . '">' . get_the_time( 'F d' ) . '</time>';
		$new_html .= '<h3 class="podcast__title">' . get_the_title() . '</h3>';
		if( $parent_podcast_id && is_singular( ShowsCPT::SHOW_CPT ) ) {
			$new_html .= '<div class="podcast__parent"><div class="podcast__parent--title">'. esc_html( $parent_podcast->post_title ) . '</div>';
			if( $itunes_url != '' ) {
				$new_html .= '<a class="podcast__subscribe" href="' . esc_url( $itunes_url ) . '" target="_blank">Subscribe</a>';
			}
			$new_html .= '<a class="podcast__rss" href="' . esc_url( $feed_url ) . '" target="_blank">Podcast RSS Feed</a>';
			$new_html .= '</div>';
		}
		$new_html .= '<div class="podcast__desc">' . get_the_excerpt() . '</div>' ;
		$new_html .= '</div>';
		$new_html .= '<div class="gmr-mediaelement-fallback">' . $html . '</div>';

		update_post_meta( $post_id, 'enclosure', esc_attr( $mp3_src ) );
		update_post_meta( $post_id, 'duration', esc_html( $metadata['length_formatted'] ) );

		//$new_html .= ob_get_clean();
		return $new_html;
	}

}

GMR_Audio_Shortcodes::init();

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
		?>
		<div class="podcast__play mp3-<?php echo esc_attr( $hash ); // Hash is used to ensure the inline audio can always match state of live player, even when the player is the buttons that are clicked ?>">
			<button class="podcast__btn--play" data-mp3-src="<?php echo esc_attr( $mp3_src );?>" data-mp3-title="<?php echo esc_attr( $metadata['title'] ); ?>" data-mp3-artist="<?php echo esc_attr( $metadata['artist'] ); ?>" data-mp3-hash="<?php echo esc_attr( $hash ); ?>"></button>
			<button class="podcast__btn--pause"></button>
			<span class="podcast__runtime"><?php echo esc_html( $metadata['length_formatted'] ); ?></span>
		</div>
		<div class="gmr-mediaelement-fallback"><?php echo $html; ?></div>

		<?php
		$new_html = ob_get_clean();
		return $new_html;
	}

}

GMR_Audio_Shortcodes::init();

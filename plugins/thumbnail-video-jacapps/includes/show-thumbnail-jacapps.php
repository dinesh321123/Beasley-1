<?php
/**
 * Class ShowThumbnailJacapps
 */
class ShowThumbnailJacapps {
    /**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
        if( self::ee_is_common_mobile_thumbnail() ) {
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        }
	}

    /**
	 * Enqueues scripts and styles.
	 */
	public static function enqueue_scripts() {
        wp_register_style('thumbnail-video-jacapps',TUMBNAIL_VIDEO_JACAPPS_URL . "assets/css/thumbnail_video.css", array(), TUMBNAIL_VIDEO_JACAPPS_VERSION, 'all');
        wp_enqueue_style('thumbnail-video-jacapps');
        wp_enqueue_script( 'thumbnail-video-jacapps', TUMBNAIL_VIDEO_JACAPPS_URL . "assets/js/thumbnail_video.js", array('jquery'), TUMBNAIL_VIDEO_JACAPPS_VERSION, true);
	}

    public static function ee_is_common_mobile_thumbnail() {
		static $jacapps_pos = null,
				$whiz_pos = null;

		if ( $jacapps_pos === null ) {
			$jacapps_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'jacapps' );

			// Allow way to toggle jacapps through URL querystring
			if ( isset( $_GET['jacapps'] ) ) {
				$jacapps_pos = 1;
			}
		}
		if($whiz_pos === null ) {
			$whiz_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'whiz' );

			// Allow way to toggle whiz through URL querystring
			if ( isset( $_GET['whiz'] ) ) {
				$whiz_pos = 1;
			}
		}
		return false !== $jacapps_pos || false !== $whiz_pos;
	}
}

ShowThumbnailJacapps::init();
<?php
/**
 * Class StnPlayerJacapps
 */
class StnPlayerJacapps {
    /**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
        if( self::ee_is_common_mobile_stn() ) {
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        }
	}

    /**
	 * Enqueues scripts and styles.
	 */
	public static function enqueue_scripts() {
        wp_register_style('stn-video-jacapps',STN_VIDEO_JACAPPS_URL . "assets/css/stn_video.css", array(), STN_VIDEO_JACAPPS_VERSION, 'all');
        wp_enqueue_style('stn-video-jacapps');
        wp_enqueue_script( 'stn-video-jacapps', STN_VIDEO_JACAPPS_URL . "assets/js/stn_video.js", array('jquery'), STN_VIDEO_JACAPPS_VERSION, true);
	}

    public static function ee_is_common_mobile_stn() {
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

StnPlayerJacapps::init();
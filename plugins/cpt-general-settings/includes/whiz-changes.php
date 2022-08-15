<?php
/**
 * Class WhizChanges
 */
class WhizChanges {
    /**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	function __construct()
	{
		$this->init();
	}
	public function init() {
		if( $this->ee_is_common_mobile_thumbnail() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        }
	}

    /**
	 * Enqueues scripts and styles.
	 */
	public static function enqueue_scripts() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
        wp_enqueue_script( 'general-whiz-script', GENERAL_SETTINGS_CPT_URL . "assets/js/whiz_script{$min}.js", array('jquery'), GENERAL_SETTINGS_CPT_VERSION, true);
        wp_enqueue_script( 'show-on-device', GENERAL_SETTINGS_CPT_URL . "assets/js/show_on_device{$min}.js",[],GENERAL_SETTGENERAL_SETTINGS_CPT_VERSION, true);
	}

    public function ee_is_common_mobile_thumbnail() {
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
			if ( isset( $_GET['Whiz'] ) ) {
				$whiz_pos = 1;
			}
		}
		return false !== $jacapps_pos || false !== $whiz_pos;
	}
}

// WhizChanges::init();
new WhizChanges();

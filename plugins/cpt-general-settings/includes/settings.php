<?php
/**
 * Class CommonSettings
 */
class CommonSettings {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'settings_cpt_init' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public static function allow_fontawesome_posttype_list() {
		return (array) apply_filters( 'allow-font-awesome-for-posttypes', array( 'listicle_cpt', 'affiliate_marketing' )  );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		
		if ( in_array( $typenow, CommonSettings::allow_fontawesome_posttype_list() ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_style('general-font-awesome',GENERAL_SETTINGS_CPT_URL . "assets/css/general-font-awesome". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
			wp_enqueue_style('general-font-awesome');
		}
	}
}

CommonSettings::init();

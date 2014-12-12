<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}


/**
 * must add `define( 'GREATER_MEDIA_GIGYA_TEST_UI', true );` to wp-config.php
 *
 * Class GreaterMediaGigyaTest
 */

class GreaterMediaGigyaTest {

	function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'gm_live_player_test_ui', array( $this, 'test_ui' ) );

	}

	public function wp_enqueue_scripts() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		if ( defined( 'GREATER_MEDIA_GIGYA_TEST_UI' ) && GREATER_MEDIA_GIGYA_TEST_UI && !is_page( 'style-guide' ) ) {
			wp_enqueue_script(
				'liveplayer-testing',
				get_template_directory_uri() . "/assets/js/liveplayer_test{$postfix}.js",
				array(
					'jquery'
				),
				false,
				false
			);
			wp_enqueue_script(
				'gigya-auth',
				get_template_directory_uri() . "/assets/js/liveplayer_test_auth{$postfix}.js",
				array(),
				false,
				false
			);
		}

	}

	public static function test_ui() {

		if ( defined( 'GREATER_MEDIA_GIGYA_TEST_UI' ) && GREATER_MEDIA_GIGYA_TEST_UI ) { ?>
			<div id="live-stream__test" class="live-stream__test" style="display:none;">
				<input type="checkbox" name="live-stream__test--audio" class="live-stream__test--audio" id="live-stream__test--audio" value="live-audio">
				<label for="live-stream__test--audio" class="live-stream__test--label"><?php _e( 'Logged In', 'greatermedia' ); ?></label>
			</div>
		<?php }

	}

	/**
	 * Check if the current listener is logged in
	 * @return bool
	 */
	public static function is_gigya_user_logged_in() {

		return isset( $_COOKIE[ 'gm_gigya_user' ] );

	}

	/**
	 * Get the current listener's Gigya user id
	 * @return null
	 */
	public static function gigya_user_id() {

		if ( ! self::is_gigya_user_logged_in() ) {
			return null;
		}

		$cookie_data = json_decode( $_COOKIE[ 'gm_gigya_user' ] );
		if ( ! $cookie_data ) {
			return null;
		}

		if ( isset( $cookie_data->UID ) ) {
			return $cookie_data->UID;
		}

		return null;

	}

}

$GreaterMediaGigyaTest = new GreaterMediaGigyaTest();
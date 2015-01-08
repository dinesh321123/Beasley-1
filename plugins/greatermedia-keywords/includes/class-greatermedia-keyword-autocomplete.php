<?php
/**
 * Created by Eduard
 * Date: 03.01.2015 1:30
 */

class GreaterMediaKeywordAutocomplete {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function enqueue_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'jquery-ui-autocomplete');

		wp_enqueue_script(
			GreaterMedia_Keyword_Admin::$plugin_slug . '-autocomplete-script'
			, GMKEYWORDS_URL . "assets/js/greatermedia_keywords_autocomplete{$postfix}.js"
			, array( 'jquery' )
			, GMKEYWORDS_VERSION
		);

		$keywords = GreaterMedia_Keyword_Admin::get_keyword_options( GreaterMedia_Keyword_Admin::$plugin_slug . '_option_name' );
		if( is_array( $keywords ) ) {
			$keywords_array = array();
			foreach( $keywords as $keyword ) {
				array_push( $keywords_array, $keyword['keyword'] );
			}
		}

		wp_localize_script(
			GreaterMedia_Keyword_Admin::$plugin_slug . '-autocomplete-script'
			, 'GMRKeywords'
			, $keywords_array
		);
	}

}

GreaterMediaKeywordAutocomplete:: init();
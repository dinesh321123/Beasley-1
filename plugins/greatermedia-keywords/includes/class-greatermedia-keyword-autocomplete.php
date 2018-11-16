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

		wp_register_script(
			'gmedia_keywords-autocomplete-script'
			, GMKEYWORDS_URL . "assets/js/greatermedia_keywords_autocomplete{$postfix}.js"
			, array( 'jquery', 'underscore' )
			, GMKEYWORDS_VERSION
		);

		$keyword_list = wp_cache_get( 'keyword_list', 'greater_media/keywords' );

		if ( !$keyword_list ) {
			$keywords = (array) GreaterMedia_Keyword_Admin::get_keyword_options( GreaterMedia_Keyword_Admin::$plugin_slug . '_option_name' );

			$keyword_list = array();

			// Generate the keyword list for use by the search script. Note that we
			// are getting fresh title and permalink data in case a post's title or
			// URL has changed since the last time the keywords were updated.
			foreach( $keywords as $keyword ) {
				$keyword_list[] = array(
					'keyword' => $keyword['keyword'],
					'id' => (int) $keyword['post_id'],
					'title' => get_the_title( $keyword['post_id'] ),
					'url' => get_permalink( $keyword['post_id'] ),
				);
			}

			// Cap the list for safety.
			$keyword_list = array_slice( $keyword_list, 0, 100 );

			wp_cache_set( 'keyword_list', $keyword_list, 'greater_media/keywords', GMKEYWORDS_LIST_CACHE_TTL );
		}

		wp_localize_script(
			GreaterMedia_Keyword_Admin::$plugin_slug . '-autocomplete-script'
			, 'GMRKeywords'
			, $keyword_list
		);
	}

}

GreaterMediaKeywordAutocomplete:: init();
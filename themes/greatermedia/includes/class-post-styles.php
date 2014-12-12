<?php

class GM_Post_Styles{

	public static function init() {
		add_filter( 'post_style_strings', array( __CLASS__, 'custom_post_style' ) );
	}

	/**
	 * @param $strings
	 *
	 * @return mixed
	 */
	public static function custom_post_style( $strings ) {
		$strings['calendar-event'] = _x( 'Calendar Event', 'greatermedia' );
		$strings['concert-event'] = _x( 'Concert Event', 'greatermedia' );
		$strings['contest'] = _x( 'Contest', 'greatermedia' );
		$strings['member-survey'] = _x( 'Member Survey', 'greatermedia' );
		$strings['live-webcam-stream'] = _x( 'Live Webcam Stream', 'greatermedia');
		$strings['news-article'] = _x( 'News Article', 'greatermedia' );
		$strings['traffic-map'] = _x( 'Traffic Map', 'greatermedia' );
		$strings['sports-calendar'] = _x( 'Sports Calendar', 'greatermedia' );
		$strings['anonymous-poll'] = _x( 'Anonymous Poll', 'greatermedia' );
		$strings['weather'] = _x( 'Weather', 'greatermedia');
		$strings['closings'] = _x( 'School &amp; Business Closings', 'greatermedia' );

		return $strings;
	}

}

GM_Post_Styles::init();
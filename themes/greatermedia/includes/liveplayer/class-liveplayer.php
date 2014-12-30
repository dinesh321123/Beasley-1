<?php

class GreaterMediaLivePlayer {

	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'render_live_player' ) );
	}

	public static function render_live_player() {
		if ( !is_page( 'style-guide' ) && apply_filters( 'load_greatermedia_livepress_sidebar', true ) ) {
			include __DIR__ . '/tpl.live-player.php';
		}
	}

}

GreaterMediaLivePlayer::init();
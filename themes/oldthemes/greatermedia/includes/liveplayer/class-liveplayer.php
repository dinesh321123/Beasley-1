<?php

class GreaterMediaLivePlayer {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_endpoint' ) );
		add_action( 'template_redirect', array( __CLASS__, 'process_onair_request' ) );
		add_action( 'gmr_live_audio_link', array( __CLASS__, 'live_audio_link' ) );
	}

	/**
	 * Registers on-air endpoint.
	 *
	 * @static
	 * @action init
	 * @access public
	 * @global WP $wp The WP object.
	 * @global WP_Rewrite $wp_rewrite The WP_Rewrite object.
	 */
	public static function register_endpoint() {
		global $wp, $wp_rewrite;

		// register blogroll query vars
		$wp->add_query_var( 'on-air' );

		// register rewrite rule
		$regex = '^on-air/?$';
		$wp_rewrite->add_rule( $regex, 'index.php?on-air=yes', 'top' );

		// flush rewrite rules if it doesn't contain blogroll endpoint
		$rules = $wp_rewrite->wp_rewrite_rules();
		if ( ! isset( $rules[ $regex ] ) ) {
			$wp_rewrite->flush_rules();
		}
	}


	/**
	 * Renders blogroll widget.
	 *
	 * @static
	 * @action template_redirect
	 * @access public
	 */
	public static function process_onair_request() {
		// do nothing if it is not a blogroll request
		if ( ! filter_var( get_query_var( 'on-air' ), FILTER_VALIDATE_BOOLEAN ) ) {
			return;
		}

		// send json error if shows plugin isn't activated
		if ( ! function_exists( 'gmrs_get_scheduled_episodes' ) ) {
			wp_send_json_error();
		}

		$schedule = array();

		$from = current_time( 'timestamp', 1 );
		$from = $from - $from % DAY_IN_SECONDS;
		$to = $from + 3 * DAY_IN_SECONDS;

		$from = date( DATE_ISO8601, $from );
		$to = date( DATE_ISO8601, $to );

		$days = gmrs_get_scheduled_episodes( $from, $to );
		foreach ( $days as $day_of_week => $episodes ) {
			foreach ( $episodes as $episode ) {
				if ( $episode->post_parent && ( $show = get_post( $episode->post_parent ) ) && ShowsCPT::SHOW_CPT == $show->post_type ) {
					$starts = strtotime( $episode->post_date_gmt );
					
					$schedule[] = array(
						'title'  => $show->post_title,
						'starts' => $starts,
						'ends'   => $starts + $episode->menu_order,
					);
				}
			}
		}

		wp_send_json_success( array(
			'tagline'  => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES ),
			'schedule' => $schedule,
		) );
		exit;
	}

	/**
	 * Returns the server location for live streaming
	 *
	 * @static
	 * @access public
	 *
	 * @return bool|mixed
	 */
	public static function live_stream_server() {

		$server = get_transient( 'gmr_livestream_server' );

		if ( false === $server ) {

			$xml = self::live_stream_endpoint();

			$server_loc = (string)$xml->mountpoints[0]->mountpoint[0]->servers->server->ip;

			if ( false === $server_loc ) {
				return false;
			}

			$server = set_transient( 'gmr_livestream_server', $server_loc, 30 * MINUTE_IN_SECONDS );

			if ( false === $server ) {
				return false;
			}
		}

		return $server;

	}

	/**
	 * Returns the mount for live streaming
	 *
	 * @static
	 * @access public
	 *
	 * @return bool|mixed
	 */
	public static function live_stream_mount() {

		$mount = get_transient( 'gmr_livestream_mount' );

		if ( false === $mount ) {

			$xml = self::live_stream_endpoint();

			$mount_point = (string)$xml->mountpoints[0]->mountpoint[0]->mount;

			if ( false === $mount_point ) {
				return false;
			}

			$mount = set_transient( 'gmr_livestream_mount', $mount_point, 30 * MINUTE_IN_SECONDS );

			if ( false === $mount ) {
				return false;
			}
		}

		return $mount;

	}

	/**
	 * Returns the xml endpoint as a string
	 *
	 * @static
	 * @access public
	 *
	 * @return SimpleXMLElement
	 */
	public static function live_stream_endpoint() {
		// So that we save this data if we call this function multiple times in one request
		static $data;

		if ( is_null( $data ) ) {
			$active_stream = gmr_streams_get_primary_stream_callsign();

			$xmlstr = "http://playerservices.streamtheworld.com/api/livestream?version=1.8&station={$active_stream}";

			$xml = wp_remote_retrieve_body( wp_remote_get( $xmlstr ) );

			$data = simplexml_load_string( $xml );
		}

		return $data;

	}

	/**
	 * Echos a full live streaming endpoint
	 *
	 * @static
	 * @access public
	 */
	public static function live_audio_link() {

		$ip = self::live_stream_server();

		$mount = self::live_stream_mount();

		if ( ! empty( $ip ) && ! empty( $mount ) ) {

			$endpoint = 'http://' . $ip . '/' . $mount . '.mp3?pname=TdPlayerApi&pversion=2.5';

			echo '<div class="live-audio">';

			echo '<a href="' . esc_url( $endpoint ) . '" class="live-audio__link">Listen Live</a>';

			echo '</div>';
		}

	}

}

GreaterMediaLivePlayer::init();

<?php

namespace Beasley\Media;

class Video extends \Beasley\Module {

	protected static $_accounts = array(
		'bbgi-philadelphia' => '27204544',
		'bbgi-boston'       => '27204552',
		'bbgi-detroit'      => '27204550',
		'bbgi-charlotte'    => '27204562',
		'bbgi-fayetteville' => '27204582',
		'bbgi'              => '27106536',
		'bbgi-nj'           => '27204560',
		'bbgi-augusta'      => '27204585',
		'bbgi-fort-myers'   => '27204580',
		'bbgi-tampa'        => '27204573',
		'bbgi-las-vegas'    => '27204579',
		'bbgi-wilmington'   => '27204589',
	);

	/**
	 * Registers current module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'init', $this( 'setup_embeds' ) );
		add_action( 'init', $this( 'setup_shortcodes' ) );
		add_action( 'beasley-register-settings', $this( 'register_settings' ), 10, 2 );
	}

	/**
	 * Registers embeds for livestream videos.
	 *
	 * @access public
	 * @action init
	 */
	public function setup_embeds() {
		wp_embed_register_handler(
			'livestream-video-id',
			'#https?://livestream.com/accounts/([^/]+)/events/([^/]+)/videos/([^/]+)/?#i',
			$this( 'account_id_embed_handler' )
		);

		wp_embed_register_handler(
			'livestream-video-name',
			'#https?://livestream.com/([^/]+)/events/([^/]+)/videos/([^/]+)/?#i',
			$this( 'account_name_embed_handler' )
		);
	}

	/**
	 * Registers shortcodes for livestream videos.
	 *
	 * @access public
	 * @action init
	 */
	public function setup_shortcodes() {
		add_shortcode( 'livestream_video', $this( 'shortcode_handler' ) );
	}

	/**
	 * Registers Livestream video settings.
	 *
	 * @access public
	 * @action beasley-register-settings
	 * @param string $group
	 * @param string $page
	 */
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_livestream_settings';

		add_settings_section( $section_id, 'Livestream', '__return_false', $page );

		add_settings_field( 'livestream_client_id', 'Client ID', 'beasley_input_field', $page, $section_id, 'name=livestream_client_id' );
		add_settings_field( 'livestream_secret_key', 'Secret Key', 'beasley_input_field', $page, $section_id, 'name=livestream_secret_key' );
		add_settings_field( 'livestream_ad_tag_url', 'Ad Tag URL', 'beasley_input_field', $page, $section_id, 'name=livestream_ad_tag_url' );

		register_setting( $group, 'livestream_client_id', 'sanitize_text_field' );
		register_setting( $group, 'livestream_secret_key', 'sanitize_text_field' );
		register_setting( $group, 'livestream_ad_tag_url', 'strip_tags' );
	}

	/**
	 * Renders embed code for livestream video URL with account id.
	 *
	 * @access public
	 * @param array $matches
	 * @return string
	 */
	public function account_id_embed_handler( $matches ) {
		$account_id = $matches[1];
		$event_id = $matches[2];
		$video_id = $matches[3];

		return $this->get_embed_code( $account_id, $event_id, $video_id );
	}

	/**
	 * Renders embed code for livestream video URL with account name.
	 *
	 * @access public
	 * @param array $matches
	 * @return string
	 */
	public function account_name_embed_handler( $matches ) {
		$account_id = isset( self::$_accounts[ $matches[1] ] )
			? self::$_accounts[ $matches[1] ]
			: false;

		if ( ! $account_id ) {
			return '';
		}

		$event_id = $matches[2];
		$video_id = $matches[3];

		return $this->get_embed_code( $account_id, $event_id, $video_id );
	}

	/**
	 * Renders embed code for livestream video shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public function shortcode_handler( $atts ) {
		if ( empty( $atts['account_id'] ) || empty( $atts['event_id'] ) || empty( $atts['video_id'] ) ) {
			return '';
		}

		return $this->get_embed_code( $atts['account_id'], $atts['event_id'], $atts['video_id'] );
	}

	/**
	 * Returns embed code for a Livestream video.
	 *
	 * @access public
	 * @param string $account_id
	 * @param string $event_id
	 * @param string $video_id
	 * @return string
	 */
	public function get_embed_code( $account_id, $event_id, $video_id ) {
		// do nothing if it is rendered before body
		if ( ! did_action( 'beasley_after_body' ) ) {
			return '';
		}

		$key = get_option( 'livestream_secret_key' );
		return ! empty( $key )
			? $this->_get_videojs_embed( $key, $account_id, $event_id, $video_id )
			: $this->_get_iframe_embed( $account_id, $event_id, $video_id );
	}

	/**
	 * Returns fallback iframe embed when Livestream secret key is not provided.
	 *
	 * @access protected
	 * @param string $account_id
	 * @param string $event_id
	 * @param string $video_id
	 * @return string
	 */
	protected function _get_iframe_embed( $account_id, $event_id, $video_id ) {
		$embed_id = $this->_get_embed_id();

		ob_start();

		?><div class="livestream livestream-oembed">
			<iframe
				id="<?php echo esc_attr( $embed_id ); ?>"
				src="//livestream.com/accounts/<?php echo esc_attr( $account_id ); ?>/events/<?php echo esc_attr( $event_id ); ?>/videos/<?php echo esc_attr( $video_id ); ?>/player?autoPlay=false&mute=false"
				frameborder="0" scrolling="no" allowfullscreen>
			</iframe>
			<script
				type="text/javascript"
				data-embed_id="<?php echo esc_attr( $embed_id ); ?>"
				src="//livestream.com/assets/plugins/referrer_tracking.js">
			</script>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Returns embed code to use with videojs.
	 *
	 * @access protected
	 * @param string $key
	 * @param string $account_id
	 * @param string $event_id
	 * @param string $video_id
	 * @return string
	 */
	protected function _get_videojs_embed( $key, $account_id, $event_id, $video_id ) {
		$json = \Beasley\Cache::get( func_get_args(), function() use ( $key, $account_id, $event_id, $video_id ) {
			$response = wp_remote_get( "https://{$key}:@livestreamapis.com/v3/accounts/{$account_id}/events/{$event_id}/videos/{$video_id}" );

			return ! is_wp_error( $response )
				? wp_remote_retrieve_body( $response )
				: '';
		}, DAY_IN_SECONDS );

		if ( empty( $json ) ) {
			return '';
		}

		$json = json_decode( $json, true );
		if ( empty( $json['m3u8'] ) ) {
			return '';
		}

		$timestamp = round( microtime( true ) * 1000 );
		$source = add_query_arg( array(
			'timestamp' => $timestamp,
			'clientId'  => get_option( 'livestream_client_id' ),
			'token'     => hash_hmac( "md5", "{$key}:playback:{$timestamp}", $key ),
		), $json['m3u8'] );

		return sprintf(
			'<div class="livestream livestream-oembed" data-ad-tag="%s">' .
				'<video id="%s" class="video-js vjs-default-skin" controls preload="auto" poster="%s" data-src="%s"></video>' .
			'</div>',
			esc_attr( $this->get_ad_tag_url( $event_id, $video_id ) ),
			esc_attr( $this->_get_embed_id() ),
			! empty( $json['thumbnailUrl'] ) ? esc_attr( $json['thumbnailUrl'] ) : '',
			esc_url_raw( $source )
		);
	}

	/**
	 * Returns id attribute for Livestream video.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _get_embed_id() {
		return 'ls_embed_' . rand( 1, getrandmax() );
	}

	/**
	 * Returns adTagUrl for a video.
	 *
	 * @see https://support.google.com/admanager/answer/1068325?hl=en
	 *
	 * @access public
	 * @param string $event_id
	 * @param string $video_id
	 * @return string
	 */
	public function get_ad_tag_url( $event_id, $video_id ) {
		$tagUrl = trim( get_option( 'livestream_ad_tag_url' ) );
		if ( empty( $tagUrl ) ) {
			return '';
		}

		$cust_params = array();

		if ( ! empty( $event_id ) ) {
			$cust_params[] = 'livestreameventid=' . urlencode( $event_id );
		}

		if ( ! empty( $video_id ) ) {
			$cust_params[] = 'livestreamvideoid=' . urlencode( $video_id );
		}

		foreach ( greatermedia_get_global_targeting() as $targeting ) {
			if ( is_array( $targeting ) && count( $targeting ) == 2 ) {
				$cust_params[] = sprintf( '%s=%s', $targeting[0], urlencode( $targeting[1] ) );
			}
		}

		$cust_params = implode( '&', $cust_params );
		$cust_params = urlencode( $cust_params );

		if ( ! filter_var( $tagUrl, FILTER_VALIDATE_URL ) ) {
			$network_id = trim( get_option( 'dfp_network_code' ) );
			$tagUrl = add_query_arg( array(
				'env'                     => 'vp',
				'gdfp_req'                => '1',
				'unviewed_position_start' => '1',
				'output'                  => 'vast',
				'gdfp_req'                => '1',
				'correlator'              => '',
				'ad_rule'                 => '0',
				'sz'                      => '920x508',
				'cmsid'                   => urlencode( $event_id ),
				'vid'                     => urlencode( $video_id ),
				'iu'                      => "/{$network_id}/{$tagUrl}",
			), 'https://pubads.g.doubleclick.net/gampad/live/ads' );
		}

		return add_query_arg( 'cust_params', $cust_params, $tagUrl );
	}

}

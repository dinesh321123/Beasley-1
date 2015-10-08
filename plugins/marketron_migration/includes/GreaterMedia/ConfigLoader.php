<?php

namespace GreaterMedia;

class ConfigLoader {

	public $container;

	function get_config() {
		return $this->container->config;
	}

	function get_config_option( $type, $key = null ) {
		return $this->get_config()->get_config_option( $type, $key );
	}

	function load() {
		$this->load_options();
	}

	function prepare() {
		$dirs = $this->get_required_dirs();

		foreach ( $dirs as $dir ) {
			if ( ! file_exists( $dir ) )  {
				mkdir( $dir, 0700, true );
				\WP_CLI::success( "Created Directory: $dir" );
			}
		}
	}

	function get_required_dirs() {
		$config = $this->container->config;

		return array(
			$config->get_input_dir(),
			$config->get_output_dir(),
			$config->get_csv_export_dir(),
			$config->get_marketron_files_dir(),
			$config->get_backups_dir(),
			$config->get_upload_backups_dir(),
		);
	}

	function load_options() {
		$this->load_gigya_options();
		$this->load_myemma_options();
		$this->load_livefyre_options();
		$this->load_ooyala_options();
		$this->load_member_page_text_options();
		$this->load_google_analytics_options();
		$this->load_social_page_options();
		$this->load_embedly_options();
	}

	function load_gigya_options() {
		if ( ! $this->get_config()->has_config_option( 'gigya' ) ) {
			return;
		}

		$api_key    = $this->get_config_option( 'gigya', 'api_key' );
		$secret_key = $this->get_config_option( 'gigya', 'secret_key' );

		$gigya_options = get_option( 'member_query_settings', '{}' );
		$gigya_options = json_decode( $gigya_options, true );

		$gigya_options['gigya_api_key']    = $api_key;
		$gigya_options['gigya_secret_key'] = $secret_key;
		$gigya_options                     = json_encode( $gigya_options );

		update_option( 'member_query_settings', $gigya_options );

		\WP_CLI::success( 'Updated Gigya Options' );
	}

	function load_livefyre_options() {
		if ( ! $this->get_config()->has_config_option( 'livefyre' ) ) {
			return;
		}

		$network_name = $this->get_config_option( 'livefyre', 'network_name' );
		$network_key  = $this->get_config_option( 'livefyre', 'network_key' );
		$site_id      = $this->get_config_option( 'livefyre', 'site_id' );
		$site_key     = $this->get_config_option( 'livefyre', 'site_key' );

		$livefyre_options = get_option( 'livefyre_settings', '{}' );
		$livefyre_options = json_decode( $livefyre_options, true );

		$livefyre_options['network_name'] = $network_name;
		$livefyre_options['network_key']  = $network_key;
		$livefyre_options['site_id']      = $site_id;
		$livefyre_options['site_key']     = $site_key;

		$livefyre_options = json_encode( $livefyre_options );
		update_option( 'livefyre_settings', $livefyre_options );

		\WP_CLI::success( 'Updated LiveFyre Options' );
	}

	function load_myemma_options() {
		if ( ! $this->get_config()->has_config_option( 'myemma' ) ) {
			return;
		}

		$account_id         = $this->get_config_option( 'myemma', 'account_id' );
		$public_key         = $this->get_config_option( 'myemma', 'public_key' );
		$private_key        = $this->get_config_option( 'myemma', 'private_key' );
		$webhook_auth_token = $this->get_config_option( 'myemma', 'webhook_auth_token' );

		$emma_options = get_option( 'member_query_settings', '{}' );
		$emma_options = json_decode( $emma_options, true );

		$emma_options['emma_account_id']         = $account_id;
		$emma_options['emma_public_key']         = $public_key;
		$emma_options['emma_private_key']        = $private_key;
		$emma_options['emma_webhook_auth_token'] = $webhook_auth_token;
		$emma_options                            = json_encode( $emma_options );

		update_option( 'member_query_settings', $emma_options );

		\WP_CLI::success( 'Updated MyEmma Options' );
	}

	function load_myemma_groups( $save = false ) {
		if ( ! $this->get_config()->has_config_option( 'myemma' ) ) {
			return;
		}

		$newsletters = $this->get_config()->get_newsletters();
		$groups      = array();

		foreach ( $newsletters as $newsletter ) {
			$group = array(
				'group_id'          => $newsletter['emma_group_id'],
				'group_name'        => $newsletter['emma_name'],
				'field_key'         => $newsletter['gigya_field_key'],
			);

			if ( ! empty( $newsletter['description'] ) ) {
				$group['group_description'] = $newsletter['description'];
			} else {
				$group['group_description'] = '';
			}

			$group['group_active'] = $newsletter['active'];
			$groups[] = $group;
		}

		if ( $save ) {
			$groups = json_encode( $groups );
			update_option( 'emma_groups', $groups );

			\WP_CLI::success( 'Updated MyEmma Groups' );
		}
	}

	function load_ooyala_options() {
		if ( ! $this->get_config()->has_config_option( 'ooyala' ) ) {
			return;
		}

		require_once( GMEDIA_PATH . '/../ooyala-video-browser/OoyalaApi.php' );

		$api_key        = $this->get_config_option( 'ooyala', 'api_key' );
		$secret_key     = $this->get_config_option( 'ooyala', 'api_secret' );
		$video_status   = $this->get_config_option( 'ooyala', 'video_status' );
		$partner_code   = $this->get_config_option( 'ooyala', 'partner_code' );
		$partner_secret = $this->get_config_option( 'ooyala', 'partner_secret' );

		$ooyala_options = get_option( 'ooyala' );
		if ( $ooyala_options === false ) {
			$ooyala_options = array();
		}

		$ooyala_options['api_key']        = $api_key;
		$ooyala_options['api_secret']     = $secret_key;
		$ooyala_options['video_status']   = $video_status;
		$ooyala_options['partner_code']   = $partner_code;
		$ooyala_options['partner_secret'] = $partner_secret;

		$ooyala_api = new \OoyalaApi( $api_key, $secret_key );
		$players    = $ooyala_api->get( 'players' );

		$ooyala_options['players'] = array();
		foreach ( $players->items as $player ) {
			$ooyala_options['players'][] = $player->id;
		}

		$ooyala_options['player_id'] = $ooyala_options['players'][0];

		update_option( 'ooyala', $ooyala_options );

		\WP_CLI::success( 'Updated Ooyala Options' );
	}

	function load_live_streams() {
		if ( ! $this->get_config()->has_config_option( 'live_player' ) ) {
			return;
		}

		$live_streams = $this->get_config_option( 'live_player', 'streams' );
		$total        = 0;

		foreach ( $live_streams as $live_stream ) {
			$existing_post = get_page_by_title( $live_stream['call_sign'], ARRAY_A, 'live-stream' );
			if ( ! is_null( $existing_post ) ) {
				continue;
			}

			$post = array(
				'post_type'   => 'live-stream',
				'post_title'  => $live_stream['call_sign'],
				'post_status' => 'publish',
			);

			$post_id = wp_insert_post( $post );

			update_post_meta( $post_id, 'call_sign', $live_stream['call_sign'] );
			update_post_meta( $post_id, 'description', $live_stream['description'] );
			update_post_meta( $post_id, 'vast_url', $live_stream['vast_url'] );

			$total++;
		}

		\WP_CLI::success( "Imported $total Live Stream(s)" );
	}

	function load_member_page_text_options() {
		if ( ! $this->get_config()->has_config_option( 'member_page_text' ) ) {
			return;
		}

		$member_page_text = $this->get_config_option( 'member_page_text' );

		update_option( 'gmr_join_page_heading', $member_page_text['join']['heading'] );
		update_option( 'gmr_join_page_message', $member_page_text['join']['message'] );

		update_option( 'gmr_login_page_heading', $member_page_text['login']['heading'] );
		update_option( 'gmr_login_page_message', $member_page_text['login']['message'] );

		update_option( 'gmr_logout_page_heading', $member_page_text['logout']['heading'] );
		update_option( 'gmr_logout_page_message', $member_page_text['logout']['message'] );

		update_option( 'gmr_password_page_heading', $member_page_text['forgot-password']['heading'] );
		update_option( 'gmr_password_page_message', $member_page_text['forgot-password']['message'] );

		update_option( 'gmr_account_page_heading', $member_page_text['account']['heading'] );
		update_option( 'gmr_account_page_message', $member_page_text['account']['message'] );

		update_option( 'gmr_cookies_page_heading', $member_page_text['cookies-required']['heading'] );
		update_option( 'gmr_cookies_page_message', $member_page_text['cookies-required']['message'] );

		\WP_CLI::success( 'Updated Member Page Text Options' );
	}

	function load_google_analytics_options() {
		if ( ! $this->get_config()->has_config_option( 'google_analytics' ) ) {
			return;
		}

		$id        = $this->get_config_option( 'google_analytics', 'id' );
		$dimension = $this->get_config_option( 'google_analytics', 'dimension' );

		update_option( 'gmr_google_analytics', $id );
		update_option( 'gmr_google_uid_dimension', $dimension );

		\WP_CLI::success( 'Updated Google Analytics Options' );
	}

	function load_social_page_options() {
		if ( ! $this->get_config()->has_config_option( 'social_pages' ) ) {
			return;
		}

		$facebook  = $this->get_config_option( 'social_pages', 'facebook' );
		$twitter   = $this->get_config_option( 'social_pages', 'twitter' );
		$youtube   = $this->get_config_option( 'social_pages', 'youtube' );
		$instagram = $this->get_config_option( 'social_pages', 'instagram' );

		update_option( 'gmr_facebook_url', $facebook );
		update_option( 'gmr_twitter_name', $twitter );
		update_option( 'gmr_youtube_url', $youtube );
		update_option( 'gmr_instagram_name', $instagram );

		\WP_CLI::success( 'Updated Social Page Options' );
	}

	function load_embedly_options() {
		if ( ! $this->get_config()->has_config_option( 'embedly' ) ) {
			return;
		}

		global $wpdb;
		$settings = array(
			'table'  => $wpdb->prefix . 'embedly_providers',
			'active' => true,
			'key'    => $this->get_config_option( 'embedly', 'api_key' ),
		);

		update_option( 'embedly_settings', $settings );

		\WP_CLI::success( 'Updated Embedly Options' );
	}

}

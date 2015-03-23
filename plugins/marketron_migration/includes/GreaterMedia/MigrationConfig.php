<?php

namespace GreaterMedia;

class MigrationConfig {

	public $container;
	public $input_dir;
	public $output_dir;
	public $site_dir;
	public $data;
	public $asset_dirs;
	public $data_files;
	public $myemma_newsletters;
	public $marketron_newsletters;

	function __construct( $site_dir ) {
		$this->site_dir   = $site_dir;
		$this->input_dir  = $site_dir . '/input';
		$this->output_dir = $site_dir . '/output';
	}

	function get_config_file() {
		return $this->input_dir . '/config.json';
	}

	function get_site_dir() {
		return $this->container->opts['site_dir'];
	}

	function get_mapping_file() {
		return $this->input_dir . '/mapping.csv';
	}

	function get_tags_file() {
		return $this->input_dir . '/tags.csv';
	}

	function get_member_ids_file() {
		return $this->input_dir . '/' . $this->get_site_option( 'member_ids_file' );
	}

	// DEPRECATED, there are multiple exports
	function get_marketron_export_file() {
		return $this->input_dir . '/marketron_export.zip';
	}

	function get_marketron_export_extract_dir() {
		return $this->output_dir . '/marketron_export';
	}

	function get_csv_export_dir() {
		return $this->output_dir . '/csv';
	}

	function get_import_script_file() {
		return $this->output_dir . '/import.sh';
	}

	function get_gigya_action_export_file() {
		return $this->output_dir . '/gigya_actions.json';
	}

	function get_gigya_profile_export_file() {
		return $this->output_dir . '/gigya_profiles.json';
	}

	function get_gigya_account_export_file() {
		return $this->output_dir . '/gigya_accounts.json';
	}

	function get_attachments_log_file() {
		return $this->output_dir . '/attachments.log';
	}

	function get_site_domain() {
		return $this->data['site']['domain'];
	}

	function get_email_domain() {
		return $this->data['site']['email_domain'];
	}

	function get_seed_id() {
		return $this->data['database']['seed_id'];
	}

	function get_site_option( $name ) {
		return $this->get_config_option( 'site', $name );
	}

	function get_error_option( $name ) {
		return $this->get_config_option( 'error', $name );
	}

	function get_config_option( $parent, $name = null ) {
		if ( is_null ( $name ) ) {
			return $this->data[ $parent ];
		}

		if ( array_key_exists( $name, $this->data[ $parent ] ) ) {
			return $this->data[ $parent ][ $name ];
		} else {
			\WP_CLI::error( "Error: Unknown config option - $name" );
		}
	}

	function get_asset_dirs() {
		if ( is_null( $this->asset_dirs ) ) {
			$asset_dirs = $this->data['site']['asset_dirs'];
			$site_dir   = $this->get_site_dir();
			$dirs       = array();

			foreach ( $asset_dirs as $asset_dir ) {
				$dirs[] = $site_dir . '/marketron_files/' . $asset_dir;
			}

			$this->asset_dirs = $dirs;
		}

		return $this->asset_dirs;
	}

	function get_data_files() {
		$data_files_map = $this->get_site_option( 'data_files' );
		return array_keys( $data_files_map );
	}

	function get_data_file_dirs_for_tool( $name ) {
		$data_files = $this->get_site_option( 'data_files' );
		$dirs       = array();
		$site_dir   = $this->get_site_dir();

		foreach ( $data_files as $data_file => $tools ) {
			if ( in_array( $name, $tools ) ) {
				$data_file_dirname = basename( $data_file, '.zip' );
				$data_file_dir     = $site_dir . '/marketron_files/' . $data_file_dirname;
				$dirs[]            = $data_file_dir;
			}
		}

		return $dirs;
	}

	function get_myemma_newsletters() {
		if ( is_null( $this->myemma_newsletters ) ) {
			$this->myemma_newsletters    = $this->get_config_option( 'myemma', 'newsletters' );
		}

		return $this->myemma_newsletters;
	}

	function has_newsletter( $marketron_name ) {
		return array_key_exists( $marketron_name, $this->newsletters );
	}

	function get_newsletter( $marketron_name ) {
		return $this->newsletters[ $marketron_name ];
	}

	function get_newsletter_id( $marketron_name ) {
		return $this->newsletters[ $marketron_name ]['emma_group_id'];
	}

	function get_newsletters() {
		return $this->newsletters;
	}

	function update_newsletter( $marketron_name, $description ) {
		if ( $this->has_newsletter( $marketron_name ) ) {
			$this->newsletters[ $marketron_name ]['description'] = $description;
		}
	}

	function load() {
		$config_file = $this->get_config_file();
		\WP_CLI::log( "Loading Config File: $config_file" );

		if ( file_exists( $config_file ) ) {
			$json       = file_get_contents( $config_file, 'r' );
			$this->data = $this->parse( $json );
			$this->newsletters = array();

			foreach ( $this->get_config_option( 'myemma', 'newsletters' ) as $newsletter ) {
				$marketron_name = $newsletter['marketron_name'];
				$this->newsletters[ $marketron_name ] = $newsletter;
			}
		} else {
			\WP_CLI::error( "Config File not found - $config_file" );
			return false;
		}
	}

	function parse( $json ) {
		$data = json_decode( $json, true );

		if ( json_last_error() === JSON_ERROR_NONE ) {
			return $data;
		} else {
			\WP_CLI::error( 'Failed to parse config JSON' );
			return false;
		}
	}

}

<?php
/*
 * Plugin Name: Show Network Domains
 * Description: Shows the domain for each site in the sites list table
 * Version: 1.0
 * Author: 10up
 * Author URI: http://10up.com
 */

/**
 * Show the site domain in the network sites list.
 *
 * Useful to tell what site is what, when site paths are all '/' and mapped to a specific domain on the network
 */
class CMM_Domain_In_Network {

	public static function init() {
		add_filter( 'wpmu_blogs_columns', array( __CLASS__, 'filter_wpmu_blogs_columns' ) );
		add_action( 'manage_sites_custom_column', array( __CLASS__, 'action_manage_sites_custom_column' ), 10, 2 );
	}

	public static function filter_wpmu_blogs_columns( $columns ) {
		$first = array_slice( $columns, 0, 2 );
		$columns = array_merge( $first, array( 'domain' => 'Domain' ), $columns );

		return $columns;
	}

	public static function action_manage_sites_custom_column( $column, $blog_id ) {
		if ( 'domain' !== $column ) {
			return;
		}

		$details = get_blog_details( $blog_id );
		echo esc_html( $details->domain );
	}

}

CMM_Domain_In_Network::init();

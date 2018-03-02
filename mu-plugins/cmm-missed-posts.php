<?php
/**
 * Plugin Name: Publish Missed Posts
 * Description: WP-CLI Command that publishes any posts that have missed their schedule. In a multisite environment, all sites are iterated over.
 * Author: Chris Marslender
 * Author URI: https://chrismarslender.com/
 */
/*
 * WP CLI Commands to publish missed schedule posts. Will publish posts of ANY post type, so use wisely.
 *
 * For a single site, run the following command:
 * `wp missed-posts publish`
 *
 * For a multisite, run one of the following commands:
 *
 * `wp missed-posts publish --url=<url>` - Use the `<url>` placeholder to only run one "blog"
 * `wp missed-posts publish-all-sites` - Publishes missed posts on ALL sites in the multisite
https://gist.github.com/cmmarslender/db2d29f35a56de3ad96ebc5630d89af5
 */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

class CMM_Missed_Posts {

	/**
	 * Publishes up to 100 missed posts on the current site.
	 *
	 * For a single site install, this will operate on the main site.
	 * In a multisite context, you can select a site with the --url flag. To publish all missed schedule posts on all
	 * sites, use the publish-all-sites subcommand
	 */
	public function publish( $args, $assoc_args ) {
		global $wpdb;

		$dry_run = isset( $assoc_args['dry-run'] ) && $assoc_args['dry-run'] ? true : false;

		WP_CLI::warning( "Finding posts that were scheduled to be published on or before " . current_time( 'mysql', 0 ) );

		$query = $wpdb->prepare( "select ID from {$wpdb->posts} where ( post_date > 0 && post_date <= %s ) AND post_status = 'future' LIMIT 0,100", current_time( 'mysql', 0 ) );

		$ids = $wpdb->get_col( $query );

		// Just in case there are any weird values
		$ids = array_filter( $ids );

		if ( empty( $ids ) ) {
			WP_CLI::success( "There are no posts that missed their schedule to publish" );
			return;
		}

		$total = count( $ids );

		WP_CLI::line( "Found {$total} missed schedule posts to publish" );

		foreach( $ids as $id ) {
			if ( $dry_run ) {
				$post = get_post( $id );
				WP_CLI::line( "- Found Post {$id}: {$post->post_title}");
			} else {
				wp_publish_post( $id );
				WP_CLI::line( " - Published Post {$id}" );
			}
		}

		WP_CLI::success( "Finished processing missing posts for current site" );
	}

	/**
	 * @subcommand publish-all-sites
	 */
	public function publish_all_sites( $args, $assoc_args ) {
		if ( ! is_multisite() ) {
			WP_CLI::error( "This is not a multisite. Use `wp missed-posts publish` instead to publish only a single site" );
		}

		$dry_run = isset( $assoc_args['dry-run'] ) && $assoc_args['dry-run'] ? true : false;

		// get sites and iterate
		$per_page = 100;
		$current_page = 0;

		do {
			$args = array(
				'number'            => $per_page,
				'offset'            => ( $per_page * $current_page ),
				'order'             => 'ASC',
				'orderby'           => 'id',
				'fields'            => 'ids',
				'count'             => false,
				'no_found_rows'     => false,
			);

			$site_query = new WP_Site_Query( $args );

			if ( $site_query ) {
				foreach ( $site_query->sites as $site ) {
					switch_to_blog( $site );
					WP_CLI::line( "Processing Site (Blog) ID: $site" );

					// Get a bit more useful info from the DB if running on a dry run
					if ( $dry_run ) {
						$url = get_option( 'siteurl' );
						WP_CLI::line( "Site URL is: {$url}" );
					}

					$this->publish( $args, $assoc_args );
				}
			}

			$current_page++;
		} while ( $site_query->max_num_pages > $current_page );

		WP_CLI::success( "Finished processing missing posts for all sites" );
	}

	/**
	 * @param $args
	 * @param $assoc_args
	 *
	 * @subcommand move-to-draft
	 * @synopsis --before-date=<before-date> [--dry-run]
	 */
	public function move_to_draft( $args, $assoc_args ) {
		global $wpdb;

		$dry_run = isset( $assoc_args['dry-run'] ) && $assoc_args['dry-run'] ? true : false;

		$before_date = $assoc_args['before-date'];

		$query = $wpdb->prepare( "select ID from {$wpdb->posts} where ( post_date > 0 && post_date <= %s ) AND post_status = 'future' LIMIT 0,100", date( 'Y-m-d H:i:s', strtotime( $before_date ) ) );

		$ids = $wpdb->get_col( $query );

		// Just in case there are any weird values
		$ids = array_filter( $ids );

		if ( empty( $ids ) ) {
			WP_CLI::success( "There are no posts that missed their schedule before the specified date" );
			return;
		}

		$total = count( $ids );

		WP_CLI::line( "Found {$total} missed schedule posts to move to draft status" );

		foreach( $ids as $id ) {
			$post = get_post( $id );
			if ( $dry_run ) {
				WP_CLI::line( "- Found Post {$id}: {$post->post_title}");
			} else {
				wp_update_post( array(
					'ID' => $id,
					'post_status' => 'draft',
				) );
				WP_CLI::line( " - Moved Post {$id} to draft" );
			}
		}
		WP_CLI::success( "Finished processing missing posts for current site" );
	}

	/**
	 * @param $args
	 * @param $assoc_args
	 *
	 * @subcommand move-all-sites-to-draft
	 * @synopsis --before-date=<before-date> [--dry-run]
	 */
	public function move_all_sites_to_draft( $args, $assoc_args ) {
		if ( ! is_multisite() ) {
			WP_CLI::error( "This is not a multisite. Use `wp missed-posts move-to-draft` instead to process only a single site" );
		}
		$dry_run = isset( $assoc_args['dry-run'] ) && $assoc_args['dry-run'] ? true : false;
		// get sites and iterate
		$per_page = 100;
		$current_page = 0;
		do {
			$args = array(
				'number'            => $per_page,
				'offset'            => ( $per_page * $current_page ),
				'order'             => 'ASC',
				'orderby'           => 'id',
				'fields'            => 'ids',
				'count'             => false,
				'no_found_rows'     => false,
			);
			$site_query = new WP_Site_Query( $args );
			if ( $site_query ) {
				foreach ( $site_query->sites as $site ) {
					switch_to_blog( $site );
					WP_CLI::line( "Processing Site (Blog) ID: $site" );
					// Get a bit more useful info from the DB if running on a dry run
					if ( $dry_run ) {
						$url = get_option( 'siteurl' );
						WP_CLI::line( "Site URL is: {$url}" );
					}
					$this->move_to_draft( $args, $assoc_args );
				}
			}
			$current_page++;
		} while ( $site_query->max_num_pages > $current_page );

		WP_CLI::success( "Finished processing posts for all sites" );
	}
}

WP_CLI::add_command( 'missed-posts', 'CMM_Missed_Posts' );

<?php
/**
 * Plugin Name: Prune Old Content
 * Description: WP-CLI Command used to delete old content for local development
 * Author: Chris Marslender
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

class CMM_Prune_Old_Content {

	/**
	 * Prunes content from the database older than the supplied date
	 * @subcommand prune
	 * @synopsis <date>
	 */
	public function prune( $args, $assoc_args ) {
		global $wpdb;

		if ( ! defined( 'LOCAL_ENV' ) || ! LOCAL_ENV ) {
			WP_CLI::error( "You must have `LOCAL_ENV` defined as `true` in wp-config in order to use this command" );
		}

		$before_date = reset($args);

		WP_CLI::log("Pruning content older than {$before_date}");

		$site_ids = $wpdb->get_results( "select blog_id from wp_blogs order by blog_id asc;" );
		$site_ids = wp_list_pluck( $site_ids, 'blog_id' );

		foreach( $site_ids as $site_id ) {
			if ( $site_id == 1 ) {
				continue;
			}
			WP_CLI::log( "Processing Site ID {$site_id}" );

			WP_CLI::log( " - Deleting from post meta" );
			$count = $wpdb->query( "delete from `wp_{$site_id}_postmeta` where post_id IN ( select ID from wp_{$site_id}_posts where post_date < \"$before_date\" order by ID desc );" );
			WP_CLI::log( " - - Deleted {$count} items" );

			WP_CLI::log( " - Deleting from term_relationships" );
			$count = $wpdb->query( "delete from `wp_{$site_id}_term_relationships` where object_id IN ( select ID from wp_{$site_id}_posts where post_date < \"$before_date\" order by ID desc );" );
			WP_CLI::log( " - - Deleted {$count} items" );

			WP_CLI::log( " - Deleting from yoast_seo_links" );
			$count = $wpdb->query( "delete from `wp_{$site_id}_yoast_seo_links` where post_id IN ( select ID from wp_{$site_id}_posts where post_date < \"$before_date\" order by ID desc );" );
			WP_CLI::log( " - - Deleted {$count} items" );

			WP_CLI::log( " - Deleting from yoast_seo_meta" );
			$count = $wpdb->query( "delete from `wp_{$site_id}_yoast_seo_meta` where object_id IN ( select ID from wp_{$site_id}_posts where post_date < \"$before_date\" order by ID desc );" );
			WP_CLI::log( " - - Deleted {$count} items" );

			WP_CLI::log( " - Deleting from wp_posts" );
			$count = $wpdb->query( "delete from `wp_{$site_id}_posts` where post_date < \"$before_date\";" );
			WP_CLI::log( " - - Deleted {$count} items" );
		}

		WP_CLI::success( "FINISHED!" );
	}
}

WP_CLI::add_command( 'prune-content', 'CMM_Prune_Old_Content' );


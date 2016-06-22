<?php

// do nothing if WP_CLI is not defined
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

WP_CLI::add_command( 'gmr-db', 'GMR_DB_Upgrade' );

class GMR_DB_Upgrade extends WP_CLI_Command {

	/**
	 * Upgrade tables to utf8mb4.
	 *
	 * ## OPTIONS
	 *
	 * --url=<url>
	 * : The URL of the WordPress site
	 *
	 * ## EXAMPLES
	 *
	 *   wp gmr-db utf-upgrade --url=http://wmgk.gmr.dev
	 *
	 * @subcommand utf-upgrade
	 * @synopsis --url=<url> [--live]
	 */
	public function utf8mb4_upgrade( $args, $assoc_args ) {
		$dry_run = (bool) ! isset( $assoc_args['live'] );

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if ( $dry_run ) {
			WP_CLI::line( 'Dry run...' );
		}

		if ( 'utf8mb4' === $wpdb->charset ) {

			$blog_id = get_current_blog_id();

			$tables = $wpdb->tables( 'blog' );

			foreach ( $tables as $table ) {
				WP_ClI::line( sprintf( 'Upgrading table %s on site %d', $table, $blog_id ) );

				if ( ! $dry_run ) {
					$result = maybe_convert_table_to_utf8mb4( $table );
					if ( false === $result ) {
						WP_CLI::error( sprintf( 'Failed to upgrade table %s', $table ) );
					} else {
						WP_CLI::success( sprintf( 'Upgrade success on table %s', $table ) );
					}
					WP_CLI::line( '' );
				}
			}

			WP_CLI::success( 'Finished' );
		}
	}
}
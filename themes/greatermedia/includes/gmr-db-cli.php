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

		if ( 'utf8mb4' === $wpdb->charset || 'utf8' === $wpdb->charset ) {

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

	/**
	 * Convert tables to utf8. Mostly used for testing, before running utf8mb4_upgrade().
	 *
	 * ## OPTIONS
	 *
	 * --url=<url>
	 * : The URL of the WordPress site
	 *
	 * ## EXAMPLES
	 *
	 *   wp gmr-db convert-to-utf --url=http://wmgk.gmr.dev
	 *
	 * @subcommand convert-to-utf
	 * @synopsis --url=<url> [--live]
	 */
	public function convert_to_utf8( $args, $assoc_args ) {
		$dry_run = (bool) ! isset( $assoc_args['live'] );

		global $wpdb;

		if ( $dry_run ) {
			WP_CLI::line( 'Dry run...' );
		}

		WP_CLI::line( 'Converting tables to utf8_unicode_ci' );

		$blog_id = get_current_blog_id();

		$tables = $wpdb->tables( 'blog' );

		foreach ( $tables as $table ) {
			WP_ClI::line( sprintf( 'Upgrading table %s on site %d', $table, $blog_id ) );

			if ( ! $dry_run ) {
				$result = $this->maybe_convert_table_to_utf8( $table );
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

	/**
	 * Convert a table to utf8.
	 *
	 * @global wpdb  $wpdb
	 *
	 * @param string $table The table to convert.
	 * @return bool true if the table was converted, false if it wasn't.
	 */
	public function maybe_convert_table_to_utf8( $table ) {
		global $wpdb;
		$results = $wpdb->get_results( "SHOW FULL COLUMNS FROM `$table`" );
		if ( ! $results ) {
			return false;
		}

		foreach ( $results as $column ) {
			if ( $column->Collation ) {
				list( $charset ) = explode( '_', $column->Collation );
				$charset = strtolower( $charset );
				if ( 'utf8' !== $charset && 'utf8mb4' !== $charset ) {
					// Don't upgrade tables that have non-utf8 columns.
					return false;
				}
			}
		}

		$table_details = $wpdb->get_row( "SHOW TABLE STATUS LIKE '$table'" );
		if ( ! $table_details ) {
			return false;
		}

		list( $table_charset ) = explode( '_', $table_details->Collation );
		$table_charset = strtolower( $table_charset );
		if ( 'utf8' === $table_charset ) {
			return true;
		}

		return $wpdb->query( "ALTER TABLE $table CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci" );
	}
}
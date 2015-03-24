<?php

namespace WordPress\Tables;

class BackupManager {

	public $container;

	function backup() {
		$backup      = $this->get_backup_file();
		$table_names = $this->get_table_names();
		$tables_arg  = implode( ',', $table_names );
		$tables_arg  = escapeshellarg( $tables_arg );

		if ( file_exists( $backup ) ) {
			\WP_CLI::confirm( 'Backup file exists, Are you sure you want to overwrite it?' );
			\WP_CLI::log( "Overwriting Database Backup: $backup" );
		} else {
			\WP_CLI::log( "Backing up Database: $backup" );
		}

		//echo( "wp a--db export \"$backup\" --tables=$tables_arg" );
		system( "wp --allow-root db export \"$backup\" --tables=$tables_arg" );
	}

	function restore() {
		$backup        = $this->get_backup_file();
		$table_factory = $this->container->table_factory;
		$table_factory->count();

		if ( file_exists( $backup ) ) {
			\WP_CLI::log( "Restoring Backup: $backup" );
			system( "wp --allow-root db import \"$backup\"" );
		} else {
			\WP_CLI::error( "Backup not found: $backup" );
		}
	}

	function get_backup_file() {
		return $this->container->config->get_site_dir() . '/backups/database.sql';
	}

	function get_table_names() {
		return $this->container->table_factory->get_table_names();
	}

}

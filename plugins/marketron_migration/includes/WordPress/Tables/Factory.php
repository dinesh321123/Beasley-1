<?php

namespace WordPress\Tables;

class Factory {

	public $container;
	public $tables;
	public $types = array(
		'users'              => 'WordPress\Tables\Users',
		'usermeta'           => 'WordPress\Tables\UserMeta',
		'terms'              => 'WordPress\Tables\Terms',
		'term_taxonomy'      => 'WordPress\Tables\TermTaxonomy',
		'term_relationships' => 'WordPress\Tables\TermRelationships',
		'posts'              => 'WordPress\Tables\Posts',
		'postmeta'           => 'WordPress\Tables\PostMeta',
	);

	public $instances = array();

	function build_all() {
		foreach ( $this->types as $name => $type ) {
			$this->build( $name );
		}
	}

	function build( $name ) {
		return $this->get_instance_for_table( $name );
	}

	function get_table_names() {
		$table_names = array();

		foreach ( $this->types as $name => $type ) {
			$table         = $this->build( $name );
			$table_names[] = $table->get_prefixed_table_name();
		}

		return $table_names;
	}

	// initializes the counters
	function count() {
		foreach ( $this->types as $name => $type ) {
			$instance = $this->build( $name );
			$instance->get_next_id();
		}
	}

	// build all csvs
	function export() {
		$this->export_all();
		$this->export_to_file();
	}

	function export_all() {
		foreach ( $this->instances as $instance ) {
			$instance->export();
		}
	}

	function export_to_file() {
		$import_script_file = $this->container->config->get_import_script_file();
		$file               = fopen( $import_script_file, 'w' );
		fwrite( $file, "#!/usr/bin/env bash\n" );
		fwrite( $file, "set -e\n" );

		foreach ( $this->instances as $instance ) {
			$cmd = $instance->get_import_command();
			fwrite( $file, 'echo "Importing ' . $instance->get_table_name() . "...\"\n" );
			fwrite( $file, $cmd . "\n" );
			fwrite( $file, "\n" );
		}

		fclose( $file );
		chmod( $import_script_file, '0700' );
		\WP_CLI::success( 'Exported Tables Successfully' );
	}

	// load data from all csvs exported
	function import() {
		foreach ( $this->instances as $instance ) {
			$instance->import();
		}
	}

	function get_type_for_table( $name ) {
		if ( array_key_exists( $name, $this->types ) ) {
			return $this->types[ $name ];
		} else {
			throw new \Exception(
				"Fatal Error: Unknown Table Type Name - $name"
			);
		}
	}

	function get_instance_for_table( $name ) {
		if ( ! array_key_exists( $name, $this->instances ) ) {
			$type = $this->get_type_for_table( $name );
			$this->instances[ $name ] = $instance = new $type();

			$instance->container = $this->container;
			$instance->factory   = $this;
		}

		return $this->instances[ $name ];
	}

}

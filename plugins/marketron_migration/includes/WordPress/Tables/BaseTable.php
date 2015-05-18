<?php

namespace WordPress\Tables;

class BaseTable {

	public $container;
	public $factory;
	public $id_counter    = null;
	public $rows          = array();
	public $columns       = array();
	public $null_columns  = array();
	public $indices       = array();
	public $indices_store = array();
	public $primary_key   = 'ID';

	function get_next_id() {
		if ( is_null( $this->id_counter ) ) {
			$seed_id         = $this->get_seed_id();
			$has_primary_key = $this->has_primary_key();

			if ( $seed_id === 0 && $has_primary_key ) {
				$this->id_counter = $this->get_max_id() + 1000;
			} else {
				$this->id_counter = $seed_id;
			}
		}

		return $this->id_counter++;
	}

	function get_seed_id() {
		return $this->container->config->get_seed_id();
	}

	function has_primary_key() {
		return in_array( $this->primary_key, $this->columns );
	}

	function get_max_id() {
		global $wpdb;

		$query  = " Select Max( $this->primary_key )";
		$query .= ' From ' . $this->get_prefixed_table_name();
		$max_id = intval( $wpdb->get_var( $query ) );

		return $max_id;
	}

	function get_rows() {
		return $this->rows;
	}

	function get_columns() {
		return $this->columns;
	}

	function get_indices() {
		return $this->indices;
	}

	function get_table( $name ) {
		return $this->factory->build( $name );
	}

	function add( &$fields ) {
		if ( ! array_key_exists( $this->primary_key, $fields ) ) {
			if ( array_key_exists( 'existing_id', $fields ) ) {
				/* if existing id, this is a record retrieved from the db */
				$fields[ $this->primary_key ] = $fields['existing_id'];
				$fields['exclude_from_csv'] = true;
			} else {
				$fields[ $this->primary_key ] = $this->get_next_id();
			}
		}

		$id                = $fields[ $this->primary_key ];
		$this->rows[ $id ] = $fields;

		$this->index_row( $fields );

		return $fields;
	}

	function update( $id, $field_name, $field_value ) {
		$this->rows[ $id ][ $field_name ] = $field_value;
	}

	function index_row( &$fields ) {
		$indices = $this->get_indices();
		$id      = $fields[ $this->primary_key ];

		foreach ( $indices as $index_field ) {
			if ( ! array_key_exists( $index_field, $this->indices_store ) ) {
				$this->indices_store[ $index_field ] = array();
			}

			if ( array_key_exists( $index_field, $fields ) ) {
				$field_value = $fields[ $index_field ];

				if ( ! array_key_exists( $field_value, $this->indices_store[ $index_field ] ) ) {
					$this->indices_store[ $index_field ][ $field_value ] = array();
				}

				$this->indices_store[ $index_field ][ $field_value ][] = $id;
			}
		}
	}

	/* Eg:- has_row_with_field( 'slug', 'foo-bar' ) */
	function has_row_with_field( $field_name, $field_value ) {
		if ( is_null( $field_value ) ) {
			return false;
		}

		if ( is_array( $field_value ) ) {
			$field_value = $field_value[0];
		}

		if ( array_key_exists( $field_name, $this->indices_store ) ) {
			if ( array_key_exists( $field_value, $this->indices_store[ $field_name ] ) ) {
				$indices = $this->indices_store[ $field_name ][ $field_value ];
				return count( $indices ) > 0;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// returns first row with matched index
	function get_row_with_field( $field_name, $field_value ) {
		if ( is_array( $field_value ) ) {
			$field_value = $field_value[0];
		}

		if ( $this->has_row_with_field( $field_name, $field_value ) ) {
			$indices = $this->indices_store[ $field_name ][ $field_value ];
			$row_id  = $indices[0];
			return $this->rows[ $row_id ];
		} else {
			return null;
		}
	}

	function get_rows_with_field( $field_name, $field_value ) {
		if ( $this->has_row_with_field( $field_name, $field_value ) ) {
			$indices = $this->indices_store[ $field_name ][ $field_value ];
			return $this->get_rows_by_ids( $indices );
		} else {
			return null;
		}
	}

	function get_row_by_id( $id ) {
		if ( array_key_exists( $id, $this->rows ) ) {
			return $this->rows[ $id ];
		} else {
			return null;
		}
	}

	function get_rows_by_ids( $ids ) {
		$rows = array();

		foreach ( $ids as $id ) {
			if ( array_key_exists( $id, $this->rows ) ) {
				$rows[] = $this->rows[ $id ];
			}
		}

		return $rows;
	}

	function get_export_dir() {
		return $this->container->config->get_csv_export_dir();
	}

	function get_table_name() {
		return 'base_table';
	}

	function is_multisite_table() {
		return true;
	}

	function get_prefixed_table_name() {
		global $wpdb;

		if ( $this->is_multisite_table() ) {
			return $wpdb->prefix . $this->get_table_name();
		} else {
			return $wpdb->base_prefix . $this->get_table_name();
		}
	}

	function get_export_file() {
		return $this->get_export_dir() . '/' . $this->get_prefixed_table_name() . '.csv';
	}

	function export() {
		$csv_file    = $this->get_export_file();
		$file_handle = fopen( $csv_file, 'w' );

		$this->to_csv( $file_handle );
	}

	function can_destroy() {
		return true;
	}

	function destroy() {
		$this->factory = null;
		unset( $this->factory );

		$this->container = null;
		unset( $this->container );

		$this->rows = null;
		unset( $this->rows );

		$this->columns = null;
		unset( $this->columns );

		$this->null_columns = null;
		unset( $this->null_columns );

		$this->indices = null;
		unset( $this->indices );

		$this->indices_store = null;
		unset( $this->indices_store );
	}

	function get_import_query() {
		$csv_file   = realpath( $this->get_export_file() );
		$table_name = $this->get_prefixed_table_name();
		$columns    = implode( ',', $this->get_columns() );
		$query      = <<<SQL
Load Data Local InFile '$csv_file'
Into Table $table_name
Fields Terminated By ','
Optionally Enclosed By '\"'
($columns);
SQL;

		$query = str_replace( "\n", " ", $query );
		return $query;
	}

	function get_import_command() {
		$table_name = $this->get_prefixed_table_name();
		$query      = $this->get_import_query();

		$cmd  = 'mysql';
		$cmd .= ' --user='     . DB_USER;
		$cmd .= ' --password=' . DB_PASSWORD;
		$cmd .= ' --host='     . DB_HOST;
		$cmd .= ' --local-infile';
		$cmd .= ' --database=' . DB_NAME;
		$cmd .= ' --show-warnings';
		$cmd .= ' -vve ' . escapeshellarg( $query );

		return $cmd;
	}

	function import() {
		$table_name = $this->get_prefixed_table_name();

		\WP_CLI::log( "Importing $table_name ..." );

		$cmd = $this->get_import_command();
		system( $cmd );

		\WP_CLI::success( "Imported $table_name" );
	}

	/*
	function import_with_mysqlimport() {
		$table_name = $this->get_prefixed_table_name();

		\WP_CLI::log( "Importing $table_name ..." );

		$cmd  = 'mysqlimport ';
		$cmd .= ' --user='     . DB_USER;
		$cmd .= ' --password=' . DB_PASSWORD;
		$cmd .= ' --host='     . DB_HOST;
		$cmd .= ' --local';
		$cmd .= ' --columns=' . implode( ',', $this->get_columns() );
		$cmd .= ' ' . DB_NAME;
		$cmd .= ' ' . realpath( $this->get_export_file() );

		system( $cmd );

		\WP_CLI::success( "Imported $table_name" );
	}
	 */

	function to_csv( $csv_file_handle ) {
		$columns    = $this->get_columns();
		$rows       = $this->get_rows();
		$total_rows = count( $rows );
		$table_name = $this->get_table_name();

		if ( $total_rows > 0 ) {
			$msg        = "Generating CSV with $total_rows $table_name";
			$msg        = str_pad( $msg, 40, ' ', STR_PAD_RIGHT );
			$notify     = new \WordPress\Utils\ProgressBar( $msg, $total_rows );

			foreach ( $rows as $row_id => $row ) {
				if ( array_key_exists( 'exclude_from_csv', $row ) ) {
					/* ignore rows marked for exclusion */
					continue;
				}

				$csv_row = $this->to_csv_row( $row, $columns );
				fputcsv(
					$csv_file_handle, $csv_row, ',', '"'
				);

				$notify->tick();
			}

			fclose( $csv_file_handle );
			$notify->finish();
		} else {
			\WP_CLI::log( "Skipped $table_name" );
		}
	}

	function to_csv_row( $row, $columns ) {
		//var_dump( $columns );
		$csv_row = array();
		foreach ( $columns as $column ) {
			if ( ! array_key_exists( $column, $row ) ) {
				if ( $this->column_has_default( $column ) ) {
					$value = 'NULL';
				} else {
					$value = null;
				}
			} else {
				$type = gettype( $row[ $column ] );
				$value = $row[ $column ];

				if ( $value instanceof \DateTime ) {
					$value = $value->format( 'Y-m-d H:i:s' );
				}
				//error_log( $column . ' ' . $type );
			}
			// TODO: type conversion
			//
			$csv_row[] = $value;
		}

		//var_dump( $csv_row );
		return $csv_row;
	}

	function column_has_default( $column ) {
		return in_array( $column, $this->columns_with_defaults );
	}

}

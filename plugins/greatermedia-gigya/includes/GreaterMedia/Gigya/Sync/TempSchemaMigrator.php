<?php

namespace GreaterMedia\Gigya\Sync;

class TempSchemaMigrator {

	public $schema_version_key      = 'member_query_temp_schema_version';
	public $required_schema_version = '0.1.0';

	function migrate() {
		if ( $this->can_migrate() ) {
			$this->run(
				$this->get_current_schema_version(),
				$this->required_schema_version
			);

			$this->change_schema_version( $this->required_schema_version );

			return true;
		}

		return false;
	}

	function run( $start_version, $end_version ) {
		$this->run_if( $start_version, '0.0.1' );
		$this->run_if( $start_version, '0.0.2' );
		$this->run_if( $start_version, '0.0.3' );
		$this->run_if( $start_version, '0.0.4' );
		$this->run_if( $start_version, $end_version );
	}

	function run_if( $start_version, $min_version ) {
		if ( version_compare( $start_version, $min_version, '<' ) ) {
			$path = $this->get_path_to_schema_for( $min_version );

			if ( file_exists( $path ) ) {
				$script = file_get_contents( $path );
				$this->execute( $script );
				return true;
			}
		}

		return false;
	}

	function execute( $script ) {
		$temp_db = TempDatabase::get_instance();
		return $temp_db->execute( $script );
	}

	function get_path_to_schema_for( $version ) {
		return GMR_GIGYA_PATH . "scripts/sync/{$version}.sql";
	}

	function can_migrate() {
		return version_compare(
			$this->get_current_schema_version(),
			$this->required_schema_version,
			'<'
		);
	}

	function get_current_schema_version() {
		return get_option( $this->schema_version_key, '0.0.0' );
	}

	function change_schema_version( $version ) {
		delete_option( $this->schema_version_key );
		add_option( $this->schema_version_key, $version, null, 'no' );
	}

}

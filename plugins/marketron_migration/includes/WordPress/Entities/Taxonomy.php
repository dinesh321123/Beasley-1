<?php

namespace WordPress\Entities;

class Taxonomy extends BaseEntity {

	public $container;
	public $relationships_cache = array();

	function get_taxonomy() {
		return 'taxonomy_name';
	}

	function add( $term_name, $post_id = null, $exclude_from_csv = false ) {
		if ( is_array( $term_name ) ) {
			$term_name = $term_name[0];
		}

		if ( trim( $term_name ) === '' ) {
			//\WP_CLI::warning( 'Empty Term name' );
			return;
		}

		$taxonomy         = $this->get_taxonomy();

		if ( $exclude_from_csv ) {
			$existing_term = $this->get_existing_term( $term_name );
		} else {
			$existing_term = false;
		}

		if ( $existing_term !== false ) {
			$term_id          = intval( $existing_term['term_id'] );
			$term_taxonomy_id = intval( $existing_term['term_taxonomy_id'] );

			$term  = array( 'name' => $term_name, 'existing_id' => $term_id );
			$table = $this->get_table( 'terms' );

			$table->add( $term );

			$table            = $this->get_table( 'term_taxonomy' );
			$term_taxonomy    = array( 'term_id' => $term_id, 'taxonomy' => $taxonomy, 'existing_id' => $term_taxonomy_id );

			$table->add( $term_taxonomy );
		} else {
			$term_id          = $this->find_or_create_term( $term_name, $taxonomy, $exclude_from_csv );
			$term_taxonomy_id = $this->find_or_create_term_taxonomy( $term_id, $taxonomy, $exclude_from_csv );
		}

		if ( ! is_null( $post_id ) && ! $exclude_from_csv ) {
			if ( ! $exclude_from_csv ) {
				$this->find_or_create_term_relationship( $term_taxonomy_id, $post_id );
			} else {
				/*
				 * The relationship already exists in the DB and since we are additionally
				 * caching the term relationships locally already, we don't
				 * go up to the table.
				 */
				$term_relationship = array(
					'term_taxonomy_id' => $term_taxonomy_id,
					'object_id'        => $object_id,
				);

				$this->cache_term_relationship( $term_relationship );
			}
		}

		//error_log( "term_id: $term_id, term_taxonomy_id: $term_taxonomy_id" );
	}

	function find_or_create_term( $term_name, $taxonomy ) {
		$table = $this->get_table( 'terms' );

		if ( ! $table->has_term( $term_name ) ) {
			$term    = array( 'name' => $term_name );
			$term    = $table->add( $term );
		} else {
			$term    = $table->get_term( $term_name );
		}

		return $term['term_id'];
	}

	function find_or_create_term_taxonomy( $term_id, $taxonomy ) {
		$table = $this->get_table( 'term_taxonomy' );

		if ( ! $table->has_term_taxonomy( $term_id, $taxonomy ) ) {
			$term_taxonomy    = array( 'term_id' => $term_id, 'taxonomy' => $taxonomy );
			$term_taxonomy    = $table->add( $term_taxonomy );
		} else {
			$term_taxonomy = $table->get_term_taxonomy( $term_id, $taxonomy );
		}

		return $term_taxonomy['term_taxonomy_id'];
	}

	function find_or_create_term_relationship( $term_taxonomy_id, $object_id ) {
		$table = $this->get_table( 'term_relationships' );

		if ( ! $this->has_cached_term_relationship( $term_taxonomy_id, $object_id ) ) {
			$term_relationship = array(
				'term_taxonomy_id' => $term_taxonomy_id,
				'object_id'        => $object_id,
			);

			$term_relationship = $table->add( $term_relationship );
			$this->cache_term_relationship( $term_relationship );
		} else {
			$term_relationship = $this->get_cached_term_relationship( $term_taxonomy_id, $object_id );
		}

		return $term_relationship;
	}

	function has_cached_term_relationship( $term_taxonomy_id, $object_id ) {
		$key = $term_taxonomy_id . ' x ' . $object_id;
		return array_key_exists( $key, $this->relationships_cache );
	}

	function cache_term_relationship( &$term_relationship ) {
		$term_taxonomy_id                  = $term_relationship['term_taxonomy_id'];
		$object_id                         = $term_relationship['object_id'];
		$key                               = $term_taxonomy_id . ' x ' . $object_id;

		$this->relationships_cache[ $key ] = $term_relationship;
	}

	function get_cached_term_relationship( $term_taxonomy_id, $object_id ) {
		$key = $term_taxonomy_id . ' x ' . $object_id;
		return $this->relationships_cache[ $key ];
	}

	function get_existing_term( $term_name ) {
		$term = get_term_by( 'name', $term_name, $this->get_taxonomy(), ARRAY_A );

		if ( $term !== false ) {
			return $term;
		} else {
			return false;
		}
	}

	function destroy() {
		$this->relationships_cache = null;
		unset( $this->relationships_cache );

		parent::destroy();
	}

}

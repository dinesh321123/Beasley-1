<?php

namespace WordPress\Entities;

class Taxonomy extends BaseEntity {

	public $container;

	function get_taxonomy() {
		return 'taxonomy_name';
	}

	function add( $term_name, $post_id = null ) {
		$taxonomy         = $this->get_taxonomy();
		$term_id          = $this->find_or_create_term( $term_name, $taxonomy );
		$term_taxonomy_id = $this->find_or_create_term_taxonomy( $term_id, $taxonomy );

		if ( ! is_null( $post_id ) ) {
			$this->find_or_create_term_relationship( $term_taxonomy_id, $post_id );
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
			//if ( strpos( $taxonomy, '_' ) === 0 ) {
				//$term    = array( 'name' => $term_name );
				//$term    = $table->add( $term );
			//} else {
			//}
		}

		//error_log( "find_or_create_term: $term_name" );
		//var_dump( $term );

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

		//error_log( "find_or_create_term_taxonomy: $term_id, $taxonomy" );
		//var_dump( $term_taxonomy );

		return $term_taxonomy['term_taxonomy_id'];
	}

	function find_or_create_term_relationship( $term_taxonomy_id, $object_id ) {
		$table = $this->get_table( 'term_relationships' );

		if ( ! $table->has_term_relationship( $term_taxonomy_id, $object_id ) ) {
			$term_relationship = array(
				'term_taxonomy_id' => $term_taxonomy_id,
				'object_id'        => $object_id,
			);

			$term_relationship = $table->add( $term_relationship );
		} else {
			$term_relationship = $table->get_term_relationship( $term_taxonomy_id, $object_id );
		}

		//error_log( "find_or_create_term_relationship: $term_taxonomy_id, object_id: $object_id" );
		//var_dump( $term_relationship );

		return $term_relationship;
	}

}

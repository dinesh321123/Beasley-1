<?php

namespace WordPress\Tables;

class TermRelationships extends BaseTable {

	public $primary_key = 'term_relationship_id'; // psuedo primary key
	public $columns = array(
		'object_id',
		'term_taxonomy_id',
		'term_order',
	);

	public $indices = array(
		'object_id',
		'term_taxonomy_id',
	);

	public $columns_with_defaults = array(
		'object_id',
		'term_taxonomy_id',
		'term_order',
	);

	function get_table_name() {
		return 'term_relationships';
	}

	function add( &$fields ) {
		if ( ! array_key_exists( 'term_order', $fields ) ) {
			$fields['term_order'] = 0;
		}

		return parent::add( $fields );
	}

	function has_term_relationship( $term_taxonomy_id, $object_id ) {
		$relationships = $this->get_rows_with_field( 'term_taxonomy_id', $term_taxonomy_id );

		if ( is_null( $relationships ) ) {
			return false;
		}

		foreach ( $relationships as $relationship ) {
			if ( $relationship['object_id'] === $object_id ) {
				return true;
			}
		}

		return false;
	}

	function get_term_relationship( $term_taxonomy_id, $object_id ) {
		$relationships = $this->get_rows_with_field( 'term_taxonomy_id', $term_taxonomy_id );

		foreach ( $relationships as $relationship ) {
			if ( $relationship['object_id'] === $object_id ) {
				return $relationship;
			}
		}

		return null;
	}

}

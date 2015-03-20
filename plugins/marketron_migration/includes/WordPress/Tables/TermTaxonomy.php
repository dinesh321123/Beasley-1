<?php

namespace WordPress\Tables;

class TermTaxonomy extends BaseTable {

	public $primary_key = 'term_taxonomy_id';
	public $columns     = array(
		'term_taxonomy_id',
		'term_id',
		'taxonomy',
		'description',
		'parent',
	);

	public $indices = array(
		'term_id',
	);

	public $columns_with_defaults = array(
		'term_id',
		'parent',
		'count',
	);

	function get_table_name() {
		return 'term_taxonomy';
	}

	function add( &$fields ) {
		$term_id  = $fields['term_id'];
		$taxonomy = $fields['taxonomy'];

		if ( ! $this->has_term_taxonomy( $term_id, $taxonomy ) ) {
			if ( ! array_key_exists( 'parent', $fields ) ) {
				$fields['parent'] = 0;
			}

			return parent::add( $fields );
		} else {
			return $fields;
		}
	}

	function has_term_taxonomy( $term_id, $taxonomy ) {
		$term_taxonomies = $this->get_rows_with_field( 'term_id', $term_id );

		if ( is_null( $term_taxonomies ) ) {
			return false;
		}

		foreach ( $term_taxonomies as $term_taxonomy ) {
			if ( $term_taxonomy['taxonomy'] === $taxonomy ) {
				return true;
			}
		}

		return false;
	}

	function get_term_taxonomy( $term_id, $taxonomy ) {
		$term_taxonomies = $this->get_rows_with_field( 'term_id', $term_id );

		foreach ( $term_taxonomies as $term_taxonomy ) {
			if ( $term_taxonomy['taxonomy'] === $taxonomy ) {
				return $term_taxonomy;
			}
		}

		return null;
	}

	function get_term_taxonomy_id( $term_id, $taxonomy ) {

	}

}

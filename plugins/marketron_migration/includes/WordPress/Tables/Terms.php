<?php

namespace WordPress\Tables;

class Terms extends BaseTable {

	public $primary_key = 'term_id';

	public $columns = array(
		'term_id',
		'name',
		'slug',
	);

	public $indices = array(
		'name',
	);

	public $columns_with_defaults = array(
		'term_group',
	);

	function get_table_name() {
		return 'terms';
	}

	function add( &$fields ) {
		$term_name = $fields['name'];

		if ( $this->has_term( $term_name ) ) {
			//error_log( "did not create term: $term_name" );
			return $this->get_term( $term_name );
		}

		if ( ! array_key_exists( 'slug', $fields ) ) {
			$fields['slug'] = sanitize_title( $term_name );
		}

		parent::add( $fields );

		if ( array_key_exists( 'term_taxonomy', $fields ) ) {
			$term_id                 = $fields['term_id'];
			$fields['term_taxonomy'] = $this->add_term_taxonomy( $term_id, $fields['term_taxonomy'] );
		}

		return $fields;
	}

	function add_term_taxonomy( $term_id, $fields ) {
		$table             = $this->get_table( 'term_taxonomy' );
		$fields['term_id'] = $term_id;

		if ( ! array_key_exists( 'description', $fields ) ) {
			$fields['description'] = null;
		}

		if ( ! array_key_exists( 'parent', $fields ) ) {
			$fields['parent'] = 0;
		}

		return $table->add( $fields );
	}

	function has_term( $term_name ) {
		return $this->has_row_with_field( 'name', $term_name );
	}

	function get_term( $term_name ) {
		return $this->get_row_with_field( 'name', $term_name );
	}

	function get_term_id( $term_name ) {
		$term = $this->get_term( 'name', $term_name );
		return $term['term_id'];
	}

}

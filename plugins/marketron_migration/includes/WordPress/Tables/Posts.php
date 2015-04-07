<?php

namespace WordPress\Tables;

class Posts extends BaseTable {

	public $columns = array(
		'post_parent',
		'menu_order',
		'post_modified',
		'ID',
		'post_modified_gmt',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_title',
		'post_name',
		'post_content',
		'post_content_filtered',
		'post_excerpt',
		'post_status',
		'comment_status',
		'ping_status',
		'post_password',
		'to_ping',
		'pinged',
		'guid',
		'post_type',
		'post_mime_type',
		'comment_count',
	);

	public $indices = array(
		'post_name',
		'post_type',
		'post_title',
	);

	public $columns_with_defaults = array(
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_status',
		'comment_status',
		'ping_status',
		'post_modified',
		'post_modified_gmt',
		'post_parent',
		'menu_order',
		'post_type',
		'comment_count',
	);

	function get_table_name() {
		return 'posts';
	}

	function add( &$fields ) {
		if ( ! array_key_exists( 'post_name', $fields ) || $fields['post_name'] === '' ) {
			$fields['post_name'] = sanitize_title( $fields['post_title'] );
		}

		$fields['post_name'] = $this->distinct_post_name( $fields['post_name'] );

		$fields  = parent::add( $fields );
		$post_id = $fields['ID'];

		if ( array_key_exists( 'postmeta', $fields ) ) {
			$fields['postmeta'] = $this->add_post_meta( $post_id, $fields['postmeta'] );
		}

		if ( ! array_key_exists( 'guid', $fields ) ) {
			$this->rows[ $post_id ]['guid'] = trailingslashit( get_site_url() ) . '?p=' . $fields['ID'];
		}

		if ( ! array_key_exists( 'post_password', $fields ) ) {
			$fields['post_password'] = '';
		}

		if ( ! array_key_exists( 'post_author', $fields ) ) {
			$fields['post_author'] = 0;
		}

		if ( ! array_key_exists( 'post_parent', $fields ) ) {
			$fields['post_parent'] = 0;
		}

		if ( ! array_key_exists( 'menu_order', $fields ) ) {
			$fields['menu_order'] = 0;
		}

		return $fields;
	}

	function distinct_post_name( $post_name ) {
		$counter = 1;
		$max_attempts = 50;

		while ( $counter < $max_attempts ) {
			if ( $this->has_row_with_field( 'post_name', $post_name ) ) {
				$post_name = $post_name . '-' . $counter;
			} else {
				break;
			}

			$counter++;
		}

		return $post_name;
	}

	function add_timestamps( &$fields ) {
		// TODO: Rethink this logic
		if ( array_key_exists( 'created_on' ) ) {
			$created_on = $fields['created_on'];

			if ( is_string( $created_on ) ) {
				// if string assuming it's already a valid date
				$fields['post_date']     = $created_on;
				$fields['post_date_gmt'] = $created_on;
			}
		}
	}

	function add_post_meta( $post_id, $fields ) {
		$meta_fields = $this->to_meta_fields( $post_id, $fields );
		$table       = $this->get_table( 'postmeta' );

		foreach ( $meta_fields as $meta_field ) {
			$table->add( $meta_field );
		}
	}

	function to_meta_fields( $post_id, $fields ) {
		$meta_fields = array();

		foreach ( $fields as $field_name => $field_value ) {
			$meta_field = array(
				'post_id'    => $post_id,
				'meta_key'   => $field_name,
				'meta_value' => $field_value,
			);

			$meta_fields[] = $meta_field;
		}

		return $meta_fields;
	}

}

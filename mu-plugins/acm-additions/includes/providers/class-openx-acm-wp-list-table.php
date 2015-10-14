<?php

namespace GreaterMedia\AdCodeManager;

class OpenX_ACM_WP_List_Table extends \ACM_WP_List_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => 'openx_acm_wp_list_table',
			'plural' => 'openx_acm_wp_list_table',
			'ajax' => true,
		) );
	}

	function get_columns( $columns = false ) {
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'id'             => 'ID',
			'tag'            => 'Tag',
			'openx_id'       => 'OpenX ID',
			'priority'       => 'Priority',
			'operator'       => 'Logical Operator',
			'conditionals'   => 'Conditionals',
		);

		$columns = apply_filters( 'gmr-acm-openx-table-columns', $columns );
		return parent::get_columns( $columns );
	}

	function column_tag( $item ) {
		$output = isset( $item['tag'] ) ? esc_html( $item['tag'] ) : esc_html( $item['url_vars']['tag'] );
		$output .= $this->row_actions_output( $item );

		return $output;
	}
}

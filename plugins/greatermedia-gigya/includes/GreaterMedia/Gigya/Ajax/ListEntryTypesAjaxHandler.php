<?php

namespace GreaterMedia\Gigya\Ajax;

class ListEntryTypesAjaxHandler extends AjaxHandler {

	public function get_action() {
		return 'list_entry_types';
	}

	public function run( $params ) {
		// TODO: Pagination
		$contests_args = array( 'post_type' => 'contest', 'post_status' => 'publish' );
		$query      = new \WP_Query( $contests_args );
		$contests   = $query->get_posts();
		$entryTypes = [];

		foreach ( $contests as $contest ) {
			$form = get_post_meta( $contest->ID, 'embedded_form', true );
			if ( $form ) {
				$entryTypes[] = array(
					'label' => $contest->post_title,
					'value' => $contest->ID,
				);
			}
		}

		return $entryTypes;
	}

}

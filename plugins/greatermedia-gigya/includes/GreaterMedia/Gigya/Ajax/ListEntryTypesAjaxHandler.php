<?php

namespace GreaterMedia\Gigya\Ajax;

class ListEntryTypesAjaxHandler extends AjaxHandler {

	public function get_action() {
		return 'list_entry_types';
	}

	public function run( $params ) {
		$handler = new GetChoicesForConstraintType();
		return $handler->run( $params );
	}

}

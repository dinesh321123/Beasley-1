<?php

namespace WordPress\Entities;

class ContestEntry extends Post {

	function get_post_type() {
		return 'contest_entry';
	}

	function add( &$fields ) {
		$fields = parent::add( $fields );

		return $fields;
	}

}

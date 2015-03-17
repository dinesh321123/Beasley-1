<?php

namespace WordPress\Entities;

class Author extends User {

	function add( &$fields ) {
		return parent::add( $fields );
	}

	function get_roles() {
		return array(
			'author' => true,
		);
	}

}

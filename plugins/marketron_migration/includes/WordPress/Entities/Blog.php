<?php

namespace WordPress\Entities;

class Blog extends Post {

	function get_post_type() {
		return 'post';
	}

	function add( &$fields ) {
		if ( ! empty( $fields['show'] ) ) {
			$show = $fields['show'];
		} else {
			$show = null;
		}

		$fields = parent::add( $fields );
		$post_id = $fields['ID'];

		if ( ! empty( $show ) ) {
			$entity = $this->get_entity( 'show_taxonomy' );
			$entity->add( $show, $post_id );
		}

		return $fields;
	}

}

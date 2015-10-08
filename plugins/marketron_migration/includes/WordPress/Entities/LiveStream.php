<?php

namespace WordPress\Entities;

class LiveStream extends Post {

	function get_post_type() {
		return 'live-stream';
	}

	function add( &$fields ) {
		$post = get_page_by_title( $fields['call_sign'], ARRAY_A, $this->get_post_type() );
		if ( ! is_null( $post ) ) {
			$fields['ID'] = $post['ID'];
			return $fields;
		}

		$call_sign   = $fields['call_sign'];
		$description = $fields['description'];
		$vast_url    = $fields['vast_url'];

		$fields['post_title'] = $call_sign;
		$fields['postmeta'] = array(
			'call_sign'   => $call_sign,
			'description' => $description,
			'vast_url'    => $vast_url,
		);

		$fields = parent::add( $fields );

		return $fields;
	}

}

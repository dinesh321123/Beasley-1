<?php

namespace WordPress\Entities;

class LegacyRedirect extends Post {

	function get_post_type() {
		return 'cmm-redirect';
	}

	function add( &$fields ) {
		$url                   = $fields['url'];
		$fields['post_parent'] = $fields['post_id'];
		$fields['post_title']  = $this->get_post_title( $url );
		$fields['post_name']   = $this->get_post_name( $url );

		return parent::add( $fields );
	}

	function get_post_title( $url ) {
		$path  = parse_url( $url, PHP_URL_PATH );
		$query = parse_url( $url, PHP_URL_QUERY );

		if ( ! is_null( $query ) ) {
			$path .= '?' . $query;
		}

		return $path;
	}

	function get_post_name( $url ) {
		return md5( $this->get_post_title( $url ) );
	}

}

<?php

namespace GreaterMedia\LiveFyreWalls;

class ContentFilter {

	public function __construct() {
		add_filter( 'content_save_pre', array( $this, 'filter_content' ), 0 );
	}

	public function filter_content( $content ) {
		$pattern = '#<div\s*id\s*=\s*\\\(\'|")wall\\\\\\1.*?</script>.*?</script>#is';
		$pattern_with_encoded_brackets = str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $pattern );

		$content = preg_replace_callback( array( $pattern, $pattern_with_encoded_brackets ), array( $this, 'replacement_callback' ), $content );

		return $content;
	}

	public function replacement_callback( $matches ) {
		$sub_matches = array();
		$pattern = '#\\\"(network|siteId|articleId)\\\"\s*:\s*\\\"(.*?)\\\"#';
		$attr_map = array(
			'network'   => 'network_id',
			'siteId'    => 'site_id',
			'articleId' => 'article_id',
		);

		preg_match_all( $pattern, $matches[0], $sub_matches, PREG_SET_ORDER );

		$res = '[livefyre-wall';
		foreach ( $sub_matches as $sub_match ) {
			if ( array_key_exists( $sub_match[1], $attr_map ) ) {
				$sub_match[1] = $attr_map[$sub_match[1]];
				$res .= ' ' . $sub_match[1] . '="' . $sub_match[2] . '"';
			}
		}
		$res .= ']';

		return $res;
	}

}
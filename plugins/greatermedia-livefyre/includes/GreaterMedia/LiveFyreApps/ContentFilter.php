<?php

namespace GreaterMedia\LiveFyreApps;

class ContentFilter
{
	public function __construct()
	{
		add_filter( 'content_save_pre', array( $this, 'filter_content' ), 0 );
	}

	public function filter_content($content)
	{
		//$pattern = '#<div\s*id\s*=\s*\\\(\'|")lf-poll-\S+\\\\\\1.*?</script>.*?</script>#is';
		$pattern = '#<script.*?data-lf-app.*?</div>.*?</script>#is';
		$pattern_with_encoded_brackets = str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $pattern );

		$content = preg_replace_callback( array( $pattern, $pattern_with_encoded_brackets ),
			array( $this, 'replacement_callback' ), $content, -1, $match_count );

		return $content;
	}

	public function replacement_callback( $matches )
	{
		$attr_map = array(
			'data-lf-app' => 'data-lf-app',
			'data-lf-env' => 'data-lf-env'
		);

		preg_match_all( '#(data-lf-app|data-lf-env)=\\\"(.*?)\\\"#', $matches[0], $sub_matches, PREG_SET_ORDER );

		$res = '[livefyre-app';
		foreach ( $sub_matches as $sub_match ) {
			if ( array_key_exists( $sub_match[1], $attr_map ) ) {
				$sub_match[1] = $attr_map[ $sub_match[1] ];
				$res .= ' ' . str_replace( "-", "_", $sub_match[1] ) . '="' . $sub_match[2] . '"';
			}
		}
		$res .= ']';

		return $res;
	}
}

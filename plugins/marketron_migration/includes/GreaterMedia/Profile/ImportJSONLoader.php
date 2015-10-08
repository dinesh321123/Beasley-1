<?php

namespace GreaterMedia\Profile;

class ImportJSONLoader {

	function load( $path ) {
		$file = file_get_contents( $path, 'r' );
		$json = json_decode( $file, true );

		return $json['accounts'];
	}

}

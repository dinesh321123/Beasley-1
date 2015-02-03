<?php

namespace GreaterMedia\Gigya\Migration;

class AffinityClubParser {

	function parse( $source, $filter_source, $dest ) {
		$errors_dest    = str_replace( '.json', '.errors.log', $dest );
		$filter_members = $this->load_member_ids( $filter_source );

		$doc                     = new \DOMDocument();
		$doc->preserveWhitespace = false;
		$doc->formatOutput       = true;
		$doc->load( $source );

		$affinity_club = new AffinityClub();
		$affinity_club->parse( $doc->documentElement );

		$result = $affinity_club->export( $filter_members );
		$json   = json_encode( $result['data'], JSON_PRETTY_PRINT );

		file_put_contents( $dest, $json );

		if ( count( $result['errors'] ) > 0 ) {
			$errors_text = '';

			foreach ( $result['errors'] as $error ) {
				$errors_text .= $error['reason'];
				$errors_text .= "\n";
				$errors_text .= $error['node'];
				$errors_text .= "\n";
			}

			file_put_contents( $errors_dest, $errors_text );
		}

		return $result['data']['settings']['totalRecords'];
	}

	function load_member_ids( $path ) {
		$file       = fopen( $path, 'r' );
		$member_ids = array();
		$line       = fgets( $file );

		while ( $line !== false ) {
			$line = trim( $line );
			if ( is_numeric( $line ) ) {
				$member_ids[] = $line;
			}

			$line = fgets( $file );
		}

		return $member_ids;
	}

}

<?php

namespace GreaterMedia\Profile;

class AffinityClubScreener {

	public $container;

	function screen( $input, $output ) {
		$this->container->opts['repair'] = true;

		$member_ids         = $this->get_member_ids();

		$source = new \DOMDocument();
		$source->formatOutput = true;
		$source->preserveWhiteSpace = false;

		\WP_CLI::log( 'Loading XML: ' . $input );
		$status = $source->load( $input );
		$xpath    = new \DOMXPath( $source );

		if ( $status === false ) {
			\WP_CLI::error( 'Failed to load XML: ' . $input );
		}

		$this->screen_source( $source, $xpath, $member_ids );

		\WP_CLI::log( 'Saving screened source ...' );
		$source->save( $output );
		\WP_CLI::success( "Saved screened source to $output" );
	}

	function screen_source( $source, $xpath, &$member_ids ) {
		$members        = $this->all_members_from_source( $source, $xpath );
		$filtered_count = 0;
		$total          = count( $members );
		$msg            = "Screening $total Members";
		$progress_bar   = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $members as $member ) {
			$member_id = trim( (string) $member->getAttribute( 'MemberID' ) );

			if ( ! $this->can_import_member( $member_id, $member_ids ) ) {
				$filtered_count++;
				$member->parentNode->removeChild( $member );
			} else if ( $this->is_facebook_member( $member, $xpath ) ) {
				$this->remove_facebook_data( $member, $xpath );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		\WP_CLI::success( 'Total Count: ' . $total );
		\WP_CLI::success( 'Filtered Count: ' . $filtered_count );
		\WP_CLI::success( 'New User Count: ' . ( $total - $filtered_count ) );
	}

	function is_facebook_member( $member, $xpath ) {
		$result = $xpath->query( './FacebookMember', $member );
		return $result->length === 1;
	}

	function remove_facebook_data( $member, $xpath ) {
		$facebook_data = $xpath->query( './FacebookMember/*', $member );

		foreach ( $facebook_data as $node ) {
			$node->parentNode->removeChild( $node );
		}
	}

	function all_members_from_source( $source, $xpath ) {
		$members  = $xpath->query( '/AffinityClub/Members/Member' );
		$children = iterator_to_array( $members );

		return $children;
	}

	function can_import_member( $member_id, &$member_ids ) {
		return array_key_exists( $member_id, $member_ids );
	}

	function get_member_ids() {
		$config          = $this->container->config;
		$member_ids_file = $config->get_member_ids_file();
		$member_ids      = array();
		$file            = fopen( $member_ids_file, 'r' );
		$line            = fgets( $file );

		while ( $line !== false ) {
			$line = trim( $line );
			$line = rtrim( $line, ',' );

			if ( is_numeric( $line ) ) {
				$member_id    = strval( $line );
				$member_ids[ $member_id ] = true;
			}

			$line = fgets( $file );
		}

		return $member_ids;
	}

}

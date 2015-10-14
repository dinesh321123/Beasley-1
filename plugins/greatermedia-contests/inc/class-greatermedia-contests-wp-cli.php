<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestsWPCLI extends WP_CLI_Command {

	/**
	 * Generate test contest entries
	 *
	 * ## OPTIONS
	 *
	 * <contest>
	 * : The contest's post ID
	 *
	 * <n>
	 * : Number of entries to generate
	 *
	 * ## EXAMPLES
	 *
	 *     wp greatermedia_contests generate_entries --contest="123" --n=5
	 *
	 * @synopsis --contest=<post_id> --n=<number>
	 */
	public function generate_entries( $args, $assoc_args ) {

		global $timestart;

		$contest_id  = intval( $assoc_args['contest'] );
		$num_entries = intval( $assoc_args['n'] );

		$contest = get_post( $contest_id, OBJECT );
		if ( GMR_CONTEST_CPT !== $contest->post_type ) {
			WP_CLI::error( sprintf( 'Post %d is not a Contest', $contest_id ) );
		}

		$personas = self::random_names( $num_entries );

		for ( $entry_index = 0; $entry_index < $num_entries; $entry_index += 1 ) {

			$comment                                   = GreaterMediaContestEntry::for_comment_data(
				$contest_id,
				$personas[ $entry_index ]->user->picture,
				ucfirst( $personas[ $entry_index ]->user->name->title ) . ' ' .
				ucfirst( $personas[ $entry_index ]->user->name->first ) . ' ' .
				ucfirst( $personas[ $entry_index ]->user->name->last ),
				$personas[ $entry_index ]->user->email
			);
			$comment->comment_data['comment_parent']   = 0;
			$comment->comment_data['comment_agent']    = 'WP-CLI';
			$comment->comment_data['comment_approved'] = 1;

			$comment->comment_data['comment_content'] = json_encode( $personas[ $entry_index ] );

			$entry_id = $comment->save();
			WP_CLI::line( sprintf( 'Created entry %d', $entry_id ) );

		}

	}

	public static function random_names( $num = 1 ) {

		// Try to pull data from a service, fall back to just some hard-coded stuff
		$data = wp_remote_get( add_query_arg( 'results', intval( $num ), 'http://api.randomuser.me' ) );
		if ( ! is_wp_error( $data ) ) {
			WP_CLI::line( 'Retrieved fake persona data from the randomuser.me API' );
			$data = json_decode( $data['body'] );

			return $data->results;
		} else {
			WP_CLI::line( 'Error retrieving fake personas from API. Using hard-coded alternative.' );

			$persona                    = new stdClass();
			$persona->user              = new stdClass();
			$persona->user->email       = 'admin@127.0.0.1';
			$persona->user->name        = new stdClass();
			$persona->user->name->title = 'Ms';
			$persona->user->name->first = 'First';
			$persona->user->name->last  = 'Last';
			$persona->user->picture     = 'http://example.com';

			$results = array();
			for ( $entry_index = 0; $entry_index < $num; $entry_index += 1 ) {
				$results[] = $persona;
			}

			return $results;
		}


	}

	/**
	 * Import contests from the Marketron data (as best we can - the data doesn't map to our fields exactly)
	 *
	 * ## OPTIONS
	 *
	 * <marketron>
	 * : The Marketron XML file to import
	 *
	 * ## EXAMPLES
	 *
	 *     wp marketron_to_gigya convert --marketron="marketron_export.xml"
	 *
	 * @synopsis --marketron=<filename>
	 */
	public function import_contests( $args, $assoc_args ) {

		if ( ! isset( $assoc_args['marketron'] ) || empty( $assoc_args['marketron'] ) ) {
			WP_CLI::error( 'Please provide the filename of the Marketron export file using the --marketron parameter' );
		}

		// Load the Marketron export
		$xml = new DOMDocument;
		$xml->load( $assoc_args['marketron'] );

		$xpath = new Domxpath( $xml );

		$unique_contests = array();
		$contests        = $xpath->query( '//Contest' );
		foreach ( $contests as $contest ) {

			$contest_data = array(
				'name'        => $contest->getAttribute( 'ContestName' ),
				'header'      => $contest->getAttribute( 'ContestHeader' ),
				'description' => $contest->getAttribute( 'ContestDescription' ),
			);

			$unique_contests[ $contest->getAttribute( 'ContestID' ) ] = $contest_data;

		}
		ksort( $unique_contests );

		foreach ( $unique_contests as $contest_id => $contest_data ) {

			$contest_obj              = new stdClass();
			$contest_obj->post_type   = GMR_CONTEST_CPT;
			$contest_obj->post_title  = $contest_data['name'];
			$contest_obj->post_status = 'publish';

			$contest_post_id = wp_insert_post( $contest_obj );
			add_post_meta( $contest_post_id, '_marketron_contest_id', $contest_id );
			add_post_meta( $contest_post_id, 'prizes-desc', $contest_data['description'] );

			WP_CLI::success( sprintf( 'Imported contest %d', $contest_id ) );

		}

	}

	/**
	 * Erase all the contests in the system (use for debugging/testing)
	 *
	 * ## EXAMPLES
	 *
	 *     wp greatermedia_contests erase_contests --marketron=marketron_export_20140723.xml
	 *
	 * @synopsis --marketron=<filename>
	 */
	public function erase_contests( $args, $assoc_args ) {

		$query = new WP_Query( array(
			'post_type'      => GMR_CONTEST_CPT,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => 'any',
		) );

		foreach ( $query->posts as $post_id ) {
			wp_trash_post( $post_id );
		}

		WP_CLI::success( sprintf( 'Deleted %d contests', count( $query->posts ) ) );

	}

}

WP_CLI::add_command( 'greatermedia_contests', 'GreaterMediaContestsWPCLI' );
<?php

// do nothing if WP_CLI is not defined
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

WP_CLI::add_command( 'rackspace', 'Rackspace_CLI_Command' );

class Rackspace_CLI_Command extends WP_CLI_Command {

	/**
	 * Uploads selected attachments.
	 *
	 * @access protected
	 * @param array $post_ids The array of attachment ids to upload.
	 * @param boolean $verbose Determines whether or not to display progress messages.
	 * @param boolean $force_reload Determines whether or not attachment should be re-uploaded.
	 */
	protected function _upload_attachments( $post_ids, $verbose, $force_reload ) {
		// deactivate attachment meta data update hook
		add_filter( 'rackspace_update_attachment_metadata', '__return_false' );

		// upload attachments
		foreach ( $post_ids as $post_id  ) {
			// check that post is attachment
			$post = get_post( $post_id );
			if ( ! $post || 'attachment' != $post->post_type ) {
				$verbose && WP_CLI::warning( sprintf( 'Post %s is not found or is not an attachment.', $post_id ) );
				continue;
			}

			// upload attachment
			$meta_data = wp_get_attachment_metadata( $post_id );
			if ( rackspace_upload_attachment( $post_id, $meta_data, $force_reload ) ) {
				wp_update_attachment_metadata( $post_id, $meta_data );
				$verbose && WP_CLI::success( sprintf( 'Attachment %s has been uploaded.', $post_id ) );
			} else {
				$verbose && WP_CLI::warning( sprintf( 'Attachment %s has not been uploaded.', $post_id ) );
			}
		}

		// activate back attachment meta data update hook
		remove_filter( 'rackspace_update_attachment_metadata', '__return_false' );
	}

	/**
	 * Uploads an attachment to the rackspace CDN storage.
	 *
	 * ## OPTIONS
	 *
	 * --id=<id>
	 * : The attachment id to upload. Could be comma separated list of ids.
	 *
	 * --force-reload
	 * : Determines whether or not to upload already uploaded attachments.
	 *
	 * --verbose
	 * : Determines whether or not to display progress messages.
	 *
	 * ## EXAMPLES
	 *
	 *     wp rackspace upload --id=5
	 *     wp rackspace upload --id=5 --force-reload
	 *     wp rackspace upload --id=5,8,10 --verbose
	 *
	 * @synopsis --id=<id> [--force-reload] [--verbose]
	 *
	 * @access public
	 * @param array $args The array of arguments.
	 * @param array $assoc_args The array of associted arguments.
	 */
	public function upload( $args, $assoc_args ) {
		$verbose = ! empty( $assoc_args['verbose'] );
		$force_reload = ! empty( $assoc_args['force-reload'] );

		$post_ids = isset( $assoc_args['id'] ) ? $assoc_args['id'] : '';
		$post_ids = array_filter( wp_parse_id_list( $post_ids ) );
		if ( empty( $post_ids ) ) {
			$verbose && WP_CLI::warning( 'No id was provided.' );
			return;
		}

		$this->_upload_attachments( $post_ids, $verbose, $force_reload );
	}

	/**
	 * Uploads all attachments to the rackspace CDN storage. This command will
	 * skip already uploaded attachments, if --force-reload argument is not provided.
	 *
	 * ## OPTIONS
	 *
	 * --force-reload
	 * : Determines whether or not to upload already uploaded attachments.
	 *
	 * --verbose
	 * : Determines whether or not to display progress messages.
	 *
	 * ## EXAMPLES
	 *
	 *     wp rackspace upload-all
	 *     wp rackspace upload-all --force-reload
	 *     wp rackspace upload-all --force-reload --verbose
	 *
	 * @synopsis [--force-reload] [--verbose]
	 * @subcommand upload-all
	 *
	 * @access public
	 * @param array $args The array of arguments.
	 * @param array $assoc_args The array of associted arguments.
	 */
	public function upload_all( $args, $assoc_args ) {
		$verbose = ! empty( $assoc_args['verbose'] );
		$force_reload = ! empty( $assoc_args['force-reload'] );

		$paged = 1;
		$query = new WP_Query();

		do {
			$post_ids = $query->query( array(
				'post_type'           => array( 'attachment' ),
				'post_status'         => array( 'inherit', 'private' ),
				'suppress_filters'    => true,
				'paged'               => $paged,
				'posts_per_page'      => 100,
				'ignore_sticky_posts' => true,
				'fields'              => 'ids',
			) );

			if ( ! empty( $post_ids ) ) {
				$this->_upload_attachments( $post_ids, $verbose, $force_reload );
				$verbose && WP_CLI::line( sprintf( '%d page of %d is processed', $paged, $query->max_num_pages ) );
				$paged++;
			}
		} while ( $query->post_count > 0 );
	}

}
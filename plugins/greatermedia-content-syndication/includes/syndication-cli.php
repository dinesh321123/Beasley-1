<?php

class GMR_Syndication_CLI extends WP_CLI_Command {

	/**
	 * Imports posts for all subscriptions with optional date constraints
	 *
	 * ## OPTIONS
	 *
	 * [<start>]
	 * : Import articles that were added on or after this date. YYYY-MM-DD
	 *
	 * [<end>]
	 * : Import articles that were added before this date. YYYY-MM-DD
	 *
	 * [<reload>]
	 * : Forces syndication to reload content.
	 *
	 * ## EXAMPLES
	 *
	 * wp gmr-syndication import
	 *
	 * wp gmr-syndication import 2014-09-30
	 *
	 * @synopsis [<start>] [<end>] [--reload]
	 *
	 * @subcommand import
	 *
	 * @param $args
	 * @param $assoc_args
	 */
	public function import( $args, $assoc_args ) {
		$start = array_shift( $args );
		$end = array_shift( $args );
		$force = ! empty( $assoc_args['reload'] );

		if ( ! empty( $start ) ) {
			if ( ! $this->validate_date( $start ) ) {
				\WP_CLI::error( "Invalid Start Date" );
			}
			$start .= ' 00:00:00';
		}

		if ( ! empty( $end ) ) {
			if ( ! $this->validate_date( $end ) ) {
				\WP_CLI::error( "Invalid End Date" );
			}
			$end .= ' 23:59:59';
		}

		// Do magic here
		$active_subsriptions = BlogData::GetActiveSubscriptions();

		foreach ( $active_subsriptions as $single_subscription ) {

			if ( empty( $start ) ) {
				$start = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, 1, 1, 2012 ) );
			}

			$result = BlogData::QueryContentSite( $single_subscription->ID, $start, $end );

			$taxonomy_names = get_object_taxonomies( 'post', 'objects' );
			$defaults = array(
				'status' => get_post_meta( $single_subscription->ID, 'subscription_post_status', true ),
			);

			foreach ( $taxonomy_names as $taxonomy ) {
				$label = $taxonomy->name;

				// Use get_post_meta to retrieve an existing value from the database.
				$terms = get_post_meta( $single_subscription->ID, 'subscription_default_terms-' . $label, true );
				$terms = explode( ',', $terms );
				$defaults[$label] = $terms;
			}

			$total = count( $result );
			$notify = new \cli\progress\Bar( "Importing $total articles", $total );

			foreach ( $result as $single_post ) {
				if ( ! empty( $single_post['post_obj'] ) ) {
					BlogData::ImportPosts(
							$single_post['post_obj']
							, $single_post['post_metas']
							, $defaults
							, $single_post['featured']
							, $single_post['attachments']
							, $single_post['gallery_attachments']
							, $single_post['galleries']
							, $single_post['term_tax']
							, $force
					);
				}

				$notify->tick();
			}

			$notify->finish();
		}

		\WP_CLI::success( "Finished Import" );
	}

	protected function validate_date( $date ) {
		$pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
		if ( ! preg_match( $pattern, $date ) ) {
			return false;
		}
		return $date;
	}

	/**
	 * Updates syndicated post with the latest version of original post.
	 *
	 * ## OPTIONS
	 *
	 * [<id>]
	 * : Post id to update.
	 *
	 *
	 * ## EXAMPLES
	 *
	 * wp gmr-syndication reload
	 *
	 * wp gmr-syndication reload 20109
	 *
	 * @synopsis [<id>]
	 *
	 * @param $args
	 * @param $assoc_args
	 */
	public function reload( $args, $assoc_args ) {
		if ( empty( $args ) ) {
			\WP_CLI::error( 'Please, provide post id to update.' );
		}

		$syndicated_post = get_post( $args[0] );
		if ( empty( $syndicated_post ) ) {
			\WP_CLI::error( 'The post has not been found.' );
		}

		$info = get_post_meta( $syndicated_post->ID, 'syndication_old_data', true );
		if ( empty( $info ) ) {
			\WP_CLI::error( 'Syndication data has not been found.' );
		}

		$info = unserialize( $info );
		switch_to_blog( $info['blog_id'] );
		$original_post = get_post( $info['id'] );
		$data = BlogData::PostDataExtractor( $syndicated_post->post_type, $original_post );
		restore_current_blog();

		BlogData::ImportPosts(
			$data['post_obj']
			, $data['post_metas']
			, array( 'status' => $syndicated_post->post_status )
			, $data['featured']
			, $data['attachments']
			, $data['gallery_attachments']
			, $data['galleries']
			, $data['term_tax']
			, true
		);

		\WP_CLI::success( 'The post has been updated.' );
	}

}

WP_CLI::add_command( 'gmr-syndication', 'GMR_Syndication_CLI' );

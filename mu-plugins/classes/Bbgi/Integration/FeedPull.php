<?php

namespace Bbgi\Integration;

class FeedPull extends \Bbgi\Module {
	const AUTHOR_KEY = 'bbgi_fp_author_byline';

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_filter( 'fp_post_args', [ $this, 'handle_authors' ] );
		add_filter( 'fp_post_author_id_lookup', '__return_false' ); // we'll handle authors ourselves.
		add_filter( 'the_author_display_name', [ $this, 'maybe_show_byline'] );
	}

	/**
	 * Handle authors in feed pull
	 *
	 * @return integer
	 */
	public function handle_authors( $post_args ) {
		if ( empty( $post_args['post_author'] ) ) {
			return $post_args;
		}

		$post_author = $post_args['post_author'];

		if ( ! is_numeric( $post_author ) ) {
			$user = get_user_by( 'login', sanitize_title( $post_author ) );
			if ( is_a( $user,  \WP_User::class ) ) {
				$post_args['post_author'] = $user->ID;
			}
		}

		// if we can't find a existing author, we'll just store the author byline in post meta.
		unset( $post_args['post_author'] );

		if ( ! isset( $post_args['meta_input'] ) ) {
			$post_args['meta_input'] = [];
		}

		$post_args['meta_input'][ self::AUTHOR_KEY ] = $post_author;

		return $post_args;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $author_meta The value of the metadata.
	 *
	 * @return string
	 */
	public function maybe_show_byline( $author_meta ) {
		$post_id = get_the_ID();

		$author_byline = get_post_meta( $post_id, self::AUTHOR_KEY, true );
		$is_syndicated = get_post_meta( $post_id, 'fp_syndicated_post', true );

		if ( $is_syndicated && ! empty( $author_byline ) ) {
			return $author_byline;
		}

		return $author_meta;
	}
}

<?php

namespace GreaterMedia\Commands;

class OEmbedCommand extends \WP_CLI_Command {

	function clear( $args, $opts ) {
		$post_id = $args[0];
		$meta = get_post_meta( $post_id );

		foreach ( $meta as $key => $value ) {
			if ( strpos( $key, '_oembed' ) === 0 ) {
				delete_post_meta( $post_id, $key );
			}
		}

		\WP_CLI::success( "Cleared OEmbed cache for: $post_id" );
	}

	function show( $args, $opts ) {
		$post_id = $args[0];
		$meta    = get_post_meta( $post_id );
		$found   = false;

		foreach ( $meta as $key => $value ) {
			if ( strpos( $key, '_oembed' ) === 0 ) {
				\WP_CLI::log( "$key: " . print_r( $value, true ) );
				$found = true;
			}
		}

		if ( ! $found ) {
			\WP_CLI::log( "No OEmbeds cached for: $post_id" );
		}
	}

}

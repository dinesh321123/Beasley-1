<?php

namespace GreaterMedia\MyEmma\Commands;

class EmmaCommand extends \WP_CLI_Command {

	function sync( $args, $opts ) {
		if ( ! empty( $opts['all'] ) ) {
			\WP_CLI::confirm(
				'Are you sure you want to sync Emma Groups across the network?'
			);

			$all = true;
		} else {
			$all = false;
		}

		/* in minutes */
		if ( ! empty( $opts['delay'] ) ) {
			$delay = intval( $opts['delay'] );
		} else {
			$delay = 1;
		}

		$syncer = new \GreaterMedia\MyEmma\Sync\EmmaGroupSyncer();
		$syncer->delay = $delay;

		if ( $all ) {
			$syncer->sync_network();
		} else {
			$syncer->sync_blog();
		}
	}

}

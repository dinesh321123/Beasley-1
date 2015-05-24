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

		$syncer = new \GreaterMedia\MyEmma\Sync\EmmaGroupSyncer();

		if ( $all ) {
			$syncer->sync_network();
		} else {
			$syncer->sync_blog();
		}
	}

}

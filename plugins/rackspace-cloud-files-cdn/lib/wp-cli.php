<?php

// do nothing if WP_CLI is not defined
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

WP_CLI::add_command( 'rackspace', 'Rackspace_CLI_Command' );

class Rackspace_CLI_Command extends WP_CLI_Command {

	/**
	 * Uploads an attachment to the rackspace CDN storage.
	 *
	 * @synopsis [--verbose]
	 *
	 * @access public
	 * @param array $args The array of arguments.
	 * @param array $assoc_args The array of associted arguments.
	 */
	public function upload( $args, $assoc_args ) {
		
	}

}
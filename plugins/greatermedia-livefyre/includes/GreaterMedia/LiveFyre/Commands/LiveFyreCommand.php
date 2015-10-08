<?php

namespace GreaterMedia\LiveFyre\Commands;

use GreaterMedia\Gigya\Sync\QueryPaginator;

class LiveFyreCommand extends \WP_CLI_Command {

	public $livefyre_plugin;

	function refresh_profiles() {
		$query     = $this->get_users_query();
		$paginator = $this->get_users_paginator();
		$has_next  = true;
		$cursor    = 0;
		$notify    = null;

		while ( $has_next ) {
			$result   = $paginator->fetch( $query, $cursor );
			$user_ids = $result['results'];
			if ( is_null( $notify ) ) {
				$total_users = $result['total_results'];
				$notify      = new \cli\progress\Bar(
					"Refreshing $total_users Profiles", $total_users
				);
			}

			$this->refresh_user_ids( $user_ids, $notify );

			$has_next = $result['has_next'];
			$cursor   = $result['cursor'];
		}

		$notify->finish();
	}

	private function refresh_user_ids( $user_ids, $notify ) {
		foreach ( $user_ids as $user ) {
			$this->refresh_user_id( $user['UID'] );
			$notify->tick();
		}
	}

	private function refresh_user_id( $user_id ) {
		\WP_CLI::log( "Refreshing User: $user_id" );

		$plugin = $this->get_livefyre_plugin();
		$plugin->sync_livefyre_user( $user_id );
	}

	private function get_users_query() {
		// for testing
		//return 'select * from accounts where profile.age >= 94';

		return 'select * from accounts';
	}

	private function get_users_paginator() {
		return new QueryPaginator( 'profile', 100 );
	}

	private function get_livefyre_plugin() {
		if ( is_null( $this->livefyre_plugin ) ) {
			$this->livefyre_plugin = new \GreaterMedia\LiveFyre\Plugin();
		}

		return $this->livefyre_plugin;
	}

}

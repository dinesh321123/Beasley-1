<?php

namespace GreaterMedia\MyEmma\Sync;

class EmmaGroupSyncer {

	function sync_blog() {
		$blog_meta   = get_blog_details();
		$blog_id     = $blog_meta->blog_id;
		$blog_domain = $blog_meta->domain;
		$groups      = $this->get_emma_groups();

		if ( ! empty( $groups ) ) {
			$total_groups = count( $groups );

			\WP_CLI::log( "Syncing MyEmma Groups: $blog_domain" );

			foreach ( $groups as $group ) {
				$this->sync_emma_group( $group, $blog_meta );
			}
		} else {
			\WP_CLI::warning( "No Emma Groups to Sync for: $blog_domain ($blog_id)" );
		}
	}

	function sync_emma_group( $group, $blog_meta ) {
		$group_name = $group['group_name'];
		$group_id   = $group['group_id'];

		\WP_CLI::log( "  Syncing Group: $group_name ($group_id) ..." );

		$builder         = new EmmaGroupQueryBuilder();
		$member_query_id = $builder->build( $group );

		if ( $member_query_id !== false ) {
			$launcher = new \GreaterMedia\Gigya\Sync\Launcher();
			$launcher->launch( $member_query_id, 'export' );
		}
	}

	function sync_network() {
		$sites = wp_get_sites();

		foreach ( $sites as $index => $site_meta ) {
			$blog_id = $site_meta['blog_id'];

			switch_to_blog( $blog_id );
			$this->sync_blog();
			restore_current_blog();
		}
	}

	function get_emma_groups() {
		$groups = get_option( 'emma_groups' );

		if ( $groups !== false ) {
			$groups = json_decode( $groups, true );

			if ( is_array( $groups ) ) {
				return $groups;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

}

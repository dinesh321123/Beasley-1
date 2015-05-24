<?php

namespace GreaterMedia\MyEmma\Sync;

class EmmaGroupQueryBuilder {

	function build( $group ) {
		$group_id      = $group['group_id'];
		$group_name    = $group['group_name'];
		$title         = "[Sync In Progress] $group_name";
		$existing_post = get_page_by_title( $title, ARRAY_A, 'member_query_preview' );

		if ( is_null( $existing_post ) ) {
			$query = $this->build_newsletter_query( $group_id );
			$post  = array(
				'post_type'   => 'member_query_preview',
				'post_title'  => $title,
				'post_status' => 'publish',
			);

			$post_id = wp_insert_post( $post );

			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta(
					$post_id,
					'member_query_constraints',
					$this->build_newsletter_query( $group_id )
				);

				update_post_meta(
					$post_id,
					'mqsm_email_segment_id',
					$group_id
				);

				return $post_id;
			} else {
				\WP_CLI::warning(
					"Failed to insert Member Query post for $group_name($group_id)"
				);

				return $false;
			}
		} else {
			\WP_CLI::warning(
				"Member Query already running: $group_name($group_id)"
			);

			return false;
		}
	}

	function build_newsletter_query( $group_id ) {
		$constraints = array(
			array(
				'type'        => 'data:subscribedToList',
				'valueType'   => 'enum',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'value'       => strval( $group_id ),
			),
		);

		return json_encode( $constraints );
	}

}

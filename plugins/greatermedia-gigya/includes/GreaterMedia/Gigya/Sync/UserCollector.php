<?php

namespace GreaterMedia\Gigya\Sync;

class UserCollector {

	public $member_query_id;
	public $site_id;

	function __construct( $site_id, $member_query_id ) {
		$this->site_id         = $site_id;
		$this->member_query_id = $member_query_id;
	}

	function clear() {
		$where = array(
			'site_id'         => $this->site_id,
			'member_query_id' => $this->member_query_id,
		);

		$db = $this->get_job_db();
		return $db->delete( 'member_query_users', $where );
	}

	function collect( $users, $store_type ) {
		$total   = count( $users );
		if ( $total === 0 ) {
			return 0;
		}

		$formats = array(
			'%d',
			'%d',
			'%s',
		);

		$format = '( %d, %d, %s, %s )';
		$values = array();
		$db     = $this->get_job_db();
		$query  = 'Insert Into member_query_users ( site_id, member_query_id, store_type, user_id ) Values ';

		for ( $i = 0; $i < $total; $i++ ) {
			$user_id = $users[ $i ];
			$values = array(
				'site_id'         => $this->site_id,
				'member_query_id' => $this->member_query_id,
				'store_type'      => $store_type,
				'user_id'         => $user_id,
			);

			$query .= $this->prepare_values( $db, $values, $format );

			if ( $i < $total - 1 && $total > 1 ) {
				$query .= ', ';
			}
		}

		$query .= ';';
		return $db->query( $query );
	}

	function get_job_db() {
		$instance = TempDatabase::get_instance();
		return $instance->get_db();
	}

	function prepare_values( $db, $values, $format ) {
		return $db->prepare( $format, $values );
	}

}

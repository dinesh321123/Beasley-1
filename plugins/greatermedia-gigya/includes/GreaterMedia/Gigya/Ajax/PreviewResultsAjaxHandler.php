<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\Sync\Launcher;
use GreaterMedia\Gigya\Sync\Sentinel;
use GreaterMedia\Gigya\Sync\CleanupTask;

class PreviewResultsAjaxHandler extends AjaxHandler {

	function get_action() {
		// using old name to preserve backwards compatibility
		return 'preview_member_query';
	}

	function run( $params ) {
		$mode = $params['mode'];

		switch ( $mode ) {
			case 'start':
				return $this->start( $params );

			case 'status':
				return $this->status( $params );

			case 'clear':
				return $this->clear( $params );

			default:
				throw new \Exception( "Unknown preview mode - {$mode}" );
		}
	}

	function start( $params ) {
		$constraints     = $params['constraints'];
		$launcher        = new Launcher();
		$member_query_id = $launcher->preview( $constraints );

		return array( 'member_query_id' => $member_query_id );
	}

	function status( $params ) {
		$member_query_id    = $params['member_query_id'];
		$sentinel           = $this->sentinel_for( $member_query_id );
		$result             = array();
		$result['complete'] = $sentinel->has_completed();

		if ( $result['complete'] ) {
			$preview_results    = $sentinel->get_preview_results();
			$result['progress'] = $sentinel->get_progress();
			$result['total']    = $preview_results['total'];
			$result['users']    = $preview_results['users'];
		} else {
			$result['progress'] = $sentinel->get_progress();
		}

		return $result;
	}

	function clear( $params ) {
		$params = array(
			'mode'            => 'preview',
			'site_id'         => get_current_blog_id(),
			'member_query_id' => $params['member_query_id'],
		);

		$cleanup_task = new CleanupTask();
		$cleanup_task->enqueue( $params );

		return true;
	}

	function sentinel_for( $member_query_id ) {
		$params   = array( 'mode' => 'preview' );
		$sentinel = new Sentinel( $member_query_id, $params );

		return $sentinel;
	}

}

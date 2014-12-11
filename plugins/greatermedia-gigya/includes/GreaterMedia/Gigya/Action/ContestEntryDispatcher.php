<?php

namespace GreaterMedia\Gigya\Action;

class ContestEntryDispatcher {

	function register() {
		add_action(
			'greatermedia_contest_entry_save',
			array( $this, 'did_save_entry' )
		);
	}

	function publish( $action ) {
		save_gigya_action( $action, 'logged_in_user' );
	}

	function did_save_entry( $entry ) {
		$action = $this->action_for_entry( $entry );
		$this->publish( $action );
	}

	function action_for_entry( $entry ) {
		$action               = array();
		$action['actionType'] = 'action:contest';
		$action['actionID']   = strval( $entry->post->post_parent );
		$action['actionData'] = $this->action_data_for_entry_reference( $entry->entry_reference );

		return $action;
	}

	function action_data_for_entry_reference( $entry_reference ) {
		$actionData = array();

		foreach ( $entry_reference as $key => $value ) {
			$item = array(
				'name'  => $key,
				'value' => $value,
			);

			$actionData[] = $item;
		}

		return $actionData;
	}

}

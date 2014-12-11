<?php

namespace GreaterMedia\Gigya\Action;

use GreaterMedia\Gigya\GigyaSession;

class Dispatcher {

	static public $instance;
	static public function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Dispatcher();
		}

		return self::$instance;
	}

	public $page_size = 100;

	function save_action( $action, $user_id = null ) {
		$actions = array( $action );
		$this->save_actions( $actions, $user_id );
	}

	function save_actions( $actions, $user_id = null ) {
		$pages = $this->actions_to_pages( $actions );

		foreach ( $pages as $page ) {
			$this->publish( $page, $user_id );
		}
	}

	function publish( $actions, $user_id = null ) {
		$this->validate_actions( $actions );

		$params    = $this->params_for_page( $actions, $user_id );
		$publisher = new Publisher();

		$publisher->enqueue( $params );
	}

	function validate_actions( $actions ) {
		foreach ( $actions as $action ) {
			$this->validate_action( $action );
		}
	}

	function validate_action( $action ) {
		if ( ! array_key_exists( 'actionType', $action ) ) {
			throw new \Exception( 'Action Validation Failed - actionType must be specified' );
		}

		if ( ! is_string( $action['actionType'] ) ) {
			throw new \Exception( 'Action Validation Failed - actionType must be a string' );
		}

		if ( ! array_key_exists( 'actionID', $action ) ) {
			throw new \Exception( 'Action Validation Failed - actionID must be specified' );
		}

		if ( ! is_string( $action['actionID'] ) ) {
			throw new \Exception( 'Action Validation Failed - actionID must be a string' );
		}

		if ( ! array_key_exists( 'actionData', $action ) ) {
			throw new \Exception( 'Action Validation Failed - actionData must be specified' );
		}

		if ( ! is_array( $action['actionData'] ) ) {
			throw new \Exception( 'Action Validation Failed - actionData must be an array' );
		}

		if ( count( $action['actionData'] ) === 0 ) {
			throw new \Exception( 'Action Validation Failed - actionData must not be empty' );
		}

		foreach ( $action['actionData'] as $item ) {
			if ( ! array_key_exists( 'name', $item ) ) {
				throw new \Exception( 'Action Validation Failed - actionData item must have a name' );
			}

			if ( ! is_string( $item['name'] ) ) {
				throw new \Exception( 'Action Validation Failed - actionData item name must be a string' );
			}

			if ( ! array_key_exists( 'value', $item ) ) {
				throw new \Exception( 'Action Validation Failed - actionData item must have a value' );
			}
		}
	}

	function actions_to_pages( $actions ) {
		return array_chunk( $actions, $this->page_size );
	}

	function params_for_page( $page, $user_id = null ) {
		$params = array();
		$params['actions'] = $page;

		if ( $user_id === 'logged_in_user' || is_null( $user_id ) ) {
			$user_id = $this->get_user_id();
		}

		$params['user_id'] = $user_id;

		return $params;
	}

	function get_user_id() {
		if ( is_gigya_user_logged_in() ) {
			$user_id = get_gigya_user_id();
		} else {
			$user_id = 'guest';
		}

		return $user_id;
	}

}

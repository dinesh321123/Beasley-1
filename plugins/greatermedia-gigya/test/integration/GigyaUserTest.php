<?php

class GigyaUserTest extends \PHPUnit_Framework_TestCase {

	public $user_id = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';

	function setUp() {
		parent::setUp();
	}

	function test_gigya_user() {
		$this->setup_action_schema();

		$action = array(
			'actionType' => 'contestEntry',
			'touchpointID' => rand( 1, 1000 ),
			'actionData' => array(
				'favoriteBand' => 'Rolling Stones',
				'favoriteFood' => 'meatballs',
			)
		);

		$this->add_action_to_user( $action );
		$this->print_user();
	}

	function request_for( $method ) {
		return new \GSRequest(
			'3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6',
			'trS0ufXWUXZ0JBcpr/6umiRfgUiwT7YhJMQSDpUz/p8=',
			$method
		);
	}

	function setup_action_schema() {
		$dataSchema = array(
			'actions' => array(
				'writeAccess' => 'clientCreate',
				'arrayOp' => 'push',
			),
			'dynamicSchema' => true,
		);

		$request = $this->request_for( 'accounts.setSchema' );
		$request->setParam( 'dataSchema', json_encode( $dataSchema ) );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			print_r( 'Success: Created Action Schema' );
		} else {
			print_r( $response->getResponseText() );
		}
	}

	function add_action_to_user( $action ) {
		$request = $this->request_for( 'accounts.setAccountInfo' );
		$request->setParam( 'UID', $this->user_id );
		$request->setParam( 'data', json_encode( $action ) );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			print_r( 'Success: Created Action Object' );
		} else {
			print_r( $response->getResponseText() );
		}
	}

	function print_user() {
		$request = $this->request_for( 'accounts.getAccountInfo' );
		$request->setParam( 'UID', $this->user_id );
		$response = $request->send();

		print_r( $response->getResponseText() );
	}

}

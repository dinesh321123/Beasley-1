<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaSession;

class RegisterAjaxHandler extends AjaxHandler {

	public $mailchimp_api;
	public $list_api;
	public $list_ids = array(
		'VIP Newsletter' => 'fee3354fe3',
		'Big Deal' => '31b32c1392',
		'Birthday Greetings' => '7cde5b1924',
	);

	public function __construct() {
		$opts = array(
			'CURLOPT_FOLLOWLOCATION' => false,
		);
		$this->mailchimp_api = new \Mailchimp( GMR_MAILCHIMP_API_KEY, $opts );
		$this->list_api      = new \Mailchimp_Lists( $this->mailchimp_api );
	}

	public function get_action() {
		return 'register_account';
	}

	public function is_public() {
		return true;
	}

	public function is_async() {
		return true;
	}

	public function run( $params ) {
		$UID       = $params['UID'];
		$listNames = $params['listNames'];
		$account   = $this->account_for( $UID );
		$email     = $account['profile']['email'];

		foreach ( $listNames as $listName ) {
			$list_id   = $this->list_id_for( $listName );
			$this->add_email_to_list( $email, $list_id );
		}
	}

	public function add_async_job( $params ) {
		parent::add_async_job( $params );

		$UID     = $params['UID'];
		$session = GigyaSession::get_instance();
		$session->login( $UID );

		return true;
	}

	public function list_id_for( $name ) {
		return $this->list_ids[ $name ];
	}

	public function add_email_to_list( $email, $list_id ) {
		try {
			return $this->list_api->subscribe(
				$list_id,
				array( 'email' => $email ),
				null,
				'html',
				false
			);
		} catch (\Exception $e) {
			error_log( "Failed to add email to list: $email - $list_id" );
			error_log( $e->getMessage() );
			return false;
		}
	}

	public function account_for( $UID ) {
		$request  = $this->request_for( 'accounts.getAccountInfo', $UID );
		$response = $request->send();
		//error_log ($response->getResponseText());

		if ( $response->getErrorCode() === 0 ) {
			return json_decode( $response->getResponseText(), true );
		} else {
			throw new \Exception( $response->getErrorMessage() );
		}
	}

	public function request_for( $method, $UID ) {
		$request = new GigyaRequest( null, null, $method );
		$request->setParam( 'UID',  $UID );

		return $request;
	}

}

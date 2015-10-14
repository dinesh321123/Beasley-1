<?php

namespace GreaterMedia\Gigya\Schema;

class ActionsSchema {

	public function update( $request ) {
		$schema = json_encode( $this->get_schema() );
		$request->setParam( 'type', 'actions' );
		$request->setParam( 'dataSchema', $schema );

		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return true;
		} else {
			error_log( $response->getResponseText() );
			throw new \Exception(
				'Failed to update actions schema: ' . $response->getErrorMessage()
			);
		}
	}

	public function fetch( $request ) {
		$request->setParam( 'type', 'actions' );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return $response->getResponseText();
		} else {
			error_log( $response->getResponseText() );
			throw new \Exception(
				'Failed to fetch Entry Schema: ' . $response->getErrorMessage()
			);
		}
	}

	public function get_schema() {
		return array(
			'dynamicSchema' => true,
		);
	}

}

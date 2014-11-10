<?php

namespace GreaterMedia\Gigya\Schema;

class AccountSchema {

	public function update( $request ) {
		$profile_schema = json_encode( $this->get_profile_schema() );
		$data_schema    = json_encode( $this->get_data_schema() );

		$request->setParam( 'profileSchema', $profile_schema );
		$request->setParam( 'dataSchema', $data_schema );

		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return true;
		} else {
			error_log( $response->getResponseText() );
			throw new \Exception(
				'Failed to Update Schema: ' . $response->getErrorMessage()
			);
		}
	}

	public function fetch( $request ) {
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return $response->getResponseText();
		} else {
			error_log( $response->getResponseText() );
			throw new \Exception(
				'Failed to Fetch Account Schema: ' . $response->getErrorMessage()
			);
		}
	}

	public function get_profile_schema() {
		return array(
			'fields' => array(
				'nickname' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'gender' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'birthDay' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'birthMonth' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'state' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'city' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'address' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'hometown' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'locale' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'timezone' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'industry' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'languages' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'likes.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'likes.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'likes.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.activities.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.activities.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.activities.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.books.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.books.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.books.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.interests.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.interests.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.interests.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.movies.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.movies.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.movies.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.music.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.music.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.music.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.television.category' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.television.id' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'favorites.television.name' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'phones.type' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
				'phones.number' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				)
			),
		);
	}

	public function get_data_schema() {
		return array(
			'fields' => array(
				'terms' => array(
					'writeAccess' => 'clientModify',
					'required' => false,
				),
			),
			'dynamicSchema' => true,
		);
	}

}

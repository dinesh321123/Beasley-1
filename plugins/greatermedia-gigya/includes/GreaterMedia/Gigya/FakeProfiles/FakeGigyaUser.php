<?php

namespace GreaterMedia\Gigya\FakeProfiles;

class FakeGigyaUser {

	public $properties = array();
	public $response;

	function __construct( $properties = array() ) {
		$this->properties = $properties;
	}

	function seed( $faker ) {
		/* all gigya profile fields */
		/*
		 * birthYear
		 * country
		 * email
		 * firstName
		 * lastName
		 * likes
		 * zip
		 * nickname
		 * gender
		 * age
		 * birthDay
		 * birthMonth
		 * proxyemail
		 * state
		 * city
		 * profileURL
		 * photoURL
		 * thumbnailURL
		 * languages
		 * address
		 * honors
		 * professionalHeadline
		 * industry
		 * specialities
		 * religion
		 * politicalView
		 * interestedIn
		 * relationshipStatus
		 * hometown
		 * bio
		 * username
		 * name
		 * locale
		 * verified
		 * timezone
		 * phones - type, number
		 * publications
		 * patents
		 * certifications
		 * skills
		 * education
		 * work
		 * favorites
		 * lastLoginLocation - city, state, country, coordinates
		 * educationLevel
		 * followersCount
		 * followingCount
		 */

		/* Relevant profile fields
		 *
		 * email
		 * firstName
		 * lastName
		 * nickname
		 * username
		 * gender
		 * age
		 * birthDay
		 * birthMonth
		 * birthYear
		 * zip
		 * state
		 * city
		 * country
		 * address
		 * hometown
		 * phones - type, number
		 * locale
		 * verified
		 * timezone
		 * likes
		 * languages
		 * industry
		 * education
		 * work
		 * favorites
		 * lastLoginLocation - city, state, country, coordinates
		 * educationLevel
		 * followersCount
		 * followingCount
		 */
		$gender = $this->random_gender( $faker );

		$this->properties['email']     = $faker->email;
		$this->properties['firstName'] = $faker->firstName( $gender );
		$this->properties['lastName']  = $faker->lastName;

		// read-only
		$this->properties['nickname'] = strtolower( $this->properties['firstName'] );
		$this->properties['username'] = $this->properties['email'];
		$this->properties['gender']   = $gender;

		// this will give use some under-age users to test
		$date  = $faker->dateTime( '-15 years' );
		$day   = intval( $date->format( 'd' ) );
		$month = intval( $date->format( 'm' ) );
		$year  = intval( $date->format( 'Y' ) );
		$age   = $date->diff( new \DateTime( 'now' ) )->y;

		// age is a computed property in Gigya
		$this->properties['age']        = $age;
		$this->properties['birthDay']   = $day;
		$this->properties['birthMonth'] = $month;
		$this->properties['birthYear']  = $year;

		$this->properties['zip']     = $faker->postcode;
		$this->properties['state']   = $faker->stateAbbr;
		$this->properties['city']    = $faker->city;
		$this->properties['country'] = 'United States';
		$this->properties['address'] = $faker->streetAddress;
		$this->properties['hometown'] = $faker->city;
		$this->properties['phones'] = array(
			array( 'type' => 'home', 'number' => $this->phone_number( $faker ) ),
			array( 'type' => 'cell', 'number' => $this->phone_number( $faker ) ),
		);

		// read-only?
		$this->properties['locale'] = 'en_US';

		// read-only
		$this->properties['verified'] = $faker->boolean( 90 );
		$this->properties['timezone'] = $this->fake_timezone( $faker );
		$this->properties['likes'] = array(
			array( 'category' => 'Color', 'name' => $faker->colorName ),
			array( 'category' => 'Color', 'name' => $faker->colorName ),
			array( 'category' => 'Color', 'name' => $faker->colorName ),
			array( 'category' => 'Color', 'name' => $faker->colorName ),
			array( 'category' => 'State/province/region', 'name' => $faker->city ),
			array( 'category' => 'State/province/region', 'name' => $faker->state ),
		);

		// read-only field
		$this->properties['favorites'] = array(
			'interests' => array(
				array( 'category' => 'Color', 'name' => $faker->colorName ),
				array( 'category' => 'Color', 'name' => $faker->colorName ),
				array( 'category' => 'Color', 'name' => $faker->colorName ),
			),
			'music' => array(
				array( 'category' => 'Rock', 'name' => $faker->colorName ),
				array( 'category' => 'Pop', 'name' => $faker->colorName ),
				array( 'category' => 'Rock', 'name' => $faker->colorName ),
			)
		);
	}

	function phone_number( $faker ) {
		return
			$faker->numberBetween( 100, 999 ) . '-' .
			$faker->numberBetween( 100, 999 ) . '-' .
			$faker->numberBetween( 100, 999 );
	}

	function fake_timezone( $faker ) {
		$num = $faker->numberBetween( 4, 11 );
		return sprintf( 'UTC-%s:00', str_pad( $num, 2, '0', STR_PAD_LEFT ) );
	}

	function random_gender( $faker ) {
		$known = $faker->boolean( 95 );

		if ( $known ) {
			$female = $faker->boolean( 51 );
			if ( $female ) {
				return 'f';
			} else {
				return 'm';
			}
		} else {
			return 'u';
		}
	}

	function save() {
		$regToken           = $this->create_reg_token();
		$finalize           = (bool)$this->properties['verified'];
		$profile_properties = $this->get_profile_properties();

		$request  = $this->request_for( 'accounts.register' );
		$request->setParam( 'email', $this->properties['email'] );
		$request->setParam( 'password', 'foobar123' );
		$request->setParam( 'regToken', $regToken );
		$request->setParam( 'finalizeRegistration', true ); // psuedo finalized for testing
		$request->setParam( 'profile', json_encode( $profile_properties  ) );

		$response = $request->send();
		$this->response = $response;

		if ( $response->getErrorCode() !== 0 ) {
			error_log( $response->getResponseText() );
			throw new \Exception(
				'Failed to Create User: ' . $response->getErrorMessage()
			);
		}

		$json = json_decode( $response->getResponseText(), true );

		// server-generated properties
		$this->properties['UID']                  = $json['UID'];
		$this->properties['createdTimestamp']     = $json['createdTimestamp'];
		$this->properties['lastLoginTimestamp']   = $json['lastLoginTimestamp'];
		$this->properties['lastUpdatedTimestamp'] = $json['lastUpdatedTimestamp'];
		$this->properties['registeredTimestamp']  = $json['registeredTimestamp'];

		// Not syncing to DS.Store for profile data any more
		//$this->create_entries();
	}

	function create_entries() {
		foreach ( $this->get_entries_for_profile() as $entry ) {
			$response = $this->create_entry( $entry );
			$json     = json_decode( $response, true );
			\WP_CLI::success( 'Created Entry: ' . $json['oid'] . ' ' . $entry['entryType_s'] );
		}
	}

	function get_entries_for_profile() {
		$profile_entries = [];

		foreach ( $this->get_entry_fields() as $property ) {
			$entries         = $this->entries_for_property( $property );
			$profile_entries = array_merge( $profile_entries, $entries );
		}

		return $profile_entries;
	}

	function entries_for_property( $property ) {
		$entries     = array();
		$entry_value = $this->properties[ $property ];
		$entry       = array();

		switch ( $property ) {
			case 'likes':
				foreach ( $entry_value as $like ) {
					$entry                   = array();
					$entry['entryType_s']    = 'profile:like';
					$entry['entryFieldID_s'] = $like['category'];
					$entry['entryValue_s']   = $like['name'];

					$entries[] = $entry;
				}
				break;

			case 'favorites':
				foreach ( $entry_value as $favoriteType => $favorite ) {
					foreach ( $favorite as $favoriteItem ) {
						$entry = array();
						$entry['entryType_s']    = 'profile:favorite:' . $favoriteType;
						$entry['entryFieldID_s'] = $favoriteItem['category'];
						$entry['entryValue_s']   = $favoriteItem['name'];

						$entries[] = $entry;
					}
				}
				break;

			default:
				switch ( $property ) {
					case 'firstName':
					case 'lastName':
					case 'nickname':
					case 'gender':
					case 'zip':
					case 'state':
					case 'city':
					case 'country':
					case 'address':
					case 'hometown':
					case 'timezone':
						$entry_type  = 'profile:' . $property;
						$entry_value_suffix = 's';
						break;

					case 'age':
					case 'birthYear':
					case 'birthDay':
					case 'birthMonth':
						$entry_type  = 'profile:' . $property;
						$entry_value_suffix = 'i';
						break;

					case 'createdTimestamp':
					case 'lastLoginTimestamp':
					case 'lastUpdatedTimestamp':
					case 'registeredTimestamp':
						$entry_type = 'system:' . $property;
						$entry_value_suffix = '_i';
						break;

					case 'isActive':
					case 'isRegistered':
					case 'verified':
						$entry_type = 'system:' . $property;
						$entry_value_suffix = 'b';
						break;

					case 'phones':
						foreach ( $entry_value as $phone ) {
							$entry                   = array();
							$entry['entryType_s']    = 'profile:phone';
							$entry['entryFieldID_s'] = $phone['type'];
							$entry['entryValue_s']   = $phone['number'];
							$entries[]               = $entry;
						}

						// returning early here because there are multiple phones
						return $entries;

					default:
						throw new \Exception(
							"Missing Profile property: $property"
						);
				}

				$entry['entryType_s'] = $entry_type;
				$entry[ 'entryValue_' . $entry_value_suffix ] = $entry_value;
				$entries[] = $entry;
		}

		return $entries;
	}

	function create_entry( $entry ) {
		$request = $this->request_for( 'ds.store' );
		$request->setParam( 'type', 'entries' );
		$request->setParam( 'UID', $this->properties['UID'] );
		$request->setParam( 'data', json_encode( $entry ) );
		$request->setParam( 'oid', 'auto' );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return $response->getResponseText();
		} else {
			error_log( $response->getResponseText() );
			throw new \Exception(
				'Failed to Create Entry: ' . json_encode( $entry )
			);
		}
	}

	function create_reg_token() {
		$request  = $this->request_for( 'accounts.initRegistration' );
		$response = $request->send();

		if ( $response->getErrorCode() !== 0 ) {
			throw new \Exception(
				'Init Registration Failed: ' . $response->getErrorMessage()
			);
		}

		$initJson = json_decode( $response->getResponseText(), true );
		return $initJson['regToken'];
	}

	function get_profile_properties() {
		return array_intersect_key(
			$this->properties, array_flip( $this->get_profile_fields() )
		);
	}

	function get_entry_properties() {
		return array_intersect_key(
			$this->properties, array_flip( $this->get_entry_fields() )
		);
	}

	function get_profile_fields() {
		return array(
			'firstName',
			'lastName',
			'gender',
			'birthDay',
			'birthMonth',
			'birthYear',
			'zip',
			'state',
			'city',
			'country',
			'address',
			'hometown',
			'phones',
			'timezone',
			'favorites',
			'likes',
		);
	}

	function get_entry_fields() {
		return array(
			'firstName',
			'lastName',
			'nickname',
			'gender',
			'age',
			'birthDay',
			'birthMonth',
			'birthYear',
			'zip',
			'state',
			'city',
			'country',
			'address',
			'hometown',
			'phones',
			'timezone',
			'favorites',
			'likes',
			'createdTimestamp',
			'lastLoginTimestamp',
			'lastUpdatedTimestamp',
			'registeredTimestamp',
		);
	}

	public function to_json() {
		return json_encode( $this->properties );
	}

	public function get( $property ) {
		return $this->properties[ $property ];
	}

	public function request_for( $method ) {
		// API key is hard coded here to avoid accidentally creating Fake
		// users on a live account
		$request = new \GSRequest(
			'3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6',
			'trS0ufXWUXZ0JBcpr/6umiRfgUiwT7YhJMQSDpUz/p8=',
			$method,
			null,
			true
		);

		return $request;
	}

}

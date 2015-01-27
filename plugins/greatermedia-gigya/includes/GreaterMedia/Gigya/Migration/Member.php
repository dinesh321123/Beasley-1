<?php

namespace GreaterMedia\Gigya\Migration;

class Member {

	public $member_id;
	public $email;
	public $first_name;
	public $last_name;
	public $screen_name;
	public $birthday;
	public $gender;
	public $city;
	public $state;
	public $country;
	public $address1;
	public $address2;
	public $zip_code;
	public $phone_number;
	public $created_on;
	public $modified_on;
	public $permission_newsletter;
	public $marketron_status;
	public $marketron_source;
	public $marketron_source_detail;
	public $member_groups = array();
	public $facebook_member;

	public $parent;
	public $password;

	function __construct( $parent ) {
		$this->parent = $parent;
	}

	function parse( $node ) {
		$attr = $node->attributes;

		$this->parse_field( $attr, 'MemberID', 'member_id' );
		$this->parse_field( $attr, 'EmailAddress', 'email', FILTER_VALIDATE_EMAIL );
		$this->parse_field( $attr, 'FirstName', 'first_name' );
		$this->parse_field( $attr, 'LastName', 'last_name' );
		$this->parse_field( $attr, 'ScreenName', 'screen_name' );
		$this->parse_field( $attr, 'Birthday', 'birthday' );
		$this->parse_field( $attr, 'Gender', 'gender' );
		$this->parse_field( $attr, 'City', 'city' );
		$this->parse_field( $attr, 'State', 'state' );
		$this->parse_field( $attr, 'Country', 'country' );
		$this->parse_field( $attr, 'Address1', 'address1' );
		$this->parse_field( $attr, 'Address2', 'address2' );
		$this->parse_field( $attr, 'ZipCode', 'zip_code' );
		$this->parse_field( $attr, 'PhoneNumber', 'phoneNumber' );
		$this->parse_field( $attr, 'UTCJoinDate', 'created_on' );
		$this->parse_field( $attr, 'UTCDateModified', 'modified_on' );
		$this->parse_field( $attr, 'Permission_Newsletter', 'permission_newsletter' );
		$this->parse_field( $attr, 'Status', 'marketron_status' );
		$this->parse_field( $attr, 'Source', 'marketron_source' );
		$this->parse_field( $attr, 'SourceDetail', 'marketron_source_detail' );

		$this->password = md5( $this->member_id );

		$this->parse_nodes( $node );
	}

	function parse_nodes( $node ) {
		$child_node = $node->firstChild;

		while ( ! is_null( $child_node ) ) {
			switch ( $child_node->nodeName ) {
				case 'MemberGroup':
					$group_name = $this->parse_member_group( $child_node );
					$this->member_groups[] = $group_name;
					break;

				case 'FacebookMember':
					$this->facebook_member = $this->parse_facebook_member( $child_node );
					break;

			}

			$child_node = $child_node->nextSibling;
		}
	}

	function parse_member_group( $node ) {
		$attr = $node->attributes;
		$group_name = $attr->getNamedItem( 'Name' )->nodeValue;

		return $group_name;
	}

	function parse_facebook_member( $node ) {
		$facebook_member = new FacebookMember();
		$facebook_member->parse( $node );

		return $facebook_member;
	}

	function parse_field( $attr, $name, $field, $filter = null ) {
		$item = $attr->getNamedItem( $name );

		if ( ! is_null( $item ) ) {
			$value = $item->nodeValue;

			if ( ! is_null( $filter ) ) {
				$value = filter_var( $value, $filter );
			}

			if ( ! is_null( $value ) ) {
				$this->$field = $value;
			}
		}
	}

	function is_active() {
		return $this->marketron_status === 'Active';
		//return $this->marketron_status !== 'Bouncedback - Terminated';
	}

	function export() {
		$account = array();
		$account['UID'] = $this->member_id;
		$account['compoundHashedPassword'] = '$1$' . $this->password;

		if ( ! is_null( $this->email ) ) {
			$account['email'] = $this->email;
		}

		$account['userInfo'] = $this->export_user_info();

		$identities = $this->export_identities();
		if ( ! is_null( $identities ) ) {
			$account['identities'] = $identities;
		}

		$account['data'] = $this->export_data();

		return $account;
	}

	function export_user_info() {
		$user_info = array();

		$this->export_field( $user_info, 'email', 'email' );
		$this->export_field( $user_info, 'first_name', 'firstName' );
		$this->export_field( $user_info, 'last_name', 'lastName' );
		$this->export_field( $user_info, 'screen_name', 'nickname' );

		$this->export_birthday( $user_info );
		$this->export_gender( $user_info );

		$this->export_field( $user_info, 'city', 'city' );
		$this->export_field( $user_info, 'state', 'state' );
		$this->export_field( $user_info, 'country', 'country' );

		$this->export_address( $user_info );

		$this->export_field( $user_info, 'zip_code', 'zip' );
		$this->export_phone_number( $user_info );

		if ( ! is_null( $this->facebook_member ) ) {
			//$this->export_facebook_likes( $user_info );
		}

		return $user_info;
	}

	function export_identities() {
		if ( ! is_null( $this->facebook_member ) ) {
			$identities = array();
			$identities[] = array(
				'provider' => 'facebook',
				'providerUID' => $this->facebook_member->facebook_id,
			);

			return $identities;
		} else {
			return null;
		}
	}

	function export_data() {
		$data = array();

		$this->export_field( $data, 'marketron_status', 'marketronStatus' );
		$this->export_field( $data, 'marketron_source', 'marketronSource' );
		$this->export_field( $data, 'marketron_source_detail', 'marketronSourceDetail' );
		$this->export_field( $data, 'member_id', 'marketronMemberID' );
		$this->export_member_groups( $data );

		$this->export_created_on( $data );
		$this->export_modified_on( $data );

		return $data;
	}

	function export_field( &$target, $property, $field ) {
		if ( ! is_null( $this->$property ) ) {
			$target[ $field ] = $this->$property;
		}
	}

	function export_birthday( &$target ) {
		$date = date_parse( $this->birthday );

		$target['birthYear']  = $date['year'];
		$target['birthMonth'] = $date['month'];
		$target['birthDay']   = $date['day'];
	}

	function export_gender( &$target ) {
		$target['gender'] = $this->gender === 'Male' ? 'm' : 'f';
	}

	function export_address( &$target ) {
		if ( ! is_null( $this->address1 ) ) {
			$target['address'] = $this->address1;
		}

		if ( ! is_null( $this->address2 ) ) {
			$target['address'] .= ' ' . $this->address2;
		}
	}

	function export_phone_number( &$target ) {
		if ( ! is_null( $this->phone_number ) ) {
			$target['phones'] = array(
				'type' => 'home',
				'number' => $this->phone_number,
			);
		}
	}

	function export_created_on( &$target ) {
		if ( ! is_null( $this->created_on ) ) {
			$date = new \DateTime( $this->created_on );
			$target['registered'] = $date->format( 'c' );
			$target['registeredTimestamp'] = $date->getTimestamp();
		}
	}

	function export_modified_on( &$target ) {
		if ( ! is_null( $this->modified_on ) ) {
			$date = new \DateTime( $this->modified_on );
			$target['lastUpdated'] = $date->format( 'c' );
			$target['lastUpdatedTimestamp'] = $date->getTimestamp();
		}
	}

	function export_member_groups( &$data ) {
		$subscribedToList = array();

		foreach ( $this->member_groups as $group_name ) {
			$subscribedToList[]       = $this->parent->get_member_group_id( $group_name );
			$gigya_field_key          = $this->parent->get_member_group_gigya_key( $group_name );
			$data[ $gigya_field_key ] = true;
		}

		$data['subscribedToList'] = $subscribedToList;
	}

	function export_facebook_likes( &$target ) {
		$likes = $this->facebook_member->likes;

		if ( count( $likes ) > 0 ) {
			$target['likes'] = array();

			foreach ( $likes as $like ) {
				$target['likes'][] = array(
					'id'       => $like->id,
					'category' => $like->category,
					'name'     => $like->name,
				);
			}
		}
	}

}

<?php

namespace GreaterMedia\Gigya\Migration;

class AffinityClub {

	public $active_count;
	public $other_count;
	public $member_groups;
	public $members;
	public $errors;

	public $member_group_ids = array(
		'@Work Network Newsletter' => '2196755',
		"MGK's Discount Deal"      => '2197779',
		'Birthday Greetings'       => '2198803',
		'Listener Appreciation'    => '',
		'Hidden Group'             => '',
	);

	public $member_group_gigya_keys = array(
		'@Work Network Newsletter' => 'workNetworkGroup',
		"MGK's Discount Deal"      => 'discountDealGroup',
		'Birthday Greetings'       => 'birthdayGreetingsGroup',
		'Listener Appreciation'    => 'listenerAppreciationGroup',
		'Hidden Group'             => 'hiddenGroup1',
	);

	function parse( $node ) {
		if ( $node->hasChildNodes() ) {
			$this->errors = array();
			$this->parse_nodes( $node );
		}
	}

	function has_errors() {
		return count( $this->errors ) > 0;
	}

	function parse_nodes( $node ) {
		$child_node = $node->firstChild;

		while ( ! is_null( $child_node ) ) {
			switch ( $child_node->nodeName ) {
				case 'MemberGroups':
					$this->member_groups = $this->parse_member_groups( $child_node );
					break;

				case 'Members':
					$this->members = $this->parse_members( $child_node );
					break;

			}

			$child_node = $child_node->nextSibling;
		}

		return $child_node;
	}

	function parse_member_groups( $node ) {
		$member_groups = array();
		$child_node    = $node->firstChild;

		while ( ! is_null( $child_node ) ) {
			if ( $child_node->nodeName === 'MemberGroup' ) {
				$member_group = new MemberGroup( $node );
				$member_group->parse( $child_node );

				$member_groups[] = $member_group;
			}

			$child_node = $child_node->nextSibling;
		}

		return $member_groups;
	}

	function parse_members( $node ) {
		$attr               = $node->attributes;
		$this->active_count = intval( $attr->getNamedItem( 'ActiveCount' )->nodeValue );
		$this->other_count  = intval( $attr->getNamedItem( 'OtherCount' )->nodeValue );
		$members            = array();
		$child_node         = $node->firstChild;
		$real_count         = 0;

		while ( ! is_null( $child_node ) ) {
			if ( $child_node->nodeName === 'Member' ) {
				try {
					$member = new Member( $this );
					$member->parse( $child_node );

					if ( $member->is_active() ) {
						$members[] = $member;
						$real_count++;

						// WARNING: For debugging only
						if ( $real_count > 1000 ) {
							//break;
						}
					}
				} catch ( \Exception $e ) {
					$tempDoc = new \DOMDocument();
					$tempDoc->preserveWhitespace = false;
					$tempDoc->formatOutput       = true;
					$tempDoc->importNode( $child_node, true );

					$this->errors[] = array(
						'reason' => $e->getMessage(),
						'node' => $child_node->nodeValue,
					);
				}
			}

			$child_node = $child_node->nextSibling;
		}

		return $members;
	}

	function export( $member_ids ) {
		$gigya_export = array(
			'settings' => $this->export_settings(),
			'accounts' => $this->export_accounts( $member_ids )
		);

		$gigya_export['settings']['totalRecords'] = count( $gigya_export['accounts'] );

		$result = array(
			'data' => $gigya_export,
			'errors' => $this->errors,
		);

		return $result;
	}

	function export_accounts( $member_ids ) {
		$accounts = array();
		$keys     = array_flip( $member_ids );

		foreach ( $this->members as $member ) {
			if ( array_key_exists( $member->member_id, $keys ) ) {
				$accounts[] = $member->export();
			}
		}

		return $accounts;
	}

	function export_settings() {
		$settings = array(
			'importFormat'         => 'gigya-users-import',
			'apiKey'               => '3_Of9KvkQkolHsJopV_NQ01gutpi_637ystD7aM0icGOhs5e0GY3zAdmAAlEynmCIh',
			'finalizeRegistration' => true,
			'skipVerification'     => true,
			'totalRecords'         => count( $this->members )
		);

		return $settings;
	}

	function get_member_group_id( $name ) {
		return $this->member_group_ids[ $name ];
	}

	function get_member_group_gigya_key( $name ) {
		return $this->member_group_gigya_keys[ $name ];
	}

}

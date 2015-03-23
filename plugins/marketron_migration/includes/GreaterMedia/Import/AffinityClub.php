<?php

namespace GreaterMedia\Import;

class AffinityClub extends BaseImporter {

	public $member_ids = array();

	function get_tool_name() {
		return 'affinity_club';
	}

	function import_source( $source ) {
		$member_groups = $this->member_groups_from_source( $source );
		$this->import_newsletters( $member_groups );

		$members = $this->members_from_source( $source );
		$this->import_members( $members );
	}

	function import_newsletters( $member_groups ) {
		$config = $this->container->config;

		foreach ( $member_groups as $member_group ) {
			$marketron_name = $this->import_string( $member_group['MemberGroupName'] );
			$description    = $this->import_string( $member_group['Description'] );

			$config->update_newsletter( $marketron_name, $description );
		}

		$config_loader = $this->container->config_loader;
		$config_loader->load_myemma_groups();
	}

	function import_members( $members ) {
		$entity       = $this->get_entity( 'gigya_user' );
		$total        = count( $members );
		$msg          = "Importing $total Members";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$user_count   = 0;
		$facebook_count = 0;

		foreach ( $members as $member ) {
			$member_id  = $this->import_string( $member['MemberID'] );
			$gigya_user = $this->gigya_user_from_member( $member );

			$entity->add( $gigya_user );
			$user_count++;

			if ( ! empty( $gigya_user['facebook_id'] ) ) {
				$facebook_count++;
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		\WP_CLI::success( 'Imported ' . $user_count . ' Users of ' . $total );
		\WP_CLI::success( 'Facebook User Count: ' . $facebook_count );
	}

	function member_groups_from_source( $source ) {
		return $source->MemberGroups->MemberGroup;
	}

	function members_from_source( $source ) {
		return $source->Members->Member;
	}

	function gigya_user_from_member( $member ) {
		$user                = array();
		$user['id']          = $this->import_string( $member['MemberID'] );
		$user['email']       = $this->import_string( $member['EmailAddress'] );
		$user['first_name']  = $this->import_string( $member['FirstName'] );
		$user['last_name']   = $this->import_string( $member['LastName'] );
		$user['nick_name']   = $this->import_string( $member['ScreenName'] );
		$user['gender']      = $this->import_string( $member['Gender'] );
		$user['city']        = $this->import_string( $member['City'] );
		$user['state']       = $this->import_string( $member['State'] );
		$user['country']     = $this->import_string( $member['Country'] );
		$user['address']     = $this->import_string( $member['Address1'] );
		$user['zip']         = $this->import_string( $member['ZipCode'] );
		$user['birthday']    = $this->import_birthday( $member );
		$user['newsletters'] = $this->newsletters_from_member( $member );
		$user['facebook_id'] = $this->facebook_id_from_member( $member );

		$user['marketron_status']    = $this->import_string( $member['Status'] );
		$user['marketron_source']    = $this->import_string( $member['Source'] );
		$user['marketron_member_id'] = $this->import_string( $member['MemberID'] );
		$user['registered']          = $this->import_string( $member['UTCJoinDate'] );
		$user['last_updated']        = $this->import_string( $member['UTCDateModified'] );

		return $user;
	}

	function import_birthday( $member ) {
		if ( ! empty( $member['Birthday'] ) ) {
			$birthday = $this->import_string( $member['Birthday'] );
			return date_parse( $birthday );
		} else {
			return null;
		}
	}

	function member_groups_from_member( $member ) {
		return $member->MemberGroup;
	}

	function newsletters_from_member( $member ) {
		$member_groups = $this->member_groups_from_member( $member );
		$newsletters   = array();

		if ( ! empty( $member_groups ) ) {
			foreach ( $member_groups as $member_group ) {
				$newsletters[] = $this->import_string( $member_group['Name'] );
			}
		}

		return $newsletters;
	}

	function facebook_id_from_member( $member ) {
		$facebook_member = $member->FacebookMember;

		if ( ! empty( $facebook_member ) ) {
			return $this->import_string( $facebook_member['FacebookID'] );
		} else {
			return null;
		}
	}

}

<?php

namespace GreaterMedia;

class EmmaGroupCreator {

	public $container;
	public $emma_groups;

	function create_and_save() {
		$this->container->opts['repair'] = true;

		$config_file = $this->container->config->get_config_file();
		$config_data = $this->container->config->get_config_data();
		if ( ! empty( $config_data['myemma']['newsletters'] ) ) {
			\WP_CLI::warning( "Newsletters already exist in config: $config_file" );
			\WP_CLI::confirm( 'Are you sure you want to overwrite the Newsletters?' );
		}

		$affinity_club_tool = $this->container->tool_factory->build( 'affinity_club' );
		$affinity_club_tool->load( false );

		$source        = $affinity_club_tool->sources[0];
		$member_groups = $source->MemberGroups->MemberGroup;
		$newsletters   = array();

		\WP_CLI::log( 'Found ' . count( $member_groups ) . ' Member Groups' );

		foreach ( $member_groups as $member_group ) {
			$newsletter = $this->newsletter_from_member_group( $member_group );

			if ( ! empty( $newsletter ) ) {
				$newsletters[] = $newsletter;
			}
		}

		if ( ! empty( $newsletters ) ) {
			$this->save( $newsletters );
		} else {
			\WP_CLI::warning( 'No Newsletters were created.' );
		}
	}

	function newsletter_from_member_group( $member_group ) {
		$marketron_name  = trim( (string) $member_group['MemberGroupName'] );
		$description     = trim( (string) $member_group['Description'] );
		$emma_name       = '[Opt-In] ' . $marketron_name;
		$gigya_field_key = $this->to_gigya_field_key( $marketron_name );
		$emma_group_id   = $this->create_emma_group( $emma_name, $gigya_field_key );
		$active          = filter_var( (string) $member_group['IsActive'], FILTER_VALIDATE_BOOLEAN );

		if ( ! empty( $emma_group_id ) ) {
			$newsletter                    = array();
			$newsletter['marketron_name']  = $marketron_name;
			$newsletter['emma_name']       = $emma_name;
			$newsletter['emma_group_id']   = $emma_group_id;
			$newsletter['gigya_field_key'] = $gigya_field_key;
			$newsletter['description']     = $description;
			$newsletter['active']          = $active;

			return $newsletter;
		} else {
			return false;
		}
	}

	function create_emma_group( $group_name, $field_key ) {
		\WP_CLI::log( "Creating MyEmma Group: $group_name ($field_key) ..." );

		$task = new \GreaterMedia\MyEmma\Ajax\AddMyEmmaGroup();

		try {
			$opts     = $this->get_emma_api_config();
			$group_id = $task->create_group( $group_name, $opts );

			if ( ! empty( $group_id ) ) {
				$task->update_schema( $field_key );

				\WP_CLI::success( "Created MyEmma Group: $group_name ($field_key) - $group_id" );
				return $group_id;
			} else {
				\WP_CLI::error(
					"Failed to created Group: $group_name - empty result"
				);

				return false;
			}
		} catch ( Exception $e ) {
			$group_id = null;
			\WP_CLI::error(
				"Failed to created Group: $group_name - " . $e->getMessage()
			);

			return false;
		}
	}

	function to_gigya_field_key( $emma_name ) {
		$emma_name       = strtolower( $emma_name );
		$gigya_field_key = preg_replace( '/[^a-z]+/', '', $emma_name );
		$gigya_field_key = preg_replace( '/group$/', '', $gigya_field_key );
		$gigya_field_key = 'emma' . ucfirst( $gigya_field_key ) . 'Group';

		return $gigya_field_key;
	}

	function save( $newsletters ) {
		$config_data = $this->container->config->get_config_data();
		$config_data['myemma']['newsletters'] = $newsletters;

		$json = json_encode(
			$config_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
		);

		$config_file = $this->container->config->get_config_file();
		file_put_contents( $config_file, $json );

		\WP_CLI::success( "Saved Newsletters to config: $config_file" );
	}

	function get_emma_api_config() {
		$config_data = $this->container->config->get_config_data();
		return $config_data['myemma'];
	}

}

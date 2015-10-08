<?php

namespace GreaterMedia\MyEmma\Webhooks;

use GreaterMedia\MyEmma\EmmaAPI;

class MessageOpen extends Webhook {

	public $groups;
	public $mailing_meta;
	public $params;

	function get_event_name() {
		return 'message_open';
	}

	function run( $params ) {
		$this->params = $params;

		$mailing_id     = $this->get_mailing_id( $params );
		$emma_member_id = $this->get_emma_member_id( $params );
		$gigya_user_id  = $this->get_gigya_user_id( $emma_member_id );
		$subject        = $this->get_subject( $params );
		$event_name     = $this->get_event_name();

		$groups_to_log  = array();
		$member_queries = $this->get_member_queries_for_mailing( $mailing_id );
		$static_groups  = $this->get_static_groups_for_mailing( $mailing_id );
		$groups_to_log  = array_merge( $groups_to_log, $member_queries, $static_groups );

		if ( count( $groups_to_log ) === 0 ) {
			$groups_to_log[] = $mailing_id;
		}

		$this->save_message_actions(
			"email_{$event_name}", $groups_to_log, $subject, $gigya_user_id
		);
	}

	function get_mailing_id( $params ) {
		return $params['data']['mailing_id'];
	}

	function get_subject( $params ) {
		return $params['data']['subject'];
	}

	function get_mailing_meta( $mailing_id ) {
		if ( is_null( $this->mailing_meta ) ) {
			$api                = $this->get_emma_api();
			$response           = $api->mailingsGetById( $mailing_id );
			$json               = json_decode( $response, true );
			$this->mailing_meta = $json;
		}

		return $this->mailing_meta;
	}

	function get_groups_for_mailing( $mailing_id ) {
		if ( is_null( $this->groups ) ) {
			$mailing_id = intval( $mailing_id );

			try {
				$meta   = $this->get_mailing_meta( $mailing_id );
				$groups = $meta['recipient_groups'];

				$this->groups = array_column( $groups, 'member_group_id' );
			} catch ( \Emma_Invalid_Response_Exception $e ) {
				$this->groups = array();
			}
		}

		return $this->groups;
	}

	function get_meta_query_for_groups( $groups ) {
		$meta_query = array( 'relation' => 'OR' );

		foreach ( $groups as $group_id ) {
			$criteria = array(
				'key'     => 'mqsm_email_segment_id',
				'value'   => strval( $group_id ),
				'compare' => '=',
			);

			$meta_query[] = $criteria;
		}

		return $meta_query;
	}

	function get_query_args_for_groups( $groups ) {
		return array(
			'meta_query' => $this->get_meta_query_for_groups( $groups ),
		);
	}

	function get_member_queries_for_groups( $groups ) {
		$args           = $this->get_query_args_for_groups( $groups );
		$query          = new \WP_Query( $args );
		$member_queries = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$member_queries[] = $query->post->ID;
		}

		return $member_queries;
	}

	function get_member_queries_for_mailing( $mailing_id ) {
		$groups = $this->get_groups_for_mailing( $mailing_id );

		if ( count( $groups ) > 0 ) {
			return $this->get_member_queries_for_groups( $groups );
		} else {
			return array();
		}
	}

	function get_static_groups_for_mailing( $mailing_id ) {
		$groups = $this->get_groups_for_mailing( $mailing_id );

		$emma_groups    = get_option( 'emma_groups' );
		$emma_groups    = json_decode( $emma_groups, true );

		if ( ! is_array( $emma_groups ) ) {
			$emma_groups = array();
		}

		$emma_group_ids = array_column( $emma_groups, 'group_id' );
		$static_groups  = array();

		foreach ( $groups as $group_id ) {
			if ( in_array( $group_id, $emma_group_ids ) ) {
				$static_groups[] = $group_id;
			}
		}

		return $static_groups;
	}

	function get_action_to_save( $type, $group_id, $subject ) {
		$action = array(
			'actionType' => 'action:' . $type,
			'actionID'   => strval( $group_id ),
			'actionData' => array(
				array( 'name' => 'subject', 'value' => $subject ),
			)
		);

		return $action;
	}

	function get_actions_to_save( $event_name, $groups, $subject ) {
		$actions = array();

		foreach ( $groups as $group_id ) {
			$actions[] = $this->get_action_to_save(
				$event_name,
				$group_id,
				$subject
			);
		}

		return $actions;
	}

	function save_message_actions( $event_name, $groups, $subject, $user_id ) {
		if ( count( $groups ) > 0 ) {
			$actions = $this->get_actions_to_save(
				$event_name,
				$groups,
				$subject
			);

			save_gigya_actions( $actions, $user_id );

			return $actions;
		} else {
			return false;
		}
	}

	function save_mailing_action( $event_name, $mailing_id, $subject, $user_id ) {
		$action = $this->get_action_to_save(
			$event_name,
			$mailing_id,
			$subject
		);

		$actions = array( $action );
		save_gigya_actions( $actions, $user_id );
	}

}

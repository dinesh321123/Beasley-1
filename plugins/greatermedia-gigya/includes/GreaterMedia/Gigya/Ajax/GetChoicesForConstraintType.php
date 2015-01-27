<?php

namespace GreaterMedia\Gigya\Ajax;

class GetChoicesForConstraintType extends AjaxHandler {

	function get_action() {
		return 'get_choices_for_constraint_type';
	}

	function run( $params ) {
		$type = $params['type'];

		switch ( $type ) {
			case 'contest':
				return $this->get_contest_choices();

			case 'survey':
				return $this->get_survey_choices();

			case 'email_engagement':
				return $this->get_email_group_choices();

			case 'member_query':
			case 'member_query_message_open':
			case 'member_query_message_click':
				return $this->get_member_query_choices();

			case 'static_group':
			case 'static_group_message_open':
			case 'static_group_message_click':
				return $this->get_static_group_choices();

			default:
				return [];

		}
	}

	function get_choices_for_post_type( $post_type, $post_status = 'publish', $required_meta = null ) {
		// TODO: pagination
		$args = array( 'post_type' => $post_type, 'post_status' => $post_status );
		$query   = new \WP_Query( $args );
		$posts   = $query->get_posts();
		$choices = [];

		foreach ( $posts as $post ) {
			if ( $required_meta ) {
				$post_meta = get_post_meta( $post->ID, $required_meta, true );

				if ( $post_meta === '' ) {
					continue;
				}
			}

			$choices[] = array(
				'label' => $post->post_title,
				'value' => $post->ID,
			);
		}

		return $choices;
	}

	function get_contest_choices() {
		return $this->get_choices_for_post_type(
			'contest', 'publish', 'embedded_form'
		);
	}

	function get_survey_choices() {
		return $this->get_choices_for_post_type(
			'survey', 'publish', 'survey_embedded_form'
		);
	}

	function get_member_query_choices() {
		return $this->get_choices_for_post_type(
			'member_query', 'publish', 'mqsm_email_segment_id'
		);
	}

	function get_static_group_choices() {
		$emma_groups = get_option( 'emma_groups' );
		$emma_groups = json_decode( $emma_groups, true );
		$choices     = array();

		foreach ( $emma_groups as $group ) {
			$choices[] = array(
				'label' => $group['group_name'],
				'value' => strval( $group['group_id'] )
			);
		}

		return $choices;
	}

	function get_email_group_choices() {
		$choices = array(
			'static' => $this->get_static_group_choices(),
			'member_query' => $this->get_member_query_choices(),
		);

		return $choices;
	}

}

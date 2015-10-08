<?php

namespace GreaterMedia\Gigya;

class MemberQueryTest extends \WP_UnitTestCase {

	public $query;
	public $post_id;

	function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();
		$this->query   = new MemberQuery( $this->post_id );
	}

	function query_for( $content ) {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, 'member_query_constraints', $content );

		$query   = new MemberQuery( $post_id );

		return $query;
	}

	function test_it_can_store_a_wp_post_id() {
		$post_id = $this->factory->post->create();
		$query   = new MemberQuery( $post_id );
		$this->assertEquals( $post_id, $query->post_id );
	}

	function test_it_can_find_content_from_wp_post_meta() {
		$query = $this->query_for( '{"a":1}' );
		$this->assertEquals( '{"a":1}', $query->content_for( $query->post_id ) );
	}

	function test_it_can_parse_empty_content() {
		$actual = $this->query->parse( '' );
		$this->assertEmpty( $actual['constraints'] );
	}

	function test_it_knows_a_member_querys_constraints() {
		$content = '[ "a", "b", "c" ]';
		$query = $this->query_for( $content );
		$expected = array( 'a', 'b', 'c' );

		$this->assertEquals( $expected, $query->get_constraints() );
	}

	function test_it_knows_suffixes_for_gigya_types() {
		$query = $this->query_for( '[]' );
		$this->assertEquals( '_i', $query->suffix_for( 'integer' ) );
		$this->assertEquals( '_f', $query->suffix_for( 'float' ) );
		$this->assertEquals( '_s', $query->suffix_for( 'string' ) );
		$this->assertEquals( '_t', $query->suffix_for( 'text' ) );
		$this->assertEquals( '_b', $query->suffix_for( 'boolean' ) );
		$this->assertEquals( '_d', $query->suffix_for( 'date' ) );
	}

	function test_it_uses_string_suffix_for_unknown_types() {
		$query = $this->query_for( '[]' );
		$this->assertEquals( '_s', $query->suffix_for( 'foo' ) );
	}

	function test_it_knows_name_of_a_field() {
		$actual = $this->query->field_name_for( 'entryType', 'string' );
		$this->assertEquals( 'data.actions.entryType_s', $actual );
	}

	function test_it_can_build_clause_for_constraint() {
		$constraint = array(
			'type'        => 'profile:city',
			'operator'    => 'contains',
			'conjunction' => 'and',
			'valueType'   => 'string',
			'value'       => 'New York',
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "profile.city contains 'New York'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_data_constraint() {
		$constraint = array(
			'type'        => 'data:comment_count',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'integer',
			'value'       => 100,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "data.comment_count = 100";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_true_comment_status_constraint() {
		$constraint = array(
			'type'        => 'data:comment_status',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'boolean',
			'value'       => true,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = 'data.comment_count > 0';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_false_comment_status_constraint() {
		$constraint = array(
			'type'        => 'data:comment_status',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'boolean',
			'value'       => false,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = 'data.comment_count = 0 or data.comment_count is null';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_comment_date_constraint() {
		$constraint = array(
			'type'         => 'action:comment_date',
			'operator'     => 'greater than',
			'conjunction'  => 'or',
			'valueType'    => 'string',
			'value'        => '01/01/2014',
		);

		$date = \DateTime::createFromFormat(
			'm/d/Y', $constraint['value'], new \DateTimeZone( 'UTC' )
		);

		$timestamp = $date->getTimestamp();

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "data.actions.actionType = 'action:comment' and data.actions.actionData.name = 'timestamp' and data.actions.actionData.value_i > {$timestamp}";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_record_constraint() {
		$constraint = array(
			'type'         => 'record:contest',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'string',
			'value'        => 'New York',
			'entryTypeID'  => 100,
			'entryFieldID' => 200,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "data.actions.actionType = 'action:contest' and data.actions.actionID = '100' and data.actions.actionData.name = '200' and data.actions.actionData.value_s = 'New York'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_system_constraint() {
		$constraint = array(
			'type'         => 'system:verified',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'boolean',
			'value'        => true,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = 'verified = true';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_likes_constraint_with_custom_category() {
		$constraint = array(
			'type'         => 'profile:likes',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'string',
			'value'        => 'Xbox One',
			'category'     => 'Games/toys',
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "profile.likes.category contains 'Games/toys' and profile.likes.name = 'Xbox One'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_likes_constraint_with_any_category() {
		$constraint = array(
			'type'         => 'profile:likes',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'string',
			'value'        => 'Xbox One',
			'category'     => 'Any Category',
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "profile.likes.name = 'Xbox One'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_favorites_constraint_with_custom_category() {
		$constraint = array(
			'type'         => 'profile:favorites',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'string',
			'value'        => 'Beetles',
			'category'     => 'Musician/Band',
			'favoriteType' => 'music',
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "profile.favorites.music.category contains 'Musician/Band' and profile.favorites.music.name = 'Beetles'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_favorites_constraint_with_any_category() {
		$constraint = array(
			'type'         => 'profile:favorites',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'string',
			'value'        => 'Beetles',
			'category'     => 'Any Category',
			'favoriteType' => 'music',
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "profile.favorites.music.name = 'Beetles'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_query_for_profile_and_record_contraints() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'New York',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$actual = $this->query->clause_for( $constraints );
		$expected = "profile.city contains 'New York' and data.actions.actionType = 'action:contest' and data.actions.actionID = '100' and data.actions.actionData.name = '200' and data.actions.actionData.value_s = 'New York'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_query_from_constraints() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'New York',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$this->query = $this->query_for( json_encode( $constraints ) );
		$actual = $this->query->to_gql();
		$expected = "select * from accounts where profile.city contains 'New York' and data.actions.actionType = 'action:contest' and data.actions.actionID = '100' and data.actions.actionData.name = '200' and data.actions.actionData.value_s = 'New York'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_save_constraints_in_post_meta() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'New York',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$_POST['constraints'] = json_encode( $constraints );

		$this->post_id = $this->factory->post->create();
		$this->query = new MemberQuery( $this->post_id );
		$this->query->build_and_save();

		$new_query = new MemberQuery( $this->query->post_id );
		$actual = $new_query->get_constraints();
		$this->assertEquals( $constraints, $actual );
	}

	function test_it_builds_empty_string_if_constraints_are_empty() {
		$constraints = array();

		$this->query = $this->query_for( json_encode( $constraints ) );
		$actual = $this->query->to_gql();
		$this->assertEquals( '', $actual );
	}

	/* TODO: Reorganize */
	/* action constraint Tests */
	function test_it_knows_suffix_for_data_store_field_name() {
		$actual = $this->query->data_store_field_name_for( 'actionType', 'string' );
		$this->assertEquals( 'data.actions.actionType_s', $actual );
	}

	function test_it_knows_store_name_for_profile_store_type() {
		$actual = $this->query->store_name_for_type( 'profile' );
		$this->assertEquals( 'accounts', $actual );
	}

	function test_it_knows_store_name_for_data_store_type() {
		$actual = $this->query->store_name_for_type( 'data_store' );
		$this->assertEquals( 'actions', $actual );
	}

	function test_it_can_build_clause_for_action_constraint() {
		$constraint = array(
			'type'         => 'action:contest',
			'operator'     => 'equals',
			'conjunction'  => 'and',
			'valueType'    => 'string',
			'value'        => 'New York',
			'actionTypeID'  => 100,
			'actionFieldID' => 200,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = "data.actions.actionType_s = 'action:contest' and data.actions.actionTypeID_i = 100 and data.actions.actionFieldID_s = '200' and data.actions.actionValue_s = 'New York'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_profile_query_from_constraints() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
		);

		$actual = $this->query->constraints_to_query( $constraints, 'profile' );
		$expected = "select * from accounts where profile.city contains 'New York' or profile.city = 'Los Angeles'";

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_data_store_query_from_constraints() {
		$constraints = array(
			array(
				'type'         => 'action:contest',
				'operator'     => 'equals',
				'conjunction'  => 'or',
				'valueType'    => 'string',
				'value'        => 'foo',
				'actionTypeID'  => 100,
				'actionFieldID' => '200',
			),
			array(
				'type'         => 'action:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'bar',
				'actionTypeID'  => 101,
				'actionFieldID' => '201',
			),
		);

		$actual = $this->query->constraints_to_query( $constraints, 'data_store' );
		$expected = "select * from actions where data.actions.actionType_s = 'action:contest' and data.actions.actionTypeID_i = 100 and data.actions.actionFieldID_s = '200' and data.actions.actionValue_s = 'foo' or data.actions.actionType_s = 'action:contest' and data.actions.actionTypeID_i = 101 and data.actions.actionFieldID_s = '201' and data.actions.actionValue_s = 'bar'";

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_group_constraints_by_store_type() {
		$constraints = array(
			array(
				'type' => 'profile:city',
			),
			array(
				'type' => 'system:verified',
			),
			array(
				'type' => 'action:contest',
			),
			array(
				'type' => 'action:foo',
			),
		);

		$actual = $this->query->group_constraints( $constraints );
		$expected = array(
			'profile' => array(
				array( 'type' => 'profile:city' ),
				array( 'type' => 'system:verified' ),
			),
			'data_store' => array(
				array( 'type' => 'action:contest' ),
				array( 'type' => 'action:foo' ),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_subqueries_from_constraints() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'or',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => '200',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'bar',
				'entryTypeID'  => 101,
				'entryFieldID' => '201',
			),
		);

		$this->query = $this->query_for( json_encode( $constraints ) );
		$actual = $this->query->to_subqueries();

		$this->assertEquals( 2, count( $actual ) );

		$expected = "select * from accounts where profile.city contains 'New York' or profile.city = 'Los Angeles'";
		$this->assertEquals( 'profile', $actual[0]['store_type'] );
		$this->assertEquals( $expected, $actual[0]['query'] );

		$expected = "select * from actions where data.actions.actionType = 'action:contest' and data.actions.actionID = '100' and data.actions.actionData.name = '200' and data.actions.actionData.value_s = 'foo' or data.actions.actionType = 'action:contest' and data.actions.actionID = '101' and data.actions.actionData.name = '201' and data.actions.actionData.value_s = 'bar'";
		$this->assertEquals( 'data_store', $actual[1]['store_type'] );
		$this->assertEquals( $expected, $actual[1]['query'] );
	}

	function test_it_can_identify_an_and_subquery_conjunction() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'or',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => '200',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'bar',
				'entryTypeID'  => 101,
				'entryFieldID' => '201',
			),
		);

		$this->query = $this->query_for( json_encode( $constraints ) );
		$actual = $this->query->get_subquery_conjunction();

		$this->assertEquals( 'and', $actual );
	}

	function test_it_can_identify_an_or_subquery_conjunction() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'or',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => '200',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'bar',
				'entryTypeID'  => 101,
				'entryFieldID' => '201',
			),
		);

		$this->query = $this->query_for( json_encode( $constraints ) );
		$actual = $this->query->get_subquery_conjunction();

		$this->assertEquals( 'or', $actual );
	}

	function test_it_can_identify_an_any_subquery_conjunction() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
		);

		$this->query = $this->query_for( json_encode( $constraints ) );
		$actual = $this->query->get_subquery_conjunction();

		$this->assertEquals( 'any', $actual );
	}

	function test_it_can_store_member_query_under_member_query_preview_post_type() {
		$post_type = new MemberQueryPostType();
		$post_type->register();

		$params = array(
			'post_name' => 'foo',
			'post_status' => 'draft',
			'post_type' => 'member_query_preview',
		);

		$post_id = $this->factory->post->create( $params );

		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
		);

		$json = json_encode( $constraints );
		$member_query = new MemberQuery( $post_id, $json );
		$member_query->save( $json );

		$member_query = new MemberQuery( $post_id );
		$this->assertEquals( $constraints, $member_query->get_constraints() );
	}

	/* inferred status constraint */
	function test_it_can_build_clause_for_true_status_constraint() {
		$constraint = array(
			'type'        => 'data:comment_status',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'boolean',
			'value'       => true,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = 'data.comment_count > 0';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_clause_for_false_status_constraint() {
		$constraint = array(
			'type'        => 'data:comment_status',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'boolean',
			'value'       => false,
		);

		$actual = $this->query->clause_for_constraint( $constraint );
		$expected = 'data.comment_count = 0 or data.comment_count is null';
		$this->assertEquals( $expected, $actual );
	}

	/* enum constraint */
	function test_it_can_build_clause_for_list_constraint() {
		$constraint = array(
			'type'        => 'data:contest_list',
			'operator'    => 'contains',
			'conjunction' => 'and',
			'valueType'   => 'enum',
			'value'       => '123',
		);

		$actual   = $this->query->clause_for_constraint( $constraint );
		$expected = "data.contest_list contains '123'";
		$this->assertEquals( $expected, $actual );
	}

	/* email engagement tally */
	function test_it_can_build_email_engagement_tally_constraint() {
		$constraint = array(
			'type'        => 'data:email_engagement_tally',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'integer',
			'value'       => 10,
			'event_name'  => 'message_click',
		);

		$actual   = $this->query->clause_for_constraint( $constraint );
		$expected = 'data.email_message_click_count = 10';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_email_engagement_tally_constraint_for_zero_value() {
		$constraint = array(
			'type'        => 'data:email_engagement_tally',
			'operator'    => 'equals',
			'conjunction' => 'and',
			'valueType'   => 'integer',
			'value'       => 0,
			'event_name'  => 'message_click',
		);

		$actual   = $this->query->clause_for_constraint( $constraint );
		$expected = 'data.email_message_click_count is null';
		$this->assertEquals( $expected, $actual );
	}

	/* email engagement */
	function test_it_can_build_email_engagement_constraint() {
		$constraint = array(
			'type'        => 'data:email_engagement',
			'operator'    => 'contains',
			'conjunction' => 'and',
			'valueType'   => 'enum',
			'value'       => '123',
			'event_name'  => 'message_click',
		);

		$actual   = $this->query->clause_for_constraint( $constraint );
		$expected = "data.email_message_click_list contains '123'";
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_email_engagement_constraint_for_any_value() {
		$constraint = array(
			'type'        => 'data:email_engagement',
			'operator'    => 'contains',
			'conjunction' => 'and',
			'valueType'   => 'enum',
			'value'       => 'any',
			'event_name'  => 'message_click',
		);

		$actual   = $this->query->clause_for_constraint( $constraint );
		$expected = "data.email_message_click_count > 0";
		$this->assertEquals( $expected, $actual );
	}
}

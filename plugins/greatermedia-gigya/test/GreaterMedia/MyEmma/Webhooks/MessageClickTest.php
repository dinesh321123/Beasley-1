<?php

namespace GreaterMedia\MyEmma\Webhooks;

class MessageClickTest extends \WP_UnitTestCase {

	public $webhook;

	function setUp() {
		parent::setUp();

		$this->webhook = new MessageClick();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_an_event_name() {
		$actual = $this->webhook->get_event_name();
		$this->assertEquals( 'message_click', $actual );
	}

	function test_it_knows_the_link_that_was_clicked() {
		$this->webhook->params = array(
			'data' => array( 'link_id' => 'foo' )
		);

		$actual = $this->webhook->get_clicked_link();
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_knows_meta_for_link_that_was_clicked() {
		$this->webhook->params = array(
			'data' => array( 'link_id' => 'foo' )
		);

		$this->webhook->mailing_meta = array(
			'links' => array(
				array(
					'link_id' => 'foo',
					'link_name' => 'Foo Link',
					'link_target' => 'http://foo.com',
				),
			)
		);

		$actual = $this->webhook->get_clicked_link_meta();
		$this->assertEquals( $this->webhook->mailing_meta['links'][0], $actual );
	}

	function test_it_add_link_meta_to_action_to_save() {
		$this->webhook->params = array(
			'data' => array( 'link_id' => 'foo' )
		);

		$this->webhook->mailing_meta = array(
			'links' => array(
				array(
					'link_id' => 'foo',
					'link_name' => 'Foo Link',
					'link_target' => 'http://foo.com',
				),
			)
		);

		$actual = $this->webhook->get_action_to_save(
			'static_group_message_click', '123', 'foo subject'
		);

		$expected = array(
			'actionType' => 'action:static_group_message_click',
			'actionID' => '123',
			'actionData' => array(
				array( 'name' => 'subject', 'value' => 'foo subject' ),
				array( 'name' => 'linkID', 'value' => 'foo' ),
				array( 'name' => 'linkName', 'value' => 'Foo Link' ),
				array( 'name' => 'linkTarget', 'value' => 'http://foo.com' ),
			)
		);

		$this->assertEquals( $expected, $actual );
	}

}

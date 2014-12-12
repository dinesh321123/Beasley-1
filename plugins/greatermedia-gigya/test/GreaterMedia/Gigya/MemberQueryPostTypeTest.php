<?php

namespace GreaterMedia\Gigya;

class MemberQueryPostTypeTest extends \WP_UnitTestCase {

	public $post_type;

	function setUp() {
		parent::setUp();

		$this->post_type = new MemberQueryPostType();
	}

	function test_it_has_a_post_type_name() {
		$this->assertEquals( 'member_query', $this->post_type->get_post_type_name() );
	}

	function test_it_only_supports_title_field() {
		$this->assertEquals( array( 'title' ), $this->post_type->get_supports() );
	}

	function test_it_is_not_public() {
		$options = $this->post_type->get_options();
		$this->assertFalse( $options['public'] );
	}

	function test_it_is_not_publicly_queryable() {
		$options = $this->post_type->get_options();
		$this->assertFalse( $options['publicly_queryable'] );
	}

	function test_it_has_non_public_options_for_preview_post_type() {
		$options = $this->post_type->get_preview_options();
		$this->assertFalse( $options['show_ui'] );
		$this->assertFalse( $options['public'] );
	}

	function test_it_has_wordpress_cpt_ui() {
		$options = $this->post_type->get_options();
		$this->assertTrue( $options['show_ui'] );
	}

	function test_it_has_valid_labels() {
		$labels = $this->post_type->get_labels();
		$this->assertEquals( 'Member Query', $labels['name'] );
	}

	function test_it_can_be_registered() {
		$this->post_type->register();
		$this->assertTrue( post_type_exists( 'member_query' ) );
	}

	function test_it_registers_a_corresponding_preview_post_type() {
		$this->post_type->register();
		$this->assertTrue( post_type_exists( 'member_query_preview' ) );
	}

	function test_it_has_meta_boxes() {
		$actual = $this->post_type->get_meta_boxes();
		$this->assertNotEmpty( $actual );
	}

	function test_it_can_verify_meta_boxes() {
		$actual = $this->post_type->verify_meta_box_nonces();
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_all_meta_boxes_nonces_are_valid() {
		$_POST['preview_nonce']       = wp_create_nonce( 'preview' );
		$_POST['direct_query_nonce']  = wp_create_nonce( 'direct_query' );
		$_POST['query_builder_nonce'] = wp_create_nonce( 'query_builder' );

		$actual = $this->post_type->verify_meta_box_nonces();
		$this->assertTrue( $actual );
	}

	function test_it_can_register_all_meta_boxes() {
		$this->post_type->register_meta_boxes( 'foo' );
		$meta_box = $this->post_type->meta_boxes['preview'];
		$this->assertEquals( 'foo', $meta_box->data );
	}

}

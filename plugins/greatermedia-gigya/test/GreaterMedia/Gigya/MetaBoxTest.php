<?php

namespace GreaterMedia\Gigya;

class MetaBoxTest extends \WP_UnitTestCase {

	public $box;

	function setUp() {
		parent::setUp();

		$this->box = $this->box_for( array( 'query' => 'foo' ) );
	}

	function box_for( $content ) {
		$post = array(
			'post_content' => json_encode( $content )
		);

		$member_query = new MemberQuery( $post );
		$box          = new MetaBox( $member_query );

		return $box;
	}

	function test_it_stores_a_member_query() {
		$box = $this->box_for( array( 'query' => 'foo' ) );
		$this->assertInstanceOf( 'GreaterMedia\Gigya\MemberQuery', $box->data );
	}

	function test_it_stores_params() {
		$this->box->params = array( 'foo' => 'bar' );
		$this->assertEquals( array( 'foo' => 'bar' ), $this->box->params );
	}

	function test_it_can_compute_path_to_template_file_without_extension() {
		$actual = $this->box->get_template_path( 'foo' );
		$this->assertStringEndsWith( 'templates/metaboxes/foo.php', $actual );
	}

	function test_it_has_a_priority() {
		$this->assertEquals( 'default', $this->box->get_priority() );
	}

	function test_it_has_context() {
		$this->assertEquals( 'normal', $this->box->get_context() );
	}

	function test_it_has_a_title() {
		$this->assertEquals( 'Meta Box', $this->box->get_title() );
	}

	function test_it_has_an_id() {
		$this->assertEquals( 'meta_box', $this->box->get_id() );
	}

	function test_it_has_a_nonce_field() {
		$this->assertEquals( 'meta_box_nonce', $this->box->get_nonce_field() );
	}

	function test_it_can_register_a_meta_box() {
		$this->box->register();
	}

	function test_it_can_render_a_meta_box() {
		ob_start();
		$this->box->render();
		$html = ob_get_clean();

		$matcher = array(
			'tag' => 'input',
			'attributes' => array(
				'type' => 'hidden',
				'id' => 'meta_box_nonce',
				'name' => 'meta_box_nonce',
				'value' => 'regexp:/[a-z0-9]*/'
			)
		);

		$this->assertTag( $matcher, $html );
	}

	function test_it_knows_if_nonce_is_invalid() {
		$_POST['meta_box_nonce'] = 'foo';
		$this->assertFalse( $this->box->verify_nonce() );
	}

	function test_it_knows_if_nonce_is_valid() {
		$_POST['meta_box_nonce'] = wp_create_nonce( 'meta_box', 'meta_box_nonce' );
		$this->assertTrue( $this->box->verify_nonce() );
	}


}

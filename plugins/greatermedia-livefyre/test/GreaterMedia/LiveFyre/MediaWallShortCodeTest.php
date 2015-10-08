<?php

namespace GreaterMedia\LiveFyre;

class MediaWallShortCodeTest extends \WP_UnitTestCase {

	public $shortcode;

	function setUp() {
		parent::setUp();
		$this->shortcode = new MediaWallShortCode();
		$this->change_livefyre_options(
			array(
				'network_name' => 'foo.fyre.co',
				'site_id' => '1234',
			)
		);
	}

	function tearDown() {
		delete_option( 'livefyre_settings' );
		parent::tearDown();
	}

	function change_livefyre_options( $options ) {
		delete_option( 'livefyre_settings' );
		update_option( 'livefyre_settings', json_encode( $options ) );
	}

	function test_it_has_livefyre_options() {
		$actual = $this->shortcode->get_livefyre_option( 'network_name' );
		$this->assertEquals( 'foo.fyre.co', $actual );
	}

	function test_it_knows_if_uat_environment() {
		$this->change_livefyre_options( array( 'network_name' => 'foo-int-0.fyre.co' ) );
		$actual = $this->shortcode->get_environment();

		$this->assertEquals( 't402.livefyre.com', $actual );
	}

	function test_it_knows_if_production_environment() {
		$this->change_livefyre_options( array( 'network_name' => 'foo.fyre.co' ) );
		$actual = $this->shortcode->get_environment();
		$this->assertEquals( 'livefyre.com', $actual );
	}

	function test_it_knows_if_current_post_is_invalid() {
		global $post;
		$post = null;

		$actual = $this->shortcode->get_current_post();
		$this->assertNull( $actual );
	}

	function test_it_knows_if_current_post_is_valid() {
		global $post;
		$post = $this->factory->post->create_and_get();

		$actual = $this->shortcode->get_current_post();
		$this->assertSame( $post, $actual );
	}

	function test_it_has_valid_media_wall_options() {
		global $post;
		$post = $this->factory->post->create_and_get();

		$actual = $this->shortcode->get_media_wall_options();
		$expected = array(
			'network_name' => 'foo.fyre.co',
			'site_id' => '1234',
			'article_id' => $post->ID,
			'environment' => 'livefyre.com',
			'walls' => array(),
		);

		$this->assertEquals( $expected, $actual['data'] );
	}

}

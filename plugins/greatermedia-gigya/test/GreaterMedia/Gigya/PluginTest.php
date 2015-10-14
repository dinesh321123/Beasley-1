<?php

namespace GreaterMedia\Gigya;

class PluginTest extends \WP_UnitTestCase {

	function setUp() {
		parent::setUp();
	}

	function test_it_stores_plugin_file() {
		$plugin = new Plugin( 'greatermedia-gigya.php' );
		$this->assertEquals( 'greatermedia-gigya.php', $plugin->plugin_file );
	}

}
